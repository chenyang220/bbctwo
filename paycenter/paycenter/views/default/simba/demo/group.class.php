<?php

/*
 * 群聊API 例子
 */

require_once 'src/Api.class.php';

class group {

    private $access_token;

    public function __construct($access_token) {
        $this->access_token = $access_token;
        $this->api = new simba\oauth\Api();
    }

    /* 格式化输出
     * 这里只是简单输出
     */

    public function format($result) {
        print_r($result);
    }

    /* 获取群成员列表 */

    public function getUserGroups() {
        $user_number = '66209676'; //用户号码
        $result = $this->api->simba_group_user_groups($this->access_token, $user_number);
        $this->format($result);
    }

    /* 获取群聊信息 */

    public function getGroupInfo() {
        $user_number = '66209676'; //用户号码
        $group_number = '256760'; //群聊号码
        $result = $this->api->simba_group_info($this->access_token, $user_number, $group_number);
        $this->format($result);
    }

    /* 邀请成员加入群聊 */

    public function groupMemberInvite() {
        $user_number = '66209676'; //用户号码
        $group_number = '325877'; //群聊号码
        $invitation_user_number = '66981728'; //被邀请人
        $result = $this->api->simba_group_member_invite($this->access_token, $user_number, $group_number, $invitation_user_number);
        $this->format($result);
    }

    /* 移除群成员 */

    public function member_remove() {
        $user_number = '66209676'; //用户号码
        $group_number = '325877'; //群聊号码
        $remove_user_number = '66981728'; //被邀请人
        $result = $this->api->simba_group_member_invite($this->access_token, $user_number, $group_number, $remove_user_number);
        $this->format($result);
    }

}
