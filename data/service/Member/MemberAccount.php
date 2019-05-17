<?php
/**
 * MemberAccount.php
 *
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\service\Member;
/**
 * 会员流水账户
 */
use data\service\BaseService;
use data\model\NsMemberAccountRecordsModel;
use data\model\NsMemberAccountModel;
use data\model\NsPointConfigModel;
use data\model\NsRptPointModel;
use data\service\Config;
use data\service\Member as MemberService;
use data\model\ConfigModel;
use data\service\shopaccount\ShopAccount;
use data\service\UniPoint;
use data\model\NsShopAccountRecordsModel;
use data\model\NsShopModel;
use think\Db;
class MemberAccount extends BaseService
{ 
    function __construct(){
        parent::__construct();
    }
    /**
     * 添加会员消费
     * @param unknown $shop_id
     * @param unknown $uid
     * @param unknown $consum
     */
    public function addMmemberConsum($shop_id, $uid, $consum){
        $account_statistics = new NsMemberAccountModel();
        $acount_info = $account_statistics->getInfo(['uid'=> $uid, 'shop_id' => $shop_id], '*');
        $data = array(
            'member_cunsum' => $acount_info['member_cunsum'] + $consum
        );
        $retval = $account_statistics->save($data, ['uid'=> $uid, 'shop_id' => $shop_id]);
        return $retval;
        
    }

	/**
     * 添加账户流水，增加对应余额、积分等
     */
    public function addMemberAccountData($shop_id, $account_type, $uid, $sign, $number, $from_type, $data_id,$text,$is_add = 1)
    {
         if($account_type == 1)
        {
            $point_config = new NsPointConfigModel();
            $config_info = $point_config->getInfo(['shop_id' => $shop_id], 'is_open');
            /* if($config_info['is_open'] == 0)
            {
                //店铺关闭了积分兑换余额功能
                return CLOSE_POINT;
            } */
            
        }
        
        $member_account = new NsMemberAccountRecordsModel();
		$account_statistics = new NsMemberAccountModel();
        $member_account->startTrans();
        try{
			$data_one = array(
				'shop_id' => $shop_id,
				'account_type' => $account_type,
				'uid' => $uid,
				'sign' => $sign,
				'number' => $number,
				'from_type' => $from_type,
				'data_id' => $data_id,
                'text' => $text,
                'is_add' => $is_add,
				'create_time' => date("Y-m-d H:i:s", time())
			);
			
			//更新对应会员账户
            if($account_type == 1)
            {
                //积分
				$account = $account_statistics->where(['uid'=> $uid, 'shop_id' => $shop_id])->value("point");//从账户取数方式
                //$account = $member_account->where(['uid'=>$uid, 'shop_id' => $shop_id, 'account_type' => $account_type])->sum('number');
                if($account < 0)
                {
                    $member_account->rollback();
                    return LOW_POINT;
                }
				if($is_add > 0){
					$account_statistics->where(['uid'=>$uid])->setInc('point',$number);
				}
            }
            if($account_type == 2)
            {
                //余额
				$account = $account_statistics->where(['uid'=> $uid])->value("balance");//从账户取数比较
				//$account = $member_account->where(['uid'=>$uid, 'shop_id' => 0, 'account_type' => $account_type])->sum('number');
                if($account < 0)
                {
                    $member_account->rollback();
                    return LOW_BALANCE;
                }
				if($is_add > 0){
					$account_statistics->where(['uid'=>$uid])->setInc('balance',$number);
				}
            }
			$retval = $member_account->save($data_one);//加入记录
            $member_account->commit();
            return 1;
        } catch (\Exception $e)
        {
            $member_account->rollback();
            return $e->getMessage();
        }
        // TODO Auto-generated method stub
        
    }

    /**
     * 获取会员在一段时间之内的账户
     * @param unknown $uid
     * @param unknown $account_type
     * @param unknown $start_time
     * @param unknown $end_time
     */
    public function getMemberAccountRecordSum($shop_id = '', $uid, $account_type, $start_time='', $end_time='', $is_add = '')
    {
        $start_time = ($start_time == '') ? '2010-1-1' : $start_time;
        $end_time = ($end_time == '') ? '2099-1-1' : $end_time;
        $condition = array(
            'create_time' => [array('EGT', $start_time),
            array('LT', $end_time)],
            'account_type'=> $account_type,
            'uid'         => $uid
        );
        if($shop_id !== ''){
            $condition['shop_id'] = $shop_id;
        }
        if($is_add !== ''){
            $condition['is_add'] = $is_add;
        }

        $member_account = new NsMemberAccountRecordsModel();
        $account = $member_account->where($condition)->sum('number');
        if(empty($account)){
            $account = 0;
        }
        return $account;
        // TODO Auto-generated method stub
    }
	
    /**
     * 积分转换成余额
     * @param unknown $point    积分
     * @param unknown $convert_rate 积分/余额
     */
    public function pointToBalance($point,$shop_id){
        $point_config = new NsPointConfigModel();
        $convert_rate = $point_config->getInfo(['shop_id'=>$shop_id, 'is_open'=>1],'convert_rate');
        if(!$convert_rate || $convert_rate == ''){
            $convert_rate = 0;
        }
//         var_dump($convert_rate);
        $balance = $point * $convert_rate["convert_rate"];
        return $balance;
    }

    /**
     * 余额兑换为积分
     * @param unknown $balance  余额
     * @param unknown $convert_rate 余额/积分
     */
    public function balanceToPoint($balance,$shop_id){
        $point_config = new NsPointConfigModel();
        $convert_rate = $point_config->getInfo(['shop_id'=>$shop_id, 'is_open'=>1],'convert_rate');
        if(!$convert_rate || $convert_rate == ''){
            $convert_rate = 0;
        }
        //         var_dump($convert_rate);
        $point  = $balance / $convert_rate["convert_rate"];
        return $point;
    }

    /**
     * 获取兑换比例
     * @param unknown $shop_id 店铺名
     */
    public function getConvertRate($shop_id){
        $point_config = new NsPointConfigModel();
        $convert_rate = $point_config->getInfo(['shop_id'=>$shop_id, 'is_open'=>1],'convert_rate');
        return $convert_rate;
    }
    /**
     * 获取购物币余额转化关系
     */
    public function getCoinConvertRate(){
        $config = new ConfigModel();
        $config_rate = $config->getInfo(['key' => 'COIN_CONFIG'], '*');
        if(empty($config_rate))
        {
            return 1;
        }else{
            $rate = json_decode($config_rate['value'], true);
            return $rate['convert_rate'];
        }
    }
    /**
     * 获取会员余额数
     * @param unknown $uid
     */
    public function getMemberBalance($uid)
    {
        $member_account = new NsMemberAccountModel();
        $balance = $member_account->getInfo(['uid'=> $uid, 'shop_id' => 0], 'balance');
        if(!empty($balance))
        {
            return $balance['balance'];
        }else{
            return 0.00;
        }
    }
    /**
     * 获取会员余额数
     * @param unknown $uid
     */
    public function getMemberPointGift($uid)
    {
        $member_account = new NsMemberAccountModel();
        $member_info = $member_account->getInfo(['uid'=> $uid, 'shop_id' => 0], 'point, achieve_point_date');
        if(empty($member_info)){
            return -1;
        }
        if(strtotime($member_info['achieve_point_date']) > strtotime("-1 day",time())){
            return -2;
        }

        $calc_date = date("Y-m-d",time());
		$param=$this->getDayRate($calc_date);//print_r($param);exit;
		$member = new MemberService();//添加1000体验通证积分领取红包功能！
		$experiencePoint=$member->experiencePoint($this->uid);
        return round(($member_info['point']+$experiencePoint) * $param, 2);
    }
    /**
     * 获取用户的通兑积分
     */
    public function getMemberTdPoint($uid)
    {
        $member_account = new NsMemberAccountModel();
        $member_info = $member_account->getInfo(['uid'=> $uid, 'shop_id' => 0], 'exc_point');
        if(empty($member_info)){
            return -1;
        }
        $member_info['exc_point'] = ceil($member_info['exc_point']);
        return $member_info;
    }
	/*获取积分通证系数*/
	public function getDayRate($calc_date){  //首页执行
		$member_account = new NsMemberAccountModel();
		$day_point = (new NsRptPointModel())->getInfo(['create_time' => $calc_date], "r25, r27" );
		if(empty($day_point)){
			$account_records = new NsMemberAccountRecordsModel();
			$C29 = $member_account->getSum(['uid'=>['>', 0]], 'point'); //全部会员积分总数
			
			/*函数简化*/
			$point_day_before_zheng=$account_records->getSum([
					'is_add'=>1,
					'account_type'=>1,
					'create_time'=>[['>=',date('Y-m-d', strtotime("-1 days", strtotime($calc_date)))],['<',$calc_date]],
					'number'=>['>',0]
				], 'number');
			$point_day_before_fu=-1 * $account_records->getSum([
					'is_add'=>1,
					'account_type'=>1,
					'create_time'=>[['>=',date('Y-m-d', strtotime("-1 days", strtotime($calc_date)))],['<',$calc_date]],
					'number'=>['<',0]
				], 'number');
			$data = UniPoint::CalculatePoint($calc_date,$point_day_before_zheng,$point_day_before_fu,$C29);
			
			$day_point = (new NsRptPointModel())->getInfo(['create_time' => $calc_date], "r25, r27" );
        }
        if($day_point['r25'] == 0){
            return 0;
        }
        $param = $day_point['r27']/$day_point['r25'];
		$creats = Db::table('ns_rpt_point')->where('id>0')->max('create_time');
		if($param < 0 || strpos($creats, '-07-') !== false){  //调整为只要不小于0 Will processe
            $param = 0;
        }
        //取最大通证系数
        $value = Db::table('sys_config')
            ->where('key', 'MAXPARAM')
            ->column('value');
		if(!empty($value)){
            if($param > $value[0]){
                $param = $value[0];//公司参数调整最大值为0.004
            }
        }
		
		$shopModel=new NsShopModel();
		$shop_arr=$shopModel->where(["point" => ['>',0]])->field('shop_id,point')->select();
		$condition['data_no'] = 'money_add-'.$calc_date; //rec_type、data_no等标记唯一
		$condition['rec_type'] = 8; //商家自动领取积分通证红包
		$condition['money'] = ['>',0];
		$shopAccountModel = new NsShopAccountRecordsModel();
		$shopAccount=new ShopAccount();
		foreach($shop_arr as $k=>$v){
			$condition['shop_id'] = $v['shop_id'];
			$shop_rec = $shopAccountModel->where($condition)->find();
			if(empty($shop_rec) && $param>0){  
				$shopAccount->addShopAccountRecords(8, $v['shop_id'], 'money_add-'.$calc_date,0, round($v['point']* $param, 2), 0, '商家'.$param.'系数领取积分通证余额红包', 1);//增加余额
				$shopAccount->addShopAccountRecords(8, $v['shop_id'], 'point_reduce-'.$calc_date,0, 0, round($v['point']* $param*(-1 / 6),4), '商家'.$param.'系数领取通证红包扣减积分', 1);//减少积分
			}
		}
		return $param;
	}
    public function addPointGiftToPoint(){
        $member_account_model = new NsMemberAccountModel();
        $point_gift = $this->getMemberPointGift($this->uid);
        if ($point_gift < 0) {
            return $point_gift;
        }
		$member_account_model->where(['uid'=>$this->uid])->setField('achieve_point_date',date('Y-m-d', time()));
		$member = new MemberService();//添加1000体验通证积分领取红包功能！
		$experiencePoint=$member->experiencePoint($this->uid);
		if($point_gift > 0){
			$once=Db::table('ns_member')->where(['uid'=>$this->uid])->field('assign_jplus_time,jplus_level')->find();
			$assign_jplus_time=$once['assign_jplus_time'];
			if(empty(strtotime($assign_jplus_time))){ $is_add=0; } else { $is_add=1; }
			if($experiencePoint==0 && $once['jplus_level']>0){
				$member_account_model->startTrans();
				$retval = $this->addMemberAccountData(0, 1, $this->uid, 1, round($point_gift * -1 / 6,4), 40, 0, '领取通证红包扣减积分',$is_add);
				if ($retval < 0) {
					$member_account_model->rollback();
					return $retval;
				}
				$retval = $this->addMemberAccountData(0, 2, $this->uid, 0, $point_gift, 20, 0, '领取积分通证红包',$is_add);
				if ($retval < 0) {
					$member_account_model->rollback();
					return $retval;
				}
				$member_account_model->commit();
			} else if($experiencePoint>0){
				/*添加1000体验通证积分领取红包功能*/
				Db::table("ns_member_account_records")->insert(["uid"=>$this->uid,"account_type"=>2,"number"=>$point_gift,"from_type"=>31,"text"=>'新注册体验通证红包-'.round($point_gift/6,4),"create_time"=>date("Y-m-d H:i:s", time()),"is_add"=>1]);
				Db::table("ns_member_account")->where(["uid"=>$this->uid])->setInc('balance',$point_gift);//增加余额
				/*1000体验通证积分领取红包功能结束*/
			}
		}
        return 1;
    }
    /**
 * 获取会员购物币
 * @param unknown $uid
 * @return unknown|number
 */
    public function getMemberCoin($uid)
    {
        $member_account = new NsMemberAccountModel();
        $coin = $member_account->getInfo(['uid'=> $uid, 'shop_id' => 0], 'coin');
        if(!empty($coin))
        {
            return $coin['coin'];
        }else{
            return 0.00;
        }
    }
    /**获取用户的推广积分
     * @param unknown $uid
     * @return unknown|number
     */
    public function getMemberIsReceivE($uid)
    {
        $member_account = new NsMemberAccountModel();
        $is_receive = $member_account->getInfo(['uid'=> $uid, 'shop_id' => 0], 'is_receive');
        if(!empty($is_receive))
        {
            return $is_receive['is_receive'];
        }else{
            return 0;
        }
    }
    public function getMemberPoint($uid, $shop_id='')
    {
        $member_account = new NsMemberAccountModel();
        if($shop_id == '')
        {
            //查询积分
            //$point = $member_account->where(['uid'=> $uid])->sum('point');
			$point = $member_account->getInfo(['uid'=> $uid], 'point');//变更为只查询唯一的积分账户
            if(!empty($point))
            {
                return $point;//此处涉及几处调用,不可改动
            }else{
                return 0;
            }
        }else{
            $point = $member_account->getInfo(['uid'=> $uid, 'shop_id' => $shop_id], 'point');
            if(!empty($point))
            {
                return $point['point'];
            }else{
                return 0;
            }
        }
    }
    public static function getMemberAccountRecordsName($from_type)
    {
        switch($from_type)
        {
       
                case 1:
                    $type_name = '商城订单';
                break;
                case 2:
                    $type_name = '订单退还';
                break;
                case 3:
                    $type_name = '兑换';
                    break;
                case 4:
                    $type_name = '充值';
                    break;
                case 5:
                    $type_name = '签到';
                    break;
                    
                case 6:
                    $type_name = '分享';
                    break;
                case 7:
                    $type_name = '注册';
                    break;
                case 8:
                    $type_name = '提现';
                    break;
                case 9:
                    $type_name = '提现退还';
                    break;
                case 10:
                    $type_name = '调整';
                    break;
                case 15:
                    $type_name = '分销分红';
                    break;
                case 20:
                    $type_name = '领取积分通证红包';
                    break;
            case 25:
                $type_name = '购买会员';
                break;
            case 30:
                $type_name = '转赠';
                break;
            case 35:
                $type_name = '消费奖励积分';
                break;
            case 40:
                $type_name = '通证红包积分扣减';
                break;
            case 55:
                $type_name = '推广返利积分';
                break;
            case 60:
                $type_name = '平台赠送推广积分100';
                break;
                default:
                    $type_name = '';
                    break;
        }
        return $type_name;

    }

    public static function getMemberAccountRecordsIsAddName($is_add)
    {
        switch($is_add)
        {
            case 0:
                $is_add_name = '预计获得';
                break;
            case 1:
                $is_add_name = '操作成功';
                break;
            default:
                $is_add_name = '';
                break;
        }
        return $is_add_name;
    }
}