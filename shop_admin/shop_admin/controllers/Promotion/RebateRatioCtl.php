<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */

class Promotion_RebateRatioCtl extends AdminController
{	
	public function __construct(&$ctl, $met, $typ)
    {	
        parent::__construct($ctl, $met, $typ);
    }

    public function rebateList(){
    	$RebateRatioModel= new RebateRatioModel();
    	$list=$RebateRatioModel->getOneByWhere();
    	$this->view->setMet('rebateList');
    	include $this->view->getView();  	
    }


    public function saveDetail(){
    	$RebateRatioModel= new RebateRatioModel();
    	$rebate_ratio_id=request_int("rebate_ratio_id");
    	$condow['first_rebate_ratio']=request_int("first_rebate_ratio");
    	$condow['second_rebate_ratio']=request_int("second_rebate_ratio");
		if(empty($rebate_ratio_id)){
    		$flag=$RebateRatioModel->addRebateRatio($condow,true);
    	}else{
    		$condow['update_time'] = date('Y-m-d h:i:s', time());
    		$flag=$RebateRatioModel->editRebateRatio($rebate_ratio_id,$condow);
    	}
		if ($flag) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('保存失败');
        }
        $this->data->addBody(-140, array(), $msg, $status);		    	    	
    }
}
?>