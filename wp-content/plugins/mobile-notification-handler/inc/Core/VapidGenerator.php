<?php
namespace MNHPush\Core;

class VapidGenerator {
    public static function generate() {
        try {
            if (!extension_loaded('openssl')) {
                throw new \Exception("Modul OpenSSL ni omogočen.");
            }

            $php_root = dirname(PHP_BINARY);
            $openssl_conf = self::find_config($php_root);

            $config_args = [
                'private_key_type' => OPENSSL_KEYTYPE_EC,
                'curve_name'       => 'prime256v1',
            ];

            if ($openssl_conf) {
                putenv("OPENSSL_CONF=$openssl_conf");
                $config_args['config'] = $openssl_conf;
            }

            $res = openssl_pkey_new($config_args);
            if (!$res) {
                throw new \Exception("Generiranje ni uspelo: " . openssl_error_string());
            }

            openssl_pkey_export($res, $private_key_pem, null, $config_args);
            $details = openssl_pkey_get_details($res);

            $base64url = function($data) {
                return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
            };

            return [
                'public'  => $base64url("\x04" . $details['ec']['x'] . $details['ec']['y']),
                'private' => $base64url($details['ec']['d'])
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private static function find_config($php_root) {
        $paths = [
            $php_root . DIRECTORY_SEPARATOR . 'extras/ssl/openssl.cnf',
            dirname($php_root) . DIRECTORY_SEPARATOR . 'extras/ssl/openssl.cnf',
            'C:/wamp64/bin/php/php' . PHP_VERSION . '/extras/ssl/openssl.cnf'
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) return $path;
        }
        return null;
    }
}