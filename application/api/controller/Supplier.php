<?php
/**
 * Supplier.php
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
use data\service\Supplier as SupplierService;


class Supplier extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new SupplierService();
    }
    
    /**
     * 供货商列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    public function getSupplierList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = ''){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 1;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getSupplierList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加供货商
     * @param unknown $uid
     * @param unknown $supplier_name
     * @param unknown $desc
    */
    public function addSupplier(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $supplier_name = isset($this->request_common_array['supplier_name']) ? $this->request_common_array['supplier_name'] : '';
        $linkman_name = isset($this->request_common_array['linkman_name']) ? $this->request_common_array['linkman_name'] : '';
        $linkman_tel = isset($this->request_common_array['linkman_tel']) ? $this->request_common_array['linkman_tel'] : '';
        $linkman_address = isset($this->request_common_array['linkman_address']) ? $this->request_common_array['linkman_address'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $res = $this->service->addSupplier($uid, $supplier_name, $linkman_name, $linkman_tel, $linkman_address, $desc);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改供货商
     * @param unknown $supplier_id
     * @param unknown $supplier_name
     * @param unknown $desc
    */
    public function updateSupplier(){
        $supplier_id = isset($this->request_common_array['supplier_id']) ? $this->request_common_array['supplier_id'] : '';
        $supplier_name = isset($this->request_common_array['supplier_name']) ? $this->request_common_array['supplier_name'] : '';
        $linkman_name = isset($this->request_common_array['linkman_name']) ? $this->request_common_array['linkman_name'] : '';
        $linkman_tel = isset($this->request_common_array['linkman_tel']) ? $this->request_common_array['linkman_tel'] : '';
        $linkman_address = isset($this->request_common_array['linkman_address']) ? $this->request_common_array['linkman_address'] : '';
        $desc = isset($this->request_common_array['desc']) ? $this->request_common_array['desc'] : '';
        $res = $this->service->addSupplier($supplier_id, $supplier_name, $linkman_name, $linkman_tel, $linkman_address, $desc);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除供货商
     * @param unknown $supplier_id
    */
    public function deleteSupplier(){
        $supplier_id = isset($this->request_common_array['supplier_id']) ? $this->request_common_array['supplier_id'] : '';
        $res = $this->service->deleteSupplier($supplier_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取单条供货商详情
     * @param unknown $supplier_id
    */
    public function getSupplierInfo(){
        $supplier_id = isset($this->request_common_array['supplier_id']) ? $this->request_common_array['supplier_id'] : '';
        $retval = $this->service->getSupplierInfo($supplier_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}