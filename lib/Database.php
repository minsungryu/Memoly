<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';

function connectDatabase() {    
    $dotenv = new Dotenv\Dotenv(ROOT);
    $dotenv->load();

    $host = getenv('MYSQL_HOST');
    $db = getenv('MYSQL_DATABASE');
    $user = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');

    return new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $password);
}

?>