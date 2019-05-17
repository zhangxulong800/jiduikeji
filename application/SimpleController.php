<?php
/**
 * BaseController.php
 * 积分呗系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.积兑.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace app;

use data\extend\WchatOauth;
use data\service\Goods as GoodsService;
use data\service\Member as Member;
use data\service\Order as OrderService;
use data\service\Shop;
use data\service\Address;
use data\service\Config;
use data\service\User;
use data\service\WebSite as WebSite;
use think\Controller;
use think\Session;
use data\model\NsPointConfigModel;
use data\model\NsOrderGoodsViewModel;
use data\service\Express;
use data\service\promotion\GoodsExpress as GoodsExpressService;
use data\service\Goods;
use data\service\Member as MemberService;
use data\service\promotion\GoodsMansong;
use data\model\NsCartModel;
use data\model\NsGoodsModel;
use data\model\AlbumPictureModel;
use data\service\Promotion;
use data\service\promotion\GoodsPreference;
use think\Db;

use data\service\Member\MemberAccount as MemberAccount;
use data\service\UnifyPay;
use data\service\Order\OrderGoods;

class SimpleController extends BaseController
{
    public $user;

    protected $uid;

    protected $instance_id;

    protected $is_member;

    protected $shop_name;

    protected $user_name;

    protected $shop_id;

    /**
     * 平台logo
     *
     * @var unknown
     */
    protected $logo;

    public $web_site;

    public $style;
    public $app_login_name;

    public $app_login_password;

    public function orderCreate()
    {
        $order = new OrderService();
        $member = new Member();
        $use_coupon = request()->post('use_coupon', 0); // 优惠券;字符串形式
        $integral = request()->post('integral', 0); // 积分
        $goods_sku_list = request()->post('goods_sku_list', ''); // 商品列表
        $leavemessage = request()->post('leavemessage', 'org\Filter::safeHtml'); // 留言
        $user_money = request()->post("account_balance", 0); // 使用余额
        $pay_type = request()->post("pay_type", 1); // 支付方式
        $buyer_invoice = request()->post("buyer_invoice", ""); // 发票
        $pick_up_id = request()->post("pick_up_id", 0); // 自提点 字符串形式代替数组
        $express_company_id = request()->post("express_company_id", 0); // 快递物流公司;字符串形式
        $shipping_time = date("Y-m-d H::i:s", time());
        $address = $member->getDefaultExpressAddress();
		//对用户购物车商品按店铺分类，然后生成不同的订单！
        $goods_sku_arr=explode(',',$goods_sku_list);
        $cart_shop_arr=array();
        foreach($goods_sku_arr as $key=>$val){
            $once=explode(':',$val);
            $shopid=Db::table('ns_cart')->where(['buyer_id'=>$this->uid,'sku_id'=>$once[0]])->value('shop_id');
            if(empty($cart_shop_arr[$shopid])){$cart_shop_arr[$shopid]=$val;}
            else {$cart_shop_arr[$shopid].=','.$val;}

            $goods_service = new Goods();
            $goods_info = $goods_service->getGoodsDetail($goods_service->getGoodsId($once[0]));
            if(!empty($goods_info)){
                if(($goods_info['category_id'] == 370) || ($goods_info['category_id'] == 371)){
                    if((new NsOrderGoodsViewModel())->getOrderGoodsViewCount(
                        ['ng.category_id'=>$goods_info['category_id'],
                            'no.create_time'=>
                        ['>', date('Y-m-d', strtotime('-60 days', time()))],
                            'no.pay_status'=>2,
                            'no.refund_money'=>0,
                                'no.buyer_id'=>$this->uid
                        ]) > 0){
                        return AjaxReturn(-1, '会员礼品【'.$goods_info['goods_name'].'】只能领取1件！');
                    }
                }
            }
        }

        $out_trade_no = $order->getOrderTradeNo();//获取支付编号
		
		//循环支付生成订单。商品价格积分设置及积分支付方式决定point字段是价格内的
        foreach($cart_shop_arr as $key=>$val){
			$shipping_type = 1; // 配送方式，1：快递物流，2：自提
			$one_pick_up=$order->getShopOne($pick_up_id,$key);
			if (!empty($one_pick_up)) {
				$shipping_type = 2;
			}
			$one_coupon=$order->getShopOne($use_coupon,$key);
			$one_express=$order->getShopOne($express_company_id,$key);
            $retval=$order->orderUpCreate('1',$out_trade_no,$pay_type,$shipping_type, '1', 1,
                $leavemessage,$buyer_invoice,$shipping_time,$address['mobile'],$address['province'],
                $address['city'],$address['district'],$address['address'],$address['zip_code'],$address['consigner'],
                $integral,$one_coupon,0,$val,$user_money,$one_pick_up,$one_express);
			
			$user_money=bcsub($user_money,$retval[1],2);
            if ($retval[0] > 0) {
                $order->deleteCart($val, $this->uid);
                $_SESSION['order_tag'] = "";
            } else {
                return AjaxReturn($retval[0]);break;
            }
        }
        return AjaxReturn($out_trade_no);
    }
    /**
     * 购物车
     * @return \think\response\View
     */
    public function cart()
    {
        $goods = new GoodsService();
		$cf = new NsPointConfigModel();
        $cart_list = $goods->getCart($this->uid);
        foreach($cart_list as $key=>$val){
            if($val['point_exchange_type']==2){
                $point_info = $cf->getInfo(['shop_id' => 0],'convert_rate');//$val['shop_id']改成0号店铺
                if($point_info['convert_rate']>0){
                    $cart_list[$key]['point_exchange']=round($val['price']/$point_info['convert_rate'],2);
                    $cart_list[$key]['price']=0.00;
                }
            }
        }
		// 店铺，店铺中的商品
        $list = Array();
        for ($i = 0; $i < count($cart_list); $i ++) {
            $list[$cart_list[$i]["shop_id"] . ',' . $cart_list[$i]["shop_name"]][] = $cart_list[$i];
        }
		//print_r($this->tokenState);exit;
		if($this->tokenState){  //isset判断是否api提交；true要判断口令正确；打印$this->tokenState知道口令状态
			return $this->outMessage($list);
		} else {
			$this->assign("list", $list);
			return view($this->style . 'Goods/cart');
		}
    }

    /**
     * 待付款订单需要的数据；后面加参数tags=exc区分兑换显示
     */
    public function paymentOrder()
    {
        $member = new MemberService();
        $order = new OrderService();
        $goods_mansong = new GoodsMansong();
        $Config = new Config();
        $promotion = new Promotion();
        $shop_service = new Shop();
        $goods_express_service = new GoodsExpressService();
        $order_goods_preference = new GoodsPreference();
        $cf = new NsPointConfigModel();
        $isMobile=request()->isMobile();
        $order_tag = isset($_SESSION['order_tag']) ? $_SESSION['order_tag'] : "";
        if (empty($order_tag)) {
            if($isMobile){
                $this->redirect('index/index'); // 没有商品返回到首页
            } else {
                $this->redirect(__URL__ . '/index'); // 没有商品返回到首页
            }
        }
        $this->assign("order_tag", $order_tag); // 标识：立即购买还是购物车中进来的

        switch ($order_tag) {
				// 立即购买
            case "buy_now":
                $res = $this->buyNowSession(); //分别对应PC及手机端的，未合并
                $goods_sku_list = $res["goods_sku_list"];
                $list = $res["list"];
                break;
            case "cart":
                // 加入购物车
                $res = $this->addShoppingCartSession();
                $goods_sku_list = $res["goods_sku_list"];
                $list = $res["list"];
                break;
        }
        $goods_sku_list = trim($goods_sku_list);
        if (empty($goods_sku_list)) {
            $this->error("待支付订单中商品不可为空");
        }
        $this->assign('goods_sku_list', $goods_sku_list); // 商品sku列表
        if(!$isMobile){
            $addresslist = $member->getMemberExpressAddressList(1, 0, '', ' is_default DESC'); // 地址查询
            if (empty($addresslist["data"])) {
                $this->assign("address_list", 0);
            } else {
                $this->assign("address_list", $addresslist["data"]); // 选择收货地址
            }
        }
        $address = $member->getDefaultExpressAddress(); // 查询默认收货地址

        $count_money = $order->getGoodsSkuListPrice($goods_sku_list); // 商品金额
        $this->assign("count_money", sprintf("%.2f", $count_money)); // 商品金额
        $count_point_exchange = 0;
        $new_list=array();
        foreach ($list as $k => $v) {
            $list[$k]['price'] = sprintf("%.2f", $list[$k]['price']);
			if(!empty($list[$k]['zero_num'])){
				$zeroArr=explode('/',$list[$k]['zero_num']);
				$countnum=Db::table('ns_order_goods')->where(['goods_id'=>$list[$k]['goods_id'],'memo'=>'2019已兑换新人专享！'])->sum('num');
                $uidNum = Db::table('ns_order_goods')->where(['goods_id'=>$list[$k]['goods_id'],'memo'=>'2019已兑换新人专享！','order_status'=>1,'buyer_id'=>$this->uid])->sum('num');
				if($zeroArr[1]>$countnum && $uidNum==0 && $list[$k]['goods_id']!=1303){
                    $list[$k]['new_freight']=0;
				} else {
				    $list[$k]['new_freight']=$zeroArr[0]*$list[$k]['num'];
				}
			}
            $list[$k]['subtotal'] = sprintf("%.2f", $list[$k]['price'] * $list[$k]['num']);
            $point_info = $cf->getInfo(['shop_id' => 0],'convert_rate');//0替换$v["shop_id"]
            if ($v["point_exchange_type"] == 1) {
                if ($v["point_exchange"] > 0) {
                    $count_point_exchange += $v["point_exchange"] * $v["num"];
                }
            } elseif ($v["point_exchange_type"] == 2 && $point_info['convert_rate']>0) {
                $point_exchange=round($v["price"] * $v["num"]/$point_info['convert_rate'],2);
                $list[$k]['point_exchange']=$point_exchange;
                $count_point_exchange += $point_exchange;
                $list[$k]['price'] = sprintf("%.2f", 0);
                $list[$k]['subtotal'] = sprintf("%.2f", 0);
            }
			
            if(empty($new_list[$v["shop_id"]])){
				$new_list[$v["shop_id"]]['shop_sku_list']=$v["sku_id"].":".$v["num"];
                $new_list[$v["shop_id"]]['shop_name']=\think\Db::table("ns_shop")->where("shop_id",$v["shop_id"])->value("shop_name");
            } else {
				$new_list[$v["shop_id"]]['shop_sku_list'].=",".$v["sku_id"].":".$v["num"];
            }
			$new_list[$v["shop_id"]]['goods_list'][]=$list[$k];
        }
		$tot_express = 0;
		$tot_discount = 0;
		foreach ($new_list as $key => $val) {
			$coupon_list = $order->getMemberCouponList($val['shop_sku_list']); // 获取优惠券
			$discount_money = $goods_mansong->getGoodsMansongMoney($val['shop_sku_list']); // 计算满减金额
			foreach ($coupon_list as $k => $v) {
				$coupon_list[$k]['start_time'] = substr($v['start_time'], 0, stripos($v['start_time'], " ") + 1);
				$coupon_list[$k]['end_time'] = substr($v['end_time'], 0, stripos($v['end_time'], " ") + 1);
			}
			$new_list[$key]['coupon_list']= $coupon_list ;
			$new_list[$key]['discount_money']= sprintf("%.2f", $discount_money);
			$tot_discount+=$discount_money;
			$express = 0;
			$express_company_list = array();
			if (! empty($address)) {
				//$shop_id = $order_goods_preference->getGoodsSkuListShop($val['shop_sku_list']);
				// 物流公司
				$express_company_list = $goods_express_service->getExpressCompany($key,$val['shop_sku_list'], $address['province'], $address['city']);
				if (! empty($express_company_list)) {
					foreach ($express_company_list as $v) {
						$express = $v['express_fee']; // 取第一个运费，初始化加载运费
						$tot_express+=$express;
						break;
					}
				}
			}

			$new_list[$key]['express']= sprintf("%.2f", $express);// 运费
			$new_list[$key]['express_company_list']= $express_company_list;// 物流公司
			//if($isMobile){ $count = $goods_express_service->getExpressCompanyCount($this->instance_id); }
			
			$promotion_full_mail = $promotion->getPromotionFullMail($key);
			$new_list[$key]['promotion_full_mail']= $promotion_full_mail; // 满额包邮

			$pickup_point_list = $shop_service->getPickupPointList(1,0,['shop_id' =>$key]);
			$new_list[$key]['pickup_point_list']= $pickup_point_list; // 自提地址列表
		}
		//店铺配置信息开始
		$shop_config = $Config->getShopConfig(0);
		$order_invoice_content = explode(",", $shop_config['order_invoice_content']);
		$shop_config['order_invoice_content_list'] = array();
		foreach ($order_invoice_content as $v) {
			if (! empty($v)) {
				array_push($shop_config['order_invoice_content_list'], $v);
			}
		}
		$this->assign("shop_config", $shop_config);
		//店铺配置信息结束
		//print_r($new_list);exit;
        $this->assign("new_list", $new_list); // 格式化后的列表
		$this->assign("tot_express", $tot_express);
		$this->assign("tot_discount", $tot_discount);
        $this->assign("count_point_exchange", $count_point_exchange); // 总积分

        $member_account = $member->getMemberAccount($this->uid,0); // 用户余额
        if ($member_account['balance'] == '' || $member_account['balance'] == 0) {
            $member_account['balance'] = '0.00';
        }
        $this->assign("member_account", $member_account); // 用户余额、积分
		$this->assign("tag",$_GET['tag']);//首单包邮全免特定标签
		if($isMobile){
            $this->assign("address_default", $address);
            $this->assign("express_company_list", $express_company_list);
			if(empty($_GET['tags'])){ //0元兑换专用
				return view($this->style . '/Order/paymentOrder');
			} else {
				if(!empty($this->uid)){
					$exc_point=Db::table('ns_member_account')->where(['uid'=>$this->uid])->value('exc_point');
					$this->assign("exc_point", $exc_point);
				}
				return view($this->style . '/Order/excpaymentOrder');
			}
		} else {
			return view($this->style . 'Member/paymentOrder');
		}
    }
	//订单余额、积分支付
	public function payorder()
    {
		if (request()->isAjax()) {
			$pay_type = request()->post("pay_type", 0);
			$out_trade_no = request()->post("out_trade_no", 0);
			$usemoney = request()->post("usemoney", 0);
			//$usepoint = request()->post("usepoint", 0);
			$member = new MemberService();
			$member_account = $member->getMemberAccount($this->uid,0); // 用户余额、积分
			//改变订单order及order_goods及其它表的状态
			if($pay_type==5 || $pay_type==6 || $pay_type==7){
				$OrderChan=new OrderService();
				$res=$OrderChan->orderchange($pay_type,$out_trade_no,$member_account,$usemoney);
			} else {$res=array('code'=>0,'message'=>'支付类型不正确！');}
			
			return $res;
		}
	}
    /**
     * 余额提现
     */
    public function pointGiftToPoint()
    {
        if (request()->isAjax()) {
            // 提现
            $member_account = new MemberAccount();
            $retval = $member_account->addPointGiftToPoint();
            return AjaxReturn($retval);
        }
    }
}