<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/15
 * Time: 17:57
 * @author fuzhehao
 */
class Plus_User extends Yf_Model
{

    //PLUS会员状态
    public static $user_status = array(
        1=> '1',//试用
        2=> '2',//正式会员
        3=> '3'//过期会员
    );
	public $_cacheKeyPrefix  = 'c|plus_user|';
	public $_cacheName       = 'plus';
	public $_tableName       = 'plus_user';
	public $_tablePrimaryKey = 'user_id';

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
	 * 根据主键值，从数据库读取数据
	 *
	 * @param  int $points_log_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getLog($id = null, $sort_key_row = null)
	{
		$rows = array();
		$rows = $this->get($id, $sort_key_row);

		return $rows;
	}

	/**
     *
     * 新增PLUS会员
     */
	public function addPlusUser($field_row, $return_insert_id = false){
        $add_flag = $this->add($field_row, $return_insert_id);
        return $add_flag;
    }

}