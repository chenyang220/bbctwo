<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Chain_BaseModel extends Chain_Base
{

	/**
	 * 读取分页列表
	 *
	 * @param  int $chain_id 主键值
	 * @return array $rows 返回的查询内容
	 * @access public
	 */
	public function getBaseList($cond_row = array(), $order_row = array(), $page=1, $rows=100)
	{
		return $this->listByWhere($cond_row, $order_row, $page, $rows);
	}

	//获取单个门店信息
	public function getChainInfo ($chain_id)
	{
		return current($this->getBase($chain_id));
	}

	/**
     * 取的附近的门店  单位米
     *
     * @param  float $lat
     * @param  float $lng
     * @param  int $distance 小于范围
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getNearChain($lat, $lng, $distance = 20000, $page = 1, $rows=10)
    {
        $offset = max(0, $rows * ($page-1));
		$a = "(SELECT  s.*, (round(6378.138*2*asin(sqrt(pow(sin( (s.latitude*pi()/180-$lat*pi()/180)/2),2)+cos(s.latitude*pi()/180)*cos($lat*pi()/180)* pow(sin( (s.longitude*pi()/180-$lng*pi()/180)/2),2)))*1000)) as distance FROM " . $this->_tableName . " s  ORDER BY distance ASC limit 200) ";

		$sql = "SELECT * FROM " . $a . $this->_tableName ." WHERE
			distance < $distance
		ORDER BY distance ASC
		limit $offset, $rows
		";

        $shop_rows = $this->sql->getAll($sql);
        $total = $this->getFoundRows();
        $data = array();
        $data['page'] = $page;
        $data['total'] = ceil_r($total / $rows);  //total page
        $data['totalsize'] = $total;
        $data['records'] = $total;
        $data['items'] = array_values($shop_rows);
        
        return $data;
    }

}
?>