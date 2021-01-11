<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 *
 *
 * @category   Framework
 * @package    __init__
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010远丰仁商
 * @version    1.0
 * @todo
 */
class Menu_Base extends Yf_Model
{
	public $_cacheKeyPrefix  = 'c|menu|';
	public $_cacheName       = 'menu';
	public $_tableName       = 'menu';
	public $_tablePrimaryKey = 'menu_id';

	/**
	 * @param string $user User Object
	 * @var   string $db_id 指定需要连接的数据库Id
	 * @return void
	 */
	public function __construct(&$db_id = 'shop_admin', &$user = null)
	{
		$this->_tableName = TABEL_PREFIX . $this->_tableName;
		parent::__construct($db_id, $user);
	}

    /**
     * 插入
     * @param array $field_row 插入数据信息
     * @param bool $return_insert_id 是否返回inset id
     * @param array $field_row 信息
     * @return bool  是否成功
     * @access public
     */
    public function addBase($field_row, $return_insert_id = false)
    {
        $add_flag = $this->add($field_row, $return_insert_id);

        //$this->removeKey($config_key);
        return $add_flag;
    }

	/**
	 * 从数据库读取数据
	 *
	 * @param  array $cond_row
	 * @return array $top_menu 返回的查询内容
	 * @access public
	 */
	public function getMenus($cond_row = array())
	{	
		$cond_row['menu_parent_id'] = 0;
		$top_menu = $this->getByWhere($cond_row);

		foreach($top_menu as $k=>$item){
			$top_menu[$k]['next_menus'] = $this->getByWhere(array('menu_parent_id'=>$k));
			
			foreach($top_menu[$k]['next_menus'] as $key=>$val){
				$top_menu[$k]['next_menus'][$key]['third_menus'] = $this->getByWhere(array('menu_parent_id'=>$key),array('menu_order'=>'ASC'));
				// 取出三级菜单中的第一条数据，将rights_id,menu_url_ctl,menu_url_met,menu_url_parem赋值给二级菜单的对应字段
				if($top_menu[$k]['next_menus'][$key]['third_menus']){
					$third_first = reset($top_menu[$k]['next_menus'][$key]['third_menus']);
					$top_menu[$k]['next_menus'][$key]['menu_url_ctl']=$third_first['menu_url_ctl'];
					$top_menu[$k]['next_menus'][$key]['menu_url_met']=$third_first['menu_url_met'];
					$top_menu[$k]['next_menus'][$key]['menu_url_parem']=$third_first['menu_url_parem'];
//					$top_menu[$k]['next_menus'][$key]['rights_id']=$third_first['rights_id'];
				}
				

			}

		}
		$shop_formvars = array();
        $shop_url = Yf_Registry::get('shop_api_url');
        $shop_key = Yf_Registry::get('shop_api_key');
        $shop_formvars['app_id']    = Yf_Registry::get('shop_app_id');
        $init_rs = get_url_with_encrypt($shop_key, $shop_url."index.php?ctl=Api_Fenxiao&met=getStatus&typ=json", $shop_formvars);
        if($init_rs['data']['status']==0){
        	unset($top_menu['16000']['next_menus']['90001']);	
        }
		//暂时隐藏手机端--应用安装，放到下个版本优化
		unset($top_menu['19000']['next_menus']['19003']);
		return $top_menu;
	}
}

?>