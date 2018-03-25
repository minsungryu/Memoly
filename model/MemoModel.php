<?php

require_once 'Model.php';

class MemoModel extends Model {

    /**
     * 메모 데이터를 저장하는 변수
     */
    private $memo;

    /**
     * 메모 수를 저장하는 변수
     */
    private $count;

    /**
     * 메모 반환
     */
    function get() {
        return $this->memo;
    }

    /**
     * 메모 수 반환
     */
    function count() {
        return $this->count;
    }

    /**
     * 회원(이메일)이 작성한 메모 목록을 불러온다.
     */
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

    /**
     * 회원(이메일)의 메모를 추가한다.
     */
    function add($email, $title = '제목 없음', $content = '') {
        $statement = $this->prepare('INSERT INTO memo(memo_title, memo_content, memo_user) VALUES (:title, :content, :email)');
        $this->bindParam(':title', $title, PDO::PARAM_STR, 255);
        $this->bindParam(':content', $content, PDO::PARAM_STR, 1000);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    /**
     * 회원(이메일)이 선택한 메모를 수정한다.
     */
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

    /**
     * 회원(이메일)이 선택한 메모를 삭제한다.
     */
    function delete($email, $id) {
        $statement = $this->prepare('DELETE FROM memo WHERE memo_id = :id AND memo_user = :email');
        $this->bindParam(':id', $id, PDO::PARAM_INT);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    /**
     * 회원(이메일)이 작성한 메모를 검색한다.
     * 검색 조건은 내용과 제목 두 가지이다.
     */
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

    /**
     * 회원(이메일)이 작성한 메모의 수를 계산한다.
     * 모든 메모의 수를 검색하거나 특정 내용, 제목을 포함한 메모 수를 계산할 수 있다.
     */
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