<?php

require_once 'Controller.php';

class SignInController extends Controller {

    function __construct() {
        parent::__construct();
    }

    function render() {
        $this->form();
    }

    function form() {
        require_once 'SignInView.php';
    }

    function rememberEmail() {
        include_once 'loader.php';

        $dotenv = new Dotenv\Dotenv(__DIR__);
        $dotenv->load();

        $cipher_email = $_COOKIE["memoly_user"];
        if (isset($cipher_email)) {
            $cipher = 'AES256';
            $key = getenv('AES256_KEY');
            if (in_array($cipher, openssl_get_cipher_methods(true))) {
                echo openssl_decrypt($cipher_email, $cipher, $key);
            }
        }
    }

    function __destruct() {
        $option = [
            'script' => [
                'https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js',
                JS.'validate.js',
                JS.'sha512.js',
                JS.'signin.js'],
            'style' => [
                CSS.'common.css',
                CSS.'signin.css'
            ]];
        $this->appendScript($option);
        parent::__destruct();
    }

}

?>