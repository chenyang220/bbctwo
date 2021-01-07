<!DOCTYPE html>
<html>
<head>
    <title><?=__('商家入驻')?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="renderer" content="webkit|ie-stand|ie-comp" />
    <?php if ($_COOKIE['lang_selected']=='en_US') { ?>
        <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/english.css"/>
    <?php } ?>
    <link href="<?= $this->view->css ?>/joinin.css" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css ?>/swiper.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="http://at.alicdn.com/t/font_1lxt0at6lohto6r.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css" />
    <link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css" rel="stylesheet">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/jquery.js"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/nav.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/swiper.min.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/base.js"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/common.js"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.toastr.min.js" charset="utf-8"></script>
    <link href="<?= $this->view->css ?>/tips.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
    <script src="<?= $this->view->js ?>/select2.min.js"></script>
    <script src="<?= $this->view->js ?>/intlTelInput.js"></script>
    <link rel="stylesheet" href="<?= $this->view->css ?>/intlTelInput.css">
    <?php
    include $this->view->getTplPath() . '/' . 'translatejs.php';
    ?>
    <script type="text/javascript">
        var BASE_URL = "<?=Yf_Registry::get('base_url')?>";
        var SITE_URL = "<?=Yf_Registry::get('url')?>";
        var INDEX_PAGE = "<?=Yf_Registry::get('index_page')?>";
        var STATIC_URL = "<?=Yf_Registry::get('static_url')?>";
        var UCENTER_URL = "<?=Yf_Registry::get('ucenter_api_url')?>";

        var DOMAIN = document.domain;
        var WDURL = "";
        var SCHEME = "default";
        try
        {
            //document.domain = 'ttt.com';
        } catch (e)
        {
        }

        var SYSTEM = SYSTEM || {};
        SYSTEM.skin = 'green';
        SYSTEM.isAdmin = true;
        SYSTEM.siExpired = false;
    </script>

</head>
<body>
<div class="bgheadr">
    <div class="header">
        <h2 class="header_logo"> <a href="index.php" class="logo"><img src="<?= $web['seller_logo']  ?>"></a> </h2>
        <p class="header_p"> <span style="margin-right:10px;">|</span><a href=""> <?=__('商家入驻')?></a></p>
        <ul class="header_menu">
            <li class="current" style="float:right;"> <a href="" class="joinin"> <i></i> <?=__('商家入驻申请')?> </a> </li>
        </ul>
    </div>
</div>