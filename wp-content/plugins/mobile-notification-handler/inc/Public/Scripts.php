<?php
namespace MNHPush\Public;

class Scripts {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_push_scripts']);
    }

    public function enqueue_push_scripts() {
        wp_register_script('mnh-push-main', false);
        wp_enqueue_script('mnh-push-main');

        $pubKey = get_option('mnh_vapid_public_key');
        $apiUrl = esc_url_raw(rest_url('mnh/v1/subscribe'));

        $js = "
        // Pomožna funkcija za ključ
        function urlBase64ToUint8Array(s) {
            const pad = '='.repeat((4 - s.length % 4) % 4);
            const b64 = (s + pad).replace(/-/g, '+').replace(/_/g, '/');
            const raw = window.atob(b64);
            const out = new Uint8Array(raw.length);
            for (let i = 0; i < raw.length; ++i) out[i] = raw.charCodeAt(i);
            return out;
        }

        // REGISTRACIJA SW (takoj ob nalaganju)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').then(reg => {
                console.log('MNH: SW registriran.');
            }).catch(err => console.error('MNH: SW napaka:', err));
        }

        // GLAVNA FUNKCIJA ZA NAROČANJE
        window.mnhTriggerSubscribe = async function() {
            try {
                const reg = await navigator.serviceWorker.ready;
                const sub = await reg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array('$pubKey')
                });

                await fetch('$apiUrl', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        endpoint: sub.endpoint,
                        keys: { 
                            p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(sub.getKey('p256dh')))),
                            auth: btoa(String.fromCharCode.apply(null, new Uint8Array(sub.getKey('auth'))))
                        },
                        source: navigator.userAgent.includes('Mobile') ? 'mobile' : 'desktop'
                    })
                });
                alert('Naročnina uspešna!');
            } catch (e) {
                console.error('MNH Napaka:', e);
                alert('Napaka pri vklopu obvestil.');
            }
        };
        ";
        wp_add_inline_script('mnh-push-main', $js);
    }
}