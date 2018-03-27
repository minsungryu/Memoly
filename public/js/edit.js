var edit_form = $('#form-edit');
var hidden_form = $('#form-hidden');

// 현재 비밀번호와 새로운 비밀번호는 같을 수 없다는 제약조건을 정의
$.validator.addMethod("notEqualTo", function (value, element, param) {
    return this.optional(element) || value !== $(param).val();
}, '현재 비밀번호와 같을 수 없습니다.');

var common_validate_rule = {
    // debug: true,
    rules: {
        password: {
            // required: true,
            // rangelength: [10, 16]
            alphanumeric: true
        },
        nickname: {
            required: true,
            rangelength: [2, 16]
        }
    },
    messages: {
        password: {
            required: '비밀번호를 입력해주세요.',
            // rangelength: '비밀번호는 {0}자 이상 {1}자 이하 영문, 숫자 조합입니다.'.
            alphanumeric: '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.'
        },
        nickname: {
            required: '닉네임을 입력해주세요.',
            rangelength: '닉네임은 {0}글자 이상 {1}자 이하로 설정해주세요.'
        }
    },
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');

        if (element.prop('type') === 'checkbox') {
            error.insertAfter(element.next());
        } else {
            error.insertAfter(element);
        }
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass(errorClass).removeClass(validClass);
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).addClass(validClass).removeClass(errorClass);
    }
}

var new_password_rule = $.extend({}, common_validate_rule);
new_password_rule['rules']['new-password'] = {
    // required: true,
    // rangelength: [10, 16]
    alphanumeric: true,
    notEqualTo: '#password'
};
new_password_rule['rules']['new-password-confirm'] = {
    // required: true,
    // rangelength: [10, 16],
    alphanumeric: true,
    equalTo: '#new-password',
    notEqualTo: '#password'
};
new_password_rule['messages']['new-password'] = {
    // required: '새 비밀번호를 입력해주세요.',
    // rangelength: '비밀번호는 {0}자 이상 {1}자 이하 영문, 숫자 조합입니다.',
    alphanumeric: '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.',
    notEqualTo: '현재 비밀번호와 같을 수 없습니다.'
};
new_password_rule['messages']['new-password-confirm'] = {
    // required: '새 비밀번호를 다시 입력해주세요.',
    // rangelength: '비밀번호는 {0}자 이상 {1}자 이하 영문, 숫자 조합입니다.',
    alphanumeric: '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.',
    equalTo: '비밀번호가 일치하지 않습니다.',
    notEqualTo: '현재 비밀번호와 같을 수 없습니다.'
};

// 수정시 제약조건 검증
$('#form-edit.edit').validate(new_password_rule);

// 삭제시 제약조건 검증
$('#form-edit.leave').validate(common_validate_rule);

$('#form-hidden').submit(function (event) {
    event.preventDefault();

    $.ajax({
        url: './edit.php',
        type: 'post',
        data: $(this).serialize(),
        success: function (result) {
            if (action === 'put' && result === '1') {
                alert('성공적으로 수정되었습니다!');
                window.location.reload(true);
            } else if (action === 'delete' && result === '1') {
                alert('탈퇴에 성공했습니다! 이용해주셔서 감사합니다.');
                window.location.href = '/signout.php';
            } else {
                alert('요청에 실패했습니다.');
            }
        },
        error: function (err) {
            alert(err.responseJSON);
        }
    });
});

// 수정 버튼 클릭시
$("#edit").click(function (e) {
    e.preventDefault();

    // validation을 구분하기 위한 작업
    hidden_form.addClass('edit').removeClass('leave');
    edit_form.addClass('edit').removeClass('leave');
    edit_form.find('#password').prop('required', true);
    // action = 'PUT';
    $('#action').val('PUT');

    // 실제로 전송될 폼은 put 메소드로 설정
    // hidden_form.prop('method', 'put');

    // 폼 데이터 검증
    var email = edit_form.find('#email');
    var password = edit_form.find('#password').val();
    var nickname = edit_form.find('#nickname').val();

    if (!email.val() || !email.valid()) {
        return alert('유효한 이메일을 입력해주세요.');
    }

    // 관리자로 인해 제거
    // if (!password || !password.length) {
    //     return alert('비밀번호를 입력해주세요.');
    // }

    if (!nickname || nickname.length < 2 || 16 < nickname.length) {
        return alert('닉네임은 2자 이상 16자 이하로 입력해주세요.');
    }

    hidden_form.find('#hidden-email').val(email.val());
    hidden_form.find('#hidden-nickname').val(nickname);
    if (password && password.length) {
        hidden_form.find('#hidden-password').val(CryptoJS.SHA512(password).toString());
    }

    var new_password = edit_form.find('#new-password').val();
    var new_password_confirm = edit_form.find('#new-password-confirm').val();

    // 새로운 비밀번호를 입력하지 않았다면
    if (!new_password && !new_password_confirm) {
        hidden_form.submit();
    } else {
        // 새로운 비밀번호를 입력했음에도 일치하지 않을 경우
        if (new_password.length && new_password_confirm.length && new_password !== new_password_confirm) {
            return alert('비밀번호가 일치하지 않습니다.');
        }

        // 전송할 데이터에 SHA512 해시 적용
        hidden_form.find('#hidden-new-password').val(CryptoJS.SHA512(new_password).toString());
        hidden_form.find('#hidden-new-password-confirm').val(CryptoJS.SHA512(new_password_confirm).toString());
        hidden_form.submit();
    }
});

// 탈퇴 버튼 클릭시
$("#leave").click(function (event) {
    event.preventDefault();

    // validation을 구분하기 위한 작업
    hidden_form.addClass('leave').removeClass('edit');
    edit_form.addClass('leave').removeClass('edit');
    edit_form.find('#password').prop('required', false);
    // action = 'delete';
    $('#action').val('DELETE');

    // 실제로 전송될 폼은 delete 메소드로 설정
    // hidden_form.prop('method', 'delete');

    // 폼 데이터 검증
    var email = edit_form.find('#email');
    var password = edit_form.find('#password').val();
    var nickname = edit_form.find('#nickname').val();

    if (!email.val() || !email.valid()) {
        return alert('유효한 이메일을 입력해주세요.');
    }

    if (!password || !password.length) {
        return alert('비밀번호를 입력해주세요.');
    }

    if (!nickname || nickname.length < 2 || 16 < nickname.length) {
        return alert('닉네임은 2자 이상 16자 이하로 입력해주세요.');
    }

    /**
     * 전송할 폼에 값 입력
     */
    hidden_form.find('#hidden-email').val(email.val());
    hidden_form.find('#hidden-nickname').val(nickname);
    if (password && password.length) {
        hidden_form.find('#hidden-password').val(CryptoJS.SHA512(password).toString());
    }
    hidden_form.submit();
});
