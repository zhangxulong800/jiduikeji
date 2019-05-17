<?php
/**
 * Member.php
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
use data\service\NfxPromoter;
use data\service\NfxShopConfig;
use data\service\NfxUser as NfxUserService;
use data\service\Member\MemberAccount;
use data\service\Member as MemberService;
use data\service\promotion\GoodsGift;
use data\service\Platform;
use data\service\Shop;
use data\service\WebSite;
use data\service\Weixin;
use think\Request;
use data\service\Goods;
use data\service\Order\Order;
use data\service\Order as OrderService;
use data\service\Order\OrderGoods;
use data\service\promotion\PromoteRewardRule;
use think;
use think\Session;
use data\service\Promotion;
use data\service\UnifyPay;
use data\service\Config;
use data\model\NsMemberModel as NsMemberModel;
use think\Db;
use data\model\UserModel as UserModel;
use data\service\Events;

/**
 * 会员
 *
 * @author Administrator
 *        
 */
class Member extends BaseController
{
    public $notice;
    public $login_verify_code;
    
    public function __construct()
    {
        parent::__construct();
        if (empty($this->uid)) {
            $this->wchatLogin();
        }
        $this->checkLogin();
        // 是否开启验证码
        $web_config = new Config();
        $this->login_verify_code = $web_config->getLoginVerifyCodeConfig($this->instance_id);
        $this->assign("login_verify_code", $this->login_verify_code["value"]);
        // 是否开启通知
        $instance_id = 0;
        $web_config = new Config();
        $noticeMobile = $web_config->getNoticeMobileConfig($instance_id);
        $noticeEmail = $web_config->getNoticeEmailConfig($instance_id);
        $this->notice['noticeEmail'] = $noticeEmail[0]['is_use'];
        $this->notice['noticeMobile'] = $noticeMobile[0]['is_use'];
        $this->assign("notice", $this->notice);
    }

    /**
     * 检测用户
     */
    private function checkLogin()
    {
        $uid = $this->uid;
        if (empty($uid)) {
            $this->redirect("login/index"); // 用户未登录
        }
        $is_member = $this->user->getSessionUserIsMember();
        if (empty($is_member)) {
            $this->redirect("Login/index"); // 当前用户不是前台会员
        }
    }

            /**
     * 用户首页
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
        public function index()
        {
            $events=new Events();
            $events->ordersComplete();
            if(!empty($this->uid)){
                $oneuser=Db::table('ns_member')->where(['uid'=>$this->uid])->find();
                //判断2019.5.1后新增邀请到的会员，按条件赠送通兑积分
                $cond2['reg_time']=array('>=','2019-05-01');
                $cond2['pid']=$this->uid;
                $soncount=Db::table('ns_member')->where($cond2)->count();
                if($soncount>=50){
                    $oneGivePoint=Db::table("ns_give_point_records")->where(["uid"=>$this->uid,"number"=>1000,"notes"=>'50SonUser'])->find();
                    if(empty($oneGivePoint)){
                        Db::table("ns_member_account")->where(["uid"=>$this->uid])->setInc('exc_point',1000);//拉人的上级赠送1000通兑积分
                        Db::table("ns_give_point_records")->insert(["uid"=>$this->uid,"number"=>1000,"notes"=>'50SonUser',"text"=>'邀请满50人获赠1000通兑积分',"create_time"=>date("Y-m-d H:i:s", time())]);
                        //另送体验399会员资格7天
                        if($oneuser['jplus_level']==0){
                            Db::table('ns_member')->where(['uid'=>$this->uid])->update(['memo'=>'jplusEndTime_'.(time()+7*24*60*60),'jplus_level'=>10]);
                        }
                    }
                } else if($soncount>=40){
                    $oneGivePoint=Db::table("ns_give_point_records")->where(["uid"=>$this->uid,"number"=>800,"notes"=>'40SonUser'])->find();
                    if(empty($oneGivePoint)){
                        Db::table("ns_member_account")->where(["uid"=>$this->uid])->setInc('exc_point',800);//拉人的上级赠送800通兑积分
                        Db::table("ns_give_point_records")->insert(["uid"=>$this->uid,"number"=>800,"notes"=>'40SonUser',"text"=>'邀请满40人获赠800通兑积分',"create_time"=>date("Y-m-d H:i:s", time())]);
                        //另送体验399会员资格5天
                        if($oneuser['jplus_level']==0){
                            Db::table('ns_member')->where(['uid'=>$this->uid])->update(['memo'=>'jplusEndTime_'.(time()+5*24*60*60),'jplus_level'=>10]);
                        }
                    }
                } else if($soncount>=20){
                    $oneGivePoint=Db::table("ns_give_point_records")->where(["uid"=>$this->uid,"number"=>400,"notes"=>'20SonUser'])->find();
                    if(empty($oneGivePoint)){
                        Db::table("ns_member_account")->where(["uid"=>$this->uid])->setInc('exc_point',400);//拉人的上级赠送400通兑积分
                        Db::table("ns_give_point_records")->insert(["uid"=>$this->uid,"number"=>400,"notes"=>'20SonUser',"text"=>'邀请满20人获赠400通兑积分',"create_time"=>date("Y-m-d H:i:s", time())]);
                        //另送体验399会员资格5天
                        if($oneuser['jplus_level']==0){
                            Db::table('ns_member')->where(['uid'=>$this->uid])->update(['memo'=>'jplusEndTime_'.(time()+5*24*60*60),'jplus_level'=>10]);
                        }
                    }
                } else if($soncount>=10){
                    $oneGivePoint=Db::table("ns_give_point_records")->where(["uid"=>$this->uid,"number"=>210,"notes"=>'10SonUser'])->find();
                    if(empty($oneGivePoint)){
                        Db::table("ns_member_account")->where(["uid"=>$this->uid])->setInc('exc_point',210);//拉人的上级赠送210通兑积分
                        Db::table("ns_give_point_records")->insert(["uid"=>$this->uid,"number"=>210,"notes"=>'10SonUser',"text"=>'邀请满10人获赠210通兑积分',"create_time"=>date("Y-m-d H:i:s", time())]);
                        //另送体验399会员资格3天
                        if($oneuser['jplus_level']==0){
                            Db::table('ns_member')->where(['uid'=>$this->uid])->update(['memo'=>'jplusEndTime_'.(time()+3*24*60*60),'jplus_level'=>10]);
                        }
                    }
                } else if($soncount>=5){
                    $oneGivePoint=Db::table("ns_give_point_records")->where(["uid"=>$this->uid,"number"=>100,"notes"=>'5SonUser'])->find();
                    if(empty($oneGivePoint)){
                        Db::table("ns_member_account")->where(["uid"=>$this->uid])->setInc('exc_point',100);//拉人的上级赠送100通兑积分
                        Db::table("ns_give_point_records")->insert(["uid"=>$this->uid,"number"=>100,"notes"=>'5SonUser',"text"=>'邀请满5人获赠100通兑积分',"create_time"=>date("Y-m-d H:i:s", time())]);
                        //另送体验399会员资格2天
                        if($oneuser['jplus_level']==0){
                            Db::table('ns_member')->where(['uid'=>$this->uid])->update(['memo'=>'jplusEndTime_'.(time()+2*24*60*60),'jplus_level'=>10]);
                        }
                    }
                }
                //处理赠送jplus到期问题 未测试;变更满3天后未购买的jplus会员属性
                $must_do=0;
                $newmemo=Db::table('ns_member')->where(['uid'=>$this->uid])->value('memo');
                $memoarr=explode('_',$newmemo);
                if(empty(strtotime($oneuser['assign_jplus_time'])) && (time()-strtotime($oneuser['reg_time'])) > 3*24*60*60){$must_do=1;}//现改为3天的时效
                if($memoarr[0]=='jplusEndTime' && $memoarr[1]<time()){$must_do=1;} //必须是jplusEndTime情形
                if($memoarr[0]=='jplusEndTime' && $memoarr[1]>time()){$must_do=0;}  //注册后未购买jplus但存在拉人因此保留有效期
                if($must_do==1){
                    Db::table('ns_member')->where(['uid'=>$this->uid])->update(['memo'=>'','jplus_level'=>0]);
                    $conditon['uid']=$this->uid;
                    $conditon['is_add']=0;
                    $conditon['from_type'] = array('in','15,35,20,40,55'); //25不包括，未购买删除预先生成的记录
                    $user_account_arr=Db::table('ns_member_account_records')->where($conditon)->field('id')->select();
                    foreach($user_account_arr as $k=>$v){
                        Db::table('ns_member_account_records')->where('id',$v['id'])->delete();
                    }
                }
            }
            $retval = $this->memberIndex();

            return $retval;
        }
        /**
         * 积分规则
         *
         * */
        public function integralRule()
        {
            return view($this->style . '/Member/integralRule');
        }

        /**
         * 首页积分规则
         *
         * */
        public function pointRule()
        {
            return view($this->style . '/Member/pointRule');
        }
        /**
         * 新人专享海报页
         */
        public function newMember()
        {
            if(!empty($this->uid)){
				$address=Db::table('ns_member_express_address')->where(['uid'=>$this->uid])->select();
				if(empty($address)){
					header("Location:/wap/member/memberaddress");
				}
			}
            return view($this->style . '/Member/newmember');
        }
        /**
         *
         * 邀请好友页面
         *
         **/
        public function invitationFriends(){
            $member         = new MemberService();
            $pidNum         = $member->getPidNum($this->uid);
            $exc_point      = $member->getExcPoint($this->uid);
            $totalExcPoint  = $member->getExcPointNum($this->uid);
            //获取我的直推人的信息
            $uidArr         = $member->getFriends($this->uid);
            $useInfo        = [];

            if(!empty($uidArr))
            {
                foreach ($uidArr as $k=>$v)
                {
                    $useInfo[$k]= Db::table('sys_user')
                        ->where('uid',$v['uid'])
                        ->field('uid,user_headimg,user_name,reg_time')
                        ->select();

                    $useInfo[$k][0]['uid'] = $member->setUidZero($useInfo[$k][0]['uid']);
                }
            }

            $this->assign('excpoint', $exc_point);
            $this->assign('pidnum', $pidNum);
            $this->assign('totalExcPoint', $totalExcPoint);
            $this->assign('useInfo', $useInfo);
        	 return view($this->style . '/Member/invitationFriends');
        }
    /**
     * 
     * 红包活动页面
     * 
     **/
    public function redPacket(){
    	 return view($this->style . '/Member/redPacket');
    }
    /*
     * 
     * 忘记支付密码
     * 
     */
    public function forgetPassword(){
    	 return view($this->style . '/Member/forgetPassword');
    }
	//我的分销
	public function mysale()
	{
		$member = new MemberService();
        $member_info = $member->getMemberDetail();
        //获取用户的待入账余额$member_info['for_add_balance']
        $Remaining_balance = Db::table('ns_member_account_records')
                                ->where(['uid' => $this->uid, 'is_add' => 0, 'account_type' => 2])
                                ->where('data_id','neq',0)//此种情况是余额通兑积分
                                ->column('data_id');
        if(!empty($Remaining_balance)){
            $conditions['order_id'] = array('in',$Remaining_balance);
            $conditions['pay_status'] = 2;
            $conditions['order_status'] = array('not in', '0, 5, -1, -2');

            $orderinfo = Db::table('ns_order')
                ->where($conditions)
                ->column('order_id');

            $conds['data_id'] = array('in', $orderinfo);
            $conds['uid'] = $this->uid;
            $conds['is_add'] = 0;
            $conds['account_type'] = 2;
            $conds['number'] = array('gt', 0);//排除number字段为负数的情况

            $member_info['for_add_balance'] = Db::table('ns_member_account_records')
                ->where($conds)
                ->sum('number');
        }else{
            $member_info['for_add_balance'] = 0;
        }

//        print_r($member_info['for_add_balance']);exit;
		//给用户的id前面自动补零变为8位数字-例如00000294
		//start
		 $num = $this->uid;
		 $bit = 8;
		 $num_len = strlen($num);
		 $zero = '';
		 for($i=$num_len; $i<$bit-1; $i++){
		  $zero .= "0";
		 }
		 $real_num = "0".$zero.$num;
		 //end
		$tot_sale=0;
		$today=date("Y-m-d",time());
		//会员及总销售订单取数，成员包含自身及下面二级
		$nsmember = new NsMemberModel();
		$team=$nsmember->where('path_pid','like','%#'.$this->uid.'#%')->whereOr('pid',$this->uid)->select();
		$all_mem=array($this->uid);//所有的成员，包括自己
		$my_team=0;//今日新增
		foreach($team as $key=>$val){
			if($val['pid']==$this->uid){
				$all_mem[]=$val['uid'];
				if(strpos($val['reg_time'],$today) !== false){
					$my_team++;
				}
			} else {
				$once=explode('#'.$this->uid.'#',$val['path_pid']);
				if(substr_count($once[1],'#')==0){
					$all_mem[]=$val['uid'];
					if(strpos($val['reg_time'],$today)!== false){
						$my_team++;
					}
				}
			}
		}

		$condition['buyer_id'] = array('in',$all_mem);
		$condition['pay_status'] = 2;
		$commission=0;//总销售
		$today_sale=0;//今日销售
		$salesRec=Db::table('ns_order')->where($condition)->field('order_id,buyer_id,pay_time,order_money,refund_money')->select();
		foreach($salesRec as $k=>$v){
			$commission=$commission+$v['order_money']-$v['refund_money'];
			if(strpos($v['pay_time'],$today)!== false){
				$today_sale=$today_sale+$v['order_money']-$v['refund_money'];
			}
		}
		/*购买会员卡的金额目前需计入*/
		$commission_arr=$this->cardConsume($all_mem);
		$commission+=$commission_arr['card_commission'];
		$today_sale+=$commission_arr['today_commission'];
		/*今日待增余额收入*/
		$cond2['uid']=$this->uid;
		$cond2['account_type']=2;
		$cond2['create_time'] = array('like',''.$today.'%');
		$cond2['is_add']=0;
		$staymoney=Db::table('ns_member_account_records')->where($cond2)->sum('number');
		/*今日待增积分*/
		$cond3['uid']=$this->uid;
		$cond3['account_type']=1;
		$cond3['create_time'] = array('like',''.$today.'%');
		$cond3['is_add']=0;
		$staypoint=Db::table('ns_member_account_records')->where($cond3)->sum('number');
		
		$this->assign('staymoney', $staymoney);
		$this->assign('staypoint', $staypoint);
		$this->assign('today_sale', $today_sale);
		$this->assign('my_team', $my_team);
		$this->assign('tot_sale', $commission);
		$this->assign('real_num', $real_num);
        $this->assign('member_info', $member_info);
        return view($this->style . '/Member/mysale');
	}
   
    public function memberIndex()
    {
        $member = new MemberService();
		$experiencePoint=$member->experiencePoint($this->uid);
		$this->assign('experiencePoint', $experiencePoint);
        $platform = new Platform();
        $get_shop = empty($this->shop_id) ? '' : '?shop_id=' . $this->shop_id;
        $account_flag = $get_shop == '' ? '?flag=1' : '&flag=1';
        // 基本信息行级显示菜单项
         $member_menu_arr = array(
            'personal' => array(
                '个人资料',
                'member/personaldata' . $get_shop
            ),
            'address' => array(
                '收货地址',
                'member/memberAddress?flag=1' . (empty($this->shop_id) ? '' : '&shop_id=' . $this->shop_id)
            ),
            'withdrawals' => array(
                '提现账号',
                'member/accountList' . $get_shop . $account_flag
            ),
            'qr_code' => array(
                '推广二维码',
                'member/getWchatQrcode' 
            ),
            "shop_code" => array(
                '店铺二维码',
                'member/getShopQrcode' . $get_shop
            ),
            "memberCoupon" => array(
                '优惠券',
                'member/memberCoupon' . $get_shop
            )
        );
        $member_info = $member->getMemberDetail($this->instance_id);
        // 头像
        if (! empty($member_info['user_info']['user_headimg'])) {
            $member_img = $member_info['user_info']['user_headimg'];
        } else {
            $member_img = '0';
        }
        $index_adv = $platform->getPlatformAdvPositionDetail(1152);
        // 平台广告位
        $menu_arr = array(
            $member_menu_arr
        );
        foreach ($menu_arr as $arr_key => $arr_item) {
            if (empty($arr_item)) {
                unset($menu_arr[$arr_key]);
                continue;
            }
            foreach ($arr_item as $key => $item) {
                $class_item = array(
                    'class' => $key,
                    'title' => $item[0],
                    'url' => $item[1]
                );
                $menu_arr[$arr_key][$key] = $class_item;
            }
        }
        // 判断是否开启了签到送积分
        $config = new Config();
        $integralconfig = $config->getIntegralConfig($this->instance_id);
        $this->assign('integralconfig', $integralconfig);
        // dump($integralconfig);
        // 判断用户是否签到
        $dataMember = new MemberService();
        $isSign = $dataMember->getIsMemberSign($this->uid, $this->instance_id);
        $this->assign("isSign", $isSign);
        
        $this->assign('member_info', $member_info);
        $this->assign('index_adv', $index_adv["adv_list"][0]);
        $this->assign('member_img', $member_img);
        $this->assign('menu_arr', $menu_arr);
        
        return view($this->style . '/Member/index');
    }
    /**
     * 会员地址管理
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function memberAddress()
    {
        $member = new MemberService();
        $addresslist = $member->getMemberExpressAddressList();
        $this->assign("list", $addresslist);
        $flag = isset($_GET['flag']) ? $_GET['flag'] : "";
        $url = isset($_GET['url']) ? $_GET['url'] : "";
        $pre_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
//         dump($flag);exit;
        $_SESSION['address_pre_url'] = $pre_url;
        $this->assign("pre_url", $pre_url);
        $this->assign("flag", $flag);
        $this->assign("url", $url);
        return view($this->style . "/Member/memberAddress");
    }

    /**
     * 添加地址
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function addMemberAddress()
    {
        if (request()->isAjax()) {
            $member = new MemberService();
            $consigner = $_POST['consigner'];
            $mobile = $_POST['mobile'];
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $province = $_POST['province'];
            $city = $_POST['city'];
            $district = $_POST['district'];
            $address = $_POST['address'];
            $zip_code = isset($_POST['zip_code']) ? $_POST['zip_code'] : '';
            $alias = isset($_POST['alias']) ? $_POST['alias'] : '';
            $retval = $member->addMemberExpressAddress($consigner, $mobile, $phone, $province, $city, $district, $address, $zip_code, $alias);
            return AjaxReturn($retval);
        } else {
            $address_id = isset($_GET['addressid']) ? $_GET['addressid'] : 0;
            $this->assign("address_id", $address_id);
            if (! empty($_SESSION['address_pre_url'])) {
                $pre_url = $_SESSION['address_pre_url'];
            } else {
                $pre_url = '';
            }
            $this->assign("pre_url", $pre_url);
            $flag = isset($_GET['flag']) ? $_GET['flag'] : "";
            $this->assign("flag", $flag);
            return view($this->style . "/Member/addMemberAddress");
        }
    }

    /**
     * 修改会员地址
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >|Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function updateMemberAddress()
    {
        $member = new MemberService();
        if (request()->isAjax()) {
            $id = $_POST['id'];
            $consigner = $_POST['consigner'];
            $mobile = $_POST['mobile'];
            $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
            $province = $_POST['province'];
            $city = $_POST['city'];
            $district = $_POST['district'];
            $address = $_POST['address'];
            $zip_code = isset($_POST['zip_code']) ? $_POST['zip_code'] : '';
            $alias = isset($_POST['alias']) ? $_POST['alias'] : '';
            $retval = $member->updateMemberExpressAddress($id, $consigner, $mobile, $phone, $province, $city, $district, $address, $zip_code, $alias);
            return AjaxReturn($retval);
        } else {
            $id = $_GET['id'];
            $flag = isset($_GET['flag']) ? $_GET['flag'] : "";
            $info = $member->getMemberExpressAddressDetail($id);
            $this->assign("address_info", $info);
            $this->assign("flag", $flag);
            $pre_url = $_SERVER['HTTP_REFERER'];
            $_SESSION['address_pre_url'] = $pre_url;
            $this->assign("pre_url", $pre_url);
            return view($this->style . "/Member/updateMemberAddress");
        }
    }

    /**
     * 获取用户地址详情
     *
     * @return Ambigous <\think\static, multitype:, \think\db\false, PDOStatement, string, \think\Model, \PDOStatement, \think\db\mixed, multitype:a r y s t i n g Q u e \ C l o , \think\db\Query, NULL>
     */
    public function getMemberAddressDetail()
    {
        $address_id = isset($_POST['id']) ? $_POST['id'] : 0;
        $member = new MemberService();
        $info = $member->getMemberExpressAddressDetail($address_id);
        return $info;
    }

    /**
     * 会员地址删除
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function memberAddressDelete()
    {
        $id = $_POST['id'];
        $member = new MemberService();
        $res = $member->memberAddressDelete($id);
        return AjaxReturn($res);
    }

    /**
     * 修改会员地址
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function updateAddressDefault()
    {
        $id = $_POST['id'];
        $member = new MemberService();
        $res = $member->updateAddressDefault($id);
        return AjaxReturn($res);
    }

    /**
     * 店铺积分列表和平台积分
     */
    public function integral()
    {
        $market_isset = false;
        $shop_isset = false;
        $market_list = '';
        $shop_list = '';
        // 获取店铺的积分列表
        $integral_list = $this->user->getShopAccountListByUser($this->uid, 1, 0);
        // 获取店铺的信息
        if (! empty($integral_list["data"])) {
            foreach ($integral_list["data"] as $shop_list) {
                if ($shop_list["shop_id"] == 0) {
                    // 此时为商场
                    $market_isset = true;
                    $market = new WebSite();
                    $market_list = $market->getWebSiteInfo();
                } else {
                    $shop_isset = true;
                    $shop = new Shop();
                    $shop_list['extra'] = $shop->getShopInfo($shop_list['shop_id']);
                }
            }
        }
        $this->assign([
            'market_isset' => $market_isset,
            'shop_isset' => $shop_isset,
            'integral' => $integral_list,
            'market_list' => $market_list
        ]);
        return view($this->style . '/Member/integral');
    }
    public function tdPointWater()
    {
        $member_account = new MemberAccount();
        $member_info['exc_point'] = $member_account->getMemberTdPoint($this->uid);
        $this->assign('member_info',$member_info['exc_point']);
        return view($this->style . '/Member/tdPointWater');
    }
    public function invitationPoster()
    {
        $user_info = Db::table('sys_user')
            ->where(['uid'=>$this->uid])
            ->field('user_headimg, nick_name')
            ->find();
        $this->assign('user_info',$user_info);
        return view($this->style . '/Member/invitationPoster');
    }
    /**
     * 店铺积分流水
     */
    public function integralWater()
    {
        if (request()->isAjax()) {
            $status = isset($_POST['status']) ? $_POST['status'] : 'all';
            $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '2016-01-01';
            $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '2099-01-01';
            if($start_time != '2016-01-01'){
                $start_time = str_replace('年', '-', $start_time);
                $start_time = str_replace('月', '-', $start_time);
                $start_time = $start_time . '01';
                $start_time = date("Y-m-01",strtotime($start_time));
                $end_time = date("Y-m-d",strtotime("+30 days", strtotime($start_time)));
            }

            $page_index = isset($_POST['page_index']) ? $_POST['page_index'] : '1';
            $page_size = isset($_POST['page_size']) ? $_POST['page_size'] : '0';
            $page_count = '';

            if ($status != 'all') {
                switch ($status) {
                    case 0:
                        $condition['nmar.is_add'] = 0;
                        break;
                    case 1:
                        $condition['nmar.is_add'] = 1;
                        break;
                    default:
                        break;
                }
            }
            // 还要考虑状态逻辑

            // 该店铺下的余额流水
            $member = new MemberService();

            $condition['nmar.uid'] = $this->uid;
//            $condition['nmar.shop_id'] = $this->instance_id;
            $condition['nmar.account_type'] = 1;
            $condition["nmar.create_time"] = [
                [
                    ">=",
                    $start_time
                ],
                [
                    "<=",
                    $end_time
                ]
            ];
            $member_point_list = $member->getAccountList($page_index, $page_size, $condition);

            return $member_point_list['data'];
        } else {
    	 	$status = isset($_GET['status']) ? $_GET['status'] : 'all';
            $this->assign("status", $status);
            //$shop_id = $this->instance_id;
            $condition['nmar.shop_id'] = $shop_id;
            $condition['nmar.uid'] = $this->uid;
            $condition['nmar.account_type'] = 1;
//            // 查看用户在该商铺下的积分消费流水
//            $member_point_list = $this->user->getAccountList(1, 0, $condition);
            // 查看积分总数
            $member = new MemberService();
            $menber_info = $member->getMemberDetail();
            // 查找店铺积分说明
            $pointConfig = new Promotion();
            $pointconfiginfo = $pointConfig->getPointConfig();
            // 该店铺下的余额流水
            $member_account = new MemberAccount();
            $account_for_add = $member_account->getMemberAccountRecordSum('', $this->uid, 1,date('Y-m-d', strtotime('-60 days', time())), date('Y-m-d H:m:s', time()), 0);

            $this->assign("sum_for_add", $account_for_add);

            $this->assign([
                "point" => $menber_info['point']['point'],
//                "member_point_list" => $member_point_list,
                "pointconfiginfo" => $pointconfiginfo
            ]);
            return view($this->style . '/Member/integralWater');
        }
    }
	
    /**
     * 会员余额
     */
    public function balance()
    {
        $market_isset = false;
        $shop_isset = false;
        $market_list = '';
        $shop_list = '';
        // 获取店铺的积分列表
        $balance_list = $this->user->getShopAccountListByUser($this->uid, 1, 0);
        // 获取店铺的信息
        foreach ($balance_list["data"] as $shop_list) {
            if ($shop_list["shop_id"] == 0) {
                // 此时为商场
                $market_isset = true;
                $market = new WebSite();
                $market_list = $market->getWebSiteInfo();
            } else {
                $shop_isset = true;
                $shop = new Shop();
                $shop_list['extra'] = $shop->getShopInfo($shop_list['shop_id']);
            }
        }
        $this->assign([
            'market_isset' => $market_isset,
            'shop_isset' => $shop_isset,
            'balance' => $balance_list,
            'market_list' => $market_list
        ]);
        return view($this->style . '/Member/balance');

    }
    /**
     * 账单收益
     * 
     */
    public function profit()
    {
        //只显示上月的账单数据
        $Y = date("Y");//当前年
        $M = date("m");//当月
        $nextM = $M-1;//上一月
        $Y1 = date("Y");
        if($nextM == 0){//如果1月
            $Y1 = date("Y")-1;
            $nextM = 12;
        }
        $start_date = date("$Y1-$nextM-01 00:00:00");//上月开始时间
        $end_date = date("$Y-$M-01 00:00:00");//上月结束时间
        $condition =  ['buyer_id'=>$this->uid,'pay_status'=>2, 'order_status'=>4];
        $condition["pay_time"] = [
            [
                ">",
                $start_date
            ],
            [
                "<",
                $end_date
            ]
        ];
        $orderinfo = Db::table('ns_order')
                        ->where($condition)
                        ->field('goods_money, order_id')
                        ->select();//消费总额
        $total_money = 0;
        $orders = array();
        if(!empty($orderinfo)){
            foreach ($orderinfo as $k=>$v){
                $total_money += $orderinfo[$k]['goods_money'];
                $orders[$k] = $v['order_id'];
                $ordermoney[$k] = $v['goods_money'];
            }
            if(request()->isAjax()){
                $data = isset($_POST['data']) ? $_POST['data'] : '0';
                if($data == 0){
                    $goodsidarray = Db::table('ns_order_goods')
                        ->where('order_id', 'in', $orders)
                        ->field('goods_id,goods_money')
                        ->select();
                    $goodsid = array();
                    foreach($goodsidarray as $kk=>$vv){
                        if($vv['goods_id']!=0){
                            $goodsid[$kk] = $vv['goods_id'];
                            $categorys = Db::table('ns_goods')
                                ->where('goods_id', 'in', $goodsid)
                                ->order('category_id', 'asc')
                                ->field('category_id, market_price')
                                ->select();
                            $categoryid = array();
                            $categorymoney = array();
                            $new_arr = array();
                            foreach ($categorys as $kkk=>$vvv){
                                $categoryid[$kkk] = $vvv['category_id'];
                                $categorymoney[$kkk] = $vvv['market_price'];
                                $new_arr[$kkk] = array('category_id'=>$categoryid[$kkk] , 'market_price'=>$categorymoney[$kkk]);
                            }
                            $item=array();
                            foreach($new_arr as $k2=>$v2){
                                if(!isset($item[$v2['category_id']])){
                                    $item[$v2['category_id']]=$v2;
                                }else{
                                    $item[$v2['category_id']]['market_price']+=$v2['market_price'];
                                    $item[$v2['category_id']]['market_price']=strval($item[$v2['category_id']]['market_price']);
                                }
                            }
                            $item = array_values($item);
                            $list = Db::table('ns_goods_category')
                                ->where('category_id', 'in', $categoryid)
                                ->field('category_id,category_name')
                                ->select();
                            foreach ($list as $k1=>$v1){
                                $list[$k1]['category_money'] = $item[$k1]['market_price'];
                            }
                        }else{
                            $list1[$kk]['category_id'] = '';
                            $list1[$kk]['category_name'] = '线下订单';
                            $list1[$kk]['category_money'] = $vv['goods_money'];
                        }
                    }
                    if($list ==''){
                    	$res = $list1;
                    }else if($list1 ==''){
                    	$res = $list;
                    }else{
                    	$res = array_merge($list,$list1);
                    }
                    return $res;
                }else{
                    return '-1';
                }
            }
        }else{
        	if(request()->isAjax()){
        		$data = isset($_POST['data']) ? $_POST['data'] : '0';
                if($data == 0){
                	return -2;
                }
            }
        }
        //获取当前用户的积分奖励总额
        $conditions = ['uid'=>$this->uid, 'account_type'=>1, 'is_add'=>1];
        $conditions['from_type'] = array('in', [25, 35]);
        $conditions['number'] = array('neq', 0);
        $conditions["create_time"] = [
            [
                ">",
                $start_date
            ],
            [
                "<",
                $end_date
            ]
        ];
        $point = Db::table('ns_member_account_records')
            ->where($conditions)
            ->sum('number');
        //获取当前用户的分销收益
        $conditionss = ['uid'=>$this->uid, 'account_type'=>2, 'is_add'=>1];
        $conditionss['from_type'] = array('in', [15]);
        $conditionss['number'] = array('neq', 0);
        $conditionss["create_time"] = [
            [
                ">",
                $start_date
            ],
            [
                "<",
                $end_date
            ]
        ];
        $money = Db::table('ns_member_account_records')
            ->where($conditionss)
            ->sum('number');

        $this->assign('point', $point);
        $this->assign('money', $money);
        $this->assign('Y', $Y1);
        $this->assign('M', $nextM);
        $this->assign('total_money', $total_money);
        return view($this->style . '/Member/profit');
    }
    /* 
     * 我的团队
     */
    public function team(){
		$myteam=$this->getTeam($this->uid);
		$user_name=Db::table('sys_user')->where(['uid'=>$this->uid])->value('user_name');
        $user_headimg=Db::table('sys_user')->where(['uid'=>$this->uid])->value('user_headimg');
        $usergrade = Db::table('ns_member')->where(['uid'=>$this->uid])->value('grade');
		$huiyuan=array();
		$jingli=array();
		$zongjian=array();
		foreach($myteam['zhi_team_id'] as $k=>$v){  //此处级别专指直属团队中的
			$grade = Db::table('ns_member')->where(['uid'=>$v])->value('grade');
			if($grade == 2){$jingli[]=$v;}
			elseif($grade == 5){$zongjian[]=$v;}
			else {$huiyuan[]=$v;}
		}
		/*直属、从属团队佣金总额*/
		$cond3['uid'] = $this->uid;
		$cond3['from_type'] = array('in','15,25'); //只含15、25这两类，并且分销是number大于0。分销只使用余额
		$cond3['number'] = array('>',0);
		$cond3['account_type'] = 2;//此处可以考虑日期create_time变化
		$zhi_commission=0; //直属佣金
		$cong_commission=0; //从属佣金
		$tot_commission=0;//总佣金
		$tot_cong_sale=0;//从属总销售
		$tot_zhi_sale=0;//直属总销售
		$commission_rec=Db::table('ns_member_account_records')->where($cond3)->select();
		$comm_buyerId_arr=array();
		foreach($commission_rec as $k=>$v){
			$tot_commission+=$v['number'];
			$orderone=Db::table('ns_order')->where('order_id',$v['data_id'])->field('buyer_id,order_money,refund_money,create_time')->find();
			$buyer_id=$orderone['buyer_id'];
			$comm_buyerId_arr[]=$buyer_id;
			if(in_array($buyer_id,$myteam['cong_team_id'])){  
				$tot_cong_sale=$tot_cong_sale+$orderone['order_money']-$orderone['refund_money'];
				$cong_commission += $v['number'];
			} else {
				$zhi_commission += $v['number'];
				$tot_zhi_sale=$tot_zhi_sale+$orderone['order_money']-$orderone['refund_money'];
			}
		}
		$this->assign('user_headimg', $user_headimg);
		$this->assign('usergrade', $usergrade);
		$this->assign('tot_zhi_sale', $tot_zhi_sale);
		$this->assign('tot_cong_sale', $tot_cong_sale);
		$this->assign('zhi_commission', $zhi_commission);
		$this->assign('cong_commission', $cong_commission);
		//佣金总额计算结束
        if(request()->isAjax()) {
            $gradenext = isset($_POST['grade']) ? $_POST['grade'] : 0;
			$date = isset($_POST['date']) ? $_POST['date'] : '';
			//获取销售数据
			if($gradenext == 2){$now_arr=$jingli;$grade_name='经理';}
			elseif($gradenext == 5){$now_arr=$zongjian;$grade_name='总监';}
			else {$now_arr=$huiyuan;$grade_name='会员';}
			$res=array();
			foreach($now_arr as $k=>$v){
				$once=Db::table('sys_user')->where(['uid'=>$v])->field('user_name,user_tel')->find();
				$res[$k]['user_str'] = $once['user_name'];
				$res[$k]['telephone'] = $once['user_tel'];
				//start
				 $num = $v;
				 $bit = 8;
				 $num_len = strlen($num);
				 $zero = '';
				 for($i=$num_len; $i<$bit-1; $i++){
				  $zero .= "0";
				 }
				 $res[$k]['id'] = "0".$zero.$num;
				 //end
				$res[$k]['grade_name']=$grade_name;
				$mem_sale=0;
				$mem_commission=0;
				//$cond1=['buyer_id'=>$v, 'pay_status'=>2];
				if(!empty($date)){$cond1['create_time'] = array('between', array($date,date("Y-m-d H:i:s", time())));}
				//$mem_order = Db::table('ns_order')->where($cond1)->field('order_id,order_money,refund_money')->select();
				if(in_array($v,$comm_buyerId_arr)){
					foreach($commission_rec as $ks=>$vs){
						$one_order = Db::table('ns_order')->where('order_id',$vs['data_id'])->field('order_id,buyer_id,order_money,refund_money')->find();
						if($one_order['buyer_id']==$v){
							$mem_sale=$mem_sale+$one_order['order_money']-$one_order['refund_money'];
							$mem_commission=$mem_commission+$vs['number'];
						}
					}
				}
				$res[$k]['user_id']= $v;
				$res[$k]['mem_sale']=$mem_sale;
				$res[$k]['mem_commission']=$mem_commission;
			}
            return $res;
        } else {
			//此处经理、总监等级别专指直属团队中的
			$team_count=array('zhi_count'=>count($myteam['zhi_team_id']),'cong_count'=>count($myteam['cong_team_id']),'huiyuan_count'=>count($huiyuan),'jingli_count'=>count($jingli),'zongjian_count'=>count($zongjian));
			$this->assign('tot_commission', $tot_commission);
			$cond3['is_add'] = 0;
			$dai_tot_commission=Db::table('ns_member_account_records')->where($cond3)->sum('number');
			$this->assign('dai_tot_commission',$dai_tot_commission);
			$this->assign('team_count', $team_count);
			$this->assign('user_name', $user_name);
            return view($this->style . '/Member/team');
        }

    }
	/*  */
	function teamNext(){
		$myteam=$this->getTeam($this->uid);
		$date = isset($_POST['date']) ? $_POST['date'] : '';
		//获取销售数据
		$data_tot['sale_tot']=0;
		//$data_tot['commission_tot'] = 0;
		$cond1['buyer_id'] = array('in',$myteam['cong_team_id']);
		$cond1['pay_status']=2;
		if(!empty($date)){$cond1['create_time'] = array('between', array($date,date("Y-m-d H:i:s", time())));}
		$mem_order = Db::table('ns_order')->where($cond1)->field('order_id,order_money,refund_money')->select();
		foreach($mem_order as $key=>$val){
			$data_tot['sale_tot']=$data_tot['sale_tot']+$val['order_money']-$val['refund_money'];
			/*
			$cond['uid'] = $this->uid;
			$cond['from_type'] = array('in','15,35');
			$cond['data_id'] = $val['order_id'];
			$once=Db::table('ns_member_account_records')->where($cond)->field('number')->find();
			if(!empty($once)){$data_tot['commission_tot']+=$once['number'];}*/
		}
		/*
		$cond2['uid'] = array('in',$myteam['cong_team_id']);
		$cond2['is_add']=1;  //积分兑换余额比例是1
		$cond2['from_type'] = array('in','15,35');
		if(!empty($date)){$cond2['create_time'] = array('between', array($date,date("Y-m-d H:i:s", time())));}
		$data_tot['commission_tot'] = Db::table('ns_member_account_records')->where($cond2)->sum('number');*/
		$res['data_tot']=$data_tot;
		//
		$res['this_team']=array();
		foreach($myteam['cong_team_id'] as $k=>$v){
			$res['this_team'][$k]['son_name']= Db::table('sys_user')->where(['uid'=>$v])->value('user_name').'的团队';
			$sonteam=$this->getTeam($v);
			$res['this_team'][$k]['team_number'] = count($sonteam['team_belong']);
			$one_sale_tot=0;
			$son_commission=0;
			if(!empty($sonteam['team_belong'])){
				$cond2['buyer_id'] = array('in',$sonteam['team_belong']);
				$cond2['pay_status']=2;
				if(!empty($date)){$cond2['create_time'] = array('between', array($date,date("Y-m-d H:i:s", time())));}
				$son_order = Db::table('ns_order')->where($cond2)->field('order_id,order_money,refund_money')->select();
				foreach($son_order as $key=>$val){
					$one_sale_tot=$one_sale_tot+$val['order_money']-$val['refund_money'];
					$cond5['uid'] = $v;
					$cond5['from_type'] = array('in','15,35');
					$cond5['data_id'] = $val['order_id'];
					$two=Db::table('ns_member_account_records')->where($cond5)->field('number')->find();
					if(!empty($two)){$son_commission+=$two['number'];}
				}
			}
			$res['this_team'][$k]['son_sale'] = $one_sale_tot;
			/*
			if(!empty($sonteam['team_belong'])){
				$cond5['uid'] = array('in',$sonteam['team_belong']);
				$cond5['is_add']=1;  //积分兑换余额比例是1
				$cond5['from_type'] = array('in','15,35');
				if(!empty($date)){$cond5['create_time'] = array('between', array($date,date("Y-m-d H:i:s", time())));}
				$res['this_team'][$k]['son_commission'] = Db::table('ns_member_account_records')->where($cond5)->sum('number');
			} */
			$res['this_team'][$k]['son_commission'] = $son_commission;
			$res['this_team'][$k]['user_headimg'] = Db::table('sys_user')->where('uid',$v)->value('user_headimg');
		}
		return $res;
	}
    /*
     * 
     * 
     * 
     */
    public function teamDeail(){
		$sonteam=$this->getTeam($_GET["uid"]);
		$onemem=Db::table('sys_user')->where(['uid'=>$_GET["uid"]])->field('user_name, user_tel, user_headimg')->find();
        $res['user_headimg']=$onemem['user_headimg'];
		$res['user_name']=$onemem['user_name'];
		$res['telephone'] = $onemem['user_tel'];
		$res['team_num'] = count($sonteam['team_belong']);
		if (request()->isAjax()) {
			$date = isset($_POST['date']) ? $_POST['date'] : '';
			$cond5['uid'] = $this->uid;
			$cond5['from_type'] = array('in','15,25'); //只含15、25这两类，并且分销是number大于0。分销只使用余额
			$cond5['number'] = array('>',0);
			$cond5['account_type'] = 2;
			if(!empty($date)){$cond5['create_time'] = array('between', array($date,date("Y-m-d H:i:s", time())));}
			$account_rec = Db::table('ns_member_account_records')->where($cond5)->select();//自身的全部分销记录
			/*所属团队*/
			$team_sale_tot=0;
			$team_commission = 0;
			$new_arr=array();
			$mem_sale=0;
			$mem_commission=0;
			foreach($account_rec as $k=>$v){
				$once=Db::table('ns_order')->where('order_id',$v['data_id'])->field('order_no,buyer_id,order_money,refund_money')->find();
				if($once['buyer_id']==$_GET["uid"]){
					$two['order_no']=$once['order_no'];
					$two['create_time']=$v['create_time'];
					$two['number']=$v['number'];
					$two['order_money']=$once['order_money']-$once['refund_money'];
					$mem_sale+=$two['order_money'];
					$mem_commission+=$v['number'];
					if($v['is_add']==0){$two['status_name']='待入账';}
					else {$two['status_name']='已入账';}
					$new_arr[]=$two;
				}
				
				if(in_array($once['buyer_id'],$sonteam['team_belong'])){
					$team_sale_tot=$team_sale_tot+$once['order_money']-$once['refund_money'];
					$team_commission+=$v['number'];
				} 
			}
			
			return array('new_arr'=>$new_arr,'mem_sale'=>$mem_sale,'mem_commission'=>$mem_commission,'team_sale_tot'=>$team_sale_tot,'team_commission'=>$team_commission);
		}
		$this->assign('res', $res);
    	return view($this->style . '/Member/teamDeail');
    }
    /*
     * 
     * 历史排行
     * 
     */
    public function ranking(){
        //获取自己的销售排行
        $orderinfo = '';
        $userinfo = '';
        $orderinfo = Db::table('ns_order')
            ->where(['pay_status'=>2, 'order_status'=>4, 'buyer_id'=>$this->uid])
            ->group('buyer_id')
            ->order("sum(order_money) desc")
            ->field('sum(order_money), buyer_id')
            ->select();
        $userinfo = Db::table('sys_user')
            ->where('uid', $this->uid)
            ->field('user_name, user_headimg')
            ->select();
        $userinfo[0]['order_moneys'] =  $orderinfo[0]['sum(order_money)'];//销售额
        if($userinfo[0]['order_moneys'] == 0){
            $userinfo[0]['order_moneys'] = '0.00';
        }
        //获取名次
        $M = Db::table('ns_order')
            ->where(['pay_status'=>2, 'order_status'=>4])
            ->group('buyer_id')
            ->order("sum(order_money) desc")
            ->field('sum(order_money), buyer_id')
            ->select();
//        print_r($M);exit;
        foreach ($M as $k=>$v){
            $num1 = 0;//本人的销售名次
            if($v['buyer_id'] == $this->uid){
                $num1 += $k+1;
                break;
            }else{
                $num1 = '无';
            }
        }
        $userinfo[0]['num1'] = $num1;


        //获取自己的人数排名
        $N = Db::table('ns_member')
            ->where('pid','neq', 0)
            ->field('pid, count(pid)')->group('pid')
            ->order('count(pid) desc')
            ->select();
        foreach ($N as $k=>$v){
            $num2 = 0;
            if($v['pid'] == $this->uid){
                $num2 += $k+1;
                $userinfo[0]['num3'] = $N[$k]['count(pid)'];
                break;
            }else{
                $num2 = '无';
                $userinfo[0]['num3'] = 0;
            }
        }
        $userinfo[0]['num2'] =$num2;
        if (request()->isAjax()) {
            $ranking_type = isset($_POST['type']) ? $_POST['type'] : 0;
            $uids = array();
            $userinfo = array();
            if($ranking_type == 0){//销售排行
                $orderinfo = Db::table('ns_order')
                    ->where(['pay_status'=>2, 'order_status'=>4])
                    ->group('buyer_id')
                    ->order("sum(order_money) desc")
                    ->limit('10')
                    ->field('sum(order_money), buyer_id')
                    ->select();
                foreach ($orderinfo as $k=>$v) {
                    $uids[$k] = $orderinfo[$k]['buyer_id'];
                    $userinfo[$k] = Db::table('sys_user')
                        ->where('uid', $uids[$k])
                        ->field('user_name, user_headimg')
                        ->select();
                    $userinfo[$k][0]['order_moneys'] = $orderinfo[$k]['sum(order_money)'];
                }
            }else{//人数排行
                $pids = Db::table('ns_member')
                    ->where('pid','neq', 0)
                    ->field('pid, count(pid)')->group('pid')
                    ->order('count(pid) desc')
                    ->limit('10')
                    ->select();
                foreach ($pids as $k=>$v){
                    $uids[$k] = $pids[$k]['pid'];
                    $userinfo[$k] = Db::table('sys_user')
                        ->where('uid', $uids[$k])
                        ->field('user_name, user_headimg')
                        ->select();
                    $userinfo[$k][0]['num'] = $pids[$k]['count(pid)'];
                }
            }
            return $userinfo;
        }
//        print_r($userinfo);exit;
        $this->assign('userinfo', $userinfo);
    	return view($this->style . '/Member/ranking');
    }
    /**
     * 
     * 会员中心
     * 
     * */
    public function memberCenter()
    {
//        $user_info = $this->user->getUserDetail();
//        $this->assign('user_info', $user_info);
//        $is_member = $this->user->getSessionUserIsMember();

        $member = new MemberService;
        $member_info = $member->getMemberDetail();

        $addresslist = $member->getMemberExpressAddressList();

        $this->assign('uid', $this->uid);
        $this->assign('member_info', $member_info);
        $this->assign('member_address', $addresslist);
        $this->assign('member_address_count', count($addresslist['data']));

        if ($member_info['jplus_level'] > 0) {
            return view($this->style . '/Member/memberCenter');
        }
        return view($this->style . '/Member/notMemberCenter');
    }

    public function assignMember()
    {
        $jplus_level = isset($_POST['jplus_level']) ? $_POST['jplus_level'] : '0';
        if(($jplus_level != 10) && ($jplus_level != 20)){
            return '请提供要购买的JPlus会员级别！';
        }
        $member = new MemberService;
        return $member->assignMember($jplus_level);
    }
    /**
     * 
     * 修改头像
     * 
     */
    public function portrait(){
        if (isset($_POST["user_headimg"]) && isset($_POST["submit2"])) {
            $img = str_replace('data:image/png;base64,', '', $_POST["user_headimg"]);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);

            $file_name = date("YmdHis") . rand(0, date("is")); // 文件名
            $file_name .= ".png";
            // 检测文件夹是否存在，不存在则创建文件夹
            $path = 'upload/avator/';
            if (! file_exists($path)) {
                $mode = intval('0777', 8);
                mkdir($path, $mode, true);
            }
            if (@file_exists($path . $file_name)) {
                @unlink($path . $file_name);
            }
            $member_info = $this->user->getMemberDetail();
            if (@file_exists($member_info['user_info']['user_headimg'])) {
                @unlink($member_info['user_info']['user_headimg']);
            }

            @clearstatcache();
            $fp = fopen($path . $file_name, 'w');
            fwrite($fp, $data);
            fclose($fp);
            $user_headimg = $path . $file_name;
            $upload_headimg_status = $this->user->updateMemberInformation("", "", "", "", "", "", $user_headimg);
            return 0;

//            move_uploaded_file($_FILES["user_headimg"]["tmp_name"], $path . $file_name);
//            $user_headimg = $path . $file_name;
//            $upload_headimg_status = $this->user->updateMemberInformation("", "", "", "", "", "", $user_headimg);
//
//            $imgPath = "test.png";
//            if (@file_exists($imgPath)) {
//                @unlink($imgPath);
//            }
//            @clearstatcache();
//            $fp = fopen($imgPath, 'w');
//            fwrite($fp, $data);
//            fclose($fp);
        }

//        if ($_FILES && isset($_POST["submit2"])) {
//            if ((($_FILES["user_headimg"]["type"] == "image/gif") || ($_FILES["user_headimg"]["type"] == "image/jpeg") || ($_FILES["user_headimg"]["type"] == "image/pjpeg") || ($_FILES["user_headimg"]["type"] == "image/png")) && ($_FILES["user_headimg"]["size"] < 10000000)) {
//                if ($_FILES["user_headimg"]["error"] > 0) {
//                    // echo "错误： " . $_FILES["user_headimg"]["error"] . "<br />";
//                }
//                $file_name = date("YmdHis") . rand(0, date("is")); // 文件名
//                $ext = explode(".", $_FILES["user_headimg"]["name"]);
//                $file_name .= "." . $ext[1];
//                // 检测文件夹是否存在，不存在则创建文件夹
//                $path = 'upload/avator/';
//                if (! file_exists($path)) {
//                    $mode = intval('0777', 8);
//                    mkdir($path, $mode, true);
//                }
//                move_uploaded_file($_FILES["user_headimg"]["tmp_name"], $path . $file_name);
//                $user_headimg = $path . $file_name;
//                $upload_headimg_status = $this->user->updateMemberInformation("", "", "", "", "", "", $user_headimg);
//            } else {
//                $this->error("请上传图片");
//            }
//        }
//        var_dump($_POST);


        $member_info = $this->user->getMemberDetail();
        $this->assign('member_info', $member_info);
        if (! empty($member_info['user_info']['user_headimg'])) {
            $member_img = $member_info['user_info']['user_headimg'];
        } elseif (! empty($member_info['user_info']['qq_openid'])) {
            $member_img = $member_info['user_info']['qq_info_array']['figureurl_qq_1'];
        } elseif (! empty($member_info['user_info']['wx_openid'])) {
            $member_img = '0';
        } else {
            $member_img = '0';
        }
        // 处理状态信息
        if ($member_info["user_info"]["user_status"] == 0) {
            $member_info["user_info"]["user_status"] = "锁定";
        } else {
            $member_info["user_info"]["user_status"] = "正常";
        }

        $member_detail = $this->user->getMemberDetail($this->instance_id);
        $this->assign("member_detail", $member_detail);

        $this->assign('qq_openid', $member_info['user_info']['qq_openid']);
        $this->assign('member_img', $member_img);

        return view($this->style . '/Member/portrait');
    }
	/**
     * 
     * 客服
     * 
     */
    public function customer()
    {
        return view($this->style . '/Member/customer');
    }
    /**
     * 会员余额流水
     */
    public function balanceWater()
    {
        if (request()->isAjax()) {
            $status = isset($_POST['status']) ? $_POST['status'] : 'all';
             $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '2016-01-01';
             $end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '2099-01-01';
            if($start_time != '2016-01-01'){
                $start_time = str_replace('年', '-', $start_time);
                $start_time = str_replace('月', '-', $start_time);
                $start_time = $start_time . '01';
                $start_time = date("Y-m-01",strtotime($start_time));
                $end_time = date("Y-m-d",strtotime("$start_time +1 month"));
            }
            $page_index = isset($_POST['page_index']) ? $_POST['page_index'] : '1';
            $page_size = isset($_POST['page_size']) ? $_POST['page_size'] : '0';
             $page_count = '';

            if ($status != 'all') {
                switch ($status) {
                    case 0:
                        $condition['nmar.is_add'] = 0;
                        break;
                    case 1:
                        $condition['nmar.is_add'] = 1;
                        break;
                    default:
                        break;
                }
            }
            // 还要考虑状态逻辑

            // 该店铺下的余额流水
            $member = new MemberService();

            $condition['nmar.uid'] = $this->uid;
            //$condition['nmar.shop_id'] = $this->instance_id;
            $condition['nmar.account_type'] = 2;
            $condition["nmar.create_time"] = [
                [
                    ">=",
                    $start_time
                ],
                [
                    "<=",
                    $end_time
                ]
            ];
            $list = $member->getAccountList($page_index, $page_size, $condition);
			foreach($list['data'] as $k=>$v){
				if($v['text']=='他人购买JPlus会员收益'){
					$once=Db::table('sys_user')->where(['uid'=>$v['data_id']])->value('user_name');
					if(empty($once)){
						$list['data'][$k]['buyer_name']='';
					}
					else {
						$list['data'][$k]['buyer_name']='购买人：'.$once;
					}
				} else {
					$list['data'][$k]['buyer_name']='';
				}
			}
			
            return $list['data'];
        } else {
            $status =  'all';
            $this->assign("status", $status);

            $member = new MemberService();
            $member_info = $member->getMemberDetail($this->instance_id);

            $config = new Config();
            //$shopid = $this->instance_id;
            $balanceConfig = $config->getBalanceWithdrawConfig($shopid);
            $this->assign("is_use", $balanceConfig['is_use']);
            $this->assign("sum", $member_info['balance']);
            //$this->assign("shopid", $shopid);
            return view($this->style . '/member/balanceWater');
        }

        return;
        // 该店铺下的余额流水
        $member = new MemberService();
        $uid = $this->uid;
        $shopid = $this->instance_id;
        $condition['nmar.uid'] = $uid;
        $condition['nmar.shop_id'] = $shopid;
        $condition['nmar.account_type'] = 2;
        $list = $member->getAccountList(1, 0, $condition);
        // 用户在该店铺的账户余额总数
        $member = new MemberService();
        $member_info = $member->getMemberDetail($this->instance_id);
        $config = new Config();
        $balanceConfig = $config->getBalanceWithdrawConfig($shopid);
        $this->assign("is_use", $balanceConfig['is_use']);
        $this->assign("sum", $member_info['balance']);
        $this->assign("balances", $list);
        $this->assign("shopid", $shopid);
        return view($this->style . '/Member/balanceWater');
    }
    /**
     * 余额提现记录
     */
    public function balanceWithdraw(){
        // 该店铺下的余额提现记录
        $member = new MemberService();
        $uid = $this->uid;
        $shopid = $this->instance_id;
        $condition['uid'] = $uid;
        $condition['shop_id'] = $shopid;
        /* $condition['status'] = 1; */
        $withdraw_list = $member->getMemberBalanceWithdraw(1, 0, $condition);
        foreach ($withdraw_list['data'] as $k=>$v){
            if($v['status'] == 1){
                $withdraw_list['data'][$k]['status'] = '已到账';
            }else if($v['status'] == 0){
                $withdraw_list['data'][$k]['status'] = '已申请';
            }else{
                $withdraw_list['data'][$k]['status'] = '已拒绝';
            }
        }
        // 用户在该店铺的账户余额总数
        $member = new MemberService();
        $member_info = $member->getMemberDetail($this->instance_id);
        $config = new Config();
        $balanceConfig = $config->getBalanceWithdrawConfig($shopid);
        $this->assign("is_use", $balanceConfig['is_use']);
        $this->assign("sum", $member_info['balance']);
        $this->assign("withdraws", $withdraw_list);
        $this->assign("shopid", $shopid);
        return view($this->style . '/Member/balanceWithdraw');
    }
    
    /**
     * 会员优惠券
     */
    public function memberCoupon()
    {
        if (request()->isAjax()) {
            $member = new MemberService();
            $type = isset($_POST['type']) ? $_POST['type'] : '';
            $shop_id = $this->instance_id;
            $counpon_list = $member->getMemberCounponList($type, $shop_id);
            foreach ($counpon_list as $key => $item) {
                $counpon_list[$key]['start_time'] = date("Y-m-d", strtotime($item['start_time']));
                $counpon_list[$key]['end_time'] = date("Y-m-d", strtotime($item['end_time']));
            }
            return $counpon_list;
        } else {
            return view($this->style . "/Member/memberCoupon");
        }
    }

    /**
     * 会员个人资料主界面
     */
    public function personalData()
    {
        $shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : 0;
        $_SESSION['bund_pre_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $uid = $this->user->getSessionUid();
        $member = new MemberService();
        $member_info = $member->getMemberDetail();
        $this->assign('member_info', $member_info);
        $account_list = $member->getMemberBankAccount();
        $this->assign('account_tot', count($account_list));
        // 查询账户信息
        // $user = new UserModel();
        // $nick_name = $user->getInfo(["uid" => $this->uid], "nick_name");
        
        if (! empty($member_info['user_info']['user_headimg'])) {
            $member_img = $member_info['user_info']['user_headimg'];
        } elseif (! empty($member_info['user_info']['qq_openid'])) {
            $member_img = $member_info['user_info']['qq_info_array']['figureurl_qq_1'];
        } elseif (! empty($member_info['user_info']['wx_openid'])) {
            $member_img = '0';
        } else {
            $member_img = '0';
        }
        //推荐人的id前面补零---例如299变为00000299
        $num1 = $member_info['pid'];
		$bit1 = 8;
		$num_len1 = strlen($num1);
		$zero1 = '';
		for($i=$num_len1; $i<$bit1-1; $i++){
		 $zero1 .= "0";
		}
		$real_num1 = "0".$zero1.$num1;
		$this->assign('real_num1', $real_num1);
        $this->assign("shop_id", $shop_id);
        $this->assign('qq_openid', $member_info['user_info']['qq_openid']);
        $this->assign('member_img', $member_img);
        return view($this->style . "/Member/personalData");
    }

    /**
     * 修改密码
     */
    public function modifyPassword()
    {
        $member = new MemberService();
        $uid = $this->user->getSessionUid();
        $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $retval = $member->ModifyUserPassword($uid, $old_password, $new_password);
        return AjaxReturn($retval);
    }

    /**
     * 修改邮箱
     */
    public function modifyEmail()
    {
        $member = new MemberService();
        $uid = $this->user->getSessionUid();
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $retval = $member->modifyEmail($uid, $email);
        return AjaxReturn($retval);
    }

    /**
     * 修改手机
     */
    public function modifyMobile()
    {
        $uid = $this->user->getSessionUid();
        $mobile = isset($_POST['mobilephone']) ? $_POST['mobilephone'] : '';
        $member = new MemberService();
        $retval = $member->modifyMobile($uid, $mobile);
        return AjaxReturn($retval);
    }

    /**
     * 修改昵称
     *
     * @return unknown[]
     */
    public function modifyNickName()
    {
        $uid = $this->user->getSessionUid();
        $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : '';
        $member = new MemberService();
        $retval = $member->modifyNickName($uid, $nickname);
        return AjaxReturn($retval);
    }

    /**
     * 修改qq
     *
     * @return Ambigous <multitype:unknown, multitype:unknown unknown string >
     */
    public function modifyQQ()
    {
        $uid = $this->user->getSessionUid();
        $qq = isset($_POST['qqno']) ? $_POST['qqno'] : '';
        $member = new MemberService();
        $retval = $member->modifyQQ($uid, $qq);
        return AjaxReturn($retval);
    }

    /**
     * 退出登录
     */
    public function logOut()
    {
        $member = new MemberService();
        $member->Logout();
        return AjaxReturn(1);
    }
	//手机端我的分销用户退出
	public function signOut()
    {
        $member = new MemberService();
        $member->signOut();
        
		return view($this->style . 'member/signOut');
    }

    /**
     * 我的收藏-->商品收藏
     * 创建人：王湘民
     * 创建时间：2018年10月31日12:26:58
     */
    public function collection()
    {

        if (request()->isAjax()) {
            $fav_type = isset($_POST['fav_type']) ? $_POST['fav_type'] : '';
            $page_index = isset($_POST['page_index']) ? $_POST['page_index'] : 1;
            $page_size = isset($_POST['page_size']) ? $_POST['page_size'] : 0;
            $goods_name = isset($_POST['goods_name']) ? $_POST['goods_name'] : '';

            if($fav_type == ''){
                return '';
            }

            $member = new MemberService();
            // 查询购物车中商品的数量
            if($fav_type == 'goods'){
                $data = array(
                    "nmf.fav_type" =>$fav_type,
                    "nmf.uid" => $this->uid
                );
                if($goods_name != ''){
                    $data['ng.goods_name'] = array(['like', '%'.$goods_name.'%']);
                }
                $goods_collection_list = $member->getMemberGoodsFavoritesList($page_index, $page_size, $data);
                return $goods_collection_list;
            }
            else{
                $data = array(
                    "nmf.fav_type" => 'shop',
                    "nmf.uid" => $this->uid
                );
                $shop_collection_list = $member->getMemberShopsFavoritesList($page_index, $page_size, $data);

                $goods = new Goods();
                foreach ($shop_collection_list['data'] as $key => $value) {
                    $shop_collection_list['data'][$key]['max_point'] = round($goods->getMaxInterest(['shop_id' => $value->shop_id]) * 0.4, 0);
                    $shop_collection_list['data'][$key]['max_cash'] = $shop_collection_list['data'][$key]['max_point'] * 6;
                }
                return $shop_collection_list;
            }
        }

        $member = new MemberService();
        $data = array(
            "nmf.fav_type" => 'goods',
            "nmf.uid" => $this->uid
        );
        $goods_collection_list = $member->getMemberGoodsFavoritesList(1, 0, $data);

        $this->assign("goods_collection_list", $goods_collection_list["data"]);
        $this->assign("goods_list", $goods_collection_list);
        $this->assign('total_count', $goods_collection_list['total_count']);

        $data = array(
            "nmf.fav_type" => 'shop',
            "nmf.uid" => $this->uid
        );
        $shop_collection_list = $member->getMemberShopsFavoritesList(1, 0, $data);

        $goods = new Goods();
        foreach ($shop_collection_list['data'] as $key => $value) {
            $shop_collection_list['data'][$key]['max_point'] = round($goods->getMaxInterest(['shop_id' => $value->shop_id]) * 0.4, 0);
            $shop_collection_list['data'][$key]['max_cash'] = $shop_collection_list['data'][$key]['max_point'] * 6;
        }
        $this->assign("shop_collection_list", $shop_collection_list["data"]);
        $this->assign("shop_list", $shop_collection_list);
        $this->assign('shop_total_count', $shop_collection_list['total_count']);

        return view($this->style . '/Member/collection');

    }

    /*
      * 余额明细
      *
      */
    public function balanceInfo(){
        $id = isset($_GET['id']) ? $_GET['id'] : 0;

        // 该店铺下的余额流水
        $member = new MemberService();

        $condition['nmar.id'] = $id;
        $list = $member->getAccountList(1, 0, $condition);

        $this->assign("account_list", $list['data']);
    	return view($this->style . 'member/balanceInfo');
    }
    /**
     * 解除QQ绑定
     */
    public function removeBindQQ()
    {
        $retval = $this->user->removeBindQQ();
        $this->success('解除绑定成功', $_SESSION['bund_pre_url']);
    }
	/**
     * 余额通兑积分
     *
     * @return \think\response\View
     */
    public function balanceExchangeIntegral()
    {
        if (request()->isAjax()) {
            $balance = (float)$_POST["amount"];
            $shop_id = isset($_POST["shop_id"]) ? $_POST["shop_id"] : '';
            $result = $this->user->memberBalanceToPoint($this->uid, $shop_id, $balance);
            return AjaxReturn($result);
        }
        else{
            // 获取兑换比例
            $account = new MemberAccount();
            $accounts = $account->getConvertRate($this->shop_id);

            // 查看积分总数
            $memberAccount = new MemberAccount();
            $balance = $memberAccount->getMemberBalance($this->uid);
            $conponSum = Db::table('ns_member_account')->where(['uid'=>$this->uid])->field('point')->find();
            $this->assign('balance', $balance);
            $this->assign('conponSum', $conponSum);
            $this->assign('convert_rate', $accounts['convert_rate']);
            return view($this->style . "/Member/balanceExchangeIntegral");
        }
    }
    /**
     * 积分兑换余额
     *
     * @return \think\response\View
     */
    public function integralExchangeBalance()
    {
        // 获取兑换比例
        $account = new MemberAccount();
        $accounts = $account->getConvertRate($this->shop_id);
        
        // 查看积分总数
//        $conponSum = $conponAccount->getMemberAccount($this->shop_id, $this->uid, 1);
        $member_account = new MemberAccount();
        $conponSum = $member_account->getMemberPoint($this->uid);

        $this->assign('conponSum', $conponSum);
        $this->assign('accounts', $accounts['convert_rate']);
        return view($this->style . "/Member/integralExchangeBalance");
    }

    /**
     * 积分兑换余额
     *
     * @return \think\response\View
     */
    public function ajaxIntegralExchangeBalance()
    {
        $point = (float) $_POST["amount"];
        $shop_id = isset($_POST["shop_id"]) ? $_POST["shop_id"] : '';
        $result = $this->user->memberPointToBalance($this->uid, $shop_id, $point);
        return AjaxReturn($result);
    }
	/**
	 * 
	 * 转赠积分
	 * 
	 **/
	public function giftGiving(){
        if (request()->isAjax()) {
            $user_tel = isset($_POST["user_tel"]) ? $_POST["user_tel"] : '';
            $point = isset($_POST["point"]) ? $_POST["point"] : -1;
            if($user_tel == ''){
                return '请输入电话号码！';
            }
            $member = new MemberService();
            $user = $member->getMemberList(1, 0, ["su.user_tel"=>$user_tel]);
            if($user['total_count'] != 1){
                return '未能确认受让人信息！'.$user['total_count'];
            }
            if($point == -1){
                return 1;
            }
            if($point <= 0){
                return '转赠数量必须大于0！';
            }
            $retr = $member->accountGiving($user['data'][0]['uid'], $point, 1);
            return AjaxReturn($retr);
        }
		return view($this->style . "Member/giftGiving");
	}
    /**
     * 账户列表
     * 任鹏强
     * 2017年3月13日10:52:59
     */
    public function accountList()
    {
        $flag = isset($_GET["flag"]) ? $_GET["flag"] : "0"; // 标识，1：从会员中心的提现账号进来，0：从申请提现进来
        if ($flag != 0) {
            $_SESSION['account_flag'] = $flag;
        } else {
            if (! empty($_SESSION['account_flag'])) {
                $flag = $_SESSION['account_flag'];
            }
        }
        $account_list = 1;
        $this->assign('flag', $flag);
        $member = new MemberService();
        $account_list = $member->getMemberBankAccount();
        $this->assign('account_list', $account_list);
        return view($this->style . "/Member/accountList");
    }

    /**
     * 添加账户
     * 任鹏强
     * 2017年3月13日10:53:06
     */
    public function addAccount()
    {
        if (request()->isAjax()) {
            $member = new MemberService();
            $uid = $this->uid;
            $realname = isset($_POST['realname']) ? $_POST['realname'] : '';
			$card_type = isset($_POST['$card_type']) ? $_POST['$card_type'] : '';
            $card_num = isset($_POST['card_num']) ? $_POST['card_num'] : '';
            $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
            $bank_type = isset($_POST['bank_type']) ? $_POST['bank_type'] : '1';
            $account_number = isset($_POST['account_number']) ? $_POST['account_number'] : '';
            $branch_bank_name = isset($_POST['branch_bank_name']) ? $_POST['branch_bank_name'] : '';
            $retval = $member->addMemberBankAccount($uid, $bank_type, $branch_bank_name, $realname,$card_type,$card_num, $account_number, $mobile);
            return AjaxReturn($retval);
        } else {
            return view($this->style . "/Member/addAccount");
        }
    }

    /**
     * 修改账户信息
     */
    public function updateAccount()
    {
        $member = new MemberService();
        if (request()->isAjax()) {
            $uid = $this->uid;
            $account_id = isset($_POST['id']) ? $_POST['id'] : '';
            $realname = isset($_POST['realname']) ? $_POST['realname'] : '';
            $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
            $bank_type = isset($_POST['bank_type']) ? $_POST['bank_type'] : '1';
            $account_number = isset($_POST['account_number']) ? $_POST['account_number'] : '';
            $branch_bank_name = isset($_POST['branch_bank_name']) ? $_POST['branch_bank_name'] : '';
            $retval = $member->updateMemberBankAccount($account_id, $branch_bank_name, $realname, $account_number, $mobile);
            return AjaxReturn($retval);
        } else {
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $result = $member->getMemberBankAccountDetail($id);
            $this->assign('result', $result);
            return view($this->style . "/Member/updateAccount");
        }
    }

    /**
     * 删除账户信息
     */
    public function delAccount()
    {
        if (request()->isAjax()) {
            $member = new MemberService();
            $uid = $this->uid;
            $account_id = isset($_POST['id']) ? $_POST['id'] : '';
            $retval = $member->delMemberBankAccount($account_id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 设置选中账户
     */
    public function checkAccount()
    {
        if (request()->isAjax()) {
            $member = new MemberService();
            $uid = $this->uid;
            $account_id = isset($_POST['id']) ? $_POST['id'] : '';
            $retval = $member->setMemberBankAccountDefault($uid, $account_id);
            return AjaxReturn($retval);
        }
    }

    /**
     * 获取微信推广二维码
     */
    public function getWchatQrcode()
    {
        // 获取微信配置
        $config = new Config();
        $auth_info = $config->getInstanceWchatConfig($this->instance_id);
        if (! isWeixin()) {
            $this->assign("is_weixin", - 1);
        } else 
            if ($auth_info['value']['appid'] == '') {
                $this->assign("is_weixin", 0);
            } else {
                $this->assign("is_weixin", 1);
            }
        $uid = $this->user->getSessionUid();
        $instance_id = $this->instance_id;
        $this->assign("shop_id", $instance_id);
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("signPackage", $ticket);
        
        return view($this->style . "/Member/myqrcode");
    }

    /**
     * 生成个人店铺二维码
     */
    public function getShopQrcode()
    {    
        if ($this->shop_id > 0) {
            $shop = new Shop();
            $shop_info = $shop->getShopInfo($this->shop_id);
            $info["logo"] = $shop_info["shop_logo"];
            $info["shop_name"] = $shop_info["shop_name"];
            $info["phone"] = $shop_info["shop_phone"];
            $info["address"] = $shop_info["live_store_address"];
        } else {
            $weisite = new WebSite();
            $weisite_info = $weisite->getWebSiteInfo();
            $info["logo"] = $weisite_info["logo"];
            $info["shop_name"] = $weisite_info["title"];
            $info["phone"] = $weisite_info["web_phone"];
            $info["address"] = $weisite_info["web_address"];
        }
        $this->assign("info", $info);
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("signPackage", $ticket);
        return view($this->style . "/Member/shopqrcode");
    }

    /**
     * 用户更换模板
     */
    public function updateUserQrcodeTemplate()
    {
        $uid = $this->user->getSessionUid();
        $instance_id = $this->instance_id;
        // 获取微信配置
        $config = new Config();
        $auth_info = $config->getInstanceWchatConfig($this->instance_id);
        if (! isWeixin()) {
            $this->assign("is_weixin", - 1);
        } else 
            if ($auth_info['value']['appid'] == '') {
                $this->assign("is_weixin", 0);
            } else {
                $this->assign("is_weixin", 1);
            }
        $weixin = new Weixin();
        $data = $weixin->updateMemberQrcodeTemplate($instance_id, $uid);
        $this->assign("shop_id", $instance_id);
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("signPackage", $ticket);
        return view($this->style . "/Member/myqrcode");
    }

    /**
     * 制作推广二维码
     */
    function showUserQrcode()
    {
        $uid = $this->user->getSessionUid();
        $instance_id = $this->instance_id;
        // 读取生成图片的位置配置
        $weixin = new Weixin();
        $data = $weixin->getWeixinQrcodeConfig($instance_id, $uid);
        $member_info = $this->user->getUserDetail();
        // 获取所在店铺信息
        $web = new WebSite();
        $shop_info = $web->getWebDetail();
        $shop_logo = $shop_info["logo"];
        
        $upload_path = "upload/qrcode/promote_qrcode/user"; // 推广二维码手机端展示
        if (! file_exists($upload_path)) {
            $mode = intval('0777',8);
            mkdir($upload_path, $mode, true);
        }
        // 查询并生成二维码
        $path = $upload_path . '/qrcode_' . $uid . '_' . $instance_id . '.png';
        
        if (! file_exists($path)) {
            $weixin = new Weixin();
            $url = $weixin->getUserWchatQrcode($uid, $instance_id);
            if ($url == WEIXIN_AUTH_ERROR) {
                exit();
            } else {
                getQRcode($url, $upload_path, "qrcode_" . $uid . '_' . $instance_id);
            }
        }
        // 定义中继二维码地址
        $thumb_qrcode = $upload_path . '/thumb_' . 'qrcode_' . $uid . '_' . $instance_id . '.png';
        $image = \think\Image::open($path);
        // 生成一个固定大小为360*360的缩略图并保存为thumb_....jpg
        $image->thumb(288, 288, \think\Image::THUMB_CENTER)->save($thumb_qrcode);
        // 背景图片
        $dst = $data["background"];
        if (! file_exists($dst)) {
            $dst = "public/static/images/qrcode_bg/qrcode_user_bg.png";
        }
        // 生成画布
        list ($max_width, $max_height) = getimagesize($dst);
        $dests = imagecreatetruecolor($max_width, $max_height);
        $dst_im = getImgCreateFrom($dst);
        imagecopy($dests, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
        imagedestroy($dst_im);
        // 并入二维码
        // $src_im = imagecreatefrompng($thumb_qrcode);
        $src_im = getImgCreateFrom($thumb_qrcode);
        $src_info = getimagesize($thumb_qrcode);
        imagecopy($dests, $src_im, $data["code_left"] * 2, $data["code_top"] * 2, 0, 0, $src_info[0], $src_info[1]);
        imagedestroy($src_im);
        // 并入用户头像
        $user_headimg = $member_info["user_headimg"];
        // $user_headimg = "upload/user/1493363991571.png";
        if (! file_exists($user_headimg)) {
            $user_headimg = "public/static/images/qrcode_bg/head_img.png";
        }
        $src_im_1 = getImgCreateFrom($user_headimg);
        $src_info_1 = getimagesize($user_headimg);
        // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
        imagecopyresampled($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, 80, 80, $src_info_1[0], $src_info_1[1]);
        // imagecopy($dests, $src_im_1, $data['header_left'] * 2, $data['header_top'] * 2, 0, 0, $src_info_1[0], $src_info_1[1]);
        imagedestroy($src_im_1);
        
        // 并入网站logo
        if ($data['is_logo_show'] == '1') {
            // $shop_logo = $shop_logo;
            if (! file_exists($shop_logo)) {
                $shop_logo = "public/static/images/logo.png";
            }
            $src_im_2 = getImgCreateFrom($shop_logo);
            $src_info_2 = getimagesize($shop_logo);
            imagecopy($dests, $src_im_2, $data['logo_left'] * 2, $data['logo_top'] * 2, 0, 0, $src_info_2[0], $src_info_2[1]);
            imagedestroy($src_im_2);
        }
        // 并入用户姓名
        $rgb = hColor2RGB($data['nick_font_color']);
        $bg = imagecolorallocate($dests, $rgb['r'], $rgb['g'], $rgb['b']);
        $name_top_size = $data['name_top'] * 2 + $data['nick_font_size'];
        @imagefttext($dests, $data['nick_font_size'], 0, $data['name_left'] * 2, $name_top_size, $bg, "public/static/font/Microsoft.ttf", $member_info["nick_name"]);
        header("Content-type: image/jpeg");
        imagejpeg($dests);
    }

    /**
     * 制作店铺二维码
     */
    function showShopQecode()
    {
        $uid = $this->user->getSessionUid();
        $instance_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : 0;
        if ($instance_id == 0) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . '/wap?source_uid=' . $uid;
        } else {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . '/wap/shop/index?shop_id=' . $instance_id . '&source_uid=' . $uid;
        }
        // 查询并生成二维码
        
        $upload_path = "upload/qrcode/promote_qrcode/shop"; // 后台推广二维码模版
        if (! file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        $path = $upload_path . '/shop_' . $uid . '_' . $instance_id . '.png';
        if (! file_exists($path)) {
            getQRcode($url, $upload_path, "shop_" . $uid . '_' . $instance_id);
        }
        
        // 定义中继二维码地址
        $thumb_qrcode = $upload_path . '/thumb_shop_' . 'qrcode_' . $uid . '_' . $instance_id . '.png';
        $image = \think\Image::open($path);
        // 生成一个固定大小为360*360的缩略图并保存为thumb_....jpg
        $image->thumb(260, 260, \think\Image::THUMB_CENTER)->save($thumb_qrcode);
        // 背景图片
        $dst = "public/static/images/qrcode_bg/shop_qrcode_bg.png";
        
        // $dst = "http://pic107.nipic.com/file/20160819/22733065_150621981000_2.jpg";
        // 生成画布
        list ($max_width, $max_height) = getimagesize($dst);
        $dests = imagecreatetruecolor($max_width, $max_height);
        $dst_im = getImgCreateFrom($dst);
        // if (substr($dst, - 3) == 'png') {
        // $dst_im = imagecreatefrompng($dst);
        // } elseif (substr($dst, - 3) == 'jpg') {
        // $dst_im = imagecreatefromjpeg($dst);
        // }
        imagecopy($dests, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
        imagedestroy($dst_im);
        // 并入二维码
        // $src_im = imagecreatefrompng($thumb_qrcode);
        $src_im = getImgCreateFrom($thumb_qrcode);
        $src_info = getimagesize($thumb_qrcode);
        imagecopy($dests, $src_im, "94px" * 2, "170px" * 2, 0, 0, $src_info[0], $src_info[1]);
        imagedestroy($src_im);
        // 获取所在店铺信息
        // 获取所在店铺信息
        if ($instance_id == 0) {
            $web = new WebSite();
            $shop_info = $web->getWebDetail();
            $shop_logo = $shop_info["logo"];
            $shop_name = $shop_info["title"];
            $shop_phone = $shop_info["web_phone"];
            $live_store_address = $shop_info["web_address"];
        } else {
            $shop = new Shop();
            $shop_info = $shop->getShopInfo($instance_id);
            $shop_logo = $shop_info["shop_logo"];
            $shop_name = $shop_info["shop_name"];
            $shop_phone = $shop_info["shop_phone"];
            $live_store_address = $shop_info["live_store_address"];
        }
        // logo
        if (! file_exists($shop_logo)) {
            $shop_logo = "public/static/images/logo.png";
        }
        // if (substr($shop_logo, - 3) == 'png') {
        // $src_im_2 = imagecreatefrompng($shop_logo);
        // } elseif (substr($shop_logo, - 3) == 'jpg') {
        // $src_im_2 = imagecreatefromjpeg($shop_logo);
        // }
        $src_im_2 = getImgCreateFrom($shop_logo);
        $src_info_2 = getimagesize($shop_logo);
        imagecopy($dests, $src_im_2, "10px" * 2, "380px" * 2, 0, 0, $src_info_2[0], $src_info_2[1]);
        imagedestroy($src_im_2);
        // 并入用户姓名
        $rgb = hColor2RGB("#333333");
        $bg = imagecolorallocate($dests, $rgb['r'], $rgb['g'], $rgb['b']);
        $name_top_size = "430px" * 2 + "23";
        @imagefttext($dests, 23, 0, "10px" * 2, $name_top_size, $bg, "public/static/font/Microsoft.ttf", "店铺名称：" . $shop_name);
        @imagefttext($dests, 23, 0, "10px" * 2, $name_top_size + 50, $bg, "public/static/font/Microsoft.ttf", "电话号码：" . $shop_phone);
        @imagefttext($dests, 23, 0, "10px" * 2, $name_top_size + 100, $bg, "public/static/font/Microsoft.ttf", "店铺地址：" . $live_store_address);
        header("Content-type: image/jpeg");
        imagejpeg($dests);
    }
    // 用户签到
    public function signIn()
    {
        if (request()->isAjax()) {
            $rewardRule = new PromoteRewardRule();
            $res = $rewardRule->memberSign($this->uid, $this->instance_id);
            return AjaxReturn($res);
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
     * 用户充值余额
     */
    public function recharge()
    {
        $num = isset($_GET['num']) ? $_GET['num'] : '';
		$goodsid = isset($_GET['goodsid']) ? $_GET['goodsid'] : '';
        $pay = new UnifyPay();
        $pay_no = $pay->createOutTradeNo();
     	$balance = Db::table('ns_member_account')->where(['uid'=>$this->uid])->field('balance')->find();
     	$this->assign('num', $num);
        $this->assign('balance', $balance);
        $this->assign("pay_no", $pay_no);
        $this->assign("goodsid", $goodsid);//首单包邮全免用
        return view($this->style . "/Member/recharge");
    }

    /**
     * 创建充值订单
     */
    public function createRechargeOrder()
    {
        $recharge_money = isset($_POST['recharge_money']) ? $_POST['recharge_money'] : 0;
        $out_trade_no = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
        if (empty($recharge_money) || empty($out_trade_no)) {
            return AjaxReturn(0);
        } else {
            $member = new MemberService();
            $retval = $member->createMemberRecharge($recharge_money, $this->uid, $out_trade_no);
            return AjaxReturn($retval);
        }
    }
	/**
     * 我的销售
     */
    public function sales()
    {
		$nsmember = new NsMemberModel();
		$team=$nsmember->where('path_pid','like','%#'.$this->uid.'#%')->whereOr('pid',$this->uid)->select();
		$tot_mem=0;$tot_jingli=0;$tot_zongjian=0;
		$zhi_mem=0;$zhi_jingli=0;$zhi_zongjian=0;
		$cong_mem=0;$cong_jingli=0;$cong_zongjian=0;
		$all_mem=array($this->uid);//所有的成员，包括自己
		foreach($team as $key=>$val){
			if($val['pid']==$this->uid){
				$all_mem[]=$val['uid'];
				if($val['grade']==2){$zhi_jingli++;} elseif($val['grade']==5){$zhi_zongjian++;} else {$zhi_mem++;}
				if($val['grade']==2){$tot_jingli++;} elseif($val['grade']==5){$tot_zongjian++;} else {$tot_mem++;}
			} else {
				$once=explode('#'.$this->uid.'#',$val['path_pid']);
				if(substr_count($once[1],'#')==0){
					$all_mem[]=$val['uid'];
					if($val['grade']==2){$cong_jingli++;} elseif($val['grade']==5){$cong_zongjian++;} else {$cong_mem++;}
					if($val['grade']==2){$tot_jingli++;} elseif($val['grade']==5){$tot_zongjian++;} else {$tot_mem++;}
				}
			}
		}
		
		$condition['buyer_id'] = array('in',$all_mem);
		$condition['pay_status'] = 2;
		$commission=0;
		$salesRec=Db::table('ns_order')->where($condition)->field('order_id,buyer_id,order_money,refund_money')->select();
		foreach($salesRec as $k=>$v){
			$commission=$commission+$v['order_money']-$v['refund_money'];
		}
		/*购买会员卡的金额目前需计入*/
		$commission_arr=$this->cardConsume($all_mem);
		$commission+=$commission_arr['card_commission'];
		$this->assign("mem_num", array("tot_mem"=>$tot_mem,"tot_jingli"=>$tot_jingli,"tot_zongjian"=>$tot_zongjian));
		$this->assign("zhi_num", array("zhi_mem"=>$zhi_mem,"zhi_jingli"=>$zhi_jingli,"zhi_zongjian"=>$zhi_zongjian));
		$this->assign("cong_num", array("cong_mem"=>$cong_mem,"cong_jingli"=>$cong_jingli,"cong_zongjian"=>$cong_zongjian));
		$this->assign("commission",$commission);
        return view($this->style . "/Member/sale");
    }
	/*会员计算购买JPlus会员卡的消费金额*/
	public function cardConsume($all_mem)
    {
		$today=date("Y-m-d",time());
		$card_commission=0;
		$today_commission=0;
		$convert_rate=Db::table('ns_point_config')->where('shop_id',0)->value('convert_rate');
		foreach($all_mem as $key=>$val){
			$condition_card['uid'] = $val;
			$condition_card['sign'] = 0;
			$condition_card['from_type'] = 25;//购买会员卡对应类型
			$condition_card['text'] = '购买JPlus会员';
			$commission_rec=Db::table('ns_member_account_records')->where($condition_card)->select();
			
			foreach($commission_rec as $k=>$v){
				if($v['account_type']==1){
					$card_commission=$card_commission-round($v['number']/$convert_rate,2); //记录是负数
					if(strpos($v['create_time'],$today)!== false){
						$today_commission=$today_commission-round($v['number']/$convert_rate,2);
					}
				} else {
					$card_commission=$card_commission-$v['number'];
					if(strpos($v['create_time'],$today)!== false){
						$today_commission=$today_commission-$v['number'];
					}
				}
			}
		}
		return array('card_commission'=>$card_commission,'today_commission'=>$today_commission);
	}
    /**
     * 新增会员
     */
    public function addMembers()
    {
		$nsmember = new NsMemberModel();
		$team=$nsmember->where('path_pid','like','%#'.$this->uid.'#%')->whereOr('pid',$this->uid)->select();
		$tot_mem=0;$tot_jingli=0;$tot_zongjian=0;$total=0;
		$today=date("Y-m-d",time());
		$add_mem=array();
		$upgrade_mem=array();
		foreach($team as $key=>$val){
			$once=explode('#'.$this->uid.'#',$val['path_pid']);
			if(substr_count($once[1],'#')==0 || $val['pid']==$this->uid){
				if(strpos($val['reg_time'],$today) !== false){
					$num = $val['uid'];
					$bit = 8;
					$num_len = strlen($num);
					$zero = '';
					for($i=$num_len; $i<$bit-1; $i++){
					 $zero .= "0";
					}
					$val['uid'] = "0".$zero.$num;
					$add_mem[]=$val;
				}
				if(strpos($val['assign_jplus_time'],$today) !== false && $val['grade']==5){
					$num = $val['uid'];
					$bit = 8;
					$num_len = strlen($num);
					$zero = '';
					for($i=$num_len; $i<$bit-1; $i++){
					 $zero .= "0";
					}
					$val['uid'] = "0".$zero.$num;
					$upgrade_mem[]=$val;
				}
				$total++;
				if($val['grade']==2){
					$tot_jingli++;
				} elseif($val['grade']==5){
					$tot_zongjian++;
				} else {
					$tot_mem++;
				}
			}
		}
		$this->assign("total", $total);
		$this->assign("tot_mem", $tot_mem);
		$this->assign("tot_jingli", $tot_jingli);
		$this->assign("tot_zongjian", $tot_zongjian);
		$this->assign("add_mem", $add_mem);
		$this->assign("upgrade_mem", $upgrade_mem);
        return view($this->style . "/Member/addMembers");
    }
    /**
     * 卡的权益
     */
    public function myBagData()
    {
        return view($this->style . "/Member/myBagData");
    }
    /**
     * 我的卡包
     */
    public function myBag()
    {
    	$uid 			= $this->uid;
		$goods         	= new Goods();
//		$group_id_array	=array();
		//获取会员卡商品的商品id
//		$result= Db::table('ns_goods')->where(['group_id_array'=>111])->field('goods_id')->select();
//		foreach($result as $key=>$val){
//			$group_id_array[]=$val['goods_id'];
//		}
		//根据买家id和支付状态为已付款----获取订单号
	
		//根据买家id和商品是会员卡---获取订单号
//		$re = Db::table('ns_order_goods')->where(['buyer_id'=>$uid,'goods_id'=>array('in',$group_id_array)])->field('order_id')->select();
//		$order_id = array();
//		foreach($re as $key=>$val){
//			$order_id[]=$val['order_id'];
//		}
		if(!empty($order_id)){
			//根据买家id和支付状态为已付款----获取订单号
			$reg = Db::table('ns_order')->where(['pay_status'=>2, 'buyer_id'=>$uid, 'order_id'=>array('in',$order_id)])->field('order_id')->select();
			//获取支付时间,计算卡的到期时间
			$time = Db::table('ns_order')->where(['pay_status'=>2, 'buyer_id'=>$uid, 'order_id'=>array('in',$order_id)])->field('pay_time')->find();
			$time = strtotime($time['pay_time']);
			$time1 = date('Y-m-d',($time+365*24*60*60)-1*24*60*60);
			//根据买家id和订单id和商品是会员卡----获取买家购买的会员卡商品
	    	$oder = new OrderGoods();
			$res = Db::table('ns_order_goods')->where(['buyer_id'=>$uid, 'order_id'=>array('in',$order_id)])->field('goods_id')->select();
			$mycard = array();
			foreach($res as $key=>$val){
				$mycard[] = $val['goods_id'];
			}
		}
		$this->assign('time1', $time1);
		$this->assign('mycard', $mycard);
        return view($this->style . "/Member/myBag");
    }
	/**
     * 会员卡及商品的分红佣金计算
     *
     */
//    public function card_commission()
    public function card_commission_cancel()
    {
        //KTV会员卡
		$group_id_array=array();
		$card_ids=Db::table('ns_goods')->where(['group_id_array'=>111])->field('goods_id')->select(); //怀化会员卡的编号是111
		foreach($card_ids as $k=>$v){
			$group_id_array[]=$v['goods_id'];
		}
        //$notake_ordergoods=Db::table('ns_order_goods')->where(['is_take'=>0,'goods_id'=>array('in',$group_id_array)])->select();
		//订单商品表中(is_take=>0)为未提成为分红的订单
        $notake_ordergoods=Db::table('ns_order_goods')->where(['is_take'=>0])->select();
		foreach($notake_ordergoods as $k=>$v){
			$oneorder=Db::table('ns_order')->where(['order_id'=>$v['order_id']])->find();
			if($oneorder['pay_status']==2){
				$path_pid=Db::table('ns_member')->where(['uid'=>$v['buyer_id']])->value('path_pid');
				if(empty($path_pid)){
					Db::table('ns_order_goods')->where(['order_goods_id'=>$v['order_goods_id']])->update(['is_take' =>2]); //2表示另一种情形
				} else {
					$path_arr=explode('#',$path_pid);
					$count=count($path_arr);
					if(!empty($path_arr[$count-1])){
						//是否会员卡在此处判断处理提成金额  团队奖励未计算！招商的！
						if(in_array($v['goods_id'],$group_id_array)){  //如果是怀化的会员卡
							if($v['price']==1680)
							{$zhi_comm=400;} elseif ($v['price']==2980){$zhi_comm=400;} elseif ($v['price']==12800){$zhi_comm=500;} else {$zhi_comm=0;}
							$zhi_comm=$zhi_comm*$v['num'];
							$text='会员卡销售分红';
						} 
						/*
						elseif(){
							//如果不是怀化的会员卡
							$grade=Db::table('ns_member')->where(['uid'=>$path_arr[$count-1]])->value('grade');
							if($grade==5){$zhi_comm=50*$v['num'];}
							elseif($grade==2){$zhi_comm=40*$v['num'];}
							else {$zhi_comm=30*$v['num'];}
							$text='会员卡销售分红';
						} */
						else {
							//如果是其它商品
                            //获取会员等级 会员的等级不同,分销比例不同
							$grade=Db::table('ns_member')->where(['uid'=>$path_arr[$count-2]])->value('grade');
							//计算利差
							$profit=($v['price']-$v['cost_price'])*$v['num'];
							//按会员级别
							if($grade==5){$zhi_comm=$profit*0.06;}
							elseif($grade==2){$zhi_comm=$profit*0.05;}
							else {$zhi_comm=$profit*0.04;}
							$text='商品销售分红';
						}
						//此处进行预处理和最终处理  如果订单过了收货后的退货期，加入余额，否则只展示不加入余额！
						$one_records=Db::table('ns_member_account_records')->where(['uid'=>$path_arr[$count-1],'account_type' =>2,'from_type'=>15,'data_id'=>$v['order_goods_id']])->find();
						//订单完成与未完成
						if($oneorder['order_status']==4){  //看5订单关闭会否进入该环节
							//无退货
							if($v['refund_real_money']==0){
								if(empty($one_records)){
									Db::table('ns_order_goods')->where(['order_goods_id'=>$v['order_goods_id']])->update(['is_take' =>1]);
									Db::table('ns_member_account')->where(['uid'=>$path_arr[$count-1]])->setInc('balance',$zhi_comm);
									$data = ['uid' =>$path_arr[$count-1],'account_type' =>2,'sign' =>1,'number' =>$zhi_comm,'from_type'=>15,'data_id'=>$v['order_goods_id'],'text'=>$text,'create_time'=>date('Y-m-d h:i:s', time()),'is_add'=>1];
									Db::table('ns_member_account_records')->insert($data);
								} else {
									Db::table('ns_member_account')->where(['uid'=>$path_arr[$count-1]])->setInc('balance',$zhi_comm);
									Db::table('ns_member_account_records')->where(['id'=>$one_records['id']])->update(['is_add' =>1]);
								}
							} else {   //有退货
								if(!empty($one_records)){
									Db::table('ns_member_account_records')->where(['id'=>$one_records['id']])->delete();
								}
							}
						} else {
							if(empty($one_records)){
								$data = ['uid' =>$path_arr[$count-1],'account_type' =>2,'sign' =>1,'number' =>$zhi_comm,'from_type'=>15,'data_id'=>$v['order_goods_id'],'text'=>$text,'create_time'=>date('Y-m-d h:i:s', time()),'is_add'=>0];
								Db::table('ns_member_account_records')->insert($data);
							}
						}
						//直推（只可使用余额）预处理显示出来
					}
					if(!empty($path_arr[$count-2])){   //团队奖励未计算！招商的！退积分给用户及商铺未计算！
						if(in_array($v['goods_id'],$group_id_array)){  //如果是怀化的会员卡
							if($v['price']==1680){$jian_comm=200;} elseif ($v['price']==2980){$jian_comm=200;} elseif ($v['price']==12800){$jian_comm=1000;} else {$jian_comm=0;}
							$jian_comm=$jian_comm*$v['num'];
							$text='会员卡销售分红';
						}
						/*
						elseif(){
							//如果不是怀化的会员卡
							$grade=Db::table('ns_member')->where(['uid'=>$path_arr[$count-2]])->value('grade');
							if($grade==5){$jian_comm=100*$v['num'];}
							elseif($grade==2){$jian_comm=80*$v['num'];}
							else {$jian_comm=70*$v['num'];}
							$text='会员卡销售分红';
						}  */
						else {
							//如果是其它商品
							$grade=Db::table('ns_member')->where(['uid'=>$path_arr[$count-2]])->value('grade');
							$profit=($v['price']-$v['cost_price'])*$v['num'];
							if($grade==5){$jian_comm=$profit*0.12;}
							elseif($grade==2){$jian_comm=$profit*0.1;}
							else {$jian_comm=$profit*0.08;}
							$text='商品销售分红';
						}
						//间推（只可使用余额）预处理显示出来
						$one_records=Db::table('ns_member_account_records')->where(['uid'=>$path_arr[$count-2],'account_type' =>2,'from_type'=>15,'data_id'=>$v['order_goods_id']])->find();
						//订单完成与未完成
						if($oneorder['order_status']==4){  //看5订单关闭会否进入该环节
							//无退货
							if($v['refund_real_money']==0){
								if(empty($one_records)){
									Db::table('ns_order_goods')->where(['order_goods_id'=>$v['order_goods_id']])->update(['is_take' =>1]);
									Db::table('ns_member_account')->where(['uid'=>$path_arr[$count-2]])->setInc('balance',$jian_comm);
									$data = ['uid' =>$path_arr[$count-2],'account_type' =>2,'sign' =>1,'number' =>$jian_comm,'from_type'=>15,'data_id'=>$v['order_goods_id'],'text'=>$text,'create_time'=>date('Y-m-d h:i:s', time()),'is_add'=>1];
									Db::table('ns_member_account_records')->insert($data);
								} else {
									Db::table('ns_member_account')->where(['uid'=>$path_arr[$count-2]])->setInc('balance',$jian_comm);
									Db::table('ns_member_account_records')->where(['id'=>$one_records['id']])->update(['is_add' =>1]);
								}
							} else {   //有退货
								if(!empty($one_records)){
									Db::table('ns_member_account_records')->where(['id'=>$one_records['id']])->delete();
								}
							}
						} else {
							if(empty($one_records)){
								$data = ['uid' =>$path_arr[$count-2],'account_type' =>2,'sign' =>1,'number' =>$jian_comm,'from_type'=>15,'data_id'=>$v['order_goods_id'],'text'=>$text,'create_time'=>date('Y-m-d h:i:s', time()),'is_add'=>0];
								Db::table('ns_member_account_records')->insert($data);
							}
						}
						//间推（只可使用余额）预处理显示出来
					}
				}
			}
		}
    }
	/*获取自身直属和从属团队成员*/
	public function getTeam($uid)
    {
		$nsmember = new NsMemberModel();
		$team=$nsmember->where('path_pid','like','%#'.$uid.'#%')->whereOr('pid',$uid)->select();
		$zhi_team_id=array();
		$cong_team_id=array();
		$team_belong=array();
		foreach($team as $key=>$val){
			if($val['pid']==$uid){
				$zhi_team_id[]=$val['uid'];
				$team_belong[]=$val['uid'];
			} else {
				$once=explode('#'.$uid.'#',$val['path_pid']);
				if(substr_count($once[1],'#')==0){
					$cong_team_id[]=$val['uid'];
					$team_belong[]=$val['uid'];
				}
			}
		}
		return array('zhi_team_id'=>$zhi_team_id,'cong_team_id'=>$cong_team_id,'team_belong'=>$team_belong);
	}
    /**
     * 销售明细 购买会员卡的记录未加入！
     */
    public function salesDetails()
    { 
		$team_arr=$this->getTeam($this->uid);
		$zhi_team_id=$team_arr['zhi_team_id'];
		$cong_team_id=$team_arr['cong_team_id'];
		//查询直属会员订单销售记录.2指从属团队；1是直属团队
		if($_GET['type']==2){$all_mem=$cong_team_id;} else {$all_mem=$zhi_team_id;}
		if(empty($all_mem)){$salesRec=array();} else {
			$condition['buyer_id'] = array('in',$all_mem);
			$condition['pay_status'] = 2;
			$salesRec=Db::table('ns_order')->where($condition)->field('order_no,buyer_id,pay_time,order_money,refund_money')->select();
			$condition_card['uid'] = array('in',$all_mem);
			$condition_card['sign'] = 0;
			$condition_card['from_type'] = 25;//购买会员卡对应类型
			$condition_card['text'] = '购买JPlus会员';
			$commission_rec=Db::table('ns_member_account_records')->where($condition_card)->select();
			foreach($commission_rec as $key=>$val){
				$once['buyer_id']=$val['uid'];
				$once['order_no']='购买会员记录编号'.$val['id'];
				$once['order_money']=-$val['number'];
				$once['pay_time']=$val['create_time'];
				$salesRec[]=$once;
			}
		}
		foreach($salesRec as $k=>$v){
			/*
			$num = $v['buyer_id'];
			 $bit = 8;
			 $num_len = strlen($num);
			 $zero = '';
			 for($i=$num_len; $i<$bit-1; $i++){
			  $zero .= "0";
			 }
			 $real_num = "0".$zero.$num;
			 $salesRec[$k]['buyer_id']=$real_num;*/
			 $two=Db::table('sys_user')->where('uid',$v['buyer_id'])->field('user_name,user_tel')->find();
			 $salesRec[$k]['user_name']=$two['user_name'];
			 $salesRec[$k]['user_tel']=$two['user_tel'];
		}
		
		$this->assign('commission_orders', $salesRec);
        return view($this->style . "/Member/salesDetails");
    }
    /**
     * 提现页面
     */
    public function putForward()
    {
		$member = new MemberService();
        $member_info = $member->getMemberDetail();
		$this->assign('member_info', $member_info);
        return view($this->style . "/Member/putForward");
    }
    /**
     * 安全中心
     */
    public function securityCenter()
    {
    	$member = new MemberService();
        $member_info = $member->getMemberDetail($this->instance_id);
        $this->assign('member_info', $member_info);
        
        return view($this->style . "/Member/securityCenter");
    }
    /**
     * 实名制认证
     */
    public function realNameSystem()
    {
    	$member = new MemberService();
        $member_info = $member->getMemberDetail();
        $this->assign('member_info', $member_info);
//		print_r($authentication_time);
//		exit;
		$update_info_status = ""; // 修改信息状态 
        $upload_card_status = ""; //上传身份证状态 
        if (isset($_POST["submit"])) {
			$card_state = 1;
			$authentication_time = date("Y-m-d H:i:s",time());;
            $real_name = isset($_POST["real_name"]) ? $_POST["real_name"] : "";
            $ID_card = isset($_POST["ID_card"]) ? $_POST["ID_card"] : "";
            $ID_card_positive = isset($_POST["ID_card_positive"]) ? $_POST["ID_card_positive"] : "";
            $ID_card_reverse = isset($_POST["ID_card_reverse"]) ? $_POST["ID_card_reverse"] : "";

            // 把从前台显示的内容转变为可以存储到数据库中的数据
            $update_info_status = $this->user->updateMemberCard($real_name, $ID_card,$authentication_time, $ID_card_positive, $ID_card_reverse, $card_state);
        }
		
        if ($_FILES && isset($_POST["submit"])) {
            if ((($_FILES["ID_card_positive"]["type"] == "image/gif" && $_FILES["ID_card_reverse"]["type"] == "image/gif") || ($_FILES["ID_card_positive"]["type"] == "image/jpeg" && $_FILES["ID_card_reverse"]["type"] == "image/jpeg") || ($_FILES["ID_card_positive"]["type"] == "image/pjpeg" && $_FILES["ID_card_reverse"]["type"] == "image/pjpeg") || ($_FILES["ID_card_positive"]["type"] == "image/png" && $_FILES["ID_card_reverse"]["type"] == "image/png")) && ($_FILES["ID_card_positive"]["size"] < 10000000 && $_FILES["ID_card_reverse"]["size"] < 10000000)) {
                if ($_FILES["ID_card_positive"]["error"] > 0 && $_FILES["ID_card_reverse"]["error"] > 0 ) {
                    
                }
				     
                $file_name = date("YmdHis") .'z'. rand(0, date("is")); // 文件名
                $ext = explode(".", $_FILES["ID_card_positive"]["name"]);
                $file_name .= "." . $ext[1];
				
				$file_name1 = date("YmdHis") .'f'. rand(0, date("is")); // 文件名
                $ext1 = explode(".", $_FILES["ID_card_reverse"]["name"]);
                $file_name1 .= "." . $ext1[1];
                // 检测文件夹是否存在，不存在则创建文件夹
                $path = 'upload/avator/';
                if (! file_exists($path)) {
                    $mode = intval('0777', 8);
                    mkdir($path, $mode, true);
                }
                move_uploaded_file($_FILES["ID_card_positive"]["tmp_name"], $path . $file_name);
                move_uploaded_file($_FILES["ID_card_reverse"]["tmp_name"], $path . $file_name1);
                $ID_card_positive = $path . $file_name;
				$ID_card_reverse = $path . $file_name1;
                $upload_card_status = $this->user->updateMemberCard("", "","", $ID_card_positive, $ID_card_reverse, $card_state);
				$this->redirect('/wap/member/personaldata');
            } else {
                $this->error("请上传图片");
            }
        }
        
        return view($this->style . "/Member/realNameSystem");
    }
    /**
     * 绑定手机号
     */
    public function cellPhone()
    {
	    $member = new MemberService();
        $member_info = $member->getMemberDetail();
        $this->assign('member_info', $member_info);
        
        return view($this->style . "/Member/cellPhone");
    }
    /**
     * 推荐商户
     */
    public function merchant()
    {
        return view($this->style . "/Member/merchant");
    }
    /**
     * 个人信息
     */
    public function information()
    {
        $member = new MemberService();
        $member_info = $member->getMemberDetail();
        $this->assign('member_info', $member_info);
		//给用户的id前面补零---例如299变为00000299
		$num = $member_info['uid'];
		$bit = 8;
		$num_len = strlen($num);
		$zero = '';
		for($i=$num_len; $i<$bit-1; $i++){
		 $zero .= "0";
		}
		$real_num = "0".$zero.$num;
		$this->assign('real_num', $real_num);
        
        //推荐人的id前面补零---例如299变为00000299
        $num1 = $member_info['pid'];
		$bit1 = 8;
		$num_len1 = strlen($num1);
		$zero1 = '';
		for($i=$num_len1; $i<$bit1-1; $i++){
		 $zero1 .= "0";
		}
		$real_num1 = "0".$zero1.$num1;
		$this->assign('real_num1', $real_num1);
		//获取团队的人数
		$team_arr=$this->getTeam($member_info['uid']);
		$totle = count($team_arr['team_belong']);
		$this->assign('totle', $totle);
		//银行卡数
        $account_list = $member->getMemberBankAccount();
		$this->assign('account_tot', count($account_list));
        return view($this->style . "/Member/information");
    }

    /**
     * 申请提现
     */
    public function toWithdraw()
    {
        if (request()->isAjax()) {
            // 提现
            $uid = $this->uid;
            $withdraw_no = time() . rand(111, 999);
            $bank_account_id = request()->post('bank_account_id', '');
            $cash = request()->post('cash', '');
            $shop_id = $this->instance_id;
            $member = new MemberService();
            $retval = $member->addMemberBalanceWithdraw($shop_id, $withdraw_no, $uid, $bank_account_id, $cash);
            return AjaxReturn($retval);
        } else {
            $member = new MemberService();
            $account_list = $member->getMemberBankAccount(1);
            // 获取会员余额
            $uid = $this->uid;
            $shop_id = $this->shop_id;
            $members = new MemberAccount();
            $account = $members->getMemberBalance($uid);
			/*首单包邮全免提现控制开始  后续订单消减充值金额*/
			$condition = array(
				'uid' => $uid,
				'account_type' => 2,
				'from_type' => 4,
				'text' => ['like','%_shou-dang-mian'],
				);
			$rec=Db::table('ns_member_account_records')->where($condition)->order('create_time asc')->select();
			$chongzhi=Db::table('ns_member_account_records')->where($condition)->order('create_time asc')->sum('number');
			$shenyu = 0;
			if(!empty($rec)){
				$first_time=$rec[0]['create_time'];
				$allbuy=Db::table('ns_order')->where(['buyer_id'=>$uid,'pay_status'=>2,'refund_money'=>0,'order_status'=>4,'pay_money'=>['>',0],'create_time'=>['> time',$first_time]])->sum('order_money');
				//payment_type的所有类型都可以当充值的余额支付的
				$shenyu=$chongzhi-$allbuy;
			}
			//看后续订单有无消费掉充值金额再控制提现金额
			if($shenyu > 0){$account-=$shenyu;}
			if($account<0){$account=0;}
			/*首单包邮全免提现控制结束 不用该活动删除中间代码*/
            $instance_id = $this->instance_id;
            $this->assign('shop_id', $instance_id);
            $this->assign('account', $account);
            $config = new Config();
            $balanceConfig = $config->getBalanceWithdrawConfig($shop_id);
            // dump($balanceConfig);
            $cash = $balanceConfig['value']["withdraw_cash_min"];
            $this->assign('cash', $cash);
            $poundage = $balanceConfig['value']["withdraw_multiple"];
            $this->assign('poundage', $poundage);
            $withdraw_message = $balanceConfig['value']["withdraw_message"];
            $this->assign('withdraw_message', $withdraw_message);
            
            $this->assign('account_list', $account_list);
            return view($this->style . "/Member/toWithdraw");
        }
    }
    /**
     * 绑定时发送短信验证码或邮件验证码
     * @return number[]|string[]|string|mixed
     */
    function sendBindCode(){
            $email = request()->post('email', '');
            $mobile = request()->post('mobile','');
            $type = request()->post("type",'');
            
			if($type == 'email'){
				$result = Db::table('sys_user')->where('uid',$this->uid)->update(['user_email' => $email]);
			}elseif($type == 'mobile'){
				$result = Db::table('sys_user')->where('uid',$this->uid)->update(['user_tel' => $mobile]);
			}
			return 1;
    }
    /**
     * 检侧动态验证码是否输入正确
     */
    public function check_dynamic_code(){
        if(request()->isAjax()){
            $code = request()->post("mobile_code",'');
			$mobile=request()->post("mobile",'');
			/*获取存储的验证码并进行比较*/
			$onerec=Db::table("ns_mobile_msgs")->where("mobile",$mobile)->order('id desc')->find();
			$sendtime=strtotime($onerec['send_time']);
			$difference=time()-$sendtime;
			if($code==$onerec['code'] && $difference<300)  //验证码5分钟内有效
			{  
				return $result = array(
                    "code" => 1,
                    "message" => "动态验证码一致"
                );
			} else {
				return $result = array(
                    "code" => -1,
                    "message" => "动态验证码不一致"
                );
			}
            /*获取存储的验证码并比较结束*/
            
        }
    }
    /**
     * 检测验证码是否正确
     */
    public function check_code(){
        if(request()->isAjax()){
            $vertification = request()->post("vertification",'');
            if (! captcha_check($vertification)) {
                $result = [
                    'code' => -1,
                    'message' => "验证码错误"
                ];
                return $result;
            }
        }
    }
    /**
     * 设置用户支付密码
     */
    public function setUserPaymentPassword()
    {
        if (request()->isAjax()) {
            $uid = $this->uid;
            $payment_password = request()->post("payment_password", '');
            $member = new MemberService();
            $res = $member->setUserPaymentPassword($uid, $payment_password);
            return AjaxReturn($res);
        }
    }
	/**
     * 比较输入的用户支付密码
     */
    public function check_paypassword()
    {
        if (request()->isAjax()) {
            $uid = $this->uid;
            $payment_password = request()->post("paypassword", '');
            $user = new UserModel();
            $res = $user->where(['uid'=>$uid])->field('payment_password')->find();
			if($res['payment_password'] == md5($payment_password) ){
				$result = ['code' => 1,'message' => "支付密码一致"];
			} else {$result = ['code' => -1,'message' => "支付密码错误"];}
            return $result;
        }
    }
    /**
     * 修改用户支付密码
     */
    public function updateUserPaymentPassword()
    {
        if (request()->isAjax()) {
            $uid = $this->uid;
            $old_payment_password = request()->post("old_payment_password", '');
            $new_payment_password = request()->post("new_payment_password", '');
            $member = new MemberService();
            $res = $member->updateUserPaymentPassword($uid, $old_payment_password, $new_payment_password);
            return AjaxReturn($res);
        }
    }

    /*
 *
 * 订单评论
 */
    public function reviewCommodity()
    {
        // 先考虑显示的样式
        if (request()->isGet()) {
            $order_id = $_GET["orderid"];
            $order = new Order();
            $list = $order->getOrderGoods($order_id);
            $orderDetail = $order->getDetail($order_id);
            $this->assign("order_no", $orderDetail['order_no']);
            $this->assign("order_id", $order_id);
            $this->assign("list", $list);
            return view($this->style . '/Member/reviewCommodity');
            if (($orderDetail['order_status'] == 3 || $orderDetail['order_status'] == 4) && $orderDetail['is_evaluate'] == 0) {} else {
                $this->redirect("shop/member/index");
            }
        } else {
            return view($this->style . "Order/orderList");
        }
//        return view($this->style . '/Order/reviewCommodity');
    }
    /*
     *
     * 抽奖
     */
    public function achieveGift()
    {
        if (request()->isAjax()) {
            $uid = $this->uid;
            $gift_id = isset($_POST['gift_id']) ? $_POST['gift_id'] : '0';
            $goods_gift = new GoodsGift();
            $res = $goods_gift->userAchieveGift($uid, $gift_id, 0);
            return AjaxReturn($res);
        }
        else{
            $goods_gift = new GoodsGift();
            $res = $goods_gift->userAchieveGift(1, 1, 0);
            return $res;
        }
    }
	/**
     * 通兑积分抵扣10%金额
     */
    public function deductFromExc()
    {   //先做记录。支付成功后扣除通兑积分；变更记录
        if (request()->isAjax()) {
            $uid = $this->uid;
            $out_trade_no = request()->post("out_trade_no", '');
            $oneNo=Db::table('ns_order_payment')->where(['out_trade_no'=>$out_trade_no])->find();
			if (strpos($oneNo['pay_body'],'deduct_from_exchangePoint') !== false) {return ['code' => -1,'message' => "通兑积分已抵扣！"];}
			$excPoint=Db::table('ns_member_account')->where(['uid'=>$uid])->value('exc_point');
			$nownum=round($oneNo['pay_money']*0.1,2);
			$usedExc=0;
			$usedPay=0;
			if($nownum*5<=$excPoint){
				$usedExc=$nownum*5;
				$usedPay=$nownum;
			} else {
				$usedExc=$excPoint;
				$usedPay=round($excPoint/5,2);
			}
			//改变支付信息；做预先通兑积分异动记录
			if(round($oneNo['pay_money']*0.1,2)>0 && round($excPoint/5,2)>0){
				Db::table('ns_order_payment')->where(['id'=>$oneNo['id']])->update(['pay_money'=>$oneNo['pay_money']-$usedPay,'pay_body'=>$oneNo['pay_body'].';deduct_from_exchangePoint:'.$usedPay.';useExcpoint:'.$usedExc]);
				$ordrec=Db::table('ns_order')->where(['out_trade_no'=>$out_trade_no])->field('order_id,pay_money')->select();
				foreach($ordrec as $key=>$val){
					if($usedPay>0 && $val['pay_money']>=$usedPay){
						Db::table('ns_order')->where(['order_id'=>$val['order_id']])->update(['pay_money'=>$val['pay_money']-$usedPay]);
					}
					$usedPay-=$val['pay_money'];
				}
				$result = ['code' => 1,'message' => "通兑积分抵扣".$usedPay."元"];
			} else {
				$result = ['code' => -1,'message' => "通兑积分不能抵扣"];
			}
            return $result;
        }
    }
}