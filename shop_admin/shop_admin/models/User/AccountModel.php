<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class User_AccountModel extends User_Account
{
	/**
	 * 读取分页列表
	 *
	 * @param  int $user_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getAccountList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		$data = $this->listByWhere($cond_row, $order_row, $page, $rows);
		
		foreach ($data['items'] as $k => $item)
		{
			unset($item['user_password']);
			unset($item['user_key']);
			$item['id']        = $item['user_id'];
			$item['name']      = $item['user_account'];
			$data['items'][$k] = $item;
		}
		
		return $data;
	}

}

?>