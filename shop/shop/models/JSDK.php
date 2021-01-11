<?php

class JSDK
{
    // start
    public function __construct() {
        //从ucenter中获取appid和appSecret
        //本地读取远程信息
        $key = Yf_Registry::get('ucenter_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
        $formvars = [];

        $formvars['app_id'] = $ucenter_app_id;

        $formvars['ctl'] = 'Connect_Bind';
        $formvars['met'] = 'getWechatInfo';
        $formvars['typ'] = 'json';
        $init_rs = get_url_with_encrypt($key, $url, $formvars);

        if (200 == $init_rs['status']) {
            //读取服务列表
            $this->appId = $init_rs['data']['app_id'];
            $this->appSecret = $init_rs['data']['app_key'];
        }
    }
    public function getSignPackage($nowurl) {
      $jsapiTicket = $this->getJsApiTicket();
      //$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $url = $nowurl;
      $timestamp = time();
      $nonceStr = $this->createNonceStr();
      // 这里参数的顺序要按照 key 值 ASCII 码升序排序
      $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
      $signature = sha1($string);
      $signPackage = array(
        "appId"     => $this->appId,
        "nonceStr"  => $nonceStr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "rawString" => $string,
      		"aa" => $jsapiTicket
      );
      return $signPackage; 
    }
    private function createNonceStr($length = 16) {
      $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      $str = "";
      for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
      }
      return $str;
    }
    private function getJsApiTicket() {
      // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
   
        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $res = json_decode($this->httpGet($url));
        $ticket = $res->ticket;
        return $ticket;
    }
    private function getAccessToken() {
      // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    $appId = $this->appId ;
	  $appSecret = $this->appSecret ;
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      return $access_token;
    }
    private function httpGet($url) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_TIMEOUT, 500);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_URL, $url);
      $res = curl_exec($curl);
      curl_close($curl);
      return $res;
    }

      // end
}

