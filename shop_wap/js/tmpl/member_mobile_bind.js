
Zepto(function ()
{
    var e = getCookie("key");
    if (!e)
    {
        window.location.href = WapSiteUrl + "/tmpl/member/login.html";
        return
    }
    loadSeccode();
    Zepto("#refreshcode").bind("click", function ()
    {
        loadSeccode()
    });
    Zepto("#re_user_mobile").on("blur", function ()
    {
        var area_code = Zepto('#area_code').val();
        var mobile = Zepto(this).val();
        mobile = mobile.replace(/\s+/g,"");

        if (Zepto(this).val() != "" && !/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(mobile) && area_code==86)
        {
            Zepto(this).val(/\d+/.exec(Zepto(this).val()))
        }
    });
    Zepto.ajax({
        type: "get", url: ApiUrl + "/index.php?ctl=Buyer_User&met=getUserInfo&typ=json", data: {k: e, u: getCookie('id')}, dataType: "json", success: function (e)
        {
            console.info(e);
            if (e.status = 200)
            {
                Zepto("#re_user_mobile").val(e.data.user_mobile);
                Zepto("#area_code").val(e.data.area_code);
                jQuery("#re_user_mobile").intlTelInput({
                    utilsScript: "../../js/utils.js"
                });
            }
        }
    });

    Zepto.sValid.init({
        rules: {captcha: {required: true, minlength: 4}, mobile: {required: true,mobiles:true}},
        messages: {captcha: {required: "请填写图形验证码", minlength: "图形验证码不正确"}, mobile: {required: "请填写手机号",mobiles:"请输入正确的手机号"}},
        callback: function (e, a, t)
        {
            if (e.length > 0)
            {
                var o = "";
                Zepto.map(a, function (e, a)
                {
                    o += "<p>" + e + "</p>"
                });
                errorTipsShow(o)
            }
            else
            {
                errorTipsHide()
            }
        }
    });
    Zepto("#send").click(function ()
    {
        var area_code = Zepto('#area_code').val();
        var mobile = Zepto('#re_user_mobile').val();
        var reg = /^1[345678]\d{9}$/;
        if(area_code == 86 && !reg.test(mobile)){
            errorTipsShow("<p>请输入正确的手机号</p>");
        }else{
            if (Zepto.sValid())
            {
                var a = Zepto.trim(Zepto("#re_user_mobile").val());
                a = a.replace(/\s+/g,"");
                var area_code = Zepto.trim(Zepto("#area_code").val());
                var t = Zepto.trim(Zepto("#captcha").val());
                var o = Zepto.trim(Zepto("#codekey").val());
                Zepto.ajax({
                    type: "post",
                    url: ApiUrl + "/index.php?ctl=Buyer_User&met=getMobileYzm&typ=json",
                    data: {k: e, u: getCookie('id'), mobile: a,area_code:area_code, captcha: t, codekey: o},
                    dataType: "json",
                    success: function (e)
                    {
                        if (e.status != 250)
                        {
                            var res = eval(e.result);
                            if(e.result && res.status==250){
                                errorTipsShow("<p>" + e.result.msg + "</p>");
                            }else{
                                Zepto("#send").hide();
                                Zepto("#auth_code").removeAttr("readonly");
                                Zepto(".code-countdown").show().find("em").html(e.data.sms_time);
                                Zepto.sDialog({skin: "block", content: "短信验证码已发出", okBtn: false, cancelBtn: false});
                                var a = setInterval(function ()
                                {
                                    var e = Zepto(".code-countdown").find("em");
                                    var t = parseInt(e.html() - 1);
                                    if (t == 0)
                                    {
                                        Zepto("#send").show();
                                        Zepto(".code-countdown").hide();
                                        clearInterval(a);
                                        //$("#codeimage").attr("src", ApiUrl + "/index.php?act=seccode&op=makecode&k=" + $("#codekey").val() + "&t=" + Math.random());
                                        //$("#captcha").val("")
                                    }
                                    else
                                    {
                                        e.html(t)
                                    }
                                }, 1e3)
                            }

                        }
                        else
                        {
                            errorTipsShow("<p>" + e.msg + "</p>");
                            //$("#codeimage").attr("src", ApiUrl + "/index.php?act=seccode&op=makecode&k=" + Zepto("#codekey").val() + "&t=" + Math.random());
                            // Zepto("#captcha").val("")
                        }
                    }
                })
            }
        }

    });
    Zepto("#nextform").click(function ()
    {
        if (!Zepto(this).parent().hasClass("ok"))
        {
            return false
        }
        var mobile = Zepto.trim(Zepto("#re_user_mobile").val());
        var area_code = Zepto.trim(Zepto("#area_code").val());
        var a = Zepto.trim(Zepto("#auth_code").val());
        if (a)
        {
            Zepto.ajax({
                type: "post", url: ApiUrl + "/index.php?ctl=Buyer_User&met=editMobileInfo&typ=json", data: {k: e, u: getCookie('id'), yzm: a, user_mobile:mobile,area_code:area_code}, dataType: "json", success: function (e)
                {
                    if (e.status == 200)
                    {
                        Zepto.sDialog({skin: "block", content: "绑定成功", okBtn: false, cancelBtn: false});
                        setTimeout("location.href = WapSiteUrl+'/tmpl/member/member_account.html'", 2e3)
                    }
                    else
                    {
                        errorTipsShow("<p>" + e.msg + "</p>")
                    }
                }
            })
        }
    })
});