<?php
define('DIR_VENDOR', __DIR__.'/vendor/');

if (file_exists(DIR_VENDOR . 'autoload.php')) {
    require_once(DIR_VENDOR . 'autoload.php');
}
?>