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
class Connect_WeixinCtl extends Yf_AppController implements Connect_Interface
{
	public $appid     = null;
	public $appsecret = null;

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

		$connect_config = Yf_Registry::get('connect_rows');;

		$this->appid     = $connect_config['weixin']['app_id'];
		$this->appsecret = $connect_config['weixin']['app_key'];

		//Yf_Registry::get('url')

		$this->redirect_url = sprintf('%s?ctl=Connect_Weixin&met=callback&from=%s&callback=%s',Yf_Registry::get('url') , request_string('from'), urlencode(request_string('callback')));
	}

	public function select()
	{
		include $this->view->getView();
	}

	public function login()
	{
		//判断当前登录账户,绑定

		//子站跳转
		
		$redirect_url = urlencode($this->redirect_url);

		$url = '';
		//

		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false)
		{
			$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$this->appid&redirect_uri=$redirect_url&response_type=code&scope=snsapi_login&state=123&connect_redirect=1#wechat_redirect";
		}
		else
		{
			$url = "https://open.weixin.qq.com/connect/qrconnect?appid=$this->appid&redirect_uri=$redirect_url&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
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
		$type = User_BindConnectModel::WEIXIN;

		$User_InfoModel = new User_InfoModel();
		
		$code = request_string('code', null);

		$redirect_url = $this->redirect_url;

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
			$token_url        = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appid . '&secret=' . $this->appsecret . '&code=' . $code . '&grant_type=authorization_code';
			$access_token_row = json_decode(file_get_contents($token_url), true);

			if (!$access_token_row || !empty($access_token_row['errcode']))
			{
				throw new Yf_ProtocalException($access_token_row['errmsg']);
				return false;
			}
			else
			{
				$user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token_row['access_token'] . '&openid=' . $access_token_row['openid'] . '&lang=zh_CN';
				$user_info_row = json_decode(@file_get_contents($user_info_url), true);

				/*
				$user_info_row[''] => 1
				$user_info_row['']
				$user_info_row['']
				$user_info_row['']
				$user_info_row['country']
				$user_info_row['']
				$user_info_row['privilege']
				*/

				$User_BindConnectModel = new User_BindConnectModel();

				$bind_id     = sprintf('%s_%s', 'weixin', $user_info_row['openid']);
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
					$bind_avator = $user_info_row['headimgurl'];

					// 下面可以需要封装
					$access_token = $access_token_row['access_token'];
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

						$connect_flag = $User_BindConnectModel->editBindConnect($bind_id, $data_row);
					}
					else
					{
						if($user_id != 0)
						{
							$bind_id_row = $User_BindConnectModel->getBindConnectByuserid($user_id,$type);
							if($bind_id_row)
							{
								echo '非法请求,该账号已经绑定';
								die();
							}
						}
						$data = array();

						$data['bind_id']           = $bind_id;
						$data['bind_type']         = $User_BindConnectModel::WEIXIN;
						$data['user_id']           = $user_id;
						$data['bind_nickname']     = $user_info_row['nickname']; // 名称
						$data['bind_avator']         = $bind_avator; //
						$data['bind_gender']       = $user_info_row['sex']; // 性别 1:男  2:女
						$data['bind_openid']       = $user_info_row['openid']; // 访问
						$data['bind_token'] = $access_token;

						$connect_flag = $User_BindConnectModel->addBindConnect($data);
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

			if ($access_token_row)
			{

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
					$User_InfoDetail = new User_InfoDetail();

					$user_info_row   = $User_InfoModel->getInfo($connect_row['user_id']);
					fb($user_info_row);
					$user_info_row = array_values($user_info_row);
					$user_info_row = $user_info_row[0];
					$session_id = $user_info_row['session_id'];

					$arr_field               = array();
					$arr_field['session_id'] = $session_id;

					if ($user_info_row)
					{
						//$arr_body = array("result"=>1,"user_name"=>$user_info_row['user_name'],"session_id"=>$session_id);
						$arr_body           = $user_info_row;
						$arr_body['result'] = 1;
						//$arr_body['session_id'] = $session_id;

						$data            = array();
						$data['user_id'] = $user_info_row['user_id'];

						//$data['session_id'] = $session_id;
						$encrypt_str = Perm::encryptUserInfo($data, $session_id);

						$arr_body['k'] = $encrypt_str;
						fb($arr_body);
						$this->data->addBody(100, $arr_body);

					}
					else
					{
						$this->data->setError('登录失败');
					}

				}

				$login_flag = true;


				if(request_string('callback'))
				{
					$us = $arr_body['user_id'];
					$ks = $arr_body['k'];
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
			//失败

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
