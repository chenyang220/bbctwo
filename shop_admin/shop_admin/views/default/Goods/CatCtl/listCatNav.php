<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
    include $this->view->getTplPath() . '/' . 'header.php';
    $data['class'] = $data['cat'];
?>
    <link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
    <link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
    <style>
    	.container:hover ul.ul>li:nth-child(2){
            background: #000;
        }
        body {
            background: #fff;
        }
        .img-hover :hover 
    </style>
    </head>
    <body class="<?=$skin?>">
<form method="post" name="manage-form" id="manage-form" action="<?= Yf_Registry::get('url') ?>?act=goods&amp;op=goods_lockup">
    <input type="hidden" name="form_submit" value="ok">
    <input type="hidden" name="common_id_input">
    <input type="hidden" name="goods_cat_id" id="goods_cat_id">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label class="cat_other_name" for="cat_other_name"><em>*</em><?= __('分类别名'); ?>:</label>
            </dt>
            <dd class="opt">
                <input type="text" maxlength="20" value="" name="cat_other_name" id="cat_other_name" class="input-txt">
                <span class="err"></span>
                <p class="notic"><?= __('必填项。设置别名后，别名将会替代原分类名称展示在'); ?>web<?= __('端的分类导航菜单列表中。'); ?></p>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label><?= __('分类图片'); ?></label>
            </dt>
            <dd class="opt">
                <div>
                    <img id="cat_image" name="setting[cat_logo]" alt="<?= __('选择图片'); ?>" src="./shop_admin/static/common/images/image.png" class="image-line" />
                </div>
                <div>
                    <div class="image-line" style="margin-left: 10px;" id="cat_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>
                    <input id="cat_logo" name="setting[cat_logo]" value="" class="ui-input w400" type="hidden" />
                </div>
            </dd>
        </dl>
        <!-- 首页分类导航展示开始 -->
      <dl class="row">
            <dt class="tit">
                <label><?= __('首页分类导航展示'); ?>:</label>
            </dt>
            <dd class="opt">
                <?php if (!empty($data['cat'])) { ?>
                    <?php
                    foreach ($data['cat'] as $key_cat => $value_cat) {
                        ?>
                        <div style="width: 950px;float: left">
                            <b>
                                <input type="checkbox" id="recommend_class_<?php print($value_cat['cat_id']); ?>" name="goods_class_recommend" class="higher_level_up_<?php print($value_cat['cat_id']); ?>" data="0" onchange="cat_up_lower(this)" value="<?php print($value_cat['cat_id']); ?>"><?php print($value_cat['cat_name']); ?>
                                <a href="javascript:" class="a_<?php print($value_cat['cat_id']); ?> " onclick="aup_click(this)" value="<?php print($value_cat['cat_id']); ?>" data="0"><?= __('▲'); ?></a>
                            </b>
                        
                        </div>
                        <div class="lower_up" data_cid_up="<?php print($value_cat['cat_id']); ?>">
                            <?php if (isset($value_cat['sub']) && !empty($value_cat['sub'])) { ?>
                                <?php foreach ($value_cat['sub'] as $k_cat => $val_cat) { ?>
                                    <div style="width: 180px;float: left;" class="lower_level_up_<?php print($value_cat['cat_id']); ?>">
                                        <label style="margin-left: 20px;">
                                            <input type="checkbox" name="goods_class_recommend" value="<?php print($val_cat['cat_id']); ?>"
                                                   id="recommend_class_<?php print($val_cat['cat_id']); ?>" onclick="cat_up_click(this,<?php print($value_cat['cat_id']); ?>)" data-p_id="<?php print($value_cat['cat_id']); ?>" class="higher_level_val_up_<?php print($value_cat['cat_id']); ?>"><?php print($val_cat['cat_name']); ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    
                    <?php }
                } else { echo '暂无子级分类可关联';} ?>
            </dd>
        </dl>
        <!-- 首页分类导航展示结束 -->
        <dl class="row">
            <dt class="tit">
                <label><?= __('推荐品牌'); ?>:</label>
            </dt>
            <dd class="opt">
                <?php
                    if (!empty($data['brand'])):
                        foreach ($data['brand'] as $k_brand => $v_brand):?>
                            <div style="width: 950px;float: left">
                                <b><?php print($v_brand['catname']); ?></b>
                            </div>
                            
                            <?php foreach ($v_brand['brand'] as $key_brand => $val_brand): ?>
                                <div style="width: 180px;float:left">
                                    <label style="width:180px;">
                                        <input type="checkbox" value="<?php print($val_brand['brand_id']); ?>" name="brand_value"
                                               id="brand_<?php print($val_brand['brand_id']); ?>"><?php print($val_brand['brand_name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach;
                    endif;
                ?>
            </dd>
        </dl>

      <dl class="row">
            <dt class="tit">
                <label><?= __('关联分类'); ?>:</label>
            </dt>
            <dd class="opt">
                <?php if (!empty($data['class'])) { ?>
                    <?php
                    foreach ($data['class'] as $key_cat => $value_cat) {
                        ?>
                        <div style="width: 950px;float: left">
                            <b>
                                <input type="checkbox" name="recommend_cat_higher" class="higher_level_<?php print($value_cat['cat_id']); ?>" data="0" onchange="cat_lower(this)" value="<?php print($value_cat['cat_id']); ?>"><?php print($value_cat['cat_name']); ?>
                                <a href="javascript:" class="a_<?php print($value_cat['cat_id']); ?> " onclick="a_click(this)" value="<?php print($value_cat['cat_id']); ?>" data="0"><?= __('▲'); ?></a>
                            </b>
                        
                        </div>
                        <div class="lower" data_cid="<?php print($value_cat['cat_id']); ?>">
                            <?php if (isset($value_cat['sub']) && !empty($value_cat['sub'])) { ?>
                                <?php foreach ($value_cat['sub'] as $k_cat => $val_cat) { ?>
                                    <div style="width: 180px;float: left;" class="lower_level_<?php print($value_cat['cat_id']); ?>">
                                        <label style="margin-left: 20px;">
                                            <input type="checkbox" name="recommend_cat" value="<?php print($val_cat['cat_id']); ?>"
                                                   id="recommend_<?php print($val_cat['cat_id']); ?>" onclick="cat_click(this,<?php print($value_cat['cat_id']); ?>)" data-p_id="<?php print($value_cat['cat_id']); ?>" class="higher_level_val_<?php print($value_cat['cat_id']); ?>"><?php print($val_cat['cat_name']); ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    
                    <?php }
                } ?>
            </dd>
        </dl>

        <dl class="row">
            <dt class="tit">
                <label><?= __('广告图'); ?>1:</label>
            </dt>
            <dd class="opt">
                <div style="position: relative;" class="img-hover iblock">
                    <img id="adv_image" name="setting[adv_logo]" alt="<?= __('选择图片'); ?>" src="./shop_admin/static/common/images/image.png" class="image-line"/>
                     <a href="javascript:void(0)" class="del a-dis" title="<?= __('移除'); ?>" id="advs_del" onclick="adv_del(this)" style="right: 5px;top: 5px;width: 16px;overflow: hidden;"><i class="iconfont icon-cancel" style="margin-left: -7px;"></i></a>
                </div>
                <div>
                    <div class="image-line iblock" id="adv_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>
                   
                    <input id="adv_logo" name="setting[adv_logo]" value="" class="ui-input w400" type="hidden" />
                    <p class="iblock upImg-tips">建议上传尺寸为200*200像素</p>
                </div>
            </dd>
        </dl>
         <dl class="row">
            <dt class="tit">
                <lable><?= __('链接地址'); ?>1:</lable>
            </dt>
            <dd class="opt">
                <input type="text" maxlength="200" value="" name="setting[adv_logo_url]" id="adv_logo_url" class="input-txt w400">
                <span class="err"></span>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label><?= __('广告图'); ?>2:</label>
            </dt>
            <dd class="opt">
                <div>
                    <div style="position: relative" class="img-hover iblock">
                        <img id="advs_image" name="setting[advs_logo]" alt="<?= __('选择图片'); ?>" src="./shop_admin/static/common/images/image.png" class="image-line"/>
                        <a href="javascript:void(0)" class="del a-dis" title="<?= __('移除'); ?>" id="advs_del" onclick="advs_del(this)" style="right: 5px;top: 2px;width: 16px;overflow: hidden;" ><i class="iconfont icon-cancel" style="margin-left: -7px;"></i></a>
                    </div>
                    <div>
                        <div class="image-line iblock" id="advs_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>
                        
                        <input id="advs_logo" name="setting[advs_logo]" value="" class="ui-input w400" type="hidden" />
                        <p class="iblock upImg-tips">建议上传尺寸为200*200像素</p>
                    </div>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <lable><?= __('链接地址'); ?>2:</lable>
            </dt>
            <dd class="opt">
                <input type="text" maxlength="200" value="" name="setting[advs_logo_url]" id="advs_logo_url" class="input-txt w400">
                <span class="err"></span>
            </dd>
        </dl>
    </div>
</form>
<script>
    //图片上传'); ?>
    $(function () {
        setting_logo_upload = new UploadImage({
            thumbnailWidth: 190,
            thumbnailHeight: 150,
            imageContainer: '#adv_image',
            uploadButton: '#adv_upload',
            inputHidden: '#adv_logo'
        });
        
        cat_logo_upload = new UploadImage({
            thumbnailWidth: 190,
            thumbnailHeight: 150,
            imageContainer: '#cat_image',
            uploadButton: '#cat_upload',
            inputHidden: '#cat_logo'
        });
        
        buyer_logo_upload = new UploadImage({
            thumbnailWidth: 190,
            thumbnailHeight: 150,
            imageContainer: '#advs_image',
            uploadButton: '#advs_upload',
            inputHidden: '#advs_logo'
        });
        
    });
    
    //显示与隐藏子级分类'); ?>
    function cat_lower(obj) {
        if ($(obj).prop('checked')) {
            $('.lower_level_' + $(obj).val()).find('input').prop('checked', 'checked');
        } else {
            $('.lower_level_' + $(obj).val()).find('input').prop('checked', false);
        }
        
    }

        //显示与隐藏子级分类'); ?>
    function cat_up_lower(obj) {
        if ($(obj).prop('checked')) {
            $('.lower_level_up_' + $(obj).val()).find('input').prop('checked', 'checked');
        } else {
            $('.lower_level_up_' + $(obj).val()).find('input').prop('checked', false);
        }
        
    }
    
    //点击标签显示与隐藏子级分类
    function a_click(obj) {
        if ($(obj).hasClass('img_down')) {
            $(obj).removeClass('img_down');
            $(obj).html('<?= __('▲'); ?>');//a标签正三角改为倒三角
            $('.lower_level_' + $(obj).attr('value')).show();
        } else {
            $(obj).addClass('img_down');
            $(obj).html('<?= __('▼'); ?>');//a标签正三角改为倒三角
            $('.lower_level_' + $(obj).attr('value')).hide();
        }
    }

        //点击标签显示与隐藏子级分类
    function aup_click(obj) {
        if ($(obj).hasClass('img_down')) {
            $(obj).removeClass('img_down');
            $(obj).html('<?= __('▲'); ?>');//a标签正三角改为倒三角
            $('.lower_level_up_' + $(obj).attr('value')).show();
        } else {
            $(obj).addClass('img_down');
            $(obj).html('<?= __('▼'); ?>');//a标签正三角改为倒三角
            $('.lower_level_up_' + $(obj).attr('value')).hide();
        }
    }
    
    function cat_up_click(obj, p_id) {
        if ($(obj).prop('checked')) {
            all_up_check(p_id);
        } else {
            $('.higher_level_up_' + p_id).prop('checked', false);
        }
        
    }

    function cat_click(obj, p_id) {
        if ($(obj).prop('checked')) {
            all_check(p_id);
        } else {
            $('.higher_level_' + p_id).prop('checked', false);
        }
        
    }
    function all_check(p_id) {
        var chknum = $(".higher_level_val_" + p_id).size();
        var chk = 0;
        $(".higher_level_val_" + p_id).each(function () {
            if ($(this).prop("checked")) {
                chk++;
            }
        });
        if (chknum == chk) {//全选'); ?>
            $(".higher_level_" + p_id).prop("checked", 'checked');
        } else {//不全选'); ?>
            $(".higher_level_" + p_id).prop("checked", false);
        }
    }

    function all_up_check(p_id) {
        var chknum = $(".higher_level_val_up_" + p_id).size();
        var chk = 0;
        $(".higher_level_val_up_" + p_id).each(function () {
            if ($(this).prop("checked")) {
                chk++;
            }
        });
        if (chknum == chk) {//全选'); ?>
            $(".higher_level_up_" + p_id).prop("checked", 'checked');
        } else {//不全选'); ?>
            $(".higher_level_up_" + p_id).prop("checked", false);
        }
    }
    
    //初始化关联分类全选按钮'); ?>
    window.onload = function () {
        $(".lower").each(function () {
            var p_id = $(this).attr('data_cid');
            var chknum = $(".higher_level_val_" + p_id).size();
            var chk = 0;
            $(".higher_level_val_" + p_id).each(function () {
                if ($(this).prop("checked")) {
                    chk++;
                }
            });
            
            if (chknum == chk) {//全选'); ?>
                $(".higher_level_" + p_id).prop("checked", 'checked');
            } else {//不全选'); ?>
                $(".higher_level_" + p_id).prop("checked", false);
            }
        });

        // $(".lower_up").each(function () {
        //     var p_id = $(this).attr('data_cid_up');
        //     var chknum = $(".higher_level_val_up_" + p_id).size();
        //     var k = 0;
        //     $(".higher_level_val_up_" + p_id).each(function () {
        //         if ($(this).prop("checked")) {
        //             k++;
        //         }
        //     });
            
        //     if (chknum == k) {//全选'); ?>
        //         $(".higher_level_up_" + p_id).prop("checked", 'checked');
        //     } else {//不全选'); ?>
        //         $(".higher_level_up_" + p_id).prop("checked", false);
        //     }
        // });
    };
    
    //删除广告图'); ?>1
    function adv_del(obj) {
        $("#adv_image").attr('src', './shop_admin/static/common/images/image.png');
        $("#adv_logo").val('./shop_admin/static/common/images/image.png');
    }
    
    //删除广告图'); ?>2
    function advs_del(obj) {
        $("#advs_image").attr('src', './shop_admin/static/common/images/image.png');
        $("#advs_logo").val('./shop_admin/static/common/images/image.png');
    }


</script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/controllers/goods/listCatNav.js" charset="utf-8"></script>


<?php
    include $this->view->getTplPath() . '/' . 'footer.php';
?>