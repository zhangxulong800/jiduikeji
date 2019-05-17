<?php
/**
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace app\shop\controller;

use app\wap\controller\Pay;

/**
 * 支付控制器
 *
 * @author Administrator
 *        
 */
class Pcpay extends Pay
{
    public function __construct()
    {
        parent::__construct();
		$this->style = 'shop/default';
    }
}