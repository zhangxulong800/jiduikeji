<?php
/**
 * Shop.php
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
use data\service\Shop as ShopService;


class Shop extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new ShopService();
    }
    
    /**
     * 获取店铺轮播图列表
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
     */
    public function getShopAdList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopAdList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加店铺轮播图
     *
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
    */
    public function addShopAd(){
        $ad_image = isset($this->request_common_array['ad_image']) ? $this->request_common_array['ad_image'] : '';
        $link_url = isset($this->request_common_array['link_url']) ? $this->request_common_array['link_url'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $background = isset($this->request_common_array['background']) ? $this->request_common_array['background'] : '';
        $res = $this->service->addShopAd($ad_image, $link_url, $sort, $type, $background);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改店铺轮播图
     *
     * @param unknown $id
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
    */
    public function updateShopAd(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $ad_image = isset($this->request_common_array['ad_image']) ? $this->request_common_array['ad_image'] : '';
        $link_url = isset($this->request_common_array['link_url']) ? $this->request_common_array['link_url'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $background = isset($this->request_common_array['background']) ? $this->request_common_array['background'] : '';
        $res = $this->service->updateShopAd($id, $ad_image, $link_url, $sort, $type, $background);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取店铺轮播图详情
     *
     * @param unknown $id
    */
    public function getShopAdDetail(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getShopAdDetail($id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 删除店铺轮播图
     *
     * @param unknown $id
    */
    public function delShopAd(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->delShopAd($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 导航列表
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function ShopNavigationList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->ShopNavigationList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺导航添加
     *
     * @param unknown $shop_id
     * @param unknown $nav_title
     * @param unknown $nav_url
     * @param unknown $type
     * @param unknown $sort
    */
    public function addShopNavigation(){
        $nav_title = isset($this->request_common_array['nav_title']) ? $this->request_common_array['nav_title'] : '';
        $nav_url = isset($this->request_common_array['nav_url']) ? $this->request_common_array['nav_url'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $align = isset($this->request_common_array['align']) ? $this->request_common_array['align'] : '';
        $nav_type = isset($this->request_common_array['nav_type']) ? $this->request_common_array['nav_type'] : '';
        $is_blank = isset($this->request_common_array['is_blank']) ? $this->request_common_array['is_blank'] : '';
        $template_name = isset($this->request_common_array['template_name']) ? $this->request_common_array['template_name'] : '';
        $res = $this->service->addShopNavigation($nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 店铺导航修改
     *
     * @param unknown $shop_id
     * @param unknown $nav_title
     * @param unknown $nav_url
     * @param unknown $type
     * @param unknown $sort
    */
    public function updateShopNavigation(){
        $nav_id = isset($this->request_common_array['nav_id']) ? $this->request_common_array['nav_id'] : '';
        $nav_title = isset($this->request_common_array['nav_title']) ? $this->request_common_array['nav_title'] : '';
        $nav_url = isset($this->request_common_array['nav_url']) ? $this->request_common_array['nav_url'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $align = isset($this->request_common_array['align']) ? $this->request_common_array['align'] : '';
        $nav_type = isset($this->request_common_array['nav_type']) ? $this->request_common_array['nav_type'] : '';
        $is_blank = isset($this->request_common_array['is_blank']) ? $this->request_common_array['is_blank'] : '';
        $template_name = isset($this->request_common_array['template_name']) ? $this->request_common_array['template_name'] : '';
        $res = $this->service->updateShopNavigation($nav_id, $nav_title, $nav_url, $type, $sort, $align, $nav_type, $is_blank, $template_name);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 店铺导航删除
     *
     * @param unknown $nav_id
    */
    public function delShopNavigation(){
        $nav_id = isset($this->request_common_array['nav_id']) ? $this->request_common_array['nav_id'] : '';
        $res = $this->service->delShopNavigation($nav_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询店铺导航详情
     *
     * @param unknown $nav_id
    */
    public function shopNavigationDetail(){
        $nav_id = isset($this->request_common_array['nav_id']) ? $this->request_common_array['nav_id'] : '';
        $retval = $this->service->shopNavigationDetail($nav_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改导航排序
     *
     * @param unknown $nav_id
     * @param unknown $sort
    */
    public function modifyShopNavigationSort(){
        $nav_id = isset($this->request_common_array['nav_id']) ? $this->request_common_array['nav_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->modifyShopNavigationSort($nav_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取店铺列表
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function getShopList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店铺分类
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function getShopGroup(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopGroup($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺申请
     *
     * @param unknown $apply_type
     * @param unknown $uid
     * @param unknown $company_name
     * @param unknown $company_province_id
     * @param unknown $company_city_id
     * @param unknown $company_address
     * @param unknown $company_address_detail
     * @param unknown $company_phone
     * @param unknown $company_employee_count
     * @param unknown $company_registered_capital
     * @param unknown $contacts_name
     * @param unknown $contacts_phone
     * @param unknown $contacts_email
     * @param unknown $contacts_card_no
     * @param unknown $contacts_card_electronic_1
     * @param unknown $contacts_card_electronic_2
     * @param unknown $contacts_card_electronic_3
     * @param unknown $business_licence_number
     * @param unknown $business_licence_address
     * @param unknown $business_licence_start
     * @param unknown $business_licence_start
     * @param unknown $business_licence_end
     * @param unknown $business_sphere
     * @param unknown $business_licence_number_electronic
     * @param unknown $organization_code
     * @param unknown $organization_code_electronic
     * @param unknown $general_taxpayer
     * @param unknown $bank_account_name
     * @param unknown $bank_account_number
     * @param unknown $bank_name
     * @param unknown $bank_code
     * @param unknown $bank_address
     * @param unknown $bank_licence_electronic
     * @param unknown $is_settlement_account
     * @param unknown $settlement_bank_account_name
     * @param unknown $settlement_bank_account_number
     * @param unknown $settlement_bank_name
     * @param unknown $settlement_bank_code
     * @param unknown $settlement_bank_address
     * @param unknown $tax_registration_certificate
     * @param unknown $taxpayer_id
     * @param unknown $tax_registration_certificate_electronic
     * @param unknown $shop_name
     * @param unknown $apply_state
     * @param unknown $apply_message
     * @param unknown $apply_year
     * @param unknown $shop_type_name
     * @param unknown $shop_type_id
     * @param unknown $shop_group_name
     * @param unknown $shop_group_id
     * @param unknown $paying_money_certificate
     * @param unknown $paying_money_certificate_explain
     * @param unknown $paying_amount
    */
    public function addShopApply(){
        $apply_type = isset($this->request_common_array['apply_type']) ? $this->request_common_array['apply_type'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $company_name = isset($this->request_common_array['company_name']) ? $this->request_common_array['company_name'] : '';
        $company_province_id = isset($this->request_common_array['company_province_id']) ? $this->request_common_array['company_province_id'] : '';
        $company_city_id = isset($this->request_common_array['company_city_id']) ? $this->request_common_array['company_city_id'] : '';
        $company_district_id = isset($this->request_common_array['company_district_id']) ? $this->request_common_array['company_district_id'] : '';
        $company_address_detail = isset($this->request_common_array['company_address_detail']) ? $this->request_common_array['company_address_detail'] : '';
        $company_phone = isset($this->request_common_array['company_phone']) ? $this->request_common_array['company_phone'] : '';
        $company_type = isset($this->request_common_array['company_type']) ? $this->request_common_array['company_type'] : '';
        $company_employee_count = isset($this->request_common_array['company_employee_count']) ? $this->request_common_array['company_employee_count'] : '';
        $company_registered_capital = isset($this->request_common_array['company_registered_capital']) ? $this->request_common_array['company_registered_capital'] : '';
        $contacts_name = isset($this->request_common_array['contacts_name']) ? $this->request_common_array['contacts_name'] : '';
        $contacts_phone = isset($this->request_common_array['contacts_phone']) ? $this->request_common_array['contacts_phone'] : '';
        $contacts_email = isset($this->request_common_array['contacts_email']) ? $this->request_common_array['contacts_email'] : '';
        $contacts_card_no = isset($this->request_common_array['contacts_card_no']) ? $this->request_common_array['contacts_card_no'] : '';
        $contacts_card_electronic_1 = isset($this->request_common_array['contacts_card_electronic_1']) ? $this->request_common_array['contacts_card_electronic_1'] : '';
        $contacts_card_electronic_2 = isset($this->request_common_array['contacts_card_electronic_2']) ? $this->request_common_array['contacts_card_electronic_2'] : '';
        $contacts_card_electronic_3 = isset($this->request_common_array['contacts_card_electronic_3']) ? $this->request_common_array['contacts_card_electronic_3'] : '';
        $business_licence_number = isset($this->request_common_array['business_licence_number']) ? $this->request_common_array['business_licence_number'] : '';
        $business_sphere = isset($this->request_common_array['business_sphere']) ? $this->request_common_array['business_sphere'] : '';
        $business_licence_number_electronic = isset($this->request_common_array['business_licence_number_electronic']) ? $this->request_common_array['business_licence_number_electronic'] : '';
        $organization_code = isset($this->request_common_array['organization_code']) ? $this->request_common_array['organization_code'] : '';
        $organization_code_electronic = isset($this->request_common_array['organization_code_electronic']) ? $this->request_common_array['organization_code_electronic'] : '';
        $general_taxpayer = isset($this->request_common_array['general_taxpayer']) ? $this->request_common_array['general_taxpayer'] : '';
        $bank_account_name = isset($this->request_common_array['bank_account_name']) ? $this->request_common_array['bank_account_name'] : '';
        $bank_account_number = isset($this->request_common_array['bank_account_number']) ? $this->request_common_array['bank_account_number'] : '';
        $bank_name = isset($this->request_common_array['bank_name']) ? $this->request_common_array['bank_name'] : '';
        $bank_code = isset($this->request_common_array['bank_code']) ? $this->request_common_array['bank_code'] : '';
        $bank_address = isset($this->request_common_array['bank_address']) ? $this->request_common_array['bank_address'] : '';
        $bank_licence_electronic = isset($this->request_common_array['bank_licence_electronic']) ? $this->request_common_array['bank_licence_electronic'] : '';
        $is_settlement_account = isset($this->request_common_array['is_settlement_account']) ? $this->request_common_array['is_settlement_account'] : '';
        $settlement_bank_account_name = isset($this->request_common_array['settlement_bank_account_name']) ? $this->request_common_array['settlement_bank_account_name'] : '';
        $settlement_bank_account_number = isset($this->request_common_array['settlement_bank_account_number']) ? $this->request_common_array['settlement_bank_account_number'] : '';
        $settlement_bank_name = isset($this->request_common_array['settlement_bank_name']) ? $this->request_common_array['settlement_bank_name'] : '';
        $settlement_bank_code = isset($this->request_common_array['settlement_bank_code']) ? $this->request_common_array['settlement_bank_code'] : '';
        $settlement_bank_address = isset($this->request_common_array['settlement_bank_address']) ? $this->request_common_array['settlement_bank_address'] : '';
        $tax_registration_certificate = isset($this->request_common_array['tax_registration_certificate']) ? $this->request_common_array['tax_registration_certificate'] : '';
        $taxpayer_id = isset($this->request_common_array['taxpayer_id']) ? $this->request_common_array['taxpayer_id'] : '';
        $tax_registration_certificate_electronic = isset($this->request_common_array['tax_registration_certificate_electronic']) ? $this->request_common_array['tax_registration_certificate_electronic'] : '';
        $shop_name = isset($this->request_common_array['shop_name']) ? $this->request_common_array['shop_name'] : '';
        $apply_state = isset($this->request_common_array['apply_state']) ? $this->request_common_array['apply_state'] : '';
        $apply_message = isset($this->request_common_array['apply_message']) ? $this->request_common_array['apply_message'] : '';
        $apply_year = isset($this->request_common_array['apply_year']) ? $this->request_common_array['apply_year'] : '';
        $shop_type_name = isset($this->request_common_array['shop_type_name']) ? $this->request_common_array['shop_type_name'] : '';
        $shop_type_id = isset($this->request_common_array['shop_type_id']) ? $this->request_common_array['shop_type_id'] : '';
        $shop_group_name = isset($this->request_common_array['shop_group_name']) ? $this->request_common_array['shop_group_name'] : '';
        $shop_group_id = isset($this->request_common_array['shop_group_id']) ? $this->request_common_array['shop_group_id'] : '';
        $paying_money_certificate = isset($this->request_common_array['paying_money_certificate']) ? $this->request_common_array['paying_money_certificate'] : '';
        $paying_money_certificate_explain = isset($this->request_common_array['paying_money_certificate_explain']) ? $this->request_common_array['paying_money_certificate_explain'] : '';
        $paying_amount = isset($this->request_common_array['paying_amount']) ? $this->request_common_array['paying_amount'] : '';
        $recommend_uid = isset($this->request_common_array['recommend_uid']) ? $this->request_common_array['recommend_uid'] : '';
        $res = $this->service->addShopApply($apply_type, $uid, $company_name, $company_province_id, $company_city_id, $company_district_id, $company_address_detail, $company_phone,
        $company_type, $company_employee_count, $company_registered_capital, $contacts_name, $contacts_phone, $contacts_email, $contacts_card_no,
        $contacts_card_electronic_1, $contacts_card_electronic_2, $contacts_card_electronic_3, $business_licence_number, $business_sphere, $business_licence_number_electronic,
        $organization_code, $organization_code_electronic, $general_taxpayer, $bank_account_name, $bank_account_number, $bank_name, $bank_code, $bank_address,
        $bank_licence_electronic, $is_settlement_account, $settlement_bank_account_name, $settlement_bank_account_number, $settlement_bank_name, $settlement_bank_code,
        $settlement_bank_address, $tax_registration_certificate, $taxpayer_id, $tax_registration_certificate_electronic, $shop_name, $apply_state, $apply_message, $apply_year,
        $shop_type_name, $shop_type_id, $shop_group_name, $shop_group_id, $paying_money_certificate, $paying_money_certificate_explain, $paying_amount, $recommend_uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取店铺详情
     *
     * @param unknown $shop_id
    */
    public function getShopDetail(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopDetail($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    public function getShopInfo(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '';
        $retval = $this->service->getShopInfo($shop_id, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加店铺
     *
     * @param unknown $shop_name
     * @param unknown $shop_type
     * @param unknown $uid
     * @param unknown $shop_group_id
     * @param unknown $shop_company_name
     * @param unknown $province_id
     * @param unknown $city_id
     * @param unknown $shop_address
     * @param unknown $shop_zip
     * @param unknown $shop_sort
    */
    public function addshop(){
        $shop_name = isset($this->request_common_array['shop_name']) ? $this->request_common_array['shop_name'] : '';
        $shop_type = isset($this->request_common_array['shop_type']) ? $this->request_common_array['shop_type'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $shop_group_id = isset($this->request_common_array['shop_group_id']) ? $this->request_common_array['shop_group_id'] : '';
        $shop_company_name = isset($this->request_common_array['shop_company_name']) ? $this->request_common_array['shop_company_name'] : '';
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $shop_address = isset($this->request_common_array['shop_address']) ? $this->request_common_array['shop_address'] : '';
        $shop_zip = isset($this->request_common_array['shop_zip']) ? $this->request_common_array['shop_zip'] : '';
        $shop_sort = isset($this->request_common_array['shop_sort']) ? $this->request_common_array['shop_sort'] : '';
        $recommend_uid = isset($this->request_common_array['recommend_uid']) ? $this->request_common_array['recommend_uid'] : '';
        $res = $this->service->addshop($shop_name, $shop_type, $uid, $shop_group_id, $shop_company_name, $province_id, $city_id, $shop_address, $shop_zip, $shop_sort, $recommend_uid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 处理店铺申请请求
     *
     * @param unknown $shop_apply_id
     * @param unknown $type
     *            'agree,disagree'
    */
    public function dealwithShopApply(){
        $shop_apply_id = isset($this->request_common_array['shop_apply_id']) ? $this->request_common_array['shop_apply_id'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $recommend_uid = isset($this->request_common_array['recommend_uid']) ? $this->request_common_array['recommend_uid'] : '';
        $res = $this->service->dealwithShopApply($shop_apply_id, $type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 店铺申请列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
    */
    public function getShopApplyList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopApplyList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店铺等级类型列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
    */
    public function getShopTypeList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopTypeList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加店铺分组/分类
     *
     * @param unknown $group_name
     * @param unknown $group_sort
    */
    public function addShopGroup(){
        $group_name = isset($this->request_common_array['group_name']) ? $this->request_common_array['group_name'] : '';
        $group_sort = isset($this->request_common_array['group_sort']) ? $this->request_common_array['group_sort'] : '';
        $res = $this->service->addShopGroup($group_name, $group_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改店铺分组/分类
     *
     * @param unknown $shop_group_id
     * @param unknown $group_name
     * @param unknown $group_sort
    */
    public function updateShopGroup(){
        $shop_group_id = isset($this->request_common_array['shop_group_id']) ? $this->request_common_array['shop_group_id'] : '';
        $group_name = isset($this->request_common_array['group_name']) ? $this->request_common_array['group_name'] : '';
        $group_sort = isset($this->request_common_array['group_sort']) ? $this->request_common_array['group_sort'] : '';
        $res = $this->service->updateShopGroup($shop_group_id, $group_name, $group_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取店铺分组/分类详情
     *
     * @param unknown $shop_id
    */
    public function getShopGroupDetail(){
        $shop_group_id = isset($this->request_common_array['shop_group_id']) ? $this->request_common_array['shop_group_id'] : '';
        $retval = $this->service->getShopGroupDetail($shop_group_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 删除店铺分组/分类详情
     *
     * @param unknown $shop_group_id
    */
    public function delShopGroup(){
        $shop_group_id = isset($this->request_common_array['shop_group_id']) ? $this->request_common_array['shop_group_id'] : '';
        $res = $this->service->delShopGroup($shop_group_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取 店铺申请的 详细信息
     *
     * @param unknown $apply_id
    */
    public function getShopApplyDetail(){
        $apply_id = isset($this->request_common_array['apply_id']) ? $this->request_common_array['apply_id'] : '';
        $retval = $this->service->getShopApplyDetail($apply_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加店铺等级
     *
     * @param unknown $type_name
     * @param unknown $type_module_array
     * @param unknown $type_desc
     * @param unknown $type_sort
    */
    public function addShopType(){
        $type_name = isset($this->request_common_array['type_name']) ? $this->request_common_array['type_name'] : '';
        $type_module_array = isset($this->request_common_array['type_module_array']) ? $this->request_common_array['type_module_array'] : '';
        $type_desc = isset($this->request_common_array['type_desc']) ? $this->request_common_array['type_desc'] : '';
        $type_sort = isset($this->request_common_array['type_sort']) ? $this->request_common_array['type_sort'] : '';
        $res = $this->service->addShopType($type_name, $type_module_array, $type_desc, $type_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改店铺等级
     *
     * @param unknown $instance_typeid
     * @param unknown $type_name
     * @param unknown $type_module_array
     * @param unknown $type_desc
     * @param unknown $type_sort
    */
    public function updateShopType(){
        $instance_typeid = isset($this->request_common_array['instance_typeid']) ? $this->request_common_array['instance_typeid'] : '';
        $type_name = isset($this->request_common_array['type_name']) ? $this->request_common_array['type_name'] : '';
        $type_module_array = isset($this->request_common_array['type_module_array']) ? $this->request_common_array['type_module_array'] : '';
        $type_desc = isset($this->request_common_array['type_desc']) ? $this->request_common_array['type_desc'] : '';
        $type_sort = isset($this->request_common_array['type_sort']) ? $this->request_common_array['type_sort'] : '';
        $res = $this->service->updateShopType($instance_typeid, $type_name, $type_module_array, $type_desc, $type_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 店铺等级详情
     *
     * @param unknown $instance_typeid
    */
    public function getShopTypeDetail(){
        $instance_typeid = isset($this->request_common_array['instance_typeid']) ? $this->request_common_array['instance_typeid'] : '';
        $retval = $this->service->getShopTypeDetail($instance_typeid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改店铺 （店铺后台端）
     *
     * @param unknown $shop_id
     * @param unknown $shop_logo
     * @param unknown $shop_banner
     * @param unknown $shop_avatar
     * @param unknown $shop_qq
     * @param unknown $shop_ww
     * @param unknown $shop_phone
     * @param unknown $shop_keywords
     * @param unknown $shop_description
    */
    public function updateShopConfigByshop(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $shop_logo = isset($this->request_common_array['shop_logo']) ? $this->request_common_array['shop_logo'] : '';
        $shop_banner = isset($this->request_common_array['shop_banner']) ? $this->request_common_array['shop_banner'] : '';
        $shop_avatar = isset($this->request_common_array['shop_avatar']) ? $this->request_common_array['shop_avatar'] : '';
        $shop_qrcode = isset($this->request_common_array['shop_qrcode']) ? $this->request_common_array['shop_qrcode'] : '';
        $shop_qq = isset($this->request_common_array['shop_qq']) ? $this->request_common_array['shop_qq'] : '';
        $shop_ww = isset($this->request_common_array['shop_ww']) ? $this->request_common_array['shop_ww'] : '';
        $shop_phone = isset($this->request_common_array['shop_phone']) ? $this->request_common_array['shop_phone'] : '';
        $shop_keywords = isset($this->request_common_array['shop_keywords']) ? $this->request_common_array['shop_keywords'] : '';
        $shop_description = isset($this->request_common_array['shop_description']) ? $this->request_common_array['shop_description'] : '';
        $shop_hint = isset($this->request_common_array['shop_hint']) ? $this->request_common_array['shop_hint'] : '';
        $res = $this->service->updateShopConfigByshop($shop_id, $shop_logo, $shop_banner, $shop_avatar, $shop_qrcode, $shop_qq, $shop_ww, $shop_phone, $shop_keywords, $shop_description, $shop_hint);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改店铺 （平台对店铺的修改）
     *
     * @param unknown $shop_id
     * @param unknown $shop_name
     * @param unknown $shop_group_id
     * @param unknown $shop_type
     * @param unknown $shop_credit
     * @param unknown $shop_desccredit
     * @param unknown $shop_servicecredit
     * @param unknown $shop_deliverycredit
     * @param unknown $store_qtian
     * @param unknown $shop_zhping
     * @param unknown $shop_erxiaoshi
     * @param unknown $shop_tuihuo
     * @param unknown $shop_shiyong
     * @param unknown $shop_xiaoxie
     * @param unknown $shop_huodaofk
     * @param unknown $shop_state
     * @param unknown $shop_close_info
    */
    public function updateShopConfigByPlatform(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $shop_name = isset($this->request_common_array['shop_name']) ? $this->request_common_array['shop_name'] : '';
        $shop_group_id = isset($this->request_common_array['shop_group_id']) ? $this->request_common_array['shop_group_id'] : '';
        $shop_platform_commission_rate = isset($this->request_common_array['shop_platform_commission_rate']) ? $this->request_common_array['shop_platform_commission_rate'] : '';
        $shop_type = isset($this->request_common_array['shop_type']) ? $this->request_common_array['shop_type'] : '';
        $shop_credit = isset($this->request_common_array['shop_credit']) ? $this->request_common_array['shop_credit'] : '';
        $shop_desccredit = isset($this->request_common_array['shop_desccredit']) ? $this->request_common_array['shop_desccredit'] : '';
        $shop_servicecredit = isset($this->request_common_array['shop_servicecredit']) ? $this->request_common_array['shop_servicecredit'] : '';
        $shop_deliverycredit = isset($this->request_common_array['shop_deliverycredit']) ? $this->request_common_array['shop_deliverycredit'] : '';
        $store_qtian = isset($this->request_common_array['store_qtian']) ? $this->request_common_array['store_qtian'] : '';
        $shop_zhping = isset($this->request_common_array['shop_zhping']) ? $this->request_common_array['shop_zhping'] : '';
        $shop_erxiaoshi = isset($this->request_common_array['shop_erxiaoshi']) ? $this->request_common_array['shop_erxiaoshi'] : '';
        $shop_tuihuo = isset($this->request_common_array['shop_tuihuo']) ? $this->request_common_array['shop_tuihuo'] : '';
        $shop_shiyong = isset($this->request_common_array['shop_shiyong']) ? $this->request_common_array['shop_shiyong'] : '';
        $shop_shiti = isset($this->request_common_array['shop_shiti']) ? $this->request_common_array['shop_shiti'] : '';
        $shop_xiaoxie = isset($this->request_common_array['shop_xiaoxie']) ? $this->request_common_array['shop_xiaoxie'] : '';
        $shop_huodaofk = isset($this->request_common_array['shop_huodaofk']) ? $this->request_common_array['shop_huodaofk'] : '';
        $shop_state = isset($this->request_common_array['shop_state']) ? $this->request_common_array['shop_state'] : '';
        $shop_close_info = isset($this->request_common_array['shop_close_info']) ? $this->request_common_array['shop_close_info'] : '';
        $res = $this->service->updateShopConfigByPlatform($shop_id, $shop_name, $shop_group_id, $shop_platform_commission_rate, $shop_type, $shop_credit, $shop_desccredit, $shop_servicecredit,
        $shop_deliverycredit, $store_qtian, $shop_zhping, $shop_erxiaoshi, $shop_tuihuo, $shop_shiyong, $shop_shiti, $shop_xiaoxie, $shop_huodaofk, $shop_state, $shop_close_info);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     *
     * @param unknown $apply_id
     * @param unknown $company_name
     * @param unknown $company_province_id
     * @param unknown $company_city_id
     * @param unknown $company_district_id
     * @param unknown $company_address_detail
     * @param unknown $company_phone
     * @param unknown $company_employee_count
     * @param unknown $company_registered_capital
     * @param unknown $contacts_name
     * @param unknown $contacts_phone
     * @param unknown $contacts_email
     * @param unknown $business_licence_number
     * @param unknown $business_sphere
     * @param unknown $business_licence_number_electronic
     * @param unknown $organization_code
     * @param unknown $organization_code_electronic
     * @param unknown $general_taxpayer
     * @param unknown $bank_account_name
     * @param unknown $bank_account_number
     * @param unknown $bank_name
     * @param unknown $bank_code
     * @param unknown $bank_address
     * @param unknown $bank_licence_electronic
     * @param unknown $is_settlement_account
     * @param unknown $settlement_bank_account_name
     * @param unknown $settlement_bank_account_number
     * @param unknown $settlement_bank_name
     * @param unknown $settlement_bank_code
     * @param unknown $settlement_bank_address
     * @param unknown $tax_registration_certificate
     * @param unknown $taxpayer_id
     * @param unknown $tax_registration_certificate_electronic
    */
    public function updateShopApply(){
        $apply_id = isset($this->request_common_array['apply_id']) ? $this->request_common_array['apply_id'] : '';
        $company_name = isset($this->request_common_array['company_name']) ? $this->request_common_array['company_name'] : '';
        $company_province_id = isset($this->request_common_array['company_province_id']) ? $this->request_common_array['company_province_id'] : '';
        $company_city_id = isset($this->request_common_array['company_city_id']) ? $this->request_common_array['company_city_id'] : '';
        $company_district_id = isset($this->request_common_array['company_district_id']) ? $this->request_common_array['company_district_id'] : '';
        $company_address_detail = isset($this->request_common_array['company_address_detail']) ? $this->request_common_array['company_address_detail'] : '';
        $company_phone = isset($this->request_common_array['company_phone']) ? $this->request_common_array['company_phone'] : '';
        $company_employee_count = isset($this->request_common_array['company_employee_count']) ? $this->request_common_array['company_employee_count'] : '';
        $company_registered_capital = isset($this->request_common_array['company_registered_capital']) ? $this->request_common_array['company_registered_capital'] : '';
        $contacts_name = isset($this->request_common_array['contacts_name']) ? $this->request_common_array['contacts_name'] : '';
        $contacts_phone = isset($this->request_common_array['contacts_phone']) ? $this->request_common_array['contacts_phone'] : '';
        $contacts_email = isset($this->request_common_array['contacts_email']) ? $this->request_common_array['contacts_email'] : '';
        $business_licence_number = isset($this->request_common_array['business_licence_number']) ? $this->request_common_array['business_licence_number'] : '';
        $business_sphere = isset($this->request_common_array['business_sphere']) ? $this->request_common_array['business_sphere'] : '';
        $business_licence_number_electronic = isset($this->request_common_array['business_licence_number_electronic']) ? $this->request_common_array['business_licence_number_electronic'] : '';
        $organization_code = isset($this->request_common_array['organization_code']) ? $this->request_common_array['organization_code'] : '';
        $organization_code_electronic = isset($this->request_common_array['organization_code_electronic']) ? $this->request_common_array['organization_code_electronic'] : '';
        $general_taxpayer = isset($this->request_common_array['general_taxpayer']) ? $this->request_common_array['general_taxpayer'] : '';
        $bank_account_name = isset($this->request_common_array['bank_account_name']) ? $this->request_common_array['bank_account_name'] : '';
        $bank_account_number = isset($this->request_common_array['bank_account_number']) ? $this->request_common_array['bank_account_number'] : '';
        $bank_name = isset($this->request_common_array['bank_name']) ? $this->request_common_array['bank_name'] : '';
        $bank_code = isset($this->request_common_array['bank_code']) ? $this->request_common_array['bank_code'] : '';
        $bank_address = isset($this->request_common_array['bank_address']) ? $this->request_common_array['bank_address'] : '';
        $bank_licence_electronic = isset($this->request_common_array['bank_licence_electronic']) ? $this->request_common_array['bank_licence_electronic'] : '';
        $is_settlement_account = isset($this->request_common_array['is_settlement_account']) ? $this->request_common_array['is_settlement_account'] : '';
        $settlement_bank_account_name = isset($this->request_common_array['settlement_bank_account_name']) ? $this->request_common_array['settlement_bank_account_name'] : '';
        $settlement_bank_account_number = isset($this->request_common_array['settlement_bank_account_number']) ? $this->request_common_array['settlement_bank_account_number'] : '';
        $settlement_bank_name = isset($this->request_common_array['settlement_bank_name']) ? $this->request_common_array['settlement_bank_name'] : '';
        $settlement_bank_code = isset($this->request_common_array['settlement_bank_code']) ? $this->request_common_array['settlement_bank_code'] : '';
        $settlement_bank_address = isset($this->request_common_array['settlement_bank_address']) ? $this->request_common_array['settlement_bank_address'] : '';
        $tax_registration_certificate = isset($this->request_common_array['tax_registration_certificate']) ? $this->request_common_array['tax_registration_certificate'] : '';
        $taxpayer_id = isset($this->request_common_array['taxpayer_id']) ? $this->request_common_array['taxpayer_id'] : '';
        $tax_registration_certificate_electronic = isset($this->request_common_array['tax_registration_certificate_electronic']) ? $this->request_common_array['tax_registration_certificate_electronic'] : '';
        $res = $this->service->updateShopApply($apply_id, $company_name, $company_province_id, $company_city_id, $company_district_id, $company_address_detail, $company_phone,
        $company_employee_count, $company_registered_capital, $contacts_name, $contacts_phone, $contacts_email, $business_licence_number, $business_sphere,
        $business_licence_number_electronic, $organization_code, $organization_code_electronic, $general_taxpayer, $bank_account_name, $bank_account_number, $bank_name,
        $bank_code, $bank_address, $bank_licence_electronic, $is_settlement_account, $settlement_bank_account_name, $settlement_bank_account_number, $settlement_bank_name,
        $settlement_bank_code, $settlement_bank_address, $tax_registration_certificate, $taxpayer_id, $tax_registration_certificate_electronic);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取用户在本店铺的消费
     *
     * @param unknown $shop_id
     * @param unknown $uid
    */
    public function getShopUserConsume(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->getShopUserConsume($shop_id, $uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店铺分享设置
     *
     * @param unknown $shop_id
    */
    public function getShopShareConfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopShareConfig($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改店铺分享设置
     *
     * @param unknown $shop_id
    */
    public function updateShopShareCinfig(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $goods_param_1 = isset($this->request_common_array['goods_param_1']) ? $this->request_common_array['goods_param_1'] : '';
        $goods_param_2 = isset($this->request_common_array['goods_param_2']) ? $this->request_common_array['goods_param_2'] : '';
        $shop_param_1 = isset($this->request_common_array['shop_param_1']) ? $this->request_common_array['shop_param_1'] : '';
        $shop_param_2 = isset($this->request_common_array['shop_param_2']) ? $this->request_common_array['shop_param_2'] : '';
        $shop_param_3 = isset($this->request_common_array['shop_param_3']) ? $this->request_common_array['shop_param_3'] : '';
        $qrcode_param_1 = isset($this->request_common_array['qrcode_param_1']) ? $this->request_common_array['qrcode_param_1'] : '';
        $qrcode_param_2 = isset($this->request_common_array['qrcode_param_2']) ? $this->request_common_array['qrcode_param_2'] : '';
        $platform_param_1 = isset($this->request_common_array['platform_param_1']) ? $this->request_common_array['platform_param_1'] : '';
        $platform_param_2 = isset($this->request_common_array['platform_param_2']) ? $this->request_common_array['platform_param_2'] : '';
        $platform_param_3 = isset($this->request_common_array['platform_param_3']) ? $this->request_common_array['platform_param_3'] : '';
        $res = $this->service->updateShopShareCinfig($shop_id, $goods_param_1, $goods_param_2, $shop_param_1, $shop_param_2, $shop_param_3, $qrcode_param_1, $qrcode_param_2,$platform_param_1,$platform_param_2,$platform_param_3);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 店铺收益账单列表
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
    */
    public function getShopAccountList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopAccountList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺提现列表
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
    */
    public function getShopAccountWithdrawList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopAccountWithdrawList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺提现账户
     *
     * @param unknown $condition
    */
    public function getShopBankAccountAll(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopBankAccountAll($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加店铺银行账户
     *
     * @param unknown $shop
     * @param unknown $bank_type
     * @param unknown $branch_bank_name
     * @param unknown $realname
     * @param unknown $account_number
     * @param unknown $mobile
    */
    public function addShopBankAccount(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $bank_type = isset($this->request_common_array['bank_type']) ? $this->request_common_array['bank_type'] : '';
        $branch_bank_name = isset($this->request_common_array['branch_bank_name']) ? $this->request_common_array['branch_bank_name'] : '';
        $realname = isset($this->request_common_array['realname']) ? $this->request_common_array['realname'] : '';
        $account_number = isset($this->request_common_array['account_number']) ? $this->request_common_array['account_number'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $res = $this->service->addShopBankAccount($shop_id, $bank_type, $branch_bank_name, $realname, $account_number, $mobile);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改店铺银行账户
     *
     * @param unknown $shop
     * @param unknown $bank_type
     * @param unknown $branch_bank_name
     * @param unknown $realname
     * @param unknown $account_number
     * @param unknown $mobile
     * @param unknown $id
    */
    public function updateShopBankAccount(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $bank_type = isset($this->request_common_array['bank_type']) ? $this->request_common_array['bank_type'] : '';
        $branch_bank_name = isset($this->request_common_array['branch_bank_name']) ? $this->request_common_array['branch_bank_name'] : '';
        $realname = isset($this->request_common_array['realname']) ? $this->request_common_array['realname'] : '';
        $account_number = isset($this->request_common_array['account_number']) ? $this->request_common_array['account_number'] : '';
        $mobile = isset($this->request_common_array['mobile']) ? $this->request_common_array['mobile'] : '';
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->updateShopBankAccount($shop_id, $bank_type, $branch_bank_name, $realname, $account_number, $mobile, $id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改银行账户是否默认
     *
     * @param unknown $shop
     * @param unknown $id
     * @param unknown $is_default
    */
    public function modifyShopBankAccountIsdefault(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->modifyShopBankAccountIsdefault($shop_id, $id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除店铺银行账户
     *
     * @param unknown $shop_id
     * @param unknown $condition
    */
    public function deleteShopBankAccouht(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $res = $this->service->deleteShopBankAccouht($condition);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 店铺账户统计
     *
     * @param unknown $shop_id
    */
    public function getShopAccount(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopAccount($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺申请提现
     *
     * @param unknown $shop_id
     * @param unknown $withdraw_no
     * @param unknown $bank_account_id
     * @param unknown $cash
    */
    public function applyShopAccountWithdraw(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $bank_account_id = isset($this->request_common_array['bank_account_id']) ? $this->request_common_array['bank_account_id'] : '';
        $cash = isset($this->request_common_array['cash']) ? $this->request_common_array['cash'] : '';
        $res = $this->service->applyShopAccountWithdraw($shop_id, $bank_account_id, $cash);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 店铺提现 审核
     *
     * @param unknown $shop_id
     * @param unknown $id
     * @param unknown $status
    */
    public function shopAccountWithdrawAudit(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
        $res = $this->service->shopAccountWithdrawAudit($shop_id, $id, $status);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取银行账户详情
     *
     * @param unknown $shop_id
     * @param unknown $id
    */
    public function getShopBankAccountDetail(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getShopBankAccountDetail($shop_id, $id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺 月 账户记录
     *
     * @param unknown $shop_id
    */
    public function getShopAccountMonthRecord(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopAccountMonthRecord($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺账户统计列表
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getShopAccountCountList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopAccountCountList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺账户明细
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getShopAccountRecordsList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopAccountRecordsList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 店铺销售订单列表
     *
     * @param unknown $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getShopOrderAccountRecordsList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopOrderAccountRecordsList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取所有店铺
     *
     * @param unknown $condition
    */
    public function getShopAll(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopAll($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店铺账户记录统计
     *
     * @param unknown $condition
    */
    public function getShopAccountRecordCount(){
        $start_date = isset($this->request_common_array['start_date']) ? $this->request_common_array['start_date'] : '';
        $end_date = isset($this->request_common_array['end_date']) ? $this->request_common_array['end_date'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getShopAccountRecordCount($start_date, $end_date, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取店商品销售
     *
     * @param unknown $start_date
     * @param unknown $end_date
    */
    public function getShopAccountSales(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopAccountSales($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 商品销售统计
    */
    public function getShopGoodsSales(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopGoodsSales($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改平台提出比率
     *
     * @param unknown $shop_id
    */
    public function updateShopPlatformCommissionRate(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $shop_platform_commission_rate = isset($this->request_common_array['shop_platform_commission_rate']) ? $this->request_common_array['shop_platform_commission_rate'] : '';
        $res = $this->service->updateShopPlatformCommissionRate($shop_id, $shop_platform_commission_rate);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取店铺总数
     * @param unknown $condition
    */
    public function getShopCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加提现设置
     *
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
    */
    public function addMemberWithdrawSetting(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $withdraw_cash_min = isset($this->request_common_array['withdraw_cash_min']) ? $this->request_common_array['withdraw_cash_min'] : '';
        $withdraw_multiple = isset($this->request_common_array['withdraw_multiple']) ? $this->request_common_array['withdraw_multiple'] : '';
        $withdraw_poundage = isset($this->request_common_array['withdraw_poundage']) ? $this->request_common_array['withdraw_poundage'] : '';
        $withdraw_message = isset($this->request_common_array['withdraw_message']) ? $this->request_common_array['withdraw_message'] : '';
        $withdraw_account_type = isset($this->request_common_array['withdraw_account_type']) ? $this->request_common_array['withdraw_account_type'] : '';
        $res = $this->service->addMemberWithdrawSetting($shop_id, $withdraw_cash_min, $withdraw_multiple, $withdraw_poundage, $withdraw_message, $withdraw_account_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 店铺提现金额
     * @param unknown $condition
    */
    public function getShopWithdrawCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopWithdrawCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 修改提现设置
     *
     * @param unknown $id
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
    */
    public function updateMemberWithdrawSetting(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $withdraw_cash_min = isset($this->request_common_array['withdraw_cash_min']) ? $this->request_common_array['withdraw_cash_min'] : '';
        $withdraw_multiple = isset($this->request_common_array['withdraw_multiple']) ? $this->request_common_array['withdraw_multiple'] : '';
        $withdraw_poundage = isset($this->request_common_array['withdraw_poundage']) ? $this->request_common_array['withdraw_poundage'] : '';
        $withdraw_message = isset($this->request_common_array['withdraw_message']) ? $this->request_common_array['withdraw_message'] : '';
        $withdraw_account_type = isset($this->request_common_array['withdraw_account_type']) ? $this->request_common_array['withdraw_account_type'] : '';
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->updateMemberWithdrawSetting($shop_id, $withdraw_cash_min, $withdraw_multiple, $withdraw_poundage, $withdraw_message, $withdraw_account_type, $id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取提现设置信息
     * @param unknown $shop_id
    */
    public function getWithdrawInfo(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getWithdrawInfo($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 修改店铺列表排序
     * @param unknown $shop_id
    */
    public function updateShopSort(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $shop_sort = isset($this->request_common_array['shop_sort']) ? $this->request_common_array['shop_sort'] : '';
        $res = $this->service->updateShopSort($shop_id,$shop_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 设置店铺推荐
     * @param unknown $shop_id
    */
    public function setRecomment(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $shop_recommend = isset($this->request_common_array['shop_recommend']) ? $this->request_common_array['shop_recommend'] : '';
        $res = $this->service->setRecomment($shop_id,$shop_recommend);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 设置店铺状态
     * @param unknown $shop_id
     * @param unknown $type
    */
    public function setStatus(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $res = $this->service->setRecomment($shop_id, $type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 查询导航的商城模块
    */
    public function getShopNavigationTemplate(){
        $use_type = isset($this->request_common_array['use_type']) ? $this->request_common_array['use_type'] : '';
        $retval = $this->service->getShopNavigationTemplate($use_type);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 自提点管理
     * @param unknown $shop_id
     * @param unknown $name
     * @param unknown $address
     * @param unknown $contact
     * @param unknown $phone
     * @param unknown $province_id
     * @param unknown $city_id
     * @param unknown $district_id
     * @param unknown $longitude
     * @param unknown $latitude
    */
    public function addPickupPoint(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $name = isset($this->request_common_array['name']) ? $this->request_common_array['name'] : '';
        $address = isset($this->request_common_array['address']) ? $this->request_common_array['address'] : '';
        $contact = isset($this->request_common_array['contact']) ? $this->request_common_array['contact'] : '';
        $phone = isset($this->request_common_array['phone']) ? $this->request_common_array['phone'] : '';
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $district_id = isset($this->request_common_array['district_id']) ? $this->request_common_array['district_id'] : '';
        $longitude = isset($this->request_common_array['longitude']) ? $this->request_common_array['longitude'] : '';
        $latitude = isset($this->request_common_array['latitude']) ? $this->request_common_array['latitude'] : '';
        $res = $this->service->addPickupPoint($shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, $longitude, $latitude);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改自提点
     * @param unknown $id
     * @param unknown $shop_id
     * @param unknown $name
     * @param unknown $address
     * @param unknown $contact
     * @param unknown $phone
     * @param unknown $province_id
     * @param unknown $city_id
     * @param unknown $district_id
     * @param unknown $longitude
     * @param unknown $latitude
    */
    public function updatePickupPoint(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $name = isset($this->request_common_array['name']) ? $this->request_common_array['name'] : '';
        $address = isset($this->request_common_array['address']) ? $this->request_common_array['address'] : '';
        $contact = isset($this->request_common_array['contact']) ? $this->request_common_array['contact'] : '';
        $phone = isset($this->request_common_array['phone']) ? $this->request_common_array['phone'] : '';
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $district_id = isset($this->request_common_array['district_id']) ? $this->request_common_array['district_id'] : '';
        $longitude = isset($this->request_common_array['longitude']) ? $this->request_common_array['longitude'] : '';
        $latitude = isset($this->request_common_array['latitude']) ? $this->request_common_array['latitude'] : '';
        $res = $this->service->updatePickupPoint($id, $shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, $longitude, $latitude);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取自提点列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
    */
    public function getPickupPointList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getPickupPointList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 删除自提点
     * @param unknown $pickip_id
    */
    public function deletePickupPoint(){
        $pickip_id = isset($this->request_common_array['pickip_id']) ? $this->request_common_array['pickip_id'] : '';
        $res = $this->service->deletePickupPoint($pickip_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取自提点详情
     * @param unknown $id
    */
    public function getPickupPointDetail(){
        $pickip_id = isset($this->request_common_array['pickip_id']) ? $this->request_common_array['pickip_id'] : '';
        $retval = $this->service->getPickupPointDetail($pickip_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 店铺订单记录分配
     * @param unknown $page_index
     * @param unknown $page_size
     * @param unknown $condition
     * @param unknown $order
    */
    public function getShopOrderReturnList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getShopOrderReturnList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 平台添加店铺
     * @param unknown $user_account
     * @param unknown $user_pwd
     * @param unknown $shop_name
     * @param unknown $shop_group_id
     * @param unknown $shop_type
    */
    public function addPlatformShop(){
        $user_account = isset($this->request_common_array['user_account']) ? $this->request_common_array['user_account'] : '';
        $user_pwd = isset($this->request_common_array['user_pwd']) ? $this->request_common_array['user_pwd'] : '';
        $shop_name = isset($this->request_common_array['shop_name']) ? $this->request_common_array['shop_name'] : '';
        $shop_group_id = isset($this->request_common_array['shop_group_id']) ? $this->request_common_array['shop_group_id'] : '';
        $shop_type = isset($this->request_common_array['shop_type']) ? $this->request_common_array['shop_type'] : '';
        $res = $this->service->addPlatformShop($user_account,$user_pwd,$shop_name,$shop_group_id,$shop_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getGuideList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : '';
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : '';
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getGuideList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加指南
    */
    public function addGuide(){
        $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $res = $this->service->addPlatformShop($title, $sort, $image, $content);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改指南
    */
    public function updateGuide(){
        $guide_id = isset($this->request_common_array['guide_id']) ? $this->request_common_array['guide_id'] : '';
        $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $res = $this->service->updateGuide($guide_id,$title,$sort,$image,$content);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取详情
     * @param unknown $guide_id
    */
    public function getGuideDetail(){
        $guide_id = isset($this->request_common_array['guide_id']) ? $this->request_common_array['guide_id'] : '';
        $retval = $this->service->getGuideDetail($guide_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 线下店铺
     * @param unknown $shop_id
     * @param unknown $shop_vrcode_prefix
     * @param unknown $live_store_name
     * @param unknown $live_store_tel
     * @param unknown $live_store_address
     * @param unknown $live_store_bus
    */
    public function updateShopOfflineStoreByshop(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $shop_vrcode_prefix = isset($this->request_common_array['shop_vrcode_prefix']) ? $this->request_common_array['shop_vrcode_prefix'] : '';
        $live_store_name = isset($this->request_common_array['live_store_name']) ? $this->request_common_array['live_store_name'] : '';
        $live_store_tel = isset($this->request_common_array['live_store_tel']) ? $this->request_common_array['live_store_tel'] : '';
        $live_store_address = isset($this->request_common_array['live_store_address']) ? $this->request_common_array['live_store_address'] : '';
        $live_store_bus = isset($this->request_common_array['live_store_bus']) ? $this->request_common_array['live_store_bus'] : '';
        $latitude_longitude = isset($this->request_common_array['latitude_longitude']) ? $this->request_common_array['latitude_longitude'] : '';
        $res = $this->service->updateShopOfflineStoreByshop($shop_id, $shop_vrcode_prefix, $live_store_name, $live_store_tel, $live_store_address, $live_store_bus,$latitude_longitude);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}