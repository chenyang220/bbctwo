<?php if (!defined('ROOT_PATH')){exit('No Permission');}

/**
 * 云闪付开发接口
 * @author     liutiliang <liutiliang@live.cn>
 */
header('content-type:text/html;charset=utf-8');
class Payment_WxappPayModel implements Payment_Interface
{

	public  $gateway_url = ''; //网关地址
	private $verify_url = ''; //消息验证地址
	private $payment;
	private $order;
	private $parameter;
	private $order_type;

	public  $apid;   // 银联的appid
	public  $md5Key; // 银联的加密秘钥
	public  $openid; //微信支付openid
	public  $appid; //微信支付appid
	/**
	 * 初始化支付配置
	 * 
	 * @dateTime  2020-06-03
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function __construct($payment_row = array(), $order_row = array())
	{
        $this->gateway_url = Web_ConfigModel::value('yunshan_url').'/queryService/UmsWebPayPlugins';
   
        $this->openid = $payment_row['openid'];
        $this->appid = $payment_row['appid'];
		$this->payment['return_url'] = Yf_Registry::get('base_url') . "/paycenter/api/payment/shanyun/return_url.php"; //返回URL
        $this->payment['notify_url'] = Yf_Registry::get('base_url') . "/paycenter/api/payment/shanyun/notify_url.php"; //通知URL

        //提起大话捷通支付
		$this->apid = Web_ConfigModel::value('yunshan_pid');  //支付接口pid 
		$this->merid = Web_ConfigModel::value('yunshan_merid'); //主商户ID
		$this->md5Key = Web_ConfigModel::value('yunshan_key');  //支付接口key
	    $this->mchid = Web_ConfigModel::value('yunshan_mchid'); //APP支付商户号
		$this->yunshan_xcxmchid = Web_ConfigModel::value('yunshan_xcxmchid');  //小程序支付商户号
		$this->yunshan_cbmchid = Web_ConfigModel::value('yunshan_cbmchid'); //C扫B支付商户号
	}
	
	/**
	 * 调起小程序支付
	 * 
	 * @dateTime  2020-06-05
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function pay($order_row)
	{
		$pay_yunshanpc='xcx';	
		$order_row['Access_mode'] =  "xcx"; 
		$order_row["pay_yunshapc"]= "xcx";
		
		$order_row["vepayshopnumer"] = $order_row["xcxpayshopnumer"];
		$this->mchid = 	$this->yunshan_xcxmchid;
		//更新一下支付方式，商城传过来的
		$Union_OrderModel = new Union_OrderModel();
        $filerowup = array();
	    $filerowup["Access_mode"] = "xcx";
	    $filerowup["pay_yunshapc"] = "xcx";
		$Union_OrderModel -> editUnionOrder($order_row["union_order_id"],$filerowup) ;
		return $this->xcx_pay($order_row);
	}
	/**
	 * 请求小程序支付参数
	 * 
	 * @dateTime  2020-06-05
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function xcx_pay($order_row){
		$merid =  $this->merid;
		if ($order_row)
		{
			$this->order = $order_row;
		}
		//1 == order_state_id  待付款状态
		if (1 != $this->order['order_state_id'])
		{
			throw new Exception('订单状态不为待付款状态');
		}

		$vepayshopnumer = explode(",",$order_row["vepayshopnumer"]);
		$vepayshopname = explode(",",$order_row["vepayshopname"]);
		$vepayshopcode = explode(",",$order_row["vepayshopcode"]);
		$vepaytermnumber = explode(",",$order_row["vepaytermnumber"]);
		$veyingcash = explode(",",$order_row["veyingcash"]);
		$verealcash = explode(",",$order_row["verealcash"]);
		$veyfeecash = explode(",",$order_row["veyfeecash"]);
		$out_trade_no = $this->order['union_order_id'];

        $subOrders = array();
		$platformAmount = 0;

		foreach($vepayshopnumer as $k=>$v){
				$SubOrderitem = array();
				$SubOrderitem["mid"] = $v ; // 商户号
				$SubOrderitem["totalAmount"] =  $verealcash[$k]*100 ;   // 应该收的钱
				$subOrders[] = $SubOrderitem ;
				$platformAmount +=  $veyfeecash[$k]*100 ; // 手续费用
		}
		$subOrders = array_filter($subOrders);
		$params = array();
		$params['version'] = 'v2'; //固定值 v2
		$params['order_no'] = $out_trade_no;
		$params['mer_id'] = $merid;
		$params['cod'] = $order_row["union_online_pay_amount"];
		$params['qrtype'] = 'xcx';
		$params['platformAmount'] = $platformAmount;
		$params['subOrders'] = json_encode($subOrders,JSON_UNESCAPED_UNICODE);
		$params['memo'] = $order_row['trade_title'];
		$params['notifyUrl'] = $this->payment['notify_url'];
		$params['returnUrl'] = $this->payment['return_url'];
		$params['subOpenId'] =  $this->openid;
		$params['subAppId'] =  $this->appid;
		$params['signType'] = 'MD5';
		$mac = $this->signsyl($params); // 签名
		$params['mac'] = $mac;
		$html_form =self::create_html($params, $this->gateway_url);
		$html_form = json_decode($html_form,true);
		if (is_array($html_form)){
			if ($html_form['errCode']=='00'){
				$html_form['money'] = $order_row["union_online_pay_amount"];
				$html_form['inorder'] = $order_row["inorder"];
				// 新增退款使用的
			    $Union_OrderModel = new Union_OrderModel();
		       // 接口传数据成功
			    $filerowup = array();
		        $filerowup["vequeryid"] = $html_form["queryId"]; // 退款使用
				$filerowup["vedevicetype"]  = $order_row['device_type'] ;
				$filerowup["vetranstype"]  =  $html_form["targetSys"]; // 交易类型
				$Union_OrderModel -> editUnionOrder($out_trade_no,$filerowup) ;
			    // 更新订单流
				$html_form = json_encode($html_form);
			}
		}else{
			$html_form = json_encode($html_form);
		}
		return  $html_form;
	}

    /**
     * 支付签名
     * 
     * @dateTime  2020-06-05
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
	public function  signsyl($postData){
		ksort($postData);
		$sign = '';
		foreach ($postData as $v) {
		    $sign .= $v;
		}
		$sign = strtoupper(md5($sign . $this->md5Key));
		return $sign;
	}

	/**
	 *
	 * 取得订单支付状态，成功或失败
	 * @param array $param
	 * @return array
	 */
	public function getPayResult($param)
	{
		return $param['trade_status'] == 'TRADE_SUCCESS';
	}

	/**
	 * 通知验证
	 *
	 * @access public
	 */
	public function verifyNotify()
	{
		include_once(LIB_PATH . "/Api/alipay/lib/alipay_notify.class.php");

		$alipayNotify  = new AlipayNotify($this->payment);
		$verify_result = $alipayNotify->verifyNotify();

		return $verify_result;
	}

	/**
	 * 通知验证
	 *
	 * @access public
	 */
	public function verifyReturn()
	{
		include_once(LIB_PATH . "/Api/alipay/lib/alipay_notify.class.php");

		$alipayNotify  = new AlipayNotify($this->payment);
		$verify_result = $alipayNotify->verifyReturn();

		return $verify_result;
	}

	public function sign($parameter)
	{
		$sign_str = '';

		$sign_str = $this->getSignature($parameter, $parameter['key']);

		return $sign_str;
	}

	public function getSignature($parameter, $cp_key = null)
	{
	}

	/**
	 * 制作支付接口的请求地址 发送请求
	 *
	 * @access public
	 */
	public function request()
	{
	}

	/**
	 * 得到异步返回数据
	 *
	 * @access public
	 */
	public function getNotifyData()
	{
		$notify_row = $this->getReturnData();

		$notify_row['deposit_async']         = 1;

		return $notify_row;
	}

	/**
	 * 得到同步返回数据
	 *
	 * @access public
	 */
	public function getReturnData($Consume_TradeModel = null)
	{
		$notify_param = $_REQUEST;
		if ($Consume_TradeModel)
		{
			$notify_row = array();
			$Union_OrderModel = new Union_OrderModel();

			$order_id = $notify_param['orderId'];
			$notify_row = $Union_OrderModel->getOne($order_id);
			$notify_row['order_id'] = $notify_param['orderId'];

		}
		else
		{
			//插入充值记录, 如果同步数据没有,从订单数据中读取过来
			$notify_row = array();
			$notify_row['order_id'] = $notify_param['orderId'];
			$notify_row['deposit_trade_no'] = $notify_param['queryId'];
			$notify_row['deposit_body']          = '';
			$notify_row['deposit_seller_id']  = $notify_param['orderId'];
			$notify_row['deposit_notify_time']  = $notify_param['settleDate'];
			$notify_row['deposit_trade_status']  = $notify_param['respCode'];
			$notify_row['deposit_total_fee']  = $notify_param['txnAmt'];
			$notify_row['deposit_gmt_payment']  = $notify_param['settleDate'];
			$notify_row['deposit_notify_id']  = $notify_param['orderId'];
			$notify_row['deposit_payment_type'] = $notify_param['bizType'];
			$notify_row['deposit_service']     =  'unionpay';
			$notify_row['deposit_sign_type']    = $notify_param['signMethod'];
			$notify_row['deposit_sign']         = $notify_param['signature'];		 
		}

		$notify_row['payment_channel_id']   = Payment_ChannelModel::UNIONPAY;

		return $notify_row;
	}


    /**
     * 请求资源
     * 
     * @dateTime  2020-06-05
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     */
	public function create_html($params, $action) {
		//初始化
		$curl = curl_init();
		//设置抓取的url
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_URL, $action);
		//设置头文件的信息作为数据流输出
		//        curl_setopt($curl, CURLOPT_HEADER, 1);
		//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		//设置post方式提交
		curl_setopt($curl, CURLOPT_POST, 1);
		//设置post数据
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
		//执行命令
		$data = curl_exec($curl);
		//关闭URL请求
		curl_close($curl);
		//显示获得的数据
		return $data;
	}

}

?>
