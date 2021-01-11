/**
 * Created by Administrator on 2016/5/15.
 */
$(function(){
    var queryConditions = {
            shop_name:''
        },
        hiddenAmount = false,
        SYSTEM = system = parent.SYSTEM;
    var THISPAGE = {
        initDom: function(){
            var defaultPage = Public.getDefaultPage();
            defaultPage.SYSTEM = defaultPage.SYSTEM || {};
            this.$_shop_name = $('#shop_name');
            this.$_shop_name.placeholder();
        },
        loadGrid: function(){
            var gridWH = Public.setGrid(), _self = this;
            var colModel = [
                {name: 'shop_name', label: '店铺名称', "classes": "ui-ellipsis", width: 200, align: "center"},
                {name: 'combo_start_time', label: '开始时间', "classes": "ui-ellipsis", width: 200, align: "center"},
                {name: 'combo_end_time', label: '结束时间', "classes": "ui-ellipsis", width: 200, align: "center"},
                {name: 'paycount', label: '支付总额', "classes": "ui-ellipsis", width: 200, align: "center"},
            ];
            $("#grid").jqGrid({
                url: SITE_URL + '?ctl=Promotion_Bargain&met=getComboList&typ=json',
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
                    id: "combo_id"
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

        },
        reloadData: function(data){
            $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
        },
        addEvent: function(){
            var _self = this;

            //搜索
            $('#search').click(function(){
                queryConditions.shop_name = _self.$_shop_name.val();
                THISPAGE.reloadData(queryConditions);
            });

            //刷新
            $("#btn-refresh").click(function ()
            {
                THISPAGE.reloadData('');
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
