<?php 
include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1" />
    <title><?= __('小店名称'); ?></title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_member.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_common.css">
    <link rel="stylesheet" type="text/css" href="../../css/nctouch_cart.css">
	
	<style type="text/css">
		.nctouch-order-item-head{font-size:0.55rem;height:2.9rem;}
		.shop_head{font-weight:bold;font-size:0.6rem;}
		.rh{text-align:right;line-height:1.0rem;}
	</style>
</head>

<body>
    <header id="header" class="fixed">
        <div class="header-wrap">
            <!-- <div class="header-l"><a href="directseller.html"><i class="back"></i></a></div> -->
             <div class="header-title">
                <h1><?= __('名称设置'); ?></h1>
            </div>
            <!-- <div class="header-r"> <a id="header-nav" href="javascript:void(0);"><i class="more"></i><sup></sup></a></div> -->
        </div>
        
		<div class="nctouch-nav-layout">
            <div class="nctouch-nav-menu"> <span class="arrow"></span>
                <ul>
                    <?php if($_COOKIE['SHOP_ID_WAP']){ ?>
                        <li><a href="../store.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../store_search.html?shop_id=<?=$_COOKIE['SHOP_ID_WAP']?>"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }else{ ?>
                        <li><a href="../../index.html"><i class="home"></i><?= __('首页'); ?></a></li>
                        <li><a href="../search.html"><i class="search"></i><?= __('搜索'); ?></a></li>
                    <?php }?>
                    <li><a href="../cart_list.html"><i class="cart"></i><?= __('购物车'); ?></a><sup></sup></li>
                    <li><a href="javascript:void(0);"><i class="message"></i><?= __('消息'); ?><sup></sup></a></li>
                </ul>
            </div>
        </div>
    
	</header>
    
	<div class="nctouch-main-layout fixed-Width">
		<div class="nctouch-inp-con">
			<form action="" method="">
				<ul class="form-box">
					<li class="form-item">
						<h4><?= __('店铺名称'); ?></h4>
						<div class="input-box">
							<input type="text" placeholder="" class="inp" name="user_directseller_shop" id="user_directseller_shop" maxlength="20" oninput="writeClear($(this));">
							<span class="input-del"></span> 
						</div>
					</li>
			 
				</ul>
				<div class="error-tips"></div>
				<div class="form-btn"><a href="javascript:void(0);" class="btn" id="loginbtn"><?= __('提交'); ?></a></div>
			</form>
		</div>
	</div>
	
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/template.js"></script>
    
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/simple-plugin.js"></script>
    <script type="text/javascript" src="../../js/zepto.waypoints.js"></script>
    <script type="text/javascript" src="../../js/tmpl/directseller_shop.js"></script>
</body>

</html>
<?php 
include __DIR__.'/../../includes/footer.php';
?>