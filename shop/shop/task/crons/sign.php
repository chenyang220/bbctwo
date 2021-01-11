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
	return ;
}
Yf_Log::log(__FILE__, Yf_Log::INFO, 'crontab');
$User_InfoModel = new User_InfoModel();
$Points_LogModel = new  Points_LogModel();
$start_time  = date('Y-m-d H:i:s',strtotime(date("Y-m-d",strtotime("-1 day"))));
$end_time  = date('Y-m-d H:i:s', strtotime(date("Y-m-d",strtotime("-1 day"))) + 24 * 60 * 60-1 );
$Yesterday_Log = $Points_LogModel->getByWhere(array('points_log_time:>='=>$start_time,'points_log_time:<='=>$end_time,'class_id'=>9));
$user_id_arr = array_unique(array_column($Yesterday_Log, 'user_id'));
$points_log_points_arr = array_unique(array_column($Yesterday_Log, 'points_log_points','user_id'));
$user_id_str = implode(",",$user_id_arr);
if ($user_id_arr) {
	$sql = "SELECT yf_user_info.user_id FROM yf_user_info WHERE  user_id NOT IN  (" . $user_id_str . ")";
} else {
	$sql = "SELECT yf_user_info.user_id FROM yf_user_info WHERE 1=1";
}
$User_Info_arr = $User_InfoModel->sql->getAll($sql);
foreach ($User_Info_arr as $key => $user_id) {
		$User_InfoModel->editInfo($user_id,array("user_sign_day"=>0));
}

