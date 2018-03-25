<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'Crypto.php';
require_once 'Model.php';

class AuthModel extends Model {

    /**
     * 인증 대기 중인 회원의 토큰을 저장한다.
     */
    function addToken($token, $email) {
        $statement = $this->db->prepare('INSERT INTO auth VALUES (:token, :email)');
        $statement->bindParam(':token', $token, PDO::PARAM_STR, 128);
        $statement->bindParam(':email', $email, PDO::PARAM_STR, 255);
        
        if ($statement->execute()) {
            return $statement->rowCount();
        } else {
            // throw
        }
    }

    /**
     * 토큰 인증을 수행한다.
     * 토큰 리스트에서 해당 토큰의 존재 여부를 파악하고
     * 토큰을 복호화 후 이메일을 추출하여 해당 회원의 인증을 수행한다.
     */
    function verifyEmail($token) {
        try {
            $this->beginTransaction();

            // 토큰이 유효한지 검사
            $statement = $this->db->prepare('SELECT user_email FROM auth WHERE token = :token');
            $statement->bindParam(':token', $token, PDO::PARAM_STR, 128);
            
            if (!$statement->execute()) {
                throw new Exception('오류가 발생했습니다.');
            }

            $result = $statement->fetch();
            $emali = $result['user_email'];
            
            if (!$email) {
                throw new Exception('인증 정보가 없습니다.');
            }

            // 토큰 해석 및 이메일 추출
            $decrypted_token = Crypto::decryptAES($token);
            $extracted_email = explode('#', $decrypted_token)[1];

            if ($email !== $extracted_email) {
                throw new Exception("인증 과정에서 오류가 발생했습니다.");
            }

            // 회원의 인증 여부를 Y로 변경
            $statement = $this->db->prepare('UPDATE user SET is_verified = \'Y\' WHERE user_email = :email');
            $statement->bindParam(':email', $user_email, PDO::PARAM_STR, 255);
            
            if (!$statement->execute()) {
                throw new Exception('오류가 발생했습니다.');
            }

            if ($statement->rowCount() !== 1) {
                throw new Exception("잘못된 접근입니다.");
            }

            // 토큰을 테이블에서 삭제
            $statement = $this->db->prepare('DELETE FROM auth WHERE token = :token AND user_email = :email');
            $statement->bindParam(':token', $token, PDO::PARAM_STR, 128);
            $statement->bindParam(':email', $user_email, PDO::PARAM_STR, 255);
            
            if (!$statement->execute()) {
                throw new Exception("잘못된 접근입니다.");
            }

            if ($statement->rowCount() !== 1) {
                throw new Exception("잘못된 접근입니다.");
            }

            $this->commit();
            return 1;
        } catch (Exception $e) {
            $this->rollBack();
            return 0;
        }
    }

}

?>