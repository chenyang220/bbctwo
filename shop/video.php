<?php
 require_once 'shop/configs/shop_api.ini.php';
 header('Access-Control-Allow-Origin: *');
 header('Access-Control-Allow-Methods:POST,GET,HEAD');
 // 带 cookie 的跨域访问
 header('Access-Control-Allow-Credentials: true');
 // 响应头设置
 header('Access-Control-Allow-Headers:x-requested-with,Content-Type,X-CSRF-Token');
 $file = $_SERVER['PATH_INFO'];
 echo $shop_api_url.  $file;exit;
?>
