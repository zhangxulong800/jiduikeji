<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019/5/11
 * Time: 22:03
 */


namespace app\wap\controller;
class Test extends BaseController {
    public function index()
    {
        \think\Db::table("sys_config")->insert(array(
            'instance_id'=>0,
            'key'=>'test_key',
            'value'=>'test_value',
            'create_time'=>date('Y-m-d H;i:s'),
            'modify_time'=>date('Y-m-d H;i:s'),
            'desc'=>'测试插入',
            'is_use'=>0
        ));
    }
}