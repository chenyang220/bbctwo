<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/15
 * Time: 17:57
 */
class Discount_GoodsModel extends Discount_Goods
{
	const NORMAL = 1;       //活动正常
	const END    = 2;        //活动结束
	const CANCEL = 3;       //活动取消

	const UNRECOMMEND = 0;  //未推荐
	const RECOMMEND   = 1;    //推荐

	public $Goods_BaseModel = null;

	public function __construct()
	{
		parent::__construct();
		$this->Goods_BaseModel = new Goods_BaseModel();
	}

	//限时折扣商品列表，分页
	public function getDiscountGoodsList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		$goods_rows   = array();
		$goods_id_row = array();
		$rows         = $this->listByWhere($cond_row, $order_row, $page, $rows);
		if ($rows['items'])
		{
			foreach ($rows['items'] as $key => $value)
			{
				if (strtotime($value['goods_end_time']) < time())
				{
					$rows['items'][$key]['discount_goods_state'] = self::END;

					$field_row['discount_goods_state'] = self::END;
					$this->editDiscountGoods($value['discount_goods_id'], $field_row);
				}
				$goods_id_row[] = $value['goods_id'];
			}
		}
		$goods_rows = $this->Goods_BaseModel->getGoodsListByGoodId($goods_id_row);
		
		foreach ($rows['items'] as $key => &$value)
		{ 
			if ($goods_rows[$value['goods_id']]!='') {
				$rows['items'][$key]['goods_name']       = $goods_rows[$value['goods_id']]['goods_name'];
				$rows['items'][$key]['goods_price']      = $goods_rows[$value['goods_id']]['goods_price'];
				$rows['items'][$key]['goods_image']      = $goods_rows[$value['goods_id']]['goods_image'];
				$rows['items'][$key]['goods_salenum']      = $goods_rows[$value['goods_id']]['goods_salenum'];
				$rows['items'][$key]['shop_name']      = $goods_rows[$value['goods_id']]['shop_name'];
				if(is_array($goods_rows[$value['goods_id']]['goods_spec']))
	            {
	                $goods_spec = current($goods_rows[$value['goods_id']]['goods_spec']);
	            }
	            else
	            {
	                $goods_spec = array();
	            }
	            $rows['items'][$key]['goods_spec_str']      = implode(',', $goods_spec);

				if($goods_rows[$value['goods_id']]['goods_price'] > 0) {
					$rows['items'][$key]['discount_percent'] = sprintf("%.1f", $rows['items'][$key]['discount_price'] / $goods_rows[$value['goods_id']]['goods_price'] * 10);
				}
				else{
					$rows['items'][$key]['discount_percent'] = '10.0';
				}
			} else{
				unset($rows['items'][$key]);
			}
		}

		return $rows;
	}

	//显示折扣商品列表，不分页
	public function getDiscountGoods($cond_row = array(), $order_row = array())
	{
		$goods_rows   = array();
		$goods_id_row = array();
		$rows         = $this->getByWhere($cond_row, $order_row);

		if ($rows)
		{
			foreach ($rows as $key => $value)
			{
				if (strtotime($value['goods_end_time']) < time())
				{
					$rows[$key]['discount_goods_state'] = self::END;

					$field_row['discount_goods_state'] = self::END;
					$this->editDiscountGoods($value['discount_goods_id'], $field_row);
				}
				$goods_id_row[] = $value['goods_id'];
			}
		}
		$goods_rows      = $this->Goods_BaseModel->getGoodsListByGoodId($goods_id_row); //活动商品信息
		$exception_goods = array();//商品异常信息，即在活动中存在的商品，但是已被卖家删除

		foreach ($rows as $key => $value)
		{
			if (@$goods_rows[$value['goods_id']])
			{
				$rows[$key]['goods_name']       = $goods_rows[$value['goods_id']]['goods_name'];
				$rows[$key]['goods_price']      = $goods_rows[$value['goods_id']]['goods_price'];
				$rows[$key]['goods_image']      = $goods_rows[$value['goods_id']]['goods_image'];
				$rows[$key]['discount_percent'] = sprintf("%.1f", $rows[$key]['discount_price'] / $goods_rows[$value['goods_id']]['goods_price'] * 10);
            }
			else
			{
				$exception_goods[] = $value['discount_goods_id'];
				unset($rows[$key]);
			}
		}
	         foreach ($cond_row['discount_goods_id:IN'] as $k=>$v)
	         {
	             foreach ($rows as $kk=>$vv)
	             {
	                 if($v==$kk)
	                 {
	                     $rowst[] =$vv;
	                 }
	             }
	         }



		if ($exception_goods)
		{
			$this->removeDiscountGoods($exception_goods);
		}

		return $rowst;
	}


	public function getDiscountGoodsByWhere($cond_row)
	{
		$rows = $this->getByWhere($cond_row);
		return $rows;
	}

    //多条件获取活动商品详情
	public function getDiscountGoodsDetailByWhere($cond_row)
	{
		$row = $this->getOneByWhere($cond_row);
		if ($row)
		{
			if (strtotime($row['goods_end_time']) < time())
			{
				$row['discount_goods_state'] = self::END;
				$this->editDiscountGoods($row['discount_goods_id'], array('discount_goods_state'=>self::END));
			}

            $goods_base_row = $this->Goods_BaseModel->getOne($row['goods_id']);

            if ($goods_base_row)
            {
                $row['goods_name']       = $goods_base_row['goods_name'];
                $row['goods_price']      = $goods_base_row['goods_price'];
                $row['goods_image']      = $goods_base_row['goods_image'];
                $row['discount_percent'] = sprintf("%.1f", $row['discount_price'] / $goods_base_row['goods_price'] * 10);
            }
            else
            {
                unset($row);
            }
		}

		return $row;
	}

	public function removeDiscountGoods($discount_goods_id)
	{
		$rs_row = array();

		$discount_goods_row = $this->get($discount_goods_id);
		$common_id_row      = array_column($discount_goods_row, 'common_id');

		//删除活动商品，先操作
		$del_flag = $this->remove($discount_goods_id);
		check_rs($del_flag, $rs_row);

		if ($common_id_row)
		{
			$need_edit_row = $this->getCommonNormalPromotion($common_id_row);

			if ($need_edit_row)
			{
				$Goods_CommonModel = new Goods_CommonModel();
				$update_flag       = $Goods_CommonModel->editCommon($need_edit_row, array('common_is_xian' => 0));
				check_rs($update_flag, $rs_row);
			}
		}

		return is_ok($rs_row);
	}

	public function getCommonNormalPromotion($common_id)
	{
		if (is_array($common_id))
		{
			$common_id                      = array_unique($common_id);
			$cond_row_goods['common_id:IN'] = $common_id;
		}
		else
		{
			$common_id                   = (array)$common_id;
			$cond_row_goods['common_id'] = $common_id;
		}
		//根据 common_id 获取对应的限时折扣商品
		$discount_goods_rows  = $this->getByWhere($cond_row_goods);
		$no_modify_common_row = array();

		if ($discount_goods_rows)
		{
			$discount_common_id_rows = array();
			foreach ($discount_goods_rows as $key => $value)
			{
				$discount_common_id_rows[$value['common_id']][] = $value['discount_id'];
			}

			$discount_id_row = array_unique(array_column($discount_goods_rows, 'discount_id')); //活动ID

			if ($discount_id_row)
			{
				$Discount_BaseModel               = new Discount_BaseModel();
				$cond_row['discount_id:IN']       = $discount_id_row;
				$cond_row['discount_state']       = Discount_BaseModel::NORMAL;
				$cond_row['discount_end_time:>='] = get_date_time();
				$increase_keys_row                = $Discount_BaseModel->getKeyByWhere($cond_row);

				foreach ($discount_common_id_rows as $key => $value)
				{
					if (array_intersect($value, $increase_keys_row))
					{
						$no_modify_common_row[] = $key;
					}
				}
			}
		}

		return array_diff($common_id, $no_modify_common_row);
	}

	public function addDiscountGoods($field_row, $return_insert_id)
	{
		return $this->add($field_row, $return_insert_id);
	}

	/*修改折扣商品部信息*/
	public function editDiscountGoods($discount_goods_id, $field_row)
	{
		$flag = $this->edit($discount_goods_id, $field_row);
		return $flag;
	}


	//除计划任务和管理员取消订单外，其它地方请勿调用
	/*修改折扣商品部信息,common表中活动状态，针对活动到期和和管理员取消*/
	public function changeDiscountGoodsUnnormal($discount_goods_id)
	{
		$rs_row = array();

		$discount_goods_row = $this->get($discount_goods_id);
		$common_id_row      = array_column($discount_goods_row, 'common_id');

		if ($common_id_row)
		{
			$need_edit_row = $this->getCommonNormalPromotion($common_id_row);

			if ($need_edit_row)
			{
				$Goods_CommonModel = new Goods_CommonModel();
				$update_flag       = $Goods_CommonModel->editCommon($need_edit_row, array('common_is_xian' => 0));
				check_rs($update_flag, $rs_row);
			}
		}

		return is_ok($rs_row);
	}
    
    /**
     * 获取店铺正在进行活动或者即将进行活动的商品
     * @param type $common_id
     * @return type
     */
    public function getDiscountByCommonId($common_id){
        //获取团购
        $cond_row = is_array($common_id) ? array('common_id:IN'=>$common_id) : array('common_id'=>$common_id);
        $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');
        $cond_row['discount_goods_state:!='] = self::END;
        $cond_row['discount_goods_state:!='] = self::CANCEL;
        $list = $this->getByWhere($cond_row);
        return $list;
    }
    
    /**
     * 获取店铺正在进行活动或者即将进行活动的商品
     * @return type
     */
    public function getDiscount(){
        //获取团购
        $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');
        $cond_row['discount_goods_state:!='] = self::END;
        $cond_row['discount_goods_state:!='] = self::CANCEL;
        $list = $this->getByWhere($cond_row);
        return $list;
    }
    
    /**
     * 获取common_id
     * @param type $list
     * @return type
     */
    public function getCommonidByDiscountList($list){
        if(!$list){
            return array();
        }
        $ids = array();
        foreach ($list as $value){
            $ids[] = $value['common_id'];
        }
        return $ids;
    }

	//获取首页版块信息
	public function getForumDiscount($discount_id)
	{
		$data = array();
		if (!empty($discount_id)) {
			$discount_list = $this->getNormalDiscount($discount_id);

			if ( !empty($discount_list) )
			{
				$Goods_BaseModel = new Goods_BaseModel();
				foreach ($discount_list as $dicsount_key => $discount_data)
				{
					if (strtotime($discount_data['goods_end_time']) > time() && $discount_data['discount_goods_state'] == Discount_BaseModel::NORMAL)
					{
						//查找商品
						$goods = $Goods_BaseModel->getOne($discount_data['goods_id']);
						$data[$dicsount_key]['discount_goods_id'] = $discount_data['discount_goods_id'];
						$data[$dicsount_key]['discount_id'] = $discount_data['discount_id'];
						$data[$dicsount_key]['discount_name'] = $discount_data['discount_name'];
						$data[$dicsount_key]['goods_id'] = $discount_data['goods_id'];
						$data[$dicsount_key]['common_id'] = $discount_data['common_id'];
						$data[$dicsount_key]['goods_image'] = $goods['goods_image'];
						$data[$dicsount_key]['goods_price'] = $discount_data['goods_price'];
						$data[$dicsount_key]['discount_price'] = $discount_data['discount_price'];
						$data[$dicsount_key]['goods_name'] = $discount_data['goods_name'];
						$data[$dicsount_key]['goods_end_time'] = $discount_data['goods_end_time'];
						$data[$dicsount_key]['goods_start_time'] = $discount_data['goods_start_time'];
						$data[$dicsount_key]['goods_lower_limit'] = $discount_data['goods_lower_limit'];
					}
				}
			}
		}
		return array_values($data);

	}


	public function getOpenForumDiscount($discount_goods_id,$num)
	{
		$not_in = '';
		if($discount_goods_id) { $not_in = "AND discount_goods_id NOT IN  (" . implode(',', $discount_goods_id) . ")";}
		//获取限时折扣
		$sql = "
                    SELECT
                        discount_goods_id,discount_id,discount_name,a.goods_id,a.common_id,b.goods_image,a.goods_price,discount_price,a.goods_name,goods_end_time,goods_start_time,goods_lower_limit
                    FROM
                        " . TABEL_PREFIX . "discount_goods a inner join " . TABEL_PREFIX . "goods_base b on a.goods_id = b.goods_id left join " . TABEL_PREFIX . "goods_common c on b.common_id=c.common_id
                    WHERE  'goods_end_time' >= '".date('Y-m-d H:i:s',time())."' ".$not_in."  AND discount_goods_state=1 AND b.goods_is_shelves=1 AND b.is_del=1 AND c.common_state=1 AND c.common_verify=1 AND c.is_del=1 ORDER BY discount_goods_id DESC  LIMIT ".$num;
		$rows = $this -> sql -> getAll($sql);

		return $rows;
	}

    /*
     *获取限时折扣列表
     * 判断限时折扣活动是否正常，判断限时折扣商品是否正常
     */
    public function getNormalDiscount($discount_goods_id)
    {
        $in = '';
        if($discount_goods_id) { $in = "AND discount_goods_id IN  (" . implode(',', $discount_goods_id) . ")";}
        $sql = "
                    SELECT
                        a.*
                    FROM
                        " . TABEL_PREFIX . "discount_goods a left join " . TABEL_PREFIX . "goods_base b on a.goods_id = b.goods_id left join " . TABEL_PREFIX . "goods_common c on b.common_id=c.common_id
                    WHERE  'goods_end_time' > '".date('Y-m-d H:i:s',time())."' ".$in."  AND discount_goods_state=1 AND b.goods_is_shelves=1 AND b.is_del=1 AND c.common_state=1 AND c.common_verify=1 AND c.is_del=1 ORDER BY discount_goods_id DESC ";

        $rows = $this -> sql -> getAll($sql);

        return $rows;
    }


}