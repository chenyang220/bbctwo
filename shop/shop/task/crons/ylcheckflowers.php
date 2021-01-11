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

	//终止执行下面内容, 否则会执行两次 
	//  return ;
}
ini_set('max_execution_time','0');

$Shop_BaseModel = new Shop_BaseModel();
//查找店铺信息
$Order_BaseModel = new Order_BaseModel();
// 提现的流水记录信息
$Ve_TxianFlowersModel  = new Ve_TxianFlowersModel();
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
}

// 提现的流水计划任务信息
Yf_Log::log(__FILE__, Yf_Log::INFO, 'crontab');

$Ve_Ylpays  = new Ve_Ylpays(); // 银联接口信息
// 提现的流水记录信息
$Ve_TxianFlowersModel  = new Ve_TxianFlowersModel();
// 查询提现记录是4  的流水表信息
$sql = "SELECT * FROM  `yf_ylfenzhangflowers`  where  status in ('3','4')   and  veischecks='1'    LIMIT 0 , 20 " ; 
$listuorder = $Order_BaseModel->sql->getAll($sql);
if($listuorder){
	foreach($listuorder as $k =>$v){
		   $editid =  $v["id"];
		   $orderids = $v["order_id"];
		   $type = $v["type"];
		   $pm = array();
		   $pm["mchntNo"] = $v["codmercode"]; //结算商户号
		   $pm["cleardate"] = $v["cleardate"];
		   $pm["banktrace"] = $v["banktrace"];
		   $pm["orderno"] = $v["uorder_id"];
	       $res = $Ve_Ylpays -> checkFlowsStatus($pm);
		   if(!isset($res["detail"])){
		     continue ;
		   }
	       $details =  $res["detail"];
		   foreach($details  as $detail){
			   if(empty($detail)){
			     continue ;
			   }
			   $status = $detail["status"] ;     //  4 成功，5 失败，0 处理中，1 待处理
			   $transAmt = $detail["transAmt"] ;  // 提现金额
			   $desc = $detail["desc"] ;
			   $mchntNo = $detail["mchntNo"] ;
			   $returntranstype = $detail["transtype"] ; // 提现处理方式
		   }
			 if($status == 4){
				$verisduai = "2" ;
				$vemsg = $desc  ;
			 }elseif($status == 5){
				$verisduai = "3" ;
				$vemsg = $desc  ;
			 }elseif($status == 0){
				$verisduai = "4" ;
				$vemsg = $desc  ;
			 }elseif($status == 1){
				 $verisduai = "4" ;
				 $vemsg = $desc;
			 }else{
			    continue ;
			 }

			  // 更新提现流水
			   $sql="update  `yf_ylfenzhangflowers`   set  status='". $verisduai ."'  , vemsg='".$vemsg."'  , returntranstype='".$returntranstype."'     where  id = '$editid'   " ;
			   $Ve_TxianFlowersModel->sql->getAll($sql);
			  // 更新对应的订单信息
			  // 如果是商家订单就更新订单的状态信息
			  if($type == "1"){
				$orderid =  $orderids ;
				$filerow   =  array();
				$filerow["verisduai"] = $verisduai ; // 清算成功
				$Order_BaseModel->editBase($orderid,$filerow);
			  }
	}
}


