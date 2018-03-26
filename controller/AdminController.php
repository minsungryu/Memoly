<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once MODEL.'UserModel.php';
require_once 'BoardController.php';

class AdminController extends BoardController {

    /**
     * 회원 목록 배열
     */
    private $user_list;

    /**
     * 회원 모델
     */
    private $user_model;

    /**
     * 컨트롤러 생성시
     * needAuthentication -> 로그인 여부를 확인하고
     * checkAdmin -> 관리자 여부를 확인한다.
     */
    function __construct() {
        $this->needAuthentication();
        $this->checkAdmin();
        $this->user_model = new UserModel();
    }

    /**
     * 화면 구성을 담당한다.
     */
    function render() {
        $this->documentHead();
        $this->header();
        $this->adminView();
        $this->footer();
        $option = ['script' => [JS.'admin.js']];
        $this->appendScript($option);
        $this->documentFoot();
    }

    /**
     * 컨트롤러에 바인딩 될 뷰를 호출한다.
     */
    function adminView() {
        require_once VIEW.'AdminView.php';
    }

    /**
     * 회원을 검색하거나 전체 조회한다.
     * 검색 기준은 이메일과 닉네임 두 가지이다.
     */
    function searchUser($option, $search, $page = 1, $count = 10) {
        try {
            $this->user_list = $this->user_model->search([$option => $search], $page, $count);
            $this->item_count = $this->user_model->count([$option => $search]);
        } catch (Exception $e) {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    /**
     * 개인정보보호를 위해 회원의 이메일을 일부만 노출한다.
     * 앞의 5글자와 도메인을 제외한 나머지 글자는 *****로 감춘다.
     * 만약 이메일 앞 부분의 길이가 5글자 미만일 경우 해당 길이만큼만 노출한다.
     */
    function hideEmail($user_email) {
        $email = explode('@', $user_email);
        $id = $email[0];
        $show_len = min(5, strlen($id));
        $email[0] = substr($id, 0, $show_len).'*****';
        return implode('@', $email);
    }

    function deleteChecked($user_emails) {
        try {
            $result = $this->user_model->multipleDelete($user_emails);

            if ($result == 0) {
                $this->error('삭제된 회원이 없습니다.');
            }
            
            echo $result;
        } catch (Exception $e) {
            $this->error('오류가 발생했습니다.');
        }
    }

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> 모델을 통해 조건에 맞는 데이터를 받아오고 render를 호출하여 화면을 출력한다.
     * HTTP METHOD가 DELETE이면 -> 선택한 회원을 삭제한다.(AJAX - 화면 호출하지 않음)
     * 이외의 요청에 대해서는 비정상 접근으로 간주하고 종료한다.
     */
    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $option = $_GET['option'];
            $search = $_GET['search'];
            $page = $_GET['page'];

            if (!isset($page) || $page < 1) {   // 비정상적인 페이지는 1로 설정한다.
                $this->current_page = 1;
            } else {
                $this->current_page = $page;
            }

            $this->searchUser($option, $search, $this->current_page);
            parent::__destruct();   // render
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            parse_str(file_get_contents("php://input"), $_DELETE);
            $user_emails = $_DELETE['user_emails'];
            $this->deleteChecked($user_emails);
            exit;
        } else {
            exit;
        }
    }

}

?>