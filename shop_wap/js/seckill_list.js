var pagesize = pagesize;
var curpage = 1;
var firstRow = 0;
var hasmore = true;
var footer = false;
var seckill_title = getQueryString("seckill_title")?getQueryString("seckill_title"):'精品秒杀';
var uptime_order = getQueryString('uptime_order');
var price_order = getQueryString('price_order');
var myDate = new Date;
var searchTimes = myDate.getTime();
if (!getCookie('sub_site_id')) {
    addCookie('sub_site_id', 0, 0);
}
var sub_site_id = getCookie('sub_site_id');
$("#seckill_title").html(seckill_title);
get_list();
$(function () {
    var handler = function () {
        event.preventDefault();
        event.stopPropagation();
    };
    $(document).on("click", "#ldg_lockmask", function () {
        $(this).remove();
        $(document.body).css("overflow", "auto");
        document.body.removeEventListener('touchmove', handler, false);
        document.body.removeEventListener('wheel', handler, false);
    });

    $('#ldg_lockmask').bind("touchmove", function (e) {
        e.preventDefault();
    });
    $("#header").on("click", ".header-inp", function () {
        location.href = WapSiteUrl + "/tmpl/search.html?keyword=" + keyword
    });

    $("#show_style").click(function () {
        if ($("#product_list").hasClass("grid")) {
            $(this).find("span").addClass("browse-grid").removeClass("browse-list");
            $("#product_list").removeClass("grid").addClass("list")
        }
        else {
            $(this).find("span").removeClass("browse-grid").addClass("browse-list");
            $("#product_list").addClass("grid").removeClass("list")
        }
    });
    $("#sort_default").click(function () {
        if ($("#sort_inner").hasClass("hide")) {
            $("#sort_inner").removeClass("hide")
        }
        else {
            $("#sort_inner").addClass("hide")
        }
    });
    $("#nav_ul").find("a").click(function () {
        $(this).addClass("current").parent().siblings().find("a").removeClass("current");
        if (!$("#sort_inner").hasClass("hide") && $(this).parent().index() > 0) {
            $("#sort_inner").addClass("hide")
        }
    });
    $("#sort_inner").find("a").click(function () {
        $("#sort_inner").addClass("hide").find("a").removeClass("cur");
        var e = $(this).addClass("cur").text();
        $("#sort_default").html(e + "<i></i>")
    });
    $("#product_list").on("click", ".goods-store a", function () {
        var e = $(this);
        var r = $(this).attr("data-id");
        var i = $(this).text();
        $.getJSON(ApiUrl + "/index.php?act=store&op=store_credit", {shop_id: r}, function (t) {
            var a = "<dl>" + '<dt><a href="store.html?shop_id=' + r + '">' + i + '<span class="arrow-r"></span></a></dt>' + '<dd class="' + t.datas.store_credit.store_desccredit.percent_class + '">描述相符：<em>' + t.datas.store_credit.store_desccredit.credit + "</em><i></i></dd>" + '<dd class="' + t.datas.store_credit.store_servicecredit.percent_class + '">服务态度：<em>' + t.datas.store_credit.store_servicecredit.credit + "</em><i></i></dd>" + '<dd class="' + t.datas.store_credit.store_deliverycredit.percent_class + '">发货速度：<em>' + t.datas.store_credit.store_deliverycredit.credit + "</em><i></i></dd>" + "</dl>";
            e.next().html(a).show()
        })
    }).on("click", ".sotre-creidt-layout", function () {
        $(this).hide()
    });
    get_list();
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 1) {
            get_list();
        }
    });
});

function get_list() {
    // $(".loading").remove();
    if (!hasmore) {
        return false
    }
    hasmore = false;
    param = {};
    param.pagesize = pagesize;
    param.curpage = curpage;
    param.firstRow = firstRow;
    param.uptime_order = uptime_order;
    param.price_order = price_order;
    param.seckill_goods_id = getQueryString("seckill_goods_id");
    param.cat_id = getQueryString("cat_id");
//定位到上次浏览的商品
    var goods_pos = getCookie('goods_pos');
    param.pos = goods_pos
    $.getJSON(ApiUrl + "/index.php?ctl=Goods_Goods&met=getSeckillGoodsList&typ=json&ua=wap&sub_site_id=" + sub_site_id, param, function (e) {
        if (e.status == 200){
            if (!e) {
                e = [];
                e.datas = [];
                e.data.goods_list = []
            }

            $(".loading").remove();
            curpage++;
            e['pagesize'] = pagesize;

            var r = template.render("home_body", e);

            $("#product_list .goods-secrch-list").append(r);
            //hasmore = e.hasmore
            if (e.data.page < e.data.total) {
                firstRow = e.data.records;
                hasmore = true;
            } else {
                hasmore = false;
                if (e.data.totalsize >= 5) {
                    $(".loading").html("已加载全部了！");
                } else {
                    $(".loading").hide();
                }
            }
        }else{
            $("#product_list .goods-secrch-list").html(e.msg);
        }

    });
}


function init_get_list(e, r) {
    order = e;
    key = r;
    if(e == 'uptime') {
        uptime_order = r;
    }
    if(e == 'price') {
        price_order = r;
    }
    curpage = 1;
    firstRow = 0;
    hasmore = true;
    $("#product_list .goods-secrch-list").html("");
    $("#footer").removeClass("posa");
    get_list();
}

$('#list-items-scroll').on('click', '#area_info', function () {
    $.areaSelected({
        success: function (a) {
            $("#area_info").val(a.area_info).attr({
                "data-areaid1": a.area_id_1,
                "data-areaid2": a.area_id_2,
                "data-areaid3": a.area_id_3,
                "data-areaid": a.area_id,
                "data-areaid2": a.area_id_2 == 0 ? a.area_id_1 : a.area_id_2
            })
        }
    });
});

$(function () {
    $(window).scroll(function (e) {
        addCookie('goodsListPosition', document.documentElement.scrollTop);
    })

    if (getQueryString('jump') == 1) {
        var goodsListPosition = getCookie('goodsListPosition');

        var s = setInterval(function () {
            $(window).scrollTop(goodsListPosition);
            if (document.documentElement.scrollTop == goodsListPosition) {
                clearInterval(s);
            }
        }, 1);
    }
})

