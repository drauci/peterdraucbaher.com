<?php
namespace MNHPush\Public;

class Display {

    public function __construct() {
        // Izpišemo popup v nogi strani, vendar ne v administraciji
        add_action('wp_footer', [$this, 'render_push_prompt']);
    }

    public function render_push_prompt() {
        if (is_admin()) return;

        // Preverimo, če sta ključa sploh nastavljena, preden težimo uporabniku
        $pub_key = get_option('mnh_vapid_public_key');
        if (empty($pub_key)) return;

        ?>
        <style>
            #mnh-push-prompt {
                display: none;
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 320px;
                background: #ffffff;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                padding: 20px;
                z-index: 999999;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                border: 1px solid #eee;
                animation: mnhFadeIn 0.4s ease-out;
            }
            @keyframes mnhFadeIn {
                from { transform: translateY(20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            #mnh-push-prompt h4 { margin: 0 0 8px 0; font-size: 17px; color: #111; }
            #mnh-push-prompt p { margin: 0 0 15px 0; font-size: 13px; color: #666; line-height: 1.4; }

            .mnh-actions { display: flex; gap: 10px; justify-content: flex-end; }

            #mnh-allow-btn {
                background: #007cba;
                color: #fff;
                border: none;
                padding: 8px 15px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: 600;
                font-size: 13px;
            }
            #mnh-dismiss-btn {
                background: transparent;
                color: #888;
                border: none;
                cursor: pointer;
                font-size: 13px;
            }
        </style>

        <div id="mnh-push-prompt">
            <h4 id="mnh-title"></h4>
            <p id="mnh-desc"></p>
            <div class="mnh-actions">
                <button id="mnh-dismiss-btn"></button>
                <button id="mnh-allow-btn"></button>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const translations = {
                    'sl': {
                        'title': 'Bodite obveščeni',
                        'desc': 'Želite prejemati obvestila o najnovejših objavah neposredno na vašo napravo?',
                        'btn_yes': 'Vklopi obvestila',
                        'btn_no': 'Ne, hvala'
                    },
                    'en': {
                        'title': 'Stay Updated',
                        'desc': 'Would you like to receive push notifications about our latest posts?',
                        'btn_yes': 'Enable Now',
                        'btn_no': 'Maybe later'
                    }
                };

                const lang = navigator.language.startsWith('sl') ? 'sl' : 'en';
                const t = translations[lang];
                const prompt = document.getElementById('mnh-push-prompt');

                // Nastavi besedila
                document.getElementById('mnh-title').innerText = t.title;
                document.getElementById('mnh-desc').innerText = t.desc;
                document.getElementById('mnh-allow-btn').innerText = t.btn_yes;
                document.getElementById('mnh-dismiss-btn').innerText = t.btn_no;

                // Logika prikaza
                setTimeout(async function() {
                    const dismissed = localStorage.getItem('mnh_push_dismissed');
                    if (dismissed === 'true') return;

                    if ('serviceWorker' in navigator && 'PushManager' in window) {
                        const reg = await navigator.serviceWorker.ready;
                        const sub = await reg.pushManager.getSubscription();

                        if (!sub && Notification.permission === 'default') {
                            prompt.style.display = 'block';
                        }
                    }
                }, 2500);

                // Gumbi
                document.getElementById('mnh-allow-btn').addEventListener('click', function() {
                    prompt.style.display = 'none';
                    if (typeof mnhTriggerSubscribe === 'function') {
                        mnhTriggerSubscribe();
                    }
                });

                document.getElementById('mnh-dismiss-btn').addEventListener('click', function() {
                    prompt.style.display = 'none';
                    localStorage.setItem('mnh_push_dismissed', 'true');
                });
            });
        </script>
        <?php
    }
}