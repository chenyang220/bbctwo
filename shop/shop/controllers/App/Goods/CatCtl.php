<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class App_Goods_CatCtl extends Controller
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

	/**
	 * 设置商城API网址及key - 后台独立使用
	 *
	 * @access public
	 */
	public function cat()
	{
        $cache_group = 'default';
        $Cache = Yf_Cache::create($cache_group);
        $cache_key_time = md5(Yf_Registry::get('url').$this->ctl.$this->met.'key'); //固定值,判断是否需要重新缓存数据
        $cache_key_time_value = $Cache->get($cache_key_time);
        $cache_value = md5(Yf_Registry::get('url').$this->ctl.$this->met.'value');  //缓存的key
        if($cache_key_time_value != date('Y-m-d')){
            $Goods_CatModel = new Goods_CatModel();
            $data           = $Goods_CatModel->getCatTree();

            //缓存
            $Cache->save(date('Y-m-d'), $cache_key_time);
            $Cache->save(json_encode($data), $cache_value);
        } else {
            $res = $Cache->get($cache_value,$cache_group);
            $data = json_decode($res,true);
        }
		$this->data->addBody(-140, $data);
	}

	/**
	 * 设置商城API网址及key - 后台独立使用
	 *
	 * @access public
	 */
	public function removeCat()
	{
		$shopClassBindModel = new Shop_ClassBindModel;
		$Goods_CatModel = new Goods_CatModel();

		$cat_id     = trim(request_string('cat_id'),',');
		$cat_id_row = explode(',', $cat_id);

		if ($cat_id_row)
		{
            //先查询分类下是否存在商品，存在则不允许删除
            $Goods_CommonModel = new Goods_CommonModel();
            if(is_array($cat_id_row) && count($cat_id_row)>1){
                $cond_row = array('cat_id:IN'=>$cat_id);
            }else{
                $cond_row = array('cat_id'=>$cat_id);
            }
            
            $goods_list_info = $Goods_CommonModel->getGoodsList($cond_row);
			$shop_class_bind_rows = $shopClassBindModel->getByWhere(['product_class_id:IN'=> $cat_id]);

            if(isset($goods_list_info['total']) && $goods_list_info['total']>0){
                $msg    = __('该分类下有商品存在');
                $status = 250;
            }elseif ($shop_class_bind_rows){
				$msg    = __('该分类下有店铺绑定');
				$status = 250;
			}else{
            
                $Goods_CatModel->sql->startTransactionDb();
                $parent_info = $Goods_CatModel->getMogamiCat($cat_id);
                $parent_info = current($parent_info);
                $flag = $Goods_CatModel->removeCat($cat_id_row);
                if(!empty($parent_info)){
                    $Goods_CatNavModel = new Goods_CatNavModel();
                    $goods_cat = $Goods_CatNavModel->getByWhere(array('goods_cat_id' => $parent_info['cat_id']));
                    if(!empty($goods_cat)){
                        foreach ($goods_cat as $key => $val) {
                            $goods_cat_nav_recommend_display = $val['goods_cat_nav_recommend_display'];
                            if (!empty($goods_cat_nav_recommend_display)) {
                                $goods_cat_nav_recommend_display = $this->array_recursion($goods_cat_nav_recommend_display, $cat_id);
                                $data['goods_cat_nav_recommend_display'] = $goods_cat_nav_recommend_display;
                                $goods_cat_nav_id = $val['goods_cat_nav_id'];
                                if(!empty($goods_cat_nav_id)){
                                    $flag             = $Goods_CatNavModel->editCatNav($goods_cat_nav_id, $data);
                                    $flag = $flag == false ? false : true;
                                }
                            }
                        }
                    }
                }
                if ($flag && $Goods_CatModel->sql->commitDb())
                {

                    $msg    = __('success');
                    $status = 200;
                }
                else
                {
                    $Goods_CatModel->sql->rollBackDb();
                    $m      = $Goods_CatModel->msg->getMessages();
                    $msg    = $m ? $m[0] : __('failure');
                    $status = 250;
                }
            }
		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}
		//清除缓存
		$error_row = array();
		$data_row  = array();

		$config_cache = Yf_Registry::get('config_cache');

		$i = 0;
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
			$i++;
			if ($i == 2) {
				break;
			}
		}
		//删除index.html 
        $Cache = Yf_Cache::create('default');
        $index_key = sprintf('%s|%s|%s', Yf_Registry::get('server_id'), 'site_index', isset($_COOKIE['sub_site_id']) ? $_COOKIE['sub_site_id'] : 0);
        $Cache->remove($index_key);

		$this->data->addBody(-140, array('id' => $cat_id_row), $msg, $status);
	}

    private function array_recursion($array,$cat_id){
        foreach ($array as $key => $value){
            if($value['cat_id'] == $cat_id){
                unset($array[$key]);
            }
            if(!empty($value['sub'])){
                if(is_array($value['sub'])){
                    $array[$key]['sub'] =  $this->array_recursion($value['sub'],$cat_id);
                }
            }
        }
        return $array;
    }
	/**
	 * 添加商品分类
	 *
	 * @access public
	 */
	public function addCat()
	{
		$Goods_CatModel   = new Goods_CatModel();
		$data['cat_name']         = request_string('cat_name'); //  分类名称
		$data['cat_parent_id']    = request_string('cat_parent_id'); // 父类
		$data['cat_pic']          = request_string('cat_pic'); // 分类图片
		$data['type_id']          = request_int('type_id'); // 类型id
		$data['cat_commission']   = request_int('cat_commission'); // 分佣比例
		$data['cat_is_wholesale'] = request_int('cat_is_wholesale'); //
		$data['cat_is_virtual']   = request_int('cat_is_virtual'); // 是否允许虚拟
		$data['cat_templates']    = request_string('cat_templates'); //
		$data['cat_displayorder'] = request_int('cat_displayorder'); // 排序
		$data['cat_level']        = request_int('cat_level'); // 分类级别
		$data['cat_show_type']    = request_string('cat_show_type'); // 1:SPU  2:颜色


		$cat_id = $Goods_CatModel->addGoodsCat($data, true);
		if ($cat_id)
		{

            if (Web_ConfigModel::value('Plugin_Fenxiao')) { //分销
                Yf_Plugin_Manager::getInstance()->trigger('updateCat', [
                    'cat_id' => $cat_id,
                    'user_id' => 0,
                    'shop_id' => 0,
                    'values' => [
                        request_string('c_first'),
                        request_string('c_second'),
                        request_string('c_third'),
                        request_string('c_lowest')
                    ],
                ]);
            }

			$msg    = __('success');
			$status = 200;
		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}

		$data['cat_id'] = $cat_id;

		$this->data->addBody(-140, $data, $msg, $status);
	}

	/*
	 * 编辑商品分类
	 */
	public function editGoodsCat()
	{
		$Goods_CatModel   = new Goods_CatModel();
        $rs_row = array();
		$edit_data        = array();
		$cat_id           = request_int('cat_id');
		$cat_name         = request_string('cat_name');
		$cat_is_virtual   = request_int('cat_is_virtual');
		$cat_show_type    = request_int('cat_show_type');
		$cat_commission   = request_int('cat_commission');
		$cat_displayorder = request_int('cat_displayorder');
		$cat_parent_id    = request_int('cat_parent_id');
		$cat_pic          = request_string('cat_pic');
		$type_id          = request_int('type_id');
		$t_gc_virtual     = request_int('t_gc_virtual');
		$t_commis_rate    = request_int('t_commis_rate');
		$t_show_type      = request_int('t_show_type');
        $return_goods_limit    = request_int('return_goods_limit');
        if(!$cat_parent_id && $return_goods_limit<0){//修改一级分类，判断分类退货期
            return $this->data->addBody(-140, array(), '一级分类所属退货期为必填项', 250);
        }
		if (isset($t_gc_virtual) && $t_gc_virtual == 1)
		{
			$edit_data['cat_is_virtual'] = $cat_is_virtual;
		}
		if (isset($t_commis_rate) && $t_commis_rate == 1)
		{
			$edit_data['cat_commission'] = $cat_commission;
		}
		if (isset($t_show_type) && $t_show_type == 1)
		{
			$edit_data['cat_show_type'] = $cat_show_type;
		}

		$flag = $Goods_CatModel->editCat($cat_id, array(
			'cat_name' => $cat_name,
			'cat_is_virtual' => $cat_is_virtual,
			'cat_show_type' => $cat_show_type,
			'cat_commission' => $cat_commission,
			'cat_displayorder' => $cat_displayorder,
			'cat_parent_id' => $cat_parent_id,
			'cat_pic' => $cat_pic,
			'type_id' => $type_id,
            'return_goods_limit'=>$return_goods_limit,
		), false);

        check_rs($flag,$rs_row);

        $flag = is_ok($rs_row);

		if ($flag && !empty($edit_data))
		{
			//$this->editChild($cat_id, $editData);
			$child_cat_id_row = $Goods_CatModel->getCatChildId($cat_id);
			if (!empty($child_cat_id_row))
			{
				$Goods_CatModel->editCat($child_cat_id_row, $edit_data, false);
			}
		}
		if ($flag)
		{
			$msg    = __('success');
			$status = 200;

            if (Web_ConfigModel::value('Plugin_Fenxiao')) { //分销
                Fenxiao::getInstance()->updateCat([
                    'cat_id' => $cat_id,
                    'user_id' => 0,
                    'shop_id' => 0,
                    'values' => [
                        request_string('c_first'),
                        request_string('c_second'),
                        request_string('c_third'),
                    ],
                ]);
            }
		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}
		if (!empty($child_cat_id_row))
		{
			array_push($child_cat_id_row, $cat_id);
		}
		else
		{
			$child_cat_id_row = $cat_id;
		}
		$data = $Goods_CatModel->getCat($child_cat_id_row);

		//清除缓存
		$error_row = array();
		$data_row  = array();

		$config_cache = Yf_Registry::get('config_cache');
		$i = 0;
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
			$i++;
			if ($i == 2) {
				break;
			}
		}
		//删除index.html
        $Cache = Yf_Cache::create('default');
        $index_key = sprintf('%s|%s|%s', Yf_Registry::get('server_id'), 'site_index', isset($_COOKIE['sub_site_id']) ? $_COOKIE['sub_site_id'] : 0);
        $Cache->remove($index_key);

		$this->data->addBody(-140, $data, $msg, $status);
	}

	/*
	 * 修改子类信息
	 */
	public function editChild($cat_id = null, $edit_data = array())
	{
		$Goods_CatModel = new Goods_CatModel();
		if ($cat_id)
		{
			$data_goods = '';
			$data_goods = $Goods_CatModel->getByWhere(array('cat_parent_id' => $cat_id));
			if (!empty($data_goods))
			{
				foreach ($data_goods as $key => $value)
				{
					$flag = $Goods_CatModel->editCat($value['cat_id'], $edit_data, false);
					$this->editChild($value['cat_id'], $edit_data);
				}
			}
		}
	}
	/*
	 * 新增分类
	 */
	public function add()
	{
		$cat_name     = $_REQUEST['cat_name'];
        if(!$cat_name){
            return $this->data->addBody(-140, [], __('分类名称不能为空'), 250);
        }
        $cat_parent_id    = request_int('cat_parent_id');
        $return_goods_limit    = request_int('return_goods_limit');
        if($return_goods_limit<0 && !$cat_parent_id){
            return $this->data->addBody(-140, [], __('请选择分类商品退货期！'), 250);
        }
		$cat_name_row = preg_split('/\n/', $cat_name);
		foreach ($cat_name_row as $name)
		{
			$cat_name_rows[] = $name;
		}

		$Goods_CatModel   = new Goods_CatModel();
		$edit_data        = array();
        
		$cat_is_virtual   = request_int('cat_is_virtual');
		$cat_show_type    = request_int('cat_show_type');
		$cat_commission   = request_int('cat_commission');
		$cat_displayorder = request_int('cat_displayorder');
		$type_id          = request_int('type_id');
		$t_gc_virtual     = request_int('t_gc_virtual');
		$t_commis_rate    = request_int('t_commis_rate');
		$t_show_type      = request_int('t_show_type');
        $cat_pic          = request_string('cat_pic');
		if (isset($t_gc_virtual) && $t_gc_virtual == 1)
		{
			$edit_data['cat_is_virtual'] = $cat_is_virtual;
		}
		if (isset($t_commis_rate) && $t_commis_rate == 1)
		{
			$edit_data['cat_commission'] = $cat_commission;
		}
		if (isset($t_show_type) && $t_show_type == 1)
		{
			$edit_data['cat_show_type'] = $cat_show_type;
		}
		$edit_cat = array(
			'cat_is_virtual' => $cat_is_virtual,
			'cat_show_type' => $cat_show_type,
			'cat_commission' => $cat_commission,
			'cat_displayorder' => $cat_displayorder,
			'cat_parent_id' => $cat_parent_id,
			'type_id' => $type_id,
            'cat_pic' => $cat_pic,
            'return_goods_limit'=>$return_goods_limit,
		);
		$return   = array();
		if (!empty($cat_name_rows))
		{
			foreach ($cat_name_rows as $name_value)
			{
				$edit_cat['cat_name'] = $name_value;

				$cat_id               = $Goods_CatModel->addCat($edit_cat, true, false);

				if ($cat_id && !empty($edit_data))
				{

					$child_cat_id_row = $Goods_CatModel->getCatChildId($cat_id);
					if (!empty($child_cat_id_row))
					{
						$Goods_CatModel->editCat($child_cat_id_row, $edit_data, false);
					}
				}
                $return[] = $cat_id;
			}
		}

		if (!empty($return))
		{
            $Goods_CatModel->removeAllCatCache('all');
			$msg    = __('success');
			$status = 200;

			foreach($return as $k=>$v){
                if (Web_ConfigModel::value('Plugin_Fenxiao')) { //分销
                    Fenxiao::getInstance()->updateCat([
                        'cat_id' => $v,
                        'user_id' => 0,
                        'shop_id' => 0,
                        'values' => [
                            request_string('c_first'),
                            request_string('c_second'),
                            request_string('c_third'),
                        ],
                    ]);
                }
            }

		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}

		//清除缓存 
		$error_row = array();
		$data_row  = array();

		$config_cache = Yf_Registry::get('config_cache');

		$i = 0;
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
			$i++;
			if ($i == 2) {
				break;
			}
		}
		//删除index.html
        $Cache = Yf_Cache::create('default');
        $index_key = sprintf('%s|%s|%s', Yf_Registry::get('server_id'), 'site_index', isset($_COOKIE['sub_site_id']) ? $_COOKIE['sub_site_id'] : 0);
        $Cache->remove($index_key);

		/*$return_data = $Goods_CatModel->getReturnData($return);*/
		$this->data->addBody(-140, $return, $msg, $status);
	}

	public function listCatNav()
	{
		$cat_id           = request_int('id');
		$Goods_CatModel   = new Goods_CatModel();
		$Goods_BrandModel = new Goods_BrandModel();

		$data_cat_rows = $Goods_CatModel->getCatDisplayRows($cat_id, array(), true);
        //$data_class_rows = $Goods_CatModel->getClassDisplayRows($cat_id, array(), true);
		//推荐品牌
		$Goods_BrandModel = new Goods_BrandModel();
		$data_brand_rows  = $Goods_BrandModel->getRecommendBrandList();


		$data['cat']   = $data_cat_rows;
		//$data['class']   = $data_cat_rows;
		$data['brand'] = $data_brand_rows;

		$this->data->addBody(-140, $data);
	}

	public function editNav()
	{
		$Goods_CatNavModel = new Goods_CatNavModel();
		$Goods_CatModel    = new Goods_CatModel();

		$id = request_int('goods_cat_id');
		$goods_cat = $Goods_CatNavModel->getByWhere(array('goods_cat_id' => $id));

		$data = array();

		$recommend_cat_rows = request_row('recommend_cat');
		$goods_class_recommend = request_row('goods_class_recommend');//改造后的分类
		$goods_cat_nav_recommend_display         = $Goods_CatModel->getCatDisplayRows($id, $recommend_cat_rows);
		$goods_cat_nav_recommend_up_display      = $Goods_CatModel->getCatDisplayRows($id, $goods_class_recommend);
		$data['goods_cat_nav_name']              = request_string('cat_other_name');
		$data['goods_cat_nav_pic']               = request_string('cat_image');
		$data['goods_cat_nav_adv']               = request_string('adv_image') . ',' . request_string('advs_image'); //广告图
		$data['goods_cat_nav_adv_url']           = htmlspecialchars_decode(request_string('adv_image_url') . ',' . request_string('advs_image_url')); //广告图链接地址
		$data['goods_cat_nav_brand']             = implode(request_row('brand_value'), ','); //推荐品牌
		$data['goods_cat_nav_recommend']         = implode(request_row('recommend_cat'), ',');
		$data['goods_cat_nav_recommend_display'] = $goods_cat_nav_recommend_display;
		//添加的字段
		$data['goods_cat_nav_recommend_up']         = implode(request_row('goods_class_recommend'), ',');
		$data['goods_cat_nav_recommend_up_display'] = $goods_cat_nav_recommend_up_display;
		$data['goods_cat_id']                    	= $id;
		if (empty($goods_cat))
		{
			$flag = $Goods_CatNavModel->addCatNav($data, true);
		}
		else
		{
			foreach ($goods_cat as $key => $val)
			{
				$goods_cat_nav_id = $val['goods_cat_nav_id'];
				$flag             = $Goods_CatNavModel->editCatNav($goods_cat_nav_id, $data);
			}
		}
		if ($flag !== false)
		{
            $Goods_CatModel->removeAllCatCache('all');
			$msg    = __('success');
			$status = 200;
		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}

		//清除缓存
		$error_row = array();
		$data_row  = array();

		$config_cache = Yf_Registry::get('config_cache');

		$i = 0;
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
			$i++;
			if ($i == 2) {
				break;
			}
		}
		//删除index.html
        $Cache = Yf_Cache::create('default');
        $index_key = sprintf('%s|%s|%s', Yf_Registry::get('server_id'), 'site_index', isset($_COOKIE['sub_site_id']) ? $_COOKIE['sub_site_id'] : 0);
        $Cache->remove($index_key);
        
		$this->data->addBody(-140, $data, $msg, $status);
	}

	public function getNav()
	{
		$Goods_CatNavModel = new Goods_CatNavModel();
		$id                = request_int('id');
        $data_re = array();
		if ($id)
		{
			$data_row = $Goods_CatNavModel->getByWhere(array('goods_cat_id' => $id));
			if ($data_row)
			{
				$data                               = pos($data_row);
				$data_re                            = array();
				$data_re['goods_cat_nav_adv']       = explode(',', $data['goods_cat_nav_adv']);
				$data_re['goods_cat_nav_adv_url']   = explode(',', $data['goods_cat_nav_adv_url']);
				$data_re['goods_cat_nav_brand']     = explode(',', $data['goods_cat_nav_brand']);
				$data_re['goods_cat_nav_recommend'] = explode(',', $data['goods_cat_nav_recommend']);
				$data_re['goods_cat_nav_recommend_up'] = explode(',', $data['goods_cat_nav_recommend_up']);
				$data_re['goods_cat_nav_pic']       = $data['goods_cat_nav_pic'];
				$data_re['goods_cat_nav_name']      = $data['goods_cat_nav_name'];
			}
		}
		if (!$data_re)
		{
            $data_re = array('goods_cat_id'=>$id);
		}
		$this->data->addBody(-140, $data_re, __('success'), 200);
	}

	public function getGoodsCatName()
	{
		$Goods_CatModel = new Goods_CatModel();
		$data_re        = array();
		$id             = request_int('id');
        if(!$id){
           return $this->data->addBody(-140, $data_re, __('success'), 200);
        }
		$data           = $Goods_CatModel->getOne($id);
         
		if ($data)
		{
			$data_re['id']       = $id;
			$data_re['cat_name'] = $data['cat_name'];
		}

		if ($data_re)
		{
			$msg    = __('success');
			$status = 200;
		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}

		return $this->data->addBody(-140, $data_re, $msg, $status);
	}

	public function getCatListByName()
	{
		$cat_name = request_string('cat_name');
		$cat_name = '%'.$cat_name.'%';

		$Goods_CatModel = new Goods_CatModel();
		$cond_row = array();
		$cond_row['cat_name:LIKE'] = $cat_name;
		$cat_row = $Goods_CatModel->getByWhere($cond_row);
		foreach ($cat_row as $key => $value) {
			if($value['cat_id']==9002)
			{
				unset($cat_row[$key]['cat_id']);
			}
		}

		if($cat_row)
		{
			foreach($cat_row as $key => $val)
			{
				$cond_row = array();
				//将拥有下级的分类删除
				$cond_row['cat_parent_id'] = $val['cat_id'];
				$cat_child = $Goods_CatModel->getByWhere($cond_row);
				if($cat_child)
				{
					unset($cat_row[$key]);
				}
				else
				{
					$cat = array();
					$cat = $Goods_CatModel->getCatParentTree($val['cat_id']);
					$cat_row[$key]['cat_name_str'] = '';

					if($cat)
					{
						foreach($cat as $catkey => $catval)
						{
							$cat_row[$key]['cat_name_str'] .= $catval['cat_name'].'>';
						}
						$cat_row[$key]['cat_name_str'] = substr($cat_row[$key]['cat_name_str'],0,strlen($cat_row[$key]['cat_name_str'])-1);
					}
				}
			}

			$cat_row = array_values($cat_row);
		}

		$this->data->addBody(-140, $cat_row);
	}
    /**
     * 根据id获取分类下的所有子分类
     */
    public function getCatListById()
    {
        $cat_id           = request_int('id');
        $Goods_CatModel   = new Goods_CatModel();
        $data_cat_rows = $Goods_CatModel->getCatDisplayRows($cat_id, array(), true);
        $this->data->addBody(-140, $data_cat_rows);
    }
}

?>