<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>

<style>
    .webuploader-pick{ padding:1px; }
    
</style>
</head>
<body class="<?=$skin?>">
<div class="">
    <form method="post" enctype="multipart/form-data" id="score-form" name="form">
        <div class="ncap-form-default">
			<dl class="row">
				<dt class="tit">
					  <label><em>*</em><?= __('修改积分'); ?></label>
				</dt>
				<dd class="opt">
				  <input type="radio" id="add" name="way_for" value="1" checked>
				  <label for="add"><?= __('增加'); ?></label>
				  <input type="radio" id="reduce" name="way_for" value="2" >
				  <label for="reduce"><?= __('减少'); ?></label>
				  <span class="err"></span>
				</dd>
			</dl>
            <dl class="row">
                <dt class="tit">
                    <label><em>*</em><?= __('积分值'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="score" name="score" class="ui-input w400" type="text"/>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('描述'); ?></label>
                </dt>
                <dd class="opt">
                    <textarea name="score_desc" id="score_desc" rows="6" class="tarea" length="10" placeholder="最多输入10个字，需描述增减原因"></textarea>
                </dd>
            </dl>
        </div>
    </form>
</div>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/user/info/score.js" charset="utf-8"></script>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>