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
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link rel="stylesheet" href="<?=$this->view->css?>/page.css?v=361">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
 <script type="text/javascript" src="<?= $this->view->js ?>/controllers/config.js" charset="utf-8"></script>
</head>

<body class="<?=$skin?>">
	<form name="form"  id="manage-form" action="#" method="post">
	    <input type="hidden" name="config_type[]" value="theme_column"/>
		<input type="hidden" name="theme_column[theme_page_color]" id="theme_page_color" value="<?=$data['theme_page_color']['config_value']?>" />
		<input type="hidden" name="theme_column[theme_page_color_back]" id="theme_page_color_back" value="<?=$data['theme_page_color_back']['config_value']?>" />
		<div class="ncap-form-default mt60">
			<dl class="row">
				<dt class="tit"><?= __('前台色彩风格：'); ?></dt>
				<dd class="opt">
					<ul class="color_list fn-clear color_list_font">
					<?php
						if(!empty($data['color_bg'])){
						foreach($data['color_bg'] as $key => $value){
						
					 ?>
						<li date-id="<?=$key?>" class="tc <?=$key?> <?php if(isset($data['config']) && $key == $data['config']){?>selected<?php }?>">
								<span></span>
								<i class="iconfont">&#xe61d;</i><?=$value?>
						   
						<?php }}?>
						</li>
					</ul>
					
				</dd>
			</dl>
			<dl class="row">
				<dt class="tit"><?= __('后台色彩风格：'); ?></dt>
				<dd class="opt">
					<ul class="color_list fn-clear color_list_back">
						<li date-id="themes-1" class="tc defalut <?php if($data['config_back']=='themes-1'){?>selected<?php }?>">
							<span></span>
							默认
							<i class="iconfont">&#xe61d;</i>
						</li>
						<li date-id="themes-2" class="tc eblue <?php if($data['config_back']=='themes-2'){?>selected<?php }?>">
							<span></span>
							科技蓝
							<i class="iconfont">&#xe61d;</i>
						</li>
						<li date-id="themes-3" class="tc lz <?php if($data['config_back']=='themes-3'){?>selected<?php }?>">
							<span></span>
							蓝紫
							<i class="iconfont">&#xe61d;</i>
						</li>
						<li date-id="themes-4" class="tc hc <?php if($data['config_back']=='themes-4'){?>selected<?php }?>">
							<span></span>
							红赤
							<i class="iconfont">&#xe61d;</i>
						</li>
						<li date-id="themes-5" class="tc ql <?php if($data['config_back']=='themes-5'){?>selected<?php }?>">
							<span></span>
							青绿
							<i class="iconfont">&#xe61d;</i>
						</li>
						<li date-id="themes-6" class="tc xrk <?php if($data['config_back']=='themes-6'){?>selected<?php }?>">
							<span></span>
							向日葵色
							<i class="iconfont">&#xe61d;</i>
						</li>
						<li date-id="themes-7" class="tc lc <?php if($data['config_back']=='themes-7'){?>selected<?php }?>">
							<span></span>
							露草色
							<i class="iconfont">&#xe61d;</i>
						</li>
					</ul>
					<p class="hits mt10"><?= __('选择板块色彩风格将影响商城首页的边框、背景色、字体色彩，但不会影响板块的内容布局。'); ?></p>
					<a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn mt20"><?= __('确认提交'); ?></a>
				</dd>
			</dl>
			
		</div>
	</form>

    <script>
        $(".color_list_font li").click(function(){
			$(this).addClass('selected').siblings().removeClass('selected');
			$("input[name='theme_column[theme_page_color]']").val($(this).attr("date-id"));
		});
		$(".color_list_back li").click(function(){
			$(this).addClass('selected').siblings().removeClass('selected');
			$("input[name='theme_column[theme_page_color_back]']").val($(this).attr("date-id"));
		});
    </script>
   
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
