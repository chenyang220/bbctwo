<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class User_FootprintModel extends User_Footprint
{

    /**
     * 读取分页列表
     *
     * @param  array $cond_row  查询条件
     * @param  array $order_row 排序信息
     * @param  array $page      当前页码
     * @param  array $rows      每页记录数
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getFootprintList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100, $group = 'footprint_date')
    {

        $data = $this->listByWhere($cond_row, $order_row, $page, $rows, true, $group);
        return $data;
    }

    /**
     * 读取足迹所有数据
     *
     * @param  array $order_row 查询条件
     *
     * @return array $data 返回的查询内容
     * @access public
     */
    public function getFootprintAll($order_row)
    {
        $data = $this->getByWhere($order_row, array('footprint_time' => 'DESC'));

        return $data;
    }

    /**
     * 读取一个足迹数据
     *
     * @param  array $order_row 查询条件
     *
     * @return array $data 返回的查询内容
     */
    public function getFootprintDetail($order_row)
    {
        $data = $this->getOneByWhere($order_row);

        return $data;
    }

    //根据user_id获取用户的足迹商品数量
    public function getFootprintNum($cond_row)
    {
        return $this->getNum($cond_row);
    }

    public function getFootGoodCommonId()
    {
        $query = 'SELECT footprint_id,common_id FROM ' . $this->_tableName . ' where user_id=' . Perm::$userId . ' group by common_id';

        $row = $this->sql->getAll($query);

        return $row;

    }

    public function getGoodsFootSum($cond_row){
        $where = 'where b.goods_id='.$cond_row['goods_id'].' and f.footprint_time>="'.$cond_row['stime'].' 00:00:00" and f.footprint_time<="'.$cond_row['etime'].' 23:59:59"';
        $sql = " SELECT FROM_UNIXTIME(unix_timestamp(f.footprint_time),'%Y-%m-%d') as time, 1 as foot_print  from `yf_user_footprint` f LEFT JOIN `yf_goods_common` c ON f.common_id = c.common_id LEFT JOIN `yf_goods_base` b ON c.common_id = b.common_id {$where}";
        $goods_footprint_list = $this->sql->getAll($sql);
        return $goods_footprint_list;
    }

    //查找用户的有效浏览记录商品数量
    public function getFootprintGoodsNum($user_id)
    {
        //查找出用户所有浏览记录
        $userfootprint = $this->getByWhere(array('user_id'=>$user_id));

        //获取所有浏览商品的common_id
        $common_id = array_column($userfootprint, 'common_id');

        $Goods_CommonModel = new Goods_CommonModel();
        //检验$common_id的有效性，删除
        $footprint_goods_num = 0;
        if($common_id)
        {
            $tb = TABEL_PREFIX . "goods_common";
            $order_by = implode(",", $common_id);
            $condition = " where common_id in (" . $order_by . ")";
            $sql = "select common_id from " . $tb . " " . $condition . " order by field(common_id," . $order_by . ")";
            $goods = $Goods_CommonModel->sql->getAll($sql);

            if($goods)
            {
                $commonid = array_column($goods, 'common_id');

                $userfootprint = $this->getByWhere(array('user_id'=>$user_id,'common_id:IN'=>$commonid));

                $footprint_goods_num = count($userfootprint);

                //删除无效的common_id的浏览记录
                $common_diff = array_diff($common_id,$commonid);
                $userfootdel = $this->getByWhere(array('user_id'=>$user_id,'common_id:IN'=>$common_diff));
                $del_id = array_column($userfootdel, 'footprint_id');

                $this->removeFootprint($del_id);

            }
        }

        return $footprint_goods_num;

    }

    //首页猜你喜欢
    public function userFavorite()
    {
        $user_id = Perm::$userId;

        if($user_id) {
            $sql = "select a.common_id from " . TABEL_PREFIX . "user_footprint a left join " . TABEL_PREFIX . "goods_common b on a.common_id = b.common_id where 1 AND b.common_state=1 AND b.is_del=1 AND b.common_verify=1 AND b.shop_status=3 AND b.supply_shop_id<>0 AND a.user_id = ".$user_id." AND b.common_is_virtual=0 order by a.footprint_time desc limit 10";
            $foot_print = $this->sql->getAll($sql);
            if($foot_print) {
                $common_ids = array_unique(array_column($foot_print,'common_id'));
            }
            $Goods_CommonModel = new Goods_CommonModel();
            $cond_row['common_id:IN'] = $common_ids;
            $goods_common = $Goods_CommonModel->getByWhere($cond_row);
            if($goods_common) {
                $goods_cat_ids = array_unique(array_column($goods_common,'cat_id'));
            }
        }else{
            $Goods_RecommendModel = new Goods_RecommendModel();
            $goods_recommend = $Goods_RecommendModel->getRecommendList();
            if($goods_recommend) {
                $goods_cat_ids = array_unique(array_column($goods_recommend,'goods_cat_id'));
            }
        }

        $sql1 = "select a.* from " . TABEL_PREFIX . "goods_base a left join " . TABEL_PREFIX . "goods_common b on a.common_id = b.common_id left join " . TABEL_PREFIX . "shop_base c on a.shop_id = c.shop_id
                     where 1 AND a.goods_is_shelves = 1 AND a.is_del = 1 AND b.common_state=1 AND b.common_verify=1 AND b.is_del=1 AND c.shop_status=3 AND c.shop_type=1";
        if($goods_cat_ids) {
            $cat_ids = implode(',',$goods_cat_ids);
            $sql1 .= " AND a.cat_id in (".$cat_ids.") ";
        }
        $info1=$this->sql->getAll($sql1);
        if(count($info1)>=4){
            $list_g1 = array_column($info1,'goods_id');
            $g_list1 = array_rand($list_g1,4);
            foreach ($g_list1 as $key => $value) {
                $goods_arr[]=$list_g1[$value];
            }    
        }else{
            $goods_arr = array_column($info1,'goods_id');
        }
        $goods_ids = array_unique($goods_arr);
        if(!is_array($goods_arr)){$goods_arr = array();}
        if(count($goods_arr) < 4) {
            $limit = 4 - count($goods_arr);
            $sql2 = "select a.goods_id from " . TABEL_PREFIX . "goods_base a left join " . TABEL_PREFIX . "shop_base c on a.shop_id = c.shop_id where a.goods_is_shelves = 1 AND a.is_del = 1 AND c.shop_status=3 AND c.shop_type=1";
            if($goods_ids) {
                $goods_ids_str = implode(',',$goods_ids);
                $sql2 .= " AND a.goods_id not in (".$goods_ids_str.") ";
            }
            $sql2 .= " order by a.goods_id DESC limit 100 ";
            $info2 = $this->sql->getAll($sql2);
            if(count($info2)>=$limit){
                $list_g = array_column($info2,'goods_id');
                $g_list = array_rand($list_g,$limit);
                foreach ($g_list as $key => $value) {
                    $goods_list[]=$list_g[$value];
                }
            }else{
                $goods_list = array_column($info2,'goods_id');
            }
            if(!is_array($goods_list)){$goods_list = array();}
            $goods_arr = array_merge($goods_ids,$goods_list);
        }
        $Goods_BaseModel = new Goods_BaseModel();
        $data = $Goods_BaseModel->getBase($goods_arr);
        foreach ($data as $k => $v) {
            $sql = "select images_id,images_image from " . TABEL_PREFIX . "goods_images where common_id = " . $v['common_id'] . " and images_color_id = " . $v['color_id'] . " limit 1";
            $goods_images = $this->sql->getAll($sql);
            $data[$k]['images'] = $goods_images[0]['images_image'];
        }     
        return $data;
    }
}

?>