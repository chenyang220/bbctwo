<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

use Yf\Upgrader\Base;
use Yf\Upgrader\Core;

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class ConfigCtl extends AdminController
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
	public function api()
	{
		include $view = $this->view->getView();
		
	}
	
	/**
	 *
	 *
	 * @access public
	 */
	public function cacheManage()
	{
		include $view = $this->view->getView();
		
	}
	
	/**
	 *
	 *
	 * @access public
	 */
	public function themeStyle()
	{
		$data = $this->getUrl('Config', 'themeStyle');
		include $view = $this->view->getView();
		
	}

	public function update()
	{
		 
		
		include $view = $this->view->getView();
	}
	
	public function updateShop()
	{
		 
		
		include $view = $this->view->getView();

	}
	
	public function upgradeShopContainer()
	{

		include $view = $this->view->getView();
	}

	public function upgradeShop()
	{
		//从API获取。
		$data = $this->getUrl('Config', 'update');
		
		$change_file_row = $data['change_file_row'];
		$version_row     = $data['version_row'];
		$client_version  = $data['client_version'];
		$partial         = $data['partial'];
		
		
		if ($partial && request_int('upgrade') || request_int('force-upgrade'))
		{
			//url 带加密数据跳转
			
			$data = $this->getUrl('Config', 'update', 'e', true);
			
		}
		
	}
	
	/**
	 * 设置用户中心API网址及key - 后台独立使用
	 *
	 * @access public
	 */
	public function editUcenterApi()
	{
		$ucenter_api_row = request_row('ucenter_api');
		
		$ucenter_api_key       = $ucenter_api_row['ucenter_api_key'];
		$ucenter_api_url       = $ucenter_api_row['ucenter_api_url'];
		$ucenter_admin_api_url = $ucenter_api_row['ucenter_admin_api_url'];
		$ucenter_app_id        = 104;
		
		//先检测API是否正确
		$key                = $ucenter_api_key;
		$url                = $ucenter_api_url;
		$formvars           = array();
		$formvars['app_id'] = $ucenter_app_id;
		$init_rs            = get_url_with_encrypt($key, sprintf('%s?ctl=Api&met=checkApi&typ=json', $url), $formvars);
		
		$data = array();
		
		if (200 == $init_rs['status'])
		{
			//读取服务列表
			$data   = $init_rs['data'];
			$status = 200;
			$msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('sucess');
			
			
			//
			
			$data                          = array();
			$data['ucenter_api_key']       = $ucenter_api_key;
			$data['ucenter_api_url']       = $ucenter_api_url;
			$data['ucenter_app_id']        = $ucenter_app_id;
			$data['ucenter_admin_api_url'] = $ucenter_admin_api_url;
			
			if (is_file(INI_PATH . '/ucenter_api_' . Yf_Registry::get('server_id') . '.ini.php'))
			{
				$file = INI_PATH . '/ucenter_api_' . Yf_Registry::get('server_id') . '.ini.php';
			}
			else
			{
				$file = INI_PATH . '/ucenter_api.ini.php';
			}
			
			if (!Yf_Utils_File::generatePhpFile($file, $data))
			{
				$status = 250;
				$msg    = __('生成配置文件错误!');
			}
			
			
			$data = $this->getUrl('Config', 'editUcenterApi');
		}
		else
		{
			$status = 250;
			$msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('请求错误!');
		}
		
		
		$this->data->addBody(-140, $data = array(), $msg, $status);
	}
	
	
	/**
	 * 设置用户中心API网址及key - 后台独立使用
	 *
	 * @access public
	 */
	public function editPaycenterApi()
	{
		$paycenter_api_row = request_row('paycenter_api');
		
		$paycenter_api_key       = $paycenter_api_row['paycenter_api_key'];
		$paycenter_api_url       = $paycenter_api_row['paycenter_api_url'];
		$paycenter_admin_api_url = $paycenter_api_row['paycenter_admin_api_url'];
		$paycenter_app_id        = 105;
		
		
		$paycenter_api_name = $paycenter_api_row['paycenter_api_name'];
		
		/*
		//先检测API是否正确
		$key                = $paycenter_api_key;
		$url                = $paycenter_api_url;
		$formvars           = array();
		$formvars['app_id'] = $paycenter_app_id;
		$init_rs            = get_url_with_encrypt($key, sprintf('%s?ctl=Api&met=checkApi&typ=json', $url), $formvars);
		*/
		$data = array();
		
		if (true || 200 == @$init_rs['status'])
		{
			/*
			//读取服务列表
			$data   = $init_rs['data'];
			$status = 200;
			$msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('sucess');
			*/
			
			//
			$data                            = array();
			$data['paycenter_api_key']       = $paycenter_api_key;
			$data['paycenter_api_url']       = $paycenter_api_url;
			$data['paycenter_admin_api_url'] = $paycenter_admin_api_url;
			$data['paycenter_app_id']        = $paycenter_app_id;
			$data['paycenter_api_name']      = $paycenter_api_name;
			
			if (is_file(INI_PATH . '/paycenter_api_' . Yf_Registry::get('server_id') . '.ini.php'))
			{
				$file = INI_PATH . '/paycenter_api_' . Yf_Registry::get('server_id') . '.ini.php';
			}
			else
			{
				$file = INI_PATH . '/paycenter_api.ini.php';
			}
			
			if (!Yf_Utils_File::generatePhpFile($file, $data))
			{
				$status = 250;
				$msg    = __('生成配置文件错误!');
			}
			else
			{
				$data = $this->getUrl('Config', 'editPaycenterApi');
				
				$status = 200;
				$msg    = __('设置成功!');
			}
			
			
		}
		else
		{
			$status = 250;
			$msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('请求错误!');
		}
		
		
		$this->data->addBody(-140, $data, $msg, $status);
	}
	
	/**
	 * 设置商城API网址及key - 后台独立使用
	 *
	 * @access public
	 */
	public function editShopApi()
	{
		$shop_api_row = request_row('shop_api');
		
		$shop_api_key = $shop_api_row['shop_api_key'];
		$shop_api_url = $shop_api_row['shop_api_url'];
		$shop_wap_url = $shop_api_row['shop_wap_url'];
		$shop_app_id  = Yf_Registry::get('shop_app_id');
		
		//先检测API是否正确
		$key = Yf_Registry::get('shop_api_key');
		$url = $shop_api_url;
		
		$formvars                     = array();
		$formvars['app_id']           = $shop_app_id;
		$formvars['shop_app_id_new']  = $shop_app_id;
		$formvars['shop_api_key_new'] = $shop_api_key;
		$formvars['shop_api_url_new'] = $shop_api_url;
		$formvars['shop_wap_url']     = $shop_wap_url;
		
		//自己调用,直接生成
		//$init_rs         = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Config&met=checkApi&typ=json', $url), $formvars);
		$init_rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Config&met=editApi&typ=json', $url), $formvars);
		
		$data = array();
		
		if (200 == $init_rs['status'])
		{
			//读取服务列表
			$data   = $init_rs['data'];
			$status = 200;
			$msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('sucess');
			
			
			//
			
			$data                 = array();
			$data['shop_api_key'] = $shop_api_key;
			$data['shop_api_url'] = $shop_api_url;
			$data['shop_app_id']  = $shop_app_id;
			$data['shop_wap_url'] = $shop_wap_url;
			
			if (is_file(INI_PATH . '/shop_api_' . Yf_Registry::get('server_id') . '.ini.php'))
			{
				$file = INI_PATH . '/shop_api_' . Yf_Registry::get('server_id') . '.ini.php';
			}
			else
			{
				$file = INI_PATH . '/shop_api.ini.php';
			}
			
			if (!Yf_Utils_File::generatePhpFile($file, $data))
			{
				$status = 250;
				$msg    = __('生成配置文件错误!');
			}
		}
		else
		{
			$status = 250;
			$msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('请求错误!');
		}
		
		
		$this->data->addBody(-140, $data = array(), $msg, $status);
	}

    /**
     * 设置Im_API网址及key - 后台独立使用
     *
     * @access public
     */
    public function editImApi()
    {
    	//IM服务未开通时，修改是禁止的
    	$data = file_get_contents(Yf_Registry::get('shop_api_url')."/index.php?ctl=Api_Wap&met=version_im&typ=json");
    	$data = json_decode($data,true);
     
    	$im_s = $data['data']['im'];
    	if($im_s!=1){
    		$status = 200;
            $msg    = __('请联系客服开通IM服务！');
            exit(json_encode(array(
            		'status'=>250,
            		'msg'=>$msg
            )));
            
    	}
        $im_api_row = request_row('im_api');

        $im_api_key       = $im_api_row['im_api_key'];
        $im_api_url       = $im_api_row['im_api_url'];
        $im_url           = $im_api_row['im_url'];
        $im_admin_api_url = $im_api_row['im_admin_api_url'];
        $im_statu         = $im_api_row['im_statu'];
		$sns_api_url      = $im_api_row['sns_api_url'];
        $im_app_id        = 103;

        //先检测API是否正确

        $key = Yf_Registry::get('shop_api_key');
        $url = Yf_Registry::get('shop_api_url');

        $formvars                     = array();
        $formvars['app_id']           = Yf_Registry::get('shop_app_id');
        $formvars['im_api_key']       = $im_api_key;
        $formvars['im_url']           = $im_url;
        $formvars['im_admin_api_url'] = $im_admin_api_url;
        $formvars['im_api_url']       = $im_api_url;
        $formvars['im_statu']         = $im_statu;
        $formvars['im_app_id']        = $im_app_id;
        $formvars['sns_api_url']        = $sns_api_url;

        //自己调用,直接生成
        //$init_rs         = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Config&met=checkApi&typ=json', $url), $formvars);
        $init_rs = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Config&met=editImApi&typ=json', $url), $formvars);
        $data = array();

        if (true || 200 == @$init_rs['status'])
        {

            //读取服务列表
            $data   = $init_rs['data'];
            $status = 200;
            $msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('sucess');

            //
            $data                            = array();
            $data['im_api_key']       = $im_api_key;
            $data['im_url']           = $im_url;
            $data['im_api_url']       = $im_api_url;
            $data['im_admin_api_url'] = $im_admin_api_url;
            $data['im_app_id']        = $im_app_id;
            $data['im_statu']         = $im_statu;
			$data['sns_api_url']        = $sns_api_url;

            if (is_file(INI_PATH . '/im_api_' . Yf_Registry::get('server_id') . '.ini.php'))
            {
                $file = INI_PATH . '/im_api_' . Yf_Registry::get('server_id') . '.ini.php';
            }
            else
            {
                $file = INI_PATH . '/im_api.ini.php';
            }

            if (!Yf_Utils_File::generatePhpFile($file, $data))
            {
                $status = 250;
                $msg    = __('生成配置文件错误!'.$file);
            }
            else
            {
                $data = $this->getUrl('Config', 'editImApi');

                $status = 200;
                $msg    = __('设置成功!');
            }


        }
        else
        {
            $status = 250;
            $msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('请求错误!');
        }


        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
	 * 列表数据
	 *
	 * @access public
	 */
	public function type()
	{
		$supply_type_rows = array();
		
		//类似数据可以放到前端整理
		$supply_type_row                = array();
		$supply_type_row['sort_index']  = 0;
		$supply_type_row['id']          = 1;
		$supply_type_row['parent_id']   = 0;
		$supply_type_row['detail']      = true;
		$supply_type_row['type_number'] = 'trade';
		$supply_type_row['level']       = 1;
		$supply_type_row['status']      = 0;
		$supply_type_row['remark']      = null;
		$supply_type_row['name']        = 'aaaa';
		
		$supply_type_rows[] = $supply_type_row;
		
		$data          = array();
		$data['items'] = $supply_type_rows;
		
		$this->data->addBody(-140, $data);
	}
    
    
    /**
     *  数据分析API设置页面
     */
    public function analyticsApi(){
        include $view = $this->view->getView();
    }
    
    /**
	 * 设置用户中心API网址及key - 后台独立使用
	 *
	 * @access public
	 */
	public function editAnalyticsApi()
	{
		$analytics_api_row = request_row('analytics_api');
		
		$analytics_api_key       = $analytics_api_row['analytics_api_key'];
		$analytics_api_url      = $analytics_api_row['analytics_api_url'];
		//$analytics_admin_url      = $analytics_api_row['analytics_admin_url'];
        $analytics_app_name       = $analytics_api_row['analytics_app_name'];
        $analytics_app_id      = $analytics_api_row['analytics_app_id'];
        $analytics_statu      = $analytics_api_row['analytics_statu'];
        $analytics_push_time      = $analytics_api_row['analytics_push_time'];

//		//先检测API是否正确
//		$key                = $analytics_api_key;
//		$url                = $analytics_api_url;
//		$formvars           = array();
//		$formvars['app_id'] = $analytics_app_id;
//		$init_rs            = get_url_with_encrypt($key, sprintf('%s?ctl=Api&met=checkApi&typ=json', $url), $formvars);
//		
//		if (200 == $init_rs['status'])
//		{
//			//读取服务列表
//			$status = 200;
//			$msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('sucess');
			
			$data                          = array();
			$data['analytics_api_key']       = $analytics_api_key;
			$data['analytics_api_url']       = $analytics_api_url;
			//$data['analytics_admin_url']       = $analytics_admin_url;
			$data['analytics_app_name']        = $analytics_app_name;
			$data['analytics_app_id'] = $analytics_app_id;
			$data['analytics_statu'] = $analytics_statu;
			$data['analytics_push_time'] = $analytics_push_time;

			if (is_file(INI_PATH . '/analytics_api_' . Yf_Registry::get('server_id') . '.ini.php'))
			{
				$file = INI_PATH . '/analytics_api_' . Yf_Registry::get('server_id') . '.ini.php';
			}
			else
			{
				$file = INI_PATH . '/analytics_api.ini.php';
			}
			
			if (!Yf_Utils_File::generatePhpFile($file, $data))
			{
				$status = 250;
				$msg    = __('生成配置文件错误!');
			}
            $key = Yf_Registry::get('shop_api_key');;
            $url         = Yf_Registry::get('shop_api_url');
            $shop_app_id = Yf_Registry::get('shop_app_id');
            $formvars = array();
            $formvars['app_id']        = $shop_app_id;
            $formvars['admin_account'] = Perm::$row['user_account'];
            $formvars['analytics_api'] = $data;
			$res = get_url_with_encrypt($key, sprintf('%s?ctl=Api_Config&met=editAnalyticsApi&typ=json', $url), $formvars);
            
            if(isset($res['status']) && $res['status'] == 200 ){
                $status = 200;
				$msg    = __('success');
            }else{
                $status = 250;
				$msg    = __('failure!');
            }
//		}
//		else
//		{
//			$status = 250;
//			$msg    = isset($init_rs['msg']) ? $init_rs['msg'] : __('请求错误!');
//		}
		
		
		$this->data->addBody(-140, array(), $res, $status);
	}

	//菜单设置
    public function setMenu()
    {
        $Menu_Base = new Menu_Base();
        $menu_list = $Menu_Base->getByWhere(array('menu_parent_id'=>0));
        include $view = $this->view->getView();
    }

    //查询一级菜单下的二级菜单
    public function getMenuById()
    {
        $parent_menu_id = request_string('menu_id');
        $cond['menu_parent_id'] = $parent_menu_id;
        $Menu_Base = new Menu_Base();
        $menu_list = $Menu_Base->getByWhere($cond);
        $this->data->addBody(-140, $menu_list, 'success', 200);
    }

    //设置菜单
    public function setMenuInfo()
    {
        $first_is_open = request_int('first_is_open');
        $first_menu_id = request_string('first_menu_id');
        $input_first_menu = request_string('input_first_menu');
        $input_first_menu_icon = request_string('input_first_menu_icon');
        $second_is_open = request_int('second_is_open');
        $second_menu_id = request_string('second_menu_id');
        $input_second_menu = request_string('input_second_menu');
        $second_menu_note = request_string('second_menu_note');
        $input_third_menu = request_string('input_third_menu');
        $third_menu_ctl = request_string('third_menu_ctl');
        $third_menu_met = request_string('third_menu_met');
        $third_menu_note = request_string('third_menu_note');

        if(!$first_menu_id && $input_first_menu == ''){
            return $this->data->addBody(-140, array(), '请设置一级菜单', 250);
        }
        if (!$second_menu_id && $input_second_menu == '') {
            return $this->data->addBody(-140, array(), '请设置二级菜单', 250);
        }
        if ($input_third_menu == '') {
            return $this->data->addBody(-140, array(), '请设置三级菜单', 250);
        }

        $first_cond = array();
        $second_cond = array();
        $third_cond = array();
        $rs_row = array();
        $Menu_Base = new Menu_Base();
        $Menu_Base->sql->startTransactionDb();
        if($first_is_open == 1){
            //创建一级菜单
            $first_cond['menu_name'] = $input_first_menu;
            $first_cond['menu_icon'] = $input_first_menu_icon;
            $first_id = $Menu_Base->addBase($first_cond,true);
            check_rs($first_id, $rs_row);
            $second_cond['menu_parent_id'] = $first_id;
        }else{
            $second_cond['menu_parent_id'] = $first_menu_id;
        }

        if ($second_is_open == 1) {
            //创建二级菜单
            $second_cond['menu_name'] = $input_second_menu;
            $second_cond['menu_url_note'] = $second_menu_note;
            $second_id = $Menu_Base->addBase($second_cond, true);
            $third_cond['menu_parent_id'] = $second_id;
            check_rs($second_id, $rs_row);
        } else {
            $third_cond['menu_parent_id'] = $second_menu_id;
        }
        $third_cond['menu_name'] = $input_third_menu;
        $third_cond['menu_url_ctl'] = $third_menu_ctl;
        $third_cond['menu_url_met'] = $third_menu_met;
        $third_cond['menu_url_note'] = $third_menu_note;
        $third_id = $Menu_Base->addBase($third_cond, true);
        check_rs($third_id, $rs_row);
        $flag = is_ok($rs_row);
        if($flag && $Menu_Base->sql->commitDb()){
            $status = 200;
            $msg = 'success';
        }else{
            $Menu_Base->sql->rollBackDb();
            $status = 250;
            $msg = 'failure';
        }

        $this->data->addBody(-140, array(), $msg, $status);

    }

    //手机端分类列表模板选择
    public function setCat()
    {
        $setCat = Web_ConfigModel::value('setWapCat');
        include $view = $this->view->getView();
    }

    //小程序分类列表模板选择
    public function setWxCat()
    {
        $setCat = Web_ConfigModel::value('setWxCat');
        include $view = $this->view->getView();
    }
}
?>