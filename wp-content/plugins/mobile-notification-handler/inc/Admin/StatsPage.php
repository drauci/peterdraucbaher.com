<?php
namespace MNHPush\Admin;

class StatsPage {

    /**
     * Glavni render za statistiko in hitre akcije
     */
    public function render() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mnh_push_subscribers';

        // Pridobivanje statistike
        $total_subscribers = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $mobile_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE source = %s", 'mobile'));
        $desktop_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE source = %s", 'desktop'));

        // Preverjanje statusa datotek in ključev
        $sw_exists = file_exists(ABSPATH . 'sw.js');
        $pub_key = get_option('mnh_vapid_public_key');
        $priv_key = get_option('mnh_vapid_private_key');
        $is_configured = (!empty($pub_key) && !empty($priv_key));

        ?>
        <div class="wrap">
            <h1><span class="dashicons dashicons-rss"></span> Status mobilne vojske</h1>
            <p>Pregled naročnikov in stanja sistema za potisna obvestila.</p>

            <div class="welcome-panel" style="padding: 20px; display: flex; gap: 40px; align-items: flex-start;">

                <!-- Stolpec 1: Statistika -->
                <div style="flex: 1;">
                    <h3>Statistika naročnin</h3>
                    <ul style="font-size: 1.1em; line-height: 2;">
                        <li>Skupaj naročnikov: <strong><?php echo esc_html($total_subscribers ?: 0); ?></strong></li>
                        <li>Mobilne naprave: <span class="dashicons dashicons-smartphone"></span> <strong><?php echo esc_html($mobile_count ?: 0); ?></strong></li>
                        <li>Namizni računalniki: <span class="dashicons dashicons-desktop"></span> <strong><?php echo esc_html($desktop_count ?: 0); ?></strong></li>
                    </ul>
                </div>

                <!-- Stolpec 2: Sistemsko stanje -->
                <div style="flex: 1;">
                    <h3>Stanje sistema</h3>
                    <ul style="line-height: 2;">
                        <li>
                            Service Worker (sw.js):
                            <?php echo $sw_exists
                                ? '<span style="color:green">✔ Nameščen v rootu</span>'
                                : '<span style="color:red">✘ Manjka v rootu!</span>'; ?>
                        </li>
                        <li>
                            VAPID ključi:
                            <?php echo $is_configured
                                ? '<span style="color:green">✔ Nastavljeni</span>'
                                : '<span style="color:red">✘ Niso nastavljeni</span>'; ?>
                        </li>
                    </ul>
                </div>

                <!-- Stolpec 3: Akcije -->
                <div style="flex: 1; background: #fff; padding: 15px; border: 1px solid #ccd0d4; border-radius: 4px;">
                    <h3>Hitre akcije</h3>
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                        <input type="hidden" name="action" value="trigger_push">
                        <?php wp_nonce_field('mnh_trigger_action', 'mnh_nonce'); ?>

                        <p><input type="submit" class="button button-primary button-large"
                                  value="Pošlji test vsem"
                                <?php echo ($total_subscribers == 0) ? 'disabled' : ''; ?>
                                  onclick="return confirm('Ali si prepričan, da želiš poslati obvestilo vsem naročnikom?');"></p>
                    </form>
                    <p class="description">Sproži ročno testno obvestilo na vse registrirane naprave.</p>
                </div>
            </div>

            <h2 style="margin-top: 30px;">Zadnjih 10 naročnikov</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Naprava</th>
                    <th>Datum vpisa</th>
                    <th>User Agent</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $subscribers = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 10");
                if ($subscribers) :
                    foreach ($subscribers as $sub) : ?>
                        <tr>
                            <td><?php echo $sub->id; ?></td>
                            <td>
                                <?php echo ($sub->source === 'mobile')
                                    ? '<span class="dashicons dashicons-smartphone"></span> Mobile'
                                    : '<span class="dashicons dashicons-desktop"></span> Desktop'; ?>
                            </td>
                            <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($sub->created_at)); ?></td>
                            <td style="font-size: 10px; color: #888;"><?php echo esc_html(substr($sub->user_agent, 0, 80)) . '...'; ?></td>
                        </tr>
                    <?php endforeach;
                else : ?>
                    <tr><td colspan="4">Ni še najdenih naročnikov.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}