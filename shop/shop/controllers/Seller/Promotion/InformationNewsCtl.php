<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

class Seller_Promotion_InformationNewsCtl extends Seller_Controller
{

    /**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     * @access public
     */
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        
        $this->Information  = new Information_Base();
        $this->InformationNewsModel   = new Information_NewsModel();
        $this->InformationNewsClassModel = new Information_NewsClassModel();
    }


    /**
     * 首页
     *活动列表
     * @access public
     */
    public function index()
    {
        $combo_row = [];
        //分页
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows'):10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $cond_row['author_id'] = Perm::$userId;
        $cond_row['author_type'] = 2;
        if (request_string('op') == 'detail' && request_int('id')) {
            $id= request_int('id');
            $data = $this->newsdetail($id);
            $this->view->setMet('detail');
        }else if(request_string('op') == 'editdetail' && request_int('id')){
            $id = request_int('id');
            $classData = $this->InformationNewsClassModel->getByWhere([]);
            $data = $this->newsdetail($id);
            $this->view->setMet('edit');
        }else{
            if (request_string('auditing') !== '') {
                $cond_row['auditing'] = request_string('auditing');
            }
            if (request_string('complaint') !== '') {
                $cond_row['complaint'] = request_string('complaint');
            }
            if (request_string('keyword') !== '') {
                $cond_row['title:LIKE'] = request_string('keyword') . '%';
            }
            $data = $this->InformationNewsModel->getBaseAllList($cond_row, ['id' => 'DESC'], $page, $rows);
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav = $Yf_Page->prompt();
        }
       
        if ('json' == $this->typ) {
            $json_data['data'] = $data;
            $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }
    }
    
    //添加活动
    public function add()
    {
        $classData = $this->InformationNewsClassModel->getByWhere([]);
        include $this->view->getView();
    }
    
    public function addInformationNews()
    {

        $author_id = Perm::$userId;
        $title = request_string('news_title');
        $subtitle =request_string('news_subtitle');
        $type = request_string('newsclass_type');
        $content = request_string('content');
        $created_time = date('Y-m-d H:i:s', time());
        $author_type =2;
        
        if (!$title) {
            return $this->data->addBody(-140, [], __('请填写资讯标题！'), 250);
        }
        if (!$type) {
            return $this->data->addBody(-140, [], __('请填写资讯标签'), 250);
        }
        if (!$content) {
            return $this->data->addBody(-140, [], __('请填写资讯内容'), 250);
        }
        
        $addData = [
           'author_id' => $author_id,
           'title' => $title,
           'subtitle' => $subtitle,
           'type' => $type,
           'content' => $content,
           'create_time' => $created_time,
           'author_type' => $author_type,
        ];
        $news_id = $this->InformationNewsModel->addBase($addData, true);
        if ($news_id) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, [], $msg, $status);
    }
    
    public function editInformationNews()
    {
        $author_id = Perm::$userId;
        $news_id=request_string('id');
        $title = request_string('news_title');
        $subtitle = request_string('news_subtitle');
        $type = request_string('newsclass_type');
        $content = request_string('content');
        if (!$title) {
            return $this->data->addBody(-140, [], __('请填写资讯标题！'), 250);
        }
        if (!$type) {
            return $this->data->addBody(-140, [], __('请填写资讯标签'), 250);
        }
        if (!$content) {
            return $this->data->addBody(-140, [], __('请填写资讯内容'), 250);
        }
        $editData = [
            'author_id' => $author_id,
            'title' => $title,
            'subtitle' => $subtitle,
            'type' => $type,
            'content' => $content,
            'auditing'=>3,//编辑之后改为是待审核
        ];
        $news_id = $this->InformationNewsModel->editBase($news_id, $editData);
        if ($news_id) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, [], $msg, $status);
    }
    

    /**
     * 资讯详情
     * @param $pintuan_id int
     * @param $details array
     * @return boolean
     */
    public function newsdetail($id)
    {
        $where = [
            'id' => $id
        ];
        $data = $this->InformationNewsModel->getOneByWhere($where);
        $data['content'] = str_replace('type="application/x-shockwave-flash"', ' ', $data['content']);
        $newclassname= $this->InformationNewsClassModel->getOneByWhere(array('id'=>$data['type']));
        $data['newscalss']=$newclassname['newsclass_name'];
        return $data;
    }
    
    /**
     * 删除资讯新闻
     *
     * @param $pintuan_id int
     * @param $details    array
     *
     * @return boolean
     */
    public function delnews()
    {
        $id=request_string('id');
        $flag = $this->InformationNewsModel->removeBase($id);
        
        if($flag !== false){
              $msg = __('success');
              $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    





}