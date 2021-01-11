<?php
/* *
 * 功能：银联在线服务器通知页面
 */
require_once '../../../configs/config.ini.php';
require_once '../../../../libraries/Api/unionpay/sdk/acp_service.php';

Yf_Log::log("r=" . json_encode($_REQUEST), Yf_Log::INFO, 'pay_unionpay_return');
Yf_Log::log("g=" . json_encode($_GET), Yf_Log::INFO, 'pay_unionpay_return');
Yf_Log::log("p=" . json_encode($_POST), Yf_Log::INFO, 'pay_unionpay_return');

$Payment_UnionPayModel = PaymentModel::create('unionpay');

$logger = com\unionpay\acp\sdk\LogUtil::getLogger();
$logger->LogInfo("receive back notify: " . com\unionpay\acp\sdk\createLinkString ( $_POST, false, true ));


if (isset ( $_POST ['signature'] )) {

    if(com\unionpay\acp\sdk\AcpService::validate ( $_POST )){

        echo '验签成功';

        $Consume_TradeModel = new Consume_TradeModel();
        $notify_row = $Payment_UnionPayModel->getReturnData($Consume_TradeModel);

        //查找此支付单的交易类型
        $Union_OrderModel = new Union_OrderModel();
        $data = $Union_OrderModel->getOne($notify_row['order_id']);
        $trade_type = Trade_TypeModel::$trade_type_row[$data['trade_type_id']];

        //购物
        if($trade_type == 'shopping')
        {
            //修改订单表中的各种状态
            $Consume_DepositModel = new Consume_DepositModel();
            $rs                 = $Consume_DepositModel->notifyShop($notify_row['order_id'],$notify_row['buyer_id']);
        }

        if($trade_type == 'deposit')
        {
            //修改充值表的状态
            $Consume_DepositModel = new Consume_DepositModel();
            $deposit = $Consume_DepositModel->getOne($notify_row['order_id']);
            if($deposit['deposit_trade_status']==2)
            {
                $rs = 1;
            }else{
                $rs = $Consume_DepositModel->notifyDeposit($notify_row['order_id'],$notify_row['buyer_id'],$notify_row['payment_channel_id']);
            }
        }elseif ($trade_type == 'plus'){//开通PLUS会员业务处理 @nsy 2019-02-14
            $rs = $Union_OrderModel->dealPlusOrder($notify_row['order_id'],'银联在线','unionpay');
        }

        if ($rs)
        {
            //重定向浏览器
            if($trade_type == 'shopping')
            {
                $app_id = $data['app_id'];

                //查找回调地址
                $User_AppModel = new User_AppModel();
                $user_app = $User_AppModel->getOne($app_id);
                if(Yf_Utils_Device::isMobile())
                {
                    // $return_app_url = Yf_Registry::get('shop_wap_api_url') . 'tmpl/member/order_list.html';
                    $return_app_url = Yf_Registry::get('paycenter_api_url') . "?ctl=Info&met=after_pay&order_id=" . $orderId . "&return=" . Yf_Registry::get('shop_wap_api_url');
                }
                else
                {
                    // $return_app_url = $user_app['app_url'].'?ctl=Buyer_Order&met=physical';
                    $return_app_url = Yf_Registry::get('paycenter_api_url') . "?ctl=Info&met=after_pay&order_id=" . $orderId . "&return=" . $user_app['app_url'];
                }
            }

            if($trade_type == 'deposit')
            {
                $return_app_url = Yf_Registry::get('paycenter_api_url')."?ctl=Info&met=index";
            }

            if($trade_type == 'plus')
            {
                $return_app_url =Yf_Registry::get('shop_api_url') . "?ctl=Plus_User&met=index";
            }
            //echo $return_app_url;

            header("Location: " .$return_app_url);
            //确保重定向后，后续代码不会被执行
            exit;
        }
    }else{
        Yf_Log::log('签名验证失败', Yf_Log::ERROR, 'pay_uinonpay_return_error');
        Yf_Log::log('签名验证失败', Yf_Log::ERROR, 'pay_unionpay_return');
        echo '验签失败';die;
    }

} else {
    Yf_Log::log('签名为空', Yf_Log::ERROR, 'pay_uinonpay_return_error');
    Yf_Log::log('签名为空', Yf_Log::ERROR, 'pay_unionpay_return');
    echo '签名为空';die;
}
?>