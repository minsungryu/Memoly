<?php
session_start();

include_once 'loader.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$email = $_POST['email'];
$password = $_POST['hidden-password'];
$password_confirm = $_POST['hidden-password-confirm'];
$nickname = $_POST['nickname'];

// 잘못된 접근
if (!isset($email) || !isset($password) || !isset($password_confirm) || !isset($nickname)) {
  header('Location: ./signup.php');
  exit;
}

// 유효성 체크
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password !== $password_confirm || strlen($nickname) < 2 || 16 < strlen($nickname)) {
  header('Location: ./signup.php');
  exit;
}

$password = password_hash($password, PASSWORD_BCRYPT);
$password_confirm = password_hash($password_confirm, PASSWORD_BCRYPT);

// DB 연결 후 삽입 성공을 가정
if (true) {

  // 사용자 - 메모 목록
  // 관리자 - 회원 목록
  // header('Location: ./welcome.html');
} else {
  // 가입 실패
}
?>