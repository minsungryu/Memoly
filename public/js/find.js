$("#form-find").submit(function(event) {
    var $this = $(this);

    if (!$this.find("#email").valid()) {
        return false;
    }

    var nickname = $this.find('#nickname').val();
    var nickname_len = nickname && nickname.length;
    if (nickname_len < 2 || 16 < nickname_len) {
        return false;
    }

    return true;
});

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