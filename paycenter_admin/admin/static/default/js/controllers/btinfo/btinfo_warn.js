var queryConditions = {
    },  
    hiddenAmount = false, 
    SYSTEM = system = parent.SYSTEM;
var THISPAGE = {
    init: function(data){
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        };
        this.mod_PageConfig = Public.mod_PageConfig.init('other-income-list');//页面配置初始化
        this.initDom();
        this.loadGrid();            
        this.addEvent();
    },
    initDom: function(){
        this.$_searchContent = $('#searchContent');
        this.$_beginDate = $('#beginDate').val(system.beginDate);
        this.$_endDate = $('#endDate').val(system.endDate);
        this.$_searchContent.placeholder();
        this.$_beginDate.datepicker();  
        this.$_endDate.datepicker();        
    },
    loadGrid: function(){
        var gridWH = Public.setGrid(), _self = this;
        queryConditions.beginDate = this.$_beginDate.val();
        queryConditions.endDate = this.$_endDate.val();
        var colModel = [
            {name:'operating', label:'发送邮件短信', width:150, fixed:true, formatter:operFmatter, align:"center"},
            {name:'pay_user_id', label:'用户id', width:100, align:"center"},
            {name:'info_type', label:'消息类型', width:100, align:"center"},
            {name:'reminder_time', label:'提醒时间', width:100,align:'center'},
            {name:'user_nickname', label:'还款账户', width:200, align:"center"},
            {name:'user_mobile', label:'电话号码', width:0, align:"center"},
            {name:'user_email', label:'邮箱', width:0, align:"center"},
            {name:'repay_price', label:'还款余额', width:100, align:"center"},
            {name:'repayment_time', label:'还款日期', width:300, align:"center"}
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url: SITE_URL +'?ctl=Paycen_PayInfo&met=getBtWarnList&typ=json',
            postData: queryConditions,
            datatype: "json",
            autowidth: true,//如果为ture时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：setGridWidth
            height: gridWH.h,
            altRows: true, //设置隔行显示
            gridview: true,
            multiselect: false,
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
              id: "pay_user_id"
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
        
        function operFmatter (val, opt, row) {
            var html_con = '<div class="operating" data-id="' + row.pay_user_id + '"><span class="ui-icon ui-icon-mail-closed" title="发送邮件提醒"></span></div>';
            return html_con;
        };
    },
    reloadData: function(data){
        $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
    },
    addEvent: function(){
        var _self = this;
        //发送短信您提醒
        $('.grid-wrap').on('click', '.ui-icon-mail-closed', function(e){
            e.preventDefault();
            var u = $(this).parent().data("id");
            var data = $("#grid").jqGrid('getRowData',u);
            if (data.user_email=='') {
                parent.Public.tips({type: 1,content: '邮箱信息不存在'});
                return false;
            }
            $.ajax({
                type: 'POST',
                url: SITE_URL+'?ctl=Paycen_PayInfo&met=sendMessage&typ=json',
                data: data,
                success:function(val){
                    if (val.status == 200) {
                        parent.Public.tips({content: '发送成功'});
                    }else{
                        parent.Public.tips({type: 1,content: '发送失败'});
                    }
                },
                fail:function(){
                   parent.Public.tips({type: 1,content: '系统错误'});
                }
            });
        });
        //查询
        $('#search').click(function(){
            queryConditions.searchName = $searchName.getValue();
            queryConditions.searchContent = _self.$_searchContent.val();
            queryConditions.beginDate = _self.$_beginDate.val();
            queryConditions.endDate = _self.$_endDate.val();
            THISPAGE.reloadData(queryConditions);
        });
        $(window).resize(function(){
            Public.resizeGrid();
        });
    }
};
$(function(){
    $searchName = $("#searchName").combo({
        data: [{
            id: "user_nickname",
            name: "会员昵称"
        }],
        value: "id",
        text: "name",
        width: 180
    }).getCombo();
    THISPAGE.init();
});
