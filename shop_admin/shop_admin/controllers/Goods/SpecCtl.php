<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Goods_SpecCtl extends AdminController
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

	/**
	 * 设置商城API网址及key - 后台独立使用
	 *
	 * @access public
	 */
	public function spec()
	{
		include $view = $this->view->getView();;

	}

    /**
     *
     *
     * @access public
     */
    public function lists()
    {

        $page = request_int('page');
        $rows = request_int('rows', 100);

        $Goods_SpecModel = new Goods_SpecModel();
        $data = $Goods_SpecModel->getSpecList(array(), array(), $page, $rows);


        $this->data->addBody(-140, $data);
    }

    /*
     * 获取规格信息
     */
    function getSpec()
    {
        $Goods_SpecModel = new Goods_SpecModel();
        $data = $Goods_SpecModel->getSpec('*');
        if ($data) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $data = array_values($data);
        $this->data->addBody(-140, $data, $msg, $status);
    }
}

?>