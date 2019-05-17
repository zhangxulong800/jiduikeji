<?php
/**
 * User.php
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
use data\service\User as UserService;


class User extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new UserService();
    }
    
    /**
     * 获取当前登录用户的uid
     */
    public function getSessionUid(){
        $retval = $this->service->getSessionUid();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取当前登录用户的实例ID
    */
    public function getSessionInstanceId(){
        $retval = $this->service->getSessionInstanceId();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 根据用户名获取用户信息
     * @param unknown $username
    */
    public function getUserInfoByUsername(){
        $username = isset($this->request_common_array['username']) ? $this->request_common_array['username'] : '';
        $retval = $this->service->getUserInfoByUsername($username);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 根据用户名修改密码
     * @param unknown $username
     * @param unknown $password
    */
    public function  updateUserInfoByUsername(){
        $username = isset($this->request_common_array['username']) ? $this->request_common_array['username'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $res = $this->service->updateUserInfoByUsername($username,$password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 根据用户id修改密码
    */
    public function updateUserInfoByUserid(){
        $userid = isset($this->request_common_array['userid']) ? $this->request_common_array['userid'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $res = $this->service->updateUserInfoByUserid($userid,$password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取当前登录用户是否是总系统管理员
    */
    public function getSessionUserIsAdmin(){
        $retval = $this->service->getSessionUserIsAdmin();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取当前登录会员是否是前台会员
    */
    public function getSessionUserIsMember(){
        $retval = $this->service->getSessionUserIsMember();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取当前登录会员是否是系统会员
    */
    public function getSessionUserIsSystem(){
        $retval = $this->service->getSessionUserIsSystem();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取当前登录用户的权限列
    */
    public function getSessionModuleIdArray(){
        $retval = $this->service->getSessionModuleIdArray();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取当前实例
    */
    public function getInstanceName(){
        $retval = $this->service->getInstanceName();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * qq登录
     *
     * @param unknown $qq
    */
    public function qqLogin(){
        $qq = isset($this->request_common_array['qq']) ? $this->request_common_array['qq'] : '';
        $res = $this->service->qqLogin($qq);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            $data['uid'] = \think\Session::get('uid');
            $data['instance_id'] = \think\Session::get('instance_id');
            $data['instance_name'] = \think\Session::get('instance_name');
            $token = niuEncrypt(json_encode($data), $key);
        }else{
            $token = '';
        }
        if($token){
            return $this->outMessage($token, 0, '登录成功');
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 微信登录
     *
     * @param unknown $openid
    */
    public function wchatLogin(){
        $openid = isset($this->request_common_array['openid']) ? $this->request_common_array['openid'] : '';
        $res = $this->service->wchatLogin($openid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            $data['uid'] = \think\Session::get('uid');
            $data['instance_id'] = \think\Session::get('instance_id');
            $data['instance_name'] = \think\Session::get('instance_name');
            $token = niuEncrypt(json_encode($data), $key);
        }else{
            $token = '';
        }
        if($token){
            return $this->outMessage($token, 0, '登录成功');
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 微信unionid登录
     * @param unknown $unionid
    */
    public function wchatUnionLogin(){
        $unionid = isset($this->request_common_array['unionid']) ? $this->request_common_array['unionid'] : '';
        $res = $this->service->wchatUnionLogin($unionid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            $data['uid'] = \think\Session::get('uid');
            $data['instance_id'] = \think\Session::get('instance_id');
            $data['instance_name'] = \think\Session::get('instance_name');
            $token = niuEncrypt(json_encode($data), $key);
        }else{
            $token = '';
        }
        if($token){
            return $this->outMessage($token, 0, '登录成功');
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 更新会员openid与unionid
     * @param unknown $wx_openid
     * @param unknown $wx_unionid
    */
    public function modifyUserWxhatLogin(){
        $wx_openid = isset($this->request_common_array['wx_openid']) ? $this->request_common_array['wx_openid'] : '';
        $wx_unionid = isset($this->request_common_array['wx_unionid']) ? $this->request_common_array['wx_unionid'] : '';
        $res = $this->service->modifyUserWxhatLogin($wx_openid, $wx_unionid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 系统添加会员
     *
     * @param unknown $user_name
     * @param unknown $password
     * @param unknown $email
     * @param unknown $mobile
     * @param unknown $is_system
     * @param unknown $user_qq_id
     * @param unknown $qq_info
     * @param unknown $wx_openid
     * @param unknown $wx_info
    */
    public function add(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $email = isset($this->request_common_array['email']) ? $this->request_common_array['email'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $is_system = isset($this->request_common_array['is_system']) ? $this->request_common_array['is_system'] : '';
        $qq_openid = isset($this->request_common_array['qq_openid']) ? $this->request_common_array['qq_openid'] : '';
        $qq_info = isset($this->request_common_array['qq_info']) ? $this->request_common_array['qq_info'] : '';
        $wx_openid = isset($this->request_common_array['wx_openid']) ? $this->request_common_array['wx_openid'] : '';
        $wx_info = isset($this->request_common_array['wx_info']) ? $this->request_common_array['wx_info'] : '';
        $wx_unionid = isset($this->request_common_array['wx_unionid']) ? $this->request_common_array['wx_unionid'] : '';
        $is_member = isset($this->request_common_array['is_member']) ? $this->request_common_array['is_member'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $res = $this->service->add($user_name, $password, $email, $mobile, $is_system, $qq_openid, $qq_info, $wx_openid, $wx_info, $wx_unionid, $is_member, $instance_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 系统用户登录
     *
     * @param unknown $user_name
     * @param unknown $password
    */
    public function login(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $key = isset($this->request_common_array['key']) ? $this->request_common_array['key'] : '';
        $res = $this->service->login($user_name, $password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            $data['uid'] = \think\Session::get('uid');
            $data['instance_id'] = \think\Session::get('instance_id');
            $data['instance_name'] = \think\Session::get('instance_name');
            $token = niuEncrypt(json_encode($data), $key);
        }else{
            $token = '';
        }
        if($token){
            return $this->outMessage($token, 0, '登录成功');
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 检测用户是否具有打开权限
     *
     * @param unknown $module_id
    */
    public function checkAuth(){
        $module_id = isset($this->request_common_array['module_id']) ? $this->request_common_array['module_id'] : '';
        $res = $this->service->checkAuth($module_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 系统用户修改密码
     *
     * @param unknown $uid
     * @param unknown $old_password
     * @param unknown $new_password
    */
    public function ModifyUserPassword(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $old_password = isset($this->request_common_array['old_password']) ? $this->request_common_array['old_password'] : '';
        $new_password = isset($this->request_common_array['new_password']) ? $this->request_common_array['new_password'] : '';
        $res = $this->service->ModifyUserPassword($uid, $old_password, $new_password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加用户日志
     *
     * @param unknown $uid
     * @param unknown $is_system
     * @param unknown $controller
     * @param unknown $method
     * @param unknown $ip
     * @param unknown $get_data
    */
    public function addUserLog(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $is_system = isset($this->request_common_array['is_system']) ? $this->request_common_array['is_system'] : '';
        $controller = isset($this->request_common_array['controller']) ? $this->request_common_array['controller'] : '';
        $method = isset($this->request_common_array['method']) ? $this->request_common_array['method'] : '';
        $ip = isset($this->request_common_array['ip']) ? $this->request_common_array['ip'] : '';
        $get_data = isset($this->request_common_array['get_data']) ? $this->request_common_array['get_data'] : '';
        $res = $this->service->addUserLog($uid, $is_system, $controller, $method, $ip, $get_data);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取用户详细信息
    */
    public function getUserDetail(){
        $retval = $this->service->getUserDetail();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 用户锁定
     *
     * @param unknown $uid
    */
    public function userLock(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->userLock($uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 用户解锁
     *
     * @param unknown $uid
    */
    public function userUnlock(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->userUnlock($uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 用户退出
    */
    public function Logout(){
        $res = $this->service->Logout();
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改手机
     *
     * @param unknown $mobile
    */
    public function modifyMobile(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $res = $this->service->modifyMobile($uid, $mobile);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改昵称
     *
     * @param unknown $uid
     * @param unknown $nickname
    */
    public function modifyNickName(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $nickname = isset($this->request_common_array['nickname']) ? $this->request_common_array['nickname'] : '';
        $res = $this->service->modifyNickName($uid, $nickname);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改邮箱
     *
     * @param unknown $email
    */
    public function modifyEmail(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $email = isset($this->request_common_array['email']) ? $this->request_common_array['email'] : '';
        $res = $this->service->modifyEmail($uid, $email);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改qq
     *
     * @param unknown $uid
     * @param unknown $qq
    */
    public function modifyQQ(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $qq = isset($this->request_common_array['qq']) ? $this->request_common_array['qq'] : '';
        $res = $this->service->modifyQQ($uid, $qq);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 重置密码 123456
     *
     * @param unknown $uid
    */
    public function resetUserPassword($uid){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->resetUserPassword($uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改头像
     *
     * @param unknown $uid
     * @param unknown $user_headimg
    */
    public function ModifyUserHeadimg(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $user_headimg = isset($this->request_common_array['user_headimg']) ? $this->request_common_array['user_headimg'] : '';
        $res = $this->service->ModifyUserHeadimg($uid, $user_headimg);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 会员手机号 绑定
     *
     * @param unknown $uid
    */
    public function userTelBind(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->userTelBind($uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 会员手机号 解除绑定
     *
     * @param unknown $uid
    */
    public function removeUserTelBind(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->removeUserTelBind($uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 会员邮箱 绑定
     *
     * @param unknown $uid
    */
    public function userEmailBind(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->userEmailBind($uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 会员邮箱 解除绑定
     *
     * @param unknown $uid
    */
    public function removeUserEmailBind(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->removeUserEmailBind($uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 判断 某qq 是否 已经绑定
     *
     * @param unknown $qq_openid
    */
    public function checkUserQQopenid(){
        $qq_openid = isset($this->request_common_array['qq_openid']) ? $this->request_common_array['qq_openid'] : '';
        $res = $this->service->checkUserQQopenid($qq_openid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 绑定 qq
     *
     * @param unknown $uid
     * @param unknown $qq_openid
     * @param unknown $qq_info
    */
    public function bindQQ(){
        $qq_openid = isset($this->request_common_array['qq_openid']) ? $this->request_common_array['qq_openid'] : '';
        $qq_info = isset($this->request_common_array['qq_info']) ? $this->request_common_array['qq_info'] : '';
        $res = $this->service->bindQQ($qq_openid, $qq_info);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 解除 会员 绑定
    */
    public function removeBindQQ(){
        $res = $this->service->removeBindQQ();
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询手机号是否已被绑定
     *
     * @param unknown $mobile
    */
    public function memberIsMobile(){
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $retval = $this->service->memberIsMobile($mobile);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询邮箱是否已被绑定
     *
     * @param unknown $email
    */
    public function memberIsEmail(){
        $email = isset($this->request_common_array['email']) ? $this->request_common_array['email'] : '';
        $retval = $this->service->memberIsEmail($email);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 根据uid查询用户信息
     *
     * @param unknown $uid
    */
    public function getUserInfoByUid(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getUserInfoByUid($uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取用户信息
     * @param unknown $uid
    */
    public function getUserInfoDetail(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getUserInfoDetail($uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 检测用户是否关注了实例公众号(应用多用户版)
     * @param unknown $uid
     * @param unknown $instance_id
    */
    public function checkUserIsSubscribeInstance(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->checkUserIsSubscribeInstance($uid, $instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 检测用户是否关注了当前实例（单用户版）
     * @param unknown $uid
    */
    public function checkUserIsSubscribe(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->checkUserIsSubscribe($uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取用户
     * @param unknown $condition
    */
    public function getUserCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getUserCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取用户详细详情
     * @param unknown $uid
    */
    public function getUserInfo(){
        $retval = $this->service->getUserInfo();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 修改会员信息
     * @param unknown $uid
     * @param unknown $user_name
     * @param unknown $email
     * @param unknown $mobile
     * @param unknown $nick_name
    */
    public function updateUserInfo(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $email = isset($this->request_common_array['email']) ? $this->request_common_array['email'] : '';
        $sex = isset($this->request_common_array['sex']) ? $this->request_common_array['sex'] : '';
        $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $nick_name = isset($this->request_common_array['nick_name']) ? $this->request_common_array['nick_name'] : '';
        $res = $this->service->updateUserInfo($uid, $user_name, $email, $sex, $status, $mobile, $nick_name);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 检测手机号是否存在
     * @param unknown $mobile
    */
    public function checkMobileIsHas(){
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $retval = $this->service->checkMobileIsHas($mobile);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 根据用户邮箱更改密码
    */
    public function updateUserPasswordByEmail(){
        $userInfo = isset($this->request_common_array['userInfo']) ? $this->request_common_array['userInfo'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $res = $this->service->updateUserPasswordByEmail($userInfo,$password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 根据用户邮箱更改密码
    */
    public function updateUserPasswordByMobile(){
        $userInfo = isset($this->request_common_array['userInfo']) ? $this->request_common_array['userInfo'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $res = $this->service->updateUserPasswordByMobile($userInfo,$password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}