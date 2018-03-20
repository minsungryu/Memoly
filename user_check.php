<?php
session_start();

include_once 'lib/loader.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$email = $_POST['email'];
$password = $_POST['password'];

// 잘못된 접근
if (!isset($email) || !isset($password)) {
  header('Location: ./signin.php');
  exit;
}

// 유효성 체크
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: ./signin.php');
  exit;
}

$hash_option = ['salt'=> md5('godomall5')];
$password = password_hash($password, PASSWORD_BCRYPT, $hash_option);

// DB 연결 후 로그인 성공을 가정
if (true) {
  $remember = $_POST['remember'];
  if (isset($remember)) {
    $cipher = 'AES256';
    $key = getenv('AES256_KEY');
    if (in_array($cipher, openssl_get_cipher_methods(true))) {
        $cipher_email = openssl_encrypt($email, $cipher, $key);
        setcookie('memoly_user', $cipher_email, time() + 60 * 60 * 24 * 14, '/signin.php'); // 14days
        setcookie('memoly_remember', 'checked="checked"', time() + 60 * 60 * 24 * 14, '/signin.php');
    }
  } else {
    // 쿠키 제거
    setcookie('memoly_user', '', time() - 1, '/signin.php');
    setcookie('memoly_remember', '', time() - 1, '/signin.php');
  }

  $_SESSION['is_login'] = true;
  $_SESSION['is_admin'] = false;
  $_SESSION['nickname'] = '민성';
  // 사용자 - 메모 목록
  // 관리자 - 회원 목록
  header('Location: ./memo.php');
} else {
  // 로그인 실패
}
?>