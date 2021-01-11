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

    //终止执行下面内容, 否则会执行两次
    return ;
}

$att =11;
Yf_Log::log($att, Yf_Log::INFO, '2222');


$file_name_row = pathinfo(__FILE__);
$crontab_file = $file_name_row['basename'];

fb($crontab_file);
//执行任务

//  $redPacketTempModel = new RedPacket_TempModel();
//  $Web_ConfigModel = new Web_ConfigModel();
//  $data_tmp = $Web_ConfigModel->getOneByWhere(array("config_key"=>'plus_general_date'));
//  $arr = $redPacketTempModel-> getOneByWhere(array("redpacket_t_start_date"=>$data_tmp['config_value']));
//  $data_price = $Web_ConfigModel->getOneByWhere(array("config_key"=>'plus_general_red'));
//  $startdate = strtotime($data_tmp['config_value'])+60; //红包开始日期
//  $endate = strtotime("+7 day", $startdate); //红包到期时间
// // $end_date = date('Y-m-d H:i', $date);
// // $datesd = strtotime($data_tmp['config_value'])+60;
// $starttime = date('Y-m-d H:i',$startdate);
// $nowtime = date('Y-m-d H:i');
// if(empty($arr))
// {
//     $arr['redpacket_t_plustartime'] = $startdate; //结束日期
//     $arr['redpacket_t_plusendtime'] = $endate; //结束日期
//     $arr['redpacket_t_price'] = $data_price['config_value']; //红包面额
//     $arr['redpacket_t_title'] = "plus超级会员七日红包"; //红包名称
//     $arr['redpacket_t_desc'] = "plus超级会员七日红包"; //红包名称
//     $arr['redpacket_t_state'] = "1"; //红包名称
//     $flag= $redPacketTempModel->addRedPacketTemp($arr);
//  }
     // if($nowtime == $starttime)
     //    {
            // $Plus_UserModel = new Plus_UserModel();
            // $redPacketBaseModel = new RedPacket_BaseModel();
            // $sql = "select * from yf_plus_user where 1=1 and user_status=2";
            // $result = $Plus_UserModel->sql->getAll($sql);
            // foreach ($result as $key => $value)
            // {
            //     $arrs['redpacket_active_date'] = $data_tmp['config_value']; //发放日期
            //     $arrs['redpacket_start_date'] = $data_tmp['config_value']; //发放日期
            //     $arrs['redpacket_end_date'] = $end_date; //发放日期
            //     $arrs['redpacket_price'] = $data_price['config_value']; //红包面额
            //     $arrs['redpacket_title'] = "plus超级会员七日红包"; //红包名称
            //     $arrs['redpacket_desc'] = "plus超级会员七日红包"; //红包名称
            //     $arrs['redpacket_state'] = 1; //红包状态
            //     $arrs['redpacket_owner_id'] =$value['user_id']; //使用者id
            //     $redPacketBaseModel->addRedPacket($arrs);
            // }
        // }


         // $db = new YFSQL();
         // $sqlyue = "SELECT redpacket_t_plusendtime,redpacket_t_id from yf_redpacket_template where redpacket_t_title='plus超级会员七日红包' and redpacket_t_state = 1 order by redpacket_t_plusendtime ASC LIMIT 0,1 ";
         // $datat = $db->find($sqlyue);
         //    if(time() > $datat[0]['redpacket_t_plusendtime'])
         //    {
         //          $arrs['redpacket_t_state'] = 3; //红包状态过期
         //          $statusy = $redPacketTempModel->editRedPacketTemp($datat[0]['redpacket_t_id'],$arrs);
         //          $sqlbases = "SELECT redpacket_id from yf_redpacket_base where redpacket_desc='plus超级会员七日红包' and redpacket_state = 1 and redpacket_active_date=".'"'.$data_tmp['config_value'].'"';
         //          $dataserd = $db->find($sqlbases);
         //          foreach ($dataserd as $key => $value)
         //          {
         //          $sqlbaer = "UPDATE yf_redpacket_base set redpacket_state =3 where redpacket_id='".$value['redpacket_id']."'";
         //          $flag = $db->find($sqlbaer);
         //          }
         //    }




$flag = true;
return $flag;
?>