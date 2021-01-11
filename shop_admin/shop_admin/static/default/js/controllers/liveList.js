$(function () {
    $('#live_start').datetimepicker({
        controlType: 'select',
        format: "Y-m-d",
        timepicker: false
    });

    $('#live_end').datetimepicker({
        controlType: 'select',
        format: "Y-m-d",
        timepicker: false
    });

    var searchFlag = false;
    var handle = {
        //审核
        operate: function (oper, row_id) {
            if (oper == 'verify') {
                var title = sprintf(_('商家直播申请审核 [%s]'), row_id);
                var data = {
                    oper: oper,
                    rowId: row_id,
                    rowData: $("#grid").data('gridData')[row_id],
                    callback: this.callback
                };
                var met = 'verifyApplication&id=' + row_id;
            }
            if (oper == 'edit') {
                var title = '编辑';
                var data = {
                    oper: oper,
                    rowId: row_id,
                    rowData: $("#grid").data('gridData')[row_id],
                    callback: this.callback
                };
                var met = 'editApplication&id=' + row_id;
            }
            $.dialog({
                title: title,
                content: 'url:' + SITE_URL + '?ctl=Live&met=' + met + '&typ=e',
                data: data,
                width: 500,
                height: 300,
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            });
        },
        callback: function (data, oper, dialogWin) {
            var gridData = $("#grid").data('gridData');
            if (!gridData) {
                gridData = {};
                $("#grid").data('gridData', gridData);
            }

            gridData[data.id] = data;
            $("#grid").jqGrid('setRowData', data.id, data);
            dialogWin && dialogWin.api.close();

            // window.location.reload();
            $("#grid").trigger("reloadGrid");
        },
        del: function (t) {
            $.dialog.confirm("请确认是否删除？", function () {
                Public.ajaxPost(SITE_URL + "?ctl=Live&met=manageApplication&typ=json&action=del", {id: t}, function (e) {
                    if (e && 200 == e.status) {
                        parent.Public.tips({content: "删除成功！"});
                        $("#grid").jqGrid("delRowData", t)
                    }
                    else {
                        parent.Public.tips({type: 1, content: "删除失败！" + e.msg})
                    }
                })
            })
        },
        //操作项格式化，适用于有“修改、删除”操作的表格
        operFmatter: function (val, opt, row) {
            var status = parseInt(row.application_status);
            switch (status) {
                case 2:
                    var html_con = '<div class="operating" data-id="' + row.live_application_id + '"><span class="ui-icon ui-icon-pencil" title="编辑"></span></div>';
                    break;
                case 3:
                    var html_con = '<div class="operating" data-id="' + row.live_application_id + '"><span class="ui-icon ui-icon-trash" title="删除"></span></div>';
                    break;
                case 4:
                    var html_con = '<div class="operating" data-id="' + row.live_application_id + '"><span class="ui-icon ui-icon-pencil" title="编辑"></span></div>';
                    break;
                default:
                    var html_con = '<div class="operating" data-id="' + row.live_application_id + '"><span class="ui-icon ui-icon-gear" title="审核"></span></div>';
                    break;
            }
            return html_con;
        },

        verifyFmatter: function (val, opt, row) {
            var text;
            if (val == 1) {
                text = __('待审核');
            } else if (val == 2) {
                text = __('通过');
            } else if (val == 3) {
                text = __('未通过');
            }else{
                text = __('关闭');
            }
            return '<span data-enable="' + val + '" data-id="' + row.live_application_id + '">' + text + '</span>';
            return val;
        },

    };

    function initGrid() {
        var grid_row = Public.setGrid();
        var colModel = [{
            "name": "operate",
            "label": "操作",
            "width": 80,
            "sortable": false,
            "search": false,
            "resizable": false,
            "fixed": true,
            "align": "center",
            "title": false,
            "formatter": handle.operFmatter
        }, {
            "name": "user_name",
            "index": "user_name",
            "label": "店主账号",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 200
        }, {
            "name": "shop_name",
            "index": "shop_name",
            "label": "店铺名称",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 200
        }, {
            "name": "shop_company_address",
            "index": "shop_company_address",
            "label": "所在区域",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 200
        }, {
            "name": "shop_tel",
            "index": "shop_tel",
            "label": "商家电话",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 200
        }, {
            "name": "live_length",
            "index": "live_length",
            "label": "申请直播期限",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "fixed": true,
            "width": 200,
        }, {
            "name": "application_time",
            "index": "application_time",
            "label": "申请时间",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "fixed": true,
            "width": 200,
        }, {
            "name": "application_status",
            "index": "application_status",
            "label": "申请状态",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "fixed": true,
            "width": 200,
            "formatter": handle.verifyFmatter
        }
        ];
        //mod_PageConfig.gridReg('grid', colModel);
        //colModel = mod_PageConfig.conf.grids['grid'].colModel;
        $('#grid').jqGrid({
            url: SITE_URL + '?ctl=Live&met=getLiveList&typ=json',
            datatype: 'json',
            autowidth: true,
            shrinkToFit: false,
            forceFit: true,
            width: grid_row.w,
            height: grid_row.h,
            altRows: true,
            gridview: true,
            /*onselectrow: false,
            multiselect: false, //多选*/
            colModel: colModel,
            pager: '#page',
            viewrecords: true,
            cmTemplate: {
                sortable: true
            },
            rowNum: 10,
            rowList: [20, 50, 100, 200, 500],
            //scroll: 1,
            jsonReader: {
                root: "data.items",
                records: "data.records",
                total: "data.total",
                repeatitems: false,
                id: "live_application_id"
            },
            loadComplete: function (response) {
                if (response && response.status == 200) {
                    var gridData = {};
                    data = response.data;
                    for (var i = 0; i < data.items.length; i++) {
                        var item = data.items[i];
                        item['id'] = item.live_application_id;
                        gridData[item.live_application_id] = item;
                    }

                    $("#grid").data('gridData', gridData);
                } else {
                    var msg = response.status === 250 ? (searchFlag ? '没有满足条件的结果哦！' : '没有数据哦！') : response.msg;
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
            resizeStop: function (newwidth, index) {
                //mod_PageConfig.setGridWidthByIndex(newwidth, index, 'grid');
            }
        }).navGrid('#page', {
            edit: false,
            add: false,
            del: false,
            search: false,
            refresh: false
        });
    }

    function initEvent() {
        //查询
        $('#search').on('click', function (e) {
            e.preventDefault();
            $("#grid").jqGrid('setGridParam', {
                page: 1,
                postData: {
                    shop_state: $("#shop_state").val(),
                    shop_info:$("#shop_info").val(),
                    live_status:$("#live_status").val(),
                    live_start:$("#live_start").val(),
                    live_end:$("#live_end").val(),
                }
            }).trigger("reloadGrid");

        });
        //审核
        $('#grid').on('click', '.operating .ui-icon-gear', function (e) {
            e.preventDefault();
            var id = $(this).parent().data('id');
            handle.operate('verify', id);
        });
        //编辑
        $('#grid').on('click', '.operating .ui-icon-pencil', function (e) {
            e.preventDefault();
            var id = $(this).parent().data('id');
            handle.operate('edit', id);
        });

        //删除
        $('#grid').on('click', '.operating .ui-icon-trash', function (e) {
            e.preventDefault();
            var id = $(this).parent().data('id');
            handle.del(id);
        });

    }

    initGrid();
    initEvent();
});
