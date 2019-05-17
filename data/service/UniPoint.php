<?php
namespace data\service;

use data\model\NsShopAccountRecordsModel;
use data\service\BaseService as BaseService;
use data\model\NsRptPointModel;
use data\model\NsMemberAccountRecordsModel;
use data\model\NsMemberAccountModel;

class UniPoint extends BaseService
{
//        $create_time = isset($_GET['create_time']) ? $_GET['create_time'] :'2018-11-03';
        static function CalculatePoint($date, $r10, $r11, $r13){
//        //当天毛利=（商品售价-成本价）*数量；--注意不是成本价大于进价
//        $interest_of_today  = 0;
//        //昨天可转入总积分数(非冻结、非待入账 )
//        //注意：模型为模拟值，实际需要从积分账户根据积分账户状态和变更时间进行统计
//        $effective_interest_of_today  = $interest_of_today * 0.49;
//        //昨天积分池内支付核销（支付或转余额核销的）
//        //实际需要根据积分账户变动记录获得数据）
//        //C11
//        $used_point  = 0;
//        //B11
//        $used_point_on_yesterday  = 0;
//        //1.期初可参与收益积分（取自前一天实际结余积分，示例数据有差异见说明）；C13
//        $unused_point = 0;
//        //昨日集采可售额（可直接使用行11数据），即昨天毛利
//        $interest_of_yestoday = 0;
//        //单日集采销售额（3% 要可配置）
//        $sales_income = $interest_of_yestoday * 0.03;
//        //2.当日集采投资收益（投资待分配额50% 、60%比例要可配置）
//        $interest_of_sales_income = $sales_income * 05 * 06;
//        //3、集采投资收益总额 当日+历史每日累计收益，表格中显示为：当日集采投资收益+上一日集采投资收益
//        $interest_of_sales_income_predate = 0;
//        $total_interest_of_sales_income = $interest_of_sales_income + $interest_of_sales_income_predate;
//        //截止昨日累计待分配积分总额
//        $total_point = $total_interest_of_sales_income + $unused_point;
//        //可用分配资金额；C20
//        $enjoy_point = $total_point / 90;
//        //实际领取金额；C21
//        $fetched_point = 0;
//        //每积分获得余额系数C27
//        $enjoy_parameter = $enjoy_point/($unused_point - $used_point_on_yesterday);
//        //实际分配领取的积分；C30
//        $enjoyed_fetched_point = $fetched_point / 6;
//        //分配积分实际结余（用户实际领取情况统计）；C31
//        $remain_point = $unused_point - $enjoyed_fetched_point;

//        $C9 = 2500.00;//昨日总毛利，统计时为：（商品售价-成本价）*数量
//        $C10 = 1225.00;//昨天可转入总积分数(非冻结、非待入账 ) ，统计时为，昨天天入帐的积分总数
//        $C11 = 245.00;//昨天积分池内支付核销，统计时为，昨天使用积分支付或消耗的总数
//
//        $C13 = 490;//1.期初可参与收益积分（取自前一天实际结余积分，示例数据有差异见说明）
//        $C14 = 147;//集采备付金(30% 不参与积分分红分配 可不计算？)
//        $C15 = 490;//昨日集采可售额（可直接使用行11数据）
//        $C16 = 14.7;//单日集采销售额（3% 要可配置）
//        $C17 = 4.41;//2.当日集采投资收益（投资待分配额50% 、60%比例要可配置）
//        $C18 = 4.41;//3、集采投资收益总额 当日+历史每日累计收益
//        $C19 = 494.41;//截止昨日累计待分配积分总额
//        $C20 = 5.49;//昨日可用分配资金额
//        $C21 = 488.92;//分配后资金预计结余
//        $C22 = 488.92;//分配后积分池对应资金实际结余（根据用户实际领取情况统计）
//
//        $C25 = 490.00 ;//可参与分配总积分
//        $C26 = 0.0112 ;//每积分获得余额系数
//        $C27 = 0.92 ;//当日已分配积分核销（预计）
//        $C28 = 489.08 ;//分配积分预计结余
//        $C29 = 489.08 ;//分配积分实际结余（用户实际领取情况统计）

        //
//        $D9 = 0.00;//昨日总毛利
//        $D10 = 0;//昨天可转入总积分数(非冻结、非待入账 ) ，统计时为，昨天天入帐的积分总数
//        $D11 = 0.00;//昨天积分池内支付核销，统计时为，昨天使用积分支付或消耗的总数
//        $C9 = 0.00;//昨日总毛利，统计时为：（商品售价-成本价）*数量
//        $C10 = 0.00;//昨天可转入总积分数(非冻结、非待入账 ) ，统计时为，昨天天入帐的积分总数
//        $C11 = 0.00;//昨天积分池内支付核销，统计时为，昨天使用积分支付或消耗的总数
//
//        $C13 = 0;//1.期初可参与收益积分（取自前一天实际结余积分，示例数据有差异见说明）
//        $C14 = 0;//集采备付金(30% 不参与积分分红分配 可不计算？)
//        $C15 = 0;//昨日集采可售额（可直接使用行11数据）
//        $C16 = 0;//单日集采销售额（3% 要可配置）
//        $C17 = 0;//2.当日集采投资收益（投资待分配额50% 、60%比例要可配置）
//        $C18 = 0;//3、集采投资收益总额 当日+历史每日累计收益
//        $C19 = 0;//截止昨日累计待分配积分总额
//        $C20 = 0;//昨日可用分配资金额
//        $C21 = 0;//分配后资金预计结余
//        $C22 = 0;//分配后积分池对应资金实际结余（根据用户实际领取情况统计）
//
//        $C25 = 0 ;//可参与分配总积分
//        $C26 = 0;//每积分获得余额系数
//        $C27 = 0;//当日已分配积分核销（预计）
//        $C28 = 0;//分配积分预计结余
//        $C29 = 0;//分配积分实际结余（用户实际领取情况统计）

//        $create_time = isset($_GET['create_time']) ? $_GET['create_time'] :'2018-11-03';
        $condition['create_time'] = date("Y-m-d",strtotime("-2 day",strtotime($date)));
//        $condition['id'] = 3;
//        var_dump($condition);

        $rpt_point = NsRptPointModel::get($condition);
        $C10 = $rpt_point->r10;//昨天可转入总积分数(非冻结、非待入账 ) ，统计时为，昨天天入帐的积分总数
        $C11 = $rpt_point->r11;//昨天积分池内支付核销，统计时为，昨天使用积分支付或消耗的总数
        $C13 = $rpt_point->r13;//1.期初可参与收益积分（取自前一天实际结余积分，示例数据有差异见说明）
        $C18 = $rpt_point->r18;//3、集采投资收益总额 当日+历史每日累计收益
        $C28 = $rpt_point->r28;//分配积分预计结余
        $C29 = $rpt_point->r29;//分配积分预计结余
//        var_dump($rpt_point);

//        $condition['create_time'] = date("Y-m-d",strtotime($date));
//        $rpt_point = (new NsRptPointModel)->getQuery($condition, '*', '');
//        $D10 = $rpt_point[0]['r10'];

        $D13 = $C10 + $C13 - $C11;//1.期初可参与收益积分（取自前一天实际结余积分，示例数据有差异见说明）
            $D13 = $r13;
//        var_dump($D13);
//            $D14 = $C10 * 0.3;//集采备付金(30% 不参与积分分红分配 可不计算？)
            $D14 = $r10 * 0.3;//集采备付金(30% 不参与积分分红分配 可不计算？)
//        var_dump($D14);
        $D15 = $D14 / 0.3;//昨日集采可售额（可直接使用行11数据）
//        var_dump($D15);
        $D16 = $D15 * 0.03;//单日集采销售额（3% 要可配置）
//        var_dump($D16);
        $D17 = $D16 * 0.5 * 0.6;//2.当日集采投资收益（投资待分配额50% 、60%比例要可配置）
//        var_dump($D17);
        $D18 = $D17 + $C18;//3、集采投资收益总额 当日+历史每日累计收益
//        var_dump($D18);
        $D19 = $D13 + $D18;//截止昨日累计待分配积分总额
//        var_dump($D19);
        $D20 = $D19 / 90;//昨日可用分配资金额；除以天数 ，90天拟定调为180天
//        var_dump($D20);
        $D21 = $D19 - $D20;//分配后资金预计结余
        $D22 = $D19 - $D20;//分配后积分池对应资金实际结余（根据用户实际领取情况统计）

        $D25 = $C29 + $C10 - $C11;//可参与分配总积分
		/*以前代码计算不准，以下修复*/
		if($D25<0){
			$start_time = date("Y-m-d",strtotime("-1 day"));
			$end_time = date("Y-m-d",time());
			$condnew = array(
				'create_time' => [array('EGT', $start_time),array('LT', $end_time)],
				'account_type'=> 1,
				'number'     => ['<',0],
				'from_type'     =>array('in',[1,8,40]),
			);
			$account_records = new NsMemberAccountRecordsModel();
			$yesterdayUse=$account_records->where($condnew)->sum('number');//数据库获取值
			$D25 = $C29 + $C10 + $yesterdayUse;
			if($D25<0){$D25 = $r13 + $C10 + $yesterdayUse;}
		}
		/*补充修复完毕*/
        $D26 = 0;
        if($D25 != 0){
            $D26 = $D20 / $D25;//每积分获得余额系数
        }
        $D27 = $D20 / 6;//当日已分配积分核销（预计）
//        var_dump($D27);
//        $D28 = $D25 - $D27;//分配积分预计结余
//            $D29 = $D25 - $r28;//分配积分实际结余（用户实际领取情况统计）
            $D29 = $D25 - 0;//分配积分实际结余（用户实际领取情况统计）
        $condition['create_time'] = $date;
        $rpt_point = NsRptPointModel::get($condition);
        if(!empty($rpt_point)){
            $rpt_point->r10 = $r10;
            $rpt_point->r11 = $r11;
            $rpt_point->r13 = $D13;
            $rpt_point->r18 = $D18;
            $rpt_point->r25 = $D25;
            $rpt_point->r27 = $D27;
//            $rpt_point->r28 = $r28;
            $rpt_point->r28 = 0;
            $rpt_point->r29 = $D29;
            $rpt_point->save();
        }
        else{
            $data = array(
                "create_time" => $date,
                "r10" => $r10,
                "r11" => $r11,
                "r13" => $D13,
                "r18" => $D18,
                "r25" => $D25,
                "r27" => $D27,
//                "r28" => $r28,
                "r28" => 0,
                "r29" => $D29
            ); 
            NsRptPointModel::create($data);
        }
        return (new NsRptPointModel)->getQuery($condition, '*', '');
    }
	/*
    static function CalcuPoint(){  //未见使用......
        $calc_date = date("Y-m-d",time());
        $today_point = (new NsRptPointModel())->getInfo(['create_time' => $calc_date], "r25, r27" );
        if(!empty($today_point)) {
            return;
        }

        $account_records = new NsMemberAccountRecordsModel();
        $member_account = new NsMemberAccountModel();
        $shop_account_records = new NsShopAccountRecordsModel();
//        1号0点0分结余积分为A（上一日30号24点台帐实际积分结余  商户+用户）（不作计算，在上一天的记录中保存）
        //1号0点0分至24点0分实入帐积分为B（ 当天0点0分至24点0分新入账积分数 用户+商户）


            $C29 = $member_account->getSum(['uid'=>['>', 0]], 'point');
            $data = UniPoint::CalculatePoint($calc_date,
                $account_records->getSum([
                    'is_add'=>1,
                    'account_type'=>1,
                    'create_time'=>[[
                        '>=',
                        date('Y-m-d', strtotime("-1 days", strtotime($calc_date)))
                    ],
                        [
                            '<',
                            $calc_date
                        ]],
                    'number'=>[
                        '>',
                        0
                    ]
                ], 'number'),
                -1 * $account_records->getSum([
                    'is_add'=>1,
                    'account_type'=>1,
                    'create_time'=>[[
                        '>=',
                        date('Y-m-d', strtotime("-1 days", strtotime($calc_date)))
                    ],
                        [
                            '<',
                            $calc_date
                        ]],
                    'number'=>[
                        '<',
                        0
                    ]
                ], 'number'),
                $C29);
            //return -3;
            $today_point = (new NsRptPointModel())->getInfo(['create_time' => $calc_date], "r25, r27" );
        } */
}
