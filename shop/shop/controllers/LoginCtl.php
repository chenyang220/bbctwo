<?php

class LoginCtl extends Yf_AppController
{
    public function index()
    {
        include $this->view->getView();
    }
    
    /*
     * 检测登录数据是否正确
     *
     *
     */
    public function login()
    {
        if (!Perm::checkUserPerm()) {
            $login_url = Yf_Registry::get('ucenter_api_url') . '?ctl=Login&met=index&typ=e';
            $reg_url = Yf_Registry::get('ucenter_api_url') . '?ctl=Login&met=regist&typ=e';
            $findpwd_url = Yf_Registry::get('ucenter_api_url') . '?ctl=Login&met=findpwd&typ=e';
            $callback = Yf_Registry::get('url') . '?ctl=Login&met=check&typ=e&redirect=' . urlencode(request_string('forward'));
            $login_url = $login_url . '&from=shop&callback=' . urlencode($callback);
            if (is_array(getenv())) {
                setcookie('comeUrl', getenv()['HTTP_REFERER']);
            } else {
                setcookie('comeUrl', getenv('HTTP_REFERER'));
            }
            header('location:' . $login_url);
            exit();
        } else {
            header('location:' . Yf_Registry::get('url'));
        }
    }
    
    /*
     * 检测登录数据是否正确
     *
     *
     */
    public function reg()
    {
        if (!Perm::checkUserPerm()) {
            $login_url = Yf_Registry::get('ucenter_api_url') . '?ctl=Login&met=index&typ=e';
            $reg_url = Yf_Registry::get('ucenter_api_url') . '?ctl=Login&met=regist&typ=e';
            $findpwd_url = Yf_Registry::get('ucenter_api_url') . '?ctl=Login&met=findpwd&typ=e';
            $callback = Yf_Registry::get('url') . '?ctl=Login&met=check&typ=e&redirect=' . urlencode(request_string('forward'));
            $login_url = $reg_url . '&from=shop&callback=' . urlencode($callback);
            header('location:' . $login_url);
            exit();
        } else {
            header('location:' . Yf_Registry::get('url'));
        }
    }
    
    /*
     * 检测登录数据是否正确
     *
     *
     */
    public function check()
    {
        //本地读取远程信息
        $key = Yf_Registry::get('ucenter_api_key');;
        $url = Yf_Registry::get('ucenter_api_url');
        $app_id = Yf_Registry::get('ucenter_app_id');
        $redirect = request_string('redirect');
        $fenxiao_uuid = request_string('fenxiao_uuid');
        $User_BaseModel = new User_BaseModel();
        $User_InfoModel = new User_InfoModel();
        $Points_LogModel = new Points_LogModel();
        $formvars = [];
        $formvars['user_id'] = request_int('us');
        $formvars['u'] = request_int('us');
        $formvars['k'] = request_string('ks');
        $formvars['app_id'] = $app_id;
        $url = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Login', 'checkLogin', 'json');
        $init_rs = get_url_with_encrypt($key, $url, $formvars);
        if (200 == $init_rs['status']) {
            //读取服务列表
            $user_row = $init_rs['data'];
            $user_id = $user_row['user_id'];
            $user_name = $user_row['user_name'];
            //本地数据校验登录
            $user_row = $User_BaseModel->getOne($user_id);
            if ($user_row) {
                //判断状态是否开启
                if ($user_row['user_delete'] == 1) {
                    $msg = __('该账户未启用，请启用后登录！');
                    if ('e' == $this->typ) {
                        location_go_back(__('初始化用户出错1!'));
                    } else {
                        return $this->data->setError($msg, []);
                    }
                }
                 /**
                 * 处理商城用户登陆成功后用户名空白，IM点击无反应问题(波涛装饰客户遇到类似问题,快速处理方案)
                 * 备注：遇到此问题，打开下方注释代码
                 * @nsy 2019-06-13
                 * */
                /*if(!$user_row['user_account']) {
                    $user_row['user_account'] = $user_name;
                    $update_base_sql = "update " . $User_BaseModel->_tableName . " set user_account = '{$user_name}' where user_id='{$user_id}'";
                    $User_BaseModel->sql->query($update_base_sql);
                    $User_InfoModel = new User_InfoModel();
                    $update_info_sql = "update " . $User_InfoModel->_tableName . " set user_name = '{$user_name}' where user_id='{$user_id}'";
                    $User_InfoModel->sql->query($update_info_sql);
                }*/
            } else {
                //添加用户
                //$data['user_id']       = $user_row['user_id']; // 用户id
                //$data['user_account']  = $user_row['user_name']; // 用户帐号
//var_dump($init_rs);die;
                $data['user_id'] = $init_rs['data']['user_id']; // 用户id
                $data['user_account'] = $init_rs['data']['user_name']; // 用户帐号
                $data['user_delete'] = 0; // 用户状态
                $user_id = $User_BaseModel->addBase($data, true);
                //判断状态是否开启
                if (!$user_id) {
                    $msg = __('初始化用户出错2!') . $init_rs['data']['user_id'] . $init_rs['data']['user_name'] . $user_id;
                    if ('e' == $this->typ) {
                        location_go_back(__('初始化用户出错3!'));
                    } else {
                        return $this->data->setError($msg, $data);
                    }
                } else {
                    $user_reg_ip = $init_rs['data']['user_reg_ip'];
                    if($user_reg_ip){
                        $district_model = new Base_DistrictModel();
                        // //取第一个ip获取准确地理位置
                        $url = 'http://ip.ws.126.net/ipquery?ip='.$user_reg_ip;
                        $ret = @file_get_contents($url);
                        $ret = iconv("gb2312", "utf-8//IGNORE",$ret);
                        !$ret && $ret = '';
                        $pattern = '#"(.*?)"#i';
                        preg_match_all($pattern, $ret, $matches);
                        $addr = array_unique($matches[1]);
                        list($prov,$city) =  $addr;
                        if($prov && $city){
                            $diz['province'] = mb_substr($prov,0,-1,'utf-8');
                            $data['city'] = $city;
                        }
                        $district = $district_model->getOneByWhere(array('district_name'=>$city));
                    }
                    //初始化用户信息
                    $user_info_row = [];
                    $user_info_row['user_id'] = $user_id;
                    $user_info_row['user_realname'] = @$init_rs['data']['user_truename'];
                    $user_info_row['user_name'] = @$data['user_account'];
                    $user_info_row['user_mobile'] = @$init_rs['data']['user_mobile'];
                    $user_info_row['area_code'] = @$init_rs['data']['area_code'] ? :86;
                    $user_info_row['user_logo'] = @$init_rs['data']['user_avatar'];
                    $user_info_row['user_regtime'] = get_date_time();
                    if($_COOKIE['SHOP_ID']){
                        $user_info_row['user_is_shop']=$_COOKIE['SHOP_ID'];
                    }
                    if($init_rs['data']['user_is_shop']){
                        $user_info_row['user_is_shop']=$init_rs['data']['user_is_shop'];
                    }                    $user_info_row['district_id'] = $district['district_id'];
                    $User_InfoModel = new User_InfoModel();
                    $info_flag = $User_InfoModel->addInfo($user_info_row);

                    //默认勾选,接收规定的5个信息,发货通知,付款成功提醒，商品咨询回复提醒，退款退货提醒，平台客服回复提醒
                    $messageSettingModel = new Message_SettingModel();
                    $cond_row            = array();
                    $cond_row['user_id'] = $user_id;
                    $re  = $messageSettingModel->getSettingDetail($cond_row);
                    if (!$re){
                        $cond_row['message_template_all']   = implode(",", array(6,7,1,8,26));
                        $cond_row['setting_time']           = get_date_time();

                        $messageSettingModel->addSetting($cond_row);
                    }

                    //wap端分享+分销
                    if (Web_ConfigModel::value('Plugin_Fenxiao')) {
                        if ($_COOKIE['uu_id']) {
                            Fenxiao::getInstance()->addUserRelationship($user_id, $_COOKIE['uu_id']);
                        }
                        if ($fenxiao_uuid) {
                            Fenxiao::getInstance()->addUserRelationship($user_id, $fenxiao_uuid);
                        }
                        if ($_COOKIE['yf_recuserparentid']) {
                            Fenxiao::getInstance()->addUserRelationship($user_id, $_COOKIE['yf_recuserparentid']);
                        }
                    }
                    if (Web_ConfigModel::value('Plugin_Directseller')) {
                        if ($_COOKIE['uu_id']) {
                            $PluginManager = Yf_Plugin_Manager::getInstance();
                            $PluginManager->trigger('regDone', $user_id);
                        }
                        
                        if ($_COOKIE['yf_recserialize']) {
                            //regDone
                            $PluginManager = Yf_Plugin_Manager::getInstance();
                            $PluginManager->trigger('regDone', $user_id);
                        }

                        if ($_COOKIE['yf_recuserparentid']) {
                            //regDone
                            $PluginManager = Yf_Plugin_Manager::getInstance();
                            $PluginManager->trigger('regDone', $user_id);
                        }
                    }
                    $user_resource_row = [];
                    $user_resource_row['user_id'] = $user_id;
                    $user_resource_row['user_points'] = Web_ConfigModel::value("points_reg");//注册获取积分;
                    $User_ResourceModel = new User_ResourceModel();
                    $res_flag = $User_ResourceModel->addResource($user_resource_row);
                    $User_PrivacyModel = new User_PrivacyModel();
                    $user_privacy_row['user_id'] = $user_id;
                    $privacy_flag = $User_PrivacyModel->addPrivacy($user_privacy_row);
                    //积分
                    $user_points_row['user_id'] = $user_id;
                    $user_points_row['user_name'] = $data['user_account'];
                    $user_points_row['class_id'] = Points_LogModel::ONREG;
                    $user_points_row['points_log_points'] = $user_resource_row['user_points'];
                    $user_points_row['points_log_time'] = get_date_time();
                    $user_points_row['points_log_desc'] = __('会员注册');
                    $user_points_row['points_log_flag'] = 'reg';
                    $Points_LogModel->addLog($user_points_row);
                    //发送站内信
                    $message = new MessageModel();
                    $message->sendMessage('welcome', $user_id, $data['user_account'], '', '', 0, MessageModel::OTHER_MESSAGE);
                    /**
                     *  统计中心
                     * shop的注册人数
                     */
                    $analytics_ip = isset($init_rs['data']['user_reg_ip']) ? $init_rs['data']['user_reg_ip']:get_ip();
                    $analytics_data = [
                        'user_name' => $data['user_account'],  //用户账号
                        'user_id' => $user_id,
                        'ip' => $analytics_ip,
                        'date' => date('Y-m-d H:i:s')
                    ];
                    Yf_Plugin_Manager::getInstance()->trigger('analyticsMemberAdd', $analytics_data);
                    /******************************************************/
                }
                $user_row = $data;
            }
            if ($user_row) {
                /*//分销
                if(Web_ConfigModel::value('Plugin_Directseller'))
                {
                    if($_COOKIE['yf_recserialize'])
                    {
                        //regDone
                        $PluginManager = Yf_Plugin_Manager::getInstance();
                        $PluginManager->trigger('regDone',$user_id);
                    }
                }*/
                $data = [];
                $data['user_id'] = $user_row['user_id'];
                srand((double)microtime() * 1000000);
                //$user_key = md5(rand(0, 32000));
                $user_key = $init_rs['data']['session_id'];
                $time = get_date_time();
                //获取上次登录的时间
                $info = $User_BaseModel->getBase($user_row['user_id']);
                $lotime = strtotime($info[$user_row['user_id']]['user_login_time']);
                $last_day = date("d ", $lotime);
                $now_day = date("d ");
                $now = time();
                $login_info_row = [];
                $login_info_row['user_key'] = $user_key;
                $login_info_row['user_login_time'] = $time;
                $login_info_row['user_login_times'] = $info[$user_row['user_id']]['user_login_times'] + 1;
                $login_info_row['user_login_ip'] = get_ip();
                $flag = $User_BaseModel->editBase($user_row['user_id'], $login_info_row, false);
                $login_row['user_logintime'] = $time;
                $login_row['lastlogintime'] = $info[$user_row['user_id']]['user_login_time'];
                $login_row['user_ip'] = get_ip();
                $login_row['user_lastip'] = $info[$user_row['user_id']]['user_login_ip'];
                $flag = $User_InfoModel->editInfo($user_row['user_id'], $login_row, false);


                //默认勾选,接收规定的5个信息,发货通知,付款成功提醒，商品咨询回复提醒，退款退货提醒，平台客服回复提醒
                $messageSettingModel = new Message_SettingModel();
                $cond_row            = array();
                $cond_row['user_id'] = $user_id;
                $re  = $messageSettingModel->getSettingDetail($cond_row);
                if (!$re){
                    $cond_row['message_template_all']   = implode(",", array(6,7,1,8,26));
                    $cond_row['setting_time']           = get_date_time();

                    $messageSettingModel->addSetting($cond_row);
                }

                //当天没有登录过执行
                if ($last_day != $now_day && $now > $lotime) {
                    $user_points = Web_ConfigModel::value("points_login");
                    $user_grade = Web_ConfigModel::value("grade_login");
                    $User_ResourceModel = new User_ResourceModel();
                    //获取当前登录的积分经验值
                    $ce = $User_ResourceModel->getResource($user_row['user_id']);
                    $resource_row['user_points'] = $ce[$user_row['user_id']]['user_points'] * 1 + $user_points * 1;
                    $resource_row['user_growth'] = $ce[$user_row['user_id']]['user_growth'] * 1 + $user_grade * 1;
                    $res_flag = $User_ResourceModel->editResource($user_row['user_id'], $resource_row);
                    $User_GradeModel = new User_GradeModel;
                    //升级判断
                    $res_flag = $User_GradeModel->upGrade($user_row['user_id'], $resource_row['user_growth']);
                    //积分
                    $points_row['user_id'] = $user_id;
                    $points_row['user_name'] = $user_row['user_account'];
                    $points_row['class_id'] = Points_LogModel::ONLOGIN;
                    $points_row['points_log_points'] = $user_points;
                    $points_row['points_log_time'] = $time;
                    $points_row['points_log_desc'] = __('会员登录');
                    $points_row['points_log_flag'] = 'login';
                    $Points_LogModel = new Points_LogModel();
                    $Points_LogModel->addLog($points_row);
                    //成长值
                    $grade_row['user_id'] = $user_id;
                    $grade_row['user_name'] = $user_row['user_account'];
                    $grade_row['class_id'] = Grade_LogModel::ONLOGIN;
                    $grade_row['grade_log_grade'] = $user_grade;
                    $grade_row['grade_log_time'] = $time;
                    $grade_row['grade_log_desc'] = __('会员登录');
                    $grade_row['grade_log_flag'] = 'login';
                    $Grade_LogModel = new Grade_LogModel;
                    $Grade_LogModel->addLog($grade_row);
                }
                //$flag     = $User_BaseModel->editBaseSingleField($user_row['user_id'], 'user_key', $user_key, $user_row['user_key']);
                Yf_Hash::setKey($user_key);
                //
                $Seller_BaseModel = new Seller_BaseModel();
                $seller_rows = $Seller_BaseModel->getByWhere(['user_id' => $data['user_id']]);
                $Chain_UserModel = new Chain_UserModel();
                $chain_rows = $Chain_UserModel->getByWhere(['user_id' => $data['user_id']]);
                if ($chain_rows) {
                    $data['chain_id_row'] = array_column($chain_rows, 'chain_id');
                    $data['chain_id'] = current($data['chain_id_row']);
                } else {
                    $data['chain_id'] = 0;
                }
                if ($seller_rows) {
                    $data['shop_id_row'] = array_column($seller_rows, 'shop_id');
                    $data['shop_id'] = current($data['shop_id_row']);
                } else {
                    $data['shop_id'] = 0;
                }
                //user_account 这个COOKIE IM是需要的。by sunkang
                $data['user_account'] = $user_row['user_account'];
                $encrypt_str = Perm::encryptUserInfo($data);
                /////
                //更新购物车
                $cartlist = [];
                if (isset($_COOKIE['goods_cart'])) {
                    $cartlist = $_COOKIE['goods_cart'];
                }
                if ($cartlist) {
                    $CartModel = new CartModel();
                    $CartModel->updateCookieCart($data['user_id']);
                }
                if (isset($_COOKIE['goods_cart'])) {
                    setcookie("goods_cart", null, time() - 1, '/');
                }
                if ('e' == $this->typ) {
                    if ($redirect) {
                        location_to(urldecode($redirect));
                    } else {
                        if ($_COOKIE['comeUrl']) {
                            location_to($_COOKIE['comeUrl']);
                        } elseif ($chain_rows) {
                            location_to(Yf_Registry::get('url') . '?ctl=Chain_Goods&met=goods&typ=e');
                        } else {
                            //location_to(Yf_Registry::get('base_url') . "/error.php?msg=您的帐号不是门店帐号");
                            location_to(Yf_Registry::get('base_url'));
                        }
                    }
                } else {
                    $data = [];
                    $data['user_id'] = $user_row['user_id'];
                    $data['user_account'] = $user_row['user_account'];
                    $data['key'] = $encrypt_str;
                    $this->data->addBody(100, $data);
                }
            } else {
                $msg = __('登录出错！');
                if ('e' == $this->typ) {
                    location_go_back($msg);
                } else {
                    return $this->data->setError($msg, []);
                }
            }
        } else {
            $msg = __('登录信息有误！');
            if ('e' == $this->typ) {
                location_go_back($msg);
            } else {
                return $this->data->setError($msg, []);
            }
        }
        if ($jsonp_callback = request_string('jsonp_callback')) {
            exit($jsonp_callback . '(' . json_encode($this->data->getDataRows()) . ')');
        }
    }
    

     //第三方查找查看用户等级
    public function userGradeApi(){
        $u_id = request_int('u_id');
        $sql = "select * from  ucenter_user_info where `u_id`=" . $u_id;
        $db = new YFSQL();
        $data = $db->find($sql);
        if (empty($data)) {
            return $this->data->addBody(-140, array(), "你查找的用户不存在！", $status);
        }
        $user_info_row = current($data);


        $User_InfoModel = new  User_InfoModel();
        $User_Info = $User_InfoModel->getOne($user_info_row['user_id']);
        //会员等级
        $User_GradeModel = new User_GradeModel();
        $grade = $User_GradeModel->getOne($User_Info['user_grade']);
        $User_Info['user_grade_name'] = $grade['user_grade_name'];

        if(empty($grade)){
            $staus = 250;
            $msg ="查找失败！";
        }else{
            $status = 200;
            $msg ="查看成功";
        }
        $this->data->addBody(-140, $User_Info, $msg, $status);
    }


    /**
     * 用户登录,通过本站输入用户名密码登录
     *
     * @access public
     */
    public function doLogin()
    {
        $Points_LogModel = new Points_LogModel();
        $User_BaseModel = new User_BaseModel();
        $User_InfoModel = new User_InfoModel();
        $user_account = $_REQUEST['user_account'];
        //本地读取远程信息
        $key = Yf_Registry::get('ucenter_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
        $formvars = [];
        $formvars['user_account'] = $_REQUEST['user_account'];
        $formvars['user_password'] = $_REQUEST['user_password'];
        $formvars['app_id'] = $ucenter_app_id;
        $formvars['ctl'] = 'Login';
        $formvars['met'] = 'login';
        $formvars['typ'] = 'json';
        $init_rs = get_url_with_encrypt($key, $url, $formvars);
        if (200 == $init_rs['status']) {
            //读取服务列表
        } else {
            $msg = __('登录信息有误');
            if ('e' == $this->typ) {
                location_go_back($msg);
            } else {
                return $this->data->setError($msg, []);
            }
        }
        $userBaseModel = new User_BaseModel();
        //本地数据校验登录
        $user_id_row = $userBaseModel->getUserIdByAccount($user_account);
        if ($user_id_row) {
            $user_rows = $userBaseModel->getBase($user_id_row);
            $user_row = array_pop($user_rows);
            //判断状态是否开启
            if ($user_row['user_delete'] == 1) {
                $msg = __('该账户未启用，请启用后登录！');
                if ('e' == $this->typ) {
                    location_go_back($msg);
                } else {
                    return $this->data->setError($msg, []);
                }
            }
            //fb($user_row);
        } else {
            $user_row = $init_rs['data'];
            //添加用户
            $data['user_id'] = $user_row['user_id']; // 用户id
            $data['user_account'] = $user_row['user_name']; // 用户帐号
            $data['user_passwd'] = $user_row['password']; // 密码：使用用户中心-此处废弃
            $data['user_delete'] = 0; // 用户状态
            $user_id = $userBaseModel->addBase($data, true);
            //初始化用户信息
            $user_info_row = [];
            $user_info_row['user_id'] = $user_id;
            $user_info_row['user_realname'] = @$init_rs['data']['user_truename'];
            $user_info_row['user_name'] = isset($init_rs['data']['nickname']) ? $init_rs['data']['nickname']:$data['user_account'];
            $user_info_row['user_mobile'] = @$init_rs['data']['user_mobile'];
            $user_info_row['user_logo'] = @$init_rs['data']['user_avatar'];
            $user_info_row['user_regtime'] = get_date_time();
            $info_flag = $User_InfoModel->addInfo($user_info_row);
            $user_resource_row = [];
            $user_resource_row['user_id'] = $user_id;
            $user_resource_row['user_points'] = Web_ConfigModel::value("points_reg");//注册获取积分;
            $User_ResourceModel = new User_ResourceModel();
            $res_flag = $User_ResourceModel->addResource($user_resource_row);
            $User_PrivacyModel = new User_PrivacyModel();
            $user_privacy_row['user_id'] = $user_id;
            $privacy_flag = $User_PrivacyModel->addPrivacy($user_privacy_row);
            //积分
            $user_points_row['user_id'] = $user_id;
            $user_points_row['user_name'] = $data['user_account'];
            $user_points_row['class_id'] = Points_LogModel::ONREG;
            $user_points_row['points_log_points'] = $user_resource_row['user_points'];
            $user_points_row['points_log_time'] = get_date_time();
            $user_points_row['points_log_desc'] = __('会员注册');
            $user_points_row['points_log_flag'] = 'reg';
            $Points_LogModel->addLog($user_points_row);
            //发送站内信
            $message = new MessageModel();
            $message->sendMessage('welcome', $user_id, $data['user_account'], '', '', 0, MessageModel::OTHER_MESSAGE);
            //判断状态是否开启
            if (!$user_id) {
                $msg = __('初始化用户出错44！');
                if ('e' == $this->typ) {
                    location_go_back($msg);
                } else {
                    return $this->data->setError($msg, []);
                }
            }
            /**
             *  统计中心
             * shop的注册人数
             */
            $analytics_ip = isset($init_rs['data']['user_reg_ip']) ? $init_rs['data']['user_reg_ip']:get_ip();
            $analytics_data = [
                'user_name' => $data['user_account'],  //用户账号
                'user_id' => $user_id,
                'ip' => $analytics_ip,
                'date' => date('Y-m-d H:i:s')
            ];
            Yf_Plugin_Manager::getInstance()->trigger('analyticsMemberAdd', $analytics_data);
            /******************************************************/
        }
        //if ($user_id_row && ($user_row['user_password'] == md5($_REQUEST['user_password'])))
        if ($user_row) {
            $data = [];
            $data['user_id'] = $user_row['user_id'];
            srand((double)microtime() * 1000000);
            //$user_key = md5(rand(0, 32000));
            $user_key = $init_rs['data']['session_id'];
            $time = get_date_time();
            //获取上次登录的时间
            $info = $User_BaseModel->getBase($user_row['user_id']);
            $lotime = strtotime($info[$user_row['user_id']]['user_login_time']);
            $last_day = date("d ", $lotime);
            $now_day = date("d ");
            $now = time();
            $login_info_row = [];
            $login_info_row['user_key'] = $user_key;
            $login_info_row['user_login_time'] = $time;
            $login_info_row['user_login_times'] = $info[$user_row['user_id']]['user_login_times'] + 1;
            $login_info_row['user_login_ip'] = get_ip();
            $flag = $User_BaseModel->editBase($user_row['user_id'], $login_info_row, false);
            $login_row['user_logintime'] = $time;
            $login_row['lastlogintime'] = $info[$user_row['user_id']]['user_login_time'];
            $login_row['user_ip'] = get_ip();
            $login_row['user_lastip'] = $info[$user_row['user_id']]['user_login_ip'];
            $flag = $User_InfoModel->editInfo($user_row['user_id'], $login_row, false);
            //当天没有登录过执行
            if ($last_day != $now_day && $now > $lotime) {
                $user_points = Web_ConfigModel::value("points_login");
                $user_grade = Web_ConfigModel::value("grade_login");
                $User_ResourceModel = new User_ResourceModel();
                //获取当前登录的积分经验值
                $ce = $User_ResourceModel->getResource($user_row['user_id']);
                $resource_row['user_points'] = $ce[$user_row['user_id']]['user_points'] * 1 + $user_points * 1;
                $resource_row['user_growth'] = $ce[$user_row['user_id']]['user_growth'] * 1 + $user_grade * 1;
                $res_flag = $User_ResourceModel->editResource($user_row['user_id'], $resource_row);
                $User_GradeModel = new User_GradeModel;
                //升级判断
                $res_flag = $User_GradeModel->upGrade($user_row['user_id'], $resource_row['user_growth']);
                //积分
                $points_row['user_id'] = $user_id;
                $points_row['user_name'] = $user_row['user_account'];
                $points_row['class_id'] = Points_LogModel::ONLOGIN;
                $points_row['points_log_points'] = $user_points;
                $points_row['points_log_time'] = $time;
                $points_row['points_log_desc'] = __('会员登录');
                $points_row['points_log_flag'] = 'login';
                $Points_LogModel = new Points_LogModel();
                $Points_LogModel->addLog($points_row);
                //成长值
                $grade_row['user_id'] = $user_id;
                $grade_row['user_name'] = $user_row['user_account'];
                $grade_row['class_id'] = Grade_LogModel::ONLOGIN;
                $grade_row['grade_log_grade'] = $user_grade;
                $grade_row['grade_log_time'] = $time;
                $grade_row['grade_log_desc'] = __('会员登录');
                $grade_row['grade_log_flag'] = 'login';
                $Grade_LogModel = new Grade_LogModel;
                $Grade_LogModel->addLog($grade_row);
            }
            //$flag     = $userBaseModel->editBaseSingleField($user_row['user_id'], 'user_key', $user_key, $user_row['user_key']);
            Yf_Hash::setKey($user_key);
            $data['user_account'] = $formvars['user_account'] ? :$user_row['user_account'];
            $encrypt_str = Perm::encryptUserInfo($data);
            if ('e' == $this->typ) {
                location_to(Yf_Registry::get('base_url'));
            } else {
                $data['user_account'] = $formvars['user_account'];
                $data['key'] = $encrypt_str;
                $this->data->addBody(100, $data);
            }
        } else {
            $msg = __('输入密码有误！');
            if ('e' == $this->typ) {
                location_go_back($msg);
            } else {
                return $this->data->setError($msg, []);
            }
        }
        //权限设置
    }
    
    /**
     * 用户登录,通过本站输入用户名密码登录
     *
     * @access public
     */
    public function doRegister()
    {
        $Points_LogModel = new Points_LogModel();
        $User_BaseModel = new User_BaseModel();
        $User_InfoModel = new User_InfoModel();
        $user_account = $_REQUEST['user_account'];
        //本地读取远程信息
        $key = Yf_Registry::get('ucenter_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
        $formvars = [];
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
            $msg = __('登录信息有误');
            if ('e' == $this->typ) {
                location_go_back($msg);
            } else {
                return $this->data->setError($msg, []);
            }
        }
        $userBaseModel = new User_BaseModel();
        //本地数据校验登录
        $user_id_row = $userBaseModel->getUserIdByAccount($user_account);
        if ($user_id_row) {
            $user_rows = $userBaseModel->getBase($user_id_row);
            $user_row = array_pop($user_rows);
            //判断状态是否开启
            if ($user_row['user_delete'] == 1) {
                $msg = __('该账户未启用，请启用后登录！');
                if ('e' == $this->typ) {
                    location_go_back($msg);
                } else {
                    return $this->data->setError($msg, []);
                }
            }
            //fb($user_row);
        } else {
            $user_row = $init_rs['data'];
            //添加用户
            $data['user_id'] = $user_row['user_id']; // 用户id
            $data['user_account'] = $user_row['user_name']; // 用户帐号
            $data['user_password'] = $user_row['password']; // 密码：使用用户中心-此处废弃
            $data['user_delete'] = 0; // 用户状态
            $user_id = $userBaseModel->addBase($data, true);
            //初始化用户信息
            $user_info_row = [];
            $user_info_row['user_id'] = $user_id;
            $user_info_row['user_realname'] = @$init_rs['data']['user_truename'];
            $user_info_row['user_name'] = isset($init_rs['data']['nickname']) ? $init_rs['data']['nickname']:$data['user_account'];
            $user_info_row['user_mobile'] = @$init_rs['data']['user_mobile'];
            $user_info_row['area_code'] = @$init_rs['data']['area_code'] ? :86;
            $user_info_row['user_logo'] = @$init_rs['data']['user_avatar'];
            $user_info_row['user_regtime'] = get_date_time();
            $info_flag = $User_InfoModel->addInfo($user_info_row);
            $user_resource_row = [];
            $user_resource_row['user_id'] = $user_id;
            $user_resource_row['user_points'] = Web_ConfigModel::value("points_reg");//注册获取积分;
            $User_ResourceModel = new User_ResourceModel();
            $res_flag = $User_ResourceModel->addResource($user_resource_row);
            $User_PrivacyModel = new User_PrivacyModel();
            $user_privacy_row['user_id'] = $user_id;
            $privacy_flag = $User_PrivacyModel->addPrivacy($user_privacy_row);
            //积分
            $user_points_row['user_id'] = $user_id;
            $user_points_row['user_name'] = $data['user_account'];
            $user_points_row['class_id'] = Points_LogModel::ONREG;
            $user_points_row['points_log_points'] = $user_resource_row['user_points'];
            $user_points_row['points_log_time'] = get_date_time();
            $user_points_row['points_log_desc'] = __('会员注册');
            $user_points_row['points_log_flag'] = 'reg';
            $Points_LogModel->addLog($user_points_row);
            //发送站内信
            $message = new MessageModel();
            $message->sendMessage('welcome', $user_id, $data['user_account'], '', '', 0, MessageModel::OTHER_MESSAGE);
            //判断状态是否开启
            if (!$user_id) {
                $msg = __('初始化用户出错5！');
                if ('e' == $this->typ) {
                    location_go_back($msg);
                } else {
                    return $this->data->setError($msg, []);
                }
            }
        }
        //if ($user_id_row && ($user_row['user_password'] == md5($_REQUEST['user_password'])))
        if ($user_row) {
            $data = [];
            $data['user_id'] = $user_row['user_id'];
            srand((double)microtime() * 1000000);
            //$user_key = md5(rand(0, 32000));
            $user_key = $init_rs['data']['session_id'];
            $time = get_date_time();
            //获取上次登录的时间
            $info = $User_BaseModel->getBase($user_row['user_id']);
            $lotime = strtotime($info[$user_row['user_id']]['user_login_time']);
            $last_day = date("d ", $lotime);
            $now_day = date("d ");
            $now = time();
            $login_info_row = [];
            $login_info_row['user_key'] = $user_key;
            $login_info_row['user_login_time'] = $time;
            $login_info_row['user_login_times'] = $info[$user_row['user_id']]['user_login_times'] + 1;
            $login_info_row['user_login_ip'] = get_ip();
            $flag = $User_BaseModel->editBase($user_row['user_id'], $login_info_row, false);
            $login_row['user_logintime'] = $time;
            $login_row['lastlogintime'] = $info[$user_row['user_id']]['user_login_time'];
            $login_row['user_ip'] = get_ip();
            $login_row['user_lastip'] = $info[$user_row['user_id']]['user_login_ip'];
            $flag = $User_InfoModel->editInfo($user_row['user_id'], $login_row, false);
            //当天没有登录过执行
            if ($last_day != $now_day && $now > $lotime) {
                $user_points = Web_ConfigModel::value("points_login");
                $user_grade = Web_ConfigModel::value("grade_login");
                $User_ResourceModel = new User_ResourceModel();
                //获取当前登录的积分经验值
                $ce = $User_ResourceModel->getResource($user_row['user_id']);
                $resource_row['user_points'] = $ce[$user_row['user_id']]['user_points'] * 1 + $user_points * 1;
                $resource_row['user_growth'] = $ce[$user_row['user_id']]['user_growth'] * 1 + $user_grade * 1;
                $res_flag = $User_ResourceModel->editResource($user_row['user_id'], $resource_row);
                $User_GradeModel = new User_GradeModel;
                //升级判断
                $res_flag = $User_GradeModel->upGrade($user_row['user_id'], $resource_row['user_growth']);
                //积分
                $points_row['user_id'] = $user_id;
                $points_row['user_name'] = $user_row['user_account'];
                $points_row['class_id'] = Points_LogModel::ONLOGIN;
                $points_row['points_log_points'] = $user_points;
                $points_row['points_log_time'] = $time;
                $points_row['points_log_desc'] = __('会员登录');
                $points_row['points_log_flag'] = 'login';
                $Points_LogModel = new Points_LogModel();
                $Points_LogModel->addLog($points_row);
                //成长值
                $grade_row['user_id'] = $user_id;
                $grade_row['user_name'] = $user_row['user_account'];
                $grade_row['class_id'] = Grade_LogModel::ONLOGIN;
                $grade_row['grade_log_grade'] = $user_grade;
                $grade_row['grade_log_time'] = $time;
                $grade_row['grade_log_desc'] = __('会员登录');
                $grade_row['grade_log_flag'] = 'login';
                $Grade_LogModel = new Grade_LogModel;
                $Grade_LogModel->addLog($grade_row);
            }
            //$flag     = $userBaseModel->editBaseSingleField($user_row['user_id'], 'user_key', $user_key, $user_row['user_key']);
            Yf_Hash::setKey($user_key);
            $data['user_account'] = $formvars['user_account'] ? :$user_row['user_account'];
            $encrypt_str = Perm::encryptUserInfo($data);
            if ('e' == $this->typ) {
                location_to(Yf_Registry::get('base_url'));
            } else {
                $data['user_account'] = $formvars['user_account'];
                $data['key'] = $encrypt_str;
                $this->data->addBody(100, $data);
            }
        } else {
            $msg = __('输入密码有误！');
            if ('e' == $this->typ) {
                location_go_back($msg);
            } else {
                return $this->data->setError($msg, []);
            }
        }
        //权限设置
    }
    
    //获取注册密码
    public function regCode1()
    {
        //本地读取远程信息
        $key = Yf_Registry::get('ucenter_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
        $formvars = [];
        $formvars['user_account'] = $_REQUEST['user_account'];
        $formvars['user_password'] = $_REQUEST['user_password'];
        $formvars['app_id'] = $ucenter_app_id;
        $formvars['ctl'] = 'Api';
        $formvars['met'] = 'login';
        $formvars['typ'] = 'json';
        $init_rs = get_url_with_encrypt($key, $url, $formvars);
    }
    
    /*
     * 用户退出
     * IM
     *
     */
    public function loginout()
    {
        if ($_REQUEST['met'] == 'loginout') {
            if (isset($_COOKIE['key']) || isset($_COOKIE['id'])) {
                echo "<script>parent.location.href='index.php';</script>";
                setcookie("key", null, time() - 3600 * 24 * 365);
                setcookie("id", null, time() - 3600 * 24 * 365);
                setcookie("user_account", null, time() - 3600 * 24 * 365);
                setcookie("key", null, time() - 3600 * 24 * 365, '/');
                setcookie("id", null, time() - 3600 * 24 * 365, '/');
                setcookie("user_account", null, time() - 3600 * 24 * 365, '/');
                // 删除IM的cookie
                Perm::removeUserInfo();
            }
            $login_url = Yf_Registry::get('ucenter_api_url') . '?ctl=Login&met=logout&typ=e';
            $callback = Yf_Registry::get('url') . '?redirect=' . urlencode(Yf_Registry::get('url')) . '&type=ucenter';
            $login_url = $login_url . '&from=shop&callback=' . urlencode($callback);
            header('location:' . $login_url);
            exit();
        }
    }
    
    public function doLoginOut()
    {
        if (isset($_COOKIE['key']) || isset($_COOKIE['id'])) {
            echo "<script>parent.location.href='index.php';</script>";
            setcookie("key", null, time() - 3600 * 24 * 365);
            setcookie("id", null, time() - 3600 * 24 * 365);
        }
        $redirect = request_string('redirect');
        if ($redirect) {
            header('location:' . $redirect);
            exit();
        }
        /*//本地读取远程信息
        $key = Yf_Registry::get('ucenter_api_key');

        $url                       = Yf_Registry::get('ucenter_api_url');
        $ucenter_app_id            = Yf_Registry::get('ucenter_app_id');
        $formvars                  = array();
        $formvars['user_account']  = $_REQUEST['user_account'];
        $formvars['user_password'] = $_REQUEST['user_password'];
        $formvars['app_id']        = $ucenter_app_id;

        $formvars['ctl'] = 'Api';
        $formvars['met'] = 'loginout';
        $formvars['typ'] = 'json';
        $init_rs         = get_url_with_encrypt($key, $url, $formvars);

        $this->data->addBody(100, $init_rs);*/
    }
    
    //手机号登陆获取验证码
    public function getMobileCode()
    {
        $mobile = request_string('mobile');
        $area_code = request_string('area_code') ? :86;
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
        $str = Sms::send($mobile, $area_code, $me, $de['baidu_tpl_id'], ['weburl_name' => $this->web['web_name'], 'yzm' => $code]);
        $status = $str ? 200:250;
        $msg = $str ? _('发送成功'):_('发送失败');
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $code;
        }
        
        return $this->data->addBody(-140, $data, $msg, $status);
    }
    
    //手机验证登陆
    public function phoneLogin()
    {
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
        $key = Yf_Registry::get('ucenter_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
        $formvars = [];
        $formvars['app_id'] = $ucenter_app_id;
        $formvars['user_phone'] = $mobile;
        $formvars['phone_code'] = $phone_code;
        $money_row = get_url_with_encrypt($key, sprintf('%sindex.php?ctl=Login&met=shopPhoneLogin&typ=json', $url), $formvars);
        
        return $this->data->addBody(-140, $money_row['data'], $money_row['msg'], $money_row['status']);
    }
    
    function getYzm()
    {
        if (!Perm::$userId) {
            return $this->data->addBody(-140, [], '请登录', 250);
        }
        $mobile = request_string('mobile');
        $area_code = request_string('area_code') ? :86;
        $email = request_string('email');
        $yzm = request_string('yzm');
        $check_code = mt_rand(100000, 999999);
        if ($mobile) {
            if (!Yf_Utils_String::isMobile($mobile) && $area_code == 86) {
                return $this->data->addBody(-140, [], __('信息有误'), 250);
            }
            //判断手机号是否已经使用
            $Shop_CompanyModel = new Shop_CompanyModel();
            $checkmobile = $Shop_CompanyModel->getByWhere(['contacts_phone' => $mobile]);
            if (isset($checkmobile['items']) && $checkmobile['items']) {
                return $this->data->addBody(-140, [], __('该手机号已使用'), 250);
            }
            $save_result = $this->_saveCodeCache($mobile, $check_code, 'verify_code');
            if (!$save_result) {
                return $this->data->addBody(-140, [], __('验证码获取失败'), 250);
            }
            //发送短消息
            if (!Perm::checkYzm($yzm)) {
                return $this->data->addBody(-140, [], __('图形验证码有误'), 250);
            }
            //发送短消息
            $message_tpl_model = new Message_TemplateModel();
            $content_data = [
                '[weburl_name]' => Web_ConfigModel::value("site_name"),
                '[yzm]' => $check_code
            ];
            $result = $message_tpl_model->sendMessage($mobile, 'phone', 'shop_personal_settled', $content_data, $area_code);
            if (200 == $result['status']) {
                $msg = __('发送成功');
                $status = 200;
            } else {
                $msg = $result['msg'] ? __($result['msg']):__('发送失败');
                $status = 250;
            }
        } elseif ($email && Yf_Utils_String::isEmail($email)) {
            //判断邮箱是否已经注册过
            $Shop_CompanyModel = new Shop_CompanyModel();
            $checkemail = $Shop_CompanyModel->getByWhere(['contacts_email' => $email]);
            if (isset($checkemail['items']) && $checkemail['items']) {
                return $this->data->addBody(-140, [], __('该邮箱已被使用'), 250);
            }
            $save_result = $this->_saveCodeCache($email, $check_code, 'verify_code');
            if (!$save_result) {
                return $this->data->addBody(-140, [], __('验证码获取失败'), 250);
            }
            //发送邮件
            $message_tpl_model = new Message_TemplateModel();
            $content_data = [
                '[weburl_name]' => Web_ConfigModel::value("site_name"),
                '[yzm]' => $check_code
            ];
            $result = $message_tpl_model->sendMessage($email, 'email', 'shop_personal_settled', $content_data, $area_code);
            if ($result) {
                $msg = __('发送成功');
                $status = 200;
            } else {
                $msg = __('发送失败');
                $status = 250;
            }
        } else {
            $msg = __('信息有误');
            $status = 250;
        }
        $data = [];
        if (DEBUG === false) {
            $data['user_code'] = $check_code;
        }
        
        return $this->data->addBody(-140, $data, $msg, $status);
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
    private function _saveCodeCache($key, $value, $group = 'default')
    {
        $config_cache = Yf_Registry::get('config_cache');
        if (!file_exists($config_cache[$group]['cacheDir'])) {
            mkdir($config_cache[$group]['cacheDir'], 0777);
        }
        $Cache_Lite = new Cache_Lite_Output($config_cache[$group]);
        $result = $Cache_Lite->save($value, $key);
        
        return $result;
    }
    
    //判断登录账号是否为门店账号，并且门店是否开启
    public function check_store()
    {
        $user_id = request_int('user_id');
        $Chain_UserModel = new Chain_UserModel();
        $chain_user = $Chain_UserModel->getOneByWhere(['user_id' => $user_id]);
        if ($chain_user) {
            $chain_id = $chain_user['chain_id'];
            $Chain_BaseModel = new Chain_BaseModel();
            $chan_base = current($Chain_BaseModel->getBase($chain_id));
            if ($chan_base && $chan_base['status'] == 0) {
                $flag = true;
            } else {
                $flag = false;
            }
        } else {
            $flag = false;
        }
        $result = [
            'flag' => $flag
        ];
        $this->data->addBody(-140, $result, $chan_base, 200);
    }
    
    /**
     * 检查是否可以进入批发市场
     *
     * @return type
     */
    public function checkSuppierLogin()
    {
        if (!Perm::$userId) {
            return $this->data->addBody(-140, [], __('请先登录系统'), 250);
        }
        if (!Perm::$shopId) {
            return $this->data->addBody(-140, [], __('非商家用户不可进入批发市场'), 250);
        }
        
        return $this->data->addBody(-140, [], 'success', 200);
    }

    public function setUserParentCookie()
    {
        $id = request_int('id');

        $t = time();
        setcookie('yf_recuserparentid', $id, $t + 365 * 86400);
        $_COOKIE['yf_recuserparentid'] = $id;

        $this->data->addBody(-140, $_COOKIE, 'success', 200);
    }
    
    /**
     * 检查用户是否是管理员
     *
     * @return type
     */
    public function check_admin()
    {
        $uname  = request_string('user_name');
        $status = 250;
        $msg = __('failure');
        if($uname){
            $User_InfoModel = new User_InfoModel();
            $sql ="select count(1)ct from yf_admin_user_base where user_account='{$uname}'";
            $ret = $User_InfoModel->sql->getRow($sql);
            if($ret['ct']){
                $msg = __('success');
                $status = 200;
            }
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }
    public function editUserdistrict(){
        $User_InfoModel = new User_InfoModel();
        $district_model = new Base_DistrictModel();
        $sql ="select * from ucenter_user_info";
        $data = $User_InfoModel->sql->getAll($sql);
        foreach ($data as $key => $val) {
            if($val['action_ip']){
                // unset() 
                $city = '';
                // //取第一个ip获取准确地理位置
                $url = 'http://ip.ws.126.net/ipquery?ip='.$val['action_ip'];
                $ret = @file_get_contents($url);
                $ret = iconv("gb2312", "utf-8//IGNORE",$ret);
                !$ret && $ret = '';
                $pattern = '#"(.*?)"#i';
                preg_match_all($pattern, $ret, $matches);
                $addr = array_unique($matches[1]);
                list($prov,$city) =  $addr;
                if($prov && $city){
                    $diz['province'] = mb_substr($prov,0,-1,'utf-8');
                    $data['city'] = $city;
                }
                Yf_Log::log($url, Yf_Log::INFO, '33333');
                Yf_Log::log($city, Yf_Log::INFO, '33333');
                $district = array();
                $district = $district_model->getOneByWhere(array('district_name'=>$city));
                $user_info_row = array();
                $user_info_row['district_id'] = $district['district_id'];
                $flag = $User_InfoModel->editInfo($val['user_id'],$user_info_row);
                var_dump($user_info_row);
                var_dump($flag);
            }
        }
        die;
    }
    public function editOrderdistrict(){
        $Order_ReturnModel = new Order_ReturnModel();
        $data = $Order_ReturnModel->getByWhere();
        $Order_BaseModel = new Order_BaseModel();
        foreach ($data as $key => $val) {
            $order_base = $Order_BaseModel->getOne($val['order_number']);
            $Order_ReturnModel->editReturn($val['order_return_id'],array('district_id'=>$order_base['district_id']));
        }
    }
}

?>