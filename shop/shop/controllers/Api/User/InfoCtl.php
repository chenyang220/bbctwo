<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_User_InfoCtl extends Yf_AppController
{
	public $userInfoModel     = null;
	public $userBaseModel     = null;
	public $userResourceModel = null;

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
		
		$this->userInfoModel     = new User_InfoModel();
		$this->userBaseModel     = new User_BaseModel();
		$this->userResourceModel = new User_ResourceModel();

	}
	    //实名认证

	    //实名认证
    public  function  authentication()
    {
    	   header("Content-type:text/html;charset=utf8");
    	   $app_id =request_string('app_id');
    	   $secret = request_string('secret');
		   $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$app_id.'&secret='.$secret.'';
		   $jsonObj = file_get_contents($url);
		   $arr = json_decode($jsonObj,true);
		   $access_token = $arr['access_token'];
		   $FilePaths= request_string('FilePaths');
		   $recitePaths= request_string('recitePaths');
		    $action = 'https://api.weixin.qq.com/cv/ocr/idcard?type=MODE&img_url='.$FilePaths.'&access_token=' . $access_token;
		    $actionb = 'https://api.weixin.qq.com/cv/ocr/idcard?type=MODE&img_url='.$recitePaths.'&access_token=' . $access_token;
		    $data = $this->create_html($params = array(),$action);
		    $datab = $this->create_html($params = array(),$actionb);
		    $data = json_decode($data,true); 
		    $datab = json_decode($datab,true); 

		    $starts= substr($datab['valid_date'],0,strrpos($datab['valid_date'],'-')); 
		    $ends = substr($datab['valid_date'],strrpos($datab['valid_date'],'-')+1); 
	        $key = Yf_Registry::get('paycenter_api_key');
	        $url = Yf_Registry::get('paycenter_api_url');
	        $app_id = Yf_Registry::get('paycenter_app_id');
	        $formvars = array();
	        $formvars['app_id'] = $app_id;
	        $formvars['user_id'] =request_string('user_id');
	        $formvars['user_realname'] = $data['name'];
	        $formvars['user_identity_card'] = $data['id'];
	        $formvars['user_identity_start_time'] =$starts;
	        $formvars['user_identity_end_time'] = $ends;
	        $formvars['user_identity_font_logo'] = $FilePaths;
	        $formvars['user_identity_face_logo'] = $recitePaths;
	        $formvars['pthoto_status'] = 1;
		    $result = get_url_with_encrypt($key, sprintf('%s?ctl=Upload&met=editCertification&typ=json', $url), $formvars);
    }




   public  function create_html($params, $action , $asyn = false) {
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



   public  function  selectuser()
   {
        $key = Yf_Registry::get('paycenter_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $app_id = Yf_Registry::get('paycenter_app_id');

        $formvars = array();
        $formvars['app_id'] = $app_id;
        $formvars['user_id'] = request_string('user_id');
        if(request_string('type')==1)
        {
        $formvars['user_realname'] = request_string('name');
        $formvars['user_identity_card'] = request_string('number');
        $formvars['user_identity_start_time'] = request_string('startime');
        $formvars['user_identity_end_time'] = request_string('endime');
        $formvars['user_identity_type'] = request_string('cerid')+1;
        $result['user_identity_statu'] = $data['data']['user_identity_statu'];
        $result = get_url_with_encrypt($key, sprintf('%s?ctl=Upload&met=editCertification&typ=json', $url), $formvars);
        }else
        {
        $data = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=getUserInfo&typ=json', $url), $formvars);
        $result['user_realname'] = $data['data']['user_realname'];
        $result['user_identity_start_time'] = $data['data']['user_identity_start_time'];
        $bx=substr($data['data']['user_identity_end_time'],0,4);
		$cx=substr($data['data']['user_identity_end_time'],4);
		$domain = strstr($data['data']['user_identity_end_time'], '-');
		if($domain)
		{
			 $result['user_identity_end_time'] = $data['data']['user_identity_end_time'];
		}else
		{
			$result['user_identity_end_time'] = $bx.'-'.substr(chunk_split($cx,2,"-"), 0, -1);	
		}
        $result['user_identity_card'] = $data['data']['user_identity_card'];
        $result['user_identity_font_logo'] = $data['data']['user_identity_font_logo'];
        $result['user_identity_face_logo'] = $data['data']['user_identity_face_logo'];
        $result['user_identity_type'] = $data['data']['user_identity_type']-1;
        $result['user_identity_statu'] = $data['data']['user_identity_statu'];
        }
         $this->data->addBody(-140, $result);	


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
		
		$cond_row = array();
		$sort     = array();
		
		if(request_int('shop_source')){
			$shop_list = $shopBaseModel->getByWhere(array('shop_type'=>request_int('shop_source')));
			$shop_user = array_column($shop_list,'user_id');
			$cond_row['user_id:IN'] = $shop_user;
		}
		
		if ($name)
		{
			if ($type == '1')
			{
				$cond_row['user_id'] = $name;
			}
			else
			{
				$type            = 'user_name:LIKE';
				$cond_row[$type] = '%' . $name . '%';
			}
			
		}

        if($user_active_time) {
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
		$data = $this->userInfoModel->getInfoList($cond_row, $sort, $page, $rows);
		$shopBaseModel = new Shop_BaseModel();
		foreach ($data['items'] as $key => $value) {
			$shop_info = 	$shopBaseModel->getOneByWhere(array('user_id'=>$value['user_id']));
			if(!empty($shop_info)){
				$data['items'][$key]['shop_type'] = $shop_info['shop_type'];
				$data['items'][$key]['shop_status'] = $shop_info['shop_status'];
			}

            $k                 = Yf_Registry::get('shop_api_key');
            $formvars            = array();
            $formvars['user_id'] = $value['user_id'];
            $formvars['app_id'] = Yf_Registry::get('shop_app_id');

            $row = get_url_with_encrypt($k, sprintf('%s?ctl=Api_User_Info&met=getUserInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);

            $data['items'][$key]['identy'] = $row['data'];
		}
		
		$this->data->addBody(-140, $data);

	}


    /**
     * 获取单个会员信息
     *
     * @access public
     * @return json
     */
    public function getUserInfoByName(){
        $uid = request_string('uid');
        $db = new YFSQL();
        $sql = "select * from ucenter_user_info where u_id=" . $uid;

        $u_arr = $db->find($sql);


        if (!$u_arr) {
            return $this -> data -> addBody(-140, array(), __('鉴权UID查无此用户'), 250);
        }
        $ucenter_user_info = current($u_arr);
        $user_id = $ucenter_user_info['user_id'];
        $cond_row['user_id'] = $user_id;
        $data = $this->userInfoModel->getByWhere($cond_row);
        $this->data->addBody(-140, $data);
    }
    //发送站内信
    public function addsendion(){
        $user_name = request_string('send_man');
        $send_content = request_string('send_content');
        $SenderAll = request_string('SenderAll');
        $User_MessageModel    = new User_MessageModel();
        $User_InfoModel = new User_InfoModel();
        $user_name = request_string('send_man');
        $send_content = request_string('send_content');
        if($SenderAll == 1)
        {
            //群发
            $sql  = "SELECT * FROM  yf_user_info";
            $db = new YFSQL();
            $data = $db->find($sql);
            foreach($data as $key => $value)
            {
                $cond_roww['user_message_content'] = $send_content;
                $cond_roww['user_message_send'] = 'admin';
                $cond_roww['user_message_send_id'] = '10001';
                $cond_roww['user_message_receive_id'] = $value['user_id'];
                $cond_roww['user_message_receive'] = $value['user_name'];
                $cond_roww['user_message_time'] = get_date_time();
                $data = $User_MessageModel->addMessage($cond_roww);
            }
        }else{
            //单发
            $res = $User_InfoModel->getOneByWhere(array("user_name"=>$user_name));
            $cond_row['user_message_content'] = $send_content;
            $cond_row['user_message_send'] = 'admin';
            $cond_row['user_message_send_id'] = '10001';
            $cond_row['user_message_receive_id'] = $res['user_id'];
            $cond_row['user_message_receive'] = $user_name;
            $cond_row['user_message_time'] = get_date_time();
            $data = $User_MessageModel->addMessage($cond_row);

        }
        if ($data['status'] == 200)
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
     * 获取会员plus信息
     *
     * @access public
     * @return json
     * @author nsy
     * @date 2019-02-13
     */
    public function getUserPlusFlag(){
        $user_id = request_string('user_id');
        $cond_row['user_id'] = $user_id;
        $plus_mdl = new Plus_UserModel();
        $data = $plus_mdl->getByWhere($cond_row);
        $this->data->addBody(-140, $data);
    }

	/**
	 * 获取修改会员信息
	 *
	 * @access public
	 */
	public function editInfo()
	{
		$user_id              = request_int('user_id');
		$order_row['user_id'] = $user_id;
		
		$data = $this->userInfoModel->getUserInfo($order_row);
		if ($data)
		{
			//会员的钱
			$key                 = Yf_Registry::get('shop_api_key');
			$formvars            = array();
			$formvars['user_id'] = $user_id;
			$formvars['app_id'] = Yf_Registry::get('shop_app_id');
			
			$money_row = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=getUserResourceInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);

			if ($money_row['status'] == '200')
			{
				$money = $money_row['data'];

				$data['user_cash']        = $money[$user_id]['user_money'];
				$data['user_freeze_cash'] = $money[$user_id]['user_money_frozen'];
				
			}
			else
			{
				$data['user_cash']        = 0;
				$data['user_freeze_cash'] = 0;
			}
			
			$re = $this->userResourceModel->getOne($order_row);
			$de = $this->userBaseModel->getOne($order_row);
			
			$data['user_points'] = $re['user_points'];
			$data['user_growth'] = $re['user_growth'];
			$data['user_delete'] = $de['user_delete'];
		}

		$this->data->addBody(-140, $data);
		
	}



	/**
	 * 获取修改会员信息
	 *
	 * @access public
	 */
	public function infoDetail()
	{
		$user_id              = request_int('user_id');
		$order_row['user_id'] = $user_id;
		$shopBaseModel = new Shop_BaseModel();
		$data = $this->userInfoModel->getUserInfo($order_row);
        $shop_info = $shopBaseModel->getOneByWhere(array('user_id' => $data['user_id']));

        if (!empty($shop_info)) {
            $data['shop_type'] = $shop_info['shop_type'];
            $data['shop_status'] = $shop_info['shop_status'];
        }

        $k = Yf_Registry::get('shop_api_key');
        $formvars = array();
        $formvars['user_id'] = $user_id;
        $formvars['app_id'] = Yf_Registry::get('shop_app_id');

        $row = get_url_with_encrypt($k, sprintf('%s?ctl=Api_User_Info&met=getUserInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);

        $data['identy'] = $row['data'];

        //会员积分
        $User_ResourceModel = new User_ResourceModel();
        $user_resource = $User_ResourceModel->getOne($data['user_id']);
        $data['user_points'] = $user_resource['user_points'];
        //会员标签
        $User_TagRecModel = new User_TagRecModel();
        $User_TagModel = new User_TagModel();
        $User_TagRec = $User_TagRecModel->getByWhere(array("user_id"=>$data['user_id']));
        foreach ($User_TagRec as $key => $value) {
        	$User_Tag = $User_TagModel->getOne($value['user_tag_id']);
			$User_TagRec[$key]['user_tag_name_title'] = $User_Tag['user_tag_name'];
        	$User_TagRec[$key]['user_tag_name'] = $this->esub($User_Tag['user_tag_name'],10);
        }
        $data['user_tag'] = array_values($User_TagRec);

        //会员等级
        $User_GradeModel = new User_GradeModel();
        $grade = $User_GradeModel->getOne($data['user_grade']);
        $data['user_grade_con'] = $grade['user_grade_name'];
		if ($data['user_sex'] == 0) {
			$data['user_sex'] = '女';
		} elseif ($data['user_sex'] == 1) {
			$data['user_sex'] = '男';
		} else {
			$data['user_sex'] = '保密';
		}
		if($data['shop_type'] == 1 && $data['shop_status'] == 3){
        		$data['shop_style'] = '商家店铺';
    	}else if($data['shop_type'] == 2 && $data['shop_status'] == 3){
    		$data['shop_style'] = '供货商店铺';
    	}else{
            $data['shop_style'] = '无';
        }
		$this->data->addBody(-140, $data);
		
	}





	/**
	 * 字节截取
	 *
	 * @access public
	 */
	function esub($str, $length = 10) {
	 
	    if($length < 1){
	        return $str;
	    }
	    //计算字符串长度
	    $strlen = (strlen($str) + mb_strlen($str,"UTF-8")) / 2;
	    if($strlen < $length){
	        return $str;
	    }
	    if(mb_check_encoding($str,"UTF-8")){
	        $str = mb_strcut(mb_convert_encoding($str, "GBK","UTF-8"), 0, $length, "GBK");
	        $str = mb_convert_encoding($str, "UTF-8", "GBK");
	    }else{
	        return "不支持的文档编码";
	    }
	    return $str;
	}



	/**
	 * 删除会员标签
	 *
	 * @access public
	 */
	function delTagRec () {
		$tag_rec_id = request_int('tag_rec_id');
		$User_TagRecModel = new User_TagRecModel();
		$User_TagRec = $User_TagRecModel->removeRec($tag_rec_id);
		if ($User_TagRec) {
			$status = 200;
			$msg    = __('success');
		} else {
			$status = 250;
			$msg    = __('failure');
		}
		$this->data->addBody(-140, array(), $msg, $status);
	}

	/**
	 * 远程通过Ucenter修改会员信息
	 *
	 * @access public
	 */
	public function editUserInfoByUcenter()
	{
		$user_id = request_int('user_id');
		$user_name = request_string('user_name');
		//$user_passwd = request_string('user_passwd');
		$user_email    = request_string('user_email');
		$user_realname = request_string('user_realname');
		$user_sex      = request_int('user_sex');
		$user_qq       = request_string('user_qq');
		$user_logo     = request_string('user_logo', request_string('user_avatar'));
		$user_delete   = request_int('user_delete');
		$user_birthday = request_string('user_birthday');
		$user_provinceid = request_int('user_provinceid');
		$user_cityid = request_int('user_cityid');
		$user_areaid = request_int('user_areaid');
		$user_area = request_string('user_area');


		$key = Yf_Registry::get('ucenter_api_key');;
		$url       = Yf_Registry::get('ucenter_api_url');
		$app_id    = Yf_Registry::get('ucenter_app_id');
		$server_id = Yf_Registry::get('server_id');
		//开通ucenter
		//本地读取远程信息
		$formvars              = array();
		$formvars['app_id']    = $app_id;
		$formvars['server_id'] = $server_id;

		$formvars['ctl'] = 'Api_User';
		$formvars['met'] = 'editUserInfo';
		$formvars['typ'] = 'json';

		isset($_REQUEST['user_mobile']) ? $formvars['user_mobile']=request_string('user_mobile') : '';
        isset($_REQUEST['area_code']) ? $formvars['area_code']=request_string('area_code') : '';
		$formvars['user_id']    = $user_id;
		$formvars['user_name']    = $user_name;
		$formvars['user_gender']    = $user_sex;
		$formvars['user_logo']     = $user_logo;
		$formvars['user_delete'] = $user_delete;

		$init_rs = get_url_with_encrypt($key, $url, $formvars);
		if ($init_rs['status'] == 200)
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
	 * 修改会员信息
	 *
	 * @access public
	 */
	public function wxEditUserInfo()
	{

		$user_id = request_int('user_id');
		$data['user_sex']      = request_string('user_gender');
		$User_InfoModel = new User_InfoModel();
		$edit_user_base = $User_InfoModel->editInfo($user_id,$data);
		if ($edit_user_base) {
			$status = 200;
			$msg    = __('success');
		} else {
			$status = 250;
			$msg    = __('failure');
		}
		$this->data->addBody(-140, array(), $msg, $status);
	}


	/**
	 * 修改会员信息
	 *
	 * @access public
	 */
	public function editUserInfo()
	{
		$user_id = request_int('user_id');
		//$user_passwd = request_string('user_passwd');
		$user_email    = request_string('user_email');
		$user_realname = request_string('user_realname');
		$user_sex      = request_int('user_sex');
		$user_qq       = request_string('user_qq');
		$user_logo     = request_string('user_logo', request_string('user_avatar'));
		$user_delete   = request_int('user_delete');
		$user_birthday = request_string('user_birthday');
		$user_provinceid = request_int('user_provinceid');
		$user_cityid = request_int('user_cityid');
		$user_areaid = request_int('user_areaid');
		$user_area = request_string('user_area');
		//$user_report = request_int('user_report');
		//$user_buy = request_int('user_buy');
		//$user_talk = request_int('user_talk');

		//$cond_row['user_passwd'] = md5($user_passwd);
		isset($_REQUEST['user_mobile']) ? $edit_user_row['user_mobile']=request_string('user_mobile') : '';
        isset($_REQUEST['area_code']) ? $edit_user_row['area_code']=request_string('area_code') : '';
		$edit_user_row['user_email']    = $user_email;
		$edit_user_row['user_sex']      = $user_sex;
		$edit_user_row['user_realname'] = $user_realname;
		$edit_user_row['user_qq']       = $user_qq;
		$edit_user_row['user_logo']     = $user_logo;
		$edit_user_row['user_birthday']     = $user_birthday;
		$edit_user_row['user_provinceid']     = $user_provinceid;
		$edit_user_row['user_cityid']     = $user_cityid;
		$edit_user_row['user_areaid']     = $user_areaid;
		$edit_user_row['user_area']     = $user_area;
		//$edit_user_row['user_report'] = $user_report;
		//$edit_user_row['user_buy'] = $user_buy;
		//$edit_user_row['user_talk'] = $user_talk;
		$edit_base_row['user_delete'] = $user_delete;
		
		//开启事物
		$rs_row = array();
		$this->userInfoModel->sql->startTransactionDb();
		
		//if(!empty($cond_row['user_passwd'])){

		//$up= $this->userBaseModel->editBase($user_id,$cond_row);
		//check_rs($up,$rs_row);

		// }
		$update_flag = $this->userBaseModel->editBase($user_id, $edit_base_row);
		
		check_rs($update_flag, $rs_row);
		
		$flag = $this->userInfoModel->editInfo($user_id, $edit_user_row);
		
		check_rs($flag, $rs_row);
		$flag = is_ok($rs_row);

		if ($flag !== false && $this->userInfoModel->sql->commitDb())
		{
			$status = 200;
			$msg    = __('success');
		}
		else
		{
			$this->userInfoModel->sql->rollBackDb();

			$status = 250;
			$msg    = __('failure');
		}

		$data = array();
		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function editUserBtStatus()
	{
		$user_id = request_int('user_id');
		$status = request_int('user_bt_status');

		$edit_row = array();
		$edit_row['user_bt_status'] = $status;
		$update_flag = $this->userInfoModel->editInfo($user_id, $edit_row);

		if ($update_flag)
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
	 * 增加会员
	 *
	 * @access public
	 */
	public function addUserInfo()
	{
		$time          = get_date_time();
		$user_name     = request_string('user_name');
		$user_passwd   = request_string('user_passwd');
		$user_email    = request_string('user_email');
		$user_realname = request_string('user_realname');
		$user_sex      = request_int('user_sex');
		$user_qq       = request_string('user_qq');
		$user_logo     = request_string('user_logo');

		$cond_row['user_account']          = $user_name;
		$edit_user_row['user_name']        = $user_name;
		$edit_user_row['user_email']       = $user_email;
		$edit_user_row['user_sex']         = $user_sex;
		$edit_user_row['user_realname']    = $user_realname;
		$edit_user_row['user_qq']          = $user_qq;
		$edit_user_row['user_logo']        = $user_logo;
		$edit_user_row['user_regtime']     = $time;
		$edit_user_row['user_update_date'] = $time;


		$key = Yf_Registry::get('ucenter_api_key');;
		$url       = Yf_Registry::get('ucenter_api_url');
		$app_id    = Yf_Registry::get('ucenter_app_id');
		$server_id = Yf_Registry::get('server_id');
		//开通ucenter
		//本地读取远程信息
		$formvars              = array();
		$formvars['user_name'] = request_string("user_name");
		$formvars['password']  = request_string("user_passwd");
		$formvars['app_id']    = $app_id;
		$formvars['server_id'] = $server_id;

		$formvars['ctl'] = 'Api';
		$formvars['met'] = 'addUserAndBindAppServer';
		$formvars['typ'] = 'json';

		$init_rs = get_url_with_encrypt($key, $url, $formvars);
		if (200 == $init_rs['status'])
		{
			//本地读取远程信息
			$data['user_id']      = $init_rs['data']['user_id']; // 用户帐号
			$data['user_account'] = request_string("user_name"); // 用户帐号
			$data['user_delete']  = 0; // 用户状态

			$user_id = $this->UserBaseModel->addBase($data, true);//初始化用户信息
			
			$User_InfoModel = new User_InfoModel();
			$info_flag      = $User_InfoModel->addInfo($user_id, $edit_user_row);

			$user_resource_row                = array();
			$user_resource_row['user_id']     = $user_id;
			$user_resource_row['user_points'] = Web_ConfigModel::value("points_reg");//注册获取积分;

			$User_ResourceModel          = new User_ResourceModel();
			$res_flag                    = $User_ResourceModel->addResource($user_resource_row);
			$User_PrivacyModel           = new User_PrivacyModel();
			$user_privacy_row['user_id'] = $user_id;
			$privacy_flag                = $User_PrivacyModel->addPrivacy($user_privacy_row);
			//积分
			$user_points_row['user_id']           = $user_id;
			$user_points_row['user_name']         = request_string("user_name");
			$user_points_row['class_id']          = Points_LogModel::ONREG;
			$user_points_row['points_log_points'] = $user_resource_row['user_points'];
			$user_points_row['points_log_time']   = get_date_time();
			$user_points_row['points_log_desc']   = '会员注册';
			$user_points_row['points_log_flag']   = 'reg';
			$Points_LogModel                      = new Points_LogModel();
			$Points_LogModel->addLog($user_points_row);

			if ($user_id)
			{

				$msg    = 'success';
				$status = 200;
				
			}
			else
			{
				$msg    = 'failure';
				$status = 250;
			}


		}
		else
		{
			$msg    = __("该会员名已存在！");
			$status = 250;
		}


		$data = array();
		$this->data->addBody(-140, $data, $msg, $status);
	}

    public function getSubUser()
    {
        $sub_user_id = request_int('sub_user_id');
        $user_id = request_int('user_id');

        $User_SubUserModel = new User_SubUserModel();
        $cond_row = array();
        $cond_row['sub_user_id'] = $sub_user_id;
        $cond_row['sub_user_active'] = User_SubUserModel::IS_ACTIVE;
        if ($user_id) {
            $cond_row['user_id'] = $user_id;
        }
        $sub_user = $User_SubUserModel->getByWhere($cond_row);
        if ($sub_user) {
            $user_ids = array_column($sub_user, 'user_id');

            //查找用户名
            $User_BaseModel = new  User_BaseModel();
            $user_base = $User_BaseModel->getByWhere(array('user_id:IN' => $user_ids));
            foreach ($sub_user as $k => $v) {
                $sub_user[$k]['user_account'] = $user_base[$v['user_id']]['user_account'];
            }

            $data['sub'] = $sub_user;
        } else {
            $data['sub'] = array();
        }
        $count = count($sub_user);
        $data['count'] = $count;
        $this->data->addBody(-140, $data);
    }

	public function checkSubUser()
	{
		$user_id = request_int('user_id');
		$subuser_id = request_int('sub_user_id');

		$User_SubUserModel = new User_SubUserModel();
		$cond_row['user_id'] = $user_id;
		$cond_row['sub_user_id'] = $subuser_id;
		$cond_row['sub_user_active'] = User_SubUserModel::IS_ACTIVE;
		$sub_user = $User_SubUserModel->getByWhere($cond_row);

		$this->data->addBody(-140, $sub_user);
	}

	//导出用户信息
    public function getInfoExcel()
    {
        $search_name = request_string("search_name");
        $user_type = request_int("user_type");
        $shop_source = request_int("shop_source");
        $limit = request_int("limit");
        $start_limit = request_int("start_limit");
        $is_limit = request_int("is_limit");
        $cond_row = array();
        if ($search_name)
        {
            $cond_row['search_name'] = $search_name;
        }
        if ($user_type)
        {
            $cond_row['user_type'] = $user_type;
        }
        if ($shop_source)
        {
            $cond_row['shop_source'] = $shop_source;
        }
        if ($limit)
        {
            $cond_row['limit'] = $limit;
        }
        if ($limit)
        {
            $cond_row['start_limit'] = $start_limit;
        }
        if ($is_limit)
        {
            $cond_row['is_limit'] = $is_limit;
        }
        $User_InfoModel = new User_InfoModel();
        $limits = $User_InfoModel->getCounts();
        if($is_limit)
        {
            $this->export($cond_row);
        }else{
            //保存地址
            $path = ROOT_PATH . '/shop/data/download/';
            $file_template = $path . time();//临时文件
            $url = $file_template . '/';
            for ($i = 0; $i < $limits; $i++) {
                $cond_row['limits'] = $i;
                $this->export($cond_row, $i, $url);
            }
            //打包
            $zip = new ZipArchive();
            $down_name = 'user_info.zip';
            $file_name = $path . $down_name;
            if ($zip->open($file_name, ZipArchive::CREATE) === TRUE) {
                $this->addFileToZip($url, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
                $zip->close(); //关闭处理的zip文件
            }
            $fp = fopen($file_name, "r");
            $file_size = filesize($file_name);//获取文件的字节
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:" . $file_size);
            Header("Content-Disposition: attachment; filename=$down_name");
            $buffer = 1024; //设置一次读取的字节数，每读取一次，就输出数据（即返回给浏览器）
            $file_count = 0; //读取的总字节数
            //向浏览器返回数据 如果下载完成就停止输出，如果未下载完成就一直在输出。根据文件的字节大小判断是否下载完成
            while(!feof($fp) && $file_count < $file_size) {
                $file_con = fread($fp, $buffer);
                $file_count += $buffer;
                echo $file_con;
            }
            fclose($fp);
            //下载完成后删除压缩包，临时文件夹
            if ($file_count >= $file_size) {
                unlink($file_name);
                exec("rm -rf ".$file_template);
            }
        }
    }

    /**压缩文件夹
     * @param $path
     * @param $zip
     */
    function addFileToZip($path,$zip){
        $handler = opendir($path); //打开当前文件夹由$path指定。
        while(($filename = readdir($handler)) !== false){
            if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
                if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                    addFileToZip($path."/".$filename, $zip);
                }else{ //将文件加入zip对象
                    $zip->addFile($path."/".$filename,$filename);
                }
            }
        }
        @closedir($path);
    }

    public function export($cond_row,$i="",$url="")
    {
        $User_InfoModel = new User_InfoModel();
        $con = $User_InfoModel->getUserInfoExcel($cond_row);
        $tit = array(
            "序号",
            "会员ID",
            "会员名称",
            "会员邮箱",
            "会员手机",
            "会员性别",
            "真实姓名",
            "出生日期",
            "注册时间",
            "商家类型",
            "最后登录时间",
        );
        $key = array(
            "user_id",
            "user_name",
            "user_email",
            "user_mobile",
            "user_sex",
            "user_realname",
            "user_birthday",
            "user_regtime",
            "shop_type",
            "lastlogintime",
        );
        if(isset($i)&& is_numeric($i)){

            $this->download_excel("会员信息".$i, $tit, $con, $key,$url);

        }else{
            $this->excel("会员信息", $tit, $con, $key);
        }
    }

    public function download_excel($title,$tit,$con,$key,$url){
        ob_end_clean();   //***这里再加一个
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("mall_new");
        $objPHPExcel->getProperties()->setLastModifiedBy("mall_new");
        $objPHPExcel->getProperties()->setTitle($title);
        $objPHPExcel->getProperties()->setSubject($title);
        $objPHPExcel->getProperties()->setDescription($title);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $letter = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T'
        );
        foreach ($tit as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[$k] . "1", $v);
        }
        foreach ($con as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[0] . ($k + 2), $k + 1);
            foreach ($key as $k2 => $v2)
            {

                $objPHPExcel->getActiveSheet()->setCellValue($letter[$k2 + 1] . ($k + 2), $v[$v2]);
            }
        }
        ob_end_clean();   //***这里再加一个
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        if (!file_exists($url) && !mkdir($url, 0777, true)) {
            return false;
        }
        $url = $url.time().'.xls';
        $objWriter->save($url);

    }

    function excel($title, $tit, $con, $key)
    {
        ob_end_clean();   //***这里再加一个
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("mall_new");
        $objPHPExcel->getProperties()->setLastModifiedBy("mall_new");
        $objPHPExcel->getProperties()->setTitle($title);
        $objPHPExcel->getProperties()->setSubject($title);
        $objPHPExcel->getProperties()->setDescription($title);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle($title);
        $letter = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T'
        );
        foreach ($tit as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[$k] . "1", $v);
        }
        foreach ($con as $k => $v)
        {
            $objPHPExcel->getActiveSheet()->setCellValue($letter[0] . ($k + 2), $k + 1);
            foreach ($key as $k2 => $v2)
            {

                $objPHPExcel->getActiveSheet()->setCellValue($letter[$k2 + 1] . ($k + 2), $v[$v2]);
            }
        }
        ob_end_clean();   //***这里再加一个
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$title.xls\"");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');exit();
    }

    public function voucher()
    {
        $cond_row = array();
        $state = request_int('state');
        $user_id = request_int('user_id');

        if(!$user_id) {
            return $this->data->addBody(-140, array(), __('缺少user_id'), 250);
        }

        $cond_row['voucher_owner_id'] = $user_id;
        if ($state) {
            $cond_row['voucher_state'] = $state;
        } else {
            $cond_row['voucher_state:!='] = Voucher_BaseModel::RECOVER;
        }

        /**
         * • 优惠券排列方式
         * 1，根据优惠金额以少至多一次排序；
         * 2，若优惠金额一致再根据满足金额以多至少依次排序；
         * 3，若满足金额也一致，则根据过期时间由近及远排序；
         * 4，“未使用”与“已失效”排列方式一致。
         *
         * Shop：
         * 未达到限额的在前面，已经过期的在后面
         * 优惠金额从小到大显示 -即：价格正序；
         * 未达到限额的按照满足金额从小到大显示 -即：价格正序；
         * 已经过期的按照有效期由近及远显示-即：有效期倒序。
         */
        $order_row = array('voucher_state' => 'asc', 'voucher_price' => 'asc', 'voucher_limit' => 'desc', 'voucher_end_date' => 'asc');

        $Voucher_BaseModel = new Voucher_BaseModel();

        $rows = request_int('rows',20);
        $page = request_int('page',1);
        if ($state == 2) {
            //把不能用的都查出来
            unset($cond_row['voucher_state']);
            $cond_row['voucher_state:NOT IN'] = [Voucher_BaseModel::UNUSED,Voucher_BaseModel::RECOVER];
        }
        $data = $Voucher_BaseModel->getUserVoucherList($cond_row, $order_row, $page, $rows);
        $data['items'] = $this->getVoucherData($data['items']);
        if ($data['page'] < $data['total']) {
            $data['hasmore'] = true;
        } else {
            $data['hasmore'] = false;
        }

        $data['page_total'] = $data['total'];
        return $this->data->addBody(-140, $data);

    }

    /**
     *  代金券数据
     */
    private function getVoucherData($data)
    {
        if (!is_array($data) || !$data) {
            return array();
        }
        $shop_id_row = array_column($data, 'voucher_shop_id');
        if (!$shop_id_row) {
            return array();
        }
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_rows = $Shop_BaseModel->getBase($shop_id_row);
        if (!$shop_rows) {
            return array();
        }
        foreach ($data as $key => $value) {
            $data[$key]['voucher_shop_name'] = $shop_rows[$value['voucher_shop_id']]['shop_name'];
            $data[$key]['voucher_shop_logo'] = $shop_rows[$value['voucher_shop_id']]['shop_logo'];
            $data[$key]["voucher_state_label"] = __(Voucher_BaseModel::$voucherState[$value["voucher_state"]]);

            $data[$key]["voucher_limit"] = number_format($data[$key]["voucher_limit"]);
            $data[$key]["voucher_end_date"] = date('Y-m-d', strtotime($data[$key]["voucher_end_date"]) + 1);
            $data[$key]["voucher_start_date"] = date('Y-m-d', strtotime($data[$key]["voucher_start_date"]));
            $data[$key]["v_end_date"] = date('Y.m.d', strtotime($data[$key]["voucher_end_date"]) + 1);
            $data[$key]["v_start_date"] = date('Y.m.d', strtotime($data[$key]["voucher_start_date"]));
        }
        return $data;
    }


    /*获取卖家领取的平台优惠券列表*/
    public function redPacket()
    {
        $cond_row  = array();
        $order_row = array();

        $user_id = request_int('user_id');
        if(!$user_id) {
            return $this->data->addBody(-140, array(), __('缺少user_id'), 250);
        }
        //分页
        $Yf_Page            = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows',12);
        $rows               = $Yf_Page->listRows;
        $offset             = request_int('firstRow', 0);
        $page               = ceil_r($offset / $rows);

        $cond_row['redpacket_owner_id']    = $user_id;
        //根据优惠券状态搜索
        $redpacket_state = request_int('state');
        if($redpacket_state)
        {
            $cond_row['redpacket_state']    = $redpacket_state;
        }
        $cond_row['redpacket_state:!='] = RedPacket_BaseModel::RECOVER;

        $order_row = array('redpacket_state' => 'asc', 'redpacket_price' => 'asc', 'redpacket_t_orderlimit' => 'desc', 'redpacket_end_date' => 'asc');

        $RedPacket_BaseModel = new RedPacket_BaseModel();
        $RedPacket_TempModel = new RedPacket_TempModel();
        $data =  $RedPacket_BaseModel->getRedPacketList($cond_row, $order_row, $page,  $rows);

        foreach($data['items'] as $key=>$value)
        {
            $data['items'][$key]['start_data']=substr($value['redpacket_start_date'],0,10);
            $data['items'][$key]['end_data']=substr($value['redpacket_end_date'],0,10);
            $cond_red[] = $value['redpacket_t_id'];
            $da =  $RedPacket_TempModel->getRedPacketTempInfoById($value['redpacket_t_id']);

            $data['items'][$key]['redpacket_t_img'] = $da['redpacket_t_img'];
        }

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav           = $Yf_Page->prompt();


        return $this->data->addBody(-140, $data);

    }


    //积分明细
    public function points()
    {
        $op = request_string('op');
        $user_id = request_int('user_id');
        if(!$user_id) {
            return $this->data->addBody(-140, array(), __('缺少user_id'), 250);
        }

        $Yf_Page           = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows',10);
        $rows              = $Yf_Page->listRows;
        $offset            = request_int('firstRow', 0);

        $page              = ceil_r($offset / $rows);

        $Points_OrderModel      = new Points_OrderModel();

        if ($op == 'getPointsOrder')//兑换记录 积分订单
        {

            $state                         = request_int('state');
            $cond_row['points_buyerid']    = $user_id;
            if($state){
                $cond_row['points_orderstate'] = $state;
            }

            $order_row['points_order_id'] = 'DESC';

            $data = $Points_OrderModel->getPointsOrderListByWhere($cond_row, $order_row, $page, $rows);

            $Yf_Page->totalRows = $data['totalsize'];

            $express = new ExpressModel;
            foreach($data['items'] as $key=>$val){
                $express_id=$express->getOneByWhere(['express_name'=>$val['points_logistics']]);

                $data['items'][$key]['points_express_id']=$express_id['express_id'];
            }

            $page_nav           = $Yf_Page->prompt();
        }
        else
        {

            $cond_row['user_id'] = $user_id;
            $start_date         = request_string("start_date");
            $end_date            = request_string("end_date");
            $class_id            = request_string("class_id");
            $des                 = request_string("des");
            $class               = "";
            //积分获取的默认设置
            $web                    = array();
            $web['points_reg']      = Web_ConfigModel::value("points_reg");//注册获取积分
            $web['points_login']    = Web_ConfigModel::value("points_login");//登陆获取积分
            $web['points_evaluate'] = Web_ConfigModel::value("points_evaluate");//评论获取积分
            $web['points_recharge'] = Web_ConfigModel::value("points_recharge");//订单每多少获取多少积分
            $web['points_order']    = Web_ConfigModel::value("points_order");//订单每多少获取多少积分

            $classId = Points_LogModel::$classId;

            $order_row = array();
            $order_row = array('points_log_time' => 'DESC');

            if ($start_date)
            {
                $cond_row['points_log_time:>='] = $start_date;
            }
            if ($end_date)
            {
                $cond_row['points_log_time:<='] = $end_date;
            }
            if ($class_id)
            {
                $cond_row['class_id'] = $class_id;

                $class = __(Points_LogModel::$classId[$class_id]);
            }
            if ($des)
            {
                $type            = 'points_log_desc:LIKE';
                $cond_row[$type] = '%' . $des . '%';
            }
            $cond_row['points_log_points:!='] = 0;

            $Points_LogModel       = new Points_LogModel();

            $data = $Points_LogModel->getPointsLogList($cond_row, $order_row, $page, $rows);

            $data['hasmore'] = $page >= $data['total'] ?false:true;

            $Yf_Page->totalRows = $data['totalsize'];

            $page_nav           = $Yf_Page->prompt();

            $data['web'] = $web;

            $User_ResourceModel = new User_ResourceModel();
            $data['points']          = $User_ResourceModel->getOne($user_id);
        }

        $this->data->addBody(-140, $data);

    }


    //评价列表
    public function evaluation()
    {
        $user_id = request_int('user_id');

        if(!$user_id) {
            return $this->data->addBody(-140, array(), __('缺少user_id'), 250);
        }

        $rows = request_int('rows',10);
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        //获取买家的所有评论
        $Goods_EvaluationModel = new Goods_EvaluationModel();
        $goods_evaluation_row = array();
        $goods_evaluation_row['user_id'] = $user_id;
        $data = $Goods_EvaluationModel -> getEvaluationByUser($goods_evaluation_row, array(), $page, $rows);

        $this->data->addBody(-140, $data);

    }
	
}

?>