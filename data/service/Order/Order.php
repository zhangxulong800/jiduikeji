<?php
/**
 * OrderAccount.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\service\Order;

use data\model\NsGoodsModel;
use data\model\NsMemberAccountModel;
use data\model\NsMemberAccountRecordsModel;
use data\model\NsOrderActionModel;
use data\service\Order\OrderStatus;
use data\model\NsOrderModel;
use data\model\UserModel as UserModel;
use data\model\NsOrderGoodsModel;
use data\service\Member\MemberAccount;
use data\service\Member\MemberCoupon;
use data\model\NsGoodsSkuModel;
use data\model\NsOrderGoodsExpressModel;
use data\service\BaseService;
use data\service\promotion\GoodsExpress;
use data\service\promotion\GoodsPreference;
use data\model\AlbumPictureModel;
use data\service\promotion\GoodsMansong;
use data\model\NsOrderPromotionDetailsModel;
use data\model\NsOrderGoodsPromotionDetailsModel;
use data\model\NsPromotionMansongRuleModel;
use data\service\Shop;
use think\Model;
use think\Db;
use data\service\UnifyPay;
use data\service\User;
use data\service\WebSite;
use data\model\NsPromotionFullMailModel;
use think\Log;
use data\model\NsMemberModel;
use data\service\Address;
use data\service\Config;
use data\model\NsPickupPointModel;
use data\model\NsOrderPickupModel;
use data\model\ConfigModel;
use data\service\Member;
use data\model\NsShopModel;
use data\service\shopaccount\ShopAccount;

/**
 * 订单操作类
 */
class Order extends BaseService
{

    public $order;
    // 订单主表
    function __construct()
    {
        parent::__construct();
        $this->order = new NsOrderModel();
    }

    /**
     * 订单创建
     */
    public function orderInCreate($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $coin)
    {	
        $this->order->startTrans();
        try {
            // 设定不使用会员余额支付
            $user_money = 0;
            // 查询商品对应的店铺ID
            $order_goods_preference = new GoodsPreference();
            $shop_id = $order_goods_preference->getGoodsSkuListShop($goods_sku_list);
            //平台版查询店铺信息
            $shop_model = new NsShopModel();
            $shop_info = $shop_model->getInfo(['shop_id' => $shop_id], 'shop_name');
            $shop_name = $shop_info['shop_name'];
            // 获取优惠券金额
            $coupon = new MemberCoupon();
            $coupon_money = $coupon->getCouponMoney($coupon_id);
            
            // 获取购买人信息
            $buyer = new UserModel();
            $buyer_info = $buyer->getInfo([
                'uid' => $this->uid
            ], 'nick_name');
			
            // 获取订单邮费,订单自提免除运费
            if ($shipping_type == 1) {
                $order_goods_express = new GoodsExpress();
                $deliver_price = $order_goods_express->getSkuListExpressFee($goods_sku_list, $shipping_company_id, $receiver_province, $receiver_city);
				
				if ($deliver_price < 0) {
                    $this->order->rollback();
					$retval[0]=$deliver_price;
                    return $retval;
                }
            } else {
                $deliver_price = 0;
            }
            
            // 订单商品费用
            
            $goods_money = $order_goods_preference->getGoodsSkuListPrice($goods_sku_list);
            $point = $order_goods_preference->getGoodsListExchangePoint($goods_sku_list);
            // 积分兑换抵用金额
            $account_flow = new MemberAccount();
            /*
             * $point_money = $order_goods_preference->getPointMoney($point, $shop_id);
             */
            $point_money = 0;
            // 订单来源
            if (isWeixin()) {
                $order_from = 1; // 微信
            } elseif (request()->isMobile()) {
                $order_from = 2; // 手机
            } else {
                $order_from = 3; // 电脑
            }
            // 订单支付方式
            
            // 订单待支付
            $order_status = 0;
            // 购买商品获取积分数
            $give_point = $order_goods_preference->getGoodsSkuListGivePoint($goods_sku_list);
            // 订单满减送活动优惠
            $goods_mansong = new GoodsMansong();
            $mansong_array = $goods_mansong->getGoodsSkuListMansong($goods_sku_list);
            $promotion_money = 0;
            $mansong_rule_array = array();
            $mansong_discount_array = array();
            if (! empty($mansong_array)) {
                foreach ($mansong_array as $k_mansong => $v_mansong) {
                    foreach ($v_mansong['discount_detail'] as $k_rule => $v_rule) {
                        $rule = $v_rule[1];
                        $discount_money_detail = explode(':', $rule);
                        $mansong_discount_array[] = array(
                            $discount_money_detail[0],
                            $discount_money_detail[1],
                            $v_rule[0]['rule_id']
                        );
                        $promotion_money += $discount_money_detail[1]; // round($discount_money_detail[1],2);
                                                                       // 添加优惠活动信息
                        $mansong_rule_array[] = $v_rule[0];
                    }
                }
                $promotion_money = round($promotion_money, 2);
            }
            $full_mail_array = array();
            // 计算订单的满额包邮
            $full_mail_model = new NsPromotionFullMailModel();
            // 店铺的满额包邮
            $full_mail_obj = $full_mail_model->getInfo([
                "shop_id" => $shop_id
            ], "*");
            if (! empty($full_mail_obj)) {
                $is_open = $full_mail_obj["is_open"];
                $full_mail_money = $full_mail_obj["full_mail_money"];
                $order_real_money = $goods_money - $promotion_money - $coupon_money - $point_money;
                if ($is_open == 1 && $order_real_money >= $full_mail_money && $deliver_price > 0) {
                    // 符合满额包邮 邮费设置为0
                    $full_mail_array["promotion_id"] = $full_mail_obj["mail_id"];
                    $full_mail_array["promotion_type"] = 'MANEBAOYOU';
                    $full_mail_array["promotion_name"] = '满额包邮';
                    $full_mail_array["promotion_condition"] = '满' . $full_mail_money . '元,包邮!';
                    $full_mail_array["discount_money"] = $deliver_price;
                    $deliver_price = 0;
                }
            }
            
            // 订单费用(具体计算)
            $order_money = $goods_money + $deliver_price - $promotion_money - $coupon_money - $point_money;
            
            if ($order_money < 0) {
                $order_money = 0;
                $user_money = 0;
                $platform_money = 0;
            }
            
            if (! empty($buyer_invoice)) {
                // 添加税费
                $config = new Config();
                $tax_value = $config->getConfig(0, 'ORDER_INVOICE_TAX');
                if (empty($tax_value['value'])) {
                    $tax = 0;
                } else {
                    $tax = $tax_value['value'];
                }
                $tax_money = $order_money * $tax / 100;
            } else {
                $tax_money = 0;
            }
            $order_money = $order_money + $tax_money;
            
            if ($order_money < $platform_money) {
                $platform_money = $order_money;
            }
            
            $pay_money = $order_money - $user_money - $platform_money;
            if ($pay_money <= 0) {
                $pay_money = 0;
                $order_status = 1;
                $pay_status = 2;
            } else {
                $order_status = 0;
                $pay_status = 0;
            }
            // 余额支付方式
            if ($pay_money == 0 && $platform_money > 0) {
                $pay_type = 5;
            }
            // 积分返还类型
            $config = new ConfigModel();
            $config_info = $config->getInfo([
                "instance_id" => $shop_id,
                "key" => "SHOPPING_BACK_POINTS"
            ], "value");
            $give_point_type = $config_info["value"];
            
            // 店铺名称
            $order_no=$this->createOrderNo($shop_id);
            $data_order = array(
                'order_type' => $order_type,
                'order_no' =>$order_no,
                'out_trade_no' => $out_trade_no,
                'payment_type' => $pay_type,
                'shipping_type' => $shipping_type,
                'order_from' => $order_from,
                'buyer_id' => $this->uid,
                'user_name' => $buyer_info['nick_name'],
                'buyer_ip' => $buyer_ip,
                'buyer_message' => $buyer_message,
                'buyer_invoice' => $buyer_invoice,
                'shipping_time' => $shipping_time, // datetime NOT NULL COMMENT '买家要求配送时间',
                'receiver_mobile' => $receiver_mobile, // varchar(11) NOT NULL DEFAULT '' COMMENT '收货人的手机号码',
                'receiver_province' => $receiver_province, // int(11) NOT NULL COMMENT '收货人所在省',
                'receiver_city' => $receiver_city, // int(11) NOT NULL COMMENT '收货人所在城市',
                'receiver_district' => $receiver_district, // int(11) NOT NULL COMMENT '收货人所在街道',
                'receiver_address' => $receiver_address, // varchar(255) NOT NULL DEFAULT '' COMMENT '收货人详细地址',
                'receiver_zip' => $receiver_zip, // varchar(6) NOT NULL DEFAULT '' COMMENT '收货人邮编',
                'receiver_name' => $receiver_name, // varchar(50) NOT NULL DEFAULT '' COMMENT '收货人姓名',
                'shop_id' => $shop_id, // int(11) NOT NULL COMMENT '卖家店铺id',
                'shop_name' => $shop_name, // varchar(100) NOT NULL DEFAULT '' COMMENT '卖家店铺名称',
                'goods_money' => $goods_money, // decimal(19, 2) NOT NULL COMMENT '商品总价',
                'tax_money' => $tax_money, // 税费
                'order_money' => $order_money, // decimal(10, 2) NOT NULL COMMENT '订单总价',
                'point' => $point, // int(11) NOT NULL COMMENT '订单消耗积分',
                'point_money' => $point_money, // decimal(10, 2) NOT NULL COMMENT '订单消耗积分抵多少钱',
                'coupon_money' => $coupon_money, // _money decimal(10, 2) NOT NULL COMMENT '订单代金券支付金额',
                'coupon_id' => $coupon_id, // int(11) NOT NULL COMMENT '订单代金券id',
                'user_money' => $user_money, // decimal(10, 2) NOT NULL COMMENT '订单预存款支付金额',
                'promotion_money' => $promotion_money, // decimal(10, 2) NOT NULL COMMENT '订单优惠活动金额',
                'shipping_money' => $deliver_price, // decimal(10, 2) NOT NULL COMMENT '订单运费',
                'pay_money' => $pay_money, // decimal(10, 2) NOT NULL COMMENT '订单实付金额',
                'refund_money' => 0, // decimal(10, 2) NOT NULL COMMENT '订单退款金额',
                'give_point' => $give_point, // int(11) NOT NULL COMMENT '订单赠送积分',
                'order_status' => $order_status, // tinyint(4) NOT NULL COMMENT '订单状态',
                'pay_status' => $pay_status, // tinyint(4) NOT NULL COMMENT '订单付款状态',
                'shipping_status' => 0, // tinyint(4) NOT NULL COMMENT '订单配送状态',
                'review_status' => 0, // tinyint(4) NOT NULL COMMENT '订单评价状态',
                'feedback_status' => 0, // tinyint(4) NOT NULL COMMENT '订单维权状态',
                'user_platform_money' => $platform_money, // 平台余额支付
                'coin_money' => $coin,
                'create_time' => date("Y-m-d H:i:s", time()),
                "give_point_type" => $give_point_type,
                'shipping_company_id' => $shipping_company_id
            ); // datetime NOT NULL DEFAULT 'CURRENT_TIMESTAMP' COMMENT '订单创建时间',
            if ($pay_money <= 0) {
				$data_order['pay_time'] = date("Y-m-d H:i:s", time());
			}
			
            $order = new NsOrderModel();
            $order->save($data_order);
            $order_id = $order->order_id;
            $pay = new UnifyPay();
            $pay->createPayment($shop_id, $out_trade_no, $shop_name . "订单", $shop_name . "订单", $pay_money, 1, $order_id);
            // 如果是订单自提需要添加自提相关信息
            if ($shipping_type == 2) {
                if (! empty($pick_up_id)) {
                    $pickup_model = new NsPickupPointModel();
                    $pickup_point_info = $pickup_model->getInfo([
                        'id' => $pick_up_id
                    ], '*');
                    $order_pick_up_model = new NsOrderPickupModel();
                    $data_pickup = array(
                        'order_id' => $order_id,
                        'name' => $pickup_point_info['name'],
                        'address' => $pickup_point_info['address'],
                        'contact' => $pickup_point_info['address'],
                        'phone' => $pickup_point_info['phone'],
                        'city_id' => $pickup_point_info['city_id'],
                        'province_id' => $pickup_point_info['province_id'],
                        'district_id' => $pickup_point_info['district_id'],
                        'supplier_id' => $pickup_point_info['supplier_id'],
                        'longitude' => $pickup_point_info['longitude'],
                        'latitude' => $pickup_point_info['latitude'],
                        'create_time' => date("Y-m-d H:i:s", time())
                    );
                    $order_pick_up_model->save($data_pickup);
                }
            }
            // 满额包邮活动
            if (! empty($full_mail_array)) {
                $order_promotion_details = new NsOrderPromotionDetailsModel();
                $data_promotion_details = array(
                    'order_id' => $order_id,
                    'promotion_id' => $full_mail_array["promotion_id"],
                    'promotion_type_id' => 2,
                    'promotion_type' => $full_mail_array["promotion_type"],
                    'promotion_name' => $full_mail_array["promotion_name"],
                    'promotion_condition' => $full_mail_array["promotion_condition"],
                    'discount_money' => $full_mail_array["discount_money"],
                    'used_time' => date("Y-m-d H:i:s", time())
                );
                $order_promotion_details->save($data_promotion_details);
            }
            // 满减送详情，添加满减送活动优惠情况
            if (! empty($mansong_rule_array)) {
                
                $mansong_rule_array = array_unique($mansong_rule_array);
                foreach ($mansong_rule_array as $k_mansong_rule => $v_mansong_rule) {
                    $order_promotion_details = new NsOrderPromotionDetailsModel();
                    $data_promotion_details = array(
                        'order_id' => $order_id,
                        'promotion_id' => $v_mansong_rule['rule_id'],
                        'promotion_type_id' => 1,
                        'promotion_type' => 'MANJIAN',
                        'promotion_name' => '满减送活动',
                        'promotion_condition' => '满' . $v_mansong_rule['price'] . '元，减' . $v_mansong_rule['discount'],
                        'discount_money' => $v_mansong_rule['discount'],
                        'used_time' => date("Y-m-d H:i:s", time())
                    );
                    $order_promotion_details->save($data_promotion_details);
                }
                // 添加到对应商品项优惠满减
                if (! empty($mansong_discount_array)) {
                    foreach ($mansong_discount_array as $k => $v) {
                        $order_goods_promotion_details = new NsOrderGoodsPromotionDetailsModel();
                        $data_details = array(
                            'order_id' => $order_id,
                            'promotion_id' => $v[2],
                            'sku_id' => $v[0],
                            'promotion_type' => 'MANJIAN',
                            'discount_money' => $v[1],
                            'used_time' => date("Y-m-d H:i:s", time())
                        );
                        $order_goods_promotion_details->save($data_details);
                    }
                }
            }
            // 添加到对应商品项优惠优惠券使用详情
            if ($coupon_id > 0) {
                $coupon_details_array = $order_goods_preference->getGoodsCouponPromoteDetail($coupon_id, $coupon_money, $goods_sku_list);
                foreach ($coupon_details_array as $k => $v) {
                    $order_goods_promotion_details = new NsOrderGoodsPromotionDetailsModel();
                    $data_details = array(
                        'order_id' => $order_id,
                        'promotion_id' => $coupon_id,
                        'sku_id' => $v['sku_id'],
                        'promotion_type' => 'COUPON',
                        'discount_money' => $v['money'],
                        'used_time' => date("Y-m-d H:i:s", time())
                    );
                    $order_goods_promotion_details->save($data_details);
                }
            }
            // 使用积分
            if ($point > 0) {
                $retval_point = $account_flow->addMemberAccountData($shop_id, 1, $this->uid, 0, $point * (- 1), 1, $order_id, '商城订单');
                if ($retval_point < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_POINT;
                }
            }
            if ($coin > 0) {
                $retval_point = $account_flow->addMemberAccountData($shop_id, 3, $this->uid, 0, $coin * (- 1), 1, $order_id, '商城订单');
                if ($retval_point < 0) {
                    $this->order->rollback();
                    return LOW_COIN;
                }
            }
            if ($user_money > 0) {
                $retval_user_money = $account_flow->addMemberAccountData($shop_id, 2, $this->uid, 0, $user_money * (- 1), 1, $order_id, '商城订单');
                if ($retval_user_money < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_USER_MONEY;
                }
            }
            if ($platform_money > 0) {
                $retval_platform_money = $account_flow->addMemberAccountData(0, 2, $this->uid, 0, $platform_money * (- 1), 1, $order_id, '商城订单');
                if ($retval_platform_money < 0) {
                    $this->order->rollback();
                    return ORDER_CREATE_LOW_PLATFORM_MONEY;
                }
            }
            // 使用优惠券
            if ($coupon_id > 0) {
                $retval = $coupon->useCoupon($this->uid, $coupon_id, $order_id);
                if (! ($retval > 0)) {
                    $this->order->rollback();
                    return $retval;
                }
            }
            // 添加order_goods订单项
            $order_goods = new OrderGoods();
            $res_order_goods = $order_goods->addOrderGoods($order_id,$order_status,$goods_sku_list);
            if (! ($res_order_goods > 0)) {
                $this->order->rollback();
                return $res_order_goods;
            }
            $this->addOrderAction($order_id, $this->uid, '创建订单');
         
            $this->order->commit();
            return array($out_trade_no,$platform_money);
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单支付
     *
     * @param unknown $order_pay_no            
     * @param unknown $pay_type(10:线下支付)            
     * @param unknown $status
     *            0:订单支付完成 1：订单交易完成
     * @return Exception
     */
    public function OrderPay($order_pay_no, $pay_type, $status)
    {
        $this->order->startTrans();
        try {
            // 改变订单状态
            // 添加订单日志
			$order = new NsOrderModel();
            // 可能是多个订单
            $order_id_array = $this->order->where(['out_trade_no' =>$order_pay_no])->field('order_id,pay_status')->select();
            foreach ($order_id_array as $k => $v) {
				if($v['pay_status']==0){
					// 修改订单状态
					$data = array(
						'payment_type' => $pay_type,
						'pay_status' => 2,
						'pay_time' => date("Y-m-d H:i:s", time()),
						'order_status' => 1
					); // 订单转为待发货状态
					
					$order->save($data, ['order_id' => $v['order_id']]);
					//改变order_goods订单状态
					$OrderGoods=new NsOrderGoodsModel();
					$allGoods=$OrderGoods->where('order_id',$v['order_id'])->field('order_goods_id,order_status')->select();
					foreach($allGoods as $key=>$val){
						if($val['order_status']==0){$OrderGoods->where('order_goods_id',$val['order_goods_id'])->update(['order_status'=>1]);}
					}
					// 赠送赠品
					$uid = $this->order->getInfo([
						'order_id' => $v['order_id']
					], 'buyer_id,pay_money');
					if ($pay_type == 10) {
						// 线下支付
						$this->addOrderAction($v['order_id'], $this->uid, '线下支付');
					} else {
						// 查询订单购买人ID
						$this->addOrderAction($v['order_id'], $uid['buyer_id'], '订单支付');
					}
					// 增加会员累计消费.需要可以改为完成环节累加
					//$account = new MemberAccount();
					//$account->addMmemberConsum(0, $uid['buyer_id'], $uid['pay_money']);
					if ($status == 1) {
						$res = $this->lastComplete($v['order_id']);
						if (! ($res > 0)) {
							$this->order->rollback();
							return $res;
						}
						// 执行订单交易完成
					}
				}
            }
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 添加订单操作日志
     * order_id int(11) NOT NULL COMMENT '订单id',
     * action varchar(255) NOT NULL DEFAULT '' COMMENT '动作内容',
     * uid int(11) NOT NULL DEFAULT 0 COMMENT '操作人id',
     * user_name varchar(50) NOT NULL DEFAULT '' COMMENT '操作人',
     * order_status int(11) NOT NULL COMMENT '订单大状态',
     * order_status_text varchar(255) NOT NULL DEFAULT '' COMMENT '订单状态名称',
     * action_time datetime NOT NULL COMMENT '操作时间',
     * PRIMARY KEY (action_id)
     *
     * @param unknown $order_id            
     * @param unknown $uid            
     * @param unknown $action_text            
     */
    public function addOrderAction($order_id, $uid, $action_text)
    {
        $this->order->startTrans();
        try {
            $order_status = $this->order->getInfo([
                'order_id' => $order_id
            ], 'order_status');
            if ($uid != 0) {
                $user = new UserModel();
                $user_name = $user->getInfo([
                    'uid' => $uid
                ], 'nick_name');
                $action_name = $user_name['nick_name'];
            } else {
                $action_name = 'system';
            }
            
            $data_log = array(
                'order_id' => $order_id,
                'action' => $action_text,
                'uid' => $uid,
                'user_name' => $action_name,
                'order_status' => $order_status['order_status'],
                'order_status_text' => $this->getOrderStatusName($order_id),
                'action_time' => date("Y-m-d H:i:s", time())
            );
            $order_action = new NsOrderActionModel();
            $order_action->save($data_log);
            $this->order->commit();
            return $order_action->action_id;
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 获取订单当前状态 名称
     *
     * @param unknown $order_id            
     */
    public function getOrderStatusName($order_id)
    {
        $order_status = $this->order->getInfo([
            'order_id' => $order_id
        ], 'order_status');
        $status_array = OrderStatus::getOrderCommonStatus();
        foreach ($status_array as $k => $v) {
            if ($v['status_id'] == $order_status['order_status']) {
                return $v['status_name'];
            }
        }
        return false;
    }

    /**
     * 通过店铺id 得到订单的订单号
     *
     * @param unknown $shop_id            
     */
    public function createOrderNo($shop_id)
    {
        $time_str = date('YmdHis');
        $order_model = new NsOrderModel();
        $order_list = $order_model->getQuery([
            "shop_id" => $shop_id
        ], "order_no", "create_time DESC");
        $num = 0;
        if (! empty($order_list)) {
            $order_obj = $order_list[0];
            $order_no_max = $order_obj["order_no"];
            if (empty($order_no_max)) {
                $num = 1;
            } else {
                if (substr($time_str, 0, 8) == substr($order_no_max, 0, 8)) {
                    $max_no = substr($order_no_max, 14, 9);
                    $num = str_replace("0", "", $max_no) + 1;
                } else {
                    $num = 1;
                }
            }
        } else {
            $num = 1;
        }
        $order_no = $time_str.$shop_id.sprintf("%07d", $num);
        return $order_no;
    }

    /**
     * 创建订单支付编号
     *
     * @param unknown $order_id            
     */
    public function createOutTradeNo()
    {
        $pay_no = new UnifyPay();
        return $pay_no->createOutTradeNo();
    }

    /**
     * 订单重新生成订单号
     *
     * @param unknown $orderid            
     */
    public function createNewOutTradeNo($orderid)
    {
        $order = new NsOrderModel();
        $new_no = $this->createOutTradeNo();
        $data = array(
            'out_trade_no' => $new_no
        );
        $retval = $order->save($data, [
            'order_id' => $orderid
        ]);
        if ($retval) {
            return $new_no;
        } else {
            return '';
        }
    }

    /**
     * 订单发货(整体发货)(不考虑订单项)
     *
     * @param unknown $orderid            
     */
    public function orderDoDelivery($orderid)
    {
        $this->order->startTrans();
        try {
            $order_item = new NsOrderGoodsModel();
            $condition['order_id'] = $orderid;
			$condition['refund_status'] = array('<>', 5);
			$condition['shipping_status'] = 0;
            $count = $order_item->where($condition)->count();
            if ($count == 0) {
                $data_delivery = array(
                    'shipping_status' => 1,
                    'order_status' => 2,
                    'consign_time' => date("Y-m-d H:i:s", time())
                );
                $order_model = new NsOrderModel();
                $order_model->save($data_delivery, ['order_id' => $orderid]);
                $this->addOrderAction($orderid, $this->uid, '订单发货');
            }
            
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单收货
     *
     * @param unknown $orderid            
     */
    public function OrderTakeDelivery($orderid)
    {
        $this->order->startTrans();
        try {
            $data_take_delivery = array(
                'shipping_status' => 2,
                'order_status' => 3,
                'sign_time' => date("Y-m-d H:i:s", time())
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_take_delivery, [
                'order_id' => $orderid
            ]);
            $this->addOrderAction($orderid, $this->uid, '订单收货');
            // 判断是否需要在本阶段赠送积分
            $this->giveGoodsOrderPoint($orderid, 2);
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单自动收货
     *
     * @param unknown $orderid            
     */
    public function orderAutoDelivery($orderid)
    {
        $this->order->startTrans();
        try {
            $data_take_delivery = array(
                'shipping_status' => 2,
                'order_status' => 3,
                'sign_time' => date("Y-m-d H:i:s", time())
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_take_delivery, [
                'order_id' => $orderid
            ]);
            
            $this->addOrderAction($orderid, 0, '订单自动收货');
            // 判断是否需要在本阶段赠送积分
            $this->giveGoodsOrderPoint($orderid, 2);
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 执行订单交易完成
     *
     * @param unknown $orderid。orderComplete更名lastComplete
     */
    public function lastComplete($orderid)
    {
        $this->order->startTrans();
        try {
            $data_complete = array(
                'order_status' => 4,
                "finish_time" => date("Y-m-d H:i:s", time())
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_complete, ['order_id' => $orderid]);
            $this->addOrderAction($orderid, $this->uid, '交易完成');
			$this->addCommission($orderid); //执行预提记录加入账户
            $this->calculateOrderMansong($orderid);
            // 判断是否需要在本阶段赠送积分
            $this->giveGoodsOrderPoint($orderid, 1);
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 统计订单完成后赠送用户积分
     *
     * @param unknown $order_id            
     */
    private function calculateOrderGivePoint($order_id)
    {
        $point = $this->order->getInfo([
            'order_id' => $order_id
        ], 'shop_id, give_point,buyer_id');
        $member_account = new MemberAccount();
        $member_account->addMemberAccountData($point['shop_id'], 1, $point['buyer_id'], 1, $point['give_point'], 1, $order_id, '订单商品赠送积分');
    }

    /**
     * 订单完成后统计满减送赠送
     *
     * @param unknown $order_id            
     */
    private function calculateOrderMansong($order_id)
    {
        $order_info = $this->order->getInfo([
            'order_id' => $order_id
        ], 'shop_id, buyer_id');
        $order_promotion_details = new NsOrderPromotionDetailsModel();
        // 查询满减送活动规则
        $list = $order_promotion_details->getQuery([
            'order_id' => $order_id,
            'promotion_type_id' => 1
        ], '*', '');
        if (! empty($list)) {
            $promotion_mansong_rule = new NsPromotionMansongRuleModel();
            foreach ($list as $k => $v) {
                $mansong_data = $promotion_mansong_rule->getInfo([
                    'rule_id' => $v['promotion_id']
                ], '*');
                if (! empty($mansong_data)) {
                    // 满减送赠送积分
                    if ($mansong_data['give_point'] != 0) {
                        $member_account = new MemberAccount();
                        $member_account->addMemberAccountData($order_info['shop_id'], 1, $order_info['buyer_id'], 1, $mansong_data['give_point'], 1, $order_id, '订单满减送赠送积分');
                    }
                    // 满减送赠送优惠券
                    if ($mansong_data['give_coupon'] != 0) {
                        $member_coupon = new MemberCoupon();
                        $member_coupon->UserAchieveCoupon($order_info['buyer_id'], $mansong_data['give_coupon'], 1);
                    }
                }
            }
        }
    }

    /**
     * 订单执行交易关闭
     *
     * @param unknown $orderid            
     * @return Exception
     */
    public function orderClose($orderid)
    {
        $this->order->startTrans();
        try {
            $data_close = array(
                'order_status' => 5
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_close, [
                'order_id' => $orderid
            ]);
            $order_info = $this->order->getInfo([
                'order_id' => $orderid
            ], 'pay_status,point, coupon_id, user_money, buyer_id,shop_id,user_platform_money, coin_money');
            // 积分返还
            $account_flow = new MemberAccount();
            if ($order_info['point'] > 0) {
                $account_flow->addMemberAccountData($order_info['shop_id'], 1, $order_info['buyer_id'], 1, $order_info['point'], 2, $orderid, '订单关闭返还积分');
            }
            if ($order_info['coin_money'] > 0) {
                $coin_convert_rate = $account_flow->getCoinConvertRate();
                $account_flow->addMemberAccountData($order_info['shop_id'], 3, $order_info['buyer_id'], 1, $order_info['coin_money'] / $coin_convert_rate, 2, $orderid, '订单关闭返还购物币');
            }
            // 会员余额返还--暂未用到此项
            
            if ($order_info['user_money'] > 0) {
                $account_flow->addMemberAccountData($order_info['shop_id'], 2, $order_info['buyer_id'], 1, $order_info['user_money'], 2, $orderid, '订单关闭返还用户余额');
            }
            // 平台余额返还
            
            if ($order_info['user_platform_money'] > 0) {
                $account_flow->addMemberAccountData(0, 2, $order_info['buyer_id'], 1, $order_info['user_platform_money'], 2, $orderid, '商城订单关闭返还平台余额');
            }
            // 优惠券返还
            $coupon = new MemberCoupon();
            if ($order_info['coupon_id'] > 0) {
                $coupon->UserReturnCoupon($order_info['coupon_id']);
            }
            // 退回库存
            $order_goods = new NsOrderGoodsModel();
            $order_goods_list = $order_goods->getQuery([
                'order_id' => $orderid
            ], '*', '');
            foreach ($order_goods_list as $k => $v) {
                $return_stock = 0;
                $goods_sku_model = new NsGoodsSkuModel();
                $goods_sku_info = $goods_sku_model->getInfo([
                    'sku_id' => $v['sku_id']
                ], 'stock');
                if ($v['shipping_status'] != 1) {
                    // 卖家未发货
                    $return_stock = 1;
                } else {
                    // 卖家已发货,买家不退货
                    if ($v['refund_type'] == 1) {
                        $return_stock = 0;
                    } else {
                        $return_stock = 1;
                    }
                }
                // 退货返回库存
                if ($return_stock == 1) {
                    $data_goods_sku = array(
                        'stock' => $goods_sku_info['stock'] + $v['num']
                    );
                    $goods_sku_model->save($data_goods_sku, [
                        'sku_id' => $v['sku_id']
                    ]);
                }
            }
            
            $this->addOrderAction($orderid, $this->uid, '交易关闭');
            $this->order->commit();

            return 1;
        } catch (\Exception $e) {
            Log::write($e->getMessage());
            $this->order->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单执行删除
     *
     * @param unknown $orderid
     * @return Exception
     */
    public function orderDelete($orderid)
    {
        $order_info = $this->order->getInfo([
            'order_id' => $orderid
        ], 'order_status');
//            ], 'pay_status,point, coupon_id, user_money, buyer_id,shop_id,user_platform_money, coin_money, order_status');

        if($order_info['order_status'] != 5){
            return '订单交易未关闭，请先关闭后，再进行删除操作！';
        }

        Db::startTrans();
        try {

            Db::query('delete from ns_order where order_id = '.$orderid);
            Db::query('delete from ns_order_action where order_id = '.$orderid);
            Db::query('delete from ns_order_goods where order_id = '.$orderid);
            Db::query('delete from ns_order_goods_express where order_id = '.$orderid);
            //ns_order_payment未处理
            Db::query('delete from ns_order_goods_promotion_details where order_id = '.$orderid);
            Db::query('delete from ns_order_pickup where order_id = '.$orderid);
            Db::query('delete from ns_order_promotion_details where order_id = '.$orderid);
            //ns_order_refund未处理
            //ns_order_shipping_fee未处理
            //ns_order_shop_return未处理

            Db::commit();
            return 1;

        } catch (\Exception $e) {
            Log::write($e->getMessage());
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单状态变更
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    public function orderGoodsRefundFinish($order_id)
    {
        $orderInfo = NsOrderModel::get($order_id);
        $orderInfo->startTrans();
        try {
            
            $total_count = NsOrderGoodsModel::where("order_id=$order_id")->count();
            $refunding_count = NsOrderGoodsModel::where("order_id=$order_id AND refund_status<>0 AND refund_status<>5 AND refund_status>0")->count();
            $refunded_count = NsOrderGoodsModel::where("order_id=$order_id AND refund_status=5")->count();
            
            $shipping_status = $orderInfo->shipping_status;
            $all_refund = 0;
            if ($refunding_count > 0) {
                
                $orderInfo->order_status = OrderStatus::getOrderCommonStatus()[6]['status_id']; // 退款中
            } elseif ($refunded_count == $total_count) {
                
                $all_refund = 1;
            } elseif ($shipping_status == OrderStatus::getShippingStatus()[0]['shipping_status']) {
                
                $orderInfo->order_status = OrderStatus::getOrderCommonStatus()[1]['status_id']; // 待发货
            } elseif ($shipping_status == OrderStatus::getShippingStatus()[1]['shipping_status']) {
                
                $orderInfo->order_status = OrderStatus::getOrderCommonStatus()[2]['status_id']; // 已发货
            } elseif ($shipping_status == OrderStatus::getShippingStatus()[2]['shipping_status']) {
                
                $orderInfo->order_status = OrderStatus::getOrderCommonStatus()[3]['status_id']; // 已收货
            }
            
            // 订单恢复正常操作
            if ($all_refund == 0) {
                $retval = $orderInfo->save();
            } else {
                // 全部退款订单转化为交易关闭
                $retval = $this->orderClose($order_id);
            }
            
            $orderInfo->commit();
            return $retval;
        } catch (\Exception $e) {
            $orderInfo->rollback();
            return $e->getMessage();
        }
        
        return $retval;
    }

    /**
     * 获取订单详情
     *
     * @param unknown $order_id            
     */
    public function getDetail($order_id)
    {
        // 查询主表
        $order_detail = $this->order->get($order_id);
        // 发票信息
        $temp_array = array();
        if ($order_detail["buyer_invoice"] != "") {
            $temp_array = explode("$", $order_detail["buyer_invoice"]);
        }
        $order_detail["buyer_invoice_info"] = $temp_array;
        if (empty($order_detail)) {
            return '';
        }
        $order_detail['payment_type_name'] = OrderStatus::getPayType($order_detail['payment_type']);
        if ($order_detail['shipping_type'] == 1) {
            $order_detail['shipping_type_name'] = '商家配送';
        } elseif ($order_detail['shipping_type'] == 2) {
            $order_detail['shipping_type_name'] = '门店自提';
        } else {
            $order_detail['shipping_type_name'] = '';
        }
        // 查询订单项表
        $order_detail['order_goods'] = $this->getOrderGoods($order_id);
        if ($order_detail['payment_type'] == 6 || $order_detail['shipping_type'] == 2) {
            $order_status = OrderStatus::getSinceOrderStatus();
        } else {
            // 查询操作项
            $order_status = OrderStatus::getOrderCommonStatus();
        }
        // 查询订单提货信息表
        if ($order_detail['shipping_type'] == 2) {
            $order_pickup_model = new NsOrderPickupModel();
            $order_pickup_info = $order_pickup_model->getInfo([
                'order_id' => $order_id
            ], '*');
            $address = new Address();
            $order_pickup_info['province_name'] = $address->getProvinceName($order_pickup_info['province_id']);
            $order_pickup_info['city_name'] = $address->getCityName($order_pickup_info['city_id']);
            $order_pickup_info['dictrict_name'] = $address->getDistrictName($order_pickup_info['district_id']);
            $order_detail['order_pickup'] = $order_pickup_info;
        } else {
            $order_detail['order_pickup'] = '';
        }
        // 查询订单操作
        foreach ($order_status as $k_status => $v_status) {
            
            if ($v_status['status_id'] == $order_detail['order_status']) {
                $order_detail['operation'] = $v_status['operation'];
                $order_detail['status_name'] = $v_status['status_name'];
            }
        }
        // 查询订单操作日志
        $order_action = new NsOrderActionModel();
        $order_action_log = $order_action->getQuery([
            'order_id' => $order_id
        ], '*', 'action_time desc');
        $order_detail['order_action'] = $order_action_log;
        
        $address_service = new Address();
        $order_detail['address'] = $address_service->getAddress($order_detail['receiver_province'], $order_detail['receiver_city'], $order_detail['receiver_district']);
        return $order_detail;
    }

    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id            
     */
    public function getOrderGoods($order_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $order_goods_list = $order_goods->all([
            'order_id' => $order_id
        ]);
        foreach ($order_goods_list as $k => $v) {
            $order_goods_list[$k]['express_info'] = $this->getOrderGoodsExpress($v['order_goods_id']);
            $shipping_status_array = OrderStatus::getShippingStatus();
            foreach ($shipping_status_array as $k_status => $v_status) {
                if ($v['shipping_status'] == $v_status['shipping_status']) {
                    $order_goods_list[$k]['shipping_status_name'] = $v_status['status_name'];
                }
            }
            // 商品图片
            $picture = new AlbumPictureModel();
            $picture_info = $picture->get($v['goods_picture']);
            $order_goods_list[$k]['picture_info'] = $picture_info;
            if ($v['refund_status'] != 0) {
                $order_refund_status = OrderStatus::getRefundStatus();
                foreach ($order_refund_status as $k_status => $v_status) {
                    
                    if ($v_status['status_id'] == $v['refund_status']) {
                        $order_goods_list[$k]['refund_operation'] = $v_status['refund_operation'];
                        $order_goods_list[$k]['status_name'] = $v_status['status_name'];
                    }
                }
            } else {
                $order_goods_list[$k]['refund_operation'] = '';
                $order_goods_list[$k]['status_name'] = '';
            }
        }
        return $order_goods_list;
    }

    /**
     * 获取订单的物流信息
     *
     * @param unknown $order_id            
     */
    public function getOrderExpress($order_id)
    {
        $order_goods_express = new NsOrderGoodsExpressModel();
        $order_express_list = $order_goods_express->all([
            'order_id' => $order_id
        ]);
        return $order_express_list;
    }

    /**
     * 获取订单项的物流信息
     *
     * @param unknown $order_goods_id            
     * @return multitype:|Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    private function getOrderGoodsExpress($order_goods_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $order_goods_info = $order_goods->getInfo([
            'order_goods_id' => $order_goods_id
        ], 'order_id,shipping_status');
        if ($order_goods_info['shipping_status'] == 0) {
            return array();
        } else {
            $order_express_list = $this->getOrderExpress($order_goods_info['order_id']);
            foreach ($order_express_list as $k => $v) {
                $order_goods_id_array = explode(",", $v['order_goods_id_array']);
                if (in_array($order_goods_id, $order_goods_id_array)) {
                    return $v;
                }
            }
            return array();
        }
    }

    /**
     * 订单价格调整
     *
     * @param unknown $order_id            
     * @param unknown $goods_money
     *            调整后的商品总价
     * @param unknown $shipping_fee
     *            调整后的运费
     */
    public function orderAdjustMoney($order_id, $goods_money, $shipping_fee)
    {
        $this->order->startTrans();
        try {
            $order_model = new NsOrderModel();
            $order_info = $order_model->get($order_id);
            // 商品金额差额
            $goods_money_adjust = $goods_money - $order_info['goods_money'];
            $shipping_fee_adjust = $shipping_fee - $order_info['shipping_money'];
            $order_money = $order_info['order_money'] + $goods_money_adjust + $shipping_fee_adjust;
            $pay_money = $order_info['pay_money'] + $goods_money_adjust + $shipping_fee_adjust;
            $data = array(
                'goods_money' => $goods_money,
                'order_money' => $order_money,
                'shipping_money' => $shipping_fee,
                'pay_money' => $pay_money
            );
            $retval = $order_model->save($data, [
                'order_id' => $order_id
            ]);
            $this->addOrderAction($order_id, $this->uid, '调整金额');
            $this->order->commit();
            return $retval;
        } catch (\Exception $e) {
            $this->order->rollback();
            return $e;
        }
    }

    /**
     * 获取订单整体商品金额(根据订单项)
     *
     * @param unknown $order_id            
     */
    public function getOrderGoodsMoney($order_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $money = $order_goods->where([
            'order_id' => $order_id
        ])->sum('goods_money');
        if (empty($money)) {
            $money = 0;
        }
        return $money;
    }

    /**
     * 获取订单赠品
     *
     * @param unknown $order_id            
     */
    public function getOrderPromotionGift($order_id)
    {
        $gift_list = array();
        $order_promotion_details = new NsOrderPromotionDetailsModel();
        $promotion_list = $order_promotion_details->getQuery([
            'order_id' => $order_id,
            'promotion_type_id' => 1
        ], 'promotion_id', '');
        if (! empty($promotion_list)) {
            foreach ($promotion_list as $k => $v) {
                $rule = new NsPromotionMansongRuleModel();
                $gift = $rule->getInfo([
                    'rule_id' => $v['promotion_id']
                ], 'gift_id');
                $gift_list[] = $gift['gift_id'];
            }
        }
        return $gift_list;
    }

    /**
     * 获取具体订单项信息
     *
     * @param unknown $order_goods_id
     *            订单项ID
     */
    public function getOrderGoodsInfo($order_goods_id)
    {
        $order_goods = new NsOrderGoodsModel();
        return $order_goods->getInfo([
            'order_goods_id' => $order_goods_id
        ], 'goods_id,goods_name,goods_money,goods_picture,shop_id');
    }

    /**
     * 通过订单id 得到该订单的世纪支付金额
     *
     * @param unknown $order_id            
     */
    public function getOrderRealPayMoney($order_id)
    {
        $order_goods_model = new NsOrderGoodsModel();
        // 查询订单的所有的订单项
        $order_goods_list = $order_goods_model->getQuery([
            "order_id" => $order_id
        ], "*", "");
        $order_real_money = 0;
        if (! empty($order_goods_list)) {
            $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
            foreach ($order_goods_list as $k => $order_goods) {
                $promotion_money = $order_goods_promotion->where([
                    'order_id' => $order_id,
                    'sku_id' => $order_goods['sku_id']
                ])->sum('discount_money');
                if (empty($promotion_money)) {
                    $promotion_money = 0;
                }
                // 订单项的真实付款金额
                $order_goods_real_money = $order_goods['goods_money'] + $order_goods['adjust_money'] - $order_goods['refund_real_money'] - $promotion_money;
                // 订单付款金额
                $order_real_money = $order_real_money + $order_goods_real_money;
            }
        }
        return $order_real_money;
    }

    /**
     * 订单提货
     *
     * @param unknown $order_id            
     */
    public function pickupOrder($order_id, $buyer_name, $buyer_phone, $remark)
    {
        // 订单转为已收货状态
        $this->order->startTrans();
        try {
            $data_take_delivery = array(
                'shipping_status' => 2,
                'order_status' => 3,
                'sign_time' => date("Y-m-d H:i:s", time())
            );
            $order_model = new NsOrderModel();
            $order_model->save($data_take_delivery, [
                'order_id' => $order_id
            ]);
            $this->addOrderAction($order_id, $this->uid, '订单提货' . '提货人：' . $buyer_name . ' ' . $buyer_phone);
            // 记录提货信息
            $order_pickup_model = new NsOrderPickupModel();
            $data_pickup = array(
                'buyer_name' => $buyer_name,
                'buyer_mobile' => $buyer_phone,
                'remark' => $remark
            );
            $order_pickup_model->save($data_pickup, [
                'order_id' => $order_id
            ]);
            $order_goods_model = new NsOrderGoodsModel();
            $order_goods_model->save([
                'shipping_status' => 2
            ], [
                'order_id' => $order_id
            ]);
            $this->giveGoodsOrderPoint($order_id, 2);
            $this->order->commit();
            return 1;
        } catch (\Exception $e) {
            
            $this->order->rollback();
            return $e->getMessage();
        }
    }
	/*订单一个发货就与商家店铺结算*/
	public function shopSettlement($order_goods_id)
    {
        Db::startTrans();
        try {
			//本项目order_goods的成本价就是店铺结算价格;本项目积分是价格内的
			$goods_info=Db::table('ns_order_goods')->where('order_goods_id',$order_goods_id)->field('order_id,goods_id,sku_id,num,cost_price,shop_id,refund_real_money')->find();
			$shopinfo=Db::table('ns_shop')->where('shop_id',$goods_info['shop_id'])->field('money,point')->find();
			$logistics=Db::table('ns_goods')->where('goods_id',$goods_info['goods_id'])->value('cost_price_logistics');
			if($goods_info['refund_real_money']==0){
				$nowmoney=$shopinfo['money']+$logistics+$goods_info['cost_price']*$goods_info['num'];//项目独用，注意运费计算有可能调整？
				$nowpoint=$shopinfo['point'];
				$data_no='F_'.$order_goods_id.'_orderid_'.$goods_info['order_id'].'_skuid_'.$goods_info['sku_id'];
				$onerec=Db::table('ns_shop_account_records')->where(['rec_type'=>1,'data_no'=>$data_no,'shop_id'=>$goods_info['shop_id'],'data_id'=>$order_goods_id])->find();
				if(empty($onerec)){
					$data=array('rec_type'=>1,'data_no'=>$data_no,'shop_id'=>$goods_info['shop_id'],'money'=>($logistics+$goods_info['cost_price'])*$goods_info['num'],'data_id'=>$order_goods_id,'shop_money'=>$nowmoney,'shop_point'=>$nowpoint,'create_time'=>date("Y-m-d H:i:s", time()),'text'=>'商家商品订单收入');
					Db::table('ns_shop_account_records')->insert($data);
					Db::table('ns_shop')->where('shop_id',$goods_info['shop_id'])->update(['point' =>$nowpoint,'money' =>$nowmoney]);
				}
			}
			Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }
    }
    /**
     * 订单发放
     *
     * @param unknown $order_id       
     */
    public function giveGoodsOrderPoint($order_id, $type)
    {
        // 判断是否需要在本阶段赠送积分
        $order_model = new NsOrderModel();
        $order_info = $order_model->getInfo([
            "order_id" => $order_id
        ], "*");
        if ($order_info["give_point_type"] == $type) {
            if ($order_info["give_point"] > 0) {
                $member_account = new MemberAccount();
                $text = "";
                if ($order_info["give_point_type"] == 1) {
                    $text = "商城订单完成赠送积分";
                } elseif ($order_info["give_point_type"] == 2) {
                    $text = "商城订单完成收货赠送积分";
                } elseif ($order_info["give_point_type"] == 3) {
                    $text = "商城订单完成支付赠送积分";
                }
                $member_account->addMemberAccountData($order_info['shop_id'], 1, $order_info['buyer_id'], 1, $order_info['give_point'], 1, $order_id, $text);
            }
        }
    }
	/**
     *计算订单的利润  
     */
    public function getOrderProfit($order_detail)
    {
		/*取订单商品明细*/
		$goods_lists = $this->getOrderGoods($order_detail['order_id']);
		$goods_cost = 0;
		foreach ($goods_lists as $goods) {
			//获取sku表中商品的额外总成本,利差=(销售价-(结算价+额外成本价))*购买数量
			$data_sku = (new NsGoodsSkuModel())->getInfo(["sku_id" => $goods["sku_id"]]);
			$onegoods=Db::table('ns_goods')->where('goods_id',$goods['goods_id'])->find();
			$logistics=$onegoods['cost_price_logistics'];
			if ($goods['refund_status'] <> 5) { //无退货
				$goods_cost = $goods_cost+$logistics+($data_sku['cost_price'] + $data_sku['total_cost_price']-$logistics) * $goods['num'];
			}
		}
		$order_profit = $order_detail['order_money'] - $order_detail['refund_money'] - $goods_cost;
		if($oneGoods['zero_num']>0 && $oneGoods['zero_point_num']>0){$order_profit=0;}//2019拼团调整；首单包邮全免order_money为0无利润
		return $order_profit;
	}
    /*
     * 订单分红计算，预处理（非jplus会员满一个月后不预先生成记录）
	 *退款时同级ordergoods.php中orderGoodsConfirmRefund函数对分销再处理，全退全删，部分退款重新执行
     */
    public function orderCommission($order_id)
    {
        $config = new Config();
        $profit_percent10 = $config->getConfig(0, 'SALES_HUI_ZHI');//当级别为会员时,其提成为直属会员销售利差的比分比
        $profit_percent12 = $config->getConfig(0, 'SALES_JING_ZHI');//当级别为经理时,其提成为直属会员销售利差的比分比
        $profit_percent15 = $config->getConfig(0, 'SALES_ZONG_ZHI');//当级别为总监时,其提成为直属会员销售利差的比分比
        $profit_percent20 = $config->getConfig(0, 'SALES_HUI_CONG');//当级别为会员时,其提成为从属会员销售利差的比分比
        $profit_percent22 = $config->getConfig(0, 'SALES_JING_CONG');//当级别为经理时,其提成为从属会员销售利差的比分比成
        $profit_percent25 = $config->getConfig(0, 'SALES_ZONG_CONG');//当级别为总监时,其提成为从属会员销售利差的比分比

        $profit_percent[1][0] = floatval(str_replace('%', '', $profit_percent10["value"])) / 100;
        $profit_percent[1][2] = floatval(str_replace('%', '', $profit_percent12["value"])) / 100;
        $profit_percent[1][5] = floatval(str_replace('%', '', $profit_percent15["value"])) / 100;
        $profit_percent[2][0] = floatval(str_replace('%', '', $profit_percent20["value"])) / 100;
        $profit_percent[2][2] = floatval(str_replace('%', '', $profit_percent22["value"])) / 100;
        $profit_percent[2][5] = floatval(str_replace('%', '', $profit_percent25["value"])) / 100;

        // 查询主表
        $order_detail = $this->order->get($order_id);
		/*
        $card_group_id_array = array();
        $card_ids = (new NsGoodsModel())->getQuery(['group_id_array' => 111], 'goods_id', ''); //怀化会员卡的编号是111
        foreach ($card_ids as $k => $v) {
            $card_group_id_array[] = $v['goods_id'];
        }  */
        //取订单买家的会员层级关系
        $path_pid = (new Member())->getMemberInfo($order_detail["buyer_id"])['path_pid'];
        $path_arr = explode('#', $path_pid);
        $path_count = count($path_arr);

        $this->order->startTrans();

        try {
			/*获取订单利润*/
			$order_profit = $this->getOrderProfit($order_detail);

            if ($order_detail['buyer_message'] == '[shop_scan_pay]') {  //线下支付有标志
                //获取线下店铺服务费比例
                $service_charge_rate = Db::table('ns_shop')->where('shop_id', $order_detail['shop_id'])->value('service_charge_rate');
                $order_profit = $order_detail['order_money'] * ($service_charge_rate / 100)-$order_detail['order_money']*0.01-($order_detail['order_money'] * ($service_charge_rate / 100)*0.05);//线下服务费,其中后面的0.05是税费
            }

            if ($order_profit <= 0) {//毛利小于0，则置为0
                $order_profit = 0;
            }

            $my_profit = 0;
            Log::record('[ commission ] ' . var_export($path_arr, true), 'info');
			$memberAccountRecords=new NsMemberAccountRecordsModel();
            for ($i = $path_count - 1; $i >= 0; $i--) {
                if (empty($path_arr[$i]) || ($path_arr[$i] == 0) || ($i < $path_count - 2)) {
                    continue;
                }
                Log::record('[ commission ] ' . var_export($path_arr[$i], true), 'info');
                //此处进行预处理；订单完成时加入余额！
                $account_record = $memberAccountRecords->get(['uid' => $path_arr[$i], 'account_type' => 2, 'from_type' => 15, 'data_id' => $order_id]);//15类型专用

                //如果非会员卡是其它商品或线下扫码
                $grade = 0;
                $member_info = (new Member())->getMemberInfo($path_arr[$i]);
                if (!empty($member_info)) {
                    $grade = $member_info['grade'];
                }

                $my_profit = ($order_profit-($order_profit * $profit_percent[$path_count - $i][0]))* $profit_percent[$path_count - $i][0];//默认会员级别为0级
                if (($grade == 5) || ($grade == 2)) {  //经理、总监级会员
                    $my_profit =($order_profit-($order_profit * $profit_percent[$path_count - $i][$grade]))*$profit_percent[$path_count - $i][$grade];
                }
				if($member_info['jplus_level'] > 0){  //jplus_level大于0才可以预生成（注册一定时间内为10）
					$text = '消费奖励余额';

					$account_record_new = array(
						'uid' => $path_arr[$i],
						'shop_id' => $order_detail['shop_id'],
						'account_type' => 2,
						'sign' => 1,//正负号.0是负号.1是正号
						'number' => round($my_profit, 2),
						'from_type' => 15,
						'data_id' => $order_id,
						'text' => $text,
						'is_add'=>0,
						'create_time' => date('Y-m-d H:i:s', time()));
					
					if (empty($account_record)) {
						if($my_profit > 0){  //有等于0情形！只能放在下面，否则0时会执行更新
							$memberAccountRecords->create($account_record_new);
                            //sendSmsByUid($path_arr[$i], '尊敬的会员，您好，您的分销收益'.$my_profit.'元已待入账，百尺竿头更进一步，请继续加油赚取更多财富。');
                        }
					} else {  //my_profit为0时可以更新
						$memberAccountRecords->where('id',$account_record['id'])->update(['number' => round($my_profit, 2),'text' =>$text.'；'.date('Y-m-d H:i:s', time()).'发生退款变更']);
					}
                }
            }
            //只jplus会员可以获得积分
            $point_record = $memberAccountRecords->get(['uid' => $order_detail['buyer_id'], 'account_type' => 1, 'from_type' => 35, 'data_id' => $order_id]);
			
			$buy_member = (new Member())->getMemberInfo($order_detail['buyer_id']);
			if($buy_member['jplus_level'] > 0){
				//$this->addUserExtendPointAccount($order_detail,Round($order_profit * 0.05, 4));//用户推广返利积分
				if(empty($point_record)){
					$point_record_new = array(
						'uid' => $order_detail['buyer_id'],
						'shop_id' => $order_detail['shop_id'],
						'account_type' => 1,
						'sign' => 1,
						'number' => Round($order_profit * 0.4, 4),   //准备调整为20%
						'from_type' => 35,  //专用类型
						'data_id' =>$order_id,
						'text' => 'jplus会员消费自身奖励积分',
						'is_add'=>0,
						'create_time' => date('Y-m-d H:i:s', time()));
					if($order_profit>0){
						$memberAccountRecords->create($point_record_new);
					}
				} else {
					$memberAccountRecords->where('id',$point_record['id'])->update(['number' =>Round($order_profit * 0.4, 4),'text' =>$point_record['text'].'；'.date('Y-m-d H:i:s', time()).'发生退款变更',]);
				}
			}
            //商家积分预提，退款时变更或删除。完成时加入账户
			$shop_account = new ShopAccount();
			if($order_profit>0){
				//$this->addShopExtendPointAccount($order_detail,Round($order_profit * 0.05, 4)); //商家推广返利积分
			}
			$shop_point = Db::table('ns_shop_account_records')->where(['rec_type'=>1,'data_no' =>$order_detail['order_no'],'shop_id' =>$order_detail['shop_id'],'data_id' =>$order_id,'money' =>0])->find();
			if(empty($shop_point)){
				if($order_profit>0){
					$shop_account->addShopAccountRecords(1, $order_detail['shop_id'], $order_detail['order_no'],$order_id, 0,Round($order_profit * 0.09, 4), '订单支付，商家积分待入账', 0);
				}
			} else {
				Db::table('ns_shop_account_records')->where('id',$shop_point['id'])->update(['point' =>Round($order_profit * 0.09, 4),'text' =>$shop_point['text'].'；'.date('Y-m-d H:i:s', time()).'发生退款变更']);
			}
            $this->order->commit();

        } catch (\Exception $e) {
            $this->order->rollback();
        }
    }
//                    if (in_array($goods['goods_id'], $card_group_id_array)) {  //如果是怀化的会员卡
//                        if ($i == $path_count - 1) {//上一层级会员分红
//                            if ($goods['cost_price'] == 1680) {
//                                $my_profit = 400;
//                            }
//
//                            if ($goods['cost_price'] == 2980) {
//                                $my_profit = 400;
//                            }
//
//                            if ($goods['cost_price'] == 12800) {
//                                $my_profit = 500;
//                            }
//                        }
//                        if ($i == $path_count - 2) {//上2层级会员分红
//                            if ($goods['cost_price'] == 1680) {
//                                $my_profit = 200;
//                            }
//
//                            if ($goods['cost_price'] == 2980) {
//                                $my_profit = 200;
//                            }
//
//                            if ($goods['cost_price'] == 12800) {
//                                $my_profit = 1000;
//                            }
//                        }
//                        $my_profit = $my_profit * $goods['num'];
//                        $text = '会员卡销售分红';
	/*
     * 退款时对分销再处理，全退删除预处理所有记录
     */
    public function deleteCommission($order_info)  
    {   //引用处已添加事务功能
		$cond=['rec_type'=>1,'data_no'=>$order_info['order_no'],'shop_id'=>$order_info['shop_id'],'data_id'=>$order_info['order_id'],'is_add'=>0];
		$cond['rec_type'] = array('in','1,9'); //含推广预提记录
		$shoprec=Db::table('ns_shop_account_records')->where($cond)->select();
		foreach($shoprec as $k=>$v){
			Db::table('ns_shop_account_records')->where(['id'=>$v['id']])->delete();
		}
		$condition['from_type'] = array('in','15,35,55'); //加入推广积分55
		$condition['data_id']=$order_info['order_id'];
		$condition['is_add']=0;
		$rec_arr=Db::table('ns_member_account_records')->where($condition)->select();
		foreach($rec_arr as $k=>$v){
			Db::table('ns_member_account_records')->where(['id'=>$v['id']])->delete();
		}
	}
	/*
     * 订单完成，预提数据加入用户、商家账户
	 *assignMember函数中获得会员资格时也有预提数据加入账户代码
	 *调整时考虑两处代码
     */
    public function addCommission($order_id)
    {	//引用处已添加事务功能
		$order_info = $this->order->get($order_id);
		$cond=['data_no'=>$order_info['order_no'],'shop_id'=>$order_info['shop_id'],'data_id'=>$order_info['order_id'],'is_add'=>0];
		$cond['rec_type'] = array('in','1,9');
		$shoprec=Db::table('ns_shop_account_records')->where($cond)->select();
		foreach($shoprec as $k=>$v){
			$shop_acc=Db::table('ns_shop')->where(['shop_id'=>$v['shop_id']])->field('point,extend_point')->find();
			if($v['rec_type']==9){  //9是推广积分
				Db::table('ns_shop_account_records')->where(['id'=>$v['id']])->update(['text'=>$v['text'].'；预生成时间'.$v['create_time'],'create_time'=>date('Y-m-d H:i:s', time()),'is_add'=>1]);
				Db::table('ns_shop')->where(['shop_id'=>$v['shop_id']])->update(['extend_point'=>$shop_acc['extend_point']+$v['point']]);
			} else {
				Db::table('ns_shop_account_records')->where(['id'=>$v['id']])->update(['shop_point'=>$shop_point+$v['point'],'text'=>$v['text'].'；预生成时间'.$v['create_time'],'create_time'=>date('Y-m-d H:i:s', time()),'is_add'=>1]);
				Db::table('ns_shop')->where(['shop_id'=>$v['shop_id']])->update(['point'=>$shop_acc['point']+$v['point']]);
			}
		}
		$condition['from_type'] = array('in','15,35,55'); //增加推广积分55
		$condition['data_id']=$order_info['order_id'];
		$condition['is_add']=0;
		$rec_arr=Db::table('ns_member_account_records')->where($condition)->select();
		foreach($rec_arr as $k=>$v){
			$member_info=Db::table('ns_member')->where(['uid'=>$v['uid']])->field('assign_jplus_time,jplus_level')->find();
			if(!empty(strtotime($member_info['assign_jplus_time'])) && $member_info['jplus_level'] > 0){
				Db::table('ns_member_account_records')->where(['id'=>$v['id']])->update(['text'=>$v['text'].'；预生成时间'.$v['create_time'],'create_time'=>date('Y-m-d H:i:s', time()),'is_add'=>1]);
				if($v['account_type']==1){
					if($v['from_type']==55){  //55类型只积分
						Db::table('ns_member_account')->where(['uid'=>$v['uid']])->setInc('extend_point',$v['number']);
					}
					else {
						Db::table('ns_member_account')->where(['uid'=>$v['uid']])->setInc('point',$v['number']);
					}
				} elseif($v['account_type']==2){
					Db::table('ns_member_account')->where(['uid'=>$v['uid']])->setInc('balance',$v['number']);
				}
			}
		}
	}
	/*
     * 添加商家预提推广积分数据
     */
    public function addShopExtendPointAccount($order_detail,$extend_point)
    {	//上部引用处已添加事务功能
		$shop_account = new ShopAccount();
		$shop_extend_point = Db::table('ns_shop_account_records')->where(['rec_type' =>9,'data_no' =>$order_detail['order_no'],'shop_id' =>$order_detail['shop_id'],'data_id' =>$order_detail['order_id']])->find();
		if(empty($shop_extend_point)){
			if($extend_point>0){
				$shop_account->addShopAccountRecords(9, $order_detail['shop_id'], $order_detail['order_no'],$order_detail['order_id'], 0,$extend_point, '商家推广返利积分待入账', 0);
			}
		} else {
			Db::table('ns_shop_account_records')->where('id',$shop_extend_point['id'])->update(['point' =>$extend_point,'text' =>$shop_extend_point['text'].'；'.date('Y-m-d H:i:s', time()).'发生退款变更']);
		}
	}
	/*
     * 添加用户预提推广积分数据
     */
    public function addUserExtendPointAccount($order_detail,$extend_point)
    {	//上部引用处已添加事务功能
		$memberAccountRecords=new NsMemberAccountRecordsModel();
		$extend_point_rec = $memberAccountRecords->get(['uid' => $order_detail['buyer_id'], 'account_type' => 1, 'from_type' => 55, 'data_id' =>$order_detail['order_id']]);//55 推广返利积分
		if(empty($extend_point_rec)){
			$point_record_new = array(
				'uid' => $order_detail['buyer_id'],
				'shop_id' => $order_detail['shop_id'],
				'account_type' => 1,
				'sign' => 1,
				'number' => $extend_point, //推广积分
				'from_type' => 55,  //专用类型
				'data_id' =>$order_detail['order_id'],
				'text' => '用户消费推广返利积分',
				'is_add'=>0,
				'create_time' => date('Y-m-d H:i:s', time()));
				$memberAccountRecords->create($point_record_new);
		} else {
			$memberAccountRecords->where('id',$extend_point_rec['id'])->update(['number' =>$extend_point,'text' =>$extend_point_rec['text'].'；'.date('Y-m-d H:i:s', time()).'发生退款变更',]);
		}
	}
}