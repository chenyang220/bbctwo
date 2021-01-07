<?php
/**
 * 不同应用之间通用数据表操作类
 * @category   Framework
 * @author     nsy
 * @copyright  Copyright (c) 2019-06-22 远丰仁商
 */
class Yf_GeneralOperator
{
    public static $_instance;//对象实例
    private $config;//配置实例
    public  static $maps =array(
        'shop'=>102,
        'ucenter'=>104,
        'paycent'=>105,
    );

    /**
     * 构造函数
     *
     */
    protected function __construct()
    {
    }

    //防止克隆
    private function __clone(){
    }

    /**
     * 单例，获取实例对象
     * @return Yf_GeneralOperator
     *
     */
    final public static function getInstance()
    {
        if (!@(self::$_instance instanceof self))
        {
            self::$_instance = new self();
            self::$_instance->config();
        }
        return self::$_instance;
    }

    /**
     * 载入配置项
     *
     */
    public function config()
    {
        $general_db = array();
        if (is_file(INI_PATH . '/general_db.ini.php')) {
            $general_db = include INI_PATH . '/general_db.ini.php';
        }
        $this->config = $general_db;
    }

    /**
     * shop表前缀
     */
    public function shopTablePerfix(){
        return $this->config[self::$maps['shop']]['shop'];
    }

    /**
     * shop_admin表前缀
     */
    public function shopAdminTablePerfix(){
        return $this->config[self::$maps['shop']]['shop_admin'];
    }

    /**
     * ucenter表前缀
     */
    public function ucenterTablePerfix(){
        return $this->config[self::$maps['ucenter']]['ucenter'];
    }

    /**
     * ucenter_admin表前缀
     */
    public function ucenterAdminTablePerfix(){
        return $this->config[self::$maps['ucenter']]['ucenter_admin'];
    }

    /**
     * paycent表前缀
     */
    public function paycentTablePerfix(){
        return $this->config[self::$maps['paycent']]['paycent'];
    }

    /**
     * paycent_admin表前缀
     */
    public function paycentAdminTablePerfix(){
        return $this->config[self::$maps['paycent']]['paycenter_admin'];
    }

}
?>