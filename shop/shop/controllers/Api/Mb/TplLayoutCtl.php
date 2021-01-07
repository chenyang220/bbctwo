<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_Mb_TplLayoutCtl extends Api_Controller
{
    public $mbTplLayoutModel;

    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

        $this->mbTplLayoutModel = new Mb_TplLayoutModel();
    }

    public function tplLayoutList()
    {
        $sub_site_id = request_int('sub_site_id', 0);
        $cond_row['sub_site_id'] = $sub_site_id;
        $mb_tpl_layout_id = request_int('mb_tpl_layout_id');
        $tpl_layout_style = request_int('tpl_layout_style', 1);
        $cond_row['tpl_layout_style'] = $tpl_layout_style;
        if($mb_tpl_layout_id) {
            $cond_row['mb_tpl_layout_id'] = $mb_tpl_layout_id;
        }
        $layout_list = $this->mbTplLayoutModel->getByWhere( $cond_row, array('mb_tpl_layout_order' => 'ASC'));
        $data = array();

        if ( is_array($layout_list) )
        {
            //如果为goods类型，则取出对应商品信息
            if ( !empty($layout_list) )
            {
                $goodsCommonModel = new Goods_CommonModel();
                foreach($layout_list as $item_id => $item_data )
                {
                    if ($item_data['mb_tpl_layout_type'] == 'goods' || $item_data['mb_tpl_layout_type'] == 'goodsB'|| $item_data['mb_tpl_layout_type'] == 'goodsC' || $item_data['mb_tpl_layout_type'] == 'home5' || $item_data['mb_tpl_layout_type'] == 'newGoods')
                    {
                        if ($item_data['mb_tpl_layout_type'] == 'home5' || $item_data['mb_tpl_layout_type'] == 'newGoods') {
                            $common_ids = $item_data['mb_tpl_layout_data']['goods_ids'];
                        } else {
                            $common_ids = $item_data['mb_tpl_layout_data'];
                        }
                        if (!empty($common_ids)) {
                            $common_ids = implode(",",$common_ids);
                            $sql = "SELECT  *  FROM  yf_goods_common  WHERE  common_id  IN($common_ids)  ORDER  BY  INSTR('$common_ids,',CONCAT(',',common_id,','))";
                            $db = new YFSQL();
                            $common_list = $db->find($sql);
//                            $common_list = $goodsCommonModel->getByWhere( array('common_id:IN' => $common_ids) );
                            if ( !empty($common_list) )
                            {
                                $layout_data = array();
                                foreach ($common_list as $common_id => $common_data)
                                {
                                    $layout_data[$common_id]['goods_id'] = $common_data['common_id'];
                                    $layout_data[$common_id]['goods_name'] = $common_data['common_name'];
                                    $layout_data[$common_id]['goods_price'] = $common_data['common_price'];
                                    $layout_data[$common_id]['goods_image'] = $common_data['common_image'];
                                }
                                if ($item_data['mb_tpl_layout_type'] == 'home5' || $item_data['mb_tpl_layout_type'] == 'newGoods') {
                                    $layout_list[$item_id]['mb_tpl_layout_data']['goods_ids'] = array_values($layout_data);
                                } else {
                                    $layout_list[$item_id]['mb_tpl_layout_data'] = array_values($layout_data);
                                } 
                            }
                        }
                    }
                    //广告版块A
                    if($layout_data['mb_tpl_layout_type'] == 'advA') {
                        $advA = [];
                        $mb_tpl_layout_data = $layout_data['mb_tpl_layout_data'];
                        $advA['title'] = $layout_data['mb_tpl_layout_title'];
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
                        $layout_list[$item_id]['mb_tpl_layout_data'] = $advA;
                    }
                    //广告版块B
                    if($layout_data['mb_tpl_layout_type'] == 'advB') {
                        $advB = [];
                        $mb_tpl_layout_data = $layout_data['mb_tpl_layout_data'];
                        $advB['title'] = $layout_data['mb_tpl_layout_title'];
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
                        $layout_list[$item_id]['mb_tpl_layout_data'] = $advB;
                    }
                    //快捷入口
                    if($layout_data['mb_tpl_layout_type'] == 'enterance') {
                        $item = [];
                        $mb_tpl_layout_data = $layout_data['mb_tpl_layout_data'];
                        foreach ($mb_tpl_layout_data as $key => $layout_data) {
                            $item[$key]['icons'] = $layout_data['icons'];
                            $item[$key]['navName'] = $layout_data['navName'];
                            $item[$key]['url'] = $layout_data['url'];
                        }
                        $layout_list[$item_id]['mb_tpl_layout_data'] = $item;
                    }
                    if($item_data['mb_tpl_layout_type'] == 'activityA' || $item_data['mb_tpl_layout_type'] == 'activityB')
                    {
                        $item = [];
                        $mb_tpl_layout_data = $item_data['mb_tpl_layout_data'];
                        foreach ($mb_tpl_layout_data as $key => $layout_data) {
                            switch($layout_data['type']) {
                                case 'groupbuy':
                                    //获取团购商品信息(common),不返回已经过期失效的团购商品信息
                                    $GroupBuy_BaseModel = new GroupBuy_BaseModel();
                                    $content_info = $GroupBuy_BaseModel->getForumGroupbuy(explode(',',$layout_data['content']));
                                    $layout_data['content_info'] = $content_info;
                                    break;
                                case 'discount':
                                    //获取限时折扣商品信息
                                    $Discount_GoodsModel = new Discount_GoodsModel();
                                    $discount_row = array();
                                    $discount_row['discount_goods_id:IN'] = explode(',', $layout_data['content']);
                                    $discount_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
                                    $discount_row['discount_goods_state'] = 1;
                                    $content = $Discount_GoodsModel->getDiscountGoods($discount_row);
                                    $content_info = array_values($content);
                                    $layout_data['content_info'] = $content_info;
                                    break;
                                case 'seckill':
                                    //获取秒杀商品信息
                                    $Seckill_GoodsModel = new Seckill_GoodsModel();
                                    $seckill_row = array();
                                    $seckill_row['seckill_goods_id:IN'] = explode(',', $layout_data['content']);
                                    $seckill_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
                                    $seckill_row['seckill_goods_state'] = 1;
                                    $content = $Seckill_GoodsModel->getSeckillGoods($seckill_row);
                                    $content_info = array_values($content);
                                    $layout_data['content_info'] = $content_info;
                                    break;
                                case 'pintuan':
                                    //拼团活动商品
                                    $PinTuan_Base = new PinTuan_Base();
                                    $cond_row = array();
                                    $cond_row['id:IN'] = explode(',',$layout_data['content']);
                                    $cond_row['status'] = 1;//拼团有效
                                    $cond_row['start_time:<'] = date('Y-m-d H:i:s');
                                    $cond_row['end_time:>'] = date('Y-m-d H:i:s');
                                    $content_info = $PinTuan_Base->getTplPinTuanGoods($cond_row);
                                    $layout_data['content_info'] = array_values($content_info);
                                    break;
                                case 'presale':
                                    //获取秒杀商品信息
                                    $Presale_GoodsModel = new Presale_GoodsModel();
                                    $presale_row = array();
                                    $presale_row['presale_goods_id:IN'] = explode(',', $layout_data['content']);
                                    $presale_row['goods_end_time:>'] = date('Y-m-d H:i:s', time());
                                    $presale_row['presale_goods_state'] = 1;
                                    $content = $Presale_GoodsModel->getPresaleGoods($presale_row);
                                    $content_info = array_values($content);
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
                        $layout_list[$item_id]['mb_tpl_layout_data'] = $item;
                    }
                }

                $data['items'] = array_values($layout_list);
            }
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     * 手机端模板
     * “广告条版块”只能添加一个
     */
    public function addTplLayout()
    {
        $item_type = request_string('item_type');
        $sub_site_id = request_int('sub_site_id', 0);
        $tpl_layout_style = request_int('tpl_layout_style', 1);
        if ( !empty($item_type) )
        {
            if ($item_type == 'adv_list')
            {
                $check_data = $this->mbTplLayoutModel->getByWhere( array('mb_tpl_layout_type' => 'adv_list','sub_site_id' => $sub_site_id,'tpl_layout_style'=>$tpl_layout_style) );

                if ( !empty($check_data) )
                {
                    return $this->data->addBody(-140, array(), __('广告条板块只能添加一个'), 250);
                }
            }

            if($item_type == 'enterance')
            {
                $mb_tpl_layout_data = array(
                    0 => array
                    (
                        'icons' => "/images/icons/jifen.png",
                        'navName' => '积分商城',
                        'select' => '积分商城',
                        'url' => 'tmpl/integral.html',
                        'imgInput' => 'false',
                    ),
                    1 => array
                    (
                        'icons' => '/images/icons/tuangou.png',
                        'navName' => '团购中心',
                        'select' => '团购中心',
                        'url' => 'tmpl/group_buy_index.html',
                        'imgInput' => 'false',
                    ),
                    2 => array
                    (
                        'icons' => '/images/icons/store.png',
                        'navName' => '店铺精选',
                        'select' => '店铺精选',
                        'url' => 'tmpl/store-list.html',
                        'imgInput' => 'false',
                    ),
                    3 => array
                    (
                        'icons' => '/images/icons/pintuan.png',
                        'navName' => '拼团中心',
                        'select' => '拼团中心',
                        'url' => 'tmpl/pintuan_index.html',
                        'imgInput' => 'false',
                    ),
                    4 => array
                    (
                        'icons' => '/images/icons/redpackets.png',
                        'navName' => '平台红包',
                        'select' => '平台红包',
                        'url' => 'tmpl/redpacket_plat.html',
                        'imgInput' => 'false',
                    )
                );
                $insert_data['mb_tpl_layout_data']   = $mb_tpl_layout_data;
            }

            $insert_data['mb_tpl_layout_type']   = $item_type;
            $insert_data['mb_tpl_layout_enable'] = 0;
            $insert_data['mb_tpl_layout_order']  = 99;
            $insert_data['sub_site_id']  = $sub_site_id;
            $insert_data['tpl_layout_style']  = $tpl_layout_style;
            $mb_tpl_layout_id = $this->mbTplLayoutModel->addTplLayout($insert_data, true);
            $mb_tpl_layout_id = 1;
            if ($mb_tpl_layout_id)
            {
                $insert_data['mb_tpl_layout_id'] = $mb_tpl_layout_id;
                $msg    = __('success');
                $status = 200;
            }
            else
            {
                $msg    = __('failure');
                $status = 250;
            }

            $this->data->addBody(-140, array(), $msg, $status);
        }
    }

    public function removeTplLayout()
    {
        $item_id = request_int('item_id');
        $flag = $this->mbTplLayoutModel->removeTplLayout($item_id);

        if ($flag)
        {
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, array(), $msg, $status);
    }

    public function editSortTplLayout()
    {
        $item_id_string = request_string('item_id_string');
        $item_id_array = explode(',', $item_id_string);

        foreach ($item_id_array as $k => $item_id)
        {
            $this->mbTplLayoutModel->editTplLayout($item_id, array('mb_tpl_layout_order' => $k));
        }

        $this->data->addBody(-140, array(), __('success'), 200);
    }

    public function editUsableTplLayout()
    {
        $item_id = request_int('item_id');
        $usable = request_string('usable');
        // $tpl_layout_style = request_int('tpl_layout_style', 1);
        $update_data['mb_tpl_layout_id'] = $item_id;
        $update_data['mb_tpl_layout_enable'] = $usable == 'usable' ? Mb_TplLayoutModel::USABLE : Mb_TplLayoutModel::UNUSABLE;

        $flag = $this->mbTplLayoutModel->editTplLayout($item_id, $update_data);

        if ($flag)
        {
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, array(), $msg, $status);
    }
    public   function editTplLayouts()
    {
        $mb_tpl_layout_id = request_string("mb_tpl_layout_id");
        $selsctL = request_row("selsctL");

        $arrs = explode(",", $selsctL);
        $this->mbTplLayoutModel = new Mb_TplLayoutModel();
        $arrys= $this->mbTplLayoutModel->getOneByWhere(array("mb_tpl_layout_id"=>$mb_tpl_layout_id));
        foreach ($arrys['mb_tpl_layout_data'] as $key => $value) {
            if(trim($arrys['mb_tpl_layout_data'][0]['title'])!=$arrs[0])
            {
                $arryr[0]['title']=$arrys['mb_tpl_layout_data'][1]['title'];
                $arryr[0]['type']=$arrys['mb_tpl_layout_data'][1]['type'];
                $arryr[0]['content']=$arrys['mb_tpl_layout_data'][1]['content'];
            }
            if(trim($arrys['mb_tpl_layout_data'][1]['title'])!=$arrs[1])
            {
                $arryr[1]['title']=$arrys['mb_tpl_layout_data'][0]['title'];
                $arryr[1]['type']=$arrys['mb_tpl_layout_data'][0]['type'];
                $arryr[1]['content']=$arrys['mb_tpl_layout_data'][0]['content'];
            }
        }



        $update_datas['mb_tpl_layout_data'] =$arryr;
        $flag = $this->mbTplLayoutModel->editTplLayout($mb_tpl_layout_id, $update_datas);

    }

    public function editTplLayout()
    {
        $item_id = request_int('item_id');
        $update_data['mb_tpl_layout_data'] = request_row('layout_data');
        $update_data['mb_tpl_layout_title'] = request_row('layout_title');
        $flag = $this->mbTplLayoutModel->editTplLayout($item_id, $update_data);

        if ($flag !== false)
        {
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, array(), $msg, $status);
    }

    // 活动版块A B编辑接口
    public function editTplABLayout()
    {
        $item_id = request_int('item_id');

        if($item_id){
            $fromto = request_string('fromto');
            $module = request_string('module');
            $update_data['mb_tpl_layout_data'] = request_row('layout_data');
            $update_data['mb_tpl_layout_title'] = request_row('layout_title');

            $mbTplList = $this->mbTplLayoutModel->getByWhere(['mb_tpl_layout_id'=>$item_id]);

            if($module == 'A'){
                $update_datas['mb_tpl_layout_data'] = request_row('layout_data');
                $update_datas['mb_tpl_layout_title'] = request_row('layout_title');
            }else{
                if($fromto == 1){
                    $update_datas['mb_tpl_layout_data'][0] = $update_data['mb_tpl_layout_data'][0];
                    $update_datas['mb_tpl_layout_data'][1] = $mbTplList[$item_id]['mb_tpl_layout_data'][1];
                }else{

                    $update_datas['mb_tpl_layout_data'][0] = $mbTplList[$item_id]['mb_tpl_layout_data'][0];
                    $update_datas['mb_tpl_layout_data'][1] = $update_data['mb_tpl_layout_data'][0];
                }
            }
            $flag = $this->mbTplLayoutModel->editTplLayout($item_id, $update_datas);

            if ($flag !== false)
            {
                $msg    = __('success');
                $status = 200;
            }
            else
            {
                $msg    = __('failure');
                $status = 250;
            }
        }else{
            $msg    = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, array(), $msg, $status);
    }

}
?>