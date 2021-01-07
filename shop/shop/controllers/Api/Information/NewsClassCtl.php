<?php

if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author
 */
class Api_Information_NewsClassCtl extends Api_Controller
{
 
     public $newsClassModel = null;
    
    /**
     * 初始化方法，构造函数
     *
     * @access public
     */
    public function init()
    {
        $this->newsClassModel = new Information_NewsClassModel();
    }
    //
    /**
     * 获取资讯列表
     */
    public function newsclasslist()
    {
        $cond_row = [];
        $order =array('sort' => 'asc');          
        $data = $this->newsClassModel->listClassWhere($cond_row, $order);
        $this->data->addBody(-140, $data);
    }
    
    
    //
    
    /**
     * 获取资讯列表
     */
    public function newstypelist()
    {
        $data = $this->newsClassModel->typelist();
        $this->data->addBody(-140, $data);
    }
    
    /**
     * 添加资讯新闻
     */
    public function addNewsClassrows()
    {
        //获取接收过来的数据
        $data['newsclass_name'] = request_string("news_class_name");
        $data['sort'] = request_string("news_sort");
        $id = $this->newsClassModel->addClass($data,true); 
        $data['news_class_id'] = $id;
        if($id !== false){
            $status = 200;
            $msg = 'success';
        }else{
            $status = 250;
            $msg = 'failure';
        }
        $this->data->addBody(-140, $data,$msg,$status);
    }
    
    /**
     * 单独资讯分类页面
     *
     * @access public
     */
    public function editNewsClass()
    {
        $id = request_int('news_class_id');
        $data = $this->newsClassModel->getClass($id);
        $this->data->addBody(-140, $data);
    }
    
    /**
     * 修改资讯分类页面
     *
     * @access public
     */
    public function editNewsClassrow()
    {
        //获取接收过来的数据
        $id = request_int('news_class_id');
        $data['newsclass_name'] = request_row("newsclass_name");
        $data['sort'] = request_row("sort");
        $add = $this->newsClassModel->editClass($id, $data);
        $data['id'] = $id;
        $this->data->addBody(-140, $data);
    }
    
    /**
     *  删除资讯分类
     *
     * @access public
     */
    public function delNewsClass()
    {
        $id = request_int('news_class_id');
        $del = $this->newsClassModel->removeClass($id);
        $data['news_class_id'] = $id;
        $this->data->addBody(-140, $data);
    }
    
    public function newsClassGroup()
    {
        $order = ['sort' => 'asc'];
        $data = $this->newsClassModel->getByWhere([], $order);
        $data = array_values($data);
        $result = [];
        $result[0]['id'] = 0;
        $result[0]['newsclass_name'] = "资讯标签";
        foreach ($data as $key => $value) {
            $result[$key + 1]['id'] = $value['id'];
            $result[$key + 1]['newsclass_name'] = $value['newsclass_name'];
        }
        $this->data->addBody(-140, $result);
    }
}