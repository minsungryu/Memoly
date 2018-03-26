<?php
require_once dirname(__DIR__).'/lib/const.php';
$current_url = explode('?', $_SERVER['REQUEST_URI'])[0];
?>
<header>
    <nav class="navbar navbar-light justify-content-between">
        <a class="navbar-brand" href="<?= $current_url ?>">
            <img src="<?php echo IMG.'memoly.png' ?>" width="162" height="42" class="d-inline-block align-top" alt="Memoly">
        </a>
        <ul class="nav">
            <li class="nav-item mx-2 d-inline-block align-middle">
                <a href="<?= 'edit.php?user-no='.$_SESSION['user_no'] ?>"><?php
                    echo $_SESSION['user_nickname'];
                    if ($_SESSION['is_admin']) {
                        echo '[관리자]';
                    }
                ?></a>님 환영합니다.
            </li>
            <?php if ($_SESSION['is_admin'] && $current_url === '/admin.php'): ?>
                <a href="memo.php"><button class="btn btn-secondary" type="submit">메모 관리</button></a>
            <?php elseif ($_SESSION['is_admin'] && $current_url === '/memo.php'): ?>
                <a href="admin.php"><button class="btn btn-secondary" type="submit">회원 관리</button></a>
            <?php endif; ?>
            <li class="nav-item mx-2 d-inline-block align-middle">
                <a href="signout.php"><button class="btn btn-primary" type="submit">로그아웃</button></a>
            </li>
        </ul>
    </nav>
</header>