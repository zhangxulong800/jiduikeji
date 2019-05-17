<?php
/**
 * OrderGoods.php
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
namespace data\service\Order;

use data\service\Order\OrderStatus;
use data\model\NsOrderGoodsModel;
use data\model\NsOrderRefundModel;
use data\model\NsGoodsModel;
use data\model\NsGoodsSkuModel;
use data\service\BaseService;
use data\service\promotion\GoodsPreference;
use data\model\UserModel;
use data\model\AlbumPictureModel;
use data\model\NsOrderGoodsPromotionDetailsModel;
use data\service\GoodsCalculate\GoodsCalculate;
use data\model\NsOrderModel;
use think\Log;
use think\Db;
use data\service\Member\MemberAccount;
use data\service\promotion\GoodsDiscount;
use data\service\Order\Order as OrderBusiness;
// use think\Model;
/**
 * 订单商品操作类
 */
class OrderGoods extends BaseService
{

    public $order_goods;
 // 订单主表
    function __construct()
    {
        parent::__construct();
        $this->order_goods = new NsOrderGoodsModel();
    }

    /**
     * 订单创建添加订单项
     * order_goods_id int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单项ID',
     * order_id int(11) NOT NULL COMMENT '订单ID',
     * goods_id int(11) NOT NULL COMMENT '商品ID',
     * goods_name varchar(50) NOT NULL COMMENT '商品名称',
     * sku_id int(11) NOT NULL COMMENT 'skuID',
     * sku_name varchar(50) NOT NULL COMMENT 'sku名称',
     * price decimal(19, 2) NOT NULL DEFAULT 0.00 COMMENT '商品价格',
     * num varchar(255) NOT NULL DEFAULT '0' COMMENT '购买数量',
     * adjust_money varchar(255) NOT NULL DEFAULT '0' COMMENT '调整金额',
     * goods_money varchar(255) NOT NULL DEFAULT '0' COMMENT '商品总价',
     * goods_picture int(11) NOT NULL DEFAULT 0 COMMENT '商品图片',
     * shop_id int(11) NOT NULL DEFAULT 1 COMMENT '店铺ID',
     * buyer_id int(11) NOT NULL DEFAULT 0 COMMENT '购买人ID',
     * goods_type varchar(255) NOT NULL DEFAULT '1' COMMENT '商品类型',
     * promotion_id int(11) NOT NULL DEFAULT 0 COMMENT '促销ID',
     * promotion_type_id int(11) NOT NULL DEFAULT 0 COMMENT '促销类型',
     * order_type int(11) NOT NULL DEFAULT 1 COMMENT '订单类型',
     * order_status int(2) NOT NULL DEFAULT 0 COMMENT '订单状态',
     * give_point int(2) NOT NULL DEFAULT 0 COMMENT '积分数量',
     * shipping_status int(2) NOT NULL DEFAULT 0 COMMENT '物流状态',
     * refund_time datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '退款时间',
     * refund_type int(11) NOT NULL DEFAULT 1 COMMENT '退款方式',
     * refund_require_money decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '退款金额',
     * refund_reason varchar(255) NOT NULL DEFAULT '' COMMENT '退款原因',
     * refund_shipping_code varchar(255) NOT NULL DEFAULT '' COMMENT '退款物流单号',
     * refund_shipping_company int(11) NOT NULL DEFAULT 0 COMMENT '退款物流公司名称',
     * refund_real_money decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '实际退款金额',
     * refund_status int(1) NOT NULL DEFAULT 0 COMMENT '退款状态',
     * memo varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
     * PRIMARY KEY (order_goods_id)
     * 
     * @param unknown $goods_sku_list            
     */
    public function addOrderGoods($order_id,$order_status,$goods_sku_list, $adjust_money = 0)
    {
        $this->order_goods->startTrans();
        try {
            $err = 0;
            $goods_sku_list_array = explode(",", $goods_sku_list);
            foreach ($goods_sku_list_array as $k => $goods_sku_array) {
                $goods_sku = explode(':', $goods_sku_array);
                $goods_sku_model = new NsGoodsSkuModel();
                $goods_sku_info = $goods_sku_model->getInfo([
                    'sku_id' => $goods_sku[0]
                ], 'sku_id,goods_id,cost_price,stock,sku_name');
                $goods_model = new NsGoodsModel();
                $goods_info = $goods_model->getInfo([
                    'goods_id' => $goods_sku_info['goods_id']
                ], 'goods_name,shop_id,price,goods_type,picture,promotion_type,promote_id,point_exchange_type,give_point');
                $goods_promote = new GoodsPreference();
                $sku_price = $goods_promote->getGoodsSkuPrice($goods_sku_info['sku_id']);
                $goods_promote_info = $goods_promote->getGoodsPromote($goods_sku_info['goods_id']);
                if (empty($goods_promote_info)) {
                    $goods_info['promotion_type'] = 0;
                    $goods_info['promote_id'] = 0;
                }
                if ($goods_sku_info['stock'] < $goods_sku[1]) {
                    $this->order_goods->rollback();
                    return LOW_STOCKS;
                }
                $give_point = $goods_sku[1] * $goods_info["give_point"];
                // 库存减少销量增加
                $goods_calculate = new GoodsCalculate();
                $goods_calculate->subGoodsStock($goods_sku_info['goods_id'], $goods_sku_info['sku_id'], $goods_sku[1], '');
                $goods_calculate->addGoodsSales($goods_sku_info['goods_id'], $goods_sku_info['sku_id'], $goods_sku[1]);
                $data_order_sku = array(
                    'order_id' => $order_id,
                    'goods_id' => $goods_sku_info['goods_id'],
                    'goods_name' => $goods_info['goods_name'],
                    'sku_id' => $goods_sku_info['sku_id'],
                    'sku_name' => $goods_sku_info['sku_name'],
                    'price' => $sku_price,
                    'num' => $goods_sku[1],
                    'adjust_money' => $adjust_money,
                    'cost_price' => $goods_sku_info['cost_price'],
                    'goods_money' => $sku_price * $goods_sku[1] - $adjust_money,
                    'goods_picture' => $goods_info['picture'],
                    'shop_id' => $goods_info['shop_id'],
                    'buyer_id' => $this->uid,
                    'goods_type' => $goods_info['goods_type'],
					'order_status' => $order_status,
                    'promotion_id' => $goods_info['promote_id'],
                    'promotion_type_id' => $goods_info['promotion_type'],
                    'point_exchange_type' => $goods_info['point_exchange_type'],
                    'order_type' => 1, // 订单类型默认1
                    'give_point' => $give_point
                ) ;// 积分数量默认0
				$goodsDiscount = new GoodsDiscount();
				$bl_id=$goodsDiscount->getDiscountGoodsidByGoodsid($goods_sku_info['goods_id']);
				if($bl_id > 0){
					$memo='discount_goods_id-'.$bl_id;
					$data_order_sku['memo'] = $memo;
				}
                if ($goods_sku[1] == 0) {
                    $err = 1;
                }
                $order_goods = new NsOrderGoodsModel();
                
                $order_goods->save($data_order_sku);
            }
            if ($err == 0) {
                $this->order_goods->commit();
                return 1;
            } elseif ($err == 1) {
                $this->order_goods->rollback();
                return ORDER_GOODS_ZERO;
            }
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 订单项发货
     * 
     * @param unknown $order_goods_id_array
     *            ','隔开
     */
    public function orderGoodsDelivery($order_id, $order_goods_id_array)
    {   //该函数单独分开，在自提与物流时共用
        $this->order_goods->startTrans();
        try {
            $id_array = explode(',', $order_goods_id_array);
			$order = new Order();
            foreach ($id_array as $k => $order_goods_id) {
                $order_goods_id = (int) $order_goods_id;
                $data = array(
                    'shipping_status' => 1
                );
                $order_goods = new NsOrderGoodsModel();
                $retval = $order_goods->save($data, [
                    'order_goods_id' => $order_goods_id
                ]);
				$order->shopSettlement($order_goods_id);//发货就与商家结算，退款时退回
            }
            
            $order->orderDoDelivery($order_id);
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
        
        return $retval;
    }

    /**
     * 买家退款申请
     * 
     * @param unknown $order_id
     *            订单ID
     * @param unknown $order_goods_id_array
     *            订单项ID (','隔开)
     * @param unknown $refund_type            
     * @param unknown $refund_require_money            
     * @param unknown $refund_reason            
     * @return number|Exception|Ambigous <number, \think\false>
     */
    public function orderGoodsRefundAskfor($order_id, $order_goods_id, $refund_type, $refund_require_money, $refund_reason)
    {
        $this->order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[0]['status_id'];
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id,
                'refund_time' => date('Y-m-d H:i:s', time()),
                'refund_type' => $refund_type,
                'refund_require_money' => $refund_require_money,
                'refund_reason' => $refund_reason
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            $goods_info = $order_goods->getInfo(['order_goods_id' => $order_goods_id], 'order_id,goods_name,shop_id');
			$phone=Db::table('ns_shop')->where('shop_id',$goods_info['shop_id'])->value('shop_phone');
			if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){
				$msg='尊敬的商户，您好！订单编号'.$goods_info['order_id'].'中的'.$goods_info['goods_name'].'，用户发起退款（退货）申请，请在24小时内登录商户后台处理，非常感谢您的配合【积分呗】';
				$smsinfo=luosimaoSmsSend($phone,$msg);
				if ($smsinfo == 'success') {
					$status=Db::table("ns_mobile_msgs")->insert(["mobile"=>$phone,"msg"=>$msg,"status"=>3]);
				}
			}
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 1, $this->uid);
            // 订单退款操作
            $order = new Order();
            $res = $order->orderGoodsRefundFinish($order_id);
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 买家取消退款
     */
    public function orderGoodsCancel($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[6]['status_id'];
            
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 1, $this->uid);
            // 订单退款操作
            $order = new Order();
            $res = $order->orderGoodsRefundFinish($order_id);
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 买家退货
     */
    public function orderGoodsReturnGoods($order_id, $order_goods_id, $refund_shipping_company, $refund_shipping_code)
    {
        $order_goods = NsOrderGoodsModel::get($order_goods_id);
        $order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[2]['status_id'];
            
            // 订单项退款操作
            $order_goods->refund_status = $status_id;
            $order_goods->refund_shipping_company = $refund_shipping_company;
            $order_goods->refund_shipping_code = $refund_shipping_code;
            $retval = $order_goods->save();
            
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 1, $this->uid);
            
            $order_goods->commit();
            return $retval;
        } catch (\Exception $e) {
            $order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 卖家同意买家退款申请
     */
    public function orderGoodsRefundAgree($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            
            // 退款信息
            $refund_status = OrderStatus::getRefundStatus();
            $orderGoodsInfo = NsOrderGoodsModel::get($order_goods_id);
            $refund_type = $orderGoodsInfo->refund_type;
            if ($refund_type == 1) { // 仅退款
                $status_id = OrderStatus::getRefundStatus()[3]['status_id'];
            } else { // 退货退款
                $status_id = OrderStatus::getRefundStatus()[1]['status_id'];
            }
            
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款记录
            
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            $this->order_goods->commit();

            $sms_info = '尊敬的会员，您好！您的订单'
                .(new NsOrderModel())->getInfo(['order_id'=>$order_id], 'order_no')['order_no']
                .'商家的退货申请已成功受理，请保持电话通常以免错过商户/平台客户联系导致您的退货进度延缓，谢谢！';
            sendSmsByUid($this->uid, $sms_info);

            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e->getMessage();
        }
    }

    /**
     * 卖家永久拒绝本退款
     */
    public function orderGoodsRefuseForever($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            
            $status_id = OrderStatus::getRefundStatus()[5]['status_id'];
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款记录
            
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            // 订单恢复正常操作
            $order = new Order();
            $res = $order->orderGoodsRefundFinish($order_id);
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e;
        }
    }

    /**
     * 卖家拒绝本次退款
     */
    public function orderGoodsRefuseOnce($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[7]['status_id'];
            
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款日志
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            // 订单恢复正常操作
            $order = new Order();
            $res = $order->orderGoodsRefundFinish($order_id);
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e;
        }
    }

    /**
     * 卖家确认收货
     */
    public function orderGoodsConfirmRecieve($order_id, $order_goods_id)
    {
        $this->order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[3]['status_id'];
            
            // 订单项退款操作
            $order_goods = new NsOrderGoodsModel();
            $order_goods_data = array(
                'refund_status' => $status_id
            );
            $res = $order_goods->save($order_goods_data, [
                'order_goods_id' => $order_goods_id
            ]);
            
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e;
        }
    }

    /**
     * 卖家确认退款
     */
    public function orderGoodsConfirmRefund($order_id, $order_goods_id, $refund_real_money)
    {
        $order_goods = NsOrderGoodsModel::get($order_goods_id);
        $order_goods->startTrans();
        try {
            $status_id = OrderStatus::getRefundStatus()[4]['status_id'];
            // 订单项退款操作
            $order_goods->refund_status = $status_id; //此处变为5了
            $order_goods->refund_real_money = $refund_real_money;
            $res = $order_goods->save();
            // 退款记录
            $this->addOrderRefundAction($order_goods_id, $status_id, 2, $this->uid);
            $order_model = new NsOrderModel();
			$order_info = $order_model->getInfo(['order_id' => $order_id],'*');
			$goods_rec=$order_goods->where('order_id',$order_id)->field('order_goods_id,refund_real_money,refund_status')->select();
			if(empty($order_info['shipping_status'])){$status_str=1;} elseif($order_info['shipping_status']==1){$status_str=2;}
			$sec_str=0;//默认订单中全部退货，为0
			foreach($goods_rec as $k=>$v){
				if($v['order_goods_id']!==$order_goods_id && $v['refund_status']!==5){$sec_str=1;} //order_money有为0并同意退款的情形
			}
			
			if($sec_str == 0){ $status_str=5; } //关闭订单
			$once=['refund_money' =>$order_info['refund_money']+$refund_real_money,'order_status' =>$status_str];
            // 订单添加退款金额,并在全部是退货时变为关闭
            $order_model->save($once,['order_id' => $order_id]);
			$member_account = new MemberAccount();
            // 退款 扣除已发放的积分
            $give_point = $order_goods["give_point"];
            if ($give_point > 0) {
                $text = "订单商品表编号".$order_goods_id."退款成功,扣除已发放的积分";
                $member_account->addMemberAccountData($order_info['shop_id'], 1, $order_info['buyer_id'], 0, -$give_point, 1,$order_goods_id,$text);
            }
            $total_point = $order_info["give_point"] - $give_point;
			if($total_point > 0){
				$order_model->save(["give_point" => $total_point],['order_id' => $order_id]);
			}
			//本项目支付的余额优先退回.差值退积分
			$condition['uid'] =$order_goods['buyer_id'];
			$condition['account_type'] =2;$condition['sign'] =1;$condition['from_type'] =2;
			$condition['text'] = array('like','%No'.$order_info['order_no']);
			$already_refmoney=Db::table('ns_member_account_records')->where($condition)->sum('number');
			$cha=$order_info['user_platform_money']+$order_info['pay_money']-$already_refmoney;//判断剩余
			$convert_rate=Db::table('ns_point_config')->where('shop_id',0)->value('convert_rate');
			if($cha > 0){
				$refund_real_cha=$cha-$refund_real_money;
				if($refund_real_cha >= 0){
					$member_account->addMemberAccountData($order_goods['shop_id'],2,$order_goods['buyer_id'],1,$refund_real_money,2,$order_goods_id,'订单商品表编号'.$order_goods_id.'的商品余额退款No'.$order_info['order_no']);
				} else {
					$member_account->addMemberAccountData($order_goods['shop_id'],2,$order_goods['buyer_id'],1,$cha,2,$order_goods_id,'订单商品表编号'.$order_goods_id.'的商品余额退款No'.$order_info['order_no']);
					$cha_ponit=round($refund_real_cha/$convert_rate,2);
					$member_account->addMemberAccountData($order_goods['shop_id'],1,$order_goods['buyer_id'],1,-$cha_ponit,2,$order_goods_id,'订单商品表编号'.$order_goods_id.'的商品退积分No'.$order_info['order_no']);
				}
			} else {  //应该只有等于0的情形
				$refund_real_ponit=round($refund_real_money/$convert_rate,2);
				$member_account->addMemberAccountData($order_goods['shop_id'],1,$order_goods['buyer_id'],1,$refund_real_ponit,2,$order_goods_id,'订单商品表编号'.$order_goods_id.'的商品退积分No'.$order_info['order_no']);
			}
			if($order_goods['shipping_status']==1){$this->shopBackAction($order_goods);}
			$orderBusiness=new OrderBusiness();
			if($sec_str == 0){ $orderBusiness->deleteCommission($order_info); } //关闭订单时，删除分销预处理记录
			else { $orderBusiness->orderCommission($order_id); }  //重新执行分销
			$order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $order_goods->rollback();
            return $e->getMessage();
        }
    }
	/*用户退款扣回商家余额*/
	public function shopBackAction($order_goods){
		$shopinfo=Db::table('ns_shop')->where('shop_id',$order_goods['shop_id'])->field('money,point')->find();
		$logistics=Db::table('ns_goods')->where('goods_id',$order_goods['goods_id'])->value('cost_price_logistics');
		$tot_back=($logistics+$order_goods['cost_price'])*$order_goods['num'];
		$nowmoney=$shopinfo['money']-$tot_back;//项目独用
		$nowpoint=$shopinfo['point'];
		$data_no='F_'.$order_goods['order_goods_id'].'_orderid_'.$order_goods['order_id'].'_skuid_'.$order_goods['sku_id'];
		$onerec=Db::table('ns_shop_account_records')->where(['rec_type'=>1,'data_no'=>$data_no,'shop_id'=>$order_goods['shop_id'],'data_id'=>$order_goods['order_goods_id']])->find();
		if(!empty($onerec)){
			$data=array('rec_type'=>1,'data_no'=>'Deduct_'.$data_no,'shop_id'=>$order_goods['shop_id'],'money'=>-$tot_back,'data_id'=>$order_goods['order_goods_id'],'shop_money'=>$nowmoney,'shop_point'=>$nowpoint,'create_time'=>date("Y-m-d H:i:s", time()),'text'=>'用户退货扣回结算款');
			Db::table('ns_shop_account_records')->insert($data);
			Db::table('ns_shop')->where('shop_id',$order_goods['shop_id'])->update(['point' =>$nowpoint,'money' =>$nowmoney]);
		}
	}
    /**
     * 添加订单退款日志
     * 
     * @param unknown $order_goods_id            
     * @param unknown $refund_status            
     * @param unknown $action            
     * @param unknown $action_way            
     * @param unknown $uid            
     * @param unknown $user_name            
     */
    public function addOrderRefundAction($order_goods_id, $refund_status_id, $action_way, $uid)
    {
        $refund_status = OrderStatus::getRefundStatus();
        foreach ($refund_status as $k => $v) {
            if ($v['status_id'] == $refund_status_id) {
                $refund_status_name = $v['status_name'];
            }
        }
        $user = new UserModel();
        $user_name = $user->getInfo([
            'uid' => $uid
        ], 'user_name');
        $order_refund = new NsOrderRefundModel();
        $data_refund = array(
            'order_goods_id' => $order_goods_id,
            'refund_status' => $refund_status_id,
            'action' => $refund_status_name,
            'action_way' => $action_way,
            'action_userid' => $uid,
            'action_username' => $user_name['user_name'],
            'action_time' => date('y-m-d H:i:s', time())
        );
        $retval = $order_refund->save($data_refund);
        return $retval;
    }

    /**
     * 订单项商品价格调整
     * 
     * @param unknown $order_goods_id_adjust_array
     *            订单项数列 order_goods_id,adjust_money;order_goods_id,adjust_money
     */
    public function orderGoodsAdjustMoney($order_goods_id_adjust_array)
    {
        $this->order_goods->startTrans();
        try {
            $order_goods_id_adjust_array = explode(';', $order_goods_id_adjust_array);
            if (! empty($order_goods_id_adjust_array)) {
                foreach ($order_goods_id_adjust_array as $k => $order_goods_id_adjust) {
                    $order_goods_adjust_array = explode(',', $order_goods_id_adjust);
                    $order_goods_id = $order_goods_adjust_array[0];
                    $adjust_money = $order_goods_adjust_array[1];
                    $order_goods_info = $this->order_goods->get($order_goods_id);
                    // 调整金额
                    $adjust_money_adjust = $adjust_money - $order_goods_info['adjust_money'];
                    $data = array(
                        'adjust_money' => $adjust_money,
                        'goods_money' => $order_goods_info['goods_money'] + $adjust_money_adjust
                    );
                    $order_goods = new NsOrderGoodsModel();
                    $order_goods->save($data, [
                        'order_goods_id' => $order_goods_id
                    ]);
                }
            }
            
            $this->order_goods->commit();
            return 1;
        } catch (\Exception $e) {
            $this->order_goods->rollback();
            return $e;
        }
    }

    /**
     * 获取订单项实际可退款金额
     * 
     * @param unknown $order_goods_id            
     */
    public function orderGoodsRefundMoney($order_goods_id)
    {
        $order_goods = new NsOrderGoodsModel();
        $order_goods_info = $order_goods->getInfo([
            'order_goods_id' => $order_goods_id
        ], 'order_id,sku_id,goods_money');
        $order_goods_promotion = new NsOrderGoodsPromotionDetailsModel();
        $promotion_money = $order_goods_promotion->where([
            'order_id' => $order_goods_info['order_id'],
            'sku_id' => $order_goods_info['sku_id']
        ])->sum('discount_money');
        if (empty($promotion_money)) {
            $promotion_money = 0;
        }
        $money = $order_goods_info['goods_money'] - $promotion_money;
        // 计算其他方式支付金额
        $order = new NsOrderModel();
        $order_other_pay_money = $order->getInfo([
            'order_id' => $order_goods_info['order_id']
        ], 'order_money,point_money,user_money,coin_money,user_platform_money');
        $all_other_pay_money = $order_other_pay_money['point_money'] + $order_other_pay_money['user_money'] + $order_other_pay_money['coin_money'] + $order_other_pay_money['user_platform_money'];
		/*  少一元，本项目不用
		if ($all_other_pay_money > 0) {
            $money = $money - floor($money / $order_other_pay_money['order_money']);
        } */
        if ($money < 0) {
            $money = 0;
        }
        return $money;
    }

    /**
     * 查询订单项退款
     * 
     * @param unknown $order_goods_id            
     */
    public function getOrderGoodsRefundDetail($order_goods_id)
    {
        // 查询基础信息
        $order_goods_info = $this->order_goods->get($order_goods_id);
        // 商品图片
        $picture = new AlbumPictureModel();
        $picture_info = $picture->get($order_goods_info['goods_picture']);
        $order_goods_info['picture_info'] = $picture_info;
        if ($order_goods_info['refund_status'] != 0) {
            $order_refund_status = OrderStatus::getRefundStatus();
            foreach ($order_refund_status as $k_status => $v_status) {
                
                if ($v_status['status_id'] == $order_goods_info['refund_status']) {
                    $order_goods_info['refund_operation'] = $v_status['refund_operation'];
                    $order_goods_info['status_name'] = $v_status['status_name'];
                }
            }
            // 查询订单项的操作日志
            $order_refund = new NsOrderRefundModel();
            $refund_info = $order_refund->all([
                'order_goods_id' => $order_goods_id
            ]);
            $order_goods_info['refund_info'] = $refund_info;
        } else {
            $order_goods_info['refund_operation'] = '';
            $order_goods_info['status_name'] = '';
            $order_goods_info['refund_info'] = '';
        }
        return $order_goods_info;
    }
}