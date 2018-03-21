<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class SignUpController extends Controller {

    function __construct() {
        $this->checkSession();
    }

    function render() {
        $this->documentHead();
        $this->signUpForm();
        $option = [
            'script' => [
                'https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js',
                JS.'validate.js',
                JS.'sha512.js',
                JS.'signup.js'],
            'style' => [
                CSS.'common.css',
                CSS.'signup.css'
            ]
        ];
        $this->appendScript($option);
        $this->documentFoot();
    }

    function signUpForm() {
        require_once VIEW.'SignUpView.php';
    }

    function checkSession() {
        if(isset($_SESSION['user_email'])){
            if (isset($_SESSION['is_admin'])) {
                header('Location: ./user.php');
            } else {
                header('Location: ./memo.php');
            }
        }
    }

    function checkForm($email, $password, $password_confirm, $nickname, $terms) {
        // 이메일 유효성 체크
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->alert('잘못된 이메일입니다.');
            return;
        }

        if ($password !== $password_confirm) {
            $this->alert('비밀번호를 다시 확인해주십시오.');
            return;
        }

        $nickname_len = strlen($nickname);
        if ($nickname_len < 2 || 16 < $nickname_len) {
            $this->alert('잘못된 닉네임입니다.');
            return;
        }

        if ($terms !== 'on') {
            $this->alert('약관에 동의해주십시오.');
            return;
        }

        $password = Crypto::hashPassword($password);
        $user = new UserModel();
        $error = $user->signup($email, $password, $nickname);

        if ($error[0] === Database::SUCCESS) {
            $this->alert('회원가입을 축하합니다!\n로그인 페이지로 이동합니다.');
            $this->redirect(SERVER_HOST.'signin.php');
        } else if ($error[0] === Database::DUPLICATE) {
            $this->alert('중복된 아이디입니다.');
        } else { // Database 쿼리 오류
            $this->alert('회원가입에 실패했습니다.');
        }
    }

    function __destruct() {
        $email = $_POST['email'];
        $password = $_POST['hidden-password'];
        $password_confirm = $_POST['hidden-password-confirm'];
        $nickname = $_POST['nickname'];
        $terms = $_POST['terms'];
        if (isset($email) && isset($password) && isset($password_confirm) && isset($nickname) && isset($terms)) {
            $this->checkForm($email, $password, $password_confirm, $nickname, $terms);
        }
        $this->render();
    }

}

?>