<?php
/**
 * WebSite.php
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
use data\service\WebSite as WebSiteService;


class WebSite extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new WebSiteService();
    }
    
    /**
     * 获取版本号
     */
    public function getVersion(){
        $retval = $this->service->getVersion();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取网站信息
     *
     * @param string $field
    */
    public function getWebSiteInfo(){
        $retval = $this->service->getWebSiteInfo();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改网站信息
     *
     * @param unknown $title
     * @param unknown $logo
     * @param unknown $web_desc
     * @param unknown $key_words
     * @param unknown $web_icp
     * @param unknown $web_style
     * @param unknown $web_qrcode
    */
    public function updateWebSite(){
        $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
        $logo = isset($this->request_common_array['logo']) ? $this->request_common_array['logo'] : '';
        $web_desc = isset($this->request_common_array['web_desc']) ? $this->request_common_array['web_desc'] : '';
        $key_words = isset($this->request_common_array['key_words']) ? $this->request_common_array['key_words'] : '';
        $web_icp = isset($this->request_common_array['web_icp']) ? $this->request_common_array['web_icp'] : '';
        $web_style = isset($this->request_common_array['web_style']) ? $this->request_common_array['web_style'] : '';
        $web_qrcode = isset($this->request_common_array['web_qrcode']) ? $this->request_common_array['web_qrcode'] : '';
        $web_url = isset($this->request_common_array['web_url']) ? $this->request_common_array['web_url'] : '';
        $web_phone = isset($this->request_common_array['web_phone']) ? $this->request_common_array['web_phone'] : '';
        $web_email = isset($this->request_common_array['web_email']) ? $this->request_common_array['web_email'] : '';
        $web_qq = isset($this->request_common_array['web_qq']) ? $this->request_common_array['web_qq'] : '';
        $web_weixin = isset($this->request_common_array['web_weixin']) ? $this->request_common_array['web_weixin'] : '';
        $web_address = isset($this->request_common_array['web_address']) ? $this->request_common_array['web_address'] : '';
        $web_status = isset($this->request_common_array['web_status']) ? $this->request_common_array['web_status'] : '';
        $wap_status = isset($this->request_common_array['wap_status']) ? $this->request_common_array['wap_status'] : '';
        $third_count = isset($this->request_common_array['third_count']) ? $this->request_common_array['third_count'] : '';
        $close_reason = isset($this->request_common_array['close_reason']) ? $this->request_common_array['close_reason'] : '';
        $res = $this->service->updateWebSite($title, $logo, $web_desc, $key_words, $web_icp, $web_style, $web_qrcode, $web_url, $web_phone, $web_email, $web_qq, $web_weixin, $web_address,$web_status,$wap_status,$third_count,$close_reason);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加系统模块
     *
     * @param unknown $module_id
     * @param unknown $module_name
     * @param unknown $controller
     *            控制器名
     * @param unknown $method
     *            方法名
     * @param unknown $pid
     *            上级模块ID
     * @param unknown $url
     *            链接url
     * @param unknown $is_menu
     *            是否是菜单
     * @param unknown $is_dev
     *            是否开发者模式可见
     * @param unknown $sort
     *            排序号
     * @param unknown $desc
     *            备注
    */
    public function addSytemModule(){
        $module_name = isset($this->request_common_array['module_name']) ? $this->request_common_array['module_name'] : '';
        $controller = isset($this->request_common_array['controller']) ? $this->request_common_array['controller'] : '';
        $method = isset($this->request_common_array['method']) ? $this->request_common_array['method'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
        $is_menu = isset($this->request_common_array['is_menu']) ? $this->request_common_array['is_menu'] : '';
        $is_dev = isset($this->request_common_array['is_dev']) ? $this->request_common_array['is_dev'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $module_picture = isset($this->request_common_array['module_picture']) ? $this->request_common_array['module_picture'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $icon_class = isset($this->request_common_array['icon_class']) ? $this->request_common_array['icon_class'] : '';
        $is_control_auth = isset($this->request_common_array['is_control_auth']) ? $this->request_common_array['is_control_auth'] : '';
        $res = $this->service->addSytemModule($module_name, $controller, $method, $pid, $url, $is_menu, $is_dev, $sort, $module_picture, $desc, $icon_class, $is_control_auth);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改系统模块
     *
     * @param unknown $module_name
     * @param unknown $controller
     * @param unknown $method
     * @param unknown $pid
     * @param unknown $url
     * @param unknown $is_menu
     * @param unknown $is_dev
     * @param unknown $sort
     * @param unknown $desc
    */
    public function updateSystemModule(){
        $module_id = isset($this->request_common_array['module_id']) ? $this->request_common_array['module_id'] : '';
        $module_name = isset($this->request_common_array['module_name']) ? $this->request_common_array['module_name'] : '';
        $controller = isset($this->request_common_array['controller']) ? $this->request_common_array['controller'] : '';
        $method = isset($this->request_common_array['method']) ? $this->request_common_array['method'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
        $is_menu = isset($this->request_common_array['is_menu']) ? $this->request_common_array['is_menu'] : '';
        $is_dev = isset($this->request_common_array['is_dev']) ? $this->request_common_array['is_dev'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $module_picture = isset($this->request_common_array['module_picture']) ? $this->request_common_array['module_picture'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $icon_class = isset($this->request_common_array['icon_class']) ? $this->request_common_array['icon_class'] : '';
        $is_control_auth = isset($this->request_common_array['is_control_auth']) ? $this->request_common_array['is_control_auth'] : '';
        $res = $this->service->updateSystemModule($module_id, $module_name, $controller, $method, $pid, $url, $is_menu, $is_dev, $sort, $module_picture, $desc, $icon_class, $is_control_auth);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除系统模块
     *
     * @param unknown $module_id
    */
    public function deleteSystemModule(){
        $module_id = isset($this->request_common_array['module_id']) ? $this->request_common_array['module_id'] : '';
        $res = $this->service->deleteSystemModule($module_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取系统模块
     *
     * @param unknown $module_id
    */
    public function getSystemModuleInfo(){
        $module_id = isset($this->request_common_array['module_id']) ? $this->request_common_array['module_id'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getSystemModuleInfo($module_id, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取系统模块列表
     *
     * @param unknown $where
     * @param unknown $order
     * @param unknown $page_size
     * @param unknown $page_index
    */
    public function getSystemModuleList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getSystemModuleList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 根据当前实例查询权限列表
    */
    public function getInstanceModuleQuery(){
        $retval = $this->service->getInstanceModuleQuery();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加用户实例
     *
     * @param unknown $uid
     * @param unknown $type
    */
    public function addSystemInstance(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $instance_name = isset($this->request_common_array['instance_name']) ? $this->request_common_array['instance_name'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $res = $this->service->addSystemInstance($uid, $instance_name, $type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改系统实例
    */
    public function updateSystemInstance(){
        $res = $this->service->updateSystemInstance();
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取系统实例
     *
     * @param unknown $instance_id
    */
    public function getSystemInstance(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getSystemInstance($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询系统实例列表
     *
     * @param unknown $where
     * @param unknown $order
     * @param unknown $page_size
     * @param unknown $page_index
    */
    public function getSystemInstanceList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getSystemInstanceList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 根据模块和方案查询权限
     *
     * @param unknown $controller
     * @param unknown $action
    */
    public function getModuleIdByModule(){
        $controller = isset($this->request_common_array['controller']) ? $this->request_common_array['controller'] : '';
        $action = isset($this->request_common_array['action']) ? $this->request_common_array['action'] : '';
        $retval = $this->service->getModuleIdByModule($controller, $action);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取下级列表子项
     *
     * @param unknown $pid
    */
    public function getModuleListByParentId(){
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $retval = $this->service->getModuleListByParentId($pid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取当前module的根节点以及二级节点
     *
     * @param unknown $module_id
    */
    public function getModuleRootAndSecondMenu(){
        $module_id = isset($this->request_common_array['module_id']) ? $this->request_common_array['module_id'] : '';
        $retval = $this->service->getModuleRootAndSecondMenu($module_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改模块单个字段 根据主键id
     *
     * @param unknown $module_id
     *            主键
     * @param unknown $field_name
     *            字段名称
     * @param unknown $field_value
     *            字段值
    */
    public function ModifyModuleField(){
        $module_id = isset($this->request_common_array['module_id']) ? $this->request_common_array['module_id'] : '';
        $field_name = isset($this->request_common_array['field_name']) ? $this->request_common_array['field_name'] : '';
        $field_value = isset($this->request_common_array['field_value']) ? $this->request_common_array['field_value'] : '';
        $res = $this->service->ModifyModuleField($module_id, $field_name, $field_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取模板样式
    */
    public function getWebStyle(){
        $retval = $this->service->getWebStyle();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取模板列表
    */
    public function getWebStyleList(){
        $retval = $this->service->getWebStyleList();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取平台信息
    */
    public function getWebDetail(){
        $retval = $this->service->getWebDetail();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}