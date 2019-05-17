<?php
/**
 * Index.php
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
use \think\Session as Session;
use data\model\UserModel;
use data\service\User;
use data\extend\WchatOauth;
use data\service\Member;
use data\model\UserLogModel;
use think\Db;
use think\Cache;
use app\SimpleController;
/**
 * 后台主界面
 * 
 * @author Administrator
 *        
 */
class Login extends SimpleController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //获取信息
        $user_name = !empty($_POST['user_name'])? $_POST['user_name'] :'';
        $password = !empty($_POST['password'])? $_POST['password']:'';
        //处理信息
        $user = new User();
        $res = array();
        $res['data'] = $user->login($user_name, $password); 
        $condition = array(
            'user_name' => $user_name,
            'user_password' => md5($password)
        );
        $users = new UserModel();   
        $member = $users->getInfo($condition,$field = 'uid,user_name,user_tel');
        $res['uid'] = $member['uid'];
        
        //返回信息
        if($res['data'] == 1){
            $member_info = (object)array();
            $member_info->uid = $member['uid'];
			$user_name=empty($member['user_name'])?'newshop':$member['user_name'];
			$user_tel=empty($member['user_tel'])?rand(10000000000,99999999999):$member['user_tel'];
			$date=date('Y-m-d H:i:s',time());
            $token='uid'.$member['uid'].'_'.time().'_'.md5($user_name.$user_tel.$date);
			$state=Db::table('ns_member_account')->where('uid',$member['uid'])->update(['token'=>$token]);
			if($state){
				Cache::set('uid'.$member['uid'],$token);//口令按会员ID缓存
			}
			$result=['uid'=>$res['uid'],'token'=>$token];
		} else {
			$result=['message'=>'用户名或密码错误！','code'=>-50];
		}
        return $this->outMessage($result);
    }  
    /**
     * 检测微信浏览器并且自动登录
     */
    public function wchatLogin()
    { 
        $infoapp['nickname'] = !empty($_POST['nickname'])  ? $_POST['nickname'] : '\U4e00\U5305\U8fa3\U6761' ;
        $infoapp['headimgurl'] = !empty($_POST['headimgurl  '])  ? $_POST['headimgurl'] : 'http://wx.qlogo.cn/mmopen/VIic1bE8bcVUyH2AyuqtzAXqibmdJTdfRnzchyhGe6RTeWt8sny9A0skeCFABu9kt2Ufwicj0LcBpomKbOYMTjersibakAm4bicbv/0' ;
        $info = json_encode($infoapp);
        $token['openid'] = !empty($_POST['openid'])  ? $_POST['openid'] : 'o_MOR1UTYwCMUEGURdC27a6ILrSk' ;
        $token['unionid'] = ! empty($_POST['unionid']) ? $_POST['unionid'] : 'oBNDo1GK-wQYDCuTO0Rzo-f_q2QY';      
            $wchat_oauth = new WchatOauth();
            if (! empty($token['openid'])) {
                if (! empty($token['unionid'])) {
                    $wx_unionid = $token['unionid'];
                    $user = new User();
                    $retval = $user->wchatUnionLogin($wx_unionid);
                    if ($retval == 1) {
                        $user = new User();
                        $user->modifyUserWxhatLogin($token['openid'], $wx_unionid);
                        $condition = array(
                            'wx_unionid' => $token['unionid']
                        );
                        $userModel = new UserModel();
                        $user_info = $userModel->getInfo($condition, $field = 'uid,user_status,user_name,is_system,instance_id,is_member, current_login_ip, current_login_time, current_login_type'); 
                        dump($user_info);
                        //return $this->outMessage('niu_index_response', $retval,'','获取成功');
                    } elseif ($retval == USER_LOCK) {
                        //用户被锁定
                       return $this->outMessage('niu_index_response', $retval, -50, '用户被锁定！');
                    } else {
                        $user = new User();
                        $retval = $user->wchatLogin($token['openid']);
                        if ($retval == USER_NBUND) {
                            $member = new Member();
                            $result = $member->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid); 
                            return $this->outMessage('niu_index_response', $result,'','成功！');
                        } elseif ($retval == USER_LOCK) {
                            // 锁定跳转
                           return $this->outMessage('niu_index_response', $retval, -50, '用户被锁定！');
                        }
                    }
                } else {
                    $wx_unionid = '';
                     $user = new User();
                     $retval = $user->wchatLogin($token['openid']);
                    if ($retval == USER_NBUND) {
                        $member = new Member();
                        $result = $member->registerMember('', '123456', '', '', '', '', $token['openid'], $info, $wx_unionid);
                        return $this->outMessage('niu_index_response', $result,'','成功！');
                    } elseif ($retval == USER_LOCK) {
                        // 返回锁定信息
                        return $this->outMessage('niu_index_response', $retval, -50, '用户被锁定！');
                    }
                }
            }
        }
    }
