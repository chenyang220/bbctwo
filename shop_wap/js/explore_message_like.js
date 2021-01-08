var page = 10;
var pagesize = 10;
var curpage = 0;
var hasMore = true;
var footer = false;

$(function () {
    get_detail();

    function get_detail() {
        if (!hasMore) {
            return false
        }
        hasMore = true;

        $.ajax({
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getLikeMessage&typ=json",
            type: "POST",
            data: {
                k: getCookie("key"),
                u: getCookie("id"),
                firstRow:curpage,
            },
            dataType: "json",
            success: function (e) {
                if (e.status == 200) {
                    curpage = e.data.page * pagesize;
                    if(page > e.data.records)
                    {
                        hasMore = false;
                    }

                    if (!hasMore) {
                        get_footer()
                    }

                    if (e.data.items.length <= 0) {
                        console.log(curpage);
                        if(curpage === 10){
                            $('.social-nodata').removeClass('hide');
                        }
                    } else {
                        $('.social-nodata').addClass('hide');
                    }

                    var like_html = template.render('like-more', e);
                    $("#like").append(like_html);

                } else {
                    $.sDialog({
                        skin: "red",
                        content: '获取数据失败，请刷新重试！',
                        okBtn: false,
                        cancelBtn: false
                    });
                }
            }
        });
    }

    function get_footer() {
        if (!footer) {
            footer = true;
            $.ajax({url: "../../js/tmpl/footer.js", dataType: "script"})
        }
    }

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            get_detail()
        }
    })

    $(document).on("click",".jump",function () {
        var _this = $(this);
        var explore_id = _this.attr("explore_id");
        var active_id = _this.attr("active_id");
        var is_del = _this.attr("is_del");
        var type = _this.attr("type");

        //判断是否被删除
        if(is_del == 0) {
            //点赞心得，直接跳转到心得详情页
            if(type == 1) {
                window.location.href = WapSiteUrl + "/tmpl/explore_base.html?explore_id="+explore_id;
            }
            //点赞评论。跳转到心得详情页中的全部评论页面
            if(type == 5) {
                window.location.href = WapSiteUrl + "/tmpl/explore_base.html?explore_id="+explore_id+"&active_id="+active_id+"&type="+type;
            }
            //点赞回复。跳转到全部回复页面
            if(type == 6) {
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

    })
})