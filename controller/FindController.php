<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once LIB.'Mailer.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class FindController extends Controller {

    /**
     * 회원 모델
     */
    private $user_model;
    
    /**
     * 컨트롤러 생성시
     * 이미 로그인되어있는 사용자는 접근할 수 없도록 한다.
     */
    function __construct() {
        $this->checkSession();
        $this->user_model = new UserModel();
    }

    /**
     * 화면 구성을 담당한다.
     */
    function render() {
        $this->documentHead();
        $this->findForm();
        $option = [
            'script' => [
                JS.'jquery.validate.min.js',
                JS.'find.js'
            ]
        ];
        $this->appendScript($option);
        $this->documentFoot();
    }

    /**
     * 컨트롤러에 바인딩 될 뷰를 호출한다.
     */
    function findForm() {
        require_once VIEW.'FindView.php';
    }

    /**
     * 폼으로 넘겨받은 데이터의 유효성을 체크하고
     * 이메일과 닉네임을 통해 회원 본인이 맞는지 확인한다.
     */
    function confirmUser($email, $nickname) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('잘못된 이메일입니다.');
        }

        $nickname_len = strlen($nickname);
        if (!$nickname || $nickname_len < 2 || 16 < $nickname_len) {
            $this->error('잘못된 닉네임입니다.');
        }

        try {
            // 본인확인
            $user = $this->user_model->confirm($email, $nickname);

            if (!$user || $user['is_admin'] === '1') { // 관리자는 감춘다.
                $this->error('존재하지 않는 사용자입니다.');
            }

            // 임시 비밀번호 생성 후 저장
            $random_string = Crypto::generateRandomString();
            $sha512_string = hash('sha512', $random_string);
            $temp_password = Crypto::hashPassword($sha512_string);

            $result = $this->user_model->update($email, $nickname, null, $temp_password);

            if ($result == 1) {
                // 메일 발송
                $this->sendMail($email, $nickname, $random_string);                
            } else {
                $this->error('오류가 발생했습니다.');    
            }

            echo 1;
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        }
    }

    /**
     *  비밀번호 찾기를 요청한 회원에게 임시 비밀번호가 담긴 메일을 발송한다.
     */
    function sendMail($email, $nickname, $temp_password) {
        try {
            $mail = new Mailer();
            $mail->tempPassSubject($nickname);
            $mail->tempPassBody($nickname, $temp_password);
            $mail->to($email, $nickname);
            $mail->send();
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        }
    }

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> render에서 뷰를 구성하고 호출한다.
     * HTTP METHOD가 POST이면 -> 본인 확인을 수행한다.
     * HTTP METHOD가 PUT이면 -> 비정상적인 요청으로 간주하고 종료한다.
     * HTTP METHOD가 DELETE이면 -> 비정상적인 요청으로 간주하고 종료한다.
     */
    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            parent::__destruct();   // render
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $nickname = $_POST['nickname'];
            $this->confirmUser($email, $nickname);
            exit;
        } else {
            exit;
        }
    }

}

?>