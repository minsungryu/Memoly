<?php

define('ROOT', dirname(__DIR__).'/');

define('CONTROLLER',ROOT.'contriller/');
define('MODEL', ROOT.'model/');
define('VIEW', ROOT.'view/');

define('LIB', ROOT.'lib/');
define('VENDOR', ROOT.'vendor/');

define('SERVER_HOST', "http://{$_SERVER['HTTP_HOST']}/");
define('PUBLIC_ASSETS', SERVER_HOST.'public/');
define('CSS', PUBLIC_ASSETS.'css/');
define('IMG', PUBLIC_ASSETS.'img/');
define('JS', PUBLIC_ASSETS.'js/');

?>