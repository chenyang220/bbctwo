var queryConditions = {
       
    },  
    hiddenAmount = false, 
    SYSTEM = system = parent.SYSTEM;
    
var THISPAGE = {
    init: function(data){
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        };
        this.mod_PageConfig = Public.mod_PageConfig.init('plus_list');//页面配置初始化
        this.initDom();
        this.loadGrid();            
        this.addEvent();
    },
    initDom: function(){
        this.$_searchName = $('#searchName');
		this.$_searchName.placeholder();
    },
    loadGrid: function(){
        var gridWH = Public.setGrid(), _self = this;
        var colModel = [
            {name:'user_name', label:'plus会员名称', width:100, align:"center"},
            {name:'user_mobile', label:'会员手机', width:100,align:'center'},
            {name:'user_status', label:'plus会员状态', width:200,align:'center'},
            {name:'end_date', label:'会员结束日期', width:300, align:"center"},
            {name:'operating', label:'操作', width:100, fixed:true, formatter:operFmatter, align:"center"},
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url:SITE_URL +  '?ctl=User_Plus&met=getPlusList&typ=json',
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
              id: "user_id"
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
           var html_con = '<div class="operating" data-id="' + row.user_id+ '"><span class="ui-icon ui-icon ui-icon-search" title="查看"></span></div></div>';
            return html_con;
        };

        function online_imgFmt(val, opt, row){
            if(val)
            {
                val = '<img src="'+val+'" height=100>';
            }
            else
            {
                val='';
            }
            return val;
        }

    },
    reloadData: function(data){
        $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
    },
    addEvent: function(){
        var _self = this;
		//查询
		 $('#search').click(function(){
            
            queryConditions.search_name = _self.$_searchName.val() === 'plus会员名称' ? '' : _self.$_searchName.val();
            queryConditions.user_type = $source.getValue();
            THISPAGE.reloadData(queryConditions);
        });
        //查看
        $('.grid-wrap').on('click', '.ui-icon-search', function(e){
            e.preventDefault();
            var e = $(this).parent().data("id");
            handle.operate("edit", e)
        });
       $("#btn-refresh").click(function (t)
        {
            t.preventDefault();
            $("#grid").trigger("reloadGrid")
        });
       
        $(window).resize(function(){
            Public.resizeGrid();
        });
    }
};

var handle = {
    operate: function (t, e)
    {
        if ("edit" == t)
        {
            var i = "会员购买详情";
            $.dialog({
                title: i,
                content: "url:"+SITE_URL + '?ctl=User_Plus&met=plusUserList&id='+ e,
                data: {id: e},
                width:1030,
                // height: $(window).height() * 0.9,
                height:400,
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            })
        }
    }
};

$(function(){
  $source = $("#source").combo({
        data: [{
            id: "0",
            name: "会员状态"
        },{
            id: "1",
            name: "试用中"
        },{
            id: "2",
            name: "未到期"
        },{
            id: "3",
            name: "已到期"
        }],
        value: "id",
        text: "name",
        width: 110
    }).getCombo();

    Public.pageTab();

    THISPAGE.init();
    
});
