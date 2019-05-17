<?php
/**
 * Shop.php
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
namespace app\wap\controller;

use data\service\NfxUser;
use data\service\Goods as GoodsService;
use data\service\Member as MemberService;
use data\service\Shop as ShopService;
use data\service\Weixin;
use data\service\User;
use data\service\Config;
use data\service\Platform;
use think\Session;
use data\service\UnifyPay;
use data\model\NsOrderPaymentModel;
use think\Db;
use data\service\Order\Order as InOrder;
use data\model\UserModel as UserModel;
use data\model\NsOrderModel;
use data\model\NsOrderGoodsModel;
use data\service\Promotion;
/**
 * 店铺控制器
 *
 * @author Administrator
 *        
 */
class Shop extends BaseController
{
    /**
     * 店铺主页
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function index()
    {
        $this->assign("is_subscribe", 0);
        $shop = new ShopService();
        $shop_info = $shop->getShopInfo($this->shop_id);
        $member = new MemberService();
        $goods = new GoodsService();
        $source_user_name = "";
        $shop_ad_list = $shop->getShopAdList(1, 0, [
            'type' => 1
        ]);
        $this->assign('source_user_name', $source_user_name);
        $this->assign('shop_ad_list', $shop_ad_list["data"]);
        if (null == $shop_info) {
            $this->redirect(__URL__.'/wap'); // 没有商品返回到首页
        }
        $this->assign("shop_info", $shop_info);
        $config = new Config();
        $notice_arr = $config->getNotice($this->shop_id);
        $this->assign('notice', $notice_arr);
        // 是否收藏店铺
        $is_member_fav_shop = $member->getIsMemberFavorites($this->uid, $this->shop_id, 'shop');
        $this->assign("is_member_fav_shop", $is_member_fav_shop);
//         // 新品推荐,推荐专区,热销专区
//         $title_arr = [
//             "新品推荐",
//             "推荐专区",
//             "热销专区"
//         ];
//         $conditions = array(
//             [
//                 'ng.shop_id' => $this->shop_id,
//                 'is_new' => 1
//             ],
//             [
//                 'ng.shop_id' => $this->shop_id,
//                 'is_recommend' => 1
//             ],
//             [
//                 'ng.shop_id' => $this->shop_id,
//                 'is_hot' => 1
//             ]
//         );
        $shop_id = request()->get('shop_id','');
        $Platform = new Platform();
        $recommend_block = $Platform->getshopPlatformGoodsRecommendClass($shop_id);
        foreach($recommend_block as $k=>$v){
            //获取模块下商品
            $goods_list = $Platform->getPlatformGoodsRecommend($v['class_id']);
            if(empty($goods_list)){
                unset($recommend_block[$k]);
            }
        }
//         $goods_list = null;
//         foreach ($conditions as $key => $item) {
//             $temp_list = $goods->getGoodsList(1, 14, $item, 'sort');
//             $goods_list["list"][$key] = $temp_list["data"];
//             $goods_list["nav"][$key]["count"] = count($temp_list["data"]);
//             $goods_list["nav"][$key]["title"] = $title_arr[$key];
//         }
        $ticket = $this->getShareTicket();
        $this->assign('is_shop_member', 1);
        $this->assign("signPackage", $ticket);
        $this->assign("recommend_block", $recommend_block);
        $is_subscribe = 0; // 标识：是否显示顶部关注 0：[隐藏]，1：[显示]
                           // 检查是否配置过微信公众号
      
        $this->assign("is_subscribe", $is_subscribe);

        $discount = new Promotion();
        $condition['end_time'] = array(
            ">",
            date('Y-m-d', time())
        );
        $condition['start_time'] = array(
            "<=",
            date('Y-m-d', time())
        );
        $condition['is_show'] = 1;
        $condition['shop_id'] = $shop_id;

        $discount_info = $discount->getCouponTypeList(1, 3, $condition, '');
//        var_dump($discount_info['data'][0]['end_time']);
        $this->assign("discount_info", $discount_info);

        $components = new Components();
        $goods_list = $components->getTypeGoodsList($shop_id, 0);
//        var_dump($goods_list);
        $this->assign("goods_sheet", $goods_list);
//		print_r($goods_list);
//		exit;
        return view($this->style . '/Shop/index');
    }

    /**
     * 获取推荐类型商品列表
     */
    public function goodsList()
    {
        if (request()->isAjax()) {
            $shop_id = isset($_POST['shop_id']) ? $_POST['shop_id'] : 0;
            $page_index = isset($_POST['page_index']) ? $_POST['page_index'] : 1;
            $page_size = isset($_POST['page_size']) ? $_POST['page_size'] : 4;
            $condition = [
                'ng.shop_id' => $shop_id
            ];
            $goods = new GoodsService();
            $goods_list = $goods->getGoodsList($page_index, $page_size, $condition, $order = 'ng.create_time desc');
            return $goods_list['data'];
        }
        else{
            $shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : 0;
            $page_size = isset($_GET['page_size']) ? $_GET['page_size'] : 4;
            $this->assign("search_title", '全部商品');

            $condition = [
                'ng.shop_id' => $shop_id
            ];
            $goods = new GoodsService();
            $goods_list = $goods->getGoodsList(1, $page_size, $condition, $order = 'ng.create_time desc');

            $this->assign("goods_list", $goods_list);
            return view($this->style . '/Shop/goodsList');
        }
    }

    /**
     * 获取推荐类型商品列表前3榜单
     */
    public function goodsListTop3()
    {
        $shop_id = isset($_POST['shop_id']) ? $_POST['shop_id'] : 1;
        $top = isset($_POST['top']) ? $_POST['top'] : 3;
        $type = isset($_POST['type']) ? $_POST['type'] : 0;
        $this->assign("search_title", '全部商品');
        $components = new Components();
        $goods_list = $components->getTypeGoodsList($shop_id, $type, $top);
        return $goods_list;
    }

    /**
     * 关注店铺
     */
    public function userAssociateShop()
    {
        if (empty($this->uid)) {
            return - 1;
        } else {
            $nfx_user = new NfxUser();
            $shop_id = isset($_POST['shop_id']) ? $_POST['shop_id'] : '';
            $session_id = 0;
            if (! empty($_SESSION["source_shop_id"])) {
                if ($shop_id == $_SESSION["source_shop_id"]) {
                    $session_id = $_SESSION["source_uid"];
                }
            }
            $retval = $nfx_user->userAssociateShop($this->uid, $shop_id, $session_id);
            return AjaxReturn($retval);
        }
    }
   /**
     * 扫描商铺二维码显示的页面
     */
    public function oneself()
    {
		$shopId = isset($_GET['shopId']) ? $_GET['shopId'] : 0;
        $uid = $this->user->getSessionUid();
        if (empty($uid)) {
            $loginstate=0;
        } else {
			$loginstate=1;
			$user = new User();
			$user_info = $user->getUserInfoByUid($uid);
			
			$memberinfo = Db::table('ns_member_account')->where(['uid'=>$uid])->find();
			$this->assign("shopId", $shopId);
			$this->assign("user_info", $user_info);
			$this->assign("memberinfo", $memberinfo);
		}
		$this->assign("loginstate", $loginstate);
		return view($this->style . '/shop/oneself');
	}
	/**
     * 扫描商铺二维码，线下支付，扣除积分余额等
     */
    public function alonepay()
    {
		$shopId = isset($_POST['shopId']) ? $_POST['shopId'] : 0;
		$oneshop = Db::table('ns_shop')->where(['shop_id'=>$shopId])->field('shop_name,shop_state,service_charge_rate')->find();
		if($oneshop['shop_state']==0){ return array('code'=>0,'message'=>'店铺已关闭！');}
		$pay = new UnifyPay();
		$out_trade_no = $pay->createOutTradeNo();
		$pay_money = isset($_POST['pay_money']) ? $_POST['pay_money'] : 0;
		$pay_type= isset($_POST['pay_type']) ? $_POST['pay_type'] : 0;
		$payment = new NsOrderPaymentModel();
		$InOrder=new InOrder();
        $order_no=$InOrder->createOrderNo($shopId);
		// 订单来源
		if (isWeixin()) {
			$order_from = 1; // 微信
		} elseif (request()->isMobile()) {
			$order_from = 2; // 手机
		} else {
			$order_from = 3; // 电脑
		}
		$buyer = new UserModel();
		$buyer_info = $buyer->getInfo([
			'uid' => $this->uid
		], 'nick_name');
		$member = new MemberService();
		$address = $member->getDefaultExpressAddress();
		$data_order = array(
			'order_no' =>$order_no,
			'out_trade_no' => $out_trade_no,
			'payment_type' => $pay_type,
			'order_from' => $order_from,
			'buyer_id' => $this->uid,
			'buyer_message' => '[shop_scan_pay]',//依据该标示进入个人中心就操作“完成”订单
			'user_name' => $buyer_info['nick_name'],
			'shipping_time' => date("Y-m-d H:i:s", time()),
			'receiver_mobile' => $address['mobile'],
			'receiver_province' => $address['province'],
			'receiver_city' => $address['city'],
			'receiver_district' => $address['district'],
			'receiver_address' => $address['address'],
			'receiver_zip' => $address['zip_code'],
			'receiver_name' =>$address['consigner'],
			'shop_id' => $shopId,
			'shop_name' =>$oneshop['shop_name'],
			'goods_money' => $pay_money,
			'order_money' => $pay_money,
			'pay_money' => $pay_money,
			'order_status' => 0,
			'pay_status' => 0,
			'shipping_status' => 0,
			'create_time' => date("Y-m-d H:i:s", time()),
		);
		if ($pay_money <= 0) {
			$data_order['pay_time'] = date("Y-m-d H:i:s", time());$data_order['pay_status'] = 2;
		}
		$order = new NsOrderModel();
		$newId=$order->save($data_order);
		//生成OrderPayment支付订单
		$data = array(
			'shop_id'       => $shopId,
			'out_trade_no'  => $out_trade_no,
			'type'          => 1,  //当做一般的商城订单
			'type_alis_id'=>$newId,
			'pay_body'      => '线下支付',
			'pay_detail'    => '线下新建订单支付',
			'pay_money'     => $pay_money,
			'create_time'   => date("Y-m-d H:i:s", time())
		);
		if($pay_money <= 0){$data['pay_status']=1;}
		$res = $payment->save($data);
		// 添加order_goods订单项
		$data_goods = array(
		'order_id' => $newId,
		'goods_name' =>'线下店铺扫描支付虚拟商品',
		'price' => $pay_money,
		'num' => 1,
		'cost_price' =>round($pay_money*(100-$oneshop['service_charge_rate'])/100,2),
		'goods_money' =>$pay_money,
		'shop_id' => $shopId,
		'buyer_id' => $this->uid,
		'goods_type' => 0,//虚拟商品
		'order_type' => 1, // 订单类型默认1
		'order_status' => 0,
		'memo' => '[shop_scan_pay]',
		);
		$order_goods = new NsOrderGoodsModel();
		$order_goods->save($data_goods);
		//-------------------------------------------
		$InOrder->addOrderAction($newId, $this->uid, '创建订单');//添加订单action
		if($res && $newId){
			return array('code'=>1,'message'=>'支付订单生成成功','out_trade_no'=>$out_trade_no);
		} else {return array('code'=>0,'message'=>'支付订单生成失败');}
	   //只生成订单
	}		
}
