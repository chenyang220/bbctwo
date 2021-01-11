<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
    
    /**
     * Api接口, 让App等调用
     *
     *
     * @category   Game
     * @package    User
     * @author     Yf <service@yuanfeng.cn>
     * @copyright  Copyright (c) 2015远丰仁商
     * @version    1.0
     * @todo
     */
    class Api_Goods_GoodsCtl extends Api_Controller
    {
        /**
         * 验证API是否正确
         *
         * @access public
         */
        public function listCommon()
        {
            $page = request_int('page', 1);
            $rows = request_int('rows', 20);
//            $page = $_SERVER['page'] ? $_SERVER['page'] : 1;
//            $rows = $_SERVER['rows'] ? $_SERVER['rows'] : 200;
            $order_row = array();
            $sidx = request_string('sidx');
            $sord = request_string('sord', 'asc');
            
            if ($sidx) {
                $order_row[$sidx] = $sord;
            }
            
            $cond_row = array();
            
            if (-1 != request_int('cat_id', -1)) {
                $cond_row['cat_id'] = request_int('cat_id');
            }
            
            if (-1 != request_int('brand_id', -1)) {
                $cond_row['brand_id'] = request_int('brand_id');
            }
            
            if (-1 != request_int('common_state', -1)) {
                $cond_row['common_state'] = request_int('common_state');
            }
            
            if (-1 != request_int('common_verify', -1)) {
                $cond_row['common_verify'] = request_int('common_verify');
            }
            
            if (request_int('common_id')) {
                $cond_row['common_id'] = request_int('common_id');
            }
            
            if (request_string('common_name')) {
                $cond_row['common_name:LIKE'] = '%' . request_string('common_name') . '%';
            }
            
            if (request_string('shop_name')) {
                $cond_row['shop_name:LIKE'] = '%' . request_string('shop_name') . '%';
            }
            if (request_string('type_wxapp') == 'wxapp') {
                $cond_row['common_is_tuan'] = 0;
            }
            if (-1 != request_int('brand_id', -1)) {
                $cond_row['brand_id'] = request_int('brand_id');
            }
            //分站筛选
            $sub_site_id = request_int('sub_site_id');
            $sub_flag = true;
            if ($sub_site_id > 0) {
                //获取站点信息
                $Sub_SiteModel = new Sub_SiteModel();
                $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
                if (!$sub_site_district_ids) {
                    $sub_flag = false;
                } else {
                    $cond_row['district_id:IN'] = $sub_site_district_ids;
                }
            }
            if ($sub_flag == false) {
                $status = 250;
                $msg = __('分站信息获取失败');
                $this->data->addBody(-140, array(), $msg, $status);
            } else {
                $Goods_CommonModel = new Goods_CommonModel();
                $data = $Goods_CommonModel->getCommonList($cond_row, $order_row, $page, $rows);
                $data = $this->countStock($data);
                if ($data) {
                    $status = 200;
                    $msg = __('success');
                } else {
                    $status = 250;
                    $msg = __('没有满足条件的结果哦');
                }
                $this->data->addBody(-140, $data, $msg, $status);
            }
        }
        
        /**
         * 是否有商品达到预警值
         * author xuexu
         *
         * @param array $data
         * return array
         */
        public function countStock($data)
        {
            $goods_base = new Goods_Base();
            foreach ($data['items'] as $k => $v) {
                foreach ($v['goods_id'] as $gid) {
                    $goods = $goods_base->getOne($gid['goods_id']);
                    $isAlarm = false;
                    if ($isAlarm == false) {
                        if ($goods['goods_stock'] <= $goods['goods_alarm']) {
                            $isAlarm = true;
                        }
                    }
                }
                $data['items'][$k]['isAlarm'] = $isAlarm;
            }
            return $data;
        }
        
        /*
         * 获取该用户下所有的商品信息
         *
         */
        public function getGoodsList()
        {
            $cond_row['shop_id'] = 1;
            
            if ($cond_row['shop_id']) {
                $Goods_BaseModel = new Goods_BaseModel();
                $data = $Goods_BaseModel->getByWhere($cond_row);
                $this->data->addBody(-140, $data);
            }
            
        }
        
        /**
         * 取得商品分类信息
         *
         * @access public
         */
        public function getCatList()
        {
            if (-1 != request_int('cat_id', -1)) {
                $cond_row['cat_id'] = request_int('cat_id');
            }
            
            $order_row = array(
                'cat_displayorder',
                'ASC'
            );
            
            $Goods_CatModel = new Goods_CatModel();
            $data = $Goods_CatModel->getCatList($cond_row['cat_id'], $order_row);
            $this->data->addBody(-140, $data);
        }
        
        /**
         * 取得商品分类信息
         *
         * @access public
         */
        public function getBrand()
        {
            $order_row = array(
                'brand_displayorder',
                'ASC'
            );
            
            $Goods_BrandModel = new Goods_BrandModel();
            $data = $Goods_BrandModel->getBrandList(array(), $order_row, 1, 10000);
            $this->data->addBody(-140, $data);
        }
        
        /**
         * 取得店铺信息
         *
         * @access public
         */
        public function getShop()
        {
            $common_id = request_int('common_id');
            $Goods_CommonModel = new Goods_CommonModel();
            $data = $Goods_CommonModel->getOne($common_id);
            $shop_id = $data['shop_id'];
            unset($data);
            $this->data->addBody(-140, array('shop_id' => $shop_id));
        }
        
        /**
         * 取得各种下拉框
         *
         * @access public
         */
        public function getStateCombo()
        {
            $cache_group = 'default';
            $Cache = Yf_Cache::create($cache_group);
            $cache_key_time = md5(Yf_Registry::get('url').$this->ctl.$this->met.'key'); //固定值,判断是否需要重新缓存数据
            $cache_key_time_value = $Cache->get($cache_key_time);
            $cache_value = md5(Yf_Registry::get('url').$this->ctl.$this->met.'value');  //缓存的key
            if($cache_key_time_value != date('Y-m-d')){
                $Goods_CommonModel = new Goods_CommonModel();
                $data = $Goods_CommonModel->getStateCombo();
                //缓存
                $Cache->save(date('Y-m-d'), $cache_key_time);
                $Cache->save(json_encode($data), $cache_value);
            } else {
                $res = $Cache->get($cache_value,$cache_group);
                $data = json_decode($res,true);
            }
            $this->data->addBody(-140, $data);
        }
        
        /**
         * 编辑状态
         *
         * @access public
         */
        public function editCommonState()
        {
            $Goods_CommonModel = new Goods_CommonModel();
            
            $common_id = request_int('common_id');
            $common_state = request_int('common_state');
            $common_state_remark = request_string('common_state_remark');
            
            if ($common_id && array_key_exists($common_state, Goods_CommonModel::$stateMap)) {
                
                $flag = $Goods_CommonModel->editCommon($common_id, array('common_state' => $common_state));
                
                if ($flag !== false) {
                    $msg = __('success');
                    $status = 200;
                    
                    //将用户发布或者修改的商品同步到im中
                    $key = Yf_Registry::get('im_api_key');
                    $url = Yf_Registry::get('im_api_url');
                    $im_app_id = Yf_Registry::get('im_app_id');
                    $formvars = array();
                    
                    $formvars['goods_common_id'] = $common_id;
                    $formvars['app_id'] = $im_app_id;
                    
                    $formvars['goods_status'] = $common_state;
                    $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Goods&met=editUserGoodsStatus&typ=json', $url), $formvars);
                    
                    $shop = $Goods_CommonModel->getOne($common_id);
                    $shop_id = $shop['shop_id'];
                    $Shp_BaseModel = new Shop_BaseModel();
                    $shop_data = $Shp_BaseModel->getOne($shop_id);
                    
                    //商品违规被下架
                    //[des][common_id]
                    $message = new MessageModel();
                    $message->sendMessage('Commodity violation is under the shelf', $shop_data['user_id'], $shop_data['user_name'], $order_id = NULL, $shop_name = NULL, 1, 2, $end_time = Null, $common_id = $common_id, $goods_id = NULL, $common_state_remark);
                    
                    //加入拼团商品判断，商品违规下架后，相关拼团活动取消
                    $pintuanModel = new PinTuan_Base;
                    $pintuanModel->cancelByCommonId($common_id);

                    //砍价活动的状态,商品违规下架后，相关砍价活动取消
                    $goodsBaseModel = new Goods_BaseModel();
                    $goods_item = $goodsBaseModel->getByWhere(array("common_id:IN" => $common_id));
                    $goods_ids = array_column($goods_item, 'goods_id');
                    $Bargain_BaseModel = new Bargain_BaseModel();
                    $Bargain_BaseModel->editBargainStatus(array_unique($goods_ids));

                } else {
                    $msg = __('failure');
                    $status = 250;
                }
            } else {
                $msg = __('请求参数有误');
                $status = 250;
                
            }
            
            $this->data->addBody(-140, array(), $msg, $status);
        }
        
        /**
         * 编辑状态
         *
         * @access public
         */
        public function editCommonVerify()
        {
            $Goods_CommonModel = new Goods_CommonModel();
            
            $common_id = request_int('common_id');
            $common_verify = request_int('common_verify');
            $common_verify_remark = request_string('common_verify_remark');
            $goods_common_data = $Goods_CommonModel->getOne($common_id);
//		echo '<pre>';print_r($goods_common_data);exit;
            if ($common_id && array_key_exists($common_verify, Goods_CommonModel::$verifyMap)) {
                //如果审核通过，且商品是外部导入商品，修改商品状态
                if ($common_verify == Goods_CommonModel::GOODS_VERIFY_ALLOW && $goods_common_data['common_goods_from'] == Goods_CommonModel::GOODS_FROM_OUTSIDEIMPORT) {
                    $flag = $Goods_CommonModel->editCommon($common_id, array('common_verify' => $common_verify, 'common_state' => Goods_CommonModel::GOODS_STATE_WAITING_ADD));
                } else {
                    $flag = $Goods_CommonModel->editCommon($common_id, array('common_verify' => $common_verify));
                }
                
                if ($flag !== false) {
                    $msg = __('success');
                    $status = 200;
                    
                    //将用户发布或者修改的商品同步到im中
                    $key = Yf_Registry::get('im_api_key');
                    $url = Yf_Registry::get('im_api_url');
                    $im_app_id = Yf_Registry::get('im_app_id');
                    $formvars = array();
                    
                    $formvars['goods_common_id'] = $common_id;
                    $formvars['app_id'] = $im_app_id;
                    
                    $formvars['goods_verify'] = $common_verify;
                    $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Goods&met=editUserGoodsVerify&typ=json', $url), $formvars);
                    
                    if ($common_verify == 0) {
                        $shop = $Goods_CommonModel->getOne($common_id);
                        $shop_id = $shop['shop_id'];
                        $Shp_BaseModel = new Shop_BaseModel();
                        $shop_data = $Shp_BaseModel->getOne($shop_id);
                        
                        //商品审核失败提醒
                        //[des][common_id]
                        $message = new MessageModel();
                        $message->sendMessage('Commodity audit failed to remind', $shop_data['user_id'], $shop_data['user_name'], $order_id = NULL, $shop_name = NULL, 1, 1, $end_time = Null, $common_id = $common_id, $goods_id = NULL, $common_verify_remark);
                    }
                } else {
                    $msg = __('failure');
                    $status = 250;
                }
            } else {
                $msg = __('请求参数有误');
                $status = 250;
                
            }
            
            $this->data->addBody(-140, array('common_verify' => $common_verify), $msg, $status);
        }
        
        /**
         * 取得各种下拉框
         *
         * @access public
         */
        public function removeCommon()
        {
            $Goods_CommonModel = new Goods_CommonModel();

            $common_id = request_string('common_id');
            $common_id_row = explode(',', $common_id);
            //判断商品是否有参加团购活动
            $group_buy_base_model = new GroupBuy_BaseModel();
            $group_buy_base = $group_buy_base_model->getByWhere(array('common_id' => $common_id));
            if (!empty($group_buy_base)) {
                $msg = __('该商品正在参加团购！');
                $status = 250;
            } else {
                $Goods_CommonModel->sql->startTransactionDb();
                $rs_row = array();
                $flag1 = $Goods_CommonModel->removeCommon($common_id_row);
                check_rs($flag1, $rs_row);

                $goodsBaseModel = new Goods_BaseModel();
                $goods_item = $goodsBaseModel->getByWhere(array("common_id:IN" => $common_id));
                $goods_ids = array_column($goods_item, 'goods_id');
                //砍价活动的状态
                $Bargain_BaseModel = new Bargain_BaseModel();
                $bargain_flag = $Bargain_BaseModel->editBargainStatus(array_unique($goods_ids));
                check_rs($bargain_flag, $rs_row);
                $flag = is_ok($rs_row);

                if ($flag && $Goods_CommonModel->sql->commitDb()) {
                    $msg = __('success');
                    $status = 200;
                } else {
                    $Goods_CommonModel->sql->rollBackDb();
                    $msg = __('failure');
                    $status = 250;
                }
            }

            $this->data->addBody(-140, array('id' => $common_id_row), $msg, $status);
        }
        
        /**
         * 取得商品SKU信息
         *
         * @access public
         */
        public function getGoodsInfo()
        {
            $Goods_BaseModel = new Goods_BaseModel();
            $Goods_SpecValueModel = new Goods_SpecValueModel();
            $Goods_SpecModel = new Goods_SpecModel();
            
            $common_id = request_int('common_id');
            $data = $Goods_BaseModel->getBaseListByCommonId($common_id);
            foreach ($data['items'] as $k => $goods) {
              foreach ($goods['goods_spec'] as $kv => $vv) {
                  foreach ($vv as $id => $goods_spec) {
                      $goods_value_info = $Goods_SpecValueModel->getOne($id);
                      $spec_id = $goods_value_info['spec_id'];
                      $goods_specc = $Goods_SpecModel->getOne($spec_id);
                      $spec_name = $goods_specc['spec_name'];
                      $data['items'][$k]['goods_spec_list'] .= $spec_name . ':' . $goods_spec.'|';
                  }
              }
            }
            $this->data->addBody(-140, $data);
        }
        
        public function listGoodsRecommend()
        {
            $page = request_int('page');
            $rows = request_int('rows');
            $cond_row = array();
            $order_row = array();
            
            $Goods_RecommendModel = new Goods_RecommendModel();
            $Goods_CatModel = new Goods_CatModel();
            $data = $Goods_RecommendModel->getRecommendList($cond_row, $order_row, $page, $rows);
            $items = $data['items'];
            unset($data['items']);
            if (!empty($items)) {
                foreach ($items as $key => $value) {
                    $cat_id = $value['goods_cat_id'];
                    $items[$key]['goods_cat_name'] = $Goods_CatModel->getNameByCatid($value['goods_cat_id']);
                }
            }
            $data['items'] = $items;
            if ($items) {
                $msg = __('success');
                $status = 200;
            } else {
                $msg = __('failure');
                $status = 250;
            }
            $this->data->addBody(140, $data, $msg, $status);
        }
        
        //商品推荐
        public function getCatGoodsList()
        {
            $Goods_CommonModel = new Goods_CommonModel();
            
            $goods_cat_id = request_int('goods_cat_id');
            $goods_name = request_string('goods_name');
            
            $Yf_Page = new Yf_Page();
            $Yf_Page->listRows = 24;
            $rows = $Yf_Page->listRows;
            $offset = request_int('firstRow', 0);
            $page = ceil_r($offset / $rows);
            
            $cond_row = array();
            $order_row = array();
            
            if ($goods_cat_id > 0) {
                $cond_row['cat_id'] = $goods_cat_id;
            }
            
            if ($goods_name) {
                $cond_row['common_name:like'] = '%' . $goods_name . '%';
            }
            
            $cond_row['shop_status'] = Shop_BaseModel::SHOP_STATUS_OPEN;
            $cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
            $cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
            $cond_row['product_distributor_flag'] = 0; //是否为分销商品 0=>不属于分销商品
            
            $data = $Goods_CommonModel->getCommonList($cond_row, $order_row, $page, $rows);
            
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav = $Yf_Page->ajaxprompt();
            
            $this->data->addBody(-140, $data);
        }
        
        //新增商品推荐
        public function addGoodsRecommend()
        {
            $Goods_RecommendModel = new Goods_RecommendModel();
            $Goods_CatModel = new Goods_CatModel();
            $add_data = array();
            $add_data['goods_cat_id'] = request_int('goods_cat_id');
            $cat_id = request_int('goods_cat_id');
            $goods_id_rows = request_row('goods_id_list');
            $add_data['common_id'] = $goods_id_rows;
            $add_data['recommend_num'] = count($goods_id_rows);
            $add_data['sub_site_id'] = request_int('sub_site_id');
            //判断分类是否已有推荐
            $data_old = $Goods_RecommendModel->getByWhere(array('goods_cat_id' => $cat_id));
            if (empty($data_old)) {
                $goods_recommend_id = $Goods_RecommendModel->addRecommend($add_data, true);
                
                if ($goods_recommend_id) {
                    $add_data['id'] = $goods_recommend_id;
                    
                    $msg = __('success');
                    $status = 200;
                } else {
                    $msg = __('failure');
                    $status = 250;
                }
                
                $add_data['goods_cat_name'] = $Goods_CatModel->getNameByCatid($cat_id);
                
            } else {
                $msg = __('该分类已有推荐');
                $status = 250;
                $add_data = array();
            }
            
            $this->data->addBody(-140, $add_data, $msg, $status);
        }
        
        public function removeGoodsRecommend()
        {
            $Goods_RecommendModel = new Goods_RecommendModel();
            $id = request_int('id');
            $flag = $Goods_RecommendModel->removeRecommend($id);
            if ($flag) {
                $msg = __('success');
                $status = 200;
            } else {
                $msg = __('failure');
                $status = 250;
            }
            
            $this->data->addBody(-140, array(), $msg, $status);
        }
        
        public function getGoodsRecommendById()
        {
            $Goods_RecommendModel = new Goods_RecommendModel();
            $Goods_CommonModel = new Goods_CommonModel();
            $id = request_int('id');
            $re = array();
            
            if ($id) {
                $data = $Goods_RecommendModel->getOne($id);
                $goods_id_rows = $data['common_id'];
                $cat_id = $data['goods_cat_id'];
                $re = $Goods_CommonModel->getCommonList(array('common_id:in' => $goods_id_rows));
                $re['goods_cat_id'] = $cat_id;
            }
            if (!empty($re)) {
                $msg = __('success');
                $status = 200;
            } else {
                $msg = __('failure');
                $status = 250;
            }
            
            $this->data->addBody(-140, $re, $msg, $status);
        }
        
        public function editGoodsRecommend()
        {
            $Goods_RecommendModel = new Goods_RecommendModel();
            $Goods_CatModel = new Goods_CatModel();
            $edit_data = array();
            
            $id = request_int('id');
            $goods_cat_id = request_int('goods_cat_id');
            $goods_id_rows = request_row('goods_id_list');
            
            if ($goods_cat_id != -1) {
                $edit_data['goods_cat_id'] = $goods_cat_id;
            }
            $edit_data['common_id'] = $goods_id_rows;
            $edit_data['recommend_num'] = count($goods_id_rows);
            
            $flag = $Goods_RecommendModel->editRecommend($id, $edit_data);
            
            if ($flag !== false) {
                $msg = __('success');
                $status = 200;
            } else {
                $msg = __('failure');
                $status = 250;
            }
            $data = array();
            $data_new = $Goods_RecommendModel->getOne($id);
            $data['goods_cat_name'] = $Goods_CatModel->getNameByCatid($data_new['goods_cat_id']);
            $data['goods_recommend_id'] = $data_new['goods_recommend_id'];
            $data['recommend_num'] = $data_new['recommend_num'];
            $data['id'] = $data_new['goods_recommend_id'];
            $this->data->addBody(-140, $data, $msg, $status);
        }
        
        //审核商品
        public function checkGoods()
        {
            $Goods_CommonModel = new Goods_CommonModel();
            
            $ids = request_string('id');
            $id_rows = explode(',', $ids);
            
            if (!empty($id_rows)) {
                foreach ($id_rows as $key => $value) {
                    $common_id = $value;
                    $goods_common = array();
                    $goods_common['common_verify'] = $Goods_CommonModel::GOODS_VERIFY_ALLOW;
                    $flag = $Goods_CommonModel->editCommon($common_id, $goods_common);
                }
            }
            $this->data->addBody(-140, $id_rows);
        }
        
        public function upGoods()
        {
            $Goods_CommonModel = new Goods_CommonModel();
            $Goods_BaseModel = new Goods_BaseModel();
            
            $ids = request_string('id');
            $id_rows = explode(',', $ids);
            
            if (!empty($id_rows)) {
                foreach ($id_rows as $key => $value) {
                    $common_id = $value;
                    $goods_common = array();
                    $goods_common['common_state'] = $Goods_CommonModel::GOODS_STATE_NORMAL;
                    $flag = $Goods_CommonModel->editCommon($common_id, $goods_common);
                    $goods_data = array();
                    $goods_data = $Goods_BaseModel->getByWhere(array('common_id' => $common_id));
                    if (!empty($goods_data)) {
                        foreach ($goods_data as $k => $v) {
                            $goods_id = $v['goods_id'];
                            $edit_goods = array();
                            $edit_goods['goods_is_shelves'] = $Goods_BaseModel::GOODS_UP;
                            $id_rows[] = $goods_id;
                            $flags = $Goods_BaseModel->editBaseFalse($goods_id, $edit_goods);
                        }
                    }
                }
            }
            
            $this->data->addBody(-140, $id_rows);
        }

        /**
         * 通过订单ids得到订单商品数组
         * 
         * @author fzh
         */
        public function getOrderGoodsByIds(){
           $orderIds = request_row('orderIds');
           $orderGoodsModel = new Order_GoodsModel();
           $data = $orderGoodsModel->getOrderGoodsByIds($orderIds);
           $this->data->addBody(-140, $data);
        }

        /*
         * 新增商品或者修改商品接口
         * 第三方通过该接口，将生成新的商品
         * */
        public function AddOrEditGoodsApi()
        {
            $common_id = request_int('common_id'); //common_id
            $spec_data = request_row('spec');  //规格商品信息
            $common_code = request_string('code');//商家货号唯一
            $action = request_string('action');//edit ,  add
            $shop_id = request_int('shop_id');  //店铺id
            $cat_id = request_int('cat_id');//商品分类id
            $sgcate_id = request_row('sgcate_id'); //店铺分类
            $cat_name = request_string('cat_name'); //分类名称
            $common_name = request_string('name'); //商品名称
            $brand_id = request_int('brand_id');  //品牌id
            $promotion_tips = request_string('promotion_tips'); //商品广告词
            $imagePath = request_string('imagePath'); //商品主图
            $videoPath = request_string('videoPath');  //商品视频
            $price = request_float('price');  //商品价格
            $market_price = request_float('market_price');  //市场价格
            $cost_price = request_float('cost_price');  //成本价
            $stock = request_int('stock');  //商品库存
            $alarm = request_int('alarm');  //库存预警值
            $formatid_top = request_int('formatid_top');  //顶部关联版式
            $formatid_bottom = request_int('formatid_bottom');  //底部关联版式
            $cubage = request_float('cubage',1);  //商品重量
            $is_return = request_int('is_return');  //7天无理由退货
            $service = request_string('service');  //售后服务
            $packing_list = request_string('packing_list');  //包装清单
            $state = request_int('state');  //商品状态
            $is_recommend = request_string('is_recommend');  //是否推荐
            $common_property = request_row('property');  //商品属性
            $spec_name = request_string('spec_name');  //规格名称
            $province_id = request_string('province_id');//商品所在地 - 省
            $city_id = request_string('city_id');//商品所在地 - 市
            $transport_area_id = request_int('transport_area_id');//选择售卖区域
            $is_gv = request_int('is_gv'); //是否是虚拟商品
            $g_vindate = request_string('g_vindate'); //虚拟商品有效期
            $g_vinvalidrefund = request_int('g_vinvalidrefund'); //支持过期退款
            $starttime = request_string('starttime');  //定时发布商品
            $time_hour = request_string('hour'); //定时发布商品 - 时
            $time_minute = request_string('minute'); //定时发布商品 - 分
            $spec_val = request_row('spec_val'); //规格值
            $body = request_string('body');  //内容详情

            $is_limit = request_int('is_limit');
            //0不限购
            $goods_limit = $is_limit ? request_int('limit') : 0;

            /*供应商店铺*/
            $product_is_allow_update = request_int('product_is_allow_update'); //是否允许修改内容
            $product_is_allow_price = request_int('product_is_allow_price');   //是否允许商品价格
            $product_is_behalf_delivery = request_int('product_is_behalf_delivery'); //是否支持待发货
            $goods_recommended_min_price = request_float('goods_recommended_min_price');  //建议最低零售价
            $goods_recommended_max_price = request_float('goods_recommended_max_price');  //建议最高零售价
            $common_distributor_description = request_string('common_distributor_description');  //分销说明

            $fenxiao = request_row('fenxiao');

            $Goods_CommonModel = new Goods_CommonModel();
            $Shop_BaseModel = new Shop_BaseModel();
            $Goods_CatModel = new Goods_CatModel();
            $Goods_BrandModel = new Goods_BrandModel();
            $Upload_BaseModel = new Upload_BaseModel();
            $Goods_CommonDetailModel =  new Goods_CommonDetailModel();
            $Goods_BaseModel = new Goods_BaseModel();

            //判断商品货号是否唯一
            if ($common_code) {
                $common_code_list = $Goods_CommonModel -> getByWhere(array('common_code' => $common_code));
                if (count($common_code_list) > 1) {
                    throw new Exception('商家货号不能重复');
                }
            }

            $shop_base = $Shop_BaseModel -> getOne($shop_id);
            $common_data['shop_status'] = $shop_base['shop_status'];  //插入店铺状态

            $goods_cat_base = $Goods_CatModel -> getOne($cat_id);
            $shop_cat_id = ',';

            //店铺分类
            if (empty($sgcate_id)) {
                $shop_cat_id .= ',';
            } else {
                foreach ($sgcate_id as $key => $val) {
                    $shop_cat_id .= $val . ',';
                }
            }
            $matche_row = array();
            //有违禁词
            if (Text_Filter::checkBanned(request_string('name'), $matche_row) || Text_Filter::checkBanned(request_string('promotion_tips'), $matche_row) || Text_Filter::checkBanned(request_string('body'), $matche_row)) {
                return $this -> data -> addBody(-140, array(), __('含有违禁词'), 250);
            }
            //当前商品数量统计, 非自营店铺
            if ($shop_base['shop_self_support'] != Shop_BaseModel::SELF_SUPPORT_TRUE) {
                $shop = array();
                $shop['shop_id'] = $shop_base['shop_id'];
                $shop_base_row = $Shop_BaseModel -> getBaseOneList($shop);
                $goods_state_normal_num = $Goods_CommonModel -> getCommonStateNum($shop_base['shop_id'], -1);
                if (!empty($shop_base_row['shop_grade_row'])) {
                    $shop_grade_goods_limit = $shop_base_row['shop_grade_row']['shop_grade_goods_limit'];
                    $shop_grade_album_limit = $shop_base_row['shop_grade_row']['shop_grade_album_limit'];
                } else {
                    $shop_grade_goods_limit = 0;
                    $shop_grade_album_limit = 0;
                }
                if (0 != $shop_grade_goods_limit && $shop_grade_goods_limit <= $goods_state_normal_num) {
                    return $this -> data -> addBody(-140, array(), __('商品发布数量超出平台限制！'), 250);
                }
            }

            $shop_album_num = $Upload_BaseModel -> getUploadNum($shop['shop_id']);
            $common_data['shop_id'] = $shop_base['shop_id'];                        //店铺id
            $common_data['shop_name'] = $shop_base['shop_name'];                        //店铺名称
            $common_data['shop_cat_id'] = $shop_cat_id;                                //店铺分类id
            $common_data['type_id'] = $goods_cat_base['type_id'];                    //类型id
            $common_data['shop_self_support'] = $shop_base['shop_self_support'] == Shop_BaseModel::SELF_SUPPORT_TRUE ? 1 : 0;     //是否自营
            $common_data['cat_id'] = $cat_id;                    //商品分类id
            $common_data['cat_name'] = $cat_name;                    //商品分类
            $common_data['common_name'] = Text_Filter::filterWords($common_name);                        //商品名称
            $common_data['brand_id'] = $brand_id;                        //品牌id
            if (request_int('brand_id')) {
                $goods_brand = $Goods_BrandModel -> getOne($common_data['brand_id']);
                $common_data['brand_name'] = $goods_brand['brand_name'];                    //品牌名称
            }
            $common_data['common_promotion_tips'] = Text_Filter::filterWords($promotion_tips);                //商品广告词
            $common_data['common_image'] = $imagePath;                    //商品主图
            $common_data['common_video'] = $videoPath;                       //商品视频
            $common_data['common_price'] = $price;                        //商品价格
            $common_data['common_market_price'] = $market_price;                //市场价
            $common_data['common_cost_price'] = $cost_price;                    //成本价
            if ($common_data['common_price'] > $common_data['common_market_price']) {
                $msg = $shop_base['shop_type'] == 2 ? __('市场价不能低于供货价格') : __('市场价不能低于商品价格');
                return $this -> data -> addBody(-140, array(), $msg, 250);
            }
            $common_data['common_stock'] = $stock;                            //商品库存
            $common_data['common_alarm'] = $alarm;                            //库存预警值
            $common_data['common_code'] = $common_code;                                     //商家编号
            $common_data['common_formatid_top'] = $formatid_top;                    //顶部关联板式
            $common_data['common_formatid_bottom'] = $formatid_bottom;                //底部关联板式
            $common_data['common_cubage'] = $cubage;                        //商品重量
            $common_data['common_is_return'] = $is_return;                        //7天无理由退货
            if ($service) {
                $common_data['common_service'] = $service;                    //售后服务
                if(mb_strwidth($common_data['common_service']) > 407){
                    $msg    = __('售后服务信息最多输入200个字符');
                    $status = 250;
                    return $this->data->addBody(-140, array(), $msg, $status);
                }
            } else {
                $common_data['common_service'] = $shop_base['shop_common_service'];
            }
            $common_data['common_packing_list'] = $packing_list;                //包装清单
            $common_data['common_state'] = $state;                            //商品状态
            $common_data['common_is_recommend'] = $is_recommend;                //商品推荐
            $common_data['common_add_time'] = date('Y-m-d H:i:s', time());                    //商品添加时间
            //设置地区
            $common_data['district_id'] = $shop_base['district_id'];

            $common_data['common_limit'] = $goods_limit;
            $common_data['common_invoices'] = 1;                    //商品默认支持开发票

            //读取店铺关联的消费者保障服务
            $Goods_CommonModel->getShopContract($common_data,$shop_id);

            //商品所在地
            if ($province_id) {
                $common_location = array();
                $common_location[] = $province_id;
                if ($city_id) {
                    $common_location[] = $city_id;
                }
                $common_data['common_location'] = $common_location;
            }

            //售卖区域
            if ($transport_area_id) {
                $area_model = new Transport_AreaModel();
                $check_area = $area_model -> checkArea($transport_area_id, $shop_id);
                if ($check_area) {
                    return $this -> data -> addBody(-140, array(), __('售卖区域数据有误'), 250);
                }
            } else {
                return $this -> data -> addBody(-140, array(), __('请设置售卖区域'), 250);
            }
            $common_data['transport_area_id'] = $transport_area_id;
            //获取运费模板信息
            $template_model = new Transport_TemplateModel();
            $transport_template = $template_model -> getOpenTemplate($shop_id);
            if (!$transport_template) {
                return $this -> data -> addBody(-140, array(), __('请设置运费模板'), 250);
            }

            //本店分类
            if (!empty($sgcate_id)) {
                $common_data['shop_goods_cat_id'] = $sgcate_id;                                //shop_goods_cat_id
            }

            /* 只有可发布虚拟商品才会显示 S */
            $common_data['common_is_virtual'] = $is_gv;                                            //虚拟商品
            if ($is_gv == 1) {
                $common_data['common_virtual_date'] = $g_vindate;                        //虚拟商品有效期
                $common_data['common_virtual_refund'] = $g_vinvalidrefund;                    //支持过期退款
            }
            if (!empty($common_property)) {
                $common_data['common_property'] = $common_property;                    //属性
            }

            //定时发布商品
            if ($common_data['common_state'] == Goods_CommonModel::GOODS_STATE_TIMING) {
                $sell_time = "$starttime $time_hour:$time_minute:00";
                if (strtotime($sell_time) < time()) {
                    return $this -> data -> addBody(-140, array(), __('发布时间不能小于当前时间'), 250);
                }
                $common_data['common_sell_time'] = date('Y-m-d H:i:s', strtotime($sell_time));            //上架时间
            } elseif ($common_data['common_state'] == Goods_CommonModel::GOODS_STATE_NORMAL) {
                //如果是立即发布，则发布时间为当前添加时间
                $common_data['common_sell_time'] = $common_data['common_add_time'];
            }

            //规格值
            if (!empty($spec_val)) {
                $diff_spec_array = array_diff_key($spec_name, $spec_val);
                $flag_spec = empty($diff_spec_array);
                if (!empty($spec_name) && $flag_spec) {
                    $common_data['common_spec_name'] = $spec_name;                        //规格名称
                    $common_data['common_spec_value'] = $spec_val;                        //规格名称
                }
            }
            //判断发布的的商品是否需要审核
            if (in_array($common_data['common_state'], [Goods_CommonModel::GOODS_STATE_NORMAL, Goods_CommonModel::GOODS_STATE_TIMING, Goods_CommonModel::GOODS_STATE_OFFLINE])) { //正常发布、定时上架、放入仓库
                if (Web_ConfigModel::value('goods_verify_flag') == 0) { //商品是否需要审核
                    if ($common_data['common_state'] == Goods_CommonModel::GOODS_STATE_OFFLINE) {  //如果是放入仓库
                        $common_data['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;    //审核未通过
                    } else {
                        $common_data['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
                    }
                    //如果是外部导入商品且不需要审核，则将商品状态改为待上架
                    if ($common_data['common_goods_from'] == Goods_CommonModel::GOODS_FROM_OUTSIDEIMPORT) {
                        $common_data['common_state'] = Goods_CommonModel::GOODS_STATE_WAITING_ADD;
                    }
                } else {
                    $common_data['common_verify'] = Goods_CommonModel::GOODS_VERIFY_WAITING;
                }
            }
            //供应商店铺
            if ($shop_base['shop_type'] == 2) {
                $common_data['product_is_allow_update'] = $product_is_allow_update; //是否允许修改内容
                $common_data['product_is_allow_price'] = $product_is_allow_price;   //是否允许商品价格
                $common_data['product_is_behalf_delivery'] = $product_is_behalf_delivery; //是否支持待发货
                $common_data['product_distributor_flag'] = 1; //供应商店铺商品
                $common_data['goods_recommended_min_price'] = $goods_recommended_min_price;
                $common_data['goods_recommended_max_price'] = $goods_recommended_max_price;
                if ($common_data['goods_recommended_max_price'] < $common_data['goods_recommended_min_price']) {
                    return $this -> data -> addBody(-140, array(), __('最高零售价格不能低于最低零售价格'), 250);
                }
                $common_data['common_distributor_description'] = $common_distributor_description;
            }

            //关联版式
            if (!empty($formatid_top)) {
                $common_data['common_formatid_top'] = $formatid_top;
            }
            if (!empty($formatid_bottom)) {
                $common_data['common_formatid_bottom'] = $formatid_bottom;
            }

            //开启事务
            $Goods_CommonModel -> sql -> startTransactionDb();
            if ($common_property) {
                $property_index = new Goods_PropertyIndexModel();
                foreach ($common_property as $k => $v) {
                    $property_row = array(
                        'common_id' => $common_id,
                        'property_id' => $v[3],
                    );
                    $property = $property_index -> getOneByWhere($property_row);
                    $property_row['property_value_id'] = $v[1];
                    if (!$property) {
                        $flag = $property_index -> addPropertyIndex($property_row);
                    } else {
                        $flag = $property_index -> editPropertyIndex($property['goods_property_index_id'], $property_row);
                    }
                }
            }


            if ($action == 'edit') {
                //分销商分销商品商品修改权限
                $common_base = $Goods_CommonModel -> getOne($common_id);
                //判断商品是否修改限购
                if ($common_base['common_limit'] != $goods_limit) {
                    $common_data['common_edit_time'] = date('Y-m-d H:i:s', time());
                }
                if ($shop_base['shop_type'] == 1 && $common_base['product_is_allow_update'] == 0 && $common_base['product_is_allow_price'] == 1) {
                    $dist_allow_edit = array();
                    $dist_allow_edit['common_price'] = $common_data['common_price'];
                    $dist_allow_edit['common_market_price'] = $common_data['common_market_price'];
                    $edit_status = $Goods_CommonModel -> editCommon($common_id, $dist_allow_edit);
                } else {
                    if ($shop_base['shop_type'] == 1 && $common_base['product_is_allow_update'] == 1 && $common_base['product_is_allow_price'] == 0) {
                        unset($common_data['common_price']);
                        unset($common_data['common_market_price']);
                    } elseif ($shop_base['shop_type'] == 1 && $common_base['product_is_allow_update'] == 0 && $common_base['product_is_allow_price'] == 0) {
                        $common_data = array();
                    }
                    $edit_status = $Goods_CommonModel -> editCommon($common_id, $common_data);
                    //获取供应商商品对应的店家商品common_id,修改库存
                    foreach ($commons as $common) {
                        if($common['product_is_behalf_delivery'] == 1)
                        {
                            $common_cond['common_stock'] = $common_data['common_stock'];
                            $edit_falg = $Goods_CommonModel->editCommon($common['common_id'], $common_cond);
                        }
                    }
                }
                $data['action'] = 'edit';
                $data['property'] = $common_property;
            } else {
                $common_data['common_edit_time'] = date('Y-m-d H:i:s', time());
                $common_id = $Goods_CommonModel -> addCommon($common_data, true);
            }

            //同步分销
            $flag = true;
            if (Web_ConfigModel::value('Plugin_Fenxiao')) {
                $dat = [
                    'shop_id' => $shop_id,
                    'cat_id' => $cat_id,
                    'goods_id' => $common_id,
                    'values' => $fenxiao,
                ];
                try{
                    Fenxiao::getInstance() -> updateGoods($dat);
                }catch (Exception $e){
                    $flag = false;
                    $msg = $e->getMessage();
                }
            }
            if ($common_id && $flag && $Goods_CommonModel -> sql -> commitDb()) {
                //将用户发布或者修改的商品同步到im中
                $key = Yf_Registry::get('im_api_key');
                $url = Yf_Registry::get('im_api_url');
                $im_app_id = Yf_Registry::get('im_app_id');
                $formvars = array();
                $formvars['goods_common_id'] = $common_id;
                $formvars['app_id'] = $im_app_id;
                $formvars['user_id'] = $shop_base['user_id'];
                $formvars['goods_name'] = $common_data['common_name'];
                $formvars['goods_price'] = $common_data['common_price'];
                $formvars['goods_pic'] = $common_data['common_image'];
                $formvars['goods_url'] = Yf_Registry::get('shop_wap_url') . '/tmpl/product_detail.html?cid=' . $common_id;
                $formvars['goods_status'] = $common_data['common_state'];
                $formvars['goods_verify'] = $common_data['common_verify'];
                $formvars['time'] = get_date_time();

                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_User_Goods&met=addOrEditUserGoods&typ=json', $url), $formvars);

                $body = Text_Filter::filterWords($body);

                //内容详情
                if (true || !empty($body)) {
                    $common_detail_data['common_id'] = $common_id;
                    $common_detail_data['common_body'] = $body;
                    if ($action == 'edit') {
                        unset($common_detail_data['common_id']);
                        if ($Goods_CommonDetailModel -> getOne($common_id)) {
                            $Goods_CommonDetailModel -> editCommonDetail($common_id, $common_detail_data);
                        } else {
                            $Goods_CommonDetailModel -> addCommonDetail($common_detail_data);
                        }
                    } else {
                        $Goods_CommonDetailModel -> addCommonDetail($common_detail_data);
                    }
                }
                //库存配置
                //判断  修改的只修改
                //取出已有的所有goods_id
                $goods_base = $Goods_BaseModel -> getByWhere(array('common_id' => $common_id));
                if (!empty($goods_base)) {
                    $goods_base_ids = array_column($goods_base, 'goods_id');
                }
                $goods_data['cat_id'] = $common_data['cat_id'];                    //商品分类id
                $goods_data['common_id'] = $common_id;                                //商品公共表id
                $goods_data['shop_id'] = $common_data['shop_id'];                    //shop_id
                $goods_data['shop_name'] = $common_data['shop_name'];                //shop_name
                $goods_data['goods_name'] = $common_data['common_name'];                //商品名称
                $goods_data['goods_promotion_tips'] = $common_data['common_promotion_tips'];    //促销提示
                $goods_data['goods_is_recommend'] = $common_data['common_is_recommend'];        //商品推荐
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
                    $spec_value_color_ids = $goodsSpecValueModel -> getSpecValueByColor();
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
                        $Goods_BaseModel -> removeBase($remove_goods_ids);
                    }
                    foreach ($spec_data as $key => $val) {
                        $goods_data['goods_price'] = $val['price'];                            //商品价格
                        $goods_data['goods_market_price'] = $val['market_price'];                        //市场价
                        $goods_data['goods_stock'] = $val['stock'];                            //商品库存
                        $goods_data['goods_alarm'] = $val['alarm'];                            //库存预警值
                        $goods_data['goods_code'] = $val['sku'];                                //商家编号货号
                        $goods_data['goods_max_sale'] = $goods_limit;                   //单人最大购买数量
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
                            $old_base = $Goods_BaseModel -> getOne($goods_id);
                            if (($goods_data['goods_price'] != $old_base['goods_price']) || ($goods_data['goods_stock'] != $old_base['goods_stock']) || ($goods_data['goods_recommended_min_price'] != $old_base['goods_recommended_min_price']) || ($goods_data['goods_recommended_max_price'] != $old_base['goods_recommended_max_price'])) {
                                //产品价格、产品库存、最低零售价、最高零售价格 是否更改
                                $down_flag = true;
                            }
                            $Goods_BaseModel -> editBase($goods_id, $goods_data, false);
                            $edit_ids[] = $goods_id;
                        } else {
                            if ($retain_flag) {
                                //获取原有的base数据信息
                                $old_base = $Goods_BaseModel -> getOne($retain_f_goods_id);
                                if (($goods_data['goods_price'] != $old_base['goods_price']) || ($goods_data['goods_stock'] != $old_base['goods_stock']) || ($goods_data['goods_recommended_min_price'] != $old_base['goods_recommended_min_price']) || ($goods_data['goods_recommended_max_price'] != $old_base['goods_recommended_max_price'])) {
                                    //产品价格、产品库存、最低零售价、最高零售价格 是否更改
                                    $down_flag = true;
                                }
                                $goods_id = $Goods_BaseModel -> editBase($retain_f_goods_id, $goods_data, false);
                                $retain_flag = false;
                            } else {
                                $goods_id = $Goods_BaseModel -> addBase($goods_data, true);
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
                    $goods_data['goods_alarm'] = $common_data['common_alarm'];                //库存预警值
                    $goods_data['goods_code'] = $common_data['common_code'];                //商家编号货号
                    //供应商店铺
                    if ($shop_base['shop_type'] == 2) {
                        $goods_data['goods_recommended_min_price'] = $goods_recommended_min_price; //最低销售价格
                        $goods_data['goods_recommended_max_price'] = $goods_recommended_max_price;   //最高销售价格
                    }
                    if ($action == 'edit') {
                        $goods_id = pos($goods_base_ids);
                        //获取原有的base数据信息
                        $old_base = $Goods_BaseModel -> getOne($goods_id);
                        if (($goods_data['goods_price'] != $old_base['goods_price']) || ($goods_data['goods_stock'] != $old_base['goods_stock']) || ($goods_data['goods_recommended_min_price'] != $old_base['goods_recommended_min_price']) || ($goods_data['goods_recommended_max_price'] != $old_base['goods_recommended_max_price'])) {
                            //产品价格、产品库存、最低零售价、最高零售价格 是否更改
                            $down_flag = true;
                        }
                        $Goods_BaseModel-> editBase($goods_id, $goods_data, false);
                        $edit_ids[] = $goods_id;
                    } else {
                        $goods_id = $Goods_BaseModel -> addBase($goods_data, true);
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
                $test_id = $Goods_CommonModel -> editCommon($common_id, $edit_common_data);
                if ($common_base['common_parent_id']) {//如果是分销商更改数据，改变修改完的跳转链接
                    $data['dist_goods'] = 1;
                }
                //供货商规格商品列表
                $supplier_goods_list = $Goods_BaseModel -> getByWhere(array('common_id' => $common_id));
                if ($shop_base['shop_type'] == 2) {
                    $MessageModel = new MessageModel();
                    $all_common = $Goods_CommonModel -> getByWhere(array('common_parent_id' => $common_id, 'product_is_behalf_delivery' => 1));
                    foreach ($all_common as $key => $value) {
                        $dist_common_base = $Goods_CommonModel -> getOne($value['common_id']);
                        $dist_shop_base = $Shop_BaseModel -> getOne($dist_common_base['shop_id']);
                        //修改商品信息，并下架,重新加载商品规格
                        $dist_common_row = $Goods_CommonModel -> SynchronousCommon($common_id, $dist_shop_base, 'edit');
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
                            $Goods_CommonModel -> removeCommon($value['common_id']);
                            //发送消息
                            $des = '该商品不支持代发货';
                            $MessageModel -> sendMessage('del goods', $dist_shop_base['user_id'], $dist_shop_base['user_name'], $order_id = null, $shop_name = null, 1, 1, $end_time = null, $value['common_id'], $goods_id = null, $des);
                        } else {
                            $old_goods_base = $Goods_BaseModel -> getByWhere(array('common_id' => $value['common_id']));
                            $base_row = array();
                            foreach ($old_goods_base as $k => $val) {
                                if (in_array($val['goods_parent_id'], $remove_goods_ids)) {
                                    $Goods_BaseModel -> removeBase($val['goods_id']);
                                } elseif (in_array($val['goods_parent_id'], $edit_ids)) {
                                    $parent_goods = $Goods_BaseModel -> getOne($val['goods_parent_id']);
                                    $edit_rows = array();
                                    $edit_rows['goods_spec'] = $parent_goods['goods_spec'];
                                    $edit_rows['goods_price'] = $parent_goods['goods_recommended_min_price'];
                                    $edit_rows['goods_market_price'] = $parent_goods['goods_recommended_max_price'];
                                    $edit_rows['goods_stock'] = $parent_goods['goods_stock'];
                                    $edit_rows['goods_recommended_min_price'] = $parent_goods['goods_recommended_min_price'];
                                    $edit_rows['goods_recommended_max_price'] = $parent_goods['goods_recommended_max_price'];
                                    $Goods_BaseModel -> editBase($val['goods_id'], $edit_rows, false);
                                    $dist_common_row['goods_id'][] = array(
                                        'goods_id' => $val['goods_id'],
                                        'color' => $val['color_id']
                                    );
                                }
                                if ($add_ids) {
                                    foreach ($add_ids as $addk => $addv) {
                                        $parent_goods = $Goods_BaseModel -> getOne($addv);
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
                                        $add_goods_id = $Goods_BaseModel -> addBase($add_rows, true);
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
                                $MessageModel -> sendMessage('Commodity violation is under the shelf', $dist_shop_base['user_id'], $dist_shop_base['user_name'], $order_id = null, $shop_name = null, 1, 1, $end_time = null, $value['common_id'], $goods_id = null, $common_state_remark);
                            }
                            $Goods_CommonModel -> editCommon($value['common_id'], $dist_common_row);
                        }
                    }
                }
                //商品添加或者修改成功向统计中心发送数据
                if ($action == 'edit') {
                    if ($edit_status) {
                        $analytics_data = array(
                            'common_id' => $common_id,
                        );
                        Yf_Plugin_Manager::getInstance() -> trigger('analyticsGoodsEdit', $analytics_data);
                        /******************************************************/
                    }
                } else {
                    $analytics_data = array(
                        'common_id' => $common_id,
                    );
                    Yf_Plugin_Manager::getInstance() -> trigger('analyticsGoodsAdd', $analytics_data);
                    /******************************************************/
                }

                $data['common_id'] = $common_id;
                $this -> data -> addBody(-140, $data, __('success'), 200);
            } else {
                $Goods_CommonModel -> sql -> rollBackDb();
                $this -> data -> addBody(-140, array(), !empty($msg)? __($msg): __('failure'), 250);
            }
        }

        public function saveGoodsImage()
        {
            $image_list = request_row('image');
            $image_list = is_array($image_list) ? $image_list : json_decode($image_list, true);


            $common_id = request_int('common_id');
            $is_color = request_int('is_color');

            if(!$common_id) {
                return $this -> data -> addBody(-140, array(), __('缺少common_id'), 250);
            }

            if(!$image_list) {
                return $this -> data -> addBody(-140, array(), __('缺少图片信息'), 250);
            }

            if (!empty($image_list)) {
                $Goods_CommonModel = new Goods_CommonModel();
                $goodsImagesModel = new Goods_ImagesModel();
                $images = $goodsImagesModel -> getByWhere(array('common_id' => $common_id));
                $images_ids = array_column($images, 'id');
                $goodsImagesModel -> removeImages($images_ids);
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
                                $Goods_CommonModel -> editCommon($common_id, array('common_image' => $v['name']));
                            }
                            $flag = $goodsImagesModel -> addImages($image_data, true);
                            $flags[] = $flag;
                        } else {
                            $common_image = $Goods_CommonModel -> getOne($common_id);
                            if ($num == 1 && $k == 0) {
                                $image_data['images_is_default'] = Goods_ImagesModel::IMAGE_DEFAULT;
                                $image_data['images_image'] = $common_image['common_image'];
                            } elseif ($num > 1 && $k == 0) {
                                $image_data['images_image'] = $common_image['common_image'];
                            }
                            $flag = $goodsImagesModel -> addImages($image_data, true);
                            $flags[] = $flag;
                        }
                    }
                    unset($new0);
                    unset($new1);
                    unset($new_val);
                }

                $this -> data -> addBody(-140, $image_list, __('success'), 200);

            }
        }
         /*
	      *分销商同步供货商数据
	      * */
        public function addto_distributor()
        {
            $Goods_CommonModel = new Goods_CommonModel();
            $Shop_BaseModel = new Shop_BaseModel();
            $user_id = request_int('user_id');
            $shop_info = $Shop_BaseModel -> getOneByWhere(['user_id'=>$user_id]);
            $old_common_id = request_int('common_id');
            $old_common   = $Goods_CommonModel->getOne($old_common_id);
            $shop_id = $shop_info['id'];
            //查看是否已经有同步数据
            $check_common = $Goods_CommonModel->getOneByWhere(array('shop_id'=>$shop_id,'common_parent_id'=>$old_common_id,'product_is_behalf_delivery' => 1));

            //分销商申请是否通过
            $shopDistributorModel = new Distribution_ShopDistributorModel();
            /**
             * 这有一个潜在bug
             * 场景：
             * 分销商申请两次，第一次失败，第二次成功
             * getOneByWhere只会获取失败的记录
             */
            $shopDistributorBase = $shopDistributorModel -> getOneByWhere([
                'distributor_id' => $shop_id,
                'shop_id' => $old_common['shop_id'],
                'distributor_enable'=> 1 //是否审核通过: 0-待审核;  1-通过;-1未通过
            ]);

            $allow_shop_cat = explode(',',$shopDistributorBase['distributor_cat_ids']);//分销商申请的店铺分类

            $data = array();
            $flag = false;

            $old_shop_cat_id = trim($old_common['shop_cat_id'],',');
            $old_shop_cat_id = $old_shop_cat_id?explode(',',$old_shop_cat_id):'';

            if(empty($check_common) && $shopDistributorBase['distributor_enable'] == 1 && (array_intersect($old_shop_cat_id, $allow_shop_cat) || empty($old_shop_cat_id)))
            {
                $shop_info  = $Shop_BaseModel ->getOne($shop_id); //店铺信息

                //同步商品common,获取common_id
                $new_common_id = $Goods_CommonModel->SynchronousCommon($old_common_id,$shop_info);

                //同步商品goods_base，获取规格id
                $new_goods_ids = $Goods_CommonModel->SynchronousGoods($old_common_id,$new_common_id,$shop_info);

                //同步商品的图片
                $Goods_CommonModel->SynchronousGoodsImage($old_common_id,$new_common_id,$shop_info);

                $edit_common_data['goods_id'] = $new_goods_ids;
                $data['goods_id'] = $edit_common_data;
                $flag=$Goods_CommonModel->editCommon($new_common_id, $edit_common_data);
            }

            if($flag){
                $data['common_id'] = $new_common_id;
                $msg = __('success');
                $status = 200;
            }elseif($shopDistributorBase['distributor_enable'] == '0'){
                $msg = __('分销商申请未通过！');
                $status = 250;
            }elseif(!empty($check_common)){
                $msg = __('该商品您已经分销！');
                $status = 250;
            }else{
                $msg = __('你未申请该分类！');
                $status = 250;
            }

            $this->data->addBody(-140, $data,$msg,$status);
        }
    }

?>