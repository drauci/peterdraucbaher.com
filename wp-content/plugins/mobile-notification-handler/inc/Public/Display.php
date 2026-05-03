<?php
namespace MNHPush\Public;

class Display {
    public function __construct() {
        add_action('wp_footer', [$this, 'render_push_prompt']);
    }

    public function render_push_prompt() {
        if (is_admin()) return;
        if (empty(get_option('mnh_vapid_public_key'))) return;

        ?>
        <div id="mnh-push-prompt" style="display:none; position:fixed; bottom:20px; right:20px; background:#fff; padding:20px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.2); z-index:9999; border:1px solid #ddd;">
            <h4 style="margin-top:0;">Obvestila</h4>
            <p>Želite prejemati obvestila na to napravo?</p>
            <div style="text-align:right;">
                <button onclick="document.getElementById('mnh-push-prompt').style.display='none'; localStorage.setItem('mnh_dismiss', '1');" style="background:none; border:none; color:#888; cursor:pointer;">Zapri</button>
                <button id="mnh-btn-allow" style="background:#007cba; color:#fff; border:none; padding:8px 15px; border-radius:5px; cursor:pointer; margin-left:10px;">Vklopi</button>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(async function() {
                    if (localStorage.getItem('mnh_dismiss') === '1') return;

                    if ('Notification' in window && Notification.permission === 'default') {
                        document.getElementById('mnh-push-prompt').style.display = 'block';
                    }
                }, 3000);

                document.getElementById('mnh-btn-allow').addEventListener('click', function() {
                    document.getElementById('mnh-push-prompt').style.display = 'none';
                    if (typeof window.mnhTriggerSubscribe === 'function') {
                        window.mnhTriggerSubscribe();
                    } else {
                        console.error('MNH: Funkcija mnhTriggerSubscribe še ni naložena!');
                    }
                });
            });
        </script>
        <?php
    }
}