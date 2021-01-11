<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Shop_HelpCtl extends AdminController
{

    public function helpList()
    {
        $Shop_HelpModel = new Shop_HelpModel();
        $data = $Shop_HelpModel->listByWhere(array('page_show:IN' => array(1, 2)), array('help_sort' => 'ASC'));
        $this->data->addBody(-140, $data);
    }
}

?>