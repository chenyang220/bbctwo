$(function () {
    var k = getCookie("key");
    var u = getCookie("id");
    var explore_id = getQueryString('explore_id');
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }

    //页面数据渲染
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Explore_UnExplore&met=getGoodsByExploreId&typ=json",
        data: {k: k, u: u, explore_id: explore_id},
        dataType: "json",
        success: function (res) {
            if (res.status == 200) {
                $(".tit").html(res.data.length + '个商品');
                var r = template.render("goods-list-template", res);
                $(".discover-goods-items").html(r)
            }
        }
    })

});

