<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class SignInController extends Controller {

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

    /**
     * 컨트롤러에 바인딩 될 뷰를 호출한다.
     */
    function signInForm() {
        require_once VIEW.'SignInView.php';
    }

    /**
     * 로그인 폼으로 넘겨받은 데이터의 유효성을 체크하고
     * 이메일과 비밀번호를 통해 회원 본인이 맞는지 확인한다.
     */
    function checkUser($email, $password, $remember) {
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
                $encrypted_email = Crypto::encryptAES($email);
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

            $signin_user = $user->get();
            if ($signin_user['is_admin'] === '0') { // 일반 사용자
                if ($this->isEmailVerified($signin_user)) {
                    $_SESSION = $signin_user;
                    header('Location: ./memo.php');
                } else {
                    $this->alert('아직 이메일 인증이 완료되지 않았습니다. 메일을 받지 못한 경우 관리자에게 문의하세요. godo.memoly@gmail.com');
                }
            } else if ($signin_user['is_admin'] === '1') { // 관리자
                $_SESSION = $signin_user;
                header('Location: ./admin.php');
            } else {    // 로그인 실패(NULL)
                $this->alert('로그인에 실패했습니다.');
            }
        } else { // Database 쿼리 오류
            $this->alert('로그인에 실패했습니다.');
        }
    }

    /**
     * 사용자의 이메일을 기억하기 위해 쿠키로 저장된 값을 복호화한다.
     */
    function rememberEmail() {
        $encrypted_email = $_COOKIE['memoly_user'];
        if (isset($encrypted_email)) {
            echo Crypto::decryptAES($encrypted_email);
        }
    }

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> render에서 뷰를 구성하고 호출한다.
     * HTTP METHOD가 POST이면 -> 로그인 요청을 확인하고 결과를 보여준다.(일관성을 위해 AJAX로 변경할까?)
     * HTTP METHOD가 PUT이면 -> 비정상적인 요청으로 간주하고 종료한다.
     * HTTP METHOD가 DELETE이면 -> 비정상적인 요청으로 간주하고 종료한다.
     */
    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // do nothing
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['hidden-password'];
            $remember = $_POST['remember'];
            $this->checkUser($email, $password, $remember);
        } else {
            exit;
        }
        parent::__destruct();   // render
    }

}

?>