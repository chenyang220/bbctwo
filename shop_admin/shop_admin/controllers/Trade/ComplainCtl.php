<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Trade_ComplainCtl extends AdminController
{
	public $webconfigModel = null;

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

    /**
     * 获取投诉列表
     *
     * @access public
     */
    public function getComplainList()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 100);

        $state = request_string('state', null);
        $user_type = request_string('user_type');
        $user_account = request_string('search_name');

        $cond_row = array();
        $data = array();
        $sub_site_id = request_int('sub_site_id');
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
        if ($sub_site_id > 0) {
            //获取站点信息
            $Sub_SiteModel = new Sub_SiteModel();
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $cond_row_district['district_id:IN'] = $sub_site_district_ids;
            }
            $Order_BaseModel = new Order_BaseModel();
            $order_base = $Order_BaseModel->getByWhere($cond_row_district);
            $order_id = array_column($order_base,'order_id');
            $cond_row['order_id:IN'] = $order_id;
        }
        //投诉状态
        if (null !== $state) {
            $cond_row['complain_state'] = $state;
        }
        //按照投诉人与被投诉人查询
        if ($user_account) {
            if ($user_type) {
                $type = 'user_account_accused:LIKE';
            } else {
                $type = 'user_account_accuser:LIKE';
            }
            $cond_row[$type] = '%' . $user_account . '%';
        }
        $Complain_BaseModel = new Complain_BaseModel();
        $data = $Complain_BaseModel->getBaseList($cond_row, array('complain_id' => 'ASC'), $page, $rows);

        if ($data['records']) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('没有满足条件的结果哦');
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 获取投诉主题列表
     *
     * @access public
     */
    public function getComplainSubjectList()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 100);

        $state = request_int('state');

        $cond_row = array();
        //投诉主题状态
        $cond_row['complain_subject_state'] = $state;


        $Complain_SubjectModel = new Complain_SubjectModel();
        $data = $Complain_SubjectModel->listByWhere($cond_row, array('complain_subject_id' => 'ASC'), $page, $rows);


        if ($data['records']) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('没有满足条件的结果哦');
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }


}

?>