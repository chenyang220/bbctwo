<?php

namespace simba\oauth;

/*
 * simba oauth授权
 */

class Oauth {

    private $config;

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

    /* oauth授权地址生成 
     * state参数会回传回来，用于做识别标志。
     */

    public function createOauthUrl($redirect_uri = '', $state = '') {
        $config_simba_oauth = $this->config;
        $redirect_uri = !empty($redirect_uri) ? $redirect_uri : $config_simba_oauth['redirect_uri'];
        $state = !empty($state) ? $state : 'abcd1234';
        $remote_url = $config_simba_oauth['oauth_server_url'] . $config_simba_oauth['action_oauth']
                . 'client_id=' . $config_simba_oauth['client_id']
                . '&scope=' . $config_simba_oauth['scope']
                . '&response_type=' . $config_simba_oauth['response_type']
                . '&state=' . $state
                . '&redirect_uri=' . $redirect_uri;
        return $remote_url;
    }

    /* 获取令牌
     * $code: 用户授权后，回调地址返回
     */

    public function getToken($code) {
        if (!$code) {
            return false;
        }
        $config_simba_oauth = $this->config;
        $token_url = $config_simba_oauth['oauth_server_url'] . $config_simba_oauth['action_token'];
        $args = array(
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $config_simba_oauth['redirect_uri']
        );

        $result = $this->curlPost($token_url, $args);
        if ($result) {
            return json_decode($result, true);
        }
        return false;
    }

    /* 刷新令牌
     * 根据时间判断是否过期，
     * 未过期： 刷新令牌，
     * 已经过期：则要求重新授权
     * http://123.206.25.74:10689/oauth/token?grant_type=refresh_token&refresh_token=${refresh_token}
     */

    public function refreshToken($refresh_token) {
        $config_simba_oauth = $this->config;
        $token_url = $config_simba_oauth['oauth_server_url'] . $config_simba_oauth['action_token'];
        $args = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token
        );
        $result = $this->curlPost($token_url, $args);
        if ($result) {
            return json_decode($result, true);
        }
        return false;
    }

    /* curl模拟授权提交 
     */

    private function curlPost($url, $args) {
        $client_id = $this->config['client_id'];
        $client_secret = $this->config['client_secret'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
