<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Seller_Promotion_PresaleCtl extends Seller_Controller
{
	public $presaleBaseModel  = null;
	public $presaleGoodsModel = null;
	public $presaleQuotaModel = null;
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

		

		$this->presaleBaseModel  = new Presale_BaseModel();
		$this->presaleGoodsModel = new Presale_GoodsModel();
		$this->presaleQuotaModel = new Presale_QuotaModel();
		$this->goodsBaseModel     = new Goods_BaseModel();
		$this->shopCostModel      = new Shop_CostModel();
		$this->shopBaseModel      = new Shop_BaseModel();

		$this->shop_info         = $this->shopBaseModel->getOne(Perm::$shopId);//店铺信息
		$this->self_support_flag = ($this->shop_info['shop_self_support'] == "true" || Web_ConfigModel::value('promotion_presale_price') == 0) ? true : false; //是否为自营店铺标志
		if ($this->self_support_flag) //平台店铺，没有套餐限制
		{
			$this->combo_flag = true;
		}
		else
		{
			$this->combo_flag = $this->presaleQuotaModel->checkQuotaStateByShopId(Perm::$shopId);//普通店铺需要查询套餐状态
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
            $presale_id = request_int('id');

            if ($presale_id)
            {
                $cond_row['presale_id']     = $presale_id;
                $cond_row['shop_id']         = Perm::$shopId;
                $data['presale_detail']     = $this->presaleBaseModel->getPresaleActInfo($cond_row);
                $data['presale_goods_rows'] = $this->presaleGoodsModel->getPresaleGoods($cond_row, array('presale_goods_id' => 'DESC'));
            }
            else
            {
                location_go_back('活动不存在');
            }

            $this->view->setMet('manage');
        }else if(request_string('op') == 'detail')
        {
            $presale_id = request_int('id'); 

            if ($presale_id)
            {
               
                $data     = $this->presaleBaseModel->getOne($presale_id);
                
                
           
            }
            else
            {
                location_go_back('活动不存在');
            }

            $this->view->setMet('detail');
        }
        else if(request_string('op') == 'edit')
        {
        	$cond_row['presale_id'] = request_int('id');
			$cond_row['shop_id']     = Perm::$shopId;
			$data                    = $this->presaleBaseModel->getPresaleActInfo($cond_row);

			
	        if (!$this->self_support_flag)  //普通店铺
			{
				$combo = $this->presaleQuotaModel->getPresaleQuotaByShopID(Perm::$shopId);
			}
			else // 自营店铺
			{
				$combo['combo_end_time'] = date("Y-m-d H:i:s", strtotime("11 june 2030"));
			}
		  
			$this->view->setMet('edit');
            
        }
        
        else{
        	$Yf_Page           = new Yf_Page();
            $Yf_Page->listRows = request_int('listRows')?request_int('listRows'):10;
            $rows              = $Yf_Page->listRows;
            $offset            = request_int('firstRow', 0);
            $page              = ceil_r($offset / $rows);

            $cond_row['shop_id'] = Perm::$shopId;         //店铺ID

            if (request_string('keyword')) {
                $cond_row['presale_name:LIKE'] = request_string('keyword') . '%';
            }
            if(request_int('state')){
            	if(request_int('state')==4){
            		$cond_row['presale_state'] = 0;
            	}else{
            		$cond_row['presale_state'] = request_int('state');
            	}
            	
            }

            $data               = $this->presaleBaseModel->getPresaleActList($cond_row, array('presale_id' => 'DESC'), $page, $rows);
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav           = $Yf_Page->prompt();
        
        }
      
		include $this->view->getView();	
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
				location_to(Yf_Registry::get('url') . '?ctl=Seller_Promotion_Presale&met=combo&typ=e');
			}
			else
			{
				$combo = $this->presaleQuotaModel->getPresaleQuotaByShopID(Perm::$shopId);
			}
		}
		else // 自营店铺
		{
			$combo['combo_end_time'] = date("Y-m-d H:i:s", strtotime("11 june 2030"));
		}

		if (request_string('op') == 'edit')
		{
			$cond_row['presale_id'] = request_int('id');
			$cond_row['shop_id']     = Perm::$shopId;
			$data                    = $this->presaleBaseModel->getPresaleActInfo($cond_row);

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
        $cond_row['activity_type'] = Shop_CostModel::PRESALE;
        $data = $this->shopCostModel->listByWhere($cond_row,$order_row,$page, $rows);

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav           = $Yf_Page->prompt();

		if('json' == $this->typ)
		{
			//购买活动套餐每个月需支付的金额
			$data['promotion_presale_price'] = Web_ConfigModel::value('promotion_presale_price');
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
		$month_price = Web_ConfigModel::value('promotion_presale_price');
		$month       = request_int('month');
		$days        = 30 * $month;

		if($month > 0)
		{
			$this->presaleQuotaModel->sql->startTransactionDb();

			$field_row['user_id']     = Perm::$row['user_id'];
			$field_row['shop_id']     = Perm::$shopId;
			$field_row['cost_price']  = $month_price * $month;
			$field_row['cost_desc']   = __('店铺购买预售活动消费');
			$field_row['cost_status'] = 0;
			$field_row['cost_time']   = get_date_time();
            $field_row['activity_type']   = Shop_CostModel::PRESALE;
            $field_row['activity_price']   = $month_price;
            $field_row['activity_month']   = $month;
			$flag                     = $this->shopCostModel->addCost($field_row, true);
			check_rs($flag, $rs_row);

			if ($flag)
			{
				$combo_row = $this->presaleQuotaModel->getPresaleQuotaByShopID(Perm::$shopId);
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
					$op_flag = $this->presaleQuotaModel->renewPresaleCombo($combo_row['combo_id'], $field);
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
					$op_flag                   = $this->presaleQuotaModel->addPresaleCombo($field, true);
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
           
			if (is_ok($rs_row) && isset($rs) && $rs['status'] == 200 && $this->presaleQuotaModel->sql->commitDb())
			{
				$msg    = __('操作成功！');
				$status = 200;
			}
			else
			{
				$this->presaleQuotaModel->sql->rollBackDb();
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
	
	public function addPresale()
	{
		if (!$this->combo_flag)
		{
			$flag = false;
		}
		else
		{
            $check_post_data = true;

            $field_row                         = array();
            $field_row['presale_name']        = request_string('presale_name');
            if(empty( $field_row['presale_name']))
            {
                $check_post_data = false;
                $msg_label = __('活动名称不能为空！');
            }
          
            $field_row['presale_start_time']  = request_string('presale_start_time');
            $field_row['presale_end_time']    = request_string('presale_end_time');
			
            if (empty( $field_row['presale_end_time']))
            {
                $check_post_data = false;
                $msg_label = __('活动结束时间不能为空！');
            }

            $field_row['presale_lower_limit'] = request_int('presale_lower_limit');

            // if ($field_row['presale_lower_limit'] <= 0)
            // {
            //     $check_post_data = false;
            //     $msg_label = __('活动商品购买下限必须为正整数！');
            // }

            if (!$this->self_support_flag)
            {
                $combo                        = $this->presaleQuotaModel->getPresaleQuotaByShopID(Perm::$shopId);
                $field_row['combo_id']        = $combo['combo_id'];
            }

            $field_row['user_id']         = Perm::$userId;
            $field_row['user_nick_name']  = Perm::$row['user_account'];
            $field_row['shop_id']         = Perm::$shopId;
            $field_row['shop_name']       = $this->shop_info['shop_name'];
            $field_row['presale_state']   = 0;
			$field_row['apply_time']      = date('Y-m-d H:i:s');
			$field_row['presale_deposit'] = request_row('presale_deposit');
			$field_row['presale_final_time'] = request_row('presale_final_time');
			$field_row['presale_final_time_end'] = date('Y-m-d H:i:s',strtotime($field_row['presale_final_time'].'+3 days'));

			//var_dump($field_row);die;
            if ($check_post_data)
            {
                $flag = $presale_id = $this->presaleBaseModel->addPresaleActivity($field_row, true);
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
		$data['presale_id'] = $presale_id;
		
		$this->data->addBody(-140, $data, $msg, $status);
	}
	
	//编辑活动
	public function editPresale()
	{

		if (!$this->combo_flag)
		{
			$flag = false;
		}
		else
		{
            $presale_id = request_int('id');
            $check_right = $this->presaleBaseModel->getOne($presale_id);

            if ($check_right['shop_id'] == Perm::$shopId)
            {
                $check_post_data = true;

                $field_row['presale_name']            = request_string('presale_name');

                $field_row['presale_start_time']      = request_string('presale_start_time');

                $field_row['presale_end_time']        = request_string('presale_end_time');

                $field_row['presale_final_time']      = request_string('presale_final_time');
				
				$field_row['presale_deposit']         = request_string('presale_deposit');
				
				$field_row['presale_lower_limit']     = request_string('presale_lower_limit');
            	
            	$field_row['presale_final_time_end']  = date('Y-m-d H:i:s',strtotime($field_row['presale_final_time'].'+3 days'));
    		
                if (empty($field_row['presale_name']))
                {
                    $check_post_data = false;
                    $msg_label = __('活动名称不能为空！');
                }

              
                $field_row['presale_lower_limit'] = request_int('presale_lower_limit');


                if ($check_post_data)
                {
                    $this->presaleBaseModel->editPresaleActInfo($presale_id, $field_row);
                    $presalegoods_list = $this->presaleGoodsModel->getByWhere(['presale_id'=>$presale_id]);
                    
                    foreach($presalegoods_list as $v){
                        $edit_goods_row = array();
                        $edit_goods_row['presale_name']        =  request_string('presale_name');
                        $edit_goods_row['goods_start_time']    =  request_string('presale_start_time');
                        $edit_goods_row['goods_end_time']      =  request_string('presale_end_time');
                        $edit_goods_row['goods_lower_limit']   =  request_int('presale_lower_limit');
                        $edit_goods_row['presale_deposit']     =  request_string('presale_deposit');
                        $edit_goods_row['goods_final_time']    =  request_string('presale_final_time');
                        $edit_goods_row['goods_final_time_end'] = date('Y-m-d H:i:s',strtotime($edit_goods_row['goods_final_time'].'+3 days'));
	                    $this->presaleGoodsModel->editPresaleGoods($v['presale_goods_id'],$edit_goods_row);
	                   
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
	
	
	/*
	 *删除活动
	 *删除活动下的商品
	 *此处需要开启事务
	*/
	public function removePresaleAct()
	{
		$presale_ids = request_row('id');

        if (is_array($presale_ids)) {
            foreach ($presale_ids as $presale_id){
                $check_right = $this->presaleBaseModel->getOne($presale_id);
            }
        }

		if ($check_right['shop_id'] == Perm::$shopId)
		{
			$this->presaleBaseModel->sql->startTransactionDb(); //开启事务

			$flag = $this->presaleBaseModel->removePresaleActItem($presale_id);

			if ($flag && $this->presaleBaseModel->sql->commitDb())
			{
				$msg    = __('删除成功！');
				$status = 200;
			}
			else
			{
				$this->presaleBaseModel->sql->rollBackDb();
				$msg    = __('删除失败！');
				$status = 250;
			}
		}
		else
		{
			$msg    = __('删除失败！');
			$status = 250;
		}

		$data['presale_id'] = $presale_id;

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
	
	
	public function addPresaleGoods()
	{
		if (!$this->combo_flag)
		{
			$msg_label    = __('您尚未购买套餐或套餐已到期！');
			$flag   = false;
		}
		else
		{
            $presale_id                                         = request_int('presale_id');
            $cond_row_presale_base['shop_id']                   = Perm::$shopId;
            $cond_row_presale_base['presale_id']               = $presale_id;
            $cond_row_presale_base['presale_state']            = Presale_BaseModel::NORMAL;
            $cond_row_presale_base['presale_end_time:>=']      = get_date_time();
			$presale_base_row = $this->presaleBaseModel->getPresaleActInfo($cond_row_presale_base);

            fb($cond_row_presale_base);
            fb($presale_base_row);
            if (empty($presale_base_row))
            {
                $msg_label    = __('活动不存在或状态不可用！');
                $flag   = false;
            }
            else
            {
                //检查商品是否已经加入过同时段的活动
                $presale_goods_rows1               = array();
                $presale_goods_rows2               = array();
                $cond_row1                         = array();
                $cond_row1['goods_id']             = request_int('goods_id');
                $cond_row1['presale_goods_state']  = Presale_goodsModel::NORMAL;
                $cond_row1['goods_start_time:<=']  = $presale_base_row['presale_start_time'];
                $cond_row1['goods_end_time:>=']    = $presale_base_row['presale_start_time'];
                $presale_goods_rows1               = $this->presaleGoodsModel->getPresaleGoodsByWhere($cond_row1);

                $cond_row2                         = array();
                $cond_row2['goods_id']             = request_int('goods_id');
                $cond_row2['presale_goods_state']  = Presale_goodsModel::NORMAL;
                $cond_row2['goods_start_time:<=']  = $presale_base_row['presale_end_time'];
                $cond_row2['goods_end_time:>=']    = $presale_base_row['presale_end_time'];
                $presale_goods_rows2               = $this->presaleGoodsModel->getPresaleGoodsByWhere($cond_row2);

                if (!empty($presale_goods_rows1) || !empty($presale_goods_rows2))
                {
                    $msg_label    = __('该商品已参加过同时段的活动！');
                    $flag         = false;
                }
                else
                {
                    $check_post_data = true;
                    $field_row['presale_price'] =   request_float('presale_price');  //商品折扣价
                    $field_row['goods_id']       =  request_int('goods_id');          //商品goods_id
                  
                    $cond_row_goods_base['goods_id'] = $field_row['goods_id'];
                    $cond_row_goods_base['shop_id']  = Perm::$shopId;
                    $goodsBaseModel = new Goods_BaseModel();
                    $goods_base_row = $goodsBaseModel->getOneByWhere($cond_row_goods_base);

                    if (!empty($goods_base_row))
                    {
                        $field_row['goods_price']    = (float)$goods_base_row['goods_price']; //商品原价
                        $field_row['common_id']      = (int)$goods_base_row['common_id'];    //商品common_id
                        $field_row['goods_name']     = $goods_base_row['goods_name'];  //商品名
						
                        //$field_row['cat_id'] = $goods_base_row['cat_id'];
                        if ($field_row['presale_price'] <= 0)
                        {
                            $check_post_data = false;
                            $msg_label = __('请填写商品的预售价格！');
                        }
                    }
                    else
                    {
                        $check_post_data = false;
                        $msg_label = __('请选择参加活动的商品！');
                    }

                    $field_row['presale_id']               = (int)$presale_id;
                    $field_row['shop_id']                  = (int)Perm::$shopId;
                    $field_row['presale_name']             = $presale_base_row['presale_name'];
                    $field_row['goods_start_time']         = $presale_base_row['presale_start_time'];
                    $field_row['goods_end_time']           = $presale_base_row['presale_end_time'];
                    $field_row['goods_lower_limit']        = (int)$presale_base_row['presale_lower_limit'];
                    $field_row['presale_goods_state']      = (int)Presale_GoodsModel::NORMAL;
					$field_row['presale_deposit']		   = $presale_base_row['presale_deposit'];
					$field_row['goods_final_time']       = $presale_base_row['presale_final_time'];
                    $field_row['goods_final_time_end']   = date('Y-m-d H:i:s',strtotime($field_row['goods_final_time']).'+3 days');
                    if ($check_post_data)
                    {
                        $rs_row = array();

                        $this->presaleGoodsModel->sql->startTransactionDb();

                        $insert_flag = $this->presaleGoodsModel->addPresaleGoods($field_row,true);
                        check_rs($insert_flag, $rs_row);
					
                        $Goods_CommonModel = new Goods_CommonModel();
                        $update_flag       = $Goods_CommonModel->editCommon(request_int('common_id'), array('common_is_yushou' => 1));
                        check_rs($update_flag, $rs_row);
   
                        if (is_ok($rs_row) && $this->presaleGoodsModel->sql->commitDb())
                        {
                        
					       

                            $flag = true;
                        }
                        else
                        {
                            $this->presaleGoodsModel->sql->rollBackDb();
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

		$data['presale_goods_id'] = $insert_flag?$insert_flag:0;

		$this->data->addBody(-140, $data, $msg, $status);
	}
	
	public function removePresaleGoods()
	{
		$presale_goods_ids = request_row('id');
        if (is_array($presale_goods_ids)) {
            foreach ($presale_goods_ids as $presale_goods_id){
                $check_right       = $this->presaleGoodsModel->getOne($presale_goods_id);
            }
        }
		if ($check_right['shop_id'] == Perm::$shopId)
		{
			$this->presaleGoodsModel->sql->startTransactionDb(); //开启事务

			$flag = $this->presaleGoodsModel->removePresaleGoods($presale_goods_id);

			if ($flag && $this->presaleGoodsModel->sql->commitDb())
			{
				$msg    = __('删除成功');
				$status = 200;
			}
			else
			{
				$this->presaleGoodsModel->sql->rollBackDb();
				$msg    = __('删除失败');
				$status = 250;
			}
		}
		else
		{
			$msg    = __('删除失败');
			$status = 250;
		}

		$data['presale_goods_id'] = $presale_goods_id;

		$this->data->addBody(-140, $data, $msg, $status);
	}

	//编辑秒杀商品价格、库存
	public function editPresaleGoodsPrice()
	{
		$data = array();

        if ($this->combo_flag)
        {
			$presale_goods_id           = request_int('presale_goods_id');
            $field_row['presale_price'] = request_float('presale_price');
            

            $cond_row_presale_goods['presale_goods_id']   = $presale_goods_id;
            $presale_goods_row = $this->presaleGoodsModel->getPresaleGoodsDetailByWhere($cond_row_presale_goods);
            
            if ($presale_goods_row)
            {
                $check_post_data = true;

                if ($field_row['presale_price'] <= 0)
                {
                    $check_post_data = false;
                    $msg_label = __('请输入商品预售价格！');
                }
                
   

                if ($check_post_data)
                {
                    $this->presaleGoodsModel->editPresaleGoods($presale_goods_id, $field_row);
                    $flag                      = true;
                    $data                      = $field_row;
                    $data['presale_goods_id']  = $presale_goods_id;
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


}

?>