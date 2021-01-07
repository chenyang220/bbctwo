<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}
/**
 * @author   fuzhehao
 */
class Api_User_PlusCtl extends Yf_AppController
{
	public $plusManageModel    = null;
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
		
		$this->plusUserModel    = new Plus_UserModel();
		$this->userInfoModel     = new User_InfoModel();
		$this->plusOrderModel     = new Plus_UserOrderModel();
	}
	
	/**
	 *获取PLUS会员列表
	 *
	 * @access public
	 */
	public function getPlusList()
	{
		
		$page = request_int('page');  //页码
		$rows = request_int('rows');  //每页行数
		$type = request_string('user_type'); //plus会员类型  1.试用中  2.已到期 3.未到期
		$name = request_string('search_name'); //用户名用于模拟查询
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
			$cond_row['user_id:IN'] = $user_id;
        }
		//查询条件拼接
		if ($type) {
			$cond_row['user_status'] = intval($type);
		}
		if ($name) {
			$cond_row['user_name'] = strval($name);
		}

		$data = $this->plusUserModel->getPlusUserList($cond_row, $sort, $page, $rows);
       
		$this->data->addBody(-140, $data);

	}

	/**
	 * 获取PLUS会员的订单列表
	 *
	 * @access public
	 */
	public function getPlusUserListById()
	{
		$cond_row                = array();
		$sort                    = array();
		$page                    = request_int('page', 1);
		$rows                    = request_int('rows', 100);
		$cond_row['user_id'] = request_int('id');
		$data = $this->plusOrderModel->getPlusUserListById($cond_row, $sort, $page, $rows);
      
		$this->data->addBody(-140, $data);
	}	
}

?>