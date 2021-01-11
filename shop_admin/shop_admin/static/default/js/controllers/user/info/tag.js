var tagIds = [];
var queryConditions = {
    user_tag_name: '',
};

var THISPAGE = {
    initDom: function () {
        var defaultPage = Public.getDefaultPage();
        defaultPage.SYSTEM = defaultPage.SYSTEM || {};
        this.$_user_tag_name = $('#user_tag_name');
        this.$_user_tag_name.placeholder();
    },
    loadGrid: function () {
        var gridWH = Public.setGrid(), _self = this;
        var colModel = [
            {name: 'user_tag_name', label: '标签名称', "classes": "ui-ellipsis", width: 300, align: "center"},
        ];
        $("#grid").jqGrid({
            url: SITE_URL + '?ctl=User_Info&met=getTagList&typ=json',
            postData: queryConditions,
            datatype: "json",
            autowidth: true,//如果为ture时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：setGridWidth
            height: gridWH.h,
            altRows: true, //设置隔行显示
            gridview: true,
            multiselect: true,
            multiboxonly: false,
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
                id: "user_tag_id"
            },
            loadComplete: function (t) {
                if (t && 200 == t.status) {
                    var e = {};
                    t = t.data;
                    for (var i = 0; i < t.items.length; i++) {
                        var a = t.items[i];
                        e[a.user_tag_id] = a;
                    }
                    $("#grid").data("gridData", e);

                    0 == t.items.length && parent.Public.tips({type: 2, content: "没有类型数据！"})
                }
                else {
                    parent.Public.tips({type: 2, content: "获取类型数据失败！" + t.msg})
                }
            },
            onSelectRow: function (rowid, status) {
                var index = $.inArray(rowid, tagIds);
                if (status) {
                    if (index <= 0) {
                        tagIds.push(rowid);
                    }
                } else {
                    if (index >= 0) {
                        tagIds.splice(index, 1);
                    }
                }
            },
            onSelectAll: function (rowids, status) {
                if (status) {
                    tagIds = rowids;
                } else {
                    tagIds = [];
                }
            },
            loadError: function (xhr, st, err) {
                parent.Public.tips({
                    type: 1,
                    content: '操作失败了哦，请检查您的网络链接！'
                });
            },
            resizeStop: function (newwidth, index) {
                THISPAGE.mod_PageConfig.setGridWidthByIndex(newwidth, index, 'grid');
            }
        }).navGrid('#page', {
            edit: false,
            add: false,
            del: false,
            search: false,
            refresh: false
        }).navButtonAdd('#page', {
            caption: "",
            buttonicon: "ui-icon-config",
            onClickButton: function () {
                THISPAGE.mod_PageConfig.config();
            },
            position: "last"
        });
    },

    reloadData: function (data) {
        $("#grid").jqGrid('setGridParam', {postData: data}).trigger("reloadGrid");
    },
    addEvent: function () {
        var _self = this;
        //搜索
        $('#search').click(function () {
            queryConditions.user_tag_name = _self.$_user_tag_name.val();
            THISPAGE.reloadData(queryConditions);
        });

        $(window).resize(function () {
            Public.resizeGrid();
        });
    }
};

THISPAGE.initDom();
THISPAGE.loadGrid();
THISPAGE.addEvent();


function initPopBtns() {
    var t = [__('确定'), __('取消')];
    api.button({
        id: "confirm", name: t[0], focus: !0, callback: function () {
            postData(oper, rowData);
            return cancleGridEdit(), $("#tag-form").trigger("validate"), !1
        }
    }, {id: "cancel", name: t[1]})
}

function postData(t, e) {
    $_form.validator({
        rules: {},
        fields: {},
        valid: function (form) {
            var me = this;
            // 提交表单之前，hold住表单，防止重复提交
            me.holdSubmit();
            var data = {
                userIds: e,
                tagIds: tagIds,
            }
            Public.ajaxPost(SITE_URL + '?ctl=User_Info&met=setTag&typ=json', data, function (e) {
                if (200 == e.status) {
                    parent.parent.Public.tips({content: "操作成功！"});
                    callback && "function" == typeof callback && callback(e.data, t, window)
                } else {
                    parent.parent.Public.tips({type: 1, content: "失败！" + e.msg})
                }
                // 提交表单成功后，释放hold，如果不释放hold，就变成了只能提交一次的表单
                me.holdSubmit(false);
            })
        },
        ignore: ":hidden",
        theme: "yellow_bottom",
        timely: 1,
        stopOnError: !0
    });
}

function cancleGridEdit() {
    null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
}

var curRow, curCol, $grid = $("#grid"), $_form = $("#tag-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
initPopBtns();


