<?php
error_reporting(0);
$uri = $_SERVER['REQUEST_URI'];
if (substr($uri, 0, 2) == '//') {
    $_SERVER['REQUEST_URI'] = substr($uri, 1);
}
$int_num = 0;
/**
 * 入口文件
 *
 * 所有程序调用的入口， 此文件属于框架的一部分，任何人不允许修改！
 *
 * @category   Framework
 * @package    __init__
 * @author     Yf <service@yuanfeng.cn>
 * @copyright  Copyright (c) 2010远丰仁商
 * @version    1.0
 * @todo
 */
//版本
define('VER', '1.02');
//设置开始的时间
$mtime = explode(' ', microtime());
$app_starttime = $mtime[1] + $mtime[0];
define('APP_DIR_NAME', 'shop');
define('EXTENDS_DIR_NAME', 'extends');
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)));
define('LIB_PATH', ROOT_PATH . '/libraries');   //ZeroPHP Framework 所在目录
define('APP_PATH', ROOT_PATH . '/' . APP_DIR_NAME);         //应用程序目录
define('MOD_PATH', APP_PATH . '/models');       //应用程序模型目录
define('EXTENDS_PATH',  ROOT_PATH . '/' . EXTENDS_DIR_NAME);
//设置php版本
$php_version = (float)phpversion();
$php_version = str_replace('.','',$php_version);
define('PHP_SYS_VERSION',  $php_version);
/**
 * 风格静态文件文件目录，此处变量名称$themes勿修改
 *
 * @var string
 */
$host = '';
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
}
$server_id = md5($host);
if (is_file(APP_PATH . '/configs/global_' . $server_id . '.ini.php')) {
    include_once APP_PATH . '/configs/global_' . $server_id . '.ini.php';
} else {
    include_once APP_PATH . '/configs/global.ini.php';
}
//载入公共函数库
$common_function =  ROOT_PATH . '/common/function.php';
if(is_file($common_function)){
    @include_once $common_function;
}

$themes_name = $theme_id ?: "default";
$pro_path = '';
if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT']) {
    $pro_path_row = explode($_SERVER['DOCUMENT_ROOT'], ROOT_PATH);
    if (isset($pro_path_row[1]) && $pro_path_row[1]) {
        $pro_path = '/' . ltrim($pro_path_row[1], '/');
        $themes = $pro_path . '/' . APP_DIR_NAME . '/static/' . $themes_name;
    } else {
        $themes = '/' . APP_DIR_NAME . '/static/' . $themes_name;
    }
} else {
    $themes = '/' . APP_DIR_NAME . '/static/' . $themes_name;
}
define('TPL_DEFAULT_PATH', APP_PATH . '/views/default');  //应用程序默认视图目录
define('TPL_PATH', APP_PATH . '/views/' . $themes_name);  //应用程序视图目录
define('CTL_PATH', APP_PATH . '/controllers');  //应用程序控制器目录
define('INI_PATH', APP_PATH . '/configs');      //应用程序配置文件目录
define('LOG_PATH', APP_PATH . '/data/logs');
define('DATA_PATH', APP_PATH . '/data');
/*
define('HLP_PATH', APP_PATH . '/helpers');
*/
define('LAN_PATH', ROOT_PATH . '/messages');
//是否开启runtime，如果为false，则不生成runtime缓存
define('RUNTIME', false);
//是否开启debug，如果为true，则不生成runtime缓存
define('DEBUG', false);
//加载协议解析文件
require_once INI_PATH . '/protocol.ini.php';
if (RUNTIME) {
    /**
     * runtime文件名称
     *
     * @var string
     */
    global $runtime;
    if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '/') {
        if (ltrim($_SERVER['PATH_INFO'], '/')) {
            $path_info_get = explode('/', ltrim($_SERVER['PATH_INFO'], '/'));
        }
        if (isset($path_info_get[0])) {
            $runtime = implode('/', explode('_', $path_info_get[0]));
        } else {
            $runtime = 'Index';
        }
    } else {
        if (isset($_REQUEST['ctl'])) {
            $runtime = implode('/', explode('_', $_REQUEST['ctl']));
        } else {
            $runtime = 'Index';
        }
    }
    /**
     * runtime文件路径
     *
     * @var string
     */
    $runtime_file = APP_PATH . '/data/runtime/' . VER . '/' . $runtime . '.php';
}
/**
 * 保存加载过的文件，只记录class或者记录按照顺序执行的全局文件。
 *
 * @var array
 */
global $import_file_row;
$import_file_row = [];
/**
 * 计算是否需要从runtime运行
 */
if (RUNTIME && is_file($runtime_file)) {
    include_once $runtime_file;
} else {
    array_push($import_file_row, LIB_PATH . '/__init__.php');
    array_push($import_file_row, APP_PATH . '/configs/config.ini.php');
    //初始化Zero
    require_once LIB_PATH . '/__init__.php';
    //引入用户配置文件
    require_once APP_PATH . '/configs/config.ini.php';
}
if (1 != $site_status) {
    if ((isset($_REQUEST['ctl']) && 'Api_' == substr($_REQUEST['ctl'], 0, 4))) {
    } elseif ((isset($_REQUEST['ctl']) && strstr($_REQUEST['ctl'], 'Seller_')) !== false) {
    } elseif ((isset($_REQUEST['ctl']) && strstr($_REQUEST['ctl'], 'Login')) !== false) {
    } else {
        include TPL_PATH . '/IndexCtl/error.php';
        die();
    }
}
if (RUNTIME) {
    Yf_Registry::set('runtime', $runtime);
    Yf_Registry::set('runtime_file', $runtime_file);
}
//程序控制器启动，计算结果
$ctl = $_GET['ctl'];
$met = $_GET['met'];
!$ctl && $ctl = "Index";
!$met && $met = 'index';

//扩展分支判断
if ($ctl) {
    $overwrite = "Extends_" . $ctl . "Ctl";
    $file =  EXTENDS_PATH ."/controllers". '/' . str_replace('_', '/', $ctl . "Ctl") . '.php';
    if (file_exists($file) && class_exists($overwrite) && method_exists($overwrite, $met)) {
        $_REQUEST['ctl'] = "Extends_" . $ctl;
    }
}

ob_start();
Yf_App::start();
$data = ob_get_contents();
ob_end_clean();

$PluginManager->trigger('end', '');
$mtime = explode(' ', microtime());
$app_endtime = $mtime[1] + $mtime[0];

include __DIR__ . '/shop/Fast.php';

 

