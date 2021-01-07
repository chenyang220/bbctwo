$(function () {
    var k = getCookie("key");
    var u = getCookie("id");
    var explore_id = '';//要删除的心得id
    var explore_type = '';

    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }

    //分享user_id
    share_user_id = getQueryString('user_id');

    //判断from
    var from = getQueryString('from');


    //判断是否当前用户访问
    var follow_url = WapSiteUrl + "/tmpl/explore_user_follow.html?user_id=";
    var fans_url = WapSiteUrl + "/tmpl/explore_user_fans.html?from=user&user_id=";
    if (share_user_id == u || !share_user_id){
        $("#user_self").removeClass('hide');
        $("#user_from").addClass('hide');
        $("#draft_count").parent().removeClass('hide');
        $(".icon-ren").removeClass('hide');
        $("#user_follow_count").parent().attr('href', follow_url + u);
        $("#user_fans_count").parent().attr('href', fans_url + u);
    } else{
        $("#user_self").addClass('hide');
        $("#user_from").removeClass('hide');
        $("#draft_count").parent().addClass('hide');
        $(".icon-ren").addClass('hide');
        $("#user_follow_count").parent().attr('href', follow_url + share_user_id);
        $("#user_fans_count").parent().attr('href', fans_url + share_user_id);
    }
    
    //页面数据渲染
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=getExploreUserInfo&typ=json",
        data: {k: k, u: u,share_user_id:share_user_id, from: from,status:0},
        dataType: "json",
        success: function (res) {
            if (res.status == 200) {
                var data = res.data;
                //用户名
                $(".user_info_name").html(data.user_info.user_name);
                //用户头像
                var img_str = "<img src='" + data.user_info.user_logo + "' alt='personal-center' class='user-logo'>";
                $(".discover-personal-head").find('.img-box').html(img_str);
                //签名
                if (data.explore_user.user_sign) {
                    $(".sign_content").html(data.explore_user.user_sign);
                    $(".js-sign-add").addClass("hide");
                    $(".discover-personal-sign .icon-bianji1").removeClass("hide");
                } else {
                    $(".sign_content").html("还未设置个性签名");
                     $(".js-sign-add").removeClass("hide");
                    $(".discover-personal-sign .icon-bianji1").addClass("hide");
                }
                $("#user_sign").html(data.explore_user.user_sign);
                if (data.explore_user.user_sign) {
                    $("#num").html(data.explore_user.user_sign.length);
                }
                //是否关注
                if (data.isFollow == 1){
                    $("#isFollow").removeClass('hide');
                    $("#follow").addClass('hide');
                }else {
                    $("#isFollow").addClass('hide');
                    $("#follow").removeClass('hide');
                }
                //头部数据
                $("#user_follow_count").html(data.explore_user.user_follow_count);
                $("#user_fans_count").html(data.explore_user.user_fans_count);
                $("#user_like").html(data.explore_user.user_like);
                //文章、草稿
                if (data.explore_count.count >0) {
                    $("#count").html("资源(" + data.explore_count.count + ")");
                    $("#count").data('count',data.explore_count.count);
                } else{
                    $("#count").html("资源");
                }
                if (data.explore_count.draft_count > 0){
                    $("#draft_count").html("草稿(" + data.explore_count.draft_count + ")");
                } else{
                    $("#draft_count").html("草稿");
                }

                if (data.explore_count.collection_count > 0){
                    $("#collection").html("收藏(" + data.explore_count.collection_count + ")");
                } else{
                    $("#collection").html("收藏");
                }

                $("#draft_count").data('draft_count', data.explore_count.draft_count);

                //文章、草稿数据渲染
                if (from && from == 'draft_edit') {
                    $(".discover-content-module").removeClass('hide');
                    $(".social-push-items").addClass('hide');
                    $("#draft_count").parent().addClass('active');
                    $("#count").parent().removeClass('active');
                    var r = template.render("unnormal-template", res.data);
                    $(".draft-items").html(r);
                } else {
                    $(".discover-content-module").addClass('hide');
                    $("#draft_count").parent().removeClass('active');
                    $("#count").parent().addClass('active');
                    if (share_user_id == u || !share_user_id) {
                        var r = template.render("waterfall-template", res.data);

                    } else {
                        var r = template.render("no-waterfall-template", res.data);
                    }

                    $("#shouchang").hide();
                    $(".social-push-items").parent().removeClass('hide');
                    $(".social-push-items").removeClass('hide').html(r);
                }

                //分享
                var title = data.user_info.user_name + '晒了超多好物，快去看看！';
                var desc = '';
                var link = WapSiteUrl + "/tmpl/explore_center.html?user_id=" + data.user_info.user_id;
                var icon = data.user_info.user_logo;
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
                $("#share").click(function () {
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

    // dilaog弹框关闭
    $(".btn-del-dialog").click(function () {
        $(this).parents(".dialog").addClass("hide");
    });
    // 删除心得(由于瀑布流调用了jquery.js,$.sDialog在jquery.js中报错，因此无法使用$.sDialog)
    $(document).on('click','.js-btn-del-draft',function () {
        explore_id = $(this).data('explore_id');
        explore_type = 2;
        $(".heart-del-dialog").removeClass("hide");
    });
     $(document).on('click','.js-btn-del-article',function () {
        explore_id = $(this).data('explore_id');
        explore_type = 1;
        $(".heart-del-dialog").removeClass("hide");
    });
    //确认删除
    $(".btn-confirm-del").click(function () {
        $(".heart-del-dialog").addClass("hide");
        if (explore_type == 1) {
          var source_count = Number($("#count").data('count')); 
        }else{
          var draft_count = Number($("#draft_count").data('draft_count'));  
        }
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=delExplore&typ=json&from=draft",
            data: {k: k, u: u, explore_id: explore_id},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (explore_type == 1) {
                        $("#count").text("资源(" + (source_count - 1) + ")");
                        $(".waterfall-li" + explore_id).remove();
                        $(".heart-del-dialog").addClass("hide");
                    }else{
                        $("#draft_count").html("草稿(" + (draft_count - 1) + ")");
                        $(".draft" + explore_id).remove();
                        $(".heart-del-dialog").addClass("hide");
                    }
                } else {
                    $("#fauile").html('删除失败');
                }
                explore_id = '';
            }
        })
    });
    //取消删除
    $(".btn-cancel").click(function () {
        $(".heart-del-dialog").addClass("hide");
    });

    // 添加个性签名
    $(".js-set-sign").click(function () {
        $(".heart-cancel-sign").removeClass("hide");
    });
    //保存个性签名
    $(".btn-sign-save").click(function () {
        var user_sign = $("#user_sign").val();
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editUserInfo&typ=json",
            data: {k: k, u: u, user_sign: user_sign},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    $(".heart-cancel-sign").addClass("hide");
                    $(".dialog-info").removeClass('hide');
                    $(".information").html('保存成功');
                    if (user_sign) {
                        $(".sign_content").html(user_sign);
                        $(".js-sign-add").addClass("hide");
                        $(".discover-personal-sign .icon-bianji1").removeClass("hide");
                    }else{
                        $(".sign_content").html('还未设置个性签名');
                         $(".js-sign-add").removeClass("hide");
                        $(".discover-personal-sign .icon-bianji1").addClass("hide");
                    }
                } else {
                    $(".dialog-info").removeClass('hide');
                    $(".information").html('保存失败');
                }
                setTimeout(function () {
                    $(".dialog-info").addClass('hide');
                }, 3000);
            }
        })
    });

    //个性签名控制字数
    $("#user_sign").keyup(function () {
        var lengths = $(this).val().length;
        if (lengths == 30) {
            // $("#user_sign").blur();
        }
        if (lengths > 30) {
            $(this).val($(this).val().substring(0, 29));
            lengths=30;
        }
        if (lengths <= 0) {
            lengths = 0;
        }
        $("#num").html(lengths);
    });
    $("#draft_count").click(function () {
        $("#count").parent().removeClass("active");
        $("#collection").parent().removeClass("active");
        $("#draft_count").parent().addClass("active");
    });
    $("#count").click(function () {
        $("#draft_count").parent().removeClass("active");
        $("#collection").parent().removeClass("active");
        $("#count").parent().addClass("active");
    });

    $("#collection").click(function () {
        $("#draft_count").parent().removeClass("active");
        $("#count").parent().removeClass("active");
        $("#collection").parent().addClass("active");
    });
    // 切换文章和草稿
        $(".js-discover-exchange li").click(function () {
        var index = $(this).index();
        var user_id = getQueryString('user_id');
      //  console.log(index);
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=getExploreUserInfo&typ=json",
            data: {k: k, u: u, status: index, share_user_id: user_id},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (index == 1) {
                        var r = template.render("unnormal-template", res.data);
                        $(".draft-items").html(r);
                        //$(".discover-personal-main-exchange li").removeClass("active");
                    }else if(index == 2){
                        $("#shouchang").show();
                        $(".social-push-items").removeClass('hide')
                        var r = template.render("waterfall-template_shouchang", res.data);
                        $("#shouchang").html(r);
                    }else {
                        $(".social-push-items").removeClass('hide');
                        var r = template.render("waterfall-template", res.data);
                        $("#wenzhang").html(r);
                    }
                }
            }
        });


        $(".discover-content-module").addClass("hide");
        $(".discover-content-module").eq(index).removeClass("hide");
    });

    //点赞、取消点赞
    $(document).on('click','.praise',function(){
        // 判断是否登录
        if (u){
            var _this = $(this);
            var explore_id = _this.data('explore_id');
            var addOrReduce = _this.hasClass('active') ? 1 : 2;
            var explore_num = _this.find('em').html();
            var user_id = u;
            var data = {
                k: k,
                u: u,
                user_id: user_id,
                explore_id: explore_id,
                addOrReduce: addOrReduce,
            };
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editExploreLike&typ=json",
                data: data,
                dataType: "json",
                success: function (res) {
                    if (res.status == 200) {
                        if (addOrReduce == 1) {
                            _this.removeClass('active');
                            _this.find('em').html(Number(explore_num) - 1);
                        } else {
                            _this.addClass('active');
                            _this.find('em').html(Number(explore_num) + 1);
                        }
                    }
                }
            });
            $(".social-login-dialog").addClass('hide');
        } else {
            $(".social-login-dialog").removeClass('hide');
        }
    });

    //关注
    $(".btn-follow").click(function () {
        // 判断是否登录
        var _this = $(this);
        if (u) {
            $(".social-login-dialog").addClass('hide');
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editUserFollow&typ=json",
                data: {k:k, u:u, user_id:share_user_id},
                dataType: "json",
                success: function (res) {
                    var user_fans_count = $("#user_fans_count").html();
                    if (res.status == 200) {
                        if ($("#isFollow").hasClass('hide')){
                            $("#user_fans_count").html(Number(user_fans_count) + 1);
                            $("#isFollow").removeClass('hide');
                            $("#follow").addClass('hide');
                        } else{
                            $("#user_fans_count").html(Number(user_fans_count) - 1);
                            $("#isFollow").addClass('hide');
                            $("#follow").removeClass('hide');
                        }
                    }
                }
            })
        } else {
            $(".social-login-dialog").removeClass('hide');
        }
    });

    //用户头像放大
    $(document).on('click', '.user-logo', function () {
        var img_url = $(this).attr('src');
        $(".nctouch-main-layout").addClass('hide');
        $("#user-logo-box").removeClass('hide');
        $("#user-logo-box").find('img').attr('src', img_url);
    });
    $("#user-logo-box").click(function () {
        $(".nctouch-main-layout").removeClass('hide');
        $("#user-logo-box").addClass('hide');
    });

});
$(".social-login-dialog").click(function(){
    $(this).hide();
})

$(".explore-center-head-close").click(function () {
    var from = getQueryString('from');
    if(from == 'center') {
        window.location.href = WapSiteUrl + '/tmpl/member/member.html';
    } else {
        window.location.href = WapSiteUrl + '/tmpl/explore_list.html';
    }
})


