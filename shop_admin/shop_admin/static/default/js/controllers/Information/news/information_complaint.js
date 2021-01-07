function initEvent() {
        $_matchCon = $("#matchCon"),
        $_shopname = $("#shopname"),
        $_matchCon.placeholder(),
        $("#search").on("click", function (a) {
            a.preventDefault();
            var news_title = $_matchCon.val() ? $.trim($_matchCon.val()):"";
            var shopname = $_shopname.val() ? $.trim($_shopname.val()):"";
            // var article_group = $source.getValue();
            $("#grid").jqGrid("setGridParam", {page: 1, postData: {news_title: news_title, shopname: shopname}}).trigger("reloadGrid")
        });
    $('.wrapper').on('click', '#import', function (a) {
        a.preventDefault(),
        Business.verifyRight('SO_导入') && parent.$.dialog({
            width: 560,
            height: 300,
            title: '批量导入',
            content: 'url:./erp.php?ctl=Vendor_Base&met=import',
            lock: !0,
            data: function () {
                $("#search").trigger('click');
            }
        })
    });
    $('.wrapper').on('click', '#audit', function (a) {
        a.preventDefault();
        var b = $('#grid').jqGrid('getGridParam', 'selarrrow'),
            c = b.join();
        return c ? void Public.ajaxPost('./index.php?ctl=Information_News&met=complaint&typ=json', {
            id: c
        }, function (a) {
            200 === a.status ? parent.Public.tips({
                content: '通过成功！'
            }):parent.Public.tips({
                type: 1,
                content: a.msg
            }),
                $('#search').trigger('click')
        }):void parent.Public.tips({
            type: 2,
            content: '请先选择需要通过的项！'
        })
    });
    
    $('.wrapper').on('click', '#notaudit', function (a) {
        
        a.preventDefault();
        var b = $('#grid').jqGrid('getGridParam', 'selarrrow'),
            c = b.join();
        console.info(c);
        return c ? $.dialog.confirm("确定要拒绝吗？", function () {void Public.ajaxPost('./index.php?ctl=Information_News&met=notcomplaint&typ=json', {
            id: c
        }, function (a) {
            200 === a.status ? parent.Public.tips({
                content: '拒绝成功！'
            }):parent.Public.tips({
                type: 1,
                content: a.msg
            }),
                $('#search').trigger('click')
        })}):void parent.Public.tips({
            type: 2,
            content: '请先选择需要拒绝的项！'
        })
        
    });
    $("#export").click(function (t) {
        var b = "按品牌编号，品牌名称" === $_matchCon.val() ? "":$.trim($_matchCon.val()),
            d = b ? '&skey=' + b:'';
        window.open(SHOP_URL + "?ctl=Api_Goods_Brand&met=getBrandListExcel&uncheck=1&debug=1" + d);
    });
    /*$("#import").click(function (t)
    {
        var b = "按品牌编号，品牌名称" === $_matchCon.val() ? "" : $.trim($_matchCon.val()),
            d=b?'&matchCon=' + b:'';
        var f = './erp.php?ctl=Vendor_Base&typ=e&met=export' + d;
        $(this).attr('href', f)
    });*/
    $("#btn-add").click(function (t) {
        t.preventDefault();
        Business.verifyRight("INVLOCTION_ADD") && handle.operate("add")
    });
    
    $("#btn-refresh").click(function (t) {
        t.preventDefault();
        $("#grid").trigger("reloadGrid")
    });
    
    $("#grid").on("click", ".operating .ui-icon-pencil", function (t) {
        t.preventDefault();
        if (Business.verifyRight("INVLOCTION_UPDATE")) {
            var e = $(this).parent().data("id");
            handle.operate("edit", e)
        }
    });
    
    //跳转店铺详情
    $('#grid').on('click', '.operating .ui-icon-search', function (e) {
        e.preventDefault();
        var news_id = $(this).parent().data("id");
        $.dialog({
            title: "查看资讯详情",
            content: "url:" + SITE_URL + '?ctl=Information_News&met=newsdetailslist&news_id=' + news_id,
            width: 950,
            height: $(window).height(),
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
        
    });
    
    $("#grid").on("click", ".operating .ui-icon-trash", function (t) {
        t.preventDefault();
        if (Business.verifyRight("INVLOCTION_DELETE")) {
            var e = $(this).parent().data("id");
            handle.del(e)
        }
    });
    
    $(window).resize(function () {
        Public.resizeGrid()
    })
}

function initGrid() {
    var t = ["操作", "资讯标题",  "发布方", "发布方名称", "添加时间", ], e = [{
        name: "operate",
        width: 60,
        fixed: !0,
        align: "center",
        formatter: operFmattershop
    },
        {name: "title", index: "title", width: 200, align: "center"},
        {name: "author_name", index: "newsclass_name", width: 100, align: "center"},
        {name: "authorname", index: "newsclassname", width: 100, align: "center"},
        {name: "create_time", index: "create_time", width: 150},
    ];
    
    $("#grid").jqGrid({
        url: SITE_URL + "?ctl=Information_News&met=informationNewsList&typ=json&status=1&auditing=1&complaint=2",
        datatype: "json",
        height: Public.setGrid().h,
        colNames: t,
        colModel: e,
        autowidth: !0,
        pager: "#page",
        viewrecords: !0,
        multiselect: true,
        multiboxonly: true,
        cmTemplate: {sortable: !1, title: !1},
        page: 1,
        rowNum: 100,
        rowList: [100, 200, 500],
        shrinkToFit: false,
        forceFit: true,
        jsonReader: {root: "data.items.items", records: "data.items.records", total: "data.items.total", repeatitems: !1, id: "id"},
        loadComplete: function (t) {
            if (t && 200 == t.status) {
                var e = {};
                t = t.data.items;
                for (var i = 0; i < t.items.length; i++) {
                    var a = t.items[i];
                    e[a.id] = a;
                }
                $("#grid").data("gridData", e);
                0 == t.items.length && parent.Public.tips({type: 2, content: "没有分类数据！"})
            }
            else {
                parent.Public.tips({type: 2, content: "获取品牌数据失败！" + t.msg})
            }
        },
        loadError: function () {
            parent.Public.tips({type: 1, content: "操作失败了哦，请检查您的网络链接！"})
        }
    })
}

var handle = {
    operate: function (t, e) {
        if ("add" == t) {
            var i = "新增品牌", a = {oper: t, callback: this.callback, menu: 0};
        }
        else {
            var i = "修改资讯新闻", a = {oper: t, rowData: $("#grid").data("gridData")[e], callback: this.callback, menu: 0};
        }
        $.dialog({
            title: i,
            content: 'url:' + SITE_URL + "?ctl=Information_News&met=" + t + "News&news_id=" + e,
            data: a,
            width: 874,
            height: $(window).height(),
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    }, callback: function (t, e, i, p) {
        var a = $("#grid").data("gridData");
        if (!a) {
            a = {};
            $("#grid").data("gridData", a)
        }
        a[t.brand_id] = t;
        if (p == 0) {
            if ("edit" == e) {
                $("#grid").jqGrid("setRowData", t.brand_id, t);
                i && i.api.close()
            }
            else {
                $("#grid").jqGrid("addRowData", t.brand_id, t, "last");
                i && i.api.close()
            }
        }
        else {
            i && i.api.close()
        }
        
    }, del: function (t) {
        $.dialog.confirm("删除的资讯新闻将不能恢复，请确认是否删除？", function () {
            Public.ajaxPost(SITE_URL + "?ctl=Information_News&met=removeBase&typ=json", {article_id: t}, function (e) {
                if (e && 200 == e.status) {
                    parent.Public.tips({content: "删除成功！"});
                    $("#grid").jqGrid("delRowData", t)
                }
                else {
                    parent.Public.tips({type: 1, content: "删除失败！" + e.msg})
                }
            })
        })
    }
};

function online_imgFmt(val) {
    var val = '<img src="' + val + '" style="width:100px;height:40px;">';
    return val;
}

initEvent();
initGrid();

function operFmattershop(val, opt, row) {
    var html_con = '<div class="operating" data-id="' + row.id + '"><span class="ui-icon ui-icon-search" title="查看"></span><span class="ui-icon ui-icon-trash" title="删除"></span></div>';
    return html_con;
    
};
// $(function () {
//
//     $.get("./index.php?ctl=Information_NewsClass&met=newstypelist&typ=json", function (result) {
//
//         var r = result.data;
//         console.log(r);
//         $source = $("#goods_cat").combo({
//             data: r,
//             value: "id",
//             text: "typename",
//             width: 110
//         }).getCombo();
//
//     });
//
//
//     THISPAGE.init();
//
// });