<?php
require_once './ucenter/configs/config.ini.php';
$domain = Yf_Registry::get('imbuilder_admin_url');
extract($_GET);
//�жϵ�ǰ�Ƿ����û���¼
//1.ucenter���Ѿ����û���¼
if (isset($_COOKIE['id']) && $_COOKIE['id']) {
    //1-1.ucenter�еĵ�¼�û���im�еĵ�¼�û���ͬ
    if ($us == $_COOKIE['id']) {
        header('Location:' . $callback);
    } else {
        //1-2.ucenter�еĵ�¼�û���im�еĵ�¼�û���ͬ
        //1-2-1.�˳���ǰ�û�
        if (isset($_COOKIE['key']) || isset($_COOKIE['id'])) {
            setcookie("key", null, time() - 3600 * 24 * 365);
            setcookie("id", null, time() - 3600 * 24 * 365);
        }
        //1-2-2.��¼IM�е��û�
        if ($user_account && $user_password) {
            
            // ��IMд�� COOKIE ���Ҳ��洢  im_seller  //˵ʵ�����Ҳ�֪�����ںδ���
            $domain = str_replace('http://', '', $domain);
            $domain = str_replace('https://', '', $domain);
            $domain = substr($domain, strpos($domain, '.') + 1);
            if (strpos($domain, '/') !== false) {
                $domain = substr($domain, 0, strpos($domain, '/'));
            }
            setcookie('yuanfeng_im_username', $user_account, time() + 3600 * 24 * 365, '/', $domain);
            $_COOKIE['yuanfeng_im_username'] = $user_account;
            
            // ��ת
            $url = sprintf('%s?ctl=Login&met=ImLogin&typ=json&user_account=%s&user_password=%s&callback=%s', 'index.php', $user_account, $user_password, $callback);
            header('Location:' . $url);
        }
    }
} else {
    // ��IMд�� COOKIE ���Ҳ��洢  im_seller  //˵ʵ�����Ҳ�֪�����ںδ���
    $domain = str_replace('http://', '', $domain);
    $domain = str_replace('https://', '', $domain);
    $domain = substr($domain, strpos($domain, '.') + 1);
    if (strpos($domain, '/') !== false) {
        $domain = substr($domain, 0, strpos($domain, '/'));
    }
    setcookie('yuanfeng_im_username', $user_account, time() + 3600 * 24 * 365, '/', $domain);
    $_COOKIE['yuanfeng_im_username'] = $user_account;
    //2.ucenter��û���û���¼
    if ($user_account && $user_password) {
        $url = sprintf('%s?ctl=Login&met=ImLogin&typ=json&user_account=%s&user_password=%s&callback=%s', 'index.php', $user_account, $user_password, $callback);
        header('Location:' . $url);
    }
}
?>
