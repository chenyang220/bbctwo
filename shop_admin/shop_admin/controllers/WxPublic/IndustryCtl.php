<?php 
if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
class  WxPublic_IndustryCtl extends AdminController
{
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);
    }
	
	
	/**
	*
	* 微信公众号模板消息
	*
	**/
	public function tpl(){
		$open_status = Web_ConfigModel::value("wxpublic_tpl_open_status",'-1');
		include $this->view->getView();
	}
	
	/**
	*
	* 保存模板消息设置
	**/
	public function setTpl(){
		$status = request_int('tpl_send',-1);
		if($status<0){
			return $this->data->addBody(-140, array(),'请选择开启或者关闭！',250);
		}
		$key = 'wxpublic_tpl_open_status';  
		$web_config_model = new Web_ConfigModel();
		$conf = $web_config_model->getOne($key);
		if($conf){
			$flag = $web_config_model->editConfig($key,array('config_value'=>$status));
		} else {
			$flag = $web_config_model->addConfig(array('config_key'=>$key,'config_value'=>$status,'config_datatype'=>'string'));
		}
		$this->set_cache_by_key($key,$status);
		$this->data->addBody(-140, array(),'模板消息推送状态设置成功！',200);
	}
	
	/**
	*
	*根据key，设置缓存
	*/
	protected function set_cache_by_key($key,$rows_db,$_cacheName = "default",$_cacheKeyPrefix  = 'c|m|'){
		$Yf_Cache = Yf_Cache::create($_cacheName);
		$Yf_Registry = Yf_Registry::getInstance();
		if (1 == $Yf_Registry['config_cache'][$_cacheName]['cacheType']){
			if ($key){
				return $Yf_Cache->save($rows_db, $_cacheKeyPrefix . $key);
			}else{
				return $Yf_Cache->save($rows_db, null);
			}
		}else{
			if($key){
				return $Yf_Cache->save($rows_db, $_cacheKeyPrefix . $key, null, 0, $expire);
			}else{
				return $Yf_Cache->save($rows_db, null, null, 0, $expire);
			}
		}
	}

}

?>