<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Seller_BillCtl extends Seller_Controller
{

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

	public function index()
    {
        $BillModel = new BillModel();
        $info = $BillModel->getBillInfo();
        include $this->view->getView();
    }

    //添加编辑背景图片
    public function addOrEditBill()
    {
        $shop_id = Perm::$shopId;
        $cond['bill_image'] = request_string('billImage');

        //判断当前编辑、添加
        $BillModel = new BillModel();
        $row['shop_id'] = $shop_id;
        $info = $BillModel->getOneByWhere($row);
        if($info){
            $flag = $BillModel->editBase($info['bill_id'], $cond,false);
        }else{
            $cond['shop_id'] = $shop_id;
            $flag = $BillModel->addBase($cond);
        }

        if($flag !== false)
        {
            $msg = 'success';
            $status = 200;
        }else{
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, array(),$msg,$status);
    }


}

?>