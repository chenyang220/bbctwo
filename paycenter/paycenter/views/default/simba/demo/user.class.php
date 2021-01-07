<?php

/*
 * 用户api例子
 */
require_once 'src/Api.class.php';

class user {

    private $access_token;

    public function __construct($access_token) {
        $this->access_token = $access_token;
        $this->api = new simba\oauth\Api();
    }

    /* 获取当前用户信息 */

    public function getUserInfo() {
        $result = $this->api->simba_user_info($this->access_token);
        $this->format($result);
    }

    /* 修改个人信息 */

    public function editUserInfo() {
        $user_info = array(
            'userNumber' => '66209676', //必填
            'nickName' => 'Ruby',
            'address' => 'abc street',
            'personalSignature' => '我思故我在',
            'personalIntro' => '好公司的一名好员工',
        );
        $result = $this->api->simba_user_edit($this->access_token, $user_info);
        $this->format($result);
    }

    /* 获取用户公开信息 */

    public function getUserPublic() {
        $account = '66209676'; //账户<手机号、邮箱、用户号码>
        $result = $this->api->simba_user_public($this->access_token, $account);
        $this->format($result);
    }

    /* 批量获取用户公开信息 */

    public function getUserPublicBatch() {
        $user_numbers = array(
            '66209676', '66565084'
        );
        $result = $this->api->simba_user_public_batch($this->access_token, $user_numbers);
        $this->format($result);
    }

    /* 格式化输出
     * 这里只是简单输出
     */

    public function format($result) {
        print_r($result);
    }

}
