$(function () {
    var k = getCookie("key");
    var u = getCookie("id");

    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
    //页面数据渲染
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Explore_Explore&met=exploreFindFriends&typ=json",
        data: {k: k, u: u, },
        dataType: "json",
        success: function (res) {
            if (res.status == 200) {
                if (res.data.items.length > 0){
                    var r = template.render("find-fiends-template", res.data);
                    $(".discover-list-items").html(r);
                     var swiper = new Swiper('.discover-list-swiper', {
                        slidesPerView: "auto",
                        freeMode: true
                    });
                    $(".social-nodata").addClass('hide');
                } else{
                    $(".social-nodata").removeClass('hide');
                }
            }
        }
    });

    //搜索好友
    $(".search-input").keypress(function (e) {
        var user_name = $(this).val();
        var data = {
            k: k,
            u: u,
            user_name: user_name,
        };
        if (e.keyCode == 13) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Explore_Explore&met=exploreFindFriends&typ=json",
                data: data,
                dataType: "json",
                success: function (res) {
                    if (res.status == 200) {
                        $('.discover-search-friends').parent().removeClass('hide');
                        $(".discover-list-items").addClass('hide');
                        if (res.data.items.length > 0) {
                            $(".load-completion").removeClass('hide');
                            var r = template.render("search-fiends-template", res.data);
                            $(".discover-search-friends").html(r);
                            $(".social-nodata").addClass('hide');
                        } else {
                            $(".social-nodata").removeClass('hide');
                            $(".load-completion").addClass('hide');
                        }
                    }
                }
            })
        }
    })

    // 搜索清空按钮显示隐藏
    $(".discover-search").bind('input porpertychange',function(){
        if($(this).val().length !=0){
            $(".discover-box-head .icon-close").removeClass("hide");
        }else{
            $(".discover-box-head .icon-close").addClass("hide");
        }
    });

    //关注、取消关注
    $(document).on('click','.follow',function () {
        var _this = $(this);
        var user_id = _this.data('user_id');
        var flag = _this.hasClass('isFollow');
        var data = {
            k: k,
            u: u,
            user_id: user_id,
        };
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=editUserFollow&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (flag) {
                        var html = "<i class='iconfont icon-add'></i><em>关注</em>";
                        _this.removeClass('isFollow');
                        _this.removeClass('active');
                    }else {
                        var html = "<em>已关注</em>";
                        _this.addClass('isFollow');
                        _this.addClass('active');
                    }
                    _this.html(html);
                }
            }
        })
    })
    $(window).scroll(function(){
        if($(window).scrollTop() >=10){
            $(".discover-box-header").addClass("active");
        }else{
            $(".discover-box-header").removeClass("active");
        }
    })

    $(document).on('click','.clean-btn',function(){
        $(".search-input").val('');
        $(this).addClass("hide");
    })

});

