<?php
/**
 * GoodsGroup.php
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
use data\service\GoodsGroup as GoodsGroupService;


class GoodsGroup extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new GoodsGroupService();
    }
    
    /**
     * 获取商品分组列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    public function getGoodsGroupList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getGoodsGroupList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加或修改商品分组
     *  group_id int(11) NOT NULL AUTO_INCREMENT,
     shop_id int(11) NOT NULL,
     group_name varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
     pid int(11) NOT NULL DEFAULT 0 COMMENT '上级id 最多2级',
     level tinyint(4) NOT NULL DEFAULT 0 COMMENT '级别',
     is_visible bit(1) NOT NULL DEFAULT b'1' COMMENT '是否可视',
     sort tinyint(4) NOT NULL DEFAULT 0 COMMENT '排序',
    */
    public function addOrEditGoodsGroup(){
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $group_name = isset($this->request_common_array['group_name']) ? $this->request_common_array['group_name'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $is_visible = isset($this->request_common_array['is_visible']) ? $this->request_common_array['is_visible'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $group_pic = isset($this->request_common_array['group_pic']) ? $this->request_common_array['group_pic'] : '';
        $res = $this->service->addOrEditGoodsGroup($group_id, $shop_id, $group_name, $pid, $is_visible, $sort, $group_pic);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除商品分组
     * @param unknown $goods_group_id_array
    */
    public function deleteGoodsGroup(){
        $goods_group_id_array = isset($this->request_common_array['goods_group_id_array']) ? $this->request_common_array['goods_group_id_array'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $res = $this->service->deleteGoodsGroup($goods_group_id_array, $shop_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取商品分组的子分类(一级)
    */
    public function getGoodsGroupListByParentId(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $retval = $this->service->getGoodsGroupListByParentId($shop_id, $pid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取分组详情
     * @param unknown $group_id
    */
    public function getGoodsGroupDetail(){
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $retval = $this->service->getGoodsGroupDetail($group_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 修改 商品分组 单个字段
    */
    public function ModifyGoodsGroupField(){
        $group_id = isset($this->request_common_array['group_id']) ? $this->request_common_array['group_id'] : '';
        $field_name = isset($this->request_common_array['field_name']) ? $this->request_common_array['field_name'] : '';
        $field_value = isset($this->request_common_array['field_value']) ? $this->request_common_array['field_value'] : '';
        $res = $this->service->ModifyGoodsGroupField($group_id, $field_name, $field_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 返回二级 分组 列表
     * @param unknown $shop_id
    */
    public function getGoodsGroupQuery(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getGoodsGroupQuery($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询分组商品列表数据结构
     * @param unknown $shop_id
    */
    public function getGroupGoodsTree(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getGroupGoodsTree($shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     *查询商品分组
     * @param unknown $condition
    */
    public function getGoodsGroupQueryList(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsGroupQueryList($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}