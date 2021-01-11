function initPopBtns() {
    var t = "add" == oper ? ["保存", "关闭"] : ["确定", "取消"];
    api.button({
        id: "confirm", name: t[0], focus: !0, callback: function () {
            postData(oper, rowData.user_id);
            return cancleGridEdit(), $("#account-form").trigger("validate"), !1
        }
    }, {id: "cancel", name: t[1]})
}

function postData(t, e) {
    $_form.validator({
        fields: {
            'user_administrator': 'required;',
            'user_name': 'required',
            // 'password': 'required;'
        },
        valid: function (form) {
            var me = this;
            me.holdSubmit();
            var n = "add" == oper ? '新增' : '编辑';
            Public.ajaxPost(SITE_URL + '?ctl=User_Account&met=addOrEditAccountInfo&typ=json', $_form.serialize(), function (e) {
                if (200 == e.status) {
                    parent.parent.Public.tips({content: n + "成功！"});
                    callback && "function" == typeof callback && callback(e.data, t, window)
                }else {
                    parent.parent.Public.tips({type: 1, content: n + "失败" + e.msg})
                }
                me.holdSubmit(false);
            })
        },
        ignore: "",
        theme: "yellow_bottom",
        timely: 1,
        stopOnError: !0
    });
}

function cancleGridEdit() {
    null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
}

var curRow, curCol, curArrears, $grid = $("#grid"), $_form = $("#account-form"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
initPopBtns();

