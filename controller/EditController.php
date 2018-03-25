<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class EditController extends Controller {

    /**
     * 컨트롤러 생성시
     * 로그인되어있지 않다면 페이지를 이동한다.
     */
    function __construct() {
        $this->needAuthentication();
    }

    /**
     * 화면 구성을 담당한다.
     */
    function render() {
        $this->documentHead();
        $this->editForm();
        $option = [
            'script' => [
                JS.'jquery.validate.min.js',
                JS.'validate.js',
                JS.'sha512.js',
                JS.'edit.js'
            ]
        ];
        $this->appendScript($option);
        $this->documentFoot();
    }

    /**
     * 컨트롤러에 바인딩 될 뷰를 호출한다.
     */
    function editForm() {
        require_once VIEW.'EditView.php';
    }

    /**
     * 회원정보수정 폼으로 넘겨받은 데이터의 유효성을 체크한다.
     */
    function checkEditForm($email, $password, $new_password, $new_password_confirm, $nickname) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->alert('잘못된 이메일입니다.');
            return 0;
        }

        if ($password === $new_password || $password === $new_password_confirm) {
            $this->alert('다른 비밀번호를 입력해주십시오.');
            return 0;
        }
        
        if ($new_password !== $new_password_confirm) {
            $this->alert('비밀번호를 다시 확인해주십시오.');
            return 0;
        }
        
        $nickname_len = strlen($nickname);
        if ($nickname_len < 2 || 16 < $nickname_len) {
            $this->alert('잘못된 닉네임입니다.');
            return 0;
        }

        $password = Crypto::hashPassword($password);
        $new_password = Crypto::hashPassword($new_password);
        $user = new UserModel();
        $error = $user->update($email, $password, $new_password, $nickname);

        if ($error[0] === Database::SUCCESS) {
            $updated_row = $user->count();
            echo $updated_row;
            // if ($updated_row === 1) {
            //     $this->alert('성공적으로 수정되었습니다. 다시 로그인 해 주십시오.');
            //     $this->redirect(SERVER_HOST.'signout.php');
            // } else if ($updated_row === 0) {
            //     $this->alert('회원정보 수정에 실패했습니다.');
            // } else {
            //     $this->alert('회원정보 수정에 실패했습니다. 잠시 후 다시 시도해주세요.');
            // }
        } else { // Database 쿼리 오류
            // $this->alert('회원정보 수정에 실패했습니다.');
            echo 0;
        }
    }

    /**
     * 회원탈퇴 폼으로 넘겨받은 데이터의 유효성을 체크한다.
     */
    function checkLeaveForm($email, $password, $nickname) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->alert('잘못된 이메일입니다.');
            return;
        }

        $nickname_len = strlen($nickname);
        if ($nickname_len < 2 || 16 < $nickname_len) {
            $this->alert('잘못된 닉네임입니다.');
            return;
        }

        $password = Crypto::hashPassword($password);
        $user = new UserModel();
        $error = $user->delete($email, $password, $nickname);

        if ($error[0] === Database::SUCCESS) {
            $deleted_row = $user->count();
            if ($deleted_row === 1) {
                $this->alert('성공적으로 탈퇴하였습니다. 이용해주셔서 감사합니다.');
                $this->redirect(SERVER_HOST.'signout.php');
            } else if ($deleted_row === 0) {
                $this->alert('회원탈퇴에 실패했습니다. 비밀번호와 닉네임을 정확히 입력해주세요.');
            } else {
                $this->alert('회원탈퇴에 실패했습니다. 잠시 후 다시 시도해주십시오.');
            }
        } else { // Database 쿼리 오류
            $this->alert('회원탈퇴에 실패했습니다. 잠시 후 다시 시도해주십시오.');
        }
    }

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> render에서 뷰를 구성하고 호출한다.
     * HTTP METHOD가 POST이면 -> 비정상적인 요청으로 간주하고 종료한다.
     * HTTP METHOD가 PUT이면 -> 폼 검사 후 정보를 수정한다.(AJAX - 화면 호출하지 않음)
     * HTTP METHOD가 DELETE이면 -> 폼 검사 후 삭제를 수행한다.(AJAX - 화면 호출하지 않음)
     */
    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            parent::__destruct();   // render
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            parse_str(file_get_contents("php://input"), $_PUT);
            $email = $_PUT['email'];
            $password = $_PUT['hidden-password'];
            $new_password = $_PUT['hidden-new-password'];
            $new_password_confirm = $_PUT['hidden-new-password-confirm'];
            $nickname = $_PUT['nickname'];
            $this->checkEditForm($email, $password, $new_password, $new_password_confirm, $nickname);
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            parse_str(file_get_contents("php://input"), $_DELETE);
            $email = $_DELETE['email'];
            $password = $_DELETE['hidden-password'];
            $nickname = $_DELETE['nickname'];
            $this->checkLeaveForm($email, $password, $nickname);
            exit;
        }
    }

}

?>