<?php if (!defined('ROOT_PATH')){exit('No Permission');}
include $this->view->getTplPath() . '/' . 'supplier_site_nav.php';



$search_words = array_map(function($v) {
	return sprintf('<a href="%s?ctl=Supplier_Goods&met=goodslist&typ=e&keywords=%s" class="cheap">%s</a>', Yf_Registry::get('url'), urlencode($v), $v);
}, explode(',',  __(Web_ConfigModel::value('search_words'))));

$keywords = current($this->searchWord);

?>
<script type="text/javascript" src="<?=$this->view->js_com?>/jquery.blueberry.js"></script>
<script src="<?=$this->view->js_com?>/plugins/jquery.timeCountDown.js" ></script>
<link href="<?= $this->view->css ?>/select2.min.css" rel="stylesheet">
<script src="<?= $this->view->js ?>/select2.min.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/jquery.lazy.js"></script>
<script src="<?= $this->view->js ?>/intlTelInput.js"></script>
<link rel="stylesheet" href="<?= $this->view->css ?>/intlTelInput.css">
<div class="wrap">
	<div class="head_cont">
		<div style="clear:both;"></div>
		<div class="nav_left">
			<a href="index.php" class="logo"><img src="<?=@$this->web['web_logo']?>"/></a>
			<a href="#" class="download iconfont"></a>
		</div>
		<div class="nav_right clearfix" >
            <ul class="clearfix search-types dd">

                <?php if (@request_string('ctl') == 'Supplier_Goods'){ ?>

                    <li class="<?php if (@request_string('ctl') == 'Supplier_Goods') {
                        echo 'active';
                    } ?>" onclick="searchWords()">
                        <a href="javascript:void(0);" data-param='goods'><?= __('宝贝') ?></a>
                    </li>

                <?php     }elseif(@request_string('ctl') == 'Supplier_Index'){ ?>

                    <?php if(@request_string('met') == 'indexs'){?>
                        <li class="<?php if (@request_string('met') == 'indexs') {
                            echo '';
                        } ?>" onclick="searchWords()">
                            <a href="javascript:void(0);" data-param='goods'><?= __('宝贝') ?></a>
                        </li>
                    <?php }else{?>
                        <li class="<?php if (@request_string('ctl') == 'Supplier_Index') {
                            echo 'active';
                        } ?>" onclick="searchWords()">
                            <a href="javascript:void(0);" data-param='goods'><?= __('宝贝') ?></a>
                        </li>

                    <?php }?>

                <?php    }?>

                <?php if (@request_string('ctl') == 'Supplier_Index'){ ?>
                    <?php if(@request_string('met') == 'indexs'){?>
                        <li class="<?php if (@request_string('ctl') == 'Supplier_Index') {
                            echo 'active';
                        } ?>" onclick="searchShopWordst()" id="shop">
                            <a href="javascript:void(0);" data-param='shop'><?= __('店铺') ?></a>
                        </li>
                    <?php }else{?>
                        <li class="<?php if (@request_string('ctl') != 'Supplier_Index') {
                            echo 'active';
                        } ?>" onclick="searchShopWordst()" id="shop">
                            <a href="javascript:void(0);" data-param='shop'><?= __('店铺') ?></a>
                        </li>
                    <?php  }?>

                <?php }else{ ?>

                    <li class="<?php if (@request_string('ctl') == 'Supplier_Index') {
                        echo 'active';
                    } ?>" onclick="searchShopWordst()" id="shop">
                        <a href="javascript:void(0);" data-param='shop'><?= __('店铺') ?></a>
                    </li>
                <?php  }?>


            </ul>
			<div class="clearfix">
                <form name="form_search" id="form_search" action="" class="">
                    <input type="hidden" id="search_ctl" name="ctl" value="<?php if (@request_string('ctl') != 'Supplier_Index') {
                        echo 'Supplier_Goods';
                    } else {
                        echo 'Supplier_Goods';
                    } ?>"> <input type="hidden" id="search_met" name="met" value="<?php if (@request_string('ctl') != 'Supplier_Index') {
                        echo 'goodslist';
                    } else {
                        echo 'goodslist';
                    } ?>"> <input type="hidden" name="typ" value="e"> <input name="keywords" id="site_keywords" type="text" value="<?= request_string('keywords') ?>" placeholder="<?php if (@request_string('ctl') == 'Supplier_Index') {
                        echo "";
                    } else echo "" ?>"> <input type="submit" style="display: none;">
                </form>
				<a href="#" class="ser" id="site_search"><?=__('搜索')?></a>
				<!-- 购物车 -->
				<div class="bbuyer_cart">
					<div class="bbc_buyer_icon bbc_buyer_icon2">
						<i class="ci_left iconfont icon-zaiqigoumai bbc_color rel_top2"></i>
						<a href="<?= Yf_Registry::get('url') ?>?ctl=Buyer_Cart&met=cart" target="_blank"><?=__('我的购物车')?></a>
						<i class="ci_right iconfont icon-iconjiantouyou"></i>
						<i class="ci-count bbc_bg" id="cart_num">0</i>
					</div>
				</div>
			</div>
			<div class="nav clearfix searchs ">
				<?= implode($search_words); ?>
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>
    <script>
        $(".dd li").click(function()
        {
            $(".search-types li").removeClass("active");
            $(this).addClass("active");
            var type = $(this).find("a").attr('data-param');

            if(type=='shop')
            {
                $("#search_ctl").val('Supplier_Index');
                $("#search_met").val('indexs');
            }else{
                $("#search_ctl").val('Supplier_Goods');
                $("#search_met").val('goodslist');
            }
        })
    </script>
	<div>
		<div class="thead clearfix">
			<div class="classic clearfix">
				<div  class="class_title"><span>&equiv;</span><a href="#" class="ta1"><?=__('全部分类')?></a></div>
				<div class="tleft" id="show" <?php if(( $this->ctl=="Supplier_Index" && $this->met == "index") || ($this->ctl =="" && $this->met == "") ){?>style="display:block;"<?php }else{?> style="display: none;"<?php }?>>
					<ul>
						<?php if($this->cat){
							foreach ($this->cat as $keyone => $catone) {
								if($keyone<14)
								{
								?>
								<li>
									<h3><?php if(!empty($catone['cat_nav'])){ ?>
                                            <img width="16" height="16" style="margin-right: 6px;"src="<?=$catone['cat_nav']['goods_cat_nav_pic']?>">
                                            <a href="index.php?ctl=Supplier_Goods&met=goodslist&debug=1&cat_id=<?=$catone['cat_nav']['goods_cat_id']?>"><?= __($catone['cat_nav']['goods_cat_nav_name']); ?></a>
                                        <?php }else{?>
                                            <a href="index.php?ctl=Supplier_Goods&met=goodslist&debug=1&cat_id=<?=$catone['cat_id']?>"><?= __($catone['cat_name']); ?></a>
                                        <?php }?>
                                        <span class="iconfont icon-iconjiantouyou"></span>
                                    </h3>

									<div class="hover_content clearfix">
										<div class="left">
											<div class="channels">
												<?php if(!empty($catone['brand'])){
													foreach ($catone['brand'] as $brand_key => $brand_value) {
														if(7 >=$brand_key && $brand_value){
															?>
															<a href="index.php?ctl=Supplier_Goods&met=goodslist&debug=1&brand_id=<?=$brand_value['brand_id']?>"><?= __($brand_value['brand_name']); ?><span class="iconfont icon-iconjiantouyou "></span></a>
														<?php } } }?>

											</div>
											<div class="rel_content">
												<?php
												if(!empty($catone['cat_nav'])){
													?>

													<?php
													foreach ($catone['cat_nav']['goods_cat_nav_recommend_display'] as $key => $value) {
														?>
														<dl class="clearfix"><dt>
																<a href="index.php?ctl=Supplier_Goods&met=goodslist&debug=1&cat_id=<?=$value['cat_id']?>"><?= __($value['cat_name']); ?>&nbsp;&nbsp;<span class="iconfont icon-iconjiantouyou rel_top1"></span></a>
															</dt>

															<dd>
																<?php if(!empty($value['sub'])){
																	foreach ($value['sub'] as $sub_key => $sub_value) {

																		?>
																		<a href="index.php?ctl=Supplier_Goods&met=goodslist&debug=1&cat_id=<?=$sub_value['cat_id']?>"><?= __($sub_value['cat_name']); ?></a>
																	<?php } } ?>
															</dd></dl>
													<?php } ?>

												<?php } ?>
											</div>
										</div>

										<!-- 广告位-->
										<div class="right">
											<!-- 品牌-->
											<ul class="d1ul clearfix">
												<?php if(!empty($catone['brand'])){
													foreach ($catone['brand'] as $brand_key => $brand_value) {
														if(3 >=$brand_key && $brand_value){
															?>
															<li class="">
																<a href="index.php?ctl=Supplier_Goods&met=goodslist&debug=1&brand=<?=$brand_value['brand_id']?>"><img src="<?=$brand_value['brand_pic']?>" alt="<?=$brand_value['brand_name']?>">
																	<span><?= __($brand_value['brand_name']); ?></span>
                                                                </a>
															</li>

														<?php } } }?>
											</ul>
											<ul class="index_ad_big">
												<?php if(!empty($catone['adv'])){
													foreach ($catone['adv'] as $adv_key => $adv_value) {

														?>
														<li>
															<a href="#"><img src="<?=$adv_value?>"></a>
														</li>
													<?php }} ?>

											</ul>
										</div>
									</div>
								</li>
							<?php } }}?>
					</ul>
				</div>
			</div>
            <nav class="tnav" style="font-weight: 500;  line-height: 36px; font-size: 14px">
                <a href="?ctl=Supplier_Index" style="padding: 0px 18px;">批发市场</a>
                <a href="?ctl=Shop_Index&met=indexg&typ=e&keywords=">供应商店铺</a>
			<p class="high_gou"></p>
		</div>
	</div>
</div>
<div class="hr hr-her">
</div>
<div class="J-global-toolbar">
</div>
<script>
	// 最新
	var theme_page_color = "<?=$this->theme_page_color?>";//后台传class名，例：front-1 front-2 ...
	document.getElementsByTagName('body')[0].className=theme_page_color;
	// 原版
	// var theme_page_color = "<?=$this->theme_page_color?>"
	// document.documentElement.style.setProperty("--color", theme_page_color);
    // 当点击店铺时，填充店铺关键词
    function searchShopWords() {
        $("#site_keywords").attr("placeholder", "<?= $shop_keywords;?>");
    }
	

</script>