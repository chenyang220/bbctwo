<?php
if (!defined('ROOT_PATH'))
{
	if (is_file('../../../shop/configs/config.ini.php'))
	{
		require_once '../../../shop/configs/config.ini.php';
	}
	else
	{
		die('请先运行index.php,生成应用程序框架结构！');
	}

	//不会重复包含, 否则会死循环: web调用不到此处, 通过crontab调用
	$Base_CronModel = new Base_CronModel();
	$rows = $Base_CronModel->checkTask(); //并非指执行自己, 将所有需要执行的都执行掉, 如果自己达到执行条件,也不执行.
}
// 银联通知结算计划任务信息
Yf_Log::log(__FILE__, Yf_Log::INFO, 'crontab');

$file_name_row = pathinfo(__FILE__);
$crontab_file = $file_name_row['basename'];
fb($crontab_file);
//执行任务

$Shop_BaseModel = new Shop_BaseModel();
//查找店铺信息
$Order_BaseModel = new Order_BaseModel();
// 提现的流水记录信息
$Ve_TxianFlowersModel  = new Ve_TxianFlowersModel();
$Ve_AccountCheckingModel  = new Ve_AccountCheckingModel;
$Order_ReturnModel = new Order_ReturnModel();
// 当天的开始时间 ，和结束时间
$starttime = mktime(14,3,0,date("m"),date("d"),date("Y"));
$endtime = mktime(16,20,0,date("m"),date("d"),date("Y"));
$curtime = time();
//定时任务执行时间、为保证能够在可提现时间段处理 给予时间限制
if($curtime>=$starttime&&$curtime<=$endtime){
}else{
    return  true ;
}

// 平台支付参数
$yunshan_key =Web_ConfigModel::value('yunshan_key');
$yunshantixian_key =Web_ConfigModel::value('yunshantixian_key');
$yunshan_mchid =Web_ConfigModel::value('yunshan_mchid');
$yunshan_merid =Web_ConfigModel::value('yunshan_merid');
$yunshan_cbmchid =Web_ConfigModel::value('yunshan_cbmchid');
$yunshan_xcxmchid = Web_ConfigModel::value('yunshan_xcxmchid');

if (!$yunshan_key || !$yunshantixian_key || !$yunshan_mchid || !$yunshan_merid || !$yunshan_cbmchid || !$yunshan_xcxmchid) {
	return ;
}else{
	$mchid = '';
	$mchid .=  '"' .$yunshan_mchid .'",';
	$mchid .=  '"' .$yunshan_cbmchid.'",';
	$mchid .=  '"' .$yunshan_xcxmchid .'"';
}
$Ve_Ylpays  = new Ve_Ylpays(); // 银联接口信息
$cond_row = array();
$cond_row['vedoups'] = 1; //计划任务未处理
$cond_row['settle_status'] = 1; //可结算订单
$order_list = $Order_BaseModel->listByWhere($cond_row,array(),1,10);
$order_list = $order_list['items'];
 // 查询数据信息，把合并订单信息拆单出来到提现记录中去
 if($order_list){
    foreach($order_list as $k => $v){
			$vepayOrderNo  = $v["vepayOrderNo"]; //合并支付单号
			$cleardate = $v["cleardate"] ; // 清算日期
			$banktrace = $v["banktrace"] ; //检索参考号
			$order_id = $v["order_id"] ; //检索参考号
			$codmercode  =  $v["codmercode"] ; //结算商户号
			$params = array();
			$params["uorder_id"] = $vepayOrderNo ;     //   订单号 
			$params["cleardate"] = $cleardate ;   //  清算日期（支付通知中有）
			$params["banktrace"] = $banktrace ;   // 银联交易参考号 （支付通知中有）
			$params["codmercode"] = $codmercode  ;  //提现商户号
			$params["transType"] = "singe";  //  固定值  singe
			$params["order_id"] = $order_id;  //  订单号
			$params["createtime"] = time();  //  创建时间
			$params["type"] = 1;  //  1，商家 2 平台
			  // 更新计划任务表示已经处理过了
			  // 等到下一次轮训处理
			if(!$vepayOrderNo || !$cleardate || !$banktrace || !$codmercode){
			   continue ;
			}
			$flag = $Ve_TxianFlowersModel->addInfo($params,true);
			if ($flag) {
	            $edit_order = array();
				$edit_order['vedoups'] = 2;
				$edit_order['settle_status'] = 2;
				$Order_BaseModel->editBase($order_id,$edit_order);
	        }

	        //支付单上的所有订单 都已经结算，则平台佣金 可以结算了 
	        $cond_row = array();
	        $cond_row['payment_other_number'] = $vepayOrderNo;
	        $order_list_all = $Order_BaseModel->getByWhere($cond_row);
	        $isruning = false;
	        $order_id = '';
	        $rs_row = [];
	        foreach ($order_list_all as $order_list) {
	        	if ($order_list['settle_status'] == 2 && $order_list['vedoups'] ==2) {
	        	    $order_id .= $order_list['order_id'] . ',';
	        		$isruning  = true;
	        	}else{
                    $isruning  = false;
	        	}
	        	check_rs($isruning,$rs_row);
	        }
	        $order_id = rtrim($order_id,',');
	        $flag = is_ok($rs_row);
	        if ($flag == false) {
	        	continue;
	        }
            //添加平台提现
	        $sql = 'select * from yf_veaccount_checking where 1 =1 and orderno = "' . $vepayOrderNo . '" and codmercode in (' .$mchid .')';
	        $ylPlatamTixian = $Ve_AccountCheckingModel->sql->getAll($sql);
	        $ylPlatamTixian = current($ylPlatamTixian);
            $params["type"] = 2;  //  1，商家 2 平台
            $params["order_id"] = $order_id;  //  1，商家 2 平台
            $params["codmercode"] = $ylPlatamTixian['codmercode'];  //提现商户号
            $Ve_TxianFlowersModel->addInfo($params,true);
    }
  }

?>