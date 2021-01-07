/**
 * Created by Administrator on 2016/5/15.
 */
$(function(){
	$source = $("#source").combo({
			data: [{
				id: "0",
				name: "活动状态"
			},{
				id: "1",
				name: "进行中"
			},{
				id: "2",
				name: "活动结束"
			}, {
                id: "3",
                name: "管理员关闭"
            }, {
                id: "4",
                name: "平台终止"
            }
			],
			value: "id",
			text: "name",
			width: 110
		}).getCombo();
		
    var queryConditions = {
            goods_name:'',
            shop_name:''
        },
        hiddenAmount = false,
        SYSTEM = system = parent.SYSTEM;

    var handle = {
        operate: function (t, e)
        {
            var i = '查看活动';
            a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};

            $.dialog({
                title: i,
                content: "url:"+SITE_URL + '?ctl=Promotion_Bargain&met=getBargainInfo&bargain_id=' + e,
                data: a,
                width:715,
                height:$(window).height(),
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            })
        },

        //修改状态
        setStatus: function(bargain_id) {
            if (!bargain_id) {
                return;
            }

            $.dialog.confirm("确认终止？", function () {
                Public.ajaxPost(SITE_URL + '?ctl=Promotion_Bargain&met=editBargainStatus&typ=json', {
                    bargain_id: bargain_id
                }, function (data) {
                    if (data && data.status == 200) {
                        parent.Public.tips({
                            content: __('终止成功！')
                        });
                        THISPAGE.reloadData('');
                    } else {
                        parent.Public.tips({
                            type: 1,
                            content: __('终止失败！') + data.msg
                        });
                    }
                });
                }
            )
        }
    };
    var THISPAGE = {
        initDom: function(){
            var defaultPage = Public.getDefaultPage();
            defaultPage.SYSTEM = defaultPage.SYSTEM || {};
            this.$_goods_name = $('#goods_name');
            this.$_shop_name = $('#shop_name');
            this.$_goods_name.placeholder();
            this.$_shop_name.placeholder();
        },
        loadGrid: function(){
            var gridWH = Public.setGrid(), _self = this;
            var colModel = [
                {name:'goods_name', label:'商品名称',"classes": "ui-ellipsis", width:200, align:"center"},
                {name:'shop_name', label:'店铺名称',"classes": "ui-ellipsis", width:150, align:"center","formatter": handle.linkShopFormatter},
                {name:'start', label:'活动开始时间',  width:150, align:"center"},
                {name:'end', label:'活动结束时间',"classes": "ui-ellipsis", width:100,align:'center'},
                {name:'join_num', label:'参与人数',"classes": "ui-ellipsis", width:100, align:"center"},
                {name:'buy_num', label:'购买人数',"classes": "ui-ellipsis",  width:150, align:"center"},
                {name:'bargain_status_con', label:'活动状态',"classes": "ui-ellipsis",  width:150, align:"center"},
                {name: 'operating', label: '操作', "classes": "ui-ellipsis", width: 80, fixed: true, formatter: operFmatter, align: "center"}
            ];
            $("#grid").jqGrid({
                url: SITE_URL + '?ctl=Promotion_Bargain&met=bargain_list&typ=json',
                postData: queryConditions,
                datatype: "json",
                autowidth: true,//如果为ture时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：setGridWidth
                height: gridWH.h,
                altRows: true, //设置隔行显示
                gridview: true,
                multiboxonly: true,
                colModel:colModel,
                cmTemplate: {sortable: false, title: false},
                page: 1,
                sortname: 'number',
                sortorder: "desc",
                pager: "#page",
                rowNum: 100,
                rowList:[100,200,500],
                viewrecords: true,
                shrinkToFit: false,
                forceFit: true,
                jsonReader: {
                    root: "data.items",
                    records: "data.records",
                    repeatitems : false,
                    total : "data.total",
                    id: "bargain_id"
                },
                loadError : function(xhr,st,err) {

                },
                resizeStop: function(newwidth, index){
                    THISPAGE.mod_PageConfig.setGridWidthByIndex(newwidth, index, 'grid');
                }
            }).navGrid('#page',{
                edit:false,
                add:false,
                del:false,
                search:false,
                refresh:false}).navButtonAdd('#page',
                {
                    caption:"",
                    buttonicon:"ui-icon-config",
                    onClickButton: function(){
                        //THISPAGE.mod_PageConfig.config();
                    },
                    position:"last"
                });

            function operFmatter (val, opt, row) {
                var html_con = '<div class="operating" data-bargain_id="' + row.bargain_id + '">' +
                    '<span class="ui-icon ui-icon-search" title="查看"></span>';

                    if(row.bargain_status == 1){
                        html_con += '<span class="ui-icon ui-icon-close" title="关闭"></span>'
                    }
                html_con += '</div>';
                return html_con;
            };
        },
        reloadData: function(data){
            $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
        },
        addEvent: function(){
            var _self = this;
            //查看活动详情
            $('.grid-wrap').on('click', '.ui-icon-search', function(e){
                e.preventDefault();
                var e = $(this).parent().data("bargain_id");
                handle.operate("edit", e)
            });

            //平台终止活动
            $('#grid').on('click', '.ui-icon-close', function (e) {
                e.stopPropagation();
                e.preventDefault();

                var bargain_id = $(this).parent().data("bargain_id");
                handle.setStatus(bargain_id);
            });

            //搜索
            $('#search').click(function(){
				queryConditions.bargain_status = $source.getValue();
                queryConditions.goods_name = _self.$_goods_name.val();
                queryConditions.shop_name = _self.$_shop_name.val();
                THISPAGE.reloadData(queryConditions);
            });

            //刷新
            $("#btn-refresh").click(function ()
            {
                THISPAGE.reloadData('');
                _self.$_goods_name.val('');
                _self.$_shop_name.val('');
            });

            $(window).resize(function(){
                Public.resizeGrid();
            });
        }
    };

    THISPAGE.initDom();
    THISPAGE.loadGrid();
    THISPAGE.addEvent();
});
