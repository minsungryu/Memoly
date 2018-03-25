<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

/**
 * 암복호화 및 해시 관련 클래스
 */
final class Crypto {

    private const email_cipher = 'AES256';

    private function __construct() { }

    /**
     * 비밀번호를 암호화한다.
     */
    public static function hashPassword($password) {
        $bcrypt_option = ['salt'=> getenv('BCRYPT_SALT')];
        return password_hash($password, PASSWORD_BCRYPT, $bcrypt_option);
    }

    /**
     * 쿠키에 저장할 이메일 주소를 암호화한다.
     */
    public static function encryptEmail($email) {
        $aes_key = getenv('AES256_KEY');
        if (in_array(self::email_cipher, openssl_get_cipher_methods(true))) {
            return openssl_encrypt($email, self::email_cipher, $aes_key);
        }
    }

    /**
     * 쿠키에 저장된 이메일 주소를 복호화한다.
     */
    public static function decryptEmail($email) {
        $aes_key = getenv('AES256_KEY');
        if (in_array(self::email_cipher, openssl_get_cipher_methods(true))) {
            return openssl_decrypt($email, self::email_cipher, $aes_key);
        }
        
    }
}

?>