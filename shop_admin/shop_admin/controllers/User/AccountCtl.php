<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class User_AccountCtl extends AdminController
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

    public function editAccount()
    {
        $user_id = request_int('user_id');
        $info = array();
        if($user_id){
            $User_AccountModel = new User_AccountModel();
            $info = $User_AccountModel->getOne($user_id);
        }
        include $this->view->getView();
    }

    //账号列表
    public function getUserList1()
    {
        $formvars['page'] = request_int('page');
        $formvars['rows'] = request_int('rows');
        $formvars['user_for'] = request_string('user_for');
        $formvars['met'] = 'getUserList';
        $init_rs = $this->UCenterUrl($formvars);
        if($init_rs['status'] == 200){
            $data = $init_rs['data'];
            $msg = 'success';
            $status = 200;
        }else{
            $data = array();
            $msg = 'failure';
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    //UCenter PayCenter账号列表
    public function getUserList()
    {
        $page = request_int('page',1);
        $rows = request_int('rows');
        $user_for = request_string('user_for');
        $cond_row = array();
        if($user_for){
            $cond_row['user_for'] = $user_for;
        }
        $cond_row['is_del'] = 1;
        $order_row = array();
        $User_AccountModel = new User_AccountModel();
        $user_list = $User_AccountModel->getAccountList($cond_row, $order_row,$page,$rows);
        $this->data->addBody(-140, $user_list);
    }

    //删除账号
    public function delUserAccount()
    {
        $rs_row = array();
        $id = request_int('id');
        $User_AccountModel = new User_AccountModel();
        $info = $User_AccountModel->getOne($id);
        $flag1 = $User_AccountModel->removeAccount($id);
        check_rs($flag1, $rs_row);

        //删除对应表用户信息
        if ($info['user_for'] == 'UCenter') {
            $table = "ucenter_admin_user_base";
        } else {
            $table = "pay_admin_user_base";
        }
        $sql = "DELETE FROM {$table} WHERE user_id={$id}";
        $res = $User_AccountModel->sql->getAll($sql);
        check_rs($res, $rs_row);

        $formvars['met'] = 'delAccount';
        $formvars['id'] = $id;
        $init_rs = $this->UCenterUrl($formvars);
        if($init_rs['status'] == 200){
            $re = true;
        }else{
            $re = false;
        }
        check_rs($re, $rs_row);
        $flag = is_ok($rs_row);
        if ($flag !== false) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    //添加、修改
    public function addOrEditAccountInfo()
    {
        $user_id = request_int('user_id');
        $cond_row = array();
        $formvars = array();
        if($user_id){
            $cond_row['user_id'] = $user_id;
            $formvars['user_id'] = $user_id;
        }
        $enable = request_string('enable');
        if ($enable) {
            $cond_row['enable'] = $enable;
        }
        $user_administrator = request_string('user_administrator');
        if($user_administrator){
            $cond_row['user_administrator'] = $user_administrator;
        }
        $user_for = request_string('user_for');
        if ($user_for) {
            $cond_row['user_for'] = $user_for;
        }
        $user_name = request_string('user_name');
        if ($user_name) {
            $cond_row['user_name'] = $user_name;
            $formvars['user_name'] = $user_name;
        }
        $password = request_string('password');
        if ($password) {
            $formvars['password'] = $password;
        }

        $formvars['met'] = 'addOrEditAccountInfo';
        $init_rs = $this->UCenterUrl($formvars);
        if($init_rs['status'] == 200){
            $User_AccountModel = new User_AccountModel();
            if($user_id){
                $flag = $User_AccountModel->editAccount($user_id, $cond_row);
            }else{
                $id = $init_rs['data']['id'];
                $cond_row['user_id'] = $id;
                $flag = $User_AccountModel->addAccount($cond_row);

                //写入对应admin主表
                if($user_for == 'UCenter'){
                    $table = "ucenter_admin_user_base";
                }else{
                    $table = "pay_admin_user_base";
                }
                $sql = "INSERT INTO {$table} VALUES ('{$id}', '{$user_name}', '', '', '', '', '', '',1,'',0,0,0)";
                $res = $User_AccountModel->sql->getAll($sql);
            }
        }else{
            $flag = false;
            $msg = $init_rs['msg'];
        }

        if($flag !== false){
            $msg = 'success';
            $status = 200;
        }else{
            $msg = $msg ? $msg : 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $init_rs, $msg, $status);
    }

    public function UCenterUrl($formvars)
    {
        //本地读取远程信息
        $key = Yf_Registry::get('ucenter_api_key');;
        $url = Yf_Registry::get('ucenter_api_url');
        $ucenter_app_id = Yf_Registry::get('ucenter_app_id');
        $formvars['app_id'] = $ucenter_app_id;
        $formvars['ctl'] = 'Index';
        $formvars['typ'] = 'json';
        $init_rs = get_url_with_encrypt($key, $url, $formvars);
        return $init_rs;
    }

}

?>