<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}

/**
 * @author
 */
class Special_ColumnCtl extends Controller
{

	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);
        $this->initData();
        $this->web = $this->webConfig();
        $this->nav = $this->navIndex();
        $this->cat = $this->catIndex();
	}

	public function index()
    {
        $Special_Column = new Special_Column();
        $cond = array();
        $cond['is_from'] = 0;
        $is_from = request_string('ua');
        if ($is_from =='wxapp') {
           $column_self_is_open = Web_ConfigModel::value("wx_column_self_is_open");
           $cond['is_from'] = 2;
        }elseif($is_from =='wap'){
           $column_self_is_open = Web_ConfigModel::value("column_wap_self_is_open");
           $cond['is_from'] = 1;
        }else{
           $column_self_is_open = Web_ConfigModel::value("column_self_is_open");
           $cond['is_from'] = 0;
        }
        if($column_self_is_open == 1){
            $Special_Set = new Special_Set();
            $set_image = $Special_Set->getByWhere($cond,array('column_set_id'=>'ASC'));
            $cond['special_type'] = 1;
        }else{
            $cond['special_type'] = 0;
        }
        $info = $Special_Column->getColumnInfo($cond);
        if ($this->typ == 'json') {
            $info['set_image'] = $set_image;
            $this->data->addBody(-140, $info);
        }else{

          include $this->view->getView();  
        }
    }


}

?>