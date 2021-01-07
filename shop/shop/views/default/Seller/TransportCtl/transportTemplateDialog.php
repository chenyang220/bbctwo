<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<!DOCTYPE HTML>
<html>
<head>
    <link href="<?= $this->view->css ?>/seller.css?ver=<?= VER ?>" rel="stylesheet">
    <link href="<?= $this->view->css ?>/iconfont/iconfont.css?ver=<?= VER ?>" rel="stylesheet" type="text/css">
    <link href="<?= $this->view->css ?>/seller_center.css?ver=<?= VER ?>" rel="stylesheet">
    <link href="<?= $this->view->css ?>/base.css?ver=<?= VER ?>" rel="stylesheet">
    <script type="text/javascript" src="<?=$this->view->js_com?>/jquery.js" charset="utf-8"></script>
</head>
<body>

<div class="dialog_content" style="margin: 0px; padding: 0px;">
    <div class="eject_con">
        <div class="adds" style=" min-height:240px;">
            <table class="ncsc-default-table">
                <thead>
                <tr>
                    <th class="w200"><?=__('运费模板名称')?></th>
                    <th class="w200">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if ( !empty($Transport_Template_List) ) { ?>
                    <?php foreach ( $Transport_Template_List as $key => $val ) { ?>
                        <tr class="bd-line">
                            <td class="tc"><?= $val['name']; ?></td>
                            <td class="tc">
                                <a href="javascript:void(0);" nc_type="select" class="ncbtn bbc_seller_btns" data-transport_template_name="<?= $val['name']; ?>" data-transport_template_id="<?= $val['id']; ?>"
                                  data-transport_template_type="<?= $val['rule_type']; ?>"  ><?=__('选择')?></a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>

<script>

    api = frameElement.api;
    callback_template = api.data.callback_template;
    $(function () {
        $('a[nc_type="select"]').on('click', function () {
            if ( typeof callback_template == 'function' ) {
                var data = { transport_template_name: $(this).data('transport_template_name'), transport_template_id: $(this).data('transport_template_id'), transport_template_type: $(this).data('transport_template_type') };
                callback_template(data, api);
            }
        })
    })
</script>