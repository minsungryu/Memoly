<?php

require_once 'const.php';

class Controller {

    function __construct($option = null) {
        require_once 'document_head.php';
        $this->render();
    }

    function render() {

    }

    function appendScript($option = null) {
        if (is_array($option)) {
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

    function __destruct() {
        require_once 'document_foot.php';
    }

}

?>