/**
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 */
$(function() {
	//初始化数据
	init();
	/*
	$(".item-options[data-flag]").click(function() {
		var curr_options = $(this);//当前点击的项
		var flag = curr_options.attr('data-flag');
		if(flag != undefined){
			$(".mask-layer").fadeIn(300);
			curr_options.next(".mask-layer-control[data-flag='"+flag+"']").slideDown(300);
			if(getCurrMaskLayer() != null){
				getCurrMaskLayer().find("li").click(function(){
					var curr_li = $(this);
					getCurrMaskLayer().find("li").removeClass("active");
					curr_li.addClass("active");
					var msg = curr_li.children("div:last").text();//内容
					switch(flag){
					
						case "use-coupon":
							//当前打开的是优惠券
							updateUseCoupon(curr_li,curr_options);
							msg = "不使用优惠券";
							var money = 0;
							if(curr_li.attr("data-id") != undefined && curr_li.attr("data-money") != undefined){
								msg = "￥"+parseFloat(curr_li.attr("data-money")).toFixed(2);
							}
							break;
							
						case "pay":
							//当前打开的是支付方式
							updatePay(curr_li);
							break;
							
						case "distribution":
							//当前打开的是配送方式
							updateDistribution(curr_li);
							break;
							
						case "express_company":
							//当前打开的是物流公司
							updateExpressCompany(curr_li);
							break;
							
						case "pickup_address":
							//当前打开的是自提地址列表
							updatePickupAddress(curr_li);
							break;
							
						case "invoice":
							//当前打开的是发票信息
							updateInvoice(curr_li);
							break;
							
						case "invoice-content":
							//当前打开的是选择发票内容
							break;
					}

					curr_options.children("span").text(msg);
					getCurrMaskLayer().slideUp(300);
					$(".mask-layer").fadeOut(300);
					calculateTotalAmount();
					
				});
			}
		}
	});
	*/
	/**
	 * 用户输入可用余额，进行验证并矫正，同时更新总优惠、应付金额等数据
	 * 规则：
	 * 1、可用余额，不可超过订单总计
	 * 2、不可超过用户最大可用余额
	 * 3、只能输入数字
	 * 2017年6月22日 15:00:14 王永杰
	 */
	$("#account_balance").keyup(function(){
		if(!validationMemberBalance()){
			calculateTotalAmount();
		}
	});
	
	/**
	 * 关闭弹出框（包括点击遮罩层、确定按钮、右上角X按钮）
	 * 2017年6月21日 14:18:15 王永杰
	 */
	$(".mask-layer,.btn-green,.mask-layer-control .close").click(function() {
		getCurrMaskLayer().slideUp(300);
		$(".mask-layer").fadeOut(300);
	});

});


/**
 * 初始化数据，仅在第一次加载时使用
 * 2017年6月22日 14:59:33 王永杰
 */
function init(){
	
	//商品数量
	$(".js-goods-num").text($("div[data-subtotal]").length);
	
	//商品总计
	var total_money = 0;
	$("div[data-subtotal]").each(function(){
		//循环小计
		total_money += parseFloat($(this).attr('data-subtotal'));
	})

	//商品总计
	$(".js-total-money").text(total_money.toFixed(2));
	
	/**
	 * 选中第一个配送方式对应的更新数据
	 * 2017年6月28日 17:33:19
	 */
	if($(".mask-layer-control[data-flag='distribution'] li").length){
		$(".mask-layer-control[data-flag='distribution'] li").each(function(i){
			if(i==0){
				switch(parseInt($(this).attr("data-flag"))){
				case 1:
					//商家配送
					$(".item-options[data-flag='express_company']").slideDown(300);//物流公司显示
					
					break;
				case 2:
					
					//门店自提
					var pickup_address = $(".item-options[data-flag='pickup_address']");//自提地址
					var pickup_count = parseInt(pickup_address.attr("data-count"));//自提列表数量
					var address = "商家未配置自提点";
					if(pickup_count){
						
						var active_li = $(".mask-layer-control[data-flag='pickup_address'] li.active");
						address = active_li.children("div:last").text();
						pickup_address.attr("data-id",active_li.attr("data-id"));
						
					}else{
						
						pickup_address.children("span").removeClass("arrow-right");
						
					}
					pickup_address.children("span").html(address);
					$(".item-options[data-flag='distribution']").attr("data-select",1);//只有是门店自提，就免邮
					pickup_address.slideDown(300);//自提列表显示
					
					break;
				}
				$(this).addClass("active");
			}
			return false;
		});
	}else{
		//没有配送方式
		$(".item-options[data-flag='distribution']").attr("data-select",-1);//只有是门店自提，就免邮
	}
	
	//初始化合计
	var init_total_money = parseFloat($("#hidden_count_money").val());//商品金额
	if($("#hidden_full_mail_is_open").val()==1){
		
		//满额包邮开启
		if(init_total_money>=parseFloat($("#hidden_full_mail_money").val())){

			$("#hidden_express").val(0);
			
		}
	}
	$("#express").text(parseFloat($("#hidden_express").val()).toFixed(2));
	init_total_money += parseFloat($("#hidden_express").val());//运费
	$("#realprice").attr("data-old-total-money",init_total_money.toFixed(2));//原合计（不包含优惠）
	$("#realprice").attr("data-old-keep-total-money",init_total_money.toFixed(2));//保持原合计
	$(".price").html("￥"+init_total_money.toFixed(2));//保持原合计
	
	calculateTotalAmount();
}


/**
 * 获取当前打开的弹出框对象
 * 2017年6月21日 14:19:20 王永杰
 */
function getCurrMaskLayer(){
	return $(".mask-layer-control:visible");
}
/**
 * 更新优惠券数据
 * 使用优惠券的同时，要更新余额的最大输入限制
 * 创建时间：2017年6月21日 16:55:15 王永杰
 * 更新时间：2017年6月22日 15:05:03 王永杰
 * @param curr_li 当前选择的优惠券
 * @param curr_options 当前的优惠券项
 */
function updateUseCoupon(curr_li,curr_options){
    var id = 0;
    var shop_id = 0;
	var money = 0;
	if(curr_li.attr("data-id") != undefined && curr_li.attr("data-money") != undefined){
        id = curr_li.attr("data-id");
        shop_id = curr_li.attr("data-shop-id");
		money = curr_li.attr("data-money");
	}
    curr_options.attr("data-id",id);
    curr_options.attr("data-shop-id",shop_id);
	curr_options.attr("data-money",money);
}
//扫码支付 选择支付类型。6积分支付;7余额加积分支付;1微信;2支付宝;5余额；参阅数据库
function payment() {
	var pay_type = $("#pay_type").val();
	var out_trade_no = $("#out_trade_no").val();
	var mjs = $('#mjs').val();
	var kijs = $('#kijs').val();
	if (pay_type == 1) {
		//微信支付
		if(mjs !=''){
			window.location.href = APPMAIN + "/pay/wchatpay?no=" + out_trade_no;
		}else{
			showBox("请输入支付金额");
		}
	} else if (pay_type == 2) {
		//支付宝支付
		if(mjs !=''){
			window.location.href = APPMAIN + "/pay/alipay?no=" + out_trade_no;
		}else{
			showBox("请输入支付金额");
		}
	}else if(pay_type == 5){
		//余额支付
		if(mjs !=''){
			$('.pwdBox').show()
			$('.price').html('￥' + mjs +'元')
		}else{
			showBox("请输入支付金额");
		}
	}else if(pay_type == 6){
		//积分支付
		if(kijs !=''){
			$('.pwdBox').show();
			$('.price').html('￥' + kijs)
		}else{
			showBox("请输入支付积分");
		}
	}else if(pay_type == 7){
		//余额+积分支付
		if(mjs !='' && kijs !=''){
			$('.price').html('余额:'+mjs+' + 积分:'+kijs+'');
			$('.pwdBox').show();
		}else if(kijs ==''){
			showBox("请输入支付积分");
		}else if(mjs ==''){
			showBox("请输入支付金额");
		}
	}
}
/**
 * 更新选择支付方式后的操作
 * 选择不同的支付方式，配送方式也会进行更新
 * 1、选择在线支付
 *   配送方式有：1、商家配送（计算运费），2、门店自提（不计算运费）
 * 2、货到付款
 *   配送方式有：1、商家配送（计算运费）
 * 创建时间：2017年6月21日 16:59:25 王永杰
 * 更新时间：2017年6月22日 14:50:57 王永杰
 * 
 * @param curr_li 当前选中的li项
 */
function updatePay(curr_li){
	var msg = curr_li.children("div:last").text();//内容
	var pay = $(".item-options[data-flag='pay']");//支付方式选项
	var pickup_address = $(".item-options[data-flag='pickup_address']");//自提地址选项
	var distribution = $(".item-options[data-flag='distribution']");//配送方式选项
	var distribution_mask = $(".mask-layer-control[data-flag='distribution']");//配送方式弹出框
	distribution_mask.find("li[data-flag]").removeClass("active");
	distribution_mask.find("li[data-flag]:first").addClass("active");
	distribution.attr("data-select",0);//配送方式 -1，：没有配置配送方式，0，商家配送，1：门店自提
	distribution_mask.children(".footer").show();
	distribution_mask.find("p").remove();
	distribution.children("span").text(distribution_mask.find("li[data-flag]:first div:last").text());//还原配送方式
	switch(msg){
		case "在线支付":
			pay.attr("data-select",0);
			distribution_mask.find("li[data-flag='2']").show();//显示配送方式，门店自提
		break;
		case "货到付款":
			pay.attr("data-select",4);
			distribution_mask.find("li[data-flag='2']").hide();//隐藏配送方式，门店自提
			pickup_address.children("span").html("");//清空自提地址
			pickup_address.attr("data-id",0);
			pickup_address.slideUp(300);//隐藏自提地址
			if(distribution_mask.find("li[data-flag='1'] div:last").text() == null || distribution_mask.find("li[data-flag='1'] div:last").text() == ''){
				//如果商家没有开启配送方式
				distribution.children("span").text("商家未配置商家配送");//给予提示
				distribution_mask.children(".footer").hide();
				distribution_mask.append('<p style="padding: 30px;text-align: center;">商家未配置配送方式</p>');
				distribution.attr("data-select",-1);//配送方式 -1，：没有配置配送方式，0，商家配送，1：门店自提
			}
		break;
	}
}


/**
 * 更新配送方式，如果选择的是门店自提则显示自提地址
 * 2017年6月21日 17:11:05 王永杰
 * 
 * @param curr_li 当前选中的配送方式
 */
function updateDistribution(curr_li){
	var flag = parseInt(curr_li.attr("data-flag"));//1：配送方式，2：门店自提
	var pickup_address = $(".item-options[data-flag='pickup_address']");//自提地址选项
	var pickup_address_span = pickup_address.children("span");//自提地址内容
	var express_company = $(".item-options[data-flag='express_company']");//物流公司（配送方式显示，门店自提隐藏）
	var pickup_address_mask = $(".mask-layer-control[data-flag='pickup_address']");//自提地址弹出框
	var active_li = pickup_address_mask.find("li.active");
	switch(flag){
	case 1:
		$(".item-options[data-flag='distribution']").attr("data-select",0);
		pickup_address.slideUp(300);//隐藏自提地址
		express_company.slideDown(300);//显示物流公司
		break;
	case 2:
		$(".item-options[data-flag='distribution']").attr("data-select",1);
		express_company.slideUp(300);//隐藏物流公司
		var address = active_li.children("div:last").text();
		pickup_address.attr("data-id",active_li.attr("data-id"));
		//处理显示方式
		if(pickup_address.attr("data-count")> 0){
			if(address.length>20){
				pickup_address_span.removeClass("arrow-right");
			}
			pickup_address_span.addClass("arrow-right");
		}else{
			address = pickup_address_mask.find("p").text();
			pickup_address_span.removeClass("arrow-right");
		}
		pickup_address_span.html(address);//设置选择后的自提地址
		pickup_address.slideDown(300);//显示自提地址
		break;
	}
}

/**
 * 选择物流公司，更新运费
 * 2017年6月28日 16:21:47 王永杰
 * @param curr_li 当前选中的物流公司
 
function updateExpressCompany(curr_li){
	var co_id = curr_li.attr("data-coid");//物流公司
	var express_fee = parseFloat(curr_li.attr("data-express-fee")).toFixed(2);//运费
	$(".item-options[data-flag='express_company']").attr("data-select",co_id);
	$(".item-options[data-flag='express_company']").attr("data-express-fee",express_fee);
	$("#hidden_express").val(express_fee);
	calculateTotalAmount();
} */


/**
 * 更新自提地址
 * 2017年6月21日 17:14:32 王永杰
 * @param curr_li
 */
function updatePickupAddress(curr_li){
	var msg = curr_li.children("div:last").text();//内容
	var pickup_address = $(".item-options[data-flag='pickup_address']");//自提地址选项
	if(msg.length>20){
		pickup_address.children("span").removeClass("arrow-right");
	}else{
		pickup_address.children("span").addClass("arrow-right");
	}
	var pickup_address_mask = $(".mask-layer-control[data-flag='pickup_address']");//自提地址弹出框
	var active_li = pickup_address_mask.find("li.active");
	pickup_address.attr("data-id",active_li.attr("data-id"));
}
/**
 * 更新发票
 * 2017年6月21日 18:12:19 王永杰
 * 
 * @param curr_li
 */
function updateInvoice(curr_li){
	var invoice = $(".item-options[data-flag='invoice']");//发票选项
	var invoice_content = $(".item-options[data-flag='invoice-content']");//发票内容选项
	//弹出框
	var invoice_content_mask = $(".mask-layer-control[data-flag='invoice-content']");//发票内容弹出框
	var msg = curr_li.children("div:last").text();//内容
	var text = "选择发票内容";
	switch(msg){
		case "不需要发票":
			invoice.attr("data-select",0);
			$(".order .invoice").slideUp(300);
			break;
		case "需要发票":
			invoice.attr("data-select",1);
			$(".order .invoice").slideDown(300);
			text = invoice_content_mask.find("li.active").children("div:last").text();
			break;
	}
	invoice_content.children("span").text(text);
}

/**
 * 验证可用余额输入是否正确，并矫正数据
 * 2017年6月22日 12:13:14 星期四
 * @returns {Boolean}
 */
function validationMemberBalance(){
	if($("#account_balance").val() != undefined){
		if(isNaN($("#account_balance").val())){
			showBox("余额输入错误");
			$("#account_balance").val("");
			calculateTotalAmount();
			return true;
		}
		var r = /^\d+(\.\d{1,2})?$/;
		var account_balance = $("#account_balance").val() == "" ? 0 : parseFloat($("#account_balance").val());//可用余额
		var max_total = parseFloat($("#realprice").attr("data-old-total-money")).toFixed(2);//总计
		if(!r.test(account_balance)){
			showBox("余额输入错误");
			$("#account_balance").val(account_balance.toString().substr(0,account_balance.toString().length-1));
			return true;
		}
		
		var user_money = $("#account_balance").attr("data-max");// 最大可用余额
		if (account_balance > user_money) {
			showBox("不能超过可用余额！");
			$("#account_balance").val($("#account_balance").attr("data-max"));
			calculateTotalAmount();
			return true;
		}
		
		//可用余额不能超过订单总计
		if(account_balance>max_total){
			$("#account_balance").val(max_total);
			calculateTotalAmount();
			return true;
		}
		
	}
	
	return false;
}

/**
 * 验证订单数据
 * 2017年6月22日 15:08:10 王永杰
 * 
 * @returns true:验证成功，false：验证失败
 */
function validationOrder(){
	if(validationMemberBalance()){
		return false;
	}

	if ($("#addressid").val() == undefined ||$("#addressid").val() == '' ) {
		showBox("请先选择收货地址");
		return false;
	}
	
	if(parseInt($(".item-options[data-flag='distribution']").attr("data-select")) == -1){
		showBox("商家未配置配送方式");
		return false;
	}
	console.log($(".item-options[data-flag='express_company']").attr("data-select"));
	if(parseInt($(".item-options[data-flag='distribution']").attr("data-select")) == 0){
		if($(".item-options[data-flag='express_company']").attr("data-select") == undefined){
			showBox("商家未配置物流公司");
			return false;
		}
	}
	
	
	if(parseInt($(".item-options[data-flag='invoice']").attr("data-select")) == 1){
		//如果选择需要发票，则发票抬头必填、发票内容必选
		if($("#invoice-title").val().length == 0){
			showBox("请输入个人或公司发票抬头");
			$("#invoice-title").focus();
			return false;
		}
		
		if($(".item-options[data-flag='invoice-content']").children("span").text().length == 0){
			showBox("请选择发票内容");
			return false;
		}
	}
	
	if(parseInt($(".item-options[data-flag='distribution']").attr("data-select")) == 1){
		//选择门店自提
		if(parseInt($(".item-options[data-flag='pickup_address']").attr("data-count"))==0){
			showBox("商家未配置自提点，请选择其他配送方式");
			return false;
		}
	}
	return true;
}

/**
 * 计算总金额
 * 2017年5月8日 13:55:48
 */
function calculateTotalAmount(){
	var money = parseFloat($("#hidden_count_money").val());// 商品总价
	var total_discount = 0;//总优惠
	var order_invoice_tax_money = 0;//发票税额 显示
	var tax_sum = parseFloat($("#hidden_count_money").val());//计算发票税额计算：（商品总计+运-优惠活动-优惠券）*发票税率
	var account_balance = 0;//可用余额
	var old_total_money = parseFloat($("#realprice").attr("data-old-keep-total-money"));//原合计
	//如果选择的是门店自提，则不计算运费
	if($(".item-options[data-flag='distribution']").attr("data-select")>0){
		$("#express").text("0.00");
	}else{
		money += parseFloat($("#hidden_express").val());
		tax_sum += parseFloat($("#hidden_express").val());
		$("#express").text(parseFloat($("#hidden_express").val()).toFixed(2));
	}

	//满减送活动
	if(parseFloat($("#hidden_discount_money").val())>0){
		total_discount+= parseFloat($("#hidden_discount_money").val());
		money-= parseFloat($("#hidden_discount_money").val());
		tax_sum -= parseFloat($("#hidden_discount_money").val());
	}
	
	// 优惠券
	var user_coupon = getUseCoupon();
	if(user_coupon.money > 0){
		// 使用优惠券
		money -= parseFloat(user_coupon.money);
		tax_sum -= parseFloat(user_coupon.money);
		if(money>0){
			total_discount += parseFloat(user_coupon.money);
		}else{
			//如果应付金额为负数，则计算出剩余的金额
			total_discount += parseFloat(user_coupon.money) + parseFloat(money);
		}
	}

	//发票税额
	if(parseInt($(".item-options[data-flag='invoice']").attr("data-select")) == 1){
		order_invoice_tax_money = tax_sum * (parseFloat($("#hidden_order_invoice_tax").val())/100);
		money += order_invoice_tax_money;
		if(order_invoice_tax_money<0){
			order_invoice_tax_money = 0;
		}
	}
	//可用余额
	if($("#account_balance").val() != undefined){
		account_balance = $("#account_balance").val() == "" ? 0 : parseFloat($("#account_balance").val());
		if(account_balance>0){
			money -= account_balance;
		}
	}
	
	//应付金额
	if(money<0){
		if($("#account_balance").val() != undefined){
			var balance = parseFloat($("#account_balance").val()) + parseFloat(money);
			account_balance = 0;//使用余额，显示
			//矫正使用余额（不能超出应付金额）
			if(balance>0){
				$("#account_balance").val(balance.toFixed(2));
			}else{
				$("#account_balance").val("");
			}
		}
		money = 0;
	}
	old_total_money += parseFloat(order_invoice_tax_money);
	$("#realprice").attr("data-old-total-money",old_total_money.toFixed(2));//原合计（包括税额,不包含优惠）
	$("#realprice").text(money.toFixed(2));//合计
	$("#realprice").attr("data-total-money",money.toFixed(2));//合计[实际付款金额]（包含优惠券、运费）
	$("#discount_money").text(total_discount.toFixed(2))//总优惠
	if($("#account_balance").val() != undefined){
		$("#use_balance").text(account_balance.toFixed(2));//使用余额，显示
	}
	$("#invoice_tax_money").text(order_invoice_tax_money.toFixed(2));//税率
	validationMemberBalance();
}

$(function(){
	var $input = $(".fake-box input");
	$("#pwd-input").on("input", function() {
	    var pwd = $(this).val().trim();
	    for (var i = 0, len = pwd.length; i < len; i++) {
	        $input.eq("" + i + "").val(pwd[i]);
	    }
	    $input.each(function() {
	        var index = $(this).index();
	        if (index >= len) {
	            $(this).val("");
	        }
	    });
	});
	$('.eudi .fr').click(function(){
		$('.pwdBox').hide();
		$('#pwd-input').val('');
		$(".fake-box input").each(function() {
	        $(this).val("");
	    });
	})
})
