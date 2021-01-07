<!DOCTYPE html>
<html>
<head>
    <title>绑定手机号</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/base.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/wap.css?v=4">
    <link rel="stylesheet" type="text/css" href="https://at.alicdn.com/t/font_1778269_y5rqr8ka6en.css">
     <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/tips.css">
</head>
<body>
   <h3 class="uwap-header"><a href="javascript:history.go(-1)" class="back-pre"><i class="iconfont icon-arrow-left fl"></i></a><span>绑定手机号</span></h3>
   
       <div class="uwap-login-content">
        <div class="uwap-login-item flex">
          <div class="iblock phone-local">
            <span>+86</span>
          </div>
          <input class="flex1 uwap-phone-input" type="" name="phone" id="phone" placeholder="请输入手机号">
        </div>
        <div class="uwap-login-item flex">
          <input class="flex1" type="" name="code" id="code" placeholder="输入验证码">
          <span class="uwap-btn-code get-code" onclick="get_randcode()">获取验证码</span>
        </div>
       <p style="display:none;" class="uwap-text-tips">
        <b></b>
        <em>验证码已发送，可能会有延后，请耐心等待</em>
      </p>
       </div>
       
    <div class="uwap-btn tc">
        <button onclick="bindphone()" class="btn-uwap-button">确认</button>
      </div>
   
   <script type="text/javascript" src="<?= $this->view->js ?>/jquery.js"></script>
   <script type="text/javascript" src="<?= $this->view->js ?>/wap.js"></script>
   <script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
   <script>
     randStatus =true;
     function get_randcode() {
        //手机号码
        if(randStatus){
          
        var mobile = $("#phone").val();
        var ajaxurl = './index.php?ctl=Login&met=getMobileCodeNew&typ=json';
        var area_code = 86;
        if(area_code == '86' && !/^1[3-9]\d{9}$/.test(mobile)){
            Public.tips.mes('请输入正确的手机号');
            return false;
        }
        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            async: false,
            data: "mobile=" + mobile ,
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
                    //Public.tips.mes(respone.data.user_code);
                    $('.uwap-text-tips').show();
                    return false;
                }
            }
        });
        }
    }
        
       var delayTime = 60;
       msg = "<?=__('获取验证码')?>";
       function countDown()
     {
            randStatus = false;
            delayTime--;
            //$('.btn-phonecode').html(delayTime + "<?=__(' 秒后重新获取')?>");
      $('.get-code').html(delayTime + "<?=__(' 秒后重新获取')?>");
            if (delayTime == 0) {
                delayTime = 60;
                //$('.btn-phonecode').html(msg);
         $('.get-code').html(msg);
                clearTimeout(t);
               randStatus = true;
                
            } else {
                t = setTimeout(countDown, 1000);
                
            }
    }
    
    //绑定手机号
   function bindphone() {
        //手机号码
      
          
        var mobile = $("#phone").val();
        var user_code = $("#code").val();
        var ajaxurl = './index.php?ctl=Login&met=bindRegistNew&typ=json&type=wap_wx';
        var token = "<?php echo $_GET['token']?>";
        var backUrl = "<?php echo $_GET['callback_url']?>";
        var area_code = 86;
        if(area_code == '86' && !/^1[3-9]\d{9}$/.test(mobile)){
            Public.tips.mes('请输入正确的手机号');
            return false;
        }
        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            async: false,
            data: {mobile:mobile,token:token,code:user_code},
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
                    Public.tips.mes('绑定成功');
          window.location.href =  decodeURIComponent(backUrl)+ '&us=' + respone.data.user_id + '&ks=' + respone.data.k;
                    //Public.tips.alert('请查看手机短信获取验证码!');
                    return false;
                }
            }
        });
        }
    

   </script>
</body>
</html>