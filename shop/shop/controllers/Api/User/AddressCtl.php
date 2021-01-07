<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

class Api_User_AddressCtl extends Api_Controller
{
    /**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     * @access public
     */
    public $userInfoModel='';
    public $userAddressModel='';
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->userAddressModel = new User_AddressModel();
        $this->userInfoModel = new User_InfoModel();
    }
    /**
     *获取会员地址信息
     *
     * @access public
     */
    public function address()
    {
        $user_id = request_int('user_id');
        $act = request_string('act');

        //获取一级地址
        $district_parent_id = request_int('pid', 0);
        $Base_DistrictModel = new Base_DistrictModel();
        $district = $Base_DistrictModel->getDistrictTree($district_parent_id);
        if ($act == 'edit') {
            $userId = $user_id;
            $user_address_id = request_int('id');
            $data = $this->userAddressModel->getAddressInfo($user_address_id);
            return $this->data->addBody(-140, $data);
        } elseif ($act == 'add') {
            $userId = $user_id;
            $data = array();
        } elseif ($act == 'edit_delivery') {
            $userId = $user_id;
            $data = array();
        } else {
            $order_row['user_id'] = $user_id;
            $data = $this->userAddressModel->getAddressList($order_row);
        }
        $district_id_row = array_merge(
                array_column($data, 'user_address_province_id'),
                array_column($data, 'user_address_city_id'),
                array_column($data, 'user_address_area_id')
            );
            $district_id_row = array_filter($district_id_row);
            if ($district_id_row) {
                $district_rows = $Base_DistrictModel = $Base_DistrictModel->getDistrict($district_id_row);

                foreach ($data as $k => $address_row) {
                    $address_row['address_info'] = sprintf('%s %s %s', @$district_rows[$address_row['user_address_province_id']]['district_name'], @$district_rows[$address_row['user_address_city_id']]['district_name'], @$district_rows[$address_row['user_address_area_id']]['district_name']);
                    $data[$k] = $address_row;
                }
            }
            $data_rows['address_list'] = array_values($data);
            $this->data->addBody(-140, $data_rows);


    }
    /**
     *删除会员地址信息
     *
     * @access public
     */
    public function delAddress()
    {
        $user_id = request_int('user_id');
        $user_address_id = request_int('id');
        //验证用户
        $cond_row = array(
            'user_id' => $user_id,
            'user_address_id' => $user_address_id
        );
        $re = $this->userAddressModel->getByWhere($cond_row);

        if ($re) {
            $flag = $this->userAddressModel->removeAddress($user_address_id);
        } else {
            $flag = false;
        }

        if ($flag !== false) {
            $status = 200;
            $msg = __('success');
        } else {
            $status = 250;
            $msg = __('failure');
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);

    }
    /**
     *增加会员地址信息
     *
     * @access public
     */
    public function addAddressInfo()
    {
        $user_id =  $user_id = request_int('user_id');
        $user_address_contact = request_string('user_address_contact');
        $user_address_area = request_string('user_address_area');
        $user_address_address = request_string('user_address_address');
        $user_address_phone = preg_replace('# #', '', request_string('user_address_phone'));
        $area_code = request_string('area_code')?:86;
        $user_address_default = request_string('user_address_default');
        if (!$user_address_phone || (!Yf_Utils_String::isMobile($user_address_phone) && $area_code==86) ) {
            return $this->data->addBody(-140, array(), __('手机号码格式有误'), 250);
        }

        $edit_address_row['user_id'] = $user_id;
        $edit_address_row['user_address_contact'] = $user_address_contact;
        $edit_address_row['user_address_province_id'] = request_int('user_address_province_id');
        $edit_address_row['user_address_city_id'] = request_int('user_address_city_id');
        $edit_address_row['user_address_area_id'] = request_int('user_address_area_id');
        $edit_address_row['user_address_area'] = $user_address_area;
        $edit_address_row['user_address_address'] = $user_address_address;
        $edit_address_row['user_address_phone'] = $user_address_phone;
        $edit_address_row['area_code'] = $area_code;
        $edit_address_row['user_address_default'] = $user_address_default;
        $edit_address_row['user_address_time'] = get_date_time();

        $cond_row['user_id'] = $user_id;
        $re = $this->userAddressModel->getCount($cond_row);
        if ($re > 19) {

            $status = 250;
            $msg = __('failure');

        } else {

            //开启事物
            $rs_row = array();
            $this->userAddressModel->sql->startTransactionDb();

            //判断是否设默认，默认改变前面的状态
            if ($user_address_default == '1') {

                $order_row['user_id'] = $user_id;
                $order_row['user_address_default'] = '1';
                $de = $this->userAddressModel->getAddressList($order_row);

                if (!empty($de)) {
                    $updata_flag = $this->userAddressModel->editAddressInfo($de);
                }
            }
            check_rs($updata_flag, $rs_row);
            $flag = $this->userAddressModel->addAddress($edit_address_row, true);
            $addess_id = $flag;
            check_rs($flag, $rs_row);
            $flag = is_ok($rs_row);
            if ($flag !== false && $this->userAddressModel->sql->commitDb()) {
                $edit_address_row['user_address_id'] = $addess_id;
                $status = 200;
                $msg = __('success');
            } else {
                $this->userAddressModel->sql->rollBackDb();

                $status = 250;
                $msg = __('failure');
            }
        }

        $data = $edit_address_row;
        $this->data->addBody(-140, $data, $msg, $status);

    }
    /**
     *编辑会员地址信息
     *
     * @access public
     */
    public function editAddressInfo()
    {
        $user_id = request_int('user_id');
        $user_address_id = request_int('user_address_id');
        $user_address_contact = request_string('user_address_contact');
        $user_address_area = request_string('user_address_area');
        $user_address_address = request_string('user_address_address');
        $user_address_phone = preg_replace('# #', '', request_string('user_address_phone'));
        $area_code = request_string('area_code')?:86;
        $user_address_default = request_string('user_address_default');

        $edit_address_row['user_id'] = $user_id;
        $edit_address_row['user_address_contact'] = $user_address_contact;
        $edit_address_row['user_address_province_id'] = request_int('user_address_province_id');
        $edit_address_row['user_address_city_id'] = request_int('user_address_city_id');
        $edit_address_row['user_address_area_id'] = request_int('user_address_area_id');
        $edit_address_row['user_address_area'] = $user_address_area;
        $edit_address_row['user_address_address'] = $user_address_address;
        $edit_address_row['user_address_phone'] = $user_address_phone;
        $edit_address_row['area_code'] = $area_code;
        $edit_address_row['user_address_default'] = $user_address_default;
        $edit_address_row['user_address_time'] = get_date_time();
        if (!$user_address_phone || (!Yf_Utils_String::isMobile($user_address_phone) && $area_code==86) ) {
            return $this->data->addBody(-140, array(), __('手机号码格式有误'), 250);
        }

        //验证用户
        $cond_row = array(
            'user_id' => $user_id,
            'user_address_id' => $user_address_id,
        );

        $re = $this->userAddressModel->getByWhere($cond_row);

        if (!$re) {
            $msg = __('failure');
            $status = 250;
        } else {
            //开启事物
            $rs_row = array();
            $this->userAddressModel->sql->startTransactionDb();

            if ($user_address_default == '1') {

                $order_row['user_id'] = $user_id;
                $order_row['user_address_default'] = '1';
                $de = $this->userAddressModel->getAddressList($order_row);

                if (!empty($de)) {
                    $updata_flag = $this->userAddressModel->editAddressInfo($de);
                    check_rs($updata_flag, $rs_row);
                }
            }

            $flag = $this->userAddressModel->editAddress($user_address_id, $edit_address_row);

            check_rs($flag, $rs_row);

            $flag = is_ok($rs_row);
            if ($flag !== false && $this->userAddressModel->sql->commitDb()) {
                $status = 200;
                $msg = __('success');
            } else {
                $this->userAddressModel->sql->rollBackDb();
                $msg = __('failure');
                $status = 250;
            }

            $edit_address_row['user_address_id'] = $user_address_id;
            $data = $edit_address_row;
            $this->data->addBody(-140, $data, $msg, $status);
        }

    }

    public function getOrderNum(){
        $user_id = request_int('user_id');
        //获取用户的订单信息
        $data = $this->userInfoModel->getUserOrderCount($user_id);
        $status = 200;
        $msg = __('success');
        $this->data->addBody(-140, $data, $msg, $status);

    }



}
