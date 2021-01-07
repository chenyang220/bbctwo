<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
if(isset(Perm::$row['sub_site_id']) && Perm::$row['sub_site_id'] > 0){
    $subsite_suffix = '_'.Perm::$row['sub_site_id'];
}else{
    $subsite_suffix = '';
}
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
</head>
<style>
.image-line {
  margin-bottom:5px;
}
.div-image-1 {
    width: 400px;
    position: relative;
}
</style>
<body class="<?=$skin?>">
<div class="wrapper page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3><?= __($menus['father_menu']['menu_name']); ?></h3>
                <h5><?= __($menus['father_menu']['menu_url_note']); ?></h5>
            </div>
            <ul class="tab-base nc-row">
                <?php 
                foreach($menus['brother_menu'] as $key=>$val){ 
                    if(in_array($val['rights_id'],$admin_rights)||$val['rights_id']==0){
                ?>
                <li><a <?php if(!array_diff($menus['this_menu'], $val)){?> class="current"<?php }?> href="<?= Yf_Registry::get('url') ?>?ctl=<?=$val['menu_url_ctl']?>&met=<?=$val['menu_url_met']?><?php if($val['menu_url_parem']){?>&<?=$val['menu_url_parem']?><?php }?>"><span><?= __($val['menu_name']); ?></span></a></li>
                <?php 
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
          <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
          <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em>
        </div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>
        </ul>
    </div>

   <form method="post" enctype="multipart/form-data" id="slider-setting-form" name="form1">
    <input type="hidden" name="config_type[]" value="slider<?=$subsite_suffix?>"/>
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label><?= __('滚动图片'); ?>1</label>
        </dt>
        <dd class="opt">
                <div class="div-image-1">
                    <img id="slider1_review" src="<?=@($data['slider1_image'.$subsite_suffix]['config_value'])?>" width="400"/>
                    <a href="javascript:void(0)" id="del_btn1" class="del" onclick="delImage('slider1_review')" title="<?= __('移除'); ?>"><i class="iconfont icon-cancel"></i></a>
                </div>
                <input type="hidden" id="slider1_image" name="slider<?=$subsite_suffix?>[slider1_image<?=$subsite_suffix?>]" value="<?=@($data['slider1_image'.$subsite_suffix]['config_value'])?>" />
                <div  id='slider1_upload' class="image-line upload-image" ><?= __('图片上传'); ?></div>

           <label title="<?= __('请输入图片要跳转的链接地址'); ?>" class="mt10"><i class="fa fa-link"></i>
                <input class="ui-input w400" type="text" name="slider<?=$subsite_suffix?>[live_link1<?=$subsite_suffix?>]" value="<?=@($data['live_link1'.$subsite_suffix]['config_value'])?>" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>">
           </label>
           <span class="err"><label for="live_link1" class="error valid"></label></span>
           <p class="notic"><?= __('请使用宽度'); ?>1043<?= __('像素，高度'); ?>396<?= __('像素的'); ?>jpg/gif/png<?= __('格式图片作为幻灯片'); ?>banner<?= __('上传，'); ?><br>
            <?= __('如需跳转请在后方添加以http://开头的链接地址。'); ?></p>
        </dd>
      </dl>

     <dl class="row">
        <dt class="tit">
          <label><?= __('滚动图片'); ?>2</label>
        </dt>
        <dd class="opt">
                <div class="div-image-1">
                    <img id="slider2_review" src="<?=@($data['slider2_image'.$subsite_suffix]['config_value'])?>" width="400"/>
                    <a href="javascript:void(0)" id="del_btn2" class="del" onclick="delImage('slider2_review')" title="<?= __('移除'); ?>"><i class="iconfont icon-cancel"></i></a>
                </div>
                <input type="hidden" id="slider2_image" name="slider<?=$subsite_suffix?>[slider2_image<?=$subsite_suffix?>]" value="<?=@($data['slider2_image'.$subsite_suffix]['config_value'])?>" />
                <div  id='slider2_upload' class="image-line upload-image" ><?= __('图片上传'); ?></div>

           <label title="<?= __('请输入图片要跳转的链接地址'); ?>" class="mt10"><i class="fa fa-link"></i>
                <input class="ui-input w400" type="text" name="slider<?=$subsite_suffix?>[live_link2<?=$subsite_suffix?>]" value="<?=@($data['live_link2'.$subsite_suffix]['config_value'])?>" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>">
           </label>
           <span class="err"><label for="live_link2" class="error valid"></label></span>
           <p class="notic"><?= __('请使用宽度'); ?>1043<?= __('像素，高度'); ?>396<?= __('像素的'); ?>jpg/gif/png<?= __('格式图片作为幻灯片'); ?>banner<?= __('上传，'); ?><br>
            <?= __('如需跳转请在后方添加以http://开头的链接地址。'); ?></p>
        </dd>
      </dl>


    <dl class="row">
        <dt class="tit">
          <label><?= __('滚动图片'); ?>3</label>
        </dt>
        <dd class="opt">
            <div class="div-image-1">
                     <img id="slider3_review" src="<?=@($data['slider3_image'.$subsite_suffix]['config_value'])?>" width="400"/>
                    <a href="javascript:void(0)" id="del_btn3" class="del" onclick="delImage('slider3_review')" title="<?= __('移除'); ?>"><i class="iconfont icon-cancel"></i></a>
            </div>
        
            <input type="hidden" id="slider3_image" name="slider<?=$subsite_suffix?>[slider3_image<?=$subsite_suffix?>]" value="<?=@($data['slider3_image'.$subsite_suffix]['config_value'])?>" />
            <div  id='slider3_upload' class="image-line upload-image" ><?= __('图片上传'); ?></div>


           <label title="<?= __('请输入图片要跳转的链接地址'); ?>" class="mt10"><i class="fa fa-link"></i>
                <input class="ui-input w400" type="text" name="slider<?=$subsite_suffix?>[live_link3<?=$subsite_suffix?>]" value="<?=@($data['live_link3'.$subsite_suffix]['config_value'])?>" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>">
           </label>
           <span class="err"><label for="live_link3" class="error valid"></label></span>
           <p class="notic"><?= __('请使用宽度'); ?>1043<?= __('像素，高度'); ?>396<?= __('像素的'); ?>jpg/gif/png<?= __('格式图片作为幻灯片'); ?>banner<?= __('上传，'); ?><br>
            <?= __('如需跳转请在后方添加以http://开头的链接地址。'); ?></p>
        </dd>
      </dl>

    <dl class="row">
        <dt class="tit">
          <label><?= __('滚动图片'); ?>4</label>
        </dt>
        <dd class="opt">
            <div class="div-image-1">
                      <img id="slider4_review" src="<?=@($data['slider4_image'.$subsite_suffix]['config_value'])?>" width="400" />
                    <a href="javascript:void(0)" id="del_btn4" class="del" onclick="delImage('slider4_review')" title="<?= __('移除'); ?>"><i class="iconfont icon-cancel"></i></a>
                </div>
                <input type="hidden" id="slider4_image" name="slider<?=$subsite_suffix?>[slider4_image<?=$subsite_suffix?>]" value="<?=@($data['slider4_image'.$subsite_suffix]['config_value'])?>" />
                <div  id='slider4_upload' class="image-line upload-image" ><?= __('图片上传'); ?></div>

           <label title="<?= __('请输入图片要跳转的链接地址'); ?>" class="mt10"><i class="fa fa-link"></i>
                <input class="ui-input w400" type="text" name="slider<?=$subsite_suffix?>[live_link4<?=$subsite_suffix?>]" value="<?=@($data['live_link4'.$subsite_suffix]['config_value'])?>" placeholder="<?= __('请输入图片要跳转的链接地址'); ?>">
           </label>
           <span class="err"><label for="live_link4" class="error valid"></label></span>
           <p class="notic"><?= __('请使用宽度'); ?>1043<?= __('像素，高度'); ?>396<?= __('像素的'); ?>jpg/gif/png<?= __('格式图片作为幻灯片'); ?>banner<?= __('上传，'); ?><br>
            <?= __('如需跳转请在后方添加以http://开头的链接地址。'); ?></p>
        </dd>
      </dl>

     <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
  </form>

    <script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>

    <script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
    <script>
        if (!$("#slider1_review").attr("src")) {
             $("#del_btn1").html('');
        }
        if (!$("#slider2_review").attr("src")) {
             $("#del_btn2").html('');
        }
        if (!$("#slider3_review").attr("src")) {
             $("#del_btn3").html('');
        }
        if (!$("#slider4_review").attr("src")) {
             $("#del_btn4").html('');
        }
       
$(function(){
    var agent = navigator.userAgent.toLowerCase();

    if ( agent.indexOf("msie") > -1 && (version = agent.match(/msie [\d]/), ( version == "msie 8" || version == "msie 9" )) ) {

        new UploadImage({
            thumbnailWidth: 1043,
            thumbnailHeight: 396,
            imageContainer: '#slider1_review',
            uploadButton: '#slider1_upload',
            inputHidden: '#slider1_image'
        });

        new UploadImage({
            thumbnailWidth: 1043,
            thumbnailHeight: 396,
            imageContainer: '#slider2_review',
            uploadButton: '#slider2_upload',
            inputHidden: '#slider2_image'
        });

        new UploadImage({
            thumbnailWidth: 1043,
            thumbnailHeight: 396,
            imageContainer: '#slider3_review',
            uploadButton: '#slider3_upload',
            inputHidden: '#slider3_image'
        });

        new UploadImage({
            thumbnailWidth: 1043,
            thumbnailHeight: 396,
            imageContainer: '#slider4_review',
            uploadButton: '#slider4_upload',
            inputHidden: '#slider4_image'
        });

    } else {
        //图片上传'); ?>
        $('#slider1_upload').on('click', function () {
            $.dialog({
                title: '<?= __('图片裁剪'); ?>',
                content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                data: {SHOP_URL:SHOP_URL,width:1043,height:396 , callback: callback1 },
                width: '800px',
                lock: true
            })
        });

        function callback1 ( respone , api ) {
            $('#slider1_review').attr('src', respone.url);
            $('#slider1_image').attr('value', respone.url);
            $("#del_btn1").html('<i class="iconfont icon-cancel"></i>');
            api.close();
        }

        $('#slider2_upload').on('click', function () {
            $.dialog({
                title: '<?= __('图片裁剪'); ?>',
                content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                data: {SHOP_URL:SHOP_URL,width:1043,height:396 , callback: callback2 },
                width: '800px',
                lock: true
            })
        });

        function callback2 ( respone , api ) {
            $('#slider2_review').attr('src', respone.url);
            $('#slider2_image').attr('value', respone.url);
            $("#del_btn2").html('<i class="iconfont icon-cancel"></i>');
            api.close();
        }

        $('#slider3_upload').on('click', function () {
            $.dialog({
                title: '<?= __('图片裁剪'); ?>',
                content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                data: {SHOP_URL:SHOP_URL,width:1043,height:396 , callback: callback3 },
                width: '800px',
                lock: true
            })
        });

        function callback3 ( respone , api ) {
            $('#slider3_review').attr('src', respone.url);
            $('#slider3_image').attr('value', respone.url);
            $("#del_btn3").html('<i class="iconfont icon-cancel"></i>');
            api.close();
        }


        $('#slider4_upload').on('click', function () {
            $.dialog({
                title: '<?= __('图片裁剪'); ?>',
                content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                data: {SHOP_URL:SHOP_URL,width:1043,height:396 , callback: callback4 },
                width: '800px',
                lock: true
            })
        });

        function callback4 ( respone , api ) {
            console.log(respone);
            $('#slider4_review').attr('src', respone.url);
            $('#slider4_image').attr('value', respone.url);
            $("#del_btn4").html('<i class="iconfont icon-cancel"></i>');
            api.close();
        }
    }
   })

     function delImage(imageId) {
        var $img = $("#" + imageId), $input;
        if (!$img.attr("src")) {
            return false;
        }
        $.dialog.confirm("<?= __('是否删除该图片'); ?>", function () {
            if (imageId == "slider1_review") {
                $input = $("#slider1_review").attr("src", "");
                $("#del_btn1").hide();
                $('#slider1_image').attr('value', '');
            }
            if (imageId == "slider2_review") {
                $input = $("#slider2_review");
                $("#del_btn2").hide();
                $('#slider2_image').attr('value', '');
            }
            if (imageId == "slider3_review") {
                $input = $("#slider3_review");
                $("#del_btn3").hide();
                $('#slider3_image').attr('value', '');
            }
            if (imageId == "slider4_review") {
                $input = $("#slider4_review");
                $("#del_btn4").hide();
                $('#slider4_image').attr('value', '');
            }
            $img.attr("src", "/shop_admin/static/common/images/image.png"), $input.attr("value", "");
        });
    }
</script>
    <?php
include $this->view->getTplPath() . '/' . 'footer.php';
    ?>