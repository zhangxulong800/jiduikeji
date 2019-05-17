<?php
/**
 * Goods.php
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
use data\service\Goods as GoodsService;
use app\SimpleController;

class Goods extends SimpleController
{
    function __construct(){
        parent::__construct();
        $this->service = new GoodsService();
    }
    
    /**
     * 获取指定条件下商品列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
    public function getGoodsList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getGoodsList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取某种条件下商品数量
     *
     * @param unknown $condition
    */
    public function getGoodsCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加或者修改商品(整体)
     * goods_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品id(SKU)',
     * goods_name varchar(50) NOT NULL COMMENT '商品名称',
     * shop_id int(10) UNSIGNED NOT NULL COMMENT '店铺id',
     * category_id int(10) UNSIGNED NOT NULL COMMENT '商品分类id',
     * category_id_1 int(10) UNSIGNED NOT NULL COMMENT '一级分类id',
     * category_id_2 int(10) UNSIGNED NOT NULL COMMENT '二级分类id',
     * category_id_3 int(10) UNSIGNED NOT NULL COMMENT '三级分类id',
     * brand_id int(10) UNSIGNED NOT NULL COMMENT '品牌id',
     * group_id_array varchar(255) NOT NULL DEFAULT '' COMMENT '店铺分类id 首尾用,隔开',
     * promotion_type tinyint(3) NOT NULL DEFAULT 0 COMMENT '促销类型 0无促销，1团购，2限时折扣',
     * goods_type tinyint(4) NOT NULL DEFAULT 1 COMMENT '实物或虚拟商品标志 1实物商品 0 虚拟商品 2 F码商品',
     * market_price decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '市场价',
     * price decimal(19, 2) NOT NULL DEFAULT 0.00 COMMENT '商品原价格',
     * promotion_price decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '商品促销价格',
     * cost_price decimal(19, 2) NOT NULL DEFAULT 0.00 COMMENT '成本价',
     * point_exchange_type enum ('0', '1', '2') NOT NULL COMMENT '积分兑换类型 0 非积分兑换 1 只能积分兑换 2 积分和商品价格组合购买',
     * point_exchange int(11) NOT NULL DEFAULT 0 COMMENT '积分兑换',
     * give_point int(11) NOT NULL DEFAULT 0 COMMENT '购买商品赠送积分',
     * is_member_discount bit(1) NOT NULL DEFAULT b'0' COMMENT '参与会员折扣',
     * shipping_fee decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '运费 0为免运费',
     * shipping_fee_id int(11) NOT NULL DEFAULT 0 COMMENT '售卖区域id 物流模板id ns_order_shipping_fee 表id',
     * stock int(10) NOT NULL DEFAULT 0 COMMENT '商品库存',
     * max_buy int(11) NOT NULL DEFAULT 0 COMMENT '限购 0 不限购',
     * min_stock_alarm int(11) NOT NULL DEFAULT 0 COMMENT '库存预警值',
     * clicks int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品点击数量',
     * sales int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '销售数量',
     * collects int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收藏数量',
     * star tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT '好评星级',
     * evaluates int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '评价数',
     * shares int(11) NOT NULL DEFAULT 0 COMMENT '分享数',
     * province_id int(10) UNSIGNED NOT NULL COMMENT '一级地区id',
     * city_id int(10) UNSIGNED NOT NULL COMMENT '二级地区id',
     * picture int(11) NOT NULL COMMENT '商品主图',
     * keywords varchar(255) NOT NULL DEFAULT '' COMMENT '商品关键词',
     * introduction varchar(255) NOT NULL DEFAULT '' COMMENT '商品简介',
     * description text NOT NULL COMMENT '商品详情',
     * QRcode varchar(255) NOT NULL DEFAULT '' COMMENT '商品二维码',
     * code varchar(50) NOT NULL COMMENT '商家编号',
     * is_stock_visible bit(1) NOT NULL DEFAULT b'0' COMMENT '页面不显示库存',
     * is_hot bit(1) NOT NULL DEFAULT b'0' COMMENT '是否热销商品',
     * is_recommend bit(1) NOT NULL DEFAULT b'0' COMMENT '是否推荐',
     * is_new bit(1) NOT NULL DEFAULT b'0' COMMENT '是否新品',
     * `is_pre_sale` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否预售',
     * is_bill bit(1) NOT NULL DEFAULT b'0' COMMENT '是否开具增值税发票 1是，0否',
     * state tinyint(3) NOT NULL DEFAULT 1 COMMENT '商品状态 0下架，1正常，10违规（禁售）',
     * sale_date datetime NOT NULL COMMENT '上下架时间',
     * create_time datetime NOT NULL COMMENT '商品添加时间',
     * update_time datetime NOT NULL COMMENT '商品编辑时间',
     * sort int(11) NOT NULL DEFAULT 0 COMMENT '排序',
     * PRIMARY KEY (goods_id)
     * $imageArray, //格式为 imageId,is_mainimage,orders;imageId,is_mainimage,orders
     * $skuArray); //#商品sku编码数组 字段之间¦分隔, 记录之间§分隔,
     * #格式skuId¦price¦stock¦pvs¦code¦barcode§skuId¦price¦stock¦pvs¦code¦barcode
     * #pvs格式 propId:valueId;propId:valueId
     *
     * @param unknown $data(数据待定)
    */
    //public function addOrEditGoods($goods_id, $goods_name, $shopid, $category_id, $category_id_1, $category_id_2, $brand_id, $group_id_array, $goods_type, $market_price, $price, $cost_price, $point_exchange_type, $point_exchange, $give_point, $is_member_discount, $shipping_fee, $shipping_fee_id, $stock, $max_buy, $min_stock_alarm, $clicks, $sales, $collects, $star, $evaluates, $shares, $province_id, $city_id, $picture, $keywords, $introduction, $description, $QRcode, $code, $is_stock_visible, $is_hot, $is_recommend, $is_new, $state, $sort, $image_array, $sku_array, $state, $sku_img_array);
    /**
     * 二维码路径进库
     * @param unknown $goodsId
     * @param unknown $url
    */
    public function goods_QRcode_make(){
        $goodsId = isset($this->request_common_array['goodsId']) ? $this->request_common_array['goodsId'] : '';
        $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
        $retval = $this->service->goods_QRcode_make($goodsId, $url);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取商品的sku信息
     *
     * @param unknown $goods_id
    */
    public function getGoodsSku(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsSku($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询sku多图数据
     * @param unknown $goods_id
    */
    public function getGoodsSkuPicture(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsSkuPicture($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 更新商品的sku数据
     *
     * @param unknown $goods_id
     * @param unknown $sku_list
    */
    public function ModifyGoodsSku(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $sku_list = isset($this->request_common_array['sku_list']) ? $this->request_common_array['sku_list'] : '';
        $res = $this->service->ModifyGoodsSku($goods_id, $sku_list);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取商品的图片信息
     *
     * @param unknown $goods_id
    */
    public function getGoodsImg(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsImg($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 商品下架
     *
     * @param unknown $condition
    */
    public function ModifyGoodsOffline(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $res = $this->service->ModifyGoodsOffline($condition);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 商品上架
     *
     * @param unknown $condition
    */
    public function ModifyGoodsOnline(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $res = $this->service->ModifyGoodsOnline($condition);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除商品
     *
     * @param unknown $goods_id
    */
    public function deleteGoods(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $res = $this->service->deleteGoods($goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除商品的图片信息
     *
     * @param unknown $goods_id
    */
    public function deleteGoodImages(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $res = $this->service->deleteGoodImages($goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取单条商品的详细信息
     *
     * @param unknown $goods_id
    */
    public function getGoodsDetail(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsDetail($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 商品规格列表
     *
     * @param unknown $condition
     * @param unknown $field
    */
    public function getGoodsAttributeList(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getGoodsAttributeList($condition, $field, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 商品规格值列表
     *
     * @param unknown $condition
     * @param unknown $field
    */
    public function getGoodsAttributeValueList(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '';
        $retval = $this->service->getGoodsAttributeValueList($condition, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加商品规格
     *
     * @param unknown $spec_name
     * @param unknown $sort
     * @param unknown $is_visible
    */
    public function addGoodsSpec(){
        $spec_name = isset($this->request_common_array['spec_name']) ? $this->request_common_array['spec_name'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : 0;
        $res = $this->service->addGoodsSpec($spec_name, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加商品规格值
     *
     * @param unknown $spec_id
     * @param unknown $spec_value
     * @param unknown $sort
    */
    public function addGoodsSpecValue(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $spec_value = isset($this->request_common_array['spec_value']) ? $this->request_common_array['spec_value'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : 0;
        $res = $this->service->addGoodsSpecValue($spec_id, $spec_value, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 根据条件查询所需条件
     *
     * @param unknown $condition
    */
    public function getSearchGoodsList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['$field'] : '*';
        $retval = $this->service->getSearchGoodsList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 修改商品分类
     *
     * @param unknown $goods_id
     * @param unknown $goods_type
    */
    public function ModifyGoodsGroup(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $goods_type = isset($this->request_common_array['goods_type']) ? $this->request_common_array['goods_type'] : '';
        $res = $this->service->ModifyGoodsGroup($goods_id, $goods_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改商品推荐 （新品 ， 精品 ， 特惠）
     *
     * @param unknown $goods_id
     * @param unknown $goods_type
    */
    public function ModifyGoodsRecommend(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $goods_type = isset($this->request_common_array['goods_type']) ? $this->request_common_array['goods_type'] : '';
        $res = $this->service->ModifyGoodsRecommend($goods_id, $goods_type);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取商品可得积分
     *
     * @param unknown $goods_id
    */
    public function getGoodsGivePoint(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsGivePoint($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 通过商品skuid查询goods_id
     *
     * @param unknown $sku_id
    */
    public function getGoodsId(){
        $sku_id = isset($this->request_common_array['sku_id']) ? $this->request_common_array['sku_id'] : '';
        $retval = $this->service->getGoodsId($sku_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取商品的店铺ID
     *
     * @param unknown $goods_id
    */
    public function getGoodsShopid(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsShopid($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取会员购物车
     *
     * @param unknown $uid
    */
    public function getCart(){  //对应是cart
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $retval = $this->service->getGoodsShopid($uid, $shop_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加购物车
     *
     * @param unknown $uid
     * @param unknown $shop_id
     * @param unknown $shop_name
     * @param unknown $goods_id
     * @param unknown $goods_name
     * @param unknown $sku_id
     * @param unknown $sku_name
     * @param unknown $price
     * @param unknown $num
     * @param unknown $picture
     * @param unknown $bl_id
    */
    public function addCart(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $shop_name = isset($this->request_common_array['shop_name']) ? $this->request_common_array['shop_name'] : '';
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $goods_name = isset($this->request_common_array['goods_name']) ? $this->request_common_array['goods_name'] : '';
        $sku_id = isset($this->request_common_array['sku_id']) ? $this->request_common_array['sku_id'] : '';
        $sku_name = isset($this->request_common_array['sku_name']) ? $this->request_common_array['sku_name'] : '';
        $price = isset($this->request_common_array['price']) ? $this->request_common_array['price'] : '';
        $num = isset($this->request_common_array['num']) ? $this->request_common_array['num'] : '';
        $picture = isset($this->request_common_array['picture']) ? $this->request_common_array['picture'] : '';
        $bl_id = isset($this->request_common_array['bl_id']) ? $this->request_common_array['bl_id'] : '';
        $res = $this->service->addCart($uid, $shop_id, $shop_name, $goods_id, $goods_name, $sku_id, $sku_name, $price, $num, $picture, $bl_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 购物车修改数量
     *
     * @param unknown $cart_id
     * @param unknown $num
    */
    public function cartAdjustNum(){
        $cart_id = isset($this->request_common_array['cart_id']) ? $this->request_common_array['cart_id'] : '';
        $num = isset($this->request_common_array['num']) ? $this->request_common_array['num'] : '';
        $res = $this->service->cartAdjustNum($cart_id, $num);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 购物车项目删除
     *
     * @param unknown $cart_id_array
     *            多项用，隔开
    */
    public function cartDelete(){
        $cart_id_array = isset($this->request_common_array['cart_id_array']) ? $this->request_common_array['cart_id_array'] : '';
        $res = $this->service->cartDelete($cart_id_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取分组商品列表
     *
     * @param unknown $goods_group_id
     * @param number $num
    */
    public function getGroupGoodsList(){
        $goods_group_id = isset($this->request_common_array['goods_group_id']) ? $this->request_common_array['goods_group_id'] : '';
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $num = isset($this->request_common_array['num']) ? $this->request_common_array['num'] : 0;
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getGroupGoodsList($goods_group_id, $condition, $num, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 通过购物车项获取列表
     *
     * @param unknown $carts
     *            ','隔开
    */
    public function getCartList(){
        $carts = isset($this->request_common_array['carts']) ? $this->request_common_array['carts'] : '';
        $retval = $this->service->getCartList($carts);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取限时折扣的商品
     *
     * @param number $page_index
     * @param number $page_size
     * @param unknown $condition
     * @param string $order
    */
    public function getDiscountGoodsList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getDiscountGoodsList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 商品评价信息
     *
     * @param unknown $goods_id
    */
    public function getGoodsEvaluate(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsEvaluate($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 商品评价表
     * @param number $page_index
     * @param number $page_size
     * @param unknown $condition
     * @param string $order
     * @param unknown $field
    */
    public function getGoodsEvaluateList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getDiscountGoodsList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 商品评价信息的数量
     * @evaluate_count总数量 @imgs_count带图的数量 @praise_count好评数量 @center_count中评数量 bad_count差评数量
     *
     * @param unknown $goods_id
    */
    public function getGoodsEvaluateCount(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsEvaluateCount($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 查询商品兑换所需积分
     *
     * @param unknown $goods_id返回0表示不能兑换
    */
    public function getGoodsPointExchange(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsPointExchange($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取单个商品的sku属性
     *
     * @param unknown $goods_id
    */
    public function getGoodsAttribute(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->getGoodsAttribute($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取商品咨询类型列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getConsultTypeList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getConsultTypeList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加 商品咨询类型
     *
     * @param unknown $ct_name
     * @param unknown $ct_introduce
     * @param unknown $ct_sort
    */
    public function addConsultType(){
        $ct_name = isset($this->request_common_array['ct_name']) ? $this->request_common_array['ct_name'] : '';
        $ct_introduce = isset($this->request_common_array['ct_introduce']) ? $this->request_common_array['ct_introduce'] : '';
        $ct_sort = isset($this->request_common_array['ct_sort']) ? $this->request_common_array['ct_sort'] : '';
        $res = $this->service->addConsultType($ct_name, $ct_introduce, $ct_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改商品咨询类型
     *
     * @param unknown $ct_id
     * @param unknown $ct_name
     * @param unknown $ct_introduce
     * @param unknown $ct_sort
    */
    public function updateConsultType(){
        $ct_id = isset($this->request_common_array['ct_id']) ? $this->request_common_array['ct_id'] : '';
        $ct_name = isset($this->request_common_array['ct_name']) ? $this->request_common_array['ct_name'] : '';
        $ct_introduce = isset($this->request_common_array['ct_introduce']) ? $this->request_common_array['ct_introduce'] : '';
        $ct_sort = isset($this->request_common_array['ct_sort']) ? $this->request_common_array['ct_sort'] : '';
        $res = $this->service->updateConsultType($ct_id, $ct_name, $ct_introduce, $ct_sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除 商品咨询类型
     *
     * @param unknown $ct_id
    */
    public function deleteConsultType(){
        $ct_id = isset($this->request_common_array['ct_id']) ? $this->request_common_array['ct_id'] : '';
        $res = $this->service->deleteConsultType($ct_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取商品咨询列表
     *
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getConsultList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getConsultList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取商品咨询详情
     *
     * @param unknown $ct_id
    */
    public function getConsultDetail(){
        $ct_id = isset($this->request_common_array['ct_id']) ? $this->request_common_array['ct_id'] : '';
        $retval = $this->service->getConsultDetail($ct_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加 商品咨询
     *
     * @param unknown $goods_id
     * @param unknown $goods_name
     * @param unknown $uid
     * @param unknown $member_name
     * @param unknown $shop_id
     * @param unknown $shop_name
     * @param unknown $ct_id
     * @param unknown $consult_content
    */
    public function addConsult(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $goods_name = isset($this->request_common_array['goods_name']) ? $this->request_common_array['goods_name'] : '';
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $member_name = isset($this->request_common_array['member_name']) ? $this->request_common_array['member_name'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $shop_name = isset($this->request_common_array['shop_name']) ? $this->request_common_array['shop_name'] : '';
        $ct_id = isset($this->request_common_array['ct_id']) ? $this->request_common_array['ct_id'] : '';
        $consult_content = isset($this->request_common_array['consult_content']) ? $this->request_common_array['consult_content'] : '';
        $res = $this->service->addConsult($goods_id, $goods_name, $uid, $member_name, $shop_id, $shop_name, $ct_id, $consult_content);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 回复 商品咨询 （店铺后台）
     *
     * @param unknown $consult_id
     * @param unknown $consult_reply
    */
    public function replyConsult(){
        $consult_id = isset($this->request_common_array['consult_id']) ? $this->request_common_array['consult_id'] : '';
        $consult_reply = isset($this->request_common_array['consult_reply']) ? $this->request_common_array['consult_reply'] : '';
        $res = $this->service->replyConsult($consult_id, $consult_reply);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除 商品咨询（店铺后台）
     *
     * @param unknown $consult_id
    */
    public function deleteConsult(){
        $consult_id = isset($this->request_common_array['consult_id']) ? $this->request_common_array['consult_id'] : '';
        $res = $this->service->deleteConsult($consult_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取销售钱排行的商品
     *
     * @param unknown $condition
    */
    public function getGoodsRank(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsRank($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取咨询个数
     *
     * @param unknown $condition
    */
    public function getConsultCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getConsultCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取商品运费模板情况
     *
     * @param unknown $goods_id
     * @param unknown $province_id
     * @param unknown $city_id
    */
    public function getGoodsExpressTemplate(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $province_id = isset($this->request_common_array['province_id']) ? $this->request_common_array['province_id'] : '';
        $city_id = isset($this->request_common_array['city_id']) ? $this->request_common_array['city_id'] : '';
        $retval = $this->service->getConsultCount($goods_id, $province_id, $city_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加商品评价回复
     * $id 评价id
     * $replyContent 回复内容
     * $replyType 回复类型
    */
    public function addGoodsEvaluateReply(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $replyContent = isset($this->request_common_array['replyContent']) ? $this->request_common_array['replyContent'] : '';
        $replyType = isset($this->request_common_array['replyType']) ? $this->request_common_array['replyType'] : '';
        $res = $this->service->addGoodsEvaluateReply($id, $replyContent, $replyType);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
     
    /**
     * 设置评价显示状态
    */
    public function setEvaluateShowStatu(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->setEvaluateShowStatu($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 删除评价
    */
    public function deleteEvaluate(){
        $id = isset($this->request_common_array['id']) ? $this->request_common_array['id'] : '';
        $res = $this->service->deleteEvaluate($id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 获取 商品规格列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getGoodsSpecList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getGoodsSpecList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取 商品规格详情
     * @param unknown $spec_id
    */
    public function getGoodsSpecDetail(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '*';
        $retval = $this->service->getGoodsSpecDetail($spec_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 添加 商品规格
     * @param unknown $shop_id
     * @param unknown $spec_name
     * @param unknown $is_visible
     * @param unknown $sort
    */
    public function addGoodsSpecService(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $spec_name = isset($this->request_common_array['spec_name']) ? $this->request_common_array['spec_name'] : '';
        $show_type = isset($this->request_common_array['show_type']) ? $this->request_common_array['show_type'] : '';
        $is_visible = isset($this->request_common_array['is_visible']) ? $this->request_common_array['is_visible'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $spec_value_str = isset($this->request_common_array['spec_value_str']) ? $this->request_common_array['spec_value_str'] : '';
        $attr_id = isset($this->request_common_array['attr_id']) ? $this->request_common_array['attr_id'] : 0;
        $res = $this->service->addGoodsSpecService($shop_id, $spec_name, $show_type, $is_visible, $sort, $spec_value_str, $attr_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加商品规格属性
     * @param unknown $spec_id
     * @param unknown $spec_value_name
     * @param unknown $spec_value_data
     * @param unknown $is_visible
     * @param unknown $sort
    */
    public function addGoodsSpecValueService(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $spec_value_name = isset($this->request_common_array['spec_value_name']) ? $this->request_common_array['spec_value_name'] : '';
        $spec_value_data = isset($this->request_common_array['spec_value_data']) ? $this->request_common_array['spec_value_data'] : '';
        $is_visible = isset($this->request_common_array['is_visible']) ? $this->request_common_array['is_visible'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->addGoodsSpecValueService($spec_id, $spec_value_name, $spec_value_data, $is_visible, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 检测 商品规格是否使用过
     * 返回true = 使用过  或者  false = 没有使用过
     * @param unknown $spec_id
    */
    public function checkGoodsSpecIsUse(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $retval = $this->service->checkGoodsSpecIsUse($spec_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 检测 商品规格属性是否使用过
     * 返回true = 使用过  或者  false = 没有使用过
     * @param unknown $spec_id
     * @param unknown $spec_value_id
    */
    public function checkGoodsSpecValueIsUse(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $spec_value_id = isset($this->request_common_array['spec_value_id']) ? $this->request_common_array['spec_value_id'] : '';
        $retval = $this->service->checkGoodsSpecValueIsUse($spec_id, $spec_value_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 删除 商品规格
     * @param unknown $spec_id
    */
    public function deleteGoodsSpec(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $res = $this->service->deleteGoodsSpec($spec_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除 商品规格属性
     * @param unknown $spec_id
     * @param unknown $spec_value_id
    */
    public function deleteGoodsSpecValue(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $spec_value_id = isset($this->request_common_array['spec_value_id']) ? $this->request_common_array['spec_value_id'] : '';
        $res = $this->service->deleteGoodsSpecValue($spec_id, $spec_value_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改 商品规格
     * @param unknown $spec_id
     * @param unknown $shop_id
     * @param unknown $spec_name
     * @param unknown $is_visible
     * @param unknown $sort
    */
    public function updateGoodsSpecService(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $spec_name = isset($this->request_common_array['spec_name']) ? $this->request_common_array['spec_name'] : '';
        $show_type = isset($this->request_common_array['show_type']) ? $this->request_common_array['show_type'] : '';
        $is_visible = isset($this->request_common_array['is_visible']) ? $this->request_common_array['is_visible'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $spec_value_str = isset($this->request_common_array['spec_value_str']) ? $this->request_common_array['spec_value_str'] : '';
        $res = $this->service->updateGoodsSpecService($spec_id, $shop_id, $spec_name, $show_type, $is_visible, $sort, $spec_value_str);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 修改商品规格单个字段
     * @param unknown $spec_id
     * @param unknown $field_name
     * @param unknown $field_value
    */
    public function modifyGoodsSpecField(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $field_name = isset($this->request_common_array['field_name']) ? $this->request_common_array['field_name'] : '';
        $field_value = isset($this->request_common_array['field_value']) ? $this->request_common_array['field_value'] : '';
        $res = $this->service->modifyGoodsSpecField($spec_id, $field_name, $field_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改 商品规格属性 单个字段
     * @param unknown $spec_value_id
     * @param unknown $field_name
     * @param unknown $field_value
    */
    public function modifyGoodsSpecValueField(){
        $spec_value_id = isset($this->request_common_array['spec_value_id']) ? $this->request_common_array['spec_value_id'] : '';
        $field_name = isset($this->request_common_array['field_name']) ? $this->request_common_array['field_name'] : '';
        $field_value = isset($this->request_common_array['field_value']) ? $this->request_common_array['field_value'] : '';
        $res = $this->service->modifyGoodsSpecValueField($spec_value_id, $field_name, $field_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 获取 商品类型列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
    */
    public function getAttributeServiceList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getAttributeServiceList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取商品类型属性列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
    */
    public function getAttributeValueServiceList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
        $retval = $this->service->getAttributeValueServiceList($page_index, $page_size, $condition, $order, $field);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取商品类型详情
     * @param unknown $attr_id
    */
    public function getAttributeServiceDetail(){
        $attr_id = isset($this->request_common_array['attr_id']) ? $this->request_common_array['attr_id'] : '';
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getAttributeServiceDetail($attr_id, $condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 添加 商品类型
     * @param unknown $attribute_name
     * @param unknown $is_use
     * @param unknown $spec_id_array
     * @param unknown $sort
     * @param unknown $value_string
    */
    public function addAttributeService(){
        $attribute_name = isset($this->request_common_array['attribute_name']) ? $this->request_common_array['attribute_name'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $spec_id_array = isset($this->request_common_array['spec_id_array']) ? $this->request_common_array['spec_id_array'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $value_string = isset($this->request_common_array['value_string']) ? $this->request_common_array['value_string'] : '';
        $res = $this->service->addAttributeService($attribute_name, $is_use, $spec_id_array, $sort, $value_string);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 添加 商品类型属性
     * @param unknown $attr_id
     * @param unknown $value
     * @param unknown $type
     * @param unknown $sort
     * @param unknown $is_search
    */
    public function addAttributeValueService(){
        $attr_id = isset($this->request_common_array['attr_id']) ? $this->request_common_array['attr_id'] : '';
        $attr_value_name = isset($this->request_common_array['attr_value_name']) ? $this->request_common_array['attr_value_name'] : '';
        $type = isset($this->request_common_array['type']) ? $this->request_common_array['type'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $is_search = isset($this->request_common_array['is_search']) ? $this->request_common_array['is_search'] : '';
        $value = isset($this->request_common_array['value']) ? $this->request_common_array['value'] : '';
        $res = $this->service->addAttributeValueService($attr_id, $attr_value_name, $type, $sort, $is_search, $value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除 商品类型
     * @param unknown $attr_value_id
    */
    public function deleteAttributeService(){
        $attr_id = isset($this->request_common_array['attr_id']) ? $this->request_common_array['attr_id'] : '';
        $res = $this->service->deleteAttributeService($attr_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除 商品类型属性
     * @param unknown $attr_id
    */
    public function deleteAttributeValueService(){
        $attr_id = isset($this->request_common_array['attr_id']) ? $this->request_common_array['attr_id'] : '';
        $attr_value_id = isset($this->request_common_array['attr_value_id']) ? $this->request_common_array['attr_value_id'] : '';
        $res = $this->service->deleteAttributeValueService($attr_id,$attr_value_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改 商品类型 单个字段
     * @param unknown $attr_id
     * @param unknown $field_name
     * @param unknown $field_value
    */
    public function modifyAttributeFieldService(){
        $attr_id = isset($this->request_common_array['attr_id']) ? $this->request_common_array['attr_id'] : '';
        $field_name = isset($this->request_common_array['field_name']) ? $this->request_common_array['field_name'] : '';
        $field_value = isset($this->request_common_array['field_value']) ? $this->request_common_array['field_value'] : '';
        $res = $this->service->modifyAttributeFieldService($attr_id, $field_name, $field_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改商品属性值  单个值
     * @param unknown $field_name
     * @param unknown $field_value
    */
    public function modifyAttributeValueService(){
        $attr_value_id = isset($this->request_common_array['attr_value_id']) ? $this->request_common_array['attr_value_id'] : '';
        $field_name = isset($this->request_common_array['field_name']) ? $this->request_common_array['field_name'] : '';
        $field_value = isset($this->request_common_array['field_value']) ? $this->request_common_array['field_value'] : '';
        $res = $this->service->modifyAttributeValueService($attr_value_id, $field_name, $field_value);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 修改商品类型
     * @param unknown $attr_id
     * @param unknown $attr_name
     * @param unknown $is_use
     * @param unknown $spec_id_array
     * @param unknown $sort
    */
    public function updateAttributeService(){
        $attr_id = isset($this->request_common_array['attr_id']) ? $this->request_common_array['attr_id'] : '';
        $attr_name = isset($this->request_common_array['attr_name']) ? $this->request_common_array['attr_name'] : '';
        $is_use = isset($this->request_common_array['is_use']) ? $this->request_common_array['is_use'] : '';
        $spec_id_array = isset($this->request_common_array['spec_id_array']) ? $this->request_common_array['spec_id_array'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $value_string = isset($this->request_common_array['value_string']) ? $this->request_common_array['value_string'] : '';
        $res = $this->service->updateAttributeService($attr_id, $attr_name, $is_use, $spec_id_array, $sort, $value_string);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 判断商品属性名称是否已经存在
     * 存在 返回 true  不存在返回 false
     * @param unknown $value_name
    */
    public function checkGoodsSpecValueNameIsUse(){
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $value_name = isset($this->request_common_array['value_name']) ? $this->request_common_array['value_name'] : '';
        $retval = $this->service->checkGoodsSpecValueNameIsUse($spec_id, $value_name);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    
    /**
     * 获取分类详情
     * @param unknown $condition
    */
    public function getAttributeInfo(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getAttributeInfo($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取所需规格
     * @param unknown $condition
    */
    public function getGoodsSpecQuery(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsSpecQuery($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取一定条件商品规格值 条数
     * @param unknown $condition
    */
    public function getGoodsSpecValueCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsSpecValueCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 获取一定条件下商品类型值的 条数
     * @param unknown $condition
    */
    public function getGoodsAttrValueCount(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsAttrValueCount($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询商品分类下的商品属性及商品规格
    */
    public function getGoodsAttrSpecQuery(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsAttrSpecQuery($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 查询商品属性
    */
    public function getGoodsAttributeQuery(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $retval = $this->service->getGoodsAttributeQuery($condition);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 商品回收库的分页
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
    */
    public function getGoodsDeletedList(){
        $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
        $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
        $retval = $this->service->getGoodsDeletedList($page_index, $page_size, $condition, $order);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 恢复商品
     * @param unknown $goods_ids
    */
    public function regainGoodsDeleted(){
        $goods_ids = isset($this->request_common_array['goods_ids']) ? $this->request_common_array['goods_ids'] : '';
        $res = $this->service->regainGoodsDeleted($goods_ids);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    
    /**
     * 拷贝商品信息
     * @param unknown $goods_id
    */
    public function copyGoodsInfo(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $retval = $this->service->copyGoodsInfo($goods_id);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 删除回收站商品
     * @param unknown $goods_id
    */
    public function deleteRecycleGoods(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $res = $this->service->deleteRecycleGoods($goods_id);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 用户登录后同步购物车数据
    */
    public function syncUserCart(){
        $uid = isset($this->request_common_array['uid']) ? $this->request_common_array['uid'] : '';
        $retval = $this->service->syncUserCart($uid);
        if($retval){
            return $this->outMessage($retval);
        }else{
            return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
        }
    }
    /**
     * 更改商品排序
     * @param unknown $goods_id
     * @param unknown $sort
    */
    public function updateGoodsSort(){
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
        $res = $this->service->updateGoodsSort($goods_id, $sort);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 添加商品规格关联图
     * @param unknown $goods_id
     * @param unknown $spec_id
     * @param unknown $spec_value_id
     * @param unknown $sku_img_array
    */
    public function addGoodsSkuPicture(){
        $shop_id = isset($this->request_common_array['shop_id']) ? $this->request_common_array['shop_id'] : '';
        $goods_id = isset($this->request_common_array['goods_id']) ? $this->request_common_array['goods_id'] : '';
        $spec_id = isset($this->request_common_array['spec_id']) ? $this->request_common_array['spec_id'] : '';
        $spec_value_id = isset($this->request_common_array['spec_value_id']) ? $this->request_common_array['spec_value_id'] : '';
        $sku_img_array = isset($this->request_common_array['sku_img_array']) ? $this->request_common_array['sku_img_array'] : '';
        $res = $this->service->addGoodsSkuPicture($shop_id, $goods_id, $spec_id, $spec_value_id, $sku_img_array);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
    /**
     * 删除商品规格图片
     * @param unknown $condition
    */
    public function deleteGoodsSkuPicture(){
        $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
        $res = $this->service->deleteGoodsSkuPicture($condition);
        $retval = AjaxReturn($res);
        if($retval['code'] > 0){
            return $this->outMessage($retval['code']);
        }else{
            return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
        }
    }
}