<?php if (!defined('ROOT_PATH')) {
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
class Live_ApplicationModel extends Live_Application
{
    const CHECK = 1;//通过
    const PASS = 2;//通过
    const NO_PASS = 3;//未通过
    const STOP = 4;//关闭

    /**
     * 读取分页列表
     *
     * @param  int $live_application_id 主键值
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getLiveList($cond_row = array(), $order_row = array(), $page = 1, $rows = 100)
    {
        $sql = "SELECT l.*,s.user_name,s.shop_name,s.shop_tel,c.shop_company_address FROM ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "live_application AS l LEFT JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "shop_base AS s ON ";
        $sql .= " l.shop_id = s.shop_id LEFT JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "shop_company AS c ON ";
        $sql .= " l.shop_id = c.shop_id ";
        $sql .= "WHERE 1 AND l.is_del = 0";
        if($cond_row){
            if ($cond_row['user_name']) {
                $sql .= " AND s.user_name LIKE '%" . $cond_row['user_name'] . "%'";
            }
            if ($cond_row['shop_name']) {
                $sql .= " AND s.shop_name LIKE '%" . $cond_row['shop_name'] . "%'";
            }
            if($cond_row['application_status']){
                $sql .= " AND l.application_status = " . $cond_row['application_status'];
            }
            if($cond_row['live_start']){
                $sql .= " AND l.application_time >= " . $cond_row['live_start'];
            }
            if(request_string('live_end')){
                $sql .= " AND l.application_time <= " . $cond_row['live_end'];
            }
        }
        $sql .= " ORDER BY l.live_application_id DESC ";
        $sql .= " LIMIT " . ($page - 1)*$rows ."," . $rows;
        $res = $this->sql->getAll($sql);

        foreach($res as $k=>$v){
            $res[$k]['application_time'] = date('Y-m-d H:i', $v['application_time']);
            switch($res[$k]['application_status']){
                case 2:
                    $res[$k]['application_status_con'] = '通过';
                    break;
                case 3:
                    $res[$k]['application_status_con'] = '未通过';
                    break;
                case 4:
                    $res[$k]['application_status_con'] = '关闭';
                    break;
                default:
                    $res[$k]['application_status_con'] = '待审核';
                    break;

            }
        }
        $query = "SELECT FOUND_ROWS() total";
        $totalRows = $this->sql->getRow($query);
        $total = $totalRows['total'];
        $data['page'] = $page;
        $data['total'] = ceil_r($total/$rows);
        $data['totalsize'] = $total;
        $data['records'] = $total;
        $data['items'] = $res;
        return $data;
    }

    public function getLiveInfoById($id)
    {
        $sql = "SELECT l.*,s.user_name,s.shop_name,s.shop_tel FROM ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "live_application AS l LEFT JOIN ";
        $sql .= Yf_GeneralOperator::getInstance()->shopTablePerfix() . "shop_base AS s ON ";
        $sql .= " l.shop_id = s.shop_id WHERE 1 ";
        $sql .= " AND l.live_application_id = " . $id;
        $res = $this->sql->getAll($sql);
        $data = current($res);
        $data['application_time'] = date('Y-m-d H:i', $data['application_time']);
        $data['application_end_time'] = date('Y-m-d H:i', $data['application_end_time']);
        return $data;
    }
}

?>