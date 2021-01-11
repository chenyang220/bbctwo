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
 * @copyright  Copyright (c) 
 * @version    1.0
 * @todo
 */
class SettlementIncome extends Yf_Model
{
    public $_cacheKeyPrefix  = 'c|settlement_income|';
    public $_cacheName       = 'settlement_income';
    public $_tableName       = 'settlement_income';
    public $_tablePrimaryKey = 'income_id';

    /**
     * @param string $user User Object
     * @var   string $db_id 指定需要连接的数据库Id
     * @return void
     */
    public function __construct(&$db_id = 'shop', &$user = null)
    {
        $this->_tableName = TABEL_PREFIX . $this->_tableName;
        parent::__construct($db_id, $user);
    }

    /**
	 * 插入
	 * @param array $field_row 插入数据信息
	 * @param bool $return_insert_id 是否返回inset id
	 * @param array $field_row 信息
	 * @return bool  是否成功
	 * @access public
	 */
	public function addSettlementIncome($field_row, $return_insert_id = false)
	{
		$add_flag = $this->add($field_row, $return_insert_id);

		//$this->removeKey($config_key);
		return $add_flag;
	}

}
?>