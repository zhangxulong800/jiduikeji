<?php
/**
 * Config.php
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
 * @date : 2015.4.24
 * @version : v1.0.0.0
 */
namespace data\service;
/**
 * 系统配置业务层
 */
use data\service\BaseService as BaseService;
use data\api\IConfig as IConfig;
use data\model\ConfigModel as ConfigModel;
use data\model\NoticeModel;
use think\Model;
use think\Db;
use data\model\NoticeTemplateModel;
use data\model\NoticeTemplateItemModel;
use data\model\NoticeTemplateTypeModel;
use think\Log;
class Config extends BaseService implements IConfig
{
    private $config_module;
    function __construct()
    {
        parent::__construct();
        $this->config_module = new ConfigModel();
    }
	/* (non-PHPdoc)
     * @see \data\api\IConfig::getWchatConfig()
     */
    public function getWchatConfig($instance_id)
    {
        $info = $this->config_module->getInfo(['key'=>'WCHATLOGIN', 'instance_id'=>$instance_id],'value,is_use');
        if(empty($info['value']))
        {
           return array(
               'value' => array(
                   'APP_KEY'=>'',
                   'APP_SECRET'=>'',
                   'AUTHORIZE' =>'',
                   'CALLBACK'=>'',
               ),
               'is_use'=>0,
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \data\api\IConfig::setWchatConfig()
     */
    public function setWchatConfig($instance_id, $appid, $appsecret, $url, $call_back_url, $is_use)
    {
        $info = array(
            'APP_KEY'=>$appid,
            'APP_SECRET'=>$appsecret,
            'AUTHORIZE' =>$url,
            'CALLBACK'=>$call_back_url
        );
        $value = json_encode($info);
        $count = $this->config_module->where(['key'=>'WCHATLOGIN', 'instance_id'=>$instance_id])->count();
        if($count > 0)
        {
            $data = array(
                'value' =>$value,
                'is_use' =>$is_use,
                'modify_time' =>date('Y-m-d H:i:s', time())
            );
            $res = $this->config_module->where(['key'=>'WCHATLOGIN', 'instance_id'=>$instance_id])->update($data);
            if($res == 1){
                return SUCCESS;
            }else{
                return UPDATA_FAIL;
            }
        }else{
            $data = array(
                'instance_id'=>$instance_id,
                'key' => 'WCHATLOGIN',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $this->config_module->save($data);
        }
        
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \data\api\IConfig::getQQConfig()
     */
    public function getQQConfig($instance_id)
    {
        $info = $this->config_module->getInfo(['key'=>'QQLOGIN','instance_id'=>$instance_id], 'value,is_use');
         if(empty($info['value']))
        {
           return array(
                'value' => array(
                   'APP_KEY'=>'',
                   'APP_SECRET'=>'',
                   'AUTHORIZE' =>'',
                   'CALLBACK'=>'',
               ),
                'is_use'=>0,
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \data\api\IConfig::setQQConfig()
     */
    public function setQQConfig($instance_id, $appkey, $appsecret, $url, $call_back_url, $is_use)
    {
        $info = array(
            'APP_KEY'=>$appkey,
            'APP_SECRET'=>$appsecret,
            'AUTHORIZE' =>$url,
            'CALLBACK'=>$call_back_url
        );
        $value = json_encode($info);
        $count = $this->config_module->where(['key'=>'QQLOGIN', 'instance_id'=>$instance_id])->count();
        if($count > 0)
        {
            $data = array(
                'value' =>$value,
                'is_use' =>$is_use,
                'modify_time' =>date('Y-m-d H:i:s', time())
            );
            $res = $this->config_module->where(['key'=>'QQLOGIN', 'instance_id'=>$instance_id])->update($data);
            if($res == 1){
                return SUCCESS;
            }else{
                return UPDATA_FAIL;
            }
        }else{
            $data = array(
                'instance_id'=>$instance_id,
                'key' => 'QQLOGIN',
                'value' => $value,
                'is_use' =>$is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $this->config_module->save($data);
            return $res;
        }
        // TODO Auto-generated method stub
        
    }
    public function getLoginConfig(){
  
        $instance_id = 0;
        $wchat_config = $this->getWchatConfig($instance_id);
        $qq_config = $this->getQQConfig($instance_id);
        
        $mobile_config = $this->getMobileMessage($instance_id);
        $email_config = $this->getEmailMessage($instance_id);
        $data = array(
            'wchat_login_config' => $wchat_config,
            'qq_login_config' => $qq_config,
            'mobile_config' => $mobile_config,
            'email_config' => $email_config
        );
        return $data;
    }

	/* (non-PHPdoc)
     * @see \data\api\IConfig::getWpayConfig()
     */
    public function getWpayConfig($instance_id)
    {
        $info = $this->config_module->getInfo(['instance_id'=>$instance_id,'key'=>'WPAY'], 'value,is_use');
        if(empty($info['value']))
        {
            return array(
                'value' => array(
                    'appid' => '',
                    'appkey' => '',
                    'mch_id' => '',
                    'mch_key' => '',
                ),
                'is_use' => 0,
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \data\api\IConfig::setWpayConfig()
     */
    public function setWpayConfig($instanceid, $appid, $appkey, $mch_id, $mch_key, $is_use)
    {
        $data = array(
            'appid'=>$appid,
            'appkey'=>$appkey,
            'mch_id' =>$mch_id,
            'mch_key'=>$mch_key
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'WPAY', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'WPAY',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'WPAY',
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid, 'key'=>'WPAY']);
        }
        return $res;
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \data\api\IConfig::getAlipayConfig()
     */
    public function getAlipayConfig($instance_id)
    {
        $info = $this->config_module->getInfo(['instance_id'=>$instance_id,'key'=>'ALIPAY'], 'value,is_use');
        if(empty($info['value']))
        {
            return array(
                'value' => array(
                    'ali_partnerid' => '',
                    'ali_seller' => '',
                    'ali_key' => ''
                ),
                'is_use' => 0,
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \data\api\IConfig::setAlipayConfig()
     */
    public function setAlipayConfig($instanceid, $partnerid, $seller, $ali_key, $is_use)
    {
        $data = array(
              'ali_partnerid' => $partnerid,
                'ali_seller' => $seller,
                'ali_key' => $ali_key
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'ALIPAY', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'ALIPAY',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'ALIPAY',
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid, 'key'=>'ALIPAY']);
        }
        return $res;
        // TODO Auto-generated method stub
        
    }
    /**
     * 设置微信和支付宝开关状态
     */
    public function setWpayStatusConfig($instanceid,$is_use,$type){
        $config_module = new ConfigModel();
        $data = array(
            'is_use' => $is_use,
            'modify_time' => date('Y-m-d H:i:s', time())
        );
        $res = $config_module->save($data, ['instance_id' => $instanceid,'key'=>$type]);
        return $res;
    }
    /*
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getHotsearchConfig()
     */
    public function getHotsearchConfig($instanceid)
    {
        $info = $this->config_module->getInfo(['key'=>'HOTKEY', 'instance_id'=>$instanceid], 'value');
        if(empty($info['value']))
        {
            return null;
        }else{
            return json_decode($info['value'], true);
        }
        // TODO Auto-generated method stub
    
    }
    /*
     * (non-PHPdoc)
     * @see \ata\api\IConfig::setHotsearchConfig()
     */
    public function setHotsearchConfig($instanceid, $keywords, $is_use)
    {
        $data = $keywords;
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'HOTKEY', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'HOTKEY',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid, 'key' => 'HOTKEY']);
        }
        return $res;
        // TODO Auto-generated method stub
    
    }
    
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::getDefaultSearchConfig()
     */
    public function getDefaultSearchConfig($instanceid){
        $info = $this->config_module->getInfo(['key'=>'DEFAULTKEY', 'instance_id'=>$instanceid], 'value');
        if(empty($info['value'])){
            return null;
        }else{
            return json_decode($info['value'], true);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::setDefaultSearchConfig()
     */
    public function setDefaultSearchConfig($instanceid, $keywords, $is_use){
        $data = $keywords;
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'DEFAULTKEY', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'DEFAULTKEY',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid,'key' => 'DEFAULTKEY']);
        }
        return $res;
    }
    
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::getUserNotice()
     */
    public function getUserNotice($instanceid){
        $info = $this->config_module->getInfo(['key'=>'USERNOTICE', 'instance_id' => $instanceid], 'value');
        if(empty($info['value'])){
            return null;
        }else{
            return json_decode($info['value'], true);
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::setUserNotice()
     */
    public function setUserNotice($instanceid, $keywords, $is_use){
        $data = $keywords;
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'USERNOTICE', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'USERNOTICE',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid, 'key'=>'USERNOTICE']);
        }
        return $res;
    }
    
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::getEmailMessage()
     */
    public function getEmailMessage($instanceid){
        $info = $this->config_module->getInfo(['key'=>'EMAILMESSAGE', 'instance_id' => $instanceid], 'value, is_use');
        if(empty($info['value'])){
            return array(
                'value' => array(
                    'email_host' => '',
                    'email_port' => '',
                    'email_addr' => '',
                    'email_pass' => '',
                    'email_id' => '',
                ),
                'is_use' => 0
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }
    
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::setEmailMessage()
     */
    public function setEmailMessage($instanceid, $email_host, $email_port, $email_addr, $email_id, $email_pass, $is_use){
        $data = array(
            'email_host' => $email_host,
            'email_port' => $email_port,
            'email_addr' => $email_addr,
            'email_id' => $email_id,
            'email_pass' => $email_pass,
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'EMAILMESSAGE', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'EMAILMESSAGE',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'EMAILMESSAGE',
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid, 'key'=>'EMAILMESSAGE']);
        }
        return $res;
    }
    
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::getMobileMessage()
     */
    public function getMobileMessage($instanceid){
        $info = $this->config_module->getInfo(['key'=>'MOBILEMESSAGE', 'instance_id' => $instanceid], 'value, is_use');
        if(empty($info['value'])){
            return array(
                'value' => array(
                    'appKey' => '',
                    'secretKey' => '',
                    'freeSignName' => '',
                ),
                'is_use' => 0
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::setMobileMessage()
     */
    public function setMobileMessage($instanceid, $app_key, $secret_key, $free_sign_name, $is_use,$user_type){
        $data = array(
            'appKey' => $app_key,
            'secretKey' => $secret_key,
            'freeSignName' => $free_sign_name,
            'user_type' => $user_type
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'MOBILEMESSAGE', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'MOBILEMESSAGE',
                'value' => $value,
                'is_use' => $is_use,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'MOBILEMESSAGE',
                'value' => $value,
                'is_use' => $is_use,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid, 'key'=>'MOBILEMESSAGE']);
        }
        return $res;
    }
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::getWinxinOpenPlatformConfig()
     */
    public function getWinxinOpenPlatformConfig($instanceid){
        $info = $this->config_module->getInfo(['key'=>'WXOPENPLATFORM', 'instance_id' => $instanceid], 'value, is_use');
        if(empty($info['value'])){
            return array(
                'value' => array(
                    'appId' => '',
                    'appsecret' => '',
                    'encodingAesKey' => '',
                    'tk' => ''
                ),
                'is_use' => 1
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }
    /**
     * {@inheritDoc}
     * @see \ata\api\IConfig::setWinxinOpenPlatformConfig()
     */
    public function setWinxinOpenPlatformConfig($instanceid, $appid, $appsecret, $encodingAesKey, $tk){
        $data = array(
            'appId' => $appid,
            'appsecret' => $appsecret,
            'encodingAesKey' => $encodingAesKey,
            'tk' => $tk
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'WXOPENPLATFORM', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'WXOPENPLATFORM',
                'value' => $value,
                'is_use' => 1,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'WXOPENPLATFORM',
                'value' => $value,
                'is_use' => 1,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid, 'key'=>'WXOPENPLATFORM']);
        }
        return $res;
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getLoginVerifyCodeConfig()
     */
    public function getLoginVerifyCodeConfig($instanceid){
        $info = $this->config_module->getInfo(['key'=>'LOGINVERIFYCODE', 'instance_id' => $instanceid], 'value, is_use');
        if(empty($info['value'])){
            return array(
                'value' => array(
                    'platform' => 0,
                    'admin' => 0,
                    'pc' => 0
                ),
                'is_use' => 1
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::setLoginVerifyCodeConfig()
     */
    public function setLoginVerifyCodeConfig($instanceid, $platform, $admin, $pc){
        $data = array(
            'platform' => $platform,
            'admin' => $admin,
            'pc' => $pc
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'LOGINVERIFYCODE', 'instance_id' => $instanceid], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instanceid,
                'key' => 'LOGINVERIFYCODE',
                'value' => $value,
                'is_use' => 1,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'LOGINVERIFYCODE',
                'value' => $value,
                'is_use' => 1,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instanceid, 'key'=>'LOGINVERIFYCODE']);
        }
        return $res;
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::setInstanceWchatConfig()
     */
    public function setInstanceWchatConfig($instance_id, $appid, $appsecret){
         $data = array(
            'appid' => $appid,
            'appsecret' => $appsecret
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'SHOPWCHAT', 'instance_id' => $instance_id], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => $instance_id,
                'key' => 'SHOPWCHAT',
                'value' => $value,
                'is_use' => 1,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'SHOPWCHAT',
                'value' => $value,
                'is_use' => 1,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => $instance_id, 'key'=>'SHOPWCHAT']);
        }
        return $res;
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getInstanceWchatConfig()
     */
    public function getInstanceWchatConfig($instance_id){
        $info = $this->config_module->getInfo(['key'=>'WCHATLOGIN','instance_id' => $instance_id], 'value'); //此处错误调整
        if(empty($info['value'])){
            return array(
                'value' => array(
                    'appid' => '',
                    'appsecret' => ''
                ),
                'is_use' => 1
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
			//$info['value']['appid']=$info['value']['APP_KEY'];
            return $info;
        }
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getOtherPayTypeConfig()
     */
    public function getOtherPayTypeConfig(){
      $info = $this->config_module->getInfo(['key'=>'OTHER_PAY', 'instance_id' => 0], 'value');
        if(empty($info['value'])){
            return array(
                'value' => array(
                    'is_coin_pay' => 0,
                    'is_balance_pay' => 0
                ),
                'is_use' => 1
            );
        }else{
            $info['value'] = json_decode($info['value'], true);
            return $info;
        }
        
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::setOtherPayTypeConfig()
     */
    public function setOtherPayTypeConfig($is_coin_pay, $is_balance_pay){
        $data = array(
            'is_coin_pay' => $is_coin_pay,
            'is_balance_pay' => $is_balance_pay
        );
        $value = json_encode($data);
        $info = $this->config_module->getInfo(['key'=>'OTHER_PAY', 'instance_id' => 0], 'value');
        if(empty($info))
        {
            $config_module = new ConfigModel();
            $data = array(
                'instance_id' => 0,
                'key' => 'OTHER_PAY',
                'value' => $value,
                'is_use' => 1,
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data);
        }else
        {
            $config_module = new ConfigModel();
            $data = array(
                'key' => 'OTHER_PAY',
                'value' => $value,
                'is_use' => 1,
                'modify_time' => date('Y-m-d H:i:s', time())
            );
            $res = $config_module->save($data, ['instance_id' => 0, 'key'=>'OTHER_PAY']);
        }
        return $res;
    }

    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::setNotice()
     */
    public function setNotice($shopid, $notice_message, $is_enable){
        $notice = new NoticeModel();
        $data = array(
            'notice_message' => $notice_message,
            'is_enable' => $is_enable,
        );
        $res = $notice->save($data, ['shopid' => $shopid]);
        return $res;
    }
    
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getNotice()
     */
    public function getNotice($shopid){
        $notice = new NoticeModel();
        $notice_info = $notice->getInfo(['shopid' => $shopid]);
        if(empty($notice_info)){
            $data = array(
                'shopid' => $shopid,
                'notice_message' => '',
                'is_enable' => 0
            );
            $notice->save($data);
            $notice_info = $notice->getInfo(['shopid' => $shopid]);
        }
        return $notice_info;
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getConfig()
     */
    public function getConfig($instance_id, $key){
        $config = new ConfigModel();
        $array = array();
        $info = $config->getInfo(['instance_id' => $instance_id, 'key'=>$key]);
        return $info;
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::setConfig()
     */
    public function setConfig($params){
        $config = new ConfigModel();
        foreach ($params as $key => $value){
            if($this->checkConfigKeyIsset($value['instance_id'], $value['key'])){
                $res = $this->updateConfig($value['instance_id'], $value['key'], $value['value'], $value['desc'], $value['is_use']);
            } else {
               $res = $this->addConfig($value['instance_id'], $value['key'], $value['value'], $value['desc'], $value['is_use']);
            }
        }
        return $res;
    }
    /**
     * 添加设置
     * @param unknown $instance_id
     * @param unknown $key
     * @param unknown $value
     * @param unknown $desc
     * @param unknown $is_use
     */
    protected function addConfig($instance_id, $key, $value, $desc, $is_use){
        $config = new ConfigModel();
        if(is_array($value)){
            $value = json_encode($value);
        }
        $data = array(
            'instance_id' => $instance_id,
            'key' => $key,
            'value' => $value,
            'desc' => $desc,
            'is_use' => $is_use,
            'create_time' => date('Y-m-d H:i:s', time())
        );
        $res = $config->save($data);
        return $res;
    }
    /**
     * 修改配置
     * @param unknown $instance_id
     * @param unknown $key
     * @param unknown $value
     * @param unknown $desc
     * @param unknown $is_use
     */
    protected function updateConfig($instance_id, $key, $value, $desc, $is_use){
        $config = new ConfigModel();
        if(is_array($value)){
            $value = json_encode($value);
        }
        $data = array(
            'value' => $value,
            'desc' => $desc,
            'is_use' => $is_use,
            'modify_time' => date('Y-m-d H:i:s', time())
        );
        $res = $config->save($data, ['instance_id' => $instance_id,'key' => $key]);
        return $res;
    }
    
    /**
     * 判断当前设置是否存在
     * 存在返回 true 不存在返回 false
     * @param unknown $instance_id
     * @param unknown $key
     */
    public function checkConfigKeyIsset($instance_id, $key){
        $config = new ConfigModel();
        $num = $config->where(['instance_id'=>$instance_id, 'key'=>$key])->count();
        return $num> 0 ? true : false;
    }
    /**
     * 
     * 得到店铺的系统通知的详情
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getNoticeTemplateDetail()
     */
    public function getNoticeTemplateDetail($shop_id, $template_type){
        $notice_template_model=new NoticeTemplateModel();
        $condition=array(
          "template_type"=>$template_type,
          "instance_id"=>$shop_id  
        );
        $template_list=$notice_template_model->getQuery($condition, "*", "");
        return $template_list;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IConfig::getNoticeTemplateOneDetail()
     */
    public function getNoticeTemplateOneDetail($shop_id, $template_type, $template_code){
        $notice_template_model = new NoticeTemplateModel();
        $info = $notice_template_model->getInfo(['instance_id' => $shop_id, 'template_type' => $template_type, 'template_code' => $template_code]);
        return $info;
    }
    /**
     * 更新通知模板的信息
     * (non-PHPdoc)
     * @see \ata\api\IConfig::updateNoticeTemplate()
     */
    public function updateNoticeTemplate($shop_id, $template_type, $template_array){
        $template_data=json_decode($template_array, true);
        foreach ($template_data as $template_obj){
            $template_code=$template_obj["template_code"];
            $template_title=$template_obj["template_title"];
            $template_content=$template_obj["template_content"];
            $sign_name=$template_obj["sign_name"];
            $is_enable=$template_obj["is_enable"];
            $notice_template_model=new NoticeTemplateModel();
            $t_count=$notice_template_model->getCount(["instance_id"=>$shop_id, "template_type"=>$template_type, "template_code"=>$template_code]);
            
            if($t_count>0){
                //更新
                $data=array(
                    "template_title"=>$template_title,
                    "template_content"=>$template_content,
                    "sign_name"=>$sign_name,
                    "is_enable"=>$is_enable,
                    "modify_time"=>date('Y-m-d H:i:s', time())
                );
                $res =  $notice_template_model->save($data, ["instance_id"=>$shop_id, "template_type"=>$template_type, "template_code"=>$template_code]);
            }else{
                //添加
                $data=array(
                    "instance_id"=>$shop_id,
                    "template_type"=>$template_type,
                    "template_code"=>$template_code,
                    "template_title"=>$template_title,
                    "template_content"=>$template_content,
                    "sign_name"=>$sign_name,
                    "is_enable"=>$is_enable,
                    "modify_time"=>date('Y-m-d H:i:s', time())
                );
                $res =  $notice_template_model->save($data);
            }
        }
        return $res;
    }
    /**
     * 得到店铺的系统通知项
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getNoticeConfig()
     */
    public function getNoticeConfig($shop_id){
        $config_model=new ConfigModel();
        $condition = array(
            'instance_id' => $shop_id,
            'key'         => array('in','EMAILMESSAGE,MOBILEMESSAGE'),
        );
        $notify_list=$config_model->getQuery($condition, "*", "");
        if(!empty($notify_list)){
            for($i=0;$i<count($notify_list);$i++){
                if($notify_list[$i]["key"]=="EMAILMESSAGE"){
                    $notify_list[$i]["notify_name"]="邮件通知";
                }else if($notify_list[$i]["key"]=="MOBILEMESSAGE"){
                    $notify_list[$i]["notify_name"]="短信通知";
                }
            }
            return $notify_list;
        }else{
            return null;
        }
    }
    /**
     * 得到店铺的email的配置信息
     * @param unknown $shop_id
     */
    public function getNoticeEmailConfig($shop_id){
        $config_model=new ConfigModel();
        $condition=array(
            'instance_id' => $shop_id,
            'key'         => 'EMAILMESSAGE'
        );
        $email_detail=$config_model->getQuery($condition, "*", "");
        return $email_detail;
    }
    /**
     * 得到店铺的短信配置信息
     * @param unknown $shop_id
     */
    public function getNoticeMobileConfig($shop_id){
        $config_model=new ConfigModel();
        $condition=array(
            'instance_id' => $shop_id,
            'key'         => 'MOBILEMESSAGE'
        );
        $mobile_detail=$config_model->getQuery($condition, "*", "");
        return $mobile_detail;
    }
    /**
     * 得到店铺的邮件发送项
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getNoticeSendItem()
     */
    public function getNoticeTemplateItem($template_code){
        $notice_model=new NoticeTemplateItemModel();
        $item_list=$notice_model->where("FIND_IN_SET('".$template_code."', type_ids)")->select();
        return $item_list;
    }
    /**
     * 得到店铺模板的集合
     * (non-PHPdoc)
     * @see \data\api\IConfig::getNoticeTemplateType()
     */
    public function getNoticeTemplateType($template_type){
        $notice_type_model=new NoticeTemplateTypeModel();
        $type_list=$notice_type_model->where("template_type='".$template_type."' or template_type='all'")->select();
        return $type_list;
    }
    
    /**
     * 支付的通知项
     * @param unknown $shop_id
     * @return string|NULL
     */
    public function getPayConfig($shop_id){
        $config_model=new ConfigModel();
        $condition = array(
            'instance_id' => $shop_id,
            'key'         => array('in','WPAY,ALIPAY'),
        );
        $notify_list=$config_model->getQuery($condition, "*", "");
        if(!empty($notify_list)){
            for($i=0;$i<count($notify_list);$i++){
                if($notify_list[$i]["key"]=="WPAY"){
                    $notify_list[$i]["logo"]="public/admin/images/wchat.png";
                    $notify_list[$i]["pay_name"]="微信支付";
                    $notify_list[$i]["desc"]="该系统支持微信网页支付和扫码支付";
                }else if($notify_list[$i]["key"]=="ALIPAY"){
                    $notify_list[$i]["pay_name"]="支付宝支付";
                    $notify_list[$i]["logo"]="public/admin/images/pay.png";
                    $notify_list[$i]["desc"]="该系统支持即时到账接口";
                }
            }
            return $notify_list;
        }else{
            return null;
        }
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::getBalanceWithdrawConfig()
     */
    public function getBalanceWithdrawConfig($shop_id){
        $key = 'WITHDRAW_BALANCE';
        $info = $this->getConfig(0, $key); //该记录生成instance_id默认是0。不对应店铺编号
        if(empty($info)){
            $params[0] = array(
                //'instance_id' => $shop_id,
                'key' => $key,
                'value' => array(
                    'withdraw_cash_min' => 0.00,
                    'withdraw_multiple' => 0,
                    'withdraw_poundage' => 0,
                    'withdraw_message' => '',
                ),
                'desc' => '会员余额提现设置',
                'is_use' => 0
            );
            $this->setConfig($params);
        }
            $info['value'] = json_decode($info['value'], true);
        return $info;
    }
    /**
     * (non-PHPdoc)
     * @see \ata\api\IConfig::setBalanceWithdrawConfig()
     */
    public function setBalanceWithdrawConfig($shop_id, $key, $value, $is_use){
        $params[0] = array(
            'instance_id' => $shop_id,
            'key' => $key,
            'value' => array(
                'withdraw_cash_min' => $value['withdraw_cash_min'],
                'withdraw_multiple' => $value['withdraw_multiple'],
                'withdraw_poundage' => $value['withdraw_poundage'],
                'withdraw_message' => $value['withdraw_message'],
            ),
            'desc' => '会员余额提现设置',
            'is_use' => $is_use
        );
        $res = $this->setConfig($params);
        return $res;
    }
	
	public function getSalesConfig($shop_id){
        $hui_zhi = $this->getConfig($shop_id, 'SALES_HUI_ZHI');
		$jing_zhi = $this->getConfig($shop_id, 'SALES_JING_ZHI');
		$zong_zhi = $this->getConfig($shop_id, 'SALES_ZONG_ZHI');
       	$hui_cong = $this->getConfig($shop_id, 'SALES_HUI_CONG');
		$jing_cong = $this->getConfig($shop_id, 'SALES_JING_CONG');
		$zong_cong = $this->getConfig($shop_id, 'SALES_ZONG_CONG');
        if(empty($hui_zhi)){
            $this->SetSalesConfig($shop_id, '', '', '', '', '', '');
            $array = array(
                'hui_zhi' => '',
                'jing_zhi' => '',
                'zong_zhi' => '',
                'hui_cong' => '',
                'jing_cong' => '',
                'zong_cong' => '',
            );
        }else{
            $array = array(
                'hui_zhi' => $hui_zhi['value'],
                'jing_zhi' => $jing_zhi['value'],
                'zong_zhi' => $zong_zhi['value'],
                'hui_cong' => $hui_cong['value'],
                'jing_cong' => $jing_cong['value'],
                'zong_cong' => $zong_cong['value'],
            );
        }
        return $array;
    }
   
	public function getCardConfig($shop_id){
        $hui_zhi = $this->getConfig($shop_id, 'HUI_ZHI');
		$jing_zhi = $this->getConfig($shop_id, 'JING_ZHI');
		$zong_zhi = $this->getConfig($shop_id, 'ZONG_ZHI');
       	$hui_cong = $this->getConfig($shop_id, 'HUI_CONG');
		$jing_cong = $this->getConfig($shop_id, 'JING_CONG');
		$zong_cong = $this->getConfig($shop_id, 'ZONG_CONG');
        if(empty($hui_zhi)){
            $this->SetCardConfig($shop_id, '', '', '', '', '', '');
            $array = array(
                'hui_zhi' => '',
                'jing_zhi' => '',
                'zong_zhi' => '',
                'hui_cong' => '',
                'jing_cong' => '',
                'zong_cong' => '',
            );
        }else{
            $array = array(
                'hui_zhi' => $hui_zhi['value'],
                'jing_zhi' => $jing_zhi['value'],
                'zong_zhi' => $zong_zhi['value'],
                'hui_cong' => $hui_cong['value'],
                'jing_cong' => $jing_cong['value'],
                'zong_cong' => $zong_cong['value'],
            );
        }
        return $array;
    }
    public function getSeoConfig($shop_id){
        $seo_title = $this->getConfig($shop_id, 'SEO_TITLE');
        $seo_meta = $this->getConfig($shop_id, 'SEO_META');
        $seo_desc = $this->getConfig($shop_id, 'SEO_DESC');
        $seo_other = $this->getConfig($shop_id, 'SEO_OTHER');
		$array = array(
			'seo_title' => $seo_title['value'],
			'seo_meta' => $seo_meta['value'],
			'seo_desc' => $seo_desc['value'],
			'seo_other' => $seo_other['value'],
		);
        return $array;
    }
	
	public function getUserGradeConfig($shop_id){
        $hui_jing_num = $this->getConfig($shop_id, 'HUI_JING_NUM');
        $hui_jing_money = $this->getConfig($shop_id, 'HUI_JING_MONEY');
        $jing_zong_num = $this->getConfig($shop_id, 'JING_ZONG_NUM');
        $jing_zong_money = $this->getConfig($shop_id, 'JING_ZONG_MONEY');
        if(empty($hui_jing_num) || empty($hui_jing_money) || empty($jing_zong_num) || empty($jing_zong_money)){
            $this->SetUserGradeConfig($shop_id, '', '', '', '');
            $array = array(
                'hui_jing_num' => '',
                'hui_jing_money' => '',
                'jing_zong_num' => '',
                'jing_zong_money' => '',
            );
        }else{
            $array = array(
                'hui_jing_num' => $hui_jing_num['value'],
                'hui_jing_money' => $hui_jing_money['value'],
                'jing_zong_num' => $jing_zong_num['value'],
                'jing_zong_money' => $jing_zong_money['value']
            );
        }
        return $array;
    }
    public function SetUserGradeConfig($shop_id, $hui_jing_num, $hui_jing_money, $jing_zong_num, $jing_zong_money){
        $array[0] = array(
            'instance_id' => $shop_id,
            'key' => 'HUI_JING_NUM',
            'value' => $hui_jing_num,
            'desc' => '会员升经理人数',
            'is_use' => 1
        );
        $array[1] = array(
            'instance_id' => $shop_id,
            'key' => 'HUI_JING_MONEY',
            'value' => $hui_jing_money,
            'desc' => '会员升经理金额',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $shop_id,
            'key' => 'JING_ZONG_NUM',
            'value' => $jing_zong_num,
            'desc' => '经理升总监人数',
            'is_use' => 1
        );
        $array[3] = array(
            'instance_id' => $shop_id,
            'key' => 'JING_ZONG_MONEY',
            'value' => $jing_zong_money,
            'desc' => '经理升总监金额',
            'is_use' => 1
        );
        $res = $this->setConfig($array);
        return $res;
    }
    public function getShopConfig($shop_id){
        $order_auto_delinery= $this->getConfig($shop_id, 'ORDER_AUTO_DELIVERY');
        $order_balance_pay = $this->getConfig($shop_id, 'ORDER_BALANCE_PAY');
        $order_delivery_complete_time = $this->getConfig($shop_id, 'ORDER_DELIVERY_COMPLETE_TIME');
        $order_show_buy_record = $this->getConfig($shop_id, 'ORDER_SHOW_BUY_RECORD');
        $order_invoice_tax = $this->getConfig($shop_id, 'ORDER_INVOICE_TAX');
        $order_invoice_content = $this->getConfig($shop_id, 'ORDER_INVOICE_CONTENT');
        $order_delivery_pay = $this->getConfig($shop_id, 'ORDER_DELIVERY_PAY');
        $order_buy_close_time = $this->getConfig($shop_id, 'ORDER_BUY_CLOSE_TIME');
        $buyer_self_lifting = $this->getConfig($shop_id, 'BUYER_SELF_LIFTING');
        $seller_dispatching = $this->getConfig($shop_id, 'ORDER_SELLER_DISPATCHING');
        $shopping_back_points = $this->getConfig($shop_id, 'SHOPPING_BACK_POINTS');
        if(empty($order_auto_delinery) || empty($order_balance_pay) || empty($order_delivery_complete_time) || empty($order_show_buy_record) || empty($order_invoice_tax) || empty($order_invoice_content) || empty($order_delivery_pay) || empty($order_buy_close_time)){
            $this->setShopConfig($shop_id, '', '', '', '','','','','', '', '', '');
            $array = array(
                'order_auto_delinery' => '',
                'order_balance_pay' => '',
                'order_delivery_complete_time' => '',
                'order_show_buy_record' => '',
                'order_invoice_tax' => '',
                'order_invoice_content' => '',
                'order_delivery_pay' => '',
                'order_buy_close_time' => '',
                'buyer_self_lifting' => '',
                'seller_dispatching' => '',
                'shopping_back_points' => '',
            );
        }else{
            $array = array(
                'order_auto_delinery' => $order_auto_delinery['value'],
                'order_balance_pay' => $order_balance_pay['value'],
                'order_delivery_complete_time' => $order_delivery_complete_time['value'],
                'order_show_buy_record' => $order_show_buy_record['value'],
                'order_invoice_tax' => $order_invoice_tax['value'],
                'order_invoice_content' => $order_invoice_content['value'],
                'order_delivery_pay' => $order_delivery_pay['value'],
                'order_buy_close_time' => $order_buy_close_time['value'],
                'buyer_self_lifting' => $buyer_self_lifting['value'],
                'seller_dispatching' => $seller_dispatching['value'],
                'shopping_back_points' => $shopping_back_points['value'],
            );
        }
        if($array['order_buy_close_time'] == 0){
            $array['order_buy_close_time'] = 60;
        }
        
        return $array;
    }
	 public function SetCardConfig($shop_id, $hui_zhi, $jing_zhi, $zong_zhi, $hui_cong, $jing_cong, $zong_cong){
        $array[0] = array(
            'instance_id' => $shop_id,
            'key' => 'HUI_ZHI',
            'value' => $hui_zhi,
            'desc' => '级别为会员时,直接推荐一个会员卡用户的提成',
            'is_use' => 1
        );
		$array[1] = array(
            'instance_id' => $shop_id,
            'key' => 'JING_ZHI',
            'value' => $jing_zhi,
            'desc' => '级别为经理时,直接推荐一个会员卡用户的提成',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $shop_id,
            'key' => 'ZONG_ZHI',
            'value' => $zong_zhi,
            'desc' => '级别为总监时,直接推荐一个会员卡用户的提成',
            'is_use' => 1
        );
        $array[3] = array(
            'instance_id' => $shop_id,
            'key' => 'HUI_CONG',
            'value' => $hui_cong,
            'desc' => '当级别为会员时,间接推荐一个会员卡用户的提成',
            'is_use' => 1
        );
		$array[4] = array(
            'instance_id' => $shop_id,
            'key' => 'JING_CONG',
            'value' => $jing_cong,
            'desc' => '当级别为经理时,间接推荐一个会员卡用户的提成',
            'is_use' => 1
        );
        $array[5] = array(
            'instance_id' => $shop_id,
            'key' => 'ZONG_CONG',
            'value' => $zong_cong,
            'desc' => '当级别为总监时,间接推荐一个会员卡用户的提成',
            'is_use' => 1
        );
        
        $res = $this->setConfig($array);
        return $res;
    }
	public function SetSalesConfig($shop_id, $hui_zhi, $jing_zhi, $zong_zhi, $hui_cong, $jing_cong, $zong_cong){
        $array[0] = array(
            'instance_id' => $shop_id,
            'key' => 'SALES_HUI_ZHI',
            'value' => $hui_zhi,
            'desc' => '级别为会员时,其提成为直属会员销售利差的比分比',
            'is_use' => 1
        );
		$array[1] = array(
            'instance_id' => $shop_id,
            'key' => 'SALES_JING_ZHI',
            'value' => $jing_zhi,
            'desc' => '级别为经理时,其提成为直属会员销售利差的比分比',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $shop_id,
            'key' => 'SALES_ZONG_ZHI',
            'value' => $zong_zhi,
            'desc' => '级别为总监时,其提成为直属会员销售利差的比分比',
            'is_use' => 1
        );
        $array[3] = array(
            'instance_id' => $shop_id,
            'key' => 'SALES_HUI_CONG',
            'value' => $hui_cong,
            'desc' => '级别为会员时,其提成为从属会员销售利差的比分比',
            'is_use' => 1
        );
		$array[4] = array(
            'instance_id' => $shop_id,
            'key' => 'SALES_JING_CONG',
            'value' => $jing_cong,
            'desc' => '当级别为经理时,其提成为从属会员销售利差的比分比成',
            'is_use' => 1
        );
        $array[5] = array(
            'instance_id' => $shop_id,
            'key' => 'SALES_ZONG_CONG',
            'value' => $zong_cong,
            'desc' => '当级别为总监时,其提成为从属会员销售利差的比分比',
            'is_use' => 1
        );
        
        $res = $this->setConfig($array);
        return $res;
    }
    public function SetSeoConfig($shop_id, $seo_title, $seo_meta, $seo_desc, $seo_other){
        $array[0] = array(
            'instance_id' => $shop_id,
            'key' => 'SEO_TITLE',
            'value' => $seo_title,
            'desc' => '标题附加字',
            'is_use' => 1
        );
        $array[1] = array(
            'instance_id' => $shop_id,
            'key' => 'SEO_META',
            'value' => $seo_meta,
            'desc' => '商城关键词',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $shop_id,
            'key' => 'SEO_DESC',
            'value' => $seo_desc,
            'desc' => '关键词描述',
            'is_use' => 1
        );
        $array[3] = array(
            'instance_id' => $shop_id,
            'key' => 'SEO_OTHER',
            'value' => $seo_other,
            'desc' => '其他页头信息',
            'is_use' => 1
        );
        $res = $this->setConfig($array);
        return $res;
    }
    public function SetShopConfig($shop_id, $order_auto_delinery,$order_balance_pay,$order_delivery_complete_time,$order_show_buy_record,$order_invoice_tax,$order_invoice_content ,$order_delivery_pay,$order_buy_close_time,$buyer_self_lifting,$seller_dispatching,$shopping_back_points){
            $array[0] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_AUTO_DELIVERY',
                'value' => $order_auto_delinery,
                'desc' => '订单多长时间自动完成',
                'is_use' => 1
            );
            $array[1] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_BALANCE_PAY',
                'value' => $order_balance_pay,
                'desc' => '是否开启余额支付',
                'is_use' => 1
            );
            $array[2] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_DELIVERY_COMPLETE_TIME',
                'value' => $order_delivery_complete_time,
                'desc' => '收货后多长时间自动完成',
                'is_use' => 1
            );
            $array[3] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_SHOW_BUY_RECORD',
                'value' => $order_show_buy_record,
                'desc' => '是否显示购买记录',
                'is_use' => 1
            );
            $array[4] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_INVOICE_TAX',
                'value' => $order_invoice_tax,
                'desc' => '发票税率',
                'is_use' => 1
            );
            $array[5] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_INVOICE_CONTENT',
                'value' => $order_invoice_content,
                'desc' => '发票内容',
                'is_use' => 1
            );
            $array[6] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_DELIVERY_PAY',
                'value' => $order_delivery_pay,
                'desc' => '是否开启货到付款',
                'is_use' => 1
            );
            $array[7] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_BUY_CLOSE_TIME',
                'value' => $order_buy_close_time,
                'desc' => '订单自动关闭时间',
                'is_use' => 1
            );
            $array[8] = array(
                'instance_id' => $this->instance_id,
                'key' => 'BUYER_SELF_LIFTING',
                'value' => $buyer_self_lifting,
                'desc' => '是否开启买家自提',
                'is_use' => 1
            );
            $array[9] = array(
                'instance_id' => $this->instance_id,
                'key' => 'ORDER_SELLER_DISPATCHING',
                'value' => $seller_dispatching,
                'desc' => '是否开启商家配送',
                'is_use' => 1
            );
            $array[10] = array(
                'instance_id' => $this->instance_id,
                'key' => 'SHOPPING_BACK_POINTS',
                'value' => $shopping_back_points,
                'desc' => '购物返积分设置',
                'is_use' => 1
            );
            
        $res = $this->setConfig($array);
        return $res;
    }
    public function SetIntegralConfig($shop_id, $register,$sign,$share){
        $array[0] = array(
            'instance_id' => $shop_id,
            'key' => 'REGISTER_INTEGRAL',
            'value' => $register,
            'desc' => '注册送积分',
            'is_use' => 1
        );
        $array[1] = array(
            'instance_id' => $shop_id,
            'key' => 'SIGN_INTEGRAL',
            'value' => $sign,
            'desc' => '签到送积分',
            'is_use' => 1
        );
        $array[2] = array(
            'instance_id' => $shop_id,
            'key' => 'SHARE_INTEGRAL',
            'value' => $share,
            'desc' => '分享送积分',
            'is_use' => 1
        );
        $res = $this->setConfig($array);
        return $res;
    }
    public function getIntegralConfig($shop_id){
        $register_integral = $this->getConfig($shop_id, 'REGISTER_INTEGRAL');
        $sign_integral = $this->getConfig($shop_id, 'SIGN_INTEGRAL');
        $share_integral = $this->getConfig($shop_id, 'SHARE_INTEGRAL');
        if(empty($register_integral) || empty($sign_integral) || empty($share_integral)){
            $this->SetIntegralConfig($shop_id, '', '', '');
            $array = array(
                'register_integral' => '',
                'sign_integral' => '',
                'share_integral' => '',
            );
        }else{
            $array = array(
                'register_integral' => $register_integral['is_use'],
                'sign_integral' => $sign_integral['is_use'],
                'share_integral' => $share_integral['is_use'],
            );
        }
        return $array;
    }
    /**
     * 修改状态
     * (non-PHPdoc)
     * @see \data\api\IConfig::updateConfigEnable()
     */
    public function updateConfigEnable($id, $is_use){
        $config_model=new ConfigModel();
        $data=array(
          "is_use"=>$is_use,
          "modify_time"=>date('Y-m-d H:i:s', time())  
        );
        $retval=$config_model->save($data, ["id"=>$id]);
        return $retval;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IConfig::getRegisterAndVisit()
     */
    public function getRegisterAndVisit($shop_id){
        $register_and_visit = $this->getConfig($shop_id, 'REGISTERANDVISIT');
        if(empty($register_and_visit))
        {
            //按照默认值显示生成
            
        }
        return $register_and_visit;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IConfig::setRegisterAndVisit()
     */
    public function setRegisterAndVisit($shop_id,$is_register, $register_info, $name_keyword, $pwd_len, $pwd_complexity, $terms_of_service, $is_use){
        $value_array = array(
            'is_register' => $is_register,
            'register_info' => $register_info,
            'name_keyword' => $name_keyword,
            'pwd_len' => $pwd_len,
            'pwd_complexity' => $pwd_complexity,
            'terms_of_service' => $terms_of_service
        );
        
        $data = array(
            'value' => json_encode($value_array),
            'modify_time' => date('Y-m-d H:i:s', time()),
            'is_use' => $is_use
        );
        
        $config_model=new ConfigModel();
        $res = $config_model->save($data,['key'=>'REGISTERANDVISIT','instance_id'=>$shop_id]);
        return $res;
    }
    
	/* (non-PHPdoc)
     * @see \data\api\IConfig::databaseList()
     */
    public function getDatabaseList()
    {
        // TODO Auto-generated method stub
        $databaseList = Db::query("SHOW TABLE STATUS");
        return $databaseList;
    }
    /**
     * 查询物流跟踪的配置信息
     * @param unknown $shop_id
     */
    public function getOrderExpressMessageConfig($shop_id){
        $express_detail = $this->config_module->getInfo(['instance_id'=>$shop_id,'key'=>'ORDER_EXPRESS_MESSAGE'], 'value,is_use');
        if(empty($express_detail['value']))
        {
            return array(
                'value' => array(
                    'appid' => '',
                    'appkey' => '',
                    'back_url' => '',
                ),
                'is_use' => 0,
            );
        }else{
            $express_detail['value'] = json_decode($express_detail['value'], true);
            return $express_detail;
        }
    }
    /**
     * 更新物流跟踪的配置信息
     * @param unknown $shop_id
     * @param unknown $appid
     * @param unknown $appkey
     * @param unknown $is_use
     */
    public function updateOrderExpressMessageConfig($shop_id, $appid, $appkey, $back_url, $is_use){
        $express_detail = $this->config_module->getInfo(['instance_id'=>$shop_id,'key'=>'ORDER_EXPRESS_MESSAGE'], 'value,is_use');
        $value=array(
          "appid"=>$appid,
          "appkey"=>$appkey,
          "back_url"=>$back_url
        );
        $value=json_encode($value);
        $config_model=new ConfigModel();
        if(empty($express_detail)){
            $data=array(
              "instance_id"=>$shop_id,
              "key"=>'ORDER_EXPRESS_MESSAGE',
              "value"=> $value,
              "create_time"=> date('Y-m-d H:i:s', time()),
              "modify_time"=> date('Y-m-d H:i:s', time()),
              "desc"=>"物流跟踪配置信息",
              "is_use"=>$is_use
            );
            $config_model->save($data);
            return $config_model->id;
        }else{
            $data=array(
                "key"=>'ORDER_EXPRESS_MESSAGE',
                "value"=> $value,
                "modify_time"=> date('Y-m-d H:i:s', time()),
                "is_use"=>$is_use
            );
            $result=$config_model->save($data, ["instance_id"=>$shop_id, "key"=>"ORDER_EXPRESS_MESSAGE"]);
            return $result;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IConfig::getOperateConfig()
     */
    public function getOperateConfig(){
        $operate_config_info = $this->getConfig(0, 'OPERATE_CONFIG');
        
        if(empty($operate_config_info)){
            $operate_config = array(
                'is_discount_open'           => 0,
                'is_discount_toExamine'      => 0,
                'is_mansong_open'            => 0,
                'is_mansong_toExamine'       => 0,
                'is_groups_open'             => 0,
                'is_groups_toExamine'        => 0,
                'is_pickuPpoint_open'        => 0
            );
            $this->addConfig(0, 'OPERATE_CONFIG',json_encode($operate_config), '运营配置信息', 1);
        }else{
            $operate_config = json_decode($operate_config_info['value']);
        }
        return $operate_config;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IConfig::updateOperateConfig()
     */
    public function updateOperateConfig($config_value){  //读取是在关键词：OPERATE_CONFIG，不能改变可能有错误
        $res = $this->updateConfig(0, 'OPERATE_CONFIG', $config_value, '运营配置信息', 1);
        return $res;
    }
}