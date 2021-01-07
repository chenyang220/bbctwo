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
 * @copyright  Copyright (c) 2010, 朱羽婷
 * @version    1.0
 * @todo
 */
class ShopInvoice extends Yf_Model
{
	public $_cacheKeyPrefix  = 'c|Shop_Invoice|';
	public $_cacheName       = 'shop_invoice';
	public $_tableName       = 'shop_invoice';
	public $_tablePrimaryKey = 'invoice_id';
	public $use_status;
	public $invoice_state;

	/**
	 * @param string $user User Object
	 * @var   string $db_id 指定需要连接的数据库Id
	 * @return void
	 */
	public function __construct(&$db_id = 'shop', &$user = null)
	{
		$this->_tableName = TABEL_PREFIX . $this->_tableName;
		parent::__construct($db_id, $user);
        $this->use_status = array(
            '1' => __('暂停'),
            '2' => __('启用'),
        );
        $this->invoice_state = array(
            '1' => __('普通发票'),
            '2' => __('电子发票'),
            '3' => __('增值税发票'),
        );
	}

	/**
	 * @param  int $config_key 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getInvoice($invoice_id = null, $sort_key_row = null)
	{
		$rows = array();
		$rows = $this->get($invoice_id, $sort_key_row);
		return $rows;
	}

	/**
	 * 插入
	 * @param array $field_row 插入数据信息
	 * @param bool $return_insert_id 是否返回inset id
	 * @param array $field_row 信息
	 * @return bool  是否成功
	 * @access public
	 */
	public function addInvoice($field_row,$flag = false)
	{
		$add_flag = $this->add($field_row,$flag);

		//$this->removeKey($config_key);
		return $add_flag;
	}

	/**
	 * 根据主键更新表内容
	 * @param mix $config_key 主键
	 * @param array $field_row key=>value数组
	 * @return bool $update_flag 是否成功
	 * @access public
	 */
	public function editInvoice($invoice_id = null, $field_row = array())
	{
		$update_flag = $this->edit($invoice_id, $field_row);

		return $update_flag;
	}

	/**
	 * 更新单个字段
	 * @param mix $config_key
	 * @param array $field_name
	 * @param array $field_value_new
	 * @param array $field_value_old
	 * @return bool $update_flag 是否成功
	 * @access public
	 */
	public function editInvoiceSingleField($invoice_id, $company)
	{
		$update_flag = $this->editSingleField($invoice_id, $company);

		return $update_flag;
	}

	/**
	 * 删除操作
	 * @param int $config_key
	 * @return bool $del_flag 是否成功
	 * @access public
	 */
	public function removeInvoice($invoice_id)
	{
		$del_flag = $this->remove($invoice_id);

		//$this->removeKey($config_key);
		return $del_flag;
	}

    /**
     * 获取店铺发票模板列表
     * @param  array $cond_row 查询条件
     * @param  array $order_row 排序信息
     * @param  array $page 当前页码
     * @param  array $rows 每页记录数
     * @return array $data 返回的查询内容
     * @access public
     */
    public function getInvoiceList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);
        if (is_array($data['items'])){
            foreach ($data['items'] as $key => $val){
                $data['items'][$key]['is_use']  = $this->use_status[$val['is_use']];
                $data['items'][$key]['invoice_state']  = $this->invoice_state[$val['invoice_state']];
            }
        }

        return $data;
    }

    //获取所有用户id
    public function getAllInvoice()
    {
        $sql = "SELECT
						invoice_id,is_use
					FROM
						" . TABEL_PREFIX . "shop_invoice
					";
        $rows = $this->sql->getAll($sql);

        return $rows;
    }
}

?>