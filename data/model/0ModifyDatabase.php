/*
ALTER TABLE `ns_member`
ADD COLUMN `assign_jplus_time`  datetime NOT NULL COMMENT '注册时间' ;

ALTER TABLE `ns_member`
MODIFY COLUMN `grade`  int(2) NULL DEFAULT 0 COMMENT '二级分销通证佣金计算级别：2、经理；5、总监' ,
ADD COLUMN `jplus_level`  tinyint(1) NOT NULL DEFAULT 0 COMMENT 'JPlus会员级别：0、无会员资格；10、399会员；20、498会员' ;

ALTER TABLE `ns_coupon_type`
ADD COLUMN `valid_days`  int(1) NOT NULL DEFAULT 0 COMMENT '领用后有效天数，为0时，表示有效时间段ny_coupon_type的start_time到end_time时间段内';

ALTER TABLE `ns_goods`
ADD COLUMN `total_interest`  decimal(19,2) NOT NULL DEFAULT 0 COMMENT '毛利';
update `ns_goods` set `total_interest` = `price` - `cost_price` - `total_cost_price`;

ALTER TABLE `ns_shop_account_records`
ADD COLUMN `is_add`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否入帐：0、未入帐；1、已入帐';

ALTER TABLE `ns_goods`
ADD COLUMN `first_free_num` int(8) DEFAULT 2 COMMENT '首单全免包邮需付款倍数';


*/