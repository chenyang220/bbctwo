$(function () {
    var t = getCookie("key");
    // if (!t) {
    //     window.location.href = WapSiteUrl + "/tmpl/member/login.html";
    //     return false;
    // }
    // var e = new ncScrollLoad;
    // e.loadInit({
    //     url: ApiUrl + "/index.php?ctl=Buyer_Favorites&met=favoritesShop&typ=json",
    //     getparam: {k: t, u: getCookie("id")},
    //     tmplid: "sfavorites_list",
    //     containerobj: $("#favorites_list"),
    //     iIntervalId: true,
    //     data: {WapSiteUrl: WapSiteUrl}
    // });

    $.ajax({
        url: ApiUrl + "/index.php?ctl=Buyer_Favorites&met=favoritesShop&typ=json",
        data: {k: t, u: getCookie("id")},
        type: 'post',
        dataType: 'json',
        success: function(data) {
            if (data.status == 200) {


                console.log(data.data)
                 var r = template.render("sfavorites_list", data.data);
                    $("#favorites_list").html(r)
                    // if($("#viewlist li").hasClass('active'))
                    // {
                    //     $("#viewlist li").removeClass('active');
                    //     $("#viewlist li").addClass('active');
                    // }
            }
        }
    });
    $("#favorites_list").on("click", "[nc_type='fav_del']", function () {
        var t = $(this).attr("data_id");
        if (t <= 0) {
            $.sDialog({skin: "red", content: "取消收藏失败", okBtn: false, cancelBtn: false});
        }
        if (dropFavoriteStore(t)) {
            $("#favitem_" + t).remove();
            if (!$.trim($("#favorites_list").html())) {
                location.href = WapSiteUrl + "/tmpl/member/favorites_store.html";
            }
        }
    });

});
