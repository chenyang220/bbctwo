<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

class Api_Shop_ShopCtl extends Api_Controller
{

    public $shopBaseModel = null;
    public $shopGoodCatModel = null;
    public $shopNavModel = null;
    public $goodsCommonModel = null;
    public $shopDecorationModel = null;
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->shopBaseModel = new Shop_BaseModel();
        $this->shopGoodCatModel = new Shop_GoodCatModel();
        $this->shopNavModel = new Shop_NavModel();
        $this->goodsCommonModel = new Goods_CommonModel();
        $this->shopDecorationModel = new Shop_DecorationModel();
    }

    public function getShopById(){
            $shop_id = request_int('shop_id');
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
                $shop_cat = $this->shopGoodCatModel->getGoodCatList($cat_row, array('shop_goods_cat_displayorder' => 'ASC'));
                //店主推荐
                $goods_recom_list = $this->goodsCommonModel->getGoodsList(array(
                    "shop_id" => $shop_id,
                    "common_is_recommend" => 2,
                    "common_state" => 1,
                    'common_verify' => 1
                ), array(), 1, 12);

                //商品销量排行榜
                $goods_selling_list = $this->goodsCommonModel->getGoodsList(array(
                    "shop_id" => $shop_id,
                    "common_state" => 1,
                    'common_verify' => 1
                ), array("common_salenum" => "desc"), 1, 3);
            }
            if ($shop_base['is_renovation']) {

            //根据店铺id，查询出装修编号
            $cat_row['shop_id'] = $shop_id;
            $decoration_row = $this->shopDecorationModel->getOneByWhere($cat_row);

            //店铺装潢
            $decoration_detail = $this->shopDecorationModel->outputStoreDecoration($decoration_row['decoration_id'], $shop_id);
            }
        }
        if($shop_base['shop_slide']){
            $shop_base['shop_slide_data'] = explode(',',$shop_base['shop_slide']);
        }
        $shop_base['shop_slide_data'] = array_filter($shop_base['shop_slide_data']);
        $data['shop_base'] = empty($shop_base) ? array() : $shop_base;
        $data['shop_nav'] = empty($shop_nav) ? array() : $shop_nav;
        $data['shop_cat'] = empty($shop_cat) ? array() : $shop_cat;
        $data['goods_recom_list'] = empty($goods_recom_list) ? array() : $goods_recom_list;
        $data['goods_selling_list'] = empty($goods_selling_list) ? array() : $goods_selling_list;
        $this->data->addBody(-140, $data);


    }
    //获取店铺信息
    public function getStoreIntro()
    {
        $shop_id = request_int('shop_id');
        $shop_base = $this->shopBaseModel->getShopDetail($shop_id);
        $this->data->addBody(-140, $shop_base);
    }
    //获取最近三个月添加的商品
    public function getNewGoods(){
        $shop_id = request_int('shop_id');
        $page = request_int('page')?request_int('page'):1;
        $goods_new_list = $this->goodsCommonModel->getGoodsList(array(
            "shop_id" => $shop_id,
            "common_state" => 1,
            'common_verify' => 1,
            "common_add_time:>="=>date('Y-m-d',strtotime("-0 year -3 month -0 day"))
        ), array("common_add_time" => "desc"),$page, 12);
        $shop_base = $this->shopBaseModel->getShopDetail($shop_id);
        //计算折扣比例
        if(isset($goods_new_list['items'])) {
            foreach ($goods_new_list['items'] as $k => $v) {
                if ($v['goods_recommended_min_price'] > $v['common_price'] && $v['goods_recommended_max_price'] > $v['goods_recommended_min_price']) {
                    $goods_new_list['items'][$k]['goods_min_interest_rate'] = number_format(($v['goods_recommended_min_price'] - $v['common_price']) / $v['goods_recommended_min_price'] * 100, 2, '.', '');
                    $goods_new_list['items'][$k]['goods_max_interest_rate'] = number_format(($v['goods_recommended_max_price'] - $v['common_price']) / $v['goods_recommended_max_price'] * 100, 2, '.', '');
                }
            }
        }
        $data = array();
        $data['goods_new_list'] = $goods_new_list;
        $data['shop_base'] = $shop_base;
        $this->data->addBody(-140, $data);
    }
    //获取全部商品
    public function goodsList()
    {
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 20;
        $rows = $Yf_Page->listRows;
        $page = request_int('page')?request_int('page'):1;

        $pagesize = request_int('pagesize');//分页条数
        $curpage = request_int('curpage');//当前页数
        if (!empty($pagesize)) {
            $rows = $pagesize;
        }
        if (!empty($curpage)) {
            $page = $curpage;
        }
        $shop_id = request_int('shop_id');
        $sort = request_string('sort');//排序
        //店铺信息
        $shop_base = $this->shopBaseModel->getOne($shop_id);
        if (!empty($shop_base) && $shop_base['shop_status'] == 3) {
            $order_row = array();
            $cond_row = array();
            $search = request_string('search');
            $order = request_string('order');
            $shop_cat_id = request_int('shop_cat_id');
            $price_from = request_float('price_from');
            $price_to = request_float('price_to');
            $cond_row['shop_id'] = $shop_id;
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

            if ($price_from) {
                $cond_row['common_price:>='] = $price_from;
            }

            if ($price_to) {
                $cond_row['common_price:<='] = $price_to;
            }

            if ($order) {
                $order_row = array($order => $sort);
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

        }

        foreach ($datas['items'] as $k => $v) {
           if (!is_array($v['goods_id'])) {
                $datas['items'][$k]['goods_id'] = [['goods_id' => $v['goods_id'], 'color_id' => 0]];
             }
             //计算利润
            if ($v['goods_recommended_min_price'] > $v['common_price'] && $v['goods_recommended_max_price'] > $v['goods_recommended_min_price']) {
                $datas['items'][$k]['goods_min_interest_rate'] = number_format(($v['goods_recommended_min_price'] - $v['common_price']) / $v['goods_recommended_min_price'] * 100, 2, '.', '');
                $datas['items'][$k]['goods_max_interest_rate'] = number_format(($v['goods_recommended_max_price'] - $v['common_price']) / $v['goods_recommended_max_price'] * 100, 2, '.', '');
            }
        }
        $datas['shop_base']=$shop_base;
        $this->data->addBody(-140, $datas);
    }
    //获取店铺分类
    public function shopCat(){
        $cat_row['shop_id']  = request_int('shop_id');
        $data = $this->shopGoodCatModel->getGoodCatList($cat_row, array('shop_goods_cat_displayorder' => 'ASC'));
        $this->data->addBody(-140, $data);
    }

    /**
     * 确定收货时得到银联商务的对账单
     */
    public function editAccountChecking(){
        $accountCheckingModel =  new Ve_AccountCheckingModel();
        $accountCheckingModel->editConfirmOrderTime();
    }
}