var comment_id = getQueryString("comment_id");
var explore_id = getQueryString("explore_id");
var reply_id = getQueryString("reply_id");
var key = getCookie("key");//登录标记
var id = getCookie("id");

$(function () {
    get_detail(comment_id);
});

//渲染页面
function get_detail(comment_id) {
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=getReplyAll&typ=json",
        type: "get",
        data: {comment_id: comment_id, k: key, u: getCookie("id")},
        dataType: "json",
        success: function (result) {
            console.log(result);
            if (result.status == 200) {
                $(".tit").html("共"+result.data.sum+"条回复");
                var a = template.render("comments-more", result);
                $("#heart-comment-box").html(a);
                $('#reply_content').attr('to-reply-id','');
                $('#reply_content').attr('placeholder','发表回复');
                $('#reply_content').val('');

                if(reply_id) {
                    goto(reply_id);
                }
            } else {
                $.sDialog({
                    content: result.msg,
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

//锚点定位
function goto(reply_id) {
    var Top = document.querySelector(".reply"+reply_id).offsetTop;
    $(window).scrollTop(Top*1-window.screen.height/2);

}

//点击回复（直接聚焦输入框回复）
$(document).on("click", ".js-comment-opearate li", function () {
    var _this=$(this);
    var type = _this.attr('type');
    var user_account = _this.find(".one-overflow").html();

    //聚焦输入框
    $('#reply_content').attr('placeholder','回复@'+user_account);
    $('#reply_content').focus();
    $('#reply_content').attr('to-reply-id',$(this).attr('reply-id'));

});

//发送回复
$(document).on("click", "#send", function () {
    var type = $("#reply_content").attr('to-reply-id');
    var reply_content = $("#reply_content").val();
    var comment_id = $('#reply_type').attr('comment-id');
    //判断是用户间回复还是对作者的回复
    if(!type) {
        var  reply_id = 0;
    } else {
        var  reply_id = type;
    }
    //用户添加回复
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
                get_detail(comment_id);
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
});
//回复点赞与取消点赞按钮
$(document).on("click", ".like-reply-btn", function () {
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
});
