<div>
    <div class="login-form">
        <div class="login-tab login-tab-r">
            <a id="userlogin" href="javascript:void(0)" class="checked">
                <?= __('账户登录') ?>
            </a> 
            <a id="phonelogin" href="javascript:void(0)" >
                <?= __('手机登陆')?>
            </a>            
            <a href="<?= Yf_Registry::get('url') ?>?ctl=Login&met=reg" target="_blank" class="fr back-to-regist"><b></b><?= __('没有账号？去注册 》') ?></a>
        </div>
        <div class="login-box" style="visibility: visible;">
            <div class="mt tab-h" style="display:none;">
            </div>
            <div class="msg-wrap" style="display:none;">
                <div class="msg-error"></div>
            </div>

            
            <div class="mc">
                <div class="form">
                    <form id="formlogin" method="post" onsubmit="return false;">

                        <div id="userphone" class="item item-fore1" style="display:none;">
                            <label for="loginname" class="login-label name-label"></label>
                            <span class="clear-btn clear-icon js_clear_btn"></span>
                            <input id="phone" type="text" class="itxt lo_user_phone" name="phone" tabindex="1" autocomplete="off" placeholder="<?= __('请输入手机号');?>">
                        </div>
                        <div id="user" class="item item-fore1">
                            <label for="loginname" class="login-label name-label"></label> <input id="loginname" class="lo_user_account" type="text" class="itxt" name="loginname" tabindex="1" autocomplete="off" placeholder="<?= __('邮箱/用户名/已验证手机') ?>"> <span class="clear-btn"></span>
                        </div>
                        <div id="entry" class="item item-fore2" style="visibility: visible;">
                            <label class="login-label pwd-label" for="nloginpwd"></label> <input type="password" class="lo_user_password" id="nloginpwd" name="nloginpwd" class="itxt itxt-error" tabindex="2" autocomplete="off" placeholder="<?= __('密码') ?>"> <span class="clear-btn"></span> <span class="capslock" style="display: none;"><b></b><?= __('大小写锁定已打开') ?></span>
                        </div>
                        <div id="imgCodeVerification" class="item item-fore2 hide">
                            <label class="login-label pwd-label" for="imgCode"></label>
                            <input id="imgCode" type="text" class="itxt" name="img_code" tabindex="3" maxlength="4" placeholder="<?= __('验证码')?>">
                            <img onclick="get_randfunc(this);" class="img-code2" src="./libraries/rand_func.php">
                        </div>
                        <div id="phoneCode" class="item item-fore2 hide">
                            <label class="login-label pwd-label"></label>
                            <input id="phone_code" type="text" class="itxt" name="phone_code" tabindex="3" maxlength="6" placeholder="<?= __('请输入手机验证码')?>">
                            <button id="getPhoneCode" class="btn-phonecode" type="button" onclick="get_randcode()"><?= __('获取验证码')?></button>
                        </div>
                        <div class="clearfix">
                                <span class="fl">
                                    <input id="autoLogin" name="auto_login" type="checkbox" class="yfcheckbox" tabindex="3">
                                    <label for="">自动登录</label>
                                </span> <a class="fr" href="<?= Yf_Registry::get('ucenter_api_url') ?>?ctl=Login&act=reset"><?= __('忘记密码') ?></a>
                        </div>
                        <div class="item item-fore5">
                            <div class="login-btn">
                                <a href="javascript:;" onclick="loginSubmit()" class="btn-img btn-entry" id="loginSubmit" tabindex="6"><?= __('登&nbsp;&nbsp;&nbsp;&nbsp;录') ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="coagent" style="display: block; visibility: visible;">
            <div class="titlea"> 其他登录方式</div>
            <ul>
                <?php if ($qq_status == 1) { ?>
                    <a href="javascript:;" onclick="bindLogin('qq')">
                        <li class="bg-1 qq"></li>
                    </a>
                <?php } ?>
                <?php if ($wx_status == 1) { ?>
                    <a href="javascript:;" onclick="bindLogin('wx')">
                        <li class="bg-1 wb"></li>
                    </a>
                <?php } ?>
                <?php if ($wb_status == 1) { ?>
                    <a href="javascript:;" onclick="bindLogin('wb')"">
                    <li class="bg-1 wx"></li></a>
                <?php } ?>
            </ul>
            <div class="extra-r fr">
                <div>
                    <div class="regist-link pa"></div>
                </div>
            </div>
        </div>
        <a class="btn-close"></a>
    </div>
</div>
<span class="mask"></span>
<script>
    var loginStatus = 1;

    $('#phonelogin').click(function(){
        loginStatus = 0;
        $(".msg-warn").show();
        $(".msg-error").html('');
        $(".msg-wrap").hide();
        $(".msg-error").hide();
        $('#user').css('display','none');
        $('#entry').addClass('hide').removeAttr('style');
        $('#userphone').css('display','block');
        $('#userlogin').removeClass('checked');
        $('#phonelogin').addClass('checked');
        $("#imgCodeVerification").removeClass('hide').css('visibility', 'visible');
        $('#phoneCode').removeClass('hide').css('visibility', 'visible');
        $('#loginname').val('');
        $('#nloginpwd').val('');
    });

    $('#userlogin').click(function(){
        loginStatus = 1;
        $(".msg-warn").show();
        $(".msg-error").html('');
        $(".msg-wrap").hide();
        $(".msg-error").hide();
        $('#user').css('display','block');
        $('#entry').removeClass('hide').css('visibility', 'visible');
        $('#userphone').css('display','none');
        $('#userlogin').addClass('checked');
        $('#phonelogin').removeClass('checked');
        $("#imgCodeVerification").addClass('hide').removeAttr('style');
        $('#phoneCode').addClass('hide').removeAttr('style');
        $('#phone').val('');
        $('#imgCode').val('');
        $('#phone_code').val('');
    });

    //图形验证码
    function get_randfunc(obj)
    {
        var sj = new Date();
        url = obj.src;
        obj.src = url + '?' + sj;
    }

    function get_randcode() {
        //手机号码
        var mobile = $("#phone").val();
        var area_code = $('#area_code1').val();
        var ajaxurl = './index.php?ctl=Login&met=getMobileCode&typ=json';
        var yzm = $("#imgCode").val();
        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            async: false,
            data: "yzm=" + yzm + "&mobile=" + mobile+"&area_code="+area_code ,
            success: function (respone) {
                if (respone.status == 250) {
                    $(".msg-warn").hide();
                    $(".msg-error").html('<b></b>'+respone.msg);
                    $(".msg-wrap").show();
                    $(".msg-error").show();
                } else {
                    $(".msg-warn").show();
                    $(".msg-error").html('');
                    $(".msg-wrap").hide();
                    $(".msg-error").hide();
                    window.countDown();
                    Public.tips.alert('请查看手机短信获取验证码!');
                    return false;
                }
            }
        });

    }

    $(".btn-close").click(function () {
        $("#login_content").hide();
        
        $(".msg-wrap").hide();
        $(".lo_user_account").val("");
        $(".lo_user_password").val("");
    });
    
    $("#formlogin").keydown(function (e) {
        var e = e || event,
            keycode = e.which || e.keyCode;
        
        if (keycode == 13) {
            loginSubmit();
        }
    });
    
    //检验验证码是否正确
    
    //登录按钮
    function loginSubmit() {
        var user_phone = $('.lo_user_phone').val();
        var phone_code = $('#phone_code').val();

        var user_account = $(".lo_user_account").val();
        var user_password = $(".lo_user_password").val();
        var auto_login = $('#autoLogin').is(':checked');
        $("#loginsubmit").html("<?=__('正在登录...')?>");
        
        if (loginStatus == 0) {

            var param = {
                "user_phone": user_phone,
                "phone_code": phone_code,
                "user_account": user_account,
                "user_password": user_password,
                "auto_login": auto_login
            };
            $.post("./index.php?ctl=Login&met=phoneLogin&typ=json", param,function(data) {
                if(data.status == 200) {
                    $.post(SITE_URL + "?ctl=Login&met=check&typ=json",{us:data.data.id,ks:data.data.k},function (e) {});
                    $("#login_content").hide();
                    login_url = UCENTER_URL + "?ctl=Login&met=ImLogin&user_account=" + data.data.user_name + "&user_password=" + data.data.password;
                    
                    login_url = login_url + "&from=shop&callback=" + encodeURIComponent(window.location.href);
                    
                    window.location.href = login_url;
                }else{
                    $(".msg-warn").hide();
                    $(".msg-error").html('<b></b>'+data.msg);
                    $(".msg-wrap").show();
                    $(".msg-error").show();
                    $("#loginsubmit").html("<?= _('登    录'); ?>");
                }
            });
        } else {
            $.post(UCENTER_URL + "?ctl=Login", {"met": "login", "typ": "json", "user_account": user_account, "user_password": user_password}, function (data) {
                console.info(data);
                if (data.status == 200) {
                    $("#login_content").hide();
                    login_url = UCENTER_URL + "?ctl=Login&met=ImLogin&user_account=" + user_account + "&user_password=" + user_password;
                    
                    login_url = login_url + "&from=shop&callback=" + encodeURIComponent(window.location.href);
                    
                    window.location.href = login_url;
                    // window.location.reload();
                } else {
                    $(".msg-warn").hide();
                    $(".msg-error").html("<b></b>" + data.msg);
                    $(".msg-wrap").show();
                    $(".msg-error").show();
                    $("#loginsubmit").html("登&nbsp;&nbsp;&nbsp;&nbsp;录");
                }
            });
        }
    }
    
    function bindLogin(type) {
        if (type == "qq") {
            window.location.href = "<?=Yf_Registry::get('ucenter_api_url')?>?ctl=Connect_Qq&met=login&callback=" + encodeURIComponent(window.parent.document.URL) + "&from=shop";
        }
        
        if (type == "wx") {
            window.location.href = "<?=Yf_Registry::get('ucenter_api_url')?>?ctl=Connect_Weixin&met=login&callback=" + encodeURIComponent(window.parent.document.URL) + "&from=shop";
        }
        
        if (type == "wb") {
            window.location.href = "<?=Yf_Registry::get('ucenter_api_url')?>?ctl=Connect_Weibo&met=login&callback=" + encodeURIComponent(window.parent.document.URL) + "&from=shop";
        }
        
    }
</script>