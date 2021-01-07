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
        this.mod_PageConfig = Public.mod_PageConfig.init('man-song-list');//页面配置初始化
        this.initDom();
        this.loadGrid();
        this.addEvent();
    },
    initDom: function()
    {
        this.$_presale_name = $('#presale_name');
        this.$_shop_name = $('#shop_name');
        this.$_presale_name.placeholder();
        this.$_shop_name.placeholder();
    },
    loadGrid: function(){
        var gridWH = Public.setGrid(), _self = this;
        var colModel = [
            {name:'operating', label:'操作', width:90, fixed:true, formatter:operFmatter, align:"center"},
            {name:'presale_name', label:'活动名称', width:200, align:"center"},
            {name:'shop_name', label:'店铺名称', width:200, align:"center","formatter": handle.linkShopFormatter},
            {name:'presale_start_time', label:'定金开始时间',  width:150, align:"center"},
            {name:'presale_end_time', label:'定金结束时间',  width:150, align:"center"},
            {name:'presale_final_time', label:'尾款开始时间',  width:150, align:"center"},
            {name:'presale_deposit', label:'定金金额',  width:150, align:"center"},
            {name:'presale_lower_limit', label:'购买上限',  width:150, align:"center"},
            {name:'presale_state_label', label:'活动状态',  width:100, align:"center"}
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url: SITE_URL + '?ctl=Promotion_Presale&met=getPresaleList&typ=json',
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
                id: "presale_id"
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
            var html_con = '';
            if(row.presale_state > 0&&row.presale_state<=1)
            {
                html_con = '<div class="operating" data-id="' + row.presale_id + '"><span class="ui-icon ui-icon-search" title="活动详情"></span><span class="ui-icon ui-icon-close" title="取消活动"></span></div>';
            }else if(row.presale_state >= 2){
                html_con = '<div class="operating" data-id="' + row.presale_id + '"><span class="ui-icon ui-icon-search" title="活动详情"></div>';
            }
            else
            {
                html_con = '<div class="operating" data-dis="1" data-id="' + row.presale_id + '"><span class="ui-icon ui-icon-search" title="活动详情"></span><span class="ui-icon ui-icon-disabled ui-icon-close" title="取消活动"></span><span class="ui-icon ui-icon-pencil" title="审核"></span></div>';
            }

            return html_con;
        };
    },

    reloadData: function(data){
        $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
    },
    addEvent: function(){
        var _self = this;
        //编辑
        $('.grid-wrap').on('click', '.ui-icon-search', function(e){
            e.preventDefault();
            var e = $(this).parent().data("id");
            handle.operate("edit", e)
        });

        //审核
        $('.grid-wrap').on('click', '.ui-icon-pencil', function(e){
            e.preventDefault();
            var e = $(this).parent().data("id");
            handle.operate("shen", e)
        });

        //搜索
        $('#search').click(function()
        {
			queryConditions.presale_state = $source.getValue();
            queryConditions.presale_name = _self.$_presale_name.val();
            queryConditions.shop_name = _self.$_shop_name.val();
            THISPAGE.reloadData(queryConditions);
        });

        //刷新
        $("#btn-refresh").click(function ()
        {
            THISPAGE.reloadData('');
            _self.$_presale_name.val('');
            _self.$_shop_name.val('');
        });

        //删除
        $("#grid").on("click", ".operating .ui-icon-trash", function (t)
        {
            t.preventDefault();
            var e = $(this).parent().data("id");
            handle.del(e)
        });

        //取消
        $("#grid").on("click", ".operating .ui-icon-close", function (t)
        {
            if($(this).parent().attr('data-dis'))
            {
                return false;
            }
            else
            {
                t.preventDefault();
                var e = $(this).parent().data("id");
                handle.operate("cancel", e)
            }

        });

        //跳转到店铺认证信息页面
        $('#grid').on('click', '.to-shop', function(e) {
            e.stopPropagation();
            e.preventDefault();
            var shop_id = $(this).attr('data-id');
            $.dialog({
                title: '查看店铺信息',
                content: "url:"+SITE_URL + '?ctl=Shop_Manage&met=getShoplist&shop_id=' + shop_id,
                width: 1000,
                height: $(window).height(),
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            })
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
        if ("edit" == t)
        {
            var i = "预售活动详情", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback,id:e};
            console.info(a);
            $.dialog({
                title: i,
                content: "url:"+SITE_URL + '?ctl=Promotion_Presale&met=presaleGoodsList&presale_id='+ e,
                data:  {id:e},
                width:1030,
                // height: $(window).height() * 0.9,
                height:400,
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            })
        }
        else if("cancel" == t)//取消活动
        {
            $.dialog.confirm("活动取消后将不能恢复，请确认是否取消？", function ()
            {
                Public.ajaxPost(SITE_URL + '?ctl=Promotion_Presale&met=cancelpresale&typ=json', {presale_id: e}, function (d)
                {
                    if (d && 200 == d.status)
                    {
                        parent.Public.tips({content: "操作成功！"});

                        d.data['operating'] = '';
                        console.info(d.data);
                        $("#grid").jqGrid("setRowData", e , d.data);

                    }
                    else
                    {
                        parent.Public.tips({type: 1, content: "操作失败！" + d.msg})
                    }
                })
            })
        }else if("shen" == t)
        {
            var i = "预售活动审核", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback,id:e};
            $.dialog({
                title: i,
                content: "url:"+ SITE_URL + '?ctl=Promotion_Presale&met=presaleShen&typ=e&id=' + e,
                data: {id:e},
                width: 400,
                height:200,
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            })


        }
    }, callback: function (t, e, i,p)
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
    }, del: function (t)
    {
        $.dialog.confirm("删除的活动将不能恢复，请确认是否删除？", function ()
        {
            Public.ajaxPost(SITE_URL + '?ctl=Promotion_Presale&met=removePresaleActivity&typ=json', {presale_id: t}, function (e)
            {
                //alert(JSON.stringify(e));
                if (e && 200 == e.status)
                {
                    parent.Public.tips({content: "活动删除成功！"});
                    $("#grid").jqGrid("delRowData", t)
                }
                else
                {
                    parent.Public.tips({type: 1, content: "活动删除失败！" + e.msg})
                }
            })
        })
    }
};

$(function(){
    $source = $("#source").combo({
			data: [{
				id: "0",
				name: "活动状态"
			},{
				id: "1",
				name: "正常"
			},{
				id: "2",
				name: "已结束"
			}
			,{
				id: "3",
				name: "管理员关闭"
			}],
			value: "id",
			text: "name",
			width: 110
		}).getCombo();

    THISPAGE.init();

});