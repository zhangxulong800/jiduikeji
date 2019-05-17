<?php
/**
 * Admin.php
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
use data\service\AdminUser as AdminUserService;


class Admin extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new AdminUserService();
    }
    
    /**
     * 获取用户的权限子项
     * @param unknown $moduleid（0标示根节点子项）
     */
    public function getchildModuleQuery(){
        $moduleid = isset($this->request_common_array['moduleid']) ? $this->request_common_array['moduleid'] : '';
        $retval = $this->service->getchildModuleQuery($moduleid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 后台操作用户列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function adminUserList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->adminUserList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加后台用户
     * @param string $user_name
     * @param number $group_id
     * @param string $user_password
     * @param string $desc
    */
    public function addAdminUser(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $user_password = isset($this->request_common_array['user_password']) ? $this->request_common_array['user_password'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $res = $this->service->addAdminUser($user_name, $group_id, $user_password, $desc, $instance_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改后台用户
     * @param number $uid
     * @param string $user_name
     * @param number $group_id
     * @param string $user_password
     * @param string $desc
    */
    public function editAdminUser(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $res = $this->service->editAdminUser($uid, $user_name, $group_id, $desc);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 用户日志列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function getUserLogList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getUserLogList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 根据系统用户组id 获取 当前用户组下用户数量
    */
    public function getAdminUserCountByGroupIdArray(){
        $condtion = isset($this->request_common_array['condtion']) ? $this->request_common_array['condtion'] : '';
        $retval = $this->service->getAdminUserCountByGroupIdArray($condtion);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}