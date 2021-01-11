<?php
if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_Seller_GoodsCtl extends Seller_Controller
{
    public $shopBaseModel;
    public $goodsTypeModel;
    public $goodsCatModel;
    public $goodsBrandModel;
    public $goodsSpecModel;
    public $goodsPropertyValueModel;
    public $goodsSpecValueModel;
    public $goodsCommonModel;
    public $goodsBaseModel;
    public $goodsCommonDetailModel;
    public $shopGoodsCat;
    
    /**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     *
     * @access public
     */
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->shopBaseModel = new Shop_BaseModel();
        $this->goodsTypeModel = new Goods_TypeModel();
        $this->goodsCatModel = new Goods_CatModel();
        $this->goodsBrandModel = new Goods_BrandModel();
        $this->goodsSpecModel = new Goods_SpecModel();
        $this->goodsPropertyValueModel = new Goods_PropertyValueModel();
        $this->goodsSpecValueModel = new Goods_SpecValueModel();
        $this->goodsCommonModel = new Goods_CommonModel();
        $this->goodsBaseModel = new Goods_BaseModel();
        $this->goodsCommonDetailModel = new Goods_CommonDetailModel();
        $this->shopGoodsCat = new Shop_GoodsCat();
    }

    /**
     * 首页
     *
     * @access public
     */
    public function getGoodsCat()
    {
        $cat_id = request_int('cat_id', 0);
        $action = request_string('action');
        $common_id = request_int('common_id');
        $user_id = request_string('user_id');
        $shop_info = $this->shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_info = current($shop_info);
        $shop_id = $shop_info['shop_id'];
        // if ($cat_id) {
        //     if (empty($common_id)) {
        //         //获取运费模板信息
        //         $template_model = new Transport_TemplateModel();
        //         $transport_template = $template_model -> getOpenTemplate($shop_id);
        //         $shop_model = new Shop_BaseModel();
        //         $shop_info = $shop_model -> getOne($shop_id);
        //         $data = $this -> goodsTypeModel -> getTypeInfoByPublishGoods($cat_id);
        //         $this -> view -> setMet('goodsInfoManage');
        //     } else {
        //         return $this -> editGoods();
        //     }
        // } elseif (!empty($action) && $action == 'goodsImageManage') {
        //     $data = $this -> goodsImageManage($common_id);
        //     $common_data = $data['common_data'];
        //     $common_image = $common_data['common_image'];
        //     if (!empty($data['color'])) {
        //         $color = $data['color'];
        //     }
        //     $this -> view -> setMet('goodsImageManage');
        // } else {
        $Goods_CatModel = new Goods_CatModel();
        $cat_rows = $Goods_CatModel->sellerGetCatTreeData($cat_id, false, 0, true, $shop_id);
        // }
        $data = [];
        $data = $cat_rows;
        $this->data->addBody(-140, $data, __('success'), 200);
    }

    public function getGoodsSpecAndVirtual()
    {
        $shopBaseModel = new Shop_BaseModel();
        $Goods_TypeModel = new Goods_TypeModel();
        $Goods_CatModel = new Goods_CatModel();
        $cat_id = request_int('cat_id');
        $user_id = request_string('user_id');
        $shop_info = $shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_info = current($shop_info);
        $shop_id = $shop_info['shop_id'];
        $data = [];
        $goods_type = $Goods_TypeModel->getTypeInfoByPublishGoods($cat_id, $shop_id);
        $data['goods_type'] = $goods_type['spec'];
        $cat['cat_id'] = $cat_id;
        $goods_cat = $Goods_CatModel->getOneByWhere($cat);
        $data['cat'] = $goods_cat;
        if ($data) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }

    public function getGoodsTemplate()
    {
        $user_id = request_string('user_id');
        $shop_info = $this->shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_info = current($shop_info);
        $shop_id = $shop_info['shop_id'];
        $data = [];
        $template_model = new Transport_TemplateModel();
        $transport_template = $template_model->getOpenTemplate($shop_id);
        $data = $transport_template;
        $this->data->addBody(-140, $data, __('success'), 200);
    }

    public function getGoodsTransportArea()
    {
        $user_id = request_string('user_id');
        $shop_info = $this->shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_info = current($shop_info);
        $shop_id = $shop_info['shop_id'];
        $data = [];

        $type_model = new Transport_AreaModel();
        $data = $type_model->getByWhere(array('shop_id' => $shop_id));
        if (!$data) {
            return array();
        }
        foreach ($data as $key => $value) {
            $area_ids = array();
            if ($value['area_ids'] == 0) {
                $data[$key]['area_name'] = __('全国');
            } else {
                $district_name = $type_model->getDistrictName($value['area_ids']);
                $data[$key]['area_name'] = mb_strimwidth($district_name, 0, 20, '...', 'utf8');
            }
        }

        $this->data->addBody(-140, $data, __('success'), 200);
    }

    /*
     * 上传商品图片 -- 第三步
     * */
    public function addOrEditShopGoods()
    {
        $common_id = request_int('common_id'); //区分是修改商品，还是添加商品
        $spec = request_row('spec');//商品规格
        $price = request_int('price');//商品售价
        if (!$this->isEditGoods($common_id, $price, $spec)) {
            throw new Exception('活动商品不允许修改商品价格');
        }

        $Goods_CommonModel = new Goods_CommonModel();
        $action = request_string('action');
        $user_id = request_string('user_id');//商家ID
        $shop_base = $this->shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_base = current($shop_base);

        $shop_id = $shop_base['shop_id'];
        $common_data['shop_status'] = $shop_base['shop_status'];  //插入店铺状态
        $goods_cat_base = $this->goodsCatModel->getCat(request_int('cat_id'));
        $goods_cat_base = current($goods_cat_base);

        $matche_row = array();
        //有违禁词
        if (Text_Filter::checkBanned(request_string('name'), $matche_row)) {
            return $this->data->addBody(-140, array(), __('含有违禁词'), 250);
        }
        //当前商品数量统计, 非自营店铺
        if ($shop_base['shop_self_support'] != Shop_BaseModel::SELF_SUPPORT_TRUE) {
            $shop = array();
            $shop['shop_id'] = $shop_base['shop_id'];
            $shop_base_row = $this->shopBaseModel->getBaseOneList($shop);
            $goods_state_normal_num = $Goods_CommonModel->getCommonStateNum($shop_base['shop_id'], -1);
            if (!empty($shop_base_row['shop_grade_row'])) {
                $shop_grade_goods_limit = $shop_base_row['shop_grade_row']['shop_grade_goods_limit'];
                $shop_grade_album_limit = $shop_base_row['shop_grade_row']['shop_grade_album_limit'];
            } else {
                $shop_grade_goods_limit = 0;
                $shop_grade_album_limit = 0;
            }
            if (0 != $shop_grade_goods_limit && $shop_grade_goods_limit <= $goods_state_normal_num) {
                return $this->data->addBody(-140, array(), __('商品发布数量超出平台限制！'), 250);
            }
        }
        $Goods_BrandModel = new Goods_BrandModel();
        $Upload_BaseModel = new Upload_BaseModel();
        $shop_album_num = $Upload_BaseModel->getUploadNum($shop['shop_id']);
        $common_data['shop_id'] = $shop_base['shop_id'];                        //店铺id
        $common_data['shop_name'] = $shop_base['shop_name'];                        //店铺名称
        $common_data['type_id'] = $goods_cat_base['type_id'];                    //类型id
        $common_data['shop_self_support'] = $shop_base['shop_self_support'] == Shop_BaseModel::SELF_SUPPORT_TRUE ? 1 : 0;     //是否自营
        $common_data['cat_id'] = request_string('cat_id');                    //商品分类id
        $common_data['cat_name'] = request_string('cat_name');                    //商品分类
        $common_data['common_name'] = Text_Filter::filterWords(request_string('name')); //商品名称
        $common_data['common_promotion_tips'] = Text_Filter::filterWords(request_string('promotion_tips')); //副标题
        $common_data['common_image'] = request_string('imagePath');                    //商品主图
        $common_data['common_price'] = request_float('price');                        //商品价格
        $common_data['common_market_price'] = request_float('market_price');                //市场价
        if ($common_data['common_price'] > $common_data['common_market_price']) {
            $msg = $shop_base['shop_type'] == 2 ? __('市场价不能低于供货价格') : __('市场价不能低于商品价格');
            return $this->data->addBody(-140, array(), $msg, 250);
        }
        $common_data['common_stock'] = request_int('stock');                            //商品库存
        $common_data['common_cubage'] = request_float('cubage');                        //商品重量
        $common_data['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;                   //商品状态
        $common_data['common_is_recommend'] = request_string('is_recommend');                //商品推荐
        $common_data['common_add_time'] = date('Y-m-d H:i:s', time());                    //商品添加时间
        //设置地区
        $common_data['district_id'] = $shop_base['district_id'];
        $common_data['common_invoices'] = 1;                    //商品默认支持开发票
        //读取店铺关联的消费者保障服务
//            $this ->goodsCommonModel->getShopContract($common_data);
        $spec_name = request_string('spec_name');
        //选择售卖区域
        $transport_area_id = request_int('transport_area_id');
        if ($transport_area_id) {
            $area_model = new Transport_AreaModel();
            $check_area = $area_model->checkArea($transport_area_id, $shop_id);
            if (!$check_area) {
                return $this->data->addBody(-140, array(), __('售卖区域数据有误'), 250);
            }
        } else {
            return $this->data->addBody(-140, array(), __('请设置售卖区域'), 250);
        }
        $common_data['transport_area_id'] = $transport_area_id;
        //获取运费模板信息
        $template_model = new Transport_TemplateModel();
        $transport_template = $template_model->getOpenTemplate($shop_id);
        if (!$transport_template) {
            return $this->data->addBody(-140, array(), __('请设置运费模板'), 250);
        }

        /* 只有可发布虚拟商品才会显示 S */
        $is_gv = request_int('is_gv');
        $common_data['common_is_virtual'] = $is_gv;  //虚拟商品
        if ($is_gv == 1) {
            $common_data['common_virtual_date'] = request_string('g_vindate');    //虚拟商品有效期
            $common_data['common_virtual_refund'] = request_int('g_vinvalidrefund');  //支持过期退款
        }

        //如果是立即发布，则发布时间为当前添加时间
        $common_data['common_sell_time'] = $common_data['common_add_time'];
        $spec_val = request_row('spec_val');
        if (!empty($spec_val)) {
            $diff_spec_array = array_diff_key($spec_name, $spec_val);
            $flag_spec = empty($diff_spec_array);
            if (!empty($spec_name) && $flag_spec) {
                $common_data['common_spec_name'] = $spec_name; //规格名称
                $common_data['common_spec_value'] = $spec_val; //规格名称
            }
        }
        //判断发布的的商品是否需要审核
        if (Web_ConfigModel::value('goods_verify_flag') == 0) { //商品是否需要审核
            $common_data['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
        } else {
            $common_data['common_verify'] = Goods_CommonModel::GOODS_VERIFY_WAITING;
        }

        $this->goodsCommonModel->sql->startTransactionDb();

        if ($action == 'edit') {
            //分销商分销商品商品修改权限
            $common_base = $this->goodsCommonModel->getOne($common_id);
            if ($shop_base['shop_type'] == 1 && $common_base['product_is_allow_update'] == 0 && $common_base['product_is_allow_price'] == 1) {
                $dist_allow_edit = array();
                $dist_allow_edit['common_price'] = $common_data['common_price'];
                $dist_allow_edit['common_market_price'] = $common_data['common_market_price'];
                $edit_status = $this->goodsCommonModel->editCommon($common_id, $dist_allow_edit);
            } else {
                if ($shop_base['shop_type'] == 1 && $common_base['product_is_allow_update'] == 1 && $common_base['product_is_allow_price'] == 0) {
                    unset($common_data['common_price']);
                    unset($common_data['common_market_price']);
                } elseif ($shop_base['shop_type'] == 1 && $common_base['product_is_allow_update'] == 0 && $common_base['product_is_allow_price'] == 0) {
                    $common_data = array();
                }
                $edit_status = $this->goodsCommonModel->editCommon($common_id, $common_data);
            }
            $data['action'] = 'edit';
            $data['edit_status'] = $edit_status;
            // $data['property'] = request_row('property');
        } else {
            $common_data['common_edit_time'] = date('Y-m-d H:i:s', time());
            $common_id = $this->goodsCommonModel->addCommon($common_data, true);
        }
        if ($common_id && $this->goodsCommonModel->sql->commitDb()) {

            // $body = Text_Filter::filterWords(request_string('body'));
            $spec_data = request_row('spec');
            $goods_base = $this->goodsBaseModel->getByWhere(array('common_id' => $common_id));
            if (!empty($goods_base)) {
                $goods_base_ids = array_column($goods_base, 'goods_id');
            }
            $goods_data['cat_id'] = $common_data['cat_id'];                    //商品分类id
            $goods_data['common_id'] = $common_id;                                //商品公共表id
            $goods_data['shop_id'] = $common_data['shop_id'];                    //shop_id
            $goods_data['shop_name'] = $common_data['shop_name'];                //shop_name
            $goods_data['goods_name'] = $common_data['common_name'];                //商品名称
             $goods_data['goods_promotion_tips'] = $common_data['common_promotion_tips'];    //促销提示
            // $goods_data['goods_is_recommend'] = $common_data['common_is_recommend'];        //商品推荐
            $goods_data['goods_image'] = $common_data['common_image'];                //商品主图
            //加入goods_id 冗余数据
            $goods_ids = array();
            $color_ids = array();
            $edit_goods_ids = array();
            $retain_flag = false;
            $down_flag = false;
            if (!empty($spec_data) && $flag_spec) {
                //读取颜色规格值
                $goodsSpecValueModel = new Goods_SpecValueModel();
                $spec_value_color_ids = $goodsSpecValueModel->getSpecValueByColor();
                //判断前台是否有老数据
                //过滤无用垃圾数据
                $edit_goods_ids = array_column($spec_data, 'goods_id');
                //判断有无修改goods_id 如果没有修改goods_id 则要删除之前goods_id 不符合现在标准
                $edit_goods_ids_string = implode("", $edit_goods_ids);
                if (empty($edit_goods_ids_string) && $action == 'edit') {
                    $retain_flag = true;
                    $goods_base_ids = array_values($goods_base_ids);
                    $retain_f_goods_id = $goods_base_ids[0];
                    unset($goods_base_ids[0]);
                }
                //删除无用垃圾数据
                $remove_goods_ids = array();
                foreach ($goods_base_ids as $old_id) {
                    if (!in_array($old_id, $edit_goods_ids)) {
                        $remove_goods_ids[] = $old_id;
                    }
                }
                if (!empty($remove_goods_ids)) {
                    $this->goodsBaseModel->removeBase($remove_goods_ids);
                }
                foreach ($spec_data as $key => $val) {
                    $goods_data['goods_price'] = $val['price'];                            //商品价格
                    $goods_data['goods_market_price'] = $val['market_price'];                        //市场价
                    $goods_data['goods_stock'] = $val['stock'];                            //商品库存
                    $goods_data['goods_alarm'] = $val['alarm'];                            //库存预警值
                    $goods_data['goods_code'] = $val['sku'];                                //商家编号货号
                    $goods_data['goods_max_sale'] = 0;                   //单人最大购买数量
                    $goods_data['goods_spec'] = array($key => $val['sp_value']);        //商品规格-JSON存储
                    //分销商店铺
                    if ($shop_base['shop_type'] == 1 && $val['goods_recommended_min_price'] && $val['goods_recommended_max_price']) {
                        if ($val['price'] < $val['goods_recommended_min_price']) {
                            $goods_data['goods_price'] = $val['goods_recommended_min_price'];
                        } elseif ($val['price'] > $val['goods_recommended_max_price']) {
                            $goods_data['goods_price'] = $val['goods_recommended_max_price'];
                        }
                    }
                    //供应商店铺
                    if ($shop_base['shop_type'] == 2) {
                        $goods_data['goods_recommended_min_price'] = $val['goods_recommended_min_price']; //最低销售价格
                        $goods_data['goods_recommended_max_price'] = $val['goods_recommended_max_price'];   //最高销售价格
                    }
                    if (!empty($val['color'])) {
                        $goods_data['color_id'] = $val['color'];                                //颜色
                    }
                    //判断是修改数据还是新增数据
                    if (!empty($val['goods_id'])) {
                        $goods_id = $val['goods_id'];
                        //获取原有的base数据信息
                        $old_base = $this->goodsBaseModel->getOne($goods_id);
                        if (($goods_data['goods_price'] != $old_base['goods_price']) || ($goods_data['goods_stock'] != $old_base['goods_stock']) || ($goods_data['goods_recommended_min_price'] != $old_base['goods_recommended_min_price']) || ($goods_data['goods_recommended_max_price'] != $old_base['goods_recommended_max_price'])) {
                            //产品价格、产品库存、最低零售价、最高零售价格 是否更改
                            $down_flag = true;
                        }
                        $this->goodsBaseModel->editBase($goods_id, $goods_data, false);
                        $edit_ids[] = $goods_id;
                    } else {
                        if ($retain_flag) {
                            //获取原有的base数据信息
                            $old_base = $this->goodsBaseModel->getOne($retain_f_goods_id);
                            if (($goods_data['goods_price'] != $old_base['goods_price']) || ($goods_data['goods_stock'] != $old_base['goods_stock']) || ($goods_data['goods_recommended_min_price'] != $old_base['goods_recommended_min_price']) || ($goods_data['goods_recommended_max_price'] != $old_base['goods_recommended_max_price'])) {
                                //产品价格、产品库存、最低零售价、最高零售价格 是否更改
                                $down_flag = true;
                            }
                            $goods_id = $this->goodsBaseModel->editBase($retain_f_goods_id, $goods_data, false);
                            $retain_flag = false;
                        } else {
                            $goods_id = $this->goodsBaseModel->addBase($goods_data, true);
                            $add_ids[] = $goods_id;
                        }
                    }
                    //color_id 冗余数据
                    foreach ($val['sp_value'] as $k => $v) {
                        if (in_array($k, $spec_value_color_ids) && !in_array($k, $color_ids)) {
                            $color_ids[] = $k;
                            $goods_ids[] = array(
                                'goods_id' => $goods_id,
                                'color_id' => $k
                            );
                            break;
                        }
                    }
                }
            } else {
                $goods_data['goods_price'] = $common_data['common_price'];                //商品价格
                $goods_data['goods_market_price'] = $common_data['common_market_price'];        //市场价
                $goods_data['goods_stock'] = $common_data['common_stock'];                //商品库存
                // $goods_data['goods_alarm'] = $common_data['common_alarm'];                //库存预警值
                // $goods_data['goods_code'] = $common_data['common_code'];                //商家编号货号
                //供应商店铺
                if ($shop_base['shop_type'] == 2) {
                    $goods_data['goods_recommended_min_price'] = request_float('goods_recommended_min_price'); //最低销售价格
                    $goods_data['goods_recommended_max_price'] = request_float('goods_recommended_max_price');   //最高销售价格
                }
                if ($action == 'edit') {
                    $goods_id = pos($goods_base_ids);
                    //获取原有的base数据信息
                    $old_base = $this->goodsBaseModel->getOne($goods_id);
                    if (($goods_data['goods_price'] != $old_base['goods_price']) || ($goods_data['goods_stock'] != $old_base['goods_stock']) || ($goods_data['goods_recommended_min_price'] != $old_base['goods_recommended_min_price']) || ($goods_data['goods_recommended_max_price'] != $old_base['goods_recommended_max_price'])) {
                        //产品价格、产品库存、最低零售价、最高零售价格 是否更改
                        $down_flag = true;
                    }
                    $this->goodsBaseModel->editBase($goods_id, $goods_data, false);
                    $edit_ids[] = $goods_id;
                } else {
                    $goods_id = $this->goodsBaseModel->addBase($goods_data, true);
                    $add_ids[] = $goods_id;
                }
            }
            if (empty($goods_ids)) {
                $goods_ids[] = array(
                    'goods_id' => $goods_id,
                    'color' => 0
                );
            }
            $edit_common_data['goods_id'] = $goods_ids;
            $test_id = $this->goodsCommonModel->editCommon($common_id, $edit_common_data);
            if ($common_base['common_parent_id']) {//如果是分销商更改数据，改变修改完的跳转链接
                $data['dist_goods'] = 1;
            }
            //供货商规格商品列表
            $supplier_goods_list = $this->goodsBaseModel->getByWhere(array('common_id' => $common_id));
            if ($shop_base['shop_type'] == 2) {
                $MessageModel = new MessageModel();
                $all_common = $this->goodsCommonModel->getByWhere(array('common_parent_id' => $common_id, 'product_is_behalf_delivery' => 1));
                foreach ($all_common as $key => $value) {
                    $dist_common_base = $this->goodsCommonModel->getOne($value['common_id']);
                    $dist_shop_base = $this->shopBaseModel->getOne($dist_common_base['shop_id']);
                    //修改商品信息，并下架,重新加载商品规格
                    $dist_common_row = $this->goodsCommonModel->SynchronousCommon($common_id, $dist_shop_base, 'edit');
                    //如果商品允许修改内容，只更新部分内容
                    if ($common_base['product_is_allow_update']) {
                        $allow_edit = array();
                        $allow_edit['common_spec_name'] = $dist_common_row['common_spec_name'];
                        $allow_edit['common_spec_value'] = $dist_common_row['common_spec_value'];
                        $allow_edit['common_price'] = $dist_common_row['common_price'];
                        $allow_edit['common_market_price'] = $dist_common_row['common_market_price'];
                        $allow_edit['goods_recommended_min_price'] = $dist_common_row['goods_recommended_min_price'];
                        $allow_edit['goods_recommended_max_price'] = $dist_common_row['goods_recommended_max_price'];
                        $allow_edit['common_cubage'] = $dist_common_row['common_cubage'];
                        $allow_edit['product_is_allow_update'] = $dist_common_row['product_is_allow_update'];
                        $allow_edit['product_is_allow_price'] = $dist_common_row['product_is_allow_price'];
                        $dist_common_row = $allow_edit;
                    }
                    if (!$common_base['product_is_behalf_delivery']) {
                        $this->goodsCommonModel->removeCommon($value['common_id']);
                        //发送消息
                        $des = '该商品不支持代发货';
                        $MessageModel->sendMessage('del goods', $dist_shop_base['user_id'], $dist_shop_base['user_name'], $order_id = null, $shop_name = null, 1, 1, $end_time = null, $value['common_id'], $goods_id = null, $des);
                    } else {
                        $old_goods_base = $this->goodsBaseModel->getByWhere(array('common_id' => $value['common_id']));
                        $base_row = array();
                        foreach ($old_goods_base as $k => $val) {
                            if (in_array($val['goods_parent_id'], $remove_goods_ids)) {
                                $this->goodsBaseModel->removeBase($val['goods_id']);
                            } elseif (in_array($val['goods_parent_id'], $edit_ids)) {
                                $parent_goods = $this->goodsBaseModel->getOne($val['goods_parent_id']);
                                $edit_rows = array();
                                $edit_rows['goods_spec'] = $parent_goods['goods_spec'];
                                $edit_rows['goods_price'] = $parent_goods['goods_recommended_min_price'];
                                $edit_rows['goods_market_price'] = $parent_goods['goods_recommended_max_price'];
                                $edit_rows['goods_stock'] = $parent_goods['goods_stock'];
                                $edit_rows['goods_recommended_min_price'] = $parent_goods['goods_recommended_min_price'];
                                $edit_rows['goods_recommended_max_price'] = $parent_goods['goods_recommended_max_price'];
                                $this->goodsBaseModel->editBase($val['goods_id'], $edit_rows, false);
                                $dist_common_row['goods_id'][] = array(
                                    'goods_id' => $val['goods_id'],
                                    'color' => $val['color_id']
                                );
                            }
                            if ($add_ids) {
                                foreach ($add_ids as $addk => $addv) {
                                    $parent_goods = $this->goodsBaseModel->getOne($addv);
                                    $add_rows = array();
                                    $add_rows['common_id'] = $value['common_id'];
                                    $add_rows['shop_id'] = $dist_shop_base['shop_id'];
                                    $add_rows['shop_name'] = $dist_shop_base['shop_name'];
                                    $add_rows['goods_name'] = $parent_goods['goods_name'];
                                    $add_rows['cat_id'] = $parent_goods['cat_id'];
                                    $add_rows['brand_id'] = $parent_goods['brand_id'];
                                    $add_rows['goods_spec'] = $parent_goods['goods_spec'];
                                    $add_rows['goods_price'] = $parent_goods['goods_recommended_min_price'];
                                    $add_rows['goods_market_price'] = $parent_goods['goods_recommended_max_price'];
                                    $add_rows['goods_stock'] = $parent_goods['goods_stock'];
                                    $add_rows['goods_image'] = $value['common_image'];
                                    $add_rows['goods_parent_id'] = $parent_goods['goods_id'];
                                    $add_rows['goods_is_shelves'] = 1;
                                    $add_rows['goods_recommended_min_price'] = $parent_goods['goods_recommended_min_price'];
                                    $add_rows['goods_recommended_max_price'] = $parent_goods['goods_recommended_max_price'];
                                    $add_goods_id = $this->goodsBaseModel->addBase($add_rows, true);
                                    $dist_common_row['goods_id'][] = array(
                                        'goods_id' => $add_goods_id,
                                        'color' => $parent_goods['color_id']
                                    );
                                }
                            }
                        }
                        $dist_common_row['common_distributor_flag'] = 2;
                        if ($down_flag) {
                            $dist_common_row['common_state'] = 0;//下架
                            $dist_common_row['common_distributor_flag'] = 1;
                            //给每个商品下架的店铺发送提示
                            $common_state_remark = '供货商修改了商品-' . $common_base["common_name"] . '！';
                            $MessageModel->sendMessage('Commodity violation is under the shelf', $dist_shop_base['user_id'], $dist_shop_base['user_name'], $order_id = null, $shop_name = null, 1, 1, $end_time = null, $value['common_id'], $goods_id = null, $common_state_remark);
                        }
                        $this->goodsCommonModel->editCommon($value['common_id'], $dist_common_row);
                    }
                }
            }
            //商品添加或者修改成功向统计中心发送数据
            if ($action == 'edit') {
                if ($edit_status) {
                    $analytics_data = array(
                        'common_id' => $common_id,
                    );
                    Yf_Plugin_Manager::getInstance()->trigger('analyticsGoodsEdit', $analytics_data);
                    /******************************************************/
                }
            } else {
                $analytics_data = array(
                    'common_id' => $common_id,
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsGoodsAdd', $analytics_data);
                /******************************************************/
            }
            if (Web_ConfigModel::value('Plugin_Fenxiao')) {
                $cat_id = request_string('cat_id');
                $values = request_row('fenxiao');
                $data = [
                    'shop_id' => $shop_id,
                    'cat_id' => $cat_id,
                    'goods_id' => $common_id,
                    'values' => $values,
                ];
                Fenxiao::getInstance()->updateGoods($data);
            }
            $data['common_id'] = $common_id;
            $this->data->addBody(-140, $data, __('success'), 200);
        } else {
            $this->goodsCommonModel->sql->rollBackDb();
            $this->data->addBody(-140, array(), __('failure'), 250);
        }
    }

    /**
     * 是否可以编辑商品
     * 规则：
     *     活动商品不允许修改商品价格低于活动价2018/01/26
     *
     * @param $commonId
     * @param $spec
     *
     * @return boolean
     */
    private function isEditGoods($commonId, $price, $spec)
    {
        if (empty($commonId)) {
            return true;
        }
        $promotion = new Promotion;
        //两种情况：1、无goodsId 2、有goodsId
        if ($spec) { //有goodsId
            //获取goodsId
            foreach ($spec as $item) {
                $goodsId = $item['goods_id'];
                $editGoodsPrice = $item['price'];
                $goodsPromotionPrice = $promotion->getGoodsPromotionPrice($goodsId);
                if ($goodsPromotionPrice > 0 && $editGoodsPrice < $goodsPromotionPrice) {
                    return false;
                }
            }
        } else {
            $goods = $this->goodsBaseModel->getOneByWhere([
                'common_id' => $commonId
            ]);
            $goodsId = $goods['goods_id'];
            $goodsPromotionPrice = $promotion->getGoodsPromotionPrice($goodsId);
            if ($goodsPromotionPrice > 0 && $price < $goodsPromotionPrice) {
                return false;
            }
        }
        return true;
    }

    //商品列表
    public function getGoodsList()
    {
        $type = request_int('type');//商品为出售状态还是下架
        $page = request_int('page');
        $rows = request_int('rows');
        $user_id = request_string('user_id');
        $shop_info = $this->shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_info = current($shop_info);
        $shop_id = $shop_info['shop_id'];
        $Goods_CommonModel = new Goods_CommonModel();

        if ($type) {
            $cront_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
        } else {
            $cront_row['common_state'] = Goods_CommonModel::GOODS_STATE_OFFLINE;
        }

        $cront_row['shop_id'] = $shop_id;
        $cront_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
        $cront_row['is_del'] = Goods_BaseModel::IS_DEL_NO;

        $goods_rows = $Goods_CommonModel->getCommonList($cront_row, array('common_id' => 'DESC'), $page, $rows);
        $data = [];
        if ($goods_rows) {
            $data = $goods_rows;
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('无商品');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    //商品上下架
    public function editGoodsCommon()
    {
        $user_id = request_string('user_id');
        $shop_info = $this->shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_info = current($shop_info);
        $shop_id = $shop_info['shop_id'];

        $Shop_BaseModel = new Shop_BaseModel();
        $Goods_CommonModel = new Goods_CommonModel();

        $goods_common_id_rows = request_row('chk');
        $shop_base = $Shop_BaseModel->getOne($shop_id);
        if ($shop_base['shop_status'] != Shop_BaseModel::SHOP_STATUS_OPEN) {
            return $this->data->addBody(-140, array(), __('请先联系平台开启店铺'), 250);
        }
        foreach ($goods_common_id_rows as $key => $value) {
            $goods_common_id = $value;
            $data_goods = $Goods_CommonModel->getOne($goods_common_id);
            if ($data_goods['shop_id'] == $shop_id) {
                if (request_string('act') == 'down') {
                    $flag = $Goods_CommonModel->editCommon($goods_common_id, array('common_state' => Goods_CommonModel::GOODS_STATE_OFFLINE));
                    //对goods_base对应的数据上架
                    $goodsBaseModel = new Goods_BaseModel();
                    $goods_item = $goodsBaseModel->getByWhere(array("common_id:IN" => $goods_common_id));
                    $goods_ids = array_column($goods_item, 'goods_id');
                    $flag = $goodsBaseModel->editBase($goods_ids, array("goods_is_shelves" => Goods_BaseModel::GOODS_DOWN), false);

                    //如果是供货商下架，同时下架其分销商的该商品
                    if ($shop_base['shop_type'] == 2) {
                        $MessageModel = new MessageModel();
                        $all_dist_common = $Goods_CommonModel->getByWhere(array('common_parent_id' => $goods_common_id));
                        if (!empty($all_dist_common)) {
                            foreach ($all_dist_common as $k => $v) {
                                $dist_shop_base = $Shop_BaseModel->getOne($v['shop_id']);
                                $dist_common_row['common_state'] = 0;//下架
                                //给每个商品下架的店铺发通知
                                $common_state_remark = '供货商修改了商品-' . $v["common_name"] . '！';
                                $MessageModel->sendMessage('Commodity violation is under the shelf', $dist_shop_base['user_id'], $dist_shop_base['user_name'], $order_id = null, $shop_name = null, 1, 1, $end_time = null, $v['common_id'], $goods_id = null, $common_state_remark);
                                $Goods_CommonModel->editCommon($v['common_id'], $dist_common_row);
                            }
                        }
                    }
                } elseif (request_string('act') == 'up') {
                    if (request_string('me') == 'lockup') {
                        $flag = $Goods_CommonModel->editCommon($goods_common_id, array('shop_status' => $shop_base['shop_status'], 'common_state' => Goods_CommonModel::GOODS_STATE_NORMAL, 'common_verify' => Goods_CommonModel::GOODS_STATE_ILLEGAL));
                    } else {
                        //判断商品是否有售卖区域，没有则不允许上架
                        $goods_common = $Goods_CommonModel->getOne($goods_common_id);
//                            if(!$goods_common['transport_area_id']) {
//                                return $this -> data -> addBody(-140, array(), __('请先选择商品售卖区域'), 250);
//                            }
                        $flag = $Goods_CommonModel->editCommon($goods_common_id, [
                            'common_state' => Goods_CommonModel::GOODS_STATE_NORMAL,
                            'common_verify' => Goods_CommonModel::GOODS_STATE_NORMAL,
                            'common_goods_from' => 1, //外部导入一经上架，撕掉外部导入标签
                            'shop_status' => $shop_base['shop_status']
                        ]);
                        //对goods_base对应的数据上架
                        $goodsBaseModel = new Goods_BaseModel();
                        $goods_item = $goodsBaseModel->getByWhere(array("common_id:IN" => $goods_common_id));
                        $goods_ids = array_column($goods_item, 'goods_id');
                        $flag = $goodsBaseModel->editBase($goods_ids, array("goods_is_shelves" => Goods_BaseModel::GOODS_UP), false);
                    }
                } elseif (request_string('act') == 'del') {
                    $flag = $Goods_CommonModel->removeCommon($goods_common_id);
                }
            }
        }
        if ($flag !== false) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure');
            $status = 250;
        }
        $data['flag'] = $flag;
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //删除商品
    public function deleteGoodsCommonRows()
    {
        $id = request_row('id');
        $user_id = request_string('user_id');
        $shop_info = $this->shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_info = current($shop_info);
        $shop_id = $shop_info['shop_id'];
        // $shop_id = Perm::$shopId;
        $MessageModel = new MessageModel();
        $shop_base = $this->shopBaseModel->getOne($shop_id);
        $data = $this->goodsCommonModel->getByWhere(array(
            'common_id:in' => $id,
            'shop_id' => $shop_id
        ));
        $Goods_CommonModel = new Goods_CommonModel();
        $common_ids = array_values(array_column($data, 'common_id'));
        $flag = $this->goodsCommonModel->removeCommon($common_ids);
        //批量删除分销商的商品
        if ($shop_base == 2 && !empty($id)) {
            foreach ($id as $key => $value) {
                $all_dist_common = $Goods_CommonModel->getByWhere(array('common_parent_id' => $value));
                if (!empty($all_dist_common)) {
                    foreach ($all_dist_common as $k => $v) {
                        if ($data['common_parent_id'] > 0 /*&& request_string('act') == 'del'*/) {
                            //2、是分销别人的商品就删除这个商品
                            $flag = $this->goodsCommonModel->removeCommon($v['common_id']);
                            // } elseif (request_string('act') == 'update') {
                            //     //3、是自己分销的商品修改商品的分销状态设置为0，分理、佣金设置为0
                            //     //修改数据
                            //     $common_data = array(
                            //         'common_is_directseller' => 0,
                            //         'common_cps_rate' => '0.00',
                            //         'common_second_cps_rate' => '0.00',
                            //         'common_third_cps_rate' => '0.00',
                            //     );
                            //     $flag = $this -> goodsCommonModel -> editCommon($v['common_id'], $common_data);
                        }
                    }
                }
            }
        }
        if ($flag) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('failure2');
            $status = 250;
        }
        $data_re['id'] = $id;
        $this->data->addBody(-140, $data_re, $msg, $status);
    }

    //添加商品规格
    public function addSpecValue()
    {
        $spec_id = request_int('spec_id');
        $name = request_string('name');
        $position = request_string('position');

        $user_id = request_string('user_id');
        $shop_info = $this->shopBaseModel->getByWhere(['user_id' => $user_id]);
        $shop_info = current($shop_info);
        $shop_id = $shop_info['shop_id'];
        $update_data['cat_id'] = request_int('cat_id');
        $update_data['shop_id'] = $shop_id;

        //商城添加规格值
        if (!empty($position) && $position == 'storeAddGoods') {
            $update_data['spec_id'] = $spec_id;
            $update_data['shop_id'] = $shop_id;
            $update_data['spec_value_name'] = $name;
            $spec_value_id = $this->goodsSpecValueModel->addSpecValue($update_data, true);

            if ($spec_value_id) {
                $status = 200;
                $msg = __('success');
                $update_data['spec_value_id'] = $spec_value_id;
            } else {
                $status = 250;
                $msg = __('failure');
                $update_data['spec_value_id'] = array();
            }
        } else {
            $status = 250;
            $msg = __('failure');
            $update_data['spec_value_id'] = array();
        }
        return $this->data->addBody(-140, $update_data, $msg, $status);
    }

    //获取商品是否需要审核
    public function getGoodsVerify()
    {
        $data['goods_verify_flag'] = Web_ConfigModel::value('goods_verify_flag');
        $msg = '';
        $status = 200;
        return $this->data->addBody(-140, $data, $msg, $status);
    }

    //根据common_id获取商品基本信息
    public function getGoodsCommonByCommonId()
    {
        $Shop_BaseModel = new Shop_BaseModel();
        $Goods_CommonModel = new Goods_CommonModel();
        $Goods_CommonDetailModel = new Goods_CommonDetailModel();
        $Goods_CatModel = new Goods_CatModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $Goods_TypeModel = new Goods_TypeModel();
        $Goods_SpecValueModel = new Goods_SpecValueModel();
        $Goods_SpecModel = new Goods_SpecModel();
        $common_id = request_int('common_id');
        $user_id = request_string('user_id');;
        $shop_info = $Shop_BaseModel->getByWhere(['user_id' => $user_id]);
        if (!$shop_info) {
            $msg = '请登录';
            $status = 250;
            $common_data = array();
        } else {
            $shop_info = current($shop_info);
            $shop_id = $shop_info['shop_id'];
            $common_data = $Goods_CommonModel->listByWhere(array('shop_id' => $shop_id, 'common_id' => $common_id));

            if (empty($common_data)) {
                return;
            }
            $common_data = pos($common_data['items']);
            $common_detail_data = $Goods_CommonDetailModel->getCommonDetail($common_data['common_id']);
            $common_detail_data = pos($common_detail_data);
            $common_sell_time_d = strtotime($common_data['common_sell_time']);
            if ($common_sell_time_d && $common_sell_time_d > 0) {
                //读取上架时间
                $common_sell_time[0] = date('Y-m-d', $common_sell_time_d);
                $common_sell_time[1] = date('H', $common_sell_time_d);
                $common_sell_time[2] = date('i', $common_sell_time_d);
                $common_data['common_sell_time'] = $common_sell_time;
            } else {
                unset($common_data['common_sell_time']);
            }
            $cat_id = $common_data['cat_id'];
            $cat_base = $Goods_CatModel->getCat($cat_id);
            if (empty($cat_base)) {
                $msg = '商品下架或店铺关闭';
                $status = 250;
                $common_data = array();
            } else {
                //判断是否修改商品分类
                $action = request_string('action');
                if (!empty($action) && $action == 'edit_goods_cat') {
                    $cat_id = request_int('cat_id');
                }
                if ($common_data['supply_shop_id']) {
                    $shop_id = $common_data['supply_shop_id'];
                    //如果是代发货，使用代发货的商家
                    $supplier_common_info = $Goods_CommonModel->getOne($common_data['common_parent_id']);
                    $common_data['transport_area_id'] = $common_data['product_is_behalf_delivery'] == 1 && $common_data['common_parent_id'] ? $supplier_common_info['transport_area_id'] : $common_data['transport_area_id'];
                    $common_data['common_limit'] = $supplier_common_info['common_limit'];
                }
                $data = $Goods_TypeModel->getTypeInfoByPublishGoods($cat_id, $shop_id); //商品属性、规格等

                $goods_base_data = $Goods_BaseModel->getByWhere(array('common_id' => $common_data['common_id'])); //取出商品规格值

                /**********构造spec数据S**********/
                /**
                 *  [
                 *      [
                 *          spec_id=> spec_id,
                 *          spec_name=> spec_name,
                 *          spec_values=> [
                 *                          spec_value_id=> spec_value_id,
                 *                          spec_value_name=> spec_value_name,
                 *                          id=> spec_value_id
                 *                        ]
                 *      ]
                 * ]
                 */
                $spec_names = $common_data['common_spec_name'];
                $spec_values = $common_data['common_spec_value'];
                $spec = [];
                if (is_array($spec_names)) {
                    foreach ($spec_names as $spec_id => $spec_name) {
                        foreach ($spec_values as $spec_value_spec_id => $spec_value) {
                            if ($spec_id == $spec_value_spec_id) {
                                $arr = [
                                    'spec_id' => $spec_id,
                                    'spec_name' => $spec_name,
                                    'spec_values' => []
                                ];
                                foreach ($spec_value as $spec_value_id => $spec_value_name) {
                                    array_push($arr['spec_values'], [
                                        'spec_value_id' => $spec_value_id,
                                        'spec_value_name' => $spec_value_name,
                                        'id' => $spec_value_id,
                                        'spec_id' => $spec_id,
                                    ]);
                                }
                                array_push($spec, $arr);
                            }
                        }
                    }
                }
                $goods_spec = array_column($spec, 'spec_values');
                $specs = [];
                foreach ($goods_spec as $key => $value) {
                    foreach ($value as $k => $v) {
                        $specs[] = $v;
                    }
                }
                foreach ($specs as $k => $v) {
                    $spec_data[$v['spec_id']][$v['spec_value_id']] = $v['spec_value_name'];
                }

                foreach ($spec_data as $k => $v) {
                    $sets[] = $v;
                }

                $result = array();
                for ($i = 0, $count = count($sets); $i < $count - 1; $i++) {
                    // 初始化
                    if ($i == 0) {
                        $result = $sets[$i];
                    }
                    // 保存临时数据
                    $tmp = array();
                    // 结果与下一个集合计算笛卡尔积
                    foreach ($result as $key => $res) {
                        foreach ($sets[$i + 1] as $k => $set) {
                            $tmp[$key . '_' . $k] = $res . ';' . $set;
                        }
                    }
                    // 将笛卡尔积写入结果
                    $result = $tmp;
                }
                $spec_value = '';
                foreach ($result as $r_k => $r_v) {
                    $spec_value .= $r_v . ' ';
                }
                $common_data['spec_val'] = $common_data['common_spec_value'];
                $common_data['spec'] = $result;
                $common_data['specs'] = $specs;
                $common_data['spec_value'] = $spec_value;
                //获取运费模板信息
                $template_model = new Transport_TemplateModel();
                $transport_template = $template_model->getOpenTemplate($shop_id);
                //获取售卖区域信息
                if ($common_data['transport_area_id']) {
                    $area_model = new Transport_AreaModel();
                    $transport_area = $area_model->getOne($common_data['transport_area_id']);
                    $common_data['transport_area_name'] = $transport_area['name'];
                } else {
                    $common_data['transport_area_name'] = __('未设置');
                }
                $common_data['shop_info'] = $shop_info;
                $msg = '';
                $status = 200;
            }
        }

        //查询对应common_id下的goods
        $Goods_BaseModel = new Goods_BaseModel();
        $goods_list = $Goods_BaseModel->getBaseByCommonId($common_id);
        $goods_spec = [];
        foreach ($goods_list as $k => $v) {
            $spec = current($v['goods_spec']);
            $spec_key = '';
            foreach ($spec as $sk => $sv) {
                $spec_key .= $sk . '_';
            }
            $skey = substr($spec_key, 0, strlen($spec_key) - 1);
            $goods_spec[$skey]['goods_spec'] = implode(';', $spec);
            $goods_spec[$skey]['goods_id'] = $k;
            $goods_spec[$skey]['goods_market_price'] = $v['goods_market_price'];
            $goods_spec[$skey]['goods_price'] = $v['goods_price'];
            $goods_spec[$skey]['goods_stock'] = $v['goods_stock'];
            $goods_spec[$skey]['goods_alarm'] = $v['goods_alarm'];
            $goods_spec[$skey]['goods_sku'] = $v['goods_sku'];
        }
        $common_data['goods_spec'] = $goods_spec;

        //查找对应规格
        $skey = [];
        foreach ($common_data['common_spec_name'] as $k => $v) {
            $skey[] = $k;
        }
        $goods_spec_val = $Goods_SpecModel->getByWhere(array('spec_id:IN' => $skey));
        foreach ($goods_spec_val as $ks => $vs) {
            $val = $Goods_SpecValueModel->spec_value($shop_id, $ks);
            $goods_spec_val[$ks]['spec_values'] = $val;
        }
        $common_data['spec_name'] = $goods_spec_val;
        return $this->data->addBody(-140, $common_data, $msg, $status);
    }

    //编辑图片
    public function editGoodsImageManage()
    {
        $common_id = request_int('common_id');
        if ($common_id) {
            $data = $this->goodsImageManage($common_id);
            $color = [];
            if (!empty($data['color'])) {
                $color = $data['color'];
            }
            $result = [];
            $result['color'] = $color;
            $result['common_image'] = $data['common_data']['common_image'];
            $result['color_images'] = $data['color_images'];
            $result['goods_images'] = $data['goods_images'];
            $msg = 'success';
            $status = 200;
        } else {
            $result = [];
            $msg = 'failure';
            $status = 250;
        }

        return $this->data->addBody(-140, $result, $msg, $status);
    }

    public function goodsImageManage($common_id)
    {
        $data = array();
        $Goods_CommonModel = new Goods_CommonModel();
        $Goods_SpecModel = new Goods_SpecModel();
        $Goods_SpecModel = new Goods_SpecModel();
        $common_data = $Goods_CommonModel->getCommon($common_id);
        $common_data = pos($common_data);
        $data['common_data'] = $common_data;
        $spec_cond['spec_name:LIKE'] = '颜色';
//            $spec_cond['spec_readonly'] = 1;
        $readonly_data = $Goods_SpecModel->getByWhere($spec_cond);
        $readonly_data = pos($readonly_data);
        //取出颜色
        $Goods_ImagesModel = new Goods_ImagesModel();
        $goods_images = $Goods_ImagesModel->getByWhere(array('common_id' => $common_id));
        //如果是分销商品而且没有设置过商品图片，则默认显示分销商品的图片
        if ($common_data['supply_shop_id'] > 0 && $common_data['common_parent_id'] > 0 && !$goods_images) {
            $goods_images = $Goods_ImagesModel->getByWhere(array('common_id' => $common_data['common_parent_id']));
        }
        if (!empty($common_data['common_spec_value'])) {
            //spec_id = 1 => 是系统默认只读属性: 颜色
            if (!empty($common_data['common_spec_value'][$readonly_data['spec_id']])) {
                $color = $common_data['common_spec_value'][$readonly_data['spec_id']];
                $data['color'] = $color;
                foreach ($color as $key => $val) {
                    foreach ($goods_images as $k => $v) {
                        if ($key == $v['images_color_id']) {
                            $color_images[$key][] = $v;
                            unset($goods_images[$k]);
                        }
                    }
                }
            }
        }
        if (empty($color_images)) {
            foreach ($goods_images as $key => $val) {
                if ($val['images_is_default'] == Goods_ImagesModel::IMAGE_DEFAULT) {
                    $image_default = $goods_images[$key];
                    unset($goods_images[$key]);
                    array_unshift($goods_images, $image_default);
                    break;
                }
            }
            $data['goods_images'] = array_values($goods_images);
        } else {
            foreach ($color_images as $key => $val) {
                foreach ($val as $k => $v) {
                    if ($v['images_is_default'] == Goods_ImagesModel::IMAGE_DEFAULT) {
                        $image_default = $color_images[$key][$k];
                        unset($color_images[$key][$k]);
                        array_unshift($color_images[$key], $image_default);
                        break;
                    }
                }
            }
            $data['color_images'] = $color_images;
        }
        return $data;
    }

    public function saveGoodsImage()
    {
        $image_list = request_row('image');
        $common_id = request_int('common_id');
        $is_color = request_int('is_color');
        if (!empty($image_list)) {
            $Goods_CommonModel = new Goods_CommonModel();
            $goodsImagesModel = new Goods_ImagesModel();
            $images = $goodsImagesModel->getByWhere(array('common_id' => $common_id));
            $images_ids = array_column($images, 'id');
            $goodsImagesModel->removeImages($images_ids);
            $image_data['shop_id'] = Perm::$shopId;
            $image_data['common_id'] = $common_id;
            $num = 0;
            foreach ($image_list as $key => $val) {
                $num++;
                foreach ($val as $k => $v) {
                    if (!empty($is_color)) {
                        $image_data['images_color_id'] = $key;
                    }
                    $image_data['images_image'] = $v['name'];
                    $image_data['images_displayorder'] = $v['displayorder'];
                    $image_data['images_is_default'] = $v['default'];
                    //图片只有都有和都没有两种情况
                    if (!empty($v['name'])) {
                        if ($v['default'] == 1) {
                            $Goods_CommonModel->editCommon($common_id, array('common_image' => $v['name']));
                        }
                        $flag = $goodsImagesModel->addImages($image_data, true);
                        $flags[] = $flag;
                    } else {
                        $common_image = $Goods_CommonModel->getOne($common_id);
                        if ($num == 1 && $k == 0) {
                            $image_data['images_is_default'] = Goods_ImagesModel::IMAGE_DEFAULT;
                            $image_data['images_image'] = $common_image['common_image'];
                        } elseif ($num > 1 && $k == 0) {
                            $image_data['images_image'] = $common_image['common_image'];
                        }
                        $flag = $goodsImagesModel->addImages($image_data, true);
                        $flags[] = $flag;
                    }
                }
                unset($new0);
                unset($new1);
                unset($new_val);
            }
            $data = $flags;
            $msg = 'success';
            $status = 200;
        } else {
            $data = [];
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }
    public function cat()
    {
        $Goods_CatModel = new Goods_CatModel();

        if (isset($_REQUEST['cat_parent_id']))
        {
            $cat_parent_id = request_int('cat_parent_id', 0);

            $data_rows     = $Goods_CatModel->getCatTreeData($cat_parent_id, false, 1);
            $data['items'] = array_values($data_rows);
        }
        else
        {
            $data = $Goods_CatModel->getCatTree();

            if ( request_string('filter') )
            {
                $Goods_CatModel->filterCatTreeData( $data['items'] );
                $data['items'] = array_values($data['items']);
            }
        }

        if (0 == $cat_parent_id)
        {
            $Mb_CatImageModel = new Mb_CatImageModel();

            $cat_img_rows = $Mb_CatImageModel->getByWhere(array());
            //$cat_img_rows = $Mb_CatImageModel->getByWhere(array('cat_id'=>$cat_id_row));

            $img_row = array();

            foreach ($cat_img_rows as $id=>$cat_img_row)
            {
                $img_row[$cat_img_row['cat_id']] = $cat_img_row['mb_cat_image'];
            }

            foreach ($data['items'] as $k=>$item)
            {
                if (isset($img_row[$item['cat_id']]))
                {
                    $data['items'][$k]['cat_pic'] = $img_row[$item['cat_id']];

                }
            }
        }

        $this->data->addBody(-140, $data);
    }
}

?>
