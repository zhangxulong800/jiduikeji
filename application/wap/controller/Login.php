<?php
/**
 * Login.php
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

use data\extend\ThinkOauth as ThinkOauth;
use data\model\UserModel;
use data\service\Goods as GoodsService;
use data\extend\WchatOauth;
use data\service\Member as Member;
use data\service\Config as WebConfig;
use data\service\WebSite as WebSite;
use data\service\Weixin;
use data\service\Shop;
use think\Controller;
use data\service\User;
use data\service\Config;
use think\Session;
use think\Log;
use think\Db;
use data\model\NsMemberModel as NsMemberModel;
\think\Loader::addNamespace('data', 'data/');

/**
 * 前台用户登录
 *
 * @author Administrator
 *        
 */
class Login extends Controller
{

    public $user;

    public $web_site;

    public $style;

    public $logo;

    protected $instance_id;

    protected $shop_name;

    // 验证码配置
    public $login_verify_code;
    
    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    public function init()
    {
        $instance_id = 0;
        $this->user = new Member();
        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        if ($web_info['wap_status'] == 2) {
            webClose($web_info['close_reason']);
        }

        $this->assign("platform_shopname", $this->user->getInstanceName()); // 平台店铺名称
        $this->assign("title", $web_info['title']);
        $this->logo = $web_info['logo'];
        $this->shop_name = $this->user->getInstanceName();
        $this->instance_id = 0;
        // 是否开启验证码
        $web_config = new Config();
        $this->login_verify_code = $web_config->getLoginVerifyCodeConfig($instance_id);
        $this->assign("login_verify_code", $this->login_verify_code["value"]);
        
        // 是否开启qq跟微信
        $tencent = new WebConfig();
        $qq_info = $tencent->getQQConfig($this->instance_id);
        $Wchat_info = $tencent->getWchatConfig($this->instance_id);
        $this->assign("qq_info", $qq_info);
        $this->assign("Wchat_info", $Wchat_info);
        
        $Config = new Config();
        $seoconfig = $Config->getSeoConfig($this->instance_id);
        $this->assign("seoconfig", $seoconfig);
        
        $this->style = 'wap/default/';
        $this->assign("style", 'wap/default');
    }
    /**
     * 检测微信浏览器并且自动登录
     */
    public function wchatLogin()
    {  
        // 微信浏览器自动注册并登录
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            
            if (empty($_SESSION['request_url'])) {
                $_SESSION['request_url'] = request()->url(true);
            }
            $domain_name = \think\Request::instance()->domain();
            if (! empty($_COOKIE[$domain_name . "member_access_token"])) {
                $token = json_decode($_COOKIE[$domain_name . "member_access_token"], true);
            } else {
                $wchat_oauth = new WchatOauth();
                $token = $wchat_oauth->get_member_access_token();
                if (! empty($token['access_token'])) {
                    setcookie($domain_name . "member_access_token", json_encode($token));
                }
            }
            $wchat_oauth = new WchatOauth();
            if (! empty($token['openid'])) {
                if (! empty($token['unionid'])) {
                    $wx_unionid = $token['unionid'];
                    $retval = $this->user->wchatUnionLogin($wx_unionid);
                    if ($retval == 1) {
                        $this->user->modifyUserWxhatLogin($token['openid'], $wx_unionid);
                    } elseif ($retval == USER_LOCK) {
                        $this->redirect(__URL__."/wap/Login/userLock");
                    } else {
                        $retval = $this->user->wchatLogin($token['openid']);
                        if ($retval == USER_NBUND) {
                            $info = $wchat_oauth->get_oauth_member_info($token);
                            $result = $this->user->registerMember('', '', '', '', '', '', $token['openid'], $info, $wx_unionid,'','');
                        } elseif ($retval == USER_LOCK) {
                            // 锁定跳转
                            $this->redirect(__URL__."/wap/Login/userLock");
                        }
                    }
                } else {
                    $wx_unionid = '';
                    $retval = $this->user->wchatLogin($token['openid']);
                    if ($retval == USER_NBUND) {
                        $info = $wchat_oauth->get_oauth_member_info($token);
                        
                        $result = $this->user->registerMember('', '', '', '', '', '', $token['openid'], $info, $wx_unionid,'','');
                    } elseif ($retval == USER_LOCK) {
                        // 锁定跳转
                        $this->redirect(__URL__."/wap/Login/userLock");
                    }
                }
                
                if (! empty($_SESSION['login_pre_url'])) {
                    $this->redirect($_SESSION['login_pre_url']);
                } else{
                    $this->redirect("index/index");
                }
            }
        }
    }

    public function index(){
        if (request()->isAjax()) {
            $user_name = request()->post('username', '');
            $password = request()->post('password', '');
            $mobile = request()->post('mobile', '');
            $sms_captcha = request()->post('sms_captcha', '');
            if (! empty($user_name)) {
                $retval = $this->user->login($user_name, $password);
            } else {
                $sms_captcha_code = Session::get('mobileVerificationCode');
                $sendMobile = Session::get('sendMobile');
//                 if ($sms_captcha == $sms_captcha_code && $sendMobile == $mobile && ! empty($sms_captcha_code)) {
                if ($sms_captcha == $sms_captcha_code && ! empty($sms_captcha_code)) {
                    $retval = $this->user->login($mobile, '');
                } else {
                    $retval = - 10;
                }
            }
            if($retval ==  1){
                if (! empty($_SESSION['login_pre_url'])) {
                    $retval = [
                        'code' => 1,
                        'url' => $_SESSION['login_pre_url']
                    ];
                } else {
                    $retval = [
                        'code' => 2,
                        'url' => 'index/index'
                    ];
                }  
            }else{
                $retval = AjaxReturn($retval);              
            }
            return $retval;         
        }
        
        // 没有登录首先要获取上一页
        $pre_url = '';
        $_SESSION['bund_pre_url'] = '';
        if (! empty($_SERVER['HTTP_REFERER'])) {
            $pre_url = $_SERVER['HTTP_REFERER'];
            if (strpos($pre_url, 'login')) {
                $pre_url = '';
            }
            $_SESSION['login_pre_url'] = $pre_url;
        }
        $config = new Config();
        $instanceid = 0;
        // 登录配置
        $web_config = new WebConfig();
        $loginConfig = $web_config->getLoginConfig();
        
        $loginCount = 0;
        if ($loginConfig['wchat_login_config']['is_use'] == 1) {
            $loginCount ++;
        }
        if ($loginConfig['qq_login_config']['is_use'] == 1) {
            $loginCount ++;
        }
        $this->assign("loginCount", $loginCount);
        $this->assign("loginConfig", $loginConfig);
        return view($this->style . '/Login/login');
    }
    /**
     * 对比当前微信用户是否已绑定
    */
    public function getWeixinUser()
    {
//        if (request()->isAjax()) {
            //微信浏览器中获取微信用户的openID,和数据库对比
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {

                if (empty($_SESSION['request_url'])) {
                    $_SESSION['request_url'] = request()->url(true);
                }
                $domain_name = \think\Request::instance()->domain();
                if (!empty($_COOKIE[$domain_name . "member_access_token"])) {
                    $token = json_decode($_COOKIE[$domain_name . "member_access_token"], true);
                } else {
                    $wchat_oauth = new WchatOauth();
                    $token = $wchat_oauth->get_member_access_token();
                }
                $userOpenid = Db::table('sys_user')
                    ->where('wx_openid', $token['openid'])
                    ->value('wx_openid');
                if (!empty($userOpenid)) {
                    return 1;
                } else {
                    return 0;
                }
            }
//        }
    }
    /**
     * 第三方登录登录
     */
    public function oauthLogin()
    {
        $config = new Config();
        $type = $_GET['type'];
        if ($type == "WCHAT") {
            $config_info = $config->getWchatConfig($this->instance_id);
            if (empty($config_info["value"]["APP_KEY"]) || empty($config_info["value"]["APP_SECRET"])) {
                $this->error("当前系统未设置微信第三方登录!");
            }
            if (isWeixin()) {
                $this->wchatLogin();
                if (! empty($_SESSION['login_pre_url'])) {
                    $this->redirect($_SESSION['login_pre_url']);
                } else{
                    $this->redirect(__URL__."/index/index");
				}
            }
        } else 
            if ($type == "QQLOGIN") {
                $config_info = $config->getQQConfig($this->instance_id);
                if (empty($config_info["value"]["APP_KEY"]) || empty($config_info["value"]["APP_SECRET"])) {
                    $this->error("当前系统未设置QQ第三方登录!");
                }
            }
        $_SESSION['login_type'] = $type;
        
        $test = ThinkOauth::getInstance($type);
        $this->redirect($test->getRequestCodeURL());
    }

    /**
     * 登录返回
     */
    public function callback()
    {
        var_dump($_SESSION['login_type']);
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        if (empty($code))
            die();
        if ($_SESSION['login_type'] == 'QQLOGIN') {
            $qq = ThinkOauth::getInstance('QQLOGIN');
            $token = $qq->getAccessToken($code);
            if (! empty($token['openid'])) {
                if (! empty($_SESSION['bund_pre_url'])) {
                    // 1.检测当前qqopenid是否已经绑定，如果已经绑定直接返回绑定失败
                    $bund_pre_url = $_SESSION['bund_pre_url'];
                    $_SESSION['bund_pre_url'] = '';
                    $is_bund = $this->user->checkUserQQopenid($token['openid']);
                    if ($is_bund == 0) {
                        // 2.绑定操作
                        $qq = ThinkOauth::getInstance('QQLOGIN', $token);
                        $data = $qq->call('user/get_user_info');
                        $_SESSION['qq_info'] = json_encode($data);
                        // 执行用户信息更新user服务层添加更新绑定qq函数（绑定，解绑）
                        $res = $this->user->bindQQ($token['openid'], json_encode($data));
                        // 如果执行成功执行跳转
                        
                        if ($res) {
                            $this->success('绑定成功', $bund_pre_url);
                        } else {
                            $this->error('绑定失败', $bund_pre_url);
                        }
                    } else {
                        $this->error('该qq已经绑定', $bund_pre_url);
                    }
                } else {
                    $retval = $this->user->qqLogin($token['openid']);
                    // 已经绑定
                    if ($retval == 1) {
                        if (! empty($_SESSION['login_pre_url'])) {
                            $this->redirect($_SESSION['login_pre_url']);
                        } else
                            $this->redirect(__URL__."/index/index");
                        // $this->success("登录成功", "Index/index");
                    }
                    if ($retval == USER_NBUND) {
                        $qq = ThinkOauth::getInstance('QQLOGIN', $token);
                        $data = $qq->call('user/get_user_info');
                        $_SESSION['qq_info'] = json_encode($data);
                        $result = $this->user->registerMember('', '123456', '', '', $token['openid'], json_encode($data), '', '', '');
                        if ($result > 0) {
                            if (! empty($_SESSION['login_pre_url'])) {
                                $this->redirect($_SESSION['login_pre_url']);
                            } else
                                $this->redirect(__URL__."/index/index");
                        }
                    }
                }
            }
        } elseif ($_SESSION['login_type'] == 'WCHAT') {
            $wchat = ThinkOauth::getInstance('WCHAT');
            $token = $wchat->getAccessToken($code);
            if (! empty($token['unionid'])) {
                $retval = $this->user->wchatUnionLogin($token['unionid']);
                
                // 已经绑定
                if ($retval == 1) {
                    if (! empty($_SESSION['login_pre_url'])) {
                        $this->redirect($_SESSION['login_pre_url']);
                    } else
                       $this->redirect(__URL__."/index/index");
                    // $this->success("登录成功", "Index/index");
                }
            }
            if ($retval == USER_NBUND) {
                // 2.绑定操作
                $wchat = ThinkOauth::getInstance('WCHAT', $token);
                $data = $wchat->call('sns/userinfo');
                $_SESSION['wx_info'] = json_encode($data);
                $result = $this->user->registerMember('', '123456', '', '', '', '', '', json_encode($data), $token['unionid'],'','');
                if ($result > 0) {
                    if (! empty($_SESSION['login_pre_url'])) {
                        $this->redirect($_SESSION['login_pre_url']);
                    } else
                        $this->redirect(__URL__."/index/index");
                }
            }
        }
    }

    /**
     * 微信授权登录返回
     */
    public function wchatCallBack()
    {
        $code = isset($_GET['code']) ? $_GET['code'] : '';
        if (empty($code))
            die();
        $wchat = ThinkOauth::getInstance('WCHATLOGIN');
        $token = $wchat->getAccessToken($code);
        $wchat = ThinkOauth::getInstance('WCHATLOGIN', $token);
        $data = $wchat->call('/sns/userinfo');
        var_dump($data);
    }

    /**
     */
    public function wchatOauth()
    {
        //$this->redirect('wchat/auth');
        echo "<a href='http://" . $_SERVER['HTTP_HOST'] . "/wap/wchat/auth'>点击授权</a>";
    }

    /**
     * 注册用户
     */
    public function addUser()
    {
        $user_name = isset($_POST["username"]) ? $_POST["username"] : '';
        $password = isset($_POST["password"]) ? $_POST["password"] : '';
        $email = isset($_POST["email"]) ? $_POST["email"] : '';
        $mobile = isset($_POST["mobile"]) ? $_POST["mobile"] : '';
        $is_system = 0;
        $is_member = 1;
        $qq_openid = isset($_POST["qq_openid"]) ? $_POST["qq_openid"] : '';
        $qq_info = isset($_SESSION['qq_info']) ? $_SESSION['qq_info'] : '';
        $wx_openid = isset($_POST["wx_openid"]) ? $_POST["wx_openid"] : '';
        $wx_info = isset($_POST["wx_info"]) ? $_POST["wx_info"] : '';
        if (empty($user_name)) {
            return AjaxReturn(0);
        } else {
            $result = $this->user->registerMember($user_name, $password, $email, $mobile, $qq_openid, $qq_info, $wx_openid, $wx_info,'','');
            if ($result > 0) {
                $this->user->qqLogin($qq_openid);
            }
        }
        
        return AjaxReturn($result);
    }

    /**
     * 注册账户
     */
    public function register()
    {
        if (request()->isAjax()) {
            $member = new Member();
            $user_name = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
			$code= isset($_POST['code']) ? $_POST['code'] : '';
			$pid=isset($_POST['pid']) ? $_POST['pid'] : 0;
			//根据用户提交的推荐人的名称或电话获取其id
			if(empty($pid)){
				$path_pid='';
			} else {
				$user  = new User();
				$user_data=$user->getUserInfoByUsername($pid);
				if(empty($user_data)){$user_data=$user->getUserInfoByMobile($pid);}
				if(empty($user_data)){
				    $pid = '';
					$path_pid='';
				} else {
					$pid   =$user_data['uid'];
					//获取推荐人的path_pid
					$mem   		= new NsMemberModel();
					$mem_data 	= $mem->getInfo(['uid'=>$pid]);
					$path_pid 	= $mem_data['path_pid'].'#'.$pid;
				}
				
			}
			/*获取存储的验证码*/
            if(count((new UserModel())->getInfo(['user_tel'=>$mobile])) > 0){
                return array('code'=>0,'message'=>'手机号码已经注册！');
            }
			$onerec=Db::table("ns_mobile_msgs")->where("mobile",$mobile)->order('id desc')->find();
			$sendtime=strtotime($onerec['send_time']);
			$difference=time()-$sendtime;
			if($code==$onerec['code'] && $difference<300)  //验证码5分钟内有效
			{  //可以增加一个前端的-2004 的功能
				$retval = $member->registerMember($user_name, $password, $email, $mobile, '', '', '', '', '',$pid, $path_pid);
				if($retval>0){  //期限以一年来计算
					Db::table("ns_member_account")->insert(["uid"=>$retval,"exc_point"=>100]);//赠送100通兑积分 account_type默认是1通兑积分
					Db::table("ns_give_point_records")->insert(["uid"=>$retval,"number"=>100,"text"=>'注册人获得赠送通兑积分',"create_time"=>date("Y-m-d H:i:s", time())]);
					if(!empty($pid)){
						Db::table("ns_member_account")->where(["uid"=>$pid])->setInc('exc_point',50);//拉人的上级赠送50通兑积分
						Db::table("ns_give_point_records")->insert(["uid"=>$pid,"number"=>50,"text"=>'邀请人获得赠送通兑积分+'.$retval,"create_time"=>date("Y-m-d H:i:s", time())]);
					}
				} 
				return AjaxReturn($retval);
			} else {
				return array('code'=>0,'message'=>'验证码无效');
			}
            /*获取存储的验证码结束*/
        } else {
            $config = new Config();
            $instanceid = 0;
            //判断是否开启邮箱注册
            $reg_config_info = $config->getRegisterAndVisit($instanceid);
            $reg_config = json_decode($reg_config_info["value"],true);
            if($reg_config["is_register"] != 1){
                $this->error("抱歉,商城暂未开放注册!",__URL__."/login/index");
            }
            if(strpos($reg_config['register_info'],"plain") === false && strpos($reg_config['register_info'],"mobile") === false ){
                $this->error("抱歉,商城暂未开放注册!",__URL__."/login/index");
            }
            $this->assign("reg_config",$reg_config);
            // 登录配置
            $web_config = new WebConfig();
            $loginConfig = $web_config->getLoginConfig();
            
            $loginCount = 0;
            if ($loginConfig['wchat_login_config']['is_use'] == 1) {
                $loginCount ++;
            }
            if ($loginConfig['qq_login_config']['is_use'] == 1) {
                $loginCount ++;
            }
            
            $code_config = $web_config->getLoginVerifyCodeConfig($this->instance_id);
            $this->assign('code_config', $code_config["value"]);
            
            $this->assign("loginCount", $loginCount);
            $this->assign("loginConfig", $loginConfig);
            return view($this->style . '/Login/register');
        }
    }
    // 判断手机号存在不
    public function mobile()
    {
        if (request()->isAjax()) {
            // 获取数据库中的用户列表
            $user_mobile = $_POST['mobile'];
            $exist = false;
            $user_list = $this->user->getMemberList();
            foreach ($user_list["data"] as $user_list2) {
                if ($user_list2["user_tel"] == $user_mobile) {
                    $exist = true;
                }
            }
            return $exist;
        }
    }
    
    
    /**
     * 判断邮箱是否存在
     */
    public function email(){
        if (request()->isAjax()) {
            // 获取数据库中的用户列表
            $user_email = $_POST['email'];
            $exist = false;
            $user_list = $this->user->getMemberList();
            foreach ($user_list["data"] as $user_list2) {
                if ($user_list2["user_email"] == $user_email) {
                    $exist = true;
                }
            }
            return $exist;
        }
    }

    /**
     * 注册手机号验证码验证
     * 任鹏强
     * 2017年6月17日16:26:46
     * 
     * @return multitype:number string
     */
    public function register_check_code()
    {
        $send_param = request()->post('send_param', '');
        // $mobile = request()->post('mobile', '');
        $param = session::get('mobileVerificationCode');
        
        if ($send_param == $param && $send_param != '') {
            $retval = [
                'code' => 0,
                'message' => "验证码一致"
            ];
        } else {
            $retval = [
                'code' => 1,
                'message' => "验证码不一致"
            ];
        }
        return $retval;
    }

    /**
     * 注册后登陆
     */
    public function register_login()
    {
        if (request()->isAjax()) {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            if (! empty($username)) {
                $res = $this->user->login($username, $password);
            } else {
                $res = $this->user->login($mobile, $password);
            }
            /* return AjaxReturn($res); */
            $_SESSION['order_tag'] = "";//清空订单
            if ($res['code'] == 1) {
                if (! empty($_SESSION['login_pre_url'])) {
                    $this->redirect($_SESSION['login_pre_url']);
                } else {
                    $this->redirect(__URL__."/member/index");
                }
            }
        }
    }

    /**
     * 制作推广二维码
     */
    function showUserQrcode()
    {
        $uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
        if ($uid == 0) {
            $this->error('无法获取到会员信息');
        }
        $instance_id = $this->instance_id;
        // 读取生成图片的位置配置
        $weixin = new Weixin();
        $data = $weixin->getWeixinQrcodeConfig($instance_id, $uid);
        $member_info = $this->user->getUserInfoByUid($uid);
        // 获取所在店铺信息
        $web = new WebSite();
        $shop_info = $web->getWebDetail();
        $shop_logo = $shop_info["logo"];
        
        // 查询并生成二维码
        $path = 'upload/qrcode/' . 'qrcode_' . $uid . '_' . $instance_id . '.png';
        
        if (! file_exists($path)) {
            $weixin = new Weixin();
            $url = $weixin->getUserWchatQrcode($uid, $instance_id);
            if ($url == WEIXIN_AUTH_ERROR) {
                exit();
            } else {
                getQRcode($url, 'upload/qrcode', "qrcode_" . $uid . '_' . $instance_id);
            }
        }
        // 定义中继二维码地址
        $thumb_qrcode = 'upload/qrcode/thumb_' . 'qrcode_' . $uid . '_' . $instance_id . '.png';
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
        $uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
        if ($uid == 0) {
            $this->error('无法获取到会员信息');
        }
        $instance_id = $this->instance_id;
        if ($instance_id == 0) {
            $url = __URL__. '/wap?source_uid=' . $uid;
        } else {
            $url = __URL__. '/wap/shop/index?shop_id=' . $instance_id . '&source_uid=' . $uid;
        }
        // 查询并生成二维码
        $path = 'upload/qrcode/' . 'shop_' . $uid . '_' . $instance_id . '.png';
        if (! file_exists($path)) {
            getQRcode($url, 'upload/qrcode', "shop_" . $uid . '_' . $instance_id);
        }
        
        // 定义中继二维码地址
        $thumb_qrcode = 'upload/qrcode/thumb_shop_' . 'qrcode_' . $uid . '_' . $instance_id . '.png';
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
        
        $web = new WebSite();
        $shop_info = $web->getWebDetail();
        $shop_logo = $shop_info["logo"];
        $shop_name = $shop_info["title"];
        $shop_phone = $shop_info["web_phone"];
        $live_store_address = $shop_info["web_address"];
        
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

    /**
     * 获取微信推广二维码
     */
    public function getWchatQrcode()
    {
        $uid = isset($_GET['source_uid']) ? $_GET['source_uid'] : 0;
        $this->assign('source_uid', $uid);
        if ($uid == 0) {
            $this->error('无法获取到会员信息');
        }
        $instance_id = 0;
        $this->assign("shop_id", $instance_id);
        $share_contents = $this->getShareContents($uid, 0, 'qrcode_my', '');
        $this->assign("share_contents", $share_contents);
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("signPackage", $ticket);
        return view($this->style . "/Login/myqrcode");
    }

    /**
     * 生成个人店铺二维码
     */
    public function getShopQrcode()
    {
        $uid = isset($_GET['source_uid']) ? $_GET['source_uid'] : 0;
        $this->assign('source_uid', $uid);
        if ($uid == 0) {
            $this->error('无法获取到会员信息');
        }
        $share_contents = $this->getShareContents($uid, 0, 'qrcode_shop', '');
        $weisite = new WebSite();
        $weisite_info = $weisite->getWebSiteInfo();
        $info["logo"] = $weisite_info["logo"];
        $info["shop_name"] = $weisite_info["title"];
        $info["phone"] = $weisite_info["web_phone"];
        $info["address"] = $weisite_info["web_address"];
        $this->assign("info", $info);
        // 分享
        $ticket = $this->getShareTicket();
        $this->assign("share_contents", $share_contents);
        $this->assign("signPackage", $ticket);
        return view($this->style . "/Login/shopqrcode");
    }

    /**
     * 获取分享相关信息
     * 首页、商品详情、推广二维码、店铺二维码
     *
     * @return multitype:string unknown
     */
    public function getShareContents($uid, $shop_id, $flag, $goods_id)
    {
        $this->uid = $uid;
        // 标识当前分享的类型[shop、goods、qrcode_shop、qrcode_my]
        $flag = isset($flag) ? $flag : "shop";
        $goods_id = isset($goods_id) ? $goods_id : "";
        
        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $this->logo; // 分享时，用到的logo，默认是平台logo
        $shop = new Shop();
        $config = $shop->getShopShareConfig($shop_id);
        
        // 当前用户名称
        $current_user = "";
        $user_info = null;
        if (empty($goods_id)) {
            switch ($flag) {
                case "shop":
                    if (! empty($this->uid)) {
                        
                        $user = new User();
                        $user_info = $user->getUserInfoByUid($this->uid);
                        $share_url = __URL__. '/wap/index?source_uid=' . $this->uid;
                        $current_user = "分享人：" . $user_info["nick_name"];
                    } else {
                        $share_url = __URL__ . '/wap/index';
                    }
                    break;
                case "qrcode_shop":
                    
                    $user = new User();
                    $user_info = $user->getUserInfoByUid($this->uid);
                    $share_url = __URL__ . '/wap/Login/getshopqrcode?source_uid=' . $this->uid;
                    $current_user = "分享人：" . $user_info["nick_name"];
                    break;
                case "qrcode_my":
                    
                    $user = new User();
                    $user_info = $user->getUserInfoByUid($this->uid);
                    $share_url = __URL__ . '/wap/Login/getWchatQrcode?source_uid=' . $this->uid;
                    $current_user = "分享人：" . $user_info["nick_name"];
                    break;
            }
        } else {
            if (! empty($this->uid)) {
                $user = new User();
                $user_info = $user->getUserInfoByUid($this->uid);
                $share_url = __URL__ . '/wap/Goods/goodsDetail?id=' . $goods_id . '&source_uid=' . $this->uid;
                $current_user = "分享人：" . $user_info["nick_name"];
            } else {
                $share_url = __URL__ . '/wap/Goods/goodsDetail?id=' . $goods_id;
            }
        }
        
        // 店铺分享
        if ($shop_id != 0) {
            $shop_info = $shop->getShopInfo($shop_id);
            $shop_name = $shop_info['shop_name'];
        } else {
            $weisite = new WebSite();
            $weisite_info = $weisite->getWebSiteInfo();
            $shop_name = $weisite_info["title"];
        }
        $share_content = array();
        switch ($flag) {
            case "shop":
                $share_content["share_title"] = $config["shop_param_1"] . $shop_name;
                $share_content["share_contents"] = $config["shop_param_2"] . "," . $config["shop_param_3"];
                $share_content['share_nick_name'] = $current_user;
                break;
            case "goods":
                // 商品分享
                $goods = new GoodsService();
                $goods_detail = $goods->getGoodsDetail($goods_id);
                $share_content["share_title"] = $goods_detail["goods_name"];
                $share_content["share_contents"] = $config["goods_param_1"] . "￥" . $goods_detail["price"] . ";" . $config["goods_param_2"];
                $share_content['share_nick_name'] = $current_user;
                if (count($goods_detail["img_list"]) > 0) {
                    $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $goods_detail["img_list"][0]["pic_cover_mid"]; // 用商品的第一个图片
                }
                break;
            case "qrcode_shop":
                // 二维码分享
                if (! empty($user_info)) {
                    $share_content["share_title"] = $shop_name . "二维码分享";
                    $share_content["share_contents"] = $config["qrcode_param_1"] . ";" . $config["qrcode_param_2"];
                    $share_content['share_nick_name'] = '分享人：' . $user_info["nick_name"];
                    if (! empty($user_info['user_headimg'])) {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $user_info['user_headimg'];
                    } else {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__TEMP__') . '/wap/' . NS_TEMPLATE . '/public/images/member_default.png';
                    }
                }
                break;
            case "qrcode_my":
                // 二维码分享
                if (! empty($user_info)) {
                    $share_content["share_title"] = $shop_name . "二维码分享";
                    $share_content["share_contents"] = $config["qrcode_param_1"] . ";" . $config["qrcode_param_2"];
                    $share_content['share_nick_name'] = '分享人：' . $user_info["nick_name"];
                    if (! empty($user_info['user_headimg'])) {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $user_info['user_headimg'];
                    } else {
                        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__TEMP__') . '/wap/' . NS_TEMPLATE . '/public/images/member_default.png';
                    }
                }
                break;
        }
        $share_content["share_url"] = $share_url;
        $share_content["share_img"] = $share_logo;
        return $share_content;
    }

    /**
     * 获取分享相关票据
     */
    public function getShareTicket()
    {
        // 获取票据
        if (isWeixin()) {
            // 针对单店版获取微信票据
            $config = new Config();
            $auth_info = $config->getInstanceWchatConfig(0);
            $wexin_auth = new WchatOauth();
            $signPackage['appId'] = $auth_info['value']['appid'];
            $signPackage['jsTimesTamp'] = time();
            $signPackage['jsNonceStr'] = $wexin_auth->get_nonce_str();
            $jsapi_ticket = $wexin_auth->jsapi_ticket();
            $signPackage['ticket'] = $jsapi_ticket;
            $url = request()->url(true);
            $Parameters = "jsapi_ticket=" . $signPackage['ticket'] . "&noncestr=" . $signPackage['jsNonceStr'] . "&timestamp=" . $signPackage['jsTimesTamp'] . "&url=" . $url;
            $signPackage['jsSignature'] = sha1($Parameters);
            return $signPackage;
        } else {
            $signPackage = array(
                'appId' => '',
                'jsTimesTamp' => '',
                'jsNonceStr' => '',
                'ticket' => '',
                'jsSignature' => ''
            );
            return $signPackage;
        }
    }

    /**
     * 用户锁定界面
     *
     * @return \think\response\View
     */
    public function userLock()
    {
        return view($this->style . "/Login/userLock");
    }

    /**
     * 检测手机号是否已经注册
     * 
     * @return Ambigous <number, \data\model\unknown>
     */
    public function checkMobileIsHas()
    {
        $mobile = request()->post('mobile', '');
        if (! empty($mobile)) {
            $count = $this->user->checkMobileIsHas($mobile);
        } else {
            $count = 0;
        }
        return $count;
    }

    /**
     * 发送注册短信验证码
     * 
     * @return boolean
     */

    public function sendSmsfindPasswordCode(){
        if($this->mobile() == false){
            return array(
                'code' => 1,
                'message' => '该手机号未注册'
            );
        }
        return $this->sendSmsCode();
    }

    public function sendSmsRegisterCode()
    {
        if($this->mobile() == true){
            return array(
                'code' => 1,
                'message' => '该手机号已注册'
            );
        }
        return $this->sendSmsCode();
    }
    public function sendSmsRegisterCodes()
    {
        return $this->sendSmsCode();
    }

        public function sendSmsCode()
    {
        $vertification = request()->post('vertification', '');
        if(!captcha_check($vertification)) {
            // 校验失败
            return array(
                'code' => 2,
                'message' => '动态码不正确，请重新输入'
            );
        }
        $mobile = request()->post('mobile', '');
        $code = rand(100000, 999999);
		/*更换短信*/
		$today=date('Y-m-d',time());
		$send_num=0;
        $param = session::set('mobileVerificationCode', $code);
        Session::set("codeMobile", $mobile);

        $result = Db::query("select count(*) from ns_mobile_msgs where send_time >= "."'".$today."'");
        $count  = $result['0']['count'];
        if($count >= 200){
            return array(
                'code' => 1,
                'message' => '您发送短信验证码的请求没有成功！'
            );
        }

        $sendrec=Db::table("ns_mobile_msgs")->where(["mobile"=>$mobile,"status"=>1])->select();
		foreach($sendrec as $key=>$val){
//			if(strpos($val['send_time'],$today) !== false){$send_num++;}
//			else {Db::table("ns_mobile_msgs")->where("id",$val['id'])->delete();}
            if(strpos($val['send_time'],$today) !== false){$send_num++;} //计算当天status为1即验证码类的发送数
//            $send_num++;
		}

		$mob_info='验证码：'.$code.'【积分呗】';
        if ($send_num <= 5) {
            $smsinfo = luosimaoSmsSend($mobile, $mob_info);
        }
		else {
			return array(
				'code' => 1,
				'message' => '短信每天发送超过三次不能再发送'
			);
		}
		/*更换短信结束*/
        if ($smsinfo == 'success') {
			$status=Db::table("ns_mobile_msgs")->insert(array("mobile"=>$mobile,"msg"=>"【积分呗】，验证码：".$code,"code"=>$code,"status"=>1));
			return array(
				'code' => 0,
				'message' => '短信发送成功'
			);
        } else {
			return array(
				'code' => 1,
				'message' => '短信发送失败'
			);
		}
    }
    /*
     * 忘记密码
     * 
     */
    public function forgetPassword()
    {
        return view($this->style . '/login/forgetPassword');
    }
    /**
     * 找回密码密码重置
     *
     * @return unknown[]
     */
    public function setNewPasswordByEmailOrmobile()
    {
        $userInfo = isset($_POST['userInfo']) ? $_POST['userInfo'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        if ($type == "email") {
            $codeEmail = Session::get("codeEmail");
            if ($userInfo != $codeEmail) {
                return $retval = array(
                    "code" => - 1,
                    "message" => "该邮箱与验证邮箱不符"
                );
            } else {
                $res = $this->user->updateUserPasswordByEmail($userInfo, $password);
                Session::delete("codeEmail");
            }
        } else 
            if ($type == "mobile") {
                $codeMobile = Session::get("codeMobile");
                if ($userInfo != $codeMobile) {
                    return $retval = array(
                        "code" => - 1,
                        "message" => "该手机号与验证手机不符"
                    );
                } else {
                    $res = $this->user->updateUserPasswordByMobile($userInfo, $password);
                    Session::delete("codeMobile");
                }
            }
        return AjaxReturn($res);
    }
}