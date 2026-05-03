<?php
namespace MNHPush\Admin;

use MNHPush\Core\VapidGenerator;

class SettingsPage {
    public function render() {
        $generated_keys = null;

        if (isset($_POST['mnh_generate_keys'])) {
            check_admin_referer('mnh_keys_action', 'mnh_keys_nonce');
            $generated_keys = VapidGenerator::generate();
        }

        if (isset($_POST['mnh_save_keys'])) {
            check_admin_referer('mnh_keys_action', 'mnh_keys_nonce');
            update_option('mnh_vapid_public_key', sanitize_text_field($_POST['public_key']));
            update_option('mnh_vapid_private_key', sanitize_text_field($_POST['private_key']));
            echo '<div class="updated"><p>Ključi shranjeni.</p></div>';
        }

        $pub = get_option('mnh_vapid_public_key', '');
        $priv = get_option('mnh_vapid_private_key', '');

        ?>
        <div class="wrap">
            <h1>VAPID Nastavitve</h1>
            <?php if (isset($generated_keys['error'])): ?>
                <div class="error"><p><?php echo esc_html($generated_keys['error']); ?></p></div>
            <?php elseif ($generated_keys): ?>
                <div class="notice notice-warning">
                    <p><strong>Novi ključi generirani!</strong> Kopirajte jih spodaj in kliknite Shrani.</p>
                    <code>Public: <?php echo $generated_keys['public']; ?></code><br>
                    <code>Private: <?php echo $generated_keys['private']; ?></code>
                </div>
            <?php endif; ?>

            <form method="post">
                <?php wp_nonce_field('mnh_keys_action', 'mnh_keys_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th>Javni ključ</th>
                        <td><input name="public_key" type="text" value="<?php echo esc_attr($pub); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th>Zasebni ključ</th>
                        <td><input name="private_key" type="password" value="<?php echo esc_attr($priv); ?>" class="large-text"></td>
                    </tr>
                </table>
                <input type="submit" name="mnh_save_keys" class="button button-primary" value="Shrani ključe">
                <input type="submit" name="mnh_generate_keys" class="button" value="Generiraj nove">
            </form>
        </div>
        <?php
    }
}