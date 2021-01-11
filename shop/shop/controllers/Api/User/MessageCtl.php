<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_User_MessageCtl extends Yf_AppController
{
	public $userMessageModel = null;
	
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
		
		$this->userMessageModel = new User_MessageModel();
	}


	/**
	 * 消息页面
	 *
	 * @access public
	 */
	public function getMessageList()
	{

		$page = request_int('page');
		$rows = request_int('rows');
		$type = request_string('user_type');
		$name = request_string('search_name');
		
		$cond_row = array();
		$sort     = array();
		$sub_site_id = request_int('sub_site_id');
		if ($sub_site_id > 0) {
            //获取站点信息
            $Sub_SiteModel = new Sub_SiteModel();
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $cond_row_district['district_id:IN'] = $sub_site_district_ids;
            }
            $User_InfoModel = new User_InfoModel();
			$user_info = $User_InfoModel->getByWhere($cond_row_district);
			$user_id = array_column($user_info,'user_id');
			$cond_row['user_message_send_id:IN'] = $user_id;
        }
        
		if ($name)
		{
			if ($type == 1)
			{
				$type = 'user_message_send:LIKE';
			}
			else
			{
				$type = 'user_message_receive:LIKE';
				
			}
			$cond_row[$type] = '%' . $name . '%';
		}
		
		$data = $this->userMessageModel->getMessageList($cond_row, $sort, $page, $rows);
		
		$this->data->addBody(-140, $data);
	}
	
}


?>