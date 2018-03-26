<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Database.php';

class Model {

    /**
     * DB 연결 객체를 저장할 변수
     */
    protected $db;

    /**
     * 모델 객체 생성 시 DB와 연결을 수행한다.
     */
    function __construct() {
        $this->db = connectDatabase();
    }
    
    /**
     * 모델 객체 생성 시 DB와 연결을 종료한다.
     */
    function __destruct() {
        $this->db = null;
    }

}

?>