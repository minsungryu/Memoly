<?php

require_once dirname(__DIR__).'/lib/const.php';

/**
 * 화면을 그려주는 render를 정의한 인터페이스
 */
interface renderable {

    public function render();

}

/**
 * renderable을 구현하는 추상 클래스 Controller
 * 공통적으로 필요한 기능들을 포함한다.
 */
abstract class Controller implements renderable {

    /**
     * 하위 클래스에서 반드시 구현해야 한다.
     */
    abstract function render();

    /**
     * html 선언부터 head에 포함된 공통 스타일 및 스크립트를 포함
     * body 태그를 연다.
     */
    function documentHead() {
        require_once VIEW.'document_head.php';
    }

    /**
     * 화면 상단에 표시될 내용
     */
    function header() {
        require_once VIEW.'header.php';
    }
    
    /**
     * 화면 하단에 표시될 내용
     */
    function footer() {
        require_once VIEW.'footer.php';
    }

    /**
     * body 및 html 태그를 닫음
     */
    function documentFoot() {
        require_once VIEW.'document_foot.php';
    }

    /**
     * 로그인이 필요한 경우 signin 페이지로 이동한다.
     */
    function needAuthentication() {
        if (empty($_SESSION)) {
            header('Location: ./signin.php');
        }
    }

    /**
     * 이미 로그인되어 있는 경우 접근하지 못하게 한다.
     * 예시) 로그인, 회원가입, 비밀번호 찾기
     */
    function checkSession() {
        if (!empty($_SESSION)) {
            header('Location: ./memo.php');
        }
    }

    /**
     * 접속한 사용자의 이메일 인증 여부를 반환한다.
     */
    function isEmailVerified($user) {
        if ($user['is_verified'] === 'Y') {
            return true;
        }
        return false;
    }

    /**
     * 관리자인지 확인 후 권한이 없다면 메모 페이지로 이동한다.
     */
    function checkAdmin() {
        if ($_SESSION['is_admin'] === '0') {
            header('Location: ./memo.php');
        }
    }

    /**
     * 스타일 및 스크립트를 추가할 수 있다.
     */
    function appendScript($option = null) {
        if (!is_array($option)) {
            return;
        }
        
        foreach($option as $tag => $src_array) {
            foreach($src_array as $url) {
                if ($tag === 'style') {
                    echo '<link rel="stylesheet" href="'.$url.'">';
                } else if ($tag === 'script') {
                    echo '<script type="text/javascript" src="'.$url.'"></script>';
                }
            }
        }
    }

    /**
     * 화면에 alert 창을 띄운다.
     */
    function alert($message) {
        echo '<script>alert('.json_encode($message).');</script>';
    }
 
    /**
     * 페이지를 이동한다.
     */
    function redirect($url) {
        echo '<script>window.location = "'.$url.'";</script>';
    }

    /**
     * 컨트롤러의 생명주기에 의해 마지막에 render를 호출한다.
     */
    function __destruct() {
        $this->render();
    }

}

?>