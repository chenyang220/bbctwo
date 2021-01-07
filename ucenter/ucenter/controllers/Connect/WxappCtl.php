<?php
if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author
 */
class Connect_WxappCtl extends Yf_AppController
{

	/**
	 * Constructor
	 *
	 * @param  string $ctl 控制器目录
	 * @param  string $met 控制器方法
	 * @param  string $typ 返回数据类型
	 * @access public
	 */
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);

		$this->userBindConnectModel  = new User_BindConnectModel();

	}
    //检测当前用户是否存在
    public function wx_app(){
        $openid = request_string('openid');
        $user_info = $_REQUEST['userInfo'];
        $bind_id     = sprintf('%s_%s', 'weixin', $openid);
        $info = json_decode($user_info);

        //根据openid查找绑定表中的数据
        $connect_rows = $this->userBindConnectModel->getBindConnect($bind_id);
        $bind_rows     = $connect_rows;

        if ($connect_rows)
        {
            $connect_row = array_pop($connect_rows);
        }
        if (isset($connect_row['user_id']) && $connect_row['user_id'])
        {
            $login_flag = true;

        }else{

            if($bind_rows  && $bind_row = array_pop($bind_rows)){
                if ($bind_row['user_id'])
                {
                    //该账号已经绑定
                    echo '非法请求,该账号已经绑定';
                    die();
                }
                $data_row                      = array();
                $data_row['user_id']           = 0;
                $data_row['bind_token'] = $_GET['token'];

                $connect_flag = $this->userBindConnectModel->editBindConnect($bind_id, $data_row);
            }else{
                //排除所有与非法情况后，在绑定表中添加绑定信息
                $User_BindConnectModel = new User_BindConnectModel();
                $data = array();

                $data['bind_id']           = $bind_id;
                $data['bind_type']         = $User_BindConnectModel::WEIXIN;
                $data['user_id']           = 0;
                $data['bind_nickname']     = base64_encode($info->nickName);  // 名称
                $data['bind_avator']         = $info->avatarUrl; //
                $data['bind_gender']       = $info->gender; // 性别 1:男  2:女
                $data['bind_openid']       = $openid; // 访问
                $data['bind_token'] = $_REQUEST['token'];

                $connect_flag = $User_BindConnectModel->addBindConnect($data);
            }
        }

        if ($connect_flag)
        {
            echo $bind_id;die;
        }
        //用户生成成功，并且与微信绑定成功后用户登录
        if ($login_flag)
        {

            $User_InfoModel  = new User_InfoModel();

            $user_info_row   = $User_InfoModel->getInfo($connect_row['user_id']);

            $user_info_row = array_values($user_info_row);
            $user_info_row = $user_info_row[0];
            $session_id = $user_info_row['session_id'];

            $arr_field               = array();
            $arr_field['session_id'] = $session_id;

            if ($user_info_row)
            {
                $arr_body           = $user_info_row;
                $arr_body['result'] = 1;

                $data            = array();
                $data['user_id'] = $user_info_row['user_id'];

                $encrypt_str = Perm::encryptUserInfo($data, $session_id);

                $arr_body['k'] = $encrypt_str;
                $arr_body['u'] = $data['user_id'];
                $this->data->addBody(100, $arr_body);

            }
            else
            {
                $this->data->setError('登录失败');
            }

        }

    }

    /*获取用户openid和session_key*/
    public function getopenid(){
        $appid = request_string('appid');
        $secret = request_string('secret');
        $js_code = request_string('code');

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$js_code."&grant_type=authorization_code";

        $ch  = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);

        curl_close($ch);

        echo $data;exit;

    }


    /*获取用户openid和session_key*/
    public function getUnionid(){
        $appid = request_string('appid');
        $sessionKey = request_string('session_key');
        $encryptedData = request_string('encryptedData');
        $iv = request_string('iv');


        $errCode = $this->decryptData($appid,$sessionKey,$encryptedData, $iv, $data );

        if ($errCode == 0) {
            print($data . "\n");
        } else {
            print($errCode . "\n");
        }

    }



    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($appid,$sessionKey,$encryptedData, $iv, &$data )
    {
        if (strlen($sessionKey) != 24) {
            return -41001;
        }
        $aesKey=base64_decode($sessionKey);


        if (strlen($iv) != 24) {
            return -41002;
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        $result=openssl_decrypt( $aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj=json_decode( $result );
        if( $dataObj  == NULL )
        {
            return -41003;
        }
        if( $dataObj->watermark->appid != $appid )
        {
            return -41003;
        }
        $data = $result;
        return 0;
    }
    /**
    * 获取accesstoken
    */
    public function getAccessToken(){
        session_start();
        $appid = request_string('appid');
        $secret = request_string('secret');
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;

        if ($_SESSION['access_token'] && $_SESSION['expires_in'] < time()) {
            $access_token = $_SESSION['access_token'];
        }else{
            $ch  = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  //跳过域名验证
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            $data = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($data,true);
            $access_token = $data['access_token'];
            $expires_in = $data['expires_in'];
            $_SESSION['access_token'] = $access_token;
            $_SESSION['expires_in'] = $expires_in + time() - 1000;
        }
        return $access_token;
    }

    /**
    * 获取小程序二维码
    */
    public function getWxacode(){
        $user_id = request_int('u');
        $goods_id = request_int('goods_id');
        $content = [
            'page' => 'pages/product_detail/product_detail',
            'scene'=> 'goods_id='.$goods_id.'&rec=u'.$user_id.'s100c100',
            'width'=> 280
        ];
        $access_token = $this->getAccessToken();
        header("Content-Type:image/jpeg");
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        $response = curl_exec($ch); //返回数据
        curl_close($ch);
        $image_name = uniqid('wx_');
        $images_name = ROOT_PATH.'/images/'.$image_name.'.png';
        file_put_contents($images_name, print_r($response,true));
        header("Content-Type:appication/json");
        echo  Yf_Registry::get('ucenter_api_url').'/images/'.$image_name.'.png';
    }

    /**
     * 获取小程序直播二维码
     */
    public function getLiveCode()
    {
        $user_id = request_int('u');
        $room_id = request_string('roomID');
        $content = [
            'page' => 'pages/live_room/live_room',
            'scene' => 'r' . $room_id . '&rec=u' . $user_id . 'sc',
            'width' => 280

        ];
        $access_token = $this->getAccessToken();
        header("Content-Type:image/jpeg");
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        $response = curl_exec($ch); //返回数据
        curl_close($ch);
        $image_name = uniqid('wx_');
        $images_name = ROOT_PATH . '/images/' . $image_name . '.png';
        file_put_contents($images_name, print_r($response, true));
        header("Content-Type:appication/json");
        echo Yf_Registry::get('ucenter_api_url') . '/images/' . $image_name . '.png';
    }

    /**
    * 获取小程序二维码
    */
    public function getShopCode(){
        $user_id = request_int('u');
        $content = [
            'path' => 'live/pages/index/index',
            'scene'=> '&rec=u'.$user_id.'s100c100',
            'width'=> 430
        ];
        $access_token = $this->getAccessToken();
        header("Content-Type:image/jpeg");
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        $response = curl_exec($ch); //返回数据
        curl_close($ch);
        $image_name = uniqid('wx_');
        $images_name = ROOT_PATH.'/images/'.$image_name.'.png';
        file_put_contents($images_name, print_r($response,true));
        header("Content-Type:appication/json");
        echo  Yf_Registry::get('ucenter_api_url').'/images/'.$image_name.'.png';
    }

        /**
    * 获取小程序二维码
    */
    public function posterShopCode(){
        $user_id = request_int('u');
        $content = [
            'page' => 'pages/distribution_shop/distribution_shop',
            'scene'=> 'sid='. request_int('distribution_shop_id'),
            'width'=> 430
        ];
        file_put_contents(dirname(__FILE__).'/abs.php', print_r(json_encode($content),true));
        $access_token = $this->getAccessToken();
        header("Content-Type:image/jpeg");
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='.$access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        $response = curl_exec($ch); //返回数据
        curl_close($ch);
        $image_name = uniqid('wx_');
        $images_name = ROOT_PATH.'/images/'.$image_name.'.png';
        file_put_contents($images_name, print_r($response,true));
        header("Content-Type:appication/json");
        echo  Yf_Registry::get('ucenter_api_url').'/images/'.$image_name.'.png';
    }

    // fzh 模拟post进行url请求
    public function curl_post_https($url,$data){
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }else{
            return $tmpInfo;
        }
    }
}

?>