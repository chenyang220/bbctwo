<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Trade_ConsultCtl extends AdminController
{
	public $webconfigModel = null;

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

    public function getConsultList()
    {
        $consult_question = request_string("consult_question");
        $user_account = request_string("user_account");
        $start_time = request_string("start_time");
        $end_time = request_string("end_time");

        $page = request_int('page', 1);
        $rows = request_int('rows', 10);
        $oname = request_string('sidx');
        $osort = request_string('sord');
        $cond_row = array();
        $sort = array();
        if ($oname != "number") {
            $sort[$oname] = $osort;
        }

        if ($consult_question) {
            $cond_row['consult_question:LIKE'] = '%' . $consult_question . '%';
        }
        if ($user_account) {
            $cond_row['user_account'] = $user_account;
        }
        if ($start_time) {
            $cond_row['question_time:>='] = $start_time;
        }
        if ($end_time) {
            $cond_row['question_time:<='] = $end_time;
        }
        $data = array();
        $Consult_BaseModel = new Consult_BaseModel();
        $data = $Consult_BaseModel->getBaseList($cond_row, $sort, $page, $rows);
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
        $sort['consult_type_sort'] = "ASC";
        if ($oname != "number") {
            $sort[$oname] = $osort;
        }
        $data = array();
        $Consult_TypeModel = new Consult_TypeModel();
        $data = $Consult_TypeModel->getCatList($cond_row, $sort, $page, $rows);
        $this->data->addBody(-140, $data);
    }
}

?>