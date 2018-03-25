<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once MODEL.'MemoModel.php';
require_once 'BoardController.php';

class MemoController extends BoardController {
    
    /**
     * 메모 목록 배열
     */
    private $memo_list;

    /**
     * 컨트롤러 생성시
     * needAuthentication -> 로그인 여부를 확인하고
     * 한 페이지에 보여질 목록 갯수를 수정한다.
     */
    function __construct() {
        $this->needAuthentication();
        $this->page_offset = 15;
    }

    /**
     * 화면 구성을 담당한다.
     */
    function render() {
        $option = [
            'script' => [JS.'memo.js'],
            'style' => [CSS.'memo.css']
        ];

        $this->documentHead();
        $this->header();
        if ($this->isEmailVerified()) { // 이메일이 인증된 사용자라면 메모 목록을 보여준다.
            $this->memoView();
        } else {                        // 아니라면 인증 대기 화면을 보여준다.
            $this->unverifiedView();
            array_push($option['style'], CSS.'unverified.css');
        }
        $this->footer();
        $this->appendScript($option);
        $this->documentFoot();
    }

    /**
     * 컨트롤러에 바인딩 될 뷰를 호출한다.
     * memoView -> 메모 목록 출력
     * unverifiedView -> 이메일 미인증시 인증 대기화면 출력
     */
    function memoView() {
        require_once VIEW.'MemoView.php';
    }

    function unverifiedView() {
        require_once VIEW.'unverified.php';
    }

    /**
     * 메모 목록을 불러온다.
     */
    function fetchMemoList($page = 1, $count = 15) {
        $memo = new MemoModel();
        $error = $memo->pull($_SESSION['user_email'], $page, $count);
        if ($error[0] === Database::SUCCESS) {
            $this->memo_list = $memo->get();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }

        $this->countMemo(); // 페이징에 필요한 전체 메모 수를 계산한다.
    }

    /**
     * 메모를 검색한다.
     * 검색 기준은 제목과 내용 두 가지이다.
     */
    function searchMemo($option, $search, $page = 1, $count = 15) {
        $memo = new MemoModel();
        $error;

        if ($option === '내용') {
            $error = $memo->searchByContent($_SESSION['user_email'], $search, $page, $count);
            $this->countMemoByContent($search); // 내용이 일치하는 전체 메모 수를 계산한다.
        } else if ($option === '제목') {
            $error = $memo->searchByTitle($_SESSION['user_email'], $search, $page, $count);
            $this->countMemoByTitle($search);   // 제목이 일치하는 전체 메모 수를 계산한다.
        }

        if ($error[0] === Database::SUCCESS) {
            $this->memo_list = $memo->get();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    /**
     * 페이징에 필요한 메모 수를 각 조건에 맞게 조회한다.
     * 결과값은 BoardController로부터 상속받은 item_count 변수에 할당한다.
     */
    function countMemo() {
        $memo = new MemoModel();
        $error = $memo->countAll($_SESSION['user_email']);

        if ($error[0] === Database::SUCCESS) {
            $this->item_count = $memo->count();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    function countMemoByTitle($title) {
        $memo = new MemoModel();
        $error = $memo->countByTitle($_SESSION['user_email'], $title);

        if ($error[0] === Database::SUCCESS) {
            $this->item_count = $memo->count();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    function countMemoByContent($content) {
        $memo = new MemoModel();
        $error = $memo->countByContent($_SESSION['user_email'], $content);

        if ($error[0] === Database::SUCCESS) {
            $this->item_count = $memo->count();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    /**
     * 메모를 추가, 수정, 삭제한다.
     */
    function addMemo($memo_title, $memo_content) {
        $memo = new MemoModel();
        $error = $memo->add($_SESSION['user_email'], $memo_title, $memo_content);

        if ($error[0] === Database::SUCCESS) {
            echo $this->item_count = $memo->count();    // 1(성공), 0(삭제)
        } else {
            echo 0;
        }
    }

    function updateMemo($memo_id, $memo_title, $memo_content) {
        $memo = new MemoModel();
        $error = $memo->update($_SESSION['user_email'], $memo_id, $memo_title, $memo_content);

        if ($error[0] === Database::SUCCESS) {
            echo $this->item_count = $memo->count();    // 1(성공), 0(삭제)
        } else {
            echo 0;
        }
    }

    function deleteMemo($memo_id) {
        $memo = new MemoModel();
        $error = $memo->delete($_SESSION['user_email'], $memo_id);

        if ($error[0] === Database::SUCCESS) {
            echo $this->item_count = $memo->count();    // 1(성공), 0(삭제)
        } else {
            echo 0;
        }
    }

    /**
     * 컨트롤러 소멸시
     * HTTP METHOD가 GET이면 -> 모델을 통해 조건에 맞는 데이터를 받아오고 render를 호출하여 화면을 출력한다.
     * HTTP METHOD가 POST이면 -> 메모를 추가한다.(AJAX - 화면 호출하지 않음)
     * HTTP METHOD가 PUT이면 -> 메모를 수정한다.(AJAX - 화면 호출하지 않음)
     * HTTP METHOD가 DELETE이면 -> 메모를 삭제한다.(AJAX - 화면 호출하지 않음)
     */
    function __destruct() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $option = $_GET['option'];
            $search = $_GET['search'];
            $page = $_GET['page'];
    
            if (!isset($page) || $page < 1) {
                $this->current_page = 1;
            } else {
                $this->current_page = $page;
            }

            if (isset($option) && isset($search)) {
                $this->searchMemo($option, $search, $this->current_page);
            } else {
                $this->fetchMemoList($this->current_page);
            }

            parent::__destruct();   // render
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $memo_user = $_POST['memo_user'];
            if ($_SESSION['user_email'] === $memo_user) {
                $memo_title = $_POST['memo_title'];
                $memo_content = $_POST['memo_content'];
                $this->addMemo($memo_title, $memo_content);
            }
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            parse_str(file_get_contents("php://input"), $_PUT);
            $memo_user = $_PUT['memo_user'];
            if ($_SESSION['user_email'] === $memo_user) {
                $memo_id = $_PUT['memo_id'];
                $memo_title = $_PUT['memo_title'];
                $memo_content = $_PUT['memo_content'];
                $this->updateMemo($memo_id, $memo_title, $memo_content);
            }
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            parse_str(file_get_contents("php://input"), $_DELETE);
            $memo_user = $_DELETE['memo_user'];
            if ($_SESSION['user_email'] === $memo_user) {
                $memo_id = $_DELETE['memo_id'];
                $this->deleteMemo($memo_id);
            }
            exit;
        }
    }

}

?>