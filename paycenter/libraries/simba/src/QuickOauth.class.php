<?php

namespace simba\oauth;

/*
 * simba  个人快速授权
 */

class QuickOauth {

    private $config;
    public $error_msg;

    public function __construct($config = array()) {
        if (empty($config)) {
            $this->config = require_once __DIR__ .'/config.php';
        } else {
            $this->config = $config;
        }

        if (!isset($this->config) || !is_array($this->config)) {

            echo 'Please config Oauth Arguments';
            exit;
        }
    }

    /* curl模拟授权提交 
     * 返回数组
     */

    private function curlPost($url, $args, $auth = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($auth) {
            //header basic auth
            $client_id = $this->config['client_id'];
            $client_secret = $this->config['client_secret'];
            //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);            
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        //$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result) {
            return json_decode($result, true);
        }
        return false;
    }

    /* 个人快速授权第一步，返回code */

    private function getCode($token, $enterprise_id, $hashkey) {
//        $token = 'a0fj9ppm4u611i6f';
////        $enterprise_id=0;
////        $hashkey ="6zbOM0vbkcuLgF7j";
        //http://139.199.102.52:8015/open-platform-oauth-server/oauth/openAuthorizePersonal?token=a0f1hdgc43k8b1qw&hashkey=67F5B9C6143353D7A33F5FD0418E92D1&clientId=0f4dfd502174eac46887bb15989898d5&enterpriseId=235604
        //http://123.206.25.74:10689/oauth/openAuthorizePersonal?token=a0ey0r42nzrs68qh&hashkey=SbmUDLmRJya8n2KI&clientId=e226e13f1443d703f445dccbac29bda9&enterpriseId=235604
        $client_id = $this->config['client_id'];
        $args = array(
            'token' => $token,
            'hashkey' => $hashkey,
            'clientId' => $this->config['client_id'],
            'enterpriseId' => $enterprise_id
        );
        $url = $this->config['oauth_server_url'] . $this->config['action_quick_oauth'];
        $result = $this->curlPost($url, $args);
        if (!empty($result) && $result['msgCode'] == 200) {
            $code = $result['result'];
            return $code;
        } else {
            return $this->error("Code获取失败", $result);
        }
        return false;
    }

    /* 个人快速收取，返回access_token */

    public function getAccessToken($token, $enterprise_id, $hashkey) {
        //http://139.199.102.52:8015/open-platform-oauth-server/oauth/token?code=a0f1hdgc43kz5cpa&grant_type=authorization_code&redirect_uri=http://127.0.0.1/test
        if (!$token || $enterprise_id==='' || ! $hashkey) {
            return $this->error("参数缺失,请检查： token，enterprise_id，hashkey");
        }
        $code = $this->getCode($token, $enterprise_id, $hashkey);
        if (!$code) {
            return;
        }

        $config_simba_oauth = $this->config;
        $token_url = $config_simba_oauth['oauth_server_url'] . $config_simba_oauth['action_token'];
        $args = array(
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $config_simba_oauth['redirect_uri']
        );

        $result = $this->curlPost($token_url, $args, true);

        if (!empty($result) && $result['access_token']) {
            return $result;
        } else {
            return $this->error("Access Token获取失败", $result);
        }
        return false;
    }

    /* 错误信息 */

    public function error($msg = '', $result = array()) {
        if (!empty($result)) {
            $this->error_msg = $msg . ' msgCode:' . $result['msgCode'] . ' msg:' . $result['msg'];
        } else {
            $this->error_msg = $msg ? $msg : '执行出错';
        }
        return;
    }


    /* 快速授权，返回hashKey */
    public function getHashkey($token) {
        if (!token) {
            return $this->error("token参数不能为空");
        }
        $url = $this->config['hashkey_url'];
        $result = $this->curlGet($url, array('Authorization: ' . $token));
        if (!empty($result) && $result['msgCode'] == 200) {
            $hashKey = $result['result'];
            return $hashKey;
        } else {
            return $this->error("hashKey获取失败", $result);
        }
        return false;
    }


    public function curlGet($url, $headers = array()){
        // 初始化
        $curl = curl_init();
        // 设置url路径
        curl_setopt($curl, CURLOPT_URL, $url);
        // 将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true) ;
        // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true) ;
        // 添加头信息
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // CURLINFO_HEADER_OUT选项可以拿到请求头信息
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        // 不验证SSL
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        // 执行
        $data = curl_exec($curl);
        // 关闭连接
        curl_close($curl);
        // 返回数据
        return json_decode($data, true);
    }



}
