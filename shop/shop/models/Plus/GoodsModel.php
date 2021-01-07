<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}


class Plus_GoodsModel extends Plus_Goods
{
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
                SQL_CALC_FOUND_ROWS c.goods_id,c.common_id,c.common_price,c.common_name,c.common_image, pg.*
            FROM
                " . TABEL_PREFIX . "plus_goods pg  JOIN " . TABEL_PREFIX
                  . "goods_common c  ON c.common_id = pg.goods_common_id ";
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
		$where.= " AND c.common_is_plus=".self::COMMON_IS_PLUS_YES." AND pg.shop_id = '{$shop_id}' AND pg.is_del = ".self::IS_DEL_NO;
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


    /**
     * 计算商品列表商品plus价格
     *
     * @param  int    $common_id 主键值
     * @param  string $type      SKU  SPU
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
	public function reformPlusGoods($goods_row = array())
    {
        //判断平台是否开启了plus功能
        $plus_switch = Web_ConfigModel::value('plus_switch');

        //获取平台设置的plus会员折扣
        $plus_rate = Web_ConfigModel::value("plus_rate");

        //获取所有的common_id
        $goods_common_id = array_column($goods_row,'common_id');

        //判断这些common中哪些是plus商品
        $sql = 'SELECT goods_common_id FROM ' . TABEL_PREFIX .'plus_goods WHERE 1=1 and is_del=0 and  goods_common_id IN ('. implode(', ', $goods_common_id) . ')';
        $plus_common = $this -> sql -> getAll($sql);
        $plus_common_id = array_column($plus_common,'goods_common_id');
        foreach ($goods_row as $key => $val) {
            $price = $val['g_price'] ? $val['g_price'] : $val['common_price'];
            if(in_array($val['common_id'],$plus_common_id) && $plus_switch) {
                //计算商品的plus价格
                $goods_row[$key]['plus_status'] = 1;
                $goods_row[$key]['plus_price'] = number_format(($price * $plus_rate / 100), 2, '.', '');
            } else {
                $goods_row[$key]['plus_status'] = 0;
                $goods_row[$key]['plus_price'] = $price;
            }
        }
        return $goods_row;

    }

    /**
     * 计算单件商品plus价格
     *
     * @param  int    $common_id 主键值
     * @param  string $type      SKU  SPU
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getGoodsPlusPrice($goods_common_id = '',$goods_price = 0)
    {
        //判断平台是否开启了plus功能
        $plus_switch = Web_ConfigModel::value('plus_switch');

        //获取平台设置的plus会员折扣
        $plus_rate = Web_ConfigModel::value("plus_rate");

        //判断该商品是否是plus商品
        $sql = 'SELECT * FROM ' . TABEL_PREFIX .'plus_goods WHERE 1=1 and  goods_common_id ='.$goods_common_id." and is_del = ".self::IS_DEL_NO;
        $plus_common = $this -> sql -> getAll($sql);

        $data = array();
        if(current($plus_common) && $plus_switch) {
            $data['plus_status'] = 1;
            $data['plus_price'] = number_format(($goods_price * $plus_rate / 100), 2, '.', '');
        } else {
            $data['plus_status'] = 0;
            $data['plus_price'] = $goods_price;
        }

        return $data;

    }


    /**
     *
     * PLUS会员首页展示12条商品
     */
    public function showPlusGoods($keyword='',$page = 1, $rows = 12){

        $sql = "
			SELECT
				SQL_CALC_FOUND_ROWS c.goods_id,c.common_id,c.common_price,c.common_name,c.common_image, pg.*
			FROM
				" . TABEL_PREFIX . "plus_goods pg LEFT  JOIN " . TABEL_PREFIX
            . "goods_common c  ON c.common_id = pg.goods_common_id ";
        $offset = $rows * ($page - 1);
        $keyword && $this->sql->setWhere('c.common_name',"%{$keyword}%", 'LIKE' );
        $this -> sql -> setLimit($offset, $rows);
        $this -> sql -> setOrder('pg.' .'create_time', 'DESC');
        $limit = $this -> sql -> getLimit();
        $where = $this -> sql -> getWhere();
        $order = $this -> sql -> getOrder();
        $where.= "AND c.common_is_plus=".self::COMMON_IS_PLUS_YES ." AND pg.is_del = ".self::IS_DEL_NO;
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
        return $common_data?:array();
    }

    //正在、即将参加plus的商品common_id
    public function isPlusGoodsCommonId()
    {
        $cond_row = array();
        $cond_row['is_del'] = 0;//未删除
        $cond_row['shop_id'] = Perm::$shopId;
        $data = $this->getByWhere($cond_row);
        $goods_common_id = array_column($data,'goods_common_id');
        return $goods_common_id;
    }

    //正在参加plus商品的对应goods_id
    public function getPlusGoodsId()
    {
        $cond_row = array();
        $cond_row['is_del'] = 0;//未删除
        $cond_row['shop_id'] = Perm::$shopId;
        $data = $this->getByWhere($cond_row);
        $goods_common_id = array_column($data, 'goods_common_id');
        if($goods_common_id){
            $goods_cond = array();
            $goods_cond['common_id:IN'] = $goods_common_id;
            $goods_cond['is_del'] = 1;//未删除
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_list = $Goods_BaseModel->getByWhere($goods_cond);
            if ($goods_list) {
                $goods_ids = array_column($goods_list, 'goods_id');
            } else {
                $goods_ids = array();
            }
        }else{
            $goods_ids = array();
        }
        return $goods_ids;
    }

    //筛选已经参加活动的商品common_id
    public function getActiveCommonId()
    {
        //正在参加所有活动商品的goods_id
        $Bargain_BaseModel = new Bargain_BaseModel();
        $active_goods_ids = $Bargain_BaseModel->getActiveGoodsId();

        $Goods_BaseModel = new Goods_BaseModel();
        $goods_cond = array();
        $goods_cond['goods_id:IN'] = $active_goods_ids;
        $goods_cond['is_del'] = 1;//未删除
        $goods_list = $Goods_BaseModel->getByWhere($goods_cond);
        if ($goods_list) {
            $active_common_ids = array_column($goods_list, 'common_id');
        } else {
            $active_common_ids = array();
        }
        return array_unique($active_common_ids);
    }

    /**
     * 获取店铺PLUS商品集合
     * @nsy 2019-04-01
     */
    public function getSellerShopPlusGoodsList($map=true){
        //筛选条件
        $cond_row =  array(
            'shop_id' => Perm::$shopId,
            'is_del' => '0',//未删除标识
        );
        $data = $this->getByWhere($cond_row);
        $data && $data = array_column($data,'goods_common_id');
        $new  = array();
        if($map && $data){
             array_walk($data, function($value, $key) use (&$data ,&$new){
                $new[$value] = $value;
            });
            $data = $new;
        }
        return $data;
    }


}