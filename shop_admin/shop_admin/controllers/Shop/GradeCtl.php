<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Shop_GradeCtl extends AdminController
{
    /**
     * 首页
     *
     * @access public
     */
    public function shopIndex()
    {
        $Shop_GradeModel = new Shop_GradeModel();
        $order = array('shop_grade_sort' => 'desc');
        $data = $Shop_GradeModel->listGradeWhere(array(), $order);
        $this->data->addBody(-140, $data);
    }

}

?>