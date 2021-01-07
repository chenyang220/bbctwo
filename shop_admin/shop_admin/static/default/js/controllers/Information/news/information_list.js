var queryConditions = {
        cardName: ''
    },
    hiddenAmount = false,
    SYSTEM = system = parent.SYSTEM;
var THISPAGE = {
    init: function (data) {
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        }
        ;
        this.mod_PageConfig = Public.mod_PageConfig.init('complain-new-list');//页面配置初始化
        this.initDom();
        this.addEvent();
    },
    initDom: function () {
        this.$_searchName = $('#searchName');
        this.$_searchName.placeholder();
    },
    
    reloadData: function (data) {
        $("#grid").jqGrid('setGridParam', {postData: data}).trigger("reloadGrid");
    },
    addEvent: function () {
        var _self = this;
        $('#search').click(function () {
            queryConditions.news_title    = $('#matchCon').val() ? $.trim($('#matchCon').val()):"";
            queryConditions.article_group = $source.getValue();
            THISPAGE.reloadData(queryConditions);
        });
    }
}


function initEvent() {
    var _self = this;
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
    
    $("#grid").on("click", ".operating .ui-icon-trash", function (t) {
        t.preventDefault();
        if (Business.verifyRight("INVLOCTION_DELETE")) {
            var e = $(this).parent().data("id");
            handle.del(e)
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
    
    $(window).resize(function () {
        Public.resizeGrid()
    })
}

function initGrid() {
    console.log(Public);
    var t = ["操作", "资讯标题", "资讯副标题", "发布方", "发布方名称", "审核时间","浏览数"], e = [{
        name: "operate",
        width: 100,
        fixed: !0,
        align: "center",
        formatter: operFmattershop
    },
        {name: "title", index: "title", width: 200, align: "center"},
        {name: "subtitle", index: "subtitle", width: 150, align: "center"},
        {name: "author_name", index: "newsclass_name", width: 100, align: "center"},
        {name: "authorname", index: "newsclassname", width: 100, align: "center"},
        {name: "create_time", index: "create_time", width: 150},
        {name: "number", index: "number", width: 150},
    ];
    
    $("#grid").jqGrid({
        url: SITE_URL + "?ctl=Information_News&met=informationNewsList&typ=json&status=1&auditing=1",
        datatype: "json",
        height: Public.setGrid().h,
        colNames: t,
        colModel: e,
        autowidth: !0,
        pager: "#page",
        viewrecords: !0,
        cmTemplate: {sortable: !1, title: !1},
        page: 1,
        rowNum: 10,
        rowList: [10, 20, 50],
        shrinkToFit: !1,
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
                parent.Public.tips({type: 2, content: "获取分类数据失败！" + t.msg})
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
            var i = "新增资讯文章", a = {oper: t, callback: this.callback};
        }
        else {
            var i = "修改资讯文章", a = {oper: t, rowData: $("#grid").data("gridData")[e], callback: this.callback};
        }
        $.dialog({
            title: i,
            content: 'url:' + SITE_URL + "?ctl=Information_News&met=" + t + "News&news_id=" + e,
            width: 1200,
            height: $(window).height(),
            data: a,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    }, callback: function (t, e, i) {
        var a = $("#grid").data("gridData");
        if (!a) {
            a = {};
            $("#grid").data("gridData", a);
           
        }
        a[t.id] = t;
        if ("edit" == e) {
            $("#grid").jqGrid("setRowData", t.id, t);
            i && i.api.close()
        }
        else {
            $("#grid").jqGrid("addRowData", t.id, t, "last");
            $("#grid").trigger("reloadGrid")
            i && i.api.close()
        }
    }, del: function (t) {
        $.dialog.confirm("删除的资讯将不能恢复，请确认是否删除？", function () {
            Public.ajaxPost(SITE_URL + "?ctl=Information_News&met=removeBase&typ=json", {article_id: t}, function (e) {
                console.log(e);
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
initEvent();
initGrid();
$(function () {
    
    $.get("./index.php?ctl=Information_NewsClass&met=newstypelist&typ=json", function (result) {

            var r = result.data;
            console.log(r);
            $source = $("#source").combo({
                data: r,
                value: "id",
                text: "typename",
                width: 110
            }).getCombo();

    });
    
    
    THISPAGE.init();
    
});

function operFmattershop(val, opt, row) {
    if(row.author_type==2||row.author_type ==1){
        var html_con = '<div class="operating" data-id="' + row.id + '"><span class="ui-icon ui-icon-search" title="查看"></span><span class="ui-icon ui-icon-trash" title="删除"></span></div>';
        return html_con;
    }else{
        var html_con = '<div class="operating" data-id="' + row.id + '"><span class="ui-icon ui-icon-search" title="查看"></span><span class="ui-icon ui-icon-pencil" title="修改"></span><span class="ui-icon ui-icon-trash" title="删除"></span></div>';
        return html_con;
    }
    
};



