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
class Seckill_BaseModel extends Seckill_Base
{   
	const ZERO   = 0;
	const NORMAL = 1;//正常
	const END    = 2;//结束
	const CANCEL = 3;//管理员关闭
	const NO_NORMAL = 4;//已驳回
	public static $state_array_map = array(
		self::ZERO =>'待审核',
		self::NORMAL => '正常',
		self::END => '已结束',
		self::CANCEL => '管理员关闭',
		self::NO_NORMAL => '已驳回'
	);

	public $Seckill_GoodsModel = null;

	public function __construct()
	{
		parent::__construct();

		$this->Seckill_GoodsModel = new Seckill_GoodsModel();
	}

	/**
	 * @param array $cond_row
	 * @param array $order_row
	 * @param int $page
	 * @param int $rows
	 * @return array
	 */
	public function getSeckillActList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		$data_rows = $this->listByWhere($cond_row, $order_row, $page, $rows);

		if ($data_rows)
		{
			$expire_seckill_id       = array();  //过期的活动id
			$expire_seckill_goods_id = array();
			foreach ($data_rows['items'] as $key => $value)
			{
				$data_rows['items'][$key]['seckill_state_label'] = __(self::$state_array_map[$value['seckill_state']]);
				if (time() > strtotime($value['seckill_end_time']))
				{
					$data_rows['items'][$key]['seckill_state']       = self::END;
					$data_rows['items'][$key]['seckill_state_label'] = __(self::$state_array_map[self::END]);

					$expire_seckill_id[] = $value['seckill_id'];
				}
			}
			
			if ($expire_seckill_id)
			{
				$field_row['seckill_state'] = self::END;

				$this->changeSeckillStateUnnormal($expire_seckill_id, $field_row);

				//$this->editDiscountActInfo($expire_discount_id, $field_row); //活动结束

				$cond_row_seckill['seckill_id:IN'] = $expire_seckill_id;
				$expire_seckill_goods_id            = array_keys($this->Seckill_GoodsModel->getSeckillGoodsByWhere($cond_row_seckill));

				if ($expire_seckill_goods_id)
				{
					$field_row_seckill_goods['seckill_goods_state'] = Seckill_GoodsModel::END;
					$this->Seckill_GoodsModel->editSeckillGoods($expire_seckill_goods_id, $field_row_seckill_goods);
				}
			}
		}


		return $data_rows;
	}


	public function removeSeckillActItem($seckill_id)
	{
		$rs_row = array();

		//删除活动下的商品
		$seckill_goods_id_row = $this->Seckill_GoodsModel->getKeyByWhere(array('seckill_id' => $seckill_id));

		if($seckill_goods_id_row)
		{
			$flag = $this->Seckill_GoodsModel->removeSeckillGoods($seckill_goods_id_row);
			check_rs($flag, $rs_row);
		}

		//删除活动
		$del_flag = $this->remove($seckill_id);
		check_rs($del_flag, $rs_row);

		return is_ok($rs_row);
	}

	public function getSeckillActItemById($seckill_id)
	{
		$row                         = $this->getOne($seckill_id);
		$row['seckill_state_label'] = __(self::$state_array_map[$row['seckill_state']]);
		return $row;
	}

	public function addSeckillActivity($field_row, $return_insert_id)
	{
		return $this->add($field_row, $return_insert_id);
	}

	public function getSeckillActInfo($cond_row)
	{
		$row = $this->getOneByWhere($cond_row);

		if ($row)
		{
			$row['seckill_state_label'] = __(self::$state_array_map[$row['seckill_state']]);
		}
		return $row;
	}

	public function editSeckillActInfo($seckill_id, $field_row)
	{
		$update_flag = $this->edit($seckill_id, $field_row);

		return $update_flag;
	}

	//除计划任务和管理员取消活动外，其它地方请勿调用
	//更改活动状态为不可用，针对活动到期或管理员关闭
	public function changeSeckillStateUnnormal($seckill_id, $field_row)
	{
		$rs_row = array();

		if(is_array($seckill_id))
		{
			$cond_row['seckill_id:IN'] = $seckill_id;
		}
		else
		{
			$cond_row['seckill_id'] = $seckill_id;
		}

		//活动下的商品
		$seckill_goods_id_row = $this->Seckill_GoodsModel->getKeyByWhere($cond_row);

		$flag = $this->Seckill_GoodsModel->changeSeckillGoodsUnnormal($seckill_goods_id_row);
		check_rs($flag, $rs_row);

		//更改活动状态
		$update_flag = $this->edit($seckill_id, $field_row);
		check_rs($update_flag, $rs_row);

		return is_ok($rs_row);

	}

	//正在、即将参加限时折扣的商品
    public function getSeckillGoodsIds()
    {
        $sql = "SELECT discount_goods.goods_id FROM ";
        $sql .= TABEL_PREFIX . "seckill_base AS seckill_base JOIN ";
        $sql .= TABEL_PREFIX . "seckill_goods AS seckill_goods ON ";
        $sql .= "seckill_base.seckill_id = seckill_goods.seckill_id ";
        $sql .= "where 1";
        $sql .= " AND seckill_base.seckill_state = 1 AND seckill_base.seckill_start_time > '" . date('Y-m-d H:i:s', time()) . "'";
        $sql .= " AND seckill_base.shop_id = " . Perm::$shopId;
        $result = $this->sql->getAll($sql);

        $goods_ids = array_column($result, 'goods_id');
        return array_unique($goods_ids);
    }
}