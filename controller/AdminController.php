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
     * 컨트롤러 생성시
     * needAuthentication -> 로그인 여부를 확인하고
     * checkAdmin -> 관리자 여부를 확인한다.
     */
    function __construct() {
        $this->needAuthentication();
        $this->checkAdmin();
    }

    /**
     * 화면 구성을 담당한다.
     */
    function render() {
        $this->documentHead();
        $this->header();
        $this->adminView();
        $this->footer();
        $this->documentFoot();
    }

    /**
     * 컨트롤러에 바인딩 될 뷰를 호출한다.
     */
    function adminView() {
        require_once VIEW.'AdminView.php';
    }

    /**
     * UserModel을 통해 회원 목록을 불러온다.
     * 결과값은 user_list 변수에 할당한다.
     */
    function fetchUserList($page = 1, $count = 10) {
        $user = new UserModel();
        $error = $user->fetchList($page, $count);

        if ($error[0] === Database::SUCCESS) {
            $this->user_list = $user->get();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }

        $this->countUser(); // 페이징을 위해 전체 회원 수를 조회한다.
    }

    /**
     * 회원을 검색한다.
     * 검색 기준은 이메일과 닉네임 두 가지이다.
     */
    function searchUser($option, $search, $page = 1, $count = 10) {
        $user = new UserModel();
        $error;

        if ($option === '이메일') {
            $error = $user->searchByEmail($search);
            $this->item_count = 1;  // 이메일은 중복될 수 없다.
        } else if ($option === '닉네임') {
            $error = $user->searchByNickname($search, $page, $count);
            $this->countUserByNickname($search);    // 닉네임은 중복이 가능하므로 같은 닉네임을 가진 회원 수를 조회한다.
        }

        if ($error[0] === Database::SUCCESS) {
            $this->user_list = $user->get();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    /**
     * 페이징에 필요한 전체 회원 수를 각 조건에 맞게 조회한다.
     * 결과값은 BoardController로부터 상속받은 item_count 변수에 할당한다.
     */
    function countUser() {
        $user = new UserModel();
        $error = $user->countAll();

        if ($error[0] === Database::SUCCESS) {
            $this->item_count = $user->count();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    function countUserByNickname($nickname) {
        $user = new UserModel();
        $error = $user->countByNickname($nickname);

        if ($error[0] === Database::SUCCESS) {
            $this->item_count = $user->count();
        } else {
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

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> 모델을 통해 조건에 맞는 데이터를 받아오고 render를 호출하여 화면을 출력한다.
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

            if (isset($option) && isset($search)) {
                $this->searchUser($option, $search, $this->current_page);
            } else {
                $this->fetchUserList($this->current_page);
            }
        } else {
            exit;
        }
        parent::__destruct();   // render
    }

}

?>