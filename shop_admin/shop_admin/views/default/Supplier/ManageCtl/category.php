<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<body class="<?=$skin?>">
<style>

.ui-jqgrid tr.jqgrow .img_flied{padding: 1px; line-height: 0px;}
.img_flied img{width: 100px; height: 30px;}

</style>

<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __('供应商管理'); ?></h3>
                <h5><?= __('供应商管理'); ?>-<?= __('经营类目申请'); ?></h5>
            </div>
            <ul class="tab-base nc-row">
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Supplier_Manage&met=indexs" ><span><?= __('供应商管理'); ?></span></a></li>
				<li><a href="<?= Yf_Registry::get('url') ?>?ctl=Supplier_Manage&met=join" ><span><?= __('审核开店信息'); ?></span></a></li>
                <li><a  href="<?= Yf_Registry::get('url') ?>?ctl=Supplier_Manage&met=pay" ><span><?= __('审核供应商付款'); ?></span></a></li>
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Supplier_Manage&met=reopen" ><span><?= __('续签申请'); ?></span></a></li>
                <li><a class="current" ><span><?= __('经营类目申请'); ?></span></a></li>
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Supplier_Manage&met=settlement"  ><span><?= __('结算周期设置'); ?></span></a></li>
            </ul>
        </div>
    </div>
          
	<!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p>
	<div class="explanation" id="explanation">
        <div class="title" id="checkZoom">
			<i class="fa fa-lightbulb-o"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em>
		</div>
        <ul>
            <li><?= __('此处可以对供应商新申请的经营类目进行'); ?> <?= __('审核'); ?>/<?= __('删除'); ?> <?= __('操作。'); ?></li>
        </ul>
    </div>
    
	<div class="mod-toolbar-top cf">
		<div class="left">
            <div id="assisting-category-select" class="ui-tab-select">
                <ul class="ul-inline">
                    <li><span id="source"></span></li>
                    <li><input type="text" id="searchName" class="ui-input ui-input-ph con" value="<?= __('请输入相关数据'); ?>..."></li>
                    <li><span id="shop_class_bind_enable"></span></li>
                    <li><a class="ui-btn" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                </ul>
            </div>
		</div>
		<div class="fr">
            <a class="ui-btn" id="btn-refresh"><?= __('刷新'); ?><i class="iconfont icon-btn01"></i></a>
        </div>
	</div>
 
    <div class="grid-wrap">
        <table id="grid"></table>
        <div id="page"></div>
    </div>
</div>




<script type="text/javascript" src="<?=$this->view->js?>/controllers/supplier/index/shop_category_list.js" charset="utf-8"></script>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>