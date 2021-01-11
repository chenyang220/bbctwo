<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 14:38
 */
class Api_Promotion_PresaleCtl extends Api_Controller
{
	public $Presale_BaseModel  = null;
	public $Presale_GoodsModel = null;
	public $Presale_QuotaModel = null;

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

		$this->Presale_BaseModel  = new Presale_BaseModel();
		$this->Presale_GoodsModel = new Presale_GoodsModel();
		$this->Presale_QuotaModel = new Presale_QuotaModel();
	}

	/* 满减活动*/
	//满送活动列表
	public function getPresaleList()
	{
		$page          	= request_int('page', 1);
		$rows          	= request_int('rows', 100);
		$yu_shou_name 	= trim(request_string('presale_name'));   //活动名称
		$shop_name     	= trim(request_string('shop_name'));       //店铺名称
		$presale_state	= request_int('presale_state');		   //活动状态

		$cond_row = array();

		if ($presale_state)
		{
			$cond_row['presale_state'] = $presale_state;
		}
		if ($yu_shou_name)
		{
			$cond_row['presale_name:LIKE'] = $yu_shou_name . '%';
		}
		if ($shop_name)
		{
			$cond_row['shop_name:LIKE'] = $shop_name . '%';
		}

		$data = $this->Presale_BaseModel->getPresaleActList($cond_row, array('presale_id' => 'DESC'), $page, $rows);
		

		$this->data->addBody(-140, $data);
	}

	public function getComboList()
    {
     	$cond_row = array();
        $page = request_int('page', 1);
        $rows = request_int('rows', 100);
        $shop_name = request_string('shop_name');
        if ($shop_name) {
            $cond_row['shop_name:LIKE'] = $shop_name . '%';
        }
        $data = $this -> Presale_QuotaModel -> getPresaleQuotaList($cond_row, array('combo_id' => 'DESC'), $page, $rows);
        $this -> data -> addBody(-140, $data);
        
    }


    /* 活动下的商品列表*/
	public function getPresaleGoodsListById()
	{
		$cond_row                = array();
		$page                    = request_int('page', 1);
		$rows                    = request_int('rows', 100);
		$cond_row['presale_id']  = request_int('id');
		$data                    = $this->Presale_GoodsModel->getPresaleGoodsList($cond_row, array(), $page, $rows);

		$this->data->addBody(-140, $data);
	}

	public function PresaleShen(){
		
		$presale_id  = request_int('id');
	
		$data = $this->Presale_BaseModel->getOne($presale_id);

		if($data){
			$status = 200;
			$msg = 'success';
		}else{
			$status = 250;
			$msg = 'false';
		}

		$this->data->addBody(-140, $data,$msg,$status);
	}

	//审核
	public function review(){
		$presale_id = request_int('presale_id');
		$presale_state = request_int('presale_state');
		$edit_row = array();
		$edit_row['presale_state'] = $presale_state;
		$flag = $this->Presale_BaseModel->editPresaleActInfo($presale_id,$edit_row);
		$data = $this->Presale_BaseModel->getOne($presale_id);
		if($flag){
			$status = 200;
			$msg = '操作成功';
		}else{
			$status = 250;
			$msg = '操作失败';
		}

		$this->data->addBody(-140, $data,$msg,$status);
	}

	//取消
	public function cancelPresale(){
		$presale_id = request_int('presale_id');
		$edit_row = array();
		$edit_row['presale_state'] = 3;
		$flag = $this->Presale_BaseModel->editPresaleActInfo($presale_id,$edit_row);
		$presale_goods_list =  $this->Presale_GoodsModel->getByWhere(array('presale_id'=>$presale_id));
		foreach ($presale_goods_list as $key => $value) {
			$edit_goods_row = array();
			$edit_goods_row['presale_goods_state'] = 3;
			$this->Presale_GoodsModel->editPresaleGoods($value['presale_goods_id'],$edit_goods_row);

		}
		$data = $this->Presale_BaseModel->getOne($presale_id);
		if($flag){
			$status = 200;
			$msg = '操作成功';
		}else{
			$status = 250;
			$msg = '操作失败';
		}

		$this->data->addBody(-140, $data,$msg,$status);
	}


	//秒杀商品列表
    public function getPresaleGoodsList()
    {
        $data = array();
       
        $Presale_GoodsModel = new Presale_GoodsModel();
        $page          	= request_int('page', 1);
        $rows          	= request_int('rows', 12);
        $yu_shou_name 	= trim(request_string('presale_name'));   //活动名称
        $presale_state	= request_int('presale_state');		   //活动状态

        $Shop_BaseModel = new Shop_BaseModel();
        $shop_cond['shop_name:LIKE'] = '%' . trim(request_string('shop_name')) . '%';//店铺名称
        $shop_list = $Shop_BaseModel->getByWhere($shop_cond);
        if($shop_list) {
            $shop_ids = array_column($shop_list,'shop_id');
            if($shop_ids) {
                $cond_row['shop_id:IN'] = $shop_ids;
            }
        }else {
            $cond_row['shop_id:IN'] = array();
        }
        if ($presale_state)
        {
            $cond_row['presale_goods_state'] = $presale_state;
        }
        if ($yu_shou_name)
        {
            $cond_row['presale_name:LIKE'] = '%' . $yu_shou_name . '%';
        }

        $cond_row['goods_start_time:<='] = date('Y-m-d H:i:s');
        $cond_row['goods_end_time:>='] = date('Y-m-d H:i:s');

        $data = $Presale_GoodsModel->getPresaleGoodsList($cond_row, array('presale_id' => 'DESC'), $page, $rows);
        if($data['items']) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = '请在后台促销设置中发布预售商品';
            $status = 250;
        }
        

        $this->data->addBody(-140, $data,$msg,$status);
    }
}
