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

$file_name_row = pathinfo(__FILE__);
$crontab_file = $file_name_row['basename'];
//执行任务(推送订单支付通知)
$Model = new CommonModel();
$sql ="select * from yf_seller_wxpublic_tplmessage where status = 0 and type=1 limit 10";
$result = $Model->sql->getAll($sql);
$arr = array();
//模板编号
$tpl_id = 'nOqmSBk7RW5iuoWR7SFqXIkNlCrqa3phbFLSdvCxk9c';
foreach($result as $items){
	//组装新数组
	$items['shop_id'] && $arr[$items['shop_id']][] = $items;
}
if(!$arr){
	return true;
}
//循环处理模板消息通知
foreach($arr as $k=>$items){
	$wx_obj = new Yf_Wxpublic($k);
	$id_arr_str = array();
	foreach($items as $it){
		$tpl_data = array();
		$uid = $it['user_id'];
		$opend_id = Yf_Wxpublic::getWxOpenIdByUserId($uid);
		$tpl_data["touser"] = $opend_id;
        $tpl_data["template_id"] = $tpl_id;
		$tpl_data["data"] = $it['tpl_data'];
		$wx_obj->send($it);
		$id_arr_str[] = $it['id'];
	}
	//修改已发送标识
	if($id_arr_str){
		$ids =  implode(",",$id_arr_str);
		$update_sql ="update yf_seller_wxpublic_tplmessage set status=1 where id in ({$ids})";
		$Model->sql->exec($update_sql);
	}
}
return true;

?>


