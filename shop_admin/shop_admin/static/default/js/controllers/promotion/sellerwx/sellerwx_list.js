/**
 * Created by Administrator on 2016/5/15.
 */
var queryConditions = {
        shop_name:''
    },
    hiddenAmount = false,
    SYSTEM = system = parent.SYSTEM;
var THISPAGE = {
    init: function(data){
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        };
        this.mod_PageConfig = Public.mod_PageConfig.init('increase-list');//页面配置初始化
        this.loadGrid();
        this.addEvent();
    },
    loadGrid: function(){
        var gridWH = Public.setGrid(), _self = this;
        var colModel = [
            {name:'operating', label:'操作', width:100, fixed:true, formatter:operFmatter, align:"center"},
            {name:'shop_name', label:'商家名称', width:300, align:"center"},
            {name:'years', label:'申请年限', width:100, align:"center"},
            {name:'wx_public_name', label:'公众号名称', width:300, align:"center","formatter": handle.linkShopFormatter},
            {name:'time', label:'申请时间',  width:150, align:"center"},
            {name: "pay_images", label: "付款凭证", formatter:online_imgFmt,align: "center", width: 150},
            {name:'start_time', label:'开始时间',  width:150, align:"center"},
            {name:'end_time', label:'结束时间',  width:100, align:"center"},
            {
                name:'status',
                label:'状态',
                width:150,
                align:"center",
                formatter: function (e) {
                    var a = "";
                    switch (e) {
                        case '0':
                            a = "待审核";
                            break;
                        case '1':
                            a = "拒绝";
                            break;
                        case '2':
                            a = "通过";
                            break;
                        case '3':
                            a = "停用";
                            break;        
                    }
                    return a
                }
            },
			{name:'tplsend', label:'模板消息推送', width:150, fixed:true, formatter:tplsendFmatter, align:"center"},
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        var type = $('#type').val();
        $("#grid").jqGrid({
            url: SITE_URL + '?ctl=Promotion_SellerWx&met=sellerWxlist&typ=json&type='+type,
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
                id: "id"
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
        function operFmatter (val, opt, row) {
            var html_con = '<div class="operating" data-id="' + row.id + '"><span class="ui-icon ui-icon-search" title="详情"></span><span class="ui-icon ui-icon-pencil" title="修改状态"></span></div>';
            return html_con;
        }
		/***** 是否开启该公众号模板消息推送 ******/
		function tplsendFmatter (val, opt, row) {
			var html = '<div class="operating" data-id="' + row.shop_id + '"><span status="' + row.state + '" class="ui-icon ui-icon-closethick tplsend" title="已关闭"></span></div>';
			if(row.state>0){
				html =  '<div class="operating" data-id="' + row.shop_id + '"><span status="' + row.state + '" class="ui-icon ui-icon-check tplsend" title="已开启"></span></div>';
			}
            return html;
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
        //活动详情
        $('.grid-wrap').on('click', '.ui-icon-search', function(e){
            e.preventDefault();
            var e = $(this).parent().data("id");
            handle.operate("detail", e)
        });

        //设置状态
        $('.grid-wrap').on('click', '.ui-icon-pencil', function (e)
        {
            e.preventDefault();
            var e = $(this).parent().data("id");
            handle.setStatus("detail",e);
        });
        //搜索
        $('#search').click(function(){
            queryConditions.shop_name = _self.$_shop_name.val();
            THISPAGE.reloadData(queryConditions);
        });

        //刷新
        $("#btn-refresh").click(function ()
        {
            THISPAGE.reloadData(queryConditions);
        });
		
		//切换开关
        $('.grid-wrap').on('click','.tplsend', function(e,){
            e.preventDefault();
            var id = $(this).parent().data("id");
			var status = $(this).attr("status")||0;
            handle.openWxpublicTplmsg(id,status);
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
        if ("detail" == t)
        {
            var i = "公众号绑定审核", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};
        }
        $.dialog({
            title: i,
            content: "url:"+ SITE_URL + '?ctl=Promotion_SellerWx&met=getList&typ=e&id=' + e,
            data: a,
            width: 670,
            height:410,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    },
    callback: function (t, e, i,p)
    {
        var a = $("#grid").data("gridData");
        if (!a)
        {
            a = {};
            $("#grid").data("gridData", a)
        }
        a[t.id] = t;
        if(p == 1)
        {
            if ("edit" == e)
            {
                $("#grid").jqGrid("setRowData", t.id, t);
                i && i.api.close()
            }
            else
            {
                $("#grid").jqGrid("addRowData", t.id, t, "last");
                i && i.api.close()
            }
        }
        else
        {
            i && i.api.close()
        }

    },
    setStatus: function (t,e)
    {
        var i = "编辑", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};
        $.dialog({
            title: i,
            content: "url:"+ SITE_URL + '?ctl=Promotion_SellerWx&met=editSellerWx&typ=e&id=' + e,
            data: a,
            width: 670,
            height:410,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    },
	openWxpublicTplmsg:function(id,status){
		var txt ="确定【"+((status>0)?"关闭":"开启")+"】该商家，微信公众号模板消息推送功能？";
		parent.$.dialog.confirm(txt, function () {
			Public.ajaxPost(SITE_URL + "?ctl=Promotion_SellerWx&met=openWxtplmsg&id="+id+"&status="+((status>0)?0:1)+"&typ=json", {}, function (data) {
				if (data.status == 200){
					parent.Public.tips({content: __("操作成功！")});
					window.location.reload();
				} else {
					parent.Public.tips({type: 1, content: data.msg || "操作失败，请稍后重试！"});
				}
			});
		},
		function () {
			
		});
	}
};

$(function(){
    $source = $("#source").combo({
        data: [{
            id: "-1",
            name: "全部"
        },{
            id: "0",
            name: "不可用"
        },{
            id: "1",
            name: "可用"
        }],
        value: "id",
        text: "name",
        width: 110
    }).getCombo();

    THISPAGE.init();
});
