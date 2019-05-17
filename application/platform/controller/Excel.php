<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2018/11/13
 * Time: 11:53
 */

namespace app\platform\controller;
use think\Db;
class Excel
{
    public function out()
    {
        $path = dirname(__FILE__);//找到当前脚本所在位置
        vendor("PHPExcel");
        vendor("PHPExcel.Writer.Excel5");
        vendor("PHPExcel.Writer.Excel2007");
        vendor("PHPExcel.IOFactory");

        $objPHPExcel  = new \PHPExcel();
        //全局居中
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //填充颜色

        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $sql = Db::table('ns_goods')->where('goods_id', '>', 399)->where('state', 1)->field('goods_id, goods_name, goods_type, market_price, price, cost_price, cost_price_logistics, real_sales')->select();
        $goods_id = [];//商品id
        $goods_name = [];//商品名称
        $market_price = [];//JPLUS价
        $price = [];//零售价
        $cost_price = [];//结算价
        //成本之损耗价$loss ,物流价$logistics,税费价$Taxation,总成本$total_cost
                $loss = [];
                $logistics = [];
                $Taxation = [];
                $total_cost= [];
        //比价之淘宝价$taobao.拼多多价$pingduo
                $taobao  = [];
                $pingduo = [];
        //利差$Profit_margin
                $Profit_margin = [];
        //积分之客户$Customer,商户$Merchant
                $Customer = [];
                $Merchant = [];
        //二级分销$distribution
                $distribution = [];
        //利润$profit 二级分销分出总计$total_distribution
                $total_distribution = [];
                $profit = [];
//        print_r($sql);exit;
        //销量$sales
                $real_sales = [];

        //集采备付金
                $jicai = [];
        //通证备付金
                $tong = [];
        //平台现金流
                $ping = [];
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '商品名称')
            ->setCellValue('B1', '结算价(单位元）')
            ->setCellValue('C1', '售价（以J价为准%)')
            ->setCellValue('E1', '成本')
            ->setCellValue('K1', '比价')
            ->setCellValue('M1', '利差')
            ->setCellValue('N1', '积分')
            ->setCellValue('P1', '二级分销30%')
            ->setCellValue('Q1', '利润')
            ->setCellValue('R1', '销量')
            ->setCellValue('S1', '集采备付金')
            ->setCellValue('T1', '通证备付金')
            ->setCellValue('U1', '平台现金流');
        //设置列的宽度//内容自适应
        $objPHPExcel->getActiveSheet()->getColumnDimension( 'A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension( 'B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension( 'C:D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension( 'N')->setAutoSize(true);
        //合并单元格,值为''
        $objPHPExcel->getActiveSheet()->mergeCells("A1:A2");
        $objPHPExcel->getActiveSheet()->mergeCells("B1:B2");
        $objPHPExcel->getActiveSheet()->mergeCells("C1:D1");
        $objPHPExcel->getActiveSheet()->mergeCells("E1:J1");
        $objPHPExcel->getActiveSheet()->mergeCells("K1:L1");
        $objPHPExcel->getActiveSheet()->mergeCells("M1:M2");
        $objPHPExcel->getActiveSheet()->mergeCells("N1:O1");
        $objPHPExcel->getActiveSheet()->mergeCells("P1:P2");
        $objPHPExcel->getActiveSheet()->mergeCells("Q1:Q2");
        $objPHPExcel->getActiveSheet()->mergeCells("R1:R2");
        $objPHPExcel->getActiveSheet()->mergeCells("S1:S2");
        $objPHPExcel->getActiveSheet()->mergeCells("T1:T2");
        $objPHPExcel->getActiveSheet()->mergeCells("U1:U2");

//        设置表头信息
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('C2', '会员价')
            ->setCellValue('D2', 'Jpluss价')
            ->setCellValue('E2', '损耗5%')//暂时按5%算
            ->setCellValue('F2', '税费5%')//暂时按5%算
            ->setCellValue('G2', '财务成本1.2%')//暂时按5%算
            ->setCellValue('H2', '销售成本1%')//暂时按5%算
            ->setCellValue('I2', '物流')
            ->setCellValue('J2', '总成本')
            ->setCellValue('K2', '拼多多')
            ->setCellValue('L2', '淘宝')
            ->setCellValue('N2', '客户40%')
            ->setCellValue('O2', '商户9%');


        //把数据库提取信息插入Excel表中,$i = 2,因为第一行第二行是表头，所以写到表格时候只能从第三行开始写。
        $i=3;  //定义一个i变量，目的是在循环输出数据是控制行数
        $count = count($sql);  //计算有多少条数据

        for ($i = 3; $i <= $count+1; $i++) {

            $goods_name = $sql[$i - 3]['goods_name'];
            $goods_type = $sql[$i - 3]['goods_type'];
            $market_price = $sql[$i - 3]['market_price'];
            $price = $sql[$i - 3]['price'];
            $cost_price = $sql[$i - 3]['cost_price'];
            $logistics = $sql[$i - 3]['cost_price_logistics'];
            $Taxation = 0.05 * ($market_price-$cost_price);
            if($goods_type === 0){  //非实物商品才会有销售成本和财务成本
                $sale = $price*0.01;//销售成本,当前为销售价的1%
                $caiwu = $price*0.012;//财务成本,当前为销售价的1.2%
                $loss = 0;
            }else{
                $sale = 0;//销售成本,当前为销售价的1%,如果是事务商品时不产生销售成本
                $caiwu =0;
                $loss = 0.05 * $price;;
            }
            $total_cost = $loss + $Taxation + $cost_price + $logistics + $caiwu + $sale;
            $Profit_margin = $market_price - $total_cost;
            $Customer = 0.4 * $Profit_margin;
            $Merchant = 0.09 * $Profit_margin;
            $distribution = 0.3*$Profit_margin;
            $total_distribution = $Customer + $distribution + $Merchant;
            $profit = $Profit_margin - $total_distribution;
            $real_sales =  $sql[$i - 3]['real_sales'];
            $jicai = ($Merchant+$distribution)*0.3*$sales;
            $tong = ($Merchant+$distribution)*0.4*$sales;
            $ping = $sales*$profit+$jicai*2;


            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $goods_name);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $cost_price);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $price);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $market_price);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $loss);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $Taxation);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $caiwu);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $sale);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $logistics);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $total_cost);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, '');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, '');
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $Profit_margin);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $Customer);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $Merchant);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $distribution);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $i, $profit);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $i, $real_sales);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $i, $jicai);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $i, $tong);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $i, $ping);
        }
        //下面是设置其他信息

        $objPHPExcel->getActiveSheet()->setTitle('商品关键数据');
        //设置sheet的名称
        $objPHPExcel->setActiveSheetIndex(0);                   //设置sheet的起始位置
        //通过PHPExcel_IOFactory的写函数将上面数据写出来
        //$objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $PHPWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        //2007是xlsx    5是xls
        header('Content-Disposition: attachment;filename="商品详情表.xlsx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $PHPWriter->save("php://output"); //生成文件
    }
}