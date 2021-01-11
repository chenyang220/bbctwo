<?php
/**
 * 控制器App基本数据
 * 
 * 初始化App控制器各项参数
 * 
 * @category   Framework
 * @package    Controller
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010远丰仁商
 * @version    1.0
 * @todo       
 */
class Yf_Controller
{
    /**
     * 控制器程序文件所在目录
     * 
     * @access public
     * @var string|null
     */
    public $ctl = null;

    /**
     * 控制器类默认调用的方法
     * 
     * @access public
     * @var string|null
     */
    public $met = null;

    /**
     * 返回个客户端数据类型，html|json
     * 
     * e : 为普通字符串
     * o : 为JSON数组
     * 
     * @access public
     * @var string|null
     */
    public $typ = null;

    /**
     * 控制器程序类名称
     * 
     * @access public
     * @var string|null
     */
    public $className = null;

    /**
     * 控制器程序类路径
     * 
     * @access public
     * @var string|null
     */
    public $path = null;

    /**
     * Constructor
     *
     * @global string $themes 视图风格
     * @param  string $ctl 控制器目录
     * @param  string $act 控制器文件
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     * @access public
     */
    public function __construct($ctl, $met, $typ)
    {
        $this->ctl = &$ctl;
        $this->met = &$met;
        $this->typ = &$typ;
        $extends_flag = false;
        if(false !== strpos($this->ctl, 'Extends_')) {
            $extends_flag = true;
            $class_name = str_replace("Extends_", "", $this->ctl);
        }

        $storeDomain = Yf_Registry::get('storeDomain');

        if( !empty($storeDomain) && $storeDomain['store_domain_status'])
        {
            $shop_id = array_keys($storeDomain['store_domain_store'],$_SERVER['HTTP_HOST']);
            if($shop_id)
            {
                $shop_id = current($shop_id);
                $_COOKIE[$_SERVER['HTTP_HOST']] = $shop_id;
                $_COOKIE['SHOP_ID'] = $shop_id;
                // 如果是店铺的域名，截控制器直接进入店铺
                if(strpos($storeDomain['website_domain'],$_SERVER['HTTP_HOST']) === false) // 网站主域名白名单
                {
                    if( (($_SERVER['HTTP_HOST'] == $storeDomain['store_domain_store'][$shop_id]) && ($this->ctl == 'IndexCtl') && ($this->met == 'index')) )
                    {
                        $this->ctl = 'ShopCtl';
                        $this->met = 'index';
                    }

                }
            }
        }
        
        $this->className = $this->ctl;
        if($extends_flag){
             $this->path = EXTENDS_PATH . '/controllers/' . implode('/', explode('_',$class_name)) . '.php';
        }else{
            $this->path = CTL_PATH . '/' . implode('/', explode('_', $this->ctl)) . '.php';
        }

    }
}
?>