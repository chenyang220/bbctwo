<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class User_InfoModel extends User_Info
{
    public static $userSex = array(
        "0" => '女',
        "1" => '男',
        "2" => '保密'
    );

    /**
	 * 读取分页列表
	 *
	 * @param  int $user_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getInfoList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
	{
		return $this->getByWhere($cond_row, $order_row, $page, $rows);
	}


    /**
     * 读取分页列表
     *
     * @param  int $user_id 主键值
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);
        foreach ($data["items"] as $key => $value) {
            $data["items"][$key]["user_sex"] = __(User_InfoModel::$userSex[$value["user_sex"]]);
        }
        return $data;
    }
}

?>