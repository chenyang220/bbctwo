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
class Seckill_Goods extends Yf_Model
{

	public $_cacheKeyPrefix  = 'c|seckill_goods|';
	public $_cacheName       = 'seckill';
	public $_tableName       = 'seckill_goods';
	public $_tablePrimaryKey = 'seckill_goods_id';

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


}