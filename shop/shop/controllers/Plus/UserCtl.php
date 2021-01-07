<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Zhuyt
 */
class Plus_UserCtl extends Controller
{
    public $tryDays;
    public $plusMdl;
    public $plus_shopping_price;
    public $plus_shopping_mode;
    public $plus_agreement;
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->open_status = Web_ConfigModel::value('plus_switch')?:0;
        if(!$this->open_status)location_to(Yf_Registry::get('url'));
        $this->tryDays = Web_ConfigModel::value('plus_probationership')?:0;
        $this->plusMdl =  new Plus_UserModel();
        $this->plus_shopping_price =  Web_ConfigModel::value('plus_shopping_price')?:'0';
        $this->plus_shopping_mode = Web_ConfigModel::value('plus_shopping_mode')?:'';
        $this->plus_agreement = Web_ConfigModel::value('plus_agreement')?:'';
        $this->userId = Perm::$userId;
        $this->plusUserMdl  = new Plus_UserModel();
        $this->app_api_key     = Yf_Registry::get('paycenter_api_key');;
        $this->app_api_url     = Yf_Registry::get('paycenter_api_url');
        $this->app_api_id  = Yf_Registry::get('paycenter_app_id');
    }

    public function index()
    {
        //PLUS会员试用天数
        $tryDays = $this->tryDays;

        //会员折扣
        $plus_rate = Web_ConfigModel::value('plus_rate')?:'100';

        //加倍积分
        $plus_integral = Web_ConfigModel::value('plus_integral')?:'0';

        //超级会员日红包
        $plus_general_red = Web_ConfigModel::value('plus_general_red')?:'0';

        //开通会员，满xx送xx红包
        $plus_quota = Web_ConfigModel::value('plus_quota')?:'0';
        $plus_red_packet = Web_ConfigModel::value('plus_red_packet')?:'0';

        //搜索关键字
        $words = request_string('words')?:'';
        //flag PLUS 会员专享
        $flag = request_string('flag')?:0;
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 12;
        $flag && $Yf_Page->listRows = 16;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $plusGoodsMdl = new Plus_GoodsModel();
        $ret  =  $plusGoodsMdl->showPlusGoods($words,$page, $rows);
        $ret && $ret['items'] = $plusGoodsMdl->reformPlusGoods($ret['items']);
        $Yf_Page->totalRows = $ret['totalsize'];
        $page_nav = $Yf_Page->prompt();
        $user_id =  $this->userId;
        $user =$this->plusMdl->getPlususerInfo($user_id);
        $status = ($user['user_status']=='1'  && $user['end_date'] && $user['end_date']>time() )?true:false;//试用PLUS
        //计算Plus会员剩余天数,百分比
        $pct=0;
        if($status ){
            $hasPlusday = round(($user['end_date']-time())/60/60/24);
            ($hasPlusday<0) && $hasPlusday = 0;
            //已使用天数
            $useDay = round((time() - $user['create_time'])/(24*60*60));
            $hasPlusday &&  $pct =ceil($hasPlusday/$this->tryDays*100);
        }
        $userPlusEndDate = date("Y年m月d日 H:i",$user['end_date']);
        //type  正式PLus：2；试用PLus：1
        $plusTyp = $user['user_status']?:0;
        ($plusTyp == 3) && $plusTyp=0;
        $user_name = Perm::$row['user_account'];
        $userInfoModel = new User_InfoModel();
        //会员用户
        $user_logo = $userInfoModel->getUserMore($user_id)['info']['user_logo'];
        $flag && $plusTyp=0;
        (($plusTyp==1 &&  !$user['end_date'])||!$plusTyp ||$user['end_date']<time())&& $plusTyp=0;//控制显示
        $flag && $active = 1;
        !$active && $active =0;
        $url = $this->app_api_url;
         if ('json' == $this -> typ) {
            //封装返回数据
            $data['tryDays'] = $tryDays;
            $data['plus_integral'] = $plus_integral;
            $data['plus_rate'] = $plus_rate;
            $data['plus_general_red'] = $plus_general_red;
            $data['plus_quota'] = $plus_quota;
            $data['plus_red_packet'] = $plus_red_packet;
            $data['user'] = $user; //plus会员信息
            $data['plusTyp'] = $plusTyp; //
            $data['user_logo'] =$user_logo;
            $data['userPlusEndDate'] =$userPlusEndDate;
            $data['hasPlusday'] =$hasPlusday;
            $data['pct'] =$pct;
            $data['user_name'] =$user_name;
            $data['items'] = $ret['items'];
            return  $this->data->addBody(-140, $data);
        }
        include $this->view->getView();
    }

    public function open()
    {
        //不存在，则新增记录
        $user = $this->plusUserMdl->getOne($this->userId);
        if(!$user){
            $insert_row =array(
                'user_id' =>$this->userId,
                'user_status'=>Plus_UserModel::$user_status[1],//这里是开通正式会员，由于未生成订单，所以初始化默认
                'create_time'=>time(),
            );
            $this->plusUserMdl->addPlusUser($insert_row);
        }
        $plus_shopping_mode_data = array(
            '1'=>'按年度收费',
            '2'=>'按季度收费',
            '3'=>'按月度收费',
        );
        $plus_shopping_price =  $this->plus_shopping_price;
        $plus_shopping_price && $plus_shopping_price = number_format($plus_shopping_price,2);
        $plus_shopping_mode = $this->plus_shopping_mode;
        $plus_shopping_mode && $plus_shopping_mode = $plus_shopping_mode_data[$plus_shopping_mode];
        $user_id =  $this->userId;
        //本地读取远程信息
        $formvars = array(
            'user_id'=>$user_id,
        );
        $formvars['app_id'] = $this->app_api_id;

        foreach ($_GET as $k => $item)
        {
            if ('ctl' != $k && 'met' != $k && 'debug' != $k)
            {
                $formvars[$k] = $item;
            }
        }
        $url = $this->app_api_url;
        $parms=  sprintf('%s?ctl=Api_%s&met=%s&typ=json', $url, 'User_Info', 'getUserInfo');
        $init_rs = get_url_with_encrypt($this->app_api_key,$parms, $formvars);
        $user_identity_statu = $init_rs['data']['user_identity_statu'];
        if ($this->typ=='json') {
            $data['plus_shopping_price'] = $plus_shopping_price;
            $data['plus_shopping_mode'] = $plus_shopping_mode;
            $data['user_identity_statu'] = $user_identity_statu;
            return  $this->data->addBody(-140, $data);
        }
        include $this->view->getView();
    }

    /**
     *
     * 开通试用PLus
     */
    public function openTry()
    {
        $arr = array(
            'status'=>500,
            'strong'=>'对不起',
            'p'=>'Plus会员试用功能开通,操作失败！',
            'em'=>'如有疑问，请联系客服，谢谢！',
            'flag' =>false,
        );
        !$this->tryDays && exit(json_encode($arr));
        //判断用户是否通过实名认证
        $user_identity_statu = '';
        $formvars = array(
            'user_id'=>$this->userId,
        );
        $formvars['app_id'] = $this->app_api_id;
        $parms=  sprintf('%s?ctl=Api_%s&met=%s&typ=json', $this->app_api_url, 'User_Info', 'getUserInfo');
        $init_rs = get_url_with_encrypt($this->app_api_key,$parms, $formvars);
        $init_rs && $user_identity_statu = $init_rs['data']['user_identity_statu'];
        if($user_identity_statu!=2){
            $arr['p'] = '您未通过实名认证，请先实名认证，通过后再次操作！';
            $arr['location'] = true;
            $arr['url'] = $this->app_api_url;
            exit(json_encode($arr));
        }
        $rs_row = array();
        $user = $this->plusUserMdl->getOne($this->userId);
        $end_date = strtotime("+".($this->tryDays-1)." day");
        $time = time();
        if($user){
            $arr['flag'] = true;
            //已开通，不能再次开通
            $arr['p'] = 'PLUS试用会员不能重复开通！';
            exit(json_encode($arr));
        }else{
            //不存在，则新增记录
            $data = array(
                'user_id' => $this->userId,
                'create_time' =>$time,
                'user_status'=>Plus_UserModel::$user_status[1],
                'end_date' => $end_date,
            );
            $flag = $this->plusMdl->addPlusUser($data);
            check_rs($flag,$rs_row);
        }
        //创建订单
        $order_row = array();
        $pay_status = Plus_UserOrderModel::$pay_status[2];//试用会员无需支付，默认为已支付
        $order_row['user_id'] =  $this->userId;
        $order_row['meal'] = 'PLUS试用会员';
        $order_row['payment'] = 0;
        $order_row['start_date'] = $time;
        $order_row['end_date'] = $end_date;
        $order_row['pay_status'] = $pay_status;
        $order_row['order_status'] = Plus_UserModel::$user_status[1];
        $order_row['create_time'] = $time;
        $plusUserOrder = new Plus_UserOrderModel();
        $flag = $plusUserOrder->addPlusUserOrder($order_row);
        check_rs($flag,$rs_row);
        if(is_ok($rs_row)){
            $arr =array();
            $arr['status'] =200;
            $arr['flag'] = 1;
        }
        exit(json_encode($arr));
    }

    /**
     *
     * 用户协议
     */
    public function agreement(){
        $plus_agreement =  Web_ConfigModel::value('plus_agreement')?:'';
         if ($this->typ == 'json') {
           die(json_encode($this->plus_agreement));
        }
        include $this->view->getView();
    }

    /**
     *
     * 生成PLUS order
     */
    public  function createPlusOrder(){
        $time = time();
        $order_row = array();
        $pay_status = Plus_UserOrderModel::$pay_status[1];
        $order_row['user_id'] =  $this->userId;
        $order_row['meal'] = 'PLUS正式会员';
        $order_row['pay_use'] = $this->plus_shopping_mode;
        $order_row['payment'] = $this->plus_shopping_price;
        $order_row['pay_status'] = $pay_status;
        $order_row['order_status'] = Plus_UserModel::$user_status[2];//正式会员
        $order_row['create_time'] = $time;
        $plusUserOrder = new Plus_UserOrderModel();
        //开启事务
        $plusUserOrder->sql->startTransaction();
        $id = $plusUserOrder->addPlusUserOrder($order_row,true);
        //支付中心生成订单
        $formvars = array(
            'trade_title' => Perm::$row['user_account'].",开通PLUS正式会员",
            'amount' => $this->plus_shopping_price,
            'user_id' => $this->userId,
            'trade_type_id' => 10,//业务类型（开通PLUS会员）
            'app_id' => $this->app_api_id,
            'user_nickname' => Perm::$row['user_account'],
            'user_type' => 2,//付款方
        );
        $rs = get_url_with_encrypt($this->app_api_key, sprintf('%s?ctl=Api_Pay_Pay&met=createGeneralTrade&typ=json', $this->app_api_url), $formvars);
        $flag =false;
        if ($rs['status'] == 200) {
            $flag = $plusUserOrder ->editPlusUserOrder($id, array('payment_number' => $rs['data']['uorder']));
        }
        if($flag&&$id){
            $plusUserOrder->sql->commit();
        }else{
            $plusUserOrder->sql->rollBack();
        }
        exit(json_encode($rs));
    }
}
?>
