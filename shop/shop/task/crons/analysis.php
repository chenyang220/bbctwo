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

//更新数据罗盘订单状态
//查询24小时内的所有订单
$stime = date("Y-m-d H:i:s",time() - 24 * 3600);
$etime = date("Y-m-d H:i:s", time());
$Order_BaseModel = new Order_BaseModel();
$sql = "SELECT A.order_id,A.order_status,A.shop_id,A.payment_number,A.buyer_user_id,A.order_shipping_code,A.order_receiver_address,A.order_create_time,A.order_goods_amount,A.order_payment_amount,A.order_discount_fee,A.order_refund_amount,A.order_return_num,A.order_from,B.return_type,B.order_goods_id,B.order_goods_name,B.order_goods_num,B.order_goods_price,B.return_cash FROM yf_order_base A LEFT JOIN yf_order_return B ON A.order_id = B.order_number WHERE order_create_time >= '{$stime}' AND order_create_time <= '{$etime}' ";
$order_list = $Order_BaseModel->sql->getAll($sql);

$analytics_data = array();
$analytics_data['order_list'] = $order_list;
Yf_Plugin_Manager::getInstance()->trigger('analyticsUpdateOrdersStatus', $analytics_data);


//更新用户信息
$User_InfoModel = new User_InfoModel();
//查询24小时内的所有用户
$stime = date("Y-m-d H:i:s", time() - 24 * 3600);
$etime = date("Y-m-d H:i:s", time());
$sql = "SELECT * FROM yf_user_info WHERE user_regtime >= {$stime} AND  user_regtime <= {$etime}";
$df = $User_InfoModel->sql->getAll($sql);
foreach ($df as $k => $v) {
    $key = Yf_Registry::get('ucenter_api_key');
    $url = Yf_Registry::get('ucenter_api_url');
    $app_id = Yf_Registry::get('ucenter_app_id');
    $formvars = [];
    $formvars['user_id'] = $v['user_id'];
    $rs = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'User', 'getUsernickname', 'json');
    $analytics_data = [
        'user_id' => $v['user_id'],
        'ip' => $rs['data']['action_ip'],
    ];
    Yf_Plugin_Manager::getInstance()->trigger('analyticsMemberupdate', $analytics_data);
}

$key = Yf_Registry::get('ucenter_api_key');
$url = Yf_Registry::get('ucenter_api_url');
$app_id = Yf_Registry::get('ucenter_app_id');
$server_id = Yf_Registry::get('server_id');
foreach ($df as $k => $v) {
    $formvars = [];
    $formvars['user_id'] = $v['user_id'];
    $formvars['app_id'] = $app_id;
    $formvars['server_id'] = $server_id;
    $url = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Api_User', 'getoneinfo', 'json');
    $rs = get_url_with_encrypt($key, $url, $formvars);
    $data = $rs['data'];
    $analytics_data = [
        'user_id' => $v['user_id'],
        'ip' => $data['action_ip'],
    ];
    Yf_Plugin_Manager::getInstance()->trigger('analyticsMemberupdate', $analytics_data);
}

$flag = true;
return $flag;
?>