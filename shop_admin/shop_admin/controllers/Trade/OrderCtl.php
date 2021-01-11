<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Trade_OrderCtl extends AdminController
{
	public $webconfigModel = null;

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

    /*
     * 获取商品订单列表
     * */
    public function getOrderList()
    {
        $page = request_int('page', 1);
        $rows = request_int('rows', 100);

        $order_row = array();
        $sidx = request_string('sidx');
        $sord = request_string('sord', 'asc');
        $action = request_string('action');

        if ($sidx) {
            $order_row[$sidx] = $sord;
        }

        if (request_string('order_id')) {
            $cond_row['order_id:LIKE'] = request_string('order_id') . '%';
        }
        if (request_string('buyer_name')) {
            $cond_row['buyer_user_name:LIKE'] = request_string('buyer_name') . '%';
        }
        if (request_string('shop_name')) {
            $cond_row['shop_name:LIKE'] = request_string('shop_name') . '%';
        }
        if (request_string('payment_other_number')) {
            $cond_row['payment_other_number:LIKE'] = '%' . request_string('payment_other_number') . '%';
        }
        if (!empty($action) && $action == 'virtual') {
            $cond_row['order_is_virtual'] = Order_BaseModel::ORDER_IS_VIRTUAL;
        }
        if (request_string('payment_date_f')) {
            $cond_row['payment_time:>='] = request_string('payment_date_f');
        }
        if (request_string('payment_date_t')) {
            $cond_row['payment_time:<='] = request_string('payment_date_t');
        }
        //分站筛选
        $sub_site_id = request_int('sub_site_id');
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
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
            $Order_BaseModel = new Order_BaseModel();
            $data = $Order_BaseModel->getPlatOrderList($cond_row, array('order_create_time' => 'desc'), $page, $rows);
            if ($data['records'] > 0) {
                $status = 200;
                $msg = __('success');
            } else {
                $status = 250;
                $msg = __('没有满足条件的结果哦');
            }
            $this->data->addBody(-140, $data, $msg, $status);
        }

    }

    //后台显示数据查询
    public function getEvaluateList()
    {
        $page = request_int('page');
        $rows = request_int('rows');
        $Goods_EvaluationModel = new Goods_EvaluationModel();

        $cond_row = array();

        $goods_name = request_string('goods_name');
        $shop_name = request_string('shop_name');
        $member_name = request_string('member_name');
        $scores = request_string('scores');
        $start_time = request_string('start_time');
        $end_time = request_string('end_time');
        $sub_site_id = request_int('sub_site_id');
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
        if ($sub_site_id > 0) {
            //获取站点信息
            $Sub_SiteModel = new Sub_SiteModel();
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $cond_row_district['district_id:IN'] = $sub_site_district_ids;
            }
        }
        $Order_BaseModel = new Order_BaseModel();
        $order_base = $Order_BaseModel->getByWhere($cond_row_district);
        $order_id = array_column($order_base,'order_id');
        if($order_id){
            $cond_row['order_id:IN'] = $order_id;
        }
        if ($goods_name) {
            $cond_row['goods_name:LIKE'] = '%' . $goods_name . '%';
        }

        if ($shop_name) {
            $cond_row['shop_name:LIKE'] = '%' . $shop_name . '%';
        }

        if ($member_name) {
            $cond_row['member_name:LIKE'] = '%' . $member_name . '%';
        }

        if ($scores) {
            $cond_row['scores'] = $scores;
        }

        if ($start_time) {
            $cond_row['create_time:>='] = $start_time;
        }

        if ($end_time) {
            $cond_row['create_time:<='] = $end_time;
        }

        $data = $Goods_EvaluationModel->listByWhere($cond_row, array(), $page, $rows);

        if ($data) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function getShopEvaluateList()
    {
        $page = request_int('page');
        $rows = request_int('rows');
        $Shop_EvaluationModel = new Shop_EvaluationModel();
        $Shop_BaseModel = new Shop_BaseModel();
        $User_BaseModel = new User_BaseModel();

        $cond_row = array();

        $evaluation_desccredit = request_string('evaluation_desccredit');
        $evaluation_servicecredit = request_string('evaluation_servicecredit');
        $evaluation_deliverycredit = request_string('evaluation_deliverycredit');
        $start_time = request_string('start_time');
        $end_time = request_string('end_time');

        if ($evaluation_desccredit) {
            $cond_row['evaluation_desccredit'] = $evaluation_desccredit;
        }

        if ($evaluation_servicecredit) {
            $cond_row['evaluation_servicecredit'] = $evaluation_servicecredit;
        }

        if ($evaluation_deliverycredit) {
            $cond_row['evaluation_deliverycredit'] = $evaluation_deliverycredit;
        }

        if ($start_time) {
            $cond_row['evaluation_create_time:>='] = $start_time;
        }

        if ($end_time) {
            $cond_row['evaluation_create_time:<='] = $end_time;
        }
        $sub_site_id = request_int('sub_site_id');
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
        if ($sub_site_id > 0) {
            //获取站点信息
            $Sub_SiteModel = new Sub_SiteModel();
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $cond_row_district['district_id:IN'] = $sub_site_district_ids;
            }
            $Order_BaseModel = new Order_BaseModel();
            $order_base = $Order_BaseModel->getByWhere($cond_row_district);
            $order_id = array_column($order_base,'order_id');
            $cond_row['order_id:IN'] = $order_id;
        }
        
        $data = $Shop_EvaluationModel->listByWhere($cond_row, array(), $page, $rows);
        $items = $data['items'];
        unset($data['items']);
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $shop_id = $value['shop_id'];
                $user_id = $value['user_id'];
                if ($shop_id) {
                    $data_shop = $Shop_BaseModel->getOne($shop_id);
                    if ($data_shop)
                        $items[$key]['shop_name'] = $data_shop['shop_name'];
                    else
                        $items[$key]['shop_name'] = '';
                }
                if ($user_id) {
                    $data_user = $User_BaseModel->getOne($user_id);
                    if ($data_user)
                        $items[$key]['user_name'] = $data_user['user_account'];
                    else
                        $items[$key]['user_name'] = '';
                }
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

        $this->data->addBody(-140, $data, $msg, $status);
    }
}

?>