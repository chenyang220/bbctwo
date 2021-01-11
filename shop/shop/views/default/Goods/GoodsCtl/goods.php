<?php
if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
$base_uri = Yf_Registry::get('root_uri');
$css_url = "";

$css_url .= $base_uri . '/shop/static/default/css/goods-detail.css,';
$css_url .= $base_uri . '/shop/static/default/css/Group-integral.css,';
$css_url .= $base_uri . '/shop/static/default/css/tips.css,';
$css_url .= $base_uri . '/shop/static/default/css/login.css';
?>
<script type="text/javascript" src="<?= $this->view->js ?>/swiper2.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.0.2/js/swiper.min.js"></script>-->
<!-- 替换css -->
<link rel="stylesheet" type="text/css" href="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f=' . $css_url); ?>"/>

<?php

$min = $base_uri . "/shop/static/default/js/tuangou-index.js,";
$min .= $base_uri . "/shop/static/common/js/plugins/jquery.slideBox.min.js,";
$min .= $base_uri . "/shop/static/common/js/sppl.js,";
$min .= $base_uri . "/shop/static/default/js/goods_detail.js,";
$min .= $base_uri . "/shop/static/common/js/plugins/jquery.imagezoom.min.js,";
$min .= $base_uri . "/shop/static/common/js/plugins/jquery.toastr.min.js";
//$min .= "/shop/static/default/js/select2.min.js";

?>
<script type="text/javascript" src="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f=' . $min); ?>"></script>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9"></script>
<div class="bgcolor">
    <div class="wrapper">
        <div class="t_goods_detail">
            <div class="crumbs clearfix">
                <?php if(!$_COOKIE['SHOP_ID']){?>
                <p>
                    <?php if ($parent_cat) { ?>
                        <?php foreach ($parent_cat as $catkey => $catval): ?>
                            <a href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goodslist&cat_id=<?= ($catval['cat_id']) ?>"><?= ($catval['cat_name']) ?></a><?php if (!isset($catval['ext'])) { ?><i class="iconfont icon-iconjiantouyou"></i><?php } ?>
                        <?php endforeach; ?>
                    <?php } ?>
                </p>
                <?php }?>
            </div>
            <div class="t_goods_ev clearfix">
                <div class="ev_left">
                    <div class="ev_left_img p-relative">
                        <?php if (!empty($goods_detail['common_base']['common_video'])) { ?>
                            <video id="jqzoomVideo" class="jqzoom lazy" width=366 height=340 src="<?= $goods_detail['common_base']['common_video'] ?>" controls="controls"/>
                        <?php } else { ?>
                            <?php if (isset($goods_detail['goods_base']['image_row'][0]['images_image'])) {
                                $goods_image = $goods_detail['goods_base']['image_row'][0]['images_image'];
                            } else {
                                $goods_image = $goods_detail['goods_base']['goods_image'];
                            } ?>
                            <!--                        --><?php //if($goods_detail['common_video']){ ?>
                            <!--                            <video src="--><? //= $goods_detail['common_video'] ?><!--"></video>-->
                            <!--                        --><?php //}else{ ?>
                            <!--                        --><?php //} ?>

                            <img id="jqzoomImg" class="jqzoom lazy" width=366 rel="<?= cdn_image_url($goods_image, 900, 976) ?>" data-original="<?= cdn_image_url($goods_image, 366, 340) ?>" src="<?= cdn_image_url($goods_image, 366, 340) ?>"/>
                        <?php } ?>
                    </div>
                    <div class="retw">
                        <div class="gdt_ul">
                            <ul class="clearfix" id="jqzoom">
                                <?php if (!empty($goods_detail['common_base']['common_video'])) { ?>
                                    <li class="check">
                                        <video width=60 height=60 src="<?= $goods_detail['common_base']['common_video'] ?>" controls="controls"/>
                                    </li>
                                <?php } ?>
                                <?php if (isset($goods_detail['goods_base']['image_row']) && $goods_detail['goods_base']['image_row']) { 
                                    $limit = 0;
                                    // 产品要求如果有视频，同时5张缩略图只显示4张
                                    foreach ($goods_detail['goods_base']['image_row'] as $imk => $imv) {?>
                                        <?php if ($limit < 5) { ?>
                                            <li <?php if (empty($goods_detail['common_base']['common_video']) && $imv['images_is_default'] == 1){ ?>class="check"<?php } ?>>
                                                <img class='lazy' width=60 data-original="<?= cdn_image_url($imv['images_image'], 60, 60) ?>"/>
                                                <input type="hidden" value="<?= cdn_image_url($imv['images_image'], 366, 340) ?>" rel="<?= cdn_image_url($imv['images_image'], 900, 976) ?>">
                                            </li>
                                        <?php } ?>
                                    <?php   $limit++; } 
                                } else { ?>
                                    <li>
                                        <img class='lazy' width=60 data-original="<?= cdn_image_url($goods_image, 60, 60) ?>"/>
                                        <input type="hidden" value="<?= cdn_image_url($goods_image, 366, 340) ?>" rel="<?= cdn_image_url($goods_image, 900, 976) ?>">
                                    </li>
                                <?php } ?>
                                <?php if (!empty($goods_detail['recImages'])) {
                                    foreach ($goods_detail['recImages'] as $k => $v) {
                                        ?>
                                        <li>
                                            <img class='lazy' width=60 data-original="<?= cdn_image_url($v, 60, 60) ?>"/>
                                            <input type="hidden" value="<?= cdn_image_url($v, 366, 340) ?>" rel="<?= cdn_image_url($v, 900, 976) ?>">
                                        </li>
                                    <?php }
                                } ?>
                            </ul>
                        </div>
                    </div>

                    <!-- 点击放大 -->
                    <div class="goods-img-enlarge-mask">
                        <div class="table">
                            <div class="table-cell tc">
                                <div class="goods-img-enlarge clearfix">
                                    <div class="swiper-container goods-img-enlarge-swiper">
                                        <div class="swiper-wrapper">
                                            <?php if (!empty($goods_detail['common_base']['common_video'])) { ?>
                                                <div class="swiper-slide">
                                                    <video id="common_video2" width=400 src="<?= $goods_detail['common_base']['common_video'] ?>" controls="controls"/>
                                                </div>
                                            <?php } ?>
                                            <?php if (isset($goods_detail['goods_base']['image_row']) && $goods_detail['goods_base']['image_row']) {
                                                foreach ($goods_detail['goods_base']['image_row'] as $imk => $imv) { ?>
                                                    <div class="swiper-slide">
                                                        <img src="<?= $imv['images_image'] ?>"/>
                                                        <input type="hidden" value="<?= cdn_image_url($imv['images_image'], 366, 340) ?>" rel="<?= cdn_image_url($imv['images_image'], 900, 976) ?>">
                                                    </div>
                                                <?php }
                                            } else { ?>
                                                <div class="swiper-slide">
                                                    <img src="<?= $goods_image ?>"/>
                                                    <input type="hidden" value="<?= cdn_image_url($goods_image, 366, 340) ?>" rel="<?= cdn_image_url($goods_image, 900, 976) ?>">
                                                </div>
                                            <?php } ?>
                                            <?php if (!empty($goods_detail['recImages'])) {
                                                foreach ($goods_detail['recImages'] as $k => $v) {
                                                    ?>
                                                    <div class="swiper-slide">
                                                        <img src="<?= $v ?>"/>
                                                        <input type="hidden" value="<?= cdn_image_url($v, 366, 340) ?>" rel="<?= cdn_image_url($v, 900, 976) ?>">
                                                    </div>
                                                <?php }
                                            } ?>

                                        </div>
                                        <div class="swiper-button-next  goods-enlarge-next"><i class="iconfont  icon-btnrightarrow"></i></div>
                                        <div class="swiper-button-prev goods-enlarge-prev"><i class="iconfont icon-btnreturnarrow"></i></div>
                                    </div>

                                    <div class="goods-infors relative">
                                        <em class="btn-close-enlarge"><i class="iconfont icon-cuowu"></i></em>
                                        <span class="iblock tl mb40 two-overflow"><?= ($goods_detail['goods_base']['goods_name']) ?></span>
                                        <ul class="goods-infors-nav clearfix">
                                            <?php if (!empty($goods_detail['common_base']['common_video'])) { ?>
                                                <li class="hide">
                                                    <video width=60 height=60 src="<?= $goods_detail['common_base']['common_video'] ?>" controls="controls"/>
                                                </li>
                                            <?php } ?>
                                            <?php if (isset($goods_detail['goods_base']['image_row']) && $goods_detail['goods_base']['image_row']) {
                                                foreach ($goods_detail['goods_base']['image_row'] as $imk => $imv) { ?>
                                                    <li>
                                                        <img src="<?= $imv['images_image'] ?>"/>
                                                        <input type="hidden" value="<?= cdn_image_url($imv['images_image'], 366, 340) ?>" rel="<?= cdn_image_url($imv['images_image'], 900, 976) ?>">
                                                    </li>
                                                <?php }
                                            } else { ?>
                                                <li>
                                                    <img src="<?= $goods_image ?>"/>
                                                    <input type="hidden" value="<?= cdn_image_url($goods_image, 366, 340) ?>" rel="<?= cdn_image_url($goods_image, 900, 976) ?>">
                                                </li>
                                            <?php } ?>
                                            <?php if (!empty($goods_detail['recImages'])) {
                                                foreach ($goods_detail['recImages'] as $k => $v) {
                                                    ?>
                                                    <li>
                                                        <img src="<?= $v ?>"/>
                                                        <input type="hidden" value="<?= cdn_image_url($v, 366, 340) ?>" rel="<?= cdn_image_url($v, 900, 976) ?>">
                                                    </li>
                                                <?php }
                                            } ?>

                                        </ul>

                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>
                    <script>
                        var enlargeswiper = new Swiper('.goods-img-enlarge-swiper', {
                            slidesPerView: 1,
                            loop: false,
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev'
                            },
                            on: {
                                slideChangeTransitionStart: function () {

                                    var index = this.realIndex;
                                    document.getElementById("common_video2").pause();
                                    //alert(index);
                                    $(".goods-infors-nav li").removeClass('active');
                                    $(".goods-infors-nav li").eq(index).addClass("active");
                                },
                            }

                        });
                        $(function () {

                            var handler = function () {
                                event.preventDefault();
                                event.stopPropagation();
                            };
                            var videoObj = $("#common_video2");
                            $(".ev_left_img").click(function () {
                                $(".goods-img-enlarge-mask").addClass("active");
                                var zoomIndex = $("#jqzoom").find("li.check").index();

                                enlargeswiper.realIndex = zoomIndex;
                                enlargeswiper.slideToLoop(zoomIndex, 0, false);
                                $(".goods-infors-nav li").removeClass("active");
                                $(".goods-infors-nav li").eq(zoomIndex).addClass("active");
                                $(document.body).css("overflow", "hidden");
                                document.body.addEventListener('touchmove', handler, false);
                                document.body.addEventListener('wheel', handler, false);
                            })

                            $(".btn-close-enlarge").click(function () {
                                // document.getElementById("common_video2").pause();
                                $(".goods-img-enlarge-mask").removeClass("active");
                                $(document.body).css("overflow", "auto");
                                document.body.removeEventListener('touchmove', handler, false);
                                document.body.removeEventListener('wheel', handler, false);
                            })
                            $(".goods-infors-nav li").click(function () {
                                var liIndex = $(this).index();
                                $(".goods-infors-nav li").removeClass("active");
                                $(this).addClass("active");
                                // console.log(11);
                                enlargeswiper.realIndex = liIndex;
                                enlargeswiper.slideToLoop(liIndex, 0, false);

                            })

                        })

                    </script>


                    <div class="ev_left_num">
                            <span class="number_imp one-overflow"><?= __('商品编号：') ?>
                                <?php if ($goods_detail['common_base']['common_code']) { ?>
                                    <?= ($goods_detail['common_base']['common_code']) ?>
                                <?php } else { ?>
                                    <?= __("无") ?>
                                <?php } ?>
                            </span>
                        <span class="others_imp share">
                            <b class="top iconfont icon-icoshare icon-1 bbc_color"></b><em class="top"><?= __('分享') ?></em>
                        </span>
                        <span onclick="collectGoods(<?= ($goods_detail['goods_base']['goods_id']) ?>)">
                            <b class="top iconfont icon-2 bbc_color <?php if ($isFavoritesGoods) { ?> icon-taoxinshi<?php } else { ?>  icon-icoheart <?php } ?>"></b>
                            <em class="top"><?= __('收藏') ?></em>
                        </span>
                        <span class="cprodict ">
                            <a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Service_Report&met=index&act=add&gid=<?= ($goods_detail['goods_base']['goods_id']) ?>">
                                <b class="top iconfont icon-jubao icon-1 bbc_color"></b>
                                <em class="top"><?= __('举报') ?></em>
                            </a>
                        </span>
                    </div>
                    <div class="bdsharebuttonbox icon-medium hidden" style="clear:both;padding:10px 20px 0 20px;">
                        <a href="#" class="bds_qzone" data-cmd="qzone"></a>
                        <a href="#" class="popup_sqq" data-cmd="sqq"></a>
                        <a href="#" class="bds_weixin" data-cmd="weixin"></a>
                        <a href="#" class="popup_copy" data-cmd="copy"></a>
                    </div>
                </div>
                <div class="ev_center">
                    <div class="ev_head">
                        <h3><?= ($goods_detail['goods_base']['goods_name']) ?></h3>
                    </div>
                    <div class="small_title">
                        <?php if ($goods_detail['common_base']['common_is_virtual']): ?>
                            <p class="bbc_color"><?= __('虚拟商品') ?></p>
                        <?php endif; ?>
                        <p class="bbc_color"><?= ($goods_detail['goods_base']['goods_promotion_tips']) ?></p>
                    </div>
                    <!-- 新增秒杀时间提示 -->
                    <dl class="seckill-time clearfix" style="display:none;">
                        <dt class="fl">秒杀时间</dt>
                        <dd class="fr"><span class="mr10">距离结束</span><em>11</em>：<em>20</em>: <em>20</em></dd>
                    </dl>
                    <div class="obvious">
                        <p class="clearfix">
                            <span class="mar-r _letter-spacing"><?= __('市场价：') ?></span>
                            <span class="mar-b-1"><del><?= format_money($goods_detail['goods_base']['goods_market_price']) ?></del></span>
                        </p>
                        <p class="clearfix">
                            <span class="mar-r _letter-spacing"><?= __('商城价：') ?></span>
                            <span class="mar-b-2">
                                    <?php if (isset($goods_detail['goods_base']['promotion_price']) && !empty($goods_detail['goods_base']['promotion_price'])
                                    ) : ?>
                                        <strong class="color-db0a07 bbc_color block"><?= format_money($goods_detail['goods_base']['promotion_price']) ?></strong><span><?= __('（原售价：') ?><?= format_money($goods_detail['goods_base']['goods_price']) ?><?= __('）') ?></span>
                                        <input type="hidden" name="goods_price" value="<?= $goods_detail['goods_base']['promotion_price'] ?>" id="goods_price"/>
                                    <?php else: ?>
                                        <input type="hidden" name="goods_price" value="<?= $goods_detail['goods_base']['goods_price'] ?>" id="goods_price"/>
                                        <strong class="color-db0a07 bbc_color block"><?= format_money($goods_detail['goods_base']['goods_price']) ?></strong>
                                    <?php endif; ?>

                                <!-- 3.6.7-plus -->
                                <?php if ($goods_detail['goods_base']['plus_status']) { ?>
                                    <i class="plus-pri align-middle"><?= format_money($goods_detail['goods_base']['plus_price']) ?></i>
                                    <b class="plus-logo align-middle"></b>
                                    <i class="plus-text"><?= __('PLUS会员专享价') ?></i>
                                    <?php if (!$plus_user) { ?>
                                        <a class="plus-open" href="<?= Yf_Registry::get('url') ?>?ctl=Plus_User&met=index"><?= __('现在开通，即享特惠>>>') ?></a>
                                    <?php } ?>
                                <?php } ?>
                                </span>
                        </p>
                        <p class="clearfix">
                            <span class="mar-r _letter-spacing-2"><?= __('商品评分：') ?></span>
                            <span class="mar-b-3">
                                <?php for ($i = 1; $i <= $goods_detail['goods_base']['goods_evaluation_good_star']; $i++) { ?>
                                    <em></em>
                                <?php } ?>
                                </span>
                        </p>
                        <p class="clearfix"><span class="mar-r _letter-spacing-2"><?= __('商品评价：') ?></span>
                            <span class="color-1876d1 mar-b-3 "><a href="#elist" name="elist" class="pl"><i class="num_style"><?= ($goods_detail['common_base']['common_evaluate']) ?></i> <?= __('条评论') ?></a></span>
                        </p>
                        <p class="clearfix"><span class="mar-r"><?= __('销&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量：') ?></span>
                            <span class="color-1876d1 mar-b-5 "><a href="#elist" name="elist" class="pl"><i class="num_style"><?= ($goods_detail['common_base']['common_salenum']) ?></i> <?= __('件') ?></a></span>
                        </p>
                        <div>
                            <img class='lazy' data-original="<?= Yf_Registry::get('base_url') ?>/shop/api/qrcode.php?data=<?= urlencode(Yf_Registry::get('shop_wap_url') . "/tmpl/product_detail.html?uu_id=" . $user_id . "&goods_id=" . $goods_detail['goods_base']['goods_id']) ?>" width="100" height="100"/>
                            <span class="mt6"><?= __('扫描二维码') ?></span><span><?= __('手机上购物') ?></span>
                        </div>
                    </div>
                    <?php if (isset($goods_detail['goods_base']['promotion_type']) && $goods_detail['goods_base']['promotion_type']) {
                        $now_time = time();
                        $start_time = strtotime($goods_detail['goods_base']['groupbuy_starttime']);
                        $end_time = strtotime($goods_detail['goods_base']['groupbuy_endtime']);
                        if ($start_time > $now_time) {
                            $time_tips = __('距开始');
                            $diff_time = $start_time - $now_time;
                        }
                        if ($end_time > $now_time && $start_time < $now_time) {
                            $time_tips = __('距结束');
                            $diff_time = $end_time - $now_time;
                        }

                        ?>
                        <div class="count-down">
                            <i class="iconfont icon-julishijian"></i>
                            <dl>
                                <dt><?= $time_tips ?>：</dt>
                                <dd>
                                    <span id="day_show"></span><?= __('天') ?>
                                    <span id="hour_show"></span><?= __('时') ?>
                                    <span id="minute_show"></span><?= __('分') ?>
                                    <span id="second_show"></span><?= __('秒') ?>
                                </dd>
                            </dl>
                            <?php
                            if ($goods_detail['goods_base']['promotion_type'] === 'groupbuy' && $goods_detail['goods_base']['groupbuy_virtual_quantity'] > 0) { ?>
                                <div class="fr"><?= $goods_detail['goods_base']['groupbuy_virtual_quantity'] ?><?= __('件已团购') ?></div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <div class="goods_style_sel ">
                        <div>
                            <input type="hidden" id="common_id" value="<?= ($goods_detail['goods_base']['common_id']) ?>"/>
                            <?php if ($goods_detail['goods_base']['plus_status']) { ?>
                                <span class="span_w lineh-1 mar_l "><?= __('促&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;销：') ?></span>
                                <b class="plus-limit mt0">PLUS限制</b>
                                <i class="fz12">PLUS价格不与套装优惠同时享受</i>
                            <?php } ?>
                            <?php if (isset($goods_detail['goods_base']['promotion_type']) || $goods_detail['goods_base']['have_gift'] == 'gift' || !empty($goods_detail['goods_base']['increase_info']) || !empty($goods_detail['mansong_info'])) { ?>
                                <?php if (isset($goods_detail['goods_base']['promotion_type']) || !empty($goods_detail['mansong_info']) || !empty($goods_detail['goods_base']['increase_info'])) { ?>
                                    <!-- 如果plus商品已经存在“促销”两个字，就不再显示 -->
                                    <?php if (!$goods_detail['goods_base']['plus_status']) { ?>
                                        <span class="span_w lineh-1 mar_l "><?= __('促&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;销：') ?></span>
                                    <?php } ?>

                                    <div class="activity_reset">
                                        <?php if (isset($goods_detail['goods_base']['title']) && $goods_detail['goods_base']['title'] != '') { ?>
                                            <span><i class="iconfont icon-huanyipi"></i><?= ($goods_detail['goods_base']['title']) ?></span>
                                            <!--S 限时折扣 -->
                                            <?php if ($goods_detail['goods_base']['promotion_type'] == 'xianshi') { ?>
                                                <i class="group_purchase "><?= __('限时折扣：') ?></i>
                                                <strong><?= __('直降') ?></strong><?= ($goods_detail['goods_base']['down_price']) ?>
                                                <?php if ($goods_detail['goods_base']['lower_limit']) { ?>
                                                    <?php echo sprintf('最低购%s件，', $goods_detail['goods_base']['lower_limit']); ?><?php echo $goods_detail['goods_base']['explain']; ?>
                                                <?php }
                                            } ?>
                                            <!--E 限时折扣 -->
                                            <!--S 团购 -->
                                            <?php if ($goods_detail['goods_base']['promotion_type'] == 'groupbuy') { ?>
                                                <?php if ($goods_detail['goods_base']['upper_limit']) { ?>
                                                    <i class="group_purchase "><?= __('团购：') ?></i>
                                                    <em><?php echo sprintf('最多限购%s件', $goods_detail['goods_base']['upper_limit']); ?></em>
                                                <?php } ?>
                                                <span><?php echo $goods_detail['goods_base']['remark']; ?></span>
                                            <?php } ?>
                                            <!--E 团购 -->
                                        <?php } ?>

                                        <!--S 加价购 -->
                                        <?php if ($goods_detail['goods_base']['increase_info']) { ?>
                                            <div class="ncs-mansong">
                                                <i class="group_purchase "><?= __('加价购：') ?></i>
                                                <span class="sale-rule">
                                                  <em><?= ($goods_detail['goods_base']['increase_info']['increase_name']) ?></em>

                                                    <?php if (!empty($goods_detail['goods_base']['increase_info']['rule'])) { ?>
                                                        <?= __('购物满') ?><em><?= format_money($goods_detail['goods_base']['increase_info']['rule'][0]['rule_price']) ?></em><?= __('即可加价换购最多') ?><?php if ($goods_detail['goods_base']['increase_info']['rule'][0]['rule_goods_limit']): ?><?= ($goods_detail['goods_base']['increase_info']['rule'][0]['rule_goods_limit']) ?><?= __('样') ?><?php endif; ?><?= __('商品') ?>
                                                    <?php } ?>

                                                    <span class="sale-rule-more" nctype="show-rule">
                                                    <a href="javascript:void(0);">
                                                        <?= __('详情') ?><i class="iconfont icon-iconjiantouxia"></i>
                                                    </a>
                                                  </span>

                                                    <?php if (!empty($goods_detail['goods_base']['increase_info']['goods'])) { ?>
                                                        <div class="sale-rule-content" style="display: none;" nctype="rule-content">
                                                            <div class="title"><span class="sale-name">
                                                            <?= ($goods_detail['goods_base']['increase_info']['increase_name']) ?></span><?= __('，共') ?>
                                                                <strong><?php echo count($goods_detail['goods_base']['increase_info']['rule']); ?></strong>
                                                                <?= __('种活动规则') ?><a href="javascript:;" nctype="hide-rule"><?= __('关闭') ?></a>
                                                            </div>

                                                            <?php foreach ($goods_detail['goods_base']['increase_info']['rule'] as $rule) { ?>
                                                                <div class="content clearfix">
                                                                    <div class="mjs-tit">
                                                                        <?= __('购物满') ?><em><?= format_money($rule['rule_price']) ?></em><?= __('即可加价换购更多') ?><?php if ($rule['rule_goods_limit']): ?><?= ($rule['rule_goods_limit']) ?><?= __('样') ?><?php endif; ?><?= __('商品') ?>
                                                                    </div>
                                                                    <ul class="mjs-info clearfix">
                                                                        <?php foreach ($rule['redemption_goods'] as $goods) { ?>
                                                                            <li>
                                                                                <a href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= ($goods['goods_id']) ?>" title="<?= ($goods['goods_name']) ?>" target="_blank" class="gift"> <img width="40" src="<?= cdn_image_url($goods['goods_image'], 80, 80) ?>" alt="<?= ($goods['goods_name']) ?>"> </a>&nbsp;
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <!--E 加价购 -->

                                        <!--S 满即送 -->
                                        <?php if ($goods_detail['mansong_info'] && $goods_detail['mansong_info']['rule']) { ?>
                                            <div class="ncs-mansong">
                                                <i class="group_purchase "><?= __('满即送：') ?></i>
                                                <span class="sale-rule">
                                              <?php $rule = $goods_detail['mansong_info']['rule'][0]; ?>
                                                    <?= __('购物满') ?><em><?= format_money($rule['rule_price']) ?></em>
                                                    <?php if (!empty($rule['rule_discount'])) { ?>
                                                        <?= __('，即享') ?><em><?= ($rule['rule_discount']) ?></em><?= __('元优惠') ?>
                                                    <?php } ?>
                                                    <?php if (!empty($rule['goods_id'])) { ?>
                                                        <?= __('，送') ?><a href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= ($rule['goods_id']) ?>" title="<?= ($rule['goods_name']) ?>" target="_blank"><?= __('赠品') ?></a>
                                                    <?php } ?>
                                              </span> <span class="sale-rule-more" nctype="show-rule"><a href="javascript:void(0);"><?= __('共') ?><strong><?php echo count($goods_detail['mansong_info']['rule']); ?></strong><?= __('项，展开') ?><i class="iconfont icon-iconjiantouxia"></i></a></span>
                                                <div class="sale-rule-content" style="display: none;" nctype="rule-content">
                                                    <div class="title"><span class="sale-name"><?= __('满即送') ?></span><?= __('共') ?><strong><?php echo count($goods_detail['mansong_info']['rule']); ?></strong><?= __('项，促销活动规则') ?><a href="javascript:;" nctype="hide-rule"><?= __('关闭') ?></a></div>
                                                    <div class="content clearfix">
                                                        <div class="mjs-tit"><?= ($goods_detail['mansong_info']['mansong_name']) ?>
                                                            <time>(<?= ($goods_detail['mansong_info']['mansong_start_time']) ?> -- <?= ($goods_detail['mansong_info']['mansong_end_time']) ?> )</time>
                                                        </div>
                                                        <ul class="mjs-info clearfix">
                                                            <?php foreach ($goods_detail['mansong_info']['rule'] as $rule) { ?>
                                                                <li> <span class="sale-rule"><?= __('购物满') ?><em><?= format_money($rule['rule_price']) ?></em>
                                                                        <?php if (!empty($rule['rule_discount'])) { ?>
                                                                            <?= __('， 即享') ?><em><?= (($rule['rule_discount'])) ?></em><?= __('元优惠') ?>
                                                                        <?php } ?>
                                                                        <?php if (!empty($rule['goods_id'])) { ?>
                                                                            <?= __('， 送 ') ?><a href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= ($rule['goods_id']) ?>" title="<?= ($rule['goods_name']) ?>" target="_blank" class="gift"> <img src="<?= cdn_image_url($rule['goods_image'], 60, 60) ?>" alt="<?= ($rule['goods_name']) ?>"> </a>&nbsp;<?= __('，数量有限，赠完为止。 ') ?>
                                                                        <?php } ?>
                                                      </span></li>
                                                            <?php } ?>
                                                        </ul>
                                                        <div class="mjs-remark wp100 overflow"><?= ($goods_detail['mansong_info']['mansong_remark']) ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <!--E 满即送 -->
                                    </div>
                                <?php } ?>

                            <?php } ?>
                        </div>

                        <p class="mar-top">
                            <span class="span_w lineh-2 mar_l "><?= __('配送至：') ?></span>
                        </p>
                        <div class="span_w_p clearfix">
                            <div id="ncs-freight-selector" class="ncs-freight-select">
                                <div class="text pr18">
                                    <div id="select-city-cookie">
                                        <?php if ($goods_detail['transport']) {
                                            echo $goods_detail['transport']['area'];
                                        } else {
                                            echo __('请选择地区');
                                        } ?>
                                    </div>
                                    <b>∨</b></div>
                                <div class="content">
                                    <div id="ncs-stock" class="ncs-stock" data-widget="tabs">
                                        <div class="mt">
                                            <ul class="tab">
                                                <li data-index="0" data-widget="tab-item" class="curr"><a href="#none" class="hover"><em><?= __('请选择') ?></em><i> ∨</i></a></li>
                                            </ul>
                                        </div>
                                        <div id="stock_province_item" data-widget="tab-content" data-area="0">
                                            <ul class="area-list"></ul>
                                        </div>
                                        <div id="stock_city_item" data-widget="tab-content" data-area="1" style="display: none;">
                                            <ul class="area-list"></ul>
                                        </div>
                                        <div id="stock_area_item" data-widget="tab-content" data-area="2" style="display: none;">
                                            <ul class="area-list"></ul>
                                        </div>
                                    </div>
                                </div>
                                <a href="javascript:;" class="close" onclick="$('#ncs-freight-selector').removeClass('hover')"><?= __('关闭') ?></a>
                            </div>

                            <span class="goods_have linehe">
                                <?php if ($goods_detail['goods_base']['goods_stock'] <= 0) {
                                    echo __('无货');
                                } ?>
                            </span>
                            <?php if($goods_detail['common_base']['common_is_delivery'] == 1 && Web_ConfigModel::value('Plugin_Delivery') == 1){ ?>
                                <em class="transport">
                                    <?= __('配送费：') ?>
                                    <?= __('￥') ?><?= Web_ConfigModel::value('delivery') ?>
                                </em>
                            <?php }else{?>
                                <em class="transport" id="transport_all_money"></em>
                            <?php } ?>
                        </div>
                        <?php if (isset($goods_detail['common_base']['common_spec_name']) && $goods_detail['common_base']['common_spec_name'] && isset($goods_detail['common_base']['common_spec_value']) && $goods_detail['common_base']['common_spec_value']) {
                            foreach ($goods_detail['common_base']['common_spec_name'] as $speck => $specv) {
                                ?>
                                <p class="goods_pl"><span class="span_w lineh-3 mar_l "><?= ($specv) ?>：</span>
                                    <?php if (isset($goods_detail['common_base']['common_spec_value']) && $goods_detail['common_base']['common_spec_value']) {
                                        foreach ($goods_detail['common_base']['common_spec_value'][$speck] as $specvk => $specvv) {
                                            if (!empty($specvv)) {
                                                ?>
                                                <a <?php if (isset($goods_detail['goods_base']['goods_spec'][$specvk])) { ?> class="check" <?php } ?> value="<?= ($specvk) ?>">
                                                    <?= ($specvv) ?>
                                                </a>
                                            <?php }
                                        }
                                    } ?>
                                </p>
                            <?php }
                        } ?>
                        <!--                           <p class="purchase_type "><span class="span_w ">购买方式:</span> <a href="# ">全新未拆封</a></p>-->
                        <?php if ($goods_detail['chain_stock'] > 0) { ?>
                            <p class="clearfix">
                                <span class="mar-r _letter-spacing-2">门店服务：</span>
                                <span class="color-1876d1 mar-b-4 ">
                                        <a href="javascript:;" name="elist" class="num_style mendian" nctype="get_chain">
                                            <!-- 是否门店配送-->
                                            <?php if($goods_detail['common_base']['common_is_delivery'] == 1 && Web_ConfigModel::value('Plugin_Delivery') == 1){ ?>
                                                <i class="iconfont icon-tabhome"></i><?= __('门店配送') ?>
                                            <?php }else{ ?>
                                            <i class="iconfont icon-tabhome"></i><?= __('门店自提') ?>
                                            <?php } ?>
                                        </a>
                                    <? __('· 选择有现货的门店下单，可立即提货') ?>
                                    </span>
                            </p>
                        <?php } ?>
                        <?php if ($goods_status) { ?>

                            <p class="need_num detail_num clearfix">
                                <span class="span_w lineh-6 mar_l "><?= __('数量：') ?></span>
                                <span class="goods_num">
                                            <?php if ($goods_detail['goods_base']['cat_id'] != 9002) { ?>
                                                <a class="no_reduce"><?= __('-') ?></a>
                                            <?php } ?>
                                            <input id="nums" name="nums" AUTOCOMPLETE="off" data-id="<?= ($goods_detail['goods_base']['goods_id']) ?>" data-min="<?php if ($goods_detail['goods_base']['lower_limits']): ?><?= ($goods_detail['goods_base']['lower_limits']) ?><?php else: ?><?= (1) ?><?php endif; ?>" data-max="<?php if ($goods_detail['buyer_limit']): ?><?= ($goods_detail['buyer_limit']) ?><?php else: ?><?= ($goods_detail['goods_base']['goods_stock']) ?><?php endif; ?>" value="<?php if ($goods_detail['goods_base']['lower_limits']) {
                                                echo $goods_detail['goods_base']['lower_limits'];
                                            } else {
                                                echo 1;
                                            } ?>" <?php if ($goods_detail['goods_base']['cat_id'] == 9002) { ?> disabled<?php } ?> >
                                            <input type="hidden" value="<?= ($goods_detail['common_base']['common_cubage']) ?>" id="weight"/>
                                            <?php if ($goods_detail['goods_base']['cat_id'] != 9002) { ?>
                                                <a class="<?php if ($goods_detail['buy_limit'] == 1 || $goods_detail['goods_base']['goods_stock'] == 1): ?>no_<?php endif; ?>add"><?= __('+') ?></a>
                                            <?php } ?>
                                        </span>

                                <span class="stock_num">&nbsp;&nbsp;(<?= __('库存') ?><?= ($goods_detail['goods_base']['goods_stock']) ?><?= __('件') ?>)</span>

                                <?php if ($goods_detail['buy_limit']) { ?>
                                    <span class="limit_purchase "><?= __('每人限购') ?><?= ($goods_detail['buy_limit']) ?><?= __('件') ?></span>
                                <?php } ?>
                            </p>
                            
                            <!--分类商品退货期-->
                            <p class="need_num detail_num clearfix">
                                <span class="span_w lineh-6 mar_l "><?= __('温馨提示:') ?></span>
                                <span><?=$rgl_str;?></span>
                            </p>
                            <?php if ($goods_detail['goods_base']['goods_stock']): ?>
                                <?php if ($goods_detail['common_base']['common_is_virtual']) { ?>
                                    <p class="buy_box">
                                        <a class="tuan_go buy_now_virtual bbc_color bbc_border"><?= __('立即购买') ?></a>
                                    </p>
                                <?php } elseif ($goods_detail['common_base']['product_is_behalf_delivery'] == 1 && $goods_detail['common_base']['common_parent_id']) { ?>
                                    <p class="buy_box">
                                        <a class="tuan_go buy_now_supplier bbc_color bbc_border"><?= __('立即购买') ?></a>
                                    </p>
                                <?php } else { ?>
                                    <p class="buy_box">
                                        <a class="tuan_join_cart bbc_btns"><?= __('加入购物车') ?></a>
                                        <a class="tuan_go buy_now  bbc_color bbc_border"><?= __('立即购买') ?></a>
                                    </p>
                                <?php } ?>

                            <?php endif; ?>
                            <p class="buy_box_gray" <?php if ($goods_detail['goods_base']['goods_stock']) { ?> style="display: none;"<?php } ?> >
                                <?php if ($goods_detail['common_base']['common_is_virtual'] != 1 && $goods_detail['common_base']['product_is_behalf_delivery'] != 1) { ?>
                                    <a class="tuan_join_cart_gray bbc_btns"><?= __('加入购物车') ?></a>
                                <?php } ?>
                                <a class="tuan_go_gray  bbc_border"><?= __('立即购买') ?></a>
                            </p>
                        <?php } else { ?>
                            <div class="good_status"><?= __('该商品已下架') ?></div>
                        <?php } ?>

                        <p class="need_num detail_num clearfix services-items">
                            <span class="span_w lineh-6 mar_l "><?= __('服务保障:') ?></span>
                                <?php foreach($consult_data as $key =>$val){?>
                                     <em title="<?= $val['contract_type_desc'] ?>"><?php echo $val['contract_type_name'] ?></em>
                                <?php }?>

                        </p>
                    </div>
                </div>
                <div class="ev_right ">
                    <div class="ev_right_pad pt20">
                        <div class="divimg ">
                            <?php if (!empty($shop_detail['shop_logo'])) {
                                $shop_logo = $shop_detail['shop_logo']; ?>
                            <?php } else {
                                $shop_logo = $this->web['shop_logo'];
                            }
                            ?>
                            <!-- 老版本/定制项目 class -->
                            <img class='lazy' width=200 height=60 src="<?= $shop_logo ?>">
                        </div>
                        <div class="txttitle clearfix">
                            <p>
                                <a class="store-names" href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=index&typ=e&id=<?= ($shop_detail['shop_id']) ?>"><?= ($shop_detail['shop_name']) ?></a>

                                <!-- YF_IM 联系客服入口 yf_chat START -->
                                <!-- 注意 老版本/定制项目 需要自行添加 rel和class -->
                                <?php if (Web_ConfigModel::value('im_statu') && Yf_Registry::get('im_statu')) { ?>
                                    <a href="javascript:;" class="chat-enter yf_chat" rel="<?= $shop_detail['shop_self_support'] == 'true' && Web_ConfigModel::value('self_shop_im') ? Web_ConfigModel::value('self_shop_im') : $shop_detail['user_name']; ?>"><i class="iconfont icon-btncomment"></i></a>
                                <?php } ?>
                                <!-- YF_IM 联系客服入口 END-->

                            </p>
                            <?php if ($shop_detail['shop_self_support'] == 'true') { ?>
                                <div class="bbc_btns"><?= __('平台自营') ?></div>
                            <?php } ?>
                        </div>

                        <!-- 品牌-->
                        <?php if ($shop_detail['shop_self_support'] == 'false') { ?>
                            <div class="brandself ">
                                <ul class="shop_score clearfix ">
                                    <li><?= __('店铺动态评分') ?></li>
                                    <li><?= __('同行业相比') ?></li>
                                </ul>
                                <ul class="shop_score_content clearfix ">
                                    <li>
                                        <span><?= __('描述相符：') ?><?= number_format($shop_detail['shop_desc_scores'], 2, '.', '') ?></span>
                                        <span class="high_than bbc_bg">
                                        <?php if ($shop_detail['com_desc_scores'] >= 0): ?><i class="iconfont  icon-gaoyu rel_top1"></i>
                                            <?= __('高于') ?><?php else: ?><i class="iconfont  icon-diyu rel_top1"></i><?= __('低于') ?><?php endif; ?>
                                    </span>
                                        <em class="bbc_color"><?= number_format(abs($shop_detail['com_desc_scores']), 2, '.', '') ?><?= __('%') ?></em>
                                    </li>
                                    <li>
                                        <span><?= __('服务态度：') ?><?= number_format($shop_detail['shop_service_scores'], 2, '.', '') ?></span>
                                        <span class="high_than bbc_bg">
                                        <?php if ($shop_detail['com_service_scores'] >= 0): ?><i class="iconfont  icon-gaoyu rel_top1"></i><?= __('高于') ?><?php else: ?><i class="iconfont  icon-diyu rel_top1"></i><?= __('低于') ?><?php endif; ?>
                                    </span>
                                        <em class="bbc_color"><?= number_format(abs($shop_detail['com_service_scores']), 2, '.', '') ?><?= __('%') ?></em>
                                    </li>
                                    <li>
                                        <span><?= __('发货速度：') ?><?= number_format($shop_detail['shop_send_scores'], 2, '.', '') ?></span>
                                        <span class="high_than bbc_bg">
                                        <?php if ($shop_detail['com_send_scores'] >= 0): ?><i class="iconfont  icon-gaoyu rel_top1"></i><?= __('高于') ?><?php else: ?><i class="iconfont  icon-diyu rel_top1"></i><?= __('低于') ?><?php endif; ?>
                                    </span>
                                        <em class="bbc_color"><?= number_format(abs($shop_detail['com_send_scores']), 2, '.', '') ?><?= __('%') ?></em>
                                    </li>
                                </ul>
                            </div>

                            <div class="shop_address">
                                <?= __('所 在 地 ：') ?><?= ($shop_detail['shop_company_address']) ?>
                            </div>

                            <div class="follow_shop ">
                                <a href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=index&typ=e&id=<?= ($shop_detail['shop_id']) ?>" target="_blank" class="shop_enter"><?= __('进入店铺') ?></a>
                                <a onclick="collectShop(<?= ($shop_detail['shop_id']) ?>)" class="shop_save"><?= __('收藏店铺') ?></a>
                            </div>

                        <?php } ?>

					
                    <?php if (isset($shop_detail['contract']) && $shop_detail['contract']): ?>
                        <!--2019-04-28  分类商品退货期prd描述，注释该项-->
                        <!--<span class="fwzc "><?= __('服务支持：') ?></span>
                        <ul class="ev_right_ul clearfix ">
                            <?php foreach ($shop_detail['contract'] as $sckey => $scval): ?>
                                <a href="<?= ($scval['contract_type_url']) ?>">
                                    <li><i><img class='lazy' width=22 height=22 data-original="<?= cdn_image_url($scval['contract_type_logo'], 22, 22) ?>"/></i>&nbsp;&nbsp;&nbsp;<?= ($scval['contract_type_name']) ?></li>
                                </a>
                            <?php
                            endforeach;
                            ?>
                        </ul>-->
                    <?php
                    endif;
                    ?>
                </div>
                <div>
               
                <!-- 自营 -->
                <?php if ($shop_detail['shop_self_support'] == 'true'&&!$_COOKIE['SHOP_ID']) { ?>
                    <div class="look_again "><?= __('看了又看') ?></div>
                    <ul class="look_again_goods clearfix ">
                        <?php if (!empty($data_recommon_goods)) {
                        foreach ($data_recommon_goods

                        as $key_recommon => $value_recommon) {
                        ?>
                        <li>
                            <a target="_blank"
                               href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= ($value_recommon['goods_id']) ?>">
                                <img class='lazy' data-original="<?= $value_recommon['common_image'] ?>"/>
                                <h5 class="bbc_color"><?= format_money($value_recommon['common_price']) ?></h5>
                            </a>
                            <?php
                            }
                            ?>
                    </ul>
                <?php
					}
				}
                        ?>
                    </div>
   
                </div>

            </div>

        </div>
    </div>

</div>

<div class="wrap">
    <div class="t_goods_bot clearfix ">
        <div class="t_goods_bot_left ">

            <?php if ($shop_detail['shop_self_support'] == 'false') { ?>

                <div class="goods_classify">
                    <h4><?= ($shop_detail['shop_name']) ?>
                        <?php if ($shop_detail['shop_qq']) { ?>
                        <a rel="1" target="_blank" href="https://wpa.qq.com/msgrd?v=3&uin=<?= $shop_detail['shop_qq'] ?>&site=qq&menu=yes" title="QQ: <?= $shop_detail['shop_qq'] ?>"><img border="0" src="https://pub.idqqimg.com/qconn/wpa/button/button_121.gif" style=" vertical-align: middle;"></a><?php } ?><?php if ($shop_detail['shop_ww']) { ?>
                        <a rel="2" target="_blank" href='https://www.taobao.com/webww/ww.php?ver=3&touid=<?= $shop_detail['shop_ww'] ?>&siteid=cntaobao&status=2&charset=utf-8'><img border="0" src='https://amos.alicdn.com/realonline.aw?v=2&uid=<?= $shop_detail['shop_ww'] ?>&site=cntaobao&s=2&charset=utf-8' alt="<?= __('点击这里给我发消息') ?>" style=" vertical-align: middle;"></a><?php } ?></h4>

                    <div class="service-list1" store_id="8" store_name="<?= ($shop_detail['shop_name']) ?>">
                        <?php if (!empty($service['pre'])) { ?>
                            <dl>
                                <dt><?= __('售前客服：') ?></dt>

                                <?php foreach ($service['pre'] as $key => $val) { ?>
                                    <?php if (!empty($val['number'])) { ?>
                                        <dd><span><?= $val['name'] ?></span><span>
                                    <span c_name="<?= $val['name'] ?>" member_id="9"><?= $val['tool'] ?></span>
                                    </span></dd>
                                    <?php } ?>
                                <?php } ?>
                            </dl>
                        <?php } ?>
                        <?php if (!empty($service['after'])) { ?>
                            <dl>
                                <dt><?= __('售后客服：') ?></dt>
                                <?php foreach ($service['after'] as $key => $val) { ?>
                                    <?php if (!empty($val['number'])) { ?>
                                        <dd><span><?= $val['name'] ?></span><span>
                                    <span c_name="<?= $val['name'] ?>" member_id="9"><?= $val['tool'] ?></span>
                                    </span></dd>
                                    <?php } ?>
                                <?php } ?>

                            </dl>
                        <?php } ?>
                        <?php if ($shop_detail['shop_workingtime']) { ?>
                            <dl class="workingtime">
                                <dt><?= __('工作时间：') ?></dt>
                                <dd>
                                    <p><?= ($shop_detail['shop_workingtime']) ?></p>
                                </dd>
                            </dl>
                        <?php } ?>
                    </div>
                </div>

            <?php } ?>

            <div class="goods_classify ">
                <h4><?= __('商品分类') ?></h4>
                <p class="classify_like">
                    <a href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=goodsList&id=<?= $shop_detail['shop_id']; ?>&order=common_sell_time "><?= __('按新品') ?></a>
                    <a href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=goodsList&id=<?= $shop_detail['shop_id']; ?>&order=common_price "><?= __('按价格') ?></a>
                    <a href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=goodsList&id=<?= $shop_detail['shop_id']; ?>&order=common_salenum "><?= __('按销量') ?></a>
                    <a href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=goodsList&id=<?= $shop_detail['shop_id']; ?>&order=common_collect"><?= __('按人气') ?></a></p>

                <p class="classify_ser"><input type="text" name="searchGoodsList" placeholder="<?= __('搜索店内商品') ?>"><a id="searchGoodsList"><?= __('搜索') ?></a></p>
                <ul class="ser_lists ">

                </ul>
            </div>
            <div class="goods_ranking ">
                <h4><?= __('商品排行') ?></h4>
                <p class="selling"><a><?= __('热销商品排行') ?></a><a><?= __('热门收藏排行') ?></a></p>
                <ul id="hot_salle">
                    <?php if (!empty($data_salle)) {
                        foreach ($data_salle as $key_salle => $value_salle) {
                            ?>
                            <li class="clearfix">
                                <a target="_blank" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= ($value_salle['goods_id']) ?>"
                                   class="selling_goods_img"><img class='lazy' data-original="<?= cdn_image_url($value_salle['common_image']) ?>"></a>

                                <p>
                                    <a target="_blank" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= ($value_salle['goods_id']) ?>"><?= $value_salle['common_name'] ?></a>
                                    <span class="bbc_color"><?= format_money($value_salle['common_price']) ?></span>
                                    <span>
                                                <i></i><?= __('出售：') ?>
                                        <i class="num_style"><?= $value_salle['common_salenum'] ?></i> <?= __('件') ?>
                                           </span>
                                </p>
                            </li>
                            <?php
                        }
                    } ?>
                </ul>
                <ul style="display: none;" id="hot_collect">
                    <?php if (!empty($data_collect)) {
                        foreach ($data_collect as $key_collect => $value_collect) {
                            ?>
                            <li class="clearfix">
                                <a target="_blank" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= $value_collect['goods_id'] ?>" class="selling_goods_img">
                                    <img src="<?= cdn_image_url($value_collect['common_image']) ?>">
                                    <!-- <img class='lazy' data-original="<?= cdn_image_url($value_collect['common_image']) ?>"> -->
                                </a>

                                <p>
                                    <a target="_blank" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= $value_collect['goods_id'] ?>"><?= $value_collect['common_name'] ?></a>
                                    <span class="bbc_color"><?= format_money($value_collect['common_price']) ?></span>
                                    <span>
                                            <i></i><?= __('收藏人气：') ?>
                                        <i class="num_style"><?= $value_collect['common_collect'] ?></i>
                                        </span>
                                </p>
                            </li>
                            <?php
                        }
                    } ?>
                </ul>
                <a target="_blank" href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=goodsList&id=<?= ($shop_detail['shop_id']) ?>"><p class="look_other_goods bbc_btns"><?= __('查看本店其他商品') ?></p></a>
            </div>
        </div>
        <div name="elist" id="elist"></div>
        <div class="t_goods_bot_right ">
            <ul class="goods_det_about goods_det clearfix border_top">
                <li><a class="xq checked"><?= __('商品详情') ?></a></li>
                <li class="al"><a class="pl"><?= __('商品评论') ?><span><?= __('(') ?><?= ($goods_detail['goods_base']['evalcount']) ?><?= __(')') ?></span></a></li>
                <!--<li><a class="xs"><? /*=__('销售记录')*/ ?><span><? /*=__('(')*/ ?><? /*= ($goods_detail['goods_base']['salecount']) */ ?><? /*=__(')')*/ ?></span></a></li>-->
                <?php if ($entity_shop || ($goods_detail['common_base']['common_is_delivery'] == 1 && Web_ConfigModel::value('Plugin_Delivery') == 1)) { ?>
                    <li><a class="wz"><?= __('商家位置') ?></a></li>
                <?php } ?>
                <li><a class="bz"><?= __('包装清单') ?></a></li>
                <li><a class="sh"><?= __('售后保障') ?></a></li>
                <li><a class="zl"><?= __('购买咨询') ?>(<?= $consult_num ?>)</a></li>
            </ul>

            <ul class="goods_det_about_cont">

                <!-- 商家位置 -->
                <li class="wz_1 clearfix" style="display: none;">
                    <?php if ($entity_shop) { ?>
                        <div id="baidu_map" style="height:600px;width: 79%;border:1px solid gray"></div>
                        <div class="entity_shop">
                            <?php foreach ($entity_shop as $key => $value) { ?>
                                <div class="entity_shop_box">
                                    <strong class="entity_shop_name"><?= $value['entity_name'] ?></strong>
                                    <?php if (in_array($value['province'], array('北京市', '上海市', '天津市', '重庆市', '香港特别行政区', '澳门特别行政区'))) { ?>
                                        <span class="entity_shop_address"><?= __("地址：") ?><?= $value['city'] ?><?= $value['entity_xxaddr'] ?></span>
                                    <?php } else { ?>
                                        <span class="entity_shop_address"><?= __("地址：") ?><?= $value['province'] ?><?= $value['city'] ?><?= $value['entity_xxaddr'] ?></span>
                                    <?php } ?>
                                    <span class="entity_shop_tel"><?= __("电话：") ?><?= $value['entity_tel'] ?></span>
                                </div>
                            <?php } ?>
                        </div>
                        <script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=5At3anZe83x8oOpFap42Gt8eHYpy3wm9"></script>
                        <link href="<?= $this->view->css_com ?>/jquery/plugins/dialog/green.css" rel="stylesheet">
                        <script type="text/javascript"></script>
                    <?php } ?>
                </li>
                <!--商品咨询-->
                <div id="goodsadvisory" style="display:none;" class="ncs-commend-main zl_1"></div>
                <!-- 商品评论 -->
                <div id="goodseval" style="display:none;" class="ncs-commend-main pl_1"></div>
                <!-- 商品查询 -->
                <div id="saleseval" style="display:none;" class="ncs-commend-main xs_1"></div>
                <!-- 详细-->

                <li class="xq_1 clearfixcat pl20 pr20 relative product-details-height" style="display:block;">
                    <div class="tc product-details-loading">
                        <img class="loading-width" src="<?= $this->view->img ?>/large-loading.gif" alt="loading">
                    </div>

                </li>
                <!-- 包装清单 -->
                <li class="bz_1 tlf" style="display: none">
                    <div class="product-details">
                        <div>
                            <?= html_entity_decode($goods_detail['common_base']['common_packing_list']) ?>
                        </div>
                    </div>
                </li>
                <!-- 售后服务 -->
                <li class="sh_1 tlf" style="display: none">
                    <div class="product-details">
                        <div>
                            <?= html_entity_decode($goods_detail['common_base']['common_service']) ?>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<!--</div>-->

<!-- 登录遮罩层 -->
<div id="login_content" style="display:none;"></div>

<script>
    var goods_id = <?=($goods_detail['goods_base']['goods_id'])?>;
    var common_id = <?=($goods_detail['goods_base']['common_id'])?>;
    var shop_id = <?=($shop_detail['shop_id'])?>;
    lazyload();

    //数据罗盘更新数据
    $.ajax({
        type: 'POST',
        url: SITE_URL + "?ctl=Goods_Goods&met=analytic_goods&typ=json",
        data: {goods_id: goods_id, shop_id: shop_id, url: window.location.href},
        dataType: 'JSON',
        success: function (a) {
            console.log(a)
        }
    });

    function contains(arr, str) {//检测goods_id是否存入
        var i = arr.length;
        while (i--) {
            if (arr[i] == str) {
                return true;
            }
        }
        return false;
    }

    //加入购物车
    $(".tuan_join_cart").bind("click", function () {

        if (!$("#nums").val()) {
            $("#nums").val(1)
        }
        if (<?=$shop_owner?>) {
            Public.tips.warning("<?=__('不能购买自己商店的商品！')?>");
            //$.dialog.alert('不能购买自己商店的商品！');

            return false;
        }
        if (<?=$dist_owner?>) {
            Public.tips.warning("<?=__('分销商的商品不可以购买！')?>");

            return false;
        }

        if (<?=$IsHaveBuy?>) {
            Public.tips.warning("<?=__('您已达购买上限！')?>");
            //$.dialog.alert('您达到购买上限！');
            return false;
        }
        if (<?=$IsOfflineBuy?> && <?=$IsOfflineBuy?> >
        $("#nums").val()
    )
        {
            Public.tips.warning("<?=__('您未达到购买下限！')?>");
            return false;
        }

        if ("<?=$goods_detail['buy_limit']?>" > 0 && "<?=$goods_detail['buy_limit']?>" < $("#nums").val() && !<?=$IsOfflineBuy?>) {
            Public.tips.warning("<?=__('该商品每人限购') . $goods_detail['buy_limit'] . __('件！')?>");
            return false;
        }

        goods_num = $("#nums").val();

        if ($.cookie('key')) {
            $.ajax({
                url: SITE_URL + '?ctl=Buyer_Cart&met=addCart&typ=json',
                data: {goods_id: goods_id, goods_num: goods_num},
                dataType: "json",
                contentType: "application/json;charset=utf-8",
                async: false,
                success: function (a) {
                    if (a.status == 250) {
                        Public.tips.error(a.msg);
                        //$.dialog.alert(a.msg);
                    } else {
                        //加入购物车成功后，修改购物车数量
                        $.ajax({
                            type: "GET",
                            url: SITE_URL + "?ctl=Buyer_Cart&met=getCartGoodsNum&typ=json",
                            data: {},
                            dataType: "json",
                            success: function (data) {
                                getCartList();
                                $('#cart_num').show();
                                $('.cart_num_toolbar').show();
                                $('#cart_num').html(data.data.cart_count);
                                $('.cart_num_toolbar').html(data.data.cart_count);
                            }
                        });
                        $.dialog({
                            title: "<?=__('加入购物车')?>",
                            height: 100,
                            width: 250,
                            lock: true,
                            drag: false,
                            content: 'url: ' + SITE_URL + '?ctl=Buyer_Cart&met=add&typ=e'
                        });
                    }
                },
                failure: function (a) {
                    Public.tips.error("<?=__('操作失败！')?>");
                    //$.dialog.alert("操作失败！");
                }
            });
        } else {
            $("#login_content").show();
            load_goodseval(SITE_URL + '?ctl=Index&met=fastLogin', 'login_content');
        }
    });

    //立即购买虚拟商品
    $(".buy_now_virtual").bind("click", function () {

        if (!$("#nums").val()) {
            $("#nums").val(1)
        }
        if (<?=$shop_owner?>) {
            Public.tips.warning("<?=__('不能购买自己商店的商品！')?>");
            return false;
        }
        if (<?=$dist_owner?>) {
            Public.tips.warning("<?=__('分销商的商品不可以购买！')?>");
            return false;
        }
        if (<?=$IsHaveBuy?>) {
            Public.tips.warning("<?=__('您已达购买上限！')?>");
            return false;
        }
        if (<?=$IsOfflineBuy?> && <?=$IsOfflineBuy?> >
        $("#nums").val()
    )
        {
            Public.tips.warning("<?=__('您未达到购买下限！')?>");
            return false;
        }

        if (<?=$goods_detail['buy_limit']?> >
        0 && <?=$goods_detail['buy_limit']?> < $("#nums").val() && !<?=$IsOfflineBuy?>)
        {
            Public.tips.warning("<?=__('该商品每人限购') . $goods_detail['buy_limit'] . __('件！')?>");
            return false;
        }
        if ($.cookie('key')) {
            $.ajax({
                type: 'POST',
                url: SITE_URL + "?ctl=Goods_Goods&met=checkVirtual&typ=json",
                data: {goods_id: goods_id, goods_num: $('#nums').val()},
                dataType: 'JSON',
                success: function (a) {
                    if (a.status == 250) {
                        Public.tips.warning("<?=__('您已达购买上限！')?>");
                        return false;
                    } else {
                        window.location.href = SITE_URL + '?ctl=Buyer_Cart&met=buyVirtual&goods_id=' + goods_id + '&goods_num=' + $("#nums").val();
                    }
                }
            });
        } else {
            $("#login_content").show();
            load_goodseval(SITE_URL + '?ctl=Index&met=fastLogin', 'login_content');
        }
    });

    //立即购买 - 实物商品
    $(".buy_now").bind("click", function () {
        if (!$("#nums").val()) {
            $("#nums").val(1)
        }
        if (<?=$shop_owner?>) {
            Public.tips.warning("<?=__('不能购买自己商店的商品！')?>");
            return false;
        }
        if (<?=$dist_owner?>) {
            Public.tips.warning("<?=__('分销商的商品不可以购买！')?>");
            return false;
        }
        if (<?=$IsHaveBuy?>) {
            Public.tips.warning("<?=__('您已达购买上限！')?>");
            return false;
        }
        if (<?=$IsOfflineBuy?> && <?=$IsOfflineBuy?> >
        $("#nums").val()
    )
        {
            Public.tips.warning("<?=__('您未达到购买下限！')?>");
            return false;
        }
        if (<?=$goods_detail['buy_limit']?> >
        0 && <?=$goods_detail['buy_limit']?> < $("#nums").val() && !<?=$IsOfflineBuy?>)
        {
            Public.tips.warning("<?=__('该商品每人限购') . $goods_detail['buy_limit'] . __('件！')?>");
            return false;
        }
        if ($.cookie('key')) {
            $.ajax({
                url: SITE_URL + '?ctl=Buyer_Cart&met=addCart&typ=json',
                data: {goods_id: goods_id, goods_num: $("#nums").val(), buy_now: 1},
                dataType: "json",
                contentType: "application/json;charset=utf-8",
                async: false,
                success: function (a) {
                    if (a.status == 250) {
                        Public.tips.error(a.msg);
                    } else {
                        if (a.data.cart_id) {
                            window.location.href = SITE_URL + '?ctl=Buyer_Cart&met=confirm&product_id=' + a.data.cart_id;
                        }
                    }
                },
                failure: function (a) {
                    Public.tips.error("<?=__('操作失败！')?>");
                    //$.dialog.alert("操作失败！");
                }
            });
        } else {
            $("#login_content").show();
            load_goodseval(SITE_URL + '?ctl=Index&met=fastLogin', 'login_content');
        }
    })


    $goodsLimit = <?= $goods_detail['buy_limit'] ?>;
    //门店自提
    $('a[nctype="get_chain"]').click(function () {
        var goodsQuantity = $('#nums').val();
        if ($goodsLimit > 0 && goodsQuantity > $goodsLimit) {
            return Public.tips.warning('该商品每人限购' + $goodsLimit + '件')
        }
        $.ajax({
            type: 'POST',
            url: "<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=chain&typ=json",
            data: {goods_id: goods_id, shop_id: shop_id, goods_num: goodsQuantity},
            dataType: 'JSON',
            success: function (data) {
                if (data.status != 250) {
                    $.dialog({
                        title: '<?=__('查看门店')?>',
                        content: "url: <?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=chain&goods_id=" + goods_id + "&shop_id=" + shop_id+'&limit='+$goodsLimit,
                        data: {callback: callback},
                        width: 800,
                        lock: true
                    })

                    function callback(url, chain_id) {
                        // api.close();
                        $.ajax({
                            type: 'POST',
                            url: "<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=check_chain_stock&typ=json",
                            data: {goods_id: goods_id, chain_id: chain_id, goods_num: 1},
                            dataType: 'JSON',
                            success: function (a) {
                                if (a.status == 200) {
                                    window.location.href = url;
                                } else {
                                    Public.tips.error(a.msg);
                                }
                            }
                        });
                    }
                } else {
                    Public.tips.error(data.msg);
                }
            }
        })
    });
    //热销商品，热收商品
    $(".selling").children().eq(0).hover(function () {
        $("#hot_salle").show();
        $("#hot_collect").hide();
    });
    $(".selling").children().eq(1).hover(function () {
        $("#hot_salle").hide();
        $("#hot_collect").show();
    });

    //收藏商品
    window.collectGoods = function (e) {
        if ($.cookie('key')) {
            $.post(SITE_URL + '?ctl=Goods_Goods&met=collectGoods&typ=json', {goods_id: e}, function (data) {
                if (data.status == 200) {
                    Public.tips.success(data.data.msg);
                    $(".icon-icoheart").addClass("icon-taoxinshi").removeClass('icon-icoheart');
                    //toolbar显示收藏效果
                    $("#collect_lable").removeClass('icon-icoheart');
                    $("#collect_lable").addClass('icon-taoxinshi').addClass('bbc_color');
                } else {
                    Public.tips.error(data.data.msg);
                }
            });
        } else {
            $("#login_content").show();
            load_goodseval(SITE_URL + '?ctl=Index&met=fastLogin', 'login_content');
        }
    }

    //收藏店铺
    window.collectShop = function (e) {
        if ($.cookie('key')) {
            $.post(SITE_URL + '?ctl=Shop&met=addCollectShop&typ=json', {shop_id: e}, function (data) {
                if (data.status == 200) {
                    Public.tips.success(data.data.msg);
                } else {
                    Public.tips.error(data.data.msg);
                }
            });
        } else {
            $("#login_content").show();
            load_goodseval(SITE_URL + '?ctl=Index&met=fastLogin', 'login_content');
        }
    }

    //输入内容解除灰置
    $("input[name='searchGoodsList']").bind("input propertychange", function () {
        var search_css = $("input[name='searchGoodsList']").val();
        if (search_css) {
            $("#searchGoodsList").css('background', '#e45050');
        } else {
            $("#searchGoodsList").css('background', '');
        }
    });
    $("input[name='searchGoodsList']").blur(function () {
        var search = $("input[name='searchGoodsList']").val();
        if (search) {
            $("#searchGoodsList").attr('href', SITE_URL + '?ctl=Shop&met=goodsList&search=' + search + '&id=' + shop_id);
        }
    });

    //立即购买一件代发的分销商品
    $(".buy_now_supplier").bind("click", function () {
        if (!$("#nums").val()) {
            $("#nums").val(1)
        }
        if ($(this).hasClass('tuan_go_gray')) {
            Public.tips.warning("<?=__('当前地区无货！')?>");
            return false;
        }
        if (<?=$shop_owner?>) {
            Public.tips.warning("<?=__('不能购买自己商店的商品！')?>");
            return false;
        }
        if (<?=$dist_owner?>) {
            Public.tips.warning("<?=__('分销商的商品不可以购买！')?>");
            return false;
        }
        if (<?=$IsHaveBuy?>) {
            Public.tips.warning("<?=__('您已达购买上限！')?>");
            return false;
        }
        if (<?=$IsOfflineBuy?> && <?=$IsOfflineBuy?> >
        $("#nums").val()
    )
        {
            Public.tips.warning("<?=__('您未达到购买下限！')?>");
            return false;
        }
        if (<?=$goods_detail['buy_limit']?> >
        0 && <?=$goods_detail['buy_limit']?> < $("#nums").val() && !<?=$IsOfflineBuy?>)
        {
            Public.tips.warning("<?=__('该商品每人限购') . $goods_detail['buy_limit'] . __('件！')?>");
            return false;
        }
        if ($.cookie('key')) {
            window.location.href = SITE_URL + '?ctl=Buyer_Cart&met=confirmGoods&goods_id=' + goods_id + '&goods_num=' + $("#nums").val();
        } else {

            $("#login_content").show();
            load_goodseval(SITE_URL + '?ctl=Index&met=fastLogin', 'login_content');
        }
    });
</script>

<script>
    $(document).ready(function () {
        url = 'index.php?ctl=Goods_Goods&met=getShopCat&shop_id=' + shop_id;
        $(".ser_lists").load(url, function () {
        });

        <?php if(isset($_REQUEST['from'])){ ?>
        from = '<?=$_REQUEST['from']?>';
        <?php }else{ ?>
        from = '';
        <?php } ?>

        if (from == 'consult') {
            window.location.hash = "#elist";
            $(".zl").click();
        }
    })


    $('.share').click(function () {
        if ($('.bdsharebuttonbox').css('display') == 'block') {
            $('.bdsharebuttonbox').hide();
            $(".bdsharebuttonbox").addClass('hidden');
        }
        else {
            $('.bdsharebuttonbox').show();
            $(".bdsharebuttonbox").removeClass('hidden');
        }
    });

    $('.wz').click(function () {
        $(".pl_1").css("display", "none");
        $(".zl_1").css("display", "none");
        $(".xs_1").css("display", "none");
        $(".wz_1").css("display", "block");
        $(".bz_1").css("display", "none");
        $(".sh_1").css("display", "none");
        $(".xq_1").css("display", "none");

        var map = new BMap.Map("baidu_map", {enableMapClick: false});
        var geo = new BMap.Geocoder();
        var city = new BMap.LocalCity();
        var top_left_navigation = new BMap.NavigationControl();
        var overView = new BMap.OverviewMapControl();
        var currentArea = '';//当前地图中心点的区域对象
        var currentCity = '';//当前地图中心点的所在城市
        var idArray = new Array();

        map.addControl(top_left_navigation);
        map.addControl(overView);
        map.enableScrollWheelZoom(true);
        city.get(local_city);

        function local_city(cityResult) {
            map.centerAndZoom(cityResult.center, 15);
            currentCity = cityResult.name;
            pointArray = new Array();
            var point = '';
            var marker = '';
            var label = '';
            var k = 0;
            <?php if($entity_shop){
            foreach ($entity_shop as $key => $value) {
            if($value['lng'] && $value['lat']){
            ?>
            point = new BMap.Point(<?=$value['lng']?>, <?=$value['lat']?>);
            pointArray[k++] = point;
            label = new BMap.Label("<?=$value['entity_name']?>", {offset: new BMap.Size(20, -10)});
            marker = new BMap.Marker(point);
            marker.setTitle("<?=__('地址-')?>" + k);
            marker.setLabel(label);
            marker.enableDragging();
            //                                    marker.addEventListener("dragend",getMarkerPoint);
            map.addOverlay(marker);
            idArray["<?=__('地址-')?>" + k] = <?=$value['entity_id']?>;
            <?php } } }?>
            map.setViewport(pointArray);
        }

        function getPointArea(point, callback) {//通过点找到地区
            geo.getLocation(point, function (rs) {
                var addComp = rs.addressComponents;
                if (addComp.province != '') {
                    callback(addComp);
                }
            }, {numPois: 1});
        }
    });

    $('.zl').click(function () {
        $(".pl_1").css("display", "none");
        $(".zl_1").css("display", "block");
        $(".xs_1").css("display", "none");
        $(".wz_1").css("display", "none");
        $(".bz_1").css("display", "none");
        $(".sh_1").css("display", "none");
        $(".xq_1").css("display", "none");
    });

    $(window).load(function () {
        $.ajax({
            type: 'POST',
            url: SITE_URL + '/index.php?ctl=Goods_Goods&met=getGoodsDetailFormat&typ=json',
            data: {gid: goods_id},
            dataType: 'JSON',
            success: function (data) {
                var html = '';
                if (data.data.goods_format_top) {
                    html += data.data.goods_format_top;
                }
                if (data.data.brand_name) {
                    html += '<span class="xgbrand one-overflow" title="'+data.data.brand_name+'"><?=__("品牌")?>：' + data.data.brand_name + '</span>';
                }
                if (data.data.common_property_row) {
                    for (var i in data.data.common_property_row) {
                        if (data.data.common_property_row[i]) {
                            html += '<span class="xgspan one-overflow" title="'+data.data.common_property_row[i]+'">' + i + '：' + data.data.common_property_row[i] + '</span>';
                        }
                    }
                }
                html += '<p class="xgdes">商品描述：' + data.data.common_detail + '</p>';
                if (data.data.goods_format_bottom) {
                    html += data.data.goods_format_bottom;
                }
                var htmls = html.replace(/type="application\/x-shockwave-flash"/g, ' ');
                $('.xq_1').html(htmls);
            }
        })
    })
</script>
<!--  地址选择 -->
<script>
    var $cur_area_list, $cur_tab, next_tab_id = 0, cur_select_area = [], calc_area_id = '', calced_area = [], calced_area_transport = [], cur_select_area_ids = [];
    var transport_rule = <?=json_encode($goods_detail['transport'])?>;
    <?php if($goods_detail['goods_base']['goods_stock']){?>
    function setCookie(name, value) {
        var Days = 30;
        var exp = new Date();
        exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
        document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
    }

    function getCookie(name) {
        var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
        if (arr != null) {
            return unescape(arr[2]);
        } else {
            return "";
        }
    }

    $(function () {
        var area_id_cookie = getCookie('area_id');
        var common_id_cookie = getCookie('common_id');
        if (area_id_cookie != '' && common_id_cookie != '') {
            $.post(SITE_URL + '?ctl=Goods_Goods&met=getTramsport&area_id=' + area_id_cookie + '&area_id_cookie=' + common_id_cookie + '&typ=json', function (data) {
                if (data.status === 250) {
                    $('.goods_have').html("<?=__('无货')?>");
                    $('.transport').html('');
                    $('a[nctype="buynow_submit"]').addClass('no-buynow');
                    $('a[nctype="addcart_submit"]').addClass('no-buynow');
                    $('.buy_box').hide();
                    $('.buy_box_gray').show();
                } else {
                    $('.goods_have').html(' ');
                    $('a[nctype="buynow_submit"]').removeClass('no-buynow');
                    $('a[nctype="addcart_submit"]').removeClass('no-buynow');
                    $('.buy_box').show();
                    $('.buy_box_gray').hide();
                }

            });
        }
		//如果是供应商商品,隐藏掉购买按钮
		var hide_flag = '<?=$goods_detail['common_base']['product_distributor_flag'];?>';
		if(hide_flag > 0){
			$(".tuan_go").hide();
		}
    });
    $(document).ready(function () {
        /*即便是无货状态也可以选择/更改 地址*/
        var status = $('.goods_have').innerHTML;
        if (status === '无货') {
            $("#ncs-freight-selector").on('mouseover', function () {
                $("#ncs-freight-selector").addClass("hover");
            });
            $("#ncs-freight-selector").on('mouseleave', function () {
                $("#ncs-freight-selector").removeClass("hover");
            });
        }

        $("#ncs-freight-selector").hover(function () {
            //如果店铺没有设置默认显示区域，马上异步请求
            if (typeof nc_a === "undefined") {
                $.post(SITE_URL + '?ctl=Base_District&met=getAllArea&typ=json', function (data) {
                        data = JSON.parse(data);
                        nc_a = data.data;
                        $cur_tab = $('#ncs-stock').find('li[data-index="0"]');
                        _loadArea(0);
                    }
                );
            }
            $(this).addClass("hover");
            $(this).on('mouseleave', function () {
                $(this).removeClass("hover");
            });
        });

        // function delCookie(name){
        //     var exp = new Date();
        //     exp.setTime(exp.getTime() - 1);
        //     var cval=getCookie(name);
        //     if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();
        // }
        $('ul[class="area-list"]').on('click', 'a', function () {
            $('#ncs-freight-selector').unbind('mouseleave');
            var tab_id = parseInt($(this).parents('div[data-widget="tab-content"]:first').attr('data-area'));
            if (tab_id == 0) {
                cur_select_area = [];
                cur_select_area_ids = []
            }
            ;
            if (tab_id == 1 && cur_select_area.length > 1) {
                cur_select_area.pop();
                cur_select_area_ids.pop();
                if (cur_select_area.length > 1) {
                    cur_select_area.pop();
                    cur_select_area_ids.pop();
                }
            }
            next_tab_id = tab_id + 1;
            var area_id = $(this).attr('data-value');
            if (tab_id == 0) {
                $.cookie('areaId', area_id)
            }
            $cur_tab = $('#ncs-stock').find('li[data-index="' + tab_id + '"]');
            $cur_tab.find('em').html($(this).html());
            $cur_tab.find('em').attr('data_value', $(this).attr('data-value'));
            $cur_tab.find('i').html(' ∨');
            if (tab_id < 2) {
                cur_select_area.push($(this).html());
                cur_select_area_ids.push(area_id);
                $cur_tab.find('a').removeClass('hover');
                $cur_tab.nextAll().remove();
                if (typeof nc_a === "undefined") {
                    $.post(SITE_URL + '?ctl=Base_District&met=getAllArea&typ=json', function (data) {
                        data = JSON.parse(data)
                        nc_a = data.data;
                        _loadArea(area_id);
                    })
                } else {
                    _loadArea(area_id);
                }
            } else {
                //点击第二级，不需要显示子分类
                if (cur_select_area.length == 3) {
                    cur_select_area.pop();
                    cur_select_area_ids.pop();
                }
                cur_select_area.push($(this).html());
                cur_select_area_ids.push(area_id);
                // console.log(cur_select_area);
                // console.log(cur_select_area.join(''));
                $('#select-city-cookie').text(cur_select_area.join(''));
                $('#ncs-freight-selector').removeClass("hover");
                _calc();
            }
            $('#ncs-stock').find('li[data-widget="tab-item"]').on('click', 'a', function () {
                var tab_id = parseInt($(this).parent().attr('data-index'));
                if (tab_id < 2) {
                    $(this).parent().nextAll().remove();
                    $(this).addClass('hover');
                    $('#ncs-stock').find('div[data-widget="tab-content"]').each(function () {
                        if ($(this).attr("data-area") == tab_id) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });
        });


        // $('ul[class="area-list"]').on('click', 'a', function () {
        //     $("#ncs-freight-selector").off('mouseenter').unbind('mouseleave');
        //     $("#ncs-freight-selector").unbind("click");
        //     var tab_id = parseInt($(this).parents('div[data-widget="tab-content"]:first').attr('data-area'));
        //     if (tab_id == 0) {
        //         cur_select_area = [];
        //         cur_select_area_ids = [];
        //     }
        //     if (tab_id == 1 && cur_select_area.length > 1) {
        //         cur_select_area.pop();
        //         cur_select_area_ids.pop();
        //         if (cur_select_area.length > 1) {
        //             cur_select_area.pop();
        //             cur_select_area_ids.pop();
        //         }
        //     }
        //     next_tab_id = tab_id + 1;
        //     var area_id = $(this).attr("data-value");
        //     if (tab_id == 0) {
        //         $.cookie("goodslist_area_id", area_id);
        //     }
        //     $cur_tab = $("#ncs-stock").find("li[data-index=\"" + tab_id + "\"]");
        //     $cur_tab.find("em").html($(this).html());
        //     $cur_tab.find("em").attr("data_value", $(this).attr("data-value"));
        //     $cur_tab.find("i").html(" ∨");
        //     if (tab_id < 2) {
        //         cur_select_area.push($(this).html());
        //         cur_select_area_ids.push(area_id);
        //         $cur_tab.find("a").removeClass("hover");
        //         $cur_tab.nextAll().remove();
        //         if (typeof nc_a === "undefined") {
        //             $.post(SITE_URL + "?ctl=Base_District&met=getAllArea&typ=json", function (data) {
        //                 data = JSON.parse(data)
        //                 nc_a = data;
        //                 _loadArea(area_id);
        //             });
        //         } else {
        //             _loadArea(area_id);
        //         }
        //     } else {
        //         //点击第三级，不需要显示子分类
        //         if (cur_select_area.length == 3) {
        //             cur_select_area.pop();
        //             cur_select_area_ids.pop();
        //         }
        //         cur_select_area.push($(this).html());
        //         cur_select_area_ids.push(area_id);
        //         $('#ncs-freight-selector > div[class="text"] > div').html(cur_select_area.join(''));
        //         $('#ncs-freight-selector').removeClass("hover");
        //     }
        //     $('#ncs-stock').find('li[data-widget="tab-item"]').on('click','a',function(){
        //         var tab_id = parseInt($(this).parent().attr('data-index'));
        //         if (tab_id < 2) {
        //             $(this).parent().nextAll().remove();
        //             $(this).addClass('hover');
        //             $('#ncs-stock').find('div[data-widget="tab-content"]').each(function(){
        //                 if ($(this).attr("data-area") == tab_id) {
        //                     $(this).show();
        //                 } else {
        //                     $(this).hide();
        //                 }
        //             });
        //         }
        //     });
        // });

        function _loadArea(area_id) {
            // console.log(area_id);
            // console.log(nc_a[area_id]);
            if (nc_a[area_id] && nc_a[area_id].length > 0) {
                $('#ncs-stock').find('div[data-widget="tab-content"]').each(function () {
                    if ($(this).attr("data-area") == next_tab_id) {
                        $(this).show();
                        $cur_area_list = $(this).find('ul');
                        $cur_area_list.html('');
                    } else {
                        $(this).hide();
                    }
                });
                var areas = [];
                areas = nc_a[area_id];
                for (i = 0; i < nc_a[area_id].length; i++) {
                    $cur_area_list.append("<li><a data-value='" + nc_a[area_id][i]['district_id'] + "' >" + nc_a[area_id][i]['district_name'] + "</a></li>");
                }
                if (area_id > 0) {
                    $cur_tab.after('<li data-index="' + (next_tab_id) + '" data-widget="tab-item"><a class="hover"  ><em><?=__("请选择")?></em><i> ∨</i></a></li>');
                }
            } else {
                //点击第一二级时，已经到了最后一级
                $cur_tab.find('a').addClass('hover');
                $('#ncs-freight-selector > div[class="text"] > div').html(cur_select_area);
                $('#ncs-freight-selector').removeClass("hover");
                _calc();
            }
        }

        //计算运费，是否配送
        function _calc() {
            var _args = '';
            calc_area_id = $('li[data-index="2"]').find("em").attr("data_value");
            if (!calc_area_id) {
                calc_area_id = $("li[data-index='1']").find("em").attr("data_value");
            }
            setCookie('area_id', calc_area_id);
            var transport_area = $('li[data-index="0"]').find("em").html();
            if ($('li[data-index="1"]').find("em").html()) {
                transport_area += $('li[data-index="1"]').find("em").html();
            }
            if ($('li[data-index="2"]').find("em").html()) {
                transport_area += $('li[data-index="2"]').find("em").html();
            }
            if (typeof calced_area[calc_area_id] == 'undefined') {
                //需要请求配送区域设置
                setCookie('common_id',<?=$goods_detail['common_base']['common_id']?>);
                $.post(SITE_URL + '?ctl=Goods_Goods&met=getTramsport&area_id=' + calc_area_id + '&common_id=' + <?=($goods_detail['common_base']['common_id'])?> +'&typ=json', function (data) {
                    calced_area[calc_area_id] = data.msg;
                    calced_area_transport[calc_area_id] = data.data.transport_str;
                    $(".text.pr18").find('div').html(transport_area);
                    $.cookie('goodslist_area_id', calc_area_id);
                    calc_area = $("#ncs-freight-selector").find(".text div").html();
                    $.cookie('goodslist_area_name', calc_area);
                    if (data.status === 250) {
                        $('.goods_have').html("<?=__('无货')?>");
                        $('.transport').html('');
                        $('a[nctype="buynow_submit"]').addClass('no-buynow');
                        $('a[nctype="addcart_submit"]').addClass('no-buynow');
                        $('.buy_box').hide();
                        $('.buy_box_gray').show();
                    } else {
                        $('.goods_have').html(' ');
//                        $('.transport').html(data.data.transport_str);
                        $('a[nctype="buynow_submit"]').removeClass('no-buynow');
                        $('a[nctype="addcart_submit"]').removeClass('no-buynow');
                        $('.buy_box').show();
                        $('.buy_box_gray').hide();
                        transport_rule = data.data;
                        get_transport_all_money(transport_rule);
                    }

                });
            } else {
                if (calced_area[calc_area_id] === 'failure') {
                    $('.goods_have').html("<?=__('无货')?>");
                    $('.transport').html('');
                    $('a[nctype="buynow_submit"]').addClass('no-buynow');
                    $('a[nctype="addcart_submit"]').addClass('no-buynow');
                    $('#store-free-time').hide();
                } else {
                    $('.goods_have').html(' ');
                    $('.transport').html(calced_area_transport[calc_area_id]);
                    $('a[nctype="buynow_submit"]').removeClass('no-buynow');
                    $('a[nctype="addcart_submit"]').removeClass('no-buynow');
                    $('#store-free-time').show();
                    $('.buy_box').show();
                    $('.buy_box_gray').hide();
                }
            }
        }
    });
    <?php }?>
    function close_area() {
        var transport_area = $('li[data-index="0"]').find("em").html();
        if ($('li[data-index="1"]').find("em").html() && $('li[data-index="1"]').find("em").hasClass('hover')) {
            transport_area += $('li[data-index="1"]').find("em").html();
        }
        $(".text.pr18").find('div').html(transport_area);
        $('#ncs-freight-selector').removeClass('hover');
    }

    function consult() {
        if (window.location.href.indexOf("&from=consult") != -1) {
            window.location.reload()
        } else {
            window.location.href = window.location.href + "&from=consult";
        }
    }

    //倒计时
    function timer(intDiff) {
        if (typeof(intDiff) == 'undefined' || intDiff <= 0) {
            $('.count-down').hide();
            return;
        }
        window.setInterval(function () {
            var day = 0,
                hour = 0,
                minute = 0,
                second = 0;//时间默认值
            if (intDiff > 0) {
                day = Math.floor(intDiff / (60 * 60 * 24));
                hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
            }

            if (intDiff == 0) {
                window.location.reload();
            }

            if (minute <= 9) minute = '0' + minute;
            if (second <= 9) second = '0' + second;
            $('#day_show').html('<i class="pri-color">' + day + '</i>');
            $('#hour_show').html('<i class="pri-color">' + hour + '</i>');
            $('#minute_show').html('<i class="pri-color">' + minute + '</i>');
            $('#second_show').html('<i class="pri-color">' + second + '</i>');
            intDiff--;
        }, 1000);
    }

    var intDiff = parseInt(<?=$diff_time?>);//倒计时总秒数量

    timer(intDiff);

    function get_transport_all_money(transport_rule) {
        var goods_price = $('#goods_price').val();
        var num = $('#nums').val();
        var shipping = <?=$goods_detail['shop_base']['shop_free_shipping']?>;

        if (shipping > 0 && goods_price * num > shipping) {
            $('#transport_all_money').html("<?=__('免运费')?>");
            return false;
        }
        //运费规则
        var unit = 0;
        var additional = 0;
        if (typeof(transport_rule.rule_info) != 'undefined' && typeof(transport_rule.rule_info.id) != 'undefined') {
            var transport_all_money = 0;
            if (transport_rule.rule_info.rule_type == 1) {
                //按重量
                var weight = <?=$goods_detail['common_base']['common_cubage']?>;
                var weights = (weight * num).toFixed(2);
                var unit = weights;
                if(unit>transport_rule.rule_info.default_num){
                    var molecule=unit-transport_rule.rule_info.default_num;
                    var additional=Math.ceil(molecule/transport_rule.rule_info.add_num);
                }
            } else if (transport_rule.rule_info.rule_type == 2) {
                //按数量
                var unit = num;
            }else if(transport_rule.rule_info.rule_type == 3){
                //按体积
                var volume = <?=$goods_detail['common_base']['common_length']*$goods_detail['common_base']['common_width']*$goods_detail['common_base']['common_height']?>;
                var volumes = (volume * num).toFixed(2);
                var unit = volumes;
                if(unit>transport_rule.rule_info.default_num){
                    var molecule=unit-transport_rule.rule_info.default_num;
                    var additional=Math.ceil(molecule/transport_rule.rule_info.add_num);
                }    
            } else {
                $('#transport_all_money').html('');
            }
            console.log(transport_rule.rule_info.add_price*additional);
            transport_all_money = parseFloat(transport_rule.rule_info.default_price)+parseFloat(transport_rule.rule_info.add_price*additional);
            $('#transport_all_money').html("<?=__('运费')?>: <?=__('￥')?>" + transport_all_money.toFixed(2));
        } else {
            if (typeof(transport_rule.transport_str) != 'undefined') {
                if (transport_rule.transport_str == '无货') {
                    $(".buy_box").hide();
                    $(".buy_box_gray").show();
                }
                $('#transport_all_money').html(transport_rule.transport_str);
            } else {
                $('#transport_all_money').html('');
            }

        }
        return;
    }

    get_transport_all_money(transport_rule);
</script>

<script>
    $.ajax({
        url: SITE_URL + '/index.php?ctl=GroupBuy&met=groupBuyViews',
        type: 'GET',
        data: {gid: goods_id},
        success: function (data) {

        }
    })
</script>

<script>
    window._bd_share_config = {
        "common": {
            "bdSnsKey": {},
            "bdText": "",
            "bdMini": "2",
            "bdPic": "",
            "bdUrl": window.location.href,
            "bdStyle": "0",
            "bdSize": "16",
        },
        "share": {},
        "image": {"viewList": ["qzone", "tsina", "tqq", "renren", "weixin"], "viewText": "分享到：", "viewSize": "16"},
        "selectShare": {"bdContainerClass": null, "bdSelectMiniList": ["qzone", "tsina", "tqq", "renren", "weixin"]}
    };
    with (document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = '/shop/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];</script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
