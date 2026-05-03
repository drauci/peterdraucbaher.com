<?php
namespace MNHPush\Public;

class Scripts {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue() {
        wp_register_script('mnh-push-js', false);
        wp_enqueue_script('mnh-push-js');

        $public_key = get_option('mnh_vapid_public_key');
        $api_url = esc_url_raw(rest_url('mnh/v1/subscribe'));

        // Tukaj vstaviš tvoj prej delujoči inline JS (urlBase64ToUint8Array, navigator.serviceWorker.register...)
        // Zaradi kratkosti tukaj ne ponavljam celotnega JS bloka.
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
}