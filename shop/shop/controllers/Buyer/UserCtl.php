<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}   
    /**
     * @author     Yf <service@yuanfeng.cn>
     */
    class Buyer_UserCtl extends Buyer_Controller
    {

         public $plusMdl;
        /**
         * Constructor
         *
         * @param  string $ctl 控制器目录
         * @param  string $met 控制器方法
         * @param  string $typ 返回数据类型
         *
         * @access public
         */
        public function __construct(&$ctl, $met, $typ)
        {
            parent::__construct($ctl, $met, $typ);
            
            $this->userInfoModel = new User_InfoModel();
            $this->userGradeModel = new User_GradeModel();
            $this->userResourceModel = new User_ResourceModel();
            $this->userAddressModel = new User_AddressModel();
            $this->userPrivacyModel = new User_PrivacyModel();
            $this->userBaseModel = new User_BaseModel();
            $this->userTagModel = new User_TagModel();
            $this->userTagRecModel = new User_TagRecModel();
            $this->userFriendModel = new User_FriendModel();
            $this->messageTemplateModel = new Message_TemplateModel();
            $this->goodsCommonModel = new Goods_CommonModel();
            $this->Information  = new Information_Base();
            $this->InformationNewsModel   = new Information_NewsModel();
            $this->InformationNewsClassModel = new Information_NewsClassModel();
            //plus 3.6.7
            $this->plusMdl =  new Plus_UserModel();
            $this->open_status = Web_ConfigModel::value('plus_switch')?:0;
        }
        
        /**
         * 会员信息--paycenter
         *
         * @access public
         */
        public function linkUserInfo()
        {
            
            $url = Yf_Registry::get('ucenter_api_url') . '?ctl=User&met=getUserInfo';
            location_to($url);
            die();
        }

        /**
         * 用户签到
         *
         * @access public
         */
        public function userInfoSign () {
            $user_id = Perm::$userId;
            $time = date('Y-m-d H:i:s',strtotime(date("Y-m-d"),time()));
            $Points_LogModel = new  Points_LogModel();
            $Points_Log = $Points_LogModel->getOneByWhere(array('user_id'=>$user_id,'points_log_time:>='=>$time,'class_id'=>9));
            if ($Points_Log) {
                //已签到
                $date['sign_satus'] = 1;
            } else {
                //未签到
                $User_InfoModel = new User_InfoModel();
                $User_InfoModel->sql->startTransactionDb();
                $User_Info = $User_InfoModel->getOne($user_id);
                $start_time  = date('Y-m-d H:i:s',strtotime(date("Y-m-d",strtotime("-1 day"))));
                $end_time  = date('Y-m-d H:i:s', strtotime(date("Y-m-d",strtotime("-1 day"))) + 24 * 60 * 60-1 );
                $Yesterday_Log = $Points_LogModel->getOneByWhere(array('user_id'=>$user_id,'points_log_time:>='=>$start_time,'points_log_time:<='=>$end_time,'class_id'=>9));
                if ($Yesterday_Log) {
                    if ($User_Info['user_sign_day'] <= 3) {
                        $points_log_points = 2;
                    } else if (3 < $User_Info['user_sign_day'] && $User_Info['user_sign_day'] <= 7) {
                        $points_log_points = 3;
                    } else if ($User_Info['user_sign_day'] > 7 && $User_Info['user_sign_day'] <= 30) {
                        $points_log_points = 5;
                    } else if ($User_Info['user_sign_day'] % 30 >= 1 && $User_Info['user_sign_day']  % 30 <= 7) {
                        $points_log_points = 10;
                    } else if ($User_Info['user_sign_day'] > 30  && ($User_Info['user_sign_day'] % 30 == 0  || $User_Info['user_sign_day']  % 30 > 7)) {
                        $points_log_points = 5;
                    }
                    $edit_User_Info['user_sign_day'] = $User_Info['user_sign_day'] + 1;
                    $User_Info_edit = $User_InfoModel->editInfo($user_id,$edit_User_Info);
                } else {
                    $points_log_points = 2;
                    $edit_User_Info['user_sign_day'] = 1;
                    $User_Info_edit = $User_InfoModel->editInfo($user_id,$edit_User_Info);
                }
                $User_ResourceModel = new  User_ResourceModel();
                $User_Resource = $User_ResourceModel->getOne($user_id);
                $editResourceRows['user_points'] =  $User_Resource['user_points'] + $points_log_points;
                $User_Resource_edit = $User_ResourceModel->editResource($user_id,$editResourceRows);
                $addLogRows['points_log_type'] = 1;
                $addLogRows['class_id'] = 9;
                $addLogRows['user_id'] = $user_id;
                $addLogRows['user_name'] = $User_Info['user_name'];
                $addLogRows['admin_name'] = 'admin';
                $addLogRows['points_log_points'] = $points_log_points;
                $addLogRows['freeze_points'] = 0;
                $addLogRows['points_log_time'] = date("Y-m-d H:i:s",time());
                $addLogRows['points_log_desc'] = "会员签到";
                $addLogRows['points_log_flag'] = 'sign';
                $Points_Log = $Points_LogModel->addLog($addLogRows);
                if ($User_Resource_edit && $Points_Log) {
                     $date['sign_satus'] = 1;
                    $User_InfoModel->sql->commitDb();
                } else {
                    $date['sign_satus'] = 0;
                    $User_InfoModel->sql->rollBackDb();
                }
            } 
            return $this->data->addBody(-140, $date);
        }



        /**
         *获取会员信息
         *
         * @access public
         */
        public function getUserInfo()
        {
            $user_id = Perm::$userId;
            //获取一级地址
            $district_parent_id = request_int('pid', 0);
            $baseDistrictModel = new Base_DistrictModel();
            $district = $baseDistrictModel->getDistrictTree($district_parent_id);
            
            $data = $this->userInfoModel->getInfo($user_id);
            $data = $data[$user_id];
            $time = date('Y-m-d H:i:s',strtotime(date("Y-m-d"),time()));
            $Points_LogModel = new  Points_LogModel();
            $Points_Log = $Points_LogModel->getOneByWhere(array('user_id'=>$user_id,'points_log_time:>='=>$time,'class_id'=>9));
            if ($Points_Log) {
                //已签到
                $data['sign_satus'] = "已签到";
            } else {
                $data['sign_satus'] = "签到";
            }
            //用户头像
            $data['user_logo']= Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$user_id;

            $privacy = $this->userPrivacyModel->getPrivacy($user_id);
            $privacy = $privacy[$user_id];
            
            if ('json' == $this->typ) {



                $data['district'] = $district;
                $data['privacy'] = $privacy;
                
                $data['shop_type'] = 0;
                $data['shop_id'] = Perm::$shopId;
                //wap端添加分销明细
                $Shop_BaseModel = new Shop_BaseModel();
                if (Perm::$shopId) {
                    $shop_base = $Shop_BaseModel->getOne(Perm::$shopId);
                    //开店成功
                    if ($shop_base['shop_status'] == Shop_BaseModel::SHOP_STATUS_OPEN) {
                        $data['shop_type'] = $shop_base['shop_type'];
                        $data['shop_wap_index'] = $shop_base['shop_wap_index'];
                    }
                } else {
                    $shop_info = $Shop_BaseModel->getByWhere(['user_id' => Perm::$userId]);
                    $arr = array_column($shop_info, 'shop_id', 'user_id');
                    $shop_id = $arr[Perm::$userId];
                    if ($shop_id) {
                        $shop_base = $Shop_BaseModel->getOne($shop_id);
                        //开店成功
                        if ($shop_base['shop_status'] == Shop_BaseModel::SHOP_STATUS_OPEN) {
                            $data['shop_type'] = $shop_base['shop_type'];
                            $data['shop_id'] = $shop_base['shop_id'];
                            $data['shop_wap_index'] = $shop_base['shop_wap_index'];
                        }
                    }
                }
                $User_FavoritesGoodsModel = new User_FavoritesGoodsModel();
                $User_FavoritesShopModel = new User_FavoritesShopModel();
                $User_FootprintModel = new User_FootprintModel();
                $Explore_UserModel = new Explore_UserModel();

                //用户发布心得数量
                $explore_base = $Explore_UserModel->getUser($user_id);
                $data['explore_base_count'] = $explore_base[$user_id]['explore_base_count']>0?$explore_base[$user_id]['explore_base_count']:0;
                $data['favorites_goods_num'] = $User_FavoritesGoodsModel->getFavoritesGoodsNum($user_id);
                $data['favorites_shop_num'] = $User_FavoritesShopModel->getFavoritesShopNum($user_id);
                //查找我的浏览足迹
                $data['footprint_goods_num'] = $User_FootprintModel->getFootprintGoodsNum( $user_id);
                $data['directseller_is_open'] = Web_ConfigModel::value('Plugin_Directseller');
                //是否开启分销插件，前端判断店铺类型为1
                $data['distribution_is_open'] = Web_ConfigModel::value('Plugin_Distribution');
                $data['fenxiao_is_open'] = Web_ConfigModel::value('Plugin_Fenxiao'); //新分销
                //获取用户积分
                $data['points'] = $this->userResourceModel->getOne($user_id);
                
                //获取当前用户余额信息
                $data['money'] = $this->userInfoModel->getBtInfo();

                //获取发现权限配置信息
                $sql = "
                    SELECT *
                    FROM `yf_web_config`
                    where
                    config_key  = 'exploreset'
                ";


                $db = new YFSQL();
                $list = $db->find($sql);

                if (!empty($list)) {
                    $data['find_type']  = $list[0]['config_value'];
                }
                //获取用户的订单信息
                $data['order_count'] = $this->userInfoModel->getUserOrderCount($user_id);

                //app分享二维码图片
                $user_id = Perm::$userId;
                $url = urlencode(Yf_Registry::get('url').'?ctl=Buyer_User&met=location_register&typ=e&uid='.$user_id);
                $qrCode = Yf_Registry::get('base_url').'/shop/api/qrcode.php?data='.$url;
                $data['qrCode'] = $qrCode;
                $data['share_icon'] = Yf_Registry::get('setting_logo');

                //添加plus会员信息
                $Plusday = '';
                $user =$this->plusMdl->getPlususerInfo($user_id);
                if($user){
                    switch ($user['user_status']) {
                        case '1':
                            $Plusday = ceil(($user['end_date']-time())/60/60/24);
                            break;
                        case '2':
                            $Plusday = date("Y年m月d日",$user['end_date']);
                            break;
                        case '3':
                            $Plusday =date("Y年m月d日",$user['end_date']) .__('已过期');
                            break;
                        default:
                            # code...
                            break;
                    }
                }
                $data['Plusday'] = $Plusday;
                $data['user'] = $user;
                $data['open_status'] = $this->open_status;

                //直播是否开始申请
                $data['live_is_open'] = Web_ConfigModel::value("live_is_open");
                $Live_ApplicationModel = new Live_ApplicationModel();
                $shop_live_info = $Live_ApplicationModel->getOneByWhere(array('shop_id' => $data['shop_id']));
                $data['shop_live_info'] = $shop_live_info;

                return $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        

        /**
         *获取会员信息 小陈程端
         *
         * @access public
         */
        public function getUserInfoWxapp()
        {
            $user_id = Perm::$userId;
            $shop_id = Perm::$shopId;
            $data = $this->userInfoModel->getOne($user_id);
            //用户头像
            $data['user_logo']= Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$user_id.'&random='.uniqid();
            //wap端添加分销明细
            $Shop_BaseModel = new Shop_BaseModel();
            if (!empty($shop_id)) {
                $shop_base = $Shop_BaseModel->getOne($shop_id);
                //开店成功
                if ($shop_base['shop_status'] == Shop_BaseModel::SHOP_STATUS_OPEN) {
                    $data['shop_type'] = $shop_base['shop_type'];
                    $data['shop_wap_index'] = $shop_base['shop_wap_index'];
                }
            } else {
                $shop_info = $Shop_BaseModel->getByWhere(['user_id' => Perm::$userId]);
                $arr = array_column($shop_info, 'shop_id', 'user_id');
                $shop_id = $arr[$user_id];
                if ($shop_id) {
                    $shop_base = $Shop_BaseModel->getOne($shop_id);
                    //开店成功
                    if ($shop_base['shop_status'] == Shop_BaseModel::SHOP_STATUS_OPEN) {
                        $data['shop_type'] = $shop_base['shop_type'];
                        $data['shop_wap_index'] = $shop_base['shop_wap_index'];
                    }
                }
            }

            $data['shop_id'] = $shop_id;
            $User_FavoritesGoodsModel = new User_FavoritesGoodsModel();
            $User_FavoritesShopModel = new User_FavoritesShopModel();
            $User_FootprintModel = new User_FootprintModel();
            $data['favorites_goods_num'] = $User_FavoritesGoodsModel->getFavoritesGoodsNum($user_id);
            $data['favorites_shop_num'] = $User_FavoritesShopModel->getFavoritesShopNum($user_id);
            //查找我的浏览足迹
            $data['footprint_goods_num'] = $User_FootprintModel->getFootprintGoodsNum( $user_id);
            $data['directseller_is_open'] = Web_ConfigModel::value('Plugin_Directseller');
            //是否开启分销插件，前端判断店铺类型为1
            $data['distribution_is_open'] = Web_ConfigModel::value('Plugin_Distribution');
            $data['fenxiao_is_open'] = Web_ConfigModel::value('Plugin_Fenxiao'); //新分销
            //获取用户的订单信息
            $data['order_count'] = $this->userInfoModel->getUserOrderCount($user_id);
            //直播是否开始申请
            $data['live_is_open'] = Web_ConfigModel::value("live_is_open");
            $Live_ApplicationModel = new Live_ApplicationModel();
            $shop_live_info = $Live_ApplicationModel->getOneByWhere(array('shop_id' => $data['shop_id']));
            $data['shop_live_info'] = $shop_live_info;
            $data['user_name'] = base64_decode($data['user_name']);
            return $this->data->addBody(-140, $data);
        }
        /**
         * 修改会员信息
         *
         * @access public
         */
        public function editUserInfo()
        {
            $user_id = Perm::$userId;
            
            $year = request_int('year');
            $month = request_int('month');
            $day = request_int('day');
            $user_qq = request_string('user_qq');
            $user_ww = request_string('user_ww');
            $rows = request_row('privacy');
            
            $edit_user_row['user_birthday'] = $year . "-" . $month . "-" . $day;
            $edit_user_row['user_sex'] = request_int('user_sex');
            $edit_user_row['user_realname'] = request_string('user_realname');
            $edit_user_row['user_provinceid'] = request_int('province_id');
            $edit_user_row['user_cityid'] = request_int('city_id');
            $edit_user_row['user_areaid'] = request_int('area_id');
            $edit_user_row['user_area'] = request_string('address_area');
            $edit_user_row['user_qq'] = $user_qq;
            $edit_user_row['user_ww'] = $user_ww;
            
            //开启事物
            $rs_row = array();
            $this->userInfoModel->sql->startTransactionDb();
            
            $flagPrivacy = $this->userPrivacyModel->editPrivacy($user_id, $rows);
            check_rs($flagPrivacy, $rs_row);
            $flag = $this->userInfoModel->editInfo($user_id, $edit_user_row);
            check_rs($flag, $rs_row);
            
            $flag = is_ok($rs_row);
            if ($flag !== false && $this->userInfoModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
            } else {
                $this->userInfoModel->sql->rollBackDb();
                $status = 250;
                $msg = __('failure');
                
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
        }
        
        /**
         *获取会员头像
         *
         * @access public
         */
        public function getUserImg()
        {
            
            include $this->view->getView();
        }
        
        /**
         * 修改会员头像
         *
         * @access public
         */
        public function editUserImg()
        {
            $user_id = Perm::$userId;
            
            $edit_user_row['user_logo'] = request_string('user_logo');
            
            $flag = $this->userInfoModel->editInfo($user_id, $edit_user_row);
            
            if ($flag === false) {
                $status = 250;
                $msg = __('failure');
            } else {
                $status = 200;
                $msg = __('success');
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
        }
        
        /**
         *获取会员等级
         *
         * @access public
         */
        public function getUserGrade()
        {
            $user_id = Perm::$userId;
            
            $re = $this->userInfoModel->getOne($user_id);
            $resource = $this->userResourceModel->getOne($user_id);
            
            $re['user_growth'] = $resource['user_growth'];
            
            $user_grade_id = $re['user_grade'];
            
            $data = $this->userGradeModel->getOne($user_grade_id);
            
            $data = $this->userGradeModel->getUserExpire($data);
            
            $gradeList = $this->userGradeModel->getGradeList();
            
            $data = $this->userGradeModel->getGradeGrowth($data, $gradeList, $re);
            
            if ('json' == $this->typ) {
                $data['gradeList'] = $gradeList;
                $data['re'] = $re;
                $data['resource'] = $resource;
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        
        /**
         *获取会员标签
         *
         * @access public
         */
        public function tag()
        {
            $user_id = Perm::$userId;
            $order_row['user_id'] = $user_id;
            
            $data = $this->userTagRecModel->getTagRecList($order_row);
            $re = array();
            
            if ($data['items']) {
                $user_tag_ids = array_column($data['items'], 'user_tag_id');
                
                $tag = array();
                
                if ($data['items']) {
                    $tag['user_tag_id:not in'] = $user_tag_ids;
                }
                
                $tag_row['user_tag_id:in'] = $user_tag_ids;
                
                $ce = $this->userTagModel->getTagList($tag_row);
                
                $user_tag = array_column($ce['items'], 'user_tag_id');
                
                $nameAll = array();
                foreach ($ce['items'] as $key => $val) {
                    $nameAll[$val['user_tag_id']] = $val;
                }
                foreach ($data['items'] as $key => $val) {
                    
                    if (in_array($val['user_tag_id'], $user_tag)) {
                        $data['items'][$key]['user_tag_name'] = $nameAll[$val['user_tag_id']]['user_tag_name'];
                    }
                }
                
                $re = $this->userTagModel->getTagList($tag);
            } else {
                $re = $this->userTagModel->getTagList();
            }
            if ('json' == $this->typ) {
                $data['re'] = $re;
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        
        /**
         *编辑会员兴趣标签
         *
         * @access public
         */
        public function editTagRec()
        {
            $user_id = Perm::$userId;
            
            $id_row = array();
            $id_row = request_row('mid');

            //去除数组中的重复值
            $id_row = array_unique($id_row);
            
            $edit_rec_row['user_id'] = $user_id;
            $edit_rec_row['tag_rec_time'] = get_date_time();
            
            //开启事物
            $rs_row = array();
            $this->userTagRecModel->sql->startTransactionDb();
            
            $order_row['user_id'] = $user_id;
            
            $de = $this->userTagRecModel->getTagRecList($order_row);
            if ($de['items']) {
                $user_tag = array_column($de['items'], 'tag_rec_id');
                
                $updata_flag = $this->userTagRecModel->removeRec($user_tag);
                check_rs($updata_flag, $rs_row);
            }
            foreach ($id_row as $v) {
                $edit_rec_row['user_tag_id'] = $v;
                $flag = $this->userTagRecModel->addRec($edit_rec_row);
                check_rs($flag, $rs_row);
            }
            
            $flag = is_ok($rs_row);
            if ($flag !== false && $this->userTagRecModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
            } else {
                $this->userTagRecModel->sql->rollBackDb();
                $msg = __('failure');
                $status = 250;
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        /**
         *获取会员地址信息
         *
         * @access public
         */
        public function address()
        {
            $user_id = Perm::$userId;
            $act = request_string('act');
            //wap端修改地址标识
			$targ = request_string('targ');
			
            //获取一级地址
            $district_parent_id = request_int('pid', 0);
            $Base_DistrictModel = new Base_DistrictModel();
            $district = $Base_DistrictModel->getDistrictTree($district_parent_id);
            
            if ($act == 'edit') {
                $userId = Perm::$userId;
                $user_address_id = request_int('id');
                
                $data = $this->userAddressModel->getAddressInfo($user_address_id);
            } elseif ($act == 'add') {
                $userId = Perm::$userId;
                
                $data = array();
            } elseif ($act == 'edit_delivery') {
                $userId = Perm::$userId;
                $data = array();
            } else {
                $order_row['user_id'] = $user_id;
                
                $data = $this->userAddressModel->getAddressList($order_row);
            }
            
            if ("json" == $this->typ) {
                //
                $district_id_row = array_merge(
                    array_column($data, 'user_address_province_id'),
                    array_column($data, 'user_address_city_id'),
                    array_column($data, 'user_address_area_id')
                );
                
                $district_id_row = array_filter($district_id_row);
                
                if ($district_id_row) {
                    $district_rows = $Base_DistrictModel = $Base_DistrictModel->getDistrict($district_id_row);
                    
                    foreach ($data as $k => $address_row) {
                        $address_row['address_info'] = sprintf('%s %s %s', @$district_rows[$address_row['user_address_province_id']]['district_name'], @$district_rows[$address_row['user_address_city_id']]['district_name'], @$district_rows[$address_row['user_address_area_id']]['district_name']);
                        $data[$k] = $address_row;
                    }
                }
                
                //对APP过来的请求，数组重新处理
                if ($_GET['format'] == 'app') {
                    unset($nd);
                    foreach ($data as $v) {
                        $nd[] = $v;
                    }
                    $data = $nd;
                }
                $data_rows['address_list'] = $data;
                $targ && $data_rows['address_list'] = $data;
                $this->data->addBody(-140, $data_rows);
            } else {
                include $this->view->getView();
            }
            
        }
        
        /**
         *获取用户默认收货地址
         *
         * @access public
         */
        public function getUserConfigAddress()
        {
            $user_id = Perm::$userId;
            
            $data = $this->userAddressModel->getByWhere(
                ['user_id' => $user_id],
                ['user_address_default' => 'DESC']
            );
            
            if ($data) {
                $data = current($data);
                $msg = 'success';
                $status = 200;
            } else {
                $data = [];
                $msg = 'failure';
                $status = 250;
            }
            $this->data->addBody(-140, $data, $msg, $status);
        }
        
        public function getUserAddressList()
        {
            $user_id = Perm::$userId;
            $addressList = $this->userAddressModel->getByWhere([
                'user_id' => $user_id
            ]);
            
            if ($addressList === false) {
                $addressList = [];
                $msg = 'failure';
                $status = 250;
            } else {
                $addressList = array_values($addressList);
                $msg = 'success';
                $status = 200;
            }
            $this->data->addBody(-140, $addressList, $msg, $status);
        }
        
        /**
         *编辑会员地址信息
         *
         * @access public
         */
        public function editAddressInfo()
        {
            $user_id = Perm::$userId;
            $user_address_id = request_int('user_address_id');
            $user_address_contact = request_string('user_address_contact');
            $user_address_area = request_string('address_area');
            $user_address_address = request_string('user_address_address');
            $user_address_phone = preg_replace('# #', '', request_string('user_address_phone'));
            $area_code = request_string('area_code')?:86;
            $user_address_default = request_string('user_address_default');
            $address_attribute = request_string('address_attribute');
            $edit_address_row['user_id'] = $user_id;
            $edit_address_row['user_address_contact'] = $user_address_contact;
            $edit_address_row['user_address_province_id'] = request_int('province_id');
            $edit_address_row['user_address_city_id'] = request_int('city_id');
            $edit_address_row['user_address_area_id'] = request_int('area_id');
            $edit_address_row['user_address_area'] = $user_address_area;
            $edit_address_row['user_address_address'] = $user_address_address;
            $edit_address_row['user_address_phone'] = $user_address_phone;
            $edit_address_row['area_code'] = $area_code;
            $edit_address_row['user_address_default'] = $user_address_default;
            $edit_address_row['user_address_time'] = get_date_time();
            $edit_address_row['user_address_attribute'] = $address_attribute;
            if (!$user_address_phone || (!Yf_Utils_String::isMobile($user_address_phone) && $area_code==86) ) {
                return $this->data->addBody(-140, array(), __('手机号码格式有误'), 250);
            }

            //验证用户
            $cond_row = array(
                'user_id' => $user_id,
                'user_address_id' => $user_address_id,
            );
            
            $re = $this->userAddressModel->getByWhere($cond_row);
            
            if (!$re) {
                $msg = __('failure');
                $status = 250;
            } else {
                //开启事物
                $rs_row = array();
                $this->userAddressModel->sql->startTransactionDb();
                
                if ($user_address_default == '1') {
                    
                    $order_row['user_id'] = $user_id;
                    $order_row['user_address_default'] = '1';
                    $de = $this->userAddressModel->getAddressList($order_row);
                    
                    if (!empty($de)) {
                        $updata_flag = $this->userAddressModel->editAddressInfo($de);
                        check_rs($updata_flag, $rs_row);
                    }
                }
                
                $flag = $this->userAddressModel->editAddress($user_address_id, $edit_address_row);
                
                check_rs($flag, $rs_row);
                
                $flag = is_ok($rs_row);
                if ($flag !== false && $this->userAddressModel->sql->commitDb()) {
                    $status = 200;
                    $msg = __('success');
                } else {
                    $this->userAddressModel->sql->rollBackDb();
                    $msg = __('failure');
                    $status = 250;
                }
                
                $edit_address_row['user_address_id'] = $user_address_id;
                $data = $edit_address_row;
                $this->data->addBody(-140, $data, $msg, $status);
            }
            
        }
        
        /**
         *地址自动填充
         *
         * @access public
         */
        public function addressSelfFill () {
            $user_adress_all = request_string('address_self_motion');
            $arr = explode(' ',$user_adress_all);
            $data=[];
            if ($arr){
                $data['addressee_name'] = $arr[0];
                $data['addressee_mobile'] = $arr[1];
            }
            $ss = urlencode($user_adress_all);
            $url = 'https://restapi.amap.com/v3/place/text?key=2b10d41301e12e68a8d33e455d22b555&keywords="'.$ss.'"&types=&city=&children=&offset=&page=&extensions=all';
            // $url_return = file_get_contents($url);
            $ch = curl_init();
            //设置选项，包括URL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//绕过ssl验证
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            //执行并获取HTML文档内容
            $url_return = curl_exec($ch);
            //释放curl句柄
            curl_close($ch);
            $url_return = json_decode($url_return,true);
            if ($url_return['count'] >0){
                $shi_zx = ['上海市','北京市','天津市','重庆市'];
                $data['city']=$url_return['pois'][0]['cityname']; //市
                $data['area']=$url_return['pois'][0]['adname']; //区
                if (in_array($url_return['pois'][0]['cityname'],$shi_zx)){
                    $province=str_replace('市','',$url_return['pois'][0]['cityname']);
                     $data['addressee_user_info'] = $province . '  ' . $data['area'];
                }else{
                     $province=str_replace('省','',$url_return['pois'][0]['pname']);
                     $data['addressee_user_info'] = $province  . ' '. $data['city'] . ' ' . $data['area'];
                }
                
                $data['user_address_address']= $url_return['pois'][0]['address'];
                if ($url_return['pois'][0]['address']==$url_return['pois'][0]['adname']){
                    $data['user_address_address']= $url_return['pois'][0]['name'];
                }
           
                $Base_DistrictModel = new  Base_DistrictModel();
                $province_arr  = $Base_DistrictModel->getOneByWhere(array("district_name:LIKE"=>$province));
                if ($province_arr) {
                    $data['user_address_province_id'] = $province_arr['district_id'];
                }
            
                $city_arr  = $Base_DistrictModel->getOneByWhere(array("district_name:LIKE"=>$data['city']));
                if ($city_arr) {
                    $data['user_address_city_id'] = $city_arr['district_id'];
                }
                $area_arr  = $Base_DistrictModel->getOneByWhere(array("district_name:LIKE"=>$data['area']));
                if ($area_arr) {
                    $data['user_address_area_id'] = $area_arr['district_id'];
                }
                $msg = 'success';
                $status = 200;
            }else{
                $msg = '请粘贴有效地址';
                $status = 250;
            }

            $this->data->addBody(-140, $data, $msg, $status);
        }

        /**
         *编辑会员/默认地址
         *
         * @access public
         */
        public function editAddressDefaultInfo()
        {
            $user_id = Perm::$userId;
            $user_address_id = request_int('user_address_id');
            $user_address_default = request_string('user_address_default');
            
            $edit_address_row['user_id'] = $user_id;
            $edit_address_row['user_address_default'] = $user_address_default;
            $edit_address_row['user_address_time'] = get_date_time();
            
            //验证用户
            $cond_row = array(
                'user_id' => $user_id,
                'user_address_id' => $user_address_id,
            );
            
            $re = $this->userAddressModel->getByWhere($cond_row);
            
            if (!$re) {
                $msg = __('failure');
                $status = 250;
            } else {
                //开启事物
                $rs_row = array();
                $this->userAddressModel->sql->startTransactionDb();
                
                if ($user_address_default == '1') {
                    
                    $order_row['user_id'] = $user_id;
                    $order_row['user_address_default'] = '1';
                    $de = $this->userAddressModel->getAddressList($order_row);
                    
                    if (!empty($de)) {
                        $updata_flag = $this->userAddressModel->editAddressInfo($de);
                        check_rs($updata_flag, $rs_row);
                    }
                }
                
                $flag = $this->userAddressModel->editAddress($user_address_id, $edit_address_row);
                
                check_rs($flag, $rs_row);
                
                $flag = is_ok($rs_row);
                if ($flag !== false && $this->userAddressModel->sql->commitDb()) {
                    $status = 200;
                    $msg = __('success');
                } else {
                    $this->userAddressModel->sql->rollBackDb();
                    $msg = __('failure');
                    $status = 250;
                }
                
                $edit_address_row['user_address_id'] = $user_address_id;
                $data = $edit_address_row;
                $this->data->addBody(-140, $data, $msg, $status);
            }
            
        }
        
        /**
         *增加会员地址信息
         *
         * @access public
         */
        public function addAddressInfo()
        {
            $user_id = Perm::$userId;
            $user_address_contact = request_string('user_address_contact');
            $user_address_area = request_string('address_area');
            $user_address_address = request_string('user_address_address');
            $user_address_phone = preg_replace('# #', '', request_string('user_address_phone'));
            $area_code = request_string('area_code')?:86;
            $user_address_default = request_string('user_address_default');
            $address_attribute = request_string('address_attribute');
            if (!$user_address_phone || (!Yf_Utils_String::isMobile($user_address_phone) && $area_code==86) ) {
                return $this->data->addBody(-140, array(), __('手机号码格式有误'), 250);
            }
            $user_address_province_id = request_int('province_id');
            $user_address_city_id = request_int('city_id');
            $user_address_area_id = request_int('area_id');
            //如果未选择地址 则查询库
            if (!$user_address_province_id && !$user_address_city_id && !$user_address_area_id) {
                $wxAddress = explode(' ', $user_address_area);
                $shi_zx = ['上海市','北京市','天津市','重庆市'];
                //直辖市处理
                if (in_array($wxAddress[0], $shi_zx)) {
                    $provin = rtrim($wxAddress[0],'市');
                    $city = $wxAddress[2];
                }else{
                    $provin = rtrim($wxAddress[0],'省');
                    $city = $wxAddress[1];
                    $country = $wxAddress[2];
                }
                $Base_DistrictModel = new  Base_DistrictModel();
                $province_arr  = $Base_DistrictModel->getOneByWhere(array("district_name:LIKE"=>$provin));
                $user_address_province_id = $province_arr['district_id'];
                $city_arr  = $Base_DistrictModel->getOneByWhere(array("district_name:LIKE"=>$city));
                $user_address_city_id = $city_arr['district_id'];
                if ($country) {
                    $area_arr  = $Base_DistrictModel->getOneByWhere(array("district_name:LIKE"=>$country));
                    $user_address_area_id = $area_arr['district_id'];
                }
            }
            $edit_address_row['user_id'] = $user_id;
            $edit_address_row['user_address_contact'] = $user_address_contact;
            $edit_address_row['user_address_province_id'] = $user_address_province_id;
            $edit_address_row['user_address_city_id'] = $user_address_city_id;
            $edit_address_row['user_address_area_id'] = $user_address_area_id;
            $edit_address_row['user_address_area'] = $user_address_area;
            $edit_address_row['user_address_address'] = $user_address_address;
            $edit_address_row['user_address_phone'] = $user_address_phone;
            $edit_address_row['area_code'] = $area_code;
            $edit_address_row['user_address_default'] = $user_address_default;
            $edit_address_row['user_address_time'] = get_date_time();
            $edit_address_row['user_address_attribute'] = $address_attribute;
            $cond_row['user_id'] = $user_id;
            
            $re = $this->userAddressModel->getCount($cond_row);
            if ($re > 19) {
                
                $status = 250;
                $msg = __('failure');
                
            } else {
                
                //开启事物
                $rs_row = array();
                $this->userAddressModel->sql->startTransactionDb();
                
                //判断是否设默认，默认改变前面的状态
                if ($user_address_default == '1') {
                    
                    $order_row['user_id'] = $user_id;
                    $order_row['user_address_default'] = '1';
                    $de = $this->userAddressModel->getAddressList($order_row);
                    
                    if (!empty($de)) {
                        $updata_flag = $this->userAddressModel->editAddressInfo($de);
                    }
                }
                check_rs($updata_flag, $rs_row);
                $flag = $this->userAddressModel->addAddress($edit_address_row, true);
                $addess_id = $flag;
                fb($flag);
                check_rs($flag, $rs_row);
                $flag = is_ok($rs_row);
                if ($flag !== false && $this->userAddressModel->sql->commitDb()) {
                    $edit_address_row['user_address_id'] = $addess_id;
                    $status = 200;
                    $msg = __('success');
                } else {
                    $this->userAddressModel->sql->rollBackDb();
                    
                    $status = 250;
                    $msg = __('failure');
                }
            }
            
            $data = $edit_address_row;
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        /**
         *删除会员地址信息
         *
         * @access public
         */
        public function delAddress()
        {
            $user_id = Perm::$row['user_id'];
            $user_address_id = request_string('id');
            if ('json' == request_string('typ')) {
                $user_address_id = request_int('id');
            }
            
            //验证用户
            $cond_row = array(
                'user_id' => $user_id,
                'user_address_id' => $user_address_id
            );
            $re = $this->userAddressModel->getByWhere($cond_row);
            
            if ($re) {
                $flag = $this->userAddressModel->removeAddress($user_address_id);
            } else {
                $flag = false;
            }
            
            if ($flag !== false) {
                $status = 200;
                $msg = __('success');
            } else {
                $status = 250;
                $msg = __('failure');
            }
            
            $data = array();
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        /**
         *查找好友
         *
         * @access public
         */
        public function friend()
        {
            $user_id = Perm::$userId;
            $act = request_string('op');
            
            $user_name = request_string("searchname");
            
            if ($act == 'follow') {
                $cond_row['user_id'] = $user_id;
                $order_row = array('friend_addtime' => 'DESC');
                
                $Yf_Page = new Yf_Page();
                $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 30;
                $rows = $Yf_Page->listRows;
                $offset = request_int('firstRow', 0);
                $page = ceil_r($offset / $rows);
                
                $friend_list = $this->userFriendModel->getFriendAllDetail($cond_row, $order_row, $page, $rows);
                
                $Yf_Page->totalRows = $friend_list['totalsize'];
                $page_nav = $Yf_Page->prompt();
                
                $data = array();
                $data['friend_list'] = $friend_list;
                $this->data->addBody(-140, $data);
                
                $this->view->setMet('follow');
                
            } elseif ($act == 'fan') {
                $cond_row['friend_id'] = $user_id;
                $order_row = array('friend_addtime' => 'DESC');
                
                $Yf_Page = new Yf_Page();
                $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 30;
                $rows = $Yf_Page->listRows;
                $offset = request_int('firstRow', 0);
                $page = ceil_r($offset / $rows);
                
                $friend_list = $this->userFriendModel->getBeFriendAllDetail($cond_row, $order_row, $page, $rows);
                
                $Yf_Page->totalRows = $friend_list['totalsize'];
                $page_nav = $Yf_Page->prompt();
                
                $data = array();
                $data['friend_list'] = $friend_list;
                $this->view->setMet('fan');
            } else {
                
                if ($user_name) {
                    $type = 'user_name:LIKE';
                    $cond_row[$type] = '%' . $user_name . '%';
                    $order_row = array();
                    
                    $Yf_Page = new Yf_Page();
                    $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 30;
                    $rows = $Yf_Page->listRows;
                    $offset = request_int('firstRow', 0);
                    $page = ceil_r($offset / $rows);
                    
                    $cond_row['user_id:!='] = $user_id;
                    
                    $user_list = $this->userInfoModel->getInfoList($cond_row, $order_row, $page, $rows);
                    
                    $friend_row = array();
                    $friend_row['friend_id:IN'] = array_column($user_list['items'], 'user_id');
                    
                    $friend_row['user_id'] = $user_id;
                    
                    $friend_list = $this->userFriendModel->getFriendAll($friend_row);
                    
                    $friend_id = array();
                    $friend_id = array_column($friend_list, 'friend_id');
                    
                    //获取已经加好友的
                    foreach ($user_list['items'] as $key => $val) {
                        
                        if (in_array($val['user_id'], $friend_id)) {
                            $user_list['items'][$key]['status'] = 1;
                        } else {
                            $user_list['items'][$key]['status'] = 0;
                        }
                    }
                    
                    $Yf_Page->totalRows = $user_list['totalsize'];
                    $page_nav = $Yf_Page->prompt();
                    
                    $data['user_list'] = $user_list;
                } else {
                    //推荐标签列表
                    $cond_row['user_tag_recommend'] = 1;
                    $sort = array('user_tag_sort' => 'DESC');
                    
                    $data = $this->userTagModel->getTagList($cond_row, $sort, 1, 10);
                    
                    if (!empty($data['items'])) {
                        foreach ($data['items'] as $key => $val) {
                            $Yf_Page = new Yf_Page();
                            $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 10;
                            $rows = $Yf_Page->listRows;
                            $offset = request_int('firstRow', 0);
                            $page = ceil_r($offset / $rows);
                            
                            //查询已经是好友的，排除掉
                            $friend_row['user_id'] = $user_id;
                            
                            $friend_list = $this->userFriendModel->getFriendAll($friend_row);
                            
                            $user = array();
                            
                            $user = array_column($friend_list, 'friend_id');
                            
                            array_push($user, $user_id);
                            $tag_row['user_id:not in'] = $user;
                            $tag_row['user_tag_id'] = $val['user_tag_id'];
                            $order_row = array();
                            
                            $tag = $this->userTagRecModel->getTagRecList($tag_row, $order_row, $page, $rows);
                            $users_id = array();
                            if ($tag['items']) {
                                
                                $users_id = array_column($tag['items'], 'user_id');
                                $detail = $this->userInfoModel->getInfo($users_id);
                            }
                            //标签下除好友的总会员个数
                            $count = count($tag['items']);
                            $data['items'][$key]['count'] = $count;
                            
                            foreach ($tag['items'] as $k => $v) {
                                if (in_array($v['user_id'], $users_id)) {
                                    $tag['items'][$k]['detail'] = $detail[$v['user_id']];
                                }
                            }
                            $data['items'][$key]['user'] = $tag;
                        }
                    }
                    
                }
                
            }
            if ('json' == $this->typ) {
                $this->data->addBody(-140, $data);
            } else {
                //ucenter 调取用户默认头像
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('ucenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');
                $formvars = array();
                $formvars['app_id'] = $shop_app_id;
                $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Index&met=getUserAvatar&typ=json', $url), $formvars);

                $userAvatar = $rs['data']['image'];
                include $this->view->getView();
            }
            
        }
        
        /**
         *关注标签下面没有关注的会员
         *
         * @access public
         */
        public function addFriends()
        {
            $user_id = Perm::$userId;
            
            $cond_row['user_id'] = $user_id;
            $cond_row['friend_addtime'] = get_date_time();
            //查询已经是好友的，排除掉
            $friend_row['user_id'] = $user_id;
            
            $friend_list = $this->userFriendModel->getFriendAll($friend_row);
            
            $user = array();
            $user = array_column($friend_list, 'friend_id');
            
            array_push($user, $user_id);
            $tag_row['user_id:not in'] = $user;
            $tag_row['user_tag_id'] = request_int('id');
            $order_row = array();
            
            $tag = $this->userTagRecModel->getTagRecList($tag_row, $order_row);
            
            //开启事物
            $rs_row = array();
            $this->userFriendModel->sql->startTransactionDb();
            
            foreach ($tag['items'] as $key => $val) {
                $user_id = $val['user_id'];
                $detail = $this->userInfoModel->getInfo($user_id);
                $detail = $detail[$user_id];
                
                $cond_row['friend_id'] = $val['user_id'];
                $cond_row['friend_name'] = $detail['user_name'];
                $cond_row['friend_image'] = $detail['user_logo'];
                
                $flag = $this->userFriendModel->addFriend($cond_row);
                check_rs($flag, $rs_row);
            }
            
            $flag = is_ok($rs_row);
            if ($flag !== false && $this->userFriendModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
            } else {
                $this->userFriendModel->sql->rollBackDb();
                $msg = __('failure');
                $status = 250;
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        /**
         *关注一个会员
         *
         * @access public
         */
        public function addFriendDetail()
        {
            $user_id = Perm::$userId;
            
            $cond_row['user_id'] = $user_id;
            $cond_row['friend_addtime'] = get_date_time();
            
            $userId = request_int('id');
            $detail = $this->userInfoModel->getOne($userId);
            if (!$detail) {
                $status = 250;
                $msg = __('failure');
            } else {
                
                $cond_row['friend_id'] = $userId;
                $cond_row['friend_name'] = $detail['user_name'];
                $cond_row['friend_image'] = $detail['user_logo'];
                
                $flag = $this->userFriendModel->addFriend($cond_row);
                
                if ($flag !== false) {
                    $status = 200;
                    $msg = __('success');
                } else {
                    $msg = __('failure');
                    $status = 250;
                }
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        /**
         *取消一个会员
         *
         * @access public
         */
        public function cancelFriendDetail()
        {
            $user_id = Perm::$userId;
            
            $user_friend_id = request_int('id');
            
            $cond_row['user_id'] = $user_id;
            $cond_row['user_friend_id'] = $user_friend_id;
            
            $de = $this->userFriendModel->getFriendInfo($cond_row);
            
            if (!$de) {
                
                $status = 250;
                $msg = __('failure');
            } else {
                $flag = $this->userFriendModel->removeFriend($user_friend_id);
                
                if ($flag !== false) {
                    $status = 200;
                    $msg = __('success');
                } else {
                    
                    $msg = __('failure');
                    $status = 250;
                }
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        /**
         * 密码修改
         *
         * @access public
         */
        public function passwd()
        {
            
            $url = Yf_Registry::get('ucenter_api_url') . '?ctl=User&met=passwd';
            location_to($url);
            die();
        }
        
        /**
         *获取会员安全信息
         *
         * @access public
         */
        public function security()
        {
            
            $url = Yf_Registry::get('ucenter_api_url') . '?ctl=User&met=security';
            location_to($url);
            die();
            
            $user_id = Perm::$userId;
            $op = request_string('op');
            
            $user_id = Perm::$userId;
            
            $data = $this->userInfoModel->getInfo($user_id);
            
            $data = $data[$user_id];
            
            if ($op == 'email') {
                $this->view->setMet('email');
            } elseif ($op == 'mobile') {
                $this->view->setMet('mobile');
            } elseif ($op == 'mobiles') {
                $name = __('手机');
                $this->view->setMet('security_identity');
            } elseif ($op == 'emails') {
                $name = __('邮箱');
                $this->view->setMet('security_identity');
            }
            
            if ('json' == $this->typ) {
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        
        //手机绑定验证
        public function getMobile()
        {
            $user_id = Perm::$userId;
            $cond_row['user_mobile'] = request_string('verify_field');
            $cond_row['user_id:!='] = $user_id;
            
            $de = $this->userInfoModel->getUserInfo($cond_row);
            
            if ($de) {
                
                $msg = __('failure');
                $status = 250;
            } else {
                
                $status = 200;
                $msg = __('success');
                
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //生成验证码,并且发送验证码
        public function getMobileYzm()
        {
            
            $mobile = request_string('mobile');
            $area_code =request_string('area_code')?:86;
            $user_id = Perm::$userId;
            $cond_row['user_mobile'] = $mobile;
            $cond_row['user_id:!='] = $user_id;
            
            $data = array('sms_time' => 60);
            //检测该手机号是否已被其他用户绑定
            $ce = $this->userInfoModel->getUserInfo($cond_row);
            
            if ($ce) {
                $msg = __('该手机号已被其他会员绑定，请更换其他手机号');
                $status = 250;
            } else {
                $code_cond_row['code'] = 'verification';
                
                $de = $this->messageTemplateModel->getTemplateDetail($code_cond_row);
                
                $msg = $de['content_phone'];
                
                $code_key = $mobile;
                $code = VerifyCode::getCode($code_key);
                $msg = str_replace("[weburl_name]", $this->web['web_name'], $msg);
                $msg = str_replace("[yzm]", $code, $msg);
                
                $str = Sms::send($mobile, $area_code,$msg, $de['baidu_tpl_id'],['weburl_name'=>$this->web['web_name'],'yzm'=>$code]);
                
                $data['code'] = $code;
                
                $status = 200;
    			//$msg = "success";
            }
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //检测验证码
        public function checkMobileYzm()
        {
            
            $yzm = request_string('yzm');
            $mobile = request_string('mobile');
            
            if (VerifyCode::checkCode($mobile, $yzm)) {
                
                $status = 200;
                $msg = __('success');
            } else {
                $msg = __('failure');
                $status = 250;
                
            }
            $data = array();
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //绑定手机
        public function editMobileInfo()
        {
            $user_id = Perm::$userId;
            $user_name = Perm::$row['user_account'];
            $user_mobile = request_string('user_mobile');
            $area_code = request_string('area_code')?:86;
            $yzm = request_string('yzm');
            
            if (!VerifyCode::checkCode($user_mobile, $yzm)) {
                $msg = __('验证码错误');
                $status = 240;
            } else {
                //远程从ucenter中修改用户的手机号
                $key = Yf_Registry::get('ucenter_api_key');
                
                $url = Yf_Registry::get('ucenter_api_url');
                $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
                $formvars = array();
                $formvars['user_id'] = $user_id;
                $formvars['user_mobile'] = $user_mobile;
                $formvars['area_code'] = $area_code;
                $formvars['user_name'] = $user_name;
                $formvars['app_id'] = $ucenter_app_id;
                
                $formvars['ctl'] = 'Api_User';
                $formvars['met'] = 'editMobileInfo';
                $formvars['typ'] = 'json';
                $init_rs = get_url_with_encrypt($key, $url, $formvars);
                
                if ($init_rs['status'] == 200) {
                    $msg = __('success');
                    $status = 200;
                } else {
                    $msg = __('绑定手机失败');
                    $status = 250;
                }
                
            }
            $data = array();
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //邮箱绑定验证
        public function getEmail()
        {
            $user_id = Perm::$userId;
            $cond_row['user_email'] = request_string('verify_field');
            $cond_row['user_id:!='] = $user_id;
            
            $de = $this->userInfoModel->getUserInfo($cond_row);
            
            if ($de) {
                
                $msg = __('failure');
                $status = 250;
            } else {
                
                $status = 200;
                $msg = __('success');
                
            }
            
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //邮箱生成验证码,并且发送验证码
        public function getEmailYzm()
        {
            
            $email = request_string('email');
            $user_id = Perm::$userId;
            $cond_row['user_email'] = $email;
            $cond_row['user_id:!='] = $user_id;
            
            $ce = $this->userInfoModel->getUserInfo($cond_row);
            
            if ($ce) {
                $msg = __('failure');
                $status = 250;
            } else {
                $cond_row['code'] = 'verification';
                
                $de = $this->messageTemplateModel->getTemplateDetail($cond_row);
                
                $me = $de['content_email'];
                $title = $de['title'];
                
                $code_key = $email;
                $code = VerifyCode::getCode($code_key);
                $me = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $me);
                $me = str_replace("[yzm]", $code, $me);
                $title = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $title);
                
                $str = Email::sendMail($email, Perm::$row['user_account'], $title, $me);
                
                $status = 200;
                $msg = 'success';
            }
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //邮箱检测验证码
        public function checkEmailYzm()
        {
            
            $yzm = request_string('yzm');
            $email = request_string('email');
            
            if (VerifyCode::checkCode($email, $yzm)) {
                
                $status = 200;
                $msg = __('success');
            } else {
                $msg = __('failure');
                $status = 250;
                
            }
            $data = array();
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //绑定邮箱
        public function editEmailInfo()
        {
            $user_id = Perm::$userId;
            $user_email = request_string('user_email');
            $yzm = request_string('yzm');
            if (!VerifyCode::checkCode($user_email, $yzm)) {
                $msg = __('failure');
                $status = 240;
            } else {
                $edit_user_row['user_email'] = request_string('user_email');
                $edit_user_row['user_email_verify'] = 1;
                
                $de = $this->userInfoModel->getOne($user_id);
                
                $user_level_id = 1;
                
                if ($de['user_mobile_verify'] == 1) {
                    
                    $user_level_id = $user_level_id + 1;
                }
                
                $edit_user_row['user_level_id'] = $user_level_id + 1;
                
                $flag = $this->userInfoModel->editInfo($user_id, $edit_user_row);
                
                if ($flag == false) {
                    $msg = __('failure');
                    $status = 250;
                } else {
                    $status = 200;
                    $msg = __('success');
                }
            }
            $data = array();
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //解除绑定,生成验证码,并且发送验证码
        public function getYzm()
        {
            
            $type = request_string('type');
            $val = request_string('val');
            $area_code = request_string('area_code')?:86;
            
            $cond_row['code'] = 'Lift verification';
            
            $de = $this->messageTemplateModel->getTemplateDetail($cond_row);
            if ($type == 'mobile') {
                $me = $de['content_phone'];
                
                $code_key = $val;
                $code = VerifyCode::getCode($code_key);
                $me = str_replace("[weburl_name]", $this->web['web_name'], $me);
                $me = str_replace("[yzm]", $code, $me);
                
                $str = Sms::send($val,$area_code, $me, $de['baidu_tpl_id'],['weburl_name'=>$this->web['web_name'],'yzm'=>$code]);
            } else {
                $me = $de['content_email'];
                $title = $de['title'];
                
                $code_key = $val;
                $code = VerifyCode::getCode($code_key);
                $me = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $me);
                $me = str_replace("[yzm]", $code, $me);
                $title = str_replace("[weburl_name]", Web_ConfigModel::value("site_name"), $title);
                
                $str = Email::sendMail($val, Perm::$row['user_account'], $title, $me);
            }
            $status = 200;
            $data = array();
            $msg = "success";
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //检测解除验证码
        public function checkYzm()
        {
            
            $yzm = request_string('yzm');
            $type = request_string('type');
            $val = request_string('val');
            
            if (VerifyCode::checkCode($val, $yzm)) {
                
                $status = 200;
                $msg = __('success');
            } else {
                $msg = __('failure');
                $status = 250;
                
            }
            $data = array();
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //解除绑定
        public function editAllInfo()
        {
            $type = request_string('type');
            $yzm = request_string('yzm');
            $val = request_string('val');
            $user_id = Perm::$userId;
            
            if (!VerifyCode::checkCode($val, $yzm)) {
                $msg = __('failure');
                $status = 240;
            } else {
                if ($type == 'mobile') {
                    $edit_user_row['user_mobile'] = '';
                    $edit_user_row['user_mobile_verify'] = 0;
                } else {
                    $edit_user_row['user_email'] = '';
                    $edit_user_row['user_email_verify'] = 0;
                }
                
                $de = $this->userInfoModel->getOne($user_id);
                
                $user_level_id = $de['user_level_id'] * 1;
                
                $edit_user_row['user_level_id'] = $user_level_id - 1;
                
                $flag = $this->userInfoModel->editInfo($user_id, $edit_user_row);
                
                if ($flag == false) {
                    $msg = __('failure');
                    $status = 250;
                    
                } else {
                    
                    $status = 200;
                    $msg = __('success');
                }
            }
            $data = array();
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        
        //显示子账号设置
        public function getSubUser()
        {
            $user_id = Perm::$userId;
            $User_SubUserModel = new User_SubUserModel();
            
            $cond_row['user_id'] = $user_id;
            $order_row = array();
            
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 30;
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            
            $sub_user_list = $User_SubUserModel->getSubUserList($cond_row, $order_row, $page, $rows);
            
            $Yf_Page->totalRows = $sub_user_list['totalsize'];
            $page_nav = $Yf_Page->prompt();
            
            $data = array();
            $data = $sub_user_list;
            
            if ('json' == $this->typ) {
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        
        public function subUser()
        {
            $id = request_int('sub_user_id');
            $User_SubUserModel = new User_SubUserModel();
            $User_InfoModel = new User_InfoModel();
            
            if ($id) {
                $data = $User_SubUserModel->getOne($id);
                //查找用户信息
                $user_info = $User_InfoModel->getUserInfo(array('user_id' => $id));
                $data['user_name'] = $user_info['user_name'];
            }
            
            if ('json' == $this->typ) {
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        
        //添加及编辑关联子账号
        public function editSubUser()
        {
            $id = request_int('sub_user_id');
            $user_name = request_string('user_name');
            $sub_user_active = request_string('sub_user_active');
            
            $user_id = Perm::$userId;
            $User_SubUserModel = new User_SubUserModel();
            $User_InfoModel = new User_InfoModel();
            
            //开启事务
            $User_SubUserModel->sql->startTransactionDb();
            
            $flag = false;
            $msg = '';
            //存在id表示：编辑，不存在id表示：新增
            if ($id) {
                //验证修改用户是否是当前用户的关联子账户
                $array = array();
                $array['sub_user_id'] = $id;
                $array['user_id'] = $user_id;
                $sub_user = $User_SubUserModel->getByWhere($array);
                
                if ($sub_user) {
                    $edit_row = array();
                    $rs_row = array();
                    $edit_row['sub_user_active'] = $sub_user_active;
                    $edit_flag = $User_SubUserModel->editSub($id, $edit_row);
                    check_rs($edit_flag, $rs_row);
                    $flag = is_ok($rs_row);
                } else {
                    $flag = false;
                    $msg = __('该用户不是您的子账号');
                }
            } else {
                if ($user_name) {
                    //判断user_name是否已经是其他用户的子账号
                    $user_info = $User_InfoModel->getUserInfo(array('user_name' => $user_name));
                    if ($user_info) {
						//判断是否存在
                        $array = array();
						$array['sub_user_id'] = $user_info['user_id'];
						$array['user_id'] = $user_id;
						$ret = $User_SubUserModel->getByWhere($array);
						if($ret){
							$flag = false;
							$msg = __('对不起，不能重复绑定！');
						}else{
							//原逻辑：一个子账号只能跟一个买家绑定，现应要求改为子账号和买家关系改为一对多关系
							$add_row = array();
							$add_row['sub_user_id'] = $user_info['user_id'];
							$add_row['user_id'] = $user_id;
							$add_row['sub_user_active'] = $sub_user_active;
							$flag = $User_SubUserModel->addSub($add_row);
						}
                    } else {
                        $flag = false;
                        $msg = __('该用户不存在');
                    }
                } else {
                    $flag = false;
                    $msg = __('用户名不能为空');
                }
                
            }
            $data = array();
            
            if ($flag && $User_SubUserModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
            } else {
                $User_SubUserModel->sql->rollBackDb();
                $msg = $msg ? $msg : __('failure');
                $status = 250;
            }
            
            $this->data->addBody(-140, $rs_row, $msg, $status);
        }
        
        public function delSubUser()
        {
            $id = request_int('sub_user_id');
            
            $user_id = Perm::$userId;
            $User_SubUserModel = new User_SubUserModel();
            
            //开启事务
            $User_SubUserModel->sql->startTransactionDb();
            
            $array = array();
            $array['sub_id'] = $id;
            $array['user_id'] = $user_id;
            $sub_user = $User_SubUserModel->getByWhere($array);
            
            //判断要解绑的用户是否是当前用户的子账户
            $msg = '';
            if ($sub_user) {
                $flag = $User_SubUserModel->removeSub($id);
            } else {
                $flag = false;
                $msg = __('该用户不是您的子账号');
            }
            
            if ($flag && $User_SubUserModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
            } else {
                $User_SubUserModel->sql->rollBackDb();
                $msg = $msg ? $msg : __('failure');
                $status = 250;
            }
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
        }
        
        public function getUserBase()
        {
            $user_id = Perm::$userId;
            $key = Yf_Registry::get('shop_api_key');
            $url = Yf_Registry::get('paycenter_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');
            $formvars = array();
            
            $formvars['app_id'] = $shop_app_id;
            $formvars['user_id'] = $user_id;
            
            $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_App&met=getUserBase&typ=json', $url), $formvars);
            
            if ($rs['status'] == 200 && $rs['data']['user_pay_passwd']) {
                $status = 200;
                $msg = __('success');
            } else {
                $msg = __('failure');
                $status = 250;
            }
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
        }
        
        public function informationnewslist(){
            $combo_row = [];
            //分页
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = request_int('listRows') ? request_int('listRows'):10;
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            $cond_row['author_id'] = Perm::$userId;
            $cond_row['author_type'] = 1;
            if (request_string('op') == 'detail' && request_int('id')) {
                $id = request_int('id');
                $data = $this->newsdetail($id);
                $this->view->setMet('detail');
            } elseif (request_string('op') == 'editdetail' && request_int('id')) {
                $id = request_int('id');
                $classData = $this->InformationNewsClassModel->getByWhere([]);
                $data = $this->newsdetail($id);
                $this->view->setMet('editinformationnews');
            } else {
                //var_dump(request_string('complaint'));die;
                if (request_string('auditing') !== '') {
                    $cond_row['auditing'] = request_string('auditing');
                }
                if (request_string('complaint') !== '') {
                    $cond_row['complaint'] = request_int('complaint');
                }
                if (request_string('keyword') !== '') {
                    $cond_row['title:LIKE'] = request_string('keyword') . '%';
                }
                $data = $this->InformationNewsModel->getBaseAllList($cond_row, ['create_time' => 'DESC'], $page, $rows);
                $Yf_Page->totalRows = $data['totalsize'];
                $page_nav = $Yf_Page->prompt();
            }
            if ('json' == $this->typ) {
                $json_data['data'] = $data;
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
             }
        }
    


         //添加活动
        public function addinformationnews()
        {
            $classData = $this->InformationNewsClassModel->getByWhere([]);
            include $this->view->getView();
        }
    
        public function addNews()
        {

            $author_id = Perm::$userId;
            $title = request_string('news_title');
            $subtitle =request_string('news_subtitle');
            $type = request_string('newsclass_type');
            $content = request_string('content');
            $created_time = date('Y-m-d H:i:s', time());
            $author_type =1;
            
            if (!$title) {
                return $this->data->addBody(-140, [], __('请填写资讯标题！'), 250);
            }
            if (!$type) {
                return $this->data->addBody(-140, [], __('请填写资讯标签'), 250);
            }
            if (!$content) {
                return $this->data->addBody(-140, [], __('请填写资讯内容'), 250);
            }
            
            $addData = [
               'author_id' => $author_id,
               'title' => $title,
               'subtitle' => $subtitle,
               'type' => $type,
               'content' => $content,
               'create_time' => $created_time,
               'author_type' => $author_type,
            ];
            $news_id = $this->InformationNewsModel->addBase($addData, true);
            if ($news_id) {
                $msg = __('success');
                $status = 200;
            } else {
                $msg = __('failure');
                $status = 250;
            }
            $this->data->addBody(-140, [], $msg, $status);
        }

        /**
         * 资讯详情
         * @param $pintuan_id int
         * @param $details array
         * @return boolean
         */
        public function newsdetail($id)
        {
            $where = [
                'id' => $id
            ];
            $data = $this->InformationNewsModel->getOneByWhere($where);
            $data['content']=strip_tags(htmlspecialchars_decode($data['content']));
            $newclassname= $this->InformationNewsClassModel->getOneByWhere(array('id'=>$data['type']));
            $data['newscalss']=$newclassname['newsclass_name'];
            return $data;
        }

        public function editNews()
        {
            $author_id = Perm::$userId;
            $news_id=request_string('id');
            $title = request_string('news_title');
            $subtitle = request_string('news_subtitle');
            $type = request_string('newsclass_type');
            $content = request_string('content');
            if (!$title) {
                return $this->data->addBody(-140, [], __('请填写资讯标题！'), 250);
            }
            if (!$type) {
                return $this->data->addBody(-140, [], __('请填写资讯标签'), 250);
            }
            if (!$content) {
                return $this->data->addBody(-140, [], __('请填写资讯内容'), 250);
            }
            $editData = [
                'author_id' => $author_id,
                'title' => $title,
                'subtitle' => $subtitle,
                'type' => $type,
                'content' => $content,
                'auditing'=>3,//编辑之后状态为待审核
            ];
            $news_id = $this->InformationNewsModel->editBase($news_id, $editData);
            if ($news_id) {
                $msg = __('success');
                $status = 200;
            } else {
                $msg = __('failure');
                $status = 250;
            }
            $this->data->addBody(-140, [], $msg, $status);
        }

            /**
         * 删除资讯新闻
         *
         * @param $pintuan_id int
         * @param $details    array
         *
         * @return boolean
         */
        public function delnews()
        {
            $id=request_string('id');
            $flag = $this->InformationNewsModel->removeBase($id);
            
            if($flag !== false){
                  $msg = __('success');
                  $status = 200;
            } else {
                $msg = __('failure');
                $status = 250;
            }
            $data = [];
            $this->data->addBody(-140, $data, $msg, $status);
        }

    }

?>