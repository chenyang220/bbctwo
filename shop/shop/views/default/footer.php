<div class="footer">
    <div class="wrapper">
        <?php if (!$this->ctl == "Seller_Shop_Settled") { ?>
            <div class="promise">
                <div><strong class="bbc_color"><?= __('七天退货') ?></strong></div>
                <div><strong class="bbc_color"><?= __('正品保障') ?></strong></div>
                <div><strong class="bbc_color"><?= __('闪电发货') ?></strong></div>
                <div><strong class="bbc_color"><?= __('满额免邮') ?></strong></div>
            </div>
        <?php } ?>
        <ul class="services clearfix">
            <?php if (!empty($this->foot)):
                $i = 1;
                $j = 0;
                $Article_BaseModel = new Article_BaseModel();
                foreach ($this->foot as $key => $value):
                    $j++;
                    if ($j > 6) {
                        break;
                    }
                    ?>
                    <li>
                        <h5><span><?= __($value['group_name']); ?></span></h5>
                        <?php
                        if (!empty($value['article'])):
                            foreach ($value['article'] as $k => $v):
                                ?>
                                <?php if ($v['article_status'] == $Article_BaseModel::ARTICLE_STATUS_TRUE) { ?>
                                <?php if (!empty($v['article_url'])) { ?>
                                    <p>
                                        <a href="<?= $v['article_url'] ?>"><?= __($v['article_title']); ?></a>
                                    </p>
                                <?php } else { ?>
                                    <p>
                                        <a href="<?= url('Article_Base/index', ['article_id' => $v['article_id']]) ?>"><?= __($v['article_title']); ?></a>
                                    </p>
                                <?php } ?>
                            <?php } ?>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    </li>
                    <?php
                    $i++;
                endforeach;
            endif; ?>
        </ul>
        <p class="about">
            <?php if (isset($this->bnav) && $this->bnav) {
                foreach ($this->bnav['items'] as $key => $nav) {
                    if ($key < 10) {
                        ?>
                        <a href="<?= $nav['nav_url'] ?>" <?php if ($nav['nav_new_open'] == 1){ ?>target="_blank"<?php } ?>><?= __($nav['nav_title']); ?></a>
                    <?php } else {
                        return;
                    }
                }
            } ?>
        </p>
        <p class="copyright">
            <?= __(Web_ConfigModel::value('copyright')); ?>
        </p>
        <?php if (Web_ConfigModel::value('shop_company_name')) { ?>
            <p class="copyright">
                <a href="<?= url('Shop/getCompany', ['id' => '001', 'from' => 'plat']) ?>"><?= __('营业执照'); ?></a>
            </p>
        <?php } ?>
        <p class="statistics_code"><?= __(Web_ConfigModel::value('icp_number')); ?></p>
    </div>
</div>
</div>
<?php
$ctl = request_string('ctl');
$met = request_string('met');

$request_uri = $ctl.'.'.$met.'.'.request_string('type');
$in = [
    'Goods_Goods.goods.goods',
    'Goods_Goods.goodslist.',
];
$minify = false;
if($ctl && $met && in_array($request_uri,$in)){
    $minify = true;
}?>
<?php if($minify){ ?>
    <?php
    $base_uri = Yf_Registry::get('root_uri');
    $min = $base_uri."/shop/static/common/js/plugins/jquery.ui.js,";
    $min .= $base_uri."/shop/static/common/js/plugins/jquery.dialog.js,";
    $min .= $base_uri."/shop/static/common/js/respond.js,";
    $min = substr($min,0,-1);

    ?>
    <script type="text/javascript" src="<?= cdn_url(Yf_Registry::get('base_url').'/min/?f='.$min); ?>"></script>
<?php }else{?>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.ui.js"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.dialog.js"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/respond.js"></script>
<?php } ?>
<?php if (Yf_Registry::get('analytics_statu')) {
    $analytis_js_url = '//luopan.yuanfeng.cn/analytics/static/default/js/h3.js';
    if (strstr(Yf_Registry::get('analytics_api_url'), '/index.php')) {
        $analytis_js_url = str_replace('/index.php', '', Yf_Registry::get('analytics_api_url')) . '/analytics/static/default/js/h3.js';
    }
    ?>
    <!--    <script type="text/javascript">
        (function () {
            var analytics = document.createElement("script");
            analytics.type = "text/javascript";
            analytics.async = true;
            analytics.src = "<?php /*echo $analytis_js_url;*/ ?>";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(analytics, s);
        })();
    </script>-->
    
<?php } ?>
<p class="statistics_code"><?php echo Web_ConfigModel::value('statistics_code'); ?></p>
<!-- <?php if (strtolower($this->ctl) == 'index' && strtolower($this->met) == 'index') { ?>
    <iframe style='width:1px;height:1px;' src="<?php echo Yf_Registry::get('paycenter_api_url') . '?ctl=Index&met=iframe'; ?>"></iframe>
<?php } else { ?>
    <?php if ($_COOKIE['paycenter_iframe'] != 1) { ?>
        <iframe style='width:1px;height:1px;' src="<?php echo Yf_Registry::get('paycenter_api_url') . '?ctl=Index&met=iframe'; ?>"></iframe>
        <?php setcookie('paycenter_iframe', 1, time() + 86400);
        $_COOKIE['paycenter_iframe'] = 1;
    }
} ?> -->

<!--<iframe class="hidden" src="<?php //echo Yf_Registry::get('paycenter_api_url') . '?ctl=Index&met=iframe'; ?>"></iframe>-->
<!--延迟加载iframe @nsy 2019-10-16-->
<iframe class="hidden" id="paycenter_ifrm"></iframe>
<script>
    setTimeout(function(){
        document.getElementById('paycenter_ifrm').src="<?php echo Yf_Registry::get('paycenter_api_url') . '?ctl=Index&met=iframe'; ?>";
    }, 5000);
</script>
<?php include $this->view->getTplPath() . '/' . 'yf_im_config.php'; ?>
</body>
</html>