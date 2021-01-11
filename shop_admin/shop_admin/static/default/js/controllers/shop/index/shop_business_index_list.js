//获取url中的参数
function getUrlParam(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return unescape(r[2]); return null; //返回参数值
}


var type = getUrlParam('type');


var queryConditions = {
        cardName: '',
        type:type
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
        $('#filter-fromDate').datetimepicker({lang:'ch'}).prop('readonly', 'readnoly');
        $('#filter-toDate').datetimepicker({lang:'ch'}).prop('readonly', 'readnoly');
    },
    loadGrid: function(){
        var gridWH = Public.setGrid(), _self = this;
        var colModel = [
            {name:'sub_site_name', label:'所属分站',  width:100, align:"center",hidden : true,sortable:false},
            {name:'user_name', label:'店主账号',  width:100, align:"center"},
            {name:'shop_name', label:'店铺名称', width:200,align:'center',"formatter": handle.linkShopFormatter},
            {name:'shop_grade', label:'店铺等级',  width:110, align:"center"},
            {name:'shop_class', label:'店铺类型',  width:100, align:"center"},
            {name:'shop_company_address', label:'所在区域',  width:200, align:"center"},
            {name:'shop_tel', label:'商家电话',  width:100, align:"center"},
            {name:'order_member_sum', label:'下单人数',  width:150, align:"center"},
            {name:'order_member', label:'订单数',  width:150, align:"center"},
            {name:'market_good_sum', label:'销量',  width:150, align:"center"},
            {name:'market_good_money', label:'销售额',  width:150, align:"center"},
            {name:'practical_money', label:'实付款金额',  width:150, align:"center"}
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url:SITE_URL +  "?ctl=Shop_Manage&met=businessIndex&typ=json",
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
            loadError : function(xhr,st,err) {
                
            },
            resizeStop: function(newwidth, index){
                THISPAGE.mod_PageConfig.setGridWidthByIndex(newwidth, index, 'grid');
            },
            loadComplete: function (data) {
                if (data.data.sub_site_id) {
                    queryConditions.sub_site_id = data.data.sub_site_id;
                } else {
                    queryConditions.sub_site_id = 0;
                }
                if (type == 'all') {
                    $("#grid").setGridParam().showCol("sub_site_name");
                } else {
                    $("#grid").setGridParam().hideCol("sub_site_name");
                }
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
    var html_con = '<div class="operating" data-id="' + row.shop_id + '"><span class="ui-icon ui-icon-search" title="查看"></span><span class="ui-icon ui-icon-gear" title="编辑店铺信息"></span><span class="ui-icon ui-icon-pencil" title="修改经营类目"></span></div>';
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
            var type = $("#t_type").html();
            if (type == 'all') {
                queryConditions.sub_id = $substations_select.getValue();
                queryConditions.acction = 'search_sub';
            } 
             
            if ( $('#filter-fromDate').val() ) {
                queryConditions.payment_date_f = $('#filter-fromDate').val();
            }
            if ( $('#filter-toDate').val() ) {
                queryConditions.payment_date_t = $('#filter-toDate').val();
            }

            THISPAGE.reloadData(queryConditions);
        });
	//编辑
	$('.grid-wrap').on('click', '.ui-icon-pencil', function(e){
            e.preventDefault();
            var shop_id = $(this).parent().data("id");
            parent.tab.addTabItem({
            tabid: shop_id,
            text: '经营类目',
            url: SITE_URL + '?ctl=Shop_Manage&met=editCategory&shop_id=' + shop_id,
            })
        });
        //跳转到店铺认证信息页面
        $('#grid').on('click', '.to-shop', function(e) {
            e.stopPropagation();
            e.preventDefault();
            var shop_id = $(this).attr('data-id');
            $.dialog({
                title: '查看店铺信息',
                content: "url:"+SITE_URL + '?ctl=Shop_Manage&met=getShoplist&shop_id=' + shop_id,
                width:1000,
                height: $(window).height(),
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            })
        });
        //获取系统当前时间
        var nowdate = new Date();
        var y = nowdate.getFullYear();
        var m = nowdate.getMonth()+1;
        var d = nowdate.getDate();
        var formatnowdate = y+'-'+m+'-'+d;
     
        //获取系统前一周的时间
        var oneweekdate = new Date(nowdate-7*24*3600*1000);
        var y = oneweekdate.getFullYear();
        var m = oneweekdate.getMonth()+1;
        var d = oneweekdate.getDate();
        var formatweekdate = y+'-'+m+'-'+d;

        //获取系统前一个月的时间
        nowdate.setMonth(nowdate.getMonth()-1);
        var y = nowdate.getFullYear();
        var m = nowdate.getMonth()+1;
        var d = nowdate.getDate();
        var formatwdate = y+'-'+m+'-'+d;
       $("#today").click(function(){
             queryConditions.payment_date_f = $('#filter-fromDate').val(formatnowdate+' 00:00:00');
             queryConditions.payment_date_t = $('#filter-toDate').val(formatnowdate+' 24:00:00');
       }) 
       $("#weekly").click(function(){
             queryConditions.payment_date_f = $('#filter-fromDate').val(formatweekdate+' 00:00:00');
             queryConditions.payment_date_t = $('#filter-toDate').val(formatnowdate+' 24:00:00');
       })
       $("#month").click(function(){
             queryConditions.payment_date_f = $('#filter-fromDate').val(formatwdate+' 00:00:00');
             queryConditions.payment_date_t = $('#filter-toDate').val(formatnowdate+' 24:00:00');
       })
   //导出
    $("#btn-excel").click(function ()
    {
        queryConditions.rows = $("#grid").jqGrid('getGridParam', 'rowNum');
        queryConditions.page = $('#grid').getGridParam('page');
        queryConditions.action = $(this).data('action');
        queryConditions.type = type;
        $.dialog({
            title: '业绩统计导出',
            content: "url:"+SITE_URL + '?ctl=Shop_Business&met=exportOrder',
            data: queryConditions,
            width: 600,
            height: $(window).height()*0.4,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    });

        //编辑
	$('.grid-wrap').on('click', '.ui-icon-gear', function(e){
                e.preventDefault();
                var e = $(this).parent().data("id");
                handle.operate("edit",e);
        });
        //跳转店铺详情
	$('.grid-wrap').on('click', '.ui-icon-search', function(e){
            e.preventDefault();
            var shop_id = $(this).parent().data("id");
              $.dialog({
                title: "查看店铺详情",
                content: "url:"+ SITE_URL + '?ctl=Shop_Manage&met=getShoplist&shop_id=' + shop_id,
                width: 950,
                height:$(window).height(),
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            })
        
        });
        
        $("#btn-refresh").click(function ()
        {
            THISPAGE.reloadData('');
            $('#filter-fromDate').val("");
            $('#filter-toDate').val("");
            _self.$_searchName.val('请输入相关数据...');
        });

        $(window).resize(function(){
            Public.resizeGrid();
        });
    }
};
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

    $.get("./index.php?ctl=Subsite_Config&met=getSubsiteListDefault&typ=json", function(result){
        if(result.status==200)
        {
            var r = result.data.items;
            $substations_select = $("#substations_select").combo({
                data:r,
                value: "subsite_id",
                text: "sub_site_name",
                width: 110
            }).getCombo();
        }
    });
    

    THISPAGE.init();
    
});
