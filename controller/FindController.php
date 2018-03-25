<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
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
        if ($nickname || $nickname_len < 2 || 16 < $nickname_len) {
            $this->error('잘못된 닉네임입니다.');
        }

        try {
            $user = $this->user_model->confirm($email, $nickname);

            if (!$user || $user['is_admin'] === '1') { // 관리자는 감춘다.
                $this->error('존재하지 않는 사용자입니다.');
            }
    
            echo 1;
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