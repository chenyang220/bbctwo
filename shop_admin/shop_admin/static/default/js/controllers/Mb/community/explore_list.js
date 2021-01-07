$(function() {


    $('.wrapper').on('click', '#reaudit', function (a) {
        a.preventDefault();
        var b = $('#grid').jqGrid('getGridParam', 'selarrrow'),
            c = b.join();
        return c ? void Public.ajaxPost('./index.php?ctl=Goods_Goods&met=checkGoods&typ=json', {
            id: c
        }, function (a) {
            200 === a.status ? parent.Public.tips({
                content: '审核成功！'
            })  : parent.Public.tips({
                type: 1,
                content: a.msg
            }),
                $('#search').trigger('click')
        })  : void parent.Public.tips({
            type: 2,
            content: '请先选择需要审核的项！'
        })
    });

    var searchFlag = false;
    var filterClassCombo, stateCombo, verifyCombo;
    var handle = {
        //审核
        operate: function(oper, row_id) {
            if (oper == 'verify'){
                var title = sprintf(_('审核内容 [%s]'), row_id);
                var data = {
                    oper: oper,
                    rowId: row_id,
                    rowData: $("#grid").data('gridData')[row_id],
                    callback: this.callback
                };
                var met = 'verifyManage&explore_id=' + row_id;
                console.log(data);
                console.log($("#grid").data('gridData')[row_id]);
            }
            $.dialog({
                title: title,
                content: 'url:' + SITE_URL + '?ctl=Mb_Community&met=' + met + '&typ=e',
                data: data,
                width:800,
                height:500,
                max: false,
                min: false,
                cache: false,
                lock: true
            });
        },

        callback: function(data, oper, dialogWin) {
            var gridData = $("#grid").data('gridData');
            if (!gridData) {
                gridData = {};
                $("#grid").data('gridData', gridData);
            }

            gridData[data.id] = data;
            if (oper == "edit" || oper == "close" ||  oper == "verify") {
                $("#grid").jqGrid('setRowData', data.id, data);
                dialogWin && dialogWin.api.close();
                window.location.reload();
            } else {
                $("#grid").jqGrid('addRowData', data.id, data, 'first');
                dialogWin && dialogWin.resetForm(data);
            }
        },

        //操作项格式化，适用于有“修改、删除”操作的表格
        operFmatter: function(val, opt, row) {

            var verify_str = '<span class="ui-icon ui-icon-search" title="审核"></span>';
            var html_con = '<div class="operating" data-id="' + row.id + '">' + verify_str + '</div>' ;
            return html_con;
        },
        verifyFmatter: function (val, opt, row)
        {
            var text, cls;

            if (val == 3) {
                text = __('待审核');
                cls = 'ui-label-default';
            } else if (val == 0) {
                text = __('审核通过');
                cls = 'ui-label-success';
            } else if (val == 4) {
                text = __('审核失败');
                cls = 'ui-label-default';
            }

            return '<span class="set-status ui-label ' + cls + '" data-enable="' + val + '" data-id="' + row.id + '">' + text + '</span>';

            return val;
        },
        stockFmatter: function (val, opt, row)
        {
            var text, cls;

            if (row.isAlarm)
            {
                text = __('库存不足');
                cls = 'ui-label-important';
            }
            else
            {
                text = __('库存充足');
                cls = 'ui-label-success';
            }

            return '<span class="ui-label ' + cls + '">' + text + '</span>';
        },
    };

    // function test(){
    //     return $("#userid").val();
    // }
  
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
            "name": "user_account",
            "index": "user_account",
            "label": "用户名",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width":200
        }, {
            "name": "user_mobile",
            "index": "user_mobile",
            "label": "联系方式",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 100
        },{
            "name": "explore_title",
            "index": "explore_title",
            "label": "标题",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 200
        }, {
            "name": "explore_content",
            "index": "explore_content",
            "label": "内容",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "width": 300
        }, {
            "name": "explore_status",
            "index": "explore_status",
            "label": "商品审核",
            "classes": "ui-ellipsis",
            "align": "center",
            "title": false,
            "fixed": true,
            "width": 100,
            "formatter": handle.verifyFmatter
        }
        ];


        //mod_PageConfig.gridReg('grid', colModel);
        //colModel = mod_PageConfig.conf.grids['grid'].colModel;
        $('#grid').jqGrid({
            url: SITE_URL + '?ctl=Mb_Community&met=getExploreAllList&typ=json',
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
            rowNum: 20,
            rowList: [20,50,100, 200, 500],
            //scroll: 1,
            jsonReader: {
                root: "data.items",
                records: "data.records",
                total: "data.total",
                repeatitems: false,
                id: "explore_id"
            },
            loadComplete: function(response) {
                if (response && response.status == 200) {
                    var gridData = {};
                    data = response.data;
                    for (var i = 0; i < data.items.length; i++) {
                        var item = data.items[i];
                        item['id'] = item.explore_id;
                        gridData[item.explore_id] = item;
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
            loadError: function(xhr, status, error) {
                parent.Public.tips({
                    type: 1,
                    content: '操作失败了哦，请检查您的网络链接！'
                });
            },
            resizeStop: function(newwidth, index) {
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
        var match_con = $('#matchCon');
        //查询
        $('#search').on('click', function(e) {
            e.preventDefault();
            var verify_id = $("#explore_verify").val();
            $("#grid").jqGrid('setGridParam', {
                page: 1,
                postData: {
                    user_name: $('#user_name').val(),
                    explore_status: verify_id,
                }
            }).trigger("reloadGrid");

        });
        //审核
        $('#grid').on('click', '.operating .ui-icon-search', function(e) {
            console.log(e);
            e.preventDefault();
            //if (!Business.verifyRight('BU_UPDATE'))
            //{
            //    return;
            //}
            var id = $(this).parent().data('id');
            handle.operate('verify', id);
        });

    }
    // 初始化查询条件
    function initFilter()
    {
        //查询条件
        Business.filterBrand();

        //商品类别
        var opts = {
            width : 200,
            //inputWidth : (SYSTEM.enableStorage ? 145 : 208),
            inputWidth : 145,
            defaultSelectValue : '-1',
            //defaultSelectValue : rowData.categoryId || '',
            showRoot : true
        }

        categoryTree = Public.categoryTree($('#goods_cat'), opts);

    }

    //var mod_PageConfig = Public.mod_PageConfig.init('customerList');//页面配置初始化
    initGrid();
    initEvent();
    initFilter();
});
