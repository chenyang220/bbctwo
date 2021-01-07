<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

class Bargain_UnBargainCtl extends Controller
{

    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

    }

    //wap砍价列表
    public function getWapBargainList()
    {
        //分页
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 8;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $user_id = request_int('user_id');
        $is_list = request_int('is_list');
        $Bargain_BaseModel = new Bargain_BaseModel();
        $cond_row['is_del'] = Bargain_BaseModel::no_del;
        if($is_list == 1){
            $cond_row['end_time'] = time();
            $cond_row['bargain_status'] = Bargain_BaseModel::ISON;
        }else{
            $cond_row['user_id'] = $user_id;
        }
        $cond_row['is_list'] = $is_list;
        $data = $Bargain_BaseModel->getBargainList($cond_row, array('create_time' => 'DESC'), $page, $rows);

        //砍价促销的状态
        $data['bargain_status'] = Web_ConfigModel::value('bargain_status');

        //当前用户砍价状态
        $Bargain_BuyUserModel = new Bargain_BuyUserModel();
        if($is_list == 1){
            $bargain_ids = array_column($data['items'],'bargain_id');
            if($bargain_ids){
                $row = array();
                $row['bargain_id:IN'] = $bargain_ids;
                $row['user_id'] = $user_id;
                $row['bargain_state'] = Bargain_BuyUserModel::ISON;
                $user_buy_list = $Bargain_BuyUserModel->getByWhere($row);
                $list = array();
                foreach($user_buy_list as $k=>$v){
                    $list[$v['bargain_id']] = $v;
                }
            }else{
                $list = array();
            }
        }else{
            $list = array();
        }
        foreach ($data['items'] as $key=>$value){
            if($list[$key]){
                $data['items'][$key]['is_join'] = 1;
                //如果有用户已发起的砍价，则显示倒计时
                $cond = array();
                $cond['user_id'] = $user_id;
                $cond['bargain_id'] = $value['bargain_id'];
                $cond['bargain_state'] = $Bargain_BuyUserModel::ISON;
                $buy_info = $Bargain_BuyUserModel->getOneByWhere($cond);
                $data['items'][$key]['user_end_time'] = $buy_info['user_end_time'];
                $data['items'][$key]['user_end_date'] = date('Y-m-d H:i:s', $buy_info['user_end_time']);
                $data['items'][$key]['buy_id'] = $buy_info['buy_id'];
                $data['items'][$key]['bargain_price_count'] = $buy_info['bargain_price_count'];
                $data['items'][$key]['overPlus'] = number_format($value['goods_price'] - $buy_info['bargain_price_count'] - $buy_info['bargain_price'],2);
            }else{
                $data['items'][$key]['is_join'] = 0;
            }
        }

        $data['items'] = array_values($data['items']);
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        $this->data->addBody(-140, $data,$buy_info);
    }

    //wap砍价详情页、帮好友砍价详情
    public function getBargainInfoByBuyId()
    {
        $buy_id = request_int('buy_id');
        $use_id = request_int('user_id');
        $Bargain_BuyUserModel = new Bargain_BuyUserModel();
        $data = $Bargain_BuyUserModel->getBargainInfoByBuyId($buy_id, $use_id);
        $this->data->addBody(-140, $data);
    }
 
}

?>
