<?php if (!defined('ROOT_PATH'))
{
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'seller_header.php';
?>
<div class="tabmenu">
    <ul class="tab pngFix">
        <li class="active bbc_seller_bg"><a><?=__('消费者保障服务')?></a></li>
    </ul>
</div>
<div class="ncsc-form-default">
	<h2 class="close-text-tips"><?=__('平台关闭了消费者保障服务')?></h2>
</div>
<link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css" rel="stylesheet">
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/jquery.js" charset="utf-8"></script>
<script>
    // setTimeout('window.history.back()', 3000);
</script>

<?php
include $this->view->getTplPath() . '/' . 'seller_footer.php';
?>