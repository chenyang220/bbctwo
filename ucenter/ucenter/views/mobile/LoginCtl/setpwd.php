<?php

$callback = urldecode($_GET['callback']);
$us = $_GET['us'];
$ks = $_GET['ks'];
$mobile = $_GET['mobile'];
?>
<!DOCTYPE html>

<html>
<head>
    <title>设置登录密码</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css?v=9">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/wap.css?v=81812721">
    <link rel="stylesheet" type="text/css" href="https://at.alicdn.com/t/font_1778269_y5rqr8ka6en.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/tips.css">
</head>
<body>
    <h3 class="uwap-header"><a href="javascript:history.go(-1)" class="back-pre"><i class="iconfont icon-arrow-left fl"></i></a><span>设置登录密码</span></h3>

    
		<div class="resetps-content">
			<!-- begin -->
			<div class="resetps-item resetps-code-item">
				<span>请设置登录密码</span>
				<div class="flex resetps-password-input">
					<input id="re_user_password" class="flex1" type="password" placeholder="<?=$str?>">
					<i class="iconfont icon-bukeshi btn-open-see"></i>
				</div>
				<p id="password-error" class="resetps-password-tips"></p>
				
			</div>
			<div class="uwap-btn tc">
				<button onclick="resetPasswdClick()" class="btn-uwap-button">完成</button>
				<p class="tc">
					<a class="pass-step" href="<?=$callback.'&us='.$us.'&ks='.$ks?>">跳过</a>
				</p>
			</div>
			<!-- end -->
		</div>
	
	<script type="text/javascript" src="<?= $this->view->js ?>/jquery.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/wap.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
	<script>
		// function resetPasswdClick(){
		// 	var user_password = $('#re_user_password').val();
		// 	var mobile = "<?php echo $mobile?>";
	 //       var reg_checkcode = 1;
	 //       var callback = "<?php echo $callback?>";
	 //       var u = "<?php echo $us?>";
	 //       var k = "<?php echo $ks?>";
	        
		// 	$.post("./index.php?ctl=Login&met=wxappresetPasswdNew&typ=json",{
		// 		"user_password": user_password,
		// 		"mobile": mobile,
		// 		"reg_checkcode":reg_checkcode,
		// 	}, function (data) {
	           
	
		// 		if(data.status == 200)
		// 		{
		// 			alert("<?=__('重置密码成功，请妥善保管新密码！')?>",afterreset);
				
		// 			window.location.href = decodeURIComponent(callback) + '&us=' + encodeURIComponent(u) + '&ks=' + encodeURIComponent(k);
		// 		}else{
		// 			$("#password-error").html(data.msg);
					
					 
		// 		}
		// 	});
		// }
		
		
	    function resetPasswdClick(){
			var user_password = $('#re_user_password').val();
		    var mobile = "<?php echo $mobile?>";
	        var reg_checkcode = 1;
	        
	        var callback = "<?php echo $callback?>";
	        var u = "<?php echo $us?>";
	        var k = "<?php echo $ks?>";
	        
	        
			$.post("./index.php?ctl=Login&met=wxappresetPasswdNew&typ=json", {
				"user_password": user_password,
				"mobile": mobile,
				"reg_checkcode":reg_checkcode,
			}, function (data) {
	           
	
				if(data.status == 200)
				{
					Public.tips.mes('密码设置成功！');
					window.location.href = decodeURIComponent(callback) + '&us=' + encodeURIComponent(u) + '&ks=' + encodeURIComponent(k);
				}else{
					$("#password-error").html(data.msg);
					 
				}
			});
		}
		
	</script>
</body>
</html>