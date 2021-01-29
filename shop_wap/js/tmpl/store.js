var t = getCookie("key");
var e = getQueryString("shop_id");
var columns=2;
if($('.style-change').hasClass('list')){
	columns=1;
}else{
	columns=2;
}
function tidyStoreNewGoodsData(t) {
    if (t.items.length <= 0) {
        return t;
    }
    var e = $("#newgoods").find("[addtimetext=\"" + t.items[0].common_add_time + "\"]");
    var o = "";
    $.each(t.items, function (s, r) {
        if (o != r.goods_addtime_text && e.html() == null) {
            t.items[s].goods_addtime_text_show = r.goods_addtime_text;
            o = r.common_add_time;
        }
    });
    return t;
}
// 收藏店铺变成已收藏
function collectShop(shop_id) {
    var k = getCookie("key");
    var u = getCookie("id");
    if (k && u) {
        $.getJSON(ApiUrl + '/index.php?ctl=Shop&met=addCollectShop&typ=json', {
            shop_id: shop_id,
            k: k,
            u: u
        }, function (data) {
            if (data.status == 200) {
                $.sDialog({skin: "green", content: "收藏成功！", okBtn: false, cancelBtn: false});
                $(".pd-collect").html("已收藏");
            } else {
                $.sDialog({skin: "red", content: "该店铺已收藏！", okBtn: false, cancelBtn: false});
            }
        });
    } else {
        $.sDialog({skin: "red", content: "请先登录！", okBtn: false, cancelBtn: false});
    }
}
$("#goods_search").on("click", ".header-inp", function ()
    {
        location.href = WapSiteUrl + "/tmpl/search.html?mb=shop";
    });
function dajinquan () {
    location.href = WapSiteUrl + "/tmpl/voucher_list.html?shop=" + shop_id;
}
var tt = '';
$(function () {
    if (!e) {
        window.location.href = WapSiteUrl + "/index.html";
    }

    var rr = {'data':e}
    var shop_footer = template.render("shop_footer", rr);
    $("#shop_footer_div").html(shop_footer);
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Shop&met=getStoreInfo&typ=json",
        data: {k: t, u: getCookie("id"), shop_id: e},
        dataType: "json",
        success: function (t) {
            var e = t.data;
            var s = e.store_info.store_name + " - 店铺首页";
            document.title = s;
            var r = template.render("store_banner_tpl", e);
			tt = e;
            var indexTem = template.render("store_index_tpl",e);
            $("#storeindex_con").html(indexTem);
			waterFall(columns);
			 window.onscroll=function(){
				if ($(window).scrollTop() + $(window).height() == $(document).height()) {
					  waterFall(columns);
				 }
				 
			}
			// 页面尺寸改变时实时触发
			window.onresize = function() {
			    //重新定义瀑布流
			    waterFall(columns);
			};
			//初始化
			window.onload = function(){
			    //实现瀑布流
			    waterFall(columns);
			}
            if (getCookie("is_app_guest")) {
                $("#shareit_store").attr("href", "/share_toall.html?shop_id=" + e.store_info.shop_id + "&title=" + encodeURIComponent(e.store_info.store_name) + "&img=" + e.store_info.store_avatar + "&url=" + WapSiteUrl + "/tmpl/store.html?shop_id=" + e.store_info.shop_id);
            }
            $("#store_banner").html(r);
            var label_name_msg = template.render("label_name_tmpl", e);
            $("#label_name").html(label_name_msg);
			$(".shop_image").attr("style","background-image:url("+ e.store_info.shop_logo+")");
            $(".shop_name").html(e.store_info.store_name);
            if (e.store_info.is_favorate) {
                $("#store_notcollect").hide();
                $("#store_collected").show();
            } else {
                $("#store_notcollect").show();
                $("#store_collected").hide();
            }
            if (e.voucher_list.length > 0) {
                var voucher_list = template.render("voucher_list_tpl", e);
                $("#voucher_list_div").show();
                $("#voucher_list_div").html(voucher_list);
            } else {
                $("#voucher_list_div").hide();
            }
            if (e.store_info.mb_title_img) {
                $(".store-top-bg .img img").attr("src", e.store_info.mb_title_img);
                 $(".store-head-bg").css("background","url("+ e.store_info.mb_title_img +") no-repeat center;");
                /*$(".store-top-bg .img").css("background-image", "url(" + e.store_info.mb_title_img + ")");*/
                $(".store-top-mask").hide();
            } else {
                $(".store-top-mask").show();
                var a = [];
                a[0] = WapSiteUrl + "/images/store_h_bg_01.jpg";
                a[1] = WapSiteUrl + "/images/store_h_bg_02.jpg";
                a[2] = WapSiteUrl + "/images/store_h_bg_03.jpg";
                a[3] = WapSiteUrl + "/images/store_h_bg_04.jpg";
                a[4] = WapSiteUrl + "/images/store_h_bg_05.jpg";
                var i = Math.round(Math.random() * 4);
                $(".store-top-bg .img").css("background-image", "url(" + a[i] + ")");
            }
            if (e.store_info.mb_sliders.length > 0) {
                var r = template.render("store_sliders_tpl", e);
                $("#store_sliders").html(r);
                var swiper = new Swiper('.nctouch-store-wapper', {
                    autoplay:"3000"
                });
                o();
            } else {
                $("#store_sliders").hide();
            }
            $("#store_kefu").click(function () {
                // 商品参数:
                //判断不是手机号时使用IM
                if ($(this).attr("href").indexOf("tel:") == -1) {
                    if (!getCookie("user_account") && getCookie("user_account") == undefined) {
                        if (!getCookie("key")) {
                            $.sDialog({
                                skin: "red",
                                content: "您还没有登录",
                                okBtn: true,
                                okBtnText: "立即登录",
                                okFn: function () {
                                    window.location.href = WapSiteUrl + "/tmpl/member/login.html";
                                },
                                cancelBtn: true,
                                cancelBtnText: "取消",
                                cancelFn: function () {
                                }
                            });
                            return false;
                        }
                    } else {
                        /*
                        * 需要带过去的参数
                        * 商品的第一张图片 result.data.good_one_image
                        * 商品名称：result.data.goods_info.common_name
                        * 商品价格：当前价格/活动价格 result.data.goods_info.common_price/result.data.goods_info.common_market_price
                        * 商品链接：callback_url
                        * 店铺名称：result.data.store_info.store_name
                        *
                        * */
                        var callback_url = window.location.href,
                        shop_name = e.store_info.store_name, 
                        shop_logo = e.store_info.store_avatar, 
                        seller_name = e.store_info.user_name;
                        window.location.href = ImApiUrl + "?to_kefu=1&shop_name=" + shop_name + "&callback_url=" + callback_url + "&shop_logo=" + shop_logo + "&seller_name=" + seller_name+ "&app_id=" + app_id+ "&app_token=" + app_token;
                    }
                }
            });
            var r = template.render("goods_recommend_tpl", e);
            $(".goods_recommend").html(r);
           
            /* 店铺首页刷新闪烁 */
            if (e.rec_goods_list_count == 0) {
                $(".shop-owner-recommend").remove();
            }
            /* 店铺首页刷新闪烁 */
			 
        }
    });
    $("#goods_rank_tab").find("a").click(function () {
        $("#goods_rank_tab").find("li").removeClass("selected");
        $(this).parent().addClass("selected").siblings().removeClass("selected");
        var t = $(this).attr("data-type");
        var o = t == "collect" ? "common_collect":"common_salenum";
        var s = 3;
        $("[nc_type='goodsranklist']").hide();
        $("#goodsrank_" + t).show();
        if ($("#goodsrank_" + t).html()) {
            return;
        }
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Shop&met=goodsList&sort=desc&typ=json",
            data: {id: e, order: o, num: s, sort: "desc"},
            dataType: "json",
            success: function (e) {
                if (e.status == 200) {
                    var o = template.render("goodsrank_" + t + "_tpl", e.data);
                    $("#goodsrank_" + t).html(o);
                    var swiper = new Swiper('.store-goods-rank-swiper', {
                        slidesPerView:"auto"
                    });
                    var swiper = new Swiper('.store-sale-rank-swiper', {
                        slidesPerView:"auto"
                    });
                    
                }
            }
        });
    });
    $("#goods_rank_tab").find("a[data-type='collect']").trigger("click");
    // $("#nav_tab").waypoint(function () {
    //     $("#nav_tab_con").toggleClass("fixed");
    // }, {offset: "80"});
    $("#store_voucher").click(function () {
        if (!$("#store_voucher_con").html()) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Voucher&met=vList&typ=json",
                data: {store_id: e, gettype: "free"},
                dataType: "json",
                async: false,
                success: function (t) {
                    if (t.status == 200) {
                        var e = template.render("store_voucher_con_tpl", t.data);
                        $("#store_voucher_con").html(e);
                    }
                }
            });
        }
        $.animationUp({valve: ""});
    });

    
    $("#store_voucher_con").on("click", "[nc_type=\"getvoucher\"]", function () {
        getFreeVoucher($(this).attr("data-tid"));
    });
    $("#store_notcollect").live("click", function () {
        var t = favoriteStore(e);
        if (t) {
            $("#store_notcollect").hide();
            $("#store_collected").show();
            var o;
            var s = (o = parseInt($("#store_favornum_hide").val())) > 0 ? o + 1:1;
            $("#store_favornum").html(s);
            $("#store_favornum_hide").val(s);
        }
    });
    $("#store_collected").live("click", function () {
        var t = dropFavoriteStore(e);
        if (t) {
            $("#store_collected").hide();
            $("#store_notcollect").show();
            var o;
            var s = (o = parseInt($("#store_favornum_hide").val())) > 1 ? o - 1:0;
            $("#store_favornum").html(s);
            $("#store_favornum_hide").val(s);
        }
    });
    //初始化
    var nav_hash = window.location.hash;
    if (nav_hash) {
        $(nav_hash).click();
    }
    // $(window).scroll(function(){
    //     var hig=$(".store-head-bg").height();
    //     if($(this).scrollTop()>=hig){
    //         $(".nctouch-store-nav,.goods-search-list-nav").addClass("fixed");
    //     }else{
    //         $(".nctouch-store-nav,.goods-search-list-nav").removeClass("fixed");
            
    //     }
    // })

    $(".shop_footer_dh").click(function () {
        $(".shop_footer_dh").each(function (e,a) {
                $(a).removeClass("active");
        });
        $(this).addClass("active");
    });

    $("#store_voucher").click(function () {
        $(".shop_footer_dh").each(function (e,a) {
                $(a).removeClass("active");
        });
        $(this).addClass("active");
    });
});

function toColumns(obj){
	obj=obj?obj:'#storeindex1';
	var dom='';
	switch (obj) {
	case "#storeindex1":
		dom='#storeindex_con';
	    break;
	case "#allgoods":
		dom='#allgoods_con';
	    break;
	case "#newgoods":
		dom="#newgoods_con";
	    break;
	case "#storeactivity":
		dom="#storeactivity_con";
	    break;
	}
	if($(dom).find('.style-change').hasClass('list')){
		$(dom).find('.style-change').removeClass('list').addClass('grid');
		columns=2;
	}else{
		$(dom).find('.style-change').removeClass('grid').addClass('list');
		columns=1;
	}
	waterFall(columns);
}

$("#menuChange").click(function(){
	var nav_hash = window.location.hash;
	toColumns(nav_hash);
})
function nav_clicks(nav_type) {
    window.location.reload();
}
function nav_click(nav_type) {
    $("#nav_tab").find("li").removeClass("selected");
    $("#" + nav_type).parent().addClass("selected").siblings().removeClass("selected");
    $("#storeindex_con,#allgoods_con,#newgoods_con,#storeactivity_con").hide();
    window.scrollTo(0, 0);
    window.location.hash = nav_type;
    switch (nav_type) {
    case "storeindex1":
		columns=2;
		$("#storeindex_con").show();
		var indexTem = template.render("store_index_tpl",tt);
        $("#storeindex_con").html(indexTem);
		$("#storeindex_con").find('.style-change').removeClass('list').addClass('grid');
		waterFall(columns);
        break;
    case "allgoods":
		columns=2;
		$("#allgoods_con").show();
        if (!$("#allgoods_con").html()) {
            $("#allgoods_con").load("store_goods_list.html", function () {
                $(".goods-search-list-nav").addClass("posr");
                $(".goods-search-list-nav").css("top", "0");
                $("#sort_inner").css("position", "static");
            });
        }
        $("#allgoods_con").find('.style-change').removeClass('list').addClass('grid');
		waterFall(columns);
        break;
    case "newgoods":
		columns=2;
        if (!$("#newgoods_con .addtime").html()) {
            s();
        }
        $("#newgoods_con").show();
		$("#newgoods_con").find('.style-change').removeClass('list').addClass('grid');
		waterFall(columns);
        break;
    case "storeactivity":
		columns=1;
		 $("#storeactivity_con").show();
        if (!$("#storeactivity_con").html()) {
            r();
        }
		$("#storeactivity_con").find('.style-change').removeClass('grid').addClass('list');
		waterFall(columns);
       
        break;
    }
	
}

function s() {
    var t = {};
    t.id = e;
    var o = new ncScrollLoad;
  //   o.loadInit({
  //       url: ApiUrl + "/index.php?ctl=Shop&met=goodsList&order=common_sell_time&sort=desc&typ=json",
  //       getparam: t,
  //       tmplid: "newgoods_tpl",
  //       containerobj: $("#newgoods_con"),
  //       iIntervalId: true,
  //       resulthandle: "tidyStoreNewGoodsData",
		// callback:waterFall(columns)
  //   });
	$.ajax({
	    type: "post",
	    url:ApiUrl + "/index.php?ctl=Shop&met=goodsList&order=common_sell_time&sort=desc&typ=json",
	    data: t,
	    dataType: "json",
	    success: function (res) {
	        var n = template.render("newgoods_tpl", res.data);
			$("#newgoods_con").html(n);
			waterFall(columns);
	    }
	});
}

function r() {
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Shop&met=getShopPromotion&typ=json",
        data: {shop_id: e},
        dataType: "json",
        success: function (t) {
            t.data.shop_id = e;
            var o = template.render("storeactivity_tpl", t.data);
            if ($.trim(o)) {
                $("#storeactivity_con").html(o);
				waterFall(columns);
            }
        }
    });
}
function o() {
    $("#store_sliders").each(function () {
        if ($(this).find(".item").length < 2) {
            return;
        }
        Swipe(this, {
            startSlide: 2,
            speed: 400,
            auto: 3e3,
            continuous: true,
            disableScroll: false,
            stopPropagation: false,
            callback: function (t, e) {
            },
            transitionEnd: function (t, e) {
            }
        });
    });
}
