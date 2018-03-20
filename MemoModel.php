<?php

require_once 'Model.php';

class MemoModel extends Model {

    private $memo_array; // array of memo object

    function __construct() {
        parent::__construct();
    }

    function get() {
        return $this->memo_array;
    }

    function pull($email, $page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        $statement = $this->prepare('SELECT * FROM memo WHERE memo_user = :email ORDER BY memo_created LIMIT :start, :end');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':start', $offset, PDO::PARAM_INT);
        $this->bindParam(':end', $limit, PDO::PARAM_INT);
        $this->execute();
        $memo_array = $statement->fetch();
        $this->memo_array = $memo_array;
        return $statement->errorInfo();
    }

    function add($email, $title = '제목 없음', $content = '') {
        $statement = $this->prepare('INSERT INTO memo(memo_title, memo_content, memo_user) VALUES (:title, :content, :email)');
        $this->bindParam(':title', $title, PDO::PARAM_STR, 255);
        $this->bindParam(':content', $content, PDO::PARAM_STR, 1000);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        return $statement->errorInfo();
    }

    function update($email, $id, $title, $content) {
        $statement = $this->prepare('UPDATE memo SET memo_title = :title, memo_content = :content WHERE memo_id = :id AND memo_user = :email');
        $this->bindParam(':title', $title, PDO::PARAM_STR, 255);
        $this->bindParam(':content', $content, PDO::PARAM_STR, 1000);
        $this->bindParam(':id', $id, PDO::PARAM_INT);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        return $statement->errorInfo();
    }

    function delete($email, $id) {
        $statement = $this->prepare('DELETE FROM memo WHERE memo_id = :id AND memo_user = :email');
        $this->bindParam(':id', $id, PDO::PARAM_INT);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        return $statement->errorInfo();
    }

}

?>