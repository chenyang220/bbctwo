<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Shop_DomainCtl extends AdminController
{
    /**
     * 首页
     *
     * @access public
     */
    public function shopIndex()
    {
        $Shop_DomainModel = new Shop_DomainModel();
        $order = array('shop_id' => 'asc');
        $data = $Shop_DomainModel->getDomainList(array(), $order);
        $this->data->addBody(-140, $data);
    }

}

?>