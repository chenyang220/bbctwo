<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}
class Api_Goods_CartCtl extends Api_Controller
{
    public $cartModel = '';
    public $appCartModel = '';
    public $shopDistributorModel = null;
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->cartModel = new CartModel();
        $this->appCartModel = new AppCartModel();
        $this->shopDistributorModel = new Distribution_ShopDistributorModel();
    }


    /**
     *添加购物车
     * @return string
     * @throws Exception
     */
    public function addCart()
    {
        $user_id = request_int('user_id');
        $goods_id = request_int('goods_id');
        $goods_num = request_int('goods_num');
        $buy_now  = request_int('buy_now');

        if ($goods_id < 1 || $goods_num < 1) {
            return $this->data->setError("数据有误");
        }

        /********************************************************************/
        //判断商品是否满足限购条件，如果限时折扣设置最低购买数量大于商品本身限购数，按照限时折扣最低数量计算
        $cartModel = new CartModel;
        $Promotion = new Promotion();
        $goods_xianshi = $Promotion->getXianShiGoodsInfoByGoodsID($goods_id);

        if ($goods_xianshi && $goods_num < $goods_xianshi['goods_lower_limit'] && strtotime($goods_xianshi['goods_start_time']) <= time() && strtotime($goods_xianshi['goods_end_time']) >= time()) {
            return $this->data->setError("添加失败，低于最低购买数量");
        } else if (!$goods_xianshi) {
            $limit_flag = $cartModel->checkCartGoodsLimits($goods_id, $goods_num);

            if (!$limit_flag) {
                return $this->data->setError("添加失败，超出限购数量");
            } else {
                //如果该商品限购，查出用户之前购买过的商品数量，当前添加购物车数量与之前购买数量相加不能超过限购数
                $Goods_BaseModel = new Goods_BaseModel();
                $Goods_CommonModel = new Goods_CommonModel();

                $goods_base = $Goods_BaseModel->getOne($goods_id);
                $common_base = $Goods_CommonModel->getOne($goods_base['common_id']);


                if ($common_base['common_limit'] > 0) {
                    $Order_GoodsModel = new Order_GoodsModel();
                    $cond_row['buyer_user_id'] = $user_id;
                    $cond_row['common_id'] = $goods_base['common_id'];
                    $cond_row['order_goods_status:IN'] = [1, 2, 3, 4, 5, 6, 8];
                    $cond_row['order_goods_time:>='] = $common_base['common_edit_time'];
                    $order_goods_data = $Order_GoodsModel->getByWhere($cond_row);
                    $goods_num_sum = array_sum(array_column($order_goods_data, 'order_goods_num'));

                    //如果是立即购买的商品则不计算原来购物车中的商品数量
                    if($buy_now)
                    {
                        $goods_num_sum = 0;
                    }
                    if ($goods_num_sum + $goods_num > $common_base['common_limit']) {
                        return $this->data->setError("添加失败，超出限购数量");
                    }
                }

            }
        }

        /********************************************************************/
        $Goods_BaseModel = new Goods_BaseModel();
        $Goods_CommonModel = new Goods_CommonModel();

        $goods_base = $Goods_BaseModel->getOne($goods_id);
        $common_base = $Goods_CommonModel->getOne($goods_base['common_id']);

        //如果是供货商的商品
        $ShopBaseModel = new Shop_BaseModel();
        $goods_shop_base = $ShopBaseModel->getOne($common_base['shop_id']);
        $shopBaseModel = new Shop_BaseModel();
        $shop_info = $shopBaseModel -> getOneByWhere(['user_id'=>$user_id]);
        if ($shop_info['id'] && $goods_shop_base['shop_type'] == 2) {
            //分销商申请是否通过
            $shopDistributorModel = new Distribution_ShopDistributorModel();
            $shopDistributorBase = $shopDistributorModel->getOneByWhere(array('distributor_id' => $shop_info['id'], 'shop_id' => $common_base['shop_id']),['shop_distributor_time'=>'desc']);
            $allow_shop_cat = explode(',', $shopDistributorBase['distributor_cat_ids']);//分销商申请的店铺分类

            $common_shopcat_id = trim($common_base['shop_cat_id'], ',');
            $common_shopcat_id = explode(',', $common_shopcat_id);

            if ($shopDistributorBase['distributor_enable'] == 1 && (array_intersect($common_shopcat_id, $allow_shop_cat) || empty($common_base['shop_cat_id']))) {

            } else {

                if (!$shopDistributorBase['distributor_enable']) {
                    return $this->data->setError("分销申请未通过！");
                }

                if (!array_intersect($common_shopcat_id, $allow_shop_cat)) {
                    return $this->data->setError("该分类未授权");
                }
            }
        }

        //判断购物车中是否存在该商品
        $cart_cond = array();
        $cart_cond['user_id'] = $user_id;
        $cart_cond['shop_id'] = $goods_base['shop_id'];
        $cart_cond['goods_id'] = $goods_id;
        $cart_row = $this->cartModel->getByWhere($cart_cond);

        /*******************加入购物车优化（xue）*****************/
        $goods_stock = $goods_base['goods_stock'];    //商品的库存
        $goods_cart  = current($cart_row);            //商品购车数量
        if (is_array($goods_cart)){
            $cart_num = $goods_cart['goods_num'];
        } else{
            $cart_num = 0;
        }
        //如果购物车数量超过库存修改购物车数量为库存数
        if ($cart_num > $goods_stock){
            $flag = $this->cartModel->editCartNum($goods_cart['cart_id'], array("goods_num"=>$goods_stock));
        }
        //提交的商品数加购物车数超过库存则返回
        if ($cart_num+$goods_num>$goods_stock){
            return $this->data->addBody(-140, array(), '购买数超出库存，或查看购物车是否已有该商品', 250);
        }
        /***********************end***********************/



        if (is_array($cart_row) && $cart_row) {
            $cart_row = array_shift($cart_row);
            //需求现改为购物车内的商品与立即购买的商品数不累加，所以如果购物车存在此商品就将购物车商品数量修改为现在购买的数量
            if($buy_now)
            {
                $cart_row['goods_num'] = 0;
            }
            $edit_cond_rows['goods_num'] = $cart_row['goods_num'] + $goods_num;
            $flag = $this->cartModel->editCart($cart_row['cart_id'], $edit_cond_rows, false);
            if ($flag !== false) {
                $flag = $cart_row['cart_id'];
            }
        } else {
            $add_row = array();
            $add_row['user_id'] = $user_id;
            $add_row['shop_id'] = $goods_base['shop_id'];
            $add_row['goods_id'] = $goods_id;
            $add_row['goods_num'] = $goods_num;
            $flag = $this->cartModel->addCart($add_row, true);
        }
        if ($flag) {
            $status = 200;
            $msg    = __('success');
        } else {
            $status = 250;
            $msg    = __('failure');
        }

        $data = array(
            'flag' => $flag,
            'msg' => $msg,
            'cart_id' => $flag
        );
        return $this->data->addBody(-140, $data, $msg, $status);

    }
    /**
     * 获取购物车列表
     *
     * @author Zhuyt
     */
    public function getCart()
    {
        $user_id = request_int('user_id');
        $Goods_BaseModel = new Goods_BaseModel();
        $cord_row = array();
        $order_row = array();

        $cond_row = array('user_id' => $user_id);
        if(request_int('goods_id')){
            $cond_row['goods_id'] =  request_int('goods_id');
        }
        $order_row['cart_id'] = 'DESC';
        $shopBaseModel = new Shop_BaseModel();
        $shop_info = $shopBaseModel -> getOneByWhere(['user_id'=>$user_id]);
        $supplier_data = $this->supplier($shop_info['id']);
        $cond_row['shop_id:IN'] =$supplier_data;
        $data = $this->appCartModel->getCardList($cond_row, $order_row,$user_id,$shop_info['id']);
        foreach ($data as $key => $value) {
            $goods_detail = $Goods_BaseModel->getGoodsDetailInfoByGoodId($value['goods'][0]['goods_id']);
            if (!empty($goods_detail['common_base']['common_spec_name'])) {
                //商品规格颜色图
                if (!empty($goods_detail['common_base']['common_spec_value_color'])) {
                    $data[$key]['goods'][0]['goods_base']['goods_image'] = $goods_detail['common_base']['common_spec_value_color'][$value['goods'][0]['goods_base']['color_id']];
                }
            }
        }
        $new_data = array();
        $sum = 0;
        $number = 0;
        $count = $data['count'];
        $cart_count = $data['cart_count'];
        $invalid_goods = $data['invalid_goods'];
        unset($data['count']);
        unset($data['cart_count']);
        unset($data['invalid_goods']);
        $new_data['count'] = $count;
        $new_data['cart_count'] = $cart_count;
        $new_data['invalid_goods'] = $invalid_goods;
        $cart_list = array_values($data);
        $new_data['cart_list'] = $cart_list;
        if (!empty($cart_list)) {
            foreach ($cart_list as $key => $val) {
                foreach ($val['goods'] as $k => $v) {
                    $sum += $v['goods_num'] * $v['now_price'];
                    $number += $v['goods_num'];
                    $count_1[] = count($v['common_base']['goods_id']);
                    if (2 === count($v['common_base']['goods_id'])) {
                        $new_data['cart_list'][$key]['goods'][$k]['common_base']['goods_id'] = array($new_data['cart_list'][$key]['goods'][$k]['common_base']['goods_id']);
                    }
                }
            }
        }
        $new_data['sum'] = $sum;
        $new_data['number'] = $number;
        $this->data->addBody(-140, $new_data);
    }
    //修改购物车的数量
    public function editCartNum()
    {
        $cart_id = request_int('cart_id');
        $num = (int)request_int('num');
        $user_id = request_int('user_id');
        if ($num < 1) {
            exit('not human!!!');
        }
        $edit_row = array('goods_num' => $num);
        if(!$cart_id){
            $data = array();
            $msg = __('购物车中已无此商品');
            $status = 250;
            $this->data->addBody(-140, $data, $msg, $status);
            return;
        }

        //获取商品信息
        $cart_base = $this->cartModel->getOne($cart_id);

        $goods_id = $cart_base['goods_id'];

//        $Goods_BaseModel = new Goods_BaseModel();
//        $goods_base = $Goods_BaseModel->getOne($goods_id);


        //如果该商品限购，查出用户之前购买过的商品数量，当前添加购物车数量与之前购买数量相加不能超过限购数
        $Goods_BaseModel = new Goods_BaseModel();
        $Goods_CommonModel = new Goods_CommonModel();

        $goods_base = $Goods_BaseModel->getOne($goods_id);
        $common_base = $Goods_CommonModel->getOne($goods_base['common_id']);

        if ($common_base['common_limit'] > 0) {
            $Order_GoodsModel = new Order_GoodsModel();
            $cond_row['buyer_user_id'] = $user_id;
            $cond_row['common_id'] = $goods_base['common_id'];
            $cond_row['order_goods_status:IN'] = [1, 2, 3, 4, 5, 6, 8];
            $order_goods_data = $Order_GoodsModel->getByWhere($cond_row);
            //已购买数量
            $goods_num_sum = array_sum(array_column($order_goods_data, 'order_goods_num'));

            if ($goods_num_sum + $num > $common_base['common_limit']) {
                $data = array();
                $msg = __('超出商品限购');
                $status = 250;
                return $this->data->addBody(-140, $data,$msg,$status);
            }
        }
        //查询该用户是否已购买过该商品
        $Order_GoodsModel = new Order_GoodsModel();
        $order_goods_cond['common_id'] = $goods_base['common_id'];
        $order_goods_cond['buyer_user_id'] = $user_id;
        $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_REFUND_FINISH;
        $order_list = $Order_GoodsModel->getByWhere($order_goods_cond);

        $order_goods_count = count($order_list);

        //如果有限购数量就计算还剩多少可购买的商品数量
        if ($goods_base['goods_max_sale']) {
            $limit_num = $goods_base['goods_max_sale'] - $order_goods_count;

            $limit_num = $limit_num < 0 ? 0 : $limit_num;

            if ($limit_num < $num) {
                $num = $limit_num;
            }
        }


        $chainId = request_string('chainId'); //代表这是门店自提

        if (!empty($chainId)) {
            $chainGoodsModel = new Chain_GoodsModel;
            $goods_stock = $chainGoodsModel->getGoodsStock($chainId, $goods_id);
        } else {
            $goods_stock = $goods_base['goods_stock'];
        }

        //判断加入购物车的数量和库存
        if ($num*1 <= $goods_stock*1) {
            $flag = $this->cartModel->editCartNum($cart_id, $edit_row);
        } else {
            $flag = false;
        }

        $data = array();
        if ($flag === false) {
            $status = 250;
            $msg = __('库存不足');
        } else {
            //获取此商品的总价
            $data['price'] = $this->cartModel->getCartGoodPrice($cart_id);

            $status = 200;
            $msg = __('success');
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }
    /*
    * 最新确认订单信息后生成订单
    *
    * @author tyf
    */
    public function newConfirm()
    {
        $user_id = request_int('user_id');
        $cart_id = request_row('product_id');
        $nums = request_row('nums');

        if (!is_array($cart_id)) {
            $product_id = request_string('product_id');
            $cart_id = explode(',', $product_id);
        }

        if (!is_array($nums)) {
            $num = request_string('nums');
            $nums = explode(',', $num);
        }
        $new_list = array();//以购物车cat_id为键，提交的购买数量为值
        $goods_row = array();//获取选中商品的详细信息列表
        $list = array();//以商品id为键值，购物车数量为值
        $cond_row['cart_id:IN'] = $cart_id;
        $cond_row['user_id'] = $user_id;

        //第一步判断提交商品数量是否与购物车相同，不相同返回false
        if ($nums && $cart_id) {
            foreach ($cart_id as $k => $v) {
                $new_list[$v] = $nums[$k];
            }
        } else {
            return $this->data->addBody(-140, array(), __('购物车不能为空'), 250);
        }

        $cart_list = $this->cartModel->getByWhere($cond_row);
        if ($cart_list) {
            foreach ($cart_list as $k1 => $v1) {
                $goods_row[] = $v1['goods_id'];
                //判断购物车数量是否与提交数量一致
                if ($v1['goods_num'] != $new_list[$v1['cart_id']]) {
                    return $this->data->addBody(-140, array(), __('数据有误，请刷新当前页面'), 250);
                }
                $list[$v1['goods_id']] = $v1['goods_num'];
            }
        }

        //第二步查goods_base表判断库存,条件不符合返回false
        $reslut = $this->cartModel->getCatGoods_stock($goods_row, $list);
        if ($reslut['state'] == false) {

            return $this->data->addBody(-140, array(), $reslut['msg'], 250);
        }
        //第三步查促销表,条件不符合返回false
        $reslut1 = $this->cartModel->getDiscount_Goods($goods_row, $list);

        if ($reslut1['state'] == false) {
            return $this->data->addBody(-140, array(), $reslut1['msg'], 250);
        }
        //第四步查团购表,条件不符合返回false,排除团购商品查看是否符合单个商品单人最高购买值
        $result2 = $this->cartModel->getGroupBuylist($goods_row, $list);

        if ($result2['state'] == false) {
            return $this->data->addBody(-140, array(), $result2['msg'], 250);
        }


        return $this->data->addBody(-140, array(), 'success', 200);
    }
    /**
     * 确认订单信息后生成订单
     *
     * @author Zhuyt
     */
    public function confirm()
    {
        $user_id = request_int('user_id');
        $address_id = request_int('address_id');
        $cart_type = request_string('cart_type');//积分商品购物车
        $cart_id = request_row('product_id');
        $is_discount = request_int('is_discount');  //判断是否计算会员折扣
        //会员折扣规则：0-所有店铺都享有会员折扣，1-仅自营店铺享有会员折扣
        $data['rate_service_status'] = Web_ConfigModel::value('rate_service_status');
        //获取用户的折扣信息
        $User_InfoMode = new User_InfoModel();
        $user_rate = $User_InfoMode->getUserGrade($user_id);

        //获取购物车id数组
        if (!is_array($cart_id)) {
            $product_id = request_string('product_id');
            $cart_id = explode(',', $product_id);
        }

        $cond_row['cart_id:IN'] = $cart_id;
        $cond_row['user_id'] = $user_id;
        //购物车中的商品信息
        if ($cart_type) {
            $Points_CartModel = new Points_CartModel();
            $cond_points_row['points_cart_id:IN'] = $cart_id;
            $cond_points_row['points_user_id'] = $user_id;
            $data['glist'] = $Points_CartModel->getOnePointsCartByWhere($cond_points_row, array());
        } else {
            $shopBaseModel = new Shop_BaseModel();
            $shop_info = $shopBaseModel -> getOneByWhere(['user_id'=>$user_id]);
            $data['glist'] = $this->appCartModel->getCardList($cond_row, array(),$user_id,$shop_info['id']);
        }
        if (!$cart_type) {
            $data['user_rate'] = $user_rate;
        }

        //plus 开关
        $openFlag = Web_ConfigModel::value('plus_switch');
        $plus =[];
        $userPlusFlag = 0;//默认非plus会员
        if($plus && $plus['user_status'] !=3 && $plus['end_date']>time() && $openFlag){
            $userPlusFlag = 1;//plus会员(包括试用和正式)
        }
        //店铺是否有plus商品标识
        $sumPlusGoods = 0;
        foreach ($data['glist'] as $k=>$item) {
            unset($data['glist']['invalid_goods']);
            $data['glist'][$k]['shopPlusGoods']= 0;
            if($item['goods']){
                $flag =  0;
                foreach ( $item['goods'] as $gitem) {
                    $gitem['isPlus'] && $flag+=1;
                }
                $flag && $data['glist'][$k]['shopPlusGoods']= 1;
            }
            $data['glist'][$k]['shopPlusGoods'] && $sumPlusGoods+=1;
            $data['glist'][$k]['shopPlusGoods']  = 1;
        }
        //获取收货地址
        $User_AddressModel = new User_AddressModel();
        $cond_address = array('user_id' => $user_id);
        $address = array_values($User_AddressModel->getAddressList($cond_address, array('user_address_default' => 'DESC', 'user_address_id' => 'DESC')));
        $data['address'] = current($address);

        //判断收货地址是否在收货区域
        $city_id = 0;
        if ($address_id) {
            //如果传递了address_id,根据address_id获取信息
            $address_row = $User_AddressModel->getOne($address_id);
            $city_id = $address_row['user_id'] != $user_id ? 0 : $address_row['user_address_city_id'];
        } else {
            //获取默认地址
            $address_row = $User_AddressModel->getDefaultAddress($user_id);
            $city_id = $address_row['user_address_city_id'] ? $address_row['user_address_city_id'] : 0;
        }
        if($address_row){
            $data['address'] = $address_row;
        }
        $area_model = new Transport_AreaModel();
        //是否开启团购
        $is_group = false;
        $count_num = 0;

        foreach($data['glist'] as $key=>$value)
        {
            if($value['goods'])
            {
                foreach($value['goods'] as $k=>$v)
                {
                    $checkArea = $area_model->isSale($v['transport_area_id'], $city_id);
                    $v['buy_able'] = !$checkArea ? 0 : 1;
                    $data['glist'][$key]['goods'][$k]['buy_able'] = !$checkArea ? 0 : 1;
                    if ($v['goods_base']['promotion_type'] == 'groupbuy' && $v['goods_base']['groupbuy_starttime'] < date('Y-m-d H:i:s') && $v['goods_base']['groupbuy_endtime'] > date('Y-m-d H:i:s')){
                        $is_group = true;
                    }
                    $count_num+=$v['goods_num'];
                }
            }
        }
        $data['count_num'] = $count_num;
        //计算运费
        if (!$cart_type) {
            $transport_cost = $this->calculateFreight($user_id,$address_id, $data, $cart_id);
        }

        $data['cost'] = !$transport_cost ? array() : $transport_cost;

        if (!$data['glist']['count']) {
            $this->view->setMet('error');
        }

        //用户是plus会员，购物车有plus会员商品则处理掉代金券
        foreach ($data['glist'] as $k=>$item) {
            $data['glist'][$k]['shopPlusGoods']= 0;
            if($item['goods']){
                $flag =  0;
                foreach ( $item['goods'] as $gitem) {
                    $gitem['isPlus'] && $flag+=1;
                }
                $flag && $data['glist'][$k]['shopPlusGoods']= 1;
            }
        }
        if($userPlusFlag && $sumPlusGoods){
            $data['user_rate'] = 0;
            foreach ($data['glist'] as $k => &$shop_data) {
                if ($shop_data['best_voucher']) {
                    $shop_data['best_voucher'] = array();
                }
            }
        }

        //计算使用红包之前的订单金额
        $this->computeOrderPriceBeforeRed($data);

        //平台优惠券（优惠券）,与各个商家无关
        $data['rpt_list'] = array();
        $data['rpt_info'] = array();
        //如果开启了会员折扣就不在计算红包
        if (!$is_discount) {
            if (!$cart_type) {
                $rpt_list = RedPacket::lists(['user_id' =>$user_id, 'order_by' => 'redpacket_price ASC,redpacket_end_date ASC', 'redpacket_state' => RedPacket_BaseModel::UNUSED . ',' . RedPacket_BaseModel::EXPIRED]);
                $rpt_info = RedPacket::lists(['user_id' =>$user_id, 'redpacket_state' => RedPacket_BaseModel::UNUSED, 'order_by' => 'redpacket_price desc', 'start_date' => get_date_time(), 'end_time' => get_date_time(), 'limit' => 1, 'pay_amount' => $data['order_price']]);
                $rpt_list = RedPacket::isable(['data' => $rpt_list, 'pay_amount' => $data['order_price']]);
                $data['rpt_list'] = $rpt_list;
                $data['rpt_info'] = $rpt_info;
            }
        }

        if($userPlusFlag && $sumPlusGoods){
            $data['rpt_list'] = array();
            $data['rpt_info'] = array();
        }

        unset($data['glist']['count']);
        unset($data['glist']['cart_count']);
        $data['glist'] = array_values($data['glist']);
        $data['cost'] = array_values($data['cost']);

        //在不是积分兑换的情况下获取加价购促销信息
        if (!$cart_type) {
            $pomption = 0;
            if ($data['glist']) {
                foreach ($data['glist'] as $k => $shop_goods) {
                    $shop_id = $shop_goods['shop_id'];
                    $goods_list = $shop_goods['goods'];
                    $shop_goods_rows = [];
                    if ($goods_list) {
                        foreach ($goods_list as $goods_data) {
                            $shop_goods_rows[$goods_data['goods_id']] = [
                                'now_price' => $goods_data['now_price'],
                                'goods_num' => $goods_data['goods_num'],
                            ];
                        }
                        array_column($goods_list, 'now_price', 'goods_id');
                    }

                    $data['glist'][$k]['jia_jia_gou'] = $this->getPromotionInfoByJiaJia($shop_id, $shop_goods_rows);

                    $data['glist'][$k]['man_song'] = $this->getPromotionInfoByManSong($shop_id, $shop_goods_rows);

                    if ($shop_goods['promotion']) {
                        $pomption = 1;
                    }
                }
            }
            $data['pomotion'] = $pomption;

            //会员折扣关闭下如果订单中不存在活动商品，并且有最优红包，需要将使用红包之后的金额计算出来
            if (!empty($data['rpt_info'][0]) && !$data['pomotion'] && !$is_discount) {
                //计算使用红包之后的订单金额
                $this->computeOrderPriceAfterRed($data, $data['rpt_info'][0]['price']);
            }

            //开启会员折扣，计算订单价格。不存在活动商品
            if ($is_discount && !$data['pomotion']) {
                //计算会员折扣(默认会员折扣关闭，故不计算会员折扣)
                $this->computeMemberRebate($data, $user_rate);
            }
        }
        if ($cart_type) {
            $data['glist'] = [];
        }

        //计算物流费用
        $this->computeFreight($data);
        $data['is_discount'] = $is_discount;

        //pc分站

            $sub_site_id = [];

        //用于判断是否自营和后台是否开启自营店铺会员折扣
        $self_shop_show_key = !$sub_site_id ? 'self_shop_show' : 'self_shop_show_'.$sub_site_id;
        if(Web_ConfigModel::value($self_shop_show_key) == 1 && Web_ConfigModel::value("rate_service_status") == 1){
            $data['shop_self_support'] ='true';
        }else{
            $data['shop_self_support'] ='false';
        }

        //页面是否根据plus标识展示
        $isPlus = $userPlusFlag;
        //判断是否为自营店铺
        $data['is_shop_self_support'] = 0;
        foreach($data['glist'] as $k=>$v)
        {
            if($v['shop_self_support'] == 'false')
            {
                $data['is_shop_self_support'] += 1;
            }
        }
            //wap货到付款
        $data['cash_on_delivery_status'] = Web_ConfigModel::value('cash_on_delivery_status');
        $data['isPlus'] = $isPlus;
        $data['sumPlusGoods'] = $sumPlusGoods;
        $this->data->addBody(-140, $data);
    }

    /**
     *计算订单运费
     * @param $address_id
     * @param $data
     * @param $cart_id
     * @return array|string
     */
    public function calculateFreight($user_id,$address_id, $data, $cart_id)
    {
        $User_AddressModel = new User_AddressModel();
        $city_id = 0;
        if ($address_id) {
            //如果传递了address_id,根据address_id获取运费信息
            $address_row = $User_AddressModel->getOne($address_id);
            $city_id = $address_row['user_id'] != $user_id ? 0 : $address_row['user_address_city_id'];
        } else {
            //获取默认地址
            $address_row = $User_AddressModel->getDefaultAddress($user_id);
            $city_id = $address_row['user_address_city_id'] ? $address_row['user_address_city_id'] : 0;

        }
        $buy_able = 1;
        $checkArea = true;
        $transport_cost = array();
        if ($city_id) {
            //判断商品的售卖区域
            $area_model = new Transport_AreaModel();
            foreach ($data['glist'] as $key => $val) {
                if (!is_array($val['goods'])) {
                    continue;
                }
                foreach ($val['goods'] as $gkey => $gval) {
                    $checkArea = $area_model->isSale($gval['transport_area_id'], $city_id);
                    $data['glist'][$key]['goods'][$gkey]['buy_able'] = !$checkArea ? 0 : $buy_able;
                }
            }
            $city_id = is_array($city_id)?$city_id:explode(',',$city_id);
            //获取商品运费
            $Transport_TemplateModel = new Transport_TemplateModel();
            $transport_cost = $Transport_TemplateModel->cartTransportCost($city_id, $cart_id);
        }

        return $transport_cost;
    }

    /**
     * 计算订单使用红包之前的总金额
     * @param $data
     */
    private function computeOrderPriceBeforeRed(&$data)
    {
        $order_price = 0; //订单折扣总额
        $ini_order_price = 0; //订单折扣总额

        foreach ($data['glist'] as $k => $shop_data) {
            //判断店铺是否有默认代金券和活动商品，如果有活动商品不计算代金券，没有活动商品则需要减去代金券价格
            if (!empty($shop_data['best_voucher'][0]) && !$shop_data['promotion']) {
                $shop_data['sprice'] = number_format(($shop_data['sprice'] - $shop_data['best_voucher'][0]['price']), 2, ".", "");
                $data['glist'][$k]['sprice'] = $shop_data['sprice'];
            }
            $order_price += $shop_data['sprice'];
            $ini_order_price += $shop_data['ini_sprice'];
        }
        $data['order_price'] = number_format($order_price, 2, ".", "");
        $data['ini_order_price'] = number_format($ini_order_price, 2, ".", "");
    }
    /**
     * @author yuli
     * @param $shop_id int
     * @param $shop_goods_rows array ['goods_id'=> 'now_price']
     *
     * @return boolean or array
     *
     * 确认订单页面判断商品是否满足加价购
     *
     * 首先判断该店铺是否含有加价购活动
     * 其次判断该商品是否参加加价购活动
     * 最后判断参加活动商品价格累加是否符合加价购条件
     *
     * 注意：有可能出现多条加价购情况，当同一时间、同一商品只能参加一条活动
     */
    public function getPromotionInfoByJiaJia($shop_id, $shop_goods_rows)
    {
        $increaseBaseModel = new Increase_BaseModel;

        $shop_goods_ids = array_keys($shop_goods_rows);

        //获取正常的加价购列表
        $increase_rows = $increaseBaseModel->getByWhere([
            'shop_id' => $shop_id, //对应店铺
            'increase_state' => Increase_BaseModel::NORMAL //活动状态正常
        ]);

        if (empty($increase_rows)) {
            return []; //没有该促销信息
        }

        //筛选出加价购促销是否含有所需要的商品
        $increase_ids = array_keys($increase_rows);
        $increaseGoodsModel = new Increase_GoodsModel;

        $increase_goods_rows = $increaseGoodsModel->getByWhere([
            'increase_id:IN' => $increase_ids,
            'goods_id:IN' => $shop_goods_ids
        ]);

        if (empty($increase_goods_rows)) {
            return []; //没有该商品促销信息
        }

        //筛选出符合条件的加价购
        $answer_increase_ids = array_column($increase_goods_rows, 'increase_id');

        //需要返回的数据
        //返回符合条件加价购数组，把其中不满足条件rule删除
        $jia_jia_rows = [];

        foreach ($answer_increase_ids as $answer_increase_id) {
            $jia_jia_data = $increaseBaseModel->getIncreaseActDetail($answer_increase_id);

            $jia_jia_goods_list = $jia_jia_data['goods'];


            $now_price_sum = 0; //当前活动商品累计价格
            foreach ($jia_jia_goods_list as $jia_jia_goods_data) {
                $goods_id = $jia_jia_goods_data['goods_id'];

                $now_price_sum += in_array($goods_id, $shop_goods_ids)
                    ? $shop_goods_rows[$goods_id]['now_price'] * $shop_goods_rows[$goods_id]['goods_num']
                    : 0;
            }

            //如果不为零，代表参加该加价购活动
            if ($now_price_sum != 0) {
                $rules = $jia_jia_data['rule']; //加价购规则数组

                //过滤符合条件的规则
                $answer_rules = array_filter($rules, function ($val) use ($now_price_sum) {
                    return $now_price_sum >= $val['rule_price']
                        ? true
                        : false;
                });

                //符合条件
                if ($answer_rules) {
                    //格式化redemption_goods
                    foreach ($answer_rules as $k => $rule) {
                        $answer_rules[$k]['redemption_goods'] = array_values($rule['redemption_goods']);
                    }
                    $jia_jia_data['rule'] = $answer_rules;
                    $jia_jia_data['goods'] = array_values($jia_jia_data['goods']);
                    $jia_jia_rows[] = $jia_jia_data;
                }
            }
        }

        return $jia_jia_rows;
    }
    /**
     * @param $shop_id
     * @param $shop_goods_rows
     * @return array
     * 获取满送信息
     * 逻辑：
     *     1.同一店铺同一时间只会对应一个满送活动
     *     2.一个满送活动对应的多条规则
     */
    public function getPromotionInfoByManSong($shop_id, $shop_goods_rows)
    {
        $manSongBaseModel = new ManSong_BaseModel;
        $man_song_data = $manSongBaseModel->getManSongActItem([
            'shop_id' => $shop_id,
            'mansong_state' => ManSong_BaseModel::NORMAL,
            'mansong_start_time:<=' => date('Y-m-d H:i:s')
        ]);

        if (!isset($man_song_data['rule'])) {
            return [];
        }

        $shop_order_amount = array_sum(array_map(function ($v) {
            return $v['now_price'] * $v['goods_num'];
        }, $shop_goods_rows));

        foreach ($man_song_data['rule'] as $k => $v) {
            $man_song_data['rule'][$k]['accord'] = $shop_order_amount >= $v['rule_price']
                ? 1
                : 0;
        }

        return $man_song_data;
    }

    /**
     * 计算订单使用红包之后的总金额
     * @param $data
     * @param $red_price
     */
    private function computeOrderPriceAfterRed(&$data, $red_price)
    {
        $order_price = $data['order_price'] - $red_price;
        $data['order_price'] = number_format($order_price, 2, ".", "");
    }

    /**
     * 计算会员折扣
     * @param $data
     * @param $user_rate
     * @return bool
     */
    private function computeMemberRebate(&$data, $user_rate)
    {
        $order_discount = 0; //订单折扣总额

        foreach ($data['glist'] as $k => $shop_data) {
            $dian_rate = 0;
            //判断店铺中是否存在活动商品。如果存在活动商品则不进行会员折扣计算
            if ($shop_data['promotion']) {
                return false;
            } else {
                //当平台设置所有店铺都享有会员折扣，或者仅自营店铺享有会员折扣并且该店铺是自营店铺。计算会员折扣优惠金额
                if (
                    Web_ConfigModel::value('rate_service_status') == 0
                    ||
                    (Web_ConfigModel::value('rate_service_status') == 1 && $shop_data['shop_self_support'] == 'true')
                ) {
                    //店铺会员折扣优惠的金额
                    $dian_rate = $shop_data['sprice'] * (100 - $user_rate) / 100;
                }
            }
            //店铺享有的会员折扣优惠价格
            $data['glist'][$k]['shop_discount'] = number_format($dian_rate, 2, ".", "");

            //订单折扣优金额
            $order_discount += $dian_rate;
        }

        $data['order_price'] = number_format(($data['order_price'] - $order_discount), 2, ".", "");
        $data['order_discount'] = number_format($order_discount, 2, ".", "");
    }
    /**
     * @param $data
     * 加入运费计算
     * 在computeMemberRebate之后调用此方法
     */
    private function computeFreight(&$data)
    {
        foreach ($data['glist'] as $k => $shop_data) {
            $freight = $data['cost'][$k]['cost'];
            $data['glist'][$k]['freight'] = $freight;
            $data['glist'][$k]['sgprice'] = $data['glist'][$k]['sprice'];
            $data['glist'][$k]['sprice'] = number_format(($data['glist'][$k]['sprice'] + $freight), 2, ".", "");

            $data['order_price'] += $freight;
        }
    }

    /**
     * 获取用户的发票信息
     *
     * @author Zhuyt
     */
    public function piao()
    {
        //获取一级地址
        $user_id = request_int('user_id');
        //获取用户的发票信息
        $InvoiceModel = new InvoiceModel();
        $data = $InvoiceModel->getInvoiceByUser($user_id);
        if ($data['normal']) {
            $da['normal'] = $data['normal'][0];
        } else {
            $da['normal'] = array();
        }

        if ($data['electron']) {
            $da['electron'] = $data['electron'][0];
        } else {
            $da['electron'] = array();
        }

        if ($data['addtax']) {
            $da['addtax'] = $data['addtax'][0];
        } else {
            $da['addtax'] = array();
        }
        $this->data->addBody(-140, $da);
    }

    //供应商
    private function supplier($shop_id)
    {
        $cond_row  = array();
        $order_row = array();

        //审核通过
         $cond_row['distributor_enable'] =1;
        $cond_row['distributor_id'] = $shop_id;
        $data = $this->shopDistributorModel->listByWhere($cond_row, $order_row);
        $re_data=[];
        if(isset($data['items'])&&!empty($data['items'])){
            $re_data = array_column($data['items'],'shop_id');
        }else{
            $re_data=[];
        }
        return $re_data;
    }

    /**
     * 删除购物车中的商品
     *
     * @author
     */
    public function delCartByCid()
    {
        $cart_ids = $_REQUEST['id'];
        $cart_id = !is_array($cart_ids) ? explode(',', $cart_ids) : $cart_ids;
        $user_id = request_int('user_id');
        $cart_list = $this->cartModel->getCatGoodsList(array('user_id' => $user_id));

        if (!isset($cart_list['items']) || !$cart_list['items']) {
            return $this->data->addBody(-140, array(), __('您的购物车没有商品'), 250);
        }
        $cart_id_arr = array_column($cart_list['items'], 'cart_id');
        $check = true;
        foreach ($cart_id as $key => $value) {
            if (!$value) {
                unset($cart_id[$key]);
                continue;
            }
            if (!in_array($value, $cart_id_arr)) {
                $check = false;
                break;
            }
        }
        // if(!$check){
        //     return $this->data->addBody(-140, array(), __('数据有误'), 250);
        // }
        $flag = $this->cartModel->removeCart($cart_id);
        if ($flag !== false) {
            $status = 200;
            $msg = __('删除成功');
        } else {
            $status = 250;
            $msg = __('删除失败');
        }
        $data = array($cart_id);
      return $this->data->addBody(-140, $data, $msg, $status);
    }
}