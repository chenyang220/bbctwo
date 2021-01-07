<?php

//用户登录认证
class Plugin_Perm implements Yf_Plugin_Interface
{
	//解析函数的参数是pluginManager的引用
	function __construct()
	{
		//注册这个插件
		//第一个参数是钩子的名称
		//第二个参数是pluginManager的引用
		//第三个是插件所执行的方法
		Yf_Plugin_Manager::getInstance()->register('perm', $this, 'checkPerm');
		Yf_Plugin_Manager::getInstance()->register('server_state', $this, 'checkServerState');
	}

	public static function desc()
	{
		return '<b>这个是用户权限认证插件!</b>';
	}

	public function checkPerm()
	{
		$data = new Yf_Data();

		//无需权限判断的文件
		$not_perm = array(
			'Pay',
			'Login',
			'Api',
			'Media'
		);

		//不需要登录
		if (!isset($_REQUEST['ctl']) || (isset($_REQUEST['ctl']) && in_array($_REQUEST['ctl'], $not_perm)))
		{

		}
		elseif (Perm::checkUserPerm())
		{

			if (!Perm::checkUserRights())
			{
				if ('e' == $_REQUEST['typ'])
				{
					// echo <<<EOF
					// <script type="text/javascript">
					//  	parent.Public.tips({content: '无访问权限'});
					// </script>
					// '无访问权限'
					// EOF;
				}
				else
				{
					$data->setError(_('无访问权限'));
					return $this->outputError($data);
				}
				
			}
			//拦截处理管理员cookie认证
			// if(!$this->cookieAuth()){
			// 	location_to(Yf_Registry::get('url') . '?ctl=Login');
			// 	$data->setError(_('需要登录'), 30);
			// 	return $this->outputError($data);
			// }
		}
		else
		{
			location_to(Yf_Registry::get('url') . '?ctl=Login');
			$data->setError(_('需要登录'), 30);
			return $this->outputError($data);
		}
	}

	public function outputError($data)
	{
		$d = $data->getDataRows();
		$protocol_data = Yf_Data::encodeProtocolData($d);
		echo $protocol_data;
		exit();
	}
	
	/**
	* 管理员cookie认证
	* 备注：管理员登录，不动原有cookie有效期的情况下，增加第二层验证
	* 适用情况（管理员后台修改密码，其他管理员的cookie失效）
	* @nsy 2020-04-02
	**/
	protected function cookieAuth(){
		$web_config_model = new Web_ConfigModel();
		$key = 'admin_login_cookie_auth';     
        $conf = $web_config_model->getOne($key);
		if(!$conf || (time()-$conf['config_value'])>0){//超过一个月客户端已经过期，也在这里中断
			return true;
		} else {
			$admin_uid = $_COOKIE['id']?$_COOKIE['id']:0;
			$ssid = $_COOKIE['PHPSESSID']?$_COOKIE['PHPSESSID']:0;		
			$sql = "select sys_expire,create_time from yf_admin_cookie_auth where ssid = '{$ssid }' and admin_user_id='{$admin_uid}' order by id desc limit 1";
			$result = $web_config_model ->sql->getRow($sql);
			if(!$result || ((time()-$result['sys_expire'])>0) ||$result['sys_expire']!==$conf['config_value'] ){
				//这里也可以进行cookie清除操作
				$expires = time() - 3600;
				setcookie('id', null, $expires, '/');
				setcookie('key', null, $expires,'/');
				return false;
			}
			return true;
		}
	}
}