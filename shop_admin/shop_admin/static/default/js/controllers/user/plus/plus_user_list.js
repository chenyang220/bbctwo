/**
 * Created by Administrator on 2016/5/18.
 */

var queryConditions = {

    },
    hiddenAmount = false,
    SYSTEM = system = parent.SYSTEM;
var THISPAGE = {
    init: function(data){
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        };
        this.mod_PageConfig = Public.mod_PageConfig.init('discount-goods-list');//页面配置初始化

        this.loadGrid();
        this.addEvent();
    },
    loadGrid: function(){
        var gridWH = Public.setGrid(), _self = this;
        var colModel = [
            {name:'payment_number', label:'订单号', width:150, align:"center"},
            {name:'pay_use', label:'付费模式', width:60, align:"center"},
            {name:'payment', label:'支付金额（元）',  width:80, align:"center"},
            {name:'pay_time', label:'支付时间',  width:120, align:"center"},
            {name:'start_date', label:'会员开始日期',  width:100, align:"center"},
            {name:'end_date', label:'会员结束日期',  width:100, align:"center"},
            {name:'order_status', label:'购买状态',  width:100, align:"center"},
            {name:'create_time', label:'创建时间',  width:100, align:"center"},
            {name:'pay_status', label:'支付状态',  width:100, align:"center"}
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url: SITE_URL + '?ctl=User_Plus&met=getPlusUserListById&typ=json&id='+data.id,
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
            sortname: 'user_order_id',
            sortorder: "desc",
            pager: "#page",
            rowNum: 10,
            rowList:[10,20,50],
            viewrecords: true,
            shrinkToFit: false,
            forceFit: true,
            jsonReader: {
                root: "data.items",
                records: "data.records",
                repeatitems : false,
                total : "data.total",
                id: "user_order_id"
            },
            loadError : function(xhr,st,err) {

            },
            ondblClickRow : function(rowid, iRow, iCol, e){
                $('#' + rowid).find('.ui-icon-pencil').trigger('click');
            },

            resizeStop: function(newwidth, index){
                THISPAGE.mod_PageConfig.setGridWidthByIndex(newwidth, index, 'grid');
            }
        }).navGrid('#page',{edit:false,add:false,del:false,search:false,refresh:false}).navButtonAdd('#page',{
            caption:"",
            buttonicon:"ui-icon-config",
            onClickButton: function(){
                THISPAGE.mod_PageConfig.config();
            },
            position:"last"
        });
    },

    reloadData: function(data){
        $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
    },
    addEvent: function(){
        var _self = this;
        $(window).resize(function(){
            Public.resizeGrid();
        });
    }
};

$(function(){
    $source = $("#source").combo({
        data: [{
            id: "0",
            name: "店主账号"
        },{
            id: "1",
            name: "店铺名称"
        }],
        value: "id",
        text: "name",
        width: 110
    }).getCombo();
    THISPAGE.init();
});

api = frameElement.api;
data = api.data;