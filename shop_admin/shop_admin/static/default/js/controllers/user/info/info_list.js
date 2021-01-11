var queryConditions = {},
    hiddenAmount = false,
    SYSTEM = system = parent.SYSTEM;
var userIds = [];
var THISPAGE = {
    init: function(data){
        if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
            hiddenAmount = true;
        };
        this.mod_PageConfig = Public.mod_PageConfig.init('user_list');//页面配置初始化
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
            {name:'operate', label:'操作', width:100, align:"center","formatter": operFmatter},
            {name:'user_id', label:'会员id', width:100, align:"center"},
            {name: 'user_name', label: '会员名称', width: 200, align: 'center'},
            {name: 'user_grade_con', label: '会员等级', width: 200, align: 'center'},
            {name:'user_tag_con', label:'会员标签', width:200,align:'center', "formatter": userTag},
            {name:'user_points', label:'会员积分', width:200,align:'center'},
            {name:'user_email', label:'会员邮箱', width:200,align:'center'},
            {name:'user_mobile', label:'会员手机', width:100, align:"center"},
            {name:'user_sex', label:'会员性别', width:100, align:"center"},
            {name:'user_realname', label:'真实姓名', width:100, align:"center"},
            {name:'user_birthday', label:'出生日期', width:150, align:"center","formatter":birthdayType},
            {name:'user_regtime', label:'注册时间', width:150, align:"center","formatter":timeType},
            {name:'shop_type', label:'商家类型', width:100, align:"center","formatter":shop_type},
            {name:'lastlogintime', label:'最后登录时间', width:150, align:"center","formatter":timeType}
           
        ];
        this.mod_PageConfig.gridReg('grid', colModel);
        colModel = this.mod_PageConfig.conf.grids['grid'].colModel;

        $("#grid").jqGrid({
            url:SITE_URL +  '?ctl=User_Info&met=getInfoList&typ=json',
            postData: queryConditions,
            datatype: "json",
            autowidth: true,//如果为ture时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：setGridWidth
            height:Public.setGrid().h,
            altRows: true, //设置隔行显示
            gridview: true,
            multiselect: true,
            multiboxonly: false,
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
            onSelectRow: function (rowid, status) {
                var index = $.inArray(rowid, userIds);
                if (status) {
                    if (index <= 0){
                        userIds.push(rowid);
                    }
                }else{
                    if (index >=0){
                        userIds.splice(index, 1);
                    }
                }
                console.log(userIds);
            },
            onSelectAll: function (rowids, status) {
                if (status){
                    userIds = rowids;
                } else{
                    userIds = [];
                }
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
        
        function shop_type(val, opt, row){
        	var str = '';
        	if(row.shop_type == 1 && row.shop_status == 3){
        		str = '商家店铺';
        	}else if(row.shop_type == 2 && row.shop_status == 3){
        		str = '供货商店铺';
        	}else{
                str = '无';
            }
        	return str;
        }
		
		/** 生日日期，显示过滤 @http://106.12.20.244:8191/index.php?m=bug&f=view&bugID=508 **/
		function birthdayType(val, opt, row) {
            var str = '';
			if(val == "1970-01-01" || val=="0000-00-00" || val ==""){
				return '暂未设置';
			}
            var myDate = new Date(val);
            if(!isNaN(myDate.getTime())){
                str = val;
            }
            return str;
        }
		
        function timeType(val, opt, row) {
            var str = '';
            var myDate = new Date(val);

            if(!isNaN(myDate.getTime())) {
                str = val;
            }
            return str;
        }

        function userTag(val, opt, row) {
            var str = "<span title='" + row.user_tag + "'>" + val + "</span>";
            return str;
        }
        //操作项格式化，适用于有“修改、删除”操作的表格
        function operFmatter(val, opt, row)
        {
            nav_str = '<span class="ui-icon-search ui-icon detail" title="编辑导航分类"></span>';
            var html_con = '<div class="operating" data-id="' + row.id + '">' + nav_str + '</div>';
            return html_con;
        }
    },
    reloadData: function(data){
        $("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
    },
    addEvent: function(){
        var _self = this;
		//添加
		$("#btn-add").click(function (t) {
			t.preventDefault();
			Business.verifyRight("INVLOCTION_ADD") && handle.add("id")
		});
		//查询
		 $('#search').click(function(){
            queryConditions.search_name = _self.$_searchName.val() === '请输入相关数据...' ? '' : _self.$_searchName.val();
            queryConditions.user_type = $source.getValue();
            queryConditions.shop_source = $shop_source.getValue();
            THISPAGE.reloadData(queryConditions);
         });
        //编辑
        $('.grid-wrap').on('click', '.detail', function(e){
            e.preventDefault();
            var e = $(this).parent().data("id");
            handle.operate("detail", e)
        });
        //打标签
        $("#btn-tag").click(function (e) {
            if (userIds.length <= 0) {
                window.Public.tips({type: 1,content: "请选择用户"});
                return
            }
            handle.batch("tag", userIds)
        });
        //送红包
        $("#btn-redpacket").click(function (e) {
            if (userIds.length <= 0) {
                window.Public.tips({type: 1,content: "请选择用户"});
                return
            }
            handle.batch("redpacket", userIds)
        });
        //修改积分
        $("#btn-score").click(function (e) {
            if (userIds.length <= 0) {
                window.Public.tips({type: 1,content: "请选择用户"});
                return
            }
            handle.batch("score", e)
        });
        //修改会员等级
        $("#btn-level").click(function (e) {
            if (userIds.length <= 0) {
                window.Public.tips({type: 1,content: "请选择用户"});
                return
            }
            handle.batch("level", e)
        });
        //导出
        $("#btn-excel").click(function ()
        {
            queryConditions.limit = $('.ui-pg-selbox').find('option:selected').val();
            var limit_page = $(".ui-paging-info").html().split(' ');
            queryConditions.start_limit = Number(limit_page[0]) - 1;
            $.dialog({
                title: '会员信息导出',
                content: "url:"+SITE_URL + '?ctl=User_Info&met=exportInfo',
                data: queryConditions,
                width: 600,
                height: $(window).height()*0.4,
                max: !1,
                min: !1,
                cache: !1,
                lock: !0
            })
        });
        $("#btn-refresh").click(function () {
            THISPAGE.reloadData(queryConditions);
            _self.$_searchName.val('请输入相关数据...');
        });

        $(window).resize(function(){
            Public.resizeGrid();
        });
    }
};
var handle = {
	operate: function (t, e)
    {
		var i = "查看会员信息详情", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};
        $.dialog({
            title: i,
            content: "url:"+SITE_URL + '?ctl=User_Info&met=infoDetail&user_id=' + e,
            data: a,
            width: 600,
            height: $(window).height()*0.9,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
       
    },add: function (t, e)
    {
		var i = "增加会员信息", a = {oper: t, rowData: $("#grid").jqGrid('getRowData',e), callback: this.callback};
        $.dialog({
            title: i,
            content: "url:"+SITE_URL+'?ctl=User_Info&met=addInfo',
            data: a,
            width: 600,
            height: $(window).height()*0.9,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
       
    }, batch:function(t,e){//批量操作
	    switch (t) {
            case 'tag':
                var i = "选择标签,已选" + userIds.length + "位会员";
                var width = 600;
                var height = $(window).height() * 0.9;
                var url = "url:" + SITE_URL + '?ctl=User_Info&met=tag';
                break;
            case 'redpacket':
                var i = "送红包,已选" + userIds.length + "位会员";
                var width = 1000;
                var height = $(window).height() * 0.9;
                var url = "url:" + SITE_URL + '?ctl=User_Info&met=redpacket';
                break;
            case 'score':
                var i = "批量修改积分,已选" + userIds.length + "位会员";
                var width = 600;
                var height = $(window).height() * 0.3;
                var url = "url:" + SITE_URL + '?ctl=User_Info&met=score';
                break;
            case 'level':
                var i = "修改会员等级,已选" + userIds.length + "位会员";
                var width = 500;
                var height = $(window).height() * 0.1;
                var url = "url:" + SITE_URL + '?ctl=User_Info&met=level';
                break;
        }
        var a = {oper: t ,rowData: userIds, callback: this.back};
        $.dialog({
            title: i,
            content: url,
            data: a,
            width: width,
            height: height,
            max: !1,
            min: !1,
            cache: !1,
            lock: !0
        })
    }, callback: function (t, e, i) {
        var a = $("#grid").data("gridData");
        if (!a) {
            a = {};
            $("#grid").data("gridData", a)
        }
        a[t.id] = t;
        i && i.api.close();
		$("#grid").trigger("reloadGrid");
    }, back: function (t, e, i) {
        i && i.api.close();
        userIds = [];
        $("#grid").trigger("reloadGrid");
    }
 
};
$(function(){
  $source = $("#source").combo({
        data: [{
            id: "1",
            name: "会员id"
        },{
            id: "2",
            name: "会员名称"
        }],
        value: "id",
        text: "name",
        width: 110
    }).getCombo();

	$shop_source = $("#shop_source").combo({
		data:[{
			id:"0",
			name:"商家类型"
		},
		{
			id:"1",
			name:"商家店铺"
		},{
			id:"2",
			name:"供货商店铺"
		}],
		value:"id",
		text:"name",
		width:120
	}).getCombo();

    Public.pageTab();

    THISPAGE.init();
    
});
