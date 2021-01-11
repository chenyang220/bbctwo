<script type="text/javascript" src="<?= $this->view->js ?>/buyer.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.datetimepicker.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.toastr.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.cookie.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/nav.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/base.js"></script>
<script src="<?= $this->view->js_com ?>/plugins/jquery.slideBox.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/tuangou-index.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/order.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/buyer.js"></script>
</div>
</div>
</div>
<div>
    <div class="wrapper">
        <p class="about">
            <?php if ($this->bnav) {
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
            <?php echo Web_ConfigModel::value('copyright'); ?>
        </p>
        <p class="statistics_code"><?php echo Web_ConfigModel::value('icp_number') ?></p>
    </div>
</div>
</div>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.ui.js"></script>
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js"></script>
<p class="statistics_code"><?php echo Web_ConfigModel::value('statistics_code') ?></p>
<script>
    $(function () {
        ucenterLogin(UCENTER_URL, SITE_URL, true);
    });
</script>
<?php include $this->view->getTplPath() . '/' . 'yf_im_config.php'; ?>
</body>
</html>