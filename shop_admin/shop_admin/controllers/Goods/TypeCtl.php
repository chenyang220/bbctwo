<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Goods_TypeCtl extends AdminController
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
	public function type()
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
        $Goods_TypeModel = new Goods_TypeModel();
        $page = request_int('page', 1);
        $page_size = request_int('rows', 20);
        $data = $Goods_TypeModel->getTypeList(array('type_draft' => 0), array('type_displayorder' => 'ASC'), $page, $page_size);

        $this->data->addBody(-140, $data);
    }

    /*
	 * 获取商品类型详细信息
	 */
    public function getType()
    {
        $Goods_TypeModel = new Goods_TypeModel();
        $type_id = request_int('type_id');
        $property_is_search = request_string('property_is_search');
        $data = $Goods_TypeModel->getTypeInfo($type_id, $property_is_search);

        if ($data) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
}

?>