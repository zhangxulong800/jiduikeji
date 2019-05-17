$(function(){
	var url = location.search; //获取url中"?"符后的字串
	var theRequest = new Object();
	if(url.indexOf("?") != -1) {
		var str = url.substr(1);
	   		strs = str.split("&");
	   for(var i = 0; i < strs.length; i ++) {
	      theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
	   }
	}
	if(theRequest.J_flag != '' || theRequest.J_flag != null || theRequest.J_flag != undefined){
		$('#J_flag').val(theRequest.J_flag);
	}
})
function saveAddress() {
	if (!Check_Consignee()) {
		return false;
	}
	var addressID = $("#AddressID").val();
	var addressinfo = $("#AddressInfo").val();
	var province = $("#seleAreaNext").val();
	var city = $("#seleAreaThird").val();
	var district = $("#seleAreaFouth").val();
	var name=$("#Name").val();
	var mobile=$("#Moblie").val();
	var $remark=$("#AddressInfo").val();
	var address_id=$("#adressid").val();
	var data_json='',ajax_url='';
	
	if(typeof(address_id)=='undefined'){
		data_json = {"consigner":name,"mobile":mobile,"province":province,"city":city,"district":district,"address":addressinfo};
		ajax_url = 'addmemberaddress';
	}else{
		data_json = {"id":address_id,"consigner":name,"mobile":mobile,"province":province,"city":city,"district":district,"address":addressinfo};
		ajax_url = 'updatememberaddress';
	}
	var flag = $("#hidden_flag").val();
	var ref_url = $("#ref_url").val();
	$.ajax({
		type: "post",
		url: ajax_url,
		data: data_json,
		success: function (txt) {
			if (txt["code"] >0) {
				if(flag == 1){
					location.href=APPMAIN+"/member/memberaddress?flag=1";
				}else{
					if($('#J_flag').val() != undefined && $('#J_flag').val() != ''){
						location.href=APPMAIN+"/order/memberCenter";
					}
					if(ref_url != '' && ($('#J_flag').val() == undefined || $('#J_flag').val() == '')){
						// location.href=APPMAIN+"/order/paymentorder";
                        window.history.go(-2)
					}
				}
			} else {
				showBox(txt);
			}
		}
	});
}

function Check_Consignee() {
	var reg = /^\d{11}$/;
	if ($("#Name").val() == "") {
		showBox("姓名不能为空");
		$("#Name").focus();
		return false;
	} 
	if ($("#Moblie").val() == "") {
		showBox("手机号码不能为空");
		$("#Moblie").focus();
		return false;
	} 
	if (!reg.test($("#Moblie").val())) {
		showBox("请输入正确的手机号码");
		$("#Moblie").focus();
		return false;
	} 
	
	if ($("#seleAreaFouth").val() < 0 || $("#seleAreaFouth").val() == "") {
		if ($("#seleAreaNext").val() == "" || $("#seleAreaNext").val() < 0) {
			showBox("请选择");
			$("#seleAreaNext").focus();
			return false;
		}
		if ($("#seleAreaThird").val() == "" || $("#seleAreaThird").val() < 0) {
			showBox("请选择市");
			$("#seleAreaThird").focus();
			return false;
		}
		if ($("#seleAreaFouth").val() == "" || $("#seleAreaFouth").val() < 0) {
			var distr_num=0;
			$("#seleAreaFouth option").each(function(){
				var valnum = $(this).val(); //获取option的内容
				if(valnum > 0){ distr_num++;return false; }
			});
			if(distr_num >0 ){
				showBox("请选择区/县");
				$("#seleAreaFouth").focus();
				return false;
			}
		}
		
	}
	
	if ($("#AddressInfo").val() == "") {
		showBox("详细地址不能为空");
		$("#AddressInfo").focus();
		return false;
	} 
	
	return true;
}

// 选择省份弹出市区
function GetProvince() {
	var id = $("#seleAreaNext").find("option:selected").val();
	var selCity = $("#seleAreaThird")[0];
	for (var i = selCity.length - 1; i >= 0; i--) {
		selCity.options[i] = null;
	}
	
	var opt = new Option("请选择", "-1");
	selCity.options.add(opt);
	$.ajax({
		type : "post",
		url : "getcity",
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
				if(typeof($("#cityid").val())!='undefined'){
					$("#seleAreaThird").val($("#cityid").val());
					getSelCity();
					$("#cityid").val('-1');
				}
			}
		}
	});
};
// 选择市区弹出区域
function getSelCity() {
	var id = $("#seleAreaThird").find("option:selected").val();
	var selArea = $("#seleAreaFouth")[0];
	for (var i = selArea.length - 1; i >= 0; i--) {
		selArea.options[i] = null;
	}
	var opt = new Option("请选择区/县", "-1");
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
				if(typeof($("#districtid").val())!='undefined'){
					$("#seleAreaFouth").val($("#districtid").val());
					$("#districtid").val('-1');
				}
				
			}
		}
	});
}

$(function() {
	$('.labelBtn').on('click','div',function(){
		$('.labelBtn div').removeClass('active');
		$(this).addClass('active');
	});
	var selCity = $("#seleAreaNext")[0];
	for (var i = selCity.length - 1; i >= 0; i--) {
		selCity.options[i] = null;
	}
	var opt = new Option("请选择", "-1");
	selCity.options.add(opt);
	// 添加省
	$.ajax({
		type : "post",
		url : "getprovince",
		dataType : "json",
		success : function(data) {
			if (data != null && data.length > 0) {
				for (var i = 0; i < data.length; i++) {
					var opt = new Option(data[i].province_name,
							data[i].province_id);
					selCity.options.add(opt);
				}
				if(typeof($("#provinceid").val())!='undefined'){
					$("#seleAreaNext").val($("#provinceid").val());
					GetProvince();
					$("#provinceid").val('-1');
				}
			}
		}
	});
});