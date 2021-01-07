<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class RebateRatioModel extends RebateRatio
{
	public function getRebateRatio($order_row)
	{
		$data = $this->getOneByWhere($order_row);

		return $data;
	}
}
?>