<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class SignInController extends Controller {

    function __construct() {
        $this->checkSession();
    }

    function render() {
        $this->documentHead();
        $this->signInForm();
        $option = [
            'script' => [
                JS.'jquery.validate.min.js',
                JS.'validate.js',
                JS.'sha512.js',
                JS.'signin.js'
            ]
        ];
        $this->appendScript($option);
        $this->documentFoot();
    }

    function signInForm() {
        require_once VIEW.'SignInView.php';
    }

    function checkUser($email, $password, $remember) {
        // 이메일 유효성 체크
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->alert('잘못된 이메일입니다.');
            return;
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
                header('Location: ./admin.php');
            } else {    // 로그인 실패(NULL)
                $this->alert('로그인에 실패했습니다.');
            }
        } else { // Database 쿼리 오류
            $this->alert('로그인에 실패했습니다.');
        }
    }

    function rememberEmail() {
        $encrypted_email = $_COOKIE['memoly_user'];
        if (isset($encrypted_email)) {
            echo Crypto::decryptEmail($encrypted_email);
        }
    }

    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['hidden-password'];
            $remember = $_POST['remember'];
            $this->checkUser($email, $password, $remember);
        }
        parent::__destruct();
    }

}

?>