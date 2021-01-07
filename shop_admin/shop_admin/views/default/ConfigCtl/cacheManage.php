<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';

// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
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
				<?php include __DIR__.'/config_comm_menu.php';?>
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
 
    
	<div class="license" align="center">
		<div id="loader"></div>
	</div>
	<div class="bot bot_reset"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn" onclick="check(2);"><?= __('清除全部缓存'); ?></a></div>
</div>

<script type='text/javascript' src='<?=$this->view->js_com?>/plugins/jquery.percentageloader-0.1.min.js?ver=1.12.3'></script>

<div id="container">
	<script>

		var $topLoader = null;
        var $topLoader1 = null;
		var topLoaderRunning = false;
		var check_data = '';
		var kb = 0;
		var totalKb = 999;


		$(function ()
		{
			$topLoader = $("#loader").percentageLoader({
				width: 256, height: 256, value: '<?= __('清理进度'); ?>', controllable: true, progress: 0, onProgressUpdate: function (val)
				{
					$topLoader.setValue(Math.round(val * 100.0));
				}
			});
			$("#loader").slideDown();
            
            $topLoader1 = $("#loader1").percentageLoader({
				width: 200, height: 200, value: '<?= __('清理进度'); ?>', controllable: true, progress: 0, onProgressUpdate: function (val)
				{
					$topLoader1.setValue(Math.round(val * 100.0));
				}
			});
			$("#loader1").slideDown();
		});

		function  check(flag)
		{
            if(flag == 1){
                var url = "./index.php?ctl=Config&met=cacheIndex&typ=json";
                var obj = $("#loader1");
                var topLoader = $topLoader1;
            } else {
                var url = "./index.php?ctl=Config&met=cache&typ=json";
                var obj = $("#loader");
                var topLoader = $topLoader;
            }
			kb = 0;
			$.ajax({
				type: "POST",
				url: url,
				data: {},
				dataType: "html",
				beforeSend: function (XMLHttpRequest)
				{
					if (topLoaderRunning)
					{
						return;
					}
					topLoaderRunning = true;
					topLoader.setProgress(0);
					var animateFunc = function ()
					{
						kb += 3;
						topLoader.setProgress(kb / totalKb);

						if (kb < totalKb)
						{
							setTimeout(animateFunc, 25);
						}
						else
						{
							topLoaderRunning = false;

							obj.slideToggle(function(){
								obj.slideToggle();
							});
						}
					}
					setTimeout(animateFunc, 25);
				},

				success: function (msg)
				{
					check_data = msg;

					if (kb < 700)
					{
						kb = 700;
					}
				},

				complete: function (XMLHttpRequest, textStatus)
				{
					kb = 999;
				},

				error: function (e, x)
				{
					kb = 999;
				}
			});
		}

	</script>
</div>

<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>