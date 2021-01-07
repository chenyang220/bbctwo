<?php

/**
 * @author     charles
 */
class InformationlistCtl extends Controller
{
    public $Information = null;
    public $InformationNewsModel = null;
    public $InformationNewsClassModel = null;
    public $shopBaseModel = null;
    
    /**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     *
     * @access public
     */
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->nav = $this->navIndex();
        $this->cat = $this->catIndex();
        $this->initData();
        $this->web = $this->webConfig();
        $this->Information = new Information_Base();
        $this->InformationNewsModel = new Information_NewsModel();
        $this->InformationNewsClassModel = new Information_NewsClassModel();
        $this->shopBaseModel = new Shop_BaseModel();
        $this->userBaseModel = new User_BaseModel();
        $this->useInfoModel = new User_InfoModel();
    }
    
    public function index()
    {
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows'):10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $newsclass_id = request_string('status');
        $number = request_string('number');
        $cond_row = [];
        $order_row = [];
        $data = [];
        if (!empty($newsclass_id)) {
            $cond_row['type'] = $newsclass_id;
        }
        $cond_row['auditing'] = 1;
        $cond_row['complaint:IN'] = array(1,2);
        if ($number == "DESC") {
            $order_row = ['number' => "DESC"];
        } else {
            $order_row = ['create_time' => 'DESC'];
        }
        $data['news'] = $this->InformationNewsModel->getBaseAllList($cond_row, $order_row, $page, $rows);
        foreach ($data['news']['items'] as $key => $vul) {
            if ($vul['author_type'] == 2) {
                $shopdata = $this->shopBaseModel->getOneByWhere(['user_id' => $vul['author_id']]);
                $data['news']['items'][$key]['authorname'] = $shopdata['shop_name'];
                $data['news']['items'][$key]['shop_id'] = $shopdata['shop_id'];
                $data['news']['items'][$key]['logo'] = $shopdata['shop_logo'];
            } elseif ($vul['author_type'] == 1) {
                $userdata = $this->useInfoModel->getUserInfo(['user_id' => $vul['author_id']]);
                $data['news']['items'][$key]['authorname'] = $userdata['user_name'];
                $data['news']['items'][$key]['logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$vul['author_id'];
            } else {
                $data['news']['items'][$key]['authorname'] = '平台发布';
            }
            if (!empty($vul['content'])) {
                preg_match('/<(img|embed).*?src="(.*?)".*?>/is',$vul['content'],$content);
                $data['news']['items'][$key]['content_type'] = trim($content[1]);
                $data['news']['items'][$key]['content_url'] = $content[2];
            }
        }
        $data['newsclass'] = $this->InformationNewsClassModel->getBywhere([]);
        $data['newsclass'] = array_values($data['newsclass']);
        $Yf_Page->totalRows = $data['news']['totalsize'];

        $page_nav = $Yf_Page->prompt();

        include $this->view->getView();
    }
    
    public function details()
    {
        $newsid = request_string('id');
        $where = [
            'id' => $newsid
        ];
        $data = $this->InformationNewsModel->getOneByWhere($where);
        // $data['content'] = substr($data['content'], 2, strlen($data['content']) - 1);
        $data['content']=str_replace('type="application/x-shockwave-flash"','', $data['content']);
        if ($data['author_type'] == 2) {
            $shopdata = $this->shopBaseModel->getOneByWhere(['user_id' => $data['author_id']]);
            $data['authorname'] = $shopdata['shop_name'];
            $data['shop_id'] = $shopdata['shop_id'];
        } elseif ($data['author_type'] == 1) {
            $userdata = $this->userBaseModel->getUserInfo(['user_id' => $data['author_id']]);
            $data['authorname'] = $userdata['user_account'];
        } else {
            $data['authorname'] = '平台发布';
        }
        $number = $data['number'] + 1;
        $order_row = ['number' => $number];
        $this->InformationNewsModel->editBase($newsid, $order_row);
        include $this->view->getView();
    }
    
    /**
     * 资讯详情
     *
     * @access public
     */
    public function Complaint()
    {
        $user_id = Perm::$userId;
        $newsid = request_string('id');
        $newsdata= $this->InformationNewsModel->getOneByWhere(array('id'=>$newsid));
        if($user_id== $newsdata['author_id']){
            $msg = __('自己不可以投诉自己发布的资讯');
            $data = [];
           return  $this->data->addBody(-140, $data, $msg, $status);
        }
        $order_row = ['complaint' => 2];
        
        $flas = $this->InformationNewsModel->editBase($newsid, $order_row);
        if ($flas) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('fail');
        }
        $this->data->addBody(-140, [], $msg, $status);
    }
    
    public function navIndex()
    {
        $platformNavModel = new Platform_NavModel();
        $cond_row = [
            "nav_location:NOT IN" => [1, 2],
            "nav_active" => "1"
        ];
        $order_row = ['nav_displayorder' => 'asc'];
        $data = $platformNavModel->getNavList($cond_row, $order_row, 1);
        if ($data['items']) {
            foreach ($data['items'] as $key => $value) {
                //团购频道关闭
                if (!Web_ConfigModel::value('groupbuy_allow')) {
                    if (preg_match("/ctl=GroupBuy/", $value['nav_url'])) {
                        unset($data['items'][$key]);
                    }
                }
                //积分商城频道关闭
                if (!Web_ConfigModel::value('pointshop_isuse')) {
                    if (preg_match("/ctl=Points/", $value['nav_url'])) {
                        unset($data['items'][$key]);
                    }
                }
            }
        }
        
        return $data;
    }
}
