var key = getCookie("key");//登录标记
var id = getCookie("id");
var user_get_id = getQueryString("user_id");
$(function () {
    if (typeof(user_get_id) != "undefined") {
       var user_id = user_get_id;
    } else {
       var user_id = getCookie("id");
    }
    get_detail(user_id)
});

function get_detail(user_id) {
    var data = {
        k: getCookie("key"),
        u: getCookie("id"),
        user_id:user_id,
    };
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getFollowList&typ=json",
        data: data,
        dataType: "json",
        success: function (res) {
            if (res.status == 200) {
                //判断是否是当前用户的关注页面
                if(user_id !== id) {
                    $(".tit").html(res.data.user_info.user_account+'的关注');
                }

                if (res.data.user.length > 0) {
                    var a = template.render("follow-template", res);
                    $("#fid-rgt").html(a);
                    $("#load-completion-di").html('<p class="load-completion"><i class="iconfont icon-icon03"></i><em>已经到底咯~</em></p>');
                } else {
                    var follow_no_content = template.render("follow_no_content");
                    $("#fid-rgt").html(follow_no_content);
                    $("#load-completion-di").html('');
                }
            } else {
                $(".social-login-dialog").show();
                // $.sDialog({
                //     content: res.msg,
                //     okBtn: false,
                //     cancelBtnText: "返回",
                //     cancelFn: function () {
                //         history.back();
                //     }
                // });
            }
        }
    });
}
//点击关注与取消关注按钮
$(document).on("click", ".btn-attention", function () {
    var _this=$(this);
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editUserFollow&typ=json",
        type: "POST",
        data: {
            k: getCookie("key"),
            u: getCookie("id"),
            user_id: $(this).attr('user-id')
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                if(_this.hasClass('active')) {
                    //取消关注
                    _this.removeClass('active');
                    _this.prepend('<i class="iconfont icon-add"></i>');
                    _this.find('em').html('关注');
                } else {
                    //关注
                    _this.addClass("active");
                    _this.find(".icon-add").remove();
                    _this.find('em').html('已关注');
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
});
$(".social-login-dialog").click(function(){
    $(this).hide();
})