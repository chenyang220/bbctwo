<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class User_BaseCtl extends AdminController
{
	public $dataUserModel = null;

	/**
	 * 初始化方法，构造函数
	 *
	 * @access public
	 */
	public function init()
	{
		//include $this->view->getView();
		$this->dataUserModel = new User_BaseModel();
	}

	/**
	 * 列表数据
	 *
	 * @access public
	 */
	public function userList()
	{
		$ctl = 'User';
		$met = 'listUser';
		$data = $this->getUrl($ctl, $met);
	}

	/**
	 * 列表数据
	 *
	 * @access public
	 */
	public function editStatus()
	{
		$ctl = 'User';
		$met = 'change';

		$data = $this->getUrl($ctl, $met);
	}

	/**
	 * 列表数据
	 *
	 * @access public
	 */
	public function allUser()
	{
		include $this->view->getView();
	}

	/**
	 * 读取
	 *
	 * @access public
	 */
	public function right()
	{
		$user_id = Perm::$userId;

		switch ($_REQUEST['action'])
		{
			case 'isMaxShareUser' :
				$this->isMaxShareUser();
				break;
			case 'auth2UserCancel' :
				$this->auth2UserCancel();
				break;
			case 'auth2User' :
				$this->auth2User();
				break;
			case 'queryAllUserRights' :
				$this->queryAllUserRights();
				break;
			default :
				break;
		}
	}

	/**
	 * 读取
	 *
	 * @access public
	 */
	public function auth2UserCancel()
	{
		$data['user_id'] = $_REQUEST['user_id']; // 用户id

		$data['user_delete'] = 0; // 是否被封禁，0：未封禁，1：封禁


		$user_id = $_REQUEST['user_id'];
		$data_rs = $data;

		unset($data['user_id']);

		$flag = $this->dataUserModel->editUser($user_id, $data);
		$this->data->addBody(-140, $data_rs);
	}

	/**
	 * 读取
	 *
	 * @access public
	 */
	public function auth2User()
	{
		$data['user_id'] = $_REQUEST['user_id']; // 用户id

		$data['user_delete'] = 1; // 是否被封禁，0：未封禁，1：封禁


		$user_id = $_REQUEST['user_id'];
		$data_rs = $data;

		unset($data['user_id']);

		$flag = $this->dataUserModel->editUser($user_id, $data);
		$this->data->addBody(-140, $data_rs);
	}

	/*
	 * 2016-5-16
	 * 权限控制
	 */
	public function index()
	{
		include $this->view->getView();
	}

	public function manage()
	{
		include $this->view->getView();
	}


	public function exportOrder()
	{
		include $this->view->getView();
	}

	/**
     * 导出Excel功能
     * @param array $header 头部标题
     * @param array $data 数据
     * @param string $file_name 文件名
     */
   function exportExcel($header,$data,$file_name=''){
       !$file_name && $file_name = date("Y-m-d").".xls";
       //组装头部标题
        $head_txt = "<tr>";
        foreach ($header as $v) {
            $head_txt .= "<td>$v</td>";
        }
        $head_txt .= "</tr>";
        $html = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";
        $html .="<table border=1>" . $head_txt;
        $html .= '';
        //组装实体数据部分
        foreach ($data as $key => $rt) {
            $html .= "<tr>";
            $g = $key+1;
            $html .= "<td >$g</td>";
            foreach ($rt as $v) {
                
                $html .= "<td >{$v}</td>";
            }
            $html .= "</tr>\n";
        }
        $html .= "</table></body></html>";
        header("Content-Type: application/vnd.ms-excel; name='excel'");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $file_name);
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        exit($html);
    }

	/**
     * 用户表导出逻辑处理
     *
     * @access public
     */
    public function getUserExcel()
    {

        ob_get_clean();
        $skey = request_string('skey');
        $is_limit = request_string('is_limit');
      	$type = request_string('type');
        $limit = request_string('limit');
        $start_limit = request_string('start_limit');
        $user_name = request_string('user_name','');
        $cond_row = array();
        $cond_row['user_reg_time'] = 'desc';
        $cond_row['is_limit'] = $is_limit;
        $cond_row['user_name'] = $user_name;
        if ($is_limit && $type != 1) {
            if($limit)
            {
                $cond_row['limit'] = $limit;
            }
            $cond_row['start_limit'] = $start_limit;
        }
        $header = array(
                "序号",
                "用户名",
                "真实姓名",
                "性别",
                "手机号码",
                "邮箱",
                "地址",
                "生日",
                "注册时间",
                "上次登录时间",
                "登陆次数",
                "状态",
            );
        $key = Yf_Registry::get('ucenter_api_key');
        $url       = Yf_Registry::get('ucenter_api_url');
        $app_id    = Yf_Registry::get('ucenter_app_id');
        //本地读取远程信息
        $formvars              = array();
        $formvars = $cond_row;
        $formvars['app_id']    = $app_id;
        $formvars['server_id'] = $server_id;
        $init_rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User&met=getUserExcelMsg&typ=json',$url), $formvars);
        if ($init_rs['status'] == 200) {
            $data = array_values($init_rs['data']);
        } else {
            die('导出失败！');
        }
        //导出全部
        if($type){
            $this->exportExcel($header,$data);
            die('导出成功！');
        }
        //导出当前页
        if ($is_limit) {
            $this->exportExcel($header,$data);
            die('导出成功！');
        } else {
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
            $down_name = 'order_info.zip';
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
        $key = Yf_Registry::get('ucenter_api_key');
        $url       = Yf_Registry::get('ucenter_api_url');
        $app_id    = Yf_Registry::get('ucenter_app_id');
        //本地读取远程信息
        $formvars              = array();
        $formvars['user_name'] = $_REQUEST['user_account'];
        $formvars['password']  = $_REQUEST['user_password'];
        $formvars['app_id']    = $app_id;
        $formvars['server_id'] = $server_id;
        $init_rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User&met=getUserExcelMsg&typ=json',$url), $formvars);
        if ($init_rs['status'] == 200) {
            $con = array_values($init_rs['data']);
        } else {
            die('导出失败！');
        }
        $tit = array(
                "序号",
                "用户名",
                "真实姓名",
                "性别",
                "手机号码",
                "邮箱",
                "地址",
                "生日",
                "注册时间",
                "上次登录时间",
                "登陆次数",
                "状态",
            );
        $key = array(
            "user_name",
            "user_truename",//订单来源
            "user_gender",
            "user_mobile",
            "user_email",//订单状态
            "user_area",
            "user_birth",
            "user_reg_time",
            "user_lastlogin_time",
            "user_count_login",
            "user_state",
        );
        if(isset($i)&& is_numeric($i)){
            $this->download_excel("用户列表信息".$i, $tit, $con, $key,$url);
        }else{
            $this->excel("用户列表信息", $tit, $con, $key);
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
        $objWriter->save('php://output');

    }
	/**
	 * 添加
	 *
	 * @access public
	 */
	public function add()
	{
		$key = Yf_Registry::get('ucenter_api_key');;
		$url       = Yf_Registry::get('ucenter_api_url');
		$app_id    = Yf_Registry::get('ucenter_app_id');
		$server_id = Yf_Registry::get('server_id');

		//验证是否已达到共享上限
		/*
		$formvars['app_id']    = $app_id;
		$formvars['server_id'] = $server_id;
		$formvars['ctl']       = 'Api';
		$formvars['met']       = 'getUserAppServerInfo';
		$formvars['typ']       = 'json';
		$UserAppServerInfo     = get_url_with_encrypt($key, $url, $formvars);

		$user_list = $this->dataUserModel->get('*');

		if ($UserAppServerInfo['data']['account_num'] - count($user_list) <= 0)
		{
			return $this->data->addBody(-140, array(), '共享人数已达到上限！', 250);
		}
		*/

		//开通ucenter
		//本地读取远程信息
		$formvars              = array();
		$formvars['user_name'] = $_REQUEST['user_account'];
		$formvars['password']  = $_REQUEST['user_password'];
		$formvars['app_id']    = $app_id;
		$formvars['server_id'] = $server_id;

		$formvars['ctl'] = 'Api';
		$formvars['met'] = 'addUserAndBindAppServer';
		$formvars['typ'] = 'json';

		$init_rs = get_url_with_encrypt($key, $url, $formvars);

		if (200 == $init_rs['status'])
		{
			//本地读取远程信息
			$data['user_id']         = $init_rs['data']['user_id']; // 用户帐号
			$data['user_account']    = $_REQUEST['user_account']; // 用户帐号
			$data['user_password']   = md5($_REQUEST['user_password']); // 密码：使用用户中心-此处废弃
			$data['user_delete']     = 0; // 用户状态
			$data['rights_group_id'] = $_REQUEST['rights_group_id']; // 用户权限组

			$user_id                    = $this->dataUserModel->addBase($data, true);
			$this->baseRightsGroupModel = new Rights_GroupModel();
			$data_rights                = $this->baseRightsGroupModel->getRightsGroupList();
			$data_rights                = $data_rights['items'];

			foreach ($data_rights as $key => $val)
			{
				if ($val['rights_group_id'] == $data['rights_group_id'])
				{
					$data['rights_group_name'] = $val['rights_group_name'];
				}
			}
			$data['user_id'] = $user_id;

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
			$data   = array();
			$msg    = $init_rs['msg'];
			$status = 250;
		}


		$this->data->addBody(-140, $data, $msg, $status);
	}

	/**
	 * 修改
	 *
	 * @access public
	 */
	public function edit()
	{
		$data['user_account'] = $_REQUEST['user_account']; // 用户帐号
		//$data['user_realname']   = $_REQUEST['user_realname']; // 真实姓名
		$data['rights_group_id'] = $_REQUEST['rights_group_id']; // 用户权限组
		$user_id                 = $_REQUEST['user_id'];


		if (!empty($_REQUEST['user_password']))
		{

			$data['user_password'] = $_REQUEST['user_password']; // 密码
			$user_dat              = current($this->dataUserModel->getBase($user_id));

			/*
			if ($user_dat['user_password'] !== $data['user_password'])
			{

			}
			*/

			$key = Yf_Registry::get('ucenter_api_key');;
			$url       = Yf_Registry::get('ucenter_api_url');
			$app_id    = Yf_Registry::get('ucenter_app_id');
			$server_id = Yf_Registry::get('server_id');

			$formvars['app_id']        = $app_id;
			$formvars['server_id']     = $server_id;
			$formvars['ctl']           = 'Api';
			$formvars['met']           = 'resetPasswd';
			$formvars['typ']           = 'json';
			$formvars['user_account']  = $data['user_account'];
			$formvars['user_password'] = $data['user_password'];
			$formvars['from']          = 'shop';
			$resetPasswd               = get_url_with_encrypt($key, $url, $formvars);

			if ($resetPasswd['status'] != 200)
			{
				return $this->data->addBody(-140, $data, $resetPasswd['msg'], 250);
			}


			$data['user_password'] = md5($_REQUEST['user_password']); // 密码：使用用户中心-此处废弃

		}

		$data_rs            = $data;
		$data_rs['user_id'] = $user_id;
		$flag               = $this->dataUserModel->editUser($user_id, $data);

		if ($flag !== false)
		{
			$this->baseRightsGroupModel = new Rights_GroupModel();
			$data_rights                = $this->baseRightsGroupModel->getRightsGroupList();
			$data_rights                = $data_rights['items'];

			foreach ($data_rights as $key => $val)
			{
				if ($val['rights_group_id'] == $data_rs['rights_group_id'])
				{
					$data_rs['rights_group_name'] = $val['rights_group_name'];
				}
			}

			$this->data->addBody(-140, $data_rs);
		}
	}

	//
	public function getShopUserList()
	{

		$key            = Yf_Registry::get('ucenter_api_key');
		$url            = Yf_Registry::get('ucenter_api_url');
		$data['app_id'] = Yf_Registry::get('ucenter_app_id');
		$data['ctl']    = 'Api';
		$data['met']    = 'getAppServerList';
		$data['typ']    = 'json';
		$data['rows']   = '999999999999';
		$data['server_id']     = request_int('id', 0);
		$data['request_app_id']     = Yf_Registry::get('shop_app_id');;
		$data['cloud_type']     =  request_int('cloud_type');

		$result = get_url_with_encrypt($key, $url, $data);

		$data['page']      = $result['data']['page'];
		$data['records']   = $result['data']['records'];
		$data['total']     = $result['data']['total'];
		$data['totalsize'] = $result['data']['totalsize'];
		$data['items']     = $result['data']['items'];

		foreach ($data['items'] as $key => $value)
		{
			$data['items'][$key]['id'] = $value['server_id'];
			if ($value['server_state'] == 0)
			{
				$data['items'][$key]['delete'] = true;
			}
			elseif ($value['server_state'] == 1)
			{
				$data['items'][$key]['delete'] = false;
			}
		}

		$msg    = $result['msg'];
		$status = $result['status'];

		$this->data->addBody(-140, $data, $msg, $status);
	}


	//
	public function getErpUserList()
	{
		$key            = Yf_Registry::get('ucenter_api_key');
		$url            = Yf_Registry::get('ucenter_api_url');
		$data['app_id'] = Yf_Registry::get('ucenter_app_id');
		$data['ctl']    = 'Api';
		$data['met']    = 'getAppServerList';
		$data['typ']    = 'json';
		$data['rows']   = '999999999999';

		$data['server_id']     = request_int('id', 0);
		$data['request_app_id']     = Yf_Registry::get('erp_app_id');;

		$result = get_url_with_encrypt($key, $url, $data);

		$data['page']      = $result['data']['page'];
		$data['records']   = $result['data']['records'];
		$data['total']     = $result['data']['total'];
		$data['totalsize'] = $result['data']['totalsize'];
		$data['items']     = $result['data']['items'];

		foreach ($data['items'] as $key => $value)
		{
			$data['items'][$key]['id'] = $value['server_id'];
			if ($value['server_state'] == 0)
			{
				$data['items'][$key]['delete'] = true;
			}
			elseif ($value['server_state'] == 1)
			{
				$data['items'][$key]['delete'] = false;
			}
		}

		$msg    = $result['msg'];
		$status = $result['status'];

		$this->data->addBody(-140, $data, $msg, $status);
	}



	public function change()
	{
		$key            = Yf_Registry::get('ucenter_api_key');
		$url            = Yf_Registry::get('ucenter_api_url');
		$data['app_id'] = Yf_Registry::get('ucenter_app_id');
		$data['request_app_id'] = request_int('request_app_id', 0);;
		$data['ctl']    = 'Api';
		$data['met']    = 'virifyUserAppServer';
		$data['typ']    = 'json';

		if ($_REQUEST['server_status'] == 0)
		{
			$data['server_state'] = 1;
			$data['server_id']    = $_REQUEST['id'];
			$data['user_name']    = '';
			//$data['server_state'] = 1;//$_REQUEST['server_status'];
			$result = get_url_with_encrypt($key, $url, $data);
			if ($result)
			{
				if ($result['status'] == 200)
				{
					$status = 200;
					$msg    = 'success';
				}
				elseif ($result['status'] == 250)
				{
					$status = 250;
					$msg    = $result['msg'];
				}
			}
			else
			{
				$status = 250;
				$msg    = 'failure';
			}
		}
		else
		{
			$status = 250;
			$msg    = '该用户已经开通服务';
		}

		$data = array();
		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function save()
	{
		$data = $_REQUEST;

		if (request_int('service_id'))
		{
			$id = $_REQUEST['service_id'];
			unset($data['service_id']);

			$key               = Yf_Registry::get('ucenter_api_key');
			$url               = Yf_Registry::get('ucenter_api_url');
			$data['app_id']    = Yf_Registry::get('ucenter_app_id');
			$data['request_app_id'] = Yf_Registry::get('shop_app_id');
			$data['ctl']       = 'Api';
			$data['met']       = 'editUserAppServer';
			$data['typ']       = 'json';
			$data['server_id'] = $id;
			$result            = get_url_with_encrypt($key, $url, $data);

			if ($result)
			{
				if ($result['status'] == 200)
				{
					$status = 200;
					$msg    = 'success';
				}
				else
				{
					$status = 250;
					$msg    = $result['msg'];
				}
			}
			else
			{
				$status = 250;
				$msg    = 'failure';
			}
		}
		else
		{
			$key            = Yf_Registry::get('ucenter_api_key');
			$url            = Yf_Registry::get('ucenter_api_url');
			$data['app_id'] = Yf_Registry::get('ucenter_app_id');

			$data['ctl']    = 'Api';
			$data['met']    = 'addUserAppServer';
			$data['typ']    = 'json';

			$result = get_url_with_encrypt($key, $url, $data);
			if ($result)
			{
				if ($result['status'] == 200)
				{
					$status = 200;
					$msg    = 'success';
				}
				else
				{
					$status = 250;
					$msg    = $result['msg'];
				}
			}
			else
			{
				$status = 250;
				$msg    = 'failure';
			}
		}
		$data = array();
		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function details()
	{
		$data = $this->getUrl('User','details');
		include $this->view->getView();
	}
}

?>