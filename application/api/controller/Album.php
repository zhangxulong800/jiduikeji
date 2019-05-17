<?php
/**
 * Album.php
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
use data\service\Album as AlbumService;


class Album extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new AlbumService();
    }
    
    /**
     * 获取相册列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    public function getAlbumClassList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getAlbumClassList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 创建相册
     * @param unknown $aclass_name
     * @param unknown $aclass_sort
     * @param number  $pid
     * @param string  $aclass_cover
     * @param number  $is_default
     * @param number  $instance_id
    */
    public function addAlbumClass(){
        $aclass_name = isset($this->request_common_array['aclass_name']) ? $this->request_common_array['aclass_name'] : '';
        $aclass_sort = isset($this->request_common_array['aclass_sort']) ? $this->request_common_array['aclass_sort'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : 0;
        $aclass_cover = isset($this->request_common_array['aclass_cover']) ? $this->request_common_array['aclass_cover'] : '';
        $is_default = isset($this->request_common_array['is_default']) ? $this->request_common_array['is_default'] : 0;
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : 1;
        $res = $this->service->addAlbumClass($aclass_name, $aclass_sort, $pid, $aclass_cover, $is_default, $instance_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 编辑相册
     * @param unknown $aclass_name
     * @param unknown $aclass_sort
     * @param number $pid
     * @param string $aclass_cover
     * @param number $is_default
    */
    public function updateAlbumClass(){
        $aclass_id = isset($this->request_common_array['aclass_id']) ? $this->request_common_array['aclass_id'] : '';
        $aclass_name = isset($this->request_common_array['aclass_name']) ? $this->request_common_array['aclass_name'] : '';
        $aclass_sort = isset($this->request_common_array['aclass_sort']) ? $this->request_common_array['aclass_sort'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : 0;
        $aclass_cover = isset($this->request_common_array['aclass_cover']) ? $this->request_common_array['aclass_cover'] : '';
        $is_default = isset($this->request_common_array['is_default']) ? $this->request_common_array['is_default'] : 0;
        $res = $this->service->updateAlbumClass($aclass_id, $aclass_name, $aclass_sort, $pid, $aclass_cover, $is_default);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 改变相册排序
     * @param unknown $aclass_id
     * @param unknown $aclass_sort
    */
    public function ModifyAlbumSort(){
        $aclass_id = isset($this->request_common_array['aclass_id']) ? $this->request_common_array['aclass_id'] : '';
        $aclass_sort = isset($this->request_common_array['aclass_sort']) ? $this->request_common_array['aclass_sort'] : '';
        $res = $this->service->ModifyAlbumSort($aclass_id, $aclass_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 改变相册上级
     * @param unknown $aclass_id
     * @param unknown $pid
    */
    public function ModifyAlbumPid(){
        $aclass_id = isset($this->request_common_array['aclass_id']) ? $this->request_common_array['aclass_id'] : '';
        $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
        $res = $this->service->ModifyAlbumPid($aclass_id, $pid);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除相册
     * @param unknown $aclass_id
    */
    public function deleteAlbumClass(){
        $aclass_id_arrray = isset($this->request_common_array['aclass_id_arrray']) ? $this->request_common_array['aclass_id_arrray'] : '';
        $res = $this->service->deleteAlbumClass($aclass_id_arrray);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取相册图片列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
    */
    public function getPictureList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getPictureList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 图片增加
     * @param unknown $pic_name  名称
     * @param unknown $pic_tag   标签
     * @param unknown $aclass_id  相册ID
     * @param unknown $pic_cover  图片路径
     * @param unknown $pic_size   大小
     * @param unknown $pic_spec   规格
     * @param unknown $pic_cover_big
     * @param unknown $pic_size_big
     * @param unknown $pic_spec_big
     * @param unknown $pic_cover_mid
     * @param unknown $pic_size_mid
     * @param unknown $pic_spec_mid
     * @param unknown $pic_cover_small
     * @param unknown $pic_size_small
     * @param unknown $pic_spec_small
     * @param unknown $pic_cover_micro
     * @param unknown $pic_size_micro
     * @param unknown $pic_spec_micro
     * @param unknown $instance_id
    */
    public function addPicture(){
        $pic_name = isset($this->request_common_array['pic_name']) ? $this->request_common_array['pic_name'] : '';
        $pic_tag = isset($this->request_common_array['pic_tag']) ? $this->request_common_array['pic_tag'] : '';
        $aclass_id = isset($this->request_common_array['aclass_id']) ? $this->request_common_array['aclass_id'] : '';
        $pic_cover = isset($this->request_common_array['pic_cover']) ? $this->request_common_array['pic_cover'] : '';
        $pic_size = isset($this->request_common_array['pic_size']) ? $this->request_common_array['pic_size'] : '';
        $pic_spec = isset($this->request_common_array['pic_spec']) ? $this->request_common_array['pic_spec'] : '';
        $pic_cover_big = isset($this->request_common_array['pic_cover_big']) ? $this->request_common_array['pic_cover_big'] : '';
        $pic_size_big = isset($this->request_common_array['pic_size_big']) ? $this->request_common_array['pic_size_big'] : '';
        $pic_spec_big = isset($this->request_common_array['pic_spec_big']) ? $this->request_common_array['pic_spec_big'] : '';
        $pic_cover_mid = isset($this->request_common_array['pic_cover_mid']) ? $this->request_common_array['pic_cover_mid'] : '';
        $pic_size_mid = isset($this->request_common_array['pic_size_mid']) ? $this->request_common_array['pic_size_mid'] : '';
        $pic_spec_mid = isset($this->request_common_array['pic_spec_mid']) ? $this->request_common_array['pic_spec_mid'] : '';
        $pic_cover_small = isset($this->request_common_array['pic_cover_small']) ? $this->request_common_array['pic_cover_small'] : '';
        $pic_size_small = isset($this->request_common_array['pic_size_small']) ? $this->request_common_array['pic_size_small'] : '';
        $pic_spec_small = isset($this->request_common_array['pic_spec_small']) ? $this->request_common_array['pic_spec_small'] : '';
        $pic_cover_micro = isset($this->request_common_array['pic_cover_micro']) ? $this->request_common_array['pic_cover_micro'] : '';
        $pic_size_micro = isset($this->request_common_array['pic_size_micro']) ? $this->request_common_array['pic_size_micro'] : '';
        $pic_spec_micro = isset($this->request_common_array['pic_spec_micro']) ? $this->request_common_array['pic_spec_micro'] : '';
        $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : 0;
        $res = $this->service->addPicture($pic_name, $pic_tag, $aclass_id, $pic_cover, $pic_size, $pic_spec, $pic_cover_big, $pic_size_big, $pic_spec_big, $pic_cover_mid, $pic_size_mid, $pic_spec_mid, $pic_cover_small, $pic_size_small, $pic_spec_small, $pic_cover_micro, $pic_size_micro, $pic_spec_micro, $instance_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 图片删除
     * @param unknown $pic_id
    */
    public function deletePicture(){
        $pic_id_array = isset($this->request_common_array['pic_id_array']) ? $this->request_common_array['pic_id_array'] : '';
        $res = $this->service->deletePicture($pic_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取相册详情
     * @param unknown $album_id
    */
    public function getAlbumClassDetail(){
        $album_id = isset($this->request_common_array['album_id']) ? $this->request_common_array['album_id'] : '';
        $retval = $this->service->getAlbumClassDetail($album_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取图片详情
     * @param unknown $pic_id
    */
    public function getAlbumDetail(){
        $pic_id = isset($this->request_common_array['pic_id']) ? $this->request_common_array['pic_id'] : '';
        $retval = $this->service->getAlbumDetail($pic_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     *获取所有相册
    */
    public function getAlbumClassAll(){
        $retval = $this->service->getAlbumClassAll();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 图片替换
     * @param unknown $pic_id
     * @param unknown $pic_name
     * @param unknown $pic_tag
     * @param unknown $aclass_id
     * @param unknown $pic_cover
     * @param unknown $pic_size
     * @param unknown $pic_spec
     * @param unknown $pic_cover_big
     * @param unknown $pic_size_big
     * @param unknown $pic_spec_big
     * @param unknown $pic_cover_mid
     * @param unknown $pic_size_mid
     * @param unknown $pic_spec_mid
     * @param unknown $pic_cover_small
     * @param unknown $pic_size_small
     * @param unknown $pic_spec_small
     * @param unknown $pic_cover_micro
     * @param unknown $pic_size_micro
     * @param unknown $pic_spec_micro
     * @param number $instance_id
    */
    public function ModifyAlbumPicture(){
        $pic_id = isset($this->request_common_array['pic_id']) ? $this->request_common_array['pic_id'] : '';
        $pic_cover = isset($this->request_common_array['pic_cover']) ? $this->request_common_array['pic_cover'] : '';
        $pic_size = isset($this->request_common_array['pic_size']) ? $this->request_common_array['pic_size'] : '';
        $pic_spec = isset($this->request_common_array['pic_spec']) ? $this->request_common_array['pic_spec'] : '';
        $pic_cover_big = isset($this->request_common_array['pic_cover_big']) ? $this->request_common_array['pic_cover_big'] : '';
        $pic_size_big = isset($this->request_common_array['pic_size_big']) ? $this->request_common_array['pic_size_big'] : '';
        $pic_spec_big = isset($this->request_common_array['pic_spec_big']) ? $this->request_common_array['pic_spec_big'] : '';
        $pic_cover_mid = isset($this->request_common_array['pic_cover_mid']) ? $this->request_common_array['pic_cover_mid'] : '';
        $pic_size_mid = isset($this->request_common_array['pic_size_mid']) ? $this->request_common_array['pic_size_mid'] : '';
        $pic_spec_mid = isset($this->request_common_array['pic_spec_mid']) ? $this->request_common_array['pic_spec_mid'] : '';
        $pic_cover_small = isset($this->request_common_array['pic_cover_small']) ? $this->request_common_array['pic_cover_small'] : '';
        $pic_size_small = isset($this->request_common_array['pic_size_small']) ? $this->request_common_array['pic_size_small'] : '';
        $pic_spec_small = isset($this->request_common_array['pic_spec_small']) ? $this->request_common_array['pic_spec_small'] : '';
        $pic_cover_micro = isset($this->request_common_array['pic_cover_micro']) ? $this->request_common_array['pic_cover_micro'] : '';
        $pic_size_micro = isset($this->request_common_array['pic_size_micro']) ? $this->request_common_array['pic_size_micro'] : '';
        $pic_spec_micro = isset($this->request_common_array['pic_spec_micro']) ? $this->request_common_array['pic_spec_micro'] : '';
        $res = $this->service->ModifyAlbumPicture($pic_id, $pic_cover, $pic_size, $pic_spec, $pic_cover_big, $pic_size_big,$pic_spec_big, $pic_cover_mid, $pic_size_mid, $pic_spec_mid, $pic_cover_small, $pic_size_small, $pic_spec_small, $pic_cover_micro, $pic_size_micro, $pic_spec_micro);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 图片名称修改
     * @param unknown $pic_id
     * @param unknown $pic_name
    */
    public function ModifyAlbumPictureName(){
        $pic_id = isset($this->request_common_array['pic_id']) ? $this->request_common_array['pic_id'] : '';
        $pic_name = isset($this->request_common_array['pic_name']) ? $this->request_common_array['pic_name'] : '';
        $res = $this->service->ModifyAlbumPictureName($pic_id, $pic_name);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 更改图片所在相册
     * @param unknown $pic_id
     * @param unknown $album_id
    */
    public function ModifyAlbumPictureClass(){
        $pic_id = isset($this->request_common_array['pic_id']) ? $this->request_common_array['pic_id'] : '';
        $album_id = isset($this->request_common_array['album_id']) ? $this->request_common_array['album_id'] : '';
        $res = $this->service->ModifyAlbumPictureClass($pic_id, $album_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 设此图片为本相册的封面
     * @param unknown $pic_id
     * @param unknown $album_id
    */
    public function ModifyAlbumClassCover(){
        $pic_id = isset($this->request_common_array['pic_id']) ? $this->request_common_array['pic_id'] : '';
        $album_id = isset($this->request_common_array['album_id']) ? $this->request_common_array['album_id'] : '';
        $res = $this->service->ModifyAlbumPictureClass($pic_id, $album_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取商品使用的图片空间
    */
    public function getGoodsUseAlbum(){
        $retval = $this->service->getGoodsUseAlbum();
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 判断图片是否已经被使用
     * return   true = 已被使用    false = 未使用
    */
    public function checkPictureIsUse(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $pic_id = isset($this->request_common_array['pic_id']) ? $this->request_common_array['pic_id'] : '';
        $retval = $this->service->checkPictureIsUse($shop_id, $pic_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取相册图片详情
     * @param unknown $condition
    */
    public function getAlubmPictureDetail(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getAlubmPictureDetail($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    public function getGoodsAlbumUsePictureQuery(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsAlbumUsePictureQuery($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
}