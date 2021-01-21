<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class IndexCtl extends Controller
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

        $this->goodsCommonModel       = new Goods_CommonModel();
        $this->mbTplLayoutModel       = new Mb_TplLayoutModel();
        $this->GroupBuyBaseModel      = new GroupBuy_BaseModel();
        $this->FrontForumModel        = new Front_ForumModel();
        $this->DiscountGoodsModel     = new Discount_GoodsModel();
        $this->PinTuanBase            = new PinTuan_Base();
        $this->UserFootprintModel     = new User_FootprintModel();
        $this->AdvPageSettingsModel   = new Adv_PageSettingsModel();
        $this->SubSite                = new Sub_Site();
        $this->subsitemodel           = new Sub_Site();
        $this->SeckillGoodsModel       = new  Seckill_GoodsModel();
        $this->PresaleGoodsModel       = new  Presale_GoodsModel();
    }


    public function indexSelect()
    {
        if ('json' == $this->typ) {
             $data = array();
             if (!Web_ConfigModel::value("tpl_layout_style")) {
                $data[0] = 1;//普通首页模板 1、普通模板 2、工业模板 3、生鲜模板
             } else {
                $data[0] = Web_ConfigModel::value("tpl_layout_style");
             }




             return $this->data->addBody(-140, $data, 'success', 200);
        }
    }



    public function tsIndex () {
        $Label_BaseModel = new Label_BaseModel();
        $Label_Base = $Label_BaseModel->getByWhere(array("label_tag_sort:>"=>0,"label_tag_sort:<="=>8));    
        $label_tag_sort_arr = array_column($Label_Base, NULL,"label_tag_sort");
        $data['label_tag_sort'] = $label_tag_sort_arr;

        $layout_list = $this->mbTplLayoutModel->getByWhere(array('tpl_layout_style'=>4));
        $mb_tpl_layout_type_arr = array_column($layout_list, NULL,'mb_tpl_layout_type');
      
        $Label_Base_arr = $Label_BaseModel->getByWhere("*");
        $label_name_arr = array_column($Label_Base_arr, "label_name","id");

        $goods_arr = array();
        $Goods_BaseModel = new  Goods_BaseModel();
        $Goods_CommonModel = new Goods_CommonModel();
        if ($mb_tpl_layout_type_arr['goods']['mb_tpl_layout_data']) {
            foreach ($mb_tpl_layout_type_arr['goods']['mb_tpl_layout_data'] as $key => $goods_id) {
               $Goods_Base = $Goods_BaseModel->getOne($goods_id);
               $goods_arr[$key]['goods_name'] = $Goods_Base['goods_name'];
               $goods_arr[$key]['goods_image'] = $Goods_Base['goods_image'];
               $goods_arr[$key]['goods_id'] = $Goods_Base['goods_id'];
               $goods_arr[$key]['goods_price'] = $Goods_Base['goods_price'];
               $goods_arr[$key]['goods_salenum'] = $Goods_Base['goods_salenum'];
               $Goods_Common = $Goods_CommonModel->getOne($Goods_Base['common_id']);

                if ($Goods_Common['label_id']) {
                   $label_id_arr = explode(",", $Goods_Common['label_id']);
                   $label_name = [];
                   foreach ($label_id_arr as $keys => $label_id) {
                        if ($label_name_arr[$label_id] == "民风民俗" || $label_name_arr[$label_id] == "非遗") {
                           $goods_arr[$key]['detail'] = "detail2";
                        }
                        $label_name[] = $label_name_arr[$label_id];
                   }
                } else {
                    $label_name = '';
                }
               $goods_arr[$key]['label_name']  = $label_name;
               $goods_arr[$key]['goods_id'] = $Goods_Base['goods_id'];
            }
        }
        $mb_tpl_layout_type_arr['goods'] = $goods_arr;
        $data['layout_list'] = $mb_tpl_layout_type_arr;
        return $this->data->addBody(-140,$data, "uuu", 200);
    }

    public function index()
    {
        if ('json' == $this->typ) {
            $Goods_CommonModel = new Goods_CommonModel();
            if(request_string('ua') == 'wap')
            {
                $site_status = Web_ConfigModel::value("site_status_wap");
                if(!$site_status)
                {
                    $msg = Web_ConfigModel::value("closed_reason_wap");
                    $status = 250;
                    $data = [];

                    return $this->data->addBody(-140, $data, $msg, $status);
                }
            }
            $data = [];

            $subsite_is_open = Web_ConfigModel::value("subsite_is_open");
            if ($subsite_is_open == Sub_SiteModel::SUB_SITE_IS_OPEN) {
                $sub_site_id = request_int('sub_site_id');
                $subsite_is_open = 1;  //开启
                if ($sub_site_id > 0) {
                    
                    $sub_site_info = $this->subsitemodel->getSubSite($sub_site_id);
                    $sub_site_name = $sub_site_info[$sub_site_id]['sub_site_name'];
                }
            } else {
                $subsite_is_open = 0; //关闭
                $sub_site_id = 0;
            }
            $tpl_layout_style = request_int('tpl_layout_style', 1);
            $layout_list = $this->mbTplLayoutModel->getByWhere(['mb_tpl_layout_enable' => Mb_TplLayoutModel::USABLE, 'sub_site_id' => $sub_site_id,'tpl_layout_style'=>$tpl_layout_style], ['mb_tpl_layout_order' => 'ASC']);
            if (!empty($layout_list)) {
                foreach ($layout_list as $mb_tpl_layout_id => $layout_data_val) {
                    if ($layout_data_val['mb_tpl_layout_type'] == 'adv_list') {
                        $adv_list = $layout_data_val;
                        //头部滚动条
                        $slide_rows = isset($adv_list['mb_tpl_layout_data']) ? $adv_list['mb_tpl_layout_data']:[];
                        $slide_items = [];
                        foreach ($slide_rows as $s_k => $s_v) {
                            $item = [];
                            $item['image'] = $s_v['image'];
                            $item['type'] = $s_v['image_type'];
                            $item['data'] = $s_v['image_data'];
                            // $item['link']  = $s_v['image_data'];
                            $slide_items[] = $item;
                        }
                        if (!empty($slide_items)) {
                            $data[$mb_tpl_layout_id + 1]['slider_list']['item'] = $slide_items;
                        }
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
                    //工业模板
                    if ($layout_data_val['mb_tpl_layout_type'] == 'home5') {
                        $home5 = [];
                        $home5['title'] = $layout_data_val['mb_tpl_layout_title'];
                        $home5['type'] = $mb_tpl_layout_data['image_type'];

                        if ($layout_data_val['mb_tpl_layout_data']['goods_ids']) {
                            $common_ids = implode(",",$layout_data_val['mb_tpl_layout_data']['goods_ids']);
                            $sql = "SELECT  *  FROM  yf_goods_common  WHERE  common_id  IN($common_ids)  ORDER  BY  INSTR('$common_ids,',CONCAT(',',common_id,','))";
                            $db = new YFSQL();
                            $common_list = $db->find($sql);
               
                            if ($common_list) {
                                $item = [];
                                foreach ($common_list as $common_id => $common_data) {
                                    $goods_ids = $common_data['goods_id'];
                                    if (!empty($goods_ids)) {
                                        $goods_ids = json_decode($goods_ids,true);
                                    }
                                    $goods_id = pos($goods_ids);
                                    if (is_array($goods_id)) {
                                        $goods_id = pos($goods_id);
                                    }
                                    $item[$common_id]['goods_id'] = $goods_id;
                                    $item[$common_id]['goods_name'] = $common_data['common_name'];
                                    $item[$common_id]['goods_promotion_price'] = $common_data['common_price'];
                                    $item[$common_id]['common_market_price'] = $common_data['common_market_price'];
                                    $item[$common_id]['goods_image'] = image_thumb($common_data['common_image'], 360);
                                    $item[$common_id]['goods_salenum'] = $common_data['common_salenum'];
                                    $item[$common_id]['goods_evaluation_count'] = $common_data['common_evaluate'];
                                }
                                $layout_data_val['mb_tpl_layout_data']['goods_ids'] =array_values($item);

                            }
                        }
                        $home5['data'] = $layout_data_val['mb_tpl_layout_data'];
                        $data[$mb_tpl_layout_id + 1]['home5'] = $home5;
                    }
                    //工业模板品牌分类
                    if ($layout_data_val['mb_tpl_layout_type'] == 'class') {
                        $class = [];
                        $class['title'] = $layout_data_val['mb_tpl_layout_title'];
                        $class['type'] = $mb_tpl_layout_data['image_type'];
                        $class['data'] = $layout_data_val['mb_tpl_layout_data'];
                        $data[$mb_tpl_layout_id + 1]['class'] = $class;
                    }
                    //生鲜模板
                    if ($layout_data_val['mb_tpl_layout_type'] == 'newGoods') {
                        $newGoods = [];
                        $newGoods['sx_title'] = $layout_data_val['mb_tpl_layout_title'];
                        $newGoods['type'] = $mb_tpl_layout_data['image_type'];
                        if ($layout_data_val['mb_tpl_layout_data']['goods_ids']) {
                            $common_ids = implode(",",$layout_data_val['mb_tpl_layout_data']['goods_ids']);
                            $sql = "SELECT  *  FROM  yf_goods_common  WHERE  common_id  IN($common_ids)  ORDER  BY  INSTR('$common_ids,',CONCAT(',',common_id,','))";
                            $db = new YFSQL();
                            $common_list = $db->find($sql);
                            if ($common_list) {
                                $item = [];
                                foreach ($common_list as $common_id => $common_data) {
                                    $goods_ids = $common_data['goods_id'];
                                    if (!empty($goods_ids)) {
                                        $goods_ids = json_decode($goods_ids,true);
                                    }
                                    $goods_id = pos($goods_ids);
                                    if (is_array($goods_id)) {
                                        $goods_id = pos($goods_id);
                                    }
                                    $item[$common_id]['goods_id'] = $goods_id;
                                    $item[$common_id]['goods_name'] = $common_data['common_name'];
                                    $item[$common_id]['goods_promotion_price'] = $common_data['common_price'];
                                    $item[$common_id]['common_market_price'] = $common_data['common_market_price'];
                                    $item[$common_id]['goods_image'] = image_thumb($common_data['common_image'], 360);
                                    $item[$common_id]['goods_salenum'] = $common_data['common_salenum'];
                                    $item[$common_id]['goods_evaluation_count'] = $common_data['common_evaluate'];
                                }
                                $newGoods['goods_ids'] =array_values($item);

                            }
                        }
                        unset($layout_data_val['mb_tpl_layout_data']['goods_ids']);
                        $newGoods['data'] = array_values($layout_data_val['mb_tpl_layout_data']);
                        $data[$mb_tpl_layout_id + 1]['newGoods'] = $newGoods;
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
                    if ($layout_data_val['mb_tpl_layout_type'] == 'goods' || $layout_data_val['mb_tpl_layout_type'] == 'goodsB'|| $layout_data_val['mb_tpl_layout_type'] == 'goodsC') {
                        if($layout_data_val['mb_tpl_layout_type'] == 'goods') {
                            $goods = [];
                        } else if($layout_data_val['mb_tpl_layout_type'] == 'goodsB') {
                            $goodsB = [];
                        }else
                        {
                            $goodsC = [];
                        }
                        $item = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        if (request_string('type_wxapp') == 'wxapp') {
                            $common_ids = implode(",",$mb_tpl_layout_data);
                            $sql = "SELECT  *  FROM  yf_goods_common  WHERE  common_id  IN($common_ids) and common_is_tuan = 0 ORDER  BY  INSTR('$common_ids,',CONCAT(',',common_id,','))";
                            $db = new YFSQL();
                            $common_list = $db->find($sql);
                        } else {
                            $common_ids = implode(",",$mb_tpl_layout_data);
                            $sql = "SELECT  *  FROM  yf_goods_common  WHERE  common_id  IN($common_ids)  ORDER  BY  INSTR('$common_ids,',CONCAT(',',common_id,','))";
                            $db = new YFSQL();
                            $common_list = $db->find($sql);
                        }
                           $Goods_CommonModel = new Goods_CommonModel();
                        if ($common_list) {
                            foreach ($common_list as $common_id => $common_data) {
                                $goods_id = $Goods_CommonModel->getNormalStateGoodsId($common_data['common_id']);
                                $item[$common_id]['goods_id'] = $goods_id;
                                $item[$common_id]['goods_name'] = $common_data['common_name'];
                                $item[$common_id]['goods_promotion_price'] = $common_data['common_price'];
                                $item[$common_id]['goods_image'] = image_thumb($common_data['common_image'], 360);
                                $item[$common_id]['common_market_price'] = $common_data['common_market_price'];
                                $item[$common_id]['goods_salenum'] = $common_data['common_salenum'];
                                $item[$common_id]['goods_evaluation_count'] = $common_data['common_evaluate'];
                            }
                            if($layout_data_val['mb_tpl_layout_type'] == 'goods') {

                                $goods['item'] =array_values($item);
                                $goods['title'] = $layout_data_val['mb_tpl_layout_title'];
                                $data[$mb_tpl_layout_id + 1]['goods'] = $goods;

                            }else if($layout_data_val['mb_tpl_layout_type'] == 'goodsB'){
                                $goodsB['item'] = array_values($item);
                                $goodsB['title'] = $layout_data_val['mb_tpl_layout_title'];
                                $data[$mb_tpl_layout_id + 1]['goodsB'] = $goodsB;
                            }else
                            {
                                $goodsC['item'] = array_values($item);
                                $goodsC['title'] = $layout_data_val['mb_tpl_layout_title'];
                                $data[$mb_tpl_layout_id + 1]['goodsC'] = $goodsC;
                            }
                        }
                    }
                    //快捷入口
                    if($layout_data_val['mb_tpl_layout_type'] == 'enterance') {
                        $enterance = [];
                        $item = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        $open_status = Web_ConfigModel::value('plus_switch')?:0;
                        foreach ($mb_tpl_layout_data as $key => $layout_data) {
                            //如果后台关闭plus则前台不显示
                            if (!$open_status && $layout_data['navName']=='plus会员') {
                                unset($layout_data);
                            }else{
                                $item[$key]['icons'] = $layout_data['icons'];
                                $item[$key]['navName'] = $layout_data['navName'];
                                $item[$key]['url'] = $layout_data['url'];       
                            }
                        }
                        $enterance['item'] = $item;
                        $enterance['title'] = $layout_data_val['mb_tpl_layout_title'];
                        $data[$mb_tpl_layout_id + 1]['enterance'] = $enterance;
                    }

                    //广告版块A
                    if($layout_data_val['mb_tpl_layout_type'] == 'advA') {
                        $advA = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        $advA['title'] = $layout_data_val['mb_tpl_layout_title'];
                        $advA['rectangle1_image'] = $mb_tpl_layout_data['rectangle1']['image'];
                        $advA['rectangle1_type'] = $mb_tpl_layout_data['rectangle1']['image_type'];
                        $advA['rectangle1_data'] = $mb_tpl_layout_data['rectangle1']['image_data'];
                        $advA['rectangle2_image'] = $mb_tpl_layout_data['rectangle2']['image'];
                        $advA['rectangle2_type'] = $mb_tpl_layout_data['rectangle2']['image_type'];
                        $advA['rectangle2_data'] = $mb_tpl_layout_data['rectangle2']['image_data'];
                        $advA['rectangle3_image'] = $mb_tpl_layout_data['rectangle3']['image'];
                        $advA['rectangle3_type'] = $mb_tpl_layout_data['rectangle3']['image_type'];
                        $advA['rectangle3_data'] = $mb_tpl_layout_data['rectangle3']['image_data'];
                        $advA['rectangle4_image'] = $mb_tpl_layout_data['rectangle4']['image'];
                        $advA['rectangle4_type'] = $mb_tpl_layout_data['rectangle4']['image_type'];
                        $advA['rectangle4_data'] = $mb_tpl_layout_data['rectangle4']['image_data'];
                        $advA['rectangle5_image'] = $mb_tpl_layout_data['rectangle5']['image'];
                        $advA['rectangle5_type'] = $mb_tpl_layout_data['rectangle5']['image_type'];
                        $advA['rectangle5_data'] = $mb_tpl_layout_data['rectangle5']['image_data'];
                        $advA['rectangle6_image'] = $mb_tpl_layout_data['rectangle6']['image'];
                        $advA['rectangle6_type'] = $mb_tpl_layout_data['rectangle6']['image_type'];
                        $advA['rectangle6_data'] = $mb_tpl_layout_data['rectangle6']['image_data'];
                        $advA['square_image'] = $mb_tpl_layout_data['square']['image'];
                        $advA['square_type'] = $mb_tpl_layout_data['square']['image_type'];
                        $advA['square_data'] = $mb_tpl_layout_data['square']['image_data'];
                        $data[$mb_tpl_layout_id + 1][$layout_data_val['mb_tpl_layout_type']] = $advA;
                    }
                    //广告版块B
                    if($layout_data_val['mb_tpl_layout_type'] == 'advB') {
                        $advB = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        $advB['title'] = $layout_data_val['mb_tpl_layout_title'];
                        $advB['rectangle1_image'] = $mb_tpl_layout_data['rectangle1']['image'];
                        $advB['rectangle1_type'] = $mb_tpl_layout_data['rectangle1']['image_type'];
                        $advB['rectangle1_data'] = $mb_tpl_layout_data['rectangle1']['image_data'];
                        $advB['rectangle2_image'] = $mb_tpl_layout_data['rectangle2']['image'];
                        $advB['rectangle2_type'] = $mb_tpl_layout_data['rectangle2']['image_type'];
                        $advB['rectangle2_data'] = $mb_tpl_layout_data['rectangle2']['image_data'];
                        $advB['rectangle3_image'] = $mb_tpl_layout_data['rectangle3']['image'];
                        $advB['rectangle3_type'] = $mb_tpl_layout_data['rectangle3']['image_type'];
                        $advB['rectangle3_data'] = $mb_tpl_layout_data['rectangle3']['image_data'];
                        $advB['rectangle4_image'] = $mb_tpl_layout_data['rectangle4']['image'];
                        $advB['rectangle4_type'] = $mb_tpl_layout_data['rectangle4']['image_type'];
                        $advB['rectangle4_data'] = $mb_tpl_layout_data['rectangle4']['image_data'];
                        $advB['rectangle5_image'] = $mb_tpl_layout_data['rectangle5']['image'];
                        $advB['rectangle5_type'] = $mb_tpl_layout_data['rectangle5']['image_type'];
                        $advB['rectangle5_data'] = $mb_tpl_layout_data['rectangle5']['image_data'];
                        $data[$mb_tpl_layout_id + 1][$layout_data_val['mb_tpl_layout_type']] = $advB;
                    }

                    //活动版块AB
                    if($layout_data_val['mb_tpl_layout_type'] == 'activityA' || $layout_data_val['mb_tpl_layout_type'] == 'activityB') {
                        if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                            $activityA = [];
                        } else {
                            $activityB = [];
                        }
                        $item = [];
                        $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
                        foreach ($mb_tpl_layout_data as $key => $layout_data) {
                            switch($layout_data['type']) {
                                case 'groupbuy':
                                    //获取团购商品信息
                                    
                                    $content_info = $this->GroupBuyBaseModel->getForumGroupbuy(explode(',',$layout_data['content']));
                                    if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 2 - count($content_info);
                                    }
                                    if($num > 0) {
                                        
                                        $content = $this->FrontForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $layout_data['content_info'] = array_values($content_info);
                                    break;
                                case 'discount':
                                    //获取限时折扣商品信息
                                   
                                    $discount_row = array();
                                    $discount_row['discount_goods_id:IN'] = explode(',', $layout_data['content']);
                                    $discount_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
                                    $discount_row['discount_goods_state'] = 1;
                                    $content = $this->DiscountGoodsModel->getDiscountGoods($discount_row);
                                    $content_info = array_values($content);
                                    if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 3 - count($content_info);
                                    }
                                    if($num > 0) {
                                        
                                        $content = $this->FrontForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $layout_data['content_info'] = array_values($content_info);
                                    break;
                                case 'pintuan':
                                    //拼团活动商品
                                    
                                    $cond_row = array();
                                    $cond_row['id:IN'] = explode(',',$layout_data['content']);
                                    $cond_row['status'] = 1;//拼团有效
                                    $cond_row['start_time:<'] = date('Y-m-d H:i:s');
                                    $cond_row['end_time:>'] = date('Y-m-d H:i:s');
                                    $content_info = $this->PinTuanBase->getTplPinTuanGoods($cond_row);
                                    if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 2 - count($content_info);
                                    }
                                    if($num > 0) {
                                        
                                        $content = $this->FrontForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $layout_data['content_info'] = array_values($content_info);
                                    break;
                                case 'seckill':
                                    //获取秒杀商品信息
                                   
                                    $seckill_row = array();
                                    $seckill_row['seckill_goods_id:IN'] = explode(',', $layout_data['content']);
                                    $seckill_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
                                    $seckill_row['seckill_goods_state'] = 1;
                                    $content = $this->SeckillGoodsModel->getSeckillGoods($seckill_row);
                                    $content_info = array_values($content);
                                    if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 3 - count($content_info);
                                    }
                                    if($num > 0) {
                                        
                                        $content = $this->FrontForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $layout_data['content_info'] = array_values($content_info);
                                    break;

                                case 'presale':
                                    //获取预售商品信息
                                   
                                    $presale_row = array();
                                    $presale_row['presale_goods_id:IN'] = explode(',', $layout_data['content']);
                                    $presale_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
                                    $presale_row['presale_goods_state'] = 1;
                                    $content = $this->PresaleGoodsModel->getPresaleGoods($presale_row);
                                    $content_info = array_values($content);
                                    if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 3 - count($content_info);
                                    }
                                    if($num > 0) {
                                        
                                        $content = $this->FrontForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $layout_data['content_info'] = array_values($content_info);
                                    break;
                                case 'redpacket':
                                    //红包
                                    $layout_data['content_info'] = $layout_data['content'];
                                    break;
                                case 'voucher':
                                    //代金券
                                    $layout_data['content_info'] = $layout_data['content'];
                                    break;
                                default:
                                    ;
                            }
                            $item[$key]['title'] = $layout_data['title'];
                            $item[$key]['content_info'] = $layout_data['content_info'];
                            $item[$key]['content'] = $layout_data['content'];
                            $item[$key]['type'] = $layout_data['type'];
                        }
                        if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                            $activityA['item'] = current($item);
                            $activityA['title'] = $layout_data_val['mb_tpl_layout_title'];
                            $data[$mb_tpl_layout_id + 1]['activityA'] = $activityA;
                        } else {
                            $activityB['item'] = $item;
                            $activityB['title'] = $layout_data_val['mb_tpl_layout_title'];
                            $data[$mb_tpl_layout_id + 1]['activityB'] = $activityB;
                        }
                    }
                }
            }

            //猜你喜欢
            $favourite_goods = $this->UserFootprintModel->userFavorite();
            $Goods_BaseModel = new Goods_BaseModel();
            
            //如果用户登录，判断是否有未读信息
            $MessageModel = new MessageModel();
            $message = $MessageModel->getMessageCount(request_string('user_id'));

            $result_data = [];
            $result_data['module_data'] = array_values($data);
            $result_data['site_logo'] = Web_ConfigModel::value("setting_logo");
            $result_data['sub_site_id'] = $sub_site_id;
            $result_data['subsite_is_open'] = $subsite_is_open;
            $result_data['favourite_goods'] = $favourite_goods;
            $result_data['message'] = $message;

            //SEO设置
            $result_data['title'] = Web_ConfigModel::value('title')? Web_ConfigModel::value('title'): Web_ConfigModel::value('site_name');
            $result_data['keyword'] = Web_ConfigModel::value('keyword');
            $result_data['description'] = Web_ConfigModel::value('description');
            if (isset($sub_site_name)) {
                $result_data['sub_site_name'] = $sub_site_name;
            } else {
                $result_data['sub_site_name'] = '';
            }
            return $this->data->addBody(-140, $result_data);
        } else {
            $Cache = Yf_Cache::create('default');
            $site_index_key = sprintf('%s|%s|%s', Yf_Registry::get('server_id'), 'site_index', isset($_COOKIE['sub_site_id']) ? $_COOKIE['sub_site_id']:0);
            if (!$Cache->start($site_index_key)) {
                $this->initData();
                $subsite_is_open = Web_ConfigModel::value("subsite_is_open");
                if (!empty($_COOKIE['sub_site_id']) && $subsite_is_open == Sub_SiteModel::SUB_SITE_IS_OPEN) {
                    $sub_site_id = $_COOKIE['sub_site_id'];
                } else {
                    $sub_site_id = 0;
                }
                //团购风暴
                
                //先判断首页推荐的团购是否超过5个，如果超过则只显示首页推荐团购，如果不超过则只显示包括推荐团购在内的5个团购
                //查找推荐团购的个数
                $groupbuy_recommend = $this->GroupBuyBaseModel->getByWhere(['groupbuy_state' => GroupBuy_BaseModel::NORMAL, 'groupbuy_recommend' => GroupBuy_BaseModel::RECOMMEND]);
                $groupbuy_count = count($groupbuy_recommend);
                $gb_goods_list = [];
                if ($groupbuy_count < 5) {
                    $cond_row = [
                        "groupbuy_starttime:<=" => get_date_time(),
                        "groupbuy_endtime :>=" => get_date_time(),
                        "groupbuy_state" => GroupBuy_BaseModel::NORMAL,
                    ];
                    $order_row = ["groupbuy_recommend" => "desc"];
                    $gb_goods_list = $this->GroupBuyBaseModel->getGroupBuyGoodsList($cond_row, $order_row, 1, 5, $sub_site_id);
                } else {
                    $cond_row = [
                        "groupbuy_starttime:<=" => get_date_time(),
                        "groupbuy_endtime :>=" => get_date_time(),
                        "groupbuy_state" => GroupBuy_BaseModel::NORMAL,
                        'groupbuy_recommend' => GroupBuy_BaseModel::RECOMMEND,
                    ];
                    $order_row = [];
                    $gb_goods_list = $this->GroupBuyBaseModel->getGroupBuyGoodsList($cond_row, $order_row, 1, 15, $sub_site_id);
                }
                //楼层设置
                
                $subsite_is_open = Web_ConfigModel::value("subsite_is_open");
                if ($sub_site_id) {
                    $cond_adv_row['sub_site_id'] = $_COOKIE['sub_site_id'];
                    //首页标题关键字
                    
                    $sub_site_info = $this->SubSite->getSubSite($_COOKIE['sub_site_id']);
                    $title = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_title'];//首页名;
                    $this->keyword = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_keyword'];//关键字;
                    $this->description = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_des'];//描述;
                    $this->title = str_replace("{sitename}", $this->web['web_name'], $title);
                    $this->keyword = str_replace("{sitename}", $this->web['web_name'], $this->keyword);
                    $this->description = str_replace("{sitename}", $this->web['web_name'], $this->description);
                } else {
                    $cond_adv_row['sub_site_id'] = 0;
                    //首页标题关键字
                    $title = Web_ConfigModel::value("title");//首页名;
                    $this->keyword = Web_ConfigModel::value("keyword");//关键字;
                    $this->description = Web_ConfigModel::value("description");//描述;
                    $this->title = str_replace("{sitename}", $this->web['web_name'], $title);
                    $this->keyword = str_replace("{sitename}", $this->web['web_name'], $this->keyword);
                    $this->description = str_replace("{sitename}", $this->web['web_name'], $this->description);
                }
                $cond_adv_row['page_status'] = 1;
                $order_adv_row = ["page_order" => "asc"];
                $adv_list = $this->AdvPageSettingsModel->listByWhere($cond_adv_row, $order_adv_row);
				//懒加载封装 @nsy 2019-10-14
				foreach( $adv_list['items'] as $k=>&$item){
					 $result = preg_replace('/<img([^<]+)src="([^"]+)"([^>]+)>/im', '<img src="" data-src="$2" $1 $3 >', $item['page_html']);
					 $item['page_html'] = $result;
					
				} 
                //后台首页模板
                $forum = $this->FrontForumModel->getOpenForumContent();

                //查找HTML
                $forum_html = $this->FrontForumModel->getForumHtml($forum);

                //猜你喜欢
                //$favourite_goods = $this->UserFootprintModel->userFavorite();
                include $this->view->getView();
                $Cache->_id = $site_index_key;
                $Cache->end($site_index_key);
            }
        }
    }

    /**
     * 猜你喜欢
     * @nsy 2019-10-15
     */
    public function guessFavourite(){
        $favourite_goods = $this->UserFootprintModel->userFavorite();
        $this->data->addBody(-140,  $favourite_goods);
    }

    public function getClosedReason()
    {
        if(request_string('ua') == 'wap')
        {
            $site_status = Web_ConfigModel::value("site_status_wap");
            if(!$site_status)
            {
                $msg = Web_ConfigModel::value("closed_reason_wap");
                $status = 250;
                $data = [];

                return $this->data->addBody(-140, $data, $msg, $status);
            }
        }

        return $this->data->addBody(-140,  array());
    }

    public function getRedpacketSet()
    {

        $status = Web_ConfigModel::value("redpacketset");
        $data['status'] = $status;

        return $this->data->addBody(-140, $data);

    }

    //小程序首页
    public function wxapp_index()
    {
        if ('json' == $this->typ) {

            $site_status = Web_ConfigModel::value("site_status_wxapp");
            if(!$site_status)
            {
                $msg = Web_ConfigModel::value("closed_reason_wxapp");
                $status = 250;
                $data = [];

                return $this->data->addBody(-140, $data, $msg, $status);
            }

            $data = [];

            // $data[] = array();
            $goods_CommonModel = new Goods_CommonModel();
            $wxTplLayoutModel = new Wx_TplLayoutModel();
            $subsite_is_open = Web_ConfigModel::value("subsite_is_open");
            if ($subsite_is_open == Sub_SiteModel::SUB_SITE_IS_OPEN) {
                $sub_site_id = request_int('sub_site_id');
                $subsite_is_open = 1;  //开启
                if ($sub_site_id > 0) {
                    $sub_site_model = new Sub_Site();
                    $sub_site_info = $sub_site_model->getSubSite($sub_site_id);
                    $sub_site_name = $sub_site_info[$sub_site_id]['sub_site_name'];
                }
            } else {
                $subsite_is_open = 0; //关闭
                $sub_site_id = 0;
            }
            $layout_list = $wxTplLayoutModel->getByWhere(['wx_tpl_layout_enable' => Wx_TplLayoutModel::USABLE, 'sub_site_id' => $sub_site_id], ['wx_tpl_layout_order' => 'ASC']);
            if (!empty($layout_list)) {
                foreach ($layout_list as $wx_tpl_layout_id => $layout_data_val) {
                    if ($layout_data_val['wx_tpl_layout_type'] == 'adv_list') {
                        $adv_list = $layout_data_val;
                        //头部滚动条
                        $slide_rows = isset($adv_list['wx_tpl_layout_data']) ? $adv_list['wx_tpl_layout_data']:[];
                        $slide_items = [];
                        foreach ($slide_rows as $s_k => $s_v) {
                            $item = [];
                            $item['image'] = $s_v['image'];
                            $item['type'] = $s_v['image_type'];
                            $item['data'] = $s_v['image_data'];
                            // $item['link']  = $s_v['image_data'];
                            $slide_items[] = $item;
                        }
                        if (!empty($slide_items)) {
                            $data[$wx_tpl_layout_id + 1]['slider_list']['item'] = $slide_items;
                        }
                    }
                    if ($layout_data_val['wx_tpl_layout_type'] == 'home1') {
                        $hom1 = [];
                        $wx_tpl_layout_data = $layout_data_val['wx_tpl_layout_data'];
                        $hom1['title'] = $layout_data_val['wx_tpl_layout_title'];
                        $hom1['image'] = $wx_tpl_layout_data['image'];
                        $hom1['type'] = $wx_tpl_layout_data['image_type'];
                        $hom1['data'] = $wx_tpl_layout_data['image_data'];
                        $data[$wx_tpl_layout_id + 1]['home1'] = $hom1;
                    }
                    if ($layout_data_val['wx_tpl_layout_type'] == 'home2' || $layout_data_val['wx_tpl_layout_type'] == 'home4') {
                        $home2_4 = [];
                        $wx_tpl_layout_data = $layout_data_val['wx_tpl_layout_data'];
                        $home2_4['title'] = $layout_data_val['wx_tpl_layout_title'];
                        $home2_4['rectangle1_image'] = $wx_tpl_layout_data['rectangle1']['image'];
                        $home2_4['rectangle1_type'] = $wx_tpl_layout_data['rectangle1']['image_type'];
                        $home2_4['rectangle1_data'] = $wx_tpl_layout_data['rectangle1']['image_data'];
                        $home2_4['rectangle2_image'] = $wx_tpl_layout_data['rectangle2']['image'];
                        $home2_4['rectangle2_type'] = $wx_tpl_layout_data['rectangle2']['image_type'];
                        $home2_4['rectangle2_data'] = $wx_tpl_layout_data['rectangle2']['image_data'];
                        $home2_4['square_image'] = $wx_tpl_layout_data['square']['image'];
                        $home2_4['square_type'] = $wx_tpl_layout_data['square']['image_type'];
                        $home2_4['square_data'] = $wx_tpl_layout_data['square']['image_data'];
                        $data[$wx_tpl_layout_id + 1][$layout_data_val['wx_tpl_layout_type']] = $home2_4;
                    }
                    if ($layout_data_val['wx_tpl_layout_type'] == 'home3') {
                        $home3 = [];
                        $item = [];
                        $wx_tpl_layout_data = $layout_data_val['wx_tpl_layout_data'];
                        foreach ($wx_tpl_layout_data as $key => $layout_data) {
                            $item[$key]['image'] = $layout_data['image'];
                            $item[$key]['type'] = $layout_data['image_type'];
                            $item[$key]['data'] = $layout_data['image_data'];
                        }
                        $home3['item'] = $item;
                        $home3['title'] = $layout_data_val['wx_tpl_layout_title'];
                        $data[$wx_tpl_layout_id + 1]['home3'] = $home3;
                    }
                    if ($layout_data_val['wx_tpl_layout_type'] == 'goods' || $layout_data_val['wx_tpl_layout_type'] == 'goodsB' || $layout_data_val['wx_tpl_layout_type'] == 'goodsC') {
                        if($layout_data_val['wx_tpl_layout_type'] == 'goods') {
                            $goods = [];
                        } else  if($layout_data_val['wx_tpl_layout_type'] == 'goodsB'){
                            $goodsB = [];
                        }else
                        {
                            $goodsC = [];
                        }
                        $item = [];
                        $wx_tpl_layout_data = $layout_data_val['wx_tpl_layout_data'];
                        if (request_string('type_wxapp') == 'wxapp') {
                            $common_list = $goods_CommonModel->getByWhere(['common_id:IN' => $wx_tpl_layout_data, 'common_is_tuan' => 0]);
                        } else {
                            $common_list = $goods_CommonModel->getByWhere(['common_id:IN' => $wx_tpl_layout_data]);
                        }
                        if ($common_list) {
                            foreach ($common_list as $common_id => $common_data) {
                                $goods_id = pos($common_data['goods_id']);
                                if (is_array($goods_id)) {
                                    $goods_id = pos($goods_id);
                                }
                                $item[$common_id]['goods_id'] = $goods_id;
                                $item[$common_id]['goods_name'] = $common_data['common_name'];
                                $item[$common_id]['goods_promotion_price'] = $common_data['common_price'];
                                $item[$common_id]['goods_image'] = image_thumb($common_data['common_image'], 360);
                                $item[$common_id]['goods_salenum'] = $common_data['common_salenum'];
                                $item[$common_id]['goods_evaluation_count'] = $common_data['common_evaluate'];
                            }
                            if($layout_data_val['wx_tpl_layout_type'] == 'goods') {
                                $goods['item'] = array_values($item);
                                $goods['title'] = $layout_data_val['wx_tpl_layout_title'];
                                $data[$wx_tpl_layout_id + 1]['goods'] = $goods;
                            } else if($layout_data_val['wx_tpl_layout_type'] == 'goodsB'){
                                $goodsB['item'] = array_values($item);
                                $goodsB['title'] = $layout_data_val['wx_tpl_layout_title'];
                                $data[$wx_tpl_layout_id + 1]['goodsB'] = $goodsB;
                            }else
                            {
                                $goodsC['item'] = array_values($item);
                                $goodsC['title'] = $layout_data_val['wx_tpl_layout_title'];
                                $data[$wx_tpl_layout_id + 1]['goodsC'] = $goodsC;
                            }
                        }
                    }
                    //快捷入口
                    if($layout_data_val['wx_tpl_layout_type'] == 'enterance') {
                        $enterance = [];
                        $item = [];
                        $wx_tpl_layout_data = $layout_data_val['wx_tpl_layout_data'];
                        foreach ($wx_tpl_layout_data as $key => $layout_data) {
                            $item[$key]['icons'] = $layout_data['icons'];
                            $item[$key]['navName'] = $layout_data['navName'];
                            $item[$key]['url'] = $layout_data['url'];
                        }
                        $enterance['item'] = $item;
                        $enterance['title'] = $layout_data_val['wx_tpl_layout_title'];
                        $data[$wx_tpl_layout_id + 1]['enterance'] = $enterance;
                    }

                    //广告版块A
                    if($layout_data_val['wx_tpl_layout_type'] == 'advA') {
                        $advA = [];
                        $wx_tpl_layout_data = $layout_data_val['wx_tpl_layout_data'];
                        $advA['title'] = $layout_data_val['wx_tpl_layout_title'];
                        $advA['rectangle1_image'] = $wx_tpl_layout_data['rectangle1']['image'];
                        $advA['rectangle1_type'] = $wx_tpl_layout_data['rectangle1']['image_type'];
                        $advA['rectangle1_data'] = $wx_tpl_layout_data['rectangle1']['image_data'];
                        $advA['rectangle2_image'] = $wx_tpl_layout_data['rectangle2']['image'];
                        $advA['rectangle2_type'] = $wx_tpl_layout_data['rectangle2']['image_type'];
                        $advA['rectangle2_data'] = $wx_tpl_layout_data['rectangle2']['image_data'];
                        $advA['rectangle3_image'] = $wx_tpl_layout_data['rectangle3']['image'];
                        $advA['rectangle3_type'] = $wx_tpl_layout_data['rectangle3']['image_type'];
                        $advA['rectangle3_data'] = $wx_tpl_layout_data['rectangle3']['image_data'];
                        $advA['rectangle4_image'] = $wx_tpl_layout_data['rectangle4']['image'];
                        $advA['rectangle4_type'] = $wx_tpl_layout_data['rectangle4']['image_type'];
                        $advA['rectangle4_data'] = $wx_tpl_layout_data['rectangle4']['image_data'];
                        $advA['rectangle5_image'] = $wx_tpl_layout_data['rectangle5']['image'];
                        $advA['rectangle5_type'] = $wx_tpl_layout_data['rectangle5']['image_type'];
                        $advA['rectangle5_data'] = $wx_tpl_layout_data['rectangle5']['image_data'];
                        $advA['rectangle6_image'] = $wx_tpl_layout_data['rectangle6']['image'];
                        $advA['rectangle6_type'] = $wx_tpl_layout_data['rectangle6']['image_type'];
                        $advA['rectangle6_data'] = $wx_tpl_layout_data['rectangle6']['image_data'];
                        $advA['square_image'] = $wx_tpl_layout_data['square']['image'];
                        $advA['square_type'] = $wx_tpl_layout_data['square']['image_type'];
                        $advA['square_data'] = $wx_tpl_layout_data['square']['image_data'];
                        $data[$wx_tpl_layout_id + 1][$layout_data_val['wx_tpl_layout_type']] = $advA;
                    }
                    //广告版块B
                    if($layout_data_val['wx_tpl_layout_type'] == 'advB') {
                        $advB = [];
                        $wx_tpl_layout_data = $layout_data_val['wx_tpl_layout_data'];
                        $advB['title'] = $layout_data_val['wx_tpl_layout_title'];
                        $advB['rectangle1_image'] = $wx_tpl_layout_data['rectangle1']['image'];
                        $advB['rectangle1_type'] = $wx_tpl_layout_data['rectangle1']['image_type'];
                        $advB['rectangle1_data'] = $wx_tpl_layout_data['rectangle1']['image_data'];
                        $advB['rectangle2_image'] = $wx_tpl_layout_data['rectangle2']['image'];
                        $advB['rectangle2_type'] = $wx_tpl_layout_data['rectangle2']['image_type'];
                        $advB['rectangle2_data'] = $wx_tpl_layout_data['rectangle2']['image_data'];
                        $advB['rectangle3_image'] = $wx_tpl_layout_data['rectangle3']['image'];
                        $advB['rectangle3_type'] = $wx_tpl_layout_data['rectangle3']['image_type'];
                        $advB['rectangle3_data'] = $wx_tpl_layout_data['rectangle3']['image_data'];
                        $advB['rectangle4_image'] = $wx_tpl_layout_data['rectangle4']['image'];
                        $advB['rectangle4_type'] = $wx_tpl_layout_data['rectangle4']['image_type'];
                        $advB['rectangle4_data'] = $wx_tpl_layout_data['rectangle4']['image_data'];
                        $advB['rectangle5_image'] = $wx_tpl_layout_data['rectangle5']['image'];
                        $advB['rectangle5_type'] = $wx_tpl_layout_data['rectangle5']['image_type'];
                        $advB['rectangle5_data'] = $wx_tpl_layout_data['rectangle5']['image_data'];
                        $data[$wx_tpl_layout_id + 1][$layout_data_val['wx_tpl_layout_type']] = $advB;
                    }

                    //活动版块AB
                    if($layout_data_val['wx_tpl_layout_type'] == 'activityA' || $layout_data_val['wx_tpl_layout_type'] == 'activityB') {
                        if($layout_data_val['wx_tpl_layout_type'] == 'activityA') {
                            $activityA = [];
                        } else {
                            $activityB = [];
                        }
                        $item = [];
                        $wx_tpl_layout_data = $layout_data_val['wx_tpl_layout_data'];
                        foreach ($wx_tpl_layout_data as $key => $layout_data) {
                            switch($layout_data['type']) {
                                case 'groupbuy':
                                    //获取团购商品信息
                                    
                                    $content_info = $this->GroupBuyBaseModel->getForumGroupbuy(explode(',',$layout_data['content']));
                                    if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 2 - count($content_info);
                                    }
                                    if($num > 0) {
                                        
                                        $content = $this->FrontForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $layout_data['content_info'] = array_values($content_info);
                                    break;
                                case 'pintuan':
                                    //拼团活动商品
                                    
                                    $cond_row = array();
                                    $cond_row['id:IN'] = explode(',',$layout_data['content']);
                                    $cond_row['status'] = 1;//拼团有效
                                    $cond_row['start_time:<'] = date('Y-m-d H:i:s');
                                    $cond_row['end_time:>'] = date('Y-m-d H:i:s');
                                    $content_info = $this->PinTuanBase->getTplPinTuanGoods($cond_row);
                                    if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 2 - count($content_info);
                                    }
                                    if($num > 0) {
                                        
                                        $content = $this->FrontForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $layout_data['content_info'] = array_values($content_info);
                                    break;
                                case 'discount':
                                    //获取限时折扣商品信息
                                    $Discount_GoodsModel = new Discount_GoodsModel();
                                    $discount_row = array();
                                    $discount_goods_ids = explode(',', $layout_data['content']);
                                    $discount_row['discount_goods_id:IN'] = $discount_goods_ids;
                                    $discount_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
                                    $discount_row['discount_goods_state'] = 1;
                                    $content = $Discount_GoodsModel->getDiscountGoods($discount_row);
                                    $content_info = array_values($content);
                                    if($layout_data_val['wx_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 3 - count($content_info);
                                    }
                                    
                                    if($num > 0) {
                                        $Front_ForumModel = new Front_ForumModel();
                                        $content = $Front_ForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $content_info = array_chunk($content_info, 3);
                                    $layout_data['content_info'] = $content_info;
                                    break;

                                case 'presale':
                                    //获取限时折扣商品信息
                                    $Presale_GoodsModel = new Presale_GoodsModel();
                                    $presale_row = array();
                                    $presale_goods_ids = explode(',', $layout_data['content']);
                                    $presale_row['presale_goods_id:IN'] = $presale_goods_ids;
                                    $presale_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
                                    $presale_row['presale_goods_state'] = 1;
                                    $content = $Presale_GoodsModel->getPresaleGoods($presale_row);

                                   // $content_info = array_values($content);
                                    $content_info = array();
                                    foreach ($presale_goods_ids as $goods_id) {
                                        $content_info[] = $content[$goods_id];
                                    }
                                    //$content_info = array_values($content);
                                    if($layout_data_val['wx_tpl_layout_type'] == 'activityA') {
                                        $num = 12 - count($content_info);
                                    } else {
                                        $num = 3 - count($content_info);
                                    }
                                    
                                    if($num > 0) {
                                        $Front_ForumModel = new Front_ForumModel();
                                        $content = $Front_ForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
                                        if($content) {
                                            $content_info = array_merge($content_info,$content);
                                        }
                                    }
                                    $content_info = array_chunk($content_info, 3);
                                    $layout_data['content_info'] = $content_info;
                                    break;

                                case 'redpacket':
                                    //红包
                                    $layout_data['content_info'] = $layout_data['content'];
                                    break;
                                case 'voucher':
                                    //代金券
                                    $layout_data['content_info'] = $layout_data['content'];
                                    break;
                                default:
                                    ;
                            }
                            $item[$key]['title'] = $layout_data['title'];
                            $item[$key]['content_info'] = $layout_data['content_info'];
                            $item[$key]['content'] = $layout_data['content'];
                            $item[$key]['type'] = $layout_data['type'];
                        }
                        if($layout_data_val['wx_tpl_layout_type'] == 'activityA') {
                            $activityA['item'] = current($item);
                            $activityA['title'] = $layout_data_val['wx_tpl_layout_title'];
                            $data[$wx_tpl_layout_id + 1]['activityA'] = $activityA;
                        } else {
                            $activityB['item'] = $item;
                            $activityB['title'] = $layout_data_val['wx_tpl_layout_title'];
                            $data[$wx_tpl_layout_id + 1]['activityB'] = $activityB;
                        }
                    }
                }
            }

            //猜你喜欢
            $User_FootprintModel = new User_FootprintModel();
            $favourite_goods = $User_FootprintModel->userFavorite();


            $result_data = [];
            $result_data['module_data'] = array_values($data);
            $result_data['site_logo'] = Web_ConfigModel::value("setting_logo");
            $result_data['sub_site_id'] = $sub_site_id;
            $result_data['subsite_is_open'] = $subsite_is_open;
            $result_data['favourite_goods'] = $favourite_goods;
            if (isset($sub_site_name)) {
                $result_data['sub_site_name'] = $sub_site_name;
            } else {
                $result_data['sub_site_name'] = '';
            }
            //小程序商城logo
            $mall_logo = Web_ConfigModel::value('mall_logo');
            $result_data['mall_logo'] = $mall_logo;
            return $this->data->addBody(-140, $result_data);
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
            $this->userInfo['plus'] = Perm::$plus;
            fb($this->userInfo);
        }
        include $this->view->getView();
        if (Perm::checkUserPerm()) {
            $data[3] = true;
            $data['user_account'] = $this->userInfo['user_name'];
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
        $search_shop_words = explode(',', Web_ConfigModel::value('search_shop_words'));
        $data['hot_shop_info']["name"] = $search_shop_words[0];
        $data['hot_shop_info']["value"] = $search_shop_words[0];
        $this->data->addBody(-140, $data);
    }
    
    public function getSearchKeyList()
    {
        $search_words = array_filter(explode(',', Web_ConfigModel::value('search_words')));
        $search_words = array_values($search_words);
        $data['his_list'] = [$search_words[1]];
        $data['default_list'] = implode('', $search_words);
        // 店铺默认关键词
        $search_shop_words = array_filter(explode(',', Web_ConfigModel::value('search_shop_words')));
        $search_shop_words = array_values($search_shop_words);
        $data['default_shop_list'] = implode('', $search_shop_words);
        $searchWordModel = new Search_WordModel();
        $search = $searchWordModel->getSearchWordList([], ['search_nums' => 'DESC'], 1, 10);
        $data['list'] = array_column($search['items'], 'search_keyword');
        $this->data->addBody(-140, $data);
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
            $this->user_money_frozen = 0;
            //会员的钱
            $key = Yf_Registry::get('shop_api_key');
            $formvars = [];
            $formvars['user_id'] = $user_id;
            $formvars['app_id'] = Yf_Registry::get('shop_app_id');
            $money_row = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Info&met=getUserResourceInfo&typ=json', Yf_Registry::get('paycenter_api_url')), $formvars);
            $userResourceModel = new User_ResourceModel();
            $user_list = $userResourceModel->getUserResource($cond_row);
            if ($money_row['status'] == '200') {
                $money = $money_row['data'];
                $this->user_money = $money['user_money'] + $money['user_recharge_card'];
                $this->user_money_frozen = $money['user_money_frozen'] + $money['user_recharge_card_frozen'];
            }
            $user_list['user_money'] = $this->user_money ? :0;
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
            $shop_list = $userFavoritesShopModel->getFavoritesShopDetail($user_id, 1, 0);
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
//
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
    
    /**
     * APP登录验证调用接口
     * 2017.3.28 hp
     *
     * return
     * [user_id] => 1
     * [user_name] => test
     * [password] => 098f6bcd4621d373cade4e832627b4f6
     * [user_state] => 1
     * [action_time] => 0
     * [action_ip] =>
     * [session_id] => 098f6bcd4621d373cade4e832627b4f6
     * [id] => 1
     * [result] => 1
     * [k] => VSRVJA01AyNfUgVvVWNVa1A3
     * [cookie] => Cn8LcQVgUScNUlBpBWVcOFE0Bi4AZVVsB2kBOgRsWAdfNls2Az5WYlRyUnFWOg85BSEDUgJtUWQOXgkpVj9RJgovCzcFR1FlDShQNQVFXDhRNAYuAHVVbAdnASMEXVgxXztbbwMy
     * cookie就是每次调用接口需要传递的k，user_id就是每次调用接口需要传递的u
     */
    public function checkApp()
    {
        //本地读取远程信息
        $key = Yf_Registry::get('ucenter_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $app_id = Yf_Registry::get('ucenter_app_id');
        $formvars = [];
        $formvars['user_name'] = request_string('user_name');
        $formvars['auto_login'] = request_string('auto_login', 'false');
        $formvars['type'] = 'json';
        $formvars['t'] = '';
        $formvars['user_password'] = request_string('user_password');
        $formvars['md5_password'] = request_string('md5_password');
        $formvars['app_id'] = $app_id;
        $url = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Login', 'login', 'json');
        $init_rs = get_url_with_encrypt($key, $url, $formvars);
        if ($init_rs['status'] == 200) {
            $check_data = [];
            $check_data['user_id'] = $init_rs['data']['user_id'];
            $check_data['u'] = $init_rs['data']['user_id'];
            $check_data['k'] = $init_rs['data']['k'];
            $check_data['app_id'] = $app_id;
            $url = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Login', 'checkLogin', 'json');
            $init_rs_check = get_url_with_encrypt($key, $url, $check_data);
            if (200 == $init_rs_check['status']) {
                //读取服务列表
                $user_row = $init_rs_check['data'];
                $user_id = $user_row['user_id'];
                $user_name = $user_row['user_name'];
                $User_BaseModel = new User_BaseModel();
                $User_InfoModel = new User_InfoModel();
                $Points_LogModel = new Points_LogModel();
                //本地数据校验登录
                $user_row = $User_BaseModel->getOne($user_id);
                if ($user_row) {
                    //判断状态是否开启
                    if ($user_row['user_delete'] == 1) {
                        $msg = __('该账户未启用，请启用后登录！');
                        if ('e' == $this->typ) {
                            location_go_back(__('初始化用户出错11!'));
                        } else {
                            return $this->data->setError($msg, []);
                        }
                    }
                } else {
                    //添加用户
                    //$data['user_id']       = $user_row['user_id']; // 用户id
                    //$data['user_account']  = $user_row['user_name']; // 用户帐号
                    $data['user_id'] = $init_rs['data']['user_id']; // 用户id
                    $data['user_account'] = $init_rs['data']['user_name']; // 用户帐号
                    $data['user_delete'] = 0; // 用户状态
                    $user_id = $User_BaseModel->addBase($data, true);
                    //判断状态是否开启
                    if (!$user_id) {
                        $msg = __('初始化用户出错22!');
                        if ('e' == $this->typ) {
                            location_go_back(__('初始化用户出错33!'));
                        } else {
                            return $this->data->setError($msg, []);
                        }
                    } else {
                        //初始化用户信息
                        $user_mobile = request_string('user_mobile',false);
                        $user_info_row = [];
                        if (@$init_rs['data']['mobile']) {
                            $user_mobile = @$init_rs['data']['mobile'];
                        }
                        $user_info_row['user_id'] = $user_id;
                        $user_info_row['user_realname'] = @$init_rs['data']['user_truename'];
                        $user_info_row['user_name'] = isset($init_rs['data']['nickname']) && $init_rs['data']['nickname'] != '' ? $init_rs['data']['nickname']:$data['user_account'];
                        $user_info_row['user_mobile'] = $user_mobile;
                        $user_info_row['area_code'] = @$init_rs['data']['area_code'] ? :86;
                        $user_info_row['user_logo'] = @$init_rs['data']['user_avatar'];
                        $user_info_row['user_regtime'] = get_date_time();
                        $User_InfoModel = new User_InfoModel();

                        if (Web_ConfigModel::value('Plugin_Directseller')) {
                            $rec = request_string('rec');
                            $b= (strpos($rec,"u"));
                            $e= (strpos($rec,"s"));
                            $user_parent_id = substr($rec,$b+1,$e-1);
                            $user_info_row['user_parent_id'] = $user_parent_id;
                            $flag=$User_InfoModel->editBase($user_parent_id,array('subordinate_num'=>1),true);
                            if($flag){
                                $DistributionShop= new Distribution_DistributionShop();
                                $subordinate=$User_InfoModel->getOne($user_parent_id);
                                if((int)$subordinate['subordinate_num']>=(int)Web_ConfigModel::value('distribution_invitations')){
                                    $User_InfoModel->editInfo($user_parent_id,array('distributor_type'=>1));
                                    $time=time();
                                    $images=Yf_Registry::get('shop_api_url').'shop/static/default/images/Bitmap.png';
                                    $DistributionShop->addBase(array('user_id'=>$user_parent_id,'distribution_name'=>$subordinate['user_name']."的小店",'distribution_logo'=>Yf_Registry::get('shop_api_url').'shop/static/default/images/Bitmap.png','add_time'=>time()));
                                }
                            }
                            // $PluginManager = Yf_Plugin_Manager::getInstance();
                            // $PluginManager->trigger('regDone', $user_id);
                        }
                        $info_flag = $User_InfoModel->addInfo($user_info_row);
                        $user_resource_row = [];
                        $user_resource_row['user_id'] = $user_id;
                        $user_resource_row['user_points'] = Web_ConfigModel::value("points_reg");//注册获取积分;
                        $User_ResourceModel = new User_ResourceModel();
                        $res_flag = $User_ResourceModel->addResource($user_resource_row);
                        $User_PrivacyModel = new User_PrivacyModel();
                        $user_privacy_row['user_id'] = $user_id;
                        $privacy_flag = $User_PrivacyModel->addPrivacy($user_privacy_row);
                        //积分
                        $user_points_row['user_id'] = $user_id;
                        $user_points_row['user_name'] = $data['user_account'];
                        $user_points_row['class_id'] = Points_LogModel::ONREG;
                        $user_points_row['points_log_points'] = $user_resource_row['user_points'];
                        $user_points_row['points_log_time'] = get_date_time();
                        $user_points_row['points_log_desc'] = __('会员注册');
                        $user_points_row['points_log_flag'] = 'reg';
                        $Points_LogModel->addLog($user_points_row);
                        //发送站内信
                        $message = new MessageModel();
                        $message->sendMessage('welcome', $user_id, $data['user_account'], '', '', 0, MessageModel::OTHER_MESSAGE);
                        /**
                         *  统计中心
                         * shop的注册人数
                         */
                        $analytics_ip = isset($init_rs['data']['user_reg_ip']) ? $init_rs['data']['user_reg_ip']:get_ip();
                        $analytics_data = [
                            'user_name' => $data['user_account'],  //用户账号
                            'user_id' => $user_id,
                            'ip' => $analytics_ip,
                            'date' => date('Y-m-d H:i:s')
                        ];
                        Yf_Plugin_Manager::getInstance()->trigger('analyticsMemberAdd', $analytics_data);
                        /******************************************************/
                    }
                    $user_row = $data;
                }
                if ($user_row) {
                    $data = [];
                    $data['user_id'] = $user_row['user_id'];
                    srand((double)microtime() * 1000000);
                    //$user_key = md5(rand(0, 32000));
                    $user_key = $init_rs['data']['session_id'];
                    $time = get_date_time();
                    //获取上次登录的时间
                    $info = $User_BaseModel->getBase($user_row['user_id']);
                    $lotime = strtotime($info[$user_row['user_id']]['user_login_time']);
                    $last_day = date("d ", $lotime);
                    $now_day = date("d ");
                    $now = time();
                    $login_info_row = [];
                    $login_info_row['user_key'] = $user_key;
                    $login_info_row['user_login_time'] = $time;
                    $login_info_row['user_login_times'] = $info[$user_row['user_id']]['user_login_times'] + 1;
                    $login_info_row['user_login_ip'] = get_ip();
                    $flag = $User_BaseModel->editBase($user_row['user_id'], $login_info_row, false);
                    $login_row['user_logintime'] = $time;
                    $login_row['lastlogintime'] = $info[$user_row['user_id']]['user_login_time'];
                    $login_row['user_ip'] = get_ip();
                    $login_row['user_lastip'] = $info[$user_row['user_id']]['user_login_ip'];
                    $flag = $User_InfoModel->editInfo($user_row['user_id'], $login_row, false);
                    //当天没有登录过执行
                    if ($last_day != $now_day && $now > $lotime) {
                        $user_points = Web_ConfigModel::value("points_login");
                        $user_grade = Web_ConfigModel::value("grade_login");
                        $User_ResourceModel = new User_ResourceModel();
                        //获取当前登录的积分经验值
                        $ce = $User_ResourceModel->getResource($user_row['user_id']);
                        $resource_row['user_points'] = $ce[$user_row['user_id']]['user_points'] * 1 + $user_points * 1;
                        $resource_row['user_growth'] = $ce[$user_row['user_id']]['user_growth'] * 1 + $user_grade * 1;
                        $res_flag = $User_ResourceModel->editResource($user_row['user_id'], $resource_row);
                        $User_GradeModel = new User_GradeModel;
                        //升级判断
                        $res_flag = $User_GradeModel->upGrade($user_row['user_id'], $resource_row['user_growth']);
                        //积分
                        $points_row['user_id'] = $user_id;
                        $points_row['user_name'] = $user_row['user_account'];
                        $points_row['class_id'] = Points_LogModel::ONLOGIN;
                        $points_row['points_log_points'] = $user_points;
                        $points_row['points_log_time'] = $time;
                        $points_row['points_log_desc'] = __('会员登录');
                        $points_row['points_log_flag'] = 'login';
                        $Points_LogModel = new Points_LogModel();
                        $Points_LogModel->addLog($points_row);
                        //成长值
                        $grade_row['user_id'] = $user_id;
                        $grade_row['user_name'] = $user_row['user_account'];
                        $grade_row['class_id'] = Grade_LogModel::ONLOGIN;
                        $grade_row['grade_log_grade'] = $user_grade;
                        $grade_row['grade_log_time'] = $time;
                        $grade_row['grade_log_desc'] = __('会员登录');
                        $grade_row['grade_log_flag'] = 'login';
                        $Grade_LogModel = new Grade_LogModel;
                        $Grade_LogModel->addLog($grade_row);
                    }
                    //$flag     = $User_BaseModel->editBaseSingleField($user_row['user_id'], 'user_key', $user_key, $user_row['user_key']);
                    Yf_Hash::setKey($user_key);
                    //
                    $Seller_BaseModel = new Seller_BaseModel();
                    $seller_rows = $Seller_BaseModel->getByWhere(['user_id' => $data['user_id']]);
                    $Chain_UserModel = new Chain_UserModel();
                    $chain_rows = $Chain_UserModel->getByWhere(['user_id' => $data['user_id']]);
                    if ($chain_rows) {
                        $data['chain_id_row'] = array_column($chain_rows, 'chain_id');
                        $data['chain_id'] = current($data['chain_id_row']);
                    } else {
                        $data['chain_id'] = 0;
                    }
                    if ($seller_rows) {
                        $data['shop_id_row'] = array_column($seller_rows, 'shop_id');
                        $data['shop_id'] = current($data['shop_id_row']);
                    } else {
                        $data['shop_id'] = 0;
                    }
                    
                    $data['user_account'] = $user_row['user_account'];
                    
                    $encrypt_str = Perm::encryptUserInfo($data);
                    //更新购物车
                    $cartlist = [];
                    if (isset($_COOKIE['goods_cart'])) {
                        $cartlist = $_COOKIE['goods_cart'];
                    }
                    if ($cartlist) {
                        $CartModel = new CartModel();
                        $CartModel->updateCookieCart($data['user_id']);
                    }
                    if (isset($_COOKIE['goods_cart'])) {
                        setcookie("goods_cart", null, time() - 1, '/');
                    }
                    $data = [];
                    $data['user_id'] = $user_row['user_id'];
                    $data['user_account'] = $user_row['user_account'];
                    $data['key'] = $encrypt_str;
                    $init_rs['data']['cookie'] = $encrypt_str;
                    $this->data->addBody(100, $init_rs['data']);
                } else {
                    $msg = __('账号或密码错误');
                    if ('e' == $this->typ) {
                        location_go_back($msg);
                    } else {
                        return $this->data->setError($msg, []);
                    }
                }
            } else {
                $msg = __('账号或密码错误');
                if ('e' == $this->typ) {
                    location_go_back($msg);
                } else {
                    return $this->data->setError($msg, []);
                }
            }
        } else {
            $msg = '账号或密码错误';
            $status = 250;
            $data = [];
            $this->data->addBody(-140, $data, $msg, $status);
        }
    }
    
    /*
     * 小程序关联用户注册paycenter
     * */
    public function wxappcheckApp()
    {
        //本地读取远程信息
        $key = Yf_Registry::get('paycenter_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $app_id = Yf_Registry::get('paycenter_app_id');
        $formvars = [];
        $formvars['us'] = request_int('us');
        $formvars['ks'] = request_string('ks');
        $formvars['app_id'] = $app_id;
        $url = sprintf('%s?ctl=%s&met=%s&typ=%s', $url, 'Login', 'check', 'json');
        $init_rs = get_url_with_encrypt($key, $url, $formvars);
        if ($init_rs['status'] == 200) {
            return $this->data->setError($init_rs['msg'], $init_rs);
        } else {
            $msg = __('账号或密码错误');
            
            return $this->data->setError($msg, []);
        }
    }
    
    //
    public function fastLogin()
    {
        //从ucenter中获取互联登录的设置
        $key = Yf_Registry::get('ucenter_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
        $formvars = [];
        $formvars['app_id'] = $ucenter_app_id;
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Config&met=connect&typ=json', $url), $formvars);
        $qq_status = 0;
        $wx_status = 0;
        $wb_status = 0;
        if ($rs['status'] == 200) {
//                $qq_status = $rs['data']['qq_status']['config_value'];
//                $wx_status = $rs['data']['weixin_status']['config_value'];
//                $wb_status = $rs['data']['weibo_status']['config_value'];
            $qq_status = $rs['data']['qq']['status'];
            $wx_status = $rs['data']['weixin']['status'];
            $wb_status = $rs['data']['weibo']['status'];
        }
        /*$callbacl_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $qq_url = sprintf('%s?ctl=Connect_Qq&met=login&callback=%s&from=%s', Yf_Registry::get('ucenter_api_url'), urlencode($callbacl_url) ,'shop');
        $wx_url = sprintf('%s?ctl=Connect_Weixin&met=login&callback=%s&from=%s', Yf_Registry::get('ucenter_api_url'), urlencode($callbacl_url) ,'shop');
        $wb_url = sprintf('%s?ctl=Connect_Weibo&met=login&callback=%s&from=%s', Yf_Registry::get('ucenter_api_url'), urlencode($callbacl_url) ,'shop');*/
        include $view = $this->view->getView();
    }
    
    /*
     * 获取购物车数据
     */
    public function getCart()
    {
        $user_id = Perm::$userId;
        $cord_row = [];
        $order_row = [];
        $cond_row = ['user_id' => $user_id];
        $CartModel = new CartModel();
        $cart_list = $CartModel->getCardList($cond_row, $order_row);
        $cart_count = $cart_list['count'];
        if ($cart_count > 0) {
            $cart_goods_list = []; //需要渲染页面数据
            unset($cart_list['count']);
            unset($cart_list['cart_count']);
            //cart_goods_list[cart_id] = [goods_id, goods_name, now_price...];
            foreach ($cart_list as $store) {
                foreach ($store['goods'] as $goods) {
                    $cart_id = $goods['cart_id'];
                    $goods_data = $goods['goods_base'];
                    empty($goods_data['goods_spec'])
                        ? $goods_name = $goods_data['goods_name']
                        :$goods_name = $goods_data['goods_name'] . sprintf('(%s)', implode(',', current($goods_data['goods_spec'])));
                    $cart_goods_list[$cart_id] = [
                        'cart_id' => $cart_id,
                        'goods_id' => $goods_data['goods_id'],
                        'goods_image' => $goods_data['goods_image'],
                        'goods_num' => $goods['goods_num'],
                        'now_price' => $goods['now_price'],
                        'goods_name' => $goods_name,
                    ];
                }
            }
            rsort($cart_goods_list, SORT_NUMERIC);
        }
        $this->view->setMet("drop_down_cart", "..");
        include $this->view->getView();
    }
    //发现模块显示权限设置
   public function findAuthor()
    {

        $Shop_BaseModel = new Shop_BaseModel();
        $sql = "
            SELECT *
            FROM `yf_web_config`
            where
            config_key  = 'exploreset'
        ";
        $db = new YFSQL();
        $list = $db->find($sql);
        $shop_id_wap = request_int('shop_id_wap');
        if($shop_id_wap){
            $first_url =Yf_Registry::get('shop_wap_url')."tmpl/store.html?shop_id=$shop_id_wap&level=1";
        }else{
            $first_url = Yf_Registry::get('shop_wap_url').'index.html';
        }
        $setCat = Web_ConfigModel::value('setWapCat');
        if ($setCat != 2) {
            $cat_url = Yf_Registry::get('shop_wap_url') . 'tmpl/product_first_categroy.html';
        } else {
            $cat_url = Yf_Registry::get('shop_wap_url') . 'tmpl/product_first_categroy2.html';
        }

        if (!empty($list) && $list[0]['config_value'] == 0) {
            $shop_base = $Shop_BaseModel->getOne($shop_id_wap);
            if($shop_id_wap){
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>$first_url],
                    '1'=>['type'=>'icon-kefu1','type_active'=>'icon-kefu1-active','name'=>'客服','url'=>Yf_Registry::get('im_url').'/wap?to_kefu=1&shop_name='.$shop_base['shop_name'].'&shop_logo='.$shop_base['shop_logo'].'&seller_name='.$shop_base['user_name']],
                    '2'=>['type'=>'icon-find','type_active'=>'icon-find-active','name'=>'订单','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/order_list.html'],
                    '3'=>['type'=>'icon-cart','type_active'=>'icon-cart-active','name'=>'购物车','url'=>Yf_Registry::get('shop_wap_url').'tmpl/cart_list.html'],
                    '4'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'我的','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/member.html'],
                ];
            }else{
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>$first_url],
                    '1'=>['type'=>'icon-class1','type_active'=>'icon-class1-active','name'=>'分类','url'=>$cat_url],
                    '2'=>['type'=>'icon-cart','type_active'=>'icon-cart-active','name'=>'购物车','url'=>Yf_Registry::get('shop_wap_url').'tmpl/cart_list.html'],
                    '3'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'我的','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/member.html'],
                ];
            }
        } else {
            $shop_base = $Shop_BaseModel->getOne($shop_id_wap);
            if($shop_id_wap){
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>$first_url],
                    '1'=>['type'=>'icon-kefu1','type_active'=>'icon-kefu1-active','name'=>'客服','url'=>Yf_Registry::get('im_url').'/wap?to_kefu=1&shop_name='.$shop_base['shop_name'].'&shop_logo='.$shop_base['shop_logo'].'&seller_name='.$shop_base['user_name']],
                    '2'=>['type'=>'icon-find','type_active'=>'icon-find-active','name'=>'订单','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/order_list.html'],
                    '3'=>['type'=>'icon-cart','type_active'=>'icon-cart-active','name'=>'购物车','url'=>Yf_Registry::get('shop_wap_url').'tmpl/cart_list.html'],
                    '4'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'我的','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/member.html'],
                ];
            }else{
                $footer_menu = [
                    '0'=>['type'=>'icon-home','type_active'=>'icon-home-active','name'=>'首页','url'=>$first_url],
                    '1'=>['type'=>'icon-class1','type_active'=>'icon-class1-active','name'=>'分类','url'=>$cat_url],
                    '2'=>['type'=>'icon-find','type_active'=>'icon-find-active','name'=>'发现','url'=>Yf_Registry::get('shop_wap_url').'tmpl/explore_list.html'],
                    '3'=>['type'=>'icon-cart','type_active'=>'icon-cart-active','name'=>'购物车','url'=>Yf_Registry::get('shop_wap_url').'tmpl/cart_list.html'],
                    '4'=>['type'=>'icon-mine','type_active'=>'icon-mine-active','name'=>'我的','url'=>Yf_Registry::get('shop_wap_url').'tmpl/member/member.html'],
                ];
            }
            
        }
        $data = $footer_menu;
        if ('json' == $this->typ)
        {
            $this->data->addBody(-140, $data);
        }
    }
    //手机端首页请求大量数据太慢，进行优化，此作为备份
//     public function index()
//     {
//         if ('json' == $this->typ) {

//             if(request_string('ua') == 'wap')
//             {
//                 $site_status = Web_ConfigModel::value("site_status_wap");
//                 if(!$site_status)
//                 {
//                     $msg = Web_ConfigModel::value("closed_reason_wap");
//                     $status = 250;
//                     $data = [];

//                     return $this->data->addBody(-140, $data, $msg, $status);
//                 }
//             }

//             $data = [];
//             // $data[] = array();
//             $goods_CommonModel = new Goods_CommonModel();
//             $mbTplLayoutModel = new Mb_TplLayoutModel();
//             $subsite_is_open = Web_ConfigModel::value("subsite_is_open");
//             if ($subsite_is_open == Sub_SiteModel::SUB_SITE_IS_OPEN) {
//                 $sub_site_id = request_int('sub_site_id');
//                 $subsite_is_open = 1;  //开启
//                 if ($sub_site_id > 0) {
//                     $sub_site_model = new Sub_Site();
//                     $sub_site_info = $sub_site_model->getSubSite($sub_site_id);
//                     $sub_site_name = $sub_site_info[$sub_site_id]['sub_site_name'];
//                 }
//             } else {
//                 $subsite_is_open = 0; //关闭
//                 $sub_site_id = 0;
//             }
//             $layout_list = $mbTplLayoutModel->getByWhere(['mb_tpl_layout_enable' => Mb_TplLayoutModel::USABLE, 'sub_site_id' => $sub_site_id], ['mb_tpl_layout_order' => 'ASC']);
//             if (!empty($layout_list)) {
//                 foreach ($layout_list as $mb_tpl_layout_id => $layout_data_val) {
//                     if ($layout_data_val['mb_tpl_layout_type'] == 'adv_list') {
//                         $adv_list = $layout_data_val;
//                         //头部滚动条
//                         $slide_rows = isset($adv_list['mb_tpl_layout_data']) ? $adv_list['mb_tpl_layout_data']:[];
//                         $slide_items = [];
//                         foreach ($slide_rows as $s_k => $s_v) {
//                             $item = [];
//                             $item['image'] = $s_v['image'];
//                             $item['type'] = $s_v['image_type'];
//                             $item['data'] = $s_v['image_data'];
//                             // $item['link']  = $s_v['image_data'];
//                             $slide_items[] = $item;
//                         }
//                         if (!empty($slide_items)) {
//                             $data[$mb_tpl_layout_id + 1]['slider_list']['item'] = $slide_items;
//                         }
//                     }
//                     if ($layout_data_val['mb_tpl_layout_type'] == 'home1') {
//                         $hom1 = [];
//                         $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
//                         $hom1['title'] = $layout_data_val['mb_tpl_layout_title'];
//                         $hom1['image'] = $mb_tpl_layout_data['image'];
//                         $hom1['type'] = $mb_tpl_layout_data['image_type'];
//                         $hom1['data'] = $mb_tpl_layout_data['image_data'];
//                         $data[$mb_tpl_layout_id + 1]['home1'] = $hom1;
//                     }
//                     if ($layout_data_val['mb_tpl_layout_type'] == 'home2' || $layout_data_val['mb_tpl_layout_type'] == 'home4') {
//                         $home2_4 = [];
//                         $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
//                         $home2_4['title'] = $layout_data_val['mb_tpl_layout_title'];
//                         $home2_4['rectangle1_image'] = $mb_tpl_layout_data['rectangle1']['image'];
//                         $home2_4['rectangle1_type'] = $mb_tpl_layout_data['rectangle1']['image_type'];
//                         $home2_4['rectangle1_data'] = $mb_tpl_layout_data['rectangle1']['image_data'];
//                         $home2_4['rectangle2_image'] = $mb_tpl_layout_data['rectangle2']['image'];
//                         $home2_4['rectangle2_type'] = $mb_tpl_layout_data['rectangle2']['image_type'];
//                         $home2_4['rectangle2_data'] = $mb_tpl_layout_data['rectangle2']['image_data'];
//                         $home2_4['square_image'] = $mb_tpl_layout_data['square']['image'];
//                         $home2_4['square_type'] = $mb_tpl_layout_data['square']['image_type'];
//                         $home2_4['square_data'] = $mb_tpl_layout_data['square']['image_data'];
//                         $data[$mb_tpl_layout_id + 1][$layout_data_val['mb_tpl_layout_type']] = $home2_4;
//                     }
//                     if ($layout_data_val['mb_tpl_layout_type'] == 'home3') {
//                         $home3 = [];
//                         $item = [];
//                         $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
//                         foreach ($mb_tpl_layout_data as $key => $layout_data) {
//                             $item[$key]['image'] = $layout_data['image'];
//                             $item[$key]['type'] = $layout_data['image_type'];
//                             $item[$key]['data'] = $layout_data['image_data'];
//                         }
//                         $home3['item'] = $item;
//                         $home3['title'] = $layout_data_val['mb_tpl_layout_title'];
//                         $data[$mb_tpl_layout_id + 1]['home3'] = $home3;
//                     }
//                     if ($layout_data_val['mb_tpl_layout_type'] == 'goods' || $layout_data_val['mb_tpl_layout_type'] == 'goodsB') {
//                         if($layout_data_val['mb_tpl_layout_type'] == 'goods') {
//                             $goods = [];
//                         } else {
//                             $goodsB = [];
//                         }
//                         $item = [];
//                         $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
//                         if (request_string('type_wxapp') == 'wxapp') {
//                             $common_list = $goods_CommonModel->getByWhere(['common_id:IN' => $mb_tpl_layout_data, 'common_is_tuan' => 0]);
//                         } else {
//                             $common_list = $goods_CommonModel->getByWhere(['common_id:IN' => $mb_tpl_layout_data]);
//                         }
//                         if ($common_list) {
//                             foreach ($common_list as $common_id => $common_data) {
//                                 $goods_id = pos($common_data['goods_id']);
//                                 if (is_array($goods_id)) {
//                                     $goods_id = pos($goods_id);
//                                 }
//                                 $item[$common_id]['goods_id'] = $goods_id;
//                                 $item[$common_id]['goods_name'] = $common_data['common_name'];
//                                 $item[$common_id]['goods_promotion_price'] = $common_data['common_price'];
//                                 $item[$common_id]['goods_image'] = image_thumb($common_data['common_image'], 360);
//                                 $item[$common_id]['goods_salenum'] = $common_data['common_salenum'];
//                                 $item[$common_id]['goods_evaluation_count'] = $common_data['common_evaluate'];
//                             }
//                             if($layout_data_val['mb_tpl_layout_type'] == 'goods') {
//                                 $goods['item'] = array_values($item);
//                                 $goods['title'] = $layout_data_val['mb_tpl_layout_title'];
//                                 $data[$mb_tpl_layout_id + 1]['goods'] = $goods;
//                             } else {
//                                 $goodsB['item'] = array_values($item);
//                                 $goodsB['title'] = $layout_data_val['mb_tpl_layout_title'];
//                                 $data[$mb_tpl_layout_id + 1]['goodsB'] = $goodsB;
//                             }
//                         }
//                     }
//                     //快捷入口
//                     if($layout_data_val['mb_tpl_layout_type'] == 'enterance') {
//                         $enterance = [];
//                         $item = [];
//                         $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
//                         foreach ($mb_tpl_layout_data as $key => $layout_data) {
//                             $item[$key]['icons'] = $layout_data['icons'];
//                             $item[$key]['navName'] = $layout_data['navName'];
//                             $item[$key]['url'] = $layout_data['url'];
//                         }
//                         $enterance['item'] = $item;
//                         $enterance['title'] = $layout_data_val['mb_tpl_layout_title'];
//                         $data[$mb_tpl_layout_id + 1]['enterance'] = $enterance;
//                     }

//                     //广告版块A
//                     if($layout_data_val['mb_tpl_layout_type'] == 'advA') {
//                         $advA = [];
//                         $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
//                         $advA['title'] = $layout_data_val['mb_tpl_layout_title'];
//                         $advA['rectangle1_image'] = $mb_tpl_layout_data['rectangle1']['image'];
//                         $advA['rectangle1_type'] = $mb_tpl_layout_data['rectangle1']['image_type'];
//                         $advA['rectangle1_data'] = $mb_tpl_layout_data['rectangle1']['image_data'];
//                         $advA['rectangle2_image'] = $mb_tpl_layout_data['rectangle2']['image'];
//                         $advA['rectangle2_type'] = $mb_tpl_layout_data['rectangle2']['image_type'];
//                         $advA['rectangle2_data'] = $mb_tpl_layout_data['rectangle2']['image_data'];
//                         $advA['rectangle3_image'] = $mb_tpl_layout_data['rectangle3']['image'];
//                         $advA['rectangle3_type'] = $mb_tpl_layout_data['rectangle3']['image_type'];
//                         $advA['rectangle3_data'] = $mb_tpl_layout_data['rectangle3']['image_data'];
//                         $advA['rectangle4_image'] = $mb_tpl_layout_data['rectangle4']['image'];
//                         $advA['rectangle4_type'] = $mb_tpl_layout_data['rectangle4']['image_type'];
//                         $advA['rectangle4_data'] = $mb_tpl_layout_data['rectangle4']['image_data'];
//                         $advA['rectangle5_image'] = $mb_tpl_layout_data['rectangle5']['image'];
//                         $advA['rectangle5_type'] = $mb_tpl_layout_data['rectangle5']['image_type'];
//                         $advA['rectangle5_data'] = $mb_tpl_layout_data['rectangle5']['image_data'];
//                         $advA['rectangle6_image'] = $mb_tpl_layout_data['rectangle6']['image'];
//                         $advA['rectangle6_type'] = $mb_tpl_layout_data['rectangle6']['image_type'];
//                         $advA['rectangle6_data'] = $mb_tpl_layout_data['rectangle6']['image_data'];
//                         $advA['square_image'] = $mb_tpl_layout_data['square']['image'];
//                         $advA['square_type'] = $mb_tpl_layout_data['square']['image_type'];
//                         $advA['square_data'] = $mb_tpl_layout_data['square']['image_data'];
//                         $data[$mb_tpl_layout_id + 1][$layout_data_val['mb_tpl_layout_type']] = $advA;
//                     }
//                     //广告版块B
//                     if($layout_data_val['mb_tpl_layout_type'] == 'advB') {
//                         $advB = [];
//                         $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
//                         $advB['title'] = $layout_data_val['mb_tpl_layout_title'];
//                         $advB['rectangle1_image'] = $mb_tpl_layout_data['rectangle1']['image'];
//                         $advB['rectangle1_type'] = $mb_tpl_layout_data['rectangle1']['image_type'];
//                         $advB['rectangle1_data'] = $mb_tpl_layout_data['rectangle1']['image_data'];
//                         $advB['rectangle2_image'] = $mb_tpl_layout_data['rectangle2']['image'];
//                         $advB['rectangle2_type'] = $mb_tpl_layout_data['rectangle2']['image_type'];
//                         $advB['rectangle2_data'] = $mb_tpl_layout_data['rectangle2']['image_data'];
//                         $advB['rectangle3_image'] = $mb_tpl_layout_data['rectangle3']['image'];
//                         $advB['rectangle3_type'] = $mb_tpl_layout_data['rectangle3']['image_type'];
//                         $advB['rectangle3_data'] = $mb_tpl_layout_data['rectangle3']['image_data'];
//                         $advB['rectangle4_image'] = $mb_tpl_layout_data['rectangle4']['image'];
//                         $advB['rectangle4_type'] = $mb_tpl_layout_data['rectangle4']['image_type'];
//                         $advB['rectangle4_data'] = $mb_tpl_layout_data['rectangle4']['image_data'];
//                         $advB['rectangle5_image'] = $mb_tpl_layout_data['rectangle5']['image'];
//                         $advB['rectangle5_type'] = $mb_tpl_layout_data['rectangle5']['image_type'];
//                         $advB['rectangle5_data'] = $mb_tpl_layout_data['rectangle5']['image_data'];
//                         $data[$mb_tpl_layout_id + 1][$layout_data_val['mb_tpl_layout_type']] = $advB;
//                     }

//                     //活动版块AB
//                     if($layout_data_val['mb_tpl_layout_type'] == 'activityA' || $layout_data_val['mb_tpl_layout_type'] == 'activityB') {
//                         if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
//                             $activityA = [];
//                         } else {
//                             $activityB = [];
//                         }
//                         $item = [];
//                         $mb_tpl_layout_data = $layout_data_val['mb_tpl_layout_data'];
//                         foreach ($mb_tpl_layout_data as $key => $layout_data) {
//                             switch($layout_data['type']) {
//                                 case 'groupbuy':
//                                     //获取团购商品信息
//                                     $GroupBuy_BaseModel = new GroupBuy_BaseModel();
//                                     $content_info = $GroupBuy_BaseModel->getForumGroupbuy(explode(',',$layout_data['content']));
//                                     if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
//                                         $num = 12 - count($content_info);
//                                     } else {
//                                         $num = 2 - count($content_info);
//                                     }
//                                     if($num > 0) {
//                                         $Front_ForumModel = new Front_ForumModel();
//                                         $content = $Front_ForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
//                                         if($content) {
//                                             $content_info = array_merge($content_info,$content);
//                                         }
//                                     }
//                                     $layout_data['content_info'] = array_values($content_info);
//                                     break;
//                                 case 'discount':
//                                     //获取限时折扣商品信息
//                                     $Discount_GoodsModel = new Discount_GoodsModel();
//                                     $discount_row = array();
//                                     $discount_row['discount_goods_id:IN'] = explode(',', $layout_data['content']);
//                                     $discount_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
//                                     $discount_row['discount_goods_state'] = 1;
//                                     $content = $Discount_GoodsModel->getDiscountGoods($discount_row);
//                                     $content_info = array_values($content);
//                                     if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
//                                         $num = 12 - count($content_info);
//                                     } else {
//                                         $num = 3 - count($content_info);
//                                     }
//                                     if($num > 0) {
//                                         $Front_ForumModel = new Front_ForumModel();
//                                         $content = $Front_ForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
//                                         if($content) {
//                                             $content_info = array_merge($content_info,$content);
//                                         }
//                                     }
//                                     $layout_data['content_info'] = array_values($content_info);
//                                     break;
//                                 case 'pintuan':
//                                     //拼团活动商品
//                                     $PinTuan_Base = new PinTuan_Base();
//                                     $cond_row = array();
//                                     $cond_row['id:IN'] = explode(',',$layout_data['content']);
//                                     $cond_row['status'] = 1;//拼团有效
//                                     $cond_row['start_time:<'] = date('Y-m-d H:i:s');
//                                     $cond_row['end_time:>'] = date('Y-m-d H:i:s');
//                                     $content_info = $PinTuan_Base->getTplPinTuanGoods($cond_row);
//                                     if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
//                                         $num = 12 - count($content_info);
//                                     } else {
//                                         $num = 2 - count($content_info);
//                                     }
//                                     if($num > 0) {
//                                         $Front_ForumModel = new Front_ForumModel();
//                                         $content = $Front_ForumModel->addOpenForumContent($layout_data['type'],explode(',',$layout_data['content']),$num);
//                                         if($content) {
//                                             $content_info = array_merge($content_info,$content);
//                                         }
//                                     }
//                                     $layout_data['content_info'] = array_values($content_info);
//                                     break;
//                                 case 'redpacket':
//                                     //红包
//                                     $layout_data['content_info'] = $layout_data['content'];
//                                     break;
//                                 case 'voucher':
//                                     //代金券
//                                     $layout_data['content_info'] = $layout_data['content'];
//                                     break;
//                                 default:
//                                     ;
//                             }
//                             $item[$key]['title'] = $layout_data['title'];
//                             $item[$key]['content_info'] = $layout_data['content_info'];
//                             $item[$key]['content'] = $layout_data['content'];
//                             $item[$key]['type'] = $layout_data['type'];
//                         }
//                         if($layout_data_val['mb_tpl_layout_type'] == 'activityA') {
//                             $activityA['item'] = current($item);
//                             $activityA['title'] = $layout_data_val['mb_tpl_layout_title'];
//                             $data[$mb_tpl_layout_id + 1]['activityA'] = $activityA;
//                         } else {
//                             $activityB['item'] = $item;
//                             $activityB['title'] = $layout_data_val['mb_tpl_layout_title'];
//                             $data[$mb_tpl_layout_id + 1]['activityB'] = $activityB;
//                         }
//                     }
//                 }
//             }

//             //猜你喜欢
//             $User_FootprintModel = new User_FootprintModel();
//             $favourite_goods = $User_FootprintModel->userFavorite();

//             //头部滚动条
// //                 $slide_rows = isset($adv_list['mb_tpl_layout_data']) ? $adv_list['mb_tpl_layout_data'] : array();
// //                 $slide_items = array();
// //                 foreach ($slide_rows as $s_k => $s_v) {
// //                     $item = array();
// //                     $item['image'] = $s_v['image'];
// //                     $item['type'] = $s_v['image_type'];
// //                     $item['data'] = $s_v['image_data'];
// // //               $item['link']  = $s_v['image_data'];
// //                     $slide_items[] = $item;
// //                 }
// //                 if (!empty($slide_items)) {
// //                     $data[0]['slider_list']['item'] = $slide_items;
// //                 }
//             $result_data = [];
//             $result_data['module_data'] = array_values($data);
//             $result_data['site_logo'] = Web_ConfigModel::value("setting_logo");
//             $result_data['sub_site_id'] = $sub_site_id;
//             $result_data['subsite_is_open'] = $subsite_is_open;
//             $result_data['favourite_goods'] = $favourite_goods;

//             //SEO设置
//             $result_data['title'] = Web_ConfigModel::value('title')? Web_ConfigModel::value('title'): Web_ConfigModel::value('site_name');
//             $result_data['keyword'] = Web_ConfigModel::value('keyword');
//             $result_data['description'] = Web_ConfigModel::value('description');
//             if (isset($sub_site_name)) {
//                 $result_data['sub_site_name'] = $sub_site_name;
//             } else {
//                 $result_data['sub_site_name'] = '';
//             }

//             return $this->data->addBody(-140, $result_data);
//         } else {
//             $Cache = Yf_Cache::create('default');
//             $site_index_key = sprintf('%s|%s|%s', Yf_Registry::get('server_id'), 'site_index', isset($_COOKIE['sub_site_id']) ? $_COOKIE['sub_site_id']:0);
//             if (!$Cache->start($site_index_key)) {
//                 $this->initData();
//                 $subsite_is_open = Web_ConfigModel::value("subsite_is_open");
//                 if (!empty($_COOKIE['sub_site_id']) && $subsite_is_open == Sub_SiteModel::SUB_SITE_IS_OPEN) {
//                     $sub_site_id = $_COOKIE['sub_site_id'];
//                 } else {
//                     $sub_site_id = 0;
//                 }
//                 //团购风暴
//                 $GroupBuy_BaseModel = new GroupBuy_BaseModel;
//                 //先判断首页推荐的团购是否超过5个，如果超过则只显示首页推荐团购，如果不超过则只显示包括推荐团购在内的5个团购
//                 //查找推荐团购的个数
//                 $groupbuy_recommend = $GroupBuy_BaseModel->getByWhere(['groupbuy_state' => GroupBuy_BaseModel::NORMAL, 'groupbuy_recommend' => GroupBuy_BaseModel::RECOMMEND]);
//                 $groupbuy_count = count($groupbuy_recommend);
//                 $gb_goods_list = [];
//                 if ($groupbuy_count < 5) {
//                     $cond_row = [
//                         "groupbuy_starttime:<=" => get_date_time(),
//                         "groupbuy_endtime :>=" => get_date_time(),
//                         "groupbuy_state" => GroupBuy_BaseModel::NORMAL,
//                     ];
//                     $order_row = ["groupbuy_recommend" => "desc"];
//                     $gb_goods_list = $GroupBuy_BaseModel->getGroupBuyGoodsList($cond_row, $order_row, 1, 5, $sub_site_id);
//                 } else {
//                     $cond_row = [
//                         "groupbuy_starttime:<=" => get_date_time(),
//                         "groupbuy_endtime :>=" => get_date_time(),
//                         "groupbuy_state" => GroupBuy_BaseModel::NORMAL,
//                         'groupbuy_recommend' => GroupBuy_BaseModel::RECOMMEND,
//                     ];
//                     $order_row = [];
//                     $gb_goods_list = $GroupBuy_BaseModel->getGroupBuyGoodsList($cond_row, $order_row, 1, 15, $sub_site_id);
//                 }
//                 //楼层设置
//                 $Adv_PageSettingsModel = new Adv_PageSettingsModel();
//                 $subsite_is_open = Web_ConfigModel::value("subsite_is_open");
//                 if ($sub_site_id) {
//                     $cond_adv_row['sub_site_id'] = $_COOKIE['sub_site_id'];
//                     //首页标题关键字
//                     $Sub_Site = new Sub_Site();
//                     $sub_site_info = $Sub_Site->getSubSite($_COOKIE['sub_site_id']);
//                     $title = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_title'];//首页名;
//                     $this->keyword = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_keyword'];//关键字;
//                     $this->description = $sub_site_info[$_COOKIE['sub_site_id']]['sub_site_web_des'];//描述;
//                     $this->title = str_replace("{sitename}", $this->web['web_name'], $title);
//                     $this->keyword = str_replace("{sitename}", $this->web['web_name'], $this->keyword);
//                     $this->description = str_replace("{sitename}", $this->web['web_name'], $this->description);
//                 } else {
//                     $cond_adv_row['sub_site_id'] = 0;
//                     //首页标题关键字
//                     $title = Web_ConfigModel::value("title");//首页名;
//                     $this->keyword = Web_ConfigModel::value("keyword");//关键字;
//                     $this->description = Web_ConfigModel::value("description");//描述;
//                     $this->title = str_replace("{sitename}", $this->web['web_name'], $title);
//                     $this->keyword = str_replace("{sitename}", $this->web['web_name'], $this->keyword);
//                     $this->description = str_replace("{sitename}", $this->web['web_name'], $this->description);
//                 }
//                 $cond_adv_row['page_status'] = 1;
//                 $order_adv_row = ["page_order" => "asc"];
//                 $adv_list = $Adv_PageSettingsModel->listByWhere($cond_adv_row, $order_adv_row);

//                 //后台首页模板
//                 $Front_ForumModel = new Front_ForumModel();
//                 $forum = $Front_ForumModel->getOpenForumContent();

//                 //查找HTML
//                 $forum_html = $Front_ForumModel->getForumHtml($forum);

//                 //猜你喜欢
//                 $User_FootprintModel = new User_FootprintModel();
//                 $favourite_goods = $User_FootprintModel->userFavorite();


//                 include $this->view->getView();
//                 $Cache->_id = $site_index_key;
//                 $Cache->end($site_index_key);
//             }
//         }
//     }
    /**
     *
     *保存客户端ip
     * @nsy 2019-02-20
     */
    public function setClientIpByCookie(){
        $ip = request_string('ip');
        if ($ip && !isset($_COOKIE['client_ip_address'])) {
            setcookie("client_ip_address", $ip);
        }
        //exit(json_encode(array('ip'=>$_COOKIE['client_ip_address'])));
    }

    //获取用户未登录情况下的用户默认头像
    public function getUserLogo()
    {
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        $res = get_url_with_encrypt($key, sprintf('%s?ctl=Index&met=getUserAvatar&typ=json', $url), $formvars);
        $data['image'] = $res['data']['image'];
        $this->data->addBody(-140, $data);
    }

    public function getMallLogo(){
        $data['mall_logo']=Web_ConfigModel::value('mall_logo');
        $this->data->addBody(-140, $data);
    }
}

?>