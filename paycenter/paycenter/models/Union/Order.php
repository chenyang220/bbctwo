<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * 
 * 
 * @category   Framework
 * @package    __init__
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010 远丰仁商
 * @version    1.0
 * @todo       
 */
class Union_Order extends Yf_Model
{
    public $_cacheKeyPrefix  = 'c|union_order|';
    public $_cacheName       = 'union';
    public $_tableName       = 'union_order';
    public $_tablePrimaryKey = 'union_order_id';
    public $jsonKey = array('notify_data');

    /**
     * @param string $user  User Object
     * @var   string $db_id 指定需要连接的数据库Id
     * @return void
     */
    public function __construct(&$db_id='paycenter', &$user=null)
    {
        $this->_tableName = TABEL_PREFIX . $this->_tableName;
		$this->_cacheFlag = CHE;
        parent::__construct($db_id, $user);
    }

    /**
     * 根据主键值，从数据库读取数据
     *
     * @param  int   $user_id  主键值
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getUnionOrder($union_order_id=null, $sort_key_row=null)
    {
        $rows = array();
        $rows = $this->get($union_order_id, $sort_key_row);

        return $rows;
    }

    /**
     * 插入
     * @param array $field_row 插入数据信息
     * @param bool  $return_insert_id 是否返回inset id
     * @param array $field_row 信息
     * @return bool  是否成功
     * @access public
     */
    public function addUnionOrder($field_row, $return_insert_id=false)
    {
        $add_flag = $this->add($field_row, $return_insert_id);

        //$this->removeKey($user_id);
        return $add_flag;
    }

    /**
     * 根据主键更新表内容
     * @param mix   $user_id  主键
     * @param array $field_row   key=>value数组
     * @return bool $update_flag 是否成功
     * @access public
     */
    public function editUnionOrder($union_order_id=null, $field_row,$flag = false)
    {
        $update_flag = $this->edit($union_order_id, $field_row,$flag);

        return $update_flag;
    }

    /**
     * 更新单个字段
     * @param mix   $user_id
     * @param array $field_name
     * @param array $field_value_new
     * @param array $field_value_old
     * @return bool $update_flag 是否成功
     * @access public
     */
    public function editUnionOrderSingleField($union_order_id, $field_name, $field_value_new, $field_value_old)
    {
        $update_flag = $this->editSingleField($union_order_id, $field_name, $field_value_new, $field_value_old);

        return $update_flag;
    }    
    
    /**
     * 删除操作
     * @param int $user_id
     * @return bool $del_flag 是否成功
     * @access public
     */
    public function removeUnionOrder($union_order_id)
    {
        $del_flag = $this->remove($union_order_id);

        //$this->removeKey($user_id);
        return $del_flag;
    }

    /**
     * PLus会员开通业务支付成功后,更新订单状态
     * @param  array $order_id
     * @return bool  处理结果
     * @access public
     */
    public function dealPlusOrder($order_id = null,$pay_name=null,$pay_code=null)
    {
        $rs_row = array();
        //修改合并订单的状态
        $Union_OrderModel = new Union_OrderModel();
        $union_order = $Union_OrderModel->getOne($order_id);
        if(!$union_order) return false;
        if($union_order['order_state_id'] == Order_StateModel::ORDER_PAYED)
        {
            return true;
        }
        $Union_OrderModel->sql->startTransactionDb();
        $edit_row = array();
        $edit_row['order_state_id'] = Order_StateModel::ORDER_PAYED;
        $p_time =  time();
        $edit_row['pay_time'] = date('Y-m-d H:i:s',$p_time);
        $result = $Union_OrderModel->editUnionOrder($order_id,$edit_row);
        check_rs($result, $rs_row);
        //修改交易明细中的订单状态
        $Consume_RecordModel = new Consume_RecordModel();
        $record_row = $Consume_RecordModel->getByWhere(array('order_id'=> $order_id));
        $record_id_row = array_column($record_row,'consume_record_id');
        $edit_consume_record['record_status'] = RecordStatusModel::RECORD_FINISH;
        $edit_consume_record['record_payorder'] = $order_id;
        $edit_consume_record['record_paytime'] = date('Y-m-d H:i:s');
        $result = $Consume_RecordModel->editRecord($record_id_row,$edit_consume_record);
        check_rs($result, $rs_row);
        if (is_ok($rs_row))
        {
            //远程改变订单状态
            $key      = Yf_Registry::get('shop_api_key');
            $url         = Yf_Registry::get('shop_api_url');
            $app_id = Yf_Registry::get('shop_app_id');
            $formvars = array();
            $formvars['app_id'] = $app_id;
            $formvars['order_id'] = $order_id;
            $formvars['user_id'] = $union_order['buyer_id'];
            $formvars['pay_time'] = $p_time;
            $pay_name && $formvars['pay_name'] = $pay_name;
            $pay_code && $formvars['pay_code'] = $pay_code;
            $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Trade_Order&met=editPlusOrderStatus&typ=json', $url), $formvars);
            if($rs['status']==200){
                $Union_OrderModel->sql->commit();
                return true;
            }
        }
        $Union_OrderModel->sql->rollBackDb();
        return false;

    }
}
?>