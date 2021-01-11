<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/intlTelInput.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/intlTelInput.js" charset="utf-8"></script>
<style type="text/css">
    .hidden{display:none;}
    .area_in_sub{height:30px;line-height: 30px;margin-bottom: 10px;}
    .area_in_sub .second{width:95%;display:inline-block;}
    .area_in_sub .second select{width:130px;height:30px;}
    i{color:red;}
</style>
</head>
<body class="<?=$skin?>">

<form method="post" enctype="multipart/form-data" id="shop_edit_class" name="form1">
    <input  name="shop_id" id="shop_id" value="<?=$data['shop_id']?>"  type="hidden"/>
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="shop_name"><i>*</i><?= __('店铺名称'); ?></label>
            </dt>
            <dd class="opt">
                <input id="shop_name" name="shop_name" value="<?=$data['shop_name']?>" class="ui-input w200" type="text" disabled="disabled" readonly="readonly"/>
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label for="shop_name"><i>*</i><?= __('店铺地址'); ?></label>
            </dt>
            <dd class="opt">
                <input id="district_name" name="district_name" value="<?=$data['district_name']?>" class="ui-input w200" type="text" disabled="disabled" /><label onclick="modifyDistrict()" style='color: blue;margin-left: 10px; cursor: pointer;'><?= __('修改地址'); ?></label>
                <input id="district_id" name="district_id" value="<?=$data['district_id']?>" class="ui-input w200" type="hidden" />
                <input id="new_district_id" name="new_district_id" value="" class="ui-input w200" type="hidden" />
            </dd>

        </dl>
        <dl class="row hidden new_district">
            <dt class="tit">
                <label for="shop_name"><i>*</i><?= __('店铺新地址'); ?></label>
            </dt>
            <dd class="opt">
                <select id="select_1" name="select_1" onChange="getDistrict(1,$(this).val());" class="hidden"></select>
                <select id="select_2" name="select_2" onChange="getDistrict(2,$(this).val());" class="hidden"></select>
                <select id="select_3" name="select_3" onChange="getDistrict(3,$(this).val());" class="hidden"></select>
                <select id="select_4" name="select_4" onChange="getDistrict(4,$(this).val());" class="hidden"></select>
                <label onclick="delDistrict()" style='color: blue;margin-left: 10px; cursor: pointer;'><?= __('取消修改'); ?></label>
            </dd>

        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="shop_create_time"><i>*</i><?= __('开店时间'); ?></label>
            </dt>
            <dd class="opt">
                <?=$data['shop_create_time']?>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="shop_tel"><?= __('联系电话'); ?></label>
            </dt>
            <dd class="opt">
                <input type="text" disabled="disabled" name="shop_tel" id="shop_tel" value="<?=$data['shop_tel']?>">
                <input type="hidden" disabled="disabled" id="area_code" name="area_code" value="<?=$data['area_code']?>">
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label for="shop_all_class"><?= __('绑定所有类目'); ?></label>
            </dt>
            <dd class="opt">
                <div class="onoff">
                    <label for="shop_all_class1" class="cb-enable <?=($data['shop_all_class'] ? 'selected' : '')?> "><?= __('开启'); ?></label>
                    <label for="shop_all_class0" class="cb-disable <?=(!$data['shop_all_class'] ? 'selected' : '')?>"><?= __('关闭'); ?></label>
                    <input id="shop_all_class1"  name ="shop_all_class" <?=($data['shop_all_class'] ? 'checked' : '')?>  value="1" type="radio">
                    <input id="shop_all_class0"  name ="shop_all_class"  <?=(!$data['shop_all_class'] ? 'checked' : '')?>   value="0" type="radio">

                </div>
            </dd>
        </dl>
        <?php if(Yf_Registry::get('yunshanstatus')==1) { ?>
        <dl class="row">
          <dt class="tit">
            <label for="payshopname"> 分账商户名称 </label>
          </dt>
          <dd class="opt">
              <input id="payshopname" class="ui-input w200" name="shop[payshopname]" value="<?= $data['payshopname']?>"  />

          </dd>
        </dl>
        
        <dl class="row">
          <dt class="tit">
            <label for="payshopnumer"> APP支付商户号 </label>
          </dt>
          <dd class="opt">
              <input id="payshopnumer" class="ui-input w200" name="shop[payshopnumer]" value="<?= $data['payshopnumer']?>"  />

          </dd>
        </dl>
        
        <dl class="row"  style="display:none;">
          <dt class="tit">
            <label for="payshopcode"> 分账商户ID </label>
          </dt>
          <dd class="opt">
             <input id="payshopcode" class="ui-input w200" name="shop[payshopcode]" value="<?= $data['payshopcode']?>"  />
          </dd>
        </dl>
        
        
        <dl class="row"   style="display:none;"  >
          <dt class="tit">
            <label for="paytermnumber"> 终端 </label>
          </dt>
          <dd class="opt">
            <input id="paytermnumber" class="ui-input w200" name="shop[paytermnumber]" value="<?= $data['paytermnumber']?>"  />

          </dd>
        </dl>
        <dl class="row"   style="display:none;">
          <dt class="tit">
            <label for="payscale"> 分账手续费百分比% </label>
          </dt>
          <dd class="opt">
             <input id="payscale" class="ui-input w200" name="shop[payscale]" value="<?= $data['payscale']?>" />

          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="cbpayshopnumer"> C扫B支付商户号 </label>
          </dt>
          <dd class="opt">
              <input id="cbpayshopnumer" class="ui-input w200" name="shop[cbpayshopnumer]" value="<?= $data['cbpayshopnumer']?>"  />

          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="xcxpayshopnumer"> 小程序支付商户号 </label>
          </dt>
          <dd class="opt">
              <input id="xcxpayshopnumer" class="ui-input w200" name="shop[xcxpayshopnumer]" value="<?= $data['xcxpayshopnumer']?>"  />

          </dd>
        </dl>
       <?php } ?>
        <dl class="row">
            <dt class="tit">
                <label for="shop_status"><?= __('状态'); ?></label>
            </dt>
            <dd class="opt">
                <div class="onoff">
                    <label for="shop_status1" class="cb-enable <?=($data['shop_status'] ? 'selected' : '')?> "><?= __('开启'); ?></label>
                    <label for="shop_status0" class="cb-disable <?=(!$data['shop_status'] ? 'selected' : '')?>"><?= __('关闭'); ?></label>
                    <input id="shop_status1"  name ="shop_status" <?=($data['shop_status'] ? 'checked' : '')?>  value="3" type="radio">
                    <input id="shop_status0"  name ="shop_status"  <?=(!$data['shop_status'] ? 'checked' : '')?>   value="0" type="radio">

                </div>
                <p class="notic"><?= __('关闭店铺时，该店铺中的商品将被全部下架，请谨慎操作！！'); ?></p>
            </dd>
        </dl>



    </div>
</form>

<script type="text/javascript">
    $("#shop_tel").intlTelInput({
        utilsScript: "<?=$this->view->js_com?>/utils.js"
    });
    function initPopBtns()
    {
        var t = "Add" == oper ? ["<?= __('保存'); ?>", "<?= __('关闭'); ?>"] : ["<?= __('确定'); ?>", "<?= __('取消'); ?>"];
        api.button({
            id: "confirm", name: t[0], focus: !0, callback: function ()
            {
                postData(oper, rowData.shop_id);
                return cancleGridEdit(),$("#shop_edit_class").trigger("validate"), !1
            }
        }, {id: "cancel", name: t[1]})
    }
    function postData(t, e)
    {

        $_form.validator({
            rules: {
                tel:function(){
                    var area_code = $('#area_code').val();
                    var shop_tel = $('#shop_tel').val();
                    var reg = /^[1][0-9]{10}$/;
                    if(area_code==86 && !reg.test(shop_tel)){
                        return '<?= __('请输入正确的手机号码'); ?>';
                    }
                }
            },
            messages: {
                required: "<?= __('请填写该字段'); ?>",
            },
            fields: {
//                'shop_name':'required;' ,
                'shop_status':'required;' ,
                'shop_tel':'tel'
            },

            valid: function (form)
            {
                var
                    shop_id = $.trim($("#shop_id").val()),
//              shop_name = $.trim($("#shop_name").val()),
                    shop_all_class = $.trim($("input[name='shop_all_class']:checked").val()),
                    shop_status = $.trim($("input[name='shop_status']:checked").val()),
                    new_district_id = $("#new_district_id").val(),
                    district_id = $("#district_id").val(),
                    shop_tel = $("#shop_tel").val(),
                    area_code = $("#area_code").val(),
                    n = "Add" == t ? "<?= __('新增店铺'); ?>" : "<?= __('修改店铺'); ?>";
                    var   payshopname = $.trim($("#payshopname").val());
                    var   payshopnumer = $.trim($("#payshopnumer").val());
                    var   paytermnumber = $.trim($("#paytermnumber").val());
                    var   payshopcode = $.trim($("#payshopcode").val());
                    var   payscale = $.trim($("#payscale").val());
                    var   cbpayshopnumer = $.trim($("#cbpayshopnumer").val());
                    var   xcxpayshopnumer = $.trim($("#xcxpayshopnumer").val());

                params =  { shop_id: shop_id, shop_all_class: shop_all_class, shop_status:shop_status, new_district_id:new_district_id, district_id:district_id,shop_tel:shop_tel,area_code:area_code };

                    params.payshopname =  payshopname ;
                    params.payshopnumer =  payshopnumer ;
                    params.paytermnumber =  paytermnumber ;
                    params.payshopcode =  payshopcode ;
                    params.payscale =  payscale ;
                    params.cbpayshopnumer =  cbpayshopnumer ;
                    params.xcxpayshopnumer =  xcxpayshopnumer ;
                Public.ajaxPost(SITE_URL +"?ctl=Shop_Selfsupport&met=" + ("Add" == t ? "Add" : "Edit")+ "ShopBase&typ=json", params, function (e)
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
        $("#shop_name").val("");
        $("input[name='shop_all_class']:checked").val("");
        $("input[name='shop_status']:checked").val("");

    }
    var curRow, curCol, curArrears, $grid = $("#grid"),  $_form = $("#shop_edit_class"), api = frameElement.api, oper = api.data.oper, rowData = api.data.rowData || {}, callback = api.data.callback;
    console.log(rowData);
    initPopBtns();



    //选择地区'); ?>
    function getDistrict(level,nodeid){
        if(nodeid == '-1'){
            $('#select_2').html('');
            $('#select_2').hide();
            $('#select_3').html('');
            $('#select_3').hide();
            $('#select_4').html('');
            $('#select_4').hide();
            return ;
        }
        var next_level = level + 1;
        $('#new_district_id').val(nodeid);
        $.post(SITE_URL+'?ctl=Base_District&met=district&typ=json&nodeid='+nodeid,function(b){
            if(b.status==200 && b.data.items.length > 0){
                $('#select_'+next_level).show();
                $('#select_'+next_level).html('');
                if(level == 1){
                    $('#select_3').html('');
                    $('#select_3').hide();
                    $('#select_4').html('');
                    $('#select_4').hide();
                }
                if(level == 2){
                    $('#select_4').html('');
                    $('#select_4').hide();
                }
                $('#select_'+next_level).append('<option value="-1">--<?= __('请选择'); ?>--</option>');
                $.each( b.data.items, function(i, v){
                    $('#select_'+next_level).append('<option value="'+v.district_id+'">'+v.district_name+'</option>');
                });
            }
        },'json');
    }
    getDistrict(0,0);

    function modifyDistrict(){
        if(!$('.new_district').hasClass('hidden')){
            return false;
        }
        getDistrict(0,'-1');
        $('#select_1').find("option[value='-1']").prop('selected','selected');
        $('.new_district').removeClass('hidden');
    }

    function delDistrict(){
        $('#new_district_id').val('');
        $('.new_district').addClass('hidden');
    }
</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
