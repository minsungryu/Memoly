<?php

require_once 'Model.php';

class UserModel extends Model {

    /**
     * 회원 데이터를 저장하는 변수
     */
    private $user;

    /**
     * 회원 수를 저장하는 변수
     */
    private $count;

    /**
     * 회원 반환
     */
    function get() {
        return $this->user;
    }

    /**
     * 회원 수 반환
     */
    function count() {
        return $this->count;
    }

    /**
     * 로그인 수행
     * 마지막 로그인 일자를 수정하기 위해 트랙잭션 수행
     */
    function signin($email, $password) {
        $statement;
        try {
            $this->beginTransaction();
            $statement = $this->prepare('SELECT user_email, user_nickname, is_admin, is_verified, signup_date, last_login, memo_count FROM user WHERE user_email = :email AND user_password = :password');
            $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
            $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
            $this->execute();
            $this->user = $statement->fetch();

            $statement = $this->prepare('UPDATE user SET last_login = CURRENT_TIMESTAMP WHERE user_email = :email AND user_password = :password');
            $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
            $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
            $this->execute();

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
        } finally {
            return $statement->errorInfo();
        }
    }

    /**
     * 회원가입 수행
     */
    function signup($email, $password, $nickname) {
        $statement = $this->prepare('INSERT INTO user(user_email, user_password, user_nickname) VALUES (:email, :password, :nickname)');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    /**
     * 본인인증 수행
     */
    function confirm($email, $nickname) {
        $statement = $this->prepare('SELECT user_email, user_nickname, is_admin, is_verified, signup_date, last_login, memo_count FROM user WHERE user_email = :email AND user_nickname = :nickname ORDER BY user_no');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->execute();
        $this->user = $statement->fetch();
        return $statement->errorInfo();
    }

    /**
     * 회원정보 수정을 위해 DB를 업데이트한다.
     */
    function update($email, $password, $new_password, $nickname) {
        $statement = $this->prepare('UPDATE user SET user_password = :new_password, user_nickname = :nickname WHERE user_email = :email AND user_password = :password');
        $this->bindParam(':new_password', $new_password, PDO::PARAM_STR, 60);
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    /**
     * 회원 탈퇴를 위해 DB에서 삭제를 수행한다.
     */
    function delete($email, $password, $nickname) {
        $statement = $this->prepare('DELETE FROM user WHERE user_email = :email AND user_password = :password AND user_nickname = :nickname AND is_admin <> 1');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    /**
     * [관리자 모드]
     * 관리자를 제외하고 전체 회원 목록을 불러온다.
     */
    function fetchList($page = 1, $count = 10) {
        $statement = $this->prepare('SELECT * FROM user WHERE is_admin <> 1 ORDER BY user_no LIMIT :start, :end');
        $this->bindParam(':start', ($page - 1) * $count, PDO::PARAM_INT);
        $this->bindParam(':end', $count, PDO::PARAM_INT);
        $this->execute();
        $this->user = $statement->fetchAll();
        return $statement->errorInfo();
    }

    /**
     * [관리자 모드]
     * 관리자를 제외하고 이메일로 회원을 검색한다.
     */
    function searchByEmail($email) {
        $statement = $this->prepare('SELECT * FROM user WHERE user_email LIKE :email AND is_admin <> 1 ORDER BY user_no');
        $this->bindParam(':email', $email.'%', PDO::PARAM_STR, 255);
        $this->execute();
        $this->user = $statement->fetchAll();
        return $statement->errorInfo();
    }

    /**
     * [관리자 모드]
     * 관리자를 제외하고 닉네임으로 회원을 검색한다.
     * 닉네임은 중복이 가능하므로 여러명이 검색될 수 있다.
     */
    function searchByNickname($nickname, $page = 1, $count = 10) {
        $statement = $this->prepare('SELECT * FROM user WHERE user_nickname = :nickname AND is_admin <> 1 ORDER BY user_no LIMIT :start, :end');
        $this->bindParam(':nickname', '%'.$nickname.'%', PDO::PARAM_STR, 16);
        $this->bindParam(':start', ($page - 1) * $count, PDO::PARAM_INT);
        $this->bindParam(':end', $count, PDO::PARAM_INT);
        $this->execute();
        $this->user = $statement->fetchAll();
        return $statement->errorInfo();
    }

    /**
     * [관리자 모드]
     * countAll - 관리자를 제외하고 전체 회원 수를 계산한다.
     * countByNickname - 닉네임이 일치하는 회원 수를 계산한다.
     */
    function countAll() {
        $statement = $this->prepare('SELECT count(*) FROM user WHERE is_admin <> 1');
        $this->execute();
        $this->count = $statement->fetchColumn(0);
        return $statement->errorInfo();
    }

    function countByNickname($nickname) {
        $statement = $this->prepare('SELECT count(*) FROM user WHERE user_nickname = :nickname AND is_admin <> 1');
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->execute();
        $this->count = $statement->fetchColumn(0);
        return $statement->errorInfo();
    }

}

?>