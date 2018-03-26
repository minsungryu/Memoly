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

        $this->mail->CharSet = 'EUC-KR';
        $this->mail->Encoding = 'base64';

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
     * 인증 메일 제목 설정
     */
    function verifySubject($nickname) {
        $this->mail->Subject = '[Memoly] '.$nickname.'님의 가입을 축하합니다!';
        $this->mail->Subject = $this->encodingSubject($this->mail->Subject);
    }

    /**
     * 인증 메일 본문 지정
     */
    function verifyBody($nickname, $link) {
        $this->mail->Body = '';
        $this->mail->Body .= '<h1>'.$nickname.'님의 가입을 진심으로 환영합니다!</h1>';
        $this->mail->Body .= '<h3><a href="'.$link.'">링크</a>을 클릭하여 회원가입을 완료해주세요.</h3>';
    }

    /**
     * 인증 메일 HTML 전송 불가시 대체 문구 설정
     */
    function verifyAlt($link) {
        $this->mail->AltBody = '메일이 보이지 않을 경우 '.$link.' 에 접속하여 인증을 완료하십시오.';
    }

    /**
     * 임시 비밀번호 메일 제목 설정
     */
    function tempPassSubject($nickname) {
        $this->mail->Subject = '[Memoly] '.$nickname.'님, 임시 비밀번호를 발급해드립니다.';
        $this->mail->Subject = $this->encodingSubject($this->mail->Subject);
    }

    /**
     * 임시 비밀번호 본문 지정
     */
    function tempPassBody($nickname, $temp_password) {
        $this->mail->Body = '';
        $this->mail->Body .= '<p>'.$nickname.'님의 임시 비밀번호는 <strong>'.$temp_password.'</strong>입니다.</p>';
        $this->mail->Body .= '<p>로그인 후 반드시 비밀번호를 변경해주세요.</p>';
    }

    /**
     * 임시 비밀번호 메일 HTML 전송 불가시 대체 문구 설정
     */
    function tempPassAlt($temp_password) {
        $this->mail->AltBody = '임시 비밀번호 '.$temp_password.' 로 로그인 후 반드시 비밀번호를 재설정해주세요.';
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
     * 메일 제목 한글 인코딩
     */
    function encodingSubject($text) {
        return '=?EUC-KR?B?'.base64_encode(iconv('utf-8', 'euc-kr', $text)).'?=';
    }

    /**
     * 메일 객체 소멸
     */
    function __destruct() {
        $this->mail = null;
    }

}

?>