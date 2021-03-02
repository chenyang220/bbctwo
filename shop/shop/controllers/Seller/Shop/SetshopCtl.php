<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Banchangle
 */
class Seller_Shop_SetshopCtl extends Seller_Controller {
    
    public $shopBaseModel     = null;
    public $shopClassModel    = null;
    public $shopGradeModel    = null;
    public $shopTemplateModel = null;
    
    /**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     *
     * @access public
     */
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->shopBaseModel     = new Shop_BaseModel();
        $this->shopClassModel    = new Shop_ClassModel();
        $this->shopGradeModel    = new Shop_GradeModel();
        $this->shopTemplateModel = new Shop_TemplateModel();
    }
    
    /**
     * 首页
     *
     * @access public
     */
    public function index()
    {
        $district_parent_id = request_int('pid', 0);
        $Base_DistrictModel = new Base_DistrictModel();
        $district = $Base_DistrictModel->getDistrictTree($district_parent_id);
        //判断是否开启店铺二级域名
        $Web_ConfigModel = new Web_ConfigModel();
        $shop_domain     = $Web_ConfigModel->getByWhere(array('config_type' => 'domain'));
        
        
        //查询出当前店铺信息
        $shop_id['shop_id'] = Perm::$shopId;
        $re                 = $this->shopBaseModel->getBaseOneList($shop_id);
        
        //查看单个店铺二级域名的编辑次数
        $Shop_DomainModel = new Shop_DomainModel();
        $domain_list      = $Shop_DomainModel->getOne($shop_id['shop_id']);


        $Label_Base = array();
        $shop_id = Perm::$shopId;
        $Label_BaseModel = new  Label_BaseModel();
        $Shop_BaseModel = new Shop_BaseModel();
        $Shop_Base = $Shop_BaseModel->getOne($shop_id);
        if ($Shop_Base['label_is_check'] == 0) {
            $label_id = "";
            $label_id_str = "";
        } else {
            $label_id = trim($Shop_Base['label_id'],",");
            $label_id_str = $label_id;
            $label_base_arr = $Label_BaseModel->getByWhere("*");
            $label_name_arr = array_column($label_base_arr, null,"id");

            $shop_label_id_arr = explode(",", trim($label_id,","));
            $shop_label_base = array();
            foreach ($shop_label_id_arr as $key => $shop_label_id) {
                $shop_label_base[$shop_label_id] = $label_name_arr[$shop_label_id];
            }

            foreach ($label_base_arr as $k => $label_base) {
                if ($label_base['label_tag_sort'] == 0) {
                    $label_id = trim($label_id,",") . "," . $label_base['id'];
                }
            }
            
            $label_id_arr = explode(",", trim($label_id,","));
            foreach ($label_id_arr as $key => $label_id) {
                $Label_Base[$label_id] = $label_name_arr[$label_id];
            }
        }
        


        if ('json' == $this->typ)
        {
            $status              = 200;
            $msg                 = __('success');
            $data['shop_domain'] = $shop_domain;
            $data['re']          = $re;
            
            
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        else
        {
            include $this->view->getView();
        }
    }
    

    /**
     * 移动端店铺模板
     *
     * @access public
     */
    public function wapTemplate () {
        $Shop_BaseModel = new Shop_BaseModel();
        $shop_id = Perm::$shopId;
        $Shop_Base = $Shop_BaseModel->getOne($shop_id);
        include $this->view->getView();
    }


    /**
     * 修改店铺信息
     *
     * @access public
     */
    public function editShop()
    {
        $edit_shop_row = request_row("shop");
        //判断是否开启店铺二级域名
        $Web_ConfigModel = new Web_ConfigModel();
        $shop_domain     = $Web_ConfigModel->getByWhere(array('config_type' => 'domain'));
        $domain_modify_frequency = Web_ConfigModel::Value('domain_modify_frequency');
        
        //是否可以修改并且修改次数没达到上线
        if (empty($shop_domain['is_modify']['config_value']) || empty($edit_shop_row['shop_domain']))
        {
            unset($edit_shop_row['shop_domain']);
        }
        
        $shop_id          = request_int('shop_id');
        $Shop_DomainModel = new Shop_DomainModel();
        $domain_list      = $Shop_DomainModel->getOne($shop_id);



        if (request_string('label_id_str')) {
            // unset($edit_shop_row['label_id_str']);
            $edit_shop_row['label_id'] = trim(request_string('label_id_str'),",");
            $edit_shop_row['label_is_check'] = 0;
        }
        if ($domain_list)
        {
            $can_times = intval($domain_list['shop_edit_domain']) + 1;
            
            if ($can_times > $domain_modify_frequency)
            {
                unset($edit_shop_row['shop_domain']);
            }
        }else{
            $add_row['shop_id'] = $shop_id;
            $add_row['shop_sub_domain'] = $edit_shop_row['shop_domain'];
            $add_row['shop_edit_domain'] = 1;
            $add_row['shop_self_domain'] = Web_ConfigModel::Value('retain_domain');
            $flag                         = $Shop_DomainModel->addDomain($add_row);
        }



        $flag             = $this->shopBaseModel->editBase($shop_id, $edit_shop_row);




        if ($can_times <= $domain_modify_frequency && !empty($edit_shop_row['shop_domain']) && !empty($shop_domain['is_modify']['config_value']))
        {
            if($domain_list['shop_sub_domain'] != $edit_shop_row['shop_domain']){
                $field_row['shop_sub_domain']  = $edit_shop_row['shop_domain'];
                $field_row['shop_edit_domain'] = $domain_list['shop_edit_domain'] + 1;
                $flag                         = $Shop_DomainModel->editDomain($shop_id, $field_row);
            }
        }
        else
        {
            $msg    = __('域名修改次数已达上限');
        }

        $this->fileDoMain(); // 缓存店铺域名信息
        
        if ($flag === false)
        {
            $status = 250;
            $msg    = __('failure');
        }
        else
        {
            $status = 200;
            $msg    = __('success');
        }
        
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 生成店铺域名缓存列表
     *
     * @access public
     */
    public function fileDoMain(){

        $data = [];
        $Web_ConfigModel = new Web_ConfigModel();
        $store_domain = $Web_ConfigModel->getOneByWhere(['config_key'=>'shop_domain']); // 是否开启店铺独立域名
       
        if($store_domain['config_value'] == 1){
           
            $Shop_DomainModel = new Shop_DomainModel();
            $domainMap = array();
            $domainMap['shop_sub_domain:!='] = null;
            $doMainList = $Shop_DomainModel->getByWhere($domainMap);
          
            if( !empty($doMainList) ){
                $store = [];
                $url = [];
                $dataDoMain = [];
                foreach ($doMainList as $k => $r){
                    $store[$r['shop_id']] = $r['shop_sub_domain'];
                    $url[$r['shop_sub_domain']] = $r['shop_id'];
                }
                $dataDoMain['store_domain']['store_domain_status'] = $store_domain['config_value'];
                $dataDoMain['store_domain']['website_domain']      = Yf_Registry::get('shop_api_url');
                $dataDoMain['store_domain']['store_domain_store']  = $store;
                //$dataDoMain['store_domain']['store_domain_url']  = $url;

                if (is_file(INI_PATH . '/store.ini.php'))
                {
                    $file = INI_PATH . '/store.ini.php';
                }
                else
                {
                    $file = INI_PATH . '/store.ini.php';
                    fopen ($file,"w+");
                }

                chmod($file, 0777);

                if (Yf_Utils_File::generatePhpFile($file, $dataDoMain))
                {
                    $status = 250;
                    $msg    = __('生成配置文件成功!');
                } else {
                    $status = 500;
                    $msg    = __('生成配置文件错误!');
                }
                return ['status'=>$status,'msg'=>$msg];
            }
        }
    }
    
    
    /**
     * 设置幻灯
     *
     * @access public
     */
    public function slide()
    {
        
        $array          = array(
            "0" => 0,
            "1" => 1,
            "2" => 2,
            "3" => 3,
            "4" => 4
        );
        $shop_id        = Perm::$shopId;
        $re             = $this->shopBaseModel->getOne($shop_id);
        $de['slide']    = $re['shop_slide'] ? explode(',', $re['shop_slide']) : "";
        $de['slideurl'] = $re['shop_slide'] ? explode(',', $re['shop_slideurl']) : "";
        if ('json' == $this->typ)
        {
            $status           = 200;
            $msg              = __('success');
            $data['slide']    = $de['slide'];
            $data['re']       = $re;
            $data['slideurl'] = $de['slideurl'];
            $data['array']    = $array;
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        else
        {
            include $this->view->getView();
        }
    }
    
    
    /**
     * 修改幻灯
     *
     * @access public
     */
    public function editSlide()
    {
        
        $data['shop_slide']    = implode(",", request_row("slide"));
        $data['shop_slideurl'] = implode(",", request_row("slideurl"));
        $shop_id               = Perm::$shopId;
        $flag                  = $this->shopBaseModel->editBase($shop_id, $data);
        if ($flag === false)
        {
            $status = 250;
            $msg    = __('failure');
        }
        else
        {
            $status = 200;
            $msg    = __('success');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
        
    }
    
    
    /**
     * 设置主题
     *
     * @access public
     */
    public function theme()
    {
        
        //店铺当前的模板
        $shop_id      = Perm::$shopId;
        $re           = $this->shopBaseModel->getOne($shop_id);
        $default_temp = $this->shopTemplateModel->getOne($re['shop_template']);
        
        if ($re['shop_self_support'] == "true")
        {
            //自营店铺绑定全部模板
            $shopTemplateModel = new Shop_TemplateModel();
            $grade_temp        = $shopTemplateModel->getByWhere();
        }
        else
        {
            //查询出当前店铺等级，根据等级查看能用多少模板
            $grade_temp = $this->shopGradeModel->getGradetemplist($re['shop_grade_id']);
        }
        
        if ('json' == $this->typ)
        {
            $status               = 200;
            $msg                  = __('success');
            $data['default_temp'] = $default_temp;
            $data['grade_temp']   = $grade_temp;
            $this->data->addBody(-140, $data, $msg, $status);
            
        }
        else
        {
            include $this->view->getView();
        }
    }
    
    /**
     * 修改店铺主题
     *
     * @access public
     */
    public function setShopTemp()
    {
        
        $shop_temp['shop_template'] = request_string("shop_temp_name");
        $shop_id                    = Perm::$shopId;
        
        $flag = $this->shopBaseModel->editBase($shop_id, $shop_temp);
        if ($flag === false)
        {
            $status = 250;
            $msg    = __('failure');
        }
        else
        {
            $status = 200;
            $msg    = __('success');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
        
    }
	function trimall($str)//删除空格
	{
		$qian=array(" ","　","\t","\n","\r");
		$hou=array("","","","","");
		return str_replace($qian,$hou,$str); 
	}
    public function service()
    {
        //查找店铺默认的售后服务内容
        $shop_id                    = Perm::$shopId;

        $data = $this->shopBaseModel->getOne($shop_id);
		//$data && $data = $this->trimall($data);
        if ('json' == $this->typ)
        {
            $status               = 200;
            $msg                  = __('success');
            $this->data->addBody(-140, $data, $msg, $status);

        }
        else
        {
            include $this->view->getView();
        }
    }

    public function editShopCommonService()
    {
        $shop_id                    = Perm::$shopId;
        $common_service = request_string('common_service');
		//替换空格，清除HTML标签
		$prg = preg_replace("/(\&nbsp\;)/", " ", strip_tags($common_service));//strip_tags($common_service);
		$sql ="SELECT CHAR_LENGTH(\"".$prg."\") length";
		$count = $this->shopBaseModel->sql->getRow($sql);
		if($count['length'] > 2000){
            $msg    = __('请最多输入2000个字符');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        //判断是否存在违禁词 
        $matche_row = array();
        //有违禁词
        if (Text_Filter::checkBanned($common_service, $matche_row))
        {
            $data   = array();
            $msg    = __('含有违禁词');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        if (Text_Filter::checkBanned($common_service, $matche_row))
        {
            
        }

        $shop_edit_row = array();
        $shop_edit_row['shop_common_service'] = $common_service;

        $flag = $this->shopBaseModel->editBase($shop_id, $shop_edit_row);
        if ($flag === false)
        {
            $status = 250;
            $msg    = __('failure');
        }
        else
        {
            $status = 200;
            $msg    = __('success');
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);

    }
}

?>