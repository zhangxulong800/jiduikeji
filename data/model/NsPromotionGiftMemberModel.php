<?php
/**
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
namespace data\model;

use data\model\BaseModel as BaseModel;
/**
 * 可领赠品/奖品会员表
CREATE TABLE `ns_promotion_gift_member` (
`id` int(11) NOT NULL,
`gift_id` int(11) NOT NULL COMMENT '赠品/奖品活动id',
`uid` int(11) NOT NULL COMMENT '用户id',
`last_restart_time` datetime NOT NULL COMMENT '最近一次参于时间',
`used_times` tinyint(4) NOT NULL COMMENT '记录id',
`gift_goods_id` int(11) NOT NULL COMMENT '赠品或中奖奖品ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='可领赠品/奖品会员表';



 */
class NsPromotionGiftMemberModel extends BaseModel {

    protected $table = 'ns_promotion_gift_member';
    protected $rule = [
        'id'  =>  '',
    ];
    protected $msg = [
        'id'  =>  '',
    ];

}