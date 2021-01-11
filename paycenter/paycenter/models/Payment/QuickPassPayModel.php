<?php if (!defined('ROOT_PATH')){exit('No Permission');}

/**
 * 云闪付开发接口
 * @author     liutiliang <liutiliang@live.cn>
 */
header('content-type:text/html;charset=utf-8');
class Payment_QuickPassPayModel implements Payment_Interface
{

	public  $gateway_url = ''; //网关地址
	private $verify_url = ''; //消息验证地址
	private $payment;
	private $order;
	private $parameter;
	private $order_type;

	public  $apid ;   // 银联的appid
	public  $md5Key ; // 银联的加密秘钥 
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
	 * 支付
	 *
	 * @access public
	 */
	public function pay($order_row)
	{
		$pay_yunshapc = $order_row["pay_yunshapc"] ; //
        if($order_row['Access_mode']=='PC'){
			$order_row["vepayshopnumer"] = $order_row["cbpayshopnumer"];  // C扫B支付商户号、C扫B支付终端号（对应支付类型：mobile_app_pay app）
			$this->mchid = 	$this->yunshan_cbmchid;
		}else if($order_row['Access_mode']=='mobile_phone'){
			$order_row["vepayshopnumer"] = $order_row["cbpayshopnumer"];  // C扫B支付商户号、C扫B支付终端号（对应支付类型：mobile_app_pay app）
			$this->mchid = 	$this->yunshan_cbmchid;
		}else if($order_row['Access_mode']=='mobile_APP'){
			 $order_row["vepayshopnumer"] = $order_row["vepayshopnumer"];  //小程序支付商户号、小程序支付终端号（对应支付类型：xcx_pay）
			 $this->mchid = 	$this->mchid;
		}elseif($order_row['Access_mode']=='xcx'){
		   $order_row["vepayshopnumer"] = $order_row["xcxpayshopnumer"];  // 
		   $this->mchid = 	$this->yunshan_xcxmchid;
		}

		// 更新一下支付方式，商城传过来的
		$Union_OrderModel = new Union_OrderModel();
        $filerowup = array();
	    $filerowup["Access_mode"] = $order_row["Access_mode"];
	    $filerowup["pay_yunshapc"] = $order_row["pay_yunshapc"];
		$Union_OrderModel -> editUnionOrder($order_row["union_order_id"],$filerowup) ;

		// 调用pc端的云闪付接口开发
		if("yunshanpc" ==  $pay_yunshapc){
			return  $this -> yunshan_pc($order_row);
		}elseif ("alipay" ==  $pay_yunshapc){
			return  $this -> alipay_pc($order_row);
		}elseif ("wx_native" ==  $pay_yunshapc){
			return  $this -> wx_native($order_row);
		}elseif ("ios_pay" ==  $pay_yunshapc){
			return  $this -> ios_pay($order_row);
		}elseif ("andriod_pay" ==  $pay_yunshapc){
			return  $this -> andriod_pay($order_row);
		}elseif("yunshan_app" ==  $pay_yunshapc){
           return  $this ->yunshan_app($order_row);
		}else{
			die("缺少必填参数pay_yunshapc");
		}

	}

	/**
	 * 调用银联云闪付app
	 * 
	 * @dateTime  2020-06-7
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public function yunshan_app($order_row){
		$md5Key = $this->md5Key;
		$merid =  $this->merid;
		if ($order_row)
		{
			$this->order = $order_row;
		}
		if (1 != $this->order['order_state_id'])
		{
			throw new Exception('订单状态不为待付款状态');
		}
		$vepayshopnumer = explode(",",$order_row["vepayshopnumer"]);
		$vepayshopname = explode(",",$order_row["vepayshopname"]);
		$vepayshopcode = explode(",",$order_row["vepayshopcode"]);
		$vepaytermnumber = explode(",",$order_row["vepaytermnumber"]);
		$veyingcash = explode(",",$order_row["veyingcash"]);
		$veyingcash = explode(",",$order_row["veyingcash"]);
		$verealcash = explode(",",$order_row["verealcash"]);
		$veyfeecash = explode(",",$order_row["veyfeecash"]);
		$out_trade_no = $this->order['union_order_id'];
		// 新增的功能
		// 新增分账的功能
        $subOrders = array();
		$platformAmount = 0;

		foreach($vepayshopnumer as $k=>$v){
			$SubOrderitem = array();
			$SubOrderitem["mid"] = '89833027311F032' ; // 商户号
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
		$params['qrtype'] = 'app';
		$params['payway'] = 94;
		$params['platformAmount'] = $platformAmount;
		$params['subOrders'] = json_encode($subOrders,JSON_UNESCAPED_UNICODE);
		$params['memo'] = $order_row['trade_title'];
		$params['notifyUrl'] = $this->payment['notify_url'];
		$params['returnUrl'] = $this->payment['return_url'];
		$params['subAppId'] = 'wxcf7c37f82cf75798';
		$params['signType'] = 'MD5';
		$mac = $this -> signsyl($params); // 签名
		$params['mac'] = $mac;

		$html_form =$this-> create_html($params, $this->gateway_url );
		$html_form = json_decode($html_form,true);
		if (is_array($html_form)){
			if ($html_form['errCode']=='00'){
				$html_form['money'] = $order_row["union_online_pay_amount"];
				$html_form['inorder'] = $order_row["inorder"];
				$html_form = json_encode($html_form);
			}
		}else{
			$html_form = json_encode($html_form);
		}
		return  $html_form;

	}
	public function andriod_pay($params=array()){         
		$apid = $this->apid;
		$md5Key = $this->md5Key;

		$mchid =  $this->mchid ;

		$order_row = $params;
		//BEGIN
		if ($order_row)
		{
			$this->order = $order_row;
		}

		 $vepayshopnumer = explode(",",$order_row["vepayshopnumer"]) ;
		 $vepayshopname = explode(",",$order_row["vepayshopname"]) ;
		 $vepayshopcode = explode(",",$order_row["vepayshopcode"]) ;
		 $vepaytermnumber = explode(",",$order_row["vepaytermnumber"]) ;
		 $veyingcash = explode(",",$order_row["veyingcash"]) ;
		 $verealcash = explode(",",$order_row["verealcash"]) ;
		 $veyfeecash = explode(",",$order_row["veyfeecash"]) ;

		$order_row['device_type'] = "android";
		$time = date("YmdHis");
		$out_trade_no = $this->order['union_order_id'];
		$amount = $this->order['union_online_pay_amount']*100; //订单金额

		$params = array();
		$params['device_type'] = $order_row['device_type']; // 设备类型    必填写
		$params['request_time'] =$time; // 交易请求时间   yyyyMMddHHmmss    必填写
		$params['trans_type'] = "PA01";  // 交易类型  必须填写   必填写
		$params['data']  = ""; // 业务数据  json数据   必填写


		$data = array(); // 业务数据
		$data['orderNo'] = $out_trade_no  ; //商户订单号
		$data['paymentType'] = "mobile_app_pay" ; // 支付方式 ，手机APP支付

		$data['paymentMethod'] = "unionpay_wallet"; // 支付渠道---银联钱包

		$data['settlementNo'] = "";  // 编码,由大华捷通分配
		$data['currency'] = "cny"  ; // 货币代码
		$data['orderGoodRemark'] = $order_row["trade_title"];   // 商品描述
		$data['notifyUrl'] = $this->payment['notify_url'];  // 前端支付通知地址

		$amount  = $order_row["union_online_pay_amount"]*100 ;
		$data['orderAmount']  = $amount ; // 支付金额，单位 分
		$data['orderDesc']  = "31" ; // 备注字段
		$data['customField'] = $order_row["inorder"];  // 用户自定义字段
		$data['couponAmount'] = "0"; // 优惠金额
		$data['couponId']  = "";  // 优惠券编号id
		$data['couponDesc'] = ""; // 优惠券 描述
		$data['settlementInfo'] = ""; // 结算信息
		$data['orderKind'] = "01" ; // 结算类型  ， 01 结算 02  收款
		$data['orderType']  = "01" ;  // 00其他消费, 01门店收款,02电商收款,03代收货款, 04物业缴费,05物流运费
		$data['appId'] = $apid;
		

			// 新增的功能
		// 新增分账的功能
        $subOrders = array();
		$platformAmount = 0 ;
		 foreach($vepayshopnumer as $k=>$v){
				$SubOrderitem = array();
				$SubOrderitem["mid"] =$v ; // 商户号
				$SubOrderitem["totalAmount"] =  $verealcash[$k]*100 ;   // 应该收的钱
				$subOrders[] = $SubOrderitem ;


				$platformAmount +=  $veyfeecash[$k]*100 ; // 手续费用

		}
        $data['subOrders'] = $subOrders;




		$data = array_filter($data);

		$data['platformAmount'] = "$platformAmount";



		$params['data'] = json_encode($data,JSON_UNESCAPED_UNICODE);
		$params['token']  = "1";
		$mac = $this -> signsyl($params); // 签名
		$params['mac']  = $mac;  // 签名  以上参数值和密钥拼接后的MD5值   必填写
		$params['callType']='PHP';
		$html_form =$this-> create_html($params, $this->url );
		$html_form = json_decode($html_form,true);
		if (is_array($html_form)){
			if ($html_form['messageCode']=='00'){
				$html_form['money'] = $order_row["union_online_pay_amount"];
				$html_form['inorder'] = $order_row["inorder"];
				$html_form = json_encode($html_form);
			}
		}else{
			$html_form = json_encode($html_form);
		}
		return  $html_form;
	}

	public function ios_pay($params=array()){

		$apid = $this->apid;
		$md5Key = $this->md5Key;

		$mchid =  $this->mchid ;



		$order_row = $params;
		//BEGIN
		if ($order_row)
		{
			$this->order = $order_row;
		}

		 $vepayshopnumer = explode(",",$order_row["vepayshopnumer"]) ;
		  $vepayshopname = explode(",",$order_row["vepayshopname"]) ;
		  $vepayshopcode = explode(",",$order_row["vepayshopcode"]) ;
		  $vepaytermnumber = explode(",",$order_row["vepaytermnumber"]) ;

		  $veyingcash = explode(",",$order_row["veyingcash"]) ;
		  $verealcash = explode(",",$order_row["verealcash"]) ;
		  $veyfeecash = explode(",",$order_row["veyfeecash"]) ;




		$order_row['device_type'] = "android";
		$time = date("YmdHis");
		$out_trade_no = $this->order['union_order_id'];
		$amount = $this->order['union_online_pay_amount']*100; //订单金额

		$params = array();
		$params['device_type'] = $order_row['device_type']; // 设备类型    必填写
		$params['request_time'] =$time; // 交易请求时间   yyyyMMddHHmmss    必填写
		$params['trans_type'] = "PA01";  // 交易类型  必须填写   必填写
		$params['data']  = ""; // 业务数据  json数据   必填写


		$data = array(); // 业务数据
		$data['orderNo'] = $out_trade_no; //商户订单号
		$data['paymentType'] = "mobile_apple_pay"; // 支付方式 ，手机APP支付

		$data['paymentMethod'] = "mobile_apple_pay"; // 支付渠道---银联钱包

		$data['settlementNo'] = "";  // 编码,由大华捷通分配
		$data['currency'] = "cny"  ; // 货币代码
		$data['orderGoodRemark'] = $order_row["trade_title"];   // 商品描述
		$data['notifyUrl'] = $this->payment['notify_url'];  // 前端支付通知地址

		$amount  = $order_row["union_online_pay_amount"]*100 ;
		$data['orderAmount']  = $amount ; // 支付金额，单位 分
		$data['orderDesc']  = "31" ; // 备注字段
		$data['customField'] = $order_row["inorder"];  // 用户自定义字段
		$data['couponAmount'] = "0"; // 优惠金额
		$data['couponId']  = "";  // 优惠券编号id
		$data['couponDesc'] = ""; // 优惠券 描述
		$data['settlementInfo'] = ""; // 结算信息
		$data['orderKind'] = "01" ; // 结算类型  ， 01 结算 02  收款
		$data['orderType']  = "01" ;  // 00其他消费, 01门店收款,02电商收款,03代收货款, 04物业缴费,05物流运费
		$data['appId'] = $apid;

		// 新增的功能
		// 新增分账的功能
        $subOrders = array();
		$platformAmount  = 0 ; 
		 foreach($vepayshopnumer as $k=>$v){
				$SubOrderitem = array();
				$SubOrderitem["mid"] =$v ; // 商户号
				$SubOrderitem["totalAmount"] =  $verealcash[$k]*100 ;   // 应该收的钱
				$subOrders[] = $SubOrderitem ;

				$platformAmount +=  $veyfeecash[$k]*100 ; // 手续费用

		}
        $data['subOrders'] = $subOrders;
       

		$data = array_filter($data);

		 $data['platformAmount'] = "$platformAmount";



		$params['data'] = json_encode($data,JSON_UNESCAPED_UNICODE);
		$params['token']  = "1";
		$mac = $this -> signsyl($params); // 签名
		$params['mac']  = $mac;  // 签名  以上参数值和密钥拼接后的MD5值   必填写
		$params['callType']='PHP';
		$html_form =$this-> create_html($params, $this->url);
		$html_form = json_decode($html_form,true);
		if (is_array($html_form)){
			if ($html_form['messageCode']=='0053'){
				$html_form['money'] = $order_row["union_online_pay_amount"];
				$html_form['inorder'] = $order_row["inorder"];
				$html_form = json_encode($html_form);
			}
		}else{
			$html_form = json_encode($html_form);
		}
		return  $html_form;
	}


	/**
	 * app微信支付
	 * 
	 * @dateTime  2020-06-04
	 * @author fzh
	 * @copyright https://www.yuanfeng.cn
	 * @license   仅限本公司授权用户使用。
	 * @version   3.8.1
	 */
	public  function  wx_native($order_row)
	{
		$md5Key = $this->md5Key;
		$merid =  $this->merid;
		if ($order_row)
		{
			$this->order = $order_row;
		}
		if (1 != $this->order['order_state_id'])
		{
			throw new Exception('订单状态不为待付款状态');
		}
		$vepayshopnumer = explode(",",$order_row["vepayshopnumer"]);
		$vepayshopname = explode(",",$order_row["vepayshopname"]);
		$vepayshopcode = explode(",",$order_row["vepayshopcode"]);
		$vepaytermnumber = explode(",",$order_row["vepaytermnumber"]);
		$veyingcash = explode(",",$order_row["veyingcash"]);
		$veyingcash = explode(",",$order_row["veyingcash"]);
		$verealcash = explode(",",$order_row["verealcash"]);
		$veyfeecash = explode(",",$order_row["veyfeecash"]);
		$out_trade_no = $this->order['union_order_id'];
		// 新增的功能
		// 新增分账的功能
        $subOrders = array();
		$platformAmount = 0;

		foreach($vepayshopnumer as $k=>$v){
			$SubOrderitem = array();
			$SubOrderitem["mid"] = $v; // 商户号
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
		$params['qrtype'] = 'app';
		$params['payway'] = 97;
		$params['platformAmount'] = $platformAmount;
		$params['subOrders'] = json_encode($subOrders,JSON_UNESCAPED_UNICODE);
		$params['memo'] = $order_row['trade_title'];
		$params['notifyUrl'] = $this->payment['notify_url'];
		$params['returnUrl'] = $this->payment['return_url'];
		$params['subAppId'] = 'wxe7d4042bd881baeb';
		$params['signType'] = 'MD5';
		$mac = $this -> signsyl($params); // 签名
		$params['mac'] = $mac;

		$html_form =$this-> create_html($params, $this->gateway_url );
		$html_form = json_decode($html_form,true);
		if (is_array($html_form)){
			if ($html_form['errCode']=='00'){
				$html_form['money'] = $order_row["union_online_pay_amount"];
				$html_form['inorder'] = $order_row["inorder"];
				$html_form = json_encode($html_form);
			}
		}else{
			$html_form = json_encode($html_form);
		}
		return  $html_form;
	}

	public  function  alipay_pc($order_row){
        header("content-type:text/html;charset=utf-8");
		$md5Key = $this->md5Key;
		$merid =  $this->merid;
		if ($order_row)
		{
			$this->order = $order_row;
		}
		if (1 != $this->order['order_state_id'])
		{
			throw new Exception('订单状态不为待付款状态');
		}
		$vepayshopnumer = explode(",",$order_row["vepayshopnumer"]);
		$vepayshopname = explode(",",$order_row["vepayshopname"]);
		$vepayshopcode = explode(",",$order_row["vepayshopcode"]);
		$vepaytermnumber = explode(",",$order_row["vepaytermnumber"]);
		$veyingcash = explode(",",$order_row["veyingcash"]);
		$veyingcash = explode(",",$order_row["veyingcash"]);
		$verealcash = explode(",",$order_row["verealcash"]);
		$veyfeecash = explode(",",$order_row["veyfeecash"]);

		$time = date("YmdHis");
		$out_trade_no = $this->order['union_order_id'];
		$amount = $this->order['union_online_pay_amount']*100; //订单金额
		// 新增的功能
		// 新增分账的功能
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
		$params['qrtype'] = 'app'; //app
		//$params['qrtype'] = 'h5'; //h5
		$params['payway'] = 98;
		$params['platformAmount'] = $platformAmount;
		$params['subOrders'] = json_encode($subOrders,JSON_UNESCAPED_UNICODE);
		$params['memo'] = $order_row['trade_title'];
		$params['notifyUrl'] = $this->payment['notify_url'];
		$params['returnUrl'] = $this->payment['return_url'];
		$params['signType'] = 'MD5';
		$mac = $this -> signsyl($params); // 签名
		$params['mac'] = $mac;
		$html_form =$this-> create_html($params, $this->gateway_url);
        
        $html_form = json_decode($html_form,true);
		//匹配出a标签内容
	  /*$pattern = "/href=\"([^\"]+)/";
		preg_match($pattern,$html_form,$hrefarray);
		$toAlipay = $hrefarray[1];
		$toAlipay = html_entity_decode($toAlipay);
 		header("Location:{$toAlipay}");
		exit;*/
		if (is_array($html_form)){
			if ($html_form['errCode']=='00'){
				$html_form['money'] = $order_row["union_online_pay_amount"];
				$html_form['inorder'] = $order_row["inorder"];
				$html_form = json_encode($html_form);
			}
		}else{
			$html_form = json_encode($html_form);
		}
		return  $html_form;
	}


	public  function  yunshan_pc($order_row){
		$md5Key = $this->md5Key;  
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
		if ($order_row['Access_mode']=='PC'){
			$data['paymentMethod'] = "unionpay_online"; // 支付渠道---银联在线
		}else {
			$data['paymentMethod'] = "unionpay_wallet"; // 支付渠道---银联钱包
		}

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
        //支付方式选择
		if ($this->order['Access_mode']=='PC'){
			$params['qrtype'] = 'qr'; // 支付方式 ，线下扫码被扫描
		}else if($this->order['Access_mode']=='mobile_phone'){
			$params['qrtype'] = 'gzh'; // 支付方式 ，手机网页支付
		}else if($this->order['Access_mode']=='mobile_APP'){
			$params['qrtype'] = 'app';; // 支付方式 ，手机APP支付
		}
		$params['platformAmount'] = $platformAmount;
		$params['subOrders'] = json_encode($subOrders,JSON_UNESCAPED_UNICODE);
		$params['memo'] = $order_row['trade_title'];
		$params['notifyUrl'] = $this->payment['notify_url'];
		$params['returnUrl'] = $this->payment['return_url'];
		$params['signType'] = 'MD5';
		$mac = $this -> signsyl($params); // 签名
		$params['mac'] = $mac;
		$html_form =$this-> create_html($params, $this->gateway_url );
		if ($params['qrtype'] == 'qr') {
		    $html_form = json_decode($html_form,true);
		}
		if (is_array($html_form)){
			if ($html_form['errCode']=='00'){
				$html_form['money'] = $order_row["union_online_pay_amount"];
				$html_form['inorder'] = $order_row["inorder"];
				$html_form = json_encode($html_form);
			}
		}else{
			$html_form = json_encode($html_form);
		}
		return  $html_form;
	}


	public function  signsyl($postData=array()){

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

	function create_html($params, $action) {
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
