<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class User_TagRecModel extends User_TagRec
{
	
	/**
	 * 读取分页列表
	 *
	 * @param  array $cond_row 查询条件
	 * @param  array $order_row 排序信息
	 * @param  array $page 当前页码
	 * @param  array $rows 每页记录数
	 * @return array $data 返回的查询内容
	 * @access public
	 */
	public function getTagRecList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		
		$data = $this->listByWhere($cond_row, $order_row, $page, $rows);

		return $data;
	}

	/**
	 * 读取列表
	 *
	 * @param  array $cond_row 查询条件
	 * @return array $data 返回的查询内容
	 * @access public
	 */
	public function getRecList($cond_row = array())
	{
		
		$data = $this->getByWhere($cond_row);

		return $data;
	}

	/**
	 * 读取一个详情
	 *
	 * @param  array $cond_row 查询条件
	 * @return array $data 返回的查询内容
	 * @access public
	 */
	public function getTagRecDetail($cond_row)
	{
		$data = $this->getOneByWhere($cond_row);

		return $data;
	}

	public function getRecTagInfo($user_id)
    {
        $sql = "SELECT r.*,t.user_tag_name FROM yf_user_tag_rec r LEFT JOIN yf_user_tag t ON r.user_tag_id=t.user_tag_id WHERE 1=1 AND r.user_id=" . $user_id;
        $data = $this->sql->getAll($sql);
        $tag_name = array_column($data,'user_tag_name');
        $tag_name_info = '';
        if($tag_name){
            $tag_name_info = implode(",", $tag_name);
        }

        $tag_name_con = '';
        if($tag_name){
            foreach($tag_name as $k=>$v){
                if($k <= 1){
                    $tag_name_con .= mb_substr($v,0,5 , 'utf-8') . ",";
                }
            }
        }
        $data['tag_name'] = $tag_name_info;
        if(count($tag_name ) > 2){
            $data['tag_name_con'] = trim($tag_name_con,",") . "...";
        }else{
            $data['tag_name_con'] = trim($tag_name_con, ",");
        }
        return $data;
    }
}

?>