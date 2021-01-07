<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Supplier_ManageCtl extends AdminController
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

//		$cond_row = array(
//			"shop_self_support" => "false"
//		);
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
        $cond_row['shop_type'] = 2; //非供应商店铺
        $Shop_BaseModel = new Shop_BaseModel();
        $data = $Shop_BaseModel->getBaseList($cond_row);


        $this->data->addBody(-140, $data);
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
        if ($shop_class) {
            $cond_row['shop_class_id'] = $shop_class;
        }
        $cond_row['shop_type'] = 2; //非供应商店铺
        $Shop_BaseModel = new Shop_BaseModel();
        $data = $Shop_BaseModel->getBaseList($cond_row);
        $this->data->addBody(-140, $data);
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
        $cond_row['shop_type'] = 2; //非供应商店铺

        //判断分站信息
        $sub_site_id = request_int('sub_site_id');
        $User_BaseModel = new User_BaseModel();
        $user_base = $User_BaseModel->getOne(Perm::$userId);
        if(!$sub_site_id){
            $sub_site_id = $user_base['sub_site_id'];
        }
        $sub_flag = true;
        if ($sub_site_id > 0) {
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
            $Shop_BaseModel = new Shop_BaseModel();
            $data = $Shop_BaseModel->getBaseList($cond_row);
            $this->data->addBody(-140, $data);
        };
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
        $shop_class = request_string('shop_class');
        if ($shop_class) {
            $cond_row['shop_class_id'] = $shop_class;
        }
        $shopBaseModel = new Shop_BaseModel;
        $shop_ids = $shopBaseModel->getShopId(['shop_type' => 2]);
        $cond_row['shop_id:IN'] = $shop_ids;
        $Shop_RenewalModel = new Shop_RenewalModel();
        $data = $Shop_RenewalModel->getRenewalList($cond_row);
        $this->data->addBody(-140, $data);
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
                $shop_shop_id = array_column($shop_base, 'shop_id');
            } else {
                $shop_base = $Shop_BaseModel->getByWhere(array('user_name:LIKE' => $shop_account . '%'));
                $user_shop_id = array_column($shop_base, 'shop_id');
            }
        }
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
        //供应商店铺ID
        $shop_base = $Shop_BaseModel->getByWhere(array('shop_type' => 2));
        $shop_id = array_column($shop_base, 'shop_id');
        if ($shop_shop_id) {
            $cond_row['shop_id:IN'] = array_intersect($shop_id, $shop_shop_id);
        } else if ($user_shop_id) {
            $cond_row['shop_id:IN'] = array_intersect($shop_id, $user_shop_id);
        } else {
            $cond_row['shop_id:IN'] = $shop_id;
        }

        $order = array('shop_id' => 'desc');
        $data = $Shop_BaseModel->getCategorylist($cond_row, $order);
        $this->data->addBody(-140, $data, $cond_row);
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
        $cond_row['shop_type'] = 2; //非供应商店铺
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
        $order = array('shop_id' => 'asc');
        $Shop_BaseModel = new Shop_BaseModel();
        $data = $Shop_BaseModel->getBaseList($cond_row, $order);
        $this->data->addBody(-140, $data);
    }


}

?>