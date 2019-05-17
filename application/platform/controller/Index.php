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
namespace app\platform\controller;

use data\service\Goods as GoodsService;
use data\service\Order as OrderService;
use data\service\User as User;
use data\service\Weixin;
use think\helper\Time;
use data\service\Shop;
use data\service\Goods;
use think\Db;
use data\service\UniPoint;
use data\model\NsMemberAccountModel;
use data\model\NsMemberAccountRecordsModel;
use data\model\NsShopAccountRecordsModel;
use data\model\NsShopModel;
use data\model\NsRptPointModel;
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

   public function index()
    {
        $debug = config('app_debug') == true ? '开发者模式':'部署模式';
        $this->assign('debug',$debug);
        $main = \think\Request::instance()->domain();
        // var_dump(\think\Request::instance()->header());
        //顶部导航统计数据
        $sale_data = $this->getIndexCount();
        $this->assign("sale_data",$sale_data);
        //订单统计数据
        $order_data = $this->getOrderCount();
        $this->assign("order_data",$order_data);
        return view($this->style.'Index/index');
    }
    /**
     * 
     * 积分通证数据
     * 
     */
    public function pointPassCard()
    {
		$today = date('Y-m-d',time());
		$member_account = new NsMemberAccountModel();
		$account_records = new NsMemberAccountRecordsModel();
		$shop_records = new NsShopAccountRecordsModel();
		$shop = new NsShopModel();
		$now_tot_point = $member_account->getSum(['uid'=>['>', 0]], 'point');
		$now_shop_point = $shop->getSum(['shop_id'=>['>=', 0]], 'point');
		//$now_shop_extendpoint = $shop->getSum(['shop_id'=>['>=', 0]], 'extend_point');
		//print_r($now_shop_extendpoint);exit;
		//$now_tot_extendpoint = $member_account->getSum(['uid'=>['>', 0]], 'extend_point');
		$today_point=$now_tot_point+$now_shop_point;
		$this->assign("today_point",$today_point);
		$today_change_point=$account_records->getSum([
				'is_add'=>1,
				'account_type'=>1,
				'create_time'=>[['>=',date('Y-m-d', strtotime("-1 days", strtotime($today)))],['<',date('Y-m-d H:i:s', time())]],
			], 'number');
		$today_shopchange_point=$shop_records->getSum([
				'is_add'=>1,  //店铺积分变化
				'create_time'=>[['>=',date('Y-m-d', strtotime("-1 days", strtotime($today)))],['<',date('Y-m-d H:i:s', time())]],
			], 'point');  //包含推广积分
		$yesterday_point=$today_point-$today_change_point-$today_shopchange_point;
		$this->assign("yesterday_point",$yesterday_point);
		/*积分资产结束*/
	   /*积分支付核销开始 以下未含推广积分extend_point*/
	   $today_pay_point=-$account_records->getSum(['is_add'=>1,'from_type'=>1,'account_type'=>1,'create_time'=>[['>=',date('Y-m-d 00:00:00',time())],['<',date('Y-m-d H:i:s', time())]],'number'=>[['<',0]],], 'number');
	   $this->assign("today_pay_point",$today_pay_point);
	   $yesterday_pay_point=-$account_records->getSum(['is_add'=>1,'from_type'=>1,'account_type'=>1,'create_time'=>[['>=',date('Y-m-d', strtotime("-1 days", strtotime($today)))],['<',date('Y-m-d 23:59:59',strtotime("-1 days", strtotime($today)))]],'number'=>[['<',0]]], 'number');
	   $tot_pay_point=-$account_records->getSum(['is_add'=>1,'from_type'=>1,'account_type'=>1,'number'=>[['<',0]]], 'number');
	   $this->assign("yesterday_pay_point",$yesterday_pay_point);
	   $this->assign("tot_pay_point",$tot_pay_point);
	   /*积分支付核销结束*/
	   /*积分通证核销开始*/
	   $today_tongzheng_point=-$account_records->getSum(['is_add'=>1,'from_type'=>40,'account_type'=>1,'create_time'=>[['>=',date('Y-m-d 00:00:00',time())],['<',date('Y-m-d H:i:s', time())]],'number'=>[['<',0]],], 'number');
	   $this->assign("today_tongzheng_point",$today_tongzheng_point);
	   $yesterday_tongzheng_point=-$account_records->getSum(['is_add'=>1,'from_type'=>40,'account_type'=>1,'create_time'=>[['>=',date('Y-m-d', strtotime("-1 days", strtotime($today)))],['<',date('Y-m-d 23:59:59',strtotime("-1 days", strtotime($today)))]],'number'=>[['<',0]],], 'number');
	   $this->assign("yesterday_tongzheng_point",$yesterday_tongzheng_point);
	   $tot_tongzheng_point=-$account_records->getSum(['is_add'=>1,'from_type'=>40,'account_type'=>1,'number'=>[['<',0]],], 'number');
	   $this->assign("tot_tongzheng_point",$tot_tongzheng_point);
	   /*积分通证核销结束*/
	   /*积分核销开始*/
	   //客户积分核销  以上要补加商家积分
	   $this->assign("tot_point",$tot_tongzheng_point+$tot_pay_point);
	   $this->assign("yesterday_tot_point",$yesterday_pay_point+$yesterday_tongzheng_point);
	   $this->assign("today_user_point",$today_pay_point+$today_tongzheng_point);
	   //商户积分核销
	   $today_shop_point=Db::table('ns_shop_account_records')->where(['is_add'=>1,'rec_type'=>8,'create_time'=>[['>=',date('Y-m-d 00:00:00',time())],['<',date('Y-m-d H:i:s', time())]],'point'=>[['<',0]]])->sum('point');
	   $this->assign("today_shop_point",-$today_shop_point);
	   /*积分核销结束*/
        /*通证分配资金开始*/
        $tz_user_money = Db::table('ns_member_account_records')
            ->where(['from_type'=>20, 'account_type'=>2, 'is_add'=>1])
            ->field('number, create_time')
            ->select();
        $user_money = 0;//用户分配的总额
        $yesterday_user_money = 0;//昨日用户分配总额
        foreach ($tz_user_money as $val){
            $user_money += $val['number'];
            if(date('Y-m-d H:i:s', strtotime("-1 days", strtotime($today)))<$val['create_time'] && $val['create_time']<date('Y-m-d 23:59:59',strtotime("-1 days", strtotime($today)))){
                $yesterday_user_money += $val['number'];
            }
        }
        $tz_shop_money = Db::table('ns_shop_account_records')
            ->where(['rec_type'=>8, 'point'=>0])
            ->field('money, create_time')
            ->select();
        $shop_money = 0;
        $yesterday_shop_money = 0;
        foreach ($tz_shop_money as $v){
            $shop_money += $v['money'];
            if(date('Y-m-d H:i:s', strtotime("-1 days", strtotime($today)))<$v['create_time'] && $val['create_time']<date('Y-m-d 23:59:59',strtotime("-1 days", strtotime($today)))){
                $yesterday_shop_money += $v['money'];
            }
        }
        $tot_money = $user_money + $shop_money;//总分配额
        $yesterday__money = $yesterday_shop_money + $yesterday_user_money;
        $this->assign("tot_money",$tot_money);
        $this->assign("yesterday__money",$yesterday__money);
        $this->assign("user_money",$user_money);
        $this->assign("shop_money",$shop_money);
        /*通证分配资金结束*/
        /*计算备付金*/
        $data = $this->getIndexCount();
        $interest_margin=$data["interest_margin"]*0.49*0.7;//总备付金
        $margin_today=$data["margin_today"]*0.49*0.7;//今日备付金
        $margin_yesterday=$data["margin_yesterday"]*0.49*0.7;//昨日备付金
        $this->assign("interest_margin",$interest_margin);
        $this->assign("margin_today",$margin_today);
        $this->assign("margin_yesterday",$margin_yesterday);
        /*计算集采备付金*/
        /*获取通证系数*/
        $calc_date = date('Y-m-d 00:00:00');
        $day_point = Db::table('ns_rpt_point')
            ->where(['create_time'=>$calc_date])
            ->field('r25, r27')
            ->find();
        if($day_point['r25']!=0){
            $param =  sprintf("%.4f", $day_point['r27']/$day_point['r25']);
        }
        $this->assign("param",$param);
        //取最大通证系数
        $value = Db::table('sys_config')
            ->where('key', 'MAXPARAM')
            ->column('value');
        $this->assign('value', $value);
        return view($this->style.'Index/pointPassCard');
    }
    /**
     *设置最大系数值
     * zxl
     */
    public function setMaxParam()
    {
        if (request()->isAjax()) {
            $param = isset($_POST['param']) ? $_POST['param'] : 0;
            $date = date('Y-m-d H:i:s');
            $resault = Db::table('sys_config')
                ->where('key', 'MAXPARAM')
                ->find();
            if(empty($resault)){
                $data = ['instance_id'=>0, 'key'=>'MAXPARAM', 'value'=>$param, 'create_time'=>$date, 'modify_time'=>'0000-00-00 00:00:00', 'desc'=>'积分通证最大系数', 'is_use'=>1];
                $res = Db::table('sys_config')
                    ->insert($data);
            }else{
                $data = ['value'=>$param, 'modify_time'=>$date];
                $res = Db::table('sys_config')
                    ->where('key', 'MAXPARAM')
                    ->update($data);
            }
        }
        $value = Db::table('sys_config')
            ->where('key', 'MAXPARAM')
            ->find('value');
        $this->assign('value', $value);
    }
    /**
     * ajax 加载 店铺 会员 信息
     */
    public function getUserInfo(){
        $auth = new User();
        $user_info = $auth->getUserDetail($this->uid);
        return $user_info;
    }
    /**
     * 获取 商品 数量       全部    出售中  已审核  已下架   
     */
    public function getGoodsCount(){
        $goods_count = new GoodsService();
        $goods_count_array = array();
        //全部
        $goods_count_array['all'] = $goods_count->getGoodsCount(['shop_id'=>$this->instance_id]);
        //出售中
        $goods_count_array['sale'] = $goods_count->getGoodsCount(['shop_id'=>$this->instance_id,'state'=>1]);
        //下架
        $goods_count_array['shelf'] = $goods_count->getGoodsCount(['shop_id'=>$this->instance_id,'state'=>0]);
        return $goods_count_array;
    }
    /**
     * 获取 订单数量     代付款  待发货  已发货    已收货    已完成  已关闭     退款中   已退款
     */
    public function getOrderCount(){
        $order = new OrderService();
        $order_count_array = array();
        $order_count_array['daifukuan'] = $order->getOrderCount(['order_status'=>0]);//代付款
        $order_count_array['daifahuo'] = $order->getOrderCount(['order_status'=>1]);//代发货
        $order_count_array['yifahuo'] = $order->getOrderCount(['order_status'=>2]);//已发货
        $order_count_array['yishouhuo'] = $order->getOrderCount(['order_status'=>3]);//已收货
        $order_count_array['yiwancheng'] = $order->getOrderCount(['order_status'=>4]);//已完成
        $order_count_array['yiguanbi'] = $order->getOrderCount(['order_status'=>5]);//已关闭
        $order_count_array['tuikuanzhong'] = $order->getOrderCount(['order_status'=>-1]);//退款中
        $order_count_array['yituikuan'] = $order->getOrderCount(['order_status'=>-2]);//已退款
        $order_count_array['all'] = $order->getOrderCount('');//全部
        return $order_count_array;
    }
    public function getSalesStatistics(){
        $order = new OrderService();
        $condition['shop_id'] = $this->instance_id;
        $condition['order_status'] = ['>',1];
        
        $data['yesterday_money'] = $order->getPayMoneySum($condition, 'yesterday');
        $data['month_money'] = $order->getPayMoneySum($condition, 'month');
        $data['yesterday_goods'] = $order->getGoodsNumSum($condition, 'yesterday');
        $data['month_goods'] = $order->getGoodsNumSum($condition, 'month');
        return $data;
    }
    /**
     * 订单 图表 数据
     */
    public function getOrderChartCount(){
        $order = new OrderService();
        $data = array(); 
        list($start, $end) = Time::month();
        for($j=0;$j<($end+1-$start)/86400;$j++){
            $date_start = date("Y-m-d H:i:s",$start+86400*$j);
            $date_end = date("Y-m-d H:i:s",$start+86400*($j+1));
            $count = $order->getOrderCount(['shop_id'=>$this->instance_id,'create_time'=>['between',[$date_start,$date_end]]]);
            $data[$j] = array(
                (1+$j).'日',$count
            );
        }
        
        return $data;
    }
    /**
     *销售统计
     */
    public function getIndexCount(){
        $start_today = strtotime(date('Y-m-d 00:00:00', time()));
        $end_today = strtotime(date('Y-m-d 00:00:00', strtotime('this day + 1 day')));
        $start_yesterday=strtotime(date('Y-m-d 00:00:00', strtotime('this day - 1 day')));
        //本日销售额
        $sale_money_today = 0; //$order->getShopSaleSum($condition);原来的不对
        $sale_total = 0; //总销售
        $sale_yesterday = 0; //昨天的销售
        $interest_margin=0; // 总利差
        $margin_today=0; //今天利差
        $margin_yesterday=0; //昨天利差
        $order_number_tot=0;//订单笔数
        $order_number_today=0;//今天订单笔数
        $order_number_yesterday=0;//昨天订单笔数
        $group_id_array = 0;//商品类型
        $condition['order_status'] = ['<>',5];
        $condition['pay_status'] = 2;
        $order_arr=Db::table('ns_order')->where($condition)->field('order_id,buyer_id,goods_money,order_money,refund_money,create_time')->select();
            foreach($order_arr as $k=>$v){
                /*总数*/
            if($v['order_money']==0){ //此种情况下是购买会员的,选购会员商品
                $sale_total=$sale_total+$v['goods_money']-$v['refund_money'];
            }else{ //此种情况是购买商品
                $sale_total=$sale_total+$v['order_money']-$v['refund_money'];
            }

            $order_number_tot++;
            /*计算利差*/
            $finalcost=0;	//总成本
            $goods_arr=Db::table('ns_order_goods')->where('order_id',$v['order_id'])->field('goods_id,num,cost_price,refund_status')->select();
            foreach($goods_arr as $key=>$val){
                if($val['refund_status'] != 5){
                    $logistics=Db::table('ns_goods')->where('goods_id',$val['goods_id'])->value('cost_price_logistics');
//                    $group_id_array = Db::table('ns_goods')->where('goods_id',$val['goods_id'])->value('group_id_array');
//                    $total_cost_price = Db::table('ns_goods')->where('goods_id',$val['goods_id'])->value('total_cost_price');

                    $finalcost=$finalcost+($logistics+$val['cost_price'])*$val['num']; //按商品计算邮费？将来调整


                }
            }
            if($v['order_money'] ==0){
                $interest_margin=$interest_margin+($v['goods_money']-$v['refund_money'])-$finalcost;
            }else{
                $interest_margin=$interest_margin+($v['order_money']-$v['refund_money'])-$finalcost;
            }

            $createtime=strtotime($v['create_time']);
            /*今天*/
            if($start_today < $createtime && $end_today > $createtime){
                if($v['order_money']==0){
                    $sale_money_today=$sale_money_today+($v['goods_money']-$v['refund_money']);
                    $margin_today=$margin_today+$v['goods_money']-$v['refund_money']-$finalcost;
                }else{
                    $sale_money_today=$sale_money_today+($v['order_money']-$v['refund_money']);
                    $margin_today=$margin_today+$v['order_money']-$v['refund_money']-$finalcost;
                }

                $order_number_today++;
            }
            /*昨天*/
            if($start_yesterday < $createtime && $start_today > $createtime){
                if($v['order_money']==0){
                    $sale_yesterday=$sale_yesterday+$v['goods_money']-$v['refund_money'];
                    $margin_yesterday=$margin_yesterday+$v['goods_money']-$v['refund_money']-$finalcost;
                }else{
                    $sale_yesterday=$sale_yesterday+$v['order_money']-$v['refund_money'];
                    $margin_yesterday=$margin_yesterday+$v['order_money']-$v['refund_money']-$finalcost;
                }
                
                $order_number_yesterday++;
            }
        }
        $mem_tot=0;//总会员
        $mem_today=0;//今日注册会员
        $mem_yesterday=0;//昨天注册会员
        $user_arr=Db::table('sys_user')->where('is_system',0)->field('reg_time')->select();
        foreach($user_arr as $k=>$v){
            /*总数*/
            $mem_tot++;
            $reg_time=strtotime($v['reg_time']);
            /*今天*/
            if($start_today < $reg_time && $end_today > $reg_time){
                $mem_today++;
            }
            /*昨天*/
            if($start_yesterday < $reg_time && $start_today > $reg_time){
                $mem_yesterday++;
            }
        }
        $jplus_arr=Db::table('ns_member')->where('assign_jplus_time','neq','0000-00-00 00:00:00')->field('assign_jplus_time,jplus_level')->select();
        $jplus_tot=0;//总数
        $jplus_data=['399_yesterday'=>0,'4998_yesterday'=>0,'399_today'=>0,'4998_today'=>0];
        foreach($jplus_arr as $k=>$v){
            $jplus_tot++;
            $regtime=strtotime($v['assign_jplus_time']);
            /*今天*/
            if($start_today < $regtime && $end_today > $regtime){
                if($v['jplus_level']==10){
                    $jplus_data['399_today']++;
                } else { $jplus_data['4998_today']++; }
            }
            /*昨天*/
            if($start_yesterday < $regtime && $start_today > $regtime){
                if($v['jplus_level']==10){
                    $jplus_data['399_yesterday']++;
                } else { $jplus_data['4998_yesterday']++; }
            }
        }
        //$shop = new Shop();
        //入驻店铺数
        $shop_arr=Db::table('ns_shop')->field('shop_create_time')->select();
        $shop_tot=0;//总店铺数
        $shop_today=0;//今日注册店铺数
        $shop_yesterday=0;//昨天注册店铺数
        foreach($shop_arr as $k=>$v){
            /*总数*/
            $shop_tot++;
            $shop_create_time=strtotime($v['shop_create_time']);
            /*今天*/
            if($start_today < $shop_create_time && $end_today > $shop_create_time){
                $shop_today++;
            }
            /*昨天*/
            if($start_yesterday < $shop_create_time && $start_today > $shop_create_time){
                $shop_yesterday++;
            }
        }
        //$shop_num = $shop->getShopCount('');
        //关注用户数
        $weixin = new Weixin();
        $fans_num = $weixin->getWeixinFansCount('');
        $result = array(
            "sale_money_today"=>$sale_money_today,
            "sale_total"=>$sale_total,
            "sale_yesterday"=>$sale_yesterday,
            "interest_margin"=>$interest_margin,
            "margin_today"=>$margin_today,
            "margin_yesterday"=>$margin_yesterday,
            "order_number_tot"=>$order_number_tot,
            "order_number_today"=>$order_number_today,
            "order_number_yesterday"=>$order_number_yesterday,
            "mem_tot"=>$mem_tot,
            "mem_today"=>$mem_today,
            "mem_yesterday"=>$mem_yesterday,
            "jplus_data"=>$jplus_data,
            "jplus_tot"=>$jplus_tot,
            "shop_tot"=>$shop_tot,
            "shop_today"=>$shop_today,
            "shop_yesterday"=>$shop_yesterday,
            "fans_num"=>$fans_num,
            "assistant_num"=>0,
            "commission"=>sprintf('%.2f', 0)
        );
        return $result;
    }
    
    /**
     * 注册会员图表数据
     */
    public function getUserRegChartCount()
    {
        $user = new User();
        $data = array();
        list ($start, $end) = Time::month();
        for ($j = 0; $j < ($end + 1 - $start) / 86400; $j ++) {
                            $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
                            $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
                            $count = $user->getUserCount([
                                'reg_time' => [
                                    'between',
                                    [
                                        $date_start,
                                        $date_end
                                    ]
                                ]
                            ]);
                            $data[0][$j] =  (1 + $j) . '日';
                            $data[1][$j] = $count;
         }                    
         return $data;
    }
    
    /**
     * 商品销售额chart数据
     * @return multitype:multitype:unknown
     */
    public function getGoodsSalesChartCount(){ 
        list ($start, $end) = Time::month();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);        
        $condition=array();        
        $order = new OrderService();
        $goods_list= $order->getShopGoodsSalesQuery($this->instance_id, $start_date, $end_date, $condition);    
        $sort_array = array();
            foreach($goods_list as $k=>$v ){
                $sort_array[$v["goods_name"]] = $v["sales_money"];
            }
            arsort($sort_array);
            $sort = array();
            $num = array();
            $i = 0;
            foreach($sort_array as $t=>$b){
                if($i < 30){
                    $sort[] = $t;
                    $num[] = $b;
                    $i++;
    
                }else{
                    break;
                }
            }
            return array($sort,$num );
    }
    /**
     * 入驻店铺数统计
     */
    public function getShopCreateNumChartCount(){
        $shop = new Shop();
        //入驻店铺数
        $data = array();
        list ($start, $end) = Time::month();
        for ($j = 0; $j < ($end + 1 - $start) / 86400; $j ++) {
            $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
            $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
            $count = $shop->getShopCount([
                'shop_create_time' => [
                    'between',
                    [
                        $date_start,
                        $date_end
                    ]
                ]
            ]);
            $data[0][$j] =  (1 + $j) . '日';
            $data[1][$j] = $count;
        }
        return $data;
    }
    /**
     * 商品添加统计
     * @return Ambigous <multitype:, unknown>
     */
    public function getGoodsCreateChartCount(){
        $goods = new Goods();
        $data = array();
        list ($start, $end) = Time::month();
        for ($j = 0; $j < ($end + 1 - $start) / 86400; $j ++) {
            $date_start = date("Y-m-d H:i:s", $start + 86400 * $j);
            $date_end = date("Y-m-d H:i:s", $start + 86400 * ($j + 1));
            $count = $goods->getGoodsCount([
                'create_time' => [
                    'between',
                    [
                        $date_start,
                        $date_end
                    ]
                ]
            ]);
            $data[0][$j] =  (1 + $j) . '日';
            $data[1][$j] = $count;
        }
        return $data;
    }
    /**
     * 商品添加统计详情
     */
    public function getGoodsCreateCount(){
        $goods = new Goods();
        //今日商品添加
        list ($start, $end) = Time::today();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        //本日添加商品数
        $num_day = $goods->getGoodsCount(['create_time' => ['between',[$start_date,$end_date]]]);
        //昨天添加商品数
        list ($start, $end) = Time::yesterday();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        $num_yesterday = $goods->getGoodsCount(['create_time' => ['between',[$start_date,$end_date]]]);
        //本月
        list ($start, $end) = Time::month();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        $num_month = $goods->getGoodsCount(['create_time' => ['between',[$start_date,$end_date]]]);
        //总计
        $num_count = $goods->getGoodsCount('');
        $result = array(
            "num_day"=>$num_day,
            "num_yesterday"=>$num_yesterday,
            "num_month"=>$num_month,
            "num_count"=>$num_count
        );
        return $result;
    }
    /**
     * 会员注册统计详情
     */
    public function getUserRegCount(){
        $user = new User();
        //今日商品添加
        list ($start, $end) = Time::today();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        //本日添加商品数
        $num_day = $user->getUserCount(['reg_time' => ['between',[$start_date,$end_date]]]);
        //昨天添加商品数
        list ($start, $end) = Time::yesterday();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        $num_yesterday = $user->getUserCount(['reg_time' => ['between',[$start_date,$end_date]]]);
        //本月
        list ($start, $end) = Time::month();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        $num_month = $user->getUserCount(['reg_time' => ['between',[$start_date,$end_date]]]);
        //总计
        $num_count = $user->getUserCount('');
        $result = array(
            "user_num_day"=>$num_day,
            "user_num_yesterday"=>$num_yesterday,
            "user_num_month"=>$num_month,
            "user_num_count"=>$num_count
        );
        return $result;
    }
    /**
     * 店铺入驻统计详情
     */
    public function getShopCreateCount(){
        $shop = new Shop();
        //今日商品添加
        list ($start, $end) = Time::today();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        //本日添加商品数
        $shop_num_day = $shop->getShopCount(['shop_create_time' => ['between',[$start_date,$end_date]]]);
        //昨天添加商品数
        list ($start, $end) = Time::yesterday();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        $shop_num_yesterday = $shop->getShopCount(['shop_create_time' => ['between',[$start_date,$end_date]]]);
        //本月
        list ($start, $end) = Time::month();
        $start_date = date("Y-m-d H:i:s", $start);
        $end_date = date("Y-m-d H:i:s", $end);
        $shop_num_month = $shop->getShopCount(['shop_create_time' => ['between',[$start_date,$end_date]]]);
        //总计
        $shop_num_count = $shop->getShopCount('');
        $result = array(
            "shop_num_day"=>$shop_num_day,
            "shop_num_yesterday"=>$shop_num_yesterday,
            "shop_num_month"=>$shop_num_month,
            "shop_num_count"=> $shop_num_count
        );
        return $result;
    }
    
}
