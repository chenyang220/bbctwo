<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

use Yf\Upgrader\Base;
use Yf\Upgrader\Core;

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class WxPublic_MenuCtl extends AdminController
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}
		
	//菜单设置
	public function menu()
	{
	  include $view = $this->view->getView();
		
	}

    //菜单设置
    public function msg()
    {
        include $view = $this->view->getView();

    }


    //菜单设置
    public function addPublicMsg()
    {
        include $view = $this->view->getView();

    }
}

?>

