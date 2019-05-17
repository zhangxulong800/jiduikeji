<?php
/**
 * GoodsGift.php
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
namespace data\service\promotion;
use data\service\BaseService;
use data\model\NsPromotionGiftGoodsModel;
use data\model\NsPromotionGiftModel;
use data\model\NsPromotionGiftMemberModel;
use data\model\AlbumPictureModel;
/**
 * 商品总赠品管理
 */
class GoodsGift extends BaseService{
    /**
     * 查询赠品的商品信息
     * @param unknown $gift_id
     */
    public function getGiftGoodsInfo($gift_id)
    {
        $gift = new NsPromotionGiftGoodsModel();
        $goods_info = $gift->getInfo(['gift_id' => $gift_id], 'goods_id,goods_name,goods_picture');
        $picture = new AlbumPictureModel();
        $picture_info = $picture->getInfo(['pic_id' => $goods_info['goods_picture']], '*');
        $goods_info['picture_info'] = $picture_info;
        return $goods_info;
    }

    public function userAchieveGift($uid, $gift_id, $order){
        $gift_info = (new NsPromotionGiftModel())->getInfo(['gift_id' => $gift_id], '*');
        if(empty($gift_info)){
            return '不要气馁，您下次的手气会更好！';
        }

//        return var_dump($gift_info);
        $gift_member = (new NsPromotionGiftMemberModel())->getInfo(['uid'=>$uid, 'gift_id'=>$gift_id], '*');
        if($gift_member['gift_goods_id'] != 0){
            return '恭禧您，您已在本次的活动中奖了！有新的活动开启后，欢迎您再来参加！';
        }

        if(strtotime($gift_info['start_time']) > time()){
            return '本次活动还没有开始，请在【'.$gift_info['start_time'].'】后，再参加本次活动！';
        }
        if(strtotime($gift_info['end_time']) < time()){
            return '本次活动已经在【'.$gift_info['end_time'].'】结束！';
        }
        //enable_status 当前状态（10、可用；20、暂停；30、结束）
        if($gift_info['enable_status'] == 20){
            return '本次活动暂时暂停，请继续关注活动通知，在活动重新开启后继续参加！';
        }
        if($gift_info['enable_status'] == 30){
            return '本次活动已经结束！';
        }
        if($gift_info['remain_quantity'] == 0){
            return '本次活动的赠品或奖品已经全部领取完！';
        }
        if(($gift_info['max_num'] > 0) && ($gift_member['used_times'] > $gift_info['max_num'])){
            return '您的抽奖次数已用完！有新的活动开启后，欢迎您再来参加！';
        }
        //achieve_type '获得赠品/奖品方式（10、先到先得；20、随机抽奖）',
        if(($gift_info['achieve_type'] == 10) && ($gift_member['gift_goods_id'] == 0) && ($gift_info['remain_quantity'] > 0)){
            return '恭禧您，您在本次的活动中赢得了某个奖品';
        }
        //interval_type 间隔类型（10、整个活动重新开始的间隔；20、单个会员重新开始的间隔）
        if(($gift_info['interval_type'] == 10) && ($gift_info['interval_hours']> 0)
            && (strtotime($gift_info['last_restart_time']) - strtotime($gift_member['last_restart_time']) < $gift_info['interval_hours'] * 60)){
            return '请在【'.date('Y-m-d H:i;s', strtotime("+".$gift_info['interval_hours']." hour", strtotime($gift_info['last_restart_time']))).'】时刻后，再次参加本次活动！';
        }
        //interval_type 间隔类型（10、整个活动重新开始的间隔；20、单个会员重新开始的间隔）
        if(($gift_info['interval_type'] == 20) && ($gift_info['interval_hours']> 0)
            && (time() - strtotime($gift_member['last_restart_time']) < $gift_info['interval_hours'] * 60)){
            return '请在【'.date('Y-m-d H:i;s', strtotime("+".$gift_info['interval_hours']." hour", strtotime($gift_member['last_restart_time']))).'】时刻后，再次参加本次活动！';
        }
        $order_id = mt_rand();
        $order_id = $order_id % $gift_info['member_quantity'];
        if($order_id > $gift_info['remain_quantity']){
            (new NsPromotionGiftMemberModel())->save(array('used_times' => $gift_member['used_times'] + 1,
                'last_restart_time'=>time()), ['gift_id' => $gift_info['gift_id'], 'uid' => $uid]);
            return '不要气馁，下次您的手气会更好！';
        }
        //master_id > 0 表示是奖品组合中的奖项
        $goods_list = (new NsPromotionGiftGoodsModel())->getQuery('gift_id = '.$gift_info['gift_id'].' and remain_quantity > 0 and master_id = 0',
            'remain_quantity, gift_goods_id, gift_value', 'gift_value desc');
        $update_data = array();
        foreach ($goods_list as $k => $v){
            $order_id = $order_id - $v['remain_quantity'];
            if($order_id <= 0){
                $order_id = $v['gift_goods_id'];
                $update_data = array(
                    'gift_goods_id' => $order_id,
                    'remain_quantity' => $v['remain_quantity'] - 1
                );
                break;
            }
        }
        if(empty($update_data)){
            (new NsPromotionGiftMemberModel())->save(array('used_times' => $gift_member['used_times'] + 1,
                'last_restart_time'=>time()), ['gift_id' => $gift_info['gift_id'], 'uid' => $uid]);
            return '不要气馁，下次您的手气会更好！';
        }
//        (new NsPromotionGiftGoodsModel())->save($update_data, );
        if((new NsPromotionGiftModel())->save(array('remain_quantity'=> $gift_info['remain_quantity'] - 1), ['gift_id' => $gift_info['gift_id'], 'remain_quantity' => $gift_info['remain_quantity']]) != 1){
            return '不要气馁，下次您的手气会更好！';
        }
        if((new NsPromotionGiftGoodsModel())->save($update_data, ['gift_goods_id' => $order_id, 'remain_quantity' => $v['remain_quantity']]) != 1){
            return '不要气馁，下次您的手气会更好！';
        }
        (new NsPromotionGiftMemberModel())->save(array('used_times' => $gift_member['used_times'] + 1,
            'last_restart_time'=>time(),
            'gift_goods_id' => $order_id), ['gift_id' => $gift_info['gift_id'], 'uid' => $uid]);
        return $v;
    }
}