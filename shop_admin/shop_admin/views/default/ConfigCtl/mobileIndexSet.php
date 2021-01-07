<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();
?>
    <link href="<?= $this->view->css ?>/index.css?v=41" rel="stylesheet" type="text/css">
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
                foreach ($menus['brother_menu'] as $key => $val) {
                    if (in_array($val['rights_id'], $admin_rights) || $val['rights_id'] == 0) {
                        ?>
                        <li><a <?php if (!array_diff($menus['this_menu'], $val)) { ?> class="current"<?php } ?> href="<?= Yf_Registry::get('url') ?>?ctl=<?= $val['menu_url_ctl'] ?>&met=<?= $val['menu_url_met'] ?><?php if ($val['menu_url_parem']) { ?>&<?= $val['menu_url_parem'] ?><?php } ?>"><span><?= __($val['menu_name']); ?></span></a></li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p>
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>
        </ul>
    </div>

    <form method="post" id="index-mb-setting" name="settingForm">
        <input type="hidden" name="config_type[]" value="mobileIndex"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('首页模板版式：'); ?></dt>
                <dd class="opt">
                    <div>
						<ul class="module-select-ul">
							<li class="mr30">
								<em class="img-box">
									<img class="w200 " src="<?=$this->view->img?>/module-default.png" alt="">
								</em>
								<label class="block">
									<input name="mobileIndex[tpl_layout_style]" type="radio" value="1" <?= (($data['tpl_layout_style']['config_value'] == 1 || !isset($data['tpl_layout_style']['config_value']))? 'checked' : '') ?>/>
									<em>标准版首页模板</em>
								</label>
							</li>
							<li class=" mr30">
								<em class="img-box">
									<img class="w200" src="<?=$this->view->img?>/module-industry.png" alt="">
								</em>
								<label class="block">
									<input name="mobileIndex[tpl_layout_style]" type="radio" value="2" <?= ($data['tpl_layout_style']['config_value'] == 2 ? 'checked' : '') ?>/>
									<em>工业类首页模板</em>
								</label>
							</li>
							<li>
								<em class="img-box">
									<img class="w200" src="<?=$this->view->img?>/module-fresh.png" alt="">
								</em>
								<label class="block">
									<input name="mobileIndex[tpl_layout_style]" type="radio" value="3" <?= ($data['tpl_layout_style']['config_value'] == 3 ? 'checked' : '') ?>/>
									<em>生鲜类首页模板</em>
								</label>
							</li>
						</ul>
                    </div>
                    <p class="notic">首页模板：使用设置好的首页模板</p>
                </dd>
            </dl>
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn ml400"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?= $this->view->js ?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>