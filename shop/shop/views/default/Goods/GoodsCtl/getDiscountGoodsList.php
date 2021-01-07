<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this -> view -> getTplPath() . '/' . 'header.php';
?>
<!-- 替换css -->
<link rel="stylesheet" type="text/css" href="<?= cdn_url(Yf_Registry::get('base_url').'/min/?f=/shop/static/default/css/goods-list.css,/shop/static/default/css/tips.css,/shop/static/default/css/login.css,/shop/static/default/css/new_file.css'); ?>"/>

    <?php
    $min = "/shop/static/default/js/tuangou-index.js,";
    $min .= "/shop/static/common/js/plugins/jquery.slideBox.min.js,";
//$min .= "/shop/static/default/js/select2.min.js";
    $min = substr($min,0,-1);
    ?>
    <script type="text/javascript" src="<?= cdn_url(Yf_Registry::get('base_url').'/min/?f='.$min); ?>"></script>


    <div class="shop-list-box">
        <div class="clearfix">
            <ul class="search_output fl">
                <li <?php if (!request_string('price')) echo 'class="active"'; ?>>
                    <a href="<?= url("Goods_Goods/getDiscountGoodsList", ['uptime' => $uptime]) ?><?php if ($goods_name) {
                        echo '&goods_name=' . $goods_name;
                    } ?>" title="<?php if ($uptime === 'desc') { ?><?= __('点击按上架时间升序') ?><?php } else { ?><?= __('点击按上架时间降序') ?><?php } ?>"><?= __('上架时间') ?><i class="iconfont <?php if ($uptime === 'asc') { ?>icon-iconjiantoushang<?php } else { ?>icon-iconjiantouxia<?php } ?>"></i></a>
                </li>

                <li <?php if (request_string('price')) { echo 'class="active"';} ?>>
                    <a href="<?= url("Goods_Goods/getDiscountGoodsList", ['price' => $price]) ?><?php if ($goods_name) {
                        echo '&goods_name=' . $goods_name;
                    } ?>" title="<?php if ($price === 'desc') { ?><?= __('点击按限时折扣价格升序') ?><?php } else { ?><?= __('点击按限时折扣价格降序') ?><?php } ?>"><?= __('折扣') ?><i class="iconfont <?php if ($price === 'asc') { ?>icon-iconjiantoushang<?php } else { ?>icon-iconjiantouxia<?php } ?>"></i></a>
                </li>
            </ul>

            <div class="fl list-searchbar">
                <input type="text" placeholder="<?= __('搜索词') ?>" name="goods_name" id="goods_name" value="<?= ($goods_name) ?>"/><a onclick="searchgoods()"><?= __('确定') ?></a>
            </div>

        </div>

        <!--商品-->
        <?php if($data['items']) { ?>
            <ul class="shop-list clearfix">

                <?php	foreach ($data['items'] as $key => $val){?>
                    <a href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&gid=<?= ($val['goods_id']) ?>">
                        <li>
                            <div>
                                <img src="<?=$val['goods_image']?>" />
                            </div>
                            <div>
                                <h4 class="one-overflow mb10"><?=$val['goods_name']?></h4>

                                <div class="clearfix">
                                    <p><?= format_money($val['discount_price'] ) ?></p>
                                    <p class="fr"><?= format_money($val['goods_price'] ) ?></p>
                                </div>
                                <div class="clearfix time">
                                    <span class="fl">已售<?=$val['goods_salenum']?></span>
                                    <div class="gr_good_lastime fr fnTimeCountDown" data-end="<?=$val['goods_end_time']?>">
                                            <span class="day">00</span><?= __('天') ?>
                                            <span class="hour">00</span><?= __('小时') ?>
                                            <span class="mini">00</span><?= __('分') ?>
                                            <span class="sec">00</span><?= __('秒') ?>
                                    </div>

                                </div>

                            </div>
                        </li>
                    </a>
                <?php }?>
            </ul>

            <?php if ($page_nav) { ?>
                <div style="clear:both"></div>
                <div class="page page_front">
                    <?= $page_nav ?>
                </div>
                <div style="clear:both"></div>
            <?php } ?>

        <?php } else {?>
            <div class="no_account">
                <img class='lazy' data-original="<?= $this -> view -> img ?>/ico_none.png"/>
                <p><?= __('暂无符合条件的数据记录') ?></p>
            </div>
        <?php } ?>


    </div>
    <!--html文件-->
    <script src="<?= $this->view->js_com ?>/plugins/jquery.timeCountDown.js"></script>
    <script>
        $(function () {
            var _TimeCountDown = $(".fnTimeCountDown");
            _TimeCountDown.fnTimeCountDown();
        });
        $('.search_output  li').click(function() {
            $('.search_output  li').removeClass('active');
            $(this).addClass('active');
            if($(this).find("i").hasClass('icon-iconjiantouxia')) {
                $(this).find("i").removeClass('icon-iconjiantouxia').addClass('icon-iconjiantoushang')
            } else {
                $(this).find("i").removeClass('icon-iconjiantoushang').addClass('icon-iconjiantouxia')
            }
        })


        //搜索商品
        function searchgoods() {
            var searchstr = $("#goods_name").val();
            //地址中的参数
            var params = window.location.search;

            params = changeURLPar(params, "goods_name", searchstr);

            window.location.href = SITE_URL + params;
        }

        function changeURLPar(destiny, par, par_value) {
            var pattern = par + "=([^&]*)";
            var replaceText = par + "=" + par_value;
            if (destiny.match(pattern)) {
                var tmp = new RegExp(pattern);
                tmp = destiny.replace(tmp, replaceText);
                return (tmp);
            }
            else {
                if (destiny.match("[\?]")) {
                    return destiny + "&" + replaceText;
                }
                else {
                    return destiny + "?" + replaceText;
                }


            }
            return destiny + "\n" + par + "\n" + par_value;
        }

    </script>

<?php
include $this -> view -> getTplPath() . '/' . 'footer.php';
?>
