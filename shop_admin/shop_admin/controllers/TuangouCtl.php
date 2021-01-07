<?php 
if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author    tech40@yuanfeng021.com
 * 统计数据初始化
 * 
 */
class TuangouCtl extends AdminController{
   
    public function __construct(&$ctl, $met, $typ){
		parent::__construct($ctl, $met, $typ);

	}
    
    /**
     *  分站信息同步
     * 
     */
    public function tuangouInstall(){
        include $this -> view -> getView();
    }
    
    
    
}

?>
