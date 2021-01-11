
    function initPopBtns() {
        var t = [__('确定'), __('取消')];
        api.button({
            id: "confirm", name: t[0], focus: !0, callback: function () {
                postData(oper, rowData);
                return cancleGridEdit(), $("#score-form").trigger("validate"), !1
            }
        }, {id: "cancel", name: t[1]})
    }

    function postData(t, e) {
        $_form.validator({
            rules: {},
            fields: {
                'score': 'required;integer[+];',
                'score_desc':'length[~10]'
            },
            valid: function (form) {
                var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                var way_for = $("input[name='way_for']:checked").val();
                var data = {
                    userIds: e,
                    score: Number($("#score").val()),
                    way_for: way_for? way_for:1,
                    score_desc:$("#score_desc").val()
                };
                Public.ajaxPost(SITE_URL + '?ctl=User_Info&met=setScore&typ=json', data, function (e) {
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

    var curRow, curCol, $grid = $("#grid"), $_form = $("#score-form"), api = frameElement.api, oper = api.data.oper,  rowData = api.data.rowData || {}, callback = api.data.callback;
    initPopBtns();


