<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

/**
 * 암복호화 및 해시 관련 클래스
 */
final class Crypto {

    private const cipher_alias = 'AES256';

    private function __construct() { }

    /**
     * 비밀번호를 bcrypt로 암호화한다.
     */
    public static function hashPassword($password) {
        $bcrypt_option = ['salt'=> getenv('BCRYPT_SALT')];
        return password_hash($password, PASSWORD_BCRYPT, $bcrypt_option);
    }

    /**
     * AES256 암호화
     */
    public static function encryptAES($message) {
        $aes_key = getenv('AES256_KEY');
        if (in_array(self::cipher_alias, openssl_get_cipher_methods(true))) {
            return openssl_encrypt($message, self::cipher_alias, $aes_key);
        }
    }

    /**
     * AES256 복호화
     */
    public static function decryptAES($message) {
        $aes_key = getenv('AES256_KEY');
        if (in_array(self::cipher_alias, openssl_get_cipher_methods(true))) {
            return openssl_decrypt($message, self::cipher_alias, $aes_key);
        }
        
    }
}

?>