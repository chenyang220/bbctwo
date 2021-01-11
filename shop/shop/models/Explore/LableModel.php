<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_LableModel extends Explore_Lable
{


    public function getExploreLable($lable_content = '')
    {
        $data = array();
        //1.精确查询
        $lable = $this->getByWhere(array('lable_content'=>$lable_content));

        $lable = array_values($lable);
        $lable_id = $lable[0]['lable_id'];

        $data['lable'] = $lable[0];

        $sql = 'select * from '. TABEL_PREFIX .'explore_lable where lable_content like \'%'.$lable_content.'%\' and lable_id !='.$lable_id.' ORDER BY lable_id ASC limit 0,20';

        $lable_list = $this -> sql -> getAll($sql);

        $data['lable_list'] = $lable_list;

        $data['lable_content'] = $lable_content;
        return $data;

    }

    public function getHotLable()
    {
        $data = array();
        $data['lable'] = false;
        $data['lable_content'] = '';
        //搜索最近一个月使用最多的标签3个
        $sql = 'select * from '. TABEL_PREFIX .'explore_lable  ORDER BY lable_month_count DESC , lable_id DESC limit 0,3';

        $lable_month_list = $this -> sql -> getAll($sql);

        $lable_month_id = array_column($lable_month_list,'lable_id');

        $sql2 = 'select * from '. TABEL_PREFIX .'explore_lable where lable_id not in ('. implode(',',$lable_month_id) .')   ORDER BY lable_used_count DESC , lable_id DESC limit 0,2';

        $lable_count_list = $this -> sql -> getAll($sql2);

        $data['lable_list'] = array_merge($lable_month_list,$lable_count_list);

        return $data;

    }

    public function getLableInfo($lable_id = array())
    {
        $lable = $this->getLable($lable_id);
        $lable = array_values($lable);

        if($lable) {
            foreach ($lable as $key => $val) {
                $data[$key]['lable_id'] = $val['lable_id'];
                $data[$key]['lable_content'] = $val['lable_content'];
            }
        }

        return $data;

    }
}

?>