<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();

?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>

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
    
    <form method="post" enctype="multipart/form-data" id="sellerWx-setting-form" name="form">
        <input type="hidden" name="config_type[]" value="seller_wx"/>

        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label><?= __('购买价格（元）/年'); ?></label>
                </dt>
                <dd class="opt">
					<input id="sellerWx_price" name="seller_wx[sellerWx_price]" value="<?=($data['sellerWx_price']['config_value'])?>" class="ui-input w40" type="text"/>
                    <p class="notic"><?= __('开通公众号权限所需费用，商家开启后可以绑定自己的公众号'); ?></p>
                </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label><?= __('设置即将到期时间'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="sellerWx_price" name="seller_wx[sellerWx_day]" value="<?=($data['sellerWx_day']['config_value'])?>" class="ui-input w40" type="text"/>
                    <p class="notic"><?= __('设置距终止时间多少天，属于即将到期'); ?></p>
                </dd>
            </dl>

            <dl class="row">
                    <dt class="tit">
                        <label><?= __('微信收款码'); ?></label>
                    </dt>
                    <dd class="opt">
                        <img id="wxcode_logo_image" name="seller_wx[sellerWx_wxcode]" alt="<?= __('选择图片'); ?>" src="<?=($data['sellerWx_wxcode']['config_value'])?>" width="200px" height="200px" />

                        <div class="image-line upload-image"  id="wxcode_logo_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                        <input id="sellerWx_wxcode"  name="seller_wx[sellerWx_wxcode]" value="<?=($data['sellerWx_wxcode']['config_value'])?>" class="ui-input w400" type="hidden"/>
                        
                    </dd>
            </dl>

             <dl class="row">
                    <dt class="tit">
                        <label><?= __('支付宝收款码'); ?></label>
                    </dt>
                    <dd class="opt">
                        <img id="alicode_logo_image" name="seller_wx[sellerWx_alicode]" alt="<?= __('选择图片'); ?>" src="<?=($data['sellerWx_alicode']['config_value'])?>" width="200px" height="200px" />

                        <div class="image-line upload-image"  id="alicode_logo_upload"><?= __('上传图片'); ?><i class="iconfont icon-tupianshangchuan"></i></div>

                        <input id="sellerWx_alicode"  name="seller_wx[sellerWx_alicode]" value="<?=($data['sellerWx_alicode']['config_value'])?>" class="ui-input w400" type="hidden"/>
                       
                    </dd>
            </dl>

            <dl class="row">
                <dt class="tit">
                    <label><?= __('开户行'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="sellerWx_bank" name="seller_wx[sellerWx_bank]" value="<?=($data['sellerWx_bank']['config_value'])?>" class="ui-input w120" type="text"/>
                    
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('银行账号'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="sellerWx_number" name="seller_wx[sellerWx_number]" value="<?=($data['sellerWx_number']['config_value'])?>" class="ui-input w120" type="text"/>
                    
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label><?= __('收款人'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="sellerWx_user" name="seller_wx[sellerWx_user]" value="<?=($data['sellerWx_user']['config_value'])?>" class="ui-input w120" type="text"/>
                    
                </dd>
            </dl>
          <div class="bot"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
 <script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
    <script>
        $(function(){

            function uploadImage() {
                var setting_logo_upload = new UploadImage({
                    thumbnailWidth: 600,
                    thumbnailHeight: 600,
                    imageContainer: '#wxcode_logo_image',
                    uploadButton: '#wecode_logo_upload',
                    inputHidden: '#sellerWx_wxcode'
                });

                var seller_logo_upload = new UploadImage({
                    thumbnailWidth: 600,
                    thumbnailHeight: 600,
                    imageContainer: '#alicode_logo_image',
                    uploadButton: '#alicode_logo_upload',
                    inputHidden: '#sellerWx_alicode'
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

                $('#wxcode_logo_upload, #alicode_logo_upload').on('click', function () {

                    if ( this.id == 'wxcode_logo_upload' ) {
                        $imagePreview = $('#wxcode_logo_image');
                        $imageInput = $('#sellerWx_wxcode');
                        imageWidth = 600, imageHeight = 600;
                    } else{
                        $imagePreview = $('#alicode_logo_image');
                        $imageInput = $('#sellerWx_alicode');
                        imageWidth = 600, imageHeight = 600;
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