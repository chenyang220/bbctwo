<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<style>
body{background: #fff;}
dd.opt {
    line-height: 20px !important;
}
</style>
</head>
<body/>
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em> </div>
            <ul>
              <li><?= __('关联规格不是必选项，它会影响商品发布时的规格及价格的录入。不选为没有规格。'); ?></li>
              <li><?= __('关联品牌不是必选项，它会影响商品发布时的品牌选择。'); ?></li>
              <li><?= __('属性值可以添加多个，每个属性值之间需要使用逗号隔开。'); ?></li>
              <li><?= __('选中属性的“显示”选项，该属性将会在商品列表页显示。'); ?></li>
              <li><?= __('自定义属性只需要填写属性名称，属性值由平台自行填写，商家可勾选。注意：自定义属性不作为商品检索项使用。'); ?></li>
              <li><?= __('选中“删”后复选框提交后该条信息将被删除。'); ?></li>
            </ul>
        </div>
    </div>
    <form id="type_form" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" name="t_id" value="3" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="type_name"><em>*</em><?= __('类型'); ?></label>
                </dt>
                <dd class="opt">
                <input type="text" class="ui-input n-valid" name="type_name" id="type_name" value="" />
                <span class="err"></span>
                <p class="notic"></p>
            </dl>
            <!--<dl class="row">
                <dt class="tit" colspan="2">
                  <label class="" for="s_sort"><?= __('快捷定位'); ?></label>
                </dt>
                <?= __('请选择'); ?><input/>
            </dl>-->
            <dl class="row">
                <dt class="tit">
                    <label for="type_displayorder"><em>*</em><?= __('排序'); ?></label>
                </dt>
                <dd class="opt">
                    <input type="text" class="ui-input n-valid" name="type_displayorder" id="type_displayorder" value=""/>
                    <span class="err"></span>

                    <p class="notic"><?= __('请填写自然数。类型列表将会根据排序进行由小到大排列显示。'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('选择关联规格'); ?></label>
                </dt>
                <dd class="opt">
                    <div id="type_spec"></div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="type_brand"><?= __('选择关联品牌'); ?></label>

                </dt>
                <dd class="opt">
                    <div id="type_brand"></div>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="member_name"><?= __('添加属性'); ?></label>
                </dt>
                <dd class="opt">
					<div class="mod-search cf">
						<div class="fr">
							<a href="#" class="ui-btn ui-btn-sp" id="btn-add"><?= __('新增'); ?><i class="iconfont icon-btn03"></i></a>
						</div>
					</div>
					<div class="grid-wrap">
                   		<table id="grid"></table>
                	</div>
                </dd>

            </dl>
        </div>
        <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn" id="submit_data"><?= __('确认提交'); ?></a></div>
    </form>
<script type="text/javascript">

</script>

<script type="text/javascript" src="<?=$this->view->js?>/controllers/goods/type_manage.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>