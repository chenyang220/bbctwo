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
class Distribution_DistributionShop extends Yf_Model
{
	public $_cacheKeyPrefix  = 'c|distribution_shop|';
	public $_cacheName       = 'shop';
	public $_tableName       = 'distribution_shop';
	public $_tablePrimaryKey = 'distribution_shop_id';

	/**
	 * @param string $user User Object
	 * @var   string $db_id 指定需要连接的数据库Id
	 * @return void
	 */
	public function __construct(&$db_id = 'shop', &$user = null)
	{
		$this->_tableName = TABEL_PREFIX . $this->_tableName;
		$this->_cacheFlag = CHE;
		parent::__construct($db_id, $user);
	}

	/**
	 * 根据主键更新表内容
	 * @param mix $distribution_shop_id 主键
	 * @param array $field_row key=>value数组
	 * @return bool $update_flag 是否成功
	 * @access public
	 */
	public function editBase($distribution_shop_id = null, $field_row, $flag = false)
	{
		$update_flag = $this->edit($distribution_shop_id, $field_row, $flag);

		return $update_flag;
	}

	/**
	 * 插入
	 * @param array $field_row 插入数据信息
	 * @param bool $return_insert_id 是否返回inset id
	 * @param array $field_row 信息
	 * @return bool  是否成功
	 * @access public
	 */
	public function addBase($field_row, $return_insert_id = false)
	{
		$add_flag = $this->add($field_row, $return_insert_id);
		return $add_flag;
	}
}
?>