<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

class Plus_UserOrder extends Yf_Model
{

    // 订单支付状态
    public static $pay_status =array(
        '1'=>'1',//未支付
        '2'=>'2'//已支付
    );
    public $_cacheKeyPrefix  = 'c|plus_user_order|';
    public $_cacheName       = 'plus';
    public $_tableName       = 'plus_user_order';
    public $_tablePrimaryKey = 'user_order_id';

    /**
     * Plus_UserOrder constructor.
     * @param string $db_id
     * @param null $user
     *
     */
    public function __construct(&$db_id = 'shop', &$user = null)
    {
        $this->_tableName = TABEL_PREFIX . $this->_tableName;
        $this->_cacheFlag = CHE;
        parent::__construct($db_id, $user);
    }


    /**
     *
     * 创建PLUS会员订单
     */
    public function addPlusUserOrder($field_row, $return_insert_id = false){
        return  $this->add($field_row, $return_insert_id);
    }

    /**
     * @param null $order_id
     * @param $field_row
     * @param bool $flag
     * @return bool修改PLUS会员订单
     */
    public function editPlusUserOrder($order_id = null, $field_row, $flag = false)
    {
        return  $this->edit($order_id, $field_row, $flag);
    }


}