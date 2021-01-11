<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Shop_SelfsupportCtl extends AdminController
{
    
    public function setIm(){
        $ctl = $_REQUEST['ctl'];
		$met = $_REQUEST['met'];
		$data = $this->getUrl($ctl, $met);
        $user_account = $data['user_account'];
        include $this->view->getView();
        
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

        $cond_row = array("shop_self_support" => "true");

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
        $cond_row['shop_type'] = 1;
        $Shop_BaseModel = new Shop_BaseModel();
        $data = $Shop_BaseModel->getBaseList($cond_row);
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
        $this->data->addBody(-140, $data);
    }
    
}

?>