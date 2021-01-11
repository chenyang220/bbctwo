<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Seller_SellerWxMenuModel extends Seller_SellerWxMenu
{

	/**
	 * 读取分页列表
	 *
	 * @param  int $id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getSellerWxMenuList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{	
		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}
}

?>