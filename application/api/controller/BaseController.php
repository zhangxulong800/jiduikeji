<?php
/**
 * BaseController.php
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

\think\Loader::addNamespace('data', 'data/');
use think\Controller;
define("RETUEN_KEY", 'niu_reponse');
define("ERROR_CODE", '-50');
define("ERROR_MESSAGE", '失败');
class BaseController extends Controller
{

    public $api_result;
    
    public $service;
    
    public $request_array;          //请求总参数
    
    public $request_controller;     //请求控制器
    
    public $request_method;         //请求方法名
    
    public $request_common_param;   //请求业务数据（json）
    
    public $request_sign;           //签名串
    
    public $request_timestamp;      //时间戳
    
    public $request_appkey;         //请求接口key
    
    public $appsecret;              //双方协定秘钥
    
    public $request_common_array;         //业务数据参数（数组）
    /**
     * 当前版本的路径
     *
     * @var string
     */

    public function __construct()
    {
        parent::__construct();
        $this->api_result = new ApiResult();
        $this->request_array = $_REQUEST;
        $this->request_controller = $this->request_array['controller'];
        $this->request_method = $this->request_array['method'];
        $this->request_common_param = $this->request_array['common_param'];
        $this->request_common_array = json_decode($this->request_common_param, true);
//         $this->request_sign = $this->request_array['sign'];
//         $this->request_timestamp = $this->request_array['timestamp'];
//         $this->request_appkey = $this->request_array['appkey'];
//         $this->appsecret = '积兑bbc';
        $this->init();
        
    }
    /**
     * 创建时间：2016-10-27
     * 功能说明：action基类 调用 加载头部数据的方法
     */
    public function init()
    {  
        
    }

    /**
     * 返回信息
     * @param unknown $res
     * @return \think\response\Json
     */
    public function outMessage($data, $code = 0, $message = "success"){
        $this->api_result->code = $code;
        $this->api_result->data = array(RETUEN_KEY=>$data);
        $this->api_result->message = $message;
        
        if ($this->api_result) {
            return json($this->api_result);
        } else {
            abort(404);
        }
    }
    
    /**
     * 加密解密
     * @param unknown $string
     * @param unknown $operation
     * @param string $key
     * @return string
     */
    function encrypt($string,$operation,$key=''){
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++){
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++){
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++){
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D'){
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
                return substr($result,8);
            }else{
                return'';
            }
        }else{
            return str_replace('=','',base64_encode($result));
        }
    }
}

class ApiResult
{

    public $code;

    public $message;

    public $data;

    public function __construct()
    {
        $this->code = 0;
        $this->message = "success";
        $this->data = null;
    }

}