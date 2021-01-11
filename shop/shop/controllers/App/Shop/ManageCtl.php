<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author
 */
class Api_Shop_ManageCtl extends Api_Controller
{
    public $messageModel = null;
    public $shopBaseModel = null;
    public $shopClassModel = null;
    public $shopGradeModel = null;
    public $shopRenewalModel = null;
    public $goodsCommonModel = null;

    /**
     * 初始化方法，构造函数
     *
     * @access public
     */
    public function init()
    {
        $this->messageModel = new MessageModel();
        $this->shopBaseModel = new Shop_BaseModel();
        $this->shopClassModel = new Shop_ClassModel();
        $this->shopGradeModel = new Shop_GradeModel();
        $this->shopRenewalModel = new Shop_RenewalModel();
        $this->goodsCommonModel = new Goods_CommonModel();
    }

    /**
     * 首页
     *
     * @access public
     */
    public function shopIndex()
    {
        $shop_type = request_string('user_type');
        $shop_account = request_string('search_name');
        $shop_class = request_string('shop_class');

        $cond_row = array(
            "shop_self_support" => "false"
        );
        $cond_row["shop_status:in"] = array("0", "3");
        //按照店主账号与店主名称查询
        if ($shop_account) {
            if ($shop_type) {
                $type = 'shop_name:LIKE';
            } else {
                $type = 'user_name:LIKE';
            }
            $cond_row[$type] = '%' . $shop_account . '%';
        }
        if ($shop_class) {
            $cond_row['shop_class_id'] = $shop_class;
        }

        $cond_row['shop_type'] = 1; //非供应商店铺

        //分站筛选
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
            $page = (int)$_REQUEST['page'] ?: 1;
            $rows = (int)$_REQUEST['rows'];
            $data = $this->shopBaseModel->getBaseList($cond_row, $order_row = array(), $page, $rows);
            if ($data) {
                $status = 200;
                $msg = __('success ' . $page . " " . $rows);
            } else {
                $status = 250;
                $msg = __('没有数据');;
            }
           // $this->data->addBody(-140, $data, $msg, $status);
        }
    }


    /**
     * 分站业绩统计
     *
     * @access public
     */
    public function businessIndex()
    {            
        $shop_type = request_string('user_type');
        $shop_account = request_string('search_name');
        $shop_class = request_string('shop_class');
        $payment_date_f = request_string('payment_date_f');
        $payment_date_t = request_string('payment_date_t');
        $sub_id = request_string('sub_id');
        $acction = request_string('acction');
        $type_all = request_string('type');
        $cond_row["shop_status:in"] = array("0", "3");
        //按照店主账号与店主名称查询
        if ($shop_account) {
            if ($shop_type) {
                $type = 'shop_name:LIKE';
            } else {
                $type = 'user_name:LIKE';
            }
            $cond_row[$type] = '%' . $shop_account . '%';
        }
        if ($shop_class) {
            $cond_row['shop_class_id'] = $shop_class;
        }

        $cond_row['shop_type'] = 1; //非供应商店铺
        
         if ($acction == 'search_sub') {
            $sub_site_id = $sub_id;
         } else {
            $sub_site_id = request_int('sub_site_id');
         }
        //分站筛选
        $sub_flag = true;
        $Sub_SiteModel = new Sub_SiteModel();
        if ($sub_site_id > 0) {
            //获取站点信息
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $cond_row['district_id:IN'] = $sub_site_district_ids;
            }
        }
        if ($type_all == 'all') {
            $Sub_Site = $Sub_SiteModel->getByWhere("*");
            $sub_site_name = array_column($Sub_Site, 'sub_site_name', 'subsite_id');
            $district_child_ids = array_column($Sub_Site, 'district_child_ids', 'subsite_id');
            foreach ($district_child_ids as $subsite_id => $value) {
                $district_child_ids[$subsite_id] = explode(',', $value);
            }
        }

        if ($sub_flag == false) {
            $status = 250;
            $msg = __('分站信息获取失败');
            $this->data->addBody(-140, array(), $msg, $status);
        } else {
            $page = (int)$_REQUEST['page'] ?: 1;
            $rows = (int)$_REQUEST['rows'];
            $data = $this->shopBaseModel->getBaseList($cond_row, $order_row = array(), $page, $rows);
            if ($data) {
                $data['sub_site_id'] = $sub_site_id;
                $shop_id_arr = array_column($data['items'],'shop_id');
                $Order_BaseModel = new Order_BaseModel();
                $Order_StateModel = new Order_StateModel();
                if (request_string('payment_date_f'))
                {
                    $where .= " payment_time2 >='" . strtotime($payment_date_f) . "'";
                }
                if (request_string('payment_date_t'))
                {
                    $where .= " and  payment_time2 <'" . strtotime($payment_date_t) . "'";
                }
                $where .=  "and  shop_id IN (" . implode(',',$shop_id_arr) .")";
                $order_status_arr =implode(',',array(2,3,4,5,6,8,9)) ;
                $where .=  "and  order_status IN (" . $order_status_arr.')';

                if (request_string('payment_date_t') || request_string('payment_date_t'))
                {
                    $sql = "SELECT   *,ifnull(UNIX_TIMESTAMP(payment_time),0)payment_time2 FROM yf_order_base WHERE   1 having" .$where;
                } else {
                    $sql = "SELECT * from `yf_order_base` WHERE 1 {$where}";
                }
                $order_Base = $Order_BaseModel->sql->getAll($sql);
                //订单数
                $shop_order_ids = array_column($order_Base, 'shop_id','order_id');
                $order_member_arr = array_count_values($shop_order_ids);

                //下单人数
                $buyer_user_id = array_column($order_Base,'buyer_user_id','order_id');
        
                //实付款金额
                $order_payment_amounts = array_column($order_Base, 'order_payment_amount','order_id');


                foreach($shop_order_ids as $order_id => $shop_id) {
                    $shop_order_arr[$shop_id][] = $order_id;
                }
                //销量
                $Order_GoodsModel = new Order_GoodsModel();
                $order_ids = array_column($order_Base, 'order_id');
                $order_goods_cond_row["order_id:IN"] = $order_ids;
                $Order_Goods = $Order_GoodsModel->getByWhere($order_goods_cond_row);
                $order_goods_ids = array_column($Order_Goods, 'order_id','order_goods_id');
                $order_goods_nums = array_column($Order_Goods, 'order_goods_num','order_goods_id');
                $market_order_good = array();
                foreach ($order_goods_ids as $order_goods_id => $order_id) {
                    if (in_array($order_id, $market_order_good_sum)) {
                        $market_order_good[$order_id] = $order_goods_nums[$order_goods_id];
                    } else {
                        $market_order_good[$order_id] = $market_order_good[$order_id] + $order_goods_nums[$order_goods_id];
                    }
                }
                //销售额
                $order_goods_amounts = array_column($order_Base, 'order_goods_amount','order_id');
                $order_shipping_fees = array_column($order_Base, 'order_shipping_fee','order_id');

                $market_good_money = array();
                foreach($shop_order_ids as $order_id => $shop_id) {
                    $market_good_money[$order_id] = $order_goods_amounts[$order_id] + $order_shipping_fees[$order_id];
                }
                $shop_order_good_sum = array();
                $shop_good_money_sum = array();
                $shop_order_payment_sum = array();
                 $market_order_member_sum = array();
                foreach($shop_order_arr as $shop_id => $shop_arr) {
                      $market_order_good_sum = 0;
                      $market_good_money_sum = 0;
                      $market_payment_amounts = 0;
                    foreach ($shop_arr as $key => $order_id) {
                        //计算每个店铺的销售量
                        if ($market_order_good_sum == 0) {
                            $market_order_good_sum = $market_order_good[$order_id];
                        } else {
                            $market_order_good_sum = $market_order_good[$order_id] + $market_order_good_sum ;
                        }
                        //计算每个店铺的销售总额
                        if ($market_good_money_sum == 0) {
                            $market_good_money_sum = $market_good_money[$order_id];
                        } else {
                            $market_good_money_sum = $market_good_money[$order_id] + $market_good_money_sum ;
                        }

                        //计算每个店铺的实际付款金额
                        if ($market_payment_amounts == 0) {
                            $market_payment_amounts = $order_payment_amounts[$order_id];
                        } else {
                            $market_payment_amounts = $order_payment_amounts[$order_id] + $market_payment_amounts ;
                        }
      
                        //计算下单人数
                        $market_order_member_sum[$shop_id][$key] = $buyer_user_id[$order_id];
                    }

                    $shop_order_good_sum[$shop_id] = $market_order_good_sum;
                    $shop_good_money_sum[$shop_id] = $market_good_money_sum;
                    $shop_order_payment_sum[$shop_id] = $market_payment_amounts;
                }


                $Base_DistrictModel = new Base_DistrictModel();
                foreach ($data['items'] as $key => $value) {
                    if (isset($market_order_member_sum[$value['shop_id']])) {
                        $data['items'][$key]['order_member_sum'] = count(array_unique($market_order_member_sum[$value['shop_id']]));
                    } else {
                         $data['items'][$key]['order_member_sum'] = 0;
                    }

                    $data['items'][$key]['order_member'] = isset($order_member_arr[$value['shop_id']])?$order_member_arr[$value['shop_id']]:0;
                    $data['items'][$key]['market_good_sum'] = isset($shop_order_good_sum[$value['shop_id']])?$shop_order_good_sum[$value['shop_id']]:0;
                    $data['items'][$key]['market_good_money'] = isset($shop_good_money_sum[$value['shop_id']])?sprintf("%.2f",$shop_good_money_sum[$value['shop_id']]):0;
                    $data['items'][$key]['practical_money'] = isset($shop_order_payment_sum[$value['shop_id']])?sprintf("%.2f",$shop_order_payment_sum[$value['shop_id']]):0;
                    if ($type_all == 'all') {
                        foreach ($district_child_ids as $subsite_id => $subsite_value) {
                            if (in_array($value['district_id'], $subsite_value)) {
                                $data['items'][$key]['sub_site_name'] = $sub_site_name[$subsite_id];
                            } 
                        }
                         $data['items'][$key]['sub_site_name'] = isset($data['items'][$key]['sub_site_name']) ?$data['items'][$key]['sub_site_name']:'';
                    }

                    if ($value['shop_self_support']) {
                        $district_name = $Base_DistrictModel->getAllName($value['district_id']);
                        $data['items'][$key]['shop_company_address'] = $district_name;
                    }
                    
                }

                $status = 200;
                $msg = __('success ' . $page . " " . $rows);
            } else {
                $status = 250;
                $msg = __('没有数据');;
            }
            $this->data->addBody(-140, $data, $msg, $status);
        }
    }

    /**
     * 分站业绩统计导出
     *
     * @access public
     */
    public function getBusinessrExcel()
    {
        ob_get_clean();
        $shop_type = request_string('user_type');
        $shop_account = request_string('search_name');
        $shop_class = request_string('shop_class');
        $payment_date_f = request_string('payment_date_f');
        $payment_date_t = request_string('payment_date_t');
        $sub_id = request_string('sub_id');
        $acction = request_string('acction');
        $user_id = Perm::$userId;
        $page = request_int("page");
        $rows = request_int("rows");
        $is_limit = request_int("is_limit");
        $type_all = request_string('type','');
        $cond_row["shop_status:in"] = array("0", "3");
        //按照店主账号与店主名称查询
        if ($shop_account) {
            if ($shop_type) {
                $type = 'shop_name:LIKE';
            } else {
                $type = 'user_name:LIKE';
            }
            $cond_row[$type] = '%' . $shop_account . '%';
        }
        if ($shop_class) {
            $cond_row['shop_class_id'] = $shop_class;
        }

        $cond_row['shop_type'] = 1; //非供应商店铺

        //分站筛选
         if ($acction == 'search_sub') {
            $sub_site_id = $sub_id;
         } else {
            $sub_site_id = request_int('sub_site_id');
         }
   
        $sub_flag = true;
        $Sub_SiteModel = new Sub_SiteModel();
        if ($sub_site_id > 0) {
            //获取站点信息
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $cond_row['district_id:IN'] = $sub_site_district_ids;
            }
        }
        if ($type_all == 'all') {
            $Sub_Site = $Sub_SiteModel->getByWhere("*");
            $sub_site_name = array_column($Sub_Site, 'sub_site_name', 'subsite_id');
            $district_child_ids = array_column($Sub_Site, 'district_child_ids', 'subsite_id');
            foreach ($district_child_ids as $subsite_id => $value) {
                $district_child_ids[$subsite_id] = explode(',', $value);
            }
        }
        $data = array();
        if ($sub_flag != false) {
            if ($is_limit  == 1) {
                $data = $this->shopBaseModel->getBaseList($cond_row, $order_row = array(), $page, $rows);
            } else {
                $data = $this->shopBaseModel->getbaseAll($cond_row);
            } 
        }
        $excel_data = array();
        if ($data) {
            $shop_id_arr = array_column($data['items'],'shop_id');
            $Order_BaseModel = new Order_BaseModel();
            $Order_StateModel = new Order_StateModel();
            if (request_string('payment_date_f'))
            {
                $where .= " payment_time2 >='" . strtotime($payment_date_f) . "'";
            }
            if (request_string('payment_date_t'))
            {
                $where .= " and  payment_time2 <'" . strtotime($payment_date_t) . "'";
            }
            $where .=  "and  shop_id IN (" . implode(',',$shop_id_arr) .")";
            $order_status_arr =implode(',',array(2,3,4,5,6,8,9)) ;
            $where .=  "and  order_status IN (" . $order_status_arr.')';
            if (request_string('payment_date_t') || request_string('payment_date_t'))
            {
                $sql = "SELECT   *,ifnull(UNIX_TIMESTAMP(payment_time),0)payment_time2 FROM yf_order_base WHERE   1 having" .$where;
            } else {
                $sql = "SELECT * from `yf_order_base` WHERE 1 {$where}";
            }
            $order_Base = $Order_BaseModel->sql->getAll($sql);
            //订单数
            $shop_order_ids = array_column($order_Base, 'shop_id','order_id');
            $order_member_arr = array_count_values($shop_order_ids);

            //下单人数
            $buyer_user_id = array_column($order_Base,'buyer_user_id','order_id');
    
            //实付款金额
            $order_payment_amounts = array_column($order_Base, 'order_payment_amount','order_id');


            foreach($shop_order_ids as $order_id => $shop_id) {
                $shop_order_arr[$shop_id][] = $order_id;
            }
            //销量
            $Order_GoodsModel = new Order_GoodsModel();
            $order_ids = array_column($order_Base, 'order_id');
            $order_goods_cond_row["order_id:IN"] = $order_ids;
            $Order_Goods = $Order_GoodsModel->getByWhere($order_goods_cond_row);
            $order_goods_ids = array_column($Order_Goods, 'order_id','order_goods_id');
            $order_goods_nums = array_column($Order_Goods, 'order_goods_num','order_goods_id');
            $market_order_good = array();
            foreach ($order_goods_ids as $order_goods_id => $order_id) {
                if (in_array($order_id, $market_order_good_sum)) {
                    $market_order_good[$order_id] = $order_goods_nums[$order_goods_id];
                } else {
                    $market_order_good[$order_id] = $market_order_good[$order_id] + $order_goods_nums[$order_goods_id];
                }
            }
            //销售额
            $order_goods_amounts = array_column($order_Base, 'order_goods_amount','order_id');
            $order_shipping_fees = array_column($order_Base, 'order_shipping_fee','order_id');

            $market_good_money = array();
            foreach($shop_order_ids as $order_id => $shop_id) {
                $market_good_money[$order_id] = $order_goods_amounts[$order_id] + $order_shipping_fees[$order_id];
            }


            $shop_order_good_sum = array();
            $shop_good_money_sum = array();
            $shop_order_payment_sum = array();
            $market_order_member_sum = array();
            foreach($shop_order_arr as $shop_id => $shop_arr) {
                  $market_order_good_sum = 0;
                  $market_good_money_sum = 0;
                  $market_payment_amounts = 0;
                foreach ($shop_arr as $key => $order_id) {
                    //计算每个店铺的销售量
                    if ($market_order_good_sum == 0) {
                        $market_order_good_sum = $market_order_good[$order_id];
                    } else {
                        $market_order_good_sum = $market_order_good[$order_id] + $market_order_good_sum ;
                    }
                    //计算每个店铺的销售总额
                    if ($market_good_money_sum == 0) {
                        $market_good_money_sum = $market_good_money[$order_id];
                    } else {
                        $market_good_money_sum = $market_good_money[$order_id] + $market_good_money_sum ;
                    }

                    //计算每个店铺的实际付款金额
                    if ($market_payment_amounts == 0) {
                        $market_payment_amounts = $order_payment_amounts[$order_id];
                    } else {
                        $market_payment_amounts = $order_payment_amounts[$order_id] + $market_payment_amounts ;
                    }
  
                    //计算下单人数
                    $market_order_member_sum[$shop_id][$key] = $buyer_user_id[$order_id];
                }

                $shop_order_good_sum[$shop_id] = $market_order_good_sum;
                $shop_good_money_sum[$shop_id] = $market_good_money_sum;
                $shop_order_payment_sum[$shop_id] = $market_payment_amounts;
            }
         
            $i = 1;
            foreach ($data['items'] as $key => $value) {
                if (isset($market_order_member_sum[$value['shop_id']])) {
                    $order_member_sum= count(array_unique($market_order_member_sum[$value['shop_id']]));
                } else {
                     $order_member_sum = 0;
                }

                $excel_data[$key][] = $i;

                if ($type_all == 'all') {
                        foreach ($district_child_ids as $subsite_id => $subsite_value) {
                            if (in_array($value['district_id'], $subsite_value)) {
                                $excel_data[$key]['sub_site_name'] = $sub_site_name[$subsite_id];
                            }
                        }
                    $excel_data[$key]['sub_site_name'] = isset($excel_data[$key]['sub_site_name']) ?$excel_data[$key]['sub_site_name']:'';
                }
                $excel_data[$key]['user_name'] = $value['user_name'];
                $excel_data[$key]['shop_name'] = $value['shop_name'];
                $excel_data[$key]['shop_grade'] = $value['shop_grade'];
                $excel_data[$key]['shop_class'] = $value['shop_class'];
                $excel_data[$key]['shop_company_address'] = $value['shop_company_address'];
                $excel_data[$key]['shop_tel'] = $value['shop_tel'];
                $excel_data[$key]['order_member_sum'] = $order_member_sum;
                $excel_data[$key]['order_member'] = isset($order_member_arr[$value['shop_id']])?$order_member_arr[$value['shop_id']]:0;
                $excel_data[$key]['market_good_sum'] =  isset($shop_order_good_sum[$value['shop_id']])?$shop_order_good_sum[$value['shop_id']]:0;
                $excel_data[$key]['market_good_money'] = isset($shop_good_money_sum[$value['shop_id']])?$shop_good_money_sum[$value['shop_id']]:0;
                $excel_data[$key]['practical_money'] = isset($shop_order_payment_sum[$value['shop_id']])?$shop_order_payment_sum[$value['shop_id']]:0;               
                $i++;
            }
        }   

             if ($type_all == 'all') {
                $header = array(
                    "序号",
                    "所属分站",
                    "店主账号",
                    "店铺名称",
                    "店铺等级",
                    "店铺类型",
                    "所在区域",
                    "商家电话",
                    "下单人数",
                    "订单数",
                    "销量",
                    "销售额",
                    "实付款金额",
                );
            } else {
                $header = array(
                    "序号",
                    "店主账号",
                    "店铺名称",
                    "店铺等级",
                    "店铺类型",
                    "所在区域",
                    "商家电话",
                    "下单人数",
                    "订单数",
                    "销量",
                    "销售额",
                    "实付款金额",
                );
            } 
            
            exportExcel($header,$excel_data);
            die('导出成功！');
    }

    /**
     * 获取店铺详情
     *
     * @access public
     */
    public function getShoplist()
    {
        $shop_id = request_int('shop_id');
        if(!$shop_id) {
            return $this -> data -> addBody(-140, array(), __('缺少shop_id'), 250);
        }
        $data = $this->shopBaseModel->getbaseAllList($shop_id);
        $data['base'][$shop_id]['bank_code'] = "'" . $data['base'][$shop_id]['bank_code'] . "'";
        $data['base'][$shop_id]['bank_account_number'] = "'" . $data['base'][$shop_id]['bank_account_number'] . "'";
        $data['base'][$shop_id]['organization_code'] = "'" . $data['base'][$shop_id]['organization_code'] . "'";

        $this->data->addBody(-140, $data);
    }

    /**
     * 店铺信息主页
     *
     * @access public
     */
    public function getinformationrow()
    {
         $id = request_int('shop_id');
         $data = $this->shopBaseModel->getOne($id);
         $data['class'] = $this->shopClassModel->getClassWhere();
         $data['grade'] = $this->shopGradeModel->getGradeWhere();


         $data["payshopname"] = "";
         $data["payshopnumer"] = "";
         $data["payshopnumer"] = "";
         $data["payshopcode"] = "";
         $data["paytermnumber"] = "";
         $data["payscale"] = "0";  //   分成比例    0  -  100
         $data["cbpayshopnumer"] = "";
         $data["xcxpayshopnumer"] = "";
         $where = array();
         $where["shop_id"] = $id ;
         $Ve_ShoppayModel = new Ve_ShoppayModel();
         $shoppayinfo =     $Ve_ShoppayModel -> getOneByWhere( $where);
         if($shoppayinfo){
            $data["payshopname"] = $shoppayinfo["payshopname"];
            $data["payshopnumer"] = $shoppayinfo["payshopnumer"]; 
            $data["payshopcode"] = $shoppayinfo["payshopcode"];
            $data["paytermnumber"] = $shoppayinfo["paytermnumber"];
            $data["payscale"] = $shoppayinfo["payscale"]; 
            $data["cbpayshopnumer"] = $shoppayinfo["cbpayshopnumer"];
            $data["xcxpayshopnumer"] = $shoppayinfo["xcxpayshopnumer"]; 
         }
        $this->data->addBody(-140, $data);
    }

    /**
     * 修改店铺信息主页
     *
     * @access public
     */
    public function editShopinformation()
    {

        $edit_shop_row['shop_class_id'] = request_int("shop_class_id");
        $edit_shop_row['shop_grade_id'] = request_int("shop_grade_id");
        $edit_shop_row['shop_status'] = request_int("shop_status");
        $shop_id = request_int('shop_id');

        /**
         * 大华捷通商家分账支付参数
         * @var [type]
         */
        $payshopname  = request_string("payshopname");
        $payshopnumer  = request_string("payshopnumer");
        $payshopcode  = request_string("payshopcode");
        $paytermnumber  = request_string("paytermnumber");
        $payscale  = request_string("payscale");
        $cbpayshopnumer  = request_string("cbpayshopnumer");
        $xcxpayshopnumer  = request_string("xcxpayshopnumer");

        $shop_base_model = new Shop_BaseModel();
        $Ve_ShoppayModel = new Ve_ShoppayModel();
        $where = array();
        $where["shop_id"] = $shop_id ;
        $shopayinfo = $Ve_ShoppayModel  -> getOneByWhere($where);
        if($shopayinfo){
          $fierow = array();
          $fierow["payshopname"] = $payshopname  ;
          $fierow["payshopnumer"] = $payshopnumer  ;
          $fierow["payshopcode"] = $payshopcode  ;
          $fierow["paytermnumber"] = $paytermnumber  ;
          $fierow["payscale"] = $payscale  ;
          $fierow["cbpayshopnumer"] = $cbpayshopnumer  ;
          $fierow["xcxpayshopnumer"] = $xcxpayshopnumer  ;
          $Ve_ShoppayModel  -> editInfo($shopayinfo["id"] , $fierow) ;
        }else{
          $fierow = array();
          $fierow["payshopname"] = $payshopname  ;
          $fierow["payshopnumer"] = $payshopnumer  ;
          $fierow["payshopcode"] = $payshopcode  ;
          $fierow["paytermnumber"] = $paytermnumber  ;
          $fierow["payscale"] = $payscale  ;
          $fierow["shop_id"] = $shop_id  ;
          $fierow["cbpayshopnumer"] = $cbpayshopnumer  ;
          $fierow["xcxpayshopnumer"] = $xcxpayshopnumer  ;
          $Ve_ShoppayModel  -> addInfo($fierow) ;
        }

        $flag = $shop_base_model->editShopInfo($shop_id, $edit_shop_row);
        if ($flag === FALSE) {
            $status = 250;
            $msg = __('failure');
        } else {
            if ($edit_shop_row['shop_status'] != Shop_BaseModel::SHOP_STATUS_OPEN) {
                //如果店铺关闭，商品则全部下架
                //下架goods_base商品 //goods_is_shelves=2
                $goodsBaseModel = new Goods_BaseModel();
                $goodsBaseModel->editBaseByShopId($shop_id, array('goods_is_shelves' => $goodsBaseModel::GOODS_DOWN));
                //下架goods_common的商品 common_state=0
                $goodsCommonModel = new Goods_CommonModel();
                $goodsCommonModel->editCommonByShopId($shop_id, array('common_state' => $goodsCommonModel::GOODS_STATE_OFFLINE, 'shop_status' => $edit_shop_row['shop_status']));
            } else {
                //修改商品店铺状态
                $goodsCommonModel = new Goods_CommonModel();
                $goodsCommonModel->editCommonByShopId($shop_id, array('shop_status' => $edit_shop_row['shop_status']));
            }
            $status = 200;
            $msg = __('success');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 开店申请首页
     *
     * @access public
     */
    public function shopJoin()
    {
        $shop_type = request_string('user_type');
        $shop_account = request_string('search_name');
        $shop_class = request_string('shop_class');

        $cond_row = array(
            "shop_status" => "1",
            "shop_self_support" => "false"
        );

        //按照店主账号与店主名称查询
        if ($shop_account) {
            if ($shop_type) {
                $type = 'shop_name:LIKE';
            } else {
                $type = 'user_name:LIKE';
            }
            $cond_row[$type] = '%' . $shop_account . '%';
        }

        if ($shop_class) {
            $cond_row['shop_class_id'] = $shop_class;
        }
        $cond_row['shop_type:IN'] = [0,1]; //非供应商店铺

        $Sub_SiteModel = new Sub_SiteModel();
        $sub_site_id = request_int('sub_site_id');
        //判断分站信息
        $sub_flag = true;
        if ($sub_site_id > 0) {
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
            $data = $this->shopBaseModel->getBaseList($cond_row);
            $this->data->addBody(-140, $data);
        }
    }

    /**
     * 经营类目申请
     *
     * @access public
     */

    public function getCategory()
    {
        // $data = array();
        $shop_type = request_string('user_type');
        $shop_account = request_string('search_name');
        $shop_class_bind_enable = request_string('status');
        $cond_row = array();

        if ($shop_class_bind_enable) {
            $type = 'shop_class_bind_enable';
            $cond_row[$type] = $shop_class_bind_enable;
        }
        //按照店主账号与店主名称查询
        if ($shop_account) {
            if ($shop_type == "1") {
                $type = 'commission_rate:LIKE';
                $cond_row[$type] = $shop_account . '%';
            } elseif ($shop_type == "2") {

                $shop_base = $this->shopBaseModel->getByWhere(array('shop_name:LIKE' => $shop_account . '%'));
                $shop_id = array_column($shop_base, 'shop_id');
                $cond_row['shop_id:IN'] = $shop_id;
            } else {
                $shop_base = $this->shopBaseModel->getByWhere(array('user_name:LIKE' => $shop_account . '%'));
                $user_id = array_column($shop_base, 'shop_id');
                $cond_row['shop_id:IN'] = $user_id;
            }

        }

        //去除供应商店铺ID
        $Sub_SiteModel = new Sub_SiteModel();
        $sub_site_id = request_int('sub_site_id');
        //判断分站信息
        $sub_flag = true;
        $where = array('shop_type' => 1);
        if ($sub_site_id > 0) {
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if (!$sub_site_district_ids) {
                $sub_flag = false;
            } else {
                $where['district_id:IN'] = $sub_site_district_ids;
            }
        }
        if ($sub_flag == false) {
            $status = 250;
            $msg = __('分站信息获取失败');
            $this->data->addBody(-140, array(), $msg, $status);
        } else {
            $shop_base = $this->shopBaseModel->getByWhere($where);
            $shop_id = array_column($shop_base, 'shop_id');

            //求交集
            if ($shop_id && $cond_row['shop_id:IN']) {
                $cond_row['shop_id:IN'] = array_intersect($cond_row['shop_id:IN'], $shop_id);
            }


            $order = array('shop_id' => 'desc');
            $data = $this->shopBaseModel->getCategorylist($cond_row, $order);
            $this->data->addBody(-140, $data);
        }
    }


    /**
     * 修改店铺经营类目
     *
     * @access public
     */
    public function editShopCategory()
    {
        $shop_class_bind_id = request_int('shop_class_bind_id');
        $shopClassBindModel = new Shop_ClassBindModel();
        $shop_class_bind_row = $shopClassBindModel->getOne($shop_class_bind_id);
        $this->data->addBody(-140, $shop_class_bind_row);
    }

    /**
     * 添加店铺经营类目
     */

    public function editShopCategoryRow()
    {
        $shop_class_bind_id = request_int('shop_class_bind_id');
        $class_list["commission_rate"] = request_string("commission_rate");
        $shopClassBindModel = new Shop_ClassBindModel();
        $flag = $shopClassBindModel->editClassBind($shop_class_bind_id, $class_list);
        if ($flag !== false) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('failure');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     * 添加店铺经营类目
     */

    public function addShopCategory()
    {
        $data["shop_id"] = request_int('shop_id');
        $this->data->addBody(-140, $data);
    }

    /**
     * 添加店铺经营类目
     */

    public function addShopCategoryRow()
    {
        $class_list["product_class_id"] = request_int('product_class_id');
        $class_list["shop_id"] = request_int('shop_id');
        $class_list["commission_rate"] = request_string("commission_rate");
        $class_list["shop_class_bind_enable"] = 2;
        $shopClassBindModel = new Shop_ClassBindModel();
        $flag = $shopClassBindModel->addClassBind($class_list, true);
        if ($flag) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('failure');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     * 删除经营类目
     *
     * @access public
     */

    public function delCategory()
    {

        $shop_class_bind_id = request_int('shop_class_bind_id');

        if ($shop_class_bind_id) {

            $shopClassBindModel = new Shop_ClassBindModel();
            $flag = $shopClassBindModel->removeClassBind($shop_class_bind_id);
            if ($flag) {
                $status = 200;
                $msg = __('success');
            } else {
                $status = 250;
                $msg = __('failure');
            }
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function categoryStatus()
    {

        $shop_class_bind_id = request_int('shop_class_bind_id');

        if ($shop_class_bind_id) {
            $shopClassBindModel = new Shop_ClassBindModel();
            $update_data = [];
            //加入拒绝状态
            $pass = request_int('pass');
            if (!$pass) {
                $refusal_reason = request_string('refusalReason');
                $update_data['shop_class_bind_enable'] = 0; //拒绝
                $update_data['shop_class_bind_desc'] = $refusal_reason; //拒绝理由
            } else {
                $update_data['shop_class_bind_enable'] = 2; //审核通过
            }

            $flag = $shopClassBindModel->editClassBind($shop_class_bind_id, $update_data);
            if ($flag) {
                $status = 200;
                $msg = __('success');
            } else {
                $status = 250;
                $msg = __('failure');
            }
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     * 结算周期首页
     *
     * @access public
     */

    public function getSettlement()
    {
        $shop_type = request_string('user_type');
        $shop_account = request_string('search_name');

        $cond_row = array();

        //按照店主账号与店主名称查询
        if ($shop_account) {
            if ($shop_type) {
                $type = 'shop_name:LIKE';
            } else {
                $type = 'user_name:LIKE';
            }
            $cond_row[$type] = '%' . $shop_account . '%';
        }
        $sub_flag = true;
        $cond_row['shop_type'] = 1; //非供应商店铺
        $Sub_SiteModel = new Sub_SiteModel();
        $sub_site_id = request_int('sub_site_id');
        $sub_flag = true;
        if ($sub_site_id > 0) {
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
            $order = array('shop_id' => 'asc');
            $data = $this->shopBaseModel->getBaseList($cond_row, $order);
            $this->data->addBody(-140, $data);
        }

    }


    /**
     * 结算周期修改页面
     *   查询一条记录
     * @access public
     */

    public function getSettlementRow()
    {
        $shop_id = request_int('shop_id');
        $data = $this->shopBaseModel->getOne($shop_id);
        $this->data->addBody(-140, $data);
    }

    /**
     * 修改周期
     *
     * @access public
     */
    public function editSettlementRow()
    {
        $shop_id = request_int('shop_id');
        $shop_settlement_cycle['shop_settlement_cycle'] = request_string('shop_settlement_cycle');
        $flag = $this->shopBaseModel->editBase($shop_id, $shop_settlement_cycle);

        if ($flag === false) {
            $status = 250;
            $msg = __('failure');
        } else {
            $status = 200;
            $msg = __('success');
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    public function reopenlist()
    {
        $shop_type = request_string('user_type');
        $shop_account = request_string('search_name');

        $cond_row = array();

        //按照店主账号与店主名称查询
        if ($shop_account) {
            if ($shop_type) {
                $type = 'shop_name:LIKE';
            } else {
                $type = 'shop_id:LIKE';
            }
            $cond_row[$type] = '%' . $shop_account . '%';
        }

        $shop_class = request_string('shop_class');
        if ($shop_class) {
            $cond_row['shop_class_id'] = $shop_class;
        }

        //非供应商店铺
        $shopBaseModel = new Shop_BaseModel;
        $shop_ids = $shopBaseModel->getShopId(['shop_type' => 1]);
        $cond_row['shop_id:IN'] = $shop_ids;
        $Sub_SiteModel = new Sub_SiteModel();
        $sub_site_id = request_int('sub_site_id');
        //判断分站信息
        $sub_flag = true;
        if ($sub_site_id > 0) {
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
            $data = $this->shopRenewalModel->getRenewalList($cond_row);
            $this->data->addBody(-140, $data);
        }
    }

    public function delReopen()
    {

        $shop_reopen_id = request_int('id');

        if ($shop_reopen_id) {
            $flag = $this->shopRenewalModel->removeRenewal($shop_reopen_id);
            if ($flag) {
                $status = 200;
                $msg = __('success');
            } else {
                $status = 250;
                $msg = __('failure');
            }
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //审核续签
    public function examineReopen()
    {

        $shop_reopen_id = request_int('id');
        //开启事物
        $this->messageModel->sql->startTransactionDb();
        if ($shop_reopen_id) {
            $status['status'] = 1;
            //更改续签的状态
            $flag = $this->shopRenewalModel->editRenewal($shop_reopen_id, $status);
            //更改店铺的结束时间
            $flag1 = $this->shopRenewalModel->editEndTime($shop_reopen_id);
            //判断事物有没有成功
            if ($flag && $flag1 && $this->messageModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
            } else {
                $this->messageModel->sql->rollBackDb();
                $status = 250;
                $msg = __('failure');
            }

        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function editCategory()
    {

        $cond_row['shop_id'] = request_int('shop_id');
        $data = $this->shopBaseModel->getCategorylist($cond_row);
        $this->data->addBody(-140, $data);

    }

    //审核店铺 状态为1，审核信息。状态为2，审核有没有付款。
    public function editShopStatus()
    {
        $shop_id = request_int('shop_id');
        $shop_row = $this->shopBaseModel->getOne($shop_id);
        if ($shop_row['shop_status'] == 1) {
            $edit_status['shop_status'] = 2;
            $flag = $this->shopBaseModel->editBase($shop_id, $edit_status);

        } elseif ($shop_row['shop_status'] == 2) {
            $edit_status['shop_status'] = 3;
            $flag = $this->shopBaseModel->editBase($shop_id, $edit_status);
        }
        if (!$flag === FALSE) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('failure');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //审核店铺信息。
    public function verifyShop()
    {
        $shop_id = request_int('shop_id');
        $shop_verify1 = request_int('shop_verify1');
        $shop_verify2 = request_int('shop_verify2');
        $shop_verify3 = request_int('shop_verify3');
        $shop_verify4 = request_int('shop_verify4');
        if (!$shop_verify1 && !$shop_verify2 && !$shop_verify3 && !$shop_verify4) {
            $flag = false;
        } else {
            if ($shop_verify4) {
                $edit_status['shop_status'] = $shop_verify4;
            } else {
                if ($shop_verify1 == 4) {
                    $edit_status['shop_status'] = 4;
                } else {
                    if ($shop_verify2 == 5) {
                        $edit_status['shop_status'] = 5;
                    } else {
                        if ($shop_verify3 == 6) {
                            $edit_status['shop_status'] = 6;
                        } else {
                            $edit_status['shop_status'] = 2;
                        }
                    }
                }
            }
            // 如果付款信息审核不通过，店铺类型就不能为1，要置0~
            // 因为店铺类型 1代表是商家类型，2代表是供应商类型，商家审核不通过，即不能改为1
//            if ($shop_verify1 == 4 || $shop_verify2 == 5 || $shop_verify3 == 6 || $shop_verify4 == 7) {
//                $edit_status['shop_type'] = 0;
//            }else{
//                $edit_status['shop_type'] = 1;
//            }
            $edit_status['shop_verify_reason'] = request_string('shop_verify_reason');
            $flag = $this->shopBaseModel->editBase($shop_id, $edit_status);
        }

        if ($flag !== false) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('failure');
        }
        $data['shop_verify1'] = $shop_verify1;
        $data['shop_verify2'] = $shop_verify2;
        $data['shop_verify3'] = $shop_verify3;
        $data['shop_verify4'] = $shop_verify4;
        $data['edit_status'] = $edit_status;
        $this->data->addBody(-140, $data, $msg, $status);
    }


    //审核付款
    public function shopPay()
    {
        $shop_type = request_string('user_type');
        $shop_account = request_string('search_name');

        $cond_row = array(
            "shop_status" => "2",
            "shop_self_support" => "false"
        );

        //按照店主账号与店主名称查询
        if ($shop_account) {
            if ($shop_type) {
                $type = 'shop_name:LIKE';
            } else {
                $type = 'user_name:LIKE';
            }
            $cond_row[$type] = '%' . $shop_account . '%';
        }

        $shop_class = request_string('shop_class');
        if ($shop_class) {
            $cond_row['shop_class_id'] = $shop_class;
        }
        $cond_row['shop_type'] = 1; //非供应商店铺
        $Sub_SiteModel = new Sub_SiteModel();
        $sub_site_id = request_int('sub_site_id');
        //判断分站信息
        $sub_flag = true;
        if ($sub_site_id > 0) {
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
            $data = $this->shopBaseModel->getBaseList($cond_row);
            $this->data->addBody(-140, $data,$cond_row);
        }
    }


}

?>
