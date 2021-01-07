<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class App_NotifyModel extends App_Notify
{
    /**
     * @param $user_id
     * @return array
     * 获取用户notify
     */
    public function getOneNotify($cond_row){

        return $this->getOneByWhere($cond_row);

    }

}

?>