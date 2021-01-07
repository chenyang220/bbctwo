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
class Presale_QuotaModel extends Presale_Quota
{
	public function getPresaleQuotaList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}

	public function getPresaleComboList($cond_row, $order_row, $page, $rows)
	{
		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}

	/**
	 *根据店铺ID获取店铺折扣套餐
	 * @param $shop_id 店铺id
	 * @return array
	 */
	public function getPresaleQuotaByShopID($shop_id)
	{
		return $this->getOneByWhere(array('shop_id' => $shop_id));
	}

	/*检查套餐状态*/
	public function checkQuotaStateByShopId($shop_id)
	{
		$row                           = array();
		$cond_row['shop_id']           = $shop_id;
		$cond_row['combo_end_time:>='] = date('Y-m-d H:i:s');
		$row                           = $this->getOneByWhere($cond_row);

		if (!empty($row))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function removeXianShiQuotaItem($combo_id)
	{
		$del_flag = $this->remove($combo_id);

		return $del_flag;
	}

	/*
	 * 购买套餐
	 * */
	public function addPresaleCombo($field_row, $return_insert_id)
	{
		return $this->add($field_row, $return_insert_id);
	}

	/*
	 * 套餐续费
	 * */
	public function renewPresaleCombo($combo_id, $field_row)
	{
		return $this->edit($combo_id, $field_row);
	}
}