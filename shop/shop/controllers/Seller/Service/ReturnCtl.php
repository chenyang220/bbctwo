<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
    
    /**
     * @author     Yf <service@yuanfeng.cn>
     */
    class Seller_Service_ReturnCtl extends Seller_Controller
    {
        public $orderReturnModel = null;
        public $orderBaseModel = null;
        public $orderGoodsModel = null;
        
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
            $this->orderReturnModel = new Order_ReturnModel();
            $this->orderBaseModel = new Order_BaseModel();
            $this->orderGoodsModel = new Order_GoodsModel();
            $this->Order_RefundGoodsModel = new Order_RefundGoodsModel();

            $paycenter_api_key = Yf_Registry::get('paycenter_api_key');
            $paycenter_app_id = Yf_Registry::get('paycenter_app_id');
            $paycenter_api_url = Yf_Registry::get('paycenter_api_url');
            $formvars = array(
                'app_id'=>$paycenter_app_id
            );
            $parms=  sprintf('%s?ctl=Api_%s&met=%s&typ=json', $paycenter_api_url, 'Pay_Pay', 'yunshanStatus');
            $init_rs = get_url_with_encrypt($paycenter_api_key,$parms,$formvars);
            if ($init_rs['status'] == 200) {
                $this->yunshanstatus = $init_rs['data']['status'];
            } 
        }
        
        public function orderReturn()
        {
            $act = request_string('act');
            
            if ($act == "detail") {
                $data = $this->detail();
                $this->view->setMet('detail');
            } else {
                $data = $this->listReturn(Order_ReturnModel::RETURN_TYPE_ORDER);
                
                //分销商分销的商品
                $GoodsCommonModel = new Goods_CommonModel();
                $Order_GoodsModel = new Order_GoodsModel();
                $dist_commons = $GoodsCommonModel->getByWhere(array('shop_id' => Perm::$shopId, "common_parent_id:>" => 0, 'product_is_behalf_delivery' => 1));
                
                if (!empty($dist_commons)) {
                    $dist_common_ids = array_column($dist_commons, 'common_id');
                }
                foreach ($data['items'] as $key => $value) {
                    if ($value['order_goods_id']) {
                        $order_goods_base = $Order_GoodsModel->getOne($value['order_goods_id']);
                        $data['items'][$key]['common_id'] = $order_goods_base['common_id'];
                    }
                }
            }
            if ($this->typ == "json") {
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        
        public function detail()
        {
            $return_id = request_int("id");
            $cond_row['order_return_id'] = $return_id;
            $cond_row['seller_user_id'] = Perm::$shopId;
            
            $data = $this->orderReturnModel->getReturn($cond_row);
            if ($data['return_goods_return']) {
                $data['text'] = __("退货");
            } else {
                $data['text'] = __("退款");
            }
            $data['goods'] = $this->orderGoodsModel->getByWhere(array('order_id' => $data['order_id']));
            if ($data['order_goods_id']) {
                $data['refund_goods'] = $this->orderGoodsModel->getOne($data['order_goods_id']);
            }
            $data['order'] = $this->orderBaseModel->getOne($data['order_number']);
            $return_limit = $this->orderReturnModel->getByWhere(array(
                'order_number' => $data['order']['order_id'],
                'return_shop_handle:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS
            ));
            $cash = 0;
            foreach ($return_limit as $v) {
                $cash += $v['return_cash'];
            }
            //该笔订单未被拒绝的“退款/退货”总金额
            $data['return_limit'] = $cash;
            $orderGoodsChainCodeModel = new Order_GoodsChainCodeModel;
            $data['isChainOrder'] = $orderGoodsChainCodeModel->isChainOrder($data['order']['order_id']);
            return $data;
        }
        
        /**
         * 首页
         *
         * @access public
         */
        public function listReturn($type)
        {
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = 10;
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            $cond_row['seller_user_id'] = Perm::$shopId;         //店铺ID
            $keyword = request_string("keys");
            $start_time = request_string("start_date");
            $end_time = request_string("end_date");
            $state = request_int("status");
            
            if ($keyword) {
                if ($type == Order_ReturnModel::RETURN_TYPE_GOODS) {
                    $cond_row['order_goods_name:LIKE'] = "%" . $keyword . "%";
                } else {
                    $cond_row['order_number'] = $keyword;
                }
            }
            if ($state) {
                if ($state == 3) {
                    $cond_row['return_state:in'] = array(3, 4, 5);
                    $cond_row['return_shop_handle'] = 3;
                } else {
                    $cond_row['return_state'] = $state;
                }
            }
            if ($type == Order_ReturnModel::RETURN_TYPE_GOODS) {
                $cond_row['return_type'] = Order_ReturnModel::RETURN_TYPE_GOODS;
            } else {
                $cond_row['return_type:!='] = Order_ReturnModel::RETURN_TYPE_GOODS;
            }
            if ($start_time) {
                $cond_row['return_add_time:>='] = $start_time;
            }
            if ($end_time) {
                $cond_row['return_add_time:<='] = $end_time;
            }
            
            $data = $this->orderReturnModel->getReturnList($cond_row, array('return_add_time' => 'DESC'), $page, $rows);
            // print_r($data);
            // exit;
            $goods_ids = array_column($data['items'], "order_goods_id");
            
            if ($goods_ids) {
                $goods = $this->orderGoodsModel->getByWhere(array("order_goods_id:IN" => $goods_ids));
                foreach ($data['items'] as $k => $v) {
                    if ($v['order_goods_id']) {
                        $data['items'][$k]['good'] = $goods[$v['order_goods_id']];
                    }
                }
            }
            
            $Yf_Page->totalRows = $data['totalsize'];
            $data['page'] = $Yf_Page->prompt();
            $data['keys'] = $keyword;
            $data['state'] = $state;
            $data['start_date'] = $start_time;
            $data['end_date'] = $end_time;
            return $data;
        }
        
        public function goodsReturn()
        {
            $act = request_string('act');
            
            if ($act == "detail") {
                $data = $this->detail();
                $this->view->setMet('detail');
            } else {
                $data = $this->listReturn(Order_ReturnModel::RETURN_TYPE_GOODS);
                
                //分销商分销的商品
                $GoodsCommonModel = new Goods_CommonModel();
                $Order_GoodsModel = new Order_GoodsModel();
                $dist_commons = $GoodsCommonModel->getByWhere(array('shop_id' => Perm::$shopId, "common_parent_id:>" => 0, 'product_is_behalf_delivery' => 1));
                
                if (!empty($dist_commons)) {
                    $dist_common_ids = array_column($dist_commons, 'common_id');
                }
                foreach ($data['items'] as $key => $value) {
                    if ($value['order_goods_id']) {
                        $order_goods_base = $Order_GoodsModel->getOne($value['order_goods_id']);
                        $data['items'][$key]['common_id'] = $order_goods_base['common_id'];
                    }
                }
            }
            if ($this->typ == "json") {
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        

        //商家审核退款/货订单
        /*
         * 1.退款退货操作都是先走agreeReturn这个方法。
         * 2.在这个方法中判断是退款还是退货，退款操作就将状态修改4（商家同意退款），如果是退货状态修改为2（等待买家退货）。
         * 3.买家发货后商家通过agreeGoods这个方法确认商家发货,将退货状态修改为4
         * */
        public function agreeReturn()
        {
            $Order_StateModel = new Order_StateModel();
            $order_return_id = request_int("order_return_id");
            $return_shop_message = request_string("return_shop_message");
            $return = $this->orderReturnModel->getOne($order_return_id);
            if ($return['return_state'] == Order_ReturnModel::RETURN_SELLER_PASS) {
                $msg = __('已经退款，请刷新页面。');
                $status = 200;
                $this->data->addBody(-140, array(), $msg, $status);
                return false;
            }
             
            $rs_row = array();
            
            $msg = '';
            $order_finish = false;
            $shop_return_amount = 0;
            $money = 0;
            
            //开启事物
            $this->orderReturnModel->sql->startTransactionDb();
            
            $matche_row = array();
            //有违禁词
            if (Text_Filter::checkBanned($return_shop_message, $matche_row)) {
                
                $msg = __('含有违禁词');
                
                $status = 250;
                $this->data->addBody(-140, array(), $msg, $status);
                return false;
            }
            
            //判断该笔退款金额的订单是否已经结算
            $Order_BaseModel = new Order_BaseModel();
            $order_base = $Order_BaseModel->getOne($return['order_number']);

            //查找订单的支付方式
            $key = Yf_Registry::get('shop_api_key');
            $formvars = array();
            $formvars['app_id'] = Yf_Registry::get('shop_app_id');
            $formvars['uorder_id'] = $order_base['payment_other_number'];

            $uoreder = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=getUorderInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);

            $payment_channel_code = $uoreder['data']['payment_channel_code'];

            // 如果是虚拟订单退款，则将冻结的兑换码变成已失效！
            if ($order_base['order_is_virtual']) {
                // 如果为虚拟订单，即查询已冻结的兑换码，
                $Order_GoodsVirtualCodeModel = new Order_GoodsVirtualCodeModel();
                $cond_row = array();
                $cond_row['order_id'] = $order_base['order_id'];
                $cond_row['virtual_code_status'] = Order_GoodsVirtualCodeModel::VIRTUAL_CODE_FROZEN;
            
                $frozencode = $Order_GoodsVirtualCodeModel->getVirtualCode($cond_row);

                // 根据$frozencode 的 值 查询 虚拟兑换码 列表 并将虚拟码code的变成3
                $rs_row = array();
                $update['virtual_code_status'] = Order_GoodsVirtualCodeModel::VIRTUAL_CODE_INVALID;
                $update['virtual_code_usetime'] = date('Y-m-d H:i:s', time());                            //失效时间
    
                if (is_array($frozencode)) {
                    foreach ($frozencode as $value) {
                        $result = $Order_GoodsVirtualCodeModel->editCode($value, $update);
                        check_rs($result, $rs_row);
                    }
                }
            }

            //判断该笔订单是否已经收货，如果没有收货的话，不扣除卖家资金。已确认收货则扣除卖家资金
            if ($order_base['order_status'] == $Order_StateModel::ORDER_FINISH) {
                
                $order_finish = false;
                
                //获取商家的账户资金资源
                $key = Yf_Registry::get('shop_api_key');
                $formvars = array();
                $user_id = Perm::$userId;
                $formvars['user_id'] = $user_id;
                $formvars['app_id'] = Yf_Registry::get('shop_app_id');
                
                $money_row = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=getUserResourceInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);
                $user_money = 0;
                $user_money_frozen = 0;
                if ($money_row['status'] == '200') {
                    $money = $money_row['data'];
                    
                    $user_money = $money['user_money'];
                    $user_money_frozen = $money['user_money_frozen'];
                }
                
                $shop_return_amount = $return['return_cash'] - $return['return_commision_fee'];
                
                //获取该店铺最新的结算结束日期
                $Order_SettlementModel = new Order_SettlementModel();
                $settlement_last_info = $Order_SettlementModel->getLastSettlementByShopid(Perm::$shopId, $return['order_is_virtual']);
                
                if ($settlement_last_info) {
                    $settlement_unixtime = $settlement_last_info['os_end_date'];
                } else {
                    $settlement_unixtime = '';
                }
                
                $settlement_unixtime = strtotime($settlement_unixtime);
                $order_finish_time = $order_base['order_finished_time'];
                $order_finish_unixtime = strtotime($order_finish_time);

                if ($settlement_unixtime >= $order_finish_unixtime) {
                    //结算时间大于订单完成时间。需要扣除卖家的现金账户
                    $money = $user_money;
                    $pay_type = 'cash';
                } else {
                    //结算时间小于订单完成时间。需要扣除卖家的冻结资金,如果冻结资金不足就扣除账户余额
                    $money = $user_money_frozen + $user_money;
                    $pay_type = 'frozen_cash';
                }
            } else {
                $order_finish = true;
            }

            //判断该笔退单的商家是否是当前商家
            if ($return['seller_user_id'] == Perm::$shopId) {
                $shop_return_amount = sprintf("%.2f", $shop_return_amount);
                $money = sprintf("%.2f", $money);
                //如果是采用可原路退还支付方式支付的订单，则不需要判断余额
                $array=['wx_native','app_wx_native','app_h5_wx_native','alipay','alipayMobile'];
                if (($shop_return_amount <= $money) || $order_finish || in_array($payment_channel_code,$array)) {
                    $data['return_shop_message'] = $return_shop_message;
                    if ($return['return_goods_return'] == Order_ReturnModel::RETURN_GOODS_RETURN) {
                        //退货
                        $data['return_state'] = Order_ReturnModel::RETURN_SELLER_PASS;
                    } else {
                        //退款
                        $data['return_state'] = Order_ReturnModel::RETURN_SELLER_GOODS;
                    }
                    $data['return_shop_handle'] = Order_ReturnModel::RETURN_SELLER_PASS;
                    $data['return_shop_time'] = get_date_time();
                    $flag = $this->orderReturnModel->editReturn($order_return_id, $data);
                    check_rs($flag, $rs_row);
                    //如果订单为分销商采购单，扣除分销商的钱
                    if ($order_base['order_source_id']) {
                        $dist_order = $Order_BaseModel->getOneByWhere(array('order_id' => $order_base['order_source_id']));

                        if (!empty($dist_order)) {
                            $dist_return_order = $this->orderReturnModel->getOneByWhere(array('order_number' => $dist_order['order_id'], 'return_type' => $return['return_type']));
                            $flag = $this->orderReturnModel->editReturn($dist_return_order['order_return_id'], $data);
                            check_rs($flag, $rs_row);
                        }
                    }

                    if ($flag && !$order_finish) {
                        //扣除卖家的金额
                        $key = Yf_Registry::get('shop_api_key');
                        $formvars = array();
                        $user_id = Perm::$userId;
                        $formvars['user_id'] = $user_id;
                        $formvars['user_name'] = $order_base['seller_user_name'];
                        $formvars['app_id'] = Yf_Registry::get('shop_app_id');
                        $formvars['money'] = $shop_return_amount * (-1);
                        $formvars['pay_type'] = $pay_type;
                        $formvars['reason'] = '退款';
                        $formvars['order_id'] = $order_base['order_id'];
                        $formvars['goods_id'] = $return['order_goods_id'];


                        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=editReturnUserResourceInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);
                        
                        $dist_rs['status'] = 200;
                        if (isset($dist_return_order) && !empty($dist_return_order)) {
                            $key = Yf_Registry::get('shop_api_key');
                            $formvars = array();
                            $user_id = Perm::$userId;
                            $formvars['user_id'] = $dist_order['seller_user_id'];
                            $formvars['user_name'] = $dist_order['seller_user_name'];
                            $formvars['money'] = ($dist_return_order['return_cash'] - $dist_return_order['return_commision_fee']) * (-1);
                            $formvars['order_id'] = $dist_order['order_id'];
                            $formvars['goods_id'] = 0;
                            $formvars['app_id'] = Yf_Registry::get('shop_app_id');
                            $formvars['pay_type'] = $pay_type;
                            $formvars['reason'] = '退款';
                            
                            $dist_rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=editReturnUserResourceInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);
                        }
                        
                        if ($rs['status'] == 200 && $dist_rs['status'] == 200) {
                            $flag = true;
                        } else {
                            $flag = false;
                        }
                        check_rs($flag, $rs_row);
                    }
                } else {
                    $flag = false;
                    $msg = __('账户余额不足');
                    check_rs($flag, $rs_row);
                }
                
            } else {
                $flag = false;
                $msg = __('failure1');
                check_rs($flag, $rs_row);
            }
            $flag = is_ok($rs_row);
            if ($flag && $this->orderReturnModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
                if ($return['return_type'] == Order_ReturnModel::RETURN_TYPE_ORDER || $return['return_type'] == Order_ReturnModel::RETURN_TYPE_VIRTUAL) {
                    //退货管理，同意退货（这一步不是收到货物）
                    if (Web_ConfigModel::value('Plugin_Fenxiao')) {
                        Fenxiao::getInstance()->cancelOrder($order_return_id);
                    }
                }
                //退款申请
                if ($return['return_type'] != 2) {
                    //当前是否开启平台审核
                    $plat_is_open = Web_ConfigModel::value("plat_is_open");
                    //平台未开启退款退货审核
                    if ($plat_is_open != 1) {
                        $data_row = $this->agree($order_return_id, $return_shop_message);
                        $status = $data_row['status'];
                        $msg = $data_row['msg'];
                        if ( $status != 200) {
                                                            $data1['return_state'] = Order_ReturnModel::RETURN_WAIT_PASS;
                                $data1['return_shop_handle'] = 1;
                                $rs_row = array();
                                $edit_flag = $this->orderReturnModel->editReturn($order_return_id, $data1);
                            $this->data->addBody(-140, array(),"退款失败！", $status);
                            return false;
                        }
                    }
                }
            } else {
                $this->orderReturnModel->sql->rollBackDb();
                $status = 250;
                $msg = $msg ? $msg : __('failure2');
            }
            
            $data = array();
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        public function agreeGoods()
        {
            $rs_row = array();
            $order_return_id = request_int("order_return_id");
            $return = $this->orderReturnModel->getOne($order_return_id);
            
            if ($return['return_state'] == Order_ReturnModel::RETURN_SELLER_GOODS) {
                throw new Exception('已收到货物');
            }
            
            //开启事物
            $this->orderReturnModel->sql->startTransactionDb();
            
            $Order_BaseModel = new Order_BaseModel();
            $order_base = $Order_BaseModel->getOne($return['order_number']);
            
            if ($return['seller_user_id'] == Perm::$shopId) {
                $data['return_state'] = Order_ReturnModel::RETURN_SELLER_GOODS;
                $flag = $this->orderReturnModel->editReturn($order_return_id, $data);
                check_rs($flag, $rs_row);
                
                //如果订单为分销商采购单，扣除分销商的钱
                if ($order_base['order_source_id']) {
                    
                    $dist_order = $Order_BaseModel->getOneByWhere(array('order_id' => $order_base['order_source_id']));

                    if (!empty($dist_order)) {
                        $dist_return_order = $this->orderReturnModel->getOneByWhere(array('order_number' => $dist_order['order_id'], 'return_type' => $return['return_type']));

                        $flag = $this->orderReturnModel->editReturn($dist_return_order['order_return_id'], $data);
                        check_rs($flag, $rs_row);
                    }
                }

                $flag = is_ok($rs_row);
                
                if ($flag && $this->orderReturnModel->sql->commitDb()) {
                    //退款退货提醒
                    $message = new MessageModel();
                    $message->sendMessage('Refund return reminder', $return['buyer_user_id'], $return['buyer_user_account'], $order_id = NULL, $shop_name = NULL, 0, 1);
                    $status = 200;
                    $msg = __('success');
                    
                    if (Web_ConfigModel::value('Plugin_Fenxiao')) {
                        $order_id = $return['order_number'];
                        Fenxiao::getInstance()->cancelOrder($order_return_id);
                        Fenxiao::getInstance()->confirmOrder($order_id);
                    }

                    //当前是否开启平台审核
                    $plat_is_open = Web_ConfigModel::value("plat_is_open");
                    //平台未开启退款退货审核
                    if ($plat_is_open != 1) {
                        $data_row = $this->agree($order_return_id, "商家收到货物，同意退款");
                        $status = $data_row['status'];
                        $msg = $data_row['msg'];
                    }
                } else {
                    $status = 250;
                    $msg = __('failure3');
                }
            } else {
                $status = 250;
                $msg = __('failure4');
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        public function closeReturn()
        {
            $Order_StateModel = new Order_StateModel();
            $order_return_id = request_int("order_return_id");
            $return_shop_message = request_string("return_shop_message");
            
            $matche_row = array();
            //有违禁词
            if (Text_Filter::checkBanned($return_shop_message, $matche_row)) {
                $data = array();
                $msg = __('failure');
                $status = 250;
                $this->data->addBody(-140, array(), $msg, $status);
                return false;
            }
            
            $return = $this->orderReturnModel->getOne($order_return_id);
            $order_goods_id = $return['order_goods_id'];
            $return_type = $return['return_type'];
            //分销订单，同时修改DD,SP订单状态
            if($return){
                $Order_BaseModel = new Order_BaseModel();
                $order_base = $Order_BaseModel->getOne($return['order_number']);
                $order_source_id = $order_base['order_source_id'];
            }
            
            if ($return['seller_user_id'] == Perm::$shopId) {
                $data['return_shop_message'] = $return_shop_message;
                $data['return_state'] = Order_ReturnModel::RETURN_SELLER_UNPASS;
                $data['return_shop_handle'] = Order_ReturnModel::RETURN_SELLER_UNPASS;
                $data['return_shop_time'] = get_date_time();
                
                $rs_row = array();
                $this->orderReturnModel->sql->startTransactionDb();
                $edit_flag = $this->orderReturnModel->editReturn($order_return_id, $data);
                check_rs($edit_flag, $rs_row);

                if($order_source_id){
                    $order_source_row['order_number'] = $order_source_id;
//                    $order_source_row['order_goods_id'] = $order_goods_id;
                    $order_source_row['return_type'] = $return_type;
                    $order_source = $this->orderReturnModel->getOneByWhere($order_source_row);
                    $edit_flag2 = $this->orderReturnModel->editReturn($order_source['order_return_id'], $data);
                }
                check_rs($edit_flag2, $rs_row);

                if ($return['order_is_virtual']) {
                    // 如果为虚拟订单，即查询已冻结的兑换码，
                    $Order_GoodsVirtualCodeModel = new Order_GoodsVirtualCodeModel();
                    $cond_row = array();
                    $cond_row['order_id'] = $return['order_number'];
                    $cond_row['virtual_code_status'] = Order_GoodsVirtualCodeModel::VIRTUAL_CODE_FROZEN;
                    
                    $frozencode = $Order_GoodsVirtualCodeModel->getVirtualCode($cond_row);
                    
                    // 根据$frozencode 的 值 查询 虚拟兑换码 列表 并将虚拟码code的变成0
                    $rs_row = array();
                    $update['virtual_code_status'] = Order_GoodsVirtualCodeModel::VIRTUAL_CODE_NEW;
                    
                    if (is_array($frozencode)) {
                        foreach ($frozencode as $value) {
                            $result = $Order_GoodsVirtualCodeModel->editCode($value, $update);
                        }
                        check_rs($result, $rs_row);
                    }
                }
                
                $flag = is_ok($rs_row);
                if ($flag && $this->orderReturnModel->sql->commitDb()) {
                    $status = 200;
                    $msg = __('success');
                } else {
                    $this->orderReturnModel->sql->rollBackDb();
                    $status = 250;
                    $msg = __('failure');
                }
            } else {
                $status = 250;
                $msg = __('failure');
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }

        private function refund($order_return_id)
        {
            $order_return = $this->orderReturnModel->getOne($order_return_id);
            $refund_amount = $order_return['return_cash'];
            $return_number = $order_return['return_code'];
            $return_goods_name = $order_return['order_goods_name'];

            $order_id = $order_return['order_number'];
            $order = $this->orderBaseModel->getOne($order_id);
            $shop_id = $order['shop_id'];

            $key = Yf_Registry::get('paycenter_api_key');
            $url = Yf_Registry::get('paycenter_api_url');
            $paycenter_app_id = Yf_Registry::get('paycenter_app_id');

            $formvars = array();
            $formvars['shop_id'] = $shop_id;
            $formvars['order_number'] = $order_id;
            $formvars['refund_amount'] = $refund_amount;
            $formvars['return_number'] = $return_number;
            $formvars['return_goods_name'] = $return_goods_name;
            $formvars['app_id'] = $paycenter_app_id;

            $res = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Refund&met=refund&typ=json', $url), $formvars);

            if ($res['status'] == 200) {
                $flag = true;
            } else {
                $flag = false;
                throw new Exception($res['msg']);
            }
            return $flag;
        }
        
        public function agreev(){
            $id = request_int('id');
            $return_platform_message = '平台同意退款';
            $this->agree($id,$return_platform_message);
        }
        //商家审核退款退货
        public function agree($order_return_id, $return_platform_message)
        {
            $Order_StateModel = new Order_StateModel();
            $Order_ReturnModel = new Order_ReturnModel();
            $Order_BaseModel = new Order_BaseModel();
            $Order_GoodsModel = new Order_GoodsModel();
            $return = $Order_ReturnModel->getOne($order_return_id);
            //开启事务
            $Order_ReturnModel->sql->startTransactionDb();
            //判断平台是否已经审核过
            if ($return['return_state'] < Order_ReturnModel::RETURN_PLAT_PASS) {
                //判断商家是否同意退款
                 if ($return['return_shop_handle'] == Order_ReturnModel::RETURN_SELLER_UNPASS) {
                    //不同意
                    $data = array();
                    $data['return_platform_message'] = $return_platform_message;
                    $data['return_state'] = Order_ReturnModel::RETURN_PLAT_PASS;
                    $data['return_finish_time'] = get_date_time();
                    $rs_row = array();
                    $edit_flag = $Order_ReturnModel->editReturn($order_return_id, $data);
                    check_rs($edit_flag, $rs_row);
                    //根据order_id查找订单信息
                    $order_base = $Order_BaseModel->getOne($return['order_number']);
                    //如果是分销商的进货单则同时退掉买家订单
                    if ($order_base['order_source_id']) {
                        $dist_return = $Order_ReturnModel->getOneByWhere(array('order_number' => $order_base['order_source_id'], 'return_type' => $return['return_type']));
                        $this->refuseDist($dist_return['order_return_id'], $data);
                    }

                    if ($return['return_goods_return']) {
                        //商家拒绝退款退货3
                        $goods_data['goods_refund_status'] = Order_GoodsModel::REFUND_REF;
                        $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $goods_data);
                        check_rs($edit_flag, $rs_row);
                    } else {
                        $goods_data['goods_return_status'] = Order_GoodsModel::REFUND_REF;
                        $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $goods_data);
                        check_rs($edit_flag, $rs_row);
                    }

                } else {
                    if ($this->yunshanstatus == 1) {
                        $order_id = $return['order_number'];
                        $order_base = $Order_BaseModel->getOne($order_id);
                        $returnfalg = "1" ; // 默认不成功
                        // 如果已经发起提现申请了，不能线上审核退款了。
                        $Ve_TxianFlowersModel  = new Ve_TxianFlowersModel();
                        $where = array();
                        $where["order_id:LIKE"] = "%".$order_id."%";
                        $where["status:IN"] = array("2","3","4");
                        $tixisnflows = $Ve_TxianFlowersModel -> getOneByWhere($where);
                        if($tixisnflows){
                            $data  = array();
                            $msg = "提现中的订单，请线下处理";
                            $status = 250 ;
                         return  $this->data->addBody(-140, $data, $msg, $status);
                        }

                        $formvars = array();
                        $formvars['app_id'] = Yf_Registry::get('shop_app_id');
                        $formvars['order_id']  =  $order_id ;
                        $key = Yf_Registry::get('shop_api_key');
                        $url = Yf_Registry::get('paycenter_api_url');
                        $uorderinfo  = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=getVeUordersBy&typ=json', $url), $formvars);
                             $this->Order_ReturnModel->sql->rollBackDb();
                        if($uorderinfo["status"] == "200"){
                           $uorder = $uorderinfo["data"] ;
                        }else{
                       
                             $data  = array();
                             $msg = "订单信息不完整，无法在线退款" ;
                             $status = 250 ;
                             return  $this->data->addBody(-140, $data, $msg, $status);
                        }

                       $order_base["return"] = $return  ; // 退款单的信息
                       $order_base["uorder"] = $uorder  ; // 合并订单信息
                       // 平台同意退款，执行接口的程序功能
                       // 执行银联的接口的功能
                       $Ve_Ylpays  = new Ve_Ylpays();
                       $res = $Ve_Ylpays -> refundorder($order_base);
                       if($res["status"] != 200){
                         // 接口不通过，直接返回失败
                         $data  = array();
                         $msg = "接口银联接口退款失败".$res["msg"];
                         $status = 250;
                         return  $this->data->addBody(-140, $data, $msg, $status);
                       }
                    }

                    //同意
                    $data = array();
                    $data['return_platform_message'] = $return_platform_message;
                    $data['return_state'] = Order_ReturnModel::RETURN_PLAT_PASS;
                    $data['return_finish_time'] = get_date_time();
                    $rs_row = array();
                    $edit_flag = $Order_ReturnModel->editReturn($order_return_id, $data);
                    check_rs($edit_flag, $rs_row);
                    //根据order_id查找订单信息
                    $order_base = $Order_BaseModel->getOne($return['order_number']);
                    $data['return_goods_return'] = $return['return_goods_return'];
                    if ($return['return_goods_return']) {
                        $Shop_BaseModel = new Shop_BaseModel();
                        $shop_detail = $Shop_BaseModel->getOne($order_base['shop_id']);
                        /*if ($shop_detail['shop_type'] == 2) {
                            $flag = $this->edit_product($return['order_number'], $return['order_goods_num']);

                            $data['edit_product'] = $flag;
                        }*/
                    }

                    //如果有平台红包的订单 退红包
                    $RedPacket_BaseModel = new RedPacket_BaseModel();
                    $Order_BaseModel = new Order_BaseModel();
                    $red_data = $Order_BaseModel->getOneByWhere(['order_id' => $return['order_number']]);
                    $red_arr = $RedPacket_BaseModel->getOneByWhere(['redpacket_code' => $red_data['redpacket_code']]);
                    if ($red_arr) {
                        $red_arrts = $RedPacket_BaseModel->editRedPacket($red_arr['redpacket_id'], ['redpacket_state' => 1]);
                    }

                    //如果有代金券的订单 退代金券
                    if ($red_data['voucher_id']) {
                        $Voucher_BaseModel = new Voucher_BaseModel();
                        $voucher_info = $Voucher_BaseModel->getOne($red_data['voucher_id']);
                        if ($voucher_info) {
                            $row['voucher_state'] = 1;
                            $row['voucher_order_id'] = '';
                            $v_flag = $Voucher_BaseModel->editVoucher($red_data['voucher_id'], $row);
                        }
                    }

                    //判断该商品是否是三级分销的商品，如果是三级分销的商品需要退还分销佣金
                    $Order_GoodsModel = new Order_GoodsModel();
                    $dc = $Order_GoodsModel->refundDirectsellercommission($return['order_goods_id'], $return['order_goods_num']);

                    //如果是分销商的进货单则同时退掉买家订单
                    if ($order_base['order_source_id']) {
                        $dist_return = $Order_ReturnModel->getOneByWhere(array('order_number' => $order_base['order_source_id'], 'return_type' => $return['return_type']));
                        $this->agreeDist($dist_return['order_return_id'], $data);
                    }
                    if ($return['return_goods_return']) {
                        //如果是退货情况下，退还三级分销佣金
                        $Order_GoodsModel->returnDirectsellercommission($return['order_goods_id'], $dc);

                        //商品退换情况为完成2
                        $goods_data['goods_refund_status'] = Order_GoodsModel::REFUND_COM;
                        $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $goods_data);
                        check_rs($edit_flag, $rs_row);

                    } else {

                        /*将商品库存加回商品中*/
                        $Goods_BaseModel = new Goods_BaseModel();
                        $Goods_BaseModel->returnGoodsStock($return['order_goods_id'], $return['order_goods_num'], $return['behalf_deliver']);

                        $goods_data['goods_return_status'] = Order_GoodsModel::REFUND_COM;
                        $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $goods_data);
                        check_rs($edit_flag, $rs_row);

                    }

                    $ogoods_data = array();
                    $ogoods_data['order_goods_returnnum'] = $return['order_goods_num'];
                    $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $ogoods_data, true);
                    check_rs($edit_flag, $rs_row);
                    //退款金额，退货数量，交易佣金退款更新到订单表中
                    $order_edit = array();
                    //判断商品金额是否全都退还，如果全部退还订单状态修改为完成状态(用订单商品数判断)
                    //订单中所有商品数量
                    $order_goods = $Order_GoodsModel->getByWhere(array('order_id' => $return['order_number'], 'order_goods_amount:>' => 0));
                    $order_all_goods_num = array_sum(array_column($order_goods, 'order_goods_num'));

                    $where = array(
                        'order_number' => $return['order_number'],
                        'return_state' => Order_ReturnModel::RETURN_PLAT_PASS,
                        'return_shop_handle:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS,
                    );
                    //查找该笔订单已经完成的退款，退货
                    $order_return = $Order_ReturnModel->getByWhere($where);
                    //订单已经退还的商品数量
                    $order_return_num = array_sum(array_column($order_return, 'order_goods_num'));

                    $order_edit['order_refund_amount'] = $return['return_cash'];
                    $order_edit['order_return_num'] = $return['order_goods_num'];
                    $order_edit['order_commission_return_fee'] = $return['return_commision_fee'];
                    $order_edit['order_rpt_return'] = $return['return_rpt_cash'];

                    $edit_flag = $Order_BaseModel->editBase($return['order_number'], $order_edit, true);
                    check_rs($edit_flag, $rs_row);
                    if ($order_all_goods_num == $order_return_num && $order_base['order_status'] != $Order_StateModel::ORDER_FINISH) {
                        $order_edit_row = array();
                        $order_edit_row['order_status'] = $Order_StateModel::ORDER_FINISH;
                        $order_edit_row['order_finished_time'] = date('Y-m-d H:i:s');
                        $edit_flag2 = $Order_BaseModel->editBase($return['order_number'], $order_edit_row);
                        check_rs($edit_flag2, $rs_row);
                        //如果全部退款，增加卖家的流水
                        $formvars = array();
                        $formvars['app_id'] = Yf_Registry::get('shop_app_id');
                        $formvars['user_id'] = $order_base['buyer_user_id']; //收款人
                        $formvars['user_account'] = $order_base['buyer_user_account'];
                        $formvars['seller_id'] = $order_base['seller_user_id']; //付款人
                        $formvars['seller_account'] = $order_base['seller_user_name'];
                        $formvars['amount'] = $return['return_cash'];
                        $formvars['return_commision_fee'] = $return['return_commision_fee'];
                        $formvars['order_id'] = $return['order_number'];
                        $formvars['goods_id'] = $return['order_goods_id'];
                        $formvars['uorder_id'] = $order_base['payment_other_number'];
                        $formvars['payment_id'] = $order_base['payment_id'];


                        $key = Yf_Registry::get('shop_api_key');
                        $url = Yf_Registry::get('paycenter_api_url');

                        $return_rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=refundShopTransfer&typ=json', $url), $formvars);
                        if ($return_rs['status'] != 200) {
                            check_rs(false, $rs_row);
                        }
                    }
                    if ($edit_flag) {
                        //判断该笔订单是否是主账号支付，如果是主账号支付，则将退款金额退还主账号
                        if ($order_base['order_sub_pay'] == Order_StateModel::SUB_SELF_PAY) {
                            $return_user_id = $return['buyer_user_id'];
                            $return_user_name = $return['buyer_user_account'];
                        }
                        if ($order_base['order_sub_pay'] == Order_StateModel::SUB_USER_PAY) {
                            //查找主管账户用户名
                            $User_BaseModel = new  User_BaseModel();
                            $sub_user_base = $User_BaseModel->getOne($order_base['order_sub_user']);

                            $return_user_id = $order_base['order_sub_user'];
                            $return_user_name = $sub_user_base['user_account'];
                        }
                        $key = Yf_Registry::get('shop_api_key');
                        $url = Yf_Registry::get('paycenter_api_url');
                        $shop_app_id = Yf_Registry::get('shop_app_id');

                        $formvars = array();
                        $formvars['app_id'] = $shop_app_id;
                        $formvars['user_id'] = $return_user_id;
                        $formvars['user_account'] = $return_user_name;
                        $formvars['seller_id'] = $return['seller_user_id'];
                        $formvars['seller_account'] = $return['seller_user_account'];
                        $formvars['amount'] = $return['return_cash'];
                        $formvars['return_commision_fee'] = $return['return_commision_fee'];
                        $formvars['order_id'] = $return['order_number'];
                        $formvars['goods_id'] = $return['order_goods_id'];
                        $formvars['payment_id'] = $order_base['payment_id'];
                        $formvars['order_return_id'] = $return['order_return_id'];

                        //SP分销单没有payment_other_number这个字段值会报错，所以在此做判断
                        if ($order_base['payment_other_number']) {
                            $formvars['uorder_id'] = $order_base['payment_other_number'];
                        } else {
                            $formvars['uorder_id'] = $order_base['payment_number'];
                        }
                        //平台同意退款（只增加买家的流水）
                        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=refundBuyerTransfer&typ=json', $url), $formvars);

                        file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'abs.php',print_r($rs,true),FILE_APPEND);
                        $data['for'] = $formvars;
                        if ($rs['status'] == 200) {
                            check_rs(true, $rs_row);
                        } else {
                            check_rs(false, $rs_row);
                        }
                        $edit_flag = is_ok($rs_row);
                    }
                    //如果订单金额全数退还需要将订单商品，支付中心的订单状态修改为订单完成(未发货)
                    if ($order_all_goods_num == $order_return_num && $order_base['order_status'] == Order_StateModel::ORDER_PAYED) {
                        $goods_data['order_goods_status'] = $Order_StateModel::ORDER_FINISH;
                        $order_goods_ids_row = $Order_GoodsModel->getByWhere(array('order_id' => $return['order_number']));
                        $order_goods_ids = current($order_goods_ids_row);
                        $ed_flag = $Order_GoodsModel->editGoods($order_goods_ids['order_goods_id'], $goods_data);
                        check_rs($ed_flag, $rs_row);

                        //将需要确认的订单号远程发送给Paycenter修改订单状态
                        //远程修改paycenter中的订单状态
                        $key = Yf_Registry::get('shop_api_key');
                        $url = Yf_Registry::get('paycenter_api_url');
                        $shop_app_id = Yf_Registry::get('shop_app_id');
                        $formvars = array();

                        $formvars['order_id'] = $return['order_number'];
                        $formvars['app_id'] = $shop_app_id;
                        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');

                        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=confirmOrder&typ=json', $url), $formvars);

                        if ($rs['status'] == 250) {
                            $rs_flag = false;
                            check_rs($rs_flag, $rs_row);
                        }

                    }

                    // 如果是虚拟商品
                    if ($order_base['order_is_virtual']) {
                        $Order_GoodsVirtualCodeModel = new Order_GoodsVirtualCodeModel();
                        // 如果 冻结的数量 = 订单总数量 - 已使用数量，就修改该订单的状态为 已完成，并向平台发起 结算请求 到商家的冻结资金里。

                        // 已使用数量
                        $order_used = $Order_GoodsVirtualCodeModel->getByWhere(array('order_id' => $order_base['order_id'], 'virtual_code_status' => Order_GoodsVirtualCodeModel::VIRTUAL_CODE_USED));
                        $used = count($order_used);
                        $order_return_num_new = $order_return_num + $used;
                        // 如果订单商品的总数量 = 退单商品数量 + 已使用兑换码的商品数量
                        if ($order_all_goods_num == $order_return_num_new) {
                            // 编辑该订单为已完成，并且向 卖家打 去 已使用的兑换码所对应的金额
                            $edit_flag = $Order_BaseModel->editBase($order_base['order_id'], array('order_status' => Order_StateModel::ORDER_FINISH, 'order_finished_time' => get_date_time()));
                            check_rs($edit_flag, $rs_row);
                            //远程同步paycenter中的订单状态
                            $key = Yf_Registry::get('shop_api_key');
                            $url = Yf_Registry::get('paycenter_api_url');
                            $shop_app_id = Yf_Registry::get('shop_app_id');
                            $formvars = array();
                            $formvars['order_id'] = $order_base['order_id'];
                            $formvars['app_id'] = $shop_app_id;
                            $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
                            $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=confirmOrder&typ=json', $url), $formvars);

                            if ($rs['status'] == 250) {
                                check_rs(false, $rs_row);
                            }
                        }
                    }
                }
                $data['rs'] = $rs_row;

           
                $flag = is_ok($rs_row);
            } else {
                $flag = false;
                $data = array();

            }
            if ($flag && $Order_ReturnModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
                /**
                 *  加入统计中心
                 */
                //如果$return['order_goods_id']为0则为退款
                if ($return['return_goods_return']) {
                    $order_goods_data = $Order_GoodsModel->getOne($return['order_goods_id']);
                    $order_return_goods_id = $order_goods_data['goods_id'];
                    $order_goods_num = $return['order_goods_num'];
                } else {
                    $order_goods_data = $Order_GoodsModel->getGoodsListByOrderId($return['order_number']);
                    if (count($order_goods_data['items']) == 1) {
                        $order_return_goods_id = $order_goods_data['items'][0]['goods_id'];
                    } else {
                        $order_return_goods_id = 0;
                    }
                    $order_goods_num = $order_goods_data['items'][0]['order_goods_num'];
                }

                $analytics_data = array(
                    'order_id' => array($return['order_number']),
                    'return_cash' => $return['return_cash'],
                    'order_goods_num' => $order_goods_num,
                    'order_goods_id' => $order_return_goods_id,
                    'status' => 9    //暂时将退款退货统一处理
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsUpdateOrderStatus', $analytics_data);
                /******************************************************************/
            } else {
                $Order_ReturnModel->sql->rollBackDb();
                $status = 250;
                $msg = __('failure');
            }


            $data['msg'] = $msg;
            $data['status'] = $status;
            return $data;

        }

        public function refuseDist($order_return_id, $data)
        {
            $Order_ReturnModel = new Order_ReturnModel();
            $Order_GoodsModel = new Order_GoodsModel();

            $return = $Order_ReturnModel->getOne($order_return_id);

            $rs_row = array();
            $edit_flag = $Order_ReturnModel->editReturn($order_return_id, $data);
            check_rs($edit_flag, $rs_row);

            if ($return['return_goods_return']) {
                //商家拒绝退款退货3
                $goods_data['goods_refund_status'] = Order_GoodsModel::REFUND_REF;
                $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $goods_data);
                check_rs($edit_flag, $rs_row);
            } else {
                $goods_data['goods_return_status'] = Order_GoodsModel::REFUND_REF;
                $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $goods_data);
                check_rs($edit_flag, $rs_row);
            }

        }

        public function agreeDist($order_return_id, $data)
        {

            $Order_ReturnModel = new Order_ReturnModel();
            $Order_BaseModel = new Order_BaseModel();
            $Order_GoodsModel = new Order_GoodsModel();
            $Order_StateModel = new Order_StateModel();

            $return = $Order_ReturnModel->getOne($order_return_id);

            $dc = $Order_GoodsModel->refundDirectsellercommission($return['order_goods_id'], $return['order_goods_num']);

            //根据order_id查找订单信息
            $order_base = $Order_BaseModel->getOne($return['order_number']);

            $rs_row = array();

            $edit_flag = $Order_ReturnModel->editReturn($order_return_id, $data);
            check_rs($edit_flag, $rs_row);

            if ($return['return_goods_return']) {
                //如果是退货情况下，退还三级分销佣金
                $Order_GoodsModel->returnDirectsellercommission($return['order_goods_id'], $dc);

                //商品退换情况为完成2
                $goods_data['goods_refund_status'] = Order_GoodsModel::REFUND_COM;
                $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $goods_data);
                check_rs($edit_flag, $rs_row);
            } else {
                $goods_data['goods_return_status'] = Order_GoodsModel::REFUND_COM;
                $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $goods_data);
                check_rs($edit_flag, $rs_row);
            }
            $ogoods_data = array();
            $ogoods_data['order_goods_returnnum'] = $return['order_goods_num'];
            $edit_flag = $Order_GoodsModel->editGoods($return['order_goods_id'], $ogoods_data, true);
            check_rs($edit_flag, $rs_row);

            $sum_data['order_refund_amount'] = $return['return_cash'];
            $sum_data['order_commission_return_fee'] = $return['return_commision_fee'];
            $edit_flag = $Order_BaseModel->editBase($return['order_number'], $sum_data, true);
            check_rs($edit_flag, $rs_row);

            //订单中所有商品数量
            $order_goods = $Order_GoodsModel->getByWhere(array('order_id' => $return['order_number'], 'order_goods_amount:>' => 0));
            $order_all_goods_num = array_sum(array_column($order_goods, 'order_goods_num'));

            //查找该笔订单已经完成的退款，退货
            $order_return = $Order_ReturnModel->getByWhere(array(
                'order_number' => $return['order_number'],
                'return_state' => Order_ReturnModel::RETURN_PLAT_PASS
            ));
            //订单已经退还的商品数量
            $order_return_num = array_sum(array_column($order_return, 'order_goods_num'));

            if ($order_all_goods_num == $order_return_num && $order_base['order_status'] != $Order_StateModel::ORDER_FINISH) {
                $order_edit_row = array();
                $order_edit_row['order_status'] = $Order_StateModel::ORDER_FINISH;

                $edit_flag2 = $Order_BaseModel->editBase($return['order_number'], $order_edit_row);
                check_rs($edit_flag2, $rs_row);
            }

            if ($edit_flag) {
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');

                $formvars = array();
                $formvars['app_id'] = $shop_app_id;
                $formvars['user_id'] = $return['buyer_user_id'];
                $formvars['user_account'] = $return['buyer_user_account'];
                $formvars['seller_id'] = $return['seller_user_id'];
                $formvars['seller_account'] = $return['seller_user_account'];
                $formvars['amount'] = $return['return_cash'];
                $formvars['return_commision_fee'] = $return['return_commision_fee'];
                $formvars['order_id'] = $return['order_number'];
                $formvars['goods_id'] = $return['order_goods_id'];
                $formvars['uorder_id'] = $order_base['payment_other_number'];
                $formvars['payment_id'] = $order_base['payment_id'];

                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=refundBuyerTransfer&typ=json', $url), $formvars);
            }

            //如果订单金额全数退还需要将订单商品，支付中心的订单状态修改为订单完成(未发货)
            if ($order_all_goods_num == $order_return_num && $order_base['order_status'] == Order_StateModel::ORDER_PAYED) {
                $goods_data['order_goods_status'] = $Order_StateModel::ORDER_FINISH;
                $order_goods_ids_row = $Order_GoodsModel->getByWhere(array('order_id' => $return['order_number']));
                $order_goods_ids = current($order_goods_ids_row);

                $ed_flag = $Order_GoodsModel->editGoods($order_goods_ids['order_goods_id'], $goods_data);
                check_rs($ed_flag, $rs_row);

                //将需要确认的订单号远程发送给Paycenter修改订单状态
                //远程修改paycenter中的订单状态
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');
                $formvars = array();

                $formvars['order_id'] = $return['order_number'];
                $formvars['app_id'] = $shop_app_id;
                $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');

                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=confirmOrder&typ=json', $url), $formvars);

                if ($rs['status'] == 250) {
                    $rs_flag = false;
                    check_rs($rs_flag, $rs_row);
                }
            }

        }
        
    }
?>
