<?php
/**
 * Article.php
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
use data\service\Article as ArticleService;


class Article extends BaseController
{
    function __construct(){
        parent::__construct();
        $this->service = new ArticleService();
    }
    
    /**
     * 添加文章列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     */
   public function getArticleList(){
       $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
       $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
       $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
       $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
       $retval = $this->service->getArticleList($page_index, $page_size, $condition, $order);
       if($retval){
           return $this->outMessage($retval);
       }else{
           return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
       }
   }
   /**
    * 获取文章详情
    * @param unknown $article_id
    */
   public function getArticleDetail(){
       $article_id = isset($this->request_common_array['article_id']) ? $this->request_common_array['article_id'] : '';
       $retval = $this->service->getArticleDetail($article_id);
       if($retval){
           return $this->outMessage($retval);
       }else{
           return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
       }
   }
   /**
    * 文章分类列表
    * @param number $page_index
    * @param number $page_size
    * @param string $condition
    * @param string $order
    */
   public function getArticleClass(){
       $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
       $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
       $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
       $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
       $retval = $this->service->getArticleClass($page_index, $page_size, $condition, $order);
       if($retval){
           return $this->outMessage($retval);
       }else{
           return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
       }
   }
   /**
    * 获取文章分类详情
    * @param unknown $class_id
    */
   public function getArticleClassDetail(){
       $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
       $retval = $this->service->getArticleClassDetail($class_id);
       if($retval){
           return $this->outMessage($retval);
       }else{
           return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
       }
   }
   /**
    * 添加文章
    * @param unknown $title
    * @param unknown $class_id
    * @param unknown $short_title
    * @param unknown $source
    * @param unknown $url
    * @param unknown $author
    * @param unknown $summary
    * @param unknown $content
    * @param unknown $image
    * @param unknown $keyword
    * @param unknown $article_id_array
    * @param unknown $click
    * @param unknown $sort
    * @param unknown $commend_flag
    * @param unknown $comment_flag
    * @param unknown $status
    * @param unknown $attachment_path
    * @param unknown $tag
    * @param unknown $comment_count
    * @param unknown $share_count
    */
   public function addArticle(){
       $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
       $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
       $short_title = isset($this->request_common_array['short_title']) ? $this->request_common_array['short_title'] : '';
       $source = isset($this->request_common_array['source']) ? $this->request_common_array['source'] : '';
       $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
       $author = isset($this->request_common_array['author']) ? $this->request_common_array['author'] : '';
       $summary = isset($this->request_common_array['summary']) ? $this->request_common_array['summary'] : '';
       $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
       $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
       $keyword = isset($this->request_common_array['keyword']) ? $this->request_common_array['keyword'] : '';
       $article_id_array = isset($this->request_common_array['article_id_array']) ? $this->request_common_array['article_id_array'] : '';
       $click = isset($this->request_common_array['click']) ? $this->request_common_array['click'] : '';
       $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
       $commend_flag = isset($this->request_common_array['commend_flag']) ? $this->request_common_array['commend_flag'] : '';
       $comment_flag = isset($this->request_common_array['comment_flag']) ? $this->request_common_array['comment_flag'] : '';
       $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
       $attachment_path = isset($this->request_common_array['attachment_path']) ? $this->request_common_array['attachment_path'] : '';
       $tag = isset($this->request_common_array['tag']) ? $this->request_common_array['tag'] : '';
       $comment_count = isset($this->request_common_array['comment_count']) ? $this->request_common_array['comment_count'] : '';
       $share_count = isset($this->request_common_array['share_count']) ? $this->request_common_array['share_count'] : '';
       $res = $this->service->addArticle($title, $class_id, $short_title, $source, $url, $author, $summary, $content, $image, $keyword, $article_id_array, $click, $sort, $commend_flag, $comment_flag, $status, $attachment_path, $tag, $comment_count, $share_count);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   /**
    * 修改文章
    * @param unknown $article_id
    * @param unknown $title
    * @param unknown $class_id
    * @param unknown $short_title
    * @param unknown $source
    * @param unknown $url
    * @param unknown $author
    * @param unknown $summary
    * @param unknown $content
    * @param unknown $image
    * @param unknown $keyword
    * @param unknown $article_id_array
    * @param unknown $click
    * @param unknown $sort
    * @param unknown $commend_flag
    * @param unknown $comment_flag
    * @param unknown $status
    * @param unknown $attachment_path
    * @param unknown $tag
    * @param unknown $comment_count
    * @param unknown $share_count
    */
   public function updateArticle(){
       $article_id = isset($this->request_common_array['article_id']) ? $this->request_common_array['article_id'] : '';
       $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
       $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
       $short_title = isset($this->request_common_array['short_title']) ? $this->request_common_array['short_title'] : '';
       $source = isset($this->request_common_array['source']) ? $this->request_common_array['source'] : '';
       $url = isset($this->request_common_array['url']) ? $this->request_common_array['url'] : '';
       $author = isset($this->request_common_array['author']) ? $this->request_common_array['author'] : '';
       $summary = isset($this->request_common_array['summary']) ? $this->request_common_array['summary'] : '';
       $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
       $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
       $keyword = isset($this->request_common_array['keyword']) ? $this->request_common_array['keyword'] : '';
       $article_id_array = isset($this->request_common_array['article_id_array']) ? $this->request_common_array['article_id_array'] : '';
       $click = isset($this->request_common_array['click']) ? $this->request_common_array['click'] : '';
       $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
       $commend_flag = isset($this->request_common_array['commend_flag']) ? $this->request_common_array['commend_flag'] : '';
       $comment_flag = isset($this->request_common_array['comment_flag']) ? $this->request_common_array['comment_flag'] : '';
       $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
       $attachment_path = isset($this->request_common_array['attachment_path']) ? $this->request_common_array['attachment_path'] : '';
       $tag = isset($this->request_common_array['tag']) ? $this->request_common_array['tag'] : '';
       $comment_count = isset($this->request_common_array['comment_count']) ? $this->request_common_array['comment_count'] : '';
       $share_count = isset($this->request_common_array['share_count']) ? $this->request_common_array['share_count'] : '';
       $res = $this->service->updateArticle($article_id, $title, $class_id, $short_title, $source, $url, $author, $summary, $content, $image, $keyword, $article_id_array, $click, $sort, $commend_flag, $comment_flag, $status, $attachment_path, $tag, $comment_count, $share_count);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   /**
    * 添加文章分类
    * @param unknown $name
    * @param unknown $sort
    */
   public function addAritcleClass(){
       $name = isset($this->request_common_array['name']) ? $this->request_common_array['name'] : '';
       $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
       $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
       $res = $this->service->addAritcleClass($name, $sort, $pid);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   /**
    * 修改文章分类
    * @param unknown $class_id
    * @param unknown $name
    * @param unknown $sort
    */
   public function updateArticleClass(){
       $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
       $name = isset($this->request_common_array['name']) ? $this->request_common_array['name'] : '';
       $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
       $pid = isset($this->request_common_array['pid']) ? $this->request_common_array['pid'] : '';
       $res = $this->service->updateArticleClass($class_id, $name, $sort, $pid);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   /**
    * 修改文章排序
    * @param unknown $article_id
    * @param unknown $sort
    */
   public function modifyArticleSort(){
       $article_id = isset($this->request_common_array['article_id']) ? $this->request_common_array['article_id'] : '';
       $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
       $res = $this->service->modifyArticleSort($article_id, $sort);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   /**
    * 修改文章分类排序
    * @param unknown $class_id
    * @param unknown $sort
    */
   public function modifyArticleClassSort(){
       $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
       $sort = isset($this->request_common_array['sort']) ? $this->request_common_array['sort'] : '';
       $res = $this->service->modifyArticleClassSort($class_id, $sort);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   /**
    * 删除文章分类（如果文章分类已被使用那就不可删除）
    * @param unknown $class_id
    */
   public function deleteArticleClass(){
       $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
       $res = $this->service->deleteArticleClass($class_id);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   
   /**
    * 删除文章
    * @param unknown $article_id
    */
   public function deleteArticle(){
       $article_id = isset($this->request_common_array['article_id']) ? $this->request_common_array['article_id'] : '';
       $res = $this->service->deleteArticle($article_id);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   
   /**
    *  文章分类使用次数
    * @param unknown $class_id
    */
   public function articleClassUseCount(){
       $class_id = isset($this->request_common_array['class_id']) ? $this->request_common_array['class_id'] : '';
       $res = $this->service->articleClassUseCount($class_id);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   
   /**
    * 文章评论列表
    * @param number $page_index
    * @param number $page_size
    * @param string $condition
    * @param string $order
    */
   public function getCommentList(){
       $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
       $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
       $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
       $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
       $retval = $this->service->getCommentList($page_index, $page_size, $condition, $order);
       if($retval){
           return $this->outMessage($retval);
       }else{
           return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
       }
   }
   
   /**
    * 查看评论详情
    * @param unknown $comment_id
    */
   public function getCommentDetail(){
       $comment_id = isset($this->request_common_array['comment_id']) ? $this->request_common_array['comment_id'] : '';
       $retval = $this->service->getCommentDetail($comment_id);
       if($retval){
           return $this->outMessage($retval);
       }else{
           return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
       }
   }
   
  /**
   * 删除评论
   * @param unknown $comment_id
   */
   public function deleteComment(){
       $comment_id = isset($this->request_common_array['comment_id']) ? $this->request_common_array['comment_id'] : '';
       $retval = $this->service->deleteComment($comment_id);
       if($retval){
           return $this->outMessage($retval);
       }else{
           return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
       }
   }
   /**
    * 获取cms分类
    */
   public function getArticleClassQuery(){
       $retval = $this->service->getArticleClassQuery();
       if($retval){
           return $this->outMessage($retval);
       }else{
           return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
       }
   }
   /**
    * 添加专题
    * @param unknown $instance_id
    * @param unknown $title
    * @param unknown $image
    * @param unknown $content
    */
   public public function addTopic(){
       $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
       $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
       $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
       $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
       $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
       $res = $this->service->addTopic($instance_id, $title, $image, $content, $status);
       $retval = AjaxReturn($res);
       if($retval['code'] > 0){
           return $this->outMessage($retval['code']);
       }else{
           return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
       }
   }
   /**
    * 专题列表
    * @param number $page_index
    * @param number $page_size
    * @param string $condition
    * @param string $order
    */
     public function getTopicList(){
         $page_index = isset($this->request_common_array['page_index']) ? $this->request_common_array['page_index'] : 1;
         $page_size = isset($this->request_common_array['page_size']) ? $this->request_common_array['page_size'] : 0;
         $condition = isset($this->request_common_array['condition']) ? $this->request_common_array['condition'] : '';
         $order = isset($this->request_common_array['order']) ? $this->request_common_array['order'] : '';
         $field = isset($this->request_common_array['field']) ? $this->request_common_array['field'] : '*';
         $retval = $this->service->getTopicList($page_index, $page_size, $condition, $order, $field);
         if($retval){
             return $this->outMessage($retval);
         }else{
             return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
         }
     }
     /**
      * 获取专题详情
      * @param unknown $topic_id
      */
     public function getTopicDetail(){
         $topic_id = isset($this->request_common_array['topic_id']) ? $this->request_common_array['topic_id'] : '*';
         $retval = $this->service->getTopicDetail($topic_id);
         if($retval){
             return $this->outMessage($retval);
         }else{
             return $this->outMessage($retval, ERROR_CODE, ERROR_MESSAGE);
         }
     }
     /**
      * 修改专题
      * @param unknown $instance_id
      * @param unknown $topic_id
      * @param unknown $title
      * @param unknown $image
      * @param unknown $content
      * @param unknown $status
      */
     public function  updateTopic(){
         $instance_id = isset($this->request_common_array['instance_id']) ? $this->request_common_array['instance_id'] : '';
         $topic_id = isset($this->request_common_array['topic_id']) ? $this->request_common_array['topic_id'] : '';
         $title = isset($this->request_common_array['title']) ? $this->request_common_array['title'] : '';
         $image = isset($this->request_common_array['image']) ? $this->request_common_array['image'] : '';
         $content = isset($this->request_common_array['content']) ? $this->request_common_array['content'] : '';
         $status = isset($this->request_common_array['status']) ? $this->request_common_array['status'] : '';
         $res = $this->service->updateTopic($instance_id, $topic_id, $title, $image, $content, $status);
         $retval = AjaxReturn($res);
         if($retval['code'] > 0){
             return $this->outMessage($retval['code']);
         }else{
             return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
         }
     }
     /**
      * 删除专题
      * @param unknown $topic_id
      */
     public function  deleteTopic(){
         $topic_id = isset($this->request_common_array['topic_id']) ? $this->request_common_array['topic_id'] : '';
         $res = $this->service->deleteTopic($topic_id);
         $retval = AjaxReturn($res);
         if($retval['code'] > 0){
             return $this->outMessage($retval['code']);
         }else{
             return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
         }
     }
     /**
      * 修改单个字符
      * @param unknown $fieldid
      * @param unknown $fieldname
      * @param unknown $fieldvalue
      */
     public public function cmsField(){
         $fieldid = isset($this->request_common_array['fieldid']) ? $this->request_common_array['fieldid'] : '';
         $fieldname = isset($this->request_common_array['fieldname']) ? $this->request_common_array['fieldname'] : '';
         $fieldvalue = isset($this->request_common_array['fieldvalue']) ? $this->request_common_array['fieldvalue'] : '';
         $res = $this->service->cmsField($fieldid, $fieldname, $fieldvalue);
         $retval = AjaxReturn($res);
         if($retval['code'] > 0){
             return $this->outMessage($retval['code']);
         }else{
             return $this->outMessage($retval['code'], ERROR_CODE, $retval['message']);
         }
     }
}