<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}
/**
 * 支付入口
 * @author     Cbin
 */
class PayCtl extends Controller
{
    private $unionOrderModel = '';
	//支付分发在PaymentWay目录中
	public function __call($name,$arg){
		$cls =  "PaymentWay_".ucfirst($name); 
		$cls = new $cls; 
		$met = $name; 
		$cls->$met();
		exit;
	}
	/**
	 *
	 * @param  string $ctl 控制器目录
	 * @param  string $met 控制器方法
	 * @param  string $typ 返回数据类型
	 * @access public
	 */
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
		$this->unionOrderModel = new Union_OrderModel();
	}

	/**
	 * 微信二维码支付
	 * 构造 url
	 * @param product_id产品ID
	 */
	public function structWXurl()
	{
		// 第一步 参数过滤
		$product_id  = trim($_REQUEST['product_id']);
		if (!$product_id || is_int($product_id))
		{
			$this->data->setError('参数错误');
			$this->data->printJSON();
			die;
		}

		// 第二步  调用url生成类
		$pw = new Payment_WxQrcodeModel();
		$url = $pw->url($product_id);
		include $this->view->getView();
	}

	/**
	 * 微信二维码支付
	 * 生成二维码
	 */
	public function structWXcode()
	{
		require_once MOD_PATH.'/Payment/phpqrcode/phpqrcode.php';
		$url = urldecode($_REQUEST["data"]);
		QRcode::png($url);
	}

	/**
	 * 微信二维码支付
	 * 微信回调
	 */
	public function WXnotify()
	{
		// 确定支付
		$pw = new Payment_WxQrcodeModel();
		$pw->notify();

		// 支付金额写入数据库
		// code
	}

	/**
	 * 支付
	 * 
	 * @author fzh
	 */
	public function money()
	{
		$trade_id = request_string('trade_id');
		$payway = request_string('payway');
		//如果订单号为合并订单号，则获取合并订单号的信息
		$Union_OrderModel = new Union_OrderModel();
		//开启事物
		$Consume_DepositModel = new Consume_DepositModel();
		$uorder = $this->unionOrderModel->getOne($trade_id);
		$data = array();
		//判断订单状态是否为等待付款状态
		if($uorder['order_state_id'] == Order_StateModel::ORDER_WAIT_PAY||$uorder['order_state_id'] == Order_StateModel::ORDER_PRESALE_DEPOSIT)
		{
			$pay_flag = false;
			$pay_user_id = 0;
			//判断当前用户是否是下单者，并且订单状态是否是待付款状态
			if($uorder['buyer_id'] == Perm::$userId)
			{
				$pay_flag = true;
				$pay_user_id = $uorder['buyer_id'];
			}
			else
			{
				//判断当前用户是否是下单者的主管账户
				$key      = Yf_Registry::get('shop_api_key');
				$url         = Yf_Registry::get('shop_api_url');
				$shop_app_id = Yf_Registry::get('shop_app_id');
				$formvars = array();

				$formvars['app_id']		 = $shop_app_id;
				$formvars['user_id']     = Perm::$userId;
				$formvars['sub_user_id'] = $uorder['buyer_id'];

				$sub_user = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=checkSubUser&typ=json',$url), $formvars);
				if(!empty($sub_user['data']) && $sub_user['status'] == 200)
				{
					$pay_flag = true;
					$pay_user_id = Perm::$userId;
				}
			}

			if($pay_flag)
			{
                //处理plus会员订单 @nsy 2019-02-16
                $trade_type = Trade_TypeModel::$trade_type_row[$uorder['trade_type_id']];
                if ($trade_type == 'plus'){
                    $ret = $Union_OrderModel->dealPlusOrder($uorder['union_order_id'],'余额支付','balance');
                    $User_ResourceModel = new User_ResourceModel();
                    $flag = $User_ResourceModel->frozenUserMoney(Perm::$userId,$uorder['union_money_pay_amount']);
                    if($ret){
                        $data['return_app_url'] = Yf_Registry::get('shop_api_url') . "?ctl=Plus_User&met=index";
                        $data['order_id'] = $uorder['inorder'];
                        $msg    = 'success';
                        $status = 200;
                    }else{
                        $msg    = __('failure8');
                        $status = 250;
                    }
                    return $this->data->addBody(-140, $data, $msg, $status);
                }
				//修改订单表中的各种状态
				$flag = $Consume_DepositModel->notifyShop($trade_id,$pay_user_id);
				if ($flag['status'] == 200)
				{
                    $flag = $this->update_order($trade_id,$uorder['inorder'],$payway);
                    if($flag == false){
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
					$msg    = __('修改订单状态失败');
					$status = 250;
				}
			}
			else
			{
				$msg    = __('该账号不是下单者或下单者的主管账号');
				$status = 250;
			}
		}
		else
		{
			$msg    = __('订单状态不为待付款状态');
			$status = 250;
		}
		$this->data->addBody(-140, $data, $msg, $status);
	}



	public function checkAvailableMoney(){

		$trade_id = request_string('trade_id');
		$Union_OrderModel = new Union_OrderModel();


		$uorder = $Union_OrderModel->getOne($trade_id);


		$key      = Yf_Registry::get('shop_api_key');
		$url         = Yf_Registry::get('shop_api_url');
		$shop_app_id = Yf_Registry::get('shop_app_id');
		$formvars = array();

		$formvars['app_id']					= $shop_app_id;
		$formvars['order_id'] = $uorder['inorder'];


		$order_base = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=getOrderBase&typ=json',$url), $formvars);
		$order_base = array_values($order_base['data']);


		//如果是分销订单
		$capital_scarcity = false;
		if($order_base){
			//查询分销商账户余额
			$User_Resource = new User_ResourceModel();
			$user_resource = $User_Resource->getOne($order_base[0]['buyer_user_id']);

			if($user_resource['user_money'] <($order_base[0]['order_payment_amount']) + $order_base[0]['order_shipping_fee']){
				$capital_scarcity = true;
			}

		}

		if(!$capital_scarcity){

			$status = 200;
			$msg = __('success');

		}else{
			$msg = __('failure');
			$status = 250;
		}

		$this->data->addBody(-140, array(), $msg, $status);


	}


	//主管账号待支付
	public function subpay()
	{
		$trade_id = request_string('trade_id');

		//如果订单号为合并订单号，则获取合并订单号的信息
		$Union_OrderModel = new Union_OrderModel();

		$uorder = $Union_OrderModel->getOne($trade_id);
		$inorder = $uorder['inorder'];

		$uorder_id = $trade_id;
		$order_id = explode(",",$inorder);
		array_filter($order_id);
		$data = array();
		$data['order_id'] = implode(',',$order_id);

		$act = request_string('act');
		//用于判断订单类型，order_g_type = physical实物订单，virtual虚拟订单

		$order_g_type = request_string('order_g_type') ? request_string('order_g_type') : 'physical';


		//获取需要支付的订单信息
		$Union_OrderModel = new Union_OrderModel();
		$uorder_base = $Union_OrderModel->getOne($uorder);


		$flag = false;
		//判断当前用户是否是下单者，并且订单状态是否是待付款状态
		if($uorder['buyer_id'] == Perm::$userId && $uorder['order_state_id'] == Order_StateModel::ORDER_WAIT_PAY)
		{
			$key      = Yf_Registry::get('shop_api_key');
			$url         = Yf_Registry::get('shop_api_url');
			$shop_app_id = Yf_Registry::get('shop_app_id');
			$formvars = array();

			$formvars['app_id']					= $shop_app_id;
			$formvars['sub_user_id']     = Perm::$userId;

			$sub_user = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=getSubUser&typ=json',$url), $formvars);

			$rs_row = array();
			//获取当前用户的主管账号
			if($sub_user['status'] == 200 && $sub_user['data']['count'] > 0)
			{
				//将该笔订单的交易明细表中的支付者修改为主管账号
				$Consume_RecordModel = new Consume_RecordModel();
				$cond_row = array();
				$cond_row['order_id:IN'] = $order_id;
				$cond_row['user_type'] = 2;
				$consume = $Consume_RecordModel->getByWhere($cond_row);
				$consume_id = array_values(array_column($consume,'consume_record_id'));

				$edit_row = array();
				$edit_row['user_id'] = $sub_user['data']['sub']['user_id'];
				$edit_row['user_nickname'] = $sub_user['data']['sub']['user_account'];
				fb($edit_row);
				$edit_flag = $Consume_RecordModel->editRecord($consume_id,$edit_row);
				check_rs($edit_flag,$rs_row);
				//修改这笔订单的支付人
				$order_edit_row = array();
				$order_edit_row['pay_user_id'] = $sub_user['data']['sub']['user_id'];
				$Consume_TradeModel = new Consume_TradeModel();
				$flag = $Consume_TradeModel->editTrade($order_id,$order_edit_row);
				check_rs($flag, $rs_row);

				if(is_ok($rs_row))
				{
					$Consume_TradeModel = new Consume_TradeModel();
					$consume_record = $Consume_TradeModel->getOne($order_id);
					$app_id = $consume_record['app_id'];

					$User_AppModel = new User_AppModel();
					$app_row = $User_AppModel->getOne($app_id);

					$return_app_url = $app_row['app_url'];

					$data['return_app_url'] = $return_app_url;

					$key = $app_row['app_key'];
					$url = $app_row['app_url'];
					$shop_app_id = $app_id;

					$formvars = array();
					$formvars = $_POST;
					$formvars['app_id'] = $shop_app_id;
					$formvars['order_id'] = $order_id;
					$formvars['order_sub_user'] = $sub_user['data']['sub']['user_id'];

					fb($formvars);

					//远程修改订单表中的order_sub_pay = 1:主管账号支付
					$rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=editOrderSubPay&typ=json', $url), $formvars);

					if($rs['status'] == 200)
					{
						$flag = true;
					}


				}

			}
		}

		if($flag)
		{
			$msg    = __('success');
			$status = 200;


//			/****************************************************************************************************/
			$user_id_row = $sub_user['data']['sub']['user_id'];
			try{
				//订单付款成功后进行极光推送
				require_once "Jpush/JPush.php";
				$type=array('type'=>'1');
				$app_key = '67c48d5035a1f01bc8c09a88';
				$master_secret = '805f959b10b0d13d63a231fd';
				$alert="您作为主管账号在".date("Y-m-d H:i:s")."帮助".$sub_user['data']['sub']['user_account']."支付订单成功，支出-".$edit_row['record_money'];
				$client = new JPush($app_key, $master_secret);
				$result=$client->push()
					->setPlatform(array('ios', 'android'))
					->addAlias($user_id_row)
					->addIosNotification($alert,'', null, null, null, $type)
					->addAndroidNotification($alert,null,null,$type)
					->setOptions(100000, 3600, null, false)
					->send();
			}
			catch(Exception $e){

			}
			/****************************************************************************************************/

		}else
		{
			$msg    = __('failure');
			$status = 250;
		}





		$this->data->addBody(-140, $data, $msg, $status);
	}


	/**
	 * 使用支付宝支付
	 *
	 */
	public function alipay()
	{
		$trade_id = request_string('trade_id');

		//如果订单号为合并订单号，则获取合并订单号的信息
		//$Union_OrderModel = new Union_OrderModel();
		$trade_row        = $this->unionOrderModel->getOne($trade_id);

		//判断订单状态是否为等待付款状态
		if($trade_row['order_state_id'] == Order_StateModel::ORDER_WAIT_PAY)
		{
			$pay_flag = false;
			$pay_user_id = 0;
            //修改
            $flag = $this->update_order($trade_id,$trade_row['inorder'],'alipay');
            if($flag == false){
                //报错
                echo"<script>alert('支付失败，请重新支付 !');history.go(-1);</script>";
            }
			//判断当前用户是否是下单者，并且订单状态是否是待付款状态
			if($trade_row['buyer_id'] == Perm::$userId)
			{
				$pay_flag = true;
				$pay_user_id = $trade_row['buyer_id'];
			}
			else
			{
				//判断当前用户是否是下单者的主管账户
				$key      = Yf_Registry::get('shop_api_key');
				$url         = Yf_Registry::get('shop_api_url');
				$shop_app_id = Yf_Registry::get('shop_app_id');
				$formvars = array();

				$formvars['app_id']					= $shop_app_id;
				$formvars['user_id']     = Perm::$userId;
				$formvars['sub_user_id'] = $trade_row['buyer_id'];

				$sub_user = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=checkSubUser&typ=json',$url), $formvars);
				if(!empty($sub_user['data']) && $sub_user['status'] == 200)
				{
					$pay_flag = true;
					$pay_user_id = Perm::$userId;
				}
			}
//ignore  给webpos使用
			if($pay_flag || $_GET['ignore']=='abc' )
			{
				if ($trade_row)
				{
					$Payment = PaymentModel::create('alipay');
					$Payment->pay($trade_row);
				}
				else
				{
					echo"<script>alert('支付失败,请重新支付');history.go(-1);</script>";
				}
			}
			else
			{
				echo"<script>alert('支付失败，请重新支付');history.go(-1);</script>";
			}
		}
		else
		{
			echo"<script>alert('支付失败，请重新支付');history.go(-1);</script>";
		}

	}




	
	
	/**
	 * 使用银联在线支付
	 *
	 */
	public function unionpay()
	{
		$trade_id = request_string('trade_id');

		//如果订单号为合并订单号，则获取合并订单号的信息

		$trade_row        = $this->unionOrderModel->getOne($trade_id);

		//判断订单状态是否为等待付款状态
		if($trade_row['order_state_id'] == Order_StateModel::ORDER_WAIT_PAY)
		{
			$pay_flag = false;
			$pay_user_id = 0;
			//判断当前用户是否是下单者，并且订单状态是否是待付款状态
			if($trade_row['buyer_id'] == Perm::$userId)
			{
				$pay_flag = true;
				$pay_user_id = $trade_row['buyer_id'];
			}
			else
			{
                //修改
                $flag = $this->update_order($trade_id,$trade_row['inorder'],'unionpay');
                if($flag == false){
                    //报错
                    echo"<script>alert('支付失败，请重新支付 !');history.go(-1);</script>";
                }
				//判断当前用户是否是下单者的主管账户
				$key      = Yf_Registry::get('shop_api_key');
				$url         = Yf_Registry::get('shop_api_url');
				$shop_app_id = Yf_Registry::get('shop_app_id');
				$formvars = array();

				$formvars['app_id']					= $shop_app_id;
				$formvars['user_id']     = Perm::$userId;
				$formvars['sub_user_id'] = $trade_row['buyer_id'];

				$sub_user = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=checkSubUser&typ=json',$url), $formvars);
				if(!empty($sub_user['data']) && $sub_user['status'] == 200)
				{
					$pay_flag = true;
					$pay_user_id = Perm::$userId;
				}
			}

			if($pay_flag)
			{
				if ($trade_row)
				{
					$Payment = PaymentModel::create('unionpay');
					$Payment->pay($trade_row);
				}
				else
				{
					echo"<script>alert('支付失败，请重新支付');history.go(-1);</script>";
				}
			}
			else
			{
				echo"<script>alert('支付失败，请重新支付');history.go(-1);</script>";
			}
		}
		else
		{
			echo"<script>alert('支付失败，请重新支付');history.go(-1);</script>";
		}

	}

    /**
     * 向shop请求修改order_base的支付渠道
     * @param $order_id
     * @param $pay_code
     */
    private function update_order($union_order_id,$order_id,$pay_code){

        $Union_OrderModel = new Union_OrderModel();
        $Union_OrderModel->editUnionOrder($union_order_id,['payment_channel_code'=>$pay_code]);
        $key      = Yf_Registry::get('shop_api_key');
        $url         = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();

        $formvars['app_id']		 = $shop_app_id;
        $formvars['user_id']     = Perm::$userId;
        $formvars['order_id'] = $order_id;
        $pay_codes = Yf_Registry::get('pay_config')['pay_code'];
        if(in_array($pay_code,$pay_codes['alipay_code'])){
            $formvars['payment_name'] = '支付宝支付';
        }elseif (in_array($pay_code,$pay_codes['wx_code'])){
            $formvars['payment_name'] ='微信支付';
        }elseif (in_array($pay_code,$pay_codes['other'])){
            $formvars['payment_name'] ='银联支付';
        }elseif($pay_code=='baitiao'){
            $formvars['payment_name'] ='白条支付';
        }else{
        	$formvars['payment_name'] ='余额支付';
        }
        return get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=editOrderPaymentName&typ=json',$url), $formvars);
    }
	/**
	 * 使用微信支付
	 *
	 */
	public function wx_native()
	{
		$trade_id = request_string('trade_id');
		$return_url = request_string('return_url');
		//如果订单号为合并订单号，则获取合并订单号的信息
		$trade_row = $this->unionOrderModel->getOne($trade_id);
		//判断订单状态是否为等待付款状态
		if($trade_row['order_state_id'] == Order_StateModel::ORDER_WAIT_PAY)
		{
            //修改
            $flag = $this->update_order($trade_id,$trade_row['inorder'],'wx_native');
            if($flag == false){
                //报错
                echo"<script>alert('支付失败，请重新支付 !');history.go(-1);</script>";
            }
			$pay_flag = false;
			$pay_user_id = 0;
			//判断当前用户是否是下单者，并且订单状态是否是待付款状态
			if($trade_row['buyer_id'] == Perm::$userId)
			{
				$pay_flag = true;
				$pay_user_id = $trade_row['buyer_id'];
			}
			else
			{
				//判断当前用户是否是下单者的主管账户
				$key      = Yf_Registry::get('shop_api_key');
				$url         = Yf_Registry::get('shop_api_url');
				$shop_app_id = Yf_Registry::get('shop_app_id');
				$formvars = array();
				$formvars['app_id']					= $shop_app_id;
				$formvars['user_id']     = Perm::$userId;
				$formvars['sub_user_id'] = $trade_row['buyer_id'];
				$sub_user = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=checkSubUser&typ=json',$url), $formvars);
				if(!empty($sub_user['data']) && $sub_user['status'] == 200)
				{
					$pay_flag = true;
					$pay_user_id = Perm::$userId;
				}
			}
			//ignore  给webpos使用
			if($pay_flag || $_GET['ignore']=='abc')
			{
				if ($trade_row)
				{
                    if (Yf_Utils_Device::isMobile() && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') == false) {
                        $trade_row['trade_type'] = "MWEB";
                        $trade_row['trade_id'] = $trade_id;
                    } else {
                        $trade_row['trade_type'] = $_REQUEST['trade_type'];
                    }
                    $trade_row['return_url'] = $return_url;
                    $Payment = PaymentModel::create('jh_app_pay');
                    // $Payment = PaymentModel::create('wx_native');
                    $Payment->pay($trade_row);
				}
				else
				{
					echo"<script>alert('支付失败，请重新支付');history.go(-1);</script>";
				}
			}
			else
			{
				echo"<script>alert('支付失败，请重新支付 !');history.go(-1);</script>";
			}
		}
		else
		{
			echo"<script>alert('支付失败，请重新支付!!!');history.go(-1);</script>";
		}

	}

	/**
	 * @param $uorder_data
	 * @return boolean
	 * 检查订单是否为付款状态
	 */
	private function checkOrderStatus ($uorder_data)
	{
		if ($uorder_data['order_state_id'] == Order_StateModel::ORDER_WAIT_PAY) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * 暂时手机端无联合付款，手机端支付为全款支付
	 * 手机端支付->修改pay_union_order状态
	 * union_online_pay_amount = trade_payment_amount
	 * union_cards_pay_amount = union_cards_return_amount = union_money_pay_amount = union_money_return_amount = 0;
	 */

	/**
	 * PHP服务端SDK生成APP支付订单信息 （支付宝）
	 */
	public function createAliOrder()
	{
		$uorder_id = request_string('uorder_id');

		//检查参数
		if (empty($uorder_id)) {
			return $this->data->addBody(-140, [], __('无效访问参数'), 250);
		}

		$uorder_data = $this->unionOrderModel->getOne($uorder_id);

		$this->unionOrderModel->editUnionOrder($uorder_id, ['union_online_pay_amount'=> $uorder_data['trade_payment_amount'],
														'union_cards_pay_amount'=> 0,
														'union_cards_return_amount'=> 0,
														'union_money_pay_amount'=> 0,
														'union_money_return_amount'=> 0
													]);
		
        $uorder_data = $this->unionOrderModel->getOne($uorder_id);

		//检查订单是否为付款状态
		if (!$this->checkOrderStatus($uorder_data)) {
			return $this->data->addBody(-140, [], __('订单状态不为待付款状态'), 250);
		}

        $paymentChannelModel = new Payment_ChannelModel();
        $config_row = $paymentChannelModel->getChannelConfig('alipay');
        //修改
        $flag = $this->update_order($uorder_id,$uorder_data['inorder'],'wx_native');
        if($flag == false){
            //报错
            return $this->data->addBody(-140, [], __('支付失败！'), 250);
        }
		$paymentAlipay = new Payment_Alipay($config_row);
        $response = $paymentAlipay->getPayString($uorder_data);
		$this->data->addBody(-140, ['orderString'=> $response], 'success', 200);
	}

	/**
	 * 微信统一下单，返回app （生成预付订单）
	 */
	public function createWXOrder()
	{
		$trade_type = request_string('trade_type');
		$uorder_id = request_string('uorder_id');
		if (empty($uorder_id)) {
			return $this->data->addBody(-140, [], __('无效访问参数'), 250);
		}
		$unionOrderModel = new Union_OrderModel();

		//恢复ConsumeTrade表金额记录，之前数据可能有误
		$uorder_data = $this->unionOrderModel->getOne($uorder_id);

		$urow = $this->unionOrderModel->getByWhere(array('inorder'=>$uorder_data['inorder']));
		$uorder_id_row = array_column($urow,'union_order_id');

		//订单支付的总金额
		$payment_amount = $uorder_data['trade_payment_amount'];
		if($uorder_data['is_presale']==1&&$uorder_data['order_state_id']==1){
			$payment_amount = $uorder_data['presale_deposit'];
		}
		if($uorder_data['is_presale']==1&&$uorder_data['order_state_id']==20){
			$payment_amount = $uorder_data['final_price'];
		}


		$edit_union_order_row = ['union_online_pay_amount'=> $payment_amount,
			'union_cards_pay_amount'=> 0,
			'union_money_pay_amount'=> 0,
            'payment_channel_id'=>$trade_type
		];

		$flag = $this->unionOrderModel->editUnionOrder($uorder_id_row, $edit_union_order_row);

		if ($flag === false) {
			return $this->data->addBody(-140, [], __('交易订单记录初始化失败'), 250);
		}

		//单据详情
		$order_row = array_merge($uorder_data, $edit_union_order_row);
        $pay_code ='';
        $pay_config = Yf_Registry::get('pay_config');
        $pay_type = $pay_config['pay_type'];

        if($trade_type == 'WXAPP'){
            $openid= request_string('openid');
            $appid= request_string('appid','');
            $body = $order_row['trade_title'];
            $total_fee = floatval($order_row['union_online_pay_amount']*100);
            $pay_code = $pay_type['WXAPP'];
            $payment_model = PaymentModel::create($pay_code,array('appid'=>$appid),$openid,$body,$total_fee,$uorder_id);
        }elseif (!empty($pay_type[$trade_type]) && $trade_type!='WXAPP'){
            $pay_code = $pay_type[$trade_type];
            $payment_model = PaymentModel::create($pay_type[$trade_type]);
        }else{
            //PC扫码
            $pay_code = 'wx_native';
            $payment_model = PaymentModel::create($pay_code);
        }
//		if($trade_type == 'APP') //原生BBC
//		{
//			$payment_model = PaymentModel::create('app_wx_native');
//		}
//		elseif($trade_type == 'APPH5')//买家版App
//		{
//		    $payment_model = PaymentModel::create('app_h5_wx_native');
//		}elseif($trade_type == 'APP_H5')//卖家版App
//		{
//            $payment_model = PaymentModel::create('seller_app_h5_wx_native');
//		}elseif($trade_type == 'WXAPP'){ //小程序
//            $openid= request_string('openid');
//            $body = $order_row['trade_title'];
//            $total_fee = floatval($order_row['union_online_pay_amount']*100);
//
//            $payment_model = PaymentModel::create('wxapp',array(),$openid,$body,$total_fee,$uorder_id);
//        }elseif($trade_type == 'IM_WXAPP'){ //IMApp
//            $payment_model = PaymentModel::create('im_wxapp');
//        }else{
//            //PC扫码
//			$payment_model = PaymentModel::create('wx_native');
//		}

        //修改
        $flag = $this->update_order($uorder_id,$urow['inorder'],$pay_code);

        if($flag == false){
            //报错
            return $this->data->addBody(-140, [], __('订单支付失败！请重试'), 250);
        }
		$result = $payment_model->pay($order_row, true);
		$this->data->addBody(-140, ['orderString'=> $result, 'APPID'=> APPID_DEF, 'MCHID'=> MCHID_DEF,'timeStamp'=>(string)time()], 'success', 200);
	}


    /*修改小程序订单状态*/
    public function order_status(){
        $order_id = request_string('order_id');
        $buyer_id = request_string('buyer_id');
        //处理一步回调-通知商城更新订单状态
        //修改订单表中的各种状态

        $Consume_DepositModel = new Consume_DepositModel();
        $rs = $Consume_DepositModel->notifyShop($order_id,$buyer_id);

        $this->data->addBody(-140, $rs, 'success', 200);
    }

    /*小程序充值后更改状态*/
    public function changeOrderStatus(){
        $order_id = request_string('order_id');
        $buyer_id = request_string('buyer_id');
        //修改充值表的状态
        $Consume_DepositModel = new Consume_DepositModel();
        $deposit = $Consume_DepositModel->getOne($order_id);
        if($deposit['deposit_trade_status']==2)
        {
            $rs = 1;
        }else{
            $rs = $Consume_DepositModel->notifyDeposit($order_id,$buyer_id,Payment_ChannelModel::WECHAT_PAY);
        }

        $this->data->addBody(-140, $rs, 'success', 200);
    }
	//收款码支付
	public function qr_pay()
	{
		$check_row = array();
		//支付方式
		$pay_type = request_string('pay_type');
		//付款金额
		$pay_money = request_float('pay_money');
		//收款店铺id
		$shopid = request_int('shopid');
		//付款用户id
		$user_id = Perm::$userId;
		//付款用户name
		$user_name = Perm::$row['user_account'];

		$yf = request_string('yf');

		//获取当前用户的手机号
		$User_InfoModel = new User_InfoModel();
		$user_info = $User_InfoModel->getOne($user_id);

		//获取店铺的信息
		$key = Yf_Registry::get('shop_api_key');
		$url = Yf_Registry::get('shop_api_url');
		$shop_app_id = Yf_Registry::get('shop_app_id');

		$formvars = array();
		$formvars = $_POST;
		$formvars['app_id'] = $shopid;
		$formvars['shop_id'] = $shopid;

		$rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Shop_Info&met=getShopInfoByShopId&typ=json', $url), $formvars);

		//开店用户id
		$shop_uid = $rs['data']['user_id'];
		$shop_uname = $rs['data']['user_name'];

		$Consume_TradeModel = new Consume_TradeModel();

		//1.添加订单记录
		$fix = sprintf('%s-%s', Yf_Registry::get('paycenter_app_id'), date('YmdHis'));
		$order_id = sprintf('%s-%s-%s-%s', 'QP', $shop_uid, $user_id, $fix);

		$da = array();
		$da['consume_trade_id'] = $order_id; //交易订单id
		$da['order_id'] = $order_id;      //商户订单id
		$da['buy_id']  = $user_id;        //买家id
		$da['pay_user_id'] = $user_id;   //付款人id
		$da['buyer_name'] = $user_name;    //付款人名称
		$da['seller_id'] = $shop_uid;     //商家用户id
		$da['seller_name'] = $shop_uname;   //商家用户名称
		$da['payment_channel_id'] = Payment_ChannelModel::MONEY;//支付渠道
		$da['trade_type'] = 1;    //交易类型 1：担保交易 2：直接交易
		$da['order_payment_amount'] = $pay_money;  //总付款额度
		$da['trade_payment_amount'] = $pay_money;  //实付金额，在线支付金额
		$da['trade_remark'] = 0;          //备注
		$da['trade_create_time'] = date('Y-m-d H:i:s');     //创建时间
		$da['trade_pay_time'] = date('Y-m-d H:i:s');        //支付时间
		$da['trade_title'] = '收款码付款';       //标题
		$da['from_app_id'] = Yf_Registry::get('paycenter_app_id');  //订单来源
		$da['order_commission_fee'] = 0;  //佣金
		$da['notify_data'] = '';  //支付回调参数信息

		if($pay_type == 'money')
		{
			$da['order_state_id'] = Union_OrderModel::PAYED;//订单状态id
			$da['trade_payment_money'] = $pay_money;   //余额支付金额
			$da['trade_payment_online'] = 0;  //在线支付金额
		}else
		{
			$da['order_state_id'] = Union_OrderModel::WAIT_PAY;//订单状态id
			$da['trade_payment_money'] = 0;   //余额支付金额
			$da['trade_payment_online'] = $pay_money;  //在线支付金额
		}

		if(!$yf)
		{
			$flag3 =  $Consume_TradeModel->addConsumeTrade($da);
			check_rs($flag3['flag'],$check_row);
		}


		//增加商家冻结资金的金额
		$User_ResourceModel = new User_ResourceModel();
		$user_resource_row = array();
		$user_resource_row['user_money'] = $pay_money;

		if(!$yf)
		{
			$flag2 = $User_ResourceModel->editResource($shop_uid,$user_resource_row,true);
			check_rs($flag2,$check_row);
		}

		switch ($pay_type)
		{
			case 'money':
				//2.根据付款金额修改用户的资金信息
				$user_resource_row = array();
				$user_resource_row['user_money'] = $pay_money*(-1);
				$flag1 = $User_ResourceModel->editResource($user_id,$user_resource_row,true);
				check_rs($flag1,$check_row);

				//短信通知用户消费情况提醒
				$contents = "您有一笔".$pay_money."元的余额支出，可去支付中心查看余额。";
                $result = Sms::send($user_info['user_mobile'],$user_info['area_code']?:86, $contents);
				break;
			case 'alipay':
				$this->qr_alipay($flag3['uorder']);
				break;
			case 'wx_native':
				$this->qr_wx_native($flag3['uorder']);
				break;
			default:
				# code...
				break;

		}
		$flag = is_ok($check_row);
		if ($flag)
		{
			$msg    = 'success';
			$status = 200;
		}
		else
		{
			$Consume_TradeModel->sql->rollBackDb();
			$m      = $Consume_TradeModel->msg->getMessages();
			$msg    = $m ? $m[0] : 'failure';
			$status = 250;
		}
		$this->data->addBody(-140, array('order_id' => $order_id), $msg, $status);
	}

	public function qr_alipay($trade_id)
	{
		//如果订单号为合并订单号，则获取合并订单号的信息
		$Union_OrderModel = new Union_OrderModel();
		$trade_row        = $Union_OrderModel->getOne($trade_id);

		if ($trade_row)
		{
			$Payment = PaymentModel::create('alipay');
			$Payment->pay($trade_row);
		}

	}

	public function qr_wx_native($trade_id)
	{
		//如果订单号为合并订单号，则获取合并订单号的信息
		$Union_OrderModel = new Union_OrderModel();
		$trade_row        = $Union_OrderModel->getOne($trade_id);

		if ($trade_row)
		{
			$trade_row['trade_type'] = $_REQUEST['trade_type'];
			$Payment = PaymentModel::create('wx_native');
			$Payment->pay($trade_row);
		}

	}

    public function test()
    {
        $Consume_TradeModel = new Consume_TradeModel();
//远程改变订单状态
        //根据订单来源，修改订单状态
        $uorder_id = 123456789;
        $order_id = ['DD-11037-200-102-20180421141414-0001'];
        $consume_record = $Consume_TradeModel->getOne($order_id);
        $app_id = $consume_record['app_id'];

        $User_AppModel = new User_AppModel();
        $app_row = $User_AppModel->getOne($app_id);


        $key = $app_row['app_key'];
        $url = $app_row['app_url'];
        $shop_app_id = $app_id;

        $formvars = array();
        $formvars = $_POST;
        $formvars['app_id'] = $shop_app_id;
        $formvars['order_id'] = $order_id;
        $formvars['uorder_id'] = $uorder_id;
        if($consume_record['payment_channel_id'] == Payment_ChannelModel::BAITIAO){
            $formvars['payment_channel_code'] = 'baitiao';
        }else{
            $formvars['payment_channel_code'] = '';
        }

        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=editOrderRowSatus&typ=json', $url), $formvars);
    }


	/**
	 * 大华捷通支付总入口
	 * 
	 * @dateTime  2020-06-04
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function yunshanpc()
	{
		$trade_id = request_string('trade_id');
		$payway_name= request_row('payway_type');
		//如果订单号为合并订单号，则获取合并订单号的信息
		$Union_OrderModel = new Union_OrderModel();
		$trade_row        = $Union_OrderModel->getOne($trade_id);
		//判断订单状态是否为等待付款状态
		if($trade_row['order_state_id'] == Order_StateModel::ORDER_WAIT_PAY)
		{
			$pay_flag = false;
			$pay_user_id = 0;
			//判断当前用户是否是下单者，并且订单状态是否是待付款状态
			if($trade_row['buyer_id'] == Perm::$userId)
			{
				$pay_flag = true;
				$pay_user_id = $trade_row['buyer_id'];
			}
			if($pay_flag)
			{
				if ($trade_row)
				{
					$Payment = PaymentModel::create('yunshanpc');
					$trade_row["pay_yunshapc"] = $payway_name;  // 区分云闪付的支付方式
					$trade_row["Access_mode"] = $this->is_mobile();
					$trade_row['device_type'] = $this->get_device_type();
					if ($trade_row['Access_mode']!= 'PC'){
						$trade_row['device_type'] = $this->get_device_type();
					}else{
						$trade_row['device_type'] = 'PC';
					}
					$QR_code = $Payment->pay($trade_row);
				    $payylresult  = json_decode($QR_code,true);
				   // 如果成功了 ，更新一下数据库信息
				   if($payylresult && $payylresult["errCode"]=="00"){
				       // 接口传数据成功
					    $filerowup = array();
				        $filerowup["vequeryid"] = $payylresult["queryId"] ;
						$filerowup["vedevicetype"]  =  $trade_row['device_type'] ;
						$filerowup["vetranstype"]  =  "PA01" ; // 交易类型
						$Union_OrderModel -> editUnionOrder($trade_row["union_order_id"],$filerowup) ;
						// 更新订单流水
						$inorder  = $trade_row["union_order_id"] ;
				   }else{
						echo"<script>alert('请求失败');history.go(-1);</script>";
						die ;
				   }


					if ($trade_row['Access_mode']=='PC'){
						  $QR_code = json_decode($QR_code,true);
						if ($QR_code['errCode']==00){
							$site_name = Web_ConfigModel::value("site_name") ? Web_ConfigModel::value("site_name") : '';
							$shop_url = Yf_Registry::get('shop_api_url');
        					$login_out_url = Yf_Registry::get('url').'?ctl=Login&met=loginout';
							$this->view->setMet('pc');
							include $this->view->getView();
						}else{
							echo "<script>alert('支付失败');history.go(-2);</script>";
						}
					}elseif ($trade_row['Access_mode']=='mobile_APP'){
						if ($trade_row['pay_yunshapc']=='wx_native'){
							$pay_way = 'wechat';
						}elseif ($trade_row['pay_yunshapc']=='alipay'){
							$pay_way = 'alipay';
						}elseif ($trade_row['pay_yunshapc']=='yunshan_app'){
							$pay_way = 'cloudPay';
						}
						if ($payylresult['errCode']=='00'){
							$QR_code =$payylresult['appPayRequest'];
							$QR_code = json_encode($QR_code);
							include $this->view->getView();
						}else{
							echo"<script>alert('支付失败，请重新支付');history.go(-1);</script>";
							die ;
						}
					}elseif ($trade_row['Access_mode']=='mobile_phone'){
						if ($payylresult['errCode']=='00'){
							$QR_code =$payylresult['appPayRequest'];
							$QR_code = json_encode($QR_code);
							include $this->view->getView();
						}else{
							echo"<script>alert('支付失败，请重新支付');history.go(-1);</script>";
							exit();
						}
						// include $this->view->getView();
					}
				}
				else
				{
					echo "<script>alert('订单信息不存在');history.go(-1);</script>";
					exit();
				}
			}
			else
			{
				echo "<script>alert('当前用户是否是下单者');history.go(-1);</script>";
				exit();
			}
		}
		else
		{
			echo"<script>alert('该订单不是待支付状态');history.go(-1);</script>";
			exit();
		}
	}
	/**
	 * 判断设备系统类型
	 * 
	 * @dateTime  2020-06-6
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function get_device_type()
	{
		//全部变成小写字母
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$type = 'other';
		//分别进行判断
		if(strpos($agent, 'iphone') || strpos($agent, 'ipad'))
		{
			$type = 'ios';
		}

		if(strpos($agent, 'android'))
		{
			$type = 'android';
		}
		return $type;
	}

    /**
     * 判断运行环境
     * 
     * @dateTime  2020-06-6
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
	public  function is_mobile(){
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
					substr($useragent, 0, 4))) {
			if(request_int('is_app')){
				return 'mobile_APP';//嵌入app
			}else{
				return 'mobile_phone';//wap端 H5
			}
		} else {
			return 'PC';
		}
	}

	/**
	 * 开启银联商务时PC支付结果查询
	 * 
	 * @dateTime  2020-06-06
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function pay_status(){
		set_time_limit(0);
		$waybillno = request_string('merOrderId');
		$queryId = request_string('queryId');
		$md5Key = Web_ConfigModel::value('yunshan_key');
		$params = array();
		$params['mer_id'] =  Web_ConfigModel::value('yunshan_merid'); //商户编号
		$params['waybillno'] = $waybillno;//单号 必填,下单时传递的 order_no
		$params['qrtype'] =  'qr';// 
		$params['queryId'] =  $queryId;
		$params['signType'] = 'MD5';
		$mac = self::signsyl($params,$md5Key); // 签名
		$params['mac'] = $mac;
		$url = Web_ConfigModel::value('yunshan_url').'/queryService/UmsWebPayQuery';
		$printJSON = self::create_html($params,$url);
		echo $printJSON; exit();
	}
	public function alipay_status(){
		$key = Yf_Registry::get('shop_api_key');
		$url = Yf_Registry::get('shop_api_url');
		$param['ctl']       = 'Shop';
		$param['met']       = 'alipay_status';
		$param['typ']       = 'json';
		$param['order_id'] = request_row('order_id');
		$param['order_status'] = Order_StateModel::ORDER_PAYED;
		$result = get_url_with_encrypt($key, $url, $param);
		echo json_encode($result);die;
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
}