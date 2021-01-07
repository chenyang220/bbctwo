<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Bargain_JoinUserModel extends Bargain_JoinUser
{
    //根据buy_id获取参加活动的会员
    public function getJoinUserList($buy_id)
    {
        $data = $this->getByWhere(array('buy_id'=>$buy_id));
        return $data;
    }

    //根据buy_id获取参与用户信息
    public function getJoinUser($buy_id)
    {
        $sql = "SELECT bargain_join_user.*,user_info.user_name,user_info.user_logo FROM ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "bargain_join_user AS bargain_join_user JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "user_info AS user_info ON ";
        $sql .= "bargain_join_user.user_id = user_info.user_id ";
        $sql .= "where 1 AND bargain_join_user.buy_id = " . $buy_id;
        $result = $this->sql->getAll($sql);
        return $result;
    }


}

?>