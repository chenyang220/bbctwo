<?php
if (!defined('ROOT_PATH'))
{
    if (is_file('../../../shop/configs/config.ini.php'))
    {
        require_once '../../../shop/configs/config.ini.php';
    }
    else
    {
        die('请先运行index.php,生成应用程序框架结构！');
    }

    //不会重复包含, 否则会死循环: web调用不到此处, 通过crontab调用
    $Base_CronModel = new Base_CronModel();
    $rows = $Base_CronModel->checkTask(); //并非指执行自己, 将所有需要执行的都执行掉, 如果自己达到执行条件,也不执行.

//    终止执行下面内容, 否则会执行两次
    return ;
}

Yf_Log::log(__FILE__, Yf_Log::INFO, 'crontab');

$file_name_row = pathinfo(__FILE__);
$crontab_file = $file_name_row['basename'];

//检查砍价活动状态
$Bargain_BaseModel = new Bargain_BaseModel();
$Bargain_BuyUserModel = new Bargain_BuyUserModel();

//活动过期
$cond_row['bargain_status'] = Bargain_BaseModel::ISON;//状态正常
$cond_row['end_time:<'] = time();//活动到期
$bargain_ids = $Bargain_BaseModel->getKeyByWhere($cond_row);
$rs_row = array();
if($bargain_ids){
    //活动状态
    $field_row['bargain_status'] = Bargain_BaseModel::ISOFF;//活动过期
    $base_flag = $Bargain_BaseModel->editBargain($bargain_ids, $field_row);
    check_rs($base_flag, $rs_row);

    //会员参与购买状态
    $cond_user['bargain_id:IN'] = $bargain_ids;
    $cond_user['bargain_state'] = Bargain_BuyUserModel::ISON;
    $buy_rows = $Bargain_BuyUserModel->getByWhere($cond_user);
    $buy_ids = array_column($buy_rows,'buy_id');
    $user_ids1 = array_column($buy_rows, 'user_id');

    //砍价库存修改
    $rs = array();
    if ($user_ids1) {
        $user_ids1 = implode(',', $user_ids1);
        $sql1 = "Select bargain_id,count(*) AS sum FROM " . TABEL_PREFIX . "bargain_buy_user WHERE user_id IN (" . $user_ids1 . ") AND bargain_state = 0 GROUP BY bargain_id";
        $rs = $Bargain_BuyUserModel->sql->getAll($sql1);
    }
    if ($rs) {
        $Bargain_BaseModel = new Bargain_BaseModel();
        foreach ($rs as $k => $v) {
            $bargain_base = $Bargain_BaseModel->getOne($v['bargain_id']);
            $edits1 = array();
            $edits1['bargain_stock'] = (int)$bargain_base['bargain_stock'] + (int)$v['sum'];
            $flags = $Bargain_BaseModel->editBargain($v['bargain_id'], $edits1);
            check_rs($flags, $rs_row);
        }
    }
    if ($buy_ids) {
        $field_buy['bargain_state'] = Bargain_BuyUserModel::FAILURE;//砍价失败
        $buy_flag = $Bargain_BuyUserModel->editBuyUser($buy_ids, $field_buy);
        check_rs($buy_flag, $rs_row);
    }
}else{
    $flags = true;
    check_rs($flags, $rs_row);
}

//会员砍价过期，24小时有效
$user_row = array();
$user_row['bargain_state'] = Bargain_BuyUserModel::ISON;//正在砍价
$user_row['user_end_time:<'] = time();//活动到期
$user_buy = $Bargain_BuyUserModel->getByWhere($user_row);
$user_buy_ids = array_column($user_buy, 'buy_id');
$user_ids = array_column($user_buy, 'user_id');
if ($user_buy_ids) {
    //砍价库存修改
    $res = array();
    if ($user_ids) {
        $user_ids = implode(',', $user_ids);
        $sql = "Select bargain_id,count(*) AS sum FROM " . TABEL_PREFIX . "bargain_buy_user WHERE user_id IN (" . $user_ids . ")  AND bargain_state = 0 GROUP BY bargain_id";
        $res = $Bargain_BuyUserModel->sql->getAll($sql);
    }
    if ($res) {
        $Bargain_BaseModel = new Bargain_BaseModel();
        foreach ($res as $k => $v) {
            $bargain_base = $Bargain_BaseModel->getOne($v['bargain_id']);
            $edits = array();
            $edits['bargain_stock'] = (int)$bargain_base['bargain_stock'] + (int)$v['sum'];
            $flags = $Bargain_BaseModel->editBargain($v['bargain_id'], $edits);
            check_rs($flags, $rs_row);
        }
    }
    //砍价状态
    $user_field_row['bargain_state'] = Bargain_BuyUserModel::FAILURE;//活动过期
    $user_flag = $Bargain_BuyUserModel->editBuyUser($user_buy_ids, $user_field_row);
    check_rs($user_flag, $rs_row);
} else {
    $user_flag1 = true;
    check_rs($user_flag1, $rs_row);
}

//活动开始
$cond['bargain_status'] = Bargain_BaseModel::WILLON;//未开始
$cond['start_time:<='] = time();//活动到期
$bargain_id = $Bargain_BaseModel->getKeyByWhere($cond);
if ($bargain_id) {
    //活动状态
    $field_row = array();
    $field_row['bargain_status'] = Bargain_BaseModel::ISON;//活动过期
    $base_flag1 = $Bargain_BaseModel->editBargain($bargain_id, $field_row);
    check_rs($base_flag1, $rs_row);
} else {
    $flags1 = true;
    check_rs($flags1, $rs_row);
}
$flag = is_ok($rs_row);

return $flag;
?>

