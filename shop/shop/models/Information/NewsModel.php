<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Information_NewsModel extends Information_Base
{
    const  tongguo= 1;
    const  weitongguo= 2;
    const  dengdai= 3;
    
    /**
     * 读取分页列表
     *
     * @param  int $article_id 主键值
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getBaseList($article_id = null, $page = 1, $rows = 100, $sort = 'asc')
    {
        //需要分页如何高效，易扩展
        $offset = $rows * ($page - 1);
        $this->sql->setLimit($offset, $rows);
        $article_id_row = [];
        $article_id_row = $this->selectKeyLimit();
        //读取主键信息
        $total = $this->getFoundRows();
        $data_rows = [];
        if ($article_id_row) {
            $data_rows = $this->getBase($article_id_row);
        }
        $data = [];
        $data['page'] = $page;
        $data['total'] = ceil_r($total / $rows);  //total page
        $data['totalsize'] = $data['total'];
        $data['records'] = count($data_rows);
        $data['items'] = array_values($data_rows);
        
        return $data;
    }
    
    /**
     * 读取分页列表
     *
     * @param  int $article_id 主键值
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getBaseAllList($cond_row = [], $order_row = [], $page = 1, $rows = 100)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);
        
        return $data;
    }
    
    /*
     * 根据一个id获取附近两条数据
     *
     * @param   int $article_id  主键值
     *
     * @return  array $data 返回查询的内容
     */
    public function getNearArticle($article_id)
    {
        $Article_BaseModel = new Article_BaseModel();
        $Article_BaseModel->sql->setLimit(0, 1);
        $data['behind'] = pos($Article_BaseModel->getByWhere(['article_id:<' => $article_id], ['article_id' => 'desc']));
        $Article_BaseModel->sql->setLimit(0, 1);
        $data['front'] = pos($Article_BaseModel->getByWhere(['article_id:>' => $article_id], ['article_id' => 'asc']));
        
        return $data;
    }
    
    /**
     * 读数量
     *
     * @param  int $config_key 主键值
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getCount($cond_row = [])
    {
        return $this->getNum($cond_row);
    }
    
  
}

?>