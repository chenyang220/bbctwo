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
	//return ;
}


// Yf_Log::log(__FILE__, Yf_Log::INFO, 'crontab');

$crontab_file = basename(__FILE__);

//fb($crontab_file);
//执行任务
//查找出所有用户id
$key = Yf_Registry::get('paycenter_api_key');
$url = Yf_Registry::get('paycenter_api_url');
$paycenter_app_id = Yf_Registry::get('paycenter_app_id');
$formvars = array();
$formvars['app_id'] = $paycenter_app_id;

$rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Paycen_PayInfo&met=getBtWarnList&typ=json', $url), $formvars);
$message = new MessageModel();
$code = 'credit return waring';
$message_type = MessageModel::USER_MESSAGE;
foreach (@$rs['data']['items'] as $item) {
	$message_user_id = $item['pay_user_id'];
	$message_user_name = $item['user_nickname'];
	$end_time = $item['repayment_time'].__('到期');
	$av_amount= $item['repay_price'];

	$message->sendMessage($code, $message_user_id, $message_user_name, $order_id = NULL, $shop_name = NULL, $message_mold = 0, $message_type,$end_time,$common_id=NULL,$goods_id=NULL,$des=NULL, $start_time = Null,$goods_name=NULL,$av_amount);
}

$flag = true;
return $flag;
?>