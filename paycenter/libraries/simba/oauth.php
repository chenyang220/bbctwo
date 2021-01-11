<?php

/*
 * 应用授权入口： http://ip-address/oauth.php
 * 授权有效期说明：授权token有效期只有24小时。过期后需重新获取。
 * 注意： 回调地址需要与提交给simba方的一致
 */
header("content-type:text/html;charset=utf-8");         //设置编码
define('S_ROOT', dirname(__FILE__));        
define('CACHE_PATH',S_ROOT.'/data/');//文件缓存路径设置

$action = trim(!empty($_GET['action']) ? $_GET['action'] : '');
if (!empty($action)) {
    require_once 'src/Oauth.class.php';
    $oauth = new simba\oauth\Oauth();

    if ($action == 'oauth') {
        //构建授权地址，并跳转到授权页面    
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/simba/oauth.php?action=oauth_back'; //回调地址用于接收返回的参数
        $remote_url = $oauth->createOauthUrl($redirect_uri);
        @header("Location: " . $remote_url);
    } elseif ($action == 'oauth_back') {
        //用户登录授权后，回调这里地址，回传参数回来
        $code = trim($_GET['code']); //授权码
        $state = trim($_GET['state']); //回传标识
        $enterprise_id = $_GET['enterpriseId']; //企业id

        $result = $oauth->getToken($code);
        $user_id = intval($result['userId']);
        if (!empty($result) && $user_id) {
            /*保存到文件缓存*/
            require_once 'src/Utils/FileCache.class.php';
            $c = new simba\oauth\Utils\FileCache();
            $c->set('oauth', $result);
            echo '<b>授权已经写入文件/data/oauth_cache.php文件中，有效期一天！可开始尝试API调用</b><br/>';
        } else {
            echo 'Access Token获取失败!<br/>';
        }
        
        //print_r($result);
        exit;
    }
} else {
    echo '点击链接开始授权：';
    echo '<a href="/simba/oauth.php?action=oauth" title="点击开始授权">OAUTH</a>';
}
?>
