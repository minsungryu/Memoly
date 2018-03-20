<?php
require_once dirname(__DIR__).'/lib/const.php';
?>
<header>
    <nav class="navbar navbar-light bg-light justify-content-between">
        <a class="navbar-brand" href="#">
            <img src="<?php echo PUBLIC_ASSETS.'memoly.png' ?>" width="30" height="30" class="d-inline-block align-top" alt="Memoly">
        </a>
        <ul class="nav">
            <li class="nav-item mx-2 d-inline-block align-middle">
                <a href="#">민성</a>님 환영합니다.
            </li>
            <li class="nav-item mx-2 d-inline-block align-middle">
                <a href="signout.php"><button class="btn btn-danger" type="submit">로그아웃</button></a>
            </li>
        </ul>
    </nav>
</header>