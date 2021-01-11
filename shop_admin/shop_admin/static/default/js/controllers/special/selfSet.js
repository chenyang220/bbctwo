var rowData = {};
var item_info = [];
init();

function init() {
    Public.ajaxPost(SITE_URL + "?ctl=Special_Column&typ=json&met=getColumnInfo&special=1", {}, function (rs) {
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
    $("#back_image").prop('src', rowData.special_back_img);
    $("#back_logo").val(rowData.special_back_img);
    var column_image = rowData.special_column_image;
    for (var i in column_image) {
        $("#column_image" + i).prop('src', column_image[i].img_path);
        $("#column_logo" + i).val(column_image[i].img_path);
        $("#column_url" + i).val(column_image[i].img_url);
    }
    //商品信息
    initGoods(rowData.goods_common);

    //版式图片
    setImage(rowData.set_info);
}

function initEvent() {
    //选择版式
    $("input[name='image']").click(function(){
        var value = $(this).val();
        handle.image(value);
    });
    $("#choose").click(function () {
        handle.column()
    });
    //手动添加推荐商品
    $("#add-goods").click(function () {
        handle.operate("edit")
    });

    //删除手动添加的商品
    $(document).on('click', '.del-goods', function () {
        //删除商品数组中对应的数据
        var place = $(this).data('place');
        item_info.splice(place, 1);
        $(this).parent().remove();
    })

    //删除已上传的图片
    $(".del-img").click(function () {
        $(this).next().val('');
        $(this).prev().prop('src', '../shop_admin/static/common/images/image.png');
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
    callback: function (t, i, data) {
        item_info = data.items;
        initGoods(item_info);
    },
    image: function (t) {
        $.dialog({
            title: '上传图片',
            content: "url:./index.php?ctl=Special_Column&met=columnImage",
            data: {data: t, callback: this.callback1},
            width: 700,
            height: 500,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    },
    callback1: function (set,data) {
        $("input[name='image']").attr('checked',false);
        if (set == 1){
            var html = "<li class='setImage style1 wp100 mr0'><i class='iconfont icon-cancel'></i><div class='div-image wp100' data-set ='1'><em class='img-box'><img class='wp100' src='" + data[0].img_path + "' alt=''></em><input class='set_path' type='hidden' value='" + data[0].img_path + "'><input class='set_url' type='text' value='" + data[0].img_url + "'></div></li>";
        }else{
            var html = "<li class='setImage style2 wp100'><i class='iconfont icon-cancel'></i>";
            for (var i = 0; i < data.length; i++) {
                html += "<div class='div-image wp25 align-top' data-set ='2'><em class='img-box'><img class='wp100' src='" + data[i].img_path + "' alt=''></em><input class='set_path' type='hidden' value='" + data[i].img_path + "'><input class='set_url' type='text' value='" + data[i].img_url + "'></div>";
            }
            html += "</li>";
        }
        $("#setImage").append(html);
    },
    // column: function (t) {
    //     $.dialog({
    //         title: '选择版式',
    //         content: "url:./index.php?ctl=Special_Column&met=columnSelf",
    //         data: {callback: this.callback2},
    //         width: 700,
    //         height: 500,
    //         max: !1,
    //         min: !1,
    //         cache: !1,
    //         lock: !0
    //     })
    // },
    // callback2: function (t, i, data) {
    //     console.log(t)
    //     console.log(i)
    //     console.log(data)
    // },
};

//删除版块
$(document).on('click','.icon-cancel',function () {
    var special_set_id = $(this).parent().data('special_set_id');
    var that = $(this);
    if (special_set_id) {
        Public.ajaxPost(SITE_URL + "?ctl=Special_Column&typ=json&met=removeColumn", {special_set_id: special_set_id}, function (data) {
            if (data.status == 200) {
                Public.tips({content: "编辑成功！"});
                setTimeout(function () {
                    that.parent().remove();
                },1000);
            } else {
                Public.tips({type: 1, content: "编辑失败！"});
            }
        });
    }else{
        $(this).parent().remove();
    }
});


//商品信息
function initGoods(item_info) {
    if (item_info) {
        var html = '';
        for (var i = 0; i < item_info.length; i++) {
            html += '<li data-goods_id="' + item_info[i]['goods_id'] + '" class="goods_info" >' +
                '<div><em class="img-box"><img class="wp100" src="' + item_info[i]['goods_image'] + '" alt="" style="height:136px;width:136px;"></em>' +
                '<span class="goods_name one-overflow">' + item_info[i]['goods_name'] + '</span>' +
                '<strong>' + item_info[i]['goods_price'] + '</strong></div>' +
                '<p class="del-goods">删除</p>' +
                '</li>';
        }
        $("#goods-info").append(html);
    }
}

//版式信息
function setImage(set_info){
    if (set_info){
        var html = "";
        for (var i = 0; i < set_info.length; i++) {
            html += "<li class='setImage style2 wp100' data-special_set_id='" + set_info[i]['id'] + " '><i class='iconfont icon-cancel'></i>";
            var column_set_image = set_info[i]['column_set_image'];
            var column_set_type = set_info[i]['column_set_type'];
            for (var j = 0; j < column_set_image.length; j++) {
                html += "<div class='div-image wp25 align-top' data-set='" + column_set_type + "'>" +
                "<em class='img-box'><img class='wp100' src='" + column_set_image[j]['set_path'] + "' alt=''></em>" +
                "<input class='set_path' type='hidden' value='" + column_set_image[j]['set_path'] + "'>" +
                "<input class='set_url' type='text' value='" + column_set_image[j]['set_url'] + "'></div>";
            }
                "</li>";
        }
        $("#setImage").append(html);
    }
}


$(".submit-btn").click(function () {
    postData();
});


function postData() {
    var column_id = $("#column_id").val();
    var special_back_img = $("#back_logo").val();
    var image_infos = [];
    $(".banner_image").find('dd').each(function () {
        var image_info = {}
        var img_path = $(this).find('.img-path').val();
        var img_url = $(this).find('.img-url').val();
        if (img_path || img_url) {
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

    var setImage = [];
    $(".setImage").each(function () {
        var that = $(this);
        var set_info = []
        var images = {}
        var set_type = that.find('div').data('set');
        var column_set_id = that.data('special_set_id');
        that.find('div').each(function () {
            var info = {}
            var set_path = $(this).find('.set_path').val();
            var set_url = $(this).find('.set_url').val();
            if (set_path || set_url) {
                info.set_url = set_url;
                info.set_path = set_path;
                set_info.push(info);
            }
            if (column_set_id) {
                images.column_set_id = column_set_id;
            }else{
                images.column_set_id = '';
            }
            images.type = set_type;
            images.info = set_info;
        });
        setImage.push(images);
    });
    var params = {
        special_type: 1,
        column_id: column_id,
        goods_common: goods_common,
        image_infos: image_infos,
        setImage: setImage,
        special_back_img: special_back_img,
    };
    Public.ajaxPost(SITE_URL + "?ctl=Special_Column&typ=json&met=AddOrEditColumn", params, function (data) {
        if (data.status == 200) {
            parent.parent.Public.tips({content: "编辑成功！"});
            typeof callback == 'function' && callback();
        } else {
            parent.parent.Public.tips({type: 1, content: "编辑失败！"});
        }
    });
}
