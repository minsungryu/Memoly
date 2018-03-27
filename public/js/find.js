/**
 * 찾기 버튼을 누르면 폼을 검증한다.
 */
$('#find-button').click(function(e) {
    e.preventDefault();
    
    var email = $("#form-find").find("#email");
    var nickname = $("#form-find").find('#nickname').val();
    
    if (!email.val() || !email.valid()) {
      return alert('유효한 이메일을 입력해주세요.');
    }

    if (!nickname || nickname.length < 2 || 16 < nickname.length) {
        return alert('닉네임은 2자 이상 16자 이하로 입력해주세요.');
    }

    $("#form-find").submit();
})

/**
 * 본인인증 폼을 전송한다.
 */
$("#form-find").submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: './find.php',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (result === '1') {
                alert('가입하신 메일로 임시 비밀번호를 발송하였습니다. 다시 로그인 후 비밀번호를 변경해주세요.')
                window.location.href = '/signin.php';
            } else {
                alert('본인인증에 실패했습니다.');
            }
        },
        error: function (err) {
            alert(err.responseJSON);
        }
    });
});

/**
 * 본인인증 폼 제약 조건 검증
 */
$('#form-find').validate({
    // debug: true,
    rules: {
        email: {
            required: true,
            email: true
        },
        nickname: {
            required: true,
            rangelength: [2, 16]
        }
    },
    messages: {
        email: {
            required: '이메일을 입력해주세요.',
            email: '올바른 이메일 주소를 입력해주세요.'
        },
        nickname: {
            required: '닉네임을 입력해주세요.',
            rangelength: '닉네임은 {0}글자 이상 {1}자 이하로 설정해주세요.'
        }
    },
    errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');

        if (element.prop('type') === 'checkbox') {
        error.insertAfter(element.next());
        } else {
        error.insertAfter(element);
        }
    },
    highlight: function(element, errorClass, validClass) {
        $(element).addClass(errorClass).removeClass(validClass);
    },
    unhighlight: function(element, errorClass, validClass) {
        $(element).addClass(validClass).removeClass(errorClass);
    }
});