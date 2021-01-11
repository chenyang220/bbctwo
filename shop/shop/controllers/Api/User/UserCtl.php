<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}
class Api_User_UserCtl extends Yf_AppController
{
    public $userAddressModel = null;
    /**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     * @access public
     */
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

        $this->userAddressModel = new User_AddressModel();

    }

    /**
     * 获取用户默认收货地址
     */
    public function getUserConfigAddress()
    {
        $user_id = request_int('user_id');
        $data = $this->userAddressModel->getByWhere(
            ['user_id' => $user_id],
            ['user_address_default' => 'DESC']
        );

        if ($data) {
            $data = current($data);
            $msg = 'success';
            $status = 200;
        } else {
            $data = [];
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
    }







}