<?php
/**
 * Express.php
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
use data\service\Express as ExpressService;


class Express extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new ExpressService();
    }
    
    /**
     * 获取物流模板列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    function getShippingFeeList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShippingFeeList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加物流模板
     *
     * @param unknown $shipping_fee_name
     * @param unknown $shipping_fee_ext
     *            ;snum; sprice; xnum; xprice;snum; sprice; xnum; xprice
    */
    function addShippingFee(){
        $co_id = isset($this->request_common_array['co_id']) ? $this->request_common_array['co_id'] : '';
        $is_default = isset($this->request_common_array['is_default']) ? $this->request_common_array['is_default'] : '';
        $shipping_fee_name = isset($this->request_common_array['shipping_fee_name']) ? $this->request_common_array['shipping_fee_name'] : '';
        $province_id_array = isset($this->request_common_array['province_id_array']) ? $this->request_common_array['province_id_array'] : '';
        $city_id_array = isset($this->request_common_array['city_id_array']) ? $this->request_common_array['city_id_array'] : '';
        $weight_is_use = isset($this->request_common_array['weight_is_use']) ? $this->request_common_array['weight_is_use'] : '';
        $weight_snum = isset($this->request_common_array['weight_snum']) ? $this->request_common_array['weight_snum'] : '';
        $weight_sprice = isset($this->request_common_array['weight_sprice']) ? $this->request_common_array['weight_sprice'] : '';
        $weight_xnum = isset($this->request_common_array['weight_xnum']) ? $this->request_common_array['weight_xnum'] : '';
        $weight_xprice = isset($this->request_common_array['weight_xprice']) ? $this->request_common_array['weight_xprice'] : '';
        $volume_is_use = isset($this->request_common_array['volume_is_use']) ? $this->request_common_array['volume_is_use'] : '';
        $volume_snum = isset($this->request_common_array['volume_snum']) ? $this->request_common_array['volume_snum'] : '';
        $volume_sprice = isset($this->request_common_array['volume_sprice']) ? $this->request_common_array['volume_sprice'] : '';
        $volume_xnum = isset($this->request_common_array['volume_xnum']) ? $this->request_common_array['volume_xnum'] : '';
        $volume_xprice = isset($this->request_common_array['volume_xprice']) ? $this->request_common_array['volume_xprice'] : '';
        $bynum_is_use = isset($this->request_common_array['bynum_is_use']) ? $this->request_common_array['bynum_is_use'] : '';
        $bynum_snum = isset($this->request_common_array['bynum_snum']) ? $this->request_common_array['bynum_snum'] : '';
        $bynum_sprice = isset($this->request_common_array['bynum_sprice']) ? $this->request_common_array['bynum_sprice'] : '';
        $bynum_xnum = isset($this->request_common_array['bynum_xnum']) ? $this->request_common_array['bynum_xnum'] : '';
        $bynum_xprice = isset($this->request_common_array['bynum_xprice']) ? $this->request_common_array['bynum_xprice'] : '';
        $res = $this->service->addShippingFee($co_id, $is_default, $shipping_fee_name, $province_id_array, $city_id_array, $weight_is_use, $weight_snum, $weight_sprice, $weight_xnum, $weight_xprice, $volume_is_use, $volume_snum, $volume_sprice, $volume_xnum, $volume_xprice, $bynum_is_use, $bynum_snum, $bynum_sprice, $bynum_xnum, $bynum_xprice);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改物流模板
     *
     * @param unknown $shipping_fee_id
     * @param unknown $shipping_fee_name
     * @param unknown $shipping_fee_ext;snum;
     *            sprice; xnum; xprice;snum; sprice; xnum; xprice
    */
    function updateShippingFee(){
        $shipping_fee_id = isset($this->request_common_array['shipping_fee_id']) ? $this->request_common_array['shipping_fee_id'] : '';
        $is_default = isset($this->request_common_array['is_default']) ? $this->request_common_array['is_default'] : '';
        $shipping_fee_name = isset($this->request_common_array['shipping_fee_name']) ? $this->request_common_array['shipping_fee_name'] : '';
        $province_id_array = isset($this->request_common_array['province_id_array']) ? $this->request_common_array['province_id_array'] : '';
        $city_id_array = isset($this->request_common_array['city_id_array']) ? $this->request_common_array['city_id_array'] : '';
        $weight_is_use = isset($this->request_common_array['weight_is_use']) ? $this->request_common_array['weight_is_use'] : '';
        $weight_snum = isset($this->request_common_array['weight_snum']) ? $this->request_common_array['weight_snum'] : '';
        $weight_sprice = isset($this->request_common_array['weight_sprice']) ? $this->request_common_array['weight_sprice'] : '';
        $weight_xnum = isset($this->request_common_array['weight_xnum']) ? $this->request_common_array['weight_xnum'] : '';
        $weight_xprice = isset($this->request_common_array['weight_xprice']) ? $this->request_common_array['weight_xprice'] : '';
        $volume_is_use = isset($this->request_common_array['volume_is_use']) ? $this->request_common_array['volume_is_use'] : '';
        $volume_snum = isset($this->request_common_array['volume_snum']) ? $this->request_common_array['volume_snum'] : '';
        $volume_sprice = isset($this->request_common_array['volume_sprice']) ? $this->request_common_array['volume_sprice'] : '';
        $volume_xnum = isset($this->request_common_array['volume_xnum']) ? $this->request_common_array['volume_xnum'] : '';
        $volume_xprice = isset($this->request_common_array['volume_xprice']) ? $this->request_common_array['volume_xprice'] : '';
        $bynum_is_use = isset($this->request_common_array['bynum_is_use']) ? $this->request_common_array['bynum_is_use'] : '';
        $bynum_snum = isset($this->request_common_array['bynum_snum']) ? $this->request_common_array['bynum_snum'] : '';
        $bynum_sprice = isset($this->request_common_array['bynum_sprice']) ? $this->request_common_array['bynum_sprice'] : '';
        $bynum_xnum = isset($this->request_common_array['bynum_xnum']) ? $this->request_common_array['bynum_xnum'] : '';
        $bynum_xprice = isset($this->request_common_array['bynum_xprice']) ? $this->request_common_array['bynum_xprice'] : '';
        $res = $this->service->updateShippingFee($shipping_fee_id, $is_default, $shipping_fee_name, $province_id_array, $city_id_array, $weight_is_use, $weight_snum, $weight_sprice, $weight_xnum, $weight_xprice, $volume_is_use, $volume_snum, $volume_sprice, $volume_xnum, $volume_xprice, $bynum_is_use, $bynum_snum, $bynum_sprice, $bynum_xnum, $bynum_xprice);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 运费模板详情
     *
     * @param unknown $shipping_fee_id
    */
    function shippingFeeDetail(){
        $shipping_fee_id = isset($this->request_common_array['shipping_fee_id']) ? $this->request_common_array['shipping_fee_id'] : '';
        $retval = $this->service->shippingFeeDetail($shipping_fee_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 运费模板删除
     *
     * @param unknown $shipping_fee_id
    */
    function shippingFeeDelete(){
        $shipping_fee_id = isset($this->request_common_array['shipping_fee_id']) ? $this->request_common_array['shipping_fee_id'] : '';
        $res = $this->service->shippingFeeDelete($shipping_fee_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 运费模板列表
     *
     * @param unknown $where
     * @param string $fields
    */
    function shippingFeeQuery(){
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $fields = isset($this->request_common_array['fields']) ? $this->request_common_array['fields'] : '*';
        $retval = $this->service->shippingFeeQuery($where, $fields);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取物流公司
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    function getExpressCompanyList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getExpressCompanyList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加物流公司
     *
     * @param unknown $shopId
     * @param unknown $company_name
     * @param unknown $express_no
     * @param unknown $is_enabled
     * @param unknown $image
     * @param unknown $phone
     * @param unknown $orders
    */
    function addExpressCompany(){
        $shopId = isset($this->request_common_array['shopId']) ? $this->request_common_array['shopId'] : '';
        $company_name = isset($this->request_common_array['company_name']) ? $this->request_common_array['company_name'] : '';
        $express_logo = isset($this->request_common_array['express_logo']) ? $this->request_common_array['express_logo'] : '';
        $express_no = isset($this->request_common_array['express_no']) ? $this->request_common_array['express_no'] : '';
        $is_enabled = isset($this->request_common_array['is_enabled']) ? $this->request_common_array['is_enabled'] : '';
        $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
        $phone = isset($this->request_common_array['phone']) ? $this->request_common_array['phone'] : '';
        $orders = isset($this->request_common_array['orders']) ? $this->request_common_array['orders'] : '';
        $is_default = isset($this->request_common_array['is_default']) ? $this->request_common_array['is_default'] : '';
        $res = $this->service->addExpressCompany($shopId, $company_name, $express_logo, $express_no, $is_enabled, $image, $phone, $orders, $is_default);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 把别的改为未默认,把当前设置为默认
    */
    public function defaultExpressCompany(){
        $retval = $this->service->defaultExpressCompany();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改物流公司
     *
     * @param unknown $co_id
     * @param unknown $shopId
     * @param unknown $company_name
     * @param unknown $express_no
     * @param unknown $is_enabled
     * @param unknown $image
     * @param unknown $phone
     * @param unknown $orders
    */
    function updateExpressCompany(){
        $co_id = isset($this->request_common_array['co_id']) ? $this->request_common_array['co_id'] : '';
        $shopId = isset($this->request_common_array['shopId']) ? $this->request_common_array['shopId'] : '';
        $company_name = isset($this->request_common_array['company_name']) ? $this->request_common_array['company_name'] : '';
        $express_logo = isset($this->request_common_array['express_logo']) ? $this->request_common_array['express_logo'] : '';
        $express_no = isset($this->request_common_array['express_no']) ? $this->request_common_array['express_no'] : '';
        $is_enabled = isset($this->request_common_array['is_enabled']) ? $this->request_common_array['is_enabled'] : '';
        $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
        $phone = isset($this->request_common_array['phone']) ? $this->request_common_array['phone'] : '';
        $orders = isset($this->request_common_array['orders']) ? $this->request_common_array['orders'] : '';
        $is_default = isset($this->request_common_array['is_default']) ? $this->request_common_array['is_default'] : '';
        $res = $this->service->updateExpressCompany($co_id, $shopId, $company_name, $express_logo, $express_no, $is_enabled, $image, $phone, $orders, $is_default);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 物流公司详情
     *
     * @param unknown $co_id
    */
    function expressCompanyDetail($co_id){
        $co_id = isset($this->request_common_array['co_id']) ? $this->request_common_array['co_id'] : '';
        $retval = $this->service->expressCompanyDetail($co_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 删除物流公司
     *
     * @param unknown $co_id
    */
    function expressCompanyDelete($co_id){
        $co_id = isset($this->request_common_array['co_id']) ? $this->request_common_array['co_id'] : '';
        $res = $this->service->expressCompanyDelete($co_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 物流公司列表
     *
     * @param unknown $where
     * @param string $fields
    */
    function expressCompanyQuery(){
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $fields = isset($this->request_common_array['fields']) ? $this->request_common_array['fields'] : '*';
        $retval = $this->service->expressCompanyQuery($where, $fields);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加物流公司地址
     *
     * @param unknown $contact
     * @param unknown $mobile
     * @param unknown $phone
     * @param unknown $company_name
     * @param unknown $province
     * @param unknown $city
     * @param unknown $district
     * @param unknown $zipcode
     * @param unknown $address
    */
    function addShopExpressAddress(){
        $contact = isset($this->request_common_array['contact']) ? $this->request_common_array['contact'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $phone = isset($this->request_common_array['phone']) ? $this->request_common_array['phone'] : '';
        $company_name = isset($this->request_common_array['company_name']) ? $this->request_common_array['company_name'] : '';
        $province = isset($this->request_common_array['province']) ? $this->request_common_array['province'] : '';
        $city = isset($this->request_common_array['city']) ? $this->request_common_array['city'] : '';
        $district = isset($this->request_common_array['district']) ? $this->request_common_array['district'] : '';
        $zipcode = isset($this->request_common_array['zipcode']) ? $this->request_common_array['zipcode'] : '';
        $address = isset($this->request_common_array['address']) ? $this->request_common_array['address'] : '';
        $res = $this->service->addShopExpressAddress($contact, $mobile, $phone, $company_name, $province, $city, $district, $zipcode, $address);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改物流地址
     *
     * @param unknown $express_address_id
     * @param unknown $contact
     * @param unknown $mobile
     * @param unknown $phone
     * @param unknown $company_name
     * @param unknown $province
     * @param unknown $city
     * @param unknown $district
     * @param unknown $zipcode
     * @param unknown $address
    */
    function updateShopExpressAddress(){
        $express_address_id = isset($this->request_common_array['express_address_id']) ? $this->request_common_array['express_address_id'] : '';
        $contact = isset($this->request_common_array['contact']) ? $this->request_common_array['contact'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $phone = isset($this->request_common_array['phone']) ? $this->request_common_array['phone'] : '';
        $company_name = isset($this->request_common_array['company_name']) ? $this->request_common_array['company_name'] : '';
        $province = isset($this->request_common_array['province']) ? $this->request_common_array['province'] : '';
        $city = isset($this->request_common_array['city']) ? $this->request_common_array['city'] : '';
        $district = isset($this->request_common_array['district']) ? $this->request_common_array['district'] : '';
        $zipcode = isset($this->request_common_array['zipcode']) ? $this->request_common_array['zipcode'] : '';
        $address = isset($this->request_common_array['address']) ? $this->request_common_array['address'] : '';
        $res = $this->service->updateShopExpressAddress($express_address_id, $contact, $mobile, $phone, $company_name, $province, $city, $district, $zipcode, $address);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改公司发货标记
     *
     * @param unknown $express_address_id
     * @param unknown $is_consigner
    */
    function modifyShopExpressAddressConsigner(){
        $express_address_id = isset($this->request_common_array['express_address_id']) ? $this->request_common_array['express_address_id'] : '';
        $is_consigner = isset($this->request_common_array['is_consigner']) ? $this->request_common_array['is_consigner'] : '';
        $res = $this->service->modifyShopExpressAddressConsigner($express_address_id, $is_consigner);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改公司收货标记
     *
     * @param unknown $express_address_id
     * @param unknown $receiver
    */
    function modifyShopExpressAddressReceiver(){
        $express_address_id = isset($this->request_common_array['express_address_id']) ? $this->request_common_array['express_address_id'] : '';
        $is_receiver = isset($this->request_common_array['is_receiver']) ? $this->request_common_array['is_receiver'] : '';
        $res = $this->service->modifyShopExpressAddressReceiver($express_address_id, $is_receiver);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取公司物流地址
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    function getShopExpressAddressList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopExpressAddressList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取公司默认收货地址
    */
    function getDefaultShopExpressAddress(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getDefaultShopExpressAddress($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 删除物流地址
     *
     * @param unknown $express_address_id_array
     *            ','隔开
    */
    function deleteShopExpressAddress(){
        $express_address_id_array = isset($this->request_common_array['express_address_id_array']) ? $this->request_common_array['express_address_id_array'] : '';
        $res = $this->service->deleteShopExpressAddress($express_address_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询单条物流地址详情
     *
     * @param unknown $express_address_id
    */
    function selectShopExpressAddressInfo(){
        $express_address_id = isset($this->request_common_array['express_address_id']) ? $this->request_common_array['express_address_id'] : '';
        $retval = $this->service->selectShopExpressAddressInfo($express_address_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取物流模板内容
     *
     * @param unknown $shop_id
    */
    function getExpressShippingItemsLibrary(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getExpressShippingItemsLibrary($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 得到物流模板
     *
     * @param unknown $co_id
    */
    function getExpressShipping(){
        $co_id = isset($this->request_common_array['co_id']) ? $this->request_common_array['co_id'] : '';
        $retval = $this->service->getExpressShipping($co_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 得到物流模板的打印项
     *
     * @param unknown $sid
    */
    function getExpressShippingItems(){
        $sid = isset($this->request_common_array['sid']) ? $this->request_common_array['sid'] : '';
        $retval = $this->service->getExpressShippingItems($sid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 得到物流模板的打印项信息
     *
     * @param unknown $sid
     * @param unknown $itemsArray
    */
    function updateExpressShippingItem(){
        $sid = isset($this->request_common_array['sid']) ? $this->request_common_array['sid'] : '';
        $itemsArray = isset($this->request_common_array['itemsArray']) ? $this->request_common_array['itemsArray'] : '';
        $res = $this->service->updateExpressShippingItem($sid, $itemsArray);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 更新物流模板的具体信息
     *
     * @param unknown $template_id
     * @param unknown $width
     * @param unknown $height
     * @param unknown $imgUrl
     * @param unknown $itemsArray
    */
    function updateExpressShipping(){
        $template_id = isset($this->request_common_array['template_id']) ? $this->request_common_array['template_id'] : '';
        $width = isset($this->request_common_array['width']) ? $this->request_common_array['width'] : '';
        $height = isset($this->request_common_array['height']) ? $this->request_common_array['height'] : '';
        $imgUrl = isset($this->request_common_array['imgUrl']) ? $this->request_common_array['imgUrl'] : '';
        $itemsArray = isset($this->request_common_array['itemsArray']) ? $this->request_common_array['itemsArray'] : '';
        $res = $this->service->updateExpressShipping($template_id, $width, $height, $imgUrl, $itemsArray);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 根据物流公司id查询是否有默认地区
     *
     * @param unknown $co_id
     *            物流公司id
    */
    function isHasExpressCompanyDefaultTemplate(){
        $co_id = isset($this->request_common_array['co_id']) ? $this->request_common_array['co_id'] : '';
        $retval = $this->service->isHasExpressCompanyDefaultTemplate($co_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     *
     * 获取物流公司的省市id组，排除默认地区
     * 2017年6月29日 11:07:40 王永杰
     *
     * @param 物流公司id&nbsp; $co_id
     * @param 排除当前编辑的省id组nbsp; $province_id_array
     * @param 排除当前编辑的市id组nbsp; $city_id_array
    */
    function getExpressCompanyProvincesAndCitiesById(){
        $co_id = isset($this->request_common_array['co_id']) ? $this->request_common_array['co_id'] : '';
        $current_province_id_array = isset($this->request_common_array['current_province_id_array']) ? $this->request_common_array['current_province_id_array'] : '';
        $current_city_id_array = isset($this->request_common_array['current_city_id_array']) ? $this->request_common_array['current_city_id_array'] : '';
        $retval = $this->service->getExpressCompanyProvincesAndCitiesById($co_id, $current_province_id_array, $current_city_id_array);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}