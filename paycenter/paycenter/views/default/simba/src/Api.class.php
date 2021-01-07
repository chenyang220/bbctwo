<?php

namespace simba\oauth;

include_once __DIR__ .'/Utils/Log.class.php';
/*
 * simba 接口调用
 */

class Api {

    private $client_id;
    private $client_secret;
    public $error_msg;

    public function __construct($config = array()) {
        if (empty($config)) {
            $this->config = include 'config.php';
        } else {
            $this->config = $config;
        }

        if (!isset($this->config) || !is_array($this->config)) {
            echo 'Please config Oauth Arguments';
            exit;
        } else {
            $this->client_id = $this->config['client_id'];
            $this->client_secret = $this->config['client_secret'];
        }
        if ($this->config['log'] == 1) {
            //是否启用日志
            $destination = S_ROOT . $this->config['log_destination'];
            $this->log = new Utils\Log($destination);
        }
    }

    /* 接口调用签名
     * 对所有API请求参数（包括公共参数和业务参数，但除去sign参数和byte[]类型的参数），根据参数名称的ASCII码表的顺序排序。 
      如：foo:1, bar:2, foo_bar:3, foobar:4排序后的顺序是bar:2, foo:1, foo_bar:3, foobar:4。
      将排序好的参数名和参数值拼装在一起，根据上面的示例得到的结果为：bar2foo1foo_bar3foobar4。
      把拼装好的字符串采用utf-8编码，使用签名算法对编码后的字节流进行摘要。如果使用MD5算法，
      则需要在拼装的字符串前后加上app的secret后， 再进行摘要，如：md5(secret+bar2foo1foo_bar3foobar4+secret)；如果使用HMAC_MD5算法，
      则需要用app的secret初始化摘要算法后，再进行摘要，如：hmac_md5(bar2foo1foo_bar3foobar4)。
      将摘要得到的字节流结果使用十六进制表示，如：hex(“helloworld”.getBytes(“utf-8”)) = “68656C6C6F776F726C64” 说明：MD5和HMAC_MD5都是128位长度的摘要算法，
      用16进制表示，一个十六进制的字符能表示4个位，所以签名后的字符串长度固定为32个十六进制字符。
     * 
     * $method : md5 ,  hmac_md5
     */

    protected function getSign($args, $method = 'md5') {
        ksort($args);
        $args_str = '';
        foreach ($args as $key => $val) {
            $args_str .= $key . $val;
        }
        if ($method == 'md5') {
            $args_md5 = md5($this->client_secret . $args_str . $this->client_secret); //32位
            $args_md5 = substr($args_md5, 8, 16); //16位的MD5加密
        } else {
            //服务端暂时不支持下面签名方法
            $args_md5 = $this->hmac_md5($args_str, $this->client_secret);
        }
        return $args_md5;
    }

    /* 暂不可用 */

    private function hmac_md5($data, $key) {
        if (function_exists('hash_hmac')) {
            return hash_hmac('md5', $data, $key);
        }

        $key = (strlen($key) > 64) ? pack('H32', 'md5') : str_pad($key, 64, chr(0));
        $ipad = substr($key, 0, 64) ^ str_repeat(chr(0x36), 64);
        $opad = substr($key, 0, 64) ^ str_repeat(chr(0x5C), 64);
        return md5($opad . pack('H32', md5($ipad . $data)));
    }

    //获取指定用户组织资料
    public function simba_enterprise_buddy_info($token, $req_user_number, $enterprise_id, $user_number) {
        $args = array(
            'method' => 'simba.enterprise.buddy.info',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'reqUserNumber' => $req_user_number, //组织用户id
            'enterpriseId' => $enterprise_id, //组织ID
            'userNumber' => $user_number        //操作用户ID
        );
        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;
        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取组织用户资料(当前用户) */

    public function simba_enterprise_buddy_self($token) {
        $args = array(
            'method' => 'simba.enterprise.buddy.self',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
        );
        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;
        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取当前用户信息
     */

    public function simba_user_info($token) {
        //method=simba.user.info&session=123456&sign=2b97fec82bd83a4b&client_id=dedac868a6dd8805672ef5aa70855378&timestamp=123456
        $args = array(
            'method' => 'simba.user.info',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
        );
        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;
        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 修改个人信息
     */

    public function simba_user_edit($token, $edit_info) {
        $args = array(
            'method' => 'simba.user.edit',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
        );
        if (!$edit_info['userNumber']) {
            return false;
        } else {
            $args['userNumber'] = $edit_info['userNumber']; //用户号码,必填
        }
        if ($edit_info['nickName']) {
            $args['nickName'] = $edit_info['nickName']; //用户昵称
        }
        if ($edit_info['address']) {
            $args['address'] = $edit_info['address']; //地址
        }
        if ($edit_info['personalSignature']) {
            $args['personalSignature'] = $edit_info['personalSignature']; //个性签名
        }
        if ($edit_info['personalIntro']) {
            $args['personalIntro'] = $edit_info['personalIntro']; //个人简介
        }

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取组织成员数量 */

    public function simba_enterprise_counts($token) {
        $args = array(
            'method' => 'simba.enterprise.counts',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取用户公开信息
     * account : 账户<手机号、邮箱、用户号码>
     */

    public function simba_user_public($token, $account) {
        $args = array(
            'method' => 'simba.user.public',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'account' => $account, //必填
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 批量获取用户公开信息
     * $user_numbers用户simba号数组
     * param_json={"userNumbers":[10000010, 10000020]}
     */

    public function simba_user_public_batch($token, $user_numbers = array()) {
        if (!is_array($user_numbers)) {
            return;
        }
        $user_numbers_str = implode(',', $user_numbers);
        $param_json = '{"userNumbers":[' . $user_numbers_str . ']}';
        $args = array(
            'method' => 'simba.user.public.batch',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'param_json' => $param_json
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 消息通知
     * 
     */

    public function simba_business_notice_send($token, $msg = array()) {
        $args = array(
            'method' => 'simba.business.notice.send',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
        );
        $messageNotice = array(
            'bizTypeCode' => $this->config['type_code'], //业务编码，请咨询客服获取
            'templateCode' => $this->config['template_code'], //模版编码，请咨询客服获取
        );
        //必须参数
        $required_args = array(
            'subject', 'content', 'sender', 'receivers'
        );
        //可选参数
        $valid_args = array(
            'businessId', 'summary', 'enterId', 'imageUrl', 'attachNum', 'linkUrl',
            'pointLink', 'form', 'multiContent', 'rich', 'mobileLinkUrl'
        );
        foreach ($msg as $key => $val) {
            if (in_array($key, $required_args) || in_array($key, $valid_args)) {
                $messageNotice[$key] = $val;
                if (in_array($key, $required_args)) {
                    $k = array_search($key, $required_args);
                    array_splice($required_args, $k, 1);
                }
            }
        }
        if (!empty($required_args)) {
            $this->error_msg = "缺失必填参数！" . array2string($required_args);
            return false;
        }
        $param_json = array(
            'messageNotice' => $messageNotice
        );
        $param_json = json_encode($param_json);
        $args['param_json'] = $param_json;

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;
        //writelog('sendmsg', array2string($args));

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取用户群聊列表
     */

    public function simba_group_user_groups($token, $user_number, $version = 1) {
        $args = array(
            'method' => 'simba.group.user.groups',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'userNumber' => $user_number,
            'version' => $version
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取群聊信息 */

    public function simba_group_info($token, $user_number, $group_number) {
        $args = array(
            'method' => 'simba.group.info',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'userNumber' => $user_number,
            'groupNumber' => $group_number, //群聊号码
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 邀请成员加入群聊 */

    public function simba_group_member_invite($token, $user_number, $group_number, $invitation_user_number) {
        $args = array(
            'method' => 'simba.group.member.invite',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'userNumber' => $user_number, //用户号码
            'groupNumber' => $group_number, //群聊号码
            'invitationUserNumber' => $invitation_user_number, //被邀请人号码
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 移除群成员 */

    public function simba_group_member_remove($token, $user_number, $group_number, $remove_user_number) {
        $args = array(
            'method' => 'simba.group.member.remove',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'userNumber' => $user_number, //用户号码
            'groupNumber' => $group_number, //群聊号码
            'removeUserNumber' => $remove_user_number, //被邀请人号码
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取部门信息 */

    public function simba_enterprise_department_info($token, $dept_id, $enterprise_id) {
        $args = array(
            'method' => 'simba.enterprise.department.info',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'deptId' => $dept_id, //部门ID ，必填
            'enterpriseId' => $enterprise_id, //企业id ，必填
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 移除部门成员 */

    public function simba_enterprise_department_member_delete($token, $enterprise_id, $dept_id, $operate_user_number, $user_numbers) {
        if (!is_array($user_numbers)) {
            return false;
        }
        $param = array(
            'deptId' => $dept_id,
            'operateUserNumber' => $operate_user_number,
            'userNumbers' => $user_numbers
        );
        $param_json = json_encode($param);

        $args = array(
            'method' => 'simba.enterprise.department.member.delete',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'enterpriseId' => $enterprise_id, //企业id ，必填
            'deptId' => $dept_id, //部门ID，必填
            'operateUserNumber' => $operate_user_number, //当前操作人ID            
            'param_json' => $param_json //被移除人ID集合
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取组织信息 */

    public function simba_enterprise_info($token, $user_number, $enterprise_id) {
        $args = array(
            'method' => 'simba.enterprise.info',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'userNumber' => $user_number, //用户id
            'enterpriseId' => $enterprise_id, //企业id ，必填            
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 成员加入部门 */

    public function simba_enterprise_department_member_join($token, $enterprise_id, $dept_id, $user_numbers, $positions = array(), $sort_nos = array()) {
        if (!is_array($user_numbers)) {
            return false;
        }
        $param = array(
            'deptId' => $dept_id,
            'userNumbers' => $user_numbers,
            'enterpriseId' => $enterprise_id
        );
        if (!empty($positions)) {
            $param['positions'] = $positions;
        }
        if (!empty($sort_nos)) {
            $param['sortNos'] = $sort_nos;
        }
        $param_json = json_encode($param);

        $args = array(
            'method' => 'simba.enterprise.department.member.join',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'param_json' => $param_json,
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取部门成员列表 */

    public function simba_enterprise_department_member_list($token, $enterprise_id, $dept_id, $is_contain_child = 0) {
        $args = array(
            'method' => 'simba.enterprise.department.member.list',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'enterpriseId' => $enterprise_id,
            'deptId' => $dept_id,
        );
        if ($is_contain_child) {
            $args['isContainChild'] = $is_contain_child;
        }

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 删除部门 */

    public function simba_enterprise_department_delete($token, $enterprise_id, $dept_id, $operate_user_number) {
        $args = array(
            'method' => 'simba.enterprise.department.delete',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'enterpriseId' => $enterprise_id,
            'deptId' => $dept_id,
            'operateUserNumber' => $operate_user_number,
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 获取组织成员列表
     */

    public function simba_enterprise_buddy_page($token, $page = 1, $pagesize = 20) {
        $args = array(
            'method' => 'simba.enterprise.buddy.page',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
        );
        //$pagesize = $pagesize > 5000 ? 5000 : $pagesize; //最大返回记录数5000
        $args['currentPage'] = $page;
        $args['pageSize'] = $pagesize;
        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* 离职用户 */

    public function simba_enterprise_buddy_leave($token, $enterprise_id, $leave_user_number, $operate_user_number) {
        $args = array(
            'method' => 'simba.enterprise.buddy.leave',
            'client_id' => $this->client_id,
            'session' => 123456,
            "timestamp" => 123456,
            'enterpriseId' => $enterprise_id,
            'leaveUserNumber' => $leave_user_number,
            'operatorUserNumber' => $operate_user_number,
        );

        $sign = $this->getSign($args); //签名
        $args['sign'] = $sign;

        $url = $this->config['api_url'];
        $result = $this->curlPost($url, $args, $token);
        return $result;
    }

    /* curl模拟授权提交 
     */

    private function curlPost($url, $args, $token) {
        $headers = array(
            'Content-Type:application/x-www-form-urlencoded',
            "auth_token:$token"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_HEADER, true); //返回header信息
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        //$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        $result = curl_exec($ch);
        curl_close($ch);
        //记录日志
        if ($this->config['log'] == 1) {
            $this->log->write($url);
            $this->log->write($args);
            $this->log->write($result);
        }
        if ($result) {

            $result = json_decode($result, true);
            return $result;
        } else {
            $error_info = "开放平台接口无返回。 地址： $url ； 参数：" . json_encode($args);
            writelog('Simba_Open_Api', $error_info);
            return;
        }
    }

}
