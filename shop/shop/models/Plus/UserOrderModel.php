<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

class Plus_UserOrderModel extends Plus_UserOrder
{

    public function __construct(){
        parent::__construct();
         //购买状态
          $this->orderState = array(
            '1' => __("试用"),
            '2' => __("购买")
          );
          //付费模式
          $this->payUse = array(
            '1' => __("年度"),
            '2' => __("季度"),
            '3' => __("月")
          );
        //支付状态
          $this->payStatus = array(
            '1' => __("未支付"),
            '2' => __("已支付")
          );
    }
    /**
     * @param null $order_id
     * @param $field_row
     * @param bool $flag
     * @return bool|bool修改PLUS会员订单
     */
    public function editPlusUserOrder($order_id=null, $field_row,$flag = false)
    {
        return $this->edit($order_id, $field_row,$flag);
    }

    /**
     * @param $sql
     * @return mixed
     * 更新plus会员订单信息
     */
    public function updatePlusUserOrder($sql)
    {
        return $this->sql->exec($sql);
    }

    public function getPlusUserListById($cond_row = array(), $order_row = array(), $page = 1, $rows = 100){
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);
        //更新应该替换的字段
        foreach (@$data['items'] as $k => $item) {
            $data['items'][$k]['order_status'] = $this->orderState[$item['order_status']];
            $data['items'][$k]['pay_use'] = $this->payUse[$item['pay_use']];
            $data['items'][$k]['pay_status'] = $this->payStatus[$item['pay_status']];
            $data['items'][$k]['payment'] = format_money($item['payment']);
            $data['items'][$k]['pay_time'] = empty($item['pay_time'])?'':date('Y-m-d',$item['pay_time']);
            $data['items'][$k]['create_time'] = empty($item['create_time'])?'':date('Y-m-d',$item['create_time']);
            $data['items'][$k]['end_date'] = empty($item['end_date'])?'':date('Y-m-d',$item['end_date']);
            $data['items'][$k]['start_date'] = empty($item['start_date'])?'':date('Y-m-d',$item['start_date']);
        }
        return $data;
    }

}