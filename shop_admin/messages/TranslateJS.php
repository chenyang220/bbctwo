<?php
/**
 * Created by PhpStorm.
 * Author: Michael Ma
 * Date: 2018年09月12日
 * Time: 13:42:30
 */
$language_id = $_COOKIE['lang_selected'];
global $i18nJs;
$i18nJs = new I18NJs();
$i18nJs->language = $language_id; // 当前语言
$i18nJs->auto_translate_js($_GET['str'], $_GET['dst']);

class I18NJs
{
    public $language = "zh_CN";
    
    /**
     *
     * Notes:js自动翻译记录写入文件
     *
     * @param $str [原文]
     * @param $dst [译文]
     */
    public function auto_translate_js($str, $dst)
    {
        // 当前目录
        $dir = dirname(__FILE__) . '/';
        if (!is_dir($dir . $this->language)) {
            return;
        }
        // 资源文件地址
        $file = $dir . $this->language . "/app.js";
        // 获取资源内容
        $resource = file_get_contents($file);
        // 判断译文是否已经存在于资源内容中? 返回字符串在另一字符串中第一次出现的位置，如果没有找到字符串则返回 FALSE。
        // 拼接双引号 ""
        $find_str = "\"" . $str . "\"";
        $flag = strpos($resource, $find_str);
        if (!$flag) { // 不存在的时候，取反 即写入 原文->译文
            // 截取到','最后一次出现的位置,截取字符串 "var source = {....,"(含小逗号)
            $new = substr($resource, 0, strripos($resource, ',') + 1);
            // 拼接需要追加的 键值对
            $fystr = "\n\t\t\"" . $str . "\":\"" . $dst . "\",";
            // 拼接需要追加的 尾符 \n\t}\n};
            $new_str = $new . $fystr . "\n\t}\n};";
            file_put_contents($file, $new_str);
        }
    }
}