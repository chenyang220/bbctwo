<?php
/* *
 * 功能：微信服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 本回调函数只是适合调用聚合支付的回调数据处理
 */
require_once '../../../configs/config.ini.php';

$data = $_REQUEST;


$order_model = new Union_OrderModel();
$Pay_Union_Order = $order_model->getOneByWhere(array("union_order_id"=>$data['out_trade_no']));
$edit_row = array();
$edit_row['notify_data'] = $data;
$result = $order_model->editUnionOrder($data['out_trade_no'],$edit_row);
if($result){
    echo "SUCCESS";        //请不要修改或删除
    Yf_Log::log('Process-SUCCESS', Yf_Log::INFO, 'pay_wxnative_notify');
}else{
    echo "FAIL";        //请不要修改或删除
    Yf_Log::log('Process-FAIL', Yf_Log::ERROR, 'pay_wxnative_notify_error');
    Yf_Log::log('Process-FAIL', Yf_Log::ERROR, 'pay_wxnative_notify');
}


//处理一步回调-通知商城更新订单状态
//修改订单表中的各种状态
$Consume_DepositModel = new Consume_DepositModel();
$rs = $Consume_DepositModel->notifyShop($Pay_Union_Order['union_order_id'],$Pay_Union_Order['buyer_id']);


