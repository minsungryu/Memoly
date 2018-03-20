<?php

require_once dirname(__DIR__).'/lib/const.php';

interface renderable {

    public function render();

}

abstract class Controller implements renderable {

    abstract function render();

    function documentHead() {
        require_once VIEW.'document_head.php';
    }

    function header() {
        require_once VIEW.'header.php';
    }

    function footer() {
        require_once VIEW.'footer.php';
    }

    function documentFoot() {
        require_once VIEW.'document_foot.php';
    }

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
    
}

?>