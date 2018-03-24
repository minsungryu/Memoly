<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class FindController extends Controller {

    function __construct() {
        $this->checkSession();
    }

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

    function findForm() {
        require_once VIEW.'FindView.php';
    }

    function confirmUser($email, $nickname) {
        // 이메일 유효성 체크
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

    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $nickname = $_POST['nickname'];
            $this->confirmUser($email, $nickname);
        }
        parent::__destruct();
    }

}

?>