<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Bargain_BaseModel extends Bargain_Base
{
    const WILLON = 0;   //未开始
	const ISON  = 1;   //进行中
	const ISOFF    = 2;     //活动结束
	const ADMINOFF = 3;  //商家关闭、商家下架商品
	const PLATOFF = 4;  //平台终止

	const no_del = 0;  //未删除
	const is_del = 1;  //删除

    //活动状态
	public static $bargainState = array(
		self::WILLON => "未开始",
		self::ISON => "进行中",
		self::ISOFF => "活动结束",
		self::ADMINOFF => "管理员关闭",
		self::PLATOFF => "平台终止"
	);

	//获取砍价活动列表
    public function getBargainList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        if ($cond_row['goods_name']) {
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_cond['goods_name:LIKE'] = '%' . $cond_row['goods_name'] . '%';
            $goods_list = $Goods_BaseModel->getBaseList($goods_cond, array(), $page = 1, $rows = 100);
            $goods_ids = array_column($goods_list['items'], 'goods_id');
            $goods_ids = implode(",", $goods_ids);
            unset($cond_row['goods_name']);
        }

        if ($cond_row['user_id']) {
            $sql = "SELECT bargain_base.*,goods_base.goods_name,goods_base.goods_price AS goods_old_price,goods_base.goods_image,bargain_buy_user.*  FROM ";
        } else {
            $sql = "SELECT bargain_base.*,goods_base.goods_name,goods_base.goods_price AS goods_old_price,goods_base.goods_image FROM ";
        }
        $sql .= TABEL_PREFIX . "bargain_base AS bargain_base JOIN ";
        $sql .= TABEL_PREFIX . "goods_base AS goods_base ON ";
        $sql .= "bargain_base.goods_id = goods_base.goods_id ";
        if ($cond_row['user_id']) {
            $sql .= "JOIN " . TABEL_PREFIX . "bargain_buy_user AS bargain_buy_user ON ";
            $sql .= "bargain_base.bargain_id = bargain_buy_user.bargain_id ";
        }
        $sql .= "where 1 AND bargain_base.is_del = 0 ";

        if ($goods_ids) {
            $sql .= " AND bargain_base.goods_id IN (" . $goods_ids . ")";
        }

        if ($cond_row['shop_name']) {
            $sql .= " AND bargain_base.shop_name LIKE %" . $cond_row['shop_name'] . "%";
        }

        if ($cond_row['bargain_status']) {
            $cond_row['bargain_status:<='] = $cond_row['bargain_status'];
            $sql .= " AND bargain_base.bargain_status = " . $cond_row['bargain_status'];
            unset($cond_row['bargain_status']);
        }

        if ($cond_row['user_id']) {
            $sql .= " AND bargain_buy_user.user_id = " . $cond_row['user_id'];
        }
        if ($cond_row['shop_id']) {
            $sql .= " AND bargain_base.shop_id = " . $cond_row['shop_id'];
        }
        if ($cond_row['end_time']) {
            $cond_row['end_time:>'] = $cond_row['end_time'];
            $sql .= " AND bargain_base.end_time > " . $cond_row['end_time'];
            unset($cond_row['end_time']);
        }
        $sql .= " ORDER BY bargain_base.create_time DESC LIMIT " . ($page - 1) . "," . $rows;
        $result = $this->sql->getAll($sql);

        $res = array();
        if($cond_row['is_list'] == 1){
            foreach ($result as $k => $v) {
                $res[$v['bargain_id']] = $v;
                if ($cond_row['user_id']) {
                    $res[$v['bargain_id']]['is_self'] = 1;
                    $res[$v['bargain_id']]['overPlus'] = number_format($v['goods_price'] - $v['bargain_price'] - $v['bargain_price_count'],2);
                } else {
                    $res[$v['bargain_id']]['is_self'] = 0;
                }
                $res[$v['bargain_id']]['start_date'] = date('Y-m-d H:i:s', $v['start_time']);
                $res[$v['bargain_id']]['start'] = date('Y-m-d', $v['start_time']);
                $res[$v['bargain_id']]['end_date'] = date('Y-m-d H:i:s', $v['end_time']);
                $res[$v['bargain_id']]['end'] = date('Y-m-d', $v['end_time']);
                if($cond_row['user_id']){
                    $res[$v['bargain_id']]['user_end_date'] = date('Y-m-d H:i:s', $v['user_end_time']);
                }
                $res[$v['bargain_id']]['bargain_status_con'] = __(self::$bargainState[$v['bargain_status']]);
            }
        }else{
            foreach ($result as $k => $v) {
                if ($cond_row['user_id']) {
                    $result[$k]['is_self'] = 1;
                    $result[$k]['overPlus'] = number_format($v['goods_price'] - $v['bargain_price'] - $v['bargain_price_count'], 2);
                } else {
                    $result[$k]['is_self'] = 0;
                }
                $result[$k]['start_date'] = date('Y-m-d H:i:s', $v['start_time']);
                $result[$k]['start'] = date('Y-m-d', $v['start_time']);
                $result[$k]['end_date'] = date('Y-m-d H:i:s', $v['end_time']);
                $result[$k]['end'] = date('Y-m-d', $v['end_time']);
                if ($cond_row['user_id']) {
                    $result[$k]['user_end_date'] = date('Y-m-d H:i:s', $v['user_end_time']);
                }
                $result[$k]['bargain_status_con'] = __(self::$bargainState[$v['bargain_status']]);
            }
            $res = $result;
        }
        unset($cond_row['is_list']);
        if ($cond_row['user_id']) {
            unset($cond_row['user_id']);
        }
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);
        $data['items'] = $res;
        return $data;
    }

    //根据砍价活动id获取对应商品信息以及活动信息
    public function getBargainInfo($bargain_id)
    {
        $sql = "SELECT bargain_base.*,goods_base.goods_name,goods_base.goods_price AS goods_old_price,goods_base.goods_stock,goods_base.goods_image FROM ";
        $sql .= TABEL_PREFIX . "bargain_base AS bargain_base JOIN ";
        $sql .= TABEL_PREFIX . "goods_base AS goods_base ON ";
        $sql .= "bargain_base.goods_id = goods_base.goods_id ";
        $sql .= "where 1";
        $sql .= " AND bargain_base.bargain_id = " . $bargain_id;
        $result = $this->sql->getAll($sql);
        $res = current($result);
        $res['start_time'] = date('Y-m-d',$res['start_time']);
        $res['end_time'] = date('Y-m-d',$res['end_time']);
        return $res;
    }

    //获取当前店铺可参加砍价活动商品列表
    public function getBargainGoodsList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        //获取当前店铺已经参加促销活动的商品goods_id;
        $active_goods_id = $this->getActiveGoodsId();

        $Goods_BaseModel = new Goods_BaseModel;
        $data = $Goods_BaseModel->getGoodsSpecByGoodsId($cond_row, array('goods_id' => 'DESC'), $page, $rows);
        foreach ($data['items'] as $key => $value) {
            if (is_array($value['spec'])) {
                foreach ($value['spec'] as $k => $v) {
                    $data['items'][$key]['spec_title'] .= $k . '：' . $v . '；';
                }
            }
            if (in_array($value['goods_id'], $active_goods_id) && $active_goods_id) {
                $data['items'][$key]['is_join'] = 'true';
            } else {
                $data['items'][$key]['is_join'] = 'false';
            }
        }
        return $data;
    }

    //获取当前店铺参加促销活动的商品
    public function getActiveGoodsId()
    {
        //正在、即将参加砍价的商品（goods_id）
        $bargain_goods_ids = $this->getBargainGoodsIds();

        //正在、即将参加拼团的商品（goods_id）
        $pinTuanBaseModel = new PinTuan_Base();
        $pintuan_goods_ids = $pinTuanBaseModel->getPinTuanGoodsIds();

        //正在、即将参加团购的商品（common_id）
        $GroupBuy_BaseModel = new GroupBuy_BaseModel();
        $groupbuy_goods_ids = $GroupBuy_BaseModel->getGroupBuyGoodsIds();

        //正在、即将参加加价购的商品（goods_id）
        $increase_base_model = new Increase_BaseModel();
        $increase_goods_ids = $increase_base_model->getIncreaseGoodsIds();

        //正在、即将参加满即送的商品id（goods_id）
        $mansong_base_model = new ManSong_BaseModel();
        $mansong_goods_ids = $mansong_base_model->getManSongGoodsIds();

        //正在、即将参加限时折扣的商品（goods_id）
        $Discount_base_model = new Discount_BaseModel();
        $discount_goods_ids = $Discount_base_model->getDiscountGoodsIds();

        //正在参加plus的商品goods_id(common_id)
        $Plus_GoodsModel = new Plus_GoodsModel();
        $plus_goods_ids = $Plus_GoodsModel->getPlusGoodsId();

        //活动中商品id
        $active_goods_id = array_merge($bargain_goods_ids, $pintuan_goods_ids, $groupbuy_goods_ids, $increase_goods_ids, $mansong_goods_ids, $discount_goods_ids, $plus_goods_ids);

        return array_unique($active_goods_id);
    }

    //当前店铺正在、即将参加砍价活动商品id
    public  function getBargainGoodsIds()
    {
        $cond_row = array();
        $cond_row['bargain_status:<='] = 1;//活动状态
        $cond_row['is_del'] = 0;//未删除
        $cond_row['shop_id'] = Perm::$shopId;
        $cond_row['end_time:>'] = time();
        $bargain_list = $this->getByWhere($cond_row);
        if($bargain_list){
            $goods_ids = array_column($bargain_list,'goods_id');
        }else{
            $goods_ids = array();
        }
        return array_unique($goods_ids);
    }

    //根据bargain_id获取活动信息
    public function getBargainInfoByBargainId($bargain_id)
    {
        $bargain_info = $this->getOne($bargain_id);
        $bargain_info['start_time'] = date("Y-m-d", $bargain_info['start_time']);
        $bargain_info['end_time'] = date("Y-m-d", $bargain_info['end_time']);
        $Goods_BaseModel = new Goods_BaseModel();
        $goods_info = $Goods_BaseModel->getGoodsInfo($bargain_info['goods_id']);
        $bargain_info['goods_base'] = $goods_info['goods_base'];
        return $bargain_info;
    }

    //判断是否为商家自己砍价
    public function checkSelf($bargain_id)
    {
        $user_id = Perm::$userId;
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_info = $Shop_BaseModel->getOneByWhere(array('user_id' => $user_id));
        $bargain_info = $this->getOneByWhere(array('bargain_id' => $bargain_id));
        if ($shop_info['shop_id'] == $bargain_info['shop_id']) {
            return true;
        } else {
            return false;
        }
    }

    //商家下架商品时，对应砍价活动的状态修改
    public function editBargainStatus($goods_ids)
    {
        $rs_row = array();
        $cond_row = array();
        $cond_row['goods_id:IN'] = $goods_ids;
        $edit_row = array();
        $bargain_ids = $this->getKeyByWhere($cond_row);
		//不存在，则过掉
		if(!$bargain_ids){
			return true;
		}
        $edit_row['bargain_status'] = self::ADMINOFF;
        $base_flag = $this->editBargain($bargain_ids, $edit_row);
        check_rs($base_flag, $rs_row);

        if($bargain_ids){
            $Bargain_BuyUserModel = new Bargain_BuyUserModel();
            $user_cond_row = array();
            $user_cond_row['bargain_id:IN'] = $bargain_ids;
            $buy_ids = $Bargain_BuyUserModel->getKeyByWhere($user_cond_row);
            $user_edit_row = array();
            $user_edit_row['bargain_state'] = Bargain_BuyUserModel::ADMINOFF;
            $base_flag = $Bargain_BuyUserModel->editBuyUser($buy_ids, $user_edit_row);
            check_rs($base_flag, $rs_row);
        }

        $flag = is_ok($rs_row);
        return $flag;
    }

    public function getBargainCommonIds()
    {
        $cond_row = array();
        $cond_row['bargain_status:<='] = 1;//活动状态
        $cond_row['is_del'] = 0;//未删除
        $cond_row['shop_id'] = Perm::$shopId;
        $cond_row['end_time:>'] = time();
        $bargain_list = $this->getByWhere($cond_row);
        if ($bargain_list) {
            $goods_ids = array_column($bargain_list, 'goods_id');
        }else{
            $goods_ids = array();
        }

        if(!empty($goods_ids)){
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_base = $Goods_BaseModel->getByWhere(array('goods_id:IN'=> $goods_ids));
            if($goods_base){
                $goods_common_id = array_column($goods_base,'common_id');
            }else{
                $goods_common_id = array();
            }
        }else{
            $goods_common_id = array();
        }

        return array_unique($goods_common_id);
    }
}

?>