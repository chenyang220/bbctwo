<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class LicenceCtl extends AdminController
{
    public function index()
    {
        include $this->view->getView();
    }

}
?>