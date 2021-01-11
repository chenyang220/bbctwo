<?php
header("content-type:text/html;charset=utf-8");         //设置编码

define('S_ROOT', dirname(__FILE__));//根目录
define('CACHE_PATH', S_ROOT . '/data/'); //文件缓存路径设置

require_once 'src/Utils/FileCache.class.php';
$c = new simba\oauth\Utils\FileCache();
$result = $c->get('oauth'); //时间过期后不会再获取到，默认一天
$result = json_decode($result, true);
$access_token = isset($result['oauth']['access_token'])?$result['oauth']['access_token']:'';
if (!$access_token){
    echo '未授权！请先点击链接开始<a href="/simba/oauth.php?action=oauth">授权</a>';
    exit;
}

$mod = isset($_GET['m'])?$_GET['m']:'';
$controller = isset($_GET['c'])?$_GET['c']:'';
$mod_arr = array(
    'user',//用户
    'group',//群聊
    'department',//组织
    'notice'//通知
);
if (!$mod || !in_array($mod, $mod_arr)){
    echo '参数错误： 请提交m和c参数';
    exit;
}

require_once 'demo/'.$mod.'.class.php';
$mod_class = new $mod($access_token);
if(!method_exists($mod_class, $controller)){
    echo '未定义'.$controller.'方法！';
    exit;
}
$mod_class->$controller();
exit;