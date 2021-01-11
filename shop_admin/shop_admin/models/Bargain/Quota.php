<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/20
 * Time: 15:42
 */
class Bargain_Quota extends Yf_Model
{
	public $_cacheKeyPrefix  = 'c|bargain_combo|';
	public $_cacheName       = 'bargain';
	public $_tableName       = 'bargain_combo';
	public $_tablePrimaryKey = 'combo_id';

	/**
	 * @param string $user User Object
	 * @var   string $db_id 指定需要连接的数据库Id
	 * @return void
	 */
	public function __construct(&$db_id = 'shop_admin', &$user = null)
	{
		$this->_tableName = Yf_GeneralOperator::getInstance()->shopTablePerfix() . $this->_tableName;
		$this->_cacheFlag = CHE;
		parent::__construct($db_id, $user);
	}


}