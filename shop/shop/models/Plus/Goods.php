<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}


class Plus_Goods extends Yf_Model
{
	//PLUS商品标记：0-未参加 1-已参加
	const COMMON_IS_PLUS_YES = 1;
	const COMMON_IS_PLUS_NO = 0;
	
	//PLUS商品删除标识：0，未删除；1，已删除
	const IS_DEL_NO = 0;
	const IS_DEL_YES = 1;
	public $_cacheKeyPrefix  = 'c|plus_goods|';
	public $_cacheName       = 'goods';
	public $_tableName       = 'plus_goods';
	public $_tablePrimaryKey = 'plus_id';

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
     * 插入
     * @param array $field_row 插入数据信息
     * @param bool $return_insert_id 是否返回inset id
     * @param array $field_row 信息
     * @return bool  是否成功
     * @access public
     */
    public function insertPlusGoods($field_row, $return_insert_id = false)
    {
        $add_flag = $this->add($field_row, $return_insert_id);
        return $add_flag;
    }

    /**
     * 修改goods_common  Plus 标识
     * @param mix $common_id 主键
     * @param array $field_row key=>value数组
     * @return bool $update_flag 是否成功
     * @access public
     */
    public function editPlusGoods($common_id = null, $field_row, $flag = false)
    {
        $update_flag = $this->edit($common_id, $field_row, $flag);

        return $update_flag;
    }

}