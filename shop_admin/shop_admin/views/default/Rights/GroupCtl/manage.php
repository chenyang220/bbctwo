<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<?php
include TPL_PATH . '/'  . 'header.php';
?>

</head>

<body class="<?=$skin?>">
<div class="wrapper page pt-10 overflow-inherit">
  <div class="mod-toolbar-top cf">
    <div class="fl"><h3 class="f14"><?= __('详细权限设置'); ?><span class="fwn"><?= __('（请勾选为'); ?> <b style='display:none;' id="rightsGroupName"></b>  <input type="text" value="<?=$rights_group_name?>" class="ui-input" name="number" id="rights_group_name"> <?= __('分配的权限）'); ?></span></h3></div>
    <div class="fr"><a id="all" class="ui-btn ui-btn-sp"><?= __('全选'); ?></a><a class="ui-btn ui-btn-sp" id="save"><?= __('确定'); ?></a><a class="ui-btn" href="./index.php?ctl=Rights_Group&met=index"><?= __('返回'); ?></a></div>
  </div>
  <!-- 新增begin-->
  <div class="module-tree-content">
  	<?php foreach( $rights_rows_new as $k => $v) { ?>
	  <dl class="big" id="<?=$v['rights_id']?>">
		<dt><label><input <?php if($v['fright']==1){ echo "checked";}?> class="one" value="<?=$v['rights_id']?>" type="checkbox"><em><?=$v['rights_name']?></em></label></dt><!-- 一级 -->
		<dd>
			<?php foreach( $v['child'] as $kk=>$vv){ ?>
			<p><label><input <?php if($vv['fright']==1){ echo "checked";}?> parent-id="<?=$vv['rights_parent_id']?>" class="two" value="<?=$vv['rights_id']?>" type="checkbox"><em><?=$vv['rights_name']?></em></label></p><!-- 二级 -->
			<div class="lab-items">
				<p>
					<?php $i = 0; ?>
					<?php foreach ($vv['children'] as $kkk=>$vvv){ ?>
					<label><input <?php if($vvv['fright']==1){ echo "checked";}?> parent-id="<?=$vvv['rights_parent_id']?>" value="<?=$vvv['rights_id']?>" class="three" type="checkbox"><em><?=$vvv['rights_name']?></em></label>
					<?php 
						$i++; 
						if($i%5==0){
							echo "</p><p>";
						}
					?>
					
					<?php }?>
				</p>
			</div>
			<?php }?>
		</dd>
	  </dl>
	  <?php }?>
  </div>
  <div class="module-fast-nav">
	<h4>快捷导航</h4>
	<p>
		<?php foreach($rights_rows_new as $key=>$val){ ?>
		<a href="#<?=$val['rights_id']?>"><?=$val['rights_name']?></a>
		<?php }?>
	</p>
  </div>
  <!-- 新增end-->
  <!-- <div class="grid-wrap">
    <table id="grid">
    </table>
    <div id="page"></div>
  </div> -->
</div>
<!-- <script>
  var urlParam = Public.urlParam(), rightsGroupName = urlParam.rights_group_name, rightsGroupId = urlParam.rights_group_id, curGroup;
  var height = Public.setGrid().h;
  var relation = {
			"<?= __('购货单'); ?>":[{name:'<?= __('商品'); ?>',rights:['<?= __('查询'); ?>']},{name:'<?= __('仓库'); ?>',rights:['<?= __('查询'); ?>']},{name:'<?= __('供应商'); ?>',rights:['<?= __('查询'); ?>']}],
			"<?= __('销货单'); ?>":[{name:'<?= __('商品'); ?>',rights:['<?= __('查询'); ?>']},{name:'<?= __('仓库'); ?>',rights:['<?= __('查询'); ?>']},{name:'<?= __('客户'); ?>',rights:['<?= __('查询'); ?>']}],
			"<?= __('调拨单'); ?>":[{name:'<?= __('商品'); ?>',rights:['<?= __('查询'); ?>']},{name:'<?= __('仓库'); ?>',rights:['<?= __('查询'); ?>']}]
	},
	$grid = $('#grid'),
	RelationalMapping = {};//Rowid<?= __('与名字的映射'); ?>
  $('#rightsGroupName').text(rightsGroupName);
  $('#rights_group_name').val(rightsGroupName);
  if(rightsGroupName)
  {
  	$("#rights_group_name").attr("disabled","disabled");
  }

  $("#grid").jqGrid({
	  url:'./index.php?ctl=Rights_Group&met=get&typ=json&action=info&rightsGroupName=' + rightsGroupName + '&rights_group_id=' + rightsGroupId,
	  datatype: "json",
	  //caption: "<?= __('科目余额表'); ?>",
	  autowidth: true,//如果为'); ?>ture<?= __('时，则当表格在首次被创建时会根据父元素比例重新调整表格宽度。如果父元素宽度改变，为了使表格宽度能够自动调整则需要实现函数：'); ?>setGridWidth
	  //width: width,
	  height: height,
	  altRows: true, //设置隔行显示'); ?>
	  //rownumbers: true,//如果为'); ?>ture<?= __('则会在表格左边新增一列，显示行顺序号，从'); ?>1<?= __('开始递增。此列名为'); ?>'rn'
	  //gridview: true,
	  colNames:['<input type="checkbox" id="all" class="vm">', '<?= __('功能列表'); ?>', '<?= __('操作'); ?>', '<label for="all"><?= __('授权'); ?></label>'],
	  colModel:[
	  	  {name:'fobjectid', width:40, align:"center", formatter:groupFmatter},
		  {name:'fobject', width:200, formatter:moduleFmatter,align:"center"},
		  {name:'faction', width:150, align:"center"},
		  {name:'fright', width:100, align:"center", formatter:rightFmatter}
	  ],
	  cmTemplate: {sortable: false, title: false},
	  //idPrefix: 'ys',
	  //loadui: 'block',
	  //multiselect: true,
	  //multiboxonly: true,
	  page: 1,
	  sortname: 'number',
	  sortorder: "desc",
	  pager: "#page",
	  rowNum: 2000,
	  rowList:[300,500,1000],
	  scroll: 1, //创建一个动态滚动的表格，当为'); ?>true<?= __('时，翻页栏被禁用，使用垂直滚动条加载数据，且在首次访问服务器端时将加载所有数据到客户端。当此参数为数字时，表格只控制可见的几行，所有数据都在这几行中加载'); ?>
	  loadonce: true,
	  viewrecords: true,
	  shrinkToFit: false,
	  forceFit: false,
	  jsonReader: {
		root: "data.items",
		records: "data.totalsize",
		repeatitems : false,
		id: -1
	  },
	  afterInsertRow: function(rowid, rowdata, rowelem) {

	  },
	  loadComplete: function(data) {
	  	$('.group').each(function(index, element) {
			 var groupId = $(this).attr('id');
			 var $_ckbox = $('.ckbox[data-for=' + groupId + ']');
			 if($_ckbox.length === $_ckbox.filter(':checked').length) {
				this.checked = true;
			 };
        });
	  	initRelation();
	  },
	  loadError: function(xhr,st,err) {

	  }
  });

  function groupFmatter(val, opt, row){
	if(curGroup !== val){
		return '<input class="group" type="checkbox" id="'  + val + '">';
	} else {
		return '';
	};
  };
  function moduleFmatter(val, opt, row){
	fillMap(val, opt ,row);//缓存映射关系'); ?>
	if(curGroup !== row.fobjectid){
		curGroup = row.fobjectid;
		return val;
	} else {
		return '';
	};
  };

  function rightFmatter(val, opt, row){
	var html_str = '<input type="checkbox" class="ckbox" data-for="' + row.fobjectid + '" data-id="' + row.frightid + '"';
	if(row.faction === '<?= __('查询'); ?>') {
		html_str = html_str + 'data-view="true"';
	};
	if(val > 0){
		return html_str + ' checked="checked">';
	} else {
		return html_str + '>';
	};
  };

  $('#all').click(function(e){
	  e.stopPropagation();
	  if(this.checked) {
		$('.ckbox').each(function(){
			this.checked = true;
		});
		$('.group').each(function(){
			this.checked = true;
		});
	  } else {
		 $('.ckbox').removeAttr('checked');
		 $('.group').removeAttr('checked');
	  }
  });
  $('#save').click(function(e){
	  
	  var group_name = $('#rights_group_name').val();
	  if(group_name=='')
	  {
		   parent.Public.tips({type: 1, content : '<?= __('请填写权限组名称！'); ?>'});
		   return false;
	  }
	  
	  var items = [];
	  $('.ckbox').each(function(i){
		  if(this.checked) {
			 items.push($(this).data('id'));
	      }
	  });
	  
	  if(items=='')
	  {
		   parent.Public.tips({type: 1, content : '<?= __('请选择权限！'); ?>'});
		   return false;
	  }

		var oper = 'add';
	  if (rightsGroupId)
	  {
		oper = 'edit';
	  }
	  Public.ajaxPost('./index.php?ctl=Rights_Group&typ=json&met=' + oper + '&rights_group_name=' + encodeURIComponent($('#rights_group_name').val()) + '&rights_group_id=' + rightsGroupId + '&rightid={"rightids":['+ items.join(',') + ']}', {}, function(data){
		  if(data.status === 200) {
			  parent.Public.tips({content : '<?= __('保存成功！'); ?>'});
		  } else {
			  parent.Public.tips({type: 1, content : data.msg});
		  }
	  });
  });
  $('.grid-wrap').on('click', '.group', function(){
	 var groupId = $(this).attr('id');
	 if(this.checked) {
		$('.ckbox[data-for=' + groupId + ']').each(function(){
			this.checked = true;
		});
	 } else {
		$('.ckbox[data-for=' + groupId + ']').removeAttr('checked');
	 };
	 $(this).closest('tr').find('input').filter('[data-view=true]').trigger('checkChange');
  });
  $('.grid-wrap').on('click', '.ckbox', function(){
	 var groupId = $(this).data('for');
	 var $_group = $('.ckbox[data-for=' + groupId + ']'), $_view = $_group.filter('[data-view=true]'), $_others = $_group.not('[data-view=true]');
	 if(!$(this).data('view')) {
		if(this.checked && $_view.length > 0) {
	 		$_view[0].checked = true;
		};
	 } else {
	 	if($_others.length > 0 && $_others.filter(':checked').length > 0) {
			this.checked = true;
		};
	 };
	 $_view.trigger('checkChange');
	 if($_group.length === $_group.filter(':checked').length) {
		$('#' + groupId)[0].checked = true;
	 } else {
		$('#' + groupId).removeAttr('checked');
	 };
  });
/**
 * <?= __('关联权限处理'); ?>
 */
 function fillMap(val, opt ,row){
		RelationalMapping[val+"-"+row.faction] = opt.rowId;
}
 function initRelation(){
	 $grid.find('input').filter('[data-view=true]').each(function(){
		setRelativeRights($(this));
	});
 };
 function setRelativeRights(view){
	 var _modelName = view.closest('tr').find('td:eq(1)').html();
	 if(relation[_modelName]){
		 view.on('checkChange',function(){
			 var _arr = relation[_modelName];
			 var _isChecked = this.checked;
			 for(var i = 0,len = _arr.length; i < len; i++){
				 var _name = _arr[i].name;
				 var _rights = _arr[i].rights;
				 for(var j=0,l = _rights.length; j<l; j++){
					 var _proName = _arr[i].name+"-"+_rights[j];
					 var _rid = RelationalMapping[_proName];
					 if(!_arr[i].ckbox){
						 _arr[i].ckbox = {};
					 }
					 if(!_arr[i].ckbox[_proName]){
					 	_arr[i].ckbox[_proName] = $('#'+_rid).find('.ckbox')[0];//缓存当前对象'); ?>
					 }
					 if(_isChecked){
						 //如果主权限获得，则做以下处理'); ?>
						 _arr[i].ckbox[_proName].checked = true;
					 }
					 else{
						 //如果主权限取消，则做以下处理'); ?>
					 }
				 }
			 }
			 this.checked = _isChecked;
		 });
	 }
}
</script> -->
<script type="text/javascript">
	//全选、取消全选
	$('#all').click(function(e){

	  var text = $(this).text();
	  if(text=='全选') {
		$('.one').each(function(){
			this.checked = true;
		});
		$('.two').each(function(){
			this.checked = true;
		});
		$('.three').each(function(){
			this.checked = true;
		});
		$(this).text('取消全选');
		$(this).addClass('active');
	  } else {
		$('.one').each(function(){
			this.checked = false;
		});
		$('.two').each(function(){
			this.checked = false;
		});
		$('.three').each(function(){
			this.checked = false;
		});
		$(this).text('全选');
		$(this).removeClass('active');
	  }
    });


    //一级
    $('.one').click(function(e){
    
    	var id = $(this).val();
    	if(this.checked){
    		$(this).parents('#'+id).find('.two').each(function(){
    			this.checked = true;
    		})
    		
    		$(this).parents('#'+id).find('.three').each(function(){
    			this.checked = true;
    		})
    	}else{
    		$(this).parents('#'+id).find('.two').each(function(){
    			this.checked = false;
    		})
    		$(this).parents('#'+id).find('.three').each(function(){
    			this.checked = false;
    		})
    	}
    })
    //二级
    $('.two').click(function(e){
    	var id = $(this).attr('parent-id');
    	if(this.checked){
    		$(this).parent().parent().next().find('.three').each(function(){
    			this.checked = true;
    		})
    	}else{
    		$(this).parent().parent().next().find('.three').each(function(){
    			this.checked = false;
    		})
    	}
    	var a = true;
    	$('#'+id).find('.two').each(function(){
    		 if(!this.checked){
    		 	a = false;
    		 }
    	})
    
    	if(!a){
    		$(this).parents('#'+id).find('.one').each(function(){
    			this.checked = false;
    		});
    	}else{
    		$(this).parents('#'+id).find('.one').each(function(){
    			this.checked = true;
    		});
    	}
    })



    //三级
    $('.three').click(function(e){
    
        
    	var a = true;
    	$(this).parents('.big').find('.three').each(function(){
    		 if(!this.checked){
    		 	a = false;
    		 }
    	})
    
    	if(!a){
    		$(this).parents('.big').find('.one').each(function(){
    			this.checked = false;
    		});
    		$(this).parent().parent().prev().find('.two').each(function(){
    			this.checked = false;
    		});
    	}else{
    		$(this).parents('.big').find('.one').each(function(){
    			this.checked = true;
    		});
    		$(this).parent().parent().prev().find('.two').each(function(){
    			this.checked = true;
    		});
    	}
    })
    
    
    
    
    
    
    
    
    
    //确定

	 $('#save').click(function(e){
	  var rightsGroupId = "<?=$rights_group_id?>"
	  var group_name = $('#rights_group_name').val();
	  if(group_name=='')
	  {
		   parent.Public.tips({type: 1, content : '<?= __('请填写权限组名称！'); ?>'});
		   return false;
	  }
	  
	  var items = [];
	  $('.one').each(function(i){
		  if(this.checked) {
			 items.push($(this).val());
	      }
	  });
	  
	  $('.two').each(function(i){
		  if(this.checked) {
			 items.push($(this).val());
	      }
	  });
	  
	  $('.three').each(function(i){
		  if(this.checked) {
			 items.push($(this).val());
	      }
	  });
	  
	  if(items=='')
	  {
		   parent.Public.tips({type: 1, content : '<?= __('请选择权限！'); ?>'});
		   return false;
	  }

		var oper = 'add';
	  if (rightsGroupId)
	  {
		oper = 'edit';
	  }
	  Public.ajaxPost('./index.php?ctl=Rights_Group&typ=json&met=' + oper + '&rights_group_name=' + encodeURIComponent($('#rights_group_name').val()) + '&rights_group_id=' + rightsGroupId + '&rightid={"rightids":['+ items.join(',') + ']}', {}, function(data){
		  if(data.status === 200) {
			  parent.Public.tips({content : '<?= __('保存成功！'); ?>'});
		  } else {
			  parent.Public.tips({type: 1, content : data.msg});
		  }
	  });
  });


</script>
<?php
include TPL_PATH . '/'  . 'footer.php';
?>