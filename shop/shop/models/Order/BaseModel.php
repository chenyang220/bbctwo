<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Order_BaseModel extends Order_Base
{

    const ORDER_IS_VIRTUAL = 1;            //虚拟订单
    const VIRTUAL_USED = 1;                //虚拟订单已使用
    const VIRTUAL_UNUSE = 0;                    //虚拟订单未使用
    const ORDER_IS_REAL = 0;                    //实物订单
    const IS_BUYER_CANCEL = 1;                //买家取消订单
    const IS_SELLER_CANCEL = 2;                //卖家取消订单
    const IS_ADMIN_CANCEL = 3;                //平台取消
    const IS_NOT_SETTLEMENT = 0;                //未结算
    const IS_SETTLEMENT = 1;                    //已结算

    const NO_BUYER_HIDDEN = 0;                //买家不隐藏订单
    const NO_SELLER_HIDDEN = 0;                //卖家不隐藏订单
    const NO_SUBUSER_HIDDEN = 0;                //主管账号不隐藏订单

    const IS_BUYER_HIDDEN = 1;                //买家隐藏订单
    const IS_SELLER_HIDDEN = 1;                //卖家隐藏订单
    const IS_SUBUSER_HIDDEN = 1;                //主管账号隐藏订单

    const IS_BUYER_REMOVE = 2;                //买家删除订单
    const IS_SELLER_REMOVE = 2;                //卖家删除订单
    const IS_SUBUSER_REMOVE = 2;               //主账号删除订单

    const RETURN_ALL = 2;
    const RETURN_SOME = 1;

    const REFUND_NO = 0;
    const REFUND_IN = 1;
    const REFUND_COM = 2;

    //订单取消身份
    const CANCEL_USER_BUYER = 1;
    const CANCEL_USER_SELLER = 2;
    const CANCEL_USER_SYSTEM = 3;

    //买家是否评价
    const BUYER_EVALUATE_NO = 0;
    const BUYER_EVALUATE_YES = 1;
    const BUYER_EVALUATE_AGAIN = 2; //已追加评价


    //买家是否评价
    const SELLER_EVALUATE_NO = 0;
    const SELLER_EVALUATE_YES = 1;

    //订单来源
    const FROM_PC       = 1;    //来源于pc端
    const FROM_WAP      = 2;    //来源于WAP手机端
    const FROM_WEBPOS   = 3;    //来源于WEBPOS线下下单
    const FROM_LY       = 4;    //来源于旅游端
    const FROM_JD       = 5;    //来源于酒店端
    const FROM_CX       = 6;    //来源于出行下单
    const FROM_MS       = 7;    //来源于美食下单
    //支付方式
    const PAY_LINE 		= 1;  	//在线支付
    const PAY_DELIVERY 	= 2; 	//货到付款

    const  ORDER_DISTRI_SELLER_TYPE = 1; //直销
    const  ORDER_BUTION_SELLER_TYPE = 2; //分销
    //状态
    public static $state = array(
        '1' => 'wait_operate',
        //已出账
        '2' => 'seller_comfirmed',
        //商家已确认
        '3' => 'platform_comfirmed',
        //平台已审核
        '4' => 'finish',
        //结算完成
    );

    public static $orderType = array(
        //虚拟订单
        '0' => 'is_physical',
        //实物订单
        '1' => 'is_virtaul',
    );

    public static $orderEvaluatBuyer = array(
        //买家已评价
        '1' => 'is_evaluated',
        //买家未评价
        '0' => 'is_uevaluate',
    );

    public static $orderEvaluatSeller = array(
        //买家已评价
        '1' => 'is_evaluated',
        //买家未评价
        '0' => 'is_uevaluate',
    );

    public function __construct()
    {
        parent::__construct();


        $this->cancelIdentity = array(
            '1' => __('买家'),
            '2' => __('商家'),
            '3' => __('系统'),
        );

        $this->goodsRefundState = array(
            '0' => __("无退货"),
            //无退货
            '1' => __("退货中"),
            //退货中
            '2' => __("退货完成"),
            //退货完成
            '3' =>__("商家拒绝退货"),
        );

        $this->goodsReturnState = array(
            '0' => __("无退款"),
            //无退货
            '1' => __("退款中"),
            //退货中
            '2' => __("退款完成"),
            //退货完成
            '3' => __("商家拒绝退款"),
        );


    }


    /**
     * 读取分页列表
     * Zhuyt
     * @param  int $config_key 主键值
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getBaseList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);
        $Shop_BaseModel = new Shop_BaseModel();
        $Order_GoodsModel = new Order_GoodsModel();
        $Order_StateModel = new Order_StateModel();
        $Goods_EvaluationModel = new Goods_EvaluationModel();
        $PinTuan_TempModel = new PinTuan_Temp();
        if ($data['items']) {
            foreach ($data['items'] as $key => $val) {
                //若是待付款订单，计算系统取消订单时间
                if ($val['order_status'] == Order_StateModel::ORDER_WAIT_PAY) {
                    $data['items'][$key]['cancel_time'] = date('Y-m-d H:i:s', strtotime($val['order_create_time']) + Yf_Registry::get('wait_pay_time'));
                    if ($data['items'][$key]['cancel_time'] <= get_date_time()) {
                        //修改订单状态 - 将订单状态改为取消
                        $this->cancelOrder($val['order_id']);
                    }
                }

                //若是已发货订单，计算系统自动确认收货时间
                if ($val['order_status'] == Order_StateModel::ORDER_WAIT_CONFIRM_GOODS) {
                    //$data['items'][$key]['confirm_time'] = date('Y-m-d H:i:s', strtotime($val['order_shipping_time']) + Yf_Registry::get('confirm_order_time'));
                    
                    //if($data['items'][$key]['confirm_time'] <= get_date_time())
                    if (strtotime($val['order_receiver_date']) < strtotime(date("Y-m-d"))) {
                        //修改订单状态 - 将订单状态改为已收货
                        //虚拟订单过期自动退款（未退款）
                        if($val['order_is_virtual'] == Order_BaseModel::ORDER_IS_VIRTUAL && $val['order_refund_status'] == Order_BaseModel::REFUND_NO)
                        {
                            $this->virtualReturn($val['order_id']);
                        }
                        else
                        {
                            $this->confirmOrder($val['order_id']);
                        }
                    }
                }
                //取出物流公司名称
                if (!empty($val['order_shipping_express_id'])) {
                    $expressModel = new ExpressModel();
                    $express_base = $expressModel->getExpress($val['order_shipping_express_id']);
                    $express_base = pos($express_base);
                    $data['items'][$key]['express_name'] = $express_base['express_name'];
                } else {
                    $data['items'][$key]['express_name'] = '';
                }

                //判断是否拼团
                $pintuan_temp = $PinTuan_TempModel->getPtInfoByOrderId($val['order_id']);
                if($pintuan_temp)
                {
                    $data['items'][$key]['pintuan_temp_order'] = 1;
                }else
                {
                    $data['items'][$key]['pintuan_temp_order'] = 0;
                }
            }
        }

        //取出所有shop_id 判断为哪家店铺的商品
        $shop_ids = array_column($data['items'], 'shop_id');
        if (!empty($shop_ids)) {
            $cond_row = array();
            $cond_row['shop_id:IN'] = $shop_ids;
            $shop_list = $Shop_BaseModel->getByWhere($cond_row, array());
        }
        $shop_name_list = array();
        if(!empty($shop_list)){
            foreach($shop_list as $val){
                $shop_name_list[$val['shop_id']] = $val['shop_name'];
            }
        }

        if ($data['items']) {
            foreach ($data['items'] as $key => $val) {
                $data['items'][$key]['directseller_commission_0']=0;
                $data['items'][$key]['directseller_commission_1']=0;
                //订单完成时间(确认收货时间)
                $order_finished_time = $val['order_finished_time'];
                $order_nums = 0;
                $data['items'][$key]['order_state_con'] = $Order_StateModel->orderState[$val['order_status']];
                $data['items'][$key]['order_refund_status_con'] = $Order_StateModel->orderRefundState[$val['order_refund_status']];
                $data['items'][$key]['shop_name'] = $shop_name_list[$val['shop_id']];
                //若是待付款订单，计算系统取消订单时间
                if ($val['order_status'] == Order_StateModel::ORDER_WAIT_PAY) {
                    $data['items'][$key]['cancel_time'] = date('Y-m-d H:i:s', strtotime($val['order_create_time']) + Yf_Registry::get('wait_pay_time'));
                }
                //若是已发货订单，计算系统自动确认收货时间
                /*if ($val['order_status'] == Order_StateModel::ORDER_WAIT_CONFIRM_GOODS)
                {
                    $data['items'][$key]['confirm_time'] = date('Y-m-d H:i:s', strtotime($val['order_shipping_time']) + Yf_Registry::get('confirm_order_time'));
                }*/

                //若为退款中订单，则查找退款单id
                if ($val['order_refund_status'] != Order_StateModel::ORDER_REFUND_NO) {
                    $Order_ReturnModel = new Order_ReturnModel();
                    $order_return_id = $Order_ReturnModel->getKeyByWhere(array('order_number' => $val['order_id'], 'order_goods_id' => '0'));
                    $data['items'][$key]['order_return_id'] = $order_return_id[0];
                }

                //放入店铺信息
                $order_goods[$key]['shop_self_support'] = $shop_list[$val['shop_id']]['shop_self_support'];
                //查找订单商品
                $order_goods = $Order_GoodsModel->getByWhere(array('order_id' => $val['order_id']));

                $Order_ReturnModel = new Order_ReturnModel();
                $rgl_show = 0;
                foreach ($order_goods as $okey => $oval) {
                    $data['items'][$key]['directseller_commission_0']+=($oval['directseller_commission_0']-$oval['directseller_commission_0_refund']);
                    $data['items'][$key]['directseller_commission_1']+=($oval['directseller_commission_1']-$oval['directseller_commission_1_refund']);
                    $order_goods[$okey]['spec_text'] = $oval['order_spec_info'] ? implode('，', $oval['order_spec_info']) : '';
                    //判断该订单商品被评论的次数
                    $goods_evaluation_row = array();
                    $goods_evaluation_row['order_id'] = $val['order_id'];
                    $goods_evaluation_row['goods_id'] = $oval['goods_id'];
                    $goods_evaluation = $Goods_EvaluationModel->getByWhere($goods_evaluation_row);

                    $order_goods[$okey]['evaluation_count'] = count($goods_evaluation);

                    //判断订单商品的退货状态
                    $order_goods[$okey]['goods_refund_status_con'] = $Order_ReturnModel->getReturnState($oval['order_goods_id'],2);  //退货
                    $order_goods[$okey]['goods_return_status_con'] = $Order_ReturnModel->getReturnState($oval['order_goods_id'],1);  //退款
                    $order_goods[$okey]['goods_virtual_return_status_con'] = $Order_ReturnModel->getReturnState($oval['order_goods_id'],3);  //虚拟退款

                    //查找订单商品的市场价格
                    $Goods_BaseModel = new Goods_BaseModel();
                    $goods_info = $Goods_BaseModel->getOne($oval['goods_id']);
                    $order_goods[$okey]['old_price'] = $goods_info['goods_market_price'];
                    $order_goods[$okey]['is_del'] = $goods_info['is_del'];
                    $goods_cat_id = $goods_info['cat_id'];//商品分类id

                    //查找退货id
                    if ($oval['goods_refund_status'] !== Order_StateModel::ORDER_GOODS_RETURN_NO) {
                        $Order_ReturnModel = new Order_ReturnModel();
                        $order_goods_return_id = $Order_ReturnModel->getKeyByWhere(array('order_number' => $val['order_id'],
                            'order_goods_id' => $oval['order_goods_id'],
                            'return_type' => 2,
                        ));
                        $order_goods[$okey]['order_refund_id'] = $order_goods_return_id[0];

                    }

                    //查找退款id
                    if ($oval['goods_refund_status'] !== Order_StateModel::ORDER_GOODS_RETURN_NO) {
                        $Order_ReturnModel = new Order_ReturnModel();
                        $order_goods_return_id = $Order_ReturnModel->getKeyByWhere(array('order_number' => $val['order_id'],
                            'order_goods_id' => $oval['order_goods_id'],
                            'return_type' => 1,));
                        $order_goods[$okey]['order_return_id'] = $order_goods_return_id[0];
                    }

                    //如果订单是虚拟订单
                    if($val['order_is_virtual'] && $oval['goods_refund_status'] !== Order_StateModel::ORDER_GOODS_RETURN_NO)
                    {
                        $Order_ReturnModel = new Order_ReturnModel();
                        $order_goods_return_id = $Order_ReturnModel->getKeyByWhere(array('order_number' => $val['order_id'],
                            'order_goods_id' => $oval['order_goods_id'],
                            'return_type' => 3,));
                        $order_goods[$okey]['order_return_id'] = $order_goods_return_id[0];
                    }

                    $order_nums += $oval['order_goods_num'];

                    //商品规格
                    if ($oval['order_spec_info']){
                        $order_goods[$okey]['title_order_spec_info'] = implode(",",$oval['order_spec_info']);
                    }

                    //分类商品退货期处理 @nsy 2019-04-29
                    if($goods_cat_id ){
                        $rgl_ret = $this->getCatReturnGoodsLimit($goods_cat_id);
                        $order_goods[$okey]['rgl_val'] = $rgl_ret['rgl_val'];
                        $order_goods[$okey]['rgl_txt'] = $rgl_ret['rgl_txt'];
                        $order_goods[$okey]['rgl_flag'] = 1;
                        //已付款未发货区间段
                        if( Order_StateModel::ORDER_PAYED<=$val['order_status'] && $val['order_status']<=Order_StateModel::ORDER_WAIT_PREPARE_GOODS){
                            !$rgl_ret['rgl_val'] &&  $order_goods[$okey]['rgl_flag'] = 0;//不支持退款
                        }
                        if($val['order_status'] == Order_StateModel::ORDER_FINISH){
                            if($rgl_ret['rgl_val']>0){
                                $rgl_time = strtotime($order_finished_time);//订单完成时间
                                if($rgl_time>0){
                                    $rgl_time = $rgl_time+($rgl_ret['rgl_val']*24*60*60);
                                    //rgl_flag：未超过分类商品退货标识(0:已超过退货期，或者不支持退货；1：在退货期内)
                                    (time()-$rgl_time)>0 &&  $order_goods[$okey]['rgl_flag'] = 0;//商品层面过期标识
                                }
                            }else{
                                //不支持退款,直接标识为过期
                                $order_goods[$okey]['rgl_flag'] = 0;
                            }
                        }
                        $order_goods[$okey]['rgl_strtotime'] = $rgl_time;//页面做定时刷新退货期的时候会用到
                        if(!$order_goods[$okey]['rgl_flag']){
                            $rgl_show+=1;
                        }
                    }
                }
                if(count($order_goods)-$rgl_show>0){
                    $data['items'][$key]['rgl_desc'] = 0;//存在未超过分类商品退货期的商品，则在订单右上方不显示：已过退货期
                }else{
                    $data['items'][$key]['rgl_desc'] = 1;//显示：已过退货期，不能退货/退款
                }

                //若是该订单已完成，判断其交易投诉的有效时间
                $Web_ConfigModel = new Web_ConfigModel();
                $day = $Web_ConfigModel->getOne('complain_datetime');
                $day = $day['config_value'];
                $data['items'][$key]['complain_day'] = $day;
                if ($val['order_status'] == Order_StateModel::ORDER_FINISH) {

                    $comtime = $day * 86400;

                    $complain_time = strtotime($val['order_finished_time']) + $comtime;

                    //当前时间在投诉有效期内
                    if ($complain_time > time()) {
                        $data['items'][$key]['complain_status'] = 1;
                    } else {
                        $data['items'][$key]['complain_status'] = 0;
                    }
                } else {
                    $data['items'][$key]['complain_status'] = 0;
                }


                $data['items'][$key]['goods_list'] = array_values($order_goods);

                $data['items'][$key]['order_nums'] = $order_nums;

                //查找订单退款数量
                $Order_ReturnModel = new Order_ReturnModel();
                $or = $Order_ReturnModel->getByWhere(array('order_number'=>$val['order_id'],'return_type'=>1));
                $orn = 0;
                if($or){
                    foreach($or as $ork => $orv){
                        $orn += $orv['order_goods_num'];
                    }
                }

                $data['items'][$key]['order_refund_nums'] = $orn;

            }
        }
        return $data;
    }

    /**
     * @param $goodsLists
     * @return mixed
     * 获取分类商品退货期
     * @nsy 2019-04-29
     */
    public function  getCatReturnGoodsLimit($cat_id){
        $Goods_CatModel = new Goods_CatModel();
        //查找该分类的父级分类
        $parent_cat = $Goods_CatModel->getCatParent($cat_id);
        //封装分类商品退货期(未设置该值，统一按不支持退货处理) @nsy 2019-04-28
        $return_goods_limit = current($parent_cat);
        $rgl_val = $return_goods_limit['return_goods_limit'];
        $rgl_val<0 && $rgl_val=0;
       
        switch ($rgl_val){
            case 0:
                $rgl_str = '不支持退款退货';
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
        return array(
            'rgl_val'=>$rgl_val,
            'rgl_txt'=>$rgl_str
        );
    }

    /*
     *  zhuyt
     *
     * 订单详情
     */
    public function getOrderDetail($order_id = null)
    {
        $data = $this->getOneByWhere(array('order_id' => $order_id));
        $order_finished_time = $data['order_finished_time'];
        $Order_StateModel = new Order_StateModel();

        $data['order_state_con'] = $Order_StateModel->orderState[$data['order_status']];

        //订单退款状态
        $data['order_refund_status_con'] = $Order_StateModel->orderRefundState[$data['order_refund_status']];

        //若为虚拟订单并且虚拟兑换码已发放,计算还有多少未使用的兑换码，虚拟商品是否支付过期退款，虚拟商品的过期时间
        if ($data['order_status'] != Order_StateModel::ORDER_WAIT_PAY && $data['order_is_virtual'] == Order_BaseModel::ORDER_IS_VIRTUAL)
        {
            $Order_GoodsVirtualCodeModel = new Order_GoodsVirtualCodeModel();
            $cond_row                    = array();
            $cond_row['order_id']        = $data['order_id'];

            $data['code_list'] = $Order_GoodsVirtualCodeModel->getVirtualCode($cond_row);

            $cond_row['virtual_code_status'] = Order_GoodsVirtualCodeModel::VIRTUAL_CODE_NEW;
            $new_code                        = $Order_GoodsVirtualCodeModel->getVirtualCode($cond_row);
            $data['new_code']                = count($new_code);


            //获取所有的订单商品id
            $code_order_goods_id = array_column($data['code_list'], 'order_goods_id');
            $Order_GoodsModel    = new Order_GoodsModel();
            $Goods_CommonModel   = new Goods_CommonModel();

            //查找订单商品
            $code_order_goods = $Order_GoodsModel->getByWhere(array('order_goods_id:IN' => $code_order_goods_id));


            //查找订单商品的common信息
            $code_order_goods_common = array();//array_column($code_order_goods, 'common_id');
            foreach ($code_order_goods as $commonkey => $commonval)
            {
                $code_order_goods_common[$commonval['order_goods_id']] = $Goods_CommonModel->getOne($commonval['common_id']);
            }
            foreach ($data['code_list'] as $codekey => $codeval)
            {
                //查找订单商品
                $data['code_list'][$codekey]['common_virtual_refund'] = $code_order_goods_common[$data['code_list'][$codekey]['order_goods_id']]['common_virtual_refund'];
                $data['code_list'][$codekey]['common_virtual_date']   = $code_order_goods_common[$data['code_list'][$codekey]['order_goods_id']]['common_virtual_date'];
                $data['common_virtual_date']    = $code_order_goods_common[$data['code_list'][$codekey]['order_goods_id']]['common_virtual_date'];
            }
        }

        //若是待付款订单，计算系统取消订单的时间
        if ($data['order_status'] == Order_StateModel::ORDER_WAIT_PAY) {
            if($data['is_seckill']==1){
                $seckill_time =(int)Web_ConfigModel::value('promotion_seckill_time')*60;
                $data['cancel_time'] = date('Y-m-d H:i:s', strtotime($data['order_create_time'])+$seckill_time);
            }else{
                $data['cancel_time'] = date('Y-m-d H:i:s', strtotime($data['order_create_time']) + Yf_Registry::get('wait_pay_time'));
            }
            
        }

        //若是已发货订单，计算系统自动确认收货时间
        /*if ($data['order_status'] == Order_StateModel::ORDER_WAIT_CONFIRM_GOODS)
        {
            $data['confirm_time'] = date('Y-m-d H:i:s', strtotime($data['order_shipping_time']) + Yf_Registry::get('confirm_order_time'));
        }*/

        //若为退款中订单，则查找退款单id
        if ($data['order_refund_status'] != Order_StateModel::ORDER_REFUND_NO) {
            $Order_ReturnModel = new Order_ReturnModel();
            $order_return_id = $Order_ReturnModel->getKeyByWhere(array('order_number' => $data['order_id'], 'order_goods_id' => '0'));
            $data['order_return_id'] = $order_return_id[0];
        }

        //若是已完成订单，计算交易投诉的有效时间
        $Web_ConfigModel = new Web_ConfigModel();
        $day = $Web_ConfigModel->getOne('complain_datetime');
        $day = $day['config_value'];
        $data['complain_day'] = $day;
        if ($data['order_status'] == Order_StateModel::ORDER_FINISH) {
            $comtime = $day * 86400;

            $complain_time = strtotime($data['order_finished_time']) + $comtime;

            //当前时间在投诉有效期内
            if ($complain_time > time()) {
                $data['complain_status'] = 1;
            } else {
                $data['complain_status'] = 0;
            }
        } else {
            $data['complain_status'] = 0;
        }

        //获取订单评价状态
        $data['order_buyer_evaluation_status_con'] = Order_BaseModel::$orderEvaluatBuyer[$data['order_buyer_evaluation_status']];


        //获取订单取消者身份
        if ($data['order_cancel_identity']) {
            $data['cancel_identity'] = $this->cancelIdentity[$data['order_cancel_identity']];
        }

        //查找店铺信息
        $Shop_CompanyModel = new Shop_CompanyModel();
        $Shop_Company = $Shop_CompanyModel->getOne($data['shop_id']);
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_base = $Shop_BaseModel->getOne($data['shop_id']);
        $data['shop_name'] = $shop_base['shop_name'];
        $data['shop_address'] = $Shop_Company['shop_company_address'];
        $data['shop_phone'] = $Shop_Company['company_phone']?$Shop_Company['company_phone']:$Shop_Company['contacts_phone'];
        $data['shop_self_support'] = $shop_base['shop_self_support'];
        $data['shop_logo'] = $shop_base['shop_logo'];
        //获取店铺IM
        if ($shop_base['shop_self_support'] == 'true' && Web_ConfigModel::value('self_shop_im')) {
            $data['self_shop_im'] = $Shop_BaseModel->getSelfShopIm($shop_base['user_name'], $shop_base['shop_self_support']);
        }

        //查找订单商品
        $Goods_CommonModel = new Goods_CommonModel();
        $Order_GoodsModel = new Order_GoodsModel();
        $Goods_EvaluationModel = new Goods_EvaluationModel();
        $order_goods = $Order_GoodsModel->getByWhere(array('order_id' => $data['order_id']));
        //是否拼团
        $pinTuan_TempModel = new PinTuan_Temp();
        $pintuan_temp = $pinTuan_TempModel->getPtInfoByOrderId($data['order_id']);

        $Order_ReturnModel = new Order_ReturnModel();
        $order_nums = 0;

        //查找订单商品分类
        $Goods_BaseModel = new Goods_BaseModel();
        foreach ($order_goods as $okey => $oval) {
            //判断该订单商品被评论的次数
            $goods_evaluation_row = array();
            $goods_evaluation_row['order_id'] = $data['order_id'];
            $goods_evaluation_row['goods_id'] = $oval['goods_id'];
            $goods_evaluation = $Goods_EvaluationModel->getByWhere($goods_evaluation_row);

            $order_goods[$okey]['evaluation_count'] = count($goods_evaluation);

            //获取订单商品退货状态
            $order_goods[$okey]['goods_refund_status_con'] = $Order_ReturnModel->getReturnState($oval['order_goods_id'],2);  //退货
            $order_goods[$okey]['goods_return_status_con'] = $Order_ReturnModel->getReturnState($oval['order_goods_id'],1);  //退款
            $order_goods[$okey]['return_shop_handle'] = $Order_ReturnModel->getReturnShopHandel($oval['order_goods_id'],1);
            $order_goods[$okey]['goods_virtual_return_status_con'] = $Order_ReturnModel->getReturnState($oval['order_goods_id'],3);  //虚拟退款

            //若为退款中订单，则查找退货单id
            if ($oval['goods_refund_status'] !== Order_StateModel::ORDER_REFUND_NO) {
                $Order_ReturnModel = new Order_ReturnModel();
                $order_return_id = $Order_ReturnModel->getKeyByWhere(array('order_number' => $data['order_id'],
                                                                         'return_type' => 2,
                                                                         'order_goods_id' => $oval['order_goods_id']));
                $order_goods[$okey]['order_refund_id'] = $order_return_id[0];
            }

            //查找退款id
            if ($oval['goods_return_status'] !== Order_StateModel::ORDER_GOODS_RETURN_NO) {
                //判断是否是虚拟订单
                if ($data['order_is_virtual']) {
                    $Order_ReturnModel = new Order_ReturnModel();
                    $order_goods_return_id = $Order_ReturnModel->getKeyByWhere(array('order_number' => $data['order_id'],
                                                                                   'return_type' => 3,
                                                                                   'order_goods_id' => $oval['order_goods_id']));
                    $order_goods[$okey]['order_return_id'] = $order_goods_return_id[0];
                } else {
                    $Order_ReturnModel = new Order_ReturnModel();
                    $order_goods_return_id = $Order_ReturnModel->getKeyByWhere(array('order_number' => $data['order_id'],
                                                                                   'return_type' => 1,
                                                                                   'order_goods_id' => $oval['order_goods_id']));
                    $order_goods[$okey]['order_return_id'] = $order_goods_return_id[0];
                }
            }
            //拼团不可退款
            if ($pintuan_temp) {
                $order_goods[$okey]['pintuan_temp_order'] = 1;
            } else {
                $order_goods[$okey]['pintuan_temp_order'] = 0;
            }

            $order_goods[$okey]['spec_text'] = $oval['order_spec_info'] ? implode('，', $oval['order_spec_info']) : '';
            $order_nums += $oval['order_goods_num'];

            $goods_info = $Goods_BaseModel->getOne($oval['goods_id']);
            $goods_cat_id = $goods_info['cat_id'];//商品分类id
            if($goods_cat_id ){
                $rgl_ret = $this->getCatReturnGoodsLimit($goods_cat_id);
                $order_goods[$okey]['rgl_val'] = $rgl_ret['rgl_val'];
                $order_goods[$okey]['rgl_txt'] = $rgl_ret['rgl_txt'];
                $order_goods[$okey]['rgl_flag'] = 1;
                $rgl_time = strtotime($order_finished_time);//订单完成时间
                //已付款未发货区间段
                if( Order_StateModel::ORDER_PAYED<=$data['order_status'] && $data['order_status']<=Order_StateModel::ORDER_WAIT_PREPARE_GOODS){
                    !$rgl_ret['rgl_val'] &&  $order_goods[$okey]['rgl_flag'] = 0;//不支持退款
                }
                if($data['order_status'] == Order_StateModel::ORDER_FINISH){
                    if($rgl_ret['rgl_val']>0){
                        $rgl_time = strtotime($order_finished_time);//订单完成时间
                        if($rgl_time>0){
                            $rgl_time = $rgl_time+($rgl_ret['rgl_val']*24*60*60);
                            //rgl_flag：未超过分类商品退货标识(0:已超过退货期，或者不支持退货；1：在退货期内)
                            (time()-$rgl_time)>0 &&  $order_goods[$okey]['rgl_flag'] = 0;//商品层面过期标识
                        }
                    }else{
                        //不支持退款,直接标识为过期
                        $order_goods[$okey]['rgl_flag'] = 0;
                    }
                }
                /*if($rgl_time>0){//已收货，支持退货,退款
                    if($rgl_ret['rgl_val']>0){
                        $rgl_time = $rgl_time+($rgl_ret['rgl_val']*24*60*60);
                        //rgl_flag：未超过分类商品退货标识(0:已超过退货期，或者不支持退货；1：在退货期内)
                        (time()-$rgl_time)>0 &&  $order_goods[$okey]['rgl_flag'] = 0;//商品层面过期标识
                    }else{
                        //不支持退款,直接标识为过期
                        $order_goods[$okey]['rgl_flag'] = 0;
                    }
                }*/
                $order_goods[$okey]['rgl_strtotime'] = $rgl_time;//页面做定时刷新退货期的时候会用到
            }
        }
        //拼团不可退款
        if ($pintuan_temp) {
            $data['pintuan_temp'] = 1;
        } else {
            $data['pintuan_temp'] = 0;
        }
        $data['goods_list'] = array_values($order_goods);
        //订单总商品数量
        $data['order_nums'] = $order_nums;
        //查找订单退款数量
        $Order_ReturnModel = new Order_ReturnModel();
        $or = $Order_ReturnModel->getByWhere(array('order_number'=>$order_id,'return_type'=>1));
        $orn = 0;
        if($or){
            foreach($or as $ork => $orv){
                $orn += $orv['order_goods_num'];
            }
        }

        $data['order_refund_nums'] = $orn;
        $data['is_open_im'] = Yf_Registry::get('im_statu');

        //自提商品
        if ($data['chain_id'] > 0) {
            $chain_base_model = new Chain_BaseModel;
            $chain_info = $chain_base_model->getChainInfo($data['chain_id']);
            $Order_GoodsChainCodeModel = new Order_GoodsChainCodeModel();
            $chain_code = current($Order_GoodsChainCodeModel->getByWhere(array('order_id' => $data['id'])));
            $data['chain_info'] = $chain_info;
            $data['chain_code'] = $chain_code ?: [];
        }
        return $data;

    }

    public function getBaseExcel($cond_row = array(), $order_row = array())
    {

        $data = $this->getByWhere($cond_row, $order_row);

        foreach ($data as $k => $v) {
            $data[$k]['order_id'] = " " . $v['order_id'] . " ";
        }

        return array_values($data);
    }

    /**
     * 读取分页列表
     *
     * @param  int $config_key 主键值
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getDetailList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        return $this->listByWhere($cond_row, $order_row, $page, $rows);
    }

    /*
     * 获取结算表下面的相关订单数据
     */

    public function getOrderDetailList($cond_row = array(), $order_row = array(), $page = 1, $rows = 10)
    {
        return $this->listByWhere($cond_row, $order_row, $page, $rows);
    }

    /*
     *  zhuyt
     *
     * 结算订单表
     * 10/22  修改订单中不再计算退款金额，退款金额通过退货单进行计算
     */
    public function settleOrder($cond_row = array(), $order_row = array())
    {
        $data = $this->getByWhere($cond_row, $order_row);
        //处理分类商品退货期
        // $start_time =  $cond_row['order_finished_time:>='];
        // $end_time =	$cond_row['order_finished_time:<='];
        // $Order_GoodsModel =  new Order_GoodsModel();
        // $Order_BaseModel = new Order_BaseModel();
        // foreach ($data as $key=>$item){
        //     $order_finished_time = $item['order_finished_time'];
        //     $ret = $Order_GoodsModel->listByWhere(array('order_id'=>$item['order_id']));//查询订单相关的商品
        //     foreach ($ret['items'] as $it){
        //         $rgl_ret = $Order_BaseModel->getCatReturnGoodsLimit($it['goods_class_id']);//根据商品分类查询分类退货期
        //         $rgl_val = $rgl_ret['rgl_val'];
        //         $rgl_time = strtotime($order_finished_time);//订单完成时间
        //         if(!$rgl_time || !$rgl_val) return ;
        //         $rgl_time = $rgl_time+($rgl_val*24*60*60);//分类商品退货有效截止时间
        //         if(($start_time<$rgl_time || $rgl_time>$end_time) && isset($data[$key])){
        //               unset($data[$key]);
        //         }
        //     }
        // }


        //订单金额
        $order_amount = 0;
        //运费
        $shipping_amount = 0;
        //佣金金额
        $commission_amount = 0;
        //退款金额
        $return_amount = 0;
        //退款佣金
        $commission_return_amount = 0;

        //货到付款补丁
        $paymet_id_fee = array();
        if(!empty($data)){
            foreach ($data as $r){
                $paymet_id_fee[] = array('payment_id'=>$r['payment_id'],'order_commission_fee'=>$r['order_commission_fee']);
            }
        }

        //结算订单部分的费用
        $res = array(
            'order_amount' => array_sum(array_column($data,'order_payment_amount')),
            'shipping_amount' => array_sum(array_column($data,'order_shipping_fee')),
            'commission_amount' => array_sum(array_column($data,'order_commission_fee')),
            'redpacket_amount' => array_sum(array_column($data,'order_rpt_price')),
            'order_directseller_commission' => array_sum(array_column($data,'order_directseller_commission')),
            'paymet_id_fee' => $paymet_id_fee, //货到付款补丁
        );

        //修改订单表中的结算时间与结算状态
        $id_row = array_keys($data);
        $this->editBase($id_row, array('order_settlement_time' => get_date_time() ,'order_is_settlement' =>Order_SettlementModel::SETTLEMENT_WAIT_OPERATE));

        return $res;

    }

    /*
     *  windfnn
     *
     * 获取卖家订单列表
     */
    public function getOrderList($cond_row = array(), $order_row = array(), $page = 1, $rows = 15)
    {
        //分销商分销的商品
        $GoodsCommonModel        = new Goods_CommonModel();
        $Order_GoodsModel = new Order_GoodsModel();
        $dist_commons = $GoodsCommonModel->getByWhere(array('shop_id' => Perm::$shopId,"common_parent_id:>" => 0,'product_is_behalf_delivery' => 1));

        $dist_common_ids = array();
        if(!empty($dist_commons)){
            $dist_common_ids  = array_column($dist_commons,'common_id');
        }

        //分页
        $Yf_Page = new Yf_Page();
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);

        $Order_StateModel = new Order_StateModel();
        $Order_InvoiceModel = new Order_InvoiceModel();
        $Order_ReturnModel = new Order_ReturnModel();

        //分页
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        $data['page_nav'] = $page_nav;

        $order_id_row = array_column($data['items'], 'order_id');
        $order_goods_list = $Order_GoodsModel->getByWhere(array('order_id:IN' => $order_id_row));

        $goods_list = array();

        foreach ($order_goods_list as $item) {
            $goods_list[$item['order_id']][] = $item;
        }

        $url = Yf_Registry::get('url');
        $data['items'] = $this->dealOrderData($data['items']);

        foreach ($data['items'] as $key => $val) {

            $data['items'][$key]['order_stauts_text'] = $Order_StateModel->orderState[$val['order_status']];
            $data['items'][$key]['order_stauts_const'] = $Order_StateModel->orderState[$val['order_status']];

            //订单详情URL
            $data['items'][$key]['info_url'] = $url . '?ctl=Seller_Trade_Order&met=physicalInfo&o&typ=e&order_id=' . $val['order_id'];
            //发货单URL
            $data['items'][$key]['delivery_url'] = $url . '?ctl=Seller_Trade_Order&met=getOrderPrint&typ=e&order_id=' . $val['order_id'];
            //设置发货URL
            $data['items'][$key]['send_url'] = $url . '?ctl=Seller_Trade_Order&met=send&typ=e&order_id=' . $val['order_id'];
            //收货人信息 名字 + 联系方式 + 地址 &nbsp
            $data['items'][$key]['receiver_info'] = $val['order_receiver_name'] . "&nbsp" . $val['order_receiver_contact'] . "&nbsp" . $val['order_receiver_address'];

            //订单发票信息
            if($val['order_invoice_id'])
            {
                $data['items'][$key]['invoice'] = $Order_InvoiceModel->getOne($val['order_invoice_id']);
                $data['items'][$key]['invoice']['invoice_statu_txt'] = $Order_InvoiceModel->invoiceState[$data['items'][$key]['invoice']['invoice_state']];
            }


            //发货人信息(发货地址)
            $Shop_ShippingAddressModel = new Shop_ShippingAddressModel();
            $address_list              = $Shop_ShippingAddressModel->getByWhere(array('shop_id' => $val['shop_id']));
            $address_list              = array_values($address_list);
            //判断商家是否设置了发货地址
            if (empty($address_list)) {
                $data['items'][$key]['shipper'] = 0;
                $data['items'][$key]['shipper_info'] = '还未设置发货地址，请进入发货设置 &gt 地址库中添加';
            } else {
                //判断是否设置了默认收货地址，如果没有的话就取第一个收货地址
                $address_default              = $Shop_ShippingAddressModel->getByWhere(array('shop_id' => $val['shop_id'],'shipping_address_default'=>1));
                $address_default              = array_values($address_default);
                if($address_default)
                {
                    $data['items'][$key]['shipper'] = 1;
                    $data['items'][$key]['shipper_info'] = $address_default[0]['shipping_address_contact'] . "&nbsp" . $address_default[0]['shipping_address_phone']. "&nbsp" .$address_default[0]['shipping_address_area'] . "&nbsp" . $address_default[0]['shipping_address_address'];
                }
                else
                {
                    $data['items'][$key]['shipper'] = 1;
                    $data['items'][$key]['shipper_info'] = $address_list[0]['shipping_address_contact']. "&nbsp" .$address_list[0]['shipping_address_phone']. "&nbsp" .$address_list[0]['shipping_address_area']. "&nbsp" .$address_list[0]['shipping_address_address'];
                }

            }

            //运费信息
            if ($val['order_shipping_fee'] == 0) {
                $data['items'][$key]['shipping_info'] = "(免运费)";
            } else {
                $shipping_fee = @format_money($val['order_shipping_fee']);
                $data['items'][$key]['shipping_info'] = "(含运费$shipping_fee)";
            }

            /*
             * 订单操作
             * 待付款状态 ==> 取消订单
             * 待发货状态 ==> 设置发货
             * */
            
            if(Perm::$shopId){
                $shopBaseModel = new Shop_BaseModel();
                $shop_base  = $shopBaseModel->getOne(Perm::$shopId);
            }


            //获取订单产品列表
            //$goods_list                        = $Order_GoodsModel->getGoodsListByOrderId($val['order_id']);

            if(isset($goods_list[$val['order_id']]))
            {
                $data['items'][$key]['goods_list'] = $goods_list[$val['order_id']];

                $goods_cat_num = 0;
                foreach ($data['items'][$key]['goods_list'] as $k => $v) {
                    $data['items'][$key]['goods_list'][$k]['spec_text'] = $v['order_spec_info'] ? implode('，', $v['order_spec_info']) : '';
                    $data['items'][$key]['goods_list'][$k]['goods_link'] = $url . '?ctl=Goods_Goods&met=snapshot&goods_id=' . $v['goods_id'] . '&order_id=' . $val['order_id'];//商品链接
                    $goods_cat_num += 1;
                    if(is_array($data['items'][$key]['goods_list'][$k]['order_spec_info']) && $data['items'][$key]['goods_list'][$k]['order_spec_info']){
                        $data['items'][$key]['goods_list'][$k]['order_spec_info'] = implode('，',$data['items'][$key]['goods_list'][$k]['order_spec_info']);
                    }

                    //判断商品是否是一件代发分销商品，如果是一件代发分销商品，分销商无法发货
                    $deilve_able = 1;
                    if(in_array($v['common_id'],$dist_common_ids))
                    {
                        $deilve_able = 0;
                    }

                    $data['items'][$key]['goods_list'][$k]['order_goods_num_c'] = $v['order_goods_num'] - $v['order_goods_returnnum'];
                    //商品小计金额（打印发货单）
                    $data['items'][$key]['goods_list'][$k]['order_goods_amount_c'] = $v['goods_price']*$data['items'][$key]['goods_list'][$k]['order_goods_num_c'];

                    //查找该订单商品是否存在退款/退货
                    $goods_return       = $Order_ReturnModel->getByWhere(array(
                                                                             'order_goods_id' => $v['order_goods_id'],
                                                                             'return_type' => Order_ReturnModel::RETURN_TYPE_ORDER,
                                                                             'return_shop_handle:!=' => Order_ReturnModel::RETURN_SELLER_UNPASS,
                                                                         ));

                    $return_txt = '';
                    $return_price = 0;
                    if($goods_return)
                    {
                        $goods_return = current($goods_return);

                        if($goods_return['return_state'] == Order_ReturnModel::RETURN_PLAT_PASS)
                        {
                            $return_txt = "<span class='colred'>已退".$goods_return['order_goods_num']."件</span>";
                        }else{
                            $return_url = $url . '?ctl=Seller_Service_Return&met=orderReturn&act=detail&id=' . $goods_return['order_return_id'];

                            if($deilve_able)
                            {
                                $return_txt = "<a class=\"ncbtn ncbtn-mint mt10 ml0  bbc_seller_btns\" href=\"$return_url\"><i class=\"icon-truck\"></i>处理退款</a>";
                            }
                        }
                        $return_price = $goods_return['return_cash'];
                    }
                    $data['items'][$key]['goods_list'][$k]['return_txt'] = $return_txt;
                    $data['items'][$key]['goods_list'][$k]['return_price'] = $return_price;
                }
            }
            //商品种类数
            $data['items'][$key]['goods_cat_num'] = $goods_cat_num;
            $data['items'][$key]['deilve_able'] = $deilve_able;
            
            if ($val['order_status'] == Order_StateModel::ORDER_WAIT_PAY) {
                $order_id = $val['order_id'];
                $set_html = "<a href=\"javascript:void(0)\" data-order_id=$order_id dialog_id=\"seller_order_cancel_order\" class=\"ncbtn ncbtn-grapefruit mt5\"><i class=\"icon-remove-circle\"></i>取消订单</a>";

                $data['items'][$key]['set_html'] = $set_html;
            } elseif ($val['order_status'] == Order_StateModel::ORDER_WAIT_PREPARE_GOODS || $val['order_status'] == Order_StateModel::ORDER_PAYED) {
                //订单已经支付，判断已支付订单是否存在退款
                $return       = $Order_ReturnModel->getByWhere(array(
                                                                   'order_number' => $val['order_id'],
                                                                   'return_type' => Order_ReturnModel::RETURN_TYPE_ORDER,
                                                                   'return_state:!=' => Order_ReturnModel::RETURN_PLAT_PASS
                                                               ));

                if ($return) {
                    //查找退款单
                    $order_retuen_cond['order_number'] = $val['order_id'];
                    $order_retuen_cond['return_goods_return'] = Order_ReturnModel::RETURN_GOODS_ISRETURN;
                    $return_id = $Order_ReturnModel->getKeyByWhere($order_retuen_cond);
                    $return_id = $return_id['0'];

                    $data['items'][$key]['retund_url'] = $url . '?ctl=Seller_Service_Return&met=orderReturn&act=detail&id=' . $return_id;
                    $retund_url = $url . '?ctl=Seller_Service_Return&met=orderReturn&act=detail&id=' . $return_id;

                    $set_html = "<span class=\"ncbtn ncbtn-mint  colred fz14\"><i class=\"icon-truck\"></i>退款中</span>";

                } else {

                    if($data['items'][$key]['deilve_able'])
                    {
                        if(isset($data['items'][$key]['pintuan_status']) && $data['items'][$key]['pintuan_status'] != 1){
                            $set_html = '';
                        }else{
                            $send_url = $data['items'][$key]['send_url'];
                            $set_html = "<a class=\"ncbtn ncbtn-mint  bbc_seller_btns \" href=\"$send_url\"><i class=\"icon-truck\"></i>设置发货</a>";
                        }
                    }
                    else
                    {
                        $order_id = $val['order_id'];
                        if ($val['order_invoice_id']){
                            $set_html = "<a class=\"edit_invoice\" data-order_id=$order_id href=\"javascript:;\"><i class=\"icon-truck\"></i>发票详情</a>";
                        }else{
                            $set_html = "<a href=\"javascript:;\"><i class=\"icon-truck\"></i>不开发票</a>";
                        }
                    }
                }


                $data['items'][$key]['set_html'] = $set_html;
            } else {
                $data['items'][$key]['set_html'] = null;
            }

            //货到付款+待发货=> 可以取消订单
            if ( $val['payment_id'] == PaymentChannlModel::PAY_CONFIRM
                && $val['order_status'] == Order_StateModel::ORDER_WAIT_PREPARE_GOODS
            ) {
                $order_id = $val['order_id'];
                $set_html = "<a href=\"javascript:void(0)\" data-order_id=$order_id dialog_id=\"seller_order_cancel_order\" class=\"ncbtn ncbtn-grapefruit mt5\"><i class=\"icon-remove-circle\"></i>取消订单</a>";

                $data['items'][$key]['set_html'] .= $set_html;
            }


        }

        return $data;
    }

    /*
     *  windfnn
     *
     * 获取平台商品订单列表
     * @return array $data 订单列表
     * */
    public function getPlatOrderList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        $data = $this->getBaseList($cond_row, $order_row, $page, $rows);
        $Order_StateModel = new Order_StateModel();
        $data['totalSums'] = $this->sumMoney($cond_row , "order_payment_amount");

        foreach ($data['items'] as $key => $val) {
            $data['items'][$key]['order_stauts_text'] = $Order_StateModel->orderState[$val['order_status']];
            $data['items'][$key]['order_from_text'] = $Order_StateModel->orderFrom[$val['order_from']];
            $data['items'][$key]['evaluation_status_text'] = $Order_StateModel->evaluationStatus[$val['order_buyer_evaluation_status']];
            $data['totalSum']+=$val['order_payment_amount'];
        }

        return $data;
    }

    /*
     *  ly
     *
     * 拼接筛选条件,多个方法公用
     * @param $condition 筛选条件
     * @return array $condition 筛选条件
     * */
    public function createSearchCondi(&$condition)
    {
        $query_start_date = request_string('query_start_date');
        $query_end_date = request_string('query_end_date');
        $buyer_name = request_string('buyer_name');
        $order_sn = request_string('order_sn');
        $skip_off = request_int('skip_off');            //是否显示已取消订单
        $chain_name = request_string('chain_name'); //门店名称

        if (!empty($query_start_date)) {
            $query_start_date = $query_start_date;
            $condition['order_create_time:>='] = $query_start_date;
        }

        if (!empty($query_end_date)) {
            $query_end_date = $query_end_date;
            $condition['order_create_time:<='] = $query_end_date;
        }

        if (!empty($buyer_name)) {
            $condition['buyer_user_name:LIKE'] = "%$buyer_name%";
        }

        if (!empty($order_sn)) {
            $condition['order_id:LIKE'] = "%$order_sn%";
        }

        if ($skip_off) {
            $condition['order_status:<>'] = Order_StateModel::ORDER_CANCEL;
        }

        //门店名字
        if ($chain_name) {
            $chain_model = new Chain_BaseModel;
            $chain_rows = $chain_model->getByWhere(['chain_name:LIKE'=> '%'.$chain_name.'%']);
            $chain_ids = empty($chain_rows) ? [] : array_keys($chain_rows);
            $condition['chain_id:IN'] = $chain_ids;
        }
    }

    /*
     *  ly
     *
     * 获取实物交易订单信息
     * @param $condition 筛选条件
     * @return array $data 订单信息
     * */
    public function getPhysicalInfoData($condi = array())
    {

        $data = $this->getOrderList($condi);
        $data = pos($data['items']);
        switch ($data['order_status']) {
            case Order_StateModel::ORDER_WAIT_PAY:
                $order_create_time = time($data['order_create_time']);
                $order_close_data = date('Y-m-d', strtotime('+7days', $order_create_time));
                $data['order_status_text'] = '订单已经提交，等待买家付款';
                $data['order_status_html'] = "<li>1. 买家尚未对该订单进行支付。</li><li>2. 如果买家未对该笔订单进行支付操作，系统将于<time>$order_close_data</time>自动关闭该订单。</li>";

                //页面的订单状态
                $data['order_payed'] = "";
                $data['order_wait_confirm_goods'] = "";
                $data['order_received'] = "";
                $data['order_evaluate'] = "";
                break;

            case Order_StateModel::ORDER_PAYED:
                $payment_name = $data['payment_name'];
                if (empty($payment_name)) {
                    $payment_name = 'XXX';
                }
                $data['order_status_text'] = '已经付款';
                $data['order_status_html'] = "<li>1. 买家已使用“" . $payment_name . "”方式成功对订单进行支付，支付单号 “" .$data['payment_other_number']. "”。</li><li>2. 订单已提交商家进行备货发货准备。</li>";

                //页面的订单状态
                $data['order_payed'] = "current";
                $data['order_wait_confirm_goods'] = "";
                $data['order_received'] = "";
                $data['order_evaluate'] = "";
                break;

            case Order_StateModel::ORDER_WAIT_PREPARE_GOODS:
                $payment_name = $data['payment_name'];
                if (empty($payment_name)) {
                    $payment_name = 'XXX';
                }
                $data['order_status_text'] = '等待发货';
                $data['order_status_html'] = "<li>1. 买家已使用“" . $payment_name . "”方式成功对订单进行支付。</li><li>2. 订单已提交商家进行备货发货准备。</li>";

                //页面的订单状态
                $data['order_payed'] = "current";
                $data['order_wait_confirm_goods'] = "";
                $data['order_received'] = "";
                $data['order_evaluate'] = "";
                break;

            case Order_StateModel::ORDER_WAIT_CONFIRM_GOODS:
                $data['order_status_text'] = '已经发货';
                if (empty($data['order_receiver_date'])) {
                    $order_shipping_time = strtotime($data['order_shipping_time']);
                    $order_shipping_time = strtotime('+1 month', $order_shipping_time);
                    $order_shipping_time = date('Y-m-d', $order_shipping_time);
                    $data['order_receiver_date'] = $order_shipping_time;
                } else {
                    $order_shipping_time = $data['order_receiver_date'];
                }

                if(!empty($data['order_shipping_express_id']) && !empty($data['order_shipping_code']))
                {
                    //查找物流公司
                    $expressModel = new ExpressModel();
                    $express_base = $expressModel->getExpress($data['order_shipping_express_id']);
                    $express_base = pos($express_base);
                    $express_name = $express_base['express_name'];
                    $order_shipping_code = $data['order_shipping_code'];

                    $data['order_status_html'] = "<li>1. 商品已发出；$express_name : $order_shipping_code 。</li><li>2. 如果买家没有及时进行收货，系统将于<time>$order_shipping_time</time>自动完成“确认收货”，完成交易。</li>";
                }
                else
                {
                    $data['order_status_html'] = "<li>1. 商品已发出；无需物流。</li><li>2. 如果买家没有及时进行收货，系统将于<time>$order_shipping_time</time>自动完成“确认收货”，完成交易。</li>";
                }

                //页面的订单状态
                $data['order_payed'] = "current";
                $data['order_wait_confirm_goods'] = "current";
                $data['order_received'] = "";
                $data['order_evaluate'] = "";
                break;

            case $data['order_status'] == Order_StateModel::ORDER_RECEIVED || $data['order_status'] == Order_StateModel::ORDER_FINISH:
                $data['order_status_text'] = '已经收货';
                $data['order_status_html'] = '<li>1. 交易已完成，买家可以对购买的商品及服务进行评价。</li><li>2. 评价后的情况会在商品详细页面中显示，以供其它会员在购买时参考。</li>';

                //页面的订单状态
                $data['order_payed'] = "current";
                $data['order_wait_confirm_goods'] = "current";
                $data['order_received'] = "current";
                if ($data['order_buyer_evaluation_status'] != Order_BaseModel::BUYER_EVALUATE_NO) {
                    $data['order_evaluate'] = "current";
                } else {
                    $data['order_evaluate'] = "";
                }
                break;

            case Order_StateModel::ORDER_CANCEL:
                $data['order_status_text'] = '交易关闭';
                $order_cancel_date = $data['order_cancel_date'];
                $order_cancel_reason = $data['order_cancel_reason'];

                //判断关闭者身份 1=>买家 2=>卖家 3=>系统
                if ($data['order_cancel_identity'] == Order_BaseModel::CANCEL_USER_BUYER) {
                    $identity = '买家';
                } else if ($data['order_cancel_identity'] == Order_BaseModel::CANCEL_USER_SELLER) {
                    $identity = '商家';
                } else if ($data['order_cancel_identity'] == Order_BaseModel::CANCEL_USER_SYSTEM) {
                    $identity = '系统';
                }
                if($order_cancel_date == '0000-00-00 00:00:00'){
                    $data['order_status_html'] = "<li> $identity 取消了订单 ( $order_cancel_reason ) </li>";
                }else{
                    $data['order_status_html'] = "<li> $identity 于 $order_cancel_date 取消了订单 ( $order_cancel_reason ) </li>";
                }
                break;
        }

        //取出物流公司名称
        if (!empty($data['order_shipping_express_id'])) {
            $expressModel = new ExpressModel();
            $express_base = $expressModel->getExpress($data['order_shipping_express_id']);
            $express_base = pos($express_base);
            $data['express_name'] = $express_base['express_name'];
        } else {
            $data['express_name'] = '';
        }

        //店主名称
        $shopBaseModel = new Shop_BaseModel();
        $shop_base = $shopBaseModel->getBase($data['shop_id']);
        $shop_base = pos($shop_base);
        $data['shop_user_name'] = $shop_base['user_name'];
        $data['shop_tel'] = $shop_base['shop_tel'];

        return $data;
    }

    /*
  *  zcg
  *
  * 获取门店自提订单信息
  * @param $condition 筛选条件
  * @return array $data 订单信息
  * */
    public function getChainInfoData($condi = array())
    {

        $data = $this->getOrderList($condi);
        $data = pos($data['items']);

        switch ($data['order_status'])
        {
            case Order_StateModel::ORDER_WAIT_PAY :
                $order_create_time         = time($data['order_create_time']);
                $order_close_data          = date('Y-m-d', strtotime('+7days', $order_create_time));
                $data['order_status_text'] = '订单已经提交，等待买家付款';
                $data['order_status_html'] = "<li>1. 买家尚未对该订单进行支付。</li><li>2. 如果买家未对该笔订单进行支付操作，系统将于<time>$order_close_data</time>自动关闭该订单。</li>";

                //页面的订单状态
                $data['order_payed']              = "";
                $data['order_wait_confirm_goods'] = "";
                $data['order_received']           = "";
                $data['order_evaluate']           = "";
                break;

            case Order_StateModel::ORDER_SELF_PICKUP :
                $payment_name = $data['payment_name'];
                if (empty($payment_name))
                {
                    $payment_name = 'XXX';
                }
                $data['order_status_text'] = '待自提';
                $data['order_status_html'] = "<li>买家还没有到门店自提。</li>";

                //页面的订单状态
                $data['order_payed']              = "current";
                $data['order_wait_confirm_goods'] = "";
                $data['order_received']           = "";
                $data['order_evaluate']           = "";
                break;

            case Order_StateModel::ORDER_RECEIVED:
                $data['order_status_text'] = '已经自提';
                $data['order_status_html'] = '<li>1. 交易已完成，买家可以对购买的商品及服务进行评价。</li><li>2. 评价后的情况会在商品详细页面中显示，以供其它会员在购买时参考。</li>';

                //页面的订单状态
                $data['order_payed']              = "current";
                $data['order_wait_confirm_goods'] = "current";
                $data['order_received']           = "current";
                if ($data['order_buyer_evaluation_status'] != Order_BaseModel::BUYER_EVALUATE_NO)
                {
                    $data['order_evaluate'] = "current";
                }
                else
                {
                    $data['order_evaluate'] = "";
                }

                break;
            case Order_StateModel::ORDER_FINISH :
                $data['order_status_text'] = '已经自提';
                $data['order_status_html'] = '<li>1. 交易已完成，买家可以对购买的商品及服务进行评价。</li><li>2. 评价后的情况会在商品详细页面中显示，以供其它会员在购买时参考。</li>';

                //页面的订单状态
                $data['order_payed']              = "current";
                $data['order_wait_confirm_goods'] = "current";
                $data['order_received']           = "current";
                if ($data['order_buyer_evaluation_status'] != Order_BaseModel::BUYER_EVALUATE_NO)
                {
                    $data['order_evaluate'] = "current";
                }
                else
                {
                    $data['order_evaluate'] = "";
                }

                break;

            case Order_StateModel::ORDER_CANCEL:
                $data['order_status_text'] = '交易关闭';
                $order_cancel_date         = $data['order_cancel_date'];
                $order_cancel_reason       = $data['order_cancel_reason'];

                //判断关闭者身份 1=>买家 2=>卖家 3=>系统
                if ($data['order_cancel_identity'] == Order_BaseModel::CANCEL_USER_BUYER)
                {
                    $identity = '买家';
                }
                else if ($data['order_cancel_identity'] == Order_BaseModel::CANCEL_USER_SELLER)
                {
                    $identity = '商家';
                }
                else if ($data['order_cancel_identity'] == Order_BaseModel::CANCEL_USER_SYSTEM)
                {
                    $identity = '系统';
                }

                $data['order_status_html'] = "<li> $identity 于 $order_cancel_date 取消了订单 ( $order_cancel_reason ) </li>";
                break;
        }

        //店主名称
        $shopBaseModel          = new Shop_BaseModel();
        $shop_base              = $shopBaseModel->getBase($data['shop_id']);
        $shop_base              = pos($shop_base);
        $data['shop_user_name'] = $shop_base['user_name'];
        $data['shop_tel']       = $shop_base['shop_tel'];

        return $data;
    }

    /*
     *  ly
     *
     * 拼接筛选条件,多个方法公用
     * @param $condition 筛选条件
     * @return array $data 订单信息 + 筛选条件
     * */
    public function getPhysicalList(&$condi,$order_row=array())
    {
        $condi['shop_id'] = Perm::$shopId;
        if(!isset($condi['order_is_virtual']) || !$condi['order_is_virtual']){
            $condi['order_is_virtual'] = 0;
        }
        if(!isset($condi['order_shop_hidden']) || !$condi['order_shop_hidden']){
            $condi['order_shop_hidden'] = 0;
        }
        $condi['order_payment_amount-order_refund_amount:>='] = '0';
        $this->createSearchCondi($condi); //生成查询条件

        $data = $this->getOrderList($condi,$order_row);
        $data['condi'] = $condi;

        //如果是门店自提，取出门店信息
        //获取门店信息
        if(isset($condi['chain_id:!=']) && $data['totalsize'] > 0) {
            $chain_base = new Chain_BaseModel;
            $chain_ids = array_unique(array_column($data['items'], 'chain_id'));
            $chain_rows = $chain_base->getBase($chain_ids);
            $data['chain_rows'] = $chain_rows;
        }

        return $data;
    }

    /**
     * 读数量
     *
     * @param  int $config_key 主键值
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getCount($cond_row = array())
    {
        return $this->getNum($cond_row);
    }


    //订单支付成功后修改订单状态
    public function editOrderStatusAferPay($order_id = null, $uorder_id = null)
    {
        $flag = false;
        //查找订单信息
        $order_base = $this->getOne($order_id);
        if($order_base['order_status'] == Order_StateModel::ORDER_WAIT_PAY||$order_base['order_status'] == Order_StateModel::ORDER_PRESALE_DEPOSIT)
        {
            if($order_base['is_presale']==1&&$order_base['order_status']==Order_StateModel::ORDER_WAIT_PAY){
                $edit_row = array('order_status' => Order_StateModel::ORDER_PRESALE_DEPOSIT);
            }else{
                $edit_row = array('order_status' => Order_StateModel::ORDER_PAYED);
            }
            $edit_row['payment_time'] = get_date_time();
            $edit_row['payment_other_number'] = $uorder_id;
            //修改订单状态
            $this->editBase($order_id, $edit_row);

            //修改订单商品状态
            $Order_GoodsModel = new Order_GoodsModel();
            if($order_base['is_presale']==1&&$order_base['order_status']==Order_StateModel::ORDER_WAIT_PAY){
                $edit_goods_row = array('order_goods_status' => Order_StateModel::ORDER_PRESALE_DEPOSIT);
            }else{
                $edit_goods_row = array('order_goods_status' => Order_StateModel::ORDER_PAYED);
            }
            $order_goods_id = $Order_GoodsModel->getKeyByWhere(array('order_id' => $order_id));
            $flag = $Order_GoodsModel->editGoods($order_goods_id, $edit_goods_row);
            //判断是否为分销礼包订单，修改用户身份
            $User_InfoModel=new User_InfoModel();
            $DistributionShop= new Distribution_DistributionShop();
            $order_list=$Order_GoodsModel->getByWhere(array('order_id'=>$order_id));
             foreach ($order_list as $key => $value) {
                 if($value['identity_type']==1){
                     $flag1=$User_InfoModel->editInfo($order_base['buyer_user_id'],array('distributor_type'=>1));
                     if($flag1){
                         $time=time();
                         $images=Yf_Registry::get('shop_api_url').'shop/static/default/images/Bitmap.png';
                         $DistributionShop->addBase(array('user_id'=>$order_base['buyer_user_id'],'distribution_name'=>$order_base['buyer_user_name']."的小店",'distribution_logo'=>Yf_Registry::get('shop_api_url').'shop/static/default/images/Bitmap.png','add_time'=>time()));

                     }
                 }
             }
            //编辑礼包商品佣金
            $gift_row['identity_type'] = 1;
            $gift_row['buyer_user_id'] = $order_base['buyer_user_id'];
            $gift_row['order_goods_status:<'] = 6;
            $gift_order['order_goods_status'] = 'DESC';
            $gift_info = $Order_GoodsModel->getByWhere($gift_row, $gift_order);

            if ($gift_info) {
                foreach ($gift_info as $k => $v) {
                    if ($v['order_id'] != $order_base['order_id']) {
                        $edit_gift['directseller_commission_0'] = 0;
                        $edit_gift['directseller_commission_1'] = 0;
                        $Order_GoodsModel->editGoods($v['order_goods_id'], $edit_gift);
                    }
                }
            }

            //修改商品的销量
            $Goods_BaseModel = new Goods_BaseModel();
            $Goods_BaseModel->editGoodsSale($order_goods_id);

            $Goods_CommonModel = new Goods_CommonModel();

            //判断是否是虚拟订单，若是虚拟订单则生成发送信息并修改订单状态为已发货
            if ($order_base['order_is_virtual'] == Order_BaseModel::ORDER_IS_VIRTUAL) {
                //循环订单商品
                $Text_Password = new Text_Password();
                $Order_GoodsVirtualCodeModel = new Order_GoodsVirtualCodeModel();


                $goods_name_str = '';
                $virtual_code_str = '';
                foreach ($order_goods_id as $k => $v) {
                    $order_goods_base = $Order_GoodsModel->getOne($v);
                    //判断该商品是否是虚拟商品
                    $goods_common_base = $Goods_CommonModel->getOne($order_goods_base['common_id']);
                    if($goods_common_base['common_is_virtual'])
                    {
                        $num = $order_goods_base['order_goods_num'];
                        //获取购买的数量，循环生成虚拟兑换码，将信息插入虚拟兑换码表中

                        for ($i = 0; $i < $num; $i++) {
                            $virtual_code = $Text_Password->create(8, 'unpronounceable', 'numeric');
                            $virtual_code_str .= $virtual_code . '，';
                            $add_row = array();
                            $add_row['virtual_code_id'] = $virtual_code;
                            $add_row['order_id'] = $order_id;
                            $add_row['order_goods_id'] = $v;
                            $add_row['virtual_code_status'] = Order_GoodsVirtualCodeModel::VIRTUAL_CODE_NEW;

                            $Order_GoodsVirtualCodeModel->addCode($add_row);
                        }

                        if($goods_name_str) {
                            $goods_name_str .= $order_goods_base['goods_name'] . '，';
                        }
                        else {
                            $goods_name_str = $order_goods_base['goods_name'];
                        }

                        //修改订单商品为已发货待收货4
                        $edit_order_goods_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
                        $Order_GoodsModel->editGoods($v, $edit_order_goods_row);
                    }
                }

                $message = new MessageModel();
                $message->sendMessage('virtual pick up code', Perm::$userId, Perm::$row['user_account'], NULL, $order_base['shop_name'], 1, MessageModel::ORDER_MESSAGE,  NUll,NULL,NULL,NULL, Null,$goods_name_str, NULL,NULL,$virtual_code_str,NULL,$order_base['order_receiver_contact']);
                //$str = Sms::send(13918675918,"尊敬的用户您已在【变量】成功购买【变量】，您可凭兑换码【变量】在本店进行消费。");

                $o_goods_id = $Order_GoodsModel->getKeyByWhere(array('order_id' => $order_id,'order_goods_amount:>'=>0));
                $goods_common_id = array_column($Order_GoodsModel->get($o_goods_id),'common_id');
                $Goods_Common = new Goods_CommonModel();
                $goods_common = current($Goods_Common->get($goods_common_id));
                //修改订单状态为已发货等待收货4
                $edit_order_row['order_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
                $edit_order_row['order_shipping_time'] = get_date_time();
                $edit_order_row['order_receiver_date'] = $goods_common['common_virtual_date'];
                $this->editBase($order_id, $edit_order_row);
            }

            //判断是否是门店自提订单，若是门店自提订单则生成发送信息并修改订单状态为待自提
            if ($order_base['chain_id']) {
                $code     = VerifyCode::getCode($order_base['order_receiver_contact']);

                $Chain_BaseModel=new Chain_BaseModel();
                $chain_base = current($Chain_BaseModel->getByWhere(array('chain_id'=>$order_base['chain_id'])));

                $order_goods_base = $Order_GoodsModel->getOne($order_goods_id[0]);

                $Order_GoodsChainCodeModel = new Order_GoodsChainCodeModel();
                $code_data['order_id']=$order_id;
                $code_data['chain_id']=$order_base['chain_id'];
                $code_data['order_goods_id']=$order_goods_id[0];
                $code_data['chain_code_id']=$code;
                $Order_GoodsChainCodeModel->addGoodsChainCode($code_data);

                //修改订单状态为待自提11
                $edit_order_goods_row['order_goods_status'] = Order_StateModel::ORDER_SELF_PICKUP;
                $Order_GoodsModel->editGoods($order_goods_id, $edit_order_goods_row);

                $edit_order_row['order_status'] = Order_StateModel::ORDER_SELF_PICKUP;
                $this->editBase($order_id, $edit_order_row);

                $message = new MessageModel();
                $message->sendMessage('Self pick up code', Perm::$userId, Perm::$row['user_account'], $order_id = NULL, $shop_name = $order_base['shop_name'], 1, MessageModel::ORDER_MESSAGE,  NUll,NULL,NULL,NULL, Null,$goods_name=$order_goods_base['goods_name'], NULL,NULL,$ztm=$code,$chain_name=$chain_base['chain_name'],$order_phone=$order_base['order_receiver_contact']);
                //$str = Sms::send(13918675918,"尊敬的用户您已在[shop_name]成功购买[goods_name]，您可凭自提码[ztm]在[chain_name]自提。");
            }


            //判断此订单是否使用了代金券，如果使用，则改变代金券的使用状态
            /*if ($order_base['voucher_id']) {
                $Voucher_BaseModel = new Voucher_BaseModel();
                $Voucher_BaseModel->changeVoucherState($order_base['voucher_id'], $order_base['order_id']);

                //代金券使用提醒
                $message = new MessageModel();
                $message->sendMessage('The use of vouchers to remind', Perm::$userId, Perm::$row['user_account'], $order_id = NULL, $shop_name = NULL, 0, MessageModel::USER_MESSAGE);
            }*/
        }
        return $flag;
    }

    //取消订单
    public function cancelOrder($order_id)
    {
        $condition['order_status'] = Order_StateModel::ORDER_CANCEL;
        $condition['order_cancel_reason'] = '支付超时自动取消';
        $condition['order_cancel_identity'] = Order_BaseModel::IS_ADMIN_CANCEL;
        $condition['order_cancel_date'] = get_date_time();

        $this->editBase($order_id, $condition);
        $order_base=current($this->getByWhere(array('order_id'=>$order_id)));

        //修改订单商品表中的订单状态
        $Order_GoodsModel = new Order_GoodsModel();
        $edit_row['order_goods_status'] = Order_StateModel::ORDER_CANCEL;
        $order_goods_id = $Order_GoodsModel->getKeyByWhere(array('order_id' => $order_id));

        $Order_GoodsModel->editGoods($order_goods_id, $edit_row);

        //退还订单商品的库存
        if($order_base['chain_id']!=0){
            $Chain_GoodsModel = new Chain_GoodsModel();
            $chain_row['chain_id:='] = $order_base['chain_id'];
            $chain_row['goods_id:='] = is_array($order_goods_id)?$order_goods_id[0]:$order_goods_id;
            $chain_row['shop_id:='] = $order_base['shop_id'];
            $chain_goods = current($Chain_GoodsModel->getByWhere($chain_row));
            $chain_goods_id = $chain_goods['chain_goods_id'];
            $goods_stock['goods_stock'] = $chain_goods['goods_stock'] + 1;
            $Chain_GoodsModel->editGoods($chain_goods_id, $goods_stock);
        }else{
            $Goods_BaseModel = new Goods_BaseModel();
            $Goods_BaseModel->returnGoodsStock($order_goods_id);
        }

        //远程关闭paycenter中的订单状态
        $key = Yf_Registry::get('paycenter_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $paycenter_app_id = Yf_Registry::get('paycenter_app_id');
        $formvars = array();

        $formvars['order_id'] = $order_id;
        $formvars['app_id'] = $paycenter_app_id;

        fb($formvars);

        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=cancelOrder&typ=json', $url), $formvars);
        fb($rs);
    }

    //确认收货
    public function confirmOrder($order_id)
    {
        $order_base = $this->getOne($order_id);
        $order_payment_amount = $order_base['order_payment_amount'];

        $condition['order_status'] = Order_StateModel::ORDER_FINISH;

        $condition['order_finished_time'] = get_date_time();

        $flag = $this->editBase($order_id, $condition);

        //修改订单商品表中的订单状态
        $edit_row['order_goods_status'] = Order_StateModel::ORDER_FINISH;

        $Order_GoodsModel = new Order_GoodsModel();

        $order_goods_id = $Order_GoodsModel->getKeyByWhere(array('order_id' => $order_id));

        $Order_GoodsModel->editGoods($order_goods_id, $edit_row);


        //远程修改paycenter中的订单状态
        $key      = Yf_Registry::get('shop_api_key');
        $url         = Yf_Registry::get('paycenter_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();

        $formvars['order_id']    = $order_id;
        $formvars['app_id']        = $shop_app_id;

        fb($formvars);

        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=confirmOrder&typ=json', $url), $formvars);


        /*
        *  经验与成长值
        */
        $user_points = Web_ConfigModel::value("points_recharge");//订单每多少获取多少积分
        $user_points_amount = Web_ConfigModel::value("points_order");//订单每多少获取多少积分

        if ($order_payment_amount / $user_points < $user_points_amount) {
            $user_points = floor($order_payment_amount / $user_points);
        } else {
            $user_points = $user_points_amount;
        }

        $user_grade = Web_ConfigModel::value("grade_recharge");//订单每多少获取多少积分
        $user_grade_amount = Web_ConfigModel::value("grade_order");//订单每多少获取多少成长值

        if ($order_payment_amount / $user_grade < $user_grade_amount)
        {
            $user_grade = floor($order_payment_amount / $user_grade);
        }
        else
        {
            $user_grade = $user_grade_amount;
        }

        $User_ResourceModel = new User_ResourceModel();
        //获取积分经验值
        $ce = $User_ResourceModel->getResource($order_base['buyer_user_id']);

        $resource_row['user_points'] = $ce[$order_base['buyer_user_id']]['user_points'] * 1 + $user_points * 1;
        $resource_row['user_growth'] = $ce[$order_base['buyer_user_id']]['user_growth'] * 1 + $user_grade * 1;

        $res_flag = $User_ResourceModel->editResource($order_base['buyer_user_id'], $resource_row);

        $User_GradeModel = new User_GradeModel;
        //升级判断
        $res_flag = $User_GradeModel->upGrade($order_base['buyer_user_id'], $resource_row['user_growth']);
        //积分
        $points_row['user_id'] = $order_base['buyer_user_id'];
        $points_row['user_name'] = $order_base['buyer_user_name'];
        $points_row['class_id'] = Points_LogModel::ONBUY;
        $points_row['points_log_points'] = $user_points;
        $points_row['points_log_time'] = get_date_time();
        $points_row['points_log_desc'] = '确认收货';
        $points_row['points_log_flag'] = 'confirmorder';

        $Points_LogModel = new Points_LogModel();

        $Points_LogModel->addLog($points_row);

        //成长值
        $grade_row['user_id'] = $order_base['buyer_user_id'];
        $grade_row['user_name'] = $order_base['buyer_user_name'];
        $grade_row['class_id'] = Grade_LogModel::ONBUY;
        $grade_row['grade_log_grade'] = $user_grade;
        $grade_row['grade_log_time'] = get_date_time();
        $grade_row['grade_log_desc'] = '确认收货';
        $grade_row['grade_log_flag'] = 'confirmorder';

        $Grade_LogModel = new Grade_LogModel;
        $Grade_LogModel->addLog($grade_row);
    }
    
    //获取推广订单数目
    function getPromotionOrderNum($cond_row)
    {
        return $this->getNum($cond_row);
    }

    //虚拟订单过期退货
    public function virtualReturn($order_id)
    {
        $Order_GoodsModel = new Order_GoodsModel();
        $Order_GoodsModel->sql->setWhere('order_id', $order_id);
        $goods_common_id = array_column($Order_GoodsModel->get('*'),'common_id');
        $Goods_Common = new Goods_CommonModel();
        $goods_common = current($Goods_Common->get($goods_common_id));
        if($goods_common['common_virtual_refund']){
            $Order_StateModel = new Order_StateModel();
            $flag2            = true;
            $Number_SeqModel  = new Number_SeqModel();
            $prefix           = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));
            $return_number    = $Number_SeqModel->createSeq($prefix);
            $return_id        = sprintf('%s-%s-%s-%s', 'TD', Perm::$userId, 0, $return_number);

            $field['return_message']       = __('虚拟商品过期自动退款');
            $field['return_code']          = $return_id;
            $field['return_reason_id']     = 0;
            $field['return_reason']        = "";
            $field['order_number']         = $order_id;
            $this->orderBaseModel         = new Order_BaseModel();
            $order                         = $this->orderBaseModel->getOne($order_id);
            $field['return_type']          = Order_ReturnModel::RETURN_TYPE_VIRTUAL;
            $field['return_goods_return']  = 0;
            $field['return_cash']          = $order['order_payment_amount'];
            $field['order_amount']         = $order['order_payment_amount'];
            $field['seller_user_id']       = $order['shop_id'];
            $field['seller_user_account']  = $order['shop_name'];
            $field['buyer_user_id']        = $order['buyer_user_id'];
            $field['buyer_user_account']   = $order['buyer_user_name'];
            $field['return_add_time']      = get_date_time();
            $field['return_commision_fee'] = $order['order_commission_fee'];
            $field['return_state']         = Order_ReturnModel::RETURN_PLAT_PASS;
            $field['return_finish_time']   = get_date_time();
            $field['order_is_virtual']     = $order['order_is_virtual'];
            $Order_GoodsModel->sql->setWhere('order_id', $order_id);
            $order_goods                   = current($Order_GoodsModel->get('*'));
            $field['order_goods_id']       = $order_goods['order_goods_id'];
            $field['order_goods_name']     = $order_goods['goods_name'];
            $field['order_goods_price']    = $order_goods['goods_price'];
            $field['order_goods_num']      = $order_goods['order_goods_num'];
            $field['order_goods_pic']      = $order_goods['goods_image'];

            $rs_row = array();
            $this->orderReturnModel = new Order_ReturnModel();
            $this->orderReturnModel->sql->startTransactionDb();

            $add_flag = $this->orderReturnModel->addReturn($field, true);
            check_rs($add_flag, $rs_row);

            $order_field['order_refund_status'] = Order_BaseModel::REFUND_IN;
            $order_field['order_refund_status'] = Order_BaseModel::REFUND_COM;
            $edit_flag                          = $this->orderBaseModel->editBase($order_id, $order_field);
            check_rs($edit_flag, $rs_row);

            $sum_data['order_refund_amount']         = $order['order_payment_amount'];
            $sum_data['order_commission_return_fee'] = $order['order_commission_fee'];
            $edit_flag                               = $this->orderBaseModel->editBase($order_id, $sum_data, true);
            check_rs($edit_flag, $rs_row);

            $order_goodsinfo['goods_return_status']      = Order_StateModel::ORDER_REFUND_END;
            $edit_flag                               = $Order_GoodsModel->editGoods($order_goods['order_goods_id'], $order_goodsinfo, true);
            check_rs($edit_flag, $rs_row);

            $key      = Yf_Registry::get('shop_api_key');
            $url         = Yf_Registry::get('paycenter_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');

            $formvars             = array();
            $formvars['app_id']        = $shop_app_id;
            $formvars['user_id']  = $order['buyer_user_id'];
            $formvars['user_account'] = $order['buyer_user_name'];
            $formvars['seller_id'] = $order['seller_user_id'];
            $formvars['seller_account'] = $order['seller_user_name'];
            $formvars['amount']   = $order['order_payment_amount'];
            $formvars['order_id'] = $order_id;
            //$formvars['goods_id'] = $return['order_goods_id'];
            $formvars['uorder_id'] = $order['payment_number'];


            $rs                   = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=refundTransfer&typ=json', $url), $formvars);

            if ($rs['status'] == 200)
            {
                check_rs(true, $rs_row);
            }
            else
            {
                check_rs(false, $rs_row);
            }

            $flag = is_ok($rs_row);
            if ($flag && $this->orderReturnModel->sql->commitDb())
            {
                return true;
            }
            else
            {
                $this->orderReturnModel->sql->rollBackDb();
                return false;
            }
        }

    }


    public function getSubQuantity($cond_row)
    {
        return $this->getNum($cond_row);
    }

    /**
     * 按照订单编号或者订单商品名称搜索
     * @param $search_str string
     * @param $condition array 其他搜索条件
     * @return array $order_ids
     */
    public function searchNumOrGoodsName($search_str, $condition = [])
    {
        $orderGoodsModel = new Order_GoodsModel();
        //$order_rows = $this->getByWhere($condition);
        //$order_ids = array_keys($order_rows);
        $search_str = $search_str;

        $order_row ['order_id:LIKE'] = $search_str;
        //订单号搜索
        $num_order_rows = $this->getByWhere($order_row);
        $num_order_ids = array_keys($num_order_rows);
        unset($order_row['order_id:LIKE']);
        $order_row['goods_name:LIKE'] = '%' . $search_str . '%';

        //订单商品名称搜索
        $goods_rows = $orderGoodsModel->getByWhere($order_row);
        $g_order_ids = array_column($goods_rows, 'order_id');

        $order_ids = array_unique(array_merge($num_order_ids, $g_order_ids));

        return $order_ids;
    }
    
    /**
     * 处理订单信息，添加或修改必须字段
     * @param type $data
     * @return type
     */
    private function dealOrderData($data){
        if(!is_array($data) || !$data){
            return array();
        }
        $order_id = array();
        foreach ($data as $value){
            $order_id[] = $value['order_id'];
        }
        $pt_temp_model  = new PinTuan_Temp();
        $pt_temp = $pt_temp_model->getByWhere(array('order_id:IN'=>$order_id));
        $order_pt = array();
        foreach ($pt_temp as $temp)
        {
            $order_pt[$temp['order_id']] = array('mark_id'=>$temp['mark_id'],'type'=>$temp['type']);
        }

        foreach ($data as $k => $val){
            if(isset($order_pt[$val['order_id']])){
                if($order_pt[$val['order_id']]['type'] == 0){
                    //拼团
                    $data[$k]['is_pintuan'] = 1;
                    $mark_id = $order_pt[$val['order_id']]['mark_id'];
                    $mark_model = new PinTuan_Mark();
                    $mark_info = $mark_model->getOne($mark_id);
                    $data[$k]['pintuan_status'] = $mark_info['status'];
                }else{
                    //单独购买
                    $data[$k]['is_pintuan'] = 2;
                    $data[$k]['pintuan_status'] = 1;
                }
                
                $data[$k]['order_shop_benefit'] = 0;

            }else{
                $data[$k]['is_pintuan'] = 0;
            }
        }
        return $data;
    }

    public function autoRefund($order_id)
    {
        //查找订单信息
        $order_base = $this->getOne($order_id);
        //判断订单状态是否是已付款订单。非已支付订单（订单状态为2）报错
        if($order_base['order_status'] != Order_StateModel::ORDER_PAYED)
        {
            $msg="该订单非已支付订单";
            return false;
        }
        //查询订单是否已经退款
        $order_return_model = new Order_ReturnModel();
        $order_return = $order_return_model->getByWhere(array('order_number'=>$order_id));
        if($order_return)
        {
            return false;
        }

        //查找订单商品信息
        $Order_GoodsModel = new Order_GoodsModel();
        $order_goods_row = $Order_GoodsModel->getByWhere(array('order_id' => $order_id));
        //循环订单将订单中的所有商品都进行退款申请
        foreach($order_goods_row as $key => $order_good)
        {
            $rs_row = array();
            //开启事物
            $this->sql->startTransactionDb();

            /* 1.增加退款单信息 */
            $Number_SeqModel  = new Number_SeqModel();
            $prefix           = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));
            $return_number    = $Number_SeqModel->createSeq($prefix);
            $return_id        = sprintf('%s-%s-%s-%s', 'TD', $order_base['buyer_user_id'], $order_base['seller_user_id'], $return_number);

            $Order_ReturnReasonModel = new Order_ReturnReasonModel();
            $Order_ReturnModel = new Order_ReturnModel();
            //定义退款单信息
            $field = array();
            $field['return_message']   = "拼团失败退款";    //“退款/退货”说明
            $field['return_reason_id']   = 6;  //“退款/退货”原因
            $field['return_code']      = $return_id;                             //退货单号
            $field['order_goods_num']   = $order_good['order_goods_num'];                          //“退款/退货”数量
            $reason                    = $Order_ReturnReasonModel->getOne(3);
            $field['return_reason']    = $reason['order_return_reason_content'];   //“退款/退货”原因

            $field['order_number']      = $order_good['order_id'];            //订单号
            $field['order_goods_id']    = $order_good['order_goods_id'];                      //订单商品id
            $field['order_goods_name']  = $order_good['goods_name'];         //退货商品名称
            $field['order_goods_price'] = $order_good['goods_price'];        //商品单价
            $field['order_goods_pic']   = $order_good['goods_image'];        //商品图片

            $field['order_amount']        = $order_base['order_payment_amount'];     //订单实际支付金额
            $field['seller_user_id']      = $order_base['shop_id'];               //店铺id
            $field['seller_user_account'] = $order_base['shop_name'];            //店铺名称
            $field['buyer_user_id']       = $order_base['buyer_user_id'];        //买家id
            $field['buyer_user_account']  = $order_base['buyer_user_name'];     //买家名称
            $field['return_add_time']     = get_date_time();                 //退款、退货申请提交时间
            $field['order_is_virtual']    = $order_base['order_is_virtual'];     //该笔订单是否为虚拟订单

            $field['return_type'] = Order_ReturnModel::RETURN_TYPE_ORDER ; //退款
            $field['return_goods_return'] = 0;      //是否需要退货  0-不需要  1-需要
            $field['return_commision_fee'] = $order_good['order_goods_commission'];
            $field['return_rpt_cash'] = $order_base['order_rpt_price'] - $order_base['order_rpt_return'];
            $field['return_cash'] = $order_base['order_payment_amount'];

            $field['return_platform_message'] = '拼团失败自动退款';
            $field['return_state']            = Order_ReturnModel::RETURN_PLAT_PASS;
            $field['return_finish_time']      = get_date_time();
            
            $add_flag = $Order_ReturnModel->addReturn($field, true);
            check_rs($add_flag,$rs_row);

            //订单商品表中插入订单商品的“退款/退货”状态
            //退款
            $goods_field['goods_return_status'] = Order_GoodsModel::REFUND_COM;
            $goods_field['order_goods_returnnum'] = $order_good['order_goods_num'];
            $goods_data['order_goods_status'] = Order_StateModel::ORDER_FINISH; //将订单商品状态改正完成
            $edit_flag                          = $Order_GoodsModel->editGoods($order_good['order_goods_id'], $goods_field);

            check_rs($edit_flag,$rs_row);

            //订单状态修改为完成
            $order_edit_row = array();
            $order_edit_row['order_status'] = Order_StateModel::ORDER_FINISH;
            $order_edit_row['order_finished_time'] = date('Y-m-d H:i:s');
            $order_edit_row['order_refund_status'] = 2; //退款完成
            $edit_flag2  = $this->editBase($field['order_number'], $order_edit_row);

            check_rs($edit_flag2,$rs_row);

            $shopBase = new Shop_BaseModel();
            $shop_detail = $shopBase->getOne($field['seller_user_id']);
            $order_return_data = $Order_ReturnModel->getOneByWhere(['order_number'=>$order_id]);
            $message = new MessageModel();
            //退款提醒
            $message->sendMessage('Refund reminder',$shop_detail['user_id'], $shop_detail['user_name'], $order_return_data['return_code'], $shop_name = NULL, 1, 1);

            /* 2.扣除商家退款金额*/
            //退款金额，退货数量，交易佣金退款更新到订单表中
            $order_edit = array();
            $order_edit['order_refund_amount'] = $order_base['order_payment_amount'];
            $order_edit['order_return_num'] = $order_good['order_goods_num'];
            $order_edit['order_commission_return_fee'] = $order_good['order_goods_commission'];
            $order_edit['order_rpt_return'] = $field['return_rpt_cash'];

            $edit_flag  = $this->editBase($field['order_number'], $order_edit,true);

            check_rs($edit_flag,$rs_row);

            //由于该订单是未完成订单所以商家不需要扣除退款金额，但是要添加扣款流水
            //如果全部退款，增加卖家的流水
            $formvars = array();
            $formvars['app_id']        = Yf_Registry::get('shop_app_id');
            $formvars['user_id']    = $order_base['buyer_user_id']; //收款人
            $formvars['user_account'] = $order_base['buyer_user_account'];
            $formvars['seller_id'] = $order_base['seller_user_id']; //付款人
            $formvars['seller_account'] = $order_base['seller_user_name'];
            $formvars['amount'] = $field['return_cash'];
            $formvars['return_commision_fee'] = $field['return_commision_fee'];
            $formvars['order_id'] = $field['order_number'];
            $formvars['goods_id'] = $field['order_goods_id'];
            $formvars['uorder_id'] = $order_base['payment_other_number'];
            $formvars['payment_id'] = $order_base['payment_id'];
            $formvars['reason'] = "拼团退款（订单编号：$order_base[order_id]）";
            $key      = Yf_Registry::get('shop_api_key');
            $url         = Yf_Registry::get('paycenter_api_url');
            $return_rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=refundShopTransfer&typ=json', $url), $formvars);

            if ($return_rs['status'] != 200)
            {
                check_rs(false, $rs_row);
            }

            //退款退货提醒
            $message = new MessageModel();
            $message->sendMessage('Refund return reminder', $order_base['buyer_user_id'], $order_base['buyer_user_name'], $order_id = NULL, $shop_name = NULL, 0, MessageModel::ORDER_MESSAGE);

            /* 3.卖价增加退款金额*/
            //判断该笔订单是否是主账号支付，如果是主账号支付，则将退款金额退还主账号
            if($order_base['order_sub_pay'] == Order_StateModel::SUB_SELF_PAY)
            {
                $return_user_id = $field['buyer_user_id'];
                $return_user_name = $field['buyer_user_account'];
            }
            if($order_base['order_sub_pay'] == Order_StateModel::SUB_USER_PAY)
            {
                //查找主管账户用户名
                $User_BaseModel = new  User_BaseModel();
                $sub_user_base = $User_BaseModel->getOne($order_base['order_sub_user']);

                $return_user_id = $order_base['order_sub_user'];
                $return_user_name = $sub_user_base['user_account'];
            }

            $key      = Yf_Registry::get('shop_api_key');
            $url         = Yf_Registry::get('paycenter_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');

            $formvars             = array();
            $formvars['app_id']        = $shop_app_id;
            $formvars['user_id']  = $return_user_id;
            $formvars['user_account'] = $return_user_name;
            $formvars['seller_id'] = $field['seller_user_id'];
            $formvars['seller_account'] = $field['seller_user_account'];
            $formvars['amount']   = $field['return_cash'];
            $formvars['return_commision_fee']   = $field['return_commision_fee'];
            $formvars['order_id'] = $field['order_number'];
            $formvars['goods_id'] = $field['order_goods_id'];
            $formvars['payment_id'] = $order_base['payment_id'];
            $formvars['reason'] = "拼团退款（订单编号：$order_base[order_id]）";

            //SP分销单没有payment_other_number这个字段值会报错，所以在此做判断
            if($order_base['payment_other_number'])
            {
                $formvars['uorder_id'] = $order_base['payment_other_number'];
            }
            else
            {
                $formvars['uorder_id'] = $order_base['payment_number'];
            }

            //平台同意退款（只增加买家的流水）
            $rs                   = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=refundBuyerTransfer&typ=json', $url), $formvars);

            if ($rs['status'] == 200)
            {
                check_rs(true, $rs_row);
            }
            else
            {
                check_rs(false, $rs_row);
            }

            //将需要确认的订单号远程发送给Paycenter修改订单状态
            //远程修改paycenter中的订单状态(修改为完成)
            $key      = Yf_Registry::get('shop_api_key');
            $url         = Yf_Registry::get('paycenter_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');
            $formvars = array();
            $formvars['order_id']    = $order_base['order_id'];
            $formvars['app_id']        = $shop_app_id;
            $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
            $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=confirmOrder&typ=json', $url), $formvars);
            if($rs['status'] == 250)
            {
                $rs_flag = false;
                check_rs($rs_flag,$rs_row);
            }

            $flag = is_ok($rs_row);
            if ($flag && $this->sql->commitDb())
            {
                /**
                 *  加入统计中心
                 */
                //如果$return['order_goods_id']为0则为退款
                $order_goods_data = $Order_GoodsModel->getGoodsListByOrderId($field['order_number']);
                if(count($order_goods_data['items']) == 1)
                {
                    $order_return_goods_id = $order_goods_data['items'][0]['goods_id'];
                }
                else
                {
                    $order_return_goods_id = 0;
                }
                $order_goods_num = $order_goods_data['items'][0]['order_goods_num'];


                $analytics_data = array(
                    'order_id'=>array($field['order_number']),
                    'return_cash'=>$field['return_cash'],
                    'order_goods_num'=>$order_goods_num,
                    'order_goods_id'=>$order_return_goods_id,
                    'status'=>9 //暂时将退款退货统一处理
                );
                Yf_Plugin_Manager::getInstance()->trigger('analyticsUpdateOrderStatus',$analytics_data);
            }
            else
            {
                $this->sql->rollBackDb();
            }
        }

    }
    /**
     * 获取（地区，时间段）下单会员数
     *
     * 1,在线支付订单已付款，已完成
     * 2，货到付款，已完成
     * 3，时间区间内
     * @author xzg
     * @param $cond_row
     * @return array
     */
    public function getOrderUsers($cond_row)
    {
        $Order_BaseModel = new Order_BaseModel();
        if ($cond_row['district_id']) {
            $where = "where district_id='" . $cond_row['district_id'] . "'
                and shop_id ='" . $cond_row['shop_id'] . "' ";
        } else {
            $where = "where ((payment_id ='" . self::PAY_LINE . "'";
            $where .= "and order_status in ('6','2','3','4','5'))";
            $where .= "or (payment_id ='" . self::PAY_DELIVERY . "'";
            $where .= "and order_status ='" . Order_StateModel::ORDER_FINISH . "'))";
            $where .= "and shop_id ='" . $cond_row['shop_id'] . "'";
        }

        $star_time = $cond_row['start_time']." 00:00:00";
        $end_time = $cond_row['end_time']." 23:59:59";
        $sql = "SELECT COUNT(*) nums from `yf_order_base`
                {$where}
                and order_create_time >='" . $star_time . "' 
                and order_create_time <='" . $end_time . "'
                GROUP BY buyer_user_id";

        $order_base_list = $Order_BaseModel->sql->getAll($sql);

        if ($order_base_list) {
            return $order_base_list;
        } else {
            return array();
        }
    }
    /**
     * 获取（地区，时间段）下单金额
     *
     * 1,在线支付订单已付款，已完成
     * 2，货到付款，已完成
     * 3，时间区间内
     * @author xzg
     * @param $cond_row
     * @return array
     */
    public function getOrderPrices($cond_row)
    {
        if ($cond_row['district_id']){
            $where = "where district_id='" . $cond_row['district_id'] . "'
                and shop_id ='" . $cond_row['shop_id'] . "' ";
        }else {
            $where = "where ((payment_id ='" . self::PAY_LINE . "'";
            $where .= "and order_status in ('6','2','3','4','5'))";
            $where .= "or (payment_id ='" . self::PAY_DELIVERY . "'";
            $where .= "and order_status ='" . Order_StateModel::ORDER_FINISH . "'))";

            $where .= "and shop_id ='" . $cond_row['shop_id'] . "'";
            if ($cond_row['order_date']){
                $where .= "and order_date ='" . $cond_row['order_date'] . "'";
            }else {
                $where .= "and order_date >='" . $cond_row['start_time'] . "' ";
                $where .= "and order_date <='" . $cond_row['end_time'] . "' ";
            }
        }
        $sql = "SELECT SUM(order_goods_amount) sums from `yf_order_base` {$where}";

        $sums = $this->sql->getAll($sql);

        return $sums[0]['sums'];
    }
    /**
     * 获取（地区，时间段）下单量
     *
     * 1,在线支付订单已付款，已完成
     * 2，货到付款，已完成
     * 3，时间区间内
     * @author xzg
     * @param $cond_row
     * @return array
     */
    public function getOrderNums($cond_row)
    {
        if ($cond_row['district_id']){
            $where = "where district_id='" . $cond_row['district_id'] . "'
                and shop_id ='" . $cond_row['shop_id'] . "' ";
        } else {
            $where = "where ((payment_id ='" . self::PAY_LINE . "'";
            $where .= "and order_status in ('6','2','3','4','5'))";
            $where .= "or (payment_id ='" . self::PAY_DELIVERY . "'";
            $where .= "and order_status ='" . Order_StateModel::ORDER_FINISH . "'))";

            $where .= "and shop_id ='" . $cond_row['shop_id'] . "'";
            if ($cond_row['order_date']){
                $where .= "and order_date ='" . $cond_row['order_date'] . "'";
            } else {
                $where .= "and order_date >='" . $cond_row['start_time'] . "' ";
                $where .= "and order_date <='" . $cond_row['end_time'] . "' ";
            }
        }
        $sql = "SELECT COUNT(*) nums from `yf_order_base` {$where}";
        $nums = $this->sql->getAll($sql);

        return $nums[0]['nums'];
    }
    /**
     * 获取某时间段下单商品(名称，数量)
     *
     * 1,在线支付订单已付款，已完成
     * 2，货到付款，已完成
     * 3，时间区间内
     * @author xzg
     * @param $cond_row
     * @return array
     */
    public function getOrderGoodsByDate($cond_row)
    {
        if ($cond_row['district_id']){
            $where = "where district_id='" . $cond_row['district_id'] . "'
                and shop_id ='" . $cond_row['shop_id'] . "' ";
        } else {
            $where = "where ((payment_id ='" . self::PAY_LINE . "'";
            $where .= "and order_status in ('6','2','3','4','5'))";
            $where .= "or (payment_id ='" . self::PAY_DELIVERY . "'";
            $where .= "and order_status ='" . Order_StateModel::ORDER_FINISH . "'))";

            $where .= "and shop_id ='" . $cond_row['shop_id'] . "'";
            if ($cond_row['order_date']){
                $where .= "and order_date ='" . $cond_row['order_date'] . "'";
            } else {
                $where .= "and order_date >='" . $cond_row['start_time'] . "' ";
                $where .= "and order_date <='" . $cond_row['end_time'] . "' ";
            }
        }
        $sql = "SELECT order_id from `yf_order_base` {$where}";
        //获取有效订单的订单id集合
        $order_ids = $this->sql->getAll($sql);
        //去除key值
        $order_ids = array_column($order_ids, 'order_id');
        //拼接成字符串
        $order_ids = implode("','", $order_ids);
        //查询有效订单出售最多的商品
        $sql1 = "SELECT goods_name,COUNT(*) order_num from `yf_order_goods`
                WHERE order_id in ('".$order_ids."')
                GROUP BY common_id ORDER BY order_num DESC";

        $order_goods_model = new Order_GoodsModel();
        $order_goods = $order_goods_model->sql->getAll($sql1);

        return $order_goods;
    }

    //订单导出
    public function getOrderInfoExcel($cond_row,$noLimit='')
    {

        $sql = "SELECT order_id,order_shipping_express_id,order_message,order_invoice,order_from,order_create_time,order_payment_amount,order_status,payment_number,payment_name,payment_time,order_shipping_code,order_refund_amount,order_finished_time,order_buyer_evaluation_status,shop_id,shop_name,buyer_user_id,buyer_user_name,order_receiver_address,order_receiver_name,order_receiver_contact FROM ".$this->_tableName." WHERE 1=1";
        if($cond_row['order_id']) {
            $sql .= " AND order_id LIKE '%".$cond_row['order_id']."%'";
        }
        if($cond_row['buyer_name']) {
            $sql .= " AND buyer_user_name LIKE '%".$cond_row['buyer_name']."%'";
        }
        if($cond_row['query_start_date']) {
            $sql .= " AND order_create_time >='".$cond_row['query_start_date'] . "'";
        }
        if($cond_row['query_end_date']) {
            $sql .= " AND order_create_time <='".$cond_row['query_end_date'] . "'";
        }
        if($cond_row['shop_name']) {
            $sql .= " AND shop_name LIKE '%".$cond_row['shop_name']."%'";
        }
        if($cond_row['shop_id']) {
            $sql .= " AND shop_id = ".$cond_row['shop_id'];
        }
        if($cond_row['action'] == 'virtual')
        {
            $sql .= " AND order_is_virtual = ".Order_BaseModel::ORDER_IS_VIRTUAL;
        }else{
            $sql .= " AND order_is_virtual = ".Order_BaseModel::ORDER_IS_REAL;
        }
        if($cond_row['payment_number']) {
            $sql .= " AND payment_number LIKE '%".$cond_row['payment_number']."%'";
        }
        if($cond_row['payment_date_f'] && !$cond_row['payment_date_t']) {
            $sql .= " AND payment_time > ".$cond_row['payment_date_f'];
        }else if(!$cond_row['payment_date_f'] && $cond_row['payment_date_t']) {
            $sql .= " AND payment_time < ".$cond_row['payment_date_t'];
        }else if($cond_row['payment_date_f'] && $cond_row['payment_date_t']) {
            $sql .= " AND payment_time BETWEEN '".$cond_row['payment_date_f']."' AND '".$cond_row['payment_date_t']."'";
        }

        if($cond_row['chain_id'] == 0) {
            $sql .= " AND chain_id = 0";
        }
        if($cond_row['order_shop_hidden'] == 0) {
            $sql .= " AND order_shop_hidden = 0";
        }
        if($cond_row['order_status']) {
            if($cond_row['order_status']=='2,3'){
                $sql .= " AND order_status IN (2,3)";
            }else{
                $sql .= " AND order_status = ".$cond_row['order_status'];
            }
            
        }
        $sql .= " AND order_payment_amount-order_refund_amount >= " . "0";
        $sql .= " ORDER BY order_create_time DESC";
   
        //不需要分页
        if(!$noLimit){
            if($cond_row['limit'] && $cond_row['is_limit']){
                $sql .= " LIMIT ".$cond_row['start_limit'].','.$cond_row['limit'];
            }else{
                $start = $cond_row['limits']*1000;
                $sql .= " LIMIT ".$start.',1000';
            }
        }
        $data = $this->sql->getAll($sql);
        if($data){
            $Order_StateModel = new Order_StateModel();
            $Order_GoodsModel = new Order_GoodsModel();
            $ExpressModel = new ExpressModel();
            //查物流公司
            foreach($data as $k=>$v){
                $order_goods_list = $Order_GoodsModel->getByWhere(array('order_id:IN' => $v['order_id']));
                $express = $ExpressModel->getOne($v['order_shipping_express_id']);
                $data[$k]['express_name'] = $express['express_name'];
                $data[$k]['goods_info'] = array_values($order_goods_list);
                $data[$k]['order_from_text'] = $Order_StateModel->orderFrom[$v['order_from']];
                $data[$k]['order_status_text'] = $Order_StateModel->orderState[$v['order_status']];
                $data[$k]['order_buyer_evaluation_status'] = $Order_StateModel->evaluationStatus[$v['order_buyer_evaluation_status']];
                
            }
        }
        return $data;
    }

    //数据库总条数分页
    public function getCounts($action)
    {
        $sqls = "SELECT count(*) as sum FROM ".$this->_tableName;
        if($action == 'virtual')
        {
            $sqls .= ' WHERE order_is_virtual = '.Order_BaseModel::ORDER_IS_VIRTUAL;
        }
        $sum = $this->sql->getRow($sqls);
        $limt = ceil($sum['sum']/1000);
        return $limt;
    }

   //订单支付成功后修改订单状态
    public function webpos_editOrderStatusAferPay($order_id = null, $uorder_id = null)
    {
        $flag = false;
        //查找订单信息
        $order_base = $this->getOne($order_id);
        if($order_base['order_status'] == Order_StateModel::ORDER_WAIT_PAY)
        {
            $edit_row = array('order_status' => Order_StateModel::ORDER_PAYED);
            $edit_row['payment_time'] = get_date_time();
            $edit_row['payment_other_number'] = $uorder_id;
            //修改订单状态
            $this->editBase($order_id, $edit_row);

            //修改订单商品状态
            $Order_GoodsModel = new Order_GoodsModel();
            $edit_goods_row = array('order_goods_status' => Order_StateModel::ORDER_PAYED);
            $order_goods_id = $Order_GoodsModel->getKeyByWhere(array('order_id' => $order_id));
            $flag = $Order_GoodsModel->editGoods($order_goods_id, $edit_goods_row);

            //修改商品的销量
            $Goods_BaseModel = new Goods_BaseModel();
            $Goods_BaseModel->editGoodsSale($order_goods_id);

            $Goods_CommonModel = new Goods_CommonModel();

            //判断是否是虚拟订单，若是虚拟订单则生成发送信息并修改订单状态为已发货
            if ($order_base['order_is_virtual'] == Order_BaseModel::ORDER_IS_VIRTUAL) {
                //循环订单商品
                $Text_Password = new Text_Password();
                $Order_GoodsVirtualCodeModel = new Order_GoodsVirtualCodeModel();

                $msg_str = '尊敬的用户您已在' . $order_base['shop_name'] . '成功购买';
                $goods_name_str = '';
                $virtual_code_str = '';
                foreach ($order_goods_id as $k => $v) {
                    $order_goods_base = $Order_GoodsModel->getOne($v);
                    //判断该商品是否是虚拟商品
                    $goods_common_base = $Goods_CommonModel->getOne($order_goods_base['common_id']);
                    if($goods_common_base['common_is_virtual'])
                    {
                        $num = $order_goods_base['order_goods_num'];
                        //获取购买的数量，循环生成虚拟兑换码，将信息插入虚拟兑换码表中

                        for ($i = 0; $i < $num; $i++) {
                            $virtual_code = $Text_Password->create(8, 'unpronounceable', 'numeric');
                            $virtual_code_str .= $virtual_code . ',';
                            $add_row = array();
                            $add_row['virtual_code_id'] = $virtual_code;
                            $add_row['order_id'] = $order_id;
                            $add_row['order_goods_id'] = $v;
                            $add_row['virtual_code_status'] = Order_GoodsVirtualCodeModel::VIRTUAL_CODE_NEW;

                            $Order_GoodsVirtualCodeModel->addCode($add_row);
                        }
                        $goods_name_str .= $order_goods_base['goods_name'] . '，';


                        //修改订单商品为已发货待收货4
                        $edit_order_goods_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
                        $Order_GoodsModel->editGoods($v, $edit_order_goods_row);
                    }
                }

                $msg_str = $msg_str . $goods_name_str . '您可凭兑换码' . $virtual_code_str . '在本店进行消费。';
                Sms::send($order_base['order_receiver_contact'], $msg_str);
                //$str = Sms::send(13918675918,"尊敬的用户您已在【变量】成功购买【变量】，您可凭兑换码【变量】在本店进行消费。");

                $goods_common_id = array_column($Order_GoodsModel->get($order_goods_id),'common_id');
                $Goods_Common = new Goods_CommonModel();
                $goods_common = current($Goods_Common->get($goods_common_id));
                //修改订单状态为已发货等待收货4
                $edit_order_row['order_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
                $edit_order_row['order_shipping_time'] = get_date_time();
                $edit_order_row['order_receiver_date'] = $goods_common['common_virtual_date'];
                $this->editBase($order_id, $edit_order_row);
            }

            //判断是否是门店自提订单，若是门店自提订单则生成发送信息并修改订单状态为待自提
            if ($order_base['chain_id']) {
                $code     = VerifyCode::getCode($order_base['order_receiver_contact']);

                $Chain_BaseModel=new Chain_BaseModel();
                $chain_base = current($Chain_BaseModel->getByWhere(array('chain_id'=>$order_base['chain_id'])));

                $order_goods_base = $Order_GoodsModel->getOne($order_goods_id[0]);

                $Order_GoodsChainCodeModel = new Order_GoodsChainCodeModel();
                $code_data['order_id']=$order_id;
                $code_data['chain_id']=$order_base['chain_id'];
                $code_data['order_goods_id']=$order_goods_id[0];
                $code_data['chain_code_id']=$code;
                $Order_GoodsChainCodeModel->addGoodsChainCode($code_data);

                //修改订单状态为待自提11
                $edit_order_goods_row['order_goods_status'] = Order_StateModel::ORDER_FINISH;
                $Order_GoodsModel->editGoods($order_goods_id, $edit_order_goods_row);

                $edit_order_row['order_status'] = Order_StateModel::ORDER_FINISH;
                $this->editBase($order_id, $edit_order_row);

                $message = new MessageModel();
                $message->sendMessage('Self pick up code', Perm::$userId, Perm::$row['user_account'], $order_id = NULL, $shop_name = $order_base['shop_name'], 1, MessageModel::ORDER_MESSAGE,  NUll,NULL,NULL,NULL, Null,$goods_name=$order_goods_base['goods_name'], NULL,NULL,$ztm=$code,$chain_name=$chain_base['chain_name'],$order_phone=$order_base['order_receiver_contact']);
                //$str = Sms::send(13918675918,"尊敬的用户您已在[shop_name]成功购买[goods_name]，您可凭自提码[ztm]在[chain_name]自提。");
            }


            //判断此订单是否使用了代金券，如果使用，则改变代金券的使用状态
            /*if ($order_base['voucher_id']) {
                $Voucher_BaseModel = new Voucher_BaseModel();
                $Voucher_BaseModel->changeVoucherState($order_base['voucher_id'], $order_base['order_id']);

                //代金券使用提醒
                $message = new MessageModel();
                $message->sendMessage('The use of vouchers to remind', Perm::$userId, Perm::$row['user_account'], $order_id = NULL, $shop_name = NULL, 0, MessageModel::USER_MESSAGE);
            }*/
        }
        return $flag;
    }

    //生成事物砍价订单
    public function addBargainOrderBase($bargain_info)
    {
        $user_id = $bargain_info['user_id'];
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOneByWhere(array('user_id'=>$user_id));
        $user_account = $user_info['user_name'];
        $goods_id = $bargain_info['goods_id'];
        $goods_num = 1;
        $remark ='';
        $pay_way_id = PaymentChannlModel::PAY_ONLINE;;
        $invoice = '';
        $invoice_id = '';
        $invoice_title = '';
        $invoice_content = '';
        $address_id = $bargain_info['address_id'];
        $order_from = Order_StateModel::FROM_WAP;//来源
        $mark_id = 0;
        $data = array();
        $rs_row = array();

        //获取商品信息
        $Goods_BaseModel = new Goods_BaseModel();
        $goods_info = $Goods_BaseModel->getGoodsAndCommon($goods_id);

        if (!$goods_info || $goods_num <= 0 || ($goods_num > $goods_info['common']['common_limit'] && $goods_info['common']['common_limit'] > 0) || $goods_num > $goods_info['common']['common_stock']) {
            $data['code'] = 2;
            $data['status'] = 250;
            $data['data'] = array();
        }
        
        //店铺信息
        $shop_model = new Shop_BaseModel();
        $shop_info = $shop_model->getOne($goods_info['common']['shop_id']);

        //地址信息
        $address_model = new User_AddressModel();
        $address_info = $address_model->getOne($address_id);
        if ($address_info['user_id'] != $user_id) {
            $data['code'] = 3;
            $data['status'] = 250;
            $data['data'] = array();
        }

        //查找收货地址
        $city_id = $address_info['user_address_city_id'];
        if ($city_id) {
            //判断商品的售卖区域
            $area_model = new Transport_AreaModel();
            $checkArea = $area_model->isSale($goods_info['common']['transport_area_id'], $city_id);
            if (!$checkArea) {
                $data['code'] = 4;
                $data['status'] = 250;
                $data['data'] = array();
            }
            $transport = array('cost' => 0, 'con' => '');
        } else {
            $data['code'] = 5;
            $data['status'] = 250;
            $data['data'] = array();
        }
        //判断支付方式为在线支付还是货到付款,如果砍价底价等于则订单状态直接为待发货状态，如果是在线砍价底价大于0则订单状态为待付款
        if ($bargain_info['bargain_price'] > 0) {
            $order_status = Order_StateModel::ORDER_WAIT_PAY;
        }else{
            $order_status = Order_StateModel::ORDER_PAYED;
        }

        $goods_info['base']['sumprice'] = $bargain_info['goods_price'];
        $goods_info['base']['rate_price'] = $bargain_info['goods_price'] - $bargain_info['bargain_price'];
        $goods_info['base']['now_price'] = $bargain_info['bargain_price'];

        //分销员开启，查找用户的上级
        if (Web_ConfigModel::value('Plugin_Directseller')) {
            $User_InfoModel = new User_InfoModel();
            $user_info = $User_InfoModel->getOne($user_id);
            $user_parent_id = $user_info['user_parent_id'];  //用户上级ID
            $user_parent = $User_InfoModel->getOne($user_parent_id);
            $directseller_p_id = $user_parent['user_parent_id'];  //二级
            $user_g_parent = $User_InfoModel->getOne($directseller_p_id);
            $directseller_gp_id = $user_g_parent['user_parent_id']; //三级
        }

        /* 计算三级分销 - S */
        //分佣开启，并且参与分佣
        $val = array();
        $val['directseller_flag'] = 0;
        if (Web_ConfigModel::value('Plugin_Directseller') && $goods_info['common']['common_is_directseller']) {
            $Distribution_ShopDirectsellerModel = new Distribution_ShopDirectsellerModel();
            $directseller_commission = 0;

            //获取该用户直属上三级用户
            $User_InfoModel = new User_InfoModel();
            $user_parent = $User_InfoModel->getUserPatents($user_id);

            //用户存在分销上级，并且这件商品是分销上级分销的商品则产生相应的分销佣金
            $val['directseller_commission_0'] = 0;
            $val['directseller_flag_0'] = 0;

            if ($user_parent['user_parent_0']) {
                //一级分佣
                $a = $Distribution_ShopDirectsellerModel->checkDirectsellerGoods($user_parent['user_parent_0'], $goods_info['base']['shop_id']);
                if ($a) {
                    $val['directseller_commission_0'] = number_format(($goods_info['base']['sumprice'] * $goods_info['common']['common_cps_rate'] / 100), 2, '.', '');
                    $val['directseller_flag_0'] = 1;
                    $val['directseller_flag'] = $goods_info['common']['common_is_directseller'];
                }

            }

            $val['directseller_commission_1'] = 0;
            $val['directseller_flag_1'] = 0;
            if ($user_parent['user_parent_1']) {
                //二级分佣
                $b = $Distribution_ShopDirectsellerModel->checkDirectsellerGoods($user_parent['user_parent_1'], $goods_info['base']['shop_id']);
                if ($b) {
                    $val['directseller_commission_1'] = number_format(($goods_info['base']['sumprice'] * $goods_info['common']['common_second_cps_rate'] / 100), 2, '.', '');
                    $val['directseller_flag_1'] = 1;
                    $val['directseller_flag'] = $goods_info['common']['common_is_directseller'];
                }

            }

            $val['directseller_commission_2'] = 0;
            $val['directseller_flag_2'] = 0;
            if ($user_parent['user_parent_2']) {
                //三级分佣
                $c = $Distribution_ShopDirectsellerModel->checkDirectsellerGoods($user_parent['user_parent_2'], $goods_info['base']['shop_id']);
                if ($c) {
                    $val['directseller_commission_2'] = number_format(($goods_info['base']['sumprice'] * $goods_info['common']['common_third_cps_rate'] / 100), 2, '.', '');
                    $val['directseller_flag_2'] = 1;
                    $val['directseller_flag'] = $goods_info['common']['common_is_directseller'];
                }

            }

            $directseller_commission += $val['directseller_commission_0'] + $val['directseller_commission_1'] + $val['directseller_commission_2'];
        }
        /* 计算三级分销 - E */

        $Number_SeqModel = new Number_SeqModel();
        $Order_GoodsModel = new Order_GoodsModel();
        $PaymentChannlModel = new PaymentChannlModel();
        $Order_GoodsSnapshot = new Order_GoodsSnapshot();
        //合并支付订单的价格
        $uprice = 0;
        $inorder = '';
        $utrade_title = '';    //商品名称 - 标题
        $prefix = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));
        $order_number = $Number_SeqModel->createSeq($prefix);
        $order_id = sprintf('%s-%s-%s-%s', 'DD', $shop_info['user_id'], $shop_info['shop_id'], $order_number);

        //生成订单发票信息
        $Order_InvoiceModel = new Order_InvoiceModel();
        $order_invoice_id = 0;
        $order_row = array();
        $order_row['order_id'] = $order_id;
        $order_row['shop_id'] = $shop_info['shop_id'];
        $order_row['shop_name'] = $shop_info['shop_name'];
        $order_row['buyer_user_id'] = $user_id;
        $order_row['buyer_user_name'] = $user_account;
        $order_row['seller_user_id'] = $shop_info['user_id'];
        $order_row['seller_user_name'] = $shop_info['user_name'];
        $order_row['order_date'] = date('Y-m-d');
        $order_row['order_create_time'] = get_date_time();
        $order_row['order_receiver_name'] = $address_info['user_address_contact'];
        $order_row['order_receiver_address'] = $address_info['user_address_area'] . ' ' . $address_info['user_address_address'];
        $order_row['order_receiver_contact'] = $address_info['user_address_phone'];
        $order_row['order_invoice'] = $invoice;
        $order_row['order_invoice_id'] = $order_invoice_id;
        $order_row['order_goods_amount'] = $goods_info['base']['now_price']; //订单商品总价（不包含运费）
        $order_row['order_payment_amount'] = $goods_info['base']['now_price'];// 订单实际支付金额 = 商品实际支付金额 + 运费
        $order_row['order_discount_fee'] = $goods_info['base']['rate_price'];   //优惠价格 = 商品总价 - 商品实际支付金额
        $order_row['order_point_fee'] = 0;    //买家使用积分
        $order_row['order_shipping_fee'] = $transport['cost'];
        $order_row['order_message'] = $remark;
        $order_row['order_status'] = $order_status;
        $order_row['order_points_add'] = 0;    //订单赠送的积分
        $order_row['voucher_id'] = '';    //代金券id
        $order_row['voucher_price'] = 0;    //代金券面额
        $order_row['voucher_code'] = '';    //代金券编码
        $order_row['order_from'] = $order_from;    //订单来源
        //平台红包及其优惠信息
        $order_row['redpacket_code'] = 0;        //红包编码
        $order_row['redpacket_price'] = 0;    //红包面额
        $order_row['order_rpt_price'] = 0;    //平台红包抵扣订单金额

        //砍价订单标识
        $order_row['order_is_bargain'] = 1; //是否砍价订单 0-不是 1-是

        //如果卖家设置了默认地址，则将默认地址信息加入order_base表
        $Shop_ShippingAddressModel = new Shop_ShippingAddressModel();
        $address_list = $Shop_ShippingAddressModel->getByWhere(array('shop_id' => $shop_info['shop_id'], 'shipping_address_default' => 1));
        if ($address_list) {
            $address_list = current($address_list);
            $order_row['order_seller_address'] = $address_list['shipping_address_area'] . " " . $address_list['shipping_address_address'];
            $order_row['order_seller_contact'] = $address_list['shipping_address_phone'];
            $order_row['order_seller_name'] = $address_list['shipping_address_contact'];
        }
        //该商品的交易佣金计算
        $Goods_CatModel = new Goods_CatModel();
        $goods_info['base']['commission'] = $Goods_CatModel->getCatCommission($goods_info['base']['sumprice'], $goods_info['base']['cat_id']);

        //后台开启商品佣金则需要收取商品佣金
        if (Web_ConfigModel::value('goods_commission')) {
            $order_row['order_commission_fee'] = $goods_info['base']['commission'];
        } else {
            $order_row['order_commission_fee'] = 0;
        }

        $order_row['order_is_virtual'] = 0;    //1-虚拟订单 0-实物订单
        $order_row['order_shop_benefit'] = '砍价活动';  //店铺优惠
        $order_row['payment_id'] = $pay_way_id;
        $order_row['payment_name'] = $PaymentChannlModel->payWay[$pay_way_id];
        $order_row['directseller_discount'] = 0;//分销商折扣

        $order_row['directseller_flag'] = @$val['directseller_flag'];

        if (@$val['directseller_flag_0']) {
            $order_row['directseller_id'] = $user_parent_id;
        }
        if (@$val['directseller_flag_1']) {
            $order_row['directseller_p_id'] = $directseller_p_id;
        }
        if (@$val['directseller_flag_2']) {
            $order_row['directseller_gp_id'] = $directseller_gp_id;
        }

        $order_row['district_id'] = $shop_info['district_id'];

        //获取店铺佣金
        $Shop_ClassBindModel = new Shop_ClassBindModel();
        $cat_commission = $Shop_ClassBindModel->getShopCateCommission($goods_info['base']['shop_id'], $goods_info['base']['cat_id']);
        $goods_info['base']['commission'] = number_format(($goods_info['base']['sumprice'] * $cat_commission / 100), 2, '.', '');

        //将不同订单号分别插入订单发票表
        if ($order_invoice_id > 0) {
            $Order_InvoiceModel->editInvoice($order_invoice_id, array('order_id' => $order_id));
            unset($order_invoice_id);
        }
        $flag1 = $this->addBase($order_row);
        check_rs($flag1, $rs_row);
        if (!$flag1) {
            $data['code'] = 11;
            $data['status'] = 250;
            $data['data'] = array();
        }

        //如果买家买的是分销商在供货商分销的支持代发货的商品，再生成分销商进货订单
        $dist_flag[] = true;
        if ($goods_info['common']['common_parent_id'] && $goods_info['common']['product_is_behalf_delivery'] == 1) {
//            $dist_flag[] = $this->distributor_add_order($goods_info['base']['goods_id'], $goods_num, $shop_info['shop_id'], $address_info['user_address_contact'], $address_info['user_address_area'] . ' ' . $address_info['user_address_address'], $address_info['user_address_phone'], $address_id, $pay_way_id, $order_id, $invoice);
//            $Goods_CommonModel = new Goods_CommonModel();
//            //获取SP订单号，添加到买家订单商品表
//            $parent_common = $Goods_CommonModel->getOne($goods_info['common']['common_parent_id']);
//            $sp_order_base = $this->getOneByWhere(array('order_source_id' => $order_id, 'shop_id' => $parent_common['shop_id']));
            if (Yf_Registry::get('supplier_is_open') == 0) {
                $invoice_data = $this->getShopInvoice($shop_info);

                $dist_flag[] = $this->distributor_add_order($goods_info['base']['goods_id'], $goods_num, $shop_info['shop_id'], $address_info['user_address_contact'], $address_info['user_address_area'] . ' ' . $address_info['user_address_address'], $address_info['user_address_phone'], $address_id, $pay_way_id, $order_id, $invoice_data['invoice'], '', '', $invoice_data['invoice_id']);
//                    $dist_flag[] = $this -> distributor_add_order($goods_info['base']['goods_id'], $goods_num, $shop_info['shop_id'], $address_info['user_address_contact'], $address_info['user_address_area'] . ' ' . $address_info['user_address_address'], $address_info['user_address_phone'], $address_id, $pay_way_id, $order_id, $invoice, '', '', $invoice_id);
                $Goods_CommonModel = new Goods_CommonModel();
                //获取SP订单号，添加到买家订单商品表
                $parent_common = $Goods_CommonModel->getOne($goods_info['common']['common_parent_id']);
                $sp_order_base = $this->getOneByWhere(array('order_source_id' => $order_id, 'shop_id' => $parent_common['shop_id']));
            } else {
                $supplierModel = new Supplier;
                $supplier_order_id = $supplierModel->addOrder($goods_info['base']['goods_id'], $goods_num, $shop_info['shop_id'], $address_info['user_address_contact'], $address_info['user_address_area'] . ' ' . $address_info['user_address_address'], $address_info['user_address_phone'], $address_id, $pay_way_id, $order_id, $invoice);
                $dist_flag[] = $supplier_order_id;
                $sp_order_base = ['order_id' => $supplier_order_id]; //供应商单子
            }
        }
        $order_goods_row = array();
        $order_goods_row['order_id'] = $order_id;
        $order_goods_row['goods_id'] = $goods_info['base']['goods_id'];
        $order_goods_row['common_id'] = $goods_info['base']['common_id'];
        $order_goods_row['buyer_user_id'] = $user_id;
        $order_goods_row['goods_name'] = $goods_info['base']['goods_name'];
        $order_goods_row['goods_class_id'] = $goods_info['base']['cat_id'];
        $order_goods_row['order_spec_info'] = $goods_info['base']['spec'];
        $order_goods_row['goods_price'] = $goods_info['base']['goods_price']; //商品原来的单价
        $order_goods_row['order_goods_payment_amount'] = $bargain_info['bargain_price'];  //商品实际支付单价
        $order_goods_row['order_goods_num'] = $goods_num;
        $order_goods_row['goods_image'] = $goods_info['base']['goods_image'];
        $order_goods_row['order_goods_amount'] = $bargain_info['bargain_price'];  //商品实际支付金额
        $order_goods_row['order_goods_discount_fee'] = $bargain_info['goods_price'] - $bargain_info['bargain_price'];//优惠价格
        $order_goods_row['order_goods_adjust_fee'] = 0;    //手工调整金额
        $order_goods_row['order_goods_point_fee'] = 0;    //积分费用
        $order_goods_row['shop_id'] = $shop_info['shop_id'];
        $order_goods_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_PAY;
        $order_goods_row['order_goods_evaluation_status'] = 0;  //0未评价 1已评价
        $order_goods_row['order_goods_benefit'] = '砍价商品';
        $order_goods_row['order_goods_time'] = get_date_time();
        $order_goods_row['directseller_goods_discount'] = 0;//分销商折扣
        if ($goods_info['common']['common_parent_id'] && $goods_info['common']['product_is_behalf_delivery'] == 1) {
            $order_goods_row['order_goods_source_id'] = $sp_order_base['order_id'];//供货商对应的订单
        }
        $order_goods_row['directseller_flag'] = @$val['directseller_flag'];
        $order_goods_row['directseller_commission_0'] = @$val['directseller_commission_0'];
        $order_goods_row['directseller_commission_1'] = @$val['directseller_commission_1'];
        $order_goods_row['directseller_commission_2'] = @$val['directseller_commission_2'];
        //平台是否收取分销商,供货商佣金
        $goods_commission = Web_ConfigModel::value('goods_commission');
        $supplier_commission = Web_ConfigModel::value('supplier_commission');

        if (Web_ConfigModel::value('goods_commission')) {
            $order_goods_row['order_goods_commission'] = $goods_info['base']['commission'];   //商品佣金(总)
        } else {
            $order_goods_row['order_goods_commission'] = 0;    //商品佣金(总)
        }

        $flag2 = $Order_GoodsModel->addGoods($order_goods_row);
        check_rs($flag2, $rs_row);
        if (!$flag2) {
            $data['code'] = 12;
            $data['status'] = 250;
            $data['data'] = array();
        }
        //加入交易快照表
        $order_goods_snapshot_add_row = array();
        $order_goods_snapshot_add_row['order_id'] = $order_id;
        $order_goods_snapshot_add_row['user_id'] = $user_id;
        $order_goods_snapshot_add_row['shop_id'] = $goods_info['base']['shop_id'];
        $order_goods_snapshot_add_row['common_id'] = $goods_info['base']['common_id'];
        $order_goods_snapshot_add_row['goods_id'] = $goods_info['base']['goods_id'];
        $order_goods_snapshot_add_row['goods_name'] = $goods_info['base']['goods_name'];
        $order_goods_snapshot_add_row['goods_image'] = $goods_info['base']['goods_image'];
        $order_goods_snapshot_add_row['goods_price'] = $goods_info['base']['now_price'];
        $order_goods_snapshot_add_row['freight'] = $transport['cost'];   //运费
        $order_goods_snapshot_add_row['snapshot_create_time'] = get_date_time();
        $order_goods_snapshot_add_row['snapshot_uptime'] = get_date_time();
        $order_goods_snapshot_add_row['snapshot_detail'] = '';
        $res = $Order_GoodsSnapshot->addSnapshot($order_goods_snapshot_add_row);
        check_rs($res, $rs_row);
        if (!$res) {
            $data['code'] = 13;
            $data['status'] = 250;
            $data['data'] = array();
        }
        //删除商品库存
        $flag3 = $Goods_BaseModel->delStock($goods_info['base']['goods_id'], $goods_num);
        check_rs($flag3, $rs_row);
        if (!$flag3) {
            $data['code'] = 23;
            $data['status'] = 250;
            $data['data'] = array();
        }
        $trade_title = $goods_info['base']['goods_name'];
        //支付中心生成订单
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        $formvars['consume_trade_id'] = $order_row['order_id'];
        $formvars['order_id'] = $order_row['order_id'];
        $formvars['buy_id'] = $user_id;
        $formvars['buyer_name'] = '';
        $formvars['seller_id'] = $order_row['seller_user_id'];
        $formvars['seller_name'] = $order_row['seller_user_name'];
        $formvars['order_state_id'] = $order_row['order_status'];
        $formvars['order_payment_amount'] = $order_row['order_payment_amount'];
        $formvars['order_commission_fee'] = $order_row['order_commission_fee'];
        $formvars['trade_remark'] = $order_row['order_message'];
        $formvars['trade_create_time'] = $order_row['order_create_time'];
        $formvars['trade_title'] = $trade_title;        //商品名称 - 标题
        $rs1 = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addConsumeTrade&typ=json', $url), $formvars);
        //将合并支付单号插入数据库
        if ($rs1['status'] == 200) {
            $flag = $this->editBase($order_id, array('payment_number' => $rs1['data']['union_order']));
            check_rs($flag, $rs_row);
            if ($flag === false) {
                $data['code'] = 14;
                $data['status'] = 250;
                $data['data'] = array();
            }
        } else {
            $data['code'] = 15;
            $data['status'] = 250;
            $data['data'] = array();
        }
        $uprice += $order_row['order_payment_amount'];
        $inorder .= $order_id . ',';
        $utrade_title .= $trade_title;
        //生成合并支付订单
        $formvars = array();
        $formvars['inorder'] = $inorder;
        $formvars['uprice'] = $uprice;
        $formvars['buyer'] = $user_id;
        $formvars['trade_title'] = $utrade_title;
        $formvars['buyer_name'] = Perm::$row['user_account'];
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        $formvars['mark_id'] = $mark_id;
        $rs2 = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addUnionOrder&typ=json', $url), $formvars);

        if ($rs2['status'] == 200) {
            $uorder = $rs2['data']['uorder'];
        } else {
            $uorder = '';
            if ($flag === false) {
                $data['code'] = 16;
                $data['status'] = 250;
                $data['data'] = array();
            }
        }

        $order_flag = is_ok($rs_row);

        if ($order_flag) {
            $data['code'] = 0;
            $data['status'] = 200;
            $data['data'] = array('uorder' => $uorder, 'order_id' => $order_id);
        } else {
            $m = $this->msg->getMessages();

            //订单提交失败，将paycenter中生成的订单删除
            if ($uorder) {
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');
                $formvars = array();
                $formvars['uorder'] = $uorder;
                $formvars['app_id'] = $shop_app_id;
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=delUnionOrder&typ=json', $url), $formvars);
            }
            $data['code'] = 0;
            $data['status'] = 250;
            $data['msg'] = $m ? $m[0] : __('failure');
            $data['data'] = array();
        }
        return $data;
    }

    /**
     * 生成分销商进货订单
     * //该方法生成的是分销商在供货商出进货的订单，分销商为买家，供货商为卖家
     */
    public function distributor_add_order($goods_id, $num, $distributor_id, $rec_name, $rec_address, $rec_phone, $addr_id, $pay_way_id, $p_order_id, $invoice, $invoice_title, $invoice_content, $invoice_id)
    {
        $Order_BaseModel = new Order_BaseModel();
        $Goods_CommonModel = new Goods_CommonModel();
        $Shop_BaseModel = new Shop_BaseModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $receiver_name = $rec_name;                //收货人
        $receiver_address = $rec_address;               //收货地址
        $receiver_phone = $rec_phone;              // 收货人电话
        $goods_num = $num;                //商品数量
        $address_id = $addr_id;                    //买家收货地址id
        //判断支付方式为在线支付还是货到付款,如果是货到付款则订单状态直接为待发货状态，如果是在线支付则订单状态为待付款
        if ($pay_way_id == PaymentChannlModel::PAY_ONLINE) {
            $order_status = Order_StateModel::ORDER_WAIT_PAY;
        }
        if ($pay_way_id == PaymentChannlModel::PAY_CONFIRM) {
            $order_status = Order_StateModel::ORDER_WAIT_PREPARE_GOODS;
        }
        //分销商（买家数据）
        $distributor_shop_info = $Shop_BaseModel->getOne($distributor_id);//分销商店铺
        $goodsbaseinfo = $Goods_BaseModel->getGoodsDetailInfoByGoodId($goods_id);//商品详情$data['goods_base']，$data['common_base']，$data['shop_base']，$data['mansong_info']
        fb($distributor_shop_info);
        $user_id = $distributor_shop_info['user_id']; //分销商店铺用户user_id
        $user_account = $distributor_shop_info['user_name'];  //分销商店铺用户user_name
        //供货商（卖家）数据
        $supplier_goodsbaseinfo = $Goods_BaseModel->getGoodsDetailInfoByGoodId($goodsbaseinfo['goods_base']['goods_parent_id']);
        $supplier_goodsbase = $Goods_BaseModel->getGoodsInfo($goodsbaseinfo['goods_base']['goods_parent_id']);

        $supplier_shop_info = $Shop_BaseModel->getOne($supplier_goodsbaseinfo['goods_base']['shop_id']);
        $shop_id = $supplier_shop_info['shop_id'];  //供货商店铺id
        //获取供货商给该分销商设置的折扣
        $shopDistributorModel = new Distribution_ShopDistributorModel();
        $shopDistributorLevelModel = new Distribution_ShopDistributorLevelModel();
        $shopDistributorInfo = $shopDistributorModel->getOneByWhere(array('shop_id' => $supplier_shop_info['shop_id'], 'distributor_id' => $distributor_shop_info['shop_id'], 'distributor_enable' => 1));
        $distritutor_rate_info = $shopDistributorLevelModel->getOne($shopDistributorInfo['distributor_level_id']);
        //查找收货地址,计算运费
        $User_AddressModel = new User_AddressModel();
        $Transport_TemplateModel = new Transport_TemplateModel();
        $city_id = 0;
        if ($address_id) {
            $user_address = $User_AddressModel->getOne($address_id);
            $city_id = $user_address['user_address_city_id'];
        }
        $orderInfo = array(
            'shop_id' => $supplier_shop_info['shop_id'],
            'count' => $goods_num,
            'weight' => $supplier_goodsbaseinfo['common_base']['common_cubage'] * $goods_num,
            'price' => $supplier_goodsbaseinfo['goods_base']['goods_price']
        );
        $costInfo = $Transport_TemplateModel->shopTransportCost($city_id, $orderInfo);
        $cost = $costInfo['cost'] ? $costInfo['cost'] : 0;
        //商品价格：供应商的进货价-分销商等级优惠+供应商设置的物流费用
        if ($distritutor_rate_info['distributor_leve_discount_rate'] > 0 && $distritutor_rate_info['distributor_leve_discount_rate'] < 100) {
            $shop_rate = number_format(($supplier_goodsbaseinfo['goods_base']['goods_price'] * (100 - $distritutor_rate_info['distributor_leve_discount_rate']) * $goods_num / 100), 2, '.', '');
        } else {
            $shop_rate = 0;
        }
        $goods_price = $supplier_goodsbaseinfo['goods_base']['goods_price'] * $goods_num - $shop_rate;
        $total_price = $goods_price + $cost;
        //计算商品单件实际支付金额（order_goods_payment_amount）
        $order_goods_payment_amount = number_format(($goods_price / $goods_num), 2, '.', '');
        //获取分类佣金
        $Goods_CatModel = new Goods_CatModel();
        $cat_base = $Goods_CatModel->getOne($supplier_goodsbaseinfo['common_base']['cat_id']);
        if ($cat_base) {
            $cat_commission = $cat_base['cat_commission'];
        } else {
            $cat_commission = 0;
        }
        //后台开启供应商佣金则需要收取供应商的商品佣金
        if (Web_ConfigModel::value('supplier_commission')) {
            $commission_fee = number_format(($goods_price * $cat_commission / 100), 2, '.', '');
        } else {
            $commission_fee = 0;
        }
        $Number_SeqModel = new Number_SeqModel();
        $Order_GoodsModel = new Order_GoodsModel();
        $Goods_BaseModel = new Goods_BaseModel();
        $PaymentChannlModel = new PaymentChannlModel();
        $Order_GoodsSnapshot = new Order_GoodsSnapshot();
        //合并支付订单的价格
        $uprice = 0;
        $inorder = '';
        $utrade_title = '';    //商品名称 - 标题
        $trade_title = '';
        //生成店铺订单
        $prefix = sprintf('%s-%s-', Yf_Registry::get('shop_app_id'), date('YmdHis'));
        $order_number = $Number_SeqModel->createSeq($prefix);
        $order_id = sprintf('%s-%s-%s-%s', 'SP', $supplier_shop_info['user_id'], $shop_id, $order_number);
        $Order_InvoiceModel = new Order_InvoiceModel();
        $order_invoice_id = $Order_InvoiceModel->getOrderInvoiceId($invoice_id, $invoice_title, $invoice_content, $order_id, true);
        $order_row = array();
        $order_row['order_id'] = $order_id;
        $order_row['shop_id'] = $shop_id;
        $order_row['shop_name'] = $supplier_shop_info['shop_name'];
        $order_row['buyer_user_id'] = $user_id;
        $order_row['buyer_user_name'] = $user_account;
        $order_row['seller_user_id'] = $supplier_shop_info['user_id'];
        $order_row['seller_user_name'] = $supplier_shop_info['user_name'];
        $order_row['order_date'] = date('Y-m-d');
        $order_row['order_create_time'] = get_date_time();
        $order_row['order_receiver_name'] = $receiver_name;
        $order_row['order_receiver_address'] = $receiver_address;
        $order_row['order_receiver_contact'] = $receiver_phone;
        $order_row['order_goods_amount'] = $goods_price; //订单商品总价（不包含运费）
        $order_row['order_payment_amount'] = $total_price;// 订单实际支付金额 = 商品实际支付金额 + 运费
        $order_row['order_discount_fee'] = 0;   //优惠价格 = 商品总价 - 商品实际支付金额
        $order_row['order_point_fee'] = 0;    //买家使用积分
        $order_row['order_shipping_fee'] = $cost;
        $order_row['order_status'] = $order_status;
        $order_row['order_points_add'] = 0;    //订单赠送的积分
        $order_row['order_commission_fee'] = $commission_fee;  //分类佣金
        $order_row['order_source_id'] = $p_order_id;    // 进货订单对应的买家订单
        $order_row['order_is_virtual'] = 0;    //1-虚拟订单 0-实物订单
        $order_row['payment_id'] = $pay_way_id;
        $order_row['payment_name'] = $PaymentChannlModel->payWay[$pay_way_id];
        $order_row['directseller_discount'] = $shop_rate;
        $order_row['order_invoice'] = $invoice;
        $order_row['order_invoice_id'] = $order_invoice_id;
        $order_row['order_distribution_seller_type'] = 2;//分销代销转发销售(P, SP)
        $flag = $Order_BaseModel->addBase($order_row);
        $order_goods_row = array();
        $order_goods_row['order_id'] = $order_id;
        $order_goods_row['goods_id'] = $supplier_goodsbaseinfo['goods_base']['goods_id'];
        $order_goods_row['common_id'] = $supplier_goodsbaseinfo['goods_base']['common_id'];
        $order_goods_row['buyer_user_id'] = $user_id;
        $order_goods_row['goods_name'] = $supplier_goodsbaseinfo['goods_base']['goods_name'];
        $order_goods_row['goods_class_id'] = $supplier_goodsbaseinfo['goods_base']['cat_id'];
        $order_goods_row['order_spec_info'] = $supplier_goodsbase['goods_base']['spec'];
        $order_goods_row['goods_price'] = $supplier_goodsbaseinfo['goods_base']['goods_price']; //商品原来的单价
        $order_goods_row['order_goods_payment_amount'] = $order_goods_payment_amount;  //商品实际支付单价
        $order_goods_row['order_goods_num'] = $goods_num;
        $order_goods_row['goods_image'] = $supplier_goodsbaseinfo['goods_base']['goods_image'];
        $order_goods_row['order_goods_amount'] = $goods_price;  //商品实际支付金额
        $order_goods_row['order_goods_discount_fee'] = 0;        //优惠价格
        $order_goods_row['order_goods_adjust_fee'] = 0;    //手工调整金额
        $order_goods_row['order_goods_point_fee'] = 0;    //积分费用
        $order_goods_row['order_goods_commission'] = $commission_fee;    //商品佣金(总)
        $order_goods_row['shop_id'] = $supplier_goodsbaseinfo['goods_base']['shop_id'];
        $order_goods_row['order_goods_status'] = Order_StateModel::ORDER_WAIT_PAY;
        $order_goods_row['order_goods_evaluation_status'] = 0;  //0未评价 1已评价
        $order_goods_row['order_goods_benefit'] = 0;
        $order_goods_row['order_goods_time'] = get_date_time();
        $order_goods_row['directseller_goods_discount'] = $shop_rate;
        fb($order_goods_row);
        $flag1 = $Order_GoodsModel->addGoods($order_goods_row);
        //加入交易快照表
        $order_goods_snapshot_add_row = array();
        $order_goods_snapshot_add_row['order_id'] = $order_id;
        $order_goods_snapshot_add_row['user_id'] = $user_id;
        $order_goods_snapshot_add_row['shop_id'] = $supplier_goodsbaseinfo['goods_base']['shop_id'];
        $order_goods_snapshot_add_row['common_id'] = $supplier_goodsbaseinfo['goods_base']['common_id'];
        $order_goods_snapshot_add_row['goods_id'] = $supplier_goodsbaseinfo['goods_base']['goods_id'];
        $order_goods_snapshot_add_row['goods_name'] = $supplier_goodsbaseinfo['goods_base']['goods_name'];
        $order_goods_snapshot_add_row['goods_image'] = $supplier_goodsbaseinfo['goods_base']['goods_image'];
        $order_goods_snapshot_add_row['goods_price'] = $supplier_goodsbaseinfo['goods_base']['goods_price'];
        $order_goods_snapshot_add_row['freight'] = $cost;   //运费
        $order_goods_snapshot_add_row['snapshot_create_time'] = get_date_time();
        $order_goods_snapshot_add_row['snapshot_uptime'] = get_date_time();
        $order_goods_snapshot_add_row['snapshot_detail'] = 0;
        $Order_GoodsSnapshot->addSnapshot($order_goods_snapshot_add_row);
        /*fb("====order_goods====");
            fb($flag2);*/
        $flag = $flag && $flag1;
        //删除商品库存
        $flag2 = $Goods_BaseModel->delStock($supplier_goodsbaseinfo['goods_base']['goods_id'], $goods_num);
        $trade_title = $supplier_goodsbaseinfo['goods_base']['goods_name'];
        //支付中心生成订单
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        $formvars['consume_trade_id'] = $order_row['order_id'];
        $formvars['order_id'] = $order_row['order_id'];
        $formvars['buy_id'] = $user_id;
        $formvars['buyer_name'] = $user_account;
        $formvars['seller_id'] = $order_row['seller_user_id'];
        $formvars['seller_name'] = $order_row['seller_user_name'];
        $formvars['order_state_id'] = $order_row['order_status'];
        $formvars['order_payment_amount'] = $order_row['order_payment_amount'];
        $formvars['order_commission_fee'] = $commission_fee;
        $formvars['trade_remark'] = '采购单';
        $formvars['trade_create_time'] = $order_row['order_create_time'];
        $formvars['trade_title'] = $trade_title;        //商品名称 - 标题
        fb($formvars);
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addConsumeTrade&typ=json', $url), $formvars);
        fb("合并支付返回的结果");
        //将合并支付单号插入数据库
        if ($rs['status'] == 200) {
            $Order_BaseModel->editBase($order_id, array('payment_number' => $rs['data']['union_order']));
            $flag = $flag && true;
        } else {
            $flag = $flag && false;
        }
        $uprice = $order_row['order_payment_amount'];
        $inorder = $order_id;
        $utrade_title = $trade_title;
        //生成合并支付订单
        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('paycenter_api_url');
        $shop_app_id = Yf_Registry::get('shop_app_id');
        $formvars = array();
        $formvars['inorder'] = $inorder;
        $formvars['uprice'] = $uprice;
        $formvars['buyer'] = $user_id;
        $formvars['trade_title'] = $utrade_title;
        $formvars['buyer_name'] = $user_account;
        $formvars['app_id'] = $shop_app_id;
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        fb($formvars);
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=addUnionOrder&typ=json', $url), $formvars);
        fb($rs);
        if ($rs['status'] == 200) {
            $uorder = $rs['data']['uorder'];
            $flag = $flag && true;
        } else {
            $uorder = '';
            $flag = $flag && false;
        }
        if ($flag) {
            $status = 200;
            $msg = __('success');
            $data = array('uorder' => $uorder);
        } else {
            //订单提交失败，将paycenter中生成的订单删除
            if ($uorder) {
                $key = Yf_Registry::get('shop_api_key');
                $url = Yf_Registry::get('paycenter_api_url');
                $shop_app_id = Yf_Registry::get('shop_app_id');
                $formvars = array();
                $formvars['uorder'] = $uorder;
                $formvars['app_id'] = $shop_app_id;
                $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Pay_Pay&met=delUnionOrder&typ=json', $url), $formvars);
            }
        }
        return $flag;
    }

    /**
     * 一件代发商品获取分销商发票信息
     *
     * @param $shop_info  店铺信息
     *
     * @return array
     */
    private function getShopInvoice($shop_info)
    {
        //获取分销商店铺发票信息
        $invoice_data = array();
        $shop_invoice_state = new ShopInvoice();
        $shop_invoice_model = new ShopInvoiceModel();
        $shop_invoice = current($shop_invoice_model->getByWhere(array('shop_id' => $shop_info['shop_id'], 'is_use' => 2)));

        //没有启用的发票信息
        if (empty($shop_invoice)) {
            $invoice = "不开发票";
            $shop_invoice['invoice_id'] = '';
        } else {
            //增值税专用发票
            if ($shop_invoice['invoice_state'] == 3) {
                $invoice = $shop_invoice_state->invoice_state[3] . ' ' . $shop_invoice['invoice_company'] . ' ' . $shop_invoice['invoice_content'];
            } elseif ($shop_invoice['invoice_state'] == 2) {
                $invoice = $shop_invoice_state->invoice_state[2] . ' ' . $shop_invoice['invoice_title'] . ' ' . $shop_invoice['invoice_content'];
            } else {
                $invoice = $shop_invoice_state->invoice_state[1] . ' ' . $shop_invoice['invoice_title'] . ' ' . $shop_invoice['invoice_content'];;
            }
        }
        $invoice_data['invoice'] = $invoice;
        $invoice_data['invoice_id'] = $shop_invoice['invoice_id'];

        return $invoice_data;
    }
}

?>
