$.validator.addMethod("notEqualTo", function(value, element, param) {
    return this.optional(element) || value !== param;
}, '현재 비밀번호와 같을 수 없습니다.');

    
$("#edit").click(function(event) {
    var $this = $('#form-edit');
    $this.addClass('edit').removeClass('leave');

    var password = $this.find('#password') && $this.find('#password').val();    
    var new_password = $this.find('#new-password') && $this.find('#new-password').val();
    var new_password_confirm = $this.find('#new-password-confirm') && $this.find('#new-password-confirm').val();

    if (!password.length || !new_password.length || !new_password_confirm.length ||
        new_password !== new_password_confirm || !$this.find("#email").valid()) {
        return false;
    }

    $this.find('#hidden-new-password').val(CryptoJS.SHA512(new_password).toString());
    $this.find('#hidden-new-password-confirm').val(CryptoJS.SHA512(new_password_confirm).toString());
    $this.find('#hidden-password').val(CryptoJS.SHA512(password).toString());
    $this.find('#password').removeAttr('value');
    $this.find('#new-password').removeAttr('value');
    $this.find('#new-password-confirm').removeAttr('value');

    var nickname = $this.find('#nickname').val();
    var nickname_len = nickname && nickname.length;

    if (nickname_len < 2 || 16 < nickname_len) {
        return false;
    }

    return true;
});

$("#leave").click(function(event) {
    var $this = $('#form-edit');
    $this.addClass('leave').removeClass('edit');

    var password = $this.find('#password') && $this.find('#password').val();    

    if (!password.length || !$this.find("#email").valid()) {
        return false;
    }

    $this.find('#hidden-password').val(CryptoJS.SHA512(password).toString());
    $this.find('#password').removeAttr('value');

    var nickname = $this.find('#nickname').val();
    var nickname_len = nickname && nickname.length;

    if (nickname_len < 2 || 16 < nickname_len) {
        return false;
    }
  
    return true;
});

$('#form-edit.edit').validate({
    // debug: true,
    rules: {
        password: {
            required: true,
            // rangelength: [10, 16]
            alphanumeric: true
        },
        'new-password': {
            required: true,
            // rangelength: [10, 16]
            alphanumeric: true,
            notEqualTo: '$password'
        },
        'new-password-confirm': {
            required: true,
            // rangelength: [10, 16],
            alphanumeric: true,
            equalTo: '#new-password',
            notEqualTo: '$password'
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
        'new-password': {
            required: '새 비밀번호를 입력해주세요.',
            // rangelength: '비밀번호는 {0}자 이상 {1}자 이하 영문, 숫자 조합입니다.',
            alphanumeric: '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.',
            notEqualTo: '현재 비밀번호와 같을 수 없습니다.'
        },
        'new-password-confirm': {
            required: '새 비밀번호를 다시 입력해주세요.',
            // rangelength: '비밀번호는 {0}자 이상 {1}자 이하 영문, 숫자 조합입니다.',
            alphanumeric: '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.',
            equalTo: '비밀번호가 일치하지 않습니다.',
            notEqualTo: '현재 비밀번호와 같을 수 없습니다.'
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

$('#form-edit.leave').validate({
    // debug: true,
    rules: {
        password: {
            required: true,
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