<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<script type="text/javascript">
    $(".dropdown").hover(function () {
        $(this).addClass("hover");
    }, function () {
        $(this).removeClass("hover");
    });
    
    $(".js-sitemap").on("click", function () {
        $(".js-menu-arrow, .sitemap-menu").show();
    });
    
    $("#closeSitemap").on("click", function () {
        $(".js-menu-arrow, .sitemap-menu").hide();
    });
</script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.ui.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/seller.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.datetimepicker.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.toastr.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js"></script>

<script>
    $(function () {
        ucenterLogin(UCENTER_URL, SITE_URL, true);
    });
</script>
<?php include $this->view->getTplPath() . '/' . 'yf_im_config.php'; ?>
</body>
</html>