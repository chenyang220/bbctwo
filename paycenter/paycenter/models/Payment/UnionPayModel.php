<?php
if (!defined('ROOT_PATH')){exit('No Permission');}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Payment_UnionPayModel implements Payment_Interface
{

    public $gateway_url = ''; //网关地址
    private $verify_url = ''; //消息验证地址
    private $payment;
    private $order;
    private $parameter;
    private $order_type;


    /**
     * Constructor
     *
     * @param  array $payment_row 支付平台信息
     * @param  array $order_row 订单信息
     * @access public
     */
    public function __construct($payment_row = array(), $order_row = array())
    {
        $this->payment = $payment_row;
        $this->order   = $order_row;

        if(!defined('unionpay_environment') && !defined('SDK_SIGN_CERT_PWD')){
            define('SDK_SIGN_CERT_PWD',$payment_row['unionpay_key']);
        }
        $this->payment['parter'] = $payment_row['unionpay_partner'];  //商户号
        $this->payment['return_url'] = Yf_Registry::get('base_url') . "/paycenter/api/payment/unionpay/return_url.php"; //返回URL
        $this->payment['notify_url'] = Yf_Registry::get('base_url') . "/paycenter/api/payment/unionpay/return_url.php"; //通知URL
    }

    /**
     * 支付
     *
     * @access public
     */
    public function pay($order_row)
    {
        //BEGIN
        if ($order_row)
        {
            $this->order = $order_row;
        }

        //1 == order_state_id  待付款状态
        if (1 != $this->order['order_state_id'])
        {
            throw new Exception('订单状态不为待付款状态');
        }

        include_once LIB_PATH . '/Api/unionpay/sdk/acp_service.php';

        $time = date("YmdHis");
        global $log;
        //商户订单号
        $out_trade_no = $this->order['union_order_id'];
        $amount = $this->order['union_online_pay_amount']*100; //订单金额

        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,                 //版本号
            'encoding' => 'utf-8',				  //编码方式
            'txnType' => '01',				      //交易类型
            'txnSubType' => '01',				  //交易子类
            'bizType' => '000201',				  //业务类型

            'frontUrl' =>  $this->payment['return_url'],  //前台通知地址

            'backUrl' => $this->payment['notify_url'],	  //后台通知地址
            'signMethod' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod,	              //签名方法
            'channelType' => '08',	              //渠道类型，07-PC，08-手机
            'accessType' => '0',		          //接入类型
            'currencyCode' => '156',	          //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $this->payment['parter'],		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $out_trade_no,	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => $time,	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $amount,	//交易金额，单位分，此处默认取demo演示页面传递的参数

            // 订单超时时间。
            // 超过此时间后，除网银交易外，其他交易银联系统会拒绝受理，提示超时。 跳转银行网银交易如果超时后交易成功，会自动退款，大约5个工作日金额返还到持卡人账户。
            // 此时间建议取支付时的北京时间加15分钟。
            // 超过超时时间调查询接口应答origRespCode不是A6或者00的就可以判断为失败。
            'payTimeout' => date('YmdHis', strtotime('+15 minutes')),

            'riskRateInfo' =>'{commodityName=测试商品名称}',

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),

            //TODO 其他特殊用法请查看 special_use_purchase.php
        );

        com\unionpay\acp\sdk\AcpService::sign ( $params );
        $uri = com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->frontTransUrl;
        $html_form = com\unionpay\acp\sdk\AcpService::createAutoFormHtml( $params, $uri );

        echo $html_form;
    }

    /**
     *
     * 取得订单支付状态，成功或失败
     * @param array $param
     * @return array
     */
    public function getPayResult($param)
    {
        return $param['trade_status'] == 'TRADE_SUCCESS';
    }

    /**
     * 通知验证
     *
     * @access public
     */
    public function verifyNotify()
    {
        include_once(LIB_PATH . "/Api/alipay/lib/alipay_notify.class.php");

        $alipayNotify  = new AlipayNotify($this->payment);
        $verify_result = $alipayNotify->verifyNotify();

        return $verify_result;
    }

    /**
     * 通知验证
     *
     * @access public
     */
    public function verifyReturn()
    {
        include_once(LIB_PATH . "/Api/alipay/lib/alipay_notify.class.php");

        $alipayNotify  = new AlipayNotify($this->payment);
        $verify_result = $alipayNotify->verifyReturn();

        return $verify_result;
    }

    public function sign($parameter)
    {
        $sign_str = '';
        $sign_str = $this->getSignature($parameter, $parameter['key']);

        return $sign_str;
    }

    public function getSignature($parameter, $cp_key = null)
    {
    }

    /**
     * 制作支付接口的请求地址 发送请求
     *
     * @access public
     */
    public function request()
    {
    }

    /**
     * 得到异步返回数据
     *
     * @access public
     */
    public function getNotifyData()
    {
        $notify_row = $this->getReturnData();

        $notify_row['deposit_async']         = 1;

        return $notify_row;
    }

    /**
     * 得到同步返回数据
     *
     * @access public
     */
    public function getReturnData($Consume_TradeModel = null)
    {
        $notify_param = $_REQUEST;
        if ($Consume_TradeModel)
        {
            $notify_row = array();
            $Union_OrderModel = new Union_OrderModel();

            $order_id = $notify_param['orderId'];
            $notify_row = $Union_OrderModel->getOne($order_id);
            $notify_row['order_id'] = $notify_param['orderId'];

        }
        else
        {
            //插入充值记录, 如果同步数据没有,从订单数据中读取过来
            $notify_row = array();
            $notify_row['order_id'] = $notify_param['orderId'];
            $notify_row['deposit_trade_no'] = $notify_param['queryId'];
            $notify_row['deposit_body']          = '';
            $notify_row['deposit_seller_id']  = $notify_param['orderId'];
            $notify_row['deposit_notify_time']  = $notify_param['settleDate'];
            $notify_row['deposit_trade_status']  = $notify_param['respCode'];
            $notify_row['deposit_total_fee']  = $notify_param['txnAmt'];
            $notify_row['deposit_gmt_payment']  = $notify_param['settleDate'];
            $notify_row['deposit_notify_id']  = $notify_param['orderId'];
            $notify_row['deposit_payment_type'] = $notify_param['bizType'];
            $notify_row['deposit_service']     =  'unionpay';
            $notify_row['deposit_sign_type']    = $notify_param['signMethod'];
            $notify_row['deposit_sign']         = $notify_param['signature'];
        }

        $notify_row['payment_channel_id']   = Payment_ChannelModel::UNIONPAY;

        return $notify_row;
    }


    function create_html($params, $action) {
        // <body onload="javascript:document.pay_form.submit();">
        $encodeType = isset ( $params ['encoding'] ) ? $params ['encoding'] : 'UTF-8';
        $html = <<<eot
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset={$encodeType}" />
</head>
<body onload="javascript:document.pay_form.submit();">
    <form id="pay_form" name="pay_form" action="{$action}" method="post">
	
eot;
        foreach ( $params as $key => $value ) {
            $html .= "    <input type=\"hidden\" name=\"{$key}\" id=\"{$key}\" value=\"{$value}\" />\n";
        }
        $html .= <<<eot
   <!-- <input type="submit" type="hidden">-->
    </form>
</body>
</html>
eot;
        return $html;
    }
}

?>