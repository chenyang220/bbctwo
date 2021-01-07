<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
}

/**
 * @author     Yf <service@yuafeng.cn>
 */
class Explore_CollectionModel extends Explore_Collection
{
    const NORMAL = 0;            //正常
    const SCREEN = 1;            //屏蔽
    const DELETE = 2;            //删除


    //用户添加评论
    public function addExploreComment($explore_id = null,$comment_content = '')
    {
        $rs_row = array();
        $add_row = array();
        $add_row['user_id'] = Perm::$userId;
        $add_row['user_account'] = Perm::$row['user_account'];
        $add_row['explore_id'] = $explore_id;
        $add_row['comment_content'] = $comment_content;
        $add_row['comment_addtime'] = time();

        $Explore_BaseModel = new Explore_BaseModel();
        $explore = $Explore_BaseModel->getOne($explore_id);

        $add_row['is_author'] = 0;
        if($explore['user_id'] == Perm::$userId) {
            $add_row['is_author'] = 1;
        }

        $comment_id = $this->addComment($add_row,true);
        check_rs($comment_id,$rs_row);

        //发送评论通知（如果是作者自己则不需要通知）
        if($add_row['is_author'] !== 1) {
            $Explore_MessageModel = new Explore_MessageModel();
            $add_row = array();
            $add_row['message_user_id'] = $explore['user_id'];
            $add_row['message_user_name'] = $explore['user_account'];
            $add_row['message_type'] = Explore_MessageModel::COMMENT;
            $add_row['active_user_id'] = Perm::$userId;
            $add_row['active_id'] = $comment_id;
            $add_row['message_create_time'] = time();

            $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
            check_rs($add_flag,$rs_row);
        }


        return is_ok($rs_row);
    }

    //根据心得详情页中的评论及其回复信息
    public function getCommentSimple($explore_id = null)
    {
        $user_id = Perm::$userId;
        $data = array();

        //查找所有的评论数量
        $sum_sql = 'SELECT COUNT(*) sum FROM '.TABEL_PREFIX.'explore_comment WHERE 1 AND explore_id='.$explore_id.' AND comment_state='.Explore_CommentModel::NORMAL;
        $sum = $this -> sql -> getAll($sum_sql);
        $data['sum'] = $sum[0]['sum'];

        //查找出前3条评论
        $com_sql = 'SELECT * FROM '.TABEL_PREFIX.'explore_comment WHERE 1 AND explore_id='.$explore_id.' AND comment_state='.Explore_CommentModel::NORMAL.' ORDER BY comment_addtime DESC LIMIT 0,3';
        $com = $this -> sql -> getAll($com_sql);

        //查找出前3条评论的前2条回复
        foreach ($com as $key => $val)
        {
            //评论用户头像
            $com[$key]['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$val['user_id'];

            //判断该条评论当前用户是否点赞过
            $com[$key]['is_like'] = 0;
            $comment_like_user = explode(',',$val['comment_like_user']);
            if($user_id && in_array($user_id,$comment_like_user)) {
                $com[$key]['is_like'] = 1;
            }

            //评论添加日期
            if (date('Y') == date('Y',$val['comment_addtime'])) {
                $com[$key]['comment_adddate'] = date("m-d",$val['comment_addtime']);
            } else {
                $com[$key]['comment_adddate'] = date("Y-m-d",$val['comment_addtime']);
            }

            $replysum_sql = 'SELECT COUNT(*) sum FROM '.TABEL_PREFIX.'explore_reply WHERE 1 AND explore_id='.$explore_id.' AND reply_state='.Explore_ReplyModel::NORMAL.' AND comment_id='.$val['comment_id'];
            $replysum = $this -> sql -> getAll($replysum_sql);
            $com[$key]['reply']['sum'] = $replysum[0]['sum'];

            $reply_sql = 'SELECT * FROM '.TABEL_PREFIX.'explore_reply WHERE 1 AND explore_id='.$explore_id.' AND comment_id='.$val['comment_id'].' AND reply_state='.Explore_ReplyModel::NORMAL.' ORDER BY reply_addtime ASC LIMIT 0,2';
            $reply = $this -> sql -> getAll($reply_sql);
            $com[$key]['reply']['reply_list'] = $reply;
        }

        $data['comment'] = $com;

        return $data;
    }

    //用户点赞评论及取消点赞
    public function editCommentLike($comment_id=null)
    {
        $user_id = Perm::$userId;
        $rs_row = array();
        $comment = $this->getOne($comment_id);

        //判断当前用户是否点赞过该评论
        $comment_like_user = explode(',',$comment['comment_like_user']);
        if($user_id && in_array($user_id,$comment_like_user)) {
            //已经点赞过，取消点赞
            $diff_user_id = array_diff($comment_like_user,[$user_id]);

            $edit_row = array();
            $edit_row['comment_like_user'] = implode(',',$diff_user_id);
            $edit_row['comment_like_count'] = $comment['comment_like_count']-1;
            $edit_flag = $this->editComment($comment_id,$edit_row);
            check_rs($edit_flag,$rs_row);
        } else {
            //没有点赞过，添加点赞
            array_push($comment_like_user, $user_id);

            $edit_row = array();
            $edit_row['comment_like_user'] = implode(',',$comment_like_user);
            $edit_row['comment_like_count'] = $comment['comment_like_count']+1;
            $edit_flag = $this->editComment($comment_id,$edit_row);
            check_rs($edit_flag,$rs_row);

            //发送评论点赞信息
            $Explore_MessageModel = new Explore_MessageModel();
            $add_row = array();
            $add_row['message_user_id'] = $comment['user_id'];
            $add_row['message_user_name'] = $comment['user_account'];
            $add_row['message_type'] = Explore_MessageModel::LIKEC;
            $add_row['active_user_id'] = $user_id;
            $add_row['active_id'] = $comment_id;
            $add_row['message_create_time'] = time();

            $add_flag = $Explore_MessageModel->addExploreMessage($add_row);
            check_rs($add_flag,$rs_row);
        }

        $flag = is_ok($rs_row);

        return $flag;

    }

    //根据explore_id获取心得的所有评论及其回复
    public function getCommentAll($explore_id = null)
    {
        $user_id = Perm::$userId;
        $data = array();

        //查找所有的评论数量
        $sum_sql = 'SELECT COUNT(*) sum FROM '.TABEL_PREFIX.'explore_comment WHERE 1 AND explore_id='.$explore_id.' AND comment_state='.Explore_CommentModel::NORMAL;
        $sum = $this -> sql -> getAll($sum_sql);
        $data['sum'] = $sum[0]['sum'];

        //查找出所有评论
        $com_sql = 'SELECT * FROM '.TABEL_PREFIX.'explore_comment WHERE 1 AND explore_id='.$explore_id.' AND comment_state='.Explore_CommentModel::NORMAL.' ORDER BY comment_addtime DESC';
        $com = $this -> sql -> getAll($com_sql);

        //查找出所有评论的前2条回复
        foreach ($com as $key => $val)
        {
            //评论用户头像
            $com[$key]['user_logo'] = Yf_Registry::get('ucenter_api_url') . '?ctl=Index&met=img&user_id='.$val['user_id'];

            //判断该条评论当前用户是否点赞过
            $com[$key]['is_like'] = 0;
            $comment_like_user = explode(',',$val['comment_like_user']);
            if($user_id && in_array($user_id,$comment_like_user)) {
                $com[$key]['is_like'] = 1;
            }

            //评论添加日期
            if (date('Y') == date('Y',$val['comment_addtime'])) {
                $com[$key]['comment_adddate'] = date("m-d",$val['comment_addtime']);
            } else {
                $com[$key]['comment_adddate'] = date("Y-m-d",$val['comment_addtime']);
            }

            $replysum_sql = 'SELECT COUNT(*) sum FROM '.TABEL_PREFIX.'explore_reply WHERE 1 AND explore_id='.$explore_id.' AND reply_state='.Explore_ReplyModel::NORMAL.' AND comment_id='.$val['comment_id'];
            $replysum = $this -> sql -> getAll($replysum_sql);
            $com[$key]['reply']['sum'] = $replysum[0]['sum'];

            $reply_sql = 'SELECT * FROM '.TABEL_PREFIX.'explore_reply WHERE 1 AND explore_id='.$explore_id.' AND comment_id='.$val['comment_id'].' AND reply_state='.Explore_ReplyModel::NORMAL.' ORDER BY reply_addtime ASC LIMIT 0,2';
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

                //回复添加日期
                if (date('Y') == date('Y',$v['reply_addtime'])) {
                    $reply[$k]['reply_adddate'] = date("m-d",$v['reply_addtime']);
                } else {
                    $reply[$k]['reply_adddate'] = date("Y-m-d",$v['reply_addtime']);
                }

            }

            $com[$key]['reply']['reply_list'] = $reply;
        }

        $data['comment'] = $com;

        return $data;
    }


    //删除我的评论
    public function delExploreComment($comment_id = null)
    {
        $rs_row = array();

        $flag = false;

        $comment = $this->getOne($comment_id);

        //判断是否是自己的评论
        if(Perm::$userId == $comment['user_id']) {
            $edit_row = array();
            $edit_row['comment_state'] = Explore_CommentModel::DELETE;

            $flag = $this->editComment($comment_id,$edit_row);
        }
        check_rs($flag,$rs_row);

        return is_ok($rs_row);

    }
}

?>