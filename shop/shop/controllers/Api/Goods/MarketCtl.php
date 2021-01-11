<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}
class Api_Goods_MarketCtl extends Api_Controller
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

    }
    //获取商品列表
    public function goodslist(){
        $getdata=[];
        $getdata['cat_id']=request_int('cat_id');//类别id
        $getdata['brand_id'] = request_int('brand_id');//商品品牌
        $getdata['common_id'] = request_int('common_id'); //商品common_id
        $getdata['keywork'] = request_string('keywork');
        $getdata['searkeywords'] = request_string('searkeywords');
        $getdata['search_property'] = request_row('search_property');
        $getdata['property_id'] = request_int('property_id');
        $getdata['property_value_id'] = request_int('property_value_id');
        $getdata['user_id'] = request_int('uid');
        $getdata['shop_id'] = request_int('shop_id');
        $getdata['page'] = request_int('page')?request_int('page'):1;


        $cat_id = $getdata['cat_id'];
        $brand_id =  $getdata['brand_id'];
        $com_id = $getdata['common_id'];
        $search =$getdata['keywork'];
        $searchkey = $getdata['searkeywords'];
        $cond_row = array();
        if($getdata['shop_id']){
            $cond_row['shop_id'] = $getdata['shop_id'];
        }
        $Goods_CommonModel = new Goods_CommonModel();

        $Yf_Page           = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows              = $Yf_Page->listRows;
        $page              =  $getdata['page'];

        //分类id
        $Goods_CatModel = new Goods_CatModel();
        //分类id
        if ($cat_id)
        {
            //查找该分类下所有的子分类
            $cat_list   = $Goods_CatModel->getCatChildId($cat_id);
            $cat_list[] = $cat_id;
            //查找该分类的父级分类
//            $parent_cat_id = $Goods_CatModel->getCatParentTree($cat_id);
            $cond_row['cat_id:IN'] = $cat_list;
        }
        //商品品牌
        if ($brand_id)
        {
            $cond_row['brand_id'] = $brand_id;
        }
        //商品common_id
        if ($com_id)
        {
            $cond_row['common_id:IN'] = $com_id;
        }
        //商品的配送区域
        //获取默认区域
        if(!isset($_COOKIE['area'])) {
            $cookid_area = $this->getCookieArea();
        } else {
            $Base_DistrictModel = new Base_DistrictModel();
            $dist = current($Base_DistrictModel->getByWhere(array('district_name'=>$_COOKIE['area'])));
            setcookie("goodslist_area_id", $dist['district_id']);
            $cookid_area = $Base_DistrictModel->getCookieDistrictName($dist['district_name'],2);
            setcookie("goodslist_area_name", $cookid_area['area']);
        }
        $transport_id = request_string('transport_id', isset($cookid_area['city']['id']) ? $cookid_area['city']['id'] : '');
        if($transport_id > 0){
            $Transport_AreaModel = new Transport_AreaModel();
            $transport_area_list = $Transport_AreaModel->getAreaTemplate($transport_id);
            if($transport_area_list){
                $transport_area_id = array_column($transport_area_list,'id');
                $transport_area_id[] = 0;
                $cond_row['transport_area_id:IN'] = $transport_area_id;
            }
        }


        $price_from = request_float('price_from');
        if ($price_from) {
            $cond_row['common_price:>='] = $price_from;
        }
        $price_to = request_float('price_to');
        if ($price_to) {
            $cond_row['common_price:<='] = $price_to;
        }
        //销量，价格，评论数
        $order_row = array();
        $act       = request_string('act');
       $order_row['common_id'] = 'DESC';
        if ($act)
        {
            //价格
            if ($act == 'up_price')
            {
                    $order_row['common_price'] = 'ASC';
                asort($order_row);

            }
            if ($act == 'low_price')
            {
                $order_row['common_price'] = 'desc';
                arsort($order_row);

            }
            //评论数
            if ($act == 'evaluate')
            {
                $order_row['common_evaluate'] = "desc";
                arsort($order_row);
            }
            //销量
            if ($act === 'sale') {
                $order_row['common_salenum'] = "desc";
                arsort($order_row);
            }
        }

        $sear_row=array();
        if($searchkey)
        {
            $sear_row[] = '%'.$searchkey.'%';
        }
        if ($search)
        {
            $sear_row[] = '%' . $search . '%';
            //记录搜索关键词
            $Search_WordModel                  = new Search_WordModel();
            $search_cond_row['search_keyword'] = $search;

            $search_row = $Search_WordModel->getSearchWordInfo($search_cond_row);

            if ($search_row)
            {
                $search_data                = array();
                $search_data['search_nums'] = $search_row['search_nums'] + 1;
                $flag = $Search_WordModel->editSearchWord($search_row['search_id'], $search_data);
            }
            else
            {
                $search_data                      = array();
                $search_data['search_keyword']    = $search;
                $search_data['search_char_index'] = Text_Pinyin::pinyin($search, '');
                $search_data['search_nums']       = 1;
                $flag                             = $Search_WordModel->addSearchWord($search_data);
            }
        }
        if($sear_row){
            $cond_row['common_name:LIKE'] = $sear_row;
        }
        $cond_row['shop_status'] = Shop_BaseModel::SHOP_STATUS_OPEN;

        $virtual = request_float('isvirtual');
        if($virtual)
        {
            $cond_row['common_is_virtual'] = Goods_CommonModel::GOODS_VIRTUAL;
        }

        //供货商商品
        $cond_row['product_distributor_flag'] = 1;


        //判断是否有属性
        $property_value_row       = array();
        $cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
        $cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;


        //$redata = [$cond_row, $order_row, $page, $rows, $property_value_row];
        $data  = $Goods_CommonModel->getGoodsList($cond_row, $order_row, $page, $rows, $property_value_row);
        //计算折扣比例
        if(isset($data['items'])) {
            foreach ($data['items'] as $k => $v) {
                if ($v['goods_recommended_min_price'] > $v['common_price'] && $v['goods_recommended_max_price'] > $v['goods_recommended_min_price']) {
                    $data['items'][$k]['goods_min_interest_rate'] = number_format(($v['goods_recommended_min_price'] - $v['common_price']) / $v['goods_recommended_min_price'] * 100, 2, '.', '');
                    $data['items'][$k]['goods_max_interest_rate'] = number_format(($v['goods_recommended_max_price'] - $v['common_price']) / $v['goods_recommended_max_price'] * 100, 2, '.', '');
                }
            }
        }

        $Seller_BaseModel = new Seller_BaseModel();
        $seller_rows = $Seller_BaseModel->getByWhere(['user_id' => $getdata['user_id']]);
        if ($seller_rows) {
            $redata['shop_id_row'] = array_column($seller_rows, 'shop_id');
            $redata['shop_id'] = current($redata['shop_id_row']);
        } else {
            $redata['shop_id'] = 0;
        }

        //分销商折扣
        if($redata['shop_id']){
            $shopBaseModel = new Shop_BaseModel();
            $shop_info = $shopBaseModel -> getOne($redata['shop_id']);
            $shopDistributorModel = new Distribution_ShopDistributorModel();
            $shopDistributorLevelModel = new Distribution_ShopDistributorLevelModel();

            //所有供货商，用于对商品操作的判断
            $suppliers = $shopDistributorModel->getByWhere([
                'distributor_id' =>$redata['shop_id'],
                'distributor_enable'=> 1 //是否审核通过: 0-待审核;  1-通过;-1未通过
            ]);
            $suppliers  = array_column($suppliers,'shop_id');
            //当前分销商已分销的商品
            $dist_goods = $Goods_CommonModel->getByWhere(array("shop_id" => $redata['shop_id'],"common_parent_id:>" => 0));
            $common_ids = array_column($dist_goods,'common_parent_id');
            //查看折扣，改变对应供销商商品显示的价格
            foreach ($data['items'] as $key => $value) {
                $shopDistributorInfo     =  $shopDistributorModel->getOneByWhere(array('shop_id' =>$value['shop_id'],'distributor_id'=>$redata['shop_id']));
                if(!empty($shopDistributorInfo)){
                    $distritutor_rate_info     = $shopDistributorLevelModel->getOne($shopDistributorInfo['distributor_level_id']);
                    if(@$distritutor_rate_info['distributor_leve_discount_rate']){
                        $data['items'][$key]['discount_common_price'] = $value['common_price']*$distritutor_rate_info['distributor_leve_discount_rate']/100;
                    }
                }
                if(isset($suppliers)&&!in_array($value['shop_id'],$suppliers)){
                   $apply = true;//申请供应商
                }elseif(isset($suppliers) && in_array($value['shop_id'],$suppliers) && $value['product_is_behalf_delivery']!=1){
                    $add_cart = true;//直接购买
                }elseif($value['product_is_behalf_delivery'] && isset($suppliers) && in_array($value['shop_id'],$suppliers)){
                    if(isset($common_ids) && in_array($value['common_id'],$common_ids)){
                        $distribution = true; //已分销
                    }else{
                        $shelf = true; //一键上架
                    }
                }
                $data['items'][$key]['apply'] = $apply;
                $data['items'][$key]['add_cart'] = $add_cart;
                $data['items'][$key]['distribution'] = $distribution;
                $data['items'][$key]['shelf'] = $shelf;
                unset($apply);
                unset($add_cart);
                unset($distribution);
                unset($shelf);
            }
        }
      return  $this->data->addBody(-140, $data);
    }
    /**
     * 获取地区并设置cookie
     * @return type
     */
    private function getCookieArea(){
        if(!isset($_COOKIE['goodslist_area_id'])) {
            $ip = get_ip();
            $Sub_SiteModel = new Sub_SiteModel();
            $area_array = $Sub_SiteModel->getIPLoc_sina_new($ip);
            $district = $Sub_SiteModel->areaConvert($area_array);
            if(!$district['province']){
                //默认数据
                setcookie("goodslist_area_id", 143);
                setcookie("goodslist_area_name", '上海 黄浦区');
                $cookid_area = array();
                $cookid_area['area'] = '上海 黄浦区';
                $cookid_area['city']['id'] = 143;
            }else{
                $Base_DistrictModel = new Base_DistrictModel();
                if(in_array($district['province'], array('北京','上海','重庆','天津'))){
                    $cookid_area = $Base_DistrictModel->getCookieDistrictName($district['province'],2);
                    setcookie("goodslist_area_id", $cookid_area['city']['id']);
                    setcookie("goodslist_area_name", $cookid_area['area']);
                }else{
                    $area_info = $Base_DistrictModel->getDistrictDetailByName($district['province'].' '.$district['city']);
                    $cookid_area = array(
                        'area'=>$district['province'].' '.$district['city'],
                        'provice'=>array('id'=>$area_info[0]['district_id'],'name'=>$area_info[0]['district_name']),
                        'city'=> array('id'=>$area_info[1]['district_id'],'name'=>$area_info[1]['district_name'])
                    );
                    setcookie("goodslist_area_id", $area_info[1]['district_id']);
                    setcookie("goodslist_area_name", $cookid_area['area']);
                }
            }
        } else {
            $cookid_area = array();
            $cookid_area['area'] = $_COOKIE['goodslist_area_name'];
            $cookid_area['city']['id'] = $_COOKIE['goodslist_area_id'];
        }
        return $cookid_area;
    }


    public function goods(){
        $goods_id        = request_int('good_id');
        $cid = request_int('cid');
        $Goods_CommonModel = new Goods_CommonModel();
        //如果传递过来的是common_id，则从此common_id中的goods_id中选择一个有效的goods_id
        if ($cid && !$goods_id) {
            $cond_row = array();
            $cond_row['common_id'] = $cid;
            $datas = $Goods_CommonModel->getGoodsList($cond_row);

            $goods_id = $datas['items'][0]['goods_id'];
        }
        //添加商品点击数
        $Goods_BaseModel = new Goods_BaseModel();
        $good_click_row  = array('goods_click' => '1');
        $Goods_BaseModel->editBase($goods_id, $good_click_row, true);
        //1.商品信息（商品活动信息，评论数，销售数，咨询数）
        $goods_detail    = $Goods_BaseModel->getGoodsDetailInfoByGoodId($goods_id);
        //检测商品状态
        $goods_check     = $Goods_BaseModel->checkGoodsII($goods_id);
        if (!$goods_detail || !$goods_check)
        {
            //返回错误
           return $this->data->addBody(140, [], '抱歉，该商品已下架或者该店铺已关闭！', 404);
        }else {
            //添加用户足迹
            $user_id = request_int('uid');
            $User_FootprintModel = new User_FootprintModel();
            //先判断该用户是否浏览过该商品
            $foot_cond_row['user_id'] = $user_id;
            $foot_cond_row['common_id'] = $goods_detail['goods_base']['common_id'];
            $foot_id = $User_FootprintModel->getKeyByWhere($foot_cond_row);

            //如果用户曾经浏览过该商品则修改浏览时间
            if ($foot_id) {
                $edit_foot_row = array();
                $edit_foot_row['footprint_time'] = get_date_time();
                $edit_foot_row['footprint_date'] = date('Y-m-d');
                $User_FootprintModel->editFootprint($foot_id, $edit_foot_row);
            } else {
                //如果没有浏览过改商品则插入数据
                $read_add_row = array();
                $read_add_row['user_id'] = $user_id;
                $read_add_row['common_id'] = $goods_detail['goods_base']['common_id'];
                $read_add_row['footprint_time'] = get_date_time();
                $read_add_row['footprint_date'] = date('Y-m-d');
                $User_FootprintModel->addFootprint($read_add_row);
            }
            $Goods_CatModel = new Goods_CatModel();
            //查找该分类的父级分类
            $parent_cat = $Goods_CatModel->getCatParent($goods_detail['goods_base']['cat_id']);
            $cat_info = $Goods_CatModel->getOne($goods_detail['goods_base']['cat_id']);
            if ($cat_info) {
                $cat_info['ext'] = 1;
                $parent_cat[] = $cat_info;
            }

            //判断此商品是否被关注过
            $User_FavoritesGoodsModel = new User_FavoritesGoodsModel();
            $user_favorites_goods_row['user_id'] = $user_id;
            $user_favorites_goods_row['goods_id'] = $goods_detail['goods_base']['goods_id'];
            $user_favorites_goods = $User_FavoritesGoodsModel->getKeyByWhere($user_favorites_goods_row);
            if ($user_favorites_goods) {
                $isFavoritesGoods = true;
            } else {
                $isFavoritesGoods = false;
            }
            //计算商品的销售数量1.直接显示本件商品的销售数量，2.显示本类common商品的销售数量

            $common_goods = $Goods_BaseModel->getByWhere(array('common_id' => $goods_detail['goods_base']['common_id']));
            $count_sale = 0;
            foreach ($common_goods as $comkey => $comval) {
                $count_sale += $comval['goods_salenum'];
            }
            $goods_detail['goods_base']['count_sale'] = $count_sale;

            //获取商品所在地
            $Base_DistrictModel = new Base_DistrictModel();
            $goods_location_row = $Base_DistrictModel->getByWhere(array('district_id:IN' => $goods_detail['common_base']['common_location']));
            $goods_location = '';
            if ($goods_location_row) {
                $goods_location_row = array_values($goods_location_row);
                foreach ($goods_location_row as $localkey => $localval) {
                    $goods_location .= $localval['district_name'] . '  ';
                }
            }

            //分销商折扣
            $shopBaseModel = new Shop_BaseModel();
            $shop_info = $shopBaseModel -> getOneByWhere(['user_id'=>$user_id]);
            if($shop_info['id']){
                $shopDistributorModel = new Distribution_ShopDistributorModel();
                $shopDistributorLevelModel = new Distribution_ShopDistributorLevelModel();

                $suppliers = $shopDistributorModel->getByWhere([//所有供货商，用于对商品操作的判断
                    'distributor_id' =>$shop_info['id'],
                    'distributor_enable'=> 1 //是否审核通过: 0-待审核;  1-通过;-1未通过
                ]);
                $suppliers  = array_column($suppliers,'shop_id');

                //查看折扣，改变商品价格
                $shopDistributorInfo     =  $shopDistributorModel->getOneByWhere(array('shop_id' =>$goods_detail['goods_base']['shop_id'],'distributor_id'=>$shop_info['id']));
                if(!empty($shopDistributorInfo)){
                    $distritutor_rate_info     = $shopDistributorLevelModel->getOne($shopDistributorInfo['distributor_level_id']);
                    if(@$distritutor_rate_info['distributor_leve_discount_rate'] > 0){
                        $goods_detail['goods_base']['goods_price'] =  $goods_detail['goods_base']['goods_price']*$distritutor_rate_info['distributor_leve_discount_rate']/100;
                    }
                }
            }
            //当前分销商已分销的商品
            $dist_goods = $Goods_CommonModel->getByWhere(array("shop_id" => $shop_info['id'], "common_parent_id:>" => 0));
            $common_ids = array_column($dist_goods, 'common_parent_id');

            //2.店铺信息
            $Shop_BaseModel = new Shop_BaseModel();
            $shop_detail = $Shop_BaseModel->getShopDetail($goods_detail['goods_base']['shop_id']);

            //查找该店铺下的实体店铺
            $Shop_EntityModel = new Shop_EntityModel();
            $entity_shop = $Shop_EntityModel->getByWhere(array("shop_id" => $goods_detail['goods_base']['shop_id']));

            //检测商品是否已经下架
            $goods_status = 1;
            if ($goods_detail['goods_base']['goods_is_shelves'] != Goods_BaseModel::GOODS_UP || $goods_detail['common_base']['common_state'] != Goods_CommonModel::GOODS_STATE_NORMAL) {
                $goods_status = 0;
            }
            //检查是否为店主本人
            $shop_owner = 0;

            if ($shop_detail['shop_id'] == $shop_info['id'] || $shop_detail['user_id'] == $shop_info['id']) {
                $shop_owner = 1;
            }

            //判断是否可以门店自提
            $Chain_GoodsModel = new Chain_GoodsModel();
            $chain_row['shop_id:='] = $goods_detail['goods_base']['shop_id'];
            $chain_row['goods_id:='] = $goods_id;
            $chain_row['goods_stock:>'] = 0;

            $chain_goods = $Chain_GoodsModel->getByWhere($chain_row);

            $goods_detail['chain_stock'] = 0;

            if ($chain_goods) {
                $goods_detail['chain_stock'] = 1;
            }

            //如果使用售卖区域（现在商品表中暂时没有字段表面售卖区域）
            $IsHaveBuy = 0;
            if ($user_id)
            {
                //团购商品是否已经开始
                //查询该用户是否已购买过该商品
                $Order_GoodsModel                          = new Order_GoodsModel();
                $order_goods_cond['common_id']             = $goods_detail['goods_base']['common_id'];
                $order_goods_cond['buyer_user_id']         = $user_id;
                $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_REFUND_FINISH;
                $order_list                                = $Order_GoodsModel->getByWhere($order_goods_cond);

                $order_goods_count = count($order_list);

                if (isset($goods_detail['goods_base']['promotion_type']))
                {
                    $promotion_type = $goods_detail['goods_base']['promotion_type'];

                    if ($promotion_type == 'groupbuy')
                    {
                        //检测是否限购数量
                        $upper_limit = $goods_detail['goods_base']['upper_limit'];
                        if ($upper_limit > 0 && $order_goods_count >= $upper_limit)
                        {
                            $IsHaveBuy = 1;
                        }
                    }
                }


                //商品限购数量判断
                if ($goods_detail['common_base']['common_limit'] > 0 && $order_goods_count >= $goods_detail['common_base']['common_limit'])
                {
                    $IsHaveBuy = 1;
                }

            }


            //计算限购数量
            if (isset($goods_detail['goods_base']['upper_limit']))
            {
                if ($goods_detail['goods_base']['upper_limit'] && $goods_detail['common_base']['common_limit'])
                {
                    if ($goods_detail['goods_base']['upper_limit'] >= $goods_detail['common_base']['common_limit'])
                    {
                        $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit'];
                    }
                    else
                    {
                        $goods_detail['buy_limit'] = $goods_detail['goods_base']['upper_limit'];
                    }
                }
                elseif ($goods_detail['goods_base']['upper_limit'] && !$goods_detail['common_base']['common_limit'])
                {
                    $goods_detail['buy_limit'] = $goods_detail['goods_base']['upper_limit'];
                }
                elseif (!$goods_detail['goods_base']['upper_limit'] && $goods_detail['common_base']['common_limit'])
                {
                    $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit'];
                }
                else
                {
                    $goods_detail['buy_limit'] = 0;
                }
            }
            else
            {
                $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit'];
            }

            $data = array();
            $spec_list = array();
            $spec_image = array();
            $store_info = array();
            $mansong_info = array();
            $common_id = $goods_detail['common_base']['common_id'];

            //商品详情
            $goods_info = array_merge($goods_detail['common_base'], $goods_detail['goods_base']);
            if ($goods_info['goods_recommended_min_price'] > $goods_info['common_price'] && $goods_info['goods_recommended_max_price'] > $goods_info['goods_recommended_min_price']) {
                $data['goods_min_interest_rate'] = number_format(($goods_info['goods_recommended_min_price'] - $goods_info['common_price']) / $goods_info['goods_recommended_min_price'] * 100, 2, '.', '');
                $data['goods_max_interest_rate'] = number_format(($goods_info['goods_recommended_max_price'] - $goods_info['common_price']) / $goods_info['goods_recommended_max_price'] * 100, 2, '.', '');
            }
            //好评率
            $Goods_EvaluationModel = new Goods_EvaluationModel();
            $all_count = $Goods_EvaluationModel->countEvaluation($common_id, 'all');
            $good_count = $Goods_EvaluationModel->countEvaluation($goods_detail['common_base']['common_id'], 'good');
            if ($all_count != 0) {
                $good_pre = round($good_count / $all_count * 100);
            } else {
                $good_pre = 100;
            }
            //配送信息
            $goods_hair_info['content'] = $goods_detail['shop_base']['shipping'];
            $goods_hair_info['if_store_cn'] = empty($goods_detail['goods_base']['goods_stock']) ? '无货' : '有货';
            $goods_hair_info['if_store'] = empty($goods_detail['goods_base']['goods_stock']) ? false : true;
            $goods_hair_info['area_name'] = '全国';

            //图片信息
            if (isset($goods_detail['goods_base']['image_row']) && !empty($goods_detail['goods_base']['image_row'])) {
                $images_list = array_column($goods_detail['goods_base']['image_row'], 'images_image');
                $images_list = array_map(function ($img) {
                    return image_thumb($img, 360, 360);
                }, $images_list);
                $goods_image = implode(',', $images_list);
            } else {
                $goods_image = $goods_detail['goods_base']['goods_image'];
            }

            //满送
            $mansong_info = $goods_detail['mansong_info'];

            if (!empty($goods_detail['common_base']['common_spec_name'])) {
                //商品规格
                $spec_list = $Goods_BaseModel->createSGIdByWap($goods_detail['common_base']['common_id']);

                //商品规格颜色图
                if (!empty($goods_detail['common_base']['common_spec_value_color'])) {
                    $spec_image = $goods_detail['common_base']['common_spec_value_color'];
                }
            }
            //店铺信息
            $store_info['is_own_shop'] = $shop_detail['shop_self_support'];
            $store_info['member_id'] = $shop_detail['user_id'];
            $store_info['member_name'] = $shop_detail['user_name'];
            $store_info['store_id'] = $shop_detail['shop_id'];
            $store_info['store_name'] = $shop_detail['shop_name'];
            $store_info['shop_logo'] = $shop_detail['shop_logo'];

            $store_credit = array();

            $store_credit['store_deliverycredit'] = array();
            $store_credit['store_deliverycredit']['credit'] = $shop_detail['shop_send_scores'];
            $store_credit['store_deliverycredit']['text'] = "物流";

            $store_credit['store_desccredit'] = array();
            $store_credit['store_desccredit']['credit'] = $shop_detail['shop_desc_scores'];
            $store_credit['store_desccredit']['text'] = "描述";

            $store_credit['store_servicecredit'] = array();
            $store_credit['store_servicecredit']['credit'] = $shop_detail['shop_service_scores'];
            $store_credit['store_servicecredit']['text'] = "服务";
            $store_info['store_credit'] = $store_credit;

            if($goods_info['common_spec_name']){
                $goods_info['default_common_spec_name'] = array_values($goods_info['common_spec_name']);
            }
            if($goods_info['goods_spec']){
                $goods_info['default_goods_spec'] = array_values($goods_info['goods_spec']);
            }
            if(empty($goods_info['image_row'])){
                $goods_info['image_row'][]['images_image'] = $goods_info['goods_image'];
            }
            $data['goods_detail'] = $goods_detail;
            $data['goods_id'] = $goods_id;
            $data['goods_info'] = $goods_info;                //商品详情
            $data['goods_hair_info'] = $goods_hair_info;        //售卖区域
            $data['goods_image'] = $goods_image;            //商品图片
            $data['mansong_info'] = $mansong_info;            //商品满送
            $data['spec_list'] = $spec_list;                //商品规格
            $data['spec_image'] = $spec_image;                //商品颜色
            $data['store_info'] = $store_info;                //店铺信息
            $data['buyer_limit'] = $goods_detail['buy_limit'];  //限购数量
            $data['is_favorate'] = $isFavoritesGoods;        //是否收藏过商品
            $data['shop_owner'] = $shop_owner;                //是否为店主
            $data['isBuyHave'] = $IsHaveBuy;                //是否已达限购数量
            $data['good_pre'] = $good_pre;                //好评率
            $this->data->addBody(-140, $data);
        }
      }

    //获取全部评论
    public function getGoodsEvaluationList()
        {
            $goods_id = request_int('goods_id');
            $goodsBaseModel = new Goods_BaseModel();
            $goods_base = $goodsBaseModel->getBase($goods_id);
            $goods_base = pos($goods_base);
            $common_id = $goods_base['common_id'];
            $type = request_string('type', 'all');
            $Goods_EvaluationModel = new Goods_EvaluationModel();
            //获取商品的评价信息
            $all_count = $Goods_EvaluationModel->countEvaluation($common_id, 'all');
            $img_count = $Goods_EvaluationModel->countEvaluation($common_id, 'image');
            $good_count = $Goods_EvaluationModel->countEvaluation($common_id, 'good');
            $middle_count = $Goods_EvaluationModel->countEvaluation($common_id, 'middle');
            $bad_count = $Goods_EvaluationModel->countEvaluation($common_id, 'bad');

            if ($all_count != 0) {
                $good_pre = round($good_count / $all_count * 100);
                $middle_pre = round($middle_count / $all_count * 100);
                $bad_pre = round($bad_count / $all_count * 100);
            } else {
                $good_pre = 100;
                $middle_pre = 100;
                $bad_pre = 100;
            }

            //获取商品的评价列表
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = 10;
            $page = request_int('curpage',1);
            $rows = request_int('page',2);

            $order_row = array();
            $cond_row['common_id'] = $common_id;

            $cond_row['status:!='] = Goods_EvaluationModel::DISPLAY;
            $order_row['status'] = 'DESC';
            $order_row['create_time'] = 'DESC';
            switch ($type) {
                    case 1:
                        $type = 'good';
                        break;
                    case 2:
                        $type = 'middle';
                        break;
                    case 3:
                        $type = 'bad';
                        break;
                    case 4:
                        $type = 'image';
                        break;
                    default:
                        $type = 'all';
                        break;
             }
            $data = $Goods_EvaluationModel->getEvaluationList($cond_row, $order_row, $page, $rows, $type);
            $data['items'] = array_values($data['items']);
            $Yf_Page->totalRows = $data['totalsize'];
            $this->data->addBody(-140, $data);
        }

    //获取售卖区域和运费
    public function getTramsportData()
    {
        $area_id = request_int('area_id');
        $common_id = request_int('common_id');
        $goodsBaseModel = new Goods_BaseModel();
        $result = $goodsBaseModel->getTransportInfo($area_id, $common_id);
        return $this->data->addBody(-140, $result);
    }







}