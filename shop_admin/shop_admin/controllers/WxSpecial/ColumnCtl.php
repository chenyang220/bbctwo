<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class WxSpecial_ColumnCtl extends AdminController
{
	public $webconfigModel = null;

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
	}

	public function index()
    {
        include $view = $this->view->getView();
    }

    //获取专题栏目数据
    public function getColumnInfo()
    {
        $special = request_string('special');
        $Special_Column = new Special_Column();
        $is_from = 2;//0、pc端  1、wap端 2、小程序端
        $info = $Special_Column->getColumnInfo($special,$is_from);

        if($special == 1){
            $Special_Set = new Special_Set();
            $set_info = $Special_Set->getByWhere(array('is_from'=>$is_from), array('column_set_id' => 'ASC'));
            $info['set_info'] = array_values($set_info);
        }
        //file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'abs.php', print_r($info,true));
        $data = $info? $info:array();
        $msg = 'success';
        $status = 200;
        $this->data->addBody(-140, $data, $msg, $status);
    }

    //添加编辑
    public function AddOrEditColumn()
    {
        $column_id = request_int('column_id');
        $setImage = request_row('setImage');

        $Special_Set = new Special_Set();
        foreach($setImage as $key=>$val){
            $row = array();
            if(!$val['column_set_id']){
                $row['column_set_type'] = $val['type'];
                $row['column_set_image'] = $val['info'];
                $row['is_from'] = 2; //0、pc端  1、wap端 2、小程序端
                $Special_Set->addBase($row);
            }else{
                $row['column_set_type'] = $val['type'];
                $row['column_set_image'] = $val['info'];
                $row['is_from'] = 2; //0、pc端  1、wap端 2、小程序端
                $Special_Set->editBase($val['column_set_id'],$row);
            }
        }

        $cond_row = array();
        $cond_row['special_type'] = request_string('special_type');
        $cond_row['special_back_img'] = request_string('special_back_img');
        $cond_row['special_column_image'] = request_row('image_infos');
        $cond_row['goods_common'] = request_row('goods_common');
        $cond_row['is_from'] = 2;//0、pc端  1、wap端 2、小程序端

        $Special_Column = new Special_Column();
        if($column_id){
            $flag = $Special_Column->editBase($column_id, $cond_row, false);
        }else{
            $flag = $Special_Column->addBase($cond_row);
        }

        if ($flag !== false) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

    //删除版式
    public function removeColumn()
    {
        $special_set_id = request_string('special_set_id');
        $Special_Set = new Special_Set();
        $flag = $Special_Set->removeBase($special_set_id);
        if ($flag !== false) {
            $msg = 'success';
            $status = 200;
        } else {
            $msg = 'failure';
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }


}

?>