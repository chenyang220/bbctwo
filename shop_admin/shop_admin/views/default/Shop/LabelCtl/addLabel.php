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
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
       <link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
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
        <form method="post" enctype="multipart/form-data" id="label_name_form" name="form1">
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label for="domain_modify_frequency"><em>*</em><?= __('标签名称：'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input  type="text"  class="ui-input w200" id="label_name"/>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="domain_modify_frequency"><em>*</em><?= __('标签排序'); ?></label>
                    </dt>
                    <dd class="opt">                    
                        <input id="label_tag_sort" name="label_tag_sort" value="" class="ui-input w400" type="text"/>
                        <p class="notic"><?= __('数字代表在特色商城首页的排序，数字越小越靠前'); ?></p>
                    </dd>
                </dl>
                <dl class="row banner_image">
                    <dt class="tit"></dt>
                    <dd class="opt show">
                        <img src="../shop_admin/static/common/images/image.png" id="label_image0" alt="<?= __('选择图片'); ?>" class="image-line" height="200"/>
                        <a href="javascript:;" class="del-img"><?= __('删除'); ?></a>
                        <input id="label_logo" value="" class="ui-input w400 img-path" type="hidden"/>
                        <p class="notic">建议尺寸：62*62px</p>
                        <div class="image-line" id="label_upload0"><?= __('上传图片'); ?>
                            <i class="iconfont icon-tupianshangchuan"></i>
                        </div>
                    </dd>
                </dl>
                <div class="bot" style="margin-left: 30px"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn buttons" ><?= __('确认提交'); ?></a></div>
            </div>
        </form>
    </div>
<script type="text/javascript" src="<?=$this->view->js_com?>/webuploader.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/models/upload_image.js" charset="utf-8"></script> 
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
    <script>
       logo_upload = new UploadImage({
            thumbnailWidth: 62,
            thumbnailHeight: 62,
            imageContainer: '#label_image0',
            uploadButton: '#label_upload0',
            inputHidden: '#label_logo'
        });
         //删除已上传的图片
        $(".del-img").click(function () {
            $(this).next().val('');
            $(this).prev().prop('src','../shop_admin/static/common/images/image.png');
        })
        $(function () {
            $(".buttons").click(function () {
                var label_name = $("#label_name").val();
                var label_logo = $("#label_logo").val();
                if (label_name == '') {
                    parent.Public.tips({type:1, content : "标签名称不能为空！" });
                    return false;
                }


                // if (label_logo == '') {
                //     parent.Public.tips({type:1, content : "标签logo不能为空！" });
                //     return false;
                // }


                $.ajax({
                    type: "POST",
                    url: SITE_URL +'?ctl=Shop_Label&met=addLabelset&typ=json',
                    data: {
                        label_name:label_name,
                        label_tag_sort: $("#label_tag_sort").val(),
                        label_logo: label_logo
                    },
                    success: function(data){
                        if(data.status == 200)
                        {
                            parent.Public.tips({type:0, content :  data.msg });
                        } else {
                            parent.Public.tips({type:1, content : data.msg });
                        }
                    }
                });

             })
        })

    </script>
