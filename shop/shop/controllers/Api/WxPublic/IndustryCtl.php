<?php 
if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
class Api_WxPublic_IndustryCtl extends Api_Controller
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
        $this->Web_ConfigModel = new Web_ConfigModel();
    }

    /**
     * 微信公众号行业设置
     * @nsy 2019-03-05
     * */
   	public function set(){
		//初始化
        $init_arr =array(
            'main'=>'',
            'sub'=>'',
			'time'=>''
        );
        $data_tmp =  $this->Web_ConfigModel->getByWhere(array('config_type' => 'wxgzh_industry','config_key'=>'wxpublic_industry_set'));
        if($data_tmp){
            $str = $data_tmp['wxpublic_industry_set']['config_value'];
            $strArr = explode("|",$str);
            $init_arr['main']= $strArr[0];
            $init_arr['sub']= $strArr[1];
			$init_arr['time']= $strArr[2];
        }
		$industry_ini = array();
		$industry_ini_file =  INI_PATH . '/industry.ini.php';
		if(is_file($industry_ini_file)){
			$industry_ini = include_once  $industry_ini_file;
		}
		$result =array(
			'config_data' => $industry_ini,//配置文件数据
			'set_data' => $init_arr,//系统设置数据
		);;
        $this->data->addBody(-140,($result) );
    }


    /**
     * 保存配置
     *
     */
    public function save(){
        $config_type = request_string('config_type');
        $main_industry = request_string('main_industry');
        $sub_industry = request_string('sub_industry');
        if(!$config_type || !$main_industry || !$sub_industry ){
            $status = 250;
            $msg    = '参数不能为空！';
            $data = array();
            return $this->data->addBody(-140, $data, $msg, $status);
        }
		if($main_industry == $sub_industry){
			return $this->data->addBody(-140, array(), '主副行业不能相同！', 250);
		}
        //拼装格式：主行业|副行业|设置时间
        $data_str = $main_industry."|".$sub_industry."|".time();
        //设置行业同步至微信公众号
        $arr = array(
            'industry_id1' => $main_industry,
			'industry_id2' => $sub_industry
        );
        $flag = $this->sync_industry($arr);
		if(!$flag){
			return $this->data->addBody(-140, array(), '主副行业设置失败！', 250);
		}
        $config_rows = $this->Web_ConfigModel->getByWhere(array('config_type' => $config_type,'config_key'=>'wxpublic_industry_set'));
        if(!$config_rows){//新增
			$add_row = array();
			$add_row['config_key'] = 'wxpublic_industry_set';
			$add_row['config_value'] =$data_str ;
			$add_row['config_type'] = $config_type;
			$add_row['config_enable'] = 1;
			$add_row['config_datatype'] = 'string';
			$this->Web_ConfigModel->addConfig($add_row);
        }else{//修改
			$edit_row = array();
			$edit_row['config_value'] = $data_str;
			$this->Web_ConfigModel->editConfig('wxpublic_industry_set', $edit_row);
        }
        $this->data->addBody(-140, array(), '所属行业设置成功', 200);
    }

    /**
     *
     * 同步行业设置到微信公众号
     */
    public function sync_industry($data){
        $token  = $this->Web_ConfigModel->getWxPublicAccessToken();
        $result = wxpublic_set_industry($token['token'],$data);
        if($result['errcode']=='0' && $result['errmsg']=='ok'){
            return true;
        }else{
            return false;
        }
    }


}

?>