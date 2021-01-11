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
                        <label for="domain_modify_frequency"><?= __('标签名称：'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input  type="text"  class="ui-input w200" id="label_name"/>
                    </dd>
                </dl>
                <div class="bot" style="margin-left: 30px"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn buttons" ><?= __('确认提交'); ?></a></div>
            </div>
        </form>
    </div>
    
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
    <script>
        $(function () {
            $(".buttons").click(function () {
                var label_name = $("#label_name").val();
                $.ajax({
                    type: "POST",
                    url: SITE_URL +'?ctl=Shop_Label&met=addLabelset&typ=json',
                    data: {
                        label_name:label_name,
                    },
                    success: function(data){
                        if(data.status == 200)
                        {
                            parent.Public.tips({type:0, content : '修改成功！' });
                        } else {
                            parent.Public.tips({type:1, content : data.msg });
                        }
                    }
                });

             })
        })

    </script>
