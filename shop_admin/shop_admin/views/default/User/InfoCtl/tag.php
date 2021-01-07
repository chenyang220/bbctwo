<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
//当前页父级菜单 同级菜单 当前菜单
$menus = $this->getThisMenus();
?>
    <link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    </head>
    <body class="<?=$skin?>">
    <form method="post" enctype="multipart/form-data" id="tag-form" name="form">
        <div class="wrapper">
            <div class="mod-search cf">
                <div class="fl">
                    <ul class="ul-inline">
                        <li>
                            <input type="text" id="user_tag_name" name="user_tag_name" class="ui-input ui-input-ph matchCon" placeholder="<?= __('标签名称'); ?>">
                        </li>
                        <li><a class="ui-btn" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="grid-wrap">
                <table id="grid"></table>
                <div id="page"></div>
            </div>
        </div>
    </form>
    <script src="<?= $this->view->js ?>/controllers/user/info/tag.js"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>