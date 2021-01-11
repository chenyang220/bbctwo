<?php
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class AlipayModel
{
    public $appid;
    public $rsaPrivateKey;
    public $alipayPublicKey;

    public function __construct()
    {
        $Payment_ChannelModel = new Payment_ChannelModel();
        $config_row = $Payment_ChannelModel->getChannelConfig('alipay');
        $this->appid = $config_row['appid'];
        $this->rsaPrivateKey = $config_row['rsaPrivateKey'];
        $this->alipayPublicKey = $config_row['alipayrsaPublicKey'];

    }
    //封装提现方法
    function withdraw($money,$identity,$name,$order_title,$order_id){
        require_once  ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/AopCertClient.php';
        require_once  ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/AopCertification.php';
        require_once  ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/request/AlipayTradeQueryRequest.php';
        require_once  ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/request/AlipayTradeWapPayRequest.php';
        require_once  ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/request/AlipayTradeAppPayRequest.php';
        require_once  ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/request/AlipayFundTransUniTransferRequest.php';
        $aop = new AopCertClient ();
        $appCertPath = APP_PATH . "/data/api/alipay_new/appCertPublicKey_2021001139690624.crt";
        $alipayCertPath = APP_PATH . "/data/api/alipay_new/alipayCertPublicKey_RSA2.crt";
        $rootCertPath = APP_PATH . "/data/api/alipay_new/alipayRootCert.crt";
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $this->appid;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->alipayrsaPublicKey=$this->alipayPublicKey;
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $aop->isCheckAlipayPublicCert = true;//是否校验自动下载的支付宝公钥证书，如果开启校验要保证支付宝根证书在有效期内
        $aop->appCertSN = $aop->getCertSN($appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($rootCertPath);//调用getRootCertSN获取支付宝根证书序列号
        $request = new AlipayFundTransUniTransferRequest ();
        $request->setBizContent("{" .
        "\"out_biz_no\":\"{$order_id}\"," .
        "\"trans_amount\":{$money}," .
        "\"product_code\":\"TRANS_ACCOUNT_NO_PWD\"," .
        "\"biz_scene\":\"DIRECT_TRANSFER\"," .
        "\"remark\":\"{$order_title}\"," .
        "\"payee_info\":{" .
        "\"identity\":\"{$identity}\"," .
        "\"identity_type\":\"ALIPAY_LOGON_ID\"," .
        "\"name\":\"{$name}\"," .
        "    }," .
        "  }");
        $result = $aop->execute($request);
        // $flag=$this->getInquireWithdraw($order_id);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
                    $isrr = array(
                    'con'=>'ok',
                    'error' => 0,
                );
        } else {
            $isrr = array(
                'error' => 1,
                'errmsg' => '失败',
            );
        }
        return json_encode($isrr);
    }


    //提现查询接口
    public function getInquireWithdraw($order_id){
        require_once ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/AopClient.php';
        require_once ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/AopCertification.php';
        require_once ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/request/AlipayTradeQueryRequest.php';
        require_once ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/request/AlipayTradeWapPayRequest.php';
        require_once ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/request/AlipayTradeAppPayRequest.php';
        require_once ROOT_PATH.'/libraries/Api/alipayMobileNew/aop/request/AlipayFundTransOrderQueryRequest.php';
        $aop = new AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $this->appid;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->alipayrsaPublicKey=$this->alipayPublicKey;
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new AlipayFundTransOrderQueryRequest ();
        $request->setBizContent("{" .
        "\"out_biz_no\":\"{$order_id}\"," .
        "  }");
        $result = $aop->execute ( $request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
        echo "成功";
        } else {
        echo "失败";
        } 
    }
}

?>
