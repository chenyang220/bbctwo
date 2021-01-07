<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class IndexCtl extends Yf_AppController
{
    public function index()
    {
        include $this->view->getView();
    }

    public function main()
    {
        $a = _("asa");
        include $this->view->getView();
    }
}
?>