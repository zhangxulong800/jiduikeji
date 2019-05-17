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
namespace app\shop\controller;

use data\model\NsPointConfigModel;
use data\model\Send;
use data\service\Article;
use data\service\Goods;
use data\service\Platform;
use data\service\Shop;
use app\SimpleController;
/**
 * 首页控制器
 * 创建人：王永杰
 * 创建时间：2017年2月6日 11:01:19
 */
class Index extends SimpleController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function _empty($name)
    {}
    /*
     * 平台首页
     * 创建人：王永杰
     * 创建时间：2017年2月7日 15:46:26
     *
     * @return \think\response\View
     */
    public function index()
    {
        $shop = new shop();
        $shop_list = $shop->getShopList(1,0,'','shop_recommend desc,shop_sort'); // 店铺查询店铺街显示
        $this->assign('shop_list', $shop_list['data']);
        $this->assign('shop_list_count', count($shop_list['data']));
        
        $article = new Article();
        $new_article = $article->getArticleList(1, 5, '', 'create_time desc'); // 公告
        $this->assign("new_article", $new_article['data']);
        
        $this->controlCommendBlock();
        $platform = new Platform();
        $web_block_list = $platform->getWebBlockListDetail();
        $this->assign('is_head_goods_nav', 1); // 代表默认显示以及分类
        $this->assign('web_block_list', $web_block_list);
        // 限时折扣
        $goods = new Goods();
        $page = isset($_GET['page']) ? $_GET['page'] : '1'; // pageindex
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '0';
        
        $condition['status'] = 1;
        if (! empty($category_id)) {
            $condition['category_id_1'] = $category_id;
        }
        $discount_list = $goods->getDiscountGoodsList($page, 5, $condition, 'end_time');
        $assign_get_list = array(
            'page' => $page,
            'page_count' => $discount_list['page_count'], // 总页数
            'total_count' => $discount_list['total_count'], // 总条数
            'discount_list' => $discount_list['data'], // 店铺分页
            'category_id' => $category_id
        ); // 已选中商品分类一级
        
        foreach ($assign_get_list as $key => $value) {
            $this->assign($key, $value);
        }
        return view($this->style . 'Index/index');
    }
    /*官网代码开始*/
    public function profile()
    {
        return view($this->style . 'Index/profile');
    }

    public function attract_investment()
    {
        return view($this->style . 'Index/attract_investment');
    }

    public function news()
    {
        return view($this->style . 'Index/news');
    }
    public function mall()
    {
        return view($this->style . 'Index/mall');
    }
    public function contactUs()
    {
        return view($this->style . 'Index/contactUs');
    }
    public function newsDetails()
    {
        return view($this->style . 'Index/newsDetails');
    }
    /*官网代码结束*/
    /**
     * 限时折扣(单独界面)
     * 创建人：王永杰
     * 创建时间：2017年2月7日 17:28:58
     *
     * @return \think\response\View
     */
    public function discount()
    {
        $goods = new Goods();
        $page = isset($_GET['page']) ? $_GET['page'] : '1'; // pageindex
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '0';
        
        $condition['status'] = 1;
        if (! empty($category_id)) {
            $condition['category_id_1'] = $category_id;
        }
        $discount_list = $goods->getDiscountGoodsList($page, 20, $condition, 'end_time');
        $assign_get_list = array(
            'page' => $page,
            'page_count' => $discount_list['page_count'], // 总页数
            'total_count' => $discount_list['total_count'], // 总条数
            'discount_list' => $discount_list['data'], // 店铺分页
            'category_id' => $category_id
        ); // 已选中商品分类一级
        
        foreach ($assign_get_list as $key => $value) {
            $this->assign($key, $value);
        }
        $this->assign('is_head_goods_nav', 1); // 代表默认显示以及分类
        return view($this->style . 'Index/discount');
    }

    /**
     * 平台促销板块信息
     * 任鹏强
     * 2017年2月22日17:56:03
     */
    public function controlCommendBlock()
    {
    	$config = new NsPointConfigModel();
        $Platform = new Platform();
		
        $recommend_block = $Platform->getPlatformGoodsRecommendClass();
        foreach($recommend_block as $k=>$v){
            //获取模块下商品
            $goods_list = $Platform->getPlatformGoodsRecommend($v['class_id']);
            if(empty($goods_list)){
                unset($recommend_block[$k]);
            } else {
				foreach($v['goods_list'] as $key=>$val){
					$convert_rate=$config->where("shop_id",$val['goods_info']['shop_id'])->value("convert_rate");
					if($val['goods_info']['point_exchange_type']==2 && $convert_rate>0){
						$recommend_block[$k]['goods_list'][$key]['goods_info']['normal_integral']=round($val['goods_info']['promotion_price']/$convert_rate,2);
						$recommend_block[$k]['goods_list'][$key]['goods_info']['jplus_integral']=round($val['goods_info']['market_price']/$convert_rate,2);
					}
				}
			}
        }
        $this->assign("recommend_block", $recommend_block);
    }

    /**
     * 发送短信
     */
    public function sms($mobile = '18649313172')
    {
        // if(request()->isPost()){
        $Send = new \data\extend\Send();
        $result = $Send->sms([
            'param' => [
                'code' => '123456',
                'time' => '60秒'
            ],
            'mobile' => $mobile,
            'template' => 'SMS_43210099'
        ]);
        if ($result !== true) {
            return $this->error($result);
        }
        return $this->success('短信下发成功！');
        // }
        // return $this->fetch();
    }
}