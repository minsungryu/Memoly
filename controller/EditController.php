<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class EditController extends Controller {

    /**
     * 수정중인 회원
     */
    private $user;

    /**
     * 수정중인 회원 모델
     */
    private $user_model;

    /**
     * 컨트롤러 생성시
     * 로그인되어있지 않다면 페이지를 이동한다.
     */
    function __construct() {
        $this->needAuthentication();
        $this->user_model = new UserModel();
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
    function checkEditForm($email, $nickname, $password, $new_password, $new_password_confirm) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('잘못된 이메일입니다.');
        }

        $nickname_len = strlen($nickname);
        if (!$nickname || $nickname_len < 2 || 16 < $nickname_len) {
            $this->error('잘못된 닉네임입니다.');
        }
        
        if (!$new_password && !$new_password_confirm) {
            $password = Crypto::hashPassword($password);
            echo $this->updateUser($email, $nickname, $password);
            return;
        }

        if ($password === $new_password || $password === $new_password_confirm) {
            $this->error('다른 비밀번호를 입력해주십시오.');
        }
        
        if ($new_password !== $new_password_confirm) {
            $this->error('비밀번호를 다시 확인해주십시오.');
        }
        
        $password = Crypto::hashPassword($password);
        $new_password = Crypto::hashPassword($new_password);
        echo $this->updateUser($email, $nickname, $password, $new_password);
    }

    /**
     * 회원탈퇴 폼으로 넘겨받은 데이터의 유효성을 체크한다.
     */
    function checkLeaveForm($email, $nickname, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('잘못된 이메일입니다.');
        }

        $nickname_len = strlen($nickname);
        if (!$nickname || $nickname_len < 2 || 16 < $nickname_len) {
            $this->error('잘못된 닉네임입니다.');
        }

        $password = Crypto::hashPassword($password);
        echo $this->deleteUser($email, $nickname, $password);
    }

    /**
     * 모델로부터 회원 정보를 받아온다.
     */
    function fetchUser($user_no) {
        try {
            $user = $this->user_model->fetch($user_no);

            if (!$user) {
                $this->error('회원 조회에 실패했습니다.');
            }

            $this->user = $user;
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        }
    }

    /**
     * 모델을 통해 회원 정보를 수정한다.
     */
    function updateUser($email, $nickname, $password = null, $new_password = null) {
        try {
            // 관리자는 비밀번호 없이 수정할 수 있다.
            if ($_SESSION['is_admin'] === '1') {
                $password = null;
            }
            
            $result = $this->user_model->update($email, $nickname, $password, $new_password);

            if ($result === 1) {
                // 본인 정보를 수정한 경우 서버에서 다시 받아온다.
                // 관리자는 다른 회원의 정보를 수정할 수 있기 때문
                if ($email === $_SESSION['user_email']) {
                    $_SESSION = $this->user_model->fetch($_SESSION['user_no']);
                }
                echo 1;
            } else {
                $this->error('회원정보 수정에 실패했습니다.');
            }
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        }
    }

    /**
     * 모델을 통해 회원탈퇴를 수행한다.
     */
    function deleteUser($email, $nickname, $password = null) {
        try {
            // 관리자는 비밀번호 없이 삭제할 수 있다.
            if ($_SESSION['is_admin'] === '1') {
                $password = null;
            }

            $result = $this->user_model->delete($email, $nickname, $password);

            if ($result === 0) {
                $this->error('회원탈퇴에 실패했습니다.');
            }
            echo $result;
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
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
            $user_no = $_GET['user-no'] ?? $_SESSION['user_no'];
            $this->fetchUser($user_no);
            parent::__destruct();   // render
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') { // as PUT and DELETE
            $action = $_POST['action'];
            // if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if ($action === 'PUT') {
                // parse_str(file_get_contents("php://input"), $_PUT);
                $email = $_POST['hidden-email'];
                $nickname = $_POST['hidden-nickname'];
                $password = $_POST['hidden-password'];
                $new_password = $_POST['hidden-new-password'];
                $new_password_confirm = $_POST['hidden-new-password-confirm'];
                $this->checkEditForm($email, $nickname, $password, $new_password, $new_password_confirm);
                exit;
            // } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            } else if ($action === 'DELETE') {
                // parse_str(file_get_contents("php://input"), $_DELETE);
                $email = $_POST['hidden-email'];
                $nickname = $_POST['hidden-nickname'];
                $password = $_POST['hidden-password'];
                $this->checkLeaveForm($email, $nickname, $password);
                exit;
            }
            exit;
        }
    }

}

?>