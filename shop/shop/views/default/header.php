

<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
}
include $this->view->getTplPath() . '/' . 'site_nav.php';
$search_array = [];
if (is_array($this->searchWord)) {
    foreach ($this->searchWord as $key => $val) {
        $search_array[] = $val['search_char_index'];
    }
}

$search_words = array_map(function($v) {
    return "<a href='" . url('Goods_Goods/goodslist', [
            'typ' => 'e',
            'keywords' => $v
        ]) . "'>" . __($v) . "</a>";
}, $search_array);
$keywords = __(Web_ConfigModel::value('search_words'));
$shop_keywords = __(Web_ConfigModel::value('search_shop_words'));
?>
<?php
$ctl = request_string('ctl');
$met = request_string('met');
$request_uri = $ctl . '.' . $met . '.' . request_string('type');
$in = [
    'Goods_Goods.goods.goods',
    'Goods_Goods.goodslist.',
];
$minify = false;
if ($ctl && $met && in_array($request_uri, $in)) {
    $minify = true;
} ?>


<?php if ($minify) { 
$base_uri = Yf_Registry::get('root_uri');
$css_url = "";
$css_url .= $base_uri.'/shop/static/common/css/iealert/style.css,';
$css_url .= $base_uri.'/shop/static/default/css/select2.min.css';
?>
    <!-- 替换css -->
    <link rel="stylesheet" type="text/css" href="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f='.$css_url); ?>"/>
    <?php
    $min = $base_uri."/shop/static/common/js/iealert.js,";
    $min .= $base_uri."/shop/static/common/js/jquery.blueberry.js,";
    $min .= $base_uri."/shop/static/common/js/plugins/jquery.timeCountDown.js,";
    $min .= $base_uri."/shop/static/default/js/jquery.lazy.js,";
    //$min .= "/shop/static/default/js/select2.min.js";
    $min = substr($min, 0, -1);
    ?>
    <script type="text/javascript" src="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f=' . $min); ?>"></script>
<?php } else { ?>
    
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css_com ?>/iealert/style.css"/>
    <link href="<?= $this->view->css ?>/select2.min.css" rel="stylesheet">
    <script src="<?= $this->view->js_com ?>/iealert.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?= $this->view->js_com ?>/jquery.blueberry.js"></script>
    <script src="<?= $this->view->js_com ?>/plugins/jquery.timeCountDown.js"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/jquery.lazy.js"></script>
    <script src="<?= $this->view->js ?>/select2.min.js"></script>
<?php } ?>
<!-- 定位用户位置 @nsy 2019-02-20 -->
<script src="http://pv.sohu.com/cityjson?ie=utf-8"></script>
<script>
$(function() {
	if(!$.cookie("c_ip")){
		setTimeout("setClientIp()",3000);//设置5秒钟后执行
	}	
});
function setClientIp(){
	var ip = returnCitySN["cip"];
	$.ajax({
		url:SITE_URL + '?ctl=Index&met=setClientIpByCookie',
		type:'POST',
		data:{ip:ip},
		success: function (data) {
			$.cookie('c_ip',ip);
		}
	});
}
</script>
<?php include APP_PATH . '/alert_box.php'; ?>
<div class="bgf">
    <div class="wrap">
        <div class="head_cont clearfix">
            <div class="nav_left">
                <a href="<?= Yf_Registry::get('url') ?>" class="logo iblock w240"> <img class="wp100" src="<?php if (Web_ConfigModel::value('subsite_is_open') && isset($_COOKIE['sub_site_logo']) && $_COOKIE['sub_site_logo'] != '' && isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                        echo $_COOKIE['sub_site_logo'];
                    }elseif($_COOKIE['SHOP_ID']){
                        echo $shop_base['shop_logo'];
                    } else {
                        if (@$this->web['web_logo']) {
                            echo @$this->web['web_logo'];
                        } else {
                            echo $this->view->img . '/setting_logo.jpg';
                        }
                    } ?>"/> </a> <a href="#" class="download iconfont"></a>
            </div>
            <div class="nav_right clearfix">
                <ul class="clearfix search-types">
                    <li class="<?php if (@request_string('ctl') != 'Shop_Index') {
                        echo 'active';
                    } ?>" onclick="searchWords()">
                        <a href="javascript:void(0);" data-param='goods'><?= __('宝贝') ?></a>
                    </li>
                    <?php if(!$_COOKIE['SHOP_ID']){ ?>
                    <li class="<?php if (@request_string('ctl') == 'Shop_Index') {
                        echo 'active';
                    } ?>" onclick="searchShopWords()" id="shop">
                        <a href="javascript:void(0);" data-param='shop'><?= __('店铺') ?></a>
                    </li>
                    <?php }?>
                </ul>
                <div class="clearfix">
                    <form name="form_search" id="form_search" action="" class="">
                        <input type="hidden" id="search_ctl" name="ctl" value="<?php if (@request_string('ctl') != 'Shop_Index') {
                            echo 'Goods_Goods';
                        } else {
                            echo 'Shop_Index';
                        } ?>"> <input type="hidden" id="search_met" name="met" value="<?php if (@request_string('ctl') != 'Shop_Index') {
                            echo 'goodslist';
                        } else {
                            echo 'index';
                        } ?>"> <input type="hidden" name="typ" value="e"> <input name="keywords" id="site_keywords" type="text" value="<?= request_string('keywords') ?>" placeholder="<?php if (@request_string('ctl') == 'Shop_Index') {
                            echo $shop_keywords;
                        } else echo $keywords ?>"> <input type="submit" style="display: none;">
                    </form>
                    <a href="#" class="ser" id="site_search"><?= __('搜索') ?></a>
                    <!-- 购物车 -->
                    <div class="bbuyer_cart" id="J_settle_up">
                        <div id="J_cart_head">
                            <a href="<?= url('Buyer_Cart/cart') ?>" target="_blank" class="bbc_buyer_icon bbc_buyer_icon2"> <i class="ci_left iconfont icon-zaiqigoumai bbc_color rel_top2"></i> <span><?= __('我的购物车') ?></span> <i class="ci_right iconfont icon-iconjiantouyou"></i> <i class="ci-count bbc_bg" id="cart_num"></i> </a>
                        </div>
                        <div class="dorpdown-layer zIndex12" id="J_cart_body"><span class="loading"></span></div>
                    </div>
                </div>
                <?php if(!$_COOKIE['SHOP_ID']){?>
                <div class="nav clearfix searchs">
                    <?= implode($search_words) ?>
                </div>
                <?php }?>
            </div>
            <div style="clear:both;"></div>
        </div>


        <?php if(count($this->nav['items'])>9) {?>
                     <div class="bbuyer_cart" id="movers" style="z-index: 99;top: 16px;">
                  <div id="J_cart_head">
                    <a href="javascript:void(0);" class="bbc_buyer_icon bbc_buyer_icon2"> <span><?= __('更多...') ?></span></a>
                </div>
                <div class="dorpdown-layer zIndex12" id="J_cart_bodys" style="width:986px;height:42px">

               <nav class="tnav" shop_id="<?= Perm::$shopId ?>">
               <?php
            if ($this->nav) {
                foreach ($this->nav['items'] as $key => $nav) {
                    if ($key >9) {
                        $nav_target = $nav['nav_new_open'] == 1 ? 'target="_blank"':'';
                        $nav_href = strpos($nav['nav_url'], 'ctl=Supplier_Index') === false ? 'href="' . $nav['nav_url'] . '"':'onclick="check_supplier_shop()"';
                        echo '<a ' . $nav_href . ' ' . $nav_target . '>' . __($nav['nav_title']) . '</a>';
                    }
                }
            }
            ?>
               </nav>
                </div>
            </div>

        <?php } ?>

        <script>
            var $dialog = $("#J_cart_bodys");
            $("#movers").click(function(){

                if( $("#J_cart_bodys").css("display")=='none' ) {

                    $dialog.css("display","block");
                }else{
                    $dialog.css("display","none");
                }
            })
            // 谁有active就给谁默认词
            $(function () {
                if ($("#shop").is(".active")) {
                    $("#site_keywords").attr("placeholder", "<?= $shop_keywords;?>");
                } else {
                    $("#site_keywords").attr("placeholder", "<?= $keywords;?>");
                }
            });
            
            // 当点击宝贝时，填充商品关键词
            function searchWords() {
                $("#site_keywords").val("<?= $keywords;?>");
                $("#site_keywords").attr("placeholder", "<?= $keywords;?>");
            }
            
            // 当点击店铺时，填充店铺关键词
            function searchShopWords() {
                $("#site_keywords").attr("placeholder", "<?= $shop_keywords;?>");
            }
        
        </script>
        <div>
             <?php if(!$_COOKIE['SHOP_ID']){ ?>
            <div class="thead clearfix" style="margin-top: 20px;">
                <div class="classic clearfix">
                    <div class="class_title">
                        <span>&equiv;</span> <a href="<?= url('Goods_Cat/goodsCatList') ?>" class="ta1"><?= __('全部分类') ?></a>
                    </div>

                    <div class="tleft" id="show" <?php if (($this->ctl == "Index" && $this->met == "index") || ($this->ctl == "" && $this->met == "")){ ?> style="display:block;"<?php } else { ?> style="display: none;"<?php } ?>>
                        <ul>
                            <?php if ($this->cat) {
                                $i = 0;
                                foreach ($this->cat as $keyone => $catone) {
                                    if ($i < 14) {
                                        ?>
                                        <li onclick="tocha(this)">
                                            <h3>
                                                <?php if (!empty($catone['cat_nav'])) { ?>
                                                    <?php if ($catone['cat_nav']['goods_cat_nav_pic']) { ?>
                                                        <img width="16" height="16" style="margin-right: 6px;" src="<?= cdn_image_url($catone['cat_nav']['goods_cat_nav_pic'], 16, 16) ?>">
                                                    <?php } ?>
                                                    <a href="<?= url('Goods_Goods/goodslist', ['cat_id' => $catone['cat_nav']['goods_cat_id']]) ?>">
                                                        <?= __($catone['cat_nav']['goods_cat_nav_name']) ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <?php if ($catone['cat_pic']) { ?>
                                                        <img width="16" height="16" style="margin-right: 6px;" src="<?= cdn_image_url($catone['cat_pic'], 16, 16) ?>">
                                                    <?php } ?>
                                                    <a href="<?= url('Goods_Goods/goodslist', ['cat_id' => $catone['cat_id']]) ?>">
                                                        <?= __($catone['cat_name']) ?>
                                                    </a>
                                                <?php } ?>
                                                <span class="iconfont icon-iconjiantouyou"></span>
                                            </h3>
                                            <div class="hover_content clearfix">
                                                <div class="left">
                                                    <div class="channels">
                                                        <?php if (!empty($catone['brand'])) {
                                                            foreach ($catone['brand'] as $brand_key => $brand_value) {
                                                                if (7 >= $brand_key && $brand_value) {
                                                                    ?>
                                                                    <a href="<?= url('Goods_Goods/goodslist', ['brand_id' => $brand_value['brand_id']]) ?>">
                                                                        <?= __($brand_value['brand_name']) ?>
                                                                        <span class="iconfont icon-iconjiantouyou "></span> </a>
                                                                <?php }
                                                            }
                                                        } ?>
                                                    </div>
                                                    
                                                    <div class="rel_content">
                                                        <?php
                                                        if (!empty($catone['cat_nav'])) {
                                                            ?>
                                                            <?php
                                                            foreach ($catone['cat_nav']['goods_cat_nav_recommend_up_display'] as $key => $value) {
                                                                ?>
                                                                <dl class="clearfix">
                                                                    <dt>
                                                                        <a class="one-overflow" href="<?= url('Goods_Goods/goodslist', ['cat_id' => $value['cat_id']]) ?>">
                                                                            <?= __($value['cat_name']) ?>
                                                                            <span class="iconfont icon-iconjiantouyou rel_top1"></span> </a>
                                                                    </dt>
                                                                    <dd>
                                                                        <?php if (!empty($value['sub'])) {
                                                                            foreach ($value['sub'] as $sub_key => $sub_value) {
                                                                                ?>
                                                                                <a href="<?= url('Goods_Goods/goodslist', ['cat_id' => $sub_value['cat_id']]) ?>">
                                                                                    <?= __($sub_value['cat_name']) ?>
                                                                                </a>
                                                                            <?php }
                                                                        } ?>
                                                                    </dd>
                                                                </dl>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                
                                                <!-- 广告位-->
                                                <div class="right">
                                                    <!-- 品牌-->
                                                    <?php if (!empty($catone['brand'])) { ?>
                                                        <div class="clearfix mb10">
                                                            <a class="fr" href="<?= url('Goods_Brand/index') ?>">
                                                                <?= __('更多品牌') ?>
                                                                <span class="middle iconfont icon-btnrightarrow"></span> </a>
                                                        </div>
                                                    <?php } ?>
                                                    <ul class="d1ul clearfix mb10">
                                                        <?php if (!empty($catone['brand'])) {
                                                            foreach ($catone['brand'] as $brand_key => $brand_value) {
                                                                if (3 >= $brand_key && $brand_value) {
                                                                    ?>
                                                                    <li class="">
                                                                        <a href="<?= url('Goods_Goods/goodslist', ['brand_id' => $brand_value['brand_id']]) ?>">
                                                                            <img src="<?= cdn_image_url($brand_value['brand_pic'],93,35) ?>" data-src="<?= cdn_image_url($brand_value['brand_pic'],93,35) ?>" alt="<?= $brand_value['brand_name'] ?>">
                                                                            <span><b class="table wp100 hp100"><i class="table-cell wp100 align-middle"><?= __($brand_value['brand_name']) ?></i></b></span>
                                                                        </a>
                                                                    </li>
                                                                <?php }
                                                            }
                                                        } ?>
                                                    </ul>
                                                    
                                                    <ul class="index_ad_big">
                                                        <?php if (!empty($catone['adv'])) {
                                                            $adv_url = explode(',', $catone['cat_nav']['goods_cat_nav_adv_url']);
                                                            foreach ($catone['adv'] as $adv_key => $adv_value) {
                                                                ?>
                                                                <li>
                                                                    <?php if($adv_value != './shop_admin/static/common/images/image.png') {?>

                                                                        <a href="<?php if ($adv_url[$adv_key]) {
                                                                            echo $adv_url[$adv_key];
                                                                        } else {
                                                                            echo "javascript:;";
                                                                        } ?>"><img src="" data-src="<?= cdn_image_url($adv_value,190,190) ?>"></a>
                                                                    <?php }?>    
                                                                </li>
                                                            <?php }
                                                        } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    <?php }
                                    $i++;
                                }
                            } ?>
                        </ul>
                    </div>
                </div>
                <nav class="tnav" shop_id="<?= Perm::$shopId ?>">
                    <?php
                    if ($this->nav) {
                        foreach ($this->nav['items'] as $key => $nav) {
                            if ($key < 10) {
                                $nav_target = $nav['nav_new_open'] == 1 ? 'target="_blank"':'';
                                $nav_href = strpos($nav['nav_url'], 'ctl=Supplier_Index') === false ? 'href="' . $nav['nav_url'] . '"':'onclick="check_supplier_shop()"';
                                echo '<a ' . $nav_href . ' ' . $nav_target . '>' . __($nav['nav_title']) . '</a>';
                            }
                        }
                    }
                    ?>
                </nav>


                <p class="high_gou"></p>
            </div>
            <?php }?>
        </div>
    </div>
</div>
<div class="hr hr-her"></div>
<div class="J-global-toolbar"></div>
<script>
    function tocha(obj) {
        $("a", obj)[0].click();
    }
</script>
