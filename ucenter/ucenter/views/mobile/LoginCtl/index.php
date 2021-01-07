 <?php if (!defined('ROOT_PATH')) exit('No Permission');?>

<?php
$re_url = '';
$re_url = Yf_Registry::get('re_url');

$from = '';
$callback = $re_url;
$t = '';
$type = '';
$act= '';
$code = '';

extract($_GET);

$qq_url = sprintf('%s?ctl=Connect_Bind&met=login&callback=%s&from=%s&type=%s', Yf_Registry::get('url'), urlencode($callback) ,$from,'qq');
$wx_url = sprintf('%s?ctl=Connect_Bind&met=login&callback=%s&from=%s&type=%s', Yf_Registry::get('url'), urlencode($callback) ,$from,'weixin');


$connect_rows = Yf_Registry::get('connect_rows');

$qq = $connect_rows['qq']['status'];
$wx = $connect_rows['weixin']['status'];


?>


<!DOCTYPE html>
<html>
<head>
    <title>登录</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/green.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/tips.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/wap.css?v=81812721">
    <link rel="stylesheet" type="text/css" href="https://at.alicdn.com/t/font_1778269_y5rqr8ka6en.css">
    
</head>
<body>
    <h3 class="uwap-header"><a href="javascript:history.go(-1)" class="back-pre"><i class="iconfont icon-arrow-left fl"></i></a><span>登录</span></h3>
    <div class="tc shopwap-logo"><img src="<?=$mall_logo?>"> </div>
    <form id="formlogin" method="post" onsubmit="return false;">
    	
    	<input type="hidden" id="area_code1">
		<input type="hidden" name="from" class="from" value="<?php echo $from;?>">
		<input type="hidden" name="callback" class="callback" value="<?php echo urlencode($callback);?>">
		<input type="hidden" name="t" class="t" value="<?php echo $t;?>">
		<input type="hidden" name="type" class="type" value="<?php echo $type;?>">
		<input type="hidden" name="act" class="act" value="<?php echo $act;?>">
		<input type="hidden" name="code" class="code" value="<?php echo $code;?>">
		<input type="hidden" name="re_url" class="re_url" value="<?php echo $re_url;?>">
		<!-- 手机号登录 begin-->
	    <div id="login1" class="uwap-login-content" >
	    	<div class="uwap-login-item flex">
	    		<div class="iblock phone-local">
	    			<span>+86</span>
	    		</div>
	    		<input class="flex1 uwap-phone-input lo_user_phone" type="text" maxlength="11" id="phone" name="phone" placeholder="请输入手机号">
	    	</div>
	    	<div class="uwap-login-item flex">
	    		<input class="flex1" type="" id="phone_code" maxlength="6"  name="phone_code" placeholder="输入验证码">
	    		<span  onclick="get_randcode()" class="uwap-btn-code get-code">发送验证码</span>

	    	</div>
	    </div>
		<!-- 手机号登录 end-->
	    <!-- 账号登录 begin-->
		<div id="login2" class="uwap-login-content" style="display:none;">
			<div class="uwap-login-item flex">
				<input class="flex1 uwap-phone-input lo_user_account" type="" name="phone" placeholder="输入用户名/手机号/邮箱">
			</div>
			<div class="uwap-login-item flex">
				<input class="flex1 lo_user_password" type="password" id="re_user_password" name="code" placeholder="输入登录密码">
				<i class="iconfont icon-bukeshi btn-open-see"></i>
		
			</div>
		</div>
		<!-- 账号登录 end-->
		<div class="uwap-btn tc">
	    	<button class="btn-uwap-button" onclick="loginSubmit()">一键登录</button>
	    </div>
	    <p class="clearfix uwap-new-entrance">
			<!-- 账户登录时，才显示忘记密码begin -->
			<a class="fl forget-passpord-entrance" href="<?=sprintf('%s?ctl=Login&act=reset&t=%s&from=%s&callback=%s', Yf_Registry::get('url'), request_string('t'), request_string('from'), urlencode(request_string('callback')))?>">忘记密码</a>
			<!-- 账户登录时，才显示忘记密码end -->
			
			<a class="fr" href="<?=sprintf('%s?ctl=Login&act=reg&t=%s&from=%s&shop_id_wap=%s&callback=%s', Yf_Registry::get('url'), request_string('t'), request_string('from'),request_int('shop_id_wap'), urlencode(request_string('callback')))?>"><em>新用户注册</em><i class="iconfont icon-arrow-right"></i></a>
		</p>

	    <div class="uwap-other-login-content">
	    	<h4 class="tc"><span>其他登录方式</span></h4>
	    	<ul class="uwap-other-login-ul flex">
	    		<li>
	    			<a href="<?=$wx_url;?>">
	    				<img src="<?=$this->view->img?>/wechat.png">
	    				<span>微信登录</span>
	    			</a>
	    		</li>
	    		<li>
	    			<a id="qie1" href="#">
	    				<img src="<?=$this->view->img?>/account.png">
	    				<span id="q1_text">账号登录</span>
	    			</a>
	    		</li>
	    		<!--<li>-->
	    		<!--	<a id="qie2" style="display:none;" href="javasript:;">-->
	    		<!--		<img src="<?=$this->view->img?>/account.png">-->
	    		<!--		<span>手机验证</span>-->
	    		<!--	</a>-->
	    		<!--</li>-->
	    		<li>
	    			<a href="<?=$qq_url;?>">
	    				<img src="<?=$this->view->img?>/qq.png">
	    				<span>QQ登录</span>
	    			</a>
	    		</li>
	    	</ul>
			
	    </div>
    </form>
	<script type="text/javascript" src="<?= $this->view->js ?>/jquery.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/wap.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/jquery.dialog.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/jquery.toastr.min.js"></script>
	<script>
		$(document).ready(function() {
			$from = $(".from").val();
			$callback = $(".callback").val();
			$t = $(".t").val();
			$type = $(".type").val();
			$act = $(".act").val();
			$re_url = $(".re_url").val();
			var k = '';
			var u = '';
		});
	
		var loginStatus = 0;

	
		$(document).on("click",'#qie1', function(){
			
			
			$('#login1').hide();
			$('#login2').show();
			$('#q1_text').html('手机验证');
			$(".btn-uwap-button").html("登录");
			$(this).attr('id','qie2')
			loginStatus = 1;
		});
		
			$(document).on("click",'#qie2', function(){
			
			$('#login1').show();
			$('#login2').hide();
			$('#q1_text').html('账号登录');
			$(".btn-uwap-button").html("一键登录");
			$(this).attr('id','qie1')
			loginStatus = 0;
		});
		
		
	  function get_randcode() {
        //手机号码
        var mobile = $("#phone").val();
        var area_code = 86;
        var ajaxurl = './index.php?ctl=Login&met=getMobileCodeNew&typ=json';
        var yzm = $("#imgCode").val();
		if(area_code == '86' && !/^1[3-9]\d{9}$/.test(mobile)){
            Public.tips.mes('请输入正确的手机号');
            return false;
        }
        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            async: false,
            data: "yzm=" + yzm + "&mobile=" + mobile+"&area_code="+area_code ,
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
                    window.countDown();
                     Public.tips.mes('请查看手机获取验证码！');
                    return false;
                }
            }
        });
	  }
        
       var delayTime = 60;
       msg = "<?=__('获取验证码')?>";
       function countDown()
	   {
            window.randStatus = false;
            delayTime--;
            //$('.btn-phonecode').html(delayTime + "<?=__(' 秒后重新获取')?>");
			$('.get-code').html(delayTime + "<?=__(' 秒后重新获取')?>");
            if (delayTime == 0) {
                delayTime = 60;
                //$('.btn-phonecode').html(msg);
				 $('.get-code').html(msg);
                clearTimeout(t);
                window.randStatus = true;
            } else {
                t = setTimeout(countDown, 1000);
            }
		}

    
    
    
    function loginSubmit()
	{  
	
		var user_phone = $('.lo_user_phone').val();
		var phone_code = $('#phone_code').val();

		var user_account = $('.lo_user_account').val();
		var user_password = $('.lo_user_password').val();
		var auto_login = $('#autoLogin').is(':checked');
		//$("#loginsubmit").html("<?= __('正在登录...'); ?>");
		
        var param = {
            "user_phone": user_phone,
            "phone_code": phone_code,
            "user_account": user_account,
            "user_password": user_password,
            "t": $t,
            "type": $type,
            "auto_login": auto_login,
			"is_mobile":1
        };
       // passwordCookie.passwordErrorCount >= 3 && (param.imgCode = $("#imgCode").val());
        
        // $.ajaxSettings.async = false;
        // $.post("http://im.yuanfeng.cn/api/user/login", {user_name: user_account}, function (e) {
        //     console.log(e);
        // },"jsonp");
        // $.ajaxSettings.async = true;
        //
        	
        if (loginStatus == 0) {
        	
        	$.post("./index.php?ctl=Login&met=phoneLoginNew&typ=json", param,function(data) {
				if(data.status == 200) {
	                k = data.data.k;
	                u = data.data.user_id;
	                //判断用户是否绑定手机号
	                // if (!data.data.mobile) {
	                //     $("#mobile_box").show();
	                // } else {
	                    if ($callback) {
	                        window.location.href = decodeURIComponent($callback) + '&us=' + encodeURIComponent(u) + '&ks=' + encodeURIComponent(k);
	                        
	                        // $.dialog.tips("<?= __('登录成功'); ?>", "1.5", false, false, function () {
	                        //     window.location.href = decodeURIComponent($callback) + '&us=' + encodeURIComponent(u) + '&ks=' + encodeURIComponent(k);
	                        // });
	                        
	                    } else {
	                        window.location.href = decodeURIComponent($re_url);
	                    }
	                // }
	                
				}else{
					 Public.tips.mes(data.msg);
				
				}
			});
        } else {
	        $.post("./index.php?ctl=Login&met=login&typ=json", param,function(data) {
				if(data.status == 200) {
	                k = data.data.k;
	                u = data.data.user_id;
	                //判断用户是否绑定手机号
	                if (!data.data.mobile) {
	                    $("#mobile_box").show();
	                } else {
	                    if ($callback) {
	                        window.location.href = decodeURIComponent($callback) + '&us=' + encodeURIComponent(u) + '&ks=' + encodeURIComponent(k);
	                        
	                        // $.dialog.tips("<?= __('登录成功'); ?>", "1.5", false, false, function () {
	                        //     window.location.href = decodeURIComponent($callback) + '&us=' + encodeURIComponent(u) + '&ks=' + encodeURIComponent(k);
	                        // });
	                        
	                    } else {
	                        window.location.href = decodeURIComponent($re_url);
	                    }
	                }
	                
				}else{
					Public.tips.mes(data.msg);
				}
			});
        }
	}
</script>
</body>
</html>