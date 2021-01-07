<?php

/*
 * 个人快速授权
 * 需要应用已经加入simba，由simba客户端发起
 */
header("content-type:text/html;charset=utf-8");         //设置编码
define('S_ROOT', dirname(__FILE__));
define('CACHE_PATH', S_ROOT . '/data/'); //文件缓存路径设置
////客户端回传参数：token,enterId,hashKey
//$token = isset($_GET['token'])?$_GET['token']:'';    //令牌
//$enterprise_id = isset($_GET['enterId'])?$_GET['enterId']:'';        //企业id
////$hashkey = isset($_GET['hashKey'])?$_GET['hashKey']:'';  //hash key
//if (!$token || $enterprise_id === '' ) {
//    echo 'Arguments Error！';
//    exit;
//}
//require_once 'src/QuickOauth.class.php';
//$quick_oauth = new simba\oauth\QuickOauth();
//$result = $quick_oauth->getAccessToken($token, $enterprise_id, $hashkey);
//if (!$result){
//    echo '授权失败';
//    print_r($result);
//    exit;
//}

//客户端回传参数：token,enterId
$token = isset($_GET['token']) ? $_GET['token'] : '';    //令牌
$enterprise_id = isset($_GET['enterId']) ? $_GET['enterId'] : '';        //企业id
if (!$token || $enterprise_id === '') {
    echo 'Arguments Error！';
    exit;
}

require_once 'src/QuickOauth.class.php';
$quick_oauth = new simba\oauth\QuickOauth();
$hashkey = $quick_oauth->getHashkey($token);
if (!$hashkey){
    echo  $quick_oauth->error_msg;
    exit;
}

$result = $quick_oauth->getAccessToken($token, $enterprise_id, $hashkey);
if (!$result){
    echo '授权失败';
    echo  '<br>' . $quick_oauth->error_msg;
    print_r($result);
    exit;
}



/*保存到文件缓存
 * 这里只是做一个示例。实际应用建议保存到数据库，每个用户对应一个授权。
 */
require_once 'src/Utils/FileCache.class.php';
$c = new simba\oauth\Utils\FileCache();
$c->set('quick_oauth', $result);
echo '<b>授权已经写入文件/data/quick_oauth_cache.php文件中，有效期一天！</b><br/>';

print_r($result);