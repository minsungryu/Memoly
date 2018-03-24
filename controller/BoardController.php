<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once 'Controller.php';

abstract class BoardController extends Controller {

    protected $item_count = 0;
    protected $last_page = 1;
    protected $current_page = 1;
    protected $page_offset = 10;
    
    function getPage($page = 1) {
        $url = explode('?', $_SERVER['REQUEST_URI'])[0].'?';
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

    function getLastPage() {
        return intval(ceil($this->item_count / $this->page_offset));
    }

}

?>