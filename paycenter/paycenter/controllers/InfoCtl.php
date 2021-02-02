<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class InfoCtl extends Controller
{
    public function __construct(&$ctl, $met, $typ)
    {



        parent::__construct($ctl, $met, $typ);
        $this->User_InfoModel = new User_InfoModel();
        $this->User_BaseModel = new User_BaseModel();
        $this->User_ResourceModel = new User_ResourceModel();
        $this->Consume_WithdrawModel = new Consume_WithdrawModel();
        $this->Consume_DepositModel = new Consume_DepositModel();
        $this->messageTemplateModel = new Message_TemplateModel();
        $this->Consume_RecordModel = new Consume_RecordModel();
        $this->Service_FeeModel = new Service_FeeModel();
        $this->shop_api_key = Yf_Registry::get('shop_api_key');
        $this->shop_app_id = Yf_Registry::get('shop_app_id'); 
        $this->shop_api_url = Yf_Registry::get('shop_api_url');
    }
        //提现到微信零钱
    public function addWithdrawWx()
    {
        $user_id = Perm::$userId;
        //判断用户是否绑定微信
        $key = Yf_Registry::get('ucenter_api_key');
        $url       = Yf_Registry::get('ucenter_api_url');
        $app_id    = Yf_Registry::get('ucenter_app_id');
        $server_id = Yf_Registry::get('server_id');
        //开通ucenter
        //本地读取远程信息
        $formvars              = array();
        $formvars['user_id'] = $user_id;
        $formvars['app_id']    = $app_id;
        $formvars['server_id'] = $server_id;
        
        $formvars['ctl'] = 'Api_User';
        $formvars['met'] = 'getUserBind';
        $formvars['typ'] = 'json';
        
        $init_rs = get_url_with_encrypt($key, $url, $formvars);

        if(empty($init_rs['data']['bind_openid']) && !$init_rs['data']['bind_openid'])
        {
            return $this->data->addBody(-140, array(), __('请先绑定微信'), 250);
        }
            //throw new Exception('请使用微信登录，绑定微信');
        
        
        //获取用户信息
        $edit['pay_uid'] = $user_id;
        
        $edit['amount'] = request_string('withdraw_money');//提现金额
        $edit['con'] = request_string('con');//提款说明
        $edit['service_fee_id'] = request_int('id');  //到账时间 1-2小时内到账  2-次日24点 3-次日48点
        $paypasswd = request_string('paypasswd');  //支付密码
        $data = [];

        
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $edit['amount']) || $edit['amount'] <= 0) {
            return $this->data->addBody(-140, $data, __('付款金额有误'), 250);
        }
        //获取用户信息
        $user_base = current($this->User_BaseModel->getBase($user_id));
        if (!$user_base['user_pay_passwd']) {
            return $this->data->addBody(-140, $data, __('请先设置支付密码'), 250);
        }
        if ($user_base['user_pay_passwd'] != MD5($paypasswd)) {
            return $this->data->addBody(-140, $data, __('支付密码错误'), 250);
        }
        
        
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($user_id);
        $user_resource = current($this->User_ResourceModel->getResource($user_id));
        $mobile = $user_info['user_mobile'];  //手机
//      $yzm = request_string('yzm');  //验证码
//      if (!VerifyCode::checkCode($mobile, $yzm)) {
//          return $this->data->addBody(-140, $data, __('验证码错误'), 250);
//      }
        $Service_FeeModel = new Service_FeeModel();
        $fee = current($Service_FeeModel->getFeeById($edit['service_fee_id']));
        $amount = $edit['amount'];//提现金额
        $num = 0;
        /*$price = $amount * ($fee['fee_rates'] * 0.01 * 1);//手续费
        if ($price > 0) {
            if ($price <= $fee['fee_min'] * 1) {
                $num = $fee['fee_min'] * 1;
            } elseif ($price >= $fee['fee_max'] * 1) {
                $num = $fee['fee_max'] * 1;
            } else {
                $num = $price;
            }
        }*/
        $amount = $amount + $num;
        if ($amount > $user_resource['user_money']) {
            return $this->data->addBody(-140, $data, __('余款不足'), 250);
        }
        //减少费用
         $resource_edit_row['user_money'] = $user_resource['user_money'] - $amount;
         $resource_edit_row['user_money_frozen'] = $user_resource['user_money_frozen'] + $amount;
//      //开启事物
        $this->User_ResourceModel->sql->startTransactionDb();
        $res = $this->User_ResourceModel->editResource($user_id, $resource_edit_row);
         if (!$res) {
             $this->User_ResourceModel->sql->rollBackDb();
             $data['code'] = 1;
             return $this->data->addBody(-140, $data, __('转账失败'), 250);
         }
        //插入交易明细表
        $flow_id = date("Ymdhis") . rand(0, 9);
        $record_row = [
        'order_id' => $flow_id,
        'user_id' => $user_id,
        'user_nickname'=>$user_info['user_nickname'],
        'record_money' => -$amount,
        'record_date' => date("Y-m-d"),
        'record_year' => date("Y"),
        'record_month' => date("m"),
        'record_day' => date("d"),
        'record_title' => _('提现'),
        'record_time' => date('Y-m-d H:i:s'),
        'trade_type_id' => '4',
        'user_type' => '2',
        ];
        $record_id = $this->Consume_RecordModel->addRecord($record_row, true);
        if (!$record_id) {
            $this->User_ResourceModel->sql->rollBackDb();
            $data['code'] = 2;
            return $this->data->addBody(-140, $data, __('转账失败'), 250);
        }
        //插入提现申请表
        $widthdraw_row = [
        'pay_uid' => $user_id,
        'orderid' => $flow_id,
        'amount' => $amount,
        'add_time' => time(),
        'con' => $edit['con'],
        'supportTime' => $edit['service_fee_id'],
        'fee' => $num,
        'withdraw_type' => 1,
        ];
        $flag = $this->Consume_WithdrawModel->addWithdraw($widthdraw_row);
        if ($flag && $this->User_ResourceModel->sql->commitDb()) {
            $data = $widthdraw_row;
        
            return $this->data->addBody(-140, $data, __('操作成功'), 200);
        } else {
            $this->User_ResourceModel->sql->rollBackDb();
            $data['code'] = 2;
        
            return $this->data->addBody(-140, $data, __('操作失败'), 250);
        }
    }

    //提现到支付宝
    //
    public function addWithdrawZfb(){
        $user_id = Perm::$userId;
        //获取用户信息
        $edit['pay_uid'] = $user_id;
        
        $edit['amount'] = request_string('withdraw_money');//提现金额
        $edit['con'] = request_string('con');//提款说明
        $edit['service_fee_id'] = request_int('id');  //到账时间 1-2小时内到账  2-次日24点 3-次日48点
        $withdraw_identity = request_string('withdraw_identity');//支付宝账号
        $withdraw_name = request_string('withdraw_name');//支付宝账号所属人姓名
        $paypasswd = request_string('paypasswd');  //支付密码
        $data = [];

        
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $edit['amount']) || $edit['amount'] <= 0) {
            return $this->data->addBody(-140, $data, __('付款金额有误'), 250);
        }
        //获取用户信息
        $user_base = current($this->User_BaseModel->getBase($user_id));
        if (!$user_base['user_pay_passwd']) {
            return $this->data->addBody(-140, $data, __('请先设置支付密码'), 250);
        }
        if ($user_base['user_pay_passwd'] != MD5($paypasswd)) {
            return $this->data->addBody(-140, $data, __('支付密码错误'), 250);
        }
        
        
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($user_id);
        $user_resource = current($this->User_ResourceModel->getResource($user_id));
        $mobile = $user_info['user_mobile'];  //手机
        $Service_FeeModel = new Service_FeeModel();
        $fee = current($Service_FeeModel->getFeeById($edit['service_fee_id']));
        $amount = $edit['amount'];//提现金额
        $num = 0;
        if ($amount + $num > $user_resource['user_money']) {
            return $this->data->addBody(-140, $data, __('余款不足'), 250);
        }
        $m = $amount + $num;
        //开启事物
        $this->User_ResourceModel->sql->startTransactionDb();
        //插入交易明细表
        $flow_id = date("Ymdhis") . rand(0, 9);
        $record_row = [
        'order_id' => $flow_id,
        'user_id' => $user_id,
        'user_nickname'=>$user_info['user_nickname'],
        'record_money' => -$m,
        'record_date' => date("Y-m-d"),
        'record_year' => date("Y"),
        'record_month' => date("m"),
        'record_day' => date("d"),
        'record_title' => _('提现'),
        'record_time' => date('Y-m-d h:i:s'),
        'trade_type_id' => '4',
        'user_type' => '2',
        ];
        $record_id = $this->Consume_RecordModel->addRecord($record_row, true);
        if (!$record_id) {
            $this->User_ResourceModel->sql->rollBackDb();
            $data['code'] = 2;
        
            return $this->data->addBody(-140, $data, __('转账失败'), 250);
        }
        //插入提现申请表
        $widthdraw_row = [
        'pay_uid' => $user_id,
        'orderid' => $flow_id,
        'amount' => $amount,
        'add_time' => time(),
        'con' => $edit['con'],
        'supportTime' => $edit['service_fee_id'],
        'fee' => $num,
        'withdraw_identity'=>$withdraw_identity,
        'withdraw_name'=>$withdraw_name,
        'withdraw_type' => 2,
        ];
        $flag = $this->Consume_WithdrawModel->addWithdraw($widthdraw_row);
        if ($flag && $this->User_ResourceModel->sql->commitDb()) {
            $data = $widthdraw_row;
        
            return $this->data->addBody(-140, $data, __('操作成功'), 200);
        } else {
            $this->User_ResourceModel->sql->rollBackDb();
            $data['code'] = 2;
        
            return $this->data->addBody(-140, $data, __('操作失败'), 250);
        }
    }
    
    // 默认设置
    public function webConfig()
    {
        $web['site_logo'] = Web_ConfigModel::value("site_logo");//首页logo
        $web['web_name'] = Web_ConfigModel::value("site_name");//首页名称
        $web['buyer_logo'] = Web_ConfigModel::value("setting_buyer_logo");//会员中心logo
        $web['seller_logo'] = Web_ConfigModel::value("setting_seller_logo");//卖家中心logo
        $web['goods_image'] = Web_ConfigModel::value("photo_goods_logo");//商品图片
        $web['shop_head_logo'] = Web_ConfigModel::value("photo_shop_head_logo");//店铺头像
        $web['shop_logo'] = Web_ConfigModel::value("photo_shop_logo");//店铺标志
        $web['user_avatar'] = Web_ConfigModel::value("photo_user_avatar");//默认头像
        
        return $web;
    }
    
    // 首页
    public function index()
    {
        $user_id = Perm::$userId;
        //获取用户信息
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getUserInfo($user_id);
        //判断用户的实名认证证件有效期是否过期，如果已经过期，则将实名认证状态修改为等待审核状态
        $endTime = $user_info['user_identity_end_time'];
        $second1 = strtotime($endTime);
        $second2 = time();
        $duff_time = ($second1 - $second2) / 86400;
        //$endTime  = 1时为长期
        if ($duff_time <= 0 && $user_info['user_identity_statu'] > 0 && $endTime != 1) {
            $User_InfoModel->editInfo($user_id, ['user_identity_statu' => $User_InfoModel::BT_VERIFY_WAIT,
                'user_bt_status' => $User_InfoModel::BT_VERIFY_NO]);
        }
        //如果用户的实名认证失败，修改用户的白条审核信息
        if ($user_info['user_identity_statu'] == $User_InfoModel::BT_VERIFY_FAIL) {
            $User_InfoModel->editInfo($user_id, ['user_bt_status' => $User_InfoModel::BT_VERIFY_NO]);
        }
        $user_info = $User_InfoModel->getUserInfo($user_id);
        fb($user_info);
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne($user_id);
        //获取用户资产
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getResource($user_id);
        $user_resource = current($user_resource);
        $user_money_total = $user_resource['user_money'] + $user_resource['user_recharge_card'];
        $data_percent = !$user_money_total ? 0:round(($user_resource['user_recharge_card'] / $user_money_total) * 100);
        //查找交易记录（3条）
        $Consume_RecordModel = new Consume_RecordModel();
        $consume_record_list = $Consume_RecordModel->getRecordList($user_id, null, null, 1, 10);
        if ($this->typ == 'json') {
            $data = array();
            $data['user_resource'] = $user_resource;
            return $this->data->addBody(-140, $data, 'success', 200);
        }

        $this->view->setMet('main');
        include $this->view->getView();
    }
    
    //首页
    public function main()
    {
        //获取用户信息
        $user_id = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($user_id);
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne($user_id);
        //获取用户资产
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getResource($user_id);
        $user_resource = current($user_resource);
        //查找交易记录（3条）
        $Consume_RecordModel = new Consume_RecordModel();
        $consume_record_list = $Consume_RecordModel->getRecordList($user_id, null, null, 1, 10);
        include $this->view->getView();
    }
    
    //交易记录页面
    public function recordlist()
    {
        $user_id = Perm::$userId;
        //获取用户资产
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getResource($user_id);
        $user_resource = current($user_resource);
        $start_date = trim(request_string("start_date"));
        $end_date = trim(request_string("end_date"));
        $time = request_string("time");
        $status = request_string("status");
        $type = request_int("type");
        $utype = request_int("utype");
        $record_delete = request_int("record_delete");
        if ($record_delete) {
            $cond_row['record_delete'] = 1;
        } else {
            $cond_row['record_delete'] = 0;
        }
        if ($time) {
            $cond_row['record_time:>='] = $time;
        }
        if ($start_date) {
            $start_date = date('Y-m-d', strtotime($start_date)) !== $start_date ? $start_date:$start_date . ' 00:00:00';
            $cond_row['record_time:>='] = $start_date;
        }
        if ($end_date) {
            $end_date = date('Y-m-d', strtotime($end_date)) !== $end_date ? $end_date:$end_date . ' 23:59:59';
            $cond_row['record_time:<='] = $end_date;
        }
        if ($status) {
            //进行中 1.购物为待付款到未确认收货之间的状态 2.其他为为处理中
            if ($status == 'doing') {
                $cond_row['record_status:IN'] = [RecordStatusModel::IN_HAND, RecordStatusModel::RECORD_WAIT_SEND_GOODS, RecordStatusModel::RECORD_WAIT_CONFIRM_GOODS];
            }
            //未付款
            if ($status == 'waitpay') {
                $cond_row['record_status'] = RecordStatusModel::IN_HAND;
            }
            //等待发货
            if ($status == 'waitsend') {
                $cond_row['record_status'] = RecordStatusModel::RECORD_WAIT_SEND_GOODS;
            }
            //未确认收货
            if ($status == 'waitconfirm') {
                $cond_row['record_status'] = RecordStatusModel::RECORD_WAIT_CONFIRM_GOODS;
            }
            //退款
            if ($status == 'retund') {
                $cond_row['trade_type_id'] = Trade_TypeModel::REFUND;
            }
            //成功
            if ($status == 'success') {
                $cond_row['record_status'] = RecordStatusModel::RECORD_FINISH;
            }
            //取消
            if ($status == 'cancel') {
                $cond_row['record_status'] = RecordStatusModel::RECORD_CANCEL;
            }
        }
        if ($type && ($status != 'retund')) {
            $cond_row['trade_type_id'] = $type;
        }
        if ($utype) {
            $cond_row['user_type'] = $utype;
        }
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows'):10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        if(request_int('page')){
            $page = request_int('page');
        }
        $cond_row['user_id'] = $user_id;
        $consume_record_list = $this->Consume_RecordModel->getRecordList1($cond_row, ["record_time" => "DESC"], $page, $rows);        $Yf_Page->totalRows = $consume_record_list['totalsize'];
        $page_nav = $Yf_Page->prompt();
        if ($this->typ === 'json') {
            //wap需按月份分组展示
            $months = [];
            foreach ($consume_record_list['items'] as $data) {
                if ($data['record_year'] == 0) { //很奇葩，管理员充值就是没有没有年月日
                    $admin_deposit_date_arr = explode('-', date('Y-n-j', strtotime($data['record_time'])));
                    $data['record_year'] = $admin_deposit_date_arr[0];
                    $data['record_month'] = $admin_deposit_date_arr[1];
                    $data['record_day'] = $admin_deposit_date_arr[2];
                }
                $k = $data['record_year'] . "-" . $data['record_month'];
                $months[$k][] = $data;
            }

            if($months)
            {
                 $consume_record_list['months'] = $months;
             }else
             {
                $months = (object)array();
                $consume_record_list['months'] = $months;
 
             }
            $consume_record_list['months'] = $months;
            
            return $this->data->addBody(-140, $consume_record_list, $cond_row, 200);
        }
        include $this->view->getView();
    }

    //小程序余额详情
    public function wxrecordlist()
    {
        $user_id = Perm::$userId;
        //获取用户资产
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getResource($user_id);
        $user_resource = current($user_resource);

        $cond_row['user_id'] = $user_id;
        $cond_row['record_delete'] = 0;
        $cond_row['trade_type_id:IN'] = array(1,3,4); //购物充值提现购物
        $cond_row['record_status:IN'] = array(
            RecordStatusModel::RECORD_WAIT_SEND_GOODS,
            RecordStatusModel::RECORD_FINISH
        );

        $rows = request_int('listRows') ? request_int('listRows'):10;
        $page = request_int('page',1);
        $consume_record_list = $this->Consume_RecordModel->getRecordList1($cond_row, ["record_time" => "DESC"], $page, $rows);

        return $this->data->addBody(-140, $consume_record_list, $cond_row, 200);

    }
    //删除记录
    public function delRecordlist()
    {
        //获取用户信息
        $user_id = Perm::$userId;
        //$user_id = '1';
        $consume_record_id = request_string('id');
        $record_delete = request_string('record_delete');
        $edit['user_id'] = $user_id;
        $re = $this->Consume_RecordModel->getRecord($consume_record_id, $edit);
        if ($re) {
            if ($record_delete) {
                $edit['record_delete'] = 0;
            } else {
                $edit['record_delete'] = 1;
            }
            $flag = $this->Consume_RecordModel->editRecord($consume_record_id, $edit);
            if ($flag) {
                $msg = 'success';
                $status = 200;
            } else {
                $msg = 'failure';
                $status = 250;
            }
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //交易详情记录页面
    public function recorddetail()
    {
        $consume_record_id = request_string('id');
        $re = $this->Consume_RecordModel->getOne($consume_record_id);
        //转账显示转账人
        if ($re['trade_type_id'] == 2) {
            $cond_row = [];
            $cond_row['user_type'] = 2;
            $cond_row['order_id'] = $re['order_id'];
            $consume = current($this->Consume_RecordModel->getByWhere($cond_row));
            //获取用户信息
            $user_info_model = new User_InfoModel();
            $user_info = $user_info_model->getOne($consume['user_id']);
            $re['payer'] = $user_info['user_nickname'];
        }
        if ($re['trade_type_id'] == 4) {
            $data = $this->Consume_WithdrawModel->getWithdrawByOid($re['order_id']);
            foreach ($data as $k => $v) {
                $id = $v['supportTime'];
                $de = $this->Service_FeeModel->getOne($id);
                $data[$k]['time_con'] = $de['name'];
            }
        }
        include $this->view->getView();
    }
    
    //充值页面 DEPOSIT
    public function deposit()
    {
        $user_id = Perm::$userId;
        //查找账户的余额和充值卡信息
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getOne($user_id);
        //获取用户所有的充值卡信息
        $Card_InfoModel = new Card_InfoModel();
        $card_list = $Card_InfoModel->getUserCard($user_id);
        include $this->view->getView();
    }
    
    //充值记录页面 DEPOSIT
    public function depositlist()
    {
        $user_id = Perm::$userId;
        //$user_id = 1;
        //获取用户资产
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getResource($user_id);
        $user_resource = current($user_resource);
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows'):10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $cond_row['server_id'] = $user_id;
        $consume_Deposit_list = $this->Consume_DepositModel->getDepositList1($cond_row, ["deposit_gmt_create" => "DESC"], $page, $rows);
        $Yf_Page->totalRows = $consume_Deposit_list['totalsize'];
        $page_nav = $Yf_Page->prompt();
        include $this->view->getView();
    }
    
    //提现页面 Withdraw
    public function withdraw()
    {
        //获取服务费用
        $Service_FeeModel = new Service_FeeModel();
        $service_fee_list = array_values($Service_FeeModel->getFee("*"));
        //获取用户的实名
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne(Perm::$userId);
        if ($user_info['user_identity_statu'] != User_InfoModel::BT_VERIFY_PASS) {
            location_to(Yf_Registry::get('url') . '/index.php?ctl=Info&met=account');
        }
        //判断是否设置过支付密码
        $user_base_info = Perm::$row;
        if (!isset($user_base_info['user_pay_passwd'])) {
            $user_result = $this->User_BaseModel->getOneByWhere(perm::$userId);
            $user_base_info['user_pay_passwd'] = $user_result['user_pay_passwd'];
        }
        if (!$user_base_info['user_pay_passwd']) {
            location_to(Yf_Registry::get('url') . '?ctl=Info&met=passwd&typ=e');
            exit;
        }
        $realname = $user_info['user_realname'];
        if ($realname) {
            $real = 1;
        } else {
            $real = 0;
            $realname = 0;
        }
        //判断是否开启大华捷通支付
        // $yunshan_status = Web_ConfigModel::value('yunshan_status');
        // if ($yunshan_status == 1) {
        //     $shop_id = 0;
        //     $data = $User_InfoModel ->getShopPayConfig();
        //     $data = current($data);
        //     if (!empty($data)) {
        //         $hour  = date("H");
        //         if ($hour < 14 &&  $hour > 17) {
        //             echo '<h5>请在14:00-17:00时间内进行结算提现</h5>';
        //             exit();
        //         }else{
        //             $shop_numbers = array();
        //             $payshopnumer =  $data['payshopnumer'];
        //             $cbpayshopnumer =  $data['cbpayshopnumer'];
        //             $xcxpayshopnumer =  $data['xcxpayshopnumer'];
        //             array_push($shop_numbers, $payshopnumer);
        //             array_push($shop_numbers, $cbpayshopnumer);
        //             array_push($shop_numbers, $xcxpayshopnumer);
        //             $shop_numbers = array_filter($shop_numbers);
        //             $shop_numbers = array_unique($shop_numbers);
        //             //余额查询  后期通过结算单处理
        //             $url =  Web_ConfigModel::value('yunshan_url').'/entryService/UmsWithdrawals';
        //             $key =  Web_ConfigModel::value('yunshantixian_key');
        //             $params = array();
        //             $params['transType'] = 'txAmountQuery';  //固定值 txAmountQuery
        //             $params['signType'] = 'MD5';
        //            foreach ($shop_numbers as $shop_number) {
        //             $params['mchntNo'] = $cbpayshopnumer; //申请提现的商户编号
        //             $mac = self::signsyl($params,$key); // 签名
        //             $params['mac'] = $mac;
        //             $html_form =self::create_html($params, $url);
        //             $html_form = json_decode($html_form,true);
        //            }
        //             if ($html_form['errCode'] == 00) {
        //                 $tzWithdrawAmtPublic = $html_form['tzWithdrawAmtPublic']/100;
        //             }else{
        //                 echo "<h5>{$html_form['errMsg']}</h5>";
        //                 exit();
        //             }
                    
        //         }
        //         $this->view->setMet('shop_settlement');
        //     }
        // }
        include $this->view->getView();
    }


    /**
     * 大华捷通提现接口
     * 
     * @dateTime  2020-06-08
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
    public function ylwithdraw(){
        $url =  Web_ConfigModel::value('yunshan_url').'/entryService/UmsWithdrawals';
        $key =  Web_ConfigModel::value('yunshantixian_key');
        $params = array();
        $params['transType'] = 'backstageMode'; //固定值backstageMod
        $params['mchntNo'] = '89833027311F039';  //商户编号
        $params['withdrawType'] = 2;
        $params['withdrawAmt'] = 1;
        $params['sysOrderId'] = uniqid('TX_');
        $params['signType'] = 'MD5';
        $mac = self::signsyl($params,$key); // 签名
        $params['mac'] = $mac;
        $html_form =self::create_html($params, $url);
        $html_form = json_decode($html_form,true);
    }

    /**
     * SHA-256算法
     * 
     * @dateTime  2020-05-28
     * 
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
    private static function encrypt_sha256($str = ''){
      return hash("sha256", $str);
    }
    
    /**
     * 簽名
     * 
     * @author fzh
     * @link      https://github.com/mustify
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
    private static function  signsyl($params=array(),$md5Key){
        ksort($params);
        $sign = '';
        foreach ($params as $v) {
            $sign .= $v;
        }
        $sign = strtoupper(md5($sign . $md5Key));
        return $sign;
    }

    /**
     * 請求接口數據
     * 
     * @dateTime  2020-05-21
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
    private static function create_html($params, $action) {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $action);
        //设置头文件的信息作为数据流输出
        //curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        $paramsdata =  http_build_query($params)  ;
        curl_setopt($curl, CURLOPT_POSTFIELDS, $paramsdata);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
   }
    //判断用户是否实名
    private function isRealName()
    {
        $userInfoModel = new User_InfoModel();
        $user_info = $userInfoModel->getOne(Perm::$userId);
        
        return $user_info['user_identity_statu'] == 2
            ? true
            :false;
    }
    
    //获取用户支付密码状态
    private function getUserPaymentPassword()
    {
        $userBaseModel = new User_BaseModel;
        $user_data = $userBaseModel->getOne(Perm::$userId);
        
        return empty($user_data['user_pay_passwd'])
            ? false
            :true;
    }
    
    public function getWithdrawalData()
    {
        if (!$this->isRealName()) {
            return $this->data->setError('未实名，请实名后重试');
        }
        //获取服务费用
        $serviceFeeModel = new Service_FeeModel();
        $service_fee_list = array_values($serviceFeeModel->getFee("*"));
        $userResourceModel = new User_ResourceModel;
        $user_resource_data = $userResourceModel->getOne(Perm::$userId);
        $identification = $this->getUserPaymentPassword()
            ? 1
            :0;
        $data = [
            'service_fee' => $service_fee_list,
            'user_resource' => $user_resource_data,
            'identification' => $identification
        ];
        //获取用户支付密码状态
        $this->data->addBody(-140, $data, 'success', 200);
    }
    
    //提现记录页面 Withdrawlist
    public function withdrawlist()
    {
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows'):10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        //$user_id = 1;
        $user_id = Perm::$userId;
        $cond_row['pay_uid'] = $user_id;
        $consume_withdraw_list = $this->Consume_WithdrawModel->getWithdrawList($cond_row, ["add_time" => "DESC"], $page, $rows);
        $Yf_Page->totalRows = $consume_withdraw_list['totalsize'];
        $page_nav = $Yf_Page->prompt();
        include $this->view->getView();
    }
    
    //转账页面 Transfer
    public function transfer()
    {
        //判断是否设置过支付密码
        $user_info = Perm::$row;
        if (!isset($user_info['user_pay_passwd'])) {
            $user_result = $this->User_BaseModel->getOneByWhere(perm::$userId);
            $user_info['user_pay_passwd'] = $user_result['user_pay_passwd'];
        }
        if (!$user_info['user_pay_passwd']) {
            $url = Yf_Registry::get('url') . '?ctl=Info&met=passwd&typ=e';
            location_to($url);
            exit;
        }
        include $this->view->getView();
    }
    
    //转账记录页面
    public function transferlist()
    {
        //$user_id = 1;
        //获取用户资产
        $user_id = Perm::$userId;
        $user_resource = $this->User_ResourceModel->getResource($user_id);
        $user_resource = current($user_resource);
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows'):10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $cond_row['user_id'] = $user_id;
        $cond_row['trade_type_id'] = 2;
        $consume_record_list = $this->Consume_RecordModel->getRecordList1($cond_row, ["record_time" => "DESC"], $page, $rows);
        $Yf_Page->totalRows = $consume_record_list['totalsize'];
        $page_nav = $Yf_Page->prompt();
        include $this->view->getView();
    }
    
    public function pay()
    {
        $shop_id_cookie = request_int('shop_id_cookie');
        $shop_id_url = request_string('shop_id_url');
        $user_id = Perm::$userId;
        $uorder = request_string('uorder');
        $act = request_string('act');
        //用于判断订单类型，order_g_type = physical实物订单，virtual虚拟订单
        $order_g_type = request_string('order_g_type') ? request_string('order_g_type'):'physical';
        //获取需要支付的订单信息
        $Union_OrderModel = new Union_OrderModel();
        $uorder_base = $Union_OrderModel->getOne($uorder);

        if($uorder_base['is_presale']==1&&$uorder_base['order_state_id']==1){
            $uorder_base['trade_payment_amount'] = $uorder_base['presale_deposit'];
        }
        if($uorder_base['is_presale']==1&&$uorder_base['order_state_id']==20){
            $uorder_base['trade_payment_amount'] = $uorder_base['final_price'];
        }
        
        $Consume_TradeModel = new Consume_TradeModel();
        if($shop_id_cookie){
            $Consume_TradeModel->editTrade($uorder_base['inorder'],array('shop_id_cookie'=>$shop_id_cookie,'shop_id_url'=>$shop_id_url));
        }

        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = [];
        $formvars['app_id'] = $shop_app_id;
        $formvars['order_id'] = $uorder_base['inorder'];
        //如果是分销订单
        $User_InfoModel = new User_InfoModel();
        if ($act != 'deposit') {
            //查找订单信息
            $Consume_TradeModel = new Consume_TradeModel();
            $consume_trade = $Consume_TradeModel->getOne($uorder_base['inorder']);
            if ($consume_trade['pay_user_id'] == Perm::$userId && $consume_trade['buyer_id'] != Perm::$userId) {
                //查找子账号的用户名
                $buyer_user_info = $User_InfoModel->getUserInfo($consume_trade['buyer_id']);
                //查找订单的商品信息
                $order_goods = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=getGoodsByOrderId&typ=json', $url), $formvars);
                $order_goods = array_values($order_goods['data']);
            }
        }
        //查询可使用的支付方式
        $Payment_ChannelModel = new Payment_ChannelModel();
        $payment_channel = $Payment_ChannelModel->getByWhere(['payment_channel_enable' => Payment_ChannelModel::ENABLE_YES]);
        $payment_channel = array_values($payment_channel);
        //查询该用户是否存在主管账号
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = [];
        $formvars['app_id'] = $shop_app_id;
        $formvars['sub_user_id'] = Perm::$userId;
        $sub_user = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=getSubUser&typ=json', $url), $formvars);
        $sub_user_status = false;
        if ($sub_user['status'] == 200 && $sub_user['data']['count'] > 0) {
            $sub_user_status = true;
        }
        //获取当前用户的资金
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getOne($user_id);
        //去掉不可用图标显示的支付方式
        //微信比较提别，这里的微信图标统一使用wx_native，客户端监听到微信并调取客户端时，再根据应用场景传对应参数调取app_wx_native或app_h5_wx_native
        $pay_channel = ['cards', 'money', 'app_wx_native', 'app_h5_wx_native', 'alipayMobile'];
        foreach ($payment_channel as $key => $val) {
            if ($val['payment_channel_code'] === 'baitiao') {
                $bt_info = $val;
                unset($payment_channel[$key]);
            }
            if (in_array($val['payment_channel_code'], $pay_channel)) {
                unset($payment_channel[$key]);
            }
        }
        if (isset($bt_info)) {
            //获取额度信息和认证信息
            $user_info = $User_InfoModel->getUserInfo($user_id);
            $data = [];
            if ($user_info['user_bt_status'] == 2) {
                $user_resource_model = new User_ResourceModel();
                $result = $user_resource_model->getOne($user_id);
                $bt_money = $result['user_credit_availability'];
                $bt_type = $result['bt_type'];
                //查看是否逾期,如果逾期白条不可用
                $bt_use = $this->checkBtUse();
            }
        }

        //是否开启大华捷通支付
        $yunshan_status = Web_ConfigModel::value('yunshan_status');
        if ($yunshan_status) {
            $payment_channel = array(
               0 => array(
                'payment_channel_code' => 'yunshanpc',
                'payment_channel_name' => '大华捷通支付',
                'payment_channel_image' => 'paycenter/static/default/images/yunshanpc.png'
               )
            );

            $is_mobile = self::is_mobile();
            if ($is_mobile=='mobile'){
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                    //微信内置浏览器打开网页

                }else{
                    if(!$_COOKIE['is_app_guest']){
                        $payment_channel = array_values($payment_channel);
                        foreach ($payment_channel as $key => $value) {
                            if ($value['payment_channel_code']=='wx_native') {
                                unset($payment_channel[$key]);
                            }
                        }
                        $count_num = count($payment_channel);
                        $payment_channel = array_values($payment_channel);
                        for ($i=0; $i <$count_num ; $i++) {
                            $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                            if ($payment_channel[$i]['payment_channel_code']=='yunshanpc'){
                                for ($i=0; $i <$count_num ; $i++) {
                                    $payment_channel[$i]['payment_channel_code'] = 'yunshanpc';
                                }
                            }
                        }
                    }else{
                        $count_num = count($payment_channel);
                        $payment_channel = array_values($payment_channel);
                        for ($i=0; $i <$count_num ; $i++) {
                            $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                            if ($payment_channel[$i]['payment_channel_code']=='yunshanpc'){
                                for ($i=0; $i <$count_num ; $i++) {
                                    $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                                    $payment_channel[$i]['payment_channel_code'] = 'yunshanpc';
                                }
                            }
                        }
                    }
                }
                foreach($payment_channel as $key=>&$val){
                    if($val['payment_channel_code']=='alipay'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/zhifubao_app.png';
                    }else if($val['payment_channel_code']=='wx_native'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/weixin_app.png';
                    }else if($val['payment_channel_code']=='yunshanpc'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/yunshan_app.png';
                    }
                }
                // echo '<pre>';
                // print_r($payment_channel);
                // echo '</pre>';
                $this->view->setMet('pay_app');

            }

        }
        include $this->view->getView();
    }
    
    public function pay_api()
    {
        $shop_id_cookie = request_int('shop_id_cookie');
        $shop_id_url = request_string('shop_id_url');
        $return_url = request_string('callbackurl');
        $user_id = Perm::$userId;
        $uorder = request_string('uorder');
        $act = request_string('act');
        //用于判断订单类型，order_g_type = physical实物订单，virtual虚拟订单
        $order_g_type = request_string('order_g_type') ? request_string('order_g_type'):'physical';
        //获取需要支付的订单信息
        $Union_OrderModel = new Union_OrderModel();
        $uorder_base = $Union_OrderModel->getOne($uorder);

        if($uorder_base['is_presale']==1&&$uorder_base['order_state_id']==1){
            $uorder_base['trade_payment_amount'] = $uorder_base['presale_deposit'];
        }
        if($uorder_base['is_presale']==1&&$uorder_base['order_state_id']==20){
            $uorder_base['trade_payment_amount'] = $uorder_base['final_price'];
        }
        
        $Consume_TradeModel = new Consume_TradeModel();
        if($shop_id_cookie){
            $Consume_TradeModel->editTrade($uorder_base['inorder'],array('shop_id_cookie'=>$shop_id_cookie,'shop_id_url'=>$shop_id_url));
        }

        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = [];
        $formvars['app_id'] = $shop_app_id;
        $formvars['order_id'] = $uorder_base['inorder'];
        //如果是分销订单
        $User_InfoModel = new User_InfoModel();
        if ($act != 'deposit') {
            //查找订单信息
            $Consume_TradeModel = new Consume_TradeModel();
            $consume_trade = $Consume_TradeModel->getOne($uorder_base['inorder']);
            if ($consume_trade['pay_user_id'] == Perm::$userId && $consume_trade['buyer_id'] != Perm::$userId) {
                //查找子账号的用户名
                $buyer_user_info = $User_InfoModel->getUserInfo($consume_trade['buyer_id']);
                //查找订单的商品信息
                $order_goods = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=getGoodsByOrderId&typ=json', $url), $formvars);
                $order_goods = array_values($order_goods['data']);
            }
        }
        //查询可使用的支付方式
        $Payment_ChannelModel = new Payment_ChannelModel();
        $payment_channel = $Payment_ChannelModel->getByWhere(['payment_channel_enable' => Payment_ChannelModel::ENABLE_YES]);
        $payment_channel = array_values($payment_channel);
        //查询该用户是否存在主管账号
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = [];
        $formvars['app_id'] = $shop_app_id;
        $formvars['sub_user_id'] = Perm::$userId;
        $sub_user = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=getSubUser&typ=json', $url), $formvars);
        $sub_user_status = false;
        if ($sub_user['status'] == 200 && $sub_user['data']['count'] > 0) {
            $sub_user_status = true;
        }
        //获取当前用户的资金
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getOne($user_id);
        //去掉不可用图标显示的支付方式
        //微信比较提别，这里的微信图标统一使用wx_native，客户端监听到微信并调取客户端时，再根据应用场景传对应参数调取app_wx_native或app_h5_wx_native
        $pay_channel = ['cards', 'money', 'app_wx_native', 'app_h5_wx_native', 'alipayMobile'];
        foreach ($payment_channel as $key => $val) {
            if ($val['payment_channel_code'] === 'baitiao') {
                $bt_info = $val;
                unset($payment_channel[$key]);
            }
            if (in_array($val['payment_channel_code'], $pay_channel)) {
                unset($payment_channel[$key]);
            }
        }
        if (isset($bt_info)) {
            //获取额度信息和认证信息
            $user_info = $User_InfoModel->getUserInfo($user_id);
            $data = [];
            if ($user_info['user_bt_status'] == 2) {
                $user_resource_model = new User_ResourceModel();
                $result = $user_resource_model->getOne($user_id);
                $bt_money = $result['user_credit_availability'];
                $bt_type = $result['bt_type'];
                //查看是否逾期,如果逾期白条不可用
                $bt_use = $this->checkBtUse();
            }
        }

        //是否开启大华捷通支付
        $yunshan_status = Web_ConfigModel::value('yunshan_status');
        if ($yunshan_status) {
            $payment_channel = array(
               0 => array(
                'payment_channel_code' => 'yunshanpc',
                'payment_channel_name' => '大华捷通支付',
                'payment_channel_image' => 'paycenter/static/default/images/yunshanpc.png'
               )
            );

            $is_mobile = self::is_mobile();
            if ($is_mobile=='mobile'){
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                    //微信内置浏览器打开网页

                }else{
                    if(!$_COOKIE['is_app_guest']){
                        $payment_channel = array_values($payment_channel);
                        foreach ($payment_channel as $key => $value) {
                            if ($value['payment_channel_code']=='wx_native') {
                                unset($payment_channel[$key]);
                            }
                        }
                        $count_num = count($payment_channel);
                        $payment_channel = array_values($payment_channel);
                        for ($i=0; $i <$count_num ; $i++) {
                            $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                            if ($payment_channel[$i]['payment_channel_code']=='yunshanpc'){
                                for ($i=0; $i <$count_num ; $i++) {
                                    $payment_channel[$i]['payment_channel_code'] = 'yunshanpc';
                                }
                            }
                        }
                    }else{
                        $count_num = count($payment_channel);
                        $payment_channel = array_values($payment_channel);
                        for ($i=0; $i <$count_num ; $i++) {
                            $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                            if ($payment_channel[$i]['payment_channel_code']=='yunshanpc'){
                                for ($i=0; $i <$count_num ; $i++) {
                                    $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                                    $payment_channel[$i]['payment_channel_code'] = 'yunshanpc';
                                }
                            }
                        }
                    }
                }
                foreach($payment_channel as $key=>&$val){
                    if($val['payment_channel_code']=='alipay'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/zhifubao_app.png';
                    }else if($val['payment_channel_code']=='wx_native'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/weixin_app.png';
                    }else if($val['payment_channel_code']=='yunshanpc'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/yunshan_app.png';
                    }
                }
                // echo '<pre>';
                // print_r($payment_channel);
                // echo '</pre>';
                $this->view->setMet('pay_app');

            }

        }
        include $this->view->getView();
    }



    /**
     * 判断支付端口
     * 
     * @dateTime  2020-06-03
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
    private  static  function is_mobile(){
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
                substr($useragent, 0, 4))) {
            return "mobile";
        } else {
            return "PC";
        }
    }
    //检查白条是否可用
    public function checkBtUse(){
        $bt_use = 0; //默认白条不可用
        $cond_row = array();
        $cond_row['pay_user_id'] = Perm::$userId;
        $cond_row['payment_channel_id'] = 9;
        $cond_row['trade_payment_status'] = 0;
        $cond_row['repayment_time:<='] = date('Y-m-d').' 23:59:59';
        $Consume_TradeModel = new Consume_TradeModel();
        $consume_trade = $Consume_TradeModel->getOneByWhere($cond_row);
        if (empty($consume_trade)) {
            $bt_use = 1;
        }
        return $bt_use;
    }
    //加载H5支付结果页面
    public function h5_pay()
    {
        $r_url = request_string('r_url');
        if ($r_url) {
                $ucenter_user_info =array();
                $order_id = request_string('order_id');
                include $this->view->getView();
        } else {
            $trade_id = request_string('trade_id');
            $user_id   = Perm::$userId;
            $db = new YFSQL();
            $sql = "select * from ucenter_user_info where user_id=" . $user_id;
            $ucenter_user_info_get = $db->find($sql);
            $ucenter_user_info = current($ucenter_user_info_get);
            //获取订单id
            $Union_OrderModel = new Union_OrderModel();
            $trade_row = $Union_OrderModel->getOne($trade_id);
            $order_id = $trade_row['inorder'];
            include $this->view->getView();
        }
        
    }


    public function wx_paystatus(){
        $order_id = request_string('order_id');
        $PaymentModel = new Payment_JhWxAppModel();
        $paystatus = $PaymentModel->paystatus($order_id);
        $Union_OrderModel = new Union_OrderModel();
        $trade_row = $Union_OrderModel->getOneByWhere(array("inorder"=>$order_id)); 
        $r_url =htmlspecialchars_decode($trade_row['r_url']);
        if ($r_url) {
            $appToken = $_COOKIE["appToken"];
            if ($paystatus['data']['status'] == 1) {
                $url = $r_url . "&appToken=" . $appToken . "&order_id=" . $order_id . "&order_status=2";
            } else {
                $url = $r_url . "&appToken=" . $appToken . "&order_id=" . $order_id . "&order_status=1";
            }    
        } else {
            $url = Yf_Registry::get('shop_wap_url') ;
        }
        //付款成功提醒 
        /*中酷消息推送begin*/
        $db = new YFSQL();
        $userModel = new User_BaseModel();
        $sql = "select * from yf_order_base where order_id='" . $order_id . "'";
        $order_base_get = $db->find($sql);
        $order_base = current($order_base_get);
        $token = request_string('token');
        $enterId = request_string('enterId');
        $ZkSms = new ZkSms();
        $getToken = $ZkSms->token($token,$enterId);
        if ($getToken) {
            if ($paystatus['data']['status'] == 1) {
                $content = "尊敬的用户" . $order_base['buyer_user_name'] . ",订单编号为："  . $order_base['order_id'] . "的订单已支付成功。";
            } else {
                 $content = "尊敬的用户" . $order_base['buyer_user_name'] . ",订单编号为："  . $order_base['order_id'] . "的订单支付失败。";
            } 
            $receivers[0] = $order_base['buyer_user_id'];
            $msg = array(
                "msgType"=>1,
                "noticeType"=>1,
                "templateCode"=>"pure_text_bill",
                "businessId"=>1,
                "subject"=>"订单支付",
                "content"=> $content,
                "enterName"=>"订单支付",
                'sender'=> $getToken['u_id'],
                "receivers"=> $receivers
            );
            $message = $ZkSms->simba_business_notice_send($getToken['token'], $msg,$order_base['order_from']);
        }
        /*中酷消息推送end*/
        header("Location:" . $url);
    }

    public function yl_paystatus(){
        $order_id = request_string('order_id');
        $Payment_JhYlAppModel = new Payment_JhYlAppModel();
        $paystatus = $Payment_JhYlAppModel->paystatus($order_id);
        $Union_OrderModel = new Union_OrderModel();
        $trade_row = $Union_OrderModel->getOneByWhere(array("inorder"=>$order_id)); 
        $r_url =htmlspecialchars_decode($trade_row['r_url']);
        if ($r_url) {
            $appToken = request_string('appToken');
            if ($paystatus['data']['status'] == 1) {
                $status = 200;
                $msg = "success";
                $url = $r_url . "&appToken=" . $appToken . "&order_id=" . $order_id . "&order_status=2";
            } else {
                $status = 250;
                $msg = "failure";
                $url = $r_url . "&appToken=" . $appToken . "&order_id=" . $order_id . "&order_status=1";
            }    
        } else {
            if ($paystatus['data']['status'] == 1) {
                $status = 200;
                $msg = "success";
                $url = Yf_Registry::get('shop_wap_url') ;
            } else {
                $status = 250;
                $msg = "failure";
                $url = Yf_Registry::get('shop_wap_url') ;
            } 
            
        }
        $data['url'] = $url;
        //付款成功提醒 
        /*中酷消息推送begin*/
        $db = new YFSQL();
        $userModel = new User_BaseModel();
        $sql = "select * from yf_order_base where order_id='" . $order_id . "'";
        $order_base_get = $db->find($sql);
        $order_base = current($order_base_get);
        $token = request_string('token');
        $enterId = request_string('enterId');
        $ZkSms = new ZkSms();
        $getToken = $ZkSms->token($token,$enterId);
        if ($getToken) {
            if ($paystatus['data']['status'] == 1) {
                $content = "尊敬的用户" . $order_base['buyer_user_name'] . ",订单编号为："  . $order_base['order_id'] . "的订单已支付成功。";
            } else {
                 $content = "尊敬的用户" . $order_base['buyer_user_name'] . ",订单编号为："  . $order_base['order_id'] . "的订单支付失败。";
            } 
            $receivers[0] = $order_base['buyer_user_id'];
            $msg = array(
                "msgType"=>1,
                "noticeType"=>1,
                "templateCode"=>"pure_text_bill",
                "businessId"=>1,
                "subject"=>"订单支付",
                "content"=> $content,
                "enterName"=>"订单支付",
                'sender'=> $getToken['u_id'],
                "receivers"=> $receivers
            );
            $message = $ZkSms->simba_business_notice_send($getToken['token'], $msg,$order_base['order_from']);
        }

        /*中酷消息推送end*/
        // header("Location:" . $url);
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //支付成功页
    public function after_pay()
    { 
        $type = request_string("type");
        $Consume_TradeModel = new Consume_TradeModel();
        $order_id = request_string('order_id');
        $return_url = request_string('return_url');
        $r_url = request_string('r_url');
        //付款用户name
        $consume_trade = $Consume_TradeModel->getOne($order_id);
        $user_name = Perm::$row['user_account'];
        if ("api" == $type) {
            header("Location:" . $return_url . "&order_id=" . $order_id . "&order_status=2");
        } else {
            include $this->view->getView();
        } 
    }



    public function checkPayWay()
    {
        $card_payway = request_string('card_payway');
        $money_payway = request_string('money_payway');
        $online_payway = request_string('online_payway');
        $bt_payway = request_string('bt_payway');
        $uorder_id = request_string('uorder_id');
        //查找订单的支付信息
        $Union_OrderModel = new Union_OrderModel();
        //开启事物
        $Union_OrderModel->sql->startTransactionDb();
        $uorder_base = $Union_OrderModel->getOne($uorder_id);
        $urow = $Union_OrderModel->getByWhere(['inorder' => $uorder_base['inorder']]);
        $uorder_id_row = array_column($urow, 'union_order_id');
        //订单支付的总金额
        $payment_amount = $uorder_base['trade_payment_amount'];
        $user_card_pay = 0;
        $user_money_pay = 0;
        $user_online_pay = 0;

        //使用充值卡或账户余额支付时，查找账户的资源资源信息
        if ($card_payway == 'true' || $money_payway == 'true') {
            $User_ResourceModel = new User_ResourceModel();
            $user_resource = $User_ResourceModel->getOne(Perm::$userId);
            $user_money = $user_resource['user_money'];
            $user_card = $user_resource['user_recharge_card'];
            //使用充值卡支付
            if ($card_payway == 'true') {
                if ($user_card <= $payment_amount) {
                    $user_card_pay = $user_card;
                    $payment_amount = $payment_amount - $user_card;
                } else {
                    $user_card_pay = $payment_amount;
                    $payment_amount = 0;
                }
            }

            //使用账户余额支付
            if ($money_payway == 'true') {
                if ($user_money <= $payment_amount) {
                    $user_money_pay = $user_money;
                    $payment_amount = $payment_amount - $user_money_pay;
                } else {
                    $user_money_pay = $payment_amount;
                    $payment_amount = 0;
                }
            }
        }

        if ($online_payway) {
            $user_online_pay = $payment_amount;
        }
        //白条支付
        $rs_row = [];
        if ($bt_payway == 'true') {
            //白条不可与其他支付方式一起使用
            if ($card_payway == 'true' || $money_payway == 'true' || $online_payway == 'true') {
                $flag1 = false;
            } else {
                //修改订单支付方式
                $order_id_row = trim($uorder_base['inorder'], ',');
                $Consume_TradeModel = new Consume_TradeModel();
                $bt_use = $this->checkBtUse();
                if ($bt_use==0) {
                   return $this->data->addBody(-140, [], __('暂不可用，请及时还款已逾期欠款'), 250);
                }
                $flag1 = $Consume_TradeModel->editConsumeTrade($order_id_row, ['payment_channel_id' => Payment_ChannelModel::BAITIAO]);
                check_rs($flag1, $rs_row);
            }
        }
        //将用户的付款信息插入表中
        $edit_union_order_row['union_cards_pay_amount'] = $user_card_pay;
        $edit_union_order_row['union_money_pay_amount'] = $user_money_pay;
        $edit_union_order_row['union_online_pay_amount'] = $user_online_pay;
        if ($user_card_pay != 0 || $user_money_pay != 0 || $user_online_pay != 0) {
            $Union_OrderModel = new Union_OrderModel();
            $flag = $Union_OrderModel->editUnionOrder($uorder_id_row, $edit_union_order_row);
            check_rs($flag, $rs_row);
        }
        if (is_ok($rs_row) && $Union_OrderModel->sql->commitDb()) {
            $msg = 'success';
            $status = 200;
        } else {
            $Union_OrderModel->sql->rollBackDb();
            $m = $Union_OrderModel->msg->getMessages();
            $msg = $m ? $m[0]:_('failure');
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //添加充值记录
    public function addDeposit()
    {
        $deposit_amount = request_float('deposit_amount');
        $Union_OrderModel = new Union_OrderModel();
        //开启事务
        $Union_OrderModel->sql->startTransactionDb();
        //生成合并支付订单
        $uorder = "U" . date("Ymdhis", time()) . rand(100, 999);  //18位
        $trade_title = $uorder;
        $uprice = $deposit_amount;
        $buyer = Perm::$userId;
        $buyer_name = Perm::$row['user_account'];
        $add_row = [
            'union_order_id' => $uorder,
            'trade_title' => $trade_title,
            'trade_payment_amount' => $uprice,
            'create_time' => date("Y-m-d H:i:s"),
            'buyer_id' => $buyer,
            'order_state_id' => Union_OrderModel::WAIT_PAY,
            'union_online_pay_amount' => $uprice,
            'trade_type_id' => Trade_TypeModel::DEPOSIT,
            'app_id' => Yf_Registry::get('paycenter_app_id'),
        ];
        $flag = $Union_OrderModel->addUnionOrder($add_row);
        //添加充值表
        $Consume_DepositModel = new Consume_DepositModel();
        $add_deposit_row = [];
        $add_deposit_row['deposit_trade_no'] = $uorder;
        $add_deposit_row['deposit_buyer_id'] = $buyer;
        $add_deposit_row['deposit_total_fee'] = $deposit_amount;
        $add_deposit_row['deposit_gmt_create'] = date('Y-m-d H:i:s');
        $add_deposit_row['deposit_trade_status'] = RecordStatusModel::IN_HAND;
        $Consume_DepositModel->addDeposit($add_deposit_row);
        //添加交易明细
        $Consume_RecordModel = new Consume_RecordModel();
        $Trade_TypeModel = new Trade_TypeModel();
        $record_add_buy_row = [];
        $record_add_buy_row['order_id'] = $uorder;
        $record_add_buy_row['user_id'] = $buyer;
        $record_add_buy_row['user_nickname'] = $buyer_name;
        $record_add_buy_row['record_money'] = $deposit_amount;
        $record_add_buy_row['record_date'] = date('Y-m-d');
        $record_add_buy_row['record_year'] = date('Y');
        $record_add_buy_row['record_month'] = date('m');
        $record_add_buy_row['record_day'] = date('d');
        $record_add_buy_row['record_title'] = $Trade_TypeModel->trade_type[Trade_TypeModel::DEPOSIT];
        $record_add_buy_row['record_time'] = date('Y-m-d H:i:s');
        $record_add_buy_row['trade_type_id'] = Trade_TypeModel::DEPOSIT;
        $record_add_buy_row['user_type'] = 1;    //收款方
        $record_add_buy_row['record_status'] = RecordStatusModel::IN_HAND;
        $Consume_RecordModel->addRecord($record_add_buy_row);
        if ($flag && $Union_OrderModel->sql->commitDb()) {
            $msg = 'success';
            $status = 200;
        } else {
            $Union_OrderModel->sql->rollBackDb();
            $m = $Union_OrderModel->msg->getMessages();
            $msg = $m ? $m[0]:_('failure');
            $status = 250;
        }
        $data = ['uorder' => $uorder];
        if ($_REQUEST['returnData'] == 1) {
            return $data;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    public function checkCardPasswor()
    {
        $card_code = request_string('card_code');
        $card_password = request_string('card_password');
        $Card_InfoModel = new Card_InfoModel();
        $card_info = $Card_InfoModel->getOne($card_code);
        if ($card_info) {
            if ($card_info['card_password'] == $card_password) {
                $flag = true;
            } else {
                $m = '支付卡密码错误';
                $flag = false;
            }
        } else {
            $m = '支付卡不存在';
            $flag = false;
        }
        if ($flag) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = $m ? $m:'failure';
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    public function depositCard()
    {
        $card_code = request_string('card_code');
        $user_id = Perm::$userId;
        //1.改变支付卡的使用情况
        $Card_InfoModel = new Card_InfoModel();
        $Card_BaseModel = new Card_BaseModel();
        $card_info = $Card_InfoModel->getOne($card_code);
        if (!$card_info || $card_info['user_account']) {
            $msg = __('充值卡已失效');
            $status = 250;
            
            return $this->data->addBody(-140, [], $msg, $status);
        }
        $card_base = $Card_BaseModel->getOne($card_info['card_id']);
        if (!$card_base || $card_base['card_end_time'] < date('Y-m-d') || $card_base['card_start_time'] > date('Y-m-d')) {
            $msg = __('充值卡未在有效期');
            $status = 250;
            
            return $this->data->addBody(-140, [], $msg, $status);
        }
        //开启事务
        $Card_InfoModel->sql->startTransactionDb();
        $card_prize = $card_base['card_prize'];
        $money = isset($card_prize['m']) ? $card_prize['m']:0;
        $edit_card_row = [];
        $edit_card_row['card_fetch_time'] = date('Y-m-d H:i:s');
        $edit_card_row['user_id'] = $user_id;
        $edit_card_row['user_account'] = Perm::$row['user_account'];
        $edit_card_row['server_id'] = get_ip();
        $Card_InfoModel->editInfo($card_code, $edit_card_row);
        //2.添加充值表
        $Consume_DepositModel = new Consume_DepositModel();
        $add_deposit_row = [];
        $add_deposit_row['deposit_trade_no'] = $card_code;
        $add_deposit_row['deposit_buyer_id'] = $user_id;
        $add_deposit_row['deposit_total_fee'] = $money;
        $add_deposit_row['deposit_gmt_create'] = date('Y-m-d H:i:s');
        $add_deposit_row['deposit_trade_status'] = RecordStatusModel::RECORD_FINISH;
        $Consume_DepositModel->addDeposit($add_deposit_row);
        //3.添加交易明细
        $Consume_RecordModel = new Consume_RecordModel();
        $Trade_TypeModel = new Trade_TypeModel();
        $record_add_buy_row = [];
        $record_add_buy_row['order_id'] = $card_code;
        $record_add_buy_row['user_id'] = $user_id;
        $record_add_buy_row['user_nickname'] = Perm::$row['user_account'];
        $record_add_buy_row['record_money'] = $money;
        $record_add_buy_row['record_date'] = date('Y-m-d');
        $record_add_buy_row['record_year'] = date('Y');
        $record_add_buy_row['record_month'] = date('m');
        $record_add_buy_row['record_day'] = date('d');
        $record_add_buy_row['record_title'] = $Trade_TypeModel->trade_type[Trade_TypeModel::DEPOSIT];
        $record_add_buy_row['record_time'] = date('Y-m-d H:i:s');
        $record_add_buy_row['trade_type_id'] = Trade_TypeModel::DEPOSIT;
        $record_add_buy_row['user_type'] = 1;    //收款方
        $record_add_buy_row['record_status'] = RecordStatusModel::RECORD_FINISH;
        $Consume_RecordModel->addRecord($record_add_buy_row);
        //4.修改用户的充值卡金额
        $User_ResourceModel = new User_ResourceModel();
        $flag = $User_ResourceModel->editResource($user_id, ['user_recharge_card' => $money], true);
        if ($flag && $Card_InfoModel->sql->commitDb()) {
            $msg = __("充值成功");
            $status = 200;
        } else {
            $Card_InfoModel->sql->rollBackDb();
            $m = $Card_InfoModel->msg->getMessages();
            $msg = $m ? $m[0]:__("充值失败");
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    public function security()
    {
        include $this->view->getView();
    }
    
    public function account()
    {
        $key = Yf_Registry::get('ucenter_api_key');;
        $url = Yf_Registry::get('ucenter_api_url');
        fb($url);
        $app_id = Yf_Registry::get('ucenter_app_id');
        $data = [];
        $data['app_id'] = $app_id;
        $data['ctl'] = 'Api';
        $data['met'] = 'getUserInfo';
        $data['typ'] = 'json';
        $data['user_id'] = Perm::$userId;
        $init_rs = get_url_with_encrypt($key, $url, $data);
        $listarr = $init_rs['data'];
        $user_id = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($user_id);
        $user_status_info = $User_InfoModel->getUserInfo($user_id);
        $User_BaseModl = new User_BaseModel();
        $user_base_info = $User_BaseModl->getOne($user_id);
        if ($user_status_info['user_identity_statu_con'] == '未认证') {
            $url = Yf_Registry::get('url') . '?ctl=Info&met=certification&typ=e';
        } else {
            $url = Yf_Registry::get('url') . '?ctl=Info&met=passwd';
        }
        include $this->view->getView();
    }
    
    //修改支付密码
    public function passwd()
    {
        //获取用户信息
        $user_id = Perm::$userId;
        $data = $this->User_InfoModel->getOne($user_id);
        $from = request_string('from');
        include $this->view->getView();
    }
    
    //实名认证
    public function certification()
    {
        //获取用户信息
        $user_id = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $data = $User_InfoModel->getOne($user_id);
        $from = request_string('from');
        if ($this->typ == 'json') {
            return $this->data->addBody(-140, $data);
        }
        include $this->view->getView();
    }

    /**
     * wap端手机上传
     * 
     * @return 图片返回url
     */
    public function upload()
    {
        //获取用户信息
        $user_id = Perm::$userId;
        $paycenter_url = Yf_Registry::get('paycenter_api_url');
        $url_path = '/image.php/paycenter/data/upload/media/plantform/image/'.date('Ymd');
        if (isset($_FILES)) {
            if ($_FILES["file"]["error"]>0) {
                return $this->data->addBody(-140, [] ,__('上传错误，请重新上传！'));
            }else{
                $path = __(APP_PATH) . '/data/upload/media/plantform/image/'.date('Ymd');
                $filename = get_time().$_FILES["file"]["name"];
                if(!is_dir($path)){
                    mkdir($path,0777,true);
                }
                $destination = $path.'/'.$filename;
                $result = move_uploaded_file($_FILES["file"]["tmp_name"],$destination);
                if ($result) {
                    $return_url = $paycenter_url.$url_path.'/'.$filename;
                    return $this->data->addBody(-140, [] ,__($return_url));
                }else{
                    return $this->data->addBody(-140, [] ,__('上传失败'));
                }
            }
        }else{
            return $this->data->addBody(-140, [] ,__('无效身份证图片！'));
        }    
    }
    
    //实名认证插入资料
    public function editCertification()
    {
        //获取用户信息
        $user_id = Perm::$userId;
        $edit['user_realname'] = request_string('user_realname');
        $edit['user_identity_card'] = request_string('user_identity_card');
        $edit['user_identity_type'] = request_string('user_identity_type');
        $longtime = request_int('longtime');
        if (!$edit['user_realname']) {
            return $this->data->addBody(-140, [], __('请填写真实姓名'), 250);
        }
        if (!$edit['user_identity_card']) {
            return $this->data->addBody(-140, [], __('请填写证件号'), 250);
        }
        $edit['user_identity_font_logo'] = trim(request_string('user_identity_font_logo'));
        $edit['user_identity_face_logo'] = trim(request_string('user_identity_face_logo'));
        if (!$edit['user_identity_font_logo'] || !$edit['user_identity_face_logo']) {
            return $this->data->addBody(-140, [], __('请上传证件照'), 250);
        }
        $edit['user_identity_start_time'] = request_string('user_identity_start_time');
        $edit['user_identity_end_time'] = request_string('user_identity_end_time');
        if (empty($longtime)) {
            if (strtotime($edit['user_identity_start_time']) < 0 || strtotime($edit['user_identity_end_time']) < time() || strtotime($edit['user_identity_start_time']) > time()) {
                return $this->data->addBody(-140, [], __('请填写正确的证件有效期'), 250);
            }
        }
        if ($longtime == 1) {
            $edit['user_identity_end_time'] = 1; //1为长期
        }
        $edit['user_identity_statu'] = 1;
        if (request_string('from') === 'bt') {
            //如果是白条，则更改白条状态
            $edit['user_bt_status'] = 1;
            $edit['user_btapply_time'] = date('Y-m-d H:i:s');
        }
        $User_InfoModel = new User_InfoModel();
        $flag = $User_InfoModel->editInfo($user_id, $edit);
        if ($flag !== false) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $data = [];
        
        return $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //添加合并支付订单信息pay_union_order
    public function addUnionOrder()
    {
        //生成合并支付订单号
        $uorder = "U" . date("Ymdhis", time()) . rand(100, 999);  //18位
        $inorder = request_string('inorder');
        $inorder = substr($inorder, 0, -1);
        $trade_title = request_string('trade_title');
        $uprice = request_float('uprice');
        $buyer = request_int('buyer');
        $buyer_name = request_string('buyer_name');
        $add_row = [
            'union_order_id' => $uorder,
            'inorder' => $inorder,
            'trade_payment_amount' => $uprice,
            'create_time' => time(),
            'buyer_id' => $buyer,
            'order_state_id' => Union_OrderModel::WAIT_PAY,
        ];
        $Union_OrderModel = new Union_OrderModel();
        $flag1 = $Union_OrderModel->addUnionOrder($add_row);
        if ($flag1) {
            //插入交易明细表
            $record_add_row = [];
            $record_add_row['order_id'] = $uorder;
            $record_add_row['user_id'] = $buyer;
            $record_add_row['user_nickname'] = $buyer_name;
            $record_add_row['record_money'] = $uprice;
            $record_add_row['record_date'] = date('Y-m-d');
            $record_add_row['record_title'] = '购物';
            $record_add_row['record_time'] = date('Y-m-d H:i:s');
            $record_add_row['trade_type_id'] = Trade_TypeModel::SHOPPING;
            $record_add_row['user_type'] = 2;
            $record_add_row['record_status'] = RecordStatusModel::IN_HAND;
            $Consume_RecordModel = new Consume_RecordModel();
            $flag = $Consume_RecordModel->addRecord($record_add_row);
        } else {
            $flag = false;
        }
        if ($flag) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $data = ['uorder' => $uorder];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //添加交易订单信息
    public function addConsumeTrade()
    {
        $consume_trade_id = request_string('consume_trade_id');
        $order_id = request_string('order_id');
        $buy_id = request_int('buy_id');
        $buyer_name = request_string('buyer_name');
        $seller_id = request_int('seller_id');
        $seller_name = request_string('seller_name');
        $order_state_id = request_int('order_state_id');
        $order_payment_amount = request_float('order_payment_amount');
        $trade_remark = request_string('trade_remark');
        $trade_create_time = request_string('trade_create_time');
        $trade_title = request_string('trade_title');
        $add_row = [];
        $add_row['consume_trade_id'] = $consume_trade_id;
        $add_row['order_id'] = $order_id;
        $add_row['buyer_id'] = $buy_id;
        $add_row['seller_id'] = $seller_id;
        $add_row['order_state_id'] = $order_state_id;
        $add_row['order_payment_amount'] = $order_payment_amount;
        $add_row['trade_type_id'] = Trade_TypeModel::SHOPPING;
        $add_row['trade_remark'] = $trade_remark;
        $add_row['trade_create_time'] =$trade_create_time;
        $add_row['trade_amount'] = $order_payment_amount;
        $add_row['trade_payment_amount'] = $order_payment_amount;
        //1.生成交易订单
        $Consume_TradeModel = new Consume_TradeModel();
        $flag = $Consume_TradeModel->addTrade($add_row);
        //2.生成合并支付订单
        $uorder = "U" . date("Ymdhis", time()) . rand(100, 999);  //18位
        $union_add_row = [
            'union_order_id' => $uorder,
            'inorder' => $order_id,
            'trade_payment_amount' => $order_payment_amount,
            'create_time' => time(),
            'buyer_id' => $buy_id,
            'order_state_id' => Union_OrderModel::WAIT_PAY,
        ];
        $Union_OrderModel = new Union_OrderModel();
        $Union_OrderModel->addUnionOrder($union_add_row);
        //3.生成交易明细（付款方，收款方）
        $Consume_RecordModel = new Consume_RecordModel();
        $record_add_buy_row = [];
        $record_add_buy_row['order_id'] = $order_id;
        $record_add_buy_row['user_id'] = $buy_id;
        $record_add_buy_row['user_nickname'] = $buyer_name;
        $record_add_buy_row['record_money'] = $order_payment_amount;
        $record_add_buy_row['record_date'] = date('Y-m-d');
        $record_add_buy_row['record_year'] = date('Y');
        $record_add_buy_row['record_month'] = date('m');
        $record_add_buy_row['record_day'] = date('d');
        $record_add_buy_row['record_title'] = '购物';
        $record_add_buy_row['record_time'] = date('Y-m-d H:i:s');
        $record_add_buy_row['trade_type_id'] = Trade_TypeModel::SHOPPING;
        $record_add_buy_row['user_type'] = 2;    //付款方
        $record_add_buy_row['record_status'] = RecordStatusModel::IN_HAND;
        $Consume_RecordModel->addRecord($record_add_buy_row);
        $record_add_seller_row = [];
        $record_add_seller_row['order_id'] = $order_id;
        $record_add_seller_row['user_id'] = $seller_id;
        $record_add_seller_row['user_nickname'] = $seller_name;
        $record_add_seller_row['record_money'] = $order_payment_amount;
        $record_add_seller_row['record_date'] = date('Y-m-d');
        $record_add_seller_row['record_year'] = date('Y');
        $record_add_seller_row['record_month'] = date('m');
        $record_add_seller_row['record_day'] = date('d');
        $record_add_seller_row['record_title'] = '购物';
        $record_add_seller_row['record_time'] = date('Y-m-d H:i:s');
        $record_add_seller_row['trade_type_id'] = Trade_TypeModel::SHOPPING;
        $record_add_seller_row['user_type'] = 1;    //收款方
        $record_add_seller_row['record_status'] = 1;
        $Consume_RecordModel->addRecord($record_add_seller_row);
        if ($flag) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //交易明细(待修改)
    public function getConsumeRecord()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 20);
        $type = request_string('type');   //交易分类  1收款方  2付款方
        $status = request_string('status'); //交易状态 1未付款 2等待发货 3未确认发货 4成功 5失败
        $user_id = Perm::$userId;
        $user_id = $user_id ? $user_id:request_int('user_id');
        //$user_id = 10001;
        $Consume_RecordModel = new Consume_RecordModel();
        $row = $Consume_RecordModel->getRecordList($user_id, $type, $status, $page, $rows);
        $this->data->addBody(-140, $row);
    }
    
    //提现记录   转账记录(1)
    public function getConsumeRecordByType()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 20);
        $user_id = Perm::$userId;
        //$user_id = 1;
        //const SHOPPING = 1;  //购物
        //const TRANSFER = 2;  //转账
        //const DEPOSIT  = 3; //充值
        //const WITHDRAW = 4;  //提现
        //const RECEIPT  = 5;  //收款
        //const PAY		= 6;   //付款
        $type = request_string('type');
        $Consume_RecordModel = new Consume_RecordModel();
        $row = $Consume_RecordModel->getRecordListByType($user_id, $type, $page, $rows);
        fb($row);
        $this->data->addBody(-140, $row);
    }
    
    //获取用户资源信息
    public function getUserResourceInfo()
    {
        $user_id = Perm::$userId;
        //$user_id = $user_id ? $user_id : request_int('user_id');
        $User_ResourceModel = new User_ResourceModel();
        $data = $User_ResourceModel->getOne($user_id);
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //获取用户基础信息
    public function getUserBase()
    {
        //$user_id = Perm::$userId;
        $cond_row['user_nickname'] = request_string("user_name");
        $data = $this->User_InfoModel->getOneByWhere($cond_row);
        if ($data) {
            $data['user_realname_mask'] = mb_substr($data['user_realname'], 0, 1, 'utf-8') . '***' . mb_substr($data['user_realname'], -1, 1, 'utf-8');
            $data['user_mobile_mask'] = substr($data['user_mobile'], 0, 3) . '***' . substr($data['user_mobile'], -3, 3);
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //获取用户信息（1）
    public function getUserInfo()
    {
        $user_id = Perm::$userId;
        //$user_id = 1;
        $User_InfoModel = new User_InfoModel();
        $data = $User_InfoModel->getInfo($user_id);
        $data = current($data);
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        
        return $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //修改用户信息(1)
    public function editUserInfo()
    {
        $user_id = Perm::$userId;
        //$user_id = 1;
        $user_info_row = [];
        //真实姓名
        $user_realname = request_string('user_realname');
        if ($user_realname) {
            $user_info_row['user_realname'] = $user_realname;
        }
        //用户昵称
        $user_nickname = request_string('user_nickname');
        if ($user_nickname) {
            $user_info_row['user_nickname'] = $user_nickname;
        }
        //手机号码
        $user_mobile = request_int('uer_mobile');
        if ($user_mobile) {
            $user_info_row['user_mobile'] = $user_mobile;
        }
        //用户邮箱
        $user_email = request_string('user_email');
        if ($user_email) {
            $user_info_row['user_email'] = $user_email;
        }
        $User_InfoModel = new User_InfoModel();
        $data = $User_InfoModel->editInfo($user_id, $user_info_row);
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $user_info_row, $msg, $status);
    }
    
    //修改用户支付密码(1)
    public function editUserPayPassword()
    {
        $user_id = Perm::$userId;
        //$user_id = 1;
        $user_base_row = [];
        $old_password = request_string('old_password');
        $set_password = request_string('set_password');
        $User_BaseModel = new User_BaseModel();
        $user_base = current($User_BaseModel->getBase($user_id));
        if (md5($old_password) == $user_base['user_pay_passwd']) {
            $user_base_row['user_pay_passwd'] = md5($set_password);
            $flag = $User_BaseModel->editBase($user_id, $user_base_row);
        } else {
            $flag = false;
        }
        if ($flag) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //提交提现申请(1)
    public function addWithdraw()
    {
        $user_id = Perm::$userId;
        //获取用户信息
        $edit['pay_uid'] = $user_id;
        $edit['bank_user'] = request_string('bank_name');//开户人姓名
        $edit['cardno'] = request_string('cardno');//银行卡号
        $edit['bank'] = request_string('bank');//银行
        $edit['amount'] = request_string('withdraw_money');//提现金额
        $edit['con'] = request_string('con');//提款说明
        $edit['service_fee_id'] = request_int('id');  //到账时间 1-2小时内到账  2-次日24点 3-次日48点
        $paypasswd = request_string('paypasswd');  //支付密码
        $data = [];
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $edit['amount']) || $edit['amount'] <= 0) {
            return $this->data->addBody(-140, $data, __('付款金额有误'), 250);
        }
        //获取用户信息
        $user_base = current($this->User_BaseModel->getBase($user_id));
        if (!$user_base['user_pay_passwd']) {
            return $this->data->addBody(-140, $data, __('请先设置支付密码'), 250);
        }
        if ($user_base['user_pay_passwd'] != MD5($paypasswd)) {
            return $this->data->addBody(-140, $data, __('支付密码错误'), 250);
        }
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($user_id);
        $user_resource = current($this->User_ResourceModel->getResource($user_id));
        $mobile = $user_info['user_mobile'];  //手机
        $yzm = request_string('yzm');  //验证码
        if (!VerifyCode::checkCode($mobile, $yzm)) {
            return $this->data->addBody(-140, $data, __('验证码错误'), 250);
        }
        $Service_FeeModel = new Service_FeeModel();
        $fee = current($Service_FeeModel->getFeeById($edit['service_fee_id']));
        $amount = $edit['amount'];//提现金额
        $num = 0;
        $price = $amount * ($fee['fee_rates'] * 0.01 * 1);//手续费
        if ($price > 0) {
            if ($price <= $fee['fee_min'] * 1) {
                $num = $fee['fee_min'] * 1;
            } elseif ($price >= $fee['fee_max'] * 1) {
                $num = $fee['fee_max'] * 1;
            } else {
                $num = $price;
            }
        }
        if ($amount + $num > $user_resource['user_money']) {
            return $this->data->addBody(-140, $data, __('余款不足'), 250);
        }
        $m = $amount + $num;
        //减少费用
        $resource_edit_row['user_money'] = $user_resource['user_money'] - $m;
        $resource_edit_row['user_money_frozen'] = $user_resource['user_money_frozen'] + $m;
        //开启事物
        $this->User_ResourceModel->sql->startTransactionDb();
        $result1 = $this->User_ResourceModel->editResource($user_id, $resource_edit_row);
        if (!$result1) {
            $this->User_ResourceModel->sql->rollBackDb();
            $data['code'] = 1;
            
            return $this->data->addBody(-140, $data, __('转账失败'), 250);
        }
        //插入交易明细表
        $flow_id = date("Ymdhis") . rand(0, 9);
        $record_row = [
            'order_id' => $flow_id,
            'user_id' => $user_id,
            'user_nickname'=>$user_info['user_nickname'],
            'record_money' => -$m,
            'record_date' => date("Y-m-d"),
            'record_year' => date("Y"),
            'record_month' => date("m"),
            'record_day' => date("d"),
            'record_title' => _('提现'),
            'record_time' => date('Y-m-d h:i:s'),
            'trade_type_id' => '4',
            'user_type' => '2',
        ];
        $record_id = $this->Consume_RecordModel->addRecord($record_row, true);
        if (!$record_id) {
            $this->User_ResourceModel->sql->rollBackDb();
            $data['code'] = 2;
            
            return $this->data->addBody(-140, $data, __('转账失败'), 250);
        }
        //插入提现申请表
        $widthdraw_row = [
            'pay_uid' => $user_id,
            'orderid' => $flow_id,
            'amount' => $amount,
            'add_time' => time(),
            'con' => $edit['con'],
            'bank' => $edit['bank'],
            'cardno' => $edit['cardno'],
            'cardname' => $edit['bank_user'],
            'supportTime' => $edit['service_fee_id'],
            'fee' => $num,
        ];
        $flag = $this->Consume_WithdrawModel->addWithdraw($widthdraw_row);
        if ($flag && $this->User_ResourceModel->sql->commitDb()) {
            $data = $widthdraw_row;
            
            return $this->data->addBody(-140, $data, __('操作成功'), 200);
        } else {
            $this->User_ResourceModel->sql->rollBackDb();
            $data['code'] = 2;
            
            return $this->data->addBody(-140, $data, __('操作失败'), 250);
        }
    }
    
    //转账(1)
    public function addTransfer()
    {
        $user_id = Perm::$userId;
        $data = [];
        $requirer = request_string('user_nickname');  //收款人
        $amount = $_REQUEST['record_money'];        //付款金额
        $reason = request_string('record_desc', '转账');  //付款说明
        $paypasswd = request_string('password');  //支付密码
        $mobile = request_string('mobile');  //支付密码
        $yzm = request_string('yzm');  //支付密码
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $amount) || $amount <= 0) {
            return $this->data->addBody(-140, $data, __('付款金额有误'), 250);
        }
        if (!VerifyCode::checkCode($mobile, $yzm)) {
            return $this->data->addBody(-140, $data, __('验证码错误'), 250);
        }
        //确认支付密码
        $user_base = current($this->User_BaseModel->getBase($user_id));
        if (!$user_base['user_pay_passwd']) {
            return $this->data->addBody(-140, $data, __('请先设置支付密码'), 250);
        }
        if ($user_base['user_pay_passwd'] != MD5($paypasswd)) {
            return $this->data->addBody(-140, $data, __('支付密码错误'), 250);
        }
        $user_resource = current($this->User_ResourceModel->getResource($user_id));
        if ($amount > $user_resource['user_money']) {
            return $this->data->addBody(-140, $data, __('余额不足'), 250);
        }
        //获取收款人的支付id
        $requirer_id = current($this->User_BaseModel->getBaseIdByAccount($requirer));
        if (!$requirer_id) {
            return $this->data->addBody(-140, $data, __('用户不存在'), 250);
        }
        $requirer_resource = current($this->User_ResourceModel->getResource($requirer_id));
        $flow_id = time();
        //插入付款方的交易记录
        $record_row1 = [
            'order_id' => $flow_id,
            'user_id' => $user_id,
            'record_money' => -$amount,
            'record_date' => date("Y-m-d"),
            'record_year' => date("Y"),
            'record_month' => date("m"),
            'record_day' => date("d"),
            'record_title' => $reason,
            'record_time' => date('Y-m-d h:i:s'),
            'trade_type_id' => '2',
            'user_type' => '2',
            'record_status' => '2',
        ];
        //开启事物
        $this->Consume_RecordModel->sql->startTransactionDb();
        $result1 = $this->Consume_RecordModel->addRecord($record_row1, true);
        if (!$result1) {
            $this->Consume_RecordModel->sql->rollBackDb();
            $data['code'] = 1;
            
            return $this->data->addBody(-140, $data, __('转账失败'), 250);
        }
        //插入收款方的交易记录
        $record_row2 = [
            'order_id' => $flow_id,
            'user_id' => $requirer_id,
            'record_money' => $amount,
            'record_date' => date("Y-m-d"),
            'record_year' => date("Y"),
            'record_month' => date("m"),
            'record_day' => date("d"),
            'record_title' => $reason,
            'record_time' => date('Y-m-d h:i:s'),
            'trade_type_id' => '2',
            'user_type' => '1',
            'record_status' => '2',
        ];
        $result2 = $this->Consume_RecordModel->addRecord($record_row2, true);
        if (!$result2) {
            $this->Consume_RecordModel->sql->rollBackDb();
            $data['code'] = 2;
            
            return $this->data->addBody(-140, $data, __('转账失败'), 250);
        }
        //修改付款方的金额
        $user_resource_row['user_money'] = $user_resource['user_money'] - $amount;
        $flag1 = $this->User_ResourceModel->editResource($user_id, $user_resource_row);
        if (!$flag1) {
            $this->Consume_RecordModel->sql->rollBackDb();
            $data['code'] = 3;
            
            return $this->data->addBody(-140, $data, __('转账失败'), 250);
        }
        //修改收款方的金额
        $requirer_resource_row['user_money'] = $requirer_resource['user_money'] + $amount;
        $flag2 = $this->User_ResourceModel->editResource($requirer_id, $requirer_resource_row);
        if ($flag2 && $this->Consume_RecordModel->sql->commitDb()) {
            $this->Consume_RecordModel->editRecord($result1, ['record_paytime' => date('Y-m-d h:i:s')]);
            $this->Consume_RecordModel->editRecord($result2, ['record_paytime' => date('Y-m-d h:i:s')]);
            
            return $this->data->addBody(-140, $data, __('转账成功'), 200);
        } else {
            $this->Consume_RecordModel->sql->rollBackDb();
            $data['code'] = 4;
            
            return $this->data->addBody(-140, $data, __('转账失败'), 250);
        }
    }
    
    //退款
    public function refundTransfer()
    {
        $data = [];
        $user_id = request_int('user_id');  //收款人
        $user_name = request_string('user_account');
        $seller_id = request_int('seller_id');        //付款人
        $seller_name = request_string('seller_account');
        $amount = request_float('amount');        //付款金额
        $reason = request_string('reason', '退款');  //付款说明
        $order_id = request_string('order_id');
        $goods_id = request_int('goods_id');
        $uorder_id = request_string('uorder_id');
        $payment_id = request_string('payment_id');
        //交易明细表
        $Consume_RecordModel = new Consume_RecordModel();
        //开启事务
        $Consume_RecordModel->sql->startTransactionDb();
        //用户资源表
        $User_ResourceModel = new User_ResourceModel();
        //合并支付表
        $Union_OrderModel = new Union_OrderModel();
        if ($amount < 0) {
            $flag = false;
            $data[] = '退款金额错误';
        } else {
            $time = time();
            $flow_id = time();
            //插入收款方的交易记录
            $record_add_buy_row = [];
            $record_add_buy_row['order_id'] = $flow_id;
            $record_add_buy_row['user_id'] = $user_id;
            $record_add_buy_row['user_nickname'] = $user_name;
            $record_add_buy_row['record_money'] = $amount;
            $record_add_buy_row['record_date'] = date('Y-m-d');
            $record_add_buy_row['record_year'] = date('Y');
            $record_add_buy_row['record_month'] = date('m');
            $record_add_buy_row['record_day'] = date('d');
            $record_add_buy_row['record_title'] = $reason;
            $record_add_buy_row['record_desc'] = "订单号:" . $order_id . "，商品id:" . $goods_id;
            $record_add_buy_row['record_time'] = date('Y-m-d H:i:s');
            $record_add_buy_row['trade_type_id'] = Trade_TypeModel::REFUND;
            $record_add_buy_row['user_type'] = 1;    //收款方
            $record_add_buy_row['record_status'] = RecordStatusModel::RECORD_FINISH;
            $Consume_RecordModel->addRecord($record_add_buy_row);
            $record_add_seller_row = [];
            $record_add_seller_row['order_id'] = $flow_id;
            $record_add_seller_row['user_id'] = $seller_id;
            $record_add_seller_row['user_nickname'] = $seller_name;
            $record_add_seller_row['record_money'] = $amount;
            $record_add_seller_row['record_date'] = date('Y-m-d');
            $record_add_seller_row['record_year'] = date('Y');
            $record_add_seller_row['record_month'] = date('m');
            $record_add_seller_row['record_day'] = date('d');
            $record_add_seller_row['record_title'] = $reason;
            $record_add_seller_row['record_desc'] = "订单号:" . $order_id . "，商品id:" . $goods_id;
            $record_add_seller_row['record_time'] = date('Y-m-d H:i:s');
            $record_add_seller_row['trade_type_id'] = Trade_TypeModel::REFUND;
            $record_add_seller_row['user_type'] = 2;    //付款方
            $record_add_seller_row['record_status'] = RecordStatusModel::RECORD_FINISH;
            $Consume_RecordModel->addRecord($record_add_seller_row);
            if ($payment_id == 1) {
                //查找合并单中的付款情况，购物卡优先退款
                $uorder_base = $Union_OrderModel->getOne($uorder_id);
                $card_return_amount = 0;
                //使用购物卡支付并且购物卡的退款金额小于支付金额时
                if (($uorder_base['union_cards_pay_amount'] > 0) && ($uorder_base['union_cards_return_amount'] < $uorder_base['union_cards_pay_amount'])) {
                    $card_can_return_amount = $uorder_base['union_cards_pay_amount'] - $uorder_base['union_cards_return_amount'];
                    //购物卡中可退款金额小于退款金额
                    if ($card_can_return_amount <= $amount) {
                        $card_return_amount = $card_can_return_amount;
                    } else {
                        $card_return_amount = $amount;
                    }
                    $amount = $amount - $card_return_amount;
                }
                //扣除购物卡的退款之后全部退还到账户余额中
                $edit_union_row = [];
                $edit_union_row['union_cards_return_amount'] = $card_return_amount;
                $edit_union_row['union_money_return_amount'] = $amount;
                $flag1 = $Union_OrderModel->editUnionOrder($uorder_id, $edit_union_row, true);
            } else {
                $flag1 = true;
            }
            $user_resource = current($User_ResourceModel->getResource($user_id));
            if ($flag1) {
                //修改收款方的金额
                $user_resource_row['user_recharge_card'] = $user_resource['user_recharge_card'] + $card_return_amount;
                $user_resource_row['user_money'] = $user_resource['user_money'] + $amount;
                $flag = $User_ResourceModel->editResource($user_id, $user_resource_row);
            } else {
                $flag = false;
            }
        }
        if ($flag && $Consume_RecordModel->sql->commitDb()) {
            $msg = 'success';
            $status = 200;
        } else {
            $Consume_RecordModel->sql->rollBackDb();
            $m = $Consume_RecordModel->msg->getMessages();
            $msg = $m ? $m[0]:'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //获取订单信息（1）
    public function getOrderInfo()
    {
        $order_id = request_string('order_id');
        $Consume_TradeModel = new Consume_TradeModel();
        $data = $Consume_TradeModel->getConsumeTradeByOid($order_id);
        if ($data) {
            $msg = 'success';
            $status = 250;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //提现详情(1)
    public function getWithdrawInfo()
    {
        $order_id = request_string('order_id');
        $Consume_RecordModel = new Consume_RecordModel();
        $Consume_WithdrawModel = new Consume_WithdrawModel();
        $record_row = current($Consume_RecordModel->getRecordByOid($order_id));
        $widthraw_row = current($Consume_WithdrawModel->getWithdrawByOid($order_id));
        $data['record'] = $record_row;
        $data['widthraw'] = $widthraw_row;
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    /*==========================================================================================================*/
    //获取提现记录
    public function getWidthrawList()
    {
        $skey = $_REQUEST['skey'];
        $user_account = request_string('user_account');
        $page = request_string('page', 1);
        $rows = request_string('rows', 20);
        $User_BaseModel = new User_BaseModel();
        $User_InfoModel = new User_InfoModel();
        $user_id = '';
        if ($user_account) {
            $user_id = $User_BaseModel->getBaseIdByAccount($user_account);
        }
        $Consume_WithdrawModel = new Consume_WithdrawModel();
        if ($skey) {
            $Consume_WithdrawModel->sql->setWhere('pay_uid', '%' . $skey . '%', 'LIKE');
        }
        $data = $Consume_WithdrawModel->getWithdrawListByPid($user_id, $page, $rows);
        foreach ($data['items'] as $key => $val) {
            $user_info = $User_InfoModel->getInfo($val['pay_uid']);
            $data['items'][$key]['user_info'] = current($user_info);
        }
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    public function editWith()
    {
        $id = $_REQUEST['id'];
        $Consume_WithdrawModel = new Consume_WithdrawModel();
        $data = $Consume_WithdrawModel->getWithdraw($id);
        //fb($data);
        $this->data->addBody(-140, $data);
    }
    
    /*public function getWith()
	{

		$Consume_WithdrawModel = new Consume_WithdrawModel();
		$datas                 = $Consume_WithdrawModel->get("*");
		foreach ($datas as $k => $v)
		{
			$data[$k]['is_succeed'] = $v['is_succeed'];
		}
		fb($data);
		$this->data->addBody(-140, $data);
	}*/
    public function edit()
    {
        $id = $_REQUEST['id'];
        $data['is_succeed'] = $_REQUEST['is_succeed'];
        $data['con'] = $_REQUEST['con'];
        $Consume_WithdrawModel = new Consume_WithdrawModel();
        $flag = $Consume_WithdrawModel->editWithdraw($id, $data);
        if ($flag) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //获取支付卡列表
    public function getCardBaseList()
    {
        $cardname = request_string('cardName');   //卡片名称
        $beginDate = request_string('beginDate');
        $endDate = request_string('endDate');
        $appid = request_int('appid');
        $page = request_string('page', 1);
        $rows = request_string('rows', 20);
        $Card_BaseModel = new Card_BaseModel();
        $data = $Card_BaseModel->getBaseList($cardname, $appid, $beginDate, $endDate, $page, $rows);
        $Card_InfoModel = new Card_InfoModel();
        foreach ($data['items'] as $key => $val) {
            $card_used_num = $Card_InfoModel->getCardusednumBy($val['card_id']);
            $data['items'][$key]['card_used_num'] = $card_used_num;
            $card_new_num = $Card_InfoModel->getCardnewnumBy($val['card_id']);
            $data['items'][$key]['card_new_num'] = $card_new_num;
        }
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //获取支付卡基本信息
    public function getCardBase()
    {
        $id = request_int('id');
        $Card_BaseModel = new Card_BaseModel();
        $data = $Card_BaseModel->getBaseById($id);
        $Card_InfoModel = new Card_InfoModel();
        $card_used_num = $Card_InfoModel->getCardusednumBy($id);
        $data['card_used_num'] = $card_used_num;
        $card_new_num = $Card_InfoModel->getCardnewnumBy($id);
        $data['card_new_num'] = $card_new_num;
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //修改支付卡信息
    public function editCardBase()
    {
        $id = request_int('id');  //卡号
        $card_name = request_string('card_name'); //卡名称
        $card_num = request_int('card_num');  //数量
        $source = request_int('source');   //适用平台
        $start_time = request_string('start_time');   //开始时间
        $end_time = request_string('end_time');    //结束时间
        $card_desc = request_string('card_desc');
        $card_image = request_string('card_image');
        $money = request_int('money');    //金额
        $point = request_int('point');    //积分
        //获取充值卡信息
        $Card_BaseModel = new Card_BaseModel();
        $card_base_row = current($Card_BaseModel->getBase($id));
        $flag = true;
        //判断充值卡数量是否改变
        $diff_num = $card_num - $card_base_row['card_num'];
        $Card_InfoModel = new Card_InfoModel();
        if ($diff_num > 0) {
            //查找最后一张充值卡
            $last_card_code = $Card_InfoModel->getListCardcodeByCid($id);
            for ($i = 1; $i <= $diff_num; $i++) {
                $filed = [
                    'card_code' => $last_card_code + $i,
                    'card_password' => rand(100000, 999999),
                    'card_id' => $id,
                    'card_fetch_time' => date('Y-m-d H:i:s'),
                ];
                $Card_InfoModel->addInfo($filed);
            }
        } elseif ($diff_num < 0) {
            $num = abs($diff_num);
            //删除充值卡
            $Card_InfoModel->delCardByCid($id, $num);
        }
        $prize = [];
        if ($money) {
            $prize['m'] = $money;
        }
        if ($point) {
            $prize['p'] = $point;
        }
        $card_prize = $prize;
        $filed_array = [
            'card_name' => $card_name,
            'card_num' => $card_num,
            'app_id' => $source,
            'card_prize' => $card_prize,
            'card_start_time' => $start_time,
            'card_end_time' => $end_time,
            'card_desc' => $card_desc,
            'card_image' => $card_image,
        ];
        $flag = $Card_BaseModel->editBase($id, $filed_array);
        if ($flag === false) {
            $msg = 'failure';
            $status = 250;
        } else {
            $msg = 'success';
            $status = 200;
        }
        $this->data->addBody(-140, $filed_array, $msg, $status);
    }
    
    //添加支付卡信息
    public function addCardBase()
    {
        $card_id = request_int('id');
        $source = request_int('source');
        $card_name = request_string('card_name');
        $money = request_string('money');
        $point = request_string('point');
        $card_desc = request_string('card_desc');
        $card_image = request_string('card_image');
        $card_num = request_int('card_num');
        $start_time = request_string('start_time');
        $end_time = request_string('end_time');
        $Card_BaseModel = new Card_BaseModel();
        $Card_InfoModel = new Card_InfoModel();
        $card_data = $Card_BaseModel->getBase($card_id);
        if ($card_data) {
            $flag = false;
            $msg = '此卡号已存在，请重新填写';
        } else {
            for ($i = 1; $i <= $card_num; $i++) {
                $add_row = [];
                $add_row = [
                    'card_code' => $card_id . str_pad($i, 4, "0", STR_PAD_LEFT),
                    'card_password' => rand(100000, 999999),
                    'card_id' => $card_id,
                    'card_time' => date("Y-m-d H:i:s"),
                ];
                $Card_InfoModel->addInfo($add_row);
            }
            $prize = [];
            if ($money) {
                $prize['m'] = $money;
            }
            if ($point) {
                $prize['p'] = $point;
            }
            $card_prize = $prize;
            $card_add_array = [
                'card_id' => $card_id,
                'card_name' => $card_name,
                'card_num' => $card_num,
                'app_id' => $source,
                'card_prize' => $card_prize,
                'card_start_time' => $start_time,
                'card_end_time' => $end_time,
                'card_desc' => $card_desc,
                'card_image' => $card_image,
            ];
            $flag = $Card_BaseModel->addBase($card_add_array);
            if ($flag) {
                $msg = 'success';
                $status = 200;
            } else {
                $msg = 'failure';
                $status = 250;
            }
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //删除支付卡（只可删除还未使用的支付卡）
    public function delCardBase()
    {
        $card_id = request_int('id');
        $Card_InfoModel = new Card_InfoModel();
        $Card_BaseModel = new Card_BaseModel();
        $used_num = $Card_InfoModel->getCardusednumBy($card_id);
        if ($used_num) {
            $flag = false;
        } else {
            //删除充值卡card_info
            $Card_InfoModel->delCardByCid($card_id);
            $flag = $Card_BaseModel->removeBase($card_id);
        }
        if ($flag) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //根据card_id获取支付卡(card_info)列表
    public function getCardInfoList()
    {
        $card_id = request_int('card_id');
        $page = request_int('page', 1);
        $rows = request_int('rows', 20);
        $Card_InfoModel = new Card_InfoModel();
        $card_info = $Card_InfoModel->getInfoList($card_id, $page, $rows);
        $this->data->addBody(-140, $card_info);
    }
    
    //根据card_code获取支付卡信息
    public function getCardInfo()
    {
        $card_code = request_int('card_code');
        $Card_InfoModel = new Card_InfoModel();
        $data = $Card_InfoModel->getInfo($card_code);
        $this->data->addBody(-140, $data);
    }
    
    public function payfinish()
    {
        $order_id = request_string('order_id');
        $Consume_DepositModel = new Consume_DepositModel();
        $data = $Consume_DepositModel->notifyShop($order_id);
        $this->data->addBody(-140, $data);
    }
    
    //解除绑定,生成验证码,并且发送验证码
    public function getYzm()
    {
        $type = request_string('type');
        $val = request_string('val');
        $code = request_string('code', 'getcode');
        $yzm = request_string('yzm');
        $area_code = request_string('area_code') ? :86;
        if (!Perm::checkYzm($yzm)) {
            return $this->data->addBody(-140, [], __('图形验证码有误'), 250);
        }
        $cond_row['code'] = $code;
        $de = $this->messageTemplateModel->getTemplateDetail($cond_row);
        if ($type == 'mobile') {
            $me = $de['content_phone'];
            $code_key = $val;
            $code = VerifyCode::getCode($code_key, null, 6);
            $me = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $me);
            $me = str_replace("[yzm]", $code, $me);
            $str = Sms::send($val, $area_code, $me, $de['baidu_tpl_id'], ['weburl_name' => Web_ConfigModel::value("site_name"), 'yzm' => $code]);
        } else {
            $me = $de['content_email'];
            $title = $de['title'];
            $code_key = $val;
            $VerifyCode = new VerifyCode();
            $code = $VerifyCode->getCode($code_key, null, 6);
            $me = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $me);
            $me = str_replace("[yzm]", $code, $me);
            $title = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $title);
            $Email = new Email();
            $str = $Email->send_mail($val, Perm::$row['user_account'], $title, $me);
        }
        $status = 200;
        $data = [];
        if (DEBUG == false) {
            $data['code'] = $code;
        }
        $msg = "success";
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //发送验证码
    public function sendVerificationCode()
    {
        $val = request_string('val');
        $area_code = request_string('area_code') ? :86;
        $type = request_string('type');
        $code = request_string('code', 'getcode');
        $cond_row['code'] = $code;
        $de = $this->messageTemplateModel->getTemplateDetail($cond_row);
        if ($type == 'mobile') {
            $me = $de['content_phone'];
            $code_key = $val;
            $code = VerifyCode::getCode($code_key, null, 6);
            $me = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $me);
            $me = str_replace("[yzm]", $code, $me);
            $str = Sms::send($val, $area_code, $me, $de['baidu_tpl_id'], ['weburl_name' => Web_ConfigModel::value("site_name"), 'yzm' => $code]);
        } else {
            $me = $de['content_email'];
            $title = $de['title'];
            $code_key = $val;
            $VerifyCode = new VerifyCode();
            $code = $VerifyCode->getCode($code_key, null, 6);
            $me = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $me);
            $me = str_replace("[yzm]", $code, $me);
            $title = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $title);
            $Email = new Email();
            $str = $Email->send_mail($val, Perm::$row['user_account'], $title, $me);
        }
        $this->data->addBody(-140, ['code' => $code], 'success', 200);
    }
    
    //检测解除验证码
    public function checkYzm()
    {
        $yzm = request_string('yzm');
        $type = request_string('type');
        $val = request_string('val');
        if (VerifyCode::checkCode($val, $yzm)) {
            $status = 200;
            $msg = _('success');
        } else {
            $msg = _('failure');
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //解除绑定
    public function editAllInfo()
    {
        $type = request_string('type');
        $yzm = request_string('yzm');
        $val = request_string('val');
        $user_id = Perm::$userId;
        $user_name = Perm::$row['user_name'];
        $flag = false;
        if (!VerifyCode::checkCode($val, $yzm)) {
            $msg = _('failure');
            $status = 240;
        } else {
            if ($type == 'passwd') {
                $password = request_string('password');
                if ($password) {
                    $edit_user_row['user_pay_passwd'] = md5($password);
                    $de = $this->User_BaseModel->getOne($user_id);
                    $flag = $this->User_BaseModel->editBase($user_id, $edit_user_row);
                } else {
                    $msg = _('密码不能为空');
                    $status = 250;
                }
            } else {
                if ($type == 'mobile') {
                    $edit_user_row['user_mobile'] = '';
                    $edit_user_row['user_mobile_verify'] = 0;
                } elseif ($type == 'email') {
                    $edit_user_row['user_email'] = '';
                    $edit_user_row['user_email_verify'] = 0;
                } else {
                    $edit_user_row['user_email'] = '';
                    $edit_user_row['user_email_verify'] = 0;
                }
                $de = $this->userInfoModel->getOne($user_name);
                $flag = $this->userInfoModel->editInfoDetail($user_name, $edit_user_row);
            }
            if ($flag === false) {
                $msg = _('failure');
                $status = 250;
            } else {
                $status = 200;
                $msg = _('success');
            }
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //验证用户的支付密码
    public function checkPassword()
    {
        $password = request_string('password');
        $user_id = Perm::$userId;
        $userBaseModel = new User_BaseModel;
        // if (User_BaseModel::isPaymentPasswordMistakeDayLimit($user_id)) {
        //     return $this->data->setError('失败次数已达到当天上限，请24小时后重试');
        // }
        // if (User_BaseModel::isPaymentPasswordMistakeBoutLimit($user_id)) {
        //     return $this->data->setError('失败次数过多，请十分钟后重试');
        // }
        $user_base = $userBaseModel->getOne($user_id);
        if ($user_base['user_pay_passwd']) {
            if (!$userBaseModel->checkPaymentPassWord($user_id, $password)) {
                $msg = _('支付密码错误');
                $status = 250;
            } else {
                $status = 200;
                $msg = _('success');
            }
        } else {
            $status = 230;
            $msg = _('请先设置支付密码');
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }


    public function district(){
        $shop_api_key = Yf_Registry::get('shop_api_key');
        $shop_app_id = Yf_Registry::get('shop_app_id'); 
        $shop_api_url = Yf_Registry::get('shop_api_url');
        //本地读取远程信息
        $formvars['app_id'] = $this->app_api_id;
        $formvars['pid'] = request_int('pid');

        $parms=  sprintf('%s?ctl=%s&met=%s&typ=json', $shop_api_url, 'Base_District', 'district');
        $data = get_url_with_encrypt($this->app_api_key,$parms, $formvars);
        $this->data->addBody(-140, $data['data']);
    }
    /**
     * 上传申请白条的凭证
     * 
     * @author fzh
     */
    public function btapplication(){
        //本地读取远程信息
        $user_id = Perm::$userId;
        $formvars['app_id'] = $this->app_api_id;
        $parms=  sprintf('%s?ctl=%s&met=%s&typ=json', $this->shop_api_url, 'Base_District', 'district');
        $init_rs = get_url_with_encrypt($this->app_api_key,$parms, $formvars);
        $district = $init_rs['data'];
        $user_info = $this->User_InfoModel->getOne($user_id);      
        include $this->view->getView();
    }

    /**
     * 白条声明
     * @author fzh
     */
    public function btstatement(){
        $bt_name = Web_ConfigModel::value('bt_name');
        $bt_statement = Web_ConfigModel::value('bt_statement');
        include $this->view->getView();
    }
    /**
     *  白条概况
     */
    public function btinfo()
    {
        if (Payment_ChannelModel::status('baitiao') != Payment_ChannelModel::ENABLE_YES) {
            location_to(Yf_Registry::get('base_url') . '?ctl=Info&met=index&typ=e');
        }
        //获取用户的白条信息
        $user_id = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getUserInfo($user_id);
        $user_base_model = new User_BaseModel();
        $user_base = $user_base_model->getUserBase(['user_id' => $user_id]);
        $user_info['user_pay_passwd'] = $user_base['user_pay_passwd'];
        if ($user_info['user_bt_status'] == User_InfoModel::BT_VERIFY_PASS) {
            $user_resource_model = new User_ResourceModel();
            $result = $user_resource_model->getResource($user_id);
            if (is_array($result) && $result) {
                $user_info['user_credit_cost'] = $txt = sprintf("%.2f", ($result[$user_id]['user_credit_limit'] - $result[$user_id]['user_credit_availability']));
                $user_info['user_credit_availability'] = $result[$user_id]['user_credit_availability'];
                $user_info['user_credit_limit'] = $result[$user_id]['user_credit_limit'];
                $user_info['bt_type'] = $result[$user_id]['bt_type'];
            } else {
                $user_info['user_credit_limit'] = 0;
            }
        }
        include $this->view->getView();
    }
    
    /**
     *  白条账单
     */
    public function btbill()
    {
        //获取用户信息
        $user_id = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getUserInfo($user_id);
        //查询白条订单
        $start_time = !request_string('start_time') ? date('Y-m-d', (time() - 720 * 3 * 3600)):request_string('start_time'); //默认获取30天数据
        $end_time = !request_string('end_time') ? date('Y-m-d'):request_string('end_time'); //没有结束时间时取当前时间
        $order_model = new Consume_TradeModel();
        $cond_rows = [
            'pay_user_id' => $user_id,
            'payment_channel_id' => Payment_ChannelModel::BAITIAO,
            'trade_pay_time:>=' => $start_time . ' 00:00:00',
            'trade_pay_time:<' => $end_time . ' 23:59:59'
        ];
        $search_order_id = trim(request_string('order_id'));
        if ($search_order_id) {
            $cond_rows['order_id'] = $search_order_id;
        }
        $order_rows = [
            'trade_pay_time' => 'DESC'
        ];
        $baitiao_order_list = $order_model->listByWhere($cond_rows, $order_rows, 1, 1000);
        $baitiao_order1 = [];
        $baitiao_order2 = [];
        $baitiao_order3 = [];
        $order_status = request_int('order_status');
        //获取白条信息
        $user_resource_model = new User_ResourceModel();
        $user_resource = $user_resource_model->getResource($user_id);
        if (isset($baitiao_order_list['items']) && $baitiao_order_list['records'] > 0) {
            foreach ($baitiao_order_list['items'] as $key => $value) {
                //计算白条到期时间
                //$bt_limit_time = strtotime($value['trade_pay_time']) + $user_resource[$user_id]['user_credit_cycle'] * 86400; //还款最后时间
                $bt_limit_time = strtotime($value['repayment_time']);
                if ($value['trade_payment_amount'] < $value['order_payment_amount'] && $bt_limit_time >= time()) {
                    //待还款
                    $baitiao_order1[$key] = $value;
                    $baitiao_order1[$key]['order_status'] = $order_status;
                    $baitiao_order1[$key]['order_status_text'] = '待还款';
                    $baitiao_order1[$key]['bt_limit_time'] = date('Y-m-d H:i:s', $bt_limit_time);
                    $baitiao_order_list['items'][$key]['order_status_text'] = '待还款';
                }
                if ($value['trade_payment_amount'] >= $value['order_payment_amount']) {
                    //已还款
                    $baitiao_order2[$key] = $value;
                    $baitiao_order2[$key]['order_status'] = $order_status;
                    $baitiao_order2[$key]['order_status_text'] = '已还款';
                    $baitiao_order2[$key]['bt_limit_time'] = date('Y-m-d H:i:s', $bt_limit_time);
                    $baitiao_order_list['items'][$key]['order_status_text'] = '已还款';
                }
                if ($value['trade_payment_amount'] < $value['order_payment_amount'] && $bt_limit_time < time()) {
                    //已延期
                    $baitiao_order3[$key] = $value;
                    $baitiao_order3[$key]['order_status'] = $order_status;
                    $baitiao_order3[$key]['order_status_text'] = '已延期';
                    $baitiao_order3[$key]['bt_limit_time'] = date('Y-m-d H:i:s', $bt_limit_time);
                    $baitiao_order_list['items'][$key]['order_status_text'] = '已延期';
                }
                $baitiao_order_list['items'][$key]['bt_limit_time'] = date('Y-m-d H:i:s', $bt_limit_time);
                $baitiao_order_list['items'][$key]['order_status'] = $order_status;
            }
            switch ($order_status) {
                case 1 :
                    $baitiao_order = $baitiao_order1;
                    break;
                case 2 :
                    $baitiao_order = $baitiao_order2;
                    break;
                case 3 :
                    $baitiao_order = $baitiao_order3;
                    break;
                default :
                    $baitiao_order = $baitiao_order_list['items'];
                    break;
            }
        }
        include $this->view->getView();
    }
    
    /**
     *  激活白条
     */
    public function btactivation()
    {
        //判断是否有实名认证
        $user_id = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getUserInfo($user_id);
        $data = [];
        if ($user_info['user_identity_statu'] == 1 || $user_info['user_identity_statu'] == 2) {
            //封装申请信息
            $field_row = array();
            $field_row['user_bt_status'] = 1;
            $field_row['user_btapply_time'] = get_date_time();
            $field_row['shop_company_name'] = request_string('shop_company_name');
            $field_row['address_area'] = request_string('address_area');
            $field_row['company_address_detail'] = request_string('company_address_detail');
            $field_row['company_phone'] = request_string('company_phone');
            $field_row['contacts_name'] = request_string('contacts_name');
            $field_row['contacts_phone'] = request_string('contacts_phone');
            $field_row['threeinone'] = request_int('threeinone');
            $field_row['business_id'] = request_string('business_id');
            $field_row['business_license_location'] = request_string('business_license_location');
            $field_row['business_licence_start'] = request_string('business_licence_start');
            $field_row['business_licence_end'] = request_string('business_licence_end');
            $field_row['business_license_electronic'] = request_string('business_license_electronic');
            $field_row['organization_code'] = request_string('organization_code');
            $field_row['organization_code_electronic'] = request_string('organization_code_electronic');
            $field_row['taxpayer_id'] = request_string('taxpayer_id');
            $field_row['tax_registration_certificate'] = request_string('tax_registration_certificate');
            $field_row['tax_registration_certificate_electronic'] = request_string('tax_registration_certificate_electronic');
            $user_edit = $User_InfoModel->editInfo($user_id,$field_row);
            if ($user_edit) {
                $data['url'] = Yf_Registry::get('base_url') . '/index.php?ctl=Info&met=btinfo';
                $status = 200;
                $msg = 'success';
            } else {
                $status = 250;
                $msg = 'failure';
            }
        } else {
            //去实名认证
            $data['url'] = Yf_Registry::get('base_url') . '/index.php?ctl=Info&met=certification&from=bt';
            $status = 200;
            $msg = 'success';
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     *白条还款
     * 
     * @author fzh
     */
    public function btrefund(){
        include $this->view->getView();
    }

    //白条确认还款
    public function editCreditReturn()
    {
        $user_id = Perm::$userId;
        $user_return_credit = request_float('user_return_credit',0);
        $certificate = request_string('certificate');
        if (empty($certificate)) {
            $this->data->addBody(-140, [], __('请上传还款凭证'), 250);
            $this->data->printJSON(); exit();
        }
        //获取用户的信用信息
        $User_ResourceModel = new User_ResourceModel();
        //开启事务
        $User_ResourceModel->sql->startTransactionDb();

        $user_res = $User_ResourceModel->getResource($user_id);
        $user_res = current($user_res);
        fb($user_res);
        $rs_row = array();
        //计算用户的欠款
        $user_credit_debt = bcsub($user_res['user_credit_limit'],$user_res['user_credit_availability'],2);

        //还款金额大于0，并且小于等于欠款金额
        if($user_return_credit >= 0 && $user_return_credit <= $user_credit_debt)
        {
            //1.修改白条支付订单的还款情况
            $Consume_TradeModel = new Consume_TradeModel();
            $edit_flag2 = $Consume_TradeModel->returnCredit($user_id,$user_return_credit);
            check_rs($edit_flag2,$rs_row);

            //获取用户信息info
            $User_InfoModel = new User_InfoModel();
            $user_info = $User_InfoModel->getOne($user_id);

            //2.添加还款信息- 流水记录
            $Consume_RecordModel = new Consume_RecordModel();
            $Trade_TypeModel = new Trade_TypeModel();
            $record_add_buy_row                  = array();
            $record_add_buy_row['user_id']       = $user_id;
            $record_add_buy_row['user_nickname'] = $user_info['user_nickname'];
            $record_add_buy_row['record_money']  = $user_return_credit;
            $record_add_buy_row['record_date']   = date('Y-m-d');
            $record_add_buy_row['record_year']     = date('Y');
            $record_add_buy_row['record_month'] = date('m');
            $record_add_buy_row['record_day']       =date('d');
            $record_add_buy_row['record_title']  = $Trade_TypeModel->trade_type[Trade_TypeModel::CREDIT_RETURN];
            $record_add_buy_row['record_time']   = date('Y-m-d H:i:s');
            $record_add_buy_row['trade_type_id'] = Trade_TypeModel::CREDIT_RETURN;
            $record_add_buy_row['user_type']     = 2;   //付款方
            $record_add_buy_row['record_status'] = RecordStatusModel::RECORD_FINISH;
            $record_add_buy_row['record_paytime'] = date('Y-m-d H:i:s');
            $record_add_buy_row['credit_remain'] = bcsub($user_credit_debt,$user_return_credit,2);
            $record_add_buy_row['certificate'] = $certificate;

            $add_flag = $Consume_RecordModel->addRecord($record_add_buy_row);
            check_rs($add_flag,$rs_row);

            $cond_row = array();
            $user_credit_availability = $user_res['user_credit_availability'] + $user_return_credit;
            $user_return_credit = $user_res['user_credit_return'] + $user_return_credit;

            //3.修改用户的信用信息
            $cond_row['user_credit_availability'] = $user_credit_availability;
            $cond_row['user_credit_return'] = $user_return_credit;
            $edit_flag = $User_ResourceModel->editResource($user_id,$cond_row);
            check_rs($edit_flag,$rs_row);

            $flag = is_ok($rs_row);
        }else
        {
            $flag = false;
        }


        if($flag && $User_ResourceModel->sql->commitDb())
        {
            $msg    = __('还款成功');
            $status = 200;
        }
        else
        {
            $User_ResourceModel->sql->rollBackDb();
            $status = 250;
            $msg    = __('还款失败');
        }

         $this->data->addBody(-140, [], $msg, $status);
         $this->data->printJSON(); exit();
    }
    
    function getInfoListIdentity()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 20);
        $username = request_string('userName');   //用户名称
        $cond_row = [];
        if ($username) {
            $cond_row['user_nickname:LIKE'] = '%' . $username . '%';
        }
        $cond_row['user_identity_statu:not in'] = '0';
        $order_row['user_active_time'] = 'DESC';
        $User_InfoModel = new User_InfoModel();
        $data = $User_InfoModel->getInfoList($cond_row, $order_row, $page, $rows);
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    

    
    /**
     * 检查订单中的商品状态
     *
     * @return array|mixed
     */
    public function checkOrder()
    {
        $union_order_id = request_string('uorder_id');
        $Union_OrderBase = new Union_OrderModel();
        $union_order_info = $Union_OrderBase->getOne($union_order_id);
        if (empty($union_order_info)) {
            $this->data->addBody(-140, [], "订单错误", 250);
            exit;
        }
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = [];
        $formvars['app_id'] = $shop_app_id;
        $formvars['user_id'] = Perm::$userId;
        $formvars['order_id'] = $union_order_info['inorder'];
        $result = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=checkOrder&typ=json', $url), $formvars);
        $this->data->addBody(-140, [], $result['msg'], $result['status']);
    }
}

?>
