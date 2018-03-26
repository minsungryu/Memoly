<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

/**
 * 암복호화 및 해시 관련 클래스
 */
final class Crypto {

    private static $cipher_alias = '';

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
        if (in_array('AES256', openssl_get_cipher_methods(true))) {
            return openssl_encrypt($message, 'AES256', $aes_key);
        }
    }

    /**
     * AES256 복호화
     */
    public static function decryptAES($message) {
        $aes_key = getenv('AES256_KEY');
        if (in_array('AES256', openssl_get_cipher_methods(true))) {
            return openssl_decrypt($message, 'AES256', $aes_key);
        }
        
    }

    /**
     * 임시 비밀번호 생성을 위한 랜덤 문자열 생성기
     */
    public static function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

?>