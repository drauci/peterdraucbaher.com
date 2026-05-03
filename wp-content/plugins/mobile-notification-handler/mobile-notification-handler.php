<?php
/**
 * Plugin Name: Mobile Notification Handler
 * Description: Sistem za zbiranje žetonov z avtomatskim upravljanjem sw.js datoteke.
 * Version: 1.4
 * Author: Peter Draučbaher
 */

if (!defined('ABSPATH')) exit;

class MobileNotificationHandler {

    private $table_name;
    private $sw_dest;
    private $sw_src;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'mobile_push_subscriptions';
        $this->sw_src = plugin_dir_path(__FILE__) . 'sw.js';
        $this->sw_dest = ABSPATH . 'sw.js';

        // Aktivacija in deaktivacija
        register_activation_hook(__FILE__, array($this, 'activation_logic'));
        register_deactivation_hook(__FILE__, array($this, 'deactivation_logic'));

        // Hook za izpis popup-a v nogi strani
        add_action('wp_footer', array($this, 'render_push_prompt'));

        add_action('rest_api_init', array($this, 'register_endpoints'));
        add_action('admin_menu', array($this, 'add_admin_info'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_push_script'));
        add_action('admin_post_trigger_push', array($this, 'procesiraj_ročno_sprožitev'));
        add_action('publish_post', array($this, 'avtomatsko_poslji_ob_objavi'), 10, 2);

        //add_action('admin_enqueue_scripts', array($this, 'add_deactivation_confirmation'));
        //add_action('admin_init', array($this, 'check_for_cleanup_request'));
    }

    /**
     * LOGIKA OB AKTIVACIJI
     */
    public function activation_logic() {
        $this->install_table();
        $this->kopiraj_sw_v_root();
    }

    /**
     * LOGIKA OB DEAKTIVACIJI
     */
    public function deactivation_logic() {
        $this->pobrisi_sw_iz_roota();
    }

    public function install_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mnh_push_subscribers';
        $charset_collate = $wpdb->get_charset_collate();

        // Pomembno: endpoint mora imeti UNIQUE KEY.
        // Ker so URL-ji lahko dolgi, omejimo indeks na 191 ali 255 znakov,
        // da preprečimo težave z MySQL ključi pri določenih verzijah.
        $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        endpoint text NOT NULL,
        p256dh varchar(255) NOT NULL,
        auth varchar(255) NOT NULL,
        user_agent text,
        source varchar(50) DEFAULT 'desktop',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY endpoint_idx (endpoint(191))
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('mnh_vapid_public_key', 'BH7HqdsAzZtOSHZ5PG-MSJph5016vBSKuXnkcHhWkJa46uk8rKtI7vXVyzqscIcVErotBLiOSyn95JWk_1UwQXg');
        update_option('mnh_vapid_private_key', '8E4Ka6f3WybZGv-Iu9XVOJSgBSRUgbO9VEsG2f2kc_3');

        // Za vsak primer dodaj še to, da preveriš, če se je koda sploh izvedla
        error_log("MNH install_table poklicana za tabelo: $table_name");

    }


    private function kopiraj_sw_v_root() {
        if (!file_exists($this->sw_src)) {
            wp_die('Napaka: Datoteka sw.js ne obstaja v mapi vtičnika. Prosim, dodaj jo pred aktivacijo.');
        }

        // Poskus kopiranja
        $success = @copy($this->sw_src, $this->sw_dest);

        if (!$success) {
            wp_die('Napaka: Vtičnik ne more kopirati sw.js v korensko mapo (root). Preveri pravice pisanja (permissions) na strežniku za mapo ' . ABSPATH);
        }
    }

    private function pobrisi_sw_iz_roota() {
        if (file_exists($this->sw_dest)) {
            @unlink($this->sw_dest);
        }
    }

    public function register_endpoints() {
        register_rest_route('mnh/v1', '/subscribe', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_subscription'),
            'permission_callback' => '__return_true',
        ));
    }

    public function handle_subscription($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mnh_push_subscribers';
        $params = $request->get_json_params();

        // Osnovna preverka, če imamo sploh endpoint
        if (empty($params['endpoint'])) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Ni endpointa.'), 400);
        }

        // Priprava podatkov
        $data = array(
            'endpoint'   => sanitize_text_field($params['endpoint']),
            'p256dh'     => sanitize_text_field($params['keys']['p256dh'] ?? ''),
            'auth'       => sanitize_text_field($params['keys']['auth'] ?? ''),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
            'source'     => sanitize_text_field($params['source'] ?? 'desktop'),
            'created_at' => current_time('mysql')
        );

        // UNIQUE KEY v bazi + REPLACE poskrbita, da se identični endpointi ne podvajajo.
        // Če pa brskalnik vrne nov žeton, bo to pač nov vnos (stari bo sčasoma postal 410 Gone).
        $result = $wpdb->replace($table_name, $data);

        if ($result !== false) {
            return new WP_REST_Response(array('success' => true), 200);
        }

        return new WP_REST_Response(array('success' => false), 500);
    }


    public function enqueue_push_script() {
        wp_register_script('mnh-push-collector', false);
        wp_enqueue_script('mnh-push-collector');

        $public_key = get_option('mnh_vapid_public_key');
        $api_url = esc_url_raw(rest_url('mnh/v1/subscribe'));

        $inline_js = "
    /**
     * 1. Pomožna funkcija za pretvorbo VAPID ključa
     */
    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    /**
     * 2. Glavna funkcija za naročanje na klik
     */
    async function mnhTriggerSubscribe() {
        console.log('MNH: Zagon postopka naročanja...');
        
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            console.error('MNH: Brskalnik ne podpira Push obvestil.');
            return;
        }

        try {
            // Počakamo, da je Service Worker pripravljen
            const reg = await navigator.serviceWorker.ready;
            
            // Preverimo obstoječo naročnino
            const existingSub = await reg.pushManager.getSubscription();
            if (existingSub) {
                console.log('MNH: Uporabnik je že naročen.', existingSub);
                // Opcijsko: Pošljemo posodobitev na strežnik, če želimo biti 100%
            }

            const pubKey = urlBase64ToUint8Array('$public_key');
            
            // Sprožimo sistemsko okno za dovoljenje
            const newSub = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: pubKey
            });

            console.log('MNH: Naročnina potrjena s strani brskalnika.');

            // Priprava podatkov za PHP
            const key = btoa(String.fromCharCode.apply(null, new Uint8Array(newSub.getKey('p256dh'))));
            const auth = btoa(String.fromCharCode.apply(null, new Uint8Array(newSub.getKey('auth'))));

            // Pošiljanje v WordPress bazo
            const response = await fetch('$api_url', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    endpoint: newSub.endpoint,
                    keys: { p256dh: key, auth: auth },
                    source: window.navigator.userAgent.includes('Mobile') ? 'mobile' : 'desktop'
                })
            });

            const result = await response.json();
            console.log('MNH: Odgovor strežnika:', result);
            
            if (result.success) {
                alert('Obvestila so uspešno vklopljena!');
            }

        } catch (err) {
            console.error('MNH Kritična napaka:', err);
            if (err.name === 'NotAllowedError') {
                alert('Prosimo, omogočite obvestila v nastavitvah brskalnika (ikona ključavnice).');
            }
        }
    }

    /**
     * 3. Registracija Service Workerja ob nalaganju strani
     */
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(reg) {
                console.log('MNH: Service Worker registriran.');
            }).catch(function(err) {
                console.error('MNH: Napaka pri registraciji SW:', err);
            });
        });
    }
    ";

        wp_add_inline_script('mnh-push-collector', $inline_js);
    }


    public function add_admin_info() {
        add_menu_page('Mobile Push', 'Mobile Push', 'manage_options', 'mnh-stats', array($this, 'render_admin_page'), 'dashicons-rss');
    }

    public function render_admin_page() {
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $this->table_name");
        echo '<div class="wrap"><h1>Status mobilne vojske</h1>';
        echo '<div class="card" style="max-width: 500px; padding: 20px;">';
        echo '<p style="font-size: 1.2rem;">Trenutno število spečih naprav: <strong>' . esc_html($count) . '</strong></p>';
        echo '<form action="' . esc_url(admin_url('admin-post.php')) . '" method="post">';
        echo '<input type="hidden" name="action" value="trigger_push">';
        wp_nonce_field('mnh_trigger_action', 'mnh_nonce');
        echo '<input type="submit" class="button button-primary button-large" value="SPROŽI TESTNO OBVESTILO VSEM" onclick="return confirm(\'Ali si prepričan?\');">';
        echo '</form></div>';
        echo '<p style="margin-top:20px;">Fizična lokacija sw.js: <code>' . (file_exists($this->sw_dest) ? 'V korenski mapi (V REDU)' : 'Manjka!') . '</code></p>';
        echo '</div>';
    }

    public function procesiraj_ročno_sprožitev() {
        if (!isset($_POST['mnh_nonce']) || !wp_verify_nonce($_POST['mnh_nonce'], 'mnh_trigger_action')) wp_die('Security check failed.');
        if (!current_user_can('manage_options')) wp_die('No permission.');
        $this->poslji_push_logika("Ročni test", "Test");
        wp_redirect(admin_url('admin.php?page=mnh-stats&status=success'));
        exit;
    }

    public function avtomatsko_poslji_ob_objavi($ID, $post) {
        if ($post->post_type !== 'post') return;
        if (wp_is_post_revision($ID) || get_post_meta($ID, '_push_sent', true)) return;
        $this->poslji_push_logika($post->post_title, $ID);
        update_post_meta($ID, '_push_sent', '1');
    }

    public function render_push_prompt() {
        if (is_admin()) return;

        ?>
        <style>
            #mnh-push-prompt {
                display: none;
                position: fixed;
                bottom: 30px;
                right: 90px;
                width: 340px;
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                padding: 24px;
                z-index: 10000;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                border: 1px solid #f0f0f0;
                animation: mnhSlideIn 0.5s ease-out;
            }
            @keyframes mnhSlideIn {
                from { transform: translateY(100px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            #mnh-push-prompt h4 { margin: 0 0 10px 0; font-size: 18px; font-weight: 700; color: #1a1a1a; line-height: 1.2; }
            #mnh-push-prompt p { margin: 0 0 15px 0; font-size: 14px; line-height: 1.5; color: #4a4a4a; }
            .mnh-note {
                background: #fff9e6;
                border-left: 4px solid #ffcc00;
                padding: 10px;
                font-size: 12px !important;
                margin-bottom: 20px !important;
                color: #856404 !important;
                font-weight: 500;
            }
            .mnh-actions { display: flex; justify-content: space-between; align-items: center; gap: 10px; }
            #mnh-allow-btn {
                background: #000;
                color: #fff;
                border: none;
                padding: 10px 18px;
                border-radius: 6px;
                cursor: pointer;
                font-weight: 600;
                font-size: 14px;
                transition: background 0.2s;
                white-space: nowrap;
            }
            #mnh-allow-btn:hover { background: #333; }
            #mnh-dismiss-btn {
                background: none;
                border: none;
                color: #999;
                cursor: pointer;
                font-size: 13px;
                text-decoration: none;
                padding: 0;
                font-weight: 500;
            }
            #mnh-dismiss-btn:hover { color: #666; }

            @media (max-width: 480px) {
                #mnh-push-prompt { right: 20px; left: 20px; width: auto; bottom: 20px; }
            }
        </style>

        <div id="mnh-push-prompt">
            <h4></h4>
            <p></p>
            <p class="mnh-note"></p>
            <div class="mnh-actions">
                <button id="mnh-dismiss-btn"></button>
                <button id="mnh-allow-btn"></button>
            </div>
        </div>

        <script>
            window.addEventListener('load', function() {
                // 1. Nastavitve prevodov
                const translations = {
                    'sl': {
                        'title': 'Obvestila o sporočilih',
                        'desc': 'Želite prejemati takojšnja obvestila o novih sporočilih v vašem nabiralniku?',
                        'note': 'Po kliku na spodnji gumb prosimo <strong>potrdite še sistemsko okno</strong> brskalnika zgoraj levo.',
                        'btn_yes': 'Vklopi obvestila',
                        'btn_no': 'Ne'
                    },
                    'en': {
                        'title': 'Push Notifications',
                        'desc': 'Would you like to receive instant notifications for new messages in your inbox?',
                        'note': 'After clicking the button, please <strong>confirm the browser system prompt</strong> in the top left corner.',
                        'btn_yes': 'Enable Notifications',
                        'btn_no': 'No'
                    }
                };

                const userLang = navigator.language.substring(0, 2);
                const t = translations[userLang] || translations['en'];
                const promptEl = document.getElementById('mnh-push-prompt');

                if (promptEl) {
                    promptEl.querySelector('h4').innerText = t.title;
                    promptEl.querySelector('p:not(.mnh-note)').innerText = t.desc;
                    promptEl.querySelector('.mnh-note').innerHTML = t.note;
                    promptEl.querySelector('#mnh-dismiss-btn').innerText = t.btn_no;
                    promptEl.querySelector('#mnh-allow-btn').innerText = t.btn_yes;
                }

                // 2. Logika prikaza s preverjanjem localStorage
                setTimeout(async function() {
                    // Preverimo, če je uporabnik v preteklosti kliknil "Ne"
                    const isDismissed = localStorage.getItem('mnh_push_dismissed');
                    if (isDismissed === 'true') return;

                    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

                    try {
                        const reg = await navigator.serviceWorker.ready;
                        const sub = await reg.pushManager.getSubscription();

                        // Prikažemo le, če ni naročen in dovoljenje še ni določeno/blokirano
                        if (!sub && Notification.permission === 'default') {
                            promptEl.style.display = 'block';
                        }
                    } catch (e) {
                        console.warn('MNH: Napaka pri preverjanju naročnine.');
                    }
                }, 3000);

                // 3. Gumb "Vklopi obvestila"
                document.getElementById('mnh-allow-btn').addEventListener('click', function() {
                    promptEl.style.display = 'none';
                    if (typeof mnhTriggerSubscribe === 'function') {
                        mnhTriggerSubscribe();
                    }
                });

                // 4. Gumb "Ne" (Trajna zavrnitev)
                document.getElementById('mnh-dismiss-btn').addEventListener('click', function() {
                    promptEl.style.display = 'none';
                    // Shranimo v localStorage, da se popup nikoli več ne pojavi
                    localStorage.setItem('mnh_push_dismissed', 'true');
                    console.log('MNH: Uporabnik je trajno zavrnil popup.');
                });
            });
        </script>
        <?php
    }


    /**
     * Preveri, če je uporabnik zahteval popoln izbris
     */
    public function check_for_cleanup_request() {
        // 1. Preveri, če je najin parameter prisoten
        if (!isset($_GET['confirm_full_cleanup']) || $_GET['confirm_full_cleanup'] !== '1') {
            return;
        }

        // 2. Varnostna preverjanja:
        // Preveri, če gre za deaktivacijo pravega vtičnika
        if (!isset($_GET['plugin']) || $_GET['plugin'] !== plugin_basename(__FILE__)) {
            return;
        }

        // 3. NAJPOMEMBNEJŠE: Preveri WordPress varnostni žeton (Nonce)
        // WordPress ob generiranju povezave za deaktivacijo doda '_wpnonce'
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'deactivate-plugin_' . plugin_basename(__FILE__))) {
            wp_die('Varnostna napaka: Neveljaven žeton. Brisanje baze prekinjeno.');
        }

        // 4. Preveri, če ima uporabnik sploh pravice za to
        if (!current_user_can('activate_plugins')) {
            wp_die('Nimate ustreznih pravic za brisanje podatkov.');
        }

        // Če gre vse skozi, šele takrat počisti
        $this->complete_uninstall_cleanup();
    }

    /**
     * Počisti vse: tabelo in nastavitve
     */
    private function complete_uninstall_cleanup() {
        global $wpdb;

        // 1. Izbris tabele
        $table_name = $wpdb->prefix . 'mnh_push_subscribers';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // 2. Izbris vseh nastavitev (VAPID ključi in ostalo)
        // Uporabiva tvoja dejanska imena ključev iz get_option()
        delete_option('mnh_vapid_public_key');
        delete_option('mnh_vapid_private_key');
        delete_option('mnh_push_settings'); // Če imaš še kakšne druge nastavitve

        // Izbrišemo tudi localStorage zastavico pri uporabnikih (to sicer ni možno preko PHP,
        // ampak v bazi smo zdaj čisti).
    }

    /**
     * Doda potrditveno okno na stran z vtičniki
     */
    public function add_deactivation_confirmation($hook) {
        if ('plugins.php' !== $hook) return;

        $plugin_slug = plugin_basename(__FILE__);
        ?>
        <script>
            jQuery(document).ready(function($) {
                var pluginSlug = '<?php echo $plugin_slug; ?>';
                var deactivationLink = $('tr[data-plugin="' + pluginSlug + '"] .deactivate a');

                deactivationLink.on('click', function(e) {
                    var confirmDelete = confirm("Želite ob deaktivaciji izbrisati vse podatke (tabelo naročnikov in VAPID ključe)?\n\nKliknite 'V redu' za popoln izbris ali 'Prekliči' le za deaktivacijo.");

                    if (confirmDelete) {
                        var originalUrl = $(this).attr('href');
                        $(this).attr('href', originalUrl + '&delete_mnh_all_data=1');
                    }
                });
            });
        </script>
        <?php
    }


    public function poslji_push_logika($naslov, $post_id) {
        // Tukaj pride tvoja WebPush koda...
        error_log("Push Signal sprožen za: " . $naslov);
        /*
        / Primer PHP logike za pošiljanje (ko boš to potreboval)
        $user_lang = get_user_meta($user_id, 'user_lang', true); // Predpostavimo, da hraniš jezik

        if ($user_lang == 'en') {
            $payload = [
                'title' => 'New Notification',
                'body'  => 'You have a new message.',
                'url'   => '/en/messages/'
            ];
        } else {
            $payload = [
                'title' => 'Novo obvestilo',
                'body'  => 'Imate novo sporočilo.',
                'url'   => '/sl/sporocila/'
            ];
        }

        // Pošlješ kot JSON string
        $webPush->sendOneNotification($subscription, json_encode($payload));
        */

    }
}

new MobileNotificationHandler();