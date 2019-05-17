<?php
/**
 * Order.php
 * 积分呗系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.积兑.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */
namespace app\wap\controller;

use data\service\Express;
use data\service\promotion\GoodsExpress as GoodsExpressService;
use data\service\Goods;
use data\service\Member;
use data\service\Member as MemberService;
use data\service\Order as OrderService;
use data\service\Order\Order as OrderOrderService;
use data\service\promotion\GoodsMansong;
use data\model\NsCartModel;
use data\model\NsGoodsModel;
use data\model\AlbumPictureModel;
use data\service\Config;
use data\service\Promotion;
use data\service\promotion\GoodsPreference;
use data\service\Shop;
use think\Db;
use data\model\NsPointConfigModel;
use app\SimpleController;
use data\service\Events;
use data\model\NsMemberModel;
use data\model\NsCouponTypeModel;
use data\service\Goods as GoodsService;
/**
 * 订单控制器
 *
 * @author Administrator
 *        
 */
class Order extends SimpleController
{
    /**
     * 加入购物车
     */
    public function addShoppingCartSession()
    {
        // 加入购物车
        $cart_list = isset($_SESSION["cart_list"]) ? $_SESSION["cart_list"] : ""; // 用户所选择的商品
        $cart_id = implode(",", $cart_list);
        if ($cart_id == "") {
            $this->redirect(__URL__); // 没有商品返回到首页
        }
        
        $cart_id_arr = explode(",", $cart_id);
        $goods = new Goods();
        $cart_list = $goods->getCartList($cart_id);
        if (count($cart_list) == 0) {
           $this->redirect(__URL__); // 没有商品返回到首页
        }
        $list = Array();
        $str_cart_id = ""; // 购物车id
        $goods_sku_list = ''; // 商品skuid集合
        for ($i = 0; $i < count($cart_list); $i ++) {
            //if ($cart_id_arr[$i] == $cart_list[$i]["cart_id"]) {}  原来的有不对
            $list[] = $cart_list[$i];
            $str_cart_id .= "," . $cart_list[$i]["cart_id"];
            $goods_sku_list .= "," . $cart_list[$i]['sku_id'] . ':' . $cart_list[$i]['num'];
        }
        $goods_sku_list = substr($goods_sku_list, 1); // 商品sku列表
        $res["list"] = $list;
        $res["goods_sku_list"] = $goods_sku_list;
        return $res;
    }
    /**
     * 商品评价
     */
    public function addGoodsEvaluate()
    {
        $order = new OrderService();
        $order_id = intval($_POST['order_id']);
        $order_no = intval($_POST['order_no']);
        $goodsEvaluateArray = json_decode($_POST['goodsEvaluate']);
        $dataArr = array();
        foreach ($goodsEvaluateArray as $key => $goodsEvaluate) {
            $orderGoods = $order->getOrderGoodsInfo($goodsEvaluate->order_goods_id);
            $data = array(
                
                'order_id' => $order_id,
                'order_no' => $order_no,
                'order_goods_id' => intval($goodsEvaluate->order_goods_id),
                
                'goods_id' => $orderGoods['goods_id'],
                'goods_name' => $orderGoods['goods_name'],
                'goods_price' => $orderGoods['goods_money'],
                'goods_image' => $orderGoods['goods_picture'],
                'shop_id' => $orderGoods['shop_id'],
                'shop_name' => "默认",
                'content' => $goodsEvaluate->content,
                'addtime' => date("Y-m-d H:i:s", time()),
                'image' => $goodsEvaluate->imgs,
                // 'explain_first' => $goodsEvaluate->explain_first,
                'member_name' => $this->user->getMemberDetail()['member_name'],
                'explain_type' => $goodsEvaluate->explain_type,
                'uid' => $this->uid,
                'is_anonymous' => $goodsEvaluate->is_anonymous,
                'scores' => intval($goodsEvaluate->scores)
            );
            $dataArr[] = $data;
        }
        
        return $order->addGoodsEvaluate($dataArr, $order_id);
    }
	/**
     * 
     * 商品-追加评价
     * 
     */
    public function addGoodsEvaluateAgain()
    {
        $order = new OrderService();
        $order_id = intval($_POST['order_id']);
        $order_no = intval($_POST['order_no']);
        $goodsEvaluateArray = json_decode($_POST['goodsEvaluate']);
        
        $result = 1;
        foreach ($goodsEvaluateArray as $key => $goodsEvaluate) {
            $res = $order->addGoodsEvaluateAgain($goodsEvaluate->content, $goodsEvaluate->imgs, $goodsEvaluate->order_goods_id);
            if ($res == false) {
                $result = false;
                break;
            }
        }
        if ($result == 1) {
            $data = array(
                'is_evaluate' => 2
            );
            $result = $order->modifyOrderInfo($data, $order_id);
        }
        
        return $result;
    }
    /**
     * 立即购买
     */
    public function buyNowSession()
    {  
        $order_sku_list = isset($_SESSION["order_sku_list"]) ? $_SESSION["order_sku_list"] : "";
        if (empty($order_sku_list)) {
           $this->redirect(__URL__); // 没有商品返回到首页
        }
        
        $cart_list = array();
        $order_sku_list = explode(":", $_SESSION["order_sku_list"]);
        $sku_id = $order_sku_list[0];
        $num = $order_sku_list[1];
        
        // 获取商品sku信息
        $goods_sku = new \data\model\NsGoodsSkuModel();
        $sku_info = $goods_sku->getInfo([
            'sku_id' => $sku_id
        ], '*');
        
        // 清除非法错误数据
        $cart = new NsCartModel();
        if (empty($sku_info)) {
            $cart->destroy([
                'buyer_id' => $this->uid,
                'sku_id' => $sku_id
            ]);
           $this->redirect(__URL__); // 没有商品返回到首页
        }
        $goods = new NsGoodsModel();
        $goods_info = $goods->getInfo([
            'goods_id' => $sku_info["goods_id"]
        ], 'max_buy,state,point_exchange_type,point_exchange,picture,goods_id,goods_name, group_id_array,zero_num,zero_point_num');
        
        $cart_list["stock"] = $sku_info['stock']; // 库存
        $cart_list["sku_id"] = $sku_info["sku_id"];
        $cart_list["sku_name"] = $sku_info["sku_name"];
        /*
        $goods_preference = new GoodsPreference();
        $member_price = $goods_preference->getGoodsSkuMemberPrice($sku_info['sku_id'], $this->uid);
        //$cart_list["price"] = $member_price < $sku_info['promote_price'] ? $member_price : $sku_info['promote_price'];暂不用
        //价格适应可用最低价格
		$cart_list["price"] = $sku_info['price'];
		//将销售价修改为JPlus价格
		$member = NsMemberModel::get($this->uid);
		if($member['jplus_level'] > 0){
			if($cart_list["price"] > $sku_info['market_price']){$cart_list["price"] = $sku_info['market_price'];}
		}  
		//判断有无有效的折扣价格，此处只折扣无团购
		$goodsDiscount=new GoodsDiscount();
		$discount=$goodsDiscount->getDiscountByGoodsid($sku_info['goods_id']);//是否存在
		if($discount > 0){
			$discount_price=$sku_info['price']*($discount/10);
			if($cart_list["price"] > $discount_price){$cart_list["price"] = $discount_price;}
		}*/
		$goodsService=new GoodsService();
		$price_arr=$goodsService->getLowestPrice($this->uid,$sku_info["sku_id"],$sku_info["price"],$num);
		$cart_list["price"] = $price_arr['price'];
        $cart_list["goods_id"] = $goods_info["goods_id"];
        $cart_list["goods_name"] = $goods_info["goods_name"];
        $cart_list["max_buy"] = $goods_info['max_buy']; // 限购数量
        $cart_list['point_exchange_type'] = $goods_info['point_exchange_type']; // 积分兑换类型 0 非积分兑换 1 只能积分兑换
        $cart_list['point_exchange'] = $goods_info['point_exchange']; // 积分兑换
        $cart_list['group_id_array'] = $goods_info['group_id_array']; // 积分兑换
		$cart_list['zero_num'] = $goods_info['zero_num']; // 0元兑换
		$cart_list['zero_point_num'] = $goods_info['zero_point_num']; // 0元兑换
        if ($goods_info['state'] != 1) {
           $this->redirect(__URL__); // 没有商品返回到首页// 商品状态 0下架，1正常，10违规（禁售）
        }
        $num = $price_arr['num'];
        // 如果购买的数量超过限购，则取限购数量
        if ($goods_info['max_buy'] != 0 && $goods_info['max_buy'] < $num) {
            $num = $goods_info['max_buy'];
        }
        // 如果购买的数量超过库存，则取库存数量
        if ($sku_info['stock'] < $num) {
            $num = $sku_info['stock'];
        }
		$cart_list["num"] = $num;
        // 获取图片信息
        $picture = new AlbumPictureModel();
        $picture_info = $picture->get($goods_info['picture']);
        $cart_list['picture_info'] = $picture_info;
        
        if (count($cart_list) == 0) {
           $this->redirect(__URL__); // 没有商品返回到首页
        }
        $list[] = $cart_list;
        $goods_sku_list = $sku_id . ":" . $num; // 商品skuid集合
        $res["list"] = $list;
        $res["goods_sku_list"] = $goods_sku_list;
        return $res;
    }

    /**
     * 订单数据存session
     *
     * @return number
     */
    public function orderCreateSession()
    {
        $tag = isset($_POST['tag']) ? $_POST['tag'] : '';
        if (empty($tag)) {
            return - 1;
        }
        if ($tag == 'cart') {
            $_SESSION['order_tag'] = 'cart';
            $_SESSION['cart_list'] = $_POST['cart_id'];
        }
        if ($tag == 'buy_now') {
            $_SESSION['order_tag'] = 'buy_now';
            $_SESSION['order_sku_list'] = $_POST['sku_id'] . ':' . $_POST['num'];
        }
        return 1;
    }
    /**
     * 获取当前会员的订单列表
     */
    public function myOrderList()
    {
		$orderService = new OrderService();
		$orderService->orderFinish();//完成线下扫描支付订单，预提记录加入
		$orderService->exchangePoint();//未扣除通兑积分的扣除通兑积分
		$orderService->excPointAfter();//通兑积分抵扣10%金额选择抵扣后后续处理
		$events=new Events();
		$events->ordersComplete();//完成时预提记录加入
        $status =  'all';
        if (request()->isAjax()) {
            $status = isset($_POST['status']) ? $_POST['status'] : 'all';
            $page_index = isset($_POST['page_index']) ? $_POST['page_index'] : 1;
            $page_size = isset($_POST['page_size']) ? $_POST['page_size'] : 0;

            if (! empty($this->shop_id)) {
                $condition['shop_id'] = $this->shop_id;
            }

            $condition['buyer_id'] = $this->uid;
            if ($status != 'all') {
                switch ($status) {
                    case 0:
                        $condition['order_status'] = 0;
                        break;
                    case 1:
                        $condition['order_status'] = 1;
                        break;
                    case 2:
                        $condition['order_status'] = 2;
                        break;
                    case 3:
                        $condition['order_status'] = 3;
                        break;
                    default:
                        break;
                }
            }
            // 还要考虑状态逻辑
            
            $order = new OrderService();
            $orderList = $order->getOrderList($page_index, $page_size, $condition, 'create_time desc');
			foreach($orderList['data'] as $key=>$val){
				foreach($val['order_item_list'] as $k=>$v){
					if($v['point_exchange_type'] == 1) {
						$onegoods=Db::table("ns_goods")->where("goods_id",$v['goods_id'])->field('point_exchange')->find();
						$orderList['data'][$key]['order_item_list'][$k]['point_exchange']=$onegoods['point_exchange'];
					} else if($v['point_exchange_type'] == 2){
						$price=Db::table("ns_goods")->where("goods_id",$v['goods_id'])->value('price');
						$convert_rate = Db::table("ns_point_config")->where("shop_id",0)->value('convert_rate');
						$orderList['data'][$key]['order_item_list'][$k]['point_exchange']=round($price/$convert_rate,2);
					}
					/*通兑积分的价格转换通兑积分*/
					if(strpos($v['memo'],"新人专享")!==false){
						$zero_point_num=Db::table("ns_goods")->where("goods_id",$v['goods_id'])->value('zero_point_num');
						$orderList['data'][$key]['order_item_list'][$k]['price']=$v['price']*$zero_point_num;
					}
					/*通兑积分的价格转换结束*/
				}
			}
            return $orderList['data'];
        } else {
            $this->assign("status", $status);
			//$this->style需重新统一定义
            return view('wap/default/Order/myOrderList');
        }
    }

    /**
     * 订单详情
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function orderDetail()
    {
        $order_id = isset($_GET['orderId']) ? $_GET['orderId'] : 0;
        if ($order_id == 0) {
            $this->error("没有获取到订单信息");
        }
        $order_service = new OrderService();
        $detail = $order_service->getOrderDetail($order_id);
        
        if (empty($detail)) {
            $this->error("没有获取到订单信息");
        }
        
        $count = 0; // 计算包裹数量（不包括无需物流）
        $express_count = count($detail['goods_packet_list']);
        $express_name = "";
        $express_code = "";
        if ($express_count) {
            foreach ($detail['goods_packet_list'] as $v) {
                if ($v['is_express']) {
                    $count ++;
                    if (! $express_name) {
                        $express_name = $v['express_name'];
                        $express_code = $v['express_code'];
                    }
                }
            }
            $this->assign('express_name', $express_name);
            $this->assign('express_code', $express_code);
        }
        $this->assign('express_count', $express_count);
        $this->assign('is_show_express_code', $count); // 是否显示运单号（无需物流不显示）

        $this->assign("order", $detail);
        $this->assign("order_remain_time",  3600 - (time() - strtotime($detail['create_time'])));
        $days = floor((7 * 24 * 60 * 60 -(time() - strtotime($detail['pay_time'])))/60/60/24);
        $hours = ceil((7 * 24 * 60 * 60 -(time() - strtotime($detail['pay_time'])))/60/60 - $days * 24);
        if($days <=0){
            $days = 0;
            $hours = 0;
        }
        $this->assign("confirm_remain_time",  $days . '天'.$hours.'小时');
        return view($this->style . '/Order/orderDetail');
    }

    /**
     * 物流详情页
     */
    public function orderExpress()
    {
        $order_id = isset($_GET['orderId']) ? $_GET['orderId'] : 0;
        if ($order_id == 0) {
            $this->error("没有获取到订单信息");
        }
        $order_service = new OrderService();
        $detail = $order_service->getOrderDetail($order_id);
        if (empty($detail)) {
            $this->error("没有获取到订单信息");
        }
        // 获取物流跟踪信息
        $order_service = new OrderService();
        $this->assign("order", $detail);
        return view($this->style . '/Order/orderExpress');
    }
    /*
     * 
     * 399会员赠品专区
     * 
     */
	public function giftArea(){
		return view($this->style . '/Order/gift_area1');
	}
    /**
     * 查询包裹物流信息
     * 2017年6月24日 10:42:34 王永杰
     */
    public function getOrderGoodsExpressMessage()
    {
        $express_id = request()->post("express_id", 0); // 物流包裹id
        $res = - 1;
        if ($express_id) {
            $order_service = new OrderService();
            $res = $order_service->getOrderGoodsExpressMessage($express_id);
        }
        return $res;
    }

    /**
     * 订单项退款详情
     */
    public function refundDetail()
    {
        $order_goods_id = isset($_GET['order_goods_id']) ? $_GET['order_goods_id'] : 0;
        if ($order_goods_id == 0) {
            $this->error("没有获取到退款信息");
        }
        $order_service = new OrderService();
        $detail = $order_service->getOrderGoodsRefundInfo($order_goods_id);
        $this->assign("order_refund", $detail);
        $refund_money = $order_service->orderGoodsRefundMoney($order_goods_id);
        $this->assign('refund_money', $refund_money);
        $this->assign("detail", $detail);
        // 查询店铺默认物流地址
        $express = new Express();
        $address = $express->getDefaultShopExpressAddress($this->instance_id);
        $this->assign("address_info", $address);
        return view($this->style . '/Order/refundDetail');
    }

    /**
     * 申请退款
     */
    public function orderGoodsRefundAskfor()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : 0;
        $refund_type = isset($_POST['refund_type']) ? $_POST['refund_type'] : 1;
        $refund_require_money = isset($_POST['refund_require_money']) ? $_POST['refund_require_money'] : 0;
        $refund_reason = isset($_POST['refund_reason']) ? $_POST['refund_reason'] : '';
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        return AjaxReturn($retval);
    }

    /**
     * 买家退货
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function orderGoodsRefundExpress()
    {
        $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
        $order_goods_id = isset($_POST['order_goods_id']) ? $_POST['order_goods_id'] : 0;
        $refund_express_company = isset($_POST['refund_express_company']) ? $_POST['refund_express_company'] : '';
        $refund_shipping_no = isset($_POST['refund_shipping_no']) ? $_POST['refund_shipping_no'] : 0;
        $refund_reason = isset($_POST['refund_reason']) ? $_POST['refund_reason'] : '';
        $order_service = new OrderService();
        $retval = $order_service->orderGoodsReturnGoods($order_id, $order_goods_id, $refund_express_company, $refund_shipping_no);
        return AjaxReturn($retval);
    }

    /**
     * 交易关闭
     */
    public function orderClose()
    {
        $order_service = new OrderService();
        $order_id = $_POST['order_id'];
        $res = $order_service->orderClose($order_id);
        return AjaxReturn($res);
    }

    /**
     * 交易删除
     */
    public function orderDelete()
    {
//        $order_service = new OrderService();
//        $order_id = $_POST['order_id'];
//        $res = $order_service->orderDelete($order_id);
//        return AjaxReturn($res);
        return AjaxReturn(0);
    }

    /**
     * 订单后期支付页面
     */
    public function orderPay()
    {
        $order_id = isset($_GET['id']) ? $_GET['id'] : 0;
        $out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : 0;
        $order_service = new OrderService();
        if ($order_id != 0) {
            // 更新支付流水号
            $new_out_trade_no = $order_service->getOrderNewOutTradeNo($order_id);
            $url = 'http://' . $_SERVER['HTTP_HOST'] . \think\Request::instance()->root() . '/wap/pay/getpayvalue?out_trade_no=' . $new_out_trade_no;
            header("Location: " . $url);
            exit();
        } else {
            // 待结算订单处理
            if ($out_trade_no != 0) {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . \think\Request::instance()->root() . '/wap/pay/getpayvalue?out_trade_no=' . $out_trade_no;
                exit();
            } else {
                $this->error("没有获取到支付信息");
            }
        }
    }

    /**
     *
     * 会员中心
     *
     * */
    public function memberCenter()
    {
//        $user_info = $this->user->getUserDetail();
//        $this->assign('user_info', $user_info);
//        $is_member = $this->user->getSessionUserIsMember();
        $uid = 0;
        $member_info = '';
        $member_info['balance'] = 0;
        if (!empty($this->uid)) {
            $uid = $this->uid;
        }
        if($uid == 0){
            $this->assign('uid', $uid);
            $this->assign('member_info', $member_info);
            return view($this->style . '/Member/notMemberCenter');
        }
        $member = new MemberService;
        $member_info = $member->getMemberDetail();
        $addresslist = $member->getMemberExpressAddressList();

        $this->assign('uid', $uid);
        $this->assign('member_info', $member_info);
        $this->assign('member_address', $addresslist);
        $this->assign('member_address_count', count($addresslist['data']));
//        if ($member_info['assign_jplus_time'] >= strtotime("-1 years",date("Y-m-d", time()))) {
//            return view($this->style . '/Member/memberCenter');
//        }


        if (!empty($this->uid)) {
            if ($member_info['jplus_level'] > 0) {
                return view($this->style . '/Member/memberCenter');
            }
        }
        return view($this->style . '/Member/notMemberCenter');
    }

    /**
     * 收货
     */
    public function orderTakeDelivery()
    {
        $order_service = new OrderService();
        $order_id = $_POST['order_id'];
        $res = $order_service->OrderTakeDelivery($order_id);
        return AjaxReturn($res);
    }
}