var k = getCookie("key");
var u = getCookie("id");
var type_id = getQueryString("type");
var search_status = "";
var search_content = "";
var page = 1;
var falg = false;
$(function () {
    if (type_id) {
        exploreList(type = type_id)
        exploreListType(type_id);
    } else {
        exploreList(type = 2)
    }
    //点击发布看是否登录
    $(document).on("click", ".btn-publish", function () {
        if (getCookie("key")) {
            window.location.href = ShopWapUrl + "/tmpl/explore.html";
        } else {
            $(".social-login-dialog").show();
        }
    });
    //点击信息查看是否登录
    $(document).on("click", ".social-message", function () {
        if (getCookie("key")) {
            window.location.href = ShopWapUrl + "/tmpl/explore_message.php";
        } else {
            $(".social-login-dialog").show();
        }
    });
    //点击顶部头像看是否登录
    $(document).on("click", ".social-user", function () {
        if (getCookie("key")) {
            window.location.href = ShopWapUrl + "tmpl/explore_center.php?user_id=" + u;
        } else {
            $(".social-login-dialog").show();
        }
    });

    //搜索查询
    $(document).on("click", "#search", function () {
        search();
    });
    //回车搜索查询
    $("body").keydown(function (e) {
        e = e || event;
        if (e.keyCode == "13") {//keyCode=13是回车键
            search();
        }
    });

    $(".social-login-dialog").click(function () {
        $(this).hide();
    })

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            page++;
            flag = true;
            exploreList(type_id, search_status, search_content, page, flag);
        }
    });
});

function exploreListType(type) {
    falg = false;
    page = 1;
    $(window).scrollTop(0);
    $("#waterfall-ul").next().remove();
    if (type == 1) {
        $(".find-cut1").addClass('active');
        $(".find-cut2").removeClass('active');
    } else {
        $(".find-cut2").addClass('active');
        $(".find-cut1").removeClass('active');
    }
    exploreList(type, '', '', 1, falg)
}

function exploreList(type, search_status, search_content, page = 1, flag = false) {
    //判断是发现还是关注 type = 1、关注 type = 2、发现
    //判断是否登录
    if (getCookie("key")) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getExploreList&typ=json",
            data: {k: k, u: u, type: type, search_status: search_status, search_content: search_content, page: page},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (type == 1) {
                        if (res.data.explore_base.length > 0) {
                            var a = template.render("waterfall-template", res);
                            if (flag == true) {
                                $("#waterfall-ul").append(a);
                            } else {
                                $("#waterfall-ul").html(a);
                            }
                            $(".social-user").find("img").attr("src", res.data.user_info.user_logo);
                            if (res.data.message_sum.message_sum > 0) {
                                $(".icon-infor").next().html("<b>" + res.data.message_sum.message_sum + "</b>");
                            }
                        } else {
                            if (search_status == 1 || search_status == 2) {
                                var no_search_content = template.render("no-search-content");
                                if (flag != true) {
                                    $("#waterfall-ul").html("");
                                    $("#fid-rgt").append(no_search_content);

                                }
                            } else {
                                var no_content = template.render("no-content");
                                if (flag != true) {
                                    $("#waterfall-ul").html("");
                                    $("#fid-rgt").append(no_content);
                                }

                            }
                        }
                    } else {
                        if (res.data.explore_base.length > 0) {
                            console.log(res);
                            var a = template.render("waterfall-template", res);
                            if (flag == true) {
                                $("#waterfall-ul").append(a);
                            } else {
                                $("#waterfall-ul").html(a);
                            }
                            $(".social-user").find("img").attr("src", res.data.user_info.user_logo);
                            if (res.data.message_sum.message_sum > 0) {
                                $(".icon-infor").next().html("<b>" + res.data.message_sum.message_sum + "</b>");
                            }
                        } else {
                            if (search_status == 1 || search_status == 2) {
                                var no_search_content = template.render("no-search-content");
                                if (flag != true) {
                                    $("#fid-rgt").html("");
                                    $("#fid-rgt").append(no_search_content);
                                }
                            } else {
                                var no_find_content = template.render("no-find-content");
                                if (flag != true) {
                                    $("#waterfall-ul").html("");
                                    $("#fid-rgt").append(no_find_content);
                                }
                            }
                        }
                    }
                } else {
                    $.sDialog({
                        skin: "red",
                        content: res.msg,
                        okBtn: false,
                        cancelBtn: false
                    });
                }
            }
        });
    } else {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=getExploreList&typ=json",
            data: {k: k, u: u, type: type, search_status: search_status, search_content: search_content, page: page},
            dataType: "json",
            success: function (res) {
                res.data.login_status = 0;
                if (res.status == 200) {
                    if (type == 1) {
                        var gz_login = template.render("gz-login");
                        $("#fid-rgt").html(gz_login);
                    } else {
                        if (res.data.explore_base.length > 0) {
                            var a = template.render("waterfall-template", res);
                            if (flag == true) {
                                $("#waterfall-ul").append(a);
                            } else {
                                $("#waterfall-ul").html(a);
                            }
                            $(".social-user").find("img").attr("src", '../../images/new/default-img.png');
                            if (res.data.message_sum.message_sum > 0) {
                                $(".icon-infor").next().html("<b>" + res.data.message_sum.message_sum + "</b>");
                            }
                        } else {
                            if (search_status == 1 || search_status == 2) {
                                var no_search_content = template.render("no-search-content");
                                if (flag != true) {
                                    $("#waterfall-ul").html("");
                                    $("#fid-rgt").append(no_search_content);
                                }
                            } else {
                                var no_find_content = template.render("no-find-content");
                                if (flag != true) {
                                    $("#waterfall-ul").html("");
                                    $("#fid-rgt").append(no_find_content);
                                }
                            }
                        }
                    }
                } else {
                    $.sDialog({
                        skin: "red",
                        content: res.msg,
                        okBtn: false,
                        cancelBtn: false
                    });
                }
            }
        });
    }
}

//用户点赞
function changetext(res) {
    //document.getElementById("video").pause();
    if (getCookie("key")) {
        var _this = $("#onclick_" + res).children().find(".active");
        $.ajax({
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editExploreLike&typ=json",
            type: "POST",
            data: {
                k: getCookie("key"),
                u: getCookie("id"),
                explore_id: res
            },
            dataType: "json",
            success: function (result) {
                console.log(result);
                if (result.status == 200) {
                    console.log(result.data.explore_like_count);
                    if (_this.hasClass('active')) {
                        //取消点赞
                        $("#onclick_" + res).children().find(".praise").removeClass('active');
                        $("#onclick_" + res).children().find("#user_account").html(result['data']['explore_like_count']);
                    } else {
                        //点赞
                        $("#onclick_" + res).children().find(".praise").addClass('active');
                        $("#onclick_" + res).children().find("#user_account").html(result['data']['explore_like_count']);
                    }
                } else {
                    if (_this.hasClass('active')) {
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
    } else {
        $(".social-login-dialog").show();
    }
}

function search() {
    var a = $("#keyword").val();
    var active = $(".find-cut2").hasClass('active');
    if (active == true) {
        // var type = 2;
        type_id = 2;
    } else {
        // var type = 1;
        type_id = 2;
    }
    //判断是否是标签搜索
    if (a.trim().substr(0, 1) == "#") {
        //标签搜素
        // var search_status = 1;
        // var search_content = a;
        search_status = 1;
        search_content = a;
    } else {
        //标题搜素
        // var search_status = 2;
        // var search_content = a;
        search_status = 2;
        search_content = a;
    }
    exploreList(type_id, search_status, search_content);
}

