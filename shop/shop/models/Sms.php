<?php
    use Qcloud\Sms\SmsSingleSender;

class Sms
{
	public static function send($mob, $pre,$content, $tple_id = null,$data=[])
	{
		/*if (is_array($content))
		{
			$content = encode_json($content);
		}
		$name     = Web_ConfigModel::value('sms_account');
		$password = md5(Web_ConfigModel::value('sms_pass'));

		$url = "http://sms.b2b-builder.com/index/sms/send";

        $params = array();
        $params['name'] = $name;
        $params['password'] = $password;
        $params['mob'] = $mob;
        $params['pre'] = $pre;
        $params['content'] = $content;
        $params['data'] = json_encode($data);

		if ($tple_id)
		{
            $params['tpl_id'] = $tple_id;
		}
        Yf_Log::log($url,Yf_Log::ERROR,'sms1');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));//设置传送的参数
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);//设置等待时间
		$result = curl_exec($ch);
		curl_close($ch);
        Yf_Log::log(($result),Yf_Log::ERROR,'sms2');
		return $result;*/




        if (is_array($content))
            {
                $content = encode_json($content);
            }

            $name     = Web_ConfigModel::value('sms_account');
            $password = md5(Web_ConfigModel::value('sms_pass'));
            $content  = urlencode($content);
            $content  = iconv("utf-8", "gb2312//IGNORE", $content);

            $url = "http://sms.b2b-builder.com/sms.php?name=" . $name . "&password=" . $password . "&mob=" . $mob ."&pre=".$pre. "&content=" . $content.'&data='. json_encode($data);

            if ($tple_id)
            {
                $url = $url . '&tpl_id=' .  $tple_id;
            }

            // Yf_Log::log($url,Yf_Log::ERROR,'sms1');

            file_put_contents(LOG_PATH . '/sms.log', $url."\n", FILE_APPEND);
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            // Yf_Log::log(($result),Yf_Log::ERROR,'sms2'); 
            curl_close($ch);
            return $result;
	}

	
	  /** 国际
         * 发送短信统一接口
         *
         */
        public static function sends($mob, $content, $tple_id = null)
		{
            try {
                $cmd="curl -X 'POST' 'https://rest.nexmo.com/sms/json' -d 'from=Acme Inc' -d 'text=".$content."' -d 'to=39".$mob."' -d 'api_key=0429a43b ' -d 'api_secret=q7gKWbkKyFZ5R3E6'";
                exec($cmd,$request);
                $a=implode(" ", $request);
                $data= json_decode($a,true);
                if($data['messages'][0][status]==0){
                    return ture;
                }
                return false;
            } catch(\Exception $e) {
                return false;
            }
        }
    
    
    /**
     * 腾讯云短信接口
     * 国内外添加国家区号即可
     */
    public static function send_new($mobile)
    {
        // 短信应用SDK AppID
        $appid = 1400046726; // 1400开头
        // 短信应用SDK AppKey
        $appkey = "37b6171fb455bd487b4ddafd6598a38a";
        // 需要发送短信的手机号码
        $phoneNumbers = ["18801963698","88751629","88751629","99185728","99188013","18356066378"];
        // 短信模板ID，需要在短信应用中申请
        // $templateId = 148038;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
        $smsSign = "感谢您注册{1}，欢迎您。"; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
        
        try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $result = $ssender->send(0, "86", $mobile, "感谢您注册{1}，欢迎您。", "", "");
            $rsp = json_decode($result);
            echo $result;
        } catch(\Exception $e) {
            echo var_dump($e);
        }
        
        return $result;
        
    }

    /**
     * @param $data json
     * 公众号发送模板通知消息
     */
    public function sendWxPublicMsg($data){
        $Web_ConfigModel = new Web_ConfigModel();
        $accToken = $Web_ConfigModel->getWxPublicAccessToken();
        $result = array();
        if($accToken['token']){
            $result = wxpublic_send( $data,$accToken['token']);
        }
        return $result;
    }
}

?>
