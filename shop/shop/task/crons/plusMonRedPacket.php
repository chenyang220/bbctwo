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

//    终止执行下面内容, 否则会执行两次
    return ;
}

Yf_Log::log(__FILE__, Yf_Log::INFO, 'crontab');

$file_name_row = pathinfo(__FILE__);
$crontab_file = $file_name_row['basename'];

/**
 * plus会员红包，系统每月在会员开通日下发会员权益红包
 *
 *///判断会员开关
$openFlag = Web_ConfigModel::value('plus_switch');
$red_packet_t_id = Web_ConfigModel::value('plus_mon_redpacket_tpl_id');
if(!$openFlag || !$red_packet_t_id){
    //plus会员关闭
    return true;
}
//当前月份最大天数
$maxDays = date('t', time());
$date_time_array = getdate (time());
$mday = $date_time_array['mday'];
$Plus_UserModel = new Plus_UserModel();
$sql = "select * from yf_plus_user where 1=1 and user_status=2";
$result = $Plus_UserModel->sql->getAll($sql);
$Plus_UserModel = new Plus_UserModel();
foreach ($result as $item) {
    ($item['issue_day']>$maxDays) && $item['issue_day'] = $maxDays;
    if($item['issue_day'] == $mday && $item['end_date']>time()){
        //下发红包
        $Plus_UserModel->publishRedpacket($item['user_id'],$red_packet_t_id,'plus_mon');
    }
}
return true;
?>


