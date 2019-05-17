<?php
/**
 * Weixin.php
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
use data\service\Weixin as WeixinService;


class Weixin extends BaseController
{
    public function __construct(){
        parent::__construct();
        $this->service = new WeixinService();
    }
    
    /**
     * 获取微信菜单列表
     *
     * @param unknown $instance_id
     * @param unknown $pid
     *            当pid=''查询全部
     */
    public function getWeixinMenuList(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $retval = $this->service->getWeixinMenuList($instance_id, $pid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加微信菜单
     *
     * @param unknown $indtance_id
     * @param unknown $menu_name
     * @param unknown $ico
     * @param unknown $pid
     * @param unknown $menu_event_type
     * @param unknown $menu_event_url
     * @param unknown $sort
    */
    public function addWeixinMenu(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $menu_name = isset($this->request_common_array['menu_name']) ? $this->request_common_array['menu_name'] : '';
        $ico = isset($this->request_common_array['ico']) ? $this->request_common_array['ico'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $menu_event_type = isset($this->request_common_array['menu_event_type']) ? $this->request_common_array['menu_event_type'] : '';
        $menu_event_url = isset($this->request_common_array['menu_event_url']) ? $this->request_common_array['menu_event_url'] : '';
        $media_id = isset($this->request_common_array['media_id']) ? $this->request_common_array['media_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addWeixinMenu($instance_id, $menu_name, $ico, $pid, $menu_event_type, $menu_event_url, $media_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改微信菜单
     *
     * @param unknown $menu_id
     * @param unknown $instance_id
     * @param unknown $menu_name
     * @param unknown $ico
     * @param unknown $pid
     * @param unknown $menu_event_type
     * @param unknown $menu_event_url
     * @param unknown $sort
    */
    public function updateWeixinMenu(){
        $menu_id = isset($this->request_common_array['menu_id']) ? $this->request_common_array['menu_id'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $menu_name = isset($this->request_common_array['menu_name']) ? $this->request_common_array['menu_name'] : '';
        $ico = isset($this->request_common_array['ico']) ? $this->request_common_array['ico'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $menu_event_type = isset($this->request_common_array['menu_event_type']) ? $this->request_common_array['menu_event_type'] : '';
        $menu_event_url = isset($this->request_common_array['menu_event_url']) ? $this->request_common_array['menu_event_url'] : '';
        $media_id = isset($this->request_common_array['media_id']) ? $this->request_common_array['media_id'] : '';
        $res = $this->service->updateWeixinMenu($menu_id, $instance_id, $menu_name, $ico, $pid, $menu_event_type, $menu_event_url, $media_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改菜单排序
     *
     * @param unknown $menu_id_arr
     * @param unknown $sort
    */
    public function updateWeixinMenuSort(){
        $menu_id_arr = isset($this->request_common_array['menu_id_arr']) ? $this->request_common_array['menu_id_arr'] : '';
        $res = $this->service->updateWeixinMenuSort($menu_id_arr);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改菜单名称
     *
     * @param unknown $menu_id
     * @param unknown $menu_name
    */
    public function updateWeixinMenuName(){
        $menu_id = isset($this->request_common_array['menu_id']) ? $this->request_common_array['menu_id'] : '';
        $menu_name = isset($this->request_common_array['menu_name']) ? $this->request_common_array['menu_name'] : '';
        $res = $this->service->updateWeixinMenuName($menu_id, $menu_name);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改跳转链接
     *
     * @param unknown $menu_id
     * @param unknown $menu_eventurl
    */
    public function updateWeixinMenuUrl(){
        $menu_id = isset($this->request_common_array['menu_id']) ? $this->request_common_array['menu_id'] : '';
        $menu_event_url = isset($this->request_common_array['menu_event_url']) ? $this->request_common_array['menu_event_url'] : '';
        $res = $this->service->updateWeixinMenuUrl($menu_id, $menu_event_url);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改菜单类型，1：文本，2：单图文，3：多图文
     *
     * @param unknown $menu_id
     * @param unknown $menu_event_type
    */
    public function updateWeixinMenuEventType(){
        $menu_id = isset($this->request_common_array['menu_id']) ? $this->request_common_array['menu_id'] : '';
        $menu_event_type = isset($this->request_common_array['menu_event_type']) ? $this->request_common_array['menu_event_type'] : '';
        $res = $this->service->updateWeixinMenuEventType($menu_id, $menu_event_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改图文消息
     *
     * @param unknown $menu_id
     * @param unknown $media_id
     * @param unknown $menu_event_type
    */
    public function updateWeiXinMenuMessage(){
        $menu_id = isset($this->request_common_array['menu_id']) ? $this->request_common_array['menu_id'] : '';
        $media_id = isset($this->request_common_array['media_id']) ? $this->request_common_array['media_id'] : '';
        $menu_event_type = isset($this->request_common_array['menu_event_type']) ? $this->request_common_array['menu_event_type'] : '';
        $res = $this->service->updateWeiXinMenuMessage($menu_id, $media_id, $menu_event_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加微信菜单点击数
     *
     * @param unknown $menu_id
    */
    public function addMenuHits(){
        $menu_id = isset($this->request_common_array['menu_id']) ? $this->request_common_array['menu_id'] : '';
        $res = $this->service->addMenuHits($menu_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取微信菜单详情
     *
     * @param unknown $menu_id
    */
    public function getWeixinMenuDetail(){
        $menu_id = isset($this->request_common_array['menu_id']) ? $this->request_common_array['menu_id'] : '';
        $retval = $this->service->getWeixinMenuDetail($menu_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 公众号授权
     *
     * @param unknown $instance_id
     * @param unknown $authorizer_appid
     * @param unknown $authorizer_refresh_token
     * @param unknown $authorizer_access_token
     * @param unknown $func_info
     * @param unknown $nick_name
     * @param unknown $head_img
     * @param unknown $user_name
     * @param unknown $alias
     * @param unknown $qrcode_url
    */
    public function addWeixinAuth(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $authorizer_appid = isset($this->request_common_array['authorizer_appid']) ? $this->request_common_array['authorizer_appid'] : '';
        $authorizer_refresh_token = isset($this->request_common_array['authorizer_refresh_token']) ? $this->request_common_array['authorizer_refresh_token'] : '';
        $authorizer_access_token = isset($this->request_common_array['authorizer_access_token']) ? $this->request_common_array['authorizer_access_token'] : '';
        $func_info = isset($this->request_common_array['func_info']) ? $this->request_common_array['func_info'] : '';
        $nick_name = isset($this->request_common_array['nick_name']) ? $this->request_common_array['nick_name'] : '';
        $head_img = isset($this->request_common_array['head_img']) ? $this->request_common_array['head_img'] : '';
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $alias = isset($this->request_common_array['alias']) ? $this->request_common_array['alias'] : '';
        $qrcode_url = isset($this->request_common_array['qrcode_url']) ? $this->request_common_array['qrcode_url'] : '';
        $retval = $this->service->addWeixinAuth($instance_id, $authorizer_appid, $authorizer_refresh_token, $authorizer_access_token, $func_info, $nick_name, $head_img, $user_name, $alias, $qrcode_url);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 用户关注添加粉丝信息
     *
     * @param unknown $instance_id
     * @param unknown $nickname
     * @param unknown $headimgurl
     * @param unknown $sex
     * @param unknown $language
     * @param unknown $country
     * @param unknown $province
     * @param unknown $city
     * @param unknown $district
     * @param unknown $openid
     * @param unknown $groupid
     * @param unknown $is_subscribe
     * @param unknown $memo
    */
    public function addWeixinFans(){
        $source_uid = isset($this->request_common_array['source_uid']) ? $this->request_common_array['source_uid'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $nickname = isset($this->request_common_array['nickname']) ? $this->request_common_array['nickname'] : '';
        $nickname_decode = isset($this->request_common_array['nickname_decode']) ? $this->request_common_array['nickname_decode'] : '';
        $headimgurl = isset($this->request_common_array['headimgurl']) ? $this->request_common_array['headimgurl'] : '';
        $sex = isset($this->request_common_array['sex']) ? $this->request_common_array['sex'] : '';
        $language = isset($this->request_common_array['language']) ? $this->request_common_array['language'] : '';
        $country = isset($this->request_common_array['country']) ? $this->request_common_array['country'] : '';
        $province = isset($this->request_common_array['province']) ? $this->request_common_array['province'] : '';
        $city = isset($this->request_common_array['city']) ? $this->request_common_array['city'] : '';
        $district = isset($this->request_common_array['district']) ? $this->request_common_array['district'] : '';
        $openid = isset($this->request_common_array['openid']) ? $this->request_common_array['openid'] : '';
        $groupid = isset($this->request_common_array['groupid']) ? $this->request_common_array['groupid'] : '';
        $is_subscribe = isset($this->request_common_array['is_subscribe']) ? $this->request_common_array['is_subscribe'] : '';
        $memo = isset($this->request_common_array['memo']) ? $this->request_common_array['memo'] : '';
        $unionid = isset($this->request_common_array['unionid']) ? $this->request_common_array['unionid'] : '';
        $qrcode_url = isset($this->request_common_array['qrcode_url']) ? $this->request_common_array['qrcode_url'] : '';
        $retval = $this->service->addWeixinFans($source_uid, $instance_id, $nickname, $nickname_decode, $headimgurl, $sex, $language, $country, $province, $city, $district, $openid, $groupid, $is_subscribe, $memo, $unionid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加关注回复
     *
     * @param unknown $instance_id
     * @param unknown $replay_media_id
     * @param unknown $sort
    */
    public function addFollowReplay(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $replay_media_id = isset($this->request_common_array['replay_media_id']) ? $this->request_common_array['replay_media_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addFollowReplay($instance_id, $replay_media_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 添加默认回复
     *
     * @param unknown $instance_id
     * @param unknown $replay_media_id
     * @param unknown $sort
    */
    public function addDefaultReplay(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $replay_media_id = isset($this->request_common_array['replay_media_id']) ? $this->request_common_array['replay_media_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addDefaultReplay($instance_id, $replay_media_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改关注回复
     *
     * @param unknown $id
     * @param unknown $instance_id
     * @param unknown $replay_media_id
     * @param unknown $sort
    */
    public function updateFollowReplay(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $replay_media_id = isset($this->request_common_array['replay_media_id']) ? $this->request_common_array['replay_media_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->updateFollowReplay($id, $instance_id, $replay_media_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改默认回复
     *
     * @param unknown $id
     * @param unknown $instance_id
     * @param unknown $replay_media_id
     * @param unknown $sort
    */
    public function updateDefaultReplay(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $replay_media_id = isset($this->request_common_array['replay_media_id']) ? $this->request_common_array['replay_media_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->updateDefaultReplay($id, $instance_id, $replay_media_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加关键字回复
     *
     * @param unknown $instance_id
     * @param unknown $key
     * @param unknown $match_type
     * @param unknown $replay_media_id
     * @param unknown $sort
    */
    public function addKeyReplay(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $key = isset($this->request_common_array['key']) ? $this->request_common_array['key'] : '';
        $match_type = isset($this->request_common_array['match_type']) ? $this->request_common_array['match_type'] : '';
        $replay_media_id = isset($this->request_common_array['replay_media_id']) ? $this->request_common_array['replay_media_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->updateKeyReplay($instance_id, $key, $match_type, $replay_media_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改关键字回复
     *
     * @param unknown $id
     * @param unknown $instance_id
     * @param unknown $key
     * @param unknown $match_type
     * @param unknown $replay_media_id
     * @param unknown $sort
    */
    public function updateKeyReplay(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $key = isset($this->request_common_array['key']) ? $this->request_common_array['key'] : '';
        $match_type = isset($this->request_common_array['match_type']) ? $this->request_common_array['match_type'] : '';
        $replay_media_id = isset($this->request_common_array['replay_media_id']) ? $this->request_common_array['replay_media_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->updateKeyReplay($id, $instance_id, $key, $match_type, $replay_media_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取关键词回复列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getKeyReplayList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getKeyReplayList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取关注时回复列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getFollowReplayList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getFollowReplayList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取默认回复列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getDefaultReplayList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getDefaultReplayList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取 关注时回复信息
     *
     * @param unknown $condition
    */
    public function getFollowReplayDetail(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getFollowReplayDetail($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取 默认回复信息
     *
     * @param unknown $condition
    */
    public function getDefaultReplayDetail(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getDefaultReplayDetail($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取微信粉丝列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getWeixinFansList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getWeixinFansList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取微信授权列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getWeixinAuthList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getWeixinAuthList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询用户实例的授权信息
     *
     * @param unknown $instance_id
    */
    public function getWeixinAuthInfo(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getWeixinAuthInfo($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加图文消息
     *
     * @param unknown $title
     * @param unknown $instance_id
     * @param unknown $type
     * @param unknown $sort
     * @param unknown $content
    */
    public function addWeixinMedia(){
        $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $res = $this->service->addWeixinMedia($title, $instance_id, $type, $sort, $content);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加图文消息内容
     *
     * @param unknown $media_id
     * @param unknown $title
     * @param unknown $author
     * @param unknown $cover
     * @param unknown $show_cover_pic
     * @param unknown $summary
     * @param unknown $content
     * @param unknown $content_source_url
     * @param unknown $sort
    */
    public function addWeixinMediaItem(){
        $media_id = isset($this->request_common_array['media_id']) ? $this->request_common_array['media_id'] : '';
        $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
        $author = isset($this->request_common_array['author']) ? $this->request_common_array['author'] : '';
        $cover = isset($this->request_common_array['cover']) ? $this->request_common_array['cover'] : '';
        $show_cover_pic = isset($this->request_common_array['show_cover_pic']) ? $this->request_common_array['show_cover_pic'] : '';
        $summary = isset($this->request_common_array['summary']) ? $this->request_common_array['summary'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $content_source_url = isset($this->request_common_array['content_source_url']) ? $this->request_common_array['content_source_url'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addWeixinMediaItem($media_id, $title, $author, $cover, $show_cover_pic, $summary, $content, $content_source_url, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改图文消息
     *
     * @param unknown $media_id
     * @param unknown $title
     * @param unknown $instance_id
     * @param unknown $type
     * @param unknown $sort
     * @param unknown $content
    */
    public function updateWeixinMedia(){
        $media_id = isset($this->request_common_array['media_id']) ? $this->request_common_array['media_id'] : '';
        $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $res = $this->service->addWeixinMediaItem($media_id, $title, $instance_id, $type, $sort, $content);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除 图文消息
     *
     * @param unknown $media_id
    */
    public function deleteWeixinMedia(){
        $media_id = isset($this->request_common_array['media_id']) ? $this->request_common_array['media_id'] : '';
        $res = $this->service->deleteWeixinMedia($media_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取微信图文消息列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getWeixinMediaList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getWeixinMediaList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取图文消息详情，包括子
     *
     * @param unknown $media_id
    */
    public function getWeixinMediaDetail(){
        $media_id = isset($this->request_common_array['media_id']) ? $this->request_common_array['media_id'] : '';
        $retval = $this->service->getWeixinMediaDetail($media_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    public function getWeixinMediaDetailByMediaId(){
        $media_id = isset($this->request_common_array['media_id']) ? $this->request_common_array['media_id'] : '';
        $retval = $this->service->getWeixinMediaDetailByMediaId($media_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 通过微信openID查询uid
     *
     * @param unknown $openid
    */
    public function getWeixinUidByOpenid(){
        $openid = isset($this->request_common_array['openid']) ? $this->request_common_array['openid'] : '';
        $retval = $this->service->getWeixinUidByOpenid($openid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 通过author_appid获取shopid
     *
     * @param unknown $author_appid
    */
    public function getShopidByAuthorAppid(){
        $author_appid = isset($this->request_common_array['author_appid']) ? $this->request_common_array['author_appid'] : '';
        $retval = $this->service->getShopidByAuthorAppid($author_appid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 取消关注
     *
     * @param unknown $instance_id
     * @param unknown $openid
    */
    public function WeixinUserUnsubscribe(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $openid = isset($this->request_common_array['openid']) ? $this->request_common_array['openid'] : '';
        $res = $this->service->WeixinUserUnsubscribe($instance_id, $openid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 通过appid获取公众账号信息
     *
     * @param unknown $author_appid
    */
    public function getWeixinInfoByAppid(){
        $author_appid = isset($this->request_common_array['author_appid']) ? $this->request_common_array['author_appid'] : '';
        $retval = $this->service->getWeixinInfoByAppid($author_appid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取实例微信菜单结构
     *
     * @param unknown $instance_id
    */
    public function getInstanceWchatMenu(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getInstanceWchatMenu($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 更新实例自定义菜单到微信
     *
     * @param unknown $instance_id
    */
    public function updateInstanceMenuToWeixin(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $res = $this->service->updateInstanceMenuToWeixin($instance_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取图文消息的微信数据结构
     *
     * @param unknown $media_info
    */
    public function getMediaWchatStruct(){
        $media_info = isset($this->request_common_array['media_info']) ? $this->request_common_array['media_info'] : '';
        $retval = $this->service->getMediaWchatStruct($media_info);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取微信回复的消息内容返回media_id
     *
     * @param unknown $instance_id
     * @param unknown $key_words
    */
    public function getWhatReplay(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $key_words = isset($this->request_common_array['key_words']) ? $this->request_common_array['key_words'] : '';
        $retval = $this->service->getWhatReplay($instance_id, $key_words);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取微信关注回复
     *
     * @param unknown $instance_id
    */
    public function getSubscribeReplay(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getSubscribeReplay($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取微信默认回复
     * @param unknown $instance_id
    */
    public function getDefaultReplay(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getDefaultReplay($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取微信推广二维码配置（注意没有的话添加一条）
     *
     * @param unknown $instance_id
    */
    public function getWeixinQrcodeConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getWeixinQrcodeConfig($instance_id, $uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 微信推广二维码设置修改
     *
     * @param unknown $instance_id
     * @param unknown $background
     * @param unknown $nick_font_color
     * @param unknown $nick_font_size
     * @param unknown $is_logo_show
     * @param unknown $header_left
     * @param unknown $header_top
     * @param unknown $name_left
     * @param unknown $name_top
     * @param unknown $logo_left
     * @param unknown $logo_top
     * @param unknown $code_left
     * @param unknown $code_top
    */
    public function updateWeixinQrcodeConfig(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $background = isset($this->request_common_array['background']) ? $this->request_common_array['background'] : '';
        $nick_font_color = isset($this->request_common_array['nick_font_color']) ? $this->request_common_array['nick_font_color'] : '';
        $nick_font_size = isset($this->request_common_array['nick_font_size']) ? $this->request_common_array['nick_font_size'] : '';
        $is_logo_show = isset($this->request_common_array['is_logo_show']) ? $this->request_common_array['is_logo_show'] : '';
        $header_left = isset($this->request_common_array['header_left']) ? $this->request_common_array['header_left'] : '';
        $header_top = isset($this->request_common_array['header_top']) ? $this->request_common_array['header_top'] : '';
        $name_left = isset($this->request_common_array['name_left']) ? $this->request_common_array['name_left'] : '';
        $name_top = isset($this->request_common_array['name_top']) ? $this->request_common_array['name_top'] : '';
        $logo_left = isset($this->request_common_array['logo_left']) ? $this->request_common_array['logo_left'] : '';
        $logo_top = isset($this->request_common_array['logo_top']) ? $this->request_common_array['logo_top'] : '';
        $code_left = isset($this->request_common_array['code_left']) ? $this->request_common_array['code_left'] : '';
        $code_top = isset($this->request_common_array['code_top']) ? $this->request_common_array['code_top'] : '';
        $res = $this->service->addOrupdateCity($instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除微信自定义菜单
     *
     * @param unknown $menu_id
    */
    public function deleteWeixinMenu(){
        $menu_id = isset($this->request_common_array['menu_id']) ? $this->request_common_array['menu_id'] : '';
        $res = $this->service->deleteWeixinMenu($menu_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除 关注时 回复
     *
     * @param unknown $instance_id
    */
    public function deleteFollowReplay(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $res = $this->service->deleteFollowReplay($instance_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除默认 回复
     *
     * @param unknown $instance_id
    */
    public function deleteDefaultReplay(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $res = $this->service->deleteDefaultReplay($instance_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取 关键字回复 详情
    */
    public function getKeyReplyDetail(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getKeyReplyDetail($id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 删除 关键字 回复
     *
     * @param unknown $id
    */
    public function deleteKeyReplay(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->deleteKeyReplay($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询所有二维码模板
     *
     * @param unknown $shop_id
    */
    public function getWeixinQrcodeTemplate(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getWeixinQrcodeTemplate($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 选择模板
     *
     * @param unknown $shop_id
     * @param unknown $id
    */
    public function modifyWeixinQrcodeTemplateCheck(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->modifyWeixinQrcodeTemplateCheck($shop_id, $id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加店铺的推广二维码模板
     *
     * @param unknown $instance_id
     * @param unknown $background
     * @param unknown $nick_font_color
     * @param unknown $nick_font_size
     * @param unknown $is_logo_show
     * @param unknown $header_left
     * @param unknown $header_top
     * @param unknown $name_left
     * @param unknown $name_top
     * @param unknown $logo_left
     * @param unknown $logo_top
     * @param unknown $code_left
     * @param unknown $code_top
     * @param unknown $template_url
    */
    public function addWeixinQrcodeTemplate(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $background = isset($this->request_common_array['background']) ? $this->request_common_array['background'] : '';
        $nick_font_color = isset($this->request_common_array['nick_font_color']) ? $this->request_common_array['nick_font_color'] : '';
        $nick_font_size = isset($this->request_common_array['nick_font_size']) ? $this->request_common_array['nick_font_size'] : '';
        $is_logo_show = isset($this->request_common_array['is_logo_show']) ? $this->request_common_array['is_logo_show'] : '';
        $header_left = isset($this->request_common_array['header_left']) ? $this->request_common_array['header_left'] : '';
        $header_top = isset($this->request_common_array['header_top']) ? $this->request_common_array['header_top'] : '';
        $name_left = isset($this->request_common_array['name_left']) ? $this->request_common_array['name_left'] : '';
        $name_top = isset($this->request_common_array['name_top']) ? $this->request_common_array['name_top'] : '';
        $logo_left = isset($this->request_common_array['logo_left']) ? $this->request_common_array['logo_left'] : '';
        $logo_top = isset($this->request_common_array['logo_top']) ? $this->request_common_array['logo_top'] : '';
        $code_left = isset($this->request_common_array['code_left']) ? $this->request_common_array['code_left'] : '';
        $code_top = isset($this->request_common_array['code_top']) ? $this->request_common_array['code_top'] : '';
        $template_url = isset($this->request_common_array['template_url']) ? $this->request_common_array['template_url'] : '';
        $res = $this->service->addWeixinQrcodeTemplate($instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $template_url);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 更新店铺的推广二维码模板
     *
     * @param unknown $id
     * @param unknown $instance_id
     * @param unknown $background
     * @param unknown $nick_font_color
     * @param unknown $nick_font_size
     * @param unknown $is_logo_show
     * @param unknown $header_left
     * @param unknown $header_top
     * @param unknown $name_left
     * @param unknown $name_top
     * @param unknown $logo_left
     * @param unknown $logo_top
     * @param unknown $code_left
     * @param unknown $code_top
     * @param unknown $template_url
    */
    public function updateWeixinQrcodeTemplate(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $background = isset($this->request_common_array['background']) ? $this->request_common_array['background'] : '';
        $nick_font_color = isset($this->request_common_array['nick_font_color']) ? $this->request_common_array['nick_font_color'] : '';
        $nick_font_size = isset($this->request_common_array['nick_font_size']) ? $this->request_common_array['nick_font_size'] : '';
        $is_logo_show = isset($this->request_common_array['is_logo_show']) ? $this->request_common_array['is_logo_show'] : '';
        $header_left = isset($this->request_common_array['header_left']) ? $this->request_common_array['header_left'] : '';
        $header_top = isset($this->request_common_array['header_top']) ? $this->request_common_array['header_top'] : '';
        $name_left = isset($this->request_common_array['name_left']) ? $this->request_common_array['name_left'] : '';
        $name_top = isset($this->request_common_array['name_top']) ? $this->request_common_array['name_top'] : '';
        $logo_left = isset($this->request_common_array['logo_left']) ? $this->request_common_array['logo_left'] : '';
        $logo_top = isset($this->request_common_array['logo_top']) ? $this->request_common_array['logo_top'] : '';
        $code_left = isset($this->request_common_array['code_left']) ? $this->request_common_array['code_left'] : '';
        $code_top = isset($this->request_common_array['code_top']) ? $this->request_common_array['code_top'] : '';
        $template_url = isset($this->request_common_array['template_url']) ? $this->request_common_array['template_url'] : '';
        $res = $this->service->updateWeixinQrcodeTemplate($id, $instance_id, $background, $nick_font_color, $nick_font_size, $is_logo_show, $header_left, $header_top, $name_left, $name_top, $logo_left, $logo_top, $code_left, $code_top, $template_url);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除店铺推广二维码模板
     *
     * @param unknown $id
     * @param unknown $instance_id
    */
    public function deleteWeixinQrcodeTemplate(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $res = $this->service->deleteWeixinQrcodeTemplate($id, $instance_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询单个的模板信息
     *
     * @param unknown $id
     * @param unknown $instance_id
    */
    public function getDetailWeixinQrcodeTemplate(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getDetailWeixinQrcodeTemplate($id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 更新用户的推广二维码模板
     *
     * @param unknown $shop_id
     * @param unknown $uid
    */
    public function updateMemberQrcodeTemplate(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->updateMemberQrcodeTemplate($shop_id, $uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取微信一键关注设置
     *
     * @param unknown $instance_id
    */
    public function getInstanceOneKeySubscribe(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getInstanceOneKeySubscribe($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 设置一键关注
     *
     * @param unknown $instance_id
     * @param unknown $url
    */
    public function setInsanceOneKeySubscribe(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
        $res = $this->service->setInsanceOneKeySubscribe($instance_id, $url);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 通过微信获取用户对应实例openid
     *
     * @param unknown $instance_id
    */
    public function getUserOpenid(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getUserOpenid($instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取粉丝个数
     * @param unknown $condition
    */
    public function getWeixinFansCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getWeixinFansCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取会员微信关注信息
     * @param unknown $uid
     * @param unknown $instance_id
    */
    public function getUserWeixinSubscribeData(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $retval = $this->service->getUserWeixinSubscribeData($uid, $instance_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加 用户消息记录
     * @param unknown $openid
     * @param unknown $content
     * @param unknown $msg_type
    */
    public function addUserMessage(){
        $openid = isset($this->request_common_array['openid']) ? $this->request_common_array['openid'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $msg_type = isset($this->request_common_array['msg_type']) ? $this->request_common_array['msg_type'] : '';
        $res = $this->service->addUserMessage($openid, $content, $msg_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加 用户消息 回复 记录
     * @param unknown $msg_id
     * @param unknown $replay_uid
     * @param unknown $replay_type
     * @param unknown $content
    */
    public function addUserMessageReplay(){
        $msg_id = isset($this->request_common_array['msg_id']) ? $this->request_common_array['msg_id'] : '';
        $replay_uid = isset($this->request_common_array['replay_uid']) ? $this->request_common_array['replay_uid'] : '';
        $replay_type = isset($this->request_common_array['replay_type']) ? $this->request_common_array['replay_type'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $res = $this->service->addOrupdateCity($msg_id, $replay_uid, $replay_type, $content);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}