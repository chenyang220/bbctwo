<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Mb_Module1Ctl extends AdminController
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }

    public function module1Edit()
    {
        include $this->view->getView();
    }
    public function module2Edit()
    {
        include $this->view->getView();
    }
}

?>