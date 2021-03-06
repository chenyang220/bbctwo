<?php if (!defined('ROOT_PATH'))
{
	exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
<link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<style>
	
	.dx-warning {
		background: #FFF;
		border: 5px solid #ffba00;
		padding: 20px;
		margin-bottom: 30px
	}
	
	.dx-warning h2 {
		margin: 0;
		margin-bottom: 20px;
		padding-bottom: 15px;
		border-bottom: 2px solid #f0f0f0
	}
	
	.dx-warning ol {
		margin-top: 20px
	}
	
	.dx-warning li {
		margin: 5px 0
	}

</style>
</head>
<body>
<div class="wrapper page">
	<div class="fixed-bar">
		<div class="item-title">
			<div class="subject">
				<h3><?= __('版本管理');?>&nbsp;</h3>
				<h5><?= __('更新');?></h5>
			</div>
			<ul class="tab-base nc-row">
                <li><a class="current" href="<?= Yf_Registry::get('url') ?>?ctl=Config&met=update"><span><?= __('更新管理中心');?></span></a></li>
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Config&met=updateUcenter"><span><?= __('更新用户中心');?></span></a></li>
			</ul>
		</div>
	</div>

	<!-- <?= __('操作说明');?> -->
	<p class="warn_xiaoma"><span></span><em></em></p>
	<div class="explanation" id="explanation">
		<div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
			<h4 title="<?= __('提示相关设置操作时应注意的要点');?>"><?= __('操作提示');?></h4>
			<span id="explanationZoom" title="<?= __('收起提示');?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
		<ul>
			<li></li>
			<li>&nbsp;</li>
		</ul>
	</div>
	
	<?php
	if ( $update )
	{
		ob_end_flush();
		
		
		$allow_relaxed_file_ownership = false;
		
		
		try{
			
			$result = $upgrader->upgrade( $update, array(
				'allow_relaxed_file_ownership' => $allow_relaxed_file_ownership
			) );
			
			
			$time = 2;
			$url = "<?= Yf_Registry::get('url') ?>?ctl=Config&met=update";
			
			//header("Refresh:{$time}; url={$url}");
			
		}
		catch(Exception $e)
		{
		    update_feedback($e->getMessage());
		    update_feedback("<p><?= __('安装失败！');?></p>");
		    update_feedback("<p><a class='ui-btn' id='reinstall'><?= __('重新安装');?><i class='iconfont'></i></a></p>");
			//print_r($e->getMessage());
		}
	}
	?>

</div>


<script>
    $('#reinstall').on("click", function (e)
    {
        window.location.href = "<?= Yf_Registry::get('url') ?>?ctl=Config&met=update&upgrade=1";
    });


</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>


