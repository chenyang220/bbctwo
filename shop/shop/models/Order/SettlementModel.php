<?php

if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Order_SettlementModel extends Order_Settlement
{

    const SETTLEMENT_WAIT_OPERATE       = 1;      //已出账     等待操作	      生成结算单
    const SETTLEMENT_SELLER_COMFIRMED   = 2;     //商家已确认  商家确认
    const SETTLEMENT_PLATFORM_COMFIRMED = 3;     //平台已审核     等待卖家发货	     配货
    const SETTLEMENT_FINISH             = 4;      //结算完成
    const SETTLEMENT_VIRTUAL_ORDER      = 1;     //虚拟订单
    const SETTLEMENT_NORMAL_ORDER       = 0;     //实物订单

    public static $state      = array(
        '1' => 'wait_operate',
        //已出账
        '2' => 'seller_comfirmed',
        //商家已确认
        '3' => 'platform_comfirmed',
        //平台已审核
        '4' => 'finish',
        //结算完成
    );
    public static $order_type = array(
        '1' => 'virtual',
        //虚拟订单
        '0' => 'normal',
        //实物订单
    );
    public        $settle_state;

    public function __construct()
    {
        parent::__construct();
        $this->settle_state = array(
            '1' => __("已出账"),
            //已出账
            '2' => __("商家已确认"),
            //商家已确认
            '3' => __("平台已审核"),
            //平台已审核
            '4' => __("结算完成"),
            //结算完成
        );
    }

    /**
     *  根据shop_id与订单类型获取结算单列表
     *
     * @param  int $config_key 主键值
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getSettlementList($cond_row = array(), $order_row = array(), $page = 1, $rows = 10)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);

        foreach ($data['items'] as $k => $v)
        {
            $data['items'][$k]['os_state_text'] = $this->settle_state[$v['os_state']];
            $data['items'][$k]['os_start']      = $v['os_start_date'];
        }
        return $data;
    }

    public function getSettlementExcel($cond_row = array(), $order_row = array())
    {
        $data = $this->getByWhere($cond_row, $order_row);

        foreach ($data as $k => $v)
        {
            $data[$k]['os_id']         = " " . $v['os_id'] . " ";
            $data[$k]['os_state_text'] = $this->settle_state[$v['os_state']];
        }
        return array_values($data);
    }

    public function getOneSettle($os_id)
    {
        $data                        = $this->getOne($os_id);
        $data['os_state_text']       = $this->settle_state[$data['os_state']];
        $data['os_state_etext']      = self::$state[$data['os_state']];
        $data['os_order_type_etext'] = self::$order_type[$data['os_order_type']];
        return $data;
    }

    //获取店铺最近结算单
    public function getLastSettlementByShopid($shop_id = null, $type = null)
    {
        $cond_row = array();
        if ($shop_id)
        {
            $cond_row = array(
                'shop_id:IN' => $shop_id,
                'os_order_type' => $type,
            );
        }

        $settlement_rows = $this->getByWhere($cond_row, array('os_id' => 'DESC'));
        $data            = reset($settlement_rows);

        return $data;

    }

    public function getLastSettlementByType($type = null)
    {
        $cond_row['os_order_type'] = $type;

        $data = $this->getByWhere($cond_row, array('os_id' => 'DESC'));
        return $data;
    }

    //店铺实物订单结算
    public function settleNormalOrder($val = array())
    {
        $Shop_BaseModel = new Shop_BaseModel();
        $Order_BaseModel = new Order_BaseModel();
        $Shop_CostModel = new Shop_CostModel();

        if($val['shop_settlement_last_time'] !== '0000-00-00 00:00:00')
        {
            $start_unixtime = strtotime($val['shop_settlement_last_time']);
        }
        else
        {
            $start_unixtime = strtotime($val['shop_create_time']);
        }
        $start_unixtime = $start_unixtime ? strtotime(date('Y-m-d 00:00:00', $start_unixtime) . "+1 day") : "";
        $start_time     = @date('Y-m-d H:i:s', $start_unixtime);

        $end_unixtime = $start_unixtime ? strtotime(date('Y-m-d 23:59:59', $start_unixtime) . "+" . ($val['shop_settlement_cycle']-1) . " day") : "";
        $end_time     = @date('Y-m-d H:i:s', $end_unixtime);

        $time = time();
        $data = array();
        $cond_row['os_start_date'] = $start_time;
        $cond_row['os_end_date'] = $end_time;
        $cond_row['shop_id'] = $val['shop_id'];
        $info = $this->getByWhere($cond_row);
        if (empty($info)) {
            if ($time > $end_unixtime) {
                $settle_row = array();
                //计算某月内，某店铺实物订单的销量，退单量，佣金
                $order_cond_row = array(
                    'shop_id' => $val['shop_id'],
                    'order_finished_time:>' => 0,
                    'order_is_virtual' => 0,
                    'order_finished_time:>=' => $start_time,
                    'order_finished_time:<=' => $end_time
                );
                $order_code = $Order_BaseModel->getByWhere($order_cond_row);
                $commit_data = [];


                if ($order_code) {
                    $order_ids = array_column($order_code, 'order_id');
//			    $user_ids = array_column($order_code,'buyer_user_id');
//                $order_user = array_combine($order_ids,$user_ids);
                    $order_ids = "'" . implode("','", $order_ids) . "'";
                    $sql = 'select * from yf_fenxiao_order_goods where order_id IN(' . $order_ids . ')';
                    $redata = $Order_BaseModel->sql->getAll($sql);
                    if ($redata) {
                        foreach ($redata as $k => $v) {
                            $sql = 'select * from yf_fenxiao_commission where  order_goods_id = ' . $v['id'];
                            $redata = $Order_BaseModel->sql->getAll($sql);
                            if ($redata) {
                                $commit_price = array_column($redata, 'price');
                                $commit_data[] = array_sum($commit_price);
                            }
                        }
                    }
                }
                $commit_coun_price = array_sum($commit_data);

                $settle_row = $Order_BaseModel->settleOrder($order_cond_row);
                //计算某月内，某店铺实物订单的退款
                $Order_ReturnModel = new Order_ReturnModel();
                $return_cond_row = array();
                $return_cond_row['seller_user_id'] = $val['shop_id'];
                $return_cond_row['return_finish_time:>='] = $start_time;
                $return_cond_row['return_finish_time:<='] = $end_time;
                $return_cond_row['order_is_virtual'] = 0;  //实物订单
                $return_cond_row['return_shop_handle'] = Order_ReturnModel::RETURN_SELLER_PASS; //商家同意退款

                $return_row = $Order_ReturnModel->settleReturn($return_cond_row);
                $settle_row['return_amount'] = $return_row['return_amount'];
                $settle_row['commission_return_amount'] = $return_row['commission_return_amount'];
                $settle_row['redpacket_return_amount'] = $return_row['redpacket_return_amount'];
                $settle_row['fenxiao_return_amount'] = $return_row['fenxiao_return_amount'];

                //结算店铺费用
                $shop_cond_row = array(
                    'shop_id' => $val['shop_id'],
                    'cost_status' => Shop_CostModel::UNSETTLED,
                    'cost_time:>=' => $start_time,
                    'cost_time:<=' => $end_time,
                );
                $settle_row['shop_cost'] = $Shop_CostModel->settleShopCost($shop_cond_row);

                $add_settle_row = array();
                //结算单编号（年月日订单type店铺id）
                $prefix = sprintf('%s%s%s', date('Ymd'), 0, $val['shop_id']);
                $Number_SeqModel = new Number_SeqModel();
                $order_number = $Number_SeqModel->createSeq($prefix);
                $add_settle_row['os_id'] = sprintf('%s', $order_number);
                //开始时间
                $add_settle_row['os_start_date'] = $start_time;
                //结束时间
                $add_settle_row['os_end_date'] = $end_time;
                //订单金额
                $add_settle_row['os_order_amount'] = $settle_row['order_amount'];
                //红包金额
                $add_settle_row['os_redpacket_amount'] = $settle_row['redpacket_amount'];
                //分销金额
                $add_settle_row['os_directseller_amount'] = $settle_row['order_directseller_commission'] + $commit_coun_price - $settle_row['fenxiao_return_amount'];
                //运费
                $add_settle_row['os_shipping_amount'] = $settle_row['shipping_amount'];
                //退单金额
                $add_settle_row['os_order_return_amount'] = $settle_row['return_amount'];
                //佣金金额
                $add_settle_row['os_commis_amount'] = $settle_row['commission_amount'];
                //退还佣金
                $add_settle_row['os_commis_return_amount_fenxiao'] = $settle_row['fenxiao_return_amount'];
                //货到付款佣金金额  //货到付款补丁
                if (!empty($settle_row['paymet_id_fee'])) {
                    foreach ($settle_row['paymet_id_fee'] as $r) {
                        if ($r['payment_id'] == 2 || $r['payment_id'] == 3) {
                            $add_settle_row['os_commis_cod'] += $r['order_commission_fee'];
                        }
                    }
                }

                //退还金额
                $add_settle_row['os_commis_return_amount'] = $settle_row['commission_return_amount'];
                //退还红包金额
                $add_settle_row['os_redpacket_return_amount'] = $settle_row['redpacket_return_amount'];
                //店铺促销活动费用
                $add_settle_row['os_shop_cost_amount'] = $settle_row['shop_cost'];
                //应结金额（订单金额（含运费）+红包金额-佣金金额-退单金额-退还红包金额+退还佣金-店铺费用+定金订单中的未退定金+下单时使用的平台红包-全部退款时应扣除的平台红包）
                $add_settle_row['os_amount'] = $settle_row['order_amount'] + $settle_row['redpacket_amount'] - $settle_row['commission_amount'] - $settle_row['return_amount'] - $settle_row['redpacket_return_amount'] + $settle_row['commission_return_amount'] - $settle_row['shop_cost'] - $settle_row['order_directseller_commission'] - $commit_coun_price + $settle_row['fenxiao_return_amount'];
                //生成结算单时间
                $add_settle_row['os_datetime'] = get_date_time();
                //结算单年月
                $add_settle_row['os_date'] = date('Y-m');
                //状态
                $add_settle_row['os_state'] = Order_SettlementModel::SETTLEMENT_WAIT_OPERATE;
                //店铺id
                $add_settle_row['shop_id'] = $val['shop_id'];
                //店铺名
                $add_settle_row['shop_name'] = $val['shop_name'];
                //结算订单类型
                $add_settle_row['os_order_type'] = 0;
                $flag = $this->addSettlement($add_settle_row);
                $data['flag'] = $flag;
                $data['os_id'] = $add_settle_row['os_id'];
                $data['end_time'] = $end_time;
                $data['start_time'] = $start_time;
            }
        }
        return $data;
    }

    //店铺结算虚拟订单
    public function settleVirtualOrder($val = array())
    {
        $Shop_BaseModel = new Shop_BaseModel();
        $Order_BaseModel = new Order_BaseModel();
        $Shop_CostModel = new Shop_CostModel();

        if($val['shop_settlement_last_time'] > 0)
        {
            $start_unixtime = strtotime($val['shop_settlement_last_time']);
        }
        else
        {
            $start_unixtime = strtotime($val['shop_create_time']);
        }

        $start_unixtime = $start_unixtime ? strtotime(date('Y-m-d 00:00:00', $start_unixtime) . "+1 day") : "";
        $start_time     = @date('Y-m-d H:i:s', $start_unixtime);

        $end_unixtime = $start_unixtime ? strtotime(date('Y-m-d 23:59:59', $start_unixtime) . "+" . ($val['shop_settlement_cycle']-1) . " day") : "";
        $end_time     = @date('Y-m-d H:i:s', $end_unixtime);

        $time = time();

        $data = array();
        $cond_row['os_start_date'] = $start_time;
        $cond_row['os_end_date'] = $end_time;
        $cond_row['shop_id'] = $val['shop_id'];
        $info = $this->getByWhere($cond_row);

        if (empty($info)) {
            if ($time > $end_unixtime) {
                $settle_row = array();
                //计算某月内，某店铺虚拟物订单的销量，退单量，佣金
                $order_cond_row = array(
                    'shop_id' => $val['shop_id'],
                    'order_finished_time:>' => 0,
                    'order_is_virtual' => 1,
                    'order_finished_time:>=' => $start_time,
                    'order_finished_time:<=' => $end_time
                );
                $settle_row = $Order_BaseModel->settleOrder($order_cond_row);

                //计算某月内，某店铺实物订单的退款
                $Order_ReturnModel = new Order_ReturnModel();
                $return_cond_row = array();
                $return_cond_row['seller_user_id'] = $val['shop_id'];
                $return_cond_row['return_finish_time:>='] = $start_time;
                $return_cond_row['return_finish_time:<='] = $end_time;
                $return_cond_row['order_is_virtual'] = 1; //虚拟订单
                $return_cond_row['return_shop_handle'] = Order_ReturnModel::RETURN_SELLER_PASS; //商家同意退款

                $return_row = $Order_ReturnModel->settleReturn($return_cond_row);
                $settle_row['return_amount'] = $return_row['return_amount'];
                $settle_row['commission_return_amount'] = $return_row['commission_return_amount'];

                $add_settle_row = array();
                //结算单编号（年月日订单type店铺id）
                $prefix = sprintf('%s%s%s', date('Ymd'), 1, $val['shop_id']);
                $Number_SeqModel = new Number_SeqModel();
                $order_number = $Number_SeqModel->createSeq($prefix);
                $add_settle_row['os_id'] = sprintf('%s', $order_number);
                //开始时间
                $add_settle_row['os_start_date'] = $start_time;
                //结束时间
                $add_settle_row['os_end_date'] = $end_time;
                //订单金额
                $add_settle_row['os_order_amount'] = $settle_row['order_amount'];
                //退单金额
                $add_settle_row['os_order_return_amount'] = $settle_row['return_amount'];
                //佣金金额
                $add_settle_row['os_commis_amount'] = $settle_row['commission_amount'];
                //退还金额
                $add_settle_row['os_commis_return_amount'] = $settle_row['commission_return_amount'];
                //应结金额（消费金额（已消费的虚拟码+已过期未消费但不退款的虚拟码）-佣金）
                $add_settle_row['os_amount'] = $settle_row['order_amount'] - $settle_row['commission_amount'] - $settle_row['return_amount'] + $settle_row['commission_return_amount'];
                //生成结算单时间
                $add_settle_row['os_datetime'] = get_date_time();
                //结算单年月
                $add_settle_row['os_date'] = date('Y-m');
                //状态
                $add_settle_row['os_state'] = Order_SettlementModel::SETTLEMENT_WAIT_OPERATE;
                //店铺id
                $add_settle_row['shop_id'] = $val['shop_id'];
                //店铺名
                $add_settle_row['shop_name'] = $val['shop_name'];
                //结算订单类型
                $add_settle_row['os_order_type'] = 1;

                $flag = $this->addSettlement($add_settle_row);

                $data['flag'] = $flag;
                $data['os_id'] = $add_settle_row['os_id'];
                $data['end_time'] = $end_time;
                $data['start_time'] = $start_time;

            }
        }
        return $data;
    }

    //实物订单结算
    public function settleNormalOrder1()
    {
        //1.查找店铺的结算周期
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_info      = $Shop_BaseModel->getSettlementCycle();
        fb($shop_info);

        $Order_BaseModel = new Order_BaseModel();
        $Shop_CostModel  = new Shop_CostModel();
        foreach ($shop_info as $key => $val)
        {
            $start_unixtime       = '';
            $start_time           = '';
            $end_time             = '';
            $end_unixtime         = '';
            $settlement_last_info = array();
            //2.查找店铺上个结算单的结算时间
            $settlement_last_info = $this->getLastSettlementByShopid($val['shop_id'], Order_SettlementModel::SETTLEMENT_NORMAL_ORDER);

            $start_unixtime = $settlement_last_info['os_end_date'] ? $settlement_last_info['os_end_date'] : "";

            //若是新开店铺没有结算单，则从开店日期开始算
            if (!$start_unixtime)
            {
                $start_unixtime = $val['shop_create_time'];
            }

            $start_unixtime = strtotime($start_unixtime);
            $start_unixtime = $start_unixtime ? strtotime(date('Y-m-d 00:00:00', $start_unixtime) . "+1 day") : "";
            $start_time     = @date('Y-m-d H:i:s', $start_unixtime);

            $end_unixtime = $start_unixtime ? strtotime(date('Y-m-d 23:59:59', $start_unixtime) . "+" . $val['shop_settlement_cycle'] . " day") : "";
            $end_time     = @date('Y-m-d H:i:s', $end_unixtime);

            $time = time();

            if ($time > $end_unixtime)
            {
                $settle_row = array();
                //计算某月内，某店铺实物订单的销量，退单量，佣金
                $order_cond_row = array(
                    'shop_id' => $val['shop_id'],
                    'order_finished_time:>' => 0,
                    'order_is_virtual' => 0,
                    'order_finished_time:>=' => $start_time,
                    'order_finished_time:<=' => $end_time
                );
                $settle_row     = $Order_BaseModel->settleOrder($order_cond_row);

                //计算某月内，某店铺实物订单的退款
                $Order_ReturnModel = new Order_ReturnModel();
                $return_cond_row = array();
                $return_cond_row['seller_user_id'] = $val['shop_id'];
                $return_cond_row['return_finish_time:>='] = $start_time;
                $return_cond_row['return_finish_time:<='] = $end_time;
                $return_cond_row['order_is_virtual'] = 0;

                $return_row = $Order_ReturnModel->settleReturn($return_cond_row);
                $settle_row['return_amount'] = $return_row['return_amount'];
                $settle_row['commission_return_amount'] = $return_row['commission_return_amount'];
                $settle_row['redpacket_return_amount']  = $return_row['redpacket_return_amount'];
                fb($return_cond_row);

                //结算店铺费用
                $shop_cond_row           = array(
                    'shop_id' => $val['shop_id'],
                    'cost_status' => Shop_CostModel::UNSETTLED,
                    'cost_time:>=' => $start_time,
                    'cost_time:<=' => $end_time,
                );
                $settle_row['shop_cost'] = $Shop_CostModel->settleShopCost($shop_cond_row);

                $add_settle_row = array();
                //结算单编号（年月日订单type店铺id）
                $add_settle_row['os_id'] = sprintf('%s%s%s', date('Ymd'), 0, $val['shop_id']);
                //开始时间
                $add_settle_row['os_start_date'] = $start_time;
                //结束时间
                $add_settle_row['os_end_date'] = $end_time;
                //订单金额
                $add_settle_row['os_order_amount'] = $settle_row['order_amount'];
                //红包金额
                $add_settle_row['os_redpacket_amount'] = $settle_row['redpacket_amount'];
                //运费
                $add_settle_row['os_shipping_amount'] = $settle_row['shipping_amount'];
                //退单金额
                $add_settle_row['os_order_return_amount'] = $settle_row['return_amount'];
                //佣金金额
                $add_settle_row['os_commis_amount'] = $settle_row['commission_amount'];
                //退还金额
                $add_settle_row['os_commis_return_amount'] = $settle_row['commission_return_amount'];
                //退还红包金额
                $add_settle_row['os_redpacket_return_amount'] = $settle_row['redpacket_return_amount'];
                //店铺促销活动费用
                $add_settle_row['os_shop_cost_amount'] = $settle_row['shop_cost'];
                //应结金额（订单金额（含运费）+红包金额-佣金金额-退单金额-退还红包金额+退还佣金-店铺费用+定金订单中的未退定金+下单时使用的平台红包-全部退款时应扣除的平台红包）
                $add_settle_row['os_amount'] = $settle_row['order_amount'] + $settle_row['redpacket_amount'] - $settle_row['commission_amount'] - $settle_row['return_amount'] - $settle_row['redpacket_return_amount'] + $settle_row['commission_return_amount'] - $settle_row['shop_cost'];
                //生成结算单时间
                $add_settle_row['os_datetime'] = get_date_time();
                //结算单年月
                $add_settle_row['os_date'] = date('Y-m');
                //状态
                $add_settle_row['os_state'] = Order_SettlementModel::SETTLEMENT_WAIT_OPERATE;
                //店铺id
                $add_settle_row['shop_id'] = $val['shop_id'];
                //店铺名
                $add_settle_row['shop_name'] = $val['shop_name'];
                //结算订单类型
                $add_settle_row['os_order_type'] = 0;
                $add_settle_row['district_id'] = $val['district_id'];

                Yf_Log::log($add_settle_row, Yf_Log::ERROR, 'jiesuan');
                fb($add_settle_row);
                die;
                $settlem_order_id = $this->addSettlement($add_settle_row,true);

                //结算单等待确认提醒
                //[start_time][end_time][order_id]
                $message = new MessageModel();
                $message->sendMessage('Settlement sheet for confirmation',$val['user_id'], $val['user_name'], $order_id = $settlem_order_id, $shop_name = NULL, 1, 1, $end_time = $end_time,$common_id=NULL,$goods_id=NULL,$des=NULL, $start_time = $start_time);
                die();
            }
        }
    }

    //结算虚拟订单
    public function settleVirtualOrder1()
    {
        //1.查找店铺的结算周期
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_info      = $Shop_BaseModel->getSettlementCycle();

        $Order_BaseModel = new Order_BaseModel();
        $Shop_CostModel  = new Shop_CostModel();
        foreach ($shop_info as $key => $val)
        {
            $start_unixtime       = '';
            $start_time           = '';
            $end_time             = '';
            $end_unixtime         = '';
            $settlement_last_info = array();
            //2.查找店铺上个结算单的结算时间
            $settlement_last_info = $this->getLastSettlementByShopid($val['shop_id'], Order_SettlementModel::SETTLEMENT_VIRTUAL_ORDER);

            $start_unixtime = $settlement_last_info['os_end_date'] ? $settlement_last_info['os_end_date'] : "";

            //若是新开店铺没有结算单，则从开店日期开始算
            if (!$start_unixtime)
            {
                $start_unixtime = $val['shop_create_time'];
            }

            $start_unixtime = strtotime($start_unixtime);
            $start_unixtime = $start_unixtime ? strtotime(date('Y-m-d 00:00:00', $start_unixtime) . "+1 day") : "";
            $start_time     = @date('Y-m-d H:i:s', $start_unixtime);

            $end_unixtime = $start_unixtime ? strtotime(date('Y-m-d 23:59:59', $start_unixtime) . "+" . $val['shop_settlement_cycle'] . " day") : "";
            $end_time     = @date('Y-m-d H:i:s', $end_unixtime);

            $time = time();

            if ($time > $end_unixtime)
            {
                $settle_row = array();
                //计算某月内，某店铺虚拟物订单的销量，退单量，佣金
                $order_cond_row = array(
                    'shop_id' => $val['shop_id'],
                    'order_finished_time:>' => 0,
                    'order_is_virtual' => 1,
                    'order_finished_time:>=' => $start_time,
                    'order_finished_time:<=' => $end_time
                );
                $settle_row     = $Order_BaseModel->settleOrder($order_cond_row);

                //计算某月内，某店铺实物订单的退款
                $Order_ReturnModel = new Order_ReturnModel();
                $return_cond_row = array();
                $return_cond_row['seller_user_id'] = $val['shop_id'];
                $return_cond_row['return_finish_time:>='] = $start_time;
                $return_cond_row['return_finish_time:<='] = $end_time;
                $return_cond_row['order_is_virtual'] = 1;

                $return_row = $Order_ReturnModel->settleReturn($return_cond_row);
                $settle_row['return_amount'] = $return_row['return_amount'];
                $settle_row['commission_return_amount'] = $return_row['return_commision_fee'];
                fb($return_cond_row);

                $add_settle_row = array();
                //结算单编号（年月日订单type店铺id）
                $add_settle_row['os_id'] = sprintf('%s%s%s', date('Ymd'), 1, $val['shop_id']);
                //开始时间
                $add_settle_row['os_start_date'] = $start_time;
                //结束时间
                $add_settle_row['os_end_date'] = $end_time;
                //订单金额
                $add_settle_row['os_order_amount'] = $settle_row['order_amount'];
                //退单金额
                $add_settle_row['os_order_return_amount'] = $settle_row['return_amount'];
                //佣金金额
                $add_settle_row['os_commis_amount'] = $settle_row['commission_amount'];
                //退还金额
                $add_settle_row['os_commis_return_amount'] = $settle_row['commission_return_amount'];
                //应结金额（消费金额（已消费的虚拟码+已过期未消费但不退款的虚拟码）-佣金）
                $add_settle_row['os_amount'] = $settle_row['order_amount'] - $settle_row['commission_amount'] - $settle_row['return_amount'] + $settle_row['commission_return_amount'];
                //生成结算单时间
                $add_settle_row['os_datetime'] = get_date_time();
                //结算单年月
                $add_settle_row['os_date'] = date('Y-m');
                //状态
                $add_settle_row['os_state'] = Order_SettlementModel::SETTLEMENT_WAIT_OPERATE;
                //店铺id
                $add_settle_row['shop_id'] = $val['shop_id'];
                //店铺名
                $add_settle_row['shop_name'] = $val['shop_name'];
                //结算订单类型
                $add_settle_row['os_order_type'] = 1;
                $add_settle_row['district_id'] = $val['district_id'];
                $settlem_order_id = $this->addSettlement($add_settle_row,true);

                //结算单等待确认提醒
                //[start_time][end_time][order_id]
                $message = new MessageModel();
                $message->sendMessage('Settlement sheet for confirmation',$val['user_id'], $val['user_name'], $order_id = $settlem_order_id, $shop_name = NULL, 1, 1, $end_time = $end_time,$common_id=NULL,$goods_id=NULL,$des=NULL, $start_time = $start_time);
            }

        }

    }


    //修复最后一次结算之前没有结算的订单(之前结算时受limit500影响，不能结算所有订单)
    public function repairSettlement($shop_id)
    {
        $Shop_BaseModel = new Shop_BaseModel();
        //查找店铺最后一次结算时间
        $shop_base = $Shop_BaseModel->getOne($shop_id);

        $last_settlement_time = $shop_base['shop_settlement_last_time'];

        //查找此结算时间之前没有结算的店铺
        $sql = 'select sum(order_payment_amount) order_amount,sum(order_shipping_fee) shipping_amount,sum(order_commission_fee) commission_amount,sum(order_rpt_price) redpacket_amount,sum(order_directseller_commission) order_directseller_commission
				from `yf_order_base`
				where shop_id='.$shop_id.' and order_is_settlement=0 and order_finished_time <="'.$last_settlement_time.'"';
        $Order_BaseModel = new Order_BaseModel();

        $data = $Order_BaseModel->sql->getAll($sql);



        //查找此结算时间之前没有结算的货到付款订单
        $sql1 = 'select sum(order_commission_fee) order_commission_fee
				from `yf_order_base`
				where shop_id='.$shop_id.' and order_is_settlement=0 and order_finished_time <="'.$last_settlement_time.'" and payment_id in (2,3)';

        $dat = $Order_BaseModel->sql->getAll($sql1);

        $commisson = $dat[0];


        $settle_row = $data[0];

        $add_settle_row = array();
        //结算单编号（年月日订单type店铺id）
        $prefix = sprintf('%s%s%s', date('Ymd'), 0, $shop_id);
        $Number_SeqModel = new Number_SeqModel();
        $order_number = $Number_SeqModel->createSeq($prefix);
        $add_settle_row['os_id'] = sprintf('%s', $order_number);
        //开始时间
        $add_settle_row['os_start_date'] = $shop_base['shop_create_time'];
        //结束时间
        $add_settle_row['os_end_date'] = $last_settlement_time;
        //订单金额
        $add_settle_row['os_order_amount'] = $settle_row['order_amount'];
        //红包金额
        $add_settle_row['os_redpacket_amount'] = $settle_row['redpacket_amount'];
        //分销金额
        $add_settle_row['os_directseller_amount'] = $settle_row['order_directseller_commission'];
        //运费
        $add_settle_row['os_shipping_amount'] = $settle_row['shipping_amount'];
        //退单金额
        $add_settle_row['os_order_return_amount'] = 0;
        //佣金金额
        $add_settle_row['os_commis_amount'] = $settle_row['commission_amount'] + $commisson['order_commission_fee'];
        //退还金额
        $add_settle_row['os_commis_return_amount'] = 0;
        //退还红包金额
        $add_settle_row['os_redpacket_return_amount'] = 0;
        //店铺促销活动费用
        $add_settle_row['os_shop_cost_amount'] = 0;
        //应结金额（订单金额（含运费）+红包金额-佣金金额-退单金额-退还红包金额+退还佣金-店铺费用+定金订单中的未退定金+下单时使用的平台红包-全部退款时应扣除的平台红包）
        $add_settle_row['os_amount'] = $settle_row['order_amount'] + $settle_row['redpacket_amount'] - $settle_row['commission_amount'] - $settle_row['return_amount'] - $settle_row['redpacket_return_amount'] + $settle_row['commission_return_amount'] - $settle_row['shop_cost'] - $settle_row['order_directseller_commission'];
        //生成结算单时间
        $add_settle_row['os_datetime'] = get_date_time();
        //结算单年月
        $add_settle_row['os_date'] = date('Y-m');
        //状态
        $add_settle_row['os_state'] = Order_SettlementModel::SETTLEMENT_WAIT_OPERATE;
        //店铺id
        $add_settle_row['shop_id'] = $shop_id;
        //店铺名
        $add_settle_row['shop_name'] = $shop_base['shop_name'];
        //结算订单类型
        $add_settle_row['os_order_type'] = 0;

        $flag = $this->addSettlement($add_settle_row);



        $sql2 = 'UPDATE `yf_order_base` SET order_settlement_time = '.get_date_time().',order_is_settlement='.Order_SettlementModel::SETTLEMENT_WAIT_OPERATE.'
				where shop_id='.$shop_id.' and order_is_settlement=0 and order_finished_time <="'.$last_settlement_time.'"';
        $Order_BaseModel = new Order_BaseModel();

        $Order_BaseModel->sql->exec($sql2);


        return $flag;

    }



}

?>