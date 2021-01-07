<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Api接口, 让App等调用
 *
 *
 * @category   Game
 * @package    User
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2015 远丰仁商
 * @version    1.0
 * @todo
 */
class PaymentWay_Tenpay  extends PaymentWay_Base
{
	/**
	 * bestpay 翼支付
	 *
	 */
	public function tenpay()
	{
		$trade_id = request_string('trade_id');
		echo "on devloping...";
	}
}