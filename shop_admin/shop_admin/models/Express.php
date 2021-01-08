<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 *
 *
 * @category   Framework
 * @package    __init__
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010远丰仁商
 * @version    1.0
 * @todo
 */
class Express extends Yf_Model
{
	public $_cacheKeyPrefix  = 'c|Express|';
	public $_cacheName       = 'express';
	public $_tableName       = 'express';
	public $_tablePrimaryKey = 'express_id';

	/**
	 * @param string $user User Object
	 * @var   string $db_id 指定需要连接的数据库Id
	 * @return void
	 */
	public function __construct(&$db_id = 'shop_admin', &$user = null)
	{
		$this->_tableName = Yf_GeneralOperator::getInstance()->shopTablePerfix() . $this->_tableName;
		parent::__construct($db_id, $user);
	}

	/**
	 * @param  int $config_key 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getExpress($express_id = null, $sort_key_row = null)
	{
		$rows = array();
		$rows = $this->get($express_id, $sort_key_row);
		return $rows;
	}

	/**
	 * 插入
	 * @param array $field_row 插入数据信息
	 * @param bool $return_insert_id 是否返回inset id
	 * @param array $field_row 信息
	 * @return bool  是否成功
	 * @access public
	 */
	public function addExpress($field_row)
	{
		$add_flag = $this->add($field_row);

		//$this->removeKey($config_key);
		return $add_flag;
	}

	/**
	 * 根据主键更新表内容
	 * @param mix $config_key 主键
	 * @param array $field_row key=>value数组
	 * @return bool $update_flag 是否成功
	 * @access public
	 */
	public function editExpress($express_id = null, $field_row = array())
	{
		$update_flag = $this->edit($express_id, $field_row);

		return $update_flag;
	}

	/**
	 * 更新单个字段
	 * @param mix $config_key
	 * @param array $field_name
	 * @param array $field_value_new
	 * @param array $field_value_old
	 * @return bool $update_flag 是否成功
	 * @access public
	 */
	public function editExpressSingleField($express_id, $company)
	{
		$update_flag = $this->editSingleField($express_id, $company);

		return $update_flag;
	}

	/**
	 * 删除操作
	 * @param int $config_key
	 * @return bool $del_flag 是否成功
	 * @access public
	 */
	public function removeExpress($express_id)
	{
		$del_flag = $this->remove($express_id);

		//$this->removeKey($config_key);
		return $del_flag;
	}
}

?>