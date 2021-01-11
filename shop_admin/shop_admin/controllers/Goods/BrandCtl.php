<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Goods_BrandCtl extends AdminController
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
	public function brand()
	{
		include $view = $this->view->getView();;
	}

	public function brandmanage()
	{
		include $view = $this->view->getView();;
	}

    /*
     * @mars
     * 品牌列表
     */
    public function listBrand($return = false)
    {
        if (request_int('page')) {
            $page = request_int('page');
        } else {
            $page = 0;
        }
        if (request_int('rows')) {
            $rows = request_int('rows');
        } else {
            $rows = 99999;
        }
        $skey = request_string('skey');
        $cat_id = request_int('cat_id');
        $Goods_BrandModel = new Goods_BrandModel();
        $Goods_TypeBrandModel = new Goods_TypeBrandModel();
        $cond_row = array();
        if (request_int('uncheck')) {
            $cond_row['brand_enable'] = 0;
        } else {
            $cond_row['brand_enable'] = 1;
        }
        if ($skey) {
            $cond_row['brand_name:like'] = '%' . $skey . '%';
        }

        if ($cat_id && $cat_id != -1) {
            $cond_row['cat_id'] = $cat_id;
        }

        $data_brand = $Goods_BrandModel->getBrandList($cond_row, array(), $page, $rows);
        $rows = $data_brand['items'];
        unset($data_brand['items']);

        if (!empty($rows)) {
            foreach ($rows as $key => $value) {
                $brand_id = $value['brand_id'];
                $rows[$key]['id'] = $brand_id;
                $data_type = $Goods_TypeBrandModel->getByWhere(array('brand_id' => $brand_id));
            }
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('没有数据');
            $status = 250;
        }
        if ($return) {
            return $rows;
        } else {
            $data_brand['rows'] = $rows;
            $this->data->addBody(-140, $data_brand, $msg, $status);
        }
    }

    public function getBrand()
    {
        $Goods_BrandModel = new Goods_BrandModel();
        $brand_id = request_int('brand_id');
        $data_brand = $Goods_BrandModel->getByWhere(array('brand_id' => $brand_id));
        $data = $data_brand[$brand_id];
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /*
	 * 取得所有品牌
	 */
    public function getBrands()
    {
        $Goods_BrandModel = new Goods_BrandModel();
        $data = $Goods_BrandModel->getBrandAll();
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