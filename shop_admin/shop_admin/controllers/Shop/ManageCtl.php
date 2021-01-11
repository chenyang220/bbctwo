<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Shop_ManageCtl extends AdminController
{
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
            $page = (int)$_REQUEST['page'] ?: 1;
            $rows = (int)$_REQUEST['rows'];
            $Shop_BaseModel = new Shop_BaseModel();
            $data = $Shop_BaseModel->getBaseList($cond_row, $order_row = array(), $page, $rows);

            $yunshanstatus =Yf_Registry::get('yunshanstatus');
            if($yunshanstatus ==1){
                $Ve_ShoppayModel  = new Ve_ShoppayModel();
                foreach($data["items"]  as $k=>$v){
                   $data["items"][$k]["payshopname"] = "" ;
                   $data["items"][$k]["payshopnumer"] = "" ;
                   $data["items"][$k]["payshopnumer"] = "" ;
                   $data["items"][$k]["payshopcode"] = "" ;
                   $data["items"][$k]["paytermnumber"] = "" ;
                   $data["items"][$k]["payscale"] = "0" ;  //   分成比例    0  -  100
                   $data["items"][$k]["cbpayshopnumer"] = "" ;
                   $data["items"][$k]["xcxpayshopnumer"] = "" ;
                   $shop_id = $v["shop_id"];
                   $where = array();
                   $where["shop_id"] = $shop_id ;
                   $shoppayinfo =   $Ve_ShoppayModel -> getOneByWhere( $where);
                   if($shoppayinfo){  
                       $data["items"][$k]["payshopname"] = $shoppayinfo["payshopname"];
                       $data["items"][$k]["payshopnumer"] = $shoppayinfo["payshopnumer"];
                       $data["items"][$k]["payshopcode"] = $shoppayinfo["payshopcode"];
                       $data["items"][$k]["paytermnumber"] = $shoppayinfo["paytermnumber"];
                       $data["items"][$k]["payscale"] = $shoppayinfo["payscale"]; 
                       $data["items"][$k]["cbpayshopnumer"] = $shoppayinfo["cbpayshopnumer"];
                       $data["items"][$k]["xcxpayshopnumer"] = $shoppayinfo["xcxpayshopnumer"];
                   }
               } 
            }
            if ($data) {
                $status = 200;
                $msg = __('success ' . $page . " " . $rows);
            } else {
                $status = 250;
                $msg = __('没有数据');
            }
            $this->data->addBody(-140, $data, $msg, $status);
        }
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
        $cond_row['shop_type:IN'] = [0, 1]; //非供应商店铺

        $Sub_SiteModel = new Sub_SiteModel();
        $sub_site_id = request_int('sub_site_id');
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
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
            $Shop_BaseModel = new Shop_BaseModel();
            $data = $Shop_BaseModel->getBaseList($cond_row);
            $this->data->addBody(-140, $data);
        }
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
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
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
            $Shop_BaseModel = new Shop_BaseModel();
            $data = $Shop_BaseModel->getBaseList($cond_row);
            $this->data->addBody(-140, $data, $cond_row);
        }
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
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
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
            $Shop_RenewalModel = new Shop_RenewalModel();
            $data = $Shop_RenewalModel->getRenewalList($cond_row);
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
        $Shop_BaseModel = new Shop_BaseModel();
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

                $shop_base = $Shop_BaseModel->getByWhere(array('shop_name:LIKE' => $shop_account . '%'));
                $shop_id = array_column($shop_base, 'shop_id');
                $cond_row['shop_id:IN'] = $shop_id;
            } else {
                $shop_base = $Shop_BaseModel->getByWhere(array('user_name:LIKE' => $shop_account . '%'));
                $user_id = array_column($shop_base, 'shop_id');
                $cond_row['shop_id:IN'] = $user_id;
            }

        }

        //去除供应商店铺ID
        $Sub_SiteModel = new Sub_SiteModel();
        $sub_site_id = request_int('sub_site_id');
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
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
            $shop_base = $Shop_BaseModel->getByWhere($where);
            $shop_base = array_values($shop_base);
            $shop_id = array_column($shop_base, 'shop_id');

            //求交集
            if ($shop_id && $cond_row['shop_id:IN']) {
                $cond_row['shop_id:IN'] = array_intersect($cond_row['shop_id:IN'], $shop_id);
            }else if($sub_site_district_ids){
                $cond_row['shop_id:IN'] = $shop_id;
            }


            $order = array('shop_id' => 'desc');
            $data = $Shop_BaseModel->getCategorylist($cond_row, $order);
            $this->data->addBody(-140, $data);
        }
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
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
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
            $Shop_BaseModel = new Shop_BaseModel();
            $data = $Shop_BaseModel->getBaseList($cond_row, $order);
            $this->data->addBody(-140, $data);
        }

    }

    public function editCategory()
    {
        //是否开启骑手APP
        $Shop_BaseModel = new Shop_BaseModel();
        $sql = "select * from yf_web_config where config_key = 'Plugin_Delivery'";
        $delivery = $Shop_BaseModel->sql->getAll($sql);
        $is_delivery = $delivery[0]['config_value'];
        include $this->view->getView();
    }

    public function getEditCategory()
    {
        $cond_row['shop_id'] = request_int('shop_id');
        $Shop_BaseModel = new Shop_BaseModel();
        $data = $Shop_BaseModel->getCategorylist($cond_row);

        //是否开启骑手APP
        $sql = "select * from yf_web_config where config_key = 'Plugin_Delivery'";
        $delivery = $Shop_BaseModel->sql->getAll($sql);
        if ($delivery[0]['config_value'] == 1) {
            $data['delivery'] = 1;
        }else{
            $data['delivery'] = 0;
        }
        $this->data->addBody(-140, $data);
    }


}

?>