var signup_form = $('#form-signup');
var hidden_form = $('#form-hidden');

/**
 * 이용약관 모달에서 동의를 누르면 체크박스를 표시한다.
 */
$('#terms-agree').click(function() {
  $('#terms').prop('checked', true);
});


/**
 * 가입하기 버튼을 클릭하면 검증 후 숨겨진 폼에 데이터를 옮긴다.
 */
$("#signup-button").click(function (e) {
  e.preventDefault();

  var email = signup_form.find("#email");
  var password = signup_form.find('#password').val();
  var password_confirm = signup_form.find('#password-confirm').val();
  var nickname = signup_form.find('#nickname').val();
  var terms = signup_form.find('#terms').val();

  if (!email.val() || !email.valid()) {
    return alert('유효한 이메일을 입력해주세요.');
  }

  if (!password || !password.length) {
    return alert('비밀번호를 입력해주세요.');
  }

  if (password !== password_confirm) {
    return alert('비밀번호가 같지 않습니다.');
  }
  
  if (!nickname || nickname.length < 2 || 16 < nickname.length) {
    return alert('닉네임은 2자 이상 16자 이하로 입력해주세요.');
  }

  if (!terms || terms !== 'on') {
    return alert('약관에 동의해주세요.');
  }
  
  hidden_form.find('#hidden-email').val(email.val());
  hidden_form.find('#hidden-password').val(CryptoJS.SHA512(password).toString());
  hidden_form.find('#hidden-password-confirm').val(CryptoJS.SHA512(password_confirm).toString());
  hidden_form.find('#hidden-nickname').val(nickname);
  hidden_form.find('#hidden-terms').val(terms);
  hidden_form.submit();
});

/**
 * ajax를 통해 폼을 전송한다.
 */
$('#form-hidden').submit(function (e) {
  e.preventDefault();

  $.ajax({
    url: '/signup.php',
    type: 'post',
    data: $(this).serialize(),
    success: function (result) {
      if (result) {
        alert('회원가입을 축하합니다! 로그인 페이지로 이동합니다.');
        window.location.href = '/signin.php';
      } else {
        alert('로그인에 실패했습니다.');
      }
    },
    error: function (err) {
      alert(err.responseJSON);
    }
  });
});

/**
 * 회원가입 폼을 검증한다.
 */
$('#form-signup').validate({
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