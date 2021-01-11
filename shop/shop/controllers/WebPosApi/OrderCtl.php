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
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2015远丰仁商
 * @version    1.0
 * @todo
 */
class WebPosApi_OrderCtl extends WebPosApi_Controller
{
    	
	public $Order_BaseModel  = null;
	public $Order_GoodsModel = null;
	public $Goods_BaseModel = null;
    public $userBaseModel    = null;
    public $tradeOrderModel = null;
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
		$this->shopInfoModel = new Shop_BaseModel();
		$this->orderBaseModel = new Order_BaseModel();
		$this->ChainUserModel = new Chain_UserModel();
		$this->orderGoodsModel = new Order_GoodsModel();
		$this->userInfoModel = new User_InfoModel();
		$this->pointsOrderModel = new Points_OrderModel();
		$this->pointsOrderGoodsModel = new Points_OrderGoodsModel();
		$this->pointsOrderAddressModel = new Points_OrderAddressModel();
		$this->userResourceModel = new User_ResourceModel();
		$this->tradeOrderModel = new Order_BaseModel();
		$this->orderReturnModel = new Order_ReturnModel();

	}

    /*
     *通过店家id获取店铺所有订单信息
     * @param int $user_id 用户id
     * @access public
     */
    public function getShopOrderByUserId(){
        $user_id = request_int('user_id');//店员或店家用户id
        $data = [];
        $shop_info = $this->shopInfoModel->getOneByWhere(['user_id'=>$user_id]);
        $chain_info = $this->ChainUserModel->getByWhere(['shop_id'=>$shop_info['shop_id']]);
        //如果没有店铺信息，说明是店员登录，返回
        if($shop_info)
        {
            $status = 200;
            foreach ($chain_info as $k => $v) {
            	$chain_id[] = $v['chain_id'];
            }
            $data = $this->orderBaseModel->getByWhere(['shop_id'=>$shop_info['shop_id'],'order_status'=>'6','chain_id:IN'=>$chain_id]);
            if($data)
            {
                $data = array_values($data);
                $msg = 'success';
            }
            else
            {
                $msg = 'success:店铺还没有订单';
            }
        }
        else
        {
            $msg = 'failure:您不是店家';
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /*
     *通过店家id获取店铺所有订单商品
     * @param int $user_id 用户id
     * @access public
     */
    public function getShopOrdergoodsByUserId(){
    	$user_id = request_int('user_id');//店员或店家用户id
        $order_id = request_row('order_id');
        $data = [];
        $shop_info = $this->shopInfoModel->getOneByWhere(['user_id'=>$user_id]);
        //如果没有店铺信息，说明是店员登录，返回
        if($shop_info)
        {
            $status = 200;
            $cond_rows['shop_id'] = $shop_info['shop_id'];
            $data = $this->orderGoodsModel->getByWhere(array("order_id:IN" => $order_id,'goods_price:!='=>'0'));
            $order_info = $this->orderBaseModel->getByWhere(["order_id:IN"=>$order_id]);
            if($data)
            {
                $data = array_values($data);
                foreach($data as $key=>$value)
                {
                	foreach ($order_info as $k => $v) {
                		if ($v['order_id'] == $value['order_id']) {
                			$data[$key]['chain_id'] = $v['chain_id'];
                		}
                	}
                    $data[$key]['goods_spec'] = implode(',',$value['order_spec_info']);
                    $data[$key]['shop_name'] = $shop_info['shop_name'];
                }
                $msg = 'success';
            }
            else
            {
                $msg = 'success:店铺还没有订单商品';
            }
        }
        else
        {
            $msg = 'failure:您不是店家';
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }
        
	//判断此会员是否存在
	public function getCustomer()
	{
		$skey = request_string('skey');
		$cond_row['user_account'] = $skey;
		$data = $this->userBaseModel->getOneBywhere($cond_row);
		if(!empty($skey))
		{
			if(!empty($data))
			{
				$status=200;
			}
			else
			{
				$status=100;
			}
		}
		else
		{
                     $status=300;
		}
		 $msg='success';
		$this->data->addBody(-140, $data, $msg, $status);
	}

	//返回会员的钱
	public function getcontactInfo()
	{
		//会员的钱
		$key                 = Yf_Registry::get('shop_api_key');
		$formvars            = array();
		$formvars['user_id'] = request_int('buid');
		$formvars['app_id']  = Yf_Registry::get('shop_app_id');

		$money_row = get_url_with_encrypt($key, sprintf('%sindex.php?ctl=Api_User_Info&met=getUserResourceInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);

		$this->data->addBody(-140, $money_row['data'], $money_row['msg'],  $money_row['status']);
	}
	
	//报表模板
	public function reportForm()
	{
		$period = request_string("period");
		$now = date('Y-m-d',time());
		switch($period)
		{
			case "daily":$begin = $now;break;
			case "weekly":$begin = date('Y-m-d',strtotime('-6 day'));break;
			case "monthly":$begin = date('Y-m-d',strtotime('-29 day'));break;
		}

		$data['begin'] = $begin;
		$msg = "success";
		$status = "200";
	    $this->data->addBody(-140, $data,$msg,$status);
	}
	
	/**
	 * 获取订单报表信息
	 */
	public function getOrderReport()
	{
		$cond_row  = array();
		$order_row = array();
		$page = request_int('page',0);
		$rows = request_int('rows',50);
		//获取时间按照时间搜索订单
		$beginDate  = request_string("beginDate");
		$endDate 	= request_string("endDate");
		$cond_row['shop_id']	   = request_int('shop_id');
		$cond_row['order_from']	   = Order_BaseModel::FROM_WEBPOS;
		$cond_row['order_date:>='] = $beginDate;
		$cond_row['order_date:<='] = $endDate;
		$order_row['order_create_time'] = 'DESC';
		$data = $this->Order_BaseModel->listByWhere($cond_row, $order_row, $page, $rows);
                
        $Order_GoodsModel = new Order_GoodsModel();
		$order_id_row     = array_column($data['items'], 'order_id');
		$order_buyer_row     = array_column($data['items'], 'buyer_user_name', 'order_id');
		$order_goods_list = $Order_GoodsModel->listByWhere(array('order_id:IN' => $order_id_row),array('order_goods_id'=>'DESC'));

		if ($order_goods_list)
		{
			foreach($order_goods_list['items'] as $key=>$value)
			{
				$order_goods_list['items'][$key]['order_spec_desc'] = json_decode($value['order_spec_info'],true);
				$order_goods_list['items'][$key]['buyer_user_name'] = $order_buyer_row[$value['order_id']];
			}
			$msg    = __('success');
			$status = 200;
		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}

		$this->data->addBody(-140, $order_goods_list, $msg, $status);
	}

	//生成订单编号
	public function generateNo()
	{
		$data 					= array();
		$data['status'] 		= 200;
		$data['msg'] 			= 'success';
		$data['data']['billNo'] = "XS".request_int('billDate');
		$msg 					= "success";
		$this->data->addBody(-140, $data, $msg, 200);
	}

	//返回狗屁数据，不知道有啥鸟用
	public function listBySelected()
	{
		echo '{"status":200,"msg":"success","data":{"result":[{"advanceDays":0,"amount":5,"barCode":"","categoryName":"","currentQty":"","delete":false,"discountRate":0,"id":129459311923097,"invSkus":[],"isSerNum":0,"isWarranty":0,"josl":"","locationId":0,"locationName":"","locationNo":"","name":"qunhong","nearPrice":10,"number":"00000000","pinYin":"","purPrice":1,"quantity":2,"remark":"","retailPrice":0,"safeDays":0,"salePrice":10,"salePrice1":0,"salePrice2":0,"salePrice3":0,"skuAssistId":"","skuBarCode":"","skuClassId":0,"skuId":0,"skuName":"","skuNumber":"","spec":"1g","unitCost":2.5,"unitId":0,"unitName":""}]}}';
		die;
	}

	//保存数据，生成订单，
	public function addOrder()
	{
		$shop_id 				= request_int('shop_id');
		$Shop_BaseModel 		= new Shop_BaseModel();
		$shop_info 				= $Shop_BaseModel->getShopBaseInfo($shop_id);

        //订单信息部分
		$buyer_id 				= request_int('buId');				//买家ID
		$salesId 				= request_int('salesId');			//销售ID
		$totalQty 				= request_int('totalQty');			//总的数量
		$totalDiscount 			= request_float('totalDiscount');	//总的折扣额
		$totalAmount 			= request_float('totalAmount');		//总的金额
		$des 					= request_string('description');	//总订单描述
		$disRate 				= request_float('disRate');			//优惠率
		$disAmount 				= request_float('disAmount');		//优惠金额
		$amount 				= request_float('amount');			//最后金额
		$cash 					= request_float('cash');			//账户余额
		$password 				= request_string('password');		//支付密码
		$paymentMethod 			= request_int('paymentMethod');		//支付方式
		$order_payment_amount 	= $amount;
		$order_goods 			= request_row('entries');			//商品列表

		//根据支付方式判断用户账户余额是否充足，决定是否进行下单操作
		switch($paymentMethod)
		{
			case "1":$payment_name = '现金支付';break;
			case "2":$payment_name = '余额支付';break;
			case "3":$payment_name = '微信支付';break;
			case "4":$payment_name = '支付宝支付';break;
		}

		//判断支付方式和余额
		//账户余额支付
		if($paymentMethod==2) //账户余额支付，和线上支付一样，卖家金额每月结算一次
		{
			$errorMsg = '';
			//获取买家支付账户资产信息
			$key      						= Yf_Registry::get('shop_api_key');
			$url         					= Yf_Registry::get('paycenter_api_url');
			$shop_app_id 					= Yf_Registry::get('shop_app_id');

			$formvars 						= array();
			$formvars['user_id']     		= $buyer_id;
			$formvars['user_pay_passwd'] 	= request_string('password');      //支付密码；
			$formvars['money'] 				= request_float('amount');         //订单支付金额
			$formvars['app_id']        		= $shop_app_id;
			$formvars['from_app_id'] 		= $shop_app_id;

			fb($formvars);

			$rs = get_url_with_encrypt($key, sprintf('%sindex.php?ctl=Api_Pay_Pay&met=getPayUserInfo&typ=json', $url), $formvars);
			fb($rs);

			if($rs['status'] == 250)
			{
				$errorMsg = $rs['msg'];
			}

			if(!empty($errorMsg))
			{
				$body_data_rows = array();
				$body_data_rows['status'] = 0;
				$body_data_rows['msg'] = $errorMsg;
				$body_data_rows['data'] = array();
				$pro_data_rows = array('cmd_id'=>-140) + $body_data_rows;
				header('Content-type: application/json');
				echo json_encode($pro_data_rows);
				die;
			}
		}

		$order_goods_base_row   = array();
		foreach($order_goods as $key=>$value)
		{
			$goods_id = $value['invId'];
			$order_goods_base_row[$goods_id]['goods_id'] 					= $value['invId'];     	//商品goods_id
			$order_goods_base_row[$goods_id]['goods_price'] 				= $value['price'];  	//商品价格
			$order_goods_base_row[$goods_id]['order_goods_num'] 			= $value['qty'];		//商品数量
			$order_goods_base_row[$goods_id]['order_goods_amount'] 			= $value['amount'];     //实付金额
			$order_goods_base_row[$goods_id]['order_goods_payment_amount'] 	= $value['amount'];     //实付金额
		}

		$goods_base_id_row = array_column($order_goods,'invId');
		$goods_base_rows   = $this->Goods_BaseModel->getGoodsListByGoodId($goods_base_id_row);
		$goods_error       = "";
		foreach($order_goods_base_row as $key=>$value)
		{
			if(in_array($value['goods_id'],array_keys($goods_base_rows)))
			{
				//获取商品Common信息
				$Goods_CommonModel = new Goods_CommonModel();
				$goods_common      = $Goods_CommonModel->getOne($goods_base_rows[$key]['common_id']);
				if (empty($goods_common))
				{
					return null;
				}

				//商品规格信息
				$spec_name  = $goods_common['common_spec_name'];
				$spec_value = $goods_common['common_spec_value'];

				if (is_array($spec_name) && $spec_name && $goods_base_rows[$key]['goods_spec'])
				{
					$goods_spec = current($goods_base_rows[$key]['goods_spec']);

					foreach ($goods_spec as $gpk => $gbv)
					{
						foreach ($spec_value as $svk => $svv)
						{
							$pk = array_search($gbv, $svv);

							if ($pk)
							{
								$goods_base_rows[$key]['spec'][] = $spec_name[$svk] . ":" . $gbv;
							}
						}
					}

				}
				else
				{
					$goods_base_rows[$key]['spec'] = array();
				}

				$order_goods_base_row[$key]['order_goods_amount']		= $goods_base_rows[$key]['goods_price']*$value['order_goods_num'];    //商品原价
				$order_goods_base_row[$key]['order_goods_discount_fee'] = $goods_base_rows[$key]['goods_price']*$value['order_goods_num'] - $value['order_goods_amount'];//商品优惠金额
				$order_goods_base_row[$key]['goods_stock']  			= $goods_base_rows[$key]['goods_stock'];    //商品库存
				$order_goods_base_row[$key]['order_spec_info']  		= $goods_base_rows[$key]['spec'];     		//商品规格
				$order_goods_base_row[$key]['common_id'] 				= $goods_base_rows[$key]['common_id'];      //商品common_id
				$order_goods_base_row[$key]['goods_name'] 				= $goods_base_rows[$key]['goods_name'];		//商品名称
				$order_goods_base_row[$key]['goods_class_id'] 			= $goods_base_rows[$key]['cat_id']; 		//商品分类
				$order_goods_base_row[$key]['goods_image'] 				= $goods_base_rows[$key]['goods_image'];    //商品图片
			}
			else
			{
				unset($order_goods_base_row[$key]);
			}
		}

		$order = array();
		$uprice = 0;
		$buyer = $buyer_id;
		$inorder = "";

		//获取买家的用户信息
		$User_InfoModel 		= new User_InfoModel();
		$user_info      		= $User_InfoModel->getOne($buyer_id);
		if($user_info)
		{
			$buyer_name = $user_info['user_name'];
		}
		else
		{
			$buyer_error = __('用户信息不存在！');
		}

		//判断各个商品的库存
		if(!empty($datas['entries']))
		{
			$str='';
			foreach($datas['entries'] as $k=>$v)
			{
				if($v['goods_stock']<$v['qty'])
				{
					$str.="商品：$v[goodsName]【$v[skuName]】库存不足（$v[goods_stock]）";
				}
			}
			if(!empty($str))
			{
				$body_data_rows = array();
				$body_data_rows['status'] = 0;
				$body_data_rows['msg'] = $str;
				$body_data_rows['data'] = array();
				$pro_data_rows = array('cmd_id'=>-140) + $body_data_rows;
				header('Content-type: application/json');
				echo json_encode($pro_data_rows);
				//die;
			}
		}

		//END 2016-01-08

		$uprice  = 0;
		$inorder = '';
		$utrade_title = '';	//商品名称 - 标题

        //开始写入订单表
        $Number_SeqModel = new Number_SeqModel();
        $prefix          = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));
        $order_number    = $Number_SeqModel->createSeq($prefix);
        $order_id 		 = sprintf('%s-%s-%s-%s', 'DD', $shop_info['user_id'], $shop_id, $order_number);

		//开启事物
		$this->Order_BaseModel->sql->startTransactionDb();

        $order_row                           	= array();
        $order_row['order_id']               	= $order_id;
        $order_row['shop_id']                	= $shop_info['shop_id'];
        $order_row['shop_name']              	= $shop_info['shop_name'];
        $order_row['buyer_user_id']          	= $buyer_id;
        $order_row['buyer_user_name']        	= $buyer_name;
        $order_row['seller_user_id']         	= $shop_info['user_id'];	//卖家ID
        $order_row['seller_user_name']       	= $shop_info['user_name'];	//卖家用户名称
        $order_row['order_date']             	= date('Y-m-d');
        $order_row['order_create_time']      	= get_date_time();
		$order_row['order_finished_time']		= get_date_time();
        $order_row['order_receiver_name']    	= "";
        $order_row['order_receiver_address'] 	= "";
        $order_row['order_receiver_contact'] 	= "";
        $order_row['order_invoice']          	= __('不需要发票');
        $order_row['order_invoice_id']	     	= 0;
        $order_row['order_from']	         	= Order_BaseModel::FROM_WEBPOS;  //来源于webpos线下下单
        $order_row['order_goods_amount']     	= array_sum(array_column($order_goods_base_row, 'order_goods_amount')); //订单商品总金额
        $order_row['order_payment_amount']   	= $order_payment_amount;	     // 店铺商品价格 + 运费价格 + 加价购商品价格   - 代金券价格
        $order_row['order_discount_fee']     	= $order_row['order_goods_amount'] - $order_row['order_payment_amount']; //折扣金额  店铺优惠价格 + 会员折扣价格  +  代金券价格
        $order_row['order_point_fee']        	= 0;    					    //买家使用积分
        $order_row['order_shipping_fee']     	= 0;						   //运费价格
        $order_row['order_message']          	= ''; 						   //订单备注信息

        if($paymentMethod == 3 || $paymentMethod == 4)
		{
            $order_row['order_status']           =  Order_StateModel::ORDER_WAIT_PAY; //如果是微信扫码支付或支付宝支付，订单状态为等待付款
        }
		else
		{
            $order_row['order_status']           = Order_StateModel::ORDER_FINISH; 	//订单状态，订单完成，针对账户余额支付和现金支付
        }

        $order_row['order_points_add']       = 0;    								//订单赠送的积分
        $order_row['voucher_id']             = 0;    								//代金券id
        $order_row['voucher_price']          = 0;    								//代金券面额
        $order_row['voucher_code']           = 0;    								//代金券编码
        $order_row['order_commission_fee']   = 0;									//佣金金额
        $order_row['order_is_virtual']       = 0;    								//1-虚拟订单 0-实物订单
        $order_row['order_shop_benefit']     = '';  								//店铺优惠
        $order_row['payment_id']			 = 3;									//支付方式
        $order_row['payment_name']			 = $payment_name;						//支付方式名称
        $flag = $this->Order_BaseModel->addBase($order_row);						//添加订单基本信息

		//写入订单商品表
		foreach ($order_goods_base_row as $k => $v)
		{
			//计算商品的优惠
			$order_goods_row                                  = array();
			$order_goods_row['order_id']                      = $order_id;
			$order_goods_row['goods_id']                      = $v['goods_id'];
			$order_goods_row['common_id']                     = $v['common_id'];
			$order_goods_row['buyer_user_id']                 = $buyer_id;
			$order_goods_row['goods_name']                    = $v['goods_name'];
			$order_goods_row['goods_class_id']                = $v['goods_class_id'];
			$order_goods_row['order_spec_info']               = $v['order_spec_info'];
			$order_goods_row['goods_price']                   = $v['goods_price'];
			$order_goods_row['order_goods_num']               = $v['order_goods_num'];
			$order_goods_row['goods_image']                   = $v['goods_image'];
			$order_goods_row['order_goods_amount']            = $v['order_goods_amount'];
			$order_goods_row['order_goods_payment_amount']    = $v['order_goods_payment_amount'];
			$order_goods_row['order_goods_discount_fee']      = $v['order_goods_discount_fee']; //优惠金额，即便宜了多少钱
			$order_goods_row['order_goods_adjust_fee']        = 0;    							//手工调整金额
			$order_goods_row['order_goods_point_fee']         = 0;    							//积分费用
			$order_goods_row['order_goods_commission']        = @$v['commission'];    			//商品佣金
			$order_goods_row['shop_id']                       = $shop_id;
			$order_goods_row['order_goods_status']            = ($paymentMethod==1 || $paymentMethod==2)?Order_StateModel::ORDER_FINISH:Order_StateModel::ORDER_WAIT_PAY;
			$order_goods_row['order_goods_evaluation_status'] = 0;  							//0未评价 1已评价
			$order_goods_row['order_goods_benefit']           = '';
			$order_goods_row['order_goods_time']              = get_date_time();
			$order_goods_row['order_goods_finish_time']		  = get_date_time();

			$flag2 = $this->Order_GoodsModel->addGoods($order_goods_row);

			$flag3 = $this->Goods_BaseModel->delStock($v['goods_id'], $v['goods_num']);			//修改商品库存信息
			$trade_title = $v['goods_name'];
		}


        /*
        *  经验与成长值
        */
        $user_points        = Web_ConfigModel::value("points_recharge");//订单每多少获取多少积分
        $user_points_amount = Web_ConfigModel::value("points_order");//订单每多少获取多少积分

        if ($order_payment_amount / $user_points > $user_points_amount)
        {
            $user_points = floor($order_payment_amount / $user_points);
        }
        else
        {
            $user_points = $user_points_amount;
        }


        $user_grade        = Web_ConfigModel::value("grade_recharge");	//订单每多少获取多少积分
        $user_grade_amount = Web_ConfigModel::value("grade_order");		//订单每多少获取多少成长值

        if ($order_payment_amount / $user_grade > $user_grade_amount)
        {
            $user_grade = floor($order_payment_amount / $user_grade);
        }
        else
        {
            $user_grade = $user_grade_amount;
        }

        $User_ResourceModel = new User_ResourceModel();
        //获取积分经验值
        $ce = $User_ResourceModel->getResource($buyer_id);

        $resource_row['user_points'] = $ce[$buyer_id]['user_points'] * 1 + $user_points * 1;
        $resource_row['user_growth'] = $ce[$buyer_id]['user_growth'] * 1 + $user_grade * 1;
        $res_flag = $User_ResourceModel->editResource($buyer_id, $resource_row);

        $User_GradeModel = new User_GradeModel;
        //升级判断
        $res_flag = $User_GradeModel->upGrade($buyer_id, $resource_row['user_growth']);

        //积分
        $points_row['user_id']           = $buyer_id;
        $points_row['user_name']         = $buyer_name;
        $points_row['class_id']          = Points_LogModel::ONBUY;
        $points_row['points_log_points'] = $user_points;
        $points_row['points_log_time']   = get_date_time();
        $points_row['points_log_desc']   = '确认收货';
        $points_row['points_log_flag']   = 'confirmorder';
        $Points_LogModel = new Points_LogModel();
        $Points_LogModel->addLog($points_row);

        //成长值
        $grade_row['user_id']         = $buyer_id;
        $grade_row['user_name']       = $buyer_name;
        $grade_row['class_id']        = Grade_LogModel::ONBUY;
        $grade_row['grade_log_grade'] = $user_grade;
        $grade_row['grade_log_time']  = get_date_time();
        $grade_row['grade_log_desc']  = '确认收货';
        $grade_row['grade_log_flag']  = 'confirmorder';
        $Grade_LogModel = new Grade_LogModel();
        $Grade_LogModel->addLog($grade_row);

		if($paymentMethod != 1)    //不是现金支付
		{
			//支付中心生成订单
			$key      							= Yf_Registry::get('shop_api_key');
			$url         						= Yf_Registry::get('paycenter_api_url');
			$shop_app_id 						= Yf_Registry::get('shop_app_id');

			$formvars 							= array();
			$formvars['app_id']					= $shop_app_id;
			$formvars['from_app_id'] 			= Yf_Registry::get('shop_app_id');
			$formvars['consume_trade_id']     	= $order_row['order_id'];
			$formvars['order_id']             	= $order_row['order_id'];
			$formvars['buy_id']               	= $buyer_id;
			$formvars['buyer_name'] 		   	= $buyer_name;
			$formvars['seller_id']            	= $order_row['seller_user_id'];
			$formvars['seller_name']		   	= $order_row['seller_user_name'];
			$formvars['order_state_id']       	= $order_row['order_status'];
			$formvars['order_payment_amount'] 	= $order_row['order_payment_amount'];
			$formvars['trade_remark']         	= $order_row['order_message'];
			$formvars['trade_create_time']    	= $order_row['order_create_time'];
			$formvars['trade_title']			= $trade_title;		//商品名称 - 标题
			fb($formvars);

			//支付中心添加交易流水
			$rsc = get_url_with_encrypt($key, sprintf('%sindex.php?ctl=Api_Pay_Pay&met=addConsumeTrade&typ=json',$url), $formvars);
			fb("合并支付返回的结果");
			//将合并支付单号插入数据库
			if($rsc['status'] == 200)
			{
				$this->Order_BaseModel->editBase($order_id,array('payment_number' => $rsc['data']['union_order']));
			}

			$uprice += $order_row['order_payment_amount'];
			$inorder .= $order_id . ',';

			$utrade_title .=$trade_title;

			//生成合并支付订单
			$key      					= Yf_Registry::get('shop_api_key');
			$url         				= Yf_Registry::get('paycenter_api_url');
			$shop_app_id 				= Yf_Registry::get('shop_app_id');

			$formvars 		         	= array();
			$formvars['inorder']     	= $inorder;
			$formvars['uprice']      	= $uprice;
			$formvars['buyer']       	= $buyer_id;
			$formvars['trade_title'] 	= $utrade_title;
			$formvars['buyer_name']  	= $buyer_name;
			$formvars['app_id']      	= $shop_app_id;
			$formvars['from_app_id'] 	= Yf_Registry::get('shop_app_id');

			fb($formvars);
			//添加合并订单
			$rs = get_url_with_encrypt($key, sprintf('%sindex.php?ctl=Api_Pay_Pay&met=addUnionOrder&typ=json', $url), $formvars);

			$data = array();

			if ($rs['status'] == 200)
			{
				$uorder = $rs['data']['uorder'];   //合并订单号

				if($paymentMethod == 2) //账户余额支付
				{
					$key       =   Yf_Registry::get('shop_api_key');
					$formvars  = array();
					$formvars['app_id'] =   Yf_Registry::get('shop_app_id');
					$formvars['trade_id'] = $uorder;
					$formvars['union_money_pay_amount']  =  $order_row['order_payment_amount'];

					$pay_res = get_url_with_encrypt($key, sprintf('%sindex.php?ctl=Api_Pay_Pay&met=money&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);
					fb($pay_res);
				}
				elseif($paymentMethod==3)	//微信支付
				{

					$key      					= Yf_Registry::get('shop_api_key');
					$url         				= Yf_Registry::get('paycenter_api_url');
					$shop_app_id 				= Yf_Registry::get('shop_app_id');

					$formvars 					= array();
					$formvars['uorder_id'] 		= $uorder;
					$formvars['card_payway'] 	= "false";
					$formvars['money_payway'] 	= "false";
					$formvars['online_payway'] 	= "wx_native";
					$formvars['app_id']        	= $shop_app_id;  //webpos可能更改
					$formvars['from_app_id'] 	= $shop_app_id;

					fb($formvars);

					$pay_res = get_url_with_encrypt($key, sprintf('%sindex.php?ctl=Api_Pay_Pay&met=checkPayWay&typ=json', $url), $formvars);
					fb($pay_res);
				}
			}
			else
			{
				$uorder = '';
			}
		}
		else        //现金支付，返回空的合并订单号
		{
			$uorder = '';
		}


		if ($flag && $this->Order_BaseModel->sql->commitDb())
		{
			$status = 200;
			$msg    = __('success');
		}
		else
		{
			$this->Order_BaseModel->sql->rollBackDb();
			$m      = $this->Order_BaseModel->msg->getMessages();
			$msg    = $m ? $m[0] : __('failure');
			$status = 250;
		}

		$data['id'] 		= $order_id;
		$data['order_id'] 	= $order_id;
		$data['uorder'] 	= $uorder;
//        var_dump('end');
		$this->data->addBody(-140, $data, $msg, $status);
	}


	/*更新订单信息
	* 订单详情信息
	*/
	public function updateOrderInfo()
	{
		$order_id = request_string('id');

		$data_order_row = $this->Order_BaseModel->getOrderDetail($order_id);
		if($data_order_row)
		{
			$data_order_row['id']               = $data_order_row['order_id'];
			$data_order_row['contactName']      = $data_order_row['buyer_user_name'];  //买家用户名
			$data_order_row['salesId']          = $data_order_row['seller_user_id'];
			$data_order_row['shopId']           = $data_order_row['shop_id'];
			$data_order_row['totalTaxAmount']   = 0;					//价税合计

			$data_order_row['disAmount']        = $data_order_row['order_goods_amount'] - $data_order_row['order_payment_amount'];     //优惠金额
			$data_order_row['totalDiscount']    = $data_order_row['order_goods_amount'] - $data_order_row['order_payment_amount'];    //总折扣
            $data_order_row['disRate']          = $data_order_row[''];    	//优惠率
			$data_order_row['totalAmount']      = $data_order_row['order_payment_amount'];	//订单总金额
			$data_order_row['amount']           = $data_order_row['order_goods_amount'];		//所有商品总价格
			$data_order_row['date']             = date("Y-m-d",strtotime($data_order_row['order_create_time']));
			$data_order_row['modifyTime']       = $data_order_row['order_create_time'];

			$data_order_row['status'] = 'view';
			$data_order_row['checked'] = 1;

			$data=$data_order_row;
            $entries = array();

            if($data_order_row['goods_list'])
            {
                foreach($data_order_row['goods_list'] as $key=>$value)
                {
                    $entries[$key]['goods_id'] 		= $value['goods_id'];
                    $entries[$key]['goods_code'] 	= $value['goods_code'];
					$entries[$key]['goods'] 		= $value['goods_name'];
					$entries[$key]['goods_name'] 	= $value['goods_name'];
					$entries[$key]['cat_id'] 		= $value['goods_class_id'];
					$entries[$key]['pic'] 			= $value['goods_image'];
					$entries[$key]['price'] 		= $value['goods_price'];
					$entries[$key]['amount'] 		= $value['order_goods_amount'];
                    $entries[$key]['qty'] 			= $value['order_goods_num'];
                    $entries[$key]['deduction'] 	= $value['order_goods_discount_fee'];
                    $entries[$key]['discountRate'] 	= ($value['order_goods_amount']/$value['order_goods_payment_amount'])*100;
                    $entries[$key]['skuName'] 		= $value['order_spec_info'];
                    $entries[$key]['shop_id'] 		= $value['shop_id'];
                    $entries[$key]['shop_name'] 	= $data_order_row['shop_name'];
                }
            }
            $data['entries'] = $entries;
            $data['totalQty'] = array_sum(array_column($data_order_row['goods_list'],'order_goods_num'));

			$this->data->addBody(-140, $data, 'success', 200);
		}

	}

	/*订单退款*/
	public function orderReturn()
	{
        $Order_StateModel = new Order_StateModel();
        $order_id = request_string("order_id");      //退款订单号
        $userId = request_string("user_id");      //买家id
        $goods_ids = request_row("goods_id");         //退货订单商品id
        $goods_nums = request_row("goods_num");         //退货订单商品数量
        $flag2 = true;
        $Number_SeqModel = new Number_SeqModel();
        $Goods_CommonModel = new Goods_CommonModel();
        $shopBase = new Shop_BaseModel();
        $message = new MessageModel();
        $order = $this->orderBaseModel->getOne($order_id);
        $goods = $this->orderGoodsModel->getByWhere(['order_id'=>$order_id,'goods_id:IN'=>$goods_ids]);
        if ($goods) {
	        foreach ($goods as $k => $v) {
		        $prefix = sprintf('%s-%s-', Yf_Registry ::get('shop_app_id'), date('YmdHis'));
		        $return_number = $Number_SeqModel->createSeq($prefix);
		        $return_id = sprintf('%s-%s-%s-%s', 'TD', $userId, 0, $return_number);
		        //$field['return_message'] = request_string("return_message");    //“退款/退货”说明
		        //$field['return_reason_id'] = request_string("return_reason_id");  //“退款/退货”原因
		        $field['return_code'] = $return_id;                             //退货单号
		        $goods_id = $v['order_goods_id'];						//退货订单商品id
		        foreach ($goods_ids as $kg => $vg) {
		        	if ($vg == $v['goods_id']) {
		        		foreach ($goods_nums as $kn => $vn) {
		        			$nums = $vn; 		//“退款/退货”数量
		        		}
		        	}
		        }
		                                     
		        $field['order_goods_num'] = $nums;                          //“退款/退货”数量
		        //$reason = $this->orderReturnReasonModel->getOne($field['return_reason_id']);
		        //$field['return_reason'] = $reason['order_return_reason_content'];   //“退款/退货”原因
		        
		        //$goods = $this->orderGoodsModel->getOne($goods_id);
		        $field['order_number'] = $v['order_id'];            //订单号
		        $field['order_goods_id'] = $goods_id;                      //订单商品id
		        $field['order_goods_name'] = $v['goods_name'];         //退货商品名称
		        $field['order_goods_price'] = $v['goods_price'];        //商品单价
		        $field['order_goods_pic'] = $v['goods_image'];        //商品图片
		        $field['order_amount'] = $order['order_payment_amount'];     //订单实际支付金额
		        $field['seller_user_id'] = $order['shop_id'];               //店铺id
		        $field['seller_user_account'] = $order['shop_name'];            //店铺名称
		        $field['buyer_user_id'] = $order['buyer_user_id'];        //买家id
		        $field['buyer_user_account'] = $order['buyer_user_name'];     //买家名称
		        $field['return_add_time'] = get_date_time();                 //退款、退货申请提交时间
		        $field['order_is_virtual'] = $order['order_is_virtual'];     //该笔订单是否为虚拟订单
		        //如果传递过来的订单号和根据商品id查到的订单号不符，报错
		        if ($v['order_id'] != $order_id) {
		            $flag2 = false;
		        }
		        
	            switch ($order['order_status']) {
	                case Order_StateModel::ORDER_PAYED:
	                    $field['return_type'] = Order_ReturnModel::RETURN_TYPE_ORDER; //退款
	                    break;
	                case Order_StateModel::ORDER_FINISH:
	                    $field['return_type'] = Order_ReturnModel::RETURN_TYPE_GOODS; //退货
	                    break;
	            }
		        
		        //如果是货到付款，确认收货（付款）后才能退款
		        if ($order['payment_id'] == PaymentChannlModel::PAY_CONFIRM) {
		            if ($order['order_status'] < Order_StateModel::ORDER_RECEIVED) {
		                $flag2 = false;
		            }
		        }
		        //退款(货到付款只支持退货，不支持退款)
		        if ($v['order_goods_status'] == Order_StateModel::ORDER_PAYED && $order['payment_id'] != PaymentChannlModel::PAY_CONFIRM) {
		            //白条支付不支持退款和退货
		            if (strstr($order['payment_name'], '白条支付')) {
		                $flag2 = false;
		            }
		            $field['return_goods_return'] = 0;      //是否需要退货  0-不需要  1-需要
		            $return = $this->orderReturnModel->getByWhere(array(
		                'order_goods_id' => $goods_id,
		                'return_type' => Order_ReturnModel::RETURN_TYPE_ORDER,
		                'return_state:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS
		            ));
		        }
		        //退货
		        if ($v['order_goods_status'] == Order_StateModel::ORDER_FINISH) {
		            if (strstr($order['payment_name'], '白条支付')) {
		                $flag2 = false;
		            }
		            $field['return_goods_return'] = 1;    //需要退货
		            //查询是否存在该订单商品的退货申请信息，且该申请未被卖家拒绝，以此判断是否重新提交退货申请
		            //只有以前没有提交过该商品的退货申请，或者提交申请未被卖家拒绝的情况下，才可以提交退货申请
		            $return = $this->orderReturnModel->getByWhere(array(
		                'order_goods_id' => $goods_id,
		                'return_type' => Order_ReturnModel::RETURN_TYPE_GOODS,
		                'return_shop_handle:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS
		            ));
		        }
		        /* 计算“退款/退货”商品和订单的各种金额 */
		        //判断这件“退款/退货”商品是否还有可退数量（退款，退货都会退还商品数量）
		        $this_goods_return = $this->orderReturnModel->getByWhere(array(
		            'order_goods_id' => $goods_id,
		            'return_shop_handle:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS
		        ));
		        //“退款/退货”商品总的退还件数
		        $this_goods_return_num = array_sum(array_column($this_goods_return, 'order_goods_num'));
		        //echo "this_goods_return_num:".$this_goods_return_num."<br>";
		        //“退款/退货”商品总的已退金额（包含正在审核中的金额）
		        $this_goods_return_cash = array_sum(array_column($this_goods_return, 'return_cash'));
		        //echo "this_goods_return_cash:".$this_goods_return_cash."<br>";
		        //“退款/退货”商品总的已退佣金金额（包含正在审核中的金额）
		        $this_goods_return_comission = array_sum(array_column($this_goods_return, 'return_commision_fee'));
		        //echo "this_goods_return_comission:".$this_goods_return_comission."<br>";
		        //echo $goods['order_goods_num']."<br>";
		        //如果该件商品的已退或正在退货的商品数量 = 该订单商品购买数量则无可退还商品数量
		        if ($this_goods_return_num == $v['order_goods_num']) {
		            $flag2 = false;
		        }
		        /*商品处于可退还状态下，判断订单还可退还的金额*/
		        //查找该笔订单已经进行过或正进行中的的退款，退货
		        $order_return = $this->orderReturnModel->getByWhere(array(
		            'order_number' => $order['order_id'],
		            'return_shop_handle:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS
		        ));
		        //订单已经退款退货的金额（包括与同意的退款和正在审核中的退款）
		        $order_return_cash = array_sum(array_column($order_return, 'return_cash'));
		        //echo "order_return_cash:".$order_return_cash."<br>";
		        //订单已经退还的商品数量
		        $order_return_num = array_sum(array_column($order_return, 'order_goods_num'));
		        //echo "order_return_num:".$order_return_cash."<br>";
		        //订单还可退还的金额 = 订单实付金额 - 订单已退金额
		        //如果没有发货，可以退运费
		        if (Order_StateModel::ORDER_PAYED == $order['order_status']) {
		            $order_can_return_cash = $order['order_payment_amount'] - $order_return_cash;
		        } else {
		            $order_can_return_cash = $order['order_payment_amount'] - $order_return_cash - $order['order_shipping_fee'];
		        }
		        //订单无可退金额，则报错
		        if ($order_can_return_cash <= 0) {
		            $flag2 = false;
		        }
		        //订单中所有商品数量
		        //'order_goods_amount:>'=>0 不包含赠品 ..
		        //sun
		        $order_goods = $this->orderGoodsModel->getByWhere(array('order_id' => $order_id, 'order_goods_amount:>' => 0));
		        $order_all_goods_num = array_sum(array_column($order_goods, 'order_goods_num'));
		        /*
		         * $data['order']['order_refund_amount'] 与 $order_return_cash 的区别
		         * $data['order']['order_refund_amount']：表示商家已经同意的退款金额
		         * $order_return_cash：表示买家已经申请的退款，除被商家拒绝的退款外，正在审核的退款也包含在内
		         */
		        //订单已退还的金额
		        $return_limit = $order['order_refund_amount'];
		        //echo "return_limit:".$return_limit."<br>";
		        //订单可退金额
		        $cash_limit = $order_can_return_cash;
		        //echo "cash_limit:".$cash_limit."<br>";
		        //订单可退商品数量
		        $goods_can_return_nums = $order_all_goods_num - $order_return_num;
		        //echo "goods_can_return_nums:".$goods_can_return_nums."<br>";
		        //该件商品可退的总金额
		        $return_goods_cash = $v['order_goods_amount'] - $this_goods_return_cash;
		        //echo "return_goods_cash:".$return_goods_cash."<br>";
		        //该件商品还可退还商品数量
		        $return_goods_nums = $v['order_goods_num'] - $this_goods_return_num;
		        //echo "return_goods_nums:".$return_goods_nums."<br>";
		        //如果商品退款/退货的数量则报错
		        if ($goods_can_return_nums < $nums) {
		            $flag2 = false;
		        }
		        //实际该件商品可退还的金额（有时可能包含运费）
		        //该件商品全部“退款/退货” //return_goods_nums
		        if ($goods_can_return_nums == $nums && Order_StateModel::ORDER_PAYED == $order['order_status']) {
		            //加上运费(未发货)
		            $return_cash = $return_goods_cash + $order['order_shipping_fee'];
		        } else {
		            $return_cash = floor($nums * $v['order_goods_payment_amount'] * 100) / 100;
		        }
		        //如果订单为已付款状态，并且所有商品都退款，则将运费退还
		        if (Order_StateModel::ORDER_PAYED == $order['order_status'] && $nums == $goods_can_return_nums) {
		            $return_cash = $cash_limit;
		        }
		        //自提商品
		        if ($order['order_status'] == Order_StateModel::ORDER_SELF_PICKUP) {
		            if ($nums == $goods_can_return_nums) {
		                $return_cash = $cash_limit;
		            } else {
		                $return_cash = floor($nums * $v['order_goods_payment_amount'] * 100) / 100;
		            }
		        }
		        /*退款退货走同样的流程。区别是：退款时可能会退还运费，退货不可能退还运费。*/
		        //如果买家申请的退货数量与最多可以申请的退货数量相同，并且退款金额=最多可申请退款金额
		        //退还佣金
		        if ($order['order_commission_fee'] && $v['order_goods_commission']) {
		            if ($nums == $goods_can_return_nums) {
		                $field['return_commision_fee'] = $v['order_goods_commission'] - $this_goods_return_comission;
		            } else {
		                $field['return_commision_fee'] = ($v['order_goods_commission'] / $v['order_goods_num']) * $nums;
		            }
		        }
		        //退还红包  order_rpt_return
		        //整笔订单金额已经退完，需要卖家退还平台红包
		        if ($order['order_rpt_price']) {
		            //整笔订单金额已经退完，需要卖家退还所有平台红包
		            if ($return_cash == $cash_limit) {
		                $field['return_rpt_cash'] = $order['order_rpt_price'] - $order['order_rpt_return'];
		            } else {
		                $field['return_rpt_cash'] = ($return_cash / ($order['order_payment_amount'] - $order['order_shipping_fee'])) * $order['order_rpt_price'];
		            }
		        }
		        if (empty($return) && ($return_cash > 0) && $flag2) {
		            $field['return_cash'] = $return_cash;
		            if ($order['buyer_user_id'] == $userId && !strstr($order['payment_name'], '白条支付')) {
		                $rs_row = array();
		                $this->orderReturnModel->sql->startTransactionDb();
		                //若果存在分销商采购单，添加退款订单，改变购物订单状态
		                $dist_order = $this->orderBaseModel->getByWhere(array('order_source_id' => $order_id));
		                if (!empty($dist_order)) {
		                    //判断该件商品是否是一件代发分销商品
		                    
		                    $goods_common = $Goods_CommonModel -> getOne($v['common_id']);
		                    if ($goods_common['product_is_behalf_delivery'] && $goods_common['common_parent_id']) {
		                        $field['behalf_deliver'] = Order_ReturnModel::BEHALF_DELIVER_SHOP;
		                    }
		                }
		                $add_flag = $this->orderReturnModel->addReturn($field, true);
		                check_rs($add_flag, $rs_row);
		                if (!empty($dist_order)) {
		                    foreach ($dist_order as $key => $value) {
		                        fb($value['order_id']);
		                        /*$dist_flag.$key = $this->addDistReturn($value['order_id'],$field['return_reason_id'],$field['return_message'],$goods_id);
		                        check_rs($dist_flag.$key, $rs_row);*/
		                        $key = $this -> addDistReturn($value['order_id'], $field['return_reason_id'], $field['return_message'], $goods_id, $add_flag);
		                        check_rs($key, $rs_row);
		                    }
		                }
		                //订单商品表中插入订单商品的“退款/退货”状态
		                if ($field['return_goods_return'] == 0) {
		                    //退款
		                    $goods_field['goods_return_status'] = Order_GoodsModel::REFUND_IN;
		                    $edit_flag = $this->orderGoodsModel->editGoods($goods_id, $goods_field);
		                    check_rs($edit_flag, $rs_row);
		                } else {
		                    //退货
		                    $goods_field['goods_refund_status'] = Order_GoodsModel::REFUND_IN;
		                    $edit_flag = $this->orderGoodsModel->editGoods($goods_id, $goods_field);
		                    check_rs($edit_flag, $rs_row);
		                }
		                $flag = is_ok($rs_row);
		                if ($flag && $this->orderReturnModel->sql->commitDb()) {
		                    $msg = __('success');
		                    $status = 200;
		                    
		                    $shop_detail = $shopBase -> getOne($field['seller_user_id']);
		                    $order_return_data = $this->orderReturnModel->getOneByWhere(['order_number' => $order_id]);
		                    
		                    if (!$field['return_goods_return']) {
		                        //退款提醒
		                        $message -> sendMessage('Refund reminder', $shop_detail['user_id'], $shop_detail['user_name'], $order_return_data['return_code'], $shop_name = null, 1, 1);
		                    } else {
		                        //退货提醒
		                        $message -> sendMessage('Return reminder', $shop_detail['user_id'], $shop_detail['user_name'], $order_return_data['return_code'], $shop_name = null, 1, 1);
		                    }
		                    $msg = __('退款/货成功，请稍候...');
		                    $status = 200;
		                } else {
		                    $this->orderReturnModel->sql->rollBackDb();
		                    $msg = __('退款/货失败，请稍候...');
		                    $status = 250;
		                }
		            } else {
		                $msg = __('请稍后~订单正在处理中...');
		                $status = 250;
		            }
		        } else {
		            $msg = __('同一商品,仅支持退款一次！');
		            $status = 250;
		        }
		        $data[] = $return_id;
	        	
	        }
        }else{
        	$data = array();
        	$status = 250;
        	$msg = __('订单中无此商品');
        }
        $this -> data -> addBody(-140, $data, $msg, $status);
	}

        
    public function addDistReturn($order_id, $return_reason_id, $return_message, $goods_parent_id, $order_return_id)
    {
        $ddorder_return = $this -> orderReturnModel -> getOne($order_return_id);
        $ddorder_goods_base = $this -> Order_GoodsModel -> getOne($ddorder_return['order_goods_id']);
        $order = $this -> Order_BaseModel -> getOne($order_id);
        $userId = $order['buyer_user_id'];
        //查找SP订单商品1.查找DD订单商品的goods_id。根据此goods_id查找出以此为goods_parent_id的goods_id。
        //				2.根据查找出的goods_id与SP的$order_id查找出订单商品信息。
        $Goods_BaseModel = new Goods_BaseModel();
        $source_goods_base = $Goods_BaseModel -> getOne($ddorder_goods_base['goods_id']);
        $goods = $this -> Order_GoodsModel -> getByWhere(array(
            'order_id' => $order_id,
            'goods_id' => $source_goods_base['goods_parent_id']));
        $goods = current($goods);
        //查找供应订单中的
        fb($goods_parent_id);
        fb($ddorder_return);
        fb($ddorder_goods_base);
        fb($order);
        fb($goods);
        //计算退款金额与退还佣金
        $nums = $ddorder_return['order_goods_num'];//退还商品数量
        //判断这件“退款/退货”商品是否还有可退数量（退款，退货都会退还商品数量）
        $this_goods_return = $this -> orderReturnModel -> getByWhere(array(
            'order_goods_id' => $goods['order_goods_id'],
            'return_state:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS
        ));
        //“退款/退货”商品总的退还件数
        $this_goods_return_num = array_sum(array_column($this_goods_return, 'order_goods_num'));
        //“退款/退货”商品总的已退金额（包含正在审核中的金额）
        $this_goods_return_cash = array_sum(array_column($this_goods_return, 'return_cash'));
        //“退款/退货”商品总的已退佣金金额（包含正在审核中的金额）
        $this_goods_return_comission = array_sum(array_column($this_goods_return, 'return_commision_fee'));
        //如果该件商品的已退或正在退货的商品数量 = 该订单商品购买数量则无可退还商品数量
        if ($this_goods_return_num == $goods['order_goods_num']) {
            return false;
        }
        /*商品处于可退还状态下，判断订单还可退还的金额*/
        //查找该笔订单已经进行过或正进行中的的退款，退货
        $order_return = $this -> orderReturnModel -> getByWhere(array(
            'order_number' => $order['order_id'],
            'return_state:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS
        ));
        //订单已经退款退货的金额（包括与同意的退款和正在审核中的退款）
        $order_return_cash = array_sum(array_column($order_return, 'return_cash'));
        //订单已经退还的商品数量
        $order_return_num = array_sum(array_column($order_return, 'order_goods_num'));
        //订单还可退还的金额 = 订单实付金额 - 订单已退金额
        //如果没有发货，可以退运费
        if (Order_StateModel::ORDER_PAYED == $order['order_status']) {
            $order_can_return_cash = $order['order_payment_amount'] - $order_return_cash;
        } else {
            $order_can_return_cash = $order['order_payment_amount'] - $order_return_cash - $order['order_shipping_fee'];
        }
        //订单无可退金额，则报错
        if ($order_can_return_cash <= 0) {
            return false;
        }
        //订单中所有商品数量
        //'order_goods_amount:>'=>0 不包含赠品 ..
        //sun
        $order_goods = $this -> orderGoodsModel -> getByWhere(array('order_id' => $order_id, 'order_goods_amount:>' => 0));
        $order_all_goods_num = array_sum(array_column($order_goods, 'order_goods_num'));
        /*
         * $data['order']['order_refund_amount'] 与 $order_return_cash 的区别
         * $data['order']['order_refund_amount']：表示商家已经同意的退款金额
         * $order_return_cash：表示买家已经申请的退款，除被商家拒绝的退款外，正在审核的退款也包含在内
         */
        //订单已退还的金额
        $return_limit = $order['order_refund_amount'];
        //订单可退金额
        $cash_limit = $order_can_return_cash;
        //订单可退商品数量
        $goods_can_return_nums = $order_all_goods_num - $order_return_num;
        //该件商品可退的总金额
        $return_goods_cash = $goods['order_goods_amount'] - $this_goods_return_cash;
        //该件商品还可退还商品数量
        $return_goods_nums = $goods['order_goods_num'] - $this_goods_return_num;
        //如果商品退款/退货的数量则报错
        if ($goods_can_return_nums < $nums) {
            return false;
        }
        //实际该件商品可退还的金额（有时可能包含运费）
        //该件商品全部“退款/退货” //return_goods_nums
        if ($goods_can_return_nums == $nums && Order_StateModel::ORDER_PAYED == $order['order_status']) {
            //加上运费(未发货)
            $return_cash = $return_goods_cash + $order['order_shipping_fee'];
        } else {
            $return_cash = floor($nums * $goods['order_goods_payment_amount'] * 100) / 100;
        }
        //如果订单为已付款状态，并且所有商品都退款，则将运费退还
        if (Order_StateModel::ORDER_PAYED == $order['order_status'] && $nums == $goods_can_return_nums) {
            $return_cash = $cash_limit;
        }
        //自提商品
        if ($order['order_status'] == Order_StateModel::ORDER_SELF_PICKUP) {
            if ($nums == $goods_can_return_nums) {
                $return_cash = $cash_limit;
            } else {
                $return_cash = floor($nums * $goods['order_goods_payment_amount'] * 100) / 100;
            }
        }
        /*退款退货走同样的流程。区别是：退款时可能会退还运费，退货不可能退还运费。*/
        //如果买家申请的退货数量与最多可以申请的退货数量相同，并且退款金额=最多可申请退款金额
        //退还佣金
        $return_commision_fee = 0;
        if ($order['order_commission_fee'] && $goods['order_goods_commission']) {
            if ($nums == $goods_can_return_nums) {
                $return_commision_fee = $goods['order_goods_commission'] - $this_goods_return_comission;
            } else {
                $return_commision_fee = ($goods['order_goods_commission'] / $goods['order_goods_num']) * $nums;
            }
        }
        fb($nums);
        fb($goods['order_goods_payment_amount']);
        fb($order['order_status']);
        fb($return_cash);
        fb($return_commision_fee);
        fb('return_cash');
        $goods_parent_base = $this -> orderGoodsModel -> getOne($goods_parent_id);
        //判断原订单是否是已完成订单，如果是已完成订单，则分销订单为退货。否则是退款
        if ($goods_parent_base['order_goods_status'] == Order_StateModel::ORDER_FINISH) {
            $order_field['order_return_status'] = 1;
            $cond_row['return_goods_return'] = 1;
            $cond_row['return_type'] = Order_ReturnModel::RETURN_TYPE_GOODS;   //退货类型 - 退货
        } else {
            $order_field['order_refund_status'] = 1;
            $cond_row['return_type'] = Order_ReturnModel::RETURN_TYPE_ORDER;   //退款
        }
        $re_rows = array();
        $order_field['order_refund_status'] = Order_BaseModel::REFUND_IN;
        $edit_flag = $this -> orderBaseModel -> editBase($order_id, $order_field);
        check_rs($edit_flag, $re_rows);
        //修改SP订单商品的退款/退货状态
        $goods_field = array();
        $goods_field['goods_return_status'] = $ddorder_goods_base['goods_return_status'];
        $goods_field['goods_refund_status'] = $ddorder_goods_base['goods_refund_status'];
        $edit_flag = $this -> orderGoodsModel -> editGoods($goods['order_goods_id'], $goods_field);
        check_rs($edit_flag, $re_rows);
        $Number_SeqModel = new Number_SeqModel();
        $prefix = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));
        $return_number = $Number_SeqModel -> createSeq($prefix);
        $return_id = sprintf('%s-%s-%s-%s', 'SPTD', $userId, 0, $return_number);
        $cond_row['order_number'] = $order['order_id'];
        $cond_row['return_message'] = $return_message;
        $cond_row['return_reason_id'] = $return_reason_id;     //退货原因id'
        $cond_row['return_code'] = $return_id;
        $cond_row['order_goods_num'] = $nums;
        $cond_row['return_reason'] = $this -> orderReturnReasonModel -> getOne($cond_row['return_reason_id']);
        $cond_row['order_goods_id'] = $goods['order_goods_id'];
        $cond_row['order_goods_name'] = $goods['goods_name'];
        $cond_row['order_goods_price'] = $goods['goods_price'];
        $cond_row['order_goods_pic'] = $goods['goods_image'];
        $cond_row['order_amount'] = $order['order_payment_amount'];
        $cond_row['seller_user_id'] = $order['shop_id'];
        $cond_row['seller_user_account'] = $order['shop_name'];
        $cond_row['buyer_user_id'] = $order['buyer_user_id'];
        $cond_row['buyer_user_account'] = $order['buyer_user_name'];
        $cond_row['return_add_time'] = get_date_time();
        $cond_row['order_is_virtual'] = $order['order_is_virtual'];
        $cond_row['return_type'] = $ddorder_return['return_type'];
        $cond_row['return_goods_return'] = $ddorder_return['return_goods_return'];
        $cond_row['return_commision_fee'] = $return_commision_fee;
        $cond_row['return_cash'] = $return_cash;
        $cond_row['behalf_deliver'] = Order_ReturnModel::BEHALF_DELIVER_DIST;
        fb($cond_row);
        $add_flag = $this -> orderReturnModel -> addReturn($cond_row, true);
        $flag = is_ok($re_rows);
        if ($flag && $add_flag) {
            return true;
        } else {
            return false;
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
        $return_ids = request_row("order_return_id");
        $shopUserId = request_int("seller_user_id");//卖家ID
        $payway = request_string("payway");
        $shop_info = $this->shopInfoModel->getOneByWhere(['user_id'=>$shopUserId]);
        $shopId = $shop_info['id'];
        foreach ($return_ids as $k => $v) {
        	$return = $this->orderReturnModel->getOneByWhere(['return_code'=>$v]);
        	$order_return_id = $return['order_return_id'];
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
	        
	        //判断该笔退款金额的订单是否已经结算
	        $Order_BaseModel = new Order_BaseModel();
	        $order_base = $Order_BaseModel->getOne($return['order_number']);

	        //判断该笔订单是否已经收货，如果没有收货的话，不扣除卖家资金。已确认收货则扣除卖家资金
	        if ($order_base['order_status'] == $Order_StateModel::ORDER_FINISH) {
	            $order_finish = false;
	            
	            //获取商家的账户资金资源
	            $key = Yf_Registry::get('shop_api_key');
	            $formvars = array();
	            $formvars['user_id'] = $shopUserId;
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
	            $settlement_last_info = $Order_SettlementModel->getLastSettlementByShopid($shopId, $return['order_is_virtual']);
	            
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
	        if ($return['seller_user_id'] == $shopId) {
	            
	            $shop_return_amount = sprintf("%.2f", $shop_return_amount);
	            $money = sprintf("%.2f", $money);
	            
	            if (($shop_return_amount <= $money) || $order_finish) {
	                //$data['return_shop_message'] = $return_shop_message;
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
	                    $formvars['user_id'] = $shopUserId;
	                    $formvars['user_name'] = $shop_info['user_name'];
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
	            //退款退货提醒
	            $message = new MessageModel();
	            $message->sendMessage('Refund return reminder', $return['buyer_user_id'], $return['buyer_user_account'], $order_id = NULL, $shop_name = NULL, 0, MessageModel::ORDER_MESSAGE);
	            
	            if ($return['return_type'] == Order_ReturnModel::RETURN_TYPE_ORDER || $return['return_type'] == Order_ReturnModel::RETURN_TYPE_VIRTUAL) {
	                if (Web_ConfigModel::value('Plugin_Fenxiao')) { //退货管理，同意退货（这一步不是收到货物）
	                    Fenxiao::getInstance()->cancelOrder($order_return_id);
	                }
	            }
	        } else {
	            $this->orderReturnModel->sql->rollBackDb();
	            $status = 250;
	            $msg = $msg ? $msg : __('failure2');
	        }

	        if ($return['return_state'] == Order_ReturnModel::RETURN_SELLER_GOODS) {
	            throw new Exception('已收到货物');
	        }
	        
	        //开启事物
	        $this->orderReturnModel->sql->startTransactionDb();
	        
	        $Order_BaseModel = new Order_BaseModel();
	        $order_base = $Order_BaseModel->getOne($return['order_number']);
	        
	        if ($return['seller_user_id'] == $shopId) {
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
	            } else {
	            	$this->orderReturnModel->sql->rollBackDb();
	                $status = 250;
	                $msg = __('failure3');
	            }
	        } else {
	            $status = 250;
	            $msg = __('failure4');
	        }    

	        //开启事务
	        $this->orderReturnModel->sql-> startTransactionDb();
	        //判断平台是否已经审核过
	        if ($return['return_state'] < Order_ReturnModel::RETURN_PLAT_PASS) {
	            //判断商家是否同意退款
	            if ($return['return_shop_handle'] == Order_ReturnModel::RETURN_SELLER_UNPASS) {
	                //不同意
	                $data = array();
	                //$data['return_platform_message'] = $return_platform_message;
	                $data['return_state'] = Order_ReturnModel::RETURN_PLAT_PASS;
	                $data['return_finish_time'] = get_date_time();
	                $rs_row = array();
	                $edit_flag = $this->orderReturnModel -> editReturn($order_return_id, $data);
	                check_rs($edit_flag, $rs_row);
	                //根据order_id查找订单信息
	                $order_base = $this->orderBaseModel -> getOne($return['order_number']);
	                //如果是分销商的进货单则同时退掉买家订单
	                if ($order_base['order_source_id']) {
	                    $dist_return = $this->orderReturnModel -> getOneByWhere(array('order_number' => $order_base['order_source_id'], 'return_type' => $return['return_type']));
	                    $this -> refuseDist($dist_return['order_return_id'], $data);
	                }
	                if ($return['return_goods_return']) {
	                    //商家拒绝退款退货3
	                    $goods_data['goods_refund_status'] = Order_GoodsModel::REFUND_REF;
	                    $edit_flag = $this->orderGoodsModel -> editGoods($return['order_goods_id'], $goods_data);
	                    check_rs($edit_flag, $rs_row);
	                } else {
	                    $goods_data['goods_return_status'] = Order_GoodsModel::REFUND_REF;
	                    $edit_flag = $this->orderGoodsModel -> editGoods($return['order_goods_id'], $goods_data);
	                    check_rs($edit_flag, $rs_row);
	                }
	            } else {
	                //同意
	                $data = array();
	                //$data['return_platform_message'] = $return_platform_message;
	                $data['return_state'] = Order_ReturnModel::RETURN_PLAT_PASS;
	                $data['return_finish_time'] = get_date_time();
	                $rs_row = array();
	                $edit_flag = $this->orderReturnModel -> editReturn($order_return_id, $data);
	                check_rs($edit_flag, $rs_row);
	                //根据order_id查找订单信息
	                $order_base = $this->orderBaseModel -> getOne($return['order_number']);
	                $data['return_goods_return'] = $return['return_goods_return'];
	                if ($return['return_goods_return']) {
	                    $Shop_BaseModel = new Shop_BaseModel();
	                    $shop_detail = $Shop_BaseModel -> getOne($order_base['shop_id']);
	                    /*if ($shop_detail['shop_type'] == 2) {
	                        $flag = $this->edit_product($return['order_number'], $return['order_goods_num']);

	                        $data['edit_product'] = $flag;
	                    }*/
	                }
	                //判断该商品是否是三级分销的商品，如果是三级分销的商品需要退还分销佣金
	                $Order_GoodsModel = new Order_GoodsModel();
	                $dc = $Order_GoodsModel -> refundDirectsellercommission($return['order_goods_id'], $return['order_goods_num']);
	                //如果是分销商的进货单则同时退掉买家订单
	                if ($order_base['order_source_id']) {
	                    $dist_return = $this->orderReturnModel -> getOneByWhere(array('order_number' => $order_base['order_source_id'], 'return_type' => $return['return_type']));
	                    $this -> agreeDist($dist_return['order_return_id'], $data);
	                }
	                if ($return['return_goods_return']) {
	                    //如果是退货情况下，退还三级分销佣金
	                    $Order_GoodsModel -> returnDirectsellercommission($return['order_goods_id'], $dc);
	                    //商品退换情况为完成2
	                    $goods_data['goods_refund_status'] = Order_GoodsModel::REFUND_COM;
	                    $edit_flag = $this->orderGoodsModel -> editGoods($return['order_goods_id'], $goods_data);
	                    check_rs($edit_flag, $rs_row);
	                } else {
	                    /*将商品库存加回商品中*/
	                    $Goods_BaseModel = new Goods_BaseModel();
	                    $Goods_BaseModel -> returnGoodsStock($return['order_goods_id'], $return['order_goods_num'], $return['behalf_deliver']);
	                    $goods_data['goods_return_status'] = Order_GoodsModel::REFUND_COM;
	                    $edit_flag = $this->orderGoodsModel -> editGoods($return['order_goods_id'], $goods_data);
	                    check_rs($edit_flag, $rs_row);
	                }
	                $ogoods_data = array();
	                $ogoods_data['order_goods_returnnum'] = $return['order_goods_num'];
	                $edit_flag = $this->orderGoodsModel -> editGoods($return['order_goods_id'], $ogoods_data, true);
	                check_rs($edit_flag, $rs_row);
	                //退款金额，退货数量，交易佣金退款更新到订单表中
	                $order_edit = array();
	                //判断商品金额是否全都退还，如果全部退还订单状态修改为完成状态(用订单商品数判断)
	                //订单中所有商品数量
	                $order_goods = $this->orderGoodsModel -> getByWhere(array('order_id' => $return['order_number'], 'order_goods_amount:>' => 0));
	                $order_all_goods_num = array_sum(array_column($order_goods, 'order_goods_num'));
	                $where = array(
	                    'order_number' => $return['order_number'],
	                    'return_state' => Order_ReturnModel::RETURN_PLAT_PASS,
	                    'return_shop_handle:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS,
	                );
	                //查找该笔订单已经完成的退款，退货
	                $order_return = $this->orderReturnModel -> getByWhere($where);
	                //订单已经退还的商品数量
	                $order_return_num = array_sum(array_column($order_return, 'order_goods_num'));
	                $order_edit['order_refund_amount'] = $return['return_cash'];
	                $order_edit['order_return_num'] = $return['order_goods_num'];
	                $order_edit['order_commission_return_fee'] = $return['return_commision_fee'];
	                $order_edit['order_rpt_return'] = $return['return_rpt_cash'];
	                $edit_flag = $this->orderBaseModel -> editBase($return['order_number'], $order_edit, true);
	                check_rs($edit_flag, $rs_row);
	                
	                if ($edit_flag) {
	                    //判断该笔订单是否是主账号支付，如果是主账号支付，则将退款金额退还主账号
	                    if ($order_base['order_sub_pay'] == Order_StateModel::SUB_SELF_PAY) {
	                        $return_user_id = $return['buyer_user_id'];
	                        $return_user_name = $return['buyer_user_account'];
	                    }
	                    if ($order_base['order_sub_pay'] == Order_StateModel::SUB_USER_PAY) {
	                        //查找主管账户用户名
	                        $User_BaseModel = new  User_BaseModel();
	                        $sub_user_base = $User_BaseModel -> getOne($order_base['order_sub_user']);
	                        $return_user_id = $order_base['order_sub_user'];
	                        $return_user_name = $sub_user_base['user_account'];
	                    }
	                    $key = Yf_Registry ::get('shop_api_key');
	                    $url = Yf_Registry ::get('paycenter_api_url');
	                    $shop_app_id = Yf_Registry ::get('shop_app_id');
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
	                    //SP分销单没有payment_other_number这个字段值会报错，所以在此做判断
	                    if ($order_base['payment_other_number']) {
	                        $formvars['uorder_id'] = $order_base['payment_other_number'];
	                    } else {
	                        $formvars['uorder_id'] = $order_base['payment_number'];
	                    }
	                    //平台同意退款（只增加买家的流水）
	                    
	                    if ($payway == '余额') {
	                    	$rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=refundBuyerTransfer&typ=json', $url), $formvars);
	                    }elseif ($payway == '现金' || $payway == '银行卡') {
	                    	$rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=webposRefundBuyerTransfer&typ=json', $url), $formvars);
	                    } else {
	                    	$rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=webposRefundBuyerTransfer&typ=json', $url), $formvars);
	                    	$refundFlag = $this->refund($order_return_id);
	            			check_rs($refundFlag, $rs_row);
	                    }
	                    
	                    if ($rs['status'] == 200) {
	                        check_rs(true, $rs_row);
	                    } else {
	                        check_rs(false, $rs_row);
	                    }
	                    $edit_flag = is_ok($rs_row);
	                }
	             
	            }
	            $flag = is_ok($rs_row);
	        } else {
	            $flag = false;
	            $data = array();
	        }
	        if ($flag && $this->orderReturnModel->sql->commitDb()) {
	            $status = 200;
	            $msg = __('success');
	            /**
	             *  加入统计中心
	             */
	            //如果$return['order_goods_id']为0则为退款
	            if ($return['return_goods_return']) {
	                $order_goods_data = $this->orderGoodsModel -> getOne($return['order_goods_id']);
	                $order_return_goods_id = $order_goods_data['goods_id'];
	                $order_goods_num = $return['order_goods_num'];
	            } else {
	                $order_goods_data = $this->orderGoodsModel -> getGoodsListByOrderId($return['order_number']);
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
	            Yf_Plugin_Manager ::getInstance() -> trigger('analyticsUpdateOrderStatus', $analytics_data);
	            /******************************************************************/
	        } else {
	            $this->orderReturnModel->sql->rollBackDb();
	            $status = 250;
	            $msg = __('failure');
	        }

        }
        
        $data = array();
        
        $this->data->addBody(-140, $data, $msg, $status);
        
    }

    public function agreeDist($order_return_id, $data)
    {
        $Order_StateModel = new Order_StateModel();
        $return = $this->orderReturnModel->getOne($order_return_id);
        
        $Order_GoodsModel = new Order_GoodsModel();
        $dc = $Order_GoodsModel->refundDirectsellercommission($return['order_goods_id'], $return['order_goods_num']);
        
        //根据order_id查找订单信息
        $order_base = $this->tradeOrderModel->getOne($return['order_number']);
        
        $rs_row = array();
        
        $edit_flag = $this->orderReturnModel->editReturn($order_return_id, $data);
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
        $edit_flag = $this->tradeOrderModel->editBase($return['order_number'], $sum_data, true);
        check_rs($edit_flag, $rs_row);
        
        //订单中所有商品数量
        $order_goods = $Order_GoodsModel->getByWhere(array('order_id' => $return['order_number'], 'order_goods_amount:>' => 0));
        $order_all_goods_num = array_sum(array_column($order_goods, 'order_goods_num'));
        
        //查找该笔订单已经完成的退款，退货
        $order_return = $this->orderReturnModel->getByWhere(array(
            'order_number' => $return['order_number'],
            'return_state' => Order_ReturnModel::RETURN_PLAT_PASS
        ));
        //订单已经退还的商品数量
        $order_return_num = array_sum(array_column($order_return, 'order_goods_num'));
        
        if ($order_all_goods_num == $order_return_num && $order_base['order_status'] != $Order_StateModel::ORDER_FINISH) {
            $order_edit_row = array();
            $order_edit_row['order_status'] = $Order_StateModel::ORDER_FINISH;
            
            $edit_flag2 = $this->tradeOrderModel->editBase($return['order_number'], $order_edit_row);
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

    public function refuseDist($order_return_id, $data)
    {
        $return = $this->orderReturnModel->getOne($order_return_id);
        
        $rs_row = array();
        $edit_flag = $this->orderReturnModel->editReturn($order_return_id, $data);
        check_rs($edit_flag, $rs_row);
        
        if ($return['return_goods_return']) {
            //商家拒绝退款退货3
            $goods_data['goods_refund_status'] = Order_GoodsModel::REFUND_REF;
            $edit_flag = $this->orderGoodsModel->editGoods($return['order_goods_id'], $goods_data);
            check_rs($edit_flag, $rs_row);
        } else {
            $goods_data['goods_return_status'] = Order_GoodsModel::REFUND_REF;
            $edit_flag = $this->orderGoodsModel->editGoods($return['order_goods_id'], $goods_data);
            check_rs($edit_flag, $rs_row);
        }
        
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

	public function addPointsOrder()
	{
		$goods = request_row('goods');
		$userId = request_int('ucenter_id');
		$points_order_id = request_string('order_id');
		//获取用户等级信息
		//用户信息，包含用户等级
		$user_info = $this->userInfoModel->getUserInfo(array('user_id' => $userId));
		//$points_cart_id_row            = request_row('point_cart_id');//购物车id
		$Points_GoodsModel = new Points_GoodsModel();

		if ($goods) 
		{
			$total_points = 0;
			foreach ($goods as $k => $v) {
				$goods_id[] = $v[1];
			}
			$pointslists = $Points_GoodsModel->getByWhere(['points_goods_id:IN'=>$goods_id]);

			foreach ($pointslists as $k => $v) {
				foreach ($goods as $key => $value) {
					if ($v['points_goods_id'] == $value[1]) {
						$total_points += $v['points_goods_points'] * $value[0];
						$pointslists[$k]['num'] = $value[0];
					}
				}

			}

			$user_resource = $this->userResourceModel->getOne($userId);
			if ($total_points <= $user_resource['user_points'])
			{
				$rs_row = array();
				//0、开启事务
				$this->pointsOrderModel->sql->startTransactionDb();
				//1、写入积分订单表
				//$points_order_id                          = date("ymdhis") . rand(0000, 9999);
				$field_row_p_order['points_order_rid']    = $points_order_id;//订单号，规则为暂时的
				$field_row_p_order['points_buyerid']      = $userId;
				$field_row_p_order['points_buyername']    = $user_info['user_name'];
				$field_row_p_order['points_buyeremail']   = $user_info['user_email'];
				$field_row_p_order['points_addtime']      = get_date_time();
				$field_row_p_order['points_allpoints']    = $total_points;
				$field_row_p_order['points_orderstate']    = 3;
				$flag_add_p_order                         = $this->pointsOrderModel->addPointsOrder($field_row_p_order, true);
				check_rs($flag_add_p_order, $rs_row);

				//2、写入积分订单商品表，并且减少购物车商品的库存
				foreach ($pointslists as $key => $value)
				{
					$field_p_o_g_row                       = array();
					$field_p_o_g_row['points_orderid']     = $points_order_id;
					$field_p_o_g_row['points_goodsid']     = $value['points_goods_id'];
					$field_p_o_g_row['points_goodsname']   = $value['points_goods_name'];
					$field_p_o_g_row['points_goodspoints'] = $value['points_goods_points'];
					$field_p_o_g_row['points_goodsnum']    = $value['num'];
					$field_p_o_g_row['points_goodsimage']  = $value['points_goods_image'];
					$flag_p_o_goods                        = $this->pointsOrderGoodsModel->addPointsOrderGoods($field_p_o_g_row, true);
					check_rs($flag_p_o_goods, $rs_row);
					//减少购物车商品的库存
					$points_goods_info = $Points_GoodsModel->getOneByWhere(['points_goods_id'=>$value['points_goods_id']]);
					//如果购买数量等于库存，则将商品库存修改为0并且下架
					if($points_goods_info['points_goods_storage'] == $value['num'])
					{
						$goods_edit_rows['points_goods_storage'] = 0;//库存
						$goods_edit_rows['points_goods_shelves'] = 0;//下架
					}
					elseif($points_goods_info['points_goods_storage'] > $value['num'])
					{
						$goods_edit_rows['points_goods_storage'] = $points_goods_info['points_goods_storage'] - $value['num'];//库存
					}
					$points_goods_flag = $Points_GoodsModel->editPointsGoods($value['points_goods_id'], $goods_edit_rows);
					check_rs($points_goods_flag, $rs_row);
				}

				//3、写入积分订单收货地址表(根据收货地址id获取用户的收货地址详细信息)
				//(1)根据用户选择的收货地址id，获取详细的收货地址信息
				//$delivery_row = $this->Delivery_BaseModel->getDeliveryInfo($delivery_id);
				//(2)将详细的收货信息入库
				$field_deliver_row['points_orderid']  = $points_order_id;
				$field_deliver_row['points_truename'] = $user_info['user_name'];//收货人姓名
				//$field_deliver_row['points_areaid'] =   request_string('');//地区id
				//$field_deliver_row['points_areainfo'] = request_string('');//地区内容
				//$field_deliver_row['points_address'] = request_string('receiver_address');//详细地址
				//$field_deliver_row['points_zipcode'] =  request_string('');//邮政编码
				//$field_deliver_row['points_telphone'] = request_string('');//电话号码
				$field_deliver_row['points_mobphone'] = $user_info['user_mobile'];//手机号码
				$flag_p_o_deliver                     = $this->pointsOrderAddressModel->addPointsOrderAddress($field_deliver_row, true);
				check_rs($flag_p_o_deliver, $rs_row);

				//4、更新用户积分值
				$field_row_points['user_points'] = '-' . $total_points;
				$edit_user_point_flag            = $this->userResourceModel->editResource($userId, $field_row_points, true);
				check_rs($edit_user_point_flag, $rs_row);

				//5、写入用户积分日志
				$field_row_points_log['user_id']           = $userId;
				$field_row_points_log['points_log_type']   = 2;
				$field_row_points_log['class_id']          = 7;
				$field_row_points_log['user_name']         = $user_info['user_name'];
				$field_row_points_log['points_log_points'] = $total_points;
				$field_row_points_log['points_log_time']   = get_date_time();
				$field_row_points_log['points_log_desc']   = __('积分换购商品');
				$Points_LogModel                           = new Points_LogModel();
				$add_p_log_flag                            = $Points_LogModel->addLog($field_row_points_log, true);
				check_rs($add_p_log_flag, $rs_row);

				if (is_ok($rs_row) && $this->pointsOrderModel->sql->commitDb())
				{
					$msg    = __('订单提交成功');
					$status = 200;
				}
				else
				{
					$this->pointsOrderModel->sql->rollBackDb();
					$msg    = __('订单提交失败');
					$status = 250;
				}
			}
			else
			{
				$status = 250;
				$msg    = __('用户积分不足！');
			}
		}
		

		$data = array();
		$this ->data->addBody(-140, $data, $msg, $status);
	}

	   	    /**
	 * 生成实物订单
	 *
	 * @author     Zhuyt
	 */
	public function webpos_addOrder()
	{       

		    $user_id      = request_string('ucenter_id');
		    $user_account      = request_string('ucenter_name');
            $flag         = true;
            $order_id     = request_string('order_id');
            $chain_id     = request_int('chain_id');//实体店id
            $goods        = request_row('goods'); 
            $mob_phone    = request_string('phone');
            $kind         = request_string('kind');
            $price        = request_string('price');
            $type         = request_string('type');
            $true_name    = request_string('shop_name');
            $remarks      = request_string('remarks');
            $increase_goods_id = request_row("increase_goods_id");
            $voucher_id   = request_row('voucher_id');//代金券 PAY_CHAINPYA
            $rpacket_id   = request_string('redpacket_id');//平台红包
            $is_discount  = request_int('discount_status');
            if(in_array($type,array(2,4))){
            	$pay_way_id   = PaymentChannlModel::PAY_CHAINPYA;
            }else{
            	$pay_way_id   = PaymentChannlModel::PAY_ONLINE;
            }

            if(in_array($type,array(2,4,5,6))){
                $trade_desc = "WEBPOS";	
            }
            
            //判断支付方式为在线支付还是货到付款,如果是货到付款则订单状态直接为待发货状态，如果是在线支付则订单状态为待付款
            if ($pay_way_id == PaymentChannlModel::PAY_ONLINE) {
            	if(in_array($type,array(1,3))){
            	  $order_status = Order_StateModel::ORDER_FINISH;
            	}else{
            		$order_status = Order_StateModel::ORDER_WAIT_PAY;
            	}
                
            }

            if ($pay_way_id == PaymentChannlModel::PAY_CHAINPYA) {            	
            		$order_status = Order_StateModel::ORDER_FINISH;       
            }
            
    //         if($kind == 'ticket')
    //         {
    //         	$PaymentChannlModel  = new PaymentChannlModel();
    //           	$order_row['order_status']          = $order_status;
    //           	$order_row['payment_id']            = $pay_way_id;
		  //       $order_row['payment_name']          = $PaymentChannlModel ->payWay[$pay_way_id];
		       
				// $orderBaseModel = new Order_BaseModel();
				// $flag_a= $orderBaseModel->editBase($order_id,$order_row);
    //             $flag = $flag&&$flag_a;

    //              if($flag){
    //             	  $Order_GoodsModel = new Order_GoodsModel();
    //             	  $order_goods['order_id'] = $order_id;
    //             	  $order_info['order_goods_status'] = Order_StateModel::ORDER_FINISH;
    //             	  $orderBase=$Order_GoodsModel->getByWhere(['order_id'=>$order_id]);
    //             	  $orderBaseKey = array_keys($orderBase);

    //                   $flag_b = $Order_GoodsModel->editGoods($orderBaseKey,$order_info);

    //             	  $flag = $flag&&$flag_b;
    //                 if($flag){
                        
		  //               if($type == 5){
		  //               	$key                          = Yf_Registry::get('shop_api_key');
		  //                   $url                          = Yf_Registry::get('paycenter_api_url');
				//             $money['price']=$price;
				//             $money['user_id'] =$user_id;
				//             $my =get_url_with_encrypt($key, sprintf('%s?ctl=Api_WebPos&met=webposusermoney&typ=json', $url), $money);
				                  
				//             if($my['status'] == 250){
			 //                     $msg =  __('failure');
				// 	             $status = 250;
				// 	             $data = array();
				// 	          return  $this->data->addBody(-140, $data, $msg, $status);
				//             }
		  //                }else{
    //                         $status = 200;
    //                         $msg = __('success');
    //                         $data = array();
				// 		}

    //                 }else{
    //                     $msg =  __('failure');
		  //               $status = 250;
		  //               $data = array();
    //                  }
    //             }else{
    //             	$msg =  __('failure');
		  //           $status = 250;
		  //           $data = array();
    //             }
    //           return  $this->data->addBody(-140, $data, $msg, $status);
    //         }

            //获取店铺信息
            $chain_userModel = new Chain_UserModel();
            $shop_baseModel  = new Shop_BaseModel();
            $chain_userBase  = $chain_userModel->getOneBywhere(array('chain_id'=>$chain_id)); 
            $shop_base       = $shop_baseModel->getOneBywhere(array('shop_id'=>$chain_userBase['shop_id']));

            //获取商品信息
            $Goods_BaseModel = new Goods_BaseModel();
            //$data = $Goods_BaseModel->getGoodsInfo($goods_id);
            $CartModel       = new CartModel();
            $data            = $CartModel -> getWebpos_ChainGoods($goods, $chain_id);

            //获取用户的折扣信息
            $User_InfoModel = new User_InfoModel();
            $user_info      = $User_InfoModel -> getOne($user_id);
            $User_GradeMode = new User_GradeModel();
            $user_grade     = $User_GradeMode -> getGradeRate($user_info['user_grade']);
            if ($is_discount) {
            	$user_rate  = $user_grade['user_grade_rate'];
               
            } else {
                 $user_rate = 100;  //不享受折扣时，折扣率为100%
            }
            //判断该店铺是否是自营店铺。后台是否设置了会员折扣限制
            if (Web_ConfigModel::value('rate_service_status') && $data['shop_base']['shop_self_support'] == 'false') {
                $user_rate  = 100;
            }
            //查找代金券的信息
            $Voucher_BaseModel = new Voucher_BaseModel();
            if ($voucher_id && !$is_discount) {
                $voucher_base  = $Voucher_BaseModel -> getOne($voucher_id);
                $voucher_id    = $voucher_base['voucher_id'];
                $voucher_price = $voucher_base['voucher_price'];
                $voucher_code  = $voucher_base['voucher_code'];
            } else {
                $voucher_id    = 0;
                $voucher_price = 0;
                $voucher_code  = 0;
            }


            $redPacket_BaseModel = new RedPacket_BaseModel();
            if ($rpacket_id && !$is_discount) {
                $redPacket_base  = $redPacket_BaseModel -> getOne($rpacket_id);
                $redpacket_id    = $redPacket_base['redpacket_id'];
                $redpacket_price = $redPacket_base['redpacket_price'];
                $redpacket_code  = $redPacket_base['redpacket_code'];  
            }else {
                $redpacket_id    = 0;
                $redpacket_price = 0;
                $redpacket_code  = 0;
            }

             foreach($data['goods'] as  $key => $val){
                 if($is_discount){
		           $order_goods_discount_fee  = $val['goods_base']['sumprice'] * (100 - $user_rate) / 100;        //优惠价格
		        }elseif($rpacket_id ||$voucher_id){
		           $order_goods_discount_fee =($voucher_price+$redpacket_price)*$val['goods_base']['sumprice']/$data['goods_sumprice'];
		        }else{
		        	$order_goods_discount_fee  =0;
		        }

		        $NewPay  = $val['goods_base']['sumprice']-$order_goods_discount_fee;

		        $data['goods'][$key]['goods_base']['commission'] = number_format(($NewPay *  $val['goods_base']['cat_commission'] / 100), 2, '.', '');

		        $data['commission']+= $data['goods'][$key]['goods_base']['commission'];
		       

            }


            //$data['goods_base']['sumprice'] = number_format($goods_num * $data['goods_base']['now_price'],2,',','');
            //开启事物
            $this->tradeOrderModel->sql->startTransactionDb();
            

            $Number_SeqModel     = new Number_SeqModel();
            $Order_BaseModel     = new Order_BaseModel();
            $Order_GoodsModel    = new Order_GoodsModel();
            $PaymentChannlModel  = new PaymentChannlModel();
            $Order_GoodsSnapshot = new Order_GoodsSnapshot();
            //生成店铺订单
            //总结店铺的优惠活动
            $order_shop_benefit = '';
            // if ($data['mansong_info']) {
            //     $order_shop_benefit = $order_shop_benefit . '店铺满送活动:';
            //     if ($data['mansong_info']['rule_discount']) {
            //         $order_shop_benefit = $order_shop_benefit . ' 优惠' . format_money($data['mansong_info']['rule_discount']) . ' ';
            //     }
            // }
            if ($user_rate < 100 && $is_discount && $user_rate < 100) {
                $order_shop_benefit = $order_shop_benefit . ' 会员折扣:' . $user_rate/10 . '折 ';
            }
            if ($voucher_price && !$is_discount) {
                $order_shop_benefit = $order_shop_benefit . ' 代金券:' . format_money($voucher_base['voucher_price']) . ' ';
            }

            if ($redpacket_price && !$is_discount) {
                $order_shop_benefit = $order_shop_benefit . ' 红包:' . format_money($redPacket_base['redpacket_price']) . ' ';
            }
            $prefix       = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));
            $order_number = $Number_SeqModel -> createSeq($prefix);
            $order_price  = $data['goods_sumprice'] ;
            $commission   = $data['commission'] ;
            $order_id     = $order_id;
            $order_row    = array();
            $order_row['order_id']              = $order_id;
            $order_row['shop_id']               = $shop_base['shop_id'];
            $order_row['shop_name']             = $shop_base['shop_name'];
            $order_row['buyer_user_id']         = $user_id;
            $order_row['buyer_user_name']       = $user_account;
            $order_row['seller_user_id']        = $shop_base['user_id'];
            $order_row['seller_user_name']      = $shop_base['user_name'];
            $order_row['order_date']            = date('Y-m-d');
            $order_row['order_create_time']     = get_date_time();
            $order_row['order_receiver_name']   = $user_account;
            $order_row['order_receiver_contact'] = $mob_phone;
            $order_row['order_goods_amount']    = $order_price;
            //判断是否使用会员折扣
            if($is_discount){
            	$order_row['order_payment_amount']  = ($order_price * $user_rate) / 100 ;//$data['sprice'];
                $order_row['order_discount_fee']    = ($order_price * (100 - $user_rate)) / 100 ;   //折扣金额
            }else if($rpacket_id ||$voucher_id){
            	$order_row['order_payment_amount']  = $order_price  - $voucher_price-$redpacket_price;//$data['sprice'];
                $order_row['order_discount_fee']    =  $voucher_price+$redpacket_price;   //折扣金额
            }else{
                $order_row['order_payment_amount']  = $order_price ;//$data['sprice'];
                $order_row['order_discount_fee']    = 0 ;   //折扣金额
            }
            
            $order_row['order_point_fee']       = 0;    //买家使用积分
            $order_row['order_message']         = $remarks;
            $order_row['order_status']          = $order_status;
            $order_row['order_points_add']      = 0;    //订单赠送的积分
            $order_row['voucher_id']            = $voucher_id;    //代金券id
            $order_row['voucher_price']         = $voucher_price;    //代金券面额
            $order_row['voucher_code']          = $voucher_code;    //代金券编码
            $order_row['order_commission_fee']  = $commission;  //交易佣金
            $order_row['redpacket_price']         = $redpacket_price;    //红包面额
            $order_row['redpacket_code']          = $redpacket_code;    //红包编码
            $order_row['order_rpt_price']          = $redpacket_price;    //红包编码            
            $order_row['order_is_virtual']      = 0;    //1-虚拟订单 0-实物订单
            $order_row['order_shop_benefit']    = $order_shop_benefit;  //店铺优惠
            $order_row['payment_id']            = $pay_way_id;
            $order_row['payment_name']          = $PaymentChannlModel ->payWay[$pay_way_id];
            $order_row['chain_id']              = $chain_id;
            $order_row['order_finished_time']   = date('Y-m-d H:i:s',time());
            $order_row['district_id']           = $shop_base['district_id'];
            $flag1                              = $this->tradeOrderModel->addBase($order_row);

            $flag                               = $flag && $flag1;
           if ($voucher_id) {
                    $Voucher_BaseModel = new Voucher_BaseModel();
                    $flag6=$Voucher_BaseModel -> changeVoucherState($voucher_id, $order_id);
                    $flag = $flag && $flag6;
                }

            //修改用户使用的红包信息
            if ($rpacket_id) {
                $redPacket_BaseModel = new RedPacket_BaseModel();
                $field_row = array();
                $field_row['redpacket_state'] = RedPacket_BaseModel::USED;
                $field_row['redpacket_order_id'] = $order_id;
                $flag5 = $redPacket_BaseModel -> editRedPacket($rpacket_id, $field_row);
                $flag = $flag && $flag5;
            }

		   foreach($data['goods'] as $key => $val){

		   	    $trade_title = '';

		        //插入订单商品表
		        $order_goods_row = array();
		        $order_goods_row['order_id']                  = $order_id;
		        $order_goods_row['goods_id']                  = $val['goods_base']['goods_id'];
		        $order_goods_row['common_id']                 = $val['goods_base']['common_id'];
		        $order_goods_row['buyer_user_id']             = $user_id;
		        $order_goods_row['goods_name']                = $val['goods_base']['goods_name'];
		        $order_goods_row['goods_class_id']            = $val['goods_base']['cat_id'];
		        $order_goods_row['order_spec_info']           = $val['goods_base']['spec'];
		        $order_goods_row['goods_price']               = $val['goods_base']['now_price'];
		        $order_goods_row['order_goods_num']           = $val['goods_base']['num'];
		        $order_goods_row['goods_image']               = $val['goods_base']['goods_image'];
		        $order_goods_row['order_goods_amount']        = $val['goods_base']['sumprice'];

		        if($is_discount){
		           $order_goods_row['order_goods_discount_fee']  = ($val['goods_base']['sumprice'] * (100 - $user_rate)) / 100;        //优惠价格
		        }else if($rpacket_id ||$voucher_id){
		           $order_goods_row['order_goods_discount_fee'] =($voucher_price+$redpacket_price)*$val['goods_base']['sumprice']/$order_price;
		        }else{
		        	$order_goods_row['order_goods_discount_fee']  =0;
		        }
		        $order_goods_row['order_goods_payment_amount'] = $val['goods_base']['now_price']-$order_goods_row['order_goods_discount_fee'] / $val['goods_base']['num'];
		 
		        $order_goods_row['order_goods_adjust_fee']    = 0;    //手工调整金额
		        $order_goods_row['order_goods_point_fee']     = 0;    //积分费用
		        $order_goods_row['order_goods_commission']    = $val['goods_base']['commission'];   //商品佣金
		        $order_goods_row['shop_id']                   = $val['goods_base']['shop_id'];
		        $order_goods_row['order_goods_status']        = $order_status;
		        $order_goods_row['order_goods_evaluation_status'] = 0;  //0未评价 1已评价
		        $order_goods_row['order_goods_benefit']       = $order_goods_benefit;
		        $order_goods_row['order_goods_time']          = get_date_time();
		        //平台是否收取分销商,供货商佣金
		        $goods_commission = Web_ConfigModel::value('goods_commission');
		        $supplier_commission = Web_ConfigModel::value('supplier_commission');
		        if ($goods_commission && strpos($order_id,'YF')!==false) {		        	 
		            $order_goods_row['order_goods_commission'] = $val['goods_base']['commission'];    //商品佣金
		        } else {
		            $order_goods_row['order_goods_commission'] = 0;    //商品佣金
		        }

		        $flag2 = $Order_GoodsModel -> addGoods($order_goods_row);
		        $trade_title .= $val['goods_base']['goods_name'] . ',';
		        //加入交易快照表
		        $order_goods_snapshot_add_row = array();
		        $order_goods_snapshot_add_row['order_id'] = $order_id;
		        $order_goods_snapshot_add_row['user_id'] = $user_id;
		        $order_goods_snapshot_add_row['shop_id'] = $val['goods_base']['shop_id'];
		        $order_goods_snapshot_add_row['common_id'] = $val['goods_base']['common_id'];
		        $order_goods_snapshot_add_row['goods_id'] = $val['goods_base']['goods_id'];
		        $order_goods_snapshot_add_row['goods_name'] = $val['goods_base']['goods_name'];
		        $order_goods_snapshot_add_row['goods_image'] = $val['goods_base']['goods_image'];
		        $order_goods_snapshot_add_row['goods_price'] = $val['now_price'];
		        $order_goods_snapshot_add_row['freight'] = 0;   //运费
		        $order_goods_snapshot_add_row['snapshot_create_time'] = get_date_time();
		        $order_goods_snapshot_add_row['snapshot_uptime'] = get_date_time();
		        $order_goods_snapshot_add_row['snapshot_detail'] = $order_goods_benefit;
		        $Order_GoodsSnapshot -> addSnapshot($order_goods_snapshot_add_row);
		        $flag = $flag && $flag2;
		   }
           //删除商品库存
           foreach($data['goods'] as $key =>$val){
	         	$Chain_GoodsModel           = new Chain_GoodsModel();
	            $chain_row['chain_id:=']    = $chain_id;
	            $chain_row['goods_id:=']    = $val['goods_base']['goods_id'];;
	            $chain_row['shop_id:=']     = $val['shop_base']['shop_id'];
	            $chain_goods                = current($Chain_GoodsModel -> getByWhere($chain_row));
	            $chain_goods_id             = $chain_goods['chain_goods_id'];
	            $goods_stock['goods_stock'] = $chain_goods['goods_stock'] - $val['goods_base']['num'];
	            if ($goods_stock['goods_stock'] < 0) {
	                throw new Exception('门店库存不足');
	            }
	            $flag3                      = $Chain_GoodsModel -> editGoods($chain_goods_id, $goods_stock);

	            if(!$flag3){
	                return $flag3         =false;
	            }
            }

            $flag                       = $flag && $flag3;


            if ($flag && $this->tradeOrderModel->sql->commitDb()) {
                //支付中心生成订单
                $key                          = Yf_Registry::get('shop_api_key');
                $url                          = Yf_Registry::get('paycenter_api_url');
                $shop_app_id                  = Yf_Registry::get('shop_app_id');
                $formvars                     = array();
                $formvars['app_id']           = $shop_app_id;
                $formvars['from_app_id']      = Yf_Registry::get('shop_app_id');
                $formvars['consume_trade_id'] = $order_row['order_id'];
                $formvars['order_id']         = $order_row['order_id'];
                $formvars['buy_id']           = $user_id;
                $formvars['buyer_name']       = $user_account;
                $formvars['seller_id']        = $order_row['seller_user_id'];
                $formvars['seller_name']      = $order_row['seller_user_name'];
                $formvars['order_state_id']   = $order_row['order_status'];
                $formvars['order_payment_amount'] = $order_row['order_payment_amount'];
                $formvars['order_commission_fee'] = $order_row['order_commission_fee'];
                $formvars['trade_remark']     = $order_row['order_message'];
                $formvars['trade_create_time'] = $order_row['order_create_time'];
                $formvars['trade_title']      = $trade_title;        //商品名称 - 标题
                $formvars['type']      = 'WEBPOS';
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addConsumeTrade&typ=json', $url), $formvars);
                fb($rs);
                if ($rs['status'] == 200 && $type !=1 && $type !=3) {
                    $Order_BaseModel -> editBase($order_row['order_id'], array('payment_number' => $rs['data']['union_order']));
                    //生成合并支付订单
                    $key                      = Yf_Registry::get('shop_api_key');
                    $url                      = Yf_Registry::get('paycenter_api_url');
                    $shop_app_id              = Yf_Registry::get('shop_app_id');
                    $formvars                 = array();
                    $formvars['inorder']      = $order_id . ',';
                    $formvars['uprice']       = $order_row['order_payment_amount'];
                    $formvars['buyer']        = $user_id;
                    $formvars['trade_title']  = $trade_title;
                    $formvars['buyer_name']   = $user_account;
                    $formvars['app_id']       = $shop_app_id;
                     $formvars['trade_desc']   =$trade_desc;
                    $formvars['from_app_id']  = Yf_Registry::get('shop_app_id');
                    fb($formvars);
                    $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addwebposUnionOrder&typ=json', $url), $formvars);

                }

                if(in_array($type,array(1,3,5))){
	                $trade_id=$rs['data']['uorder'];
		            $checkedPayData['uorder_id']=$trade_id;
		            if($type == 1 || $type == 3){
                       $checkedPayData['online_payway'] =true;
                       $checkedPayData['money_payway'] =false;
		            }else{
		               $checkedPayData['online_payway'] =false;
                       $checkedPayData['money_payway'] =true;
		            }
		            
		            $checkedPayData['user_id'] =$user_id;
		            $checkedPay =get_url_with_encrypt($key, sprintf('%s?ctl=Api_WebPos&met=checkPayWay&typ=json', $url), $checkedPayData);
		       //      $data[111]=$checkedPay;
		       // $this->data->addBody(-140, $data, $msg, $status);
		            if($checkedPay['status'] == 250){
	                    $msg =  __('failure');
			            $status = 250;
			            $data = array();
			            $this->data->addBody(-140, $data, $msg, $status);
		            }else{
		            	$money['trade_id']=$trade_id;
		                $money['user_id'] =$user_id;
		                if($type == 5){
		            	    $my =get_url_with_encrypt($key, sprintf('%s?ctl=Api_WebPos&met=webpos_money&typ=json', $url), $money);
		                }else{
		                	$my['status'] == 200;
		                }
                   
		            	if($my['status'] == 250){
	                        $msg =  __('failure');
			                $status = 250;
			                $data = array();
			                $this->data->addBody(-140, $data, $msg, $status);
			            }
			            else{
			            	$quren['order_id']=$order_id;
			            	$qurenOrder = get_url_with_encrypt($key, sprintf('%s?ctl=Api_WebPos&met=confirmOrder&typ=json', $url), $quren);
			            	
                             // $data[111] = $qurenOrder;
                             // $this->data->addBody(-140, $data, $msg, $status);
			            	if($qurenOrder['status'] == 250){
		                      $msg =  __('failure');
				              $status = 250;
				              $data = array(); 
				              $this->data->addBody(-140, $data, $msg, $status);
				            }
			            }
		            }
                 }
                
                  //统计中心
                  //添加订单统计
                 
                $analytics_data = array(
                    'order_id' => $order_id,
                    'union_order_id' => $uorder,
                    'user_id' => $user_id,
                    'ip' => get_ip(),
                    'addr' => '',
                    'chain_id' => $chain_id,
                    'type' => 3
                );
                Yf_Plugin_Manager::getInstance() -> trigger('analyticsOrderAdd', $analytics_data);
                $status = 200;
                $msg = __('success');
                $data = $rs['data'];
            } else {
                $this ->tradeOrderModel->sql ->rollBackDb();
                $m = $this->tradeOrderModel->msg->getMessages();
                $msg = $m ? $m[0] : __('failure');
                $status = 250;
                $data = array();
            }
            $this->data->addBody(-140, $data, $msg, $status);

	}
	
}

?>
