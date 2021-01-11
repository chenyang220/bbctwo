<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class IndexCtl extends Controller
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

	//首页
	public function index()
	{
		if (!Perm::checkUserPerm())
		{
			include $this->view->getView();
		}
		else
		{
			header('location:' . Yf_Registry::get('base_url') . '/index.php?ctl=Info&met=index');
			exit();
		}
	}

    /**
     *  空白iframe页面
     *  shop商城调用
     */
    public function iframe(){
        include $this->view->getView();
    }



    public function wx_paystatus(){
        $order_id = request_string('order_id');
        $union_order_id = request_string('union_order_id');
        $PaymentModel = new Payment_JhWxAppModel();
        $paystatus = $PaymentModel->paystatus($order_id);
        $Union_OrderModel = new Union_OrderModel();
        $trade_row = $Union_OrderModel->getOneByWhere(array("inorder"=>$order_id,"union_order_id"=>$union_order_id)); 
        if ($paystatus['data']['status'] == 1) {
             $url = $trade_row['r_url'] . "&order_id=" . $order_id . "&order_status=2";
        } else {
             $url = $trade_row['r_url'] . "&order_id=" . $order_id . "&order_status=1";
        }    
        header("Location:" . $url);
    }
}

?>