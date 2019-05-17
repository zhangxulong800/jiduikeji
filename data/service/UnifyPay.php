<?php
/**
 * UnifyPay.php
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
namespace data\service;

/**
 * 统一支付接口服务层
 */
use data\service\BaseService as BaseService;
use data\api\IUnifyPay;
use data\model\NsOrderPaymentModel;
use data\service\Pay\WeiXinPay;
use data\service\Pay\AliPay;
use data\service\Config;
use app\wap\controller\Assistant;
use data\service\niubusiness\NbsBusinessAssistant;
use think\Log;
use think\Cache;

class UnifyPay extends BaseService implements IUnifyPay
{

    function __construct()
    {
        parent::__construct();
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IUnifyPay::createOutTradeNo()
     */
     public function createOutTradeNo()
    {
        $cache = Cache::get("myshop");
		$time=time();
        if(empty($cache))
        {
			$num = 0;
            Cache::set("myshop",$time."_".$num);
        } else{
			$arr=explode("_",$cache);
			if($arr[0]==$time){
				$num = $arr[1]+1;
				Cache::set("myshop",$time."_".$num);
			} else {
				$num = 0;
				Cache::set("myshop",$time."_".$num);
			}
        }
        $no = $time.rand(10000,99999).$num;
        return $no;
    }
    /**
     * 获取支付配置(non-PHPdoc)
     * @see \data\api\IUnifyPay::getPayConfig()
     */
    public function getPayConfig()
    {
       
        $instance_id = 0;
        $config = new Config();
        $wchat_pay = $config->getWpayConfig($instance_id);
        $ali_pay = $config->getAlipayConfig($instance_id);
        $data_config = array(
            'wchat_pay_config' => $wchat_pay,
            'ali_pay_config'   => $ali_pay
        );
        return $data_config;
    }
    /**
     * 创建待支付单据
     * @param unknown $pay_no
     * @param unknown $pay_body
     * @param unknown $pay_detail
     * @param unknown $pay_money
     * @param unknown $type  订单类型  1. 商城订单  2.
     * @param unknown $pay_money
     */
    public function createPayment($shop_id, $out_trade_no, $pay_body, $pay_detail, $pay_money, $type, $type_alis_id)
    {
        $pay = new NsOrderPaymentModel();
        $data = array(
            'shop_id'       => $shop_id,
            'out_trade_no'  => $out_trade_no,
            'type'          => $type,
            'type_alis_id'  => $type_alis_id,
            'pay_body'      => $pay_body,
            'pay_detail'    => $pay_detail,
            'pay_money'     => $pay_money,
            'create_time'   => date("Y-m-d H:i:s", time())
        );
        if($pay_money <= 0)
        {
            $data['pay_status'] = 1;
			$data['pay_type'] = 5;
			$data['pay_time'] = date("Y-m-d H:i:s", time());
        }
        $res = $pay->save($data);
        return $res;
    }

    /**
     * (non-PHPdoc)
     * @see \data\api\IUnifyPay::updatePayment()
     */
    public function updatePayment($out_trade_no,$shop_id, $pay_body, $pay_detail, $pay_money, $type, $type_alis_id)
    {
        $pay = new NsOrderPaymentModel();
        $data = array(
            'shop_id'       => $shop_id,
            'type'          => $type,
            'type_alis_id'  => $type_alis_id,
            'pay_body'      => $pay_body,
            'pay_detail'    => $pay_detail,
            'pay_money'     => $pay_money,
            'modify_time'   => date("Y-m-d H:i:s", time())
        );
        if($pay_money <= 0)
        {
            $data['pay_status'] = 1;
        }
        $res = $pay->save($data,['out_trade_no'=>$out_trade_no]);
        return $res;
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\IUnifyPay::delPayment()
     */
    public function delPayment($out_trade_no){
        $pay = new NsOrderPaymentModel();
        $res = $pay->where('out_trade_no',$out_trade_no)->delete();
        return $res;
    }
    
    /**
     * 线上支付主动根据支付方式执行支付成功的通知
     * @param unknown $out_trade_no
     */
    public function onlinePay($out_trade_no, $pay_type)
    {
        $pay = new NsOrderPaymentModel();
        try{
            //$pay_info = $pay->getInfo(['out_trade_no' => $out_trade_no]);
			$pay_info_arr= $pay->where(['out_trade_no' => $out_trade_no])->select();
			foreach($pay_info_arr as $key=>$val){
				if($val['pay_status'] == 0)
				{
					$data = array(
						'pay_status'     => 1,
						'pay_type'       => $pay_type,
						'pay_time'   => date("Y-m-d H:i:s", time())
					);
					$retval = $pay->save($data, ['id' => $val['id']]);
					//$pay_info = $pay->getInfo(['out_trade_no' => $out_trade_no], 'type');
					switch ($val['type']){
						case 1:  //订单支付
							$order = new Order();
							$order->orderOnLinePay($out_trade_no, $pay_type);
							break;
						case 2:
							$assistant = new NbsBusinessAssistant();
							$assistant->payOnlineBusinessAssistantApply($out_trade_no);
							break;
						case 4:
							//充值
							$member = new Member();
							$member->payMemberRecharge($out_trade_no);
							break;
						default:
							break;
					}
				}
			}
            return 1;
        }catch(\Exception $e)
        {
            Log::write("weixin-------------------------------".$e->getMessage());
            return $e->getMessage();
        }
    
    }
    /**
     * 只是执行单据支付，不进行任何处理用于执行支付后被动调用
     * @param unknown $out_trade_no
     * @param unknown $pay_type
     */
    public function offLinePay($out_trade_no, $pay_type)
    {
        $pay = new NsOrderPaymentModel();
        $data = array(
            'pay_status'     => 1,
            'pay_type'       => $pay_type,
            'pay_time'   => date("Y-m-d H:i:s", time())
        );
        $retval = $pay->save($data, ['out_trade_no' => $out_trade_no]);
        return $retval;
    }
    /**
     * 获取支付信息
     * @param unknown $out_trade_no
     */
    public function getPayInfo($out_trade_no)
    { 
        $pay = new NsOrderPaymentModel();
		$info=array();
        //$info = $pay->getInfo(['out_trade_no' => $out_trade_no], '*'); 改为多商户支付
		$info_arr=$pay->getQuery(['out_trade_no' => $out_trade_no], '*','id asc');
		$pay_status=1;
		foreach($info_arr as $key=>$val){
			$info['pay_money'] =empty($info['pay_money'])?$val['pay_money']:$info['pay_money']+$val['pay_money'];
			if($val['pay_status']==0){$pay_status=0;}
			$info['pay_status']=$pay_status; //只要有一个未支付该支付编号为未支付完
			$info['out_trade_no']=$out_trade_no;
			$info['type']=$val['type'];
			$info['create_time']=$val['create_time'];
			$arr=explode(';',$val['pay_body']);
			if($arr[1]=='shou_dang_mian'){
				$info['is_shou']=1;
			} else {$info['is_shou']=0;}
		}
        return $info;
    }
    /**
     * 重新设置编号，用于修改价格订单
     * @param unknown $out_trade_no
     * @param unknown $new_no
     * @return Ambigous <number, \think\false, boolean, string>
     */
    public function modifyNo($out_trade_no, $new_no)
    {   //单店铺支付使用的，弃用
        $pay = new NsOrderPaymentModel();
        $data = array(
            "out_trade_no" => $new_no
        );
        $retval = $pay->where(['out_trade_no' => $out_trade_no])->update($data);
        return $retval;
    }
    /**
     * 修改支付价格
     * @param unknown $out_trade_no
     */
    public function modifyPayMoney($out_trade_no, $pay_money)
    {
        $pay = new NsOrderPaymentModel();
        $data = array(
            'pay_money'       => $pay_money
        );
        $retval = $pay->save($data, ['out_trade_no' => $out_trade_no]);
    }
	/* (non-PHPdoc)
     * @see \data\api\IUnifyPay::wchatPay()
     */
    public function wchatPay($out_trade_no, $trade_type, $red_url)
    { 
        $data = $this->getPayInfo($out_trade_no);
        if(empty($data))
        {
            return -1;
        }
        $weixin_pay = new WeiXinPay();
        if($trade_type == 'JSAPI')
        {
            $openid = $weixin_pay->get_openid();
            $product_id = '';
        }
        if($trade_type == 'NATIVE')
        {
            $openid = '';
            $product_id = $out_trade_no;
        }
        if($trade_type == 'MWEB')
        {
            $openid = '';
            $product_id = $out_trade_no;
        }
        #接口参数中的total_fee单位必须为分,所以$data['pay_money']*100
        $retval = $weixin_pay->setWeiXinPay('微信订单支付', '微信支付', $data['pay_money']*100, $out_trade_no, $red_url, $trade_type, $openid, $product_id);
        return $retval;
        
        // TODO Auto-generated method stub
        
    }

	/* (non-PHPdoc)
     * @see \data\api\IUnifyPay::aliPay()
     */
    public function aliPay($out_trade_no, $notify_url, $return_url, $show_url)
    {
        $data = $this->getPayInfo($out_trade_no);
        if($data < 0)
        {
            return $data;
        }
        $ali_pay = new AliPay();
        $retval = $ali_pay->setAliPay($out_trade_no, $data['pay_body'], $data['pay_detail'], $data['pay_money'], 3, $notify_url, $return_url, $show_url);
        return $retval;
        // TODO Auto-generated method stub
        
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IUnifyPay::getWxJsApi()
     */
    public function getWxJsApi($UnifiedOrderResult)
    {
        $weixin_pay = new WeiXinPay();
        $retval = $weixin_pay->GetJsApiParameters($UnifiedOrderResult);
        return $retval;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\IOrder::getVerifyResult()
     */
    public function getVerifyResult($type){
        $pay = new AliPay();
        $verify = $pay->getVerifyResult($type);
        return $verify;
    }
    /**
     * 微信支付检测签名串
     * @param unknown $post_obj
     * @param unknown $sign
     */
    public function checkSign($post_obj, $sign)
    {
        $weixin_pay = new WeiXinPay();
        $retval = $weixin_pay->checkSign($post_obj, $sign);
        return $retval;
    }

}
