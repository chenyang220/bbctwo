<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class WxPublic_MenuModel extends WxPublic_Menu
{
	public $treeRows   = array();
	public $treeAllKey = null;
	public $catListAll = null;
	public static $map = array(
	    1=>'发送消息',
        2=>'跳转网页',
        3=>'打开小程序'
    );

	/**
	 * 读取分页列表
	 *
	 * @param  int $cat_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getPublicMenuList($cond_row = array(), $order_row = array('id' => 'ASC'), $page = 1, $rows = 100)
	{
		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}

	public function getMenuListData($cond_row = array(), $order_row = array()){
	    return $this->getByWhere($cond_row , $order_row );
    }


    
}

?>