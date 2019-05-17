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
 */
namespace app\admin\controller;

use data\service\Goods as GoodsService;
use data\service\Order as OrderService;
use data\service\User as User;
use data\service\Weixin;
use data\service\Shop;
use think\helper\Time;
use data\service\WebSite;
use think\Config;
use think\Db;
use data\service\shopaccount\ShopAccount;
use data\service\Member as MemberService;
/**
 * 后台主界面
 *
 * @author Administrator
 *        
 */
class Index extends BaseController
{
	public function __construct()
    {
        parent::__construct();
    }
    /**
     * zxl
     * 生成商家二维码
     */
    public function createrShopQecode()
    {
        //制作店铺二维码需要(平台的URL,商家的id,商家图片,商家名称)
        $uid = $this->user->getSessionUid();//登录商家后台的管理员的ID

        $instance_id = (new User())->getUserInfoByUid($uid)['instance_id'];

        $shop_all = Db::table('ns_shop')->where('shop_id', $instance_id)->field('uid, shop_name, shop_phone, shop_logo')->find();

        $shop_id = $instance_id;
        $UID = $shop_all['uid'];
        $shop_name = $shop_all['shop_name'];
        $shop_logo = $shop_all['shop_logo'];
        $shop_phone = $shop_all['shop_phone'];

        $url = Config::get('view_replace_str.APP_MAIN').'/shop/oneself?'.'shop_id'.'='.$shop_id.'&'.'shop_uid'.'='.$UID.'&'.'shop_name'.'='.$shop_name.'&'.'shop_phone'.'='.$shop_phone;

        // 生成二维码
        $upload_path = "upload/qrcode/promote_qrcode/shop"; // 商家二维码保存路径
        if (! file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        if(file_exists('shop_'.$shop_id.'.'.'png')){
            unlink('shop_'.$shop_id.'.'.'png');
        }else{
            getQRcode($url, $upload_path, 'shop_' . $shop_id);
        }
    }
    public function index()
    {
        @include ROOT_PATH . 'version.php';
        $debug = config('app_debug') == true ? '开发者模式' : '部署模式';
        $this->assign('debug', $debug);
        $main = \think\Request::instance()->domain();
        $this->assign('main', $main);
        // 销售排行
        $goods_rank = $this->getGoodsRealSalesRank();
        $this->assign("goods_list", $goods_rank);
        $web_info = $this->website->getWebSiteInfo();
        $this->assign('web_title', $this->instance_name);
        $this->assign('web_phone', $web_info['web_phone']);
        $this->assign('web_email', $web_info['web_email']);
        $shop = new Shop();
        // 得到店铺的账户情况
        $shop_account_info = $shop->getShopInfo($this->instance_id);

        $shop_shop = new ShopAccount();
        $shop_account_info['account_for_add'] = $shop_shop->getAccountRecordSum($this->instance_id, 1,
            date('Y-m-d', strtotime('-60 days', time())), date('Y-m-d H:m:s', time()), 0);
        $this->assign('shop_account_info', $shop_account_info);
		$ava_cost_price=$shop->shopAvailable($this->instance_id);
		$this->assign('ava_cost_price', $ava_cost_price);
        if (request()->isAjax()) {
            $user_tel = isset($_POST["user_tel"]) ? $_POST["user_tel"] : '';
            $point = isset($_POST["point"]) ? $_POST["point"] : -1;
            $shop_id = $this->instance_id;
            if($user_tel == ''){
                return '请输入电话号码！';
            }
            $member = new MemberService();
            $shop = new Shop();
            $user = $member->getMemberList(1, 0, ["su.user_tel"=>$user_tel]);
//            print_r($user);exit;
            if($user['total_count'] != 1){
                return '-1';
            }
            if($point == -1){
                return 1;
            }
            if($point <= 0){
                return '转赠数量必须大于0！';
            }
            $retr = $shop->givePoint($user['data'][0]['uid'], $point, $shop_id);
            return AjaxReturn($retr);
        }
        return view($this->style . 'Index/index');
    }
    /**
     * ajax 加载 店铺 会员 信息
     */
    public function getUserInfo()
    {
        $auth = new User();
        $user_info = $auth->getUserDetail($this->uid);
        return $user_info;
    }
    /**
     * 获取 商品 数量 全部 出售中 已审核 已下架
     */
    public function getGoodsCount()
    {
        $goods_count = new GoodsService();
        $goods_count_array = array();
        // 全部
        $goods_count_array['all'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id
        ]);
        // 出售中
        $goods_count_array['sale'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id,
            'state' => 1
        ]);
        // 仓库中已审核
        $goods_count_array['audit'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id,
            'state' => 0
        ]);
        // 下架
        $goods_count_array['shelf'] = $goods_count->getGoodsCount([
            'shop_id' => $this->instance_id,
            'state' => 10
        ]);
        return $goods_count_array;
    }

    /**
     * 获取 订单数量 代付款 待发货 已发货 已收货 已完成 已关闭 退款中 已退款
     */
    public function getOrderCount()
    {
        $order = new OrderService();
        $order_count_array = array();
        $order_count_array['daifukuan'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 0
        ]); // 代付款
        $order_count_array['daifahuo'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 1
        ]); // 代发货
        $order_count_array['yifahuo'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 2
        ]); // 已发货
        $order_count_array['yishouhuo'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 3
        ]); // 已收货
        $order_count_array['yiwancheng'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 4
        ]); // 已完成
        $order_count_array['yiguanbi'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => 5
        ]); // 已关闭
        $order_count_array['tuikuanzhong'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => - 1
        ]); // 退款中
        $order_count_array['yituikuan'] = $order->getOrderCount([
            'shop_id' => $this->instance_id,
            'order_status' => - 2
        ]); // 已退款
        $order_count_array['all'] = $order->getOrderCount([
            'shop_id' => $this->instance_id
        ]); // 全部
        return $order_count_array;
    }

    /**
     * 获取销售统计
     *
     * @return unknown
     */
    public function getSalesStatistics()
    {
        $order = new OrderService();
        $condition['shop_id'] = $this->instance_id;
        $condition['order_status'] = [
            '>',
            1
        ];
        $data['yesterday_money'] = $order->getPayMoneySum($condition, 'yesterday');
        $data['month_money'] = $order->getPayMoneySum($condition, 'month');
        $data['yesterday_goods'] = $order->getGoodsNumSum($condition, 'yesterday');
        $data['month_goods'] = $order->getGoodsNumSum($condition, 'month');
        return $data;
    }

    /**
     * 订单 图表 数据
     */
    public function getOrderChartCount()
    {
        $type = isset($_POST['date']) ? $_POST['date'] : 1;
        $order = new OrderService();
        $data = array();
        if ($type == 1) {
            list ($start, $end) = Time::today();
            for ($i = 0; $i < 24; $i ++) {
                $date_start = date("Y-m-d H:i:s", $start + 3600 * $i);
                $date_end = date("Y-m-d H:i:s", $start + 3600 * ($i + 1));
                $count = $order->getOrderCount([
                    'shop_id' => $this->instance_id,
                    'create_time' => [
                        'between',
                        [
                            $date_start,
                            $date_end
                        ]
                    ]
                ]);
                $data[$i] = array(
                    $i . ':00',
                    $count
                );
            }
        } elseif ($type == 2) {
            list ($start, $end) = Time::yesterday();
            for ($j = 0; $j < 24; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 3600 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 3600 * ($j + 1));
                $count = $order->getOrderCount([
                    'shop_id' => $this->instance_id,
                    'create_time' => [
                        'between',
                        [
                            $date_start,
                            $date_end
                        ]
                    ]
                ]);
                $data[$j] = array(
                    $j . ':00',
                    $count
                );
            }
        } elseif ($type == 3) {
            list ($start, $end) = Time::week();
            $start = $start - 604800;
            for ($j = 0; $j < 7; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                $count = $order->getOrderCount([
                    'shop_id' => $this->instance_id,
                    'create_time' => [
                        'between',
                        [
                            $date_start,
                            $date_end
                        ]
                    ]
                ]);
                $data[$j] = array(
                    '星期' . ($j + 1),
                    $count
                );
            }
        } elseif ($type == 4) {
            list ($start, $end) = Time::month();
            for ($j = 0; $j < ($end + 1 - $start) / 86400; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                $count = $order->getOrderCount([
                    'shop_id' => $this->instance_id,
                    'create_time' => [
                        'between',
                        [
                            $date_start,
                            $date_end
                        ]
                    ]
                ]);
                $data[$j] = array(
                    (1 + $j) . '日',
                    $count
                );
            }
        }
        return $data;
    }

    /**
     * 商品销售排行
     *
     * @return unknown
     */
    public function getGoodsRealSalesRank()
    {
        $goods = new GoodsService();
        $goods_list = $goods->getGoodsRank(array(
            "shop_id" => $this->instance_id
        ));
        return $goods_list;
    }

    /**
     * 咨询个数
     *
     * @param unknown $condition            
     * @return unknown
     */
    public function getConsultCount()
    {
        $goods = new GoodsService();
        $good_count = $goods->getConsultCount(array(
            "shop_id" => $this->instance_id,
            "consult_reply" => ""
        ));
        return $good_count;
    }

    /**
     * 订单 图表 数据
     */
    public function getWeiXinFansChartCount()
    {
        $type = isset($_POST['date']) ? $_POST['date'] : 1;
        $weixin = new Weixin();
        $data = array();
        if ($type == 1) {
            list ($start, $end) = Time::today();
            for ($i = 0; $i < 24; $i ++) {
                $date_start = date("Y-m-d H:i:s", $start + 3600 * $i);
                $date_end = date("Y-m-d H:i:s", $start + 3600 * ($i + 1));
                $count = $weixin->getWeixinFansCount([
                    'instance_id' => $this->instance_id,
                    'subscribe_date' => [
                        'between',
                        [
                            $date_start,
                            $date_end
                        ]
                    ]
                ]);
                $data[0][$i] = $i . ':00';
                $data[1][$i] = $count;
            }
        } elseif ($type == 2) {
            list ($start, $end) = Time::yesterday();
            for ($j = 0; $j < 24; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 3600 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 3600 * ($j + 1));
                $count = $weixin->getWeixinFansCount([
                    'instance_id' => $this->instance_id,
                    'subscribe_date' => [
                        'between',
                        [
                            $date_start,
                            $date_end
                        ]
                    ]
                ]);
                $data[0][$j] = $j . ':00';
                $data[1][$j] = $count;
            }
        } elseif ($type == 3) {
            list ($start, $end) = Time::week();
            $start = $start - 604800;
            for ($j = 0; $j < 7; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                $count = $weixin->getWeixinFansCount([
                    'instance_id' => $this->instance_id,
                    'subscribe_date' => [
                        'between',
                        [
                            $date_start,
                            $date_end
                        ]
                    ]
                ]);
                $data[0][$j] = '星期' . ($j + 1);
                $data[1][$j] = $count;
            }
        } elseif ($type == 4) {
            list ($start, $end) = Time::month();
            for ($j = 0; $j < ($end + 1 - $start) / 86400; $j ++) {
                $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                $count = $weixin->getWeixinFansCount([
                    'instance_id' => $this->instance_id,
                    'subscribe_date' => [
                        'between',
                        [
                            $date_start,
                            $date_end
                        ]
                    ]
                ]);
                $data[0][$j] = (1 + $j) . '日';
                $data[1][$j] = $count;
            }
        }
        return $data;
    }
}
