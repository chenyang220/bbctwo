<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}

/**
 * @author     Zhuyt
 */
class Explore_ExploreCtl extends Controller
{

    public function __construct(&$ctl, $met, $typ)
    {
        parent::__construct($ctl, $met, $typ);

    }

    /**
     * 添加心得图片
     *
     * @access public
     */
    public function addExploreImages()
    {
        $images_url = request_string('images_url');
        $type = request_string('type');
        $poster_image = request_string('poster_image');

        $Explore_ImagesModel = new Explore_ImagesModel();
        $id = $Explore_ImagesModel->addExploreImages($images_url,$type,$poster_image);

        $data = array();
        if ($id) {
            $status = 200;
            $msg = __('success');
            $data['id'] = $id;
        } else {
            $msg = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);

    }


    /**
     * 添加心得图片商品
     *
     * @access public
     */
    public function addImagesGoods()
    {
        $images_id = request_int('images_id');
        $brand_id = request_int('brand_id');
        $goods_common_id = request_int('goods_common_id');

        $add_row = array();
        $add_row['images_id'] = $images_id;
        $add_row['brand_id'] = $brand_id;
        $add_row['goods_common_id'] = $goods_common_id;

        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();
        $id = $Explore_ImagesGoodsModel->addImagesGoods($add_row, true);

        $data = array();
        if ($id) {
            $status = 200;
            $msg = __('success');
            $data['id'] = $id;
        } else {
            $msg = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);

    }


    /**
     * 删除心得图片
     *
     * @access public
     */
    public function delExploreImages()
    {
        $images_id = request_int('images_id');

        $Explore_ImagesModel = new Explore_ImagesModel();

        //开启事务
        $Explore_ImagesModel->sql->startTransactionDb();

        $flag = $Explore_ImagesModel->delExploreImages($images_id);

        if ($flag && $Explore_ImagesModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_ImagesModel->sql->rollBackDb();
            $m = $Explore_ImagesModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);

    }


    /**
     * 修改心得图片商品
     *
     * @access public
     */
    public function editImagesGoods()
    {
        $id = request_int('id');
        $brand_id = request_int('brand_id');
        $goods_common_id = request_int('goods_common_id');

        $edit_row = array();
        $edit_row['brand_id'] = $brand_id;
        $edit_row['goods_common_id'] = $goods_common_id;

        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();
        $flag = $Explore_ImagesGoodsModel->editImagesGoods($id, $edit_row);

        $flag = is_ok($flag);

        if ($flag) {
            $status = 200;
            $msg = __('success');
        } else {
            $msg = __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);

    }

    /**
     * 删除心得图片商品
     *
     * @access public
     */
    public function delImagesGoods()
    {
        $image_goods_id = request_int('id');
        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();

        //开启事务
        $Explore_ImagesGoodsModel->sql->startTransactionDb();

        $flag = $Explore_ImagesGoodsModel->removeImagesGoods($image_goods_id);

        if ($flag && $Explore_ImagesGoodsModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_ImagesGoodsModel->sql->rollBackDb();
            $m = $Explore_ImagesGoodsModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 根据心得图片id获取对应商品信息
     *
     * @access public
     */
    public function getGoodsByImagesId()
    {
        $images_id = request_int('images_id');

        Yf_Log::log($images_id, Yf_Log::ERROR, 'qweqwe');

        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();
        $images_goods = $Explore_ImagesGoodsModel->getGoodsByImagesId($images_id);

        Yf_Log::log($images_goods, Yf_Log::ERROR, 'qweqwe');
        $msg = 'success';
        $status = 200;
        $this->data->addBody(-140, $images_goods, $msg, $status);
    }

    /**
     * 根据心得图片id获取对应商品信息
     *
     * @access public
     */
    public function getImagesId()
    {
        $images_id = request_int('images_id');

        Yf_Log::log($images_id, Yf_Log::ERROR, 'qweqwe');

        $Explore_ImagesModel = new Explore_ImagesModel();
        $images_goods = $Explore_ImagesModel->getImageId($images_id);

        Yf_Log::log($images_goods, Yf_Log::ERROR, 'qweqwe');
        $msg = 'success';
        $status = 200;
        $this->data->addBody(-140, $images_goods, $msg, $status);
    }

    /**
     * 修改心得图片商品
     *
     * @access public
     */
    public function editExploreImages()
    {
        $images_id = request_int('images_id');

        $images_url = request_string('images_url');

        $edit_row = array();
        $edit_row['images_url'] = $images_url;

        $Explore_ImagesModel = new Explore_ImagesModel();

        $flag = $Explore_ImagesModel->editImages($images_id, $edit_row);
        $flag = is_ok($flag);

        if ($flag) {
            $status = 200;
            $msg = __('success');
        } else {
            $msg = __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);
    }


    /**
     * 发布心得
     *
     * @access public
     */
    public function addExplore()
    {
        $explore_title = request_string('explore_title');
        $explore_content = request_string('explore_content');
        $images_id = request_row('images_id');
        $lables_id = request_row('lable_ids');
        $explore_status = request_int('explore_status',0);

        //判断发布心得内容或者标题是否存在违禁词
        $matche_row = array();
        //有违禁词
        if (Text_Filter::checkBanned($explore_title, $matche_row)) {
            $data = array();
            $msg = __('心得标题含有违禁词');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        $matche_row = array();
        //有违禁词
        if (Text_Filter::checkBanned($explore_content, $matche_row)) {
            $data = array();
            $msg = __('心得内容含有违禁词');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        $Explore_BaseModel = new  Explore_BaseModel();

        //开启事务
        $Explore_BaseModel->sql->startTransactionDb();

        $flag = $Explore_BaseModel->addExplore($explore_title, $explore_content, $images_id, $lables_id, $explore_status);

        if ($flag && $Explore_BaseModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_BaseModel->sql->rollBackDb();
            $m = $Explore_BaseModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);

    }

    /**
     * 修改心得
     *
     * @access public
     */
    public function editExplore()
    {
        $explore_id = request_int('explore_id');
        $explore_title = request_string('explore_title');
        $explore_content = request_string('explore_content');
        $images_id = request_row('images_id');
        $lables_id = request_row('lable_ids');
        $explore_status = request_row('explore_status');
        $edit_explore_status = request_string('edit_explore_status');
        $Explore_BaseModel = new  Explore_BaseModel();

        //开启事务
        $Explore_BaseModel->sql->startTransactionDb();

        $flag = $Explore_BaseModel->editExplore($explore_id, $explore_title, $explore_content, $images_id, $lables_id, $explore_status, $edit_explore_status);

        if ($flag !== false && $Explore_BaseModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_BaseModel->sql->rollBackDb();
            $m = $Explore_BaseModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);

    }

    /**
     * 删除心得
     *
     * @access public
     */
    public function delExplore()
    {
        $explore_id = request_int('explore_id');
        $Explore_BaseModel = new  Explore_BaseModel();
        $rs_row = array();
        //开启事务
        $Explore_BaseModel->sql->startTransactionDb();

        $flag = $Explore_BaseModel->editBase($explore_id, array('is_del' => 1));
        check_rs($flag, $rs_row);

        //如果删除草稿，则用户心得发表数量不修改
        $from = request_string('from');
        if($from != 'draft'){
            //修改用户文章发表次数
            $user_id = Perm::$userId;
            $Explore_UserModel = new Explore_UserModel();
            $res = $Explore_UserModel->getUser($user_id);
            $explore_user = $res[$user_id];
            $edit_row['explore_base_count'] = $explore_user['explore_base_count'] - 1;
            $user_flag = $Explore_UserModel->editUser($user_id, $edit_row);
            check_rs($user_flag, $rs_row);
        }

        $result = is_ok($rs_row);

        if ($result !== false && $Explore_BaseModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_BaseModel->sql->rollBackDb();
            $m = $Explore_BaseModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 根据心得id获取心得信息
     *
     * @access public
     */
    public function getExploreByExploreId()
    {
        $explore_id = request_int('explore_id');
        $Explore_BaseModel = new Explore_BaseModel();
        $data = $Explore_BaseModel->getExploreByExploreId($explore_id);
        if ($data) {
            $msg = 'success';
            $status = '200';
        } else {
            $msg = 'failure';
            $status = '250';
        }

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 获取推荐标签
     *
     * @access public
     */
    public function getHotLable()
    {
        $Explore_LableModel = new Explore_LableModel();

        $data = $Explore_LableModel->getHotLable();

        $this->data->addBody(-140, $data);
    }

    /**
     * 添加标签
     *
     * @access public
     */
    public function addExploreLable()
    {
        $lable_content = request_string('lable_content');

        $Explore_LableModel = new Explore_LableModel();

        //判断该标签是否已经添加过
        $lable = $Explore_LableModel->getByWhere(array('lable_content' => $lable_content));

        if ($lable) {
            $this->data->addBody(-140, array(), '该标签已添加！', 250);
            return;
        }

        $add_row = array();
        $add_row['lable_content'] = $lable_content;
        $add_row['user_id'] = Perm::$userId;
        $add_row['lable_create_time'] = time();

        $id = $Explore_LableModel->addLable($add_row, true);

        $flag = is_ok($id);

        $data = array();
        if ($flag) {
            $status = 200;
            $msg = __('success');
            $data['id'] = $id;
        } else {
            $msg = __('failure');
            $status = 250;
        }

        $this->data->addBody(-140, $data, $msg, $status);

    }

    /**
     * 搜索标签
     *
     * @access public
     */
    public function getExploreLable()
    {
        $lable_content = request_string('lable_content');

        $Explore_LableModel = new Explore_LableModel();

        $data = $Explore_LableModel->getExploreLable($lable_content);


        $this->data->addBody(-140, $data);

    }

    /*
     * 举报用户心得添加
     */
    public function addExploreReport()
    {
        $user_id = Perm::$userId;

        $explore_id = request_int('explore_id');
        $report_reason_id = request_int('report_reason_id'); //为4时，表示原因为其他 字段应存0
        $report_reason = request_string('report_reason','其他');

        $ExploreReportModel = new Explore_ReportModel();

        $resault = $ExploreReportModel->addExploreReport($explore_id,$report_reason_id,$report_reason);

        if ($resault) {
            $msg = __('success');
            $status = 200;
        } else {
            $msg = __('false');
            $status = 250;
        }

        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 编辑用户标签
     *
     * @access public
     */
    public function editUserInfo()
    {
        $user_id = Perm::$userId;
        $user_sign = request_string('user_sign');
        $Explore_UserModel = new Explore_UserModel();
        //开启事务
        $Explore_UserModel->sql->startTransactionDb();
        $flag = $Explore_UserModel->editEexploreUser($user_id, array('user_sign' => $user_sign));

        if ($flag !== false && $Explore_UserModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_UserModel->sql->rollBackDb();
            $m = $Explore_UserModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }
        $data = array();
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 用户关注与取消关注
     *
     * @access public
     */
    public function editUserFollow()
    {
        $to_user_id = request_int('user_id');

        $Explore_UserModel = new Explore_UserModel();

        //开启事务
        $Explore_UserModel->sql->startTransactionDb();

        $flag = $Explore_UserModel->editUserFollow($to_user_id);

        if ($flag && $Explore_UserModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_UserModel->sql->rollBackDb();
            $m = $Explore_UserModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 用户添加评论
     *
     * @access public
     */
    public function addExploreComment()
    {
        $explore_id = request_int('explore_id');
        $comment_content = request_string('comment_content');

        //判断评论内容是否存在违禁词
        $matche_row = array();
        //有违禁词
        if (Text_Filter::checkBanned($comment_content, $matche_row)) {
            $data = array();
            $msg = __('评论内容含有违禁词');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        if (!$comment_content) {
            $data = array();
            $msg = __('评论内容不可为空');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        $Explore_CommontModel = new Explore_CommentModel();

        //开启事务
        $Explore_CommontModel->sql->startTransactionDb();

        $flags = $Explore_CommontModel->addExploreComment($explore_id, $comment_content);

        if ($flags && $Explore_CommontModel->sql->commitDb()) {

            $Social_SocialModel = new Social_SocialModel();
            //浏览一次加多少积分
            $points_comment = Web_ConfigModel::value('points_comment');

            $points_comment_max = Web_ConfigModel::value('points_comment_max');
            //帖子ID
            $explore_id = request_int("explore_id");
            //用户ID
            $user_id = request_int("u");

            $user_account = request_string("user_account");

            $user = $Social_SocialModel->getOne($user_id);
            //print_r($user);DIE;
            $var = explode(",",$user['p_explore_id']);

            if(!$user && $user_id){
                //积分日志表增加流水明细
                $Points_LogModel = new Points_LogModel();
                $Points = array();
                $Points['points_log_type'] = 1;
                $Points['class_id'] = Points_LogModel::POINTS_COMMENT;
                $Points['user_id'] = $user_id;
                $Points['user_name'] = $user_account;
                $Points['points_log_points'] = $points_comment;
                $Points['points_log_time'] = date('Y-m-d H:i:s', time());
                $Points['points_log_desc'] = '评论帖子';
                $Points['points_log_flag'] = 'points_comment';
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
                $data =array();
                $flag = is_ok($rs_row);

                $row['user_id'] = $user_id;
                $row['time'] = date("Y-m-d");
                $row['p_explore_id'] = $explore_id;
                $row['points_comment_max'] = 1;

                // print_r($row);die;
                $Social_SocialModel->addBase($row);
            }else if(!in_array($explore_id,$var) && $user['points_comment_max'] < $points_comment_max ||  $user['time'] != date("Y-m-d")) {

                //退还用户使用积分
                //积分日志表增加流水明细
                $Points_LogModel = new Points_LogModel();
                $Points = array();
                $Points['points_log_type'] = 1;
                $Points['class_id'] = Points_LogModel::POINTS_COMMENT;
                $Points['user_id'] = $user_id;
                $Points['user_name'] = $user_account;
                $Points['points_log_points'] = $points_comment;
                $Points['points_log_time'] = date('Y-m-d H:i:s', time());
                $Points['points_log_desc'] = '评论帖子';
                $Points['points_log_flag'] = 'points_comment';
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
                    $row['time'] = date("Y-m-d");

                    $row['p_explore_id'] = $user['p_explore_id'] . "," . $explore_id;
                    $row['points_comment_max'] = $user['points_comment_max'] += 1;
                    if ($user['time'] != date("Y-m-d")) {
                        $row['p_explore_id'] = "";
                        $row['points_comment_max'] = "";
                    }
                    $Social_SocialModel->editBase($user_id, $row);
                    $status = 200;
                    $msg = __('success');

                }
            }

            $status = 200;
            $msg = __('success');
        } else {
            $Explore_CommontModel->sql->rollBackDb();
            $m = $Explore_CommontModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data['id'] = $flags;

        $this->data->addBody(-140, $data, $msg, $status);

    }

    /**
     * 用户添加收藏
     *
     * @access public
     */
    public function addCollertion(){
        $user_id = request_int('u');
        $where['user_id'] = $user_id;
        $explore_id = request_string('explore_id');


        $Explore_CollectionModel = new Explore_CollectionModel();
        $row = $Explore_CollectionModel->getByWhere($where);

        if($row){
            $data_exit = array();
            foreach ($row as $i){
                $data_exit = $i;
            }
            $rows['explore_id'] = $data_exit['explore_id'].",".$explore_id;
            $rows['time'] = date("Y-m-d H:i:s");
            $flag = $Explore_CollectionModel->editCollection($data_exit['id'],$rows);
        }else{
            $data['user_id'] = $user_id;
            $data['explore_id'] = $explore_id;
            $data['time'] = date("Y-m-d H:i:s");
            $flag = $Explore_CollectionModel->addCollection($data,true);
        }
        print_r($flag);die;
    }
    /**
     * 用户取消收藏
     *
     * @access public
     */
    public function exitCollertion(){
        $user_id = request_int('u');
        $where['user_id'] = $user_id;
        $explore_id = request_string('explore_id');

        $Explore_CollectionModel = new Explore_CollectionModel();

        $row = $Explore_CollectionModel->getByWhere($where);

        $data_exit = array();
        foreach ($row as $i){
            $data_exit = $i;
        }

        $data_exit['explore_id'] = explode(",", $data_exit['explore_id']);

        foreach ($data_exit['explore_id'] as $k=>$i){
            if($i == $explore_id){
                unset($i);
            }
            $data_exit['explore_id'][$k] = $i;
        }
        $data_exit['explore_id'] = implode(",", array_filter($data_exit['explore_id']));

        $rows['explore_id'] = $data_exit['explore_id'];

        $rows['time'] = date("Y-m-d H:i:s");

        $flag = $Explore_CollectionModel->editCollection($data_exit['id'],$rows);

        print_r($flag);die;


    }

    /**
     * 用户点赞评论及取消点赞
     *
     * @access public
     */
    public function editCommentLike()
    {
        $comment_id = request_int('comment_id');

        $Explore_CommentModel = new Explore_CommentModel();

        //开启事务
        $Explore_CommentModel->sql->startTransactionDb();

        $flag = $Explore_CommentModel->editCommentLike($comment_id);

        if ($flag && $Explore_CommentModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_CommentModel->sql->rollBackDb();
            $m = $Explore_CommentModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 用户点赞评论及取消点赞
     *
     * @access public
     */
    public function editReplyLike()
    {
        $reply_id = request_int('reply_id');

        $Explore_ReplyModel = new Explore_ReplyModel();

        //开启事务
        $Explore_ReplyModel->sql->startTransactionDb();

        $flag = $Explore_ReplyModel->editReplyLike($reply_id);

        if ($flag && $Explore_ReplyModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_ReplyModel->sql->rollBackDb();
            $m = $Explore_ReplyModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 用户回复
     *
     * @access public
     */
    public function addCommentReply()
    {
        $explore_id = request_int('explore_id');
        $comment_id = request_int('comment_id');
        $to_reply_id = request_int('to_reply_id');
        $reply_content = request_string('reply_content');

        //判断评论内容是否存在违禁词
        $matche_row = array();
        //有违禁词
        if (Text_Filter::checkBanned($reply_content, $matche_row)) {
            $data = array();
            $msg = __('回复内容含有违禁词');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }

        if (!$reply_content) {
            $data = array();
            $msg = __('回复内容不可为空');
            $status = 250;
            $this->data->addBody(-140, array(), $msg, $status);
            return false;
        }
        $Explore_ReplyModel = new Explore_ReplyModel();
        //开启事务
        $Explore_ReplyModel->sql->startTransactionDb();
        $flag = $Explore_ReplyModel->addCommentReply($explore_id, $comment_id, $to_reply_id, $reply_content);

        if ($flag && $Explore_ReplyModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_ReplyModel->sql->rollBackDb();
            $m = $Explore_ReplyModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);

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

        $data['explore_base'] = $Explore_BaseModel->getExploreList($type, $search_status, $search_content, $page);

        $user_id = Perm::$userId;
        $User_InfoModel = new  User_InfoModel();
        $user_info = $User_InfoModel->getUser($user_id);
        //用户头像
        $user_info['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id=' . $user_id;
        $data['user_info'] = $user_info;


        $Explore_MessageModel = new Explore_MessageModel();
        $data['message_sum'] = $Explore_MessageModel->getUnreadMeaasgeNum();
        $this->data->addBody(-140, $data);
    }


    /**
     * 用户点击点及取消赞
     *
     * @access public
     */
    public function editExploreLike()
    {
        $explore_id = request_int('explore_id');
        $Explore_BaseModel = new Explore_BaseModel();
        //开启事务
        //$Explore_BaseModel->sql->startTransactionDb();

        $flag = $Explore_BaseModel->editExploreLike($explore_id);
        //if ($flag['is_ok'] && $Explore_BaseModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        // } else {
        //     $Explore_BaseModel->sql->rollBackDb();
        //     $m = $Explore_BaseModel->msg->getMessages();
        //     $msg = $m ? $m[0] : __('failure');
        //     $status = 250;
        //}
        $data = $flag;
        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 搜索品牌
     *
     * @access public
     */
    public function chooseBrand()
    {
        $search_words = request_string('search_words');
        $Goods_BrandModel = new Goods_BrandModel();
        $page = 1;
        $rows = 20;
        $cond_row['brand_name:LIKE'] = '%' . $search_words . '%';
        $brand_list = $Goods_BrandModel->getBrandList($cond_row, array('brand_id' => 'ASC'), $page, $rows);
        $this->data->addBody(-140, $brand_list);
    }


    /**
     * 搜索商品（根据品牌id）
     *
     * @access public
     */
    public function chooseGoodsByBrand()
    {
        $brand_id = request_int('brand_id');
        $brand_name = request_string('brand_name');
        $common_name = request_string('search_words');
        if ($brand_id) {
            $cond_row['brand_id'] = $brand_id;
        }
        if ($common_name) {
            $cond_row['common_name:LIKE'] = '%' . $common_name . '%';
        }
        $Goods_CommonModel = new Goods_CommonModel();
        $cond_row['common_state'] = Goods_CommonModel::GOODS_STATE_NORMAL;
        $cond_row['common_verify'] = Goods_CommonModel::GOODS_VERIFY_ALLOW;
        $cond_row['is_del'] = Goods_BaseModel::IS_DEL_NO;

        $page = 1;
        $rows = 20;
        $goods_rows = $Goods_CommonModel->getGoodsByBrandId($cond_row, array('common_id' => 'ASC'), $page, $rows);
        if($brand_name){
            foreach($goods_rows['items'] as $k=>$v){
                $goods_rows['items'][$k]['brand_name'] = $brand_name;
            }
        }
        $this->data->addBody(-140, $goods_rows);
    }

    /**
     * 搜索订单中的商品
     *
     * @access public
     */
    public function getGoodsFromOrder()
    {
        $goods_name = request_string('search_words');
        $cond_row['user_id'] = Perm::$userId;
        if ($goods_name) {
            $cond_row['goods_name'] = $goods_name;
        }
        $Order_GoodsModel = new Order_GoodsModel();
        $order_goods_list = $Order_GoodsModel->getGoodsFromOrder($cond_row);
        $this->data->addBody(-140, $order_goods_list);
    }

    //删除我的评论
    public function delExploreComment()
    {
        $comment_id = request_int('comment_id');

        $Explore_CommontModel = new Explore_CommentModel();

        //开启事务
        $Explore_CommontModel->sql->startTransactionDb();

        $flag = $Explore_CommontModel->delExploreComment($comment_id);

        if ($flag && $Explore_CommontModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_CommontModel->sql->rollBackDb();
            $m = $Explore_CommontModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);
    }

    //删除我的回复
    public function delCommentReply()
    {
        $reply_id = request_int('reply_id');

        $Explore_ReplyModel = new Explore_ReplyModel();

        //开启事务
        $Explore_ReplyModel->sql->startTransactionDb();

        $flag = $Explore_ReplyModel->delExploreComment($reply_id);

        if ($flag && $Explore_ReplyModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_ReplyModel->sql->rollBackDb();
            $m = $Explore_ReplyModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 用户点赞、取消心得
     *
     * @access public
     */
    public function supportExplore()
    {
        $user_id = request_string('user_id');
        $explore_id = request_int('explore_id');
        $addOrReduce = request_int('addOrReduce');//增加或减少

        $Explore_BaseModel = new Explore_BaseModel();
        $explore_base = $Explore_BaseModel->getBase($explore_id);
        $explore = $explore_base[$explore_id];

        $cond_row = array();
        if ($addOrReduce == 1) {
            $cond_row['explore_like_count'] = $explore['explore_like_count'] - 1;
            $explore_like_user = explode(',', $explore['explore_like_user']);
            $result = array_diff($explore_like_user, [$user_id]);
            $like_user = implode(',', $result);
        } else {
            $cond_row['explore_like_count'] = $explore['explore_like_count'] + 1;
            $explore_like_user = $explore['explore_like_user'] . ',' . $user_id;
            $like_user = trim($explore_like_user, ",");
        }

        $cond_row['explore_like_user'] = $like_user;

        //开启事务
        $Explore_BaseModel->sql->startTransactionDb();

        $flag = $Explore_BaseModel->editBase($explore_id, $cond_row);

        if ($flag && $Explore_BaseModel->sql->commitDb()) {
            $status = 200;
            $msg = __('success');
        } else {
            $Explore_BaseModel->sql->rollBackDb();
            $m = $Explore_BaseModel->msg->getMessages();
            $msg = $m ? $m[0] : __('failure');
            $status = 250;
        }

        $data = array();

        $this->data->addBody(-140, $data, $msg, $status);
    }

    /**
     * 获取用户所有未读信息数
     *
     * @access public
     */
    public function getUnreadMeaasgeNum()
    {
        $Explore_MessageModel = new Explore_MessageModel();

        $data = $Explore_MessageModel->getUnreadMeaasgeNum();

        $this->data->addBody(-140, $data);
    }

    /**
     * 获取用户所有未读信息数
     *
     * @access public
     */
    public function exploreFindFriends()
    {
        $user_name = request_string('user_name');
        $Explore_UserModel = new Explore_UserModel();
        $data = $Explore_UserModel->exploreFindFriends($cond_row = [], $order_row = [], $page = 1, $rows = 20, $user_name);
        $this->data->addBody(-140, $data);
    }

    /**
     * 获取用户所有点赞信息
     *
     * @access public
     */
    public function getLikeMessage()
    {
        $rows = 10;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $Explore_MessageModel = new Explore_MessageModel();

        $data = $Explore_MessageModel->getLikeMessage($page,$rows);

        $this->data->addBody(-140, $data);
    }

    /**
     * 获取用户所有点赞信息
     *
     * @access public
     */
    public function getCommentMessage()
    {
        $rows = 10;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $Explore_MessageModel = new Explore_MessageModel();

        $data = $Explore_MessageModel->getCommentMessage($page,$rows);

        $this->data->addBody(-140, $data);
    }

    /**
     * 获取用户所有通知信息
     *
     * @access public
     */
    public function getReportMessage()
    {
        $rows = 10;
        $offset = request_int('firstRow', 0);
        $page = ceil_r($offset / $rows);

        $Explore_MessageModel = new Explore_MessageModel();

        $data = $Explore_MessageModel->getReportMessage($page,$rows);

        $this->data->addBody(-140, $data);
    }

    public function getReportInfo()
    {
        $report_id = request_int('report_id');

        $Explore_ReportModel = new Explore_ReportModel();

        $data = $Explore_ReportModel->getReportInfo($report_id);

        $this->data->addBody(-140, $data);

    }

    /**
     *我的粉丝一览列表
     *
     * @access public
     */
    public function getFansList()
    {
        $user_id = request_int('u');
        $Explore_UserModel = new  Explore_UserModel();
        $data = $Explore_UserModel->getUserFansList($user_id);

        $this->data->addBody(-140, $data);
    }

    /**
     *我的关注一览列表
     *
     * @access public
     */
    public function getFollowList()
    {
        $user_id = request_int('user_id');
        $Explore_UserModel = new  Explore_UserModel();
        $data = $Explore_UserModel->getUserFollowList($user_id);
        $this->data->addBody(-140, $data);
    }

    /**
     *获取举报原因
     *
     * @access public
     */
    public function getReportReason()
    {
        $Explore_ReportReasonModel = new  Explore_ReportReasonModel();
        $data = $Explore_ReportReasonModel->getReportReasonAll();
        $this->data->addBody(-140, $data);
    }

    public function addjifeng(){

        $Social_SocialModel = new Social_SocialModel();
        //点赞一次加多少积分
        $points_praised = Web_ConfigModel::value('points_praised');

        $points_praised_max = Web_ConfigModel::value('points_praised_max');

        //用户ID
        $user_id = request_int("u");
        $user_account = request_string("user_account");
        $explore_id = request_string("explore_id");

        $user = $Social_SocialModel->getOne($user_id);

        $var = explode(",",$user['share_explore_id']);

        if(!$user && $user_id){
            //积分日志表增加流水明细
            $Points_LogModel = new Points_LogModel();
            $Points = array();
            $Points['points_log_type'] = 1;
            $Points['class_id'] = Points_LogModel::POINTS_COMMENT;
            $Points['user_id'] = $user_id;
            $Points['user_name'] = $user_account;
            $Points['points_log_points'] = $points_praised;
            $Points['points_log_time'] = date('Y-m-d H:i:s', time());
            $Points['points_log_desc'] = '分享心得';
            $Points['points_log_flag'] = 'points_comment';
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
            $data =array();
            $flag = is_ok($rs_row);

            $row['user_id'] = $user_id;
            $row['time'] = date("Y-m-d");
            $row['share_explore_id'] = $explore_id;
            $row['points_share_max'] = 1;
            $Social_SocialModel->addBase($row);
        }else if(!in_array($explore_id,$var) && $user['points_share_max'] < $points_praised_max ||  $user['time'] != date("Y-m-d")) {
            //退还用户使用积分
            //积分日志表增加流水明细
            $Points_LogModel = new Points_LogModel();
            $Points = array();
            $Points['points_log_type'] = 1;
            $Points['class_id'] = Points_LogModel::POINTS_COMMENT;
            $Points['user_id'] = $user_id;
            $Points['user_name'] = $user_account;
            $Points['points_log_points'] = $points_praised;
            $Points['points_log_time'] = date('Y-m-d H:i:s', time());
            $Points['points_log_desc'] = '分享心得';
            $Points['points_log_flag'] = 'points_comment';
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
            $flag2 = is_ok($rs_row);
            if ($flag2) {
                $row['time'] = date("Y-m-d");

                $row['share_explore_id'] = $user['share_explore_id'] . "," . $explore_id;
                $row['points_share_max'] = $user['points_share_max'] += 1;
                if ($user['time'] != date("Y-m-d")) {
                    $row['share_explore_id'] = "";
                    $row['points_share_max'] = "";
                }
                $Social_SocialModel->editBase($user_id, $row);
            }
        }
    }

    public function exinfo(){

        $Explore_BaseModel = new  Explore_BaseModel();

        $row = $Explore_BaseModel->getBaseAllList();

        echo "<pre>";
        print_r($row);die;
    }

}

?>
