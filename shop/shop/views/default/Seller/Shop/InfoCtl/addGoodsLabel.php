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
  function unique(arr) {
      let newArr = [];
      arr.forEach(item => {
          return newArr.includes(item) ? '' : newArr.push(item);
      });
      return newArr;
  }
  var categoryTree;
  var api = frameElement.api;
  var callback = api.data.callback;
  // var label_id_arr = [];
  var label_id_arr = api.data.label_id_arr;
	$(function() {
      del_label_name();
      $('#label_id_select').css('height','30px');
  		$('#label_id_select').change(function(){
          var id = $("select[name='label_id']").val();
          var name = $("select[name='label_id']  option:selected").html();
          var html =  $("#select_label_name").html();
          if (!label_id_arr[id]) {
              html += "<span>"+ name + "<a href='javascript:void(0)' onclick=del_label_name("+id+")>X</a></span>";
              $("#select_label_name").html(html);
              label_id_arr[id] = name;
          }        
  		});
	});

  function del_label_name (id) {
     var html = '';
     if (id) {
      delete label_id_arr[id];
     }
     
     for (label_id in label_id_arr) {
        html += "<span>"+ label_id_arr[label_id] + "<a href='javascript:void(0)' onclick=del_label_name("+label_id+")>X</a></span>";
     }
    $("#select_label_name").html(html);
  }

api.button({
        id: "confirm", name: '<?= __('确定'); ?>', focus: !0, callback: function ()
        {
            
            callback(label_id_arr);
        }
    }, {id: "cancel", name: '<?= __('取消'); ?>'});
</script>

