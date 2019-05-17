<?php
/**
 * Pay.php
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

use data\extend\QRcode;
use data\service\Config;
use data\service\Member as MemberService;
use data\service\UnifyPay;
use data\service\WebSite;
use think\Controller;
use think\Log;
use think\Db;
use data\model\NsOrderModel;
use data\service\Order as OrderService;
use data\service\Order\Order as OrderBusiness;
\think\Loader::addNamespace('data', 'data/');

/**
 * 支付控制器
 *
 * @author Administrator
 *        
 */
class Pay extends Controller
{

    public $style;

    public $shop_config;

    public function __construct()
    {
        parent::__construct();
        $this->web_site = new WebSite();
        $web_info = $this->web_site->getWebSiteInfo();
        $this->assign("web_info", $web_info);
        $this->assign("shopname", $web_info['title']);
        $this->assign("title", $web_info['title']);
        $this->style = 'wap/default';
        $this->assign("style", $this->style);
        $Config = new Config();
        $seoConfig = $Config->getSeoConfig(0);
        // 购物设置
        $this->shop_config = $Config->getShopConfig(0);
        $this->assign("shop_config", $this->shop_config);
        $this->assign("seoconfig", $seoConfig);
        if ($web_info['wap_status'] == 2) {
            webClose($web_info['close_reason']);
        }
        // 获取会员昵称
        $member = new MemberService();
        $member_info = $member->getMemberDetail();
        $this->assign('member_info', $member_info);
    }

    /* 演示版本 */
    public function demoVersion(){
        return view($this->style . '/Pay/demoVersion');
    }
	/*异步获取支付状态信息*/
	public function ajaxgetPay(){
        $out_trade_no = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
        if (empty($out_trade_no)){
            return 0;
        }
        
        $pay = new UnifyPay();
        $pay_value = $pay->getPayInfo($out_trade_no);
		if($pay_value['pay_status']==1){return 1;}  //OrderPayment这个订单表状态为1时表示已经支付
		else {return 0;}
	}

    /**
     * 获取支付相关信息
     */
    public function getPayValue(){
		$out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
		if(!empty($_GET['goodid'] && !empty($out_trade_no))){
			$pay_body=Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->value('pay_body');
			if(strpos($pay_body,'shou_orderid-') == false){
				Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->update(['pay_body'=>$pay_body.';shou_orderid-'.$_GET['goodid']]);
			}
		}  
		/*2月首单包邮全免功能开始*/
		$orderService=new OrderService;
		$orderService->firstFree($out_trade_no);//此处满足条件会变更订单
		/*2月首单包邮全免功能结束*/
		/*新人专享开始*/
		if(!empty($_GET['hidden_exc'])){
			$noexc=$orderService->userAssemble($out_trade_no);//此处满足条件会变更订单
			$this->assign("noexc", $noexc);
		}
		/*新人专享结束*/
        $member = new MemberService();
        $member_info = $member->getMemberDetail();
        $payment_password = $member_info['user_info']['payment_password'];
        $this->assign("payment_password", $payment_password);
        
        if (empty($out_trade_no)) {
            $this->error("没有获取到支付信息");
        }

        $pay = new UnifyPay();
        $pay_config = $pay->getPayConfig();
        $this->assign("pay_config", $pay_config);
        $pay_value = $pay->getPayInfo($out_trade_no);
        $zero1 = date("Y-m-d H:i:s"); // 当前时间 ,注意H 是24小时 h是12小时

        if (empty($pay_value)){
            $this->error("订单主体信息已发生变动!", __URL__ . "/member/index");
        }
        if ($pay_value['pay_status'] == 1) {
            // 订单已经支付
            $this->error("订单已经支付或者订单价格为0.00，无需再次支付!");
        }

        $zero2 = $pay_value['create_time'];
        if (strtotime($zero1) > (strtotime($zero2) + ($this->shop_config['order_buy_close_time'] * 60))) {
            $this->error("订单已关闭");
        }

        $this->assign('pay_value', $pay_value);
        return view($this->style . '/Pay/getPayValue');
    }
	/**
     * 获取支付相关信息
     */
    public function getExcPay(){
		$out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
		if(!empty($_GET['goodid'] && !empty($out_trade_no))){
			$pay_body=Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->value('pay_body');
			if(strpos($pay_body,'shou_orderid-') == false){
				Db::table('ns_order_payment')->where('out_trade_no',$out_trade_no)->update(['pay_body'=>$pay_body.';shou_orderid-'.$_GET['goodid']]);
			}
		}  
		/*2月首单包邮全免功能开始*/
		$orderService=new OrderService;
		//$orderService->firstFree($out_trade_no);//此处满足条件会变更订单
		/*2月首单包邮全免功能结束*/
		/*新人专享开始*/
		if(!empty($_GET['hidden_exc'])){
			$noexc=$orderService->userAssemble($out_trade_no);//此处满足条件会变更订单
			$this->assign("noexc", $noexc);
		}
		$oneorder=Db::table('ns_order')->where('out_trade_no',$out_trade_no)->find();
		$onegoods=Db::table('ns_order_goods')->where('order_id',$oneorder['order_id'])->find();
		$zero_point_num=Db::table('ns_goods')->where('goods_id',$onegoods['goods_id'])->value('zero_point_num');
		$this->assign("pointNum",$zero_point_num*$onegoods['price']);
		/*新人专享结束*/
        $member = new MemberService();
        $member_info = $member->getMemberDetail();
        $payment_password = $member_info['user_info']['payment_password'];
        $this->assign("payment_password", $payment_password);
        
        if (empty($out_trade_no)) {
            $this->error("没有获取到支付信息");
        }

        $pay = new UnifyPay();
        $pay_config = $pay->getPayConfig();
        $this->assign("pay_config", $pay_config);
        $pay_value = $pay->getPayInfo($out_trade_no);
        $pay_value['pay_money'] = explode('.', $pay_value['pay_money']);
        $pay_value['pay_money'] = $pay_value['pay_money'][0];
        $zero1 = date("Y-m-d H:i:s"); // 当前时间 ,注意H 是24小时 h是12小时

        if (empty($pay_value)){
            $this->error("订单主体信息已发生变动!", __URL__ . "/member/index");
        }
        if ($pay_value['pay_status'] == 1) {
            // 订单已经支付
            $this->error("订单已经支付或者订单价格为0.00，无需再次支付!");
        }

        $zero2 = $pay_value['create_time'];
        if (strtotime($zero1) > (strtotime($zero2) + ($this->shop_config['order_buy_close_time'] * 60))) {
            $this->error("订单已关闭");
        }

        $this->assign('pay_value', $pay_value);
        return view($this->style . '/Pay/getExcPay');
    }

    /**
     * 支付完成后回调界面
     *
     * status 1 成功
     *
     * @return \think\response\View
     */
    public function payCallback(){
        $out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : ""; // 流水号
        $msg = isset($_GET['msg']) ? $_GET['msg'] : ""; // 测试，-1：在其他浏览器中打开，1：成功，2：失败
        $this->assign("status", $msg);
        $this->assign("out_trade_no", $out_trade_no);
		$paybody=Db::table('ns_order_payment')->where("out_trade_no",$out_trade_no)->value('pay_body');
		$arr=explode('-',$paybody);
		if(empty($arr[count($arr)-1])){
			$goodsid='';
		} else {$goodsid=$arr[count($arr)-1];}
		$this->assign("goodsid", $goodsid);
        if(request()->isMobile()) {
            return view($this->style . "/Pay/payCallback");
        } else {
            return view($this->style . "/Pay/payCallbackPc");
        }
    }

    /**
     * 订单微信支付
     */
    public function wchatPay()
    {
        $out_trade_no = isset($_GET['no']) ? $_GET['no'] : '';
        if (empty($out_trade_no)) {
            $this->error("没有获取到支付信息");
        }
        
        $red_url = __URL__ . '/wap/Pay/wchatUrlBack';
        $pay = new UnifyPay();
        if (! isWeixin()) {
            // 扫码支付
            $res = $pay->wchatPay($out_trade_no, 'NATIVE', $red_url);
			if($res['result_code']=='FAIL'){$this->error("订单已经支付或有错误");}
            if ($res["return_code"] == "SUCCESS") {
                $code_url = $res['code_url'];
            } else {
                $code_url = "生成支付二维码失败!";
            }
            $path = getQRcode($code_url, "upload/qrcode/pay", $out_trade_no);
            $this->assign("path", __ROOT__ . '/' . $path);
            $pay_value = $pay->getPayInfo($out_trade_no);
			if (request()->isMobile()) {$loca_site="/wap/order/myorderlist";}
			else {$loca_site="/member/orderlist";}
			$this->assign('loca_site', $loca_site);
            $this->assign('pay_value', $pay_value);
            return view($this->style . "/Pay/pcWeChatPay");
        } else {
            // jsapi支付
            $res = $pay->wchatPay($out_trade_no, 'JSAPI', $red_url);
            $retval = $pay->getWxJsApi($res);
            $this->assign("out_trade_no", $out_trade_no);
            $this->assign('jsApiParameters', $retval);
            return view($this->style . '/Pay/weixinPay');
        }
    }

    /**
     * 微信支付异步回调（只有异步回调对订单进行处理）
     */
    public function wchatUrlBack()
    {
        $postStr = file_get_contents('php://input');
        if (! empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $pay = new UnifyPay();
            $check_sign = $pay->checkSign($postObj, $postObj->sign);
            if ($postObj->result_code == 'SUCCESS' && $check_sign == 1) {
                $retval = $pay->onlinePay($postObj->out_trade_no, 1);
                $xml = "<xml>
                    <return_code><![CDATA[SUCCESS]]></return_code>
                    <return_msg><![CDATA[支付成功]]></return_msg>
                </xml>";
                echo $xml;
            } else {
                $xml = "<xml>
                    <return_code><![CDATA[FAIL]]></return_code>
                    <return_msg><![CDATA[支付失败]]></return_msg>
                </xml>";
                echo $xml;
            }
        }
    }

    /**
     * 微信支付同步回调（不对订单处理）
     */
    public function wchatPayResult()
    {
        $out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
        $msg = isset($_GET['msg']) ? $_GET['msg'] : '';
        $this->assign("status", $msg);
        $this->assign("out_trade_no", $out_trade_no);
		$paybody=Db::table('ns_order_payment')->where("out_trade_no",$out_trade_no)->value('pay_body');
		$goodsid='';
		$arr=explode('-',$paybody);
		if(count($arr)==2){
			if(empty($arr[count($arr)-1])){
				$goodsid='';
			} else {$goodsid=$arr[count($arr)-1];}
		}  
		$this->assign("goodsid", $goodsid);
		$pay = new UnifyPay();
		$pay_info = $pay->getPayInfo($out_trade_no);
		$this->assign("type",$pay_info['type']);//4是充值
        if(request()->isMobile()) {
            return view($this->style . "/Pay/payCallback");
        } else {
            return view($this->style . "/Pay/payCallbackPc");
        }
    }

    /**
     * 支付宝支付
     */
    public function aliPay()
    {
        $out_trade_no = isset($_GET['no']) ? $_GET['no'] : '';
        if (empty($out_trade_no)) {
            $this->error("没有获取到支付信息");
        }
        if (! isWeixin()) {
            $notify_url = __URL__ . '/wap/Pay/aliUrlBack';
            $return_url = __URL__ . '/wap/Pay/aliPayReturn';
            $show_url = __URL__ . '/wap/Pay/aliUrlBack';
            $pay = new UnifyPay();
            $res = $pay->aliPay($out_trade_no, $notify_url, $return_url, $show_url);
            echo "<script>window.location.href='" . $res . "'</script>";
        } else {
            // echo "点击右上方在浏览器中打开";
            $this->assign("status", - 1);
            if (request()->isMobile()) {
                return view($this->style . "/Pay/payCallback");
            } else {
                return view($this->style . "/Pay/payCallbackPc");
            }
        }
    }

    /**
     * 支付宝支付异步回调
     */
    public function aliUrlBack()
    {
        $pay = new UnifyPay();
        $verify_result = $pay->getVerifyResult('notify');
        if ($verify_result) { // 验证成功
            $out_trade_no = $_POST['out_trade_no'];
            // 支付宝交易号
            $trade_no = $_POST['trade_no'];
            
            // 交易状态
            $trade_status = $_POST['trade_status'];
            
            if ($trade_status == 'TRADE_FINISHED') {
                // 判断该笔订单是否在商户网站中已经做过处理
                // 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                // 如果有做过处理，不执行商户的业务程序
                // 注意：
                // 退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                
                // 调试用，写文本函数记录程序运行情况是否正常
                // logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                $retval = $pay->onlinePay($out_trade_no, 2);
                // $res = $order->orderOnLinePay($out_trade_no, 2);
            } else 
                if ($trade_status == 'TRADE_SUCCESS') {
                    // 判断该笔订单是否在商户网站中已经做过处理
                    // 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                    // 如果有做过处理，不执行商户的业务程序
                    
                    // 注意：
                    // 付款完成后，支付宝系统发送该交易状态通知
                    $retval = $pay->onlinePay($out_trade_no, 2);
                    // $res = $order->orderOnLinePay($out_trade_no, 2);
                    // 调试用，写文本函数记录程序运行情况是否正常
                    // logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
                }
            
            // ——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            
            echo "success"; // 请不要修改或删除
                                
            // $this->assign("status", 1);
                                // $this->assign("out_trade_no", $out_trade_no);
                                // return view($this->style . "/Pay/payCallback");
                                
            // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            // 验证失败
            echo "fail";
            
            // $this->assign("status", 2);
            // $this->assign("out_trade_no", $out_trade_no);
            // return view($this->style . "/Pay/payCallback");
            // 调试用，写文本函数记录程序运行情况是否正常
        } // logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }

    /**
     * 支付宝支付同步会调（页面）（不对订单进行处理）
     */
    public function aliPayReturn()
    {
        $out_trade_no = $_GET['out_trade_no'];
        
        $pay = new UnifyPay();
        $verify_result = $pay->getVerifyResult('return');
        if ($verify_result) {
            $trade_no = $_GET['trade_no'];
            $trade_status = $_GET['trade_status'];
            
            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                // return view($this->style . '/Pay/pay_success');
                // echo "<h1>支付成功</h1>";
                $this->assign("status", 1);
                $this->assign("out_trade_no", $out_trade_no);
                if (request()->isMobile()) {
                    return view($this->style . "/Pay/payCallback");
                } else {
                    return view($this->style . "/Pay/payCallbackPc");
                }
            } else {
                echo "trade_status=" . $_GET['trade_status'];
            }
            $this->assign("orderNumber", $_GET['out_trade_no']);
            $this->assign("msg", 1);
        } else {
            $this->assign("orderNumber", $_GET['out_trade_no']);
            $this->assign("msg", 0);
            // echo "<h1>支付失败</h1>";
            $this->assign("status", 2);
            $this->assign("out_trade_no", $out_trade_no);
            if (request()->isMobile()) {
                return view($this->style . "/Pay/payCallback");
            } else {
                return view($this->style . "/Pay/payCallbackPc");
            }
            // echo "验证失败";
        }
    }
}