<?php
/**
 * Created by PhpStorm.
 * User: UFO
 * Date: 17/7/13
 * Time: 下午6:42
 */
require_once '../../../configs/config.ini.php';
//
//require_once LIB_PATH . '/Api/wxapp/WxPay.Api.php';
//
//require_once LIB_PATH . '/Api/wxapp/WxPay.Notify.php';
//
//class PayNotifyCallBack extends WxPayNotify
//{
//    //查询订单
//    public function Queryorder($transaction_id)
//    {
//        $input = new WxPayOrderQuery();
//        $input->SetTransaction_id($transaction_id);
//        $result = WxPayApi::orderQuery($input);
//
//        if(array_key_exists("return_code", $result)
//            && array_key_exists("result_code", $result)
//            && $result["return_code"] == "SUCCESS"
//            && $result["result_code"] == "SUCCESS")
//        {
//            return true;
//        }
//        return false;
//    }
//
//    //重写回调处理函数
//    public function NotifyProcess($data, &$msg)
//    {
//        $notfiyOutput = array();
//
//        if(!array_key_exists("transaction_id", $data)){
//            $msg = "输入参数不正确";
//            return false;
//        }
//        //查询订单，判断订单真实性
//        if(!$this->Queryorder($data["transaction_id"])){
//            $msg = "订单查询失败";
//            return false;
//        }
//        return true;
//    }
//}
//$notify = new PayNotifyCallBack();
////print_r($notify);die;
////echo LIB_PATH . '/Api/wxapp/lib/WxPay.Api.php';die;
//$notify->Handle(false);


//$xml ="<xml><appid><![CDATA[wxb8a215008b1378aa]]></appid>
//<bank_type><![CDATA[CFT]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[N]]></is_subscribe>
//<mch_id><![CDATA[1536158211]]></mch_id>
//<nonce_str><![CDATA[h0nd97ccjrjm4ut9ez7u3vgpnz3q12uk]]></nonce_str>
//<openid><![CDATA[ofy--4pF4smCBaMsE_da1jMm8xo8]]></openid>
//<out_trade_no><![CDATA[1567504585]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[1FBEC645834557A598064D148DBF319A]]></sign>
//<time_end><![CDATA[20190903175629]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4200000347201909030727227903]]></transaction_id></xml>";
//
//public function xml_to_array($xml){
//    if(!$xml){
//        return false;
//    }
//    //将XML转为array
//    //禁止引用外部xml实体
//    libxml_disable_entity_loader(true);
//    $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
//    return $data;
//}

//Xml转数组
//function XmlToArr($xml)
//{
//    if($xml == '') return '';
//    libxml_disable_entity_loader(true);
//    $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
//    return $arr;
//}
libxml_disable_entity_loader(true);
$data = json_decode(json_encode(simplexml_load_string(file_get_contents("php://input"), 'SimpleXMLElement', LIBXML_NOCDATA)), true);
//$data = file_get_contents("php://input");
file_put_contents('zzzz.txt', print_r($data,true));

//将回调通知参数写入数据库
$order_model = new Union_OrderModel();
//$notify_data = json_encode($data);
$order_model->editUnionOrder($data['out_trade_no'],array('notify_data'=>$data));
//libxml_disable_entity_loader(true);
//$arr = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
//file_put_contents('filename1.txt', $arr);
