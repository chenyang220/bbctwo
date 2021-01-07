<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
    <link href="<?= $this->view->css ?>/index.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= $this->view->css_com ?>/jquery/plugins/validator/jquery.validator.css">
    <link rel="stylesheet" href="<?= $this->view->css ?>/page.css">
    <script type="text/javascript" src="<?= $this->view->js_com ?>/layer/layer.min.js"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/jquery.validator.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/validator/local/zh_CN.js" charset="utf-8"></script>
    </head>
    <div class="container">
        <div class="m <?php echo $data['page_color'] ?> frame-<?php echo $data['layout_id'] ?>">
            <div class="mt fn-clear">
                <div class="title"><?= __($data['page_name']); ?></div>
            </div>
            <div class="mc fn-clear">
                
                <?php
                foreach ($data["structure"]["layout_structure"] as $keyss => $valss) {
                    ?>
                    <div id="<?php echo $keyss ?>" style="    <?php foreach ($valss['style'] as $keysss => $valsss) { ?><?php echo $keysss ?>:<?php echo $valsss ?><?php if ($keysss == 'height' || $keysss == 'width') { ?>px;<?php } ?><?php } ?>" data-type="<?php if (!empty($valss['type'])) {
                        echo $valss['type'];
                    } ?>" class="i-mc <?php if (empty($valss['child'])) { ?> block <?php } ?>">
                        <?php
                        if (!empty($valss["child"])) {
                            foreach ($valss["child"] as $keysd => $valsd) {
                                ?>
                                <div id="<?php echo $keysd ?>" style="    <?php foreach ($valsd['style'] as $keysss => $valsss) { ?><?php echo $keysss ?>:<?php echo $valsss ?><?php if ($keysss == 'height' || $keysss == 'width') { ?>px;<?php } ?><?php } ?>" data-type="<?php echo $valsd["type"] ?>" class=" block ">
                                    <?php
                                    if ($valsd['type'] == "ad" || $valsd['type'] == "ag" ) {
                                        if (!empty($valsd['html'])) {
                                            foreach ($valsd['html'] as $html => $img) {
                                                ?>
                                                <img width="<?php echo $valsd['style']['width'] ?>" height="<?php echo $valsd['style']['height'] ?>" alt="<?= __($img['item_name']); ?>" title="<?= __($img['item_name']); ?>" src="<?php echo $img['item_img_url'] ?>"/>
                                            
                                            <?php }
                                        }
                                    }elseif($valsd['type'] == "goods"){
                                        if (!empty($valsd['html'])) {
                                            foreach ($valsd['html'] as $html => $hval) {
                                                ?>
                                                <div class="goods">
                                                    <img width="292" height="292" alt="<?= __($hval['common_image']); ?>" title="<?= __($hval['common_name']); ?>" src="<?php echo $hval['common_image'] ?>"/>
                                                </div>
                                                <p class="pl10 pr10"><?=$hval['common_name']?></p>
                                                <p class="default-color">￥<?=$hval['common_price']?></p>
                                            <?php }
                                            }
                                      }else { ?>
                                        <ul class="fn-clear">
                                            <?php
                                            if (!empty($valsd['html'])) {
                                                foreach ($valsd['html'] as $html => $cat) {
                                                    ?>
                                                    <li><?php echo __($cat['item_name']); ?></li>
                                                <?php }
                                            } ?>
                                        </ul>
                                    
                                    <?php } ?>
                                </div>
                                
                                <?php
                            }
                        } else {
                            if (!empty($valss['html'])) {
                                foreach ($valss['html'] as $html => $img) {
                                    ?>
                                    <img width="<?php echo $valss['style']['width'] ?>" height="<?php echo $valss['style']['height'] ?>" alt="<?= __($img['item_name']); ?>" title="<?= __($img['item_name']); ?>" src="<?php echo $img['item_img_url'] ?>">
                                
                                <?php }
                            }
                        } ?>
                    </div>
                <?php } ?>
            </div>
            <script type="text/javascript">
                $(".m .block").click(function () {
                    $(".block").removeClass("cur");
                    $(this).addClass("cur");
                    var data_type = $(this).attr("data-type");
                    var name = $(this).attr("id");
                    var height = $(this).css("height");
                    var width = $(this).css("width");
                    $.dialog({
                        title: "<?= __('模块编辑'); ?>",
                        content: "url:" + SITE_URL + "?ctl=Floor_Adposition&met=" + data_type + "&op=iframe&page_id=<?php echo $data['page_id'] ?>&layout_id=<?php echo $data['layout_id'] ?>&widget_name=" + name + "&width=" + width + "&height=" + height,
                        data: {callback: testF},
                        width: 700,
                        height: 550,
                        max: !1,
                        min: !1,
                        cache: !1,
                        lock: !0,
                        zIndex: 2000
                        //ok:true,
                        //cancel:true
                    });
                    
                });
                
                function testF() {
                    window.location.reload();
                }
                
                $(".tit_nav").click(function () {
                    $.dialog({
                        title: "<?= __('分类设置'); ?>",
                        content: "url:" + SITE_URL + "?ctl=Floor_Adposition&met=nav&op=iframe&page_id=<?php echo $data['page_id'] ?>",
                        data: {callback: testF},
                        width: 700,
                        height: 550,
                        max: !1,
                        min: !1,
                        cache: !1,
                        zIndex: 2000,
                        lock: !0
                    });
                });
            </script>
        </div>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>