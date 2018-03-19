$('#terms-agree').click(function() {
  $('#terms').attr('checked', true);
});

$("#form-signup").submit(function(event) {
  var $this = $(this);
  var password = $this.find('#password') && $this.find('#password').val();
  var password_confirm = $this.find('#password-confirm') && $this.find('#password-confirm').val();

  if (!password.length || !password_confirm.length || password !== password_confirm || !$this.find("#email").valid()) {
    return false;
  }

  $this.find('#hidden-password').val(CryptoJS.SHA512(password));
  $this.find('#hidden-password-confirm').val(CryptoJS.SHA512(password_confirm));
  $this.find('#password').removeAttr('value');
  $this.find('#password-confirm').removeAttr('value');

  return true;
});

$('#form-signup').validate({
  // debug: true,
  rules: {
    email: {
      required: true,
      email: true
    },
    password: {
      required: true,
      // rangelength: [10, 16]
      alphanumeric: true
    },
    'password-confirm': {
      required: true,
      // rangelength: [10, 16],
      alphanumeric: true,
      equalTo: '#password'
    },
    nickname: {
      required: true,
      rangelength: [2, 16]
    },
    terms: 'required'
  },
  messages: {
    email: {
      required: '이메일을 입력해주세요.',
      email: '올바른 이메일 주소를 입력해주세요.'
    },
    password: {
      required: '비밀번호를 입력해주세요.',
      // rangelength: '비밀번호는 {0}자 이상 {1}자 이하 영문, 숫자 조합입니다.'.
      alphanumeric: '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.'
    },
    'password-confirm': {
      required: '비밀번호를 다시 입력해주세요.',
      // rangelength: '비밀번호는 {0}자 이상 {1}자 이하 영문, 숫자 조합입니다.',
      alphanumeric: '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.',
      equalTo: '비밀번호가 일치하지 않습니다.'
    },
    nickname: {
      required: '닉네임을 입력해주세요.',
      rangelength: '닉네임은 {0}글자 이상 {1}자 이하로 설정해주세요.'
    },
    terms: '반드시 동의해야 회원가입을 진행할 수 있습니다.'
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