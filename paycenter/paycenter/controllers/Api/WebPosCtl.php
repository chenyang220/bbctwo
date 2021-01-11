<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

class Api_WebPosCtl extends Yf_AppController
{
    public $unionOrderModel;
    public $request_parameter;
    public $notifyUrlConfig;
    public $unionOrderData;

    public static $orderConfig = [
        'trade_desc'=> 'WEBPOS',
        'app_id'=> 207
    ];

    public static $paymentMethodConfig = [
        'alipay'=> Payment_ChannelModel::ALIPAY,
        'wx'=> Payment_ChannelModel::WECHAT_PAY
    ];

    public $require_field = [ //必要字段
        'createOrder'=> [
            'order_id',
            'trade_title',
            'amount',
            'payment_way'
        ],
        'getOrderInfo'=> [
            'order_id'
        ]
    ];

    function __construct($ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->verify();
        $this->unionOrderModel = new Union_OrderModel();
        $this->notifyUrlConfig = [
            'alipay'=> Yf_Registry::get('base_url') . "/paycenter/api/payment/alipay/webpos_notify_url.php",
            'wx'=> Yf_Registry::get('base_url') . "/paycenter/api/payment/wx/webpos_notify_url.php?trade_type=JSAPI"
        ];
    }

    //验证
    private function verify ()
    {
        $this->request_parameter = $_REQUEST;
        if (isset($this->require_field[$this->request_parameter['met']])) {
            foreach ($this->require_field[$this->request_parameter['met']] as $field) {
                if (! isset($this->request_parameter[$field]) || empty($this->request_parameter[$field])) {
                    $this->showError('无效请求');
                }
            }
        }
    }

    private function showError ($msg)
    {
        $error_json = json_encode(array('cmd_id' => -140, 'status' => 250, 'msg' => $msg, 'data' => []));
        exit($error_json);
    }

    /**
     * webPos生成支付订单
     * 注意：
     * 1.buyer_id可能为空，为空为线下会员，未在payCenter注册
     * 2.app_id不清楚为什么等于102，翻遍这个项目没有找到相关注释，暂定102
     * 3.目前webPos只有两种支付方式支付宝、微信！该订单只有在线支付union_online_pay_amount
     * 4.新增交易类型trade_type_id = Trade_TypeModel::WEB_POS = 9
     * 5.in_array(payment_channel_id, Payment_ChannelModel::ALIPAY, Payment_ChannelModel::WECHAT_PAY)
     *
     * 目前确认该业务只和pay_union_order发生关联
     */
    public function createOrder ()
    {
        $param = [
            'order_id'=> request_string('order_id'),
            'trade_title'=> request_string('trade_title'),
            'amount'=> request_string('amount'),
            'buyer_id'=> request_string('buyer_id'),
            'payment_way'=> request_string('payment_way')
        ];

        if ($this->getOrder($param['order_id']) === false) {
            return $this->showError('服务器异常，请稍后重试');
        }

        if (! empty($this->unionOrderData)) {
            //如果订单已经存在且完成支付，则禁止本次访问，保证单号唯一性
            if ($this->unionOrderData['order_state_id'] == Union_OrderModel::PAYED) {
                return $this->showError('该订单已支付，请勿重复创建');
            }

            //如果订单存在且未完成支付，则判断订单支付信息是否一样。如果不一样先更新订单
            if ( $param['amount'] != $this->unionOrderData['union_online_pay_amount'] ||
                self::$paymentMethodConfig[$param['payment_way']] != $this->unionOrderData['payment_channel_id']
            ) {
                if ($this->updateUnionOrder($this->unionOrderData['union_order_id'], $param) === false) {
                    return $this->showError('更新订单失败，请稍后重试');
                }
            }
        } else {
            //如果订单不存在则创建订单
            if ($this->createUnionOrder($param) === false) {
                return $this->showError('生成支付订单失败');
            }
        }

        if ($param['payment_way'] === 'alipay') {
            $this->aliPay();
        } else {
            $this->wxPay();
        }
    }

    /**
     * @param $order_id
     * @return array
     * 获取订单信息
     */
    private function getOrder ($order_id)
    {
        return $this->unionOrderData = $this->unionOrderModel->getOneByWhere([
            'inorder'=> $order_id,
            'trade_type_id'=> Trade_TypeModel::SHOPPING,
        ]);
    }

    /**
     * 更新订单信息
     * @param order_id
     * @param $param array
     * @return boolean
     */
    public function updateUnionOrder ($order_id, $param)
    {
        $payment_channel_id = self::$paymentMethodConfig[$param['payment_way']];

        $update_data = [
            'trade_title'=> $param['trade_title'],
            'create_time'=> date('Y-m-d H:i:s'),
            'buyer_id'=> $param['buyer_id'],
            'payment_channel_id'=> $payment_channel_id,
            'union_online_pay_amount'=> $param['amount'],
            'trade_payment_amount'=> $param['amount']
        ];
        if ($param['payment_way'] === 'alipay') {
            $update_data['payment_channel_code'] = 'alipay';
        } else if($param['payment_way'] === 'wx'){
            $update_data['payment_channel_code'] = 'wx_native';
        }
        return $this->unionOrderModel->editUnionOrder($order_id, $update_data);
    }

    private function createUnionOrder ($param)
    {
        $payment_channel_id = self::$paymentMethodConfig[$param['payment_way']];

        $insert_row = [
            'union_order_id'=> Union_OrderModel::createUnionOrderId(),
            'inorder'=> $param['order_id'],
            'trade_title'=> $param['trade_title'],
            'trade_payment_amount'=> $param['amount'],
            'create_time'=> date('Y-m-d H:i:s'),
            'buyer_id'=> $param['buyer_id'],
            'trade_desc'=> self::$orderConfig['trade_desc'],
            'order_state_id'=> Union_OrderModel::WAIT_PAY,
            'payment_channel_id'=> $payment_channel_id,
            'app_id'=> self::$orderConfig['app_id'],
            'trade_type_id'=> Trade_TypeModel::SHOPPING,
            'union_online_pay_amount'=> $param['amount']
        ];

        if ($param['payment_way'] === 'alipay') {
            $insert_row['payment_channel_code'] = 'alipay';
        } else if($param['payment_way'] === 'wx'){
            $insert_row['payment_channel_code'] = 'wx_native';
        }
        

        $this->unionOrderData = $insert_row;
        return $this->unionOrderModel->addUnionOrder($insert_row);
    }

    private function aliPay ()
    {
        $payment = PaymentModel::create('alipay', [
            'notify_url'=> $this->notifyUrlConfig['alipay']
        ]);
        $payment->pay($this->unionOrderData);
    }

    private function wxPay ()
    {
        $payment = PaymentModel::create('wx_native', [
            'notify_url'=> $this->notifyUrlConfig['wx']
        ]);
        $payment->pay($this->unionOrderData);
    }

    public function getOrderInfo ()
    {
        $order_id = request_string('order_id');
        $data = $this->unionOrderModel->getOneByWhere([
            'inorder'=> $order_id,
            'trade_type_id'=> Trade_TypeModel::SHOPPING
        ]);

        if (empty($data)) {
            return $this->showError('未找到此订单');
        }
        $this->data->addBody(-140, $data, 'success', 200);
    }

    /**
     * 使用余额支付
     *
     */
    public function webposmoney()
    {
        $trade_id = request_string('trade_id');
        $user_id = request_string('user_id');
        //如果订单号为合并订单号，则获取合并订单号的信息
        $Union_OrderModel = new Union_OrderModel();

        $uorder = $Union_OrderModel->getOne($trade_id);

        $User_ResourceModel=new User_ResourceModel();
        $user_money = $User_ResourceModel->getOne($user_id);

        if($user_money['user_money'] >= $uorder['trade_payment_amount'])
        {
            $user_money['user_money']=$user_money['user_money'] - $uorder['trade_payment_amount'];
           // $data['11']=$user_money['user_money'];
           $rs = $User_ResourceModel->editResource($user_id,array('user_money'=>$user_money['user_money']));

            if($rs){
                $msg    = __('success');
                $status = 200;
            }else{
               $msg    = __('failure1');
               $status = 250;
            }
        }else{
           $msg    = __('failure');
           $status = 250;
        }
        $this->data->addBody(-140, $data=array(), $msg, $status);
    }
    

    /**
     * 减少用户余额
     *
     */
    public function webposusermoney()
    {
        $user_id = request_string('user_id');
        $price = request_string('price');

        $User_ResourceModel=new User_ResourceModel();
        $user_money = $User_ResourceModel->getOne($user_id);

        if($user_money['user_money'] >= $price)
        {
           $user_money['user_money']=$user_money['user_money'] - $price;
           $rs = $User_ResourceModel->editResource($user_id,array('user_money'=>$user_money['user_money']));

            if($rs){
                $msg    = __('success');
                $status = 200;
            }else{
               $msg    = __('failure1');
               $status = 250;
            }
        }else{
           $msg    = __('failure');
           $status = 250;
        }
        $this->data->addBody(-140, $data=array(), $msg, $status);
    }


    // /**
    //  * 使用余额支付
    //  *
    //  */
    // public function webpos_money()
    // {
    //     $trade_id = request_string('trade_id');
    //     $user_id = request_string('user_id');

    //     //如果订单号为合并订单号，则获取合并订单号的信息
    //     $Union_OrderModel = new Union_OrderModel();

    //     //开启事物
    //     $Consume_DepositModel = new Consume_DepositModel();

    //     $uorder = $this->unionOrderModel->getOne($trade_id);

    //     $User_ResourceModel=new User_ResourceModel();
    //     $user_money = $User_ResourceModel->getOne($user_id);

    //     if($user_money['user_money'] >= $uorder['trade_payment_amount'])
    //     {
    //         $user_money['user_money']=$user_money['user_money'] - $uorder['trade_payment_amount'];
    //        // $data['11']=$user_money['user_money'];
    //        $rs = $User_ResourceModel->editResource($user_id,array('user_money'=>$user_money['user_money']));
    //     }

    //     $data = array();

    //     $pay_flag = true;
    //     $pay_user_id = $uorder['buyer_id'];
   
    //     if($pay_flag)
    //     {
    //         //修改订单表中的各种状态
    //         $flag = $Consume_DepositModel->notifyShop($trade_id,$pay_user_id);
    //         if ($flag['status'] == 200)
    //         {
    //             $flag = $this->update_order($trade_id,$uorder['inorder'],'balance',$user_id);
    //             if($flag == false){
    //                 //报错
    //                 $msg    = __('failure4');
    //                 $status = 250;
    //             }else{
    //                 //查找回调地址
    //                 $User_AppModel = new User_AppModel();
    //                 $user_app = $User_AppModel->getOne($uorder['app_id']);
    //                 $return_app_url = $user_app['app_url'];

    //                 $data['return_app_url'] = $return_app_url;
    //                 $data['order_id'] = $uorder['inorder'];
    //                 $msg    = 'success';
    //                 $status = 200;
    //             }

    //         }else{
    //             $msg    = __('failure3');
    //             $status = 250;
    //         }
    //     }else{
    //         $msg    = __('failure2');
    //         $status = 250;
    //     }
 
    //     $this->data->addBody(-140, $data, $msg, $status);
    // }


    //     /**
    //  * 向shop请求修改order_base的支付渠道
    //  * @param $order_id
    //  * @param $pay_code
    //  */
    // public function update_order($union_order_id,$order_id,$pay_code,$user_id){

    //     $Union_OrderModel = new Union_OrderModel();
    //     $Union_OrderModel->editUnionOrder($union_order_id,['payment_channel_code'=>$pay_code]);
    //     $key      = Yf_Registry::get('shop_api_key');
    //     $url         = Yf_Registry::get('shop_api_url');
    //     $shop_app_id = Yf_Registry::get('shop_app_id');
    //     $formvars = array();

    //     $formvars['app_id']                 = $shop_app_id;
    //     $formvars['user_id']     = $user_id;
    //     $formvars['order_id'] = $order_id;
    //     $pay_codes = Yf_Registry::get('pay_config')['pay_code'];

    //     if(in_array($pay_code,$pay_codes['alipay_code'])){
    //         $formvars['payment_name'] = '支付宝支付';
    //     }elseif (in_array($pay_code,$pay_codes['wx_code'])){
    //         $formvars['payment_name'] ='微信支付';
    //     }elseif (in_array($pay_code,$pay_codes['other'])){
    //         $formvars['payment_name'] ='银联支付';
    //     }else{
    //         $formvars['payment_name'] ='余额支付';
    //     }
    //     return get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=editOrderPaymentName&typ=json',$url), $formvars);
    // }
    
    public function checkPayWay()
    {
        $card_payway = request_string('card_payway');
        $money_payway = request_string('money_payway');
        $online_payway = request_string('online_payway');
        $bt_payway = request_string('bt_payway');
        $uorder_id = request_string('uorder_id');
        $user_id = request_string('user_id');
        //查找订单的支付信息
        $Union_OrderModel = new Union_OrderModel();
        //开启事物
        $Union_OrderModel -> sql -> startTransactionDb();
        $uorder_base = $Union_OrderModel -> getOne($uorder_id);
        $urow = $Union_OrderModel -> getByWhere(array('inorder' => $uorder_base['inorder']));
        $uorder_id_row = array_column($urow, 'union_order_id');
        //订单支付的总金额
        $payment_amount = $uorder_base['trade_payment_amount'];
        $user_card_pay = 0;
        $user_money_pay = 0;
        $user_online_pay = 0;
        //使用充值卡或账户余额支付时，查找账户的资源资源信息
        if ($card_payway == true || $money_payway == true) {
            $User_ResourceModel = new User_ResourceModel();
            $user_resource = $User_ResourceModel -> getOne($user_id);
            $user_money = $user_resource['user_money'];
            $user_card = $user_resource['user_recharge_card'];
            //使用充值卡支付
            if ($card_payway == true) {
                if ($user_card <= $payment_amount) {
                    $user_card_pay = $user_card;
                    $payment_amount = $payment_amount - $user_card;
                } else {
                    $user_card_pay = $payment_amount;
                    $payment_amount = 0;
                }
            }
            //使用账户余额支付
            if ($money_payway == true) {
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

        //将用户的付款信息插入表中
        $edit_union_order_row['union_cards_pay_amount'] = $user_card_pay;
        $edit_union_order_row['union_money_pay_amount'] = $user_money_pay;
        $edit_union_order_row['union_online_pay_amount'] = $user_online_pay;
        if ($user_card_pay != 0 || $user_money_pay != 0 || $user_online_pay != 0) {
            $Union_OrderModel = new Union_OrderModel();
            $flag = $Union_OrderModel -> editUnionOrder($uorder_id_row, $edit_union_order_row);
            check_rs($flag, $rs_row);
        }
        if (is_ok($rs_row) && $Union_OrderModel -> sql -> commitDb()) {
            $msg = 'success';
            $status = 200;
        } else {
            $Union_OrderModel -> sql -> rollBackDb();
            $m = $Union_OrderModel -> msg -> getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }
        $data = array();
        $this -> data -> addBody(-140, $data, $msg, $status);
    }

    /**
     * 使用余额支付
     *
     */
    public function webpos_money()
    {
        //$trade_id = request_string('trade_id');
        $trade_id = request_string('trade_id');
        $user_id = request_string('user_id');
        //如果订单号为合并订单号，则获取合并订单号的信息
        $Union_OrderModel = new Union_OrderModel();
        //开启事物
        $Consume_DepositModel = new Consume_DepositModel();
        $uorder = $this->unionOrderModel->getOne($trade_id);  
        $data = array();
        //判断订单状态是否为等待付款状态     
        $pay_flag = true;
        $pay_user_id = $uorder['buyer_id'];

        if($pay_flag)
        {
            //修改订单表中的各种状态
            $flag = $Consume_DepositModel->webpos_notifyShop($trade_id,$pay_user_id);
            
            if ($flag['status'] == 200)
            {
                $flag = $this->update_order($trade_id,$uorder['inorder'],'balance',$user_id);
                // $data[222]=$flag;
                // $this->data->addBody(-140, $data, $msg, $status);
                if($flag['status'] == 250){
                    //报错
                    $msg    = __('failure4');
                    $status = 250; 
                }else{
                    //查找回调地址
                    $User_AppModel = new User_AppModel();
                    $user_app = $User_AppModel->getOne($uorder['app_id']);
                    $return_app_url = $user_app['app_url'];

                    $data['return_app_url'] = $return_app_url;
                    $data['order_id'] = $uorder['inorder'];
                    $msg    = 'success';
                    $status = 200;
                }

            }
            else
            {
                $msg    = __('failure3');
                $status = 250;
            }
         }
        else
        {
            $msg    = __('failure2');
            $status = 250;

        }

        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     * 向shop请求修改order_base的支付渠道
     * @param $order_id
     * @param $pay_code
     */
    private function update_order($union_order_id,$order_id,$pay_code,$user_id){

        $Union_OrderModel = new Union_OrderModel();
        $Union_OrderModel->editUnionOrder($union_order_id,['payment_channel_code'=>$pay_code]);
        $key      = Yf_Registry::get('shop_api_key');
        $url         = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();

        $formvars['app_id']                 = $shop_app_id;
        $formvars['user_id']     = $user_id;
        $formvars['order_id'] = $order_id;
        $pay_codes = Yf_Registry::get('pay_config')['pay_code'];

        if(in_array($pay_code,$pay_codes['alipay_code'])){
            $formvars['payment_name'] = '支付宝支付';
        }elseif (in_array($pay_code,$pay_codes['wx_code'])){
            $formvars['payment_name'] ='微信支付';
        }elseif (in_array($pay_code,$pay_codes['other'])){
            $formvars['payment_name'] ='银联支付';
        }else{
            $formvars['payment_name'] ='余额支付';
        }
        return get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=editOrderPaymentName&typ=json',$url), $formvars);
    }


    //确认收货
    public function confirmOrder()
    {
        $rs_row = array();
        if (request_string('type') == 'row') {
            $order_id = request_row('order_id');
        } else {
            $order_id[] = request_string('order_id');
        }
        
        //判断是否是货到付款订单，货到付款订单不需要修改卖家资金 1-货到付款
        $payment = request_int('payment', '1');
        
        //1.修改订单表（consume_trade）
        $Consume_TradeModel = new Consume_TradeModel();
        
        //开启事物
        $Consume_TradeModel->sql->startTransactionDb();
        
        $Consume_TradeModel->editTrade($order_id, array('order_state_id' => Union_OrderModel::FINISH));
        
        $consume_trade_row = $Consume_TradeModel->getOne($order_id);
        
        //2.合并支付表
        $Union_OrderModel = new Union_OrderModel();
        $union_row = $Union_OrderModel->getByWhere(array('inorder:IN' => $order_id));
        $uorder_id = array_column($union_row, 'union_order_id');
        $flag1 = $Union_OrderModel->editUnionOrder($uorder_id, array('order_state_id' => Union_OrderModel::FINISH));
        check_rs($flag1, $rs_row);
        
        //3.交易明细
        $Consume_RecordModel = new Consume_RecordModel();
        $record_row = $Consume_RecordModel->getByWhere(array('order_id:IN' => $order_id));
        $record_id_row = array_column($record_row, 'consume_record_id');
        $flag2 = $Consume_RecordModel->editRecord($record_id_row, array('record_status' => RecordStatusModel::RECORD_FINISH));
        check_rs($flag2, $rs_row);
        
        if ($payment) {
            //4.减少买家冻结中的资金
            $union_row_buy = current($union_row);
            $card_money = $union_row_buy['union_cards_pay_amount'];
            $money = $union_row_buy['union_money_pay_amount'];
            $user_resource_edit_row = array();
            $user_resource_edit_row['user_money_frozen'] = $money * (-1);
            $user_resource_edit_row['user_recharge_card_frozen'] = $card_money * (-1);
            
            $User_ResourceModel = new User_ResourceModel();
            //$User_ResourceModel->editResource($union_row_buy['buyer_id'],$user_resource_edit_row,true);
            
            //5.增加卖家冻结中的资金（冻结金额 = 订单金额 - 佣金 - 退款金额 + 退还佣金）
            $seller_resource_edit_row = array();
            $seller_resource_edit_row['user_money_frozen'] = $consume_trade_row['order_payment_amount'] - $consume_trade_row['trade_commis_amount'] - $consume_trade_row['trade_refund_amount'] + $consume_trade_row['trade_commis_refund'];
            
            //如果不存在卖家信息，就添加一个
            $seller_recource_check = $User_ResourceModel->getOne($consume_trade_row['seller_id']);
            
            if (!$seller_recource_check) {
                $seller_resource_edit_row['user_id'] = $consume_trade_row['seller_id'];
                $flag3 = $User_ResourceModel->addResource($seller_resource_edit_row);
            } else {
                $flag3 = $User_ResourceModel->editResource($consume_trade_row['seller_id'], $seller_resource_edit_row, true);
            }
            check_rs($flag3, $rs_row);
        }
        
        $flag = is_ok($rs_row);
        
        if ($flag && $Consume_TradeModel->sql->commitDb()) {
            $msg = 'success';
            $status = 200;
        } else {
            $Consume_TradeModel->sql->rollBackDb();
            $m = $Consume_TradeModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

}