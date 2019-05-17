<?php
namespace data\service;
use app\common\controller\Common;
use think\Db;
use think\Session;
use think\cache;


class JSSDK {
    //   private $redirect_uri;

    public function _initialize(){

        parent::_initialize();


    }

    /* JSSDK */
    public function index($url) {
        $info  = Db::name('sys_config')->where('key', 'WPAY')->field('value')->find();
        $infos = json_decode($info['value'],true);

        $appid    = $infos['appid'];
        $secret   = $infos['appkey'];
//        $url = $url?:'http://'.$_SERVER['HTTP_HOST'].$_SERVER['QUERY_STRING'];
//         var_dump($url);exit;
        //生成JSSDK签名
        if (!cache('jsapi_ticket')) {
            $this->getJsApi($appid,$secret);
        }
        $noncestr = $this->getRandChar(18);
        $jsapi_ticket = cache('jsapi_ticket');

        $timestamp = time();
        $strSign = sha1("jsapi_ticket=$jsapi_ticket&noncestr=$noncestr&timestamp=$timestamp&url=$url");
        $arr = array(
            'appId' => $appid,
            'sign' => $strSign,
            'timestamp' => $timestamp,
            'noncestr' => $noncestr,
        );
       // print_r($arr);exit;
        return $arr;
    }

    public function getJsApi($appid,$secret) {
        if (!cache('p_access_token')) {
            $this->getAccessToken_p($appid,$secret);
        }
        $access_token = cache('p_access_token');
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$access_token&type=jsapi";
        $output = $this->http_curl_get($url);
        $output = json_decode($output, true);

        if ($output['errmsg'] == 'ok') {
            cache('jsapi_ticket', $output['ticket'],7190);
        } else {
            echo "jsapi_ticket异常";
        }
    }
    //获取普通access
    public function getAccessToken_p($appid,$secret) {

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
        $output = $this->http_curl_get($url);
        $output = json_decode($output, true);

        if (!empty($output['access_token'])) {
            cache('p_access_token', $output['access_token'],7190);
        } else {
            echo "access_token异常";
        }
    }
    private function http_curl_get($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    private function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }
}

