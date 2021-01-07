<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
</head>
<style>
	.col60{color: #F60;}
</style>
<body class="<?=$skin?>">
        <form method="post" enctype="multipart/form-data" id="shop_edit_level" name="form1">
            <?php foreach ($data as $key => $value) {
                
            ?>
            <input  id="shop_grade_id" value="<?=$value["shop_grade_id"]?>"  type="hidden"/>
        <div class="ncap-form-default">
              <dl class="row">
                <dt class="tit">
                    <label for="shop_grade_name"><em class="col60">* </em><?= __('等级名称'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="shop_grade_name" name="shop_grade_name"  value="<?=$value["shop_grade_name"]?>" class="ui-input w200" type="text"/>
                </dd>
              </dl>
             <dl class="row">
                <dt class="tit">
                    <label for="shop_grade_goods_limit"><?= __('可发布商品数'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="shop_grade_goods_limit" name="shop_grade_goods_limit" value="<?=$value["shop_grade_goods_limit"]?>" class="ui-input w200" type="text"/>
                     <span class="err"></span>
                     <p class="notic">0<?= __('表示没有限制'); ?></p>
                </dd>
              
              </dl>
             <dl class="row">
                <dt class="tit">
                    <label for="shop_grade_album_limit"><?= __('可上传图片数'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="shop_grade_album_limit" name="shop_grade_album_limit" value="<?=$value["shop_grade_album_limit"]?>" class="ui-input w200" type="text"/>
                     <span class="err"></span>
                     <p class="notic">0<?= __('表示没有限制'); ?></p>
                </dd>
              
              </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="retain_domain"><?= __('可选模板套数'); ?></label>
                </dt>
                <dd class="opt">
                   (<?= __('在店铺等级列表设置'); ?>)
                </dd>
            </dl>
            
             <dl class="row">
                <dt class="tit">
                    <label for="shop_grade_album_limit"><?= __('可用附加功能'); ?></label>
                </dt>
                <dd class="opt">
                <div class="onoff">
                        <label for="shop_grade_function_id1" class="cb-enable <?=($value['shop_grade_function_id'] ? 'selected' : '')?> "><?= __('开启'); ?></label>
                        <label for="shop_grade_function_id0" class="cb-disable <?=(!$value['shop_grade_function_id'] ? 'selected' : '')?>"><?= __('关闭'); ?></label>
                        <input id="shop_grade_function_id1"  name ="shop_grade_function_id" <?=($value['shop_grade_function_id'] ? 'checked' : '')?>  value="1" type="radio">
                        <input id="shop_grade_function_id0"  name ="shop_grade_function_id"  <?=(!$value['shop_grade_function_id'] ? 'checked' : '')?>   value="0" type="radio">
                       
                    </div>
                 </dd>
              </dl>
             <dl class="row">
                <dt class="tit">
                    <label for="shop_grade_fee"><em class="col60">* </em><?= __('收费标准'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="shop_grade_fee" name="shop_grade_fee"  value="<?=$value["shop_grade_fee"]?>" class="ui-input w200" type="text"/>
                </dd>
          
            </dl>
            
             <dl class="row">
                <dt class="tit">
                    <label for="shop_grade_desc"><?= __('申请说明'); ?></label>
                </dt>
                <dd class="opt">
                    <textarea style="width:200px;height: 73px;" rows="6" class="tarea" id="shop_grade_desc" ><?=$value["shop_grade_desc"]?></textarea>
                </dd>
            </dl>
             <dl class="row">
                <dt class="tit">
                    <label for="shop_grade_sort"><em class="col60">* </em><?= __('级别'); ?></label>
                </dt>
                <dd class="opt">
                    <select name="shop_grade_sort">
                        <!-- <?= __('写死，就'); ?>TM<?= __('五级'); ?> -->
                        <option value="1" <?php echo  $value["shop_grade_sort"] == 1 ? " selected='selected' " : '';  ?>><?= __('一级'); ?></option>
                        <option value="2" <?php echo  $value["shop_grade_sort"] == 2 ? " selected='selected' " : '';  ?>><?= __('二级'); ?></option>
                        <option value="3" <?php echo  $value["shop_grade_sort"] == 3 ? " selected='selected' " : '';  ?>><?= __('三级'); ?></option>
                        <option value="4" <?php echo  $value["shop_grade_sort"] == 4 ? " selected='selected' " : '';  ?>><?= __('四级'); ?></option>
                        <option value="5" <?php echo  $value["shop_grade_sort"] == 5 ? " selected='selected' " : '';  ?>><?= __('五级'); ?></option>
                    </select>
                </dd>
          
            </dl>
          
          
        </div>
            <?php }?>
    </form>

    <script>

function initPopBtns()
{
    var t = "add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"] : ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
    api.button({
        id: "confirm", name: t[0], focus: !0, callback: function ()
        {
            
            postData(oper, rowData.shop_grade_id);
           return cancleGridEdit(),$("#shop_edit_level").trigger("validate"), !1
        }
    }, {id: "cancel", name: t[1]})
}
function postData(t, e)
{
 
	$_form.validator({
           
           messages: {
                    required: "<?= __('请填写该字段'); ?>",
           },
            fields: {
                'shop_grade_name':'required;' ,
                'shop_grade_goods_limit':'integer[+0];' ,
                'shop_grade_album_limit':'integer[+0];' ,
                'shop_grade_fee':'required;integer[+0];' ,
                'shop_grade_sort':'required;integer[+0];' 
            },

        valid: function (form)
        {
            var shop_grade_name = $.trim($("#shop_grade_name").val()), 
            shop_grade_goods_limit = $.trim($("#shop_grade_goods_limit").val()), 
            shop_grade_album_limit = $.trim($("#shop_grade_album_limit").val()), 
            shop_grade_fee = $.trim($("#shop_grade_fee").val()), 
            shop_grade_desc = $.trim($("#shop_grade_desc").val()), 
            shop_grade_function_id = $.trim($("input[name='shop_grade_function_id']:checked").val()),
            shop_grade_sort = $.trim($("#shop_grade_sort").val()), 
            shop_grade_id = $.trim($("#shop_grade_id").val()), 
    
			n = "add" == t ? "<?= __('新增等级'); ?>" : "<?= __('修改等级'); ?>";
			params = rowData.shop_grade_id ? {
				shop_grade_id: e, 
				shop_grade_name: shop_grade_name, 
				shop_grade_goods_limit: shop_grade_goods_limit,
                                shop_grade_album_limit:shop_grade_album_limit,
                                shop_grade_fee:shop_grade_fee,
                                shop_grade_desc:shop_grade_desc,
                                shop_grade_function_id:shop_grade_function_id,
                                shop_grade_sort:shop_grade_sort,
			} : {
                shop_grade_id: shop_grade_id,
                shop_grade_name: shop_grade_name,
				shop_grade_goods_limit: shop_grade_goods_limit,
                                shop_grade_album_limit:shop_grade_album_limit,
                                shop_grade_fee:shop_grade_fee,
                                shop_grade_desc:shop_grade_desc,
                                shop_grade_function_id:shop_grade_function_id,
                                shop_grade_sort:shop_grade_sort,
			};
			Public.ajaxPost(SITE_URL +"?ctl=Shop_Grade&met=" + ("add" == t ? "add" : "edit")+ "ShopLevelrow&typ=json", params, function (e)
			{
				if (200 == e.status)
				{
					parent.parent.Public.tips({content: n + "<?= __('成功！'); ?>"});
					callback && "function" == typeof callback && callback(e.data, t, window)
				}
				else
				{
					parent.parent.Public.tips({type: 1, content: n + "<?= __('失败！'); ?>" + e.msg})
				}
			})
        },
        ignore: ":hidden",
        theme: "yellow_bottom",
        timely: 1,
        stopOnError: !0
    });
}
function cancleGridEdit()
{
    null !== curRow && null !== curCol && ($grid.jqGrid("saveCell", curRow, curCol), curRow = null, curCol = null)
}
function resetForm(t)
{
    $_form.validate().resetForm();
    $("#shop_grade_name").val("");
    $("#shop_grade_goods_limit").val("");
    $("#shop_grade_album_limit").val("");
    $("#shop_grade_fee").val("");
    $("#shop_grade_desc").val("");
    $("#shop_grade_sort").val("");
    $("input[name='shop_grade_function_id']:checked").val("");
			
}
var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#shop_edit_level"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
//console.log(rowData);
initPopBtns();

    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
