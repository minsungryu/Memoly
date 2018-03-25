<?php

require_once 'Model.php';

class MemoModel extends Model {

    /**
     * 회원(이메일)의 메모를 추가한다.
     */
    function add($email, $title = '제목 없음', $content = '') {
        $statement = $this->db->prepare('INSERT INTO memo(memo_title, memo_content, memo_user) VALUES (:title, :content, :email)');
        $statement->bindParam(':title', $title, PDO::PARAM_STR, 255);
        $statement->bindParam(':content', $content, PDO::PARAM_STR, 1000);
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        
        if ($statement->execute()) {
            return $statement->rowCount();
        } else {
            // throw
        }
    }

    /**
     * 회원(이메일)이 선택한 메모를 수정한다.
     */
    function update($email, $id, $title, $content) {
        $statement = $this->db->prepare('UPDATE memo SET memo_title = :title, memo_content = :content WHERE memo_id = :id AND memo_user = :email');
        $statement->bindParam(':title', $title, PDO::PARAM_STR, 255);
        $statement->bindParam(':content', $content, PDO::PARAM_STR, 1000);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        
        if ($statement->execute()) {
            return $statement->rowCount();
        } else {
            // throw
        }
    }

    /**
     * 회원(이메일)이 선택한 메모를 삭제한다.
     */
    function delete($email, $id) {
        $statement = $this->db->prepare('DELETE FROM memo WHERE memo_id = :id AND memo_user = :email');
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        
        if ($statement->execute()) {
            return $statement->rowCount();
        } else {
            // throw
        }
    }

    /**
     * 회원(이메일)이 작성한 메모를 검색한다.
     * 검색 조건은 내용과 제목 두 가지이다.
     */
    function search($email, $option = null, $page = 1, $count = 15) {
        $offset = ($page - 1) * $count;
        $content_word = $option['내용'] ? '%'.$option['내용'].'%' : null;
        $title_word = $option['제목'] ? '%'.$option['제목'].'%' : null;

        $statement = $this->db->prepare('SELECT * FROM memo WHERE memo_user = :email AND (memo_content LIKE :content or :content is null) AND (memo_title LIKE :title or :title is null) ORDER BY memo_id LIMIT :start, :end');
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $statement->bindParam(':content', $content_word, PDO::PARAM_STR, 1000);
        $statement->bindParam(':title', $title_word, PDO::PARAM_STR, 255);
        $statement->bindParam(':start', $offset, PDO::PARAM_INT);
        $statement->bindParam(':end', $count, PDO::PARAM_INT);
        
        if ($statement->execute()) {
            return $statement->fetchAll();
        } else {
            // throw
        }
    }

    /**
     * 회원(이메일)이 작성한 메모의 수를 계산한다.
     * 모든 메모의 수를 검색하거나 특정 내용, 제목을 포함한 메모 수를 계산할 수 있다.
     */
    function count($email, $option = null) {
        $content_word = $option['내용'] ? '%'.$option['내용'].'%' : null;
        $title_word = $option['제목'] ? '%'.$option['제목'].'%' : null;

        $statement = $this->db->prepare('SELECT count(*) FROM memo WHERE memo_user = :email AND (memo_content LIKE :content or :content is null) AND (memo_title LIKE :title or :title is null)');
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $statement->bindParam(':content', $content_word, PDO::PARAM_STR, 1000);
        $statement->bindParam(':title', $title_word, PDO::PARAM_STR, 255);

        if ($statement->execute()) {
            return $statement->fetchColumn(0);
        } else {
            // throw
        }
    }
    
}

?>