<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_UserModel extends Explore_User
{

    //用户关注与取消关注
    public function editUserFollow($to_user_id = null)
    {

        //判断当前用户是否是要关注的用户
        if($to_user_id == Perm::$userId) {
            return false;
        }

        $rs_row = array();
        $explore_user = $this->getOne(Perm::$userId);

        $to_explore_user = $this->getOne($to_user_id);
        if(empty($explore_user)) {
            //用户信息不存在，则添加用户信息
            $add_row = array();
            $add_row['user_id'] = Perm::$userId;
            $add_row['user_account'] = Perm::$row['user_account'];
            $add_row['user_follow_count']= 1;
            $add_row['user_follow_id'] = $to_user_id;

            $flag = $this->addUser($add_row);
            check_rs($flag,$rs_row);
        } else {
            //判断当前用户是否关注过该用户
            $user_follow_id_row = explode(',',$explore_user['user_follow_id']);
            if(in_array($to_user_id,$user_follow_id_row)) {
                //关注过用户，则取消关注
                $diff_user_id = array_diff($user_follow_id_row,[$to_user_id]);

                $edit_row = array();
                $edit_row['user_follow_count'] = -1;

                $edit_flag = $this->editUser(Perm::$userId,$edit_row,true);
                check_rs($edit_flag,$rs_row);

                $edit_row = array();
                $edit_row['user_follow_id'] = implode(',',$diff_user_id);
                $edit_flag = $this->editUser(Perm::$userId,$edit_row);
                check_rs($edit_flag,$rs_row);

            } else {
                //没有关注过用户，则增加关注信息
                array_push($user_follow_id_row, $to_user_id);

                $edit_row = array();
                $edit_row['user_follow_count'] = 1;

                $edit_flag = $this->editUser(Perm::$userId,$edit_row,true);
                check_rs($edit_flag,$rs_row);

                $edit_row = array();
                $edit_row['user_follow_id'] = implode(',',$user_follow_id_row);
                $edit_flag = $this->editUser(Perm::$userId,$edit_row);
                check_rs($edit_flag,$rs_row);

            }
        }

        //查找用户信息
        $User_BaseModel = new User_BaseModel();

        $user_info = $User_BaseModel->getOne($to_user_id);

        //修改被关注用户信息
        if(empty($to_explore_user)) {
            $add_row = array();
            $add_row['user_id'] = $to_user_id;
            $add_row['user_account'] = $user_info['user_account'];
            $add_row['user_fans_count']= 1;
            $add_row['user_fans_id'] = Perm::$userId;

            $flag = $this->addUser($add_row);
            check_rs($flag,$rs_row);

            //发送新增粉丝信息
            $Explore_MessageModel = new Explore_MessageModel();
            $add_row = array();
            $add_row['message_user_id'] = $to_user_id;
            $add_row['message_user_name'] = $user_info['user_account'];
            $add_row['message_type'] = Explore_MessageModel::FANS;
            $add_row['active_user_id'] = Perm::$userId;
            $add_row['active_id'] = Perm::$userId;
            $add_row['message_create_time'] = time();

            $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
            check_rs($add_flag,$rs_row);

        } else {
            //判断被关注用户是否被当前用户关注过
            $user_fans_id_row = explode(',',$to_explore_user['user_fans_id']);

            if(in_array(Perm::$userId,$user_fans_id_row)) {
                //已被用户关注，则取消关注
                $diff_fans_user_id = array_diff($user_fans_id_row,[Perm::$userId]);

                $edit_row = array();
                $edit_row['user_fans_count'] = -1;

                $edit_flag = $this->editUser($to_user_id,$edit_row,true);
                check_rs($edit_flag,$rs_row);

                $edit_row = array();
                $edit_row['user_fans_id'] = implode(',',$diff_fans_user_id);
                $edit_flag = $this->editUser($to_user_id,$edit_row);
                check_rs($edit_flag,$rs_row);

            } else {
                //未被用户关注，则增加关注信息
                array_push($user_fans_id_row,Perm::$userId);

                $edit_row = array();
                $edit_row['user_fans_count'] = 1;

                $edit_flag = $this->editUser($to_user_id,$edit_row,true);
                check_rs($edit_flag,$rs_row);

                $edit_row = array();
                $edit_row['user_fans_id'] = implode(',',$user_fans_id_row);
                $edit_flag = $this->editUser($to_user_id,$edit_row);
                check_rs($edit_flag,$rs_row);

                //发送新增粉丝信息
                $Explore_MessageModel = new Explore_MessageModel();
                $add_row = array();
                $add_row['message_user_id'] = $to_user_id;
                $add_row['message_user_name'] = $user_info['user_account'];
                $add_row['message_type'] = Explore_MessageModel::FANS;
                $add_row['active_user_id'] = Perm::$userId;
                $add_row['active_id'] = Perm::$userId;
                $add_row['message_create_time'] = time();

                $add_flag = $Explore_MessageModel->addExploreMessage($add_row);

                check_rs($add_flag,$rs_row);
            }
        }

        $flag = is_ok($rs_row);

        return $flag;

    }

    /* 粉丝一览查询
    * @param  int $user_id 用户ID
    *
    * @return array $data 返回的查询内容
    * @access public
    */
    public function getUserFansList($user_id = null)
    {

        $get_fans_id = $this->getUser($user_id);
        $data = array();
        $user = array();

        //当前登录用户的关注列表
        $peruser_follow = array();
        if(Perm::$userId) {
            $peruser = $this->getOne(Perm::$userId);
            $peruser_follow = explode(',',$peruser['user_follow_id']);
        }

        $user_fans_ids = array_filter(array_reverse(explode(',',$get_fans_id[$user_id]['user_fans_id'])));
        $User_InfoModel = new  User_InfoModel();

        foreach ($user_fans_ids as $key=>$user_fans_id) {
            $attention_status = in_array($user_fans_id,$peruser_follow);
            $user[$key] = $User_InfoModel->getUser($user_fans_id);
            $user[$key]['attention_status'] = $attention_status;
        }

        //查找信息表中未读新增粉丝信息，将状态修改为已读
        $Explore_MessageModel = new Explore_MessageModel();
        $Explore_MessageModel->editMessageLooked(array(4));

        $data['user_info'] = current($get_fans_id);
        $data['user'] = $user;
        return $data;
    }

    /* 关注一览查询
    * @param  int $user_id 用户ID
    *
    * @return array $data 返回的查询内容
    * @access public
    */
    public function getUserFollowList($user_id = null)
    {
        $get_follow_id = $this->getUser($user_id);
        $data = array();
        $user = array();
        $user_follow_ids = array_filter(array_reverse(explode(',',$get_follow_id[$user_id]['user_follow_id'])));
        $User_InfoModel = new  User_InfoModel();

        //查找当前用户的关注列表
        $peruser_follow = array();
        if(Perm::$userId) {
            $peruser = $this->getOne(Perm::$userId);
            $peruser_follow = explode(',',$peruser['user_follow_id']);
        }

        foreach ($user_follow_ids as $key=>$user_follow_id) {
            $user[$key] = $User_InfoModel->getUser($user_follow_id);
            $user_sign = $this->getUser($user_follow_id);
            $user[$key]['user_sign'] =$user_sign[$user_follow_id]['user_sign'];
            $user[$key]['is_follow'] = 0;
            if(in_array($user_follow_id,$peruser_follow)) {
                $user[$key]['is_follow'] = 1;
            }
        }

        $data['user_info'] = current($get_follow_id);
        $data['user'] = $user;

        return $data;
    }

    /**
     * 发现好友列表分页列表
     *
     * @param  int $article_id 主键值
     *
     * @return array $rows 返回的查询内容
     * @access public
     */
    public function exploreFindFriends($cond_row = [], $order_row = [], $page = 1, $rows = 100, $user_name)
    {
        $User_InfoModel = new User_InfoModel();
        $order_row['explore_base_count'] = 'DESC';
        $order_row['user_fans_count'] = 'DESC';
        if ($user_name) {
            //好友搜索
            $user_row['user_name:LIKE'] = '%' . $user_name . '%';
            $user_info = $User_InfoModel->getByWhere($user_row);
            $user_ids = array_column($user_info, 'user_id');
            $cond['user_id:IN'] = $user_ids;
            $data = $this->listByWhere($cond, $order_row, $page, $rows);
        } else {
            //默认显示
            $data = $this->listByWhere($cond_row, $order_row, $page, $rows);
            $user_ids = array_column($data['items'], 'user_id');
            $user_row['user_id:IN'] = $user_ids;
            $user_info = $User_InfoModel->getByWhere(array('user_id:IN' => $user_ids));

        }
        foreach ($data['items'] as $k => $v) {
            $data['items'][$k]['user_name'] = $user_info[$v['user_id']]['user_name'];
            $data['items'][$k]['user_logo'] = $user_info[$v['user_id']]['user_logo'];
            if (!$user_name) {
                $explore_base = $this->getExploreBaseByUserId($v['user_id']);
                $data['items'][$k]['explore_base'] = $explore_base;
            }else {
                unset($user_info[$v['user_id']]);
            }
            //判断是否为粉丝
            if (in_array(Perm::$userId, explode(',', $v['user_fans_id']))) {
                $data['items'][$k]['isFollow'] = 1;
            } else {
                $data['items'][$k]['isFollow'] = 0;
            }

            //过滤当前登录用户信息
            if(Perm::$userId == $v['user_id']) {
                unset($data['items'][$k]);
            }
        }

        //可以搜索商城未使用社交电商的用户
        if($user_name){
            $res = [];
            foreach($user_info as $key=>$value){
                $res[$key]['user_id'] = $value['user_id'];
                $res[$key]['user_name'] = $value['user_name'];
                $res[$key]['user_logo'] = $value['user_logo'];
                $res[$key]['explore_base_count'] = 0;
                $res[$key]['isFollow'] = 0;
                $res[$key]['user_fans_count'] = 0;
            }
            $result = array_merge($data['items'],$res);
            $data['items'] = array_slice($result,0,20);
        }
        $data['items'] = array_values($data['items']);
        return $data;
    }

    //根据用户id获取用户最近发表的一篇心得以及心得图片
    public function getExploreBaseByUserId($user_id)
    {
        $sql = "SELECT explore_base.*,explore_images.images_url FROM ";
        $sql .= TABEL_PREFIX . "explore_base AS explore_base JOIN ";
        $sql .= TABEL_PREFIX . "explore_images AS explore_images ON ";
        $sql .= "explore_base.explore_id = explore_images.explore_id ";
        $sql .= "where explore_base.is_del = 0 AND explore_base.explore_status = 0 ";
        $sql .= "AND explore_base.user_id = " . $user_id;
        $sql .= " GROUP BY explore_base.explore_id ORDER BY explore_base.explore_id DESC LIMIT 4";

        $result = $this->sql->getAll($sql);

        return $result;
    }

    //编辑用户签名
    public function editEexploreUser($user_id,$user_row)
    {
        $user_info = $this->getUser($user_id);
        if($user_info){
            $flag = $this->editUser($user_id, $user_row);
        }else{
            $user_row['user_id'] = $user_id;
            $user_row['user_account'] = Perm::$row['user_account'];
            $flag = $this->addUser($user_row);
        }
        return $flag;
    }

}

?>