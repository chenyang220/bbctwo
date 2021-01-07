<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
    
    /**
     * @author     Yf <service@yuanfeng.cn>
     */
    class Buyer_VoucherCtl extends Buyer_Controller
    {
        
        /**
         * Constructor
         *
         * @param  string $ctl 控制器目录
         * @param  string $met 控制器方法
         * @param  string $typ 返回数据类型
         *
         * @access public
         */
        public function __construct(&$ctl, $met, $typ)
        {
            parent::__construct($ctl, $met, $typ);
            
            $this->voucherBaseModel = new Voucher_BaseModel();
            
        }
        
        /**
         * 代金券领取列表
         *
         * @access public
         *
         */
        public function voucher()
        {
            $cond_row = array();
            $state = request_int('state');
            $cond_row['voucher_owner_id'] = Perm::$userId;
            if ($state) {
                $cond_row['voucher_state'] = $state;
            } else {
                $cond_row['voucher_state:!='] = Voucher_BaseModel::RECOVER;
            }

            if($_COOKIE['SHOP_ID']){
                $cond_row['voucher_shop_id'] = $_COOKIE['SHOP_ID'];
            }
            if(request_int('shop_id_wap')){
                $cond_row['voucher_shop_id'] = request_int('shop_id_wap');
            }
//            $order_row = array('voucher_state' => 'asc', 'voucher_active_date' => 'desc');
            /**
             * • 优惠券排列方式
             * 1，根据优惠金额以少至多一次排序；
             * 2，若优惠金额一致再根据满足金额以多至少依次排序；
             * 3，若满足金额也一致，则根据过期时间由近及远排序；
             * 4，“未使用”与“已失效”排列方式一致。
             *
             * Shop：
             * 未达到限额的在前面，已经过期的在后面
             * 优惠金额从小到大显示 -即：价格正序；
             * 未达到限额的按照满足金额从小到大显示 -即：价格正序；
             * 已经过期的按照有效期由近及远显示-即：有效期倒序。
             */
            $order_row = array('voucher_state' => 'asc', 'voucher_price' => 'asc', 'voucher_limit' => 'desc', 'voucher_end_date' => 'asc');
            
            if ('json' == $this->typ) {
                $rows = request_int('pagesize') ? request_int('pagesize') : 20;
                $page = request_int('curpage');
                if ($state == 2) {
                    //把不能用的都查出来
                    unset($cond_row['voucher_state']);
                    $cond_row['voucher_state:NOT IN'] = [Voucher_BaseModel::UNUSED,Voucher_BaseModel::RECOVER];
                }
                $data = $this->voucherBaseModel->getUserVoucherList($cond_row, $order_row, $page, $rows);
                $data['items'] = $this->getVoucherData($data['items']);
                if ($data['page'] < $data['total']) {
                    $data['hasmore'] = true;
                } else {
                    $data['hasmore'] = false;
                }
                
                $data['page_total'] = $data['total'];
                return $this->data->addBody(-140, $data);
            } else {

                $Yf_Page = new Yf_Page();
                $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 10;
                $rows = $Yf_Page->listRows;
                $offset = request_int('firstRow', 0);
                $page = ceil_r($offset / $rows);
                $data = $this->voucherBaseModel->getUserVoucherList($cond_row, $order_row, $page, $rows);
                $data['items'] = $this->getVoucherData($data['items']);
                $Yf_Page->totalRows = $data['totalsize'];
                $page_nav = $Yf_Page->prompt();
                
                include $this->view->getView();
            }
        }
        
        /**
         *  代金券数据
         */
        private function getVoucherData($data)
        {
            if (!is_array($data) || !$data) {
                return array();
            }
            $shop_id_row = array_column($data, 'voucher_shop_id');
            if (!$shop_id_row) {
                return array();
            }
            $Shop_BaseModel = new Shop_BaseModel();
            $shop_rows = $Shop_BaseModel->getBase($shop_id_row);
            if (!$shop_rows) {
                return array();
            }
            foreach ($data as $key => $value) {
                $data[$key]['voucher_shop_name'] = $shop_rows[$value['voucher_shop_id']]['shop_name'];
                $data[$key]['voucher_shop_logo'] = $shop_rows[$value['voucher_shop_id']]['shop_logo'];
                $data[$key]["voucher_state_label"] = __(Voucher_BaseModel::$voucherState[$value["voucher_state"]]);
                
                $data[$key]["voucher_limit"] = number_format($data[$key]["voucher_limit"]);
                $data[$key]["voucher_end_date"] = date('Y-m-d', strtotime($data[$key]["voucher_end_date"]) + 1);
                $data[$key]["voucher_start_date"] = date('Y-m-d', strtotime($data[$key]["voucher_start_date"]));
                $data[$key]["v_end_date"] = date('Y.m.d', strtotime($data[$key]["voucher_end_date"]) + 1);
                $data[$key]["v_start_date"] = date('Y.m.d', strtotime($data[$key]["voucher_start_date"]));
            }
            return $data;
        }
        
        public function delVoucher()
        {
            $voucher_id = request_int('id');
            $flag = $this->voucherBaseModel->editVoucher($voucher_id, ['voucher_state' => Voucher_BaseModel::RECOVER]);
            $rs_row = array();
            check_rs($flag, $rs_row);
            $fl = is_ok($rs_row);
            
            if ($fl) {
                $status = 200;
                $msg = __('success');
                
            } else {
                $status = 250;
                $msg = __('failure');
                
            }
            $data = array('fl' => $fl);
            
            $this->data->addBody(-140, $data, $msg, $status);
        }
        /*
         * 清空失效代金券
         *
         */
        public function delVouchers()
        {
            $user_id = Perm::$userId;

            $vou_id = array();
            $voucher_list = $this->voucherBaseModel->getByWhere(['voucher_owner_id'=>$user_id,'voucher_state:IN'=>[Voucher_BaseModel::USED,Voucher_BaseModel::EXPIRED]]);
            if($voucher_list){
                $vou_id = array_column($voucher_list,'voucher_id');
            }

            $flag = $this->voucherBaseModel->editVoucher($vou_id, ['voucher_state' => Voucher_BaseModel::RECOVER]);
            $rs_row = array();
            check_rs($flag, $rs_row);
            $fl = is_ok($rs_row);

            if ($fl) {
                $status = 200;
                $msg = __('success');

            } else {
                $status = 250;
                $msg = __('failure');

            }
            $data = array('fl' => $fl);

            $this->data->addBody(-140, $data, $msg, $status);
        }
        
    }

?>