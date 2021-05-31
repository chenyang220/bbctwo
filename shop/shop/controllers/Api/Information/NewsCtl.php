<?php
/**
 * Created by PhpStorm.
 * Author: JangCheng
 * Date: 2018年09月06日
 * Time: 14:59:42
 */

if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author
 */
class Api_Information_NewsCtl extends Api_Controller
{
    public $Information = null;
    public $InformationNewsModel = null;
    public $NewsClassModel = null;
    
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }
    /**
     * 初始化方法，构造函数
     *
     * @access public
     */
    public function init()
    {
        $this->Information = new Information_Base();
        $this->InformationNewsModel = new Information_NewsModel();
        $this->NewsClassModel = new Information_NewsClassModel();
        $this->shopBaseModel = new Shop_BaseModel();
        $this->userBaseModel = new User_BaseModel();
    }
    
    /*
	 * 获取文章列表
	 *
	 * @param int $page 页数
	 * @param int $rows 每页显示行数
	 *
	 * @return data 文章显示数据
	 */
    public function informationNewsList()
    {
        $page = request_int('page',1);
        $rows = request_int('rows',10);
        $cond_row = [];
        $author_type = request_int('article_group');
        $news_title = request_string('news_title');
        $status=request_int('status');
        $auditing=request_int('auditing');
        $complaint = request_int('complaint');
        $shopname= request_string('shopname');
        
        if($shopname){
            $shop_row['shop_name:LIKE'] = '%' . $shopname . '%';
            $shopBase = $this->shopBaseModel->getOneByWhere($shop_row);
            $cond_row['author_id'] = $shopBase['user_id'];
        }
        
        $cond_row['status']= $status;
        $cond_row['auditing'] = $auditing;
        if ($author_type) {
            $cond_row['author_type'] = $author_type;
        }
        if ($news_title) {
            $cond_row['title:LIKE'] ='%'.$news_title.'%';
        }
        if ($complaint) {
            $cond_row['complaint'] = $complaint;
        }
        
        $order_row = [
            'create_time' => 'DESC',
        ];
        
        $items = $this->InformationNewsModel->listByWhere($cond_row, $order_row, $page, $rows);
        foreach($items['items'] as $key=>$vul){
            if($vul['author_type']==3){
                $items['items'][$key]['authorname']='平台';
                $items['items'][$key]['author_name'] = '平台';
                
            }elseif($vul['author_type']==2){
                $shopdata = $this->shopBaseModel->getOneByWhere(['user_id' => $vul['author_id']]);
                $items['items'][$key]['authorname'] = $shopdata['shop_name'];
                $items['items'][$key]['author_name']='店铺';
            }else{
                $userdata = $this->userBaseModel->getUserInfo(['user_id' => $vul['author_id']]);
                $items['items'][$key]['authorname'] = $userdata['user_account'];
                $items['items'][$key]['author_name']='用户';
            }
        }
        if ($items) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $data['items'] = $items;//
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    /**
     * 首页
     *
     * @access public
     */
    public function index()
    {
        // $order = array('shop_class_displayorder' => 'asc');
        // $data  = $this->shopClassModel->listClassWhere(array(), $order);
        $this->data->addBody(-140, $data);
    }
    
    /**
     * 添加资讯新闻
     */
    public function addInformationNews()
    {
        $data = [];
        $data['author_id'] = request_int('user_id');
        $data['title'] = request_string('title');
        $data['subtitle'] = request_string('subtitle');
        $data['content'] = request_string('content');
        $data['type'] = request_int('newsclass_id');
        $data['author_type'] = request_int('author_type');
        $data['auditing'] = 1;//平台不用审核
        $data['create_time'] = date('Y-m-d H:i:s', time());
        $news_id = $this->InformationNewsModel->addBase($data, true);
        if ($news_id) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
        
    }
    
    /**
     * 修改资讯新闻
     */
    public function editInformationNews()
    {
        $news_id = request_int('news_id');
        $data['title'] = request_string('title');
        $data['subtitle'] = request_string('subtitle');
        $data['content'] = request_string('content');
        $data['type'] = request_int('newsclass_id');
        $data['author_type'] = request_int('author_type');
        $data['create_time'] = date('Y-m-d H:i:s', time());
        
        $flag = $this->InformationNewsModel->editBase($news_id, $data);
        if ($flag !== false) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    /*
	 * 删除文章
	 *
	 * @param int article_id 文章id
	 *
	 * @return data 操作记录id
	 */
    public function removeBase()
    {
        $id = request_int('article_id');
        if ($id) {
            $flag = $this->InformationNewsModel->removeBase($id);
        }
        if ($flag) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $data['id'] = $id;
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    /*
	 * 审核资讯新闻
	 *
	 * @param int article_id 文章id
	 *
	 * @return data 操作记录id
	 */
    function check()
    {
        $ids = request_string('id');
        $id_rows = explode(',', $ids);
        if (!empty($id_rows)) {
            foreach ($id_rows as $key => $value) {
                $news_id = $value;
                $edit_row = [];
                $edit_row['auditing'] = 1;
                $edit_row['create_time'] = date('Y-m-d H:i:s', time());
                $flag = $this->InformationNewsModel->editBase($news_id, $edit_row);
            }
        }
        $this->data->addBody(-140, $id_rows);
    }
    
    /*
	 * 审核资讯新闻
	 *
	 * @param int article_id 文章id
	 *
	 * @return data 操作记录id
	 */
    function notcheck()
    {
        $ids = request_string('id');
        $id_rows = explode(',', $ids);
        if (!empty($id_rows)) {
            foreach ($id_rows as $key => $value) {
                $news_id = $value;
                $edit_row = [];
                $edit_row['auditing'] = 2;
                $edit_row['create_time'] = date('Y-m-d H:i:s',time());
                $flag = $this->InformationNewsModel->editBase($news_id, $edit_row);
            }
        }
        $this->data->addBody(-140, $id_rows);
    }
    
    /*
	 * 投诉资讯新闻
	 *
	 * @param int article_id 文章id
	 *
	 * @return data 操作记录id
	 */
    function complaint()
    {
        $ids = request_string('id');
        $id_rows = explode(',', $ids);
        if (!empty($id_rows)) {
            foreach ($id_rows as $key => $value) {
                $news_id = $value;
                $edit_row = [];
                $edit_row['complaint'] = 3;
                $edit_row['create_time'] = date('Y:m:d H:i:s', time());
                $flag = $this->InformationNewsModel->editBase($news_id, $edit_row);
            }
        }
        $this->data->addBody(-140, $id_rows);
    }
    
    /*
    * 投诉资讯新闻
    *
    * @param int article_id 文章id
    *
    * @return data 操作记录id
    */
    function notcomplaint()
    {
        $ids = request_string('id');
        $id_rows = explode(',', $ids);
        if (!empty($id_rows)) {
            foreach ($id_rows as $key => $value) {
                $news_id = $value;
                $edit_row = [];
                $edit_row['complaint'] = 1;
                $flag = $this->InformationNewsModel->editBase($news_id, $edit_row);
            }
        }
        $this->data->addBody(-140, $id_rows);
    }
    
    
    
    /**
     * 获取资讯详情
     *
     * @access public
     */
    public function newsdetailslist()
    {
        $news_id = request_int('news_id');
        $data = $this->InformationNewsModel->getOneByWhere(array('id'=>$news_id));
        $data['content'] = str_replace('type="application/x-shockwave-flash"', ' ', $data['content']);
        if ($data['author_type'] == 3) {
            $data['authorname'] = '平台';
            $data['author_name'] = '平台';
        } elseif ($data['author_type'] == 2) {
            $shopdata = $this->shopBaseModel->getOneByWhere(['user_id' => $data['author_id']]);
            $data['authorname'] = $shopdata['shop_name'];
            $data['author_name'] = '店铺';
        } else {
            $userdata = $this->userBaseModel->getUserInfo(['user_id' => $data['author_id']]);
            $data['authorname'] = $userdata['user_account'];
            $data['author_name'] = '用户';
        }
        
        $newsclassname = $this->NewsClassModel->getOneByWhere(array('id'=>$data['type']));
        $data['newsclass_name']= $newsclassname['newsclass_name'];
        $this->data->addBody(-140, $data);
    }
    
}