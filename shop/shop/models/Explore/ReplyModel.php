<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuanfeng.cn>
 */
class Explore_ReplyModel extends Explore_Reply
{
    const NORMAL = 0;            //正常
    const SCREEN = 1;            //屏蔽
    const DELETE = 2;            //删除

    //用户回复
    public function addCommentReply($explore_id=null ,$comment_id=null ,$to_reply_id=null ,$reply_content='')
    {
        $rs_row = array();

        $add_row = array();
        $add_row['user_id'] = Perm::$userId;
        $add_row['user_account'] = Perm::$row['user_account'];
        $add_row['explore_id'] = $explore_id;
        $add_row['comment_id'] = $comment_id;
        $add_row['reply_content'] = $reply_content;
        $add_row['reply_addtime'] = time();

        //判断当前用户是否是心得作者
        $Explore_BaseModel = new Explore_BaseModel();
        $explore = $Explore_BaseModel->getOne($explore_id);
        $add_row['is_author'] = 0;
        if(Perm::$userId == $explore['user_id']) {
            $add_row['is_author'] = 1;
        }

        //如果有to_reply_id,查找出被回复的用户名
        if($to_reply_id) {
            $to_reply = $this->getOne($to_reply_id);
            $add_row['to_reply_id'] = $to_reply_id;
            $add_row['to_reply_user_account'] = $to_reply['user_account'];
            $add_row['to_reply_user_id'] = $to_reply['user_id'];
            $add_row['to_reply_is_author'] = 0;
            //判断回复的回复者是否是作者
            if($to_reply['user_id'] == $explore['user_id']) {
                $add_row['to_reply_is_author'] = 1;
            }

        }

        file_put_contents(dirname(__FILE__).'/abs.php', print_r($add_row,true));
        $flag = $this->addReply($add_row,true);
        check_rs($flag,$rs_row);


        //添加通知消息
        $Explore_MessageModel = new Explore_MessageModel();
        $add_row = array();
        if($to_reply_id) {
            $add_row['message_user_id'] = $to_reply['user_id'];
            $add_row['message_user_name'] = $to_reply['user_account'];
            $add_row['reply_type'] = Explore_MessageModel::REPLY;
        } else {
            $Explore_CommentModel = new Explore_CommentModel();
            $comment = $Explore_CommentModel->getOne($comment_id);

            $add_row['message_user_id'] = $comment['user_id'];
            $add_row['message_user_name'] = $comment['user_account'];
            $add_row['reply_type'] = Explore_MessageModel::COMMENT;

        }
        $add_row['active_id'] = $flag;

        $add_row['message_type'] = Explore_MessageModel::COMMENT;
        $add_row['active_user_id'] = Perm::$userId;
        $add_row['message_create_time'] = time();

        //用户给自己评论不需要发送信息
        if(Perm::$userId !== $add_row['message_user_id']) {
            $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
            check_rs($add_flag,$rs_row);
        }
        return is_ok($rs_row);

    }


    //用户点赞回复与取消回复
    public function editReplyLike($reply_id = null)
    {
        $rs_row = array();
        $reply = $this->getOne($reply_id);

        //判断当前用户是否点赞过该评论
        $reply_like_user = explode(',',$reply['reply_like_user']);
        if(in_array(Perm::$userId,$reply_like_user)) {
            //已经点赞过，取消点赞
            $diff_user_id = array_diff($reply_like_user,[Perm::$userId]);

            $edit_row = array();
            $edit_row['reply_like_user'] = implode(',',$diff_user_id);
            $edit_row['reply_like_count'] = $reply['reply_like_count']-1;
            $edit_flag = $this->editReply($reply_id,$edit_row);
            check_rs($edit_flag,$rs_row);
        } else {
            //没有点赞过，添加点赞
            array_push($reply_like_user, Perm::$userId);

            $edit_row = array();
            $edit_row['reply_like_user'] = implode(',',$reply_like_user);
            $edit_row['reply_like_count'] = $reply['reply_like_count']+1;
            $edit_flag = $this->editReply($reply_id,$edit_row);
            check_rs($edit_flag,$rs_row);

            //发送评论点赞信息(用户给自己点赞不需要发送信息)
            if(Perm::$userId !== $reply['user_id']) {
                $Explore_MessageModel = new Explore_MessageModel();
                $add_row = array();
                $add_row['message_user_id'] = $reply['user_id'];
                $add_row['message_user_name'] = $reply['user_account'];
                $add_row['message_type'] = Explore_MessageModel::LIKER;
                $add_row['active_user_id'] = Perm::$userId;
                $add_row['active_id'] = $reply_id;
                $add_row['message_create_time'] = time();

                $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
                check_rs($add_flag,$rs_row);
            }

        }

        $flag = is_ok($rs_row);

        return $flag;
    }

    //用户删除自己的回复
    public function delExploreComment($reply_id = null)
    {
        $rs_row = array();

        $flag = false;

        $reply = $this->getOne($reply_id);

        //判断是否是自己的评论
        if(Perm::$userId == $reply['user_id']) {
            $edit_row = array();
            $edit_row['reply_state'] = Explore_ReplyModel::DELETE;

            $flag = $this->editReply($reply_id,$edit_row);
        }
        check_rs($flag,$rs_row);

        return is_ok($rs_row);

    }

    //用户
    public function getReplyAll( $comment_id = null)
    {
        $user_id = Perm::$userId;
        $data = array();

        $replysum_sql = 'SELECT COUNT(*) sum FROM '.TABEL_PREFIX.'explore_reply WHERE 1 AND reply_state='.Explore_ReplyModel::NORMAL.' AND comment_id='.$comment_id;
        $replysum = $this -> sql -> getAll($replysum_sql);
        $data['sum'] = $replysum[0]['sum'];

        $reply_sql = 'SELECT * FROM '.TABEL_PREFIX.'explore_reply WHERE 1 AND comment_id='.$comment_id.' AND reply_state='.Explore_ReplyModel::NORMAL.' ORDER BY reply_addtime ASC';
        $reply = $this -> sql -> getAll($reply_sql);

        foreach ($reply as $k => $v) {
            //回复用户头像
            $reply[$k]['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$v['user_id'];

            //判断该条回复当前用户是否点赞过
            $reply[$k]['is_like'] = 0;
            $reply_like_user = explode(',',$v['reply_like_user']);
            if($user_id && in_array($user_id,$reply_like_user)) {
                $reply[$k]['is_like'] = 1;
            }

            //评论添加日期
            if (date('Y') == date('Y',$v['reply_addtime'])) {
                $reply[$k]['reply_adddate'] = date("m-d",$v['reply_addtime']);
            } else {
                $reply[$k]['reply_adddate'] = date("Y-m-d",$v['reply_addtime']);
            }

        }

        $data['reply'] = $reply;

        return $data;

    }


    
}

?>