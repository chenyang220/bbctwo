<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_BaseModel extends Explore_Base
{
    const IS_DEL = 0;//未删除
    const NO_DEL = 1;//删除
    const NormalStatus = 0;//正常
    const UnnormalStatus = 1;//下架
    const DraftStatus = 2;//草稿

    /**
     * 读取分页列表
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function getBaseAllList($cond_row = [], $order_row = [], $page = 1, $rows = 100)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);

        return $data;
    }

    //发布心得
    public function addExplore($title = '',$content = '',$images_id = array(),$lables_id = array(),$explore_status)
    {
        $rs_row = array();
        $user_id = Perm::$userId;
        //1.先将心得标题，内容，标签存入表中
        $add_row = array();
        $add_row['user_id'] = $user_id;
        $add_row['user_account'] = Perm::$row['user_account'];
        $add_row['explore_title'] = $title;
        $add_row['explore_content'] = $content;
        $add_row['explore_create_time'] = time();
        $add_row['explore_lable'] = implode(',', $lables_id);
        $explorereview = Web_ConfigModel::value('explorereview');
        if ($explorereview == 1) {
            $add_row['explore_status'] = $explore_status == 0 ? 3 : $explore_status;//平台开启审核，发布状态改为待审核
        } else {
            $add_row['explore_status'] = $explore_status;
        }

        $explore_id = $this->addBase($add_row,true);
        check_rs($explore_id,$rs_row);

        //图片不为空
        if (!empty($images_id)) {
            //2.将返回的$explore_id添加带心得图片表中
            $Explore_ImagesModel = new Explore_ImagesModel();
            $edit_row = array();
            $edit_row['explore_id'] = $explore_id;
            $flag = $Explore_ImagesModel->editImages($images_id, $edit_row);
            check_rs($flag, $rs_row);
        }

        //标签不为空
        if(!empty($lables_id)) {
            //3.修改标签使用次数
            $Explore_LableModel = new Explore_LableModel();
            $edit_row = array();
            $edit_row['lable_used_count'] = 1;
            $edit_row['lable_month_count'] = 1;
            $flag = $Explore_LableModel->editLable($lables_id,$edit_row,true);
            check_rs($flag,$rs_row);
        }

        //修改用户文章发表次数
        $Explore_UserModel = new Explore_UserModel();
        $explore_user = $Explore_UserModel->getUser($user_id);
        if($explore_user){
            $user_flag = $Explore_UserModel->editUser($user_id,array('explore_base_count'=>1),true);
        }else{
            //如果yf_explore_user没有当前登录用户信息，则添加当前用户信息
            $add_user_row = [];
            $add_user_row['user_id'] = $user_id;
            $add_user_row['user_account'] = Perm::$row['user_account'];
            $add_user_row['explore_base_count'] = 1;
            $user_flag = $Explore_UserModel->addUser($add_user_row);
        }
        check_rs($user_flag, $rs_row);

        $result = is_ok($rs_row);

        return $result;

    }

    /**
     * 根据主键更新表内容
     * @param mix $explore_id 主键
     * @param array $field_row key=>value数组
     * @return bool $update_flag 是否成功
     * @access public
     */
    public function editExploreKey($explore_id = null, $field_row, $flag = false)
    {
        $update_flag = $this->edit($explore_id, $field_row,$flag);
        return $update_flag;
    }

    //修改心得
    public function editExplore($id = 0,$title = '',$content = '',$images_id = array(),$lables_id = array(), $explore_status = 0, $edit_explore_status = 0)
    {
        $rs_row = array();

        //获取心得中得原信息
        $explore_base = $this->getOne($id);

        //1.修改心得内容
        $edit_row1 = array();
        $edit_row1['explore_title'] = $title;
        $edit_row1['explore_content'] = $content;
        $edit_row1['explore_lable'] = implode(',', $lables_id);
        $edit_row1['explore_status'] = $explore_status;
        if($edit_explore_status == 2){
            $edit_row1['explore_create_time'] = time();
        }
        $edit_flag = $this->editBase($id,$edit_row1);
        check_rs($edit_flag,$rs_row);

        //2.判断之前的心得标签和现在的心得标签。修改心得标签的使用次数
        $old_lable = $explore_base['explore_lable'];
        $diff_lable = array_diff($lables_id,$old_lable);
        //如果有新增心得标签
        if($diff_lable){
            $Explore_LableModel = new Explore_LableModel();
            $edit_row2 = array();
            $edit_row2['lable_used_count'] = 1;
            $edit_row2['lable_month_count'] = 1;
            $flag = $Explore_LableModel->editLable($diff_lable, $edit_row2, true);
            check_rs($flag, $rs_row);
        }

        //3.修改心得图片绑定的心得id
        $Explore_ImagesModel = new Explore_ImagesModel();
        if($images_id){
            $edit_row3 = array();
            $edit_row3['explore_id'] = $id;
            $flag = $Explore_ImagesModel->editImages($images_id,$edit_row3);
            check_rs($flag,$rs_row);
        }
        $result = is_ok($rs_row);
        return $result;
    }

    //获取心得详情（包含当前用户是否点赞，用户是否关注，心得评论）
    public function getExploreDetail($id = NULL)
    {
        $user_id = Perm::$userId;

        $data = array();
        //1.心得详情
        $explore = $this->getBase($id);
        $data['explore_base'] = current($explore);
        $data['explore_base']['explore_create_date'] = date("Y-m-d H:i",$data['explore_base']['explore_create_time']);
        //判断当前用户是否点赞了该心得
        $explore_like_user = explode(',',$data['explore_base']['explore_like_user']);
        $data['explore_base']['is_like'] = 0;
        if($user_id && in_array($user_id,$explore_like_user)) {
            $data['explore_base']['is_like'] = 1;
        }

        //2.心得图片
        $Explore_ImagesModel = new Explore_ImagesModel();
        $explore_images = $Explore_ImagesModel->getByWhere(array('explore_id'=>$id));
        $data['explore_images'] = array_values($explore_images);

        //3.心得发布者的用户信息(及是否关注过)
        $user_info['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$data['explore_base']['user_id'];
        $user_info['user_account'] = $data['explore_base']['user_account'];
        $user_info['is_follow'] = 0;
        $user_info['is_author'] = 0;

        $Explore_UserModel = new Explore_UserModel();
        if ($user_id) {
            $explore_user = $Explore_UserModel->getOne($user_id);
        } else {
            $explore_user = array();
        }
        if($explore_user)
        {
            $user_follow_id = explode(',',$explore_user['user_follow_id']);
            if(in_array($data['explore_base']['user_id'],$user_follow_id)) {
                $user_info['is_follow'] = 1;
            }
        }
        $data['num'] = mb_strlen($data['explore_base']['explore_content'],'utf8');



        if($data['num']  > 60){
            $data['explore_content'] = substr($data['explore_base']['explore_content'],0,80);
        }else{
            $data['explore_content'] = $data['explore_base']['explore_content'];
        }

        //判断当前用户是否是心得发布者
        if($user_id && $user_id == $data['explore_base']['user_id'])
        {
            $user_info['is_author'] = 1;
        }
        $data['user_info'] = $user_info;

        //4.心得标签
        $lable_id = explode(',',$data['explore_base']['explore_lable']);
        $Explore_LableModel = new Explore_LableModel();
        $explore_lable = $Explore_LableModel->getLableInfo($lable_id);
        if($explore_lable){
            $data['explore_lable'] = $explore_lable;
        }else{
            $data['explore_lable'] = array();
        }
        $data['explore_lable_name'] = $explore_lable[0]['lable_content'];

        //5.心得商品
        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();
        $goods = $Explore_ImagesGoodsModel->getGoodsSimple($id);
        $data['goods'] = $goods;

        //6.心得评论
        $Explore_CommentModel = new Explore_CommentModel();
        $comment = $Explore_CommentModel->getCommentSimple($id);
        $data['comment'] = $comment;

        //7.心得收藏
        $Explore_CollectionModel = new Explore_CollectionModel();
        $where['user_id'] = $user_id;
        $collection = $Explore_CollectionModel->getByWhere($where);


        foreach ($collection as $i){
            $collection = $i;
        }
        $collection['explore_id'] = explode(",", $collection['explore_id']);

        $collection['start'] = in_array($id,$collection['explore_id']);

        $data['collection'] = $collection;


        //7.判断当前用户是否正在举报该心得
        $data['explore_base']['is_reporting'] = 0;
        $Explore_ReportModel = new Explore_ReportModel();
        $flag = $Explore_ReportModel->getUserExplore($user_id,$id);
        if($flag) {
            $data['explore_base']['is_reporting'] = 1;
        }

        return $data;

    }

    //心得列表 $type 1、关注 2、发现
    //$search_status 1、标签 2、标题
    //$search_content 搜索框内容 string
    public function getExploreList($type,  $search_status, $search_content, $page = 1, $row = 6)
    {
        $user_id = Perm::$userId;
        if ($type == 1) {
            $Explore_UserModel = new  Explore_UserModel();
            $data = $Explore_UserModel ->getOne($user_id);
            $user_follow_ids = array_filter(array_reverse(explode(',', $data['user_follow_id'])));
            $array_follow = implode(',',$user_follow_ids);
//            if ($search_status == 1) {
//                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE user_id IN (". $array_follow .") AND explore_lable LIKE '%" . ltrim($search_content,'#') ."%' ORDER BY sort DESC";
//            } elseif ($search_status == 2) {
//                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE user_id IN (". $array_follow .") AND explore_title LIKE '%" . $search_content ."%'  ORDER BY sort DESC";
//            } else {
//                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE user_id IN  (".$array_follow.')  ORDER BY sort DESC';
//            }
            if ($search_status == 1) {
                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE user_id IN (". $array_follow .") AND explore_lable LIKE '%" . ltrim($search_content,'#') ."%' ORDER BY explore_create_time DESC";
            } elseif ($search_status == 2) {
                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE user_id IN (". $array_follow .") AND explore_title LIKE '%" . $search_content ."%'  ORDER BY explore_create_time DESC";
            } else {
                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE user_id IN  (".$array_follow.')  ORDER BY explore_create_time DESC';
            }
            $sql .= " LIMIT " . ($page - 1) * $row . "," . $row;
            $data = $this -> sql -> getAll($sql);
        } else {
//            if ($search_status == 1) {
//                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE 1 AND explore_status=0 AND is_del=0 AND explore_lable LIKE '%" . ltrim($search_content,'#')."%'  ORDER BY sort DESC";
//            } elseif ($search_status == 2) {
//                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE 1 AND explore_status=0 AND is_del=0 AND explore_title LIKE '%". $search_content . "%'  ORDER BY sort DESC";
//            } else {
//                $sql = 'SELECT * FROM '. TABEL_PREFIX .'explore_base WHERE 1 AND explore_status=0 AND is_del=0  ORDER BY sort DESC';
//            }
            if ($search_status == 1) {
                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE 1 AND explore_status=0 AND is_del=0 AND explore_lable LIKE '%" . ltrim($search_content,'#')."%'  ORDER BY explore_create_time DESC";
            } elseif ($search_status == 2) {
                $sql = 'SELECT * FROM '. TABEL_PREFIX ."explore_base WHERE 1 AND explore_status=0 AND is_del=0 AND explore_title LIKE '%". $search_content . "%'  ORDER BY explore_create_time DESC";
            } else {
                $sql = 'SELECT * FROM '. TABEL_PREFIX .'explore_base WHERE 1 AND explore_status=0 AND is_del=0  ORDER BY explore_create_time DESC';
            }
            $sql .= " LIMIT " . ($page - 1) * $row . "," . $row;
            $data = $this -> sql -> getAll($sql);
        }
        foreach ($data as $key => $val)
        {
            //心得图片
            $img_sql = 'SELECT * FROM '. TABEL_PREFIX .'explore_images WHERE 1 AND explore_id='.$val['explore_id'].' LIMIT 0,1';
            $img = $this -> sql -> getAll($img_sql);
            $data[$key]['img_url'] = $img[0]['images_url'];
            $data[$key]['poster_image'] = $img[0]['poster_image'];
            $data[$key]['type'] = $img[0]['type'];
            $data[$key]['type_s'] = preg_replace( '/[\W]/', '', $img[0]['type']); ;

            //用户头像
            $data[$key]['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$val['user_id'];

            $data[$key]['is_like'] = 0;
            //判断用户是否点赞
            $like_user_row = explode(',',$val['explore_like_user']);
            if($user_id && in_array($user_id,$like_user_row)) {
                $data[$key]['is_like'] = 1;
            }
        }
        return $data;
    }



    //全部心得列表 $type 1、关注 2、发现
    //$search_status 1、标签 2、标题
    //$search_content 搜索框内容 string
    public function getExploreListAll($cond_row = [], $order_row = array('explore_create_time' => 'DESC'), $page = 1, $rows = 100)
    {
        $data = $this->listByWhere($cond_row, $order_row, $page, $rows);

        foreach ($data['items'] as $key => $val) {
            $User_InfoModel = new User_InfoModel();
            $user_info = $User_InfoModel->getOne($val['user_id']);
            $data['items'][$key]['user_mobile'] = $user_info['user_mobile'];
        }

        return $data;
    }

    //心得列表点赞及取消赞
    public function editExploreLike($explore_id=null)
    {
        $rs_row = array();
        $explore = $this->getOne($explore_id);

        //查找心得发布者信息
        $Explore_UserModel = new Explore_UserModel();
        $user = $Explore_UserModel->getOne($explore['user_id']);
        if(!$user) {
            //没有用户信息，增加用户信息
            $add_row = array();
            $add_row['user_id'] = $explore['user_id'];
            $add_row['user_account'] =$explore['user_id'];

            $Explore_UserModel->addUser($add_row);
        }
        //判断当前用户是否点赞过该心得
        $explore_like_user = explode(',',$explore['explore_like_user']);
        if(in_array(Perm::$userId,$explore_like_user)) {
            //已经点赞过，取消点赞
            $diff_user_id = array_diff($explore_like_user,[Perm::$userId]);

            $edit_row = array();
            $edit_row['explore_like_user'] = implode(',',$diff_user_id);
            $edit_row['explore_like_count'] = $explore['explore_like_count']-1;
            $explore_like_count = $explore['explore_like_count']-1;
            $edit_flag = $this->editExploreKey($explore_id,$edit_row);
            check_rs($edit_flag,$rs_row);

            //修改用户点赞数量
            $edit_row = array();
            $edit_row['user_like'] = -1;
            $edit_flag = $Explore_UserModel->editUser($explore['user_id'],$edit_row,true);
            check_rs($edit_flag,$rs_row);

        } else {
            $Social_SocialModel = new Social_SocialModel();
            //点赞一次加多少积分
            //$points_fabulous = Web_ConfigModel::value('points_fabulous');
            $points_fabulous = 1;
            //$points_fabulous_max = Web_ConfigModel::value('points_fabulous_max');
            $points_fabulous_max = 10;
            //帖子ID
          //  $explore_id = $explore_id;
            //用户ID
            $user_id = request_int("u");
            $user_account = request_string("n");
            $user = $Social_SocialModel->getOne($user_id);

            $var = explode(",",$user['likes_explore_id']);
            if(!$user && $user_id){
                //积分日志表增加流水明细
                $Points_LogModel = new Points_LogModel();
                $Points = array();
                $Points['points_log_type'] = 1;
                $Points['class_id'] = Points_LogModel::POINTS_COMMENT;
                $Points['user_id'] = $user_id;
                $Points['user_name'] = $user_account;
                $Points['points_log_points'] = $points_fabulous;
                $Points['points_log_time'] = date('Y-m-d H:i:s', time());
                $Points['points_log_desc'] = '心得点赞';
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
                $row['likes_explore_id'] = $explore_id;
                $row['points_likes_max'] = 1;
                // print_r($row);die;
                $Social_SocialModel->addBase($row);

            }else if(!in_array($explore_id,$var) && $user['points_likes_max'] < $points_fabulous_max ||  $user['time'] != date("Y-m-d")) {
                //退还用户使用积分
                //积分日志表增加流水明细
                $Points_LogModel = new Points_LogModel();
                $Points = array();
                $Points['points_log_type'] = 1;
                $Points['class_id'] = Points_LogModel::POINTS_COMMENT;
                $Points['user_id'] = $user_id;
                $Points['user_name'] = $user_account;
                $Points['points_log_points'] = $points_fabulous;
                $Points['points_log_time'] = date('Y-m-d H:i:s', time());
                $Points['points_log_desc'] = '心得点赞';
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

                    $row['likes_explore_id'] = $user['likes_explore_id'] . "," . $explore_id;
                    $row['points_likes_max'] = $user['points_likes_max'] += 1;
                    if ($user['time'] != date("Y-m-d")) {
                        $row['likes_explore_id'] = "";
                        $row['points_likes_max'] = "";
                    }
                    $Social_SocialModel->editBase($user_id, $row);
                }
            }


            $this->beidianzhan($explore_id,$explore['user_id'],$explore['user_account']);

            //没有点赞过，添加点赞
            array_push($explore_like_user, Perm::$userId);

            $edit_row = array();
            $edit_row['explore_like_user'] = implode(',',$explore_like_user);
            $edit_row['explore_like_count'] = $explore['explore_like_count']+1;
            $explore_like_count = $explore['explore_like_count']+1;
            $edit_flag = $this->editExploreKey($explore_id,$edit_row);
            check_rs($edit_flag,$rs_row);
            //修改用户点赞数量
            $edit_row = array();
            $edit_row['user_like'] = 1;
            $edit_flag = $Explore_UserModel->editUser($explore['user_id'],$edit_row,true);
            check_rs($edit_flag,$rs_row);

            //发送心得点赞信息(自己给自己的心得点赞不需要发送通知)
            if($explore['user_id'] !== Perm::$userId)
            {
                $Explore_MessageModel = new Explore_MessageModel();
                $add_row = array();
                $add_row['message_user_id'] = $explore['user_id'];
                $add_row['message_user_name'] = $explore['user_account'];
                $add_row['message_type'] = Explore_MessageModel::LIKE;
                $add_row['active_user_id'] = Perm::$userId;
                $add_row['active_id'] = $explore_id;
                $add_row['message_create_time'] = time();
                $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
                check_rs($add_flag,$rs_row);
            }

        }
        $flag2= array();
        $flag2['is_ok'] = is_ok($rs_row);
        $flag2['explore_like_count'] = $explore_like_count;
        //file_put_contents(dirname(__FILE__).'/abs.php', print_r($flag2,true));
        return $flag2;

    }

    //根据心得id获取心得信息
    public function getExploreByExploreId($explore_id)
    {
        $explore_base = current($this->get($explore_id));

        $Explore_ImagesModel = new Explore_ImagesModel();
        $images = $Explore_ImagesModel->getByWhere(array('explore_id'=> $explore_id));
        $images_id = array_column($images,'images_id');

        $Explore_LableModel = new Explore_LableModel();
        $lable_ids = explode(',', $explore_base['explore_lable']);
        $lables = $Explore_LableModel->getByWhere(array('lable_id:IN' => $lable_ids));

        $explore_base['images'] = array_values($images);
        $explore_base['lables'] = array_values($lables);
        return $explore_base;
    }

    //用户发布文章数
    public function explore_count($user_id, $is_self)
    {
        //判断当前被访问用户是否为当前登录用户
        if($is_self == 1){
            $sql1 = 'SELECT count(*) count FROM ' . TABEL_PREFIX . 'explore_base WHERE 1 AND explore_status IN (0,1) AND is_del = 0 AND user_id = ' . $user_id;
        }else{
            $sql1 = 'SELECT count(*) count FROM ' . TABEL_PREFIX . 'explore_base WHERE 1 AND explore_status = 0 AND is_del = 0 AND user_id = ' . $user_id;
        }
        $count1 = current($this->sql->getAll($sql1));

        $sql2 = 'SELECT count(*) draft_count FROM ' . TABEL_PREFIX . 'explore_base WHERE 1 AND explore_status >= 2 AND is_del = 0 AND user_id = ' . $user_id;
        $count2 = current($this->sql->getAll($sql2));

        $sql3 = 'SELECT *  FROM ' . TABEL_PREFIX . 'explore_collection WHERE  user_id = ' . $user_id;
        $count3 = current($this->sql->getAll($sql3));



        $explore_id_x = array_filter(explode(",", $count3['explore_id']));

        $contshouchang = 0;

        foreach ($explore_id_x as $a){
                $contshouchang++;
        }

        //Yf_Log::log($contshouchang, Yf_Log::ERROR, 'tiaoshu');

        $data['count'] = $count1['count'];//文章数
        $data['draft_count'] = $count2['draft_count'];//草稿数
        $data['collection_count'] = $contshouchang;//收藏数
        return $data;
    }

    //判断当前用户是否对心得点赞
    public function isSupport($user_id,$explore_id)
    {
        $explore_base = $this->get($explore_id);
        $data = $explore_base[$explore_id];
        $explore_like_user = explode(',',$data['explore_like_user']);
        if(in_array($user_id, $explore_like_user)) {
            $falg = true;
        }else {
            $falg = false;
        }
        return $falg;
    }

    //获取心得信息（不包括当前用户是否点赞，关注信息。不包括评论）
    public function getExploreInfo($id = NULL)
    {
        $data = array();
        //1.心得详情
        $explore = $this->getBase($id);
        $data['explore_base'] = current($explore);
        $data['explore_base']['explore_create_date'] = date("Y-m-d H:i",$data['explore_base']['explore_create_time']);

        //2.心得图片
        $Explore_ImagesModel = new Explore_ImagesModel();
        $explore_images = $Explore_ImagesModel->getByWhere(array('explore_id'=>$id));
        $data['explore_images'] = array_values($explore_images);

        //3.心得发布者的用户信息
        $user_info['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$data['explore_base']['user_id'];
        $user_info['user_account'] = $data['explore_base']['user_account'];
        $data['user_info'] = $user_info;

        //4.心得标签
        $lable_id = explode(',',$data['explore_base']['explore_lable']);
        $Explore_LableModel = new Explore_LableModel();
        $explore_lable = $Explore_LableModel->getLableInfo($lable_id);
        $data['explore_lable'] = $explore_lable;

        //5.心得商品
        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();
        $goods = $Explore_ImagesGoodsModel->getGoodsSimple($id);
        $data['goods'] = $goods;

        return $data;

    }
    
    /**
     * 根绝explode_id获取对应商品
     *
     * @access public
     */
    public function getGoodsByExploreId()
    {
        $explore_id = request_int('explore_id');
        $Explore_ImagesModel = new Explore_ImagesModel();
        $image_base = $Explore_ImagesModel->getByWhere(array('explore_id' => $explore_id));
        $images_id = array_column($image_base, 'images_id');
        $Explore_ImagesGoodsModel = new Explore_ImagesGoodsModel();
        $image_goods_base = $Explore_ImagesGoodsModel->getGoodsByImagesId($images_id);
        return $image_goods_base;
    }

    public function beidianzhan($explore_id,$user_id,$user_account){

        $Social_SocialModel = new Social_SocialModel();
        //点赞一次加多少积分
        $points_praised = Web_ConfigModel::value('points_praised');

        $points_praised_max = Web_ConfigModel::value('points_praised_max');

        //用户ID
        //$user_id = request_int("u");
        //$user_account = request_string("n");

        $user = $Social_SocialModel->getOne($user_id);

        $var = explode(",",$user['cover_explore_id']);
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
            $Points['points_log_desc'] = '心得被点赞';
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
            $row['cover_explore_id'] = $explore_id;
            $row['points_cover_max'] = 1;


            $Social_SocialModel->addBase($row);
        }else if(!in_array($explore_id,$var) && $user['points_cover_max'] < $points_praised_max ||  $user['time'] != date("Y-m-d")) {

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
            $Points['points_log_desc'] = '心得被点赞';
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

                $row['cover_explore_id'] = $user['cover_explore_id'] . "," . $explore_id;
                $row['points_cover_max'] = $user['points_cover_max'] += 1;
                if ($user['time'] != date("Y-m-d")) {
                    $row['cover_explore_id'] = "";
                    $row['points_cover_max'] = "";
                }
                $Social_SocialModel->editBase($user_id, $row);
            }
        }
    }

}

?>