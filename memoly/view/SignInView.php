<div class="center-box">
<div class="form-box">
    <img src="public/img/memoly.png" class="mb-5">
    <form id="form-signin" novalidate>
        <div class="form-group">
            <label for="email">이메일 주소</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="your@example.com" required value="<?= $this->loadEmail() ?>">
        </div>
        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" class="form-control" name="password" required>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" <?= $_COOKIE["memoly_remember"] ?>>
                <label for="remember">아이디 저장</label>
            </div>
        </div>
        <button type="button" id="signin-button" class="btn btn-primary mb-3">로그인</button>
    </form>
    <form id="form-hidden" novalidate>
        <input type="hidden" id="hidden-email" name="hidden-email">
        <input type="hidden" id="hidden-password" name="hidden-password">
        <input type="hidden" id="hidden-remember" name="hidden-remember">
    </form>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item px-0" href="signup.php">처음 방문하셨나요? 가입하기</a>
    <a class="dropdown-item px-0" href="find.php">비밀번호를 잊으셨나요?</a>
</div>
</div>