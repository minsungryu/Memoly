<?php
session_start();

require_once dirname(__DIR__).'/lib/const.php';
require_once MODEL.'UserModel.php';
require_once 'Controller.php';

class AdminController extends Controller {

    private $user_list;
    private $user_count;
    private $last_page;
    private $current_page = 1;
    private $page_offset = 10;

    function __construct() {
        $this->needAuthentication();
    }

    function render() {
        $this->documentHead();
        $this->header();
        $this->adminView();
        $this->appendScript();
        $this->footer();
        $this->documentFoot();
    }

    function adminView() {
        require_once VIEW.'AdminView.php';
    }

    function fetchUserList($page = 1, $count = 10) {
        $user = new UserModel();
        $error = $user->fetchList($page, $count);
        if ($error[0] === Database::SUCCESS) {
            $this->user_list = $user->get();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }

        $this->countUser();
    }

    function searchUser($option, $search, $page = 1, $count = 10) {
        $user = new UserModel();
        $error;

        if ($option === '이메일') {
            $error = $user->searchByEmail($search);
            $this->user_count = 1;
        } else if ($option === '닉네임') {
            $error = $user->searchByNickname($search, $page, $count);
            $this->countUserByNickname($search);
        }

        if ($error[0] === Database::SUCCESS) {
            $this->user_list = $user->get();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    function hideEmail($user_email) {
        $email = explode('@', $user_email);
        $id = $email[0];
        $domain = $email[1];
        $id_len = strlen($id);
        if ($id_len < 5) {
            $id = substr($id, 0, $id_len);
        } else {
            $id = substr($id, 0, 5);
        }
        $id .= '*****';
        $email[0] = $id;
        return implode('@', $email);
    }

    function getPage($page = 1) {
        $url = 'admin.php?';
        if ($page < 1) {
            $page = 1;
        }
        $url .= 'page='.$page;
        if (isset($_GET['option'])) {
            $url .= '&option='.$_GET['option'];
        }
        if (isset($_GET['search'])) {
            $url .= '&search='.$_GET['search'];
        }

        return $url;
    }

    function countUser() {
        $user = new UserModel();
        $error = $user->countAll();

        if ($error[0] === Database::SUCCESS) {
            $this->user_count = $user->count();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    function countUserByNickname($nickname) {
        $user = new UserModel();
        $error = $user->countByNickname($nickname);

        if ($error[0] === Database::SUCCESS) {
            $this->user_count = $user->count();
        } else {
            $this->alert('데이터를 받아오는데 실패했습니다.');
        }
    }

    function getLastPage() {
        return intval(ceil($this->user_count / $this->page_offset));
    }

    function __destruct() {
        $option = $_GET['option'];
        $search = $_GET['search'];
        $page = $_GET['page'];

        if (!isset($page) || $page < 1) {
            $this->current_page = 1;
        } else {
            $this->current_page = $page;
        }

        if (isset($option) && isset($search)) {
            $this->searchUser($option, $search, $this->current_page);
        } else {
            $this->fetchUserList($this->current_page);
        }
        $this->render();
    }

}

?>