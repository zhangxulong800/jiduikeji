<?php
use think\Route;
use think\Cookie;
use think\Request;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//检测后台系统模块
     if(ADMIN_MODULE != 'admin')
    {
        Route::group(ADMIN_MODULE,function(){
            Route::rule(':controller/:action','admin/:controller/:action');
            Route::rule('','admin/index/index');
          
        });
		Route::group('admin',function(){
			Route::rule(':controller/:action','platfrom/:controller/:action');
			Route::rule('','platform/index/index');
		
		});
        
    }
//检测平台端后台模块
    if(PLATFORM_MODULE != 'platform')
    {
        Route::group(PLATFORM_MODULE,function(){
            Route::rule(':controller/:action','platform/:controller/:action');
            Route::rule('','admin/index/index');
    
        });
        Route::group('platform',function(){
            Route::rule(':controller/:action','');
            Route::rule('','platform/index/index');
    
        });
    
    }
    //检测开启路由规则配置
    
    function getRouteConfig($type)
    {
        $route_config = array(
            //商品详情
            'GOODS'          =>  1,
            //商品列表
            'GOODSLIST'      =>  1,
            //品牌列表
            'BEAND'          =>  1,
            //会员中心
            'MEMBER'         =>  1
             
        );
        return $route_config[$type];
    }
    //检测浏览器类型以及显示方式(电脑端、手机端)
    function getShowModule(){
    
        $default_client = Cookie::get('default_client');
        if(!empty($default_client)){
            $default_client = Cookie::get('default_client');
        }else{
            if(Request::instance()->get('default_client') == 'shop'){
                $default_client = 'shop';
            }else{
                $default_client = 'wap';
            }
        }
        $is_mobile = Request::instance()->isMobile();
    
        if($is_mobile)
        {
            if($default_client == 'wap')
            {
                return 'wap';
            }else{
                return 'shop';
            }
        }else{
            if($default_client == 'wap')
            {
                return 'wap';
            }else{
                return 'shop';
            }
        }
    }
    $show_module = getShowModule();
    //pc端开启路由去除shop
    return [
        //pc端相关
        '[goods]'     => [':action' => ['shop/goods/:action'],],
        '[list]'     => ['/'  => ['shop/goods/goodslist'],],
        '[index]'     => [':action' => ['shop/index/:action'],'/' => ['shop/index/index'],], 
        '[helpcenter]' => [':action' => ['shop/helpcenter/:action'],'/' => ['shop/helpcenter/index'],],
        '[login]'     => [':action'  => ['shop/login/:action'],'/' => ['shop/login/index'],],
        '[member]'     => [':action'  => ['shop/member/:action'],'/' => ['shop/member/index'],],
        '[components]' => [':action'  => ['shop/components/:action'],'/' => ['shop/components/index'],],
        '[helpcenter]' => [':action'  => ['shop/helpcenter/:action'],'/' => ['shop/helpcenter/index'],],
        '[order]'    => [ ':action'  => ['shop/order/:action'], '/'  => ['shop/order/index'],],
        '[topic]'    => [':action'  => ['shop/topic/:action'],'/' => ['shop/topic/index'],],
        '[cms]'     => [':action'  => ['shop/cms/:action'],'/' => ['shop/cms/index'],],
        '[shop]'    => [':action'=> ['shop/shop/:action'],'/' =>['shop/shop/index'],],
		'[pcpay]'    => [':action'=> ['shop/pcpay/:action'],],
    ];

//检测伪静态启用
  /*   if(getRouteConfig('GOODS'))
    {
        /* Route::group('goods_:goodsid',[
            '' => ['shop/goods/goodsinfo', ['method' => 'get'], ['goodsid' => '\d+']],
            ]); 
       // Route::get('goods/:goodsid','shop/goods/goodsinfo');
        Route::any('goods-:goodsid','shop/goods/goodsinfo/hoods',['method'=>'get']);
 
         
     
    } */
/* 
Route::rule([
	'admin$'=>'admin/manage/login',
	'admin/:action/[:id]'=>'admin/manage/:action',
	'member$'=>'index/member/index',
	'member/:action/[:name]/[:id]'=>'index/member/:action',
	':action/[:name]/[:id]'=>'index/index/:action'
	]);
 */

