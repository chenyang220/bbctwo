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
class Seckill_GoodsModel extends Seckill_Goods
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
	public function getSeckillGoodsList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
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
					$rows['items'][$key]['seckill_goods_state'] = self::END;

					$field_row['seckill_goods_state'] = self::END;
					$this->editSeckillGoods($value['seckill_goods_id'], $field_row);
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
					$rows['items'][$key]['seckill_percent'] = sprintf("%.1f", $rows['items'][$key]['seckill_price'] / $goods_rows[$value['goods_id']]['goods_price'] * 10);
				}
				else{
					$rows['items'][$key]['seckill_percent'] = '10.0';
				}
			} else{
				unset($rows['items'][$key]);
			}
		}

		return $rows;
	}

	//显示折扣商品列表，不分页
	public function getSeckillGoods($cond_row = array(), $order_row = array())
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
					$rows[$key]['seckill_goods_state'] = self::END;

					$field_row['seckill_goods_state'] = self::END;
					$this->editSeckillGoods($value['seckill_goods_id'], $field_row);
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
				$rows[$key]['seckill_percent'] = sprintf("%.1f", $rows[$key]['seckill_price'] / $goods_rows[$value['goods_id']]['goods_price'] * 10);
            }
			else
			{
				$exception_goods[] = $value['seckill_goods_id'];
				unset($rows[$key]);
			}
		}

		if ($exception_goods)
		{
			$this->removeSeckillGoods($exception_goods);
		}

		return $rows;
	}


	public function getSeckillGoodsByWhere($cond_row)
	{
		$rows = $this->getByWhere($cond_row);
		return $rows;
	}

    //多条件获取活动商品详情
	public function getSeckillGoodsDetailByWhere($cond_row)
	{
		$row = $this->getOne($cond_row['seckill_goods_id']);
		if ($row)
		{
			if (strtotime($row['goods_end_time']) < time())
			{
				$row['seckill_goods_state'] = self::END;
				$this->editSeckillGoods($row['seckill_goods_id'], array('seckill_goods_state'=>self::END));
			}

            $goods_base_row = $this->Goods_BaseModel->getOne($row['goods_id']);

            if ($goods_base_row)
            {
                $row['goods_name']       = $goods_base_row['goods_name'];
                $row['goods_price']      = $goods_base_row['goods_price'];
                $row['goods_image']      = $goods_base_row['goods_image'];
                $row['seckill_percent'] = sprintf("%.1f", $row['seckill_price'] / $goods_base_row['goods_price'] * 10);
            }
            else
            {
                unset($row);
            }
		}

		return $row;
	}

	public function removeSeckillGoods($seckill_goods_id)
	{
		$rs_row = array();

		$seckill_goods_row = $this->get($seckill_goods_id);
		$common_id_row      = array_column($seckill_goods_row, 'common_id');

		//删除活动商品，先操作
		$del_flag = $this->remove($seckill_goods_id);
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

		if ($seckill_goods_rows)
		{
			$seckill_common_id_rows = array();
			foreach ($seckill_goods_rows as $key => $value)
			{
				$seckill_common_id_rows[$value['common_id']][] = $value['seckill_id'];
			}

			$seckill_id_row = array_unique(array_column($seckill_goods_rows, 'seckill_id')); //活动ID

			if ($seckill_id_row)
			{
				$Seckill_BaseModel               = new Seckill_BaseModel();
				$cond_row['seckill_id:IN']       = $seckill_id_row;
				$cond_row['seckill_state']       = Seckill_BaseModel::NORMAL;
				$cond_row['seckill_end_time:>='] = get_date_time();
				$increase_keys_row                = $Seckill_BaseModel->getKeyByWhere($cond_row);

				foreach ($seckill_common_id_rows as $key => $value)
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

	public function addSeckillGoods($field_row, $return_insert_id)
	{
		return $this->add($field_row, $return_insert_id);
	}

	/*修改折扣商品部信息*/
	public function editSeckillGoods($seckill_goods_id, $field_row)
	{
		$flag = $this->edit($seckill_goods_id, $field_row);
		return $flag;
	}


	//除计划任务和管理员取消订单外，其它地方请勿调用
	/*修改折扣商品部信息,common表中活动状态，针对活动到期和和管理员取消*/
	public function changeSeckillGoodsUnnormal($seckill_goods_id)
	{
		$rs_row = array();

		$seckill_goods_row = $this->get($seckill_goods_id);
		$common_id_row      = array_column($seckill_goods_row, 'common_id');

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
    public function getSeckillByCommonId($common_id){
        //获取团购
        $cond_row = is_array($common_id) ? array('common_id:IN'=>$common_id) : array('common_id'=>$common_id);
        $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');
        $cond_row['seckill_goods_state:!='] = self::END;
        $cond_row['seckill_goods_state:!='] = self::CANCEL;
        $list = $this->getByWhere($cond_row);
        return $list;
    }
    
    /**
     * 获取店铺正在进行活动或者即将进行活动的商品
     * @return type
     */
    public function getSeckill(){
        //获取团购
        $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');
        $cond_row['seckill_goods_state:!='] = self::END;
        $cond_row['seckill_goods_state:!='] = self::CANCEL;
        $list = $this->getByWhere($cond_row);
        return $list;
    }
    
    /**
     * 获取common_id
     * @param type $list
     * @return type
     */
    public function getCommonidBySeckillList($list){
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
	public function getForumSeckill($seckill_id)
	{
		$data = array();
		if (!empty($seckill_id)) {
			$seckill_list = $this->getNormalSeckill($seckill_id);

			if ( !empty($seckill_list) )
			{
				$Goods_BaseModel = new Goods_BaseModel();
				foreach ($seckill_list as $seckill_key => $seckill_data)
				{
					if (strtotime($seckill_data['goods_end_time']) > time() && $seckill_data['seckill_goods_state'] == Seckill_BaseModel::NORMAL)
					{
						//查找商品
						$goods = $Goods_BaseModel->getOne($seckill_data['goods_id']);
						$data[$dicsount_key]['seckill_goods_id'] = $seckill_data['seckill_goods_id'];
						$data[$dicsount_key]['seckill_id'] = $seckill_data['seckill_id'];
						$data[$dicsount_key]['seckill_name'] = $seckill_data['seckill_name'];
						$data[$dicsount_key]['goods_id'] = $seckill_data['goods_id'];
						$data[$dicsount_key]['common_id'] = $seckill_data['common_id'];
						$data[$dicsount_key]['goods_image'] = $goods['goods_image'];
						$data[$dicsount_key]['goods_price'] = $seckill_data['goods_price'];
						$data[$dicsount_key]['seckill_price'] = $seckill_data['seckill_price'];
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


	public function getOpenForumSeckill($seckill_goods_id,$num)
	{
		$not_in = '';
		if($seckill_goods_id) { $not_in = "AND seckill_goods_id NOT IN  (" . implode(',', $seckill_goods_id) . ")";}
		//获取限时折扣
		$sql = "
                    SELECT
                        seckill_goods_id,seckill_id,seckill_name,a.goods_id,a.common_id,b.goods_image,a.goods_price,seckill_price,a.goods_name,goods_end_time,goods_start_time,goods_lower_limit
                    FROM
                        " . TABEL_PREFIX . "seckill_goods a inner join " . TABEL_PREFIX . "goods_base b on a.goods_id = b.goods_id left join " . TABEL_PREFIX . "goods_common c on b.common_id=c.common_id
                    WHERE  'goods_end_time' >= '".date('Y-m-d H:i:s',time())."' ".$not_in."  AND seckill_goods_state=1 AND b.goods_is_shelves=1 AND b.is_del=1 AND c.common_state=1 AND c.common_verify=1 AND c.is_del=1 ORDER BY seckill_goods_id DESC  LIMIT ".$num;
		$rows = $this -> sql -> getAll($sql);

		return $rows;
	}

    /*
     *获取限时折扣列表
     * 判断限时折扣活动是否正常，判断限时折扣商品是否正常
     */
    public function getNormalSeckill($seckill_goods_id)
    {
        $in = '';
        if($seckill_goods_id) { $in = "AND seckill_goods_id IN  (" . implode(',', $seckill_goods_id) . ")";}
        $sql = "
                    SELECT
                        a.*
                    FROM
                        " . TABEL_PREFIX . "seckill_goods a left join " . TABEL_PREFIX . "goods_base b on a.goods_id = b.goods_id left join " . TABEL_PREFIX . "goods_common c on b.common_id=c.common_id
                    WHERE  'goods_end_time' > '".date('Y-m-d H:i:s',time())."' ".$in."  AND seckill_goods_state=1 AND b.goods_is_shelves=1 AND b.is_del=1 AND c.common_state=1 AND c.common_verify=1 AND c.is_del=1 ORDER BY seckill_goods_id DESC ";

        $rows = $this -> sql -> getAll($sql);

        return $rows;
    }


    public function getSeckillGoodsDetailByWheres($cond_row)
	{
		$row = $this->getOneByWhere($cond_row);

		if(date('Y-m-d '.$row['day_end_time'].':00:00')<time()){
			if ($row)
			{
				if (strtotime($row['goods_end_time']) < time())
				{
					$row['seckill_goods_state'] = self::END;
					$this->editSeckillGoods($row['seckill_goods_id'], array('seckill_goods_state'=>self::END));
				}

	            $goods_base_row = $this->Goods_BaseModel->getOne($row['goods_id']);

	            if ($goods_base_row)
	            {
	                $row['goods_name']       = $goods_base_row['goods_name'];
	                $row['goods_price']      = $goods_base_row['goods_price'];
	                $row['goods_image']      = $goods_base_row['goods_image'];
	                $row['seckill_percent'] = sprintf("%.1f", $row['seckill_price'] / $goods_base_row['goods_price'] * 10);
	            }
	            else
	            {
	                unset($row);
	            }
			}
		}else{
			$row = [];
		}
		

		return $row;
	}


}