<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once MODEL.'MemoModel.php';
require_once 'BoardController.php';

class MemoController extends BoardController {

    /**
     * 메모 모델
     */
    private $memo_model;
    
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
        if (!$this->isEmailVerified($_SESSION)) {
            header('Location: ./signin.php');
        }
        $this->needAuthentication();
        $this->memo_model = new MemoModel();
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
        $this->memoView();
        $this->footer();
        $this->appendScript($option);
        $this->documentFoot();
    }

    /**
     * 컨트롤러에 바인딩 될 뷰를 호출한다.
     * memoView -> 메모 목록 출력
     */
    function memoView() {
        require_once VIEW.'MemoView.php';
    }

    /**
     * 메모를 검색하거나 전체 조회한다.
     * 검색 기준은 제목과 내용 두 가지이다.
     */
    function searchMemo($option, $search, $page = 1, $count = 15) {
        try {
            $this->memo_list = $this->memo_model->search($_SESSION['user_email'], [$option => $search], $page, $count);
            $this->item_count = $this->memo_model->count($_SESSION['user_email'], [$option => $search]);
        } catch (Exception $e) {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    /**
     * 메모를 추가, 수정, 삭제한다.
     */
    function addMemo($memo_title, $memo_content) {
        try {
            $result = $this->memo_model->add($_SESSION['user_email'], $memo_title, $memo_content);

            if ($result === 1) {
                echo 1;
            } else {
                $this->alert('메모를 추가하는데 실패했습니다.');
            }
        } catch (Exception $e) {
            $this->alert('오류가 발생했습니다.');
        }
    }

    function updateMemo($memo_id, $memo_title, $memo_content) {
        try {
            $result = $this->memo_model->update($_SESSION['user_email'], $memo_id, $memo_title, $memo_content);

            if ($result === 1) {
                echo 1;
            } else {
                $this->alert('메모를 수정하는데 실패했습니다.');
            }
        } catch (Exception $e) {
            $this->alert('오류가 발생했습니다.');
        }
    }

    function deleteMemo($memo_id) {
        try {
            $result = $this->memo_model->delete($_SESSION['user_email'], $memo_id);

            if ($result === 1) {
                echo 1;
            } else {
                $this->alert('메모를 삭제하는데 실패했습니다.');
            }
        } catch (Exception $e) {
            $this->alert('오류가 발생했습니다.');
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
            
            $this->searchMemo($option, $search, $this->current_page);
            parent::__destruct();   // render
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $memo_user = $_POST['memo_user'];
            if ($_SESSION['user_email'] === $memo_user) {
                $memo_title = $_POST['memo_title'];
                $memo_content = $_POST['memo_content'];
                $this->addMemo($memo_title, $memo_content);
            } else {
                $this->alert('비정상적인 접근입니다.');
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
            } else {
                $this->alert('비정상적인 접근입니다.');
            }
            exit;
        } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            parse_str(file_get_contents("php://input"), $_DELETE);
            $memo_user = $_DELETE['memo_user'];
            if ($_SESSION['user_email'] === $memo_user) {
                $memo_id = $_DELETE['memo_id'];
                $this->deleteMemo($memo_id);
            } else {
                $this->alert('비정상적인 접근입니다.');
            }
            exit;
        }
    }

}

?>