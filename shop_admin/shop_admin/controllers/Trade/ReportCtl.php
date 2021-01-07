<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Trade_ReportCtl extends AdminController
{
	public $webconfigModel = null;

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

    public function getReportList()
    {
        $goods_name = request_string("goods_name");
        $shop_name = request_string("shop_name");
        $user_account = request_string("user_account");
        $report_subject_name = request_string("report_subject_name");
        $report_type_name = request_string("report_type_name");

        $page = request_int('page', 1);
        $rows = request_int('rows', 10);
        $oname = request_string('sidx');
        $osort = request_string('sord');
        $cond_row = array();
        $sort = array();
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
            $Shop_BaseModel = new Shop_BaseModel();
            $shop_base = $Shop_BaseModel->getByWhere($cond_row_district);
            $shop_id = array_column($shop_base,'shop_id');
            $cond_row['shop_id:IN'] = $shop_id;
        }
        if ($oname != "number") {
            $sort[$oname] = $osort;
        }

        if ($goods_name) {
            $cond_row['goods_name:LIKE'] = '%' . $goods_name . '%';
        }
        if ($shop_name) {
            $cond_row['shop_name'] = $shop_name;
        }
        if ($user_account) {
            $cond_row['user_account'] = $user_account;
        }
        if ($report_subject_name) {
            $cond_row['report_subject_name'] = $report_subject_name;
        }
        if ($report_type_name) {
            $cond_row['report_type_name'] = $report_type_name;
        }
        $cond_row['report_state'] = Report_BaseModel::REPORT_DO;
        $data = array();
        $Report_BaseModel = new Report_BaseModel();
        $data = $Report_BaseModel->getCatList($cond_row, $sort, $page, $rows);
        $this->data->addBody(-140, $data);
    }

    public function getReportDoneList()
    {
        $goods_name = request_string("goods_name");
        $shop_name = request_string("shop_name");
        $user_account = request_string("user_account");
        $report_subject_name = request_string("report_subject_name");
        $report_type_name = request_string("report_type_name");

        $page = request_int('page', 1);
        $rows = request_int('rows', 10);
        $oname = request_string('sidx');
        $osort = request_string('sord');
        $cond_row = array();
        $sort = array();
        if ($oname != "number") {
            $sort[$oname] = $osort;
        }
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
            $Shop_BaseModel = new Shop_BaseModel();
            $shop_base = $Shop_BaseModel->getByWhere($cond_row_district);
            $shop_id = array_column($shop_base,'shop_id');
            $cond_row['shop_id:IN'] = $shop_id;
        }
        if ($goods_name) {
            $cond_row['goods_name:LIKE'] = '%' . $goods_name . '%';
        }
        if ($shop_name) {
            $cond_row['shop_name'] = $shop_name;
        }
        if ($user_account) {
            $cond_row['user_account'] = $user_account;
        }
        if ($report_subject_name) {
            $cond_row['report_subject_name'] = $report_subject_name;
        }
        if ($report_type_name) {
            $cond_row['report_type_name'] = $report_type_name;
        }
        $cond_row['report_state'] = Report_BaseModel::REPORT_DONE;
        $data = array();
        $Report_BaseModel = new Report_BaseModel();
        $data = $Report_BaseModel->getCatList($cond_row, $sort, $page, $rows);
        $this->data->addBody(-140, $data);
    }

    public function getTypeList()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 10);
        $oname = request_string('sidx');
        $osort = request_string('sord');
        $cond_row = array();
        $sort = array();
        $data = array();
        $Report_TypeModel    = new Report_TypeModel();
        $data = $Report_TypeModel   ->getCatList($cond_row, $sort, $page, $rows);
        $this->data->addBody(-140, $data);
    }

    public function getSubjectList()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 10);
        $oname = request_string('sidx');
        $osort = request_string('sord');
        $cond_row = array();
        $sort = array();
        $data = array();
        $Report_SubjectModel = new Report_SubjectModel();
        $data = $Report_SubjectModel->getCatList($cond_row, $sort, $page, $rows);
        $this->data->addBody(-140, $data);
    }
}

?>