<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<style>
    body{background: #fff;}
    .js-commission {
        display: none;
    }
</style>
</head>
<body class="<?=$skin?>">

<form method="post" name="manage-form" id="manage-form" action="<?= Yf_Registry::get('url') ?>?act=goods&amp;op=goods_lockup">
    <input type="hidden" name="form_submit" value="ok">
    <input type="hidden" name="common_id_input">

    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label class="cat_name" for="cat_name"><em>*</em><?= __('分类名称'); ?></label>
            </dt>
            <dd class="opt">
                <!--<input type="text" maxlength="20" value="" name="cat_name" id="cat_name" class="ui-input ui-input-ph">-->
                <textarea value="" name="cat_name" id="cat_name" class="ui-input ui-input-ph" /></textarea>
                <span class="err"></span>
                <p class="notic"><?= __('批量添加商品分类的时候，以回车键隔开'); ?></p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="cat_image"><?= __('分类图片'); ?>:</label>
            </dt>
            <dd class="opt">
                <img src="../shop_admin/static/common/images/image.png"  id="cat_image" name="setting[cat_logo]" alt="<?= __('选择图片'); ?>"  class="image-line" />
                <span id="del_img"><p class="notic">删除</p></span>
                <div class="image-line"  id="cat_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>
                <p class="notic"><?= __('上传分类图片时，请上传宽度不低于'); ?>240<?= __('像素，高度不低于'); ?>200<?= __('像素的图片'); ?></p>
                <input id="cat_logo" name="setting[cat_logo]" value="" class="ui-input w400" type="hidden"/>
            </dd>
        </dl>
            <dl class="row">
            <dt class="tit">
                <label><?= __('是否在首页展示'); ?></label>
            </dt>
            <dd class="opt">
                <div style="float: left;width:50%;">
                <label>
                    <input type="checkbox" class="checkbox" name="cat_is_index" id="cat_is_index" value=""><?= __('允许'); ?>
                </label>
                <p class="notic mb10"><?= __('勾选允许在首页展示以后,三级分类在wap端首页分类里会展示。'); ?></p>
                </div>
            </dd>
        </dl>

        <dl class="row" id="is_virtual">
            <dt class="tit">
                <label><?= __('发布虚拟商品'); ?></label>
            </dt>
            <dd class="opt">
                <div style="float: left;width:50%;">
                <label>
                    <input type="checkbox" class="checkbox" name="cat_is_virtual" id="cat_is_virtual" value=""><?= __('允许'); ?>
                </label>
                <p class="notic mb10"><?= __('勾选允许发布虚拟商品后，在发布该分类的商品时可选择交易类型为“虚拟兑换码”形式。'); ?></p>
                </div>
                <!--<div style="float: right; width:50%;">
                    <label for="t_gc_virtual">
                        <input id="t_gc_virtual" type="checkbox" class="checkbox" value="1" name="t_gc_virtual"><?= __('关联到子分类'); ?>
                    </label>
                <p class="notic"><?= __('勾选关联到子分类后，该分类下的子分类交易类型也将被设定为“虚拟兑换码”形式。'); ?></p>
                </div>-->
            </dd>
        </dl>
        <dl class="row" id="return_goods_limit_dl">
            <dt class="tit">
                <label><em>*</em><?= __('分类商品退货期'); ?></label>
            </dt>
            <dd class="opt">
                <div style="float: left;width:50%;">
                    <select class="ui-input" id="return_goods_limit" name ="return_goods_limit" style="width: 130px;height:30px;">
                        <option value='-1' selected>请选择   </option>
                        <option value='0'>不支持退货退款</option>
                        <option value='1'>不支持退货</option>
                        <option value='7'>7天       </option>
                        <option value='15'>15天      </option>
                        <option value='30'>30天      </option>
                    </select>
                    <p class="notic mb10"><b>此处设置之后不要轻易修改，否则影响结算周期！</b></p>
                    <p class="notic mb10"><?= __('注：若选择“7天”，退货期是用户确认收货之后的7*24小时的时间范围内，精确到分钟。“15天”和“30天”同样遵从上述规则。'); ?></p>
                </div>
            </dd>
        </dl>
        <!--
        <dl class="row">
            <dt class="tit">
                <label><?= __('商品展示方式'); ?></label>
            </dt>
            <dd class="opt">
                <select name="cat_show_type" id="cat_show_type" class="ui-combo-wrap">
                    <option name='show_type' value="1" selected>SUP</option>
                    <option name='show_type' value="2" ><?= __('颜色'); ?></option>
                </select>
                <span class="err"></span>
                    <p class="notic mb10"><?= __('在商品列表页的展示方式。'); ?><br>   <?= __('“颜色”：每个'); ?>SPU<?= __('只展示不同颜色的'); ?>SKU<?= __('，同一颜色多个'); ?>SKU<?= __('只展示一个'); ?>SKU<?= __('。'); ?><br>    <?= __('“'); ?>SPU<?= __('”：每个'); ?>SPU<?= __('只展示一个'); ?>SKU<?= __('。'); ?></p>
                <label for="t_show_type">
                <input id="t_show_type" type="checkbox" class="checkbox" value="1" name="t_show_type"><?= __('关联到子分类'); ?></label>
              <p class="notic"><?= __('勾选关联到子分类后，被绑定的商品展示方式也将继承到子分类中使用。'); ?></p>
            </dd>
        </dl>
        -->
        <dl class="row">
            <dt class="tit">
                <label><em>*</em><?= __('分佣比例'); ?></label>
            </dt>
            <dd class="opt">
                <div style="float: left;width:50%;">
                    <input id="cat_commission" class="ui-input ui-input-ph" type="text" value="1" name="cat_commission">
                    <i>%</i> <span class="err"></span>
                    <p class="notic mb10"><?= __('分佣比例必须为'); ?>0-100<?= __('的整数。'); ?></p>
                </div>
                <!--<div style="float: right;width:50%;">
                    <label for="t_commis_rate">
                    <input id="t_commis_rate" class="checkbox" type="checkbox" value="1" name="t_commis_rate"><?= __('关联到子分类'); ?></label>
                    <p class="notic"><?= __('勾选关联到子分类后，该分类下的子分类分佣比利也将按此继承设定。'); ?></p>
                </div>-->
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label><?= __('上级分类'); ?>:</label>
                <input id="parent_id" name="parent_id" class="ui-input" type="hidden"/>
            </dt>
			<dd class="opt">
				<div class="ctn-wrap"><input type="text" value="" class="ui-input" name="parent_cat" id="parent_cat" readonly="true"></div>
			</dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label><?= __('类型'); ?></label>
                <input id="type_id" name="type_id" class="ui-input" type="hidden"/>
            </dt>
			<dd class="opt">
				<span id="goods_type_combo"></span>
			</dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label for="gc_sort"><?= __('排序'); ?></label>
            </dt>
            <dd class="opt">
                <input type="text" value="0" name="cat_displayorder" id="cat_displayorder" class="ui-input ui-input-ph">
                <span class="err"></span>
                <p class="notic"><?= __('数字范围为'); ?>0~255<?= __('，数字越小越靠前'); ?></p>
            </dd>
        </dl>
        

    </div>
</form>
<script>
    //图片上传
    $(function(){
        buyer_logo_upload = new UploadImage({
            thumbnailWidth: 240,
            thumbnailHeight: 200,
            imageContainer: '#cat_image',
            uploadButton: '#cat_upload',
            inputHidden: '#cat_logo'
        });

        Public.ajaxGet(SITE_URL + '?ctl=Fenxiao&met=getStatus&typ=json', {}, function (data) {
            if (data.status == 200) {
                if (data.data.status == 1) {
                    $('dl.js-commission').show();
                }
            }
        });
    })

    $("#del_img").click(function () {
        $("#cat_logo").val("");
        $(this).prev().prop('src', '../shop_admin/static/common/images/image.png');
    })
</script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/goods/cat_manage.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>