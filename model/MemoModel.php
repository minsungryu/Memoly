<?php

require_once 'Model.php';

class MemoModel extends Model {

    private $memo;
    private $count;

    function __construct() {
        parent::__construct();
    }

    function get() {
        return $this->memo;
    }

    function count() {
        return $this->count;
    }

    function pull($email, $page = 1, $count = 15) {
        $offset = ($page - 1) * $count;
        $statement = $this->prepare('SELECT * FROM memo WHERE memo_user = :email ORDER BY memo_id DESC LIMIT :start, :end');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':start', $offset, PDO::PARAM_INT);
        $this->bindParam(':end', $count, PDO::PARAM_INT);
        $this->execute();
        $this->memo = $statement->fetchAll();
        return $statement->errorInfo();
    }

    function add($email, $title = '제목 없음', $content = '') {
        $statement = $this->prepare('INSERT INTO memo(memo_title, memo_content, memo_user) VALUES (:title, :content, :email)');
        $this->bindParam(':title', $title, PDO::PARAM_STR, 255);
        $this->bindParam(':content', $content, PDO::PARAM_STR, 1000);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    function update($email, $id, $title, $content) {
        $statement = $this->prepare('UPDATE memo SET memo_title = :title, memo_content = :content WHERE memo_id = :id AND memo_user = :email');
        $this->bindParam(':title', $title, PDO::PARAM_STR, 255);
        $this->bindParam(':content', $content, PDO::PARAM_STR, 1000);
        $this->bindParam(':id', $id, PDO::PARAM_INT);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    function delete($email, $id) {
        $statement = $this->prepare('DELETE FROM memo WHERE memo_id = :id AND memo_user = :email');
        $this->bindParam(':id', $id, PDO::PARAM_INT);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    function searchByContent($email, $content, $page = 1, $count = 15) {
        $offset = ($page - 1) * $count;
        $statement = $this->prepare('SELECT * FROM memo WHERE memo_user = :email AND memo_content LIKE :content ORDER BY memo_id LIMIT :start, :end');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':content', '%'.$content.'%', PDO::PARAM_STR, 1000);
        $this->bindParam(':start', $offset, PDO::PARAM_INT);
        $this->bindParam(':end', $count, PDO::PARAM_INT);
        $this->execute();
        $this->memo = $statement->fetchAll();
        return $statement->errorInfo();
    }

    function searchByTitle($email, $title, $page = 1, $count = 15) {
        $offset = ($page - 1) * $count;
        $statement = $this->prepare('SELECT * FROM memo WHERE memo_user = :email AND memo_title LIKE :title ORDER BY memo_id LIMIT :start, :end');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':title', '%'.$title.'%', PDO::PARAM_STR, 255);
        $this->bindParam(':start', $offset, PDO::PARAM_INT);
        $this->bindParam(':end', $count, PDO::PARAM_INT);
        $this->execute();
        $this->memo = $statement->fetchAll();
        return $statement->errorInfo();
    }

    function countAll($email) {
        $statement = $this->prepare('SELECT count(*) FROM memo WHERE memo_user = :email');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        $this->count = $statement->fetchColumn(0);
        return $statement->errorInfo();
    }

    function countByContent($email, $content) {
        $statement = $this->prepare('SELECT count(*) FROM memo WHERE memo_user = :email AND memo_content LIKE :content');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':content', '%'.$content.'%', PDO::PARAM_STR, 1000);
        $this->execute();
        $this->count = $statement->fetchColumn(0);
        return $statement->errorInfo();
    }

    function countByTitle($email, $title) {
        $statement = $this->prepare('SELECT count(*) FROM memo WHERE memo_user = :email AND memo_title LIKE :title');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':title', '%'.$title.'%', PDO::PARAM_STR, 255);
        $this->execute();
        $this->count = $statement->fetchColumn(0);
        return $statement->errorInfo();
    }
}

?>