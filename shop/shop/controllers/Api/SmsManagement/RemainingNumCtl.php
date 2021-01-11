<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_SmsManagement_RemainingNumCtl extends Api_Controller
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);

	}

	public function obtainRemainingNum(){
		$Web_ConfigModel = new Web_ConfigModel();
		$data =$Web_ConfigModel->getByWhere(array('config_type'=>'sms'));
		foreach ($data as $key => $value) {
			if($value['config_key']=='sms_account'){
				$uname = $value['config_value'];
			}
			if($value['config_key']=='sms_pass'){
				$upass = $value['config_value'];
			}
		}
		$url="http://106.13.185.162/yuanfengjia/public/index.php/index/mall/num?uname=".$uname."&upass=".$upass;
		$result = get_url($url);
		if($result['status']==1){
			$data=$result['data'];
		}else{
			$data = "";
		}
		$this->data->addBody(-140, $data);
	}

	public function smsDetail(){
		$Web_ConfigModel = new Web_ConfigModel();
		$info =$Web_ConfigModel->getByWhere(array('config_type'=>'sms'));
		foreach ($info as $key => $value) {
			if($value['config_key']=='sms_account'){
				$uname = $value['config_value'];
			}
			if($value['config_key']=='sms_pass'){
				$upass = $value['config_value'];
			}
		}
		$page = request_int('page', 1);
        $rows = request_int('rows', 20);

		$url="http://106.13.185.162/yuanfengjia/public/index.php/index/mall/sendLog?uname=".$uname."&upass=".$upass."&rows=".$rows."&page=".$page;
		$result = get_url($url);
		if($result['status']==1){
			$data = $result['data'];
			foreach ($data['items'] as $key => $value) {
				$data['items'][$key]['add_time']=date('Y-m-d h:i:s',$value['add_time']);
			}
			$msg = __('success');
            $status = 200;
		}else{
			$data = array();
			$msg = __('failure');
            $status = 250;
		}
		$this->data->addBody(-140, $data, $msg, $status);
	}
}	