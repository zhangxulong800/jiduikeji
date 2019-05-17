function saveAddress() {
	if (!Check_Consignee()) {
		return false;
	}
	var url = "";
/*	var ref_url = $("#ref_url").val();*/
	var addressID = $("#AddressID").val();
	var tempSeleAreaFouth = $("#seleAreaFouth").find("option:selected").text();
	// 表示没有区县
	if (tempSeleAreaFouth == "选择区/县") {
		tempSeleAreaFouth = "";
	}
	var addressinfo = $("#AddressInfo").val();
	var province = $("#seleAreaNext").val();
	var city = $("#seleAreaThird").val();
	var district = $("#seleAreaFouth").val();
	var name=$("#Name").val();
	var mobile=$("#Moblie").val();
	var $remark=$("#AddressInfo").val();
	var address_id=$("#adressid").val();
	if(address_id <= 0 &&　address_id　!= ''){
		$.ajax({
			type: "POST",
			url: "addressList",
			data: {"consigner":name,"mobile":mobile,"province":province,"city":city,"district":district,"address":addressinfo},
			success: function (txt) {
				if (txt["code"] >0) {
				   window.location.href ="addressTotal";
				} else {
					alert(txt);
				}
			}
		});
	}else{
		$.ajax({
			type: "POST",
			url: "updateMemberAddress",
			data: {"id":address_id,"consigner":name,"mobile":mobile,"province":province,"city":city,"district":district,"address":addressinfo},
			success: function (txt) {
				if (txt["code"] > 0) {
					 window.location.href ="addressTotal";
				} else {
					 alert(txt);
				}
			}
		});
	}
}

function Check_Consignee() {
	var reg = /^\d{11}$/;
	if ($("#seleAreaFouth").val() < 0 || $("#seleAreaFouth").val() == "") {
		if ($("#seleAreaNext").val() == "") {
			alert("请选择省份");
			$("#seleAreaNext").focus();
			return false;
		}
		if ($("#seleAreaThird").val() == "") {
			alert("请选择市");
			$("#seleAreaThird").focus();
			return false;
		}
		if ($("#seleAreaFouth")[0].length == 1 && $("#seleAreaThird")[0].length > 1 && $("#seleAreaThird").val() > -1) {
			return true;
		} else {
			alert("请选择区/县");
			$("#seleAreaFouth").focus();
			return false;
		}
	}
	if ($("#Name").val() == "") {
		alert("收货人姓名不能为空");
		$("#Name").focus();
		return false;
	} 
	if ($("#AddressInfo").val() == "") {
		alert("详细地址不能为空");
		$("#AddressInfo").focus();
		return false;
	} 
	if ($("#Moblie").val() == "") {
		alert("手机号码不能为空");
		$("#Moblie").focus();
		return false;
	} 
	if (!reg.test($("#Moblie").val())) {
		alert("请输入正确的手机号码");
		$("#Moblie").focus();
		return false;
	} 
	
	
	return true;
}

// 选择省份弹出市区
function GetProvince(obj,sencond) {
	
	var id = $(obj).find("option:selected").val();
	var selCity = $(sencond)[0];
	for (var i = selCity.length - 1; i >= 0; i--) {
		selCity.options[i] = null;
	}
	var opt = new Option("请选择市", "-1");
	selCity.options.add(opt);
	$.ajax({
		type : "post",
		url : "getCity",
		dataType : "json",
		data : {
			"province_id" : id
		},
		success : function(data) {
			if (data != null && data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var opt = new Option(data[i].city_name,data[i].city_id);
					selCity.options.add(opt);
				}
			}
		}
	});
};
// 选择市区弹出区域
function getSelCity(obj,sencond) {
	var id = $(obj).find("option:selected").val();
	var selArea = $(sencond)[0];
	for (var i = selArea.length - 1; i >= 0; i--) {
		selArea.options[i] = null;
	}
	var opt = new Option("请选择市", "-1");
	selArea.options.add(opt);
	$.ajax({
		type : "post",
		url : "getDistrict",
		dataType : "json",
		data : {
			"city_id" : id
		},
		success : function(data) {
			if (data != null && data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var opt = new Option(data[i].district_name,data[i].district_id);
					selArea.options.add(opt);
				}
			}
		}
	});
}
function element() {   //身份证号码验证
    var elValue = $('#contacts_card_no').val(),
			isValid = true;
		if( !(elValue.length == 18 || elValue.length == 15) ){
			isValid = false;
		}
		if(/^(\d{18}|\d{17}(\d|X|x))$/.test(elValue)){	//身份证号为18位校检年月日
			var date = new Date(),	//日期对象
			oYear = date.getFullYear(),	//当前年份
			idYear = elValue.substr(6,4);	//获取用户输入的身份证生日年份
			if(oYear - idYear > 120 || oYear - idYear < 3){
				isValid = false;
			};
			var month = elValue.substr(10,2);	//获取用户输入的身份证生日月份
			if(month<1 || month>12){
				isValid = false;
			};
			var day = elValue.substr(12,2);	//获取用户输入的身份证出生日期
			if(day < 1 || day > 31){
				isValid = false;
			}
			if(month == 4 || month == 6 || month == 9 || month == 11){
				if(day>30){
					isValid = false;
				}
			}else if( month == 2){
				if(idYear%400 == 0 || idYear%4 == 0 && idYear%100 != 0){
					if(day>29){
						isValid = false;
					}
				}else{
					if(day>28){
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
				isValid = false;
			}
		}else{	//身份证号为15位校检月份日期
			var idYear = Number(elValue.substr(6,2)) + 1900,	//用户输入的身份证年份
			month = elValue.substr(8,2);	//用户输入的身份证月份
			if(month<1 || month>12){
				isValid = false;
			};
			var day = elValue.substr(10,2);	//用户输入的身份证日期
			if(day < 1 || day > 31){
				isValid = false;
			}
			if(month == 4 || month == 6 || month == 9 || month == 11){
				if(day>30){
					isValid = false;
				}
			}else if( month == 2){
				if(idYear%400 == 0 || idYear%4 == 0 && idYear%100 != 0){
					if(day>29){
						isValid = false;
					}
				}else{
					if(day>28){
						isValid = false;
					}
				}
			}
		}
		if(isValid){
			bankcard();
		}else{
			$.msg('身份证错误，请重新输入!');
			return isValid;
		}
}
function bankcard(){
		var bank = $('#bank_account_number').val();
	    var value = bank.replace(/\s/g, ''),
	        isValid = true,
	        rFormat = /^[\d]{12,19}$/;
	    if ( value == '' ) {
	       isValid = false;
	    }else if ( !rFormat.test(value) ) {
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
	            isValid = false;
	        }
	    }
	    if(isValid){
			$("#applySecond").hide();
			$("#applyThird").show();
			$.footer();
		}else{
			var k = 0;
			$.msg('请填写有效的银行卡号!');
			return ;
		}
	};
function bizcode() {
    var value = $('#business_licence_number').val(),
        isValid = true,
        rFormat = /^[1-6]\d{14}$/;

    // 共15位：6位首次登记机关代码 + 8位顺序码 + 校验位
    if (!rFormat.test(value)) {
        isValid = false;
    } else {
        var s = [],
            p = [10];

        for (var i=0; i<15; i++) {
            s[i] = ( p[i] % 11 ) + (+value.charAt(i));
            p[i+1] = (s[i] % 10 || 10) * 2; 
        }
        if (1 !== s[14] % 10) {
            isValid = false;
        }
    }
    if(isValid){
    	orgcode();
	}else{
		$.msg('请填写正确的营业执照号!');
		return isValid;
	}
}
function orgcode() {
    var value = $('#organization_code').val(),
        isValid = true,
        rFormat = /^[A-Z\d]{8}-[X\d]/;

    if (!rFormat.test(value)) {
        isValid = false;
    } else {
        var Wi = [3,7,9,10,5,8,4,2];
        var Ci = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        // 加权求和
        var sum = 0;
        for(var i = 0; i < 8; i++){
            sum += Ci.indexOf( value.charAt(i) ) * Wi[i];
        }
        // 计算校验值： C9 = 11 - MOD ( ∑(Ci*Wi), 11 )
        var C9 = 11 - (sum % 11);
        if (C9===10) C9 = 'X';
        else if (C9===11) C9 = 0;
        C9 = ''+C9;
        // 与校验位比对
        if ( C9 !== value.charAt(9)) {
            isValid = false;
        }
    }
 	if(isValid){
		$("#applySecond").hide();
		$("#applyThird").show();
		$.footer();
	}else{
		$.msg('请填写正确的组织机构代码!');
		return isValid;
	}
}