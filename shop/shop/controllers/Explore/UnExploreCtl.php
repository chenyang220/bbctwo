<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Zhuyt
 */
class Explore_UnExploreCtl extends Controller
{

    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

    }

    /**
     * 我的心得中心\筛选草稿心得
     *
     * @access public
     */
    public function getExploreUserInfo()
    {
        $uid = Perm::$userId;
        $share_user_id = request_int('share_user_id');
        //判断来源
        $from = request_string('from');
        $status = request_int('status'); // 0-文章 1-草稿 2-收藏

        //判断是当前用户访问，还是通过分享链接访问
        if ($uid == $share_user_id || !$share_user_id) {
            $user_id = $uid;
            $data['isFollow'] = 0;
            $is_self = 1;
        } else {
            $is_self = 0;
            //判断当前用户是否为粉丝
            $Explore_UserModel = new Explore_UserModel();
            $res = $Explore_UserModel->getUser($uid);
            $user_base = $res[$uid];
            $u_ids = explode(',', $user_base['user_follow_id']);
            if (in_array($share_user_id, $u_ids)) {
                $data['isFollow'] = 1;
            } else {
                $data['isFollow'] = 0;
            }
            //分享用户访问
            $user_id = $share_user_id;
        }
        //用户基本信息
        $User_Info = new User_Info();
        $user_info = $User_Info->getInfo($user_id);
        $user_info = $user_info[$user_id];
        $user_info['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id=' . $user_id;//用户头像
        $data['user_info'] = $user_info;

        //关注、粉丝、被赞
        $Explore_UserModel = new Explore_UserModel();
        $explore_user = $Explore_UserModel->getOne($user_id);
        $data['explore_user'] = $explore_user;

        //心得文章
        $Explore_BaseModel = new Explore_BaseModel();
        $cond = [];
        $cond['user_id'] = $user_id;
        $cond['is_del'] = Explore_BaseModel::IS_DEL;
        //判断草稿、文章
        if($from && $from == 'draft_edit'){
            $cond['explore_status'] = Explore_BaseModel::DraftStatus;
        }else{
            if($status == 1){
                $cond['explore_status:IN'] = array(2,3,4);
            }else{
                //判断当前被访问用户是否为当前登录用户
                if ($uid == $share_user_id || !$share_user_id) {
                    $cond['explore_status:IN'] = array(0,1);
                }else{
                    $cond['explore_status'] = Explore_BaseModel::NormalStatus;
                }
            }
        }

        $orde_row['explore_create_time'] = 'DESC';
        if($status == 2){
            $Explore_CollectionModel = new Explore_CollectionModel();
            $where['user_id'] = $user_id;
            $row = $Explore_CollectionModel->getByWhere($where);
            $data_exit = array();
            foreach ($row as $i){
                $data_exit = $i;
            }
            $explore_id_x = explode(",", $data_exit['explore_id']);
            $cond['explore_id:IN'] = array_filter($explore_id_x);
        }

        $explore_base = $Explore_BaseModel->getByWhere($cond, $orde_row);

        $explore_count = $Explore_BaseModel->explore_count($user_id, $is_self);
        $data['explore_count'] = $explore_count;

        $explore_id = array_column($explore_base, 'explore_id');
        $Explore_ImagesModel = new Explore_ImagesModel();
        $explore_image = $Explore_ImagesModel->getOneImageByExploreId($explore_id);

        foreach ($explore_base as $k => $v) {
            //判断当前用户是否点赞
            $flag = $Explore_BaseModel->isSupport($uid, $v['explore_id']);
            if(Perm::$userId) {
                if ($flag) {
                    $explore_base[$k]['is_support'] = 1;
                } else {
                    $explore_base[$k]['is_support'] = 0;
                }
            }else{
                $explore_base[$k]['is_support'] = 0;
            }
            $explore_base[$k]['images_url'] = $explore_image[$k]['images_url'];
            $explore_base[$k]['type'] = $explore_image[$k]['type'];
            $explore_base[$k]['explore_create_time'] = date('Y-m-d H:i:s', $v['explore_create_time']);
        }
        $data['explore_base'] = array_values($explore_base);
        $this->data->addBody(-140, $data, $cond);
    }

    /**
     * 获取心得列表
     *
     * @access public
     */
    public function getExploreList()
    {
        $type = request_int('type');
        $search_status = request_int('search_status');
        $search_content = request_string('search_content');
        $page = request_int('page');
        $Explore_BaseModel = new  Explore_BaseModel();

        $data['explore_base'] = $Explore_BaseModel->getExploreList($type, $search_status, $search_content,$page);

        $user_id = Perm::$userId;
        $User_InfoModel = new  User_InfoModel();
        $data['user_info'] = $User_InfoModel->getUser($user_id);


        $Explore_MessageModel = new Explore_MessageModel();
        $data['message_sum'] = $Explore_MessageModel->getUnreadMeaasgeNum();
        $this->data->addBody(-140, $data);
    }

    /**
     * 获取心得详情
     *
     * @access public
     */
    public function getExploreDetail()
    {
        $explore_id = request_int('explore_id');

        $Explore_BaseModel = new  Explore_BaseModel();

        $data = $Explore_BaseModel->getExploreDetail($explore_id);
        $this->data->addBody(-140, $data);

    }

    /**
     * 获取心得所有评论及其回复
     *
     * @access public
     */
    public function getCommentAll()
    {
        $explore_id = request_int('explore_id');
        $row_id = request_string('cont_id');

        $Explore_CommentModel = new Explore_CommentModel();
        $data = $Explore_CommentModel->getCommentAll($explore_id,$row_id);

        $this->data->addBody(-140, $data);

    }

    /**
     * 根据common_id获取所有回复
     *
     * @access public
     */
    public function getReplyAll()
    {
        $comment_id = request_int('comment_id');

        $Explore_ReplyModel = new Explore_ReplyModel();

        $data = $Explore_ReplyModel->getReplyAll($comment_id);

        $this->data->addBody(-140, $data);

    }

    /**
     *根据explore_id获取商品信息
     *
     * @access public
     */
    public function getGoodsByExploreId()
    {
        $explore_id = request_string('explore_id');
        $Explore_BaseModel = new  Explore_BaseModel();
        $data = $Explore_BaseModel->getGoodsByExploreId($explore_id);
        $this->data->addBody(-140, $data);
    }

    /**
     *根据reply_id获取comment_id
     *
     * @access public
     */
    public function getCommentIdByReplyId()
    {
        $reply_id = request_string('reply_id');
        $Explore_ReplyModel = new Explore_ReplyModel();
        $data = $Explore_ReplyModel->getOne($reply_id);
        $this->data->addBody(-140, $data);
    }

    /**
     * 浏览加积分
     */
    public function browseadd()
    {

        $Social_SocialModel = new Social_SocialModel();
        //浏览一次加多少积分
        //$points_browse = Web_ConfigModel::value('points_browse');
        $points_browse = 1;

        //$points_browse_max = Web_ConfigModel::value('points_browse_max');
        $points_browse_max = 10;
        //帖子ID
        $explore_id = request_int("explore_id");
        //用户ID
        $user_id = request_int("u");

        $user_account = request_string("user_account");

        $user = $Social_SocialModel->getOne($user_id);
        if (empty($user)) {
            $var = array(0);
        } else {
            $var = explode(",", $user['explore_id']);
        }
        if (!$user && $user_id) {

            //退还用户使用积分
            //积分日志表增加流水明细
            $Points_LogModel = new Points_LogModel();
            $Points = array();
            $Points['points_log_type'] = 1;
            $Points['class_id'] = Points_LogModel::POINTS_BROWSE;
            $Points['user_id'] = $user_id;
            $Points['user_name'] = $user_account;
            $Points['points_log_points'] = $points_browse;
            $Points['points_log_time'] = date('Y-m-d H:i:s', time());
            $Points['points_log_desc'] = '浏览帖子';
            $Points['points_log_flag'] = 'points_browse';
            //积分流程表数据添加
            $add_points_message = $Points_LogModel->addLog($Points);
            check_rs($add_points_message, $rs_row);
            $User_ResourceModel = new User_ResourceModel();
            //获取积分经验值
            $ce = $User_ResourceModel->getOne($user_id);
            $resource_row = array();
            $resource_row['user_points'] = $ce['user_points'] * 1 + $Points['points_log_points'] * 1;
            $res_flag = $User_ResourceModel->editResource($user_id, $resource_row);
            check_rs($res_flag, $rs_row);
            $data = array();
            $flag = is_ok($rs_row);

            $row['user_id'] = $user_id;
            $row['time'] = date("Y-m-d");
            $row['explore_id'] = $explore_id;
            $row['points_browse_max'] = 1;
            $rel = $Social_SocialModel->addBase($row);
        } else if (!in_array($explore_id, $var) && $user['points_browse_max'] < $points_browse_max || $user['time'] != date("Y-m-d")) {
            //退还用户使用积分
            //积分日志表增加流水明细
            $Points_LogModel = new Points_LogModel();
            $Points = array();
            $Points['points_log_type'] = 1;
            $Points['class_id'] = Points_LogModel::POINTS_BROWSE;
            $Points['user_id'] = $user_id;
            $Points['user_name'] = $user_account;
            $Points['points_log_points'] = $points_browse;
            $Points['points_log_time'] = date('Y-m-d H:i:s', time());
            $Points['points_log_desc'] = '浏览帖子';
            $Points['points_log_flag'] = 'points_browse';
            //积分流程表数据添加
            $add_points_message = $Points_LogModel->addLog($Points);
            check_rs($add_points_message, $rs_row);
            $User_ResourceModel = new User_ResourceModel();
            //获取积分经验值
            $ce = $User_ResourceModel->getOne($user_id);
            $resource_row = array();
            $resource_row['user_points'] = $ce['user_points'] * 1 + $Points['points_log_points'] * 1;
            $res_flag = $User_ResourceModel->editResource($user_id, $resource_row);
            check_rs($res_flag, $rs_row);
            $data = array();
            $flag = is_ok($rs_row);
            if ($flag) {
                // $row['user_id'] = $user_id;
                $row['time'] = date("Y-m-d");

                $row['explore_id'] = $user['explore_id'] . "," . $explore_id;
                $row['points_browse_max'] = $user['points_browse_max'] += 1;
                if ($user['time'] != date("Y-m-d")) {
                    $row['explore_id'] = "";
                    $row['points_browse_max'] = "";
                }
                $Social_SocialModel->editBase($user_id, $row);
                $status = 200;
                $msg = __('success');

            }
            print_r($msg("Y-m-d"));
            die;
        } else {
            print_r("已经添加过了");
            die;
        }

    }

}

?>
