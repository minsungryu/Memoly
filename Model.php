<?php

require_once 'Database.php';

class Model {

    protected $db;

    function __construct() {
        $this->db = new Database();
    }

    function query($sql) {
        return $this->db->query($sql);
    }

    function prepare($sql) {
        return $this->db->prepare($sql);
    }

    function bindParam($param, $value, $data_type = PDO::PARAM_STR, $length = 16, $driver_options = null) {
        return $this->db->bindParam($param, $value, $data_type, $length, $driver_options);
    }

    function execute($params = null) {
        return $this->db->execute($params);
    }

    function __destruct() {
        $this->db = null;
    }

}

?>