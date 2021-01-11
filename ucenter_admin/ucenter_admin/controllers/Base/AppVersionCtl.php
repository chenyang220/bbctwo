<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Base_AppVersionCtl extends AdminController
{
	public function index()
	{
		include $this->view->getView();
	}
	
	public function main()
	{
		include $this->view->getView();
	}
}
?>