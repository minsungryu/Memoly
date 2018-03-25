<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once LIB.'Mailer.php';
require_once MODEL.'UserModel.php';
require_once MODEL.'AuthModel.php';
require_once 'Controller.php';

class SignUpController extends Controller {

    /**
     * 컨트롤러 생성시
     * 이미 로그인되어있는 사용자는 접근할 수 없도록 한다.
     */
    function __construct() {
        $this->checkSession();
    }

    /**
     * 화면 구성을 담당한다.
     */
    function render() {
        $this->documentHead();
        $this->signUpForm();
        $option = [
            'script' => [
                JS.'jquery.validate.min.js',
                JS.'validate.js',
                JS.'sha512.js',
                JS.'signup.js'
            ],
            'style' => [
                CSS.'signup.css'
            ]
        ];
        $this->appendScript($option);
        $this->documentFoot();
    }

    /**
     * 컨트롤러에 바인딩 될 뷰를 호출한다.
     */
    function signUpForm() {
        require_once VIEW.'SignUpView.php';
    }

    /**
     * 회원가입 폼으로 넘겨받은 데이터의 유효성을 체크한다.
     */
    function checkForm($email, $password, $password_confirm, $nickname, $terms) {
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
            $insert_row = $user->count();
            if ($insert_row === 1) {
                $token = Crypto::encryptAES($nickname.'#'.$email);
                $auth = new AuthModel();
                $auth->addToken($token, $email);

                $mail = new Mailer();
                $mail->subject($nickname);
                $mail->body($nickname, SERVER_HOST.'signup.php?token='.urlencode($token));
                $mail->to($email, $nickname);
                $mail->send();

                $this->alert('회원가입을 축하합니다! 로그인 페이지로 이동합니다.');
                $this->redirect(SERVER_HOST.'signin.php');
            } else if($insert_row === 0) {
                $this->alert('회원가입에 실패했습니다.');
            } else {
                $this->alert('회원가입에 실패했습니다.');
            }
        } else if ($error[0] === Database::DUPLICATE) {
            $this->alert('중복된 아이디입니다.');
        } else { // Database 쿼리 오류
            $this->alert('회원가입에 실패했습니다.');
        }
    }

    /**
     * 토큰을 분석하여 이메일을 인증하고 회원가입을 완료시킨다.
     */
    function verifyEmail($token) {
        $auth = new AuthModel();
        $success = $auth->verifyEmail(urldecode($token));

        if ($success) {
            $this->alert('인증에 성공했습니다! 로그인 페이지로 이동합니다.');
        } else {
            $this->alert('유효하지 않은 토큰입니다. 로그인 페이지로 이동합니다.');
        }
        $this->redirect(SERVER_HOST.'signin.php');
    }

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> render에서 뷰를 구성하고 호출하거나, 토큰이 있으면 인증과정을 수행한다.
     * HTTP METHOD가 POST이면 -> 로그인 요청을 확인하고 결과를 보여준다.(일관성을 위해 AJAX로 변경할까?)
     * HTTP METHOD가 PUT이면 -> 비정상적인 요청으로 간주하고 종료한다.
     * HTTP METHOD가 DELETE이면 -> 비정상적인 요청으로 간주하고 종료한다.
     */
    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $token = $_GET['token'];
            if (isset($token)) {
                $this->verifyEmail($token);
                exit;
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['hidden-password'];
            $password_confirm = $_POST['hidden-password-confirm'];
            $nickname = $_POST['nickname'];
            $terms = $_POST['terms'];
            $this->checkForm($email, $password, $password_confirm, $nickname, $terms);
        } else {
            exit;
        }
        parent::__destruct();
    }

}

?>