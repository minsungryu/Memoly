<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

class Database {

    private $pdo;
    private $statement;

    const SUCCESS = '00000';
    const DUPLICATE = '23000';

    function __construct() {        
        $host = getenv('MYSQL_HOST');
        $db = getenv('MYSQL_DATABASE');
        $user = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');
        $this->pdo = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $password);
    }

    function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    function commit() {
        return $this->pdo->commit();
    }

    function rollBack() {
        return $this->pdo->rollBack();
    }

    function query($sql) {
        return $this->pdo->query($sql);
    }

    function prepare($sql) {
        $this->statement = $this->pdo->prepare($sql);
        return $this->statement;
    }

    function bindParam($param, $value, $data_type = PDO::PARAM_STR, $length = 16, $driver_options = null) {
        return $this->statement->bindParam($param, $value, $data_type, $length, $driver_options);
    }

    function execute($params = null) {
        return $this->statement->execute($params);
    }

    function fetch() {
        return $this->statement->fetch();
    }

    function __destruct() {
        $this->pdo = null;
    }

}

?>