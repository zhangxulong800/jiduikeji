<?php
/**
 * GoodsCategory.php
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
use data\service\GoodsCategory as GoodsCategoryService;


class GoodsCategory extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new GoodsCategoryService();
    }
    
    /**
     * 商品分类
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    public function getGoodsCategoryList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getGoodsCategoryList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取商品分类的子分类(一级)
     * @param unknown $pid
    */
    public function getGoodsCategoryListByParentId(){
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $retval = $this->service->getGoodsCategoryListByParentId($pid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加或者修改商品分类信息
     * @param unknown $goods_classid  添加时$goods_classid=0
     * @param unknown $data
    */
    public function addOrEditGoodsCategory(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $category_name = isset($this->request_common_array['category_name']) ? $this->request_common_array['category_name'] : '';
        $short_name = isset($this->request_common_array['short_name']) ? $this->request_common_array['short_name'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $is_visible = isset($this->request_common_array['is_visible']) ? $this->request_common_array['is_visible'] : '';
        $keywords = isset($this->request_common_array['keywords']) ? $this->request_common_array['keywords'] : '';
        $description = isset($this->request_common_array['description']) ? $this->request_common_array['description'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : 0;
        $category_pic = isset($this->request_common_array['category_pic']) ? $this->request_common_array['category_pic'] : '';
        $attr_id = isset($this->request_common_array['attr_id']) ? $this->request_common_array['attr_id'] : 0;
        $attr_name = isset($this->request_common_array['attr_name']) ? $this->request_common_array['attr_name'] : '';
        $res = $this->service->addOrEditGoodsCategory($category_id, $category_name, $short_name, $pid, $is_visible, $keywords, $description, $sort, $category_pic, $attr_id, $attr_name);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除商品分类信息
     * @param unknown $goods_classid_array
    */
    public function deleteGoodsCategory(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $res = $this->service->deleteGoodsCategory($category_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取商品分类树形列表
     * @param unknown $show_deep  深度
     * @param unknown $condition  条件
    */
    public function getTreeCategoryList(){
        $show_deep = isset($this->request_common_array['show_deep']) ? $this->request_common_array['show_deep'] : '';
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getTreeCategoryList($show_deep, $condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取分类关键词
     * @param unknown $goods_classid
    */
    public function getKeyWords(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getKeyWords($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取指定商品分类的详情
     * @param unknown $goods_classid
    */
    public function getGoodsCategoryDetail(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getGoodsCategoryDetail($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取分类级次
     * @param unknown $category_id
    */
    public function getLevel(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getLevel($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取分类名称
     * @param unknown $category_id
    */
    public function getName(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getName($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取分类树，（暂时是查询两级）
    */
    public function getGoodsCategoryTree(){
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $retval = $this->service->getGoodsCategoryTree($pid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改单个 字段
    */
    public function ModifyGoodsCategoryField(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $field_name = isset($this->request_common_array['field_name']) ? $this->request_common_array['field_name'] : '';
        $field_value = isset($this->request_common_array['field_value']) ? $this->request_common_array['field_value'] : '';
        $res = $this->service->ModifyGoodsCategoryField($category_id, $field_name, $field_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取商品分类下的品牌列表
     * @param unknown $category_id
    */
    public function getGoodsCategoryBrands(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getGoodsCategoryBrands($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取商品分类下的价格区间
     * @param unknown $category_id
    */
    public function getGoodsCategoryPriceGrades(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getGoodsCategoryPriceGrades($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 计算商品分类销量
    */
    public function getGoodsCategorySaleNum(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getGoodsCategorySaleNum();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取商品分类的子项列
     * @param unknown $category_id
     * @return string|unknown
    */
    public function getCategoryTreeList(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getCategoryTreeList($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取分类的父级分类
     * @param unknown $category_id
    */
    public function getCategoryParentQuery(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getCategoryParentQuery($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 得到上级分类信息
     * @param unknown $category_id
    */
    public function getParentCategory(){
        $category_id = isset($this->request_common_array['category_id']) ? $this->request_common_array['category_id'] : '';
        $retval = $this->service->getParentCategory($category_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}