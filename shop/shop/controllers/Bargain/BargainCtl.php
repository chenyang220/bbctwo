<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

class Bargain_BargainCtl extends Controller
{

    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

    }

    //wap我的砍价列表
    public function getWapSelfBargainList()
    {
        //分页
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 8;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $Bargain_BuyUserModel = new Bargain_BuyUserModel();
        $cond_row['user_id'] = Perm::$userId;
        $data = $Bargain_BuyUserModel->getBuyUserBargainList($cond_row, array('create_time' => 'DESC'), $page, $rows);

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        $this->data->addBody(-140, $data);
    }

    //发起砍价
    public function InitiateBargain()
    {
        $add_row = array();
        $add_row['user_id'] = request_int('user_id');
        $add_row['bargain_id'] = request_int('bargain_id');
        $add_row['address_id'] = request_int('address_id');
        $Bargain_BuyUserModel = new Bargain_BuyUserModel();
        $data = $Bargain_BuyUserModel->InitiateBargain($add_row);
        $this->data->addBody(-140, $data['data'], $data['msg'], $data['status']);
    }

    //帮好友砍价
    public function HelpBargain()
    {
        $buy_id = request_int('buy_id');
        $Bargain_BuyUserModel = new Bargain_BuyUserModel();
        $data = $Bargain_BuyUserModel->HelpBargain($buy_id);
        $this->data->addBody(-140, $data['data'],$data['msg'],$data['status']);
    }

    //判断是否为商家自己发起砍价、是否参加过改砍价
    public function checkSelf()
    {
        $bargain_id = request_int('bargain_id');
        //是否商家自己发起砍价
        $Bargain_BaseModel = new Bargain_BaseModel();
        $flag = $Bargain_BaseModel->checkSelf($bargain_id);

        //当前用户是否参加过该砍价
        $user_id = Perm::$userId;
        $Bargain_BuyUserModel = new Bargain_BuyUserModel();
        $row['user_id'] = $user_id;
        $row['bargain_id'] = $bargain_id;
        $row['bargain_state'] = Bargain_BuyUserModel::ISON;
        $buy_user = $Bargain_BuyUserModel->getByWhere($row);

        if ($flag || $buy_user) {
            if($flag){
                $msg = '商家自己不可以发起砍价';
            }

            if ($buy_user) {
                $msg = '您已经参加过该活动';
            }

            $status = 200;
        } else {
            $msg = '可以正常参加活动';
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

}

?>
