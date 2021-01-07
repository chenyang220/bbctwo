<?php
// 打开翻译，如果关闭，多语言功能将不能使用
$open_translate = true;
// 开启自动翻译，直接返回原来的值。需要用户自己到对应的包中进行翻译
$i18nDebug = true;
// 声明初始化 全局变
global $allowLanguage;
global $i18n;// 定义全局变量 $i18n
$allowLanguage = ['en_US', 'zh_CN', 'zh_TW'];
$i18n = new I18N; // 实例化 翻译类，处理第三方翻译和写入 原文->译文 的键值对
/**
 * 从后台取得当前语言设置
 * shop_admin后台
 *
 */
// $row = (new Web_Config)->getConfig('language_id');
// if ($row['language_id']['config_value']) {
//     $language_id = trim($row['language_id']['config_value']);
// }
$language_id = "zh_CN";
// 设置 $_COOKIE lang_selected
setcookie('lang_selected', $language_id, time() + 86400 * 365);
// 防止 设置失败
$_COOKIE['lang_selected'] = $language_id;
// 默认系统语言
$language_id = $_COOKIE['lang_selected'] ? :"zh_CN";
/**
 * 如果是其他系统传入参数  _system_language_ 返回当前语言。
 * 目的使paycenter ucenter shop三系统语言统一。界面一致
 */
if (isset($_GET['_system_language_'])) {
    echo $language_id;
    exit;
}
$i18n->open = $open_translate; // 打开语言翻译
$i18n->language = $language_id; // 当前语言
$i18n->debug = $i18nDebug; // 开启自动翻译
$i18n->load();
/**
 * translate php翻译的方法
 *
 * @param  [type] $str [description]
 *
 * @return [type]      [description]
 */
function __($str)
{
    global $i18n;
    if ($_COOKIE['lang_selected'] == "zh_CN") {
        return $str;
    }
    return $i18n->translate_PHP($str);
}

class I18N
{
    public $language = 'zh_CN';
    public $open = false;
    public $debug = false;
    static $arr = [];
    public $lang_file;
    
    /**
     *
     * Notes: __('')翻译的定义方法
     *
     * @param $str
     *
     * @return mixed
     */
    public function translate_PHP($str)
    {
        if ($this->open !== true || preg_match('/^(\d|\s)+$/', $str)) {
            return $str;
        }
        if ($this->debug && !static::$arr[$str]) {
            static::$arr[$str] = $this->auto_translate($str) ? :$str;
            file_put_contents($this->lang_file, "<?php return " . var_export(static::$arr, true) . ";");
        }
        
        return static::$arr[$str] ? :$str;
    }
    
    /**
     *
     * Notes:百度自动翻译
     *
     * @param $str
     *
     * @return mixed
     */
    protected function auto_translate($str)
    {
        include_once __DIR__ . '/baidu_transapi.php';
        $to = $this->language == "en_US" ? "en":($this->language == "zh-TW" ? "cht":"zh");
        
        return baidu_translate($str, $to)['trans_result'][0]['dst'];
    }
    
    /**
     *
     * Notes:加载语言包
     *
     */
    public function load()
    {
        global $allowLanguage;
        if (static::$arr) {
            return;
        }
        $dir = dirname(__FILE__) . '/';
        if (!is_dir($dir . $this->language)) {
            return;
        }
        if (!in_array($this->language, $allowLanguage)) {
            return;
        }
        $this->lang_file = $dir . $this->language . "/app.php";
        if (file_exists($this->lang_file)) {
            static::$arr = include $this->lang_file;
            static::$arr['lang'] = $this->language;
        }
    }
}