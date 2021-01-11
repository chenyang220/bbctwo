<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class User_ResourceModel extends User_Resource
{

	/**
	 * 读取分页列表
	 *
	 * @param  int $user_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getResourceList($cond_row= array(), $page=1, $rows=100, $sort='asc')
	{
	    $data = array();
	    $sql = 'SELECT SUM(user_money) user_money_sum,SUM(user_money_frozen) user_money_frozen_sum,SUM(user_recharge_card) user_recharge_card_sum,SUM(user_recharge_card_frozen) user_recharge_card_frozen_sum FROM '.TABEL_PREFIX.'user_resource';
        $rows = $this->sql->getAll($sql);

        $data = current($rows);

		return $data;
	}

    //用余额支付后冻结用户余额
    public function frozenUserMoney($user_id = null,$amount = 0)
    {
        $data = $this->getOne($user_id);

        if($data['user_money'] < $amount)
        {
            return false;
        }
        else
        {
            $eidt_row['user_money'] = $amount*(-1);
            //$eidt_row['user_money_frozen'] = $amount;

            $flag = $this->editResource($user_id,$eidt_row,true);
        }

        return $flag;
    }

    //用充值卡支付后冻结用户余额
    public function frozenUserCards($user_id = null,$amount = 0)
    {
        $data = $this->getOne($user_id);

        if($data['user_recharge_card'] < $amount)
        {
            return false;
        }
        else
        {
            $eidt_row['user_recharge_card'] = $amount*(-1);
            //$eidt_row['user_recharge_card_frozen'] = $amount;

            $flag = $this->editResource($user_id,$eidt_row,true);
        }

        return $flag;
    }

    public function getCreditReturnUserId($smybol = '')
    {
        $sql = "
					SELECT
						user_id
					FROM
						" . TABEL_PREFIX . "user_resource where user_credit_limit>0 ". $smybol ."
					";
        $rows = $this->sql->getAll($sql);

        return $rows;
    }

    /**
     * @param user_id
     * @param amount
     * @return boolean
     * 转账过期
     * pay_user_resource用户余额从冻结金额还原
     */
    public function expireTransferMoney($user_id, $amount)
    {
        return $this->editResource($user_id, [
            'user_money'=> $amount,
            'user_money_frozen'=> -$amount
        ], true);
    }
}
?>