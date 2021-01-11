<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 14:38
 */
class Api_Promotion_DiscountCtl extends Api_Controller
{
	public $Discount_BaseModel  = null;
	public $Discount_GoodsModel = null;
	public $Discount_QuotaModel = null;

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

		$this->Discount_BaseModel  = new Discount_BaseModel();
		$this->Discount_GoodsModel = new Discount_GoodsModel();
		$this->Discount_QuotaModel = new Discount_QuotaModel();
	}

	/* 满减活动*/
	//满送活动列表
	public function getDiscountList()
	{
		$page          	= request_int('page', 1);
		$rows          	= request_int('rows', 100);
		$xian_shi_name 	= trim(request_string('discount_name'));   //活动名称
		$shop_name     	= trim(request_string('shop_name'));       //店铺名称
		$discount_state	= request_int('discount_state');		   //活动状态

		$cond_row = array();

		if ($discount_state)
		{
			$cond_row['discount_state'] = $discount_state;
		}
		if ($xian_shi_name)
		{
			$cond_row['discount_name:LIKE'] = $xian_shi_name . '%';
		}
		if ($shop_name)
		{
			$cond_row['shop_name:LIKE'] = $shop_name . '%';
		}

		$data = $this->Discount_BaseModel->getDiscountActList($cond_row, array('discount_id' => 'DESC'), $page, $rows);

		$this->data->addBody(-140, $data);
	}

	/* 活动下的商品列表*/
	public function getDiscountGoodsListById()
	{
		$cond_row                = array();
		$page                    = request_int('page', 1);
		$rows                    = request_int('rows', 100);
		$cond_row['discount_id'] = request_int('id');
		$data                    = $this->Discount_GoodsModel->getDiscountGoodsList($cond_row, array(), $page, $rows);

		$this->data->addBody(-140, $data);
	}

	//取消活动
	public function cancelDiscount()
	{
		$data        = array();
		$discount_id = request_int('discount_id');
		if ($discount_id)
		{
			$rs_row = array();

			//获取活动下的商品
			$cond_discount_goods_row['discount_id'] = $discount_id;
			$discount_goods_id_row                  = $this->Discount_GoodsModel->getKeyByWhere($cond_discount_goods_row);

			$this->Discount_BaseModel->sql->startTransactionDb();

			//修改活动状态
			$field_row['discount_state'] = Discount_BaseModel::CANCEL;
			$update_flag1                = $this->Discount_BaseModel->editDiscountActInfo($discount_id, $field_row);
			check_rs($update_flag1, $rs_row);

			//修改活动下商品状态
			if ($discount_goods_id_row)
			{
				$field_discount_goods_row['discount_goods_state'] = Discount_GoodsModel::CANCEL;
				$update_flag2                                     = $this->Discount_GoodsModel->editDiscountGoods($discount_goods_id_row, $field_discount_goods_row);
				check_rs($update_flag2, $rs_row);

				$update_flag3                                     = $this->Discount_GoodsModel->changeDiscountGoodsUnnormal($discount_goods_id_row);
				check_rs($update_flag3, $rs_row);
			}

			if (is_ok($rs_row) && $this->Discount_BaseModel->sql->commitDb())
			{
				$data      = $this->Discount_BaseModel->getDiscountActItemById($discount_id);
				$data['a'] = $discount_id;
				$msg       = __('操作成功');
				$status    = 200;
			}
			else
			{
				$this->Discount_BaseModel->sql->rollBackDb();
				$msg    = __('操作失败');
				$status = 250;
			}

			$this->data->addBody(-140, $data, $msg, $status);
		}
	}

	/*
	 * 删除限时折扣活动
	 * 删除活动
	 * 删除活动下的商品
	*/
	public function removeDiscountActivity()
	{
		$data        = array();
		$discount_id = request_int('discount_id');

		$this->Discount_BaseModel->sql->startTransactionDb();

		$flag = $this->Discount_BaseModel->removeDiscountActItem($discount_id);

		if ($flag = $this->Discount_BaseModel->sql->commitDb())
		{
			$msg    = 'success';
			$status = 200;
		}
		else
		{
			$this->Discount_BaseModel->sql->rollBackDb();
			$msg    = 'failure';
			$status = 250;
		}

		$this->data->addBody(-140, $data, $msg, $status);
	}

	//套餐列表
	public function getPackageList()
	{
		$cond_row  = array();
		$page      = request_int('page', 1);
		$rows      = request_int('rows', 100);
		$shop_name = request_string('shop_name');

		if ($shop_name)
		{
			$cond_row['shop_name:LIKE'] = $shop_name . '%';
		}

		$data = $this->Discount_QuotaModel->getDiscountComboList($cond_row, array('combo_id' => 'DESC'), $page, $rows);

		$this->data->addBody(-140, $data);
	}

	//限时折扣商品列表
    public function getDiscountGoodsList()
    {
        $data = array();
        $promotion_allow = Web_ConfigModel::value("promotion_allow");
        if($promotion_allow == 1)
        {
            $Discount_GoodsModel = new Discount_GoodsModel();
            $page          	= request_int('page', 1);
            $rows          	= request_int('rows', 12);
            $xian_shi_name 	= trim(request_string('discount_name'));   //活动名称
            $discount_state	= request_int('discount_state');		   //活动状态

            $Shop_BaseModel = new Shop_BaseModel();
            $shop_cond['shop_name:LIKE'] = '%' . trim(request_string('shop_name')) . '%';//店铺名称
            $shop_list = $Shop_BaseModel->getByWhere($shop_cond);
            if($shop_list) {
                $shop_ids = array_column($shop_list,'shop_id');
                if($shop_ids) {
                    $cond_row['shop_id:IN'] = $shop_ids;
                }
            }else {
                $cond_row['shop_id:IN'] = array();
            }
            if ($discount_state)
            {
                $cond_row['discount_goods_state'] = $discount_state;
            }
            if ($xian_shi_name)
            {
                $cond_row['discount_name:LIKE'] = '%' . $xian_shi_name . '%';
            }

            $cond_row['goods_start_time:<'] = date('Y-m-d H:i:s');
            $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');

            $data = $Discount_GoodsModel->getDiscountGoodsList($cond_row, array('discount_id' => 'DESC'), $page, $rows);
            if($data['items']) {
                $msg = 'success';
                $status = 200;
            } else {
                $msg = '请在后台促销设置中打开商品促销功能并发布限时折扣商品1';
                $status = 250;
            }
        }else {
            $msg = '请在后台促销设置中打开商品促销功能并发布限时折扣商品2';
            $status = 250;
        }

        $this->data->addBody(-140, $data,$msg,$status);
    }
}
