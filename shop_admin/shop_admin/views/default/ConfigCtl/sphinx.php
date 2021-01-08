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
		    <?= ($data['sphinx_ext']==1 ? '' : '<li class="red">Sphinx '.__('扩展尚未安装').' </li>')?>
		    <?=($data['scws_ext']==1 ? '' : '<li class="red">Scws '.__('扩展尚未安装').'</li>')?>
        </ul>
    </div>

    <form method="post" enctype="multipart/form-data" id="shop-sphinx-form" name="shop-sphinx-form">
        <input type="hidden" name="config_type[]" value="sphinx"/>

        <div class="ncap-form-default">


            <dl class="row">
                <dt class="tit">Sphinx<?= __('状态'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="sphinx_statu1" name="sphinx[sphinx_statu]"  value="1" type="radio" <?=(@$data['sphinx_statu']['config_value']==1 ? 'checked' : '')?>>
						<label title="<?= __('开启'); ?>" class="cb-enable <?=(@$data['sphinx_statu']['config_value']==1 ? 'selected' : '')?> " for="sphinx_statu1"><?= __('开启'); ?></label>

                        <input id="sphinx_statu0" name="sphinx[sphinx_statu]"  value="0" type="radio" <?=(@$data['sphinx_statu']['config_value']==0 ? 'checked' : '')?>>
						<label title="<?= __('关闭'); ?>" class="cb-disable <?=(@$data['sphinx_statu']['config_value']==0 ? 'selected' : '')?>" for="sphinx_statu0"><?= __('关闭'); ?></label>
                    </div>
                    <p class="notic"></p>
                </dd>
            </dl>


            <dl class="row">
                <dt class="tit">
                    <label for="sphinx_search_host">sphinx_search_host</label>
                </dt>
                <dd class="opt">
                    <input id="sphinx_search_host" name="sphinx[sphinx_search_host]" value="<?=(@$data['sphinx_search_host']['config_value'])?>" class="ui-input w400" type="text"/>

                    <p class="notic"></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">
                    <label for="sphinx_search_port">sphinx_search_port</label>
                </dt>
                <dd class="opt">
                    <input id="sphinx_search_port" name="site[sphinx_search_port]" value="<?=(@$data['sphinx_search_port']['config_value'])?>" class="ui-input w400" type="text"/>

                    <p class="notic"><?= __('网站名称，将显示在前台顶部欢迎信息等位置'); ?></p>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>
<script type="text/javascript">
</script>

<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>