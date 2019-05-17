<?php
/**
 * Upgrade.php
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
use data\service\Upgrade as UpgradeService;


class Upgrade extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new UpgradeService();
    }
    
    public function getVersionPatch(){
        $retval = $this->service->getVersionPatch();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获得当前域名的授权信息
    */
    public function getUserDevolution(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $retval = $this->service->getUserDevolution($user_name, $password);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询可以升级的补丁信息
     * @param unknown $patch_release
     * @param unknown $host_url
     * @param unknown $devolution_version
     * @param unknown $devolution_code
    */
    public function getVersionPatchList(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $retval = $this->service->getVersionPatchList($user_name, $password);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 版本补丁列表
     * (non-PHPdoc)
    */
    public function getProductPatchList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getProductPatchList($page_index, $page_size,  $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询补丁的具体信息
     * @param unknown $patch_release
     * @param unknown $devolution_version
     * @param unknown $devolution_code
    */
    public function getVersionPatchDetail(){
        $patch_release = isset($this->request_common_array['patch_release']) ? $this->request_common_array['patch_release'] : '';
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $retval = $this->service->getVersionPatchDetail($patch_release, $user_name, $password);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 修改更新状态
    */
    public function updateVersionPatchState(){
        $patch_release = isset($this->request_common_array['patch_release']) ? $this->request_common_array['patch_release'] : '';
        $res = $this->service->updateVersionPatchState($patch_release);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 查询需要升级的所有数据
    */
    public function getUpgradePatchList(){
        $retval = $this->service->getUpgradePatchList();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     *  查询授权账户表是否有数据
    */
    public function getVersionDevolution(){
        $retval = $this->service->getVersionDevolution();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 给授权账户表添加一条数据
    */
    public function addVersionDevolution(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $res = $this->service->addVersionDevolution($user_name, $password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}