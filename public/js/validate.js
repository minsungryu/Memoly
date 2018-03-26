$.validator.addMethod("alphanumeric", function(value, element) {
return this.optional(element) || /^[a-zA-Z0-9]{10,16}$/.test(value);
}, '비밀번호는 10자 이상 16자 이하 영문, 숫자 조합입니다.');
