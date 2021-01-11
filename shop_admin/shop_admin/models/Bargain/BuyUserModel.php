<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Bargain_BuyUserModel extends Bargain_BuyUser
{
    const ISON = 0;   //砍价中
    const SUCCESS = 1;   //砍价成功
    const FAILURE = 2;     //砍价失败
    const ADMINOFF = 3;  //商家关闭、商家下架商品
    const PLATOFF = 4;  //平台终止

    const ISCHARTER = 1; //发起人
    const NOCHARTER = 0; //发起人

    //活动状态
    public static $bargainOrderState = array(
        self::ISON => "砍价中",
        self::SUCCESS => "砍价成功",
        self::FAILURE => "活动过期，砍价失败",
        self::ADMINOFF => "商家关闭，砍价失败",
        self::PLATOFF => "平台终止，砍价失败",
    );
    //根据bargain_id获取活动订单列表
    public function getOrderList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);
        foreach ($data['items'] as $k => $v) {
            $data['items'][$k]['bargain_status_con'] = __(self::$bargainOrderState[$v['bargain_status']]);
        }
        return $data;
    }

    //获取用户砍价列表
    public function getBuyUserBargainList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);

        $sql = "SELECT bargain_buy_user.*,bargain_base.*,goods_base.goods_name,goods_base.goods_price AS goods_old_price,goods_base.goods_image,user_info.user_name FROM ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "bargain_buy_user AS bargain_buy_user JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "bargain_base AS bargain_base ON ";
        $sql .= "bargain_buy_user.bargain_id = bargain_base.bargain_id JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "goods_base AS goods_base ON ";
        $sql .= "bargain_base.goods_id = goods_base.goods_id JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "user_info AS user_info ON ";
        $sql .= "user_info.user_id = bargain_buy_user.user_id ";
        $sql .= "where 1 AND bargain_base.is_del = 0";
        if($cond_row['user_id'])
        {
            $sql .= " AND bargain_buy_user.user_id = " . $cond_row['user_id'];
        }
        if ($cond_row['bargain_id']) {
            $sql .= " AND bargain_base.bargain_id = " . $cond_row['bargain_id'];
        }
        $sql .= " ORDER BY bargain_buy_user.create_time DESC LIMIT " . ($page - 1) . "," . $rows;

        $result = $this->sql->getAll($sql);
        foreach ($result as $k => $v) {
            $result[$k]['bargain_status_con'] = __(self::$bargainOrderState[$v['bargain_status']]);
        }
        $data['items'] = $result;
        return $data;
    }

    //根据buy_id获取砍价信息、
    public function getBargainInfoByBuyId($buy_id,$user_id = 0)
    {
        $sql = "SELECT bargain_buy_user.*,bargain_base.*,goods_base.goods_name,goods_base.goods_price AS goods_old_price,goods_base.goods_image FROM ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "bargain_buy_user AS bargain_buy_user JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "bargain_base AS bargain_base ON ";
        $sql .= "bargain_buy_user.bargain_id = bargain_base.bargain_id JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "goods_base AS goods_base ON ";
        $sql .= "bargain_base.goods_id = goods_base.goods_id ";
        $sql .= "where 1 AND bargain_base.is_del = 0";
        $sql .= " AND bargain_buy_user.buy_id = " . $buy_id;
        $res = $this->sql->getAll($sql);
        $result = current($res);
        $result['start'] = date("Y-m-d", $result['start_time']);
        $result['end'] = date("Y-m-d", $result['end_time']);
        $result['end_date'] = date("Y-m-d H:i:s", $result['end_time']);

        //发起砍价用户信息
        $User_InfoModel = new User_InfoModel();
        $user_info = $User_InfoModel->getOne($result['user_id']);
        $result['user_name'] = $user_info['user_name'];
        $result['user_logo'] = $user_info['user_logo'];

        //砍价金额比例
        $result['rate'] = round($result['bargain_price_count']/ ($result['goods_price'] - $result['bargain_price'])*100) . "%";
        $result['over_price'] = number_format($result['goods_price'] - $result['bargain_price'] - $result['bargain_price_count'],2);

        //参与砍价用户
        $Bargain_JoinUserModel = new Bargain_JoinUserModel();
        $user_list = $Bargain_JoinUserModel->getJoinUser($result['buy_id']);
        $result['join_user'] = $user_list;
        //判断当前用户是否帮助砍价过
        $join_user_ids = array_column($user_list, 'user_id');
        if (in_array($user_id, $join_user_ids)) {
            $result['is_join'] = 1;
        } else {
            $result['is_join'] = 0;
        }

        //判断当前用户是否为发起砍价用户
        if($user_id == $result['user_id']){
            $result['is_self'] = 1;
        }else{
            $result['is_self'] = 0;
        }

        //判断当前活动是否过期
        if($result['end_time'] > time() && $result['bargain_status'] == 1){
            $result['is_on'] = 1;
        }else{
            $result['is_on'] = 0;
        }

        $result['user_end_date'] = date('Y-m-d H:i:s',$result['user_end_time']);

        return $result;
    }

    //发起砍价
    public function InitiateBargain($add_row)
    {
        $Bargain_BaseModel = new Bargain_BaseModel();
        $bargain_info = $Bargain_BaseModel->getOneByWhere(array('bargain_id'=>$add_row['bargain_id']));

        //判断当前砍价是否正在砍价中
        $row = array();
        $row['user_id'] = $add_row['user_id'];
        $row['bargain_id'] = $add_row['bargain_id'];
        $row['bargain_state'] = Bargain_BuyUserModel::ISON;
        $buy_user_info = $this->getByWhere($row);
        if(empty($buy_user_info)){
            //判断当前砍价活动是否在有效期内
            if ($bargain_info['end_time'] < time()) {
                $data['msg'] = '该活动已过期';
                $data['status'] = 250;
                $data['data'] = array();
            } else {
                //判断砍价库存是否足够
                if($bargain_info['bargain_stock'] > 0){
                    $rs_row = array();
                    $Bargain_BaseModel->sql->startTransactionDb();

                    //写入yf_bargain_buy_user
                    $add_row['bargain_num'] = 1;
                    $add_row['bargain_state'] = self::ISON;
                    $add_row['create_time'] = time();
                    $add_row['user_end_time'] = strtotime('+1 day');
                    $add_row['bargain_price'] = $bargain_info['bargain_price'];
                    $add_buy_flag = $this->addBuyUser($add_row, true);
                    check_rs($add_buy_flag, $rs_row);

                    //砍价
                    $bargain_price = $this->BargainPrice($add_buy_flag);

                    //写入yf_bargain_join_user
                    $Bargain_JoinUserModel = new Bargain_JoinUserModel();
                    $join_row['user_id'] = $add_row['user_id'];
                    $join_row['bargain_id'] = $add_row['bargain_id'];
                    $join_row['is_charter'] = 1;//发起人
                    $join_row['create_time'] = time();
                    $join_row['buy_id'] = $add_buy_flag;
                    $join_row['help_bargain_price'] = $bargain_price;//砍掉的价格
                    $add_join_row = $Bargain_JoinUserModel->addJoinUser($join_row);
                    check_rs($add_join_row, $rs_row);

                    //修改yf_bargain_buy_user
                    $edit_row1 = array();
                    $edit_row1['bargain_price_count'] = $bargain_price;
                    $edit_flag1 = $this->editBuyUser($add_buy_flag, $edit_row1);
                    check_rs($edit_flag1, $rs_row);

                    //修改yf_bargain_base库存、参与人数
                    $edit_row2 = array();
                    $edit_row2['join_num'] = $bargain_info['join_num'] + 1;
                    $edit_row2['bargain_stock'] = $bargain_info['bargain_stock'] - 1;
//                $edit_row2['buy_num'] = $bargain_info['buy_num'] + 1;
                    $edit_flag2 = $Bargain_BaseModel->editBargain($bargain_info['bargain_id'], $edit_row2);
                    check_rs($edit_flag2, $rs_row);

                    $flag = is_ok($rs_row);
                    if ($flag && $Bargain_JoinUserModel->sql->commitDb()) {
                        $data['msg'] = 'success';
                        $data['status'] = 200;
                        $data['data']['buy_id'] = $add_buy_flag;
                        $data['data']['bargain_price'] = $bargain_price;
                    } else {
                        $Bargain_JoinUserModel->sql->rollBackDb();
                        $data['msg'] = '砍价失败';
                        $data['status'] = 250;
                        $data['data'] = array();
                    }
                }else{
                    $data['msg'] = '砍价库存不足';
                    $data['status'] = 250;
                    $data['data'] = array();
                }
            }
        }else{
            $data['msg'] = '您已经参加过该活动';
            $data['status'] = 250;
            $data['data'] = array();
        }

        return $data;
    }

    //砍掉的价格
    public function BargainPrice($buy_id)
    {
        $sql = "SELECT bargain_base.*,bargain_buy_user.*  FROM ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "bargain_buy_user AS bargain_buy_user JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "bargain_base AS bargain_base ON ";
        $sql .= "bargain_buy_user.bargain_id = bargain_base.bargain_id ";
        $sql .= "where 1";
        $sql .= " AND bargain_buy_user.buy_id = " . $buy_id;
        $result = $this->sql->getAll($sql);
        $buy_order_info = current($result);

        $user_id = $buy_order_info['user_id'];

        //当前用户是否第一次砍价
        $Bargain_JoinUserModel = new Bargain_JoinUserModel();
        $buy_order = $Bargain_JoinUserModel->getOneByWhere(array('user_id'=> Perm::$userId));

        //商品原价与商品底价的差价
        $price = $buy_order_info['goods_price'] - $buy_order_info['bargain_price'];

        //判断活动的砍价方式 1-共砍刀数 2-最多可砍价格
        if($buy_order_info['bargain_type'] == 2){
            if ($user_id == Perm::$userId) {
                //自己默认砍价第一刀 若是从未发起过砍价的用户，砍价为商品原价与商品底价的差价的30%；否则砍价为商品原价与商品底价的差价的10%
                //最大可砍金额占商品原价与商品底价的差价比例，大于不同情况下的10%，30% 优先考虑
                $bargain_rate = rand(0.1,$buy_order_info['bargain_num_price'] / $buy_order_info['goods_price']*100);
                if($buy_order){
                    //最大比例大于10%，优先考虑
                    if($bargain_rate < 10){
                        $bargain_price = (10 * $price)/100;
                    }else{
                        $bargain_price = $bargain_rate * $price/100;
                    }
                }else{
                    //最大比例大于30%，优先考虑
                    if ($bargain_rate < 30) {
                        $bargain_price = (30 * $price) / 100;
                    } else {
                        $bargain_price = $bargain_rate * $price/100;
                    }
                }
            } else {
                //邀请好友砍价 若好友第一次参与砍价，则砍价金额为砍掉商品金额的10%，若不是第一次参与砍价，则砍价金额随机。随机的金额大于0不得超过总金额的20%，概率大于0%不得超过20%
                if ($buy_order) {
                    $rate = rand(0.1, $buy_order_info['bargain_num_price']/ $buy_order_info['goods_price']*100);//最大占额为：最多砍价价格占商品原价的百分比
                    $bargain_price = ($rate * $price) / 100;
                } else {
                    $bargain_price = (10 * $price) / 100;
                }
            }
        }else{
            //根据砍价次数，随机砍价金额
            if ($user_id == Perm::$userId) {
                //自己默认砍价第一刀 若是从未发起过砍价的用户，砍价为商品金额的30%；否则砍价为商品金额的10%
                if ($buy_order) {
                    $bargain_price = (10 * $price) / 100;
                } else {
                    $bargain_price = (30 * $price) / 100;
                }
            } else {
                //邀请好友砍价 若好友第一次参与砍价，则砍价金额为砍掉差价的10%，若不是第一次参与砍价，则砍价金额随机。随机的金额大于0不得超过差价的20%，概率大于0%不得超过20%
                // 判断是否为最后一个砍价
                $last_num = $buy_order_info['bargain_num_price'] - $buy_order_info['bargain_num'];
                if ($buy_order || $last_num == 1 ) {
                    $total = $buy_order_info['goods_price'] - $buy_order_info['bargain_price'] - $buy_order_info['bargain_price_count'];//还需要砍掉的价格总数
                    $sum_num = $buy_order_info['bargain_num_price'];// 总共可以参与砍价的次数
                    $min = 0.01;//每人最少可砍多少钱
                    $num = $buy_order_info['bargain_num'] + 1; //当前砍价的次数
                    if($last_num == 1){
                        $safe_total = ($total - ($sum_num - $num) * $min);//随机安全上限
                        $money = $safe_total;
                    }else{
                        $safe_total = ($total - ($sum_num - $num) * $min) / ($sum_num - $num);//随机安全上限
                        $money = mt_rand($min * 100, $safe_total * 100) / 100;
                    }
                    $bargain_price = $money;
                } else {
                    $bargain_price = (10 * $price) / 100;
                }
            }
        }
        return number_format($bargain_price,2);
    }

    //帮助砍价
    public function HelpBargain($buy_id)
    {
        //查询当前用户当天帮助砍价次数
        $user_count_cond['user_id'] = Perm::$userId;
        $user_count_cond['join_date'] = date('Y-m-d',time());
        $Bargain_JoinCountModel = new Bargain_JoinCountModel();
        $user_count = $Bargain_JoinCountModel->getByWhere($user_count_cond);
        $user_count = current($user_count);

        //每个会员每天最多帮助砍价3次
        if($user_count && $user_count['join_count'] >= 3){
            $data['data'] = array();
            $data['status'] = 250;
            $data['msg'] = '每天最多只能帮助好友砍价3次';
        }else{
            $user_bargain_price = $this->BargainPrice($buy_id);
            if($user_bargain_price <= 0){
                $user_bargain_price = $this->BargainPrice($buy_id);
            }
            $bargain_info = $this->getBargainInfoByBuyId($buy_id);

            $User_InfoModel = new User_InfoModel();
            $user_info = $User_InfoModel->getOne($bargain_info['user_id']);
            //判断当前砍价活动是否在有效期内
            if($bargain_info['end_time'] < time()){
                $data['data'] = array();
                $data['status'] = 250;
                $data['msg'] = '该砍价活动已结束';
            }else{
                $rs_row = array();

                $Bargain_JoinUserModel = new Bargain_JoinUserModel();
                $Bargain_JoinUserModel->sql->startTransactionDb();

                $over_bargain_price = $bargain_info['goods_price'] - $bargain_info['bargain_price'] - $bargain_info['bargain_price_count']; //剩余需要砍价总数
                $over_bargain_price = number_format($over_bargain_price, 2);

                $edit_row1 = array();
                $edit_row2 = array();
                if($user_bargain_price >= $over_bargain_price){
                    //砍价成功
                    $bargain_price = number_format($over_bargain_price, 2);
                    //砍价成功的状态
                    $edit_row1['bargain_state'] = 1;//砍价状态 - 成功

                    //砍价购买人数+1
                    $edit_row2['buy_num'] = $bargain_info['buy_num'] + 1;

                    //生成实物订单
                    $Order_BaseModel = new Order_BaseModel();
                    $order_data = $Order_BaseModel->addBargainOrderBase($bargain_info);
                    if($order_data['status'] == 200){
                        $order_flag = true;
                    }else{
                        $order_flag = false;
                    }
                    $edit_row1['order_id'] = $order_data['data']['order_id'];//砍价状态 - 订单id

                    check_rs($order_flag, $rs_row);

                    //推送砍价成功消息
                    $message = new MessageModel();
                    $code = 'bargain success code';
                    $message_user_id = $bargain_info['user_id'];
                    $message_user_name = $user_info['user_name'];
                    $order_id = NULL;
                    $shop_name = NULL;
                    $message_mold = 0;
                    $message_type = 1;
                    $end_time = Null;
                    $common_id = NULL;
                    $goods_id = NULL;
                    $des = NULL;
                    $start_time = Null;
                    $goods_name = NULL;
                    $av_amount = NULL;
                    $freeze_amount = NULL;
                    $ztm = NULL;
                    $chain_name = NULL;
                    $content_mobile = NULL;
                    $area_code = NULL;
                    $user_name = $user_info['user_name'];
                    $message->sendMessage($code, $message_user_id, $message_user_name, $order_id, $shop_name, $message_mold, $message_type, $end_time = Null, $common_id, $goods_id, $des, $start_time = Null, $goods_name, $av_amount, $freeze_amount, $ztm, $chain_name, $content_mobile, $area_code, $user_name);

                    //砍价成功状态
                    $is_success = 1;
                }else{
                    //砍价尚未成功
                    $bargain_price = number_format($user_bargain_price, 2);
                    $is_success = 0;
                }

                $join_row['user_id'] = Perm::$userId;
                $join_row['bargain_id'] = $bargain_info['bargain_id'];
                $join_row['is_charter'] = 1;//发起人
                $join_row['create_time'] = time();
                $join_row['buy_id'] = $buy_id;
                $join_row['help_bargain_price'] = $bargain_price;//砍掉的价格
                $add_join_row = $Bargain_JoinUserModel->addJoinUser($join_row);
                check_rs($add_join_row, $rs_row);

                //修改yf_bargain_buy_user
                $bargain_price_count = number_format($bargain_info['bargain_price_count'] + $bargain_price, 2);
                $edit_row1['bargain_price_count'] = $bargain_price_count;//已经砍掉的价格
                $edit_row1['bargain_num'] = $bargain_info['bargain_num'] + 1;
                $edit_flag1 = $this->editBuyUser($buy_id, $edit_row1);
                check_rs($edit_flag1, $rs_row);

                //修改yf_bargain_base库存、参与人数
                $edit_row2['join_num'] = $bargain_info['join_num'] + 1;
                $Bargain_BaseModel = new Bargain_BaseModel();
                $edit_flag2 = $Bargain_BaseModel->editBargain($bargain_info['bargain_id'], $edit_row2);
                check_rs($edit_flag2, $rs_row);

                //编辑用户当天帮助砍价次数
                if(empty($user_count)){
                    $add_cond['user_id'] = Perm::$userId;
                    $add_cond['join_date'] = date('Y-m-d', time());
                    $add_cond['join_count'] = 1;
                    $add_count_flag = $Bargain_JoinCountModel->addBargain($add_cond);
                    check_rs($add_count_flag, $rs_row);
                }else{
                    $edit_cond['join_count'] = $user_count['join_count'] + 1;
                    $edit_count_flag = $Bargain_JoinCountModel->editBargain($user_count['count_id'],$edit_cond);
                    check_rs($edit_count_flag, $rs_row);
                }

                $flag = is_ok($rs_row);
                if($flag && $Bargain_JoinUserModel->sql->commitDb()){
                    $data['data']['is_success'] = $is_success;
                    $data['data']['help_bargain_price'] = $bargain_price;
                    $data['data']['bargain_price_count'] = $bargain_price_count;
                    $data['data']['over_bargain_price'] = number_format($bargain_info['goods_price'] - $bargain_info['bargain_price'] - $bargain_info['bargain_price_count'], 2);
                    $data['data']['rate'] = round($bargain_price_count / ($bargain_info['goods_price'] - $bargain_info['bargain_price']) * 100) . "%";
                    $data['status'] = 200;
                    $data['msg'] = '砍价成功';
                }else{
                    $Bargain_JoinUserModel->sql->rollBackDb();
                    $data['data'] = array();
                    $data['status'] = 250;
                    $data['msg'] = '砍价失败';
                }
            }
        }
        return $data;
    }

}

?>