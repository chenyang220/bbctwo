<?php
include __DIR__ . '/route.ini.php';
/**
 * main config
 *
 *
 * @category   Config
 * @package    Config
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010远丰仁商
 * @version    1.0
 * @todo
 */
if (!isset($_COOKIE['guest_id']) || !$_COOKIE['guest_id']) {
    $t = time();
    setcookie('guest_id', 'g_' . $t, $t + 365 * 86400);
    $_COOKIE['guest_id'] = 'g_' . $t;
}
//定义系统路径''
if (!defined('ROOT_PATH')) {
    define('APP_DIR_NAME', 'shop');
    define('ROOT_PATH', substr(str_replace('\\', '/', dirname(__FILE__)), 0, -13));
    define('LIB_PATH', ROOT_PATH . '/libraries');   //ZeroPHP Framework 所在目录
    define('APP_PATH', ROOT_PATH . '/' . APP_DIR_NAME);         //应用程序目录
    define('MOD_PATH', APP_PATH . '/models');       //应用程序模型目录
    $themes_name = 'default';
    $pro_path = '';
    /**
     * 风格静态文件文件目录，此处变量名称$themes勿修改
     *
     * @var string
     */
    if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT']) {
        $pro_path_row = explode($_SERVER['DOCUMENT_ROOT'], ROOT_PATH);
        if (isset($pro_path_row[1])) {
            $pro_path = '/' . ltrim($pro_path_row[1], '/');
            $themes = $pro_path . '/' . APP_DIR_NAME . '/static/' . $themes_name;
        } else {
            $themes = '/' . APP_DIR_NAME . '/static/' . $themes_name;
        }
    } else {
        $themes = '/' . APP_DIR_NAME . '/static/' . $themes_name;
    }
    define('TPL_DEFAULT_PATH', APP_PATH . '/views/default');  //应用程序默认视图目录
    define('TPL_PATH', APP_PATH . '/views/' . $themes_name);
    define('CTL_PATH', APP_PATH . '/controllers');
    define('INI_PATH', APP_PATH . '/configs');
    define('HLP_PATH', APP_PATH . '/helpers');
    define('LOG_PATH', APP_PATH . '/data/logs');
    define('LAN_PATH', ROOT_PATH . '/messages');
    define('DATA_PATH', APP_PATH . '/data');
    //是否开启runtime，如果为false，则不生成runtime缓存
    define('RUNTIME', false);
    //是否开启debug，如果为true，则不生成runtime缓存
    define('DEBUG', false);
    global $import_file_row;
    if (!isset($import_file_row)) {
        $import_file_row = [];
    }
    //公用函数库
    require_once LIB_PATH . '/__init__.php';
}
define('CODE_TEMPLATE_PATH', ROOT_PATH . '/build_tools/code_template');
define("CONTROLLER_CLASS_NAME", 'Game_'); //控制器class前缀
define("MODEL_CLASS_NAME", ''); //模型class前缀
define('REDIS_AUTO_ID', 'auto_id:');
define('FIGHT_REPORT_PATH', APP_PATH . '/data/fight_report');
$version_file = ROOT_PATH . "/pack/version.php";
if (is_file($version_file)) {
    $version = @include $version_file;
} else {
    $version = ['version' => '3.0'];
}
define('SHOP_VERSION', $version['version']);
if (!function_exists('is_local')) {
    /**
     * 是否是本机访问
     *
     * @return  boolean
     * @weichat sunkangchina
     * @date    2017
     */
    function is_local()
    {
         
        if (in_array(ip(), ['127.0.0.1', '::1', '0.0.0.0'])) {
            return true;
        }
        
        return false;
    }
}
/**
 * 获取客户端IP地址
 *
 * @param   返回类型 0 返回IP地址 1 返回IPV4地址数字      $type
 *
 * @return  [type]
 * @weichat sunkangchina
 * @date    2017-04-07
 */
if (!function_exists('ip')) {
    function ip($type = 0)
    {
        $type = $type ? 1:0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? [$ip, $long]:['0.0.0.0', 0];
        
        return $ip[$type];
    }
}
if (is_local() === true && $_GET['debug']==1) {
	ini_set('display_errors', 1);
	error_reporting(E_ALL & ~(E_STRICT | E_NOTICE));
	//记录错误提示
	ini_set('log_errors', 1);
	ini_set('error_log', APP_PATH . '/data/logs/debug.log');

} else {
	ini_set('display_errors', 0);
	error_reporting(0);
}
//设置时区
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set(isset($time_zone_id) && $time_zone_id ? $time_zone_id:'Asia/Shanghai');
    //date_default_timezone_set('UTC');
}
//插件启动
$plugin_rows = [];
include_once 'plugin.ini.php';
//$plugin_rows   = get_active_plugins();
//必须开启
if (!array_key_exists('Plugin_Perm', $plugin_rows)) {
    $plugin_rows['Plugin_Perm'] = [
        'name' => 'Plugin_Perm'
    ];
}
$PluginManager = Yf_Plugin_Manager::getInstance($plugin_rows);
$PluginManager->trigger('init', '');
Yf_Registry::set('hook', $PluginManager);
define('LANG', 'zh_CN');
if ('cli' != SAPI) {
    set_time_limit(60); //运行时间限制一定要有的。 切记！
    //是否压缩输出
    $gzipcompress = 0;
    if ($gzipcompress && function_exists('ob_gzhandler')) {
        ob_start('ob_gzhandler');
    } else {
        $gzipcompress = 0;
        ob_start();
    }
    Yf_Registry::set('gzipcompress', $gzipcompress);
    if (function_exists('session_cache_limiter')) {
        session_cache_limiter('private, must-revalidate');
    }
    header('Content-type: text/html; charset=UTF-8');
    //强制过期，ajax请求不需要要加随机字符串
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
    header('Pragma: no-cache'); // HTTP/1.0
    header('P3P: CP=CAO PSA OUR');  //ie iframe cookie
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Headers: Range,X-Requested-With,X_Requested_With'); //设置允许的跨域header
    // other CORS headers if any...
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit; // finish preflight CORS requests here
    }
}
$host = '';
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
}
/**
 * 判断http https
 *
 * @return  http或https
 * @weichat sunkangchina
 * @date    2017
 */
function get_http_s()
{
    
    $top = 'http';
    if ($_SERVER['SERVER_PORT'] == 443 || $_SERVER['HTTPS'] == 1 || $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTP_X_CLIENT_SCHEME'] == 'https') {
        $top = 'https';
    } 
    if($_SERVER['HTTP_VIA'] && 
        strpos(strtolower($_SERVER['HTTP_VIA']),'baidu-cdn-')!==false){
        $top = 'https';
    } 
    return $top;
}

Yf_Registry::set('base_url', get_http_s() . '://' . $host . $pro_path);
//兼容非域名部署的情况,修复 min css js不工作的问题。
Yf_Registry::set('root_uri',  $pro_path);
 

Yf_Registry::set('index_page', 'index.php');
Yf_Registry::set('url', Yf_Registry::get('base_url') . '/' . Yf_Registry::get('index_page'));
Yf_Registry::set('static_url', get_http_s() . '://' . $host . $themes);
//加载静态文件配置
if (is_file(INI_PATH . '/static_' . $server_id . '.ini.php')) {
    $static_conf = include_once INI_PATH . '/static_' . $server_id . '.ini.php';
} else {
    $static_conf = include_once INI_PATH . '/static.ini.php';
}
$static_default_host = $host . $themes;
setStaticUrl($static_conf, $static_default_host, $themes_name);
function setStaticUrl($static_conf, $default_host, $themes_name)
{
    $protocol = get_http_s();
    $common_host = str_replace('/' . APP_DIR_NAME . '/static/' . $themes_name, '/' . APP_DIR_NAME . '/static/common', $default_host);
    $static_conf['static_url'] = $static_conf['static_url'] ? $static_conf['static_url']:$default_host;
    $static_conf['static_images'] = $static_conf['static_images'] ? $static_conf['static_images']:$default_host . '/images';
    $static_conf['static_css'] = $static_conf['static_css'] ? $static_conf['static_css']:$default_host . '/css';
    $static_conf['static_js'] = $static_conf['static_js'] ? $static_conf['static_js']:$default_host . '/js';
    $static_conf['static_com_url'] = $static_conf['static_com_url'] ? $static_conf['static_com_url']:$common_host;
    $static_conf['static_com_images'] = $static_conf['static_com_images'] ? $static_conf['static_com_images']:$common_host . '/images';
    $static_conf['static_com_css'] = $static_conf['static_com_css'] ? $static_conf['static_com_css']:$common_host . '/css';
    $static_conf['static_com_js'] = $static_conf['static_com_js'] ? $static_conf['static_com_js']:$common_host . '/js';
    foreach ($static_conf as $conf_key => $conf_value) {
        Yf_Registry::set($conf_key, $protocol . '://' . $conf_value);
    }
}
//设置扩展前端文件路径部分
$extends_static_path =  get_http_s() . '://' .$host.'/' .EXTENDS_DIR_NAME . '/static' ;
Yf_Registry::set('static_ext_url', $extends_static_path);
Yf_Registry::set('static_ext_css', $extends_static_path."/css");
Yf_Registry::set('static_ext_js', $extends_static_path."/js");
Yf_Registry::set('static_ext_img', $extends_static_path."/css");
Yf_Registry::set('static_ext_public', $extends_static_path."/public");

//if (isset($pro_path_row[1]))
//系统类型bbc, shop, drp
if (false !== strpos($host, 't1.shop') || false !== strpos($host, 'lingshou.')) {
    define('SYS_TYPE', 'shop');
} elseif (false !== strpos($host, 't40.shop')) {
    define('SYS_TYPE', 'bbc');
} else {
    define('SYS_TYPE', 'bbc');
}
/*
应用cache, 如果使用cache，配置格式必须严格按照如下格式
0|false:none
1|true:file/memcache
*/
define('CHE', 1);
if (true) {
    //cache配置参数
    //require_once INI_PATH . '/cache.ini.php';
    $config_cache['memcache']['base'] = [
        [
            '139.196.6.92' => '11211'
        ],
    ];
    $config_cache['memcache']['data'] = [
        [
            '139.196.6.92' => '11211'
        ],
    ];
}
//设置cache 参数
//cacheType 1:file  2:memcache   3：redis  4:apc   5:eA  6:xcache
$config_cache['default'] = [
    'cacheType' => 1,
    'cacheDir' => APP_PATH . '/data/cache/default_cache/',
    'memoryCaching' => false,
    'automaticSerialization' => true,
    'hashedDirectoryLevel' => 2,
    'lifeTime' => 86400
];
$config_cache['base'] = [
    'cacheType' => 1,
    'cacheDir' => APP_PATH . '/data/cache/base_cache/',
    'memoryCaching' => false,
    'automaticSerialization' => true,
    'hashedDirectoryLevel' => 2,
    'lifeTime' => 86400
];
$config_cache['data'] = [
    'cacheType' => 1,
    'cacheDir' => APP_PATH . '/data/cache/data_cache/',
    'memoryCaching' => false,
    'automaticSerialization' => true,
    'hashedDirectoryLevel' => 2,
    'lifeTime' => 86400
];
$goods_cache = APP_PATH . '/data/cache/goods_cache/';
if (!is_dir($goods_cache)) {
    mkdir($goods_cache, 0777, true) or die($goods_cache . " created failed!");
}
$config_cache['goods_cache'] = [
    'cacheType' => 1,
    'cacheDir' => $goods_cache,
    'memoryCaching' => false,
    'automaticSerialization' => true,
    'hashedDirectoryLevel' => 2,
    'lifeTime' => 600
];
$config_cache['verify_code'] = [
    'cacheType' => 1,
    'cacheDir' => APP_PATH . '/data/cache/verify_code_cache/',
    'memoryCaching' => false,
    'automaticSerialization' => true,
    'hashedDirectoryLevel' => 2,
    'lifeTime' => 300
];
Yf_Registry::set('config_cache', $config_cache);
//包含Db配置文件，如果使用DB，配置格式必须严格按照如下格式
//require_once ROOT_PATH . '/../../config/config.inc.php';
define('DB_DRIVE', 'Yf_Db_Pdo');
define('DB_DEBUG', true);
//不同的平台,必须有独立域名,因为用户可能是公用的.
//根据域名,决定访问的平台不同,而不是通过server_id
//md5(域名), server_id
$server_id = md5($host);
Yf_Registry::set('server_id', $server_id);
if (is_file(INI_PATH . '/db_' . $server_id . '.ini.php')) {
    $db_row = include_once INI_PATH . '/db_' . $server_id . '.ini.php';
} else {
    $db_row = include_once INI_PATH . '/db.ini.php';
}
if (!is_array($db_row)) {
    $db_row = [];
}
$db_config_row = $db_row;
$config['db_cfg_rows'] = [
    'master' => [
        'shop' => [
            $db_row
        ],
        'root_rights' => [
            [
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'test',
                'password' => 'test',
                'database' => 'mysql',
                'charset' => 'UTF8'
            ]
        ]
    ]
];
//通过这儿设置默认Db， 目前从主库读取数据，示例如下
$config['db_write_read'] = 'master';
//如果需要从slave库中读取， 需要设置如下：$db_cfg_rows['default'] = $db_cfg_rows['slave'];
Yf_Registry::set('db_cfg', $config);
if (Yf_Registry::get('magic_quotes_gpc')) {
    $_POST = unquotes($_POST);
    $_GET = unquotes($_GET);
    $_REQUEST = unquotes($_REQUEST);
    $_COOKIE = unquotes($_COOKIE);
    $_FILES = unquotes($_FILES);
}
//初始化参数
//require_once(INI_PATH . '/init.php');
//判断类型，转换
$int_row = [
    'int8_t',
    'int16_t',
    'int32_t',
    'int64_t',
    'uint8_t',
    'uint16_t',
    'uint32_t',
    'uint64_t'
];
$float_row = [
    'float'
];
Yf_Registry::set('int_row', $int_row);
Yf_Registry::set('float_row', $float_row);
if (isset($ccmd_rows)) {
    Yf_Registry::set('ccmd_rows', $ccmd_rows);
}
Yf_Registry::set('error_url', false);
$sms_config = include_once 'sms.ini.php';
// var_export($wx_config); die;
//用户中心配置
if (is_file(INI_PATH . '/ucenter_api_' . $server_id . '.ini.php')) {
    $db_row = include_once INI_PATH . '/ucenter_api_' . $server_id . '.ini.php';
} else {
    $db_row = include_once INI_PATH . '/ucenter_api.ini.php';
}
Yf_Registry::set('ucenter_api_key', $ucenter_api_key);
Yf_Registry::set('ucenter_app_id', $ucenter_app_id);
Yf_Registry::set('ucenter_api_url', $ucenter_api_url);
//支付中心配置

if (is_file(INI_PATH . '/paycenter_api_' . $server_id . '.ini.php')) {
    $db_row = include_once INI_PATH . '/paycenter_api_' . $server_id . '.ini.php';
} else {
    $db_row = include_once INI_PATH . '/paycenter_api.ini.php';
}
Yf_Registry::set('paycenter_api_key', $paycenter_api_key);
Yf_Registry::set('paycenter_app_id', $paycenter_app_id);
Yf_Registry::set('paycenter_api_url', $paycenter_api_url);
Yf_Registry::set('paycenter_api_name', @$paycenter_api_name ? $paycenter_api_name:__('网付宝'));
//SHOP配置
if (is_file(INI_PATH . '/shop_api_' . $server_id . '.ini.php')) {
    include_once INI_PATH . '/shop_api_' . $server_id . '.ini.php';
} else {
    include_once INI_PATH . '/shop_api.ini.php';
}
Yf_Registry::set('shop_api_key', $shop_api_key);
Yf_Registry::set('shop_api_url', $shop_api_url);
Yf_Registry::set('shop_app_id', $shop_app_id);
Yf_Registry::set('shop_wap_url', @$shop_wap_url);
//SUPPLIER配置
if (is_file(INI_PATH . '/b2b_api_' . $server_id . '.ini.php')) {
    include_once INI_PATH . '/b2b_api_' . $server_id . '.ini.php';
} else {
    include_once INI_PATH . '/b2b_api.ini.php';
}
//cndimageurl配置
if (is_file(INI_PATH . '/cdn_image_urls' . $server_id . '.ini.php')) {
    include_once INI_PATH . '/cdn_image_urls' . $server_id . '.ini.php';
} else {
    include_once INI_PATH . '/cdn_image_urls.ini.php';
}
Yf_Registry::set('cdn_image_urls',$cdn_image_urls);
Yf_Registry::set('supplier_api_key', $supplier_api_key);
Yf_Registry::set('supplier_api_url', $supplier_api_url);
Yf_Registry::set('supplier_app_id', $supplier_app_id);
Yf_Registry::set('supplier_is_open', $supplier_is_open);
//IM配置
if (is_file(INI_PATH . '/im_api_' . $server_id . '.ini.php')) {
    include_once INI_PATH . '/im_api_' . $server_id . '.ini.php';
} else {
    include_once INI_PATH . '/im_api.ini.php';
}
Yf_Registry::set('im_api_key', $im_api_key);
Yf_Registry::set('im_api_url', $im_api_url);
Yf_Registry::set('im_url', $im_url);
Yf_Registry::set('im_app_id', $im_app_id);
Yf_Registry::set('im_statu', $im_statu);
Yf_Registry::set('im_admin_api_url', $im_admin_api_url);

//ERP配置
if (is_file(INI_PATH . '/erp_api_' . $server_id . '.ini.php')) {
    include_once INI_PATH . '/erp_api_' . $server_id . '.ini.php';
} else {
    include_once INI_PATH . '/erp_api.ini.php';
}
Yf_Registry::set('erp_api_url', $erp_api_url);

//sns链接
Yf_Registry::set('sns_api_url', $sns_api_url);
//数据分析配置
if (is_file(INI_PATH . '/analytics_api_' . $server_id . '.ini.php')) {
    include_once INI_PATH . '/analytics_api_' . $server_id . '.ini.php';
} else {
    include_once INI_PATH . '/analytics_api.ini.php';
}

//店铺基本信息配置
if (is_file(INI_PATH . '/store.ini.php')) {
    include_once INI_PATH . '/store.ini.php';
    Yf_Registry::set('storeDomain', $store_domain);
}

Yf_Registry::set('analytics_api_key', $analytics_api_key);
Yf_Registry::set('analytics_api_url', $analytics_api_url);
Yf_Registry::set('analytics_app_id', $analytics_app_id);
Yf_Registry::set('analytics_app_name', $analytics_app_name);
Yf_Registry::set('analytics_statu', @$analytics_statu);
//设置未付款订单的取消时间(秒)24小时
Yf_Registry::set('wait_pay_time', 86400);
//设置系统自动确认收货的时间(秒)7天
Yf_Registry::set('confirm_order_time', 604800);
//加载扩展禁用配置文件 @nsy 2019-02-21
$extends_ini_file =  INI_PATH . '/extends.ini.php';
if(is_file($extends_ini_file)){
    $extends_ini = include_once  $extends_ini_file;
}
!$extends_ini && $extends_ini = array();
$GLOBALS['extends_ini'] =$extends_ini;//Yf_Registry::set('extends_ini',$extends_ini );

//加载登录过滤扩展控制器
$extends_noperm_arr = array();
$extends_no_perm_file =  EXTENDS_PATH . '/includes/noPerm.ini.php';
if(is_file($extends_no_perm_file)){
    $extends_noperm_arr = include_once  $extends_no_perm_file;
}
$GLOBALS['extends_ini'] = $extends_noperm_arr;//Yf_Registry::set('extends_noperm_arr',$extends_noperm_arr );

//加载公共配置
if (is_file(INI_PATH . '/common_api_' . $server_id . '.ini.php')) {
    $common_conf = include_once INI_PATH . '/common_api_' . $server_id . '.ini.php';
} else {
    $common_conf = include_once INI_PATH . '/common_api.ini.php';
}
if (is_array($common_conf)) {
    foreach ($common_conf as $conf_key => $conf_value) {
        Yf_Registry::set($conf_key, $conf_value);
    }
}
include APP_PATH . '/../messages/I18N.php';
if (!isset($_COOKIE['area'])) {
    $ip = get_ip();
    //$area = getIPLoc_sina($ip);
    //setcookie("area",$area);
}
//获取顶级域名
function get_top_domain($domain)
{
    $suffix = [
        'ac', 'ad', 'ae', 'aero', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'arpa', 'as', 'asia', 'at', 'au', 'aw', 'ax', 'az', 'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'biz', 'bj', 'bl', 'bm', 'bn', 'bo', 'bq', 'br', 'bs', 'bt', 'bv', 'bw', 'by', 'bz', 'ca', 'cat', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'com', 'coop', 'cr', 'cu', 'cv', 'cw', 'cx', 'cy', 'cz', 'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'edu', 'ee', 'eg', 'eh', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo', 'fr', 'ga', 'gb', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gov', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw', 'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'info', 'int', 'io', 'iq', 'ir', 'is', 'it', 'je', 'jm', 'jo', 'jobs', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kp', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk', 'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'me', 'mf', 'mg', 'mh', 'mil', 'mk', 'ml', 'mm', 'mn', 'mo', 'mobi', 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'museum', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'name', 'nc', 'ne', 'net', 'nf', 'ng', 'ni', 'nl', 'no', 'np', 'nr', 'nu', 'nz', 'om', 'org', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pm', 'pn', 'pr', 'pro', 'ps', 'pt', 'pw', 'py', 'qa', 're', 'ro', 'rs', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sj', 'sk', 'sl', 'sm', 'sn', 'so', 'sr', 'ss', 'st', 'su', 'sv', 'sx', 'sy', 'sz', 'tc', 'td', 'tel', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tp', 'tr', 'travel', 'tt', 'tv', 'tw', 'tz', 'ua', 'ug', 'uk', 'um', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'xxx', 'ye', 'yt', 'za', 'zm', 'zw'
    ];
    $domain_arr = explode('.', $domain);
    $domain_sub1 = array_pop($domain_arr);
    $domain_sub2 = array_pop($domain_arr);
    $top_domain = in_array($domain_sub2, $suffix) ? array_pop($domain_arr) . '.' . $domain_sub2 . '.' . $domain_sub1:$domain_sub2 . '.' . $domain_sub1;
    
    return $top_domain;
}
function cdn_image_url($img, $width = 64, $height = 64){
    return CDN::img($img, $width , $height);
}
function cdn_content_url($body){ 
    return CDN::content($body);
}
function cdn_url($url){
    return CDN::url($url);
}
$top_domain = get_top_domain($_SERVER['HTTP_HOST']);

define('TOP_DOMAIN', $top_domain);
define('TOP_DOMAIN_SHOP', $_SERVER['HTTP_HOST']);
//wap端分享+分销
if ((request_int('uuid'))) {
    $user_id = request_int('uuid');
    setcookie('uu_id', $user_id, time() + 60 * 60 * 24 * 3, '/', TOP_DOMAIN);
}
//是否开启rewrite及规则
$config_rewrite['open'] = 0;
$config_rewrite['mod'] = 1;
$rewrite_search[] = "/index\.php\?ctl=(\w+)&met=(\w+)/";
/*$rewrite_search[] = "/index\.php\?ctl=Index&met=category&category_id=(.*)&page=(\w+)/";
$rewrite_search[] = "/index\.php\?ctl=Index&met=category&category_id=(\.*)/";
$rewrite_search[] = "/index\.php\?ctl=Article_Base&met=get&article_id=(\.*)/";
$rewrite_search[] = "/index\.php\?ctl=User_Base&met=index&user_id=(\w+)/";*/
//$rewrite_replace[] = "\\1/\\2.html";
$rewrite_replace[] = "index/\\1/\\2";
/*$rewrite_replace[] = "cat/\\1/\\2";
$rewrite_replace[] = "cat/\\1";
$rewrite_replace[] = "news/\\1";
$rewrite_replace[] = "author/\\1";*/
$config_rewrite[1]['search'] = $rewrite_search;
$config_rewrite[1]['replace'] = $rewrite_replace;
Yf_Registry::set('config_rewrite', $config_rewrite);
Yf_Registry::set('error_url', Yf_Registry::get('base_url') . '/error.php');
if (request_string('typ') != 'json' && !request_string('redirect') && Yf_Utils_Device::isMobile() && request_string('ctl') != 'Qr') {

    $site_status = Web_ConfigModel::value("site_status_wap");
    if(!$site_status)
    {
        $msg = Web_ConfigModel::value("closed_reason_wap");
        $url = $shop_wap_url . '/tmpl/error.html';
        location_to($url);
        exit;
    }


    if ($_GET['ctl'] == 'Goods_Goods' && $_GET['met'] == 'goodslist') {
        $url = $shop_wap_url . '/tmpl/product_list.html?cat_id=' . $_GET['cat_id'];
        location_to($url);
        exit;
    }
    if (request_string('type') == 'goods') {
        $rec = request_string('rec');
        setcookie('recserialize', $rec, time() + 60 * 60 * 24 * 3, '/');
        $url = $shop_wap_url . '/tmpl/product_detail.html?goods_id=' . request_int('gid') . '&rec=' . request_string('rec');
        location_to($url);
    } elseif (request_string('ctl') == 'Buyer_Order' && request_string('met') == 'physical') {
        // $url = $shop_wap_url.'/tmpl/member/order_list.html';
        $url = Yf_Registry::get('paycenter_api_url') . "?ctl=Info&met=after_pay&return=" . $shop_wap_url . "&order_id=" . request_string('order_id') . "&r_url=" . request_string('r_url');
        location_to($url);
    } elseif (request_string('ctl') == 'Buyer_Order' && request_string('met') == 'chain') {
        // $url = $shop_wap_url.'/tmpl/member/chain_order_list.html';
        $url = Yf_Registry::get('paycenter_api_url') . "?ctl=Info&met=after_pay&return=" . $shop_wap_url;
        location_to($url);

    }
    else if(request_string('ctl') == 'Buyer_Order' && request_string('met') == 'virtual' )
	{
		// $url = $shop_wap_url.'/tmpl/member/vr_order_list.html';
		$url = Yf_Registry::get('paycenter_api_url') . "?ctl=Info&met=after_pay&return=" . $shop_wap_url;
		location_to($url);
	}
	else if(request_string('ctl') == 'Shop' && request_string('met') == 'index' )
	{
		$url = $shop_wap_url.'/tmpl/store.html?shop_id='.request_int('id');
		location_to($url);
	}
	else if(request_string('ctl') == 'Buyer_User' && request_string('met') == 'location_register' )
	{
		$fenxiao_uuid = request_int('uu_id');
        $callback = urlencode(Yf_Registry::get('url').'?ctl=Login&met=check&typ=e&redirect=&fenxiao_uuid='.$fenxiao_uuid);
        $url = Yf_Registry::get('ucenter_api_url').'?ctl=Login&act=reg&t=&from=wap&callback='.$callback;

    } elseif (request_string('ctl') == 'Buyer_Order' && request_string('met') == 'virtual') {
        // $url = $shop_wap_url.'/tmpl/member/vr_order_list.html';
        $url = Yf_Registry::get('paycenter_api_url') . "?ctl=Info&met=after_pay&return=" . $shop_wap_url;

        location_to($url);
    } elseif (request_string('ctl') == 'Shop' && request_string('met') == 'index') {
        $url = $shop_wap_url . '/tmpl/store.html?shop_id=' . request_int('id');
        location_to($url);
    } elseif (request_string('ctl') == 'Goods_Goods' && request_string('met') == 'location_register') {
        $url = Yf_Registry::get('shop_wap_url');
        location_to($url);
    } else {
        $domain_shop_id = Perm::checkSubDomain();
        $domain_shop_url = $domain_shop_id ? sprintf('%s/tmpl/store.html?shop_id=%s', Yf_Registry::get('shop_wap_url'), $domain_shop_id):$shop_wap_url;

        //判断是否是店铺二级域名
        $storeDomain = Yf_Registry::get('storeDomain');
       
        if( !empty($storeDomain) && $storeDomain['store_domain_status'])
        {
            $shop_id = array_keys($storeDomain['store_domain_store'],$_SERVER['HTTP_HOST']);

            if($shop_id)
            {
                $shop_id = current($shop_id);
                $_COOKIE['SHOP_ID_WAP'] = $shop_id;
                $domain_shop_url =$shop_wap_url . '/tmpl/store.html?shop_id=' . $shop_id.'&level=1';
            }
        }

        location_to($domain_shop_url);
    }
} 
//城市分站
if (strpos($_SERVER['SCRIPT_NAME'], '/install/index.php') === false) {
    if (Web_ConfigModel::value('subsite_is_open') == 1) {
        $subsite_host = $_SERVER['HTTP_HOST'];
        $local_web_url_array = parse_url(Yf_Registry::get('shop_api_url'));
        $local_wap_url_array = parse_url(Yf_Registry::get('shop_wap_url'));
        if ($subsite_host != $local_web_url_array['host'] && $subsite_host != $local_wap_url_array['host']) {
            $point_num = substr_count($subsite_host, '.');
            if ($point_num > 1) {
                $sub = substr($subsite_host, 0, strpos($subsite_host, '.'));
                $subsite_model = new Subsite_BaseModel();
                $sub_result = $subsite_model->getByWhere(['sub_site_domain' => $sub]);
                if (is_array($sub_result)) {
                    $sub_result = array_shift($sub_result);
                    $sub_result['sub_site_id'] = $sub_result['subsite_id'];
                    $cookie_array = ['sub_site_name', 'sub_site_id', 'sub_site_logo', 'sub_site_copyright'];
                    foreach ($cookie_array as $v) {
                        setcookie($v, $sub_result[$v], 0, '/', TOP_DOMAIN_SHOP);
                        $_COOKIE[$v] = $sub_result[$v];
                    }
                    if (!isset($_GET['sub_site_id'])) {
                        $_GET['sub_site_id'] = $sub_result['subsite_id'];
                    }
                }
            }
        }
        $sub_site_id = $_GET['sub_site_id'];
        $cookie_array = ['sub_site_name', 'sub_site_id', 'sub_site_logo', 'sub_site_copyright'];
        if ($sub_site_id > 0) {
            $subsite_model = new Subsite_BaseModel();
            $sub_result = $subsite_model->getSubsite($sub_site_id);
            if (is_array($sub_result) && $sub_result) {
                $sub_result = array_shift($sub_result);
                if ($sub_result['sub_site_is_open'] == 1) {
                    $sub_result['sub_site_id'] = $sub_result['subsite_id'];
                    foreach ($cookie_array as $v) {
                        $_COOKIE[$v] = $sub_result[$v];
                        setcookie($v, $sub_result[$v], 0, '/', TOP_DOMAIN_SHOP);
                    }
                } else {
                    $_GET['sub_site_id'] = 0;
                }
            }
        }
        if (isset($_GET['sub_site_id']) && $_GET['sub_site_id'] == 0) {
            foreach ($cookie_array as $v) {
                if ($v == 'sub_site_id') {
                    $_COOKIE[$v] = 0;
                    setcookie($v, 0, 0, '/', TOP_DOMAIN_SHOP);
                } elseif ($v == 'sub_site_name') {
                    $s_name = __('全部');
                    $_COOKIE[$v] = $s_name;
                    setcookie($v, $s_name, 0, '/', TOP_DOMAIN_SHOP);
                } else {
                    $_COOKIE[$v] = '';
                    setcookie($v, '', 0, '/', TOP_DOMAIN_SHOP);
                }
            }
        }
    } else {
        if (!isset($_COOKIE['area']) || !$_COOKIE['area']) {
            $cur_area = current_location();
            if (isset($cur_area['province']) && $cur_area['province']) {
                setcookie("area", $cur_area['province'], 0, '/', TOP_DOMAIN_SHOP);
                $_COOKIE['area'] = $cur_area['province'];
            }
        }
        setcookie('sub_site_id', 0, 0, '/', TOP_DOMAIN_SHOP);
    }
}
?>
