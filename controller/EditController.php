<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class EditController extends Controller {

    function __construct() {
        $this->needAuthentication();
    }

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

    function editForm() {
        require_once VIEW.'EditView.php';
    }

    function checkEditForm($email, $password, $new_password, $new_password_confirm, $nickname) {
        // 이메일 유효성 체크
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->alert('잘못된 이메일입니다.');
            return;
        }

        if ($password === $new_password || $password === $new_password_confirm) {
            $this->alert('다른 비밀번호를 입력해주십시오.');
            return;
        }
        
        if ($new_password !== $new_password_confirm) {
            $this->alert('비밀번호를 다시 확인해주십시오.');
            return;
        }
        
        $nickname_len = strlen($nickname);
        if ($nickname_len < 2 || 16 < $nickname_len) {
            $this->alert('잘못된 닉네임입니다.');
            return;
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

    function checkLeaveForm($email, $password, $nickname) {
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

    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
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
        
        parent::__destruct();
    }

}

?>