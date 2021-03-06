<?php

/**
 * @author     charles
 */
class ShopCtl extends Controller
{

    public $shopBaseModel = null;
    public $shopGoodCatModel = null;
    public $shopNavModel = null;
    public $goodsCommonModel = null;
    public $shopDecorationModel = null;

    // public $shopDecorationBlockModel = null;


    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

         // 此处植入判断是否登记店铺独立域名
        $storeDomain = Yf_Registry::get('storeDomain');
        if( !empty($storeDomain) && $storeDomain['store_domain_status'])
        {
            $shop_id = array_keys($storeDomain['store_domain_store'],$_SERVER['HTTP_HOST']);
            //var_dump($shop_id);
            $shop_id = current($shop_id);
            if($shop_id){
                $this->storeId = $shop_id;
            } else {
                $this->storeId = request_int('id');
            }
        } else {
            $this->storeId = request_int('id');
        }
        
        $this->shopBaseModel = new Shop_BaseModel();
        $this->shopGoodCatModel = new Shop_GoodCatModel();
        $this->shopNavModel = new Shop_NavModel();
        $this->goodsCommonModel = new Goods_CommonModel();
        $this->shopDecorationModel = new Shop_DecorationModel();
        // $this->shopDecorationBlockModel = new Shop_DecorationBlockModel();
        //调用这个方法查询出当下店铺是否开启自定义店铺，如果开启自定义店铺只能用店铺默认的模板，如果不是自定义店铺则需要分配那个模板
        $this->setTemp();
        $this->initData();
    }

    public function setTemp()
    {
        $shop_id = $this->storeId;

        if ($shop_id) {
            //根据店铺id查询出是否开启自定义店铺
            $renovation_list = $this->shopBaseModel->getOne($shop_id);
            if (!empty($renovation_list['is_renovation'])) {

                //店铺装修
                $this->view->setMet(null, "default");
            } else {
                if ($renovation_list || $_GET['from'] == 'plat') {
                    //分配模板
                    $shop_template = $renovation_list['shop_template'];
                    if($_GET['from'] == 'plat'){
                        $shop_template = 'default';
                    }
                    $this->view->setMet(null, $shop_template);
                } else {
                    $this->view->setMet('404');
                }
            }
        } else {
            $this->view->setMet('404');

        }
    }

    public function index()
    {
        $shop_id = $this->storeId;
        if ($shop_id) {
            $this->shopCustomServiceModel = new Shop_CustomServiceModel;

            $cond_row['shop_id'] = $shop_id;

            $service = $this->shopCustomServiceModel->getServiceList($cond_row);
            if ($service['items']) {
                foreach ($service['items'] as $key => $val) {
                    //QQ
                    if ($val['tool'] == 1) {
                        $service[$key]["tool"] = "<a target='_blank' href='http://wpa.qq.com/msgrd?v=3&uin=" . $val['number'] . "&site=qq&menu=yes'><img border='0' src='http://wpa.qq.com/pa?p=2:123456789:41 &amp;r=0.22914223582483828' alt='点击这里'></a>";
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
        $GroupBuy_BaseModel = new GroupBuy_BaseModel();
        $data_hot_groupbuy = $GroupBuy_BaseModel->getGroupBuyGoodsList(array("shop_id" => $shop_id), array('groupbuy_buy_quantity' => 'desc'), 0, 5);

        if (!empty($data_hot_groupbuy['items'])) {
            //排除已经结束和审核中的团购商品
            foreach ($data_hot_groupbuy['items'] as $k => $v) {
                if (!in_array($data_hot_groupbuy['items'][$k]['groupbuy_state'], array(0, 2))) {
                    unset($data_hot_groupbuy['items'][$k]);
                }
            }
            $hot_groupbuy_data = $data_hot_groupbuy['items'];
        }

        //店铺信息
        $shop_base = $this->shopBaseModel->getOne($shop_id);
        $shop_type = $shop_base['shop_type']; //店铺类型: 1-卖家店铺; 2:供应商店铺
        $ctl = 'Goods_Goods';
        if ($shop_type == 2) {
            $ctl = 'Supplier_Goods';
        }

        //2.评分信息
        $shop_detail = $this->shopBaseModel->getShopDetail($shop_id);
        $shop_scores_num = ($shop_detail['shop_desc_scores'] + $shop_detail['shop_service_scores'] + $shop_detail['shop_send_scores']) / 3;
        $shop_scores_count = sprintf("%.2f", $shop_scores_num);
        $shop_scores_percentage = $shop_scores_count * 20;
        if ($shop_base['shop_self_support'] == 'false') {
            $shop_all_base = $this->shopBaseModel->getbaseCompanyList($shop_id);
        }

        //判断是否显示自营店铺
        if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
            $sub_site_id = $_COOKIE['sub_site_id'];
        }

        $self_shop_show_key = !$sub_site_id ? 'self_shop_show' : 'self_shop_show_' . $sub_site_id;
        $check_shop_show = $shop_base['shop_self_support'] == 'true' && !Web_ConfigModel::value($self_shop_show_key) ? false : true;

        if (!empty($shop_base) && $shop_base['shop_status'] == 3 && $check_shop_show) {
            //店铺幻灯和幻灯对应的连接
            $shop_slide = explode(",", $shop_base['shop_slide']);
            $shop_slide_url = explode(",", $shop_base['shop_slideurl']);

            //用来判断是不是开启了店铺装潢
            // $renovation_list = $this->shopRenovationModel->getOne($shop_id);
            //查询数据的条件
            $nav_cond_row = array(
                "shop_id" => $shop_id,
                "status" => 1
            );
            $nav_order_row = array("displayorder" => "asc");
            //店铺导航
            $shop_nav = $this->shopNavModel->listByWhere($nav_cond_row, $nav_order_row);
            if (($shop_base['is_renovation'] && $shop_base['is_only_renovation'] == "0") || !$shop_base['is_renovation']) {
                //店铺分类
                $cat_row['shop_id'] = $shop_id;
                $shop_cat = $this->shopGoodCatModel->getGoodCatList($cat_row,array('shop_goods_cat_displayorder' => 'ASC'));

                //店铺下面的产品 新品 推荐 热销排行 收藏排行
                $goods_new_list = $this->goodsCommonModel->getGoodsList(array(
                    "shop_id" => $shop_id,
                    "common_state" => 1,
                    'common_verify' => 1
                ), array("common_add_time" => "desc"), 1, 12);
                $goods_recom_list = $this->goodsCommonModel->getGoodsList(array(
                    "shop_id" => $shop_id,
                    "common_is_recommend" => 2,
                    "common_state" => 1,
                    'common_verify' => 1
                ), array(), 1, 12);

                //ajax 读取
                $goods_selling_list = $this->goodsCommonModel->getGoodsList(array(
                    "shop_id" => $shop_id,
                    "common_state" => 1,
                    'common_verify' => 1
                ), array("common_salenum" => "desc"), 1, 5);


                $goods_collec_list = $this->goodsCommonModel->getGoodsList(array(
                    "shop_id" => $shop_id,
                    "common_state" => 1,
                    'common_verify' => 1
                ), array("common_collect" => "desc"), 1, 5);
            }

            if ($shop_base['is_renovation']) {

                //根据店铺id，查询出装修编号
                $cat_row['shop_id'] = $shop_id;
                $decoration_row = $this->shopDecorationModel->getOneByWhere($cat_row);

                //店铺装潢
                $decoration_detail = $this->shopDecorationModel->outputStoreDecoration($decoration_row['decoration_id'], $shop_id);
            }
            $title = Web_ConfigModel::value("shop_title");//首页名;
            $this->keyword = Web_ConfigModel::value("shop_keyword");//关键字;
            $this->description = Web_ConfigModel::value("shop_description");//描述;
            $this->title = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $title);
            $this->title = str_replace("{shopname}", $shop_base['shop_name'], $this->title);
            $this->keyword = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $this->keyword);
            $this->keyword = str_replace("{shopname}", $shop_base['shop_name'], $this->keyword);
            $this->description = str_replace("{sitename}", Web_ConfigModel::value("site_name"), $this->description);
            $this->description = str_replace("{shopname}", $shop_base['shop_name'], $this->description);
        } else {
            $this->view->setMet('404');
        }


        //传递数据
        if ('json' == $this->typ) {
            $data['shop_base'] = empty($shop_base) ? array() : $shop_base;
            $data['shop_nav'] = empty($shop_nav) ? array() : $shop_nav;
            $data['shop_cat'] = empty($shop_cat) ? array() : $shop_cat;
            $data['goods_new_list'] = empty($goods_new_list) ? array() : $goods_new_list;
            $data['goods_recom_list'] = empty($goods_recom_list) ? array() : $goods_recom_list;
            $data['goods_selling_list'] = empty($goods_selling_list) ? array() : $goods_selling_list;
            $data['goods_collec_list'] = empty($goods_collec_list) ? array() : $goods_collec_list;
            $this->data->addBody(-140, $data);

        } else {
            include $this->view->getView();
        }
    }

    /**
     * 收藏店铺
     *
     * @author     Zhuyt
     */
    public function addCollectShop()
    {
        $shop_id = request_int('shop_id');
        $data = array();
        $User_FavoritesShopModel = new User_FavoritesShopModel();
        //开启事物
        $User_FavoritesShopModel->sql->startTransactionDb();
        $data = array();
        $data['msg'] = '';

        if (Perm::checkUserPerm()) {
            $user_id = Perm::$userId;
            //用户登录情况下,插入用户收藏商品表
            $add_row = array();
            $add_row['user_id'] = $user_id;
            $add_row['shop_id'] = $shop_id;

            $res = $User_FavoritesShopModel->getByWhere($add_row);

            if ($res) {
                $flag = false;
                $data['msg'] = __("您已收藏过该店铺！");

            } else {
                $Shop_BaseModel = new Shop_BaseModel();
                $shop_base = $Shop_BaseModel->getOne($shop_id);

                $add_row['shop_name'] = $shop_base['shop_name'];
                $add_row['shop_logo'] = $shop_base['shop_logo'];
                $add_row['favorites_shop_time'] = get_date_time();


                $User_FavoritesShopModel->addShop($add_row);

                //店铺详情中收藏数量增加
                $edit_row = array();
                $edit_row['shop_collect'] = '1';
                $flag = $Shop_BaseModel->editBaseCollectNum($shop_id, $edit_row, true);
                fb($flag);
                fb($shop_id);
            }

        } else {
            $flag = false;
            $data['msg'] = '请先登录';
        }

        if ($flag && $User_FavoritesShopModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
            $data['msg'] = $data['msg'] ? $data['msg'] : __("收藏成功！");

            //店铺收藏成功添加数据到统计中心
            $analytics_data = array(
                'shop_id' => $shop_id,
                'date' => date('Y-m-d'),
            );
            Yf_Plugin_Manager::getInstance()->trigger('analyticsShopCollect', $analytics_data);
            /******************************************************/
        } else {
            $User_FavoritesShopModel->sql->rollBackDb();
            $m = $User_FavoritesShopModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
            $data['msg'] = $data['msg'] ? $data['msg'] : __("收藏失败！");
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function goodsList()
    {
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 20;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $wap_pagesize = request_int('pagesize');
        $wap_curpage = request_int('curpage');

        if (!empty($wap_pagesize)) {
            $rows = $wap_pagesize;
        }

        if (!empty($wap_curpage)) {
            $page = $wap_curpage;
        }

        $shop_id = $this->storeId;
        $sort = request_string('sort');
        if ($shop_id) {
            $this->shopCustomServiceModel = new Shop_CustomServiceModel;

            $cond_row['shop_id'] = $shop_id;

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
        //店铺信息
        $shop_base = $this->shopBaseModel->getOne($shop_id);
        $shop_type = $shop_base['shop_type']; //店铺类型: 1-卖家店铺; 2:供应商店铺
        $ctl = 'Goods_Goods';
        if ($shop_type == 2) {
            $ctl = 'Supplier_Goods';
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
        //2.评分信息
        $shop_detail = $this->shopBaseModel->getShopDetail($shop_id);
        $shop_scores_num = ($shop_detail['shop_desc_scores'] + $shop_detail['shop_service_scores'] + $shop_detail['shop_send_scores']) / 3;
        $shop_scores_count = sprintf("%.2f", $shop_scores_num);
        $shop_scores_percentage = $shop_scores_count * 20;

        if ($shop_base['shop_self_support'] == 'false') {
            $shop_all_base = $this->shopBaseModel->getbaseCompanyList($shop_id);
        }

        if (!empty($shop_base) && $shop_base['shop_status'] == 3) {
            //店铺幻灯和幻灯对应的连接
            $shop_slide = explode(",", $shop_base['shop_slide']);
            $shop_slide_url = explode(",", $shop_base['shop_slideurl']);
            //店铺导航
            $nav_cond_row = array(
                "shop_id" => $shop_id,
                "status" => 1
            );
            $nav_order_row = array("displayorder" => "asc");

            $shop_nav = $this->shopNavModel->listByWhere($nav_cond_row, $nav_order_row);

            if ($sort == 'desc') {
                $new_sort = 'asc';
            } else {
                $new_sort = 'desc';
            }


            $order_row = array();
            $cond_row = array();
            $search = request_string('search');
            $order = request_string('order');
            $shop_cat_id = request_int('shop_cat_id');
            $price_from = request_float('price_from');
            $price_to = request_float('price_to');
            $cond_row['shop_id'] = $shop_id;

            if (request_int('common_is_xian')) {
                $cond_row['common_is_xian'] = 1;
            }
            $Goods_CommonModel = new Goods_CommonModel();

            if ($search) {
                $cond_row['common_name:like'] = '%' . $search . '%';
            }
            if ($shop_cat_id) {
                //查询是否是一级店铺分类
                $shop_goods_cat_model = new Shop_GoodCatModel();
                $shop_goods_cat = $shop_goods_cat_model->getByWhere(array('parent_id'=> 0, 'shop_goods_cat_id'=>$shop_cat_id));
                //如果是一级店铺分类查询出所有二级分类
                if ($shop_goods_cat){
                    $shop_goods_cat_ids = $shop_goods_cat_model->getByWhere(array('parent_id'=> $shop_cat_id));
                    $shop_goods_cat_ids = array_column($shop_goods_cat_ids, 'shop_goods_cat_id');
                }else{
                    $cond_row['shop_cat_id:like'] = '%' . ',' . $shop_cat_id . ',' . '%';
                }
                $cond_row['shop_cat_id:like'] = '%' . ',' . $shop_cat_id . ',' . '%';
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



            if ($price_from) {
                $cond_row['common_price:>='] = $price_from;
            }

            if ($price_to) {
                $cond_row['common_price:<='] = $price_to;
            }
            $virtual = request_int('virtual');
            if ($virtual == 1) {
                $cond_row['common_is_virtual'] = Goods_CommonModel::GOODS_VIRTUAL;
            }
            if ($order) {
                $order_row = array($order => $sort);
            }
            //商品品牌
            $brand_id = request_row('brand_id');
            if (!is_array($brand_id)) {
                $brand_id = explode(',', $brand_id);
            }
            if ($brand_id) {
                $cond_row['brand_id:in'] = $brand_id;
            }
            $cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
            $cond_row['common_verify'] = 1;
            if (request_string('type_wxapp') == 'wxapp' && request_string('type_wxapp')) {
                $cond_row['common_is_tuan'] = 0;
            }
            if ($shop_goods_cat){
                $datas = $Goods_CommonModel->getGoodsListByShopIds($shop_goods_cat_ids, $cond_row, $order_row, $page, $rows);
            }else{
                $datas = $Goods_CommonModel->getGoodsList($cond_row, $order_row, $page, $rows);
            }

            $Yf_Page->totalRows = $datas['totalsize'];
            $page_nav = $Yf_Page->prompt();
            $data = $datas['items'];

        } else {
            $this->view->setMet('404');
        }

        if ('json' == $this->typ) {
            $arrayList = [];
            foreach ($datas['items'] as $k => $v) {
                if (!is_array($v['goods_id'])) {
                    $datas['items'][$k]['goods_id'] = [['goods_id' => $v['goods_id'], 'color_id' => 0]];
                }

                //wap版显示，商品上新。
                $check = substr($datas['items'][$k]['common_add_time'], 0, 10);
                if (!$arrayList[$check]) {
                    $datas['items'][$k]['goods_addtime_text'] = $check;
                    $arrayList[$check] = true;
                }

            }

            $datas['goods_brand'] = $goods_brand;
            foreach ($datas['goods_brand'] as $key =>$val){
                $datas['goods_brand'][$key]['key'] = $key;
                $datas['goods_brand'][$key]['checked'] = false;
            }
            $datas['goods_brand_all'] = $goods_brand_all;
            array_pop($goods_brands_all);
            $datas['goods_brands_all'] = array_values($goods_brands_all);
            foreach ($datas['goods_brand_all'] as $key=>$val){
                foreach ($val as $k=>$v){
                    $datas['goods_brand_all'][$key][$k]['key'] = $k;
                    $datas['goods_brand_all'][$key][$k]['checked'] = false;
                }
                
            }
            $datas['brand_info'] = array_values($goods_brand_all);
            $this->data->addBody(-140, $datas);

        } else {
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

    public function getCommonList()
    {
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 20;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $shop_id = $this->storeId;
        $Goods_CommonModel = new Goods_CommonModel();
        $cond_row['shop_id'] = $shop_id;
        $cond_row['common_state'] = 1;
        $cond_row['common_verify'] = 1;
        $data = $Goods_CommonModel->getCommonList($cond_row,array(),$page,$rows);

        $goodsIds = request_string('goodsIds');
        $goodsIds = explode(',', $goodsIds);
        if ($goodsIds) {
            foreach ($data['items'] as $k => $v) {
                if (in_array($v['common_id'], $goodsIds)) {
                    $data['items'][$k]['isChecked'] = 1;
                } else {
                    $data['items'][$k]['isChecked'] = 0;
                }
            }
        }
        return  $this->data->addBody(-140, $data);
    }

    /**
     *
     * 获取店铺信息和推荐商品 wap
     */
    public function getStoreInfo()
    {
        $data = array();
        $store_info = array();
        $shop_id = request_int('shop_id');
        if (!$shop_id) {
            return $this->data->addBody(-140, $data, __('数据有误'), 250);
        }
        //读取店铺详情
        $shop_base = $this->shopBaseModel->getShopDetail($shop_id);


       
        if (!$shop_base) {
            return $this->data->addBody(-140, $data, __('数据有误'), 250);
        }
        $condi_rec_goods['shop_id'] = $shop_id;
        $condi_rec_goods['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;

        $goods_common_list = $this->goodsCommonModel->getbywhere($condi_rec_goods);

        //读取推荐商品
        $condi_rec_goods['common_is_recommend'] = Goods_CommonModel::RECOMMEND_TRUE;
        $rec_goods_list = $this->goodsCommonModel->getGoodsList($condi_rec_goods);


        $Label_BaseModel = new Label_BaseModel();
        $Label_Base = $Label_BaseModel->getByWhere("*");
        $label_name_arr = array_column($Label_Base, "label_name","id");


        $cond_row_x['shop_id'] = $shop_id;
        $cond_row_x['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;

        $order_row_x['common_salenum'] = 'asc';


        $Goods_CommonModel = new Goods_CommonModel();
        $common_salenum_list = $Goods_CommonModel->getGoodsList($cond_row_x, $order_row_x,1,2);


        if ($common_salenum_list['items']) {
            $data['common_salenum_list'] = $common_salenum_list['items'];
        }
        //判断当前店铺是否为用户所收藏
        $condi_u_f = array();
        $condi_u_f['user_id'] = Perm::$userId;
        $condi_u_f['shop_id'] = $shop_id;
        $userFavoritesShopModel = new User_FavoritesShopModel();
        $user_f_base = $userFavoritesShopModel->getByWhere($condi_u_f);
        if (empty($user_f_base)) {
            $u_f_shop = false;
        } else {
            $u_f_shop = true;
        }

        //店铺幻灯片
        $mb_sliders = array();
        if ($shop_base['shop_slide']) {
            $shop_slide = explode(",", $shop_base['shop_slide']);
            $shop_slide_url = explode(",", $shop_base['shop_slideurl']);

            if (!empty($shop_slide)) {
                foreach ($shop_slide as $key => $silde_img) {
                    if ($shop_slide_url[$key] && $silde_img) {
                        $sliders['link'] = $shop_slide_url[$key];
                        $sliders['imgUrl'] = $silde_img;
                        array_push($mb_sliders, $sliders);
                    }
                }
            }
        }
   
        $shop_label_name = array();
        if ($shop_base['label_id']) {
           $label_id_arr = explode(",", $shop_base['label_id']);
           foreach ($label_id_arr as $key => $label_id) {
               $shop_label_name[$label_id] = $label_name_arr[$label_id];
           }
        } 
        $store_info['shop_desc_scores'] = $shop_base['shop_desc_scores'];
        $store_info['shop_label_name'] = $shop_label_name;
        $store_info['goods_count'] = count($goods_common_list);
        $store_info['is_favorate'] = $u_f_shop;
        $store_info['is_own_shop'] = $shop_base['shop_self_support'];
        $store_info['shop_wap_index'] = $shop_base['shop_wap_index'];
        $store_info['mb_sliders'] = $mb_sliders;
        $store_info['mb_title_img'] = $shop_base['shop_wap_banner'];
        $store_info['member_id'] = Perm::$userId;
        $store_info['store_avatar'] = $shop_base['wap_shop_logo']?$shop_base['wap_shop_logo']:Web_ConfigModel::value("photo_shop_head_logo_wap");
        $store_info['wap_store_avatar'] = $shop_base['wap_shop_logo'];
        $store_info['user_name'] = $shop_base['user_name'];
        $store_info['store_collect'] = $shop_base['shop_collect'];
        $store_info['store_credit_text'] = sprintf('描述: %.2f, 服务: %.2f, 物流: %.2f', $shop_base['shop_desc_scores'], $shop_base['com_service_scores'], $shop_base['shop_send_scores']);        //描述: 5.0, 服务: 5.0, 物流: 5.0
        $store_info['shop_id'] = $shop_base['shop_id'];
        $store_info['store_name'] = $shop_base['shop_name'];
        $store_info['user_id'] = $shop_base['user_id'];
        $store_info['store_tel'] = $shop_base['shop_tel'];
        $store_info['is_open_im'] = Yf_Registry::get('im_statu');
        $store_info['shop_logo'] = $shop_base['shop_logo'];
        //获取店铺IM
        if ($shop_base['shop_self_support'] == 'true' && Web_ConfigModel::value('self_shop_im')) {
            $store_info['self_shop_im'] = $this->shopBaseModel->getSelfShopIm($shop_base['user_name'], $shop_base['shop_self_support']);
        }
        $data['rec_goods_list'] = $rec_goods_list['items'];
        $data['rec_goods_list_count'] = count($rec_goods_list['items']);
        $data['store_info'] = $store_info;
        //获取代金券信息
        $voucher_model = new Voucher_TempModel();
        $voucher_list = $voucher_model->getShopVoucher($shop_id);

        $data['voucher_list'] = $voucher_list['items'] ? $voucher_list['items'] : array();
        return $this->data->addBody(-140, $data);

    }

 /**
     *
     * wap 获取店铺满送 限时
     */

    public function getShopPromotion()
    {
        $mansong = array();
        $xianshi = array();
        $kanjia = array();
        $pintuan = array();
        $promotion = array();

        $discountBaseModel = new Discount_BaseModel();
        $manSongBaseModel = new ManSong_BaseModel();
        $bargainBaseModel = new Bargain_BaseModel();
        $pinTuanBaseModel = new PinTuan_Base();
        $shop_id = request_int('shop_id');
        $Discount_GoodsModel = new  Discount_GoodsModel();
        $Discount_Goods = $Discount_GoodsModel->getByWhere("*");
        $discount_goods_id_arr = array_column($Discount_Goods, "goods_id","discount_id");

        $Goods_BaseModel = new Goods_BaseModel();
    

        $discount_goods_image_arr = array();
        foreach ($discount_goods_id_arr as $key => $discount_goods_id) {
            $Goods_Base = $Goods_BaseModel->getOne(array("goods_id"=>$discount_goods_id));

            $discount_goods_image_arr[$key] =  $Goods_Base['goods_image'];

        }

 
       
        //限时
        $discount_list = $discountBaseModel->getDiscountActList(array('discount_state' => Discount_BaseModel::NORMAL, 'shop_id' => $shop_id));
        if ($discount_list['items']) {
            foreach ($discount_list['items'] as $key => $value) {
                $discount_list['items'][$key]['goods_image'] =  $discount_goods_image_arr[$value['discount_id']];

            }
        }
        $xianshi = $discount_list['items'];

        //砍价
        $bargain_list = $bargainBaseModel->getBargainList(array('bargain_status' => Bargain_BaseModel::ISON, 'shop_id' => $shop_id));
        $kanjia = $bargain_list['items'];

        //拼团
        $pintuan_list = $pinTuanBaseModel->getPinTuanList(array('status' => PinTuan_Base::$statusEnabled, 'shop_id' => $shop_id));
        $pintuan = $pintuan_list['items'];
        //满送
        $mansong_list = $manSongBaseModel->getManSongActList(array('mansong_state' => ManSong_BaseModel::NORMAL, 'shop_id' => $shop_id));
        $mansong_list_f = $mansong_list['items'];
        fb($mansong_list_f);

        if ($mansong_list_f) {
            foreach ($mansong_list_f as $maskey => $masval) {
                $mansong[] = $manSongBaseModel->getManSongActItem(array('shop_id' => $shop_id, 'mansong_id' => $masval['mansong_id']));
            }

        } else {
            $mansong = $mansong_list_f;
        }

        //当店铺没有满送活动和限时活动的时候对应字段返回默认值，防止App接收数据崩溃
        $flag_mansong = false;
        if (!$mansong && 'json' == request_string('typ')) {
            $mansong['mansong_id'] = 0;
            $mansong['mansong_name'] = '';
            $mansong['combo_id'] = 0;
            $mansong['mansong_start_time'] = '2017-04-01 10:00:00';
            $mansong['mansong_end_time'] = '2017-04-01 11:00:00';
            $mansong['user_id'] = 0;
            $mansong['shop_id'] = 0;
            $mansong['user_nickname'] = '';
            $mansong['shop_name'] = '';
            $mansong['mansong_state'] = 2;
            $mansong['mansong_remark'] = '';
            $mansong['id'] = 0;
            $mansong['mansong_state_label'] = '已关闭';
            $mansong['rule']['rule_id'] = 0;
            $mansong['rule']['mansong_id'] = 0;
            $mansong['rule']['rule_price'] = 0;
            $mansong['rule']['rule_discount'] = 0;
            $mansong['rule']['goods_name'] = '';
            $mansong['rule']['goods_id'] = 0;
            $mansong['rule']['id'] = 0;
            $mansong['rule']['goods_price'] = 0;
            $mansong['rule']['goods_image'] = '';
            $mansong[] = $mansong;
            $flag_mansong = true;
        }
        $flag_kanjia = false;
        if (!$kanjia && 'json' == request_string('typ')) {
            $kanjia['bargain_id'] =0;
            $kanjia['shop_id'] = 0;
            $kanjia['shop_name'] = '';
            $kanjia ['goods_id'] =0;
            $kanjia['goods_price'] =0;
            $kanjia['bargain_price'] =0;
            $kanjia['bargain_stock'] =0;
            $kanjia['bargain_desc'] ='砍价砍价';
            $kanjia['start_time'] =0;
            $kanjia['end_time'] =0;
            $kanjia['join_num'] =0;
            $kanjia['buy_num'] =0;
            $kanjia['bargain_status'] =0;
            $kanjia['bargain_type'] =0;
            $kanjia['bargain_num_price'] =0;
            $kanjia['create_time'] ='';
            $kanjia['is_del'] =0;
            $kanjia['bargain_stock_count']=0;
            $kanjia['goods_name'] ='';
            $kanjia ['goods_old_price'] ='';
            $kanjia['goods_image'] ='';
            $kanjia ['is_self'] =0;
            $kanjia ['start_date'] ='';
            $kanjia['start'] ='0000-00-00';
            $kanjia['end_date'] ='0000-00-00';
            $kanjia['end'] ='0000-00-00';
            $kanjia['bargain_status_con'] ='进行中';
            $kanjia[] = $kanjia;
            $flag_kanjia = true;
        }

        $flag_xianshi = false;
        if (!$xianshi && 'json' == request_string('typ')) {
            $xianshi['discount_id'] = 0;
            $xianshi['discount_name'] = '';
            $xianshi['discount_title'] = '';
            $xianshi['discount_explain'] = '';
            $xianshi['combo_id'] = 0;
            $xianshi['discount_start_time'] = '2017-04-01 10:00:00';
            $xianshi['discount_end_time'] = '2017-04-01 11:00:00';
            $xianshi['user_id'] = 0;
            $xianshi['shop_id'] = 0;
            $xianshi['user_nick_name'] = '';
            $xianshi['shop_name'] = '';
            $xianshi['discount_lower_limit'] = 0;
            $xianshi['discount_state'] = 0;
            $xianshi['id'] = 0;
            $xianshi['discount_state_label'] = '已关闭';
            $xianshi[] = $xianshi;
            $flag_xianshi = true;
        }

        if ($flag_mansong && $flag_xianshi&&$flag_kanjia) {
            $promotion['count'] = 0;
        } else {
            $promotion['count'] = 1;
        }

        $promotion['mansong'] = $mansong;
        $promotion['xianshi'] = $xianshi;
        $promotion['kanjia'] =$kanjia;
        $promotion['pintuan'] =$pintuan;
        $data['promotion'] = $promotion;

        $this->data->addBody(-140, $data);
    }
    /**
     *
     * wap 获取店铺满送 限时
     */

    public function getShopPromotion2()
    {
        $mansong = array();
        $xianshi = array();
        $promotion = array();

        $discountBaseModel = new Discount_BaseModel();
        $manSongBaseModel = new ManSong_BaseModel();

        $shop_id = request_int('shop_id');

        //限时
        $discount_list = $discountBaseModel->getDiscountActList(array('discount_state' => Discount_BaseModel::NORMAL, 'shop_id' => $shop_id));
        $xianshi = $discount_list['items'];

        //满送
        $mansong_list = $manSongBaseModel->getManSongActList(array('mansong_state' => ManSong_BaseModel::NORMAL, 'shop_id' => $shop_id));
        $mansong_list_f = $mansong_list['items'];
        fb($mansong_list_f);

        if ($mansong_list_f) {
            foreach ($mansong_list_f as $maskey => $masval) {
                $mansong[] = $manSongBaseModel->getManSongActItem(array('shop_id' => $shop_id, 'mansong_id' => $masval['mansong_id']));
            }

        } else {
            $mansong = $mansong_list_f;
        }

        //当店铺没有满送活动和即时活动的时候对应字段返回默认值，防止App接收数据崩溃
        $flag_xianshi = false;
        if (!$mansong && 'json' == request_string('typ')) {
            $mansong['mansong_id'] = 0;
            $mansong['mansong_name'] = '';
            $mansong['combo_id'] = 0;
            $mansong['mansong_start_time'] = '2017-04-01 10:00:00';
            $mansong['mansong_end_time'] = '2017-04-01 11:00:00';
            $mansong['user_id'] = 0;
            $mansong['shop_id'] = 0;
            $mansong['user_nickname'] = '';
            $mansong['shop_name'] = '';
            $mansong['mansong_state'] = 2;
            $mansong['mansong_remark'] = '';
            $mansong['id'] = 0;
            $mansong['mansong_state_label'] = '已关闭';
            $mansong['rule']['rule_id'] = 0;
            $mansong['rule']['mansong_id'] = 0;
            $mansong['rule']['rule_price'] = 0;
            $mansong['rule']['rule_discount'] = 0;
            $mansong['rule']['goods_name'] = '';
            $mansong['rule']['goods_id'] = 0;
            $mansong['rule']['id'] = 0;
            $mansong['rule']['goods_price'] = 0;
            $mansong['rule']['goods_image'] = '';
            $mansong[] = $mansong;
            $flag_mansong = true;
        }

        $flag_xianshi = false;
        if (!$xianshi && 'json' == request_string('typ')) {
            $xianshi['discount_id'] = 0;
            $xianshi['discount_name'] = '';
            $xianshi['discount_title'] = '';
            $xianshi['discount_explain'] = '';
            $xianshi['combo_id'] = 0;
            $xianshi['discount_start_time'] = '2017-04-01 10:00:00';
            $xianshi['discount_end_time'] = '2017-04-01 11:00:00';
            $xianshi['user_id'] = 0;
            $xianshi['shop_id'] = 0;
            $xianshi['user_nick_name'] = '';
            $xianshi['shop_name'] = '';
            $xianshi['discount_lower_limit'] = 0;
            $xianshi['discount_state'] = 0;
            $xianshi['id'] = 0;
            $xianshi['discount_state_label'] = '已关闭';
            $xianshi[] = $xianshi;
            $flag_xianshi = true;
        }

        if ($flag_mansong && $flag_xianshi) {
            $promotion['count'] = 0;
        } else {
            $promotion['count'] = 1;
        }

        $promotion['mansong'] = $mansong;
        $promotion['xianshi'] = $xianshi;


        $data['promotion'] = $promotion;

        $this->data->addBody(-140, $data);
    }

    /**
     * 店铺详细信息
     */
    public function getStoreIntro()
    {
        $data = array();
        $shop_id = request_int('shop_id');

        $shop_base = $this->shopBaseModel->getShopDetail($shop_id);

        //判断当前店铺是否为用户所收藏
        $condi_u_f = array();
        $condi_u_f['user_id'] = Perm::$userId;
        $condi_u_f['shop_id'] = $shop_id;
        $userFavoritesShopModel = new User_FavoritesShopModel();
        $user_f_base = $userFavoritesShopModel->getByWhere($condi_u_f);
        if (empty($user_f_base)) {
            $u_f_shop = false;
        } else {
            $u_f_shop = true;
        }
        $shop_base['is_favorate'] = $u_f_shop;

        $data['store_info'] = $shop_base;
        $this->data->addBody(-140, $data);
    }


    /**
     * 获取分销员分销的商品
     */
    public function directsellerGoodsList()
    {
        $uid = request_int('uid')?:Perm::$userId;
        $sort = request_string('sort');

        $cond_row['directseller_id'] = $uid;
        $Distribution_ShopDirectsellerModel = new Distribution_ShopDirectsellerModel();
        $shops = $Distribution_ShopDirectsellerModel->getByWhere($cond_row);
        $shop_ids = array_column($shops, 'shop_id');

        $cond_good_row['shop_id:in'] = $shop_ids;
        $cond_good_row['common_is_directseller'] = 1;
        if (request_string('keywords')) {
            $cond_good_row['common_name:LIKE'] = '%' . request_string('keywords') . '%'; //商品名称搜索
        }

        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows = $Yf_Page->listRows;

        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $act = request_string('act');
        $actorder = request_string('actorder', 'DESC');

        if ($act !== '') {
            //销量
            if ($act == 'sales') {
                $order_row['common_salenum'] = $actorder;
            }

            //佣金排序
            if ($act == 'price') {
                if (request_string('actorder')) {
                    $order_row['common_price'] = $actorder;
                } else {
                    $order_row['common_price'] = 'ASC';
                }
            }

            //时间排序
            if ($act == 'uptime') {
                $order_row['common_add_time'] = $actorder;
            }

        } else {
            $order_row['common_id'] = 'DESC';
        }

        //获取推广商品
        $data = array();
        $Goods_CommonModel = new Goods_CommonModel();
        $data = $Goods_CommonModel->getCommonList($cond_good_row, $order_row, $page, $rows);
        $data['user_id'] = $uid;

        //获取店铺名称
        $data['shop'] = $Distribution_ShopDirectsellerModel->getOneByWhere(array('directseller_id' => $uid));
        $data['shop_qrcode'] = Yf_Registry::get('shop_wap_url') . "/tmpl/member/directseller_store.html?uid=" . $uid;
        $data['shop_wap_qrcode'] = Yf_Registry::get('base_url') . "/shop/api/qrcode.php?data=" . urlencode(Yf_Registry::get('shop_wap_url') . "/tmpl/member/directseller_store.html?uid=" . $uid);
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();

        if ('json' == $this->typ) {
            $this->data->addBody(-140, $data);

        } else {
            include $this->view->getView();
        }

    }


    public function info()
    {
        $shop_id = $this->storeId;
        $shop_base = $this->shopBaseModel->getShopDetail($shop_id);
        //2.评分信息
        $shop_detail = $this->shopBaseModel->getShopDetail($shop_id);
        $shop_scores_num = ($shop_detail['shop_desc_scores'] + $shop_detail['shop_service_scores'] + $shop_detail['shop_send_scores']) / 3;
        $shop_scores_count = sprintf("%.2f", $shop_scores_num);
        $shop_scores_percentage = $shop_scores_count * 20;

        $nav_cond_row = array(
            "shop_id" => $shop_id,
            "status" => 1
        );
        $nav_order_row = array("displayorder" => "asc");
        //店铺导航
        $shop_nav = $this->shopNavModel->listByWhere($nav_cond_row, $nav_order_row);

        $nav_id = request_int('nav_id');
        $data = $this->shopNavModel->getOne($nav_id);

        if ($shop_base['shop_self_support'] == 'false') {
            $shop_all_base = $this->shopBaseModel->getbaseCompanyList($shop_id);
        }
        if ($shop_id) {
            $service = $this->getService($shop_id);
        }
        //视频显示匹配字符串
        $data['detail'] = str_replace('type="application/x-shockwave-flash"',' ',$data['detail']);
        if ('json' == $this->typ) {
            $this->data->addBody(-140, $data);

        } else {
            include $this->view->getView();
        }
    }

    public function getService($shop_id)
    {
        $this->shopCustomServiceModel = new Shop_CustomServiceModel;
        $cond_row['shop_id'] = $shop_id;
        $service = $this->shopCustomServiceModel->getServiceList($cond_row);
        if ($service['items']) {
            foreach ($service['items'] as $key => $val) {
                $service[$key]["tool"] = $val["tool"] == 2 ? "<a target='_blank' href='http://www.taobao.com/webww/ww.php?ver=3&amp;touid=" . $val['number'] . "&amp;siteid=cntaobao&amp;status=1&amp;charset=utf-8' ><img border='0' src='http://amos.alicdn.com/online.aw?v=2&amp;uid=" . $val['number'] . "&amp;site=cntaobao&s=1&amp;charset=utf-8' alt='点击这里' /></a>" : "<a target='_blank' href='http://wpa.qq.com/msgrd?v=3&uin=" . $val['number'] . "&site=qq&menu=yes'><img border='0' src='http://wpa.qq.com/pa?p=2:" . $val['number'] . ":41 &amp;r=0.22914223582483828' alt='点击这里'></a>";
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

        return $service;
    }

    /**
     * 获取商家的代金券信息
     * @return type
     */
    public function getShopVoucher()
    {
        //获取代金券信息
        $shop_id = request_int('shop_id');
        $data = array();
        if (!$shop_id) {
            return $this->data->addBody(-140, $data, __('数据有误'), 250);
        }
        $Voucher_TempModel = new Voucher_TempModel();
        $voucher_list = $Voucher_TempModel->getShopVoucher($shop_id);
        if ($voucher_list['items']) {
            $data['items'] = $voucher_list['items'];
        }
        //获取我的优惠券

        $data['items'] = $voucher_list['items'] ? $voucher_list['items'] : array();
        return $this->data->addBody(-140, $data);

    }

    public function getCompanyInfo()
    {
        include $this->view->getView();
    }

    public function getMobileYzm()
    {
        $code = request_string('code');
        if (Perm::checkYzm($code)) {
            $status = 200;
            $msg = 'success';
        }else{

            $status = 250;
            $msg = '图形验证码有误';
        }
        $this->data->addBody(-140, array(),$msg,$status);
    }

    public function getCompany()
    {
        $shop_id = request_int('id')? request_int('id'): request_int('shop_id');
        $from = request_string('from');
        $typ = request_string('typ');
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_base = $Shop_BaseModel->getOne($shop_id);

        if($from == 'plat' || $shop_base['shop_self_support'] == "true"){
            $data['shop_company_name'] = Web_ConfigModel::value('shop_company_name');
            $data['legal_person'] = Web_ConfigModel::value('legal_person');
            $data['company_registered_capital'] = Web_ConfigModel::value('company_registered_capital');
            $data['company_address_detail'] = Web_ConfigModel::value('company_address_detail');
            $data['business_sphere'] = Web_ConfigModel::value('business_sphere');
            $data['business_license_location'] = Web_ConfigModel::value('business_license_location');
            $data['business_license_electronic'] = Web_ConfigModel::value('business_license_electronic');
            $data['business_licence_start'] = Web_ConfigModel::value('business_licence_start');
            $data['business_licence_end'] = Web_ConfigModel::value('business_licence_end');
            $data['business_id'] = Web_ConfigModel::value('business_id');
            $data['shop_company_address'] = "";
        }else{
            $Shop_CompanyModel = new Shop_CompanyModel();
            $data = $Shop_CompanyModel->getOne($shop_id);
        }

        $image =  $this->_addWaterMark($data['business_license_electronic'],"image/waterimage.png");
        $data['business_license_electronic'] = Yf_Registry::get('shop_api_url').$image? Yf_Registry::get('shop_api_url').$image:$data['business_license_electronic'];

        if($typ == 'json'){
            return $this->data->addBody(-140, $data);
        }else{
           include $this->view->getView();
        }
    }

    public function _addWaterMark($picPath, $logoPath)
    {

        $type = getimagesize($picPath);

        //创建图片的实例
        $im = imagecreatefromstring(file_get_contents($picPath));
        // $im = imagecreatefrompng(($picPath));

        //获取水印源
        $watermark = imagecreatefromstring(file_get_contents($logoPath));
        // $watermark = imagecreatefrompng($logoPath);

        //获取图、水印 宽高类型
        list($bgWidth, $bgHight, $bgType) = getimagesize($picPath);
        list($logoWidth, $logoHight, $logoType) = getimagesize($logoPath);

        //定义平铺数据
        $xLength = $bgWidth - 10; //x轴总长度
        $yLength = $bgHight - 10; //y轴总长度

        //创建透明画布 伪白色
        $opacity = 15;
        $w       = imagesx($watermark);
        $h       = imagesy($watermark);
        $cut     = imagecreatetruecolor($w, $h);
        $white   = imagecolorallocatealpha($cut, 255, 255, 255, 0);
        imagefill($cut, 0, 0, $white);

        //整合水印
        imagecopy($cut, $watermark, 0, 0, 0, 0, $w, $h);

        //循环平铺水印
        for ($x = 0; $x < $xLength; $x++) {
            for ($y = 0; $y < $yLength; $y++) {
                imagecopymerge($im, $cut, $x, $y, 0, 0, $logoWidth, $logoHight, $opacity);
                $y += $logoHight;
            }
            $x += $logoWidth;
        }

        list($dst_type) = getimagesize($im);


        $file = "shop/data/upload/media/plantform/" . time();

        switch($type[2]){
          case 1://GIF
            $file = $file . ".gif";
            imagegif($im, $file);
            break;
          case 2://JPG
            $file = $file . ".jpg";
            imagejpeg($im, $file);
            break;
          case 3://PNG
            $file = $file . ".png";
            imagepng($im, $file);
            break;
        }
        if(is_file($file)){
            return $file;
        }else{
            return '';
        }
        imagedestroy($im);
    }

}
