<?php
    
    class LoginCtl extends AdminController
    {
        public function index()
        {
            include $this -> view -> getView();
        }
        
        /**
         * 用户登录
         *
         * @access public
         */
        public function login()
        {
            session_start();
            if (strtolower($_COOKIE['auth']) != strtolower($_REQUEST['yzm']) || empty($_REQUEST['yzm'])) {
                $data = array();
                $msg = '验证码错误!';
                $status = 250;
				setcookie("auth", null, time() - 3600 * 24 * 365);
                return $this -> data -> addBody(-140, $data, $msg, $status);
            }
            $user_account = $_REQUEST['user_account'];
			//验证是否触发当天禁登(当天登录失败记录条数>=6时，禁止当天登录)
			/*$beginToday = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$endToday = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			$sql = "select count(1)cts from ucenter_login_limit where 1=1 and account='{$user_account}' and type=2 and (login_time >={$beginToday } and login_time<={$endToday})";
            $userBaseModel = new User_BaseModel();
			$err_ct = $userBaseModel->sql->getRow($sql)['cts'];
			if($err_ct>=6){
				return  $this->data->addBody(-140, array(), '登录失败次数已达6次，今天已被禁止登录！', 250);
			}*/
            //本地读取远程信息
            $key = Yf_Registry::get('ucenter_api_key');;
            $url = Yf_Registry::get('ucenter_api_url');
            $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
            $formvars = array();
            $formvars['user_account'] = $_REQUEST['user_account'];
            $formvars['user_password'] = $_REQUEST['user_password'];
            $formvars['app_id'] = $ucenter_app_id;
            $formvars['ctl'] = 'Api';
            $formvars['met'] = 'login';
            $formvars['typ'] = 'json';
            $init_rs = get_url_with_encrypt($key, $url, $formvars);
            
            if (200 == $init_rs['status']) {
                //读取服务列表
            } else {
                //location_go_back(isset($init_rs['msg']) ? '登录失败,请重新登录!' . $init_rs['msg'] : '登录失败,请重新登录!');
                $data = array();
                $msg = isset($init_rs['msg']) ? $init_rs['msg'] . '!' : '登录失败,请重新登录!';
                $status = 250;
                return $this -> data -> addBody(-140, $data, $msg, $status);
            }
            $userBaseModel = new User_BaseModel();
            //本地数据校验登录
            $user_id_row = $userBaseModel -> getUserIdByAccount($user_account);
            if ($user_id_row) {
                $user_rows = $userBaseModel -> getBase($user_id_row);
                $user_row = array_pop($user_rows);
                //判断状态是否开启
                if ($user_row['user_delete'] == 1) {
                    //return location_go_back('');
                    $data = array();
                    $msg = '该账户未启用，请启用后登录！';
                    $status = 250;
                    return $this -> data -> addBody(-140, $data, $msg, $status);
                }
                //fb($user_row);
            } else {
                $user_row = $init_rs['data'];
                //return location_go_back('该账户未启用，请启用后登录！');
                $data = array();
                $msg = '该账户未启用，请启用后登录！';
                $status = 250;
                return $this -> data -> addBody(-140, $data, $msg, $status);
            }
            if ($user_row) {
                $data = array();
                $data['user_id'] = $user_row['user_id'];
                srand((double)microtime() * 1000000);
                //$user_key = md5(rand(0, 32000));
                $user_key = 'ttt';
                $flag = $userBaseModel -> editBaseSingleField($user_row['user_id'], 'user_key', $user_key, $user_row['user_key']);
                Yf_Hash::setKey($user_key);
                Perm::encryptUserInfo($data);
                //location_to(Yf_Registry::get('base_url'));
                $msg = '登录成功';
                $status = 200;
				/****************************************************************/
				$auth_time = Web_ConfigModel::value('admin_login_cookie_auth');
				//没超过系统设置的过期时间，需要加入登录日志
				if($auth_time && ((time()-$auth_time)<0)){
					$login_data = array(
						'ssid' => $_COOKIE['PHPSESSID']?$_COOKIE['PHPSESSID']:0,
						'admin_user_id' => $data['user_id'],
						'create_time' => time(),
						'ip' => ip(),
						'sys_expire' => Web_ConfigModel::value('admin_login_cookie_auth')
					);
					$this->addAdminLoginCookie($userBaseModel,$login_data);
				}
				/*******************************************************************/
                return $this -> data -> addBody(-140, array('url' => Yf_Registry::get('base_url')), $msg, $status);
            } else {
                //location_go_back('输入密码有误');
                $data = array();
                $msg = '输入密码有误！';
                $status = 250;
                return $this -> data -> addBody(-140, $data, $msg, $status);
            }
            //权限设置
        }
        
        /*
         * 用户退出
         *
         *
         */
        public function loginout()
        {
            if ($_REQUEST['met'] == 'loginout') {
                if (isset($_COOKIE['key']) || isset($_COOKIE['id'])) {
                    setcookie("key", null, time() - 3600 * 24 * 365, '/');
                    setcookie("id", null, time() - 3600 * 24 * 365, '/');
                    echo "<script>parent.location.href='" . Yf_Registry::get('base_url') . "';</script>";
                } else {
                    echo "<script>parent.location.href='" . Yf_Registry::get('base_url') . "';</script>";
                }
            }
        }
        
        public function getCheckCode()
        {
            session_start();
            //===============================
            $width = $_GET['w'] ? $_GET['w'] : "80";
            $height = $_GET['h'] ? $_GET['h'] : "33";
            $image = new ValidationCode($width, $height, '4');
            $image -> outImg();
            $_SESSION["auth"] = $image -> checkcode;
        }
		
		/**
		* 管理员登录cookie二次验证记录
		* @nsy 2020-04-02
		**/
		protected function addAdminLoginCookie(Yf_Model $model,array $data){
			$sql = "insert into yf_admin_cookie_auth (ssid,admin_user_id,create_time,ip,sys_expire) 
			values
			('{$data['ssid']}','{$data['admin_user_id']}','{$data['create_time']}','{$data['ip']}','{$data['sys_expire']}');";
			$result = $model ->sql->getRow($sql);
			return $result;
		}
    }

?>


