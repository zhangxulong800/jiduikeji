/*只有共用的才可放在该文件中！！！便于查找！！！*/
/**
 * 获取快递物流id
 */
function getExpressId(){
	var id = '';
	$(".express_company").each(function(){
		var state=$(this).parent().css("display");//display状态
		if(state !== 'none'){ //显示快递物流时使用
			var nowval=$(this).val();
			var shop_id=$(this).attr("data-shop-id");//店铺编号
			id=id+','+shop_id+'_'+nowval;
		}
    });
	if(id=='' || id==undefined){
		$(".delivery").each(function(){
			var nowval=$(this).attr("data-str");
			id=id+nowval;
		});
	}
	return id;
}
/**
 * 获取自提地址id
 */
function getPickupId(){
	var id = '';
	$(".pickup_address").each(function(){
		var state=$(this).parent().css("display");//display状态
		if(state == 'block'){
			var nowval=$(this).val();
			var shop_id=$(this).attr("data-shop-id");//店铺编号
			id=id+','+shop_id+'_'+nowval;
		}
    });
	return id;
}
/**
 * 获取优惠券信息
 */
function getUseCoupon(){
	var coupon = {
		id : '',
		money : 0
	};
	$(".coupon").each(function(){
		var nowval=$(this).val();
		var shop_id=$(this).attr("data-shop-id");//店铺编号
		if(parseInt(nowval) > 0){
			coupon.id=coupon.id+','+shop_id+'_'+nowval;
			coupon.money += parseFloat($(this).find("option[value='"+nowval+"']").attr("data-money"));
		}
    });
	return coupon;
}
/*校验支付密码*/
function post_paypw() {
	var paypassword=$('#pwd-input').val();
	$.ajax({
		url : APPMAIN + "/member/check_paypassword",
		type : "post",
		data : {
			'paypassword' : paypassword,
		},
		success : function(res) {
			if (res.code > 0) {
				userpay();
			} else {
				showBox(res.message);
			}
		}
	});
}
//选择支付类型。6积分支付;7余额加积分支付;1微信;2支付宝;5余额；参阅数据库
function calculate() {
	var pay_type = $("#pay_type").val();
	var out_trade_no = $("#out_trade_no").val();
	if (pay_type == 1) {
		//微信支付
		window.location.href = APPMAIN + "/pay/wchatpay?no=" + out_trade_no;
	} else if (pay_type == 2) {
		//支付宝支付
		window.location.href = APPMAIN + "/pay/alipay?no=" + out_trade_no;
	}else if(pay_type == 5){
		//余额支付
		$('.pwdBox').show();
		$('.bbg').show();
	}else if(pay_type == 6){
		//积分支付
		$('.pwdBox').show();
		$('.bbg').show();
	}else if(pay_type == 7){
		//余额+积分支付
		var usemoney = $('#usemoney').val();
		var usepoint = $('#usepoint').val();
		if(usemoney ==''){
			showBox('余额不能为空！');
			return ;
		}
		if(usepoint ==''){
			showBox('积分不能为空！');
			return ;
		}
		var pay_money = $('#pay_money').val();
		var reg = /^[1-9]\d*$/;
		$('.price').html('余额:'+usemoney+'+ 积分:'+usepoint );
		$('.pwdBox').show();
		$('.bbg').show();
	}
}
//余额、积分等支付处理
function userpay() {
	var pay_type = $("#pay_type").val();
	var out_trade_no = $("#out_trade_no").val();
	if(pay_type ==5 || pay_type ==6 || pay_type ==7){
		if(pay_type ==7){
			var usemoney=$("#usemoney").val();
			var usepoint=$("#usepoint").val();
		} else {var usemoney=0;var usepoint=0;}
		$.ajax({
			url : APPMAIN + "/order/payorder",
			type : "post",
			data : {
				'pay_type' : pay_type,
				'out_trade_no' : out_trade_no,
				'usemoney':usemoney,
				'usepoint':usepoint,
			},
			success : function(res) {
				if (res.code > 0) {
					location.href = APPMAIN+'/order/myorderlist';
				} else {
					showBox(res.message);
				}
			}
		});
	}
}
var flag = false;//防止重复提交
/*提交数据，两端共用。num为1指pc端 2指wap端*/
function submitOrder(num){
	if(num==1){
		var url=shop_main+"/order/ordercreate";
		if(Number($('#count_point_exchange').val()) > $('#member_account_point').html()){ $.msg('积分不足');return false; }
	} else {
		var url=APPMAIN+"/order/ordercreate";
	}
	if(validationOrder()){
		if(flag){
			return;
		}
		flag = true;
		var goods_sku_list = $("#goods_sku_list").val();// 商品Skulist
		var leavemessage = $("#leavemessage").val();// 订单留言
		var use_coupon = getUseCoupon();//优惠券id
		var account_balance = 0;//可用余额
		if($("#account_balance").val() != undefined){
			account_balance = $("#account_balance").val() == "" ? 0 : $("#account_balance").val();
		}
		var integral = $("#count_point_exchange").val() == ""? 0 : $("#count_point_exchange").val();//积分
		var pay_type = parseInt($("#paylist").attr("data-select"));//支付方式 0：在线支付，4：货到付款（目前只用0）
		var buyer_invoice = getInvoiceContent();//发票
		$.ajax({
			url : url,
			type : "post",
			data : {
				'goods_sku_list' : goods_sku_list,
				'leavemessage' : leavemessage,
				'use_coupon' : use_coupon.id,
				'integral' : integral,
				'account_balance' : account_balance,
				'pay_type' : pay_type,
				'buyer_invoice' : buyer_invoice,
				'pick_up_id' : getPickupId(),
				'express_company_id' :getExpressId(),
			},
			success : function(res) {
				if (res.code <= 0) {
					if(res.code == '-1'){
						showBox('你已购买过该会员的商品，无法在选购！');
					}else{
						showBox(res.message);
					}
					flag = false;
				} else{
					if(num==1){
						$(".btn-jiesuan").css("background-color","#ccc");
						//如果实际付款金额为0，跳转到个人中心的订单界面中
						if(parseFloat($("#realprice").attr("data-total-money")) == 0){
							location.href = APPMAIN + '/pay/paycallback?msg=1&out_trade_no=' + res.code;
						}else if(pay_type == 4){
							location.href = shop_main + '/member/orderlist';
						}else{
							location.href = shop_main + '/pcpay/getpayvalue?out_trade_no=' + res.code;
						}
					} else {
						if (res.code > 0) {
							//如果实际付款金额为0，跳转到个人中心的订单界面中
							if(parseFloat($("#realprice").attr("data-total-money")) == 0){
								location.href = APPMAIN + '/pay/paycallback?msg=1&out_trade_no=' + res.code;
							}else if(pay_type == 4){
								location.href = APPMAIN + '/order/myorderlist';
							}else{
								var hidden_exc=$("#hidden_exc").val();
								if(hidden_exc==1){
									location.href = APPMAIN + '/pay/getExcPay?out_trade_no=' + res.code+'&hidden_exc=1';
								} else {
									location.href = APPMAIN + '/pay/getpayvalue?out_trade_no=' + res.code;
								}
							}
						} else {
							showBox(res.message);
							flag = false;
						}
					}
				}
			}
		});
	}
}
/**
 * 获取选择的发票内容，返回拼装好的格式
 * 2017年6月14日 19:39:56 王永杰
 */
function getInvoiceContent(){
	var content = "";
	/*
	if(parseInt($(".item-options[data-flag='invoice']").attr("data-select")) == 1){
		//如果选择需要发票，则发票抬头必填、发票内容必选
		content = $("#invoice-title").val()+"$"+$(".item-options[data-flag='invoice-content']").children("span").text()+"$"+$("#taxpayer-identification-number").val();
	} */
	return content;
}
/*通兑积分抵10%金额*/
function useExc_pay() {
	var out_trade_no=$('#out_trade_no').val();
	$.ajax({
		url : APPMAIN + "/member/deductFromExc",
		type : "post",
		data : {
			'out_trade_no' : out_trade_no,
		},
		success : function(res) {
			if (res.code > 0) {
				window.location.reload();
			} else {
				showBox(res.message);
			}
		}
	});
}