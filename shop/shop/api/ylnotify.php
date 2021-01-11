<?php
	header('content-type:text/html;charset=utf-8');
	ini_set('max_execution_time','0');
    //接收银联推送的对账单
	require_once '../configs/config.ini.php';
	//查找店铺信息
	error_log(var_export($_POST,true),3,__FILE__.'6.log');
	$Order_BaseModel = new Order_BaseModel();
    $AccountCheckingModel = new Ve_AccountCheckingModel();
	$context = $_POST['context'];
	$mac = $_POST['mac'];
	$xml = simplexml_load_string($context);
	$xmljson = json_encode($xml);
	$xmlarr =json_decode($xmljson,true);
    $account_list = $xmlarr['acctRequest'];
	foreach ($account_list as $account) {
		$account_info = array();
	    $account_info['tracedate'] = $account['tracedate'];
	    $account_info['tracetime'] = $account['tracetime'];
	    $account_info['orderno'] = $account['orderno'];
	    $account_info['ordertype'] = $account['ordertype'];
	    $account_info['txnamt'] = $account['txnamt'];
	    $account_info['cod'] = $account['cod'];
	    $account_info['fee'] = $account['fee'];
	    $account_info['payway'] = $account['payway'];
	    $account_info['settledate'] = $account['settledate'];
	    $account_info['settleamount'] = $account['settleamount'];
	    $account_info['charge'] = $account['charge'];
	    $account_info['cardid'] = $account['cardid'];
	    $account_info['bankname'] = $account['bankname'];
	    $account_info['settletermid'] = $account['settletermid'];
	    $account_info['termid'] = $account['termid'];
	    $account_info['postrace'] = $account['postrace'];
	    $account_info['banktrace'] = $account['banktrace'];
	    $account_info['txntype'] = $account['txntype'];
	    $account_info['codmername'] = $account['clearcomname'];
	    $account_info['codmercode'] = $account['clearcomcode'];
	    $account_info['cardtype'] = $account['cardtype'];
	    $account_info['status'] = 0;
        
        //更改订单状态
        $payment_other_number = $account['orderno'];
        $clearcomcode = $account['clearcomcode'];
	    $sql = "select * from  `yf_order_base`  where payment_other_number= '" . $payment_other_number ."' and (vepayshopnumer='" . $clearcomcode . "' or cbpayshopnumer = '" . $clearcomcode . "' or xcxpayshopnumer = '" .$clearcomcode . "') limit 1";
	    $result = $Order_BaseModel->sql->getAll($sql);
	    if (!empty($result)) {
	     $order_info = current($result);
	     $order_id = $order_info['order_id'];
	     $edit_cond = array();
	     $edit_cond['cleardate'] = $account['settledate'];
	     $edit_cond['settleamount'] = $account['settleamount'];
	     $edit_cond['banktrace'] = $account['banktrace'];
	     $edit_cond['vepayOrderNo'] = $payment_other_number;
	     $edit_cond['charge'] = $account['charge'];
	     $edit_cond['codmercode'] = $clearcomcode;
	     $Order_BaseModel->editBase($order_id,$edit_cond);
	    }
	    $AccountCheckingModel ->addPayInfo($account_info,true);
	}

	echo '<acctResponse> <response_code>00</response_code> <response_msg>提交成功</response_msg> </acctResponse>';
	exit();
