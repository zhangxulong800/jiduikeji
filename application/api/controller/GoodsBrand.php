<?php
/**
 * GoodsBrand.php
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
use data\service\GoodsBrand as GoodsBrandService;


class GoodsBrand extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new GoodsBrandService();
    }
    
    /**
     * 获取商品品牌列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    public function getGoodsBrandList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getGoodsBrandList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加或修改品牌
     * @param unknown $brand_id
     * @param unknown $shop_id
     * @param unknown $brand_name
     * @param unknown $brand_initial
     * @param unknown $brand_class
     * @param unknown $brand_pic
     * @param unknown $brand_recommend
     * @param unknown $sort
     * @param unknown $brand_category_name
    */
    public function addOrUpdateGoodsBrand(){
        $brand_id = isset($this->request_common_array['brand_id']) ? $this->request_common_array['brand_id'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $brand_name = isset($this->request_common_array['brand_name']) ? $this->request_common_array['brand_name'] : '';
        $brand_initial = isset($this->request_common_array['brand_initial']) ? $this->request_common_array['brand_initial'] : '';
        $brand_class = isset($this->request_common_array['brand_class']) ? $this->request_common_array['brand_class'] : '';
        $brand_pic = isset($this->request_common_array['brand_pic']) ? $this->request_common_array['brand_pic'] : '';
        $brand_recommend = isset($this->request_common_array['brand_recommend']) ? $this->request_common_array['brand_recommend'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $brand_category_name = isset($this->request_common_array['brand_category_name']) ? $this->request_common_array['brand_category_name'] : '';
        $category_id_array = isset($this->request_common_array['category_id_array']) ? $this->request_common_array['category_id_array'] : '';
        $brand_ads = isset($this->request_common_array['brand_ads']) ? $this->request_common_array['brand_ads'] : '';
        $category_name = isset($this->request_common_array['category_name']) ? $this->request_common_array['category_name'] : '';
        $category_id_1 = isset($this->request_common_array['category_id_1']) ? $this->request_common_array['category_id_1'] : '';
        $category_id_2 = isset($this->request_common_array['category_id_2']) ? $this->request_common_array['category_id_2'] : '';
        $category_id_3 = isset($this->request_common_array['category_id_3']) ? $this->request_common_array['category_id_3'] : '';
        $res = $this->service->addOrUpdateGoodsBrand($brand_id, $shop_id, $brand_name, $brand_initial, $brand_class, $brand_pic, $brand_recommend, $sort, $brand_category_name, $category_id_array, $brand_ads, $category_name, $category_id_1, $category_id_2, $category_id_3);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改品牌排序号
     * @param unknown $brand_id
     * @param unknown $sort
    */
    public function ModifyGoodsBrandSort(){
        $brand_id = isset($this->request_common_array['brand_id']) ? $this->request_common_array['brand_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addOrupdateCity($brand_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改品牌推荐
     * @param unknown $brand_id
     * @param unknown $brand_recommend
    */
    public function ModifyGoodsBrandRecomend(){
        $brand_id = isset($this->request_common_array['brand_id']) ? $this->request_common_array['brand_id'] : '';
        $brand_recommend = isset($this->request_common_array['brand_recommend']) ? $this->request_common_array['brand_recommend'] : '';
        $res = $this->service->ModifyGoodsBrandRecomend($brand_id, $brand_recommend);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除品牌
     * @param unknown $brand_id_array
    */
    public function deleteGoodsBrand(){
        $brand_id_array = isset($this->request_common_array['brand_id_array']) ? $this->request_common_array['brand_id_array'] : '';
        $res = $this->service->deleteGoodsBrand($brand_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}