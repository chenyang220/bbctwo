
    var redpacketIds = [];
    var queryConditions = {
            redpacket_t_title: '',
            redpacket_t_state:1 //有效红包
        };

    var THISPAGE = {
        initDom: function () {
            var defaultPage = Public.getDefaultPage();
            defaultPage.SYSTEM = defaultPage.SYSTEM || {};
            this.$_redpacket_t_title = $('#redpacket_t_title');
            this.$_redpacket_t_title.placeholder();
        },
        loadGrid: function () {
            var gridWH = Public.setGrid(), _self = this;
            var colModel = [
                {name: 'redpacket_t_title', label: '红包名称', "classes": "ui-ellipsis", width: 200, align: "center"},
                {name: 'redpacket_t_type', label: '红包类型', "classes": "ui-ellipsis", width: 100, align: "center", formatter: typeInfo},
                {name: 'redpacket_t_price', label: '面额（元）', "classes": "ui-ellipsis", width: 100, align: 'center'},
                {name: 'redpacket_t_orderlimit', label: '消费限额（元）', "classes": "ui-ellipsis", width: 100, align: "center"},
                {name: 'redpacket_t_date', label: '有效期', "classes": "ui-ellipsis", width: 200, align: "center", formatter: dateInfo},
                {name: 'redpacket_t_stock', label: '库存数量', "classes": "ui-ellipsis", width: 100, align: "center", formatter: stockInfo},
                {name: 'redpacket_t_user_grade_label', label: '会员级别', "classes": "ui-ellipsis", width: 100, align: "center"},
            ];
            $("#grid").jqGrid({
                url: SITE_URL + '?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json',
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
                    id: "redpacket_t_id"
                },
                loadComplete: function (t) {
                    if (t && 200 == t.status) {
                        var e = {};
                        t = t.data;
                        for (var i = 0; i < t.items.length; i++) {
                            var a = t.items[i];
                            e[a.redpacket_t_id] = a;
                        }
                        $("#grid").data("gridData", e);

                        0 == t.items.length && parent.Public.tips({type: 2, content: "没有类型数据！"})
                    }
                    else {
                        parent.Public.tips({type: 2, content: "获取类型数据失败！" + t.msg})
                    }
                },
                onSelectRow: function (rowid, status) {
                    var index = $.inArray(rowid, redpacketIds);
                    if (status) {
                        if (index <= 0) {
                            redpacketIds.push(rowid);
                        }
                    } else {
                        if (index >= 0) {
                            redpacketIds.splice(index, 1);
                        }
                    }
                },
                onSelectAll: function (rowids, status) {
                    if (status) {
                        redpacketIds = rowids;
                    } else {
                        redpacketIds = [];
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

            //红包类型 1-新人注册红包，2-普通红包
            function typeInfo(val, opt, row) {
                var html_con = '';
                if (row.redpacket_t_type ==1) {
                    html_con = "新人注册红包";
                } else {
                    html_con = "普通红包";
                }
                return html_con;
            };

            //有效期
            function dateInfo(val, opt, row) {
                var html_con = "<span  title='" + row.redpacket_t_start_date + "至" + row.redpacket_t_end_date + "'>" + row.redpacket_t_start_date + "至" + row.redpacket_t_end_date + "</span>";
                return html_con;
            };

            //库存
            function stockInfo(val, opt, row) {
                if (row.redpacket_t_total > 0){
                    var html_con = Number(row.redpacket_t_total) - Number(row.redpacket_t_giveout);
                } else{
                    var html_con = '不限额';
                }
                return html_con;
            };
        },

        reloadData: function (data) {
            $("#grid").jqGrid('setGridParam', {postData: data}).trigger("reloadGrid");
        },
        addEvent: function () {
            var _self = this;
            //搜索
            $('#search').click(function () {
                queryConditions.redpacket_t_title = _self.$_redpacket_t_title.val();
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
                return cancleGridEdit(), $("#redpacket-form").trigger("validate"), !1
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
                    redpacketIds: redpacketIds,
                }
                Public.ajaxPost(SITE_URL + '?ctl=User_Info&met=giveRedpacket&typ=json', data, function (e) {
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

    var curRow, curCol, $grid = $("#grid"), $_form = $("#redpacket-form"), api = frameElement.api, oper = api.data.oper,  rowData = api.data.rowData || {}, callback = api.data.callback;
    initPopBtns();


