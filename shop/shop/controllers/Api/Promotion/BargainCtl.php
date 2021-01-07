<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Api接口, 让App等调用
 *
 *
 * @category   Game
 * @package    User
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2015远丰仁商
 * @version    1.0
 * @todo
 */
class Api_Promotion_BargainCtl extends Api_Controller
{
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

	}

	//砍价活动列表
   public function bargain_list(){
        $page = request_int('page', 1);
        $rows = request_int('rows', 100);
        $cond_row = array();
        if(request_string('shop_name')){
            $cond_row['shop_name:LIKE'] = '%' . request_string('shop_name') . '%';
        }
        if(request_string('goods_name')){
            $cond_row['goods_name'] = request_string('goods_name');
        }
       if (request_int('bargain_status')) {
           $cond_row['bargain_status'] = request_int('bargain_status');
       }
        $Bargain_BaseModel = new Bargain_BaseModel();
        $data = $Bargain_BaseModel->getBargainList($cond_row, array('create_time'=>'DESC'), $page, $rows);
        $data['items'] = array_values($data['items']);
        $this->data->addBody(-140, $data);
   }

   //砍价套餐购买列表
   public function getComboList()
   {
       $page = request_int('page', 1);
       $rows = request_int('rows', 100);
       $cond_row = array();
       if (request_string('shop_name')) {
           $cond_row['shop_name:LIKE'] = '%' . request_string('shop_name') . '%';
       }
       $Bargain_QuotaModel = new Bargain_QuotaModel();
       $data = $Bargain_QuotaModel->getBargainQuotaList($cond_row, array('combo_end_time' => 'DESC'), $page, $rows);
       $this->data->addBody(-140, $data);
   }

   //根据砍价活动id获取对应的砍价信息以及商品信息
    public function getBargainInfo()
    {
        $bargain_id = request_int('bargain_id');
        $Bargain_BaseModel = new Bargain_BaseModel();
        $data = $Bargain_BaseModel->getBargainInfo($bargain_id);
        $this->data->addBody(-140, $data);
    }

    //平台终止活动
    public function editBargainStatus()
    {
        $bargain_id = request_int('bargain_id');
        $rs_row = array();
        $Bargain_BaseModel = new Bargain_BaseModel();
        $cond_row['bargain_status'] = Bargain_BaseModel::PLATOFF;
        $res = $Bargain_BaseModel->editBargain($bargain_id, $cond_row);
        check_rs($res, $rs_row);

        //对应砍价活动砍价结束
        $Bargain_BuyUserModel = new Bargain_BuyUserModel();
        $info = $Bargain_BuyUserModel->getByWhere(array('bargain_id'=> $bargain_id));
        $buy_ids = array_column($info,'buy_id');
        $edit_row['bargain_state'] = Bargain_BuyUserModel::PLATOFF;
        $flag = $Bargain_BuyUserModel->editBuyUser($buy_ids, $edit_row);
        check_rs($flag, $rs_row);

        $result = is_ok($rs_row);
        if($result){
            $msg = 'success';
            $status = 200;
        }else{
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, array(),$msg,$status);
    }

}

?>