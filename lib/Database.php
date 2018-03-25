<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

class Database {

    /**
     * PDO 객체 변수
     */
    private $pdo;

    /**
     * MySQL statement 변수
     */
    private $statement;

    /**
     * 요청 성공시 errorInfo 객체는 00000을 반환한다.
     */
    const SUCCESS = '00000';

    /**
     * 중복으로 인한 요청 실패시 errorInfo 객체는 23000을 반환한다.
     */
    const DUPLICATE = '23000';

    /**
     * 환경변수 혹은 .env 파일에 저장된 값을 통해 PDO 객체를 초기화한다.
     */
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

    /**
     * PDO 객체를 소멸시켜 MySQL과 연결을 끊는다.
     */
    function __destruct() {
        $this->pdo = null;
    }

}

?>