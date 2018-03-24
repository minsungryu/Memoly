<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once MODEL.'MemoModel.php';
require_once 'BoardController.php';

class MemoController extends BoardController {
    
    private $memo_list;

    function __construct() {
        $this->needAuthentication();
        $this->page_offset = 15;
    }

    function render() {
        $this->documentHead();
        $this->header();
        $this->memoView();
        $option = [
            'script' => [JS.'memo.js'],
            'style' => [CSS.'memo.css']
        ];
        $this->appendScript($option);
        $this->footer();
        $this->documentFoot();
    }

    function memoView() {
        require_once VIEW.'MemoView.php';
    }

    function fetchMemoList($page = 1, $count = 15) {
        $memo = new MemoModel();
        $error = $memo->pull($_SESSION['user_email'], $page, $count);
        if ($error[0] === Database::SUCCESS) {
            $this->memo_list = $memo->get();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }

        $this->countMemo();
    }

    function searchMemo($option, $search, $page = 1, $count = 15) {
        $memo = new MemoModel();
        $error;

        if ($option === '내용') {
            $error = $memo->searchByContent($_SESSION['user_email'], $search, $page, $count);
            $this->countMemoByContent($search);
        } else if ($option === '제목') {
            $error = $memo->searchByTitle($_SESSION['user_email'], $search, $page, $count);
            $this->countMemoByTitle($search);
        }

        if ($error[0] === Database::SUCCESS) {
            $this->memo_list = $memo->get();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

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
        parent::__destruct();
    }

}

?>