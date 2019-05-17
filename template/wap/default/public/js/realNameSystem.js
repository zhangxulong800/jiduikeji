$(function(){
	$('#submit').click(function(){
		var myReg = /^[\u4e00-\u9fa5]+$/;
		var userName = $('#userName').val(),
			userCard = $('#userCard').val();
		if(userName == ''){
			$('.msg').html('请输入真实姓名!').show();
			$("#userName").focus();
		}else if(!myReg.test(userName)){
			$('.msg').html('请输入正确的姓名!').show();
			$("#userName").focus();
			$('#userName').val('');
		}else if(userCard == ''){
			$('.msg').html('请输入身份证号!').show();
			$("#userCard").focus();
		}else if($('#positiveImg').val() =='' && $('#positiveImg').next('img').attr('src')==''){
			$('.msg').html('请上传身份证正面!').show();
		}else if($('#oppositeImg').val() =='' && $('#oppositeImg').next('img').attr('src')==''){
			$('.msg').html('请上传身份证反面!').show();
		}else{
			element();
		}
	})
	$("input[id$='Img']").change(function() {
		var filepath = $(this).val();
		var extStart = filepath.lastIndexOf(".");
		var ext = filepath.substring(extStart, filepath.length).toUpperCase();
		if (ext != ".PNG" && ext != ".JPG" &&  ext != ".JPEG") {
			alert("图片限于png,jpg,jpeg格式");
			//清空文件选择
			$(this).after($(this).clone(true).val(""))
			$(this).remove();
		}
		if(this.files){	//IE10及其它
			var fileObject = this.files[0];
			if(fileObject){
				var fileSize = fileObject.size,
					size = fileSize/1024/1024;
				if (size > 5){
					alert("上传的图片大小不能超过5M!");
					//清空文件选择
					$(this).after($(this).clone(true).val(""));
					$(this).remove();
					return;
				}
				//获取图片blob编码
				var windowURL = window.URL || window.webkitURL,
				dataURL = windowURL.createObjectURL(fileObject);
				$(this).next('img').attr("src", dataURL).show();     //将图片路劲赋给img标签
			}
		}
    });
})
function element() {   //身份证号码验证
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
		if(isValid){
			$('#formEdit').attr('action',urls);
			$('#submit').attr('type','submit').submit()
		}else{
			return isValid; 
		}
}
