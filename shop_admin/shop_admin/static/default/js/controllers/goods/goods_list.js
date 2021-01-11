$(function ()
{
    var searchFlag = false;
    var filterClassCombo, userCombo;
    SYSTEM = system = parent.SYSTEM;
    function initGrid()
    {
        var grid_row = Public.setDialogGrid();
        console.info(grid_row);
        var colModel = [ {
            "name": "goods_id",
            "index": "goods_id",
            "label": "商品SKU",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "fixed": true,
            "width": 120
        },  {
            "name": "goods_price",
            "index": "goods_price",
            "label": "商品价格",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 120
        },{
            "name": "goods_stock",
            "index": "goods_stock",
            "label": "商品库存",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "fixed": true,
            "width": 120
        },{
            "name": "goods_code",
            "index": "goods_code",
            "label": "商家编号货号",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 120
        },{
            "name": "goods_spec_list",
            "index": "goods_spec_list",
            "label": "商品规格",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 200
        }];
        //mod_PageConfig.gridReg('grid', colModel);
        //colModel = mod_PageConfig.conf.grids['grid'].colModel;
        $('#grid').jqGrid({
                data: goods_data.items,
                datatype: 'local',
                autowidth: false,
                shrinkToFit: false,
                forceFit: true,
                width:800,
                height: grid_row.h,
                altRows: true,
                gridview: true,
                onselectrow: false,
                multiselect: false, //多选
                colModel: colModel,
                viewrecords: true,
                cmTemplate: {
                    sortable: false
                },
                rowNum: 100,
                rowList: [100, 200, 500],
                localReader: {root: "data.items", records: "data.records", total: "data.total", repeatitems: !1, id: "goods_id"},
                //scroll: 1,
                loadComplete: function (res)
                {
                    console.info(res);
                    var re_records = $("#grid").getGridParam('records');
                    if (re_records==0 || re_records==null)
                    {
                        $("#grid").parent().append("<div class=\"norecords\">没有符合数据</div>");

                        $(".norecords").show();
                    }
                    else
                    {
                        //如果存在记录，则隐藏提示信息。
                        $(".norecords").hide();
                    }
                },
                resizeStop: function (newwidth, index)
                {
                    //mod_PageConfig.setGridWidthByIndex(newwidth, index, 'grid');
                }
            }
        ).navGrid('#page', {
            edit: false,
            add: false,
            del: false,
            search: false,
            refresh: false
        });
    }
    initGrid();
})
;