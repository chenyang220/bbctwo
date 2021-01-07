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
class Api_Promotion_SellerWxCtl extends Api_Controller
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

	/**
	* 列表信息
	**/
	public function sellerWxlist(){	
	
		//rows
		$rows = request_int('rows',100);
		//page
		$page = request_int('page',1);
		//即将过期天数
		$sellerWx_day=Web_ConfigModel::value("sellerWx_day");//过期天数
		$shopName = request_string('shop_name');
		$type = request_string('type');
		if (request_string('shop_name')) {
            $shopModel = new Shop_BaseModel();
            $shopRows = $shopModel->getByWhere([
                'shop_name:LIKE'=> "%$shopName%"
            ]);

            if ($shopRows) {
                $shopIds = array_column($shopRows, 'shop_id');
                $cond_row['shop_id:IN'] = $shopIds;
            }
        }
        //待审核
        if($type=='wait'){
        	$cond_row['status'] = 0;
        }
        //审核通过
        if($type=='yes'){
        	$cond_row['status'] = 2;
        }
        //审核失败
        if($type=='no'){
        	$cond_row['status'] = 1;
        }
        //停用
        if($type=='stop'){
        	$cond_row['status'] = 3;
        }
        if($type=='end'){
        	$date = date('Y-m-d 00:00:00',strtotime("+$sellerWx_day day"));
        	$cond_row['end_time:<='] = $date;
        	$cond_row['status'] = 2;
        }
        $SellerWxModel = new Seller_SellerWxModel();
		$where = " 1 ";
		foreach($cond_row as $k=>$items){
			if($k)$where .= " and {$k} ='". $items."' ";
		}
		$sql = "select w.*,IFNULL(t.status ,0) state from yf_seller_wxpublic w left join yf_seller_wxpublic_tplmsgstate t 
		on w.shop_id = t.shop_id  where {$where} LIMIT " . ($page - 1)*$rows . "," . $rows;
		$result = $SellerWxModel->sql->getAll($sql);
		$query = 'SELECT FOUND_ROWS() total';
        $dataRows = $SellerWxModel->sql->getRow($query);
		$total = $dataRows['total'];
		//原生态分页
		$data = array(
			'cmd_id'=>140,
			'status'=>200,
			'msg'=>'success',
			'data'=> array(
				'page' => $page,
				'total' =>ceil_r($total / $rows),
				'totalsize' =>$total,
				'records' =>$total,
				'items' => $result
			)
		);
		print_r(encode_json(conversion_array_type($data)));
		exit;
	}
	//列表信息
	public function sellerWxlist_base(){	
		//即将过期天数
		$sellerWx_day=Web_ConfigModel::value("sellerWx_day");//过期天数
		$shopName = request_string('shop_name');
		$type = request_string('type');
		if (request_string('shop_name')) {
            $shopModel = new Shop_BaseModel();
            $shopRows = $shopModel->getByWhere([
                'shop_name:LIKE'=> "%$shopName%"
            ]);

            if ($shopRows) {
                $shopIds = array_column($shopRows, 'shop_id');
                $cond_row['shop_id:IN'] = $shopIds;
            }
        }
        //待审核
        if($type=='wait'){
        	$cond_row['status'] = 0;
        }
        //审核通过
        if($type=='yes'){
        	$cond_row['status'] = 2;
        }
        //审核失败
        if($type=='no'){
        	$cond_row['status'] = 1;
        }
        //停用
        if($type=='stop'){
        	$cond_row['status'] = 3;
        }
        if($type=='end'){
        	$date = date('Y-m-d 00:00:00',strtotime("+$sellerWx_day day"));
        	$cond_row['end_time:<='] = $date;
        	$cond_row['status'] = 2;
        }
        	
       


        $SellerWxModel = new Seller_SellerWxModel();
        $data = $SellerWxModel->getSellerWxList($cond_row);
        // if($data['items']){
        // 	foreach ($data['items'] as $key => $value) {
        // 		$Seller_SellerWxListModel = new Seller_SellerWxListModel();
        // 		$info = $Seller_SellerWxListModel->getOneByWhere(array('shop_id'=>$value['shop_id']));
        // 		if($info['seller_wxpublic_status']==2){
        // 			$data['items'][$key]['status']='3';
        // 		}        		
        // 	}
        // }
        if($data){       	
        	$msg='success';
        	$status=200;
        }else{
        	$msg = 'false';
            $status = 250;
        }



        $error_row = array();
		$data_row  = array();

		$config_cache = Yf_Registry::get('config_cache');

		foreach ($config_cache as $name => $item)
		{
			if (isset($item['cacheDir']))
			{
				if (clean_cache($item['cacheDir']))
				{
					$data_row[] = $item['cacheDir'];
				}
				else
				{
					$error_row[] = $item['cacheDir'];
				}

				$Cache = Yf_Cache::create($name);

				$data_row[] = json_encode($config_cache['memcache'][$name]);

				if (method_exists($Cache, 'flush') && !$Cache->flush())
				{
					$error_row[] = 'memcache-' . $name;
				}
			}
		}
		//删除index.html
		$this->cacheIndex();

        $this->data->addBody(-140, $data, $msg, $status);
	}


	 /**
	 * 清除缓存
	 *
	 * @access public
	 */
	public function cacheIndex()
	{
		$data_row  = array();
        $Cache = Yf_Cache::create('default');
        $index_key = sprintf('%s|%s|%s', Yf_Registry::get('server_id'), 'site_index', isset($_COOKIE['sub_site_id']) ? $_COOKIE['sub_site_id'] : 0);
        $Cache->remove($index_key);
        /*$content = file_get_contents(Yf_Registry::get('url').'?ctl=Index&met=index&typ=e');
        file_put_contents( ROOT_PATH.'/index.html', $content);*/
		$this->data->addBody(-140, $data_row);
	}

	//详情
	public function getList(){
		$id = request_int('id');
		$SellerWxModel = new Seller_SellerWxModel();
		$data = $SellerWxModel->getOne($id);
		if($data){
        	$msg='success';
        	$status=200;
        }else{
        	$msg = 'false';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
	}


	public function editSellerWx(){
		$id = request_int('id');
		$SellerWxModel = new Seller_SellerWxModel();
		$data = $SellerWxModel->getOne($id);
		if($data){
        	$msg='success';
        	$status=200;
        }else{
        	$msg = 'false';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
	}

	//审核
	public function review(){
		$SellerWxModel = new Seller_SellerWxModel();
		$cond_row=array();
		$id = request_int('id');
		$status = request_string('seller_wxpublic_status');
		$review_info = request_string('review_info');
		$cond_row['status'] = $status;
		$cond_row['review_info'] = $review_info;
		$seller_wx = $SellerWxModel->getOne($id);

		
		if($status==2){
			$cond_row['start_time']= date('Y-m-d h:i:s');
			$cond_row['end_time'] =  date ( 'Y-m-d h:i:s' , strtotime ( '+'.$seller_wx['years'].' year' ));
		}
		
		$flag=$SellerWxModel->editSellerWx($id,$cond_row);
		$data = $SellerWxModel->getOne($id);
		if($flag){
        	$msg='success';
        	$status=200;
        }else{
        	$msg = 'false';
            $status = 250;
        }
        $this->data->addBody(-140, $data, $msg, $status);
	}

	//变更状态
	public function editStatus(){
		$id = request_int('id');
		$wx_public_name = request_string('wx_public_name');
		$status = request_int('status');
		$SellerWxModel = new Seller_SellerWxModel();
		$data = $SellerWxModel->getOne($id);
		$flag ='false';
		if($data){
			$Seller_SellerWxListModel = new Seller_SellerWxListModel();
			$info = $Seller_SellerWxListModel->getOneByWhere(array('shop_id'=>$data['shop_id']));
			if(empty($info)){
				return $this -> data -> addBody(-140, $data, '该商家还未绑定公众号', 250);
			}
			if($status==2){
				//启用
				$flag = $Seller_SellerWxListModel->editSellerWxList($info['seller_wxpublic_id'],array('seller_wxpublic_status'=>0));
				$SellerWxModel->editSellerWx($id,array('status'=>2,'wx_public_name'=>$wx_public_name));
			}else{
				//关闭
				$flag = $Seller_SellerWxListModel->editSellerWxList($info['seller_wxpublic_id'],array('seller_wxpublic_status'=>2));
				$SellerWxModel->editSellerWx($id,array('status'=>3,'wx_public_name'=>$wx_public_name));
			}
			// if($info['seller_wxpublic_status']==0||$info['seller_wxpublic_status']==1){
			// 	$flag = $Seller_SellerWxListModel->editSellerWxList($info['seller_wxpublic_id'],array('seller_wxpublic_status'=>2));
			// 	$SellerWxModel->editSellerWx($id,array('status'=>3));
			// }elseif($info['seller_wxpublic_status']==2){
			// 	$flag = $Seller_SellerWxListModel->editSellerWxList($info['seller_wxpublic_id'],array('seller_wxpublic_status'=>0));

			// 	$SellerWxModel->editSellerWx($id,array('status'=>3));
			// }
		}else{
			return $this -> data -> addBody(-140, $data, '查不到该数据', 250);
		}
		if($flag){
        	$msg='success';
        	$status=200;
        }else{
        	$msg = '变更状态失败';
            $status = 250;
        }

        $data = $SellerWxModel->getOne($id);
        $this->data->addBody(-140, $data, $msg, $status);
	}
}

?>