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
        <h3><?= __('白条审核'); ?></h3>
        <h5><?= __('相关白条激活申请列表'); ?></h5>
      </div>
       <ul class="tab-base nc-row">
          <li><a href="index.php?ctl=Paycen_BtInfo&met=index"><?= __('白条审核'); ?></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtLimit"><?= __('白条信用额度设置'); ?></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtReturn"><?= __('白条收款确认'); ?></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtReturnList"><?= __('还款明细'); ?></a></li>
          <li><a class="current"><span><?= __('白条订单明细'); ?></span></a></li>
          <li><a href="index.php?ctl=Paycen_BtInfo&met=setBtWarnList"><?= __('白条提醒'); ?></a></li>
          <li><a href="index.php?ctl=Config&met=bt"><?= __('白条声明'); ?></a></li>
      </ul>
    </div>
  </div>
  <div class="ncap-form-default">

    <div class="mod-search cf">
    <div class="fl">
      <ul class="ul-inline">
<!--            <li>
                <span id="status"></span>
            </li>-->
            <li>
                <span id="searchName"></span>
            </li>
            <li>
              <input type="text" id="searchContent" class="ui-input ui-input-ph con" value="">
            </li>
            <li>
              <label><?= __('订单日期'); ?>:</label>
              <input type="text" id="beginDate" value="" class="ui-input ui-datepicker-input">
              <i>-</i>
              <input type="text" id="endDate" value="" class="ui-input ui-datepicker-input">
            </li>
        <li><a class="ui-btn" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
      </ul>
    </div>
    <div class="fr">
        <a class="ui-btn" id="btn-excel" data-action="all"><?= __('导出白条订单'); ?><i class="iconfont icon-btn04"></i></a>
     </div>
  </div>
  <div class="grid-wrap">
      <table id="grid">
      </table>
      <div id="page"></div>
  </div>
  </div>
  
</div>
<script src="./admin/static/default/js/controllers/btinfo/btorder_list.js"></script>
<?php
include TPL_PATH . '/'  . 'footer.php';
?>