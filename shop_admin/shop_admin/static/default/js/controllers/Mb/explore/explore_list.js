var queryConditions = {},
    hiddenAmount = false,
    SYSTEM = system = parent.SYSTEM;
var THISPAGE = {
    init: function(data){
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        };
        this.mod_PageConfig = Public.mod_PageConfig.init('explore_list');//页面配置初始化
        this.initDom();
        this.loadGrid();
        this.addEvent();
    },
    initDom: function(){
        this.$_searchName = $('#exploreName');
		this.$_searchName.placeholder();

        this.$_exploreTitle = $('#exploreTitle');
        this.$_exploreTitle.placeholder();
    },
    loadGrid: function(){
        var gridWH = Public.setGrid(), _self = this;
        var colModel = [
            {name:'explorename', label:'举报人', width:200, align:"center"},
            {name:'explore_title', label:'举报标题', width:300,align:'center'},
            {name:'report_reason', label:'举报原因', width:350,align:'center'},
            {name:'report_time', label:'举报时间', width:150, align:"center"},
            {name:'reportmsg', label:'处理状态', width:100, align:"center"},
            {name:'operating', label: '操作', width: 100, fixed: true, formatter: operFmatter, align: "center"},

        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
        $("#grid").jqGrid({
            url:SITE_URL +  '?ctl=Mb_Explore&met=getExploreList&typ=json',
            postData: queryConditions,
            datatype: "json",
            autowidth: true,//如果为ture时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：setGridWidth
            height:Public.setGrid().h,
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
            var html_con;
            if (row.report_status > 0) {
                html_con = '<div class="operating" data-id="' + row.id+ '"><span class="ui-icon ui-icon-search" title="查看"></span></div>';
            }else{
                html_con = '<div class="operating" data-id="' + row.id+ '"><span class="ui-icon ui-icon-pencil" title="处理"></span></div>';
            }

            return html_con;
        };

    },
    reloadData: function(data){
        $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
    },
    addEvent: function(){
        var _self = this;
		//查询
		 $('#search').click(function(){
            queryConditions.exploreName = _self.$_searchName.val() === '举报人...' ? '' : _self.$_searchName.val();
            queryConditions.exploreTitle = _self.$_exploreTitle.val() === '被举报心得标题...' ? '' : _self.$_exploreTitle.val();
            queryConditions.reason = $reason.getValue();
            queryConditions.status = $status.getValue();
            THISPAGE.reloadData(queryConditions);
         });
        //编辑
        $('.grid-wrap').on('click', '.ui-icon-pencil', function(e){
            e.preventDefault();
            var e = $(this).parent().data("id");

            handle.operate("edit", e)
        });
        //查看
        $('.grid-wrap').on('click', '.ui-icon-search', function(e){
            e.preventDefault();
            var e = $(this).parent().data("id");

            var i = "查看举报信息详情", a = {oper: 'info', rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};
            $.dialog({
                title: i,
                content: "url:"+ SITE_URL + '?ctl=Mb_Explore&met=exploreinfo&report_id=' + e,
                data: a,
                width: 950,
                height:$(window).height(),
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            });
        });



        //删除-暂时无删除功能需求、2018.11.21
        $("#grid").on("click", ".operating .ui-icon-trash", function (t)
        {
            t.preventDefault();
            if (Business.verifyRight("INVLOCTION_DELETE"))
            {
                var e = $(this).parent().data("id");
                handle.del(e)
            }
        });

        $("#btn-refresh").click(function ()
        {
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
		var i = "处理举报信息", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};
        $.dialog({
            title: i,
            content: "url:"+SITE_URL + '?ctl=Mb_Explore&met=editexplore&report_id=' + e,
            data: a,
            width: 950,
            height: $(window).height()*0.9,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0,
        })

    },
    add: function (t, e)
    {
		var i = "添加举报信息", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};
        $.dialog({
            title: i,
            content: "url:"+SITE_URL+'?ctl=Mb_Explore&met=addexploreo',
            data: a,
            width: 950,
            height: $(window).height()*0.9,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })

    },
    del: function (t)
    {
        $.dialog.confirm("删除的举报信息将不能恢复，请确认是否删除？", function ()
        {
            Public.ajaxPost(SITE_URL+"?ctl=Mb_Explore&met=delExplore&typ=json", {report_id: t}, function (e)
            {
                if (e && 200 == e.status)
                {
                    parent.Public.tips({content: "举报信息删除成功！"});
                    $("#grid").jqGrid("delRowData", t)
                }
                else
                {
                    parent.Public.tips({type: 1, content: "举报信息删除失败！" + e.msg})
                }
            })
        })
    },
    callback: function (t, e, i)
    {
        var a = $("#grid").data("gridData");
        if (!a)
        {
            a = {};
            $("#grid").data("gridData", a)
        }
        a[t.id] = t;
        i && i.api.close();
		$("#grid").trigger("reloadGrid");

    }

};
$(function(){
  $reason = $("#reason").combo({
        data: [
        {
            id: "-1",
            name: "举报原因"
        },
        {
            id: "0",
            name: "其他"
        },
        {
            id: "1",
            name: "色情、政治等敏感信息"
        },
        {
            id: "2",
            name: "广告信息或骚扰用户"
        },
        {
            id: "3",
            name: "侵权盗用行为"
        }
        ],
        value: "id",
        text: "name",
        width: 110
    }).getCombo();

	$status = $("#status").combo({
		data:[
        {
			id:"0",
			name:"处理状态"
		},
		{
			id:"1",
			name:"未处理"
		},
        {
			id:"2",
			name:"已处理"
		}
        ],
		value:"id",
		text:"name",
		width:120
	}).getCombo();

    Public.pageTab();

    THISPAGE.init();

});
