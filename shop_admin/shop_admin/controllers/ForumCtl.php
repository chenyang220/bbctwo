<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

use Yf\Upgrader\Base;
use Yf\Upgrader\Core;

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class ForumCtl extends AdminController
{

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

	/**
	 * 显示首页版块页
	 *
	 * @access public
	 */
	public function front_forum()
	{
		include $view = $this->view->getView();

	}
}

?>