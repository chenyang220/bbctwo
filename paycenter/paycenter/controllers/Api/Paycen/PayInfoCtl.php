<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * Api接口, 让App等调用
 *
 *
 * @category   Game
 * @package    User
 * @author     banchangle <1427825015@qq.com>
 * @copyright  Copyright (c) 2016, 班常乐
 * @version    1.0
 * @todo
 */
class Api_Paycen_PayInfoCtl extends Api_Controller
{
    /**
     *交易流水
     *
     * @access public
     */
    //获取卡片列表
    public function getCardBaseList() 
    {
        $cardname  = request_string('cardName');   //卡片名称
        $beginDate = request_string('beginDate');
        $endDate   = request_string('endDate');
        $appid     = request_int('appid');

        $page = request_string('page', 1);
        $rows = request_string('rows', 20);

        $Card_BaseModel = new Card_BaseModel();
        $data           = $Card_BaseModel->getBaseList($cardname, $appid, $beginDate, $endDate, $page, $rows);


        $Card_InfoModel = new Card_InfoModel();
        foreach ($data['items'] as $key => $val)
        {
            $card_used_num                        = $Card_InfoModel->getCardusednumBy($val['card_id']);
            $data['items'][$key]['card_used_num'] = $card_used_num;

            $card_new_num                        = $Card_InfoModel->getCardnewnumBy($val['card_id']);
            $data['items'][$key]['card_new_num'] = $card_new_num;
        }

        if ($data)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        fb($data);
        $this->data->addBody(-140, $data, $msg, $status);
    }
    //实名验证中的数据
    function getInfoListIdentity() {
        $page = request_int('page',1);
        $rows = request_int('rows',20);
        $username  = request_string('userName');   //用户名称
        $source  = request_string('status');   //状态
        $cond_row = array();
        if($source==1)
        {
            $cond_row['user_identity_statu'] =1;
        }elseif ($source==2)
        {
            $cond_row['user_identity_statu'] =2;
        }elseif ($source==3) {
            $cond_row['user_identity_statu'] = 3;
        }

        if($username){
            $cond_row['user_nickname:LIKE'] = '%' . $username . '%';
        }
           $cond_row['user_identity_statu:not in'] = '0';
        $order_row['user_active_time'] = 'DESC';
        $User_InfoModel = new User_InfoModel();
        $data           = $User_InfoModel->getInfoList($cond_row,$order_row,$page,$rows);
        if ($data)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }


    function  editInfoFen()
    {
        $user_id = request_row("user_id");

        $newuser_id= explode(",", $user_id);

        $status = request_int("status");
        $type = request_string('type');

        $cond_row = array();
        $re_flag = true;
        if($type == 'bt')
        {
            $cond_row['user_bt_status'] = $status;
            $cond_row['user_btverify_time'] = get_date_time();

            //远程修改shop中的用户白条审核状态
            $key = Yf_Registry::get('shop_api_key');
            $url = Yf_Registry::get('shop_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');

            $formvars = array();
            $formvars = $_POST;
            $formvars['app_id'] = $shop_app_id;
            $formvars['user_id'] = $user_id;
            $formvars['user_bt_status'] = $status;

            /*$rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=editUserBtStatus&typ=json', $url), $formvars);
            if($rs['status'] == 200)
            {
                $re_flag = true;
            }
            else
            {
                $re_flag = false;
            }*/
        }
        else
        {
            $cond_row['user_identity_statu'] = $status;
        }

        $User_InfoModel = new User_InfoModel();
        foreach ($newuser_id as $key=> $value) {
            $flag           = $User_InfoModel->editInfo($value,$cond_row);

        }

        if ($flag !== false && $re_flag)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        $data =array();

        $data['aaaa'] =$user_id;
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //    展示info表中的数据
    function getInfoList() {
        $page = request_string('page', 1);
        $rows = request_string('rows', 20);
        $card_id  = request_string('cardName');   //卡片名称
        $beginDate = request_string('beginDate'); //卡片生成时间
        $User_InfoModel = new  Card_InfoModel();
        $data      = $User_InfoModel->getInfoList($card_id,$beginDate,$page,$rows);
        if(!isset($data['items']) || !$data['items']){
            return $this->data->addBody(-140, array(), '没有数据', 250);
        }
        foreach ($data['items'] as $k=>$value){
            if($value['card_fetch_time'] == '0000-00-00 00:00:00' || !$value['card_fetch_time']){
                $data['items'][$k]['card_fetch_time'] = '';
            }
        }
        //从paycard分配数据到info表中************
        $Card_BaseModel = new Card_BaseModel();
        $datas          = $Card_BaseModel->getBaseList();

        foreach($datas['items'] as $key=>$val){
            $paydata[]=$val['card_id'];
        }
        $pdata=json_encode($paydata);
        $data['card_id']=$pdata;

        if ($data)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //添加info表中的数据生成卡片
    public function add()
    {
        $card_id = request_int('card_id');

        $Buyer_TestModel           = new Card_InfoModel();
        $Card_BaseModel            = new Card_BaseModel();
        $card_base = $Card_BaseModel->getOne($card_id);
        //判断卡号是否存在
        if(empty($card_base))
        {
            $msg    = '该卡号不存在';
            $status = 250;
        }else{

             $card_prize_row = json_decode($card_base['card_prize'],true);
             $money = $card_prize_row['m'];      //卡片价格
             $num = $card_base['card_num'];  //卡片最高数量

             $all_card = $Buyer_TestModel->getCardnumBy($card_id);

             $data                      = array();
             $data['card_id']           = $card_id;                  //卡id
             $length                  = request_string('card_sum');                //生成卡的数量

             if(($length+$all_card)>$num)
             {
                 $msg    = '只能生成'.$num .'张卡片';
                 $status = 250;
             }
             else
             {
                 for ($i=1; $i<=$length;$i++){
                     $data['card_code']=$data['card_id'].Text_Password::create(4,unpronounceable,1234567890);
                     $data['card_password'] = Text_Password::create(6,unpronounceable,1234567890);
                     $data['card_money'] = $money;
                     $flag = $Buyer_TestModel->addInfo($data, true);
                 }

                 if ($flag)
                 {
                     $msg    = 'failure';
                     $status = 250;
                 }
                 else
                 {
                     $msg    = 'success';
                     $status = 200;
                 }
             }
        }

         $data = array();
         $this->data->addBody(-140, $data, $msg, $status);
    }
    /*
     * 删除购物卡
     */
    public function remove()
    {
        $Card_InfoModel     = new Card_InfoModel();

        $card_code = request_int('card_code');
        if ($card_code)
        {
            $flag = $Card_InfoModel->delCardByCid($card_code);


        }
        if ($flag)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {

            $msg    = 'failure';
            $status = 250;

        }

        $data['card_code'] = $card_code;
        $this->data->addBody(-140, $data, $msg, $status);
    }
    function getEditInfo(){
          $user_id = request_int("user_id");
          $User_InfoModel = new User_InfoModel();
          $data           = $User_InfoModel->getOne($user_id);
          if ($data)
            {
                $msg    = 'success';
                $status = 200;
            }
            else
            {
                $msg    = 'failure';
                $status = 250;
            }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    
    function scanUserInfo(){
          $user_id = request_int("user_id");
          $User_InfoModel = new User_InfoModel();
          $data           = $User_InfoModel->getOne($user_id);
          if ($data)
            {
                $msg    = 'success';
                $status = 200;
            }
            else
            {
                $msg    = 'failure';
                $status = 250;
            }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    function editInfoRow()
    {
        $user_id = request_int("user_id");
        $status = request_int("status");
        $type = request_string('type');

        $cond_row = array();
        $re_flag = true;
        if($type == 'bt')
        {
            $cond_row['user_bt_status'] = $status;
            $cond_row['user_btverify_time'] = get_date_time();

            //远程修改shop中的用户白条审核状态
            $key = Yf_Registry::get('shop_api_key');
            $url = Yf_Registry::get('shop_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');

            $formvars = array();
            $formvars = $_POST;
            $formvars['app_id'] = $shop_app_id;
            $formvars['user_id'] = $user_id;
            $formvars['user_bt_status'] = $status;

            /*$rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=editUserBtStatus&typ=json', $url), $formvars);
            if($rs['status'] == 200)
            {
                $re_flag = true;
            }
            else
            {
                $re_flag = false;
            }*/
        }
        else
        {
            $cond_row['user_identity_statu'] = $status;
        }

        $User_InfoModel = new User_InfoModel();
        $flag           = $User_InfoModel->editInfo($user_id,$cond_row);
        if ($flag !== false && $re_flag)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        $data =array();
        $this->data->addBody(-140, $data, $msg, $status);
    }
    //修改充值卡
    public function editBases()
    {
        $Card_InfoModel     = new Card_InfoModel();
        $card_code                 = request_int('card_code');
        $data                      = array();
        $data['card_id']           = request_int('card_id');                  //卡id
        $data['user_id']           = request_string('user_id');
        $data['card_code']         = request_string('card_code');
        $data['card_password']     = request_string('card_password');
        $data['card_fetch_time']   = request_string('card_fetch_time');
        $data['card_media_id']     = request_string('card_media_id');
        $data['server_id']         = request_string('server_id');
        $data['user_account']      = request_string('user_account');
        $data['card_time']         = request_string('card_time');
        $data['card_money']        = request_string('card_money');
        $data['card_froze_money']  = request_string('card_froze_money');
        $flag = $Card_InfoModel->editInfo($card_code, $data, false);

        if ($flag)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {

            $msg    = 'failure';
            $status = 250;

        }
        $this->data->addBody(-140, $data, $msg, $status);

    }

    //白条实名认证中的数据
   public function getBtInfoList()
   {
        $status = request_int('status');
        $beginDate = request_string('beginDate');
        $endDate = request_string('endDate');
        $searchName = request_string('searchName');
        $searchContent = request_string('searchContent');

       $page = request_int('page',1);
       $rows = request_int('rows',20);

        $cond_row = array();

        //申请时间
        if($beginDate){
            $cond_row['user_btapply_time:>='] = $beginDate;
        }
        if($endDate){
            $cond_row['user_btapply_time:<='] = $endDate;
        }

        //查询标题与查询内容结合
        if($searchName && $searchContent)
        {
            $cond_row[$searchName] = $searchContent;
        }

        //审核状态
        if($status > 0){
            $cond_row['user_bt_status'] = $status;
        }
        else
        {
            $cond_row['user_bt_status:not in'] = User_InfoModel::BT_VERIFY_NO;
        }

        $User_InfoModel = new User_InfoModel();
        $data           = $User_InfoModel->getInfoList($cond_row,array(),$page,$rows);
        if ($data)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //白条额度设置列表
    public function getBtLimitList()
    {
        //查找已经成功申请白条用户
        $page = request_int('page',1);
        $rows = request_int('rows',20);

        $status = request_int('status');
        $beginDate = request_string('beginDate');
        $endDate = request_string('endDate');
        $searchName = request_string('searchName');
        $searchContent = request_string('searchContent');

        $page = request_int('page',1);
        $rows = request_int('rows',20);

        $cond_row = array();

        //申请时间
        if($beginDate){
            $cond_row['user_btapply_time:>='] = $beginDate;
        }
        if($endDate){
            $cond_row['user_btapply_time:<='] = $endDate;
        }

        //查询标题与查询内容结合
        if($searchName && $searchContent)
        {
            $cond_row[$searchName] = $searchContent;
        }

        //额度设置状态
        $User_ResourceModel = new User_ResourceModel();
        if($status == 1){
            //查找戳所有还未设置信用额度的用户id
            $user_reso = $User_ResourceModel->getByWhere(array('user_credit_limit'=>0));
            $user_id_row = array_column($user_reso,'user_id');
            $cond_row['user_id:IN'] = array_values($user_id_row);
        }
        if($status == 2)
        {
            //查找戳所有还未设置信用额度的用户id
            $user_reso = $User_ResourceModel->getByWhere(array('user_credit_limit:>'=>0));
            $user_id_row = array_column($user_reso,'user_id');
            $cond_row['user_id:IN'] =array_values($user_id_row);
        }

        //查找已经成功申请白条用户
        $User_InfoModel = new User_InfoModel();
        $cond_row['user_bt_status'] = User_InfoModel::BT_VERIFY_PASS;
        $data           = $User_InfoModel->getInfoList($cond_row,array(),$page,$rows);

        $items = $data['items'];
        if($items)
        {
            $user_id = array_column($items,'user_id');
            //查找信用额度
            $user_res = $User_ResourceModel->getResource($user_id);
            //账号累计消费金额
            $Consume_RecordModel = new Consume_RecordModel();
            $last_three=mktime(0,0,0,date('m')-2,1,date('y'));
            $last_three = date('Y-m-d H:i:s',$last_three);
            $now_time = get_date_time();

            foreach($items as $key => $val)
            {
                $sumall = current($Consume_RecordModel->sumMonetary($val['user_id']));
                $sum3 = current($Consume_RecordModel->sumMonetary($val['user_id'],$last_three,$now_time));

                $user_credit_limit = $user_res[$val['user_id']]['user_credit_limit'];
                $user_res[$val['user_id']]['bt_type'] ==2 && $user_credit_limit = __('不定额');
                $items[$key]['user_sumall'] = $sumall['SUM(record_money)'];
                $items[$key]['user_sum3'] = $sum3['SUM(record_money)'];
                $items[$key]['user_credit_cycle'] = $user_res[$val['user_id']]['user_credit_cycle'];
                $items[$key]['user_credit_limit'] = $user_credit_limit;

                if($user_res[$val['user_id']]['user_credit_limit'] > 0 || $user_res[$val['user_id']]['bt_type'] ==2)
                {
                    $items[$key]['user_credit_active'] = '已激活';
                }
                else
                {
                    $items[$key]['user_credit_active'] = '未激活';
                }

            }

            $data['items'] = $items;
        }

        $this->data->addBody(-140, $data);
    }

    public function getCreditInfo()
    {
        $user_id = request_int('user_id');
        $User_ResourceModel = new User_ResourceModel();

        $User_InfoModel = new User_InfoModel();
        $data           = $User_InfoModel->getOne($user_id);

        $user_res = $User_ResourceModel->getOne($user_id);
        $user_res['user_name'] = $data['user_nickname'];

        $this->data->addBody(-140, $user_res);
    }

    public function editCreditInfo()
    {
        $user_id = request_int('user_id');
        $bt_type = request_int('bt_type');
        $user_credit_limit = request_float('user_credit_limit');
        $user_credit_cycle = request_int('user_credit_cycle','1');

        $User_ResourceModel = new User_ResourceModel();
        //获取用户原来的信用信息
        $user_resource = $User_ResourceModel->getOne($user_id);
        //将原来的信用金额和需要修改的信用金额进行对比，修改用户的可用信用额度
        $duiff_credit = $user_credit_limit - $user_resource['user_credit_limit'];
        $user_new_credit_availability = $user_resource['user_credit_availability'] + $duiff_credit;
        if($user_new_credit_availability > 0 || $bt_type ==2)
        {
            $edit_row = array();
            $edit_row['bt_type'] = $bt_type;
            $edit_row['user_credit_limit'] = $user_credit_limit;
            $edit_row['user_credit_cycle'] = $user_credit_cycle;
            $edit_row['user_credit_availability'] = $user_new_credit_availability;
            $edit_flag = $User_ResourceModel->editResource($user_id,$edit_row);
        }
        else
        {
            $edit_flag = false;
        }

        if ($edit_flag)
        {
            $msg    = 'success';
            $status = 200;
        }
        else
        {
            $msg    = 'failure';
            $status = 250;
        }
        $data =array();
        $this->data->addBody(-140, $data, $msg, $status);

    }

    //白条收款确认列表
    public function getBtWarnList()
    {
        //查找已经成功申请白条用户
        $beginDate = request_string('beginDate');
        $endDate = request_string('endDate');
        $searchName = request_string('searchName');
        $searchContent = request_string('searchContent');

        $page = request_int('page',1);
        $rows = request_int('rows',20);
        $order_row = array();
        $cond_row = array();
        $user_InfoModel = new User_InfoModel();
        if($searchName && $searchContent)
        {
            $cond_row[$searchName.':LIKE'] = '%'.$searchContent.'%';
            $user_info = $user_InfoModel->getByWhere($cond_row,$order_row);
            $userIds = array_column($user_info, 'user_id');
            unset($cond_row);
        }
        $cond_row['payment_channel_id'] = Payment_ChannelModel::BAITIAO;
        $cond_row['trade_payment_status'] = 0;
        //查询标题与查询内容结合
        if ($beginDate) {
            $cond_row['repayment_time:>='] = $beginDate.' 00:00:00';
        }
        if ($endDate) {
            $cond_row['repayment_time:<='] = $endDate.' 23:59:59';
        }
        if (isset($userIds) && !empty($userIds)) {
            $cond_row['pay_user_id:IN'] = $userIds;
        }
        $order_row['trade_pay_time'] = 'DESC';
        //还款状态
        $consume_TradeModel = new Consume_TradeModel();
        $order_lists = $consume_TradeModel->listByWhere($cond_row, $order_row, $page, $rows, $flag=true,'pay_user_id');
        $repay_price = $consume_TradeModel->getCurrentMonthRepaymentOrderList($cond_row, $order_row, $page, $rows);
        //还款用户id
        foreach ($order_lists['items'] as &$order_list) {
            foreach ($repay_price as $repay_item) {
               if ($order_list['pay_user_id'] == $repay_item['pay_user_id']) {
                  $user_info = $user_InfoModel->getOne($repay_item['pay_user_id']);
                  $order_list['repay_price'] = $repay_item['repay_price'];
                  $order_list['user_realname'] = $user_info['user_realname'];
                  $order_list['user_nickname'] = $user_info['user_nickname'];
                  $order_list['user_mobile'] = $user_info['user_mobile'];
                  $order_list['user_email'] = $user_info['user_email'];
                  $order_list['reminder_time'] = '';
                  $order_list['info_type'] = __('还款提醒');
               }
            }
        }
        $this->data->addBody(-140, $order_lists);
    }
    //白条收款确认列表
    public function getBtReturnList()
    {
        //查找已经成功申请白条用户
        $status = request_int('status');
        $beginDate = request_string('beginDate');
        $endDate = request_string('endDate');
        $searchName = request_string('searchName');
        $searchContent = request_string('searchContent');

        $page = request_int('page',1);
        $rows = request_int('rows',20);

        $cond_row = array();

        //申请时间
        if($beginDate){
            $cond_row['user_btapply_time:>='] = $beginDate;
        }
        if($endDate){
            $cond_row['user_btapply_time:<='] = $endDate;
        }

        //查询标题与查询内容结合
        if($searchName && $searchContent)
        {
            $cond_row[$searchName] = $searchContent;
        }

        //还款状态
        $User_ResourceModel = new User_ResourceModel();
        //已还清
        if($status == 1){
            //查找戳所有已还清的用户id
            $sql = ' and user_credit_limit = user_credit_availability';
            $user_reso = $User_ResourceModel->getCreditReturnUserId($sql);
            $user_id_row = array_column($user_reso,'user_id');
            $cond_row['user_id:IN'] = array_values($user_id_row);
        }
        //未还清
        if($status == 2)
        {
            //查找戳所有未还清的用户id
            $sql = ' and user_credit_limit > user_credit_availability';
            $user_reso = $User_ResourceModel->getCreditReturnUserId($sql);
            $user_id_row = array_column($user_reso,'user_id');
            $cond_row['user_id:IN'] = array_values($user_id_row);
        }

        if($status == 0)
        {
            $user_reso = $User_ResourceModel->getCreditReturnUserId();
            $user_id_row = array_column($user_reso,'user_id');
            $cond_row['user_id:IN'] =array_values($user_id_row);
        }

        //查找已经成功申请白条用户并且已经设置信用额度的用户
        $User_InfoModel = new User_InfoModel();
        $cond_row['user_bt_status'] = User_InfoModel::BT_VERIFY_PASS;
        $data           = $User_InfoModel->getInfoList($cond_row,array(),$page,$rows);

        $items = $data['items'];
        if($items)
        {
            $user_id = array_column($items,'user_id');

            //查找信用额度
            $user_res = $User_ResourceModel->getResource($user_id);

            foreach($items as $key => $val)
            {
                //额度限额
                $items[$key]['user_credit_limit'] = __('不限额度');
                if ($user_res[$val['user_id']]['bt_type'] ==1) {
                    $items[$key]['user_credit_limit'] = $user_res[$val['user_id']]['user_credit_limit'];
                }
                //欠款额度
                $items[$key]['user_credit_debt'] = bcsub($user_res[$val['user_id']]['user_credit_limit'],$user_res[$val['user_id']]['user_credit_availability'],2);
                //总计还款金额
                $items[$key]['user_credit_return'] = $user_res[$val['user_id']]['user_credit_return'];
                //转款状态
                if($items[$key]['user_credit_debt'] > 0)
                {
                    $items[$key]['user_credit_status'] = '未还清';
                }
                else
                {
                    $items[$key]['user_credit_status'] = '已还清';
                }

            }

            $data['items'] = $items;
        }

        $this->data->addBody(-140, $data);
    }

    //白条确认还款
    public function editCreditReturn()
    {
        $user_id = request_int('user_id');
        $user_return_credit = request_float('user_return_credit',0);
        $consume_record_id = request_int('consume_record_id');
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
        if($user_return_credit > 0 && $user_return_credit <= $user_credit_debt)
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
            $record_add_buy_row['record_year']	   = date('Y');
            $record_add_buy_row['record_month']	= date('m');
            $record_add_buy_row['record_day']		=date('d');
            $record_add_buy_row['record_title']  = $Trade_TypeModel->trade_type[Trade_TypeModel::CREDIT_RETURN];
            $record_add_buy_row['record_time']   = date('Y-m-d H:i:s');
            $record_add_buy_row['trade_type_id'] = Trade_TypeModel::CREDIT_RETURN;
            $record_add_buy_row['user_type']     = 2;	//付款方
            $record_add_buy_row['record_status'] = RecordStatusModel::RECORD_FINISH;
            $record_add_buy_row['record_paytime'] = date('Y-m-d H:i:s');
            $record_add_buy_row['credit_remain'] = bcsub($user_credit_debt,$user_return_credit,2);
            if (empty($consume_record_id)) {
                $add_flag = $Consume_RecordModel->addRecord($record_add_buy_row);
                check_rs($add_flag,$rs_row);
            }else{
                $$update_flag = $Consume_RecordModel->editRecord($consume_record_id,$record_add_buy_row);
                check_rs($update_flag,$rs_row);
            }

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
        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);
    }

    //白条还款明细列表
    public function getBtReturnRecordList()
    {
        $status = request_int('status');
        $beginDate = request_string('beginDate');
        $endDate = request_string('endDate');
        $searchName = request_string('searchName');
        $searchContent = request_string('searchContent');

        $page = request_int('page',1);
        $rows = request_int('rows',20);

        $cond_row = array();

        //还款时间
        if($beginDate){
            $cond_row['record_paytime:>='] = $beginDate;
        }
        if($endDate){
            $cond_row['record_paytime:<='] = $endDate;
        }

        //查询标题与查询内容结合
        $User_InfoModel = new User_InfoModel();
        if($searchName && $searchContent)
        {
            if($searchName == 'user_nickname')
            {
                $cond_row['user_nickname'] = $searchContent;
            }else
            {
                //通过真实姓名与手机号获取用户id
                $user_cond_row[$searchName] = $searchContent;
                $user = $User_InfoModel->getByWhere($user_cond_row);
                $user_id_row = array_column($user,'user_id');
                $cond_row['user_id:IN'] = array_values($user_id_row);
            }
        }

        //还款状态
        $User_ResourceModel = new User_ResourceModel();
        //已还款（已还清）
        if($status == 1){
            $cond_row['credit_remain'] = 0;
        }
        //待还清（未还款）
        if($status == 2)
        {
            $cond_row['credit_remain:>'] = 0;
        }

        $cond_row['trade_type_id'] = Trade_TypeModel::CREDIT_RETURN;

        $Consume_RecordModel = new Consume_RecordModel();
        $data = $Consume_RecordModel->listByWhere($cond_row,array(),$page,$rows);

        $items = $data['items'];
        fb($items);
        if($items)
        {
            $user_id = array_column($items,'user_id');

            //查找用户信息
            $user_infos = $User_InfoModel->getInfo($user_id);
            fb($user_infos);

            //查找信用额度
            $user_res = $User_ResourceModel->getResource($user_id);
            fb($user_res);

            foreach($items as $key => $val)
            {
                $items[$key]['user_realname'] = $user_infos[$val['user_id']]['user_realname'];
                $items[$key]['user_mobile'] = $user_infos[$val['user_id']]['user_mobile'];
                if($items[$key]['credit_remain'] > 0)
                {
                    $items[$key]['credit_status'] = '待还款';
                }
                else
                {
                    $items[$key]['credit_status'] = '已还款';
                }
            }

            $data['items'] = $items;
        }

        $this->data->addBody(-140, $data);
    }

    //白条订单列表
    public function getBtOrderList()
    {
        $status = request_int('status');
        $beginDate = request_string('beginDate');
        $endDate = request_string('endDate');
        $searchName = request_string('searchName');
        $searchContent = request_string('searchContent');

        $page = request_int('page',1);
        $rows = request_int('rows',20);

        $cond_row = array();

        //订单日期
        if($beginDate){
            $cond_row['trade_create_time:>='] = $beginDate;
        }
        if($endDate){
            $cond_row['trade_create_time:<='] = $endDate;
        }

        //查询标题与查询内容结合
        $User_InfoModel = new User_InfoModel();
        if($searchName && $searchContent)
        {
            if($searchName == 'order_id')
            {
                $cond_row['order_id'] = $searchContent;
            }else
            {
                //通过真实姓名与账号名获取用户id
                $user_cond_row[$searchName] = $searchContent;
                $user = $User_InfoModel->getByWhere($user_cond_row);
                $user_id_row = array_column($user,'user_id');
                $cond_row['pay_user_id:IN'] = array_values($user_id_row);
            }
        }

        $Consume_TradeModel = new Consume_TradeModel();
        //还款状态
        //已还款
        //当前日期的30天前
        //获取白条信息
//        
//        $date = date("Y-m-d", strtotime('-30 day'));
//        if($status == 1)
//        {
//            $symbol = '';
//            $symbol = $symbol.' and order_payment_amount = trade_payment_amount';
//            $rs = $Consume_TradeModel->getTradeId($symbol);
//            $cond_row['consume_trade_id:IN'] = $rs;
//
//        }
//        //待还清
//        if($status == 2)
//        {
//            $symbol = '';
//            $symbol = $symbol.' and order_payment_amount > trade_payment_amount and trade_create_time>'.$date;
//            $rs = $Consume_TradeModel->getTradeId($symbol);
//            $cond_row['consume_trade_id:IN'] = $rs;
//        }
//        //已延期
//        if($status == 3)
//        {
//            $symbol = '';
//            $symbol = $symbol.' and order_payment_amount > trade_payment_amount and trade_create_time<'.$date;
//            $rs = $Consume_TradeModel->getTradeId($symbol);
//            $cond_row['consume_trade_id:IN'] = $rs;
//        }

        $cond_row['payment_channel_id'] = Payment_ChannelModel::BAITIAO;


        $data = $Consume_TradeModel->listByWhere($cond_row,array('trade_create_time'=>'desc'),$page,$rows);

        $items = $data['items'];
        //添加 商品名 数量
        $order_id_column = array_column($items, 'order_id');
         //本地读取远程信息
        $formvars = array();
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $shop_api_url = Yf_Registry::get('shop_api_url');
        $shop_api_key = Yf_Registry::get('shop_api_key');
        $formvars['app_id'] = $shop_app_id;
        $formvars['orderIds'] = $order_id_column;
        $parms=  sprintf('%s?ctl=Api_%s&met=%s&typ=json', $shop_api_url, 'Goods_Goods', 'getOrderGoodsByIds');
        $init_rs = get_url_with_encrypt($shop_api_key,$parms, $formvars);
        foreach ($items as &$item) {
            foreach ($init_rs['data'] as $itemAdd) {
                if (strcasecmp($item['order_id'], $itemAdd['order_id'])===0) {
                    $item['goods_name'] = $itemAdd['goods_name'];
                    $item['order_goods_num'] = $itemAdd['order_goods_num'];
                }
            }
        }
        fb($items);
        if($items)
        {
            $user_id = array_column($items,'pay_user_id');
            //查找用户信息
            $user_infos = $User_InfoModel->getInfo($user_id);
            fb($user_infos);
            $user_resource_model = new User_ResourceModel();
            $user_resource  = $user_resource_model->getResource($user_id);
            foreach($items as $key => $val)
            {
                $items[$key]['user_realname'] = $user_infos[$val['pay_user_id']]['user_realname'];
                $items[$key]['user_nickname'] = $user_infos[$val['pay_user_id']]['user_nickname'];
                $items[$key]['return_remain'] = bcsub($val['order_payment_amount'],$val['trade_payment_amount'],2);
                if($items[$key]['return_remain'] == 0)
                {
                    $items[$key]['credit_status'] = '已还款';
                }
                else
                {
                    $second = strtotime($val['trade_create_time']);
                    $diff_day =  (time() - $second) / 86400;
                    if($diff_day > $user_resource[$val['pay_user_id']]['user_credit_cycle'])
                    {
                        $items[$key]['credit_status'] = '已延期';
                    }
                    else
                    {
                        $items[$key]['credit_status'] = '待还款';
                    }

                }
            }

            $data['items'] = $items;
        }
        $this->data->addBody(-140, $data);
    }

    /**
     * 导出商品订单
     * 
     * @author fzh
     */
    public function getOrderExcel()
    {
        ob_end_clean();
        $beginDate = request_string("beginDate");
        $endDate = request_string("endDate");
        $searchContent = request_string("searchContent");
        $is_limit = request_int("is_limit");
        $totalpage = request_int("totalpage");
        $page = request_int('page',1);
        $rows = request_int('limit',100);

        $cond_row = array();
        //订单日期
        if($beginDate){
            $cond_row['trade_create_time:>='] = $beginDate;
        }
        if($endDate){
            $cond_row['trade_create_time:<='] = $endDate;
        }

        //查询标题与查询内容结合
        $User_InfoModel = new User_InfoModel();
        if($searchName && $searchContent)
        {
            if($searchName == 'order_id')
            {
                $cond_row['order_id'] = $searchContent;
            }else
            {
                //通过真实姓名与账号名获取用户id
                $user_cond_row[$searchName] = $searchContent;
                $user = $User_InfoModel->getByWhere($user_cond_row);
                $user_id_row = array_column($user,'user_id');
                $cond_row['pay_user_id:IN'] = array_values($user_id_row);
            }
        }

        $Consume_TradeModel = new Consume_TradeModel();

        $cond_row['payment_channel_id'] = Payment_ChannelModel::BAITIAO;

        $order = array('trade_create_time'=>'desc');
        if ($is_limit) {
            $this->export($cond_row,$order,$page,$rows);
        } else {
            //保存地址
            $path = ROOT_PATH . '/data/upload/download/';
            $file_template = $path . time();//临时文件
            $url = $file_template . '/';
            $limits = $totalpage;
            for ($i = 1; $i <= $limits; $i++) {
                $page = $i;
                $this->export($cond_row,$order,$page,$rows,$i,$url);
            }
            //打包
            $zip = new ZipArchive();
            $down_name = 'order_info.zip';
            $file_name = $path . $down_name;
            if ($zip->open($file_name, ZipArchive::CREATE) === TRUE) {
                $this->addFileToZip($url, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
                $zip->close(); //关闭处理的zip文件
            }
            $fp = fopen($file_name, "r");
            $file_size = filesize($file_name);//获取文件的字节

            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:" . $file_size);
            Header("Content-Disposition: attachment; filename=$down_name");
            $buffer = 1024; //设置一次读取的字节数，每读取一次，就输出数据（即返回给浏览器）
            $file_count = 0; //读取的总字节数
            //向浏览器返回数据 如果下载完成就停止输出，如果未下载完成就一直在输出。根据文件的字节大小判断是否下载完成
            while(!feof($fp) && $file_count < $file_size) {
                $file_con = fread($fp, $buffer);
                $file_count += $buffer;
                echo $file_con;
            }
            fclose($fp);
            //下载完成后删除压缩包，临时文件夹
            if ($file_count >= $file_size) {
                unlink($file_name);
                exec("rm -rf ".$file_template);
            }
        }
    }

    /**压缩文件夹
     * @param $path
     * @param $zip
     */
    function addFileToZip($path,$zip){
        $handler = opendir($path); //打开当前文件夹由$path指定。
        while(($filename = readdir($handler)) !== false){
            if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
                if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                    addFileToZip($path."/".$filename, $zip);
                }else{ //将文件加入zip对象
                    $zip->addFile($path."/".$filename,$filename);
                }
            }
        }
        @closedir($path);
    }
    /**
     * 导出单页数据
     * 
     * @author fzh
     */
    public function export($cond_row,$order,$page,$rows,$i="",$url="")
    {

        $Consume_TradeModel = new Consume_TradeModel();
        $data = $Consume_TradeModel->listByWhere($cond_row,$order,$page,$rows);
        $items = $data['items'];
        //添加 商品名 数量
        $order_id_column = array_column($items, 'order_id');
         //本地读取远程信息
        $formvars = array();
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $shop_api_url = Yf_Registry::get('shop_api_url');
        $shop_api_key = Yf_Registry::get('shop_api_key');
        $formvars['app_id'] = $shop_app_id;
        $formvars['orderIds'] = $order_id_column;
        $parms=  sprintf('%s?ctl=Api_%s&met=%s&typ=json', $shop_api_url, 'Goods_Goods', 'getOrderGoodsByIds');
        $init_rs = get_url_with_encrypt($shop_api_key,$parms, $formvars);
        foreach ($items as &$item) {
            foreach ($init_rs['data'] as $itemAdd) {
                if (strcasecmp($item['order_id'], $itemAdd['order_id'])===0) {
                    $item['goods_name'] = $itemAdd['goods_name'];
                    $item['order_goods_num'] = $itemAdd['order_goods_num'];
                }
            }
        }
        fb($items);
        if($items)
        {
            $user_id = array_column($items,'pay_user_id');
            $User_InfoModel = new User_InfoModel();
            //查找用户信息
            $user_infos = $User_InfoModel->getInfo($user_id);
            fb($user_infos);
            $user_resource_model = new User_ResourceModel();
            $user_resource  = $user_resource_model->getResource($user_id);
            foreach($items as $key => $val)
            {
                $items[$key]['user_realname'] = $user_infos[$val['pay_user_id']]['user_realname'];
                $items[$key]['user_nickname'] = $user_infos[$val['pay_user_id']]['user_nickname'];
                $items[$key]['return_remain'] = bcsub($val['order_payment_amount'],$val['trade_payment_amount'],2);
                if($items[$key]['return_remain'] == 0)
                {
                    $items[$key]['credit_status'] = '已还款';
                }
                else
                {
                    $second = strtotime($val['trade_create_time']);
                    $diff_day =  (time() - $second) / 86400;
                    if($diff_day > $user_resource[$val['pay_user_id']]['user_credit_cycle'])
                    {
                        $items[$key]['credit_status'] = '已延期';
                    }
                    else
                    {
                        $items[$key]['credit_status'] = '待还款';
                    }

                }
            }

         $data['items'] = $items;
        }
        $con = $items;
        $tit = array(
            "序号",
            "订单编号",
            "真实姓名",
            "账号名",
            "订单日期",
            "商品名",
            "数量",
            "订单总金额（￥）",
            "实际还款金额（￥）",
            "剩余还款金额（￥）",
            "还款状态"
        );
        $key = array(
            "consume_trade_id",
            "user_realname",//订单来源
            "user_nickname",
            "trade_create_time",
            "goods_name",//订单状态
            "order_goods_num",
            "order_payment_amount",
            "trade_payment_amount",
            "return_remain",
            "credit_status"
        );
        if(isset($i)&& is_numeric($i)){
            $this->download_excel("订单信息".$i, $tit, $con, $key,$url);
        }else{
            $this->excel("订单信息", $tit, $con, $key);
        }
    }
    public function download_excel($title,$tit,$con,$key,$url){
        ob_end_clean();   //***这里再加一个
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("mall_new");
        $objPHPExcel->getProperties()->setLastModifiedBy("mall_new");
        $objPHPExcel->getProperties()->setTitle($title);
        $objPHPExcel->getProperties()->setSubject($title);
        $objPHPExcel->getProperties()->setDescription($title);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $letter = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K'
        );
        foreach ($tit as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[$k] . "1", $v);
        }
        foreach ($con as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[0] . ($k + 2), $k + 1);
            foreach ($key as $k2 => $v2)
            {

                $objPHPExcel->getActiveSheet()->setCellValue($letter[$k2 + 1] . ($k + 2), $v[$v2]);
            }
        }
        ob_end_clean();   //***这里再加一个
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        if (!file_exists($url) && !mkdir($url, 0777, true)) {
            return false;
        }
        $url = $url.uniqid().'.xls';
        $objWriter->save($url);

    }
    function excel($title, $tit, $con, $key)
    {
        ob_end_clean();   //***这里再加一个
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("mall_new");
        $objPHPExcel->getProperties()->setLastModifiedBy("mall_new");
        $objPHPExcel->getProperties()->setTitle($title);
        $objPHPExcel->getProperties()->setSubject($title);
        $objPHPExcel->getProperties()->setDescription($title);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $letter = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'k'
        );
        foreach ($tit as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[$k] . "1", $v);
        }

        foreach ($con as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[0] . ($k + 2), $k + 1);
            foreach ($key as $k2 => $v2)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($letter[$k2 + 1] . ($k + 2), $v[$v2]);
            }
        }

        ob_end_clean();   //***这里再加一个
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$title.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * 发送白条信息
     * 
     * @author fzh
     * @copyright [上海远丰集团科技有限公司]
     */
    public function sendMessage(){
        //邮件发送
        $email = new Email();
        $email_to = request_string('user_email');
        $user_mobile = request_string('user_mobile');
        $repay_price = request_float('repay_price');
        $user_nickname = request_string('user_nickname');
        $repayment_time = request_string('repayment_time');
        $email_addr = Web_ConfigModel::value("email_addr");
        $title = __('白条还款');
        $date = date('Y-m-d H:i:s');
        $con = <<<BT
           <p>{$user_nickname}你好，你本月的白条还款额度为{$repay_price}元,请及时还款,
           最晚还款时间为{$repayment_time}</p><br/>

                                                        <p style='text-align:right;'>{$email_addr}</p>
                                                        <p style='text-align:right;'>{$date}</p>
BT;
        //Sms::send($mob, $pre,$content, $tple_id=null,$data=[]);
         $data = $email->send_mail($email_to, $user_nickname, $title, $con);
         if (in_array(200, $data)) {
            $user_info = new User_InfoModel();
            $msg = 'success';
            $status = 200;
         }else{
            $msg = 'failure';
            $status = 250;
         }
         $this->data->addBody(-140, $data,$msg,$status);
    }
}

?>