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
class Presale_GoodsModel extends Presale_Goods
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
	
	//预售商品列表，分页
	public function getPresaleGoodsList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
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
					$rows['items'][$key]['presale_goods_state'] = self::END;

					$field_row['presale_goods_state'] = self::END;
					$this->editPresaleGoods($value['presale_goods_id'], $field_row);
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
					$rows['items'][$key]['presale_percent'] = sprintf("%.1f", $rows['items'][$key]['presale_price'] / $goods_rows[$value['goods_id']]['goods_price'] * 10);
				}
				else{
					$rows['items'][$key]['presale_percent'] = '10.0';
				}
			} else{
				unset($rows['items'][$key]);
			}
		}

		return $rows;
	}
	
	
	public function getPresaleGoods($cond_row = array(), $order_row = array())
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
					$rows[$key]['presale_goods_state'] = self::END;

					$field_row['presale_goods_state'] = self::END;
					$this->editPresaleGoods($value['presale_goods_id'], $field_row);
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
				$rows[$key]['presale_percent'] = sprintf("%.1f", $rows[$key]['presale_price'] / $goods_rows[$value['goods_id']]['goods_price'] * 10);
            }
			else
			{
				$exception_goods[] = $value['presale_goods_id'];
				unset($rows[$key]);
			}
		}

		if ($exception_goods)
		{
			$this->removePresaleGoods($exception_goods);
		}

		return $rows;
	}
	
	
	public function getPresaleGoodsByWhere($cond_row)
	{
		$rows = $this->getByWhere($cond_row);
		return $rows;
	}

    //多条件获取活动商品详情
	public function getPresaleGoodsDetailByWhere($cond_row)
	{
		$row = $this->getOne($cond_row['presale_goods_id']);
		if ($row)
		{
			if (strtotime($row['goods_end_time']) < time())
			{
				$row['presale_goods_state'] = self::END;
				$this->editPresaleGoods($row['presale_goods_id'], array('presale_goods_state'=>self::END));
			}

            $goods_base_row = $this->Goods_BaseModel->getOne($row['goods_id']);

            if ($goods_base_row)
            {
                $row['goods_name']       = $goods_base_row['goods_name'];
                $row['goods_price']      = $goods_base_row['goods_price'];
                $row['goods_image']      = $goods_base_row['goods_image'];
                $row['presale_percent'] = sprintf("%.1f", $row['presale_price'] / $goods_base_row['goods_price'] * 10);
            }
            else
            {
                unset($row);
            }
		}

		return $row;
	}

	public function removePresaleGoods($presale_goods_id)
	{
		$rs_row = array();

		$presale_goods_row = $this->get($presale_goods_id);
		$common_id_row      = array_column($presale_goods_row, 'common_id');

		//删除活动商品，先操作
		$del_flag = $this->remove($presale_goods_id);
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
		$seckill_goods_rows  = $this->getByWhere($cond_row_goods);
		$no_modify_common_row = array();

		if ($presale_goods_rows)
		{
			$presale_common_id_rows = array();
			foreach ($presale_goods_rows as $key => $value)
			{
				$presale_common_id_rows[$value['common_id']][] = $value['presale_id'];
			}

			$presale_id_row = array_unique(array_column($presale_goods_rows, 'presale_id')); //活动ID

			if ($presale_id_row)
			{
				$Presale_BaseModel               = new Presale_BaseModel();
				$cond_row['presale_id:IN']       = $presale_id_row;
				$cond_row['presale_state']       = Presale_BaseModel::NORMAL;
				$cond_row['presale_end_time:>='] = get_date_time();
				$increase_keys_row                = $Presale_BaseModel->getKeyByWhere($cond_row);

				foreach ($presale_common_id_rows as $key => $value)
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

	public function addPresaleGoods($field_row, $return_insert_id)
	{
		return $this->add($field_row, $return_insert_id);
	}

	/*修改折扣商品部信息*/
	public function editPresaleGoods($presale_goods_id, $field_row)
	{
		$flag = $this->edit($presale_goods_id, $field_row);
		return $flag;
	}


	//除计划任务和管理员取消订单外，其它地方请勿调用
	/*修改折扣商品部信息,common表中活动状态，针对活动到期和和管理员取消*/
	public function changePresaleGoodsUnnormal($presale_goods_id)
	{
		$rs_row = array();

		$presale_goods_row = $this->get($presale_goods_id);
		$common_id_row      = array_column($presale_goods_row, 'common_id');

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
    public function getPresaleByCommonId($common_id){
        //获取团购
        $cond_row = is_array($common_id) ? array('common_id:IN'=>$common_id) : array('common_id'=>$common_id);
        $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');
        $cond_row['presale_goods_state:!='] = self::END;
        $cond_row['presale_goods_state:!='] = self::CANCEL;
        $list = $this->getByWhere($cond_row);
        return $list;
    }
    
    /**
     * 获取店铺正在进行活动或者即将进行活动的商品
     * @return type
     */
    public function getPresale(){
        //获取团购
        $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');
        $cond_row['presale_goods_state:!='] = self::END;
        $cond_row['presale_goods_state:!='] = self::CANCEL;
        $list = $this->getByWhere($cond_row);
        return $list;
    }
    
    /**
     * 获取common_id
     * @param type $list
     * @return type
     */
    public function getCommonidByPresaleList($list){
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
	public function getForumPresale($presale_id)
	{
		$data = array();
		if (!empty($presale_id)) {
			$presale_list = $this->getNormalPresale($presale_id);

			if ( !empty($presale_list) )
			{
				$Goods_BaseModel = new Goods_BaseModel();
				foreach ($presale_list as $presale_key => $presale_data)
				{
					if (strtotime($presale_data['goods_end_time']) > time() && $presale_data['presale_goods_state'] == Presale_BaseModel::NORMAL)
					{
						//查找商品
						$goods = $Goods_BaseModel->getOne($presale_data['goods_id']);
						$data[$dicsount_key]['presale_goods_id'] = $presale_data['presale_goods_id'];
						$data[$dicsount_key]['presale_id'] = $seckill_data['presale_id'];
						$data[$dicsount_key]['presale_name'] = $seckill_data['presale_name'];
						$data[$dicsount_key]['goods_id'] = $seckill_data['goods_id'];
						$data[$dicsount_key]['common_id'] = $seckill_data['common_id'];
						$data[$dicsount_key]['goods_image'] = $goods['goods_image'];
						$data[$dicsount_key]['goods_price'] = $seckill_data['goods_price'];
						$data[$dicsount_key]['presale_price'] = $seckill_data['presale_price'];
						$data[$dicsount_key]['goods_name'] = $seckill_data['goods_name'];
						$data[$dicsount_key]['goods_end_time'] = $seckill_data['goods_end_time'];
						$data[$dicsount_key]['goods_start_time'] = $seckill_data['goods_start_time'];
						$data[$dicsount_key]['goods_lower_limit'] = $seckill_data['goods_lower_limit'];
					}
				}
			}
		}
		return array_values($data);

	}


	public function getOpenForumPresale($presale_goods_id,$num)
	{
		$not_in = '';
		if($presale_goods_id) { $not_in = "AND presale_goods_id NOT IN  (" . implode(',', $presale_goods_id) . ")";}
		//获取限时折扣
		$sql = "
                    SELECT
                        presale_goods_id,presale_id,presale_name,a.goods_id,a.common_id,b.goods_image,a.goods_price,presale_price,a.goods_name,goods_end_time,goods_start_time,goods_lower_limit
                    FROM
                        " . TABEL_PREFIX . "presale_goods a inner join " . TABEL_PREFIX . "goods_base b on a.goods_id = b.goods_id left join " . TABEL_PREFIX . "goods_common c on b.common_id=c.common_id
                    WHERE  'goods_end_time' >= '".date('Y-m-d H:i:s',time())."' ".$not_in."  AND presale_goods_state=1 AND b.goods_is_shelves=1 AND b.is_del=1 AND c.common_state=1 AND c.common_verify=1 AND c.is_del=1 ORDER BY presale_goods_id DESC  LIMIT ".$num;
		$rows = $this -> sql -> getAll($sql);

		return $rows;
	}

    /*
     *获取限时折扣列表
     * 判断限时折扣活动是否正常，判断限时折扣商品是否正常
     */
    public function getNormalPresale($presale_goods_id)
    {
        $in = '';
        if($presale_goods_id) { $in = "AND presale_goods_id IN  (" . implode(',', $presale_goods_id) . ")";}
        $sql = "
                    SELECT
                        a.*
                    FROM
                        " . TABEL_PREFIX . "presale_goods a left join " . TABEL_PREFIX . "goods_base b on a.goods_id = b.goods_id left join " . TABEL_PREFIX . "goods_common c on b.common_id=c.common_id
                    WHERE  'goods_end_time' > '".date('Y-m-d H:i:s',time())."' ".$in."  AND presale_goods_state=1 AND b.goods_is_shelves=1 AND b.is_del=1 AND c.common_state=1 AND c.common_verify=1 AND c.is_del=1 ORDER BY presale_goods_id DESC ";

        $rows = $this -> sql -> getAll($sql);

        return $rows;
    }


    public function getPresaleGoodsDetailByWheres($cond_row)
	{
		$row = $this->getOneByWhere($cond_row);

	
			if ($row)
			{
				if (strtotime($row['goods_end_time']) < time())
				{
					$row['presale_goods_state'] = self::END;
					$this->editPresaleGoods($row['presale_goods_id'], array('presale_goods_state'=>self::END));
				}
				
	            $goods_base_row = $this->Goods_BaseModel->getOne($row['goods_id']);

	            if ($goods_base_row)
	            {
	                $row['goods_name']       = $goods_base_row['goods_name'];
	                $row['goods_price']      = $goods_base_row['goods_price'];
	                $row['goods_image']      = $goods_base_row['goods_image'];
	                $row['presale_percent'] = sprintf("%.1f", $row['presale_price'] / $goods_base_row['goods_price'] * 10);
	            }
	            else
	            {
	                unset($row);
	            }
			}
		
		

		return $row;
	}


}