<?php

if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

class Operation_CustomCtl extends AdminController
{
    public function getCustomList()
    {

        $page = request_int('page', 1);
        $rows = request_int('rows', 10);
        $custom_service_question = request_string('custom_service_question');
        $user_account = request_string('user_account');
        $oname = request_string('sidx');
        $osort = request_string('sord');

        $cond_row = array();
        $sort = array();

        if ($custom_service_question) {
            $cond_row['custom_service_question:LIKE'] = "%" . $custom_service_question . "%";
        }
        if ($user_account) {
            $cond_row['user_account'] = $user_account;
        }
        if ($oname != "number") {
            $sort[$oname] = $osort;
        }

        $data = array();
        $Platform_CustomServiceModel = new Platform_CustomServiceModel();
        $data = $Platform_CustomServiceModel->getCustomServiceList($cond_row, $sort, $page, $rows);
        $this->data->addBody(-140, $data);
    }
	
}

?>