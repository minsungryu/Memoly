<div class="find-box">
    <h3 class="mb-4">비밀번호 찾기</h3>
    <form action="find.php" method="post" id="form-find" novalidate>
        <div class="form-group">
            <label for="email">이메일 주소</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="your@example.com" required autofocus>
        </div>
        <div class="form-group">
            <label for="nickname">닉네임</label>
            <input type="text" class="form-control" id="nickname" name="nickname" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">사용자 확인</button>
    </form>
</div>