<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
    
    /**
     * @author     windfnn
     */
    class Seller_Trade_OrderCtl extends Seller_Controller
    {
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
        }
        
        /**
         * 实物交易订单
         *
         * @access public
         */
        public function physical()
        {
            $trade_name = trim(request_string("trade_name"));
            $condition = array();
            $condition['chain_id'] = 0;
            $Order_BaseModel = new Order_BaseModel();
            $order_row = array('order_create_time' => 'desc');
            $order_rows['goods_name:LIKE'] = '%' . $trade_name . '%';
            $orderGoodsModel = new Order_GoodsModel();
            $goods_rows = $orderGoodsModel->getByWhere($order_rows);
            $g_order_ids = array_column($goods_rows, 'order_id');
            $condition['order_id:IN'] =$g_order_ids;
            $data = $Order_BaseModel -> getPhysicalList($condition, $order_row);


            $condition = $data['condi'];
            foreach ($data['items'] as $k => $v) {
                $data['items'][$k]['order_benefits'] = array_filter(explode(' ', $v['order_shop_benefit']));
            }
            $Express = new Express();
            $Order_GoodsModel = new Order_GoodsModel();

            foreach ($data['items'] as $key => $val) {             
                $wuliuPc  =  array();              
                $order_id = $val['order_id'] ;
                $shiping_codes  = $val['shiping_codes'] ;
                if($shiping_codes ){
                        $shipping_codes = explode(',', $shiping_codes);
                        $shipping_codes = array_unique(array_values(array_filter($shipping_codes, function($value) {return !empty($value);})));
                        foreach ($shipping_codes as $kk => $vv){
                            $tmp = array();                         
                            $orderws = array();
                            $orderws["order_goods_shiping"] = $vv ;
                            $orderws["order_id"] = $order_id  ;
                            $order_goods =array_values($Order_GoodsModel->getByWhere($orderws));
                            $tmp['image'] = array();
                            foreach ($order_goods as $goods_k => $goods_v) {
                                $image = array();
                                $image = $goods_v['goods_image'];
                                $tmp['image'][] = $image ; 
                            }
                            $tmp['shiping_code'] = $vv;
                            $tmp['shiping_express'] = $order_goods[0]['order_goods_express'];
                            $express_name = $Express->getOne($order_goods[0]['order_goods_express']);
                            $tmp['express_name'] = $express_name['express_name'];
                            $tmp['order_id'] = $order_id;
                            $content = $this->getUrl($order_id,$order_goods[0]['order_goods_express'],$vv); 
                            $tmp['content'] =$content;
                            $wuliuPc[] =  $tmp ;               
                        }
               }
               $data['items'][$key]["wuliuPc"]  =  $wuliuPc  ;
            }
            include $this -> view -> getView();
        }
        public function exportOrder () {
            include $this -> view -> getView();
        }

    public static function getUrl($order_id,$express_id,$shipping_code){ 
       $url = Yf_Registry::get('shop_api_url')."/shop/api/logistic.php";
       $array['order_id'] = $order_id;
       $array['express_id'] = $express_id;
       $array['shipping_code'] = $shipping_code;
        $curl = curl_init(); 
        //设置提交的url 
        curl_setopt($curl, CURLOPT_URL, $url); 
        //设置头文件的信息作为数据流输出 
        curl_setopt($curl, CURLOPT_HEADER, 0); 
        //设置获取的信息以文件流的形式返回，而不是直接输出。 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        //设置post方式提交 
        curl_setopt($curl, CURLOPT_POST, 1); 
        //设置post数据 
        $post_data = $array; 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data); 
        //执行命令 
        $data = curl_exec($curl); 
        //关闭URL请求 
        curl_close($curl); 
       //获得数据并返回 
        return $data; 
      }
        /**
         * 实物交易订单Excel导出
         *
         * @access public
         */
        public function getOrderExcel ()
        {
            ob_end_clean();
            $order_id = request_string("order_id");
            $query_start_date = request_string("query_start_date");
            $query_end_date = request_string("query_end_date");
            $buyer_name = request_int("buyer_name");
            $shop_id = Perm::$shopId;
            $limit = request_int("limit");
            $start_limit = request_int("start_limit");
            $is_limit = request_int("is_limit");
            $order_status = request_int("order_status",0);
            //导出类型(0:分页导出，1：全部导出)
            $type = request_int("type");
            $cond_row = array();
            if ($order_id) {
                $cond_row['order_id'] = $order_id;
            }
            if ($buyer_name) {
                $cond_row['buyer_name'] = $buyer_name;
            }
            if ($query_start_date) {
                $cond_row['query_start_date'] = $query_start_date;
            }
            if ($query_end_date) {
                $cond_row['query_end_date'] = $query_end_date;
            }
            if ($shop_id) {
                $cond_row['shop_id'] = $shop_id;
            }
            if ($limit) {
                $cond_row['limit'] = $limit;
            }
            if ($limit) {
                $cond_row['start_limit'] = $start_limit;
            }
            if ($is_limit) {
                $cond_row['is_limit'] = $is_limit;
            }
            if ($order_status) {
                $cond_row['order_status'] = $order_status;
            }
            $cond_row['chain_id'] = 0;
            $cond_row['order_is_virtual'] = 0;
            $cond_row['order_shop_hidden'] = 0;

                $header = array(
                    "序号",
                    "订单编号",
                    "订单来源",
                    "下单时间",
                    "订单状态",
                    "支付单号",
                    "支付方式",
                    "支付时间",
                    "<span style='color:red'>发货物流单号</span>",
                    "<span style='color:red'>发货物流公司</span>",
                    "退款金额（元）",
                    "订单完成时间",
                    "是否评价",
                    "收货地址",
                    "收货人",
                    "收货人电话",
                    "买家账号",
                    "订单金额（元）",
                    "发票信息",
                    "订单留言",
                    "商品名称",
                     "单价", 
                    "订单数量",
                    "商品规格",
                );
                $Order_BaseModel = new Order_BaseModel();
                if ($type) {
                   $data = $Order_BaseModel->getOrderInfoExcel($cond_row,true);
                }else{
                   $data = $Order_BaseModel->getOrderInfoExcel($cond_row); 
                }
                $i = 1;
                $row =array();
                foreach ($data as $k=>$v) {
                    $row[$k][$k] = $i;
                    $row[$k]['order_id'] = $v['order_id'];
                    $row[$k]['order_from'] = $v['order_from'];
                    $row[$k]['order_create_time'] = $v['order_create_time'];
                    $row[$k]['order_status_text'] = $v['order_status_text'];
                    $row[$k]['payment_number'] = $v['payment_number'];
                    $row[$k]['payment_name'] = $v['payment_name'];
                    $row[$k]['payment_time'] = $v['payment_time'];
                    $row[$k]['order_shipping_code'] = '';
                    $row[$k]['order_shipping_company'] = '';
                    $row[$k]['order_refund_amount'] = $v['order_refund_amount'];
                    $row[$k]['order_finished_time'] = $v['order_finished_time'];
                    $row[$k]['order_buyer_evaluation_status'] = $v['order_buyer_evaluation_status'];
                    $row[$k]['order_receiver_address'] = $v['order_receiver_address'];
                    $row[$k]['order_receiver_name'] = $v['order_receiver_name'];
                    $row[$k]['order_receiver_contact'] = $v['order_receiver_contact'];
                    $row[$k]['buyer_user_name'] = $v['buyer_user_name'];  
                    $row[$k]['order_payment_amount'] = $v['order_payment_amount'];
                    $row[$k]['order_message'] = $v['order_message'];  
                    $row[$k]['order_invoice'] = $v['order_invoice'];  
                    $row[$k]['goods_info'] = $v['goods_info']; 
                    $i++;
                }
                $this->exportExcel($header,$row);
                die('导出成功！');
        }
        /**
         * 导出Excel功能
         * @param array $header 头部标题
         * @param array $data 数据
         * @param string $file_name 文件名
         */
       public function exportExcel(array $header,array $data,$file_name=''){
            ob_end_clean();
           !$file_name && $file_name = date("Y-m-d").".xls";
           //组装头部标题
            $head_txt = "<tr>";
            foreach ($header as $v) {
                $head_txt .= "<td>$v</td>";
            }
            $head_txt .= "</tr>";
            $html = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";
            $html .="<table border=1>" . $head_txt;
            $html .= '';
            //组装实体数据部分
            foreach ($data as $key => $rt) {
                $num = count($rt['goods_info']); //商品类的数量
                $html .= "<tr>";
                $i = 0;
                foreach ($rt as $k => $v) {
                     if (!is_array($v)) {
                       $html .= "<td rowspan=\"{$num}\">{$v}</td>\n";
                     }else{
                        if (count($v) > 1 ) {
                            foreach ($v as $goods) {
                                $i++;
                                if ($i > 1) {
                                   $html .= "<tr>";
                                }
                                $order_spec_info = implode(',',$goods['order_spec_info']);
                                $html .= "<td >{$goods['goods_name']}</td>";
                                $html .= "<td >{$goods['order_goods_num']}</td>";
                                $html .= "<td >{$goods['goods_price']}</td>";
                                $html .= "<td >{$order_spec_info}</td>";
                                if ($i > 1) {
                                   $html .= "</tr>\n";
                                }
                           }
                        }else{
                            $order_spec_info = implode(',',$v[0]['order_spec_info']);
                            $html .= "<td >{$v[0]['goods_name']}</td>";
                            $html .= "<td >{$v[0]['goods_price']}</td>";
                            $html .= "<td >{$v[0]['order_goods_num']}</td>";
                            $html .= "<td >{$order_spec_info}</td>";
                        }
                     }
                }
                $html .= "</tr>\n";
            }
            $html .= "</table></body></html>";
            ob_end_clean();
            header("Content-Type: application/vnd.ms-excel; name='excel'");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $file_name);
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");
            header("Expires: 0");
            exit($html);
        }
        /**
         * 虚拟交易订单
         *
         * @access public
         */
        public function virtual()
        {
            
            $Order_BaseModel = new Order_BaseModel();
            
            $condition['shop_id'] = Perm::$shopId;
            $condition['order_is_virtual'] = Order_BaseModel::ORDER_IS_VIRTUAL;
            $condition['order_shop_hidden'] = Order_BaseModel::ORDER_IS_REAL;
            $Order_BaseModel -> createSearchCondi($condition);
            $order_row = array('order_create_time' => 'desc');
            $order_virtual_list = $Order_BaseModel -> getOrderList($condition, $order_row);  //获取店铺订单列表
            
            include $this -> view -> getView();
        }
        
        /**
         * 门店自提订单
         *
         * @access public
         */
        public function chain()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condition['chain_id:!='] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condition, $order_row);
            $condition = $data['condi'];
            $condition['chain_name'] = request_string('chain_name');
            
            foreach ($data['items'] as $k => $v) {
                $data['items'][$k]['order_benefits'] = array_filter(explode(' ', $v['order_shop_benefit']));
            }
            
            include $this -> view -> getView();
        }
        
        /**
         * 虚拟交易订单--待付款订单
         *
         * @access public
         */
        public function getVirtualNew()
        {
            $Order_BaseModel = new Order_BaseModel();
            $Order_BaseModel -> createSearchCondi($condition);
            
            $condition['shop_id'] = Perm::$shopId;
            $condition['order_is_virtual'] = Order_BaseModel::ORDER_IS_VIRTUAL;
            $condition['order_shop_hidden'] = Order_BaseModel::ORDER_IS_REAL;
            $condition['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
            $order_row = array('order_create_time' => 'desc');
            $order_virtual_list = $Order_BaseModel -> getOrderList($condition, $order_row);  //获取店铺订单列表
            
            $this -> view -> setMet('virtual');
            include $this -> view -> getView();
        }
        
        /**
         * 虚拟交易订单--已付款订单(已发货)
         *
         * @access public
         */
        public function getVirtualPay()
        {
            $Order_BaseModel = new Order_BaseModel();
            $Order_BaseModel -> createSearchCondi($condition);
            
            $condition['shop_id'] = Perm::$shopId;
            $condition['order_is_virtual'] = Order_BaseModel::ORDER_IS_VIRTUAL;
            $condition['order_shop_hidden'] = Order_BaseModel::ORDER_IS_REAL;
            $condition['order_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
            $order_row = array('order_create_time' => 'desc');
            $order_virtual_list = $Order_BaseModel -> getOrderList($condition, $order_row);  //获取店铺订单列表
            
            $this -> view -> setMet('virtual');
            include $this -> view -> getView();
        }
        
        /**
         * 虚拟交易订单--交易成功订单
         *
         * @access public
         */
        public function getVirtualSuccess()
        {
            $Order_BaseModel = new Order_BaseModel();
            $Order_BaseModel -> createSearchCondi($condition);
            
            $condition['shop_id'] = Perm::$shopId;
            $condition['order_is_virtual'] = Order_BaseModel::ORDER_IS_VIRTUAL;
            $condition['order_shop_hidden'] = Order_BaseModel::ORDER_IS_REAL;
            $condition['order_status'] = Order_StateModel::ORDER_FINISH;
            $order_row = array('order_create_time' => 'desc');
            $order_virtual_list = $Order_BaseModel -> getOrderList($condition, $order_row);  //获取店铺订单列表
            
            $this -> view -> setMet('virtual');
            include $this -> view -> getView();
        }
        
        /**
         * 虚拟交易订单--取消订单列表
         *
         * @access public
         */
        public function getVirtualCancel()
        {
            $Order_BaseModel = new Order_BaseModel();
            $Order_BaseModel -> createSearchCondi($condition);
            
            $condition['shop_id'] = Perm::$shopId;
            $condition['order_is_virtual'] = Order_BaseModel::ORDER_IS_VIRTUAL;
            $condition['order_shop_hidden'] = Order_BaseModel::ORDER_IS_REAL;
            $condition['order_status'] = Order_StateModel::ORDER_CANCEL;
            $order_row = array('order_create_time' => 'desc');
            $order_virtual_list = $Order_BaseModel -> getOrderList($condition, $order_row);  //获取店铺订单列表
            
            $this -> view -> setMet('virtual');
            include $this -> view -> getView();
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
            
            if ($typ == 'e') {
                $cancel_row['cancel_identity'] = Order_CancelReasonModel::CANCEL_SELLER;
                
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
                
                if (($order_base['payment_id'] == PaymentChannlModel::PAY_CONFIRM
                        && $order_base['order_status'] == Order_StateModel::ORDER_WAIT_PREPARE_GOODS) //货到付款+等待发货
                    || $order_base['order_status'] == Order_StateModel::ORDER_WAIT_PAY
                    && $order_base['seller_user_id'] == Perm::$userId
                ) {
                    if (empty($state_info)) {
                        $state_info = request_string('state_info1');
                    }
                    //加入取消时间
                    $condition['order_status'] = Order_StateModel::ORDER_CANCEL;
                    $condition['order_cancel_reason'] = addslashes($state_info);
                    
                    $condition['order_cancel_identity'] = Order_BaseModel::IS_SELLER_CANCEL;
                    
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
                    
                    $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=cancelOrder&typ=json', $url), $formvars);
                    //如果是供货商取消进货订单，同时取消买家的订单或减少买家订单的金额
                    $dist_order = $Order_BaseModel -> getOne($order_base['order_source_id']);
                    if (!empty($dist_order)) {
                        $dist_goods_order = $Order_GoodsModel -> getByWhere(array('order_id' => $dist_order['order_id']));
                        if (count($dist_goods_order) == 1) {
                            $Order_BaseModel -> editBase($dist_order['order_id'], $condition);
                            $Order_GoodsModel -> editGoods($dist_goods_order[0]['order_goods_id'], $edit_row);
                            $Goods_BaseModel -> returnGoodsStock($dist_goods_order[0]['order_goods_id']);
                        } else {
                            foreach ($dist_goods_order as $key => $value) {
                                if ($value['order_goods_source_id'] == $order_id) {
                                    $dist_edit_row = array();
                                    $dist_edit_row['order_goods_amount'] = $dist_order['order_goods_amount'] - $value['goods_price'] * $value['order_goods_num'];
                                    $dist_edit_row['order_payment_amount'] = $dist_order['order_payment_amount'] - $value['order_goods_amount'];
                                    $Order_BaseModel -> editBase($dist_order['order_id'], $dist_edit_row);
                                    $Order_GoodsModel -> editGoods($dist_goods_order[$key]['order_goods_id'], $edit_row);
                                    $Goods_BaseModel -> returnGoodsStock($dist_goods_order[$key]['order_goods_id']);
                                }
                            }
                            $formvars['payment_amount'] = $dist_edit_row['order_payment_amount'];
                        }
                        $formvars['order_id'] = $dist_order['order_id'];
                        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=cancelOrder&typ=json', $url), $formvars);
                    }
                    
                    if ($rs['status'] == 200) {
                        $edit_flag3 = true;
                        check_rs($edit_flag3, $rs_row);
                    } else {
                        $edit_flag3 = false;
                        check_rs($edit_flag3, $rs_row);
                    }
                }

                $flag = is_ok($rs_row);
                
                if ($flag && $Order_BaseModel -> sql -> commitDb()) {
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
            
        }
        
        /**
         * 虚拟订单列表详情
         *
         * @access public
         */
        public function virtualInfo()
        {
            $Goods_BaseModel = new Goods_BaseModel();
            $Order_BaseModel = new Order_BaseModel();
            $condition['order_id'] = request_string('order_id');
            
            $order_data = $Order_BaseModel -> getOrderList($condition);
            $order_data = isset($order_data['items']) ? pos($order_data['items']) : array();
            $goods_list = isset($order_data['goods_list']) ? pos($order_data['goods_list']) : array();
            if ($goods_list) {
                //取出虚拟商品有效期 common_base => common_virtual_date
                $goods_id = $goods_list['goods_id'];
                $common_data = $Goods_BaseModel -> getCommonInfo($goods_id);
                $order_data['common_virtual_date'] = isset($common_data['common_virtual_date']) ? $common_data['common_virtual_date'] : '';
            }
            
            $data = $Order_BaseModel -> getOrderDetail($condition['order_id']);
            include $this -> view -> getView();
        }
        
        /**
         * 兑换虚拟订单
         *
         * @access public
         */
        public function virtualExchange()
        
{            $typ = request_string('typ');
            if ($typ == 'e') {
                include $this -> view -> getView();
            } else {
                $virtual_code_id = request_string('vr_code');
                $orderGoodsVirtualCodeModel = new Order_GoodsVirtualCodeModel();
                $virtual_code = $orderGoodsVirtualCodeModel -> getOne($virtual_code_id);
                $Order_GoodsModel = new Order_GoodsModel();
                $order_goods = $Order_GoodsModel -> getOne($virtual_code['order_goods_id']);
                // 先判断是否为同一家商店
                $shop_id = Perm::$shopId;
                if ($shop_id == $order_goods['shop_id']) {
                    //根据订单查看商品的有效期
                    $Goods_CommonModel = new Goods_CommonModel();
                    $goods_common_info = $Goods_CommonModel->getOne($order_goods['common_id']);
                    //商品过期时间
                    $common_virtual_date = strtotime($goods_common_info['common_virtual_date'].' 23:59:59');

                    if(time()>$common_virtual_date){
                        $msg = __('兑换失败,该商品已失效！');
                        return $this -> data -> addBody(-140, array(), $msg, 250);
                    }
                    // 如果订单发生退款申请，需要 退款完成 之后才可以继续兑换余下的兑换码，即平台审核完成。
                    $result = $orderGoodsVirtualCodeModel -> virtualExchange($virtual_code_id,Perm::$userId);

                    if ($result['status'] == true) {
                        $msg = $result['msg'] ? __($result['msg']) : __('兑换成功');
                        $data = $result['data'] ? $result['data'] : array();
                        $data['order_goods'] = $order_goods;
                        if (Web_ConfigModel::value('Plugin_Fenxiao')) {
                            $order_id = $order_goods['order_id'];
                            Fenxiao::getInstance() -> confirmOrder($order_id);
                        }
                        return $this -> data -> addBody(-140, $data, $msg, 200);
                    } else {
                        $msg = $result['msg'] ? __($result['msg']) : __('兑换失败');
                        return $this -> data -> addBody(-140, array(), $msg, 250);
                    }
                } else {
                    $msg = __('该兑换码不属于本店商品');
                    return $this -> data -> addBody(-140, array(), $msg, 250);
                }
            }
        }
        
        /**
         * 实物交易订单 ==> 待付款
         *
         * @access public
         */
        public function getPhysicalNew()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
            $condi['chain_id'] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            
            $this -> view -> setMet('physical');
            include $this -> view -> getView();
        }
        
        /**
         * 实物交易订单 ==> 已付款
         *
         * @access public
         */
        public function getPhysicalPay()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_PAYED;
            $condi['chain_id'] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            $this -> view -> setMet('physical');
            include $this -> view -> getView();
        }
        
        /**
         * 实物交易订单 ==> 待自提
         *
         * @access public
         */
        public function getPhysicalNotakes()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_SELF_PICKUP;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            
            $this -> view -> setMet('physical');
            include $this -> view -> getView();
        }
        
        /**
         * 实物交易订单 ==> 已发货
         *
         * @access public
         */
        public function getPhysicalSend()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
            $condi['chain_id'] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];


            foreach ($data['items'] as $k => $v) {
                $data['items'][$k]['order_benefits'] = array_filter(explode(' ', $v['order_shop_benefit']));
            }
            $Express = new Express();
            $Order_GoodsModel = new Order_GoodsModel();

            foreach ($data['items'] as $key => $val) {
                $wuliuPc  =  array();
                $order_id = $val['order_id'] ;
                $shiping_codes  = $val['shiping_codes'] ;
                if($shiping_codes ){
                    $shipping_codes = explode(',', $shiping_codes);
                    $shipping_codes = array_unique(array_values(array_filter($shipping_codes, function($value) {return !empty($value);})));
                    foreach ($shipping_codes as $kk => $vv){
                        $tmp = array();
                        $orderws = array();
                        $orderws["order_goods_shiping"] = $vv ;
                        $orderws["order_id"] = $order_id  ;
                        $order_goods =array_values($Order_GoodsModel->getByWhere($orderws));
                        $tmp['image'] = array();
                        foreach ($order_goods as $goods_k => $goods_v) {
                            $image = array();
                            $image = $goods_v['goods_image'];
                            $tmp['image'][] = $image ;
                        }
                        $tmp['shiping_code'] = $vv;
                        $tmp['shiping_express'] = $order_goods[0]['order_goods_express'];
                        $express_name = $Express->getOne($order_goods[0]['order_goods_express']);
                        $tmp['express_name'] = $express_name['express_name'];
                        $tmp['order_id'] = $order_id;
                        $content = $this->getUrl($order_id,$order_goods[0]['order_goods_express'],$vv);
                        $tmp['content'] =$content;
                        $wuliuPc[] =  $tmp ;
                    }
                }
                $data['items'][$key]["wuliuPc"]  =  $wuliuPc  ;

            }
            $this -> view -> setMet('physical');
            include $this -> view -> getView();
        }
        
        /**
         * 实物交易订单 ==> 已完成
         *
         * @access public
         */
        public function getPhysicalSuccess()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_FINISH;
            $condi['chain_id'] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            
            $this -> view -> setMet('physical');
            include $this -> view -> getView();
        }
        
        /**
         * 实物交易订单 ==> 已取消
         *
         * @access public
         */
        public function getPhysicalCancel()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_CANCEL;
            $condi['chain_id'] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            
            $this -> view -> setMet('physical');
            include $this -> view -> getView();
        }
        
        /**
         * 实物交易订单 ==> 订单详情
         *
         * @access public
         */
        public function physicalInfo()
        {
            $order_id = request_string('order_id');
            $Order_BaseModel = new Order_BaseModel();
            $data = $Order_BaseModel -> getPhysicalInfoData(array('order_id' => $order_id));
            $data['order_benefits'] = array_filter(explode(' ', $data['order_shop_benefit']));
            include $this -> view -> getView();
        }
        
        /**
         * 实物交易订单 ==> 打印发货单
         *
         * @access public
         */
        public function getOrderPrint()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_id'] = request_string('order_id');
            
            $data = $Order_BaseModel -> getOrderList($condi);
            $data = pos($data['items']);
            
            $data['goods_count'] = 0;
            $data['order_goods_amount_c'] = 0;
            $all_return_price = 0;
            foreach ($data['goods_list'] as $key => $val) {
                $data['goods_count'] += $val['order_goods_num_c'];
                $data['order_goods_amount_c'] += $val['order_goods_amount_c'];
                $all_return_price += $val['return_price'];

                //如果商品有规格属性，则展示
                if (!empty($val['order_spec_info'])) {
                    $data['goods_list'][$key]['goods_name'] .= "($val[order_spec_info])";
                }
            }
            $data['order_payment_amount_c'] = $data['order_payment_amount'] - $all_return_price;
            $data['order_discount_fee_c'] = $data['order_goods_amount_c'] + $data['order_shipping_fee'] - $data['order_payment_amount_c'];
            
            //读取店铺印章等信息
            $shop_id = Perm::$shopId;
            $shop_BaseModel = new Shop_BaseModel();
            $shop_base = $shop_BaseModel -> getBase($shop_id);
            $shop_base = pos($shop_base);
            $shop_print_desc = $shop_base['shop_print_desc'];
            $shop_stamp = $shop_base['shop_stamp'];
            
            $this -> view -> setMet('orderPrint');
            include $this -> view -> getView();
        }
        
        /**
         * 实物交易订单 ==> 设置发货
         *
         * @access public
         */
        public function send()
        {
            $typ = request_string('typ');
            $order_id = request_string('order_id');
            $Order_BaseModel = new Order_BaseModel();
            $Shop_ExpressModel = new Shop_ExpressModel();
            $Order_GoodsModel = new Order_GoodsModel();
            $style = request_string('style');
            if ($typ == 'e') {
                $condi['order_id'] = $order_id;
                $data = $Order_BaseModel -> getOrderList($condi);
                $data = pos($data['items']);
                //默认物流公司 url
                $default_express_url = Yf_Registry::get('url') . '?ctl=Seller_Trade_Deliver&met=express&typ=e';
                //打印运单URL
                $print_tpl_url = Yf_Registry::get('url') . '?ctl=Seller_Trade_Waybill&met=printTpl&typ=e&order_id=' . $order_id;
                //默认物流公司
                $express_list = $Shop_ExpressModel -> getDefaultShopExpress();
                if (is_array($express_list) && $express_list) {
                    $express_list = array_values($express_list);
                }
                include $this -> view -> getView();
            } else {
                //判断该笔订单是否是自己的单子
                $order_base = $Order_BaseModel -> getOne($order_id);
                if (!request_string('selectOne')) {
                    return $this->data->addBody(-140,array(),__('请选择要发货的商品'),250);
                }
                $rs_row = array();
                //开启事物
                $Order_BaseModel -> sql -> startTransactionDb();
                //批量发货判断条件
                  $user_id  = request_string("user_id");
                  if($user_id ==$order_base['seller_user_id'])
                  {
                      $check_send = 1;
                  }else
                  {
                  //判断账号是否可以发货
                  $check_send = $this -> checkSend($order_base['seller_user_id'], $order_base['shop_id']);  
                  }
                if ($check_send && $order_base['order_status'] < Order_StateModel::ORDER_RECEIVED) {
                    $Order_GoodsModel= new Order_GoodsModel();
                    $Order_GoodsAllId = explode(",",request_string('selectOne'));
                    $Order_GoodsAllId = array_values(array_filter($Order_GoodsAllId, function($value) {return !empty($value);}));     
                    if($Order_GoodsAllId){
                    }else{
                      return $this->data->addBody(-140,array(),__('请选择要发货的商品'),250);
                    }

                    foreach ($Order_GoodsAllId as $key => $val) {
                        $update_row = array();
                        $update_row['order_goods_shiping'] = request_string('order_shipping_code');
                        $update_row['order_goods_express'] = request_int('order_shipping_express_id');
                        $update_row['order_goods_is_receiving'] = 1;  // 1  表示发货
                        $update_row['order_goods_status'] =  Order_StateModel::ORDER_WAIT_CONFIRM_GOODS  ; // 变成已经发货
                        $flag = $Order_GoodsModel->editGoods($val,$update_row);
                        check_rs($flag,$rs_row);
                    }
                    $order_goods = $Order_GoodsModel->getByWhere(array('order_id'=>$order_id));

                    $is_receivng = 1;  // 表示发货完成 也就是全部发货
                    foreach ($order_goods as $kk => $vv) {
                        if($vv['order_goods_is_receiving']<1){
                            $is_receivng = 4 ; // 表示部分发货
                        }
                    }
                // 获取商品的物流单号信息
                $shiping_codes  = array_column($order_goods ,"order_goods_shiping") ;
                $update_data['order_shipping_express_id'] = request_int('order_shipping_express_id'); //配送公司id
                // $update_data['order_shipping_code']       = request_int('order_shipping_code'); //物流单号
                $update_data['order_shipping_message']    = request_string('order_shipping_message');  //卖家备注
                $update_data['order_seller_message']      = request_string('order_seller_message');

            
                if($shiping_codes){
                   $update_data['shiping_codes'] = implode(",",$shiping_codes);
                }else{
                   $update_data['shiping_codes']  = ""  ;
                }
                
                $update_data['is_receivng'] = $is_receivng;
                if($update_data['is_receivng'] == "1"){
                    // 如果等于 1 说明都发货了 要把订单状态修改成 已经发货的状态信息
                    //设置发货
                    $update_data['order_status']              = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
                }




                    $order_send_type = request_int('order_send_type');
                    //设置发货
                    $update_data['order_send_type'] = $order_send_type;
                    // $update_data['order_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
                    if($order_send_type==1){
                        $update_data['contact_name'] = request_string('user_name');
                        $update_data['contact_mobile'] = request_string('user_mobile');
                        $update_data['contact_remarks'] = request_string('remarks');
                    }else{
                        $update_data['order_shipping_express_id'] = request_int('order_shipping_express_id'); //配送公司id
                        $update_data['order_shipping_code'] = request_int('order_shipping_code'); //物流单号
                        $update_data['order_shipping_message'] = request_string('order_shipping_message');  //卖家备注
                        $update_data['order_seller_message'] = request_string('order_seller_message');
                    }
                    
                    //配送时间 收货时间
                    $current_time = time();
                    $confirm_order_time = Yf_Registry::get('confirm_order_time');
                    $update_data['order_shipping_time'] = date('Y-m-d H:i:s', $current_time);
                    $update_data['order_receiver_date'] = date('Y-m-d H:i:s', $current_time + $confirm_order_time);
                 
                    $edit_flag = $Order_BaseModel -> editBase($order_id, $update_data);
                    check_rs($edit_flag, $rs_row);
                    if(!empty($order_base['order_source_id'])){
                        $order_list = $Order_GoodsModel -> getByWhere(array('order_id' => $order_base['order_source_id'], 'order_goods_source_id' => ''));//查看不是分销商品的订单
                        if (!empty($order_list)) {
                            foreach ($order_list as $key => $value) {
                                $edit_flag1 = $Order_GoodsModel -> editGoods($key, array('order_goods_source_ship' => $update_data['order_shipping_code'] . '-' . $update_data['order_shipping_express_id']));
                                check_rs($edit_flag1, $rs_row);
                            }
                        }
                    }

                    //如果为采购单，改变 "买家<-->分销商" 订单状态
                    if ($order_base['order_source_id']) {
                        $dist_order = $Order_BaseModel -> getOneByWhere(array('order_id' => $order_base['order_source_id']));
                        if (!empty($dist_order)) {
                            /*
                                只有订单中不含分销商自己的商品时改变订单状态，如果含有分销商自己的商品，
                                供货商发货改变订单状态，分销商自己就发不了货了.
                                所以如果订单中含有分销商自己的商品，只有分销商的商品发货了，才能改变订单状态
                            */
                            if (empty($order_list)) {
                                $dist_flag = $Order_BaseModel -> editBase($dist_order['order_id'], $update_data);
                                check_rs($dist_flag, $rs_row);
                            }
                            //买家商品订单表里添加物流单号
                            $order_goods_id = $Order_GoodsModel -> getKeyByWhere(array('order_goods_source_id' => $order_id));
                            $edit_flag2 = $Order_GoodsModel -> editGoods($order_goods_id, array('order_goods_source_ship' => $update_data['order_shipping_code'] . '-' . $update_data['order_shipping_express_id']));
                            
                            check_rs($edit_flag2, $rs_row);
                        }
                    }
                    
                    $message = new MessageModel();
                    //远程修改paycenter中的订单信息
                    $key = Yf_Registry::get('shop_api_key');
                    $url = Yf_Registry::get('paycenter_api_url');
                    $shop_app_id = Yf_Registry::get('shop_app_id');
                    $formvars = array();
                    
                    $formvars['order_id'] = $order_id;
                    $formvars['app_id'] = $shop_app_id;
                    
                    $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=sendOrderGoods&typ=json', $url), $formvars);
                    
                    if ($rs['status'] == 200) {
                        $rs_flag = true;
                        check_rs($rs_flag, $rs_row);
                    } else {
                        $rs_flag = false;
                        check_rs($rs_flag, $rs_row);
                    }
                    if (!empty($dist_order) && isset($dist_flag) && $dist_flag) {//如果为采购单，改变 "买家<-->分销商" 订单状态
                        $message -> sendMessage('ordor_complete_shipping', $dist_order['buyer_user_id'], $dist_order['buyer_user_name'], $dist_order['order_id'], $dist_order['shop_name'], 0, MessageModel::ORDER_MESSAGE);
                        $formvars['order_id'] = $dist_order['order_id'];
                        $formvars['app_id'] = $shop_app_id;
                        
                        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=sendOrderGoods&typ=json', $url), $formvars);
                        if ($rs['status'] == 200) {
                            $rs_flag = true;
                            check_rs($rs_flag, $rs_row);
                        } else {
                            $rs_flag = false;
                            check_rs($rs_flag, $rs_row);
                        }
                    }
                } else {
                    $flag = false;
                    check_rs($flag, $rs_row);
                }

                $flag = is_ok($rs_row);
                if ($flag && $Order_BaseModel -> sql -> commitDb()) {
                    //发送站内信
                    $message = new MessageModel();
                    $message -> sendMessage('order_complete_shipping', $order_base['buyer_user_id'], $order_base['buyer_user_name'], $order_id, $order_base['shop_name'], 0, MessageModel::ORDER_MESSAGE);
                    $msg = __('发货成功');
                    $status = 200;
					//插入发货信息
					//$wx_sql ="select status from yf_seller_wxpublic_tplmsgstate where shop_id = '{$order_base['shop_id']}'";
					//$state = $Order_BaseModel->sql->getRow($wx_sql);
					//if($state['status']){
					//判断平台公众号，模板消息推送是否开启
					$tpl_status = Yf_Wxpublic::getWxPublixTplMsgStatus();
					if($tpl_status){
						$goods_sql ="select goods_name,order_goods_num from yf_order_goods where order_id='{$order_base['order_id']}'";
						$ord_gs = $Order_BaseModel -> sql->getAll($goods_sql);
						$num = 0;
						$gs_str ="";
						foreach($ord_gs as $items){
							$num+=$items['order_goods_num'];
							$gs_str.= $items['goods_name'].";";
						}
						$tpl_arr[] = array(
							'first'=> '您的订单已发货！',
							'keyword1'=> $order_base['order_payment_amount'],//订单金额
							'keyword2'=>$gs_str,//订单详情
							'keyword3'=>$order_base['order_id'],//订单单号
							'keyword4'=>$order_base['order_receiver_name'],//收货人
							'keyword5'=>$num,//商品数量
							'remark'=> '请注意查收！',
							'shop_id'=>$order_base['shop_id'],
							'buyer_user_id'=>$order_base['buyer_user_id'],
							'buyer_user_name'=>$order_base['buyer_user_name'],
							'type'=>2,//发货通知
						);
						Yf_Wxpublic::addWxpublicTplMsg($message,$tpl_arr);
						unset($tpl_arr);
					}


                } else {
                    $Order_BaseModel -> sql -> rollBackDb();
                    $msg = __('failure');
                    $status = 250;
                }
                $this -> data -> addBody(-140, array(), $msg, $status);
            }
        }
        
        //顺丰电子面单
        
        /**
         * 检验该账户是否可以发货
         *
         * @param type $seller_id
         * @param type $shop_id
         *
         * @return boolean
         */
        public function checkSend($seller_id, $shop_id)
        {
            $user_id = Perm::$userId;
            if ($seller_id == $user_id) {
                return true;
            } else {
                //判断是否为子账号
                $seller_base_model = new Seller_BaseModel();
                $result = $seller_base_model -> getByWhere(array('user_id' => $user_id));
                $seller_info = array_shift($result);
                if ($seller_info['shop_id'] == $shop_id) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        
        public function sendShipInfo()
        {
            $seller_address_span = request_string('seller_address_span');
            $buyer_address_span = request_string('buyer_address_span');
            $order_id = request_string('order_id');
            $receive_name = request_string('receive_name');
            $receive_mobile = request_string('receive_mobile');
            $receive_address = request_string('receive_address');
            //构造电子面单提交信息
            
            $seller = explode(' ', trim($seller_address_span));
            $buyer = explode(' ', trim($receive_address));
            
            $receiver = array();
            $receiver["Name"] = $receive_name;
            $receiver["Mobile"] = $receive_mobile;
            $receiver["ProvinceName"] = $buyer[0];
            $receiver["CityName"] = $buyer[1];
            $receiver["ExpAreaName"] = $buyer[2];
            $receiver["Address"] = $buyer[3];
            
            $sender = ["Name" => array_shift($seller)];
            if (preg_match("/^\d*$/", $seller[0])) {
                list($sender["Mobile"], $sender["ProvinceName"], $sender["CityName"], $sender["ExpAreaName"], $sender["Address"]) = $seller;
            } else {
                list($sender["ProvinceName"], $sender["CityName"], $sender["ExpAreaName"], $sender["Address"], $sender["Mobile"]) = $seller;
            }
            
            $commodityOne = array();
            $commodityOne["GoodsName"] = $order_id;
            $commodity = array();
            $commodity[] = $commodityOne;
            
            $eorder = array();
            $eorder["ShipperCode"] = "SF";
            $eorder["OrderCode"] = $order_id;
            $eorder["PayType"] = 1;
            $eorder["ExpType"] = 1;
            $eorder["Sender"] = $sender;
            $eorder["Receiver"] = $receiver;
            $eorder["Commodity"] = $commodity;
            
            //解析电子面单返回结果
            $kdo = new Api_KdNiao();
            $result = $kdo -> submitEOrder($eorder);
//            echo '<pre>';
//            print_r($result);die;
            $this -> data -> addBody(-140, $result);
        }
        
        /**
         * 实物交易订单 ==> 选择发货地址
         *
         * @access public
         */
        public function chooseSendAddress()
        {
            $typ = request_string('typ');
            
            if ($typ == 'e') {
                $shop_id = request_int('shop_id');
                $Shop_ShippingAddressModel = new Shop_ShippingAddressModel();
                $address_list = $Shop_ShippingAddressModel -> getByWhere(array('shop_id' => $shop_id));
                $address_list = array_values($address_list);
                foreach ($address_list as $key => $val) {
                    $address_list[$key]['address_info'] = $val['shipping_address_area'] . " " . $val['shipping_address_address'];
                    $address_list[$key]['address_value'] = $val['shipping_address_contact'] . "&nbsp" . $val['shipping_address_phone'] . "&nbsp" . $val['shipping_address_area'] . "&nbsp" . $val['shipping_address_address'];
                }
                
                include $this -> view -> getView();
            } else {
                $order_id = request_string('order_id');
                $send_address = request_row('send_address');
                
                $Order_BaseModel = new Order_BaseModel();
                $update_data['order_seller_name'] = $send_address['order_seller_name'];
                $update_data['order_seller_address'] = $send_address['order_seller_address'];
                $update_data['order_seller_contact'] = $send_address['order_seller_contact'];
                $flag = $Order_BaseModel -> editBase($order_id, $update_data);
                
                if ($flag || $flag === 0) {
                    $msg = __('设置成功');
                    $status = 200;
                } else {
                    $msg = __('设置失败');
                    $status = 250;
                }
                
                $this -> data -> addBody(-140, array(), $msg, $status);
            }
            
        }
        
        /**
         * 实物交易订单 ==> 选择发货地址
         *
         * @access public
         */
        public function editBuyerAddress()
        {
            $typ = request_string('typ');
            //获取一级地址
            $district_parent_id = request_int('pid', 0);
            $baseDistrictModel = new Base_DistrictModel();
            $district = $baseDistrictModel -> getDistrictTree($district_parent_id);
            if ($typ == 'e') {
                include $this -> view -> getView();
            } else {
                $Order_BaseModel = new Order_BaseModel();
                $order_id = request_string('order_id');
                $address_info = request_string('address_info');
                $receiver_address = trim(request_string('order_receiver_address'));
                $update_data['order_receiver_name'] = request_string('order_receiver_name');
                $update_data['order_receiver_contact'] = request_string('order_receiver_contact');
                $update_data['area_code'] = request_string('area_code')?:86;
                $update_data['order_receiver_address'] = $address_info . ' ' . $receiver_address;
                $flag = $Order_BaseModel -> editBase($order_id, $update_data);
                
                if ($flag) {
                    $update_data['receiver_info'] = $update_data['order_receiver_name'] . "&nbsp;" . $update_data['order_receiver_contact'] . "&nbsp;" . $update_data['order_receiver_address'];
                    $msg = __('success');
                    $status = 200;
                } else {
                    $msg = __('failure');
                    $status = 250;
                }
                
                $this -> data -> addBody(-140, $update_data, $msg, $status);
            }
            
        }
        
        /**
         * 商家中心首页不同状态订单数目
         *
         * @access public
         */
        public function getOrderNum()
        {
            $order_type = request_int('order_type');
            
            $orderBaseModel = new Order_BaseModel();
            $orderReturn = new Order_ReturnModel();
            
            //待付款订单
            $condi = array();
            $condi['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
            $condi['chain_id'] = 0;
            $wait = $orderBaseModel -> getPhysicalList($condi);
            $wait_pay_num = $wait['records'];
            
            //待发货订单
            //待发货订单
            $condi = array();
            $condi['order_status:IN'] = array(
                Order_StateModel::ORDER_PAYED,
                Order_StateModel::ORDER_WAIT_PREPARE_GOODS
            );
            $condi['order_is_virtual'] = 0;
            $condi['order_payment_amount-order_refund_amount:>='] = '0';
            $condi['shop_id'] = Perm::$shopId;
            $order = $orderBaseModel -> getByWhere($condi);
            $Order_ReturnModel = new Order_ReturnModel();
            foreach($order as $key => $val){
                $order_return = $Order_ReturnModel -> getOneByWhere(array('order_number'=>$val['order_id']));
                if($order_return){
                    unset($order[$key]);
                }
            }
            
            //退款订单
            $condi = array();
            $condi['seller_user_id'] = Perm::$shopId;
            $condi['return_state'] = Order_ReturnModel::RETURN_WAIT_PASS;
            $condi['return_type:!='] = Order_ReturnModel::RETURN_TYPE_GOODS;
            $refund_data = $orderReturn -> getByWhere($condi);
            
            //退货订单
            $condi = array();
            $condi['seller_user_id'] = Perm::$shopId;
            $condi['return_state'] = Order_ReturnModel::RETURN_WAIT_PASS;
            $condi['return_type'] = Order_ReturnModel::RETURN_TYPE_GOODS;
            $return_data = $orderReturn -> getByWhere($condi);
            
            $data['wait_pay_num'] = $wait_pay_num;
            $data['payed_num'] = count($order);
            $data['refund_num'] = count($refund_data);
            $data['return_num'] = count($return_data);
            
            $this -> data -> addBody(-140, $data);
            
        }
        
        /**
         * 门店自提订单 ==> 待付款
         *
         * @access public
         */
        public function getChainNew()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
            $condi['chain_id:!='] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            
            $this -> view -> setMet('chain');
            include $this -> view -> getView();
        }
        
        /**
         * 门店自提订单 ==> 待自提
         *
         * @access public
         */
        public function getChainNotakes()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_SELF_PICKUP;
            $condi['chain_id:!='] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            
            $this -> view -> setMet('chain');
            include $this -> view -> getView();
        }
        
        /**
         * 门店自提订单 ==> 已完成
         *
         * @access public
         */
        public function getChainSuccess()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_FINISH;
            $condi['chain_id:!='] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            
            $this -> view -> setMet('chain');
            include $this -> view -> getView();
        }
        
        /**
         * 门店自提订单 ==> 已取消
         *
         * @access public
         */
        public function getChainCancel()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_status'] = Order_StateModel::ORDER_CANCEL;
            $condi['chain_id:!='] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            
            $this -> view -> setMet('chain');
            include $this -> view -> getView();
        }
        
        /**
         * 门店自提订单 ==> 订单详情
         *
         * @access public
         */
        public function chainInfo()
        {
            $order_id = request_string('order_id');
            $Order_BaseModel = new Order_BaseModel();
            $data = $Order_BaseModel -> getChainInfoData(array('order_id' => $order_id));
            $data['order_benefits'] = array_filter(explode(' ', $data['order_shop_benefit']));
            //获取门店信息
            $chain_id = $data['chain_id'];
            $chain_model = new Chain_BaseModel;
            $chain_data = $chain_model -> getChainInfo($chain_id);
            include $this -> view -> getView();
        }
        
        /**
         * 修改订单金额
         *
         * @access public
         */
        public function cost()
        {
            
            $Order_BaseModel = new Order_BaseModel();
            
            $order_id = request_string('order_id');
            
            $order_base = $Order_BaseModel -> getBase($order_id);  //获取店铺订单列表
            $order_base = $order_base[$order_id];
            
            //获取订单商品信息
            $Order_GoodsModel = new Order_GoodsModel();
            $order_goods_row = $Order_GoodsModel -> getGoodsListByOrderId($order_id);
            $data = $order_goods_row['items'];
            
            include $this -> view -> getView();
        }
        
        public function editCost()
        {
            $order_id = request_string('order_id');
            $product_row = request_row('product_id');
            $shipping = request_float('shipping');
            $goods_edit_flag = false;
            $shipping_edit_flag = false;
            $flag = true;
            
            $Order_GoodsModel = new Order_GoodsModel();
            
            //开启事物
            $Order_GoodsModel -> sql -> startTransactionDb();
            
            $order_goods_row = $Order_GoodsModel -> getGoodsListByOrderId($order_id);
            //订单商品列表
            $data = $order_goods_row['items'];
            
            $Order_BaseModel = new Order_BaseModel();
            //订单详情
            $order_base = $Order_BaseModel -> getBase($order_id);
            $order_base = $order_base[$order_id];
            
            $Goods_CatModel = new Goods_CatModel();
            $Order_GoodsSnapshot = new Order_GoodsSnapshot();
            
            //1.修改订单商品表中商品的价格
            $order_edit_row = array();
            $order_goods_amount = 0;    //商品总价（不包含运费）
            $order_payment_amount = 0;  //实际应付金额（商品总价 + 运费）
            $order_discount_fee = 0;   //优惠价格
            $order_commission_fee = 0;   //交易佣金
            
            //判断该订单是否为待付款订单
            if ($order_base['order_status'] == Order_StateModel::ORDER_WAIT_PAY) {
                foreach ($data as $key => $val) {
                    //判断商品价格是否被修改了
                    if ($val['order_goods_payment_amount'] != $product_row[$val['goods_id']]) {
//                        if (intval($product_row[$val['goods_id']]) > intval($val['goods_price'])) {
                        if (bccomp($product_row[$val['goods_id']], $val['goods_price'], 2) > 0) {
                           return $this -> data -> addBody(-140, $data, "修改后的金额不能大于商品单价*数量", 250);
                        } else {
                            $goods_edit_flag = true;

                            $edit_row = array();

                            //每件商品实际支付金额
                            $edit_row['order_goods_payment_amount'] = $product_row[$val['goods_id']];
                            //手工调整金额
                            $edit_row['order_goods_adjust_fee'] = $val['order_goods_payment_amount'] - $product_row[$val['goods_id']];
                            //商品实际支付总金额
                            $edit_row['order_goods_amount'] = $product_row[$val['goods_id']] * $val['order_goods_num'];
                            //优惠价格，修改订单商品的优惠信息
                            if ($edit_row['order_goods_adjust_fee'] > 0) {
                                $edit_row['order_goods_benefit'] = $val['order_goods_benefit'] . __(" 调整单价:直降") . format_money($edit_row['order_goods_adjust_fee']);
                            } else {
                                $adjust_fee = $edit_row['order_goods_adjust_fee'] * (-1);
                                $edit_row['order_goods_benefit'] = $val['order_goods_benefit'] . __(" 调整单价:增加") . format_money($adjust_fee);
                            }

                            //重新计算该件商品的佣金
                            //获取分类佣金
                            $cat_base = $Goods_CatModel -> getOne($val['goods_class_id']);
                            if ($cat_base) {
                                $cat_commission = $cat_base['cat_commission'];
                            } else {
                                $cat_commission = 0;
                            }

                            //订单商品的佣金
                            $edit_row['order_goods_commission'] = number_format(($edit_row['order_goods_amount'] * $cat_commission / 100), 2, '.', '');

                            $Order_GoodsModel -> editGoods($val['order_goods_id'], $edit_row);

                            $order_goods_amount += $edit_row['order_goods_amount'];
                            $order_discount_fee += $edit_row['order_goods_benefit'];
                            $order_commission_fee += $edit_row['order_goods_commission'];

                            //2.修改快照表
                            $array = array();
                            $array['order_id'] = $order_id;
                            $array['goods_id'] = $val['goods_id'];
                            $snapshot_id = $Order_GoodsSnapshot -> getKeyByWhere($array);

                            $edit_snapshot_row = array();
                            $edit_snapshot_row['goods_price'] = $product_row[$val['goods_id']];
                            $edit_snapshot_row['freight'] = $shipping;
                            $Order_GoodsSnapshot -> editSnapshot($snapshot_id, $edit_snapshot_row);
                        }
                        
                    } else {
                        $order_goods_amount += $val['order_goods_amount'];
                        $order_discount_fee += $val['order_goods_benefit'];
                        $order_commission_fee += $val['order_goods_commission'];
                    }
                    
                }
                
                //3.修改订单表
                //判断运费是否改变
                if ($order_base['order_shipping_fee'] < $shipping) {
                    $flag = false;
                } else {
                    if ($order_base['order_shipping_fee'] != $shipping) {
                        $shipping_edit_flag = true;
                        $order_edit_row['order_shipping_fee'] = $shipping;
                    }
                }
                
                //如果修改了商品价格或者修改了运费则需要修改订单表
                if ($shipping_edit_flag || $goods_edit_flag) {
                    //商品总价（不包含运费）
                    $order_edit_row['order_goods_amount'] = $order_goods_amount;
                    //应付金额（商品总价 + 运费）
                    $order_edit_row['order_payment_amount'] = $order_goods_amount + $shipping;
                    //优惠价格
                    $order_edit_row['order_discount_fee'] = $order_discount_fee;
                    //交易佣金
                    $order_edit_row['order_commission_fee'] = $order_commission_fee;
                    
                    $Order_BaseModel -> editBase($order_id, $order_edit_row);
                    
                    //远程修改paycenter中的订单数据
                    //生成合并支付订单
                    $key = Yf_Registry::get('shop_api_key');
                    $url = Yf_Registry::get('paycenter_api_url');
                    $shop_app_id = Yf_Registry::get('shop_app_id');
                    $formvars = array();
                    
                    $formvars['order_id'] = $order_id;
                    $formvars['uorder_id'] = $order_base['payment_number'];
                    $formvars['app_id'] = $shop_app_id;
                    $formvars['edit_row'] = $order_edit_row;
                    
                    $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=editOrderCost&typ=json', $url), $formvars);

                    if ($rs['status'] == 200) {
                        $flag = true;
                    } else {
                        $flag = false;
                    }
                }
            } else {
                $flag = false;
            }
            
            if ($flag && $Order_GoodsModel -> sql -> commitDb()) {
                $msg = 'success';
                $status = 200;
            } else {
                $Order_GoodsModel -> sql -> rollBackDb();
                $m = $Order_GoodsModel -> msg -> getMessages();
                $msg = $m ? $m[0] : __('failure');
                $status = 250;
            }
            $data = array();
            $this -> data -> addBody(-140, $data, $msg, $status);
            
        }
        
        /**
         *  获取隐藏的实物订单
         *
         * @author Str
         */
        public function getPhysicalHideOrder()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condi['order_shop_hidden'] = $Order_BaseModel::IS_SELLER_HIDDEN;
            $condi['chain_id'] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condi, $order_row);
            $condition = $data['condi'];
            $this -> view -> setMet('physical');
            include $this -> view -> getView();
        }
        
        public function getVirtualHideOrder()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condition['order_shop_hidden'] = $Order_BaseModel::IS_SELLER_HIDDEN;
            $condition['shop_id'] = Perm::$shopId;
            $condition['order_is_virtual'] = Order_BaseModel::ORDER_IS_VIRTUAL;
            $Order_BaseModel -> createSearchCondi($condition);
            $order_row = array('order_create_time' => 'desc');
            $order_virtual_list = $Order_BaseModel -> getOrderList($condition, $order_row);  //获取店铺订单列表
            $this -> view -> setMet('virtual');
            include $this -> view -> getView();
        }
        
        public function getChainHideOrder()
        {
            $Order_BaseModel = new Order_BaseModel();
            $condition['order_shop_hidden'] = $Order_BaseModel::IS_SELLER_HIDDEN;
            $condition['chain_id:!='] = 0;
            $order_row = array('order_create_time' => 'desc');
            $data = $Order_BaseModel -> getPhysicalList($condition, $order_row);
            $condition = $data['condi'];
            $this -> view -> setMet('chain');
            include $this -> view -> getView();
        }
        
        /**
         * 删除订单
         *
         * @author     Str
         */
        public function hideOrder()
        {
            $order_id = request_string('order_id');
            $user = request_string('user');
            $op = request_string('op');
            
            $edit_row = array();
            $flag = false;
            $Order_BaseModel = new Order_BaseModel();
            $order_base = $Order_BaseModel -> getOne($order_id);
            
            //买家删除订单
            if ($user == 'seller') {
                //判断订单状态是否是已完成（6）或者已取消（7）状态
                if ($order_base['order_status'] >= Order_StateModel::ORDER_FINISH) {
                    //判断当前用户是否是卖家
                    if ($order_base['seller_user_id'] == Perm::$userId) {
                        if ($op == 'del') {
                            $edit_row['order_shop_hidden'] = Order_BaseModel::IS_SELLER_REMOVE;
                        } else {
                            $edit_row['order_shop_hidden'] = Order_BaseModel::IS_SELLER_HIDDEN;
                        }
                    }
                }
                
                $flag = $Order_BaseModel -> editBase($order_id, $edit_row);
            }
            
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
         * 还原回收站中的订单
         *
         * @author     Str
         */
        public function restoreOrder()
        {
            $order_id = request_string('order_id');
            $user = request_string('user');
            
            $edit_row = array();
            $flag = false;
            $Order_BaseModel = new Order_BaseModel();
            
            if ($user == 'seller') {
                $edit_row['order_shop_hidden'] = Order_BaseModel::NO_SELLER_HIDDEN;
                $flag = $Order_BaseModel -> editBase($order_id, $edit_row);
            }
            
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
         * 货到付款订单确认收款
         *
         * @param type $order_id
         *
         * @return boolean
         */
        public function confirmCollection()
        {
            $order_id = request_string('order_id');
            
            $Order_BaseModel = new Order_BaseModel();
            //查找订单信息
            $order_base = $Order_BaseModel -> getOne($order_id);
            
            //判断当前用户是否是商家，判断订单状态是否是已发货或者已完成状态，判断当前订单是否是货到付款订单，判断是否已经确认收款
            if ($order_base['seller_user_id'] == Perm::$userId && ($order_base['order_status'] == Order_StateModel::ORDER_WAIT_CONFIRM_GOODS || $order_base['order_status'] == Order_StateModel::ORDER_FINISH) && $order_base['payment_id'] == PaymentChannlModel::PAY_CONFIRM && $order_base['payment_time'] <= 0) {
                //修改订单的付款时间
                $flag = $Order_BaseModel -> editBase($order_id, array('payment_time' => get_date_time()));
            } else {
                $flag = false;
            }
            if ($flag) {
                $status = 200;
                $msg = __('success');
            } else {
                $msg = __('failure');
                $status = 250;
            }
            
            $this -> data -> addBody(-140, array(), $msg, $status);
            
        }
    }

?>
