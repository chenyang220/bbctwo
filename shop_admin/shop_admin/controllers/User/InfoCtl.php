<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class User_InfoCtl extends AdminController
{
	public $webconfigModel = null;

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}
    public function addsendstation()
    {
        $send_man = request_string('send_man');
        $send_content = request_string('send_content');
        $SenderAll = request_string('SenderAll');


        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');
        $app_id = Yf_Registry::get('shop_app_id');

        $formvars = array();
        $formvars['app_id'] = $app_id;
        $formvars['send_man'] = $send_man;
        $formvars['send_content'] = $send_content;
        $formvars['SenderAll'] = $SenderAll;
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=addsendion&typ=json', $url), $formvars);
        if ($rs['status'] == 200)
        {
            $status = 200;
            $msg    = __('success');
        }
        else
        {
            $status = 250;
            $msg    = __('failure');
        }

        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     *获取会员信息
     *
     * @access public
     */
    public function getInfoList()
    {

        $page = request_int('page', 1);
        $rows = request_int('rows', 10);
        $type = request_string('user_type');
        $name = request_string('search_name');
        $user_active_time = request_string('user_active_time');

        $shopBaseModel = new Shop_BaseModel();
        $User_InfoModel = new User_InfoModel();

        $cond_row = array();
        $sort = array();

        if (request_int('shop_source')) {
            $shop_list = $shopBaseModel->getByWhere(array('shop_type' => request_int('shop_source')));
            $shop_user = array_column($shop_list, 'user_id');
            $cond_row['user_id:IN'] = $shop_user;
        }

        if ($name) {
            if ($type == '1') {
                $cond_row['user_id'] = $name;
            } else {
                $type = 'user_name:LIKE';
                $cond_row[$type] = '%' . $name . '%';
            }

        }

        if ($user_active_time) {
            $cond_row['user_active_time'] = $user_active_time;
        }
        $sub_site_id = request_string('sub_site_id');
        if ($sub_site_id > 0) {
            //获取站点信息
            $Sub_SiteModel = new Sub_SiteModel();
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $cond_row['district_id:IN'] = $sub_site_district_ids;
            }
        }
        $data = $User_InfoModel->getList($cond_row, $sort, $page, $rows);
        foreach ($data['items'] as $key => $value) {
            $shop_info = $shopBaseModel->getOneByWhere(array('user_id' => $value['user_id']));
            if (!empty($shop_info)) {
                $data['items'][$key]['shop_type'] = $shop_info['shop_type'];
                $data['items'][$key]['shop_status'] = $shop_info['shop_status'];
            }

            $k = Yf_Registry::get('shop_api_key');
            $formvars = array();
            $formvars['user_id'] = $value['user_id'];
            $formvars['app_id'] = Yf_Registry::get('shop_app_id');

            $row = get_url_with_encrypt($k, sprintf('%s?ctl=Api_User_Info&met=getUserInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);

            $data['items'][$key]['identy'] = $row['data'];

            //会员积分
            $User_ResourceModel = new User_ResourceModel();
            $user_resource = $User_ResourceModel->getOne($value['user_id']);
            $data['items'][$key]['user_points'] = $user_resource['user_points'];

            //会员标签
            $User_TagRecModel = new User_TagRecModel();
            $user_tage = $User_TagRecModel->getRecTagInfo($value['user_id']);
            $data['items'][$key]['user_tag'] = $user_tage['tag_name'];
            $data['items'][$key]['user_tag_con'] = $user_tage['tag_name_con'];

            //会员等级
            $User_GradeModel = new User_GradeModel();
            $grade = $User_GradeModel->getOne($value['user_grade']);
            $data['items'][$key]['user_grade_con'] = $grade['user_grade_name'];
        }
        $this->data->addBody(-140, $data);

    }
    
    //送红包
    public function giveRedpacket()
    {
        $userIds = request_row('userIds');
        $redpacketIds = request_row('redpacketIds');
        $RedPacket_TempModel = new RedPacket_TempModel();
        $User_InfoModel = new User_InfoModel();
        $RedPacket_BaseModel = new RedPacket_BaseModel();

        //领取等级是否匹配
        $user_list = $User_InfoModel->getByWhere(array('user_id:IN'=> $userIds));
        $red_list = $RedPacket_TempModel->getByWhere(array('redpacket_t_id:IN' => $redpacketIds));
        $user_grade = array_unique(array_column($user_list,'user_grade'));
        $red_grade = array_unique(array_column($red_list, 'redpacket_t_user_grade_limit'));
        if($user_grade[array_search(min($user_grade), $user_grade)] < $red_grade[array_search(max($red_grade), $red_grade)]){
            $msg = '请按照会员级别发放红包';
            $status = 250;
            return $this->data->addBody(-140, array(), $msg, $status);
        }

        $msg = '';
        $rs_row = array();
        foreach($userIds as $value){
            $user_info = $User_InfoModel->getOne($value);
            foreach($redpacketIds as $val){
                $redpacket_info = $RedPacket_TempModel->getOne($val);
                
                //修改红包数量
                $red_flag = $RedPacket_TempModel->editRedPacketTempSingleField($redpacket_info['redpacket_t_id'], 'redpacket_t_giveout', $redpacket_info['redpacket_t_giveout'] + 1, $redpacket_info['redpacket_t_giveout']);
                check_rs($red_flag, $rs_row);

                //写入领取表
                $add_row['redpacket_code'] = $RedPacket_BaseModel->get_rpt_code($user_info['user_id']);
                $add_row['redpacket_t_id'] = $redpacket_info['redpacket_t_id'];
                $add_row['redpacket_title'] = $redpacket_info['redpacket_t_title'];
                $add_row['redpacket_desc'] = $redpacket_info['redpacket_t_desc'];
                $add_row['redpacket_start_date'] = $redpacket_info['redpacket_t_start_date'];
                $add_row['redpacket_end_date'] = $redpacket_info['redpacket_t_end_date'];
                $add_row['redpacket_price'] = $redpacket_info['redpacket_t_price'];
                $add_row['redpacket_t_orderlimit'] = $redpacket_info['redpacket_t_orderlimit'];
                $add_row['redpacket_state'] = 1;
                $add_row['redpacket_active_date'] = date('Y-m-d H:i:s',time());
                $add_row['redpacket_owner_id'] = $user_info['user_id'];
                $add_row['redpacket_owner_name'] = $user_info['user_name'];
                $base_flag = $RedPacket_BaseModel->addRedPacket($add_row);
                check_rs($base_flag, $rs_row);
            }
        }

        $flag = is_ok($rs_row);

        if(!$flag){
            $msg = $msg? $msg:'发放失败，稍后再试';
            $status = 250;
        }else{
            $msg = '发放成功';
            $status = 200;
        }

        $this->data->addBody(-140, array(), $msg, $status);
    }

    //批量修改积分
    public function setScore(){
        $userIds = request_row('userIds');
        $score = request_int('score');
        $way_for = request_int('way_for',1);//1+ 2-
        $score_desc = request_string('score_desc');

        $User_ResourceModel = new User_ResourceModel();
        $Points_LogModel = new Points_LogModel();
        $User_InfoModel = new User_InfoModel();
        $resource_list = $User_ResourceModel->getByWhere(array('user_id:IN'=> $userIds));
        $points = array_column($resource_list, 'user_points');
        if($score > $points[array_search(min($points), $points)] && $way_for == 2){
            $msg = '会员积分不足';
            $status = 250;
            return $this->data->addBody(-140, array(), $msg, $status);
        }

        $rs_row = array();
        foreach($userIds as $value){
            $user_resource = $User_ResourceModel->getOne($value);
            $user_info = $User_InfoModel->getOne($value);
            if($way_for == 1){
                $cond_row['user_points'] = $user_resource['user_points'] + $score;
            }else{
                $cond_row['user_points'] = $user_resource['user_points'] - $score;
            }

            $resource_flag = $User_ResourceModel->editResource($value, $cond_row);
            check_rs($resource_flag, $rs_row);

            //加入积分明细
            $add_row['points_log_type'] = $way_for;
            $add_row['class_id'] = 6; //管理员操作
            $add_row['user_id'] = $value;
            $add_row['user_name'] = $user_info['user_name'];
            $add_row['admin_name'] = 'admin';
            $add_row['points_log_points'] = $score;
            $add_row['points_log_time'] = date('Y-m-d H:i:s', time());
            $add_row['points_log_desc'] = $score_desc;
            $point_flag = $Points_LogModel->addLog($add_row);
            check_rs($point_flag, $rs_row);
        }

        $flag = is_ok($rs_row);

        if (!$flag) {
            $msg =  '修改积分失败，稍后再试';
            $status = 250;
        } else {
            $msg = '修改积分成功';
            $status = 200;
        }

        $this->data->addBody(-140, array(), $msg, $status);
    }

    //会员等级
    public function level(){
        $User_GradeModel = new User_GradeModel();
        $data = $User_GradeModel->getByWhere(array('user_grade_id:>'=>0));
        include $this->view->getView();
    }

    public function setLevel()
    {
        $userIds = request_row('userIds');
        $user_grade = request_int('user_grade');

        $User_InfoModel = new User_InfoModel();
        $User_GradeModel = new User_GradeModel();
        $User_ResourceModel = new User_ResourceModel();
        $Grade_LogModel = new Grade_LogModel();

        $grade_info = $User_GradeModel->getOne($user_grade);

        $rs_row = array();
        foreach($userIds as $value){
            $user_info = $User_InfoModel->getOne($value);
            $resource = $User_ResourceModel->getOne($value);
            //修改会员等级
            $info_flag = $User_InfoModel->editInfo($value,array('user_grade'=> $user_grade));
            check_rs($info_flag, $rs_row);

            //修改经验值
            $cond_row['user_growth'] = $grade_info['user_grade_demand'];
            $growth_flag = $User_ResourceModel->editResource($value, $cond_row);
            check_rs($growth_flag, $rs_row);

            //写入经验值明细
            if($user_grade > $user_info['user_grade']){
                $add_row['points_log_type'] = 1;//1获取
                $add_row['grade_log_grade'] = $grade_info['user_grade_demand'] - $resource['user_growth'];

            }else{
                $add_row['points_log_type'] = 2;
                $add_row['grade_log_grade'] = $resource['user_growth'] - $grade_info['user_grade_demand'];
            }

            $add_row['class_id'] = 4; //管理员操作
            $add_row['user_id'] = $value;
            $add_row['user_name'] = $user_info['user_name'];
            $add_row['admin_name'] = 'admin';
            $add_row['grade_log_time'] = date('Y-m-d H:i:s', time());
            $add_row['grade_log_desc'] = '管理员修改会员等级';
            if($add_row['grade_log_grade'] > 0){
                $point_flag = $Grade_LogModel->addLog($add_row);
                check_rs($point_flag, $rs_row);
            }
        }

        $flag = is_ok($rs_row);

        if (!$flag) {
            $msg = '修改会员等级失败，稍后再试';
            $status = 250;
        } else {
            $msg = '修改会员等级成功';
            $status = 200;
        }

        $this->data->addBody(-140, array(), $msg, $status);
    }


    //标签列表
    public function getTagList()
    {
        $User_TagModel = new User_TagModel();
        $data = $User_TagModel->getTagList();
        $this->data->addBody(-140, $data);
    }

    public function setTag()
    {
        $userIds = request_row('userIds');
        $tagIds = request_row('tagIds');

        $User_TagRecModel = new User_TagRecModel();
        $rs_row = array();
        foreach($userIds as $value){
            foreach($tagIds as $val){
                $cond_row['user_tag_id'] = $val;
                $cond_row['user_id'] = $value;
                $info = $User_TagRecModel->getByWhere($cond_row);
                if(empty($info)){
                    $cond_row['tag_rec_time'] = date('Y-m-d H:i:s', time());
                    $flag = $User_TagRecModel->addRec($cond_row);
                    check_rs($flag, $rs_row);
                }
            }
        }
        $flag = is_ok($rs_row);

        if (!$flag) {
            $msg = '打标签失败，稍后再试';
            $status = 250;
        } else {
            $msg = '打标签成功';
            $status = 200;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

}

?>