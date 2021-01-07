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
class Presale_BaseModel extends Presale_Base
{   
	const ZERO   = 0;
	const NORMAL = 1;//正常
	const END    = 2;//结束
	const CANCEL = 3;//管理员关闭
	public static $state_array_map = array(
		self::ZERO =>'待审核',
		self::NORMAL => '正常',
		self::END => '已结束',
		self::CANCEL => '管理员关闭'
	);

	public $Presale_GoodsModel = null;

	public function __construct()
	{
		parent::__construct();

		$this->Presale_GoodsModel = new Presale_GoodsModel();
	}
	
	public function getPresaleActList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		$data_rows = $this->listByWhere($cond_row, $order_row, $page, $rows);

		if ($data_rows)
		{
			$expire_presale_id       = array();  //过期的活动id
			$expire_presale_goods_id = array();
			foreach ($data_rows['items'] as $key => $value)
			{
				$data_rows['items'][$key]['presale_state_label'] = __(self::$state_array_map[$value['presale_state']]);
				if (time() > strtotime($value['presale_end_time']))
				{
					$data_rows['items'][$key]['presale_state']       = self::END;
					$data_rows['items'][$key]['presale_state_label'] = __(self::$state_array_map[self::END]);

					$expire_presale_id[] = $value['presale_id'];
				}
			}
			
			if ($expire_presale_id)
			{
				$field_row['presale_state'] = self::END;

				$this->changePresaleStateUnnormal($expire_presale_id, $field_row);

				//$this->editDiscountActInfo($expire_discount_id, $field_row); //活动结束

				$cond_row_presale['presale_id:IN'] = $expire_presale_id;
				$expire_presale_goods_id            = array_keys($this->Presale_GoodsModel->getPresaleGoodsByWhere($cond_row_presale));

				if ($expire_presale_goods_id)
				{
					$field_row_presale_goods['presale_goods_state'] = Presale_GoodsModel::END;
					$this->Presale_GoodsModel->editPresaleGoods($expire_presale_goods_id, $field_row_presale_goods);
				}
			}
		}


		return $data_rows;
	}
	
	public function addPresaleActivity($field_row, $return_insert_id)
	{
		return $this->add($field_row, $return_insert_id);
	}
	
	public function getPresaleActInfo($cond_row)
	{
		$row = $this->getOneByWhere($cond_row);

		if ($row)
		{
			$row['presale_state_label'] = __(self::$state_array_map[$row['presale_state']]);
		}
		return $row;
	}
	
	//除计划任务和管理员取消活动外，其它地方请勿调用
	//更改活动状态为不可用，针对活动到期或管理员关闭
	public function changePresaleStateUnnormal($presale_id, $field_row)
	{
		$rs_row = array();

		if(is_array($presale_id))
		{
			$cond_row['presale_id:IN'] = $presale_id;
		}
		else
		{
			$cond_row['presale_id'] = $presale_id;
		}

		//活动下的商品
		$presale_goods_id_row = $this->Presale_GoodsModel->getKeyByWhere($cond_row);

		$flag = $this->Presale_GoodsModel->changePresaleGoodsUnnormal($presale_goods_id_row);
		check_rs($flag, $rs_row);

		//更改活动状态
		$update_flag = $this->edit($presale_id, $field_row);
		check_rs($update_flag, $rs_row);

		return is_ok($rs_row);

	}
	
	public function editPresaleActInfo($presale_id, $field_row)
	{
		$update_flag = $this->edit($presale_id, $field_row);

		return $update_flag;
	}
	
	public function removePresaleActItem($presale_id)
	{
		$rs_row = array();

		//删除活动下的商品
		$presale_goods_id_row = $this->Presale_GoodsModel->getKeyByWhere(array('presale_id' => $presale_id));

		if($presale_goods_id_row)
		{
			$flag = $this->Presale_GoodsModel->removePresaleGoods($presale_goods_id_row);
			check_rs($flag, $rs_row);
		}

		//删除活动
		$del_flag = $this->remove($presale_id);
		check_rs($del_flag, $rs_row);

		return is_ok($rs_row);
	}


}