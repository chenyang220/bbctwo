function initGrid()
{
     var gridWH = Public.setGrid(), _self = this;
   var t = ["排序", "板块名称", "启用","操作"], e = [
	   {name: "forum_order", index: "forum_order", align: "center",width: 100},
	   {name: "forum_name", index: "forum_name",  align: "center",width: 200},
       {name: "forum_state", index: "forum_state", align: "center",width: 100},
       {name: "operate", width: 100, fixed: !0, align: "center", formatter: operFmattershop}
   ];
	$("#grid").jqGrid({
        url: SITE_URL + "?ctl=Forum&met=front&typ=json",
        datatype: "json",
        height: Public.setGrid().h,
        colNames: t,
        colModel: e,
        autowidth: !0,
        pager: "#page",
        viewrecords: !0,
        cmTemplate: {sortable: !1, title: !1},
        page: 1,
        rowNum: 100,
        rowList: [100, 200, 500],
        shrinkToFit: !1,
		jsonReader: {root: "data.items", records: "data.records", total: "data.total", repeatitems: !1, id: "page_id"},
        loadComplete: function (t)
        {
            if (t && 200 == t.status)
            {
                var e = {};
                t = t.data;
                for (var i = 0; i < t.items.length; i++)
                {
                    var a = t.items[i];
                    e[a.id] = a;
                }
                $("#grid").data("gridData", e);
                0 == t.items.length && parent.Public.tips({type: 2, content: "没有楼层数据！"})
            }
            else
            {
                parent.Public.tips({type: 2, content: "获取楼层数据失败！" + t.msg})
            }
        },
        loadError: function ()
        {
            parent.Public.tips({type: 1, content: "操作失败了哦，请检查您的网络链接！"})
        }
    })
}
function operFmattershop(val, opt, row) {
    var html_con = '<div class="operating" data-id="' + row.page_id + '"><span class="ui-icon ui-icon-pencil" title="编辑"></span></div>';
    return html_con;
};
initGrid();
