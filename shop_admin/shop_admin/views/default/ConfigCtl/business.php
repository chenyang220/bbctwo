<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';

// 当前管理员权限
$admin_rights = $this->getAdminRights();
// 当前页父级菜单，同级菜单，当前菜单
$menus = $this->getThisMenus();

?>
    <link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
    <link href="<?= $this->view->css_com ?>/webuploader.css" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css_com ?>/jquery/plugins/datepicker/dateTimePicker.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    <script src="<?= $this->view->js_com ?>/plugins/jquery.datetimepicker.js"></script>
    </head>
    <body>
    <div class="wrapper page">
        <div class="fixed-bar">
            <div class="item-title">
                <div class="subject">
                    <h3><?= __($menus['father_menu']['menu_name']); ?></h3>
                    <h5><?= __($menus['father_menu']['menu_url_note']); ?></h5>
                </div>
                <ul class="tab-base nc-row">
                    <?php
                    foreach ($menus['brother_menu'] as $key => $val) {
                        if (in_array($val['rights_id'], $admin_rights) || $val['rights_id'] == 0) {
                            ?>
                            <li>
                                <a <?php if (!array_diff($menus['this_menu'], $val)) { ?> class="current"<?php } ?> href="<?= Yf_Registry::get('url') ?>?ctl=<?= $val['menu_url_ctl'] ?>&met=<?= $val['menu_url_met'] ?><?php if ($val['menu_url_parem']) { ?>&<?= $val['menu_url_parem'] ?><?php } ?>">
                                    <span><?= __($val['menu_name']); ?></span>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>

                    <?php
                    $image = Yf_Registry::get('url') . '/../shop_admin/static/default/images/image.png';
                    $business_license_electronic = $data['business_license_electronic']['config_value'] ? $data['business_license_electronic']['config_value'] : $image;
                    ?>

                </ul>
            </div>
        </div>

        <p class="warn_xiaoma"><span></span><em></em></p>
        <div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
                <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
                <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn iconfont icon-guanbifuzhi"></em></div>
            <ul>
                <?= __($menus['this_menu']['menu_url_note']); ?>
            </ul>
        </div>

        <form id="business-setting-form" method="post" enctype="multipart/form-data" name="settingForm">
            <input type="hidden" name="config_type[]" value="business"/>
            <div class="ncap-form-default">
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('企业名称'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="shop_company_name" name="business[shop_company_name]" value="<?= $data['shop_company_name']['config_value'] ?>" class="ui-input w400" type="text" placeholder="请输入企业名称">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('营业执照注册号'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="business_id" name="business[business_id]" value="<?= $data['business_id']['config_value'] ?>" class="ui-input w400" type="text" placeholder="请输入营业执照注册号">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('法人代表人姓名'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="legal_person" name="business[legal_person]" value="<?= $data['legal_person']['config_value'] ?>" class="ui-input w400" type="text" placeholder="请输入法人代表人姓名">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('营业执照所在地'); ?></label>
                    </dt>
                    <dd class="opt">
                        <div id="address" <?php if ($data['business_license_location']['config_value']) { ?> style="display:none"<?php } ?> >
                            <select id="select_1" name="select_1" onChange="getDistrict(1,$(this).val());" class="ui-select"></select>
                            <select id="select_2" name="select_2" onChange="getDistrict(2,$(this).val());" class="ui-select" style="display:none"></select>
                            <select id="select_3" name="select_3" onChange="getDistrict(3,$(this).val());" class="ui-select" style="display:none"></select>
                            <select id="select_4" name="select_4" onChange="getDistrict(4,$(this).val());" class="ui-select" style="display:none"></select>
                        </div>

                        <?php if ($data['business_license_location']['config_value']) { ?>
                            <input id="business_license_location" name="business[business_license_location]" value="<?= $data['business_license_location']['config_value'] ?>" class="ui-input w400" type="text" readonly>
                            <span id="edit"><?= __('编辑地址'); ?></span>
                        <?php } else { ?>
                            <input id="business_license_location" name="business[business_license_location]" value="" class="ui-input w400" type="hidden">
                        <?php } ?>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('企业注册资金'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="company_registered_capital" name="business[company_registered_capital]" value="<?= $data['company_registered_capital']['config_value'] ?>" class="ui-input w400" type="text" placeholder="请输入企业注册资金：万元">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('营业执照有效期'); ?></label>
                    </dt>
                    <dd class="opt">
                        <input id="business_licence_start" name="business[business_licence_start]" value="<?= $data['business_licence_start']['config_value'] ?>" class="ui-input ui-datepicker-input" type="text" placeholder="营业执照有效期">
                        <input id="business_licence_end" name="business[business_licence_end]" value="<?= $data['business_licence_end']['config_value'] ?>" class="ui-input ui-datepicker-input" type="text" placeholder="营业执照有效期">
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('公司地址'); ?></label>
                    </dt>
                    <dd class="opt">
                        <textarea id="company_address_detail" name="business[company_address_detail]" placeholder="请输入公司地址"><?= $data['company_address_detail']['config_value'] ?></textarea>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('营业执照经营范围'); ?></label>
                    </dt>
                    <dd class="opt">
                        <textarea id="business_sphere" name="business[business_sphere]" placeholder="请输入营业执照经营范围"><?= $data['business_sphere']['config_value'] ?></textarea>
                    </dd>
                </dl>
                <dl class="row">
                    <dt class="tit">
                        <label><em>*</em><?= __('营业执照'); ?></label>
                    </dt>
                    <dd class="opt">
                        <img id="business_license_electronic" name="business_license_electronic" alt="<?= __('选择图片'); ?>" src="<?= $business_license_electronic ?>">
                        <div class="image-line upload-image" id="business_license_electronic_upload"><?= __('上传图片'); ?></div>
                        <input id="business_license_electronic_code_img" name="business[business_license_electronic]" value="<?= $data['business_license_electronic']['config_value'] ?>" class="ui-input w400" type="hidden">
                        <!-- <div class="notic"><?= __('选择上传'); ?>...<?= __('选择文件建议大小'); ?>90px*90px</div> -->
                    </dd>
                </dl>

                <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/webuploader.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/models/upload_image.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/controllers/config.js" charset="utf-8"></script>
    <script type="text/javascript">
        var business_license_electronic_upload = new UploadImage({
            thumbnailWidth: 750,
            thumbnailHeight: 450,
            imageContainer: '#business_license_electronic',
            uploadButton: '#business_license_electronic_upload',
            inputHidden: '#business_license_electronic_code_img'
        });

        var i = 1;
        getDistrict(0, 0);

        function getDistrict(level, nodeid) {
            if (nodeid == '-1') {
                $('#select_2').html('');
                $('#select_2').hide();
                $('#select_3').html('');
                $('#select_3').hide();
                $('#select_4').html('');
                $('#select_4').hide();
                return;
            }

            var next_level = level + 1;
            $.post(SITE_URL + '?ctl=Base_District&met=district&typ=json&nodeid=' + nodeid, function (b) {
                if (b.status == 200 && b.data.items.length > 0) {
                    $('#select_' + next_level).show();
                    $('#select_' + next_level).html('');
                    if (level == 1) {
                        $('#select_3').html('');
                        $('#select_3').hide();
                        $('#select_4').html('');
                        $('#select_4').hide();
                    }
                    if (level == 2) {
                        $('#select_4').html('');
                        $('#select_4').hide();
                    }
                    $('#select_' + next_level).append('<option value="-1">--<?= __('请选择'); ?>--</option>');
                    $.each(b.data.items, function (i, v) {
                        $('#select_' + next_level).append('<option value="' + v.district_id + '">' + v.district_name + '</option>');
                    });
                    if (level > 0) {
                        $("#business_license_location").val('');
                    }
                }
            }, 'json');

            if (level > 0) {
                var info = '';
                $("select[name^=select_]").each(function () {
                    if ($(this).find("option:selected").val() > 0) {
                        info += $(this).find("option:selected").html() + " ";
                    }
                });
                $("#business_license_location").val(info);
            }
        }

        $("#edit").click(function () {
            $("#address").show();
            $("#business_license_location").hide();
        })

        $('#business_licence_start').datetimepicker({lang: 'ch'}).prop('readonly', 'readnoly');
        $('#business_licence_end').datetimepicker({lang: 'ch'}).prop('readonly', 'readnoly');
        $('#business_licence_start').datetimepicker({
            controlType: 'select',
            format: "Y-m-d",
            timepicker: false
        });

        $('#business_licence_end').datetimepicker({
            controlType: 'select',
            format: "Y-m-d",
            timepicker: false,
            onShow: function (ct) {
                this.setOptions({
                    minDate: ($('#business_licence_start').val() && (new Date(Date.parse($('#business_licence_start').val().replace(/-/g, "/")))) > (new Date())) ? (new Date(Date.parse($('#business_licence_start').val().replace(/-/g, "/")))) : (new Date())
                })
            }
        });

    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>