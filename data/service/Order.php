<?php
/**
 * Order.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 */
namespace data\service;

/**
 * 订单
 */
use data\api\IOrder as IOrder;
use data\extend\Kdniao;
use data\model\AlbumPictureModel;
use data\model\CityModel;
use data\model\DistrictModel;
use data\model\NsCartModel;
use data\model\NsGoodsEvaluateModel;
use data\model\NsGoodsModel;
use data\model\NsOrderExpressCompanyModel;
use data\model\NsOrderGoodsExpressModel;
use data\model\NsOrderGoodsModel;
use data\model\NsOrderModel;
use data\model\NsOrderShopReturnModel;
use data\model\NsShopModel;
use data\model\ProvinceModel;
use data\service\BaseService;
use data\service\GoodsCalculate\GoodsCalculate;
use data\service\NfxCommissionCalculate;
use data\service\NfxUser;
use data\service\niubusiness\NbsBusinessAssistantAccount;
use data\service\Order\Order as OrderBusiness;
use data\service\Order\OrderAccount;
use data\service\Order\OrderExpress;
use data\service\Order\OrderGoods;
use data\service\Order\OrderStatus;
use data\service\promotion\GoodsExpress;
use data\service\promotion\GoodsPreference;
use data\service\shopaccount\ShopAccount;
use data\model\NsShopOrderReturnModel;
use think\Log;
use think\Db;
use data\service\Member\MemberAccount;

class Order extends BaseService implements IOrder
{

    function __construct()
    {
        parent::__construct();
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::getOrderDetail()
     */
    public function getOrderDetail($order_id)
    {
        // 查询主表信息
        $order = new OrderBusiness();
        $detail = $order->getDetail($order_id);
        $detail['pay_status_name'] = $this->getPayStatusInfo($detail['pay_status'])['status_name'];
        $detail['shipping_status_name'] = $this->getShippingInfo($detail['shipping_status'])['status_name'];
        
        $express_list = $this->getOrderGoodsExpressList($order_id);
        // 未发货的订单项
        $order_goods_list = array();
        // 已发货的订单项
        $order_goods_delive = array();
        // 没有配送信息的订单项
        $order_goods_exprss = array();
        foreach ($detail["order_goods"] as $order_goods_obj) {
            $shipping_status = $order_goods_obj["shipping_status"];
            if ($shipping_status == 0) {
                // 未发货
                $order_goods_list[] = $order_goods_obj;
            } else {
                $order_goods_delive[] = $order_goods_obj;
            }
        }
        $detail["order_goods_no_delive"] = $order_goods_list;
        // 没有配送信息的订单项
        if (! empty($order_goods_delive) && count($order_goods_delive) > 0) {
            foreach ($order_goods_delive as $goods_obj) {
                $is_have = false;
                $order_goods_id = $goods_obj["order_goods_id"];
                foreach ($express_list as $express_obj) {
                    $order_goods_id_array = $express_obj["order_goods_id_array"];
                    $goods_id_str = explode(",", $order_goods_id_array);
                    if (in_array($order_goods_id, $goods_id_str)) {
                        $is_have = true;
                    }
                }
                if (! $is_have) {
                    $order_goods_exprss[] = $goods_obj;
                }
            }
        }
        $goods_packet_list = array();
        if (count($order_goods_exprss) > 0) {
            $packet_obj = array(
                "packet_name" => "无需物流",
                "express_name" => "",
                "express_code" => "",
                "express_id" => 0,
                "is_express" => 0,
                "order_goods_list" => $order_goods_exprss
            );
            $goods_packet_list[] = $packet_obj;
        }
        if (! empty($express_list) && count($express_list) > 0 && count($order_goods_delive) > 0) {
            $packet_num = 1;
            foreach ($express_list as $express_obj) {
                $packet_goods_list = array();
                $order_goods_id_array = $express_obj["order_goods_id_array"];
                $goods_id_str = explode(",", $order_goods_id_array);
                foreach ($order_goods_delive as $delive_obj) {
                    $order_goods_id = $delive_obj["order_goods_id"];
                    if (in_array($order_goods_id, $goods_id_str)) {
                        $packet_goods_list[] = $delive_obj;
                    }
                }
                $packet_obj = array(
                    "packet_name" => "包裹  + " . $packet_num,
                    "express_name" => $express_obj["express_name"],
                    "express_code" => $express_obj["express_no"],
                    "express_id" => $express_obj["id"],
                    "is_express" => 1,
                    "order_goods_list" => $packet_goods_list
                );
                $packet_num = $packet_num + 1;
                $goods_packet_list[] = $packet_obj;
            }
        }
        $detail["goods_packet_list"] = $goods_packet_list;
        return $detail;
        // TODO Auto-generated method stub
    }

    /**
     * 获取订单基础信息
     *
     * @param unknown $order_id            
     */
    public function getOrderInfo($order_id)
    {
        $order_model = new NsOrderModel();
        $order_info = $order_model->get($order_id);
        return $order_info;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::getOrderList()
     */
    public function getOrderList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $order_model = new NsOrderModel();
        // 查询主表
        $order_list = $order_model->pageQuery($page_index, $page_size, $condition, $order, '*');
        
        if (! empty($order_list['data'])) {
            foreach ($order_list['data'] as $k => $v) {
                // 查询订单项表
                $order_item = new NsOrderGoodsModel();
                $order_item_list = $order_item->where([
                    'order_id' => $v['order_id']
                ])->select();
                
                $province_name = "";
                $city_name = "";
                $district_name = "";
                
                $province = new ProvinceModel();
                $province_info = $province->getInfo(array(
                    "province_id" => $v["receiver_province"]
                ), "*");
                if (count($province_info) > 0) {
                    $province_name = $province_info["province_name"];
                }
                $order_list['data'][$k]['receiver_province_name'] = $province_name;
                $city = new CityModel();
                $city_info = $city->getInfo(array(
                    "city_id" => $v["receiver_city"]
                ), "*");
                if (count($city_info) > 0) {
                    $city_name = $city_info["city_name"];
                }
                $order_list['data'][$k]['receiver_city_name'] = $city_name;
                $district = new DistrictModel();
                $district_info = $district->getInfo(array(
                    "district_id" => $v["receiver_district"]
                ), "*");
                if (count($district_info) > 0) {
                    $district_name = $district_info["district_name"];
                }
                $order_list['data'][$k]['receiver_district_name'] = $district_name;
                foreach ($order_item_list as $key_item => $v_item) {
                    $picture = new AlbumPictureModel();
                    // $order_item_list[$key_item]['picture'] = $picture->get($v_item['goods_picture']);
                    $goods_picture = $picture->get($v_item['goods_picture']);
                    if (empty($goods_picture)) {
                        $goods_picture = array(
                            'pic_cover' => '',
                            'pic_cover_big' => '',
                            'pic_cover_mid' => '',
                            'pic_cover_small' => '',
                            'pic_cover_micro' => ''
                        );
                    }
                    $order_item_list[$key_item]['picture'] = $goods_picture;
                    if ($v_item['refund_status'] != 0) {
                        $order_refund_status = OrderStatus::getRefundStatus();
                        foreach ($order_refund_status as $k_status => $v_status) {
                            
                            if ($v_status['status_id'] == $v_item['refund_status']) {
                                $order_item_list[$key_item]['refund_operation'] = $v_status['refund_operation'];
                                $order_item_list[$key_item]['status_name'] = $v_status['status_name'];
                            }
                        }
                    } else {
                        $order_item_list[$key_item]['refund_operation'] = '';
                        $order_item_list[$key_item]['status_name'] = '';
                    }
                }
                $order_list['data'][$k]['order_item_list'] = $order_item_list;
                $order_list['data'][$k]['operation'] = '';
                // 订单来源名称
                $order_list['data'][$k]['order_from_name'] = OrderStatus::getOrderFrom($v['order_from']);
                $order_list['data'][$k]['pay_type_name'] = OrderStatus::getPayType($v['payment_type']);
                // 根据订单类型判断订单相关操作 $order_list['data'][$k]['payment_type'] == 6 || （积分支付也可以发货）
                if ($order_list['data'][$k]['shipping_type'] == 2) {
                    $order_status = OrderStatus::getSinceOrderStatus();
                } else {
                    $order_status = OrderStatus::getOrderCommonStatus();
                }
                // 查询订单操作
                foreach ($order_status as $k_status => $v_status) {
                    if ($v_status['status_id'] == $v['order_status']) {
                        $order_list['data'][$k]['operation'] = $v_status['operation'];
                        $order_list['data'][$k]['member_operation'] = $v_status['member_operation'];
                        $order_list['data'][$k]['status_name'] = $v_status['status_name'];
                        $order_list['data'][$k]['is_refund'] = $v_status['is_refund'];
                    }
                }
            }
        }
        return $order_list;
    }
    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderCreate()
     */
    public function orderUpCreate($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $coin = 0)
    {
        $order = new OrderBusiness();
        if ($pay_type == 4) {
            // 如果是货到付款 判断当前地址是否符合货到付款的地址
            $address = new Address();
            $result = $address->getDistributionAreaIsUser(0, $receiver_province, $receiver_city, $receiver_district);
            if (! $result) {
                return ORDER_CASH_DELIVERY;
            }
        }
        $retval = $order->orderInCreate($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $coin);
		runhook("Notify", "orderCreate", array(
            "order_id" => $retval[0]
        ));
        //针对特殊订单执行支付处理
        if($retval[0] > 0)
        {
            //货到付款
            if($pay_type == 4)
            {
                $this->orderOnLinePay($out_trade_no, 4);
            }else{
                $order_model = new NsOrderModel();
                $order_info = $order_model->getInfo(['order_id' => $retval[0]], '*');
                if(!empty($order_info))
                {
                    if($order_info['user_platform_money'] != 0)
                    {
                        if($order_info['pay_money'] == 0)
                        {
                            $this->orderOnLinePay($out_trade_no, 5);
        
                        }
                    }else{
        
                        if($order_info['pay_money'] == 0)
                        {
                            $this->orderOnLinePay($out_trade_no, 1);//默认微信支付
                        }
                    }
                }
                 
            }
        
        }
        return $retval;
        // TODO Auto-generated method stub
    }
	/**
     提取对应店铺的对应信息
     */
    public function getShopOne($use_string,$key)
    {
        if(!empty($use_string)){
				$shop_arr=explode(','.$key.'_',$use_string);
				if(count($shop_arr)>1){
					$one_id=explode(",",$shop_arr[1])[0];
				} else {$one_id=0;}
			} else {$one_id=0;}
        return $one_id;
    }
    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getOrderTradeNo()
     */
    public function getOrderTradeNo()
    {
        $order = new OrderBusiness();
        $no = $order->createOutTradeNo();
        return $no;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderDelivery()
     */
    public function orderDelivery($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no)
    {
        $order_express = new OrderExpress();
        $retval = $order_express->delivey($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no);
        runhook("Notify", "orderDelivery", array(
            "order_goods_ids" => $order_goods_id_array
        ));
        return $retval;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsDelivery()
     */
    public function orderGoodsDelivery($order_id, $order_goods_id_array)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsDelivery($order_id, $order_goods_id_array);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderClose()
     */
    public function orderClose($order_id)
    {
        $order = new OrderBusiness();
        $retval = $order->orderClose($order_id);
        return $retval;
        // TODO Auto-generated method stub
    }
    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderClose()
     */
    public function orderDelete($order_id)
    {
        $order = new OrderBusiness();
        $retval = $order->orderDelete($order_id);
        return $retval;
        // TODO Auto-generated method stub
    }
    /*
     * 订单完成的函数
     * (non-PHPdoc)
     * @see \data\api\IOrder::firstComplete()
     */
    public function firstComplete($orderid)
    {
        $order = new OrderBusiness();
        $retval = $order->lastComplete($orderid);

        runhook("Notify", "firstComplete", array(
            "order_id" => $orderid
        ));
        return $retval;
    }

    /*
     * 订单在线支付
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderOnLinePay()
     */
    public function orderOnLinePay($order_pay_no, $pay_type)
    {
        $order = new OrderBusiness();
		
        $retval = $order->OrderPay($order_pay_no, $pay_type, 0);
        if ($retval > 0) {
            // 处理店铺的账户资金
            $this->dealShopAccount_OrderPay($order_pay_no);
            // 处理平台的资金账户
            $this->dealPlatformAccountOrderPay($order_pay_no);
            
			$order_model = new NsOrderModel();
            $condition = " out_trade_no=" . $order_pay_no;
            $order_list = $order_model->getQuery($condition, "order_id,pay_status", "");
            foreach ($order_list as $k => $v) {
				$order->orderCommission($v['order_id']); //在线支付完成执行分销函数
				/*  //发送订单付款后的短信和邮件通知
					runhook("Notify", "orderPay", array(
						"order_id" => $v["order_id"]
					));
					// 判断是否需要在本阶段赠送积分
					$order = new OrderBusiness();
					$res = $order->giveGoodsOrderPoint($v["order_id"], 3);
				}  */
            }
        }
        return $retval;
    }

    /*
     * 订单线下支付
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderOffLinePay()
     */
    public function orderOffLinePay($order_id, $pay_type, $status)
    {
        $order = new OrderBusiness();
        
        $new_no = $this->getOrderNewOutTradeNo($order_id);
        if ($new_no) {
            $retval = $order->OrderPay($new_no, $pay_type, $status);
            if ($retval > 0) {
                $pay = new UnifyPay();
                $pay->offLinePay($new_no, $pay_type);
                // 处理店铺的账户资金
                $this->dealShopAccount_OrderPay('', $order_id);
                // 处理平台的资金账户
                $this->dealPlatformAccountOrderPay('', $order_id);
                // 判断是否需要在本阶段赠送积分
                $order = new OrderBusiness();
                $res = $order->giveGoodsOrderPoint($order_id, 3);
            }
            return $retval;
        } else {
            return 0;
        }
        // TODO Auto-generated method stub
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getOrderNewOutTradeNo()
     */
    public function getOrderNewOutTradeNo($order_id)
    {
        $order_model = new NsOrderModel();
        $out_trade_no = $order_model->getInfo([
            'order_id' => $order_id
        ], 'out_trade_no,shop_id');
        $order = new OrderBusiness();
        $new_no = $order->createNewOutTradeNo($order_id);
		Db::table('ns_order_payment')->where(['out_trade_no'=>$out_trade_no['out_trade_no'],'shop_id'=>$out_trade_no['shop_id']])->update(["out_trade_no" => $new_no]);
        //$pay = new UnifyPay();
        //$pay->modifyNo($out_trade_no['out_trade_no'], $new_no);单店铺使用的，弃用
        return $new_no;
    }

    /**
     * 订单调整金额(non-PHPdoc)
     *
     * @see \data\api\IOrder::orderMoneyAdjust()
     */
    public function orderMoneyAdjust($order_id, $order_goods_id_adjust_array, $shipping_fee)
    {
        // 调整订单
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsAdjustMoney($order_goods_id_adjust_array);
        
        if ($retval >= 0) {
            // 计算整体商品调整金额
            $new_no = $this->getOrderNewOutTradeNo($order_id);
            $order = new OrderBusiness();
            $order_goods_money = $order->getOrderGoodsMoney($order_id);
            $retval_order = $order->orderAdjustMoney($order_id, $order_goods_money, $shipping_fee);
            $order_model = new NsOrderModel();
            $order_money = $order_model->getInfo([
                'order_id' => $order_id
            ], 'pay_money');
            $pay = new UnifyPay();
            $pay->modifyPayMoney($new_no, $order_money['pay_money']);
            return $retval_order;
        } else {
            return $retval;
        }
    }

    /**
     * 查询订单
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::orderQuery()
     */
    public function orderQuery($where = "", $field = "*")
    {
        $order = new OrderBusiness();
        return $order->where($where)
            ->field($field)
            ->select();
    }

    /**
     * 查询订单项退款信息(non-PHPdoc)
     *
     * @see \data\api\IOrder::getOrderGoodsRefundInfo()
     */
    public function getOrderGoodsRefundInfo($order_goods_id)
    {
        $order_goods = new OrderGoods();
        $order_goods_info = $order_goods->getOrderGoodsRefundDetail($order_goods_id);
        return $order_goods_info;
    }

    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id            
     */
    public function getOrderGoods($order_id)
    {
        $order = new OrderBusiness();
        return $order->getOrderGoods($order_id);
    }

    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id            
     */
    public function getOrderGoodsInfo($order_goods_id)
    {
        $order = new OrderBusiness();
        $picture = new AlbumPictureModel();
        $order_goods_info = $order->getOrderGoodsInfo($order_goods_id);
        $order_goods_info['goods_picture'] = $picture->get($order_goods_info['goods_picture'])['pic_cover'];
        return $order_goods_info;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::addOrder()
     */
    public function addOrder($data)
    {
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsRefundAskfor()
     */
    public function orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsCancel()
     */
    public function orderGoodsCancel($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsCancel($order_id, $order_goods_id);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsReturnGoods()
     */
    public function orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsRefundAgree()
     */
    public function orderGoodsRefundAgree($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsRefundAgree($order_id, $order_goods_id);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsRefuseForever()
     */
    public function orderGoodsRefuseForever($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsRefuseForever($order_id, $order_goods_id);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsRefuseOnce()
     */
    public function orderGoodsRefuseOnce($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsRefuseOnce($order_id, $order_goods_id);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsConfirmRecieve()
     */
    public function orderGoodsConfirmRecieve($order_id, $order_goods_id)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsConfirmRecieve($order_id, $order_goods_id);
        return $retval;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::orderGoodsConfirmRefund()
     */
    public function orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money)
    {
        $order_goods = new OrderGoods();
        $retval = $order_goods->orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money);
        // 订单出现关闭的情形，没有-2状态
		//$ordergoods_model= new NsOrderGoodsModel();
		//$info = $ordergoods_model->get($order_goods_id);
        //$MemberAccount=new MemberAccount();
		//$MemberAccount->addMemberAccountData($info['shop_id'],2,$info['buyer_id'],1,$refund_real_money,2,$order_goods_id,'订单商品表编号'.$order_goods_id.'的商品退款');
        return $retval;
    }

    /**
     * 获取对应sku列表价格
     *
     * @param unknown $goods_sku_list            
     */
    public function getGoodsSkuListPrice($goods_sku_list)
    {
        $goods_preference = new GoodsPreference();
        $money = $goods_preference->getGoodsSkuListPrice($goods_sku_list);
        return $money;
    }

    /**
     * 获取邮费
     *
     * @param unknown $goods_sku_list            
     * @param unknown $province            
     * @param unknown $city            
     * @return Ambigous <unknown, number>
     */
    public function getExpressFee($goods_sku_list, $express_company_id, $province, $city)
    {
        $goods_express = new GoodsExpress();
        $fee = $goods_express->getSkuListExpressFee($goods_sku_list, $express_company_id, $province, $city);
        return $fee;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::orderGoodsRefundMoney()
     */
    public function orderGoodsRefundMoney($order_goods_id)
    {
        $order_goods = new OrderGoods();
        $money = $order_goods->orderGoodsRefundMoney($order_goods_id);
        return $money;
    }

    /**
     * 获取用户可使用优惠券
     *
     * @param unknown $goods_sku_list            
     */
    public function getMemberCouponList($goods_sku_list)
    {
        $goods_preference = new GoodsPreference();
        $coupon_list = $goods_preference->getMemberCouponList($goods_sku_list);
        return $coupon_list;
    }

    /**
     * 查询商品列表可用积分数
     *
     * @param unknown $goods_sku_list            
     */
    public function getGoodsSkuListUsePoint($goods_sku_list)
    {
        $point = 0;
        $goods_sku_list_array = explode(",", $goods_sku_list);
        foreach ($goods_sku_list_array as $k => $v) {
            
            $sku_data = explode(':', $v);
            $sku_id = $sku_data[0];
            $goods = new Goods();
            $goods_id = $goods->getGoodsId($sku_id);
            $goods_model = new NsGoodsModel();
            $point_use = $goods_model->getInfo([
                'goods_id' => $goods_id
            ], 'point_exchange_type,point_exchange');
            if ($point_use['point_exchange_type'] == 1) {
                $point += $point_use['point_exchange'];
            }
        }
        return $point;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::OrderTakeDelivery()
     */
    public function OrderTakeDelivery($order_id)
    {
        $order = new OrderBusiness();
        $res = $order->OrderTakeDelivery($order_id);
        return $res;
    }

    /**
     * 删除购物车中的数据
     * 修改时间：2017年5月26日 14:35:38 王永杰
     * 首先要查询当前商品在购物车中的数量，如果商品数量等于1则删除，如果商品数量大于1个，则减少该商品的数量
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::deleteCart()
     */
    public function deleteCart($goods_sku_list, $uid)
    {
        $cart = new NsCartModel();
        $goods_sku_list_array = explode(",", $goods_sku_list);
        foreach ($goods_sku_list_array as $k => $v) {
            $sku_data = explode(':', $v);
            $sku_id = $sku_data[0];
            // 购物车中该商品删除
			$cart->destroy([
				'buyer_id' => $uid,
				'sku_id' => $sku_id
			]);
        }
        $_SESSION["user_cart"] = '';
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::getOrderCount()
     */
    public function getOrderCount($condition)
    {
        $order = new NsOrderModel();
        $count = $order->where($condition)->count();
        return $count;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::getPayMoneySum()
     */
    public function getPayMoneySum($condition, $date)
    {
        $order_model = new NsOrderModel();
        $money_sum = $order_model->where($condition)
            ->whereTime('create_time', $date)
            ->sum('pay_money');
        return $money_sum;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::getGoodsNumSum()
     */
    public function getGoodsNumSum($condition, $date)
    {
        $order_model = new NsOrderModel();
        $order_list = $order_model->where($condition)
            ->whereTime('create_time', $date)
            ->select();
        $goods_sum = 0;
        foreach ($order_list as $k => $v) {
            $order_goods = new NsOrderGoodsModel();
            $goods_sum += $order_goods->where([
                'order_id' => $v['order_id']
            ])->sum('num');
        }
        return $goods_sum;
    }

    /**
     * 获取具体配送状态信息
     *
     * @param unknown $shipping_status_id            
     * @return Ambigous <NULL, multitype:string >
     */
    public static function getShippingInfo($shipping_status_id)
    {
        $shipping_status = OrderStatus::getShippingStatus();
        $info = null;
        foreach ($shipping_status as $shipping_info) {
            if ($shipping_status_id == $shipping_info['shipping_status']) {
                $info = $shipping_info;
                break;
            }
        }
        return $info;
    }

    /**
     * 获取具体支付状态信息
     *
     * @param unknown $pay_status_id            
     * @return multitype:multitype:string |string
     */
    public static function getPayStatusInfo($pay_status_id)
    {
        $pay_status = OrderStatus::getPayStatus();
        $info = null;
        foreach ($pay_status as $pay_info) {
            if ($pay_status_id == $pay_info['pay_status']) {
                $info = $pay_info;
                break;
            }
        }
        return $info;
    }

    /**
     * 获取订单各状态数量
     */
    public static function getOrderStatusNum($condition = '')
    {
        $order = new NsOrderModel();
        $orderStatusNum['all'] = $order->where($condition)->count(); // 全部
        $condition['order_status'] = 0; // 待付款
        $orderStatusNum['wait_pay'] = $order->where($condition)->count();
        $condition['order_status'] = 1; // 待发货
        $orderStatusNum['wait_delivery'] = $order->where($condition)->count();
        $condition['order_status'] = 2; // 待收货
        $orderStatusNum['wait_recieved'] = $order->where($condition)->count();
        $condition['order_status'] = 3; // 已收货
        $orderStatusNum['recieved'] = $order->where($condition)->count();
        $condition['order_status'] = 4; // 交易成功
        $orderStatusNum['success'] = $order->where($condition)->count();
        $condition['order_status'] = 5; // 已关闭
        $orderStatusNum['closed'] = $order->where($condition)->count();
        $condition['order_status'] = - 1; // 退款中
        $orderStatusNum['refunding'] = $order->where($condition)->count();
        $condition['order_status'] = - 2; // 已退款
        $orderStatusNum['refunded'] = $order->where($condition)->count();
        $condition['order_status'] = array(
            'in',
            '3,4'
        ); // 已收货
        $condition['is_evaluate'] = 0; // 未评价
        $orderStatusNum['wait_evaluate'] = $order->where($condition)->count(); // 待评价
        
        return $orderStatusNum;
    }

    /**
     * 商品评价-添加
     *
     * @param unknown $dataList
     *            评价内容的 数组
     * @return Ambigous <multitype:, \think\false>
     */
    public function addGoodsEvaluate($dataArr, $order_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $goods = new NsGoodsModel();
        $res = $goodsEvaluate->saveAll($dataArr);
        $result = false;
        
        if ($res != false) {
            // 修改订单评价状态
            $order = new NsOrderModel();
            $data = array(
                'is_evaluate' => 1
            );
            $result = $order->save($data, [
                'order_id' => $order_id
            ]);
        }
        foreach ($dataArr as $item) {
            $good_info = $goods->get($item['goods_id']);
            $evaluates = $good_info['evaluates'] + 1;
            $star = $good_info['star'] + $item['scores'];
            $match_point = $star / $evaluates;
            $match_ratio = $match_point / 5 * 100 + '%';
            $data = array(
                'evaluates' => $evaluates,
                'star' => $star,
                'match_point' => $match_point,
                'match_ratio' => $match_ratio
            );
            $goods->update($data, [
                'goods_id' => $item['goods_id']
            ]);
        }
        
        return $result;
    }

    /**
     * 商品评价-回复
     *
     * @param unknown $explain_first
     *            评价内容
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    public function addGoodsEvaluateExplain($explain_first, $order_goods_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $data = array(
            'explain_first' => $explain_first
        );
        return $goodsEvaluate->save($data, [
            'order_goods_id' => $order_goods_id
        ]);
    }

    /**
     * 商品评价-追评
     *
     * @param unknown $again_content
     *            追评内容
     * @param unknown $againImageList
     *            传入追评图片的 数组
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    public function addGoodsEvaluateAgain($again_content, $againImageList, $order_goods_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $data = array(
            'again_content' => $again_content,
            'again_addtime' => date("Y-m-d H:i:s", time()),
            'again_image' => $againImageList
        );
        return $goodsEvaluate->save($data, [
            'order_goods_id' => $order_goods_id
        ]);
    }

    /**
     * 商品评价-追评回复
     *
     * @param unknown $again_explain
     *            追评的 回复内容
     * @param unknown $ordergoodsid
     *            订单项ID
     * @return Ambigous <number, \think\false>
     */
    public function addGoodsEvaluateAgainExplain($again_explain, $order_goods_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $data = array(
            'again_explain' => $again_explain
        );
        return $goodsEvaluate->save($data, [
            'order_goods_id' => $order_goods_id
        ]);
    }

    /**
     * 获取指定订单的评价信息
     *
     * @param unknown $orderid
     *            订单ID
     */
    public function getOrderEvaluateByOrder($order_id)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $condition['order_id'] = $order_id;
        $field = 'order_id, order_no, order_goods_id, goods_id, goods_name, goods_price, goods_image, shop_id, shop_name, content, addtime, image, explain_first, member_name, uid, is_anonymous, scores, again_content, again_addtime, again_image, again_explain';
        return $goodsEvaluate->getQuery($condition, $field, 'order_goods_id ASC');
    }

    /**
     * 获取指定会员的评价信息
     *
     * @param unknown $uid
     *            会员ID
     */
    public function getOrderEvaluateByMember($uid)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $condition['uid'] = $uid;
        $field = 'order_id, order_no, order_goods_id, goods_id, goods_name, goods_price, goods_image, shop_id, shop_name, content, addtime, image, explain_first, member_name, uid, is_anonymous, scores, again_content, again_addtime, again_image, again_explain';
        return $goodsEvaluate->getQuery($condition, $field, 'order_goods_id ASC');
    }

    /**
     * 评价信息 分页
     *
     * @param unknown $page_index            
     * @param unknown $page_size            
     * @param unknown $condition            
     * @param unknown $order            
     * @return number
     */
    public function getOrderEvaluateDataList($page_index, $page_size, $condition, $order)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        return $goodsEvaluate->pageQuery($page_index, $page_size, $condition, $order, "*");
    }

    /**
     * 获取评价列表
     *
     * @param unknown $page_index
     *            页码
     * @param unknown $page_size
     *            页大小
     * @param unknown $condition
     *            条件
     * @param unknown $order
     *            排序
     * @return multitype:number unknown
     */
    public function getOrderEvaluateList($page_index, $page_size, $condition, $order)
    {
        $goodsEvaluate = new NsGoodsEvaluateModel();
        $field = 'order_id, order_no, order_goods_id, goods_id, goods_name, goods_price, goods_image, shop_id, shop_name, content, addtime, image, explain_first, member_name, uid, is_anonymous, scores, again_content, again_addtime, again_image, again_explain';
        return $goodsEvaluate->pageQuery($page_index, $page_size, $condition, $order, $field);
    }

    /**
     * 修改订单数据
     *
     * @param unknown $order_id            
     * @param unknown $data            
     */
    public function modifyOrderInfo($data, $order_id)
    {
        $order = new NsOrderModel();
        return $order->save($data, [
            'order_id' => $order_id
        ]);
    }

    /**
     * 判断店铺类型
     *
     * @param unknown $shop_id            
     */
    private function getShopTypeDetail($shop_id)
    {
        $shop_model = new NsShopModel();
        $shop_detail = $shop_model->get($shop_id);
        if (empty($shop_detail)) {
            return 0;
        } else {
            return $shop_detail["shop_type"];
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopOrderAccountList()
     */
    public function getShopOrderAccountList($shop_id, $start_time, $end_time, $page_index, $page_size)
    {
        $order_account = new OrderAccount();
        $list = $order_account->getShopOrderSumList($shop_id, $start_time, $end_time, $page_index, $page_size);
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopOrderRefundList()
     */
    public function getShopOrderRefundList($shop_id, $start_time, $end_time, $page_index, $page_size)
    {
        $order_account = new OrderAccount();
        $list = $order_account->getShopOrderRefundList($shop_id, $start_time, $end_time, $page_index, $page_size);
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopOrderStatics()
     */
    public function getShopOrderStatics($shop_id, $start_time, $end_time)
    {
        $order_account = new OrderAccount();
        $order_sum = $order_account->getShopOrderSum($shop_id, $start_time, $end_time);
        $order_refund_sum = $order_account->getShopOrderSumRefund($shop_id, $start_time, $end_time);
        $order_sum_account = $order_sum - $order_refund_sum;
        $array = array(
            'order_sum' => $order_sum,
            'order_refund_sum' => $order_refund_sum,
            'order_account' => $order_sum_account
        );
        return $array;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopOrderAccountDetail()
     */
    public function getShopOrderAccountDetail($shop_id)
    {
        // 获取总销售统计
        $account_all = $this->getShopOrderStatics($shop_id, '2015-1-1', '3050-1-1');
        // 获取今日销售统计
        $date_day_start = date("Y-m-d", time());
        $date_day_end = date("Y-m-d H:i:s", time());
        $account_day = $this->getShopOrderStatics($shop_id, $date_day_start, $date_day_end);
        // 获取周销售统计（7天）
        $date_week_start = date('Y-m-d', strtotime('-7 days'));
        $date_week_end = $date_day_end;
        $account_week = $this->getShopOrderStatics($shop_id, $date_week_start, $date_week_end);
        // 获取月销售统计(30天)
        $date_month_start = date('Y-m-d', strtotime('-30 days'));
        $date_month_end = $date_day_end;
        $account_month = $this->getShopOrderStatics($shop_id, $date_month_start, $date_month_end);
        $array = array(
            'day' => $account_day,
            'week' => $account_week,
            'month' => $account_month,
            'all' => $account_all
        );
        return $array;
    }

    /*
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopAccountCountInfo()
     */
    public function getShopAccountCountInfo($shop_id)
    {
        // 本月第一天
        $date_month_start = date('Y-m-d', strtotime('-30 days'));
        $date_month_end = date("Y-m-d H:i:s", time());
        // 下单金额
        $order_account = new OrderAccount();
        $condition["create_time"] = [
            [
                ">=",
                $date_month_start
            ],
            [
                "<=",
                $date_month_end
            ]
        ];
        $condition['order_status'] = array(
            'NEQ',
            0
        );
        $condition['order_status'] = array(
            'NEQ',
            5
        );
        if ($shop_id != 0) {
            $condition['shop_id'] = array(
                'NEQ',
                0
            );
        }
        $order_money = $order_account->getShopSaleSum($condition);
        // var_dump($order_money);
        // 下单会员
        $order_user_num = $order_account->getShopSaleUserSum($condition);
        // 下单量
        $order_num = $order_account->getShopSaleNumSum($condition);
        // 下单商品数
        $order_goods_num = $order_account->getShopSaleGoodsNumSum($condition);
        // 平均客单价
        if ($order_user_num > 0) {
            $user_money_average = $order_money / $order_user_num;
        } else {
            $user_money_average = 0;
        }
        // 平均价格
        if ($order_goods_num > 0) {
            $goods_money_average = $order_money / $order_goods_num;
        } else {
            $goods_money_average = 0;
        }
        $array = array(
            "order_money" => sprintf('%.2f', $order_money),
            "order_user_num" => $order_user_num,
            "order_num" => $order_num,
            "order_goods_num" => $order_goods_num,
            "user_money_average" => sprintf('%.2f', $user_money_average),
            "goods_money_average" => sprintf('%.2f', $goods_money_average)
        );
        return $array;
    }

    /*
     * (non-PHPdoc)
     *
     * @see \data\api\IOrder::getShopGoodsSalesList()
     */
    public function getShopGoodsSalesList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        // $goods_calculate = new GoodsCalculate();
        // $goods_sales_list = $goods_calculate->getGoodsSalesInfoList($page_index, $page_size , $condition , $order );
        // return $goods_sales_list;
        $goods_model = new NsGoodsModel();
        $goods_list = $goods_model->pageQuery($page_index, $page_size, $condition, $order, '*');
        // 条件
        $start_date = date('Y-m-d', strtotime('-30 days'));
        $end_date = date("Y-m-d H:i:s", time());
        $condition['create_time'] = [
            'between',
            [
                $start_date,
                $end_date
            ]
        ];
        
        $order_condition["shop_id"] = $condition["shop_id"];
        $goods_calculate = new GoodsCalculate();
        // 得到条件内的订单项
        $order_goods_list = $goods_calculate->getOrderGoodsSelect($order_condition);
        // 遍历商品
        foreach ($goods_list["data"] as $k => $v) {
            $data = array();
            $goods_sales_num = $goods_calculate->getGoodsSalesNum($order_goods_list, $v["goods_id"]);
            $goods_sales_money = $goods_calculate->getGoodsSalesMoney($order_goods_list, $v["goods_id"]);
            $data["sales_num"] = $goods_sales_num;
            $data["sales_money"] = $goods_sales_money;
            $goods_list["data"][$k]["sales_info"] = $data;
        }
        return $goods_list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IOrder::getShopGoodsSalesAll()
     */
    public function getShopGoodsSalesQuery($shop_id, $start_date, $end_date, $condition)
    {
        // TODO Auto-generated method stub
        // 商品
        $goods_model = new NsGoodsModel();
        $goods_list = $goods_model->getQuery($condition, "*", '');
        // 订单项
        $condition['create_time'] = [
            'between',
            [
                $start_date,
                $end_date
            ]
        ];
        $order_condition["create_time"] = [
            [
                ">=",
                $start_date
            ],
            [
                "<=",
                $end_date
            ]
        ];
        $order_condition['order_status'] = array(
            'NEQ',
            0
        );
        $order_condition['order_status'] = array(
            'NEQ',
            5
        );
        if ($shop_id != '') {
            $order_condition["shop_id"] = $shop_id;
        }
        $goods_calculate = new GoodsCalculate();
        $order_goods_list = $goods_calculate->getOrderGoodsSelect($order_condition);
        // 遍历商品
        foreach ($goods_list as $k => $v) {
            $data = array();
            $goods_sales_num = $goods_calculate->getGoodsSalesNum($order_goods_list, $v["goods_id"]);
            $goods_sales_money = $goods_calculate->getGoodsSalesMoney($order_goods_list, $v["goods_id"]);
            $goods_list[$k]["sales_num"] = $goods_sales_num;
            $goods_list[$k]["sales_money"] = $goods_sales_money;
        }
        return $goods_list;
    }

    /**
     * 查询一段时间内的店铺下单金额
     *
     * @param unknown $shop_id            
     * @param unknown $start_date            
     * @param unknown $end_date            
     * @return Ambigous <\data\service\Order\unknown, number, unknown>
     */
    public function getShopSaleSum($condition)
    {
        $order_account = new OrderAccount();
        $sales_num = $order_account->getShopSaleSum($condition);
        return $sales_num;
    }

    /**
     * 查询一段时间内的店铺下单量
     *
     * @param unknown $shop_id            
     * @param unknown $start_date            
     * @param unknown $end_date            
     * @return unknown
     */
    public function getShopSaleNumSum($condition)
    {
        $order_account = new OrderAccount();
        $sales_num = $order_account->getShopSaleNumSum($condition);
        return $sales_num;
    }

  /**
     * ***********************************************店铺账户--Start******************************************************
     */
    /**
     * 订单支付的时候 调整店铺账户
     *
     * @param string $order_out_trade_no            
     * @param number $order_id            
     */
    private function dealShopAccount_OrderPay($order_out_trade_no = "", $order_id = 0)
    {
            if ($order_out_trade_no != "" && $order_id == 0) {
                $order_model = new NsOrderModel();
                $condition = " out_trade_no=" . $order_out_trade_no;
                $order_list = $order_model->getQuery($condition, "order_id", "");
                foreach ($order_list as $k => $v) {
                    $this->updateShopAccount_OrderPay($v["order_id"]);
                }
            } else {
                if ($order_out_trade_no == "" && $order_id != 0) {
                    $this->updateShopAccount_OrderPay($order_id);
                }
            }
    }
    /**
     * 订单支付
     *
     * @param unknown $order_id            
     */
    private function updateShopAccount_OrderPay($order_id)
    {
        $order_model = new NsOrderModel();
        $shop_account = new ShopAccount();
        $order = new OrderBusiness();
        $order_model->startTrans();
        try {
            $order_obj = $order_model->get($order_id);
            // 订单的实际付款金额
            $pay_money = $order->getOrderRealPayMoney($order_id);
            // 订单的支付方式
            $payment_type = $order_obj["payment_type"];
            // 店铺id
            $shop_id = $order_obj["shop_id"];
            // 订单号
            $order_no = $order_obj["order_no"];
            // 处理订单的营业总额
            //$shop_account->addShopAccountProfitRecords(getSerialNo(), $shop_id, $pay_money, 1, $order_id, "店铺订单支付金额" . $pay_money . "元, 订单号为：" . $order_no . ", 支付方式【线下支付】。");
            if ($payment_type != ORDER_REFUND_STATUS) {
                // 在线支付 处理店铺的入账总额
                //$shop_account->addShopAccountMoneyRecords(getSerialNo(), $shop_id, $pay_money, 1, $order_id, "店铺订单支付金额" . $pay_money . "元, 订单号为：" . $order_no . ", 支付方式【在线支付】, 已入店铺账户。");
            }
            // 处理平台的利润分成
            $this->addShopOrderAccountRecords($order_id, $order_no, $shop_id, $pay_money);
            $order_model->commit();
        } catch (\Exception $e) {
            Log::write("updateShopAccount_OrderPay".$e->getMessage());
            $order_model->rollback();
        }
    }
    /**
     * ***********************************************店铺账户--End******************************************************
     */
    
    /**
     * ***********************************************平台账户计算--Start******************************************************
     */
    /**
     * 订单支付时处理 平台的账户
     *
     * @param string $order_out_trade_no            
     * @param number $order_id            
     */
    public function dealPlatformAccountOrderPay($order_out_trade_no = "", $order_id = 0)
    {
            if ($order_out_trade_no != "" && $order_id == 0) {
                $order_model = new NsOrderModel();
                $condition = " out_trade_no=" . $order_out_trade_no;
                $order_list = $order_model->getQuery($condition, "order_id,pay_status", "");
                foreach ($order_list as $k => $v) {
					if($v['pay_status']==0){
						$this->updateAccountOrderPay($v["order_id"]);
					}
                }
            } else 
                if ($order_out_trade_no == "" && $order_id != 0) {
                    $this->updateAccountOrderPay($order_id);
                }
    }

    /**
     * 处理平台的利润抽成
     *
     * @param unknown $order_id            
     * @param unknown $order_no            
     * @param unknown $shop_id            
     * @param unknown $pay_money            
     */
    private function addShopOrderAccountRecords($order_id, $order_no, $shop_id, $pay_money)
    {
        $account_service = new ShopAccount();    
        $account_service->addShopOrderAccountRecords($order_id, $order_no, $shop_id, $pay_money);
          
    }
    /**
     * 订单支付成功后处理 平台账户
     *
     * @param unknown $orderid            
     */
    private function updateAccountOrderPay($order_id)
    {
        $order_model = new NsOrderModel();
        $shop_account = new ShopAccount();
        $order = new OrderBusiness();
        $order_model->startTrans();
        try {
            $order_obj = $order_model->get($order_id);
            // 订单的实际付款金额
            $pay_money = $order->getOrderRealPayMoney($order_id);
            // 订单的支付方式
            $payment_type = $order_obj["payment_type"];
            // 店铺id
            $shop_id = $order_obj["shop_id"];
            // 订单号
            $order_no = $order_obj["order_no"];
            if ($payment_type != ORDER_REFUND_STATUS) {
                // 在线支付 处理平台的资金账户
                $shop_account->addAccountOrderRecords($shop_id, $pay_money, 1, $order_id, "店铺订单支付金额" . $pay_money . "元, 订单号为：" . $order_no . ", 支付方式【在线支付】。");
            }
            $order_model->commit();
        } catch (\Exception $e) {
            Log::write("updateAccountOrderPay:".$e->getMessage());
            $order_model->rollback();
        }
    }
    /**
     * ***********************************************平台账户计算--End******************************************************
     */
    
    /**
     * ***********************************************订单的佣金计算--Start******************************************************
     */
    
    /**
     * 支付后续佣金操作
     *
     * @param unknown $order_out_trade_no            
     * @param unknown $order_id            
     */
    private function orderCommissionCalculate($order_out_trade_no = "", $order_id = 0)
    {
      
    }

    /**
     * 处理单个 订单佣金计算
     *
     * @param unknown $order_id            
     */
    private function oneOrderCommissionCalculate($order_id)
    {
       
    }

    public function partent_test()
    {
      
    }

    /**
     * 订单退款成功后需要重新计算订单的佣金
     *
     * @param unknown $order_id            
     * @param unknown $order_goods_id            
     */
    public function updateCommissionMoney($order_id, $order_goods_id)
    {
       
    }

    /**
     * 订单完成交易进行 佣金结算
     *
     * @param unknown $order_id            
     */
    private function updateOrderCommission($order_id)
    {
        
    }

    /**
     * ***********************************************订单的佣金计算--End******************************************************
     */
    
    /**
     * ***********************************************招商员的账户计算--Start******************************************************
     */
    /**
     * 招商员的订单佣金计算
     *
     * @param string $order_out_trade_no            
     * @param number $order_id            
     */
    private function AssistantOrderCommissionCalculate($order_out_trade_no = "", $order_id = 0)
    {}

    /**
     * 订单退款 更新佣金金额
     *
     * @param unknown $order_id            
     */
    private function UpdateAssistantOrderCommissionRefund($order_id)
    {
     
    }

    /**
     * 订单交易完成发放订单的佣金
     *
     * @param unknown $order_id            
     */
    private function UpdateAssistantOrderCommission($order_id)
    {}

    /**
     * ***********************************************招商员的账户计算--End******************************************************
     */
    /**
     * 查询店铺的退货设置
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::getShopReturnSet()
     */
    public function getShopReturnSet($shop_id)
    {
        $shop_return = new NsOrderShopReturnModel();
        $shop_return_obj = $shop_return->get($shop_id);
        if (empty($shop_return_obj)) {
            $data = array(
                "shop_id" => $shop_id,
                "create_time" => date("Y-m-d H:i:s", time())
            );
            $shop_return->save($data);
            $shop_return_obj = $shop_return->get($shop_id);
        }
        return $shop_return_obj;
    }

    /**
     *
     * 更新店铺的退货信息
     * (non-PHPdoc)
     *
     * @see \data\api\IShop::updateShopReturnSet()
     */
    public function updateShopReturnSet($shop_id, $address, $real_name, $mobile, $zipcode)
    {
        $shop_return = new NsOrderShopReturnModel();
        $data = array(
            "shop_address" => $address,
            "seller_name" => $real_name,
            "seller_mobile" => $mobile,
            "seller_zipcode" => $zipcode,
            "modify_time" => date("Y-m-d H:i:s", time())
        );
        $result_id = $shop_return->save($data, [
            "shop_id" => $shop_id
        ]);
        return $result_id;
    }

    /**
     * 得到订单的发货信息
     *
     * @param unknown $order_ids            
     */
    public function getOrderGoodsExpressDetail($order_ids, $shop_id)
    {
        $order_goods_model = new NsOrderGoodsModel();
        $order_model = new NsOrderModel();
        $order_goods_express = new NsOrderGoodsExpressModel();
        // 查询订单的订单项的商品信息
        $order_goods_list = $order_goods_model->where(" order_id in ($order_ids)")->select();
        
        for ($i = 0; $i < count($order_goods_list); $i ++) {
            $order_id = $order_goods_list[$i]["order_id"];
            $order_goods_id = $order_goods_list[$i]["order_goods_id"];
            $order_obj = $order_model->get($order_id);
            $order_goods_list[$i]["order_no"] = $order_obj["order_no"];
            $goods_express_obj = $order_goods_express->where("FIND_IN_SET($order_goods_id,order_goods_id_array)")->select();
            if (! empty($goods_express_obj)) {
                $order_goods_list[$i]["express_company"] = $goods_express_obj[0]["express_company"];
                $order_goods_list[$i]["express_no"] = $goods_express_obj[0]["express_no"];
            } else {
                $order_goods_list[$i]["express_company"] = "";
                $order_goods_list[$i]["express_no"] = "";
            }
        }
        return $order_goods_list;
    }

    /**
     * 通过订单id 得到 该订单的发货物流
     *
     * @param unknown $order_id            
     */
    public function getOrderGoodsExpressList($order_id)
    {
        $order_goods_express_model = new NsOrderGoodsExpressModel();
        $express_list = $order_goods_express_model->getQuery([
            "order_id" => $order_id
        ], "*", "");
        return $express_list;
    }

    /**
     * 订单提货(non-PHPdoc)
     *
     * @see \data\api\IOrder::pickupOrder()
     */
    public function pickupOrder($order_id, $buyer_name, $buyer_phone, $remark)
    {
        $order = new OrderBusiness();
        $retval = $order->pickupOrder($order_id, $buyer_name, $buyer_phone, $remark);
        return $retval;
    }

    /**
     * 查询订单项的物流信息
     *
     * @param unknown $order_goods_id            
     */
    public function getOrderGoodsExpressMessage($express_id)
    {
        try {
            $order_express_model = new NsOrderGoodsExpressModel();
            $express_obj = $order_express_model->get($express_id);
            if (! empty($express_obj)) {
                $order_id = $express_obj["order_id"];
                $order_model = new NsOrderModel();
                // 订单编号
                $order_obj = $order_model->get($order_id);
                $order_no = $order_obj["order_no"];
                $shop_id = $order_obj["shop_id"];
                // 物流公司信息
                $express_company_id = $express_obj["express_company_id"];
                $express_company_model = new NsOrderExpressCompanyModel();
                $express_company_obj = $express_company_model->get($express_company_id);
                // 快递公司编号
                $express_no = $express_company_obj["express_no"];
                // 物流编号
                $send_no = $express_obj["express_no"];
                $kdniao = new Kdniao($shop_id);
                $data = array(
                    "OrderCode" => $order_no,
                    "ShipperCode" => $express_no,
                    "LogisticCode" => $send_no
                );
                $result = $kdniao->getOrderTracesByJson(json_encode($data));
                return json_decode($result, true);
            } else {
                return array(
                    "Success" => false,
                    "Reason" => "订单物流信息有误!"
                );
            }
        } catch (\Exception $e) {
            return array(
                "Success" => false,
                "Reason" => "订单物流信息有误!"
            );
        }
    }

    /**
     * 添加卖家对订单的备注
     *
     * @param unknown $order_goods_id            
     */
    public function addOrderSellerMemo($order_id, $memo)
    {
        $order = new NsOrderModel();
        $data = array(
            'seller_memo' => $memo
        );
        $retval = $order->save($data, [
            'order_id' => $order_id
        ]);
        return $retval;
    }
    /**
     * 获取订单备注信息
     *
     * {@inheritdoc}
     *
     * @see \data\api\IOrder::getOrderRemark()
     */
    public function getOrderSellerMemo($order_id)
    {
        $order = new NsOrderModel();
        $res = $order->getQuery([
            'order_id' => $order_id
        ], "seller_memo", '');
        $seller_memo = "";
        if (! empty($res[0]['seller_memo'])) {
            $seller_memo = $res[0]['seller_memo'];
        }
        return $seller_memo;
    }
	/**
     * 线下店铺扫码订单支付就改变成完成状态；补增支付后发送通知短信
     */
    public function orderFinish()
    {
		/*判断是否发送短信*/
		$statrec=Db::table('ns_order')->where(['pay_status'=>2,'order_status'=>1,'feedback_status'=>0])->field('order_id,order_no,shop_id,order_money')->select();
		foreach($statrec as $k=>$v){
			$phone=Db::table('ns_shop')->where('shop_id',$v['shop_id'])->value('shop_phone');
			if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
				$jie_suan=0;
				$inforec=Db::table('ns_order_goods')->where(['order_id'=>$v['order_id']])->field('goods_id,cost_price,num')->select();
				foreach($inforec as $ks=>$vs){
					$logistics=Db::table('ns_goods')->where(['goods_id'=>$vs['goods_id']])->value('cost_price_logistics');
					$jie_suan=$jie_suan+($logistics+$vs['cost_price'])*$vs['num'];
				}
				$msg='尊敬的商户，您好！商品订单已支付，订单编号：'.$v['order_no'].'，商家结算款'.$jie_suan.'元，请尽快安排发货配送。【积分呗】';
				$smsinfo=luosimaoSmsSend($phone,$msg);
				if ($smsinfo == 'success') {
					$status=Db::table("ns_mobile_msgs")->insert(["mobile"=>$phone,"msg"=>$msg,"status"=>2]);
					$stat=Db::table("ns_order")->where('order_id',$v['order_id'])->update(["feedback_status"=>6]);
				}
			}
		}
		$order = new OrderBusiness();
        //线下扫描支付及虚拟商品支付完成就完成订单
		// 启动事务
		Db::startTrans();
		try{
			$ordrec=Db::table('ns_order')->where(['buyer_message'=>'[shop_scan_pay]','pay_status'=>2,'order_status'=> ['neq',4]])->field('order_id,order_status,pay_status')->select();
			foreach($ordrec as $key=>$val){
				Db::table('ns_order')->where('order_id',$val['order_id'])->update(['order_status'=>4,'finish_time'=>date("Y-m-d H::i:s", time())]);
				$order->addCommission($val['order_id']); //预提记录加入账户
				$goodsrec=Db::table('ns_order_goods')->where(['order_id'=>$val['order_id']])->field('order_goods_id,order_status')->select();
				foreach($goodsrec as $k=>$v){
					Db::table('ns_order_goods')->where('order_goods_id',$v['order_goods_id'])->update(['order_status'=>4]);
					$order->shopSettlement($v['order_goods_id']);
				}
			}
			Db::commit();
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
		}
	}
	/*首单包邮全免*/
	public function firstFree($out_trade_no){
		$OrderModel=new NsOrderModel(); 
		$order_arr=$OrderModel->getQuery(['out_trade_no' => $out_trade_no], '*','order_id asc');
		if(count($order_arr)==1){ 
			  //改为只要充值配置的倍数就可以获得该商品！
				$order_goods=Db::table('ns_order_goods')->where('order_id',$order_arr[0]['order_id'])->select();
				$first_free_num=$records_arr=Db::table('ns_goods')->where('goods_id',$order_goods[0]['goods_id'])->value('first_free_num');
				//毛利率ratio要大于40%。提现限制已加！
				$totprice=0;$totcost_price=0;$goods_money=0;
				foreach($order_goods as $k=>$v){
					$totprice+=$v['price']*$v['num'];
					$totcost_price+=$v['cost_price']*$v['num'];
					$goods_money+=$v['goods_money']*$v['num'];
				}
				$ratio=($totprice-$totcost_price)/$totprice;
				//改为1小时内的充值额。要求$money/$order_goods[0]['goods_money'])>3
				$condition = array(
				'uid' => $order_goods[0]['buyer_id'],
				'account_type' => 2,
				'from_type' => 4,
				'create_time' => array('between time',[date("Y-m-d H:i:s",time()-3600),date("Y-m-d H:i:s", time())]),
				'text'=>array('notlike','%shou-dang-mian%'),
				);
				$records_arr=Db::table('ns_member_account_records')->where($condition)->select();
				$money=0;
				foreach($records_arr as $k=>$v){
					$money+=$v['number'];
				}
				$jplus_level=Db::table('ns_member')->where('uid',$order_goods[0]['buyer_id'])->value('jplus_level');//必须是jplus会员
				//判断条件结束
				if($first_free_num>=2 && $jplus_level>0 && $ratio>0.4 && ($money/$goods_money)>=$first_free_num){
					//改变订单信息
					Db::table('ns_order')->where('order_id',$order_arr[0]['order_id'])->update(['seller_memo'=>'首单包邮全免活动！','order_money'=>0,'promotion_money'=>$order_arr[0]['pay_money'],'pay_money'=>0]);
					foreach($order_goods as $k=>$v){
						Db::table('ns_order_goods')->where('order_goods_id',$v['order_goods_id'])->update(['memo'=>'首单包邮全免活动！']);
					}
					$onepay=Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->find();//0元利润不会产生二级分销，注意测试
					Db::table('ns_order_payment')->where('id',$onepay['id'])->update(['pay_body'=>$onepay['pay_body'].'；首单包邮全免','pay_money'=>0]);
					$count=0;//给充值记录做标记，以控制提现
					foreach($records_arr as $k=>$v){
						$count+=$v['number'];
						Db::table('ns_member_account_records')->where('id',$v['id'])->update(['text'=>$v['text'].'_shou-dang-mian']);
						if(($count/$order_goods[0]['goods_money']) >=$first_free_num){ //充值金额是购物金额的倍数后台配置
							break;
						}
					}
				}
		}
	}
	/*新人专享 后台标识。*/
	public function userAssemble($out_trade_no){
		$OrderModel=new NsOrderModel();
		$order_arr=$OrderModel->getQuery(['out_trade_no' => $out_trade_no], '*','order_id asc');
		if(count($order_arr)==1){
			$order_goods=Db::table('ns_order_goods')->where('order_id',$order_arr[0]['order_id'])->select();
			if(count($order_goods)==1){ //只会一条一条的购买。getvalue显示通兑积分是否够
				//查看商品对应的新人专享信息
				$oneGoods=Db::table('ns_goods')->where('goods_id',$order_goods[0]['goods_id'])->find();
				//还要查看该商品以前的购买记录。格式如：goods_id:52;goods_id:12
				$buy_num=0;
				$user_info=Db::table('ns_member')->where('uid',$order_arr[0]['buyer_id'])->find();
				if(!empty($user_info['zero_notes'])){
					$note_arr=explode(';',$user_info['zero_notes']);
					foreach($note_arr as $k=>$v){
						$two_arr=explode(':',$v);
						if($two_arr[1]==$order_goods[0]['goods_id']){
							$buy_num+=$order_goods[0]['num'];
						}
					}
				}
				if($oneGoods['zero_point_num']>1){$exc_ratio=1;} else {$exc_ratio=$oneGoods['zero_point_num'];}
				$now_excpoint=$exc_ratio*$order_goods[0]['price'];//需要支付的通兑积分
				$user_excpoint=Db::table('ns_member_account')->where('uid',$order_arr[0]['buyer_id'])->value('exc_point');
				$freight_arr=explode('/',$oneGoods['zero_num']);
				$noexc='';
				if($user_excpoint<$now_excpoint){$noexc='通兑积分不够，正常价格交易';}
				//判断条件结束
				if($freight_arr[0]>0 && $oneGoods['zero_point_num']>0 && $user_excpoint>=$now_excpoint && $order_goods[0]['memo']==''){   //满足条件变更订单
					//改变订单信息 需增加显示用信息；要支付配置邮费；考虑二级分销将order_money变更；支付成功后扣减通兑积分
					/*判断该活动的销售量决定邮费的计算开始*/
					$goodscount=Db::table('ns_order_goods')->where(['goods_id'=>$order_goods[0]['goods_id'],'memo'=>'2019已兑换新人专享！'])->count();
					if(!empty($freight_arr[1]) && $goodscount<$freight_arr[1] && $buy_num<1){  //后台读取小于等于时免邮费，免邮费限一件
						$totmoney=0;
					} else {
						$totmoney=$freight_arr[0]*$order_goods[0]['num'];
					}
					/*判断该活动的销售量决定邮费的计算结束*/
					Db::table('ns_order')->where('order_id',$order_arr[0]['order_id'])->update(['seller_memo'=>'2019新人专享！','promotion_money'=>$order_arr[0]['pay_money']-$totmoney,'shipping_money'=>$totmoney,'order_money'=>$totmoney,'pay_money'=>$totmoney]);
					Db::table('ns_order_goods')->where('order_goods_id',$order_goods[0]['order_goods_id'])->update(['memo'=>'2019新人专享！']);
					$onepay=Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->find();
					Db::table('ns_order_payment')->where('id',$onepay['id'])->update(['pay_body'=>$onepay['pay_body'].'；2019新人专享','pay_money'=>$totmoney]);
					//添加购买记录
					if(empty($user_info['zero_notes'])){
						$notes='goods_id:'.$order_goods[0]['goods_id'];
					} else {
						$notes=$user_info['zero_notes'].';goods_id:'.$order_goods[0]['goods_id'];
					}
					Db::table('ns_member')->where('uid',$order_goods[0]['buyer_id'])->update(['zero_notes'=>$notes]);
				}
				return $noexc;
			}
		}
	}
	/*扣减通兑积分*/
	public function exchangePoint(){
		$excorder=Db::table('ns_order')->where(['seller_memo'=>'2019新人专享！','pay_status'=>2])->field('order_id')->select();
		foreach($excorder as $key=>$val){
			Db::table('ns_order')->where(['order_id'=>$val['order_id']])->update(['seller_memo'=>'2019已兑换新人专享！']);
			$ordergoods=Db::table('ns_order_goods')->where(['order_id'=>$val['order_id']])->select();
			foreach($ordergoods as $k=>$v){
				$zero_point_num=Db::table('ns_goods')->where(['goods_id'=>$v['goods_id']])->value('zero_point_num');
				Db::table('ns_order_goods')->where(['order_goods_id'=>$v['order_goods_id']])->update(['memo'=>'2019已兑换新人专享！']);
				Db::table('ns_member_account')->where(['uid'=>$v['buyer_id']])->setDec('exc_point',$zero_point_num*$v['price']);
				Db::table("ns_give_point_records")->insert(["uid"=>$v['buyer_id'],"number"=>-$zero_point_num*$v['price'],"text"=>'兑换商品扣减通兑积分',"create_time"=>date("Y-m-d H:i:s", time())]);
			}
		}
	}
	/*通兑积分抵扣10%的支付后处理*/
	public function excPointAfter(){
		$excarr=Db::table('ns_order_payment')->where('pay_body','like','%deduct_from_exchangePoint%')->select();
		foreach($excarr as $key=>$val){
			if($val['pay_status']==1){
				$textarr=explode('deduct_from_exchangePoint:',$val['pay_body']);
				$usedarr=explode(';useExcpoint:',$textarr[1]);
				if(!empty($usedarr[0]) && !empty($usedarr[1])){
					Db::table('ns_order_payment')->where('id',$val['id'])->update(['pay_body'=>$textarr[0]]);
					Db::table("ns_give_point_records")->insert(["uid"=>$this->uid,"number"=>-$usedarr[1],"text"=>'通兑积分抵扣支付金额'.$usedarr[0],"create_time"=>date("Y-m-d H:i:s", time())]);
					Db::table('ns_member_account')->where(['uid'=>$this->uid])->setDec('exc_point',$usedarr[1]);
				}
			}
		} //未支付的还要处理
	}
	/**
     * 变更订单信息
     * @see \data\api\IOrder::getOrderRemark()
     */
    public function orderchange($type,$out_trade_no,$account,$usemoney)
    {
		Db::startTrans();
		try{
			//$pay_cash = $usemoney;
			$pay_money=0;
			$pay_status=0;
			$ordrec=Db::table('ns_order')->where(['out_trade_no'=>$out_trade_no])->field('order_id,shop_id,point,user_platform_money,pay_money,order_status,pay_status')->select();
			foreach($ordrec as $key=>$val){
				$pay_money+=$val['pay_money'];
				if($val['pay_status']==2){$pay_status=2;}
			}
			if($pay_status==2){
				return array('code'=>0,'message'=>'订单已经支付！');
			}
			if($type==5){  //余额支付
				if($pay_money > $account['balance']){
					return array('code'=>0,'message'=>'余额不足！');
				}
			} elseif($type==6){  //积分支付
				$convert_rate = Db::table("ns_point_config")->where("shop_id",0)->value('convert_rate');
				$orderpoint=round($pay_money/$convert_rate,2);
				if($orderpoint > $account['point']){
					return array('code'=>0,'message'=>'订单所需'.$orderpoint.'积分，积分不足！');
				}
			} elseif($type==7){  //余额加积分支付
				if($usemoney > $account['balance']){
					return array('code'=>0,'message'=>'余额不足！');
				}
				$convert_rate = Db::table("ns_point_config")->where("shop_id",0)->value('convert_rate');
				$cha=$pay_money-$usemoney;
				if($cha>0){
					$chapoint=round($cha/$convert_rate,2);
					if($account['point'] < $chapoint){ return array('code'=>0,'message'=>'积分不足！'); } //总数控制
				}
				else {$type==5;}
			}
			$nowtime = date("Y-m-d H::i:s", time());
			$orderBusiness=new OrderBusiness();
			foreach($ordrec as $key=>$val){
				$later_point=0; //订单另外的积分
				if($type==5){
					Db::table('ns_order')->where('order_id',$val['order_id'])->update(['payment_type'=>$type,'pay_time'=>$nowtime,'user_platform_money'=>$val['pay_money'],'pay_money'=>0,'order_status'=>1,'pay_status'=>2]);
				} elseif($type==6){
					$pay_point=round($val['pay_money']/$convert_rate,2);
					$valpoint=$val['point']+$pay_point;
					Db::table('ns_order')->where('order_id',$val['order_id'])->update(['payment_type'=>$type,'pay_time'=>$nowtime,'point'=>$valpoint,'pay_money'=>0,'order_status'=>1,'pay_status'=>2]); 
				} elseif($type==7){
					if($usemoney>=$val['pay_money']){
						$user_platform_money=$val['user_platform_money']+$val['pay_money'];
						Db::table('ns_order')->where('order_id',$val['order_id'])->update(['payment_type'=>$type,'pay_time'=>$nowtime,'user_platform_money'=>$user_platform_money,'pay_money'=>0,'order_status'=>1,'pay_status'=>2]);
						$usemoney-=$val['pay_money'];
						$account_type=2;$number=-$val['pay_money'];
						Db::table('ns_member_account')->where('id',$account['id'])->setDec('balance',$val['pay_money']);
					} else {
						if($usemoney>0){
							$onepoint=round(($val['pay_money']-$usemoney)/$convert_rate,2);
							$nowpoint=$onepoint+$val['point'];
							$user_platform_money=$val['user_platform_money']+$usemoney;
							Db::table('ns_order')->where('order_id',$val['order_id'])->update(['payment_type'=>$type,'pay_time'=>$nowtime,'point'=>$nowpoint,'user_platform_money'=>$user_platform_money,'pay_money'=>0,'order_status'=>1,'pay_status'=>2]); 
							$account_type=2;$number=-$usemoney;$later_point=$onepoint; //添加一个订单另外需要的积分
							Db::table('ns_member_account')->where('id',$account['id'])->setDec('balance',$usemoney);
							Db::table('ns_member_account')->where('id',$account['id'])->setDec('point',$onepoint);
							$usemoney=0;
						} else {
							$onepoint=round($val['pay_money']/$convert_rate,2);
							$nowpoint=$onepoint+$val['point'];
							Db::table('ns_order')->where('order_id',$val['order_id'])->update(['payment_type'=>$type,'pay_time'=>$nowtime,'point'=>$nowpoint,'pay_money'=>0,'order_status'=>1,'pay_status'=>2]);
							$account_type=1;$number=-$onepoint;
							Db::table('ns_member_account')->where('id',$account['id'])->setDec('point',$onepoint);
						}
					}
				}
				Db::table('ns_order_action')->where('order_id',$val['order_id'])->update(['order_status'=>1,'order_status_text'=>'待发货','action_time'=>$nowtime]);
				$goodsrec=Db::table('ns_order_goods')->where(['order_id'=>$val['order_id']])->field('order_goods_id')->select();
				foreach($goodsrec as $k=>$v){
					Db::table('ns_order_goods')->where('order_goods_id',$v['order_goods_id'])->update(['order_status'=>1]);
				}
				$paymentrec=Db::table('ns_order_payment')->where(['out_trade_no'=>$out_trade_no])->field('id')->select();
				foreach($paymentrec as $k=>$v){
					Db::table('ns_order_payment')->where('id',$v['id'])->update(['pay_money'=>0,'pay_status'=>1,'pay_time'=>$nowtime,'pay_type'=>$type]);
				}
				if($type==5){ $account_type=2;$number=-$val['pay_money']; } elseif($type==6){$account_type=1;$number=-$pay_point;}
				$data=array('uid' =>$this->uid,'shop_id' =>$val['shop_id'],'account_type' =>$account_type,'sign' =>0,'number' =>$number,'from_type' =>1,'data_id' =>$val['order_id'],'text' =>'商城订单','create_time' =>$nowtime);
				Db::table('ns_member_account_records')->insert($data);
				if($later_point>0){
					$data2=array('uid' =>$this->uid,'shop_id' =>$val['shop_id'],'account_type' =>1,'sign' =>0,'number' =>-$later_point,'from_type' =>1,'data_id' =>$val['order_id'],'text' =>'商城订单使用积分','create_time' =>$nowtime);
					Db::table('ns_member_account_records')->insert($data2);
				}
				$orderBusiness->orderCommission($val['order_id']); //支付完成执行分销函数
			}
			if($type==5){
				Db::table('ns_member_account')->where('id',$account['id'])->setDec('balance',$pay_money);
				$res=array('code'=>1,'message'=>'余额支付成功！');
			} elseif($type==6){
				Db::table('ns_member_account')->where('id',$account['id'])->setDec('point',$orderpoint);
				$res=array('code'=>1,'message'=>'积分支付成功！');
			} elseif($type==7){ $res=array('code'=>1,'message'=>'余额+积分支付成功！'); }
			// 提交事务
			Db::commit();
			return $res;	//order_action未见加入
		} catch (\Exception $e) {
			// 回滚事务
			Db::rollback();
			return array('code'=>0,'message'=>'支付失败！');
		}
		
    }
}