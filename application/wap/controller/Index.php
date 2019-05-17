<?php
/**
 * Index.php
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
namespace app\wap\controller;

use data\model\NsMemberAccountRecordsModel;
use data\service\Goods;
use data\service\GoodsBrand as GoodsBrand;
use data\service\GoodsCategory;
use data\service\Member as MemberService;
use data\service\Platform;
use data\service\Shop;
use data\service\Config;
use data\service\Weixin;
use data\service\WebSite;
use data\service\promotion\PromoteRewardRule;
use data\service\promotion\GoodsDiscount;
use data\service\Member;
use data\service\Promotion;
use data\service\Goods as GoodsService;
use think\Db;
class Index extends BaseController
{
    /**
     * 平台端首页
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function index()
        {
		//$this->redirect('/wap/index/ktv');//临时变更
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("signPackage", $ticket);
        $platform = new Platform();
        // 轮播图
        $plat_adv_list = $platform->getPlatformAdvPositionDetail(1105);
        $this->assign('plat_adv_list', $plat_adv_list);
        // 促销模块
        $class_list = $platform->getPlatformGoodsRecommendClass();
        $this->assign("class_list", $class_list);
        // 品牌
        $goods_brand = new GoodsBrand();
        $list = $goods_brand->getGoodsBrandList(1, 6, '', 'sort');
        $this->assign('list', $list['data']);
        
        // 限时折扣
        $goods = new Goods();
        $condition['status'] = 1;
        $discount_list = $goods->getDiscountGoodsList(1, 4, $condition, 'end_time');
        foreach ($discount_list['data'] as $k => $v) {
            $v['discount'] = str_replace('.00', '', $v['discount']);
//             $v['promotion_price'] = str_replace('.00', '', $v['promotion_price']);
//             $v['price'] = str_replace('.00', '', $v['price']);
        }
        $this->assign('discount_list', $discount_list['data']);
        
        // 首页限时折扣广告位
        $discount_adv = $platform->getPlatformAdvPositionDetail(1167);
        $this->assign('discount_adv', $discount_adv);
        
        // 首页商城热卖
        $hot_selling_adv = $platform->getPlatformAdvPositionDetail(1164);
        $this->assign('hot_selling_adv', $hot_selling_adv);
        
        // 公众号配置查询
        $config = new Config();
        $wchat_config = $config->getInstanceWchatConfig($this->instance_id);
        
        $is_subscribe = 0; // 标识：是否显示顶部关注 0：[隐藏]，1：[显示]
        // 检查是否配置过微信公众号
        if (! empty($wchat_config['value'])) {
            if (! empty($wchat_config['value']['appid']) && ! empty($wchat_config['value']['appsecret'])) {
                // 如何判断是否关注
                if (isWeixin()) {
                    if (! empty($this->uid)) {
                        // 检查当前用户是否关注
                        $user_sub = $this->user->checkUserIsSubscribeInstance($this->uid, $this->instance_id);
                        if ($user_sub == 0) {
                            // 未关注
                            $is_subscribe = 1;
                        }
                    }
                }
            }
        }
        $this->assign("is_subscribe", $is_subscribe);
        // 公众号二维码获取
        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        // var_dump($web_info);die;
        $this->assign('web_info', $web_info);
        
        $member = new MemberService();
        $source_user_name = "";
        $source_img_url = "";
        if (! empty($_GET['source_uid'])) {
            $_SESSION['source_uid'] = $_GET['source_uid'];
            $user_info = $member->getUserInfoByUid($_SESSION['source_uid']);
            if (! empty($user_info)) {
                $source_user_name = $user_info["nick_name"];
                if (! empty($user_info["user_headimg"])) {
                    $source_img_url = $user_info["user_headimg"];
                }
            }
        }
        $notice_arr = $config->getNotice(0);
		$experiencePoint=$member->experiencePoint($this->uid);
		$this->assign('experiencePoint', $experiencePoint);
        $this->assign('notice', $notice_arr);
        $this->assign('source_user_name', $source_user_name);
        $this->assign('source_img_url', $source_img_url);
        
        $member = new Member();
        $coupon_list = $member->getMemberCouponTypeList('all', $this->uid);//$this->instance_id改成all
        $this->assign('coupon_list', $coupon_list);
        $member_info = $member->getMemberDetail($this->instance_id);
        if(empty($member_info)){
            $member_info['balance'] = 0;
            $member_info['point']['point'] = 0;
            $member_info['exc_point']['exc_point'] = 0;
        }
        /*获取用户昨日收益开始*/
        $start_time = date('Y-m-d H:i:s',strtotime(date("Y-m-d 23:59:59",strtotime("-1 day"))));   //昨天开始时间
        $end_time   = date('Y-m-d H:i:s',mktime(0,0,0,date('m'),date('d')-1,date('Y')));  //昨天结束时间
        $condit ['uid']             =array('eq',$this->uid);
        $condit ['account_type']    =array('eq',2);
        $condit ['from_type']       =array('in','15,20,25,31');
        $condit ['is_add']          =array('eq',1);
        $condit ['number']          =array('gt',0);
        $condit ['create_time']     =array('between',array($end_time,$start_time));

        $members = Db::table('ns_member_account_records')
            ->where($condit)
            ->field('number')
            ->sum('number');
        /*获取用户昨日收益结束*/
        $this->assign('member_info', $member_info);
        $this->assign('members', $members);
        $goods_category = new GoodsCategory();
        $goods_category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
            "is_visible"    => 1,
            "level"         => 1
        ], 'sort');

		$goods      = new GoodsService();
        $goods_list = $goods->getMianGoodsList(1, 3, '', '',1   );

        foreach ($goods_list['data'] as $k=>$v){
            $point                                  = round(($v['market_price']-($v['cost_price']*2))*0.4,2);
            $goods_list['data'][$k]['pointNum']     = $point;
            $price                                  = ceil($goods_list['data'][$k]['price']);
            $goods_list['data'][$k]['price']        = $price;
            $market_price                           = ceil($goods_list['data'][$k]['market_price']);
            $goods_list['data'][$k]['market_price'] = $market_price;
        }

        $this->assign('goods_list', $goods_list);

        $condition = ["goods_id"=>["in",'1148,1112,1294,1260']];
		$goodsList = $goods->getGoodsList($page_index = 1, $page_size = 4, $condition, $order = '');
		
		foreach ($goodsList['data'] as $k=>$v){
            $point = round(($v['market_price']-($v['cost_price']*2))*0.4,2);
            $goodsList['data'][$k]['pointNum'] = $point;
            $price = ceil($goodsList['data'][$k]['price']);
            $goodsList['data'][$k]['price'] = $price;
            $market_price = ceil($goodsList['data'][$k]['market_price']);
            $goodsList['data'][$k]['market_price'] = $market_price;
        }

        $this->assign('goodsList', $goodsList);
		foreach($goodsList['data'] as $k=>$v){
			$goodsList['data'][$k]['point'] = round(($v['market_price']-($v['cost_price']+$v['sku_list'][0]['total_cost_price']))*0.4,2);
		}

        $this->assign('goodsList', $goodsList);
        $shop = new Shop();
        $shop_group_list = $shop->getShopGroup(); // 店铺分类
        $this->assign('shop_group_list', $shop_group_list['data']);
        $this->assign('goods_category_list_1', $goods_category_list_1['data']);
		$newuser_list = $goods->getZeroGoodsList(1,5,[],'');
		$this->assign('newuser_list', $newuser_list);
		$youlike=Db::table('ns_goods')->order('sales desc')->limit(3)->select();
		foreach($youlike as $ks=>$vs){
			$youlike[$ks]['picturedis']=Db::table('sys_album_picture')->where('pic_id',$vs['picture'])->value('pic_cover_big');
			$youlike[$ks]['point'] = round(($vs['market_price']-($vs['cost_price']+$vs['total_cost_price']))*0.4,2);
		}
		$this->assign('youlike', $youlike);
        return view($this->style . 'Index/index');
    }
    /**
     * 限时折扣
     */
    public function discount()
    {
        $platform = new Platform();
        // 限时折扣广告位
        $discounts_adv = $platform->getPlatformAdvPositionDetail(1163);
        $this->assign('discounts_adv', $discounts_adv);
        if (request()->isAjax()) {
            $goods = new Goods();
            $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '0';
            $condition['status'] = 1;
            if (! empty($category_id)) {
                $condition['category_id_1'] = $category_id;
            }
            $discount_list = $goods->getDiscountGoodsList(1, 0, $condition);
            foreach ($discount_list['data'] as $k => $v) {
                $v['discount'] = str_replace('.00', '', $v['discount']);
                $v['promotion_price'] = str_replace('.00', '', $v['promotion_price']);
                $v['price'] = str_replace('.00', '', $v['price']);
            }
            return $discount_list['data'];
        } else {
            $goods_category = new GoodsCategory();
            $goods_category_list_1 = $goods_category->getGoodsCategoryList(1, 0, [
                "is_visible" => 1,
                "level" => 1
            ]);
            
            $this->assign('goods_category_list_1', $goods_category_list_1['data']);
            
            return view($this->style . 'Index/discount');
        }
    }

    public function getGoodsDiscount(){
        if (request()->isAjax()) {
            $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : date('Y-m-d H:i:s', time()); // 店铺名称

            $goods_discount = new GoodsDiscount;
            $condition = array(
                "discount_name" => ["like", '%团购%'],
                "status" => 1,
                "start_time" => ['<',
                    $start_time],
                "end_time" => ['>',
                    $start_time]
            );
            $goods_discount_list = $goods_discount->getDiscountID($condition);
//            var_dump($goods_discount_list);
            $goods_discount_list = $goods_discount->getDiscountGoodsList(1, 0, ["discount_id"=>$goods_discount_list]);
            return $goods_discount_list;
        }
    }

    // 分享送积分
    public function shareGivePoint()
    {
        if (request()->isAjax()) {
            $rewardRule = new PromoteRewardRule();
            $res = $rewardRule->memberShareSendPoint($this->instance_id, $this->uid);
            return AjaxReturn($res);
        }
    }
    /**
     * 设置页面打开cookie
     */
    public function setClientCookie(){
        $client = request()->post('client', '');
        setcookie('default_client', $client);
        //$cookie = request()->cookie('default_client', '');
       // return $cookie;
       return AjaxReturn(1);
    }
    /**
     * 首页领用优惠券
     */
    public function getCoupon(){
        $coupon_type_id = request()->post('coupon_type_id', 0);
        if(!empty($this->uid))
        {
            $member = new Member();
            $retval = $member->memberGetCoupon($this->uid, $coupon_type_id, 2);
            return AjaxReturn($retval);
        }else{
            return AjaxReturn(NO_LOGIN);
        }
       
    }
    /**
     * ktv
     */
    public function ktv()
    {
		return view($this->style . 'index/ktv');
    }
    /**
     * ktv购买会员套餐详情
     */
    public function ktvDetails()
    {
		return view($this->style . 'index/ktvDetails');
    }
    /**
     * ktv列表
     */
    public function ktvList()
    {
		return view($this->style . 'index/ktvList');
    }
    /**
     * ktv大礼包
     */
    public function giftBag()
    {
		return view($this->style . 'index/giftBag');
    }
    /**
     * 活动详情页
     */
    public function indexDetail()
    {
		return view($this->style . 'index/indexDetail');
    }
    /**
     * 一键分享
     */
    public function qrCode()
    {
		return view($this->style . 'index/qrCode');
    }
    /*
     * 消息中心
     * 
     */
    /**
     * 精品酒店
     */
    public function hotel()
    {
		return view($this->style . 'index/hotel');
    }
    public function news()
    {
		return view($this->style . 'index/news');
    }
    /**
     * 店铺街
     */
    public function shopStreet()
    {
        $shop = new Shop();
        if (request()->isAjax()) {
            $shop_name = isset($_POST['shop_name']) ? $_POST['shop_name'] : ''; // 店铺名称
            $shop_group_id = isset($_POST['shop_group_id']) ? $_POST['shop_group_id'] : ''; // 店铺分类
            $shop_group_name = isset($_POST['shop_group_name']) ? $_POST['shop_group_name'] : ''; // 店铺名称
            $order_type = isset($_POST['order_type']) ? $_POST['order_type'] : ''; // 排序类型 为1销售排行2信誉排行
            $sort = isset($_POST['sort']) ? $_POST['sort'] : 'asc'; // 倒排正排
            $page_index = isset($_POST['page_index']) ? $_POST['page_index'] : '1'; // 倒排正排
            $page_size = isset($_POST['page_size']) ? $_POST['page_size'] : '0'; // 倒排正排

            $order = "shop_sort " . $sort;
            if ($order_type == 1) {
                $order = "shop_sales " . $sort;
            } else
                if ($order_type == 2) {
                    $order = "shop_credit " . $sort;
                }

            $condition['shop_state'] = 1;
            if (! empty($shop_group_id)) {
                $condition['shop_group_id'] = $shop_group_id;
            }

            if (! empty($shop_name)) {
                $condition['shop_name'] = array(
                    "like",
                    "%" . $shop_name . "%"
                );
            }

            $shop_list = $shop->getShopList($page_index, $page_size, $condition, $order); // 店铺查询
            foreach ($shop_list['data'] as $key => $value){
                $components = new Components();
                $goods_list = $components->getTypeGoodsList( $value->shop_id, 0,3);
                $value['goods_list'] = $goods_list;

                $discount = new Promotion();
                $condition = array(
                    'shop_id'=>$value->shop_id,
                    'start_time' => array(
                        'ELT',
                        date('Y-m-d H:i:s', time())
                    ),
                    'end_time' => array(
                        'EGT',
                        date('Y-m-d H:i:s', time())
                    ),
                    'is_show' => 1,
                );

                $discount_info = $discount->getCouponTypeList(1, 3, $condition, '');
                $shop_list['data'][$key]['promotion_discount'] = $discount_info['data'];

                $goods = new Goods();
                $shop_list['data'][$key]['max_point'] = round($goods->getMaxInterest(['shop_id'=>$value->shop_id]) * 0.4, 0);
                $shop_list['data'][$key]['max_cash'] = $shop_list['data'][$key]['max_point'] * 6;
            }
            return $shop_list;
        }
        else{

            $shop_name = isset($_GET['shop_name']) ? $_GET['shop_name'] : ''; // 店铺名称
            $shop_group_id = isset($_GET['shop_group_id']) ? $_GET['shop_group_id'] : ''; // 店铺分类
            $shop_group_name = isset($_GET['shop_group_name']) ? $_GET['shop_group_name'] : ''; // 店铺名称
            $order_type = isset($_GET['order_type']) ? $_GET['order_type'] : ''; // 排序类型 为1销售排行2信誉排行
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'asc'; // 倒排正排

            $order = "shop_sort " . $sort;
            if ($order_type == 1) {
                $order = "shop_sales " . $sort;
            } else
                if ($order_type == 2) {
                    $order = "shop_credit " . $sort;
                }

            $condition['shop_state'] = 1;
            if (! empty($shop_group_id)) {
                $condition['shop_group_id'] = $shop_group_id;
            }

            if (! empty($shop_name)) {
                $condition['shop_name'] = array(
                    "like",
                    "%" . $shop_name . "%"
                );
            }

            $shop_list = $shop->getShopList(1, 0, $condition, $order); // 店铺查询
            foreach ($shop_list['data'] as $key => $value){
                $components = new Components();
                $goods_list = $components->getTypeGoodsList( $value->shop_id, 0,3);
                $value['goods_list'] = $goods_list;
                $goods = new Goods();

                $shop_list['data'][$key]['max_point'] = round($goods->getMaxInterest(['shop_id'=>$value->shop_id]) * 0.4, 0);
                $shop_list['data'][$key]['max_cash'] = $shop_list['data'][$key]['max_point'] * 6;
            }
            $shop_group_list = $shop->getShopGroup(); // 店铺分类
            $assign_get_list = array(
                'order_type' => $order_type, // 排序类型
                'shop_group_id' => $shop_group_id, // 店铺类型
                'shop_name' => $shop_name, // 搜索名称
                'sort' => $sort, // 排序
//            'shop_list' => $shop_list['data'], // 店铺列表
                'total_count' => $shop_list['total_count'], // 总条数
                'shop_group_list' => $shop_group_list['data']
            ); // 店铺分页

            foreach ($assign_get_list as $key => $value) {
                $this->assign($key, $value);
            }

            $this->assign('shop_group_name', $shop_group_name);
            return view($this->style . '/Index/shopStreet');

        }

    }
    

}
