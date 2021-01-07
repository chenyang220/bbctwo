<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Seller_Promotion_SeckillCtl extends Seller_Controller
{
	public $seckillBaseModel  = null;
	public $seckillGoodsModel = null;
	public $seckillQuotaModel = null;
	public $goodsBaseModel     = null;
	public $shopCostModel      = null;
	public $shopBaseModel      = null;
	
	public $combo_flag        = false;
	public $shop_info         = array();  //店铺信息
	public $self_support_flag = false;    //是否为自营店铺

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

		if (!Web_ConfigModel::value('seckill_allow')) //团购功能设置，关闭，跳转到卖家首页
		{
			if ("e" == $this->typ)
			{
				$this->view->setMet('error');
				include $this->view->getView();
				die;
			}
			else
			{
				$data = new Yf_Data();
				$data->setError(__('秒杀功能已关闭'), 30);
				$d = $data->getDataRows();

				$protocol_data = Yf_Data::encodeProtocolData($d);
				echo $protocol_data;
				exit();
			}
		}

		$this->seckillBaseModel  = new Seckill_BaseModel();
		$this->seckillGoodsModel = new Seckill_GoodsModel();
		$this->seckillQuotaModel = new Seckill_QuotaModel();
		$this->goodsBaseModel     = new Goods_BaseModel();
		$this->shopCostModel      = new Shop_CostModel();
		$this->shopBaseModel      = new Shop_BaseModel();

		$this->shop_info         = $this->shopBaseModel->getOne(Perm::$shopId);//店铺信息
		$this->self_support_flag = ($this->shop_info['shop_self_support'] == "true" || Web_ConfigModel::value('promotion_seckill_price') == 0) ? true : false; //是否为自营店铺标志
		if ($this->self_support_flag) //平台店铺，没有套餐限制
		{
			$this->combo_flag = true;
		}
		else
		{
			$this->combo_flag = $this->seckillQuotaModel->checkQuotaStateByShopId(Perm::$shopId);//普通店铺需要查询套餐状态
		}

		
	}


	/**
	 * 首页
	 * @access public
	 * 卖家发布的秒杀活动
	 */
	public function index()
	{	
	    $data   = array();
		$combo_row = array();

        if(request_string('op') == 'manage')
        {
            $seckill_id = request_int('id');

            if ($seckill_id)
            {
                $cond_row['seckill_id']     = $seckill_id;
                $cond_row['shop_id']         = Perm::$shopId;
                $data['seckill_detail']     = $this->seckillBaseModel->getSeckillActInfo($cond_row);
                $data['seckill_goods_rows'] = $this->seckillGoodsModel->getSeckillGoods($cond_row, array('seckill_goods_id' => 'DESC'));
            }
            else
            {
                location_go_back('活动不存在');
            }

            $this->view->setMet('manage');
        }
        else if(request_string('op') == 'detail')
        {
            $seckill_id = request_int('id'); 

            if ($seckill_id)
            {
               
                $data     = $this->seckillBaseModel->getOne($seckill_id);
                
                
            	if($data['seckill_time_slot']==1){
    				$data['seckill_time_slot'] = '0:00~2:00';
	        	}
	        	if($data['seckill_time_slot']==2){
	        		$data['seckill_time_slot'] = '8:00~10:00';
	        	}
	        	if($data['seckill_time_slot']==3){
	        		$data['seckill_time_slot'] = '10:00~12:00';
	        	}
	        	if($data['seckill_time_slot']==4){
	        		$data['seckill_time_slot'] = '12:00~14:00';
	        	}
	        	if($data['seckill_time_slot']==5){
	        		$data['seckill_time_slot'] = '14:00~16:00';
	        	}
	        	if($data['seckill_time_slot']==6){
	        		$data['seckill_time_slot'] = '16:00~18:00';
	        	}
	        	if($data['seckill_time_slot']==7){
	        		$data['seckill_time_slot'] = '18:00~20:00';
	        	}
	        	if($data['seckill_time_slot']==8){
	        		$data['seckill_time_slot'] = '20:00~22:00';
	        	}
	        	if($data['seckill_time_slot']==9){
	        		$data['seckill_time_slot'] = '22:00~0:00';
	        	}
	        	if($data['seckill_state']==0){
	        		$data['seckill_states'] = '待审核';
	        	}
	        	if($data['seckill_state']==1){
	        		$data['seckill_states'] = '正常';
	        	}
	        	if($data['seckill_state']==2){
	        		$data['seckill_states'] = '结束';
	        	}
	        	if($data['seckill_state']==3){
	        		$data['seckill_states'] = '管理员关闭';
	        	}
	        	if($data['seckill_state']==4){
	        		$data['seckill_states'] = '已驳回';
	        	}
	        	if($value['seckill_state']==5){
        			$data['items'][$key]['seckill_states'] = '已撤销';
        		}	
                
            }
            else
            {
                location_go_back('活动不存在');
            }

            $this->view->setMet('detail');
        }
        else if(request_string('op') == 'edit')
        {
        	$cond_row['seckill_id'] = request_int('id');
			$cond_row['shop_id']     = Perm::$shopId;
			$data                    = $this->seckillBaseModel->getSeckillActInfo($cond_row);

			
	        if (!$this->self_support_flag)  //普通店铺
			{
				$combo = $this->seckillQuotaModel->getSeckillQuotaByShopID(Perm::$shopId);
			}
			else // 自营店铺
			{
				$combo['combo_end_time'] = date("Y-m-d H:i:s", strtotime("11 june 2030"));
			}
		  
			$this->view->setMet('edit');
            
        }
        else
        {
            $Yf_Page           = new Yf_Page();
            $Yf_Page->listRows = request_int('listRows')?request_int('listRows'):10;
            $rows              = $Yf_Page->listRows;
            $offset            = request_int('firstRow', 0);
            $page              = ceil_r($offset / $rows);

            $cond_row['shop_id'] = Perm::$shopId;         //店铺ID

            if (request_string('keyword')) {
                $cond_row['seckill_name:LIKE'] = '%'.request_string('keyword') . '%';
            }
            if(request_int('state')){
            	if(request_int('state')==4){
            		$cond_row['seckill_state'] = 0;
            	}else{
            		$cond_row['seckill_state'] = request_int('state');
            	}
            	
            }

            $data               = $this->seckillBaseModel->getSeckillActList($cond_row, array('seckill_id' => 'DESC'), $page, $rows);
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav           = $Yf_Page->prompt();
        }

        $shop_type = $this->self_support_flag;

        if (!$this->self_support_flag)  //普通店铺
        {
            $com_flag = $this->combo_flag;

            if ($this->combo_flag)//套餐可用
            {
            	
                $combo_row = $this->seckillQuotaModel->getSeckillQuotaByShopID(Perm::$shopId);
            }
        }
        
        foreach ($data['items'] as $key => $value) {
        	if($value['seckill_time_slot']==1){
        		$data['items'][$key]['seckill_time_slot'] = '0:00~2:00';
        	}
        	if($value['seckill_time_slot']==2){
        		$data['items'][$key]['seckill_time_slot'] = '8:00~10:00';
        	}
        	if($value['seckill_time_slot']==3){
        		$data['items'][$key]['seckill_time_slot'] = '10:00~12:00';
        	}
        	if($value['seckill_time_slot']==4){
        		$data['items'][$key]['seckill_time_slot'] = '12:00~14:00';
        	}
        	if($value['seckill_time_slot']==5){
        		$data['items'][$key]['seckill_time_slot'] = '14:00~16:00';
        	}
        	if($value['seckill_time_slot']==6){
        		$data['items'][$key]['seckill_time_slot'] = '16:00~18:00';
        	}
        	if($value['seckill_time_slot']==7){
        		$data['items'][$key]['seckill_time_slot'] = '18:00~20:00';
        	}
        	if($value['seckill_time_slot']==8){
        		$data['items'][$key]['seckill_time_slot'] = '20:00~22:00';
        	}
        	if($value['seckill_time_slot']==9){
        		$data['items'][$key]['seckill_time_slot'] = '22:00~0:00';
        	}
        	if($value['seckill_state']==0){
        		$data['items'][$key]['seckill_states'] = '待审核';
        	}
        	if($value['seckill_state']==1){
        		$data['items'][$key]['seckill_states'] = '正常';
        	}
        	if($value['seckill_state']==2){
        		$data['items'][$key]['seckill_states'] = '结束';
        	}
        	if($value['seckill_state']==3){
        		$data['items'][$key]['seckill_states'] = '管理员关闭';
        	}
        	if($value['seckill_state']==4){
        		$data['items'][$key]['seckill_states'] = '已驳回';
        	}
        	if($value['seckill_state']==5){
        		$data['items'][$key]['seckill_states'] = '已撤销';
        	}
        }

		if('json' == $this->typ)
		{
			$json_data['data']       = $data;
			$json_data['shop_type']  = $shop_type;
			$json_data['combo_flag'] = $this->combo_flag;
			$json_data['combo_row']  = $combo_row;

			$this->data->addBody(-140, $json_data);
		}
		else
		{
			
			include $this->view->getView();
		}	
    }
    public function add()
	{	
        $data      = array();
		$combo     = array();
		$shop_type = $this->self_support_flag;

		if (!$this->self_support_flag)  //普通店铺
		{

			if (!$this->combo_flag)
			{
				location_to(Yf_Registry::get('url') . '?ctl=Seller_Promotion_Seckill&met=combo&typ=e');
			}
			else
			{
				$combo = $this->seckillQuotaModel->getSeckillQuotaByShopID(Perm::$shopId);
			}
		}
		else // 自营店铺
		{
			$combo['combo_end_time'] = date("Y-m-d H:i:s", strtotime("11 june 2030"));
		}

		if (request_string('op') == 'edit')
		{
			$cond_row['seckill_id'] = request_int('id');
			$cond_row['shop_id']     = Perm::$shopId;
			$data                    = $this->seckillBaseModel->getSeckillActInfo($cond_row);

			$this->view->setMet('edit');
		}

		if('json' == $this->typ)
		{
			$json_data['data']		= $data;
			$json_data['shop_type']	= $shop_type;	//店铺类型
			$json_data['combo']		= $combo; 		//套餐信息

			$this->data->addBody(-140, $json_data);
		}
		else
		{
			include $this->view->getView();
		}
    }
    public function package()
	{	

      include $this->view->getView();
    }

    public function combo(){
    	if ($this->self_support_flag)   //免费发布活动
		{
            location_go_back(__('自营店铺或者套餐续费， 不需要设置。'));
			//location_to('index.php?ctl=Seller_Promotion_Discount&met=add&typ=e');
		}

        //查找出店铺团购活动的消费记录
        $Yf_Page           = new Yf_Page();
        $Yf_Page->listRows = request_int('listRows')?request_int('listRows'):10;
        $rows              = $Yf_Page->listRows;
        $offset            = request_int('firstRow', 0);
        $page              = ceil_r($offset / $rows);

        $cond_row = array();
        $order_row = array();
        $cond_row['shop_id'] = Perm::$shopId;
        $cond_row['activity_type'] = Shop_CostModel::SECKILL;
        $data = $this->shopCostModel->listByWhere($cond_row,$order_row,$page, $rows);

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav           = $Yf_Page->prompt();

		if('json' == $this->typ)
		{
			//购买活动套餐每个月需支付的金额
			$data['promotion_seckill_price'] = Web_ConfigModel::value('promotion_seckill_price');
			$this->data->addBody(-140, $data);
		}
		else
		{
			include $this->view->getView();
		}
    }


    public function addCombo()
	{
		$data        = array();
		$combo_row   = array();
		$rs_row      = array();
		$month_price = Web_ConfigModel::value('promotion_seckill_price');
		$month       = request_int('month');
		$days        = 30 * $month;

		if($month > 0)
		{
			$this->seckillQuotaModel->sql->startTransactionDb();

			$field_row['user_id']     = Perm::$row['user_id'];
			$field_row['shop_id']     = Perm::$shopId;
			$field_row['cost_price']  = $month_price * $month;
			$field_row['cost_desc']   = __('店铺购买秒杀活动消费');
			$field_row['cost_status'] = 0;
			$field_row['cost_time']   = get_date_time();
            $field_row['activity_type']   = Shop_CostModel::SECKILL;
            $field_row['activity_price']   = $month_price;
            $field_row['activity_month']   = $month;
			$flag                     = $this->shopCostModel->addCost($field_row, true);
			check_rs($flag, $rs_row);

			if ($flag)
			{
				$combo_row = $this->seckillQuotaModel->getSeckillQuotaByShopID(Perm::$shopId);
				//记录已经存在，套餐续费
				if ($combo_row)
				{
					//1、原套餐已经过期,更新套餐开始时间和结束时间
					if (strtotime($combo_row['combo_end_time']) < time())
					{
						$field['combo_start_time'] = get_date_time();
						$field['combo_end_time']   = date('Y-m-d H:i:s', strtotime("+$days days"));
					}
					elseif ((time() >= strtotime($combo_row['combo_start_time'])) && (time() <= strtotime($combo_row['combo_end_time'])))
					{
						//2、原套餐尚未过期，只需更新结束时间
						$field['combo_end_time'] = date('Y-m-d H:i:s', strtotime("+$days days", strtotime($combo_row['combo_end_time'])));
					}
					$op_flag = $this->seckillQuotaModel->renewSeckillCombo($combo_row['combo_id'], $field);
				}
				else            //记录不存在，添加套餐
				{
					$shop_row = $this->shopBaseModel->getBaseOneList(array('shop_id' => Perm::$shopId));

					$field['combo_start_time'] = get_date_time();
					$field['combo_end_time']   = date('Y-m-d H:i:s', strtotime("+$days days"));
					$field['shop_id']          = Perm::$shopId;
					$field['shop_name']        = $shop_row['shop_name'];
					$field['user_id']          = Perm::$userId;
					$field['user_nickname']    = Perm::$row['user_account'];
					$op_flag                   = $this->seckillQuotaModel->addSeckillCombo($field, true);
				}
				check_rs($op_flag, $rs_row);
			}

            if(is_ok($rs_row))
            {
                //在paycenter中添加交易记录
                $key      = Yf_Registry::get('shop_api_key');
                $url         = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');

                $formvars             = array();
                $formvars['app_id']        = $shop_app_id;
                $formvars['buyer_user_id'] = Perm::$userId;
                $formvars['buyer_user_name'] = Perm::$row['user_account'];
                $formvars['amount'] = $month_price * $month;

                $rs                   = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addCombo&typ=json', $url), $formvars);
            }
			if (is_ok($rs_row) && isset($rs) && $rs['status'] == 200 && $this->seckillQuotaModel->sql->commitDb())
			{
				$msg    = __('操作成功！');
				$status = 200;
			}
			else
			{
				$this->seckillQuotaModel->sql->rollBackDb();
				$msg    = __('操作失败！');
				$status = 250;
			}
		}
		else
		{
			$msg    = __('购买月份必须为正整数！');
			$status = 250;
		}

		$this->data->addBody(-140, $data, $msg, $status);
	}

	/**添加秒杀活动
     * 注意：同一个秒杀活动可以有多个商品参加
     * 商品的购买下限设置只是针对每个商品而言
     * 参加同一活动的多个商品数量不可累加作为满足最低购买数量的限定标准
     * 后期如需调整规则可在此基础上进行修改
     */
	public function addSeckill()
	{
		if (!$this->combo_flag)
		{
			$flag = false;
		}
		else
		{
            $check_post_data = true;

            $field_row                         = array();
            $field_row['seckill_name']        = request_string('seckill_name');
            if(empty( $field_row['seckill_name']))
            {
                $check_post_data = false;
                $msg_label = __('活动名称不能为空！');
            }
          
            $field_row['seckill_start_time']  = request_string('seckill_start_time');
            $field_row['seckill_end_time']    = request_string('seckill_end_time');

            if (empty( $field_row['seckill_end_time']))
            {
                $check_post_data = false;
                $msg_label = __('活动结束时间不能为空！');
            }

            $field_row['seckill_lower_limit'] = request_int('seckill_lower_limit');

            if ($field_row['seckill_lower_limit'] <= 0)
            {
                $check_post_data = false;
                $msg_label = __('活动商品购买下限必须为正整数！');
            }

            if (!$this->self_support_flag)
            {
                $combo                        = $this->seckillQuotaModel->getSeckillQuotaByShopID(Perm::$shopId);
                $field_row['combo_id']       = $combo['combo_id'];
            }

            $field_row['user_id']        = Perm::$userId;
            $field_row['user_nick_name'] = Perm::$row['user_account'];
            $field_row['shop_id']        = Perm::$shopId;
            $field_row['shop_name']      = $this->shop_info['shop_name'];
            $field_row['seckill_state'] = 0;
            $field_row['seckill_time_slot'] = request_int('seckill_time_slot');
			//$field_row['order_cancel_time'] = request_int('order_cancel_time');
			$field_row['apply_time'] = date('Y-m-d H:i:s');

			if($field_row['seckill_time_slot']==1){
				$field_row['day_start_time'] = 0;
				$field_row['day_end_time'] = 2;
			}
			if($field_row['seckill_time_slot']==2){
				$field_row['day_start_time'] = 8;
				$field_row['day_end_time'] = 10;
			}
			if($field_row['seckill_time_slot']==3){
				$field_row['day_start_time'] = 10;
				$field_row['day_end_time'] = 12;
			}
			if($field_row['seckill_time_slot']==4){
				$field_row['day_start_time'] = 12;
				$field_row['day_end_time'] = 14;
			}
			if($field_row['seckill_time_slot']==5){
				$field_row['day_start_time'] = 14;
				$field_row['day_end_time'] = 16;
			}
			if($field_row['seckill_time_slot']==6){
				$field_row['day_start_time'] = 16;
				$field_row['day_end_time'] = 18;
			}
			if($field_row['seckill_time_slot']==7){
				$field_row['day_start_time'] = 18;
				$field_row['day_end_time'] = 20;
			}
			if($field_row['seckill_time_slot']==8){
				$field_row['day_start_time'] = 20;
				$field_row['day_end_time'] = 22;
			}
			if($field_row['seckill_time_slot']==9){
				$field_row['day_start_time'] = 22;
				$field_row['day_end_time'] = 0;
			}

			if(request_string('is_limit')){
				$field_row['is_limit'] = 1;
			}else{
				$field_row['is_limit'] = 0;
			}
			
			$field_row['seckill_start_date'] = date('Y-m-d',strtotime($field_row['seckill_start_time']));
			$field_row['seckill_end_date'] = date('Y-m-d',strtotime($field_row['seckill_end_time']));
            if ($check_post_data)
            {
                $flag = $seckill_id = $this->seckillBaseModel->addSeckillActivity($field_row, true);
            }
            else
            {
                $flag = false;
            }

		}

		if ($flag)
		{
			$msg    = __('添加成功!');
			$status = 200;
		}
		else
		{
			$msg    = $msg_label?$msg_label:__('添加失败！');
			$status = 250;
		}
		$data['seckill_id'] = $seckill_id;
		
		$this->data->addBody(-140, $data, $msg, $status);
	}

	/*
	 *删除活动
	 *删除活动下的商品
	 *此处需要开启事务
	*/
	public function removeSeckillAct()
	{
		$seckill_ids = request_row('id');

        if (is_array($seckill_ids)) {
            foreach ($seckill_ids as $seckill_id){
                $check_right = $this->seckillBaseModel->getOne($seckill_id);
            }
        }

		if ($check_right['shop_id'] == Perm::$shopId)
		{
			$this->seckillBaseModel->sql->startTransactionDb(); //开启事务

			$flag = $this->seckillBaseModel->removeSeckillActItem($seckill_id);

			if ($flag && $this->seckillBaseModel->sql->commitDb())
			{
				$msg    = __('删除成功！');
				$status = 200;
			}
			else
			{
				$this->seckillBaseModel->sql->rollBackDb();
				$msg    = __('删除失败！');
				$status = 250;
			}
		}
		else
		{
			$msg    = __('删除失败！');
			$status = 250;
		}

		$data['seckill_id'] = $seckill_id;

		$this->data->addBody(-140, $data, $msg, $status);
	}


	//编辑活动
	public function editSeckill()
	{

		if (!$this->combo_flag)
		{
			$flag = false;
		}
		else
		{
            $seckill_id = request_int('id');
            $check_right = $this->seckillBaseModel->getOne($seckill_id);

            if ($check_right['shop_id'] == Perm::$shopId)
            {
                $check_post_data = true;

                $field_row['seckill_name']            = request_string('seckill_name');

                $field_row['seckill_start_time']      = request_string('seckill_start_time');

                $field_row['seckill_end_time']        = request_string('seckill_end_time');

                $field_row['seckill_time_slot']       = request_string('seckill_time_slot');

                if($field_row['seckill_time_slot']==1){
					$field_row['day_start_time'] = 0;
					$field_row['day_end_time'] = 2;
				}
				if($field_row['seckill_time_slot']==2){
					$field_row['day_start_time'] = 8;
					$field_row['day_end_time'] = 10;
				}
				if($field_row['seckill_time_slot']==3){
					$field_row['day_start_time'] = 10;
					$field_row['day_end_time'] = 12;
				}
				if($field_row['seckill_time_slot']==4){
					$field_row['day_start_time'] = 12;
					$field_row['day_end_time'] = 14;
				}
				if($field_row['seckill_time_slot']==5){
					$field_row['day_start_time'] = 14;
					$field_row['day_end_time'] = 16;
				}
				if($field_row['seckill_time_slot']==6){
					$field_row['day_start_time'] = 16;
					$field_row['day_end_time'] = 18;
				}
				if($field_row['seckill_time_slot']==7){
					$field_row['day_start_time'] = 18;
					$field_row['day_end_time'] = 20;
				}
				if($field_row['seckill_time_slot']==8){
					$field_row['day_start_time'] = 20;
					$field_row['day_end_time'] = 22;
				}
				if($field_row['seckill_time_slot']==9){
					$field_row['day_start_time'] = 22;
					$field_row['day_end_time'] = 0;
				}
				if(request_string('is_limit')){
					$field_row['is_limit'] = 1;
				}else{
					$field_row['is_limit'] = 0;
				}
              
              	$field_row['seckill_start_date'] = date('Y-m-d',strtotime($field_row['seckill_start_time']));
				$field_row['seckill_end_date'] = date('Y-m-d',strtotime($field_row['seckill_end_time']));
				$field_row['seckill_state'] = 0;


                if (empty($field_row['seckill_name']))
                {
                    $check_post_data = false;
                    $msg_label = __('活动名称不能为空！');
                }

              
                $field_row['seckill_lower_limit'] = request_int('seckill_lower_limit');

                if ($field_row['seckill_lower_limit'] <= 0)
                {
                    $check_post_data = false;
                    $msg_label = __('活动商品购买下限必须为正整数！');
                }

                if ($check_post_data)
                {
                    $this->seckillBaseModel->editSeckillActInfo($seckill_id, $field_row);
                    $seckillgoods_list = $this->seckillGoodsModel->getByWhere(['seckill_id'=>$seckill_id]);
                    
                    foreach($seckillgoods_list as $v){
                        $edit_goods_row = array();
                        $edit_goods_row['seckill_name']        =  request_string('seckill_name');
                        $edit_goods_row['goods_start_time']    =  request_string('seckill_start_time');
                        $edit_goods_row['goods_end_time']      =  request_string('seckill_end_time');
                        $edit_goods_row['goods_time_slot']     =  request_int('seckill_time_slot');
                        $edit_goods_row['day_start_time']      =  $field_row['day_start_time'];
                        $edit_goods_row['day_end_time']        =  $field_row['day_end_time'];
                        $edit_goods_row['goods_start_date']    =  $field_row['seckill_start_date'];
                        $edit_goods_row['goods_end_date']      =  $field_row['seckill_end_date'];
                        $edit_goods_row['goods_lower_limit'] =  request_int('seckill_lower_limit');
	                  	$edit_goods_row['is_limit'] =  $field_row['is_limit'];
	                    $this->seckillGoodsModel->editSeckillGoods($v['seckill_goods_id'],$edit_goods_row);
	                   
                    }
                    

                    $flag = true;
                }
                else
                {
                    $flag = false;
                }

            }
            else
            {
                $flag = false;
            }
		}
		
		if ($flag)
		{
			$msg    = __('编辑成功！');
			$status = 200;
		}
		else
		{
			$msg    = $msg_label?$msg_label:__('编辑失败！');
			$status = 250;
		}
		$data['discount_id'] = $discount_id;
		
		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function getShopGoods()
	{
		$cond_row = array();

		//分页
		$Yf_Page           = new Yf_Page();
		$Yf_Page->listRows = request_int('listRows')?request_int('listRows'):12;
		$rows              = $Yf_Page->listRows;
		$offset            = request_int('firstRow', 0);
		$page              = ceil_r($offset / $rows);

		$cond_row['shop_id'] = Perm::$shopId;

		$goods_name = request_string('goods_name');
		if ($goods_name)
		{
			$cond_row['common_name:LIKE'] = "%".$goods_name . "%";
		}
        $Goods_CommonModel = new Goods_CommonModel();
        //虚拟商品只参加团购活动
        $cond_row['common_is_virtual'] = Goods_CommonModel::GOODS_NORMAL;
        //获取一键代发的商品
        $shop_id = Perm::$shopId;
        $supplier_end_list = $Goods_CommonModel->getSupplierSendCommonByShopId($shop_id);
        $supplier_end_common_id = is_array($supplier_end_list) ? array_column($supplier_end_list, 'common_id') : array();
        //获取plus商品集合 @nsy 2019-04-02
        $goodsPlusGoodsModel = new Plus_GoodsModel();
        $plus_common_ids = $goodsPlusGoodsModel->getSellerShopPlusGoodsList();
        !$plus_common_ids && $plus_common_ids = array();
        //所有礼包商品
        $gift_common_id = $Goods_CommonModel->getGiftList();
        if ($supplier_end_common_id || $plus_common_ids || $gift_common_id) {
            $list_ids = array_merge($plus_common_ids, $supplier_end_common_id, $gift_common_id);
            //$cond_row['common_id:not in'] = $supplier_end_common_id;
            $cond_row['common_id:not in'] = $list_ids;
        }
       /* if($supplier_end_common_id){
            $cond_row['common_id:not in'] = $supplier_end_common_id;
        }*/
		$data = $Goods_CommonModel->getNormalSateGoodsBase($cond_row, array('goods_id' => 'DESC'), $page, $rows);

        if($data['items']){
            //如果商品参加活动标记
            $goods_ids = array();
            foreach ($data['items'] as $value){
                $goods_ids[] = $value['goods_ids']; 
            }
            $GroupBuy_BaseModel = new GroupBuy_BaseModel();
            //参加团购、限时折扣的商品
            $check_goods_ids = $GroupBuy_BaseModel->getAllActivityGoodsId($goods_ids);

            //参加砍价的商品
            $Bargain_BaseModel = new Bargain_BaseModel();
            $bargain_goods_ids = $Bargain_BaseModel->getBargainGoodsIds();

            $check_goods_ids = array_merge($check_goods_ids, $bargain_goods_ids);
        
            foreach ($data['items'] as $key=>$val){
                if(in_array($val['goods_id'], $check_goods_ids))
                {
                    $data['items'][$key]['is_join'] = 'true';
                }
            }
        }
        
		$Yf_Page->totalRows = $data['totalsize'];
		$page_nav           = $Yf_Page->prompt();
		if('json' == $this->typ)
		{
			$this->data->addBody(-140, $data);
		}
		else
		{
			include $this->view->getView();
		}
	}


	//添加活动商品
	public function addSeckillGoods()
	{
		if (!$this->combo_flag)
		{
			$msg_label    = __('您尚未购买套餐或套餐已到期！');
			$flag   = false;
		}
		else
		{
            $seckill_id                                         = request_int('seckill_id');
            $cond_row_seckill_base['shop_id']                   = Perm::$shopId;
            $cond_row_seckill_base['seckill_id']               = $seckill_id;
            $cond_row_seckill_base['seckill_state']            = Seckill_BaseModel::NORMAL;
            $cond_row_seckill_base['seckill_end_time:>=']      = get_date_time();
			$seckill_base_row = $this->seckillBaseModel->getSeckillActInfo($cond_row_seckill_base);

            fb($cond_row_seckill_base);
            fb($seckill_base_row);
            if (empty($seckill_base_row))
            {
                $msg_label    = __('活动不存在或状态不可用！');
                $flag   = false;
            }
            else
            {
                //检查商品是否已经加入过同时段的活动
                $seckill_goods_rows1              = array();
                $seckill_goods_rows2              = array();
                $cond_row1                         = array();
                $cond_row1['goods_id']             = request_int('goods_id');
                $cond_row1['seckill_goods_state']  = Seckill_goodsModel::NORMAL;
                $cond_row1['goods_start_time:<=']  = $seckill_base_row['seckill_start_time'];
                $cond_row1['goods_end_time:>=']    = $seckill_base_row['seckill_start_time'];
                $seckill_goods_rows1              = $this->seckillGoodsModel->getSeckillGoodsByWhere($cond_row1);

                $cond_row2                         = array();
                $cond_row2['goods_id']             = request_int('goods_id');
                $cond_row2['seckill_goods_state']  = Seckill_goodsModel::NORMAL;
                $cond_row2['goods_start_time:<=']  = $seckill_base_row['seckill_end_time'];
                $cond_row2['goods_end_time:>=']    = $seckill_base_row['seckill_end_time'];
                $seckill_goods_rows2              = $this->seckillGoodsModel->getSeckillGoodsByWhere($cond_row2);

                if (!empty($seckill_goods_rows1) || !empty($seckill_goods_rows2))
                {
                    $msg_label    = __('该商品已参加过同时段的活动！');
                    $flag         = false;
                }
                else
                {
                    $check_post_data = true;
                    $field_row['seckill_price'] = request_float('seckill_price');  //商品折扣价
                    $field_row['goods_id']       =  request_int('goods_id');          //商品goods_id
                    $field_row['seckill_stock']            = request_int('seckill_stock');
                    $field_row['seckill_stock_s']          = request_int('seckill_stock');
               		$field_row['goods_stock']              = request_int('goods_stock');
               		$field_row['goods_time_slot']          = request_int('seckill_time_slot');

                    $cond_row_goods_base['goods_id'] = $field_row['goods_id'];
                    $cond_row_goods_base['shop_id']  = Perm::$shopId;
                    $goodsBaseModel = new Goods_BaseModel();
                    $goods_base_row = $goodsBaseModel->getOneByWhere($cond_row_goods_base);

                    if (!empty($goods_base_row))
                    {
                        $field_row['goods_price']    = $goods_base_row['goods_price']; //商品原价
                        $field_row['common_id']      = $goods_base_row['common_id'];    //商品common_id
                        $field_row['goods_name']     = $goods_base_row['goods_name'];  //商品名
                        $field_row['cat_id'] = $goods_base_row['cat_id'];
                        if ($field_row['seckill_price'] <= 0)
                        {
                            $check_post_data = false;
                            $msg_label = __('请填写商品的秒杀价格！');
                        }
                        else
                        {
                            if ($field_row['seckill_price'] >= $field_row['goods_price'])
                            {
                                $check_post_data = false;
                                $msg_label = __('秒杀价格必须小于商品价格！');
                            }
                        }

                        if ($field_row['seckill_stock'] <= 0)
                        {
                            $check_post_data = false;
                            $msg_label = __('请填写商品的秒杀库存！');
                        }
                        else
                        {
                            if ($field_row['seckill_stock'] >= $field_row['goods_stock'])
                            {
                                $check_post_data = false;
                                $msg_label = __('秒杀库存必须小于商品库存！');
                            }
                        }
                    }
                    else
                    {
                        $check_post_data = false;
                        $msg_label = __('请选择参加活动的商品！');
                    }

                    $field_row['seckill_id']               = $seckill_id;
                    $field_row['shop_id']                  = Perm::$shopId;
                    $field_row['seckill_name']             = $seckill_base_row['seckill_name'];
                   
                    $field_row['goods_start_time']         = $seckill_base_row['seckill_start_time'];
                    $field_row['goods_end_time']           = $seckill_base_row['seckill_end_time'];
                    $field_row['goods_lower_limit']        = $seckill_base_row['seckill_lower_limit'];
                    $field_row['seckill_goods_state']      = Seckill_GoodsModel::NORMAL;
                    $field_row['day_start_time']           = $seckill_base_row['day_start_time'];
                    $field_row['day_end_time']             = $seckill_base_row['day_end_time'];
                    $field_row['goods_start_date']         = $seckill_base_row['seckill_start_date'];
                    $field_row['goods_end_date']           = $seckill_base_row['seckill_end_date'];
                    $field_row['is_limit']                 = $seckill_base_row['is_limit'];
                    if ($check_post_data)
                    {
                        $rs_row = array();

                        $this->seckillGoodsModel->sql->startTransactionDb();

                        $insert_flag = $this->seckillGoodsModel->addSeckillGoods($field_row, true);
                        check_rs($insert_flag, $rs_row);

                        $Goods_CommonModel = new Goods_CommonModel();
                        $update_flag       = $Goods_CommonModel->editCommon(request_int('common_id'), array('common_is_miao' => 1));
                        check_rs($update_flag, $rs_row);

                        if (is_ok($rs_row) && $this->seckillGoodsModel->sql->commitDb())
                        {
                        	$redis = new Redis();

					        $redis->connect('127.0.0.1',6379);

					        $password = '123456';

					        $redis->auth($password);

					        for ($i=1; $i <= $field_row['seckill_stock'] ; $i++) { 
					        	$redis->rpush("seckill".$insert_flag,$i);
					        }
					       
					       

                            $flag = true;
                        }
                        else
                        {
                            $this->seckillGoodsModel->sql->rollBackDb();
                            $flag = false;
                        }
                    }
                    else
                    {
                        $flag = false;
                    }
                }
            }
		}

        if ($flag)
        {
            $msg    = __('添加成功！');
            $status = 200;
        }
        else
        {
            $msg    = $msg_label?$msg_label:__('商品添加失败！');
            $status = 250;
        }

		$data['seckill_goods_id'] = $insert_flag?$insert_flag:0;

		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function removeSeckillGoods()
	{
		$seckill_goods_ids = request_row('id');
        if (is_array($seckill_goods_ids)) {
            foreach ($seckill_goods_ids as $seckill_goods_id){
                $check_right       = $this->seckillGoodsModel->getOne($seckill_goods_id);
            }
        }
		if ($check_right['shop_id'] == Perm::$shopId)
		{
			$this->seckillGoodsModel->sql->startTransactionDb(); //开启事务

			$flag = $this->seckillGoodsModel->removeSeckillGoods($seckill_goods_id);

			if ($flag && $this->seckillGoodsModel->sql->commitDb())
			{
				$msg    = __('删除成功');
				$status = 200;
			}
			else
			{
				$this->seckillGoodsModel->sql->rollBackDb();
				$msg    = __('删除失败');
				$status = 250;
			}
		}
		else
		{
			$msg    = __('删除失败');
			$status = 250;
		}

		$data['seckill_goods_id'] = $seckill_goods_id;

		$this->data->addBody(-140, $data, $msg, $status);
	}

	//编辑秒杀商品价格、库存
	public function editSeckillGoodsPrice()
	{
		$data = array();

        if ($this->combo_flag)
        {
			$seckill_goods_id           = request_int('seckill_goods_id');
            $field_row['seckill_price'] = request_float('seckill_price');
            $field_row['seckill_stock'] = request_int('seckill_stock');

            $cond_row_seckill_goods['seckill_goods_id']   = $seckill_goods_id;
            $seckill_goods_row = $this->seckillGoodsModel->getSeckillGoodsDetailByWhere($cond_row_seckill_goods);
            
            if ($seckill_goods_row)
            {
                $check_post_data = true;

                if ($field_row['seckill_price'] <= 0)
                {
                    $check_post_data = false;
                    $msg_label = __('请输入商品秒杀价格！');
                }
                else
                {
                    if ($field_row['seckill_price'] >= $seckill_goods_row['goods_price'])
                    {
                        $check_post_data = false;
                        $msg_label = __('秒杀价格必须低于商品价格！');
                    }
                }

                if ($field_row['seckill_stock'] <= 0)
                {
                    $check_post_data = false;
                    $msg_label = __('请输入商品秒杀库存！');
                }
                else
                {
                    if ($field_row['seckill_stock'] >= $seckill_goods_row['goods_stock'])
                    {
                        $check_post_data = false;
                        $msg_label = __('秒杀库存必须低于商品库存！');
                    }
                }

                if ($check_post_data)
                {
                    $this->seckillGoodsModel->editSeckillGoods($seckill_goods_id, $field_row);
                    $flag                      = true;
                    $data                      = $field_row;
                    $data['seckill_goods_id']  = $seckill_goods_id;
                }
                else
                {
                    $flag = false;
                }
            }
            else
            {
               $flag = false;
            }
		}
        else
        {
            $flag = false;
            $msg_label = __('套餐不可用！');
        }

		if ($flag)
		{
			$msg    = __('操作成功！');
			$status = 200;
		}
		else
		{
			$msg    = $msg_label?$msg_label:__('操作失败！');
			$status = 250;
		}

		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function revoke()
	{
		$seckill_ids = request_row('id');

        if (is_array($seckill_ids)) {
            foreach ($seckill_ids as $seckill_id){
                $check_right = $this->seckillBaseModel->getOne($seckill_id);
            }
        }

		if ($check_right['shop_id'] == Perm::$shopId)
		{
			$this->seckillBaseModel->sql->startTransactionDb(); //开启事务

			$flag = $this->seckillBaseModel->editSeckillActInfo($seckill_id,array('seckill_state'=>5));

			if ($flag && $this->seckillBaseModel->sql->commitDb())
			{
				$msg    = __('撤销成功！');
				$status = 200;
			}
			else
			{
				$this->seckillBaseModel->sql->rollBackDb();
				$msg    = __('撤销失败！');
				$status = 250;
			}
		}
		else
		{
			$msg    = __('撤销失败！');
			$status = 250;
		}

		$data['seckill_id'] = $seckill_id;

		$this->data->addBody(-140, $data, $msg, $status);
	}
}

?>