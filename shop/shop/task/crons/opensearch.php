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

$file_name_row = pathinfo(__FILE__);
$crontab_file = $file_name_row['basename'];

fb($crontab_file);


//执行任务

//上传opensearch数据
$Goods_CommonModel = new Goods_CommonModel();
//查找出当天新增数据
$date_start_time = date('Y-m-d').' 00:00:00';
$date_end_time = date('Y-m-d').' 23:59:59';
$cond_row = array();
$cond_row['common_add_time:>='] = $date_start_time;
$cond_row['common_add_time:<='] = $date_end_time;
$data = $Goods_CommonModel->getByWhere($cond_row);
$data_common = array();
foreach ($data as $key=>$val) {
	$data_common[$key]['common_id'] = $val['common_id'];
	$data_common[$key]['common_name'] = $val['common_name'];
}
$data_common = json_encode($data_common);
$url = Yf_Registry::get('shop_api_url').'/shop/opensearch/demo/demo_document.php?data='.urlencode($data_common);
$a=get_url($url);
Yf_Log::log($a,Yf_Log::INFO,'opensearch');

return  true;
?>