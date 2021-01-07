<?php

if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

class Operation_SettlementCtl extends AdminController
{
    public function getSettleList()
    {

        $os_order_type = request_int("otyp", Order_SettlementModel::SETTLEMENT_NORMAL_ORDER);
        $page = request_int('page', 1);
        $rows = request_int('rows', 10);
        $type = request_string('user_type');
        $settleId = request_string('settleId');
        $shopName = request_string('shopName');
        $state = request_string('state');
        $start_time = request_string('start_time');
        $end_time = request_string('end_time');
        $oname = request_string('sidx');
        $osort = request_string('sord');

        $cond_row = array();
        $sort = array();
        $cond_row["os_order_type"] = $os_order_type;
        if ($settleId) {
            $cond_row['os_id:LIKE'] = '%' . $settleId . '%';
        }
        if ($shopName) {
            $cond_row['shop_name:LIKE'] = '%' . $shopName . '%';
        }
        if ($state) {
            $cond_row['os_state'] = $state;
        }
        if ($start_time) {
            $cond_row['os_start_date:>='] = $start_time;
        }
        if ($end_time) {
            $cond_row['os_end_date:<='] = $end_time;
        }
        if ($oname != "number") {
            $sort[$oname] = $osort;
        }
        //结算分站筛选
        $sub_site_id = request_int('sub_site_id');
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
            $Order_SettlementModel = new Order_SettlementModel();
            $data = $Order_SettlementModel->getSettlementList($cond_row, $sort, $page, $rows);
            if ($data) {
                $status = 200;
                $msg = __('success');
            } else {
                $status = 250;
                $msg = __('没有数据');;
            }
            $this->data->addBody(-140, $data, $msg, $status);
        }
    }
	
}
