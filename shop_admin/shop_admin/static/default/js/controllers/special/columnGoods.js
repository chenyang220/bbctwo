var api = frameElement.api,
    items_info = api.data.data.items_info? api.data.data.items_info:[],
    callback = api.data.callback;
var goods_ids = new Array();
var items = new Array();

//init
//分类名称
var goods_html = new String();
for (var i = 0; i < items_info.length; i++) {
    var item = items_info[i];
    goods_html = '<div nctype="item_image" class="item">' +
        '<div class="goods-pic"><img width="220px" height="220px" nctype="image" src="' + item.goods_image + '" alt=""></div>' +
        '<div class="goods-name" nctype="goods_name">' + item.goods_name + '</div>' +
        '<div class="goods-price" nctype="goods_price">' + item.goods_price + '</div>' +
        '<input nctype="goods_id" name="item_data[item][]" type="hidden" value="' + item.goods_id + '">' +
        '<input nctype="common_order" name="common_order" type="hidden" value="' + item.common_order + '">' +
        '<a nctype="btn_del_item_image" href="javascript:;">删除</a>' +
        '</div>';
    $('[nctype="item_content"]').append(goods_html);
    goods_ids.push(items_info[i].goods_id);
}

//搜索商品
$('#btn_mb_special_goods_search').on('click', function () {
    var keyword = $('#txt_goods_name').val();
    var shop_name = $('#txt_shop_name').val();
    //这里不需要判断搜索词是否为空，如果为空则显示所有商品
    $("#grid").jqGrid('setGridParam', {
        page: 1,
        postData: {
            is_immediate: 1, //马上装商品
            common_state: 1,    //正常状态
            common_verify: 1,   //通过审核
            common_name: keyword,
            shop_name: shop_name,
        }
    }).trigger("reloadGrid");
});

//添加为推荐商品
$('#mb_special_goods_list').on('click', '[nctype="btn_add_goods"]', function () {
    var item = {},
        rowId = $(this).data('id'),
        rowData = $('#grid').jqGrid('getRowData', rowId);
    common_image = $("#grid").data('gridData')[rowId].common_image;
    goods_ids.push(rowData.common_id);
    item.goods_id = rowData.common_id;
    item.goods_name = rowData.common_name;
    item.goods_price = rowData.common_price;
    item.goods_image = common_image;
    items.push(item);
    var html = template.render('item_goods_template', item);
    $('[nctype="item_content"]').append(html);
});

//删除手动添加的商品
$('.goods-list').on('click', '[nctype="btn_del_item_image"]', function () {
    var goods_id = $(this).prev().val();
    goods_ids.remove(parseInt(goods_id));
    $(this).parents('div:eq(0)').remove();
});

var handle = {
    imageFmatter: function (val, opt, row) {
        if (row.common_image) {
            val = '<img width="60px" height="60px" src="' + row.common_image + '">';
        } else {
            val = '&#160;';
        }
        return val;
    },
    addFmatter: function (val, opt, row) {
        return '<span class="set-status ui-label ui-label-success" nctype="btn_add_goods" data-enable="1" data-id="' + row.id + '">' + '选择' + '</span>';
    }
};

function initGrid() {
    var grid_row = Public.setGrid();
    var colModel = [
        {"name": "common_id", "index": "common_id", "label": "商品SPU", "classes": "ui-ellipsis", "align": "center", "title": false, "width": 20},
        {"name": "common_name", "index": "common_name", "label": "商品名称", "classes": "ui-ellipsis", "align": "center", "title": false, "width": 40},
        {"name": "common_price", "index": "common_price", "label": "商品价格", "classes": "ui-ellipsis", "align": "center", "title": false, "width": 30},
        {"name": "common_image", "index": "common_image", "label": "商品主图", "classes": "ui-ellipsis", "align": "center", "title": false, "width": 30, "formatter": handle.imageFmatter, "classes": 'img_flied'},
        {"name": "shop_id", "index": "shop_id", "label": "操作", "classes": "ui-ellipsis", "align": "center", "title": false, "width": 20, "formatter": handle.addFmatter}
    ];
    $('#grid').jqGrid({
        url: SITE_URL + '?ctl=Goods_Goods&met=listCommon&typ=json',
        postData: {
            common_state: 1,    //正常状态
            common_verify: 1,    //通过审核
            product_distributor_flag:0
        },
        datatype: 'json',
        shrinkToFit: true,
        forceFit: true,
        width: 650,
        height: 300,
        colModel: colModel,
        pager: '#page',
        cmTemplate: {
            sortable: true
        },
        rowNum: 100,
        rowList: [10, 20, 50, 100, 200, 500],
        //scroll: 1,
        jsonReader: {
            root: "data.items",
            records: "data.records",
            total: "data.total",
            repeatitems: false,
            id: "common_id"
        },
        loadComplete: function (response) {
            if (response && response.status == 200) {
                var gridData = {};
                data = response.data;
                for (var i = 0; i < data.items.length; i++) {
                    var item = data.items[i];
                    item['id'] = item.common_id;
                    gridData[item.common_id] = item;
                    $("#grid").data('gridData', gridData);
                }
            } else {
                var msg = response.status === 250 ? ('没有数据哦！') : response.msg;
                parent.Public.tips({
                    type: 2,
                    content: msg
                });
            }
        },
        loadError: function (xhr, status, error) {
            parent.Public.tips({
                type: 1,
                content: '操作失败了哦，请检查您的网络链接！'
            });
        },
    }).navGrid('#page', {
        edit: false,
        add: false,
        del: false,
        search: false,
        refresh: false
    });
}

initGrid();
api.button({
    id: "confirm", name: '确定', focus: !0, callback: function () {
        postData();
        return false;
    }
}, {id: "cancel", name: '取消'});

//将数据渲染到父级页面
function postData() {
    var common_infos = [];
    $('[nctype="item_image"]').each(function () {
        var common_info = {}
        common_info.goods_image = $(this).find('.goods-pic').find('[nctype="image"]').attr('src');
        common_info.goods_name = $(this).find('.goods-name').html();
        common_info.goods_price = $(this).find('.goods-price').html();
        common_info.goods_id = $(this).find('[nctype="goods_id"]').val();
        common_info.common_order = $(this).find('[nctype="common_order"]').val();
        common_infos.push(common_info);
    });
    var data = {
        length: common_infos.length,
        items: common_infos
    }
    api.close();
    typeof callback == 'function' && callback('', '', data);
}

Array.prototype.remove = function (val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) {
            this.splice(i, 1);
        }
    }
};
