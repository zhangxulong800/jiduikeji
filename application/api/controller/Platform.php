<?php
/**
 * Platform.php
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
use data\service\Platform as PlatformService;


class Platform extends BaseController
{
    public function __construct(){
        parent::__construct();
        $this->service = new PlatformService();
    }
    
    /**
     * 获取友情链接
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
     */
    public function getLinkList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getLinkList($page_index, $page_size, $where, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取友情链接详情
     * @param unknown $link_id
    */
    public function getLinkDetail(){
        $link_id = isset($this->request_common_array['link_id']) ? $this->request_common_array['link_id'] : '';
        $retval = $this->service->getLinkDetail($link_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加友情链接
     * @param unknown $link_title
     * @param unknown $link_url
     * @param unknown $link_pic
     * @param unknown $link_sort
    */
    public function addLink(){
        $link_title = isset($this->request_common_array['link_title']) ? $this->request_common_array['link_title'] : '';
        $link_url = isset($this->request_common_array['link_url']) ? $this->request_common_array['link_url'] : '';
        $link_pic = isset($this->request_common_array['link_pic']) ? $this->request_common_array['link_pic'] : '';
        $link_sort = isset($this->request_common_array['link_sort']) ? $this->request_common_array['link_sort'] : '';
        $is_blank = isset($this->request_common_array['is_blank']) ? $this->request_common_array['is_blank'] : '';
        $is_show = isset($this->request_common_array['is_show']) ? $this->request_common_array['is_show'] : '';
        $res = $this->service->addLink($link_title, $link_url, $link_pic, $link_sort, $is_blank, $is_show);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改友情链接
     * @param unknown $link_id
     * @param unknown $link_title
     * @param unknown $link_url
     * @param unknown $link_pic
     * @param unknown $link_sort
    */
    public function updateLink(){
        $link_id = isset($this->request_common_array['link_id']) ? $this->request_common_array['link_id'] : '';
        $link_title = isset($this->request_common_array['link_title']) ? $this->request_common_array['link_title'] : '';
        $link_url = isset($this->request_common_array['link_url']) ? $this->request_common_array['link_url'] : '';
        $link_pic = isset($this->request_common_array['link_pic']) ? $this->request_common_array['link_pic'] : '';
        $link_sort = isset($this->request_common_array['link_sort']) ? $this->request_common_array['link_sort'] : '';
        $is_blank = isset($this->request_common_array['is_blank']) ? $this->request_common_array['is_blank'] : '';
        $is_show = isset($this->request_common_array['is_show']) ? $this->request_common_array['is_show'] : '';
        $res = $this->service->addLink($link_id, $link_title, $link_url, $link_pic, $link_sort, $is_blank, $is_show);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除友情链接
     * @param unknown $link_id
    */
    public function deleteLink(){
        $link_id = isset($this->request_common_array['link_id']) ? $this->request_common_array['link_id'] : '';
        $res = $this->service->deleteLink($link_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取系统广告
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function getAdList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getAdList($page_index, $page_size, $where, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加系统广告
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
    */
    public function addAd(){
        $ad_image = isset($this->request_common_array['ad_image']) ? $this->request_common_array['ad_image'] : '';
        $link_url = isset($this->request_common_array['link_url']) ? $this->request_common_array['link_url'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addAd($ad_image, $link_url, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改商城广告
     * @param unknown $id
     * @param unknown $ad_image
     * @param unknown $link_url
     * @param unknown $sort
    */
    public function updateAd(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $ad_image = isset($this->request_common_array['ad_image']) ? $this->request_common_array['ad_image'] : '';
        $link_url = isset($this->request_common_array['link_url']) ? $this->request_common_array['link_url'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->updateAd($id, $ad_image, $link_url, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取商城广告详情
     * @param unknown $id
    */
    public function getAdDetail(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getAdDetail($id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 删除商城广告
     * @param unknown $id
    */
    public function delAd(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->delAd($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 首页板块列表（不含详情）
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function webBlockList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->webBlockList($page_index, $page_size, $where, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 版块列表 （包含详情）
    */
    public function getWebBlockListDetail(){
        $retval = $this->service->getWebBlockListDetail();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加首页板块
    */
    public function addWebBlock(){
        $is_display = isset($this->request_common_array['is_display']) ? $this->request_common_array['is_display'] : '';
        $block_color = isset($this->request_common_array['block_color']) ? $this->request_common_array['block_color'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $block_name = isset($this->request_common_array['block_name']) ? $this->request_common_array['block_name'] : '';
        $block_short_name = isset($this->request_common_array['block_short_name']) ? $this->request_common_array['block_short_name'] : '';
        $recommend_ad_image_name = isset($this->request_common_array['recommend_ad_image_name']) ? $this->request_common_array['recommend_ad_image_name'] : '';
        $recommend_ad_image = isset($this->request_common_array['recommend_ad_image']) ? $this->request_common_array['recommend_ad_image'] : '';
        $recommend_ad_slide_name = isset($this->request_common_array['recommend_ad_slide_name']) ? $this->request_common_array['recommend_ad_slide_name'] : '';
        $recommend_ad_slide = isset($this->request_common_array['recommend_ad_slide']) ? $this->request_common_array['recommend_ad_slide'] : '';
        $recommend_ad_images_name = isset($this->request_common_array['recommend_ad_images_name']) ? $this->request_common_array['recommend_ad_images_name'] : '';
        $recommend_ad_images = isset($this->request_common_array['recommend_ad_images']) ? $this->request_common_array['recommend_ad_images'] : '';
        $recommend_brands = isset($this->request_common_array['recommend_brands']) ? $this->request_common_array['recommend_brands'] : '';
        $recommend_categorys = isset($this->request_common_array['recommend_categorys']) ? $this->request_common_array['recommend_categorys'] : '';
        $recommend_goods_category_name_1 = isset($this->request_common_array['recommend_goods_category_name_1']) ? $this->request_common_array['recommend_goods_category_name_1'] : '';
        $recommend_goods_category_1 = isset($this->request_common_array['recommend_goods_category_1']) ? $this->request_common_array['recommend_goods_category_1'] : '';
        $recommend_goods_category_name_2 = isset($this->request_common_array['recommend_goods_category_name_2']) ? $this->request_common_array['recommend_goods_category_name_2'] : '';
        $recommend_goods_category_2 = isset($this->request_common_array['recommend_goods_category_2']) ? $this->request_common_array['recommend_goods_category_2'] : '';
        $recommend_goods_category_name_3 = isset($this->request_common_array['recommend_goods_category_name_3']) ? $this->request_common_array['recommend_goods_category_name_3'] : '';
        $recommend_goods_category_3 = isset($this->request_common_array['recommend_goods_category_3']) ? $this->request_common_array['recommend_goods_category_3'] : '';
        $res = $this->service->addWebBlock($is_display, $block_color, $sort, $block_name, $block_short_name, $recommend_ad_image_name, $recommend_ad_image, $recommend_ad_slide_name, $recommend_ad_slide, $recommend_ad_images_name, $recommend_ad_images, $recommend_brands, $recommend_categorys, $recommend_goods_category_name_1, $recommend_goods_category_1, $recommend_goods_category_name_2, $recommend_goods_category_2, $recommend_goods_category_name_3, $recommend_goods_category_3);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改首页板块
    */
    public function updateWebBlock(){
        $block_id = isset($this->request_common_array['block_id']) ? $this->request_common_array['block_id'] : '';
        $is_display = isset($this->request_common_array['is_display']) ? $this->request_common_array['is_display'] : '';
        $block_color = isset($this->request_common_array['block_color']) ? $this->request_common_array['block_color'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $block_name = isset($this->request_common_array['block_name']) ? $this->request_common_array['block_name'] : '';
        $block_short_name = isset($this->request_common_array['block_short_name']) ? $this->request_common_array['block_short_name'] : '';
        $recommend_ad_image_name = isset($this->request_common_array['recommend_ad_image_name']) ? $this->request_common_array['recommend_ad_image_name'] : '';
        $recommend_ad_image = isset($this->request_common_array['recommend_ad_image']) ? $this->request_common_array['recommend_ad_image'] : '';
        $recommend_ad_slide_name = isset($this->request_common_array['recommend_ad_slide_name']) ? $this->request_common_array['recommend_ad_slide_name'] : '';
        $recommend_ad_slide = isset($this->request_common_array['recommend_ad_slide']) ? $this->request_common_array['recommend_ad_slide'] : '';
        $recommend_ad_images_name = isset($this->request_common_array['recommend_ad_images_name']) ? $this->request_common_array['recommend_ad_images_name'] : '';
        $recommend_ad_images = isset($this->request_common_array['recommend_ad_images']) ? $this->request_common_array['recommend_ad_images'] : '';
        $recommend_brands = isset($this->request_common_array['recommend_brands']) ? $this->request_common_array['recommend_brands'] : '';
        $recommend_categorys = isset($this->request_common_array['recommend_categorys']) ? $this->request_common_array['recommend_categorys'] : '';
        $recommend_goods_category_name_1 = isset($this->request_common_array['recommend_goods_category_name_1']) ? $this->request_common_array['recommend_goods_category_name_1'] : '';
        $recommend_goods_category_1 = isset($this->request_common_array['recommend_goods_category_1']) ? $this->request_common_array['recommend_goods_category_1'] : '';
        $recommend_goods_category_name_2 = isset($this->request_common_array['recommend_goods_category_name_2']) ? $this->request_common_array['recommend_goods_category_name_2'] : '';
        $recommend_goods_category_2 = isset($this->request_common_array['recommend_goods_category_2']) ? $this->request_common_array['recommend_goods_category_2'] : '';
        $recommend_goods_category_name_3 = isset($this->request_common_array['recommend_goods_category_name_3']) ? $this->request_common_array['recommend_goods_category_name_3'] : '';
        $recommend_goods_category_3 = isset($this->request_common_array['recommend_goods_category_3']) ? $this->request_common_array['recommend_goods_category_3'] : '';
        $res = $this->service->updateWebBlock($block_id, $is_display, $block_color, $sort, $block_name, $block_short_name, $recommend_ad_image_name, $recommend_ad_image, $recommend_ad_slide_name, $recommend_ad_slide, $recommend_ad_images_name, $recommend_ad_images, $recommend_brands, $recommend_categorys, $recommend_goods_category_name_1, $recommend_goods_category_1, $recommend_goods_category_name_2, $recommend_goods_category_2, $recommend_goods_category_name_3, $recommend_goods_category_3);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除首页板块
    */
    public function deleteWebBlock(){
        $block_id = isset($this->request_common_array['block_id']) ? $this->request_common_array['block_id'] : '';
        $res = $this->service->deleteWebBlock($block_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取板块详情
    */
    public function getWebBlockDetail(){
        $block_id = isset($this->request_common_array['block_id']) ? $this->request_common_array['block_id'] : '';
        $retval = $this->service->getWebBlockDetail($block_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取广告位列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function getPlatformAdvPositionList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getPlatformAdvPositionList($page_index, $page_size, $where, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取广告位详情
     * @param unknown $ap_id
    */
    public function getPlatformAdvPositionDetail(){
        $ap_id = isset($this->request_common_array['ap_id']) ? $this->request_common_array['ap_id'] : '';
        $retval = $this->service->getPlatformAdvPositionDetail($ap_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 删除广告位
     * @param unknown $ap_id
    */
    public function delPlatfromAdvPosition(){
        $ap_id = isset($this->request_common_array['ap_id']) ? $this->request_common_array['ap_id'] : '';
        $res = $this->service->delPlatfromAdvPosition($ap_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加广告位
     * @param unknown $ap_name
     * @param unknown $ap_intro
     * @param unknown $ap_class
     * @param unknown $ap_display
     * @param unknown $is_use
     * @param unknown $ap_height
     * @param unknown $ap_width
     * @param unknown $default_content
    */
    public function addPlatformAdvPosition(){
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $ap_name = isset($this->request_common_array['ap_name']) ? $this->request_common_array['ap_name'] : '';
        $ap_intro = isset($this->request_common_array['ap_intro']) ? $this->request_common_array['ap_intro'] : '';
        $ap_class = isset($this->request_common_array['ap_class']) ? $this->request_common_array['ap_class'] : '';
        $ap_display = isset($this->request_common_array['ap_display']) ? $this->request_common_array['ap_display'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $ap_height = isset($this->request_common_array['ap_height']) ? $this->request_common_array['ap_height'] : '';
        $ap_width = isset($this->request_common_array['ap_width']) ? $this->request_common_array['ap_width'] : '';
        $default_content = isset($this->request_common_array['default_content']) ? $this->request_common_array['default_content'] : '';
        $ap_background_color = isset($this->request_common_array['ap_background_color']) ? $this->request_common_array['ap_background_color'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $res = $this->service->addPlatformAdvPosition($instance_id, $ap_name, $ap_intro, $ap_class, $ap_display, $is_use, $ap_height, $ap_width, $default_content, $ap_background_color, $type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改广告位
     * @param unknown $ap_id
     * @param unknown $ap_name
     * @param unknown $ap_intro
     * @param unknown $ap_class
     * @param unknown $ap_display
     * @param unknown $is_use
     * @param unknown $ap_height
     * @param unknown $ap_width
     * @param unknown $default_content
    */
    public function updatePlatformAdvPosition(){
        $ap_id = isset($this->request_common_array['ap_id']) ? $this->request_common_array['ap_id'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
        $ap_name = isset($this->request_common_array['ap_name']) ? $this->request_common_array['ap_name'] : '';
        $ap_intro = isset($this->request_common_array['ap_intro']) ? $this->request_common_array['ap_intro'] : '';
        $ap_class = isset($this->request_common_array['ap_class']) ? $this->request_common_array['ap_class'] : '';
        $ap_display = isset($this->request_common_array['ap_display']) ? $this->request_common_array['ap_display'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $ap_height = isset($this->request_common_array['ap_height']) ? $this->request_common_array['ap_height'] : '';
        $ap_width = isset($this->request_common_array['ap_width']) ? $this->request_common_array['ap_width'] : '';
        $default_content = isset($this->request_common_array['default_content']) ? $this->request_common_array['default_content'] : '';
        $ap_background_color = isset($this->request_common_array['ap_background_color']) ? $this->request_common_array['ap_background_color'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $res = $this->service->updatePlatformAdvPosition($ap_id, $instance_id, $ap_name, $ap_intro, $ap_class, $ap_display, $is_use, $ap_height, $ap_width, $default_content, $ap_background_color, $type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取平台广告列表
     * @param unknown $page_index
     * @param number $page_size
     * @param string $order
     * @param string $where
    */
    public function getPlatformAdvList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getPlatformAdvList($page_index, $page_size, $where, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取广告详情
     * @param unknown $adv_id
    */
    public function getPlatformAdDetail(){
        $adv_id = isset($this->request_common_array['adv_id']) ? $this->request_common_array['adv_id'] : '';
        $retval = $this->service->getPlatformAdDetail($adv_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加平台广告
     * @param unknown $ap_id
     * @param unknown $adv_title
     * @param unknown $adv_url
     * @param unknown $adv_image
     * @param unknown $slide_sort
    */
    public function addPlatformAdv(){
        $ap_id = isset($this->request_common_array['ap_id']) ? $this->request_common_array['ap_id'] : '';
        $adv_title = isset($this->request_common_array['adv_title']) ? $this->request_common_array['adv_title'] : '';
        $adv_url = isset($this->request_common_array['adv_url']) ? $this->request_common_array['adv_url'] : '';
        $adv_image = isset($this->request_common_array['adv_image']) ? $this->request_common_array['adv_image'] : '';
        $slide_sort = isset($this->request_common_array['slide_sort']) ? $this->request_common_array['slide_sort'] : '';
        $background = isset($this->request_common_array['background']) ? $this->request_common_array['background'] : '';
        $res = $this->service->addPlatformAdv($ap_id, $adv_title, $adv_url, $adv_image, $slide_sort, $background);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改平台广告
     * @param unknown $adv_id
     * @param unknown $ap_id
     * @param unknown $adv_title
     * @param unknown $adv_url
     * @param unknown $adv_image
     * @param unknown $slide_sort
    */
    public function updatePlatformAdv(){
        $adv_id = isset($this->request_common_array['adv_id']) ? $this->request_common_array['adv_id'] : '';
        $ap_id = isset($this->request_common_array['ap_id']) ? $this->request_common_array['ap_id'] : '';
        $adv_title = isset($this->request_common_array['adv_title']) ? $this->request_common_array['adv_title'] : '';
        $adv_url = isset($this->request_common_array['adv_url']) ? $this->request_common_array['adv_url'] : '';
        $adv_image = isset($this->request_common_array['adv_image']) ? $this->request_common_array['adv_image'] : '';
        $slide_sort = isset($this->request_common_array['slide_sort']) ? $this->request_common_array['slide_sort'] : '';
        $background = isset($this->request_common_array['background']) ? $this->request_common_array['background'] : '';
        $res = $this->service->addPlatformAdv($adv_id, $ap_id, $adv_title, $adv_url, $adv_image, $slide_sort, $background);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除平台广告
     * @param unknown $adv_id
    */
    public function deletePlatformAdv(){
        $adv_id = isset($this->request_common_array['adv_id']) ? $this->request_common_array['adv_id'] : '';
        $res = $this->service->deletePlatformAdv($adv_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 帮助中心类别列表
     * @param number $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
     * @param string $field
    */
    public function getPlatformHelpClassList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getPlatformHelpClassList($page_index, $page_size, $where, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 帮助中心内容列表
     * @param number $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
     * @param string $field
    */
    public function getPlatformHelpDocumentList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getPlatformHelpDocumentList($page_index, $page_size, $where, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 删除帮助分类
     * @param unknown $class_id
    */
    public public function deleteHelpClass(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $res = $this->service->deleteHelpClass($class_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 添加帮助中心分类
     * @param unknown $type
     * @param unknown $class_name
     * @param unknown $parent_class_id
     * @param unknown $sort
    */
    public function addPlatformHelpClass(){
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $class_name = isset($this->request_common_array['class_name']) ? $this->request_common_array['class_name'] : '';
        $parent_class_id = isset($this->request_common_array['parent_class_id']) ? $this->request_common_array['parent_class_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addPlatformHelpClass($type, $class_name, $parent_class_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改帮助中心分类
     * @param unknown $class_id
     * @param unknown $type
     * @param unknown $class_name
     * @param unknown $parent_class
     * @param unknown $sort
    */
    public function updatePlatformClass(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $class_name = isset($this->request_common_array['class_name']) ? $this->request_common_array['class_name'] : '';
        $parent_class_id = isset($this->request_common_array['parent_class_id']) ? $this->request_common_array['parent_class_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->updatePlatformClass($class_id, $type, $class_name, $parent_class_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 根据class_id删除内容
     * @param unknown $class_id
    */
    public function deleteHelpClassTitle(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $res = $this->service->deleteHelpClassTitle($class_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 添加帮助中心内容
     * @param unknown $uid
     * @param unknown $class_id
     * @param unknown $title
     * @param unknown $link_url
     * @param unknown $sort
     * @param unknown $content
     * @param unknown $image
    */
    public function addPlatformDocument(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
        $link_url = isset($this->request_common_array['link_url']) ? $this->request_common_array['link_url'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
        $res = $this->service->addPlatformDocument($uid, $class_id, $title, $link_url, $sort, $content, $image);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除帮助中心标题
     * @param unknown $id
    */
    public function deleteHelpTitle(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->deleteHelpTitle($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改帮助中心内容
     * @param unknown $id
     * @param unknown $uid
     * @param unknown $class_id
     * @param unknown $title
     * @param unknown $link_url
     * @param unknown $sort
     * @param unknown $content
     * @param unknown $image
    */
    public function updatePlatformDocument(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
        $link_url = isset($this->request_common_array['link_url']) ? $this->request_common_array['link_url'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
        $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
        $res = $this->service->updatePlatformDocument($id, $uid, $class_id, $title, $link_url, $sort, $content, $image);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取帮助中心内容详情
     * @param unknown $id
    */
    public function getPlatformDocumentDetail(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $retval = $this->service->getPlatformDocumentDetail($id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取帮助类型细节
     * @param unknown $class_id
    */
    public function getPlatformClassDetail(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $retval = $this->service->getPlatformClassDetail($class_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取平台商品
     * @param number $page_index
     * @param number $page_size
     * @param string $where
     * @param string $order
    */
    public function getPlatformGoodsList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getPlatformGoodsList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取平台商品推荐
     * @param unknown $type 类型1.新品2.精品3.特卖（其他为用户自定义类型）
    */
    public function getPlatformGoodsRecommend(){
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $retval = $this->service->getPlatformGoodsRecommend($type);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询商品是否热卖（平台）
     * @param unknown $goods_id
    */
    public function getGoodsIshot(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsIshot($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询商品是否是新品（平台）
     * @param unknown $goods_id
    */
    public function getGoodsIsnew(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsIsnew($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询商品是否是精品
     * @param unknown $goods_id
    */
    public function getGoodsIsBest(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsIsBest($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 修改商品推荐类型
     * @param unknown $goods_id
     * @param unknown $type  1.新品2.精品3.热卖  （其他推荐类型根据设定）
     * @param unknown $is_recommend
    */
    public function modifyGoodsRecommend(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $is_recommend = isset($this->request_common_array['is_recommend']) ? $this->request_common_array['is_recommend'] : '';
        $res = $this->service->addOrupdateCity($goods_id, $type, $is_recommend);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 首页获取平台促销板块信息
    */
    public function getPlatformGoodsRecommendClass(){
        $retval = $this->service->getPlatformGoodsRecommendClass();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 店铺街获取平台促销版块
    */
    public function getshopPlatformGoodsRecommendClass(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getshopPlatformGoodsRecommendClass($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加促销板块
     * @param unknown $class_name
     * @param unknown $sort
    */
    public function addPlatformGoodsRecommendClass(){
        $class_name = isset($this->request_common_array['class_name']) ? $this->request_common_array['class_name'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addPlatformGoodsRecommendClass($class_name, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改商品促销板块
     * @param unknown $class_id
     * @param unknown $class_name
     * @param unknown $sort
    */
    public function updatePlatformGoodsRecommendClass(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $class_name = isset($this->request_common_array['class_name']) ? $this->request_common_array['class_name'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $goods_id_array = isset($this->request_common_array['goods_id_array']) ? $this->request_common_array['goods_id_array'] : '';
        $res = $this->service->updatePlatformGoodsRecommendClass($class_id, $class_name, $sort, $goods_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改促销板块排序
     * @param unknown $class_id
     * @param unknown $sort
    */
    public function modifyPlatformGoodsRecommendClassSort(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->modifyPlatformGoodsRecommendClassSort($class_id,$sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改促销板块名称
     * @param unknown $class_id
     * @param unknown $class_name
    */
    public function modifyPlatformGoodsRecommendClassName(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $class_name = isset($this->request_common_array['class_name']) ? $this->request_common_array['class_name'] : '';
        $res = $this->service->modifyPlatformGoodsRecommendClassName($class_id, $class_name);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取平台促销板块信息单条信息
     * @param unknown $class_id
    */
    public function getPlatformGoodsRecommendClassDetail(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $retval = $this->service->getPlatformGoodsRecommendClassDetail($class_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 删除平台促销板块
     * @param unknown $class_id
    */
    public function deletePlatformGoodsRecommendClass(){
        $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
        $res = $this->service->deletePlatformGoodsRecommendClass($class_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 平台统计
    */
    public function getAccountCount(){
        $retval = $this->service->getAccountCount();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 平台收入日统计
    */
    public function  getPlatformAccountMonthRecord(){
        $retval = $this->service->getPlatformAccountMonthRecord();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     *
    */
    public function getPlatformAccountRecordsList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $where = isset($this->request_common_array['where']) ? $this->request_common_array['where'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getPlatformAccountRecordsList($page_index, $page_size, $where, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 平台统计
     * @param unknown $start_date
     * @param unknown $end_date
    */
    public function getPlatformCount(){
        $start_date = isset($this->request_common_array['start_date']) ? $this->request_common_array['start_date'] : '';
        $end_date = isset($this->request_common_array['end_date']) ? $this->request_common_array['end_date'] : '';
        $retval = $this->service->getPlatformCount($start_date, $end_date);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 店铺销售排行
    */
    public function getShopSalesVolume(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getShopSalesVolume($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 商品销售排行
    */
    public function getGoodsSalesVolume(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsSalesVolume($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 修改   广告排序
     * @param unknown $adv_id
     * @param unknown $slide_sort
    */
    public function updateAdvSlideSort(){
        $adv_id = isset($this->request_common_array['adv_id']) ? $this->request_common_array['adv_id'] : '';
        $slide_sort = isset($this->request_common_array['slide_sort']) ? $this->request_common_array['slide_sort'] : '';
        $res = $this->service->updateAdvSlideSort($adv_id, $slide_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 设置广告位的启用和禁用
     * @param unknown $ap_id
    */
    public function setPlatformAdvPositionUse(){
        $ap_id = isset($this->request_common_array['ap_id']) ? $this->request_common_array['ap_id'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $res = $this->service->setPlatformAdvPositionUse($ap_id, $is_use);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 设置首页版本的显示与不显示
     * @param unknown $block_id
     * @param unknown $is_use
    */
    public function setWebBlockIsBlock(){
        $block_id = isset($this->request_common_array['block_id']) ? $this->request_common_array['block_id'] : '';
        $is_display = isset($this->request_common_array['is_display']) ? $this->request_common_array['is_display'] : '';
        $res = $this->service->setWebBlockIsBlock($block_id, $is_display);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}