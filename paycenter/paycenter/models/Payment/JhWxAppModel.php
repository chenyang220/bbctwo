<?php if ( ! defined('ROOT_PATH'))
{
    exit('No Permission');
}
require_once LIB_PATH . '/Api/wx/lib/WxPay.Api.php';
require_once LIB_PATH . '/Api/wx/lib/WxPay.Notify.php';
include_once LIB_PATH . '/Api/wx/WxPay.JsApiPay.php';
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Payment_JhWxAppModel {
    private $open_id = 'b7cce94a2d9d09fcdaf969ba7e0a75ce';
    private $open_key = '0e60b9fcc2b2e9423851e645889b68bf';
    private $gz_open_id = 'e646f39f441d656041731c45d52147ed';
    private $gz_open_key = 'e27cc7ff234c39f1b8107a7db1a8fec5';
    private $gz_app_id = 'wx6e791c29beaa82af';  //公众号APPID
    private $type = '';
    /**
     * Constructor
     *
     * @param  array $payment_row 支付平台信息
     * @param  array $order_row 订单信息
     * @access public
     */
    public function __construct($type = "h5")
    {
        $this->type = $type;
    }


    /**
     * 支付
     *
     * @access public
     */
    public function pay($order_row)
    {
        if ($this->type == 'jsdk') {
            $return = $this->JhwxJsdkPay($order_row);
            $Union_OrderModel = new Union_OrderModel();
            $Union_OrderModel->editUnionOrder($order_row['union_order_id'],array("jh_pay_style"=>"wx_native_gz"));
            if ($return['errcode'] == 0) {
                $getRows = $return['data'];
                $jsApiParametersRows['appId'] = $getRows['appId'];
                $jsApiParametersRows['timeStamp'] = $getRows['timeStamp'];
                $jsApiParametersRows['nonceStr'] = $getRows['nonceStr'];
                $jsApiParametersRows['signType'] = $getRows['signType'];
                $jsApiParametersRows['package'] = $getRows['package'];
                $jsApiParametersRows['paySign'] = $getRows['paySign'];
                $jsApiParameters = json_encode($jsApiParametersRows);
                // 查找回调地址
                $shop_wap_url = Yf_Registry::get('shop_wap_url');
                $return_url =  $shop_wap_url . "member/member.html";
print <<<EOT
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付</title>
    <script type="text/javascript">
    //调用微信JS api 支付
    function jsApiCall()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            {$jsApiParameters},
            function(res){
                WeixinJSBridge.log(res.err_msg);
                if (res.err_msg == "get_brand_wcpay_request:ok")
                {
                    //alert('支付成功');
                    window.location.href = "{$return_url}";
                }
                else
                {
                    if (res.err_msg == "get_brand_wcpay_request:cancel")
                    {
                            //alert('取消支付');
                        history.back(-1);
                    }
                    else
                    {
                        alert(res.err_code+res.err_desc+res.err_msg);
                        history.back(-1);
                    }
                }
            }
        );
    }

    function callpay()
    {
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        }else{
            jsApiCall();
        }
    }
    window.onload = function(){
        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', callpay, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', callpay); 
                document.attachEvent('onWeixinJSBridgeReady', callpay);
            }
        }else{
            callpay();
        }
    };
    
    </script>
</head>
<body>
<!--<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>-->
</body>
</html>
EOT;
die;
            } else {
                Yf_Log::log('GetPayUrl RES:=' . encode_json($result), Yf_Log::INFO, 'pay_wxjs_info_jh');
                throw new Exception(encode_json($result));
            }
        } else {
            if ($order_row)
                {
                    $this->order = $order_row;
                }   
                //1 == order_state_id  待付款状态
                if (1 != $this->order['order_state_id'])
                {
                    throw new Exception('订单状态不为待付款状态');
                }
                $result = $this->JhwxAppPay($order_row);
                if ($result['errcode'] == 0) {
                    $Union_OrderModel = new Union_OrderModel();
                    $Union_OrderModel->editUnionOrder($order_row['union_order_id'],array("ord_no"=>$result['data']['ord_no'],"out_no"=>$result['data']['out_no']));
                    $trade_result = json_decode($result['data']['trade_result'],true);
                    if ($order_row['return_url']) {
                        $rediret_url = urlencode(Yf_Registry::get('url')."?ctl=Info&met=h5_pay"  . "&order_id=" . $this->order['inorder'] . "&r_url=" . $order_row['return_url']);
                    } else {
                        $rediret_url = urlencode(Yf_Registry::get('url')."?ctl=Info&met=h5_pay&trade_id=".$this->order['trade_id']);
                    }
                    $mweb_url = $trade_result['mweb_url'] . "&redirect_url=" . $rediret_url;
                    header("Location:" . $mweb_url);
                }
                $app_id = $this->order['app_id'];
                //查找回调地址
                $User_AppModel = new User_AppModel();
                $user_app      = $User_AppModel->getOne($app_id);
                if ($order_row['return_url']) {
                    $user_app['app_url'] = $order_row['return_url'] . "&order_id=" . $this->order['inorder'] . "&order_status=2";
                }
        }
        
    }



    /*------h5支付begin------*/
    /**
     * @param $trade_row 传送的支付单数据
     * @return boolean
     * 聚合微信APP预支付接口 (h5支付)
     */
    public function JhwxAppPay($trade_row) {
        $url = "https://api.tlinx.com/mct1/payorder";
        //验签数据
        $timestamp = time();
        $code = $this->randCode(6,1);
        $data['out_no'] = time() . $code;
        $data['pmt_tag'] = "WeixinTTGL";
        $data["pmt_name"] = "微信支付";
        $data['original_amount'] = sprintf("%.2f",$trade_row['trade_payment_amount']) * 100 ;//原始交易金额
        $data['trade_amount'] = sprintf("%.2f",$trade_row['union_online_pay_amount']) * 100 ;//实际交易金额
        $data['trade_type'] = "MWEB";
        $data['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
        $data["notify_url"] = Yf_Registry::get('paycenter_api_url') . "/paycenter/api/payment/wx/jh_notify_url.php?out_trade_no=" . $trade_row['union_order_id'];
        $data['scene_info'] = '{"h5_info":{"type":"Wap","wap_url":"https://ms.look56.com/","wap_name":"支付"}}';
        $result=$this->api($url,$data,$this->open_key,$timestamp,$this->open_id);
        return $result;
    }

    public function pmt_tag ($open_key,$timestamp,$open_id) {
        $url = "https://api.tlinx.com/mct1/paylist";
        $data['pmt_type']='';
        $pmt_tag = $this->api($url,$data,$this->open_key,$timestamp,$this->open_id);
        return $pmt_tag;
    }

    /**
     * @param $$order_id  订单号
     * @return boolean
     * 聚合微信APP查询付款状态接口
     */
    public function paystatus($order_id) {
        $Union_OrderModel = new Union_OrderModel();
        $Union_Order = $Union_OrderModel->getOneByWhere(array("inorder"=>$order_id));
        $url = "https://api.tlinx.com/mct1/paystatus";
         //验签数据
        $timestamp = time();
        $data['ord_no'] = $Union_Order['ord_no'];
        $data['out_no'] = $Union_Order['out_no'];
        $result=$this->api($url,$data,$this->open_key,$timestamp,$this->open_id);
        return $result;
    }

    /**
     * @param $order_id  中酷微信退款
     * @return boolean
     * 聚合微信APP查询付款状态接口
     */
    public function wxReturnMoney($union_notify_data) {
        $url = "https://api.tlinx.com/mct1/payrefund";
        //验签数据
        $timestamp = time();
        $code = $this->randCode(10,1);
        $data['out_no'] = $union_notify_data['out_no'];
        $data['ord_no'] = $union_notify_data['ord_no'];
        $data["refund_out_no"] = 'tk'.time().$code;
        if (isset($union_notify_data['refund_amount'])) {
            $data['refund_amount'] = $union_notify_data['refund_amount'];
        } else {
            $data['refund_amount'] = $union_notify_data['trade_payment_amount'] * 100;//退款金额
        }
        $data['shop_pass'] = sha1('123456');//实际交易金额
        $Union_OrderModel = new Union_OrderModel();
        $Union_Order =  $Union_OrderModel->getOneByWhere(array("union_order_id"=>$union_notify_data['out_trade_no']));
        if ($Union_Order['jh_pay_style'] == "wx_native_gz") {
            $open_key = $this->gz_open_key;
            $open_id = $this->gz_open_id;
        } else {
            $open_key = $this->open_key;
            $open_id = $this->open_id;
        }
        $result=$this->api($url,$data,$open_key,$timestamp,$open_id);

        //付款成功提醒 
        /*中酷消息推送begin*/
        $db = new YFSQL();       
        $sql = "SELECT * from ucenter_user_info where user_id=" . $Union_Order['buyer_id'];
        $ucenter_user_info_select = $db->find($sql);
        $ucenter_user_info = current($ucenter_user_info_select);
        $ZkSms = new ZkSms();
        $getToken = $ZkSms->token($ucenter_user_info['token'],$ucenter_user_info['enterId']);
        if ($result['errcode'] == 0) {
             $content = "尊敬的用户" . $ucenter_user_info['user_name'] . ",编号为："  . $Union_Order['inorder'] . "的订单已成功退款";
        } else {
            $content = "尊敬的用户" . $ucenter_user_info['user_name'] . ",编号为："  . $Union_Order['inorder'] . "的订单退款失败，请稍后重试！";
        }

file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'abs.php',print_r($content,true),FILE_APPEND);
        $order_sql = "SELECT * from yf_order_base where order_id=" . $Union_Order['inorder'];
        $order_base = $db->find($order_sql);
        if ($getToken) {
          $receivers[0] = $Union_Order['buyer_id'];
          $msg = array(
            "msgType"=>1,
            "noticeType"=>1,
            "templateCode"=>"pure_text_bill",
            "businessId"=>1,
            "subject"=>"取消订单",
            "content"=> $content,
            "enterName"=>"取消订单",
            'sender'=> $getToken['u_id'],
            "receivers"=> $receivers
          );
          $message = $ZkSms->simba_business_notice_send($getToken['token'], $msg,$order_base[0]['order_from']);
        }
        Yf_Log::log($result,Yf_Log::LOG,'wxReturnMoney');
        return $result;
    }
    /*------h5支付end------*/


    /*------JSDK原生支付begin------*/
    /**
     * @param $trade_row 传送的支付单数据
     * @return boolean
     * 聚合微信APP支付接口 (原生JSDK支付)
     */
    public function JhwxJsdkPay($trade_row) {
        $url = "https://api.tlinx.com/mct1/payorder";
        //验签数据
        $timestamp = time();
        $code = $this->randCode(6,1);
        $data['out_no'] = time() . $code;
        $data['pmt_tag'] = "WeixinSXF2";
        $data["pmt_name"] = "微信支付";
        $data['original_amount'] = sprintf("%.2f",$trade_row['trade_payment_amount']) * 100 ;//原始交易金额
        $data['trade_amount'] = sprintf("%.2f",$trade_row['union_online_pay_amount']) * 100 ;//实际交易金额
        $data['JSAPI'] = "1";
        $data['sub_appid'] = $this->gz_app_id;
        $data['sub_openid'] = $this->sub_openid();//用户的open_id
        $data["notify_url"] = Yf_Registry::get('paycenter_api_url') . "/paycenter/api/payment/wx/jh_notify_url.php?out_trade_no=" . $trade_row['union_order_id'];
        $result=$this->api($url,$data,$this->gz_open_key,$timestamp,$this->gz_open_id);
        return $result;
    }

    /*
    *
    *  获取用户的openID
    *
    *
    */
    public function sub_openid () {
        //①、获取用户openid
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();
        return $openId;
    }
    /*------JSDK原生支付end------*/


    #API调用主接口
    public function api($url,$map=null,$open_key,$timestamp,$open_id){
        if(count($map)>0){
            $post['data']=$this->aes_encode(json_encode($map),$open_key);   
        }
        $post['open_id']=$open_id;
        $post['timestamp']=$timestamp;
        $post['sign']=$this->make_sign($post,$open_key);
        #http请求数据
        $result=$this->http_query($url,null,$post);
        $array=json_decode($result,true);

        // 返回数据
        if(isset($array['data'])){
            //解密数据
            $array['data']=$this->aes_decode($array['data'],$open_key);

            $array['data']=  json_decode($array['data'],true);
        }
        return $array;
    }

    #AES解密
    public function aes_decode($sStr, $sKey) {
        $sStr=hex2bin($sStr);
        $decrypted= mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$sKey,$sStr,MCRYPT_MODE_ECB);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }

    #AES加密
    public function aes_encode($input, $key) {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = strtoupper(bin2hex($data));
        return $data;
    }
    #AES算法
    private function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }   
    #签名过程
    private function make_sign($data,$open_key){
        $data['open_key'] = $open_key;
        ksort($data);
        $arr_temp = array ();
        foreach ($data as $key => $val) {
            $arr_temp[]=$key.'='.$val;
        }
        $sign_str = implode('&', $arr_temp);
        $sign_str = md5(sha1($sign_str));
        return $sign_str;
    }

    private function randCode($length = 32, $type = 0) {
        $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
        if ($type == 0) {
            array_pop($arr);
            $string = implode("", $arr);
        } elseif ($type == "-1") {
            $string = implode("", $arr);
        } else {
            $string = $arr[$type];
        }
        $count = strlen($string) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $string[rand(0, $count)];
        }
        return $code;
    }


    #http数据交互接口
    public function http_query($url,$get=null,$post=null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if(isset($post)){
            curl_setopt($curl, CURLOPT_POST, 1); //是否开启post
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post); //post数据
        }
        curl_setopt($curl, CURLOPT_HEADER,0);//是否需要头部信息（否）
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//如果成功只将结果返回，不自动输出任何内容。
        curl_setopt($curl, CURLOPT_TIMEOUT,5);//设置允许执行的最长秒数。
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,5);//在发起连接前等待的时间，如果设置为0，则无限等待。
        //忽略证书
        if(substr($url,0,5)=='https'){
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        $curl_result = curl_exec($curl);
        if($curl_result){
            curl_close($curl);
            return $curl_result;
        }else{
            $err_str=curl_error($curl);
            curl_close($curl);  
            return $err_str;
        }
    }

}

?>


