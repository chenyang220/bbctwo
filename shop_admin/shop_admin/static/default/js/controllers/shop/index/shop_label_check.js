var queryConditions = {
        cardName: ''
    },  
    hiddenAmount = false, 
    SYSTEM = system = parent.SYSTEM;
var THISPAGE = {
    init: function(data){
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        };
        this.mod_PageConfig = Public.mod_PageConfig.init('complain-new-list');//页面配置初始化
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
            {name:'operating', label:'操作', width:90, fixed:true, formatter:operFmattershop, align:"center"},
            {name:'user_name', label:'店主账号',  width:100, align:"center"},
            {name:'shop_name', label:'店铺名称', width:200,align:'center',"formatter": handle.linkShopFormatter},
            {name:'shop_logo', label:'店铺logo', width:100,align:"center", "formatter": handle.imgFmt ,classes:'img_flied'},
            {name:'shop_grade', label:'店铺等级',  width:110, align:"center"},
            {name:'shop_create_time', label:'开店时间',  width:150, align:"center"},
            {name:'shop_end_time', label:'到期时间',  width:150, align:"center"},
            {name:'shop_status_cha', label:'当前状态',  width:100, align:"center"},
            {name:'shop_company_address', label:'所在区域',  width:200, align:"center"},
            {name:'company_address_detail', label:'详细地址',  width:300, align:"left"},
            {name:'label_name', label:'标签',  width:150,fixed:true,  align:"center"},
            {name:'shop_id', label:'标签',  width:150,fixed:true,  align:"center",data:"shop_id" ,hidden:true},
            {name:'label_remarks', label:'标签',  width:150,fixed:true,  align:"center",data:"label_remarks"},
            {name:'label_is_check', label:'标签',  width:150,fixed:true,  align:"center",data:"label_is_check",hidden:true}
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url:SITE_URL +  "?ctl=Shop_Manage&met=shopIndexs&typ=json",
            postData: queryConditions,
            datatype: "json",
            autowidth: true,//如果为ture时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：setGridWidth
            height: Public.setGrid().h,
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
            rowNum: 20,
            rowList:[20,50,100,200,500],
            viewrecords: true,
            shrinkToFit: false,
            forceFit: true,
            jsonReader: {
              root: "data.items",
              records: "data.records",
              repeatitems : false,
              total : "data.total",
              id: "shop_id"
            },
            loadComplete: function (t)
            {
              console.log(t);
            },
            loadError : function(xhr,st,err) {
                
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
        
    
    function operFmattershop(val, opt, row) {
        if (row.label_is_check == 1) {
            var html_con = "";
        } else {
            var html_con = '<div class="operating" data-id="' + row.shop_id + '" data-remarks="' + row.label_remarks + '"><span class="ui-icon ui-icon-pencil" title="审核"></span></div>';
        }
        
        return html_con;
    };

    },
    reloadData: function(data){
        $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
    },
    addEvent: function(){
        var _self = this;
        $('#search').click(function(){
            queryConditions.search_name = _self.$_searchName.val() === '请输入相关数据...' ? '' : _self.$_searchName.val();
            queryConditions.user_type = $source.getValue();
            queryConditions.shop_class = $shop_class.getValue();
            THISPAGE.reloadData(queryConditions);
        });
	    //编辑
        $('.grid-wrap').on('click', '.ui-icon-pencil', function(){
            var shop_id = $(this).parent().data("id");
               var label_remarks = $(this).parent().data("remarks");
            $.dialog({
                title: "审核标签信息",
                content: "url:"+ SITE_URL + '?ctl=Shop_Manage&met=checkLabel',
                data: {shop_id:shop_id,label_remarks:label_remarks,callback:callback},
                width: 500,
                height: 300,
                max: !1,
                min: !1,
                cache: !1,
                lock: !0,
            })
        });
        $("#btn-refresh").click(function ()
        {
            $("#grid").trigger("reloadGrid");
        });

        $(window).resize(function(){
            Public.resizeGrid();
        });
    }
};

function callback (i)
{
   
    $("#grid").trigger("reloadGrid");
    i && i.api.close()
}
var handle = {
       linkShopFormatter: function(val, opt, row) {
        return '<a href="javascript:void(0)"><span class="to-shop" data-id="' + row.shop_id + '">' + val + '</span></a>';
    },
    operate: function (t, e)
    {         
        if ("add" == t)
        {
            var i = "新增店铺", a = {oper: t,  callback:testF};
        }
        else
        {
            var i = "编辑店铺", a = {oper: t, callback:testF};
           
        }
        $.dialog({
            title: i,
            content: "url:"+ SITE_URL + "?ctl=Shop_Manage&met=getinformationrow&shop_id=" + e,
            data: a,
            width: 600,
            height: 450,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    }, 

      imgFmt: function (val, opt, row)
    {
        if (row.level == 0 && val)
        {
            val = '<img src="' + val + '">';
        }
        else
        {
            if (row.shop_logo)
            {
                val = '<img src="' + row.shop_logo + '">';
            }
            else
            {
                val = '&#160;';
            }
        }
        return val;
    }
};
   function testF(){ 
      window.location.reload(); 
}
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

    $.get("./index.php?ctl=Shop_Class&met=shopClass&typ=json", function(result){
        if(result.status==200)
        {
            var r = result.data;

            $shop_class = $("#shop_class").combo({
                data:r,
                value: "id",
                text: "name",
                width: 110
            }).getCombo();
        }
    });

    /*$shop_class = $("#shop_class").combo({
        data: [{
            id: "0",
            name: "分类111"
        },{
            id: "1",
            name: "分类222"
        }],
        value: "id",
        text: "name",
        width: 110
    }).getCombo();*/

    THISPAGE.init();
    
});
