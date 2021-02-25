<?php
//免登录鉴权
$token = isset($_GET['token']) ? $_GET['token'] : ''; //令牌
$enterprise_id = isset($_GET['enterId']) ? $_GET['enterId'] : ''; //企业id


if ($token && $enterprise_id != '') {
    include_once __DIR__ . '/../simba/src/QuickOauth.class.php';
    
    $quick_oauth = new simba\oauth\QuickOauth();

    $hashkey = $quick_oauth->getHashkey($token);  // 获取hashKey


    $result = $quick_oauth->getAccessToken($token, $enterprise_id, $hashkey);

    include_once __DIR__ .'/../simba/src/Api.class.php';
    $access_token = $result['access_token']; // 放入从授权入口或者快速授权入口获取到的access_token
    $apiObj = new simba\oauth\Api();
    $result = $apiObj->simba_user_info($access_token);
    $u_id = '';
    if($result['msgCode'] == 200){
       $u_id = $result['result']['userNumber'];
    }
}

      print_r($token);  
print_r($result);
// exit;
// if ($_GET['qr']) {
//     setcookie('is_app_guest', 1, time() + 86400 * 366);
//     $_COOKIE['is_app_guest'] = 1;
// }


?>


<!DOCTYPE html>  
 <html>  
 <head>  
 <meta charset="utf-8" />  
 <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />  
 <title>测试页面</title>  
 </head>  
 <body>  
 <p id="wrap">555</p >  
<script type="text/javascript" src="../js/ucsdk.min.js"></script>
 <script type="text/javascript">  
 // 配置SDK  
 UC.config = {  
 debug: true, // 开启调试模式  
 appId: '172d1c69e247207db52b54081255e450', // 必填  
 appSecurity: '896f886913826082f52a49a63aef4cff', // 必填  
 redirectUri: location.href // 鉴权后跳转地址（当前页面地址）, 注意域名必须和创建APP时候设置的域名保持一致  
 };  
 // 用于处理接口异常、接口不存在  
 UC.error = function (data) {  
 alert('客户端接口打开失败');  
 };  
 UC.ready = function () { 
 // testInterface2();  
 // testInterface3();  
 }  
   function testInterface2 () {  
 UC.call('setNavigationBarRight', '', function (data) {  
  document.getElementById('wrap').innerHTML = '成功2';  
 }, function (error) {  
  document.getElementById('wrap').innerHTML = '失败2';  
 });  
 }  
   function testInterface3 () {  
 UC.call('hideNavigationBarLeft', '', function (data) {  
  document.getElementById('wrap').innerHTML = '成功3';  
 }, function (error) {  
  document.getElementById('wrap').innerHTML = '失败3';  
 });  
 }  
 </script>  
 </body>  
 </html>  