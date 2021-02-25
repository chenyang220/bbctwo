<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
    
    /**
     * @author     Zhuyt
     */
    class Goods_GoodsCtl extends Controller
    {
        
        public function __construct(&$ctl, $met, $typ)
        {
            parent::__construct($ctl, $met, $typ);
            
            $this->cart = new Buyer_CartCtl($ctl, $met, $typ);
            $this->initData();
            $this->web = $this->webConfig();
            $this->nav = $this->navIndex();
            $this->cat = $this->catIndex();
            $this->pinTuanMarkModel = new PinTuan_Mark();
            
        }
        public function lists(){
            $data = GoodSearch::pager();
            $recommend_row = $data['recommend_row'];
            $data['items'] = $data['items'];
            $page_nav = $data['pager'];
            //地区
            $transport_area =  request_string('transport_area');
            //判断是否显示自营店铺
            if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                $sub_site_id = $_COOKIE['sub_site_id'];
            }
            $self_shop_show_key = !$sub_site_id ? 'self_shop_show' : 'self_shop_show_' . $sub_site_id;
            // op
            $op1 = request_string('op1');
            $op2 = request_string('op2');
            $op3 = request_string('op3');
            if ('json' == $this->typ) {
                //重组data返回值
                $data['items'] = $this->reformData($data['items']);
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }

            
        }
        /**
         * 商品列表页
         *
         * @access public
         */
        public function goodslist()
        {
            $is_opensearch = Web_ConfigModel::value('Plugin_OpenSearch');
            $Goods_CommonModel = new Goods_CommonModel();
            if($is_opensearch){
                $open_search = request_string('keywords');
                $url = Yf_Registry::get('shop_api_url').'/shop/opensearch/demo/demo_search.php?hh='.urlencode($open_search);
                $gets = get_url($url);
                $common_open_ids = array_column($gets['result']['items'],'common_id');
                $cond_row = array();
                //查询分类品牌和分类关联属性
                $brand_property = $this->getBrandAndProperty();
                if (!empty($brand_property['common_ids'])) {
                    if (count($brand_property['common_ids']) == 1 && $brand_property['common_ids'][0] === false) {
                        $cond_row['common_id'] = -1;
                    } else {
                        $cond_row['common_id:IN'] = $brand_property['common_ids'];
                    }
                }
                if($cond_row['common_id:IN']){
                    $cond_row['common_id:IN'] = array_merge($cond_row['common_id'],$common_open_ids);
                }else{
                    $cond_row['common_id:IN'] = $common_open_ids;
                }
            }else{
                $cond_row = array();
                //查询分类品牌和分类关联属性
                $brand_property = $this->getBrandAndProperty();
                if (!empty($brand_property['common_ids'])) {
                    if (count($brand_property['common_ids']) == 1 && $brand_property['common_ids'][0] === false) {
                        $cond_row['common_id'] = -1;
                    } else {
                        $cond_row['common_id:IN'] = $brand_property['common_ids'];
                    }
                }
            }

            /*首页点击分类后 进入的属性分类排序*/
            $arr = $brand_property['property'];
            if(is_array($arr)){
                function my_sort($a,$b)
                {
                    if ($a['property_displayorder']==$b['property_displayorder']) return 0;
                    return ($a['property_displayorder']<$b['property_displayorder'])?-1:1;
                }
                $flag = uasort($arr,"my_sort");
            }
            /*首页点击分类后 进入的属性分类排序*/
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = request_int('rows', 12);
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            
            $wap_pagesize = request_int('pagesize');
            $pos = request_int('pos');
            if ($pos > 0) {
                $wap_pagesize = $wap_pagesize * (ceil($pos / $wap_pagesize)) + $wap_pagesize;
            }
            $wap_curpage = request_int('curpage');
            if (!empty($wap_pagesize)) {
                $rows = $wap_pagesize;
            }
            if (!empty($wap_curpage)) {
                $page = $wap_curpage;
            }
            //分类id
            $cat_id = request_int('cat_id');
            $Goods_CatModel = new Goods_CatModel();
            if ($cat_id) {
                //查找该分类下所有的子分类
                $cat_list = $Goods_CatModel->getCatChildId($cat_id);
                $cat_list[] = $cat_id;
                $cond_row['cat_id:IN'] = $cat_list;
            }
            $brand_property['property'] = $arr;
            //不显示供货商商品
            $shopBaseModel = new Shop_BaseModel();
            //shop_type 1.商家2.供应商

            $op4 = request_string('op4');
            if ($op4 && $op4 === 'distance') {
                //仅显示有货
                $lat = request_string('lat');
                $lng = request_string('lng');
                $shop_id_distance = $shopBaseModel->getNearShopNew($lat, $lng);
                $shop_id_distance_arr = array_column($shop_id_distance['items'],NULL, "shop_id");
                foreach ($shop_id_distance_arr as $key => $shop_id_distance) {
                    if ($shop_id_distance['shop_type'] == 1) {
                        $shop_list[] = $shop_id_distance;
                    }
                }
            } else {
                $shop_list = $shopBaseModel->getByWhere(array('shop_type' => 1));
            }
            $shop_ids = array_column($shop_list, 'shop_id');
            $cond_row['shop_id:IN'] = $shop_ids;
            //商品品牌
            $brand_id = request_row('brand_id');
            if (!is_array($brand_id)) {
                $brand_id = explode(',', $brand_id);
            }
            if ($brand_id) {
                $cond_row['brand_id:in'] = $brand_id;
            }
            //品牌
            $Goods_BrandModel = new Goods_BrandModel();
            $goods_brand = $Goods_BrandModel->listByWhere(array(),array(),1,8);
            $goods_brand = $goods_brand['items'];
            $goods_brand_all = $Goods_BrandModel->listByWhere(array(),array(),1,100);
            $goods_brand_all = $this->data_letter_sort($goods_brand_all['items'],'brand_initial');

            //推荐品牌
            $Goods_BrandModel = new Goods_BrandModel();
            $goods_brands = $Goods_BrandModel->getByWhere(array('brand_recommend'=>1));
            $goods_brands_all = $this->data_letter_sort($goods_brands,'brand_initial');
            //商品common_id
            $com_id = request_int('common_id');
            if ($com_id) {
                $cond_row['common_id:IN'] = $com_id;
            }
            
            //商品的配送区域
            //获取默认区域
            if (!isset($_COOKIE['area'])) {
                $cookid_area = $this->getCookieArea();
            } else {
                $Base_DistrictModel = new Base_DistrictModel();
                $dist = current($Base_DistrictModel->getByWhere(array('district_name' => $_COOKIE['area'])));
                setcookie("goodslist_area_id", $dist['district_id']);
                $cookid_area = $Base_DistrictModel->getCookieDistrictName($dist['district_name'], 2);
                setcookie("goodslist_area_name", $cookid_area['area']);
            }
             
            $transport_id = request_string('transport_id', isset($cookid_area['city']['id']) ? $cookid_area['city']['id'] : '');
            $transport_area = request_string('transport_area', isset($cookid_area['area']) ? $cookid_area['area'] : '请选择地区');

            //获取该售卖区域的所有模板
            if ($transport_id > 0) {
                $Transport_AreaModel = new Transport_AreaModel();
                $transport_area_list = $Transport_AreaModel->getAreaTemplate($transport_id);
                if ($transport_area_list) {
                    $transport_area_id = array_column($transport_area_list, 'id');
                    $transport_area_id[] = 0;
                    $cond_row['transport_area_id:IN'] = $transport_area_id;
                }
            }

            //pc分站
            if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                $sub_site_id = $_COOKIE['sub_site_id'];
                $pc_site = true;
            }

            //wap分站
            if (request_string('ua') === 'wap') {
                $sub_site_id = request_int('sub_site_id');
                $wap_site = true;
                unset($cond_row['transport_area_id:IN']);
            }

            //获取分站信息
            if (Web_ConfigModel::value('subsite_is_open') && isset($sub_site_id) && $sub_site_id > 0) {
                //获取站点信息
                $Sub_SiteModel = new Sub_SiteModel();
                $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
                if ($sub_site_district_ids) {
                    $cond_row['district_id:IN'] = $sub_site_district_ids;
                }
            }

            //商品搜索（总）
            //用户输入都是不靠谱的！
            if (preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is', $_REQUEST['keywords'])) {
                $_REQUEST['keywords'] = "";
            }
            $_REQUEST['keywords'] = $this->strFilter(strip_tags($_REQUEST['keywords']));
            $search = request_string('keywords');
            $searchkey = request_string('searkeywords');
            $sear_row = array();
            if ($searchkey) {
                $sear_row[] = '%' . $searchkey . '%';
            }
           
            if ($search) {
                /**
                 * 统计中心
                 */
//                $analytics_ip = get_ip();
//                $analytics_data = array(
//                    'keywords' => $search,
//                    'ip' => $analytics_ip,
//                    'date' => date('Y-m-d')
//                );
//                Yf_Plugin_Manager::getInstance()->trigger('analyticsKeywords', $analytics_data);
                /**********************************************************************/
                
                $sear_row[] = '%' . $search . '%';

                //记录搜索关键词 后台设置的关键词进行记录搜索次数
                $Search_WordModel                  = new Search_WordModel();
                $search_cond_row['search_keyword'] = $search;
    
                $search_row = $Search_WordModel->getSearchWordInfo($search_cond_row);
    
                if ($search_row)
                {
                    $search_data                = array();
                    $search_data['search_nums'] = $search_row['search_nums'] + 1;
    
                    $flag = $Search_WordModel->editSearchWord($search_row['search_id'], $search_data);
                }
                /*
                 *  为防止恶意刷热词，不再记录客户搜索热词，所有热词由后台管理员手动设置
                else
                {
                    $search_data                      = array();
                    $search_data['search_keyword']    = $search;
                    $search_data['search_char_index'] = Text_Pinyin::pinyin($search, '');
                    $search_data['search_nums']       = 1;
                    $flag                             = $Search_WordModel->addSearchWord($search_data);
                }
                 *
                 */
            }
            if ($sear_row&&!$is_opensearch) {
                $cond_row['common_name:LIKE'] = $sear_row;
            }
            $cond_row['shop_status'] = Shop_BaseModel::SHOP_STATUS_OPEN;
            
            //上架时间，销量，价格，评论数
            $order_row = array();
            $act = request_string('act');
            
            $actorder = strtolower(request_string('actorder', 'desc'));
            if ($actorder === 'desc') {
                $next_order = 'asc';
            } else {
                $next_order = 'desc';
            }
            
            //上架时间,这里用商品入库时间
            if ($act === 'all') {
                $order_row['common_id'] = $actorder;
            }
            //销量
            if ($act === 'sale') {
                $order_row['common_salenum'] = $actorder;
            }
            
            //销量
            if ($act === 'sale') {
                $order_row['common_salenum'] = $actorder;
            }
            
            //价格
            if ($act === 'price') {
                $order_row['common_price'] = $actorder;
            }
            
            //评论数
            if ($act === 'evaluate') {
                $order_row['common_evaluate'] = $actorder;
            }
            $label_id = request_string('label_id');
            //评论数
            if ($label_id) {
                $cond_row['label_id'] = $label_id;
            }
            $op1 = request_string('op1');
            $op2 = request_string('op2');
            $op3 = request_string('op3');

            if ($op1) {
                //仅显示有货
                if ($op1 === 'havestock') {
                    $cond_row['common_stock:>'] = 0;
                }
            }
            

            



            $actgoods = request_int('actgoods',0);
            if ($actgoods) {
                //仅显示促销商品
                $cond_row['common_is_xian'] = 1;
            }
            
            $own_shop = request_int('own_shop');
            $other_shop = request_int('other_shop');
            //自营店铺和入驻店铺同时选择，则不对该条搜索条件处理
            if (($own_shop || $other_shop) && !($own_shop && $other_shop)) {
                if ($own_shop) {
                    $cond_row['shop_self_support'] = 1;
                } else {
                    $cond_row['shop_self_support'] = 0;
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
            
            $virtual = request_int('virtual');
            if ($virtual == 1) {
                $cond_row['common_is_virtual'] = Goods_CommonModel::GOODS_VIRTUAL;
            }
            //判断是否有属性
            $property_value_row = array();
            $cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
            $cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
            
            if (request_string('type_wxapp') == 'wxapp' && request_string('type_wxapp')) {
                $cond_row['common_is_tuan'] = 0;
            }
            $shop_goods_cat_id = request_int('shop_goods_cat_id');
            if($shop_goods_cat_id){
                $Shop_GoodsCatModel = new Shop_GoodsCatModel();
                $shop_goods_cat = $Shop_GoodsCatModel->getOne($shop_goods_cat_id);
                $cond_row['shop_id'] = $shop_goods_cat['shop_id'];
            }

            if (request_string('mb') == "shop") {
                $cond_row['shop_id'] = request_string('shop_id_search');
            }


            if (trim(request_string('search_text')) != "") {
                $cond_row['common_name:LIKE'] ='%' . urldecode(trim(request_string('search_text'))) . '%' ;
            }
            $data = $Goods_CommonModel->getGoodsList($cond_row, $order_row, $page, $rows, $property_value_row);
            //店铺分类
            $shop_goods_cat_id = request_int('shop_goods_cat_id');
            if($shop_goods_cat_id){
                $Shop_GoodsCatModel = new Shop_GoodsCatModel();
                $child = $Shop_GoodsCatModel->getByWhere(array('parent_id'=>$shop_goods_cat_id));
                if($child){
                    $shop_goods_cat_ids = array_column($child,'shop_goods_cat_id');
                }
                $shop_goods_cat_ids[] = $shop_goods_cat_id;
                foreach ($data['items'] as $key => $value) {
                    if(!array_intersect($shop_goods_cat_ids,$value['shop_goods_cat_id'])){

                        unset($data['items'][$key]);
                    }
                }
                
                $data['items'] = array_merge($data['items']);

            }

            $Label_BaseModel = new Label_BaseModel();
            $Label_Base = $Label_BaseModel->getByWhere("*");
            $label_name_arr = array_column($Label_Base, "label_name","id");
            $data['label_name_arr'] = $label_name_arr;



            // 商品参加促销活动，即显示促销价，取消原价显示
            $Goods_BaseModel = new Goods_BaseModel();
            if (!empty($data['items'])) {
               foreach ($data['items'] as $k=>$v) {
                    $label_id_arr = '';
                    $label_name = [];
                    if (trim($v['label_id'])) {
                        $label_id_arr = explode(",", trim($v['label_id']));
                        foreach ($label_id_arr as $key => $label_id) {
                            $label_name[] = $label_name_arr[$label_id];
                        }
                    } else {
                        $label_name = '';
                    }

                    $data['items'][$k]['label_name'] = $label_name;
                    $goods_detail = $Goods_BaseModel->getGoodsDetailInfoByGoodId($v['goods_id']);
                    if ($goods_detail['goods_base']['promotion_price']) {
                        $data['items'][$k]['g_price'] =  $goods_detail['goods_base']['promotion_price'];
                    }else{
                        $data['items'][$k]['g_price'] =  $v['common_price'];
                    }
                    if($goods_detail['goods_base']['promotion_type']=='presale'){
                        $data['items'][$k]['promotion_type'] =  'presale';
                    }
                    // 如果商品不存在，即删除$k
                   // if (empty($v['good'])){
                   //      unset($data['items'][$k]);
                   // }
                    // 如果商品不存在，即删除$k
                    if (!empty(request_string('label_id')) && (!trim($v['label_id']) || !in_array(request_string('label_id'), $label_id_arr))){
                        unset($data['items'][$k]);
                    }
               }
           }
    
            $data['transport_area'] = $transport_area;

            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav = $Yf_Page->prompt();
            //推广产品
            $recommend_cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
            $recommend_cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
            $recommend_order_row['common_is_recommend'] = 'DESC';
            
            //热卖推荐
            $hot_sale = array();
            $brand_row = array();
            $cat_row = array();
            $recommend_row = array();
            
            //热卖推荐，查找商城中销量最多的商品
            $hot_order_row['common_salenum'] = 'DESC';
            $hot_sale = $Goods_CommonModel->getGoodsList($cond_row, $hot_order_row, 1, 3);
            $hot_sale = $hot_sale['items'];
            if (!$hot_sale) {
                $hot_cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
                $hot_cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
                if ($sub_site_district_ids) {
                    $hot_cond_row['district_id:IN'] = $sub_site_district_ids;
                }
                $hot_order_row['common_salenum'] = 'DESC';
                $hot_sale = $Goods_CommonModel->getGoodsList($hot_cond_row, $hot_order_row, 1, 3);
                $hot_sale = $hot_sale['items'];
            }
            
            //获取推广商品
            $Goods_RecommendModel = new Goods_RecommendModel();
            $recommond_cond_row = array();
            $recommond_order_row = array();
            $recommend_order_row['supply_shop_id'] = 0;
            //如果有查找的分类就显示该分类下的推广商品，如果没有传递分类就显示最新设置的分类推广
            if ($cat_id) {
                $recommond_cond_row['goods_cat_id'] = $cat_id;
            } else {
                $recommond_order_row['goods_recommend_id'] = 'DESC';
            }
            
            //如果有分站，查询分站
            if ($sub_site_id) {
                $recommond_cond_row['sub_site_id'] = $sub_site_id;
            }
            
            $recommend_row = $Goods_RecommendModel->getRccommonGoodsInfo($recommond_cond_row, $recommond_order_row);
            
            //如果商城没有设定推广商品，则将最新发布的四件商品作为推广商品显示
            if (!$recommend_row) {
                $recommend_order_row['common_is_recommend'] = 'DESC';
                $recommend_order_row['common_id'] = 'DESC';
                if ($sub_site_district_ids) {
                    $recommend_cond_row['district_id:IN'] = $sub_site_district_ids;
                }
                $recommend_row = $Goods_CommonModel->getGoodsList($recommend_cond_row, $recommend_order_row, 1, 4);
                $recommend_row = $recommend_row['items'];
            }
            
            //获取品牌信息
            $Goods_TypeModel = new Goods_TypeModel();
            $type_cond_row = array();
            //如果有查找的分类就显示该分类的相关品牌，如果没有传递分类就不显示品牌
            if ($cat_id) {
                $type_cond_row['cat_id'] = $cat_id;
                $brand_row = $Goods_TypeModel->getTypeBrand($type_cond_row);
                
            }
            
            //获取分类信息
            $Goods_TypeBrandModel = new Goods_TypeBrandModel();
            $tbrand_cond_row = array();
            //如果有品牌就显示该品牌下的分类，如果没有就不显示分类
            if ($brand_id) {
                $tbrand_cond_row['brand_id:IN'] = $brand_id;
                $cat_row = $Goods_TypeBrandModel->getBrandType($tbrand_cond_row);
            }
            
            $title = Web_ConfigModel::value("category_title");//首页名;
            $this->keyword = Web_ConfigModel::value("category_keyword");//关键字;
            $this->description = Web_ConfigModel::value("category_description");//描述;
            $this->title = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $title);
            $this->keyword = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $this->keyword);
            $this->description = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $this->description);
            
            //商品分类{name}{sitename} 是否会发生变化
            if ($cat_id) { //当用户搜索选中分类
                $selected_cat_rows = $Goods_CatModel->getCat($cat_id);
                $selected_cat_data = current($selected_cat_rows);
                $selected_cat_name = $selected_cat_data['cat_name'];
                $this->title = str_replace('{name}', $selected_cat_name, $this->title);
            } else { //当用户搜索没有选中分类
                $this->title = str_replace('商品分类{name}', "$search-", $this->title);
            }
            
            //计算plus商品
            $Plus_GoodsModel = new Plus_GoodsModel();
            $data['items'] = $Plus_GoodsModel->reformPlusGoods($data['items']);

            $db = new YFSQL();
            $sql = "SELECT * from ucenter_user_info where user_id=" . Perm::$userId;
            $ucenter_user_info_select = $db->find($sql);
            $ucenter_user_info = current($ucenter_user_info_select);


            foreach ($data['items'] as $k => $goods_common_c) {
                if ($goods_common_c['third_url'] != NULL) {
                    $data['items'][$k]['third_url'] = $goods_common_c['third_url'] . "&token=" . $ucenter_user_info['token'] . "&enterId=" . $ucenter_user_info['enterId'] ;
                }
                if(Web_ConfigModel::value('Plugin_Directseller') ==1 && $goods_common_c['common_is_directseller'] == 1){
                    $data['items'][$k]['common_a_first'] = number_format($goods_common_c['common_a_first'] * $goods_common_c['common_price'] / 100, 2);
                    $data['items'][$k]['common_c_first'] = number_format($goods_common_c['common_c_first'] * $goods_common_c['common_price'] / 100, 2);
                }
            }
            if ('json' == $this->typ) {
                //重组data返回值
                //$data['items'] = $this->reformData($data['items']);
                $user_id = request_int('u',0);
                if($user_id){
                    $User_InfoModel = new User_InfoModel();
                    $info = $User_InfoModel->getOne($user_id);
                    //分销员类型 0.分销客 1.分销掌柜
                    $data['distributor_type'] = $info['distributor_type']; 
                    $data['distributor_open'] = Web_ConfigModel::value('Plugin_Directseller'); 
                }
                $data['goods_brand'] = $goods_brand;
                foreach ($data['goods_brand'] as $key =>$val){
                    $data['goods_brand'][$key]['key'] = $key;
                    $data['goods_brand'][$key]['checked'] = false;
                }
                $data['goods_brand_all'] = $goods_brand_all;
                array_pop($goods_brands_all);
                $data['goods_brands_all'] = array_values($goods_brands_all);
                foreach ($data['goods_brand_all'] as $key=>$val){
                    foreach ($val as $k=>$v){
                        $data['goods_brand_all'][$key][$k]['key'] = $k;
                        $data['goods_brand_all'][$key][$k]['checked'] = false;
                    }
                    
                }
                $data['brand_info'] = array_values($goods_brand_all);
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        //得到小程序直播商品列表
        public function getLiveGoods()
        {
            $goodsIds = request_string('goodsIds');
            $commonIds = explode(',', $goodsIds);
            $goodsBaseModel = new Goods_BaseModel();
            $goodsCommonModel = new Goods_CommonModel();
            //获取直播商品goods_common
            $condition = array(
                'is_del' => 1,
                'common_state' => 1,
                'common_stock:>' => 0,
                'common_id:IN' => $commonIds
            );
            $common_data = $goodsCommonModel->getCommonList($condition);

            $common_data = $common_data['items'];
            if (!empty($common_data)) {
                foreach ($common_data as $k => $v) {
                    $goods_detail = $goodsBaseModel->getGoodsDetailInfoByGoodId($v['goods_id']);
                    if ($goods_detail['goods_base']['promotion_price']) {
                        $common_data[$k]['g_price'] = $goods_detail['goods_base']['promotion_price'];
                    } else {
                        $common_data[$k]['g_price'] = $v['common_price'];
                    }
                }
            }

            $data = array();
            foreach ($common_data as $k => $v) {
                $data[$k]['common_id'] = $v['common_id'];
                $data[$k]['goods_image'] = $v['common_image'];
                $data[$k]['goods_name'] = $v['common_name'];
                $data[$k]['goods_price'] = $v['g_price'];
                $goods_info = current($v['goods_id']);
                $data[$k]['goods_id'] = $goods_info['goods_id'];
                $data[$k]['goods_common'] = $v;
            }

            $this->data->addBody(-140, $data);
        }
        /**
         * 添加直播商品
         *
         * @return array
         */
        public function saveLiveGoods(){
          $roomId =  request_string('roomId');
          $goodsIds =  request_string('goodsIds');
          $roomName =  request_string('roomName');
          $roomImg =  request_string('roomImg');
          $u =  request_string('u');
          $liveGoodsModel = new Live_GoodsModel();
          $condition = array(
            'roomId' => $roomId,
            'goodsIds' => $goodsIds,
            'room_name' => $roomName,
            'room_img' => $roomImg,
            'user_id' => $u
          );
          $liveGoodsModel->addLiveGoods($condition);
        }

        /**
         * 添加直播商品
         *
         * @return array
         */
        public function getLiveGoodsList()
        {
            $roomId = request_string('roomId');
            $liveGoodsModel = new Live_GoodsModel();
            $condition = array(
                'roomId' => $roomId
            );
            $data = $liveGoodsModel->getOneByWhere($condition);

            //用户信息
            $User_InfoModel = new User_InfoModel();
            $user_info = $User_InfoModel->getOne($data['user_id']);
            $data['user_name'] = $user_info['user_name'];
            $data['user_logo'] = $user_info['user_logo'];
            $this->data->addBody(-140, $data);
        }
        /**
         * 查询分类品牌和分类关联属性
         *
         * @return array
         */
        public function getBrandAndProperty()
        {
            $cat_id = request_int('cat_id');
            $brand_id = request_int('brand_id');
            $property_id = request_int('property_id');
            $property_value_id = request_int('property_value_id');
            $search_property = request_row('search_property');
            
            if (!empty($cat_id)) {
                //存储查询条件
                $search_string = '';
                $property_value_ids = array();
                
                if (!empty($property_id)) {
                    $search_property[$property_id] = $property_value_id;
                }
                
                $goodsCatModel = new Goods_CatModel();
                $goodsTypeModel = new Goods_TypeModel();
                $goodsBrandModel = new Goods_BrandModel();
                
                $cata_data = $goodsCatModel->getCat($cat_id);
                
                $cata_data = pos($cata_data);
                $type_id = $cata_data['type_id'];
                
                if ($type_id) {
                    $data = $goodsTypeModel->getTypeInfo($type_id);
                }
                
                if (!empty($data['property'])) {
                    //过滤类型为 text property
                    foreach ($data['property'] as $key => $property_data) {
                        if ($property_data['property_format'] == 'text' || empty($property_data['property_format']) || empty($property_data['property_values'])) {
                            unset($data['property'][$key]);
                        } else {
                            //拼接搜索条件
                            if (!empty($search_property[$property_data['property_id']])) {
                                $property_value_id = $search_property[$property_data['property_id']];
                                
                                $property_array = array();
                                $property_array['property_name'] = $property_data['property_name'];
                                $property_array['property_value_id'] = $property_value_id;
                                $property_array['property_value_name'] = $property_data['property_values'][$property_value_id]['property_value_name'];
                                $search_property[$property_data['property_id']] = $property_array;
                                
                                unset($data['property'][$key]);
                            }
                        }
                    }
                    
                    $data['search_property'] = $search_property;
                    
                    if (!empty($data['search_property'])) {
                        foreach ($data['search_property'] as $property_id => $property_data) {
                            $property_value_id = $property_data['property_value_id'];
                            $string = "search_property[$property_id]=$property_value_id&";
                            $search_string .= $string;
                            
                            $property_value_ids[] = $property_value_id;
                        }
                    }
                    
                    $data['search_string'] = $search_string;
                    
                }
                
                if (!empty($brand_id)) {
                    unset($data['brand']);
                    
                    $data['search_string'] .= "brand_id=$brand_id&";
                    
                    $search_brand = $goodsBrandModel->getBrand($brand_id);
                    if (!empty($search_brand)) {
                        $data['search_brand'] = pos($search_brand);
                    }
                    
                } else if (!empty($data['brand'])) {
                    $brand_list = $goodsBrandModel->getBrand($data['brand']);
                    
                    $data['brand_list'] = $brand_list;
                }
                
                //过滤出所有符合筛选条件的common_id
                if (!empty($property_value_ids)) {
                  $data['common_ids'] =  $this->filterBySpec($property_value_ids, $data);
                }
                //如果有下级分类，则取出展示
                $child_cat = $goodsCatModel->getChildCat($cat_id);
                if (!empty($cat_id)) {
                    $data['child_cat'] = $child_cat;
                }
                
                return $data;
            }
        }
        
        private function filterBySpec($property_value_ids, &$data)
        {
            $common_ids = array();
            $condition_search = array();
            $goodsPropertyIndexModel = new Goods_PropertyIndexModel();
            
            foreach ($property_value_ids as $key => $property_value_id) {
                $condition_search['property_value_id'] = $property_value_id;
                
                $property_index_list = $goodsPropertyIndexModel->getByWhere($condition_search);
                
                if (empty($property_index_list)) {
                    return $data['common_ids'][] = false;
                } else {
                    $property_index_list = array_column($property_index_list, 'common_id');
                    
                    if ($key == 0) {
                        $common_ids = $property_index_list;
                    } else {
                        $common_ids = array_intersect($common_ids, $property_index_list);
                        
                        if (empty($common_ids)) {
                            return $data['common_ids'][] = false;
                        }
                    }
                }
            }
            return $data['common_ids'] = array_values($common_ids);
        }
        
        /**
         * 获取地区并设置cookie
         *
         * @return type
         */
        private function getCookieArea()
        {
            unset($_COOKIE['goodslist_area_id']);
            if (!isset($_COOKIE['goodslist_area_id'])) {
                $ip = get_ip();
                $Sub_SiteModel = new Sub_SiteModel();
                $area_array = $Sub_SiteModel->getIPLoc_sina_new($ip);
                $district = $Sub_SiteModel->areaConvert($area_array);
                if (!$district['province']) {
                    //默认数据
                    setcookie("goodslist_area_id", 143);
                    setcookie("goodslist_area_name", '上海 黄浦区');
                    $cookid_area = array();
                    $cookid_area['area'] = '上海 黄浦区';
                    $cookid_area['city']['id'] = 143;
                } else {
                    //配送区域修改 @nsy 2019-02-20
                    $Base_DistrictModel = new Base_DistrictModel();
                    $area_info = $Base_DistrictModel->getDistrictDetailByName($district['province'] . ' ' . $district['city']);
                    $cookid_area = array(
                        'area' => $district['province'] . ' ' . $district['city'],
                        'provice' => array('id' => $area_info[0]['district_id'], 'name' => $area_info[0]['district_name']),
                        'city' => array('id' => $area_info[1]['district_id'], 'name' => $area_info[1]['district_name'])
                    );
                    setcookie("goodslist_area_id", $area_info[1]['district_id']);
                    setcookie("goodslist_area_name", $cookid_area['area']);
                    //下边注释部分为老版本
                    /*if (in_array($district['province'], array('北京', '上海', '重庆', '天津'))) {
                        $cookid_area = $Base_DistrictModel->getCookieDistrictName($district['province'], 2);
                        setcookie("goodslist_area_id", $cookid_area['city']['id']);
                        setcookie("goodslist_area_name", $cookid_area['area']);
                    } else {
                        $area_info = $Base_DistrictModel->getDistrictDetailByName($district['province'] . ' ' . $district['city']);
                        $cookid_area = array(
                            'area' => $district['province'] . ' ' . $district['city'],
                            'provice' => array('id' => $area_info[0]['district_id'], 'name' => $area_info[0]['district_name']),
                            'city' => array('id' => $area_info[1]['district_id'], 'name' => $area_info[1]['district_name'])
                        );
                        //setcookie("goodslist_area_id", $area_info[1]['district_id']);
                        //setcookie("goodslist_area_name", $cookid_area['area']);
                    }*/
                }
            } else {
                $cookid_area = array();
                $cookid_area['area'] = $_COOKIE['goodslist_area_name'];
                $cookid_area['city']['id'] = $_COOKIE['goodslist_area_id'];
            }
            return $cookid_area;
        }
        
        protected function strFilter($str)
        {
            $str = str_replace('`', '', $str);
//      $str = str_replace('·', '', $str);
            $str = str_replace('~', '', $str);
            $str = str_replace('!', '', $str);
            $str = str_replace('！', '', $str);
            // $str = str_replace('@', '', $str);
            // $str = str_replace('#', '', $str);
            // $str = str_replace('$', '', $str);
            // $str = str_replace('￥', '', $str);
            // $str = str_replace('%', '', $str);
            $str = str_replace('……', '', $str);
            // $str = str_replace('&', '', $str);
            $str = str_replace('*', '', $str);
            $str = str_replace('(', '', $str);
            $str = str_replace(')', '', $str);
            $str = str_replace('（', '', $str);
            $str = str_replace('）', '', $str);
            //$str = str_replace('-', '', $str);
            $str = str_replace('_', '', $str);
            $str = str_replace('——', '', $str);
            $str = str_replace('+', '', $str);
            $str = str_replace('=', '', $str);
            $str = str_replace('|', '', $str);
            $str = str_replace('\\', '', $str);
            $str = str_replace('[', '', $str);
            $str = str_replace(']', '', $str);
            $str = str_replace('【', '', $str);
            $str = str_replace('】', '', $str);
            $str = str_replace('{', '', $str);
            $str = str_replace('}', '', $str);
            $str = str_replace(';', '', $str);
            $str = str_replace('；', '', $str);
            $str = str_replace(':', '', $str);
            $str = str_replace('：', '', $str);
            $str = str_replace('\'', '', $str);
            $str = str_replace('"', '', $str);
            $str = str_replace('“', '', $str);
            $str = str_replace('”', '', $str);
            // $str = str_replace(',', '', $str);
            // $str = str_replace('，', '', $str);
            $str = str_replace('<', '', $str);
            $str = str_replace('>', '', $str);
            $str = str_replace('《', '', $str);
            $str = str_replace('》', '', $str);
//      $str = str_replace('.', '', $str);
            $str = str_replace('。', '', $str);
            $str = str_replace('/', '', $str);
            $str = str_replace('、', '', $str);
            $str = str_replace('?', '', $str);
            $str = str_replace('？', '', $str);
            return trim($str);
        }
        
        function search()
        {
            $sphinx_search_flag = false;
            $sphinx_search_host = false;
            $sphinx_search_port = false;
            $key = request_string('key');
            
            //是否启用Sphinx搜索
            if ($sphinx_search_flag && $key && extension_loaded("sphinx") && extension_loaded("scws")) {
                $b_time = microtime(true);
                //$key = "我是一个测试";
                $index = "product_search";
                
                $so = new Yf_Search_Scws($key);
                $words = $so->getResult();
                
                $sc = new SphinxClient();
                $sc->SetServer($sphinx_search_host, $sphinx_search_port);
                #$sc->SetMatchMode(SPH_MATCH_ALL);
                $sc->SetMatchMode(SPH_MATCH_EXTENDED);
                $sc->SetArrayResult(TRUE);
                
                $sc->setFilter('shop_statu', array(1));
                $sc->setFilter('p_status', array(1));
                $sc->setFilter('is_shelves', array(1));
                //$sc->setFilter('tg', array(crc32('false')));
                
               /* if (!empty($_GET['ptype']) and $_GET['ptype'] >= 0 and $_GET['ptype'] < count($ptype)) {
                    $sc->setFilter('type', array($_GET[ptype]));
                }*/
                
                if (isset($_GET['province'])) {
                    $sc->setFilter('provinceid', array(intval($_GET['province'])));
                }
                
                $order = '';
                $orderby='';
                if ($orderby == 1) {
                    $order .= "sales DESC";
                } elseif ($orderby == 2) {
                    $order .= "clicks DESC";
                } elseif ($orderby == 3) {
                    $order .= "goodbad DESC";
                } elseif ($orderby == 4) {
                    $order .= "uptime DESC";
                } elseif ($orderby == 5) {
                    $order .= "price ASC";
                } elseif ($orderby == 6) {
                    $order .= "price DESC";
                } else {
                    $order .= "rank DESC, uptime DESC";
                }
                
                $sc->SetSortMode(SPH_SORT_EXTENDED, $order);
                
                if ($_GET['firstRow']) {
                    $start = $_GET['firstRow'];
                } else {
                    $start = 0;
                }
                
                $sc->SetLimits($start, 30, 1000);    // 最大结果集10000
                
                $res = $sc->Query($words, $index);
                
                $prol = array();
                
                if ($res['matches']) {
                    foreach ($res['matches'] as $matches) {
                        $matches['attrs']['id'] = $matches['id'];
                        $prol[] = $matches['attrs'];
                    }
                    
                }
                $config =array();
                include_once("includes/page_utf_class.php");
                $page = new Page;
                $page->url = $config['weburl'] . '/';
                $page->listRows = 30;
                
                if (!$page->__get('totalRows')) {
                    $page->totalRows = $res['total'];
                }
                
                $prolist['list'] = $prol;
                $prolist['page'] = $page->prompt();
                $prolist['count'] = $res['total'];
                //$tpl->assign("info", $prolist);
                unset($prolist);
            }
            
        }
        
        /**
         * 商品详情页  goodsdetailinfo
         *
         * @access public
         */
        public function goodDetail()
        {
            $goods_id = request_int('goods_id');
            
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_base = $Goods_BaseModel->getGoodsInfo($goods_id);
            
            //计算商品价格
            if (isset($goods_base['goods_base']['promotion_price']) && !empty($goods_base['goods_base']['promotion_price']) && $goods_base['goods_base']['promotion_price'] < $goods_base['goods_base']['goods_price']) {
                $goods_base['goods_base']['old_price'] = $goods_base['goods_base']['goods_price'];
                $goods_base['goods_base']['now_price'] = $goods_base['goods_base']['promotion_price'];
                $goods_base['goods_base']['down_price'] = $goods_base['goods_base']['down_price'];
            } else {
                $goods_base['goods_base']['old_price'] = 0;
                $goods_base['goods_base']['now_price'] = $goods_base['goods_base']['goods_price'];
                $goods_base['goods_base']['down_price'] = 0;
            }
            
            $this->data->addBody(-140, $goods_base);
            
        }
        

        public function getShopUid () {
            $goods_id = request_int('goods_id');
             // $UC = request_row('UC');
            $Goods_BaseModel = new Goods_BaseModel();
            $Goods_Base = $Goods_BaseModel->getOne($goods_id);
            $Shop_BaseModel = new Shop_BaseModel();
            $Shop_Base = $Shop_BaseModel->getOne($Goods_Base['shop_id']);
            $db = new YFSQL();
            $sql = "SELECT * from ucenter_user_info where user_id=" . $Shop_Base['user_id'];
            $ucenter_user_info = $db->find($sql);
            $this->data->addBody(-140, $ucenter_user_info);

        }

        //获取购物车中的数量
        
        public function getGoodsidByCid()
        {
            $cid = request_int('cid');
            
            $Goods_CommonModel = new Goods_CommonModel();
            $property_value_row = array();
            $cond_row['common_id'] = $cid;
            $data = $Goods_CommonModel->getGoodsList($cond_row);
            $goods_id = $data['items'][0]['goods_id'];
            $this->data->addBody(-140, array('goods_id' => $goods_id));
            
        }
        

        /**
         * 点赞
         *
         */
        public function addZan () {
            $common_id = request_int('common_id');
            $userId = Perm::$userId;
            if ($userId == 0) {
                return $this->data->addBody(140, array(), "未登录，无法点赞！", 250);
            }
            $Goods_ZanLogModel = new Goods_ZanLogModel();
            $Goods_ZanLog = $Goods_ZanLogModel->getOneByWhere(array("common_id"=>$common_id,"user_id"=>$userId));

            $Goods_CommonModel = new Goods_CommonModel();
            $Goods_Common = $Goods_CommonModel->getOne($common_id);
            $editCommon = array();
            if ($Goods_ZanLog && $Goods_ZanLog['status'] == 1) {
                //取消点赞
                $msg = "已取消点赞！";
                $editCommon['zan_sum'] = $Goods_Common['zan_sum'] - 1;
                $Goods_ZanLog_Status = $Goods_ZanLogModel->editZanLog($Goods_ZanLog['id'],array("status"=>2));
            } elseif ($Goods_ZanLog && $Goods_ZanLog['status'] == 2) {
                //点赞
                $msg = "点赞成功";
                $editCommon['zan_sum'] = $Goods_Common['zan_sum'] + 1;
                $Goods_ZanLog_Status = $Goods_ZanLogModel->editZanLog($Goods_ZanLog['id'],array("status"=>1));
            } else {
                $msg = "点赞成功";
                $editCommon['zan_sum'] = $Goods_Common['zan_sum'] + 1;
                $addZanLog["common_id"] = $common_id;
                $addZanLog["user_id"] = $userId;
                $addZanLog["status"] = 1;
                $Goods_ZanLog_Status = $Goods_ZanLogModel->addZanLog($addZanLog);
            }
            $data['zan_sum'] = $editCommon['zan_sum'];
            $Goods_Common_edit = $Goods_CommonModel->editCommon($common_id,array("zan_sum"=>$editCommon['zan_sum']));
            if ($Goods_ZanLog_Status && $Goods_Common_edit) {
                $status = 200;
            } else {
                $status = 250;
            }
            $this->data->addBody(140, $data, $msg, $status);
        }

        /**
         * 获取商品详情页面商品详情
         *
         * 2017.6.15 hp
         */
        public function getGoodsDetailFormat()
        {
            $goods_id = request_int('gid');
            $isCheck = request_string('isCheck');
            //关联样式
            $Goods_BaseModel = new Goods_BaseModel();
            $Goods_CommonModel = new Goods_CommonModel();
            $Goods_FormatModel = new Goods_FormatModel();
            $goods_data = $Goods_BaseModel->getOne($goods_id);
            $common_id = $goods_data['common_id'];
            $common_data = $Goods_CommonModel->getOne($common_id);
            $goods_detail = $Goods_BaseModel->getGoodsDetailInfoByGoodId($goods_id, false);
            $goods_check = $Goods_BaseModel->checkGoodsII($goods_id);
            $data = [];
            if ($isCheck == 'false') {
                $goods_check = true;
            }
            if ($goods_detail && $goods_check) {
                if ($common_data) {
                    $common_formatid_top = $common_data['common_formatid_top'];
                    if ($common_formatid_top) {
                        //商品详情头部信息
                        $goods_format_top = $Goods_FormatModel->getOne($common_formatid_top);
                        if (isset($goods_format_top) && !empty($goods_format_top)) {
                            $data['goods_format_top'] = $goods_format_top['content'];
                        } else {
                            $data['goods_format_top'] = '';
                        }
                    } else {
                        $data['goods_format_top'] = '';
                    }
                    
                    $common_formatid_bottom = $common_data['common_formatid_bottom'];
                    if ($common_formatid_bottom) {
                        //商品详情底部信息
                        $goods_format_bottom = $Goods_FormatModel->getOne($common_formatid_bottom);
                        if (isset($goods_format_bottom) && !empty($goods_format_bottom)) {
                            $data['goods_format_bottom'] = $goods_format_bottom['content'];
                        } else {
                            $data['goods_format_bottom'] = '';
                        }
                    } else {
                        $data['goods_format_bottom'] = '';
                    }
                    //商品品牌名
                    if ($goods_detail['common_base']['brand_name']) {
                        $data['brand_name'] = $goods_detail['common_base']['brand_name'];
                    } else {
                        $data['brand_name'] = '';
                    }
                    $data['common_id'] =  $common_data['common_id'];
                    //商品详情
                    $data['common_detail'] = cdn_content_url($goods_detail['common_base']['common_detail']);  
                    //商品属性
                    $data['common_property_row'] = $goods_detail['common_base']['common_property_row'];
                }
                $this->data->addBody(-140, $data);
            }
        }
        
        /*
         * 判断是否超过限购数量
         */
        
        /**
         * 商品详情页  goodsdetailinfo
         *
         * @access public
         */
        public function goods()
        {
            $cid = request_int('cid');
            $goods_id = request_int('gid', request_int('goods_id'));
            //如果传递过来的是common_id，则从此common_id中的goods_id中选择一个有效的goods_id
            $Goods_CommonModel = new Goods_CommonModel();
            if ($cid && !$goods_id) {
               $goods_id = $Goods_CommonModel->getNormalStateGoodsId($cid);
            }
            //区分wap pc端
            if ($this->typ == 'json') {
                $is_wap = true;
            } else {
                $is_wap = false;
            }
            $goods_data = array();
            
            //添加商品点击数
            $Goods_BaseModel = new Goods_BaseModel();
            $good_click_row = array('goods_click' => '1');
            $Goods_BaseModel->editBase($goods_id, $good_click_row, true);
            
            //1.商品信息（商品活动信息，评论数，销售数，咨询数）
            $goods_detail = $Goods_BaseModel->getGoodsDetailInfoByGoodId($goods_id,true,true);
            $goods_common_details = $Goods_CommonModel->getOne($goods_detail['goods_base']['common_id']);

            //消费者保障
            $XFZ = $Goods_BaseModel->getBase($goods_id);
            foreach($XFZ as $key =>$val){
                $xfz['contract_type_id'] = $val['contract_type_id'];
            }

            $contract_type_id = $xfz['contract_type_id'];
            $contract_type_id = trim($contract_type_id,',');
            $contract_type_ids = explode(',',$contract_type_id);

            $shop_ContractTypeModel = new Shop_ContractTypeModel();
            $consult_data = $shop_ContractTypeModel->getByWhere(array('contract_type_id:IN'=>$contract_type_ids));

            //虚拟产品过期了则不能购买
            if($goods_common_details['common_is_virtual'] == 1 && $goods_common_details['common_virtual_date']<date("Y-m-d")){
                $this->view->setMet('404');
            }

            if (!$goods_detail) {
                if ($this->typ == 'json') {
                    return $this->data->addBody(140, [], '抱歉，该商品已下架或者该店铺已关闭！', 404);
                }
                $this->view->setMet('404');

            } else {
                $user_id = Perm::$userId;
                //添加用户足迹
                if (Perm::checkUserPerm()) {
                    $user_id = Perm::$userId;
                    $User_FootprintModel = new User_FootprintModel();
                    //先判断该用户是否浏览过该商品
                    $foot_cond_row['user_id'] = $user_id;
                    $foot_cond_row['common_id'] = $goods_detail['goods_base']['common_id'];
                    $foot_cond_row['footprint_date'] = date('Y-m-d');
                    $foot_id = $User_FootprintModel->getKeyByWhere($foot_cond_row);
                    //如果用户今天曾经浏览过该商品则修改浏览时间
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
                }



                $Goods_CatModel = new Goods_CatModel();
                //查找该分类的父级分类
                $parent_cat = $Goods_CatModel->getCatParent($goods_detail['goods_base']['cat_id']);
                $cat_info = $Goods_CatModel->getOne($goods_detail['goods_base']['cat_id']);
                if ($cat_info) {
                    $cat_info['ext'] = 1;
                    $parent_cat[] = $cat_info;
                }

                //封装分类商品退货期(未设置该值，统一按不支持退货处理) @nsy 2019-04-28
                $return_goods_limit = current($parent_cat);
                $rgl_val = $return_goods_limit['return_goods_limit'];
                $rgl_val<0 && $rgl_val=0;
                switch ($rgl_val){
                    case 0:
                        $rgl_str = '不支持退货退款';
                        break;
                    case 1:
                        $rgl_str = '不支持退货';
                        break;
                    case 7:
                    case 15:
                    case 30:
                        $rgl_str = $rgl_val."天无理由退货";
                        break;
                    default:
                        $rgl_str = '不支持退货';
                        break;
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
                if (Web_ConfigModel::value('Plugin_Directseller')) {
                    $goods_detail['recImages'] = '';
                    //推荐者上传的图片
                    $PluginManager = Yf_Plugin_Manager::getInstance();
                    $data = $PluginManager->trigger('rec_goods');
                    $goods_detail['recImages'] = isset($data['Plugin_Directseller_recGoods']) ? $data['Plugin_Directseller_recGoods'] : '';
                    //--END
                }

                //2.店铺信息
                $Shop_BaseModel = new Shop_BaseModel();
                $shop_detail = $Shop_BaseModel->getShopDetail($goods_detail['goods_base']['shop_id']);

                //判断是否显示自营店铺
                if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                    $sub_site_id = $_COOKIE['sub_site_id'];
                }
                $self_shop_show_key = !$sub_site_id ? 'self_shop_show' : 'self_shop_show_' . $sub_site_id;
                $check_shop_show = $shop_detail['shop_self_support'] == 'true' && !Web_ConfigModel::value($self_shop_show_key) ? false : true;
                if (!$shop_detail || !$check_shop_show) {
                    $this->view->setMet('404');
                }
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
                
                //查找当前用户是否是商品店铺的子账号
                $Seller_BaseModel = new Seller_BaseModel();
                $seller_row = array();
                $seller_row['user_id'] = Perm::$userId;
                $seller_info = $Seller_BaseModel->getByWhere($seller_row);
                $seller_info = array_pop($seller_info);
                
                if (($seller_info && $shop_detail['shop_id'] == $seller_info['shop_id']) || $shop_detail['user_id'] == Perm::$userId) {
                    $shop_owner = 1;
                }
                


                $db = new YFSQL();
                $sql  = "SELECT * from ucenter_user_info where user_id=" . $shop_detail['user_id'];
                $ucenter_user_info_select = $db->find($sql);
                $ucenter_user_info = current($ucenter_user_info_select);




                //判断当前用户是否是该商品的供应商
                $dist_owner = 0;
                if ($goods_detail['goods_base']['goods_parent_id']) {
                    $goods_parent_base = $Goods_BaseModel->getOne($goods_detail['goods_base']['goods_parent_id']);
                    if ($goods_parent_base['shop_id'] == Perm::$shopId) {
                        $dist_owner = 1;
                    }
                }
                
                //判断是否可以门店自提
                $Chain_GoodsModel = new Chain_GoodsModel();
                $chain_row['shop_id:='] = $goods_detail['goods_base']['shop_id'];
                $chain_row['goods_id:='] = $goods_id;
                $chain_row['goods_stock:>'] = 0;
                
                $chain_goods = $Chain_GoodsModel->getByWhere($chain_row);
                foreach ($chain_goods as $val){
                    $chain_goods_stock[] = $val['goods_stock'];
                }
                if(is_array($chain_goods_stock)){
                    $chain_goods_stock_max = max($chain_goods_stock);
                }
                $goods_detail['chain_stock'] = 0;
                
                if ($chain_goods) {
                    $goods_detail['chain_stock'] = 1;
                    $goods_detail['chain_goods_stock'] = $chain_goods_stock_max;
                }
                
                //如果使用售卖区域（现在商品表中暂时没有字段表面售卖区域）
                $IsHaveBuy = 0;
                if ($user_id) {
                    //团购商品是否已经开始
                    //查询该用户是否已购买过该商品
                    $Order_GoodsModel = new Order_GoodsModel();
                    if($goods_detail['common_base']['common_edit_time'] > $goods_detail['goods_base']['groupbuy_starttime'])
                    {
                        $start_time = $goods_detail['common_base']['common_edit_time'];
                    }
                    else
                    {
                        $start_time = $goods_detail['goods_base']['groupbuy_starttime'];
                    }
                    $order_goods_cond['common_id'] = $goods_detail['goods_base']['common_id'];
                    $order_goods_cond['buyer_user_id'] = $user_id;
                    $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_REFUND_FINISH;
                    $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_CANCEL;
                    $order_goods_cond['order_goods_time:>='] = $start_time;
                    $order_goods_cond['order_goods_time:<'] = $goods_detail['goods_base']['groupbuy_endtime'];
                    $order_list = $Order_GoodsModel->getByWhere($order_goods_cond);
                    $order_goods_count = 0;
                    foreach ($order_list as $val) {
                        if ($val['common_id'] == $goods_detail['goods_base']['common_id']) {
                            $order_goods_count += $val['order_goods_num'];
                        }
                        
                    }
                    if (isset($goods_detail['goods_base']['promotion_type'])) {
                        $promotion_type = $goods_detail['goods_base']['promotion_type'];
                        if ($promotion_type == 'groupbuy') {
                            //检测是否限购数量
                            $upper_limit = $goods_detail['goods_base']['upper_limit'];
                            if ($upper_limit > 0 && $order_goods_count >= $upper_limit) {
                                $IsHaveBuy = 1;
                            }
                        }
                    }
                    $cart_num = $this->getCartGoodsNum($goods_detail['common_base']['common_id']);
                    $order_num = $order_goods_count;
                    
                    //商品限购数量判断
                    if ($goods_detail['common_base']['common_limit'] > 0 && $order_goods_count >= $goods_detail['common_base']['common_limit']) {
                        $IsHaveBuy = 1;
                    }
                    
                }
                
                $IsOfflineBuy = 0;
                if ($user_id) {
                    //团购商品是否已经开始
                    //查询该用户是否已购买过该商品
                    $Order_GoodsModel = new Order_GoodsModel();
                    $order_goods_cond['common_id'] = $goods_detail['goods_base']['common_id'];
                    $order_goods_cond['buyer_user_id'] = $user_id;
                    $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_REFUND_FINISH;
                    $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_CANCEL;
                    $order_list = $Order_GoodsModel->getByWhere($order_goods_cond);
                    
                    $order_goods_count = count($order_list);
                    if (isset($goods_detail['goods_base']['promotion_type'])) {
                        $promotion_type = $goods_detail['goods_base']['promotion_type'];
                        if ($promotion_type == 'xianshi') {
                            $lower_limit = $goods_detail['goods_base']['lower_limit'];
                            if ($lower_limit > 0 && $order_goods_count < $lower_limit) {
                                $IsOfflineBuy = $lower_limit;
                            }
                        }
                    }
                }
                
                //计算限购数量
                if (isset($goods_detail['goods_base']['upper_limit']) && $goods_detail['goods_base']['groupbuy_starttime'] <= date('Y-m-d H:i:s') && $goods_detail['goods_base']['groupbuy_endtime'] >= date('Y-m-d H:i:s')) {
                    if ($goods_detail['goods_base']['upper_limit'] && $goods_detail['common_base']['common_limit']) {
                        if ($goods_detail['goods_base']['upper_limit'] >= $goods_detail['common_base']['common_limit']) {
                            $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit'];
                        } else {
                            $goods_detail['buy_limit'] = $goods_detail['goods_base']['upper_limit'];
                        }
                    } elseif ($goods_detail['goods_base']['upper_limit'] && !$goods_detail['common_base']['common_limit']) {
                        $goods_detail['buy_limit'] = $goods_detail['goods_base']['upper_limit'];
                    } elseif (!$goods_detail['goods_base']['upper_limit'] && $goods_detail['common_base']['common_limit']) {
                        $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit'];
                    } else {
                        $goods_detail['buy_limit'] = 0;
                    }
                } else {
                    $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit']?$goods_detail['common_base']['common_limit']:0;
                }
                
                $shop_id = $shop_detail['shop_id'];

                $Goods_CommonModel = new Goods_CommonModel();
                if ($shop_id) {
                    $data_recommon = $Goods_CommonModel->listByWhere(array(
                        'shop_id' => $shop_id
                    ), array('common_is_recommend' => 'desc', 'common_sell_time' => 'desc'), 0, 4);
                    $data_recommon_goods = $Goods_CommonModel->getRecommonRow($data_recommon);
                    
                    //推荐商品
                    $data_foot_recommon = $Goods_CommonModel->listByWhere(array(
                        'shop_id' => $shop_id
                    ), array('common_is_recommend' => 'DESC'), 0, 5);
                    $data_foot_recommon_goods = $Goods_CommonModel->getRecommonRow($data_foot_recommon);
                    
                    //热门销售
                    $data_hot_salle = $Goods_CommonModel->getHotSalle($shop_id, $is_wap);
                    $data_salle = $Goods_CommonModel->getRecommonRow($data_hot_salle);
                    
                    //热门收藏
                    $data_hot_collect = $Goods_CommonModel->getHotCollect($shop_id);
                    $data_collect = $Goods_CommonModel->getRecommonRow($data_hot_collect);
                    
                    //商品咨询数量
                    $Consult_BaseModel = new Consult_BaseModel();
                    $consult_num = $Consult_BaseModel->getNum(array(
                        'goods_id' => $goods_id,
                        'shop_id' => $shop_id
                    ));
                }
            }

            $title = Web_ConfigModel::value("product_title");//首页名;
            $this->keyword = Web_ConfigModel::value("product_keyword");//关键字;
            $this->description = Web_ConfigModel::value("product_description");//描述;
            $this->title = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $title);
            $this->title = str_replace("{name}", $goods_detail['goods_base']['goods_name'], $this->title);
            $this->keyword = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $this->keyword);
            $this->keyword = str_replace("{name}", $goods_detail['goods_base']['goods_name'], $this->keyword);
            $this->description = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $this->description);
            $this->description = str_replace("{name}", $goods_detail['goods_base']['goods_name'], $this->description);
            
            if ($goods_data) {
                $this->shopCustomServiceModel = new Shop_CustomServiceModel;
                $cond_row['shop_id'] = $goods_data['shop_id'];
                $service = $this->shopCustomServiceModel->getServiceList($cond_row);
                if ($service['items']) {
                    foreach ($service['items'] as $key => $val) {
                        //QQ
                        if ($val['tool'] == 1) {
                            $service[$key]["tool"] = "<a target='_blank' href='http://wpa.qq.com/msgrd?v=3&uin=" . $val['number'] . "&site=qq&menu=yes'><img border='0' src='http://wpa.qq.com/pa?p=2:" . $val['number'] . ":41 &amp;r=0.22914223582483828' alt='点击这里'></a>";
                        }
                        //旺旺
                        if ($val['tool'] == 2) {
                            $service[$key]["tool"] = "<a target='_blank' href='http://www.taobao.com/webww/ww.php?ver=3&amp;touid=" . $val['number'] . "&amp;siteid=cntaobao&amp;status=1&amp;charset=utf-8' ><img border='0' src='http://amos.alicdn.com/online.aw?v=2&amp;uid=" . $val['number'] . "&amp;site=cntaobao&s=1&amp;charset=utf-8' alt='点击这里' /></a>";
                        }
                        //IM
                        if ($val['tool'] == 3) {
                            $service[$key]["tool"] = '<a href="javascript:;" class="chat-enter" onclick="return chat(\'' . $val['number'] . '\');"><img src="' . $this->view->img . '/icon-im.gif" alt=""></a>';
                        }
                        //$service[$key]["tool"] = $val['tool'];
                        $service[$key]["number"] = $val['number'];
                        $service[$key]["name"] = $val['name'];
                        $service[$key]["id"] = $val['id'];
                        if ($val['type'] == 1) {
                            $de['after'][] = $service[$key];
                        } else {
                            $de['pre'][] = $service[$key];
                        }
                    }
                    $service = array();
                    $service = $de;
                }
            }
            
            // 促销活动没开始的时候，最低购买数是1，而不是活动的最低购买数。 $IsOfflineBuy = 0
            // sun
            if (isset($goods_detail['goods_base']['promotion_type']) && $goods_detail['goods_base']['promotion_type']) {
                $now_time = time();
                $start_time = strtotime($goods_detail['goods_base']['groupbuy_starttime']);
                $end_time = strtotime($goods_detail['goods_base']['groupbuy_endtime']);
                if ($start_time > $now_time) {
                    $goods_detail['goods_base']['lower_limits'] = 1;
                    $IsOfflineBuy = 0;
                } else {
                    $goods_detail['goods_base']['lower_limits'] = $goods_detail['goods_base']['lower_limit'];
                }
                if ($end_time > $now_time && $start_time < $now_time) {
                    $time_tips = __('距结束');
                }
            }

            //计算商品PLUS价格
            $Plus_GoodsModel = new Plus_GoodsModel();
            if (isset($goods_detail['goods_base']['promotion_price']) && !empty($goods_detail['goods_base']['promotion_price'])){
                $price = $goods_detail['goods_base']['promotion_price'];
            } else {
                $price = $goods_detail['goods_base']['goods_price'];
            }
            $plus = $Plus_GoodsModel->getGoodsPlusPrice($goods_detail['goods_base']['common_id'],$price);

            $goods_detail['goods_base']['plus_status'] = $plus['plus_status'];
            $goods_detail['goods_base']['plus_price'] = $plus['plus_price'];

            //判读当前用户是否是PLUS会员
            $Plus_UserModel = new Plus_UserModel();
            $plus_user = $Plus_UserModel->getPlusUserStatus(Perm::$userId);
            if ('json' == $this->typ) {

                $goods_detail['goods_base']['plus_user'] = $plus_user;
                /**
                 * ly wap 端返回数据
                 *
                 */
                $data = array();
                $goods_eval_list = array();
                $goods_evaluate_info = array();
                $goods_hair_info = array();
                $goods_image = '';
                $goods_info = array();
                $spec_list = array();
                $spec_image = array();
                $store_info = array();
                $mansong_info = array();
                $common_id = $goods_detail['common_base']['common_id'];
                
                //商品规格描述
                $show_goods_spec_str = "";
                $show_goods_spec_value = $goods_detail['goods_base']['goods_spec'] ? array_values($goods_detail['goods_base']['goods_spec']) : array();
                if ($show_goods_spec_value) {
                    $show_goods_spec_title = array_values($goods_detail['common_base']['common_spec_name']);
                    $show_goods_spec = array_combine($show_goods_spec_title, $show_goods_spec_value);
                    foreach ($show_goods_spec as $k_title => $title) {
                        $show_goods_spec_str .= "$k_title:$title ";
                    }
                }
                


                $Goods_CommonModel = new Goods_CommonModel();
                $Goods_Common_lab = $Goods_CommonModel->getOne($common_id);

 
                $label_id_arr = explode(",", $Goods_Common_lab['label_id']);

                $Label_BaseModel = new Label_BaseModel();
                $Label_Base = $Label_BaseModel->getByWhere(array("id:IN"=>$label_id_arr));
                $label_name_arr = array_column($Label_Base, "label_name","id");
                //商品详情
                $goods_info = array_merge($goods_detail['common_base'], $goods_detail['goods_base']);
                $goods_info['label_name_arr'] = $label_name_arr;
                if (empty($goods_info['common_spec_name'])) {
                    $goods_info['common_spec_name'] = "";
                    $goods_info['common_spec_value'] = "";
                }
                $goods_info['shop_company_address'] = $shop_detail['shop_company_address'];
                $shop_label_id_arr = explode(",", $shop_detail['label_id']);
                $Label_Base_Shop = $Label_BaseModel->getByWhere(array("id:IN"=>$shop_label_id_arr));
                $shop_label_name_arr = array_column($Label_Base_Shop, "label_name","id");
                $goods_info['shop_label_name_arr'] = $shop_label_name_arr;
                $goods_info['wap_shop_logo'] = $shop_detail['wap_shop_logo'];
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
                $goods_hair_info['store'] = $goods_detail['goods_base']['goods_stock'];
                
                //定位
                $subSiteModel = new Sub_SiteModel();
                $lbs_geo = request_string('lbs_geo');
                
                $level = 3;
                if($lbs_geo){
                    $user_lbs_geo = $subSiteModel->getLbsGeo($lbs_geo, $level);
                }else{
                    $user_lbs_geo = [];
                }

                //获取默认配送区域运费
                if ($user_lbs_geo['district_id']) {
                    $area_model = new Transport_AreaModel();
                    $transportInfo = $Goods_BaseModel->getTransportInfo($user_lbs_geo['district_id'], $goods_detail['common_base']['common_id']);
                    $goods_hair_info['content'] = $transportInfo['transport_str'];
                    $goods_hair_info['result'] = $transportInfo['result'];
                    if ($transportInfo['result'] == true && (!empty($goods_detail['goods_base']['goods_stock']) && $goods_detail['goods_base']['goods_stock'] > 0)) {
                        $goods_hair_info['if_store_cn'] = __('有货');
                        $goods_hair_info['if_store'] = true;
                    } else {
                        $goods_hair_info['if_store_cn'] = __('无货');
                        $goods_hair_info['if_store'] = false;
                    }
                    
                }
                if (isset($user_lbs_geo['district_name']) && $user_lbs_geo['district_name']) {
                    $goods_hair_info['area_name'] = __($user_lbs_geo['district_name']);
                    $goods_hair_info['district_id'] = $user_lbs_geo['district_id'];
                    
                    $goods_hair_info['transport_data'] = $this->getTramsportData($goods_hair_info['district_id'], $common_id);
                } else {
                    $goods_hair_info['area_name'] = __('全国');
                    $goods_hair_info['district_id'] = 0;

                    if(!empty($goods_detail['goods_base']['goods_stock']) && $goods_detail['goods_base']['goods_stock'] > 0) {
                        $goods_hair_info['if_store_cn'] = __('有货');
                        $goods_hair_info['if_store'] = true;
                    } else {
                        $goods_hair_info['if_store_cn'] = __('无货');
                        $goods_hair_info['if_store'] = false;
                    }
                }
                //图片信息
                if (isset($goods_detail['goods_base']['image_row']) && !empty($goods_detail['goods_base']['image_row'])) {
                    $images_list = array_column($goods_detail['goods_base']['image_row'], 'images_image');
                    $images_list = array_map(function ($img) {
                        return image_thumb($img, 360, 360);
                    }, $images_list);
                    $goods_image = implode(';', $images_list);
                    
                } else {
                    $goods_image = $goods_detail['goods_base']['goods_image'];
                }
                
                //满送
                $mansong_info = $goods_detail['mansong_info'];
                
                if (!empty($goods_detail['common_base']['common_spec_name'])) {
                    //商品规格
                    $spec_list = $Goods_BaseModel->createSGIdByWap($goods_detail['common_base']['common_id']);
                    $spec_list_info = $Goods_BaseModel->createSGIdByWaps($goods_detail['common_base']['common_id']);
                    //商品规格颜色图
                    if (!empty($goods_detail['common_base']['common_spec_value_color'])) {
                        $spec_image = $goods_detail['common_base']['common_spec_value_color'];
                    }
                }
                $stock_list = $Goods_BaseModel->getGoodsStockList($goods_detail['common_base']['common_id']);
                $price_list = $Goods_BaseModel->getGoodsPriceList($goods_detail['common_base']['common_id']);
                //店铺信息
                $store_info['is_own_shop'] = $shop_detail['shop_self_support'];
                $store_info['member_id'] = $shop_detail['user_id'];
                $store_info['member_name'] = $shop_detail['user_name'];
                $store_info['store_id'] = $shop_detail['shop_id'];
                $store_info['store_name'] = $shop_detail['shop_name'];
                $store_info['store_logo'] = $shop_detail['shop_logo'];
                $store_info['store_tel'] = $shop_detail['shop_tel'];
                $store_info['shop_wap_index'] = $shop_detail['shop_wap_index'];
                $store_info['shop_u_id'] = $ucenter_user_info['u_id'];
                $store_credit = array();
                
                $store_credit['store_deliverycredit'] = array();
                $store_credit['store_deliverycredit']['credit'] = number_format($shop_detail['shop_send_scores'], 2, '.', '');
                $store_credit['store_deliverycredit']['text'] = "物流";
                
                $store_credit['store_desccredit'] = array();
                $store_credit['store_desccredit']['credit'] = number_format($shop_detail['shop_desc_scores'], 2, '.', '');
                $store_credit['store_desccredit']['text'] = "描述";
                
                $store_credit['store_servicecredit'] = array();
                $store_credit['store_servicecredit']['credit'] = number_format($shop_detail['shop_service_scores'], 2, '.', '');
                $store_credit['store_servicecredit']['text'] = "服务";
                
                $store_info['store_credit'] = $store_credit;
                
                $data['goods_id'] = $goods_id;
                $data['goods_info'] = $goods_info;                //商品详情

                //是否门店配送
                if($goods_info['common_is_delivery'] == 1 && Web_ConfigModel::value('Plugin_Delivery') == 1){
                    $data['is_delivery'] = 1;
                }else{
                    $data['is_delivery'] = 0;
                }
                
                $detail_id = request_int('pt_detail_id');
                $page = request_int('page', 1);
                $rows = 20;
                $mark_list = $this->pinTuanMarkModel->getMarkDetail(array('detail_id' => $detail_id, 'status' => 0), array('num' => 'desc'), $page, $rows);
                
                $data['goods_info']['pintuan_info']['mark_list'] = count($mark_list) > 0 ? count($mark_list) : 0;
                
                $data['goods_commend_list'] = $data_salle;                //推荐商品（销量）
                $data['goods_eval_list'] = $goods_eval_list;        //商品评论
                $data['goods_evaluate_info'] = $goods_evaluate_info;    //商品评论
                
                $data['goods_hair_info'] = $goods_hair_info;        //售卖区域
                $data['goods_image'] = $goods_image;            //商品图片
                $data['goods_one_image'] = $goods_detail['goods_base']['goods_image'];    //商品主图
                $data['mansong_info'] = $mansong_info;            //商品满送
                if (empty($spec_list)) {
                    $spec_list['重量'] = '';
                }
                
                if ($user_id) {
                
                    //预售商品是否已经开始
                    //查询该用户是否已购买过该商品
                    $Order_GoodsModel = new Order_GoodsModel();
                    $order_goods_cond['common_id'] = $goods_detail['goods_base']['common_id'];
                    $order_goods_cond['buyer_user_id'] = $user_id;
                    $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_REFUND_FINISH;
                    $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_CANCEL;
                    $order_goods_cond['order_goods_time:>='] = $goods_detail['goods_base']['groupbuy_starttime'];
                    $order_goods_cond['order_goods_time:<'] = $goods_detail['goods_base']['groupbuy_endtime'];
                    $order_list = $Order_GoodsModel->getByWhere($order_goods_cond);
                    $order_goods_nums =  array_column($order_list, 'order_goods_num');
                    $order_goods_num = array_sum($order_goods_nums);
                    //$order_goods_count = count($order_list);
                    if (isset($goods_detail['goods_base']['promotion_type'])) {
                        
                        $promotion_type = $goods_detail['goods_base']['promotion_type'];
                        if ($promotion_type == 'presale') {
                        
                            $lower_limit = $goods_detail['goods_base']['lower_limit'];
                            if ($lower_limit > 0 && $order_goods_num >= $lower_limit) {
                                $IsHaveBuy = 1;
                            }else{
                                $data['remain_limit'] = $lower_limit - $order_goods_num;
                            }
                        }
                    }
                }
                
                $data['spec_list'] = $spec_list;                //商品规格
                $data['spec_list_info'] = $spec_list_info;                //商品规格 详细信息
                $data['stock_list'] = $stock_list;                //规格库存
                $data['price_list'] = $price_list;                //规格库存
                $data['spec_image'] = $spec_image;                //商品颜色
                $data['store_info'] = $store_info;                //店铺信息
                $data['buyer_limit'] = $goods_detail['buy_limit'];  //限购数量
                $data['is_favorate'] = $isFavoritesGoods;        //是否收藏过商品
                $data['shop_owner'] = $shop_owner;                //是否为店主
                $data['isBuyHave'] = $IsHaveBuy;                //是否已达限购数量
                $data['good_pre'] = $good_pre;                //好评率
                
                if (Web_ConfigModel::value('Plugin_Directseller')) {
                    $data['rec_images'] = $goods_detail['recImages'];//推荐者上传图片
                }
                
                foreach ($data['goods_commend_list'] as $dkey => $dval) {
                    if ($data['goods_commend_list'][$dkey]['common_spec_name'] == null)
                        $data['goods_commend_list'][$dkey]['common_spec_name'] = '';
                    if ($data['goods_commend_list'][$dkey]['common_spec_value'] == null)
                        $data['goods_commend_list'][$dkey]['common_spec_value'] = '';
                    if ($data['goods_commend_list'][$dkey]['common_property'] == null)
                        $data['goods_commend_list'][$dkey]['common_property'] = '';
                    if ($data['goods_commend_list'][$dkey]['common_location'] == null)
                        $data['goods_commend_list'][$dkey]['common_location'] = '';
                    if ($data['goods_commend_list'][$dkey]['common_distributor_description'] == null)
                        $data['goods_commend_list'][$dkey]['common_distributor_description'] = '';
                }
                
                $a = array();
                if ($goods_info['common_spec_name']) {
                    $spec_list = array();
                    $spec_list = $goods_info['common_spec_name'];
                    foreach ($goods_info['common_spec_value_c'] as $key => $value) {
                        foreach ($value as $key1 => $value1) {
                            $arr[$key][$key1]['specs_value_id'] = $key1;
                            $arr[$key][$key1]['specs_value_name'] = $value1;
                        }
                    }
                    
                    foreach ($arr as $k => $value) {
                        $a[$spec_list[$k]] = $value;
                    }
                    
                }
                if($goods_detail['goods_transport_rule']){
                   $data['goods_transport_rule'] = $goods_detail['goods_transport_rule'];
                }
                //获取商品的促销信息
                $promotion_info = $this->getPromotionInfo($goods_id);
                $data['promotion_info'] = $promotion_info;
                $data['goods_info']['show_goods_spec_str'] = $show_goods_spec_str;
                $data['goods_info']['spec_list'] = $a;//小程序获取规格列表
                $data['store_info']['is_open_Im'] = Yf_Registry::get('im_statu');
                //商品的分销链接
//                $data['share'] = Yf_Registry::get('shop_wap_url') . '/tmpl/pintuan_detail.html?goods_id=' . $goods_id . '&pt_detail_id=' . $data['goods_info']['pintuan_info']['detail']['id'];
                // Plugin_Fenxiao 
                if (Web_ConfigModel::value('Plugin_Fenxiao')) {
                    $user_id = Perm::$userId?Perm::$userId:request_int('uuid');
                    $data['share'] = Yf_Registry::get('shop_wap_url') . '/tmpl/product_detail.html?goods_id=' . $goods_id.'&uu_id='.$user_id;
                }elseif(Web_ConfigModel::value('Plugin_Directseller')){
                    $user_id = Perm::$userId?Perm::$userId:request_int('uuid');
                    $data['share'] = Yf_Registry::get('shop_wap_url') . '/tmpl/product_detail.html?goods_id=' . $goods_id.'&uu_id='.$user_id;
                }else{
                    $data['share'] = Yf_Registry::get('shop_wap_url') . '/tmpl/product_detail.html?goods_id=' . $goods_id;
                } 
                $data['goods_info']['chain_stock'] = $goods_detail['chain_stock'];
                $data['goods_info']['plus_status'] = $goods_detail['goods_base']['plus_status'];
                $data['goods_info']['plus_price'] = $goods_detail['goods_base']['plus_price'];
                $data['goods_info']['user_ids'] =Perm::$userId;

                $data['contract_type_id'] = array_merge($consult_data);
//                var_dump($data['contract_type_id']);die;
                $data['rgl_str'] = $rgl_str;
                if($data['goods_info']['promotion_type']=='seckill'){
                    $data['buyer_limit'] = $data['goods_info']['seckill_lower_limit'];
                }
                if($data['goods_info']['promotion_type']=='presale'){
                    $data['buyer_limit'] = $data['goods_info']['presale_lower_limit'];
                }
                if($data['goods_info']['common_is_directseller'] ==1 && Web_ConfigModel::value('Plugin_Directseller') == 1){
                    $promotion_price = $data['goods_info']['promotion_price'] ? $data['goods_info']['promotion_price'] : $data['goods_info']['goods_price'];
                    $data['goods_info']['common_a_first'] = number_format($data['goods_info']['common_a_first'] * $promotion_price / 100, 2);
                    $data['goods_info']['common_c_first'] = number_format($data['goods_info']['common_c_first'] * $promotion_price / 100, 2);
                }
                $user_id=Perm::$userId;
                $User_InfoModel = new User_InfoModel();
                $info = $User_InfoModel->getOne($user_id);
                //分销员类型 0.分销客 1.分销掌柜
                $data['goods_info']['distributor_type'] = $info['distributor_type']; 
                $data['goods_info']['distributor_open'] = Web_ConfigModel::value('Plugin_Directseller'); 
                $data['poster_head'] = Web_ConfigModel::value('mall_poster',0);
                $this->data->addBody(-140, $data);
            } else {
                //获取商品的物流和运费信息
                $cookie_area = $this->getCookieArea();

                if ($cookie_area['city']['id']) {
                    $transportInfo = $this->getTramsportData($cookie_area['city']['id'], $goods_detail['goods_base']['common_id']);
                    $transportInfo['area'] = $cookie_area['area'];
                    $transportInfo['area_id'] = $cookie_area['city']['id'];
                } else {
                    $transportInfo = array();
                }
                $goods_detail['transport'] = $transportInfo;

                //商品配送规则
                $area_id[] = $cookie_area['city']['id'];
                $GoodsTransportModel = new GoodsTransportModel();
                $goods_transport_rule = $GoodsTransportModel->getTransportInfo($area_id, $goods_detail['shop_base']['shop_id']);
                $goods_detail['goods_transport_rule'] = $goods_transport_rule;
                include $this->view->getView();
            }
            
        }
        
        public function getCartGoodsNum($common_id)
        {
            $goods_id = request_int("goods_id");
//        $common_id = request_int("common_id");
            $cart_num = 0;
            
            $user_id = Perm::$row['user_id'];
            $cond_row['user_id'] = $user_id;
            
            //获取该商品所有规格
            $goods_base_model = new Goods_BaseModel();
            $goods_bases = $goods_base_model->getByWhere(array("common_id" => $common_id));
            //查询该规格在购物车中的数量
            if (is_array($goods_bases)) {
                foreach ($goods_bases as $goods_base) {
                    $cond_row['goods_id'] = $goods_base['id'];
                    $cart_model = new CartModel();
                    $cart = $cart_model->getByWhere($cond_row, array());
                    $cart = current($cart);
                    $cart_num += $cart['goods_num'];
                }
            }
            
            return $cart_num;
//
//            var_dump($cart_num);
//
            //查询该用户是否购买过该商品
            
        }
        
        private function getTramsportData($area_id, $common_id)
        {
            $goodsBaseModel = new Goods_BaseModel();
            $result = $goodsBaseModel->getTransportInfo($area_id, $common_id);
            return $result;
        }
        
        /**
         * author yuli
         * 获取促销信息，此方法不考虑团购情况和限时折扣
         *
         * 促销分为两类：
         *        1、针对于商品=>加价购
         *        2、不针对于商品=>满即送
         *
         * @param $goods_id int
         *
         * @return array
         */
        public function getPromotionInfo($goods_id)
        {
            $goodsBaseModel = new Goods_BaseModel();
            $goods_data = $goodsBaseModel->getOne($goods_id);
            
            $result = [];
            
            $shop_id = $goods_data['shop_id'];
            
            $result['jia_jia_gou'] = $this->getPromotionByJiaJia($goods_id, $shop_id);
            $result['man_song'] = $this->getPromotionByManSong($shop_id);
            $voucher_model = new Voucher_TempModel();
            $voucher_list = $voucher_model->getShopVoucher($shop_id);
            $result['voucher_list'] = $voucher_list['items'] ? $voucher_list['items'] : array();
            return array_filter($result);
        }
        
        /*
         * 获取商品咨询
         */
        
        /**
         * 加价购，判断该商品是否启用加价购促销信息
         * 同一时间同一商品只有一个加价购活动
         *
         * @param $goods_id int
         * @param $shop_id  int
         *
         * @return boolean or array
         */
        private function getPromotionByJiaJia($goods_id, $shop_id)
        {
            $increaseBaseModel = new Increase_BaseModel;
            
            //获取正常的加价购列表
            $increase_rows = $increaseBaseModel->getByWhere(array(
                'shop_id' => $shop_id, //对应店铺
                'increase_state' => Increase_BaseModel::NORMAL //活动状态正常
            ));
            
            if (empty($increase_rows)) {
                return false; //没有该促销信息
            }
            
            //筛选出加价购促销是否含有所需要的商品
            $increase_ids = array_keys($increase_rows);
            $increaseGoodsModel = new Increase_GoodsModel;
            
            $increase_goods_rows = $increaseGoodsModel->getByWhere(array(
                'increase_id:IN' => $increase_ids,
                'goods_id' => $goods_id
            ));
            
            if (empty($increase_goods_rows)) {
                return false; //没有该商品促销信息
            }
            
            //商品启用了加价购促销
            $answer_increase_data = current($increase_goods_rows);
            $answer_increase_id = $answer_increase_data['increase_id'];
            
            $jia_jia_data = $increaseBaseModel->getIncreaseActDetail($answer_increase_id);
            
            //格式化redemption_goods
            foreach ($jia_jia_data['rule'] as $k => $rule) {
                $jia_jia_data['rule'][$k]['redemption_goods'] = array_values($rule['redemption_goods']);
            }
            
            return $jia_jia_data;
        }
        
        /**
         * 满送
         * 同一时间只有一个满送活动
         *
         * @param $shop_id int
         *
         * @return boolean or array
         */
        private function getPromotionByManSong($shop_id)
        {
            $manSongBaseModel = new ManSong_BaseModel();
            
            $mansong_rows = $manSongBaseModel->getByWhere(array(
                'shop_id' => $shop_id, //对应店铺
                'mansong_state' => ManSong_BaseModel::NORMAL //活动状态正常
            ));
            
            if (empty($mansong_rows)) {
                return false; //没有该促销信息
            }
            
            $result_mansong_rows = $manSongBaseModel->getManSongActItem(array(
                'mansong_id:IN' => array_keys($mansong_rows)
            ));
            
            return $result_mansong_rows;
        }
        
        /**
         * 商品预览  goodspreview
         *
         * @access public
         */
        public function goodspreview()
        {
            $cid = request_int('cid');
            $goods_id = request_int('gid', request_int('goods_id'));
            $met = request_string('met');
            $auth = request_string('au');
            //如果传递过来的是common_id，则从此common_id中的goods_id中选择一个有效的goods_id
            if ($cid && !$goods_id) {
                $Goods_CommonModel = new Goods_CommonModel();
                $cond_row = array();
                $cond_row['common_id'] = $cid;
                $data = $Goods_CommonModel->getGoodsList($cond_row);
                
                $goods_id = $data['items'][0]['goods_id'];
            }
            
            //区分wap pc端
            if ($this->typ == 'json') {
                $is_wap = true;
            } else {
                $is_wap = false;
            }
            
            //添加商品点击数
            $Goods_BaseModel = new Goods_BaseModel();
            
            //1.商品信息（商品活动信息，评论数，销售数，咨询数）
            $goods_detail = $Goods_BaseModel->getGoodsDetailInfoByGoodId($goods_id, false);
            
            if (!$goods_detail) {
                $_REQUEST['msg'] = __('抱歉，该商品已下架或者该店铺已关闭！');
                $this->view->setMet('404');
                return include $this->view->getView();
            } else {
                $shopId = Perm::$shopId;
                //2.店铺信息
                $Shop_BaseModel = new Shop_BaseModel();
                $shop_detail = $Shop_BaseModel->getShopDetail($goods_detail['goods_base']['shop_id']);
                
                if ($auth != 'ad') {
                    //判断当前商品是否属于当前店铺
                    if ($goods_detail['goods_base']['shop_id'] != $shopId) {
                        $_REQUEST['msg'] = __('抱歉，您只能预览自己店铺商品！');
                        $this->view->setMet('404');
                        return include $this->view->getView();
                    }
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
                $IsOfflineBuy = 0;
                
                //计算限购数量
                if (isset($goods_detail['goods_base']['upper_limit']) && $goods_detail['goods_base']['groupbuy_starttime'] <= date('Y-m-d H:i:s') && $goods_detail['goods_base']['groupbuy_endtime'] >= date('Y-m-d H:i:s')) {
                    if ($goods_detail['goods_base']['upper_limit'] && $goods_detail['common_base']['common_limit']) {
                        if ($goods_detail['goods_base']['upper_limit'] >= $goods_detail['common_base']['common_limit']) {
                            $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit'];
                        } else {
                            $goods_detail['buy_limit'] = $goods_detail['goods_base']['upper_limit'];
                        }
                    } elseif ($goods_detail['goods_base']['upper_limit'] && !$goods_detail['common_base']['common_limit']) {
                        $goods_detail['buy_limit'] = $goods_detail['goods_base']['upper_limit'];
                    } elseif (!$goods_detail['goods_base']['upper_limit'] && $goods_detail['common_base']['common_limit']) {
                        $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit'];
                    } else {
                        $goods_detail['buy_limit'] = 0;
                    }
                } else {
                    $goods_detail['buy_limit'] = $goods_detail['common_base']['common_limit'];
                }
                
                $shop_id = $shop_detail['shop_id'];
                $Goods_CommonModel = new Goods_CommonModel();
                if ($shop_id) {
                    $data_recommon = $Goods_CommonModel->listByWhere(array(
                        'shop_id' => $shop_id
                    ), array('common_is_recommend' => 'desc', 'common_sell_time' => 'desc'), 0, 4);
                    $data_recommon_goods = $Goods_CommonModel->getRecommonRow($data_recommon);
                    
                    //推荐商品
                    $data_foot_recommon = $Goods_CommonModel->listByWhere(array(
                        'shop_id' => $shop_id
                    ), array('common_is_recommend' => 'DESC'), 0, 5);
                    $data_foot_recommon_goods = $Goods_CommonModel->getRecommonRow($data_foot_recommon);
                    
                    //热门销售
                    $data_hot_salle = $Goods_CommonModel->getHotSalle($shop_id, $is_wap);
                    $data_salle = $Goods_CommonModel->getRecommonRow($data_hot_salle);
                    //热门收藏
                    $data_hot_collect = $Goods_CommonModel->getHotCollect($shop_id);
                    $data_collect = $Goods_CommonModel->getRecommonRow($data_hot_collect);
                    
                    //商品咨询数量
                    $Consult_BaseModel = new Consult_BaseModel();
                    $data_consult = $Consult_BaseModel->getByWhere(array(
                        'goods_id' => $goods_id,
                        'shop_id' => $shop_id
                    ));
                    $consult_num = count($data_consult);
                }
            }
            
            include $this->view->getView();
        }
        
        public function IsHaveBuy()
        {
            //团购商品是否已经开始
            //查询该用户是否已购买过该商品
            $goods_id = request_int('goods_id');
            
            $Goods_BaseModel = new Goods_BaseModel();
            
            $goods_detail = $Goods_BaseModel->getGoodsDetailInfoByGoodId($goods_id);
            $IsHaveBuy = 0;
            $Order_GoodsModel = new Order_GoodsModel();
            $order_goods_cond['common_id'] = $goods_detail['goods_base']['common_id'];
            $order_goods_cond['buyer_user_id'] = Perm::$userId;
            $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_REFUND_FINISH;
            $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_CANCEL;
            $order_list = $Order_GoodsModel->getByWhere($order_goods_cond);
            
            $order_goods_count = 0;
            foreach ($order_list as $val) {
                if ($val['order_goods_benefit'] && $val['common_id'] == $goods_detail['goods_base']['common_id']) {
                    $order_goods_count += $val['order_goods_num'];
                }
                
            }
            if (isset($goods_detail['goods_base']['promotion_type'])) {
                $promotion_type = $goods_detail['goods_base']['promotion_type'];
                
                if ($promotion_type == 'groupbuy') {
                    //检测是否限购数量
                    $upper_limit = $goods_detail['goods_base']['upper_limit'];
                    if ($upper_limit > 0 && $order_goods_count >= $upper_limit) {
                        $IsHaveBuy = 1;
                    }
                }
            }
            
            //商品限购数量判断
            if ($goods_detail['common_base']['common_limit'] > 0 && $order_goods_count >= $goods_detail['common_base']['common_limit']) {
                $IsHaveBuy = 1;
            }
            
            $data['IsHaveBuy'] = $IsHaveBuy;
            
            $this->data->addBody(-140, $data);
            
            return $data;
            
        }
        
        /**
         * 取得商品信息
         *
         * @access public
         */
        public function getGoodsDetailInfo()
        {
            $goods_id = request_int("gid");
            
            //商品detail信息
            $Goods_BaseModel = new Goods_BaseModel();
            $data['goods'] = $Goods_BaseModel->getGoodsDetailInfoByGoodId($goods_id);
            
            $this->data->addBody(-140, $data);
            
            return $data;
        }
        
        //虚拟兑换码过期之前提醒
        
        /**
         * 获取店铺分类
         *
         * @access public
         */
        
        public function getShopCat()
        {
            $shop_id = request_int("shop_id");
            $shopGoodCatModel = new Shop_GoodCatModel();
            $cat_row['shop_id'] = $shop_id;
            $shop_cat = $shopGoodCatModel->getGoodCatList($cat_row);
            
            if ('json' == $this->typ) {
                $shopBaseModel = new Shop_BaseModel();
                $shop_base = $shopBaseModel->getBase($shop_id);
                $shop_base = pos($shop_base);
                
                $shop_cat = array_values($shop_cat);
                $data['store_goods_class'] = $shop_cat;
                $data['shop_id'] = $shop_id;
                $data['shop_name'] = $shop_base['shop_name'];
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
            
        }
        
        /**
         * 取得商品销售信息
         *
         * @access public
         */
        public function getGoodsSaleList()
        {
            $goods_id = request_int('goods_id');
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = 10;
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            
            $Order_GoodsModel = new Order_GoodsModel();
            $cond_row = array();
            $cond_row['goods_id'] = $goods_id;
            $data = $Order_GoodsModel->getGoodSaleList($cond_row, array('order_goods_id' => 'DESC'), $page, $rows);
            
            fb($data);
            fb('销售记录');
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav = $Yf_Page->ajaxprompt();
            
            include $this->view->getView();
        }
        
        public function getConsultListRows()
        {
            $goods_id = request_int('goods_id');
            
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_base = $Goods_BaseModel->getOne($goods_id);
            
            $Yf_Page = new Yf_Page();
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            
            $ConsultBaseModel = new Consult_BaseModel();
            $cond_row = array();
            $cond_row['goods_id'] = $goods_id;
            $consult_base_data = $ConsultBaseModel->getBaseList($cond_row, array(), $page, $rows);
            
            $Yf_Page->totalRows = $consult_base_data['totalsize'];
            $page_nav = $Yf_Page->ajaxprompt();
            
            //头部
            $Web_ConfigModel = new Web_ConfigModel();
            $head = $Web_ConfigModel->getConfigValue('consult_header_text');
            
            include $this->view->getView();
        }
        
        /**
         * 取得商品评价信息
         *
         * @access public
         */
        public function getGoodsEvaluationList()
        {
            $common_id = request_int('common_id');
            $type = request_string('type', 'all');
            $source = request_string('sou', 'pc');
            
            if ($this->typ == 'json') {
                //wap  根据goods_id 找 common_d
                $goods_id = request_int('goods_id');
                $goodsBaseModel = new Goods_BaseModel();
                $goods_base = $goodsBaseModel->getBase($goods_id);
                $goods_base = pos($goods_base);
                $common_id = $goods_base['common_id'];
            }
            
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
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            
            $order_row = array();
            $cond_row['common_id'] = $common_id;
            
            $cond_row['status:!='] = Goods_EvaluationModel::DISPLAY;
            $order_row['status'] = 'DESC';
            $order_row['create_time'] = 'DESC';
            
            if ($this->typ == 'json') {
                $page = request_int('curpage');
                $rows = request_int('page');
                
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
            }
            
            $data = $Goods_EvaluationModel->getEvaluationList($cond_row, $order_row, $page, $rows, $type);
            $data['items'] = array_values($data['items']);
            
            fb($data);
            fb('评论列表');
            
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav = $Yf_Page->ajaxprompt();
            
            if ($source == 'wap') {
                $this->data->addBody(-140, $data);
            } else {
                include $this->view->getView();
            }
        }
        
        //交易快照
        
        /**
         * 收藏商品
         *
         * @author     Zhuyt
         */
        public function collectGoods()
        {
            $goods_id = request_int('goods_id');
            
            if (Perm::checkUserPerm()) {
                $user_id = Perm::$userId;
                //用户登录情况下,插入用户收藏商品表
                $add_row = array();
                $add_row['user_id'] = $user_id;
                $add_row['goods_id'] = $goods_id;
                
                $User_FavoritesGoodsModel = new User_FavoritesGoodsModel();
                //开启事物
                $User_FavoritesGoodsModel->sql->startTransactionDb();
                
                $res = $User_FavoritesGoodsModel->getFavoritesGoods($add_row);
                
                if ($res) {
                    $flag = false;
                    $data['msg'] = __("您已收藏过该商品！");
                    
                } else {
                    $add_row['favorites_goods_time'] = get_date_time();
                    
                    $User_FavoritesGoodsModel->addGoods($add_row);
                    
                    //商品详情中收藏数量增加
                    $Goods_BaseModel = new Goods_BaseModel();
                    $goods_base = $Goods_BaseModel->getOne($goods_id);
                    $edit_row = array();
                    $edit_row['goods_collect'] = '1';
                    $flag = $Goods_BaseModel->editBase($goods_id, $edit_row, true);
                    
                    //商品common中
                    $Goods_CommonModel = new Goods_CommonModel();
                    $edit_common_row = array();
                    $edit_common_row['common_collect'] = '1';
                    $Goods_CommonModel = $Goods_CommonModel->editCommonTrue($goods_base['common_id'], $edit_common_row);
                    
                }
                
            } else {
                $flag = false;
            }
            
            if ($flag && $User_FavoritesGoodsModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
                $data['msg'] = $data['msg'] ? $data['msg'] : __("收藏成功！");
                
                //删除收藏商品成功添加数据到统计中心
                $analytics_data = array(
                    'product_id' => $goods_id,
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsProductCollect', $analytics_data);
                /******************************************************/
            } else {
                $User_FavoritesGoodsModel->sql->rollBackDb();
                $m = $User_FavoritesGoodsModel->msg->getMessages();
                $msg = $m ? $m[0] : __('failure');
                $status = 250;
                $data['msg'] = $data['msg'] ? $data['msg'] : __("收藏失败！");
            }
            
            $this->data->addBody(-140, $data, $msg, $status);
        }
        
        /**
         * 取消收藏商品
         *
         * @author     Zhuyt
         */
        public function canleCollectGoods()
        {
            $goods_id = request_int('goods_id');
            
            if (Perm::checkUserPerm()) {
                $user_id = Perm::$userId;
                //用户登录情况下,删除用户收藏商品
                $fav_row = array();
                $fav_row['user_id'] = $user_id;
                $fav_row['goods_id'] = $goods_id;
                
                $User_FavoritesGoodsModel = new User_FavoritesGoodsModel();
                //开启事物
                $User_FavoritesGoodsModel->sql->startTransactionDb();
                $res = $User_FavoritesGoodsModel->getFavoritesGoods($fav_row);
                
                if ($res) {
                    $User_FavoritesGoodsModel->removeGoods($res['favorites_goods_id']);
                }
                
                //商品详情中收藏数量减少
                $Goods_BaseModel = new Goods_BaseModel();
                $goods_base = $Goods_BaseModel->getOne($goods_id);
                $edit_row = array();
                $edit_row['goods_collect'] = '-1';
                $flag = $Goods_BaseModel->editBase($goods_id, $edit_row, true);
                
                //商品common中收藏数量减少
                $Goods_CommonModel = new Goods_CommonModel();
                $edit_common_row = array();
                $edit_common_row['common_collect'] = '1';
                $Goods_CommonModel = $Goods_CommonModel->editCommonTrue($goods_base['common_id'], $edit_common_row);
            } else {
                $flag = false;
            }
            
            if ($flag && $User_FavoritesGoodsModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
            } else {
                $User_FavoritesGoodsModel->sql->rollBackDb();
                $m = $User_FavoritesGoodsModel->msg->getMessages();
                $msg = $m ? $m[0] : __('failure');
                $status = 250;
            }
            $data = array();
            $this->data->addBody(-140, $data, $msg, $status);
        }
        
        public function getGoodsIdBySpec()
        {
            $common_id = request_int('common_id');
            $spec = request_row('spec');
            $Goods_BaseModel = new Goods_BaseModel();
            $res = $Goods_BaseModel->getBaseSpecByCommonId($common_id);
            $spec=json_decode('[' . join(',', $spec) . ']', true);
            natsort($spec);
            $data = array();
            foreach ($res as $ke => $val) {
                $key = array_keys($val);
                natsort($key);
                $boolean = array_diff($key,$spec);
                if (empty($boolean)) {  
                    $data['goods_id'] = $ke;
                }
            }
            $this->data->addBody(-140, $data);
        }
        
        public function VirtualCodeAuto()
        {
            $Goods_CommonModel = new Goods_CommonModel();
            //1.查找出过期退款的虚拟商品
            $goods_cond_row['common_is_virtual'] = Goods_CommonModel::GOODS_VIRTUAL;
            $goods_cond_row['common_virtual_refund'] = Goods_CommonModel::GOODS_VIRTUAL_REFUND;
            $goods_cond_row['common_virtual_date:<'] = date("Y-m-d H:i:s", strtotime("+2 day"));
            $common_base = $Goods_CommonModel->getByWhere($goods_cond_row);
            $common_id = array_column($common_base, 'common_id');
            
            //2.查找出虚拟订单中未使用的订单商品
            $order_goods_cond_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
            
            //3.查找出不为退款订单商品
            $order_goods_cond_row['goods_refund_status'] = Order_GoodsModel::REFUND_NO;
            
            $order_goods_cond_row['common_id:IN'] = $common_id;
            
            $Order_GoodsModel = new Order_GoodsModel();
            $order_goods = $Order_GoodsModel->getByWhere($order_goods_cond_row);
            
            foreach ($order_goods as $key => $val) {
                //兑换码即将到期提醒
                //[end_time]
                $message = new MessageModel();
                $message->sendMessage('Redemption code is about to expire reminder', $val['buyer_user_id'], __('亲爱的会员'), $order_id = NULL, $shop_name = NULL, 0, 1, $end_time = $common_base[$val['common_id']]['common_virtual_date']);
            }
            
        }
        
        /**
         *  获取运费和售卖区域
         */
        public function getTramsport()
        {
            $area_id = request_int('area_id');
            $common_id = request_int('common_id');
            
            $result = $this->getTramsportData($area_id, $common_id);
            $data = $result;
            $status = $result['result'] ? 200 : 250;
            $msg = $result['result'] ? 'success' : 'failure';
            return $this->data->addBody(-140, $data, $msg, $status);
        }
        
        //当用户点击查看商品详情时，向统计中心发送数据
        
        public function snapshot()
        {
            $order_id = request_string('order_id');
            $goods_id = request_int('goods_id');
            
            $Order_GoodsSnapshotModel = new Order_GoodsSnapshotModel();
            $snapshot = $Order_GoodsSnapshotModel->getByWhere(array('order_id' => $order_id, 'goods_id' => $goods_id));
            
            $snapshot = current($snapshot);
            
            //商品详情
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_base = $Goods_BaseModel->getOne($snapshot['goods_id']);
            
            //查找店铺信息
            $Shop_BaseModel = new Shop_BaseModel();
            $shop_detail = $Shop_BaseModel->getShopDetail($snapshot['shop_id']);
            
            $Shop_CompanyModel = new Shop_CompanyModel();
            $shop_company = $Shop_CompanyModel->getOne($shop_detail['shop_id']);
            
            //订单信息
            $Order_BaseModel = new Order_BaseModel();
            $order_base = $Order_BaseModel->getOne($snapshot['order_id']);
            fb($order_base);
            
            include $this->view->getView();
        }
        
        /**
         * ly
         * wap 获取商品详情信息
         */
        public function getCommonDetail(){
            $goods_id = request_int('goods_id');

            $goodsBaseModel = new Goods_BaseModel();
            $goodsCommonDetailModel = new Goods_CommonDetailModel();
            
            $goods_base = $goodsBaseModel->getBase($goods_id);
            $goods_base = pos($goods_base);
            
            $common_id = $goods_base['common_id'];
            $common_detail_base = $goodsCommonDetailModel->getCommonDetail($common_id);
            $common_detail_base = pos($common_detail_base);
            $data = array();
            $data['common_body'] = str_replace('type=webp', 'type=jpg', $common_detail_base['common_body']);
            $Goods_CommonModel = new Goods_CommonModel();
            $Goods_FormatModel = new Goods_FormatModel();
            $goods_data = $goodsBaseModel->getOne($goods_id);
            $common_id = $goods_data['common_id'];
            $common_data = $Goods_CommonModel->getOne($common_id);
            $goods_detail = $goodsBaseModel->getGoodsDetailInfoByGoodId($goods_id, false);
            $goods_check = $goodsBaseModel->checkGoodsII($goods_id);
            if ($goods_detail && $goods_check) {
                if ($common_data) {
                    $common_formatid_top = $common_data['common_formatid_top'];
                    if ($common_formatid_top) {
                        //ÉÌÆ·ÏêÇéÍ·²¿ÐÅÏ¢
                        $goods_format_top = $Goods_FormatModel->getOne($common_formatid_top);
                        if (isset($goods_format_top) && !empty($goods_format_top)) {
                            $data['goods_format_top'] = $goods_format_top['content'];
                        } else {
                            $data['goods_format_top'] = '';
                        }
                    } else {
                        $data['goods_format_top'] = '';
                    }

                    $common_formatid_bottom = $common_data['common_formatid_bottom'];
                    if ($common_formatid_bottom) {
                        $goods_format_bottom = $Goods_FormatModel->getOne($common_formatid_bottom);
                        if (isset($goods_format_bottom) && !empty($goods_format_bottom)) {
                            $data['goods_format_bottom'] = $goods_format_bottom['content'];
                        } else {
                            $data['goods_format_bottom'] = '';
                        }
                    } else {
                        $data['goods_format_bottom'] = '';
                    }
                }
            }
            $this->data->addBody(-140, $data);
        }
        
        /**
         * zcg
         * 取得门店信息
         *
         * @access public
         */
        public function getChain()
        {
            $chain_id = request_int("chain_id");
            $Chain_BaseModel = new Chain_BaseModel();
            $chan_base = current($Chain_BaseModel->getBase($chain_id));
            include $this->view->getView();
        }
        
        /**
         * zcg
         * 取得门店
         *
         * @access public
         */
        public function chain()
        {
            //地区
            $district_parent_id = request_int('pid', 0);
            $Base_DistrictModel = new Base_DistrictModel();
            $district = $Base_DistrictModel->getDistrictTree($district_parent_id);
            $Chain_GoodsModel = new Chain_GoodsModel();
            $Chain_UserModel = new Chain_UserModel();
            $Shop_BaseModel = new Shop_BaseModel();
            $chain_BaseModel = new Chain_BaseModel();
            $CartModel = new CartModel();
            $user_id = Perm::$userId;
            
            $shop_id = request_int("shop_id");
            $goods_id = request_int("goods_id");
            $goods_num = request_int("goods_num");
            $goods_shop_info = $Chain_GoodsModel->getOneByWhere(array('goods_id' => $goods_id));
            $chain_info = $Chain_UserModel->getOneByWhere(array('user_id' => $user_id));
            $buyer_shop_info = $Shop_BaseModel->getOneByWhere(array('user_id' => $user_id));
            
            $chain_goods_where = array(
                'shop_id' => $shop_id,
                'goods_id' => $goods_id,
            );
            
            $chain_goods_rows = $Chain_GoodsModel->getByWhere($chain_goods_where);
            $chain_ids = array_column($chain_goods_rows, 'chain_id');

            $chain_row['chain_id:IN'] = $chain_ids;
            $chain_row['status'] = 1;//店铺开启
            $chain_rows = $chain_BaseModel->getByWhere($chain_row);
            $chain_id = array_column($chain_rows,'chain_id');//排除关闭的门店
            $chain_goods_row = $Chain_GoodsModel->getByWhere(array('shop_id' => $shop_id,'goods_id'=>$goods_id,'chain_id:IN'=>$chain_id));
            $chain_stock = array_column($chain_goods_row,'goods_stock','chain_id');

            $chain_provinces = [];
            $chain_cities = [];

            $a = array();
            foreach ($chain_rows as $key=>$chain_data) {
                $chain_province_id = $chain_data['chain_province_id'];
                $chain_city_id = $chain_data['chain_city_id'];
                if(!is_array($chain_cities[$chain_city_id]))
                {
                    $chain_cities[$chain_city_id] = array();
                }
                
                $chain_provinces[$chain_province_id][] = $chain_city_id;
                $chain_data['goods_stock'] = $chain_stock[$chain_data['chain_id']];
                $chain_rows[$key]['goods_stock'] = $chain_stock[$chain_data['chain_id']];

                if($chain_data['chain_id'] <= 0)
                {
                    array_push($a,$chain_rows[$key]);
                    array_push($chain_cities[$chain_city_id],$chain_data);
                }
                else
                {
                    array_unshift($a,$chain_rows[$key]);
                    array_unshift($chain_cities[$chain_city_id],$chain_data);
                }

            }

            
            //获取定位地址
            $current_location_info = current_location();
            $current_province = $current_location_info['province'];
            
            $data = [];
            $data['current_province'] = $current_province;
            $data['chain_provinces'] = $chain_provinces;
            $data['chain_cities'] = $chain_cities;
            $data['chain_rows'] = array_values($a);
            
            $chain_provinces = json_encode($chain_provinces);
            $chain_cities = json_encode($chain_cities);

            //是否同城配送
            $Goods_CommonModel = new Goods_CommonModel();
            $goods_common = $Goods_CommonModel->getOne($goods_shop_info['common_id']);
            if($goods_common['common_is_delivery'] == 1 && Web_ConfigModel::value('Plugin_Delivery') == 1){
                $data['is_delivery'] = 1;
            }
            
            //判断当前买家是否是当前门店商品店家
            if ('json' == request_string("typ")) {
                
                $msg = 'success';
                $status = 200;
                if (($chain_info && $goods_shop_info['shop_id'] == $chain_info['shop_id']) || ($buyer_shop_info && $buyer_shop_info['shop_id'] == $goods_shop_info['shop_id'])) {
                    $msg = '不能购买自己门店商品！';
                    $status = 250;
                } elseif ($goods_num < 1) {
                    $msg = '购买数量有误！';
                    $status = 250;
                } else {
                    $cart['user_id'] = $user_id;
                    $cart['shop_id'] = $shop_id;
                    $cart['goods_id'] = $goods_id;
                    $cart_data = $CartModel->getOneByWhere($cart);
                    if ($cart_data) {
                        $edit_flag = $CartModel->editCart($cart_data['cart_id'], array('goods_num' => $goods_num), false);
                        if ($edit_flag === false) {
                            $msg = '修改购物车失败！';
                            $status = 250;
                        }
                    } else {
                        $cart['goods_num'] = $goods_num;
                        $flag = $CartModel->addCart($cart);
                        if (!$flag) {
                            $msg = '添加购物车失败！';
                            $status = 250;
                        }
                    }
                    
                }
                
                return $this->data->addBody(-140, $data, $msg, $status);
            } else {
                include $this->view->getView();
            }
            
        }

        /**
         * 获取来源信息
         * @return string
         */
        public function getUserAgent()
        {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/ipad/i', $agent)) {
                return 'ipad';
            } else if (preg_match('/iphone\s*os/i', $agent)) {
                return 'iphone';
            } else if (preg_match('/android|wp7|wp8|wp9|wp10|wp11|surface|nokia|sumsang/i', $agent)) {
                return 'android';
            } else if (preg_match('/wbxml|wml/i', $_SERVER['HTTP_ACCEPT'])) {
                return 'wap';
            } else {
                return 'PC';
            }
        }

        public function analytic_goods()
        {
            $goods_id = request_int('goods_id');//数模id和工坊id不会同时存在
            $shop_id = request_int('shop_id');
            $skip_url = request_string('url');
            $from = $this->getUserAgent();
            $uv = 1;
            $pv = 1;
            $ip = get_ip();
            $date = date("Y-m-d", time());
            if ($goods_id) {
                $Goods_Base = new Goods_BaseModel;
                $goodsbase = current($Goods_Base->getBase($goods_id));
                $analytics_data = array(
                    'product_id' => $goods_id,
                    'product_name' => $goodsbase['goods_name'],
                    'shop_id' => $goodsbase['shop_id'],
                    'date' => $date,
                    'url' => $skip_url,
                    'from' => $from,
                    'uv_num' => $uv,
                    'pv_num' => $pv,
                    'ip' => $ip,
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsUvCount', $analytics_data);
                /******************************************************/
            } elseif ($shop_id) {
                $analytics_data = array(
                    'shop_id' => $shop_id,
                    'date' => $date,
                    'url' => $skip_url,
                    'from' => $from,
                    'uv_num' => $uv,
                    'pv_num' => $pv,
                    'ip' => $ip,
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsUvCount', $analytics_data);
                /******************************************************/
            } else {
                $analytics_data = array(
                    'date' => $date,
                    'url' => $skip_url,
                    'from' => $from,
                    'uv_num' => $uv,
                    'pv_num' => $pv,
                    'ip' => $ip,
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsUvCount', $analytics_data);
                /******************************************************/
            }
            return $this->data->addBody(-140, $analytics_data, '', '');
        }

        
        public function analytic_goods1()
        {
            $goods_id = request_int('goods_id');//商品id和店铺id不会同时存在
            $shop_id = request_int('shop_id');
            $skip_url = request_string('url');
            $from = request_string('from');
            $uv = request_int('uv_num');
            $date = date("Y-m-d", strtotime(request_string('date')));
            if ($goods_id) {
                $Goods_Base = new Goods_BaseModel;
                $goodsbase = current($Goods_Base->getBase($goods_id));
                $analytics_data = array(
                    'product_id' => $goods_id,
                    'product_name' => $goodsbase['goods_name'],
                    'shop_id' => $goodsbase['shop_id'],
                    'date' => $date,
                    'url' => $skip_url,
                    'from' => $from,
                    'uv_num' => $uv,
                );

                Yf_Plugin_Manager::getInstance()->trigger('analyticsUvCount', $analytics_data);
                /******************************************************/
            } elseif ($shop_id) {
                $analytics_data = array(
                    'shop_id' => $shop_id,
                    'date' => $date,
                    'url' => $skip_url,
                    'from' => $from,
                    'uv_num' => $uv,
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsUvCount', $analytics_data);
                /******************************************************/
            } else {
                $analytics_data = array(
                    'date' => $date,
                    'url' => $skip_url,
                    'from' => $from,
                    'uv_num' => $uv,
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsUvCount', $analytics_data);
                /******************************************************/
            }
        }
        
        public function checkTask()
        {
            //需要设计规则,随机触发.
            
            //db需要为master
            $Base_CronModel = new Base_CronModel();
            $rows = $Base_CronModel->checkTask();
            
            fb($rows);
        }
        
        /**
         * 获取最近两条评论信息
         */
        
        public function getGoodsNewReview()
        {
            $goods_id = request_int('goods_id');
            $goodsBaseModel = new Goods_BaseModel();
            $goods_base = $goodsBaseModel->getBase($goods_id);
            $goods_base = pos($goods_base);
            $common_id = $goods_base['common_id'];
            
            $order_row = array();
            $cond_row['common_id'] = $common_id;
            $cond_row['status:!='] = Goods_EvaluationModel::DISPLAY;
            $order_row['status'] = 'DESC';
            $order_row['create_time'] = 'DESC';
            
            $Goods_EvaluationModel = new Goods_EvaluationModel();
            $goods_review_rows = $Goods_EvaluationModel->getEvaluationList($cond_row, $order_row, 1, 2, 'all');
            $goods_review_rows = $goods_review_rows['items'];
            $result_review_rows = [];
            
            foreach ($goods_review_rows as $k => $val) {
                $row = $val[0];
                $row['spec_val_str'] = implode(',', $row['goods_spec']);
                $result_review_rows[] = $row;
            }
            
            $num = $Goods_EvaluationModel->getEvalutionNum(array_merge($cond_row, array('result:!=' => '')));
            
            $this->data->addBody(-140, array('goods_review_rows' => $result_review_rows, 'num' => $num));
        }
        
        /**
         * 获取地区并设置cookie
         *
         * @return type
         */
        public function getArea()
        {
            $cookie_area = $this->getCookieArea();
            return $this->data->addBody(-140, $cookie_area);
        }
        
        /**
         *  验证虚拟物品--购买数量判断
         */
        public function checkVirtual()
        {
            $user_id = Perm::$userId;
            $goods_id = request_int("goods_id");
            $goods_num = request_int("goods_num");
            $con['goods_id'] = $goods_id;
            $con['buyer_user_id'] = $user_id;
            $con['order_goods_status:!='] = Order_StateModel::ORDER_CANCEL;
            
            $Goods_BaseModel = new Goods_BaseModel();
            $goods_detail = $Goods_BaseModel->getOne($goods_id);
            
            $Order_GoodsModel = new Order_GoodsModel();
            $order_count = $Order_GoodsModel->getOrderGoodsNum($con);
            
            $goods_count = $order_count + $goods_num;
            //检测当前商品是否参加团购活动
            $Groupbuy_BaseModel = new GroupBuy_BaseModel();
            $con_row['goods_id'] = $goods_id;
            $con_row['groupbuy_starttime:<='] = date('Y-m-d H:i:s');
            $con_row['groupbuy_endtime:>='] = date('Y-m-d H:i:s');
            $con_row['groupbuy_state'] = GroupBuy_BaseModel::NORMAL;
            $info = $Groupbuy_BaseModel->getByWhere($con_row);
            
            if ($info) {
                $limit = 0;
                foreach ($info as $k => $v) {
                    $limit = $v['groupbuy_upper_limit'];
                }
                $goods_max_sale = $limit;
            } else {
                $goods_max_sale = $goods_detail['goods_max_sale'];
            }
            
            $msg = ($goods_max_sale > 0 && $goods_max_sale < $goods_count) ? '您已达购买上限' : '库存够';
            $status = ($goods_max_sale > 0 && $goods_max_sale < $goods_count) ? 250 : 200;
            return $this->data->addBody(-140, [], $msg, $status);
        }
        
        /**
         * 限时折扣
         * 同一时间同一商品只有一个限时活动
         *
         * @param $goods_id int
         * @param $shop_id  int
         *
         * @return boolean or array
         */
        private function getPromotionByXianShi($goods_id, $shop_id)
        {
            $discountBaseModel = new Discount_BaseModel();
            
            $discount_rows = $discountBaseModel->getByWhere(array(
                'shop_id' => $shop_id, //对应店铺
                'discount_state' => Discount_BaseModel::NORMAL //活动状态正常
            ));
            
            if (empty($discount_rows)) {
                return false; //没有该促销信息
            }
            
            //筛选出限制折扣促销是否含有所需要的商品
            $discount_ids = array_keys($discount_rows);
            $discountGoodsModel = new Discount_GoodsModel;
            
            $discount_goods_rows = $discountGoodsModel->getByWhere(array(
                'discount_id:IN' => $discount_ids,
                'goods_id' => $goods_id
            ));
            
            if (empty($discount_goods_rows)) {
                return false; //没有该商品促销信息
            }
            //商品启用了限制折扣促销
            return current($discount_goods_rows);
        }
        
        //重组wap端返回的数据
        
        private function reformData($data)
        {
            $result = array();
            foreach ($data as $key => $val) {
                $result[$key]['common_salenum'] = $val['common_salenum'];
                $result[$key]['common_evaluate'] = $val['common_evaluate'];
                $result[$key]['common_is_xian'] = $val['common_is_xian'];
                $result[$key]['common_is_tuan'] = $val['common_is_tuan'];
                $result[$key]['common_is_jia'] = $val['common_is_jia'];
                $result[$key]['common_image'] = $val['common_image'];
                $result[$key]['common_price'] = $val['common_price'];
                $result[$key]['common_name'] = $val['common_name'];
                $result[$key]['goods_id'] = $val['goods_id'];
                $result[$key]['common_cps_commission'] = $val['common_cps_commission']; //推广佣金
                $result[$key]['plus_status'] = $val['plus_status'];
                $result[$key]['plus_price'] = $val['plus_price'];

                foreach ($val['good'] as $k => $v) {
                    $result[$key]['good'][$k]['goods_id'] = $v['goods_id'];
                    $result[$key]['good'][$k]['goods_image'] = $v['goods_image'];
                    $result[$key]['good'][$k]['goods_name'] = $v['goods_name'];
                    $result[$key]['good'][$k]['goods_jingle'] = $v['goods_jingle'];
                    $result[$key]['good'][$k]['goods_price'] = $v['goods_price'];
                    $result[$key]['good'][$k]['sole_flag'] = $v['sole_flag'];
                    $result[$key]['good'][$k]['is_virtual'] = $v['is_virtual'];
                    $result[$key]['good'][$k]['is_presell'] = $v['is_presell'];
                    $result[$key]['good'][$k]['is_fcode'] = $v['is_fcode'];
                    $result[$key]['good'][$k]['xianshi_flag'] = $v['xianshi_flag'];
                    $result[$key]['good'][$k]['have_gift'] = $v['have_gift'];
                    $result[$key]['good'][$k]['is_own_shop'] = $v['is_own_shop'];
                    $result[$key]['good'][$k]['shop_id'] = $v['shop_id'];
                    $result[$key]['good'][$k]['store_name'] = $v['store_name'];
                }
            }
            
            return $result;
        }


        //判断门店库存
        public function check_chain_stock()
        {
            $chain_id = request_int('chain_id');
            $goods_id = request_int('goods_id');
            $goods_num = request_int('goods_num');
            $Chain_GoodsModel = new Chain_GoodsModel();
            $chain_goods_num = $Chain_GoodsModel->getGoodsStock($chain_id,$goods_id);
            if($goods_num <= intval($chain_goods_num))
            {
                $msg = '';
                $status = 200;
            }else{
                $msg = '该门店已售馨！';
                $status = 250;
            }
            $data['goods_num'] = $goods_num;
            $data['chain_goods_num'] = intval($chain_goods_num);
            return $this->data->addBody(140, $data, $msg, $status);
        }

        //wap端二维码分享注册成为下级分销员
        //wap端我的二维码
        public function shareWap()
        {
            $user_id = Perm::$userId?Perm::$userId:request_int('uuid');
            $url = urlencode(Yf_Registry::get('url').'?ctl=Goods_Goods&met=location_register&typ=e&uuid='.$user_id);
            $qrCode = Yf_Registry::get('base_url').'/shop/api/qrcode.php?data='.$url;
            $data['qrCode'] = $qrCode;
            $this->data->addBody(-140, $data, 'success', '200'); 
        }
        //扫描二维码跳转至注册页面
        public function location_register()
        {
            $user_id = request_int('uuid');
            setcookie('uu_id',$user_id,time()+60*60*24*3,'/');
            $url = Yf_Registry::get('shop_wap_url');
            location_to($url);
        }


        public function getDiscountGoodsList()
        {
            $Discount_GoodsModel = new Discount_GoodsModel();
            $promotion_allow = Web_ConfigModel::value("promotion_allow");
            $data = array();
            if($promotion_allow == 1) {
                $Yf_Page = new Yf_Page();
                $Yf_Page->listRows = 12;
                $rows = $Yf_Page->listRows;
                $offset = request_int('firstRow', 0);
                $page = ceil_r($offset / $rows);

                $xian_shi_name = trim(request_string('discount_name'));   //活动名称
                $discount_state = request_int('discount_state');           //活动状态
                $goods_name = request_string('goods_name');           //商品名称

                $uptime = strtolower(request_string('uptime', 'desc'));

                $price = strtolower(request_string('price', 'desc'));

                $Shop_BaseModel = new Shop_BaseModel();
                $shop_cond['shop_name:LIKE'] = '%' . trim(request_string('shop_name')) . '%';//店铺名称
                $shop_list = $Shop_BaseModel->getByWhere($shop_cond);
                $shop_ids = array_column($shop_list, 'shop_id');
                if ($shop_ids) {
                    $cond_row['shop_id:IN'] = $shop_ids;
                }
                if ($discount_state) {
                    $cond_row['discount_state'] = $discount_state;
                }
                if ($xian_shi_name) {
                    $cond_row['discount_name:LIKE'] = $xian_shi_name . '%';
                }
                if ($goods_name) {
                    $cond_row['goods_name:LIKE'] = $goods_name . '%';
                }

                $order_row = array();

                if ($uptime == 'asc') {
                    $order_row = array('discount_id' => 'ASC');
                    $uptime = 'desc';
                } else {
                    $order_row = array('discount_id' => 'DESC');
                    $uptime = 'asc';
                }

                if (request_string('price')) {
                    if ($price == 'asc') {
                        $order_row = array('discount_price' => 'ASC');
                        $price = 'desc';
                    } else {
                        $order_row = array('discount_price' => 'DESC');
                        $price = 'asc';
                    }
                }

                //首页版块置顶操作
                if (request_string('discount_goods_id')) {
                    $discount_goods_id = request_string('discount_goods_id');
                    $ids = explode(',', $discount_goods_id);
                    $discount_goods_cond_row['discount_goods_id:IN'] = $ids;
                    $discount_goods_list = $Discount_GoodsModel->getDiscountGoodsList($discount_goods_cond_row);
                    $cond_row['discount_goods_id:NOT IN'] = $ids;
                    $cond_row['discount_goods_state'] = 1;
                }
                $cond_row['goods_start_time:<'] = date('Y-m-d H:i:s');
                $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');

                $data = $Discount_GoodsModel->getDiscountGoodsList($cond_row, $order_row, $page, $rows);

                if ($discount_goods_list) {
                    $data['items'] = array_merge($discount_goods_list['items'], $data['items']);
                }

                foreach($data['items'] as $key => $val)
                {
                    $goods_rows[] = $val['goods_id'];
                }
                $Goods_BaseModel = new Goods_BaseModel();
                foreach($goods_rows as $kk=>$vv)
                {
                    $result[] = $Goods_BaseModel->getOne($vv);
                }
                foreach($data['items'] as $key => $val)
                {
                    if($val['goods_id']==$result[$key]['goods_id'])
                    {
                        $data['items'][$key]['goods_stock'] =$result[$key]['goods_stock'];
                    }
                }


                $Yf_Page->totalRows = $data['totalsize'];
                $page_nav = $Yf_Page->prompt();
                $msg = 'success';
                $status = 200;
            }else {
                $msg = '限时折扣功能已关闭';
                $status = 250;


            }

            if ('json' == $this->typ) {
                $data['items'] = array_values($data['items']);
                $this->data->addBody(-140, $data, $msg,$status);
            } else {

                if (!$promotion_allow) {
                    $this -> showMsg("商品促销活动已关闭!");
                }

                include $this->view->getView();
            }

        }


        //wap二维码界面信息展示
        public function getMycodeSet(){
            //$user_id = Perm::$userId?Perm::$userId:request_int('uuid');
            $user_id = request_int('uuid')?request_int('uuid'):Perm::$userId;
            $bind_avator = '';
            $key = Yf_Registry::get('ucenter_api_key');;
            $url = Yf_Registry::get('ucenter_api_url');
            $app_id = Yf_Registry::get('ucenter_app_id');
            $formvars = [];
            $formvars['user_id'] = $user_id;
            $formvars['u'] = request_int('us');
            $formvars['k'] = request_string('ks');
            $formvars['app_id'] = $app_id;
            $url = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Api_User', 'getWxLogoByUser', 'json');
            $init_rs = get_url_with_encrypt($key, $url, $formvars);
            if (200 == $init_rs['status'] && $init_rs['data']['ret']['bind_avator']) {
                $bind_avator = $init_rs['data']['ret']['bind_avator'];
                $wx_name = $init_rs['data']['ret']['bind_nickname'];
            }
            $user_name = $user_logo ='';
            if((!$bind_avator || !$wx_name) && $user_id){
                $user_info_mdl = new User_InfoModel();
                $result = $user_info_mdl->getInfo($user_id);
                $result = current($result);
                $user_name = $result['user_name'];
                $user_logo = $result['user_logo'];
                unset($result);
            }
            $txt = Web_ConfigModel::value('myqrcode_describe');
            $img = Web_ConfigModel::value('myqrcode_bgimg');
            $data = array(
                'txt'=>$txt,
                'img'=>$img,
                'wxlogo'=>$bind_avator?$bind_avator:$user_logo,
                'wxName'=>$wx_name?$wx_name:$user_name,
            );
            $this->data->addBody(-140, $data, 'success', '200');

        }

        //合成图片
        public function composite_user_pic(){
            //此功能为创精品定制，标准版不吸收，临时改造
            return   $this->data->addBody(-140, array(), 'error', '250');
            set_time_limit(0);
            $user_id = request_int('uuid')?request_int('uuid'):Perm::$userId;
            if(!intval($user_id)){
                return $this->data->addBody(-140, array('composite_pic'=>''), 'success', '200');
            }
            //查询用户有没有生成合成图片
            $user_info_mdl = new User_InfoModel();
            $sql ="select composite_pic from  yf_user_info  where 1 and user_id='{$user_id}'";
            $result = $user_info_mdl->sql->getRow($sql);
            if($result['composite_pic']){
                return $this->data->addBody(-140, array('composite_pic'=>$result['composite_pic']), 'success', '200');
            }
            $base_dir = APP_PATH."/data/upload/share";
            $url = urlencode(Yf_Registry::get('url').'?ctl=Goods_Goods&met=location_register&typ=e&uuid='.$user_id);
            //微信二维码图片地址
            $qr_img = Yf_Registry::get('base_url').'/shop/api/qrcode.php?data='.$url;
            //下载微信二维码图片
            //$qr_img = $base_dir."/".$this->downImg($qrCode);
            //$qr_img = $qrCode;
            //获取微信头像
            /*$bind_avator = '';
            $key = Yf_Registry::get('ucenter_api_key');;
            $url = Yf_Registry::get('ucenter_api_url');
            $app_id = Yf_Registry::get('ucenter_app_id');
            $formvars = [];
            $formvars['user_id'] = $user_id;
            $formvars['u'] = request_int('us');
            $formvars['k'] = request_string('ks');
            $formvars['app_id'] = $app_id;
            $url = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Api_User', 'getWxLogoByUser', 'json');
            $init_rs = get_url_with_encrypt($key, $url, $formvars);
            if (200 == $init_rs['status'] && $init_rs['data']['ret']['bind_avator']) {
                $bind_avator = $init_rs['data']['ret']['bind_avator'];
                $wx_name = $init_rs['data']['ret']['bind_nickname'];
            }
            $user_name = $user_logo ='';
            if((!$bind_avator || !$wx_name) && $user_id){
                $user_info_mdl = new User_InfoModel();
                $result = $user_info_mdl->getInfo($user_id);
                $result = current($result);
                $user_name = $result['user_name'];
                $user_logo = $result['user_logo'];
                unset($result);
            }
            $bind_img = $bind_avator?$bind_avator:$user_logo;*/
            $user_info_mdl = new User_InfoModel();
            $result = $user_info_mdl->getInfo($user_id);
            $result = current($result);

            $user_name = $result['user_name'];
            $bind_img = $result['user_logo'];
            //print_r($bind_img);exit;
            unset($result);
            //下载头像
            //$user_logo_img = $base_dir."/".$this->downImg($bind_img);
            //生成圆形图
            $path = APP_PATH."/data/upload/share/compositeImg/".$user_id."_".time().".png";
            $this->create_yuan_img($bind_img,$path);
            $user_logo_img = $path;
            //展示水印文字$wx_name?$wx_name:$user_name
            $txt ="  我是".($user_name)."\r\n".Web_ConfigModel::value('myqrcode_describe');
            //背景图片
            $myqrcode_bgimg = Web_ConfigModel::value('myqrcode_bgimg');
            //下载背景图片
            //$back_img = $base_dir."/".$this->downImg($myqrcode_bgimg);
            $back_img = $myqrcode_bgimg ;

            //根据类型创建图片
            $img = $this->deal_composite_img($txt,$qr_img,$user_logo_img,$back_img);
            //写入数据库
            //$sql ="update yf_user_info set composite_pic='{$img}' where 1 and user_id='{$user_id}'";
            //$user_info_mdl->sql->exec($sql);
            $this->data->addBody(-140, array('composite_pic'=>$img), 'success', '200');
        }

        //处理合成图片
        public function deal_composite_img($nickname,$erweimaurl,$logourl,$beijing){
            $beijing = imagecreatefromstring(file_get_contents($beijing));
            $logourl = imagecreatefromstring(file_get_contents($logourl));
            $erweimaurl = imagecreatefromstring(file_get_contents($erweimaurl));
            //$erweimaurl_width =  imagesx($erweimaurl);
            $imagesx_erweimaurl  = imagesx($erweimaurl);
            $imagesy_erweimaurl  =imagesy($erweimaurl);

            $imagesx_logourl = imagesx($logourl);
            $imagesy_logourl = imagesy($logourl);
            /*$beijing = $this->createImgBytype($beijing);
            print_r($beijing);exit;
            $logourl = $this->createImgBytype($logourl);
            $erweimaurl = $this->createImgBytype($erweimaurl);*/
            //背景图片宽度
            //$back_img_width = imagesx($beijing);
            $imagesx_beijing = imagesx($beijing);
            $imagesy_beijing = imagesy($beijing);
            //背景图片高度
            $back_img_height = imagesy($beijing);
            $ops_x = intval($imagesx_beijing/2);
            $image_3 = imageCreatetruecolor($imagesx_beijing,$imagesy_beijing);
            $color = imagecolorallocate($image_3, 255, 255, 255);
            imagefill($image_3, 0, 0, $color);
            imageColorTransparent($image_3, $color);
            imagecopyresampled($image_3,$beijing,0,0,0,0,$imagesx_beijing,$imagesy_beijing,$imagesx_beijing,$imagesy_beijing);
            //字体颜色
            //$white = imagecolorallocate($image_3, 111, 255, 255);
            //$rqys = imagecolorallocate($image_3, 255, 255, 255);
            //$black = imagecolorallocate($image_3,120,84,26);
            $black = imagecolorallocate($image_3, 0, 0, 0);
            $font = '/usr/share/fonts/chinese/simsun.ttc';//simhei.ttf写的文字用到的字体。字体最好用系统有得，否则会包charmap的错，这是黑体
            //imagettftext设置生成图片的文本
            $nickname = mb_convert_encoding($nickname,"html-entities","UTF-8");
            imagettftext($image_3,16,0,intval(($imagesx_beijing-$imagesx_erweimaurl)/2-30),($back_img_height-420),$black,$font,$nickname);
            //imagecopymerge($image_3,$logourl, 470,200,0,0,160,160,60);//左，上，右，下，宽度，高度，透明度
            imagecopy($image_3,$logourl, intval(($imagesx_beijing-$imagesx_logourl)/2),200,0,0,$imagesx_logourl,$imagesy_logourl);
            //imagecopymerge($image_3,$logourl, intval(($back_img_width-imagesx($logourl))/2),200,0,0,imagesx($logourl),imagesy($logourl),100);
            //imagecopymerge($image_3,$erweimaurl, 120,100,0,0,imagesx($erweimaurl),imagesy($erweimaurl), 100);
            //imagecopy($image_3,$erweimaurl, intval(($imagesx_beijing-$imagesx_erweimaurl)/2),($back_img_height-330),0,0,$imagesx_erweimaurl,$imagesy_erweimaurl);
            imagecopymerge($image_3,$erweimaurl,intval(($imagesx_beijing-$imagesx_erweimaurl)/2),($back_img_height-330),0,0,$imagesx_erweimaurl,$imagesy_erweimaurl, 90);
            //生成图片
            //imagepng($image_3);//在浏览器上显示
            $img_va = time().".png";
            $path = APP_PATH."/data/upload/share/compositeImg/".$img_va;
            $http = isHttps()?"https://":"http://";
            $img_url =  $http.$_SERVER['HTTP_HOST']."/shop/data/upload/share/compositeImg/".$img_va;;
            imagepng($image_3,$path);
            imagedestroy($image_3);
            return $img_url;

        }

        //生成圆形图
        function create_yuan_img($imgpath,$saveName = '') {
            //$base_dir = APP_PATH."/data/upload/share";
            $src_img = imagecreatefromstring(file_get_contents($imgpath));
            $w = imagesx($src_img);$h = imagesy($src_img);
            $w = $h = min($w, $h);

            $img = imagecreatetruecolor($w, $h);
            //这一句一定要有
            imagesavealpha($img, true);
            //拾取一个完全透明的颜色,最后一个参数127为全透明
            $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
            imagefill($img, 0, 0, $bg);
            $r   = $w / 2; //圆半径
            for ($x = 0; $x < $w; $x++) {
                for ($y = 0; $y < $h; $y++) {
                    $rgbColor = imagecolorat($src_img, $x, $y);
                    if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                        imagesetpixel($img, $x, $y, $rgbColor);
                    }
                }
            }

            //返回资源
            if(!$saveName) return $img;
            //输出图片到文件
            imagepng ($img,$saveName);
            //释放空间
            imagedestroy($src_img);
            imagedestroy($img);
        }

        //下载图片
        public  function downImg($img_url)
        {
            $base_dir = APP_PATH."/data/upload/share";
            $img_url = $img_url;
            $save_dir = date("Ymd");// . '/' . date("d") . '/';
            $file_dir = $base_dir . '/' . $save_dir . '/';
            if (!file_exists($file_dir)) {
                mkdir($file_dir, 0777, TRUE);
            }
            $file_name = uniqid() . rand(10000, 99999);// 文件名
            $mimes=array(
                'image/bmp'=>'bmp',
                'image/gif'=>'gif',
                'image/jpeg'=>'jpg',
                'image/png'=>'png',
                'image/x-icon'=>'ico'
            );
            //获取响应头
            if(($headers=get_headers($img_url, 1))!==false)
            {
                //类型
                $type=$headers['Content-Type'];
                if(isset($mimes[$type]))
                {
                    $extension=$mimes[$type];
                    $file_path = $file_dir.$file_name.".".$extension;
                    $contents=file_get_contents($img_url);
                    if(file_put_contents($file_path , $contents))
                    {
                        return $save_dir.$file_name.".".$extension;
                    }
                }
            }
            return false;
        }


        public function createImgBytype($filename){
            $typeArr=explode(".",$filename);
            //print_r($typeArr);exit;
            switch($typeArr['1']) {
                case "png":
                    $img_r=imagecreatefrompng($filename);
                    break;
                case "jpg":
                    echo 9;
                    $img_r=imagecreatefromstring($filename);
                    print_r($img_r);
                    break;
                case "jpeg":
                    $img_r=imagecreatefromjpeg($filename);
                    break;
                case "gif":
                    $img_r=imagecreatefromgif($filename);
                    break;
            }
            return $img_r;

        }

        public function aging(){
            $area_id =request_int("area_id");
            $common_id=request_int("common_id");
            $Goods_CommonModel = new Goods_CommonModel();
            $goods_common = $Goods_CommonModel->getOne($common_id);

            //商品配送规则
            $DistrictParent = new Base_DistrictModel();
            $parentArea = $DistrictParent->getDistrictParentData($area_id);
            $parentArea = array_column($parentArea,'district_id');
            if(!$parentArea){
                $parentArea[] = $area_id;
            }
            $GoodsTransportModel = new GoodsTransportModel();
            $goods_transport_rule = $GoodsTransportModel->getTransportInfo($parentArea, $goods_common['shop_id']);
            var_dump($goods_transport_rule);die;
            $this->data->addBody(-140, $goods_transport_rule);
        }


        //分销商品分享
        public function goodsShare(){
            $common_id = request_int('common_id');
            $Goods_CommonModel= new Goods_CommonModel();
            $data = $Goods_CommonModel->getOne($common_id);
            if($data){
                $data['share'] = Yf_Registry::get('shop_wap_url') . '/tmpl/product_detail.html?goods_id=' . $data['goods_id'][0]['goods_id'];
                $status = 200;
                $msg = __('success');
            }else{
                $status = 250;
                $msg = __('failure');
            }
            $this->data->addBody(-140, $data, $msg, $status);
        }


        public function getSeckillGoodsList()
        {
            $Seckill_GoodsModel = new Seckill_GoodsModel();
            $Goods_CatModel = new Goods_CatModel();
            $seckill_allow = Web_ConfigModel::value("seckill_allow");
            $data = array();
            $cat_id = request_int('cat_id');
            if($cat_id){
                //查找全部下级分类
                $cat_list1 = $Goods_CatModel->getByWhere(array('cat_parent_id'=>$cat_id));
                $cat_ids1  = array_column($cat_list1,'cat_id');
                $cat_list2 = $Goods_CatModel->getByWhere(array('cat_parent_id:IN'=>$cat_ids1));
                $cat_ids2  = array_column($cat_list2,'cat_id');
                $cat_ids = array_merge($cat_ids1,$cat_ids2);
            }
            if($seckill_allow == 1) {
                $Yf_Page = new Yf_Page();
                $Yf_Page->listRows = 12;
                $rows = $Yf_Page->listRows;
                $offset = request_int('firstRow', 0);
                $page = ceil_r($offset / $rows);

                $miao_sha_name = trim(request_string('seckill_name'));   //活动名称
                $seckill_state = request_int('seckill_state');           //活动状态
                $goods_name = request_string('goods_name');           //商品名称

                $uptime = strtolower(request_string('uptime', 'desc'));

                $price = strtolower(request_string('price', 'desc'));

                $Shop_BaseModel = new Shop_BaseModel();
                $shop_cond['shop_name:LIKE'] = '%' . trim(request_string('shop_name')) . '%';//店铺名称
                $shop_list = $Shop_BaseModel->getByWhere($shop_cond);
                $shop_ids = array_column($shop_list, 'shop_id');
                if ($shop_ids) {
                    $cond_row['shop_id:IN'] = $shop_ids;
                }
                if ($discount_state) {
                    $cond_row['seckill_state'] = $seckill_state;
                }
                if ($xian_shi_name) {
                    $cond_row['seckill_name:LIKE'] = $miao_sha_name . '%';
                }
                if ($goods_name) {
                    $cond_row['goods_name:LIKE'] = $goods_name . '%';
                }
                if($cat_ids){
                    $cond_row['cat_id:IN'] = $cat_ids;
                }

                $order_row = array();

                if ($uptime == 'asc') {
                    $order_row = array('seckill_id' => 'ASC');
                    $uptime = 'desc';
                } else {
                    $order_row = array('seckill_id' => 'DESC');
                    $uptime = 'asc';
                }

                if (request_string('price')) {
                    if ($price == 'asc') {
                        $order_row = array('seckill_price' => 'ASC');
                        $price = 'desc';
                    } else {
                        $order_row = array('seckill_price' => 'DESC');
                        $price = 'asc';
                    }
                }

                // //首页版块置顶操作
                // if (request_string('seckill_goods_id')) {
                //     $seckill_goods_id = request_string('seckill_goods_id');
                //     $ids = explode(',', $seckill_goods_id);
                //     $seckill_goods_cond_row['seckill_goods_id:IN'] = $ids;
                //     $seckill_goods_list = $Seckill_GoodsModel->getSeckillGoodsList($seckill_goods_cond_row);
                //     $cond_row['seckill_goods_id:NOT IN'] = $ids;
                //     $cond_row['seckill_goods_state'] = 1;
                // }
                $cond_row['goods_start_date:<='] = date('Y-m-d');
                $cond_row['goods_end_date:>='] = date('Y-m-d');
                if(request_int('goods_time_slot')){
                    $cond_row['goods_time_slot'] = request_int('goods_time_slot');
                }
                $data = $Seckill_GoodsModel->getSeckillGoodsList($cond_row, $order_row, $page, $rows);
                foreach ($data['items'] as $key => $value) {
                    $data['items'][$key]['sold_bai'] = round(($value['seckill_sold']/$value['seckill_stock_s'])*100);
                    $data['items'][$key]['bai_style'] = "style=width:".$data['items'][$key]['sold_bai']."%";
                }
                if ($seckill_goods_list) {
                    $data['items'] = array_merge($seckill_goods_list['items'], $data['items']);
                }
                $Yf_Page->totalRows = $data['totalsize'];
                $page_nav = $Yf_Page->prompt();
                $msg = 'success';
                $status = 200;
            }else {
                $msg = '秒杀功能已关闭';
                $status = 250;


            }
            //查询所有分类
            $data['cat_list'] = $Goods_CatModel->getByWhere(array('cat_parent_id'=>0));
            $data['cat_list'] = array_merge($data['cat_list']);
            foreach ($data['cat_list'] as $key => $value) {
                if(strlen($value['cat_name'])>12){
                    $data['cat_list'][$key]['cat_name'] = substr($value['cat_name'], 0,12).'...';
                }
            }
            if ('json' == $this->typ) {
                $this->data->addBody(-140, $data, $msg,$status);
            } else {

                if (!$promotion_allow) {
                    $this -> showMsg("商品促销活动已关闭!");
                }

                include $this->view->getView();
            }

        }

//        按字母分组
        function data_letter_sort($list, $field)
        {
            $resault = array();

            foreach( $list as $key => $val )
            {
                // 添加 # 分组，用来 存放 首字母不能 转为 大写英文的 数据
                $resault['#'] = array();
                // 首字母 转 大写英文
                $letter = strtoupper( substr($val[$field], 0, 1) );
                // 是否 大写 英文 字母
                if( !preg_match('/^[A-Z]+$/', $letter) )
                {
                    $letter = '#';
                }
                // 创建 字母 分组
                if( !array_key_exists($letter, $resault) )
                {
                    $resault[$letter] = array();
                }
                // 字母 分组 添加 数据
                Array_push($resault[$letter], $val);
            }
            // 依据 键名 字母 排序，该函数 返回 boolean
            ksort($resault);
            // 将 # 分组 放到 最后
            $arr_last = $resault['#'];
            unset($resault['#']);
            $resault['#'] = $arr_last;
            return $resault;
        }

        public function getPresaleGoodsList()
        {
            $Presale_GoodsModel = new Presale_GoodsModel();
            //$promotion_allow = Web_ConfigModel::value("promotion_allow");
            $data = array();
            // if($promotion_allow == 1) {
                $Yf_Page = new Yf_Page();
                $Yf_Page->listRows = 12;
                $rows = $Yf_Page->listRows;
                $offset = request_int('firstRow', 0);
                $page = ceil_r($offset / $rows);

                $yu_shou_name = trim(request_string('presale_name'));   //活动名称
                $presale_state = request_int('presale_state');           //活动状态
                $goods_name = request_string('goods_name');           //商品名称

                $uptime = strtolower(request_string('uptime', 'desc'));

                $price = strtolower(request_string('price', 'desc'));

                $Shop_BaseModel = new Shop_BaseModel();
                $shop_cond['shop_name:LIKE'] = '%' . trim(request_string('shop_name')) . '%';//店铺名称
                $shop_list = $Shop_BaseModel->getByWhere($shop_cond);
                $shop_ids = array_column($shop_list, 'shop_id');
                if ($shop_ids) {
                    $cond_row['shop_id:IN'] = $shop_ids;
                }
                if ($presale_state) {
                    $cond_row['presale_state'] = $presale_state;
                }
                if ($yu_shou_name) {
                    $cond_row['presale_name:LIKE'] = $yu_shou_name . '%';
                }
                if ($goods_name) {
                    $cond_row['goods_name:LIKE'] = $goods_name . '%';
                }

                $order_row = array();

                if ($uptime == 'asc') {
                    $order_row = array('presale_id' => 'ASC');
                    $uptime = 'desc';
                } else {
                    $order_row = array('presale_id' => 'DESC');
                    $uptime = 'asc';
                }

                if (request_string('price')) {
                    if ($price == 'asc') {
                        $order_row = array('presale_price' => 'ASC');
                        $price = 'desc';
                    } else {
                        $order_row = array('presale_price' => 'DESC');
                        $price = 'asc';
                    }
                }

                //首页版块置顶操作
                if (request_string('presale_goods_id')) {
                    $presale_goods_id = request_string('presale_goods_id');
                    $ids = explode(',', $presale_goods_id);
                    $presale_goods_cond_row['presale_goods_id:IN'] = $ids;
                    $presale_goods_list = $Presale_GoodsModel->getPresaleGoodsList($presale_goods_cond_row);
                    $cond_row['presale_goods_id:NOT IN'] = $ids;
                    $cond_row['presale_goods_state'] = 1;
                }
                $cond_row['goods_start_time:<'] = date('Y-m-d H:i:s');
                $cond_row['goods_end_time:>'] = date('Y-m-d H:i:s');

                $data = $Presale_GoodsModel->getPresaleGoodsList($cond_row, $order_row, $page, $rows);
            
                if ($presale_goods_list) {
                    $data['items'] = array_merge($presale_goods_list['items'], $data['items']);
                }

                foreach($data['items'] as $key => $val)
                {
                    $goods_rows[] = $val['goods_id'];
                }
                $Goods_BaseModel = new Goods_BaseModel();
                foreach($goods_rows as $kk=>$vv)
                {
                    $result[] = $Goods_BaseModel->getOne($vv);
                }
                foreach($data['items'] as $key => $val)
                {
                    if($val['goods_id']==$result[$key]['goods_id'])
                    {
                        $data['items'][$key]['goods_stock'] =$result[$key]['goods_stock'];
                    }
                }


                $Yf_Page->totalRows = $data['totalsize'];
                $page_nav = $Yf_Page->prompt();
                $msg = 'success';
                $status = 200;
            // }else {
            //     $msg = '限时折扣功能已关闭';
            //     $status = 250;


            // }

            if ('json' == $this->typ) {
                $this->data->addBody(-140, $data, $msg,$status);
            } else {

                if (!$promotion_allow) {
                    $this -> showMsg("商品促销活动已关闭!");
                }

                include $this->view->getView();
            }

        }
    }

?>
