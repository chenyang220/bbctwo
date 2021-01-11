<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Goods_CatCtl extends Controller
{
	public $goodsCatModel = null;

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
		$this->initData();
		$this->web = $this->webConfig();
		$this->nav = $this->navIndex();
		$this->cat = $this->catIndex();
		//include $this->view->getView();
		$this->goodsCatModel = new Goods_CatModel();
	}
    public function wxcat(){
        $Cache = Yf_Cache::create('base');
        $cache_key = 'yf_goods_cat_wxapp';
        $data = $Cache->get($cache_key);
        if (empty($data)) {
           $data = $this->cat();
           $Cache->save($data, $cache_key);
        }
        $this->data->addBody(-140, $data);
    }
	/**
	 * 设置商城API网址及key - 后台独立使用
	 *
	 * @access public
	 */
	public function cat()
	{
		$Goods_CatModel = new Goods_CatModel();
		$Shop_ClassBindModel = new Shop_ClassBindModel();
		$shop_id_wap = request_int('shop_id_wap');
        $wxapp = request_string('wxapp');
        $cat_is_index = request_int('cat_is_index');
		if ($this->typ =='e') {
			$Shop_BaseModel = new Shop_BaseModel();
			$type = request_int('type');
			$Shop_Base = $Shop_BaseModel->getByWhere(array("shop_self_support"=>'true'));
			$shop_goods_ids = array_column($Shop_Base,'shop_id','shop_id');
		}
		if($shop_id_wap){
			$shop_goods_cat = $Shop_ClassBindModel->getByWhere(array('shop_id'=>$shop_id_wap));
			$shop_goods_ids = array_column($shop_goods_cat,'product_class_id');
		}

		if (isset($_REQUEST['cat_parent_id']))
		{
			$cat_parent_id = request_int('cat_parent_id', 0);

			$data_rows     = $Goods_CatModel->getCatTreeData($cat_parent_id, false, 1);
			$data['items'] = array_values($data_rows);
		}
		else
		{
			$data = $Goods_CatModel->getCatTree();
			if (($data && !in_array($shop_id_wap, $shop_goods_ids)) || $type == 'Supplier') {
				foreach ($data['items'] as $key => $value) {
					$Goods_Parent_Cat = $Goods_CatModel->getCatParent($value['cat_id']);
		            $Goods_Parent = array_column($Goods_Parent_Cat, 'cat_id','cat_id');
		            if (in_array(9000, $Goods_Parent)) {
		                unset($data['items'][$key]);
		            }
				}	
			}
			if ( request_string('filter') )
			{
				$Goods_CatModel->filterCatTreeData( $data['items'] );
				$data['items'] = array_values($data['items']);
			}
		}
		if (0 == $cat_parent_id)
        {
            $Mb_CatImageModel = new Mb_CatImageModel();
            $cat_img_rows = $Mb_CatImageModel->getByWhere(array());
            //$cat_img_rows = $Mb_CatImageModel->getByWhere(array('cat_id'=>$cat_id_row));
            $img_row = array();

            foreach ($cat_img_rows as $id=>$cat_img_row)
            {
                $img_row[$cat_img_row['cat_id']] = $cat_img_row['mb_cat_image'];
            }

            foreach ($data['items'] as $k=>$item)
            {
                if (isset($img_row[$item['cat_id']]))
                {
                    $data['items'][$k]['cat_pic'] = $img_row[$item['cat_id']];

                }
            }

            foreach ($shop_goods_ids as $key => $value) {
            	$cat_row = $Goods_CatModel->getOne($value);
            	if($cat_row['cat_parent_id']){
            		$shop_goods_ids[] = $cat_row['cat_parent_id'];
            		$parent_row = $Goods_CatModel->getOne($cat_row['cat_parent_id']);
            		if($parent_row['cat_parent_id']);
            		$shop_goods_ids[] = $parent_row['cat_parent_id'];
            	}
            }

            if($shop_goods_ids){
            	foreach ($data['items'] as $k=>$item)
	            {
	                if (!in_array($item['cat_id'], $shop_goods_ids))
	                {
	                    unset($data['items'][$k]);
	                }
	            }
            }

            $data['items'] = array_merge($data['items']);
        }
        if (1 == $cat_is_index) {
            $Goods_Cat_index = $Goods_CatModel->getByWhere(array("cat_is_index"=>$cat_is_index));
            $data['items'] =  array_values($Goods_Cat_index);
        }
        if ($wxapp == 'wxapp') {
            return $data;exit;
        }
		$this->data->addBody(-140, $data);
	}


    /**
     * 中酷定制分类排序
     *
     * @access public
     */
    public function cat1()
    {
        $cache_group = 'default';
        $Cache = Yf_Cache::create($cache_group);

        $cache_key_time = md5(Yf_Registry::get('url').$this->ctl.$this->met.'key'); //固定值,判断是否需要重新缓存数据
        $cache_key_time_value = $Cache->get($cache_key_time);

        $cache_value = md5(Yf_Registry::get('url').$this->ctl.$this->met.'value');  //缓存的key
        if($cache_key_time_value != date('Y-m-d')){
            $Goods_CatModel = new Goods_CatModel();
            $data           = $Goods_CatModel->getCatTree();
            foreach ($data['items'] as $key=>$val){
                $data['items'][$key]['cat_pics'] = $val['cat_pic'];
            }   
    
            //缓存
            $Cache->save(date('Y-m-d'), $cache_key_time);
            $Cache->save(json_encode($data), $cache_value);
        } else {
            $res = $Cache->get($cache_value,$cache_group);
            $data = json_decode($res,true);
            foreach ($data['items'] as $key=>$val){
                $data['items'][$key]['cat_pics'] = $val['cat_pic'];
            }
        }

        $Goods_Cat_First = array();
        $Goods_CatModel = new Goods_CatModel();
        $Goods_Cat_First = $Goods_CatModel->getOneCatList();
        $goods_cat_first_id = array_column($Goods_Cat_First, "cat_id");


        //去除二级目录
        $goods_cat_two = $Goods_CatModel->getByWhere(array("cat_parent_id:IN"=>$goods_cat_first_id));
        $goods_cat_two_id = array_flip(array_column($goods_cat_two,"cat_id"));
        $cat_list = array_column($data['items'],NULL,"cat_id");
        foreach ($goods_cat_two_id as $key => $value) {
            unset($cat_list[$key]);
        }


        //重新排序分类
        foreach ($Goods_Cat_First as $_First_key => $Cat_First_Id) {
            $cat_id_key = $Goods_CatModel->getCatTreeData($Cat_First_Id['cat_id']);
            $cat_id_key_arr = array_column($cat_id_key,"cat_id");
            foreach ($cat_id_key_arr as $key => $cat_id) {
                if ($cat_list[$cat_id]) {
                    $Goods_Cat_First[$_First_key]['son'][] =  $cat_list[$cat_id];
                }
                
            }
        }
        $this->data->addBody(-140, $Goods_Cat_First);
    }


	//shop_wap 分类列表
    public function cat2()
    {
        $Goods_CatModel = new Goods_CatModel();

        //一级分类
        $cond_row['cat_parent_id'] = 0;
        $order_row['cat_displayorder'] = 'ASC';
        $cat_list = $Goods_CatModel->getByWhere($cond_row, $order_row);
        $data['items'] = array_values($cat_list);

        //默认显示第一个一级分类
        $f_cat = current($cat_list);
        $cat_id = $f_cat['cat_id'];
        $data['cat_id'] = $cat_id;
        
        //某一级分类下的二级分类
        $cond_s_row['cat_parent_id'] = $cat_id;
        $cat_s_list = $Goods_CatModel->getByWhere($cond_s_row, $order_row);
        $data['s_items'] = array_values($cat_s_list);

        //一级分类下的所有商品
        $cat_ids = $Goods_CatModel->getChildCatId($cat_id,1);
        array_push($cat_ids, $cat_id);
        $Goods_CommonModel = new Goods_CommonModel();
        $goods_row['cat_id:IN'] = $cat_ids;

        $common_info = $Goods_CommonModel->getCommonList($goods_row,array(),1,4);
        $data['goods'] = $common_info['items'];
        $this->data->addBody(-140, $data);
    }

    //shop_wap列表商品
    public function getCatGoodsList()
    {
        $first_cat_id = request_int('first_cat');
        $second_cat_id = request_int('second_cat');
        $order = request_string('order');
        $page = request_int('page',1);
        $level = request_int('level');//一、二级分类

        $Goods_CatModel = new Goods_CatModel();
        $data = array();
        $cat_s_list = array();
        if($first_cat_id && $level == 1){
            $cond_s_row['cat_parent_id'] = $first_cat_id;
            $order_row['cat_displayorder'] = 'ASC';
            $cat_s_list = $Goods_CatModel->getByWhere($cond_s_row, $order_row);
            $cat_s_list = array_values($cat_s_list);
        }
        $data['s_items'] = $cat_s_list;

        if($level == 2){
            //二级分类下的商品
            $cat_ids = $Goods_CatModel->getChildCatId($second_cat_id, $level);
            array_push($cat_ids, $second_cat_id);
        }else{
            //一级分类下的所有商品
            $cat_ids = $Goods_CatModel->getChildCatId($first_cat_id, $level);
            array_push($cat_ids, $first_cat_id);
        }

        $Goods_CommonModel = new Goods_CommonModel();
        $goods_row['cat_id:IN'] = $cat_ids;

        //排序
        if($order){
            if($order == 'zh'){
                $order_goods['common_id'] = 'DESC';
            }
            if ($order == 'sale') {
                $order_goods['common_salenum'] = 'DESC';
            }
            if ($order == 'up') {
                $order_goods['common_price'] = 'ASC';
            }
            if ($order == 'down') {
                $order_goods['common_price'] = 'DESC';
            }
        }else{
            $order_goods['common_id'] = 'DESC';
        }

        $common_info = $Goods_CommonModel->getCommonList($goods_row, $order_goods, $page, 4);
        $data['goods'] = $common_info['items'];

        $this->data->addBody(-140, $data);
    }


	public function tree()
	{

		$Shop_ClassBindModel = new Shop_ClassBindModel();
		$shop_id_wap = request_int('shop_id_wap');
		$wxapp = request_string('wxapp',0);
		if($shop_id_wap){
			$shop_goods_cat = $Shop_ClassBindModel->getByWhere(array('shop_id'=>$shop_id_wap));
			$shop_goods_ids = array_column($shop_goods_cat,'product_class_id');
		}
		$Goods_CatModel = new Goods_CatModel();

		$cat_parent_id = request_int('cat_parent_id', 0);

        if ($wxapp) {//小程序
            $data['items'] = $Goods_CatModel->getChildCats($cat_parent_id);
        }else
        {  
            $data['items'] = $Goods_CatModel->getChildCat($cat_parent_id);
        }
		// foreach ($data['items'] as $k=>$item)
  //       {
  //       	foreach ($item as $key => $value) {
  //       		if (!in_array($value['cat_id'], $shop_goods_ids))
	 //            {
	 //                unset($data['items'][$k]['child'][$key]);
	 //            }
  //       	}
           
  //       }
		$this->data->addBody(-140, $data);
	}


	public function goodsCatList()
	{
		$Goods_CatModel = new Goods_CatModel();
		$data           = $Goods_CatModel->getGoodsCatList();

		//最近浏览
		$user_id             = Perm::$userId;
		$User_FootprintModel = new User_FootprintModel();
		$data_foot           = $User_FootprintModel->getByWhere(array('user_id' => $user_id), array('footprint_time' => 'desc'));
		$common_id_rows      = array_column($data_foot, 'common_id');
		$common_id_rows      = array_unique($common_id_rows);
		$common_id_rows      = array_slice($common_id_rows, 0, 4);
		$Goods_CommonModel   = new Goods_CommonModel();
		$data_recommon       = $Goods_CommonModel->listByWhere(array('common_id:in' => $common_id_rows), array('common_sell_time' => 'desc'), 0, 4);
		$data_recommon_goods = $Goods_CommonModel->getRecommonRow($data_recommon);

		include $this->view->getView();
	}


	public function getCat () {
		$cat_id = request_int('cat_id');
		$Goods_CatModel = new  Goods_CatModel();
		$Goods_Cat = $Goods_CatModel->getOne($cat_id);
		$Shop_ClassBindModel = new Shop_ClassBindModel();
		$ShopClassBind = $Shop_ClassBindModel->getByWhere(array("shop_id"=>Perm::$shopId));
		$product_class_id_arr = array_column($ShopClassBind, 'product_class_id','shop_class_bind_id');

		$flag = true;
		foreach ($product_class_id_arr as $shop_class_bind_id => $product_class_id) {
			$cat_id_arr = explode(",",$product_class_id);
			if (in_array($cat_id, $cat_id_arr)) {
					$flag = false;
			}
		}
		if ($flag)
        {
            $status = 200;
            $msg    = __('success');
        }
        else
        {
            $status = 250;
            $msg    = __('该类目已存在');
        }
		$this->data->addBody(-140, $Goods_Cat,$msg,$status);
	}

	public function getCatMb()
    {
        $cat = Web_ConfigModel::value('setWxCat');
        $data['cat'] = $cat? $cat:1;
        $this->data->addBody(-140, $data, 'success', 200);
    }

}

?>