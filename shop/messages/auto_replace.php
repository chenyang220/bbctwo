<!--
 /**
  *   Notes: 一键替换  汉字  使用翻译函数包裹
  * 步骤
  *  1、先读取文件，获取字符串
  *  2、去除原有的 函数包裹方法
  *  3、重新添加 函数包裹方法
  *  4、检查注释部分，去除函数包裹方法
  *  5、请考虑下 单双引号
  */
  
  /**
    *
    * Notes: 替换文件内容中的中文部分，使用翻译函数包裹
    *
    *  执行:
    *      php视图文件、
    *      php控制器文件、
    *      js静态文件、
    *
    * @param $path
    */
 -->
<?php
// 获取指定 目录
$dir_now = dirname(__FILE__) . '/bbc';
read_all($dir_now);
/*
* 遍历文件夹下所有文件
*
*/
function read_all($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    $handle = opendir($dir);
    if ($handle) {
        while (($fl = readdir($handle)) !== false) {
            $temp = $dir . DIRECTORY_SEPARATOR . $fl;
            //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
            if (is_dir($temp) && $fl != '.' && $fl != '..') {
                read_all($temp);
            } else {
                if ($fl != '.' && $fl != '..') {
                    // 截取 文件后缀
                    $ext = strrchr($fl, '.');
                    // 先移除原有的 翻译方法
                    remove_fun($temp, $ext);
                    // 追加新的 翻译方法:
                    replace_fun($temp, $ext);
                    // 修复个别 可能存在的bug ,请检查 文件 是否翻译正确
                    repair($temp);
                }
            }
        }
    }
}

/**
 *
 * 把 非注释内容的 中文
 *    非替换过的  中文
 * 替换成 __('中文') 或者 <?= __('中文'); ?>
 * Notes:把中文替换成 函数包裹的
 *
 * @param $path
 */
function replace_fun($path, $ext)
{
    $conent = file_get_contents($path);
    // PHP对应的中文表达式
    $preg_ch = "/([\x80-\xff]+)/";
    $preg_jp = "/((\"|')(| +)[^\x80-\xff]+(| +)(\"|'))/";
    // 匹配中文替换掉
    if ($ext == '.php') {// PHP 视图文件
        $replacement = "<?= __('\\1'); ?>";
        // 后项引用替换成内容
        $new_conent = preg_replace($preg_ch, $replacement, $conent);
    } else {
        // 如果是JS文件或者 PHP控制器文件
        $replacement = "__(\\1)";
        // 后项引用替换成内容
        $new_conent = preg_replace($preg_jp, $replacement, $conent);
    }
    // 写入新文件
    file_put_contents($path, $new_conent);
}

/**
 * Notes: 移除 翻译方法
 */
function remove_fun($path, $ext)
{
    $content = file_get_contents($path);
    $preg_ch = "/(|<\?(php( +)echo|=)(| +))(_|)_\(('|\")([\x80-\xff]+)('|\")\)(| +|;)(|\?>)/";
    $preg_jp = "/(_|)_\(((\"|')(| +)([^\x80-\xff]+)(| +)('|\"))\)/";
    if ($ext == ".php") {
        // 后项引用替换内容
        $replacement = "\\7";
        // 清空 原有的翻译方法 返回字符串
        $new_content = preg_replace($preg_ch, $replacement, $content);
    } else {
        // 后项引用替换内容
        $replacement = "\\2";
        // 清空 原有的翻译方法 返回字符串
        $new_content = preg_replace($preg_jp, $replacement, $content);
    }
    file_put_contents($path, $new_content);
}

/**
 *
 * Notes: 清除 原有的翻译方法
 * 处理匹配出来的字符串
 *
 * @param $str
 *
 */
function remove_fun_str($str)
{
    // 匹配 字符串中的 翻译写法
    $preg = "/((<\?=|<\?php( +|)echo)|)(| +)_(|_)\((\"|')((| +)[^\x80-\xff]+(| +))(\"|')\)( +|)(;|)( +|)(\?>|)/";
    // 后项引用替换内容
    $replacement = "\\7";
    
    // 清空 原有的翻译方法 返回字符串
    return preg_replace($preg, $replacement, $str);
}

/**
 *
 * Notes:匹配注释内容 清除翻译方法
 * (\/\*.*(\*\/|))|((#|\*)( |).*?\n)|(\/\/.*?\n)|(<!--[^\!\[]*?-->)
 *
 * "/*  *\/"
 * "#"
 * "*
 * "<!--
 * "//
 *
 */
function remove_notes($path)
{
    $content = file_get_contents($path);
    // 获取 注释内容
    $preg = "/(\/\*.*\*\/)|((#|\*).*?\n)|(\/\/.*?\n)|(<!--[^\!\[]*?-->)/";
    $isMatched = preg_match_all($preg, $content, $data);
    $relacement = "\\1";
    $new_content = preg_match_all($preg, $relacement, $content);
    file_put_contents($path, $new_content);
}

/**
 *
 * Notes: 修复 内容中可能出现  的
 * ; ?>; ?>
 *
 *  ?>?>
 *
 */
function repair($path)
{
    $content = file_get_contents($path);
    // 匹配
    $preg = "/; \?>(|; | +)\?>/";
    $replacement = "; ?>";
    $new_content = preg_replace($preg, $replacement, $content);
    file_put_contents($path, $new_content);
}