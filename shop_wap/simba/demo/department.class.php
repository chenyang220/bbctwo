<?php

/*
 * 组织API 例子
 * 如果返回 无权限， 要先进行组织授权（组织里添加应用，组织和应用关联）
 */

require_once 'src/Api.class.php';

class department {

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

    /* 获取部门信息 */

    public function enterprise_department_info() {
        $dept_id = '328496'; //部门id
        $enterprise_id = '239424'; //企业id
        $result = $this->api->simba_enterprise_department_info($this->access_token, $dept_id, $enterprise_id);
        $this->format($result);
    }

    /* 移除部门成员 */

    public function enterprise_department_member_delete() {
        $enterprise_id = '239424'; //企业id
        $dept_id = '328496'; //部门id
        $operate_user_number = '66209676'; //用户号
        $user_numbers = array(
            66209674, 66209675
        );
        $result = $this->api->simba_enterprise_department_member_delete($this->access_token, $enterprise_id, $dept_id, $operate_user_number, $user_numbers);
        $this->format($result);
    }

    /* 获取组织信息 
     */

    public function enterprise_info() {
        $user_number = '66209676'; //用户号
        $enterprise_id = '239424'; //企业id
        $result = $this->api->simba_enterprise_info($this->access_token, $user_number, $enterprise_id);
        $this->format($result);
    }

    /* 成员加入部门 
     * 成员必须隶属与这个组织，才能加入到这个组织当中的部门。
     */

    public function enterprise_department_member_join() {
        $enterprise_id = '239424'; //企业id
        $dept_id = '328495'; //部门id
        $user_numbers = array(
            '66209676'
        );
        $result = $this->api->simba_enterprise_department_member_join($this->access_token, $enterprise_id, $dept_id, $user_numbers);
        $this->format($result);
    }

    /* 获取部门成员列表 */

    public function enterprise_department_member_list() {
        $enterprise_id = '239424'; //企业id
        $dept_id = '328496'; //部门id

        $result = $this->api->simba_enterprise_department_member_list($this->access_token, $enterprise_id, $dept_id);
        $this->format($result);
    }

    /* 删除部门 */

    public function enterprise_department_delete() {
        $enterprise_id = '239424'; //企业id
        $dept_id = '328496'; //部门id
        $operate_user_number = '66209676'; //操作人

        $result = $this->api->simba_enterprise_department_delete($this->access_token, $enterprise_id, $dept_id, $operate_user_number);
        $this->format($result);
    }

    /* 获取组织成员列表
     */

    public function enterprise_buddy_page() {
        $result = $this->api->simba_enterprise_buddy_page($this->access_token);
        $this->format($result);
    }

    /* 离职用户 */

    public function enterprise_buddy_leave() {
        $enterprise_id = '239424'; //企业id
        $leave_user_number = '66209675'; //离职用户号码
        $operate_user_number = '66209676'; //操作人

        $result = $this->api->simba_enterprise_buddy_leave($this->access_token, $enterprise_id, $leave_user_number, $operate_user_number);
        $this->format($result);
    }

}
