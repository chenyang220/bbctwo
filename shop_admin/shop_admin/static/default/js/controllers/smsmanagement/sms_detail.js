
	var queryConditions = {},  
		hiddenAmount = false, 
		SYSTEM = system = parent.SYSTEM;

		if ( window.location.href.indexOf('met=virtualOrder') > -1 ) {
			queryConditions.action = 'virtual';
		}
	
	var THISPAGE = {
		init: function(data){
			if (SYSTEM.isAdmin === false && !SYSTEM.rights.AMOUNT_COSTAMOUNT) {
				hiddenAmount = true;
			};
			this.mod_PageConfig = Public.mod_PageConfig.init('other-income-list');//页面配置初始化
			this.loadGrid();            
			this.addEvent();
		},
		
		loadGrid: function(){
			var gridWH = Public.setGrid(), _self = this;
			
			var colModel = [
				{name:'member_name', label:'会员名', width:250, align:"center"},
				{name:'tomob', label:'发送号码',  width:100, align:"center"},
				{name:'content', label:'内容', width:300,align:'center'},
				{name:'add_time', label:'发送时间',  width:150, align:"center","formatter":timeType},
			];
			
			this.mod_PageConfig.gridReg('grid', colModel);
			colModel = this.mod_PageConfig.conf.grids['grid'].colModel;
			$("#grid").jqGrid({
				url:SITE_URL + '?ctl=SmsManagement_RemainingNum&met=smsDetail&typ=json',
				datatype: "json",
				autowidth: true,//如果为ture时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：setGridWidth
				height: gridWH.h,
				altRows: true, //设置隔行显示
				gridview: true,
				multiselect: true,
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
				forceFit:true,
				jsonReader: {
				  root: "data.items",
				  records: "data.records",
				  repeatitems : false,
				  total : "data.total",
				  id: "id",
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
			

			function timeType(val, opt, row)
			{
				var str = '';
				var myDate = new Date(val);

				if(!isNaN(myDate.getTime()))
				{
					str = val;
				}
				return str;
			}
		},
		
		//重新加载数据
		reloadData: function(data){
			$("#grid").jqGrid('setGridParam',{postData: data}).trigger("reloadGrid");
		},
		
		//增加事件
		addEvent: function()
		{
			var _self = this;					
			//刷新
			$("#btn-refresh").click(function ()
			{
				THISPAGE.reloadData(queryConditions);
				_self.$_searchName.placeholder('请输入相关数据...');
				_self.$_searchName.val('');
			});

			$(window).resize(function(){
				Public.resizeGrid();
			});
		}
	};
	
	$(function(){
		THISPAGE.init();
	});