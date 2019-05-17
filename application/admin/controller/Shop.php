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
namespace app\admin\controller;

use data\service\Address;
use data\service\Shop as ShopService;

/**
 * 店铺设置控制器
 *
 * @author Administrator
 *        
 */
class Shop extends BaseController
{

    /**
     * 店铺基础设置
     */
    public function shopConfig()
    {
 
        $shop = new ShopService();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $shop_logo = isset($_POST['shop_logo']) ? $_POST['shop_logo'] : '';
            $shop_banner = isset($_POST['shop_banner']) ? $_POST['shop_banner'] : '';
            $shop_avatar = isset($_POST['shop_avatar']) ? $_POST['shop_avatar'] : '';
            $shop_qq = isset($_POST['shop_qq']) ? $_POST['shop_qq'] : '';
            $shop_ww = isset($_POST['shop_ww']) ? $_POST['shop_ww'] : '';
            $shop_phone = isset($_POST['shop_phone']) ? $_POST['shop_phone'] : '';
            $shop_keywords = isset($_POST['shop_keywords']) ? $_POST['shop_keywords'] : '';
            $shop_description = isset($_POST['shop_description']) ? $_POST['shop_description'] : '';
            $shop_hint = isset($_POST['shop_hint']) ? $_POST['shop_hint'] : '';
            $res = $shop->updateShopConfigByshop($shop_id, $shop_logo, $shop_banner, $shop_avatar, '', $shop_qq, $shop_ww, $shop_phone, $shop_keywords, $shop_description, $shop_hint);
            return AjaxReturn($res);
        }
        $shop_info = $shop->getShopDetail($this->instance_id);
        $this->assign('shop_info', $shop_info);
        return view($this->style . "Shop/shopConfig");
    }
    /**
     * 自提点列表
     */
    public function pickupPointList()
    {
        if (request()->isAjax()) {
            $shop = new ShopService();
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition = array(
                'name' => array(
                    'like',
                    '%' . $search_text . '%'
                ),
                'shop_id' => $this->instance_id
            );
            $result = $shop->getPickupPointList($page_index, $page_size, $condition, 'create_time asc');
            return $result;
        } else {
            return view($this->style . "Shop/sinceList");
        }
    }

    /**
     * 添加自提点
     */
    public function addPickupPoint()
    {
        if (request()->isAjax()) {
            $shop = new ShopService();
            $shop_id = $this->instance_id;
            $name = request()->post('name');
            $address = request()->post('address');
            $contact = request()->post('contact');
            $phone = request()->post('phone');
            $province_id = request()->post('province_id');
            $city_id = request()->post('city_id');
            $district_id = request()->post('district_id');
            $res = $shop->addPickupPoint($shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, '', '');
            return AjaxReturn($res);
        }
        return view($this->style . "Shop/addSince");
    }

    /**
     * 修改自提点
     */
    public function updatePickupPoint()
    {
        $pickip_id = isset($_GET['id']) ? $_GET['id'] : '';
        if (request()->isAjax()) {
            $shop = new ShopService();
            $id = request()->post('id');
            $shop_id = $this->instance_id;
            $name = request()->post('name');
            $address = request()->post('address');
            $contact = request()->post('contact');
            $phone = request()->post('phone');
            $province_id = request()->post('province_id');
            $city_id = request()->post('city_id');
            $district_id = request()->post('district_id');
            $res = $shop->updatePickupPoint($id, $shop_id, $name, $address, $contact, $phone, $province_id, $city_id, $district_id, '', '');
            return AjaxReturn($res);
        }
        $shop = new ShopService();
        $pickupPoint_detail = $shop->getPickupPointDetail($pickip_id);
        if(empty($pickupPoint_detail) || $pickupPoint_detail['shop_id'] != $this->instance_id)
        {
            $this->error("非法操作商家自提点!");
        }
        $this->assign('pickupPoint_detail', $pickupPoint_detail);
        $this->assign('pickip_id', $pickip_id);
        return view($this->style . "Shop/updatePickupPoint");
    }

    /**
     * 删除自提点
     */
    public function deletepickupPoint()
    {
        if (request()->isAjax()) {
            $pickip_id = request()->post('pickupPoint_id');
            $shop = new ShopService();
            $res = $shop->deletePickupPoint($pickip_id);
            return AjaxReturn($res);
        }
    }

    /**
     * 获取省列表
     */
    public function getProvince()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        return $province_list;
    }

    /**
     * 获取城市列表
     *
     * @return Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    public function getCity()
    {
        $address = new Address();
        $province_id = isset($_POST['province_id']) ? $_POST['province_id'] : 0;
        $city_list = $address->getCityList($province_id);
        return $city_list;
    }

    /**
     * 获取区域地址
     */
    public function getDistrict()
    {
        $address = new Address();
        $city_id = isset($_POST['city_id']) ? $_POST['city_id'] : 0;
        $district_list = $address->getDistrictList($city_id);
        return $district_list;
    }

    /**
     * 获取选择地址
     *
     * @return unknown
     */
    public function getSelectAddress()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        $province_id = isset($_POST['province_id']) ? $_POST['province_id'] : 0;
        $city_id = isset($_POST['city_id']) ? $_POST['city_id'] : 0;
        $city_list = $address->getCityList($province_id);
        $district_list = $address->getDistrictList($city_id);
        $data["province_list"] = $province_list;
        $data["city_list"] = $city_list;
        $data["district_list"] = $district_list;
        return $data;
    }
    /**
     * 线下店铺
     */
    public function offlineStore()
    {
        $shop = new ShopService();
        if (request()->isAjax()) {
            $shop_id = $this->instance_id;
            $shop_vrcode_prefix = request()->post('shop_vrcode_prefix');
            $live_store_name = request()->post('live_store_name');
            $live_store_tel = request()->post('live_store_tel');
            $live_store_address = request()->post('live_store_address');
            $live_store_bus = request()->post('live_store_bus');
            $latitude_longitude = request()->post('latitude_longitude');
            $res = $shop->updateShopOfflineStoreByshop($shop_id, $shop_vrcode_prefix, $live_store_name, $live_store_tel, $live_store_address, $live_store_bus,$latitude_longitude);
            return AjaxReturn($res);
        }
        $shop_info = $shop->getShopDetail($this->instance_id);
        $this->assign('shop_info', $shop_info['base_info']);
        return view($this->style . "Shop/offlineStore");
    }
    
    
    
}
