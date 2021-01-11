<?php if (!defined('ROOT_PATH')) {
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
class Information_NewsClassModel extends Yf_Model
{
    public $_cacheKeyPrefix = 'c|information_newsclass|';
    public $_cacheName = 'information';
    public $_tableName = 'information_newsclass';
    public $_tablePrimaryKey = 'id';
    
    /**
     * @param string $user  User Object
     *
     * @var   string $db_id 指定需要连接的数据库Id
     * @return void
     */
    public function __construct(&$db_id = 'shop', &$user = null)
    {
        $this->_tableName = TABEL_PREFIX . $this->_tableName;
        $this->_cacheFlag = CHE;
        parent::__construct($db_id, $user);
    }
    
    public function typelist()
    {
        $rows=[
            // ['id'=>1,'typename'=>'用户'],
            ['id' => 2, 'typename' => '店铺'],
            ['id' => 3, 'typename' => '平台']
        ];
        return $rows;
    }
    
    /**
     * 根据主键值，从数据库读取数据
     *
     * @param  int $config_key 主键值
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getClass($config_key = null, $sort_key_row = null)
    {
        $rows = [];
        $rows = $this->get($config_key, $sort_key_row);
        
        return $rows;
    }
    
    /**
     * 插入
     *
     * @param array $field_row        插入数据信息
     * @param bool  $return_insert_id 是否返回inset id
     * @param array $field_row        信息
     *
     * @return bool  是否成功
     * @access public
     */
    public function addClass($field_row, $return_insert_id = false)
    {
        $add_flag = $this->add($field_row, $return_insert_id);
        return $add_flag;
    }
    
    /**
     * 根据主键更新表内容
     *
     * @param mix   $config_key 主键
     * @param array $field_row  key=>value数组
     *
     * @return bool $update_flag 是否成功
     * @access public
     */
    public function editClass($config_key = null, $field_row)
    {
        $update_flag = $this->edit($config_key, $field_row);
        
        return $update_flag;
    }
    
    /**
     * 更新单个字段
     *
     * @param mix   $config_key
     * @param array $field_name
     * @param array $field_value_new
     * @param array $field_value_old
     *
     * @return bool $update_flag 是否成功
     * @access public
     */
    public function editClassSingleField($config_key, $field_name, $field_value_new, $field_value_old)
    {
        $update_flag = $this->editSingleField($config_key, $field_name, $field_value_new, $field_value_old);
        
        return $update_flag;
    }
    
    /**
     * 删除操作
     *
     * @param int $config_key
     *
     * @return bool $del_flag 是否成功
     * @access public
     */
    public function removeClass($config_key)
    {
        $del_flag = $this->remove($config_key);
        
        //$this->removeKey($config_key);
        return $del_flag;
    }

    //多条件获取主键
    public function getClassId($cond_row = array(), $order_row = array())
    {
        return $this->getKeyByMultiCond($cond_row, $order_row);
    }

    public function listClassWhere($cond_row = [], $order_row = [], $page = 1, $rows = 100)
    {
        return $this->listByWhere($cond_row, $order_row, $page, $rows);
    }
}

?>