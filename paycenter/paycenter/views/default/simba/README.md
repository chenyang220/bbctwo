# simba-php-sdk
simba开放平台php版开发包

## 一、环境依赖
* PHP SDK 需要依赖 PHP 5及以上

## 二、SDK下载地址
* [https://gitee.com/songchen5287/simba-open-php-demo](https://gitee.com/songchen5287/simba-open-php-demo)

## 三、目录结构说明
* ***/data/*** <br>
  缓存文件和log文件， 需要设置为**可读写**
* ***/demo/*** <br>
  接口示例代码
* ***/src/*** <br>
  simba开放平台类库包，可独立迁移到其他系统使用。
* ***/index.php*** <br>
  功能总入口
* ***/oauth.php*** <br>
  通用授权入口
* ***/quick_oauth.php*** <br>
  快速授权入口

## 四、代码示例
### 基础配置 **/src/config.php**
* 配置文件中的相关服务IP地址需要根据自身的服务IP进行修改。<br>
  其中，client_id和client_secret请到开发者平台进行注册获取；模板消息的参数配置可通过向技术支持联系获取。
  <pre>
    // 应用标识
    'client_id' => 'f16e8ffce0dbc3ae49786ba914e96e61',
    // 应用私钥
    'client_secret' => 'bbfd7059d3b10b4ef840aecffc7e448c',
    // 接口请求地址
    'api_url' => 'http://114.115.200.40:8555/gateway',

    // 应用授权相关配置
    // 授权服务器
    'oauth_server_url' => 'http://114.115.200.40:8558/', 
    // oauth通用授权地址
    'action_oauth' => 'oauth/authorize?',
    // 获取、刷新令牌地址
    'action_token' => 'oauth/token?',
    // 快速授权地址
    'action_quick_oauth' => 'oauth/openAuthorizePersonal', 
    // 权限参数，目前支持参数值：read
    'scope' => 'read',
    // 授权类型，该值固定为code
    'response_type' => 'code',
    // 回调地址
    'redirect_uri' => 'http://127.0.0.1:8021/oauth.php?action=oauth_back', 
    // hashKey获取地址
    'hashkey_url' => 'http://114.115.200.40:9029/apidesk/portal/page/workbench/getRandomCode',

    // 模板消息参数
    'type_code' => 'work-journal',
    'template_code' => 'cloud-disk_general',

    // 是否开启日志，1：启用，其他：否
    'log' => 1,
    // 日志保存位置
    'log_destination' => '/data/access.log.php',
  </pre>

### 获取授权信息
* 1、通用授权入口 **/oauth.php** <br>
  先跳转至授权登录页面进行登录和授权，授权完成后会转跳到配置的回调地址上。
  <pre>
    header("content-type:text/html;charset=utf-8");         //设置编码
    require_once 'src/Oauth.class.php';
    $oauth = new simba\oauth\Oauth();
    $remote_url = $oauth->createOauthUrl('');
    @header("Location: " . $remote_url);
  </pre>
  然后再对回调地址的请求进行处理，通过GET方式获取的参数code去请求获取access_token。
  <pre>
    header("content-type:text/html;charset=utf-8");         //设置编码
    require_once 'src/Oauth.class.php';
    $oauth = new simba\oauth\Oauth();
    //用户登录授权后，回调这里地址，回传参数回来
    $code = trim($_GET['code']); //授权码
    $state = trim($_GET['state']); //回传标识
    $enterprise_id = $_GET['enterpriseId']; //企业id
    $result = $oauth->getToken($code);
  </pre>
  access_token有效期只有24小时。过期后需重新刷新获取。

* 2、快速授权入口 **/quick_oauth.php**
  快速授权的参数token和enterId是必传的，需要配置到simba客户端应用中才能获取到，hashKey的值需要通过调取新的服务去获取。
  <pre>
    header("content-type:text/html;charset=utf-8");         //设置编码
    //客户端回传参数：token,enterId
    $token = isset($_GET['token']) ? $_GET['token'] : ''; //令牌
    $enterprise_id = isset($_GET['enterId']) ? $_GET['enterId'] : ''; //企业id
    if (!$token || !$enterprise_id) {
        echo 'Arguments Error！';
        exit;
    }
    require_once 'src/QuickOauth.class.php';
    $quick_oauth = new simba\oauth\QuickOauth();
    $hashkey = $quick_oauth->getHashkey($token);  // 获取hashKey
    $result = $quick_oauth->getAccessToken($token, $enterprise_id, $hashkey);
  </pre>

### API调用示例
* 代码示例
  <pre>
    require_once 'src/Api.class.php';
    $access_token = 'test'; // 放入从授权入口或者快速授权入口获取到的access_token
    $apiObj = new simba\oauth\Api();
    $result = $apiObj->simba_user_info($access_token);
  </pre>

* 返回结果示例
  <pre>
    {
        "msg":"操作成功",
        "msgCode":200,
        "result":{
            "avatar":"https://file.isimba.cn/HeadImages/470000/66020765_20171115142107_a.png",
            "mobile":"xxxxxxxxxxx",
            "nickName":"test",
            "personalIntro":"测试文字1",
            "personalSignature":"测试文字2",
            "realName":"张三",
            "sex":1,
            "userNumber":66020765
        }
    }
  </pre>

* 具体的API调用可以通过访问/index.php中的例子链接进行查看。其中，<br>
/demo/user.class.php对应的是用户APi，<br>
/demo/department.class.php对应的是组织APi，<br>
/demo/group.class.php对应的是群聊API，<br>
/demo/notice.class.php对应的是消息API。

## 四、常见问题

## 五、License

Simba.pro All Rights Reserved.