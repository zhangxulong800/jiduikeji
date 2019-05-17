<?php
/**
 * Config.php
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
use data\service\Config as ConfigService;


class Config extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new ConfigService();
    }
    
    /**
     * 获取微信基本配置(WCHAT)
     */
    public function getWchatConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getWchatConfig($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     *   开放平台网站应用授权登录
     * @param unknown $appid
     * @param unknown $appsecret
     * @param unknown $url
     * @param unknown $call_back_url
    */
    public function setWchatConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $appid = isset($this->request_common_array['appid']) ? $this->request_common_array['appid'] : '';
        $appsecret = isset($this->request_common_array['appsecret']) ? $this->request_common_array['appsecret'] : '';
        $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
        $call_back_url = isset($this->request_common_array['call_back_url']) ? $this->request_common_array['call_back_url'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setWchatConfig($instance_id, $appid, $appsecret, $url, $call_back_url, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取QQ互联配置(QQ)
    */
    public function getQQConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getQQConfig($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * qq互联
     * @param unknown $appkey
     * @param unknown $appsecret
     * @param unknown $url
     * @param unknown $call_back_url
    */
    public function setQQConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $appkey = isset($this->request_common_array['appkey']) ? $this->request_common_array['appkey'] : '';
        $appsecret = isset($this->request_common_array['appsecret']) ? $this->request_common_array['appsecret'] : '';
        $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
        $call_back_url = isset($this->request_common_array['call_back_url']) ? $this->request_common_array['call_back_url'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setQQConfig($instance_id, $appkey, $appsecret, $url, $call_back_url, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取系统登录配置信息
    */
    public function getLoginConfig(){
        $retval = $this->service->getLoginConfig();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取微信支付参数(WPAY)
    */
    public function getWpayConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getWpayConfig($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置微信支付参数(WPAY)
     * @param unknown $appid  微信登录appid
     * @param unknown $appkey  微信登录appkey
     * @param unknown $mch_id  商户账号
     * @param unknown $mch_key  商户支付秘钥
    */
    public function setWpayConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $appid = isset($this->request_common_array['appid']) ? $this->request_common_array['appid'] : '';
        $appsecret = isset($this->request_common_array['appsecret']) ? $this->request_common_array['appsecret'] : '';
        $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
        $call_back_url = isset($this->request_common_array['call_back_url']) ? $this->request_common_array['call_back_url'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setWpayConfig($instance_id, $appid, $appkey, $mch_id, $mch_key, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取支付宝支付参数(ALIPAY)
    */
    public function getAlipayConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getAlipayConfig($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置支付宝支付配置(ALIPAY)
     * @param unknown $partnerid  商户ID
     * @param unknown $seller    商户账号
     * @param unknown $ali_key   商户秘钥
    */
    public function setAlipayConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $partnerid = isset($this->request_common_array['partnerid']) ? $this->request_common_array['partnerid'] : '';
        $seller = isset($this->request_common_array['seller']) ? $this->request_common_array['seller'] : '';
        $ali_key = isset($this->request_common_array['ali_key']) ? $this->request_common_array['ali_key'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setAlipayConfig($instance_id, $partnerid, $seller, $ali_key, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 设置微信和支付宝开关状态
    */
    public function setWpayStatusConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $res = $this->service->setWpayStatusConfig($instance_id, $is_use, $type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * PC商城热搜关键词获取
    */
    public function getHotsearchConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getHotsearchConfig($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * PC商城热搜关键词设置
     * @param unknown $partnerid
     * @param unknown $seller
     * @param unknown $ali_key
    */
    public function setHotsearchConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $keywords = isset($this->request_common_array['keywords']) ? $this->request_common_array['keywords'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setHotsearchConfig($instance_id, $keywords, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * pc 商城获取 默认搜索
     * @param unknown $instance_id
    */
    public function getDefaultSearchConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getDefaultSearchConfig($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * PC商城热搜关键词设置
     * @param unknown $instance_id
     * @param unknown $keywords
     * @param unknown $is_use
    */
    public function setDefaultSearchConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $keywords = isset($this->request_common_array['keywords']) ? $this->request_common_array['keywords'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setDefaultSearchConfig($instance_id, $keywords, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取 用户通知
    */
    public function getUserNotice(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getUserNotice($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置 用户通知
    */
    public function setUserNotice(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $keywords = isset($this->request_common_array['keywords']) ? $this->request_common_array['keywords'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setUserNotice($instance_id, $keywords, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取 发送邮件接口设置
    */
    public function getEmailMessage(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getEmailMessage($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置  发送邮件接口设置
    */
    public function setEmailMessage(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $email_host = isset($this->request_common_array['email_host']) ? $this->request_common_array['email_host'] : '';
        $email_port = isset($this->request_common_array['email_port']) ? $this->request_common_array['email_port'] : '';
        $email_addr = isset($this->request_common_array['email_addr']) ? $this->request_common_array['email_addr'] : '';
        $email_id = isset($this->request_common_array['email_id']) ? $this->request_common_array['email_id'] : '';
        $email_pass = isset($this->request_common_array['email_pass']) ? $this->request_common_array['email_pass'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setEmailMessage($instance_id, $email_host, $email_port, $email_addr, $email_id, $email_pass, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取 发送短信接口设置
     * @param unknown $instance_id
    */
    public function getMobileMessage(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getMobileMessage($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 设置    发送短信接口设置
     * @param unknown $instance_id
     * @param unknown $app_key
     * @param unknown $secret_key
     * @param unknown $is_use
     * @param unknown $user_type 用户类型
    */
    public function setMobileMessage(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $app_key = isset($this->request_common_array['app_key']) ? $this->request_common_array['app_key'] : '';
        $secret_key = isset($this->request_common_array['secret_key']) ? $this->request_common_array['secret_key'] : '';
        $free_sign_name = isset($this->request_common_array['free_sign_name']) ? $this->request_common_array['free_sign_name'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $user_type = isset($this->request_common_array['user_type']) ? $this->request_common_array['user_type'] : '';
        $res = $this->service->setMobileMessage($instance_id, $app_key, $secret_key, $free_sign_name, $is_use, $user_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取   微信开放平台接口设置
     * @param unknown $instance_id
    */
    public function getWinxinOpenPlatformConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getWinxinOpenPlatformConfig($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置   微信开放平台接口设置
     * @param unknown $instance_id
     * @param unknown $appid
     * @param unknown $appsecret
     * @param unknown $encodingAesKey
     * @param unknown $tk
     * @param unknown $is_use
    */
    public function setWinxinOpenPlatformConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $appid = isset($this->request_common_array['appid']) ? $this->request_common_array['appid'] : '';
        $appsecret = isset($this->request_common_array['appsecret']) ? $this->request_common_array['appsecret'] : '';
        $encodingAesKey = isset($this->request_common_array['encodingAesKey']) ? $this->request_common_array['encodingAesKey'] : '';
        $tk = isset($this->request_common_array['tk']) ? $this->request_common_array['tk'] : '';
        $res = $this->service->setWinxinOpenPlatformConfig($instance_id, $appid, $appsecret, $encodingAesKey, $tk);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取     登录验证码
    */
    public function getLoginVerifyCodeConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getLoginVerifyCodeConfig($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置       登录验证码是否开启
     * @param unknown $platform
     * @param unknown $admin
     * @param unknown $pc
    */
    public function setLoginVerifyCodeConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $platform = isset($this->request_common_array['platform']) ? $this->request_common_array['platform'] : '';
        $admin = isset($this->request_common_array['admin']) ? $this->request_common_array['admin'] : '';
        $pc = isset($this->request_common_array['pc']) ? $this->request_common_array['pc'] : '';
        $res = $this->service->setLoginVerifyCodeConfig($instance_id, $platform, $admin, $pc);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 对于单店铺系统获取微信配置
     * @param unknown $instance_id
    */
    public function getInstanceWchatConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getInstanceWchatConfig();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 对于单店铺系统设置微信配置
     * @param unknown $instance_id
     * @param unknown $appid
     * @param unknown $appsecret
    */
    public function setInstanceWchatConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $appid = isset($this->request_common_array['appid']) ? $this->request_common_array['appid'] : '';
        $appsecret = isset($this->request_common_array['appsecret']) ? $this->request_common_array['appsecret'] : '';
        $res = $this->service->setInstanceWchatConfig($instance_id, $appid, $appsecret);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取其他支付方式配置
    */
    public function getOtherPayTypeConfig(){
        $retval = $this->service->getOtherPayTypeConfig();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置其他支付方式配置
     * @param unknown $is_coin_pay
     * @param unknown $is_balance_pay
    */
    public function setOtherPayTypeConfig(){
        $is_coin_pay = isset($this->request_common_array['is_coin_pay']) ? $this->request_common_array['is_coin_pay'] : '';
        $is_balance_pay = isset($this->request_common_array['is_balance_pay']) ? $this->request_common_array['is_balance_pay'] : '';
        $res = $this->service->setOtherPayTypeConfig($is_coin_pay, $is_balance_pay);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取公告单条详情
     * @param unknown $id
    */
    public function getNotice(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getNotice($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置公告
     * @param unknown $id
     * @param unknown $is_enable
    */
    public function setNotice(){
        $shopid = isset($this->request_common_array['shopid']) ? $this->request_common_array['shopid'] : '';
        $notice_message = isset($this->request_common_array['notice_message']) ? $this->request_common_array['notice_message'] : '';
        $is_enable = isset($this->request_common_array['is_enable']) ? $this->request_common_array['is_enable'] : '';
        $res = $this->service->setNotice($shopid, $notice_message, $is_enable);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取系统设置value值
     * 传入字符串    $key = 'key1,key2,key3,.....'
     * 返回数组    array('key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3', .....)
     * @param unknown $instance_id
     * @param unknown $key
    */
    public function getConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $key = isset($this->request_common_array['key']) ? $this->request_common_array['key'] : '';
        $retval = $this->service->getConfig($instance_id, $key);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 设置系统设置
     * 传入数组 格式必须为
     * 例：$array[0] = array(
     'instance_id' => $this->instance_id,
     'key' => 'ORDER_BUY_CLOSE_TIME',
     'value' => '30',
     'desc' => '订单下单之后多少分钟未支付则关闭订单',
     'is_use' => 1
     );
     $array[1] = array(
     'instance_id' => $this->instance_id,
     'key' => 'ORDER_DELIVERY_COMPLETE_TIME',
     'value' => '7',
     'desc' => '订单收货之后多长时间自动完成',
     'is_use' => 1
     );
     ...
    */
    public function setConfig(){
        $params = isset($this->request_common_array['params']) ? $this->request_common_array['params'] : '';
        $res = $this->service->setConfig($params);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 得到系统通知的详情
     * @param unknown $shop_id
     * @param unknown $template_code
    */
    public function getNoticeTemplateDetail(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $template_code = isset($this->request_common_array['template_code']) ? $this->request_common_array['template_code'] : '';
        $retval = $this->service->getNoticeTemplateDetail($shop_id, $template_code);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 更新通知模板
     * @param unknown $template_id
     * @param unknown $shop_id
     * @param unknown $template_code
     * @param unknown $template
    */
    public function updateNoticeTemplate(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $template_code = isset($this->request_common_array['template_code']) ? $this->request_common_array['template_code'] : '';
        $template = isset($this->request_common_array['template']) ? $this->request_common_array['template'] : '';
        $res = $this->service->updateNoticeTemplate($shop_id, $template_code, $template);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 得到店铺的通知系统项
     * @param unknown $shop_id
    */
    public function getNoticeConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getNoticeConfig($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 得到店铺的发送项
    */
    public function getNoticeTemplateItem(){
        $template_code = isset($this->request_common_array['template_code']) ? $this->request_common_array['template_code'] : '';
        $retval = $this->service->getNoticeTemplateItem($template_code);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 得到店铺的模板集合
     * @param unknown $template_type
    */
    public function getNoticeTemplateType(){
        $template_type = isset($this->request_common_array['template_type']) ? $this->request_common_array['template_type'] : '';
        $retval = $this->service->getNoticeTemplateType($template_type);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 支付的通知项
     * @param unknown $shop_id
    */
    public function getPayConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getPayConfig($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员余额提现设置
     * @param unknown $shop_id
    */
    public function getBalanceWithdrawConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getBalanceWithdrawConfig($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置会员余额设置
     * @param unknown $shop_id
     * @param unknown $key
     * @param unknown $value
     * @param unknown $desc
     * @param unknown $is_use
    */
    public function setBalanceWithdrawConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $key = isset($this->request_common_array['key']) ? $this->request_common_array['key'] : '';
        $value = isset($this->request_common_array['value']) ? $this->request_common_array['value'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setBalanceWithdrawConfig($shop_id, $key, $value, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取seo设置
     * @param unknown $shop_id
    */
    public function getSeoConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getSeoConfig($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置 seo设置
     * @param unknown $shop_id
     * @param unknown $seo_title
     * @param unknown $seo_meta
     * @param unknown $seo_desc
     * @param unknown $seo_other
    */
    public function SetSeoConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $seo_title = isset($this->request_common_array['seo_title']) ? $this->request_common_array['seo_title'] : '';
        $seo_meta = isset($this->request_common_array['seo_meta']) ? $this->request_common_array['seo_meta'] : '';
        $seo_desc = isset($this->request_common_array['seo_desc']) ? $this->request_common_array['seo_desc'] : '';
        $seo_other = isset($this->request_common_array['seo_other']) ? $this->request_common_array['seo_other'] : '';
        $res = $this->service->SetSeoConfig($shop_id, $seo_title, $seo_meta, $seo_desc, $seo_other);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    public function updateConfigEnable(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->updateConfigEnable($id, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取通知模板单条信息
     * @param unknown $shop_id
     * @param unknown $template_type
     * @param unknown $template_code
    */
    public function getNoticeTemplateOneDetail(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $template_type = isset($this->request_common_array['template_type']) ? $this->request_common_array['template_type'] : '';
        $template_code = isset($this->request_common_array['template_code']) ? $this->request_common_array['template_code'] : '';
        $retval = $this->service->getNoticeTemplateOneDetail($shop_id, $template_type, $template_code);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取注册与访问的设置
     * @param unknown $shop_id
    */
    public function getRegisterAndVisit(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getRegisterAndVisit($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置注册与访问的设置
     * @param unknown $is_register
     * @param unknown $register_info
     * @param unknown $name_keyword
     * @param unknown $pwd_len
     * @param unknown $pwd_complexity
     * @param unknown $terms_of_service
     * @param unknown $is_use
    */
    public function setRegisterAndVisit(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $is_register = isset($this->request_common_array['is_register']) ? $this->request_common_array['is_register'] : '';
        $register_info = isset($this->request_common_array['register_info']) ? $this->request_common_array['register_info'] : '';
        $name_keyword = isset($this->request_common_array['name_keyword']) ? $this->request_common_array['name_keyword'] : '';
        $pwd_len = isset($this->request_common_array['pwd_len']) ? $this->request_common_array['pwd_len'] : '';
        $pwd_complexity = isset($this->request_common_array['pwd_complexity']) ? $this->request_common_array['pwd_complexity'] : '';
        $terms_of_service = isset($this->request_common_array['terms_of_service']) ? $this->request_common_array['terms_of_service'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $retval = $this->service->setRegisterAndVisit($shop_id,$is_register,$register_info,$name_keyword,$pwd_len,$pwd_complexity,$terms_of_service,$is_use);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 数据库表信息列表
    */
    public function getDatabaseList(){
        $retval = $this->service->getDatabaseList();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 得到店铺的邮箱配置信息
     * @param unknown $shop_id
    */
    public function getNoticeEmailConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getNoticeEmailConfig($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 得到店铺的短信配置信息
     * @param unknown $shop_id
    */
    public function getNoticeMobileConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getNoticeMobileConfig($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 物流跟踪信息查询
     * @param unknown $shop_id
    */
    public function getOrderExpressMessageConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getOrderExpressMessageConfig($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 物流跟踪信息修改
     * @param unknown $shop_id
     * @param unknown $appid
     * @param unknown $appkey
     * @param unknown $is_use
    */
    public function updateOrderExpressMessageConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $appid = isset($this->request_common_array['appid']) ? $this->request_common_array['appid'] : '';
        $appkey = isset($this->request_common_array['appkey']) ? $this->request_common_array['appkey'] : '';
        $back_url = isset($this->request_common_array['back_url']) ? $this->request_common_array['back_url'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->updateOrderExpressMessageConfig($shop_id, $appid, $appkey, $back_url, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取运营中的基本配置
    */
    public function getOperateConfig(){
        $retval = $this->service->getOperateConfig();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    public function updateOperateConfig(){
        $config_value = isset($this->request_common_array['config_value']) ? $this->request_common_array['config_value'] : '';
        $res = $this->service->updateOperateConfig($config_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}