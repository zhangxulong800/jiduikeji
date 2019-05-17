<?php
/**
 * Address.php
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
namespace app\api\controller;


use think\Controller;
class Index extends BaseController
{
    function __construct(){
        parent::__construct();
    }

    public function index(){
//         $arr = array(
//             'page_index' => 1,
//             'page_size' => 5,
//             'condition' => '',
//             'order' => '',
//             'field' => 'uid',
//         );
//         var_dump($arr);
//         var_dump(json_encode($arr));
//         var_dump(json_decode($this->request_common_param));
        
//         return $this->request_common_param;
        
//         die;
        //#访问页面时的请求数据。例如：“GET”、“HEAD”，“POST”，“PUT”。
        if (!$this->checkParam()){
            return $this->outMessage(0, ERROR_CODE, "缺少系统参数!");
        }
        $this->checkLogin();
        
        //确认链接时否合法
//         $check_url_res = $this->checkUrl($paramsArray);
//         if($check_url_res === -1){
//             return $this->outMessage(0, ERROR_CODE, "当前链接不合法!");
//         }else if($check_url_res === -2){
//             return $this->outMessage(0, ERROR_CODE, "接口调用超时!");
//         }
        $controller = $this->request_controller;
        $method = $this->request_method;
        $class = "app\api\controller\\$controller";
        $obj = new $class();
        $result = $obj->$method ();
        return $result;
    }
    
    public function checkLogin(){
        $token = isset($this->request_common_array['token']) ? $this->request_common_array['token'] : '';
        $key = isset($this->request_common_array['key']) ? $this->request_common_array['key'] : '';
        if($token == ''){
            return false;
        }else{
            $data_json = niuDecrypt($token, $key);
            $data_array = json_decode($data_json, true);
            if($data_array){
                \think\Session::set('uid', $data_array['uid']);
                \think\Session::set('instance_id', $data_array['instance_id']);
                \think\Session::set('instance_name', $data_array['instance_name']);
            }
            return false;
        }
    }
    
    /**
     * 验证url是否合法
     * @param unknown $params_array
     */
    public function checkUrl($params_array){
        $start_date = $this->request_timestamp;
        $end_date = time();
        $timediff = $end_date - $start_date;
        $mins = intval($timediff/60);
        $client_sign = $this->request_sign;
        unset($this->request_array["sign"]);
        $sign = $this->generateSign($this->request_array);
        if($mins > 1){
            return -2;
        }
        if(strtoupper($client_sign) != $sign){
            return -1;
        }
        return true;
    }
    
    /**
     * 确认系统参数是否正确
     */
    public function checkParam(){
//         if(empty($this->request_sign) || empty($this->request_appkey) || empty($this->request_controller) || empty($this->request_method) || empty($this->request_timestamp)){
//             return false;
//         }
        return true;
    }
    
    /**
     * 生成签名字符串
     * @param unknown $params
     */
    private function generateSign($params)
    {
        ksort($params);
        $sign = $this->appsecret;
        foreach ($params as $k => $v)
        {
            if("@" != substr($v, 0, 1))
            {
                $sign .= "$k$v";
            }
        }
        unset($k, $v);
        $sign .= $this->appsecret;
        return strtoupper(md5($sign));
    }
}