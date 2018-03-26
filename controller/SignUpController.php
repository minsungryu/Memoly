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
     * 회원 모델
     */
    private $user_model;

    /**
     * 인증 모델
     */
    private $auth_model;
    
    /**
     * 컨트롤러 생성시
     * 이미 로그인되어있는 사용자는 접근할 수 없도록 한다.
     */
    function __construct() {
        $this->checkSession();
        $this->user_model = new UserModel();
        $this->auth_model = new AuthModel();
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
            $this->error('잘못된 이메일입니다.');
        }

        if (!$password || !$password_confirm) {
            $this->error('비밀번호를 입력해주세요.');
        }

        if ($password !== $password_confirm) {
            $this->error('비밀번호를 다시 확인해주십시오.');
        }

        $nickname_len = strlen($nickname);
        if (!$nickname || $nickname_len < 2 || 16 < $nickname_len) {
            $this->error('잘못된 닉네임입니다.');
        }

        if (!$terms || $terms !== 'on') {
            $this->error('약관에 동의해주십시오.');
        }

        $password = Crypto::hashPassword($password);

        try {
            $result = $this->user_model->signup($email, $password, $nickname);

            if ($result === 1) { // 결과 row가 1이면 성공
                $token = $this->addToken($email, $nickname);
                $this->sendMail($email, $nickname, $token);
                echo 1;
            } else {
                // 아이디 중복 검사해야함.
                $this->error('회원가입에 실패했습니다.');
            }
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        }
    }

    /**
     * DB에 인증 토큰을 추가한다.
     */
    function addToken($email, $nickname) {
        $token = Crypto::encryptAES($nickname.'#'.$email);
        
        try {
            if ($this->auth_model->addToken($token, $email) === 1) {
                return $token;
            } else {
                $this->error('회원가입에 과정에서 오류가 발생했습니다.');
            }
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        }
    }

    /**
     *  가입한 회원에게 인증 메일을 발송한다.
     */
    function sendMail($email, $nickname, $token) {
        try {
            $mail = new Mailer();
            $mail->verifySubject($nickname);
            $mail->verifyBody($nickname, SERVER_HOST.'signup.php?token='.urlencode($token));
            $mail->to($email, $nickname);
            $mail->send();
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        }
    }

    /**
     * 토큰을 분석하여 이메일을 인증하고 회원가입을 완료시킨다.
     */
    function verifyEmail($token) { 
        try {
            $result = $this->auth_model->verifyEmail($token);

            if ($result) {
                $this->alert('인증에 성공했습니다! 로그인 페이지로 이동합니다.');
            } else {
                $this->alert('유효하지 않은 토큰입니다. 로그인 페이지로 이동합니다.');
            }
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        } finally {
            $this->redirect(SERVER_HOST.'signin.php');
        }
    }

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> render에서 뷰를 구성하고 호출하거나, 토큰이 있으면 인증과정을 수행한다.
     * HTTP METHOD가 POST이면 -> 회원가입 요청을 수행한다.
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
            parent::__destruct();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['hidden-email'];
            $password = $_POST['hidden-password'];
            $password_confirm = $_POST['hidden-password-confirm'];
            $nickname = $_POST['hidden-nickname'];
            $terms = $_POST['hidden-terms'];
            $this->checkForm($email, $password, $password_confirm, $nickname, $terms);
            exit;
        } else {
            exit;
        }
    }

}

?>