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
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace app\api\controller;
use data\service\Order as OrderService;


class Order extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new OrderService();
    }
    
    /**
     * 添加订单
     *
     * @param unknown $data
     */
    public function addOrder(){
        $data = isset($this->request_common_array['data']) ? $this->request_common_array['data'] : '';
        $res = $this->service->addOrder($data);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取订单详情
     *
     * @param unknown $order_id
    */
    public function getOrderDetail(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $retval = $this->service->getOrderDetail($order_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取订单列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getOrderList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getOrderList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 订单创建
     *
     * @param unknown $order_type
     * @param unknown $out_trade_no
     * @param unknown $pay_type
     * @param unknown $shipping_type
     * @param unknown $order_from
     * @param unknown $buyer_ip
     * @param unknown $buyer_message
     * @param unknown $buyer_invoice
     * @param unknown $shipping_time
     * @param unknown $receiver_mobile
     * @param unknown $receiver_province
     * @param unknown $receiver_city
     * @param unknown $receiver_district
     * @param unknown $receiver_address
     * @param unknown $receiver_zip
     * @param unknown $receiver_name
     * @param unknown $point
     * @param unknown $point_money
     * @param unknown $coupon_money
     * @param unknown $coupon_id
     * @param unknown $user_money
     * @param unknown $promotion_money
     * @param unknown $shipping_money
     * @param unknown $pay_money
     * @param unknown $give_point
     * @param unknown $goods_sku_list
     * @param unknown $platform_money
    */
    public function orderCreate(){  //这个函数已经不对！
        $order = new OrderService();
        $member = new Member();

        $use_coupon = request()->post('use_coupon', 0); // 优惠券
        $integral = request()->post('integral', 0); // 积分
        $goods_sku_list = request()->post('goods_sku_list', ''); // 商品列表
        $leavemessage = request()->post('leavemessage', 'org\Filter::safeHtml'); // 留言
        $user_money = request()->post("account_balance", 0); // 使用余额
        $pay_type = request()->post("pay_type", 1); // 支付方式
        $buyer_invoice = request()->post("buyer_invoice", ""); // 发票
        $pick_up_id = request()->post("pick_up_id", 0); // 自提点
        $shipping_type = 1; // 配送方式，1：物流，2：自提
        $express_company_id = request()->post("express_company_id", 0); // 物流公司
        if ($pick_up_id != 0) {
            $shipping_type = 2;
        }
        $shipping_time = date("Y-m-d H::i:s", time());
        $address = $member->getDefaultExpressAddress();
//对用户购物车商品按店铺分类，然后生成不同的订单！
        $goods_sku_arr=explode(',',$goods_sku_list);
        $cart_shop_arr=array();
        foreach($goods_sku_arr as $key=>$val){
            $once=explode(':',$val);
            $shopid=\think\Db::table('ns_cart')->where(['buyer_id'=>$this->uid,'sku_id'=>$once[0]])->value('shop_id');
            if(empty($cart_shop_arr[$shopid])){$cart_shop_arr[$shopid]=$val;}
            else {$cart_shop_arr[$shopid].=','.$val;}
        }
        $out_trade_no = $order->getOrderTradeNo();//获取支付编号
//循环支付生成订单
        foreach($cart_shop_arr as $key=>$val){
            $retval=$order->orderCreate('1', $out_trade_no, $pay_type, $shipping_type, '1', 1, $leavemessage, $buyer_invoice, $shipping_time, $address['mobile'], $address['province'], $address['city'], $address['district'], $address['address'], $address['zip_code'], $address['consigner'], $integral, $use_coupon, 0, $val, $user_money, $pick_up_id,$express_company_id);
            $user_money=bcsub($user_money,$retval[1],2);
            if ($retval[0] > 0) {
                $order->deleteCart($val, $this->uid);
                $_SESSION['order_tag'] = "";
            } else {
                return $retval[0];break;
            }
        }
        return $out_trade_no;

        $order_type = isset($this->request_common_array['order_type']) ? $this->request_common_array['order_type'] : '';
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $pay_type = isset($this->request_common_array['pay_type']) ? $this->request_common_array['pay_type'] : '';
        $shipping_type = isset($this->request_common_array['shipping_type']) ? $this->request_common_array['shipping_type'] : '';
        $order_from = isset($this->request_common_array['order_from']) ? $this->request_common_array['order_from'] : '';
        $buyer_ip = isset($this->request_common_array['buyer_ip']) ? $this->request_common_array['buyer_ip'] : '';
        $buyer_message = isset($this->request_common_array['buyer_message']) ? $this->request_common_array['buyer_message'] : '';
        $buyer_invoice = isset($this->request_common_array['buyer_invoice']) ? $this->request_common_array['buyer_invoice'] : '';
        $shipping_time = isset($this->request_common_array['shipping_time']) ? $this->request_common_array['shipping_time'] : '';
        $receiver_mobile = isset($this->request_common_array['receiver_mobile']) ? $this->request_common_array['receiver_mobile'] : '';
        $receiver_province = isset($this->request_common_array['receiver_province']) ? $this->request_common_array['receiver_province'] : '';
        $receiver_city = isset($this->request_common_array['receiver_city']) ? $this->request_common_array['receiver_city'] : '';
        $receiver_district = isset($this->request_common_array['receiver_district']) ? $this->request_common_array['receiver_district'] : '';
        $receiver_address = isset($this->request_common_array['receiver_address']) ? $this->request_common_array['receiver_address'] : '';
        $receiver_zip = isset($this->request_common_array['receiver_zip']) ? $this->request_common_array['receiver_zip'] : '';
        $receiver_name = isset($this->request_common_array['receiver_name']) ? $this->request_common_array['receiver_name'] : '';
        $point = isset($this->request_common_array['point']) ? $this->request_common_array['point'] : '';
        $coupon_id = isset($this->request_common_array['coupon_id']) ? $this->request_common_array['coupon_id'] : '';
        $user_money = isset($this->request_common_array['user_money']) ? $this->request_common_array['user_money'] : '';
        $goods_sku_list = isset($this->request_common_array['goods_sku_list']) ? $this->request_common_array['goods_sku_list'] : '';
        $platform_money = isset($this->request_common_array['platform_money']) ? $this->request_common_array['platform_money'] : '';
        $pick_up_id = isset($this->request_common_array['pick_up_id']) ? $this->request_common_array['pick_up_id'] : '';
        $shipping_company_id = isset($this->request_common_array['shipping_company_id']) ? $this->request_common_array['shipping_company_id'] : '';
        $coin = isset($this->request_common_array['coin']) ? $this->request_common_array['coin'] : 0;
        $res = $this->service->orderCreate($order_type, $out_trade_no, $pay_type, $shipping_type, $order_from, $buyer_ip, $buyer_message, $buyer_invoice, $shipping_time, $receiver_mobile, $receiver_province, $receiver_city, $receiver_district, $receiver_address, $receiver_zip, $receiver_name, $point, $coupon_id, $user_money, $goods_sku_list, $platform_money, $pick_up_id, $shipping_company_id, $coin);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 订单物流发货
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id_array
     *            //订单项ID列 ','隔开
     * @param unknown $express_name
     *            //物流公司名称
     * @param unknown $shipping_type
     *            //物流方式
     * @param unknown $express_company_id
     *            //物流公司ID
     * @param unknown $express_no
     *            //运单编号
    */
    public function orderDelivery(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id_array = isset($this->request_common_array['order_goods_id_array']) ? $this->request_common_array['order_goods_id_array'] : '';
        $express_name = isset($this->request_common_array['express_name']) ? $this->request_common_array['express_name'] : '';
        $shipping_type = isset($this->request_common_array['shipping_type']) ? $this->request_common_array['shipping_type'] : '';
        $express_company_id = isset($this->request_common_array['express_company_id']) ? $this->request_common_array['express_company_id'] : '';
        $express_no = isset($this->request_common_array['express_no']) ? $this->request_common_array['express_no'] : '';
        $res = $this->service->orderDelivery($order_id, $order_goods_id_array, $express_name, $shipping_type, $express_company_id, $express_no);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 订单不执行物流发货
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id_array
    */
    public function orderGoodsDelivery(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id_array = isset($this->request_common_array['order_goods_id_array']) ? $this->request_common_array['order_goods_id_array'] : '';
        $res = $this->service->orderGoodsDelivery($order_id, $order_goods_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 订单执行交易关闭
     *
     * @param unknown $order_id
    */
    public function orderClose(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $res = $this->service->orderClose($order_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 订单执行交易完成
     *
     * @param unknown $orderid
    */
    public function orderComplete(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $res = $this->service->firstComplete($order_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 订单线上支付完成
     *
     * @param unknown $order_pay_no
     * @param unknown $pay_type
    */
    public function orderOnLinePay(){
        $order_pay_no = isset($this->request_common_array['order_pay_no']) ? $this->request_common_array['order_pay_no'] : '';
        $pay_type = isset($this->request_common_array['pay_type']) ? $this->request_common_array['pay_type'] : '';
        $res = $this->service->orderOnLinePay($order_pay_no, $pay_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 订单线下支付或后期支付
     *
     * @param unknown $order_id
     * @param unknown $status
     *            0:订单支付 1：交易完成
    */
    public function orderOffLinePay(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $pay_type = isset($this->request_common_array['pay_type']) ? $this->request_common_array['pay_type'] : '';
        $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
        $res = $this->service->orderOffLinePay($order_id, $pay_type, $status);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询订单
     *
     * @param unknown $where
     * @param string $fields
    */
    public function orderQuery(){
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $fields = isset($this->request_common_array['fields']) ? $this->request_common_array['fields']: '*';
        $retval = $this->service->orderQuery($where, $fields);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 订单金额调整
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id_adjust_array
     *            订单项数列 order_goods_id,adjust_money;order_goods_id,adjust_money
     * @param unknown $shipping_fee
    */
    public function orderMoneyAdjust(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id_adjust_array = isset($this->request_common_array['order_goods_id_adjust_array']) ? $this->request_common_array['order_goods_id_adjust_array'] : '';
        $shipping_fee = isset($this->request_common_array['shipping_fee']) ? $this->request_common_array['shipping_fee'] : '';
        $res = $this->service->orderMoneyAdjust($order_id, $order_goods_id_adjust_array, $shipping_fee);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询订单项退款信息
     *
     * @param unknown $order_goods_id
    */
    public function getOrderGoodsRefundInfo(){
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $retval = $this->service->getOrderGoodsRefundInfo($order_goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id
    */
    public function getOrderGoods(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $retval = $this->service->getOrderGoods($order_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询订单的订单项列表
     *
     * @param unknown $order_id
    */
    public function getOrderGoodsInfo(){
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $retval = $this->service->getOrderGoodsInfo($order_goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 买家退款申请
     *
     * @param unknown $order_id
     *            订单ID
     * @param unknown $order_goods_id_array
     *            订单项ID (','隔开)
     * @param unknown $refund_type
     * @param unknown $refund_require_money
     *            //需要退款金额
     * @param unknown $refund_reason
     *            //退款原因
     * @return number|Exception|Ambigous <number, \think\false>
    */
    public function orderGoodsRefundAskfor(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $refund_type = isset($this->request_common_array['refund_type']) ? $this->request_common_array['refund_type'] : '';
        $refund_require_money = isset($this->request_common_array['refund_require_money']) ? $this->request_common_array['refund_require_money'] : '';
        $refund_reason = isset($this->request_common_array['refund_reason']) ? $this->request_common_array['refund_reason'] : '';
        $res = $this->service->orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 买家取消退款
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id
    */
    public function orderGoodsCancel(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->orderGoodsCancel($order_id, $order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 买家退货
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id
     * @param unknown $refund_shipping_company
     *            //退货物流公司名称
     * @param unknown $refund_shipping_code
     *            //退货物流运单号
    */
    public function orderGoodsReturnGoods(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $refund_shipping_company = isset($this->request_common_array['refund_shipping_company']) ? $this->request_common_array['refund_shipping_company'] : '';
        $refund_shipping_code = isset($this->request_common_array['refund_shipping_code']) ? $this->request_common_array['refund_shipping_code'] : '';
        $res = $this->service->orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 卖家同意买家退款申请
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id
    */
    public function orderGoodsRefundAgree(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->orderGoodsRefundAgree($order_id, $order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 卖家永久决绝退款
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id
    */
    public function orderGoodsRefuseForever(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->orderGoodsRefuseForever($order_id, $order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 卖家拒绝本次退款
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id
    */
    public function orderGoodsRefuseOnce(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->orderGoodsRefuseOnce($order_id, $order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 卖家确认收货
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id
    */
    public function orderGoodsConfirmRecieve(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->orderGoodsConfirmRecieve($order_id, $order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 卖家确认退款
     *
     * @param unknown $order_id
     * @param unknown $order_goods_id
     * @param unknown $refund_real_money
    */
    public function orderGoodsConfirmRefund(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $refund_real_money = isset($this->request_common_array['refund_real_money']) ? $this->request_common_array['refund_real_money'] : '';
        $res = $this->service->orderGoodsConfirmRecieve($order_id, $order_goods_id, $refund_real_money);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取对应sku列表价格
     *
     * @param unknown $goods_sku_list
    */
    public function getGoodsSkuListPrice(){
        $goods_sku_list = isset($this->request_common_array['goods_sku_list']) ? $this->request_common_array['goods_sku_list'] : '';
        $retval = $this->service->getGoodsSkuListPrice($goods_sku_list);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取邮费
     *
     * @param unknown $goods_sku_list
     * @param unknown $province
     * @param unknown $city
     * @return Ambigous <unknown, number>
    */
    public function getExpressFee(){
        $goods_sku_list = isset($this->request_common_array['goods_sku_list']) ? $this->request_common_array['goods_sku_list'] : '';
        $express_company_id = isset($this->request_common_array['express_company_id']) ? $this->request_common_array['express_company_id'] : '';
        $province = isset($this->request_common_array['province']) ? $this->request_common_array['province'] : '';
        $city = isset($this->request_common_array['city']) ? $this->request_common_array['city'] : '';
        $retval = $this->service->getExpressFee($goods_sku_list, $express_company_id, $province, $city);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取支付编号
    */
    public function getOrderTradeNo(){
        $retval = $this->service->getOrderTradeNo();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 订单实际退款金额
     *
     * @param unknown $order_goods_id
     *            //订单商品ID（订单项）
    */
    public function orderGoodsRefundMoney(){
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->orderGoodsRefundMoney($order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取用户可使用优惠券
     *
     * @param unknown $goods_sku_list
     *            商品sku列表 skuid:num,skuid:num
    */
    public function getMemberCouponList(){
        $goods_sku_list = isset($this->request_common_array['goods_sku_list']) ? $this->request_common_array['goods_sku_list'] : '';
        $retval = $this->service->getMemberCouponList($goods_sku_list);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取订单新的支付流水号
     *
     * @param unknown $order_id
    */
    public function getOrderNewOutTradeNo(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $retval = $this->service->getOrderNewOutTradeNo($order_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取购买商品可用积分
     *
     * @param unknown $goods_sku_list
    */
    public function getGoodsSkuListUsePoint(){
        $goods_sku_list = isset($this->request_common_array['goods_sku_list']) ? $this->request_common_array['goods_sku_list'] : '';
        $retval = $this->service->getGoodsSkuListUsePoint($goods_sku_list);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 订单收货
     *
     * @param unknown $order_id
    */
    public function OrderTakeDelivery(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $res = $this->service->OrderTakeDelivery($order_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除购物车
     *
     * @param unknown $gooods_sku_list
     * @param unknown $uid
    */
    public function deleteCart(){
        $gooods_sku_list = isset($this->request_common_array['gooods_sku_list']) ? $this->request_common_array['gooods_sku_list'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->deleteCart($gooods_sku_list, $uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取某种条件下订单数量
     *
     * @param unknown $conditon
    */
    public function getOrderCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getOrderCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取某种条件 订单总金额（元）
    */
    public function getPayMoneySum(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $date = isset($this->request_common_array['date']) ? $this->request_common_array['date'] : '';
        $retval = $this->service->getPayMoneySum($condition, $date);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取某种条件 订单量（件）
     *
     * @param unknown $condition
    */
    public function getGoodsNumSum(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $date = isset($this->request_common_array['date']) ? $this->request_common_array['date'] : '';
        $retval = $this->service->getGoodsNumSum($condition, $date);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取具体配送状态信息
     *
     * @param unknown $shipping_status_id
     * @return Ambigous <NULL, multitype:string >
    */
    static public function getShippingInfo(){
//        $shipping_status_id = isset($this->request_common_array['shipping_status_id']) ? $this->request_common_array['shipping_status_id'] : '';
//        $retval = $this->service->getShippingInfo($shipping_status_id);
//        if($retval){
//            return $this->outMessage($retval);
//        }else{
//            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
//        }
    }
    
    /**
     * 获取具体支付状态信息
     *
     * @param unknown $pay_status_id
     * @return multitype:multitype:string |string
    */
    static public function getPayStatusInfo(){
//        $pay_status_id = isset($this->request_common_array['pay_status_id']) ? $this->request_common_array['pay_status_id'] : '';
//        $retval = $this->service->getPayStatusInfo($pay_status_id);
//        if($retval){
//            return $this->outMessage($retval);
//        }else{
//            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
//        }
    }
    
    /**
     * 获取订单各状态数量
    */
    static public function getOrderStatusNum(){
//        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
//        $retval = $this->service->getOrderStatusNum($condition);
//        if($retval){
//            return $this->outMessage($retval);
//        }else{
//            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
//        }
    }
    
    /**
     * 商品评价-添加
     *
     * @param unknown $dataList
     *            评价内容的 数组
     * @return Ambigous <multitype:, \think\false>
    */
    public function addGoodsEvaluate(){
        $data_arr = isset($this->request_common_array['data_arr']) ? $this->request_common_array['data_arr'] : '';
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $res = $this->service->addGoodsEvaluate($data_arr, $order_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
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
    public function addGoodsEvaluateExplain(){
        $explain_first = isset($this->request_common_array['explain_first']) ? $this->request_common_array['explain_first'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->addGoodsEvaluateExplain($explain_first, $order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
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
    public function addGoodsEvaluateAgain(){
        $again_content = isset($this->request_common_array['again_content']) ? $this->request_common_array['again_content'] : '';
        $again_image_list = isset($this->request_common_array['again_image_list']) ? $this->request_common_array['again_image_list'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->addGoodsEvaluateAgain($again_content, $again_image_list, $order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
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
    public function addGoodsEvaluateAgainExplain(){
        $again_explain = isset($this->request_common_array['again_explain']) ? $this->request_common_array['again_explain'] : '';
        $order_goods_id = isset($this->request_common_array['order_goods_id']) ? $this->request_common_array['order_goods_id'] : '';
        $res = $this->service->addGoodsEvaluateAgainExplain($again_explain, $order_goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取指定订单的评价信息
     *
     * @param unknown $orderid
     *            订单ID
    */
    public function getOrderEvaluateByOrder($order_id){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $retval = $this->service->getOrderEvaluateByOrder($order_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取指定会员的评价信息
     *
     * @param unknown $uid
     *            会员ID
    */
    public function getOrderEvaluateByMember($uid){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getOrderEvaluateByMember($uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
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
    public function getOrderEvaluateDataList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getOrderEvaluateDataList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
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
    public function getOrderEvaluateList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getOrderEvaluateList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改订单数据
     *
     * @param unknown $order_id
     * @param unknown $data
    */
    public function modifyOrderInfo(){
        $data = isset($this->request_common_array['data']) ? $this->request_common_array['data'] : '';
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $res = $this->service->modifyOrderInfo($data, $order_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取店铺订单销售统计（统计店铺订单账户）
     *
     * @param unknown $shop_id
    */
    public function getShopOrderStatics(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $retval = $this->service->getShopOrderStatics($shop_id, $start_time, $end_time);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店铺在一段时间之内账户列表
     *
     * @param unknown $shop_id
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $page_index
     * @param unknown $page_size
    */
    public function getShopOrderAccountList(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $retval = $this->service->getShopOrderAccountList($shop_id, $start_time, $end_time, $page_index, $page_size);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店铺在一段时间之内订单退款列表
     *
     * @param unknown $shop_id
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $page_index
     * @param unknown $page_size
    */
    public function getShopOrderRefundList(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $retval = $this->service->getShopOrderRefundList($shop_id, $start_time, $end_time, $page_index, $page_size);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店铺订单账户详情
     *
     * @param unknown $shop_id
    */
    public function getShopOrderAccountDetail(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopOrderAccountDetail($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 订单销售概况
     *
     * @param unknown $shop_id
     * @param unknown $start_date
     * @param unknown $end_date
    */
    public function getShopAccountCountInfo($shop_id){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopAccountCountInfo($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 商品销售列表
     *
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
    */
    public function getShopGoodsSalesList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopGoodsSalesList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 所有商品销售情况
     *
     * @param unknown $condition
    */
    public function getShopGoodsSalesQuery(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $start_date = isset($this->request_common_array['start_date']) ? $this->request_common_array['start_date'] : 0;
        $end_date = isset($this->request_common_array['end_date']) ? $this->request_common_array['end_date'] : '';
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopGoodsSalesQuery($shop_id, $start_date, $end_date, $condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询时间内的下单金额
    */
    public function getShopSaleSum(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopSaleSum($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询时间内的下单量
    */
    public function getShopSaleNumSum(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopSaleNumSum($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询店铺的退货设置
     *
     * @param unknown $shop_id
    */
    public function getShopReturnSet(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopReturnSet($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改店铺的退货这是
     *
     * @param unknown $shop_id
     * @param unknown $address
     * @param unknown $real_name
     * @param unknown $mobile
     * @param unknown $zipcode
    */
    public function updateShopReturnSet(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $address = isset($this->request_common_array['address']) ? $this->request_common_array['address'] : '';
        $real_name = isset($this->request_common_array['real_name']) ? $this->request_common_array['real_name'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $zipcode = isset($this->request_common_array['zipcode']) ? $this->request_common_array['zipcode'] : '';
        $res = $this->service->updateShopReturnSet($shop_id, $address, $real_name, $mobile, $zipcode);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询订单的物流信息
     *
     * @param unknown $order_ids
     * @param unknown $shop_id
    */
    public function getOrderGoodsExpressDetail($order_ids, $shop_id){
        $order_ids = isset($this->request_common_array['order_ids']) ? $this->request_common_array['order_ids'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopReturnSet($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 订单提货
     *
     * @param unknown $order_id
    */
    public function pickupOrder(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $buyer_name = isset($this->request_common_array['buyer_name']) ? $this->request_common_array['buyer_name'] : '';
        $buyer_phone = isset($this->request_common_array['buyer_phone']) ? $this->request_common_array['buyer_phone'] : '';
        $remark = isset($this->request_common_array['remark']) ? $this->request_common_array['remark'] : '';
        $res = $this->service->pickupOrder($order_id, $buyer_name, $buyer_phone, $remark);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 物流跟踪信息查询
     *
     * @param unknown $order_goods_id
    */
    public function getOrderGoodsExpressMessage(){
        $express_id = isset($this->request_common_array['express_id']) ? $this->request_common_array['express_id'] : '';
        $retval = $this->service->getOrderGoodsExpressMessage($express_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询订单的发货物流信息
     *
     * @param unknown $order_id
    */
    public function getOrderGoodsExpressList(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $retval = $this->service->getOrderGoodsExpressList($order_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加卖家对订单的备注
     *
     * @param unknown $order_id
    */
    public function addOrderSellerMemo(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $memo = isset($this->request_common_array['memo']) ? $this->request_common_array['memo'] : '';
        $res = $this->service->addOrderSellerMemo($order_id, $memo);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取卖家对订单的备注
     *
     * @param unknown $order_id
    */
    public function getOrderSellerMemo(){
    $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $retval = $this->service->getOrderSellerMemo($order_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}