<?php
/**
 * 此页面专门处理POS端业务
 * 逻辑很简单，改变订单支付状态为已支付
 */
require_once '../../../configs/config.ini.php';

$Payment_WxNativeModel = PaymentModel::create('wx_native');
$verify_result = $Payment_WxNativeModel->verifyNotify();
$data = xmltoarray(file_get_contents("php://input"));
Yf_Log::log(encode_json($data), Yf_Log::INFO, 'db');

//计算得出通知验证结果
if ($verify_result && $notify_row = $Payment_WxNativeModel->getNotifyData())
{
    Yf_Log::log(var_export($notify_row, true), Yf_Log::INFO, 'pay_webpos_wx_notify');

    $unionOrderModel = new Union_OrderModel();
    $union_order_id = $notify_row['order_id']; //订单id

    $edit_row['order_state_id'] = Union_OrderModel::PAYED;
    $edit_row['pay_time'] = date('Y-m-d H:i:s');
    $edit_row['notify_data'] = $data;
    $flag = $unionOrderModel->editUnionOrder($union_order_id, $edit_row);

    Yf_Log::log("执行结果：订单号=>$union_order_id,执行状态=>$flag", Yf_Log::INFO, 'pay_webpos_wx_notify');

    echo $flag ? 'SUCCESS' : 'FAIL';
} else {
    echo 'FAIL';
}


function xml_to_array( $xml )
{
    $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches))
    {
        $count = count($matches[0]);
        $arr = array();
        for($i = 0; $i < $count; $i++)
        {
            $key= $matches[1][$i];
            $val = xml_to_array( $matches[2][$i] );  // 递归
            if(array_key_exists($key, $arr))
            {
                if(is_array($arr[$key]))
                {
                    if(!array_key_exists(0,$arr[$key]))
                    {
                        $arr[$key] = array($arr[$key]);
                    }
                }else{
                    $arr[$key] = array($arr[$key]);
                }
                $arr[$key][] = $val;
            }else{
                $arr[$key] = $val;
            }
        }
        return $arr;
    }else{
        return $xml;
    }
}
// Xml 转 数组, 不包括根键
function xmltoarray( $xml )
{
    $arr = xml_to_array($xml);
    $key = array_keys($arr);
    $data = $arr[$key[0]];
    foreach ($data as $k=>$value){
        $data[$k] = str_replace(array('<![CDATA[',']]>'), '', $value);
    }
    return $data;
}