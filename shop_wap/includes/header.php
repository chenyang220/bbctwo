<?php
// echo '<pre>';
// print_r($_COOKIE);die;

error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Origin:*');
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN']:'';
header("Access-Control-Allow-Origin:$origin");
define('PROJECT_PATH', "/test369/shop_wap");//网站目录
$project_path = PROJECT_PATH;
function menu_active($name)
{
    if ($name == '/index.html' && $_SERVER['REQUEST_URI'] == '/') {
        return true;
    }
    if (strpos($_SERVER['REQUEST_URI'], $name) !== false) {
        return true;
    }
    
    return false;
}

$host = '';
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
}
include __DIR__ . '/weixin_login.php';
$config_js = __DIR__ . "/../js/config_" . $_SERVER['SERVER_NAME'] . ".js";
if (file_exists($config_js)) {
    $_js_header = file_get_contents($config_js);
    $_js_header = '<script type="text/javascript">' . $_js_header . '</script>';
} else {
    include __DIR__ . '/../configs/config.php';
    ob_start();
    include __DIR__ . '/js.php';
    $_js_header = ob_get_contents();
    ob_clean();
}
$_js_header = str_replace('~', "", $_js_header);
$_js_header = str_replace('~', "", $_js_header);
include __DIR__ . '/../messages/I18N.php';
@include __DIR__ . '/../vendor/autoload.php';
ob_start();
//免登录鉴权
$token = isset($_GET['token']) ? $_GET['token'] : ''; //令牌
$enterprise_id = isset($_GET['enterId']) ? $_GET['enterId'] : ''; //企业id
if ($token && $enterprise_id != '') {
    include_once __DIR__ . '/../simba/src/QuickOauth.class.php';
    include_once __DIR__ .'/../simba/src/Api.class.php';
    $quick_oauth = new simba\oauth\QuickOauth();
    $hashkey = $quick_oauth->getHashkey($token);  // 获取hashKey
    $result = $quick_oauth->getAccessToken($token, $enterprise_id, $hashkey);
    $access_token = $result['access_token']; // 放入从授权入口或者快速授权入口获取到的access_token
    $apiObj = new simba\oauth\Api();
    $result = $apiObj->simba_user_info($access_token);
    $u_id = '';
    if($result['msgCode'] == 200){
       $u_id = $result['result']['userNumber'];
    }
}
if ($_GET['qr']) {
    setcookie('is_app_guest', 1, time() + 86400 * 366);
    $_COOKIE['is_app_guest'] = 1;
}
?>

