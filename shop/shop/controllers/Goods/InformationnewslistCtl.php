<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Goods_InformationnewslistCtl extends Controller
{
	public $Information = null;
	public $InformationNewsModel = null;
	public $InformationNewsClassModel = null;
	public $shopBaseModel=null;

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
        $this->shopBaseModel = new Shop_BaseModel();
        $this->userBaseModel = new User_BaseModel();

	}

	/**
	 * 资讯列表
	 *
	 * @access public
	 */
	public function index()
	{
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows'):12;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        if ($_REQUEST['curpage']) {
            $page = (int)$_REQUEST['curpage'];
        }
        $newsclass_id = request_string('status');
        $number = request_string('number');
        $cond_row = [];
        $order_row = [];
        $page_nav = '';
        $data = [];
        if (!empty($newsclass_id)) {
            $cond_row['type'] = $newsclass_id;
        }
        $cond_row['auditing'] = 1; //审核通过
        $cond_row['complaint:IN'] = [1, 2]; //是否投诉
        if (!empty($number)) {
            $order_row = ['number' => 'DESC'];
        } else {
            $order_row = ['create_time' => 'DESC'];
        }
        $data = $this->InformationNewsModel->getBaseAllList($cond_row, $order_row, $page, $rows);
        foreach ($data['items'] as $key => $vul) {
            if ($vul['author_type'] == 2) {
                $shopdata = $this->shopBaseModel->getOneByWhere(['user_id' => $vul['author_id']]);
                $data['items'][$key]['authorname'] = $shopdata['shop_name'];
                $data['items'][$key]['shop_id'] = $shopdata['shop_id'];
                $data['items'][$key]['logo'] = $shopdata['shop_logo'];
                $data['items'][$key]['time']= $data['items'][$key]['create_time'];
            } elseif ($vul['author_type'] == 1) {
                $userdata = $this->userBaseModel->getUserInfo(['user_id' => $vul['author_id']]);
                $data['items'][$key]['authorname'] = $userdata['user_account'];
                $data['items'][$key]['logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$vul['author_id'];
                $data['items'][$key]['time'] = $data['items'][$key]['create_time'];
            } else {
                $data['items'][$key]['authorname'] = '平台发布';
                $data['items'][$key]['time'] = $data['items'][$key]['create_time'];
            }
            if (!empty($vul['content'])) {
                preg_match('/<(img|embed).*?src="(.*?)".*?>/is',$vul['content'],$content);
                $data['items'][$key]['content_type'] = trim($content[1]);
                $data['items'][$key]['content_url'] = $content[2];
            }
        }
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        $data['hasmore'] = $page >= $data['total'] ? false:true;
        // $data['newsclass'] = $this->InformationNewsClassModel->getBywhere([]);
        // $data['newsclass'] = array_values($data['newsclass']);
        if ('json' == $this->typ) {
            return $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }
        ;
	}
	
	
	public function newsclasslist(){
        $data= $this->InformationNewsClassModel->getBywhere([]);
        $data=array_values($data);
        $this->data->addBody(-140, $data);
    }

	/**
	 * 资讯详情
	 *
	 * @access public
	 */
	public function newsdetails()
	{
		$newsid=request_string('id');
        $where = [
            'id' => $newsid
        ];
        $data = $this->InformationNewsModel->getOneByWhere($where);
        $data['content'] = str_replace('type="application/x-shockwave-flash"', ' ', $data['content']);
        if($data['author_type']==2){
              $shopdata=$this->shopBaseModel->getOneByWhere(array('user_id'=>$data['author_id']));
              $data['authorname']=$shopdata['shop_name'];
              $data['shop_id']=$shopdata['shop_id'];
        }elseif($data['author_type']==1){
              $userdata=$this->userBaseModel->getUserInfo(array('user_id'=>$data['author_id']));
              $data['authorname']=$userdata['user_account'];
        }else{
        	$data['authorname']='平台发布';
        }


        $number=$data['number']+1;
        $order_row=array('number'=>$number);
        $this->InformationNewsModel->editBase($newsid,$order_row);

		$this->data->addBody(-140, $data);
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
        $newsdata = $this->InformationNewsModel->getOneByWhere(['id' => $newsid]);
        if ($user_id == $newsdata['author_id']) {
            $msg = __('自己不可以投诉自己发布的资讯');
            $data = [];
            return $this->data->addBody(-140, $data, $msg, $status);
        }
        $order_row=array('complaint'=>2);
        $flas=$this->InformationNewsModel->editBase($newsid,$order_row);
        if($flas){
            $status = 200;
            $msg    = __('success');
        }else{
            $status = 250;
            $msg    = __('fail');
        }
		$this->data->addBody(-140, [$flas],$msg, $status);
	}

	


}

?>