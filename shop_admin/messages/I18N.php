<?php
global $i18n;// 定义全局变量 $i18n
$i18n = new I18N; // 实例化 翻译类，处理第三方翻译和写入 原文->译文 的键值对
// 读SHOP的 语言设置
$shop_url = Yf_Registry::get('shop_api_url');
if (!$shop_url) {
    exit('Please set shop_api_url at config.ini.php');
}
$language_id = trim(file_get_contents($shop_url . "index.php?_system_language_=1"));
$language_id = "zh_CN";
if (!$language_id) {
    exit('Language not setting.');
}
// 设置 $_COOKIE lang_selected
setcookie('lang_selected', $language_id, time() + 86400 * 365);
// 防止 设置失败
$_COOKIE['lang_selected'] = $language_id;
// 默认系统语言
$language_id = $_COOKIE['lang_selected'] ? :"zh_CN";
$i18n->language = $language_id; // 当前语言
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
    
    if ($_COOKIE['lang_selected'] == "zh_CN"){
        return $str;
    }
    
    return $i18n->translate_PHP($str);
}

class I18N
{
    public $language = 'zh_CN';
    public $lang_file;
    static $arr = [];
    
    /**
     *
     * Notes:加载语言包
     *
     */
    public function load()
    {
        if (static::$arr) {
            return;
        }
        $dir = dirname(__FILE__) . '/';
        if (!is_dir($dir . $this->language)) {
            return;
        }

        $this->lang_file = $dir . $this->language . "/app.php";
        if (file_exists($this->lang_file)) {
            static::$arr = include $this->lang_file;
            static::$arr['lang'] = $this->language;
        }
    }
    
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
        if (!static::$arr[$str]) {
            static::$arr[$str] = $this->auto_translate($str) ? :$str;
            file_put_contents($this->lang_file, "<?php return " . var_export(static::$arr, true) . ";");
        }
        
        return static::$arr[$str] ? :$this->auto_translate($str);
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
}