<div class="center-box">
<div class="form-box">
    <img src="public/img/memoly.png" class="mb-5">
    <form action="signin.php" method="post" id="form-signin" novalidate>
        <div class="form-group">
            <label for="email">이메일 주소</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="your@example.com" required value="<?php $this->rememberEmail() ?>">
                <div class="invalid-feedback">
                    올바른 이메일 주소를 입력해주세요.
                </div>
        </div>
        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" class="form-control" name="password" required>
            <input type="hidden" id="hidden-password" name="hidden-password">
            <div class="invalid-feedback">
                비밀번호가 틀렸습니다.
            </div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" <?php echo $_COOKIE["memoly_remember"]; ?>>
                <label for="remember">아이디 저장</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-3">로그인</button>
    </form>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item px-0" href="signup.php">처음 방문하셨나요? 가입하기</a>
    <a class="dropdown-item px-0" href="find.php">비밀번호를 잊으셨나요?</a>
</div>
</div>