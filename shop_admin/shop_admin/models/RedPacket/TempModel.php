<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class RedPacket_TempModel extends RedPacket_Temp
{
	const VALID   = 1;//有效
	const INVALID = 2;//失效

	const GETBYPOINTS = 1;//积分兑换
	const GETFBYPWD   = 2;//卡密
	const GETFREE     = 3;//免费领取
    const GETSYSTEMGIVE = 4;//平台发放（plus会员赠送）

	const UNRECOMMEND = 0; //不推荐
	const RECOMMEND   = 1;  //推荐

    const REGISTER = 1;  //新人注册优惠券
    const COMMONRPT =2; //平台发放的普通优惠券
    const PLUSOPEN = 3;//开通plus会员，系统赠送
    const PLUSDAY = 4; //超级会员日（每年双十一）


	//平台优惠券模板状态
	public static $redpacket_state_map = array(
		self::VALID => '有效',
		self::INVALID => '失效'
	);
	//平台优惠券获取方式
	public static $redpacket_access_method_map = array(
		self::GETBYPOINTS => '积分兑换',
		self::GETFREE => '免费领取',
        self::GETSYSTEMGIVE =>'平台发放',
	);

	//平台优惠券推荐状态，推荐的话在积分商城首页显示
	public static $redpacket_recommend_map = array(
		self::UNRECOMMEND => '否',
		self::RECOMMEND => '是'
	);

    //优惠券类型
    public static $redpacket_getrouter_map = array(
        self::REGISTER=>'注册优惠券',
        self::COMMONRPT=>'普通优惠券',
        self::PLUSOPEN=>'PLUS会员红包',// plus 专属
        self::PLUSDAY=>'超级会员日红包',//plus 专属
    );

	//多条件获取优惠券列表，有分页
	public function getRedPacketTempList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		$expire_row = array();
		$rows = $this->listByWhere($cond_row, $order_row, $page, $rows);
        if($rows['items'])
        {
            $userGradeModel = new User_GradeModel();
            $grade_rows = $userGradeModel->getGradeList();//用户等级列表
            foreach ($rows['items'] as $key => $value)
            {
                $rows['items'][$key]['redpacket_t_state_label']         = __(self::$redpacket_state_map[$value['redpacket_t_state']]);
                $rows['items'][$key]['redpacket_t_access_method_label'] = __(self::$redpacket_access_method_map[$value['redpacket_t_access_method']]);
                $rows['items'][$key]['redpacket_t_recommend_label']     = __(self::$redpacket_recommend_map[$value['redpacket_t_recommend']]);
                $rows['items'][$key]['redpacket_t_end_date_day']        = date('Y-m-d', strtotime($value['redpacket_t_end_date']));
                $rows['items'][$key]['redpacket_t_user_grade_label']    = in_array($value['redpacket_t_user_grade_limit'],array_column($grade_rows,'user_grade_id'))?$grade_rows[$value['redpacket_t_user_grade_limit']]['user_grade_name']:NULL;
				$rows['items'][$key]['redpacket_t_img']        			= $value['redpacket_t_img'];

                if (strtotime($value['redpacket_t_end_date']) < time())
                {
                    $rows['items'][$key]['redpacket_t_state']       = self::INVALID;
                    $rows['items'][$key]['redpacket_t_state_label'] = __(self::$redpacket_state_map[self::INVALID]);
                    $expire_row[]                                 = $value['redpacket_t_id'];
                }
            }
        }

		$field_row['redpacket_t_state'] = self::INVALID;
		$this->editRedPacketTemp($expire_row, $field_row);

		return $rows;
	}

    //根据主键获取平台优惠券模板详细信息
    public function getRedPacketTempInfoById($redpacket_t_id)
    {
        $row = $this->getOne($redpacket_t_id);
        if ($row)
        {
            $userGradeModel = new User_GradeModel();
            $grade_rows = $userGradeModel->getGradeList();//用户等级列表

            $row['redpacket_t_access_method_label'] = __(self::$redpacket_access_method_map[$row['redpacket_t_access_method']]);
            $row['redpacket_t_state_label']         = __(self::$redpacket_state_map[$row['redpacket_t_state']]);
            $row['redpacket_t_recommend_label']     = __(self::$redpacket_recommend_map[$row['redpacket_t_recommend']]);
            $row['redpacket_t_user_grade_label']    = in_array($row['redpacket_t_user_grade_limit'],array_column($grade_rows,'user_grade_id'))?$grade_rows[$row['redpacket_t_user_grade_limit']]['user_grade_name']:NULL;

        }
        return $row;
    }

    //多条件获取平台优惠券模板的详细信息
	public function getRedPacketTempInfoByWhere($cond_row)
	{
		$row = $this->getOneByWhere($cond_row);
		if ($row)
		{
            $userGradeModel = new User_GradeModel();
            $grade_rows = $userGradeModel->getGradeList();//用户等级列表

			$row['redpacket_t_access_method_label']         = __(self::$redpacket_access_method_map[$row['redpacket_t_access_method']]);
			$row['redpacket_t_state_label']                  = __(self::$redpacket_state_map[$row['redpacket_t_state']]);
			$row['redpacket_t_recommend_label']              = __(self::$redpacket_recommend_map[$row['redpacket_t_recommend']]);
            $row['redpacket_t_user_grade_label']            = in_array($row['redpacket_t_user_grade_limit'],array_column($grade_rows,'user_grade_id'))?$grade_rows[$row['redpacket_t_user_grade_limit']]['user_grade_name']:NULL;
            // $row['user_grade_demand'] = $grade_rows['demand'];
        }
		return $row;
	}

	/**
	 * 插入
	 * @param array $field_row_row 插入数据信息
	 * @param bool $return_insert_id 是否返回inset id
	 * @param array $field_row_row 信息
	 * @return bool  是否成功
	 * @access public
	 */
	public function addRedPacketTemp($field_row, $return_insert_id)
	{
		$add_flag = $this->add($field_row, $return_insert_id);

		return $add_flag;
	}

	public function removeRedPacketTemp($redpacket_t_id)
	{
		return $this->remove($redpacket_t_id);
	}

	public function editRedPacketTemp($redpacket_t_id, $field_row)
	{
		$update_flag = $this->edit($redpacket_t_id, $field_row);

		return $update_flag;
	}

	public function editRedPacketTemplate($redpacket_t_id, $field_row, $flag=false)
	{
		$update_flag = $this->edit($redpacket_t_id, $field_row, $flag);
		return $update_flag;
	}
	
	public function getRedPacketTempNumByWhere($cond_row)
	{
		return $this->getNum($cond_row);
	}

	public function getForumRedpacket($redpacket_t_id)
	{
		$data = array();
		if (!empty($redpacket_t_id)) {
			$redpacket_list = $this->getByWhere( array('redpacket_t_id:IN' => $redpacket_t_id) ,array('redpacket_t_id'=>'DESC'));
			if ( !empty($redpacket_list) )
			{
				foreach ($redpacket_list as $redpacket_id => $redpacket_data)
				{
					if (strtotime($redpacket_data['redpacket_t_end_date']) > time() && $redpacket_data['redpacket_t_state'] == self::VALID && $redpacket_data['redpacket_t_access_method']!=RedPacket_TempModel::GETSYSTEMGIVE) {
						$data[$redpacket_id]['redpacket_t_id'] = $redpacket_data['redpacket_t_id'];
						$data[$redpacket_id]['redpacket_t_title'] = $redpacket_data['redpacket_t_title'];
						$data[$redpacket_id]['redpacket_t_start_date'] = $redpacket_data['redpacket_t_start_date'];
						$data[$redpacket_id]['redpacket_t_end_date'] = $redpacket_data['redpacket_t_end_date'];
						$data[$redpacket_id]['redpacket_t_orderlimit'] = $redpacket_data['redpacket_t_orderlimit'];
						$data[$redpacket_id]['redpacket_t_giveout'] = $redpacket_data['redpacket_t_giveout'];
						$data[$redpacket_id]['redpacket_t_total'] = $redpacket_data['redpacket_t_total'];
						$data[$redpacket_id]['redpacket_t_used'] = $redpacket_data['redpacket_t_used'];
						$data[$redpacket_id]['redpacket_t_price'] = $redpacket_data['redpacket_t_price'];
						$data[$redpacket_id]['redpacket_t_img'] = $redpacket_data['redpacket_t_img'];

						if($redpacket_data['redpacket_t_total'] > 0) {
							$data[$redpacket_id]['redpacket_t_usedper'] = round($redpacket_data['redpacket_t_giveout']/$redpacket_data['redpacket_t_total']*100)."%";
						} else {
							$data[$redpacket_id]['redpacket_t_usedper'] = "0%";
						}
					}


				}
			}
		}

		return array_values($data);
	}


	//首页版块中补齐红包信息
	public function getOpenForumRedpacket($redpacket_t_id,$num)
	{
		$not_in = '';
		if($redpacket_t_id) { $not_in = "AND redpacket_t_id NOT IN  (" . implode(',', $redpacket_t_id) . ") AND  redpacket_t_access_method !=".RedPacket_TempModel::GETSYSTEMGIVE;}
		//获取红包
		$sql = "
                    SELECT
                        redpacket_t_id,redpacket_t_title,redpacket_t_start_date,redpacket_t_end_date,redpacket_t_orderlimit,redpacket_t_total,redpacket_t_giveout,redpacket_t_used,redpacket_t_price,redpacket_t_img
                    FROM
                        " . TABEL_PREFIX . "redpacket_template
                    WHERE  'redpacket_t_end_date' > '".date('Y-m-d H:i:s',time())."' ".$not_in."  AND redpacket_t_state=1 ORDER BY redpacket_t_id DESC LIMIT ".$num;

		$rows = $this -> sql -> getAll($sql);

		foreach($rows as $key => $val)
		{
			if($val['redpacket_t_total'] > 0) {
				$rows[$key]['redpacket_t_usedper'] = round($val['redpacket_t_giveout']/$val['redpacket_t_total']*100)."%";
			} else {
				$rows[$key]['redpacket_t_usedper'] = "0%";
			}
		}
		return $rows;
	}
}

?>