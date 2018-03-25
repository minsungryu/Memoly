<?php

require_once 'Model.php';

class UserModel extends Model {

    /**
     * 로그인
     * 마지막 로그인 일자를 수정하기 위해 트랙잭션 수행
     */
    function signin($email, $password) {
        try {
            $this->db->beginTransaction();

            $statement = $this->db->prepare('SELECT user_no, user_email, user_nickname, is_admin, is_verified, signup_date, last_login, memo_count FROM user WHERE user_email = :email AND user_password = :password');
            $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
            $statement->bindParam(':password', $password, PDO::PARAM_STR, 60);

            if (!$statement->execute()) {
                throw new Exception('오류가 발생했습니다.');
            }

            $user = $statement->fetch();

            $statement = $this->db->prepare('UPDATE user SET last_login = CURRENT_TIMESTAMP WHERE user_email = :email AND user_password = :password');
            $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
            $statement->bindParam(':password', $password, PDO::PARAM_STR, 60);

            if (!$statement->execute()) {
                throw new Exception('오류가 발생했습니다.');
            }

            if ($statement->rowCount() === 1) {
                $this->db->commit();
            } else {
                throw new Exception('오류가 발생했습니다.');
            }

            return $user;
        } catch (Exception $e) {
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * 회원가입 수행
     */
    function signup($email, $password, $nickname) {
        $statement = $this->db->prepare('INSERT INTO user(user_email, user_password, user_nickname) VALUES (:email, :password, :nickname)');
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $statement->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $statement->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);

        if ($statement->execute()) {
            return $statement->rowCount();
        } else {
            // throw
        }
    }

    /**
     * 본인인증 수행
     */
    function confirm($email, $nickname) {
        $statement = $this->db->prepare('SELECT user_no, user_email, user_nickname, is_admin, is_verified, signup_date, last_login, memo_count FROM user WHERE user_email = :email AND user_nickname = :nickname ORDER BY user_no');
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $statement->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);

        if ($statement->execute()) {
            return $statement->fetch();
        } else {
            // throw
        }
    }

    /**
     * 회원정보 수정을 위해 DB를 업데이트한다.
     */
    function update($email, $nickname, $password = null, $new_password = null) {
        if (!$new_password) {
            return $this->updateWithoutNewPassword($email, $nickname, $password);
        }

        $statement = $this->db->prepare('UPDATE user SET user_password = :new_password, user_nickname = :nickname WHERE user_email = :email AND (user_password = :password or :password is null)');
        $statement->bindParam(':new_password', $new_password, PDO::PARAM_STR, 60);
        $statement->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $statement->bindParam(':password', $password, PDO::PARAM_STR, 60);

        if ($statement->execute()) {
            return $statement->rowCount();
        } else {
            throw new Exception('문제가 발생했습니다.');
        }
    }

    /**
     * 비밀번호를 제외하고 업데이트 할 경우.
     */
    private function updateWithoutNewPassword($email, $nickname, $password = null) {
        $statement = $this->db->prepare('UPDATE user SET user_nickname = :nickname WHERE user_email = :email AND (user_password = :password or :password is null)');
        $statement->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $statement->bindParam(':password', $password, PDO::PARAM_STR, 60);

        if ($statement->execute()) {
            return $statement->rowCount();
        } else {
            throw new Exception('문제가 발생했습니다.');
        }
    }

    /**
     * 회원 탈퇴를 위해 DB에서 삭제를 수행한다.
     */
    function delete($email, $nickname, $password = null) {
        $statement = $this->db->prepare('DELETE FROM user WHERE user_email = :email AND (user_password = :password or :password is null) AND user_nickname = :nickname AND is_admin <> 1');
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $statement->bindParam(':password', $password, PDO::PARAM_STR, 60);
        $statement->bindParam(':nickname', $nickname, PDO::PARAM_STR, 16);
        
        if ($statement->execute()) {
            return $statement->rowCount();
        } else {
            // throw
        }
    }

    /**
     * [관리자 모드]
     * user_no를 통해 특정 회원을 불러온다.
     */
    function fetch($user_no) {
        $statement = $this->db->prepare('SELECT user_no, user_email, user_nickname, is_admin, is_verified, signup_date, last_login, memo_count FROM user WHERE user_no = :user_no');
        $statement->bindParam(':user_no', $user_no, PDO::PARAM_INT);
        
        if ($statement->execute()) {
            return $statement->fetch();
        } else {
            // throw
        }
    }

    /**
     * [관리자 모드]
     * 관리자를 제외하고 전체 회원을 불러온다. 이메일 또는 닉네임으로 회원을 검색할 수 있다.
     */
    function search($option = null, $page = 1, $count = 10) {
        $offset = ($page - 1) * $count;
        $email_word = $option['이메일'] ? $option['이메일'].'%' : null;
        $nickname_word = $option['닉네임'] ? '%'.$option['닉네임'].'%' : null;

        $statement = $this->db->prepare('SELECT user_no, user_email, user_nickname, is_admin, is_verified, signup_date, last_login, memo_count FROM user WHERE (user_email LIKE :email or :email is null) AND (user_nickname LIKE :nickname or :nickname is null) AND is_admin <> 1 ORDER BY user_no DESC LIMIT :start, :end');
        $statement->bindParam(':email', $email_word, PDO::PARAM_STR, 255);
        $statement->bindParam(':nickname', $nickname_word, PDO::PARAM_STR, 16);
        $statement->bindParam(':start', $offset, PDO::PARAM_INT);
        $statement->bindParam(':end', $count, PDO::PARAM_INT);
        
        if ($statement->execute()) {
            return $statement->fetchAll();
        } else {
            // throw
        }
    }

    /**
     * [관리자 모드]
     * countAll - 관리자를 제외하고 조건에 맞는 회원 수를 계산한다.
     */
    function count($option = null) {
        $email_word = $option['이메일'] ? $option['이메일'].'%' : null;
        $nickname_word = $option['닉네임'] ? '%'.$option['닉네임'].'%' : null;

        $statement = $this->db->prepare('SELECT count(*) FROM user WHERE (user_email LIKE :email or :email is null) AND (user_nickname LIKE :nickname or :nickname is null) AND is_admin <> 1');
        $statement->bindParam(':email', $email_word, PDO::PARAM_STR, 255);
        $statement->bindParam(':nickname', $nickname_word, PDO::PARAM_STR, 16);

        if ($statement->execute()) {
            return $statement->fetchColumn(0);
        } else {
            // throw
        }
    }

}

?>