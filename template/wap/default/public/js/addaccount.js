function settime(obj) { //发送验证码倒计时
    if(countdown == 0) { 
        obj.attr('disabled',false); 
        //obj.removeattr("disabled"); 
        obj.val("发送验证码");
        countdown = 60; 
        return;
    }else { 
        obj.attr('disabled',true);
        obj.val("重新发送(" + countdown + ")");
        countdown--; 
    } 
	setTimeout(function() { 
    	settime(obj) 
	},1000)
}
$(function(){
	$('#userCard').val('');
	$('#bank').val('');
	$('#userName').val('');
	$('#phone').val('');
	$('#branch_bank_name').val('');
	// 身份证
	function idCard(){    //身份证
		var elValue = $('#userCard').val(),
			isValid = true;
		if( !(elValue.length == 18 || elValue.length == 15) ){
			$('.msg').html('身份证错误，请重新输入!').show()
			isValid = false;
		}
		if(/^(\d{18}|\d{17}(\d|X|x))$/.test(elValue)){	//身份证号为18位校检年月日
			var date = new Date(),	//日期对象
			oYear = date.getFullYear(),	//当前年份
			idYear = elValue.substr(6,4);	//获取用户输入的身份证生日年份
			if(oYear - idYear > 120 || oYear - idYear < 3){
				$('.msg').html('身份证错误，请重新输入!').show()
				isValid = false;
			};
			var month = elValue.substr(10,2);	//获取用户输入的身份证生日月份
			if(month<1 || month>12){
				$('.msg').html('身份证错误，请重新输入!').show()
				isValid = false;
			};
			var day = elValue.substr(12,2);	//获取用户输入的身份证出生日期
			if(day < 1 || day > 31){
				$('.msg').html('身份证错误，请重新输入!').show()
				isValid = false;
			}
			if(month == 4 || month == 6 || month == 9 || month == 11){
				if(day>30){
					$('.msg').html('身份证错误，请重新输入!').show()
					isValid = false;
				}
			}else if( month == 2){
				if(idYear%400 == 0 || idYear%4 == 0 && idYear%100 != 0){
					if(day>29){
						$('.msg').html('身份证错误，请重新输入!').show()
						isValid = false;
					}
				}else{
					if(day>28){
						$('.msg').html('身份证错误，请重新输入!').show()
						isValid = false;
					}
				}
			}
			var coefficientArr = [7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2],	//身份证前17位对应系数
			remainderArr = [1,0,"X",9,8,7,6,5,4,3,2],	//余数对应的数字
			val = elValue.substr(17,1),	//获取身份证最后一位数
			sum = 0;	//用于求身份证前17位乘以系数之和
			for(var i=0, len = coefficientArr.length; i<len; i++){//身份证最后一位校检码求和	前17位乘以系数取模11
				sum += elValue.charAt(i)*coefficientArr[i];
			};
			if(remainderArr[sum%11] != val.toUpperCase()){//验证校检码
				$('.msg').html('身份证错误，请重新输入!').show()
				isValid = false;
			}
		}else{	//身份证号为15位校检月份日期
			var idYear = Number(elValue.substr(6,2)) + 1900,	//用户输入的身份证年份
			month = elValue.substr(8,2);	//用户输入的身份证月份
			if(month<1 || month>12){
				$('.msg').html('身份证错误，请重新输入!').show()
				isValid = false;
			};
			var day = elValue.substr(10,2);	//用户输入的身份证日期
			if(day < 1 || day > 31){
				$('.msg').html('身份证错误，请重新输入!').show()
				isValid = false;
			}
			if(month == 4 || month == 6 || month == 9 || month == 11){
				if(day>30){
					$('.msg').html('身份证错误，请重新输入!').show()
					isValid = false;
				}
			}else if( month == 2){
				if(idYear%400 == 0 || idYear%4 == 0 && idYear%100 != 0){
					if(day>29){
						$('.msg').html('身份证错误，请重新输入!').show()
						isValid = false;
					}
				}else{
					if(day>28){
						$('.msg').html('身份证错误，请重新输入!').show()
						isValid = false;
					}
				}
			}
		}
		return isValid; 
	};
	function bankcard(){
		var bank = $('#bank').val();
	    var value = bank.replace(/\s/g, ''),
	        isValid = true,
	        rFormat = /^[\d]{12,19}$/;
	    if ( value == '' ) {
	       $('.msg').html('请填写您的银行卡号!').show();
	       isValid = false;
	    }else if ( !rFormat.test(value) ) {
	       $('.msg').html('请填写有效的银行卡号!').show();
	       isValid = false;
	    }else {
	        var arr = value.split('').reverse(),
	            i = arr.length,
	            temp,
	            sum = 0;
	        while ( i-- ) {
	            if ( i%2 === 0 ) {
	                sum += +arr[i];
	            } else {
	                temp = +arr[i] * 2;
	                sum += temp % 10;
	                if ( temp > 9 ) sum += 1;
	            }
	        }
	        if(sum % 10 !== 0 ) {
	            $('.msg').html('请填写有效的银行卡号!').show();
	            isValid = false;
	        }
	    }
	    return isValid; 
	};
	function userName(){
		var userName = $('#userName').val();
		var isValid = true;
		if(userName == ''){
			$('.msg').html('请填写用户名!').show();
			$("#userName").focus();
			isValid = false;
		}
		return isValid; 
	}
	function phone(){
		var phone = $('#phone').val();
		var isValid = true;
		var mobile = /^1[3-9]\d{9}$/;
		if(phone == ''){
			$('.msg').html('请填写手机号码!').show();
			$("#phone").focus();
			isValid = false;
		}else if(!mobile.test(phone)){
			$('.msg').html('请填写有效的手机号!').show();
			$("#phone").focus();
			isValid = false;
		}
		return isValid; 
	}
	function code(){
		var code = $('#code').val();
		var isValid = true;
		if(code == ''){
			$('.msg').html('请填写验证码!').show();
			$("#code").focus();
			isValid = false;
		}
		return isValid; 
	}
	function branch_bank_name(){
		var code = $('#branch_bank_name').val();
		var shop_id = $('#shop_id').val()
		var isValid = true;
		if(code == ''){
			$('.msg').html('请填写支行信息!').show();
			$("#branch_bank_name").focus();
			isValid = false;
		}else{
			$.ajax({
				type : "post",
				url : "addAccount",
				dataType : "json",
				data : {
					"realname":$('#userName').val(),
					"card_type":$('#card_type').val(),
					"card_num":$('#userCard').val(),
					"mobile":$('#phone').val(),
					"bank_type":$('#bank_type').val(),
					"account_number":$('#bank').val(),
					"branch_bank_name":$('#branch_bank_name').val()
				},
				success : function(data) {
					//alert(JSON.stringify(data));
					if(data['code']>1){
						$('.msg').html("添加成功").show();
						window.location.href = URL+"?shop_id="+shop_id;
					}else{
						$('.msg').html("添加失败").show();
					}
					
				}
			})
		}
		return isValid; 
	}
	$('#submit').click(function(){
//		bankcard() && userName() &&idCard()&&phone()&& code();
		bankcard() && userName() && idCard() && phone() && branch_bank_name();
	})
	$('#bank').change(function(){
		bankCardAttribution($('#bank').val(),1);
	})
});
var countdown=60; 
function sendemail(){
	var isValid = true;
	var mobile = /^1[3-9]\d{9}$/;
	var phone = $("#phone").val();
	if(phone == ''){
		$('.msg').html('请填写手机号码!').show();
		$("#phone").focus();
		isValid = false;
	}else if(!mobile.test(phone)){
		$('.msg').html('请填写有效的手机号!').show();
		$("#phone").focus();
		isValid = false;
	}
    var obj = $("#btn");
    $.ajax({       //获取验证码
    	type: "post",
		url: "APP_MAIN/login/sendSmsRegisterCode",
		data: {"mobile":mobile},
    	dataType:'json',
    	success:function(data){
    		if(data['code']=='0'){
				settime(obj);
			}else{
				$('.msg').html(data["message"]).show();
				return false;
			}
    	},
    });
settime(obj);
}
