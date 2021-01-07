<?php
//extends Yf_AppController
class TeCtl extends Controller
{
	public function settlement()
	{
		//店铺id
		$shop_id = request_int('shop_id');

		//结算开始时间
		$se_start_time = request_string('start');
		//结算结束时间
		$se_end_time = request_string('end');


		$Shop_BaseModel = new Shop_BaseModel();
		$Order_BaseModel = new Order_BaseModel();
		$Shop_CostModel = new Shop_CostModel();

		//查找店铺信息
		$shop_base = $Shop_BaseModel->getOne($shop_id);

		//如果有结算开始时间就从开始时间开始结算，如果没有开始时间就从店铺的创建时间开始结算
		if(!$se_start_time)
		{
			$start_unixtime = strtotime($se_start_time);
		}
		else
		{
			$start_unixtime = strtotime($shop_base['shop_create_time']);
		}

		$start_unixtime = $start_unixtime ? strtotime(date('Y-m-d 00:00:00', $start_unixtime) . "+1 day") : "";
		$start_time     = @date('Y-m-d H:i:s', $start_unixtime);

		$end_unixtime = $start_unixtime ? strtotime(date('Y-m-d 23:59:59', $start_unixtime) . "+" . ($shop_base['shop_settlement_cycle']-1) . " day") : "";
		$end_time     = @date('Y-m-d H:i:s', $end_unixtime);

		//如果计算出来的结算结束时间在需要结算的时间内就进行结算
		if($end_time < $se_end_time)
		{
			$time = time();

			$data = array();

			$settle_row = array();
			//计算某月内，某店铺所有订单的销量，退单量，佣金
			$order_cond_row = array(
				'shop_id' => $shop_id,
				'order_finished_time:>' => 0,
				'order_finished_time:>=' => $start_time,
				'order_finished_time:<=' => $end_time,
			);
			$settle_row     = $Order_BaseModel->settleOrder($order_cond_row);


			echo "<pre>";
				print_r($settle_row);
			die;



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
			$settle_row['redpacket_return_amount']  = $return_row['redpacket_return_amount'];

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
			$add_settle_row['os_directseller_amount'] = $settle_row['order_directseller_commission'];
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
			$add_settle_row['os_amount'] = $settle_row['order_amount'] + $settle_row['redpacket_amount'] - $settle_row['commission_amount'] - $settle_row['return_amount'] - $settle_row['redpacket_return_amount'] + $settle_row['commission_return_amount'] - $settle_row['shop_cost'] - $settle_row['order_directseller_commission'];
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

			$Order_SettlementModel = new Order_SettlementModel();
			$flag = $Order_SettlementModel->addSettlement($add_settle_row);

			$data['flag'] = $flag;
			$data['os_id'] = $add_settle_row['os_id'];
			$data['end_time'] = $end_time;
			$data['start_time'] = $start_time;

		}

		return $data;
	}


}