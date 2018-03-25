<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once 'Controller.php';

abstract class BoardController extends Controller {

    /**
     * 전체 아이템의 갯수
     * 페이지를 계산하기 위해 필요하다.
     */
    protected $item_count = 0;

    /**
     * 마지막 페이지
     * [다음]버튼의 활성화 여부와 관련있다.
     */
    protected $last_page = 1;

    /**
     * 현재 페이지
     * 최대 5개의 페이지를 보여주고 현재 페이지는 가운데 위치한다.
     * [이전]버튼의 활성화 여부와 관련있다.
     */
    protected $current_page = 1;

    /**
     * 한 페이지에 보여질 아이템의 갯수
     */
    protected $page_offset = 10;
    
    /**
     * 원하는 페이지를 요청할 수 있는 url을 반환한다.
     * 현재 컨트롤러에 종속된다.
     */
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

    /**
     * 마지막 페이지를 반환한다.
     */
    function getLastPage() {
        return intval(ceil($this->item_count / $this->page_offset));
    }

    /**
     * 페이지 내비게이터 뷰를 호출한다.
     */
    function pageNavigator() {
        require_once VIEW.'PageNavigator.php';
    }

}

?>