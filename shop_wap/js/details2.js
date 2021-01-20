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
        url: ApiUrl + "/index.php?ctl=Goods_Goods&met=getGoodsDetailFormat&typ=json",
        data: {gid: goods_id},
        type: "get",
        success: function (data) {
            common_id = data.data.common_id;
            console.log(data);
        }
    });
});