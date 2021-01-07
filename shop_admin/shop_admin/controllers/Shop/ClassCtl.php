<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Shop_ClassCtl extends AdminController
{
    /**
     * 首页
     *
     * @access public
     */
    public function shopIndex()
    {
        $Shop_ClassModel = new Shop_ClassModel();
        $order = array('shop_class_displayorder' => 'asc');
        $data = $Shop_ClassModel->listClassWhere(array(), $order);
        $this->data->addBody(-140, $data);
    }
    public function shopClass()
    {
        $Shop_ClassModel = new Shop_ClassModel();
        $order = array('shop_class_displayorder' => 'asc');
        $data = $Shop_ClassModel->getByWhere(array(), $order);
        $data = array_values($data);
        $result = array();
        $result[0]['id'] = 0;
        $result[0]['name'] = "店铺类型";
        foreach ($data as $key => $value) {
            $result[$key + 1]['id'] = $value['shop_class_id'];
            $result[$key + 1]['name'] = $value['shop_class_name'];
        }

        $this->data->addBody(-140, $result);
    }

}

?>