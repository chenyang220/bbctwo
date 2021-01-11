<?php
require_once './ucenter/configs/config.ini.php';
$domain = Yf_Registry::get('imbuilder_admin_url');
extract($_GET);
//判断当前是否有用户登录
//1.ucenter中已经有用户登录
if (isset($_COOKIE['id']) && $_COOKIE['id']) {
    //1-1.ucenter中的登录用户与im中的登录用户相同
    if ($us == $_COOKIE['id']) {
        header('Location:' . $callback);
    } else {
        //1-2.ucenter中的登录用户与im中的登录用户不同
        //1-2-1.退出当前用户
        if (isset($_COOKIE['key']) || isset($_COOKIE['id'])) {
            setcookie("key", null, time() - 3600 * 24 * 365);
            setcookie("id", null, time() - 3600 * 24 * 365);
        }
        //1-2-2.登录IM中的用户
        if ($user_account && $user_password) {
            
            // 向IM写入 COOKIE 暂且不存储  im_seller  //说实话，我不知道用在何处？
            $domain = str_replace('http://', '', $domain);
            $domain = str_replace('https://', '', $domain);
            $domain = substr($domain, strpos($domain, '.') + 1);
            if (strpos($domain, '/') !== false) {
                $domain = substr($domain, 0, strpos($domain, '/'));
            }
            setcookie('yuanfeng_im_username', $user_account, time() + 3600 * 24 * 365, '/', $domain);
            $_COOKIE['yuanfeng_im_username'] = $user_account;
            
            // 跳转
            $url = sprintf('%s?ctl=Login&met=ImLogin&typ=json&user_account=%s&user_password=%s&callback=%s', 'index.php', $user_account, $user_password, $callback);
            header('Location:' . $url);
        }
    }
} else {
    // 向IM写入 COOKIE 暂且不存储  im_seller  //说实话，我不知道用在何处？
    $domain = str_replace('http://', '', $domain);
    $domain = str_replace('https://', '', $domain);
    $domain = substr($domain, strpos($domain, '.') + 1);
    if (strpos($domain, '/') !== false) {
        $domain = substr($domain, 0, strpos($domain, '/'));
    }
    setcookie('yuanfeng_im_username', $user_account, time() + 3600 * 24 * 365, '/', $domain);
    $_COOKIE['yuanfeng_im_username'] = $user_account;
    //2.ucenter中没有用户登录
    if ($user_account && $user_password) {
        $url = sprintf('%s?ctl=Login&met=ImLogin&typ=json&user_account=%s&user_password=%s&callback=%s', 'index.php', $user_account, $user_password, $callback);
        header('Location:' . $url);
    }
}
?>
