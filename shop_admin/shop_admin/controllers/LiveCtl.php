<?php 
if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author    tech40@yuanfeng021.com
 * 统计数据初始化
 * 
 */
class LiveCtl extends AdminController{
   
    public function __construct(&$ctl, $met, $typ){
		parent::__construct($ctl, $met, $typ);

	}

    public function liveList(){
        include $this -> view -> getView();

    }

    /**
     * 列表数据
     *
     * @access public
     */
    public function getLiveList()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 20);
        $order_row['live_application_id'] = "DESC";
        $cond_row = array();

        if (request_string('shop_info')) {
            if (request_int('shop_state') == 1) {
                $cond_row['user_name'] = request_string('shop_info');
            } else {
                $cond_row['shop_name'] = request_string('shop_info');
            }
        }
        if (request_int('live_status')) {
            $cond_row['application_status'] = request_int('live_status');
        }
        if (request_string('live_start')) {
            $cond_row['live_start'] = strtotime(request_string('live_start'));
        }
        if (request_string('live_end')) {
            $cond_row['live_end'] = strtotime(request_string('live_end'));
        }
        $Live_ApplicationModel = new Live_ApplicationModel();
        $data = $Live_ApplicationModel->getLiveList($cond_row, $order_row, $page, $rows);

        return $this->data->addBody(-140, $data);
    }


    public function editApplication()
    {
        $live_application_id = request_int('id');
        $Live_ApplicationModel = new Live_ApplicationModel();
        $data = $Live_ApplicationModel->getLiveInfoById($live_application_id);
        include $this->view->getView();
    }

    //编辑申请
    public function manageApplication()
    {
        $live_application_id = request_int('id');
        $Live_ApplicationModel = new Live_ApplicationModel();
        $live_info = $Live_ApplicationModel->getOne($live_application_id);

        $edit_row = array();
        $action = request_string('action');
        //审核是否通过
        switch ($action){
            case 'verify':
                $edit_row['application_info'] = request_string('application_info');
                $edit_row['application_status'] = request_string('application_status') ? request_string('application_status') : 2;
                $edit_row['application_status_time'] = time();
                if(request_string('application_status') == 2){
                    $edit_row['application_end_time'] = strtotime("+" . $live_info['live_length'] . " month");
                }
                break;
            case 'edit':
                $edit_row['application_status'] = request_string('application_status') ? request_string('application_status') : 2;
                break;
            case 'del':
                $edit_row['is_del'] = 1;
                $edit_row['application_status_time'] = '';
                $edit_row['application_info'] = '';
                break;
        }

        $flag = $Live_ApplicationModel->editApplication($live_application_id, $edit_row);

        if($flag !== false){
            $msg = 'success';
            $status = 200;
        }else{
            $msg = 'failure';
            $status = 250;
        }
        $edit_row['id'] = $live_application_id;
        return $this->data->addBody(-140, $edit_row,$msg,$status);
    }
    
    
}

?>
