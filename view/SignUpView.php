<div class="center-box">
<div class="form-box">
<form id="form-signup" novalidate>
    <h3 class="mb-4">회원가입</h3>
    <div class="form-group">
        <label for="email">이메일 주소</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="your@example.com" required>
    </div>
    <div class="form-group">
        <label for="password">비밀번호</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="password-confirm">비밀번호 확인</label>
        <input type="password" class="form-control" id="password-confirm" name="password-confirm" required>
    </div>
    <div class="form-group">
        <label for="nickname">닉네임</label>
        <input type="text" class="form-control" id="nickname" name="nickname" required>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
            <label class="form-check-label" for="terms">
            <a class="border-bottom border-dark" data-toggle="modal" data-target="#terms-and-services-modal-center">이용약관</a>에 동의합니다.
            </label>
        </div>
    </div>
    <button type="button" id="signup-button" class="btn btn-primary">가입하기</button>
</form>
<form id="form-hidden" novalidata>
    <input type="hidden" id="hidden-email" name="hidden-email">
    <input type="hidden" id="hidden-password" name="hidden-password">
    <input type="hidden" id="hidden-password-confirm" name="hidden-password-confirm">
    <input type="hidden" id="hidden-nickname" name="hidden-nickname">
    <input type="hidden" id="hidden-terms" name="hidden-terms">
</form>
</div>
<!-- Modal -->
<div class="modal fade" id="terms-and-services-modal-center">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terms-and-services-modal-long-title">회원가입 이용약관</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="닫기">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <textarea readonly>
제1조 (목적) 본 약관은 엔에이치엔고도 주식회사(이하 '회사')가 Memoly 사이트(이하 ‘사이트’)를 통하여 인터넷상에서 제공하는 서비스(이하 ‘서비스’)를 이용하는 고객 (이하 '고객')간의 권리와 의무 및 책임 등
기타 제반사항을 규정함을 목적으로 합니다.

제2조 (용어의 정의) 이 약관에서 사용하는 용어의 정의는 다음과 같습니다.
가. '회원'이라 함은 이 약관에 동의하고 회원가입을 통하여 이용자ID(고유번호)와 비밀번호를 발급받은 자로서, 회사가 제공하는 서비스를 이용할 수 있는 이용자를 말합니다.
나. '이용자ID'라 함은 회원의 식별 및 서비스 이용을 위하여 회원의 신청에 따라 회사가 회원 별로 부여하는 고유한 문자와 숫자의 조합을 말합니다.
다. '비밀번호'라 함은 이용자ID로 식별되는 회원의 본인 여부를 검증하기 위하여 회원이 설정하여 회사에 등록한 고유의 문자와 숫자의 조합을 말합니다.
라. ‘로그인’이라 함은 이용자ID와 비밀번호를 통하여 서비스 신청 및 사용 중 서비스의 세부정보를 확인할 수 있는 행위를 말합니다.
마. '탈퇴'라 함은 회원이 서비스 이용을 해지하는 것을 말합니다.
이 약관에서 사용하는 용어 중 제1항에서 정하지 아니한 것은 관계 법령 및 서비스 별 안내에서 정하는 바에 따르며, 그 외에는 일반 관례에 따릅니다.

이하 생략
                </textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">취소</button>
                <button type="button" id="terms-agree" class="btn btn-primary" data-dismiss="modal">동의</button>
            </div>
        </div>
    </div>
</div>
</div>