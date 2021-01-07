<?php


include_once LIB_PATH . '/simba/src/QuickOauth.class.php';
include_once LIB_PATH .'/simba/src/Api.class.php';

class ZkSms
{
    public function __construct() {
        $this->client_id = "172d1c69e247207db52b54081255e450";
        $this->client_secret = "896f886913826082f52a49a63aef4cff";
        $this->config['api_url'] = "http://117.145.30.131:8555/gateway";
    }

    public function token ($token, $enterprise_id) {
        $quick_oauth = new simba\oauth\QuickOauth();
        $hashkey = $quick_oauth->getHashkey($token);  // 获取hashKey
        $result = $quick_oauth->getAccessToken($token, $enterprise_id, $hashkey);
        $access_token = $result['access_token']; // 放入从授权入口或者快速授权入口获取到的access_token
        $apiObj = new simba\oauth\Api();
        $result = $apiObj->simba_user_info($access_token);
        if ($result['msgCode'] == 200) {
            $date['u_id'] = $result['result']['userNumber'];
            $date['token'] =  $access_token;
        } else {
            $date = array();
        }
        return $date;
    }

    public function test () {
        $ZkSms = new ZkSms();
        $msg = array(
            "msgType"=>1,
            "noticeType"=>1,
            "bizTypeCode"=>"yellowpager-mall",
            "templateCode"=>"pure_text_bill",
            "businessId"=>1,
            "subject"=>"测试标题",
            "content"=>"测试内容",
            "enterName"=>"网程通信",
            'sender'=> '80014511',
            "receivers"=>[80001669,80014691]
        );

        $access_token = request_string("access_token");
        // file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'abs.php',print_r($access_token,true),FILE_APPEND);
        // $access_token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX25hbWUiOiI4MDAxNDUxMSIsInNjb3BlIjpbXSwicm9sZXMiOm51bGwsImV4cCI6MTYwNTkwMTI5NCwidXNlck5hbWUiOiI4MDAxNDUxMSIsIm9hdXRoQ2xpZW50VG9rZW4iOnsiYXV0aGVudGljYXRpb25Db2RlIjoiYTBmcDNwbWllYnE0MnBuMiIsImFwaUF1dGhvcml6ZVR5cGUiOm51bGwsInRva2VuU3RyIjpudWxsLCJ0b2tlbkJ5dGUiOm51bGwsIm9wZW5BcHBJZCI6NjIyLCJjbGllbnRJZCI6IjE3MmQxYzY5ZTI0NzIwN2RiNTJiNTQwODEyNTVlNDUwIiwiY2xpZW50U2VjcmV0IjoiODk2Zjg4NjkxMzgyNjA4MmY1MmE0OWE2M2FlZjRjZmYiLCJ1c2VyTmFtZSI6IjgwMDE0NTExIiwidXNlck51bWJlciI6ODAwMTQ1MTEsImF1dGhlbnRpY2F0aW9uU3RyIjpudWxsLCJlbnRlcnByaXNlSWQiOjAsImF1dGhlbnRpY2F0aW9uQnl0ZSI6bnVsbCwidG9rZW4iOm51bGwsInJlZnJlc2hUb2tlbiI6bnVsbH0sInVzZXJJZCI6IjgwMDE0NTExIiwianRpIjoiZjM4ZmUzYTUtZDc0Zi00OGUzLThiNmMtMWY5ZGNlMjRjMmUxIiwiY2xpZW50X2lkIjoiMTcyZDFjNjllMjQ3MjA3ZGI1MmI1NDA4MTI1NWU0NTAifQ.2Er3Z63JlfKprfzXOUrXCqBbBFYEyniE1oYhdQOJgjQ";
        $a = $ZkSms->simba_business_notice_send($access_token, $msg);
        exit;
    }


     /* 消息通知
     * 
     */
    public function simba_business_notice_send($token, $msg = array(),$order_from) {

        if ($order_from == 6) {
            $msg['bizTypeCode'] = "trip";
        } elseif ($order_from == 4) {
            $msg['bizTypeCode'] = "travel";
        } elseif ($order_from == 5) {
            $msg['bizTypeCode'] = "hotel";
        } elseif ($order_from == 7) {
            $msg['bizTypeCode'] = "delicious";
        } else {
            $msg['bizTypeCode'] = "yellowpager-mall";
        }
        $receivers_str = implode(",", $msg['receivers']);
        $db = new YFSQL();       
        $sql = "SELECT * from ucenter_user_info where user_id IN (" . $receivers_str . ")";
        $ucenter_user_info = $db->find($sql);
        $u_id_arr = array_column($ucenter_user_info, "u_id");
        $msg['receivers'] = $u_id_arr;
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        $args = array(
            "timestamp" => $msectime,
            'session' => 0,
            'method' => "simba.business.notice.send",
            'client_id' => $this->client_id
        );
        $param_json = array(
            'messageNotice' => $msg
        );
        $param_json = json_encode($param_json,JSON_UNESCAPED_UNICODE);
        $args['param_json'] = $param_json;
        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;
        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        if ($result) {
            $log = $msg['sender'] . "成功发送给用户" . json_encode($msg['receivers']);
        } else {
            $log = $msg['sender'] . "给用户" . json_encode($msg['receivers']) . "发送失败！";
        }

        Yf_Log::log($log,Yf_Log::LOG,'ZkSms');
        return $result;
    }

      /* 接口调用签名
     * 对所有API请求参数（包括公共参数和业务参数，但除去sign参数和byte[]类型的参数），根据参数名称的ASCII码表的顺序排序。 
      如：foo:1, bar:2, foo_bar:3, foobar:4排序后的顺序是bar:2, foo:1, foo_bar:3, foobar:4。
      将排序好的参数名和参数值拼装在一起，根据上面的示例得到的结果为：bar2foo1foo_bar3foobar4。
      把拼装好的字符串采用utf-8编码，使用签名算法对编码后的字节流进行摘要。如果使用MD5算法，
      则需要在拼装的字符串前后加上app的secret后， 再进行摘要，如：md5(secret+bar2foo1foo_bar3foobar4+secret)；如果使用HMAC_MD5算法，
      则需要用app的secret初始化摘要算法后，再进行摘要，如：hmac_md5(bar2foo1foo_bar3foobar4)。
      将摘要得到的字节流结果使用十六进制表示，如：hex(“helloworld”.getBytes(“utf-8”)) = “68656C6C6F776F726C64” 说明：MD5和HMAC_MD5都是128位长度的摘要算法，
      用16进制表示，一个十六进制的字符能表示4个位，所以签名后的字符串长度固定为32个十六进制字符。
     * 
     * $method : md5 ,  hmac_md5
     */
    protected function getSign($args, $method = 'md5') {
        ksort($args);
        $args_str = '';
        foreach ($args as $key => $val) {
            $args_str .= $key . $val;
        }
        if ($method == 'md5') {
            $args_md5 = md5($this->client_secret . $args_str . $this->client_secret); //32位
            $args_md5 = substr($args_md5, 8, 16); //16位的MD5加密
        } else {
            //服务端暂时不支持下面签名方法
            $args_md5 = $this->hmac_md5($args_str, $this->client_secret);
        }
        return $args_md5;
    }

    /* 暂不可用 */

    private function hmac_md5($data, $key) {
        if (function_exists('hash_hmac')) {
            return hash_hmac('md5', $data, $key);
        }

        $key = (strlen($key) > 64) ? pack('H32', 'md5') : str_pad($key, 64, chr(0));
        $ipad = substr($key, 0, 64) ^ str_repeat(chr(0x36), 64);
        $opad = substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64);
        return md5($opad . pack('H32', md5($ipad . $data)));
    }


    /* curl模拟授权提交 
     */

    private function curlPost($url, $args, $token) {
        $headers = array(
            'Content-Type:application/x-www-form-urlencoded',
            "auth_token:$token"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_HEADER, true); //返回header信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        //$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        $result = curl_exec($ch);
        curl_close($ch);
        //记录日志
        if ($result) {
            $result = json_decode($result, true);
            return $result;
        } else {
            // $error_info = "开放平台接口无返回。 地址： $url ； 参数：" . json_encode($args);
            // writelog('Simba_Open_Api', $error_info);
            return false;
        }
    }
}

?>
