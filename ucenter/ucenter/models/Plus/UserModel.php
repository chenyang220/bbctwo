<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/15
 * Time: 17:57
 * @author fu zhehao
 */
class Plus_UserModel extends Plus_User
{
    /**
     *
     * 查询Plus会员信息
     */
    public function  getPlususerInfo($uid){
        $plusUserInfo = $this->getOne($uid);
        $endDate =  $plusUserInfo['end_date'];
        if ($endDate - time() <= 0) {  //如果会员过期则改变状态 user_status = 3
            $field_row =array(
                'user_status'=>Plus_UserModel::$user_status[3],
            );
            $this->editPlusUserInfo($uid, $field_row,$flag = false);
        }
        return $this->getOne($uid);
    }

    /**
     * 修改会员信息
     */
    public function editPlusUserInfo($user_id=null, $field_row,$flag = false)
    {
        return $this->edit($user_id, $field_row,$flag);
    }
}