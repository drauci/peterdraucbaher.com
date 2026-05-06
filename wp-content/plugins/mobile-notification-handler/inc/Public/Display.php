<?php
namespace MNHPush\Public;

class Display {
    public function __construct() {
        add_action('wp_footer', [$this, 'render_push_prompt']);
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
                        'title': 'Obvestila',
                        'desc': 'Želite prejemati  obvestila?',
                        'note': 'Po kliku na spodnji gumb prosimo <strong>potrdite še sistemsko okno</strong> brskalnika.',
                        'btn_yes': 'Vklopi obvestila',
                        'btn_no': 'Ne'
                    },
                    'en': {
                        'title': 'Notifications',
                        'desc': 'Would you like to receive notifications?',
                        'note': 'After clicking the button, please <strong>confirm the browser system prompt</strong>.',
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

                    if (Notification.permission !=='denied' && Notification.permission === 'default') {
                        console.log('mnhTriggerSubscribe klican');
                        mnhTriggerSubscribe();
                    }
                    /*
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
                    */
                }, 3000);

                // 3. Gumb "Vklopi obvestila"
             /*   document.getElementById('mnh-allow-btn').addEventListener('click', function() {
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
                });*/
            });
        </script>
        <?php
    }
}