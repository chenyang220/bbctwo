<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';

// 当前管理员权限
$admin_rights = $this->getAdminRights();
$ctl = rtrim($this->ctl,'Ctl');
$met = $this->met;
$Menu_Base = new Menu_Base();
// 当前页面所在菜单
$this_menu = $Menu_Base->getOneByWhere(array('menu_url_ctl'=>$ctl,'menu_url_met'=>$met));
// 当前页面所在的父级菜单
$father_menu = $Menu_Base->getOneByWhere(array('menu_id'=>$this_menu['menu_parent_id']));
// 当前页面的全部同级菜单
$brother_menu = $Menu_Base->getByWhere(array('menu_parent_id'=>$father_menu['menu_id']));

?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>

</head>
<body class="<?=$skin?>">
<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?=$father_menu['menu_name']?></h3>
                <h5><?=$father_menu['menu_url_note']?></h5>
            </div>
            <ul class="tab-base nc-row">
                <?php 
                foreach($brother_menu as $key=>$val){ 
                    if(in_array($val['rights_id'],$admin_rights)||$val['rights_id']==0){
                ?>
                <li><a <?php if(!array_diff($this_menu, $val)){?> class="current"<?php }?> href="<?= Yf_Registry::get('url') ?>?ctl=<?=$val['menu_url_ctl']?>&met=<?=$val['menu_url_met']?><?php if($val['menu_url_parem']){?>&<?=$val['menu_url_parem']?><?php }?>"><span><?= __($val['menu_name']); ?></span></a></li>
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
            <?=$this_menu['menu_url_note']?>
        </ul>
    </div>


    <form method="post" enctype="multipart/form-data" id="grade-setting-form" name="form">
        <input type="hidden" name="config_type[]" value="grade"/>

        <div class="ncap-form-default">
			<div class="title">
				<h3><?= __('会员日常获取经验值设定'); ?></h3>
			</div>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('会员每天第一次登录'); ?></label>
                </dt>
                <dd class="opt">
					<input id="grade_login" name="grade[grade_login]" value="<?=($data['grade_login']['config_value'])?>" class="ui-input w400" type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>          
			<dl class="row">
                <dt class="tit">
                    <label><?= __('订单商品评论'); ?></label>
                </dt>
                <dd class="opt">
					<input id="grade_evaluate" name="grade[grade_evaluate]" value="<?=($data['grade_evaluate']['config_value'])?>" class="ui-input w400" type="text"/>
                    <p class="notic"></p>
                </dd>
            </dl>
			<div class="title">
				<h3><?= __('会员购物获取经验值设定'); ?></h3>
			</div>
			<dl class="row">
                <dt class="tit">
                    <label><?= __('消费额与赠送经验值比例'); ?></label>
                </dt>
                <dd class="opt">
					<input id="grade_recharge" name="grade[grade_recharge]" value="<?=($data['grade_recharge']['config_value'])?>" class="ui-input w400" type="text"/>
                    <p class="notic"><?= __('该值为大于'); ?>0<?= __('的数，例'); ?>:<?= __('设置为'); ?>10<?= __('，表明消费'); ?>10<?= __('单位货币赠送'); ?>1<?= __('经验值'); ?></p>
                </dd>
            </dl>
			<dl class="row">
                <dt class="tit">
                    <label><?= __('每订单最多赠送经验值'); ?></label>
                </dt>
                <dd class="opt">
					<input id="grade_order" name="grade[grade_order]" value="<?=($data['grade_order']['config_value'])?>" class="ui-input w400" type="text"/>
                    <p class="notic"><?= __('该值为大于'); ?>0<?= __('的数，例'); ?>:<?= __('设置为'); ?>100<?= __('，表明每订单赠送经验值最多为'); ?>100<?= __('经验值'); ?></p>
                </dd>
            </dl>
          <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<script>
  
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>