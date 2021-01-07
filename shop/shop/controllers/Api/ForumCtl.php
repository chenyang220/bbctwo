<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
}



/**
 * Api接口, 让App等调用
 *
 *
 * @category   Game
 * @package    User
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2015远丰仁商
 * @version    1.0
 * @todo
 */
class Api_ForumCtl extends Api_Controller
{
	public function __construct(&$ctl, $met, $typ)
	{
		parent::__construct($ctl, $met, $typ);

		$this->Front_ForumModel = new Front_ForumModel();
	}
	/**
	 * 显示数据
	 *
	 * @access public
	 */
	public function front()
	{
		$data = $this->Front_ForumModel->getList(array(),array('forum_order'=>'asc'));
		$this->data->addBody(-140, $data);
	}

	/**
	 * 获取版块内容
	 *
	 * @access public
	 */
	public function getForumContent()
	{
		$id = request_int('id');

		$data = $this->Front_ForumModel->getForumContent($id);
		$this->data->addBody(-140, $data);
	}


	/**
	 * 修改首页版块
	 *
	 * @access public
	 */
	public function editForum()
	{
		$id = request_int('id');
		$data['edit_content'] = request_int('edit_content');   //1-左  2- 右
		$data['forum_name'] = request_string('forum_name');
		$data['forum_content'] = request_row('forum_connect');
		$data['forum_state'] = request_int('forum_state');
		$data['forum_style'] = request_int('forum_style');

        $flag = $this->Front_ForumModel->editForumInfo($id, $data);

		if ($flag !== false)
		{
			$msg    = __('success');
			$status = 200;
		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}
		
		$this->data->addBody(-140, array(), $msg, $status);
	}

	//模板首页修改顺序
    public function setForumOrder()
    {
        $order_forum1 = request_row('order_forum1');
        $order_forum2 = request_row('order_forum2');
        $update_forum1['forum_order'] = $order_forum1['order'];
        $update_forum2['forum_order'] = $order_forum2['order'];
        $flag1 = $this->Front_ForumModel->editforum($order_forum1['id'],$update_forum1);
        $flag2 = $this->Front_ForumModel->editforum($order_forum2['id'],$update_forum2);

        $rs_rows = array();
        check_rs($flag1, $rs_rows);
        check_rs($flag2, $rs_rows);
        if (is_ok($rs_rows))
        {
            $msg    = __('success');
            $status = 200;
        }
        else
        {
            $msg    = __('failure');
            $status = 250;
        }
        $this->data->addBody(-140, array(), $msg, $status);
    }

	//添加首页版块
	public function addFrontForum()
	{
		$forum_name = request_string('forum_name');
		$forum_content = request_row('forum_connect');
		$forum_style = request_int('forum_style',1);

		$add_data = array();
		$add_data['forum_name'] = $forum_name;
		$add_data['forum_content'] = $forum_content;
		$add_data['forum_style'] = $forum_style;

		$flag = $this->Front_ForumModel->addFrontForum($add_data);
		$rs_rows = array();
		check_rs($flag['flag'], $rs_rows);
		if (is_ok($rs_rows))
		{
			$msg    = $flag['msg'];
			$status = 200;
		}
		else
		{
			$msg    = $flag['msg'];
			$status = 250;
		}
		$this->data->addBody(-140, array(), $msg, $status);
	}


	//删除首页版块
	public function delFrontForum()
	{
		$id = request_int('id');
		$flag = $this->Front_ForumModel->removeforum($id);

		$rs_rows = array();
		check_rs($flag, $rs_rows);
		if (is_ok($rs_rows))
		{
			$msg    = __('success');
			$status = 200;
		}
		else
		{
			$msg    = __('failure');
			$status = 250;
		}
		$this->data->addBody(-140, array(), $msg, $status);
	}
}

?>