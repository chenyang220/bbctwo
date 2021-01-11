var rowData = {};
var item_info = [];
init();

function init() {
    Public.ajaxPost(SITE_URL + "?ctl=Special_Column&typ=json&met=getColumnInfo&special=0&is_from=1", {}, function (rs) {
        200 == rs.status ? (rowData = rs.data, item_info = rs.data.goods_common, initField(), initEvent()) : $.dialog({
            title: __('系统提示'),
            content: "获取专题栏目数据失败，暂不能修改专题栏目，请稍候重试",
            icon: "alert.gif",
            max: !1,
            min: !1,
            cache: !1,
            lock: !0,
            ok: "确定",
            ok: function () {
                return !0
            },
            close: function () {
                return !0
            }
        })
    })
}

function initField() {
    //活动id
    $("#column_id").val(rowData.id);
    var column_image = rowData.special_column_image;
    for (var i in column_image) {
        $("#column_image" + i).prop('src', column_image[i].img_path);
        $("#column_logo" + i).val(column_image[i].img_path);
        $("#column_url" + i).val(column_image[i].img_url);
    }
    initGoods(rowData.goods_common);
}

function initEvent() {
    //手动添加推荐商品
    $("#add-goods").click(function(){
        handle.operate("edit")
    });

    //删除手动添加的商品
    $(document).on('click','.del-goods',function() {
        //删除商品数组中对应的数据image_infos
        var place = $(this).data('place');
        item_info.splice(place, 1);
        $(this).parent().remove();
    })

    //删除已上传的图片
    $(".del-img").click(function () {
        $(this).next().val('');
        $(this).prev().prop('src','../shop_admin/static/common/images/image.png');
    })

}

var handle = {
    operate: function (t) {
        var data = {
            items_info: item_info
        };
        var i = "添加推荐商品", a = {oper: t, data, callback: this.callback};
        $.dialog({
            title: i,
            content: "url:./index.php?ctl=Special_Column&met=columnGoods",
            data: a,
            width: 700,
            height: 500,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    },
    callback: function (t, i,data) {
        item_info = data.items;
        initGoods(item_info);
    },
};

function initGoods(item_info)
{
    if (item_info) {
        var html = '';
        for (var i = 0; i < item_info.length; i++) {
            html += '<li data-goods_id="' + item_info[i]['goods_id'] + '" class="goods_info" >' +
                '<div><em class="img-box"><img class="wp100" src="' + item_info[i]['goods_image'] + '" alt="" style="height:136px;width:136px;"></em>' +
                '<span class="goods_name" style="height:8rem">' + item_info[i]['goods_name'] + '</span>' +
                '<strong>' + item_info[i]['goods_price'] + '</strong></div>' +
                '<p class="del-goods">删除</p>' +
                '</li>';
        }
        $("#goods-info").append(html);
    }
}

$(".submit-btn").click(function () {
    postData();
});


function postData() {
    var column_id = $("#column_id").val();
    var image_infos = [];
    $(".banner_image").find('dd').each(function () {
        var image_info = {}
        var img_path = $(this).find('.img-path').val();
        var img_url = $(this).find('.img-url').val();
        if (img_path || img_url){
            image_info.img_path = img_path;
            image_info.img_url = img_url;
            image_infos.push(image_info);
        }
    });

    var goods_common = [];
    $(".goods_info").each(function () {
        var common_id = $(this).data('goods_id');
        goods_common.push(common_id);
    });

    var params = {
        special_type:0,
        column_id: column_id,
        goods_common: goods_common,
        image_infos: image_infos,
    };
    Public.ajaxPost(SITE_URL + "?ctl=Special_Column&typ=json&met=AddWapOrEditColumn", params, function (data) {
        if (data.status == 200) {
            parent.parent.Public.tips({content: "编辑成功！"});
            typeof callback == 'function' && callback();
        } else {
            parent.parent.Public.tips({type: 1, content: "编辑失败！"});
        }
    });
}
