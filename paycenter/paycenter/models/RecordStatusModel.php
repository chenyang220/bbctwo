<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class RecordStatusModel
{
	const IN_HAND = 1; //处理中
	const RECORD_FINISH = 2; //交易完成
	const RECORD_CANCEL = 3; //交易取消
	const RECORD_FAIL = 4; //交易失败
	const RECORD_WAIT_SEND_GOODS = 5; //待发货
	const RECORD_WAIT_CONFIRM_GOODS = 6; //待收货
	const ORDER_PRESALE_DEPOSIT = 20; //定金已支付

	public function __construct()
	{
		$this->recordStatus = array(
			'1' => __('处理中'),
			'2' => __('交易完成'),
			'3' => __('交易取消'),
			'4' => __('交易失败'),
			'20' => __('定金已支付'),

		);
		$this->userType = array(
			'1' => __('收款方'),
			'2' => __('付款方'),
            '3'=>_('管理员'),
		);

	}

}
?>