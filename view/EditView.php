<div class="center-box">
<div class="form-box">
<form id="form-edit" class="edit leave" novalidate>
    <h3 class="mb-4">회원정보 수정</h3>
    <div class="form-group">
        <label for="email">이메일 주소</label>
        <input type="email" class="form-control" id="email" name="email" <?php if (!$_SESSION['is_admin']) echo 'readonly'; ?> required value="<?= $this->user['user_email'] ?>">
    </div>
    <div class="form-group">
        <label for="password">현재 비밀번호</label>
        <input type="password" class="form-control" id="password" name="password" required autofocus>
    </div>
    <div class="form-group">
        <label for="password">새 비밀번호</label>
        <input type="password" class="form-control" id="new-password" name="new-password">
    </div>
    <div class="form-group">
        <label for="password-confirm">새 비밀번호 확인</label>
        <input type="password" class="form-control" id="new-password-confirm" name="new-password-confirm">
    </div>
    <div class="form-group">
        <label for="nickname">닉네임</label>
        <input type="text" class="form-control" id="nickname" name="nickname" required value="<?= $this->user['user_nickname'] ?>">
    </div>
    <div class="mt-4">
        <button type="button" class="btn btn-primary" id="edit">수정하기</button>
        <?php if ($_SESSION['is_admin'] === '0'): ?>
        <button type="button" class="btn btn-dark" id="leave">탈퇴하기</button>
        <?php endif; ?>
    </div>
</form>
<form id="form-hidden" novalidate>
    <input type="hidden" id="hidden-email" name="hidden-email">
    <input type="hidden" id="hidden-password" name="hidden-password">
    <input type="hidden" id="hidden-new-password" name="hidden-new-password">
    <input type="hidden" id="hidden-new-password-confirm" name="hidden-new-password-confirm">
    <input type="hidden" id="hidden-nickname" name="hidden-nickname">
</form>
</div>
</div>