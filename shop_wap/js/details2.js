var key = getCookie('key');
var common_id = '';
$(function(){
    var goods_id = getQueryString("goods_id");
    //收藏
    $(".pd-collect").click(function () {
        if ($(".pd-collect").html() == "已收藏") {
            if (dropFavoriteGoods(goods_id)) {
                $(".pd-collect").html("收藏");
            }
        } else {
            if (favoriteGoods(goods_id)) {
                $(".pd-collect").html("已收藏");
            }
        }
    });

    //点赞
    $(".btn-zan").click(function () {
        $.ajax({
            type: 'post',
            url: ApiUrl + '/index.php?ctl=Goods_Goods&met=addZan&typ=json',
            data: {k: key,u:getCookie('id'), common_id: common_id},
            dataType: 'json',
            async: false,
            success: function(result) {
                if (result.status == 200) {
                    $.sDialog({skin: "green", content: result.msg, okBtn: false, cancelBtn: false});
                    $("#zan_sum").html(result.data.zan_sum);
                    return_val = true;
                } else {
                    $.sDialog({skin: "red", content: result.msg, okBtn: false, cancelBtn: false});
                }
            }
        });
    });

    $.ajax({
            url: ApiUrl + "/index.php?ctl=Goods_Goods&met=goods&typ=json",
            type: "get",
            data: {goods_id: goods_id, k: key, u: getCookie("id"), ua: "wap"},
            dataType: "json",
            success: function (result) {


                var data = result.data;

                $("#shop_name").html(data.goods_info.shop_name);
                $("#shop_img").attr("style","background:url("+data.store_info.store_logo+") no-repeat center;background-size:contain");
                console.log(data);
                if (data.goods_image) {
                    data.goods_image = data.goods_image.split(";");
                } else {
                    data.goods_image = [];
                }
                var html = template.render("product_detail", data);
                $("#goods_image").html(html);
                var swiper = new Swiper('.custom-product-det-swiper', {
                    autoplay:3000,
                    pagination: '.swiper-pagination',
                    paginationType: 'fraction'
                });
            }
    });

    $.ajax({
        url: ApiUrl + "/index.php?ctl=Goods_Goods&met=getGoodsDetailFormat&typ=json",
        data: {gid: goods_id},
        type: "get",
        success: function (data) {

            var html = data.data.common_detail;
            $(".fixed-tab-pannel").html(html);
            // $("#content").html(data.data.common_detail);
        }
    });
});