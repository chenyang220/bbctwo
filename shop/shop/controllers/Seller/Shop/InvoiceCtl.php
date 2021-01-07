<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Banchangle
 */
class Seller_Shop_InvoiceCtl extends Seller_Controller
{
	public $shop_invoice_model = null;
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
		$this->shop_invoice_model = new ShopInvoiceModel();
	}
	/**
	 * 首页
	 *
	 * @access public
	 */
	public function invoice()
	{
		$act = request_string('act');
        if ($act == 'edit') {
            $nav_id = request_int('nav_id');
            $data = $this->shop_invoice_model->getNavinfo($nav_id);
            $this->view->setMet('setNav');


        } elseif ($act == 'add') {
            $this->view->setMet('setNav');
            $data = array();
        } else {
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = 10;
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            $cond_row = array();
            $cond_row['shop_id'] = Perm::$shopId;
            $data = $this->shop_invoice_model->getInvoiceList($cond_row, array(), $page, $rows);
            // var_dump($data);
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav = $Yf_Page->prompt();

        }
        if ('json' == $this->typ) {

            $this->data->addBody(-140, $data);

        } else {
            include $this->view->getView();
        }
    }

    /**
     * 获取用户的发票信息
     *
     * @author Zhuyt
     */
    public function setInvoice()
    {
        $act = request_string('act');
        $invoice_id = request_string('invoice_id');
        //获取一级地址
        $district_parent_id = request_int('pid', 0);
        $baseDistrictModel = new Base_DistrictModel();
        $district = $baseDistrictModel->getDistrictTree($district_parent_id);

        //获取用户的发票信息
        if ($act == "edit"){
            $data = current($this->shop_invoice_model->getInvoice($invoice_id));
        }

        if ($this->typ == 'json') {
//            if ($data['normal']) {
//                $da['normal'] = $data['normal'];
//            } else {
//                $da['normal'] = array();
//            }
//
//            if ($data['electron']) {
//                $da['electron'] = $data['electron'];
//            } else {
//                $da['electron'] = array();
//            }
//
//            if ($data['addtax']) {
//                $da['addtax'] = $data['addtax'];
//            } else {
//                $da['addtax'] = array();
//            }
            $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }


    }

    /**
     * 添加商家发票信息
     *
     * @author xue
     */
    public function addInvoice()
    {
        $invoice_id = request_int('invoice_id');
        //新增模板判断该模板名称是否被使用
        $flag = $this->shop_invoice_model->getByWhere(array("invoice_name"=>request_string('invoice_name')));
        if (!empty($flag) && empty($invoice_id)){
            return $this->data->addBody(-140, $flag, "该模板名称已被使用", 250);
        }

        $add_row['user_id']              = Perm::$row['user_id'];                    //会员id
        $add_row['shop_id']              = Perm::$shopId;                    //店铺id
        $add_row['invoice_title']        = request_string('invoice_title');        //发票抬头
        $add_row['invoice_content']      = request_string('invoice_content');        //发票抬头
        $add_row['invoice_name']         = request_string('invoice_name');        //发票模板名称
        $add_row['invoice_state']        = request_int('invoice_state');            //发票类型 1普通发票 2电子发票 3增值税发票
        $add_row['invoice_company']      = request_string('invoice_company');        //单位名称
        $add_row['invoice_code']         = request_string('invoice_code');            //纳税人识别号
        $add_row['invoice_reg_addr']     = request_string('invoice_reg_addr');        //注册地址
        $add_row['invoice_reg_phone']    = request_string('invoice_reg_phone');    //注册电话
        $add_row['invoice_reg_bname']    = request_string('invoice_reg_bname');        //开户银行
        $add_row['invoice_reg_baccount'] = request_string('invoice_reg_baccount');    //银行账户
        $add_row['invoice_rec_name']     = request_string('invoice_rec_name');        //收票人姓名
        $add_row['invoice_rec_phone']    = request_string('invoice_rec_phone');    //收票人手机号
        $add_row['area_code']            = request_string('area_code');    //收票人手机号区号
        $add_row['invoice_rec_email']    = request_string('invoice_rec_email');    //收票人邮箱
        $add_row['invoice_rec_province'] = request_string('invoice_rec_province'); //收票人省份
        $add_row['invoice_goto_addr']    = request_string('invoice_goto_addr');    //送票地址
        $add_row['invoice_province_id']  = request_int('invoice_province_id');
        $add_row['invoice_city_id']      = request_int('invoice_city_id');
        $add_row['invoice_area_id']      = request_int('invoice_area_id');
        $add_row['is_use']               = request_int('invoice_use');

        //检测数据
        $checkInvoiceData = $this->checkInvoiceData($add_row);
        if(!$checkInvoiceData['status']){
            $msg = !isset($checkInvoiceData['msg']) ? __() : $checkInvoiceData['msg'];
            return $this->data->addBody(-140, array(), $msg, 250);
        }

        //如果添加的模板为开启，则关闭其他的所有模板
        if ($add_row['is_use'] == 2){
            $invoice_all = $this->shop_invoice_model->getByWhere(array("is_use"=>2));
            foreach ($invoice_all as $key => $val){
                $cond_row['is_use'] = 1;
                $this->shop_invoice_model->editInvoice($val['invoice_id'],$cond_row);
            }
        }
        if ($invoice_id) {
            $flag = $this->shop_invoice_model->editInvoice($invoice_id, $add_row);
        } else {
            $flag = $this->shop_invoice_model->addInvoice($add_row, true);
            $invoice_id = $flag;
        }

        if ($flag !== false) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('failure');
        }
        $data = array('invoice_id' => $invoice_id);
        $this->data->addBody(-140, $data, $msg, $status);

    }

    /**
     * 检测发票数据
     * @param type $data
     * @return type
     */
    private function checkInvoiceData($data){
        if(!in_array($data['invoice_state'], array(1,2,3))){
            return array('status'=>false,'msg'=>__('请选择发票类型'));
        }
        if($data['invoice_state'] == 1 || $data['invoice_state'] == 2){
            if($data['invoice_title'] != "个人"){
                if(!$data['invoice_code']){
                    return array('status'=>false,'msg'=>__('请填写企业税号'));
                }
            }
        }

        if($data['invoice_state'] == 2 || $data['invoice_state'] == 3){
            if(!Yf_Utils_String::isMobile($data['invoice_rec_phone']) && $data['area_code'] == 86){
                return array('status'=>false,'msg'=>__('请填写正确的手机号码'));
            }
        }

        if($data['invoice_state'] == 3){
            if(!preg_match('/^\d{5,20}$/',$data['invoice_reg_phone'])){
                return array('status'=>false,'msg'=>__('请填写正确的注册电话'));
            }
            if(!$data['invoice_company']){
                return array('status'=>false,'msg'=>__('请填写单位名称'));
            }
            if(!$data['invoice_code']){
                return array('status'=>false,'msg'=>__('请填写纳税人识别码'));
            }
            if(!$data['invoice_reg_addr']){
                return array('status'=>false,'msg'=>__('请填注册地址'));
            }
            if(!$data['invoice_reg_bname']){
                return array('status'=>false,'msg'=>__('请填写开户银行'));
            }
            if(!$data['invoice_reg_baccount']){
                return array('status'=>false,'msg'=>__('请填银行账户'));
            }
            if(!$data['invoice_rec_name']){
                return array('status'=>false,'msg'=>__('请填写收票人姓名'));
            }
            if(!trim($data['invoice_rec_province'])){
                return array('status'=>false,'msg'=>__('请选择收票人省份和城市'));
            }
            if(!$data['invoice_goto_addr']){
                return array('status'=>false,'msg'=>__('请填写送票地址'));
            }
            if(!Yf_Utils_String::isMobile($data['invoice_rec_phone'])){
                return array('status'=>false,'msg'=>__('请填写正确的收票人手机'));
            }
        }
        return array('status'=>true);
    }

    public function delInvoice()
    {
        $invoice_id = request_row("id");

        $flag = $this->shop_invoice_model->removeInvoice($invoice_id);

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

}

?>