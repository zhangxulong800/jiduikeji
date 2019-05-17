<?php
/**
 * UnifyPay.php
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
use data\service\UnifyPay as UnifyPayService;


class UnifyPay extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new UnifyPayService();
    }
    
    /**
     * 创建订单支付编号
     * @param unknown $order_id
     */
    public function createOutTradeNo(){
        $retval = $this->service->createOutTradeNo();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 创建待支付单据
     * @param unknown $pay_no
     * @param unknown $pay_body
     * @param unknown $pay_detail
     * @param unknown $pay_money
     * @param unknown $type  订单类型  1. 商城订单  2.
     * @param unknown $pay_money
    */
    public function createPayment(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $pay_body = isset($this->request_common_array['pay_body']) ? $this->request_common_array['pay_body'] : '';
        $pay_detail = isset($this->request_common_array['pay_detail']) ? $this->request_common_array['pay_detail'] : '';
        $pay_money = isset($this->request_common_array['pay_money']) ? $this->request_common_array['pay_money'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $type_alis_id = isset($this->request_common_array['type_alis_id']) ? $this->request_common_array['type_alis_id'] : '';
        $res = $this->service->createPayment($shop_id, $out_trade_no, $pay_body, $pay_detail, $pay_money, $type, $type_alis_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 根据支付编号修改待支付单据
     * @param unknown $out_trade_no
     * @param unknown $shop_id
     * @param unknown $pay_body
     * @param unknown $pay_detail
     * @param unknown $pay_money
     * @param unknown $type 订单类型  1. 商城订单  2.
     * @param unknown $type_alis_id
    */
    public function updatePayment(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $pay_body = isset($this->request_common_array['pay_body']) ? $this->request_common_array['pay_body'] : '';
        $pay_detail = isset($this->request_common_array['pay_detail']) ? $this->request_common_array['pay_detail'] : '';
        $pay_money = isset($this->request_common_array['pay_money']) ? $this->request_common_array['pay_money'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $type_alis_id = isset($this->request_common_array['type_alis_id']) ? $this->request_common_array['type_alis_id'] : '';
        $res = $this->service->updatePayment($out_trade_no,$shop_id, $pay_body, $pay_detail, $pay_money, $type, $type_alis_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除待支付单据
     * @param unknown $out_trade_no
    */
    public function delPayment(){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $res = $this->service->delPayment($out_trade_no);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 线上支付主动根据支付方式执行支付成功的通知
     * @param unknown $out_trade_no
    */
    public function onlinePay(){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $pay_type = isset($this->request_common_array['pay_type']) ? $this->request_common_array['pay_type'] : '';
        $res = $this->service->onlinePay($out_trade_no, $pay_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 只是执行单据支付，不进行任何处理用于执行支付后被动调用
     * @param unknown $out_trade_no
     * @param unknown $pay_type
    */
    public function offLinePay(){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $pay_type = isset($this->request_common_array['pay_type']) ? $this->request_common_array['pay_type'] : '';
        $res = $this->service->offLinePay($out_trade_no, $pay_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取支付信息
     * @param unknown $out_trade_no
    */
    public function getPayInfo($out_trade_no){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $retval = $this->service->getPayInfo($out_trade_no);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 重新设置编号，用于修改价格订单
     * @param unknown $out_trade_no
     * @param unknown $new_no
     * @return Ambigous <number, \think\false, boolean, string>
    */
    public function modifyNo(){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $new_no = isset($this->request_common_array['new_no']) ? $this->request_common_array['new_no'] : '';
        $res = $this->service->modifyNo($out_trade_no, $new_no);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改支付价格
     * @param unknown $out_trade_no
    */
    public function modifyPayMoney(){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $pay_money = isset($this->request_common_array['pay_money']) ? $this->request_common_array['pay_money'] : '';
        $res = $this->service->modifyPayMoney($out_trade_no, $pay_money);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 执行微信支付
     * @param unknown $out_trade_no
     * @param unknown $trade_type
     * @param unknown $red_url
    */
    public function wchatPay(){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $trade_type = isset($this->request_common_array['trade_type']) ? $this->request_common_array['trade_type'] : '';
        $red_url = isset($this->request_common_array['red_url']) ? $this->request_common_array['red_url'] : '';
        $res = $this->service->wchatPay($out_trade_no, $trade_type, $red_url);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 执行支付宝支付
     * @param unknown $out_trade_no
     * @param unknown $notify_url
     * @param unknown $return_url
     * @param unknown $show_url
    */
    public function aliPay(){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $notify_url = isset($this->request_common_array['notify_url']) ? $this->request_common_array['notify_url'] : '';
        $return_url = isset($this->request_common_array['return_url']) ? $this->request_common_array['return_url'] : '';
        $show_url = isset($this->request_common_array['show_url']) ? $this->request_common_array['show_url'] : '';
        $res = $this->service->aliPay($out_trade_no, $notify_url, $return_url, $show_url);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取微信jsapi
     * @param unknown $UnifiedOrderResult
    */
    public function getWxJsApi(){
        $UnifiedOrderResult = isset($this->request_common_array['UnifiedOrderResult']) ? $this->request_common_array['UnifiedOrderResult'] : '';
        $retval = $this->service->getWxJsApi($UnifiedOrderResult);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取支付宝配置参数是否正确,支付成功后使用
    */
    public function getVerifyResult(){
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $retval = $this->service->getVerifyResult($type);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取支付配置
    */
    public function getPayConfig(){
        $retval = $this->service->getPayConfig();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
}