$(function(){
	$.ajax({
		type:"post",
		data:{'page_index':1,'page_size':3,'goods_arr':1},
		url:APPMAIN+"/Goods/firstFree",
		success:function(res){
			console.log(res)
			var category_html = ''
			for(var i=0;i<res.data.length;i++){
				category_html+='<li class="col-sm-4">'
					+'<a href="'+APPMAIN+'/goods/goodsdetail?id='+res.data[i].goods_id+'&type=1">'
					+'<img src="'+UPLOAD+'/'+res.data[i].picture_info.pic_cover_small+'" />'
					+'<div class="product_name">'+res.data[i].goods_name+'</div>'
					+'<div class="J_price">￥'+res.data[i].price+'</div>'
					+'<div class="price cf">'
					+'<img src="'+J_IMG+'/images/jplus.png" width="23px" height="11px"/>'
					+'<span>￥'+res.data[i].market_price+'</span>'
					+'</div>'
					+'<!--<div class="between">火拼</div>-->'
					+'</a>'
					+'</li>';
			}
			$('#firstFree ul').html(category_html);
		}
	});
	$.ajax({
		type:'post',
		url:APPMAIN+'/index/getGoodsDiscount',
		success:function(msg){
			var goods_discount_list = '';
			if(msg.data.length>0){
				for(var i = 0;i<msg.data.length;i++){
					goods_discount_list+='<span class="settime" starttime="'+msg.data[i].start_time+'" endtime="'+msg.data[i].end_time+'" ></span>'
						+'<li class="swiper-slide">'
						+'<a href="'+APPMAIN+'/goods/goodsdetail?id='+msg.data[i].goods_id+'&discount_id='+msg.data[i].discount_id+'" class="cf">'
						+'<div class="fl" style="height:188px"><img src="'+UPLOAD+'/'+msg.data[i].picture.pic_cover_big+'" width="149px" style="width:149px"/><div class="limit">限量50</div></div>'
						+'<div class="fr"><div class="J_title">'+msg.data[i].goods_name+'</div><div class="sign" style="display:none;">仅剩：'+msg.data[i].stock+'份</div>'
						+'</div><div class="positom"><div class="font-size-12 overview-countdown js-activity-tips started " data-status="started"><div class="js-discount-time countdown" data-countdown="3542691">'
						+'<span class="c-red js-hour">00</span> :<span class="c-red js-min">00</span> :<span class="c-red js-sec">00</span> </div></div>'
						+'<div class="group">立即参团</div></div></a>'
					if((((msg.data[i].price)*((msg.data[i].discount)/10)-msg.data[i].cost_price-msg.data[i].total_cost_price)*0.4).toFixed(0) <0){
						goods_discount_list+='<div class="share"><ul class="cf"><li><div>0积分</div><div>约0元</div></li>'
					}else{
						goods_discount_list+='<div class="share"><ul class="cf"><li><div>'+(((msg.data[i].price)*((msg.data[i].discount)/10)-msg.data[i].cost_price-msg.data[i].total_cost_price)*0.4).toFixed(0)+'积分</div><div>约'+((((msg.data[i].price)*((msg.data[i].discount)/10)-msg.data[i].cost_price-msg.data[i].total_cost_price)*0.4).toFixed(0))*6+'元</div></li>'
					}
					goods_discount_list+='<li><div>7天</div><div>无理由退货</div></li><li><div>2-5折</div><div>集采底价团购</div></li>'
						+'<li><div><a href="/wap/goods/share?id='+msg.data[i].goods_id+'"><img src="'+J_IMG+'/images/index/share.png" width="13px"/></a></div>'
						+'<div>分享</div></li></ul></div>'
						+'<div class="group_price">'
						+'<div class="vip_price">会员价:'+msg.data[i].market_price+'</div>'
						+'<div class="chenduan">成团价</div>'
						+'<div class="chengtuanjia"><font>￥</font>'+((msg.data[i].price)*((msg.data[i].discount)/10)).toFixed(0)+'</div>'
						+'</div>'
						+'</li>'
				}
			}
			$('#buying').html(goods_discount_list);
			var swiper = new Swiper('.park_product', {
				slidesPerView: 1,
				autoplay:4000,
				centeredSlides: true,
				paginationClickable: true,
				autoplayDisableOnInteraction: false,
				spaceBetween: 30,
				loop: true,
				nextButton: '.swiper-button-next',
				prevButton: '.swiper-button-prev',
			});
		}
	})
})
//用户微信绑定
$(function(){
	$('#existing').click(function(){
		$('.btnzhang').hide();
		$('.existing').show();
	})
	$('.deposit1').click(function(){
		$.ajax({
			type:"post",
			url : APPMAIN+"/member/pointGiftToPoint",
			success : function(data){
				if(data.code>0){
					location.href = APPMAIN;
				}else{
					showBox(data.message);
					setTimeout("top.location.href='" + APPMAIN + "'",1000);
				}
			}
		})
	})
	$('#haveNot').click(function(){
		$('.btnzhang').hide();
		$('.existings').show();
	});
	var mobile = /^1[34578]\d{9}$/;              //手机号码正则
	var chinese = /^[\u0391-\uFFE5]+$/;        //纯文字正则
	var regex = /^[0-9]+$/;                   //纯数字正则
	$('#submit').click(function(){            //未有账户
		if($('#userName').val().replace(/\s/g,"") == ''){
			showBox('请填写您的用户名!');
			$("#userName").focus();
		}else if($("#oldPwa").val() == ''){
			showBox('请输入密码!');
			$("#oldPwa").focus();
		}else if($("#oldPwa").val().length < 6){
			showBox('请输入6-16位密码!');
			$("#oldPwa").val('');
			$("#oldPwa").focus();
		}else if($("#newsPwa").val() ==''){
			showBox('请确认密码!');
			$("#newsPwa").focus();
		}else if($("#newsPwa").val() != $("#oldPwa").val()){
			showBox('两次密码不一致,请重新输入!');
			$("#newsPwa").val('');
			$("#newsPwa").focus();
		}else if($("#phone").val() == ''){
			showBox('请输入手机号!');
			$("#phone").focus();
		}else if(!mobile.test($("#phone").val())){
			showBox('请填写有效的手机号!');
			$("#phone").val('');
			$("#phone").focus();
		}else if($("#code").val() == ''){
			showBox('请输入动态验证码!');
			$("#code").focus();
		}else if(!$("#checked").is(':checked')){
			showBox('请选中用户协议');
		}else{
			$.ajax({
				type : "post",
				url : "register",
				async : true,
				data : {
					"username" : $('#userName').val().replace(/\s/g,""),
					"password" : $("#oldPwa").val(),
					"pid" :$("#pid").val(),
					"code" :$("#code").val(),
					"mobile" :$("#phone").val(),
				},
				success : function(data) {
					if(data["code"] > 0 ){
						showBox(data["message"]);
					}else{
						if(data["code"] == -2004){
							showBox('设置成功');
							location.href = APPMAIN;
						}else{
							showBox(data["message"]);
						}
					}
				},
				error:function(data){
					console.log(data);
				}
			});
		}
	});
	$('#fig').click(function(){              //已有账户
		$.ajax({
			type : "post",
			url : "",
			async : true,
			data : {
				"code" :$("#existingCode").val(),
				"mobile" :$("#existingPhone").val(),
			},
			success : function(data) {
				if(data["code"] > 0 ){
					showBox(data["message"]);
				}else{
					if(data["code"] == -2004){
						showBox('设置成功');
						location.href = APPMAIN;
					}else{
						showBox(data["message"]);
					}
				}
			},
			error:function(data){
				console.log(data);
			}
		});
	})
})
var countdown=60;
function sendemail(num){
	var mobile = /^1[34578]\d{9}$/;
	var regStr = /^(?=.*[a-zA-Z]+)(?=.*[0-9]+)[a-zA-Z0-9]+$/;    //字母加数字正则
	if(num == 1){
		var obj = $("#btns");
		if($("#existingPhone").val() == ''){
			showBox('请填写您的手机号!');
			$("#existingPhone").focus();
		}else if(!mobile.test($("#existingPhone").val())){
			showBox('请填写有效的手机号!');
			$("#existingPhone").val('');
			$("#existingPhone").focus();
		}else if($("#existingCaptcha").val() == ''){
			showBox('请输入动态码!');
			$("#existingCaptcha").val('');
			$("#existingCaptcha").focus();
		}else{
			$.ajax({
				type: "post",
				url: APPMAIN+"/Login/sendSmsRegisterCode",                     //判断验证码
				data: {"vertification":$("#existingCaptcha").val(),"mobile" :$("#existingPhone").val()},
				success: function(data){
					if(data.code == '0'){
						settime(obj,num);
					}else{
						if(data.code == '2'){              //动态码有错
							showBox(data.message);
							$(".verifyimg").attr("src",'/captcha.html'); //'{:captcha_src()}'
						}else{                 //后台返回的报错
							showBox(data.message);
							return false;
						}
					}
				}
			});
		}
	}else{
		var obj = $("#btn");
		if(!regStr.test($("#oldPwa").val())){
			showBox('密码必须是字母加数字!');
			$("#oldPwa").focus();
		}else if($("#newsPwa").val() != $("#oldPwa").val()){
			showBox('两次密码不一致,请重新输入!');
			$("#newsPwa").val('');
			$("#newsPwa").focus();
		}else if($("#phone").val() == ''){
			showBox('请填写您的手机号!');
			$("#phone").focus();
		}else if(!mobile.test($("#phone").val())){
			showBox('请填写有效的手机号!');
			$("#phone").val('');
			$("#phone").focus();
		}else if($("#captcha").val() == ''){
			showBox('请输入动态码!');
			$("#captcha").val('');
			$("#captcha").focus();
		}else{
			$.ajax({
				type: "post",
				url: APPMAIN+"/Login/sendSmsRegisterCode",                     //判断验证码
				data: {"vertification":$("#captcha").val(),"mobile" :$("#phone").val()},
				success: function(data){
					if(data.code == '0'){
						settime(obj,num);
					}else{
						if(data.code == '2'){              //动态码有错
							showBox(data.message);
							$(".verifyimgs").attr("src",'/captcha.html'); //'{:captcha_src()}'
						}else{                 //后台返回的报错
							showBox(data.message);
							return false;
						}
					}
				}
			});
		}
	}
}
function settime(obj,num) {     //发送验证码倒计时
	if(num == 1){
		if(countdown == 0) {
			obj.attr('disabled',false);
			//obj.removeattr("disabled");
			obj.html("发送验证码");
			countdown = 60;
			$("#btns").css('background','');
			return;
		}else {
			obj.attr('disabled',true);
			obj.html("重新发送(" + countdown + ")");
			countdown--;
			$("#btns").css('background','#c5c5c5');
			setTimeout(function() {
				settime(obj)
			},1000);
		}
	}else{
		if(countdown == 0) {
			obj.attr('disabled',false);
			//obj.removeattr("disabled");
			obj.html("发送验证码");
			countdown = 60;
			$("#btn").css('background','');
			return;
		}else {
			obj.attr('disabled',true);
			obj.html("重新发送(" + countdown + ")");
			countdown--;
			$("#btn").css('background','#c5c5c5');
			setTimeout(function() {
				settime(obj)
			},1000);
		}
	}
}
function publicBusi(){
	var arrt = ['****5668十分钟前抢的***礼包一份*','****2818十分钟前成为Jplus会员','****4342十分钟前抢的***礼包一份','***6762五分钟前成为Jplus会员','****9658一小时前抢购礼包一份','****5968二分钟前参与团购','****7451三分钟前注册成功','****1405五分钟前邀请好友成为会员获得新人券','****4546十分钟前通过线下商户积分兑付成功']
	var index = Math.floor((Math.random()*arrt.length));
	$('.myscroll').html(arrt[index]).toggle();
}
setInterval(publicBusi,5000);
function getNow(s) {
	return s < 10 ? '0' + s: s;
}

//导航滑动
var swiper1 = new Swiper('.swiper-container-nav', {
	slidesPerView: 4,
	spaceBetween: 10,
	pagination: {
		el: '.swiper-pagination',
		clickable: true,
	},
});
var page_index = 1;
var page_size = 6;
var p = 1;
$(document).ready(function(){
	var k = 0;
	category($('ul .categoryList').eq(0).attr('val'));
	$('.swiper-container-nav>.swiper-wrapper>.swiper-slide>a').click(function(){
		$('.swiper-container-nav>.swiper-wrapper>.swiper-slide>a').removeClass("tab");
		$(this).addClass("tab");
		var id =  $(this).attr('data-category-id');
		var no = '-1';
		category(id);
		page_index = 1;
		page_size = 10;
	});
	$('.category_list a').click(function(){
		var id =  $(this).attr('data-category-id');
		category(id);
		$('.category_list').hide();
	});
	$('#arrow').click(function(){
		if($('.category_list').is(':hidden')){
			$('.category_list').show();
		} else{
			$('.category_list').hide();
		}
	})
});
function category(id){
	var category_html = '';
//	 	if(k == '-1'){
//	 		$('.nav_menu ul').html('');
//	 	}
	$.ajax({
		type:'post',
		url:APPMAIN+'/Goods/goodsList',
		data:{'category_id':id,'page_index':1,'page_size':12},
		success:function(msg){
			if(msg.data.length>0){
				p = 1;
				if(page_index > 0){
					for(var i = 0;i<msg.data.length;i++){
						if(msg.data[i].state == '1'){
							category_html+='<li class="col-sm-4">'
								+'<a href="'+APPMAIN+'/goods/goodsdetail?id='+msg.data[i].goods_id+'">'
								+'<img src="'+UPLOAD+'/'+msg.data[i].pic_cover_small+'" />'
								+'<div class="product_name">'+msg.data[i].goods_name+'</div>'
								+'<div class="J_price">￥'+msg.data[i].price+'</div>'
								+'<div class="price cf">'
								+'<img src="'+J_IMG+'/images/jplus.png" width="23px" height="11px"/>'
								+'<span>￥'+msg.data[i].market_price+'</span>'
								+'</div>'
								+'<!--<div class="between">火拼</div>-->'
								+'</a>'
								+'</li>';
						}
					}
//	        			$(window).scroll(function(){
//					        var scrollTop = $(this).scrollTop();
//					        var scrollHeight = $(document).height();
//					        var windowHeight = $(this).height();
//					        if (scrollTop + windowHeight == scrollHeight) {
//	        					category(id,k);
//					        }
//					        if(scrollTop > 1000){
//					        	$('#scrollTop').addClass('J_fixed').css('width','89%');
//					        	$('#arrow').addClass('J_fixed');
//					        }else{
//					        	$('#scrollTop').removeClass('J_fixed').css('width','');
//					        	$('#arrow').removeClass('J_fixed');
//					        }
//					    });
				}
//      			console.log(page_index);
			}else{
//      			category($('ul .categoryList').eq(k+1).attr('val'),k+1);
//      			$('ul .categoryList a').removeClass("tab");
//      			$('ul .categoryList').eq(k+1).find('a').addClass("tab");
//      			page_index = 1;
//  				page_size = 10;
//      			if($('ul .categoryList').eq(k+1).attr('val') == undefined){
//      				$(window).unbind ('scroll');
//      				p = 0;
//      			}
				category_html+='<li style="text-align:center;height: 130px;">'
					+'<img src="'+J_IMG+'/images/commend-type.png" style="max-width: 80px;vertical-align: middle;margin: 10px 0 2px 0;"/>'
					+'<div style="text-align:center;color:#666;margin-top: 10px;">还没有商品哦</div>'
					+'</li>';
			}
			$('#goodsLists ul').html(category_html);
		}
	})
//      if(p == 0){
//      	page_index = -1
//      }else{
//      }
	page_index +=1;
}
//关注微信公众号弹出
$("#subscribe").click(function(){
	$(".shade").show();
	$(".popup").show();
})
//关注微信公众号关闭
$("#close").click(function(){
	$(".shade").hide();
	$(".popup").hide();

})
$(function(){
	//shop(7)
	$('.delicious_food').click(function(){
		var dropDown = $('.drop-down');
		if(dropDown.is(':hidden')){
			dropDown.show();
		}else{
			dropDown.hide();
		}
	});
	$('.drop-down li').click(function(){
		$('.theme').html($(this).html());
		shop($(this).attr('id'));
	})
	$('.popularity').click(function(){
		var dropDown = $('.drop-down2');
		if(dropDown.is(':hidden')){
			dropDown.show();
		}else{
			dropDown.hide();
		}
	});
	$('.drop-down2 li').click(function(){
		$('.theme2').html($(this).html());
	})
	//qq_position();

})
function shop(id){
	var html ='';
	$.ajax({
		type:"post",
		url : APPMAIN+"/index/shopStreet",
		data:{'shop_group_id':id},
		success : function(msg){
			console.log(msg);
			if(msg.data.length>0){
				for(var i=0;i<msg.data.length;i++){
					if(msg.data[i].shop_type != '1'){
						html+='<div class="column">'
							+'<div class="J_column cf">'
							+'<div class="fl J_columnImg"><img src="'+UPLOAD+'/'+msg.data[i].shop_logo+'" width="100%"/></div>'
							+'<div class="fr cf J_lomjug">'
							+'<div class="fl J_kimls">'
							+'<a href="/wap/shop/index?shop_id='+msg.data[i].shop_id+'">'
							+'<span class="tile_j">'+msg.data[i].shop_name+'</span>&nbsp;&nbsp;'
							+'<span class="ki_ji">'+msg.data[i].grou_name+'</span>'
						if(msg.data[i].shop_id == '55'){
							html+='<div class="consumption consumptionImg">会员尊享<font color="#ff000">9.5</font>折</div>'
						}else if(msg.data[i].shop_id == '57'){
							html+='<div class="consumption consumptionImg">会员尊享<font color="#ff000">9.0</font>折</div>'
						}else{
							html+='<div class="consumption consumptionImg">会员尊享<font color="#ff000">8.5</font>折</div>'
						}
						if(msg.data[i].shop_group_id == '7' || msg.data[i].shop_group_id == '9' || msg.data[i].shop_group_id == '11' || msg.data[i].shop_group_id == '12'){
							if(msg.data[i].shop_id == '43' || msg.data[i].shop_id == '46'){
								html+='<div class="consumption">消费金额的<font>15%</font>可用于会员返利，一积分约可分红<font>6</font>元</div>'
							}else{
								html+='<div class="consumption">消费金额的<font>10%</font>可用于会员返利，一积分约可分红<font>6</font>元</div>'
							}
						}else{
							html+='<div class="consumption">消费得<font>40%</font>积分,每1积分可提现约<font>6</font>元</div>'
						}
						html+='<div class="li_hlok">'+msg.data[i].live_store_address+'</div>'
							+'</div>'
							+'</a>'
							+'<div class="fr J_kimls2">'
							+'<a href="https://uri.amap.com/marker?position='+msg.data[i].latitude_longitude+'&name='+msg.data[i].shop_name+'" class="latitude" data-let= '+msg.data[i].latitude_longitude+'><img src="'+J_IMG+'/images/icon/daohang.png" style="width:44px"/></a>'
							+'<div class="juli" style="margin-top:0">582m</div>'
							+'</div>'
							+'</div>'
							+'</div>'
							+'<div class="column_quan cf">';
						if(msg.data[i].promotion_discount.length>0){
							var k =  msg.data[i].promotion_discount.length;
							if(k>2){             //首页只显示两张优惠券
								k=2;
							}
							for(var j=0;j<k;j++){
								html+='<div class="voucher" onclick="coupon_receive(this,'+msg.data[i].promotion_discount[j].coupon_type_id+')">'
									+'<a href="javascript:void(0);" class="cf">'
									+'<span class="fl money">￥<font>'+msg.data[i].promotion_discount[j].money+'</font></span>'
									+'<span class="fr msju">'
									+'<div class="full">满'+msg.data[i].promotion_discount[j].at_least+'元可用</div>'
									+'<div class="date">'+msg.data[i].promotion_discount[j].end_time.split(" ")[0]+'前使用</div>'
									+'</span>'
									+'</a>'
									+'<div class="receive">已领取</div>'
									+'</div>';
							}
						}
						html+='</div>'
							+'</div>'
					}
				}
			}else{
				html+='<div class="nothing" align="center" style="box-shadow: 0 6px 10px rgba(0,0,0,.1);"><img src="/template/wap/default/public/images/wap_nodata.png" width="100px"><div>没有找到您想要的商铺…</div></div>'
			}
			$('#column').html(html);
			latitude();
		}
	})
}
var couponObj = new Array();
function coupon_receive(event,coupon_type_id){
	var is_have = true;
	$.each(couponObj, function(key, val) {
		if(val == coupon_type_id){
			is_have = false;
		}
	});
	if(is_have){
		couponObj.push(coupon_type_id);
		$.ajax({
			type:"post",
			url : APPMAIN+"/index/getCoupon",
			async: false,
			dataType:"json",
			data:{
				'coupon_type_id' : coupon_type_id
			},
			success : function(data){
				if(data['code']>0){
					$(event).children('.receive').show();
				}else{
					$(event).children('.receive').show().text(data['message']);
				}
			}
		})
	}
}
//建立构造函数
/*function qq_position() {
	var geolocation = new qq.maps.Geolocation("GRIBZ-ERNK4-LRLUF-DTLZW-AFU6T-WIFVV", "myapp");
	if (geolocation) {
        geolocation.getLocation(success, error);
    } else {
        alert("失败");
    }

}*/
//获取当前的定位信息成功
/*function success(position) {
	var text = position.city;
	$('#lat').val(position.lat);
	$('#lng').val(position.lng);
    var city = text.replace (/(.*)市.*!/, '$1');
	document.getElementById("botn").innerHTML=city;
};
//获取当前的定位信息失败
function error() {
   document.getElementById("botn").innerHTML="失败";
};*/
function latitude(){
	var len = $('body .latitude').length;
	var userLat = $('#lat').val();
	var userLng = $('#lng').val();
	for(var i = 0;i < len; i++){
		var num = $('body .latitude').eq(i).attr('data-let');
		var lng = num.split(",")[0];
		var lat = num.split(",")[1];
		caculateLL(userLat, userLng, lat, lng,i)
	};
}
function caculateLL(lat1, lng1, lat2, lng2,k) {
	if(lat1 != '' && lat2 != undefined){
		var radLat1 = lat1 * Math.PI / 180.0;
		var radLat2 = lat2 * Math.PI / 180.0;
		var a = radLat1 - radLat2;
		var b = lng1 * Math.PI / 180.0 - lng2 * Math.PI / 180.0;
		var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(a / 2), 2) + Math.cos(radLat1) * Math.cos(radLat2) * Math.pow(Math.sin(b / 2), 2)));
		s = s * 6378.137;
		s = Math.round(s * 10000) / 10000;
		$('.juli').eq(k).html(s.toFixed(1) + 'km');
		return s
	}else{
		$('.juli').eq(k).html(0 + 'km');
	}
};
$('document').ready(function() {
	updateEndTime();
	//积分红包js部分
	$(".bonusClose").click(function(){
		$('.bonusPoints').hide();
	})
	$('.receive').click(function(){
		$.ajax({
			type:'post',
			url:APPMAIN+'/index/getSharePoint',
			success:function(msg){
				if(msg == 0){                  //msg等于0是未领取，等于1是已经领取过
					location.href = APPMAIN;
					return;
				}
				if(msg == -1){
					window.location.href = APPMAIN+'/login/index'
				}
			}
		})
	})
});
//倒计时函数
function updateEndTime() {
	var date = new Date();
	var time = date.getTime(); //当前时间距1970年1月1日之间的毫秒数

	$(".settime").each(function(i) {
		var endDate = this.getAttribute("endTime"); //结束时间字符串

		//转换为时间日期类型
		var endDate1 = eval('new Date(' + endDate.replace(/\d+(?=-[^-]+$)/, function(a) {
			return parseInt(a, 10) - 1;
		}).match(/\d+/g) + ')');

		var endTime = endDate1.getTime(); //结束时间毫秒数

		var lag = ((endTime - time) / 1000);        //当前时间和结束时间之间的秒数
		if (lag > 0) {
			var second = Math.floor(lag % 60);
			var minite = Math.floor((lag / 60) % 60);
			var hour = Math.floor(lag / 3600);    //没有将小时转换成天
			//var hour = Math.floor((lag / 3600) % 24);    //多少个小时
			//var day = Math.floor((lag / 3600) * 24);   //多少天
			var hour = hour<10?"0"+hour:hour;
			var minite = minite<10?"0"+minite:minite;
			var second = second<10?"0"+second:second;
			$('.js-hour').html(hour);
			$('.js-min').html(minite);
			$('.js-sec').html(second);
		} else{
			$(this).html("活动已经结束啦！");
		}
	});
	setTimeout("updateEndTime()", 1000);
}
