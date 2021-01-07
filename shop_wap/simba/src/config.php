<?php

/*
 * simba开放平台 配置参数
 */
$_config = array(
    /* 应用参数 */
    'client_id' => '172d1c69e247207db52b54081255e450', //应用标识
    'client_secret' => '896f886913826082f52a49a63aef4cff', // 应用私钥
    'api_url' => 'http://117.145.30.131:8555/gateway', //接口请求地址.
    'hashkey_url' => 'http://117.145.30.131:9029/apidesk/portal/page/workbench/getRandomCode',
    /* 应用授权 */
    'oauth_server_url' => 'http://117.145.30.131:8558/', //授权服务器
    'action_oauth' => 'oauth/authorize?', //oauth授权地址
    'action_token' => 'oauth/token?', //获取、刷新令牌地址
    'action_quick_oauth' => 'oauth/openAuthorizePersonal', //快速授权
    'scope' => 'read',
    'response_type' => 'code',
    'redirect_uri' => 'http://zwap.yuanfengshop.com?token=#TOKEN&enterId=#ENTERID&hashkey=#HASHKEY', //回调地址
    /* 模板消息参数 */
    'type_code' => 'cloud-disk',
    'template_code' => 'cloud-disk_general',
    'log' => 1, //是否开启日志，1：启用，其他：否
    'log_destination' => '/data/access.log.php', //日志保存位置
);
return $_config;
