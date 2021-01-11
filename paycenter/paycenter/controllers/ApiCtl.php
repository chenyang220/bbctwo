<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Api接口, 让App等调用
 *
 *
 * @category   Game
 * @package    User
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2015 远丰仁商
 * @version    1.0
 * @todo
 */
class ApiCtl extends Yf_AppController
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

		//include $this->view->getView();


		/*
		$base_app_row = array_pop($base_app_rows);
		$key = $base_app_row['app_key'];

		if (!check_url_with_encrypt($key, $_POST))
		{
			$this->data->setError('协议数据有误');

			$d = $this->data->getDataRows();

			$protocol_data = Yf_Data::encodeProtocolData($d);
			echo $protocol_data;

			exit();
		}
		*/

		//判断用户是否存在, 不存在则从ucenter初始化

		/*
		//获取用户信息
		//$user_id = Perm::$userId;
		$user_id = 1;
		$user_account = '111test11';

		//本地读取远程信息
		$key = Yf_Registry::get('ucenter_api_key');

		$url                       = Yf_Registry::get('ucenter_api_url');
		$ucenter_app_id            = Yf_Registry::get('ucenter_app_id');
		$formvars                  = array();
		$formvars['app_id']        = $ucenter_app_id;

		$formvars['ctl'] = 'Api';
		$formvars['met'] = 'getUserInfo';
		$formvars['typ'] = 'json';
		$formvars['user_id'] = $user_id;
		$user         = get_url_with_encrypt($key, $url, $formvars);


		if ($user['status'] == 200)
		{
			$user_info = current($user['data']);
		}
		else
		{
			$user_info = array();
		}
		*/
	}


	/**
	 * 用户登录
	 *
	 * @access public
	 * http://localhost/imbuilder/index.php?ctl=Api&met=login&user_account=admin&user_password=111111
	 */
	public function login()
	{
		$user_account = $_REQUEST['user_account'];
		//本地读取远程信息
		$key = Yf_Registry::get('ucenter_im_key');;
		$url    = Yf_Registry::get('ucenter_api_url');
		$app_id = Yf_Registry::get('app_id');

		$formvars              = array();
		$formvars['user_account'] = $_REQUEST['user_account'];
		$formvars['user_password']  = $_REQUEST['user_password'];
		$formvars['app_id']    = $app_id;

		$formvars['ctl'] = 'Api';
		$formvars['met'] = 'login';
		$formvars['typ'] = 'json';
		
		$init_rs         = get_url_with_encrypt($key, $url, $formvars);

		fb($init_rs);
		$server_id = 10001;
		if (200 == $init_rs['status'])
		{
			/*
			//读取服务列表
			$formvars = array();
			$formvars['user_name'] = $_REQUEST['user_account'];
			$formvars['app_id'] = $app_id;

			$formvars['ctl'] = 'Api';
			$formvars['met'] = 'getUserAppServer';
			$formvars['typ'] = 'json';
			$server_rows_rs = get_url_with_encrypt($key, $url, $formvars);

			fb($server_rows_rs);
			if (200 == $server_rows_rs['status'])
			{
				$server_rows = $server_rows_rs['data'];

				$server_row = array_pop($server_rows);
				$server_id = $server_row['server_id'];

				if (!$server_id)
				{
					//location_go_back('尚未开通服务');
					$this->data->setError('尚未开通服务');
					return;
				}
			}
			else
			{
				//location_go_back('获取服务信息有误');
				$this->data->setError('获取服务信息有误');
				return;
			}
			*/
		}
		else
		{
			//location_go_back('登录信息有误');
			$msg = $init_rs['msg'];
			$this->data->setError($msg);
			return;
		}


		$config = Yf_Registry::get('db_cfg');

		$db_row = include INI_PATH . '/db_' . $server_id . '.ini.php';

		fb($db_row);
		//设置本地数据库信息, 通过server_id本地文件读取PHP文件,
		$config['db_cfg_rows'] = array(
			'master' => array(
				'im-builder' => array(
					array(
						$db_row
					)
				)
			)
		);

		Yf_Registry::set('db_cfg', $config);


		$userBaseModel = new User_BaseModel();

		$userInfoModel = new User_InfoModel();

		//本地数据校验登录
		$user_id_row = $userBaseModel->getUserIdByAccount($user_account);

		fb($user_id_row);

		//初始化用户信息,插入数据
		if (!$user_id_row)
		{
			$user_row = array();
			$user_row['user_account'] = $user_account;
			$user_row['user_password'] = md5($formvars['password']);
			//$user_row['user_mobile'] = $user_account;
			$user_row['server_id'] = $server_id;


			$user_id = $userBaseModel->addUser($user_row, true);
			$user_id_row = $userBaseModel->getUserIdByAccount($user_account);


			//插入info表
			$now_time = time();
			$ip = get_ip();
			$user_info = array();
			$user_info['user_name'] = $user_account;
			$user_info['user_reg_time'] = $now_time;
			$user_info['user_count_login'] = 1;
			$user_info['user_lastlogin_time'] = $now_time;
			$user_info['user_lastlogin_ip'] = $ip;
			$user_info['user_reg_ip'] = $ip;

			$userInfoModel->addInfo($user_info);
		}

		if ($user_id_row)
		{
			$user_rows = $userBaseModel->getUser($user_id_row);
			$user_row  = array_pop($user_rows);

			//判断状态是否开启
			if ($user_row['user_delete'] == 1)
			{
				$this->data->setError('用户尚未启用');
				return;
			}

			unset($user_row['user_password']);
			fb($user_row);
		}
		else
		{
		}

		//if ($user_id_row && ($user_row['user_password'] == md5($_REQUEST['user_password'])))
		if ($user_id_row)
		{
			$data              = array();
			$data['user_id']   = $user_row['user_id'];
			$data['server_id'] = $user_row['server_id'];
			srand((double)microtime() * 1000000);
			$user_key = md5(rand(0, 32000));
			$userBaseModel->editSingleField($user_row['user_id'], 'user_key', $user_key, $user_row['user_key']);
			Yf_Hash::setKey($user_key);
			$encrypt_str        = Perm::encryptUserInfo($data);

			$user_row['k'] = $encrypt_str;
			//location_to(Yf_Registry::get('base_url'));
		}
		else
		{
			//location_go_back('输入密码有误');
			$this->data->setError('输入密码有误');
			return;
		}

		//权限设置
		$user_row['user_name'] = $user_row['user_account'];
		fb($user_row);

		$this->data->addBody(-140, $user_row);
	}


	public function checkLogin()
	{
		$user_name  = strtolower($_REQUEST['user_name']);
		$session_id = $_REQUEST['session_id'];

		if (!$user_name || !$session_id)
		{
			$this->data->setError('参数错误');
		}

		$name_hash      = Yf_Hash::hashNum($user_name, 2);

		$User_BaseModel = new User_BaseModel();
		$user_id_row = $User_BaseModel->getUserIdByName($user_name);

		if ($user_id_row)
		{
			$user_info_rows = $User_BaseModel->getUser($user_id_row);

			if ($user_info_rows)
			{
				$user_info_row = array_pop($user_info_rows);
			}
		}

		if (!$user_info_row)
		{
			$this->data->setError('账号不存在');
		}

		if ($user_info_row['session_id'] != $session_id)
		{
			$this->data->setError('登录验证失败');
		}

		$arr_body = array("result" => 1);

		$this->data->addBody($arr_body);
	}

	public function returnVersion()
	{
		echo $_REQUEST['version'];
		die();
	}


	public function index()
	{
		include $this->view->getView();
	}

	/**
	 * 手机获取注册码
	 *
	 * @access public
	 */
	public function regCode()
	{
		$mobile                    = request_string('mobile');

		$data = array();


		//本地读取远程信息
		$key = Yf_Registry::get('ucenter_im_key');;
		$url    = Yf_Registry::get('ucenter_api_url');
		$app_id = Yf_Registry::get('app_id');

		$formvars              = array();
		$formvars['mobile']    = $mobile;
		$formvars['app_id']    = $app_id;

		$formvars['ctl'] = 'Login';
		$formvars['met'] = 'regCode';
		$formvars['typ'] = 'json';
		$init_rs         = get_url_with_encrypt($key, $url, $formvars);

		if (200 == $init_rs['status'])
		{
			$data['user_code'] = $init_rs['data']['user_code'];
		}

		/*
				$contents = array($data['user_code'], 2);
				$tpl_id = 63463;
				$result = Sms::send($mobile, $contents, $tpl_id);
		*/
		{
			if (true)
			{
				$msg = 'success';
				$status = 200;
			}
			else
			{
				$msg = '失败';
				$status = 250;
			}

		}


		$this->data->addBody(-140, $data, $msg, $status);
	}


	/**
	 * 手机获取找回密码验证码
	 *
	 * @access public
	 */
	public function findPasswdCode()
	{
		$mobile                    = request_string('mobile');

		$data = array();


		//本地读取远程信息
		$key = Yf_Registry::get('ucenter_im_key');;
		$url    = Yf_Registry::get('ucenter_api_url');
		$app_id = Yf_Registry::get('app_id');
		//$url = 'http://localhost/pcenter/';


		$formvars              = array();
		$formvars['mobile']    = $mobile;
		$formvars['app_id']    = $app_id;

		$formvars['ctl'] = 'Login';
		$formvars['met'] = 'findPasswdCode';
		$formvars['typ'] = 'json';
		$init_rs         = get_url_with_encrypt($key, $url, $formvars);

		if (200 == $init_rs['status'])
		{
			$data['user_code'] = $init_rs['data']['user_code'];

			$config_cache = Yf_Registry::get('config_cache');

			if (!file_exists($config_cache['default']['cacheDir']))
			{
				mkdir($config_cache['default']['cacheDir']);
			}

			$Cache_Lite = new Cache_Lite_Output($config_cache['default']);

			$Cache_Lite->save($data['user_code'], $mobile);
		}

		/*
				$contents = array($data['user_code'], 2);
				$tpl_id = 63463;
				$result = Sms::send($mobile, $contents, $tpl_id);
		*/
		{
			if (true)
			{
				$msg = 'success';
				$status = 200;
			}
			else
			{
				$msg = '失败';
				$status = 250;
			}

		}


		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function resetPasswd()
	{
		$mobile   = request_string('mobile');
		$account  = request_string('user_account');
		$code     = request_string('user_code');
		$password = request_string('user_password');


		$data = array();


		//本地读取远程信息
		$key = Yf_Registry::get('ucenter_im_key');;
		$url    = Yf_Registry::get('ucenter_api_url');
		$app_id = Yf_Registry::get('app_id');

		//$url = 'http://localhost/pcenter/';

		$formvars                  = array();
		$formvars['mobile']        = $mobile;
		$formvars['user_account']  = $account;
		$formvars['user_password'] = $password;
		$formvars['user_code']     = $code;
		$formvars['app_id']        = $app_id;

		$formvars['ctl'] = 'Login';
		$formvars['met'] = 'resetPasswd';
		$formvars['typ'] = 'json';
		$init_rs         = get_url_with_encrypt($key, $url, $formvars);

		fb($init_rs);
		if (200 == $init_rs['status'])
		{
				$User_BaseModel = new User_BaseModel();

				//检测登录状态
				$user_id_row = $User_BaseModel->getInfoByName($account);

				if ($user_id_row)
				{
					//重置密码
					$user_id          = $user_id_row['user_id'];
					$reset_passwd_row = array();

					$reset_passwd_row['user_password'] = md5($password);

					$flag = $User_BaseModel->editUser($user_id, $reset_passwd_row);

					if ($flag)
					{
						$msg    = '重置密码成功';
						$status = 200;
						$data['user'] = $account;
						$config_cache = Yf_Registry::get('config_cache');
						$Cache_Lite   = new Cache_Lite_Output($config_cache['default']);

						$Cache_Lite->remove($data['user']);
					}
					else
					{
						$msg    = '重置密码失败';
						$status = 250;
					}
				}
				else
				{
					$msg    = '用户不存在';
					$status = 250;
				}
		}

		/*
				$contents = array($data['user_code'], 2);
				$tpl_id = 63463;
				$result = Sms::send($mobile, $contents, $tpl_id);
		*/
		else
		{
				$msg = $init_rs['msg'];
				$status = 250;

		}


		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function resetPasswd1()
	{
		//
		$user_code = request_string('user_code');

		$data         = array();
		$data['user'] = request_string('user_account');
		$data['mobile'] = request_string('mobile');
		$mobile = request_string('mobile');

		if (!$data['mobile'])
		{
			$this->data->setError('手机号不能为空');
			return false;
		}

		if (request_string('user_password'))
		{
			$data['password'] = md5(request_string('user_password'));


			$config_cache = Yf_Registry::get('config_cache');
			$Cache_Lite   = new Cache_Lite_Output($config_cache['default']);

			$user_code_pre = $Cache_Lite->get($data['mobile']);
			fb($user_code);
			fb($user_code_pre);

			if ($user_code == $user_code_pre)
			{
				$User_BaseModel = new User_BaseModel();

				//检测登录状态
				$user_id_row = $User_BaseModel->getInfoByName($data['user']);

				if ($user_id_row)
				{
					//重置密码
					$user_id          = $user_id_row['user_id'];
					$reset_passwd_row = array();

					$reset_passwd_row['user_password'] = $data['password'];

					fb($user_id);
					fb($reset_passwd_row);
					$flag = $User_BaseModel->editUser($user_id, $reset_passwd_row);

					if ($flag)
					{
						$msg    = '重置密码成功';
						$status = 200;

						$Cache_Lite->remove($data['user']);
					}
					else
					{
						$msg    = '重置密码失败';
						$status = 250;
					}
				}
				else
				{
					$msg    = '用户不存在';
					$status = 250;
				}
			}
			else
			{
				$msg = '验证码错误';
				$status = 250;
			}

		}
		else
		{
			$msg    = '密码不能为空';
			$status = 250;
		}


		unset($data['password']);

		$this->data->addBody(-140, $data, $msg, $status);
	}


	public function register()
	{
		$user_account = request_string('user_account', null);

		//本地读取远程信息
		$key = Yf_Registry::get('ucenter_im_key');;
		$url    = Yf_Registry::get('ucenter_api_url');
		$app_id = Yf_Registry::get('app_id');

		$formvars              = array();
		$formvars['user_account'] = $user_account;
		$formvars['user_password']  = request_string('user_password', null);
		$formvars['user_code'] = request_string('user_code');
		$formvars['mobile'] = request_string('mobile');
		$formvars['app_id']    = $app_id;

		$formvars['ctl'] = 'Login';
		$formvars['met'] = 'register';
		$formvars['typ'] = 'json';
		$init_rs         = get_url_with_encrypt($key, $url, $formvars);


		fb($init_rs);
		$server_id = 10001;
		if (200 == $init_rs['status'])
		{
			/*
			//读取服务列表
			$formvars = array();
			$formvars['user_name'] = $_REQUEST['user_account'];
			$formvars['app_id'] = $app_id;

			$formvars['ctl'] = 'Api';
			$formvars['met'] = 'getUserAppServer';
			$formvars['typ'] = 'json';
			$server_rows_rs = get_url_with_encrypt($key, $url, $formvars);

			fb($server_rows_rs);
			if (200 == $server_rows_rs['status'])
			{
				$server_rows = $server_rows_rs['data'];

				$server_row = array_pop($server_rows);
				$server_id = $server_row['server_id'];

				if (!$server_id)
				{
					//location_go_back('尚未开通服务');
					$this->data->setError('尚未开通服务');
					return;
				}
			}
			else
			{
				//location_go_back('获取服务信息有误');
				$this->data->setError('获取服务信息有误');
				return;
			}
			*/
		}
		else
		{
			//location_go_back('登录信息有误');
			$msg = $init_rs['msg'];
			$this->data->setError($msg);
			return;
		}


		$config = Yf_Registry::get('db_cfg');

		$db_row = include INI_PATH . '/db_' . $server_id . '.ini.php';

		fb($db_row);
		//设置本地数据库信息, 通过server_id本地文件读取PHP文件,
		$config['db_cfg_rows'] = array(
			'master' => array(
				'im-builder' => array(
					array(
						$db_row
					)
				)
			)
		);

		Yf_Registry::set('db_cfg', $config);


		$userBaseModel = new User_BaseModel();
		$userInfoModel = new User_InfoModel();

		//本地数据校验登录
		$user_id_row = $userBaseModel->getUserIdByAccount($user_account);

		if (!$user_id_row)
		{
			$user_row = array();
			$user_row['user_account'] = $user_account;
			$user_row['user_password'] = md5($formvars['user_password']);
			//$user_row['user_mobile'] = $user_account;
			$user_row['server_id'] = $server_id;


			$user_id = $userBaseModel->addUser($user_row, true);
			$user_row['user_id'] = $user_id;
			$user_row['user_key'] = '';


			//插入info表
			$ip       = get_ip();
			$mobile = request_string('mobile');
			$user_info = array();
			$user_info['user_name'] = $user_account;
			$user_info['user_reg_time'] = time();
			$user_info['user_count_login'] = 1;
			$user_info['user_lastlogin_time'] = time();
			$user_info['user_reg_ip'] = $ip;
			$user_info['user_lastlogin_ip'] = $ip;
			$user_info['user_mobile'] = $mobile;
			$userInfoModel->addInfo($user_info);

			//登录
			if ($user_id)
			{
				$data              = array();
				$data['user_id']   = $user_row['user_id'];
				$data['server_id'] = $user_row['server_id'];
				srand((double)microtime() * 1000000);
				$user_key = md5(rand(0, 32000));
				$userBaseModel->editSingleField($user_row['user_id'], 'user_key', $user_key, $user_row['user_key']);
				Yf_Hash::setKey($user_key);
				$encrypt_str        = Perm::encryptUserInfo($data);

				$user_row['k'] = $encrypt_str;
				//location_to(Yf_Registry::get('base_url'));
			}
			else
			{
				//location_go_back('输入密码有误');

				$this->data->setError('输入密码有误');
				return;
			}
		}


		//权限设置

		$this->data->addBody(-140, $user_row);
	}

	/*
	 * 检测登录数据是否正确,app端先直接请求用户中心登录, 获取登录信息后发送到此处验证, 此处请求用户中心判断是否正确,然后完成app登录
	 *
	 *
	 */
	public function check()
	{
		$ucenter_u    = request_string('ucenter_u', null);
		$ucenter_key  = request_string('ucenter_key', null);


		//本地读取远程信息
		$key = Yf_Registry::get('ucenter_im_key');;
		$url    = Yf_Registry::get('ucenter_api_url');
		$app_id = Yf_Registry::get('app_id');

		$formvars              = array();
		$formvars['ucenter_u'] = $ucenter_u;
		$formvars['ucenter_key']  = $ucenter_key;
		$formvars['app_id']    = $app_id;

		$formvars['ctl'] = 'Api';
		$formvars['met'] = 'checkLogin';
		$formvars['typ'] = 'json';
		$init_rs         = get_url_with_encrypt($key, $url, $formvars);

		fb($init_rs);
		$server_id = 10001;
		if (200 == $init_rs['status'])
		{
			/*
			//读取服务列表
			$formvars = array();
			$formvars['user_name'] = $_REQUEST['user_account'];
			$formvars['app_id'] = $app_id;

			$formvars['ctl'] = 'Api';
			$formvars['met'] = 'getUserAppServer';
			$formvars['typ'] = 'json';
			$server_rows_rs = get_url_with_encrypt($key, $url, $formvars);

			fb($server_rows_rs);
			if (200 == $server_rows_rs['status'])
			{
				$server_rows = $server_rows_rs['data'];

				$server_row = array_pop($server_rows);
				$server_id = $server_row['server_id'];

				if (!$server_id)
				{
					//location_go_back('尚未开通服务');
					$this->data->setError('尚未开通服务');
					return;
				}
			}
			else
			{
				//location_go_back('获取服务信息有误');
				$this->data->setError('获取服务信息有误');
				return;
			}
			*/
		}
		else
		{
			//location_go_back('登录信息有误');
			$this->data->setError('登录信息有误');
			return;
		}


		$config = Yf_Registry::get('db_cfg');

		$db_row = include INI_PATH . '/db_' . $server_id . '.ini.php';

		fb($db_row);
		//设置本地数据库信息, 通过server_id本地文件读取PHP文件,
		$config['db_cfg_rows'] = array(
			'master' => array(
				'im-builder' => array(
					array(
						$db_row
					)
				)
			)
		);

		Yf_Registry::set('db_cfg', $config);


		$userBaseModel = new User_BaseModel();

		//本地数据校验登录
		$user_id_row = $userBaseModel->getUserIdByAccount($user_account);

		if ($user_id_row)
		{
			$user_rows = $userBaseModel->getUser($user_id_row);
			$user_row  = array_pop($user_rows);

			//判断状态是否开启
			if ($user_row['user_delete'] == 1)
			{
				$this->data->setError('用户尚未启用');
				return;
			}

			unset($user_row['user_password']);
			fb($user_row);
		}

		//if ($user_id_row && ($user_row['user_password'] == md5($_REQUEST['user_password'])))
		if ($user_id_row)
		{
			$data              = array();
			$data['user_id']   = $user_row['user_id'];
			$data['server_id'] = $user_row['server_id'];
			srand((double)microtime() * 1000000);
			$user_key = md5(rand(0, 32000));
			$userBaseModel->editSingleField($user_row['user_id'], 'user_key', $user_key, $user_row['user_key']);
			Yf_Hash::setKey($user_key);
			$encrypt_str        = Perm::encryptUserInfo($data);

			$user_row['k'] = $encrypt_str;
			//location_to(Yf_Registry::get('base_url'));
		}
		else
		{
			//location_go_back('输入密码有误');

			$this->data->setError('输入密码有误');
			return;
		}

		//权限设置

		$this->data->addBody(-140, $user_row);
	}

	/*
	 * 用户退出
	 *
	 *
	 */
	public function loginout()
	{
		if ($_REQUEST['met'] == 'loginout')
		{
			if(isset($_COOKIE['key']) || isset($_COOKIE['id']))
			{
				echo "<script>parent.location.href='index.php';</script>";
				setcookie("key", null, time()-3600*24*365);
				setcookie("id", null, time()-3600*24*365);

			}
		}
	}
    public function getPayWays()
    {
		$type = $_REQUEST['type'];
		$Pay_PaymentChannelModel = new Pay_PaymentChannelModel();
		$data = $Pay_PaymentChannelModel->getPayWaysByCode($type);
		fb($data);
		if ($flag)
		{
			$msg    = 'success';
			$status = 200;
		}
		else
		{
			$msg    = 'failure';
			$status = 250;
		}
		$this->data->addBody(-140, $data,$msg, $status);
	}
	public function editPay()
	{
		$payid = $_REQUEST['payment_channel_id'];
		$data['payment_channel_code'] = $_REQUEST['payment_channel_code'];
		$data['payment_channel_name'] = $_REQUEST['payment_channel_name'];
		$data['payment_channel_status'] = 1;
		$data['payment_channel_config'] = $_REQUEST['payment_channel_config'];
		
		
		$Pay_PaymentChannelModel = new Pay_PaymentChannelModel();
		$flag = $Pay_PaymentChannelModel->editPaymentChannel($payid,$data);
		
		if ($flag)
		{
			$msg    = 'success';
			$status = 200;
		}
		else
		{
			$msg    = 'failure';
			$status = 250;
		}
		$this->data->addBody(-140, $data, $msg, $status);
	}
	public function editPayStatus()
	{
		$payid = $_REQUEST['payment_channel_id'];
		$Pay_PaymentChannelModel = new Pay_PaymentChannelModel();
		$date = $Pay_PaymentChannelModel->getPaymentChannel($payid);
		
		$payment_channel_status = $date[$payid]['payment_channel_status']?0:1;
		$data =array('payment_channel_status'=>$payment_channel_status);
		$flag = $Pay_PaymentChannelModel->editPaymentChannel($payid,$data);
		
		
		//$data=array();
		//fb($flag);
		if ($flag)
		{
			$msg    = 'success';
			$status = 200;
		}
		else
		{
			$msg    = 'failure';
			$status = 250;
		}
		$this->data->addBody(-140, $data, $msg, $status);
	}

	/*
	 *中库调查询支付状态
	 */
	public function getPayAfterStatus()
    {
		$union_order_id = request_string('uorder');
		$u_id = request_int('u_id');
   		$db = new YFSQL();
        $sql = "select * from ucenter_user_info where u_id=" . $u_id;
        $ucenter_user_info_get = $db->find($sql);
		$ucenter_user_info = current($ucenter_user_info_get);


		$data = array();
        if (!$ucenter_user_info) {
        	$data['msg'] = "查询的该用户不存在！";
			$data['status'] = 250;
        } else {

        	$Union_OrderModel = new Union_OrderModel();
        	$Union_Order = $Union_OrderModel->getOne($union_order_id);
        	if (!$Union_Order) {
        		$data['msg'] = "查询的该支付单号不存在！";
				$data['status'] = 250;
        	} else {
        		if ($Union_Order['buyer_id'] != $ucenter_user_info['user_id']) {
        			$data['msg'] = "查询用户与下单用户不一致，请核对信息！";
					$data['status'] = 250;
        		} else {
        			$data['data']['order_state_id'] = $Union_Order['order_state_id'];
        			$data['data']['union_order_id'] = $Union_Order['union_order_id'];
        			$data['data']['inorder'] = $Union_Order['inorder'];
					$data['msg'] = "查询成功";
					$data['status'] = 200;
        		}
        	}
        }
		echo(json_encode($data));
		exit();
    }


	/*
	 *中库调微信退款接口
	*/
	public function wxReturnMoneyApi()
    {
	    $Payment_JhWxAppModel =	new Payment_JhWxAppModel();
	    $Union_OrderModel = new Union_OrderModel();
		$db = new YFSQL();
		$u_id =  request_string('u_id');
	    $union_order_id =  request_string('union_order_id');
		$return_type =  request_string('return_type');//1-退款申请 2-退货申请 3-虚拟退款',
	    $Union_Order = $Union_OrderModel->getOneByWhere(array("union_order_id"=>$union_order_id,"order_state_id>"=>1));
	    if (!$Union_Order) {
	    	$data['data']['inorder'] = $union_order_id;
			$data['msg'] = "接口订单不存在！";
			$data['status'] = 250;
	    	echo(json_encode($data,JSON_UNESCAPED_UNICODE));
			exit();
	    }
		$u_sql = "select * from ucenter_user_info where u_id=$u_id";
		$ucenter_user_info_get = $db->find($u_sql);
		$ucenter_user_info = current($ucenter_user_info_get);
		if ($ucenter_user_info['user_id'] != $Union_Order['buyer_id']) {
			$data['data']['u_id'] = $u_id;
			$data['msg'] = "该用户非购买用户无法退款！";
			$data['status'] = 250;
	    	echo(json_encode($data,JSON_UNESCAPED_UNICODE));
			exit();
		}

		$sql = "SELECT * from yf_order_goods where order_id = '" . $Union_Order['inorder'] . "'";
		$order_goods_rows = $db->find($sql);
		if (!$order_goods_rows) {
			$data['msg'] = "提交信息查无此退款商品！";
			$data['status'] = 250;
			echo(json_encode($data,JSON_UNESCAPED_UNICODE));
			exit();
		}
	    // $Payment_JhWxApp = $Payment_JhWxAppModel->wxReturnMoney($Union_Order);
	   	$Union_OrderModel->sql->startTransactionDb();
	    // if ($Payment_JhWxApp['errcode'] == 0 && $order_goods_rows) {	
		$sql1 = "select * from yf_order_base where order_id='". $Union_Order['inorder'] . "'";
		$order_base_select = $db->find($sql1);
		$order_base = current($order_base_select);
        $prefix = sprintf('%s-%s', Yf_Registry::get('shop_app_id'), date('YmdHis'));
        $return_code = sprintf('%s-%s-%s-%s', 'TD', $ucenter_user_info['user_id'], 0, $prefix);
		$return_add_time = get_date_time();
		$order_return_status = 0;
		$order_refund_status = 0;
		$order_return_num = 0;
		if ($return_type == 2) {
			//退货
			$return_goods_return = 1;
			$order_return_status = 2;
		} else if($return_type == 1){
			//退款
			$order_refund_status = 2;
		  	$return_goods_return = 0;	
		} else {
			$return_goods_return = 0;
		}


		foreach ($order_goods_rows as $key => $order_goods) {
			$sql = "INSERT INTO `yf_order_return` 
				(`order_return_id`, `order_number`, `order_is_virtual`, `order_amount`, `order_goods_id`, `order_goods_name`, `order_goods_price`, `order_goods_num`, `order_goods_pic`, `return_code`, `return_type`, `seller_user_id`, `seller_user_account`, `buyer_user_id`, `buyer_user_account`, `return_add_time`, `return_reason_id`, `return_reason`, `return_message`, `return_real_name`, `return_addr_id`, `return_addr_name`, `return_addr`, `return_post_code`, `return_tel`, `return_mobile`, `return_state`, `return_cash`, `return_shop_time`, `return_shop_message`, `return_finish_time`, `return_commision_fee`, `return_platform_message`, `return_goods_return`, `behalf_deliver`, `return_rpt_cash`, `return_shop_handle`, `district_id`)
				VALUES
				('', '" . $Union_Order['inorder'] ."', " . $order_base['order_is_virtual'] . "," . $order_base['order_payment_amount'] . ", 1, '" . $order_goods['goods_name'] . "', " . $order_goods['goods_price'] . "," . $order_goods['order_goods_num'] . ",'" . $order_goods['goods_image'] . "','" . $return_code . "'," . $return_type . "," . $order_base['shop_id'] . ",'" . $order_base['shop_name'] . "'," . $order_base['buyer_user_id'] . ",'" . $order_base['buyer_user_name'] . "','" . $return_add_time . "','','','','" . $order_base['order_receiver_name'] . "','0','','" . $order_base['order_receiver_address'] . "','','" . $order_base['order_receiver_contact'] . "','" . $order_base['order_receiver_contact'] . "','5'," . $order_base['order_payment_amount'] . ",'" . $return_add_time . "','','" . $return_add_time . "','0.00',''," . $return_goods_return . ",'0','0.00','2','145');
				";			
			$db->find($sql);
			$update_order_goods_sql = "UPDATE yf_order_goods set order_goods_status='6',goods_return_status=" . $order_refund_status . ",goods_refund_status= " . $order_return_status . " where order_id= '" .$Union_Order['inorder'] . "'" . "and goods_name LIKE '%" . $goods_name . "%'";			
			$db->find($update_order_goods_sql);
		}
		
		$update_order_base_sql = "UPDATE yf_order_base set order_status = '6', order_refund_status = " . $order_refund_status . " ,order_return_status=" . $order_return_status . ", order_return_status = " . $order_base['order_payment_amount'] . ",order_return_num = " . $order_return_num . "  where order_id= '" . $Union_Order['inorder'] . "'";	
		$db->find($update_order_base_sql);
	
		$select_add_order_return = "SELECT * from yf_order_return where order_number='" . $Union_Order['inorder'] . "'";
		$flge = $db->find($select_add_order_return);
		if ($flge) {
			$Payment_JhWxApp = $Payment_JhWxAppModel->wxReturnMoney($Union_Order);
			if ($Payment_JhWxApp['errcode'] == 0) {	
				$Union_OrderModel->sql-> commitDb();
				$data['msg'] = "退款成功";
				$data['status'] = 200;
			} else {
				$Union_OrderModel->sql-> rollBackDb();
				$data['msg'] = "提交失败,请稍后重试！";
				$data['status'] = 250;
			}
		} else {
			$Union_OrderModel->sql-> rollBackDb();
			$data['msg'] = "提交失败,提交信息有误！";
			$data['status'] = 250;
		}
			// if ($flge && $Union_OrderModel->sql-> commitDb()) {
			// 	$data['msg'] = "退款成功";
			// 	$data['status'] = 200;
			// } else {
			// 	$Union_OrderModel->sql-> rollBackDb();
			// 	$data['msg'] = "提交失败,请稍后重试！";
			// 	$data['status'] = 250;
			// }
	  //   } else {
			// $data['msg'] = "退款失败,请稍后重试！";
			// $data['status'] = 250;
	  //   }
	    $data['order_id'] = $Union_Order['inorder'];
		echo(json_encode($data,JSON_UNESCAPED_UNICODE));
		exit();
    }
    	
	/*
	 *中库调支付的页面
	 */
	public function payApi()
    {
        $shop_id_cookie = request_int('shop_id_cookie');
        $shop_id_url = request_string('shop_id_url');
        $user_id = Perm::$userId;
        $uorder = request_string('uorder');
        $act = request_string('act');
        if (request_string("return_url")) {
        	$return_url = request_string("return_url");
        } else {
        	$return_url = Yf_Registry::get('shop_api_url');
        }
   
        $appToken = '';
		$rrr =explode("&",$return_url) ;

		foreach ($rrr as $key => $value) {
			if (strstr($value,"appToken=")) {
				$result = explode("appToken=", $value);
				$appToken = $result[1];
			}
		}
        $Union_OrderModel = new Union_OrderModel();
        $Union_OrderModel->editUnionOrder($uorder,array("r_url"=>$return_url));    
        //用于判断订单类型，order_g_type = physical实物订单，virtual虚拟订单
        $order_g_type = request_string('order_g_type') ? request_string('order_g_type'):'physical';
        //获取需要支付的订单信息
        $Union_OrderModel = new Union_OrderModel();
        $uorder_base = $Union_OrderModel->getOne($uorder);

        if($uorder_base['is_presale']==1&&$uorder_base['order_state_id']==1){
            $uorder_base['trade_payment_amount'] = $uorder_base['presale_deposit'];
        }
        if($uorder_base['is_presale']==1&&$uorder_base['order_state_id']==20){
            $uorder_base['trade_payment_amount'] = $uorder_base['final_price'];
        }
        
        $Consume_TradeModel = new Consume_TradeModel();
        if($shop_id_cookie){
            $Consume_TradeModel->editTrade($uorder_base['inorder'],array('shop_id_cookie'=>$shop_id_cookie,'shop_id_url'=>$shop_id_url));
        }
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = [];
        $formvars['app_id'] = $shop_app_id;
        $formvars['order_id'] = $uorder_base['inorder'];
        //如果是分销订单
        $User_InfoModel = new User_InfoModel();
        if ($act != 'deposit') {
            //查找订单信息
            $Consume_TradeModel = new Consume_TradeModel();
            $consume_trade = $Consume_TradeModel->getOne($uorder_base['inorder']);
            if ($consume_trade['pay_user_id'] == Perm::$userId && $consume_trade['buyer_id'] != Perm::$userId) {
                //查找子账号的用户名
                $buyer_user_info = $User_InfoModel->getUserInfo($consume_trade['buyer_id']);
                //查找订单的商品信息
                $order_goods = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=getGoodsByOrderId&typ=json', $url), $formvars);
                $order_goods = array_values($order_goods['data']);
            }
        }
        //查询可使用的支付方式
        $Payment_ChannelModel = new Payment_ChannelModel();
        $payment_channel = $Payment_ChannelModel->getByWhere(['payment_channel_enable' => Payment_ChannelModel::ENABLE_YES]);
        $payment_channel = array_values($payment_channel);
        //查询该用户是否存在主管账号
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = [];
        $formvars['app_id'] = $shop_app_id;
        $formvars['sub_user_id'] = Perm::$userId;
        $sub_user = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=getSubUser&typ=json', $url), $formvars);
        $sub_user_status = false;
        if ($sub_user['status'] == 200 && $sub_user['data']['count'] > 0) {
            $sub_user_status = true;
        }
        //获取当前用户的资金
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getOne($user_id);
        //去掉不可用图标显示的支付方式
        //微信比较提别，这里的微信图标统一使用wx_native，客户端监听到微信并调取客户端时，再根据应用场景传对应参数调取app_wx_native或app_h5_wx_native
        $pay_channel = ['cards', 'money', 'app_wx_native', 'app_h5_wx_native', 'alipayMobile'];
        foreach ($payment_channel as $key => $val) {
            if ($val['payment_channel_code'] === 'baitiao') {
                $bt_info = $val;
                unset($payment_channel[$key]);
            }
            if (in_array($val['payment_channel_code'], $pay_channel)) {
                unset($payment_channel[$key]);
            }
        }
        if (isset($bt_info)) {
            //获取额度信息和认证信息
            $user_info = $User_InfoModel->getUserInfo($user_id);
            $data = [];
            if ($user_info['user_bt_status'] == 2) {
                $user_resource_model = new User_ResourceModel();
                $result = $user_resource_model->getOne($user_id);
                $bt_money = $result['user_credit_availability'];
                $bt_type = $result['bt_type'];
                //查看是否逾期,如果逾期白条不可用
                $bt_use = $this->checkBtUse();
            }
        }

        //是否开启大华捷通支付
        $yunshan_status = Web_ConfigModel::value('yunshan_status');
        if ($yunshan_status) {
            $payment_channel = array(
               0 => array(
                'payment_channel_code' => 'yunshanpc',
                'payment_channel_name' => '大华捷通支付',
                'payment_channel_image' => 'paycenter/static/default/images/yunshanpc.png'
               )
            );

            $is_mobile = self::is_mobile();
            if ($is_mobile=='mobile'){
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                    //微信内置浏览器打开网页

                }else{
                    if(!$_COOKIE['is_app_guest']){
                        $payment_channel = array_values($payment_channel);
                        foreach ($payment_channel as $key => $value) {
                            if ($value['payment_channel_code']=='wx_native') {
                                unset($payment_channel[$key]);
                            }
                        }
                        $count_num = count($payment_channel);
                        $payment_channel = array_values($payment_channel);
                        for ($i=0; $i <$count_num ; $i++) {
                            $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                            if ($payment_channel[$i]['payment_channel_code']=='yunshanpc'){
                                for ($i=0; $i <$count_num ; $i++) {
                                    $payment_channel[$i]['payment_channel_code'] = 'yunshanpc';
                                }
                            }
                        }
                    }else{
                        $count_num = count($payment_channel);
                        $payment_channel = array_values($payment_channel);
                        for ($i=0; $i <$count_num ; $i++) {
                            $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                            if ($payment_channel[$i]['payment_channel_code']=='yunshanpc'){
                                for ($i=0; $i <$count_num ; $i++) {
                                    $payment_channel[$i]['payment_channel_codess'] = $payment_channel[$i]['payment_channel_code'];
                                    $payment_channel[$i]['payment_channel_code'] = 'yunshanpc';
                                }
                            }
                        }
                    }
                }
                foreach($payment_channel as $key=>&$val){
                    if($val['payment_channel_code']=='alipay'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/zhifubao_app.png';
                    }else if($val['payment_channel_code']=='wx_native'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/weixin_app.png';
                    }else if($val['payment_channel_code']=='yunshanpc'){
                        $val['payment_channel_image'] = '../paycenter/static/default/images/yunshan_app.png';
                    }
                }
                $this->view->setMet('pay_app');

            }

        }
        include $this->view->getView();
    }
}

?>