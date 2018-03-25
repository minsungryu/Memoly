<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class FindController extends Controller {

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
            $this->alert('잘못된 이메일입니다.');
            return;
        }

        $nickname_len = strlen($nickname);
        if ($nickname_len < 2 || 16 < $nickname_len) {
            $this->alert('잘못된 닉네임입니다.');
            return;
        }

        $user = new UserModel();
        $error = $user->confirm($email, $nickname);

        if ($error[0] === Database::SUCCESS) {
            $confirmed_user = $user->get();
            if (!$confirmed_user) {
                $this->alert('존재하지 않는 사용자입니다.');
            } else if ($confirmed_user['is_admin'] === '0') {
                $this->alert('확인되었습니다. 회원정보 수정 페이지로 이동합니다.');
                $this->ridirect(SERVER_HOST.'edit.php'); // form? session? email?
            } else if ($confirmed_user['is_admin'] === '1') {
                // 관리자는 감춘다.
                $this->alert('존재하지 않는 사용자입니다.');
            }
        } else { // Database 쿼리 오류
            $this->alert('사용자 확인에 실패했습니다.');
        }
    }

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> render에서 뷰를 구성하고 호출한다.
     * HTTP METHOD가 POST이면 -> 본인 확인을 수행한다.(일관성을 위해 AJAX로 변경할까?)
     * HTTP METHOD가 PUT이면 -> 비정상적인 요청으로 간주하고 종료한다.
     * HTTP METHOD가 DELETE이면 -> 비정상적인 요청으로 간주하고 종료한다.
     */
    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $nickname = $_POST['nickname'];
            $this->confirmUser($email, $nickname);
        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            exit;
        }
        parent::__destruct();   // render
    }

}

?>