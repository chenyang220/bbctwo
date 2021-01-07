<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

class AppCartModel extends Cart
{

    public function getCardList($cond_row = array(), $order_row = array(),$user_id,$shop_id,$is_discount=false,$chain_id=0)
    {

        $cart_row = $this->getByWhere($cond_row, $order_row);
        $Goods_BaseModel   = new Goods_BaseModel();
        $Shop_BaseModel    = new Shop_BaseModel();
        $Order_GoodsModel  = new Order_GoodsModel();
        $Goods_CatModel    = new Goods_CatModel();
        $data = array();
        $invalid_goods = [];//无效商品
        //判断商品库存，商品状态，商品审核，店铺状态。 将无效的商品从购物车中删除
        if($chain_id <= 0)
        {
            foreach ($cart_row as $key => $val)
            {
                $goods_base = $Goods_BaseModel->checkGoods($val['goods_id']);
//                $goods_base = $Goods_BaseModel->checkGoods(3436);
                if (!$goods_base)
                {
                    //查找无效商品
                    $invalid_goods[] = $cart_row[$key];
                    unset($cart_row[$key]);
                }
            }
        }

        //门店商品需要进行第二次检验
        if($chain_id)
        {
            $cart_row = array_values($cart_row);

            $goods_id = $cart_row[0]['goods_id'];
            $num = $cart_row[0]['goods_num'];
            $chain_goods_base = $Goods_BaseModel->isValidChainGoods($goods_id, $num, $chain_id);
            if(!is_array($chain_goods_base))
            {
                $cart_row = array();
            }
        }
        //循环有效的购物车数组
        foreach ($cart_row as $key => $val)
        {
            $shop_base  = array();
            $goods_base = array();
            //获取商品信息
            $goods_base = $Goods_BaseModel->getGoodsInfo($val['goods_id']);
            //商品的售卖区域
            $val['transport_area_id'] = $goods_base['common_base']['transport_area_id'];
            $val['buy_able'] = 1;
            //商品重量
            $val['cubage'] = $goods_base['common_base']['common_cubage'];

            //计算商品库存，如果商品库存小于当前购物车中的商品数量，则将购物车中的商品数量改为商品库存
            if ($goods_base['goods_base']['goods_stock'] < $val['goods_num'])
            {
                $val['goods_num'] = $goods_base['goods_base']['goods_stock'];
                //将cart中的商品数量修改为商品库存
                $edit_cart_row['goods_num'] = $val['goods_num'];
                $this->editCart($val['cart_id'],$edit_cart_row);
            }

            $val['old_price']  = 0;
            $val['now_price']  = $goods_base['goods_base']['goods_price'];
            $val['down_price'] = 0;


            $IsHaveBuy = 0;
            if ($user_id)
            {
                //团购商品是否已经开始
                //查询该用户是否已购买过该商品
                $order_goods_cond['common_id']             = $goods_base['goods_base']['common_id'];
                $order_goods_cond['buyer_user_id']         = $user_id;
                $order_goods_cond['order_goods_status:!='] = Order_StateModel::ORDER_REFUND_FINISH;
                $order_list                                = $Order_GoodsModel->getByWhere($order_goods_cond);

                $order_goods_count        = count($order_list);
                $val['order_goods_count'] = $order_goods_count;
                $promotion = 0;  //用于记录店铺中是否存在活动商品

                if (isset($goods_base['goods_base']['promotion_type']) && $goods_base['goods_base']['promotion_type'])
                {
                    if ($goods_base['goods_base']['groupbuy_starttime'] < date('Y-m-d H:i:s') && $goods_base['goods_base']['groupbuy_endtime'] > date('Y-m-d H:i:s'))
                    {
                        //检测是否限购数量
                        $upper_limit = $goods_base['goods_base']['upper_limit'];
                        if ($upper_limit > 0 && $order_goods_count >= $upper_limit)
                        {
                            $IsHaveBuy = 1;
                        }
                        $val['old_price']  = $goods_base['goods_base']['goods_price'];
                        $val['now_price']  = $goods_base['goods_base']['promotion_price'];
                        $val['down_price'] = $goods_base['goods_base']['down_price'];
                    }

                    $promotion= 1;
                }

                //商品限购数量判断
                if ($goods_base['common_base']['common_limit'] > 0 && $order_goods_count >= $goods_base['common_base']['common_limit'])
                {
                    $IsHaveBuy = 1;
                }

                $val['IsHaveBuy'] = $IsHaveBuy;
            }

            //计算商品购买数量
            //计算限购数量
            if (isset($goods_base['goods_base']['upper_limit']))
            {
                if ($goods_base['goods_base']['upper_limit'] && $goods_base['common_base']['common_limit'])
                {
                    if ($goods_base['goods_base']['upper_limit'] >= $goods_base['common_base']['common_limit'])
                    {
                        $val['buy_limit'] = $goods_base['common_base']['common_limit'];
                    }
                    else
                    {
                        $val['buy_limit'] = $goods_base['goods_base']['upper_limit'];
                    }
                }
                elseif ($goods_base['goods_base']['upper_limit'] && !$goods_base['common_base']['common_limit'])
                {
                    $val['buy_limit'] = $goods_base['goods_base']['upper_limit'];
                }
                elseif (!$goods_base['goods_base']['upper_limit'] && $goods_base['common_base']['common_limit'])
                {
                    $val['buy_limit'] = $goods_base['common_base']['common_limit'];
                }
                else
                {
                    $val['buy_limit'] = 0;
                }
            }
            else
            {
                $val['buy_limit'] = $goods_base['common_base']['common_limit'];
            }

            //有限购数量且仍可以购买，计算还可购买的数量
            if ($val['buy_limit'] && !$IsHaveBuy)
            {
                $val['buy_residue'] = $val['buy_limit'] - $order_goods_count;
            }

            //商品总价格
            $val['sumprice'] = number_format($val['now_price'] * $val['goods_num'], 2, '.', '');
            //如果是分销商购买的供货商的商品，计算折扣
            if(Web_ConfigModel::value('Plugin_Distribution') && $shop_id)
            {
                $shopDistributorModel = new Distribution_ShopDistributorModel();
                $shopDistributorLevelModel = new Distribution_ShopDistributorLevelModel();

                //所有供货商，用于对商品操作的判断
                $suppliers = $shopDistributorModel->getByWhere(array('distributor_id' =>$shop_id));
                $suppliers  = array_column($suppliers,'shop_id');

                //查看折扣，改变对应供销商商品显示的价格
                $shopDistributorInfo     =  $shopDistributorModel->getOneByWhere(array('shop_id' =>$val['shop_id'],'distributor_id'=>$shop_id));
                if(!empty($shopDistributorInfo) && $shopDistributorInfo['distributor_enable'] == 1){
                    $distritutor_rate_info     = $shopDistributorLevelModel->getOne($shopDistributorInfo['distributor_level_id']);

                    if(!empty($distritutor_rate_info) && $distritutor_rate_info['distributor_leve_discount_rate']){
                        $val['now_price'] = $val['now_price']*$distritutor_rate_info['distributor_leve_discount_rate']/100;
                        $distritutor_rate = $val['sumprice'] - number_format($val['now_price'] * $val['goods_num'], 2, '.', '');
                        $val['sumprice'] -= $distritutor_rate;
                        $val['rate_price']  = $distritutor_rate;
                    }
                }
            }

            //该商品的交易佣金计算
            $Shop_ClassBindModel = new Shop_ClassBindModel();
            $goods_cat = $Shop_ClassBindModel->getByWhere(array('shop_id'=>$val['shop_id'],'product_class_id'=>$goods_base['goods_base']['cat_id']));
            if($goods_cat)
            {
                $goods_cat = current($goods_cat);
                $cat_commission = $goods_cat['commission_rate'];
            }
            else
            {
                $goods_cat = $Goods_CatModel->getOne($goods_base['goods_base']['cat_id']);
                if ($goods_cat)
                {
                    $cat_commission = $goods_cat['cat_commission'];
                }
                else
                {
                    $cat_commission = 0;
                }
            }

            $val['cat_commission'] = $cat_commission;
            $val['commission'] = number_format(($val['sumprice'] * $cat_commission / 100), 2, '.', '');
            //分佣开启，并且参与分佣
            $val['directseller_flag'] = 0;
            if(Web_ConfigModel::value('Plugin_Directseller')&&$goods_base['common_base']['common_is_directseller'])
            {
                $Distribution_ShopDirectsellerModel = new Distribution_ShopDirectsellerModel();
                $directseller_commission = 0;

                //获取该用户直属上三级用户
                $User_InfoModel = new User_InfoModel();
                $user_parent = $User_InfoModel->getUserPatents($user_id);

                //用户存在分销上级，并且这件商品是分销上级分销的商品则产生相应的分销佣金
                $val['directseller_commission_0'] = 0;
                $val['directseller_flag_0'] = 0;

                if($user_parent['user_parent_0'])
                {
                    //一级分佣
                    $a = $Distribution_ShopDirectsellerModel->checkDirectsellerGoods($user_parent['user_parent_0'],$goods_base['common_base']['shop_id']);
                    if($a)
                    {
                        $val['directseller_commission_0'] =  number_format(($val['sumprice']*$goods_base['common_base']['common_cps_rate']/100), 2, '.', '');
                        $val['directseller_flag_0'] = 1;
                        $val['directseller_flag'] = $goods_base['common_base']['common_is_directseller'];
                    }

                }

                $val['directseller_commission_1']=0;
                $val['directseller_flag_1'] = 0;
                if($user_parent['user_parent_1'])
                {
                    //二级分佣
                    $b = $Distribution_ShopDirectsellerModel->checkDirectsellerGoods($user_parent['user_parent_1'],$goods_base['common_base']['shop_id']);
                    if($b)
                    {
                        $val['directseller_commission_1'] = number_format(($val['sumprice']*$goods_base['common_base']['common_second_cps_rate']/100), 2, '.', '');
                        $val['directseller_flag_1'] = 1;
                        $val['directseller_flag'] = $goods_base['common_base']['common_is_directseller'];
                    }

                }

                $val['directseller_commission_2']=0;
                $val['directseller_flag_2'] = 0;
                if($user_parent['user_parent_2'])
                {
                    //三级分佣
                    $c = $Distribution_ShopDirectsellerModel->checkDirectsellerGoods($user_parent['user_parent_2'],$goods_base['common_base']['shop_id']);
                    if($c)
                    {
                        $val['directseller_commission_2'] = number_format(($val['sumprice']*$goods_base['common_base']['common_third_cps_rate']/100), 2, '.', '');
                        $val['directseller_flag_2'] = 1;
                        $val['directseller_flag'] = $goods_base['common_base']['common_is_directseller'];
                    }

                }

                $directseller_commission += $val['directseller_commission_0'] + $val['directseller_commission_1'] + $val['directseller_commission_2'];
            }

            $val['goods_base']  = $goods_base['goods_base'];
            $val['common_base'] = $goods_base['common_base'];
            if (!array_key_exists($val['shop_id'], $data))
            {
                //获取店铺信息
                $shop_base = $Shop_BaseModel->getOne($val['shop_id']);

                $data[$val['shop_id']]['shop_id']        = $shop_base['shop_id'];
                $data[$val['shop_id']]['shop_name']      = $shop_base['shop_name'];
                $data[$val['shop_id']]['shop_user_id']   = $shop_base['user_id'];
                $data[$val['shop_id']]['shop_user_name'] = $shop_base['user_name'];
                $data[$val['shop_id']]['district_id'] = $shop_base['district_id'];
                $data[$val['shop_id']]['promotion'] = $promotion;
                $data[$val['shop_id']]['shop_self_support'] = $shop_base['shop_self_support'];   //店铺是否自营  true 自营 false 非自营
                $data[$val['shop_id']]['goods'][]        = $val;
            }
            else
            {
                $data[$val['shop_id']]['goods'][] = $val;
            }

            if(isset($data[$val['shop_id']]['promotion']) && $data[$val['shop_id']]['promotion'] == 0)
            {
                $data[$val['shop_id']]['promotion'] = $promotion;
            }

            if (isset($data[$val['shop_id']]['sprice']))
            {
                //店铺总价
                $data[$val['shop_id']]['sprice'] = str_replace(',', '', $data[$val['shop_id']]['sprice']) * 1;
                $val['sumprice']                 = str_replace(',', '', $val['sumprice']) * 1;

                $data[$val['shop_id']]['sprice'] += $val['sumprice'];

                //店铺佣金
                $data[$val['shop_id']]['commission'] = str_replace(',', '', $data[$val['shop_id']]['commission']) * 1;
                $val['commission']                   = str_replace(',', '', $val['commission']) * 1;

                $data[$val['shop_id']]['commission'] += $val['commission'];
            }
            else
            {
                $data[$val['shop_id']]['sprice']     = $val['sumprice'];
                $data[$val['shop_id']]['commission'] = $val['commission'];
            }

            //分销商折扣
            if(isset($distritutor_rate)){
                if(isset($data[$val['shop_id']]['distributor_rate'])){
                    $data[$val['shop_id']]['distributor_rate']  += $distritutor_rate;
                }else{
                    $data[$val['shop_id']]['distributor_rate'] = 0;
                    $data[$val['shop_id']]['distributor_rate']  += $distritutor_rate;
                }
            }

            $data[$val['shop_id']]['sprice']     = number_format($data[$val['shop_id']]['sprice'] * 1, 2, '.', '');
            $data[$val['shop_id']]['commission'] = number_format($data[$val['shop_id']]['commission'] * 1, 2, '.', '');

            if($val['directseller_flag'])
            {
                $data[$val['shop_id']]['directseller_flag'] = $val['directseller_flag'];
            }

            if($val['directseller_flag_0'])
            {
                $data[$val['shop_id']]['directseller_flag_0'] = $val['directseller_flag_0'];
            }
            if($val['directseller_flag_1'])
            {
                $data[$val['shop_id']]['directseller_flag_1'] = $val['directseller_flag_1'];
            }
            if($val['directseller_flag_2'])
            {
                $data[$val['shop_id']]['directseller_flag_2'] = $val['directseller_flag_2'];
            }
        }
        $Voucher_BaseModel = new Voucher_BaseModel();
        $Promotion         = new Promotion();
        foreach ($data as $key => $val)
        {
            $data[$val['shop_id']]['ini_sprice'] = $data[$val['shop_id']]['sprice'];

            //如果是门店商品则不需要考虑满送和加价购活动
            $mansong_info = array();
            $increase_info = array();
            if(!$chain_id)
            {
                /*  店铺满即送  */
                $mansong_info = $Promotion->getShopOrderGift($val['shop_id'], $val['sprice']);

                if ($mansong_info)
                {
                    if (isset($mansong_info['gift_goods_id']))
                    {
                        $goods_base = $Goods_BaseModel->checkGoods($mansong_info['gift_goods_id']);
                        if (!$goods_base )
                        {
                            $mansong_info['gift_goods_id'] = 0;
                        }elseif ($goods_base['goods_base']['is_del'] == Goods_BaseModel::IS_DEL_YES){
                            $mansong_info['gift_goods_id'] = 0;
                        }else
                        {
                            $mansong_info['goods_name']  = $goods_base['goods_base']['goods_name'];
                            $mansong_info['goods_image'] = $goods_base['goods_base']['goods_image'];
                            $mansong_info['common_id']   = $goods_base['goods_base']['common_id'];

                        }
                    }

                    if (!$mansong_info['gift_goods_id'] && !$mansong_info['rule_discount'])
                    {
                        $mansong_info = array();
                    }

                }

                //计算满减后的金额
                if (isset($mansong_info['rule_discount']) && $mansong_info['rule_discount'])
                {
                    $data[$val['shop_id']]['sprice'] = number_format(($data[$val['shop_id']]['sprice'] - $mansong_info['rule_discount']),2,".","");
                    $val['sprice'] = $data[$val['shop_id']]['sprice'];
                }

                /*  加价购  */
                $increase_info = $Promotion->getOrderIncreaseInfo($val);

                //去除加价购商品中没有库存和不存在的商品，若是该活动下没有有效商品则去除该活动
                foreach ($increase_info as $inckey => $incval)
                {
                    if (!empty($incval['exc_goods']))
                    {
                        foreach ($incval['exc_goods'] as $excgkey => $excgval)
                        {
                            $goods_base = $Goods_BaseModel->checkGoods($excgval['goods_id']);

                            if (!$goods_base)
                            {
                                unset($incval['exc_goods'][$excgkey]);
                                unset($increase_info[$inckey]['exc_goods'][$excgkey]);
                            }
                            else
                            {
                                $increase_info[$inckey]['exc_goods'][$excgkey]['goods_name']  = $goods_base['goods_base']['goods_name'];
                                $increase_info[$inckey]['exc_goods'][$excgkey]['goods_image'] = $goods_base['goods_base']['goods_image'];
                            }
                        }

                        if (empty($incval['exc_goods']))
                        {
                            unset($increase_info[$inckey]);
                        }
                    }
                    else
                    {
                        unset($increase_info[$inckey]);
                    }
                }
            }

            $data[$val['shop_id']]['mansong_info'] = $mansong_info;
            $data[$key]['increase_info'] = $increase_info;

            //如果开启了会员折扣，就不在计算代金券
            $data[$key]['best_voucher'] = array();
            $data[$key]['voucher_base'] = array();
            if(!$is_discount)
            {
                //选出用户拥有的该店铺优惠券中最优的代金券
                $best_voucher = Voucher::user(['shop_id'=>$val['shop_id'],'voucher_end_date'=>get_date_time(),'voucher_start_date'=>get_date_time(),'limit'=>1,'order_price'=>$val['sprice'],'order_by'=>'voucher_price DESC','voucher_state'=>Voucher_BaseModel::UNUSED]);
                $data[$key]['best_voucher'] = $best_voucher;

                //店铺代金券(将用户拥有的该店铺的所有代金券都查询出来)
                $user_voucher = Voucher::user(['shop_id'=>$val['shop_id'],'order_by'=>'voucher_price ASC,voucher_end_date ASC','voucher_state'=>Voucher_BaseModel::UNUSED.','.Voucher_BaseModel::EXPIRED]);
                $user_voucher = Voucher::isable(['data'=>$user_voucher,'order_price'=>$val['sprice']]);
                $data[$key]['voucher_base'] = $user_voucher;
            }

            //获取该店铺可领取的代金券
            $shop_base = Voucher::shop(['shop_id'=>$val['shop_id'],'end_date'=>get_date_time(),'voucher_state'=>Voucher_BaseModel::UNUSED]);

            $data[$key]['shop_voucher'] = $shop_base;
        }
        $data['count'] = count($cart_row);
        $count = 0;

        $cart_list = array_values($data);
        if (!empty($cart_list)) {
            foreach ($cart_list as $key => $val) {
                if(isset($val['goods']))
                {
                    foreach ($val['goods'] as $k => $v) {
                        $count += $v['goods_num'];
                    }
                }

            }
        }
        //循环无效商品的数组
        $goods_info=[];
        $data['invalid_goods']=[];
        if($invalid_goods){
            foreach ($invalid_goods as $key => $val)
            {
                $goods_info[$key] =   $Goods_BaseModel->getOne($val['goods_id']);
                $goods_info[$key]['cart_id'] = $val['cart_id'];
            }
            $data['invalid_goods'] = $goods_info;
        }

        $data['cart_count'] = $count;
        return $data;
    }

}