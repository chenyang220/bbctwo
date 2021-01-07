<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Promotion_VoucherCtl extends AdminController
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

	public function index()
	{
		$view = $this->view->getView();
		include $view;
	}
	public function voucher()
	{
		$view = $this->view->getView();
		include $view;
	}

}

?>