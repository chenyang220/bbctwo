<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class FeedCtl extends AdminController
{
    function index()
    {
        include $view = $this->view->getView();
    }
}

?>