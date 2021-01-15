function initEvent()
{
	$_matchCon = $("#matchCon"), 
	$_matchCon.placeholder(), 

	
	$("#btn-add").click(function (t)
    {
        t.preventDefault();
        Business.verifyRight("INVLOCTION_ADD") && handle.operate("add")
    });
    
    $("#btn-refresh").click(function (t)
    {
        t.preventDefault();
        $("#grid").trigger("reloadGrid")
    });
	
    $("#grid").on("click", ".operating .ui-icon-pencil", function (t)
    {
        t.preventDefault();
        if (Business.verifyRight("INVLOCTION_UPDATE"))
        {
            var e = $(this).parent().data("id");
        
            handle.operate("edit", e)
        }
    });
	
    $("#grid").on("click", ".operating .ui-icon-trash", function (t)
    {
        t.preventDefault();
        if (Business.verifyRight("INVLOCTION_DELETE"))
        {
            var e = $(this).parent().data("id");
            handle.del(e)
        }
    });
	
    $(window).resize(function ()
    {
        Public.resizeGrid()
    })
}
function initGrid()
{
     var gridWH = Public.setGrid(), _self = this;
     var t = ["id","操作","标签名称","标签排序", "添加时间","标签logo"], e = [
        {name: "id", index: "id",  align: "center",width: 200,hidden:true},
        {name: "operate",width: 70,fixed: !0,align: "center",formatter: operFmattershop}, 
        {name: "label_name", index: "label_name",  align: "center",width: 200},
        {name: "label_tag_sort", index: "label_tag_sort", align: "center",width: 100},
        {name: "create_time", index: "create_time", align: "center",width: 200},
        {name: "label_logo", index: "label_logo", align: "center",width: 100,formatter: img_logo}
        ];
	$("#grid").jqGrid({
        url: SITE_URL + "?ctl=Shop_Label&met=getLabelList&typ=json",
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
		jsonReader: {root: "data.items", records: "data.records", total: "data.total", repeatitems: !1, id: "id"},
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
                0 == t.items.length && parent.Public.tips({type: 2, content: "没有店铺分类数据！"})
            }
            else
            {
                parent.Public.tips({type: 2, content: "获取店铺分类数据失败！" + t.msg})
            }
        },
        loadError: function ()
        {
            parent.Public.tips({type: 1, content: "操作失败了哦，请检查您的网络链接！"})
        }
    })
}

var handle = {
    operate: function (t, e)
    {         
        if ("add" == t)
        {
            var i = "新增店铺分类", a = {oper: t, callback: this.callback};
        }
        else
        {
            var i = "修改标签名称", a = {oper: t, rowData: $("#grid").data("gridData")[e], callback: this.callback}; 
        }
    
        $.dialog({
            title: i,
            content: "url:"+ SITE_URL + "?ctl=Shop_Label&met="+t+"ShopLabel&label_id="+e,
            data: a,
            width: 700,
            height: 800,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
      
    
  
    }, callback: function (t, e, i)
    {
       
            $("#grid").trigger("reloadGrid");
            i && i.api.close()
    }, del: function (t)
    {
        $.dialog.confirm("删除的标签将不能恢复，请确认是否删除？", function ()
        {
            Public.ajaxPost(SITE_URL + "?ctl=Shop_Label&met=delShopLabel&typ=json", {id: t}, function (e)
            {
                if (e && 200 == e.status)
                {
                    parent.Public.tips({content: "标签删除成功！"});
                    $("#grid").jqGrid("delRowData", t)
                }
                else
                {
                    parent.Public.tips({type: 1, content: "标签删除失败！" + e.msg})
                }
            })
        })
    }
};

function operFmattershop(val, opt, row) {
    var html_con = '<div class="operating" data-id="' + row.id + '"><span class="ui-icon ui-icon-pencil" title="修改"></span><span class="ui-icon ui-icon-trash" title="删除"></span></div>';
    return html_con;
};

function img_logo (val, opt, row) {
    var html_con = '';
    if (row.label_logo) {
         html_con = '<img style="width:60px;height:60px" src="' + row.label_logo + '">';
    } 
    return html_con;
}

initEvent();
initGrid();
