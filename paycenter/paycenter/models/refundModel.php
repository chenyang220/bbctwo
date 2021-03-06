<?php

/**
 * Class refundModel
 * @author yuli
 * 退款
 * 目前只支持支付宝、微信退款
 */
class refundModel
{
    private static $config = [
        'alipay'=> [
            'alipay',
            'alipayMobile'
        ],
        'wx'=> [
            'wx_native',
            'app_wx_native',
            'app_h5_wx_native'
        ]
    ];

    public $orderModel;
    public $paymentChannelModel;

    public function __construct()
    {
        $this->orderModel = new Union_Order;
        $this->paymentChannelModel = new Payment_ChannelModel;
    }


    /**
     * @param $arr
     * @return boolean
     * @throws Exception
     */
    public function refundSingle($arr)
    {
        $refund_amount = $arr['refund_amount'];
        $return_number = $arr['return_number'];
        $return_goods_name = $arr['return_goods_name'];
       //$shop_id = $arr['shop_id'];
        $payment_channel_code = $arr['payment_channel_code'];
        $inorder = $arr['order_number'];

        $union_order_list = $this->orderModel->getByWhere([
            'inorder:LIKE'=> "%$inorder%",
        ]);

        //购物卡支付金额\预存款支付金额 不走此方法
        $union_order = current($union_order_list);
        if ($union_order['union_online_pay_amount'] == 0) {
            return true;
        }

        $union_order_list = array_filter($union_order_list, function ($item) {
            return empty($item['notify_data'])
                ? false
                : true;
        });

        if (empty($union_order_list)) {
            throw new Exception('未找到支付返回结果');
        }
        $union_order = current($union_order_list);
        //payment_channel_code支付方式union_order表有无需传参
        if (empty($arr['payment_channel_code'])) {
            $payment_channel_code = $union_order['payment_channel_code'];
        } 
        $order_amount = $union_order['trade_payment_amount'];
        $third_party_result = $union_order['notify_data']; //第三方交易返回结果

        //中酷微信退款
        if (isset($third_party_result['trade_no'])) {
            #支付宝
            $third_party_trade_no = $third_party_result['trade_no'];
        } else if (isset($third_party_result['transaction_id'])) {
            #微信
            $third_party_trade_no = $third_party_result['transaction_id'];
        }

        if (!is_numeric($refund_amount) || $refund_amount <= 0) {
            throw new Exception('退款金额格式不正确');
        }

        $payment_channel = $this->getPaymentChannel($payment_channel_code);

        $payment_config = $payment_channel['payment_channel_config'];

        $payment_config['payment_channel_code'] = $payment_channel['payment_channel_code'];

        $refund_order = [
            'refund_amount'=> $refund_amount, #退款金额
            'return_number'=> $return_number, #退款单号
            'return_goods_name'=> $return_goods_name, #退款/退货商品名称（可以为空）
            'order_amount'=> $order_amount, #订单总金额
            'third_party_trade_no'=> $third_party_trade_no #第三方订单号
        ];
        $pay_code = Yf_Registry::get('pay_config')['pay_code'];
        if(in_array($payment_channel_code,$pay_code['alipay_code'])){
            $flag = $this->alipay($refund_order, $payment_config);
        }else if(in_array($payment_channel_code,$pay_code['wx_code'])){
            $flag = $this->wx($union_order);
        } else if ($payment_channel_code == 'unionpay') {
            $flag = $this->unionpay($union_order);
        }
        return $flag;
    }



    /**
     * 中酷微信退款
     * @param $union_order array 订单信息
     * @throws WxPayException0
     * @return boolean
     */
    private function wx($union_order)
    {
       if ($union_order['notify_data']) {
            $union_notify_data = ($union_order['notify_data']);
            $union_notify_data['refund_amount'] = $union_order['union_online_pay_amount'] * 100;
            $Payment_JhWxAppModel =  new Payment_JhWxAppModel();
            $Payment_JhWxApp = $Payment_JhWxAppModel->wxReturnMoney($union_notify_data);
            if ($Payment_JhWxApp['errcode'] == 0) {
                return true;
            } else {
                return false;
            }
       } else {
            return false;
       }
    }


    /**
     * 中酷银联退款
     * @param $union_order array 订单信息
     * @throws WxPayException0
     * @return boolean
     */
    private function unionpay($union_order)
    {
       if ($union_order['notify_data']) {
            $union_notify_data = ($union_order['notify_data']);
            $union_notify_data['refund_amount'] = $union_order['union_online_pay_amount'] * 100;
            $Payment_JhYlAppModel =  new Payment_JhYlAppModel();
            $Payment_JhYlApp = $Payment_JhYlAppModel->YlReturnMoney($union_notify_data);
            if ($Payment_JhYlApp['errcode'] == 0) {
                return true;
            } else {
                return false;
            }
       } else {
            return false;
       }
    }

    public function getPaymentChannel($payment_channel_code)
    {
        $payment_channel_info = $this->paymentChannelModel->getByWhere(['payment_channel_code'=>$payment_channel_code]);
        $payment_channel = current($payment_channel_info);

        if ($payment_channel === false) {
            throw new Exception('未找到支付配置');
        }

        return $payment_channel;
    }

    /**
     * @param $order_ids
     * @return array
     * @throws Exception
     *
     * return array = [
     *     [order_id, status, msg
     * ]
     */
    public function refund($order_ids)
    {
        $payment_channel_list = $this->getPaymentChannelList();
        $order_list = $this->getOrder($order_ids); 
        $resultList = array();
        foreach ($order_list as $orderId=> $order) {
            #退款金额
            $order_amount = $order['trade_payment_amount'];
            $refund_amount = $order['trade_payment_amount'];
            $return_number = $order['union_order_id'];

            //购物卡支付金额\预存款支付金额 不走此方法
            if ($order['union_online_pay_amount'] == 0) {
                $resultList[] = [
                    'union_order_id'=> $order['union_order_id'],
                    'order_id'=> $order['inorder'],
                    'status'=> 200
                ];
                continue;
            }

            #notify返回信息
            $third_party_result = $order['notify_data'];


            try {
                if (isset($third_party_result['app_id'])) {
                    $third_party_app_id = $third_party_result['app_id'];
                } else if (isset($third_party_result['appid'])) {
                    $third_party_app_id = $third_party_result['appid'];
                } else {
                    throw new Exception('未找到appid');
                }

                $payment_channel_config = $payment_channel_list[$third_party_app_id];
                $payment_channel_code = $payment_channel_config['payment_channel_code'];
                //$shop_id = $payment_channel_config['shop_id'];

                #支付宝或者微信流水号
                if (isset($third_party_result['trade_no'])) {
                    #支付宝
                    $third_party_trade_no = $third_party_result['trade_no'];
                } else if (isset($third_party_result['transaction_id'])) {
                    #微信
                    $third_party_trade_no = $third_party_result['transaction_id'];
                }
                $refund_order = [
                    //'shop_id'=> $shop_id, #店铺id
                    'refund_amount'=> $refund_amount, #退款金额
                    'return_number'=> $return_number, #退款单号
                    //'return_goods_name'=> $return_goods_name, #退款/退货商品名称（可以为空）
                    'order_amount'=> $order_amount, #订单总金额
                    'third_party_trade_no'=> $third_party_trade_no #第三方订单号
                ];

                if (in_array($payment_channel_code, static::$config['alipay'])) {
                    $flag = $this->alipay($refund_order, $payment_channel_config);
                } else if (in_array($payment_channel_code, static::$config['wx'])) {
                    $flag = $this->wx($refund_order, $payment_channel_config);
                }
                $resultList[] = [
                    'union_order_id'=> $order['union_order_id'],
                    'order_id'=> $order['inorder'],
                    'status'=> $flag ? 200 : 250
                ];
            } catch (Exception $e) {
                $resultList[] = [
                    'union_order_id'=> $order['union_order_id'],
                    'order_id'=> $order['inorder'],
                    'status'=> 250,
                    'msg'=> $e->getMessage()
                ];
            }
        }
        return $resultList;
    }

    /**
     * 获取支付配置信息
     * @return array = [
     *      app_id=> array config
     * ]
     */
    private function getPaymentChannelList()
    {
        $paymentChannelModel = new Payment_ChannelModel;
        $paymentChannelList = $paymentChannelModel->getByWhere();  //全部取出（不合理，我知道）

        $res_list = [];
        foreach ($paymentChannelList as $paymentChannel) {
            //$shop_id = $paymentChannel['shop_id'];
            $payment_channel_config = $paymentChannel['payment_channel_config'];
            $payment_channel_code = $paymentChannel['payment_channel_code'];

            //$payment_channel_config['shop_id'] = $shop_id;
            $payment_channel_config['payment_channel_code'] = $payment_channel_code;

            $app_id = $payment_channel_config['appid'];
            $res_list[$app_id] = $payment_channel_config;
        }
        return $res_list;
    }

    /**
     * 获取订单信息
     */
    private function getOrder($order_ids)
    {
        $order_list = $this->orderModel->getUnionOrder($order_ids);

        if ( !$order_list ) {
            throw new Exception('获取订单失败');
        }
        return $order_list;
    }

    /**
     * 支付宝退款
     * @param $order array 订单信息
     * @param $paymentConfig 支付配置信息
     * @return boolean
     * @throws Exception
     *
     * 注意：
     * 现系统内支付宝有两套账号，退款时需找到对应账号进行退款
     *
     * out_trade_no 订单支付时传入的商户订单号,不能和trade_no同时为空
     * trade_no 支付宝交易号，和商户订单号不能同时为空
     * refund_amount 需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
     * refund_reason 退款的原因说明
     * out_request_no 标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传
     * operator_id 商户的操作员编号
     * store_id 商户的门店编号
     * terminal_id 商户的终端编号
     *
     */
    private function alipay($order, $paymentConfig)
    {
        require_once './libraries/Api/alipayMobile/AopSdk.php';

        $aop = new AopClient();
        $aop->appId = $paymentConfig['appid'];
        $aop->rsaPrivateKey = $paymentConfig['rsaPrivateKey'];
        $aop->alipayrsaPublicKey = $paymentConfig['alipayPublicKey'];

        $request = new AlipayTradeRefundRequest();
        $bizContent = <<<EOF
            {
                "trade_no": "$order[third_party_trade_no]",
                "refund_amount": $order[refund_amount],
                "refund_reason": "正常退款",
                "out_request_no": "$order[return_number]",
                "operator_id": "OP001",
                "store_id": "NJ_S_001",
                "terminal_id": "NJ_T_001"
            }            
EOF;
        $request->setBizContent($bizContent);
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;

        if (!empty($resultCode) && $resultCode == 10000) {
            return true;
        } else {
            return false;
        }
    }

    

    /**
     * 定义微信支付常量
     * @param $app_id string 微信分配的公众账号ID（企业号corpid即为此appId）
     * @param $mch_id string
     * @param $key string 商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
     * @param $app_secret string 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置）
     * @param $payment_key string 支付代码名称 （商城内部）
     * @throws Exception
     */
    private function defineWxConstants($paymentConfig)
    {
        $app_id = $paymentConfig['appid'];
        $mch_id = $paymentConfig['mchid'];
        $key = $paymentConfig['key'];
        $app_secret = $paymentConfig['appsecret'];
        $payment_channel_code = $paymentConfig['payment_channel_code'];
        //$shop_id = $paymentConfig['shop_id'];

        !defined('APPID_DEF') && define('APPID_DEF', $app_id);
        !defined('MCHID_DEF') && define('MCHID_DEF', $mch_id);
        !defined('KEY_DEF') && define('KEY_DEF', $key);
        !defined('APPSECRET_DEF') && define('APPSECRET_DEF', $app_secret);

        $sslCertPath = $paymentConfig['apiclient_cert'];//APP_PATH . "/data/api/wx/cert/apiclient_cert.pem";
        $sslKeyPath = $paymentConfig['apiclient_key'];//APP_PATH . "/data/api/wx/cert/apiclient_key.pem";
        !defined('SSLCERT_PATH_DEF') && define('SSLCERT_PATH', $sslCertPath);
        !defined('SSLKEY_PATH_DEF') && define('SSLKEY_PATH',$sslKeyPath);

        if (! is_file($sslCertPath)) {
            throw new Exception('apiclient_cert.pem文件不存在');
        }

        if (! is_file($sslKeyPath)) {
            throw new Exception('apiclient_key.pem文件不存在');
        }
    }
}