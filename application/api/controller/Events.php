<?php
/**
 * Events.php
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
use data\service\Events as EventsService;


class Events extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new EventsService();
    }
    
    /**
     * 订单长时间未付款自动交易关闭
     */
    public function ordersClose(){
        $retval = $this->service->ordersClose();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 订单收货后7天自动交易完成
    */
    public function ordersComplete(){
        $retval = $this->service->ordersComplete();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 满减送超过期限自动关闭, 进入时间自动开始
    */
    public function mansongOperation(){
        $retval = $this->service->mansongOperation();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 赠品超过有效期限自动取消
    */
    public function giftClose(){
        $retval = $this->service->giftClose();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 限时折扣自动开始以及自动关闭
    */
    public function discountOperation(){
        $retval = $this->service->discountOperation();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 自动收货
    */
    public function autoDeilvery(){
        $retval = $this->service->autoDeilvery();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}