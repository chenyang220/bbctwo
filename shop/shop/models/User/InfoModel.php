<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class User_InfoModel extends User_Info
{
	public static $userSex = array(
		"0" => '女',
		"1" => '男',
		"2" => '保密'
	);

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
	public function getInfoList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		$data = $this->listByWhere($cond_row, $order_row, $page, $rows);
		foreach ($data["items"] as $key => $value)
		{
			$data["items"][$key]["user_sex"] = __(User_InfoModel::$userSex[$value["user_sex"]]);
		}
		return $data;
	}

	/**
	 * 读取一个会员信息
	 *
	 * @param  array $order 查询条件
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getUserInfo($order_row = array())
	{
		return $this->getOneByWhere($order_row);
	}

	public function getUser($user_id)
	{
		return $this->getOne($user_id);
	}

	/**
	 * 读取头部会员信息
	 *
	 * @param  int $user_id 主键值
	 * @return array $user 返回的查询内容
	 * @access public
	 */
	public function getUserMore($user_id)
	{

		$user = array();

		$user['info'] = $this->getOne($user_id);

		$user_grade_id = $user['info']['user_grade'];

		$this->userGradeModel = new User_GradeModel();
		$user['grade']        = $this->userGradeModel->getOne($user_grade_id);
		if (empty($user['grade']))
		{
			$user['grade']['user_grade_name'] = __('普通会员');
		}
		$this->userResourceModel = new User_ResourceModel();
		$user['points']          = $this->userResourceModel->getOne($user_id);

		$this->voucherBaseModel = new Voucher_BaseModel();

		$cond_row['voucher_owner_id'] = $user_id;
		$cond_row['voucher_state'] = 1;
		
		$vo                           = $this->voucherBaseModel->getCount($cond_row);

		$user['voucher'] = $vo;

		return $user;
	}
	
	//获取用户的直属下级用户数量
	public function getSubQuantity($cond_row)
	{
		return $this->getNum($cond_row);
	}

	//获取所有用户id
	public function getAllUserId()
	{
		$sql = "SELECT
						user_id,user_name
					FROM
						" . TABEL_PREFIX . "user_info
					";
		$rows = $this->sql->getAll($sql);
		if($rows)
		{
			$rows = array_column($rows,'user_id');
		}

		return $rows;
	}

	/**
	 * 获取订单数量
     * 1.实物订单
     * 2.虚拟订单
     * 3.门店自提订单
     * @param type $user_id
     */
    public function getUserOrderCount($user_id){
        $order_count = array();
        $order_model = new Order_BaseModel();



        //待付款
        $cond_row1 = array();
        $cond_row1['buyer_user_id']        = $user_id;
        $cond_row1['order_buyer_hidden:<'] = Order_BaseModel::IS_BUYER_REMOVE;

        $cond_row1['order_status:IN'] = array(Order_StateModel::ORDER_WAIT_PAY,Order_StateModel::ORDER_PRESALE_DEPOSIT);
        $cond_row1['chain_id:='] = 0; //门店自提订单
        $order_count['wait'] = $order_model->getCount($cond_row1);

        //待发货
        $cond_row2 = array();
        $cond_row2['buyer_user_id']        = $user_id;
        $cond_row2['order_buyer_hidden:<'] = Order_BaseModel::IS_BUYER_REMOVE;
        $cond_row2['order_status:IN'] = array(Order_StateModel::ORDER_WAIT_PREPARE_GOODS,Order_StateModel::ORDER_PAYED);
        $cond_row2['chain_id:='] = 0; //门店自提订单
        $cond_row2['order_is_virtual']     = Order_BaseModel::ORDER_IS_REAL; //实物订单
        $order_count['payed'] = $order_model->getCount($cond_row2);
        //待收货
        $cond_row3 = array();
        $cond_row3['buyer_user_id']        = $user_id;
        $cond_row3['order_buyer_hidden:<'] = Order_BaseModel::IS_BUYER_REMOVE;
        $cond_row3['order_buyer_hidden:!='] = Order_BaseModel::IS_BUYER_HIDDEN;
        $cond_row3['order_status'] = Order_StateModel::ORDER_WAIT_CONFIRM_GOODS;
        $cond_row3['order_is_virtual']     = Order_BaseModel::ORDER_IS_REAL; //实物订单
		//		$cond_row3['chain_id:=']     = 0; //不是门店自提订单

		$order_count['confirm'] = $order_model->getCount($cond_row3);
        //待评价                
        $cond_row4 = array();
        $cond_row4['buyer_user_id']        = $user_id;
        $cond_row4['order_buyer_hidden:<'] = Order_BaseModel::IS_BUYER_REMOVE;
        $cond_row4['order_status'] = Order_StateModel::ORDER_FINISH;
        $cond_row4['order_is_virtual']     = Order_BaseModel::ORDER_IS_REAL; //实物订单
        $cond_row4['order_buyer_evaluation_status'] = 0; //买家未评价
        $cond_row4['chain_id:=']     = 0; //不是门店自提订单
        $cond_row4['order_buyer_hidden:!='] = Order_BaseModel::IS_BUYER_HIDDEN;
        $order_count['finish'] = $order_model->getCount($cond_row4);
        //退款退货
        $return_model = new Order_ReturnModel();
        $cond_row5 = array();
        $cond_row5['buyer_user_id']        = $user_id;
        $cond_row5['return_state:!='] = Order_ReturnModel::RETURN_PLAT_PASS;
        $order_count['return'] = $return_model->getCount($cond_row5);
        return $order_count;
    }

	//根据user_id获取会员折扣
	public function getUserGrade($user_id)
	{
		$user_info     = $this->getOne($user_id);

		$User_GradeModel = new User_GradeModel();

		$user_grade     = $User_GradeModel->getGradeRate($user_info['user_grade']);

		$user_rate = 100;  //不享受折扣时，折扣率为100%
		if ($user_grade)
		{
			$user_rate = $user_grade['user_grade_rate'];
		}

		return $user_rate;

	}

	//获取用户的上3级
	public function getUserPatents($user_id)
	{
		$data = array();
		$data['user_parent_0'] = 0;
		$data['user_parent_1'] = 0;
		$data['user_parent_2'] = 0;
		$data['user_parent_type_0'] = 0;
		$data['user_parent_type_1']	= 0;
		$data['user_parent_type_2']	= 0;
		$user_info     = $this->getOne($user_id);

		if($user_info['user_parent_id'])
		{
			$data['user_parent_0'] = $user_info['user_parent_id'];
			$user_info1     = $this->getOne($user_info['user_parent_id']);
			$data['user_parent_type_0'] = $user_info1['distributor_type'];


			if($user_info1['user_parent_id'])
			{
				$data['user_parent_1'] = $user_info1['user_parent_id'];
				$user_info2     = $this->getOne($user_info1['user_parent_id']);
				$data['user_parent_type_1'] = $user_info2['distributor_type'];


				if($user_info2['user_parent_id'])
				{
					$data['user_parent_2'] = $user_info2['user_parent_id'];
					$user_info3     = $this->getOne($user_info2['user_parent_id']);
					$data['user_parent_type_2'] = $user_info3['distributor_type'];

				}

			}
		}

		return $data;

	}

	//同步会员
	// public function getByUserInfo($start,$rows){
	// 	    $offset = $rows * ($start - 1);
	// 	    $sql = 'SELECT user_id, user_sex,user_mobile,user_email,user_birthday,user_realname,user_name,user_regtime,user_grade FROM '.$this->_tableName.' LIMIT '.$offset.','.$rows;
 //            $rs = $this->sql->getAll($sql);
 //            return $rs;
	// }

    //报表导出
    public function getUserInfoExcel($cond_row){
        $sql = "SELECT u.user_id,u.user_name,u.user_email,u.user_mobile,u.user_sex,u.user_realname,u.user_birthday,u.user_regtime,u.lastlogintime,s.shop_type,s.shop_status FROM ".$this->_tableName ." u left join yf_shop_base s on u.user_id = s.user_id WHERE 1=1";
        if($cond_row['user_type'] == 1) {
            $sql .= " AND u.user_id = ".$cond_row['search_name'];
        }else{
            $sql .= " AND u.user_name LIKE '%".$cond_row['search_name']."%'";
        }
        if($cond_row['shop_source']){
            $sql .= " AND s.shop_type = ".$cond_row['shop_source'];
        }
        if($cond_row['user_is_shop']){
            $sql .= " AND u.user_is_shop = ".$cond_row['user_is_shop'];
        }
        $sql .= " ORDER BY u.user_id ASC";
        if($cond_row['limit'] && $cond_row['is_limit']){
            $sql .= " LIMIT ".$cond_row['start_limit'].','.$cond_row['limit'];
        }else{
            $start = $cond_row['limits']*1000;
            $sql .= " LIMIT ".$start.',1000';
        }
        $data = $this->sql->getAll($sql);
        if($data){
            foreach($data as $k=>$v){
                if($v['shop_type'] == 1 && $v['shop_status'] == 3){
                    $data[$k]['shop_type'] = '商家店铺';
                }else if($v['shop_type'] == 2 && $v['shop_status'] == 3){
                    $data[$k]['shop_type'] = '供应商店铺';
                }else{
                    $data[$k]['shop_type'] = '无';
                }
                $data[$k]['user_sex'] = User_InfoModel::$userSex[$v["user_sex"]];
            }
        }
        return $data;
    }

    //数据导出统计
    public function getCounts()
    {
        $sqls = "SELECT count(*) as sum FROM ".$this->_tableName;
        $sum = $this->sql->getRow($sqls);
        $limt = ceil($sum['sum']/1000);
        return $limt;
    }

}

User_InfoModel::$userSex = array(
	"0" => __('女'),
	"1" => __('男'),
	"2" => __('保密')
);
?>