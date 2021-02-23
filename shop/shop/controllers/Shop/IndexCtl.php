<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Shop_IndexCtl extends Controller
{
	public $shopBaseModel = null;
	public $goodsCommonModel    = null;
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

		$this->shopBaseModel = new Shop_BaseModel();
		$this->goodsCommonModel    = new Goods_CommonModel();
		
		$this->web = $this->webConfig();
		$this->nav = $this->navIndex();
		$this->cat = $this->catIndex();
	}

    public function indexg()
    {
        $this->initData();

        $cond_row = array();
        $cond_row['shop_status'] = Shop_BaseModel::SHOP_STATUS_OPEN;

        if(request_string('or')=='collect')
        {
            $order_row['shop_collect'] = 'DESC';
        }else{
            $order_row['shop_create_time'] = 'DESC';
        }

        if(request_string('district'))
        {
            $Shop_CompanyModel = new Shop_CompanyModel();
            $shop_row['shop_company_address:LIKE'] = '%'.request_string('district').'%';
            $shops = $Shop_CompanyModel->getByWhere($shop_row);
            $shop_ids = array_column($shops,'shop_id');

            $cond_row['shop_id:in'] = $shop_ids;
        }

        if(request_string('keywords'))
        {
            $cond_row['shop_name:LIKE'] = '%'.request_string('keywords').'%';
        }

        //pc分站
        if(request_int('sub_site_id')){
            //修复城市分站的问题 @nsy 2019-06-18
            $sub_site_id = request_int('sub_site_id');
            cookie('sub_site_id', $sub_site_id, 0);
            $_COOKIE['sub_site_id'] = $sub_site_id ;
        }else{
            if(isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0){
                $sub_site_id = $_COOKIE['sub_site_id'];
            }
        }


        //wap分站
        if(request_string('ua') === 'wap'){
            $sub_site_id = request_int('sub_site_id');
        }

        //获取分站信息
        if(Web_ConfigModel::value('subsite_is_open') && isset($sub_site_id) && $sub_site_id > 0){
            //获取站点信息
            $Sub_SiteModel = new Sub_SiteModel();
            $sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
            if($sub_site_district_ids){
                $cond_row['district_id:IN'] = $sub_site_district_ids;
            }

        }
        //用于判断自营是否显示
        $self_shop_show_key = !$sub_site_id ? 'self_shop_show' : 'self_shop_show_'.$sub_site_id;
        if(Web_ConfigModel::value($self_shop_show_key) == 1){
            if(request_int('plat'))
            {
                $cond_row['shop_self_support'] ='true';
            }
        }else{
            $cond_row['shop_self_support'] ='false';
        }


        $cond_row['shop_type'] = 2;   //卖家店铺

        $Yf_Page           = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows              = $Yf_Page->listRows;
        $offset            = request_int('firstRow', 0);
        $page              = ceil_r($offset / $rows);
        if(request_string('typ') == 'json')
        {
            $page = request_int('page', 1);
        }
        $data = $this->shopBaseModel->getBaseList($cond_row,$order_row,$page,$rows);
        //wap端只展示两条推荐商品
        $rows = $this->typ == 'json'? 3: 4;

        if(!empty($data['items']))
        {
            foreach($data['items'] as $key=>$val)
            {
                //判断PC端店铺是否存在，不存在取默认值
                $data['items'][$key]['shop_logo'] = empty($val['shop_logo']) ?Web_ConfigModel::value("photo_shop_head_logo") : $val['shop_logo'];
                //判断wap端店铺logo是否存在，不存在取默认值
                $data['items'][$key]['wap_shop_logo'] = empty($val['wap_shop_logo']) ? Web_ConfigModel::value("photo_shop_head_logo_wap") : $val['wap_shop_logo'];
                //获取店铺评分信息
                $data['items'][$key]['shop_detail']    = $this->shopBaseModel->getShopDetail($val['shop_id']);

                //获取店铺推荐商品
                $goods_recommended = $this->goodsCommonModel->getGoodsList(array("shop_id" => $val['shop_id'],"common_is_recommend" => 2,"common_state" => 1,'common_verify' =>1),array('common_salenum'=>'DESC'), 1,$rows);
                //如果店铺没有推荐商品则获取商品销量的前4件有效商品
                if($goods_recommended)
                {
                    $goods_recommended = $this->goodsCommonModel->getGoodsList(array("shop_id" => $val['shop_id'],"common_state" => 1,'common_verify' =>1),array('common_sell_time'=>'DESC'), 1,$rows);
                }
                $data['items'][$key]['goods_recommended'] = $goods_recommended;


                $condi_rec_goods['shop_id'] 			= $val['shop_id'];
                $condi_rec_goods['common_state'] 		= Goods_CommonModel::GOODS_STATE_NORMAL;
                $condi_rec_goods['common_verify'] 		= Goods_CommonModel::GOODS_VERIFY_ALLOW;
                $goods_common_list = $this->goodsCommonModel->getbywhere( $condi_rec_goods );
                //店铺商品数量
                $data['items'][$key]['goods_num'] = count($goods_common_list);
            }
        }

        $Yf_Page->totalRows = $data['totalsize'];
        $page_nav           = $Yf_Page->prompt();

        $district = new Base_DistrictModel();
        $district_data = $district->getDistrictTree(0);

        if ('json' == $this->typ)
        {
            $this->data->addBody(-140, $data);

        }
        else
        {
            $now_page = 'shop_page';
            include $this->view->getView();
       }
    }

	/**
	 * 首页
	 *
	 * @access public
	 */
	public function index()
	{
		$this->initData();
		$cond_row = array();
		$cond_row['shop_status'] = Shop_BaseModel::SHOP_STATUS_OPEN;
		
		if(request_string('or')=='collect')
		{
			$order_row['shop_collect'] = 'DESC';
		}else{
			$order_row['shop_create_time'] = 'DESC';
		}

		if(request_string('district'))
		{
			$Shop_CompanyModel = new Shop_CompanyModel();
			$shop_row['shop_company_address:LIKE'] = '%'.request_string('district').'%';
			$shops = $Shop_CompanyModel->getByWhere($shop_row);
			$shop_ids = array_column($shops,'shop_id');
			
			$cond_row['shop_id:in'] = $shop_ids;
		}
		
		if(request_string('keywords'))
		{
			$cond_row['shop_name:LIKE'] = '%'.request_string('keywords').'%';
		}
		
        //pc分站
        if(request_int('sub_site_id')){
            //修复城市分站的问题 @nsy 2019-06-18
            $sub_site_id = request_int('sub_site_id');
            cookie('sub_site_id', $sub_site_id, 0);
            $_COOKIE['sub_site_id'] = $sub_site_id ;
        }else{
            if(isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0){
                $sub_site_id = $_COOKIE['sub_site_id'];
            }
        }

        
        //wap分站
        if(request_string('ua') === 'wap'){
            $sub_site_id = request_int('sub_site_id');
        }
        
        //获取分站信息
        if(Web_ConfigModel::value('subsite_is_open') && isset($sub_site_id) && $sub_site_id > 0){
            //获取站点信息
			$Sub_SiteModel = new Sub_SiteModel();
			$sub_site_district_ids = $Sub_SiteModel->getDistrictChildId($sub_site_id);
			if($sub_site_district_ids){
				$cond_row['district_id:IN'] = $sub_site_district_ids;
			}

        }
		//用于判断自营是否显示
		$self_shop_show_key = !$sub_site_id ? 'self_shop_show' : 'self_shop_show_'.$sub_site_id;
        if(Web_ConfigModel::value($self_shop_show_key) == 1){
            if(request_int('plat'))
            {
                $cond_row['shop_self_support'] ='true';
            }
        }else{
            $cond_row['shop_self_support'] ='false';
        }
        

		$cond_row['shop_type'] = 1;   //卖家店铺
		
		$Yf_Page           = new Yf_Page();
		$Yf_Page->listRows = 10;
		$rows              = $Yf_Page->listRows;
		$offset            = request_int('firstRow', 0);
		$page              = ceil_r($offset / $rows);
		if(request_string('typ') == 'json')
		{
			$page = request_int('page', 1);
		}
		$data = $this->shopBaseModel->getBaseList($cond_row,$order_row,$page,$rows);
		//wap端只展示两条推荐商品
		$rows = $this->typ == 'json'? 3: 4;
		
		if(!empty($data['items']))
		{
			foreach($data['items'] as $key=>$val)
			{
			    //判断PC端店铺是否存在，不存在取默认值
                $data['items'][$key]['shop_logo'] = empty($val['shop_logo']) ?Web_ConfigModel::value("photo_shop_head_logo") : $val['shop_logo'];
                //判断wap端店铺logo是否存在，不存在取默认值
                $data['items'][$key]['wap_shop_logo'] = empty($val['wap_shop_logo']) ? Web_ConfigModel::value("photo_shop_head_logo_wap") : $val['wap_shop_logo'];
				//获取店铺评分信息
				$data['items'][$key]['shop_detail']    = $this->shopBaseModel->getShopDetail($val['shop_id']);
				
				//获取店铺推荐商品
				$goods_recommended = $this->goodsCommonModel->getGoodsList(array("shop_id" => $val['shop_id'],"common_is_recommend" => 2,"common_state" => 1,'common_verify' =>1),array('common_salenum'=>'DESC'), 1,$rows);
				//如果店铺没有推荐商品则获取商品销量的前4件有效商品
				if(!$goods_recommended)
				{
					$goods_recommended = $this->goodsCommonModel->getGoodsList(array("shop_id" => $val['shop_id'],"common_state" => 1,'common_verify' =>1),array('common_sell_time'=>'DESC'), 1,$rows);
				}
				$data['items'][$key]['goods_recommended'] = $goods_recommended;

				
				$condi_rec_goods['shop_id'] 			= $val['shop_id'];
				$condi_rec_goods['common_state'] 		= Goods_CommonModel::GOODS_STATE_NORMAL;
				$condi_rec_goods['common_verify'] 		= Goods_CommonModel::GOODS_VERIFY_ALLOW;
				$goods_common_list = $this->goodsCommonModel->getbywhere( $condi_rec_goods );
				//店铺商品数量
				$data['items'][$key]['goods_num'] = count($goods_common_list);
			}
		}
			
		$Yf_Page->totalRows = $data['totalsize'];
		$page_nav           = $Yf_Page->prompt();
		
		$district = new Base_DistrictModel();
		$district_data = $district->getDistrictTree(0);
		if ('json' == $this->typ)
		{
			$this->data->addBody(-140, $data);

		}
		else
		{
			$now_page = 'shop_page';
			include $this->view->getView();
		}
	}

    /**
     * 编辑商铺首页模板
     *
     * @access public
     */
    public function shopIndexTemplate()
    {
        $shop_wap_index = request_int('shop_wap_index');
        if (empty($shop_wap_index)) {
            $msg = "请至少设置一个店铺首页模板";
            $status = 250;
            $this->data->addBody(-140, array(),$msg,$status);
            return;
        }
        $shop_id = Perm::$shopId;
        $Shop_BaseModel = new Shop_BaseModel();
        $Shop_Base = $Shop_BaseModel->getOne($shop_id);
        $flag = $Shop_BaseModel->editBase($shop_id,array("shop_wap_index"=>$shop_wap_index));
        if ($Shop_Base['shop_wap_index'] == $shop_wap_index) {
           $flag = true;
        }
        if ($flag) {
            $msg = "编辑成功";
            $status = 200;
        } else {
            $msg = "编辑失败";
            $status = 250;
        }
        $this->data->addBody(-140, array(),$msg,$status);
    }

    /**
     * 首页  附近的店铺
     *
     * @access public
     */
    public function near()
    {
        $this->initData();

        $Yf_Page           = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows              = $Yf_Page->listRows;
        $offset            = request_int('firstRow', 0);
        $page              = ceil_r($offset / $rows);

        $coordinate = request_row('coordinate');

        $lat = $coordinate['lat'];
        $lng = $coordinate['lng'];

        $data = $this->shopBaseModel->getNearShop($lat, $lng, 20000, $page, $rows);
        
        if(!empty($data['items']))
        {
            foreach($data['items'] as $key=>$val)
            {
                //获取店铺评分信息
                $data['items'][$key]['shop_detail']    = $this->shopBaseModel->getShopDetail($val['shop_id']);
                
                //获取店铺推荐商品
                $data['items'][$key]['goods_recommended'] = $this->goodsCommonModel->getGoodsList(array("shop_id" => $val['shop_id'],"common_is_recommend" => 2,"common_state" => 1,'common_verify' =>1),array('common_salenum'=>'DESC'), 1,4);
                
                $condi_rec_goods['shop_id'] 			= $val['shop_id'];
                $condi_rec_goods['common_state'] 		= Goods_CommonModel::GOODS_STATE_NORMAL;
                $goods_common_list = $this->goodsCommonModel->getbywhere( $condi_rec_goods );
                //店铺商品数量
                $data['items'][$key]['goods_num'] = count($goods_common_list);
            }
        }
        
        if ('json' == $this->typ)
        {
            $this->data->addBody(-140, $data);
            
        }
        else
        {
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav           = $Yf_Page->prompt();
    
            $district = new Base_DistrictModel();
            $district_data = $district->getDistrictTree(0);
    
            
            include $this->view->getView();
        }
    }

    /**
     * 首页  附近的门店
     *
     * @access public
     */
    public function nearChain()
    {
        $this->initData();
        $Yf_Page           = new Yf_Page();
        $Yf_Page->listRows = 10;
        $rows              = $Yf_Page->listRows;
        $offset            = request_int('firstRow', 0);
        $page              = ceil_r($offset / $rows);
        $coordinate = request_row('coordinate');
        $lat = $coordinate['lat'];
        $lng = $coordinate['lng'];
        $type = request_string('type');
        if ($type == 'wx') {
            $lat = request_string('latitude');
            $lng = request_string('longitude');
        }
        $Goods_BaseModel = new Goods_BaseModel(); 
        $Chain_BaseModel = new Chain_BaseModel();
        $data = $Chain_BaseModel->getNearChain($lat, $lng, 50000, $page, $rows);
        if(!empty($data['items']))
        {
            foreach($data['items'] as $key=>$val)
            {
                $data['items'][$key]['entity_xxaddr'] = $val['chain_address'];
                $data['items'][$key]['distance'] = sprintf("%.1f",$val['distance']/1000);
                $data['items'][$key]['chain_name'] = $val['chain_name'];             
                //获取店铺推荐商品
                $recommend_goods_arr = explode(",", $val['recommend_goods']);
                $data['items'][$key]['goods_recommended'] = $Goods_BaseModel->getByWhere(array("goods_id:IN" => $recommend_goods_arr));
            }
        }
        if ('json' == $this->typ)
        {
            $this->data->addBody(-140, $data);
            
        }
        else
        {
            $Yf_Page->totalRows = $data['totalsize'];
            $page_nav           = $Yf_Page->prompt();
    
            $district = new Base_DistrictModel();
            $district_data = $district->getDistrictTree(0);
    
            
            include $this->view->getView();
        }
    }


    /**
     * @param string $address 地址
     * @param string $city  城市名
     * @return array
     */
    function getLatLng($address='',$city='')
    {
        $result = array();
        $ak = '5At3anZe83x8oOpFap42Gt8eHYpy3wm9';//您的百度地图ak，可以去百度开发者中心去免费申请
        $url ="http://api.map.baidu.com/geocoder/v2/?callback=renderOption&output=json&address=".$address."&city=".$city."&ak=".$ak;
        $data = file_get_contents($url);
        $data = str_replace('renderOption&&renderOption(', '', $data);
        $data = str_replace(')', '', $data);
        $data = json_decode($data,true);
        if (!empty($data) && $data['status'] == 0) {
            $result['lat'] = $data['result']['location']['lat'];
            $result['lng'] = $data['result']['location']['lng'];
            return $result;//返回经纬度结果
        }else{
            return null;
        }
     
    }
}
?>