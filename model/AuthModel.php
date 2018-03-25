<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once 'Model.php';

class AuthModel extends Model {

    private $count;

    function count() {
        return $this->count();
    }

    /**
     * 인증 대기 중인 회원의 토큰을 저장한다.
     */
    function addToken($token, $email) {
        $statement = $this->prepare('INSERT INTO auth VALUES (:token, :email)');
        $this->bindParam(':token', $token, PDO::PARAM_STR, 128);
        $this->bindParam(':email', $email, PDO::PARAM_STR, 255);
        $this->execute();
        $this->count = $statement->rowCount();
        return $statement->errorInfo();
    }

    /**
     * 토큰 인증을 수행한다.
     * 토큰 리스트에서 해당 토큰의 존재 여부를 파악하고
     * 토큰을 복호화 후 이메일을 추출하여 해당 회원의 인증을 수행한다.
     */
    function verifyEmail($token) {
        $statement;
        $success = false;
        try {
            $this->beginTransaction();

            $statement = $this->prepare('SELECT user_email FROM auth WHERE token = :token');
            $this->bindParam(':token', $token, PDO::PARAM_STR, 128);
            $this->execute();
            $result = $statement->fetch();
            $user_email = $result[0];
            
            if (!$user_email) {
                throw new Exception("존재하지 않는 토큰입니다.");
            }

            $decrypted_token = Crypto::decryptAES($token);
            $extracted_email = explode('#', $decrypted_token)[1];

            if ($user_email !== $extracted_email) {
                throw new Exception("잘못된 접근입니다.");
            }

            $statement = $this->prepare('UPDATE user SET is_verified = \'Y\' WHERE user_email = :email');
            $this->bindParam(':email', $user_email, PDO::PARAM_STR, 255);
            $this->execute();
            $count = $statement->rowCount();

            if ($count !== 1) {
                throw new Exception("잘못된 접근입니다.");
            }

            $statement = $this->prepare('DELETE FROM auth WHERE token = :token AND user_email = :email');
            $this->bindParam(':token', $token, PDO::PARAM_STR, 128);
            $this->bindParam(':email', $user_email, PDO::PARAM_STR, 255);
            $this->execute();
            $count = $statement->rowCount();

            if ($count !== 1) {
                throw new Exception("잘못된 접근입니다.");
            }
            
            $success = true;
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
        } finally {
            return $success;
            // return $statement->errorInfo();
        }
    }

}

?>