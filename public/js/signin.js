var signin_form = $('#form-signin');
var hidden_form = $('#form-hidden');

/**
 * 로그인 버튼을 클릭하면 검증 후 숨겨진 폼에 데이터를 옮긴다.
 */
$("#signin-button").click(function (e) {
  e.preventDefault();

  var email = signin_form.find("#email");
  var password = signin_form.find('#password').val();
  
  if (!email || !email.valid()) {
    return alert('유효한 이메일을 입력해주세요.');
  }

  if (!password || !password.length) {
      return alert('비밀번호를 입력해주세요.');
  }
  
  var remember = signin_form.find('#remember').val();
  hidden_form.find('#hidden-email').val(email.val());
  hidden_form.find('#hidden-password').val(CryptoJS.SHA512(password).toString());
  hidden_form.find('#hidden-remember').val(remember);
  hidden_form.submit();
});

/**
 * ajax를 통해 폼을 전송한다.
 */
$('#form-hidden').submit(function (e) {
  e.preventDefault();

  $.ajax({
    url: '/signin.php',
    type: 'post',
    data: $(this).serialize(),
    success: function (result) {
      if (result) {
        window.location.href = '/memo.php';
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
 * 로그인 폼을 검증한다.
 */
$('#form-signin').validate({
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