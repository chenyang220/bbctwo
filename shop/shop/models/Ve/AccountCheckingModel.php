<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * 银联对账单
 */
class Ve_AccountCheckingModel extends Ve_AccountChecking
{
    //支付方式
	private static $payway = array(
		'01'=> '现金', 
		'02'=> '刷卡（银行卡)', 
		'03'=> '预付卡',
		'04'=>'抵用券' ,
		'05'=> '支票',
		'06'=> '积分', 
		'94'=> '银联二维码', 
		'95'=> '账户快捷', 
		'96'=> '账户余额' ,
		'97'=> '微信', 
		'98'=> '支付宝', 
		'99'=> '其它'
	);

	//交易类型
	private static $txntype = array(
		'01'=> '消费', 
		'02'=> '消费撤销', 
		'03'=> '退货',
		'9123'=>'差错-结算退单' ,
		'9127'=> '差错-二次结算退单'
	);
	//运单类型
	private static $ordertype = array(
		'01'=> '普通运单', 
		'02'=> '快速签单', 
		'03'=> '合并签单',
		'04'=>'组合支付签单'
	);
   /**
    * 储存银联对账单
    * 
    * @dateTime  2020-06-15
    * @author fzh
    * @copyright https://www.yuanfeng.cn
    * @license   仅限本公司授权用户使用。
    * @version   3.8.1
    */
   public function addPayInfo($field_row, $return_insert_id = false){

   	 $field_row['payway'] = self::$payway[$field_row['payway']];
   	 $field_row['txntype'] = self::$txntype[$field_row['txntype']];
   	 $field_row['ordertype'] = self::$ordertype[$field_row['ordertype']];
   	 $this->addInfo($field_row, $return_insert_id);
   }
   //更新订单确认时间
   public function editConfirmOrderTime(){
   	 $orderno = request_string('orderno');
   	 $codmercode = request_string('codmercode');
   	 $sql = "UPDATE yf_veaccount_checking SET comfirmtime = " ."'". time() . "'"." WHERE orderno = " ."'". $orderno . "'"." and codmercode = "."'".$codmercode."'";
     $this->sql->exec($sql);
   }
}

?>