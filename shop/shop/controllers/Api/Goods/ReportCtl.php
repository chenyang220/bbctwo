<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}
class Api_Goods_ReportCtl extends Api_Controller
{
    public $reportBaseModel    = null;
    public $reportSubjectModel = null;
    public $reportTypeModel    = null;
    public $goodsBaseModel     = null;
    public $goodsCommonModel     = null;

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
        $this->reportBaseModel    = new Report_BaseModel();
        $this->reportSubjectModel = new Report_SubjectModel();
        $this->reportTypeModel    = new Report_TypeModel();
        $this->goodsBaseModel     = new Goods_BaseModel();
        $this->goodsCommonModel     = new Goods_CommonModel();
    }
    public function add()
    {
        $goods_id        = request_int("gid");
        $userId = request_int('user_id');
        $data['goods']   = $this->goodsBaseModel->getOne($goods_id);

        $goods = $this->goodsBaseModel->getByWhere(array("common_id"=>$data['goods']['common_id']));
        $goods_ids = array_column($goods,"goods_id");
        $report = $this->reportBaseModel->getByWhere(array("user_id"=>$userId,"goods_id:IN"=>$goods_ids,"report_state"=>Report_BaseModel::REPORT_DO));
        if(!empty($report)){
           return  $this->data->addBody(-140, []);
        }
        $data['type']    = $this->reportTypeModel->getByWhere();
        $data['type']    = array_values($data['type']);
        $data['subject'] = $this->reportSubjectModel->getByWhere(array("report_type_id" => $data['type'][0]['report_type_id']));
        $this->data->addBody(-140, $data);
    }
    public function detail()
    {
        $id                    = request_int("id");
        $userId = request_int('user_id');
        $cond_row['report_id'] = $id;
        $cond_row['user_id']   = $userId;

        $data = $this->reportBaseModel->getReportBase($cond_row);
        return $data;
    }
    public function getSubject()
    {
        $type_id = request_int("type_id");
        $data    = $this->reportSubjectModel->getByWhere(array("report_type_id" => $type_id));
        $this->data->addBody(-140, $data);
    }
    public function addReport()
    {
        $user_id = request_int('user_id');
        $userModel = new User_BaseModel();
        $user_rows = $userModel->getBase($user_id);
        $user_account = $user_rows[$user_id]['user_account'];
        $data['report_type_id']      = request_int("report_type_id");
        $type                        = $this->reportTypeModel->getOne($data['report_type_id']);
        $data['report_type_name']    = $type['report_type_name'];
        $data['report_subject_id']   = request_int("report_subject_id");
        $subject                     = $this->reportSubjectModel->getOne($data['report_subject_id']);
        $data['report_subject_name'] = $subject['report_subject_name'];
        $data['report_message']      = request_string("report_message");
        $pic                         = request_row("report_pic");
        $data['report_pic']          = implode(",", $pic);
        $data['goods_id']            = request_int("goods_id");
        $goods                       = $this->goodsBaseModel->getOne($data['goods_id']);
        $data['goods_name']          = $goods['goods_name'];
        $data['shop_id']             = $goods['shop_id'];
        $data['shop_name']           = $goods['shop_name'];
        $data['goods_pic']           = $goods['goods_image'];
        $data['user_id']             = $user_id;
        $data['user_account']        = $user_account;
        $data['report_date']         = get_date_time();

        $goods_l = $this->goodsBaseModel->getByWhere(array("common_id"=>$goods ['common_id']));
        $goods_ids = array_column($goods_l,"goods_id");
        $report = $this->reportBaseModel->getByWhere(array("user_id"=>$user_id,"goods_id:IN"=>$goods_ids,"report_state"=>Report_BaseModel::REPORT_DO));
        if(!empty($report)){
            $data   = array();
            $msg    = __('failure');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        $matche_row = array();
        //有违禁词
        if (Text_Filter::checkBanned($data['report_message'], $matche_row))
        {
            $data   = array();
            $msg    = __('failure');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        $flag = $this->reportBaseModel->addCat($data, true);
        if ($flag)
        {
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }



}