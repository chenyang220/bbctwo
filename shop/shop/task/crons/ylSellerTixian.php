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
// 通知提现到账指令
$Ve_Ylpays  = new Ve_Ylpays(); // 银联接口信息
//处理商家的提现订单的问题 type = 1 
$sql = "SELECT * FROM `yf_ylfenzhangflowers` where type='1' and actdo='1' and returstatus='1' and  status in('1','3')  LIMIT 0 , 10 "; 
//商家的待提现订单和提现失败的订单
$order_list = $Ve_TxianFlowersModel->sql->getAll($sql);
if($order_list){
    foreach($order_list as $val){
		$id  = $val["id"] ;
       // 如果这个订单退款的了就不发起提现了
		$where = array();
		$where["order_number"] = $val["order_id"];
		$returnorder = $Order_ReturnModel  -> getOneByWhere($where);
		if($returnorder){
			  if($returnorder["return_state"] == "3"){
                //退款不通过
				$sql="update  `yf_ylfenzhangflowers` set  returstatus='1'   ,  returcash='0' where  id = '$id'" ;
				$Ve_TxianFlowersModel->sql->getAll($sql);
			  }else{
				// 退款的订单或者退款中的订单不能提现。
				$sql="update  `yf_ylfenzhangflowers` set  returstatus='2' where  id = '$id' " ;
				$Ve_TxianFlowersModel->sql->getAll($sql);
				continue ;
			  }
		}
            $crontab = array();
            $crontab['actdo'] = 2;
            $Ve_TxianFlowersModel->editInfo($id,$crontab);
		   // 商户号 和 提现的金额的处理功能
		   //调用接口信息
			$params = array();
			$params['mchntNo'] = $val['codmercode']; //固定值 v2
			$params['transType'] = 'singe';
			$params['cleardate'] = $val['cleardate'];
			$params['banktrace'] = $val['banktrace'];
			$params['orderno'] = $val['uorder_id'];
			$params['withdrawType'] = 2;
			$params['timestamp'] =  date("YmdHis");
			$params['signType'] = 'MD5';
			 // 调用接口发送清算通知
			$res = $Ve_Ylpays -> notifytodahua($params);
			//file_put_contents(dirname(__FILE__).'/abs.php', print_r($res,true),FILE_APPEND);
			if($res["status"] == "200"){
				// 成功
			  $sql="update  `yf_ylfenzhangflowers`   set  status='2'  , vemsg='".$res["msg"]."'   where  id = '$id' " ;
			   $Ve_TxianFlowersModel->sql->getAll($sql);
			}elseif($res["status"] == "300"){
			  // 受理中
			  $sql="update  `yf_ylfenzhangflowers`   set  status='4'  , vemsg='".$res["msg"]."'   where  id = '$id' " ;
			  $Ve_TxianFlowersModel->sql->getAll($sql);
			}else{
			   // 通知提现提现失败
			   $sql="update   `yf_ylfenzhangflowers`    set  status='3'  , vemsg='".$res["msg"]."'   where  id = '$id' " ;
			   $Ve_TxianFlowersModel->sql->getAll($sql); 
			}
			// 提现时间
            $curtime = time();
            $sql="update  `yf_ylfenzhangflowers` set  tixiantime='".$curtime."'   where  id = '$id' " ;
		    $Ve_TxianFlowersModel->sql->getAll($sql); 
     }
	return true;
 }
?>