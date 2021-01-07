<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}
class Api_Order_OrderCtl extends Api_Controller
{
    public $userInfoModel     = null;
    public $tradeOrderModel = null;
    public $userPlus = null;
    public $shopDistributorModel = null;
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->userInfoModel     = new User_InfoModel();
        $this -> tradeOrderModel = new Order_BaseModel();
        $this->plus_switch = Web_ConfigModel::value('plus_switch');
        $this->shopDistributorModel = new Distribution_ShopDistributorModel();
    }

    /**
     * 生成实物订单
     *
     * @author     Zhuyt
     */
    public function addOrder()
    {
        $user_id = request_int('user_id');
        $userModel = new User_BaseModel();
        $user_rows = $userModel->getBase($user_id);
        $user_account = $user_rows[$user_id]['user_account'];
        $flag = true;
        $this->userPlus = perm::getUserPlusInfo($user_id);
        $receiver_name = request_string('receiver_name');
        $receiver_address = request_string('receiver_address');
        $receiver_phone = request_string('receiver_phone');
        $area_code = request_string('area_code')?:86;
        $invoice = request_string('invoice');
        $cart_id = request_row("cart_id");
        $shop_id = request_row("shop_id");
        $chain_id = request_row("chain_id");
        $remark = request_row("remark");
        $increase_arr = request_row("increase_arr");
        $voucher_id = request_row('voucher_id');
        $pay_way_id = request_int('pay_way_id');
        $invoice_id = request_int('invoice_id')?request_int('invoice_id'):'';
        $invoice_title = request_string('invoice_title');
        $invoice_content = request_string('invoice_content');
        $address_id = request_int('address_id');
        $from = request_string('from', 'pc');
        $rpacket_id = request_string('redpacket_id');
        $rpacket_id = json_decode($rpacket_id, true);
        $is_discount = request_int('is_discount');
        //确认订单来源
        if ($from == 'pc') {
            $order_from = Order_StateModel::FROM_PC;
        } elseif ($from == 'wap') {
            $order_from = Order_StateModel::FROM_WAP;
        } else {
            $order_from = Order_StateModel::FROM_PC;
        }
        if (request_string('wxapp') == 'wxapp') {
            if (!is_array($cart_id)) {
                $cart_id = explode(',', $cart_id);
            }
            if (!is_array($shop_id)) {
                $shop_id = explode(',', $shop_id);
            }
            if (!is_array($remark)) {
                $remark = explode(',', $remark);
            }
            if (!is_array($voucher_id)) {
                $voucher_id = explode(',', $voucher_id);
            }
        }
        //plus 开关
        $userPlusFlag = 0;//默认非plus会员

        if($this->userPlus && $this->userPlus['user_status'] !=3 && $this->userPlus['end_date']>time() && $this->plus_switch){
            $userPlusFlag = 1;//plus会员(包括试用和正式)
            //判断plus会员是否通过身份证信息审核
            $user_identity_statu = 0;
            $app_api_key     = Yf_Registry::get('paycenter_api_key');;
            $app_api_url     = Yf_Registry::get('paycenter_api_url');
            $app_api_id  = Yf_Registry::get('paycenter_app_id');
            $formvars = array(
                'user_id'=>$user_id,
            );
            $formvars['app_id'] = $app_api_id;
            $parms=  sprintf('%s?ctl=Api_%s&met=%s&typ=json', $app_api_url, 'User_Info', 'getUserInfo');
            $init_rs = get_url_with_encrypt($app_api_key,$parms, $formvars);
            $init_rs && $user_identity_statu = $init_rs['data']['user_identity_statu'];
            if($user_identity_statu!=2){
                //return $this -> data -> addBody(-140, array('code'=>'404'), __('实名认证信息未审核通过，请重新提交，谢谢！'), 250);
            }
        }
        $cart_id = is_array($cart_id) ? $cart_id : json_decode($cart_id, true);
        $shop_id = is_array($shop_id) ? $shop_id : json_decode($shop_id, true);
        $remark = is_array($remark) ? $remark : json_decode($remark, true);
        $voucher_id = is_array($voucher_id) ? $voucher_id : json_decode($voucher_id, true);
        $increase_arr = is_array($increase_arr) ? $increase_arr : json_decode($increase_arr, true);
        $increase_shop_row = array();

        //门店自提商品不需要考虑加价购商品
        if ($increase_arr && !$chain_id) {
            //检验加价购商品信息是否正确
            $increase_price_info = $this -> checkIncreaseGoods($increase_arr, $cart_id);
            $data['$increase_price_info'] = $increase_price_info;
            $data['$increase_arr'] = $increase_arr;
            $data['$cart_id'] = $cart_id;
            if (!is_array($increase_price_info)) {
                return $this -> data -> addBody(-140, $data, __('加价购商品信息有误'), 250);
            }
            //重组加价购商品
            //活动下的所有规则下的换购商品信息
            $Promotion = new Promotion();
            $increase_shop_row = $Promotion -> reformIncrease($increase_price_info);
        }
        if (request_string('app') == 1) {
            $cart_id = json_decode($cart_id, true);
        }
        //判断支付方式为在线支付还是货到付款,如果是货到付款则订单状态直接为待发货状态，如果是在线支付则订单状态为待付款
        if ($pay_way_id == PaymentChannlModel::PAY_ONLINE) {
            $order_status = Order_StateModel::ORDER_WAIT_PAY;
        }
        if ($pay_way_id == PaymentChannlModel::PAY_CONFIRM) {
            $order_status = Order_StateModel::ORDER_WAIT_PREPARE_GOODS;
        }
        if ($pay_way_id == PaymentChannlModel::PAY_CHAINPYA) {
            $order_status = Order_StateModel::ORDER_SELF_PICKUP;
        }
        //将店铺id和店铺留言组合为一个数组
        if (is_array($shop_id)) {
            $shop_remark = array_combine($shop_id, $remark);
        } else {
            $shop_remark[$shop_id] = $remark[0];
        }
        //开启事物
        $this -> tradeOrderModel -> sql -> startTransactionDb();
        //获取用户的折扣信息
        $user_info = $this->userInfoModel -> getOne($user_id);

        //获取用户的折扣信息  分销商购买不计算会员折扣
        $user_rate = $this->userInfoModel -> getUserGrade($user_id);

        //分销员开启，查找用户的上级
        if (Web_ConfigModel::value('Plugin_Directseller')) {
            $user_parent_id = $user_info['user_parent_id'];  //用户上级ID
            $user_parent = $this->userInfoModel -> getOne($user_parent_id);
            @$directseller_p_id = $user_parent['user_parent_id'];  //二级
            $user_g_parent = $this->userInfoModel -> getOne($directseller_p_id);
            @$directseller_gp_id = $user_g_parent['user_parent_id']; //三级
        }

        //重组代金券信息
        $shop_voucher_row = array();
        if ($voucher_id) {
            //查找代金券的信息
            $Voucher_BaseModel = new Voucher_BaseModel();
            $shop_voucher_row = $Voucher_BaseModel -> reformVoucher($voucher_id);
        }
        $cond_row = array('cart_id:IN' => $cart_id);
        $order_row = array();
        //购物车中的商品信息
        $shopBaseModel = new Shop_BaseModel();
        $shop_info = $shopBaseModel -> getOneByWhere(['user_id'=>$user_id]);
        $CartModel = new AppCartModel();
        $data = $CartModel -> getCardList($cond_row, $order_row,$user_id, $shop_info['id'],$is_discount, $chain_id);
        unset($data['invalid_goods']);
        if (!$data['count']) {
            $flag = false;
            return $this -> data -> addBody(-140, array(), __('请刷新页面后重试'), 250);
        }
        //定义一个新数组，存放店铺与订单商品详情订单商品
        $shop_order_goods_row = array();
        //计算购物车中每件商品的最后优惠的实际价格（使用代金券）
        /*
         * 店铺商品总价 = 加价购商品总价 + 购物车商品总价（按照限时折扣和团购价计算）
         *
         */
        $shop_order_goods_row = $this -> reformShopOrderGoods($data, $user_rate, $is_discount, $increase_shop_row, $shop_voucher_row);
        //平台红包券抵扣金额(用户没有开启会员折扣的情况下可用。)
        if ($rpacket_id && !$is_discount) {
            $redPacket_BaseModel = new RedPacket_BaseModel();
            $shop_order_goods_row = $redPacket_BaseModel -> computeRedPacket($shop_order_goods_row, $rpacket_id);
            if (!$shop_order_goods_row) {
                $flag = false;
                return $this -> data -> addBody(-140, array(), __('红包信息有误'), 250);
            }
        }
        unset($shop_order_goods_row['order_price']);
        //计算每个商品订单实际支付的金额，以及每件商品的实际支付单价为多少
        $shop_order_goods_row = $this -> computeShopPrice($shop_order_goods_row);
        foreach ($shop_order_goods_row as $sogkey => $sogval) {
            foreach ($sogval['goods'] as $soggkey => $soggval) {
                //将加价购商品从普通购物车商品数组中剔除，重新放入加价购商品数组中
                if (isset($soggval['redemp_price'])) {
                    $shop_order_goods_row[$sogkey]['increase_goods'][] = $shop_order_goods_row[$sogkey]['goods'][$soggkey];
                    unset($shop_order_goods_row[$sogkey]['goods'][$soggkey]);
                }
            }
        }
        //查找收货地址,如果是门店不需要计算运费
        $transport_cost = array();
        if (!$chain_id) {
            $transport_cost = $this -> getTransportCost($address_id, $cart_id);
        }
        $Number_SeqModel = new Number_SeqModel();
        $Order_BaseModel = new Order_BaseModel();
        $Order_GoodsModel = new Order_GoodsModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $PaymentChannlModel = new PaymentChannlModel();
        $Order_GoodsSnapshot = new Order_GoodsSnapshot();
        //合并支付订单的价格
        $uprice = 0;
        $inorder = '';
        $utrade_title = '';    //商品名称 - 标题

        foreach ($shop_order_goods_row as $key => $val) {
            $trade_title = '';
            //生成店铺订单
            //总结店铺的优惠活动
            $order_shop_benefit = '';
            if ($val['mansong_info']) {
                $order_shop_benefit = $order_shop_benefit . '满即送:';
                if ($val['mansong_info']['rule_discount']) {
                    $order_shop_benefit = $order_shop_benefit . '优惠' . format_money($val['mansong_info']['rule_discount']) . ' ';
                }
            }
            if ($val['user_rate'] < 100 && $is_discount && $user_rate < 100) {
                $order_shop_benefit = $order_shop_benefit . ' 会员折扣:' . $user_rate/10 . '折 ';
            }
            //计算店铺的代金券
            if ($val['voucher_id'] && !$is_discount) {
                $order_shop_benefit = $order_shop_benefit . ' 代金券:' . format_money($val['voucher_price']) . ' ';
            }
            //平台红包
            if ($rpacket_id && !$is_discount) {
                $order_shop_benefit = $order_shop_benefit . ' 平台红包:' . format_money($val['order_rpt_price']) . ' ';
            }
            $prefix = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));


            $order_number = $Number_SeqModel -> createSeq($prefix);
            //$Order_Seq = new OrderSeq();
            //$order_number = $prefix.$Order_Seq->get_order_no();
            $order_id = sprintf('%s-%s-%s-%s', 'DD', $val['shop_user_id'], $key, $order_number);


            //生成订单发票信息
            $Order_InvoiceModel = new Order_InvoiceModel();
            $order_invoice_id = $Order_InvoiceModel -> getOrderInvoiceId($invoice_id, $invoice_title, $invoice_content, $order_id);
            //开启会员折扣后，平台红包和代金券不可以使用
            if ($is_discount == 1) {
                $val['voucher_id'] = 0;
                $val['voucher_price'] = 0;
                $val['voucher_code'] = 0;
            }

            //plus 优惠金额
            //$plus_diff_price = 0;
            //$plus_rate = Web_ConfigModel::value("plus_rate");
            //if($userPlusFlag && $val['plus_diff_price']){
            //   $plus_diff_price=$val['plus_diff_price'];
            //$order_shop_benefit = $order_shop_benefit . ' 平台PLUS会员优惠（折扣：'.$plus_rate.'）:' . format_money($plus_diff_price) . ' ';
            //}


            $order_row = array();
            $order_row['order_id'] = $order_id;
            $order_row['shop_id'] = $key;
            $order_row['shop_name'] = $val['shop_name'];
            $order_row['buyer_user_id'] = $user_id;
            $order_row['buyer_user_name'] = $user_account;
            $order_row['seller_user_id'] = $val['shop_user_id'];
            $order_row['seller_user_name'] = $val['shop_user_name'];
            $order_row['order_date'] = date('Y-m-d');
            $order_row['order_create_time'] = get_date_time();
            $order_row['order_receiver_name'] = $receiver_name;
            $order_row['order_receiver_address'] = $receiver_address;
            $order_row['order_receiver_contact'] = $receiver_phone;
            $order_row['area_code'] = $area_code;
            $order_row['order_invoice'] = $invoice;
            $order_row['order_invoice_id'] = $order_invoice_id;
            $order_row['order_goods_amount'] = $val['shop_sumprice']; //订单商品总价（不包含运费）
            $order_row['order_payment_amount'] = $val['shop_pay_amount'] + $transport_cost[$key]['cost'];// 订单实际支付金额 = 商品实际支付金额 + 运费
            $order_row['order_discount_fee'] = $val['shop_sumprice'] - $val['shop_pay_amount'];   //优惠价格 = 商品总价 - 商品实际支付金额
            $order_row['order_user_discount'] = $val['shop_discount'];    //会员折扣优惠的金额
            $order_row['order_point_fee'] = 0;    //买家使用积分
            $order_row['order_shipping_fee'] = $transport_cost[$key]['cost'];
            $order_row['order_message'] = $shop_remark[$key];
            $order_row['order_status'] = $order_status;
            $order_row['order_points_add'] = 0;    //订单赠送的积分
            $order_row['voucher_id'] = $val['voucher_id'];    //代金券id
            $order_row['voucher_price'] = $val['voucher_price'];    //代金券面额
            $order_row['voucher_code'] = $val['voucher_code'];    //代金券编码
            $order_row['order_from'] = $order_from;    //订单来源
            //平台红包及其优惠信息
            $order_row['redpacket_code'] = isset($val['redpacket_code']) ? $val['redpacket_code'] : 0;        //红包编码
            $order_row['redpacket_price'] = isset($val['redpacket_price']) ? $val['redpacket_price'] : 0;    //红包面额
            $order_row['order_rpt_price'] = isset($val['order_rpt_price']) ? $val['order_rpt_price'] : 0;    //平台红包抵扣订单金额
            //如果卖家设置了默认地址，则将默认地址信息加入order_base表
            $Shop_ShippingAddressModel = new Shop_ShippingAddressModel();
            $address_list = $Shop_ShippingAddressModel -> getByWhere(array('shop_id' => $key, 'shipping_address_default' => 1));
            if ($address_list) {
                $address_list = current($address_list);
                $order_row['order_seller_address'] = $address_list['shipping_address_area'] . " " . $address_list['shipping_address_address'];
                $order_row['order_seller_contact'] = $address_list['shipping_address_phone'];
                $order_row['order_seller_name'] = $address_list['shipping_address_contact'];
                $order_row['order_seller_area_code'] = $address_list['area_code'];
            }
            $order_row['order_commission_fee'] = $val['commission'];
            $order_row['order_is_virtual'] = 0;    //1-虚拟订单 0-实物订单
            $order_row['order_shop_benefit'] = $order_shop_benefit;  //店铺优惠
            $order_row['payment_id'] = $pay_way_id;
            $order_row['payment_name'] = $PaymentChannlModel -> payWay[$pay_way_id];
            $order_row['chain_id'] = $chain_id;
            $order_row['directseller_discount'] = $val['directseller_discount'];//分销商折扣
            $order_row['directseller_flag'] = @$val['directseller_flag'];

            if(@$val['directseller_flag_0'])
            {
                $order_row['directseller_id'] = $user_parent_id;
            }
            if(@$val['directseller_flag_1'])
            {
                $order_row['directseller_p_id'] = $directseller_p_id;
            }
            if(@$val['directseller_flag_2'])
            {
                $order_row['directseller_gp_id'] = $directseller_gp_id;
            }

            $order_row['district_id'] = $val['district_id'];
            $flag1 = $this -> tradeOrderModel -> addBase($order_row);
            $flag = $flag && $flag1;
            //修改用户使用的代金券信息
            if ($val['voucher_id']) {
                if (isset($shop_voucher_row[$key])) {
                    $Voucher_BaseModel = new Voucher_BaseModel();
                    $flag6 = $Voucher_BaseModel -> changeVoucherState($val['voucher_id'], $order_id);
                    //代金券使用提醒
                    $message = new MessageModel();
                    $message -> sendMessage('The use of vouchers to remind', $user_id, $user_account, null, $shop_name = null, 0, MessageModel::USER_MESSAGE);
                    $flag = $flag && $flag6;
                }
            }
            foreach ($val['goods'] as $k => $v) {
                if (!isset($v['cart_id'])) {
                    continue;
                }
                //如果买家买的是分销商在供货商分销的支持代发货的商品，再生成分销商进货订单
                $Goods_CommonModel = new Goods_CommonModel();
                $common_base = $Goods_CommonModel -> getOne($v['common_id']);
                $dist_flag[] = true;
                if ($common_base['common_parent_id'] && $common_base['product_is_behalf_delivery'] == 1) {
                    $dist_flag[] = $this -> distributor_add_order($v['goods_base']['goods_id'], $v['goods_num'], $key, $receiver_name, $receiver_address, $receiver_phone, $address_id, $pay_way_id, $order_id, $invoice_id);
                    //获取SP订单号，添加到买家订单商品表
                    $parent_common = $Goods_CommonModel -> getOne($common_base['common_parent_id']);
                    $sp_order_base = $Order_BaseModel -> getOneByWhere(array('order_source_id' => $order_id, 'shop_id' => $parent_common['shop_id']));
//                    $supplierModel = new Supplier;
//                    $supplier_order_id = $supplierModel->addOrder($v['goods_base']['goods_id'], $v['goods_num'], $key, $receiver_name, $receiver_address, $receiver_phone, $address_id, $pay_way_id, $order_id, $invoice_id);
//                    $sp_order_base = ['order_id'=> $supplier_order_id]; //供应商单子
                }
                //计算商品的优惠
                $order_goods_benefit = '';
                if (isset($v['goods_base']['promotion_type'])) {
                    if ($v['goods_base']['promotion_type'] == 'groupbuy' && strtotime($v['goods_base']['groupbuy_starttime']) < time()) {
                        $order_goods_benefit = $order_goods_benefit . '团购';
                        if ($v['goods_base']['down_price']) {
                            $order_goods_benefit = $order_goods_benefit . ':直降' . format_money($v['goods_base']['down_price']) . ' ';
                        }
                    }
                    if ($v['goods_base']['promotion_type'] == 'xianshi' && strtotime($v['goods_base']['discount_start_time']) < time()) {
                        $order_goods_benefit = $order_goods_benefit . '限时折扣';
                        if ($v['goods_base']['down_price']) {
                            $order_goods_benefit = $order_goods_benefit . ':直降' . format_money($v['goods_base']['down_price']) . ' ';
                        }
                    }
                }
                //$plus_fee = 0;
                //if($v['isPlus'] && $userPlusFlag){
                //    $plus_fee = $v['diff_price'];
                //    $order_goods_benefit = $order_goods_benefit . ':PLUS会员优惠（折扣:'.$plus_rate.'）' . format_money($plus_fee) . ' ';
                // }
                $plus_price = 0;
                $v['isPlus'] && $plus_price = $v['plus_price'];
                $order_goods_row = array();
                $order_goods_row['order_id'] = $order_id;
                $order_goods_row['goods_id'] = $v['goods_base']['goods_id'];
                $order_goods_row['common_id'] = $v['goods_base']['common_id'];
                $order_goods_row['buyer_user_id'] = $user_id;
                $order_goods_row['goods_name'] = $v['goods_base']['goods_name'];
                $order_goods_row['goods_class_id'] = $v['goods_base']['cat_id'];
                $order_goods_row['order_spec_info'] = $v['goods_base']['spec'];
                $order_goods_row['goods_price'] = $v['now_price']; //商品原来的单价
                $order_goods_row['plus_price'] = $plus_price; //plus会员支付单价
                $order_goods_row['order_goods_payment_amount'] = $v['goods_pay_price'];  //商品实际支付单价
                $order_goods_row['order_goods_num'] = $v['goods_num'];
                $order_goods_row['goods_image'] = $v['goods_base']['goods_image'];
                $order_goods_row['order_goods_amount'] = $v['goods_pay_amount'];  //商品实际支付金额
                $order_goods_row['order_goods_discount_fee'] = $v['goods_sumprice'] - $v['goods_pay_amount'];        //优惠价格
                $order_goods_row['order_goods_adjust_fee'] = 0;    //手工调整金额
                $order_goods_row['order_goods_point_fee'] = 0;    //积分费用
                $order_goods_row['shop_id'] = $v['goods_base']['shop_id'];
                $order_goods_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_PAY;
                $order_goods_row['order_goods_evaluation_status'] = 0;  //0未评价 1已评价
                $order_goods_row['order_goods_benefit'] = $order_goods_benefit;
                $order_goods_row['order_goods_time'] = get_date_time();
                $order_goods_row['directseller_goods_discount'] = $v['directseller_goods_discount'];//分销商折扣

                $order_goods_row['order_goods_commission'] = $v['goods_commission_amount'];    //商品佣金(总)

                if ($common_base['common_parent_id'] && $common_base['product_is_behalf_delivery'] == 1) {
                    $order_goods_row['order_goods_source_id'] = $sp_order_base['order_id'];//供货商对应的订单
                }
                if (Web_ConfigModel::value('Plugin_Directseller')){
                    $order_goods_row['directseller_flag'] = $v['directseller_flag'];
                    if ($order_goods_row['directseller_flag']) {
                        //产品佣金
                        $order_goods_row['directseller_commission_0'] = $v['directseller_commission_0'];
                        $order_goods_row['directseller_commission_1'] = $v['directseller_commission_1'];
                        $order_goods_row['directseller_commission_2'] = $v['directseller_commission_2'];
                    }
                    $order_goods_row['directseller_id'] = $user_parent_id;
                }
                $flag2 = $Order_GoodsModel -> addGoods($order_goods_row, true);
                //加入交易快照表
                $order_goods_snapshot_add_row = array();
                $order_goods_snapshot_add_row['order_id'] = $order_id;
                $order_goods_snapshot_add_row['user_id'] = $user_id;
                $order_goods_snapshot_add_row['shop_id'] = $v['goods_base']['shop_id'];
                $order_goods_snapshot_add_row['common_id'] = $v['goods_base']['common_id'];
                $order_goods_snapshot_add_row['goods_id'] = $v['goods_base']['goods_id'];
                $order_goods_snapshot_add_row['goods_name'] = $v['goods_base']['goods_name'];
                $order_goods_snapshot_add_row['goods_image'] = $v['goods_base']['goods_image'];
                $order_goods_snapshot_add_row['goods_price'] = $v['goods_pay_price'];
                $order_goods_snapshot_add_row['freight'] = $transport_cost[$key]['cost'];   //运费
                $order_goods_snapshot_add_row['snapshot_create_time'] = get_date_time();
                $order_goods_snapshot_add_row['snapshot_uptime'] = get_date_time();
                $order_goods_snapshot_add_row['snapshot_detail'] = $order_goods_benefit;
                $Order_GoodsSnapshot -> addSnapshot($order_goods_snapshot_add_row);
                $flag = $flag && $flag2;
                //删除门店中的商品库存
                if ($chain_id) {
                    $Chain_GoodsModel = new Chain_GoodsModel();
                    $chain_row['chain_id:='] = $chain_id;
                    $chain_row['goods_id:='] = $v['goods_base']['goods_id'];
                    $chain_row['shop_id:='] = $v['goods_base']['shop_id'];
                    $chain_goods_id = $v['goods_base']['goods_id'];
                    $chain_goods = current($Chain_GoodsModel -> getByWhere($chain_row));
                    $chain_goods_id = $chain_goods['chain_goods_id'];
                    $goods_stock['goods_stock'] = $chain_goods['goods_stock'] - $v['goods_num'];
                    if ($goods_stock['goods_stock'] < 0) {
                        throw new Exception('门店库存不足');
                    }
                    $flag3 = $Chain_GoodsModel -> editGoods($chain_goods_id, $goods_stock);
                    //如果手机门店付款订单需要发送自提码
                    //如果是门店商品，需要发送自提码
                    if ($pay_way_id == PaymentChannlModel::PAY_CHAINPYA) {
                        $code = VerifyCode::getCode($receiver_phone);
                        $Chain_BaseModel = new Chain_BaseModel();
                        $chain_base = current($Chain_BaseModel -> getByWhere(array('chain_id' => $chain_id)));
                        $Order_GoodsChainCodeModel = new Order_GoodsChainCodeModel();
                        $code_data['order_id'] = $order_id;
                        $code_data['chain_id'] = $chain_id;
                        $code_data['order_goods_id'] = $flag2;
                        $code_data['chain_code_id'] = $code;
                        $Order_GoodsChainCodeModel -> addGoodsChainCode($code_data);
                        $message = new MessageModel();
                        $str = $message -> sendMessage('Self pick up code', $user_id, $user_account, $order_id = null, $shop_name = $val['shop_name'], 1, MessageModel::ORDER_MESSAGE, null, null, null, null, null, $goods_name = $v['goods_base']['goods_name'], null, null, $ztm = $code, $chain_name = $chain_base['chain_name'], $receiver_phone,$area_code);
                        //$str = Sms::send(13918675918,"尊敬的用户您已在[shop_name]成功购买[goods_name]，您可凭自提码[ztm]在[chain_name]自提。");
                    }
                } else {
                    //删除商品库存
                    $flag3 = $Goods_BaseModel -> delStock($v['goods_id'], $v['goods_num']);
                }
                $trade_title = $v['goods_base']['goods_name'];
                $flag = $flag && $flag3;
                //从购物车中删除商品
                if (isset($v['cart_id'])) {
                    $flag4 = $CartModel -> removeCart($v['cart_id']);
                } else {
                    $flag4 = true;
                }
                $flag = $flag && $flag4;
            }
            //加价购商品
            if (isset($val['increase_goods'])) {
                foreach ($val['increase_goods'] as $k => $v) {
                    //判断加价购的商品库存
                    $order_goods_row = array();
                    $order_goods_row['order_id'] = $order_id;
                    $order_goods_row['goods_id'] = $v['goods_id'];
                    $order_goods_row['common_id'] = $v['common_id'];
                    $order_goods_row['buyer_user_id'] = $user_id;
                    $order_goods_row['goods_name'] = $v['goods_name'];
                    $order_goods_row['goods_class_id'] = $v['cat_id'];
                    //$order_goods_row['order_spec_info']               = $v['goods_base']['spec'];
                    $order_goods_row['goods_price'] = $v['redemp_price']; //商品原来的单价
                    $order_goods_row['order_goods_payment_amount'] = $v['goods_pay_price'];  //商品实际支付单价
                    $order_goods_row['order_goods_num'] = $v['goods_num'];
                    $order_goods_row['goods_image'] = $v['goods_image'];
                    $order_goods_row['order_goods_amount'] = $v['goods_pay_amount'];  //商品实际支付金额
                    $order_goods_row['order_goods_discount_fee'] = $v['goods_sumprice'] - $v['goods_pay_amount'];        //优惠价格
                    $order_goods_row['order_goods_adjust_fee'] = 0;    //手工调整金额
                    $order_goods_row['order_goods_point_fee'] = 0;    //积分费用
                    $order_goods_row['order_goods_commission'] = $v['goods_commission_amount'];    //商品佣金(总)
                    $order_goods_row['shop_id'] = $key;
                    $order_goods_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_PAY;
                    $order_goods_row['order_goods_evaluation_status'] = 0;  //0未评价 1已评价
                    $order_goods_row['order_goods_benefit'] = '加价购商品';
                    $order_goods_row['order_goods_time'] = get_date_time();
                    if (Web_ConfigModel::value('Plugin_Directseller')) {
                        $order_goods_row['directseller_commission_0'] = $v['directseller_commission_0'];
                        $order_goods_row['directseller_commission_1'] = $v['directseller_commission_1'];
                        $order_goods_row['directseller_commission_2'] = $v['directseller_commission_2'];
                        $order_goods_row['directseller_flag'] = $v['directseller_flag'];
                        $order_goods_row['directseller_id'] = $user_parent_id;
                    }
                    $flag2 = $Order_GoodsModel -> addGoods($order_goods_row);
                    //加入交易快照表(加价购商品)
                    $order_goods_snapshot_add_row = array();
                    $order_goods_snapshot_add_row['order_id'] = $order_id;
                    $order_goods_snapshot_add_row['user_id'] = $user_id;
                    $order_goods_snapshot_add_row['shop_id'] = $v['shop_id'];
                    $order_goods_snapshot_add_row['common_id'] = $v['common_id'];
                    $order_goods_snapshot_add_row['goods_id'] = $v['goods_id'];
                    $order_goods_snapshot_add_row['goods_name'] = $v['goods_name'];
                    $order_goods_snapshot_add_row['goods_image'] = $v['goods_image'];
                    $order_goods_snapshot_add_row['goods_price'] = $v['redemp_price'];
                    $order_goods_snapshot_add_row['freight'] = $transport_cost[$key]['cost'];   //运费
                    $order_goods_snapshot_add_row['snapshot_create_time'] = get_date_time();
                    $order_goods_snapshot_add_row['snapshot_uptime'] = get_date_time();
                    $order_goods_snapshot_add_row['snapshot_detail'] = '加价购商品';
                    $Order_GoodsSnapshot -> addSnapshot($order_goods_snapshot_add_row);
                    $flag = $flag && $flag2;
                    //删除商品库存
                    $flag3 = $Goods_BaseModel -> delStock($v['goods_id'], 1);
                    $flag = $flag && $flag3;
                }
            }
            //店铺满赠商品
            if ($val['mansong_info'] && $val['mansong_info']['gift_goods_id']) {
                $order_goods_row = array();
                $order_goods_row['order_id'] = $order_id;
                $order_goods_row['goods_id'] = $val['mansong_info']['gift_goods_id'];
                $order_goods_row['common_id'] = $val['mansong_info']['common_id'];
                $order_goods_row['buyer_user_id'] = $user_id;
                $order_goods_row['goods_name'] = $val['mansong_info']['goods_name'];
                $order_goods_row['goods_class_id'] = 0;
                $order_goods_row['goods_price'] = 0;
                $order_goods_row['order_goods_num'] = 1;
                $order_goods_row['goods_image'] = $val['mansong_info']['goods_image'];
                $order_goods_row['order_goods_amount'] = 0;
                $order_goods_row['order_goods_discount_fee'] = 0;        //优惠价格
                $order_goods_row['order_goods_adjust_fee'] = 0;    //手工调整金额
                $order_goods_row['order_goods_point_fee'] = 0;    //积分费用
                $order_goods_row['order_goods_commission'] = 0;    //商品佣金
                $order_goods_row['shop_id'] = $key;
                $order_goods_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_PAY;
                $order_goods_row['order_goods_evaluation_status'] = 0;  //0未评价 1已评价
                $order_goods_row['order_goods_benefit'] = '店铺满赠商品';
                $order_goods_row['order_goods_time'] = get_date_time();
                $flag2 = $Order_GoodsModel -> addGoods($order_goods_row);
                //加入交易快照表(满赠商品)
                $order_goods_snapshot_add_row = array();
                $order_goods_snapshot_add_row['order_id'] = $order_id;
                $order_goods_snapshot_add_row['user_id'] = $user_id;
                $order_goods_snapshot_add_row['shop_id'] = $key;
                $order_goods_snapshot_add_row['common_id'] = $val['mansong_info']['common_id'];
                $order_goods_snapshot_add_row['goods_id'] = $val['mansong_info']['gift_goods_id'];
                $order_goods_snapshot_add_row['goods_name'] = $val['mansong_info']['goods_name'];
                $order_goods_snapshot_add_row['goods_image'] = $val['mansong_info']['goods_image'];
                $order_goods_snapshot_add_row['goods_price'] = 0;
                $order_goods_snapshot_add_row['freight'] = $transport_cost[$key]['cost'];   //运费
                $order_goods_snapshot_add_row['snapshot_create_time'] = get_date_time();
                $order_goods_snapshot_add_row['snapshot_uptime'] = get_date_time();
                $order_goods_snapshot_add_row['snapshot_detail'] = '满赠商品';
                $Order_GoodsSnapshot -> addSnapshot($order_goods_snapshot_add_row);
                $flag = $flag && $flag2;
                //删除商品库存
                $flag3 = $Goods_BaseModel -> delStock($val['mansong_info']['gift_goods_id'], 1);
                $flag = $flag && $flag3;
            }

            //支付中心生成订单
            $key = Yf_Registry::get('shop_api_key');
            $url = Yf_Registry::get('paycenter_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');
            $formvars = array();
            $formvars['app_id'] = $shop_app_id;
            $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
            $formvars['consume_trade_id'] = $order_row['order_id'];
            $formvars['order_id'] = $order_row['order_id'];
            $formvars['buy_id'] = $user_id;
            $formvars['buyer_name'] = $user_account;
            $formvars['seller_id'] = $order_row['seller_user_id'];
            $formvars['seller_name'] = $order_row['seller_user_name'];
            $formvars['order_state_id'] = $order_row['order_status'];
            $formvars['order_payment_amount'] = $order_row['order_payment_amount'];
            $formvars['order_commission_fee'] = $order_row['order_commission_fee'];
            $formvars['trade_remark'] = $order_row['order_message'];
            $formvars['trade_create_time'] = $order_row['order_create_time'];
            $formvars['trade_title'] = $trade_title;        //商品名称 - 标题
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addConsumeTrade&typ=json', $url), $formvars);
            //将合并支付单号插入数据库
            if ($rs['status'] == 200) {
                $Order_BaseModel -> editBase($order_id, array('payment_number' => $rs['data']['union_order']));
                $flag = $flag && true;
            } else {
                $flag = $flag && false;
            }
            $uprice += $order_row['order_payment_amount'];
            $inorder .= $order_id . ',';
            $utrade_title .= $trade_title;
        }
        //修改用户使用的红包信息
        if ($rpacket_id) {
            $redPacket_BaseModel = new RedPacket_BaseModel();
            $field_row = array();
            $field_row['redpacket_state'] = RedPacket_BaseModel::USED;
            $field_row['redpacket_order_id'] = $inorder;
            $flag5 = $redPacket_BaseModel -> editRedPacket($rpacket_id, $field_row);
            $flag = $flag && $flag5;
        }
        //生成合并支付订单
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();
        $formvars['inorder'] = $inorder;
        $formvars['uprice'] = $uprice;
        $formvars['buyer'] = $user_id;
        $formvars['trade_title'] = $utrade_title;
        $formvars['buyer_name'] = $user_account;
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addUnionOrder&typ=json', $url), $formvars);
        if ($rs['status'] == 200) {
            $uorder = $rs['data']['uorder'];
            $flag = $flag && true;
        } else {
            $uorder = '';
            $flag = $flag && false;
        }
        if (is_ok($dist_flag) && $flag && $this -> tradeOrderModel -> sql -> commitDb()) {
            /**
             * 统计中心
             * 添加订单统计
             */
//                $analytics_data = array(
//                    'order_id' => $inorder,
//                    'union_order_id' => $uorder,
//                    'user_id' => Perm::$userId,
//                    'ip' => get_ip(),
//                    'addr' => $receiver_address,
//                    'type' => 1
//                );
//                Yf_Plugin_Manager::getInstance() -> trigger('analyticsOrderAdd', $analytics_data);
            //下单成功推送信息给商家IM
            $Shop_BaseModel = new Shop_BaseModel();
            $shop_user_info = $Shop_BaseModel -> getByWhere(['shop_id:IN' => $shop_id]);
            $user_id_row = array_column($shop_user_info, 'user_id');
            $user_name_row = array_column($shop_user_info, 'user_name');
            //向im发送消息
            $im_url = Yf_Registry::get('im_api_url') . '?' . 'ctl=ImApi&met=pushMsg';
            $im_typ = 'json';
            $im_method = 'GET';
            $im_alert = "您的会员在" . date("Y-m-d H:i:s") . "提交了订单" . $inorder . '请在用户确认付款后尽快发货。';
            $im_receiver = implode(',', $user_name_row);
            $im_param = [];
            $im_param['receiver'] = $im_receiver;
            $im_param['account_system'] = 'admin';
            $im_code = '下单通知';
            $im_param['msg_content'] = $im_alert . '&*' . '#1' . '&*' . $im_code;
            $im_param['push_type'] = 1;
            $im_param['msg_type'] = 1;
            $im_result = get_url($im_url, $im_param, $im_typ, $im_method);
            $status = 200;
            $msg = __('success');
            $data = array('uorder' => $uorder);
        } else {
            $this -> tradeOrderModel -> sql -> rollBackDb();
            $m = $this -> tradeOrderModel -> msg -> getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
            //订单提交失败，将paycenter中生成的订单删除
            if ($uorder) {
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');
                $formvars = array();
                $formvars['uorder'] = $uorder;
                $formvars['app_id'] = $shop_app_id;
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=delUnionOrder&typ=json', $url), $formvars);
            }
            $data = array();
        }
        $this -> data -> addBody(-140, $data, $msg, $status);
    }

    /**
     * 判断提交订单的加价购商品信息是否正确
     *
     * @param $increase_arr array 所有的加价购商品信息，包括店铺id，商品id，规则id，商品数量，限购数量，加价购商品单价
     * @param $cart_id      array 购物车id
     *                      return $increase_shop_price
     *                      hp 2017-07-21
     */
    private function checkIncreaseGoods($increase_arr, $cart_id)
    {
        $CartModel = new CartModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $Increase_BaseModel = new Increase_BaseModel();
        $Increase_RuleModel = new Increase_RuleModel();
        $Increase_RedempGoodsModel = new Increase_RedempGoodsModel();
        $cart_info = $CartModel -> getByWhere(['cart_id:IN' => $cart_id]);
        $res = $increase_shop_price = [];
        foreach ($cart_info as $ckey => $cval) {
            $res[$cval['shop_id']][] = $cval;
        }
        //店铺id，商品id，商品数量都是一一对应的
        //判断传值店铺id对应传值加价购商品
        foreach ($increase_arr as $key => $val) {
            $shop_total_price = 0;//对应店铺购物车商品金额
            foreach ($res as $rkey => $rval) {
                //如果购物车有加价购商品的店铺，则判断是否满足加价购条件
                if ($val['increase_shop_id'] == $rkey) {
                    //循环购物车商品
                    foreach ($rval as $rk => $rv) {
                        //找到当前店铺正常状态的加价购信息
                        $increase_shop_base = $Increase_BaseModel -> getByWhere(['shop_id' => $rv['shop_id'], 'increase_state' => Increase_BaseModel::NORMAL]);
                        //该店铺正常状态的加价购id
                        $increase_ids = array_keys($increase_shop_base);
                        //找出当前店铺加价购商品对应的规则id，一个商品可以属于多个规则
                        $increase_redgoods_info = $Increase_RedempGoodsModel -> getByWhere(['shop_id' => $rv['shop_id'], 'goods_id' => $val['increase_goods_id'], 'increase_id:IN' => $increase_ids]);
                        //如果只有一条规则，去找出对应规则，判断当前店铺购物金额是否满足规则金额
                        if (count($increase_redgoods_info) == 1) {
                            $increase_redgoods_info = current($increase_redgoods_info);
                            $increase_rule_info = $Increase_RuleModel -> getOneByWhere(['rule_id' => $increase_redgoods_info['rule_id']]);
                        } //如果该加价购商品属于多个规则，则找出最低金额的规则，判断当前店铺购物车商品是否大于等于这个规则金额
                        elseif (count($increase_redgoods_info) > 1) {
                            $rule_ids = array_column($increase_redgoods_info, 'rule_id');
                            $increase_rules = $Increase_RuleModel -> getByWhere(['rule_id:IN' => $rule_ids]);
                            $increase_rules_price = array_column($increase_rules, 'rule_price');
                            $min_rule_key = array_search(min($increase_rules_price), $increase_rules_price);
                            $increase_rule_info = $increase_rules[$min_rule_key];
                        }
                        $goods_info = $Goods_BaseModel -> getOneByWhere(['goods_id' => $rv['goods_id']]);
                        $shop_total_price += ($goods_info['goods_price'] * $rv['goods_num']);
                    }
                    //判断当前购物车店铺商品是否满足加价购条件,
                    if ((($shop_total_price * 100 - $increase_rule_info['rule_price'] * 100) > 0) || (($shop_total_price * 100 - $increase_rule_info['rule_price'] * 100) == 0)) {
                        //一个店铺可以对应多个加价购商品，判断当前商品是否在返回的数组中
                        $increase_goods_info = $Increase_RedempGoodsModel -> getByWhere(['shop_id' => $val['increase_shop_id']], ['redemp_goods_id' => 'desc']);
                        $increase_goods_ids = array_column($increase_goods_info, 'goods_id','id');
                        $increase_id = array_search($val['increase_goods_id'], $increase_goods_ids);
                        if ($increase_id) {
                            //如果存在就判断购买数量是否符合当前店铺加价购规则
                            $increase_red_goods = $Increase_RedempGoodsModel -> getOneByWhere(['redemp_goods_id' => $increase_id, 'goods_id' => $increase_goods_ids[$increase_id]]);
                            $increase_goods_rule = $Increase_RuleModel -> getOne($increase_red_goods['rule_id']);
                            if ($increase_goods_rule['rule_goods_limit'] == 0) {
                                $increase_goods_base = $Goods_BaseModel -> getOne($increase_red_goods['goods_id']);
                                $increase_goods_rule['rule_goods_limit'] = $increase_goods_base['goods_stock'];
                            }
                            if (($val['increase_goods_num'] <= $increase_goods_rule['rule_goods_limit']) && ($val['increase_goods_num'] >= 1)) {
                                //商品数必须大于等于1小于等于限购数并且数据类型为整型，否则返回false；
                                //判断该店铺加价购商品总金额是否正确
                                if ((intval($val['increase_goods_num'] * $val['increase_price'] * 100) - intval(($val['increase_goods_num'] * $increase_red_goods['redemp_price']) * 100)) == 0) {
                                    $increase_shop_price[$key]['goods_id'] = $val['increase_goods_id'];
                                    $increase_shop_price[$key]['redemp_price'] = $increase_red_goods['redemp_price'];
                                    $increase_shop_price[$key]['goods_sumprice'] = $increase_red_goods['redemp_price'] * $val['increase_goods_num'];
                                    $increase_shop_price[$key]['goods_num'] = $val['increase_goods_num'];
                                } else {
                                    $increase_shop_price = 1;  //加价购商品金额有误
                                    break;
                                }
                            } else {
                                $increase_shop_price = 2; //加价购商品数量有误
                                break;
                            }
                        } else {
                            $increase_shop_price = 3;  //加价购活动id有误
                            break;
                        }
                    } else {
                        $increase_shop_price = 4;  //当前购物车商品不满足加价购活动要求
                        break;
                    }
                } else {
                    continue;
                }
            }
            if ($shop_total_price == 0) {
                $increase_shop_price = 5;  //店铺总金额为0
                break;
            }
        }
        if ($increase_shop_price) {
            return $increase_shop_price;
        } else {
            return 6;
        }
    }

    /**
     * 重组确认订单页中店铺与商品信息，形成一个新数组
     *
     * @param $data
     * @param $user_rate
     * @param $is_discount
     * @param $increase_shop_row
     * @param $shop_voucher_row
     *
     * @return array
     */
    private function reformShopOrderGoods($data, $user_rate, $is_discount, $increase_shop_row, $shop_voucher_row)
    {
        unset($data['count']);
        unset($data['cart_count']);
        //plus 开关
        $userPlusFlag = 0;//默认非plus会员
        if($this->userPlus && $this->userPlus['user_status'] !=3 && $this->userPlus['end_date']>time() && $this->plus_switch){
            $userPlusFlag = 1;//plus会员(包括试用和正式)
        }
        $shop_order_goods_row = array();
        $order_price = 0;
        foreach ($data as $ckey => $cval) {
            $shop_order_goods_row[$ckey]['shop_id'] = $cval['shop_id'];
            $shop_order_goods_row[$ckey]['shop_name'] = $cval['shop_name'];
            $shop_order_goods_row[$ckey]['shop_user_id'] = $cval['shop_user_id'];
            $shop_order_goods_row[$ckey]['shop_user_name'] = $cval['shop_user_name'];
            $shop_order_goods_row[$ckey]['shop_self_support'] = $cval['shop_self_support'];  //是否是自营店铺 false非自营  true自营
            $shop_order_goods_row[$ckey]['directseller_discount'] = $cval['distributor_rate'] ? $cval['distributor_rate'] : 0;//分销商折扣
            $shop_order_goods_row[$ckey]['directseller_flag'] = $cval['directseller_flag'] ? $cval['directseller_flag'] : 0;//分销商折扣
            $shop_order_goods_row[$ckey]['directseller_flag_0'] = $cval['directseller_flag_0'] ? $cval['directseller_flag_0'] : 0;//分销商折扣
            $shop_order_goods_row[$ckey]['directseller_flag_1'] = $cval['directseller_flag_1'] ? $cval['directseller_flag_1'] : 0;//分销商折扣
            $shop_order_goods_row[$ckey]['directseller_flag_2'] = $cval['directseller_flag_2'] ? $cval['directseller_flag_2'] : 0;//分销商折扣
            $shop_order_goods_row[$ckey]['shop_sumprice'] = 0;
            $shop_order_goods_row[$ckey]['district_id'] = $cval['district_id'];
            foreach ($cval['goods'] as $cgkey => $cgval) {
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['cart_id'] = $cgval['cart_id'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['goods_id'] = $cgval['goods_id'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['common_id'] = $cgval['goods_base']['common_id'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['goods_name'] = $cgval['goods_base']['goods_name'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['cat_commission'] = $cgval['cat_commission'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['now_price'] = $cgval['now_price'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['goods_num'] = $cgval['goods_num'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['directseller_goods_discount'] = $cgval['rate_price'] ? $cgval['rate_price'] : 0;//分销商折扣
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['goods_base'] = $cgval['goods_base'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['isPlus'] = $cgval['isPlus'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['plus_price'] = $cgval['plus_price'];
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['diff_price'] = $cgval['now_price']-$cgval['plus_price'];
                //判断plus，处理价格
                if ($cgval['isPlus'] && $userPlusFlag){
                    $shop_order_goods_row[$ckey]['goods'][$cgkey]['goods_sumprice'] = $cgval['plus_price'] * $cgval['goods_num'] * 1;  //单种商品总价
                }else{
                    $shop_order_goods_row[$ckey]['goods'][$cgkey]['goods_sumprice'] = $cgval['now_price'] * $cgval['goods_num'] * 1;  //单种商品总价
                }
                $shop_order_goods_row[$ckey]['goods'][$cgkey]['goods_pay_amount'] = $shop_order_goods_row[$ckey]['goods'][$cgkey]['goods_sumprice'];
                if ($cgval['isPlus'] && $userPlusFlag){
                    $shop_order_goods_row[$ckey]['shop_sumprice'] += $cgval['plus_price'] * $cgval['goods_num'] * 1;
                }else{
                    $shop_order_goods_row[$ckey]['shop_sumprice'] += $cgval['now_price'] * $cgval['goods_num'] * 1;
                }
                //开启分销
                if (Web_ConfigModel::value('Plugin_Directseller')) {
                    $shop_order_goods_row[$ckey]['goods'][$cgkey]['directseller_commission_0'] = $cgval['directseller_commission_0'];
                    $shop_order_goods_row[$ckey]['goods'][$cgkey]['directseller_commission_1'] = $cgval['directseller_commission_1'];
                    $shop_order_goods_row[$ckey]['goods'][$cgkey]['directseller_commission_2'] = $cgval['directseller_commission_2'];
                    $shop_order_goods_row[$ckey]['goods'][$cgkey]['directseller_flag'] = $cgval['directseller_flag'];
                }
            }
            //计算加价购商品的价格
            if (isset($increase_shop_row[$ckey])) {
                $increase_price = $increase_shop_row[$ckey]['price'];
                foreach ($increase_shop_row[$ckey]['goods'] as $insgkey => $insgval) {
                    array_push($shop_order_goods_row[$ckey]['goods'], $insgval);
                }
                if ($increase_shop_row[$ckey]['directseller_flag'] && isset($increase_shop_row[$ckey])) {
                    $increase_directseller_commission = $increase_shop_row[$ckey]['directseller_commission'];
                } else {
                    $increase_directseller_commission = 0;
                }
                $order_directseller_commission = $cgval['directseller_commission'] + $increase_directseller_commission;
            } else {
                $increase_price = 0;
                $order_directseller_commission = 0;
            }
            $shop_order_goods_row[$ckey]['shop_sumprice'] += $increase_price;
            //计算该店铺订单中一共有几种商品
            $shop_order_goods_row[$ckey]['goods_common_num'] = count($shop_order_goods_row[$ckey]['goods']);
            //计算店铺的满减
            $shop_order_goods_row[$ckey]['mansong_info'] = $cval['mansong_info'];
            if ($cval['mansong_info']) {
                if ($cval['mansong_info']['rule_discount'] && $cval['mansong_info']['rule_discount']) {
                    $shop_order_goods_row[$ckey]['shop_mansong_discount'] = $cval['mansong_info']['rule_discount'];
                } else {
                    $shop_order_goods_row[$ckey]['shop_mansong_discount'] = 0;
                }
            } else {
                $shop_order_goods_row[$ckey]['shop_mansong_discount'] = 0;
            }
            //计算店铺代金券
            if (isset($shop_voucher_row[$ckey])) {
                $voucher_price = $shop_voucher_row[$ckey]['voucher_price'];
                $voucher_id = $shop_voucher_row[$ckey]['voucher_id'];
                $voucher_code = $shop_voucher_row[$ckey]['voucher_code'];
            } else {
                $voucher_price = 0;
                $voucher_id = 0;
                $voucher_code = 0;
            }
            $shop_order_goods_row[$ckey]['voucher_price'] = $voucher_price;
            $shop_order_goods_row[$ckey]['voucher_id'] = $voucher_id;
            $shop_order_goods_row[$ckey]['voucher_code'] = $voucher_code;
            //计算店铺折扣（此店铺订单实际需要支付的价格）
            if ($user_rate > 100 || $user_rate < 0) {
                //如果折扣配置有误，按没有折扣计算
                $user_rate = 100;
            }
            //判断平台是否开启会员折扣只限自营店铺使用
            //如果是平台自营店铺需要计算会员折扣，非平台自营不需要计算折扣
            if (Web_ConfigModel::value('rate_service_status') && $cval['shop_self_support'] == 'false') {
                $shop_order_goods_row[$ckey]['user_rate'] = 100;
            } else {
                $shop_order_goods_row[$ckey]['user_rate'] = $user_rate;
            }
            //店铺实际支付的价格根据用户选择的会员折扣开启关闭和平台设置的会员折扣使用规则决定
            if ($is_discount) {
                //每家店铺实际支付金额
                $shop_order_goods_row[$ckey]['shop_pay_amount'] = round(((($shop_order_goods_row[$ckey]['shop_sumprice'] - $shop_order_goods_row[$ckey]['shop_mansong_discount']) * $shop_order_goods_row[$ckey]['user_rate']) / 100), 2);
                //每家店铺最后优惠金额
                $shop_order_goods_row[$ckey]['shop_user_rate'] = round(((($shop_order_goods_row[$ckey]['shop_sumprice'] - $shop_order_goods_row[$ckey]['shop_mansong_discount']) * (100 - $shop_order_goods_row[$ckey]['user_rate'])) / 100), 2);
                //店铺享受的会员折扣
                $shop_order_goods_row[$ckey]['shop_discount'] = $shop_order_goods_row[$ckey]['shop_user_rate'];
            } else {
                //每家店铺实际支付金额
                $shop_order_goods_row[$ckey]['shop_pay_amount'] = round(($shop_order_goods_row[$ckey]['shop_sumprice'] - $shop_order_goods_row[$ckey]['shop_mansong_discount'] - $shop_order_goods_row[$ckey]['voucher_price']), 2);
                //每家店铺最后优惠金额
                $shop_order_goods_row[$ckey]['shop_user_rate'] = round(($shop_order_goods_row[$ckey]['shop_mansong_discount'] + $shop_order_goods_row[$ckey]['voucher_price']), 2);
                //店铺享受的会员折扣
                $shop_order_goods_row[$ckey]['shop_discount'] = 0;
            }
            //订单总支付金额
            $order_price += $shop_order_goods_row[$ckey]['shop_pay_amount'];
        }
        $shop_order_goods_row['order_price'] = $order_price;

        return $shop_order_goods_row;
    }
    /**
     * 生成分销商进货订单
     * //该方法生成的是分销商在供货商出进货的订单，分销商为买家，供货商为卖家
     */
    public function distributor_add_order($goods_id, $num, $distributor_id, $rec_name, $rec_address, $rec_phone, $addr_id, $pay_way_id, $p_order_id, $invoice, $invoice_title, $invoice_content, $invoice_id)
    {
        $Goods_CommonModel = new Goods_CommonModel();
        $Shop_BaseModel = new Shop_BaseModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $receiver_name = $rec_name;                //收货人
        $receiver_address = $rec_address;               //收货地址
        $receiver_phone = $rec_phone;              // 收货人电话
        $goods_num = $num;                //商品数量
        $address_id = $addr_id;                    //买家收货地址id
        //判断支付方式为在线支付还是货到付款,如果是货到付款则订单状态直接为待发货状态，如果是在线支付则订单状态为待付款
        if ($pay_way_id == PaymentChannlModel::PAY_ONLINE) {
            $order_status = Order_StateModel::ORDER_WAIT_PAY;
        }
        if ($pay_way_id == PaymentChannlModel::PAY_CONFIRM) {
            $order_status = Order_StateModel::ORDER_WAIT_PREPARE_GOODS;
        }
        //分销商（买家数据）
        $distributor_shop_info = $Shop_BaseModel -> getOne($distributor_id);//分销商店铺
        $goodsbaseinfo = $Goods_BaseModel -> getGoodsDetailInfoByGoodId($goods_id);//商品详情$data['goods_base']，$data['common_base']，$data['shop_base']，$data['mansong_info']
        fb($distributor_shop_info);
        $user_id = $distributor_shop_info['user_id']; //分销商店铺用户user_id
        $user_account = $distributor_shop_info['user_name'];  //分销商店铺用户user_name
        //供货商（卖家）数据
        $supplier_goodsbaseinfo = $Goods_BaseModel -> getGoodsDetailInfoByGoodId($goodsbaseinfo['goods_base']['goods_parent_id']);
        $supplier_goodsbase = $Goods_BaseModel->getGoodsInfo($goodsbaseinfo['goods_base']['goods_parent_id']);

        $supplier_shop_info = $Shop_BaseModel -> getOne($supplier_goodsbaseinfo['goods_base']['shop_id']);
        $shop_id = $supplier_shop_info['shop_id'];  //供货商店铺id
        //获取供货商给该分销商设置的折扣
        $shopDistributorModel = new Distribution_ShopDistributorModel();
        $shopDistributorLevelModel = new Distribution_ShopDistributorLevelModel();
        $shopDistributorInfo = $shopDistributorModel -> getOneByWhere(array('shop_id' => $supplier_shop_info['shop_id'], 'distributor_id' => $distributor_shop_info['shop_id'], 'distributor_enable' => 1));
        $distritutor_rate_info = $shopDistributorLevelModel -> getOne($shopDistributorInfo['distributor_level_id']);
        //查找收货地址,计算运费
        $User_AddressModel = new User_AddressModel();
        $Transport_TemplateModel = new Transport_TemplateModel();
        $city_id = 0;
        if ($address_id) {
            $user_address = $User_AddressModel -> getOne($address_id);
            $city_id = $user_address['user_address_city_id'];
        }
        $orderInfo = array(
            'shop_id' => $supplier_shop_info['shop_id'],
            'count' => $goods_num,
            'weight' => $supplier_goodsbaseinfo['common_base']['common_cubage'] * $goods_num,
            'price' => $supplier_goodsbaseinfo['goods_base']['goods_price']
        );
        $costInfo = $Transport_TemplateModel -> shopTransportCost($city_id, $orderInfo);
        $cost = $costInfo['cost'] ? $costInfo['cost'] : 0;
        //商品价格：供应商的进货价-分销商等级优惠+供应商设置的物流费用
        if ($distritutor_rate_info['distributor_leve_discount_rate'] > 0 && $distritutor_rate_info['distributor_leve_discount_rate'] < 100) {
            $shop_rate = number_format(($supplier_goodsbaseinfo['goods_base']['goods_price'] * (100 - $distritutor_rate_info['distributor_leve_discount_rate']) * $goods_num / 100), 2, '.', '');
        } else {
            $shop_rate = 0;
        }
        $goods_price = $supplier_goodsbaseinfo['goods_base']['goods_price'] * $goods_num - $shop_rate;
        $total_price = $goods_price + $cost;
        //计算商品单件实际支付金额（order_goods_payment_amount）
        $order_goods_payment_amount = number_format(($goods_price / $goods_num), 2, '.', '');
        //获取分类佣金
        $Goods_CatModel = new Goods_CatModel();
        $cat_base = $Goods_CatModel -> getOne($supplier_goodsbaseinfo['common_base']['cat_id']);
        if ($cat_base) {
            $cat_commission = $cat_base['cat_commission'];
        } else {
            $cat_commission = 0;
        }
        //后台开启供应商佣金则需要收取供应商的商品佣金
        if (Web_ConfigModel::value('supplier_commission')) {
            $commission_fee = number_format(($goods_price * $cat_commission / 100), 2, '.', '');
        } else {
            $commission_fee = 0;
        }
        $Number_SeqModel = new Number_SeqModel();
        $Order_BaseModel = new Order_BaseModel();
        $Order_GoodsModel = new Order_GoodsModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $PaymentChannlModel = new PaymentChannlModel();
        $Order_GoodsSnapshot = new Order_GoodsSnapshot();
        //合并支付订单的价格
        $uprice = 0;
        $inorder = '';
        $utrade_title = '';    //商品名称 - 标题
        $trade_title = '';
        //生成店铺订单
        $prefix = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));
        $order_number = $Number_SeqModel -> createSeq($prefix);
        $order_id = sprintf('%s-%s-%s-%s', 'SP', $supplier_shop_info['user_id'], $shop_id, $order_number);
        $Order_InvoiceModel = new Order_InvoiceModel();
        $order_invoice_id = $Order_InvoiceModel -> getOrderInvoiceId($invoice_id, $invoice_title, $invoice_content, $order_id, true);
        $order_row = array();
        $order_row['order_id'] = $order_id;
        $order_row['shop_id'] = $shop_id;
        $order_row['shop_name'] = $supplier_shop_info['shop_name'];
        $order_row['buyer_user_id'] = $user_id;
        $order_row['buyer_user_name'] = $user_account;
        $order_row['seller_user_id'] = $supplier_shop_info['user_id'];
        $order_row['seller_user_name'] = $supplier_shop_info['user_name'];
        $order_row['order_date'] = date('Y-m-d');
        $order_row['order_create_time'] = get_date_time();
        $order_row['order_receiver_name'] = $receiver_name;
        $order_row['order_receiver_address'] = $receiver_address;
        $order_row['order_receiver_contact'] = $receiver_phone;
        $order_row['order_goods_amount'] = $goods_price; //订单商品总价（不包含运费）
        $order_row['order_payment_amount'] = $total_price;// 订单实际支付金额 = 商品实际支付金额 + 运费
        $order_row['order_discount_fee'] = 0;   //优惠价格 = 商品总价 - 商品实际支付金额
        $order_row['order_point_fee'] = 0;    //买家使用积分
        $order_row['order_shipping_fee'] = $cost;
        $order_row['order_status'] = $order_status;
        $order_row['order_points_add'] = 0;    //订单赠送的积分
        $order_row['order_commission_fee'] = $commission_fee;  //分类佣金
        $order_row['order_source_id'] = $p_order_id;    // 进货订单对应的买家订单
        $order_row['order_is_virtual'] = 0;    //1-虚拟订单 0-实物订单
        $order_row['payment_id'] = $pay_way_id;
        $order_row['payment_name'] = $PaymentChannlModel -> payWay[$pay_way_id];
        $order_row['directseller_discount'] = $shop_rate;
        $order_row['order_invoice'] = $invoice;
        $order_row['order_invoice_id'] = $order_invoice_id;
        $order_row['order_distribution_seller_type'] = 2;//分销代销转发销售(P, SP)
        $flag = $this -> tradeOrderModel -> addBase($order_row);
        $order_goods_row = array();
        $order_goods_row['order_id'] = $order_id;
        $order_goods_row['goods_id'] = $supplier_goodsbaseinfo['goods_base']['goods_id'];
        $order_goods_row['common_id'] = $supplier_goodsbaseinfo['goods_base']['common_id'];
        $order_goods_row['buyer_user_id'] = $user_id;
        $order_goods_row['goods_name'] = $supplier_goodsbaseinfo['goods_base']['goods_name'];
        $order_goods_row['goods_class_id'] = $supplier_goodsbaseinfo['goods_base']['cat_id'];
        $order_goods_row['order_spec_info'] = $supplier_goodsbase['goods_base']['spec'];
        $order_goods_row['goods_price'] = $supplier_goodsbaseinfo['goods_base']['goods_price']; //商品原来的单价
        $order_goods_row['order_goods_payment_amount'] = $order_goods_payment_amount;  //商品实际支付单价
        $order_goods_row['order_goods_num'] = $goods_num;
        $order_goods_row['goods_image'] = $supplier_goodsbaseinfo['goods_base']['goods_image'];
        $order_goods_row['order_goods_amount'] = $goods_price;  //商品实际支付金额
        $order_goods_row['order_goods_discount_fee'] = 0;        //优惠价格
        $order_goods_row['order_goods_adjust_fee'] = 0;    //手工调整金额
        $order_goods_row['order_goods_point_fee'] = 0;    //积分费用
        $order_goods_row['order_goods_commission'] = $commission_fee;    //商品佣金(总)
        $order_goods_row['shop_id'] = $supplier_goodsbaseinfo['goods_base']['shop_id'];
        $order_goods_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_PAY;
        $order_goods_row['order_goods_evaluation_status'] = 0;  //0未评价 1已评价
        $order_goods_row['order_goods_benefit'] = 0;
        $order_goods_row['order_goods_time'] = get_date_time();
        $order_goods_row['directseller_goods_discount'] = $shop_rate;
        fb($order_goods_row);
        $flag1 = $Order_GoodsModel -> addGoods($order_goods_row);
        //加入交易快照表
        $order_goods_snapshot_add_row = array();
        $order_goods_snapshot_add_row['order_id'] = $order_id;
        $order_goods_snapshot_add_row['user_id'] = $user_id;
        $order_goods_snapshot_add_row['shop_id'] = $supplier_goodsbaseinfo['goods_base']['shop_id'];
        $order_goods_snapshot_add_row['common_id'] = $supplier_goodsbaseinfo['goods_base']['common_id'];
        $order_goods_snapshot_add_row['goods_id'] = $supplier_goodsbaseinfo['goods_base']['goods_id'];
        $order_goods_snapshot_add_row['goods_name'] = $supplier_goodsbaseinfo['goods_base']['goods_name'];
        $order_goods_snapshot_add_row['goods_image'] = $supplier_goodsbaseinfo['goods_base']['goods_image'];
        $order_goods_snapshot_add_row['goods_price'] = $supplier_goodsbaseinfo['goods_base']['goods_price'];
        $order_goods_snapshot_add_row['freight'] = $cost;   //运费
        $order_goods_snapshot_add_row['snapshot_create_time'] = get_date_time();
        $order_goods_snapshot_add_row['snapshot_uptime'] = get_date_time();
        $order_goods_snapshot_add_row['snapshot_detail'] = 0;
        $Order_GoodsSnapshot -> addSnapshot($order_goods_snapshot_add_row);
        /*fb("====order_goods====");
            fb($flag2);*/
        $flag = $flag && $flag1;
        //删除商品库存
        $flag2 = $Goods_BaseModel -> delStock($supplier_goodsbaseinfo['goods_base']['goods_id'], $goods_num);
        $trade_title = $supplier_goodsbaseinfo['goods_base']['goods_name'];
        //支付中心生成订单
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        $formvars['consume_trade_id'] = $order_row['order_id'];
        $formvars['order_id'] = $order_row['order_id'];
        $formvars['buy_id'] = $user_id;
        $formvars['buyer_name'] = $user_account;
        $formvars['seller_id'] = $order_row['seller_user_id'];
        $formvars['seller_name'] = $order_row['seller_user_name'];
        $formvars['order_state_id'] = $order_row['order_status'];
        $formvars['order_payment_amount'] = $order_row['order_payment_amount'];
        $formvars['order_commission_fee'] = $commission_fee;
        $formvars['trade_remark'] = '采购单';
        $formvars['trade_create_time'] = $order_row['order_create_time'];
        $formvars['trade_title'] = $trade_title;        //商品名称 - 标题
        fb($formvars);
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addConsumeTrade&typ=json', $url), $formvars);
        fb("合并支付返回的结果");
        //将合并支付单号插入数据库
        if ($rs['status'] == 200) {
            $Order_BaseModel -> editBase($order_id, array('payment_number' => $rs['data']['union_order']));
            $flag = $flag && true;
        } else {
            $flag = $flag && false;
        }
        $uprice = $order_row['order_payment_amount'];
        $inorder = $order_id;
        $utrade_title = $trade_title;
        //生成合并支付订单
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();
        $formvars['inorder'] = $inorder;
        $formvars['uprice'] = $uprice;
        $formvars['buyer'] = $user_id;
        $formvars['trade_title'] = $utrade_title;
        $formvars['buyer_name'] = $user_account;
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        fb($formvars);
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addUnionOrder&typ=json', $url), $formvars);
        fb($rs);
        if ($rs['status'] == 200) {
            $uorder = $rs['data']['uorder'];
            $flag = $flag && true;
        } else {
            $uorder = '';
            $flag = $flag && false;
        }
        if ($flag) {
            $status = 200;
            $msg = __('success');
            $data = array('uorder' => $uorder);
        } else {
            //订单提交失败，将paycenter中生成的订单删除
            if ($uorder) {
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');
                $formvars = array();
                $formvars['uorder'] = $uorder;
                $formvars['app_id'] = $shop_app_id;
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=delUnionOrder&typ=json', $url), $formvars);
            }
        }
        return $flag;
    }
    //计算每个商品订单实际支付的金额，以及每件商品的实际支付单价为多少
    private function computeShopPrice($shop_order_goods_row)
    {
        foreach ($shop_order_goods_row as $sogkey => $sogval) {
            $add_pay_amount = 0;
            $add_commission_amount = 0;
            $sum_diff_price = 0;
            foreach ($sogval['goods'] as $soggkey => $soggval) {
                //此种方式计算商品价格，只能保证每样商品实际支付金额相加等于最后支付的金额。但其中每件商品实际支付单价会有偏差。在计算退款金额的时候需要注意
                if ($soggkey < ($sogval['goods_common_num'] - 1)) {
                    //计算每样商品的单价
                    $goods_common_price = round(((($soggval['goods_sumprice'] / $sogval['shop_sumprice']) * $sogval['shop_pay_amount']) / $soggval['goods_num']), 2);
                    $shop_order_goods_row[$sogkey]['goods'][$soggkey]['goods_pay_price'] = $goods_common_price;
                    //计算每样商品实际支付的金额
                    $goods_common_pay_amount = $goods_common_price * $soggval['goods_num'];

                    $shop_order_goods_row[$sogkey]['goods'][$soggkey]['goods_pay_amount'] = $goods_common_pay_amount;
                    //计算每样商品的佣金
                    $shop_order_goods_row[$sogkey]['goods'][$soggkey]['goods_commission_amount'] = round((($goods_common_pay_amount * $soggval['cat_commission']) / 100), 2);
                    //计算店铺订单的总佣金
                    $add_commission_amount += round((($goods_common_pay_amount * $soggval['cat_commission']) / 100), 2);
                    //累计每样商品的实际支付金额
                    $add_pay_amount += $goods_common_pay_amount;
                } else {
                    //计算每样商品实际支付的金额
                    $goods_common_pay_amount = $sogval['shop_pay_amount'] - $add_pay_amount;
                    $shop_order_goods_row[$sogkey]['goods'][$soggkey]['goods_pay_amount'] = $goods_common_pay_amount;
                    //计算每样商品的单价
                    $goods_common_price = round(($goods_common_pay_amount / $soggval['goods_num']), 2);
                    $shop_order_goods_row[$sogkey]['goods'][$soggkey]['goods_pay_price'] = $goods_common_price;
                    //计算每样商品的佣金
                    $shop_order_goods_row[$sogkey]['goods'][$soggkey]['goods_commission_amount'] = round((($goods_common_pay_amount * $soggval['cat_commission']) / 100), 2);
                    //计算店铺订单的总佣金
                    $add_commission_amount += round((($goods_common_pay_amount * $soggval['cat_commission']) / 100), 2);
                }
                if($soggval['isPlus']){
                    $sum_diff_price+=$soggval['diff_price'];
                }

            }
            $shop_order_goods_row[$sogkey]['plus_diff_price'] = $sum_diff_price;
            $shop_order_goods_row[$sogkey]['commission'] = $add_commission_amount;
        }

        return $shop_order_goods_row;
    }
    //查找收货地址
    private function getTransportCost($address_id, $cart_id)
    {
        $transport_cost = array();
        if ($address_id) {
            $User_AddressModel = new User_AddressModel();
            $city_id = 0;
            if ($address_id) {
                $user_address = $User_AddressModel -> getOne($address_id);
                $city_id = $user_address['user_address_city_id'];
            }
            $Transport_TemplateModel = new Transport_TemplateModel();
            $transport_cost = $Transport_TemplateModel -> cartTransportCost($city_id, $cart_id);
        }
        return $transport_cost;
    }
    //查找全部订单
    public function physical()
    {
        $userId  = request_int('user_id');
        $act = request_string('act');
        $order_id = request_string('order_id');
        //订单详情页
        if ($act == 'details') {
            $data = $this -> tradeOrderModel -> getOrderDetail($order_id);
            $buyer_user_id = $data['buyer_user_id'];
            if ($userId!= $buyer_user_id) {
                $host = Yf_Registry::get('shop_api_url');
                $path =  '/index.php?ctl=Buyer_Index&met=index';
                $url = $host.$path;
                header("Location:".$url); exit;
            }
            if ($data['order_is_bargain'] != 1) {
                $data['new_benefit'] = substr($data['order_shop_benefit'], strpos($data['order_shop_benefit'], ':') + 1); //新的折扣值
            } else {
                $data['new_benefit'] = $data['order_shop_benefit'];
            }
            if (trim($data['order_invoice']) == "不开发票") {
                $data['order_invoice'] = "无";
            }
            //团购是否开启
            $data['goods_list'] = $this -> isGroupBuy($data['goods_list']);
            //获取订单中的商品信息
            $goods_ids = array_column($data['goods_list'],'goods_id');
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_info_list = $Goods_BaseModel->getByWhere(array('goods_id:IN'=>$goods_ids));
            $is_del = array_column($goods_info_list,'is_del');
            $del_flag =false;
            if(in_array(Goods_BaseModel::IS_DEL_YES, $is_del)){
                $del_flag = true;
            }
            $this -> view -> setMet('details');
        } else {
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = 10;
            $rows = $Yf_Page->listRows;
            $page = request_int('page') ? request_int('page') : 1;
            $status = request_string('status');
            $recycle = request_int('recycle');
            $search_str = request_string('orderkey');
            $order_row['buyer_user_id'] = $userId;
            $order_row['order_buyer_hidden:<'] = Order_BaseModel::IS_BUYER_REMOVE;
            $order_row['order_is_virtual'] = Order_BaseModel::ORDER_IS_REAL; //实物订单
            $order_row['chain_id:='] = 0; //不是门店自提订单

            $shopBaseModel = new Shop_BaseModel();
            $shop_info = $shopBaseModel->getOneByWhere(['user_id' => $userId]);
            $supplier_data = $this->supplier($shop_info['id']);
            $order_row['shop_id:IN'] = $supplier_data;
            //待付款
            if ($status == 'wait_pay') {
                $order_row['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
            }
            //待发货 -> 只可退款
            if ($status == 'wait_perpare_goods') {
                $order_row['order_status'] = Order_StateModel::ORDER_WAIT_PREPARE_GOODS;
            }
            //已付款
            if ($status == 'order_payed') {
                $order_row['order_status:>='] = Order_StateModel::ORDER_PAYED;
                $order_row['order_status:<='] = Order_StateModel::ORDER_WAIT_PREPARE_GOODS;
            }
            //待收货、已发货 -> 退款退货
            if ($status == 'wait_confirm_goods') {
                $order_row['order_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
            }
            //已完成 -> 订单评价
            if ($status == 'finish') {
                $order_row['order_status'] = Order_StateModel::ORDER_FINISH;
                $order_row['order_buyer_evaluation_status'] = 0; //买家未评价
            }
            //已取消
            if ($status == 'cancel') {
                $order_row['order_status'] = Order_StateModel::ORDER_CANCEL;
            }
            //订单回收站
            if ($recycle) {
                $order_row['order_buyer_hidden'] = Order_BaseModel::IS_BUYER_HIDDEN;
            } else {
                $order_row['order_buyer_hidden:!='] = Order_BaseModel::IS_BUYER_HIDDEN;
            }
            if (request_string('start_date')) {
                $order_row['order_create_time:>'] = request_string('start_date');
            }
            if (request_string('end_date')) {
                $order_row['order_create_time:<'] = request_string('end_date');
            }

            if ($search_str) {
                //搜索：订单号、订单中商品名称 or
                $order_ids = $this->tradeOrderModel->searchNumOrGoodsName($search_str, $order_row);
                //unset($order_row);
                $order_row['order_id:IN'] = $order_ids;
            }
            if (isset($order_row['order_id:IN']) && !$order_row['order_id:IN']) {
                $data = array('totalsize' => 0, 'page' => $page, 'records' => 0, 'total' => 0, 'items' => array());
            } else {
                $data = $this->tradeOrderModel->getBaseList($order_row, array('order_create_time' => 'DESC'), $page, $rows);
            }
            $pinTuanTemp_model = new PinTuan_Temp();
            //获取交易时效
            $Web_ConfigModel = new Web_ConfigModel();
            $data['complain_datetime'] = $Web_ConfigModel->getConfigValue('complain_datetime');
            foreach ($data['items'] as $k => $v) {
                $pintuan_temp = $pinTuanTemp_model->getPtInfoByOrderId($v['id']);
                $data['items'][$k]['pintuan_person_num'] = $pintuan_temp['base']['person_num'];
                $data['items'][$k]['pintuan_type'] = $pintuan_temp['temp']['type'];
                //团购是否开启
                $v['goods_list'] = $this->isGroupBuy($v['goods_list']);
                //商品是否删除
                $goods_ids = array_column($v['goods_list'], 'goods_id');
            }
            $Yf_Page -> totalRows = $data['totalsize'];
            $page_nav = $Yf_Page -> prompt();
        }
            $pinTuanTemp_model = new PinTuan_Temp();
            foreach ($data['items'] as $key => &$val) {
                $evala_status = 0;
                //判断当前订单评价状态
                if (!$val['order_buyer_evaluation_status']) {
                    //订单评价状态，1表示待评价，2表示已评价待追加评价，3表示查看评价
                    $evala_status = 1;
                }
                if ($val['order_buyer_evaluation_status'] == 1) {
                    if (count($val['goods_list']) == 1 && $val['goods_list'][0]['evaluation_count'] == 1) {
                        $evala_status = 2;
                    } elseif (count($val['goods_list']) == 1 && $val['goods_list'][0]['evaluation_count'] == 2) {
                        $evala_status = 3;
                    } elseif (count($val['goods_list']) != 1) {
                        if (in_array(1, array_column($val['goods_list'], 'evaluation_count'))) {
                            $evala_status = 2;
                        } else {
                            $evala_status = 3;
                        }
                    }
                }
                $pintuan_temp = $pinTuanTemp_model -> getPtInfoByOrderId($val['id']);
                //如果该笔订单是拼团商品需要修改订单商品的old_price
                if ($pintuan_temp) {
                    $Goods_BaseModel = new Goods_BaseModel();
                    $data['items'][$key]['goods_list'] = $Goods_BaseModel -> editGoodsOldPrice($val['goods_list']);
                }
                $data['items'][$key]['evala_status'] = $evala_status;

                //退货
                foreach ($val['goods_list'] as $ogkey => &$ogval){
                    //满赠商品没有退货操作
                    $ogval['return_order_state'] = 0;
                   if($ogval['goods_price'] > 0){
                       //货到付款 -- 货到付款的商品没有退款操作只有退货操作
                        if ($val['payment_id'] == PaymentChannlModel::PAY_CONFIRM) {
                            //货到付款的订单只有当订单确认收货完成订单后才会出现“退款/退货”按钮
                            if (($val['order_status'] == Order_StateModel::ORDER_RECEIVED || $val['order_status'] == Order_StateModel::ORDER_FINISH) && $val['order_refund_status'] == Order_StateModel::ORDER_REFUND_NO) {
                                //白条支付的订单需要线下进行退款/退货操作
                                if (strstr($val['payment_name'], '白条支付')) {

                                    if($val['order_is_bargain'] != 1){
                                        //白条支付请联系商家线下退款/退货
                                        $ogval['return_order_state']=1;
                                    }
                                }else {
                                    if ($ogval['goods_refund_status'] == Order_StateModel::ORDER_GOODS_RETURN_NO && $ogval['order_goods_num'] > $ogval['order_goods_returnnum']) {
                                        if ($val['order_is_bargain'] != 1) {
                                            if($ogval['rgl_flag']){
                                                //退款退货
//                                                '?ctl=Buyer_Service_Return&met=index&act=add&oid='.$val['order_id'].'&gid='.$ogval['order_goods_id'];
//                                                '退款/退货';
                                                $data['items'][$key]['goods_list'][$ogkey]['return_order_state']=2;
                                            }
                                        }
                                    }else{
//                                        '?ctl=Buyer_Service_Return&met=index&act=detail&id='.$ogval['order_refund_id'];
//                                        $ogval['goods_refund_status_con'];
                                        $ogval['return_order_state']=3;
                                    }
                                }

                            }
                        }else{
                            //在线支付 -- 已付款（可退款），订单完成（可退货）
                            //已经付款（但是没有退款的商品），已经完成（但是没有退货的商品）出现“退款/退货”按钮
                            //由于之前数据的影响，之前订单存在退款的商品的“退款/退货”按钮也不显示
                            if ((($val['order_status'] == Order_StateModel::ORDER_PAYED && $ogval['goods_return_status'] == Order_StateModel::ORDER_GOODS_RETURN_NO) || ($val['order_status'] == Order_StateModel::ORDER_FINISH && $ogval['goods_refund_status'] == Order_StateModel::ORDER_GOODS_RETURN_NO)) && !$val['order_source_id'] && $val['order_refund_status'] == Order_StateModel::ORDER_REFUND_NO && $ogval['order_goods_num'] > $ogval['order_goods_returnnum'] && $ogval['goods_price'] > 0) {
                                if ($val['pintuan_temp_order'] != 1) {
                                    if (strstr($val['payment_name'], '白条支付')) {
                                        if ($val['order_is_bargain'] != 1) {
                                            //白条支付请联系商家线下退款/退货
                                            $ogval['return_order_state']=1;
                                        }
                                    }else {
                                        //订单状态为已付款，并且订单商品没有退款 则显示订单商品的退款按钮
                                        if ($val['order_is_bargain'] != 1) {
                                            if($ogval['rgl_flag']){
                                                //退款退货
//                                                '?ctl=Buyer_Service_Return&met=index&act=add&oid='.$val['order_id'].'&gid='.$ogval['order_goods_id'];
//                                                '退款/退货';
                                                $ogval['return_order_state']=2;
                                            }

                                        }
                                    }
                                }
                            }
                            if ($ogval['goods_return_status'] != Order_StateModel::ORDER_REFUND_NO) {
                               // '?ctl=Buyer_Service_Return&met=index&act=detail&id='.$ogval['order_return_id'];
                                //$ogval['goods_return_status_con'];
                                $ogval['return_order_state']=3;
                            }
                            if ($ogval['goods_return_status'] == Order_StateModel::ORDER_REFUND_IN) {
                             //   '?ctl=Buyer_Service_Return&met=cancelRefund&typ=json';
//                                $ogval['order_return_id'];
//                                '取消退款';
                                $ogval['return_order_state']=4;
                            }
                        }
                    }
                }
            }

            $this -> data -> addBody(-140, $data);

    }
    //是否开启了团购活动
    public function isGroupBuy($goodsLists)
    {
        //根据common_id 获取商品团购信息
        foreach ($goodsLists as $key => $value) {
            $common_id = $value['common_id'];
            $group_cond_row['groupbuy_state'] = GroupBuy_BaseModel::NORMAL;
            $group_cond_row['common_id'] = $common_id;
            $group_cond_row['groupbuy_starttime:>='] = date('Y-m-d H:i:s', time());
            $group_cond_row['groupbuy_endtime:<='] = date('Y-m-d H:i:s', time());
            $group_base_model = new GroupBuy_BaseModel();
            $flag = current($group_base_model -> getByWhere($group_cond_row));
            //是否开启了团购
            if (is_array($flag)) {
                $goodsLists[$key]['is_group'] = true;
            } else {
                $goodsLists[$key]['is_group'] = false;
            }
        }
        return $goodsLists;
    }
    /**
     * 虚拟兑换订单
     *
     * @author
     */
    public function virtual()
    {
        $userId  = request_int('user_id');
            $Yf_Page = new Yf_Page();
            $Yf_Page -> listRows = 10;
            $rows = $Yf_Page -> listRows;
             $page  = request_int('page')?request_int('page'):1;
            $status = request_string('status');
            $recycle = request_int('recycle');
            $user_id = $userId;
            $order_row['buyer_user_id'] = $user_id;
            $order_row['order_buyer_hidden:<'] = Order_BaseModel::IS_BUYER_HIDDEN;
            $order_row['order_is_virtual'] = Order_BaseModel::ORDER_IS_VIRTUAL; //虚拟订单
            $search_str = request_string('keyword');
            //待付款
            if ($status == 'wait_pay') {
                $order_row['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
            }
            //待发货 -> 只可退款
            if ($status == 'wait_perpare_goods') {
                $order_row['order_status'] = Order_StateModel::ORDER_WAIT_PREPARE_GOODS;
            }
            //待收货、已发货 -> 只可退款
            if ($status == 'wait_confirm_goods') {
                $order_row['order_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
            }
            //已完成 -> 订单评价
            if ($status == 'finish') {
                $order_row['order_status'] = Order_StateModel::ORDER_FINISH;
            }
            //已取消
            if ($status == 'cancel') {
                $order_row['order_status'] = Order_StateModel::ORDER_CANCEL;
            }
//                //订单回收站
//                if ($recycle) {
//                    $order_row['order_buyer_hidden'] = Order_BaseModel::IS_BUYER_CANCEL;
//                } else {
//                    $order_row['order_buyer_hidden:!='] = Order_BaseModel::IS_BUYER_HIDDEN;
//                }
            //订单回收站
            if ($recycle) {
                unset($order_row['order_buyer_hidden:<']);
                $order_row['order_buyer_hidden'] = Order_BaseModel::IS_BUYER_HIDDEN;
            } else {
                unset($order_row['order_buyer_hidden:<']);
                $order_row['order_buyer_hidden:!='] = Order_BaseModel::IS_BUYER_HIDDEN;
            }
            if (request_string('start_date')) {
                $order_row['order_create_time:>'] = request_string('start_date');
            }
            if (request_string('end_date')) {
                $order_row['order_create_time:<'] = request_string('end_date');
            }

            if ($search_str) {
                //搜索：订单号、订单中商品名称 or
                $order_ids = $this -> tradeOrderModel -> searchNumOrGoodsName($search_str, $order_row);
                //unset($order_row);
                $order_row['order_id:IN'] = $order_ids;
            }
            if (isset($order_row['order_id:IN']) && !$order_row['order_id:IN']) {
                $data = array('totalsize' => 0, 'page' => $page, 'records' => 0, 'total' => 0, 'items' => array());
            } else {
                $data = $this -> tradeOrderModel -> getBaseList($order_row, array('order_create_time' => 'DESC'), $page, $rows);
            }
            $Yf_Page -> totalRows = $data['totalsize'];
            $page_nav = $Yf_Page -> prompt();
            $this -> data -> addBody(-140, $data);

    }
    /**
     * 门店自提订单
     *
     * @access public
     */
    public function chain()
    {

            $Yf_Page = new Yf_Page();
            $Yf_Page -> listRows = 10;
            $rows = $Yf_Page -> listRows;
            $page  = request_int('page')?request_int('page'):1;
            $userId  = request_int('user_id');
            $order_row['buyer_user_id'] = $userId;
            $order_row['order_buyer_hidden:<'] = Order_BaseModel::IS_BUYER_HIDDEN;
            $order_row['chain_id:!='] = 0; //门店自提订单
            $status = request_string('status');
            $recycle = request_int('recycle');
            $search_str = request_string('orderkey');
            //待付款
            if ($status == 'wait_pay') {
                $order_row['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
            }
            //待自提
            if ($status == 'order_chain') {
                $order_row['order_status'] = Order_StateModel::ORDER_SELF_PICKUP;
            }
            //已完成 -> 订单评价
            if ($status == 'finish') {
                $order_row['order_status'] = Order_StateModel::ORDER_FINISH;
            }
            //已取消
            if ($status == 'cancel') {
                $order_row['order_status'] = Order_StateModel::ORDER_CANCEL;
            }
            //已完成并评价
            if ($status == 'chain_finish') {
                $order_row['order_status'] = Order_StateModel::ORDER_FINISH;
                $order_row['order_buyer_evaluation_status'] = 1; //买家已评价
            }
            //待评价
            if ($status == 'received') {
                $order_row['order_status'] = Order_StateModel::ORDER_FINISH;
                $order_row['order_buyer_evaluation_status'] = 0; //买家未评价
            }
            //订单回收站
            if ($recycle) {
                unset($order_row['order_buyer_hidden:<']);
                $order_row['order_buyer_hidden'] = Order_BaseModel::IS_BUYER_HIDDEN;
            } else {
                unset($order_row['order_buyer_hidden:<']);
                $order_row['order_buyer_hidden:!='] = Order_BaseModel::IS_BUYER_HIDDEN;
            }
            if (request_string('start_date')) {
                $order_row['order_create_time:>'] = request_string('start_date');
            }
            if (request_string('end_date')) {
                $order_row['order_create_time:<'] = request_string('end_date');
            }
            if ($search_str) {
                //搜索：订单号、订单中商品名称 or
                $order_ids = $this -> tradeOrderModel -> searchNumOrGoodsName($search_str, $order_row);
                //unset($order_row);
                $order_row['order_id:IN'] = $order_ids;
            }
            if (isset($order_row['order_id:IN']) && !$order_row['order_id:IN']) {
                $data = array('totalsize' => 0, 'page' => $page, 'records' => 0, 'total' => 0, 'items' => array());
            } else {
                $data = $this -> tradeOrderModel -> getBaseList($order_row, array('order_create_time' => 'DESC'), $page, $rows);
            }
            $Yf_Page -> totalRows = $data['totalsize'];
            $page_nav = $Yf_Page -> prompt();
            $this -> data -> addBody(-140, $data);
    }
    //供应商
    private function supplier($shop_id)
    {
        $cond_row  = array();
        $order_row = array();

        //审核通过
        $cond_row['distributor_enable'] =1;
        $cond_row['distributor_id'] = $shop_id;
        $data = $this->shopDistributorModel->listByWhere($cond_row, $order_row);
        $re_data=[];
        if(isset($data['items'])&&!empty($data['items'])){
            $re_data = array_column($data['items'],'shop_id');
        }else{
            $re_data=[];
        }
        return $re_data;
    }
    /**
     * 删除订单
     *
     * @author
     */
    public function hideOrder()
    {
        $order_id = request_string('order_id');
        $user = request_string('user');
        $user_id = request_string('user_id');
        $op = request_string('op');
        $edit_row = array();
        $Order_BaseModel = new Order_BaseModel();
        //查找订单信息
        $order_base = $Order_BaseModel -> getOne($order_id);
        //买家删除订单
        if ($user == 'buyer') {
            //判断订单状态是否是已完成（6）或者已取消（7）状态
            if ($order_base['order_status'] >= Order_StateModel::ORDER_FINISH) {
                //判断当前用户是否是下单者
                if ($order_base['buyer_user_id'] == $user_id) {
                    if ($op == 'del') {
                        $edit_row['order_buyer_hidden'] = Order_BaseModel::IS_BUYER_REMOVE;
                    } else {
                        $edit_row['order_buyer_hidden'] = Order_BaseModel::IS_BUYER_HIDDEN;
                    }
                } else {
                    //判断当前用户是否是下单者的主管账户
                    $User_SubUserModel = new User_SubUserModel();
                    $cond_row['user_id'] = $user_id;
                    $cond_row['sub_user_id'] = $order_base['buyer_user_id'];
                    $cond_row['sub_user_active'] = User_SubUserModel::IS_ACTIVE;
                    $sub_user = $User_SubUserModel -> getByWhere($cond_row);
                    if ($sub_user) {
                        if ($op == 'del') {
                            $edit_row['order_subuser_hidden'] = Order_BaseModel::IS_SUBUSER_REMOVE;
                        } else {
                            $edit_row['order_subuser_hidden'] = Order_BaseModel::IS_SUBUSER_HIDDEN;
                        }
                    }
                }
            }
        }
        $flag = $Order_BaseModel -> editBase($order_id, $edit_row);
        if ($flag) {
            $status = 200;
            $msg = __('success');
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $this -> data -> addBody(-140, array(), $msg, $status);
    }
    /**
     * 检查订单中的商品状态
     */
    public function checkOrder(){
        $order_id = request_string('order_id');
        if(empty($order_id)){
            $this -> data -> addBody(-140, array(), "订单错误", 250);
        }
        $Order_GoodsModel = new Order_GoodsModel();
        $order_goods_info = $Order_GoodsModel->getByWhere(array('order_id'=>$order_id));
        $goods_ids = array_column($order_goods_info,'goods_id');
        $Goods_BaseModel = new Goods_BaseModel();
        $goods_info = $Goods_BaseModel->getByWhere(array('goods_id:IN'=> $goods_ids));
        if(empty($goods_info)){
            $this -> data -> addBody(-140, array(), "订单中商品已被商户下架或者删除，请重新选择商品并下单！", 250);
        }
        foreach ($goods_info as $k => $value){
            if($value['is_del'] == Goods_BaseModel::IS_DEL_YES){
                $this -> data -> addBody(-140, array(), "订单中的商品:{$value['goods_name']}已被商户下架或者删除，请重新选择商品并下单", 250);
            }
        }
        $this->data->addBody(-140,array(),'',200);
    }
    /**
     * 取消订单
     *
     * @access public
     */
    public function orderCancel()
    {
        $typ = request_string('typ');
        $rs_row = array();
        $data = array();
        $user_id = request_string('user_id');
        if ($typ == 'e') {
            $cancel_row['cancel_identity'] = Order_CancelReasonModel::CANCEL_BUYER;
            //获取取消原因
            $Order_CancelReasonModel = new Order_CancelReasonModel;
            $reason = array_values($Order_CancelReasonModel -> getByWhere($cancel_row));
            include $this -> view -> getView();
        } else {
            $Order_BaseModel = new Order_BaseModel();
            //开启事物
            $Order_BaseModel -> sql -> startTransactionDb();
            $order_id = request_string('order_id');
            $state_info = request_string('state_info');
            //获取订单详情，判断订单的当前状态与下单这是否为当前用户
            $order_base = $Order_BaseModel -> getOne($order_id);
            $data['order_id'] = $order_id;
            //加入货到付款订单取消功能
            if (($order_base['payment_id'] == PaymentChannlModel::PAY_CONFIRM
                    && $order_base['order_status'] == Order_StateModel::ORDER_WAIT_PREPARE_GOODS) //货到付款+等待发货
                || $order_base['order_status'] == Order_StateModel::ORDER_WAIT_PAY
                && $order_base['buyer_user_id'] == $user_id) {
                if (empty($state_info)) {
                    $state_info = request_string('state_info1');
                }
                //加入取消时间
                $condition['order_status'] = Order_StateModel::ORDER_CANCEL;
                $condition['order_cancel_reason'] = addslashes($state_info);
                $condition['order_cancel_identity'] = Order_BaseModel::IS_BUYER_CANCEL;
                $condition['order_cancel_date'] = get_date_time();
                $edit_flag = $Order_BaseModel -> editBase($order_id, $condition);
                check_rs($edit_flag, $rs_row);
                //修改订单商品表中的订单状态
                $edit_row['order_goods_status'] = Order_StateModel::ORDER_CANCEL;
                $Order_GoodsModel = new Order_GoodsModel();
                $order_goods_id = $Order_GoodsModel -> getKeyByWhere(array('order_id' => $order_id));
                $edit_flag1 = $Order_GoodsModel -> editGoods($order_goods_id, $edit_row);
                check_rs($edit_flag1, $rs_row);
                //退还订单商品的库存
                $Goods_BaseModel = new Goods_BaseModel();
                $Chain_GoodsModel = new Chain_GoodsModel();
                if ($order_base['chain_id'] != 0) {
                    $order_goods = $Order_GoodsModel -> getOneByWhere(array('order_id' => $order_id));
                    $chain_row['chain_id:='] = $order_base['chain_id'];
                    $chain_row['goods_id:='] = $order_goods['goods_id'];
                    $chain_row['shop_id:='] = $order_base['shop_id'];
                    $chain_goods = current($Chain_GoodsModel -> getByWhere($chain_row));
                    $chain_goods_id = $chain_goods['chain_goods_id'];
                    $goods_stock['goods_stock'] = $chain_goods['goods_stock'] + 1;
                    $edit_flag2 = $Chain_GoodsModel -> editGoods($chain_goods_id, $goods_stock);
                    check_rs($edit_flag2, $rs_row);
                } else {
                    $edit_flag2 = $Goods_BaseModel -> returnGoodsStock($order_goods_id);
                    check_rs($edit_flag2, $rs_row);
                }
                //将需要取消的订单号远程发送给Paycenter修改订单状态
                //远程修改paycenter中的订单状态
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');
                $formvars = array();
                $formvars['order_id'] = $order_id;
                $formvars['app_id'] = $shop_app_id;
                $formvars['payment_id'] = $order_base['payment_id'];
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=cancelOrder&typ=json', $url), $formvars);

                if ($rs['status'] == 200) {
                    $edit_flag3 = true;
                    check_rs($edit_flag3, $rs_row);
                } else {
                    $edit_flag3 = false;
                    check_rs($edit_flag3, $rs_row);
                }
                //如果有分销商进货单，同时取消进货单
                $dist_orders = $Order_BaseModel -> getByWhere(array('order_source_id' => $order_id));
                if (!empty($dist_orders)) {
                    foreach ($dist_orders as $value) {
                        //改变订单状态
                        $Order_BaseModel -> editBase($value['order_id'], $condition);
                        $dist_order_base = current($Order_BaseModel -> getByWhere(array('order_id' => $value['order_id'])));
                        //修改订单商品表中的订单状态
                        $order_goods_id = $Order_GoodsModel -> getKeyByWhere(array('order_id' => $value['order_id']));
                        $Order_GoodsModel -> editGoods($order_goods_id, $edit_row);
                        if ($dist_order_base['chain_id'] != 0) {
                            $chain_row['chain_id:='] = $dist_order_base['chain_id'];
                            $chain_row['goods_id:='] = is_array($order_goods_id) ? $order_goods_id[0] : $order_goods_id;
                            $chain_row['shop_id:='] = $dist_order_base['shop_id'];
                            $chain_goods = current($Chain_GoodsModel -> getByWhere($chain_row));
                            $dist_order_goods = $Order_BaseModel -> getOneByWhere(['order_id' => $value['order_id'], 'goods_id' => $order_goods_id]);
                            $chain_goods_id = $chain_goods['chain_goods_id'];
                            $goods_stock['goods_stock'] = $chain_goods['goods_stock'] + $dist_order_goods['order_goods_num'];
                            $edit_goods_flag = $Chain_GoodsModel -> editGoods($chain_goods_id, $goods_stock);
                            check_rs($edit_goods_flag, $rs_row);
                        } else {
                            $edit_goods_flag = $Goods_BaseModel -> returnGoodsStock($order_goods_id);
                            check_rs($edit_goods_flag, $rs_row);
                        }
                        $formvars['order_id'] = $value['order_id'];
                        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=cancelOrder&typ=json', $url), $formvars);
                        if ($rs['status'] == 200) {
                            $edit_flag4 = true;
                            check_rs($edit_flag4, $rs_row);
                        } else {
                            $edit_flag4 = false;
                            check_rs($edit_flag4, $rs_row);
                        }
                    }
                }
//				//判断订单是否使用平台红包，如果使用，将平台红包状态改为未使用
                $RedPacket_BaseModel = new RedPacket_BaseModel();
                $red_data = $RedPacket_BaseModel -> getOneByWhere(['redpacket_order_id' => $order_base['order_id']]);
                if ($red_data) {
                    $red_flag = $RedPacket_BaseModel -> editRedPacket($red_data['redpacket_id'], ['redpacket_state' => RedPacket_BaseModel::UNUSED]);
                }
            }
            $flag = is_ok($rs_row);
            if ($flag && $Order_BaseModel -> sql -> commitDb()) {
                /**
                 *  加入统计中心
                 */
                $analytics_data = array();
                if ($order_id) {
                    $analytics_data['order_id'] = array($order_id);
                    $analytics_data['status'] = Order_StateModel::ORDER_CANCEL;
                    Yf_Plugin_Manager::getInstance() -> trigger('analyticsUpdateOrderStatus', $analytics_data);
                }
                /******************************************************************/
                $status = 200;
                $msg = __('success');
            } else {
                $Order_BaseModel -> sql -> rollBackDb();
                $m = $Order_BaseModel -> msg -> getMessages();
                $msg = $m ? $m[0] : __('failure');
                $status = 250;
            }
            $this -> data -> addBody(-140, $data, $msg, $status);
        }
    }
    /**
     * 评价订单/晒单
     *
     * @author
     */
    public function evaluation()
    {
        $order_id = request_string('order_id');
        $act = request_string('act');
        if ($act == 'again') {
            $evaluation_goods_id = request_int("oge_id");
            //获取已评价信息
            $Goods_EvaluationModel = new Goods_EvaluationModel();
            $data = $Goods_EvaluationModel -> getOne($evaluation_goods_id);
            if ($data['image']) {
                $data['image_row'] = explode(',', $data['image']);
                $data['image_row'] = array_filter($data['image_row']);
            }
            //商品信息
            $Order_GoodsModel = new Order_GoodsModel();
            $data['goods_base'] = current($Order_GoodsModel -> getByWhere(array('goods_id' => $data['goods_id'], 'order_id' => $data['order_id'])));
            //订单信息
            $Order_BaseModel = new Order_BaseModel();
            $data['order_base'] = $Order_BaseModel -> getOne($data['order_id']);
            //评价用户的信息
            $User_InfoModel = new User_InfoModel();
            $data['user_info'] = $User_InfoModel -> getOne($data['order_base']['buyer_user_id']);
            return $this -> data -> addBody(-140, $data);
        } elseif ($act == 'add') {
            //订单信息
            $Order_BaseModel = new Order_BaseModel();
            $data['order_base'] = $Order_BaseModel -> getOne($order_id);
            //评价用户的信息
            $User_InfoModel = new User_InfoModel();
            $data['user_info'] = $User_InfoModel -> getOne($data['order_base']['buyer_user_id']);
            //店铺信息
            $Shop_BaseModel = new Shop_BaseModel();
            $data['shop_base'] = $Shop_BaseModel -> getOne($data['order_base']['shop_id']);
            //查找出订单中的商品
            $Order_GoodsModel = new Order_GoodsModel();
            $order_goods_id_row = $Order_GoodsModel -> getKeyByWhere(array('order_id' => $order_id));
            //虚拟订单，商品评价
            $Goods_EvaluationModel = new Goods_EvaluationModel();
            $evaluation = $Goods_EvaluationModel -> getOneByWhere(array('order_id' => $order_id));
            if ($evaluation) {
                $data['evaluation'] = $evaluation;
            } else {
                $data['evaluation'] = array();
            }
            //商品信息
            foreach ($order_goods_id_row as $ogkey => $order_good_id) {
                $data['order_goods'][] = $Order_GoodsModel -> getOne($order_good_id);
            }
            return $this -> data -> addBody(-140, $data);

        }else{
            return $this -> data -> addBody(-140, []);
        }
    }
    /**
     * 确认收货
     *
     * @author
     */
    public function confirmOrder()
    {
            $user_id = request_int('user_id');
            $userModel = new User_BaseModel();
            $user_rows = $userModel->getBase($user_id);
            $user_account = $user_rows[$user_id]['user_account'];
            $Order_BaseModel = new Order_BaseModel();
            $Shop_BaseModel = new Shop_BaseModel();
            $Order_GoodsModel = new Order_GoodsModel();
            $Order_ReturnModel = new Order_ReturnModel();
            $rs_row = array();
            //开启事物
            $Order_BaseModel -> sql -> startTransactionDb();
            $order_id = request_string('order_id');
            $order_base = $Order_BaseModel -> getOne($order_id);
            //判断下单者是否是当前用户
            if ($order_base['buyer_user_id'] ==$user_id && $order_base['order_status'] == Order_StateModel::ORDER_WAIT_CONFIRM_GOODS) {
                $order_payment_amount = $order_base['order_payment_amount'];
                $condition['order_status'] = Order_StateModel::ORDER_FINISH;
                $condition['order_finished_time'] = get_date_time();
                //判断是否是货到付款订单，如果是货到付款订单，则将支付时间一起修改
                if ($order_base['payment_id'] == PaymentChannlModel::PAY_CONFIRM) {
                    $condition['payment_time'] = get_date_time();
                }
                if (Web_ConfigModel::value('Plugin_Directseller')) {
                    //确认收货以后将总佣金写入商品订单表
                    $order_goods_data = $Order_GoodsModel -> getByWhere(array('order_id' => $order_id));
                    $order_directseller_commission = array_sum(array_column($order_goods_data, 'directseller_commission_0')) + array_sum(array_column($order_goods_data, 'directseller_commission_1')) + array_sum(array_column($order_goods_data, 'directseller_commission_2'));
                    $order_directseller_commission_refund = array_sum(array_column($order_goods_data, 'directseller_commission_0_refund')) + array_sum(array_column($order_goods_data, 'directseller_commission_1_refund')) + array_sum(array_column($order_goods_data, 'directseller_commission_2_refund'));
                    $condition['order_directseller_commission'] = $order_directseller_commission - $order_directseller_commission_refund;
                }
                $edit_flag = $Order_BaseModel -> editBase($order_id, $condition);
                check_rs($edit_flag, $rs_row);
                //修改订单商品表中的订单状态
                $edit_row['order_goods_status'] = Order_StateModel::ORDER_FINISH;
                $order_goods_id = $Order_GoodsModel -> getKeyByWhere(array('order_id' => $order_id));
                $edit_flag1 = $Order_GoodsModel -> editGoods($order_goods_id, $edit_row);
                check_rs($edit_flag1, $rs_row);
                //货到付款时修改商品销量
                if ($order_base['payment_id'] == PaymentChannlModel::PAY_CONFIRM) {
                    $Goods_BaseModel = new Goods_BaseModel();
                    $edit_flag2 = $Goods_BaseModel -> editGoodsSale($order_goods_id);
                    check_rs($edit_flag2, $rs_row);
                }
                //将需要确认的订单号远程发送给Paycenter修改订单状态
                //远程修改paycenter中的订单状态
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');
                $formvars = array();
                $formvars['order_id'] = $order_id;
                $formvars['app_id'] = $shop_app_id;
                $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
                //判断订单是否是货到付款订单，货到付款订单不需要修改卖家资金
                if ($order_base['payment_id'] == PaymentChannlModel::PAY_CONFIRM) {
                    $formvars['payment'] = 0;
                }
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=confirmOrder&typ=json', $url), $formvars);
                if ($rs['status'] == 250) {
                    $rs_flag = false;
                    check_rs($rs_flag, $rs_row);
                } else {
                    //判断用户该笔订单是否存在退款，如果有的话添加商家的退款流水
                    $cond_row = array();
                    $cond_row['order_number'] = $order_id;
                    $cond_row['return_type'] = Order_ReturnModel::RETURN_TYPE_ORDER;
                    $cond_row['return_shop_handle'] = Order_ReturnModel::RETURN_SELLER_PASS;
                    $reforder = $Order_ReturnModel -> getByWhere($cond_row);
                    if ($reforder) {
                        $reforder = current($reforder);
                        $formvars = array();
                        $formvars['app_id'] = $shop_app_id;
                        $formvars['user_id'] = $order_base['buyer_user_id']; //收款人
                        $formvars['user_account'] = $order_base['buyer_user_account'];
                        $formvars['seller_id'] = $order_base['seller_user_id']; //付款人
                        $formvars['seller_account'] = $order_base['seller_user_name'];
                        $formvars['amount'] = $reforder['return_cash'];
                        $formvars['return_commision_fee'] = $reforder['return_commision_fee'];
                        $formvars['order_id'] = $order_id;
                        $formvars['goods_id'] = $reforder['order_goods_id'];
                        $formvars['uorder_id'] = $order_base['payment_other_number'];
                        $formvars['payment_id'] = $order_base['payment_id'];
                        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=refundShopTransfer&typ=json', $url), $formvars);
                    }
                }
                //查看是否是用户购买的分销商从供货商处分销的支持代发货的商品，如果是改变订单状态
                $supplier = new Supplier;
                if (Yf_Registry::get('supplier_is_open') == 0) {
                    $sp_order = $Order_BaseModel -> getByWhere(array('order_source_id' => $order_id));
                } else {
                    $sp_order = $supplier -> getOrderList(array('order_source_id' => $order_id));
                }
                if (!empty($sp_order)) {
                    foreach ($sp_order as $k => $value) {
                        $condition['payment_other_number'] = $value['payment_number'];
                        //分销订单的分销佣金需要单独结算
                        unset($condition['order_directseller_commission']);
                        if (Web_ConfigModel::value('Plugin_Directseller')) {
                            //确认收货以后将总佣金写入商品订单表
                            if (Yf_Registry::get('supplier_is_open') == 0) {
                                $order_goods_data = $Order_GoodsModel -> getByWhere(array('order_id' => $value['order_id']));
                            } else {
                                $order_goods_data = $supplier -> getOrderList(array('order_id' => $value['order_id']));
                            }
                            $order_directseller_commission = array_sum(array_column($order_goods_data, 'directseller_commission_0')) + array_sum(array_column($order_goods_data, 'directseller_commission_1')) + array_sum(array_column($order_goods_data, 'directseller_commission_2'));
                            $order_directseller_commission_refund = array_sum(array_column($order_goods_data, 'directseller_commission_0_refund')) + array_sum(array_column($order_goods_data, 'directseller_commission_1_refund')) + array_sum(array_column($order_goods_data, 'directseller_commission_2_refund'));
                            $condition['order_directseller_commission'] = $order_directseller_commission - $order_directseller_commission_refund;
                        }
                        if (Yf_Registry::get('supplier_is_open') == 0) {
                            $Order_BaseModel -> editBase($value['order_id'], $condition);
                            $sporder_goods_id = $Order_GoodsModel -> getKeyByWhere(array('order_id' => $value['order_id']));
                            $Order_GoodsModel -> editGoods($sporder_goods_id, $edit_row);
                        } else {
                            $supplier -> editOrder($value['order_id'], $condition);
                            $sporder_goods_id = $supplier -> getOrderGoodsKeyByWhere(array('order_id' => $value['order_id']));
                            $supplier -> editOrderGoods($sporder_goods_id, $edit_row);
                        }
                        //$Order_BaseModel->editBase($value['order_id'], $condition);
                        //$sporder_goods_id = $Order_GoodsModel->getKeyByWhere(array('order_id' => $value['order_id']));
                        //$Order_GoodsModel->editGoods($sporder_goods_id, $edit_row);
                        $formvars['order_id'] = $value['order_id'];
                        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=confirmOrder&typ=json', $url), $formvars);
                        if ($rs['status'] == 250) {
                            $rs_flag = false;
                            check_rs($rs_flag, $rs_row);
                            get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=confirmOrder&typ=json', $url), $formvars);
                        }
                    }
                }
                /*
            *  经验与成长值
            */
                $user_points = Web_ConfigModel::value("points_recharge");//订单每多少获取多少积分
                $user_points_amount = Web_ConfigModel::value("points_order");//订单每多少获取多少积分
                if ($order_payment_amount / $user_points < $user_points_amount) {
                    $user_points = floor($order_payment_amount / $user_points);
                } else {
                    $user_points = $user_points_amount;
                }
                //plus会员积分加倍
                $open_status = Web_ConfigModel::value('plus_switch')?:0;
                $plus_integral = Web_ConfigModel::value('plus_integral')?:0;
                $plusUserMdl =  new Plus_UserModel();
                $plus_desc = '';
                if($open_status && $plus_integral){
                    $plusUser = $plusUserMdl->getOne($user_id);
                    if($plusUser['user_status'] !=3 && $plusUser['end_date']>time()){
                        $plus_desc = ";PLUS会员积分加{$plus_integral}倍.";
                        $user_points=$user_points*$plus_integral;
                    }
                }
                $user_grade = Web_ConfigModel::value("grade_recharge");//订单每多少获取多少积分
                $user_grade_amount = Web_ConfigModel::value("grade_order");//订单每多少获取多少成长值
                if ($order_payment_amount / $user_grade > $user_grade_amount) {
                    $user_grade = floor($order_payment_amount / $user_grade);
                } else {
                    $user_grade = $user_grade_amount;
                }
                $User_ResourceModel = new User_ResourceModel();
                //获取积分经验值
                $ce = $User_ResourceModel -> getResource($user_id);
                $resource_row['user_points'] = $ce[$user_id]['user_points'] * 1 + $user_points * 1;
                $resource_row['user_growth'] = $ce[$user_id]['user_growth'] * 1 + $user_grade * 1;
                $res_flag = $User_ResourceModel -> editResource($user_id, $resource_row);
                $User_GradeModel = new User_GradeModel;
                //升级判断
                $res_flag = $User_GradeModel -> upGrade($user_id, $resource_row['user_growth']);
                //积分
                $points_row['user_id'] = $user_id;
                $points_row['user_name'] = $user_account;
                $points_row['class_id'] = Points_LogModel::ONBUY;
                $points_row['points_log_points'] = $user_points;
                $points_row['points_log_time'] = get_date_time();
                $points_row['points_log_desc'] = '确认收货'.$plus_desc;
                $points_row['points_log_flag'] = 'confirmorder';
                $Points_LogModel = new Points_LogModel();
                $Points_LogModel -> addLog($points_row);
                //成长值
                $grade_row['user_id'] = $user_id;
                $grade_row['user_name'] = $user_account;
                $grade_row['class_id'] = Grade_LogModel::ONBUY;
                $grade_row['grade_log_grade'] = $user_grade;
                $grade_row['grade_log_time'] = get_date_time();
                $grade_row['grade_log_desc'] = '确认收货';
                $grade_row['grade_log_flag'] = 'confirmorder';
                $Grade_LogModel = new Grade_LogModel;
                $Grade_LogModel -> addLog($grade_row);
                //分销商进货
                $shopBaseModel = new Shop_BaseModel();
                $shop_info = $shopBaseModel -> getOneByWhere(['user_id'=>$user_id]);
                $shop_id = $shop_info['id'];
                $shop_detail = $Shop_BaseModel -> getOne($order_base['shop_id']);
                if ($shop_id &&$shop_detail['shop_type'] == 2) {
                    $this -> add_product($order_id,$shop_id);
                }
            } else {
                $flag = false;
                check_rs($flag, $rs_row);
            }
            $flag = is_ok($rs_row);
            if ($flag && $Order_BaseModel -> sql -> commitDb()) {
                /**
                 *  加入统计中心
                 */
                $analytics_data = array();
                if ($order_id) {
                    $analytics_data['order_id'] = array($order_id);
                    $analytics_data['status'] = Order_StateModel::ORDER_FINISH;
                    Yf_Plugin_Manager::getInstance() -> trigger('analyticsUpdateOrderStatus', $analytics_data);
                }
                /******************************************************************/

                if (Web_ConfigModel::value('Plugin_Fenxiao')) {
                    if ($order_base['payment_id'] == PaymentChannlModel::PAY_CONFIRM  || $order_base['payment_id'] == PaymentChannlModel::PAY_CHAINPYA) {
                        Fenxiao::getInstance()->order($order_id);
                    }
                    Fenxiao::getInstance() -> confirmOrder(array($order_id));
                }
                $status = 200;
                $msg = __('success');
            } else {
                $Order_BaseModel -> sql -> rollBackDb();
                $m = $Order_BaseModel -> msg -> getMessages();
                $msg = $m ? $m[0] : __('failure');
                $status = 250;
            }
            $this -> data -> addBody(-140, array(), $msg, $status);

    }
    function add_product($order_id,$shop_id)
    {
        $shopDistributorModel = new Distribution_ShopDistributorModel();
        $Goods_CommonModel = new Goods_CommonModel();
        $Shop_BaseModel = new Shop_BaseModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $Order_GoodsModel = new Order_GoodsModel();
        $order_goods_list = $Order_GoodsModel -> getByWhere(array('order_id' => $order_id));
        foreach ($order_goods_list as $key => $value) {
            $edit_common_data = array();
            $shop_info = $Shop_BaseModel -> getOne($shop_id);
            $common_info = $Goods_CommonModel -> getOne($value['common_id']);
            //查看店铺商品中是否已经有该商品
            $shop_common = $Goods_CommonModel -> getOneByWhere(array('shop_id' => $shop_id, 'common_parent_id' => $common_info['common_id'], 'product_is_behalf_delivery' => 0));
            $old_common_id = $common_info['common_id'];
            if (empty($shop_common)) {
                //同步新商品
                $edit_common_data['common_stock'] = $value['order_goods_num'] - $value['order_goods_returnnum'];
                $common_id = $Goods_CommonModel -> SynchronousCommon($old_common_id, $shop_info);
            } else {
                $edit_common_data['common_spec_value'] = $shop_common['common_spec_value'];
                $common_id = $shop_common['common_id'];
                $stock = $shop_common['common_stock'] + $value['order_goods_num'] - $value['order_goods_returnnum'];
                //获取同步商品的信息
                $common_row = $Goods_CommonModel -> SynchronousCommon($old_common_id, $shop_info, 'edit');
                $common_row['common_stock'] = $stock;
                $Goods_CommonModel -> editCommon($shop_common['common_id'], $common_row);
                //商品详情信息
                $goodsCommonDetailModel = new Goods_CommonDetailModel();
                $common_detail = $goodsCommonDetailModel -> getOne($old_common_id);
//							$common_detail_data['common_id']   = $common_id;
                $common_detail_data['common_body'] = $common_detail['common_body'];
                $goodsCommonDetailModel -> editCommonDetail($common_id, $common_detail_data);
            }
            //查看店铺的商品goods_parent_id是否存在
            $shop_base = $Goods_BaseModel -> getOneByWhere(array('shop_id' => $shop_id, 'goods_parent_id' => $value['goods_id']));
            //根据商品订单表数据，同步goodbase数据
            $base = $Goods_BaseModel -> getOneByWhere(array('goods_id' => $value['goods_id']));
            if (!empty($base)) {
                $base_row = array();
                $base_row['common_id'] = $common_id;
                $base_row['shop_id'] = $shop_info['shop_id'];
                $base_row['shop_name'] = $shop_info['shop_name'];
                $base_row['goods_name'] = $base['goods_name'];
                $base_row['cat_id'] = $base['cat_id'];
                $base_row['brand_id'] = $base['brand_id'];
                $base_row['goods_spec'] = $base['goods_spec'];
                $base_row['goods_price'] = $base['goods_recommended_min_price'];
                $base_row['goods_market_price'] = $base['goods_recommended_max_price'];
                $base_row['goods_stock'] = $value['order_goods_num'] - $value['order_goods_returnnum'];
                $base_row['goods_image'] = $base['goods_image'];
                $base_row['goods_parent_id'] = $base['goods_id'];
                $base_row['goods_is_shelves'] = 2;
                $base_row['goods_recommended_min_price'] = $base['goods_recommended_min_price'];
                $base_row['goods_recommended_max_price'] = $base['goods_recommended_max_price'];
                $base_row['is_del'] = $base['is_del'];
                if (empty($shop_base)) {
                    $goods_id = $Goods_BaseModel -> addBase($base_row, true);
                } else {
                    $stock = $shop_base['goods_stock'] + $value['order_goods_num'] - $value['order_goods_returnnum'];
                    $base_row['goods_stock'] = $stock;
                    $goods_id = $shop_base['goods_id'];
                    $Goods_BaseModel -> editBase($shop_base['goods_id'], $base_row, false);
                }
                $goods_ids[] = array(
                    'goods_id' => $goods_id,
                    'color' => $base['color_id']
                );
                //重新构造common表common_spec_value,common_spec_name
                $GoodsSpecValueModel = new Goods_SpecValueModel();
                foreach ($base['goods_spec'] as $skey => $svalue) {
                    foreach ($svalue as $k => $v) {
                        $spec_valuebase = $GoodsSpecValueModel -> getOne($k);
                        if (!isset($edit_common_data['common_spec_value'][$spec_valuebase['spec_id']][$spec_valuebase['spec_value_id']])) {
                            $edit_common_data['common_spec_value'][$spec_valuebase['spec_id']][$spec_valuebase['spec_value_id']] = $spec_valuebase['spec_value_name'];
                        }
                    }
                }
            }
            $edit_common_data['goods_id'] = $goods_ids;
            $edit_common_data['common_state'] = 0;
            $Goods_CommonModel -> editCommon($common_id, $edit_common_data);
        }
    }

}