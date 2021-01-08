var explore_id = getQueryString("explore_id");
var key = getCookie("key");//登录标记
var id = getCookie("id");
var active_id = getQueryString("active_id");
var type = getQueryString("type");

$(function () {

    get_detail(explore_id);
    //渲染页面
    function get_detail(explore_id) {
        $.ajax({
            url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=getExploreDetail&typ=json",
            type: "get",
            data: {explore_id: explore_id, k: key, u: getCookie("id")},
            dataType: "json",
            success: function (result) {
                result.data.comment.explore_id = explore_id;
                if (result.status == 200) {
                    var data = result.data;

                    //该心得已被删除
                    if(data.explore_base.is_del == 1) {
                        $(".heart-banner-swiper").addClass('hide');
                        $(".heart-detail-content").addClass('hide');
                        $(".heart-detail-bottom").addClass('hide');

                        $(".explore_report_content").removeClass('hide');
                        $(".warning").html('该心得已删除~');

                        return false;
                    }

                    //判断该心得是否已下架(作者自己打开已下架心得)
                    if(data.user_info.is_author == 1 && data.explore_base.explore_status == 1) {
                        $(".goods-shelves").removeClass("hide");
                        $(".js-heart-share").addClass('hide');
                        $(".js-heart-delete").removeClass('hide');
                    }
                    //非作者打开心得
                    if(data.user_info.is_author !== 1 && data.explore_base.explore_status == 1) {
                        $(".heart-banner-swiper").addClass('hide');
                        $(".heart-detail-content").addClass('hide');
                        $(".heart-detail-bottom").addClass('hide');

                        $(".explore_report_content").removeClass('hide');

                        return false;
                    }

                    //判断是否是作者自己，是否显示“编辑/删除/举报”按钮
                    if(data.user_info.is_author == 1) {
                        $(".edit").removeClass('hide');
                        $(".delete").removeClass('hide');
                        $(".is_jubao").addClass('hide');
                    }

                    //如果用户正在举报该心得，则不可重复举报
                    if(data.explore_base.is_reporting == 1) {
                        $(".js-heart-jubao").find('h5').html('已举报');
                        $(".js-heart-jubao").removeClass("js-heart-jubao");
                    }

                    //心得图片
                    var images_html = template.render("images_list", data);

                    console.log(data);

                    $("#explore_images").html(images_html);

                    // banner 轮播
                    var windowWidth=$(window).width();
                    $(".heart-banner-swiper").css("height",windowWidth);
                    var swiper = new Swiper('.heart-banner-swiper', {
                        pagination : '.heart-banner-pagination',
                        paginationType : 'fraction',
                    });

                    //心得内容（用户信息，内容，标签）
                    var explore_html = template.render("explore", data);
                    $("#explore_base").html(explore_html);

                    // 头部心得用户信息
                    var explore_html = template.render("head-infor", data);
                    $("#explore_head_user").html(explore_html);

                    //心得商品
                    var goods_html = template.render("goods", data);
                    $("#explore_goods").html(goods_html);

                    //心得评论及回复
                    var comment_html = template.render("comment", data);
                    $("#explore_comment").html(comment_html);

                    //心得点赞，评论，立即购买
                    var info_html = template.render("info", data);
                    $("#explore_info").html(info_html);

                    // 标签
                    var swiper = new Swiper('.heart-publish-tags', {
                        slidesPerView:"auto",
                        freeMode: true
                    });
                    // 评论
                    if(key) {
                        $.animationUp({
                            valve: '.js-more-comment',          // 动作触发，为空直接触发
                            wrapper: '#heart-comments-more',    // 动作块
                            scroll: '#js-comment-scroll'  // 滚动块，为空不触发滚动
                        });
                    }

                    //分享
                    var link = WapSiteUrl + "/tmpl/explore_base.html?explore_id=" + explore_id;
                    var title = data.explore_base.explore_title;
                    var desc = data.explore_base.explore_content;
                    var icon = data.user_info.user_logo;
                    var config = {
                        url: link,// 分享的网页链接
                        title: title,// 标题
                        desc: desc,// 描述
                        img: icon,// 图片
                        img_title: '我的心得',// 图片标题
                        from: 'bbcBuilder', // 来源
                        isExplore: 'explore',
                    };
                    var share_obj = new nativeShare('share-box', config);

                    //锚点定位
                    if(active_id) {
                        goto(active_id,type)
                    }
                    $.ajax({
                        url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=browseadd&typ=json",
                        type: "get",
                        data: {explore_id: explore_id, k: key, u: getCookie("id"),user_account:getCookie("user_account")},
                        dataType: "json",
                        success: function (e) {
                            console.log(e);
                        }
                    })




                } else {
                    $.sDialog({
                        content: result.msg + "！<br>请返回上一页继续操作…",
                        okBtn: false,
                        cancelBtnText: "返回",
                        cancelFn: function () {
                            history.back();
                        }
                    });
                }
            }
        });
    }

    //查看全部评论按钮
    $(document).on("click",".js-more-comment",function(){
        //未登录显示登录框
        if(!key) {
            $(".social-login-dialog").show();
            return false;
        }
    })

    //发送评论
    $(document).on("click", "#send", function () {
        //未登录显示登录框
        if(!key) {
            $(".social-login-dialog").show();
            return false;
        }

        var comment_content = $("#comment_content").val();

        //用户添加评论
        $.ajax({
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=addExploreComment&typ=json",
            type: "POST",
            data: {
                k: getCookie("key"),
                u: getCookie("id"),
                user_account:getCookie("user_account"),
                explore_id: explore_id,
                comment_content:comment_content,
            },
            dataType: "json",
            success: function (result) {
                if (result.status == 200) {
                    $.sDialog({
                        skin: "green",
                        content: '评论成功',
                        okBtn: false,
                        cancelBtn: false
                    });
                    getCommentAll();

                    num = $(".js-more-comment-num").find(".num").html();

                    num = num*1 +1;
                    if (num > 0){
                        $(".js-more-comment-num").find(".num").removeClass('hide');
                        $(".js-more-comment-num").find(".num").html(num);
                    }else{
                        $(".js-more-comment-num").find(".num").addClass('hide');
                    }

                    get_detail(explore_id);
                } else {
                    $.sDialog({
                        skin: "red",
                        content: '评论失败',
                        okBtn: false,
                        cancelBtn: false
                    });
                }
            }
        });
    })


    //点击弹框中的“删除”
    $(document).on("click",".comment-operate-del",function () {
        var _this=$(this);
        //获取回复类型
        var type = _this.attr('type');
        var id = _this.attr(type+'-id');

        if(type == 'comment') {
            var  url = ApiUrl + "/index.php?ctl=Explore_Explore&met=delExploreComment&typ=json";

        }
        if(type == 'reply') {
            var  url = ApiUrl + "/index.php?ctl=Explore_Explore&met=delCommentReply&typ=json";

        }

        $.ajax({
            url: url,
            type: "POST",
            data: {
                k: getCookie("key"),
                u: getCookie("id"),
                comment_id: id,
                reply_id: id,
            },
            dataType: "json",
            success: function (result) {
                if (result.status == 200) {
                    $.sDialog({
                        skin: "green",
                        content: '删除成功',
                        okBtn: false,
                        cancelBtn: false
                    });
                    getCommentAll();
                    get_detail(explore_id);
                } else {
                    $.sDialog({
                        skin: "red",
                        content: '删除失败',
                        okBtn: false,
                        cancelBtn: false
                    });
                }

                hideopearate();
            }
        });
    })


    $("body").on("click", "#shouchang", function (e) {


        if($("#shouchang").hasClass('active')) {
            //取消收藏
            $.ajax({
                url: ApiUrl + "/index.php?ctl=Explore_Explore&met=exitCollertion&typ=json",
                type: "POST",
                data: {
                    k: getCookie("key"),
                    u: getCookie("id"),
                    explore_id: explore_id
                },
                dataType: "json",
                success: function (result) {
                    console.log(result);
                }
            })
        } else {
            //收藏
            $.ajax({
                url: ApiUrl + "/index.php?ctl=Explore_Explore&met=addCollertion&typ=json",
                type: "POST",
                data: {
                    k: getCookie("key"),
                    u: getCookie("id"),
                    explore_id: explore_id
                },
                dataType: "json",
                success: function (result) {
                    console.log(result);
                }
            })
        }
        $("#shouchang").toggleClass("active");
    });

    
    function goto(active_id,type) {
        //type=0，评论心得，跳转到全部评论页，定位到该评论
        //type=5,点赞评论，跳转到全部评论页，定位到该评论
        if(type == 0 || type == 5) {
            getCommentAll();
            $(document.body).css({"position":"fixed","width":"100%","height":"100%","overflow":"hidden"});
            document.body.addEventListener('touchmove',handler,false);
            document.body.addEventListener('wheel',handler,false);
            $("#heart-comments-more").removeClass('down').addClass('up');
        }
        //type=1，回复回复，跳转到全部回复页，定位到该回复
        //type=2，回复评论，跳转到全部回复页，定位到该回复
        //type=6,点赞回复，跳转到全部回复页，定位到该回复
        if(type == 1 || type == 2 || type == 6) {
            //根据reply_id 查找 comment_id
            $.ajax({
                url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=getCommentIdByReplyId&typ=json",
                type: "POST",
                data: {
                    k: getCookie("key"),
                    u: getCookie("id"),
                    reply_id: active_id,
                },
                dataType: "json",
                success: function (result) {
                    if (result.status == 200) {
                        window.location.href = WapSiteUrl + "/tmpl/explore_reply.php?explore_id="+explore_id+"&comment_id="+result.data.comment_id+"&reply_id="+active_id;
                    }
                }
            });
        }



    }
    // 弹框关闭
    $(".social-login-dialog").click(function(){
        $(this).hide();
    })

    
    
});

// 分享
$.animationUp({
    valve: '.js-heart-share',          // 动作触发，为空直接触发
    wrapper: '#heart-share',    // 动作块
    scroll: ''  // 滚动块，为空不触发滚动
});


//用户关注与取消关注按钮
$(document).on("click", ".follow-btn", function () {
    //未登录显示登录框
    if(!key) {
        $(".social-login-dialog").show();
        return false;
    }

    var _this=$(this);
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editUserFollow&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            user_id: $(this).attr('data-id')
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                if(_this.hasClass('active')) {
                    //取消关注
                    $(".follow-btn").removeClass('active');
                    $(".follow-btn").prepend('<i class="iconfont icon-add"></i>');
                    $(".follow-btn").find('em').html('关注');
                } else {
                    //关注
                    $(".follow-btn").addClass("active");
                    $(".follow-btn").find(".icon-add").remove();
                    $(".follow-btn").find('em').html('已关注');
                }
            } else {
                if(_this.hasClass('active')) {
                    //取消关注
                    con = '取消关注失败';
                } else {
                    //关注
                    con = '关注失败';
                }
                $.sDialog({
                    skin: "red",
                    content: con,
                    okBtn: false,
                    cancelBtn: false
                });
            }
        }
    });
})

//评论点赞与取消点赞按钮
$(document).on("click", ".like-comment-btn", function () {
    //未登录显示登录框
    if(!key) {
        $(".social-login-dialog").show();
        return false;
    }

    var _this=$(this);
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editCommentLike&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            comment_id: $(this).attr('data-id')
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                if(_this.hasClass('active')) {
                    //取消点赞
                    _this.removeClass('active');
                } else {
                    //点赞
                    _this.addClass("active");
                }
            } else {
                if(_this.hasClass('active')) {
                    //取消关注
                    con = '取消点赞失败';
                } else {
                    //关注
                    con = '点赞失败';
                }
                $.sDialog({
                    skin: "red",
                    content: con,
                    okBtn: false,
                    cancelBtn: false
                });
            }
        }
    });
})

var handler = function () {
    event.preventDefault();
    event.stopPropagation();
};

//显示所有评论
$(document).on("click",".js-more-comment",function () {
    getCommentAll();
   $(document.body).css({"position":"fixed","width":"100%","height":"100%","overflow":"hidden"});
    document.body.addEventListener('touchmove',handler,false);
    document.body.addEventListener('wheel',handler,false);

})

//渲染所有评论页面
function getCommentAll()
{
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=getCommentAll&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            explore_id:explore_id
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                //所有评论页面
                var commont_more_html = template.render("comments-more", result.data);
                $("#heart-comments-more").html(commont_more_html);

                if(active_id) {
                    var Top = $(".comment"+active_id).position().top;
                    $("#js-comment-scroll").scrollTop(Top);
                }
                // 评论input-发送变红
                $("#comment_content").bind('input porpertychange',function(){
                    if($(this).val().length !=0){
                        $(".btn-heart-views-send").addClass("active");
                    }else{
                        $(".btn-heart-views-send").removeClass("active");
                    }
                });

            }
        }
    });
}

//隐藏弹框
function hideopearate()
{
    $("#comment-opearate").removeClass('up').addClass('down');
}

//多条评论页点击“X”
function hidecomments()
{
    $("#heart-comments-more").removeClass('up').addClass('down');
}

//回复点赞与取消点赞按钮
$(document).on("click", ".like-reply-btn", function () {

    //未登录显示登录框
    if(!key) {
        $(".social-login-dialog").show();
        return false;
    }

    var _this=$(this);
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editReplyLike&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            reply_id: $(this).attr('data-id')
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                if(_this.hasClass('active')) {
                    //取消点赞
                    _this.removeClass('active');
                } else {
                    //点赞
                    _this.addClass("active");
                }
            } else {
                if(_this.hasClass('active')) {
                    //取消关注
                    con = '取消点赞失败';
                } else {
                    //关注
                    con = '点赞失败';
                }
                $.sDialog({
                    skin: "red",
                    content: con,
                    okBtn: false,
                    cancelBtn: false
                });
            }
        }
    });
})


//点击评论/回复（作者-删除/回复，非作者-直接聚焦输入框回复）
$(document).on("click", ".js-comment-opearate li .comment_content", function () {


    //未登录显示登录框
    if(!key) {
        $(".social-login-dialog").show();
        return false;
    }

    var _this=$(this).parents("li");
    var type = _this.attr('type');
    var user_account = _this.find(".one-overflow").html();
    //点击的是评论
    if( type == 'comment') {
        //评论的内容
        var content = _this.find(".comment_content").html();
        //评论id
        var comment_id = _this.attr('comment-id');
    }
    //点击的是回复
    if( type == 'reply') {
        //评论的内容
        var content = _this.find(".reply_content").html();
        //评论id
        var comment_id = _this.attr('comment-id');
        //回复id
        var reply_id = _this.attr('reply-id');
    }

    //如果点击的评论/回复不是自己的，则直接聚焦到输入框中进行回复评论/回复回复

    if(_this.attr('data-id') !== id){
        //聚焦输入框
        $('#comment_content').attr('placeholder','回复@'+user_account);
        $('#comment_content').attr('id', 'reply_content');
        $('#reply_content').attr('placeholder','回复@'+user_account);
        $('#send').attr('id', 'reply_send');
        $('#reply_content').focus();

        $('#reply_content').attr('type',type);
        $('#reply_content').attr('comment-id',comment_id);
        if(type == 'reply') {
            $('#reply_content').attr('reply-id',reply_id);
        }
    } else {
        var delcontent = '确认删除“' + content + '”';
        $(".del-content").html(delcontent);
        $("#comment-opearate").removeClass('down').addClass('up');

        $(".comment-operate-reply").attr('type',type);
        $(".comment-operate-del").attr('type',type);

        $(".comment-operate-reply").attr('comment-id',comment_id);
        $(".comment-operate-del").attr('comment-id',comment_id);

        //点击的是回复
        if( type == 'reply') {
            $(".comment-operate-reply").attr('reply-id',reply_id);
            $(".comment-operate-del").attr('reply-id',reply_id);
        }

    }
})

//弹框中的“取消” - 隐藏按钮
$(document).on("click",".btn-discover-operate",function () {
    hideopearate();
})

//多条评论页点击“X” - 隐藏按钮
$(document).on("click",".comment-close",function () {
    hidecomments();
    $(document.body).css({"position":"relative","width":"100%","height":"100%","overflow":"auto"});
    document.body.removeEventListener('touchmove',handler,false);
    document.body.removeEventListener('wheel',handler,false);


})
$(document).on("click",".nctouch-bottom-mask-bg",function () {
     $(document.body).css({"position":"relative","width":"100%","height":"100%","overflow":"auto"});
    document.body.removeEventListener('touchmove',handler,false);
    document.body.removeEventListener('wheel',handler,false);
})

//发送回复
$(document).on("click", "#reply_send", function () {

    //未登录显示登录框
    if(!key) {
        $(".social-login-dialog").show();
        return false;
    }

    var type = $(this).attr('type');
    var reply_content = $("#reply_content").val();
    var comment_id = $('#reply_content').attr('comment-id');

    if(type == 'reply') {
        var  reply_id = $('#reply_content').attr('reply-id');
    } else {
        var  reply_id = 0;
    }
    //用户添加评论
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=addCommentReply&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            explore_id: explore_id,
            comment_id: comment_id,
            reply_content:reply_content,
            to_reply_id:reply_id,
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                $.sDialog({
                    skin: "green",
                    content: '回复成功',
                    okBtn: false,
                    cancelBtn: false
                });
                getCommentAll()
            } else {
                $.sDialog({
                    skin: "red",
                    content: '回复失败',
                    okBtn: false,
                    cancelBtn: false
                });
            }
        }
    });
})

//点击弹框中的“回复”  (此功能已屏蔽)
$(document).on("click",".comment-operate-reply",function () {
    var _this=$(this);
    //获取回复类型
    var type = _this.attr('type');

    var comment_id = _this.attr('comment-id');


    if(type == 'comment') {
        var user_account = $(".comment"+comment_id).find(".one-overflow").html();
    }

    if(type == 'reply') {
        var reply_id = _this.attr('reply-id');
        var user_account = $(".reply"+reply_id).find(".one-overflow").html();
    }

    hideopearate();

    //聚焦输入框
    $('#comment_content').attr('placeholder','回复@'+user_account);
    $('#comment_content').attr('id', 'reply_content');
    $('#send').attr('type', type);
    $('#send').attr('id', 'reply_send');
    $('#reply_content').focus();

    $('#reply_content').attr('comment-id',comment_id);

    if(type == 'reply') {
        $('#reply_content').attr('reply-id',reply_id);
    }

})


//心得点赞与取消点赞按钮
$(document).on("click", ".like-explore-btn", function () {

    //未登录显示登录框
    if(!key) {
        $(".social-login-dialog").show();
        return false;
    }

    var _this=$(this);
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editExploreLike&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            explore_id: explore_id
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                num = _this.find('.num').html();

                if(_this.hasClass('active')) {
                    //取消点赞
                    _this.removeClass('active');
                    num -= 1;
                } else {
                    //点赞
                    _this.addClass("active");
                    num = num*1 + 1;
                }
                if (num > 0){
                    _this.find('.num').removeClass('hide');
                    _this.find('.num').html(num);
                }else{
                    _this.find('.num').addClass('hide');
                    _this.find('.num').html(num);
                }
            } else {
                if(_this.hasClass('active')) {
                    //取消关注
                    con = '取消点赞失败';
                } else {
                    //关注
                    con = '点赞失败';
                }
                $.sDialog({
                    skin: "red",
                    content: con,
                    okBtn: false,
                    cancelBtn: false
                });
            }
        }
    });
});

//举报
$(document).on("click", ".js-heart-jubao", function () {
    //未登录显示登录框
    if(!key) {
        $(".social-login-dialog").show();
        return false;
    }

    var _this=$(this);
    //获取举报原因
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getReportReason&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                var reason_html = template.render("reason_list", result);
                $("#reason").html(reason_html);
            }
        }
    });

    $("#heart-jubao").removeClass('down').addClass('up');
})

$(document).on("click","#jubao-close",function () {
    $("#heart-jubao").removeClass('up').addClass('down');
});

$(document).on("click",".js-jubao-reason li",function () {
    $(".js-jubao-reason li").removeClass("active");
    $(this).addClass("active");
});

$(document).on("click","#jubao-btn",function () {
    //获取举报的原因
    var reason_id = $("#reason").find(".active").attr("data-id");
    var reason = $("#reason_content").val();

    console.info(reason_id);
    if(reason_id == undefined) {
        $.sDialog({
            skin: "red",
            content: '请选择举报原因',
            okBtn: false,
            cancelBtn: false
        });
        
        return false;
    }

    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=addExploreReport&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            report_reason_id:reason_id,
            report_reason:reason,
            explore_id:explore_id,
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                $.sDialog({
                    skin: "green",
                    content: '提交审核成功',
                    okBtn: false,
                    cancelBtn: false
                });
            }

            $(".js-heart-jubao").find('h5').html('已举报');
            $(".js-heart-jubao").removeClass("js-heart-jubao");

            $("#heart-jubao").removeClass('up').addClass('down');
            $("#heart-share").removeClass('up').addClass('down');
            $("#reason_content").val('');
        }
    });
});

$(".login").click(function(){
    window.location.href = ShopWapUrl + "/tmpl/member/login.html";
});

$(document).on("click",".js-heart-delete",function () {

    $.sDialog({
        skin: "green",
        content: '确认删除此心得吗？',
        okFn: function () {
            delexplore(explore_id);
        },
        cancelBtn: true
    });
})

//删除心得
function delexplore(id) {
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=delExplore&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            explore_id:explore_id,
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                $.sDialog({
                    skin: "green",
                    content: '删除成功',
                    okBtn: false,
                    cancelBtn: false
                });
            }
            window.location.href = ShopWapUrl + "/tmpl/explore_list.html";

        }
    });
}

//删除心得
$(document).on("click",".delete",function () {

    $.sDialog({
        skin: "green",
        content: '确认删除此心得吗？',
        okFn: function () {
            delexplore(explore_id);
        },
        cancelBtn: true
    });

});

//编辑
$(document).on("click",".edit",function () {
    window.location.href = ShopWapUrl + "/tmpl/explore.php?explore_id="+explore_id;
});

// 心得滑动头部变化
$(window).scroll(function(){
    if($(window).scrollTop()>$(window).width()){
        $(".heart-detail-header").addClass("headactive");
        $("#explore_head_user").removeClass("hide");
    }else{
        $(".heart-detail-header").removeClass("headactive");
        $("#explore_head_user").addClass("hide");
    }
})