<?php
/**
 * Address.php
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
use data\service\Address as AddressService;


class Address extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new AddressService();
    }
    
    /**
     * 获取区域列表
     */
    public function getAreaList(){
        $retval = $this->service->getAreaList();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取省列表
     *
     * @param number $Area_id            
     */
    public function getProvinceList()
    {
        $area_id = isset($this->request_common_array['area_id']) ? $this->request_common_array['area_id'] : 0;
        $retval = $this->service->getProvinceList($area_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 根据省id组、市id组查询地址信息，并整理
     * @param unknown $province_id_arr
     * @param unknown $city_id_arr
     */
    public function getAddressListById(){
        $province_id_arr = isset($this->request_common_array['province_id_arr']) ? $this->request_common_array['province_id_arr']: '';
        $city_id_arr = isset($this->request_common_array['city_id_arr']) ? $this->request_common_array['city_id_arr']: '';
        $retval = $this->service->getAddressListById($province_id_arr,$city_id_arr);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取市列表
     *
     * @param number $province_id            
     */
    public function getCityList(){
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : 0;
//         $province_id = isset($this->request_common_array['test']) ? $this->request_common_array['province_id', 0);
        $retval = $this->service->getCityList($province_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取区县列表
     *
     * @param number $city_id            
     */
    public function getDistrictList(){
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : 0;
        $retval = $this->service->getDistrictList($city_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取省名称
     *
     * @param unknown $province_id            
     */
    public function getProvinceName(){
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id']: '';
        $retval = $this->service->getProvinceName($province_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取市名称
     *
     * @param unknown $city_id            
     */
    public function getCityName(){
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id']: '';
        $retval = $this->service->getCityName($city_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取区县名称
     *
     * @param unknown $cistrict_id            
     */
    public function getDistrictName($district_id){
        $district_id = isset($this->request_common_array['district_id']) ? $this->request_common_array['district_id']: '';
        $retval = $this->service->getDistrictName($district_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取地区树
     */
    public function getAreaTree(){
        $getAreaTree = isset($this->request_common_array['getAreaTree']) ? $this->request_common_array['getAreaTree'] : '';
        $retval = $this->service->getAreaTree($getAreaTree);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 传入 省市县 获取 省市县 名称
     *
     * @param unknown $province_id            
     * @param unknown $city_id            
     * @param unknown $district_id            
     */
    public function getAddress(){
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id']: '';
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id']: '';
        $district_id = isset($this->request_common_array['district_id']) ? $this->request_common_array['district_id']: '';
        $retval = $this->service->getAddress($province_id, $city_id, $district_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取省id
     *
     * @param unknown $province_name            
     */
    function getProvinceId(){
        $province_name = isset($this->request_common_array['province_name']) ? $this->request_common_array['province_name'] : '';
        $retval = $this->service->getProvinceId($province_name);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 获取市id
     *
     * @param unknown $city_name            
     */
    function getCityId(){
        $city_name = isset($this->request_common_array['city_name']) ? $this->request_common_array['city_name']: '';
        $retval = $this->service->getCityId($city_name);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 添加市级地区
     */
    function addOrupdateCity(){
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $city_name = isset($this->request_common_array['city_name']) ? $this->request_common_array['city_name'] : '';
        $zipcode = isset($this->request_common_array['zipcode']) ? $this->request_common_array['zipcode'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addOrupdateCity($city_id, $province_id, $city_name, $zipcode, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 添加县级地区
     */
    function addOrupdateDistrict(){
        $district_id = isset($this->request_common_array['district_id']) ? $this->request_common_array['district_id'] : '';
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $district_name = isset($this->request_common_array['district_name']) ? $this->request_common_array['district_name'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addOrupdateDistrict($district_id, $city_id, $district_name, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 修改省级区域
     */
    function updateProvince(){
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $province_name = isset($this->request_common_array['province_name']) ? $this->request_common_array['province_name'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $area_id = isset($this->request_common_array['area_id']) ? $this->request_common_array['area_id'] : '';
        $res = $this->service->updateProvince($province_id, $province_name, $sort, $area_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 添加省级区域
     *
     * @param unknown $province_name            
     * @param unknown $sort            
     */
    public function addProvince(){
        $province_name = isset($this->request_common_array['province_name']) ? $this->request_common_array['province_name'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $area_id = isset($this->request_common_array['area_id']) ? $this->request_common_array['area_id'] : '';
        $res = $this->service->addProvince($province_name, $sort, $area_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 删除 省
     */
    public function deleteProvince(){
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $res = $this->service->deleteProvince($province_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 删除 市
     */
    public function deleteCity(){
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $res = $this->service->deleteCity($city_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 删除 县
     */
    public function deleteDistrict(){
        $district_id = isset($this->request_common_array['district_id']) ? $this->request_common_array['district_id'] : '';
        $res = $this->service->deleteDistrict($district_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 修改省市县的排序与名称
     */
    public function updateRegionNameAndRegionSort(){
        $upType = isset($this->request_common_array['upType']) ? $this->request_common_array['upType'] : '';
        $regionType = isset($this->request_common_array['regionType']) ? $this->request_common_array['regionType'] : '';
        $regionName = isset($this->request_common_array['regionName']) ? $this->request_common_array['regionName'] : '';
        $regionSort = isset($this->request_common_array['regionSort']) ? $this->request_common_array['regionSort'] : '';
        $regionId = isset($this->request_common_array['regionId']) ? $this->request_common_array['regionId'] : '';
        $res = $this->service->updateRegionNameAndRegionSort($upType, $regionType, $regionName, $regionSort, $regionId);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 通过省级id获取其下级的数量
     */
    public function getCityCountByProvinceId(){
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $retval = $this->service->getCityCountByProvinceId($province_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 通过市级id获取其下级的数量
     */
    public function getDistrictCountByCityId(){
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $retval = $this->service->getDistrictCountByCityId($city_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 添加或修改配送地区
     */
    public function addOrUpdateDistributionArea(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $district_id = isset($this->request_common_array['district_id']) ? $this->request_common_array['district_id'] : '';
        $res = $this->service->addOrUpdateDistributionArea($shop_id, $province_id, $city_id, $district_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }

    /**
     * 获取配送地区信息
     */
    public function getDistributionAreaInfo(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getDistributionAreaInfo($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }

    /**
     * 检测 配置地址是否 符合货到付款
     */
    public function getDistributionAreaIsUser(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $district_id = isset($this->request_common_array['district_id']) ? $this->request_common_array['district_id'] : '';
        $retval = $this->service->getDistributionAreaIsUser($shop_id, $province_id, $city_id, $district_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}