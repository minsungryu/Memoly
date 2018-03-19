<?php

require_once 'Model.php';

class UserModel extends Model {

    private $user;

    function __construct() {
        parent::__construct();
    }

    function get() {
        return $this->user;
    }

    function signin($email, $password) {
        $statement = $this->prepare('SELECT * FROM user WHERE user_email = :email AND user_password = :password');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $this->execute();
        $user = $statement->fetch();
        $this->user = $user;
        return $statement->errorInfo();
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
        $statement = $this->prepare('SELECT * FROM user WHERE user_email = :email AND user_nickname = :nickname');
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $this->execute();
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

}

?>