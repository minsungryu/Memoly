<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

final class Crypto {

    private const email_cipher = 'AES256';

    private function __construct() { }

    public static function hashPassword($password) {
        $bcrypt_option = ['salt'=> getenv('BCRYPT_SALT')];
        return password_hash($password, PASSWORD_BCRYPT, $bcrypt_option);
    }

    public static function encryptEmail($email) {
        $aes_key = getenv('AES256_KEY');
        if (in_array(self::email_cipher, openssl_get_cipher_methods(true))) {
            return openssl_encrypt($email, self::email_cipher, $aes_key);
        }
    }

    public static function decryptEmail($email) {
        $aes_key = getenv('AES256_KEY');
        if (in_array(self::email_cipher, openssl_get_cipher_methods(true))) {
            return openssl_decrypt($email, self::email_cipher, $aes_key);
        }
        
    }
}

?>