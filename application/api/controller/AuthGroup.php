<?php
/**
 * AuthGroup.php
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
use data\service\AuthGroup as AuthGroupService;


class AuthGroup extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new AuthGroupService();
    }
    
    /**
     * 获取系统用户组
     * @param unknown $where
     * @param unknown $order
     * @param unknown $page_size
     * @param unknown $page_index
     */
    public function getSystemUserGroupList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getSystemUserGroupList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加系统用户组
     * @param unknown $group_id
     * @param unknown $group_name
     * @param unknown $is_system
     * @param unknown $module_id_array
     * @param unknown $desc
    */
    public function addSystemUserGroup(){
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $group_name = isset($this->request_common_array['group_name']) ? $this->request_common_array['group_name'] : '';
        $is_system = isset($this->request_common_array['is_system']) ? $this->request_common_array['is_system'] : '';
        $module_id_array = isset($this->request_common_array['module_id_array']) ? $this->request_common_array['module_id_array'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $res = $this->service->addSystemUserGroup($group_id, $group_name, $is_system, $module_id_array, $desc);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改系统用户组
     * @param unknown $group_id
     * @param unknown $group_name
     * @param unknown $is_system
     * @param unknown $module_id_array
     * @param unknown $desc
    */
    public function updateSystemUserGroup(){
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $group_name = isset($this->request_common_array['group_name']) ? $this->request_common_array['group_name'] : '';
        $group_status = isset($this->request_common_array['group_status']) ? $this->request_common_array['group_status'] : '';
        $is_system = isset($this->request_common_array['is_system']) ? $this->request_common_array['is_system'] : '';
        $module_id_array = isset($this->request_common_array['module_id_array']) ? $this->request_common_array['module_id_array'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $res = $this->service->updateSystemUserGroup($group_id, $group_name, $group_status, $is_system, $module_id_array, $desc);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改用户组的状态
     * @param unknown $group_id
     * @param unknown $group_status
    */
    public function ModifyUserGroupStatus(){
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $group_status = isset($this->request_common_array['group_status']) ? $this->request_common_array['group_status'] : '';
        $res = $this->service->ModifyUserGroupStatus($group_id, $group_status);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除用户组
     * @param unknown $group_id
    */
    public function deleteSystemUserGroup($group_id){
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $res = $this->service->deleteSystemUserGroup($group_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取用户组详情
     * @param unknown $group_id
    */
    public function getSystemUserGroupDetail(){
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $retval = $this->service->getSystemUserGroupDetail($group_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询所有用户组
    */
    public function getSystemUserGroupAll(){
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $retval = $this->service->getSystemUserGroupAll($where);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}