<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Supplier_HelpCtl extends AdminController
{
	public $webconfigModel = null;

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

    public function helpList()
    {
        $Shop_HelpModel = new Shop_HelpModel();
        $data = $Shop_HelpModel->listByWhere(array('page_show:IN' => array(3, 4)), array('help_sort' => 'ASC'));
        $this->data->addBody(-140, $data);
    }
}
?>