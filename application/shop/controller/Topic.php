<?php
/**
 * Member.php
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
namespace app\shop\controller;

use data\service\Article;

/**
 * 专题
 * 
 * @author Administrator
 *        
 */
class Topic extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 专题页
     */
    public function detail()
    {
        $article = new Article();
        $topic_id = request()->get('topic_id', '');
        $info = $article->getTopicDetail($topic_id);
        $this->assign('info', $info);
        return view($this->style . '/Topic/detail');
    }
}