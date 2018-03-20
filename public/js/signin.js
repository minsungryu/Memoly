$("#form-signin").submit(function () {
  var $this = $(this);
  var password = $this.find('#password') && $this.find('#password').val();

  if (!password.length || !$this.find("#email").valid()) {
    return false;
  }

  $this.find('#hidden-password').val(CryptoJS.SHA512(password).toString());
  $this.find('#password').removeAttr('value');
  return true;
});

$('#form-signin').validate({
  // debug: true,
  rules: {
    email: {
      required: true,
      email: true
    },
    password: {
      required: true,
      // rangelength: [10, 16],
      alphanumeric: [10, 16]
    }
  },
  messages: {
    email: {
      required: '이메일을 입력해주세요.',
      email: '올바른 이메일 주소를 입력해주세요.'
    },
    password: {
      required: '비밀번호를 입력해주세요.',
      // rangelength: '비밀번호는 {0}자 이상 {1}자 이하 영문, 숫자 조합입니다.',
      alphanumeric: '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.'
    }
  },
  errorPlacement: function(error, element) {
    error.addClass('invalid-feedback');
    error.insertAfter(element);
  },
  highlight: function(element, errorClass, validClass) {
    $(element).addClass(errorClass).removeClass(validClass);
  },
  unhighlight: function(element, errorClass, validClass) {
    $(element).addClass(validClass).removeClass(errorClass);
  }
});