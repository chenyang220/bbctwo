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
     * 构造函数，在控制器实例化时自动运行
     */
    public function __construct(){
        parent::__construct();
        //对过期的会员进行状态处理
        $sql = 'select * from '.TABEL_PREFIX.'plus_user';
        $plus_users = $this->sql->getAll($sql);
        foreach ($plus_users as $plus_user) {
            if ($plus_user['end_date'] <= get_time()) {
              $field_row =array(
                'user_status'=>Plus_UserModel::$user_status[3],
               );
             $this->editPlusUserInfo(intval($plus_user['user_id']), $field_row,$flag = false);
            }
        }
    }
	/**
	 * 读取分页列表
	 *
	 * @param  array $cond_row 查询条件
	 * @param  array $order_row 排序信息
	 * @param  array $page 当前页码
	 * @param  array $rows 每页记录数
	 * @return array $data 返回的查询内容
	 * @access public
	 */
	public function getPlusUserList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{

		$items = array();
		$data  = array();
		//引入数据库配置信息
		$dbinfo = include APP_PATH.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'db.ini.php';
		//链接数据库和指定字符集
		$conn = mysqli_connect($dbinfo['host'],$dbinfo['user'],$dbinfo['password']);
		        mysqli_select_db($conn,$dbinfo['database']);
		        mysqli_set_charset("set names utf8");
		 //构造sql语句
		 $sql = 'select pu.*,u.user_name as user_name,u.user_mobile as user_mobile from '.TABEL_PREFIX.'plus_user pu 
		 		left join yf_user_info u on pu.user_id=u.user_id';
		 //拼接sql查询条件
		 if (!empty($cond_row)) {
		 	$i = 0; //计数器
		 	foreach ($cond_row as $k => $v) {
		 		if ($k=='user_name') {
		 		    $i?$sql .= ' and  u.'.$k.'="'.$v.'"':$sql .= ' where  u.'.$k.'="'.$v.'"';
		 		    $i++;
		 		}
		 		if ($k=='user_status') {
		 			$i?$sql .= ' and  pu.'.$k.'='.$v:$sql .= ' where  pu.'.$k.'='.$v;
		 		    $i++;
		 		}
		 	}
		 }
		 //执行sql
		 $result  = mysqli_query($conn,$sql);
		 $totalsize = $result->num_rows;  //总条数
		 $total = ceil($totalsize/$rows); //总页数

		 $offset = ($page - 1) * $rows;
		 $sql .= ' limit '. $offset.','.$rows;
		 $result  = mysqli_query($conn,$sql);
		 while (false != ($row = mysqli_fetch_assoc($result))) {
		 	//字符串替换
		 	switch ($row['user_status']) {
		 		case 1:
		 			$row['user_status'] = '试用中';
		 			break;
		 		case 2:
		 			$row['user_status'] = '未到期';
		 			break;
		 		case 3:
		 			$row['user_status'] = '已到期';
		 			break;
		 		default:
		 			$row['user_status'] = '未开通';
		 			break;
		 	}
		 	$row['end_date'] = date('Y-m-d H:i:s',$row['end_date']);
		 	//封装数组
		 	array_push($items, $row);
		 }
		mysqli_free_result($result);
		mysqli_close($conn);

		//封装数据
		$data['page'] = $page;
		$data['total'] = $total;
		$data['totalsize'] = $totalsize;
		$data['records'] = $totalsize;
		$data['items'] = $items;
		
		return $data;
	}

	//判断会员是否是plus会员
	public function getPlusUserStatus($user_id='')
    {
        $sql = 'select * from '.TABEL_PREFIX.'plus_user where user_id='.$user_id;
        $plus_user = $this->sql->getAll($sql);
        $status = 0;
        if($plus_user[0]['user_status'] == 1 || $plus_user[0]['user_status'] == 2) {
            $status = 1;
        }

        return $status;
    }

    /**
     *
     * 查询Plus会员信息
     */
    public function  getPlususerInfo($uid){
        return $this->getOne($uid);
    }

    /**
     * 修改会员信息
     */
    public function editPlusUserInfo($user_id=null, $field_row,$flag = false)
    {
        return $this->edit($user_id, $field_row,$flag);
    }
    /**
     *
     * 下发红包
     */
    public function publishRedpacket($user_id,$red_packet_t_id){
        $cond_row = array();
        $cond_row['redpacket_t_id'] = $red_packet_t_id;
        $cond_row['redpacket_t_state'] = RedPacket_TempModel::VALID;
        $cond_row['redpacket_t_end_date:>='] = get_date_time();
        //获取平台优惠券详情
        $redPacketTempModel = new RedPacket_TempModel();
        $redPacketBaseModel = new RedPacket_BaseModel();
        $row = $redPacketTempModel->getRedPacketTempInfoByWhere($cond_row);
        $redpacket_start_time = strtotime($row['redpacket_t_start_date']);
        $redpacket_now_time = time();
        $flag =fasle;
        if ($redpacket_now_time < $redpacket_start_time) {
            $msg = __('未到领取时间');
            $data = array();
            $this->data->addBody(-140, $data, $msg, 250);
        } else {
            if ($row) {
                $ava_flag = true;
                if ($row['redpacket_t_total'] == $row['redpacket_t_giveout'] && $row['redpacket_t_giveout'] != 0) {
                    $ava_flag = false;
                    $msg = __('红包已被领完');
                } else {
                    //判断当月是否已领取
                    $sql = "select * from yf_redpacket_base where 1=1 and redpacket_t_id='{$red_packet_t_id}' and redpacket_owner_id ='{$user_id}' order by redpacket_id desc limit 1";
                    $ret = $redPacketBaseModel->sql->getRow($sql);
                    if ($ret )  //如果限制每个人每月的限领张数
                    {
                        $m = date('Y-m', time());
                        $give_m = date('Y-m', strtotime($ret['redpacket_active_date']));
                        ($m==$give_m) && $ava_flag = false;
                    }
                }
                if ($ava_flag) {
                    $userBaseModel = new User_BaseModel();
                    $user_data = $userBaseModel->getOne($user_id);
                    $rs_row = array();
                    $redPacketBaseModel->sql->startTransactionDb(); //开启事务
                    $field_row['redpacket_code'] = $redPacketBaseModel->get_rpt_code($user_id);
                    $field_row['redpacket_t_id'] = $red_packet_t_id;
                    $field_row['redpacket_title'] = $row['redpacket_t_title'];
                    $field_row['redpacket_desc'] = $row['redpacket_t_desc'];
                    $field_row['redpacket_start_date'] = $row['redpacket_t_start_date'];
                    $field_row['redpacket_end_date'] = $row['redpacket_t_end_date'];
                    $field_row['redpacket_price'] = $row['redpacket_t_price'];
                    $field_row['redpacket_t_orderlimit'] = $row['redpacket_t_orderlimit'];
                    $field_row['redpacket_state'] = RedPacket_BaseModel::UNUSED;
                    $field_row['redpacket_active_date'] = get_date_time();
                    $field_row['redpacket_owner_id'] = $user_id;
                    $field_row['redpacket_owner_name'] = $user_data['user_account'];
                    $add_flag = $redPacketBaseModel->addRedPacket($field_row, true);
                    check_rs($add_flag, $rs_row);
                    $edit_row = array();
                    $edit_row['redpacket_t_giveout'] = 1;
                    $update_flag = $redPacketTempModel->editRedPacketTemplate($red_packet_t_id, $edit_row, true);
                    check_rs($update_flag, $rs_row);

                    if (is_ok($rs_row) && $redPacketBaseModel->sql->commitDb()) {
                        $flag = true;
                    } else {
                        $redPacketBaseModel->sql->rollBackDb();
                        $flag = false;
                    }
                }
            }
        }
        return $flag;
    }

}