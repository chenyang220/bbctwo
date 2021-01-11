<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * 
 * 
 * @category   Framework
 * @package    __init__
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2016远丰仁商
 * @version    1.0
 * @todo       
 */
class WxPublic_Message extends Yf_Model
{
    public $_cacheKeyPrefix  = 'c|wxpublic_message|';
    public $_cacheName       = 'mb';
    public $_tableName       = 'wxpublic_message';
    public $_tablePrimaryKey = 'id';

    /**
     * @param string $user  User Object
     * @var   string $db_id 指定需要连接的数据库Id
     * @return void
     */
    public function __construct(&$db_id='shop', &$user=null)
    {
        $this->_tableName = TABEL_PREFIX . $this->_tableName;
 		$this->_cacheFlag        = CHE;
        parent::__construct($db_id, $user);
    }

    public function addPublicMsg($field_row, $return_insert_id = false){
        $add_flag = $this->add($field_row, $return_insert_id);
        return $add_flag;
    }

    public function editPublicMsg($id = null, $field_row, $flag = false)
    {
        return  $this->edit($id, $field_row, $flag);
    }
    public function removeBase($id)
    {
        $del_flag = $this->remove($id);
        return $del_flag;
    }

}
?>