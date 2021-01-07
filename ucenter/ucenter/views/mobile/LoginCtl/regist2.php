<?php
$re_url = '';
$re_url = Yf_Registry::get('re_url');
$from = $_REQUEST['callback'];
$callback = $from ?: $re_url;
$t = '';
$code = '';
$mobile = $_REQUEST['mobile'];
extract($_GET);
?>
<!DOCTYPE html>
<html>
<head>
    <title>新用户注册</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css?v=9">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/wap.css?v=81812721">
    <link rel="stylesheet" type="text/css" href="https://at.alicdn.com/t/font_1778269_y5rqr8ka6en.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/tips.css">
</head>
<body>
    
    <input type="hidden" name="from" class="from" value="<?php echo $from; ?>">
    <input type="hidden" name="callback" class="callback" value="<?php echo urlencode($callback); ?>">
    <input type="hidden" name="t" class="token" value="<?php echo $t; ?>">
    <input type="hidden" name="code" class="code" value="<?php echo $code; ?>">
    <input type="hidden" name="re_url" class="re_url" value="<?php echo $re_url; ?>">
    <h3 class="uwap-header"><a href="javascript:history.go(-1)" class="back-pre"><i class="iconfont icon-arrow-left fl"></i></a><span>新用户注册</span></h3>
    <!--begin -->
    <div class="resetps-item resetps-code-item">
        <span>请输入验证码</span>
        <p class="resetps-code-tips">请输入+86 <b id="mobile"><?php echo $mobile; ?></b></b>收到的验证码</p>
        <div class="resetps-code-input flex">
            <input id="num1" type="number" class="num-input" maxlength="1">
            <input id="num2" type="number" class="num-input" maxlength="1">
            <input id="num3" type="number" class="num-input" maxlength="1">
            <input id="num4" type="number" class="num-input" maxlength="1">
            <input id="num5" type="number" class="num-input" maxlength="1">
            <input id="num6" type="number" class="num-input" maxlength="1">
        </div>
    </div>
    <div class="uwap-btn tc">
        <button  class="btn-uwap-button get-code" onclick="get_randcodes()">重新获取验证码</button><!--class= active -->
    </div>
  
    <!-- end -->
    <script type="text/javascript" src="<?= $this->view->js ?>/jquery.js"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/wap.js"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
    <script>
        //验证码自动跳到下个
		$(".num-input").each(function(){
			$(this).focus(function(){
				$(this).val(' ');
			})
			
		    $(this).keyup(function(e){
					if($(this).val().length < 1){
						$(this).prev().focus();
					}else{
						if($(this).val().length == 1){
							if($(this).index()<5&&$(this).index()>=0) {
								if($(this).next().val().length<1){
									$(this).next().focus();
								}else{
									$(this).blur();
								}
							}else{
								$(this).blur();
								regist();
							}
						}else if($(this).val().length>1){
							$(this).val(' ');
						}
					}
		    });
			
		     
		});
		
        
        
        
        function regist(){
            var callback = $('.callback').val();
            var re_url = $('.re_url').val();
            var mobile = $('#mobile').text();
            var token =  $('.token').val();
            var from = $('.from').val();
            var register_obj = {};
            
            var num1 = $('#num1').val();
            var num2 = $('#num2').val();
            var num3 = $('#num3').val();
            var num4 = $('#num4').val();
            var num5 = $('#num5').val();
            var num6 = $('#num6').val();
            var user_code = num1+num2+num3+num4+num5+num6;
            register_obj = $.extend(register_obj, {"mobile": mobile,"token":token,"user_code":user_code});

            $.post("./index.php?ctl=Login&met=newReg&typ=json", register_obj, function (data) {
                if (data.status == 200) {
                    k = data.data.k;
                    u = data.data.user_id;
                   
                        window.location.href = './index.php?ctl=Login&met=setpwd&typ=e&us='+ encodeURIComponent(u) + '&ks=' + encodeURIComponent(k)+'&callback='+callback+'&mobile='+mobile;
                       // window.location.href = decodeURIComponent(callback) + '&us=' + encodeURIComponent(u) + '&ks=' + encodeURIComponent(k);
                       // window.location.href = decodeURIComponent(re_url);
                   
                } else {
                    /*$(".img-code").click();*/
                    Public.tips.mes(data.msg);
                    return false;
                }
            });
            
        }
        
        
         //重新获取验证码
        function get_randcodes() {
            //手机号码
            var mobile = $("#mobile").html();
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
        
    </script>
</body>
</html>