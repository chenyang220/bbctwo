<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class WxPublic_MessageModel extends WxPublic_Message
{

    public static $map = array(
        'match_type'=>array(
            '1'=>'精准匹配',
            '2'=>'模糊匹配'
        ),
        'msg_type'=>array(
            '1'=>'文本类型'
        ),
    );
    public function __construct(){
        parent::__construct();
    }

    /**
	 * 读取分页列表
	 *
	 * @param  int $cat_image_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getPublicMsgList($cond_row = array(), $order_row = array(), $page=1, $rows=100)
	{
		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}

    /**
     *
     * 自动回复消息列表
     */
	public function  autoReplyList($val){
	    $sql ="select * from ".$this->_tableName." where 1=1 and words like '%{$val}%'";
        $result = $this->sql->getAll($sql);
        return $result;
    }
}
?>