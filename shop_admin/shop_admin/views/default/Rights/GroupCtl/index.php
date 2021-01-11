<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<?php
include TPL_PATH . '/'  . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
//当前页父级菜单 同级菜单 当前菜单
$menus = $this->getThisMenus();
?>
</head>
<body class="<?=$skin?>">
<div class="wrapper page">

    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __($menus['father_menu']['menu_name']); ?></h3>
                <h5><?= __($menus['father_menu']['menu_url_note']); ?></h5>
            </div>
            <ul class="tab-base nc-row">
                <?php 
                foreach($menus['brother_menu'] as $key=>$val){ 
                    if(in_array($val['rights_id'],$admin_rights)||$val['rights_id']==0){
                ?>
                <li><a <?php if(!array_diff($menus['this_menu'], $val)){?> class="current"<?php }?> href="<?= Yf_Registry::get('url') ?>?ctl=<?=$val['menu_url_ctl']?>&met=<?=$val['menu_url_met']?><?php if($val['menu_url_parem']){?>&<?=$val['menu_url_parem']?><?php }?>"><span><?= __($val['menu_name']); ?></span></a></li>
                <?php 
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>
        </ul>
    </div>
    
	<div class="mod-toolbar-top cf">
	    <div class="fl"><strong class="tit"><?= __('权限组'); ?></strong></div>
	    <div class="fr">
	    	<a href="./index.php?ctl=Rights_Group&met=manage" class="ui-btn ui-btn-sp mrb" id="btn-add"><?= __('新增'); ?><i class="iconfont icon-btn03"></i></a><!-- <a class="ui-btn" id="btn-disable"><?= __('禁用'); ?></a> <a class="ui-btn" id="btn-enable"><?= __('启用'); ?></a>--><!-- <a class="ui-btn" id="btn-print"><?= __('打印'); ?></a>--><!-- <a class="ui-btn" id="btn-import"><?= __('导入'); ?></a>--><!-- <a class="ui-btn" id="btn-export"><?= __('导出'); ?></a>--> <a class="ui-btn" id="btn-refresh"><?= __('刷新'); ?><i class="iconfont icon-btn01"></i></a><!-- <a class="ui-btn" href="./index.php?ctl=User_Base&met=index"><?= __('返回'); ?><i class="iconfont icon-btn05"></i></a> -->
	    	
	    </div>
	  </div>
    <div class="grid-wrap">
	    <table id="grid">
	    </table>
	    <div id="page"></div>
	  </div>
</div>
<script src="./shop_admin/static/default/js/controllers/rights/rights_group_list.js"></script>
<?php
include TPL_PATH . '/'  . 'footer.php';
?>