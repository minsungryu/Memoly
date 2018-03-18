<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memoly</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link href="public/css/common.css" rel="stylesheet">
    <link href="public/css/edit.css" rel="stylesheet">
</head>
<body class="text-center">
<form class="form-edit">
    <h1>회원정보 수정</h1>
    <label class="form-control text-left">minsungryu@godo.co.kr</label>
    <input type="password" id="input-current-password" class="form-control" placeholder="Current password" required autofocus>
    <input type="password" id="input-new-password" class="form-control" placeholder="New password" required>
    <input type="password" id="input-new-password-check" class="form-control" placeholder="New password again" required>
    <input type="text" id="input-password-check" class="form-control" placeholder="Nickname" required>
    <button class="btn btn-lg btn-danger btn-block" type="submit">가입하기</button>
    <button class="btn btn-lg btn-dark btn-block" type="submit">탈퇴하기</button>
</form>
</body>
</html>