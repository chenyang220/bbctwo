<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';

// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();
?>
    <link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    </head>
    <body class="<?=$skin?>">
    <div class="wrapper page"></div>
    <div class="mod-toolbar-top cf">
		<div class="left">
                    <div id="assisting-category-select" class="ui-tab-select">
                        <ul class="ul-inline">
                                <li>
                                    <span id="source"></span>
                                </li>
                                <li>
                                    <span id="shop_class"></span>
                                </li>
                                <li>
                                  <input type="text" id="searchName" class="ui-input ui-input-ph con" value="<?= __('请输入相关数据'); ?>...">
                                </li>
                                <li><a class="ui-btn" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                                <li><a class="ui-btn"><?= __('导出'); ?><i class="iconfont icon-btn04"></i></a></li>
                         </ul>
                  </div>
		</div>
	<div class="fr">
            <a class="ui-btn" id="btn-refresh"><?= __('刷新'); ?><i class="iconfont icon-btn01"></i></a>
        </div>
	</div>

  
    <div class="grid-wrap">
        <table id="grid">
        </table>
        <div id="page"></div>
    </div>
</div>




<script type="text/javascript" src="<?=$this->view->js?>/controllers/shop/index/shop_label_check.js" charset="utf-8"></script>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>