<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link href="<?=$this->view->css?>/index.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="<?=$this->view->css_com?>/jquery/plugins/validator/jquery.validator.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/ueditor/lang/zh-cn/zh-cn.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$this->view->js_com?>/ueditor/themes/default/css/ueditor.min.css"">
</head>
<body>
<div class="wrapper page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?= __('白条审核'); ?></h3>
        <h5><?= __('相关白条激活申请列表'); ?></h5>
      </div>
       <ul class="tab-base nc-row">
          <li><a href="index.php?ctl=Paycen_BtInfo&met=index"><?= __('白条审核'); ?></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtLimit"><?= __('白条信用额度设置'); ?></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtReturn"><?= __('白条收款确认'); ?></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtReturnList"><?= __('还款明细'); ?></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtOrderList"><span><?= __('白条订单明细'); ?></span></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtWarnList"><?= __('白条提醒'); ?></a></li>
          <li><a class="current"><?= __('白条声明'); ?></a></li>
      </ul>
    </div>
  </div>
    <!-- <?= __('操作说明'); ?> -->
    <p class="warn_xiaoma"><span></span><em></em></p><div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="iconfont icon-lamp"></i>
            <h4 title="<?= __('提示相关设置操作时应注意的要点'); ?>"><?= __('操作提示'); ?></h4>
            <span id="explanationZoom" title="<?= __('收起提示'); ?>"></span><em class="close_warn">X</em></div>
        <ul>
            <li><?= __('白条申明明细。'); ?></li>
        </ul>
    </div>

    <form method="post" enctype="multipart/form-data" id="ht-setting-form" name="form">
        <input type="hidden" name="config_type[]" value="bt"/>
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="bt_name"><?= __('申明名称'); ?></label>
                </dt>
                <dd class="opt">
                    <input id="bt_name" name="bt[bt_name]" value="<?=($data['bt_name']['config_value'])?>" class="ui-input w346" type="text"/>

                    <p class="notic"><?= __('白条申明名称，必填'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="bt_statement"> <?= __('申明详情'); ?></label>
                </dt>
                <dd class="opt">
                    <textarea id="bt_statement" name="bt[bt_statement]" type="text/plain"></textarea>
                    <p class="notic"><?= __('申请白条者必须同意申明才能申请白条。'); ?></p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="bt_time"><?= __('更新时间'); ?></label>
                </dt>
                <dd class="opt">
                    <input type="hidden" name="bt[bt_time]" value="<?php echo date("Y-m-d H:i");?>">
                   <span><?=($data['bt_time']['config_value'])?></span>
                </dd>
            </dl>
        </div>
        <div class="bot"><a href="javascript:void(0);" class="ui-btn ui-btn-sp submit-btn"><?= __('确认提交'); ?></a></div>
    </form>
</div>
</script>
<script type="text/javascript" src="<?=$this->view->js?>/controllers/config.js" charset="utf-8"></script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
<!-- <?= __('实例化编辑器'); ?> -->
<script type="text/javascript">
    //数据初始化
    var bt_statement = '<?=($data['bt_statement']['config_value'])?>';
    var ue = UE.getEditor('bt_statement', {
        initialFrameWidth: '65%',
        initialFrameHeight: 600,
        toolbars: [
            [
             'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'justifyleft', 'justifycenter', 'justifyright', 'insertunorderedlist', 'insertorderedlist', 'link', 'removeformat'
            ]
        ],
        autoClearinitialContent: true,
        //关闭字数统计'); ?>
        wordCount: false,
        //关闭'); ?>elementPath
        elementPathEnabled: false,
        zIndex:1
    });
   //编辑器初始化完成再赋值
   ue.ready(function() {
        ue.setContent(bt_statement);  //赋值给UEditor
    });
</script>