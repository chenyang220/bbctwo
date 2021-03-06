<?php

/**
 * Created by PhpStorm.
 * User: rd04
 * Date: 2017/11/13
 * Time: 15:43
 */
class PinTuan_Base extends Yf_Model
{
    public $_cacheKeyPrefix  = 'c|pintuan|';
    public $_cacheName       = 'pintuan';
    public $_tableName       = 'pintuan';
    public $_tablePrimaryKey = 'id';

    const WILLSTART    = 0; //审核通过，但未到开始时间，即将开始
    const UNDERREVIEW  = 1;  //审核中
    const NORMAL       = 2;  //正常
    const FINISHED     = 3;  //结束
    const AUDITFAILUER = 4; //审核失败
    const CLOSED       = 5; //管理员关闭

    public static $statusEnabled = 1; //是否可用，1为可用
    public static $statusDisabled = 0; //是否可用，1为可用
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

    /**
     * 获取参加活动的商品common_id
     * @param type $common_id
     * @return type
     */
    public function getAllActivityCommonId($common_id){
        //拼团
        $group_list = $this->getPinTuanByCommonId($common_id);
        $group_common_ids = $this->getCommonidByPinTuanList($group_list);
        //折扣
        $discount_goods_model = new Discount_GoodsModel();
        $discount_list = $discount_goods_model->getDiscountByCommonId($common_id);
        $discount_common_ids = $discount_goods_model->getCommonidByDiscountList($discount_list);
        $ids = array_unique(array_merge($group_common_ids,$discount_common_ids));
        return $ids;
    }

    /**
     * 获取common_id
     * @param type $list
     * @return type
     */
    public function getCommonidByPinTuanList($list){
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
     * 获取店铺正在进行活动或者即将进行活动的商品
     * @param type $common_id
     * @return type
     */
    public function getPinTuanByCommonId($common_id){
        //获取拼团
        $cond_row = is_array($common_id) ? array('common_id:IN'=>$common_id) : array('common_id'=>$common_id);
        $cond_row['end_time:>'] = date('Y-m-d H:i:s');
        // $cond_row['state:IN'] = array(self::UNDERREVIEW, self::NORMAL, self::AUDITFAILUER);
        
        $list = $this->getByWhere($cond_row);
        return $list;
    }

    /*
     *获取拼团商品
     *分页
     * $is_all 是否查询全部数据，包括不正常的商品
     */
    public function getPinTuanGoodsList($cond_row = array(), $order_row = array(), $page = 1, $pagesize = 100,$sub_site_id = 0 ,$is_all = false)
    {
        $rows = $this->listByWhere($cond_row, $order_row, $page, $pagesize);

        return $rows;
    }

    /**
     * 获取分类信息
     * @return type
     */
    public function getCategory(){
        $categoryModel = new Goods_CatModel();
        $category = $categoryModel->getOneCatList();
        return $category;
    }
    
    /**
     * 获取拼团banner
     * @return type
     */
    public function getBanner(){
        $banner = array();
//        if(Web_ConfigModel::value('subsite_is_open') && isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0){
//            $subsite_id = '_'.$_COOKIE['sub_site_id'];
//        }else{
//            $subsite_id = '';
//        }
        $subsite_id = ''; //暂时3.1.3版本此功能不考虑分站
        for($n = 1; $n <= 4; $n ++){
            $image_key = 'pintuan_slider_image'.$n.$subsite_id;
            $link_key = 'pintuan_slider_link'.$n.$subsite_id;
            if(Web_ConfigModel::value($image_key)){
                $banner[$n]['image'] = Web_ConfigModel::value($image_key);
                $banner[$n]['link'] = Web_ConfigModel::value($link_key);
            }
        }
        return $banner;
    }

    
    /**
     * 根据分类获取拼团商品
     * @return type
     */
    public function getGoodsListByCatId(){
        
        return [];
    }
    
    /**
     * 获取推荐商品
     * @return type
     */
    public function getRecommend(){
        
        return [];
    }



    /**
     * 获取拼团商品
     * @return type
     */
    public function getGoodsList($where = array(),$cat_id=0){
        $pt_detail = $this->getDetailList($where);
        $goods_id = array();
        foreach ($pt_detail as $key=>$value){
            if(!$value['detail'] || !$value['detail']['goods_id']){
                unset($pt_detail[$key]);
                continue;
            } else {
                $goods_id[] = $value['detail']['goods_id'];
            }
        }

        $goods_detail = array();
        if($goods_id){
            $goods_model = new Goods_BaseModel();
            $goods_detail = $goods_model->getBase($goods_id);
            //对已下架商品进行过滤,
            foreach ($goods_detail as $key => $val) {
                if ($val['goods_is_shelves'] == 2) {
                    unset($goods_detail[$key]);
                }
            }
            
        }
        foreach ($pt_detail as $k=>$val){
            if(!$goods_detail[$val['detail']['goods_id']]){
                unset($pt_detail[$k]);
                continue;
            } else {
                $pt_detail[$k]['goods'] = $goods_detail[$val['detail']['goods_id']];

                if($cat_id>0) {
                    $Goods_CatModel = new Goods_CatModel();
                    $cat_tree           = $Goods_CatModel->getCatTreeData($cat_id, true, 0);

                    $category_ids = array_column($cat_tree,'cat_id');


                    if (!in_array($goods_detail[$val['detail']['goods_id']]['cat_id'] ,$category_ids)) {
                        unset($pt_detail[$k]);
                    }
                }

            }
        }
        foreach ($where['id:IN'] as $k=>$v)
        {
            foreach ($pt_detail as $kk=>$vv)
            {
                if($v==$kk)
                {
                    $pt_details[] =$vv;
                }
            }
        }
        return $pt_details;
    }

 //    /**
 //     * 获取拼团商品
 //     * @return type
 //     */
 //    public function getGoodsList($where = array(),$cat_id=0){
 //        $pt_detail = $this->getDetailList($where);
 //        $goods_id = array();
 //        foreach ($pt_detail as $key=>$value){
 //            if(!$value['detail'] || !$value['detail']['goods_id']){
 //                unset($pt_detail[$key]);
 //                continue;
 //            } else {
 //                $goods_id[] = $value['detail']['goods_id'];
 //            }
 //        }
 //        $goods_detail = array();
 //        if($goods_id){
 //            $goods_model = new Goods_BaseModel();
 //            $goods_detail = $goods_model->getBase($goods_id);
 //            //对已下架商品进行过滤,
 //            foreach ($goods_detail as $key => $val) {
 //                if ($val['goods_is_shelves'] == 2) {
 //                    unset($goods_detail[$key]);
 //                }
 //            }
            
 //        }
 //        foreach ($pt_detail as $k=>$val){
 //            if(!$goods_detail[$val['detail']['goods_id']]){
 //                unset($pt_detail[$k]);
 //                continue;
 //            } else {
 //                $pt_detail[$k]['goods'] = $goods_detail[$val['detail']['goods_id']];

 //                if($cat_id>0) {
 //                    $Goods_CatModel = new Goods_CatModel();
 //                    $cat_tree           = $Goods_CatModel->getCatTreeData($cat_id, true, 0);

 //                    $category_ids = array_column($cat_tree,'cat_id');


 //                    if (!in_array($goods_detail[$val['detail']['goods_id']]['cat_id'] ,$category_ids)) {
 //                        unset($pt_detail[$k]);
 //                    }
 //                }

 //            }
 //        }
 // // file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'abs.php',print_r($where,true),FILE_APPEND);
 //        foreach ($where['id:IN'] as $k=>$v)
 //        {


 //            foreach ($pt_detail as $kk=>$vv)
 //            {
 //                if($v==$kk)
 //                {
 //                    $pt_details[] =$vv;
 //                }
 //            }
 //        }

 //        return $pt_details;
 //    }


    /**
     * 获取拼团列表
     * @param $cond_row
     * @param array $order_row
     * @param int $page
     * @param int $rows
     * @return array
     */
    public function getPinTuanList($cond_row = array(), $order_row = array(), $page=1, $rows=100)
    {
        $rows = $this->listByWhere($cond_row, $order_row, $page, $rows);

        if ($rows['items']) {
            $shopModel = new Shop_BaseModel;
            $shopIds = array_column($rows['items'], 'shop_id');
            $shopRows = $shopModel->get($shopIds);

            foreach ($rows['items'] as $k=> $item) {
                $rows['items'][$k]['shop_name'] = $shopRows[$item['shop_id']]['shop_name'];
                if($item['status'] == 0 && strtotime($item['end_time']) < time())
                {
                    $this->editBase($item['id'],[
                            'status'=>0
                        ]);
                }
            }
        }
        return $rows;
    }
    //删除活动
    public function removePinTuanActItem($PinTuanId)
    {
        $rs_row                   = array();
        
        //删除活动详情
        $this->removePinTuanDetail = new PinTuan_Detail();
        $flag1 = $this->removePinTuanDetail->removePinTuanDetail($PinTuanId);
        check_rs($flag1, $rs_row);

        //删除活动
        $del_flag = $this->remove($PinTuanId);  //删除活动本身
        check_rs($del_flag, $rs_row);

        return is_ok($rs_row);
    }

    /**
     * 获取活动和详情
     * @return type
     */
    public function getDetailList($where = array()){
        $where_default = array(
            'status'=>self::$statusEnabled,
            'start_time:<'=>date('Y-m-d H:i:s'),
            'end_time:>'=>date('Y-m-d H:i:s')
        );
        $where = !$where ? $where_default : $where;
        $pt = $this->getByWhere($where);
        $detail_model = new PinTuan_Detail();
        $result = $detail_model->getDetailByList($pt);
        return $result;
    }

    /**
     * 根据拼团ID获取参团人数
     */
    public function getSaleNumsByPtId($id){
        $list = $this->getBase($id);
        $detail_model = new PinTuan_Detail();
        $detail_list = $detail_model->getDetailByList($list);
        $detail_id = array();
        foreach ($detail_list as $value){
            $detail_id[] = $value['detail']['id'];
        }
        $num = $detail_model->getSaleNumsByDetailId($detail_id);
        return $num;
    }

    /**
     * 获取活动和详情
     * @return type
     */
    public function getPinTuanDetailList($pintuan_id){
        $where = array(
            'id'=>$pintuan_id
        );
        $pt = $this->getOneByWhere($where);
        $detail_model = new PinTuan_Detail();
        $pt_detail = $detail_model->getByWhere(array('pintuan_id'=>$pt['id']));
        foreach ($pt_detail as $val){
                $pt['detail'] = $val;
        }
        return $pt;
    }


    public function getActiveIds($shop_id)
    {
        //加价购商品、换购商品
        $increase_base_model = new Increase_BaseModel();
        $increase_where = array(
            'shop_id'=>$shop_id,
            'increase_state'=>1
        );
        $increase_list = $increase_base_model->getIncreaseByWhere($increase_where);
        if(is_array($increase_list))
        {
            $increase_ids = array_column($increase_list, 'increase_id');
        }
        
        $increase_goods_model = new Increase_GoodsModel();
        $increase_goods = $increase_goods_model->getIncreaseGoodsByWhere(array('increase_id:IN'=>$increase_ids));
        if(is_array($increase_goods))
        {
            $increase_common_id = array_column($increase_goods,'common_id');
            $increase_goods_id = array_column($increase_goods,'goods_id');
        }

        $increase_redemp_goods = new Increase_RedempGoodsModel();
        $redemp_goods_list = $increase_redemp_goods->getIncreaseRedempGoodsByWhere(array('increase_id:IN'=>$increase_ids));
        if(is_array($redemp_goods_list))
        {
            $increase_redemp_goods_id = array_column($redemp_goods_list,'goods_id');
        }

        //团购商品
        $groupbuy_base_model = new GroupBuy_BaseModel();
        $group_where = array(
                'shop_id' => $shop_id,
                'groupbuy_endtime:>' => date('Y-m-d H:i:s'),
                'groupbuy_state:IN' => array(1, 2, 4) //团购状态:审核中 正常 审核失败
            );
        $groupbuy_list = $groupbuy_base_model->getGroupbuyList($group_where);
        if(is_array($groupbuy_list))
        {
            $groupbuy_goods_id = array_column($groupbuy_list, 'goods_id');
            $groupbuy_common_id = array_column($groupbuy_list, 'common_id');
        }

        //限时折扣
        $discount_base_model = new Discount_BaseModel();
        $discount_where = array(
                'shop_id'=>$shop_id,
                'discount_state'=>1
            );
        $discount_list = $discount_base_model->getDiscountActList($discount_where);
        if(is_array($discount_list['items']))
        {
            $discount_id = array_column($discount_list['items'], 'discount_id');
        }
        $discount_goods_model = new Discount_GoodsModel();
        $discount_goods_list = $discount_goods_model->getDiscountGoodsByWhere(array('discount_id:IN'=>$discount_id));
        if(is_array($discount_goods_list))
        {
            $discount_goods_id = array_column($discount_goods_list, 'goods_id');
        }

        //满即送
        $mansong_base_model = new ManSong_BaseModel();
        $mansong_where = array(
                'shop_id'=>$shop_id,
                'mansong_state'=>1
            );
        $mansong_list = $mansong_base_model->getManSongByWhere($mansong_where);
        if(is_array($mansong_list['items']))
        {
            $mansong_id = array_column($mansong_list['items'], 'mansong_id');
        }
        $mansong_rule_model = new ManSong_RuleModel();
        $mansong_rule_list = $mansong_rule_model->getManSongRuleByWhere(array('mansong_id:IN'=>$mansong_id));
        if(is_array($mansong_rule_list))
        {
            $mansong_goods_id = array_column($mansong_rule_list, 'goods_id');
        }

        //正在拼团的商品
        $pintuan_where = array(
                'shop_id'=>$shop_id,
                'status'=>1
            );
        $pintuan_list = $this->getPinTuanList($pintuan_where,array('id'=>'DESC'));
        if(is_array($pintuan_list))
        {
            $pintuan_id = array_column($pintuan_list['items'], 'id');
        }
        $pintuan_detail_model = new PinTuan_Detail();
        $pintuan_detail_list = $pintuan_detail_model->getPinTuanDetail(array('pintuan_id:IN'=>$pintuan_id));
        if(is_array($pintuan_detail_list))
        {
            $pintuan_goods_id = array_column($pintuan_detail_list, 'goods_id');
        }

        //砍价商品
        $Bargain_BaseModel = new Bargain_BaseModel();
        $bargain_goods_ids = $Bargain_BaseModel->getBargainGoodsIds();

        //所有参加活动商品
        $goods_id = array_unique(array_merge($increase_goods_id,$increase_redemp_goods_id,$groupbuy_goods_id,$discount_goods_id,$mansong_goods_id,$pintuan_goods_id, $bargain_goods_ids));
        $common_id = array_unique(array_merge($increase_common_id,$groupbuy_common_id));
        $ids['goods_id'] = $goods_id;
        $ids['common_id'] = $common_id;
        return $ids;
    }

    /**
     * 检查商品是否为拼团商品
     * @param $goods_id int
     * @return boolean
     */
    public function isPinTuanGoods($goods_id)
    {
        $pinModel = new PinTuan_Base;
        $nowDate = date('Y-m-d H:i:s');
        //活动中的商品
        $pinList1 = $pinModel->getByWhere([
            'status'=> PinTuan_Base::$statusEnabled,
            'start_time:<='=> $nowDate,
            'end_time:>='=> $nowDate,
        ]);
        //即将参加活动的商品
        $pinList2 = $pinModel->getByWhere([
            'status'=> PinTuan_Base::$statusEnabled,
            'start_time:>='=> $nowDate,
        ]);
        $pinList = array_merge($pinList1,$pinList2);

        if (empty($pinList)) { //没有拼团
            return false;
        }

        $pinIds = array_column($pinList, 'id');
        $pinDetailModel = new PinTuan_Detail;

        $pinDetails = $pinDetailModel->getByWhere([
            'pintuan_id:IN'=> $pinIds,
            'goods_id'=> $goods_id
        ]);

        return empty($pinDetails)
            ? false
            : true;
    }

    /**
     * 商品违规下架后，活动结束
     * @param $common_id int
     * @return boolean
     */
    public function cancelByCommonId($common_id)
    {
        $pinModel = new PinTuan_Base;
        $nowDate = date('Y-m-d H:i:s');
        $pinList = $pinModel->getByWhere([
            'status'=> PinTuan_Base::$statusEnabled,
            'start_time:<='=> $nowDate,
            'end_time:>='=> $nowDate,
        ]);
        if (empty($pinList)) { //没有拼团
            return true;
        }

        $goodsModel = new Goods_BaseModel;
        $goodsList = $goodsModel->getByWhere([
            'common_id'=> $common_id
        ]);

        $goodsIds = array_keys($goodsList);

        $pinIds = array_column($pinList, 'id');
        $pinDetailModel = new PinTuan_Detail;

        $pinDetailList = $pinDetailModel->getByWhere([
            'pintuan_id:IN'=> $pinIds,
            'goods_id:IN'=> $goodsIds
        ]);

        if (empty($pinDetailList)) { //没有相关商品才加拼团
            return true;
        }

        $pinTuanIds = array_column($pinDetailList, 'pintuan_id');
        $flag = $pinModel->editBase($pinTuanIds, [
            'status'=> PinTuan_Base::$statusDisabled
        ]);

        return $flag === false
            ? false
            : true;
    }

    //wap模板首页拼团活动商品
    public function getTplPinTuanGoods($cond_row)
    {
        $pintuan_list = $this->getGoodsList($cond_row);
        $data = array();
        foreach($pintuan_list as $k=>$v)
        {
            $sql = "select count(*) 'buyer_count' from yf_pintuan_buyer where detail_id = ".$v['detail']['id'];
            $rows = $this -> sql -> getAll($sql);
            $data[$k]['pintuan_name'] = $v['name'];
            $data[$k]['goods_name'] = $v['goods']['goods_name'];
            $data[$k]['goods_image'] = $v['goods']['goods_image'];
            $data[$k]['price'] = $v['detail']['price'];
            $data[$k]['price_one'] = $v['detail']['price_one'];
            $data[$k]['person_num'] = $v['person_num'];
            $data[$k]['detail_id'] = $v['detail']['id'];
            $data[$k]['goods_id'] = $v['detail']['goods_id'];
            $data[$k]['price_ori'] = $v['detail']['price_ori'];
            $data[$k]['pintuan_id'] = $v['id'];
            $data[$k]['buyer_count'] = $rows[0]['buyer_count'];
        }
        return $data;
    }

    //拼团活动商品列表
    public function getPinTuanGoods($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        $goods_id_row = array();
        $rows = $this->listByWhere($cond_row, $order_row, $page, $rows);
        $PinTuan_Detail = new PinTuan_Detail();
        foreach ($rows['items'] as $key => $value)
        {
            if (strtotime($value['end_time']) < time())
            {
                $rows['items'][$key]['status'] = 0;

                $field_row['status'] = 0;
                $this->editBase($value['id'], $field_row);
            }
            $data = $PinTuan_Detail->getGoodsIdById($value['id']);
            $rows['items'][$key]['pintuan_goods'] = $data['goods_info'];
            $rows['items'][$key]['pintuan_price'] = $data['detail_info']['price'];
        }
        return $rows;
    }

    //首页版块中补齐拼团商品信息
    public function getOpenPinTuan($pintuan_id,$num)
    {
        //拼团商品
        $sql = "select
                   a.id as pintuan_id,a.name as pintuan_name,a.person_num,b.goods_id,b.price,b.price_one,b.price_ori,b.id as detail_id,c.goods_name,c.goods_image as goods_image
                from
                    `yf_pintuan` a left join `yf_pintuan_detail` b on a.id = b.pintuan_id left join `yf_goods_base` c on b.goods_id = c.goods_id
                where
                  a.status = 1 and a.id not in (" . implode(',', $pintuan_id) . ") and a.start_time < '". date('Y-m-d H:i:s',time())."' and a.end_time > '".date('Y-m-d H:i:s',time())."'  order by a.id limit ".$num;
        $rows = $this -> sql -> getAll($sql);
        foreach($rows as $k=>$v)
        {
            $sql2 = "select count(*) 'buyer_count' from yf_pintuan_buyer where detail_id = " . $v['detail_id'];
            $count = $this->sql->getAll($sql2);
            $rows[$k]['buyer_count'] = $count[0]['buyer_count'];
        }
        return $rows;
    }

    //获取当前店铺正在、即将参加拼团活动的商品id
    public function getPinTuanGoodsIds()
    {
        $sql = "SELECT pintuan_detail.goods_id FROM ";
        $sql .= TABEL_PREFIX . "pintuan AS pintuan JOIN ";
        $sql .= TABEL_PREFIX . "pintuan_detail AS pintuan_detail ON ";
        $sql .= "pintuan.id = pintuan_detail.pintuan_id ";
        $sql .= "where 1";
        $sql .= " AND pintuan.status = 1 AND pintuan.end_time > '" . date('Y-m-d H:i:s',time()) ."'";
        $sql .= " AND pintuan.shop_id = " . Perm::$shopId;
        $result = $this->sql->getAll($sql);
        $goods_ids = array_column($result,'goods_id');
        return array_unique($goods_ids);
    }

}