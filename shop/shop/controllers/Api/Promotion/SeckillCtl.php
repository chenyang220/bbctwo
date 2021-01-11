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
class Api_Promotion_SeckillCtl extends Api_Controller
{
	public $Seckill_BaseModel  = null;
	public $Seckill_GoodsModel = null;
	public $Seckill_QuotaModel = null;

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

		$this->Seckill_BaseModel  = new Seckill_BaseModel();
		$this->Seckill_GoodsModel = new Seckill_GoodsModel();
		$this->Seckill_QuotaModel = new Seckill_QuotaModel();
	}

	/* 满减活动*/
	//满送活动列表
	public function getSeckillList()
	{
		$page          	= request_int('page', 1);
		$rows          	= request_int('rows', 100);
		$miao_sha_name 	= trim(request_string('seckill_name'));   //活动名称
		$shop_name     	= trim(request_string('shop_name'));       //店铺名称
		$seckill_state	= request_int('seckill_state');		   //活动状态

		$cond_row = array();

		if ($seckill_state)
		{
			$cond_row['seckill_state'] = $seckill_state;
		}
		if ($xian_shi_name)
		{
			$cond_row['seckill_name:LIKE'] = $xian_shi_name . '%';
		}
		if ($shop_name)
		{
			$cond_row['shop_name:LIKE'] = $shop_name . '%';
		}
		$cond_row['seckill_state:!='] = 5;
		$data = $this->Seckill_BaseModel->getSeckillActList($cond_row, array('seckill_id' => 'DESC'), $page, $rows);
		

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
        $data = $this -> Seckill_QuotaModel -> getSeckillQuotaList($cond_row, array('combo_id' => 'DESC'), $page, $rows);
        $this -> data -> addBody(-140, $data);
        
    }


    /* 活动下的商品列表*/
	public function getSeckillGoodsListById()
	{
		$cond_row                = array();
		$page                    = request_int('page', 1);
		$rows                    = request_int('rows', 100);
		$cond_row['seckill_id']  = request_int('id');
		$data                    = $this->Seckill_GoodsModel->getSeckillGoodsList($cond_row, array(), $page, $rows);

		$this->data->addBody(-140, $data);
	}

	public function seckillShen(){
		
		$seckill_id  = request_int('id');
	
		$data = $this->Seckill_BaseModel->getOne($seckill_id);

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
		$seckill_id = request_int('seckill_id');
		$seckill_state = request_int('seckill_state');
		$edit_row = array();
		$edit_row['seckill_state'] = $seckill_state;
		$flag = $this->Seckill_BaseModel->editSeckillActInfo($seckill_id,$edit_row);
		$data = $this->Seckill_BaseModel->getOne($seckill_id);
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
	public function cancelSeckill(){
		$seckill_id = request_int('seckill_id');
		$edit_row = array();
		$edit_row['seckill_state'] = 3;
		$flag = $this->Seckill_BaseModel->editSeckillActInfo($seckill_id,$edit_row);
		$seckill_goods_list =  $this->Seckill_GoodsModel->getByWhere(array('seckill_id'=>$seckill_id));
		foreach ($seckill_goods_list as $key => $value) {
			$edit_goods_row = array();
			$edit_goods_row['seckill_goods_state'] = 3;
			$this->Seckill_GoodsModel->editSeckillGoods($value['seckill_goods_id'],$edit_goods_row);

		}
		$data = $this->Seckill_BaseModel->getOne($seckill_id);
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
    public function getSeckillGoodsList()
    {
        $data = array();
        $seckill_allow = Web_ConfigModel::value("seckill_allow");
        if($seckill_allow == 1)
        {
            $Seckill_GoodsModel = new Seckill_GoodsModel();
            $page          	= request_int('page', 1);
            $rows          	= request_int('rows', 12);
            $miao_sha_name 	= trim(request_string('seckill_name'));   //活动名称
            $seckill_state	= request_int('seckill_state');		   //活动状态

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
            if ($seckill_state)
            {
                $cond_row['seckill_goods_state'] = $seckill_state;
            }
            if ($miao_sha_name)
            {
                $cond_row['seckill_name:LIKE'] = '%' . $miao_sha_name . '%';
            }

            $cond_row['goods_start_date:<='] = date('Y-m-d');
            $cond_row['goods_end_date:>='] = date('Y-m-d');

            $data = $Seckill_GoodsModel->getSeckillGoodsList($cond_row, array('seckill_id' => 'DESC'), $page, $rows);
            if($data['items']) {
                $msg = 'success';
                $status = 200;
            } else {
                $msg = '请在后台促销设置中打开秒杀功能并发布秒杀商品1';
                $status = 250;
            }
        }else {
            $msg = '请在后台促销设置中打开秒杀功能并发布秒杀商品2';
            $status = 250;
        }

        $this->data->addBody(-140, $data,$msg,$status);
    }
}
