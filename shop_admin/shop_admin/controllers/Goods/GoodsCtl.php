<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Goods_GoodsCtl extends AdminController
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
	public function common()
    {


        include $view = $this->view->getView();


	}

    public function listCommon()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 20);
//            $page = $_SERVER['page'] ? $_SERVER['page'] : 1;
//            $rows = $_SERVER['rows'] ? $_SERVER['rows'] : 200;
        $order_row = array();
        $sidx = request_string('sidx');
        $sord = request_string('sord', 'asc');

        if ($sidx) {
            $order_row[$sidx] = $sord;
        }

        $cond_row = array();
        //排除供应商
        //$cond_row['supply_shop_id'] = 0;
        if (-1 != request_int('product_distributor_flag', -1)) {
            $cond_row['product_distributor_flag'] = request_int('product_distributor_flag');
        }
        if (-1 != request_int('cat_id', -1)) {
            $cat_id = request_int('cat_id');
        }
        $Goods_CatModel = new Goods_CatModel();
        if ($cat_id) {
            //查找该分类下所有的子分类
            $cat_list = $Goods_CatModel->getCatChildId($cat_id);
            $cat_list[] =$cat_id;

            //查找该分类的父级分类
            $parent_cat_id = $Goods_CatModel->getCatParentTree($cat_id);

            $cond_row['cat_id:IN'] = $cat_list;
        }
        if (-1 != request_int('brand_id', -1)) {
            $cond_row['brand_id'] = request_int('brand_id');
        }

        if (-1 != request_int('common_state', -1)) {
            $cond_row['common_state'] = request_int('common_state');
        }

        if (-1 != request_int('common_verify', -1)) {
            $cond_row['common_verify'] = request_int('common_verify');
        }

        if (request_int('common_id')) {
            $cond_row['common_id'] = request_int('common_id');
        }

        if (request_string('common_name')) {
            $cond_row['common_name:LIKE'] = '%' . request_string('common_name') . '%';
        }

        if (request_string('shop_name')) {
            $cond_row['shop_name:LIKE'] = '%' . request_string('shop_name') . '%';
        }
        if (request_string('type_wxapp') == 'wxapp') {
            $cond_row['common_is_tuan'] = 0;
        }
        if (-1 != request_int('brand_id', -1)) {
            $cond_row['brand_id'] = request_int('brand_id');
        }
        //分站筛选
        $sub_site_id = request_int('sub_site_id');
        if(!$sub_site_id){
            $User_BaseModel = new User_BaseModel();
            $user_base = $User_BaseModel->getOne(Perm::$userId);
            $sub_site_id = $user_base['sub_site_id'];
        }
        $sub_flag = true;
        if ($sub_site_id > 0) {
            //获取站点信息
            $Sub_SiteModel = new Sub_SiteModel();
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $cond_row['district_id:IN'] = $sub_site_district_ids;
            }
        }

        if ($sub_flag == false) {
            $status = 250;
            $msg = __('分站信息获取失败');
            $this->data->addBody(-140, array(), $msg, $status);
        } else {
            $Goods_CommonModel = new Goods_CommonModel();
            $data = $Goods_CommonModel->getCommonList($cond_row, $order_row, $page, $rows);
//            $data = $this->countStock($data);

            foreach ($data['items'] as $kk => $vv) {
                $data['items'][$kk]['content_fenxiao'] = '分销客一级：'.$vv ['common_c_first'].',分销客二级：'.$vv['common_c_second'].',分销掌柜一级：'.$vv ['common_a_first'].',分销掌柜二级：'.$vv['common_a_second'];
            }
            if ($data) {
                $status = 200;
                $msg = __('success');
            } else {
                $status = 250;
                $msg = __('没有满足条件的结果哦');
            }
            $this->data->addBody(-140, $data, $msg, $status);
        }
    }

    public function listGoodsRecommend()
    {
        $page = request_int('page');
        $rows = request_int('rows');
        $cond_row = array();
        $order_row = array();

        $Goods_RecommendModel = new Goods_RecommendModel();
        $Goods_CatModel = new Goods_CatModel();
        $data = $Goods_RecommendModel->getRecommendList($cond_row, $order_row, $page, $rows);
        $items = $data['items'];
        unset($data['items']);
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $cat_id = $value['goods_cat_id'];
                $items[$key]['goods_cat_name'] = $Goods_CatModel->getNameByCatid($value['goods_cat_id']);
            }
        }
        $data['items'] = $items;
        if ($items) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $this->data->addBody(140, $data, $msg, $status);
    }

    public function getGoodsRecommendById()
    {
        $Goods_RecommendModel = new Goods_RecommendModel();
        $Goods_CommonModel = new Goods_CommonModel();
        $id = request_int('id');
        $re = array();

        if ($id) {
            $data = $Goods_RecommendModel->getOne($id);
            $goods_id_rows = $data['common_id'];
            $cat_id = $data['goods_cat_id'];
            $re = $Goods_CommonModel->getCommonList(array('common_id:in' => $goods_id_rows));
            $re['goods_cat_id'] = $cat_id;
        }
        if (!empty($re)) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $re, $msg, $status);
    }

    /**
     * 编辑状态
     *
     * @access public
     */
    public function editCommonState1()
    {

        $Goods_CommonModel = new Goods_CommonModel();

        $common_id = request_int('common_id');
        $common_state = request_int('common_state');
        $common_state_remark = request_string('common_state_remark');

        if ($common_id && array_key_exists($common_state, Goods_CommonModel::$stateMap)) {

            $flag = $Goods_CommonModel->editCommon($common_id, array('common_state' => $common_state));

            if ($flag !== false) {
                $msg = __('success');
                $status = 200;

                //将用户发布或者修改的商品同步到im中
                $key = Yf_Registry::get('im_api_key');
                $url = Yf_Registry::get('im_api_url');
                $im_app_id = Yf_Registry::get('im_app_id');
                $formvars = array();

                $formvars['goods_common_id'] = $common_id;
                $formvars['app_id'] = $im_app_id;

                $formvars['goods_status'] = $common_state;
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Goods&met=editUserGoodsStatus&typ=json', $url), $formvars);

                $shop = $Goods_CommonModel->getOne($common_id);
                $shop_id = $shop['shop_id'];
                $Shp_BaseModel = new Shop_BaseModel();
                $shop_data = $Shp_BaseModel->getOne($shop_id);

                //商品违规被下架
                //[des][common_id]
                $message = new MessageModel();
                $message->sendMessage('Commodity violation is under the shelf', $shop_data['user_id'], $shop_data['user_name'], $order_id = NULL, $shop_name = NULL, 1, 2, $end_time = Null, $common_id = $common_id, $goods_id = NULL, $common_state_remark);

                //加入拼团商品判断，商品违规下架后，相关拼团活动取消
                $pintuanModel = new PinTuan_Base;
                $pintuanModel->cancelByCommonId($common_id);

                //砍价活动的状态,商品违规下架后，相关砍价活动取消
                $goodsBaseModel = new Goods_BaseModel();
                $goods_item = $goodsBaseModel->getByWhere(array("common_id:IN" => $common_id));
                $goods_ids = array_column($goods_item, 'goods_id');
                $Bargain_BaseModel = new Bargain_BaseModel();
                $Bargain_BaseModel->editBargainStatus(array_unique($goods_ids));

            } else {
                $msg = __('failure');
                $status = 250;
            }
        } else {
            $msg = __('请求参数有误');
            $status = 250;

        }

        $this->data->addBody(-140, array(), $msg, $status);
    }


	public function goodsManage()
	{
		$data = $this->getUrl('Goods_Goods', 'getGoodsInfo');
		$json = json_encode($data);

		include $view = $this->view->getView();;

	}

}

?>