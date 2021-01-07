<?php $callback=$_GET['callback'];?>
<!DOCTYPE html>
<html>
<head>
    <title>忘记密码</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css?v=9">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/wap.css?v=81812721">
    <link rel="stylesheet" type="text/css" href="https://at.alicdn.com/t/font_1778269_y5rqr8ka6en.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/tips.css">
</head>
<body>
    <h3 class="uwap-header"><a href="javascript:history.go(-1)" class="back-pre"><i class="iconfont icon-arrow-left fl"></i></a><span>忘记密码</span></h3>

    <form action="" onsubmit="return false;">
		<div class="resetps-content">
			<!-- 第一步begin -->
			<div  class="resetps-item resetps-content-item flex one">
				<span>账号</span>
				<input type="text" id="mobile" class="flex1" value="">
				<i class="iconfont icon-close"></i>
			</div>
			<div class="uwap-btn tc one">
				<button class="btn-uwap-button" id="button_one">下一步</button>
			</div>
			<!-- 第一步end -->
			<!-- 第二步begin -->
			<div  class="resetps-item resetps-code-item two" style="display:none;">
				<span>请输入验证码</span>
				<p class="resetps-code-tips">请输入+86 <b id="phone"></b>收到的验证码</p>
				<div class="resetps-code-input flex">
		    		<input id="num1" type="number" class="num-input" maxlength="1">
		    		<input id="num2" type="number" class="num-input" maxlength="1">
		    		<input id="num3" type="number" class="num-input" maxlength="1">
		    		<input id="num4" type="number" class="num-input" maxlength="1">
		    		<input id="num5" type="number" class="num-input" maxlength="1">
		    		<input id="num6" type="number" class="num-input" maxlength="1">
	    		</div>
			</div>
			<div class="uwap-btn tc two" style="display:none;">
				<button class="btn-uwap-button get-code" id="button_two" onclick="get_randcodes()">重新获取验证码</button>
			</div> 
			<!-- 第二步end -->
			<!-- 第三步begin -->
			<div  class="resetps-item resetps-code-item three" style="display:none;">
				<span>请设置新的登录密码</span>
				<div class="flex resetps-password-input">
					<input id="re_user_password" class="flex1" type="password" placeholder="<?=$pwd_str?>">
					<i class="iconfont icon-bukeshi btn-open-see"></i>
				</div>
				<p class="resetps-password-tips"></p>
				
			</div>
			<div class="uwap-btn tc three" style="display:none;">
				<button class="btn-uwap-button" id="button_three">完成</button>
			</div>
			<!-- 第三步end -->
		</div>
		<input type="hidden" value="<?=$callback?>" id="callback">
	</form>
	<script type="text/javascript" src="<?= $this->view->js ?>/jquery.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/wap.js"></script>
	<script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
	<script>
		//验证码自动跳到下个
		$(".num-input").each(function(){
		    $(this).keyup(function(e){
					if($(this).val().length < 1){
						$(this).prev().focus();
					}else{
						if($(this).val().length = 1){
							if($(this).index()<5&&$(this).index()>=0) {
								$(this).next('input').focus();
							}else{
								$(this).blur();
								//检测验证码是否正确
								var ajaxurl = './index.php?ctl=Login&met=newCheckYzm&typ=json';
								var mobile = $("#mobile").val();
								var num1 = $('#num1').val();
								var num2 = $('#num2').val();
								var num3 = $('#num3').val();
								var num4 = $('#num4').val();
								var num5 = $('#num5').val();
								var num6 = $('#num6').val();
								var user_code = num1+num2+num3+num4+num5+num6;
								
								$.ajax({
								    type: "POST",
								    url: ajaxurl,
								    dataType: "json",
								    // async: false,
								    data:  "&mobile=" + mobile+"&user_code="+user_code ,
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
								            $('.one').hide();
											$('.two').hide();
											$('.three').show();
								            	//window.location.href = './index.php?ctl=Login&met=regist2&mobile='+mobile+'&callback='+callback+'&t='+token+'&from='+from+'&re_url='+re_url;
								            return false;
								        }
								    }
								});
							}
						}else if($(this).val().length>1){
							$(this).focus();
						}
					}
		    });
		     
		});
		
		$('#button_one').click(function(){
			get_randcode();
		
		})
		
		$('#button_three').click(function(){
			
			resetPasswdClick();
		})
		
		function resetPasswdClick(){
			var user_password = $('#re_user_password').val();
			var mobile = $('#mobile').val();
	        var reg_checkcode = 1;
	        var callback = $('#callback').val();
			$.post("./index.php?ctl=Login&met=resetPasswdNew&typ=json", {
				"user_password": user_password,
				"mobile": mobile,
				"reg_checkcode":reg_checkcode,
			}, function (data) {
	           
	
				if(data.status == 200)
				{
					alert("<?=__('重置密码成功，请妥善保管新密码！')?>");
					window.location.href = './index.php?ctl=Login&met=index&typ=e&from=wap&callback='+encodeURIComponent(callback);
				}else{
					$('.resetps-password-tips').html(data.msg);
					$("#form-authcode").val("");
					$(".img-code").click();
					 
				}
			});
		}
		
		function get_randcode() {
	   	
	        //手机号码
	        var mobile = $("#mobile").val();
	        var area_code = 86;
	        var ajaxurl = './index.php?ctl=Login&met=getMobileCodeNew&typ=json&login_type=reset';
	        
	        
	        if(!mobile){
		   		 Public.tips.mes('请输入手机号码');
		   		return;
		   	}
		   	
		   
	        $.ajax({
	            type: "POST",
	            url: ajaxurl,
	            dataType: "json",
	            async: false,
	            data:  "&mobile=" + mobile+"&area_code="+area_code ,
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
	                    
	                	var phone=$('#mobile').val();
						$('#phone').html(phone);
						$('.one').hide();
						$('.two').show();
	                    	//window.location.href = './index.php?ctl=Login&met=regist2&mobile='+mobile+'&callback='+callback+'&t='+token+'&from='+from+'&re_url='+re_url;
	                    return false;
	                }
	            }
	        });
	  }
	  
	  //重新获取验证码
	    function get_randcodes() {
	        //手机号码
	        var mobile = $("#phone").html();
	        var ajaxurl = './index.php?ctl=Login&met=getMobileCodeNew&typ=json';
	        var area_code = 86;
	        $.ajax({
	            type: "POST",
	            url: ajaxurl,
	            dataType: "json",
	            async: false,
	            data:  "mobile=" + mobile+"&area_code="+area_code ,
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
	                    //Public.tips.alert('请查看手机短信获取验证码!');
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

		$('.icon-close').click(function(){
		
			$('#mobile').val('');
		
		})
		
	</script>
</body>
</html>