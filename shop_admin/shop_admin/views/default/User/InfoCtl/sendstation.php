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
                    <!-- <li><a class="current"><span><?= __('设置'); ?></span></a></li>
                <li><a href="<?= Yf_Registry::get('url') ?>?ctl=Shop_Domain&met=indexs"><span><?= __('域名列表'); ?></span></a></li> -->

                </ul>
            </div>
        </div>
        <!-- <?= __('操作说明'); ?> -->
        <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
                <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
                <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em> </div>
            <ul>
                <?= __($menus['this_menu']['menu_url_note']); ?>
            </ul>
        </div>

        <form method="post" enctype="multipart/form-data" id="shop_domain_form" name="form1">
            <input type="hidden" name="config_type[]" value="domain"/>

            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label for="domain_modify_frequency"><?= __('收件人：'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input  type="text"  class="ui-input w400" id="Sender"/>

                        <p class="notic"><?= __('请输入收件人名称'); ?></p>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label for="domain_modify_frequency"><?= __('发送给全部：'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input type="checkbox" value="0"  onclick="this.value=(this.value==0)?1:0"  id="SenderAll"/>
                        <p class="notic"><?= __(''); ?></p>
                    </dd>
                </dl>

                <dl class="row">
                    <dt class="tit">
                        <label for="retain_domain"><?= __('发送内容：'); ?></label>
                    </dt>
                    <dd class="opt">
                        <textarea maxlength="200" rows='10' cols='80'id ='send_content'>

                        </textarea>
                        <p class="notic"><?= __('最多输入200个字'); ?></p>
                    </dd>
                </dl>

                <div class="bot" style="margin-left: 30px"> <a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn buttons" ><?= __('确认提交'); ?></a></div>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
    <script>
        $(function () {
            $(".buttons").click(function () {
                var send_man = $("#Sender").val();
                var send_content = $("#send_content").val();
                var SenderAll = $("#SenderAll").val();
                $.ajax({
                    type: "GET",
                    url: SITE_URL +'?ctl=User_Info&met=addsendstation&typ=json',
                    data: {
                        send_man:send_man,
                        send_content:send_content,
                        SenderAll:SenderAll,
                    },
                    success: function(data){
                        if(data.status == 200)
                        {
                            parent.Public.tips({type:0, content : '修改成功！' });
                        }
                    }
                });

             })
        })

    </script>
