<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Seller_SellerWxMsgModel extends Seller_SellerWxMsg
{

	/**
	 * 读取分页列表
	 *
	 * @param  int $wxpublic_message_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getSellerWxMsgList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{	
		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}

	/**
     *
     * 自动回复消息列表
     */
	public function  autoReplyList($val,$shop_id){
	    $sql ="select * from ".$this->_tableName." where 1=1 and shop_id='{$shop_id}' and words like '%{$val}%'";
        $result = $this->sql->getAll($sql);
        return $result;
    }

    /**
	 * 删除操作
	 * @param int $wxpublic_message_id
	 * @return bool $del_flag 是否成功
	 * @access public
	 */
	public function removeSellerWxMsg($wxpublic_message_id)
	{
		$del_flag = $this->remove($wxpublic_message_id);

		return $del_flag;
	}
}

?>