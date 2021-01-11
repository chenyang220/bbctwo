<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Base_SearchCtl extends AdminController
{
	function search()
	{
		include $view = $this->view->getView();;
	}

	function manage()
	{
		include $view = $this->view->getView();;
	}
}

?>