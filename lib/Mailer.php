<?php

require_once dirname(__DIR__).'/lib/const.php';
require_once LIB.'loader.php';

use PHPMailer\PHPMailer\PHPMailer;

class Mailer {

    /**
     * PHPMailer 변수
     */
    private $mail;

    /**
     * 메일 객체 초기화
     */
    function __construct() {
        $this->mail = new PHPMailer();

        $this->mail->isSMTP();                                      // SMTP 설정
        $this->mail->Host = 'smtp.gmail.com';                       // gmail 사용
        $this->mail->SMTPAuth = true;
        $this->mail->Username = 'godo.memoly@gmail.com';            // 메일 주소
        $this->mail->Password = 'godomall5';                        // 비밀번호
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = 587;

        $this->mail->setFrom('godo.memoly@gmail.com', 'Memoly');    // 발신자 지정

        $this->mail->isHTML(true);                                  // HTML 메일로 설정
    }

    /**
     * 메일 제목 설정
     */
    function subject($nickname) {
        $this->mail->Subject = '[Memoly] '.$nickname.'님의 가입을 축하합니다!';
    }

    /**
     * 메일 본문 지정
     */
    function body($nickname, $link) {
        $this->mail->Body = '';
        $this->mail->Body .= '<h1>'.$nickname.'님의 가입을 진심으로 환영합니다!</h1>';
        $this->mail->Body .= '<h3><a href="'.$link.'">링크</a>을 클릭하여 회원가입을 완료해주세요.</h3>';
    }

    /**
     * HTML 전송 불가시 대체 문구 설정
     */
    function alt($link) {
        $this->mail->AltBody = '메일이 보이지 않을 경우 '.$link.' 에 접속하여 인증을 완료하십시오.';
    }

    /**
     * 수신자 지정
     */
    function to($email, $nickname) {
        $this->mail->addAddress($email, $nickname);
    }

    /**
     * 메일 발송
     */
    function send() {
        $this->mail->send();
    }

    /**
     * 메일 객체 소멸
     */
    function __destruct() {
        $this->mail = null;
    }

}

?>