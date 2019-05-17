<?php
/**
 * Member.php
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
use data\service\Member as MemberService;


class Member extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new MemberService();
    }
    
    /**
     * 前台会员添加
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
    public function registerMember(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $email = isset($this->request_common_array['email']) ? $this->request_common_array['email'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $user_qq_id = isset($this->request_common_array['user_qq_id']) ? $this->request_common_array['user_qq_id'] : '';
        $qq_info = isset($this->request_common_array['qq_info']) ? $this->request_common_array['qq_info'] : '';
        $wx_openid = isset($this->request_common_array['wx_openid']) ? $this->request_common_array['wx_openid'] : '';
        $wx_info = isset($this->request_common_array['wx_info']) ? $this->request_common_array['wx_info'] : '';
        $wx_unionid = isset($this->request_common_array['wx_unionid']) ? $this->request_common_array['wx_unionid'] : '';
        $res = $this->service->registerMember($user_name, $password, $email, $mobile, $user_qq_id, $qq_info, $wx_openid, $wx_info, $wx_unionid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除会员
     *
     * @param unknown $uid(会员ID)
    */
    public function deleteMember(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $res = $this->service->deleteMember($uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 会员列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
    */
    public function getMemberList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getMemberList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员默认地址
    */
    public function getDefaultExpressAddress(){
        $retval = $this->service->getDefaultExpressAddress();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员基础信息
    */
    public function getMemberInfo(){
        $retval = $this->service->getMemberInfo();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员详情
     * $shop_id不传就为全部
    */
    public function getMemberDetail(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getMemberDetail($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 会员地址管理列表
    */
    public function getMemberExpressAddressList(){
        $retval = $this->service->getMemberExpressAddressList();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改会员地址
     *
     * @param unknown $id
     * @param unknown $consigner
     * @param unknown $mobile
     * @param unknown $phone
     * @param unknown $province
     * @param unknown $city
     * @param unknown $district
     * @param unknown $address
     * @param unknown $zip_code
     * @param unknown $alias
    */
    public function updateMemberExpressAddress(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $consigner = isset($this->request_common_array['consigner']) ? $this->request_common_array['consigner'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $phone = isset($this->request_common_array['phone']) ? $this->request_common_array['phone'] : '';
        $province = isset($this->request_common_array['province']) ? $this->request_common_array['province'] : '';
        $city = isset($this->request_common_array['city']) ? $this->request_common_array['city'] : '';
        $district = isset($this->request_common_array['district']) ? $this->request_common_array['district'] : '';
        $address = isset($this->request_common_array['address']) ? $this->request_common_array['address'] : '';
        $zip_code = isset($this->request_common_array['zip_code']) ? $this->request_common_array['zip_code'] : '';
        $alias = isset($this->request_common_array['alias']) ? $this->request_common_array['alias'] : '';
        $res = $this->service->addMemberExpressAddress($id, $consigner, $mobile, $phone, $province, $city, $district, $address, $zip_code, $alias);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加会员物流地址
     *
     * @param unknown $consigner
     * @param unknown $mobile
     * @param unknown $phone
     * @param unknown $province
     * @param unknown $city
     * @param unknown $district
     * @param unknown $address
     * @param unknown $zip_code
     * @param unknown $alias
    */
    public function addMemberExpressAddress(){
        $consigner = isset($this->request_common_array['consigner']) ? $this->request_common_array['consigner'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $phone = isset($this->request_common_array['phone']) ? $this->request_common_array['phone'] : '';
        $province = isset($this->request_common_array['province']) ? $this->request_common_array['province'] : '';
        $city = isset($this->request_common_array['city']) ? $this->request_common_array['city'] : '';
        $district = isset($this->request_common_array['district']) ? $this->request_common_array['district'] : '';
        $address = isset($this->request_common_array['address']) ? $this->request_common_array['address'] : '';
        $zip_code = isset($this->request_common_array['zip_code']) ? $this->request_common_array['zip_code'] : '';
        $alias = isset($this->request_common_array['alias']) ? $this->request_common_array['alias'] : '';
        $res = $this->service->addMemberExpressAddress($consigner, $mobile, $phone, $province, $city, $district, $address, $zip_code, $alias);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取会员物流地址详情
     *
     * @param unknown $id
     *            地址ID
    */
    public function getMemberExpressAddressDetail(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getMemberExpressAddressDetail($id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 删除会员物流地址
     *
     * @param unknown $id
     *            地址ID
    */
    public function memberAddressDelete(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->memberAddressDelete($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改地址为默认地址
     *
     * @param unknown $id
    */
    public function updateAddressDefault(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->updateAddressDefault($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改个人信息
     *
     * @param unknown $user_name
     * @param unknown $user_tel
     * @param unknown $user_qq
     * @param unknown $user_email
     * @param unknown $real_name
     * @param unknown $sex
     * @param unknown $birthday
     * @param unknown $location
     * @param unknown $user_headimg
    */
    public function updateMemberInformation(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $user_qq = isset($this->request_common_array['user_qq']) ? $this->request_common_array['user_qq'] : '';
        $real_name = isset($this->request_common_array['real_name']) ? $this->request_common_array['real_name'] : '';
        $sex = isset($this->request_common_array['sex']) ? $this->request_common_array['sex'] : '';
        $birthday = isset($this->request_common_array['birthday']) ? $this->request_common_array['birthday'] : '';
        $location = isset($this->request_common_array['location']) ? $this->request_common_array['location'] : '';
        $user_headimg = isset($this->request_common_array['user_headimg']) ? $this->request_common_array['user_headimg'] : '';
        $res = $this->service->updateMemberInformation($user_name, $user_qq, $real_name, $sex, $birthday, $location, $user_headimg);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 分页获取用户积分和余额
     *
     * @param unknown $uid
     *            //用户ID
     * @param unknown $page_index
     *            //分页列
     * @param unknown $page_size
     *            //分页数量
    */
    public function getShopAccountListByUser(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $retval = $this->service->getShopAccountListByUser($uid, $page_index, $page_size);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员积分记录
     *
     * @param unknown $start_time
     *            //开始时间
     * @param unknown $end_time
     *            //结束时间
    */
    public function getMemberPointList(){
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $retval = $this->service->getMemberPointList($start_time, $end_time);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 分页获取会员积分记录
     *
     * @param unknown $start_time
     *            //开始时间
     * @param unknown $end_time
     *            //结束时间
     * @param unknown $page_index
     *            //分页列
     * @param unknown $page_size
     *            //分页数量
     * @param unknown $shop_id
     *            //店铺ID
    */
    public function getPageMemberPointList(){
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getPageMemberPointList($start_time, $end_time, $page_index, $page_size, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员余额
     *
     * @param unknown $start_time
     *            //开始时间
     * @param unknown $end_time
     *            //结束时间
    */
    public function getMemberBalanceList(){
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $retval = $this->service->getMemberBalanceList($start_time, $end_time);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 分页获取会员余额记录
     *
     * @param unknown $start_time
     *            //开始时间
     * @param unknown $end_time
     *            //结束时间
     * @param unknown $page_index
     *            //分页列
     * @param unknown $page_size
     *            //分页数量
     * @param unknown $shop_id
     *            //店铺ID
    */
    public function getPageMemberBalanceList(){
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getPageMemberBalanceList($start_time, $end_time, $page_index, $page_size, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 通过订单ID获取订单号
     *
     * @param unknown $order_id
     *            //订单ID
    */
    public function getOrderNumber(){
        $order_id = isset($this->request_common_array['order_id']) ? $this->request_common_array['order_id'] : '';
        $retval = $this->service->getOrderNumber($order_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员优惠券
     *
     * @param unknown $type
     *            1已领用未使用 2.已使用 3.已过期
    */
    public function getMemberCounponList(){
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $retval = $this->service->getMemberCounponList($type);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 通过商铺号来获取商铺名
     *
     * @param unknown $shop_id
     *            商铺ID
    */
    public function getShopNameByShopId(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopNameByShopId($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 会员锁定
     *
     * {@inheritdoc}
     *
     * @see \data\api\system\IUser::userLock()
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
     * 会员解锁
     *
     * @param unknown $uid
    */
    public function userUnlock($uid){
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
     * 获取会员商品收藏
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getMemberGoodsFavoritesList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getMemberGoodsFavoritesList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员店铺收藏
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getMemberShopsFavoritesList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getMemberShopsFavoritesList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加收藏
     *
     * @param unknown $fav_id
     *            对应店铺或者会员ID
     * @param unknown $fav_type
     *            收藏方式 goods shop
     * @param unknown $log_msg
     *            收藏备注
    */
    public function addMemberFavouites(){
        $fav_id = isset($this->request_common_array['fav_id']) ? $this->request_common_array['fav_id'] : '';
        $fav_type = isset($this->request_common_array['fav_type']) ? $this->request_common_array['fav_type'] : '';
        $log_msg = isset($this->request_common_array['log_msg']) ? $this->request_common_array['log_msg'] : '';
        $res = $this->service->addMemberFavouites($fav_id, $fav_type, $log_msg);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 取消收藏
     *
     * @param unknown $fav_id
     *            对应店铺或者会员ID
     * @param unknown $fav_type
     *            收藏方式 goods shop
    */
    public function deleteMemberFavorites(){
        $fav_id = isset($this->request_common_array['fav_id']) ? $this->request_common_array['fav_id'] : '';
        $fav_type = isset($this->request_common_array['fav_type']) ? $this->request_common_array['fav_type'] : '';
        $res = $this->service->deleteMemberFavorites($fav_id, $fav_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 判断会员 是否已经收藏（商品，店铺） 返回 1 or 0
     *
     * @param unknown $uid
     * @param unknown $fav_id
     * @param unknown $fav_type
    */
    public function getIsMemberFavorites(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $fav_id = isset($this->request_common_array['fav_id']) ? $this->request_common_array['fav_id'] : '';
        $fav_type = isset($this->request_common_array['fav_type']) ? $this->request_common_array['fav_type'] : '';
        $retval = $this->service->getIsMemberFavorites($uid, $fav_id, $fav_type);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取浏览历史
    */
    public function getMemberViewHistory(){
        $retval = $this->service->getMemberViewHistory();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员浏览历史
     *
     * @param unknown $uid
     * @param unknown $start_time
     * @param unknown $end_time
    */
    public function getMemberAllViewHistory(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $retval = $this->service->getMemberAllViewHistory($uid, $start_time, $end_time);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加浏览历史
     *
     * @param unknown $goods_id
     * @param unknown $goods_name
     * @param unknown $goods_category_id
    */
    public function addMemberViewHistory(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $res = $this->service->addMemberViewHistory($goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除浏览历史
    */
    public function deleteMemberViewHistory(){
        $res = $this->service->deleteMemberViewHistory();
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取用户猜你喜欢
    */
    public function getGuessMemberLikes(){
        $retval = $this->service->getGuessMemberLikes();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员申请店铺情况
     *
     * @param unknown $uid
    */
    public function getMemberIsApplyShop(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getMemberIsApplyShop($uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店铺账户
     *
     * @param unknown $uid
     * @param unknown $shop_id
    */
    public function getMemberAccount(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getMemberAccount($uid, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 会员积分转余额
     *
     * @param unknown $uid
     * @param unknown $shop_id
     * @param unknown $point
    */
    public function memberPointToBalance(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $point = isset($this->request_common_array['point']) ? $this->request_common_array['point'] : '';
        $res = $this->service->memberPointToBalance($uid, $shop_id, $point);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 会员对应店积分总额默认为平台的
     *
     * @param unknown $shop_id
    */
    public function memberShopPointCount(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : 0;
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : 0;
        $res = $this->service->memberShopPointCount($uid, $shop_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 会员对应店余额默认为平台的
     *
     * @param unknown $shop_id
    */
    public function memberShopBalanceCount(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : 0;
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : 0;
        $res = $this->service->memberShopBalanceCount($uid, $shop_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取所有会员
     *
     * @param unknown $condition
    */
    public function getMemberAll(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getMemberAll($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员头像
     *
     * @param unknown $uid
    */
    public function getMemberImage(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getMemberImage($uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员总数
    */
    public function getMemberCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getMemberCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查看某月的会员注册情况
     *
     * @param unknown $begin_date
     * @param unknown $end_date
    */
    public function getMemberMonthCount(){
        $begin_date = isset($this->request_common_array['begin_date']) ? $this->request_common_array['begin_date'] : '';
        $end_date = isset($this->request_common_array['end_date']) ? $this->request_common_array['end_date'] : '';
        $retval = $this->service->getMemberMonthCount($begin_date, $end_date);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 会员购物币流水
     *
     * @param unknown $start_time
     * @param unknown $end_time
    */
    public function getMemberCoinList(){
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $retval = $this->service->getMemberCoinList($start_time, $end_time);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 会员购物币分页流水
     *
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $shop_id
    */
    public function getPageMemberCoinList(){
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getPageMemberCoinList($start_time, $end_time, $page_index, $page_size, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 充值会员账户（针对会员账户充值）1.
     * 积分2. 余额 3. 购物币
     *
     * @param unknown $shop_id
     * @param unknown $type
     * @param unknown $num
     * @param unknown $text
    */
    public function addMemberAccount(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $num = isset($this->request_common_array['num']) ? $this->request_common_array['num'] : '';
        $text = isset($this->request_common_array['text']) ? $this->request_common_array['text'] : '';
        $res = $this->service->addMemberAccount($shop_id, $uid, $type, $num, $text);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 判断会员是否已经签到
     * 返回 1 or 0
    */
    public function getIsMemberSign(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getIsMemberSign($uid, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员签到记录
     *
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $shop_id
    */
    public function getPageMemberSignList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getPageMemberSignList($page_index, $page_size, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 判断 会员今天 是否已经分享过
     * 返回 1 or 0
    */
    public function getIsMemberShare(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getIsMemberShare($uid, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取网站信息
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
     * 获取 会员等级列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
    */
    public function getMemberLevelList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getMemberLevelList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加 会员等级
     *
     * @param unknown $level_name
     * @param unknown $min_integral
     * @param unknown $goods_discount
     * @param unknown $desc
    */
    public function addMemberLevel(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $level_name = isset($this->request_common_array['level_name']) ? $this->request_common_array['level_name'] : '';
        $min_integral = isset($this->request_common_array['min_integral']) ? $this->request_common_array['min_integral'] : '';
        $quota = isset($this->request_common_array['quota']) ? $this->request_common_array['quota'] : '';
        $upgrade = isset($this->request_common_array['upgrade']) ? $this->request_common_array['upgrade'] : '';
        $goods_discount = isset($this->request_common_array['goods_discount']) ? $this->request_common_array['goods_discount'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $relation = isset($this->request_common_array['relation']) ? $this->request_common_array['relation'] : '';
        $res = $this->service->addMemberLevel($shop_id, $level_name, $min_integral, $quota, $upgrade, $goods_discount, $desc, $relation);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改会员等级
     *
     * @param unknown $level_id
     * @param unknown $level_name
     * @param unknown $min_integral
     * @param unknown $goods_discount
     * @param unknown $desc
    */
    public function updateMemberLevel(){
        $level_id = isset($this->request_common_array['level_id']) ? $this->request_common_array['level_id'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $level_name = isset($this->request_common_array['level_name']) ? $this->request_common_array['level_name'] : '';
        $min_integral = isset($this->request_common_array['min_integral']) ? $this->request_common_array['min_integral'] : '';
        $quota = isset($this->request_common_array['quota']) ? $this->request_common_array['quota'] : '';
        $upgrade = isset($this->request_common_array['upgrade']) ? $this->request_common_array['upgrade'] : '';
        $goods_discount = isset($this->request_common_array['goods_discount']) ? $this->request_common_array['goods_discount'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $relation = isset($this->request_common_array['relation']) ? $this->request_common_array['relation'] : '';
        $res = $this->service->updateMemberLevel($level_id, $shop_id, $level_name, $min_integral, $quota, $upgrade, $goods_discount, $desc, $relation);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除会员等级
     *
     * @param unknown $level_id
    */
    public function deleteMemberLevel(){
        $level_id = isset($this->request_common_array['level_id']) ? $this->request_common_array['level_id'] : '';
        $res = $this->service->deleteMemberLevel($level_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取 会员等级详情
     *
     * @param unknown $level_id
    */
    public function getMemberLevelDetail(){
        $level_id = isset($this->request_common_array['level_id']) ? $this->request_common_array['level_id'] : '';
        $retval = $this->service->getMemberLevelDetail($level_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改 会员等级 单个字段
     *
     * @param unknown $level_id
     * @param unknown $field_name
     * @param unknown $field_value
    */
    public function modifyMemberLevelField(){
        $level_id = isset($this->request_common_array['level_id']) ? $this->request_common_array['level_id'] : '';
        $field_name = isset($this->request_common_array['field_name']) ? $this->request_common_array['field_name'] : '';
        $field_value = isset($this->request_common_array['field_value']) ? $this->request_common_array['field_value'] : '';
        $res = $this->service->modifyMemberLevelField($level_id, $field_name, $field_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加前台会员（后台添加）
     *
     * @param unknown $user_name
     * @param unknown $password
     * @param unknown $email
     * @param unknown $mobile
     * @param unknown $member_level
    */
    public function addMember(){
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $password = isset($this->request_common_array['password']) ? $this->request_common_array['password'] : '';
        $email = isset($this->request_common_array['email']) ? $this->request_common_array['email'] : '';
        $sex = isset($this->request_common_array['sex']) ? $this->request_common_array['sex'] : '';
        $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $member_level = isset($this->request_common_array['member_level']) ? $this->request_common_array['member_level'] : '';
        $res = $this->service->addMember($user_name, $password, $email, $sex, $status, $mobile, $member_level);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 创建充值余额
     *
     * @param unknown $recharge_money
     * @param unknown $uid
     * @param unknown $out_trade_no
    */
    public function createMemberRecharge(){
        $recharge_money = isset($this->request_common_array['recharge_money']) ? $this->request_common_array['recharge_money'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $res = $this->service->addMember($recharge_money, $uid, $out_trade_no);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 支付充值余额
     *
     * @param unknown $out_trade_no
    */
    public function payMemberRecharge(){
        $out_trade_no = isset($this->request_common_array['out_trade_no']) ? $this->request_common_array['out_trade_no'] : '';
        $res = $this->service->payMemberRecharge($out_trade_no);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 会员提现账号列表
    */
    public function getMemberBankAccount(){
        $is_default = isset($this->request_common_array['is_default']) ? $this->request_common_array['is_default'] : '';
        $retval = $this->service->getMemberBankAccount($is_default);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加会员提现账号
    */
    public function addMemberBankAccount(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $bank_type = isset($this->request_common_array['bank_type']) ? $this->request_common_array['bank_type'] : '';
        $branch_bank_name = isset($this->request_common_array['branch_bank_name']) ? $this->request_common_array['branch_bank_name'] : '';
        $realname = isset($this->request_common_array['realname']) ? $this->request_common_array['realname'] : '';
        $account_number = isset($this->request_common_array['account_number']) ? $this->request_common_array['account_number'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $res = $this->service->addMemberBankAccount($uid, $bank_type, $branch_bank_name, $realname, $account_number, $mobile);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改会员提现账号
    */
    public function updateMemberBankAccount(){
        $account_id = isset($this->request_common_array['account_id']) ? $this->request_common_array['account_id'] : '';
        $branch_bank_name = isset($this->request_common_array['branch_bank_name']) ? $this->request_common_array['branch_bank_name'] : '';
        $realname = isset($this->request_common_array['realname']) ? $this->request_common_array['realname'] : '';
        $account_number = isset($this->request_common_array['account_number']) ? $this->request_common_array['account_number'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $res = $this->service->updateMemberBankAccount($account_id, $branch_bank_name, $realname, $account_number, $mobile);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除会员提现账号
     *
     * @param unknown $id
    */
    public function delMemberBankAccount(){
        $account_id = isset($this->request_common_array['account_id']) ? $this->request_common_array['account_id'] : '';
        $res = $this->service->delMemberBankAccount($account_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 设定会员默认账户
     *
     * @param unknown $uid
     * @param unknown $account_id
    */
    public function setMemberBankAccountDefault(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $account_id = isset($this->request_common_array['account_id']) ? $this->request_common_array['account_id'] : '';
        $res = $this->service->setMemberBankAccountDefault($uid, $account_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取提现账号详情信息
     *
     * @param unknown $id
    */
    public function getMemberBankAccountDetail(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getMemberBankAccountDetail($id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取提现记录
     *
     * @param unknown $uid
     * @param unknown $shop_id
    */
    public function getMemberBalanceWithdraw(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getMemberBalanceWithdraw($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员提现审核数量
     * @param unknown $condition
    */
    public function getMemberBalanceWithdrawCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getMemberBalanceWithdrawCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 申请提现
     *
     * @param unknown $shop_id
     * @param unknown $withdraw_no
     * @param unknown $distributor_uid
     * @param unknown $bank_account_id
     * @param unknown $cash
    */
    public function addMemberBalanceWithdraw(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $withdraw_no = isset($this->request_common_array['withdraw_no']) ? $this->request_common_array['withdraw_no'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $bank_account_id = isset($this->request_common_array['bank_account_id']) ? $this->request_common_array['bank_account_id'] : '';
        $cash = isset($this->request_common_array['cash']) ? $this->request_common_array['cash'] : '';
        $res = $this->service->addMemberBalanceWithdraw($shop_id, $withdraw_no, $uid, $bank_account_id, $cash);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 用户提现审核
     *
     * @param unknown $shop_id
     * @param unknown $id
     * @param unknown $status
    */
    public function MemberBalanceWithdrawAudit(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
        $res = $this->service->MemberBalanceWithdrawAudit($shop_id, $id, $status);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 拒绝用户提现申请
     *
     * @param unknown $shop_id
     * @param unknown $id
     * @param unknown $status
     * @param unknown $remark
    */
    public function userCommissionWithdrawRefuse(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
        $remark = isset($this->request_common_array['remark']) ? $this->request_common_array['remark'] : '';
        $res = $this->service->MemberBalanceWithdrawAudit($shop_id, $id, $status, $remark);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取用户提现详情
     *
     * @param unknown $id
    */
    public function getMemberWithdrawalsDetails(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getMemberWithdrawalsDetails($id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员下面的优惠券列表
     *
     * @param unknown $uid
    */
    public function getMemberCouponTypeList(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getMemberCouponTypeList($shop_id, $uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员提现记录
     *
     * @param unknown $uid
    */
    public function getMemberExtractionBalanceList(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getMemberExtractionBalanceList($uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 后台修改会员相关信息
     *
     * @param unknown $uid
     * @param unknown $user_name
     * @param unknown $email
     * @param unknown $mobile
     * @param unknown $nick_name
     * @param unknown $member_level
    */
    public function updateMemberByAdmin(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $user_name = isset($this->request_common_array['user_name']) ? $this->request_common_array['user_name'] : '';
        $email = isset($this->request_common_array['email']) ? $this->request_common_array['email'] : '';
        $sex = isset($this->request_common_array['sex']) ? $this->request_common_array['sex'] : '';
        $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $nick_name = isset($this->request_common_array['nick_name']) ? $this->request_common_array['nick_name'] : '';
        $member_level = isset($this->request_common_array['member_level']) ? $this->request_common_array['member_level'] : '';
        $res = $this->service->updateMemberByAdmin($uid, $user_name, $email, $sex, $status, $mobile, $nick_name, $member_level);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 设置用户支付密码
    */
    public function setUserPaymentPassword(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $payment_password = isset($this->request_common_array['payment_password']) ? $this->request_common_array['payment_password'] : '';
        $res = $this->service->setUserPaymentPassword($uid, $payment_password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改用户支付密码
    */
    public function updateUserPaymentPassword(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $old_payment_password = isset($this->request_common_array['old_payment_password']) ? $this->request_common_array['old_payment_password'] : '';
        $new_payment_password = isset($this->request_common_array['new_payment_password']) ? $this->request_common_array['new_payment_password'] : '';
        $res = $this->service->updateUserPaymentPassword($uid, $old_payment_password, $new_payment_password);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 通过用户的id 更新用户的昵称
     *
     * @param unknown $uid
     * @param unknown $nickName
    */
    public function updateNickNameByUid(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $nick_name = isset($this->request_common_array['nick_name']) ? $this->request_common_array['nick_name'] : '';
        $res = $this->service->updateNickNameByUid($uid, $nick_name);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 添加意见反馈
    */
    public function addOpinion(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $opinion = isset($this->request_common_array['opinion']) ? $this->request_common_array['opinion'] : '';
        $reply = isset($this->request_common_array['reply']) ? $this->request_common_array['reply'] : '';
        $res = $this->service->addOpinion($uid, $opinion, $reply);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 意见反馈列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getFeedbackList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getFeedbackList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     *  删除意见
     * @param unknown $id
    */
    public function deleteFeedback(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->deleteFeedback($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 回复
     * @param unknown $id
     * @param unknown $reply
    */
    public function replyFeedback(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $reply = isset($this->request_common_array['reply']) ? $this->request_common_array['reply'] : '';
        $res = $this->service->replyFeedback($id, $reply);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}