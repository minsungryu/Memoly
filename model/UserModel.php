<?php

require_once 'Model.php';

class UserModel extends Model {

    private $user;
    private $count;

    function get() {
        return $this->user;
    }

    function count() {
        return $this->count;
    }

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

    function signup($email, $password, $nickname) {
        $statement = $this->prepare('INSERT INTO user(user_email, user_password, user_nickname) VALUES (:email, :password, :nickname)');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->execute();
        return $statement->errorInfo();
    }

    function confirm($email, $nickname) {
        $statement = $this->prepare('SELECT user_email, user_nickname, is_admin, is_verified, signup_date, last_login, memo_count FROM user WHERE user_email = :email AND user_nickname = :nickname ORDER BY user_no');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->execute();
        $this->user = $statement->fetch();
        return $statement->errorInfo();
    }

    function update($email, $password, $new_password, $nickname) {
        $statement = $this->prepare('UPDATE user SET user_password = :new_password, user_nickname = :new_nickname WHERE user_email = :email AND user_password = :password');
        $this->bindParam(':password', $new_password, PDO::PARAM_STR, 60);
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $this->execute();
        return $statement->errorInfo();
    }

    function delete($email, $password) {
        $statement = $this->prepare('DELETE FROM user WHERE user_email = :email AND user_password = :password');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $this->execute();
        return $statement->errorInfo();
    }

    function fetchList($page = 1, $count = 10) {
        $statement = $this->prepare('SELECT * FROM user WHERE is_admin <> 1 ORDER BY user_no LIMIT :start, :end');
        $this->bindParam(':start', ($page - 1) * $count, PDO::PARAM_INT);
        $this->bindParam(':end', $count, PDO::PARAM_INT);
        $this->execute();
        $this->user = $statement->fetchAll();
        return $statement->errorInfo();
    }

    function searchByEmail($email) {
        $statement = $this->prepare('SELECT * FROM user WHERE user_email LIKE :email AND is_admin <> 1 ORDER BY user_no');
        $this->bindParam(':email', $email.'%', PDO::PARAM_STR, 255);
        $this->execute();
        $this->user = $statement->fetchAll();
        return $statement->errorInfo();
    }

    function searchByNickname($nickname, $page = 1, $count = 10) {
        $statement = $this->prepare('SELECT * FROM user WHERE user_nickname = :nickname AND is_admin <> 1 ORDER BY user_no LIMIT :start, :end');
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->bindParam(':start', ($page - 1) * $count, PDO::PARAM_INT);
        $this->bindParam(':end', $count, PDO::PARAM_INT);
        $this->execute();
        $this->user = $statement->fetchAll();
        return $statement->errorInfo();
    }

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