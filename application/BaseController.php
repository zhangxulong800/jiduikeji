<?php
/**
 * BaseController.php
 * 积分呗系统 - 团队十年电商经验汇集巨献!
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace app;

use data\extend\WchatOauth;
use data\service\Goods as GoodsService;
use data\service\Member as Member;
use data\service\Address;
use data\service\Config;
use data\service\User;
use data\service\WebSite as WebSite;
use think\Controller;
use think\Session;
use data\service\GoodsCategory;
use data\service\Platform;
use data\service\Shop as ShopService;
//use think\View;
use think\Db;
use think\Cache;

\think\Loader::addNamespace('data', 'data/');

class BaseController extends Controller
{
	private $DEFAULT_STYLE = "shop/default";
	// 默认模板样式
	private $BLUE_STYLE = "shop/blue";
	// 蓝色清爽版样式
	
	// 验证码配置
	public $login_verify_code;
	//pc端变量结束
	//API变量
	public $apiReq;    //请求总参数
	public $tokenState;//口令状态
    public function __construct()
    {
		parent::__construct();
		$this->apiReq = $_REQUEST;
		if(!empty($_REQUEST['token'])){  //token要加上uid67_，要缓存；预先存入数据表，并在此处获取到用户信息。公共的不需要口令时可提交555（无）
			define("ERROR_CODE", '-50');
			define("ERROR_MESSAGE", '失败');
			$tok_arr=explode('_',$_REQUEST['token']);
			if(count($tok_arr)>2){  //标准口令数组元素为3
				$tokenCac= Cache::get($tok_arr[0]);
				if(empty($tokenCac)){ //可能出现清除缓存的情况
					$tokenCac=Db::table('ns_member_account')->where('uid',substr($tok_arr[0],3))->value('token');
					Cache::set($tok_arr[0],$tokenCac);
				}
				if($tokenCac==$_REQUEST['token']){
					$this->tokenState =1;
					$this->uid =substr($tok_arr[0],3);
				} else {
					$this->tokenState =4;  //口令不正确
				} 
			} else {$this->tokenState =0;}
		}
		if(request()->isMobile() && time()<1560906421){  //Will processe
			if(stripos($_SERVER["QUERY_STRING"],'/wap')===false){
				$this->redirect(__URL__ . "/wap"); 
			}
			Session::delete('default_client');
			$this->app_login_name = !empty($_GET['login_name'])?$_GET['login_name']:"";
			$this->app_login_password = !empty($_GET['login_password'])?$_GET['login_password']:"";
			if(!empty($this->app_login_name) && !empty($this->app_login_password)){
				$this->user = new Member();
				$retval = $this->user->login($this->app_login_name,$this->app_login_password);
			}
		   
			$this->initInfo();
		} else {
			if(empty($_REQUEST['token'])){  //余下为PC端。token提交为api标志
				$default_client = request()->get("default_client", "");
				if ($default_client == "shop") {
					// 当前切换到了PC端
				} elseif (request()->isMobile()) {
					$this->redirect(__URL__ . "/wap");
					exit();
				}
				
				//当切换到PC端时，隐藏右下角返回手机端按钮
				if (! request()->isMobile() && $default_client == "shop") {
					$default_client = "";
				}
				
				// 请求端（PC端、手机端）
				// getShopCache();//开启缓存
				$this->init();
				$this->assign("default_client", $default_client);
				$get_array = request()->get();
			}
		}
    }
	/**
     * 功能说明：action基类
     */
    public function init()
    {
        $this->user = new Member();
        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        
        if ($web_info['web_status'] == 2) {
            webClose($web_info['close_reason']);
        }
        $this->uid = $this->user->getSessionUid();
        $this->instance_id = $this->user->getSessionInstanceId();
        $this->shop_name = $this->user->getInstanceName();
        $this->assign("uid", $this->uid);
        $this->assign("title", $web_info['title']);
        $this->assign("web_info", $web_info);
        $this->assign("title_before", '');
        if ($web_info['style_id'] == 1) {
            $this->style = $this->DEFAULT_STYLE . "/";
            $this->assign("style", $this->DEFAULT_STYLE);
        } else {
            $this->style = $this->BLUE_STYLE . "/";
            $this->assign("style", $this->BLUE_STYLE);
        }
        
        if (! request()->isAjax()) {
            $Config = new Config();
            $seoconfig = $Config->getSeoConfig($this->instance_id);
            $this->assign("seoconfig", $seoconfig);
            // 是否开启验证码
            $web_config = new Config();
            $this->login_verify_code = $web_config->getLoginVerifyCodeConfig($this->instance_id);
            $this->assign("login_verify_code", $this->login_verify_code["value"]);
            
            $qq_info = $web_config->getQQConfig($this->instance_id);
            $Wchat_info = $web_config->getWchatConfig($this->instance_id);
            $this->assign("qq_info", $qq_info);
            $this->assign("Wchat_info", $Wchat_info);
            $keyword = isset($_GET["keyword"]) ? $_GET["keyword"] : "";
            $this->assign("keyword", $keyword);
            /* 商品分类查询 */
            $goodsCategory = new GoodsCategory();
            $goods_category_one = $goodsCategory->getGoodsCategoryList('', '', [
                'level' => 1,
                'is_visible' => 1
            ], 'sort');
            
            // 查询一级分类下的对应二级分类个数
            foreach ($goods_category_one['data'] as $k => $v) {
                $goodsCategory = new GoodsCategory();
                $goods_category_two_list = $goodsCategory->getGoodsCategoryListByParentId($v['category_id']);
                $goods_category_one['data'][$k]['count'] = count($goods_category_two_list);
                // var_dump($goods_category_one['data']);
            }
            $this->assign('goods_category_one', $goods_category_one['data']); // 商品分类一级
                                                                              // var_dump($goods_category_one['data']);
            $goodsCategory = new GoodsCategory();
            $goods_category_two = $goodsCategory->getGoodsCategoryList('', '', [
                'level' => 2,
                'is_visible' => 1
            ], 'sort');
            
            // 查询二级分类下的对应三级分类个数
            foreach ($goods_category_two['data'] as $k => $v) {
                $goodsCategory = new GoodsCategory();
                $goods_category_three_list = $goodsCategory->getGoodsCategoryListByParentId($v['category_id']);
                $goods_category_two['data'][$k]['count'] = count($goods_category_three_list);
                // var_dump($goods_category_one['data']);
            }
            
            $this->assign('goods_category_two', $goods_category_two['data']); // 商品分类二级
            
            $goodsCategory = new GoodsCategory();
            $goods_category_three = $goodsCategory->getGoodsCategoryList('', '', [
                'level' => 3,
                'is_visible' => 1
            ], 'sort');
            $this->assign('goods_category_three', $goods_category_three['data']); // 商品分类三级
            
            $this->getCms(); // 底部文章分类列表
            $this->assign("platform_shopname", $this->shop_name);
            
            // 导航
            $nav = new ShopService();
            $navigation_list = $nav->ShopNavigationList(1, 0, [
                'type' => 1
            ], 'sort');
            $this->assign("navigation_list", $navigation_list["data"]);
            $this->getHotkeys(); // 热搜关键词
            
            $this->getPageUrl(); // 分页url拼接
            
            $this->assign('page_num', 5); // 分页显示的页码个数 注：误删不然所有分页都报错必须为奇数
            
            $this->assign('is_head_goods_nav', 0); // 商品分类是否显示样式
        }
    }
    public function initInfo()
    {
        $this->user = new Member();
        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        if($web_info['wap_status']  == 2)
        {
            webClose($web_info['close_reason']);
        }
        $this->uid = $this->user->getSessionUid();
        $this->instance_id = $this->user->getSessionInstanceId();
      
        $this->shop_name = $this->user->getInstanceName();
        $this->logo = $web_info['logo'];
        $this->style = 'wap/default/';
        if(!request()->isAjax())
        {
            if (empty($this->uid)) {
                $this->wchatLogin();
            }
            $this->currentShop();
            $this->assign("uid", $this->uid);
            $this->assign("title", $web_info['title']);
            $this->assign("style", 'wap/default');
            $this->assign("platform_shopname", $this->shop_name); // 平台店铺名称
            $Config = new Config();
            $seoConfig = $Config->getSeoConfig($this->instance_id);
            $this->assign("seoconfig", $seoConfig);
        }
       
    }
	/**
     * 获取导航
     */
    public function getNavigation()
    {
        $nav = new ShopService();
        $list = $nav->ShopNavigationList(1, 0, '', 'sort');
        return $list;
    }
	public function _empty($name)
    {}
    /**
     * 当前操作店铺的一些必须值
     */
    public function currentShop()
    {
        $this->shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : '0';
        $assign_list = array();
        if (empty($this->shop_id)) {
            $assign_list['extend_name'] = 'wap/default/base';
            $assign_list['current_shop_name'] = '我的';
        } else {
            $shop = new ShopService();
            $shop_info = $shop->getShopInfo($this->shop_id, 'shop_name');
            $this->shop_name = $shop_info['shop_name'];
            $assign_list['current_shop_name'] = $shop_info['shop_name'];
            $assign_list['extend_name'] = 'wap/default/Shop/shopBase';
        }
        $assign_list['shop_id'] = $this->shop_id;
        foreach ($assign_list as $key => $item) {
            $this->assign($key, $item);
        }
    }

    /**
     * 检测微信浏览器并且自动登录
     */
    public function wchatLogin()
    {
        
        // 微信浏览器自动登录
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        
            if (empty($_SESSION['request_url'])) {
                $_SESSION['request_url'] = request()->url(true);
            }
            $config = new Config();
            $wchat_config = $config->getInstanceWchatConfig(0);
            if(empty($wchat_config['value']['appid']))
            {
                return false;
            }
            $wchat_oauth = new WchatOauth();
            $domain_name = \think\Request::instance()->domain();
            if (! empty($_COOKIE[$domain_name . "member_access_token"])) {
                $token = json_decode($_COOKIE[$domain_name . "member_access_token"], true);
            } else {
                $token = $wchat_oauth->get_member_access_token();
                if (! empty($token['access_token'])) {
                    setcookie($domain_name . "member_access_token", json_encode($token));
                }
            }
            if (! empty($token['openid'])) {
                if (! empty($token['unionid'])) {
                    $wx_unionid = $token['unionid'];
                    $retval = $this->user->wchatUnionLogin($wx_unionid);
                    if ($retval == 1) {
                        $this->user->modifyUserWxhatLogin($token['openid'], $wx_unionid);
                    } elseif($retval == USER_LOCK)
                    {
                        $this->redirect(__URL__."/login/userlock");
                    }else{
                        $retval = $this->user->wchatLogin($token['openid']);
                        if ($retval == USER_NBUND) {
                            $info = $wchat_oauth->get_oauth_member_info($token);
                            
                            $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid,'','');
                        }elseif($retval == USER_LOCK)
                        {
                            //锁定跳转
                            $this->redirect(__URL__."/login/userlock");
                        }
                    }
                } else {
                    $wx_unionid = '';
                    $retval = $this->user->wchatLogin($token['openid']);
                    if ($retval == USER_NBUND) {
                        $info = $wchat_oauth->get_oauth_member_info($token);
                        
                        $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid,'','');
                    }elseif($retval == USER_LOCK)
                    {
                        //锁定跳转
                        $this->redirect(__URL__."/login/userlock");
                    }
                }
                echo "<script language=JavaScript> window.location.href='" . $_SESSION['request_url'] . "'</script>";
                exit();
            }
        }
    }

    /**
     * 获取分享相关信息
     * 首页、商品详情、推广二维码、店铺二维码
     *
     * @return multitype:string unknown
     */
    public function getShareContents()
    {
        $shop_id =  isset($_POST["shop_id"]) ? $_POST["shop_id"] : "0";
        // 标识当前分享的类型[shop、goods、qrcode_shop、qrcode_my]
        $flag = isset($_POST["flag"]) ? $_POST["flag"] : "shop";
        $goods_id = isset($_POST["goods_id"]) ? $_POST["goods_id"] : "";
        
        $share_logo = 'http://' . $_SERVER['HTTP_HOST'] . config('view_replace_str.__UPLOAD__') . '/' . $this->logo; // 分享时，用到的logo，默认是平台logo
        $shop = new ShopService();
        $config = $shop->getShopShareConfig(0);
        
        // 当前用户名称
        $current_user = "";
        $user_info = null;
        if (empty($goods_id)) {
            switch ($flag) {
                case "shop":
                 
                    if (! empty($this->uid)) {
                        
                        $user = new User();
                        $user_info = $user->getUserInfoByUid($this->uid);
                        if(!empty($shop_id))
                        {
                            $share_url = __URL__. '/wap/shop/index?shop_id='.$shop_id.'&source_uid=' . $this->uid;
                         
                        }else{
                            $share_url = __URL__. '/wap/index?source_uid=' . $this->uid;
                        }
                      
                        $current_user = "分享人：" . $user_info["nick_name"];
                    } else {
                        if(!empty($shop_id))
                        {
                            $share_url = __URL__. '/wap/shop/index?shop_id='.$shop_id;
                             
                        }else{
                              $share_url = __URL__. '/wap/index';
                        }
                      
                    }
                    break;
                case "qrcode_shop":
                    
                    $user = new User();
                    $user_info = $user->getUserInfoByUid($this->uid);
                    $share_url = __URL__. '/wap/Login/getshopqrcode?source_uid=' . $this->uid;
                    $current_user = "分享人：" . $user_info["nick_name"];
                    break;
                case "qrcode_my":
                    
                    $user = new User();
                    $user_info = $user->getUserInfoByUid($this->uid);
                    $share_url = __URL__. '/wap/Login/getWchatQrcode?source_uid=' . $this->uid;
                    $current_user = "分享人：" . $user_info["nick_name"];
                    break;
            }
        } else {
            if (! empty($this->uid)) {
                $user = new User();
                $user_info = $user->getUserInfoByUid($this->uid);
                $share_url = __URL__. '/wap/Goods/goodsDetail?id=' . $goods_id . '&source_uid=' . $this->uid;
                $current_user = "分享人：" . $user_info["nick_name"];
            } else {
                $share_url = __URL__. '/wap/Goods/goodsDetail?id=' . $goods_id;
            }
        }
        
        // 店铺分享
        $shop_name = $this->shop_name;
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
        $config = new Config();
        $auth_info = $config->getInstanceWchatConfig($this->instance_id);
        // 获取票据
        if (isWeixin() && !empty($auth_info['value']['appid'])) {
            // 针对单店版获取微信票据
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
     * 获取省列表
     */
    public function getProvince()
    {
        $address = new Address();
        $province_list = $address->getProvinceList();
        return $province_list;
    }

    /**
     * 获取城市列表
     *
     * @return Ambigous <multitype:\think\static , \think\false, \think\Collection, \think\db\false, PDOStatement, string, \PDOStatement, \think\db\mixed, boolean, unknown, \think\mixed, multitype:, array>
     */
    public function getCity()
    {
        $address = new Address();
        $province_id = isset($_POST['province_id']) ? $_POST['province_id'] : 0;
        $city_list = $address->getCityList($province_id);
        return $city_list;
    }

    /**
     * 获取区域地址
     */
    public function getDistrict()
    {
        $address = new Address();
        $city_id = isset($_POST['city_id']) ? $_POST['city_id'] : 0;
        $district_list = $address->getDistrictList($city_id);
        return $district_list;
    }
	/**
     * 拼接共用的分页中的url
     */
    public function getPageUrl()
    {
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : ""; // 地址
                                                                                // $path_info = substr($path_info, 6, strlen($path_info));
        $path_info = substr($path_info, 1);
        $get_array = request()->get();
        $query_string = '';
        if (array_key_exists('page', $get_array)) {
            $tag = '&';
        } else {
            if (! empty($get_array)) {
                $tag = '&';
            } else
                $tag = '?';
        }
        foreach ($get_array as $k => $v) {
            if ($k != 'page') {
                $query_string .= $tag . $k . '=' . $v;
            }
        }
        $this->assign('path_info', $path_info);
        $this->assign('query_string', $query_string);
    }
	/**
     * 底部信息
     */
    public function getCms()
    {
        $platform = new Platform();
        $platform_help_class = $platform->getPlatformHelpClassList(1, 5, '', 'sort');
        $this->assign('platform_help_class', $platform_help_class['data']); // 帮助中心分类列表
        
        $platform_help_Document = $platform->getPlatformHelpDocumentList(1, 0, '', 'sort');
        $this->assign('platform_help_document', $platform_help_Document['data']); // 帮助中心列表
    }
	/**
     * 热搜关键词
     */
    public function getHotkeys()
    {
        $config = new Config();
        $hot_keys = $config->getHotsearchConfig($this->instance_id);
        $this->assign("hot_keys", $hot_keys);
        $default_keywords = $config->getDefaultSearchConfig($this->instance_id);
        $this->assign('default_keywords', $default_keywords);
    }
	/**
     * 返回信息
     * @return \think\response\Json
     */
    public function outMessage($result){
		if(empty($result['code'])){
			$api_result['code'] = 0;
			$api_result['message']="success";
			$api_result['data'] = $result;
		} else {
			$api_result['code'] = $result['code'];
			$api_result['message']=$result['message'];
			$api_result['data'] = [];
		}
        return json($api_result);
    }
}