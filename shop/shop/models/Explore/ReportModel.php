<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_ReportModel extends Explore_Report
{
	public function __construct()
    {
        parent::__construct();


        $this->reportStatus = array(
            '0' => __('未处理'),
            '1' => __('已处理'),
            '2' => __('已处理'),
        );
        $this->infoStatus = array(
            '0' => __('未处理'),
            '1' => __('审核通过并已下架该心得'),
            '2' => __('审核不通过'),
        );

        $this->userInfoModel 	  		= new User_InfoModel();
		$this->ExploreBaseModel   		= new Explore_BaseModel();
		$this->ExploreImagesModel 		= new Explore_ImagesModel();
		$this->ExploreLableModel  		= new Explore_LableModel();
		$this->ExploreImagesGoodsModel  = new Explore_ImagesGoodsModel();
		$this->GoodsBaseModel  	  		= new Goods_BaseModel();
		$this->ExploreMessageModel 		= new Explore_MessageModel();

    }
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
	public function getReportList($user_name=null, $explore_title=null, $explore_reason=null, $explore_status=null,$page=1,$rows=100)
	{
	    $cond_row = array();
	    $order_row = array();
        //举报原因表
        if($explore_reason >= 0) {
            $cond_row['report_reason_id'] = $explore_reason;
        }

        //处理状态
        if($explore_status) {
            if($explore_status == 1) {
                $cond_row['report_status'] = 0;
            } else {
                $cond_row['report_status:>'] = 0;
            }

        }

        //根据用户名，模糊查找出user_id
        if($user_name) {
            $user_sql = 'SELECT user_id FROM '.TABEL_PREFIX.'user_base WHERE user_account LIKE \'%'.$user_name.'%\'';
            $user_id_row = $this->sql->getAll($user_sql);
            $user_id = array_column($user_id_row,'user_id');
            $cond_row['user_id:IN'] = $user_id;
        }

        //被举报的心得标题
        if ($explore_title) {
            $explore_sql = 'SELECT explore_id FROM '.TABEL_PREFIX.'explore_base WHERE explore_title LIKE \'%'.$explore_title.'%\'';
            $explore_id_row = $this->sql->getAll($explore_sql);
            $explore_id = array_column($explore_id_row,'explore_id');
            $cond_row['explore_id:IN'] = $explore_id;
        }

        $order_row['report_id']     = 'DESC';
		$data = $this->listByWhere($cond_row, $order_row, $page, $rows);

		$User_BaseModel = new User_BaseModel();
		$Explore_BaseModel = new Explore_BaseModel();

		foreach ($data["items"] as $key => $val)
		{
			$info 	 = $User_BaseModel->getOne($val['user_id']); //举报人信息
			$expinfo = $Explore_BaseModel->getOne($val['explore_id']); //被举报的心得信息

			$data["items"][$key]["explore_content"] 	= $expinfo['explore_content'];//心得内容
			$data["items"][$key]["explore_create_time"] = date("Y:m:d",$expinfo['explore_create_time']);//心得添加时间
			$data["items"][$key]["explore_lable"] 		= $expinfo['explore_lable'];//心得标签
			$data["items"][$key]["explore_like_count"]  = $expinfo['explore_like_count'];//心得点赞数
			$data["items"][$key]["explore_like_user"] 	= $expinfo['explore_like_user'];//心得点赞会员
			$data["items"][$key]["explore_status"] 		= $expinfo['explore_status'];//心得状态,  0-正常 1-下架
			$data["items"][$key]["explore_title"] 		= $expinfo['explore_title'];//心得标题
			$data["items"][$key]["is_del"] 				= $expinfo['is_del'];//心得是否删除  0-未删除 1-已删除
			$data["items"][$key]["touser_name"] 		= $expinfo['user_account'];//心得作者，被举报人
			$data["items"][$key]["explorename"] 		= $info['user_account'];
			$data["items"][$key]["report_time"] 		= date("Y:m:d",$val['report_time']);
			$data["items"][$key]["report_handle_time"]  = date("Y:m:d",$val['report_handle_time']);
			$data["items"][$key]["reportmsg"] 			= $this->reportStatus[$val['report_status']];

		}

		return $data;
	}

	/*
     * 获取单条举报信息
     */
	public function getReportDetail($report_id = null)
	{
		$report = $this->getOne($report_id);
		$report['report_time'] = date("Y-m-d H:i",$report['report_time']);
		$report['report_handle_time'] = date("Y-m-d H:i",$report['report_handle_time']);
        $report["reportmsg"] 			= $this->infoStatus[$report['report_status']];

        $User_BaseModel = new User_BaseModel();
        $user = $User_BaseModel->getOne($report['user_id']);
        $report['user_name'] = $user['user_account'];

		$Explore_BaseModel = new Explore_BaseModel();
		$explore = $Explore_BaseModel->getExploreInfo($report['explore_id']);
        $count 		= $this->listByWhere(array("explore_id"=>$report['explore_id'],"report_status"=>0)); //计算此心得有多少人举报

        $data= array();
        $data["count"] 		 		= $count['records'];
        $data["report"] 		 	= $report;
        $data["explore"] 		 	= $explore;
      
        return $data;
	}
	/*
     * 举报用户心得添加
     */
	public  function addExploreReport($explore_id=null,$report_reason_id=null,$report_reason='')
	{
	    $add_row = array();

        //根据explore_id查找user_id
        $Explore_BaseModel = new Explore_BaseModel();
        $explore = $Explore_BaseModel->getOne($explore_id);
        $add_row['explore_id'] = $explore_id;
        $add_row['to_user_id'] = $explore['user_id'];

        $add_row['user_id'] = Perm::$userId;
        $add_row['report_time'] = time();
        $add_row['report_reason_id'] = $report_reason_id;

        //查找举报原因
        if($report_reason_id) {
            $ReportReasonModel      = new Explore_ReportReasonModel();

            $reason = $ReportReasonModel->getOne($report_reason_id);
            $add_row['report_reason'] = $reason['explore_report_reason_content'];
        } else {
            if(!$report_reason) {
                $report_reason = '其他';
            }
            $add_row['report_reason'] = $report_reason;
        }
        
        $flag = $this->addReport($add_row);

        return $flag;
	}
	/*
     * 后台举报用户心得编辑
     */
	public function editReportDetail($report_id=null,$explore_id=null,$report_handle=null,$report_status=null)
	{
	    $rs_row = array();

	    $Explore_BaseModel = new Explore_BaseModel();
	    $report = $this->getOne($report_id);

	    $User_BaseModel = new User_BaseModel();
	    $user = $User_BaseModel->getOne($report['user_id']);

		//如果审核不通过，执行单条记录
		if ($report_status == 2) {
		    $edit_row = array();
		    $edit_row['report_status'] = $report_status;
		    $edit_row['report_handle'] = $report_handle;
		    $edit_row['report_handle_time'] = time();
            $flag = $this->editReport($report_id, $edit_row);
            check_rs($flag,$rs_row);

            //审核不通过，给举报者发通知信息
            $Explore_MessageModel = new Explore_MessageModel();
            $add_row = array();
            $add_row['message_user_id'] = $report['user_id'];
            $add_row['message_user_name'] = $user['user_account'];
            $add_row['message_type'] = Explore_MessageModel::REPORT;
            $add_row['message_title'] = __('举报未通过');
            $add_row['message_content'] = $report_handle;
            $add_row['active_id'] = $report_id;
            $add_row['message_create_time'] = time();
            $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
            check_rs($add_flag,$rs_row);
		}

        //如果是审核通过，就下架心得
        if ($report_status == 1) {

            //1.下架心得
            $flag = $Explore_BaseModel->editBase($explore_id,array("explore_status"=>1));
            check_rs($flag,$rs_row);

            //2.批量处理改心得的举报
            $sql = "SELECT * FROM ".TABEL_PREFIX."explore_report WHERE explore_id=".$explore_id." AND report_status=0";
            $report_list = $this->sql->getAll($sql);
            foreach ($report_list as $key => $val) {
                //修改举报信息状态
                $edit_row = array();
                $edit_row['report_status'] = $report_status;
                $edit_row['report_handle'] = $report_handle;
                $edit_row['report_handle_time'] = time();
                $flag = $this->editReport($val['report_id'], $edit_row);
                check_rs($flag,$rs_row);

                //给举报者发消息通知
                $vuser = $User_BaseModel->getOne($val['user_id']);
                $Explore_MessageModel = new Explore_MessageModel();
                $add_row = array();
                $add_row['message_user_id'] = $val['user_id'];
                $add_row['message_user_name'] = $vuser['user_account'];
                $add_row['message_type'] = Explore_MessageModel::REPORT;
                $add_row['message_title'] = __('举报成功');
                $add_row['message_content'] = $report_handle;
                $add_row['active_id'] = $val['report_id'];
                $add_row['message_create_time'] = time();
                $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
                check_rs($add_flag,$rs_row);
            }

            //3.被举报用户收到信息
            $Explore_BaseModel = new Explore_BaseModel();
            $explore = $Explore_BaseModel->getOne($explore_id);

            $Explore_MessageModel = new Explore_MessageModel();
            $add_row = array();
            $add_row['message_user_id'] = $explore['user_id'];
            $add_row['message_user_name'] = $explore['user_account'];
            $add_row['message_type'] = Explore_MessageModel::BREPORT;
            $add_row['message_title'] = __('举报成功');
            $add_row['message_content'] = $report_handle;
            $add_row['active_user_id'] = $report['user_id'];
            $add_row['active_id'] = $report_id;
            $add_row['message_create_time'] = time();
            $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
            check_rs($add_flag,$rs_row);
        }

		return is_ok($rs_row);
	}

	//获取举报详情
	public function getReportInfo($report_id = null)
    {
        $data = $this->getOne($report_id);

        $User_BaseModel = new User_BaseModel();

        $user_info = $User_BaseModel->getOne($data['user_id']);
        $data['user_account'] = $user_info['user_account'];

        $to_user_info = $User_BaseModel->getOne($data['to_user_id']);
        $data['to_user_account'] = $to_user_info['user_account'];

        return $data;
    }

    //获取用户是否正在举报改心得
    public function getUserExplore($user_id = null,$explore_id=null,$report_status=0)
    {
        $sql = 'SELECT * FROM '.TABEL_PREFIX.'explore_report WHERE 1 AND user_id='.$user_id.' AND explore_id='.$explore_id.' AND report_status='.$report_status;

        $data = $this->sql->getAll($sql);

        return $data;
    }

}

?>