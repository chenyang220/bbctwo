<?php if (!defined('ROOT_PATH')) exit('No Permission');?>
<?php
include TPL_PATH . '/'  . 'header.php';
?>

    </head>


    <div class="wrapper page">

        <p class="warn_xiaoma"><span></span><em></em></p>
        <div class="explanation" id="explanation">
            <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
                <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
                <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn">X</em>
            </div>
            <ul>
                <li></li>
                <li></li>
            </ul>
        </div>
        <div class="fixed-bar">
            <div class="item-title">
                <div class="subject">
                    <h3><?= __('管理与详情'); ?></h3>
                    <h5><?= __('购物卡管理与详情'); ?></h5>
                </div>
                <ul class="tab-base nc-row">
                    <li><a  href="index.php?ctl=Paycen_PayCard&met=index"><?= __('购物卡管理'); ?></a></li>
                    <li><a class="current" href="index.php?ctl=Paycen_PayCard&met=payCard"><?= __('管理与详情'); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="ncap-form-default">
            <div class="mod-search cf">
                <div class="fl">
                    <ul class="ul-inline">
                        <li>
                            <span id="source" value="<?= __('请选择卡号'); ?>"></span>
                            <label><?= __('卡片生成时间'); ?>:</label>
                            <input type="text" id="beginDate" class="ui-input ui-datepicker-input">
                        </li>
                        <li><a class="ui-btn" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                    </ul>
                </div>
                <div class="fr">
                    <a href="javascript:void(0)" class="ui-btn" id="export"><?= __('导出'); ?><i class="iconfont icon-btn04"></i></a>
                    <a href="#" class="ui-btn ui-btn-sp mrb" id="add"><?= __('生成'); ?><i class="iconfont icon-btn03"></i></a>
                    <!--            <a href="javascript:void(0)" class="ui-btn" id="btn-batchDel"><?= __('删除'); ?><i class="iconfont icon-bin"></i></a>-->
                </div>
            </div>
            <div class="grid-wrap">
                <table id="grid">
                </table>
                <div id="page"></div>
            </div>
        </div>
    </div>

<?php

$paydata = array(array('id'=>'','name'=>__('请选择卡号')));
foreach($data['items'] as $key=>$val){
    $item = array();
    $item['id'] = $val['card_id'];
    $item['name'] = $val['card_id'];
    $paydata[]= $item;
}

?>
<script>
    var card_row = <?php echo json_encode($paydata)?>;
    var card_list_row={
        data: card_row,
        value: "id",
        text: "name",
        width: 180
    }
</script>
    <script src="./admin/static/default/js/controllers/paycard/card_list.js"></script>
<?php
include TPL_PATH . '/'  . 'footer.php';
?>