<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Seller_Promotion_BargainCtl extends Seller_Controller
{
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

		
	}

	/**
	 * 首页
	 * @access public
	 * 卖家发布的代金券列表
	 */
	public function index()
	{
	    $op = request_string('op');
	    $bargain_id = request_int('id');
        $Bargain_BaseModel = new Bargain_BaseModel();

        if($op == 'edit'){
	        $data = $Bargain_BaseModel->getBargainInfoByBargainId($bargain_id);
            $this->view->setMet('edit');
        }else if($op == 'detail'){
            $bargain_info = $Bargain_BaseModel->getBargainInfo($bargain_id);
            
            $page = request_int('page', 1);
            $rows = request_int('rows', 100);
            $cond_row = array();
            $cond_row['bargain_id'] = $bargain_id;
            $Bargain_BuyUserModel = new Bargain_BuyUserModel();
            $data = $Bargain_BuyUserModel->getBuyUserBargainList($cond_row, array('create_time' => 'DESC'), $page, $rows);
            $this->view->setMet('detail');
        }else{
            $page = request_int('page', 1);
            $rows = request_int('rows', 100);
            $cond_row = array();
            if (request_string('keyword')) {
                $cond_row['goods_name'] = request_string('keyword');
            }
            if (request_int('status')) {
                $cond_row['bargain_status'] = request_int('status');
            }
            $cond_row['shop_id'] = Perm::$shopId;

            $BargainComboModel = new Bargain_QuotaModel();
            $combo_info = $BargainComboModel->getOneByWhere(array('shop_id' => Perm::$shopId));
            $data = $Bargain_BaseModel->getBargainList($cond_row, array('create_time' => 'DESC'), $page, $rows);
        }
        include $this->view->getView();
    }


    public function add()
	{
        $BargainComboModel = new Bargain_QuotaModel();
        $combo_info = $BargainComboModel->getOneByWhere(array('shop_id' => Perm::$shopId));
        $combo['combo_end_time'] = $combo_info['combo_end_time'];
        if ($combo['combo_end_time'] > date('Y-m-d H:i:s', time())) {
            include $this->view->getView();
        }else{
            location_to(Yf_Registry::get('url') . '?ctl=Seller_Promotion_Bargain&met=combo');
        }
    }

    //购买套餐、套餐续费
    public function combo()
	{
        //查找出店铺团购活动的消费记录
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $cond_row = array();
        $order_row = array();
        $shopCostModel = new Shop_CostModel();
        $cond_row['shop_id'] = Perm::$shopId;
        $cond_row['activity_type'] = Shop_CostModel::BARGAIN;
        $data = $shopCostModel->listByWhere($cond_row, $order_row, $page, $rows);

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
	    include $this->view->getView();
   }

    /*
      * 在店铺的账期结算中扣除相关费用 购买套餐
      * */
    public function addCombo()
    {
        $field = array();
        $rs_row = array();

        $BargainComboModel = new Bargain_QuotaModel();
        $ShopCostModel = new Shop_CostModel();
        $ShopBaseModel = new Shop_BaseModel();
        $shopInfo = $ShopBaseModel->getOne(Perm::$shopId);//店铺信息
        $month_price = Web_ConfigModel::value('bargain_buy_price');
        $month = request_int('month');
        $days = 30 * $month;

        if ($month > 0) {
            $BargainComboModel->sql->startTransactionDb();
            //记录到店铺费用表
            $field_row['user_id'] = Perm::$userId;
            $field_row['shop_id'] = Perm::$shopId;
            $field_row['cost_price'] = $month_price * $month;
            $field_row['cost_desc'] = __('店铺购买砍价活动消费');
            $field_row['cost_status'] = 0;
            $field_row['cost_time'] = get_date_time();
            $field_row['activity_type'] = Shop_CostModel::BARGAIN;
            $field_row['activity_price'] = $month_price;
            $field_row['activity_month'] = $month;
            $flag = $ShopCostModel->addCost($field_row, true);
            check_rs($flag, $rs_row);
            if ($flag) {
                //购买或续费套餐
                $combo_row = $BargainComboModel->getBargainQuotaByShopID(Perm::$shopId);

                //记录已经存在，套餐续费
                if ($combo_row) {
                    //1、原套餐已经过期,更新套餐开始时间和结束时间
                    if (strtotime($combo_row['combo_end_time']) < time()) {
                        $field['combo_start_time'] = get_date_time();
                        $field['combo_end_time'] = date('Y-m-d H:i:s', strtotime("+$days days"));
                    } elseif ((time() >= strtotime($combo_row['combo_start_time'])) && (time() <= strtotime($combo_row['combo_end_time']))) {
                        //2、原套餐尚未过期，只需更新结束时间
                        $field['combo_end_time'] = date('Y-m-d H:i:s', strtotime("+$days days", strtotime($combo_row['combo_end_time'])));
                    }
                    $field['paycount'] = number_format($combo_row['pay_count'] + $month_price * $month);
                  
                    $op_flag = $BargainComboModel->renewBargainCombo($combo_row['combo_id'], $field);
                } else            //记录不存在，添加套餐
                {
                    $field['combo_start_time'] = get_date_time();
                    $field['combo_end_time'] = date('Y-m-d H:i:s', strtotime("+$days days"));
                    $field['shop_id'] = Perm::$shopId;
                    $field['shop_name'] = $shopInfo['shop_name'];
                    $field['user_id'] = Perm::$userId;
                    $field['user_nickname'] = Perm::$row['user_account'];
                    $field['paycount'] = number_format($month_price * $month);

                    $op_flag = $BargainComboModel->addBargainCombo($field, true);
                }
                check_rs($op_flag, $rs_row);
            }
            if (is_ok($rs_row)) {
                //在paycenter中添加交易记录
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');

                $formvars = array();
                $formvars['app_id'] = $shop_app_id;
                $formvars['buyer_user_id'] = Perm::$userId;
                $formvars['buyer_user_name'] = Perm::$row['user_account'];
                $formvars['amount'] = $month_price * $month;

                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addCombo&typ=json', $url), $formvars);
            }

            if (is_ok($rs_row) && isset($rs) && $rs['status'] == 200 && $BargainComboModel->sql->commitDb()) {
                $msg = __('操作成功！');
                $status = 200;
            } else {
                $BargainComboModel->sql->rollBackDb();
                $msg = __('操作失败！');
                $status = 250;
            }
        } else {
            $msg = __('购买月份必须为正整数！');
            $status = 250;
        }
        $this->data->addBody(-140, $field, $op_flag, $status);
    }

   //获取当前店铺可参与砍价活动的商品
    public function getBargainGoods()
    {
        //分页
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows') ? request_int('listRows') : 8;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        //当前店铺非分销商品common_id
        $Goods_CommonModel = new Goods_CommonModel();
        $all_common_ids = $Goods_CommonModel->getNoSupplierCommonIds(Perm::$shopId);
        if ($all_common_ids) {
            //排除plus商品 @nsy 2019-04-13
            $goodsPlusGoodsModel = new Plus_GoodsModel();
            $plus_common_ids = $goodsPlusGoodsModel->getSellerShopPlusGoodsList();
            //排除礼包商品
            $gift_common_id = $Goods_CommonModel->getGiftList();
            // $s_g_common_id = array();
            if ($plus_common_ids || $gift_common_id) {
                $s_g_common_id = array_merge($plus_common_ids, $gift_common_id);
            }

            foreach ($all_common_ids as $k => $item) {
                if (in_array($item, $s_g_common_id)) {
                    unset($all_common_ids[$k]);
                }
                // if(in_array($item,$plus_common_ids)){
                //     unset($all_common_ids[$k]);
                // }
            }
            $goods_row['common_id:IN'] = $all_common_ids;
        }
        $goods_name = request_string('goods_name');
        if ($goods_name) {
            $goods_row['goods_name:LIKE'] = "%" . $goods_name . "%";
        }

        $Bargain_BaseModel = new Bargain_BaseModel();
        $data = $Bargain_BaseModel->getBargainGoodsList($goods_row, array('goods_id' => 'DESC'), $page, $rows);

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        include $this->view->getView();
    }

    //添加砍价活动
    public function addBargain()
    {
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_info = $Shop_BaseModel->getOneByWhere(array('shop_id'=> Perm::$shopId));
        $add_row['shop_id'] = $shop_info['shop_id'];
        $add_row['shop_name'] = $shop_info['shop_name'];
        $add_row['goods_id'] = request_int('goods_id');
        $add_row['goods_price'] = request_string('goods_price');
        $add_row['bargain_price'] = request_string('bargain_price');
        $add_row['bargain_stock'] = request_int('bargain_stock');
        $add_row['bargain_stock_count'] = request_int('bargain_stock');
        $add_row['bargain_type'] = request_string('bargain_type');
        $add_row['bargain_num_price'] = request_string('bargain_num_price');
        $add_row['bargain_desc'] = request_string('bargain_desc');
        $add_row['start_time'] = strtotime(request_string('start_time'));
        $add_row['end_time'] = strtotime(request_string('end_time'));
        $add_row['create_time'] = time();
        if(request_string('start_time') && strtotime(request_string('start_time')) > time()){
            $add_row['bargain_status'] = Bargain_BaseModel::WILLON;
        }else{
            $add_row['bargain_status'] = Bargain_BaseModel::ISON;
        }
        $Bargain_BaseModel = new Bargain_BaseModel();
        $flag = $Bargain_BaseModel->addBargain($add_row);
        if($flag){
            $status = 200;
            $msg = 'success';
        }else{
            $status = 250;
            $msg = 'failure';
        }
        $this->data->addBody(-140, array(),$msg,$status);
    }

    //编辑状态
    public function editBargain()
    {
        $bargain_id = request_row('id');
        $bargain_id = $bargain_id[0];

        $rs_row = array();
        $Bargain_BaseModel = new Bargain_BaseModel();
        $flag1 = $Bargain_BaseModel->editBargain($bargain_id,array('bargain_status'=> Bargain_BaseModel::ADMINOFF));
        check_rs($flag1, $rs_row);

        //对应砍价活动砍价结束
        $Bargain_BuyUserModel = new Bargain_BuyUserModel();
        $info = $Bargain_BuyUserModel->getByWhere(array('bargain_id' => $bargain_id));
        if($info){
            $buy_ids = array_column($info, 'buy_id');
            $edit_row['bargain_state'] = Bargain_BuyUserModel::ADMINOFF;
            $flag2 = $Bargain_BuyUserModel->editBuyUser($buy_ids, $edit_row);
            check_rs($flag2, $rs_row);
        }

        $flag = is_ok($rs_row);

        if ($flag) {
            $status = 200;
            $msg = '操作成功';
        } else {
            $status = 250;
            $msg = '操作失败';
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    //删除活动
    public function delBargain()
    {
        $bargain_id = request_string('id');
        $Bargain_BaseModel = new Bargain_BaseModel();
        $flag = $Bargain_BaseModel->editBargain($bargain_id, array('is_del' => Bargain_BaseModel::is_del));
        if ($flag) {
            $status = 200;
            $msg = '删除成功';
        } else {
            $status = 250;
            $msg = 'failure';
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }


}

?>