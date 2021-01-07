<?php 
	if (!defined('ROOT_PATH')) {
		exit('No Permission');
	}
    /**
     * @author     nsy <tech134@yuanfeng.cn>
	 * @date       2019-01-08 11:30
     */
    class Goods_PlusGoodsModel extends Yf_Model
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
		public function __construct(&$db_id = 'shop_admin', &$user = null)
		{
			$this->_tableName = yf_GeneralOperator::getInstance()->shopTablePerfix() . $this->_tableName;
			$this->_cacheFlag = CHE;
			parent::__construct($db_id, $user);
		}
        
        /**
         * 读取商品,
         *
         * @param  int    $common_id 主键值
         * @param  string $type      SKU  SPU
         *
         * @return array $rows 返回的查询内容
         * @access public
         */
        public function getPlusGoodsList($cond_row = array(), $order_row = array(), $page = 1, $rows = 10)
        {
			
			$cond_arr = array();
			foreach($cond_row as $k=>$v){
				if($k=='shop_id'){
					$shop_id = $v;
					continue;
				}
				$cond_arr[$k] = $v;
			}
			$sql = "
				SELECT
					SQL_CALC_FOUND_ROWS c.common_price,c.common_name,c.common_image, pg.*
				FROM
					" . yf_GeneralOperator::getInstance()->shopTablePerfix() . "plus_goods pg LEFT OUTER JOIN " . yf_GeneralOperator::getInstance()->shopTablePerfix()
					  . "goods_common c  ON c.common_id = pg.goods_common_id AND c.common_is_plus=".self::COMMON_IS_PLUS_YES." AND pg.is_del = ".self::IS_DEL_NO." AND pg.shop_id = '{$shop_id}'";;
			//需要分页如何高效，易扩展
			$offset = $rows * ($page - 1);
			$this -> sql -> setLimit($offset, $rows);
            if ($cond_arr) {
                foreach ($cond_arr as $k => $v){
                    $k_row = explode(':', $k);
                    if (count($k_row) > 1) {
                        $this -> sql -> setWhere('c.' . $k_row[0], $v, $k_row[1]);
                    } else {
                        $this -> sql -> setWhere('c.' . $k, $v);
                    }
                }
            }

			if ($order_row) {
				foreach ($order_row as $k => $v) {
					$this -> sql -> setOrder('pg.' . $k, $v);
				}
			}
			$limit = $this -> sql -> getLimit();
			$where = $this -> sql -> getWhere();
			$order = $this -> sql -> getOrder();
			$sql = $sql . $where . $order . $limit;
			$common_rows = $this -> sql -> getAll($sql);
			$total = $this -> getFoundRows();
			$common_data = array();
			$common_data['page'] = $page;
			$common_data['total'] = ceil_r($total / $rows);
			$common_data['totalsize'] = $total;
			$common_data['records'] = count($common_rows);
			$common_data['items'] = $common_rows;
            $common_data['items'] = array_values($common_rows);
            return $common_data;
        }
        
       

    }

?>
