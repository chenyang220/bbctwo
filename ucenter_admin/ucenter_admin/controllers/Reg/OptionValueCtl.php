<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Reg_OptionValueCtl extends AdminController
{
    /**
     * 首页
     * 
     * @access public
     */
    public function index()
    {
        include $this->view->getView();
    }
    
    /**
     * 管理界面
     * 
     * @access public
     */
    public function manage()
    {
        include $this->view->getView();
    }
}
?>