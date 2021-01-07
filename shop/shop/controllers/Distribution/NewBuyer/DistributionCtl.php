<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Distribution_NewBuyer_DistributionCtl extends Buyer_Controller
{
    public $directseller_model = null;

    /**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     * @access public
     */
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->directseller_model = new Distribution_ShopDirectsellerModel();
        $this->order_GoodsModel = new Order_GoodsModel();
        $this->order_BaseModel = new Order_BaseModel();
        $this->User_InfoModel = new User_InfoModel();
        //礼包商品的处理
        $userId = Perm::$userId;
        $directseller_ids = array();
        array_push($directseller_ids, Perm::$userId);
        $condition = array();
        //当前用户的上级
        $info = $this->User_InfoModel->getByWhere(array('user_parent_id' => $userId));
        $ids = array_column($info, 'user_id');
        if ($ids) {
            $directseller_ids = array_merge_recursive($directseller_ids, $ids);//上上级
        }

//        $condition['directseller_id'] = Perm::$userId;
        $condition['directseller_id:IN'] = $directseller_ids;//上级、上上级
        $condition['identity_type'] = 1;
        $order_goods_info = $this->order_GoodsModel->getByWhere($condition);
        $order_ids = array_column($order_goods_info,'order_id');
        //条件
        $cond_row['order_id:IN'] = $order_ids;
        $cond_row['order_status:>='] = Order_StateModel::ORDER_PAYED;
        $cond_row['order_status:<='] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
        //排序
        $order_row = array('order_create_time'=>ASC);
        $order_base_info = $this->order_BaseModel->getByWhere($cond_row,$order_row);
        $order_goods = array();
        $today_num = 0;
        if (!empty($order_base_info)) {
           $order_base_info = current($order_base_info);
           $order_goods = $this->order_GoodsModel->getOneByWhere(array('order_id'=>$order_base_info['order_id']));
           if(strtotime($order_goods['order_goods_time']) > strtotime(date('Y-m-d',time()))){
             $today_num = 1;
           }

        }
       $this->today_num = $today_num;
       $this->order_Goods = $order_goods;
    }

    /*
    * 分销中心-WAP端
    */
    public function wapIndex1()
    {
        $userId = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $data = $User_InfoModel->getOne($userId);

        $member_all1=array();
        $member_direct1=array();
        $member_indirect1=array();
        $member_all2=array();
        $member_direct2=array();
        $member_indirect2=array();
        //获取用户下级会员---全部
        $row1['user_parent_id'] = Perm::$userId;
        $member_direct_list1 =$User_InfoModel->getByWhere($row1);
        //直接会员ID
        $member_direct1=array_column($member_direct_list1,'user_id');
        //间接会员ID
        foreach ($member_direct1 as $key => $value) {
            $row1['user_parent_id']=$value;
            $member_indirect_list1=$User_InfoModel->getByWhere($row1);
            $member_indirect1=array_merge($member_indirect1,array_column($member_indirect_list1,'user_id'));
        }
        //全部会员ID
        $member_all1=array_merge($member_direct1,$member_indirect1);
        $data['invitors']=count($member_all1);

        //获取用户邀请的直属会员---当日
        $row2['user_parent_id'] = Perm::$userId;
        $row2['user_regtime:<='] = get_date_time();
        $beginDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d',time())));
        $row2['user_regtime:>='] = $beginDate;
        $member_direct_list2 = $User_InfoModel->getByWhere($row2);
        //直接会员ID
        $member_direct2 = array_column($member_direct_list2,'user_id');
        //间接会员ID
        foreach ($member_direct2 as $k => $val) {
            $row2['user_parent_id'] = $val;
            $member_indirect_list2 = $User_InfoModel->getByWhere($row2);
            $member_indirect2 = array_merge($member_indirect2,array_column($member_indirect_list2,'user_id'));
        }
        //全部会员ID
        $member_all2 = array_merge($member_direct2,$member_indirect2);
        $data['day_invitors'] = count($member_all2);
        //用户推广商品的数量
        $Order_GoodsModel = new Order_GoodsModel();
        $goods_con1['directseller_id'] = $userId;  
        $goods_con1['directseller_flag'] = 1;

        $goods_list1 = $Order_GoodsModel->getByWhere($goods_con1);
        $goods_sql2 = "SELECT a.`common_id` FROM `yf_order_goods` a LEFT OUTER JOIN `yf_order_base` b  ON a.`order_id` = b.`order_id` WHERE b.`directseller_p_id` = '{$userId}' AND a. `directseller_flag` = 1 ";
        $goods_list2 = $Order_GoodsModel->sql->getAll($goods_sql2);
        $goods_list1 = empty($goods_list1)?array():array_column($goods_list1,'common_id');
        $goods_list2 = empty($goods_list2)?array():array_column($goods_list2,'common_id');
        $data['goods_num'] = count(array_unique(array_merge($goods_list1,$goods_list2)));
        if (!empty($this->order_Goods)) {
            $data['goods_num'] += 1;
        }
        //用户推广商品的数量--当日
        $goods_con1['order_goods_time:>'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d',time())));
        $time_day = date('Y-m-d H:i:s', strtotime(date('Y-m-d',time())));
        $goods_list_day1 = $Order_GoodsModel->getByWhere($goods_con1);
        $goods_sql_day2 = "SELECT a.`common_id` FROM `yf_order_goods` a LEFT OUTER JOIN `yf_order_base` b  ON a.`order_id` = b.`order_id` WHERE b.`directseller_p_id` = '{$userId}' AND a. `directseller_flag` = 1 AND order_goods_time>'{$time_day}'";
        $goods_list_day2 = $Order_GoodsModel->sql->getAll($goods_sql_day2);
        //礼包商品只统计一次
        $goods_list_day1 = empty($goods_list_day1)?array():array_column($goods_list_day1,'common_id');
        $goods_list_day2 = empty($goods_list_day2)?array():array_column($goods_list_day2,'common_id');
        $data['day_goods_num']=count(array_unique(array_merge($goods_list_day1,$goods_list_day2)));
        if ($this->today_num == 1) {
            $data['day_goods_num'] += 1;
        }
            
        //用户推广订单
        $Order_BaseModel = new Order_BaseModel();
        $order_con['directseller_id'] = $userId;
        $order_con['directseller_flag'] = 1;
        $order_con1['directseller_p_id'] = $userId;
        $order_con1['directseller_flag'] = 1;
        $num1 = $Order_BaseModel->getPromotionOrderNum($order_con);
        $num2 = $Order_BaseModel->getPromotionOrderNum($order_con1);

        $data['promotion_order_nums'] = $num1 + $num2;
        //加上礼包商品 礼包商品只统计一次
        if (!empty($this->order_Goods)) {
            $data['promotion_order_nums'] += 1;
        }

        //用户推广订单--当日
        $order_con['order_date:>='] = date('Y-m-d H:i:s', strtotime(date('Y-m-d',time())));
        $order_con1['order_date:>='] = date('Y-m-d H:i:s', strtotime(date('Y-m-d',time())));
        $nums1 = $Order_BaseModel->getPromotionOrderNum($order_con);
        $nums2 = $Order_BaseModel->getPromotionOrderNum($order_con1);
        $data['day_order_nums'] = $nums1 + $nums2;
        if ($this->today_num ==1) {
            $data['day_order_nums'] += 1;
        }
        //用户今日预估收益  今日预估收益中的数据应该只有已付款的数据且金额一致
        $data['income_tatol'] = 0;
        $income_con['directseller_flag'] = 1;
        $income_con['order_date:>='] = date('Y-m-d H:i:s', strtotime(date('Y-m-d',time())));
        //$income_con['order_status'] = Order_StateModel::ORDER_PAYED;
        $income_con['order_status:>='] = Order_StateModel::ORDER_PAYED;
        $income_con['order_status:<'] = Order_StateModel::ORDER_CANCEL;
        $income_order=$Order_BaseModel->getByWhere($income_con);
        if($income_order){
            foreach ($income_order as $key => $value) {
                $income_order_list = $Order_GoodsModel->getByWhere(array('directseller_flag'=>1,'order_id'=>$value['order_id']));
                if($income_order_list){
                    foreach ($income_order_list as $k => $val) {
                        if($userId==$value['directseller_id']){
                            $data['income_tatol']+=$val['directseller_commission_0']-$val['directseller_commission_0_refund'];
                        }elseif($userId==$value['directseller_p_id']){
                            $data['income_tatol']+=$val['directseller_commission_1']-$val['directseller_commission_1_refund'];
                        }
                    }
                }               
            }
        }
        //加上礼包商品的返佣
        $gift_info = $this->getTodayOrder(2,'');
        $gift_num = count($gift_info);
        if ($gift_num) {
            if ($this->order_Goods['directseller_id'] == $userId) {
                $data['income_tatol'] += Web_ConfigModel::value("direct_reward") * $gift_num;
            } elseif ($this->order_Goods['directseller_p_id'] == $userId) {
                $data['income_tatol'] += Web_ConfigModel::value("indirect_reward") * $gift_num;
            }
        }

        //用户今日结算佣金
        $SettlementIncome = new SettlementIncome();
        $settlement_con['user_id'] = $userId;
        $settlement_con['settlement_time:>'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d',time())));
        $settlement_list = $SettlementIncome->getByWhere($settlement_con);
        $data['settlement_income'] = array_sum(array_column($settlement_list,'settlement_amount'));

        if ($this->typ == "json") {
            $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }
    }

    /*
    * 分销中心-WAP端
    */
    public function wapIndex()
    {
        //礼包商品
        $gift_info = $this->getTodayOrder(2, '');
        $goods_num_today = count(array_unique(array_column($gift_info, 'goods_id')));
        $gift_num_today = count($gift_info);

        //礼包商品累计
        $gift_info_sum = $this->getTodayOrder(0, '');
        $goods_num_sum = count(array_unique(array_column($gift_info_sum, 'goods_id')));
        $gift_num_sum = count($gift_info_sum);

        $userId = Perm::$userId;
        $User_InfoModel = new User_InfoModel();
        $data = $User_InfoModel->getOne($userId);

        $member_all1 = array();
        $member_direct1 = array();
        $member_indirect1 = array();
        $member_all2 = array();
        $member_direct2 = array();
        $member_indirect2 = array();
        //获取用户下级会员---全部
        $row1['user_parent_id'] = Perm::$userId;
        $member_direct_list1 = $User_InfoModel->getByWhere($row1);
        //直接会员ID
        $member_direct1 = array_column($member_direct_list1, 'user_id');
        //间接会员ID
        foreach ($member_direct1 as $key => $value) {
            $row1['user_parent_id'] = $value;
            $member_indirect_list1 = $User_InfoModel->getByWhere($row1);
            $member_indirect1 = array_merge($member_indirect1, array_column($member_indirect_list1, 'user_id'));
        }
        //全部会员ID
        $member_all1 = array_merge($member_direct1, $member_indirect1);
        $data['invitors'] = count($member_all1);

        //获取用户邀请的直属会员---当日
        $row2['user_parent_id'] = Perm::$userId;
        $row2['user_regtime:<='] = get_date_time();
        $beginDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d', time())));
        $row2['user_regtime:>='] = $beginDate;
        $member_direct_list2 = $User_InfoModel->getByWhere($row2);
        //直接会员ID
        $member_direct2 = array_column($member_direct_list2, 'user_id');
        //间接会员ID
        foreach ($member_direct2 as $k => $val) {
            $row2['user_parent_id'] = $val;
            $member_indirect_list2 = $User_InfoModel->getByWhere($row2);
            $member_indirect2 = array_merge($member_indirect2, array_column($member_indirect_list2, 'user_id'));
        }
        //全部会员ID
        $member_all2 = array_merge($member_direct2, $member_indirect2);
        $data['day_invitors'] = count($member_all2);
        //用户推广商品的数量
        $Order_GoodsModel = new Order_GoodsModel();
        $goods_con1['directseller_id'] = $userId;
        $goods_con1['directseller_flag'] = 1;

        $goods_list1 = $Order_GoodsModel->getByWhere($goods_con1);
        $goods_sql2 = "SELECT a.`common_id` FROM `yf_order_goods` a LEFT OUTER JOIN `yf_order_base` b  ON a.`order_id` = b.`order_id` WHERE b.`directseller_p_id` = '{$userId}' AND a. `directseller_flag` = 1 ";
        $goods_list2 = $Order_GoodsModel->sql->getAll($goods_sql2);
        $goods_list1 = empty($goods_list1) ? array() : array_column($goods_list1, 'common_id');
        $goods_list2 = empty($goods_list2) ? array() : array_column($goods_list2, 'common_id');
        $data['goods_num'] = count(array_unique(array_merge($goods_list1, $goods_list2)));
        if ($goods_num_sum) {
            $data['goods_num'] += $goods_num_sum;
        }
        // if (!empty($this->order_Goods)) {
        //     $data['goods_num'] += 1;
        // }
        //用户推广商品的数量--当日
        $goods_con1['order_goods_time:>'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', time())));
        $time_day = date('Y-m-d H:i:s', strtotime(date('Y-m-d', time())));
        $goods_list_day1 = $Order_GoodsModel->getByWhere($goods_con1);
        $goods_sql_day2 = "SELECT a.`common_id` FROM `yf_order_goods` a LEFT OUTER JOIN `yf_order_base` b  ON a.`order_id` = b.`order_id` WHERE b.`directseller_p_id` = '{$userId}' AND a. `directseller_flag` = 1 AND order_goods_time>'{$time_day}'";
        $goods_list_day2 = $Order_GoodsModel->sql->getAll($goods_sql_day2);
        //礼包商品只统计一次
        $goods_list_day1 = empty($goods_list_day1) ? array() : array_column($goods_list_day1, 'common_id');
        $goods_list_day2 = empty($goods_list_day2) ? array() : array_column($goods_list_day2, 'common_id');
        $data['day_goods_num'] = count(array_unique(array_merge($goods_list_day1, $goods_list_day2)));
        if ($goods_num_today) {
            $data['day_goods_num'] += $goods_num_today;
        }


        //用户推广订单
        $Order_BaseModel = new Order_BaseModel();
        $order_con['directseller_id'] = $userId;
        $order_con['directseller_flag'] = 1;
        $order_con1['directseller_p_id'] = $userId;
        $order_con1['directseller_flag'] = 1;
        $num1 = $Order_BaseModel->getPromotionOrderNum($order_con);
        $num2 = $Order_BaseModel->getPromotionOrderNum($order_con1);

        $data['promotion_order_nums'] = $num1 + $num2;
        //加上礼包商品 礼包商品  每个用户只统计一次
        if ($gift_num_sum) {
            $data['promotion_order_nums'] += $gift_num_sum;
        }

        //用户推广订单--当日
        $order_con['order_date:>='] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', time())));
        $order_con1['order_date:>='] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', time())));
        $nums1 = $Order_BaseModel->getPromotionOrderNum($order_con);
        $nums2 = $Order_BaseModel->getPromotionOrderNum($order_con1);

        $data['day_order_nums'] = $nums1 + $nums2;
        //礼包数量
        if ($gift_num_today) {
            $data['day_order_nums'] += $gift_num_today;
        }
        //用户今日预估收益  今日预估收益中的数据应该只有已付款的数据且金额一致
        $data['income_tatol'] = 0;
        $income_con['directseller_flag'] = 1;
        $income_con['order_date:>='] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', time())));
        //$income_con['order_status'] = Order_StateModel::ORDER_PAYED;
        $income_con['order_status:>='] = Order_StateModel::ORDER_PAYED;
        $income_con['order_status:<'] = Order_StateModel::ORDER_CANCEL;
        $income_order = $Order_BaseModel->getByWhere($income_con);
        if ($income_order) {
            foreach ($income_order as $key => $value) {
                $income_order_list = $Order_GoodsModel->getByWhere(array('directseller_flag' => 1, 'order_id' => $value['order_id']));
                if ($income_order_list) {
                    foreach ($income_order_list as $k => $val) {
                        if ($userId == $value['directseller_id']) {
                            $data['income_tatol'] += $val['directseller_commission_0'] - $val['directseller_commission_0_refund'];
                        } elseif ($userId == $value['directseller_p_id']) {
                            $data['income_tatol'] += $val['directseller_commission_1'] - $val['directseller_commission_1_refund'];
                        }
                    }
                }
            }
        }

        if ($gift_num_today) {
            foreach($gift_info as $k=>$v){
                if ($v['directseller_id'] == $userId) {
                    $data['income_tatol'] += Web_ConfigModel::value("direct_reward");
                } elseif ($v['directseller_p_id'] == $userId) {
                    $data['income_tatol'] += Web_ConfigModel::value("indirect_reward");
                }
            }
        }


        //用户今日结算佣金
        $SettlementIncome = new SettlementIncome();
        $settlement_con['user_id'] = $userId;
        $settlement_con['income_type'] = 1;//普通商品
        $settlement_con['settlement_time:>'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', time())));
        $settlement_list = $SettlementIncome->getByWhere($settlement_con);

        //礼包
        $gift_info = $this->getTodayOrder(2, '');
        $order_ids = array_column($gift_info, 'order_id');
        $settlement_con['settlement_order_id:IN'] = $order_ids;
        unset($settlement_con['income_type']);
        $settlement_list2 = $SettlementIncome->getByWhere($settlement_con);
        $settlement_list = array_merge($settlement_list, $settlement_list2);

        $data['settlement_income'] = array_sum(array_column($settlement_list, 'settlement_amount'));
        if ($this->typ == "json") {
            $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }
    }

    //上级用户信息
    public function getParentInfo()
    {
        $data = array();
        $parent_id = request_int('parent_id');
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($parent_id);

        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('ucenter_api_url');
        $formvars = array();
        $formvars['user_id'] = $parent_id;
        $formvars['app_id'] = Yf_Registry::get('shop_app_id');
        $formvars['from_app_id'] = Yf_Registry::get('shop_app_id');
        $rs = get_url_with_encrypt($key, sprintf('%s?ctl=Index&met=getBindInfo&typ=json', $url), $formvars);

        $data['wx_name'] = $rs['data']['bind_nickname'];
        $data['user_name'] = $user_info['user_name'];
        $data['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id=' . $parent_id;
        $this->data->addBody(-140, $data);
    }

    /*
    * 分销佣金明细
    */
    public function directsellerCommission()
    {   
        $userId = Perm::$userId;
        if (request_string('orderkey')) {
            $cond_row['settlement_order_id:LIKE'] = '%' . request_string('orderkey') . '%';
        }
        if(request_int('status')){
            $cond_row['settlement_level'] = request_int('status');
        }
        if (request_string('ctime')) {
            switch (request_string('ctime')) {
                case 's'://7天
                    $cond_row['settlement_time:>='] = date('Y-m-d 00:00:00', strtotime('-7 day'));
                    $cond_row['settlement_time:<='] = date('Y-m-d H:i:s', time());
                    break;
                case 'o'://1个月
                    $cond_row['settlement_time:>='] = date('Y-m-d 00:00:00', strtotime('-1 month'));
                    $cond_row['settlement_time:<='] = date('Y-m-d H:i:s', time());
                    break;
                case 't'://3个月
                    $cond_row['settlement_time:>='] = date('Y-m-d 00:00:00', strtotime('-3 month'));
                    $cond_row['settlement_time:<='] = date('Y-m-d H:i:s', time());
                    break;
                case 'y'://1年
                    $cond_row['settlement_time:>='] = date('Y-m-d 00:00:00', strtotime('-1 year'));
                    $cond_row['settlement_time:<='] = date('Y-m-d H:i:s', time());
                    break;
                default:
                    break;
            }
        } else {
            $cond_row['settlement_time:>='] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', time())));
        }
//        $cond_row['settlement_time:>='] = date('Y-m-d H:i:s', strtotime(date('Y-m-d',time())));
        $cond_row['user_id'] = $userId;
        $SettlementIncome = new SettlementIncome();
        $list = $SettlementIncome->getByWhere($cond_row);

        foreach ($list as $k => $v) {
            $list[$k]['time'] = date('Y-m-d H:i', strtotime($v['settlement_time']));
        }
        $this->data->addBody(-140, array_values($list));
    }
    /*
    * 分销用户下级数量
    */
    public function distributionNum(){
        $member_all=array();
        $member_direct=array();
        $member_indirect=array();
        $User_InfoModel = new User_InfoModel();
        $section = request_int('section'); // 1：今日推广 0：累计推广
        // 今日时间起始点
        $condition = array('user_parent_id'=>Perm::$userId);
        if ($section == 1) {
            $beginTime = date('Y-m-d 00:00:00');
            $endTime = date('Y-m-d 23:59:59');
            $condition['user_regtime:>='] = $beginTime;
            $condition['user_regtime:<='] = $endTime;
        }
        $member_direct_list =$User_InfoModel->getByWhere($condition);
        //直接会员ID
        $member_direct=array_column($member_direct_list,'user_id');
        $data['direct_num']=count($member_direct);
        //间接会员ID
        foreach ($member_direct as $key => $value) {
            $condition['user_parent_id']=$value;
            $member_indirect_list=$User_InfoModel->getByWhere($condition);
            $member_indirect=array_merge($member_indirect,array_column($member_indirect_list,'user_id'));
        }
        $data['indirect_num']=count($member_indirect);
        //全部会员ID
        $member_all=array_merge($member_direct,$member_indirect);
        $data['all_num']=count($member_all);
        $this->data->addBody(-140, $data);
    }

    /*
    * 分销用户下级列表
    */
    public function directsellerList()
    {   
        $member_all=array();
        $member_direct=array();
        $member_indirect=array();
        $User_InfoModel = new User_InfoModel();
        $userList=$User_InfoModel->getOne(Perm::$userId);
        $section = request_int('section'); // 1：今日推广 0：累计推广
        // 今日时间起始点
        $condition = array('user_parent_id'=>Perm::$userId);
        if ($section == 1) {
            $beginTime = date('Y-m-d 00:00:00');
            $endTime = date('Y-m-d 23:59:59');
            $condition['user_regtime:>='] = $beginTime;
            $condition['user_regtime:<='] = $endTime;
        }
        $member_direct_list =$User_InfoModel->getByWhere($condition);
        //直接会员ID
        $member_direct=array_column($member_direct_list,'user_id');
        //间接会员ID
        foreach ($member_direct as $key => $value) {
            $condition['user_parent_id'] = $value;
            $member_indirect_list=$User_InfoModel->getByWhere($condition);
            $member_indirect=array_merge($member_indirect,array_column($member_indirect_list,'user_id'));
        }
        //全部会员ID
        $member_all=array_merge($member_direct,$member_indirect);
       
        $status = request_string('genre')?request_string('genre'):"all";
        switch ($status) {
            case "direct" :
                $cond_row['user_id:IN'] = $member_direct;
                $level=0;
                break;
            case "indirect" :
                $cond_row['user_id:IN'] = $member_indirect;
                $level=1;
                break;
            default:
                $cond_row['user_id:IN'] = $member_all;
                break;
        }
        $order_row['user_regtime'] = 'DESC';

        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        if($status=='all'){
            if (request_string('orderkey')) {
                $cond_row1['user_name:LIKE'] = '%' . request_string('orderkey') . '%';
                $cond_row2['user_name:LIKE'] = '%' . request_string('orderkey') . '%';
            }
            $cond_row1['user_id:IN']=$member_direct;
            $cond_row2['user_id:IN']=$member_indirect;
            $data1=$this->directseller_model->getInvitors($cond_row1, $order_row, $page, $rows,0);
            $data2=$this->directseller_model->getInvitors($cond_row2, $order_row, $page, $rows,1);
            $data['page']=1;
            $data['total']=1;
            $data['totalsize']=$data1['totalsize']+$data2['totalsize'];
            $data['records']=$data1['records']+$data2['records'];
            $data['items']=array_merge($data1['items'],$data2['items']);
        }else{
            $data = $this->directseller_model->getInvitors($cond_row, $order_row, $page, $rows,$level);
        }
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();

        if ($this->typ == "json") {
            $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }
    }





    /*
         *  用户推广订单
         */
    public function directsellerOrder()
    {
        $User_InfoModel = new User_InfoModel();
        $userList = $User_InfoModel->getOne(Perm::$userId);

        $status = request_string('status');
        $section = request_int('section', 0); // 0.全部推广订单 1.今日推广订单2,今日预估收益
        if (request_string('orderkey')) {
            $cond_row1['order_id:LIKE'] = '%' . request_string('orderkey') . '%';
            $cond_row2['order_id:LIKE'] = '%' . request_string('orderkey') . '%';
        }

        if ($section == 1) {
            $beginTime = date('Y-m-d 00:00:00');
            $endTime = date('Y-m-d 23:59:59');
            $cond_row1['order_create_time:>='] = $beginTime;
            $cond_row1['order_create_time:<='] = $endTime;
            $cond_row2['order_create_time:>='] = $beginTime;
            $cond_row2['order_create_time:<='] = $endTime;

        }
        if ($section == 2) {
            $beginTime = date('Y-m-d 00:00:00');
            $endTime = date('Y-m-d 23:59:59');
            $cond_row1['order_create_time:>='] = $beginTime;
            $cond_row1['order_create_time:<='] = $endTime;
            $cond_row2['order_create_time:>='] = $beginTime;
            $cond_row2['order_create_time:<='] = $endTime;


        }

        if (request_string('start_date')) {
            $cond_row1['order_create_time:>'] = request_string('start_date');
            $cond_row2['order_create_time:>'] = request_string('start_date');
        }
        if (request_string('end_date')) {
            $cond_row1['order_create_time:<'] = request_string('end_date');
            $cond_row2['order_create_time:<'] = request_string('end_date');
        }

        //待付款
         if ($status == 'wait') {
             $cond_row1['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
             $cond_row2['order_status'] = Order_StateModel::ORDER_WAIT_PAY;
         }
        //已付款
        if ($status == 'already') {
            $cond_row1['order_status:>='] = Order_StateModel::ORDER_PAYED;
            $cond_row2['order_status:>='] = Order_StateModel::ORDER_PAYED;
            $cond_row1['order_status:<='] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
            $cond_row2['order_status:<='] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
        }
        //已完成 -> 订单评价
        if ($status == 'finish') {
            $cond_row1['order_status'] = Order_StateModel::ORDER_FINISH;
            $cond_row2['order_status'] = Order_StateModel::ORDER_FINISH;
        }
        //已取消
        if ($status == 'cancel') {
            $cond_row1['order_status'] = Order_StateModel::ORDER_CANCEL;
            $cond_row2['order_status'] = Order_StateModel::ORDER_CANCEL;
        }
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);
        $userId = Perm::$userId;
        $data = array();
        //$cond_row['order_status'] = Order_StateModel::ORDER_FINISH;
        $cond_row1['directseller_flag'] = 1;
        $cond_row2['directseller_flag'] = 1;
        $cond_row1['directseller_id'] = $userId;
        $cond_row2['directseller_p_id'] = $userId;

        $Order_BaseModel = new Order_BaseModel();
        $Order_GoodsModel = new Order_GoodsModel();


        $data['direct'] = $Order_BaseModel->getBaseList($cond_row1, array('order_create_time' => 'DESC'), $page, $rows);//上级
        $data['indirect'] = $Order_BaseModel->getBaseList($cond_row2, array('order_create_time' => 'DESC'), $page, $rows);//上上级



        $gift_package = $this->getTodayOrder($section, $status);

        //礼包查询
        if (!empty($gift_package)) {
            foreach ($gift_package as $k => $v) {
                $order_id = $v['order_id'];
                $gift_order = $Order_BaseModel->getOne($order_id);
                $payment_time = $gift_order['payment_time'];
                $payment_number = $gift_order['payment_number'];
                $payment_other_number = $gift_order['payment_other_number'];
                $gift_con = array(
                    'payment_time' => $payment_time,
                    'payment_number' => $payment_number,
                    'payment_other_number' => $payment_other_number
                );
                $gift_order = $Order_BaseModel->getBaseList($gift_con);
                $gift_order = current($gift_order['items']);


                if ($v['directseller_id'] == $userId) {
                    $gift_order['directseller_commission_0'] = Web_ConfigModel::value("direct_reward");
                    $gift_order['goods_list'][0]['directseller_commission_0'] = Web_ConfigModel::value("direct_reward");
                    array_push($data['direct']['items'], $gift_order);
                } elseif ($v['directseller_p_id'] == $userId) {
                    $gift_order['directseller_commission_1'] = Web_ConfigModel::value("indirect_reward");
                    $gift_order['goods_list'][0]['directseller_commission_1'] = Web_ConfigModel::value("indirect_reward");
                    array_push($data['indirect']['items'], $gift_order);
                }
            }
        }
        $Yf_Page->totalRows = $data['direct']['totalsize'] + $data['indirect']['totalsize'];
        $page_nav = $Yf_Page->prompt();
        $data['t'] = $status;

        if ($this->typ == "json") {
            $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }
    }


    public function getTodayOrder1($section)
    {
        //0.全部推广订单 1.今日推广订单2,今日预估收益
        //礼包商品的处理
        $userId = Perm::$userId;
        $directseller_ids = array();
        array_push($directseller_ids, Perm::$userId);

        $cond = array();
        //当前用户的上级
        $info = $this->User_InfoModel->getByWhere(array('user_parent_id' => $userId));
        $ids = array_column($info, 'user_id');
        if ($ids) {
            $directseller_ids = array_merge_recursive($directseller_ids, $ids);//上上级
        }

        $cond['directseller_id:IN'] = $directseller_ids;//上级
        $cond['identity_type'] = 1;
        if ($section == 2 || $section == 1) {
            $cond['order_goods_time:>='] = date("Y-m-d: 00:00:00", time());
        }

        // $cond['order_goods_status'] = Order_StateModel::ORDER_FINISH;
        $cond['order_goods_status:>='] = Order_StateModel::ORDER_PAYED;
        // $cond['order_status:<='] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
        $cond['order_goods_status:<='] = Order_StateModel::ORDER_FINISH;
        $order_row['order_goods_time'] = 'ASC';
        $order_goods_info = $this->order_GoodsModel->getByWhere($cond, $order_row);
        $buyer_user_ids = array_column($order_goods_info, 'buyer_user_id');


        // 每个用户的第一单满足升级的礼包产品
        $finsh_info = array();
        foreach ($order_goods_info as $k => $v) {
            if (in_array($v['buyer_user_id'], $finsh_info)) {
                unset($order_goods_info[$k]);
            }

            $finsh_info[] = $v['buyer_user_id'];
        }


        foreach ($order_goods_info as $k => $v) {
            $order_goods = $this->order_GoodsModel->getOneByWhere(array('order_id' => $v['order_id']));
            $order_info[] = $order_goods;
        }
        return $order_info;
    }

    public function getTodayOrder($section, $status)
    {
        //0.全部推广订单 1.今日推广订单2,今日预估收益
        //礼包商品的处理
        $userId = Perm::$userId;
        $directseller_ids = array();
        array_push($directseller_ids, Perm::$userId);
        $cond = array();
        //当前用户的上级
        $info = $this->User_InfoModel->getByWhere(array('user_parent_id' => $userId));
        $ids = array_column($info, 'user_id');
        if ($ids) {
            $directseller_ids = array_merge_recursive($directseller_ids, $ids);//上上级
        }

        $cond['directseller_id:IN'] = $directseller_ids;//上级
        $cond['identity_type'] = 1;
        if ($section == 2 || $section == 1) {
            $cond['order_goods_time:>='] = date("Y-m-d: 00:00:00", time());
        }

        // $cond['order_goods_status'] = Order_StateModel::ORDER_FINISH;
        $cond['order_goods_status:>='] = Order_StateModel::ORDER_WAIT_PAY;
        // $cond['order_status:<='] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
        $cond['order_goods_status:<='] = Order_StateModel::ORDER_CANCEL;
        $order_row['order_goods_status'] = 'DESC';
        $order_row['order_goods_finish_time'] = 'ASC';
        $order_goods_info = $this->order_GoodsModel->getByWhere($cond, $order_row);
        $buyer_user_ids = array_column($order_goods_info, 'buyer_user_id');
        // 每个用户的第一单满足升级的礼包产品
        $finsh_info = array();
        foreach ($order_goods_info as $k => $v) {
            if (in_array($v['buyer_user_id'], $finsh_info)) {
                unset($order_goods_info[$k]);
            }

            //已经取消的订单不计算
            if ($v['order_goods_status'] != Order_StateModel::ORDER_CANCEL) {
                $finsh_info[] = $v['buyer_user_id'];
            }
        }

        foreach ($order_goods_info as $k => $v) {

            //待付款
            if ($status == 'wait' && $v['order_goods_status'] != Order_StateModel::ORDER_WAIT_PAY) {
                unset($order_goods_info[$k]);
            }
            //已付款
            if ($status == 'already' && $v['order_goods_status'] != Order_StateModel::ORDER_PAYED) {
                unset($order_goods_info[$k]);
            }
            //已完成 -> 订单评价
            if ($status == 'finish' && $v['order_goods_status'] != Order_StateModel::ORDER_FINISH) {
                unset($order_goods_info[$k]);
            }
            //已取消
            if ($status == 'cancel' && $v['order_goods_status'] != Order_StateModel::ORDER_CANCEL) {
                unset($order_goods_info[$k]);
            }
        }
        $order_info = array();
        foreach ($order_goods_info as $k => $v) {
            $order_goods = $this->order_GoodsModel->getOneByWhere(array('order_id' => $v['order_id']));
            $order_info[] = $order_goods;
        }
        return $order_info;
    }


    /*
     *  用户推广商品
     */
    public function distributionGoods(){
        $data=array();
        $status = request_string('status');
        $section = request_int('section');
        $where = " 1=1 AND ";
        if ($section == 1) {
            $beginTime = date('Y-m-d 00:00:00');
            $endTime = date('Y-m-d 23:59:59');
            $where .=" b.`order_create_time`>'{$beginTime}' AND b.`order_create_time`< '{$endTime}' AND";
        }
        if (request_string('orderkey')) {
            $orderkey = request_string('orderkey');
            $where.=" a.`goods_name` LIKE '%{$orderkey}%' AND";
        }
        switch ($status) {
            case "new" :
                $order="ORDER BY goods_id DESC";
                break;
            case "hot" :
                $order="ORDER BY num DESC";
                break;
            default:
                $order="ORDER BY goods_id ASC";
                break;
        }
        $userId=Perm::$userId;
        $Order_BaseModel = new Order_BaseModel();
        //这语句不是我想写的，但是为了兼顾后续分页操作只能如此
        $sql = "
            SELECT 
              SUM(total) AS total,
              SUM(num) AS num,
              `goods_name`,
              `goods_price`,
              `goods_image`,
              `goods_id`,
              `goods_return_status`
            FROM
              (SELECT 
                * 
              FROM
                (SELECT 
                  (
                    SUM(a.`directseller_commission_0`)
                  ) AS total,
                  COUNT(b.`order_id`) AS num,
                  a.`goods_name`,
                  a.`goods_price`,
                  a.`goods_image`,
                  a.`goods_id`,
                  a.`goods_return_status`
                FROM
                  `yf_order_goods` a 
                  LEFT OUTER JOIN `yf_order_base` b 
                    ON a.`order_id` = b.`order_id` 
                WHERE {$where} 
                   b.`directseller_id` = '{$userId}'
                   AND a. `directseller_flag` = 1
                GROUP BY a.`goods_id` 
                UNION
                ALL 
                SELECT 
                  (
                    SUM(a.`directseller_commission_1`)
                  ) AS total,
                  COUNT(b.`order_id`) AS num,
                  a.`goods_name`,
                  a.`goods_price`,
                  a.`goods_image`,
                  a.`goods_id` ,
                  a.`goods_return_status`
                FROM
                  `yf_order_goods` a 
                  LEFT OUTER JOIN `yf_order_base` b 
                    ON a.`order_id` = b.`order_id` 
                WHERE {$where} 
                   b.`directseller_p_id` = '{$userId}'
                   AND a. `directseller_flag` = 1 
                GROUP BY a.`goods_id`) AS n) AS m 
            GROUP BY goods_id {$order} 
";          
        $data=$Order_BaseModel->sql->getAll($sql);

        $sql2 = "
            SELECT 
              SUM(total) AS total,
              SUM(num) AS num,
              `goods_name`,
              `goods_price`,
              `goods_image`,
              `goods_id`,
              `goods_return_status`
            FROM
              (SELECT 
                * 
              FROM
                (SELECT 
                  (
                    SUM(a.`directseller_commission_0`)
                  ) AS total,
                  COUNT(b.`order_id`) AS num,
                  a.`goods_name`,
                  a.`goods_price`,
                  a.`goods_image`,
                  a.`goods_id`,
                  a.`goods_return_status`
                FROM
                  `yf_order_goods` a 
                  LEFT OUTER JOIN `yf_order_base` b 
                    ON a.`order_id` = b.`order_id` 
                WHERE {$where} 
                   b.`directseller_id` = '{$userId}'
                   AND a. `identity_type` = 1 AND a. `directseller_commission_0` > 0
                GROUP BY a.`goods_id` 
                UNION
                ALL 
                SELECT 
                  (
                    SUM(a.`directseller_commission_1`)
                  ) AS total,
                  COUNT(b.`order_id`) AS num,
                  a.`goods_name`,
                  a.`goods_price`,
                  a.`goods_image`,
                  a.`goods_id` ,
                  a.`goods_return_status`
                FROM
                  `yf_order_goods` a 
                  LEFT OUTER JOIN `yf_order_base` b 
                    ON a.`order_id` = b.`order_id` 
                WHERE {$where} 
                   b.`directseller_p_id` = '{$userId}'
                   AND a. `identity_type` = 1  AND a. `directseller_commission_1` > 0
                GROUP BY a.`goods_id`) AS n) AS m 
            GROUP BY goods_id {$order} ";

        $gift = $Order_BaseModel->sql->getAll($sql2);
        $data = array_merge($data, $gift);
        

        //退款完成 佣金为0
        foreach ($data as $k=> $val) {
        	if ($val['goods_return_status'] == 2) {
        		$data[$k]['total'] = 0.00;
        	}
        }
        $this->data->addBody(-140, $data);
    }

    /*
     *  PC佣金记录
     */
    public function distributionCommission(){
        $User_InfoModel = new User_InfoModel();
        $userList=$User_InfoModel->getOne(Perm::$userId);
            
        $status = request_string('status');
        // BEGIN 条件筛选
        if (request_string('orderkey')) {
            $cond_row['order_id:LIKE'] = '%' . request_string('orderkey') . '%';
        }
        if (request_string('start_date')) {
            $cond_row['order_finished_time:>'] = request_string('start_date');
        }
        if (request_string('end_date')) {
            $cond_row['order_finished_time:<'] = request_string('end_date');
        }
        // END

        switch ($status) {
            case "second" :
                $cond_row['directseller_p_id'] = Perm::$userId;
                break;
            case "third" :
                $cond_row['directseller_gp_id'] = Perm::$userId;
                break;
            default:
                $cond_row['directseller_id'] = Perm::$userId;
                break;
        }
        $Yf_Page = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows = $Yf_Page->listRows;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $cond_row['order_status'] = Order_StateModel::ORDER_FINISH;
        $Order_BaseModel = new Order_BaseModel();
        $data = $Order_BaseModel->getBaseList($cond_row, array('order_create_time' => 'DESC'), $page, $rows);
        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav = $Yf_Page->prompt();
        $data['t'] = $status;

        if ($this->typ == "json") {
            $this->data->addBody(-140, $data);
        } else {
            include $this->view->getView();
        }
    }

}

?>
