<?php
define("APPID", Web_ConfigModel::value("appid")); // 商户账号appid
define("MCHID", Web_ConfigModel::value("mchid")); 		// 商户号
define("SECRECT_KEY", Web_ConfigModel::value("secrect_key"));  //支付密钥签名
define("IP", $_SERVER[REMOTE_ADDR]);   //IP
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class WxModel
{
    public $appid;
    public $mchid;
    public $secrect_key;

    public function __construct()
    {
        $Payment_ChannelModel = new Payment_ChannelModel();
        $config_row = $Payment_ChannelModel->getChannelConfig('wx_native');
        $this->appid = $config_row['appid'];
        $this->mchid = $config_row['mchid'];
        $this->secrect_key = $config_row['appsecret'];
        $this->apiclient_key_url = $config_row['apiclient_key'];
        $this->apiclient_cert_url = $config_row['apiclient_cert'];

    }
    function curl_post_ssl($url, $vars, $second = 30, $aHeader = array())
    {
        $ch = curl_init();//初始化curl

        curl_setopt($ch, CURLOPT_TIMEOUT, $second);//设置执行最长秒数
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');//证书类型
        curl_setopt($ch, CURLOPT_SSLCERT, $this->apiclient_cert_url);//证书位置
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');//CURLOPT_SSLKEY中规定的私钥的加密类型
        curl_setopt($ch, CURLOPT_SSLKEY, $this->apiclient_key_url);//证书位置
        curl_setopt($ch, CURLOPT_CAINFO, 'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, LIB_PATH .'/Api/wx/tixiancert/rootca.pem');
        if (count($aHeader) >= 1) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);//设置头部
        }
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);//全部数据使用HTTP协议中的"POST"操作来发送

        $data = curl_exec($ch);//执行回话
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
    //封装提现方法
    function tixian($money,$openid){
        $arr = array();
        $arr['mch_appid'] = APPID;
        $arr['mchid'] = MCHID;
        $arr['nonce_str'] = date("Ymd") . rand(10000, 800000) . rand(10000, 800000);//随机字符串，不长于32位
        $arr['partner_trade_no'] = '1298016501' . date("Ymd") . rand(10000, 90000) . rand(10000, 90000);//商户订单号
        $arr['openid'] = $openid;
        $arr['check_name'] = 'NO_CHECK';//是否验证用户真实姓名，这里不验证
        $arr['amount'] = $money;//付款金额，单位为分
        $desc = "提现";
        $arr['desc'] = $desc;//描述信息
        $arr['spbill_create_ip'] = IP;//获取服务器的ip
        //封装的关于签名的算法
        $arr['sign'] = $this->getSign($arr);//签名

        $var = $this->arrayToXml($arr);
        $xml = $this->curl_post_ssl('https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers', $var, 30, array(), 1);
        $rdata = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $return_code = (string)$rdata->return_code;
        $result_code = (string)$rdata->result_code;
        $return_code = trim(strtoupper($return_code));
        $result_code = trim(strtoupper($result_code));

        if ($return_code == 'SUCCESS' && $result_code == 'SUCCESS') {
            $isrr = array(
                'con'=>'ok',
                'error' => 0,
            );
        } else {
            $returnmsg = (string)$rdata->return_msg;
            $isrr = array(
                'error' => 1,
                'errmsg' => $returnmsg,
            );
        }
        return json_encode($isrr);
    }
//遍历数组方法
function arraytoxml($data){
    $str='<xml>';
    foreach($data as $k=>$v) {
        $str.='<'.$k.'>'.$v.'</'.$k.'>';
    }
    $str.='</xml>';
    return $str;
}
function xmltoarray($xml) {
    //禁止引用外部xml实体 
   libxml_disable_entity_loader(true); 
   $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
   $val = json_decode(json_encode($xmlstring),true); 
   return $val;
}
function getSign($data){

    ksort($data);//排序
    //使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串
    $str='';
    foreach($data as $k=>$v) {
        $str.=$k.'='.$v.'&';
    }
    //拼接API密钥
    $str.='key='.SECRECT_KEY;
    $data['sign']=md5($str);//加密
    return $data['sign'];
}



}

?>
