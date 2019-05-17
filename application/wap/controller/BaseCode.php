<?php
class basecode{

    public function __construct()
    {
        Session::delete('default_client');
        $this->app_login_name = !empty($_GET['login_name'])?$_GET['login_name']:"";
        $this->app_login_password = !empty($_GET['login_password'])?$_GET['login_password']:"";
        if(!empty($this->app_login_name) && !empty($this->app_login_password)){
            $this->user = new Member();
            $retval = $this->user->login($this->app_login_name,$this->app_login_password);
        }
       // $cookie = request()->cookie('default_client', '');
       
        // getWapCache();//开启缓存
        parent::__construct();
        $this->initInfo();
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
            $shop = new Shop();
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
                            
                            $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);
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
                        
                        $result = $this->user->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);
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
        $shop = new Shop();
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
}
