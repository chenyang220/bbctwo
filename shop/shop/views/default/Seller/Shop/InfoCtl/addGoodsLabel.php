    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link href="<?= $this->view->css ?>/seller.css?ver=<?=VER?>" rel="stylesheet">
    <link href="<?= $this->view->css ?>/iconfont/iconfont.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/ztree.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?=$this->view->js_com?>/jquery.js" charset="utf-8"></script>
    <link href="<?= $this->view->css ?>/base.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
	<script type="text/javascript">
		var BASE_URL = "<?=Yf_Registry::get('base_url')?>";
		var SITE_URL = "<?=Yf_Registry::get('url')?>";
		var INDEX_PAGE = "<?=Yf_Registry::get('index_page')?>";
		var STATIC_URL = "<?=Yf_Registry::get('static_url')?>";

		var DOMAIN = document.domain;
		var WDURL = "";
		var SCHEME = "default";
		try
		{
			//document.domain = 'ttt.com';
		} catch (e)
		{
		}

		var SYSTEM = SYSTEM || {};
		SYSTEM.skin = 'green';
		SYSTEM.isAdmin = true;
		SYSTEM.siExpired = false;
	</script>
<link href="<?= $this->view->css ?>/seller_center.css?ver=<?=VER?>" rel="stylesheet">
<div class="eject_con" id="eject_con">
  <form id="form" method="post" action="#" >
    <dl>
      <dt><?=__('商品标签：')?></dt>
      <dd>
         <select name="label_id" id="label_id_select">
              <option value=""><?= __('请选择') ?></option>
              <?php if (!empty($Label_Base)) { ?>
                  <?php foreach ($Label_Base as $key => $val) { ?>
                      <option value="<?= $val['id']; ?>" date-name="<?= $val['label_name']; ?>"><?= $val['label_name']; ?></option>
                  <?php } ?>
              <?php } ?>
          </select>
      </dd>
    </dl>
    <dl>
       <dt><?=__('已选商品标签：')?></dt>
       <dd id="select_label_name" class="select_cat_name">
       </dd>
    </dl>
    <div class="bottom">
          <label class="submit-border"><input type="submit" class="bbc_seller_submit_btns" value="<?=__('确定')?>" /></label>
    </div>
  </form>
</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.ui.js"></script>
<script type="text/javascript" src="<?=$this->view->js?>/seller.js"></script>

<link href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>

<link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css?ver=<?=VER?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.datetimepicker.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.toastr.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/upload/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/common.js" charset="utf-8"></script>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.combo.js"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.ztree.all.js"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/jquery.ztree.exhide.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com?>/plugins/jquery.cookie.js"></script>


<script>
  var categoryTree;
  var label_id_arr = {};
	$(function() {
    $('#label_id_select').css('height','30px');
		$('#label_id_select').change(function(){
      var id = $("select[name='label_id']").val();
      var name = $("select[name='label_id']  option:selected").html();
      // $.post(SITE_URL + '?ctl=Goods_Cat&met=getCat&typ=json', {cat_id:i}, function (data) {
      //   if ( data.status == 200 )
      //   {
      //      cat_name_select[i] = data.data.cat_name;
      //      var cat_name_keys = Object.keys(cat_name_select);
      //      if (cat_name_keys.length < 10) {
              var html =  $("#select_label_name").html();
      //         var cat_id_arr = '';
      //         for (cat_id in cat_name_select) {
      //           cat_id_arr = cat_id_arr + cat_id + ',';
                html += "<span>"+ name + "<a href='javascript:void(0)' onclick=del_label_name("+id+")>X</a></span>";
      //         }
              $("#select_label_name").html(html);

              label_id_arr[id] = name;
      //         $('#cat_id').val(cat_id_arr);
      //       } else {
      //         parent.Public.tips.error("<?=__('最多一次性申请9个')?>");
      //       }
      //   } else {
      //      parent.Public.tips.error(data.msg);
      //   }
      // });
		});
	});

  function del_label_name (id) {
     var html = '';
     delete label_id_arr[id];
     console.log(label_id_arr);
     for (label_id in label_id_arr) {
        html += "<span>"+ label_id_arr[label_id] + "<a href='javascript:void(0)' onclick=del_label_name("+label_id+")>X</a></span>";
     }
    $("#select_label_name").html(html);
    
    // $('#cat_id').val(cat_id_arr);
  }


  ///根据文本框的关键词输入情况自动匹配树内节点 进行模糊查找
    function AutoMatch(txtObj) {
                if (txtObj.value.length > 0) { 
                    var zTree = categoryTree.zTree;
                    console.log(zTree);
                    var nodeList = zTree.getNodesByParamFuzzy("name", txtObj.value); 
                    $.fn.zTree.init($("#cat_name"), setting, nodeList);
                    
                } else {
                      
                               
                }              
    }


</script>
<script type="text/javascript">
function refreshPage() 
{ 
    parent.location.reload();
} 

    
 $(document).ready(function(){
         var ajax_url = './index.php?ctl=Seller_Shop_Info&met=addcategoryrow&typ=json';
        $('#form').validator({
            ignore: ':hidden',
            theme: 'yellow_right',
            timely: 1,
            stopOnError: false,
            rules: {
              
            },
            fields: {
                 'entity[entity_name]': 'required',
                'entity[entity_xxaddr]':'required',
                'entity[entity_tel]':'tel'
            },
           valid:function(form){
                 var me = this;
                // 提交表单之前，hold住表单，防止重复提交
                me.holdSubmit();
                //表单验证通过，提交表单
                $.ajax({
                    url: ajax_url,
                    data:$("#form").serialize(),
                    success:function(a){
                        if(a.status == 200)
                        {
                           parent.Public.tips.success("<?=__('操作成功！')?>");
                            refreshPage();
                        }
                        else
                        {
                            if(a.msg !== 'failure')
                            {
                                parent.Public.tips.error(a.msg);
                            }else{
                                parent.Public.tips.error("<?=__('操作失败！')?>");
                            }

                            me.holdSubmit(false);
                        }
                    }
                });
            }

        });
    });



    

 
</script>
</script>
