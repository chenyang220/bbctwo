<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_MessageModel extends Explore_Message
{
    const LIKE = 1;            //点赞评论
    const COMMENT = 2;         //评论（回复评论）
    const REPORT = 3;          //举报别人
    const FANS = 4;            //新增粉丝
    const LIKEC = 5;          //点赞评论
    const LIKER = 6;           //点赞回复
    const BREPORT = 7;         //被举报

    const EXPLORE = 0;         //评论心得
    const REPLY = 1;         //回复回复
    /* 回复评论设置为2 */

    public function addExploreMessage($add_row = array())
    {
        $flag = true;
        $rs_row = array();
        /*
         * 点赞，举报，新增粉丝每次操作都只发送一条通知。但是一个用户可以多次评论，发送多条通知。
         * */
        //判断该条信息是否已经发送过
        if($add_row['message_type'] !== Explore_MessageModel::COMMENT) {
            $sql = 'SELECT message_id FROM '.TABEL_PREFIX.'explore_message 
                WHERE 
                message_user_id='.$add_row['message_user_id'].' 
                AND message_user_name=\''.$add_row['message_user_name'].'\' 
                AND message_type='.$add_row['message_type'].' 
                AND active_user_id='.$add_row['active_user_id'].' 
                AND active_id='.$add_row['active_id'];
            $message = $this -> sql -> getAll($sql);
            if(!$message) {
                $add_flag = $this->addMessage($add_row);
                check_rs($add_flag,$rs_row);

                $flag = is_ok($rs_row);
            }
        } else {
            $add_flag = $this->addMessage($add_row);
            check_rs($add_flag,$rs_row);

            $flag = is_ok($rs_row);
        }

        return $flag;
    }

    //获取当前用户所有未读信息
    public function getUnreadMeaasgeNum()
    {
        //1.未读点赞信息
        $like_sql = 'SELECT count(*) sum FROM '.TABEL_PREFIX.'explore_message 
                    WHERE 1 AND message_user_id='.Perm::$userId.' 
                    AND message_type IN (1,5,6) 
                    AND message_islook=0 
                    AND message_isdelete=0';
        $like_sum = $this -> sql -> getAll($like_sql);
        $data['like_sum'] = $like_sum[0]['sum'];

        //2.未读评论信息
        $comment_sql = 'SELECT count(*) sum FROM '.TABEL_PREFIX.'explore_message 
                    WHERE 1 AND message_user_id='.Perm::$userId.' 
                    AND message_type =2
                    AND message_islook=0 
                    AND message_isdelete=0';
        $comment_sum = $this -> sql -> getAll($comment_sql);
        $data['comment_sum'] = $comment_sum[0]['sum'];

        //3.未读通知信息
        $report_sql = 'SELECT count(*) sum FROM '.TABEL_PREFIX.'explore_message 
                    WHERE 1 AND message_user_id='.Perm::$userId.' 
                    AND message_type IN (3,7)
                    AND message_islook=0 
                    AND message_isdelete=0';
        $report_sum = $this -> sql -> getAll($report_sql);
        $data['report_sum'] = $report_sum[0]['sum'];

        //4.未读新增粉丝信息
        $fans_sql = 'SELECT count(*) sum FROM '.TABEL_PREFIX.'explore_message 
                    WHERE 1 AND message_user_id='.Perm::$userId.' 
                    AND message_type =4
                    AND message_islook=0 
                    AND message_isdelete=0';
        $fans_sum = $this -> sql -> getAll($fans_sql);
        $data['fans_sum'] = $fans_sum[0]['sum'];
        $data['message_sum'] = $data['fans_sum'] + $data['report_sum'] + $data['comment_sum'] + $data['like_sum'];
        return $data;
    }

    //获取用户所有点赞信息
    public function getLikeMessage($page=1,$rows=10)
    {
        $rs_row = array();

        $cond_row = array();
        $cond_row['message_user_id'] = Perm::$userId;
        $cond_row['message_type:IN'] = array(1,5,6);
        $cond_row['message_isdelete'] = 0;

        $data = $this->listByWhere($cond_row, array('message_create_time'=>'DESC'), $page, $rows);

        $Explore_BaseModel = new Explore_BaseModel();
        $Explore_CommentModel = new Explore_CommentModel();
        $Explore_ReplyModel = new Explore_ReplyModel();
        $User_BaseModel = new User_BaseModel();
        $Explore_ImagesModel = new Explore_ImagesModel();

        foreach ($data['items'] as $key => $val) {
            $data['items'][$key]['active_user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$val['active_user_id'];
            $user_info = $User_BaseModel->getOne($val['active_user_id']);
            $data['items'][$key]['active_user_account'] = $user_info['user_account'];

            if (date('Y') == date('Y',$val['message_create_time'])) {
                $data['items'][$key]['message_create_date'] = date("m-d",$val['message_create_time']);
            } else {
                $data['items'][$key]['message_create_date'] = date("Y-m-d",$val['message_create_time']);
            }

            //查找是否被删除
            if($val['message_type'] == 1) {
                $data['items'][$key]['type'] = __('心得');
                //点赞心得（判断是否被删除）
                $explore = $Explore_BaseModel->getOne($val['active_id']);
                $data['items'][$key]['is_del'] = $explore['is_del'];
                $data['items'][$key]['active_content'] = $explore['explore_title'];
                $data['items'][$key]['explore_images'] = $Explore_ImagesModel->getImageByExploreId([$explore['explore_id']]);
                $data['items'][$key]['explore_id'] = $explore['explore_id'];
                $data['items'][$key]['explore_del'] = $explore['is_del'];
            }
            if($val['message_type'] == 5) {
                $data['items'][$key]['type'] = __('评论');
                //点赞评论
                $comment = $Explore_CommentModel->getOne($val['active_id']);
                $data['items'][$key]['is_del'] = $comment['comment_state'];
                $data['items'][$key]['active_content'] = $comment['comment_content'];
                $data['items'][$key]['explore_images'] = $Explore_ImagesModel->getImageByExploreId([$comment['explore_id']]);
                $data['items'][$key]['explore_id'] = $comment['explore_id'];

                $explore = $Explore_BaseModel->getOne($comment['explore_id']);
                $data['items'][$key]['explore_del'] = $explore['is_del'];
            }
            if($val['message_type'] == 6) {
                $data['items'][$key]['type'] = __('回复');
                //点赞回复
                $reply = $Explore_ReplyModel->getOne($val['active_id']);
                $data['items'][$key]['is_del'] = $reply['reply_state'];
                $data['items'][$key]['active_content'] = $reply['reply_content'];
                $data['items'][$key]['explore_images'] = $Explore_ImagesModel->getImageByExploreId([$reply['explore_id']]);
                $data['items'][$key]['explore_id'] = $reply['explore_id'];

                $explore = $Explore_BaseModel->getOne($reply['explore_id']);
                $data['items'][$key]['explore_del'] = $explore['is_del'];
            }

        }

        //将所有未读信息就修改为已读
        $this->editMessageLooked(array(1,5,6));

        $data['flag'] = is_ok($rs_row);
        $data['records'] = count($data['items']);

        return $data;
    }

    //获取用户所有的评论信息
    public function  getCommentMessage($page=1, $rows=10)
    {
        $rs_row = array();

        $cond_row = array();
        $cond_row['message_user_id'] = Perm::$userId;
        $cond_row['message_type'] = 2;
        $cond_row['message_isdelete'] = 0;

        $data = $this->listByWhere($cond_row, array('message_create_time'=>'DESC'), $page, $rows);

        $Explore_BaseModel = new Explore_BaseModel();
        $Explore_CommentModel = new Explore_CommentModel();
        $Explore_ReplyModel = new Explore_ReplyModel();
        $User_BaseModel = new User_BaseModel();
        $Explore_ImagesModel = new Explore_ImagesModel();

        foreach ($data['items'] as $key => $val) {
            $data['items'][$key]['active_user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$val['active_user_id'];
            $user_info = $User_BaseModel->getOne($val['active_user_id']);
            $data['items'][$key]['active_user_account'] = $user_info['user_account'];

            if (date('Y') == date('Y',$val['message_create_time'])) {
                $data['items'][$key]['message_create_date'] = date("m-d",$val['message_create_time']);
            } else {
                $data['items'][$key]['message_create_date'] = date("Y-m-d",$val['message_create_time']);
            }

            //查找是否被删除
            if($val['reply_type'] == 0) {
                $data['items'][$key]['type'] = __('评论');
                $data['items'][$key]['to_type'] = __('心得');
                //评论心得（判断是否被删除）
                $comment = $Explore_CommentModel->getOne($val['active_id']);
                $data['items'][$key]['active_content'] = $comment['comment_content'];  //评论内容
                $data['items'][$key]['is_del'] = $comment['comment_state'];  //评论是否删除

                $explore = $Explore_BaseModel->getOne($comment['explore_id']);
                $data['items'][$key]['explore_images'] = $Explore_ImagesModel->getImageByExploreId([$explore['explore_id']]);
                $data['items'][$key]['explore_id'] = $explore['explore_id'];
                $data['items'][$key]['explore_title'] = $explore['explore_title'];
                $data['items'][$key]['explore_del'] = $explore['is_del'];
                $data['items'][$key]['to_is_del'] = $explore['is_del'];  //心得是否删除
                $data['items'][$key]['to_active_content'] =  $explore['explore_title'];
            }
            if($val['reply_type'] == 2) {
                $data['items'][$key]['type'] = __('回复');
                $data['items'][$key]['to_type'] = __('评论');
                //回复评论
                $reply = $Explore_ReplyModel->getOne($val['active_id']);
                $data['items'][$key]['active_content'] = $reply['reply_content'];  //回复的内容
                $data['items'][$key]['is_del'] = $reply['reply_state']; //回复是否被删除

                $comment = $Explore_CommentModel->getOne($reply['comment_id']);
                $data['items'][$key]['explore_images'] = $Explore_ImagesModel->getImageByExploreId([$comment['explore_id']]);
                $data['items'][$key]['explore_id'] = $comment['explore_id'];
                $explore = $Explore_BaseModel->getOne($comment['explore_id']);
                $data['items'][$key]['explore_title'] = $explore['explore_title'];
                $data['items'][$key]['explore_del'] = $explore['is_del'];
                $data['items'][$key]['to_is_del'] = $comment['comment_state'];  //评论是否被删除
                $data['items'][$key]['to_active_content'] =  $comment['comment_content'];
            }
            if($val['reply_type'] == 1) {
                $data['items'][$key]['type'] = __('回复');
                $data['items'][$key]['to_type'] = __('回复');
                //回复回复
                $reply = $Explore_ReplyModel->getOne($val['active_id']);
                $data['items'][$key]['is_del'] = $reply['reply_state'];
                $data['items'][$key]['active_content'] = $reply['reply_content'];

                $to_reply = $Explore_ReplyModel->getOne($reply['to_reply_id']);
                $data['items'][$key]['explore_images'] = $Explore_ImagesModel->getImageByExploreId([$reply['explore_id']]);
                $data['items'][$key]['explore_id'] = $reply['explore_id'];
                $explore = $Explore_BaseModel->getOne($reply['explore_id']);
                $data['items'][$key]['explore_title'] = $explore['explore_title'];
                $data['items'][$key]['explore_del'] = $explore['is_del'];
                $data['items'][$key]['to_is_del'] = $to_reply['reply_state'];  //回复是否被删除
                $data['items'][$key]['to_active_content'] =  $to_reply['reply_content'];
            }
        }

        //将所有未读信息就修改为已读
        $this->editMessageLooked(array(2));

        $data['flag'] = is_ok($rs_row);
        $data['records'] = count($data['items']);

        return $data;
    }

    //获取用户所有的投诉通知信息
    public function getReportMessage($page=1, $rows=10)
    {
        $rs_row = array();

        $cond_row = array();
        $cond_row['message_user_id'] = Perm::$userId;
        $cond_row['message_type:IN'] = array(3,7);
        $cond_row['message_isdelete'] = 0;

        $data = $this->listByWhere($cond_row, array('message_create_time'=>'DESC'), $page, $rows);

        $Explore_ReportModel = new Explore_ReportModel();
        $Explore_BaseModel = new Explore_BaseModel();
        $User_BaseModel = new User_BaseModel();

        foreach ($data['items'] as $key => $val) {
            $user_info = $User_BaseModel->getOne($val['active_user_id']);
            $data['items'][$key]['active_user_account'] = $user_info['user_account'];

            //举报处理状态
            $report = $Explore_ReportModel->getOne($val['active_id']);
            $data['items'][$key]['report_status'] = $report['report_status'];
            if($report['report_status'] == 1) {
                $data['items'][$key]['status'] = __('投诉成功');
            }

            if($report['report_status'] == 2) {
                $data['items'][$key]['status'] = __('投诉未通过');
            }

            $explore = $Explore_BaseModel->getOne($report['explore_id']);
            $data['items'][$key]['explore_id'] = $report['explore_id'];
            $data['items'][$key]['explore_title'] = $explore['explore_title'];

        }
        //将所有未读信息就修改为已读
        $this->editMessageLooked(array(3,7));

        $data['flag'] = is_ok($rs_row);
        $data['records'] = count($data['items']);

        return $data;
    }


    //修改信息状态为已读
    public function editMessageLooked($message_type = array())
    {
        $message_type_str = implode(',',$message_type);
        $sql = 'SELECT message_id FROM '.TABEL_PREFIX.'explore_message  
                WHERE message_type in ('.$message_type_str.') AND message_islook=0 AND message_user_id='.Perm::$userId.' AND message_isdelete=0';

        $message_id = $this -> sql -> getAll($sql);

        $id = array_column($message_id,'message_id');

        $edit_row = array();
        $edit_row['message_islook'] = 1;
        $flag = $this->editMessage($id,$edit_row);

        return $flag;

    }
}

?>