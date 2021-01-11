<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
    <div class="pc_user_about">
        <div class="recharge-content-top content-public clearfix">
            <ul class="tab">
                <li class="active"><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btinfo"><?=__('白条概览')?></a></li>
                <li><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btbill"><?=__('白条账单')?></a></li>
                <li><a href="<?=Yf_Registry::get('url')?>?ctl=Info&met=btrefund"><?=__('白条还款')?></a></li>
            </ul>
        </div>
      <div class="wrap">
          <div class="white-strip">
              <h3 class="tc white-strip-tit"><?=$bt_name?></h3>
             <div class="white-strip-apply-text">
                 <?=$bt_statement?>
             </div>
              <label class="block white-strip-agreement"><input type="checkbox"><span>同意此白条说明</span></label>
              <div class="pc_trans_btn"><a href="javascript:next(2);" class="btn_big btn_active">下一步，填写企业资质信息</a></div>
          </div>
        </div>
    </div>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
<script type="text/javascript">
  function next(step){
    var select = $('input[type=checkbox]').prop("checked");
    if (!select) {
       Public.tips.error("<?=__('请同意此白条说明')?>");
       return false;
    }
    window.location.href = '<?=Yf_Registry::get('url')?>' +'?ctl=Info&met=btapplication';
  }
</script>



