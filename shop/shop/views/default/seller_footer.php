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
</div>
</div>
</div>
<!-- <div class="footer">
    <div class="wrapper">
        <p class="about">
            <?php if (isset($this->bnav) && $this->bnav) {
                foreach ($this->bnav['items'] as $key => $nav) {
                    if ($key < 10) {
                        ?>
                        <a href="<?= $nav['nav_url'] ?>" <?php if ($nav['nav_new_open'] == 1){ ?>target="_blank"<?php } ?>><?= $nav['nav_title'] ?></a>
                    <?php } else {
                        return;
                    }
                }
            } ?>
        </p>
        <p class="copyright">
            <?= __(Web_ConfigModel::value('copyright')); ?>
        </p>
        <p class="statistics_code"><?php echo Web_ConfigModel::value('icp_number') ?></p>
    </div>
</div> -->
<script>
    $(function () {
        ucenterLogin(UCENTER_URL, SITE_URL, true);
    });
</script>
<?php include $this->view->getTplPath() . '/' . 'yf_im_config.php'; ?>
</body>
</html>