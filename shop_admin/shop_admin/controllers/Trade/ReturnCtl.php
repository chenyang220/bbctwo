<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
    
    /**
     * @author
     */
    class Trade_ReturnCtl extends AdminController
    {
        public $webconfigModel = null;
        
        public function __construct(&$ctl, $met, $typ)
        {
            parent::__construct($ctl, $met, $typ);
        }
        
        public function refundWait()
        {
            $otyp = request_int("otyp", 1);
            include $this->view->getView();
        }
        
        public function refundAll()
        {
            $otyp = request_int("otyp", 1);
            include $this->view->getView();
        }

        public function getReturnWaitList()
        {
            $type = request_int("otyp", Order_ReturnModel::RETURN_TYPE_ORDER);
            $return_code = request_string("return_code");
            $seller_user_account = request_string("seller_user_account");
            $buyer_user_account = request_string("buyer_user_account");
            $order_goods_name = request_string("order_goods_name");
            $order_number = request_string("order_number");
            $start_time = request_string("start_time");
            $end_time = request_string("end_time");
            $min_cash = request_float("min_cash");
            $max_cash = request_float("max_cash");

            $page = request_int('page', 1);
            $rows = request_int('rows', 10);
            $oname = request_string('sidx');
            $osort = request_string('sord');
            $cond_row = array();
            $sort = array('return_add_time' => 'desc');
            if ($oname != "number") {
                $sort[$oname] = $osort;
            }
            if ($order_number) {
                $cond_row['order_number'] = $order_number;
            }
            if ($return_code) {
                $cond_row['return_code'] = $return_code;
            }
            if ($seller_user_account) {
                $cond_row['seller_user_account'] = $seller_user_account;
            }
            if ($buyer_user_account) {
                $cond_row['buyer_user_account'] = $buyer_user_account;
            }
            if ($order_goods_name) {
                $cond_row['order_goods_name:LIKE'] = '%' . $order_goods_name . '%';
            }
            if ($start_time) {
                $cond_row['return_add_time:>='] = $start_time;
            }
            if ($end_time) {
                $cond_row['return_add_time:<='] = $end_time;
            }
            if ($min_cash) {
                $cond_row['return_cash:>='] = $min_cash;
            }
            if ($max_cash) {
                $cond_row['return_cash:<='] = $max_cash;
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
            $cond_row['return_state:IN'] = array('0' => Order_ReturnModel::RETURN_SELLER_UNPASS, '1' => Order_ReturnModel::RETURN_SELLER_GOODS);
            $cond_row['return_type'] = $type;
            $cond_row['behalf_deliver:!='] = Order_ReturnModel::BEHALF_DELIVER_SHOP;
            $data = array();
            $Order_ReturnModel = new Order_ReturnModel();
            $data = $Order_ReturnModel->getReturnList($cond_row, $sort, $page, $rows);
            $this->data->addBody(-140, $data);
        }

        public function getReturnAllList()
        {
            $type = request_int("otyp", Order_ReturnModel::RETURN_TYPE_ORDER);
            $return_code = request_string("return_code");
            $seller_user_account = request_string("seller_user_account");
            $buyer_user_account = request_string("buyer_user_account");
            $order_goods_name = request_string("order_goods_name");
            $order_number = request_string("order_number");
            $start_time = request_string("start_time");
            $end_time = request_string("end_time");
            $min_cash = request_float("min_cash");
            $max_cash = request_float("max_cash");

            $page = request_int('page', 1);
            $rows = request_int('rows', 10);
            $oname = request_string('sidx');
            $osort = request_string('sord');
            $cond_row = array();
            $sort = array();
            if ($oname != "number") {
                $sort[$oname] = $osort;
            }

            if ($order_number) {
                $cond_row['order_number'] = $order_number;
            }
            if ($return_code) {
                $cond_row['return_code'] = $return_code;
            }
            if ($seller_user_account) {
                $cond_row['seller_user_account'] = $seller_user_account;
            }
            if ($buyer_user_account) {
                $cond_row['buyer_user_account'] = $buyer_user_account;
            }
            if ($order_goods_name) {
                $cond_row['order_goods_name:LIKE'] = '%' . $order_goods_name . '%';
            }
            if ($start_time) {
                $cond_row['return_add_time:>='] = $start_time;
            }
            if ($end_time) {
                $cond_row['return_add_time:<='] = $end_time;
            }
            if ($min_cash) {
                $cond_row['return_cash:>='] = $min_cash;
            }
            if ($max_cash) {
                $cond_row['return_cash:<='] = $max_cash;
            }
            $cond_row['return_type'] = $type;
            $data = array();
            $Order_ReturnModel = new Order_ReturnModel();
            $data = $Order_ReturnModel->getReturnList($cond_row, $sort, $page, $rows);
            $this->data->addBody(-140, $data);
        }

        public function getReasonList()
        {
            $page = request_int('page', 1);
            $rows = request_int('rows', 10);
            $oname = request_string('sidx');
            $osort = request_string('sord');
            $cond_row = array();
            $sort = array();
            $sort['order_return_reason_sort'] = "ASC";
            if ($oname != "number") {
                $sort[$oname] = $osort;
            }
            $data = array();
            $Order_ReturnReasonModel = new Order_ReturnReasonModel();
            $data = $Order_ReturnReasonModel->getReturnReasonList($cond_row, $sort, $page, $rows);
            $this->data->addBody(-140, $data);
        }

    }

?>