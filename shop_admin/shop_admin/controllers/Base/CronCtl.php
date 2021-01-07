<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Base_CronCtl extends AdminController
{
	function index()
	{
		include $view = $this->view->getView();
	}
}

?>