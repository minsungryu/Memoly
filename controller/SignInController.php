<?php
session_start();

error_reporting(E_STRICT);
ini_set("display_errors", 1);

if(isset($_SESSION['is_login'])){
    if (isset($_SESSION['is_admin'])) {
        header('Location: ./user.php');
    } else {
        header('Location: ./memo.php');
    }
}

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';
require_once LIB.'Crypto.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

class SignInController extends Controller {

    function render() {
        $this->documentHead();
        $this->form();
        $option = [
            'script' => [
                'https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js',
                JS.'validate.js',
                JS.'sha512.js',
                JS.'signin.js'],
            'style' => [
                CSS.'common.css',
                CSS.'signin.css'
            ]
        ];
        $this->appendScript($option);
        $this->documentFoot();
    }

    function form() {
        require_once VIEW.'SignInView.php';
    }

    function checkUser($email, $password, $remember) {
        // 이메일 유효성 체크
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          header('Location: ./signin.php');
        }

        $password = Crypto::hashPassword($password);
        $user = new UserModel();
        $error = $user->signin($email, $password);

        if ($error[0] === Database::SUCCESS) {
            // 쿠키로 아이디 저장
            if (isset($remember)) {
                $encrypted_email = Crypto::encryptEmail($email);
                if (!is_null($encrypted_email)) {
                    $duration = time() + 60 * 60 * 24 * 14; // 14일
                    setcookie('memoly_user', $encrypted_email, $duration, '/signin.php');
                    setcookie('memoly_remember', 'checked="checked"', $duration, '/signin.php');
                }
            } else {
                // 쿠키 제거
                setcookie('memoly_user', '', time() - 1, '/signin.php');
                setcookie('memoly_remember', '', time() - 1, '/signin.php');
            }

            $_SESSION = $user->get();
            if ($_SESSION['is_admin'] === '0') { // 일반 사용자
                header('Location: ./memo.php');
            } else if ($_SESSION['is_admin'] === '1') { // 관리자
                // header('Location: ./user.php');
            } else {    // unknown error
                header('Location: ./signin.php');
            }
        } else {
            echo 5;
            echo "<script>alert('로그인에 실패했습니다.')</script>";
        }
    }

    function rememberEmail() {
        $encrypted_email = $_COOKIE['memoly_user'];
        if (isset($encrypted_email)) {
            echo Crypto::decryptEmail($encrypted_email);
        }
    }

    function __destruct() {
        $email = $_POST['email'];
        $password = $_POST['hidden-password'];
        $remember = $_POST['remember'];
        if (isset($email) && isset($password)) {
            $this->checkUser($email, $password, $remember);
        } else {
            $this->render();
        }
    }

}

?>