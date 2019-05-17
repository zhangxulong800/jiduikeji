<?php
/**
 * Promotion.php
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
use data\service\Promotion as PromotionService;


class Promotion extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new PromotionService();
    }
    
    /**
     * 获取优惠券类型列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    public function getCouponTypeList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : 'create_time desc';
        $retval = $this->service->getCouponTypeList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取优惠券类型详情
     * @param unknown $coupon_type_id  类型主键
    */
    public function getCouponTypeDetail(){
        $coupon_type_id = isset($this->request_common_array['coupon_type_id']) ? $this->request_common_array['coupon_type_id'] : '';
        $retval = $this->service->getCouponTypeDetail($coupon_type_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加优惠券类型
     * @param unknown $coupon_name
     * @param unknown $money
     * @param unknown $count
     * @param unknown $max_fetch
     * @param unknown $at_least
     * @param unknown $need_user_level
     * @param unknown $range_type
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $goods_list
    */
    public function addCouponType(){
        $coupon_name = isset($this->request_common_array['coupon_name']) ? $this->request_common_array['coupon_name'] : '';
        $money = isset($this->request_common_array['money']) ? $this->request_common_array['money'] : '';
        $count = isset($this->request_common_array['count']) ? $this->request_common_array['count'] : '';
        $max_fetch = isset($this->request_common_array['max_fetch']) ? $this->request_common_array['max_fetch'] : '';
        $at_least = isset($this->request_common_array['at_least']) ? $this->request_common_array['at_least'] : '';
        $need_user_level = isset($this->request_common_array['need_user_level']) ? $this->request_common_array['need_user_level'] : '';
        $range_type = isset($this->request_common_array['range_type']) ? $this->request_common_array['range_type'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $goods_list = isset($this->request_common_array['goods_list']) ? $this->request_common_array['goods_list'] : '';
        $is_show = isset($this->request_common_array['is_show']) ? $this->request_common_array['is_show'] : '';
        $res = $this->service->addCouponType($coupon_name, $money, $count, $max_fetch, $at_least, $need_user_level, $range_type, $start_time, $end_time, $goods_list, $is_show);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改优惠券类型
     * @param unknown $data
    */
    public function updateCouponType(){
        $coupon_id = isset($this->request_common_array['coupon_id']) ? $this->request_common_array['coupon_id'] : '';
        $coupon_name = isset($this->request_common_array['coupon_name']) ? $this->request_common_array['coupon_name'] : '';
        $money = isset($this->request_common_array['money']) ? $this->request_common_array['money'] : '';
        $count = isset($this->request_common_array['count']) ? $this->request_common_array['count'] : '';
        $repair_count = isset($this->request_common_array['repair_count']) ? $this->request_common_array['repair_count'] : '';
        $max_fetch = isset($this->request_common_array['max_fetch']) ? $this->request_common_array['max_fetch'] : '';
        $at_least = isset($this->request_common_array['at_least']) ? $this->request_common_array['at_least'] : '';
        $need_user_level = isset($this->request_common_array['need_user_level']) ? $this->request_common_array['need_user_level'] : '';
        $range_type = isset($this->request_common_array['range_type']) ? $this->request_common_array['range_type'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $goods_list = isset($this->request_common_array['goods_list']) ? $this->request_common_array['goods_list'] : '';
        $is_show = isset($this->request_common_array['is_show']) ? $this->request_common_array['is_show'] : '';
        $res = $this->service->updateCouponType($coupon_id, $coupon_name, $money, $count, $repair_count, $max_fetch, $at_least, $need_user_level, $range_type, $start_time, $end_time, $goods_list,$is_show);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取店铺积分配置信息
    */
    public function getPointConfig(){
        $retval = $this->service->getPointConfig();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 店铺积分设置
     * @param unknown $is_open
     * @param unknown $convert_rate
     * @param unknown $desc
    */
    public function setPointConfig(){
        $convert_rate = isset($this->request_common_array['convert_rate']) ? $this->request_common_array['convert_rate'] : '';
        $is_open = isset($this->request_common_array['is_open']) ? $this->request_common_array['is_open'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $res = $this->service->setPointConfig($convert_rate, $is_open, $desc);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取优惠券类型的优惠券列表
     * @param unknown $coupon_type_id
     * @param unknown $get_type  获取类型  0 标示全部
     * @param unknown $use_type  获取类型  0 标示全部
    */
    public function getTypeCouponList(){
        $coupon_type_id = isset($this->request_common_array['coupon_type_id']) ? $this->request_common_array['coupon_type_id'] : '';
        $get_type = isset($this->request_common_array['get_type']) ? $this->request_common_array['get_type'] : 0;
        $use_type = isset($this->request_common_array['use_type']) ? $this->request_common_array['use_type'] : 0;
        $retval = $this->service->getTypeCouponList($coupon_type_id,$get_type=0,$use_type=0);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取优惠券详情
     * @param unknown $coupon_id
    */
    public function getCouponDetail(){
        $coupon_id = isset($this->request_common_array['coupon_id']) ? $this->request_common_array['coupon_id'] : '';
        $retval = $this->service->getCouponDetail($coupon_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 赠品活动列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getPromotionGiftList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : 'create_time desc';
        $retval = $this->service->getPromotionGiftList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加赠品活动
     * @param unknown $gift_name
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $days
     * @param unknown $max_num
     * @param unknown $goods_id_array
    */
    public function addPromotionGift(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $gift_name = isset($this->request_common_array['gift_name']) ? $this->request_common_array['gift_name'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $days = isset($this->request_common_array['days']) ? $this->request_common_array['days'] : '';
        $max_num = isset($this->request_common_array['max_num']) ? $this->request_common_array['max_num'] : '';
        $goods_id_array = isset($this->request_common_array['goods_id_array']) ? $this->request_common_array['goods_id_array'] : '';
        $res = $this->service->addPromotionGift($shop_id, $gift_name, $start_time, $end_time, $days, $max_num, $goods_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改赠品活动
     * @param unknown $gift_id
     * @param unknown $shop_id
     * @param unknown $gift_name
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $days
     * @param unknown $max_num
     * @param unknown $goods_id_array
    */
    public function updatePromotionGift(){
        $gift_id = isset($this->request_common_array['gift_id']) ? $this->request_common_array['gift_id'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $gift_name = isset($this->request_common_array['gift_name']) ? $this->request_common_array['gift_name'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $days = isset($this->request_common_array['days']) ? $this->request_common_array['days'] : '';
        $max_num = isset($this->request_common_array['max_num']) ? $this->request_common_array['max_num'] : '';
        $goods_id_array = isset($this->request_common_array['goods_id_array']) ? $this->request_common_array['goods_id_array'] : '';
        $res = $this->service->addPromotionGift($gift_id, $shop_id, $gift_name, $start_time, $end_time, $days, $max_num, $goods_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取赠品详情
     * @param unknown $gift_id
    */
    public function getPromotionGiftDetail(){
        $gift_id = isset($this->request_common_array['gift_id']) ? $this->request_common_array['gift_id'] : '';
        $retval = $this->service->getPromotionGiftDetail($gift_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取满减送列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getPromotionMansongList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : 'create_time desc';
        $retval = $this->service->getPromotionMansongList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加满减送活动
     * @param unknown $mansong_name
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $shop_id
     * @param unknown $remark
     * @param unknown $type
     * @param unknown $range_type
     * @param unknown $rule   price,discount,fee_shipping,give_point,give_coupon,gift_id;price,discount,fee_shipping,give_point,give_coupon,gift_id
     * @param unknown $goods_id_array
    */
    public function addPromotionMansong(){
        $mansong_name = isset($this->request_common_array['mansong_name']) ? $this->request_common_array['mansong_name'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $remark = isset($this->request_common_array['remark']) ? $this->request_common_array['remark'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $range_type = isset($this->request_common_array['range_type']) ? $this->request_common_array['range_type'] : '';
        $rule = isset($this->request_common_array['rule']) ? $this->request_common_array['rule'] : '';
        $goods_id_array = isset($this->request_common_array['goods_id_array']) ? $this->request_common_array['goods_id_array'] : '';
        $res = $this->service->addPromotionMansong($mansong_name, $start_time, $end_time, $shop_id, $remark, $type, $range_type,$rule, $goods_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改满减送活动
     * @param unknown $mansong_id
     * @param unknown $mansong_name
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $shop_id
     * @param unknown $remark
     * @param unknown $type
     * @param unknown $range_type
     * @param unknown $rule   price,discount,fee_shipping,give_point,give_coupon,gift_id;price,discount,fee_shipping,give_point,give_coupon,gift_id
     * @param unknown $goods_id_array
    */
    public function updatePromotionMansong(){
        $mansong_id = isset($this->request_common_array['mansong_id']) ? $this->request_common_array['mansong_id'] : '';
        $mansong_name = isset($this->request_common_array['mansong_name']) ? $this->request_common_array['mansong_name'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $remark = isset($this->request_common_array['remark']) ? $this->request_common_array['remark'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $range_type = isset($this->request_common_array['range_type']) ? $this->request_common_array['range_type'] : '';
        $rule = isset($this->request_common_array['rule']) ? $this->request_common_array['rule'] : '';
        $goods_id_array = isset($this->request_common_array['goods_id_array']) ? $this->request_common_array['goods_id_array'] : '';
        $res = $this->service->updatePromotionMansong($mansong_id, $mansong_name, $start_time, $end_time, $shop_id, $remark, $type, $range_type,$rule, $goods_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取满减送详情
     * @param unknown $mansong_id
    */
    public function getPromotionMansongDetail(){
        $mansong_id = isset($this->request_common_array['mansong_id']) ? $this->request_common_array['mansong_id'] : '';
        $retval = $this->service->getPromotionMansongDetail($mansong_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加限时折扣
     * @param unknown $discount_name
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $remark
     * @param unknown $goods_id_array  goods_id:discount,goods_id:discount
    */
    public function addPromotiondiscount(){
        $discount_name = isset($this->request_common_array['discount_name']) ? $this->request_common_array['discount_name'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $remark = isset($this->request_common_array['remark']) ? $this->request_common_array['remark'] : '';
        $goods_id_array = isset($this->request_common_array['goods_id_array']) ? $this->request_common_array['goods_id_array'] : '';
        $res = $this->service->addPromotiondiscount($discount_name, $start_time, $end_time, $remark, $goods_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改限时折扣
     * @param unknown $discount_id
     * @param unknown $discount_name
     * @param unknown $start_time
     * @param unknown $end_time
     * @param unknown $remark
     * @param unknown $goods_id_array
    */
    public function updatePromotionDiscount(){
        $discount_id = isset($this->request_common_array['discount_id']) ? $this->request_common_array['discount_id'] : '';
        $discount_name = isset($this->request_common_array['discount_name']) ? $this->request_common_array['discount_name'] : '';
        $start_time = isset($this->request_common_array['start_time']) ? $this->request_common_array['start_time'] : '';
        $end_time = isset($this->request_common_array['end_time']) ? $this->request_common_array['end_time'] : '';
        $remark = isset($this->request_common_array['remark']) ? $this->request_common_array['remark'] : '';
        $goods_id_array = isset($this->request_common_array['goods_id_array']) ? $this->request_common_array['goods_id_array'] : '';
        $res = $this->service->addPromotiondiscount($discount_id,$discount_name, $start_time, $end_time, $remark, $goods_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 关闭限时折扣活动
     * @param unknown $discount_id
    */
    public function closePromotionDiscount(){
        $discount_id = isset($this->request_common_array['discount_id']) ? $this->request_common_array['discount_id'] : '';
        $res = $this->service->closePromotionDiscount($discount_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取限时折扣列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getPromotionDiscountList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : 'create_time desc';
        $retval = $this->service->getPromotionDiscountList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取限时折扣详情
     * @param unknown $discount_id
    */
    public function getPromotionDiscountDetail(){
        $discount_id = isset($this->request_common_array['discount_id']) ? $this->request_common_array['discount_id'] : '';
        $retval = $this->service->getPromotionDiscountDetail($discount_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 删除限时折扣
     * @param unknown $discount_id
    */
    public function delPromotionDiscount(){
        $discount_id = isset($this->request_common_array['discount_id']) ? $this->request_common_array['discount_id'] : '';
        $res = $this->service->delPromotionDiscount($discount_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 关闭满减送活动
     * @param unknown $mansong_id
    */
    public function closePromotionMansong(){
        $mansong_id = isset($this->request_common_array['mansong_id']) ? $this->request_common_array['mansong_id'] : '';
        $res = $this->service->closePromotionMansong($mansong_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除满减送活动
     * @param unknown $mansong_id
    */
    public function delPromotionMansong(){
        $mansong_id = isset($this->request_common_array['mansong_id']) ? $this->request_common_array['mansong_id'] : '';
        $res = $this->service->delPromotionMansong($mansong_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 得到店铺的满额包邮信息
     * @param unknown $shop_id
    */
    public function getPromotionFullMail(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getPromotionFullMail($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 更新店铺的满额包邮活动
     * @param unknown $shop_id
     * @param unknown $is_open
     * @param unknown $full_mail_money
    */
    public function updatePromotionFullMail(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $is_open = isset($this->request_common_array['is_open']) ? $this->request_common_array['is_open'] : '';
        $full_mail_money = isset($this->request_common_array['full_mail_money']) ? $this->request_common_array['full_mail_money'] : '';
        $res = $this->service->updatePromotionFullMail($shop_id, $is_open, $full_mail_money);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改满减送活动审核状态
     * @param unknown $mansong_id
     * @param unknown $type
    */
    public function setStatusPromotionMansong(){
        $mansong_id = isset($this->request_common_array['mansong_id']) ? $this->request_common_array['mansong_id'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $res = $this->service->setStatusPromotionMansong($mansong_id,$type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改限时折扣状态
     * @param unknown $discount_id
     * @param unknown $type
    */
    public function setStatusPromotionDiscount(){
        $discount_id = isset($this->request_common_array['discount_id']) ? $this->request_common_array['discount_id'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $res = $this->service->setStatusPromotionDiscount($discount_id,$type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}