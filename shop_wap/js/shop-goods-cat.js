
var u = getCookie('id');
var k = getCookie('key');
var shop_id = getQueryString("shop_id");


var kefu_click = true;
function store_voucher () {
    $(".shop_footer_dh").removeClass("shop_footer_dh");
    $(".store_voucher").parent().parent().addClass("active");
     if (!$("#store_voucher_con").html()) {
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Voucher&met=vList&typ=json",
                data: {store_id: getQueryString("shop_id"), gettype: "free"},
                dataType: "json",
                async: false,
                success: function (t) {
                    if (t.status == 200) {
                        var e = template.render("store_voucher_con_tpl", t.data);
                        $("#store_voucher_con").html(e);
						// $(".nctouch-bottom-mask").removeClass("down").addClass("up");
						// $('.nctouch-bottom-mask-close,.nctouch-bottom-mask-bg').click(function(){
						// 	$(".nctouch-bottom-mask").addClass("down").removeClass("up");
						// })
						$.animationUp({
							valve: ".animation-up",            // 动作触发
							wrapper: "#store_voucher_con",                   // 动作块
							scroll: ' '    // 滚动块，为空不触发滚动
						});
                    }
                }
            });
        }
}

function store_kefu () {
    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Shop&met=getStoreInfo&typ=json",
        data: {k: k, u: u, shop_id: shop_id},
        dataType: "json",
        success: function (t) {
            var e = t.data;
            var tel = t.data.store_info.store_tel;
            $.getJSON(SiteUrl + "/index.php?ctl=Api_Wap&met=version_im&typ=json", function (r) {
                var st = r.data.im;
                if (st != 1) {
                    imUrl(e);
                } else if (tel) {
                    kefu_click = false;
                    setTimeout(function () {
                        $(".kefu").parent().attr("href", "tel:" + tel).show();
                    }, 500);
                } else {
                    imUrl(e);
                }
            });
        }
    });
}

function imUrl (e) {
    if (kefu_click == true) {
        if (!getCookie("key")) {
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
                return;
            }
        } else {
            var shop_name = e.store_info.store_name, shop_logo = e.store_info.store_avatar, seller_name = e.store_info.user_name;
            window.location.href = ImApiUrl + "?to_kefu=1&shop_name=" + shop_name + "&shop_logo=" + shop_logo + "&seller_name=" + seller_name;
        }
        // if (window.chatTo) {
        //     chatTo(e.store_info.user_name.toString());
        // } else {
        //     window.location.href = WapSiteUrl + "/tmpl/im-chatinterface.html?contact_type=C&contact_you=" + e.store_info.user_name + "&uname=" + getCookie("user_account");
        // }
     }
}



// $(document).on("click","#store_voucher_con", "[nc_type=\"getvoucher\"]", function () {

//         getFreeVoucher($(this).attr("data-tid"));
//         var b = $(this).parent().HTML();
//         $(this).parent().addClass("active");
// })

$("#store_voucher_con").on("click", "[nc_type=\"getvoucher\"]", function () {
        getFreeVoucher($(this).attr("data-tid"));
});
$(function ()
{
    var key=getCookie("key");if(!key){location.href="member/login.html"}
    var e;
    $("#header").on("click", ".header-inp", function ()
    {
        var mb = getQueryString("mb");
        location.href = WapSiteUrl + "/tmpl/search.html?mb=" + mb;
    });
    var index;
    index=index?"0":sessionStorage.getItem('index');
   
    var shop_id_wap = getCookie('SHOP_ID_WAP');
   
  
    // 右侧初始化
    $.getJSON(ApiUrl + "/index.php?ctl=Shop_GoodsCat&met=tree&typ=json&shop_id_wap="+shop_id_wap + "&shop_id=" + shop_id, {cat_parent_id: 0,u:u,k:k}, function (e)
    {
        var t = e.data;
        t.WapSiteUrl = WapSiteUrl;
        var r = template.render("category-two", t);
        $("#categroy-rgt").html(r);
        $(".pre-loading").hide();
        //new IScroll("#categroy-rgt", {mouseWheel: true, click: true})
    });

        //e.scrollToElement(document.querySelector(".categroy-list li:nth-child(" + ($(this).parent().index() + 1) + ")"), 1e3)
    

});

    