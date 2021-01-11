<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();


?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
</head>
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
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
        <ul>
            <?= __($menus['this_menu']['menu_url_note']); ?>
        </ul>
    </div>
    
    <form method="post" id="dump-setting-form" name="settingForm">
        <input type="hidden" name="config_type[]" value="goods"/>
        <input type="hidden" name="config_type[]" value="photo"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit"><?= __('商品是否需要审核'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="goods_verify_flag1" name="goods[goods_verify_flag]" value="1" type="radio" <?=($data['goods_verify_flag']['config_value']==1 ? 'checked' : '')?> >
						<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['goods_verify_flag']['config_value']==1 ? 'selected' : '')?> " for="goods_verify_flag1"><?= __('开启'); ?></label>

                        <input id="goods_verify_flag0" name="goods[goods_verify_flag]" value="0" type="radio"  <?=($data['goods_verify_flag']['config_value']==0 ? 'checked' : '')?> >
						<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['goods_verify_flag']['config_value']==0 ? 'selected' : '')?>" for="goods_verify_flag0"><?= __('关闭'); ?></label>
                    </div>

                    <p class="notic"></p>
                </dd>
            </dl>
            
            <dl class="row">
                <dt class="tit"><?= __('收取分销商佣金'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="goods_commission1" name="goods[goods_commission]" value="1" type="radio" <?=($data['goods_commission']['config_value']==1 ? 'checked' : '')?> >
						<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['goods_commission']['config_value']==1 ? 'selected' : '')?> " for="goods_commission1"><?= __('开启'); ?></label>

                        <input id="goods_commission0" name="goods[goods_commission]" value="0" type="radio"  <?=($data['goods_commission']['config_value']==0 ? 'checked' : '')?> >
						<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['goods_commission']['config_value']==0 ? 'selected' : '')?>" for="goods_commission0"><?= __('关闭'); ?></label>
                    </div>

                    <p class="notic"><?= __('针对分销商从供应商同步的代发货商品，买家购买商品时，是否收取分销商的佣金。'); ?></p>
                </dd>
            </dl>
            
            <dl class="row">
                <dt class="tit"><?= __('收取供货商佣金'); ?></dt>
                <dd class="opt">
                    <div class="onoff">
                        <input id="supplier_commission1" name="goods[supplier_commission]" value="1" type="radio" <?=($data['supplier_commission']['config_value']==1 ? 'checked' : '')?> >
						<label title="<?= __('开启'); ?>" class="cb-enable <?=($data['supplier_commission']['config_value']==1 ? 'selected' : '')?> " for="supplier_commission1"><?= __('开启'); ?></label>

                        <input id="supplier_commission0" name="goods[supplier_commission]" value="0" type="radio"  <?=($data['supplier_commission']['config_value']==0 ? 'checked' : '')?> >
						<label title="<?= __('关闭'); ?>" class="cb-disable <?=($data['supplier_commission']['config_value']==0 ? 'selected' : '')?>" for="supplier_commission0"><?= __('关闭'); ?></label>
                    </div>

                    <p class="notic"><?= __('针对分销商从供应商同步的代发货商品，分销商向供应商同步下单时，是否收取供应商的佣金。'); ?></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label><?= __('默认商品图片'); ?></label>
                </dt>
                <dd class="opt">
                    <img id="photo_goods_image" name="photo[photo_goods_logo]" alt="<?= __('选择图片'); ?>" src="<?php if ($data['photo_goods_logo']['config_value']){echo $data['photo_goods_logo']['config_value'];}else{ echo '../shop_admin/static/common/images/image.png';} ?>" width="300px" height="300px"/>

                    <div class="image-line upload-image" id="photo_goods_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                    <input id="photo_goods_logo" name="photo[photo_goods_logo]" value="<?php if ($data['photo_goods_logo']['config_value']){echo $data['photo_goods_logo']['config_value'];}else{ echo '../shop_admin/static/common/images/image.png';} ?>" class="ui-input w400" type="hidden"/>
                    <div class="notic"><?= __('默认商品图片'); ?>,<?= __('最佳显示尺寸为'); ?>300*300<?= __('像素'); ?></div>
                </dd>
            </dl>
            
            <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<script>
    $(function(){

        function uploadImage() {
            var photo_goods_upload = new UploadImage({
                thumbnailWidth: 300,
                thumbnailHeight: 300,
                imageContainer: "#photo_goods_image",
                uploadButton: "#photo_goods_upload",
                inputHidden: "#photo_goods_logo"
            });
        }


        var agent = navigator.userAgent.toLowerCase();

        if ( agent.indexOf("msie") > -1 && (version = agent.match(/msie [\d]/), ( version == "msie 8" || version == "msie 9" )) ) {
            uploadImage();
        } else {
            cropperImage();
        }

        function cropperImage() {
            var $imagePreview, $imageInput, imageWidth, imageHeight;

            $('#photo_goods_upload').on('click', function () {

                if (this.id == "photo_goods_upload") {
                    $imagePreview = $("#photo_goods_image");
                    $imageInput = $("#photo_goods_logo");
                    imageWidth = 300, imageHeight = 300;
                }

                $.dialog({
                    title: '<?= __('图片裁剪'); ?>',
                    content: "url: <?= Yf_Registry::get('url') ?>?ctl=Index&met=cropperImage&typ=e",
                    data: { SHOP_URL: SHOP_URL, width: imageWidth, height: imageHeight, callback: callback },
                    width: '800px',
                    lock: true
                })
            });

            function callback ( respone , api ) {
                $imagePreview.attr('src', respone.url);
                $imageInput.attr('value', respone.url);
                api.close();
            }
        }

    })
</script>


<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>