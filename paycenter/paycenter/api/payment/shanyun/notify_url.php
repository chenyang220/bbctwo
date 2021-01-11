<?php
	require_once '../../../configs/config.ini.php';
	header('content-type:text/html;charset=utf-8');
	$notify_str = file_get_contents("php://input");
	Yf_Log::log($notify_str, Yf_Log::INFO, 'yunshanfu');
	//$notify_str = 'context={"header":{"version":"V2.1.2","transtype":"P033","employno":"01","termid":"H0001622","request_time":"20200709174333","shopid":"89833027311F027","wlid":"cf07123d942b4930b2fbf23af7816f9d"},"body":{"orderno":"U20200709033621296","cod":"0.10","payway":"97","banktrace":"15540785547N","postrace":"155407","tracetime":"20200709153637","cardid":"","signflag":"0","dssn":"","dsname":"","memo":"Ci日本进口儿童牙刷小头软毛小胖子婴幼儿宝宝乳牙","queryId":"20200709153623957788","cleartime":"20200709"}}&mac=934B7C0948E2BD44577DF6049C9B3DC1';
	if ($notify_str == '') {
		echo "参数传输失败";die;
	}
    //对返回字符串进行修改
    $notify_str = explode('&', $notify_str);
	$mac = $notify_str[1];
	$notify_str = pos($notify_str);
    $notify_str = explode('=', $notify_str);
    $notify_str = end($notify_str);
	$notify_str = json_decode($notify_str,true);
	extract($notify_str);
    extract($body);
	/**
		//运单号
		$orderno;
		//代收款金额(单位（元），精确到小数点后两位，例如：100.32 元，直接填写 100.32) 
	    $cod;       
	    //代收款支付方式 (97 微信 98 支付宝 94 银联二维码 CP chinapay 网关支付UP unionpay 网关支付 BBunionpay 企业网银支付)
	    $payway;
	    //系统参考号  当刷卡交易时必需要有此项    
	    $banktrace;
	    //POS 机的流水号  当刷卡交易时必需要有此项
	    $postrace;
	    //交易时间 在收单系统完成金融交易的具体时间
	    $tracetime;
	    //卡号/支付号  现金支付时空格补充
	    $cardid;
	    //本人签收标记 0:本人签收 1：他人签收
	    $signflag;
	    //签收号
	    $dssn;
	    //签收人
	    $dsname;
	    //备注字段
	    $memo;
	    //查询流水号  查询和退款使用
	    $queryId;
	**/
	$order_model = new Union_OrderModel();
	$union_order = $order_model ->getOne($orderno);
	$editu = array();
	$editu["notify_data"] =  $body;
	$editu["vepayOrderNo"] = $orderno;
	$editu["vepayChannel"] = $payway;
	$editu["banktrace"] =  $banktrace;
	$editu["vecando"] =  2; //付款后可以进入计划任务
	$editu["cleardata"] = $union_order['cleardata']; //$cleartime; //付款后可以进入计划任务
	// $editu["postrace"] =  $postrace;
	// $editu["tracetime"] =  $tracetime;
	// $editu["cardid"] =  $cardid;
	// $editu["signflag"] =  $signflag;
	// $editu["dssn"] =  $dssn;
	// $editu["dsname"] =  $dsname; 
	// $editu["memo"] =  $memo; 
	$editu["vequeryid"] = $union_order['vequeryid'];//$queryId; // 查询支付单号
	//查找此支付单的交易类型
    $editu['order_state_id'] = Order_StateModel::ORDER_PAYED;
    $editu['pay_time'] = date('Y-m-d H:i:s');

    $order_model->editUnionOrder($orderno, $editu);
	//修改订单表中的各种状态
	$Consume_DepositModel = new Consume_DepositModel();
	$dirname = APP_PATH . '/api/payment/shanyun/lock.txt';
	$fp = fopen($dirname, 'r');
	$rs = false;
	if (flock($fp,LOCK_EX|LOCK_NB)) {
	    $rs = $Consume_DepositModel->notifyShopYl($orderno, $data['buyer_id'],$editu);
	    flock($fp,LOCK_UN);
	}
	fclose($fp);
	
	$yunshan_key =Web_ConfigModel::value('yunshan_key');
	if ($rs) {
	    $notify_str['header']['response_code'] = '00';
	    $notify_str['header']['response_msg'] = '交易成功';
	    $signJson = json_encode($notify_str,JSON_UNESCAPED_UNICODE);
		$signJsonKey = $signJson.$yunshan_key;
		$md5Val = md5($signJsonKey);
		echo $signJson .'&mac='.$md5Val; exit();
	}else{
	    $notify_str['header']['response_code'] = 'H4';
	    $notify_str['header']['response_msg'] = '签收失败 ';
	    $signJson = json_encode($notify_str,JSON_UNESCAPED_UNICODE);
		$signJsonKey = $signJson.$yunshan_key;
		$md5Val = md5($signJsonKey);
		echo $signJson .'&mac='.$md5Val; exit();
	}

?>
