var queryConditions = {},
    hiddenAmount = false,
    SYSTEM = system = parent.SYSTEM;
var THISPAGE = {
    init: function (data) {
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        }
        ;
        this.mod_PageConfig = Public.mod_PageConfig.init('user_list');//页面配置初始化
        this.loadGrid();
        this.addEvent();
    },
    loadGrid: function () {
        var gridWH = Public.setGrid(), _self = this;
        var colModel = [
            {name: 'operating', label: '操作', width: 60, fixed: true, formatter: operFmatter, align: "center"},
            {name: 'user_administrator', label: '管理员名称', width: 100, align: "center"},
            {name: 'user_name', label: '账号', width: 200, align: 'center'},
            {name: 'user_for', label: '账号类别', width: 100, align: 'center'},
            {name: 'enable', label: '启用授权', width: 100, align: "center", "title": false, "formatter": handle.statusFmatter},
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url: SITE_URL + '?ctl=User_Account&met=getUserList&typ=json',
            postData: queryConditions,
            datatype: "json",
            autowidth: true,//如果为ture时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：setGridWidth
            height: gridWH.h,
            altRows: true, //设置隔行显示
            gridview: true,
            multiselect: false,
            multiboxonly: true,
            colModel: colModel,
            cmTemplate: {sortable: false, title: false},
            page: 1,
            sortname: 'number',
            sortorder: "desc",
            pager: "#page",
            rowNum: 10,
            rowList: [10, 20, 50],
            viewrecords: true,
            shrinkToFit: false,
            forceFit: true,
            jsonReader: {
                root: "data.items",
                records: "data.records",
                repeatitems: false,
                total: "data.total",
                id: "user_id"
            },
            loadError: function (xhr, st, err) {

            },
            ondblClickRow: function (rowid, iRow, iCol, e) {
                $('#' + rowid).find('.ui-icon-pencil').trigger('click');
            },
            resizeStop: function (newwidth, index) {
                THISPAGE.mod_PageConfig.setGridWidthByIndex(newwidth, index, 'grid');
            }
        }).navGrid('#page', {edit: false, add: false, del: false, search: false, refresh: false}).navButtonAdd('#page', {
            caption: "",
            buttonicon: "ui-icon-config",
            onClickButton: function () {
                THISPAGE.mod_PageConfig.config();
            },
            position: "last"
        });


        function operFmatter(val, opt, row) {
            if (row.enable == 2) {
                var html_con = '<div class="operating" data-id="' + row.id + '"><span class="ui-icon ui-icon-pencil" title="编辑"></span><span class="ui-icon ui-icon-trash" title="删除"></span></div></div>';
            }else{
                var html_con = '<div class="operating" data-id="' + row.id + '"><span class="ui-icon ui-icon-pencil" title="编辑"></span></div></div>';
            }
            return html_con;
        };


    },
    reloadData: function (data) {
        $("#grid").jqGrid('setGridParam', {postData: data}).trigger("reloadGrid");
    },
    addEvent: function () {
        var _self = this;
        //添加
        $("#btn-add").click(function (t) {
            t.preventDefault();
            Business.verifyRight("INVLOCTION_ADD") && handle.operate("add",'')
        });
        //查询
        $('#search').click(function () {
            queryConditions.user_for = $source.getValue();
            THISPAGE.reloadData(queryConditions);
        });
        //编辑
        $('.grid-wrap').on('click', '.ui-icon-pencil', function (e) {
            e.preventDefault();
            var e = $(this).parent().data("id");
            handle.operate("edit", e)
        });
        //删除
        $("#grid").on("click", ".operating .ui-icon-trash", function (e) {
            e.preventDefault();
            var id = $(this).parent().data("id");
            handle.del(id)
        });
        //设置状态
        $('#grid').on('click', '.set-status', function (e) {
            var id = $(this).data('id');
            if ($(this).data('enable') == 1){
                var is_enable = 2;
            } else{
                var is_enable = 1
            }
            handle.setStatus(id, is_enable);
        });
        $("#btn-refresh").click(function () {
            THISPAGE.reloadData('');
        });

        $(window).resize(function () {
            Public.resizeGrid();
        });
    }
};
var handle = {
    operate: function (t, e) {
        var url = "url:" + SITE_URL + '?ctl=User_Account&met=editAccount';
        if (t == 'add'){
            var i = '新增';
        } else{
            var i = '编辑';
            url = url + "&user_id=" + e;
        }
        var  a = {oper: t, rowData: $("#grid").jqGrid('getRowData', e), callback: this.callback};
        $.dialog({
            title: i,
            content: url,
            data: a,
            width: 600,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })

    }, callback: function (t, e, i) {
        var a = $("#grid").data("gridData");
        if (!a) {
            a = {};
            $("#grid").data("gridData", a)
        }
        a[t.id] = t;
        i && i.api.close();
        $("#grid").trigger("reloadGrid");

    }, statusFmatter: function (val, opt, row, oper) {
        var text = val == 1 ? __('已启用') : __('已禁用');
        var cls = val == 1 ? 'ui-label-success' : 'ui-label-default';
        return '<span class="set-status ui-label ' + cls + '" data-enable="' + val + '" data-id="' + row.id + '">' + text + '</span>';
    },//修改状态
    setStatus: function (id, is_enable) {
        if (!id) {
            return;
        }
        Public.ajaxPost(SITE_URL + '?ctl=User_Account&met=addOrEditAccountInfo&typ=json', {
            user_id: id,
            enable: Number(is_enable)
        }, function (data) {
            if (data && data.status == 200) {
                parent.Public.tips({content: __('状态修改成功！')});
                // $('#grid').jqGrid('setCell', id, 'enable', is_enable);
                // if (is_enable == 1){
                //     var html_con = '<div class="operating" data-id="' + id + '"><span class="ui-icon ui-icon-pencil" title="编辑"></span></div></div>';
                // } else{
                //     var html_con = '<div class="operating" data-id="' + id + '"><span class="ui-icon ui-icon-pencil" title="编辑"></span><span class="ui-icon ui-icon-trash" title="删除"></span></div></div>';
                // }
                // console.log(html_con);
                // $('#grid').jqGrid('setCell', id, 'operating', html_con);
                $("#grid").trigger("reloadGrid");
            }
            else {
                parent.Public.tips({type: 1, content: '状态修改失败！' + data.msg});
            }
        });
    }, del: function (t) {
        $.dialog.confirm("删除的账号将不能恢复，请确认是否删除？", function () {
            Public.ajaxPost(SITE_URL + "?ctl=User_Account&met=delUserAccount&typ=json", {id: t}, function (e) {
                if (e && 200 == e.status) {
                    parent.Public.tips({content: "账号删除成功！"});
                    $("#grid").jqGrid("delRowData", t)
                }
                else {
                    parent.Public.tips({type: 1, content: "账号删除失败！" + e.msg})
                }
            })
        })
    }
};
$(function () {
    $source = $("#source").combo({
        data: [{
            id: "0",
            name: "请选择"
        }, {
            id: "PayCenter",
            name: "PayCenter"
        }, {
            id: "UCenter",
            name: "UCenter"
        }],
        value: "id",
        text: "name",
        width: 110
    }).getCombo();

    Public.pageTab();

    THISPAGE.init();

});
