<?php if (!defined('ROOT_PATH')) exit('No Permission');
/**
 * @author     Yf <service@yuanfeng.cn>
 */
class {{class_name}}Ctl extends Yf_AppController
{
    public ${{class_name_lc}}Model = null;

    /**
     * Constructor
     *
     * @param  string $ctl 控制器目录
     * @param  string $met 控制器方法
     * @param  string $typ 返回数据类型
     * @access public
     */
    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

        //include $this->view->getView();
        $this->{{class_name_lc}}Model = new {{class_name}}Model();
    }

    /**
     * 首页
     * 
     * @access public
     */
    public function index()
    {
        include $this->view->getView();
    }
    
    /**
     * 管理界面
     * 
     * @access public
     */
    public function manage()
    {
        include $this->view->getView();
    }

    /**
     * 列表数据
     * 
     * @access public
     */
    public function lists()
    {
        $user_id = Perm::$userId;

		$page = request_int('page');
		$rows = request_int('rows');
		$sort = request_int('sord');

		$cond_row  = array();
		$order_row = array();

		$data = array();

		if ($skey = request_string('skey'))
		{
			$data = $this->{{class_name_lc}}Model->get{{file_name}}List($cond_row, $order_row, $page, $rows);
		}
		else
		{
			$data = $this->{{class_name_lc}}Model->get{{file_name}}List($cond_row, $order_row, $page, $rows);
		}


		$this->data->addBody(-140, $data);
    }

    /**
     * 读取
     * 
     * @access public
     */
    public function get()
    {
        $user_id = Perm::$userId;

		${{field_name}} = request_int('{{field_name}}');
		$rows = $this->{{class_name_lc}}Model->get{{file_name}}(${{field_name}});

		$data = array();

		if ($rows)
		{
			$data = array_pop($rows);
		}

		$this->data->addBody(-140, $data);
    }

    /**
     * 添加
     *
     * @access public
     */
    public function add()
    {
{{columns_name_str}}

        ${{field_name}} = $this->{{class_name_lc}}Model->add{{file_name}}($data, true);

        if (${{field_name}})
        {
			$msg = __('success');
			$status = 200;
		}
        else
        {
			$msg = __('failure');
			$status = 250;
		}

        $data['{{field_name}}'] = ${{field_name}};

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 删除操作
     *
     * @access public
     */
    public function remove()
    {
        ${{field_name}} = request_int('{{field_name}}');

        $flag = $this->{{class_name_lc}}Model->remove{{file_name}}(${{field_name}});

        if ($flag)
		{
			$msg = __('success');
			$status = 200;
		}
		else
		{
			$msg = __('failure');
			$status = 250;
		}

        $data['{{field_name}}'] = array(${{field_name}});

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 修改
     *
     * @access public
     */
    public function edit()
    {
{{columns_name_str}}

        ${{field_name}} = request_int('{{field_name}}');
		$data_rs = $data;

        unset($data['{{field_name}}']);

        $flag = $this->{{class_name_lc}}Model->edit{{file_name}}(${{field_name}}, $data);
        $this->data->addBody(-140, $data_rs);
    }
}
?>