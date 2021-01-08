<?php

/**
 * Created by PhpStorm.
 * User: rd04
 * Date: 2017/11/13
 * Time: 15:43
 */
class Special_Column extends Yf_Model
{
    public $_cacheKeyPrefix  = 'c|special_column|';
    public $_cacheName       = 'column';
    public $_tableName       = 'special_column';
    public $_tablePrimaryKey = 'special_column_id';
    public $jsonKey = array('special_column_image','goods_common');

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
     * @param  int $config_key 主键值
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getBase($id = null, $sort_key_row = null)
    {
        $rows = array();
        $rows = $this->get($id, $sort_key_row);

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
    public function addBase($field_row, $return_insert_id = false)
    {
        $add_flag = $this->add($field_row, $return_insert_id);

        return $add_flag;
    }

    /**
     * 根据主键更新表内容
     * @param mix $config_key 主键
     * @param array $field_row key=>value数组
     * @return bool $update_flag 是否成功
     * @access public
     */
    public function editBase($id = null, $field_row, $flag = false)
    {
        $update_flag = $this->edit($id, $field_row, $flag);

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
    public function editBaseSingleField($id, $field_name, $field_value_new, $field_value_old)
    {
        $update_flag = $this->editSingleField($id, $field_name, $field_value_new, $field_value_old);

        return $update_flag;
    }

    /**
     * 删除操作
     * @param int $config_key
     * @return bool $del_flag 是否成功
     * @access public
     */
    public function removeBase($id)
    {
        $del_flag = $this->remove($id);
        return $del_flag;
    }

    public function getColumnInfo($cond)
    {
        $info = $this->getOneByWhere($cond);
        $cond = array();
        $cond['common_id:IN'] = $info['goods_common'];
        $Goods_CommonModel = new Goods_CommonModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $Discount_GoodsModel = new Discount_GoodsModel();
        $common_info = $Goods_CommonModel->getByWhere($cond);
        $common_id_arr = array_column($common_info, 'common_id', 'common_id');
        $goods_detail = $Goods_BaseModel->getByWhere(array("common_id :IN"=>$common_id_arr));
        $goods_id_arr = array_column($goods_detail, 'goods_id', 'common_id');
        $goods_list = array();

        foreach($common_info as $k=>$v)
        {
            $Goods_Base = $Goods_BaseModel->getByWhere(array('common_id'=>$v['common_id']));
            $goods_id_arr = array_column($Goods_Base, 'common_id','goods_id');
            $goods_price_arr = array_column($Goods_Base, 'goods_price');
            $goods_price_min = '';
            foreach ($goods_price_arr as $key => $goods_price) {
                if ($goods_price_min == '' || ($goods_price_min && $goods_price_min > $goods_price)) {
                    $goods_price_min = $goods_price;
                }
            }
            $Discount_Goods = array();
            $discount_price = '';
            foreach ($goods_id_arr as $goods_id => $common_id) {
                $Discount_Goods = $Discount_GoodsModel->getOneByWhere(array('discount_goods_state'=>1,'goods_id'=>$goods_id,'common_id'=>$common_id));
                if ($Discount_Goods) { 
                    if (empty($discount_price) || (!empty($discount_price) && $discount_price > $Discount_Goods['discount_price'])) {
                        $discount_price = $Discount_Goods['discount_price'];
                    }
                }
            }
            $goods_list[$k]['discount_price'] = $discount_price; 
            $goods_list[$k]['goods_id'] = $v['common_id'];
            $goods_list[$k]['goods_name'] = $v['common_name'];
            $goods_list[$k]['goods_image'] = $v['common_image'];
            $goods_list[$k]['goods_price'] = $goods_price_min;
        }
        $info['goods_common'] = array_values($goods_list);
        return $info;
    }
}