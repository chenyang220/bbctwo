$(function () {
    var k = getCookie("key");
    var u = getCookie("id");
    var userInitiate = getCookie("userInitiate");//判断是否会员发起砍价
    var buy_id = getQueryString('buy_id');
    var InitiatePrice = getCookie("InitiatePrice");//判断是否会员发起砍价成功

    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }

    // 关闭砍价成功提示弹框
    $(".js-bargain-alert-close").click(function () {
        $(".bargain-help-success-alert").addClass("hide");
    });
    // 规则弹框
    $(".js-bargain-rule").click(function () {
        $(".bargain-rule-alert").removeClass("hide");
    });
    // 关闭规则弹框提示
    $(".js-bargain-rule-alert-close").click(function () {
        $(".bargain-rule-alert").addClass("hide");
    });

    //加载数据
    getBargainDetail();

    function getBargainDetail()
    {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Bargain_UnBargain&met=getBargainInfoByBuyId&typ=json",
            data: {buy_id:buy_id,user_id:u},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    console.log(res.data);
                    var data = res.data;
                    $(".bargain-user").html(data.user_name);//用户名
                    $("#user_logo").html("<img src='" + data.user_logo + "' alt='user'>");//用户头像
                    $("#goods_image").css({"background":"url("+data.goods_image+")","backgroundSize":"cover"});//商品图片
                    $("#goods_name").html(data.goods_name);//商品名称
                    $("#goods_price").html("￥" + data.goods_price);//商品价格
                    $("#bargain_price").html("最低可砍至<b>"+ data.bargain_price +"</b>元");//商品底价
                    $("#bargain_price_count").html(data.bargain_price_count);//已经砍掉的价格
                    $("#over_price").html(data.over_price);//剩余要砍的价格
                    $(".fnTimeCountDown").data('end', data.user_end_date);

                    //判断砍价活动状态,非自然失败的活动
                    if (data.bargain_state > 2 || data.is_on != 1 ){
                        $(".bargain-activity-end").removeClass('hide');
                        $(".nctouch-main-layout").addClass('hide');
                    }

                    //判断是否会员发起砍价弹框
                    if (userInitiate == 1 && InitiatePrice){
                        $(".self-bargain").removeClass('hide');
                        $("#self_bargain_price").html(InitiatePrice);
                        addCookie('InitiatePrice', '');
                        addCookie('userInitiate', '');
                    }

                    //判断是自己砍价还是帮助好友砍价
                    if (data.is_self == 1){
                        $(".bargain-index-help").removeClass('hide');
                        $(".bargain-index-rule").addClass('hide');

                        if (data.bargain_state == 0){
                            //砍价进行中
                            $("#rate").removeClass('hide');
                            $("#self-show-btn").removeClass('hide');
                            $("#show_rate").attr('style', 'width:' + data.rate + '');//百分比
                        } else if (data.bargain_state == 1){
                            //砍价成功
                            $("#self_success_btn").removeClass('hide');
                            $("#self_success_rate").removeClass('hide');
                            $(".bargain-order-detail").find('a').attr('href','./member/order_detail.html?order_id=' + data.order_id);
                        } else{
                            //砍价失败
                            $("#self_failure_btn").removeClass('hide');
                            $("#self_failure_rate").removeClass('hide');
                        }
                        var r = template.render("join-user-lists-tmpl", res.data);
                        $(".bargain-index-help-li").html(r);
                    } else{
                        $("#bargain-rule").addClass('hide');
                        if (data.bargain_state == 0) {
                            //砍价进行中
                            if(data.is_join == 1){
                                $("#friend_join_btn").removeClass('hide');
                            }else{
                                $("#friend-show-btn").removeClass('hide');
                            }
                            $("#rate").removeClass('hide');
                            $("#show_rate").attr('style', 'width:' + data.rate + '');//百分比
                        } else if (data.bargain_state == 1) {
                            //砍价成功
                            $("#friend_success_rate").removeClass('hide');
                            if (data.is_join == 1) {
                                $("#friend_success_btn").removeClass('hide');
                            } else {
                                $("#no_join_success").removeClass('hide');
                            }
                        } else {
                            //砍价失败
                            $("#friend_failure_rate").removeClass('hide');
                            if (data.is_join == 1) {
                                $("#friend_failure_btn").removeClass('hide');
                            } else {
                                $("#no_join_failure").removeClass('hide');
                            }
                        }
                    }

                    //时间倒计时
                    var _TimeCountDown = $(".fnTimeCountDown");
                    _TimeCountDown.fnTimeCountDown();

                    //分享
                    var title = data.bargain_desc;
                    var desc = data.goods_name;
                    var link = WapSiteUrl + "/tmpl/bargain_detail.html?buy_id=" + data.buy_id;
                    var icon = data.goods_image;
                    var nativeShare = new NativeShare();
                    var shareData = {
                        title: title,
                        desc: desc,
                        // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
                        link: link,
                        icon: icon,
                        // 不要过于依赖以下两个回调，很多浏览器是不支持的
                        success: function () {
                            alert("success");
                        },
                        fail: function () {
                            alert("fail");
                        }
                    };
                    nativeShare.setShareData(shareData);
                    $(document).on('click','.share',function () {
                        try {
                            nativeShare.call();
                        } catch (err) {
                            // 如果不支持，你可以在这里做降级处理
                            alert(err.message);
                        }
                    });
                }
            }
        });
    }

    //帮好友砍一刀
    $(".js-bargain-help").click(function () {
        var _this = $(this);
        //判断是否登录
        if (u){
            //帮助砍价
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Bargain_Bargain&met=HelpBargain&typ=json",
                data: {u: u, k: k, buy_id: buy_id},
                dataType: "json",
                success: function (res) {
                    if(res.status == 200){
                        var data = res.data;
                        $(".friend-help").removeClass('hide');
                        $("#friend_help_price").html(data.help_bargain_price);
                        //砍价成功
                        if (data.is_success == 1){
                            $(".friend-help-share").addClass('hide');
                            $("#friend_success_rate").removeClass('hide');
                            $("#friend_success_btn").removeClass('hide');
                            $("#rate").addClass('hide');
                        } else{
                            //砍价进行中
                            $("#friend_join_btn").removeClass('hide');
                            $("#show_rate").attr('style', 'width:' + data.rate + '');//百分比
                            $("#bargain_price_count").html(data.bargain_price_count);
                            $("#over_price").html((data.over_bargain_price - data.help_bargain_price).toFixed(2));
                        }
                        _this.addClass('hide');
                    }else{
                        if (res.msg){
                            var msg = res.msg;
                        } else{
                            var msg = '帮助砍价失败';
                        }
                        $.sDialog({skin: "red",content: msg,okBtn: false,cancelBtn: false});
                    }
                }
            })
        } else{
            $(".login-alert-tips").toggleClass("hide");
        }
    })

    //微信浏览器调起分享引导弹框
    $(document).on('click','.share-wechat',function () {
        $("#wechat").removeClass('hide');
    })

});

