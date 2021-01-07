<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Api_Seller_ExternalCtl extends Seller_Controller
{
    /**
     * 对外快速注册接口
     */
    public function Modify(){
        $data['u_id'] = request_int('u_id');
        $data['user_name'] = request_string('user_mobile');
        $uid = $data['u_id'];
        $user_name = $data['user_name'];
        if (!$user_name) {
            return $this->data->addBody(-140,array(), "手机号不能为空！", 250);
        }
        if (!$uid) {
            return $this->data->addBody(-140,array(), "用户ID不能为空！", 250);
        }
        $User_InfoModel = new User_InfoModel();
        $User_BaseModel = new User_BaseModel();
        $db = new YFSQL();
        $sql = "SELECT  * FROM ucenter_user_info  where `u_id` = $uid ";
        $ucenter_user_info = $db->find($sql);
        $user_info = current($ucenter_user_info); 
        $User_InfoModel->sql->startTransactionDb();
        $edit_user_info = $User_InfoModel->editInfo($user_info['user_id'],array('user_name'=>$user_name,"user_mobile"=>$user_name));
        check_rs($edit_user_info, $rs_row);
        $edit_user_base = $User_BaseModel->editBase($user_info['user_id'],array('user_account'=>$user_name));
        check_rs($edit_user_base, $rs_row);
        $sql1 = "UPDATE ucenter_user_info_detail SET  `user_name` = $user_name,`nickname` = $user_name,`user_tel` = $user_name where `user_name` = " . $user_info['user_name'];
        $ucenter_user_info_detail = $db->update($sql1); 
        check_rs($ucenter_user_info_detail, $rs_row);
        $sql2 = "UPDATE  ucenter_user_info SET  `user_name` = $user_name where `u_id` =" . $uid ;
        $ucenter_user_info = $db->update($sql2);
        check_rs($ucenter_user_info, $rs_row);
        $sql3 = "UPDATE  pay_user_base SET  `user_account` = $user_name where `user_id` =" . $user_info['user_id'] ;
        $pay_user_base = $db->update($sql3);
        check_rs($pay_user_base, $rs_row);
        $sql4 = "UPDATE  pay_user_info SET  `user_mobile` = $user_name,`user_nickname` = $user_name where `user_id` =" . $user_info['user_id'] ;
        $pay_user_info = $db->update($sql4);
        check_rs($pay_user_info, $rs_row);
        $flag = is_ok($rs_row);
        // exit;
        if($flag){
            $User_InfoModel->sql->commitDb();
            $msg = __('修改成功');
            $status = 200;
        }else{
            $User_InfoModel->sql->rollBackDb();
            $msg = __('修改失败');
            $status = 250;
        }
        $this->data->addBody(-140,array(), $msg, $status);
    }
     
    // public function Modify(){
    //     $data['u_id'] = request_int('u_id');
    //     $data['user_name'] = request_string('user_mobile');
    //     $uid = $data['u_id'];
    //     $user_name = $data['user_name'];
    //     $db = new YFSQL();
    //     $sql = "SELECT  * FROM ucenter_user_info  where `u_id` = $uid ";
    //     $ucenter_user_info = $db->find($sql);
    //     $user_info = current($ucenter_user_info); 
    //     $sql1 = "UPDATE  ucenter_user_info SET  `user_name` = $user_name where `u_id` = $uid ";
    //     $data = $db->find($sql1);
    //     $sql2 = "UPDATE ucenter_user_info_detail SET  `user_name` = $user_name,`nickname` = $user_name,`user_tel` = $user_name,`user_mobile` = $user_name where `user_name` = " . $user_info['user_name'];
    //       $data = $db->find($sql2);
    //     if(empty($data)){
    //         $msg = __('修改成功');
    //         $status = 200;
    //     }else{

    //         $msg = __('修改失败');
    //         $status = 250;
    //     }
    //     $this->data->addBody(-140,$data, $msg, $status);
    // }

}