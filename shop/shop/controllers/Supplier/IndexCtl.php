<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Supplier_IndexCtl extends Controller
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }
    public function indexs()
    {
        $cond_row = array();
        $cond_row['shop_status'] = Shop_BaseModel::SHOP_STATUS_OPEN;
        if(request_string('or')=='collect')
        {
            $order_row['shop_collect'] = 'DESC';
        }else{
            $order_row['shop_create_time'] = 'DESC';
        }

        if(request_string('district'))
        {
            $Shop_CompanyModel = new Shop_CompanyModel();
            $shop_row['shop_company_address:LIKE'] = '%'.request_string('district').'%';
            $shops = $Shop_CompanyModel->getByWhere($shop_row);
            $shop_ids = array_column($shops,'shop_id');

            $cond_row['shop_id:in'] = $shop_ids;
        }

        if(request_string('keywords'))
        {
            $cond_row['shop_name:LIKE'] = '%'.request_string('keywords').'%';
        }
        //pc分站
        if(isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0){
            $sub_site_id = $_COOKIE['sub_site_id'];
        }

        //wap分站
        if(request_string('ua') === 'wap'){
            $sub_site_id = request_int('sub_site_id');
        }

        //获取分站信息
        if(Web_ConfigModel::value('subsite_is_open') && isset($sub_site_id) && $sub_site_id > 0){
            //获取站点信息
            $Sub_SiteModel = new Sub_SiteModel();
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if($sub_site_district_ids){
                $cond_row['district_id:IN'] = $sub_site_district_ids;
            }

        }
        //用于判断自营是否显示
        $self_shop_show_key = !$sub_site_id ? 'self_shop_show' : 'self_shop_show_'.$sub_site_id;
        if(Web_ConfigModel::value($self_shop_show_key) == 1){
            if(request_int('plat'))
            {
                $cond_row['shop_self_support'] ='true';
            }
        }else{
            $cond_row['shop_self_support'] ='false';
        }

        $cond_row['shop_type'] = 1;   //卖家店铺

        $Yf_Page           = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows              = $Yf_Page->listRows;
        $offset            = request_int('firstRow', 0);
        $page              = ceil_r($offset / $rows);
        if(request_string('typ') == 'json')
        {
            $page = request_int('page', 1);
        }
        $data = $this->shopBaseModel->getBaseList($cond_row,$order_row,$page,$rows);
        //wap端只展示两条推荐商品
        $rows = $this->typ == 'json'? 3: 4;

        if(!empty($data['items']))
        {
            foreach($data['items'] as $key=>$val)
            {
                //判断PC端店铺是否存在，不存在取默认值
                $data['items'][$key]['shop_logo'] = empty($val['shop_logo']) ?Web_ConfigModel::value("photo_shop_head_logo") : $val['shop_logo'];
                //判断wap端店铺logo是否存在，不存在取默认值
                $data['items'][$key]['wap_shop_logo'] = empty($val['wap_shop_logo']) ? Web_ConfigModel::value("photo_shop_head_logo_wap") : $val['wap_shop_logo'];
                //获取店铺评分信息
                $data['items'][$key]['shop_detail']    = $this->shopBaseModel->getShopDetail($val['shop_id']);

                //获取店铺推荐商品
                $goods_recommended = $this->goodsCommonModel->getGoodsList(array("shop_id" => $val['shop_id'],"common_is_recommend" => 2,"common_state" => 1,'common_verify' =>1),array('common_salenum'=>'DESC'), 1,$rows);
                //如果店铺没有推荐商品则获取商品销量的前4件有效商品
                if($goods_recommended)
                {
                    $goods_recommended = $this->goodsCommonModel->getGoodsList(array("shop_id" => $val['shop_id'],"common_state" => 1,'common_verify' =>1),array('common_sell_time'=>'DESC'), 1,$rows);
                }
                $data['items'][$key]['goods_recommended'] = $goods_recommended;


                $condi_rec_goods['shop_id'] 			= $val['shop_id'];
                $condi_rec_goods['common_state'] 		= Goods_CommonModel::GOODS_STATE_NORMAL;
                $condi_rec_goods['common_verify'] 		= Goods_CommonModel::GOODS_VERIFY_ALLOW;
                $goods_common_list = $this->goodsCommonModel->getbywhere( $condi_rec_goods );
                //店铺商品数量
                $data['items'][$key]['goods_num'] = count($goods_common_list);
            }
        }

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav           = $Yf_Page->prompt();

        $district = new Base_DistrictModel();
        $district_data = $district->getDistrictTree(0);

        if ('json' == $this->typ)
        {
            $this->data->addBody(-140, $data);

        }
        else
        {
            $now_page = 'shop_page';
            include $this->view->getView();
        }
    }
    
    public function index()
    {
        if ('json' == $this->typ) {
            $goods_CommonModel = new Goods_CommonModel();
            $mbTplLayoutModel = new Mb_TplLayoutModel();
            $layout_list = $mbTplLayoutModel->getByWhere(['mb_tpl_layout_enable' => Mb_TplLayoutModel::USABLE], ['mb_tpl_layout_order' => 'ASC']);
            $data = [];
            $data[] = [];
            if (!empty($layout_list)) {
                foreach ($layout_list as $mb_tpl_layout_id => $layout_data_val) {
                    if ($layout_data_val['mb_tpl_layout_type'] == 'adv_list') {
                        $adv_list = $layout_data_val;
                    }
                    if ($layout_data_val['mb_tpl_layout_type'] == 'home1') {
                        $hom1 = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        $hom1['title'] = $layout_data_val['mb_tpl_layout_title'];
                        $hom1['image'] = $mb_tpl_layout_data['image'];
                        $hom1['type'] = $mb_tpl_layout_data['image_type'];
                        $hom1['data'] = $mb_tpl_layout_data['image_data'];
                        $data[$mb_tpl_layout_id + 1]['home1'] = $hom1;
                    }
                    if ($layout_data_val['mb_tpl_layout_type'] == 'home2' || $layout_data_val['mb_tpl_layout_type'] == 'home4') {
                        $home2_4 = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        $home2_4['title'] = $layout_data_val['mb_tpl_layout_title'];
                        $home2_4['rectangle1_image'] = $mb_tpl_layout_data['rectangle1']['image'];
                        $home2_4['rectangle1_type'] = $mb_tpl_layout_data['rectangle1']['image_type'];
                        $home2_4['rectangle1_data'] = $mb_tpl_layout_data['rectangle1']['image_data'];
                        $home2_4['rectangle2_image'] = $mb_tpl_layout_data['rectangle2']['image'];
                        $home2_4['rectangle2_type'] = $mb_tpl_layout_data['rectangle2']['image_type'];
                        $home2_4['rectangle2_data'] = $mb_tpl_layout_data['rectangle2']['image_data'];
                        $home2_4['square_image'] = $mb_tpl_layout_data['square']['image'];
                        $home2_4['square_type'] = $mb_tpl_layout_data['square']['image_type'];
                        $home2_4['square_data'] = $mb_tpl_layout_data['square']['image_data'];
                        $data[$mb_tpl_layout_id + 1][$layout_data_val['mb_tpl_layout_type']] = $home2_4;
                    }
                    if ($layout_data_val['mb_tpl_layout_type'] == 'home3') {
                        $home3 = [];
                        $item = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        foreach ($mb_tpl_layout_data as $key => $layout_data) {
                            $item[$key]['image'] = $layout_data['image'];
                            $item[$key]['type'] = $layout_data['image_type'];
                            $item[$key]['data'] = $layout_data['image_data'];
                        }
                        $home3['item'] = $item;
                        $home3['title'] = $layout_data_val['mb_tpl_layout_title'];
                        $data[$mb_tpl_layout_id + 1]['home3'] = $home3;
                    }
                    if ($layout_data_val['mb_tpl_layout_type'] == 'goods') {
                        $goods = [];
                        $item = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        $common_list = $goods_CommonModel->getByWhere(['common_id:IN' => $mb_tpl_layout_data]);
                        if ($common_list) {
                            foreach ($common_list as $common_id => $common_data) {
                                $goods_id = pos($common_data['goods_id']);
                                if (is_array($goods_id)) {
                                    $goods_id = pos($goods_id);
                                }
                                $item[$common_id]['goods_id'] = $goods_id;
                                $item[$common_id]['goods_name'] = $common_data['common_name'];
                                $item[$common_id]['goods_promotion_price'] = $common_data['common_price'];
                                $item[$common_id]['goods_image'] = sprintf('%s!360x360', $common_data['common_image']);
                            }
                            $goods['item'] = array_values($item);
                            $goods['title'] = $layout_data_val['mb_tpl_layout_title'];
                            $data[$mb_tpl_layout_id + 1]['goods'] = $goods;
                        }
                    }
                }
            }
            //头部滚动条
            $slide_row = $adv_list['mb_tpl_layout_data'];
            foreach ($slide_row as $s_k => $s_v) {
                $item = [];
                $item['image'] = $s_v['image'];
                $item['type'] = $s_v['image_type'];
                $item['data'] = $s_v['image_data'];
//				$item['link']  = $s_v['image_data'];
                $slide_row[] = $item;
            }
            if (!empty($slide_row)) {
                $data[0]['slider_list']['item'] = $slide_row;
            }
            $data = array_values($data);
            $this->data->addBody(-140, $data);
        } else {
//			$Cache = Yf_Cache::create('default');
//
//			$site_index_key = sprintf('%s|%s|', Yf_Registry::get('server_id'), 'site_index');
//			if (!$Cache->start($site_index_key))
//			{
            $this->initData();
            //团购风暴
            $GroupBuy_BaseModel = new GroupBuy_BaseModel;
            $cond_row = [
                "groupbuy_starttime:<=" => get_date_time(),
                "groupbuy_endtime :>=" => get_date_time(),
                "groupbuy_state" => GroupBuy_BaseModel::NORMAL,
            ];
            $order_row = ["groupbuy_recommend" => "desc"];
            $gb_goods_list = $GroupBuy_BaseModel->getGroupBuyGoodsList($cond_row, $order_row, 1, 15);
            //楼层设置
            $Adv_PageSettingsModel = new Adv_PageSettingsModel();
            $cond_adv_row = ["page_status" => 1, 'sub_site_id' => -1];
            $order_adv_row = ["page_order" => "asc"];
            $adv_list = $Adv_PageSettingsModel->listByWhere($cond_adv_row, $order_adv_row);
            //首页标题关键字
            $subsite_is_open = Web_ConfigModel::value("subsite_is_open");
            if (!empty($_COOKIE['sub_site_id']) && $subsite_is_open == Sub_SiteModel::SUB_SITE_IS_OPEN) {
                //首页标题关键字
                $Sub_Site = new Sub_Site();
                $sub_site_info = $Sub_Site->getSubSite($_COOKIE['sub_site_id']);
                $title = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_title'];//首页名;
                $this->keyword = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_keyword'];//关键字;
                $this->description = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_des'];//描述;
                $this->title = str_replace("{sitename}", $this->web['web_name'], $title);
                $this->keyword = str_replace("{sitename}", $this->web['web_name'], $this->keyword);
                $this->description = str_replace("{sitename}", $this->web['web_name'], $this->description);
            } else {
                $title = Web_ConfigModel::value("title");//首页名;
                $this->keyword = Web_ConfigModel::value("keyword");//关键字;
                $this->description = Web_ConfigModel::value("description");//描述;
                $this->title = str_replace("{sitename}", $this->web['web_name'], $title);
                $this->keyword = str_replace("{sitename}", $this->web['web_name'], $this->keyword);
                $this->description = str_replace("{sitename}", $this->web['web_name'], $this->description);
            }
            include $this->view->getView();
//				$Cache->_id = $site_index_key;
//				$Cache->end($site_index_key);
//			}
        }
    }
    
    public function main()
    {
        //include $this->view->getView();
    }
    
    public function getUserLoginInfo()
    {
        $data = [];
        if (Perm::checkUserPerm()) {
            $user_id = Perm::$userId;
            $userInfoModel = new User_InfoModel();
            $this->userInfo = $userInfoModel->getOne($user_id);
            fb($this->userInfo);
        }
        include $this->view->getView();
        if (Perm::checkUserPerm()) {
            $data[3] = true;
        } else {
            $data[3] = false;
        }
        $this->data->addBody(-140, $data);
    }
    
    public function getSearchWords()
    {
        $search_words = explode(',', Web_ConfigModel::value('search_words'));
        $data['hot_info']["name"] = $search_words[0];
        $data['hot_info']["value"] = $search_words[0];
        $this->data->addBody(-140, $data);
    }
    
    public function getSearchKeyList()
    {
        $search_words = explode(',', Web_ConfigModel::value('search_words'));
        $data['list'] = $search_words;
        $data['his_list'] = [$search_words[1]];
        $this->data->addBody(-140, $data);
    }
    
    public function test()
    {
        include $this->view->getView();
    }
    
    //获取侧边栏的信息
    public function toolbar()
    {
        $this->initData();
        //$this->user_info = $this->userInfo();
        //公告
        $this->articleBaseModel = new Article_BaseModel();
        $Announcement_row['article_type'] = 1;
        $Announcement_row['article_status'] = 1;
        $Announcement = $this->articleBaseModel->getBaseAllList($Announcement_row, ['article_add_time' => 'DESC'], 1, 20);
        //用户登录情况下获取信息
        if (Perm::checkUserPerm()) {
            $user_id = Perm::$userId;
            $cord_row = [];
            $cond_row = ['user_id' => $user_id];
            $userResourceModel = new User_ResourceModel();
            $user_list = $userResourceModel->getUserResource($cond_row);
        }
        //用户登录情况下获取购物车信息
        if (Perm::checkUserPerm()) {
            $user_id = Perm::$userId;
            $cord_row = [];
            $order_row = [];
            $cond_row = ['user_id' => $user_id];
            $CartModel = new CartModel();
            $cart_list = $CartModel->getCardList($cond_row, $order_row);
        }
        //用户登录情况下获取关注店铺信息
        if (Perm::checkUserPerm()) {
            $user_id = Perm::$userId;
            $userFavoritesShopModel = new User_FavoritesShopModel();
            $goodsCommonModel = new Goods_CommonModel();
            $shop_list = $userFavoritesShopModel->getFavoritesShopDetail($user_id, 1, 4);
            if ($shop_list['items']) {
                foreach ($shop_list['items'] as $key => $val) {
                    $cond_row = [];
                    $cond_row['shop_id'] = $val['shop_id'];
                    $goods = $goodsCommonModel->getGoodsList($cond_row, [], 1, 2);
                    if ($goods) {
                        $shop_list['items'][$key]['detail'] = $goods;
                    }
                }
            }
        }
        //用户登录情况下获取收藏商品信息
        if (Perm::checkUserPerm()) {
            $user_id = Perm::$userId;
            $userFavoritesGoodsModel = new User_FavoritesGoodsModel();
            $favorites_row['user_id'] = $user_id;
            $goods_list = $userFavoritesGoodsModel->getFavoritesGoodsDetail($favorites_row, ['favorites_goods_time' => 'DESC'], 1, 20);
        }
        //用户登录情况下获取足迹信息
        if (Perm::checkUserPerm()) {
            $user_id = Perm::$userId;
            $cord_row = [];
            $order_row = [];
            $cond_row = ['user_id' => $user_id];
            $userFootprintModel = new User_FootprintModel();
            $footprint_list = $userFootprintModel->getFootprintList($cond_row, ['footprint_time' => 'DESC'], 1, 10, '');
            if ($footprint_list['items']) {
                $goods_id_row = [];
                $goods_id_row['common_id:in'] = array_column($footprint_list['items'], 'common_id');
                $goods_id_row = array_unique($goods_id_row);
                $goodsCommonModel = new Goods_CommonModel();
                $goods = $goodsCommonModel->getGoodsList($goods_id_row, [], 1, 10, [], false);
                $goods_id = array_column($goods['items'], 'common_id');
                //以common_id为下表
                $commonAll = [];
                foreach ($goods['items'] as $k => $v) {
                    $commonAll[$v['common_id']] = $v;
                }
                foreach ($footprint_list['items'] as $key => $val) {
                    if (in_array($val['common_id'], $goods_id)) {
                        $footprint_list['items'][$key]['detail'] = $commonAll[$val['common_id']];
                    }
                }
            }
        }
        include $this->view->getView();
    }
    
    /**
     *
     * 取出地区（一级） 店铺保障
     */
    public function getSearchAdv()
    {
        $data = [];
        $area_list = [];
        $contract_list = [];
        $baseDistrictModel = new Base_DistrictModel();
        $shopContractTypeModel = new Shop_ContractTypeModel();
        $district_list = $baseDistrictModel->getDistrictTree(0, false);
        $contract_type_list = $shopContractTypeModel->getByWhere(['contract_type_state' => Shop_ContractTypeModel::CONTRACT_OPEN, 'contract_type_name:<>' => '']);
        $district_list = pos($district_list);
        foreach ($district_list as $key => $district_data) {
            $area_list[$key]['area_id'] = $district_data['district_id'];
            $area_list[$key]['area_name'] = $district_data['district_name'];
        }
        $contract_type_list = array_values($contract_type_list);
        foreach ($contract_type_list as $key => $type_data) {
            $contract_list[$key]['id'] = $type_data['contract_type_id'];
            $contract_list[$key]['name'] = $type_data['contract_type_name'];
        }
        $data['area_list'] = $area_list;
        $data['contract_list'] = $contract_list;
        $this->data->addBody(-140, $data);
    }
}

?>