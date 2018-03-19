<?php
    session_start();

    if(isset($_SESSION['is_login'])){
        if (isset($_SESSION['is_admin'])) {
            header('Location: ./user.php');
        } else {
            header('Location: ./memo.php');
        }
    }
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='shortcut icon' type='image/ico' href='public/img/favicon.ico'>
    <title>Memoly</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>
    <link href="public/css/common.css" rel="stylesheet">
    <link href="public/css/signin.css" rel="stylesheet">
</head>
<body>
<div class="signin-box">
    <img src="public/img/memoly.png" class="mb-5">
    <form action="user_check.php" method="post" id="form-signin" class="needs-validation" novalidate>
        <div class="form-group">
            <label for="input-email">이메일 주소</label>
            <input type="email" class="form-control" id="input-email" name="email" placeholder="your@example.com" required
            value="<?php
                include_once 'loader.php';

                $dotenv = new Dotenv\Dotenv(__DIR__);
                $dotenv->load();

                $cipher_email = $_COOKIE["memoly_user"];
                if (isset($cipher_email)) {
                    $cipher = 'AES256';
                    $key = getenv('AES256_KEY');
                    if (in_array($cipher, openssl_get_cipher_methods(true))) {
                        echo openssl_decrypt($cipher_email, $cipher, $key);
                    }
                }   
                ?>">
                <div class="invalid-feedback">
                    올바른 이메일 주소를 입력해주세요.
                </div>
        </div>
        <div class="form-group">
            <label for="input-password">비밀번호</label>
            <input type="password" id="input-password" class="form-control" name="password" required>
            <div class="invalid-feedback">
                비밀번호가 틀렸습니다.
            </div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember"> 아이디 저장
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-3">로그인</button>
    </form>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item px-0" href="signup.php">처음 방문하셨나요? 가입하기</a>
    <a class="dropdown-item px-0" href="identify.php">비밀번호를 잊으셨나요?</a>
</div>
<script type="text/javascript" src="public/js/validation.js"></script>
<script type="text/javascript" src="public/js/sha512.js"></script>
<script type="text/javascript">
    $("#form-signin").submit(function () {
        var password = $("#input-password").val();
        if (password && password.length && $("#input-email").valid()) {
            var encrypted = CryptoJS.SHA512(password);
            $("#input-password").val(encrypted);
            return true;
        }
    });
</script>
</body>
</html>