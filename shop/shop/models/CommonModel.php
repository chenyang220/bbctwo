<?php 
if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 *
 * 公共数据库操作类，做跳板用
 * @nsy 2020-03-23
 */
class CommonModel extends Yf_Model
{
	public $_cacheKeyPrefix  = '';
	public $_cacheName       = '';
	public $_tableName       = '';
	public $_tablePrimaryKey = '';

	/**
	 * @param string $user User Object
	 * @var   string $db_id 指定需要连接的数据库Id
	 * @return void
	 */
	public function __construct(&$db_id = 'shop', &$user = null,$data = array())
	{
		if($data){
			$this->setTableBasic($data);
		}
		$this->_tableName  && $this->_tableName = TABEL_PREFIX . $this->_tableName;
		parent::__construct($db_id, $user);
	}
	
	/**
	*
	*动态设置表基础信息
	**/
	public function setTableBasic($data){
		if(isset($data['cacheKeyPrefix'])  && $data['cacheKeyPrefix']){
			$this->_cacheKeyPrefix = $data['cacheKeyPrefix'];
		}
		if(isset($data['cacheName'])  && $data['cacheName']){
			$this->_cacheName = $data['cacheName'];
		}
		if(isset($data['tableName'])  && $data['tableName']){
			$this->_tableName = $data['tableName'];
		}
		if(isset($data['tablePrimaryKey'])  && $data['tablePrimaryKey']){
			$this->_tablePrimaryKey = $data['tablePrimaryKey'];
		}
	}
}

?>