<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Api接口
 *
 *
 * @category   Game
 * @package    User
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2015 远丰仁商
 * @version    1.0
 * @todo
 */
class Connect_QQCtl extends Yf_AppController implements Connect_Interface
{
	public $appid     = null;
	public $appsecret = null;
	public $redirect_url = null;

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

		//$sms_config = include_once 'sms.ini.php'
		$connect_config = Yf_Registry::get('connect_rows');
		$this->appid     = $connect_config['qq']['app_id'];
		$this->appsecret = $connect_config['qq']['app_key'];
		
		$this->callback = request_string('callback');//Yf_Registry::get('url')
		$this->redirect_url = Yf_Registry::get('base_url') . '/login.php';
	}

	public function select()
	{
		include $this->view->getView();
	}

	public function login()
	{
		//判断当前登录账户,绑定

		//子站跳转

		$redirect_url = $this->redirect_url;

		$url = '';
		
		//1.授权
		if ("QQ")
		{
			$url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=".$this->appid."&redirect_uri=".urlencode($redirect_url)."&client_secret=".$this->appsecret."&state=".urlencode($this->callback)."&cope=get_user_info,get_info&callback=".urlencode($this->callback);
		}
		else
		{
			$url = "https://open.weixin.qq.com/connect/qrconnect?appid=".$appid."&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
		}

		location_to($url);
	}

	/**
	 * callback 回调函数
	 *
	 * @access public
	 */
	public function callback()
	{
		$type = User_BindConnectModel::QQ;
		
		$code = request_string('code', null);

		$redirect_url = $this->redirect_url;

		$openid = '';

		$login_flag = false;

		//判断当前登录账户
		if (Perm::checkUserPerm())
		{
			$user_id = Perm::$userId;
		}
		else
		{
			$user_id = 0;
		}

		if ($code)
		{
            if (request_string('isApp') == 1) {
                goto App;
            }

			//登录
			$token_url        = 'https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=' . $this->appid . '&client_secret=' . $this->appsecret . '&code=' . $code . '&redirect_uri='.urlencode($redirect_url);
			
			$curl = curl_init($token_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_FAILONERROR, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$response = curl_exec($curl);
			curl_close($curl);

			$error = strpos($response, 'error');
			
				
			if($error)
			{
				$error_info = preg_match_all("|{(.*)}|U", $response, $out,PREG_PATTERN_ORDER);
				
				$error_info = json_decode($out[0][0]);
					
				$error_id = $error_info->client_id;
				    
				$error_des = $error_info->error_description;
				    
				$this->data->addBody($error_des);
				die();
			}
			else
			{
				$access_token_row = explode('&', $response);
				//取出token
				$access_token = substr($access_token_row[0],strpos($access_token_row[0],"=")+1);
				
				/*
				array(
					[0] =>'access_token=7FDE8093B8EE39CC223EC1433C5ACD7B'
					[1] =>'expires_in=7776000'
					[2] =>'refresh_token=1B4F45BED87A3EF8F0C8ACF86288E217'
					)
				*/
				

				//获取用户openid
				$user_openid_url = 'https://graph.qq.com/oauth2.0/me?'.$access_token_row[0];
				$curl = curl_init($user_openid_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_FAILONERROR, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				$user_openid = curl_exec($curl);
				
				curl_close($curl);
				$user_openid_info_row = array();
				$client_id = "";

				if($user_openid)
				{
					$user_openid_info = preg_match_all("|{(.*)}|U", $user_openid, $out,PREG_PATTERN_ORDER);
				
					$user_openid_info_row = json_decode($out[0][0]);
					
					$client_id = $user_openid_info_row->client_id;
				    
				    $openid = $user_openid_info_row->openid;
				}
				
				
				$User_BindConnectModel = new User_BindConnectModel();

				if($openid)
				{
					//获取用户信息
					$user_info_url = 'https://graph.qq.com/user/get_user_info?'.$access_token_row[0].'&oauth_consumer_key='.$client_id.'&openid='.$openid;
					$curl = curl_init($user_info_url);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curl, CURLOPT_FAILONERROR, false);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					$user_info = curl_exec($curl);
					
					curl_close($curl);

					$user_info = json_decode($user_info);
				}

                if (request_string('isApp') == 1) {
                    //app调用，app内可以获取以上所有消息，不需要重复请求
                    //构造上述变量
                    App:
                    $openid = request_string('openid');
                    $access_token = request_string('access_token');
                    $gender = request_string('gender');
                    $nickname = request_string('nickname');
                    $figureurl_qq_2 = request_string('figureurl_qq_2');
                    $user_info = <<<EOF
                        {"gender"=> "$gender", "nickname"=> "$nickname", "figureurl_qq_2": "$figureurl_qq_2"}
EOF;

                }

				$connect_rows = array();
				$User_BindConnectModel = new User_BindConnectModel();
				$bind_id     = sprintf('%s_%s', 'qq', $openid);
				$connect_rows = $User_BindConnectModel->getBindConnect($bind_id);
					
				if ($connect_rows)
				{
					$connect_row = array_pop($connect_rows);
				}
				//已经绑定,并且用户正确
				if (isset($connect_row['user_id']) && $connect_row['user_id'])
				{
					//验证通过, 登录成功.
					$users = $User_InfoModel-> getOne($connect_row['user_id']);
                    $d = [];
                    $d['user_id'] = $connect_row['user_id'];
                    $encrypt_str = Perm::encryptUserInfo($d, $users['session_id']);
                    $backUrl = request_string('callback') ? : $_GET['callbak'];
                    $url = $backUrl . '&us=' . $connect_row['user_id'] . '&ks='. $encrypt_str;

                    location_to($url);
				}
				else
				{
					// 下面可以需要封装
					$bind_rows     = $User_BindConnectModel->getBindConnect($bind_id);

					if ($bind_rows  && $bind_row = array_pop($bind_rows))
					{
						if ($bind_row['user_id'])
						{
							//该账号已经绑定
							echo '非法请求,该账号已经绑定';
							die();
						}
						if($user_id != 0)
						{
							$bind_id_row = $User_BindConnectModel->getBindConnectByuserid($user_id,$type);
							if($bind_id_row)
							{
								echo '非法请求,该账号已经绑定';
								die();
							}
						}

						$data_row                      = array();
						$data_row['user_id']           = $user_id;
						$data_row['bind_token'] = $access_token;

						$connect_flag = true;
						$User_BindConnectModel->editBindConnect($bind_id, $data_row);
					}
					else
					{
						if($user_id != 0)
						{
							$bind_id_row = $User_BindConnectModel->getBindConnectByuserid($user_id,$type);
							fb($bind_id_row);
							if($bind_id_row)
							{
								echo '非法请求,该账号已经绑定';
								die();
							}
						}

						//插入绑定表
						if($user_info->gender == '女')
						{
							$user_gender = 2;
						}else
						{
							$user_gender = 1;
						}

						$bind_array = array();
						$bind_array = array(
											'bind_id'=>$bind_id,
											'bind_type'=>$User_BindConnectModel::QQ,
											'user_id'=>$user_id,
											'bind_nickname'=>$user_info->nickname,
											'bind_avator'=>$user_info->figureurl_qq_2,
											'bind_gender'=>$user_gender,
											'bind_openid'=>$openid,
											'bind_token'=>$access_token,
											);
						
						$connect_flag = $User_BindConnectModel->addBindConnect($bind_array);

					}
					
					//取得open id, 需要封装
					if ($connect_flag)
					{
						//选择,登录绑定还是新创建账号 $user_id == 0
						if (!Perm::checkUserPerm())
						{
							$data_regist_row['token'] = $access_token;
                            $data_regist_row['password'] = $User_InfoModel -> random_str(8);
                            $res = $this->bindRegist($data_regist_row);
                            $backUrl = request_string('callback') ? : $_GET['callbak'];
                            $url = $backUrl . '&us=' . $res['user_id'] . '&ks='. $res['k'];

                            location_to($url);
						}
						else
						{
							$login_flag = true;
						}
					}
					else
					{
						//
					}
				}

			}

			if ($login_flag)
			{
				//验证通过, 登录成功.
				if ($user_id && $user_id == $connect_row['user_id'])
				{
					echo '非法请求,已经登录用户不应该访问到此页面';
					die();
				}
				else
				{
					$User_InfoModel  = new User_InfoModel();
					$result = $User_InfoModel->userlogin($connect_row['user_id']);
					if($result)
					{
						$msg    = 'success';
						$status = 200;

						$this->data->addBody(-140, array(), $msg, $status);
					}
					else
					{
						$this->data->setError('登录失败');
					}

				}

				$login_flag = true;

				if(request_string('callback'))
				{
					$us = $result['user_id'];
					$ks = $result['k'];
				    $url = sprintf('%s&us=%s&ks=%s', request_string('callback'), $us, $ks);
				    location_to($url);
				}
				else
				{
					$url = sprintf('%s?ctl=Login', Yf_Registry::get('url'));
					location_to($url);
				}
				echo '登录系统';
				die();

			}
			else
			{
				//失败
			}

		}
		else
		{
			$this->data->setError('code 获取失败');

		}
	}


    //第三方注册
    public function bindRegist($data_regist_row)
    {
        $token = $data_regist_row['token'];
        $password = $data_regist_row['password'];
        $server_id = 0;
        $rs_row = [];
        //根据token从绑定表中查找用户信息
        $User_BindConnectModel = new User_BindConnectModel();
        //开启事务
        $User_BindConnectModel->sql->startTransaction();
        $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
        $bind_info = current($bind_info);
        if (!$bind_info) {
            $this->data->setError('绑定账号不存在');

            return false;
        }
        //判断绑定账户是否已经绑定过用户，已经绑定过用户的账号，不可重复绑定
        if ($bind_info['user_id']) {
            $this->data->setError('该账号已经绑定用户!');

            return false;
        }
        //判断该账号名是否已经存在
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $user_rows = $User_InfoModel->getInfoByName($bind_info['bind_nickname']);
        //如果用户名已经存在了，则在用户名后面添加随机数
        if ($user_rows) {
            $bind_info['bind_nickname'] = $bind_info['bind_nickname'] . rand(0000, 9999);
        }
        //在user_info中插入用户信息
        $Db = Yf_Db::get('ucenter');
        $seq_name = 'user_id';
        $user_id = $Db->nextId($seq_name);
        $now_time = time();
        $ip = get_ip();
        if(strpos($ip, ',')){
            $ip = substr($ip,0,strpos($ip, ','));
        }
        $session_id = uniqid();
        $arr_field_user_info = [];
        $arr_field_user_info['user_id'] = $user_id;
        $arr_field_user_info['user_name'] = $bind_info['bind_nickname'];
        $arr_field_user_info['password'] = md5($password);
        $arr_field_user_info['action_time'] = $now_time;
        $arr_field_user_info['action_ip'] = $ip;
        $arr_field_user_info['session_id'] = $session_id;
        $user_info_add_flag = $User_InfoModel->addInfo($arr_field_user_info);
        check_rs($user_info_add_flag, $rs_row);
        //在绑定表中插入用户id
        $bind_user_row = [];
        $time = date('Y-m-d H:i:s', time());
        $bind_user_row['user_id'] = $user_id;
        $bind_user_row['bind_time'] = $time;
        $bind_user_row['bind_token'] = $token;
        $bind_edit_flag = $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_user_row);
        check_rs($bind_edit_flag, $rs_row);
        //在user_info_detail表中插入用户信息
        $arr_field_user_info_detail = [];
        if ($bind_info['bind_gender'] == 1) {
            $gender = 1;
        } else {
            $gender = 0;
        }
        $qq_logo = substr($bind_info['bind_avator'], 0, strrpos($bind_info['bind_avator'], '/'));
        $qq_logo = $qq_logo . '/40';
        $arr_field_user_info_detail['user_name'] = $bind_info['bind_nickname'];
        $arr_field_user_info_detail['nickname'] = $bind_info['bind_nickname'];
        $arr_field_user_info_detail['user_avatar'] = $bind_info['bind_avator'];
        $arr_field_user_info_detail['user_avatar_thumb'] = $qq_logo;
        $arr_field_user_info_detail['user_gender'] = $gender;
        $arr_field_user_info_detail['user_reg_time'] = $now_time;
        $arr_field_user_info_detail['user_count_login'] = 1;
        $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
        $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
        $arr_field_user_info_detail['user_reg_ip'] = $ip;
        $user_detail_add_flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
        check_rs($user_detail_add_flag, $rs_row);
        if (is_ok($rs_row) && $User_BindConnectModel->sql->commit()) {
            $d = [];
            $d['user_id'] = $user_id;
            $encrypt_str = Perm::encryptUserInfo($d, $session_id);
            $arr_body = [
                "user_name" => $bind_info['bind_nickname'],
                "server_id" => $server_id,
                "k" => $encrypt_str,
                "session_id" => $session_id,
                "user_id" => $user_id
            ];
            return $arr_body;
        } else {
            $User_BindConnectModel->sql->rollBack();
            return false;
        }
    }

}

?>
