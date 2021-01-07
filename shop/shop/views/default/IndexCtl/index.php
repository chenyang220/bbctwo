<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
if (!isset($_COOKIE['sub_site_id'])) {
    $_COOKIE['sub_site_id'] = 0;
}
?>
<link href="<?= $this->view->css ?>/login.css" rel="stylesheet">
<link href="<?= $this->view->css ?>/new_file.css" rel="stylesheet">
<link href="<?=$this->view->css?>/tips.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.toastr.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js?>/home.js"></script>


<!--<link rel="stylesheet" type="text/css" href="<?= $this->view->css_com ?>/swiper.min.css" />-->
<!--<script src="<?= $this->view->js_com ?>/swiper.min.js"></script>-->

<style>
    body {
        min-width: 1200px;
        background: #eee;
    }
    .plus-status {
        display: inline-block;
        width: 48px;
        height: 20px;
        line-height: 18px;
        background: #E94458;
        border: 1px solid #FFE5E8;
        font-size: 12px;
        font-weight: normal;
        color: #FFFFFF;
        text-align: center;
        box-sizing: border-box;
    }
    .wrap, .wrapper {
        width: 1202px;
        margin: 0 auto;
        font-size: 14px;
        position: relative
    }
</style>
<?php
$prefix_site = isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0 ? $_COOKIE['sub_site_id']:'';
?>

<div id="redpacket_content" style="display:none">
    <img src="<?= $this->view->img?>/icon-closes.png" id="redpacket-close">
    <a href="<?=Yf_Registry::get('url')?>?ctl=RedPacket&met=redPacket"><img class="redpacket-img" width="50%" src="<?= $this->view->img?>/redpacket.png"></a>
</div>

<script>
    $("#redpacket-close").click(function(){
        $("#redpacket_content").hide();
    });
</script>
<div style="height:500px;" class="slideBox">
    <div class="hd">
        <ul>
            <?php
            for ($i = 1; $i <= 5; $i++) {
                $live_image_url = $prefix_site ? Web_ConfigModel::value($prefix_site . 'index_slider_image' . $i):Web_ConfigModel::value('index_slider_image' . $i, Web_ConfigModel::value('index_slider' . $i . '_image'));
                if ($live_image_url) {
                    ?>
                    <li></li>
                <?php }
            } ?>
        </ul>
    </div>
    <div class="banner  bd">
        <ul class="banimg">
            <?php
            for ($i = 1; $i <= 5; $i++) {
                $live_image_url = $prefix_site ? Web_ConfigModel::value($prefix_site . 'index_slider_image' . $i):Web_ConfigModel::value('index_slider_image' . $i, Web_ConfigModel::value('index_slider' . $i . '_image'));
                if ($live_image_url) {
                    ?>
                    <li style="height: 499px;">
                        <a href="<?php echo Web_ConfigModel::value($prefix_site . 'index_live_link' . $i); ?>" target="_blank">
                            <img src="<?= $live_image_url ?>" alt="banner">
                        </a>
                    </li>
                <?php }
            } ?>
        
        </ul>
        <script type="text/javascript">
            jQuery(".slideBox").slide({mainCell: ".bd ul", autoPlay: true, delayTime:3000,interTime:3000});
        </script>
        <div class="wrap t_cont clearfix">
            <ul class="tcenter fz0">
                <li>
                    <?php if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                        $liandong_img_url1 = Web_ConfigModel::value($_COOKIE['sub_site_id'] . 'index_liandong_image1');
                    } else {
                        $liandong_img_url1 = Web_ConfigModel::value('index_liandong_image1');
                    } ?>
                    <?php if ($liandong_img_url1) { ?>
                        <a href="<?php if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                            echo Web_ConfigModel::value($_COOKIE['sub_site_id'] . 'index_liandong_url1');
                        } else {
                            echo Web_ConfigModel::value('index_liandong_url1');
                        } ?>"><img src="<?php if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                                echo Web_ConfigModel::value($_COOKIE['sub_site_id'] . 'index_liandong_image1');
                            } else {
                                echo Web_ConfigModel::value('index_liandong_image1', Web_ConfigModel::value('index_liandong1_image'));
                            } ?>"/></a>
                    <?php } ?>
                </li>
                <li>
                    <?php if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                        $liandong_img_url2 = Web_ConfigModel::value($_COOKIE['sub_site_id'] . 'index_liandong_image2');
                    } else {
                        $liandong_img_url2 = Web_ConfigModel::value('index_liandong_image2');
                    } ?>
                    <?php if ($liandong_img_url2) { ?>
                        <a href="<?php if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                            echo Web_ConfigModel::value($_COOKIE['sub_site_id'] . 'index_liandong_url2');
                        } else {
                            echo Web_ConfigModel::value('index_liandong_url2');
                        } ?>"><img src="<?php if (isset($_COOKIE['sub_site_id']) && $_COOKIE['sub_site_id'] > 0) {
                                echo Web_ConfigModel::value($_COOKIE['sub_site_id'] . 'index_liandong_image2');
                            } else {
                                echo Web_ConfigModel::value('index_liandong_image2', Web_ConfigModel::value('index_liandong2_image'));
                            } ?>"/></a>
                    <?php } ?>
                </li>
            </ul>
            <div class="tright" id="login_tright">
            </div>
        </div>
    </div>
</div>
<div class="wrap" style="width:1202px;">

    <?=$forum_html;?>
    
    <div class="wrap floor fn-clear">
        <?php if (!empty($adv_list['items'])) {
            foreach ($adv_list['items'] as $key => $value) {
                ?>
                <?= $value['page_html'] ?>
            <?php }
        } ?>
    </div>

    <!--猜你喜欢-->
    <div class="activity-a guess-like">
        <h3>
            <span></span>
            <em><?= __('猜你喜欢') ?></em>
            <span></span>
        </h3>
        <ul class="clearfix" id="guess_id"></ul>
    </div>
</div>
</div>
<div class="J_f J_lift lift" id="lift" style="left: 42.5px; top: 134px;">
    <ul class="lift_list  aad">
        <li class="J_lift_item_top lift_item lift_item_top">
            <a href="javascript:;" class="lift_btn">
                <span class="lift_btn_txt">
                    <?= __('顶部') ?>
                    <i class="lift_btn_arrow"></i>
                </span>
            </a>
        </li>
    </ul>
</div>
<script src="<?= $this->view->js_com ?>/plugins/jquery.timeCountDown.js"></script>
<script>
    function ReceiveRedPack(redpacket_t_id) {
        if (!redpacket_t_id) {
            return;
        }

        Public.ajaxPost(SITE_URL + "?ctl=RedPacket&typ=json&met=receiveRedPacket", {red_packet_t_id: redpacket_t_id}, function (data) {
            if (data.status == 200) {
                Public.tips.success(data.msg);
            } else {
                Public.tips.warning(data.msg);
            }
        });

    }
    $(function () {
        var _TimeCountDown = $(".fnTimeCountDown");
        _TimeCountDown.fnTimeCountDown();
    });
    $(function () {

//              a标签隐藏
        if ($("#ul-num").children("li").length < 6) {
            $(".js-a-none").css("display", "none");
        }
        
        //遍历导航楼层111
        var atrf = [];
        var len = $(".floor .m").length;
        for (var mm = 0; mm < len; mm++) {
            var str = $(".floor .m").eq(mm).find("em").text();
            atrf.push(str);
        }
        var lis = "";
        $(atrf).each(function (i, n) {
            lis += "<li class=\"J_lift_item lift_item lift_item_first\"><a class=\"lift_btn\"><span class=\"lift_btn_txt\">" + n + "</span></a></li>";
        });
        $(".lift_list").prepend(lis);
        
        $(window).scroll(function () {
            //滚动轴
            var CTop = document.documentElement.scrollTop || document.body.scrollTop;
            var floorone = $(".floor .m").eq(0).offset().top;
            //当滚动轴到达楼层一时，左菜单栏显示
            if (CTop >= floorone) {
                $("#lift").show(500);
            }else if(CTop<floorone-200){
                $("#lift").hide(500);
            }
        });
        
        var b;
        $(".lift_list .J_lift_item").click(function () {
            b = $(this).index();
            $(".J_lift_item").removeClass("reds");
            $(this).addClass("reds");
            //离顶部距离
            var offsettop = $(".floor .m").eq(b).offset().top;
            //滚动轴距离
            var scrolltop = document.body.scrollTop || document.documentElement.scrollTop;
            //scrollTop() 方法返回或设置匹配元素的滚动条的垂直位置。
            $("html,body").stop().animate({
                scrollTop: offsettop
            }, 1000);
        });
        //返回顶部
        $(".lift_item_top").click(function () {
            $("html,body").animate({
                scrollTop: "0px"
            }, 800);
        });
        //滚动楼层对应切换左侧楼层导航
        var le = $(".floor .m").length;
        var arr = [];
        for (var s = 0; s < le; s++) {
            var nums = $(".floor .m").eq(s).offset().top;
            arr.push(nums);
        }
       

        $(window).scroll(function () {
            var scrTop = $(window).scrollTop();
            for (var w = 0; w < arr.length; w++) {
                var cc = arr[w + 1] || 1111111111;
                if (scrTop > arr[w] && scrTop < cc) {
                    if (arr[w + 1] < 0) {
                        w = w + 1;
                    }
                    $(".J_lift_item").removeClass("reds");
                    $(".J_lift_item").eq(w + 1).addClass("reds");
                }else if(scrTop<arr[0]){
                    $(".J_lift_item").removeClass("reds");
                    $(".J_lift_item").eq(0).addClass("reds");
                }
            }
            
            
        });
        
    });
</script>
<script>
    //删除限时折扣和团购模块商品滑玩箭头跳转事件
    function clockcss(){
        $(".swiper-button-next").removeClass("swiper-button-disabled");
        $(".swiper-button-prev").removeClass("swiper-button-disabled");
    }
    setInterval(clockcss,1000);

	 // window.onload=function(){
	 	var swiper1 = new Swiper('.swiper-container1', {
			pagination: {
				el: '.swiper-pagination1',
				dynamicBullets: true,
			},
			  observer:true,//修改swiper自己或子元素时，自动初始化swiper
	        observeParents:true,//修改swiper的父元素时，自动初始化swiper
		});

	 	//限时折扣版块1
		var swiper2 = new Swiper('.time-limit-a .swiper-container', {
			slidesPerView: 5,
			spaceBetween: 0,
			slidesPerGroup: 5,
			loopFillGroupWithBlank: true,
		
			prevButton:'.time-limit-a .swiper-button-prev',
			nextButton:'.time-limit-a .swiper-button-next',

			  observer:true,//修改swiper自己或子元素时，自动初始化swiper
	        observeParents:true,//修改swiper的父元素时，自动初始化swiper
		});

		//团购风暴版块1
		var swiper3 = new Swiper('.group-purchase-a .swiper-container', {
			slidesPerView: 5,
			spaceBetween: 0,
			slidesPerGroup: 5,
			loopFillGroupWithBlank: true,
			
			prevButton:'.group-purchase-a .swiper-button-prev',
			nextButton:'.group-purchase-a .swiper-button-next',
			 observer:true,//修改swiper自己或子元素时，自动初始化swiper
	        observeParents:true,//修改swiper的父元素时，自动初始化swiper
		});

		//领券中心版块1
		var swiper4 = new Swiper('.coupon-a .swiper-container', {
			slidesPerView: 3,
			spaceBetween: 10,
			slidesPerGroup: 3,
			loopFillGroupWithBlank: true,

			prevButton:'.coupon-a .swiper-button-prev',
			nextButton:'.coupon-a .swiper-button-next',
			  observer:true,//修改swiper自己或子元素时，自动初始化swiper
	        observeParents:true,//修改swiper的父元素时，自动初始化swiper
		});

		//平台红包版块1
		var swiper5 = new Swiper('.platform-a .swiper-container', {
			slidesPerView: 3,
			spaceBetween: 10,
			slidesPerGroup: 3,
			loopFillGroupWithBlank: true,
//			navigation: {
//				nextEl: '.platform-a .swiper-button-next',
//				prevEl: '.platform-a .swiper-button-prev',
//			},
			prevButton:'.platform-a .swiper-button-prev',
			nextButton:'.platform-a .swiper-button-next',
			 observer:true,//修改swiper自己或子元素时，自动初始化swiper
	        observeParents:true,//修改swiper的父元素时，自动初始化swiper
		});

		//团购风暴版块2
        var swiper6 = new Swiper('.swiper-container-groupbuy', {
            slidesPerView: 1,
            slidesPerColumn:3,
            spaceBetween: 10,
            pagination: '.swiper-pagination-groupbuy',
            paginationClickable: true,
        });

        //限时折扣版块2
        var swiper8 = new Swiper('.swiper-container-discount', {
            slidesPerView: 1,
            slidesPerColumn:3,
            spaceBetween: 10,
            pagination: '.swiper-pagination-discount',
            paginationClickable: true,
        });

        //领券中心版块2
        var swiper9 = new Swiper('.swiper-container-voucher', {
            slidesPerView: 1,
            slidesPerColumn:3,
            spaceBetween: 10,
            pagination: '.swiper-pagination-voucher',
            paginationClickable: true,
        });

        //平台红包版块2
        var swiper10 = new Swiper('.swiper-container-redpacket', {
            slidesPerView: 1,
            slidesPerColumn:3,
            spaceBetween: 10,
            pagination: '.swiper-pagination-redpacket',
            paginationClickable: true,
        });


	 // }
	
	$(function() {
		var arr = ["#fff0f0", "#fdf5f2", "#f1f6ef", "#f9f9f9", "#f2fbff"];
		$.each($(".group-purchase-a .swiper-slide"), function(i, obj) {
			if(i >= 5) {
				var thisindex = $(this).index();
				i = thisindex - Math.floor(thisindex / 5) * 5;
			}
			$(this).css("backgroundColor", arr[i])
		})
	})
</script>
<script> 
	/**
	 *懒加载处理 
	 *@nsy 2019-10-14
	 **/
	var lock_id;
	$(window).on('scroll', function (){
		if (lock_id) {
			clearTimeout(lock_id);
		}
		lock_id = setTimeout(function (){
			lazyLoading();
		}, 300);
	});

	//懒加载主方法 
	function lazyLoading(){
		$('.wrap img').each(function (){
			if (_check($(this)) && !loadFlag($(this))){
				loadingImage($(this));
			}
		})
	}

	//检测
	function _check($pic){
		var scrollTop = $(window).scrollTop();
		var windowHeight = $(window).height();
		var offsetTop = $pic.offset().top; 
		if ((offsetTop < (scrollTop + windowHeight)) && (offsetTop > scrollTop)){
			return true;
		}else{
			return false;
		}
	}

	//加载标识
	function loadFlag($pic){
		if($pic.attr('data-src') === $pic.attr('src')){
			return true;//已加载
		}else{
			return false;//未加载
		}
	}
	
	//加载图片
	function loadingImage($pic){
		$pic.attr('src', $pic.attr('data-src'));
		//图片加载完成，轮播图展示
		$("ul.slides").css("height", "100%");
	} 
</script>
<script language="JavaScript">
    $(function() {
        setTimeout("getFavouriteGoods()",5000);
    });

    /**
     * 异步获取猜你喜欢商品数据
     * @nsy 2019-10-15
     */
    function getFavouriteGoods(){
        Public.ajaxPost(SITE_URL + "?ctl=Index&met=guessFavourite&typ=json", '',function(data) {
            var html = '';
            var url = "<?=Yf_Registry::get('url')?>";
            if(data.status==200 && data.data){
                for (var i in data.data){
                    html+='<li><a href="'+url+'?ctl=Goods_Goods&met=goods&type=goods&gid='+data.data[i].goods_id+'" target="_blank"><div class="img-box-limit"><em><b><img src="'+data.data[i].goods_image+'"/></b></em></div><span class="one-overflow">'+data.data[i].goods_name+'</span><p><b class="rmb"><?=Web_ConfigModel::value('monetary_unit')?></b>'+data.data[i].goods_price+'</p></a></li>';
                }
            }
            $("#guess_id").html(html);
        } );
    }
</script>
<script>
    $(function () {
        setTimeout("getWapQrcodeImg()",1000);
    });
    /**
     *获取wap端扫码二维码进入的图片
     *@nsy 2019-10-16
     */
    function getWapQrcodeImg(){
        Public.ajaxPost(SITE_URL + "?ctl=Common&met=qrCodeImage&typ=json", {},
            function (data) {
                if (data.status == 200) {
                    $("#left_wap_qr_img").attr('src',data.data.img);
                    var mobile_app = data.data.mobile_app;
                    var mobile_wap = data.data.mobile_wap;
                    var mobile_wx_code = data.data.mobile_wx_code;
                    var a = 0;
                    if(mobile_app){
                        $(".mobile_app").show();
                        a++;
                    }
                    if(mobile_wap){
                        $(".mobile_wap").show();
                        $(".mobile_app").css("border-right","1px solid #e1e1e1");
                        a++;
                    }
                    if(mobile_wx_code){
                        $(".mobile_wx").show();
                        $(".mobile_wap").css("border-right","1px solid #e1e1e1");
                        a++;
                    }
                    console.log(a);
                    if(a==1){
                        $(".qrcode_erweima").css("width","140px");
                    }else if(a==2){
                        $(".qrcode_erweima").css("width","280px");
                    }
                    $("#mobile_top_app_qr_img").attr('src',mobile_app);
                    $("#mobile_top_wap_qr_img").attr('src',mobile_wap);
                    $("#mobile_top_wx_qr_img").attr('src',mobile_wx_code);
                }
            }
        );
    }
</script>
</div>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>

