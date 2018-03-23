<?php
require_once dirname(__DIR__).'/lib/const.php';
?>
<header>
    <nav class="navbar navbar-light justify-content-between">
        <a class="navbar-brand" href="admin.php">
            <img src="<?php echo IMG.'memoly.png' ?>" width="162" height="42" class="d-inline-block align-top" alt="Memoly">
        </a>
        <ul class="nav">
            <li class="nav-item mx-2 d-inline-block align-middle">
                <a href="edit.php"><?php
                    echo $_SESSION['user_nickname'];
                    if ($_SESSION['is_admin']) {
                        echo '[관리자]';
                    }
                ?></a>님 환영합니다.
            </li>
            <li class="nav-item mx-2 d-inline-block align-middle">
                <a href="signout.php"><button class="btn btn-primary" type="submit">로그아웃</button></a>
            </li>
        </ul>
    </nav>
</header>