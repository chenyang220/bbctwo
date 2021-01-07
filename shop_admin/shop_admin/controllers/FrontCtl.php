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
class FrontCtl extends AdminController{
   
    public function __construct(&$ctl, $met, $typ){
		parent::__construct($ctl, $met, $typ);

	}
    
    /**
     *  分站信息同步
     * 
     */
    public function manage(){
        include $this -> view -> getView();
    }

    public function style1(){
        include $this -> view -> getView();
    }

    public function style2(){
        include $this -> view -> getView();
    }
    public function groupbuy(){
        include $this -> view -> getView();
    }

    public function discount(){
        include $this -> view -> getView();
    }

    public function pintuan(){
        include $this -> view -> getView();
    }
    public function voucher(){
        include $this -> view -> getView();
    }

    public function redpacket(){
        include $this -> view -> getView();
    }
    
    
    
}

?>
