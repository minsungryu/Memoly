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
    <link href="public/css/user.css" rel="stylesheet">
</head>
<body>
<?php
    include_once "./header.php";
?>
<div class="container">
    <h1 class="text-center m-5">회원 목록</h1>
    <ul class="nav justify-content-between">
        <button class="btn btn-lg btn-primary">선택삭제</button>
        <form class="form-inline my-2 my-lg-0">
            <select class="form-control">
                <option>Default select</option>
            </select>
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">Search</button>
        </form>
    </ul>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
        <th scope="col"></th>
        <th scope="col" class="text-center"><input type="checkbox"></th>
        <th scope="col" class="text-center">이메일</th>
        <th scope="col" class="text-center">닉네임</th>
        <th scope="col" class="text-center">가입일자</th>
        <th scope="col" class="text-center">마지막로그인</th>
        <th scope="col" class="text-center">작성한 메모 수</th>
        </tr>
    </thead>
    <tbody>
        <tr>
        <th scope="row" class="text-center">1</th>
        <td class="text-center"><input type="checkbox"></td>
        <td class="text-center">minsu*****@godo.co.kr</td>
        <td class="text-center">민성</td>
        <td class="text-center">2018.03.05</td>
        <td class="text-center">2018.03.19.</td>
        <td class="text-center">99</td>
        </tr>
        <tr>
        <th scope="row" class="text-center">1</th>
        <td class="text-center"><input type="checkbox"></td>
        <td class="text-center">minsu*****@godo.co.kr</td>
        <td class="text-center">민성</td>
        <td class="text-center">2018.03.05</td>
        <td class="text-center">2018.03.19.</td>
        <td class="text-center">99</td>
        </tr>
        <tr>
        <th scope="row" class="text-center">1</th>
        <td class="text-center"><input type="checkbox"></td>
        <td class="text-center">minsu*****@godo.co.kr</td>
        <td class="text-center">민성</td>
        <td class="text-center">2018.03.05</td>
        <td class="text-center">2018.03.19.</td>
        <td class="text-center">99</td>
        </tr>
    </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled">
            <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
            <a class="page-link" href="#">Next</a>
            </li>
        </ul>
    </nav>
</div>
<?php
    include_once "./footer.html";
?>
</body>
</html>