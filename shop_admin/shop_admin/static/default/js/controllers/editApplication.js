function initPopBtns() {
    var t = "add" == oper ? ["保存", "关闭"] : ["确定", "取消"];
    api.button({
        id: "confirm", name: t[0], focus: !0, callback: function () {
            postData(oper, rowData.id);
            return cancleGridEdit(), $("#manage-form").trigger("validate"), !1
        }
    }, {id: "cancel", name: t[1]})
}

function postData(t, e) {
    $_form.validator({
        valid: function (form) {
            var me = this;
            me.holdSubmit();
            Public.ajaxPost(SITE_URL + '?ctl=Live&met=manageApplication&typ=json&action=edit&id=' + e, $_form.serialize(), function (e) {
                if (200 == e.status) {
                    parent.parent.Public.tips({content: "编辑成功"});
                    console.log(window.api)
                    callback && "function" == typeof callback && callback(e.data, t, window)
                }
                else {
                    parent.parent.Public.tips({type: 1, content: "编辑失败"})
                }
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

var curRow, curCol, curArrears, $grid = $("#grid"), $_form = $("#manage-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
initPopBtns();