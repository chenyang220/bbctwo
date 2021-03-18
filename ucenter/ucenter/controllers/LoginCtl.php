<?php
header("Access-Control-Allow-Credentials: true");
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN']:'';
header("Access-Control-Allow-Origin:$origin");

class LoginCtl extends Yf_AppController
{
    public function callback()
    {
        echo 'callback 地址';
    }

    /**
     * 授权登录
     * 
     * @dateTime  2020-08-16
     * @author fzh
     * @copyright https://www.yuanfeng.cn
     * @license   仅限本公司授权用户使用。
     * @version   3.8.1
     * @return    [type]                  [description]
     */
    public function oauth(){

        $u_id = request_string('u_id');
        $return_url = request_string('return_url');
        $User_InfoModel = new User_InfoModel();
        $user_info_row = $User_InfoModel->getOneByWhere(array("u_id"=>$u_id));
        $User_InfoDetailModel = new User_InfoDetailModel();
        $User_InfoDetail_row = $User_InfoDetailModel->getOneByWhere(array("user_name"=>$user_info_row['user_name']));
        if (3 == $user_info_row['user_state']) {
            $result["status"] = "250";
            $result["msg"] = "用户已经锁定,禁止登录!";
            die(json_encode($result));
        }
        $session_id = $user_info_row['session_id'];
        $arr_body = $user_info_row;
        $arr_body['mobile'] = $user_info_row['user_name'];
        $user_id = $user_info_row['user_id'];
        $arr_body['result'] = 1;
        $server_id = 0;
        $data = array();
        $data['user_id'] = $user_info_row['user_id'];
        $encrypt_str = Perm::encryptUserInfo($data, $session_id);

        $arr_body = array(
            "user_name" => $user_info_row['user_name'],
            "server_id" => $server_id,
            "k" => $encrypt_str,
            "user_id" => $user_id,
            "mobile" => $user_info_row['user_name']
        );
        $this->check_paycenter_and_shop($user_info_row['user_id'] , $encrypt_str , $user_info_row['user_name']);

        if ($user_info_row) {
            $edit_row['token'] = request_string('token',0);
            $edit_row['enterId'] = request_string('enterId',0);
            $edit_row['appToken'] = request_string('appToken',0);
            $edit_row['action_time'] = time();
            $User_InfoModel->editInfo($user_info_row['user_id'],$edit_row);
        }

        if (!$return_url) {
            $return_url = Yf_Registry::get('shop_wap_url');
            include $this->view->getView();
        } elseif ($return_url &&  "pay" == request_string('typ')) {
            $url = './index.php?ctl=Login&act=login&callback=' . urlencode($return_url);
            header('location:' . $url);
        } else {

$url = Yf_Registry::get('shop_api_url') . "/?ctl=Login&met=check&typ=e&redirect=" . urlencode($return_url) . "&us={$user_id}&ks={$encrypt_str}";
            header('location:' . $url);
        }
    }
    public function create_html($params, $action , $asyn = false) {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_URL, $action);
        //设置头文件的信息作为数据流输出
        //        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);

        if($asyn == true){
            curl_setopt ( $curl, CURLOPT_NOSIGNAL, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 300);
            curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);
        }

        //设置post数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }
    private function check_paycenter_and_shop($user_id , $key , $username){
        $post_data = [];
        //检测shop
        $url = Yf_Registry::get('shop_api_url')."?ctl=Index&met=checkApp&user_name={$username}";
        $this->create_html($post_data,$url );

        //检测shop
        $url = Yf_Registry::get('shop_api_url')."?ctl=Login&met=check&us={$user_id}&ks={$key}";
        $this->create_html($post_data,$url );

        //检测paycenter
        $url = Yf_Registry::get('paycenter_api_url')."?ctl=Login&met=check&us={$user_id}&ks={$key}";
        $this->create_html($post_data,$url , true);

    }
    public function select()
    {
        $web['site_logo'] = Web_ConfigModel::value("site_logo");//首页logo
        $BaseAppModel = new BaseApp_BaseAppModel();
        $shop_row = $BaseAppModel->getOne('102');
        $shop_url = '';
        if ($shop_row) {
            $shop_url = $shop_row['app_url'];
        }
        $type = request_int('type');
        $re_url = Yf_Registry::get('re_url');
        $from = $_REQUEST['callback'];
        $callback = $from ? $from:$re_url;
        //根据token获取用户信息
        $token = request_string('t');
        $User_BindConnectModel = new User_BindConnectModel();
        $user_info = $User_BindConnectModel->getBindConnectByToken($token);
        $user_info = current($user_info);
        fb($user_info);
        //查找注册的设置
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        $pwd_str = '';
        //判断是否开启了用户密码必须包含数字
        if ($reg_row['reg_number']['config_value']) {
            $pwd_str .= "'纯数字组合'";
        }
        //判断是否开启了用户密码必须包含小写字母
        if ($reg_row['reg_lowercase']['config_value']) {
            $pwd_str .= "'纯英文字母组合'";
        }
        //判断是否开启了用户密码必须包含大写字母
        if ($reg_row['reg_uppercase']['config_value']) {
            $pwd_str .= "'数字英文字母组合'";
        }
        //判断是否开启了用户密码必须包含符号
        if ($reg_row['reg_symbols']['config_value']) {
            $pwd_str .= "'数字英文字母及符号组合'";
        }
        if ($pwd_str) {
            $pwd_str = '密码中必须使用：' . $pwd_str;
        }
        if ($pwd_str) {
            $pwd_str .= '，';
        }
        $pwd_str .= $reg_row['reg_pwdlength']['config_value'] . '-20个字符。';
        include $this->view->getView();
    }

    //第三方注册账号绑定手机号
    public function bindmobile()
    {
        $data = [];
        $user_id = Perm::getUserByCookie();
        $mobile = request_string('mobile');
        $bind_mobile_id = sprintf('mobile_%s', $mobile);
        $User_BindConnectModel = new User_BindConnectModel();
        $User_InfoModel = new User_InfoModel();
        $user_bind_info = $User_BindConnectModel->getOneByWhere($user_id);
        $user_bind_row = $User_BindConnectModel->getOne($bind_mobile_id);
        $user_info = $User_InfoModel->getOne($user_bind_row['user_id']);
        if ($user_bind_row) {
            $bind_info = $User_BindConnectModel->getByWhere(['user_id'=>$user_bind_row['user_id']]);
        }
        if ($user_bind_info) {
            $type = substr($user_bind_info['bind_id'],0,6);
        }

        foreach ($bind_info as $k => $v) {
            if (strstr($v['bind_id'],$type) && $type == 'qq_UID') {
                $status = 250;
                $msg = __('该账号已绑定QQ，请解绑后再绑定');
                return $this->data->addBody(-140, $data, $msg, $status);
            } elseif (strstr($v['bind_id'],$type) && $type == 'weixin') {
                $status = 250;
                $msg = __('该账号已绑定微信，请解绑后再绑定');
                return $this->data->addBody(-140, $data, $msg, $status);
            }
        }

        //开启事务
        $User_BindConnectModel->sql->startTransaction();
        $data_row['user_id'] = $user_bind_row['user_id'];
        $data_row['bind_time'] = date('Y-m-d H:i:s',time());

        $user_detail_add_flag = $User_BindConnectModel -> editBindConnect($user_bind_info['bind_id'], $data_row);

        check_rs($user_detail_add_flag, $rs_row);
        if (is_ok($rs_row) && $User_BindConnectModel->sql->commit()) {
            $d = [];
            $d['user_id'] = $user_bind_row['user_id'];
            $encrypt_str = Perm::encryptUserInfo($d, $user_info['session_id']);
            $status = 200;
            $msg = __('关联成功');
            return $this->data->addBody(-140, $data, $msg, $status);
        } else {
            $User_BindConnectModel->sql->rollBack();
            $status = 250;
            $msg = __('关联失败');
            return $this->data->addBody(-140, $data, $msg, $status);
        }
    }
    //第三方注册
    public function bindRegist()
    {
        $token = request_string('token');
        $user_code = request_string('code');
        $mobile = request_string('mobile');
        $password = request_string('password');
        $server_id = 0;
        if (!$user_code) {
            $this->data->setError('请输入验证码');

            return false;
        }
        if (!$mobile) {
            $this->data->setError('请输入手机号');

            return false;
        }
        if (!$password) {
            $this->data->setError('请输入密码');

            return false;
        }
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if ($reg_row['reg_number']['config_value'] == 1) {
            if (!preg_match("/^[0-9]*$/", $password)) {
                $this->data->setError('密码必须纯数字组合');

                return false;
            }
        }
        if ($reg_row['reg_lowercase']['config_value'] == 1) {
            if (!preg_match("/^[A-Za-z]+$/", $password)) {
                $this->data->setError('密码必须纯英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_uppercase']['config_value'] == 1) {
            if (!preg_match("/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]{0,20})$/", $password)) {
                $this->data->setError('密码必须数字英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_symbols']['config_value'] == 1) {
            if (!preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、])[\da-zA-Z`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、]{0,20}$/", $password)) {
                $this->data->setError('密码必须数字英文字母及符号组合');

                return false;
            }
        }
        if (strlen($password)< $reg_row['reg_pwdlength']['config_value']) {
            $this->data->setError('密码最小长度为'.$reg_row['reg_pwdlength']['config_value'].'位');
            return false;

        }
        $config_cache = Yf_Registry::get('config_cache');
        $Cache_Lite = new Cache_Lite_Output($config_cache['verify_code']);
        $user_code_pre = $Cache_Lite->get($mobile,'verify_code');
        if ($user_code != $user_code_pre) {
            if (DEBUG !== true) {
                $user_code_pre = "";
            }
            $this->data->setError('验证码错误');

            return false;
        }
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
        //添加mobile绑定.
        //绑定标记：mobile/email/openid  绑定类型+openid
        $bind__mobile_id = sprintf('mobile_%s', $mobile);
        //查找bind绑定表
        $User_BindConnectModel = new User_BindConnectModel();
        $bind_mobile_info = $User_BindConnectModel->getOne($bind__mobile_id);
        if (!$bind_mobile_info) {
            $time = date('Y-m-d H:i:s', time());
            //插入绑定表
            $bind_array = [
                'bind_id' => $bind__mobile_id,
                'user_id' => $user_id,
                'bind_type' => $User_BindConnectModel::MOBILE,
                'bind_time' => $time
            ];
            $flag = $User_BindConnectModel->addBindConnect($bind_array);
            check_rs($flag, $rs_row);
            array_push($rs_row, $flag);
            //绑定关系
            $arr_field_user_info_detail['user_mobile_verify'] = 1;
        } else {
            //针对之前的历史数据处理。之前已经解绑的手机号在bind_connect表中还是存在，给之后用该手机号绑定用户造成了困扰
            $time = date('Y-m-d H:i:s', time());
            //插入绑定表
            $bind_array = [
                'user_id' => $user_id,
                'bind_type' => $User_BindConnectModel::MOBILE,
                'bind_time' => $time
            ];
            $flag = $User_BindConnectModel->editBindConnect($bind__mobile_id, $bind_array);
            check_rs($flag, $rs_row);
            array_push($rs_row, $flag);
            //绑定关系
            $arr_field_user_info_detail['user_mobile_verify'] = 1;
        }
        $arr_field_user_info_detail['user_name'] = $bind_info['bind_nickname'];
        $arr_field_user_info_detail['user_mobile'] = $mobile;
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
            $this->data->addBody(100, $arr_body);
        } else {
            $User_BindConnectModel->sql->rollBack();
            $this->data->setError('创建用户信息失败');
        }
    }

    //第三方关联登录
    public function bindLogin()
    {
        $token = request_string('token');
        $type = request_int('type');
        $user_name = strtolower(request_string('user_account'));
        $password = request_string('user_password');
        $User_BindConnectModel = new User_BindConnectModel();
        $rs_row = [];
        //开启事务
        $User_BindConnectModel->sql->startTransaction();
        $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
        $bind_info = current($bind_info);
        //判断绑定账户是否已经绑定过用户，已经绑定过用户的账号，不可重复绑定
        if ($bind_info['user_id']) {
            $this->data->setError('该账号已经绑定用户!');

            return false;
        }
        if (!strlen($user_name)) {
            $this->data->setError('请输入账号');

            return false;
        }
        if (!strlen($password)) {
            $this->data->setError('请输入密码');

            return false;
        }
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        //判断传过来的用户名是否是手机号，如果是手机号就查找该手机号的用户，如果不是则直接用用户名查找手机号
        if (Yf_Utils_String::isMobile($user_name)) {
            $user_name = $User_InfoDetail->getUserByMobile($user_name);
            if (!$user_name) {
                $this->data->setError('账号不存在');

                return false;
            }
        }
        $user_info_row = $User_InfoModel->getInfoByName($user_name);
        if (!$user_info_row) {
            $this->data->setError('账号不存在');

            return false;
        }
        $pswd = md5($password);
        if ($pswd != $user_info_row['password']) {
            $this->data->setError('密码错误');

            return false;
        }
        if (3 == $user_info_row['user_state']) {
            $this->data->setError('用户已经锁定,禁止登录!');

            return false;
        }
        //查找该用户是否已经绑定过其他用户
        $bind_id_row = $User_BindConnectModel->getBindConnectByuserid($user_info_row['user_id'], $type);
        if ($bind_id_row) {
            $this->data->setError('账号已绑定');

            return false;
        }
        $info_row = $User_InfoDetail->getOne($user_name);
        if (!$info_row['user_mobile']) {
            $this->data->setError('该账号未绑定手机号！');

            return false;
        }
        $session_id = $user_info_row['session_id'];
        $arr_body = $user_info_row;
        $arr_body['result'] = 1;
        $data = [];
        $data['user_id'] = $user_info_row['user_id'];
        $arr_body['user_id'] = $user_info_row['user_id'];
        $encrypt_str = Perm::encryptUserInfo($data, $session_id);
        $arr_body['k'] = $encrypt_str;
        //插入绑定表
        $time = date('Y-m-d H:i:s', time());
        $User_BindConnectModel = new User_BindConnectModel();
        $bind_array = [
            'user_id' => $user_info_row['user_id'],
            'bind_time' => $time,
            'bind_token' => $token,
        ];
        $user_bind_flag = $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
        check_rs($rs_row, $user_bind_flag);
        $arr_field_user_info_detail['user_count_login'] = $info_row['user_count_login'] + 1;
        $arr_field_user_info_detail['user_lastlogin_time'] = time();
        $user_detail_flag = $User_InfoDetail->editInfoDetail($user_name, $arr_field_user_info_detail);
        check_rs($rs_row, $user_detail_flag);
        if (is_ok($rs_row) && $User_BindConnectModel->sql->commit()) {
            $this->data->addBody(100, $arr_body);
        } else {
            $User_BindConnectModel->sql->rollBack();
            $this->data->setError('关联用户失败');
        }
    }

    public function index()
    {
        $web['site_logo'] = Web_ConfigModel::value("site_logo");//首页logo
        //新注册登录商城logo
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Index&met=getMallLogo&typ=json', $url), $formvars);
        $mall_logo = $rs['data']['mall_logo'];
        //新注册登录商城logo

        $BaseAppModel = new BaseApp_BaseAppModel();
        $shop_row = $BaseAppModel->getOne('102');
        $shop_url = '';
        if ($shop_row) {
            $shop_url = $shop_row['app_url'];
        }
        $act = request_string('act');
        //如果已经登录,则直接跳转
        if (Perm::checkUserPerm()) {
            $data = Perm::$row;
            //查找用户是否绑定了手机号。如果没有绑定的手机号就退出
            $User_InfoDetailModel = new User_InfoDetailModel();
            $user_info = $User_InfoDetailModel->getInfoDetail($data['user_name']);
            $user_info = current($user_info);
            if (!$user_info['user_mobile']) {
                $this->loginout();
            }
            $k = $_COOKIE[Perm::$cookieName];
            $u = $_COOKIE[Perm::$cookieId];
            if (isset($_REQUEST['callback']) && $_REQUEST['callback']) {
                $url = $_REQUEST['callback'] . '&us=' . $u . '&ks=' . urlencode($k);
            } else {
                $url = './index.php';
            }
            header('location:' . $url);
        } else {
            //查找注册的设置
            $Web_ConfigModel = new Web_ConfigModel();
            $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
            if (!isset($reg_row['reg_checkcode']) || !$reg_row['reg_checkcode']) {
                //config_value = 1手机，2邮箱，默认手机验证
                $reg_row['reg_checkcode'] = [
                    'config_key' => 'reg_checkcode',
                    'config_value' => 1,
                    'config_type' => 'register',
                    'config_enable' => 1,
                    'config_comment' => '',
                    'config_datatype' => 'number'
                ];
            }
            $pwd_str = '';
            if ($reg_row['reg_checkcode']['config_value'] == 2) {
                $email_display = 'display';
                $mobile_display = 'none';
                $both_display = 'none';
            } elseif ($reg_row['reg_checkcode']['config_value'] == 3) {
                $email_display = 'none';
                $mobile_display = 'display';
                $both_display = 'display';
            } else {
                $email_display = 'none';
                $mobile_display = 'display';
                $both_display = 'none';
            }
            //判断是否开启了用户密码必须包含数字
            if ($reg_row['reg_number']['config_value']) {
                $pwd_str .= "'纯数字组合'";
            }
            //判断是否开启了用户密码必须包含小写字母
            if ($reg_row['reg_lowercase']['config_value']) {
                $pwd_str .= "'纯英文字母组合'";
            }
            //判断是否开启了用户密码必须包含大写字母
            if ($reg_row['reg_uppercase']['config_value']) {
                $pwd_str .= "'数字英文字母组合'";
            }
            //判断是否开启了用户密码必须包含符号
            if ($reg_row['reg_symbols']['config_value']) {
                $pwd_str .= "'数字英文字母及符号组合'";
            }
            //密码规则提示
            $pwd_icon = $pwd_str . '组合,' . $reg_row['reg_pwdlength']['config_value'] . '-20个字符';
            if ($pwd_str) {
                $pwd_str = '密码中必须使用：' . $pwd_str;
            }
            if ($pwd_str) {
                $pwd_str .= '，';
            }
            $pwd_str .= $reg_row['reg_pwdlength']['config_value'] . '-20个字符。';
            if ($act == 'reset') {
                $this->view->setMet('resetpwd');
            }
            if ($act == 'reg') {
                $Reg_OptionModel = new Reg_OptionModel();
                $reg_opt_rows = $Reg_OptionModel->getByWhere(['reg_option_active' => 1]);

                $this->view->setMet('regist');
            }
            include $this->view->getView();
        }
    }

    public function regist()
    {
        //如果已经登录,则直接跳转
        if (Perm::checkUserPerm()) {
            $data = Perm::$row;
            $k = $_COOKIE[Perm::$cookieName];
            $u = $_COOKIE[Perm::$cookieId];
            if (isset($_REQUEST['callback'])) {
                $url = $_REQUEST['callback'] . '&us=' . $u . '&ks=' . urlencode($k);
            } else {
                $url = './index.php';
            }
            header('location:' . $url);
        } else {
            if (isset($_REQUEST['callback'])) {
                $url = './index.php?ctl=Login&act=reg&callback=' . urlencode($_REQUEST['callback']);
            } else {
                $url = './index.php?ctl=Login&act=reg';
            }
            header('location:' . $url);
        }
    }

    public function findpwd()
    {
        $url = './index.php?ctl=Login&act=reset';
        header('location:' . $url);
    }

    public function getPhonCode()
    {
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $check_code = mt_rand(100000, 999999);
        if ($mobile) {
            if($area_code==86 && !Yf_Utils_String::isMobile($mobile)){
                $msg = __('发送失败');
                $status = 250;
            }else{
                $save_result = $this->_saveCodeCache($mobile, $check_code, 'verify_code');
                if (!$save_result) {
                    $msg = _('发送失败');
                    $status = 250;
                } else {
                    //发送短消息
                    $message_model = new Message_TemplateModel();
                    $pattern = ['/\[weburl_name\]/', '/\[yzm\]/'];
                    $replacement = [Web_ConfigModel::value("site_name"), $check_code];
                    $message_info = $message_model->getTemplateInfo(['code' => 'regist_verify'], $pattern, $replacement);
                    if (!$message_info['is_phone']) {
                        $this->data->addBody(-140, [], _('信息内容创建失败'), 250);
                    }
                    $contents = $message_info['content_phone'];
                    $result = Sms::send($mobile,$area_code, $contents, $message_info['baidu_tpl_id'],['weburl_name'=>Web_ConfigModel::value("site_name"),'yzm'=>$check_code]);
                    if ($result) {
                        $msg = _('发送成功');
                        $status = 200;
                    } else {
                        $msg = _('发送失败');
                        $status = 250;
                    }
                }
            }

        } else {
            $msg = __('发送失败');
            $status = 250;
        }
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $check_code;
        }
        $data['user_code'] = $code;
        return $this->data->addBody(-140, $data, $msg, $status);
    }

    /*
     * 小程序短信验证码获取
     * */
    public function wxappregCode()
    {
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $check_code = mt_rand(100000, 999999);
        if ($mobile && Yf_Utils_String::isMobile($mobile)) {
            //判断手机号是否已经注册过
            $User_InfoDetail = new User_InfoDetailModel();
            $checkmobile = $User_InfoDetail->checkMobile($mobile);
            if ($checkmobile) {
                $msg = _('该手机号已注册');
                $status = 250;
            } else {
                $save_result = $this->_saveCodeCache($mobile, $check_code, 'verify_code');
                if (!$save_result) {
                    $msg = _('发送失败');
                    $status = 250;
                } else {
                    //发送短消息
                    $message_model = new Message_TemplateModel();
                    $pattern = ['/\[weburl_name\]/', '/\[yzm\]/'];
                    $replacement = [Web_ConfigModel::value("site_name"), $check_code];
                    $message_info = $message_model->getTemplateInfo(['code' => 'regist_verify'], $pattern, $replacement);
                    if (!$message_info['is_phone']) {
                        $this->data->addBody(-140, [], _('信息内容创建失败'), 250);
                    }
                    $contents = $message_info['content_phone'];
                    $result = Sms::send($mobile,$area_code, $contents, $message_info['baidu_tpl_id'],['weburl_name'=>Web_ConfigModel::value("site_name"),'yzm'=>$check_code]);
                    if ($result) {
                        $msg = _('发送成功');
                        $status = 200;
                    } else {
                        $msg = _('发送失败');
                        $status = 250;
                    }
                }
            }
        } else {
            $msg = __('发送失败');
            $status = 250;
        }
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $check_code;
        }

        return $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 手机获取注册码
     *
     * @access public
     */
    public function regCode()
    {
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $email = request_string('email');
        $yzm = request_string('yzm');
        if (!Perm::checkYzm($yzm)) {
            return $this->data->addBody(-140, [], _('图形验证码有误'), 230);
        }
        $check_code = mt_rand(100000, 999999);
        if ($mobile && Yf_Utils_String::isMobile($mobile)) {
            //判断手机号是否已经注册过
            $User_InfoDetail = new User_InfoDetailModel();
            $checkmobile = $User_InfoDetail->checkMobile($mobile);
            if ($checkmobile) {
                $msg = _('该手机号已注册');
                $status = 250;
            } else {
                $save_result = $this->_saveCodeCache($mobile, $check_code, 'verify_code');
                if (!$save_result) {
                    $msg = _('发送失败');
                    $status = 250;
                } else {
                    //发送短消息
                    $message_model = new Message_TemplateModel();
                    $pattern = ['/\[weburl_name\]/', '/\[yzm\]/'];
                    $replacement = [Web_ConfigModel::value("site_name"), $check_code];
                    $message_info = $message_model->getTemplateInfo(['code' => 'regist_verify'], $pattern, $replacement);
                    if (!$message_info['is_phone']) {
                        $this->data->addBody(-140, [], _('信息内容创建失败'), 250);
                    }
                    $contents = $message_info['content_phone'];
                    $result = Sms::send($mobile,$area_code, $contents, $message_info['baidu_tpl_id'],['weburl_name'=>Web_ConfigModel::value("site_name"),'yzm'=>$check_code]);
                    if ($result) {
                        $msg = __('发送成功');
                        $status = 200;
                    } else {
                        $msg = __('发送失败');
                        $status = 250;
                    }
                }
            }
        } elseif ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //判断邮箱是否已经注册过
            $User_InfoDetail = new User_InfoDetailModel();
            $checkemail = $User_InfoDetail->checkEmail($email);
            if ($checkemail) {
                $msg = _('该邮箱已注册');
                $status = 250;
            } else {
                $save_result = $this->_saveCodeCache($email, $check_code, 'verify_code');
                if (!$save_result) {
                    $msg = _('验证码获取失败');
                    $status = 250;
                } else {
                    //发送邮件
                    $message_model = new Message_TemplateModel();
                    $pattern = ['/\[weburl_name\]/', '/\[yzm\]/'];
                    $replacement = [Web_ConfigModel::value("site_name"), $message_info];
                    $message_info = $message_model->getTemplateInfo(['code' => 'regist_verify'], $pattern, $replacement);
                    if (!$message_info['is_email']) {
                        $this->data->addBody(-140, [], __('信息内容创建失败'), 250);
                    }
                    $title = '注册验证';
                    $contents = $message_info['content_email'];
                    $result = Email::send($email, '', $title, $contents);
                    if ($result) {
                        $msg = _('发送成功');
                        $status = 200;
                    } else {
                        $msg = _('发送失败');
                        $status = 250;
                    }
                }
            }
        } else {
            $msg = __('发送失败');
            $status = 250;
        }
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $check_code;
        }
        $data['user_code'] = $check_code;

        return $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 手机获取找回密码验证码
     *
     * @access public
     */
    public function findPasswdCode()
    {
        $mobile = request_string('mobile');
        $email = request_string('email');
        $yzm = request_string('yzm');
        $area_code = request_string('area_code')?:86;
        $isyzm = request_string('isyzm');
        if (!Perm::checkYzm($yzm) && !$isyzm) {
            return $this->data->addBody(-140, [], _('图形验证码有误'), 230);
        }
        $data = [];
        //验证手机号是否是用户手机号
        $User_InfoDetail = new User_InfoDetailModel();
        $checkMobile = $User_InfoDetail->isUserMobile($mobile);
        if (!$checkMobile) {
            $this->data->setError('请填写已注册或绑定的手机号');

            return false;
        }
        //判断用户是否存在  $mobile
        $pattern_email = '/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/';
        $check_code = mt_rand(100000, 999999);
        if ($mobile && Yf_Utils_String::isMobile($mobile)) {
            //缓存数据
            $save_result = $this->_saveCodeCache($mobile, $check_code, 'verify_code');
            if (!$save_result) {
                $msg = '发送失败';
                $status = 250;
            } else {
                //发送短消息
                $contents = '您的验证码是：' . $check_code . '。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';
                $result = Sms::send($mobile,$area_code, $contents);
                if ($result) {
                    $msg = '发送成功';
                    $status = 200;
                } else {
                    $msg = '发送失败';
                    $status = 250;
                }
            }
        } elseif (preg_match($pattern_email, $email)) {
            //缓存数据
            $save_result = $this->_saveCodeCache($email, $check_code, 'verify_code');
            if (!$save_result) {
                $msg = '发送失败';
                $status = 250;
            } else {
                //发送短消息
                $contents = '您的验证码是：' . $check_code . '。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';
                $result = Email::send($email, '', '找回密码验证码', $contents);
                if ($result) {
                    $msg = '发送成功';
                    $status = 200;
                } else {
                    $msg = '发送失败';
                    $status = 250;
                }
            }
        } else {
            $msg = '用户账号不存在';
            $status = 250;
        }
        if (DEBUG === false) {
            $data['user_code'] = $check_code;
        }

        $data['user_code'] = $check_code;
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function resetPasswd()
    {
//		$img_code = request_string('imgCode');
//        session_start();
//        if (strtolower($img_code) != strtolower($_SESSION['auth'])) {
//            return $this->data->addBody(-140, [], '验证码错误', 210);
//        }
        $user_code = request_string('user_code');
        $from = request_string('from');
        $data = [];
        $data['mobile'] = request_string('mobile');
        $data['email'] = request_string('email');
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if ($reg_row['reg_number']['config_value'] == 1) {
            if (!preg_match("/^[0-9]*$/", request_string('user_password'))) {
                $this->data->setError('密码必须纯数字组合');

                return false;
            }
        }
        if ($reg_row['reg_lowercase']['config_value'] == 1) {
            if (!preg_match("/^[A-Za-z]+$/", request_string('user_password'))) {
                $this->data->setError('密码必须纯英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_uppercase']['config_value'] == 1) {
            if (!preg_match("/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]{0,20})$/", request_string('user_password'))) {
                $this->data->setError('密码必须数字英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_symbols']['config_value'] == 1) {
            if (!preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、])[\da-zA-Z`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、]{0,20}$/", request_string('user_password'))) {
                $this->data->setError('密码必须数字英文字母及符号组合');

                return false;
            }
        }
        if (strlen(request_string('user_password'))< $reg_row['reg_pwdlength']['config_value']) {
            $this->data->setError('密码最小长度为'.$reg_row['reg_pwdlength']['config_value'].'位');
            return false;

        }
        $data['password'] = md5(request_string('user_password'));
        $data['passworderp'] = request_string('user_password');
        $reg_checkcode = request_string('reg_checkcode', '1');
        //为erp做的修改密码
        if ($from == 'erp' || $from == 'chain') {
            $data['user'] = request_string('user_account');
            $User_InfoModel = new User_InfoModel();
            //检测登录状态
            $user_id_row = $User_InfoModel->getInfoByName($data['user']);
            if ($user_id_row) {
                //重置密码
                $user_id = $user_id_row['user_id'];
                $reset_passwd_row = [];
                $reset_passwd_row['password'] = $from == 'erp' ? $data['passworderp']:$data['password'];
                $flag = $User_InfoModel->editInfo($user_id, $reset_passwd_row);
                if ($flag !== false) {
                    $msg = '重置密码成功';
                    $status = 200;
                } else {
                    $msg = '重置密码失败';
                    $status = 250;
                }
            } else {
                $msg = '用户不存在';
                $status = 250;
            }
            unset($data['password']);
        } else {
            if ($user_code) {
                if ($reg_checkcode == 1 || $reg_checkcode == 3) {
                    if (!$data['mobile']) {
                        $this->data->setError('手机号不能为空');

                        return false;
                    }
                } else {
                    if (!$data['email']) {
                        $this->data->setError('邮箱不能为空');

                        return false;
                    }
                }
                if (request_string('user_password')) {
                    $config_cache = Yf_Registry::get('config_cache');
                    $Cache_Lite = new Cache_Lite_Output($config_cache['verify_code']);
                    if ($reg_checkcode == 2) {
                        $user_code_pre = $Cache_Lite->get($data['email'], 'verify_code');
                    } else {
                        $user_code_pre = $Cache_Lite->get($data['mobile'], 'verify_code');
                    }
                    //$user_code_pre = $reg_checkcode == 1 ? $Cache_Lite->get($data['mobile']) : $Cache_Lite->get($data['email']);
                    if ($user_code == $user_code_pre) {
                        $User_InfoModel = new User_InfoModel();
                        $User_InfoDetailModel = new User_InfoDetailModel();
                        //检测登录状态
                        if ($reg_checkcode == 2) {
                            $data['user'] = $User_InfoDetailModel->getUserByEmail($data['email']);
                        } else {
                            $data['user'] = $User_InfoDetailModel->getUserByMobile($data['mobile']);
                        }
                        //$data['user'] = $reg_checkcode == 1 ? $User_InfoDetailModel->getUserByMobile($data['mobile']) : $User_InfoDetailModel->getUserByEmail($data['email']);
                        $user_id_row = $User_InfoModel->getInfoByName($data['user']);
                        if ($user_id_row) {
                            //重置密码
                            $user_id = $user_id_row['user_id'];
                            $reset_passwd_row = [];
                            $reset_passwd_row['password'] = $data['password'];
                            $flag = $User_InfoModel->editInfo($user_id, $reset_passwd_row);
                            if ($flag === 'false') {
                                $msg = '网路故障，请稍后重试';
                                $status = 250;
                            } else {
                                $msg = '重置密码成功';
                                $status = 200;
                                //使验证码失效
                                if ($reg_checkcode == 2) {
                                    $reg_checkcode == $Cache_Lite->remove($data['email']);
                                } else {
                                    $reg_checkcode == $Cache_Lite->remove($data['mobile']);
                                }
                                //$reg_checkcode == 1 ? $Cache_Lite->remove($data['mobile']) : $Cache_Lite->remove($data['email']);
                            }
                        } else {
                            $msg = '用户不存在';
                            $status = 250;
                        }
                    } else {
                        $msg = '验证码错误' . $Cache_Lite->get($data['email']);
                        $status = 250;
                    }
                } else {
                    $msg = '密码不能为空';
                    $status = 250;
                }
            } else {
                $msg = '手机或邮箱验证码不能为空';
                $status = 250;
            }
            unset($data['password']);
        }
        $this->data->addBody(-140, ['user_code' => $user_code, 'user_code_pre' => $user_code_pre], $msg, $status);
    }

    public function register()
    {
        //本地读取远程信息
        //只能只能只能使用分享链接注册新账号功能（闭环）
        /*$key = Yf_Registry::get('shop_api_key');;
        $url         = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars           = $_POST;
        $formvars['app_id'] = $shop_app_id;
        $fenxiao_data = get_url_with_encrypt($key, sprintf('%s?ctl=Api_%s&met=%s&typ=json', $url, 'Fenxiao', 'getStatus'), $formvars);
        if($fenxiao_data['data']['status'] == 1){
            if(empty($_COOKIE['uu_id']))
            {
                throw new Exception("请使用分享链接注册新账号");
            }
        }*/

        /** 此功能不完善，不能支持手机版和客户端，所以关掉
        $option_value_row = request_row('option');
        $Reg_OptionModel = new Reg_OptionModel();
        $reg_opt_rows = $Reg_OptionModel->getByWhere(['reg_option_active' => 1]);
        foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
        if ($reg_opt_row['reg_option_required']) {
        if ('' == $option_value_row[$reg_option_id]) {
        $this->data->setError('请输入' . $reg_opt_row['reg_option_name']);

        return false;
        }
        }
        }
         * **/
        $token = request_string('t');
        $app_id = request_int('app_id');
        $user_name = request_string('user_account', null);
        $password = request_string('user_password', null);
        $user_code = request_string('user_code');
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $email = request_string('email');
        $reg_checkcode = request_int('reg_checkcode', 1);
        $shop_id_wap = request_int('shop_id_wap');
        
        $server_id = 0;
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if (!$user_name) {
            $this->data->setError('请输入账号');

            return false;
        }
        if (!$password) {
            $this->data->setError('请输入密码');

            return false;
        }
        if ($reg_row['reg_number']['config_value'] == 1) {
            if (!preg_match("/^[0-9]*$/", $password)) {
                $this->data->setError('密码必须纯数字组合');

                return false;
            }
        }
        if ($reg_row['reg_lowercase']['config_value'] == 1) {
            if (!preg_match("/^[A-Za-z]+$/", $password)) {
                $this->data->setError('密码必须纯英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_uppercase']['config_value'] == 1) {
            if (!preg_match("/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]{0,20})$/", $password)) {
                $this->data->setError('密码必须数字英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_symbols']['config_value'] == 1) {
            if (!preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、])[\da-zA-Z`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、]{0,20}$/", $password)) {
                $this->data->setError('密码必须数字英文字母及符号组合');

                return false;
            }
        }
        if (strlen($password)< $reg_row['reg_pwdlength']['config_value']) {
            $this->data->setError('密码最小长度为'.$reg_row['reg_pwdlength']['config_value'].'位');
            return false;

        }


        if (!$user_code) {
            $this->data->setError('请输入验证码');

            return false;
        }
        if ($reg_checkcode == 1 || $reg_checkcode == 3) {
            if (!$mobile) {
                $this->data->setError('请输入手机号');

                return false;
            }
            $code_from = '手机';
        } else {
            if (!$email) {
                $this->data->setError('请输入邮箱');

                return false;
            }
            $code_from = '邮箱';
        }
        $verify_key = $reg_checkcode == 2 ? $email:$mobile;
        $verify_check = Perm::checkAppYzm($user_code, $verify_key);
        if ($verify_check) {
            $rs_row = [];
            //用户是否存在
            $User_InfoModel = new User_InfoModel();
            $User_InfoDetail = new User_InfoDetailModel();
            $user_rows = $User_InfoDetail->checkUserName($user_name);
            $user_name_info = $User_InfoModel->getInfoByName($user_name);
            if ($user_rows || $user_name_info) {
                $this->data->setError('用户已经存在,请更换用户名!');

                return false;
            } else {
                $User_InfoModel->sql->startTransaction();
                $Db = Yf_Db::get('ucenter');
                $seq_name = 'user_id';
                $user_id = $Db->nextId($seq_name);
//				$User_InfoModel->check_input($user_name, $password, $user_mobile);
                $now_time = time();
                $ip = get_ip();
                $session_id = uniqid();
                $arr_field_user_info = [];
                $arr_field_user_info['user_id'] = $user_id;
                $arr_field_user_info['user_name'] = $user_name;
                $arr_field_user_info['password'] = md5($password);
                $arr_field_user_info['action_time'] = $now_time;
                $arr_field_user_info['action_ip'] = $ip;
                $arr_field_user_info['session_id'] = $session_id;
                $flag = $User_InfoModel->addInfo($arr_field_user_info);
                array_push($rs_row, $flag);
                $arr_field_user_info_detail = [];
                //添加mobile绑定.
                //绑定标记：mobile/email/openid  绑定类型+openid
                $bind_id = $reg_checkcode == 1 ? sprintf('mobile_%s', $mobile):sprintf('email_%s', $email);
                //查找bind绑定表
                $User_BindConnectModel = new User_BindConnectModel();
                $bind_info = $User_BindConnectModel->getOne($bind_id);
                if (!$bind_info) {
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'bind_id' => $bind_id,
                        'user_id' => $user_id,
                        'bind_type' => $reg_checkcode == 1 ? $User_BindConnectModel::MOBILE:$User_BindConnectModel::EMAIL,
                        'bind_time' => $time
                    ];
                    $flag = $User_BindConnectModel->addBindConnect($bind_array);
                    array_push($rs_row, $flag);
                    //绑定关系
                    if ($reg_checkcode == 1) {
                        $arr_field_user_info_detail['user_mobile_verify'] = 1;
                    } else {
                        $arr_field_user_info_detail['user_email_verify'] = 1;
                    }
                }
                $arr_field_user_info_detail['user_name'] = $user_name;
                if ($reg_checkcode == 1) {
                    $arr_field_user_info_detail['user_mobile'] = $mobile;
                    $arr_field_user_info_detail['area_code'] = $area_code;
                } else {
                    $arr_field_user_info_detail['user_email'] = $email;
                }
                //$arr_field_user_info_detail['user_mobile_verify']         = 1;
                $arr_field_user_info_detail['user_reg_time'] = $now_time;
                $arr_field_user_info_detail['user_count_login'] = 1;
                $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
                $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
                $arr_field_user_info_detail['user_reg_ip'] = $ip;
                $arr_field_user_info_detail['user_is_shop'] = $shop_id_wap;
                $arr_field_user_info_detail['user_avatar'] = Web_ConfigModel::value('user_default_avatar', Yf_Registry::get('static_url') . '/images/default_user_portrait.png');
                $flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
                array_push($rs_row, $flag);
                $User_OptionModel = new User_OptionModel();
                foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
                    if (isset($option_value_row[$reg_option_id])) {
                        $reg_option_value_row = explode(',', $reg_opt_row['reg_option_value']);
                        $user_option_row = [];
                        $user_option_row['reg_option_id'] = $reg_option_id;
                        $user_option_row['reg_option_value_id'] = $option_value_row[$reg_option_id];
                        $user_option_row['user_id'] = $user_id;
                        $user_option_row['user_option_value'] = isset($reg_option_value_row[$option_value_row[$reg_option_id]]) ? $reg_option_value_row[$option_value_row[$reg_option_id]]:$option_value_row[$reg_option_id];
                        $flag = $User_OptionModel->addOption($user_option_row);
                        array_push($rs_row, $flag);
                    }
                }
            }
            if (is_ok($rs_row) && $User_InfoDetail->sql->commit()) {
                $d = [];
                $d['user_id'] = $user_id;
                $encrypt_str = Perm::encryptUserInfo($d, $session_id);
                $arr_body = [
                    "user_name" => $user_name,
                    "server_id" => $server_id,
                    "k" => $encrypt_str,
                    "user_id" => $user_id
                ];
                if ($token) {
                    //查找bind绑定表
                    $User_BindConnectModel = new User_BindConnectModel();
                    $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
                    $bind_info = $bind_info[0];
                    //获取qq缩略头像
                    $qq_logo = substr($bind_info['bind_avator'], 0, strrpos($bind_info['bind_avator'], '/'));
                    $qq_logo = $qq_logo . '/40';
                    //更新用户详情表
                    if ($bind_info['bind_gender'] == 1) {
                        $gender = 1;
                    } else {
                        $gender = 0;
                    }
                    $user_info_detail = [
                        'nickname' => $bind_info['bind_nickname'],
                        'user_avatar' => $bind_info['bind_avator'],
                        'user_gender' => $gender,
                        'user_avatar_thumb' => $qq_logo,
                    ];
                    $User_InfoDetail->editInfoDetail($user_name, $user_info_detail);
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'user_id' => $user_id,
                        'bind_time' => $time,
                        'bind_token' => $token,
                    ];
                    $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
                }

                return $this->data->addBody(100, $arr_body);
            } else {
                $User_InfoDetail->sql->rollBack();
                $this->data->setError('创建用户信息失败');
            }
        } else {
            $msg = $code_from . '验证码错误';
            $status = 250;
            if (DEBUG !== true) {
                $user_code = "";
            }

            return $this->data->addBody(-1, ['code' => $user_code,'debug'=>DEBUG], $msg, $status);
        }
    }

    public function userRegist()
    {
        $option_value_row = request_row('option');
//        $Reg_OptionModel = new Reg_OptionModel();
//        $reg_opt_rows = $Reg_OptionModel->getByWhere(array('reg_option_active'=>1));
//        foreach ($reg_opt_rows as $reg_option_id=>$reg_opt_row)
//        {
//            if ($reg_opt_row['reg_option_required'])
//            {
//                if ('' == $option_value_row[$reg_option_id])
//                {
//                    $this->data->setError('请输入' . $reg_opt_row['reg_option_name']);
//                    return false;
//                }
//            }
//        }
        $token = request_string('t');
        $app_id = request_int('app_id');
        $user_name = request_string('user_account', null);
        $password = request_string('user_password', null);
        $user_code = request_string('user_code');
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $email = request_string('email');
        $reg_checkcode = request_int('reg_checkcode', 1);
        $server_id = 0;
        if (!$user_name) {
            $this->data->setError('请输入账号');

            return false;
        }
        if (!$password) {
            $this->data->setError('请输入密码');

            return false;
        }
        if (!$user_code) {
            $this->data->setError('请输入验证码');

            return false;
        }
        if ($reg_checkcode == 1 || $reg_checkcode == 3) {
            if (!$mobile) {
                $this->data->setError('请输入手机号');

                return false;
            }
            $code_from = '手机';
        } else {
            if (!$email) {
                $this->data->setError('请输入邮箱');

                return false;
            }
            $code_from = '邮箱';
        }
        $verify_key = $reg_checkcode == 2 ? $email:$mobile;
        $verify_check = Perm::checkAppYzm($user_code, $verify_key);
        if ($verify_check) {
            $rs_row = [];
            //用户是否存在
            $User_InfoModel = new User_InfoModel();
            $User_InfoDetail = new User_InfoDetailModel();
            $user_rows = $User_InfoDetail->checkUserName($user_name);
            $user_name_info = $User_InfoModel->getInfoByName($user_name);
            if ($user_rows || $user_name_info) {
                $this->data->setError('用户已经存在,请更换用户名!');

                return false;
            } else {
                $User_InfoModel->sql->startTransaction();
                $Db = Yf_Db::get('ucenter');
                $seq_name = 'user_id';
                $user_id = $Db->nextId($seq_name);
//				$User_InfoModel->check_input($user_name, $password, $user_mobile);
                $now_time = time();
                $ip = get_ip();
                $session_id = uniqid();
                $arr_field_user_info = [];
                $arr_field_user_info['user_id'] = $user_id;
                $arr_field_user_info['user_name'] = $user_name;
                $arr_field_user_info['password'] = md5($password);
                $arr_field_user_info['action_time'] = $now_time;
                $arr_field_user_info['action_ip'] = $ip;
                $arr_field_user_info['session_id'] = $session_id;
                $flag = $User_InfoModel->addInfo($arr_field_user_info);
                array_push($rs_row, $flag);
                $arr_field_user_info_detail = [];
                //添加mobile绑定.
                //绑定标记：mobile/email/openid  绑定类型+openid
                $bind_id = $reg_checkcode == 1 ? sprintf('mobile_%s', $mobile):sprintf('email_%s', $email);
                //查找bind绑定表
                $User_BindConnectModel = new User_BindConnectModel();
                $bind_info = $User_BindConnectModel->getOne($bind_id);
                if (!$bind_info) {
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'bind_id' => $bind_id,
                        'user_id' => $user_id,
                        'bind_type' => $reg_checkcode == 1 ? $User_BindConnectModel::MOBILE:$User_BindConnectModel::EMAIL,
                        'bind_time' => $time
                    ];
                    $flag = $User_BindConnectModel->addBindConnect($bind_array);
                    array_push($rs_row, $flag);
                    //绑定关系
                    if ($reg_checkcode == 1) {
                        $arr_field_user_info_detail['user_mobile_verify'] = 1;
                    } else {
                        $arr_field_user_info_detail['user_email_verify'] = 1;
                    }
                }
                $arr_field_user_info_detail['user_name'] = $user_name;
                if ($reg_checkcode == 1) {
                    $arr_field_user_info_detail['user_mobile'] = $mobile;
                    $arr_field_user_info_detail['area_code'] = $area_code;
                } else {
                    $arr_field_user_info_detail['user_email'] = $email;
                }
                //$arr_field_user_info_detail['user_mobile_verify']         = 1;
                $arr_field_user_info_detail['user_reg_time'] = $now_time;
                $arr_field_user_info_detail['user_count_login'] = 1;
                $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
                $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
                $arr_field_user_info_detail['user_reg_ip'] = $ip;
                $arr_field_user_info_detail['user_avatar'] = Web_ConfigModel::value('user_default_avatar', Yf_Registry::get('static_url') . '/images/default_user_portrait.png');
                $flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
                array_push($rs_row, $flag);
                $User_OptionModel = new User_OptionModel();
                foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
                    if (isset($option_value_row[$reg_option_id])) {
                        $reg_option_value_row = explode(',', $reg_opt_row['reg_option_value']);
                        $user_option_row = [];
                        $user_option_row['reg_option_id'] = $reg_option_id;
                        $user_option_row['reg_option_value_id'] = $option_value_row[$reg_option_id];
                        $user_option_row['user_id'] = $user_id;
                        $user_option_row['user_option_value'] = isset($reg_option_value_row[$option_value_row[$reg_option_id]]) ? $reg_option_value_row[$option_value_row[$reg_option_id]]:$option_value_row[$reg_option_id];
                        $flag = $User_OptionModel->addOption($user_option_row);
                        array_push($rs_row, $flag);
                    }
                }
            }
            if (is_ok($rs_row) && $User_InfoDetail->sql->commit()) {
                $d = [];
                $d['user_id'] = $user_id;
                $encrypt_str = Perm::encryptUserInfo($d, $session_id);
                $arr_body = [
                    "user_name" => $user_name,
                    "server_id" => $server_id,
                    "k" => $encrypt_str,
                    "user_id" => $user_id
                ];
                if ($token) {
                    //查找bind绑定表
                    $User_BindConnectModel = new User_BindConnectModel();
                    $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
                    $bind_info = $bind_info[0];
                    //获取qq缩略头像
                    $qq_logo = substr($bind_info['bind_avator'], 0, strrpos($bind_info['bind_avator'], '/'));
                    $qq_logo = $qq_logo . '/40';
                    //更新用户详情表
                    if ($bind_info['bind_gender'] == 1) {
                        $gender = 1;
                    } else {
                        $gender = 0;
                    }
                    $user_info_detail = [
                        'nickname' => $bind_info['bind_nickname'],
                        'user_avatar' => $bind_info['bind_avator'],
                        'user_gender' => $gender,
                        'user_avatar_thumb' => $qq_logo,
                    ];
                    $User_InfoDetail->editInfoDetail($user_name, $user_info_detail);
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'user_id' => $user_id,
                        'bind_time' => $time,
                        'bind_token' => $token,
                    ];
                    $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
                }

                return $this->data->addBody(100, $arr_body);
            } else {
                $User_InfoDetail->sql->rollBack();
                $this->data->setError('创建用户信息失败');
            }
        } else {
            $msg = $code_from . '验证码错误';
            $status = 250;
            if (DEBUG !== true) {
                $user_code = "";
            }

            return $this->data->addBody(-1, ['code' => $user_code], $msg, $status);
        }
    }

    public function loginex()
    {
        $token = request_string('t');
        fb($token);
        $user_name = strtolower($_REQUEST['user_account']);
        if (!$user_name) {
            $user_name = strtolower($_REQUEST['user_name']);
        }
        $password = $_REQUEST['user_password'];
        $md5_password = $_REQUEST['md5_password'];
        if (!$password) {
            $password = $_REQUEST['password'];
        }
        if (!strlen($user_name)) {
            $this->data->setError('请输入账号');

            return false;
        }
        if (!strlen($password) && !strlen($md5_password)) {
            $this->data->setError('请输入密码');
        } else {
            $User_InfoModel = new User_InfoModel();
            $User_InfoDetail = new User_InfoDetailModel();
            $user_info_row = $User_InfoModel->getInfoByName($user_name);
            if (!$user_info_row) {
                $this->data->setError('账号不存在');

                return false;
            }
            if ($password) {
                $pswd = md5($password);
            }
            if ($md5_password) {
                $pswd = $md5_password;
            }
            if ($pswd != $user_info_row['password']) {
                $this->data->setError('密码错误');
            } else {
                //$session_id = uniqid();
                $session_id = $user_info_row['session_id'];
                $arr_field = [];
                $arr_field['session_id'] = $session_id;
                //if ($User_InfoModel->editInfo($user_info_row['user_id'], $arr_field) > 0)
                if (true) {
                    //$arr_body = array("result"=>1,"user_name"=>$user_info_row['user_name'],"session_id"=>$session_id);
                    $arr_body = $user_info_row;
                    $arr_body['result'] = 1;
                    //$arr_body['session_id'] = $session_id;
                    $data = [];
                    $data['user_id'] = $user_info_row['user_id'];
                    //$data['session_id'] = $session_id;
                    $encrypt_str = Perm::encryptUserInfo($data, $session_id);
                    $arr_body['k'] = $encrypt_str;
                    //插入绑定表
                    if ($token) {
                        //查找bind绑定表
                        $User_BindConnectModel = new User_BindConnectModel();
                        $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
                        $bind_info = $bind_info[0];
                        //插入绑定表
                        $time = date('Y-m-d H:i:s', time());
                        $User_BindConnectModel = new User_BindConnectModel();
                        $bind_array = [
                            'user_id' => $user_info_row['user_id'],
                            'bind_time' => $time,
                            'bind_token' => $token,
                        ];
                        $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
                    }
                    $this->data->addBody(100, $arr_body);
                } else {
                    $this->data->setError('登录失败');
                }
            }
        }
    }

    /**
     * 给IOS自动登录使用
     * 调用URL /index.php?ctl=Login&met=login_wkwebview&typ=json&token=XXX
     *
     * @return  [type]
     * @weichat sunkangchina
     * @date    2017-12-05
     */
    public function login_wkwebview()
    {
        $cr = new ECrypt();
        $token = $_GET['token'];
        $msg = "error";
        $flag = true;
        if (!$token) {
            $flag = false;
            goto E1;
        }
        $token = urldecode($token);
        $arr = $cr->decode($token);
        $user_id = $arr['user_id'];
        $time = $arr['time'];
        if (!$time || !$user_id) {
            $flag = false;
            $msg = "no user_infomation params";
            goto E1;
        }
        $User_InfoModel = new User_InfoModel();
        $user_info_row = $User_InfoModel->getOne($user_id);
        //print_r($user_info_row['user_name']);
        if (!$user_info_row) {
            $flag = false;
            $msg = "not find";
            goto E1;
        }
        $session_id = $user_info_row['session_id'];
        $data = $arr_body = [];
        $data['user_id'] = $user_info_row['user_id'];
        $encrypt_str = Perm::encryptUserInfo($data, $session_id);
        $arr_body['k'] = $encrypt_str;
        $this->data->addBody(100, $arr_body);
        E1:
        if ($flag == false) {
            $this->data->addBody(-1, [], $msg, 250);
        }
        if ($jsonp_callback = request_string('jsonp_callback')) {
            exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
        }
    }

    public function login()
    {
        $token = request_string('t');
        $type = request_int('type');
        //如果密码错误三次及以上开启图形验证码
        /*if($is_mobile!=1){
             if ($_COOKIE['passwordErrorCount'] >= 3 || isset($_REQUEST['imgCode'])) {
                session_start();
                if (!request_string('imgCode') || strtolower(request_string('imgCode')) !== strtolower($_SESSION["auth"])) {
                    return $this->data->setError('验证码错误');
                }
            }
        }*/
		if($is_mobile!=1){
			 if (isset($_REQUEST['imgCode'])) {
				session_start();
				$this->data->clear_session_code = "auth";
				if (!request_string('imgCode') || strtolower(request_string('imgCode')) !== strtolower($_SESSION["auth"])) {
					return $this->data->setError('验证码错误');
				}
			}
		}
        $user_name = strtolower(request_string('user_account'));
        //查找bind绑定表
        $User_BindConnectModel = new User_BindConnectModel();
        if (!$user_name) {
            $user_name = strtolower(request_string('user_name'));
        }
        $password = $_REQUEST['user_password'];
        $md5_password = request_string('md5_password');
        if (!$password) {
            $password = request_int('password');
        }
        if (!strlen($user_name)) {
            $this->data->setError('请输入账号');

            return false;
        }
        if (!strlen($password) && !strlen($md5_password)) {
            $this->data->setError('请输入密码');
        } else {
			//验证是否触发当天禁登(当天登录失败记录条数>=6时，禁止当天登录)
			/*$beginToday = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$endToday = mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			$sql = "select count(1)cts from ucenter_login_limit where 1=1 and account='{$user_name}' and type=1 and (login_time >={$beginToday } and login_time<={$endToday})";
			$err_ct = $User_BindConnectModel->sql->getRow($sql)['cts'];
			if($err_ct>=6){
				return  $this->data->setError('登录失败次数已达6次，今天已被禁止登录！');
			}*/
            $User_InfoModel = new User_InfoModel();
            $User_InfoDetail = new User_InfoDetailModel();
            $bind_id = '';
            $user_info_row = [];
            //添加mobile绑定.
            //绑定标记：mobile/email/openid  绑定类型+openid
            
            {
                if (filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
                    //邮件登录
                    $bind_id = sprintf('email_%s', $user_name);
                } elseif (Yf_Utils_String::isMobile($user_name)) {
                    //手机号登录
                    $bind_id = sprintf('mobile_%s', $user_name);
                }
                if ($bind_id) {
                    //查找bind绑定表
                    $User_BindConnectModel = new User_BindConnectModel();
                    $bind_info = $User_BindConnectModel->getOne($bind_id);
                    if ($bind_info) {
                        //用户名登录
                        $user_info_row = $User_InfoModel->getOne($bind_info['user_id']);
                    }
                }
                if ($user_info_row) {
                } else {
                    //用户名登录
                    $user_info_row = $User_InfoModel->getInfoByName($user_name);
                }
            }
            if (!$user_info_row) {
                $this->err_login_add($User_InfoModel ,$user_name);
                $this->data->setError('账号或密码错误！');
                return false;
            }
			//防止后台管理员登录
			$key = Yf_Registry::get('shop_api_key');
			$url = Yf_Registry::get('shop_api_url');
			$shop_app_id = Yf_Registry::get('shop_app_id');
			$formvars = [];
			$formvars['app_id'] = $shop_app_id;
			$formvars['user_name'] = $user_name;
			$result = get_url_with_encrypt($key, sprintf('%s?ctl=Login&met=check_admin&typ=json', $url), $formvars);
			if ($result['status']==200) {
				$this->data->setError('账号或密码错误！！');
				return false;
			}
			
            if ($password) {
                $pswd = md5($password);
            }
            if ($md5_password) {
                $pswd = $md5_password;
            }
            if ($pswd != $user_info_row['password']) {
				$this->err_login_add($User_InfoModel ,$user_name);
                $this->data->setError('账号或密码错误！');
            } else {
                if (3 == $user_info_row['user_state']) {
                    $this->data->setError('用户已经锁定,禁止登录!');

                    return false;
                }
                //$session_id = uniqid();
                $session_id = $user_info_row['session_id'];
                $arr_field = [];
                $arr_field['session_id'] = $session_id;
                //if ($User_InfoModel->editInfo($user_info_row['user_id'], $arr_field) > 0)
                if ($user_info_row['user_id'] != 0 && $token) {
                    $bind_id_row = $User_BindConnectModel->getBindConnectByuserid($user_info_row['user_id'], $type);
                    if ($bind_id_row) {
                        $this->data->setError('账号已绑定');

                        return false;
                    }
                }
                $info_row = $User_InfoDetail->getOne($user_info_row['user_name']);
                if (!$info_row) {
                    $this->data->setError('账号信息有误', $user_info_row, 210);

                    return false;
                }
                //判断是否为有效门店账号
                if ($user_info_row && $user_info_row['user_id'] != 0) {
                    $key = Yf_Registry::get('shop_api_key');
                    $url = Yf_Registry::get('shop_api_url');
                    $shop_app_id = Yf_Registry::get('shop_app_id');
                    $formvars = [];
                    $formvars['app_id'] = $shop_app_id;
                    $formvars['user_id'] = $user_info_row['user_id'];
                    $chain_flag = get_url_with_encrypt($key, sprintf('%s?ctl=Login&met=check_store&typ=json', $url), $formvars);
                    if ($chain_flag['data']['flag']) {
                        $this->data->setError('该门店账号已关闭', $chain_flag, 250);

                        return false;
                    }
                }
                // if ($info_row['user_no_mobile'] && isset($info_row['user_no_mobile'])) {
                //     $info_row['user_no_mobile'] = $info_row['user_no_mobile'];
                // } else {
                //     $info_row['user_no_mobile'] = 1;
                // }
                // if (!$info_row['user_mobile'] && $info_row['user_no_mobile'] != 0) {
                //     $this -> data -> setError('登录失败', $info_row, 210);
                //     return false;
                // } else {
                $arr_body = $user_info_row;
                $arr_body['mobile'] = $info_row['user_mobile'];
                $arr_body['area_code'] = $info_row['area_code'];
                $arr_body['result'] = 1;
                //$arr_body['session_id'] = $session_id;
                $data = [];
                $data['user_id'] = $user_info_row['user_id'];
                //$data['session_id'] = $session_id;
                $encrypt_str = Perm::encryptUserInfo($data, $session_id);
                $arr_body['k'] = $encrypt_str;
                //插入绑定表
                if ($token) {
                    $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
                    $bind_info = $bind_info[0];
                    //插入绑定表
                    $time = date('Y-m-d H:i:s', time());
                    $User_BindConnectModel = new User_BindConnectModel();
                    $bind_array = [
                        'user_id' => $user_info_row['user_id'],
                        'bind_time' => $time,
                        'bind_token' => $token,
                    ];
                    $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
                }
                $arr_field_user_info_detail['user_count_login'] = $info_row['user_count_login'] + 1;
                $arr_field_user_info_detail['user_lastlogin_time'] = time();
                $User_InfoDetail->editInfoDetail($user_name, $arr_field_user_info_detail);

                $this->data->addBody(100, $arr_body);
                // }
            }
        }
        if ($jsonp_callback = request_string('jsonp_callback')) {
            exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
        }
    }

    //手机验证登陆
    public function phoneLogin()
    {
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $bind_id = '';
        $user_info_row = [];
        $token = request_string('t');
        $type = request_int('type');
        $mobile = request_string('user_phone');
        $phone_code = request_string('phone_code');
        $bind_id = sprintf('mobile_%s', $mobile);

        if (!$mobile) {
            $this->data->setError('手机号不能为空');
            return false;
        }

        if (!$phone_code) {
            $this->data->setError('手机验证码不能为空');
            return false;
        }

        if (!VerifyCode::checkCode($mobile, $phone_code)) {
            $data = [];
            return $this->data->addBody(-140, $data, __('手机验证码错误'), 250);
        }

        if ($bind_id) {
            //查找bind绑定表
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_info = $User_BindConnectModel->getOne($bind_id);

            if ($bind_info) {
                //用户名登录
                $user_info_row = $User_InfoModel->getOne($bind_info['user_id']);
            }
        }

        if (!$user_info_row) {
            $this->data->setError('手机号未绑定账号');
            return false;
        }

        if (3 == $user_info_row['user_state']) {
            $this->data->setError('用户已经锁定,禁止登录!');
            return false;
        }

        $session_id = $user_info_row['session_id'];
        $arr_field = [];
        $arr_field['session_id'] = $session_id;

        $info_row = $User_InfoDetail->getOne($user_info_row['user_name']);
        if (!$info_row) {
            $this->data->setError('账号信息有误', $user_info_row, 210);
            return false;
        }
        //判断是否为有效门店账号
        if ($user_info_row && $user_info_row['user_id'] != 0) {
            $key = Yf_Registry::get('shop_api_key');
            $url = Yf_Registry::get('shop_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');
            $formvars = [];
            $formvars['app_id'] = $shop_app_id;
            $formvars['user_id'] = $user_info_row['user_id'];
            $chain_flag = get_url_with_encrypt($key, sprintf('%s?ctl=Login&met=check_store&typ=json', $url), $formvars);
            if ($chain_flag['data']['flag']) {
                $this->data->setError('该门店账号已关闭', $chain_flag, 250);
                return false;
            }
        }

        $arr_body = $user_info_row;
        $arr_body['mobile'] = $info_row['user_mobile'];
        $arr_body['area_code'] = $info_row['area_code'];
        $arr_body['result'] = 1;
        //$arr_body['session_id'] = $session_id;
        $data = [];
        $data['user_id'] = $user_info_row['user_id'];
        //$data['session_id'] = $session_id;
        $encrypt_str = Perm::encryptUserInfo($data, $session_id);
        $arr_body['k'] = $encrypt_str;
        //插入绑定表
        if ($token) {
            $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
            $bind_info = $bind_info[0];
            //插入绑定表
            $time = date('Y-m-d H:i:s', time());
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_array = [
                'user_id' => $user_info_row['user_id'],
                'bind_time' => $time,
                'bind_token' => $token,
            ];
            $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
        }
        $arr_field_user_info_detail['user_count_login'] = $info_row['user_count_login'] + 1;
        $arr_field_user_info_detail['user_lastlogin_time'] = time();
        $User_InfoDetail->editInfoDetail($user_name, $arr_field_user_info_detail);

        $this->data->addBody(100, $arr_body);

        if ($jsonp_callback = request_string('jsonp_callback')) {
            exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
        }

    }

    //shop端手机验证登陆
    public function shopPhoneLogin()
    {
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $bind_id = '';
        $user_info_row = [];
        $mobile = request_string('user_phone');
        $phone_code = request_string('phone_code');
        $bind_id = sprintf('mobile_%s', $mobile);

        if ($bind_id) {
            //查找bind绑定表
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_info = $User_BindConnectModel->getOne($bind_id);
            if ($bind_info) {
                //用户名登录
                $user_info_row = $User_InfoModel->getOne($bind_info['user_id']);
            }
        }

        if (!$user_info_row) {
            $this->data->setError('手机号未绑定账号');
            return false;
        }

        if (3 == $user_info_row['user_state']) {
            $this->data->setError('用户已经锁定,禁止登录!');
            return false;
        }

        $session_id = $user_info_row['session_id'];
        $arr_field = [];
        $arr_field['session_id'] = $session_id;

        $info_row = $User_InfoDetail->getOne($user_info_row['user_name']);
        if (!$info_row) {
            $this->data->setError('账号信息有误', $user_info_row, 210);
            return false;
        }
        //判断是否为有效门店账号
        if ($user_info_row && $user_info_row['user_id'] != 0) {
            $key = Yf_Registry::get('shop_api_key');
            $url = Yf_Registry::get('shop_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');
            $formvars = [];
            $formvars['app_id'] = $shop_app_id;
            $formvars['user_id'] = $user_info_row['user_id'];
            $chain_flag = get_url_with_encrypt($key, sprintf('%s?ctl=Login&met=check_store&typ=json', $url), $formvars);
            if ($chain_flag['data']['flag']) {
                $this->data->setError('该门店账号已关闭', $chain_flag, 250);
                return false;
            }
        }

        $arr_body = $user_info_row;
        $arr_body['mobile'] = $info_row['user_mobile'];
        $arr_body['area_code'] = $info_row['area_code'];
        $arr_body['result'] = 1;
        $data = [];
        $data['user_id'] = $user_info_row['user_id'];
        $encrypt_str = Perm::encryptUserInfo($data, $session_id);
        $arr_body['k'] = $encrypt_str;

        $this->data->addBody(100, $arr_body);

        if ($jsonp_callback = request_string('jsonp_callback')) {
            exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
        }

    }

    /**
     ** @desc 封装 curl 的调用接口，post的请求方式
     **/
    function doCurlPostRequest($url, $requestString, $timeout = 5)
    {
        if ($url == '' || $requestString == '' || $timeout <= 0) {
            return false;
        }
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, $requestString);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);

        return curl_exec($con);
    }

    /*
     * 用户退出
     *
     *
     */
    public function loginout()
    {
        if (isset($_COOKIE['key']) || isset($_COOKIE['id'])) {
            setcookie("key", null, time() - 3600 * 24 * 365);
            setcookie("id", null, time() - 3600 * 24 * 365);
        }
        //如果已经登录,则直接跳转
        if (isset($_REQUEST['callback'])) {
            $url = urldecode($_REQUEST['callback']);
        } else {
            $url = Yf_Registry::get('url');
        }
        if ('e' == $this->typ) {
            header('location:' . $url);
        } else {
            $this->data->addBody(-1, []);
            if ($jsonp_callback = request_string('jsonp_callback')) {
                exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
            }
        }
    }

    public function logout()
    {
        $this->loginout();
    }

    /*
     * 检测用户登录
     */
    public function checkLogin()
    {
        if (Perm::checkUserPerm()) {
            $msg = '数据正确';
            $status = 200;
            $data = Perm::$row;
            //user detail
            $User_InfoDetailModel = new User_InfoDetailModel();
            $data_info = $User_InfoDetailModel->getOne($data['user_name']);
            $data = array_merge($data, $data_info);
            if (!$data['user_avatar']) {
                $data['user_avatar'] = Web_ConfigModel::value('user_default_avatar', Yf_Registry::get('static_url') . '/images/default_user_portrait.png');
            }
            $data['k'] = $_COOKIE['key'];
            $data['u'] = $_COOKIE['id'];
            //unset($data['session_id']);
        } else {
            $msg = '权限错误';
            $status = 250;
            $data = [];
            $data['k'] = $_COOKIE['key'];
            $data['u'] = $_COOKIE['id'];
        }

        $this->data->addBody(100, $data, $msg, $status);
    }

    /**
     *  检查用户名是否存在
     *  检查user_name, user_tel,user_mobile,user_emial
     */
    public function checkUserName()
    {
        $user_name = request_string('user_name');
        $cond_row = [];
        $cond_row['user_name'] = $user_name;
        $User_Info = new User_Info();
        $flag = $User_Info->getByWhere($cond_row);
        $data = [];
        if ($flag) {
            $msg = '该用户名已存在！';
            $status = 200;
        } else {
            $msg = '未查询到该用户名';
            $status = 250;
        }
        $this->data->addBody(-1, $data, $msg, $status);
    }

    public function checkMobile()
    {
        $mobile = request_string('mobile');
        //判断手机号是否已经注册过
        $User_InfoDetail = new User_InfoDetail();
        $checkmobile = $User_InfoDetail->checkUserName($mobile, Perm::$userId);
        $data = [];
        if (count($checkmobile)) {
            $msg = 'failure';
            $status = 250;
        } else {
            $msg = 'success';
            $status = 200;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function checkStatus()
    {
        $data = [];
        //如果已经登录,则直接跳转
        if (Perm::checkUserPerm()) {
            $data = Perm::$row;
            $k = $_COOKIE[Perm::$cookieName];
            $u = $_COOKIE[Perm::$cookieId];
            $data['ks'] = $k;
            $data['us'] = $u;
            $msg = '已登录';
            $status = 200;
        } else {
            $data = Perm::$row;
            $msg = '未登录';
            $status = 250;
        }
        $this->data->addBody(-1, $data, $msg, $status);
        if ($jsonp_callback = request_string('jsonp_callback')) {
            exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
        }
    }

    //门店帐号注册
    public function registerChain()
    {
        $user_name = request_string('user_account', null);
        $password = request_string('user_password', null);
        $mobile = request_string('user_mobile', null);
        $server_id = 0;
        if (!$user_name) {
            return $this->data->addBody(-1, [], '请输入账号', 250);
        }
        if (!$password) {
            return $this->data->addBody(-1, [], '请输入密码', 250);
        }
        $rs_row = [];
        //用户是否存在
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $user_rows = $User_InfoModel->getInfoByName($user_name);
        if ($user_rows) {
            $arr_body = [
                "user_name" => $user_name,
                "user_id" => $user_rows['user_id']
            ];

            return $this->data->addBody(-1, $arr_body, '用户名已经存在', 250);
        } else {
            $User_InfoModel->sql->startTransaction();
            $Db = Yf_Db::get('ucenter');
            $seq_name = 'user_id';
            $user_id = $Db->nextId($seq_name);
            //$User_InfoModel->check_input($user_name, $password, $user_mobile);
            $now_time = time();
            $ip = get_ip();
            $session_id = uniqid();
            $arr_field_user_info = [];
            $arr_field_user_info['user_id'] = $user_id;
            $arr_field_user_info['user_name'] = $user_name;
            $arr_field_user_info['password'] = md5($password);
            $arr_field_user_info['action_time'] = $now_time;
            $arr_field_user_info['action_ip'] = $ip;
            $arr_field_user_info['session_id'] = $session_id;
            $flag = $User_InfoModel->addInfo($arr_field_user_info);
            array_push($rs_row, $flag);
            $arr_field_user_info_detail = [];
            $arr_field_user_info_detail['user_name'] = $user_name;
            $arr_field_user_info_detail['user_mobile'] = $mobile;
            //$arr_field_user_info_detail['user_mobile_verify']         = 1;
            $arr_field_user_info_detail['user_reg_time'] = $now_time;
            $arr_field_user_info_detail['user_count_login'] = 1;
            $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
            $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
            $arr_field_user_info_detail['user_reg_ip'] = $ip;
            $flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
            array_push($rs_row, $flag);
        }
        $app_id = isset($_REQUEST['app_id']) ? $_REQUEST['app_id']:0;
        $Base_App = new Base_AppModel();
        if ($app_id && !($base_app_rows = $Base_App->getApp($app_id))) {
            /*
            $base_app_row = array_pop($base_app_rows);

            $arr_field_user_app = array();
            $arr_field_user_app['user_name'] = $user_name;
            $arr_field_user_app['app_id'] = $app_id;
            $arr_field_user_app['active_time'] = time();

            $User_App = new User_AppModel();

            //是否存在
            $user_app_row = $User_App->getAppByNameAndAppId($user_name, $app_id);

            if ($user_app_row)
            {
                // update app_quantity
                $app_quantity_row = array();
                $app_quantity_row['app_quantity'] = $user_app_row['app_quantity'] + 1;
                $flag = $User_App->editApp($user_name, $app_quantity_row);
                array_push($rs_row, $flag);
            }
            else
            {

                $flag = $User_App->addApp($arr_field_user_app);
                array_push($rs_row, $flag);

            }

            $User_AppServerModel = new User_AppServerModel();

            $user_app_server_row = array();
            $user_app_server_row['user_name'] = $user_name;
            $user_app_server_row['app_id'] = $app_id;
            $user_app_server_row['server_id'] = $server_id;
            $user_app_server_row['active_time'] = time();

            $flag = $User_AppServerModel->addAppServer($user_app_server_row);
            */
        } else {
        }
        if (is_ok($rs_row) && $User_InfoDetail->sql->commit()) {
            $d = [];
            $d['user_id'] = $user_id;
            $encrypt_str = Perm::encryptUserInfo($d, $session_id);
            $arr_body = [
                "user_name" => $user_name,
                "server_id" => $server_id,
                "k" => $encrypt_str,
                "user_id" => $user_id
            ];
            $this->data->addBody(100, $arr_body, 'sucess', 200);
        } else {
            $Base_App->sql->rollBack();

            return $this->data->addBody(-1, [], '创建用户信息失败', 250);
        }
    }

    public function regConfig()
    {
        $Web_ConfigModel = new Web_ConfigModel();
        $config_type_row = request_row('config_type');
        fb($config_type_row);
        $rs_row = [];
        foreach ($config_type_row as $config_type) {
            $config_value_row = request_row($config_type);
            fb($config_value_row);
            $config_rows = $Web_ConfigModel->getByWhere(['config_type' => $config_type]);
            fb($config_rows);
            foreach ($config_rows as $config_key => $config_row) {
                $edit_row = [];
                if (isset($config_value_row[$config_key])) {
                    if ('json' == $config_row['config_datatype']) {
                        $edit_row['config_value'] = json_encode($config_value_row[$config_key]);
                    } else {
                        $edit_row['config_value'] = $config_value_row[$config_key];
                    }
                } else {
                    if ('number' == $config_row['config_datatype']) {
                        if ('theme_id' != $config_key) {
                            //$edit_row['config_value'] = 0;
                        }
                    } else {
                    }
                }
                if ($edit_row) {
                    $flag = $Web_ConfigModel->editConfig($config_key, $edit_row);
                    check_rs($flag, $rs_row);
                }
            }
        }
        $flag = is_ok($rs_row);
        if ($flag) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-1, $edit_row, $msg, $status);
    }

    public function checkEmail()
    {
        $email = request_string('email');
        //判断邮箱号是否已经注册过
        $User_InfoDetail = new User_InfoDetailModel();
        $checkmobile = $User_InfoDetail->checkEmail($email);
        $data = [];
        if ($checkmobile) {
            $msg = 'failure';
            $status = 250;
        } else {
            $msg = 'success';
            $status = 200;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     *  缓存验证码
     *
     * @param type $key
     * @param type $value
     * @param type $group
     *
     * @return type
     */
    public function _saveCodeCache($key, $value, $group = 'verify_code')
    {
        $config_cache = Yf_Registry::get('config_cache');
        if (!file_exists($config_cache[$group]['cacheDir'])) {
            mkdir($config_cache[$group]['cacheDir'], 0777, true);
        }
        $Cache_Lite = new Cache_Lite_Output($config_cache[$group]);
        $result = $Cache_Lite->save($value, $key, $group);

        return $result;
    }

    //判断用户是否绑定手机号
    public function checkUserMobile()
    {
        $user_id = request_int('user_id');
        //查找绑定表
        $User_BindConnectModel = new User_BindConnectModel();
        $bind_id_row = $User_BindConnectModel->getBindConnectByuserid($user_id, User_BindConnectModel::MOBILE);
        if ($bind_id_row) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, [], $msg, $status);
    }

    public function editNickName()
    {
        $nickname = request_string("nickname");
        $token = request_string('token');
        if (!$nickname || !$token) {
            return $this->data->setError('无效用户名');
        }
        $User_BindConnectModel = new User_BindConnectModel();
        $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
        $bind_info = current($bind_info);
        if (!$bind_info) {
            return $this->data->setError('绑定账号不存在');
        }
        //判断绑定账户是否已经绑定过用户，已经绑定过用户的账号，不可重复绑定
        if ($bind_info['user_id']) {
            return $this->data->setError('该账号已经绑定用户');
        }
        //判断该账号名是否已经存在
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $user_rows = $User_InfoModel->getInfoByName($nickname);
        //如果用户名已经存在了，则在用户名后面添加随机数
        if ($user_rows) {
            return $this->data->setError('已存在该用户名');
        }
        $bind_id = $bind_info['bind_id'];
        $flag = $User_BindConnectModel->editBindConnect($bind_id, ['bind_nickname' => $nickname]);
        if ($flag) {
            $msg = __('修改成功');
            $status = 200;
        } else {
            $msg = __('修改失败');
            $status = 250;
        }
        $this->data->addBody(-140, ['bind_nickname' => $nickname], $msg, $status);
    }

    /**
     * 客户端验证码
     */
    public function getAppVerifyCode()
    {
        $app_token = urldecode($_REQUEST['app_token']);
        $request_data = $this->ecryptDecode($app_token);
        if ($request_data) {
            $verify_key = trim($request_data['verify_key']);
            $code = mt_rand(100000, 999999);
            $save_result = $this->_saveCodeCache($verify_key, $code, 'verify_code');
            if (!$save_result) {
                return $this->data->addBody(-140, ['verify_code' => ''], __('failure'), 250);
            } else {
                return $this->data->addBody(-140, ['verify_code' => $code], __('success'), 200);
            }
        } else {
            return $this->data->addBody(-140, ['verify_code' => ''], __('验证失败'), 250);
        }
    }

    /**
     *  客户端验证短信内容
     */
    public function appRegCode()
    {
        $app_token = urldecode($_REQUEST['app_token']);
        $request_data = $this->ecryptDecode($app_token);
        $mobile = $request_data['mobile'];
        $area_code = $request_data['area_code']?:86;
        $email = $request_data['email'];
        if (!Perm::checkAppYzm($request_data['verify_code'], $request_data['verify_key'])) {
            return $this->data->addBody(-140, [], __('验证失败'), 250);
        }
        $check_code = mt_rand(100000, 999999);
        if ($mobile ) {
            if(!preg_match('/^1[\d]{10}$/', $mobile) && $area_code == 86){
                $msg = __('数据有误');
                $status = 250;
            }else {
                //判断手机号是否已经注册过
                $User_InfoDetail = new User_InfoDetailModel();
                $checkmobile = $User_InfoDetail->checkMobile($mobile);
                if ($checkmobile) {
                    $msg = __('该手机号已注册');
                    $status = 250;
                } else {
                    $save_result = $this->_saveCodeCache($mobile, $check_code, 'verify_code');
                    if (!$save_result) {
                        $msg = __('发送失败');
                        $status = 250;
                    } else {
                        //发送短消息
                        $message_model = new Message_TemplateModel();
                        $pattern = ['/\[weburl_name\]/', '/\[yzm\]/'];
                        $replacement = [Web_ConfigModel::value("site_name"), $check_code];
                        $message_info = $message_model->getTemplateInfo(['code' => 'regist_verify'], $pattern, $replacement);
                        if (!$message_info['is_phone']) {
                            $this->data->addBody(-140, [], __('信息内容创建失败'), 250);
                        }
                        $contents = $message_info['content_phone'];
                        $result = Sms::send($mobile,$area_code, $contents, $message_info['baidu_tpl_id'], ['weburl_name' => Web_ConfigModel::value("site_name"), 'yzm' => $check_code]);
                        if ($result) {
                            $msg = __('发送成功');
                            $status = 200;
                        } else {
                            $msg = __('发送失败');
                            $status = 250;
                        }
                    }
                }
            }
        } elseif ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            //判断邮箱是否已经注册过
            $User_InfoDetail = new User_InfoDetailModel();
            $checkemail = $User_InfoDetail->checkEmail($email);
            if ($checkemail) {
                $msg = __('该邮箱已注册');
                $status = 250;
            } else {
                $save_result = $this->_saveCodeCache($email, $check_code, 'verify_code');
                if (!$save_result) {
                    $msg = __('验证码获取失败');
                    $status = 250;
                } else {
                    //发送邮件
                    $message_model = new Message_TemplateModel();
                    $pattern = ['/\[weburl_name\]/', '/\[yzm\]/'];
                    $replacement = [Web_ConfigModel::value("site_name"), $message_info];
                    $message_info = $message_model->getTemplateInfo(['code' => 'regist_verify'], $pattern, $replacement);
                    if (!$message_info['is_email']) {
                        $this->data->addBody(-140, [], __('信息内容创建失败'), 250);
                    }
                    $title = '注册验证';
                    $contents = $message_info['content_email'];
                    $result = Email::send($email, '', $title, $contents);
                    if ($result) {
                        $msg = __('发送成功');
                        $status = 200;
                    } else {
                        $msg = __('发送失败');
                        $status = 250;
                    }
                }
            }
        } else {
            $msg = __('数据有误');
            $status = 250;
        }
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $check_code;
        }

        return $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 解密数据
     *
     * @param type $value
     *
     * @return type
     */
    private function ecryptDecode($value)
    {
        $ECryptModel = new ECrypt();
        $data = $ECryptModel->decode($value);

        return $data;
    }

    /**
     * wap 第三方登录接口
     *
     * @param  string data 用户信息数组
     * @param
     *
     * @return type
     */
    public function connect()
    {
        //ucenter.local.yuanfeng021.com/?ctl=Login&met=connect&bind_id=wb_4551A53720464EE84FBD83514454EE70&access_token=08FE7CD67F2BA1223BE002BD71877B11&bind_avator=http://q.qlogo.cn/qqapp/1106211603/4551A53720464EE84FBD83514454EE70/100&bind_gender=1&bind_nickname="\U7406\U60f3\U4e09\U65ec"&openid=4551A53720464EE84FBD83514454EE70&type=qq
        //localhost.pcenter.yuanfeng021.com/?ctl=Login&met=connect&bind_id=wb_4551A53720464EE84FBD83514454EE70&access_token=08FE7CD67F2BA1223BE002BD71877B11&bind_avator=http://q.qlogo.cn/qqapp/1106211603/4551A53720464EE84FBD83514454EE70/100&bind_gender=1&bind_nickname="\U7406\U60f3\U4e09\U65ec"&openid=4551A53720464EE84FBD83514454EE70&type=qq
        $bind_id = request_string('bind_id');
        $access_token = request_string('access_token');
        $openid = request_string('openid');
        $bind_avator = request_string('bind_avator');
        $bind_nickname = request_string('bind_nickname');
        $bind_gender = request_int('bind_gender');
        $ty = request_string('type'); //qq,weixin,weibo
        if ($ty == 'qq') {
            $type = User_BindConnectModel::QQ;
        }
        if ($ty == 'weixin') {
            $type = User_BindConnectModel::WEIXIN;
        }
        if ($ty == 'weibo') {
            $type = User_BindConnectModel::SINA_WEIBO;
        }
        /*$data['bind_id'] = $bind_id;
        $data['access_token'] = $access_token;
        $data['bind_nickname'] = $bind_nickname;
        $data['bind_gender'] = $bind_gender;
        $data['bind_gender'] = $ty;
        $data['bind_gender'] = $type;
        return $this->data->addBody(-140,$data);*/
        //判断当前登录账户
        if (Perm::checkUserPerm()) {
            $user_id = Perm::$userId;
        } else {
            $user_id = 0;
        }
        $connect_rows = [];
        $User_BindConnectModel = new User_BindConnectModel();
        $connect_rows = $User_BindConnectModel->getBindConnect($bind_id);
        if ($connect_rows) {
            $connect_row = array_pop($connect_rows);
        }
        //已经绑定,并且用户正确
        if (isset($connect_row['user_id']) && $connect_row['user_id']) {
            //验证通过, 登录成功.
            if ($user_id && $user_id == $connect_row['user_id']) {
                echo '非法请求,已经登录用户不应该访问到此页面';

                return $this->data->addBody('非法请求,已经登录用户不应该访问到此页面');
                die();
            }
            $login_flag = true;
        } else {
            // 下面可以需要封装
            $bind_rows = $User_BindConnectModel->getBindConnect($bind_id);
            if ($bind_rows && $bind_row = array_pop($bind_rows)) {
                if ($bind_row['user_id']) {
                    //该账号已经绑定
                    echo '非法请求,该账号已经绑定';

                    return $this->data->addBody('非法请求,该账号已经绑定');
                    die();
                }
                if ($user_id != 0) {
                    $bind_id_row = $User_BindConnectModel->getBindConnectByuserid($user_id, $type);
                    if ($bind_id_row) {
                        echo '非法请求,该账号已经绑定';

                        return $this->data->addBody('非法请求,该账号已经绑定');
                        die();
                    }
                }
                $data_row = [];
                $data_row['user_id'] = $user_id;
                $data_row['bind_token'] = $access_token;
                $connect_flag = true;
                $User_BindConnectModel->editBindConnect($bind_id, $data_row);
            } else {
                if ($user_id != 0) {
                    $bind_id_row = $User_BindConnectModel->getBindConnectByuserid($user_id, $type);
                    if ($bind_id_row) {
                        echo '非法请求,该账号已经绑定';

                        return $this->data->addBody('非法请求,该账号已经绑定');
                        die();
                    }
                }
                //插入绑定表
                $bind_array = [];
                $bind_array = [
                    'bind_id' => $bind_id,
                    'bind_type' => $type,
                    'user_id' => $user_id,
                    'bind_nickname' => $bind_nickname,
                    'bind_avator' => $bind_avator,
                    'bind_gender' => $bind_gender,
                    'bind_openid' => $openid,
                    'bind_token' => $access_token,
                ];
                $connect_flag = $User_BindConnectModel->addBindConnect($bind_array);
            }
            //取得open id, 需要封装
            if ($connect_flag) {
                //选择,登录绑定还是新创建账号 $user_id == 0
                if (!Perm::checkUserPerm()) {
                    $url = sprintf('%s?ctl=Login&met=select&t=%s&type=%s&from=%s&callback=%s', Yf_Registry::get('url'), $access_token, $type, request_string('from'), urlencode(request_string('callback') ? :$_GET['callbak']));
                    $msg = 'success';
                    $status = 210;
                    $da['url'] = $url;

                    return $this->data->addBody(-140, $da, $msg, $status);
                    die;
                } else {
                    $login_flag = true;
                }
            }
        }
        if ($login_flag) {
            //验证通过, 登录成功.
            if ($user_id && $user_id == $connect_row['user_id']) {
                echo '非法请求,已经登录用户不应该访问到此页面';

                return $this->data->addBody('非法请求,已经登录用户不应该访问到此页面');
                die;
            } else {
                $User_InfoModel = new User_InfoModel();
                $result = $User_InfoModel->userlogin($connect_row['user_id']);
                if ($result) {
                    $msg = 'success';
                    $status = 200;

                    return $this->data->addBody(-140, $result, $msg, $status);
                } else {
                    return $this->data->addBody('登录失败');
                }
            }
        }
    }

    public function del()
    {
        $bind_id = request_string('bind_id');
        $User_BindConnectModel = new User_BindConnectModel();
        $flag = $User_BindConnectModel->removeBindConnect($bind_id);
        if ($flag) {
            $msg = 'success';
            $status = 200;

            return $this->data->addBody(-140, [], $msg, $status);
        } else {
            return $this->data->addBody('失败');
        }
    }

    /**
     * app发送短信验证码
     * 找回密码
     *
     * @return type
     */
    public function appPhoneCode()
    {
        $app_token = urldecode($_REQUEST['app_token']);
        $request_data = $this->ecryptDecode($app_token);
        $mobile = $request_data['mobile'];
        $area_code = $request_data['area_code']?:86;
        $data = [];
        if (!Perm::checkAppYzm($request_data['verify_code'], $request_data['verify_key'])) {
            return $this->data->addBody(-140, $data, __('验证失败'), 250);
        }
        //判断用户是否存在  $mobile
        $User_InfoDetail = new User_InfoDetailModel();
        $checkMobile = $User_InfoDetail->isUserMobile($mobile);
        if (!$checkMobile) {
            return $this->data->addBody(-140, $data, __('请填写注册或绑定的手机号'), 250);
        }
        $check_code = mt_rand(100000, 999999);
        if ($mobile) {
            if(!Yf_Utils_String::isMobile($mobile) && $area_code==86){
                $msg = __('手机号码有误');
                $status = 250;
            }else {
                //缓存数据
                $save_result = $this->_saveCodeCache($mobile, $check_code, 'verify_code');
                if (!$save_result) {
                    $msg = __('发送失败');
                    $status = 250;
                } else {
                    //发送短消息
                    $contents = '您的验证码是：' . $check_code . '。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';
                    $result = Sms::send($mobile,$area_code, $contents);
                    $msg = $result ? __('发送成功') : __('发送失败');
                    $status = $result ? 200 : 250;
                }
            }
        } else {
            $msg = __('手机号码有误');
            $status = 250;
        }
        if (DEBUG === false) {
            $data['user_code'] = $check_code;
        }

        return $this->data->addBody(-140, $data, $msg, $status);
    }

    public function getMobileYzm()
    {
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $cond_row['code'] = request_string('type') == 'passwd' ? 'edit_passwd':'verification';
        $yzm = request_string('yzm');
        if (!Perm::checkYzm($yzm)) {
            return $this->data->addBody(-140, [], _('图形验证码有误'), 250);
        }
        $Message_TemplateModel = new Message_TemplateModel();
        $de = $Message_TemplateModel->getTemplateDetail($cond_row);
        $me = $de['content_phone'];
        $code_key = $mobile;
        $code = VerifyCode::getCode($code_key);
        $me = str_replace("[weburl_name]", $this->web['web_name'], $me);
        $me = str_replace("[yzm]", $code, $me);
        $str = Sms::send($mobile,$area_code, $me, $de['baidu_tpl_id'],['weburl_name'=>$this->web['web_name'],'yzm'=>$code]);
        $status = $str ? 200:250;
        $msg = $str ? _('发送成功'):_('发送失败');
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $code;
        }
        $data['user_code'] = $code;
        return $this->data->addBody(-140, $data, $msg, $status);
    }

    //手机号登陆获取验证码
    public function getMobileCode()
    {
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $cond_row['code'] = request_string('type') == 'passwd' ? 'edit_passwd':'verification';
        $yzm = request_string('yzm');

        if (!empty($yzm)) {
            session_start();
            if (!$yzm || strtolower($yzm) !== strtolower($_SESSION["auth"])) {
                return $this->data->setError('图片验证码错误');
            }
        } else {
            return $this->data->setError('图片验证码不能为空');
        }
        $Message_TemplateModel = new Message_TemplateModel();
        $de = $Message_TemplateModel->getTemplateDetail($cond_row);
        $me = $de['content_phone'];
        $code_key = $mobile;
        $code = VerifyCode::getCode($code_key);
        $me = str_replace("[weburl_name]", $this->web['web_name'], $me);
        $me = str_replace("[yzm]", $code, $me);
        $str = Sms::send($mobile,$area_code, $me, $de['baidu_tpl_id'],['weburl_name'=>$this->web['web_name'],'yzm'=>$code]);
        $status = $str ? 200:250;
        $msg = $str ? _('发送成功'):_('发送失败');
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $code;
        }
        $data['user_code'] = $code;
        return $this->data->addBody(-140, $data, $msg, $status);
    }

    public function checkMobileYzm()
    {
        $yzm = request_string('yzm');
        $mobile = request_string('mobile');
        if (VerifyCode::checkCode($mobile, $yzm)) {
            $status = 200;
            $msg = _('success');
        } else {
            $msg = _('failure');
            $status = 250;
        }
        $data = [];
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function editMobileInfo()
    {
        $user_name = request_string('user_name');
        $user_pwd = request_string('user_pwd');
        $userModel = new User_InfoModel();
        //通过用户名和用户密码查找用户
        $cond['user_name'] = $user_name;
        $cond['password'] = md5($user_pwd);
        $user = $userModel->getByWhere($cond);
        if ($user) {
            $user = current($user);
            $user_id = $user['user_id'];
        } else {
            return $this->data->addBody(-140, [], __('用户信息有误'), 250);
        }
        //检查用户信息
        $cond_user['user_id'] = $user_id;
        $user_info_row = $userModel->getOne($cond_user);
        if (!$user_info_row) {
            $data['code'] = 3;

            return $this->data->addBody(-140, $data, __('用户信息有误'), 250);
        }
        $user_name = $user_info_row['user_name'];
        $rs_row = [];
        $user_mobile = request_string('user_mobile');
        $yzm = request_string('yzm', request_string('auth_code'));
        $data = [];
        if (!VerifyCode::checkCode($user_mobile, $yzm)) {
            $data['code'] = 1;

            return $this->data->addBody(-140, $data, __('验证码错误'), 250);
        }
        //检查手机号是否被使用
        $userInfoDetailModel = new User_InfoDetail();
        $check_user_name = $userInfoDetailModel->checkUserName($user_mobile, $user_id);
        if ($check_user_name) {
            $data['code'] = 2;

            return $this->data->addBody(-140, $data, __('该手机已经被使用'), 250);
        }
        //查找bind绑定表
        $new_bind_id = sprintf('mobile_%s', $user_mobile);
        $User_BindConnectModel = new User_BindConnectModel();
        $bind_info = $User_BindConnectModel->getOne($new_bind_id);
        if (isset($bind_info['user_id']) && $bind_info['user_id'] != $user_id) {
            $data['code'] = 4;

            return $this->data->addBody(-140, $data, __('该手机已经被使用'), 250);
        }
        //开启事务
        $User_InfoDetailModel = new User_InfoDetailModel();
        $User_InfoDetailModel->sql->startTransactionDb();
        //查找该用户之前是否已经绑定手机号，如果有的话需要删除
        $user_mobile_bind = $User_BindConnectModel->getByWhere(['user_id' => $user_id, 'bind_type' => $User_BindConnectModel::MOBILE]);
        if ($user_mobile_bind) {
            $old_bind_id_row = array_keys($user_mobile_bind);
            //将之前用户绑定的手机号删除
            $flag_remove = $User_BindConnectModel->removeBindConnect($old_bind_id_row);
            check_rs($flag_remove, $rs_row);
        }
        //该手机号可用，将手机号写入用户详情表中，验证状态为已验证
        if ($user_name) {
            $edit_user_row['user_mobile'] = $user_mobile;
            $edit_user_row['user_mobile_verify'] = 1;
            $flag_edit = $User_InfoDetailModel->editInfoDetail($user_name, $edit_user_row);
            check_rs($flag_edit, $rs_row);
        } else {
            $flag_edit = true;
        }
        if ($flag_edit === false) {
            $User_InfoDetailModel->sql->rollBackDb();
            $data['code'] = 5;

            return $this->data->addBody(-140, $data, __('绑定失败'), 250);
        }
        //用户信息表中的手机号修改完成后，修改绑定表中的数据
        //添加mobile绑定.
        //绑定标记：mobile/email/openid  绑定类型+openid
        //插入绑定表
        if (isset($bind_info['user_id']) && $bind_info['user_id'] == $user_id) {
            check_rs(true, $rs_row);
        } else {
            $time = date('Y-m-d H:i:s', time());
            $bind_array = ['bind_id' => $new_bind_id, 'user_id' => $user_id, 'bind_type' => $User_BindConnectModel::MOBILE, 'bind_time' => $time];
            $flag_add = $User_BindConnectModel->addBindConnect($bind_array);
            if ($flag_add) {
                //将用户原来绑定的手机号删除
                $bind_id = sprintf('mobile_%s', $user_info_row['user_mobile']);
                $flag_del = $User_BindConnectModel->removeBindConnect($bind_id);
                check_rs($flag_del, $rs_row);
            }
        }
        $flag = is_ok($rs_row);
        $User_InfoDetailModel->sync($user_id);
        if ($flag && $User_InfoDetailModel->sql->commitDb()) {
            $status = 200;
            $msg = __('操作成功');
        } else {
            $User_InfoDetailModel->sql->rollBackDb();
            $msg = __('操作失败');
            $status = 250;
        }

        return $this->data->addBody(-140, $data, $msg, $status);
    }

    /**** IM 调用的接口-S  *****/
    //im登录接口
    public function ImLogin()
    {
        $user_name = strtolower($_REQUEST['user_account']);
        if (!$user_name) {
            $user_name = strtolower($_REQUEST['user_name']);
        }
        $password = $_REQUEST['user_password'];
        if (!$password) {
            $password = $_REQUEST['password'];
        }
        if (!strlen($user_name)) {
            $this->data->setError('请输入账号');
        }
        if (!strlen($password)) {
            $this->data->setError('请输入密码');
        }
        $User_BindConnectModel = new User_BindConnectModel();
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetailModel = new User_InfoDetailModel();
        //查找绑定表中是否存在此用户
        $bind_id = '';
        $user_info_row = [];
        //绑定标记：mobile/email/openid  绑定类型+openid
        {
            if (filter_var($user_name, FILTER_VALIDATE_EMAIL)) {
                //邮件登录
                $bind_id = sprintf('email_%s', $user_name);
            } elseif (Yf_Utils_String::isMobile($user_name)) {
                //手机号码登录
                $bind_id = sprintf('mobile_%s', $user_name);
            }
            if ($bind_id) {
                //查找bind绑定表
                $User_BindConnectModel = new User_BindConnectModel();
                $bind_info = $User_BindConnectModel->getOne($bind_id);
                if ($bind_info) {
                    //用户名登录
                    $user_info_row = $User_InfoModel->getOne($bind_info['user_id']);
                    $user_info_detail = $User_InfoDetailModel->getOne($user_info_row['user_name']);
                    $user_info_row = $user_info_row + $user_info_detail;
                }
            }
            if ($user_info_row) {
            } else {
                //用户名登录
                $user_info_row = $User_InfoModel->getInfoByName($user_name);
                $user_info_detail = $User_InfoDetailModel->getOne($user_name);
                $user_info_row = $user_info_row + $user_info_detail;
            }
        }
        if (!$user_info_row) {
            $this->data->setError('账号不存在');
        } else {
            if (md5($password) != $user_info_row['password'] && $password != $user_info_row['password']) {
                $this->data->setError('密码错误');
            } else {
                if (3 == $user_info_row['user_state']) {
                    $this->data->setError('用户已经锁定,禁止登录!');

                    return false;
                }
                $session_id = $user_info_row['session_id'];
                $arr_field = [];
                $arr_field['session_id'] = $session_id;
                if (true) {
                    $arr_body = $user_info_row;
                    $arr_body['result'] = 1;
                    $data = [];
                    $data['user_id'] = $user_info_row['user_id'];
                    $encrypt_str = Perm::encryptUserInfo($data, $session_id);
                    $arr_body['k'] = $encrypt_str;
                    $this->data->addBody(100, $arr_body);
                } else {
                    $this->data->setError('登录失败');
                }
            }
        }
        if (isset($_REQUEST['callback']) && $_REQUEST['callback']) {
            header("Location:" . urldecode($_REQUEST['callback']));
        }
    }

    /**** IM 调用的接口-E  *****/
    public function test()
    {
        $verify_key = '18667108609';
        $code = 123123123;
        $res = $this->_saveCodeCache($verify_key, $code, $group = 'default');
        var_dump($res);
        $config_cache = Yf_Registry::get('config_cache');
        $Cache_Lite = new Cache_Lite_Output($config_cache['default']);
        $user_code = $Cache_Lite->get($verify_key);
        echo $user_code;
    }


    //隐私协议
    public function privacy(){
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        include $this -> view -> getView();
    }



    
    /**** 新注册登录新增方法  *****/

    public function regist2(){
        include $this -> view -> getView();
        
        
    }
    
    public function newReg()
    {
         
    
        $user_name = request_int('mobile');
        $user_code = request_string('user_code');
        $mobile = request_string('mobile');
        //本地读取远程信息
        //只能只能只能使用分享链接注册新账号功能（闭环）
        /*$key = Yf_Registry::get('shop_api_key');;
        $url         = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars           = $_POST;
        $formvars['app_id'] = $shop_app_id;
        $fenxiao_data = get_url_with_encrypt($key, sprintf('%s?ctl=Api_%s&met=%s&typ=json', $url, 'Fenxiao', 'getStatus'), $formvars);
        if($fenxiao_data['data']['status'] == 1){
            if(empty($_COOKIE['uu_id']))
            {
                throw new Exception("请使用分享链接注册新账号");
            }
        }*/

        /** 此功能不完善，不能支持手机版和客户端，所以关掉
        $option_value_row = request_row('option');
        $Reg_OptionModel = new Reg_OptionModel();
        $reg_opt_rows = $Reg_OptionModel->getByWhere(['reg_option_active' => 1]);
        foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
        if ($reg_opt_row['reg_option_required']) {
        if ('' == $option_value_row[$reg_option_id]) {
        $this->data->setError('请输入' . $reg_opt_row['reg_option_name']);

        return false;
        }
        }
        }
         * **/
        $user_code = request_string('user_code');
        $mobile = request_string('mobile');
        $token = request_string('t');
        $app_id = request_int('app_id');
        $area_code = 86;
        $reg_checkcode = 1;
        $shop_id_wap = request_int('shop_id_wap');


        $server_id = 0;
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if (!$user_name) {
            $this->data->setError('请输入账号');

            return false;
        }
        // if (!$password) {
        //     $this->data->setError('请输入密码');

        //     return false;
        // }
       
      
      
        if (VerifyCode::checkCode($mobile, $user_code)) {
            $rs_row = [];
            //用户是否存在
            $User_InfoModel = new User_InfoModel();
            $User_InfoDetail = new User_InfoDetailModel();
            $user_rows = $User_InfoDetail->checkUserName($user_name);
            $user_name_info = $User_InfoModel->getInfoByName($user_name);
            $user_info_detail = $User_InfoDetail->getOneByWhere(array('user_mobile'=>$mobile));
            if ($user_rows || $user_name_info||$user_info_detail) {
                $this->data->setError('用户已经存在,请更换用户名!');

                return false;
            } else {
                $User_InfoModel->sql->startTransaction();
                $Db = Yf_Db::get('ucenter');
                $seq_name = 'user_id';
                $user_id = $Db->nextId($seq_name);

//              $User_InfoModel->check_input($user_name, $password, $user_mobile);
                $now_time = time();
                $ip = get_ip();
                $session_id = uniqid();
                $arr_field_user_info = [];
                $arr_field_user_info['user_id'] = $user_id;
                $arr_field_user_info['user_name'] = $user_name;
                //$arr_field_user_info['password'] = md5($password);
                $arr_field_user_info['action_time'] = $now_time;
                $arr_field_user_info['action_ip'] = $ip;
                $arr_field_user_info['session_id'] = $session_id;
                $flag = $User_InfoModel->addInfo($arr_field_user_info);
                array_push($rs_row, $flag);
                $arr_field_user_info_detail = [];
                //添加mobile绑定.
                //绑定标记：mobile/email/openid  绑定类型+openid
                $bind_id = $reg_checkcode == 1 ? sprintf('mobile_%s', $mobile):sprintf('email_%s', $email);
                //查找bind绑定表
                $User_BindConnectModel = new User_BindConnectModel();
                $bind_info = $User_BindConnectModel->getOne($bind_id);
                if (!$bind_info) {
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'bind_id' => $bind_id,
                        'user_id' => $user_id,
                        'bind_type' => $reg_checkcode == 1 ? $User_BindConnectModel::MOBILE:$User_BindConnectModel::EMAIL,
                        'bind_time' => $time
                    ];
                    $flag = $User_BindConnectModel->addBindConnect($bind_array);
                    array_push($rs_row, $flag);
                    //绑定关系
                    if ($reg_checkcode == 1) {
                        $arr_field_user_info_detail['user_mobile_verify'] = 1;
                    } else {
                        $arr_field_user_info_detail['user_email_verify'] = 1;
                    }
                }
                $arr_field_user_info_detail['user_name'] = $user_name;
                if ($reg_checkcode == 1) {
                    $arr_field_user_info_detail['user_mobile'] = $mobile;
                    $arr_field_user_info_detail['area_code'] = $area_code;
                } else {
                    $arr_field_user_info_detail['user_email'] = $email;
                }
                //$arr_field_user_info_detail['user_mobile_verify']         = 1;
                $arr_field_user_info_detail['user_reg_time'] = $now_time;
                $arr_field_user_info_detail['user_count_login'] = 1;
                $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
                $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
                $arr_field_user_info_detail['user_reg_ip'] = $ip;
                $arr_field_user_info_detail['user_is_shop'] = $shop_id_wap;
                $arr_field_user_info_detail['user_avatar'] = Web_ConfigModel::value('user_default_avatar', Yf_Registry::get('static_url') . '/images/default_user_portrait.png');
               
                $flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
                array_push($rs_row, $flag);
                $User_OptionModel = new User_OptionModel();
                foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
                    if (isset($option_value_row[$reg_option_id])) {
                        $reg_option_value_row = explode(',', $reg_opt_row['reg_option_value']);
                        $user_option_row = [];
                        $user_option_row['reg_option_id'] = $reg_option_id;
                        $user_option_row['reg_option_value_id'] = $option_value_row[$reg_option_id];
                        $user_option_row['user_id'] = $user_id;
                        $user_option_row['user_option_value'] = isset($reg_option_value_row[$option_value_row[$reg_option_id]]) ? $reg_option_value_row[$option_value_row[$reg_option_id]]:$option_value_row[$reg_option_id];
                        $flag = $User_OptionModel->addOption($user_option_row);
                        array_push($rs_row, $flag);
                    }
                }
            }
            if (is_ok($rs_row) && $User_InfoDetail->sql->commit()) {
                $d = [];
                $d['user_id'] = $user_id;
                $encrypt_str = Perm::encryptUserInfo($d, $session_id);
                $arr_body = [
                    "user_name" => $user_name,
                    "server_id" => $server_id,
                    "k" => $encrypt_str,
                    "user_id" => $user_id
                ];
                if ($token) {
                    //查找bind绑定表
                    $User_BindConnectModel = new User_BindConnectModel();
                    $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
                    $bind_info = $bind_info[0];
                    //获取qq缩略头像
                    $qq_logo = substr($bind_info['bind_avator'], 0, strrpos($bind_info['bind_avator'], '/'));
                    $qq_logo = $qq_logo . '/40';
                    //更新用户详情表
                    if ($bind_info['bind_gender'] == 1) {
                        $gender = 1;
                    } else {
                        $gender = 0;
                    }
                    $user_info_detail = [
                        'nickname' => $bind_info['bind_nickname'],
                        'user_avatar' => $bind_info['bind_avator'],
                        'user_gender' => $gender,
                        'user_avatar_thumb' => $qq_logo,
                    ];
                    $User_InfoDetail->editInfoDetail($user_name, $user_info_detail);
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'user_id' => $user_id,
                        'bind_time' => $time,
                        'bind_token' => $token,
                    ];
                    $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
                }
            
                return $this->data->addBody(100, $arr_body);
            } else {
                $User_InfoDetail->sql->rollBack();
                $this->data->setError('创建用户信息失败');
            }
        } else {
            $msg = $code_from . '验证码错误';
            $status = 250;
            if (DEBUG !== true) {
                $user_code = "";
            }

            return $this->data->addBody(-1, ['code' => $user_code,'debug'=>DEBUG], $msg, $status);
        }
    }
    
    public function wxappnewReg()
    {
         
    
        $user_name = request_int('mobile');
        //本地读取远程信息
        //只能只能只能使用分享链接注册新账号功能（闭环）
        /*$key = Yf_Registry::get('shop_api_key');;
        $url         = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars           = $_POST;
        $formvars['app_id'] = $shop_app_id;
        $fenxiao_data = get_url_with_encrypt($key, sprintf('%s?ctl=Api_%s&met=%s&typ=json', $url, 'Fenxiao', 'getStatus'), $formvars);
        if($fenxiao_data['data']['status'] == 1){
            if(empty($_COOKIE['uu_id']))
            {
                throw new Exception("请使用分享链接注册新账号");
            }
        }*/

        /** 此功能不完善，不能支持手机版和客户端，所以关掉
        $option_value_row = request_row('option');
        $Reg_OptionModel = new Reg_OptionModel();
        $reg_opt_rows = $Reg_OptionModel->getByWhere(['reg_option_active' => 1]);
        foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
        if ($reg_opt_row['reg_option_required']) {
        if ('' == $option_value_row[$reg_option_id]) {
        $this->data->setError('请输入' . $reg_opt_row['reg_option_name']);

        return false;
        }
        }
        }
         * **/
        $user_code = request_string('user_code');
        $mobile = request_string('mobile');
        $token = request_string('t');
        $app_id = request_int('app_id');
        $area_code = 86;
        $reg_checkcode = 1;
        $shop_id_wap = request_int('shop_id_wap');


        $server_id = 0;
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if (!$user_name) {
            $this->data->setError('请输入账号');

            return false;
        }
        // if (!$password) {
        //     $this->data->setError('请输入密码');

        //     return false;
        // }
       
      
        $config_cache = Yf_Registry::get('config_cache');
        $Cache_Lite = new Cache_Lite_Output($config_cache['verify_code']);
        $user_code_pre = $Cache_Lite->get($mobile,'verify_code');
        

      
        if ($user_code == $user_code_pre) {
            $rs_row = [];
            //用户是否存在
            $User_InfoModel = new User_InfoModel();
            $User_InfoDetail = new User_InfoDetailModel();
            $user_rows = $User_InfoDetail->checkUserName($user_name);
            $user_name_info = $User_InfoModel->getInfoByName($user_name);
            $user_info_detail = $User_InfoDetail->getOneByWhere(array('user_mobile'=>$mobile));
            if ($user_rows || $user_name_info||$user_info_detail) {
                $this->data->setError('用户已经存在,请更换用户名!');

                return false;
            } else {
                $User_InfoModel->sql->startTransaction();
                $Db = Yf_Db::get('ucenter');
                $seq_name = 'user_id';
                $user_id = $Db->nextId($seq_name);

//              $User_InfoModel->check_input($user_name, $password, $user_mobile);
                $now_time = time();
                $ip = get_ip();
                $session_id = uniqid();
                $arr_field_user_info = [];
                $arr_field_user_info['user_id'] = $user_id;
                $arr_field_user_info['user_name'] = $user_name;
                //$arr_field_user_info['password'] = md5($password);
                $arr_field_user_info['action_time'] = $now_time;
                $arr_field_user_info['action_ip'] = $ip;
                $arr_field_user_info['session_id'] = $session_id;
                $flag = $User_InfoModel->addInfo($arr_field_user_info);
                array_push($rs_row, $flag);
                $arr_field_user_info_detail = [];
                //添加mobile绑定.
                //绑定标记：mobile/email/openid  绑定类型+openid
                $bind_id = $reg_checkcode == 1 ? sprintf('mobile_%s', $mobile):sprintf('email_%s', $email);
                //查找bind绑定表
                $User_BindConnectModel = new User_BindConnectModel();
                $bind_info = $User_BindConnectModel->getOne($bind_id);
                if (!$bind_info) {
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'bind_id' => $bind_id,
                        'user_id' => $user_id,
                        'bind_type' => $reg_checkcode == 1 ? $User_BindConnectModel::MOBILE:$User_BindConnectModel::EMAIL,
                        'bind_time' => $time
                    ];
                    $flag = $User_BindConnectModel->addBindConnect($bind_array);
                    array_push($rs_row, $flag);
                    //绑定关系
                    if ($reg_checkcode == 1) {
                        $arr_field_user_info_detail['user_mobile_verify'] = 1;
                    } else {
                        $arr_field_user_info_detail['user_email_verify'] = 1;
                    }
                }
                $arr_field_user_info_detail['user_name'] = $user_name;
                if ($reg_checkcode == 1) {
                    $arr_field_user_info_detail['user_mobile'] = $mobile;
                    $arr_field_user_info_detail['area_code'] = $area_code;
                } else {
                    $arr_field_user_info_detail['user_email'] = $email;
                }
                //$arr_field_user_info_detail['user_mobile_verify']         = 1;
                $arr_field_user_info_detail['user_reg_time'] = $now_time;
                $arr_field_user_info_detail['user_count_login'] = 1;
                $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
                $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
                $arr_field_user_info_detail['user_reg_ip'] = $ip;
                $arr_field_user_info_detail['user_is_shop'] = $shop_id_wap;
                $arr_field_user_info_detail['user_avatar'] = Web_ConfigModel::value('user_default_avatar', Yf_Registry::get('static_url') . '/images/default_user_portrait.png');
               
                $flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
                array_push($rs_row, $flag);
                $User_OptionModel = new User_OptionModel();
                foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
                    if (isset($option_value_row[$reg_option_id])) {
                        $reg_option_value_row = explode(',', $reg_opt_row['reg_option_value']);
                        $user_option_row = [];
                        $user_option_row['reg_option_id'] = $reg_option_id;
                        $user_option_row['reg_option_value_id'] = $option_value_row[$reg_option_id];
                        $user_option_row['user_id'] = $user_id;
                        $user_option_row['user_option_value'] = isset($reg_option_value_row[$option_value_row[$reg_option_id]]) ? $reg_option_value_row[$option_value_row[$reg_option_id]]:$option_value_row[$reg_option_id];
                        $flag = $User_OptionModel->addOption($user_option_row);
                        array_push($rs_row, $flag);
                    }
                }
            }
            if (is_ok($rs_row) && $User_InfoDetail->sql->commit()) {
                $d = [];
                $d['user_id'] = $user_id;
                $encrypt_str = Perm::encryptUserInfo($d, $session_id);
                $arr_body = [
                    "user_name" => $user_name,
                    "server_id" => $server_id,
                    "k" => $encrypt_str,
                    "user_id" => $user_id
                ];
                if ($token) {
                    //查找bind绑定表
                    $User_BindConnectModel = new User_BindConnectModel();
                    $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
                    $bind_info = $bind_info[0];
                    //获取qq缩略头像
                    $qq_logo = substr($bind_info['bind_avator'], 0, strrpos($bind_info['bind_avator'], '/'));
                    $qq_logo = $qq_logo . '/40';
                    //更新用户详情表
                    if ($bind_info['bind_gender'] == 1) {
                        $gender = 1;
                    } else {
                        $gender = 0;
                    }
                    $user_info_detail = [
                        'nickname' => $bind_info['bind_nickname'],
                        'user_avatar' => $bind_info['bind_avator'],
                        'user_gender' => $gender,
                        'user_avatar_thumb' => $qq_logo,
                    ];
                    $User_InfoDetail->editInfoDetail($user_name, $user_info_detail);
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'user_id' => $user_id,
                        'bind_time' => $time,
                        'bind_token' => $token,
                    ];
                    $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
                }
            
                return $this->data->addBody(100, $arr_body);
            } else {
                $User_InfoDetail->sql->rollBack();
                $this->data->setError('创建用户信息失败');
            }
        } else {
            $msg = $code_from . '验证码错误';
            $status = 250;
            if (DEBUG !== true) {
                $user_code = "";
            }

            return $this->data->addBody(-1, ['code' => $user_code,'debug'=>DEBUG], $msg, $status);
        }
    }
    
    public function newCheckYzm(){
        $user_code = request_string('user_code');
        $mobile = request_string('mobile');
        if (!VerifyCode::checkCode($mobile, $user_code)) {
            $msg = '手机验证码错误';
            $status = 250;
            
        }else{
            
            $msg = '通过';
            $status = 200;
        }
        $this->data->addBody(-1, ['code' => $user_code,'debug'=>DEBUG], $msg, $status);
    }
    
    public function resetPasswdNew()
    {
//      $img_code = request_string('imgCode');
//        session_start();
//        if (strtolower($img_code) != strtolower($_SESSION['auth'])) {
//            return $this->data->addBody(-140, [], '验证码错误', 210);
//        }
       
        $from = request_string('from');
        $data = [];
        $data['mobile'] = request_string('mobile');
      
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if ($reg_row['reg_number']['config_value'] == 1) {
            if (!preg_match("/^[0-9]*$/", request_string('user_password'))) {
                $this->data->setError('密码必须纯数字组合');

                return false;
            }
        }
        if ($reg_row['reg_lowercase']['config_value'] == 1) {
            if (!preg_match("/^[A-Za-z]+$/", request_string('user_password'))) {
                $this->data->setError('密码必须纯英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_uppercase']['config_value'] == 1) {
            if (!preg_match("/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]{0,20})$/", request_string('user_password'))) {
                $this->data->setError('密码必须数字英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_symbols']['config_value'] == 1) {
            if (!preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、])[\da-zA-Z`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、]{0,20}$/", request_string('user_password'))) {
                $this->data->setError('密码必须数字英文字母及符号组合');

                return false;
            }
        }
        if (strlen(request_string('user_password'))< $reg_row['reg_pwdlength']['config_value']) {
            $this->data->setError('密码最小长度为'.$reg_row['reg_pwdlength']['config_value'].'位');
            return false;

        }
        $data['password'] = md5(request_string('user_password'));
        $data['passworderp'] = request_string('user_password');
        $reg_checkcode = request_string('reg_checkcode', '1');
        //为erp做的修改密码
        if ($from == 'erp' || $from == 'chain') {
            $data['user'] = request_string('user_account');
            $User_InfoModel = new User_InfoModel();
            //检测登录状态
            $user_id_row = $User_InfoModel->getInfoByName($data['user']);
            if ($user_id_row) {
                //重置密码
                $user_id = $user_id_row['user_id'];
                $reset_passwd_row = [];
                $reset_passwd_row['password'] = $from == 'erp' ? $data['passworderp']:$data['password'];
                $flag = $User_InfoModel->editInfo($user_id, $reset_passwd_row);
                if ($flag !== false) {
                    $msg = '重置密码成功';
                    $status = 200;
                } else {
                    $msg = '重置密码失败';
                    $status = 250;
                }
            } else {
                $msg = '用户不存在';
                $status = 250;
            }
            unset($data['password']);
        } else {
           
                if ($reg_checkcode == 1 || $reg_checkcode == 3) {
                    if (!$data['mobile']) {
                        $this->data->setError('手机号不能为空');

                        return false;
                    }
                } else {
                    if (!$data['email']) {
                        $this->data->setError('邮箱不能为空');

                        return false;
                    }
                }
                if (request_string('user_password')) {
                    $config_cache = Yf_Registry::get('config_cache');
                    $Cache_Lite = new Cache_Lite_Output($config_cache['verify_code']);
                    if ($reg_checkcode == 2) {
                        $user_code_pre = $Cache_Lite->get($data['email'], 'verify_code');
                    } else {
                        $user_code_pre = $Cache_Lite->get($data['mobile'], 'verify_code');
                    }
                    //$user_code_pre = $reg_checkcode == 1 ? $Cache_Lite->get($data['mobile']) : $Cache_Lite->get($data['email']);
                    if ($user_code == $user_code_pre) {
                        $User_InfoModel = new User_InfoModel();
                        $User_InfoDetailModel = new User_InfoDetailModel();
                        //检测登录状态
                        if ($reg_checkcode == 2) {
                            $data['user'] = $User_InfoDetailModel->getUserByEmail($data['email']);
                        } else {
                            $data['user'] = $User_InfoDetailModel->getUserByMobile($data['mobile']);
                        }
                        //$data['user'] = $reg_checkcode == 1 ? $User_InfoDetailModel->getUserByMobile($data['mobile']) : $User_InfoDetailModel->getUserByEmail($data['email']);
                        $user_id_row = $User_InfoModel->getInfoByName($data['user']);
                        if ($user_id_row) {
                            //重置密码
                            $user_id = $user_id_row['user_id'];
                            $reset_passwd_row = [];
                            $reset_passwd_row['password'] = $data['password'];
                            $flag = $User_InfoModel->editInfo($user_id, $reset_passwd_row);
                            if ($flag === 'false') {
                                $msg = '网路故障，请稍后重试';
                                $status = 250;
                            } else {
                                $msg = '重置密码成功';
                                $status = 200;
                                //使验证码失效
                                if ($reg_checkcode == 2) {
                                    $reg_checkcode == $Cache_Lite->remove($data['email']);
                                } else {
                                    $reg_checkcode == $Cache_Lite->remove($data['mobile']);
                                }
                                //$reg_checkcode == 1 ? $Cache_Lite->remove($data['mobile']) : $Cache_Lite->remove($data['email']);
                            }
                        } else {
                            $msg = '用户不存在';
                            $status = 250;
                        }
                    } else {
                        $msg = '验证码错误' . $Cache_Lite->get($data['email']);
                        $status = 250;
                    }
                } else {
                    $msg = '密码不能为空';
                    $status = 250;
                }
            
            unset($data['password']);
        }
        $this->data->addBody(-140, ['user_code' => $user_code, 'user_code_pre' => $user_code_pre], $msg, $status);
    }
    
    //小程序设置密码
    public function wxappresetPasswdNew()
    {
//      $img_code = request_string('imgCode');
//        session_start();
//        if (strtolower($img_code) != strtolower($_SESSION['auth'])) {
//            return $this->data->addBody(-140, [], '验证码错误', 210);
//        }
       
        $from = request_string('from');
        $data = [];
        $data['mobile'] = request_string('mobile');
      
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if ($reg_row['reg_number']['config_value'] == 1) {
            if (!preg_match("/^[0-9]*$/", request_string('user_password'))) {
                $this->data->setError('密码必须纯数字组合');

                return false;
            }
        }
        if ($reg_row['reg_lowercase']['config_value'] == 1) {
            if (!preg_match("/^[A-Za-z]+$/", request_string('user_password'))) {
                $this->data->setError('密码必须纯英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_uppercase']['config_value'] == 1) {
            if (!preg_match("/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]{0,20})$/", request_string('user_password'))) {
                $this->data->setError('密码必须数字英文字母组合');

                return false;
            }
        }
        if ($reg_row['reg_symbols']['config_value'] == 1) {
            if (!preg_match("/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、])[\da-zA-Z`~!@#$%^&*()_\-+=<>?:\"{}|,.\/;'\\[\]·~！@#￥%……&*（）——\-+={}|《》？：“”【】、；‘’，。、]{0,20}$/", request_string('user_password'))) {
                $this->data->setError('密码必须数字英文字母及符号组合');

                return false;
            }
        }
        if (strlen(request_string('user_password'))< $reg_row['reg_pwdlength']['config_value']) {
            $this->data->setError('密码最小长度为'.$reg_row['reg_pwdlength']['config_value'].'位');
            return false;

        }
        $data['password'] = md5(request_string('user_password'));
        $data['passworderp'] = request_string('user_password');
        $reg_checkcode = request_string('reg_checkcode', '1');
        //为erp做的修改密码
        if ($from == 'erp' || $from == 'chain') {
            $data['user'] = request_string('user_account');
            $User_InfoModel = new User_InfoModel();
            //检测登录状态
            $user_id_row = $User_InfoModel->getInfoByName($data['user']);
            if ($user_id_row) {
                //重置密码
                $user_id = $user_id_row['user_id'];
                $reset_passwd_row = [];
                $reset_passwd_row['password'] = $from == 'erp' ? $data['passworderp']:$data['password'];
                $flag = $User_InfoModel->editInfo($user_id, $reset_passwd_row);
                if ($flag !== false) {
                    $msg = '重置密码成功';
                    $status = 200;
                } else {
                    $msg = '重置密码失败';
                    $status = 250;
                }
            } else {
                $msg = '用户不存在';
                $status = 250;
            }
            unset($data['password']);
        } else {
           
                if ($reg_checkcode == 1 || $reg_checkcode == 3) {
                    if (!$data['mobile']) {
                        $this->data->setError('手机号不能为空');

                        return false;
                    }
                } else {
                    if (!$data['email']) {
                        $this->data->setError('邮箱不能为空');

                        return false;
                    }
                }
                if (request_string('user_password')) {
                    $config_cache = Yf_Registry::get('config_cache');
                    $Cache_Lite = new Cache_Lite_Output($config_cache['verify_code']);
                    if ($reg_checkcode == 2) {
                        $user_code_pre = $Cache_Lite->get($data['email'], 'verify_code');
                    } else {
                        $user_code_pre = $Cache_Lite->get($data['mobile'], 'verify_code');
                    }
                    //$user_code_pre = $reg_checkcode == 1 ? $Cache_Lite->get($data['mobile']) : $Cache_Lite->get($data['email']);
                   
                        $User_InfoModel = new User_InfoModel();
                        $User_InfoDetailModel = new User_InfoDetailModel();
                        //检测登录状态
                        if ($reg_checkcode == 2) {
                            $data['user'] = $User_InfoDetailModel->getUserByEmail($data['email']);
                        } else {
                            $data['user'] = $User_InfoDetailModel->getUserByMobile($data['mobile']);
                        }
                        //$data['user'] = $reg_checkcode == 1 ? $User_InfoDetailModel->getUserByMobile($data['mobile']) : $User_InfoDetailModel->getUserByEmail($data['email']);
                        $user_id_row = $User_InfoModel->getInfoByName($data['user']);
                        if ($user_id_row) {
                            //重置密码
                            $user_id = $user_id_row['user_id'];
                            $reset_passwd_row = [];
                            $reset_passwd_row['password'] = $data['password'];
                            $flag = $User_InfoModel->editInfo($user_id, $reset_passwd_row);
                            if ($flag === 'false') {
                                $msg = '网路故障，请稍后重试';
                                $status = 250;
                            } else {
                                $msg = '重置密码成功';
                                $status = 200;
                                //使验证码失效
                                if ($reg_checkcode == 2) {
                                    $reg_checkcode == $Cache_Lite->remove($data['email']);
                                } else {
                                    $reg_checkcode == $Cache_Lite->remove($data['mobile']);
                                }
                                //$reg_checkcode == 1 ? $Cache_Lite->remove($data['mobile']) : $Cache_Lite->remove($data['email']);
                            }
                        } else {
                            $msg = '用户不存在';
                            $status = 250;
                        }
                    
                } else {
                    $msg = '密码不能为空';
                    $status = 250;
                }
            
            unset($data['password']);
        }
        $this->data->addBody(-140, ['user_code' => $user_code, 'user_code_pre' => $user_code_pre], $msg, $status);
    }
    
    public function wxappYzCode(){
        $user_code = request_int('user_code');
        $mobile = request_int('mobile');
        $config_cache = Yf_Registry::get('config_cache');
        $Cache_Lite = new Cache_Lite_Output($config_cache['verify_code']);
        $user_code_pre = $Cache_Lite->get($mobile,'verify_code');
        if ($user_code != $user_code_pre) {
            if (DEBUG !== true) {
                $user_code_pre = "";
            }
            $this->data->setError('验证码错误');

            return false;
        }else{
             $this->data->addBody(-140, array());
        }
        
    }
    
    public function bindphone(){
        
        include $this->view->getView();
    }
    
    public function setpwd(){
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if ($reg_row['reg_number']['config_value'] == 1) {
           
                $str = '密码由'.$reg_row['reg_pwdlength']['config_value'].'-20位纯字数组成';
    
        }
        if ($reg_row['reg_lowercase']['config_value'] == 1) {
            

                $str = '密码由'.$reg_row['reg_pwdlength']['config_value'].'-20位纯英文字母组成，字母需区分大小写';
            
        }
        if ($reg_row['reg_uppercase']['config_value'] == 1) {
           
               
                $str = '密码由'.$reg_row['reg_pwdlength']['config_value'].'-20位数字及字母组成，字母需区分大小写';
            
        }
        if ($reg_row['reg_symbols']['config_value'] == 1) {
                $str = '密码由'.$reg_row['reg_pwdlength']['config_value'].'-20位字母、数字或半角符号组成，不能是纯数字/字母/半角符号，字母需区分大小写';
            
        }
       

        include $this->view->getView();
        
    }

     //小程序手机验证登陆
    public function wxappphoneLogin()
    {
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $bind_id = '';
        $user_info_row = [];
        $token = request_string('t');
        $type = request_int('type');
        $mobile = request_string('user_phone');
        $phone_code = request_string('phone_code');
        $bind_id = sprintf('mobile_%s', $mobile);

        if (!$mobile) {
            $this->data->setError('手机号不能为空');
            return false;
        }

        if (!$phone_code) {
            $this->data->setError('手机验证码不能为空');
            return false;
        }

        $config_cache = Yf_Registry::get('config_cache');
        $Cache_Lite = new Cache_Lite_Output($config_cache['verify_code']);
        $user_code_pre = $Cache_Lite->get($mobile,'verify_code');
        if ($phone_code != $user_code_pre) {
            if (DEBUG !== true) {
                $user_code_pre = "";
            }
            $this->data->setError('验证码错误');

            return false;
        }

        if ($bind_id) {
            //查找bind绑定表
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_info = $User_BindConnectModel->getOne($bind_id);
            if ($bind_info) {
                //用户名登录
                $user_info_row = $User_InfoModel->getOne($bind_info['user_id']);
            }
        }

        if (!$user_info_row) {
            //调用注册
            $arr_body = $this->newRegThis($mobile);
            //$this->data->setError('手机号未绑定账号');
            if($arr_body){
                $arr_body['mobile'] = $mobile;
                return $this->data->addBody(-140, $arr_body,'',201);
            }else{
                return $this->data->addBody(-140, array(), __('登录失败'), 250);
            }
            
        }

        if (3 == $user_info_row['user_state']) {
            $this->data->setError('用户已经锁定,禁止登录!');
            return false;
        }

        $session_id = $user_info_row['session_id'];
        $arr_field = [];
        $arr_field['session_id'] = $session_id;

        $info_row = $User_InfoDetail->getOne($user_info_row['user_name']);
        if (!$info_row) {
            $this->data->setError('账号信息有误', $user_info_row, 210);
            return false;
        }
        //判断是否为有效门店账号
        if ($user_info_row && $user_info_row['user_id'] != 0) {
            $key = Yf_Registry::get('shop_api_key');
            $url = Yf_Registry::get('shop_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');
            $formvars = [];
            $formvars['app_id'] = $shop_app_id;
            $formvars['user_id'] = $user_info_row['user_id'];
            $chain_flag = get_url_with_encrypt($key, sprintf('%s?ctl=Login&met=check_store&typ=json', $url), $formvars);
            if ($chain_flag['data']['flag']) {
                $this->data->setError('该门店账号已关闭', $chain_flag, 250);
                return false;
            }
        }

        $arr_body = $user_info_row;
        $arr_body['mobile'] = $info_row['user_mobile'];
        $arr_body['area_code'] = $info_row['area_code'];
        $arr_body['result'] = 1;
        //$arr_body['session_id'] = $session_id;
        $data = [];
        $data['user_id'] = $user_info_row['user_id'];
        //$data['session_id'] = $session_id;
        $encrypt_str = Perm::encryptUserInfo($data, $session_id);
        $arr_body['k'] = $encrypt_str;
        //插入绑定表
        if ($token) {
            $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
            $bind_info = $bind_info[0];
            //插入绑定表
            $time = date('Y-m-d H:i:s', time());
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_array = [
                'user_id' => $user_info_row['user_id'],
                'bind_time' => $time,
                'bind_token' => $token,
            ];
            $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
        }
        $arr_field_user_info_detail['user_count_login'] = $info_row['user_count_login'] + 1;
        $arr_field_user_info_detail['user_lastlogin_time'] = time();
        $User_InfoDetail->editInfoDetail($user_name, $arr_field_user_info_detail);

        $this->data->addBody(100, $arr_body);

        if ($jsonp_callback = request_string('jsonp_callback')) {
            exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
        }

    }

     //小程序获取登录验证码
    public function wxapploginCode()
    {
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $check_code = mt_rand(100000, 999999);
        if ($mobile && Yf_Utils_String::isMobile($mobile)) {
            //判断手机号是否已经注册过
            $User_InfoDetail = new User_InfoDetailModel();
           
            $save_result = $this->_saveCodeCache($mobile, $check_code, 'verify_code');
            if (!$save_result) {
                $msg = _('发送失败');
                $status = 250;
            } else {
                //发送短消息
                $message_model = new Message_TemplateModel();
                $pattern = ['/\[weburl_name\]/', '/\[yzm\]/'];
                $replacement = [Web_ConfigModel::value("site_name"), $check_code];
                $message_info = $message_model->getTemplateInfo(['code' => 'regist_verify'], $pattern, $replacement);
                if (!$message_info['is_phone']) {
                    $this->data->addBody(-140, [], _('信息内容创建失败'), 250);
                }
                $contents = $message_info['content_phone'];
                $result = Sms::send($mobile,$area_code, $contents, $message_info['baidu_tpl_id'],['weburl_name'=>Web_ConfigModel::value("site_name"),'yzm'=>$check_code]);
                if ($result) {
                    $msg = _('发送成功');
                    $status = 200;
                } else {
                    $msg = _('发送失败');
                    $status = 250;
                }
            }
            
        } else {
            $msg = __('发送失败');
            $status = 250;
        }
        $data = [];
        //if (DEBUG === false) {
        $data['user_code'] = $check_code;
        //}

        return $this->data->addBody(-140, $data, $message_info, 200);
    }

    public function bindRegistNew()
    {
        $token = request_string('token');
        $user_code = request_string('code');
        $mobile = request_string('mobile');
        //$password = request_string('password');
        $type = request_string('type');
        if($type=='wap_wx'){
            //验证验证码
            
            if (!VerifyCode::checkCode($mobile, $user_code)) {
                $data = [];
                return $this->data->addBody(-140, $data, __('手机验证码错误'), 250);
            }
        }
        $server_id = 0;
      
        if (!$mobile) {
            $this->data->setError('请输入手机号');

            return false;
        }
      
       
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
        $user_info_detail = $User_InfoDetail->getOneByWhere(array('user_mobile'=>$mobile));
        $user_info = $User_InfoModel ->getOneByWhere(array('user_name'=>$user_info_detail['user_name']));
        //如果手机号已经存在，直接绑定
        if($user_info_detail){
             //在绑定表中插入用户id
            $bind_user_row = [];
            $time = date('Y-m-d H:i:s', time());
            $bind_user_row['user_id'] = $user_info['user_id'];
            $bind_user_row['bind_time'] = $time;
            $bind_user_row['bind_token'] = $token;
            $bind_edit_flag = $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_user_row);
            check_rs($bind_edit_flag, $rs_row);
            $session_id = $user_info['session_id'];
            $user_id = $user_info['user_id'];
        }else{
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
            //$arr_field_user_info['password'] = md5($password);
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
            //添加mobile绑定.
            //绑定标记：mobile/email/openid  绑定类型+openid
            $bind__mobile_id = sprintf('mobile_%s', $mobile);
            //查找bind绑定表
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_mobile_info = $User_BindConnectModel->getOne($bind__mobile_id);
            if (!$bind_mobile_info) {
                $time = date('Y-m-d H:i:s', time());
                //插入绑定表
                $bind_array = [
                    'bind_id' => $bind__mobile_id,
                    'user_id' => $user_id,
                    'bind_type' => $User_BindConnectModel::MOBILE,
                    'bind_time' => $time
                ];
                $flag = $User_BindConnectModel->addBindConnect($bind_array);
                check_rs($flag, $rs_row);
                array_push($rs_row, $flag);
                //绑定关系
                $arr_field_user_info_detail['user_mobile_verify'] = 1;
            } else {
                //针对之前的历史数据处理。之前已经解绑的手机号在bind_connect表中还是存在，给之后用该手机号绑定用户造成了困扰
                $time = date('Y-m-d H:i:s', time());
                //插入绑定表
                $bind_array = [
                    'user_id' => $user_id,
                    'bind_type' => $User_BindConnectModel::MOBILE,
                    'bind_time' => $time
                ];
                $flag = $User_BindConnectModel->editBindConnect($bind__mobile_id, $bind_array);
                check_rs($flag, $rs_row);
                array_push($rs_row, $flag);
                //绑定关系
                $arr_field_user_info_detail['user_mobile_verify'] = 1;
            }
            $arr_field_user_info_detail['user_name'] = $bind_info['bind_nickname'];
            $arr_field_user_info_detail['user_mobile'] = $mobile;
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

        }
        $user_rows = $User_InfoModel->getOne($user_id);
        if (is_ok($rs_row) && $User_BindConnectModel->sql->commit()) {
            $d = [];
            $d['user_id'] = $user_id;
            $encrypt_str = Perm::encryptUserInfo($d, $session_id);
            $arr_body = [
                "user_name" => $bind_info['bind_nickname'],
                "server_id" => $server_id,
                "k" => $encrypt_str,
                "session_id" => $session_id,
                "user_id" => $user_id,
                "password" => $user_rows['password']?$user_rows['password']:'',
            ];
            $this->data->addBody(100, $arr_body);
        } else {
            $User_BindConnectModel->sql->rollBack();
            $this->data->setError('创建用户信息失败');
        }
    }

     //手机号登陆获取验证码
    public function getMobileCodeNew()
    {
        $mobile = request_string('mobile');
        $area_code = request_string('area_code')?:86;
        $login_type = request_string('login_type');
        $User_InfoDetailModel = new User_InfoDetailModel();
        $user_info = $User_InfoDetailModel->getOneByWhere(array('user_mobile'=>$mobile));
        if($login_type=='reg'&&$user_info){
            //注册，检测手机号是否注册
            return $this->data->setError('该手机号已注册');
            
        }
        if($login_type=='reset'&&!$user_info){
            //重置密码，检测手机号是否注册
            return $this->data->setError('该手机号未注册');
        }
        $cond_row['code'] = request_string('type') == 'passwd' ? 'edit_passwd':'verification';
       
        if (!$mobile||$mobile=='') {
          
            return $this->data->setError('请输入手机号');
        }
        $Message_TemplateModel = new Message_TemplateModel();
        $de = $Message_TemplateModel->getTemplateDetail($cond_row);
        $me = $de['content_phone'];
        $code_key = $mobile;
        $code = VerifyCode::getCode($code_key);
        $me = str_replace("[weburl_name]", $this->web['web_name'], $me);
        $me = str_replace("[yzm]", $code, $me);
        $str = Sms::send($mobile,$area_code, $me, $de['baidu_tpl_id'],['weburl_name'=>$this->web['web_name'],'yzm'=>$code]);
        $status = $str ? 200:250;
        $msg = $str ? _('发送成功'):_('发送失败');
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $code;
        }
        $data['user_code'] = $code;
        return $this->data->addBody(-140, $data, $msg, $status);
    }

    //手机验证登陆
    public function phoneLoginNew()
    {
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $bind_id = '';
        $user_info_row = [];
        $token = request_string('t');
        $type = request_int('type');
        $mobile = request_string('user_phone');
        $phone_code = request_string('phone_code');
        $bind_id = sprintf('mobile_%s', $mobile);

        if (!$mobile) {
            $this->data->setError('手机号不能为空');
            return false;
        }

        if (!$phone_code) {
            $this->data->setError('手机验证码不能为空');
            return false;
        }

        if (!VerifyCode::checkCode($mobile, $phone_code)) {
            $data = [];
            return $this->data->addBody(-140, $data, __('手机验证码错误'), 250);
        }
        if ($bind_id) {
            //查找bind绑定表
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_info = $User_BindConnectModel->getOne($bind_id);
            if ($bind_info) {
                //用户名登录
                $user_info_row = $User_InfoModel->getOne($bind_info['user_id']);
            }
        }

        if (!$user_info_row) {
            //调用注册
            $arr_body = $this->newRegThis($mobile);
            //$this->data->setError('手机号未绑定账号');
            if($arr_body){
                return $this->data->addBody(-140, $arr_body);
            }else{
                return $this->data->addBody(-140, $data, __('登录失败'), 250);
            }
            
        }
        if (3 == $user_info_row['user_state']) {
            $this->data->setError('用户已经锁定,禁止登录!');
            return false;
        }

        $session_id = $user_info_row['session_id'];
        $arr_field = [];
        $arr_field['session_id'] = $session_id;

        $info_row = $User_InfoDetail->getOne($user_info_row['user_name']);
        if (!$info_row) {
            $this->data->setError('账号信息有误', $user_info_row, 210);
            return false;
        }
        //判断是否为有效门店账号
        if ($user_info_row && $user_info_row['user_id'] != 0) {
            $key = Yf_Registry::get('shop_api_key');
            $url = Yf_Registry::get('shop_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');
            $formvars = [];
            $formvars['app_id'] = $shop_app_id;
            $formvars['user_id'] = $user_info_row['user_id'];
            $chain_flag = get_url_with_encrypt($key, sprintf('%s?ctl=Login&met=check_store&typ=json', $url), $formvars);
            if ($chain_flag['data']['flag']) {
                $this->data->setError('该门店账号已关闭', $chain_flag, 250);
                return false;
            }
        }

        $arr_body = $user_info_row;
        $arr_body['mobile'] = $info_row['user_mobile'];
        $arr_body['area_code'] = $info_row['area_code'];
        $arr_body['result'] = 1;
        //$arr_body['session_id'] = $session_id;
        $data = [];
        $data['user_id'] = $user_info_row['user_id'];
        //$data['session_id'] = $session_id;
        $encrypt_str = Perm::encryptUserInfo($data, $session_id);
        $arr_body['k'] = $encrypt_str;
        //插入绑定表
        if ($token) {
            $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
            $bind_info = $bind_info[0];
            //插入绑定表
            $time = date('Y-m-d H:i:s', time());
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_array = [
                'user_id' => $user_info_row['user_id'],
                'bind_time' => $time,
                'bind_token' => $token,
            ];
            $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
        }
        $arr_field_user_info_detail['user_count_login'] = $info_row['user_count_login'] + 1;
        $arr_field_user_info_detail['user_lastlogin_time'] = time();
        $User_InfoDetail->editInfoDetail($user_name, $arr_field_user_info_detail);

        $this->data->addBody(100, $arr_body);

        if ($jsonp_callback = request_string('jsonp_callback')) {
            exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
        }

    }

    public function newRegThis($mobile)
    {
         
    
        $user_name = $mobile;
        //$user_code = request_string('user_code');
        $mobile = $mobile;
        //本地读取远程信息
        //只能只能只能使用分享链接注册新账号功能（闭环）
        /*$key = Yf_Registry::get('shop_api_key');;
        $url         = Yf_Registry::get('shop_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars           = $_POST;
        $formvars['app_id'] = $shop_app_id;
        $fenxiao_data = get_url_with_encrypt($key, sprintf('%s?ctl=Api_%s&met=%s&typ=json', $url, 'Fenxiao', 'getStatus'), $formvars);
        if($fenxiao_data['data']['status'] == 1){
            if(empty($_COOKIE['uu_id']))
            {
                throw new Exception("请使用分享链接注册新账号");
            }
        }*/

        /** 此功能不完善，不能支持手机版和客户端，所以关掉
        $option_value_row = request_row('option');
        $Reg_OptionModel = new Reg_OptionModel();
        $reg_opt_rows = $Reg_OptionModel->getByWhere(['reg_option_active' => 1]);
        foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
        if ($reg_opt_row['reg_option_required']) {
        if ('' == $option_value_row[$reg_option_id]) {
        $this->data->setError('请输入' . $reg_opt_row['reg_option_name']);

        return false;
        }
        }
        }
         * **/
        //$user_code = request_string('user_code');
        //$mobile = request_string('mobile');
        //$token = request_string('t');
        //$app_id = request_int('app_id');
        $area_code = 86;
        $reg_checkcode = 1;
        //$shop_id_wap = request_int('shop_id_wap');


        $server_id = 0;
        $Web_ConfigModel = new Web_ConfigModel();
        $reg_row = $Web_ConfigModel->getByWhere(['config_type' => 'register']);
        if (!$user_name) {
            $this->data->setError('请输入账号');

            return false;
        }
        // if (!$password) {
        //     $this->data->setError('请输入密码');

        //     return false;
        // }
        if (VerifyCode::checkCode($mobile, $user_code)) {
            $rs_row = [];
            //用户是否存在
            $User_InfoModel = new User_InfoModel();
            $User_InfoDetail = new User_InfoDetailModel();
            $user_rows = $User_InfoDetail->checkUserName($user_name);
            $user_name_info = $User_InfoModel->getInfoByName($user_name);
            $user_info_detail = $User_InfoDetail->getOneByWhere(array('user_mobile'=>$mobile));
            if ($user_rows || $user_name_info||$user_info_detail) {
                $this->data->setError('用户已经存在,请更换用户名!');

                return false;
            } else {
                $User_InfoModel->sql->startTransaction();
                $Db = Yf_Db::get('ucenter');
                $seq_name = 'user_id';
                $user_id = $Db->nextId($seq_name);

//              $User_InfoModel->check_input($user_name, $password, $user_mobile);
                $now_time = time();
                $ip = get_ip();
                $session_id = uniqid();
                $arr_field_user_info = [];
                $arr_field_user_info['user_id'] = $user_id;
                $arr_field_user_info['user_name'] = $user_name;
                $arr_field_user_info['password'] = md5('a52936521b7502091d9853a180f07b66');
                $arr_field_user_info['action_time'] = $now_time;
                $arr_field_user_info['action_ip'] = $ip;
                $arr_field_user_info['session_id'] = $session_id;
                $flag = $User_InfoModel->addInfo($arr_field_user_info);
                array_push($rs_row, $flag);
                $arr_field_user_info_detail = [];
                //添加mobile绑定.
                //绑定标记：mobile/email/openid  绑定类型+openid
                $bind_id = $reg_checkcode == 1 ? sprintf('mobile_%s', $mobile):sprintf('email_%s', $email);
                //查找bind绑定表
                $User_BindConnectModel = new User_BindConnectModel();
                $bind_info = $User_BindConnectModel->getOne($bind_id);
                if (!$bind_info) {
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'bind_id' => $bind_id,
                        'user_id' => $user_id,
                        'bind_type' => $reg_checkcode == 1 ? $User_BindConnectModel::MOBILE:$User_BindConnectModel::EMAIL,
                        'bind_time' => $time
                    ];
                    $flag = $User_BindConnectModel->addBindConnect($bind_array);
                    array_push($rs_row, $flag);
                    //绑定关系
                    if ($reg_checkcode == 1) {
                        $arr_field_user_info_detail['user_mobile_verify'] = 1;
                    } else {
                        $arr_field_user_info_detail['user_email_verify'] = 1;
                    }
                }
                $arr_field_user_info_detail['user_name'] = $user_name;
                if ($reg_checkcode == 1) {
                    $arr_field_user_info_detail['user_mobile'] = $mobile;
                    $arr_field_user_info_detail['area_code'] = $area_code;
                } else {
                    $arr_field_user_info_detail['user_email'] = $email;
                }
                //$arr_field_user_info_detail['user_mobile_verify']         = 1;
                $arr_field_user_info_detail['user_reg_time'] = $now_time;
                $arr_field_user_info_detail['user_count_login'] = 1;
                $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
                $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
                $arr_field_user_info_detail['user_reg_ip'] = $ip;
                $arr_field_user_info_detail['user_is_shop'] = $shop_id_wap;
                $arr_field_user_info_detail['user_avatar'] = Web_ConfigModel::value('user_default_avatar', Yf_Registry::get('static_url') . '/images/default_user_portrait.png');
               
                $flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
                array_push($rs_row, $flag);
                $User_OptionModel = new User_OptionModel();
                foreach ($reg_opt_rows as $reg_option_id => $reg_opt_row) {
                    if (isset($option_value_row[$reg_option_id])) {
                        $reg_option_value_row = explode(',', $reg_opt_row['reg_option_value']);
                        $user_option_row = [];
                        $user_option_row['reg_option_id'] = $reg_option_id;
                        $user_option_row['reg_option_value_id'] = $option_value_row[$reg_option_id];
                        $user_option_row['user_id'] = $user_id;
                        $user_option_row['user_option_value'] = isset($reg_option_value_row[$option_value_row[$reg_option_id]]) ? $reg_option_value_row[$option_value_row[$reg_option_id]]:$option_value_row[$reg_option_id];
                        $flag = $User_OptionModel->addOption($user_option_row);
                        array_push($rs_row, $flag);
                    }
                }
            }
       

            if (is_ok($rs_row) && $User_InfoDetail->sql->commit()) {
                $d = [];
                $d['user_id'] = $user_id;
                $encrypt_str = Perm::encryptUserInfo($d, $session_id);
                $arr_body = [
                    "user_name" => $user_name,
                    "server_id" => $server_id,
                    "k" => $encrypt_str,
                    "user_id" => $user_id,
                    "session_id"=>$session_id,
                    'password'=>$arr_field_user_info['password']
                ];
                if ($token) {
                    //查找bind绑定表
                    $User_BindConnectModel = new User_BindConnectModel();
                    $bind_info = $User_BindConnectModel->getBindConnectByToken($token);
                    $bind_info = $bind_info[0];
                    //获取qq缩略头像
                    $qq_logo = substr($bind_info['bind_avator'], 0, strrpos($bind_info['bind_avator'], '/'));
                    $qq_logo = $qq_logo . '/40';
                    //更新用户详情表
                    if ($bind_info['bind_gender'] == 1) {
                        $gender = 1;
                    } else {
                        $gender = 0;
                    }
                    $user_info_detail = [
                        'nickname' => $bind_info['bind_nickname'],
                        'user_avatar' => $bind_info['bind_avator'],
                        'user_gender' => $gender,
                        'user_avatar_thumb' => $qq_logo,
                    ];
                    $User_InfoDetail->editInfoDetail($user_name, $user_info_detail);
                    $time = date('Y-m-d H:i:s', time());
                    //插入绑定表
                    $bind_array = [
                        'user_id' => $user_id,
                        'bind_time' => $time,
                        'bind_token' => $token,
                    ];
                    $User_BindConnectModel->editBindConnect($bind_info['bind_id'], $bind_array);
                }
            
                return $arr_body;
            } else {
                $User_InfoDetail->sql->rollBack();
                $this->data->setError('创建用户信息失败');
            }
        } else {
            $msg = $code_from . '验证码错误';
            $status = 250;
            if (DEBUG !== true) {
                $user_code = "";
            }

            return $this->data->addBody(-1, ['code' => $user_code,'debug'=>DEBUG], $msg, $status);
        }
    }
	
	/**
	*
	* 插入登录失败记录
	*
	* @nsy 2020-06-05
	**/
	private function err_login_add(Yf_Model $mdl ,String $account){
		$ip = get_ip();
		$type = 1;
		$login_time = time();
		$sql = "insert into ucenter_login_limit (`account`,`type` ,`login_time`,`ip`) 
		values ('{$account}',{$type},{$login_time},'{$ip}')";
		$int =  $mdl->sql->exec($sql);
		return $int;
	}

    /**
     * 验证手机号码的合法性
     * 
     * @dateTime  2020-08-14
     * @author fzh
     * @version   3.8.1
     */
    private static function is_mobile_number($mobile){
        $preg = "/^1[3456789]\d{9}/";
        if (preg_match($preg, $mobile)){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 第三方中酷快速注册
     * 
     * @dateTime  2020-08-14
     * @author fzh
     * @version   3.8.1
     */
    public function thirdQuick(){
        //参数接收
        $user_name = request_string('user_mobile');  //用户手机号
        $password = 'uid123456';
        $password = md5($password);
        $mobile = $user_name;
        $bolean = true;
        $status = 200;

        //参数验证
        if (empty($mobile)) {
            $date['status_code'] = 2;
           return $this->data->addBody(-140, $date,'手机号码不能为空', 250);
        }else{
           $bolean = self::is_mobile_number($mobile);
        }
        if (!$bolean) {
           $date['status_code'] = 3;
           return $this->data->addBody(-140, $date,'手机号码格式不正确', 250);
        }
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $User_Info_name = $User_InfoModel->getOneByWhere(array("user_name"=>$user_name));
        if ($User_Info_name) {
            $status = 250;
            $msg = '该手机用户已存在，创建用户信息失败！';
            $date['status_code'] = 4;
            return $this->data->addBody(-140, $date, $msg, $status);
        } else {
            // 注册一个会员信息
            $server_id = 0;
            $rs_row = array();
            $User_InfoModel->sql->startTransaction();
            $Db = Yf_Db::get('ucenter');
            $seq_name = 'user_id';
            $user_id = $Db->nextId($seq_name);
            $now_time = time();
            $ip = get_ip();
            $session_id = uniqid();
            $arr_field_user_info = array();
            $arr_field_user_info['user_name'] = $mobile;
            $arr_field_user_info['action_time'] = $now_time;
            $arr_field_user_info['action_ip'] = $ip;
            $arr_field_user_info['session_id'] = $session_id;
            $arr_field_user_info['u_id'] = '';
            $user_id = $User_InfoModel->addInfo($arr_field_user_info,true);
            $arr_field_user_info_detail = array();
            //添加mobile绑定.
            //绑定标记：mobile/email/openid  绑定类型+openid
            $bind_id = sprintf('mobile_%s', $mobile);
            //查找bind绑定表
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_info = $User_BindConnectModel->getOne($bind_id);
            if (!$bind_info) {
                $time = date('Y-m-d H:i:s', time());
                //插入绑定表
                $bind_array = array(
                    'bind_id' => $bind_id,
                    'user_id' => $user_id,
                    'bind_type' => $User_BindConnectModel::MOBILE,
                    'bind_time' => $time
                );
                $flag = $User_BindConnectModel->addBindConnect($bind_array);
                array_push($rs_row, $flag);
            }
            $arr_field_user_info_detail['user_mobile_verify'] = "1";
            $arr_field_user_info_detail['user_name'] = $mobile;
            $arr_field_user_info_detail['nickname'] = $user_name;
            $arr_field_user_info_detail['user_mobile'] = $mobile;
            $arr_field_user_info_detail['user_tel'] = $mobile;
            $arr_field_user_info_detail['user_reg_time'] = $now_time;
            $arr_field_user_info_detail['user_count_login'] = 1;
            $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
            $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
            $arr_field_user_info_detail['user_reg_ip'] = $ip;
            $arr_field_user_info_detail['user_avatar'] = '';
            $flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
            array_push($rs_row, $flag);
            if (is_ok($rs_row) && $User_InfoModel->sql->commit()) {
                $d = array();
                $d['user_id'] = $user_id;
                $encrypt_str = Perm::encryptUserInfo($d, $session_id);
                $date['status_code'] = 1;
                $date['user_name'] = $user_name;
                return $this->data->addBody(-140, $date, "创建用户信息成功", 200);
            } else {
                $date['status_code'] = 5;
                $User_InfoModel->sql->rollBack();
                return $this->data->addBody(-140,$date, "创建用户信息失败！", 250);
            }
        }
        
    }

    /**
     * 第三方中酷快速注册
     * 
     * @dateTime  2020-08-14
     * @author fzh
     * @version   3.8.1
     */
    public function Quick(){
        //参数接收
        $user_name = request_string('user_mobile');  //用户手机号
        $uid = request_int('u_id');  //奥通用户id
        $password = 'uid123456';
        $password = md5($password);
        $mobile = $user_name;
        $bolean = true;
        $status = 200;

        //参数验证
        if (empty($mobile)) {
           return $this->data->addBody(-140, array(),'手机号码不能为空', 250);
        }else{
           $bolean = self::is_mobile_number($mobile);
        }
        if (!$bolean) {
           return $this->data->addBody(-140, array(),'手机号码格式不正确', 250);
        }
        if (empty($uid) || !is_integer($uid)) {
             return $this->data->addBody(-140, array(),'奥通用户id不正确', 250);
        }
        $User_InfoModel = new User_InfoModel();
        $User_InfoDetail = new User_InfoDetailModel();
        $User_Info_name = $User_InfoModel->getOneByWhere(array("user_name"=>$user_name));
        if ($User_Info_name) {
            $edit_User_Info = $User_InfoModel->editInfo($User_Info_name['user_id'],array("u_id"=>$uid));
            if ($edit_User_Info) {
                $status = 200;
                $msg = "success";
            } else {
                $status = 250;
                $msg = '创建用户信息失败';
            }
            return $this->data->addBody(-140, array(), $msg, $status);
        } else {
            // 注册一个会员信息
            $server_id = 0;
            $rs_row = array();
            $User_InfoModel->sql->startTransaction();
            $Db = Yf_Db::get('ucenter');
            $seq_name = 'user_id';
            $user_id = $Db->nextId($seq_name);
            $now_time = time();
            $ip = get_ip();
            $session_id = uniqid();
            $arr_field_user_info = array();
            $arr_field_user_info['user_name'] = $mobile;
            $arr_field_user_info['action_time'] = $now_time;
            $arr_field_user_info['action_ip'] = $ip;
            $arr_field_user_info['session_id'] = $session_id;
            $arr_field_user_info['u_id'] = $uid;
            $user_id = $User_InfoModel->addInfo($arr_field_user_info,true);
            $arr_field_user_info_detail = array();
            //添加mobile绑定.
            //绑定标记：mobile/email/openid  绑定类型+openid
            $bind_id = sprintf('mobile_%s', $mobile);
            //查找bind绑定表
            $User_BindConnectModel = new User_BindConnectModel();
            $bind_info = $User_BindConnectModel->getOne($bind_id);
            if (!$bind_info) {
                $time = date('Y-m-d H:i:s', time());
                //插入绑定表
                $bind_array = array(
                    'bind_id' => $bind_id,
                    'user_id' => $user_id,
                    'bind_type' => $User_BindConnectModel::MOBILE,
                    'bind_time' => $time
                );
                $flag = $User_BindConnectModel->addBindConnect($bind_array);
                array_push($rs_row, $flag);
            }
            $arr_field_user_info_detail['user_mobile_verify'] = "1";
            $arr_field_user_info_detail['user_name'] = $mobile;
            $arr_field_user_info_detail['nickname'] = $user_name;
            $arr_field_user_info_detail['user_mobile'] = $mobile;
            $arr_field_user_info_detail['user_tel'] = $mobile;
            $arr_field_user_info_detail['user_reg_time'] = $now_time;
            $arr_field_user_info_detail['user_count_login'] = 1;
            $arr_field_user_info_detail['user_lastlogin_time'] = $now_time;
            $arr_field_user_info_detail['user_lastlogin_ip'] = $ip;
            $arr_field_user_info_detail['user_reg_ip'] = $ip;
            $arr_field_user_info_detail['user_avatar'] = '';
            $flag = $User_InfoDetail->addInfoDetail($arr_field_user_info_detail);
            array_push($rs_row, $flag);
            if (is_ok($rs_row) && $User_InfoModel->sql->commit()) {
                $d = array();
                $d['user_id'] = $user_id;
                $encrypt_str = Perm::encryptUserInfo($d, $session_id);
                $arr_body = array(
                    "user_name" => $user_name
                );
                return $this->data->addBody(-140, $arr_body);
            } else {
                $User_InfoModel->sql->rollBack();
                return $this->data->setError('创建用户信息失败');
            }
        }
        
    }
}

?>
