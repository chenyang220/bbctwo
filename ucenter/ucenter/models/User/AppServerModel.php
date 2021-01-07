<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class User_AppServerModel extends User_AppServer
{

	/**
	 * 读取分页列表
	 *
	 * @param  int $user_name 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getAppServerList($user_name = null, $page=1, $rows=100, $sort='asc')
	{
		//需要分页如何高效，易扩展
		$offset = $rows * ($page - 1);

		$this->sql->setLimit($offset, $rows);

		$user_name_row = array();
		$user_name_row = $this->selectKeyLimit();

		//读取主键信息
		$total = $this->getFoundRows();

		$data_rows = array();

		if ($user_name_row)
		{
			$data_rows = $this->getAppServer($user_name_row);
		}

		$data = array();
		$data['page'] = $page;
		$data['total'] = ceil_r($total / $rows);  //total page
		$data['totalsize'] = $data['total'];
		$data['records'] = count($data_rows);
		$data['items'] = array_values($data_rows);

		return $data;
	}


	public function getUserAppServerByCondition($condition)
	{
		foreach ($condition as $key=>$value)
		{
			$this->sql->setWhere($key, $value);
		}

		return $this->getAppServer('*');

	}

}
?>