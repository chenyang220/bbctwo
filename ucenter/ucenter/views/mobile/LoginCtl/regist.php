<?php
$re_url = '';
$re_url = Yf_Registry::get('re_url');
$from = $_REQUEST['callback'];
$callback = $from ?: $re_url;
$t = '';
$code = '';
extract($_GET);
?>
<!DOCTYPE html>
<html>
<head>
    <title>手机快速注册</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css?v=9">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/wap.css?v=7">
	<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/slide.css?v=7">
	<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/tips.css">
	<link rel="stylesheet" href="<?= $this->view->css ?>/drag.css">
    <link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_1778269_d99acvse5t.css">
</head>
<body>
    <h3 class="uwap-header"><a href="javascript:history.go(-1)" class="back-pre"><i class="iconfont icon-arrow-left fl"></i></a><span>手机快速注册</span></h3>
    <input type="hidden" name="from" class="from" value="<?php echo $from; ?>">
    <input type="hidden" name="callback" class="callback" value="<?php echo urlencode($callback); ?>">
    <input type="hidden" name="t" class="token" value="<?php echo $t; ?>">
    <input type="hidden" name="code" class="code" value="<?php echo $code; ?>">
    <input type="hidden" name="re_url" class="re_url" value="<?php echo $re_url; ?>">
    <div class="uwap-login-content">
    	<div class="uwap-login-item flex">
    		<div class="iblock phone-local">
    			<span>+86</span>
    		</div>
    		<input class="flex1 uwap-phone-input" type="" id="phone" maxlength="11" name="phone" placeholder="请输入手机号">
    	</div>
		<!-- 滑动验证 -->
		<div class="drag-box mb-30">
		    <div id="drag">
		        <div class="drag_bg"></div>
		        <div class="drag_text slidetounlock" onselectstart="return false;" unselectable="on">
		            请按住滑块，拖动到最右边
		        </div>
		        <div class="handler handler_bg"></div>
		    </div>
		</div>
		
		<div class="uwap-btn tc">
			<button id="next" onclick="get_randcode()" class="btn-uwap-button mb-0">同意协议并注册</button> <!--class= active -->
		</div>
		<p class="uwap-agreement">已阅读并同意以下协议：<a class="btn-open-agreement" href="javascript:;">《用户注册协议》</a>与 <a class="btn-open-agreements" href="javascript:;">《用户隐私协议》</a></p>                  
	</div>
	<!-- 用户注册协议begin -->
	<div class="uwap-alert-dialog" id="reg_protocol">
		<div class="uwap-alert-dialog-box">
			<i class="mask"></i>
			<div class="uwap-alert-content uwap-regist-agreement-text">
				<h4 class="tc">用户注册协议 <i class="iconfont icon-close btn-close-mask"></i></h4>
				<div class="uwap-alert-body">
					<?php echo $reg_row['reg_protocol']['config_value']?>
				</div>
			</div>
		</div>
	</div>
	<!-- 用户注册协议end-->

	<!-- 用户注册协议begin -->
	<div class="uwap-alert-dialog uwap-alert-dialogs" id="privacy_protocol">
		<div class="uwap-alert-dialog-box">
			<i class="mask"></i>
			<div class="uwap-alert-content uwap-regist-agreement-text">
				<h4 class="tc">用户隐私协议 <i class="iconfont icon-close btn-close-mask"></i></h4>
				<div class="uwap-alert-body">
					<?php echo $reg_row['privacy_protocol']['config_value']?>
				</div>
			</div>
		</div>
	</div>
	<!-- 用户注册协议end-->
	
	
	<script type="text/javascript" src="<?= $this->view->js ?>/jquery.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/wap.js?=1"></script>
	<script src="https://apps.bdimg.com/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="<?= $this->view->js ?>/jquery.mobile.min.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
	<script src="<?= $this->view->js ?>/drag.js"></script>
	<script>
		var from = $(".from").val();
	    var callback = $(".callback").val();
	    var token = $(".token").val();
	    var re_url = $(".re_url").val();
	    
	    $('#drag').drag();
	    
	   
	    
	    
	   function get_randcode() {
	   	
        //手机号码
        var mobile = $("#phone").val();
        var area_code = 86;
        var ajaxurl = './index.php?ctl=Login&met=getMobileCodeNew&typ=json';
        if(area_code == '86' && !/^1[3-9]\d{9}$/.test(mobile)){
            Public.tips.mes('请输入正确的手机号');
            return false;
        }
        
        if(!mobile){
	   		Public.tips.mes('请输入手机号码');
	   		return;
	   	}
	   	
	   	var drag_text = $('.drag_text').text();
	   	if(drag_text!='验证通过'){
	   		Public.tips.mes('请通过验证');
	   		return;
	   	}
        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            async: false,
            data:  "&mobile=" + mobile+"&area_code="+area_code+"&login_type=reg" ,
            success: function (respone) {
                if (respone.status == 250) {
                    //$(".msg-warn").hide();
					//$(".msg-error").html('<b></b>'+respone.msg);
					//$(".msg-wrap").show();
					//$(".msg-error").show();
					Public.tips.mes(respone.msg);
				
                } else {
                    //$(".msg-warn").show();
					//$(".msg-error").html('');
					//$(".msg-wrap").hide();
					//$(".msg-error").hide();
                    //window.countDown();
                    //Public.tips.alert('请查看手机短信获取验证码!');
                    Public.tips.mes('请查看手机短信获取验证码!');
                    	window.location.href = './index.php?ctl=Login&met=regist2&mobile='+mobile+'&callback='+callback+'&t='+token+'&from='+from+'&re_url='+re_url;
                    return false;
                }
            }
        });
	  }
	</script>
</body>
</html>