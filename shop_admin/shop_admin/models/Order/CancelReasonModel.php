<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Order_CancelReasonModel extends Order_CancelReason
{
	const CANCEL_BUYER  = 1;    //���ȡ������
	const CANCEL_SELLER = 2;    //����ȡ������

}

?>