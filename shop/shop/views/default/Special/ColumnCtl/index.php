<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
<div>
		<div class="swiper-container gpc-banner-swiper">
			<ul class="swiper-wrapper">
                <?php foreach($info['special_column_image'] as $k=>$v){ ?>
				<li class="swiper-slide">
                    <a href="<?= $v['img_url'] ?>">
					    <img src="<?= $v['img_path'] ?>" alt="banner" class="wp100">
                    </a>
				</li>
                <?php } ?>
			</ul>
		</div>
        
		<div class="gpc-special-goods-box">
            <div class="wrap">
                <div class="mb20">
                    <input type="hidden" name="column_self_is_open" value="<?= $column_self_is_open ?>">
                    <!-- style -->
                    <?php if($column_self_is_open == 1){ ?>
                        <input type="hidden" name="back_image" value="<?= $info['special_back_img'] ?>">
                        <?php foreach($set_image as $key=>$val){ ?>
                                <ul class="<?php if ($val['column_set_type'] == 1) { ?>gpc-goods-style1<?php }else{ ?>gpc-goods-style2 clearfix<?php } ?>">
                                    <?php if($val['column_set_image']){ ?>
                                        <?php foreach ($val['column_set_image'] as $k => $v) { ?>
                                            <li>
                                                <a href="<?= $v['set_url'] ?>" class="block">
                                                    <img src="<?= $v['set_path'] ?>" alt="" class="wp100">
                                                </a>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                        <?php } ?>
                    <?php } ?>

        			<ul class="gpc-special-goods-lists">
                        <?php foreach ($info['goods_common'] as $key => $val) { ?>
                            <li>
                                <div class="bgf clearfix">
                                    <em class="img-box"><img class="cter" src="<?= $val['goods_image'] ?>" alt=""></em>
                                    <div class="gpc-special-goods-text">
                                        <h4><?= $val['goods_name'] ?></h4>
                                        <p>
                                            <?php if ($val['discount_price']) {?>
                                                <span>商城价：</span>
                                                <strong><?= $val['discount_price'] ?></strong>
                                                <em>元  起</em>
                                                <span>（原售价：￥<?= $val['goods_price'] ?>）</span>
                                            <?php } else { ?>
                                                <span>商城价：</span>
                                                <strong><?= $val['goods_price'] ?></strong>
                                                <em>元  起</em>
                                            <?php } ?>
                                        </p>
                                        <a class="btn-gpc-special-goods" href="<?= Yf_Registry::get('url') ?>?ctl=Goods_Goods&met=goods&type=goods&cid=<?= ($val['goods_id']) ?>">立即抢购</a>
                                        <div class="iblock gpc-special-goods-code-box">
                                            <img src="<?= Yf_Registry::get('base_url') ?>/shop/api/qrcode.php?data=<?php echo urlencode(Yf_Registry::get('shop_wap_url') . '/tmpl/product_detail.html?cid=' . $val['goods_id'] ); ?>" alt="" class="gpc-special-goods-code">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
        			</ul>
                </div>
            </div>
		</div>
</div>
<script>
   // var value=red;
   // document.div.style.background =value+"!imporant";
    // var class_title =document.querySelector('.class_title');
    // root.setAttribute('style', '--background: #e74c3c');
	$(function(){
	    var column_self_is_open = $("input[name='column_self_is_open']").val();
	    var back_image = $("input[name='back_image']").val();
	    if(column_self_is_open == 1){
	        $(".gpc-special-goods-box").css("background-image", "url(" + back_image + ")");
        }
		var swiper = new Swiper('.gpc-banner-swiper', {
				autoplay:3000,
			});
	})
</script>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>