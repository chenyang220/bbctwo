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
            {name:'operating', label:'操作', width:60, fixed:true, formatter:operFmatter, align:"center"},
            {name:'consume_record_id', label:'序号', width:100, align:"center"},
            {name:'user_realname', label:'真实姓名', width:100, align:"center"},
            {name:'user_id', label:'账号主键', width:100, align:"center"},
            {name:'user_nickname', label:'账号名', width:100,align:'center'},
            {name:'user_mobile', label:'手机号', width:120, align:"center"},
            {name:'record_money', label:'本次还款金额', width:150, align:"center"},
            {name:'credit_remain', label:'剩余还款金额', width:150, align:"center"},
            {name:'credit_status', label:'状态', width:100, align:"center"},
            {name:'record_paytime', label:'还款日期', width:160, align:"center"},
            {name:'certificate', label:'还款凭证', width:160, align:"center",formatter: imgFormatter},
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url: SITE_URL +'?ctl=Paycen_PayInfo&met=getBtReturnRecordList&typ=json',
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
              id: "consume_record_id"
            },
            loadError : function(xhr,st,err) {
                
            },
            // ondblClickRow : function(rowid, iRow, iCol, e){
            //     e.stopPropagation();
            //     e.preventDefault();
            //     $('#' + rowid).find('.ui-icon-pencil').trigger('click');
            // },
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
            var html_con = '<div class="operating" data-id="' + row.consume_record_id + '">';
               if (row.record_money==0.00 || row.record_money==0) {
                html_con +='<span class="ui-icon ui-icon-pencil" title="编辑"></span></div>';
               }else{
                html_con +='<span class="ui-icon ui-icon-pencil ui-icon-disabled" title="编辑"></span></div>';
               }
            return html_con;
        };
        function imgFormatter(val, opt, row) {
            return '<img src="'+val+'" style="width:50px;height:50px;" class="pimg"/>';
        };

 

    },
    reloadData: function(data){
        $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
    },
    addEvent: function(){
        var _self = this;
        //编辑
        $('.grid-wrap').on('click', '.ui-icon-pencil', function(e){
            e.stopPropagation();
            e.preventDefault();
            if (!$(e.target).hasClass('ui-icon-disabled')) {
                var e = $(this).parent().data("id");
                handle.operate("edit", e)
            }
        });
        //删除


        $('#search').click(function(){
            queryConditions.searchName = $searchName.getValue();
            queryConditions.searchContent = _self.$_searchContent.val();
            queryConditions.beginDate = _self.$_beginDate.val();
            queryConditions.endDate = _self.$_endDate.val();
            queryConditions.status = $status.getValue() === '请选择审核状态' ? '' : $status.getValue();
            THISPAGE.reloadData(queryConditions);
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
            var i = "收款确认", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};
            var getRowData = $("#grid").jqGrid('getRowData',e);
            var user_id = getRowData.user_id;
            $.dialog({
                title: i,
                content: "url:./index.php?ctl=Paycen_BtInfo&met=editCreditReturn&user_id="+user_id+'&consume_record_id='+e,
                data: a,
                width: 550,
                height: 100,
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
                })
        }
        else
        {
            var i = "新增购物卡", a = {oper: t, callback: this.callback};
        }
    }, callback: function (t, e, i)
    {
           window.location.reload(); 
    },imgFmt: function (val, opt, row)
    {
        if (val)
        {
            val = '<img src="' + val + '">';
        }
        else
        {
            if (row.user_identity_face_logo)
            {
                val = '<img height="30" width="100" src="' + row.user_identity_face_logo + '">';
            }
            else
            {
                val = '<img height="30" width="100" src="' + row.user_identity_font_logo + '">';
            }
        }
        return val;
    }
};
$(function(){
     $status = $("#status").combo({
        data: [{
            id: "0",
            name: "请选择还款状态"
        },{
            id: "1",
            name: "已还款"
        }, {
            id: "2",
            name: "待还款"
        }],
        value: "id",
        text: "name",
        width: 180
    }).getCombo();

    $searchName = $("#searchName").combo({
        data: [{
            id: "user_nickname",
            name: "会员昵称"
        },{
            id: "user_realname",
            name: "真实姓名"
        }, {
            id: "user_mobile",
            name: "用户手机号"
        }],
        value: "id",
        text: "name",
        width: 180
    }).getCombo();

    THISPAGE.init();
    
});


$(document).on('click','.pimg',function(){
    var _this = $(this);//将当前的pimg元素作为_this传入函数  
    imgShow("#outerdiv", "#innerdiv", "#bigimg", _this);  
});
function imgShow(outerdiv, innerdiv, bigimg, _this){  
    var src = _this.attr("src");//获取当前点击的pimg元素中的src属性  
    $(bigimg).attr("src", src);//设置#bigimg元素的src属性  
  
        /*获取当前点击图片的真实大小，并显示弹出层及大图*/  
    $("<img/>").attr("src", src).load(function(){  
        var windowW = $(window).width();//获取当前窗口宽度  
        var windowH = $(window).height();//获取当前窗口高度  
        var realWidth = this.width;//获取图片真实宽度  
        var realHeight = this.height;//获取图片真实高度  
        var imgWidth, imgHeight;  
        var scale = 0.8;//缩放尺寸，当图片真实宽度和高度大于窗口宽度和高度时进行缩放  
          
        if(realHeight>windowH*scale) {//判断图片高度  
            imgHeight = windowH*scale;//如大于窗口高度，图片高度进行缩放  
            imgWidth = imgHeight/realHeight*realWidth;//等比例缩放宽度  
            if(imgWidth>windowW*scale) {//如宽度扔大于窗口宽度  
                imgWidth = windowW*scale;//再对宽度进行缩放  
            }  
        } else if(realWidth>windowW*scale) {//如图片高度合适，判断图片宽度  
            imgWidth = windowW*scale;//如大于窗口宽度，图片宽度进行缩放  
                        imgHeight = imgWidth/realWidth*realHeight;//等比例缩放高度  
        } else {//如果图片真实高度和宽度都符合要求，高宽不变  
            imgWidth = realWidth;  
            imgHeight = realHeight;  
        }  
          $(bigimg).css("width",imgWidth);//以最终的宽度对图片缩放  
          
        var w = (windowW-imgWidth)/2;//计算图片与窗口左边距  
        var h = (windowH-imgHeight)/2;//计算图片与窗口上边距  
        $(innerdiv).css({"top":h, "left":w});//设置#innerdiv的top和left属性  
        $(outerdiv).fadeIn("fast");//淡入显示#outerdiv及.pimg  
    });  
          
        $(outerdiv).click(function(){//再次点击淡出消失弹出层  
            $(this).fadeOut("fast");  
        });  
    }

