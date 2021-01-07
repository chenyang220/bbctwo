<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/20
 * Time: 15:44
 */
class GroupBuy_BaseModel extends GroupBuy_Base
{
	const WILLSTART    = 0; //审核通过，但未到开始时间，即将开始
	const UNDERREVIEW  = 1;  //审核中
	const NORMAL       = 2;  //正常
	const FINISHED     = 3;  //结束
	const AUDITFAILUER = 4; //审核失败
	const CLOSED       = 5; //管理员关闭


	const ONLINEGBY = 1;  //线上团
	const VIRGBY    = 2;  //虚拟团

	const UNRECOMMEND        = 0;   //不推荐
	const RECOMMEND          = 1;   //首页推荐
	const HIGHLYRECOMMEND   = 2;   //大图推荐

	public $Goods_CommonModel = null;
	//团购状态 1.未发布 2.已取消 3.正常 4.已完成 5.已结束'
	public static $groupbuy_state_map = array(
		self::UNDERREVIEW => '审核中',
		self::NORMAL => '正常',
		self::FINISHED => '结束',
		self::AUDITFAILUER => '审核失败',
		self::CLOSED => '管理员关闭',
		self::WILLSTART => '即将开始'
	);

	//团购商品推荐状态 0.否 1.是'
	public static $recommend_map = array(
		self::UNRECOMMEND => '否',
		self::RECOMMEND => '首页推荐',
		self::HIGHLYRECOMMEND => '首页大图推荐'
	);

	//团购商品类型 1-实物，2-虚拟商品
	public static $goods_type_map = array(
		self::ONLINEGBY => '实物',
		//线上团
		self::VIRGBY => '虚拟商品'
		//虚拟团
	);

	public $htmlKey = array(
		'groupbuy_intro'
	);

	public function __construct()
	{
		parent::__construct();
		$this->Goods_CommonModel = new Goods_CommonModel();
	}

	//多条件 获取商品团购详情
	public function getGroupBuyDetailByWhere($cond_row)
	{
		$row = $this->getOneByWhere($cond_row);
		if ($row)
		{
			$row['recommend_label']      = __(self::$recommend_map[$row['groupbuy_recommend']]);
			$row['groupbuy_state_label'] = __(self::$groupbuy_state_map[$row['groupbuy_state']]);

			$goods_common_row = $this->Goods_CommonModel->getOneByWhere(array('common_id' => $row['common_id']));

			if ($goods_common_row)
			{
				$row['goods_name']  = $goods_common_row['common_name'];
				$row['goods_price'] = $goods_common_row['common_price'];
				$row['reduce']      = $goods_common_row['common_price'] - $row['groupbuy_price'];
				$row['rate']        = sprintf("%.2f", $row['groupbuy_price'] / $goods_common_row['common_price'] * 10);

				if (strtotime($row['groupbuy_endtime']) < time() && $row['groupbuy_state'] == self::NORMAL)
				{
					$row['groupbuy_state']       = self::FINISHED;
					$row['groupbuy_state_label'] = __(self::$groupbuy_state_map[self::FINISHED]);

					$field_row['groupbuy_state'] = self::FINISHED;
					$this->editGroupBuy($row['groupbuy_id'], $field_row);
				}
				else
				{
					if ($row['groupbuy_state'] == self::NORMAL && strtotime(($row['groupbuy_starttime'])) > time())//即将开始
					{
						$row['groupbuy_state']       = self::WILLSTART; //审核通过，即将开始
						$row['groupbuy_state_label'] = __(self::$groupbuy_state_map[self::WILLSTART]);
					}
				}
			}
			else
			{
//				$this->removeGroupBuyGoods($row['groupbuy_id']);
				unset($row);
			}
		}

		return $row;
	}

	//根据主键搜索团购详情
	public function getGroupBuyDetailByID($groupbuy_id)
	{
		$row = $this->getOne($groupbuy_id);
		if ($row)
		{
			$row['recommend_label']      = __(self::$recommend_map[$row['groupbuy_recommend']]);
			$row['groupbuy_state_label'] = __(self::$groupbuy_state_map[$row['groupbuy_state']]);

			$goods_common_row = $this->Goods_CommonModel->getOneByWhere(array('common_id' => $row['common_id']));

			if ($goods_common_row)
			{
				$row['goods_name']  = $goods_common_row['common_name'];
				$row['goods_price'] = $goods_common_row['common_price'];
				$row['reduce']      = $goods_common_row['common_price'] - $row['groupbuy_price'];
				$row['rate']        = sprintf("%.2f", $row['groupbuy_price'] / $goods_common_row['common_price'] * 10);

				if (strtotime($row['groupbuy_endtime']) < time() && $row['groupbuy_state'] == self::NORMAL) //活动到期
				{
					$row['groupbuy_state']       = self::FINISHED;
					$row['groupbuy_state_label'] = __(self::$groupbuy_state_map[self::FINISHED]);

					$field_row['groupbuy_state'] = self::FINISHED;
					$this->editGroupBuy($row['groupbuy_id'], $field_row);
				}
				else
				{
					if ($row['groupbuy_state'] == self::NORMAL && strtotime(($row['groupbuy_starttime'])) > time())//即将开始
					{
						//$row['groupbuy_state']       = self::WILLSTART; //审核通过，即将开始
						$row['groupbuy_state_label'] = __(self::$groupbuy_state_map[self::WILLSTART]);
					}
				}
			}
			else  //参加团购的商品已被删除
			{
//				$this->removeGroupBuyGoods($row['groupbuy_id']);
				unset($row);
			}
		}

		return $row;
	}

	//发布活动
	public function addGroupBuy($field_row, $return_insert_flag)
	{
		return $this->add($field_row, $return_insert_flag);
	}
	/*删除团购商品*/
	/**
	 * @param $groupbuy_id
	 * @return bool
	 */
	public function removeGroupBuyGoods($groupbuy_id)
	{
		$rs_row = array();

		/*//活动商品对应的common_id
		$groupbuy_goods_rows = $this->get($groupbuy_id);
		$common_id_row = array_column($groupbuy_goods_rows,'common_id');
		$cond_row['common_id:IN'] =    $common_id_row;
		$cond_row['groupbuy_id:!='] =  $groupbuy_id;*/


		$del_flag = $this->remove($groupbuy_id);
		check_rs($del_flag, $rs_row);

		return is_ok($rs_row);
	}

	/*修改团购信息*/
	public function editGroupBuy($groupbuy_id, $field_row, $flag = null)
	{
		$update_flag = $this->edit($groupbuy_id, $field_row, $flag);
		return $update_flag;
	}
    
    /**
     * 获取店铺正在进行活动或者即将进行活动的商品
     * @param type $common_id
     * @return type
     */
    public function getGroupbuyByCommonId($common_id){
        //获取团购
        $cond_row = is_array($common_id) ? array('common_id:IN'=>$common_id) : array('common_id'=>$common_id);
        $cond_row['groupbuy_endtime:>'] = date('Y-m-d H:i:s');
        $cond_row['groupbuy_state:IN'] = array(self::UNDERREVIEW, self::NORMAL, self::AUDITFAILUER);
        
        $list = $this->getByWhere($cond_row);
        return $list;
    }
    
    
    /**
     * 获取店铺正在进行活动或者即将进行活动的商品
     * @return type
     */
    public function getGroupbuy(){
        //获取团购
        $cond_row['groupbuy_endtime:>'] = date('Y-m-d H:i:s',time());
        $cond_row['groupbuy_state:IN'] = array(self::UNDERREVIEW, self::NORMAL, self::AUDITFAILUER);
        
        $list = $this->getByWhere($cond_row);
        return $list;
    }
    
    
    /**
     * 获取common_id
     * @param type $list
     * @return type
     */
    public function getCommonidByGroupbuyList($list){
        if(!$list){
            return array();
        }
        $ids = array();
        foreach ($list as $value){
            $ids[] = $value['common_id'];
        }
        return $ids;
    }
    
    
    
    /**
     * 获取参加活动的商品common_id
     * @param type $common_id
     * @return type
     */
    public function getAllActivityCommonId($common_id){
        //团购
        $group_list = $this->getGroupbuyByCommonId($common_id);
        $group_common_ids = $this->getCommonidByGroupbuyList($group_list);
        //折扣
        $discount_goods_model = new Discount_GoodsModel();
        $discount_list = $discount_goods_model->getDiscountByCommonId($common_id);
        $discount_common_ids = $discount_goods_model->getCommonidByDiscountList($discount_list);
        $ids = array_unique(array_merge($group_common_ids,$discount_common_ids));
        return $ids;
    }
    
    /**
     * 获取参加活动的商品goods_id
     * @param type $goods_id
     * @return type
     */
    public function getAllActivityGoodsId(){
        //获取正在团购的商品
        $group_list = $this->getGroupbuy();
        $group_common_ids = array_column($group_list, 'common_id');
        $goods_model = new Goods_BaseModel();
        $goods_list = $goods_model->getByWhere(array('common_id:IN'=>$group_common_ids));
        $group_goods_ids = array_column($goods_list, 'goods_id');
        
        //折扣
        $discount_goods_model = new Discount_GoodsModel();
        $discount_list = $discount_goods_model->getDiscount();
        $discount_goods_ids = array_column($discount_list, 'goods_id');
        $ids = array_unique(array_merge($group_goods_ids,$discount_goods_ids));

         //秒杀
        $seckill_goods_model = new Seckill_GoodsModel();
        $seckill_list = $seckill_goods_model->getSeckill();
        $seckill_goods_ids = array_column($seckill_list, 'goods_id');
        $ids = array_unique(array_merge($group_goods_ids,$discount_goods_ids,$seckill_goods_ids));

         //预售
        $presale_goods_model = new Presale_GoodsModel();
        $presale_list = $presale_goods_model->getPresale();
        $presale_goods_ids = array_column($presale_list, 'goods_id');
        $ids = array_unique(array_merge($group_goods_ids,$discount_goods_ids,$seckill_goods_ids,$presale_goods_ids));
        return $ids;
    }

    /**
     * 获取店铺正在进行活动或者即将进行活动的商品
     * @return type
     */
    public function getGroupbuyList($cond_row){
        $list = $this->getByWhere($cond_row);
        return $list;
    }

	//获取首页版块中团购信息(不返回过期失效的团购商品,不返回已经下架的团购商品)
	public function getForumGroupbuy($groupbuy_id)
	{
		$data = array();
		if (!empty($groupbuy_id)) {
		    $cond_row['groupbuy_id:IN'] = $groupbuy_id;
		    $cond_row['groupbuy_state'] = 2;
            $cond_row['groupbuy_starttime:<'] = date('Y-m-d H:i:s');
            $cond_row['groupbuy_endtime:>'] = date('Y-m-d H:i:s');
			$groupbuy_list = $this->getNormalGroupbuy($groupbuy_id);

			if ( !empty($groupbuy_list) )
			{
			    $goods_id = array_column($groupbuy_list,'goods_id');
			    $Goods_BaseModel = new Goods_BaseModel();
                $goods_rows = $Goods_BaseModel->getGoodsListByGoodId($goods_id);

				foreach ($groupbuy_list as $groupbuy_key => $groupbuy_data)
				{
					if (strtotime($groupbuy_data['groupbuy_endtime']) > time() && $groupbuy_data['groupbuy_state'] == self::NORMAL)
					{
						$data[$groupbuy_key]['groupbuy_id'] = $groupbuy_data['groupbuy_id'];
						$data[$groupbuy_key]['goods_id'] = $groupbuy_data['goods_id'];
						$data[$groupbuy_key]['goods_name'] = $goods_rows[$groupbuy_data['goods_id']]['goods_name'];
						$data[$groupbuy_key]['groupbuy_image_rec'] = $groupbuy_data['groupbuy_image_rec'];
						$data[$groupbuy_key]['groupbuy_image'] = $groupbuy_data['groupbuy_image'];
						$data[$groupbuy_key]['groupbuy_name'] = $groupbuy_data['groupbuy_name'];
						$data[$groupbuy_key]['shop_name'] = $groupbuy_data['shop_name'];
						$data[$groupbuy_key]['groupbuy_endtime'] = $groupbuy_data['groupbuy_endtime'];
						$data[$groupbuy_key]['groupbuy_starttime'] = $groupbuy_data['groupbuy_starttime'];
						$data[$groupbuy_key]['groupbuy_buyer_count'] = $groupbuy_data['groupbuy_buyer_count'];
						$data[$groupbuy_key]['goods_price'] = $goods_rows[$groupbuy_data['goods_id']]['goods_price'];
						$data[$groupbuy_key]['groupbuy_price'] = $groupbuy_data['groupbuy_price'];
						$data[$groupbuy_key]['buy_num'] = $groupbuy_data['groupbuy_buy_quantity'];
						$data[$groupbuy_key]['groupbuy_id'] = $groupbuy_data['groupbuy_id'];
					}
				}
			}
		}

		return array_values($data);
	}

	//首页版块中获取补齐正在进行的团购活动
	public function getOpenForumGroupbuy($groupbuy_id,$num)
	{
		$not_in = '';
		if($groupbuy_id) { $not_in = "AND groupbuy_id NOT IN  (" . implode(',', $groupbuy_id) . ")";}
		//获取团购
		$sql = "
                    SELECT
                        a.groupbuy_id,a.goods_id,a.groupbuy_image_rec,a.groupbuy_image,a.groupbuy_name,a.shop_name,a.groupbuy_endtime,a.groupbuy_starttime,a.groupbuy_buyer_count,a.groupbuy_price,a.groupbuy_price,a.groupbuy_buy_quantity,a.goods_price,b.goods_name
                    FROM
                        " . TABEL_PREFIX . "groupbuy_base a left join " . TABEL_PREFIX . "goods_base b on a.goods_id = b.goods_id left join " . TABEL_PREFIX . "goods_common c on b.common_id=c.common_id
                    WHERE  'groupbuy_endtime' > '".date('Y-m-d H:i:s',time())."' AND 'groupbuy_starttime' > '".date('Y-m-d H:i:s',time())."' ".$not_in." AND groupbuy_state = 2 AND b.goods_is_shelves=1 AND  b.is_del=1 AND c.common_state=1 AND c.common_verify=1 AND c.is_del=1 ORDER BY groupbuy_id DESC LIMIT ".$num;
		$rows = $this -> sql -> getAll($sql);

		return $rows;
	}

	public function getGroupBuyGoodsList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100,$sub_site_id = 0 )
    {
        //分站di
        if($sub_site_id > 0){
            $sql_str = " AND district_id=".$sub_site_id;
        }

        //需要分页如何高效，易扩展
        $offset = $rows * ($page - 1);

        $Limit = ' LIMIT ' . $offset . ', ' . $rows;


        $sql = "SELECT a.* ,b.common_price,b.common_stock
                FROM " . TABEL_PREFIX . "groupbuy_base a LEFT JOIN " . TABEL_PREFIX . "goods_common b ON (a.common_id = b.common_id) 
                WHERE 1 AND b.common_state=1 AND b.common_verify=1 AND b.shop_status=3 AND b.is_del=1 ".$sql_str;

        $pre = 'a.';
        $where =  $this->setCondWhere($cond_row,$pre);
        $order =  $this->setOrderBy($order_row,$pre);

        $sql = $sql.$where.$order;

        $data = $this->sql->getAll($sql);
        //读取影响的函数, 和记录封装到一起.
        $query = '
                SELECT
                    FOUND_ROWS() total';

        $row = $this->sql->getRow($query);
        $total = $row['total'];

        $sql = $sql.$Limit;

        $items = $this->sql->getAll($sql);

        if($items) {
            foreach ($items as $ik => $iv) {

                $items[$ik]['groupbuy_recommend_label'] = __(self::$recommend_map[$iv['groupbuy_recommend']]);
                $items[$ik]['groupbuy_state_label']     = __(self::$groupbuy_state_map[$iv['groupbuy_state']]);
                $items[$ik]['groupbuy_type_label']      = __(self::$goods_type_map[$iv['groupbuy_type']]);
                $items[$ik]['reduce']      = $iv['common_price'] - $iv['groupbuy_price'];
                $items[$ik]['rate']        = sprintf("%.2f", $iv['groupbuy_price'] / $iv['common_price'] * 10);
                $items[$ik]['is_start']  = $iv['groupbuy_starttime'] > date('Y-m-d H:i:s') ? 0 : 1;
            }
        }


        $data = array();

        $data['items'] = $items;
        $data['page'] = $page;
        $data['total'] = ceil_r($total / $rows);  //total page
        $data['totalsize'] = $total;
        $data['records'] = $total;


        //修改已经过期的团购活动状态
        $upsql = "UPDATE " . TABEL_PREFIX . "groupbuy_base SET groupbuy_state = 3 WHERE groupbuy_state = 2 AND groupbuy_endtime < '".get_date_time()."'";
        $this->sql->exec($upsql);

        return $data;
    }

    /*
	 *获取团购列表
	 * 判断团购活动是否正常，判断团购商品是否正常
     */
    public function getNormalGroupbuy($groupbuy_id)
    {
        $in = '';
        if($groupbuy_id) { $in = "AND groupbuy_id IN  (" . implode(',', $groupbuy_id) . ")";}
        $sql = "
                    SELECT
                        a.*
                    FROM
                        " . TABEL_PREFIX . "groupbuy_base a left join " . TABEL_PREFIX . "goods_base b on a.goods_id = b.goods_id left join " . TABEL_PREFIX . "goods_common c on b.common_id=c.common_id
                    WHERE  'groupbuy_endtime' > '".date('Y-m-d H:i:s',time())."' AND 'groupbuy_starttime' > '".date('Y-m-d H:i:s',time())."' ".$in." AND groupbuy_state = 2 AND b.goods_is_shelves=1 AND b.is_del=1 AND c.common_state=1 AND c.common_verify=1 AND c.is_del=1 ORDER BY groupbuy_id DESC ";

        $rows = $this -> sql -> getAll($sql);

        return $rows;
    }


    /*
	 *获取团购商品   这个方法已不再使用，这个方法中获取的分页商品会缺失。
	 *分页
     * $is_all 是否查询全部数据，包括不正常的商品
     *

    public function getGroupBuyGoodsList20180925($cond_row = array(), $order_row = array(), $page = 1, $pagesize = 100,$sub_site_id = 0 ,$is_all = false)
    {
        $rows = $this->listByWhere($cond_row, $order_row, $page, $pagesize);

        if ($rows['items'])
        {
            $groupbuy_goods  = array();  //团购商品
            $expire_groupbuy = array(); //过期的活动
//			$delete_groupbuy = array(); //活动下的商品已经被删除

            foreach ($rows['items'] as $key => $value)
            {
                $rows['items'][$key]['groupbuy_recommend_label'] = __(self::$recommend_map[$value['groupbuy_recommend']]);
                $rows['items'][$key]['groupbuy_state_label']     = __(self::$groupbuy_state_map[$value['groupbuy_state']]);
                $rows['items'][$key]['groupbuy_type_label']      = __(self::$goods_type_map[$value['groupbuy_type']]);

                if (strtotime($value['groupbuy_endtime']) < time() && $value['groupbuy_state'] == self::NORMAL)
                {
                    $rows['items'][$key]['groupbuy_state']       = self::FINISHED;
                    $rows['items'][$key]['groupbuy_state_label'] = __(self::$groupbuy_state_map[self::FINISHED]);

                    $expire_groupbuy[] = $value['groupbuy_id'];
                }

                $groupbuy_goods[] = $value['common_id'];
            }

            //获取分站的商品
            $goods_cond = array();

            if($sub_site_id > 0){
                //获取站点信息
                $sub_SiteModel = new Sub_SiteModel();
                $sub_site_district_ids = $sub_SiteModel->getDistrictChildId($sub_site_id);
                if($sub_site_district_ids){
                    $goods_cond['district_id:IN'] = $sub_site_district_ids;
                }
            }

            $goods_common_rows = $this->Goods_CommonModel->getNormalStateGoodsCommon($groupbuy_goods,$goods_cond);

            $group_rows = array();
            $k = 0;
            foreach ($rows['items'] as $key => $value)
            {
                if (in_array($value['common_id'], array_keys($goods_common_rows)))
                {
                    $rows['items'][$key]['goods_name']  = $goods_common_rows[$value['common_id']]['common_name'];
                    $rows['items'][$key]['goods_price'] = $goods_common_rows[$value['common_id']]['common_price'];
                    $rows['items'][$key]['reduce']      = $goods_common_rows[$value['common_id']]['common_price'] - $value['groupbuy_price'];
                    $rows['items'][$key]['rate']        = sprintf("%.2f", $value['groupbuy_price'] / $goods_common_rows[$value['common_id']]['common_price'] * 10);
                    $rows['items'][$key]['goods_stock']  = $goods_common_rows[$value['common_id']]['common_stock'];
                    $rows['items'][$key]['is_start']  = $value['groupbuy_starttime'] > date('Y-m-d H:i:s') ? 0 : 1;
                    $group_rows[$k] = $rows['items'][$key];
                    $k ++ ;
                }
//				else
//				{
//					unset($rows['items'][$key]);
//					$delete_groupbuy[] = $value['groupbuy_id'];
//				}
            }

            if($is_all == false){
                $rows['items'] = $group_rows;
            }

            $field_row['groupbuy_state'] = self::FINISHED;
            $this->editGroupBuy($expire_groupbuy, $field_row);  //活动到期，更改活动状态

//			$this->removeGroupBuyGoods($delete_groupbuy);       //删除商品不存在的活动
        }


        return $rows;
    }
    */

    //当前店铺正在、即将参加团购活动的商品
    public function getGroupBuyGoodsIds()
    {
        $cond_row['groupbuy_type'] = 1;//线上团
        $cond_row['groupbuy_state:IN'] = array(1, 2, 4); //团购状态:审核中 正常 审核失败
        $cond_row['shop_id'] = Perm::$shopId;
        $cond_row['groupbuy_endtime:>'] = date('Y-m-d H:i:s', time());
        $groupbuy_list = $this->getByWhere($cond_row);
        if ($groupbuy_list) {
            $common_ids = array_column($groupbuy_list, 'common_id');
        }
        if($common_ids){
            $goods_cond['common_id:IN'] = $common_ids;
            $goods_cond['is_del'] = 1;//未删除
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_list = $Goods_BaseModel->getByWhere($goods_cond);
            if($goods_list)
            {
                $goods_ids = array_column($goods_list,'goods_id');
            }else{
                $goods_ids = array();
            }
        }else{
            $goods_ids = array();
        }
        return array_unique($goods_ids);
    }

}