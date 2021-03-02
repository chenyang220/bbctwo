<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Banchangle
 */
class Seller_Shop_InfoCtl extends Seller_Controller
{

	public $shopBaseModel      = null;
	public $shopClassModel     = null;
	public $shopGradeModel     = null;
	public $shopClassBindModel = null;
	public $shopRenewalModel   = null;
	public $goodsCatModel      = null;

	/**
	 * Constructor
	 *
	 * @param  string $ctl 控制器目录
	 * @param  string $met 控制器方法
	 * @param  string $typ 返回数据类型
	 * @access public
	 */
	public function __construct(&$ctl, $met, $typ)
	{

		parent::__construct($ctl, $met, $typ);
		$this->shopBaseModel      = new Shop_BaseModel();
		$this->shopClassModel     = new Shop_ClassModel();
		$this->shopGradeModel     = new Shop_GradeModel();
		$this->shopClassBindModel = new Shop_ClassBindModel();
		$this->shopRenewalModel   = new Shop_RenewalModel();
		$this->goodsCatModel      = new Goods_CatModel();

	    //查询大华捷通支付是否开启，然后作为全局变量
        // --------------------start-----------------------------
        $paycenter_api_key = Yf_Registry::get('paycenter_api_key');
        $paycenter_app_id = Yf_Registry::get('paycenter_app_id');
        $paycenter_api_url = Yf_Registry::get('paycenter_api_url');

        $formvars = array(
            'app_id'=>$paycenter_app_id
        );
        $parms=  sprintf('%s?ctl=Api_%s&met=%s&typ=json', $paycenter_api_url, 'Pay_Pay', 'yunshanStatus');
        $init_rs = get_url_with_encrypt($paycenter_api_key,$parms,$formvars);
        if ($init_rs['status'] == 200) {
            Yf_Registry::set('yunshanstatus',$init_rs['data']['status']);
        }else{
        	Yf_Registry::set('yunshanstatus',0);
        }
        //-------------------- end -----------------------------

	}

	/**
	 * 首页
	 *
	 * @access public
	 */
	public function info()
	{
		$act = request_string('act');
		//首先判断一下是不是自营店铺如果是自营店铺就没有店铺公司信息以及续费申请
		$shop_id = Perm::$shopId;
		$shop    = $this->shopBaseModel->getOne($shop_id);

		if ($act == 'renew')
		{
			//推算出他的续签时间（前一个月即可申请）
			$frontmonth = date("Y-m-d H:i:s", strtotime("$shop[shop_end_time] - 1 month"));
			$date       = date("Y-m-d h:i:s", time());
			$data       = $this->shopRenewalModel->getRenewalList(array("shop_id" => $shop_id));
			$grade      = $this->shopGradeModel->getGradeWhere();
			// var_dump($grade);
			$this->view->setMet('renew');

		}
		elseif ($act == 'info')
		{
			//店铺信息
			$shopCompanyModel = new Shop_CompanyModel();
			$company          = $shopCompanyModel->getCompanyrow($shop_id);

			if ($company)
			{
				$data = $this->shopBaseModel->getbaseAllList($shop_id);
			}
			else
			{
				$data = array();
			}
		}
		elseif ($act == 'createQrCode')
		{
			$Shop_BaseModel = new Shop_BaseModel();
			$shop_id = Perm::$shopId;
			$shop_base = $Shop_BaseModel->getOne($shop_id); 
			$this->view->setMet('Qrcode');
		}elseif($act == 'setpayconfig'){
			//大华捷通支付的商家配置
			 // 入网配置信息
			  $Ve_ShoppayModel = new Ve_ShoppayModel();
			  $where = array();
			  $where["shop_id"] =   $shop_id;		  
			  $shoppay = $Ve_ShoppayModel  -> getOneByWhere($where);
			$this->view->setMet('setpayconfig');
		}
		else
		{
			//判断是否绑定所有类目
			if ($shop['shop_all_class'])
			{
				$data = array();
			}
			else
			{
				$Yf_Page            = new Yf_Page();
				$Yf_Page->listRows  = 10;
				$rows               = $Yf_Page->listRows;
				$offset             = request_int('firstRow', 0);
				$page               = ceil_r($offset / $rows);
				$data               = $this->shopClassBindModel->getPluralClassBindlist(array("shop_id" => $shop_id), array("shop_class_bind_id"=>"DESC"), $page, $rows);
				$Yf_Page->totalRows = $data['totalsize'];
				$page_nav           = $Yf_Page->prompt();
			}
			$this->view->setMet('category');
		}

		if ('json' == $this->typ)
		{

			$this->data->addBody(-140, $data);

		}
		else
		{
			include $this->view->getView();
		}
	}


	public function delInfo()
	{

		$shop_class_bind_id = request_string('id');
		$shop_id            = Perm::$shopId;
		if ($shop_class_bind_id)
		{
			//判断是不是当前用户操作的
			$class_Bind_info = $this->shopClassBindModel->getOne($shop_class_bind_id);
			if ($shop_id == $class_Bind_info['shop_id'])
			{
				$flag = $this->shopClassBindModel->removeClassBind($shop_class_bind_id);
				if ($flag)
				{
					$status = 200;
					$msg    = __('success');
				}
				else
				{
					$status = 250;
					$msg    = __('failure');
				}
			}
			else
			{
				$status = 250;
				$msg    = __('failure');
			}

		}
		$data = array();
		$this->data->addBody(-140, $data, $msg, $status);
	}


	/**
	 * 
	 *
	 * @access public
	 */
	public function tsSet()
	{
		$Label_BaseModel = new  Label_BaseModel();
        $Label_Base = $Label_BaseModel->getByWhere(array("label_tag_sort:>"=>0,"label_tag_sort:<="=>8));
        $label_name_arr = array_column($Label_Base, "label_name", "id");
		$shop_id            = Perm::$shopId;
		$Shop_BaseModel = new  Shop_BaseModel();
		$Shop_Base = $Shop_BaseModel->getOne($shop_id);
		$label_id_arr  = explode(",", $Shop_Base['label_id']);
		include $this->view->getView();
	}

	/**
	 * 首页
	 *
	 * @access public
	 */
	public function addTsSet()
	{
		$label_id           = trim(request_string('label_id',0),",");
		$label_remarks 		= request_string('label_remarks');
		$shop_id            = Perm::$shopId;
		$Shop_BaseModel = new  Shop_BaseModel();
		$editBase['label_id'] = $label_id;
		$editBase['label_remarks'] = $label_remarks;
		$editBase['label_is_check'] = 0;
		$Shop_Base = $Shop_BaseModel->editBase($shop_id,$editBase);
		if ($Shop_Base) {
			$status = 200;
			$msg    = __('success');
		} else {
			$status = 250;
			$msg    = __('failure');
		}
		$data = array();
		$this->data->addBody(-140, $data, $msg, $status);
	}




	public function addRenew()
	{
		//接收数据
		$shop_grade_id           = request_int('shop_grade');
		$renew_row['renew_time'] = request_int('renew_time');
		//根据等级id获取等级的名称以及单价
		$renew                        = $this->shopGradeModel->getOne($shop_grade_id);
		$renew_row['shop_grade_id']   = $renew['shop_grade_id'];
		$renew_row['shop_grade_name'] = $renew['shop_grade_name'];
		$renew_row['shop_grade_fee']  = $renew['shop_grade_fee'];
		$renew_row['renew_cost']      = $renew_row['renew_time'] * $renew['shop_grade_fee'];
		$renew_row['create_time']     = date("Y-m-d H:i:s", time());

		$shop_id = Perm::$shopId;
		//根据店铺id查询出店铺信息
		$shop_row = $this->shopBaseModel->getOne($shop_id);

		$renew_row['start_time'] = $shop_row['shop_end_time'];
		$renew_row['shop_name']  = $shop_row['shop_name'];
		$renew_row['shop_id']    = $shop_row['shop_id'];
		//续费结束时间等于店铺结束时间 + 续费的年数
		$renew_row['end_time'] = date("Y-m-d H:i:s", strtotime("$shop_row[shop_end_time] + $renew_row[renew_time] year"));
		$renew_row['status']   = 0;
        //获取店铺位置
        $renew_row['district_id']  = $shop_row['district_id'];
		$flag                  = $this->shopRenewalModel->addRenewal($renew_row);
		if ($flag)
		{
			$status = 200;
			$msg    = __('success');

		}
		else
		{

			$status = 250;
			$msg    = __('failure');

		}
		$data = array();
		$this->data->addBody(-140, $data, $msg, $status);

	}

	public function delRenew()
	{
		$renew_id = request_string('id');
		$shop_id  = Perm::$shopId;
		if ($renew_id)
		{
			//判断是不是当前用户操作的
			$renew_info = $this->shopRenewalModel->getOne($renew_id);
			if ($shop_id == $renew_info['shop_id'])
			{
				$flag = $this->shopRenewalModel->removeRenewal($renew_id);
				if ($flag)
				{
					$status = 200;
					$msg    = __('success');
				}
				else
				{
					$status = 250;
					$msg    = __('failure');
				}
			}
			else
			{
				$status = 250;
				$msg    = __('failure');
			}

		}
		$data = array();
		$this->data->addBody(-140, $data, $msg, $status);
	}

	//加载添加类目页面
	public function addcategoryInfo()
	{
		include $this->view->getView();
	}
	//加载添加商品标签
	public function addGoodsLabel()
	{

		 $shop_id = Perm::$shopId;
		 $Label_BaseModel = new  Label_BaseModel();
		 $Shop_BaseModel = new Shop_BaseModel();
            $Shop_Base = $Shop_BaseModel->getOne($shop_id);
            $Label_Base = array();
            if ($Shop_Base['label_is_check'] == 0) {
                $label_id = "";
            } else {
                $label_id = trim($Shop_Base['label_id'],",");
                $label_base_arr = $Label_BaseModel->getByWhere("*");
	            $label_name_arr = array_column($label_base_arr, null,"id");
	            foreach ($label_base_arr as $k => $label_base) {
	                if ($label_base['label_tag_sort'] == 0) {
	                    $label_id = trim($label_id,",") . "," . $label_base['id'];
	                }
	            }
	            
	            $label_id_arr = explode(",", trim($label_id,","));
	            foreach ($label_id_arr as $key => $label_id) {
	                $Label_Base[$label_id] = $label_name_arr[$label_id];
	            }
            }
            
	
		include $this->view->getView();
	}
	public function addcategoryrow()
	{
		$cat_id_str = trim(request_string('cat_id'), ',');
		$cat_id_arr = explode(",",$cat_id_str);
		if ($cat_id_str ) {
            $good_cat_arr                   = $this->goodsCatModel->getByWhere(array("cat_id :IN"=>$cat_id_arr));
            $cat_commission_arr 			= array_column($good_cat_arr, 'cat_commission','cat_id');

            $cat_commission_str 			= implode(",",$cat_commission_arr);
            $data['commission_rate']        = $cat_commission_str;
            $data['shop_class_bind_enable'] = "1";
            $data['shop_id']                = Perm::$shopId;
            $data['product_class_id']       = $cat_id_str;
            $flag                           = $this->shopClassBindModel->addClassBind($data);
            $status = 200;
			$msg    = __('添加成功');
		} else {
			$status = 250;
			$msg    = __('请选择添加类目');
		}

		$date = array();
		$this->data->addBody(-140, $date, $msg, $status);

	}

	    // 添加账户信息
    public function addEditshppay()
    {
        //接收数据
        $payshopname = request_string('payshopname');
        $payshopnumer = request_string('payshopnumer');
        $payshopcode = request_string('payshopcode');
        $paytermnumber = request_string('paytermnumber');
        $cbpayshopnumer = request_string('cbpayshopnumer');
        $xcxpayshopnumer = request_string('xcxpayshopnumer');
        $shop_id = Perm::$shopId;
        $user_id = Perm::$row['user_id'];
        if(!$shop_id){
          $data = array();
          $msg = "请先登录" ;
          $status = 250 ;
          return $this->data->addBody(-140, $data, $msg, $status);
        }
        if(!$payshopname){
          $data = array();
          $msg = "请输入商户名称" ;
          $status = 250 ;
          return $this->data->addBody(-140, $data, $msg, $status);
        }
        if(!$payshopnumer){
          $data = array();
          $msg = "请输入APP支付商户号" ;
          $status = 250 ;
          return $this->data->addBody(-140, $data, $msg, $status);
        
        }
        if(!$cbpayshopnumer){
          $data = array();
          $msg = "请输入C扫B支付商户号" ;
          $status = 250 ;
          return $this->data->addBody(-140, $data, $msg, $status);
        }

        if(!$xcxpayshopnumer){
          $data = array();
          $msg = "请输入小程序支付商户号" ;
          $status = 250 ;
          return $this->data->addBody(-140, $data, $msg, $status);
        }

        $Ve_ShoppayModel = new Ve_ShoppayModel();
        $where = array();
        $where["shop_id"] = $shop_id ;
        $shoppay = $Ve_ShoppayModel -> getOneByWhere($where );
        $flag = true ;
        if($shoppay){
            $fieldrow = array();
            $fieldrow["payshopname"] = $payshopname  ;
            $fieldrow["payshopnumer"] = $payshopnumer  ;
            $fieldrow["payshopcode"] = $payshopcode  ;
            $fieldrow["paytermnumber"] = $paytermnumber  ;

            $fieldrow["cbpayshopnumer"] = $cbpayshopnumer  ;
            $fieldrow["xcxpayshopnumer"] = $xcxpayshopnumer  ;
            $Ve_ShoppayModel ->editInfo($shoppay["id"],$fieldrow);
        }else{

            $fieldrow = array();
            $fieldrow["payshopname"] = $payshopname  ;
            $fieldrow["payshopnumer"] = $payshopnumer  ;
            $fieldrow["payshopcode"] = $payshopcode  ;
            $fieldrow["paytermnumber"] = $paytermnumber  ;
            $fieldrow["shop_id"] = $shop_id ;
            $fieldrow["user_id"] = $user_id ;
            $fieldrow["status"] = "1" ;
            $fieldrow["cbpayshopnumer"] = $cbpayshopnumer  ;
            $fieldrow["xcxpayshopnumer"] = $xcxpayshopnumer  ;
            $Ve_ShoppayModel ->addInfo($fieldrow);
        }
         if ($flag)
        {
            $status = 200;
            $msg    = __('success');
        }
        else
        {
            $status = 250;
            $msg    = __('failure');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }
}

?>