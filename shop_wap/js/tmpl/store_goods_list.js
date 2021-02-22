var shop_id = getQueryString("shop_id");
var keyword = decodeURIComponent(getQueryString("keyword"));
var order_key = getQueryString("key");
var order_val = getQueryString("order");
var price_from = getQueryString("price_from");
var price_to = getQueryString("price_to");
var stc_id = getQueryString("stc_id");
var prom_type = getQueryString("prom_type");
//var load_class_storegoodslist = new ncScrollLoad;
var isload_goods = false;
var pagesize = pagesize;
var curpage = 1;
var firstRow = 0;
var hasmore = true;
var footer = false;
var columns=2;

var myDate = new Date;
var searchTimes = myDate.getTime();
var ci = getQueryString("ci");
var specials_search = getQueryString("specials_search");
var label_id = getQueryString("label_id");
var brandsHtml="";
var brandHtml2="";



var area_id = getQueryString("area_id");
var own_shop = getQueryString("own_shop");
var other_shop = getQueryString("other_shop");
var gift = getQueryString("gift");
var actgoods = getQueryString("actgoods");
var virtual = getQueryString("virtual");
var plus = getQueryString("plus");
var priority = getQueryString("priority");
var distance = getQueryString("distance");
var brand_id = getQueryString("brand_id");
var brand_ids = getQueryString("brand_ids");
var goods_brand_alls = getQueryString("goods_brand_alls");
var goods_brands_alls = getQueryString("goods_brands_alls");





function back () {
    $(".header").addClass("hide")
}
$(function () {
    $("#show_style").click(function () {
        var e = $('[nc_type="product_content"]');
        if ($(e).hasClass("grid")) {
            $(this).find("span").removeClass("browse-grid").addClass("browse-list");
            $(e).removeClass("grid").addClass("list")
        } else {
            $(this).find("span").addClass("browse-grid").removeClass("browse-list");
            $(e).addClass("grid").removeClass("list")
        }
    });
    $("#sort_default").click(function () {
        $(this).addClass("current");
        $("#sort_prices").removeClass("current");
        $("#sort_salesnum").removeClass("current");
        $("#sort_price").addClass("hide")
        if ($("#sort_inner").hasClass("hide")) {
            $("#sort_inner").removeClass("hide")
        } else {
            $("#sort_inner").addClass("hide")
        }
    });
    $("#sort_prices").click(function () {
        $(this).addClass("current");

        $("#sort_default").removeClass("current");
        $("#sort_salesnum").removeClass("current");
        $("#sort_inner").addClass("hide")
        $("#sort_price").removeClass("hide")
    });
    //销量优先
    $("#sort_salesnum").click(function () {
        order_val = 2;
        order_key = 3;
        $(this).addClass("current");
        $("#sort_default").removeClass("current");
        $("#sort_prices").removeClass("current");
        $("#sort_price").addClass("hide")
        $("#sort_inner").addClass("hide").find("a").removeClass("cur");
        hasmore = true;
        $("#product_list").html('');
        curpage = 1;
        firstRow = 0;
        get_list()
    });

    $("#sort_inner").find("a").click(function () {
        $("#sort_inner").addClass("hide").find("a").removeClass("cur");
        var e = $(this).addClass("cur").text();
        $("#sort_default").addClass("current").html(e + "<i></i>");
        $("#sort_salesnum").removeClass("current")
    });

    


    //价格从高到底 onclick="get_list({'order_val':'2','order_key':'2'})"
    $("#pricedown_price").click(function (){
        order_val = 2;
        order_key = 2;
        hasmore = true;
        $("#product_list").html('');
        curpage = 1;
        firstRow = 0;
        get_list();
    });
    //价格从底到高 onclick="get_list({'order_val':'1','order_key':'2'})"
    $("#priceup_price").click(function (){
        order_val = 1;
        order_key = 2;
        hasmore = true;
        $("#product_list").html('');
        curpage = 1;
        firstRow = 0;
        get_list();
    });


    //综合排序   onclick="get_list({'order_val':'0','order_key':'0'})"
    $("#default").click(function (){
        order_val = 0;
        order_key = 0;
        hasmore = true;
        $("#product_list").html('');
        curpage = 1;
        firstRow = 0;
        get_list();
    });
    //价格从高到底 onclick="get_list({'order_val':'2','order_key':'2'})"
    $("#pricedown").click(function (){
        order_val = 2;
        order_key = 2;
        hasmore = true;
        $("#product_list").html('');
        curpage = 1;
        firstRow = 0;
        $("#sort_price").addClass("hide")
        $("#sort_prices").html("价格从高到底")
        get_list();
    });
    //价格从底到高 onclick="get_list({'order_val':'1','order_key':'2'})"
    $("#priceup").click(function (){
        order_val = 1;
        order_key = 2;
        hasmore = true;
        $("#sort_price").addClass("hide")
        $("#product_list").html('');
        $("#sort_prices").html("价格从底到高")
        curpage = 1;
        firstRow = 0;
        get_list();
    });
    //人气排序 onclick="get_list({'order_val':'2','order_key':'5'})"
    $("#collect").click(function (){
        order_val = 2;
        order_key = 5;
        hasmore = true;
        $("#product_list").html('');
        curpage = 1;
        firstRow = 0;
        get_list();
    });

    $("#product_list").on("click", '[nc_type="goods_more_link"]', function () {
        var e = $(this).attr("param_id");
        if (e <= 0) {
            $.sDialog({skin: "green", content: "参数错误", okBtn: false, cancelBtn: false});
            return false
        }
        var r = getCookie("key");
        if (!r) {
            $("#goods_more_" + e).show()
        }
        var o = $(this);
        if ($(o).hasClass("goods_more_loading")) {
            return
        }
        $(o).addClass("goods_more_loading");
        if ($("#goods_more_" + e).hasClass("goods_more_has")) {
            $("#goods_more_" + e).show();
            $(o).removeClass("goods_more_loading");
            return
        }
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Buyer_Favorites&met=getGoodsFI&typ=json",
            data: {k: r, u: getCookie('id'), fav_id: e},
            dataType: "json",
            success: function (r) {
                if (r.data.favorites_info) {
                    $("#goods_more_" + e + " [nc_type='goods_cancelfav']").show();
                    $("#goods_more_" + e + " [nc_type='goods_addfav']").hide()
                } else {
                    $("#goods_more_" + e + " [nc_type='goods_cancelfav']").hide();
                    $("#goods_more_" + e + " [nc_type='goods_addfav']").show()
                }
                $("#goods_more_" + e).addClass("goods_more_has");
                $("#goods_more_" + e).show();
                $(o).removeClass("goods_more_loading")
            }
        })
    }).on("click", '[nc_type="goods_more_con"]', function () {
        var e = $(this).attr("param_id");
        $("#goods_more_" + e).hide()
    }).on("click", '[nc_type="goods_addfav"]', function () {
        var e = $(this).attr("param_id");
        favoriteGoods(e);
        $(this).hide();
        $("#goods_more_" + e + " [nc_type='goods_cancelfav']").show()
    }).on("click", '[nc_type="goods_cancelfav"]', function () {
        var e = $(this).attr("param_id");
        dropFavoriteGoods(e);
        $(this).hide();
        $("#goods_more_" + e + " [nc_type='goods_addfav']").show()
    });
    $.animationLeft({valve: "#search_adv", wrapper: ".nctouch-full-mask"});
     $("#search_adv").click(function () {
        $(".header").removeClass("hide")
     });
    // $("#search_submit").click(function () {
    //     var e = false;
    //     if ($("#price_from").val() != "") {
    //         price_from = $("#price_from").val();
    //         e = true
    //     } else {
    //         price_from = ""
    //     }
    //     if ($("#price_to").val() != "") {
    //         price_to = $("#price_to").val();
    //         e = true
    //     } else {
    //         price_to = ""
    //     }
    //     if (e) {
    //         $("#search_adv").addClass("current");
    //         hasmore = true;
    //         $("#product_list").html('');
    //         curpage = 1;
    //         firstRow = 0;
    //         get_list();
    //         get_list()
    //     } else {
    //         $("#search_adv").removeClass("current")
    //     }
    //     $(".nctouch-full-mask").addClass("hide").removeClass("left")
    // });
    $('input[nctype="price"]').on("blur", function () {
        if ($(this).val() != "" && !/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test($(this).val())) {
            $(this).val("")
        }
    });
    $("#reset").click(function () {
        $('input[nctype="price"]').val("")
    });
    get_list();
	search_adv();
    $(window).scroll(function ()
    {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 1)
        {
            get_list()
			waterFall(columns);
        }
    });
});




function get_list()
{
    $(".loading").remove();
    if (!hasmore)
    {
        return false
    }
    hasmore = false;
    param = {};
    param.pagesize = pagesize;
    param.curpage = curpage;
    param.firstRow = firstRow;
    param.id = shop_id;
    if (keyword) {
        param.search = keyword
    }
    if (price_from) {
        param.price_from = price_from
    }
    if (price_to) {
        param.price_to = price_to
    }
    if (stc_id) {
        param.catid = stc_id
    }
    if (prom_type) {
        param.prom_type = prom_type
    }
 
    if (own_shop != "") {
        param.op3 = 'ziying'
    }
    if (other_shop != "") {
        param.op3 = 'ruzhu'
    }
    if (actgoods != '') {
        param.op2 = 'active'
    }
    if (virtual != '') {
        param.virtual = virtual
    }
    if(plus !=''){
        param.op2 = 'plus'
    }
    if(priority !=''){
        param.op1 = 'priority'
    }

    if(distance !=''){
        param.op4 = 'distance'
        param.lng = getCookie('lng');
        param.lat = getCookie('lat');
    }


    if(brand_ids != ''){
        param.op3 = 'brand_ids'
    }
    if(goods_brand_alls!=''){
        param.op1 = 'goods_brand_alls'
    }
    if(goods_brands_alls !=''){
        param.op1 = 'goods_brands_alls'
    }



    if (order_key) {
        if ( order_key == 2 ) {
            param.order = 'common_price';
        } else if ( order_key == 3 ) {
            param.order = 'common_salenum';
        } else if ( order_key == 5 ) {
            param.order = 'common_collect';
        }
    }
    if (order_val) {
        if ( order_val == 2 ) {
            param.sort = 'desc';
        } else if ( order_val == 1 ) {
            param.sort = 'asc';
        }
    }

    $.getJSON(ApiUrl + "/index.php?ctl=Shop&met=goodsList&typ=json" + window.location.search.replace("?", "&"), param, function (e)
    {
        if (!e)
        {
            e = [];
            e.datas = [];
            e.data.goods_list = []
        }
        $(".loading").remove();
        curpage++;
        console.info(e);
        var r = template.render("goods_list_tpl", e);

        var  goods_brand_html = template.render("goods_brand", e.data);
        $("#goods_brands").html(goods_brand_html);

        brandsHtml=template.render("goods_brand_all", e.data);
        $(".loading").remove();
        brandsHtml2=template.render("goods_brands_all", e.data);
        $(".loading").remove();



        $("#product_list").append(r);
		 waterFall(columns);
       if(e.data.page < e.data.total)
       {
           firstRow = e.data.records;
           hasmore = true;
       }
        else
       {
           hasmore = false;
       }
    })
}
function search_adv() {
    $.ajax({
        type: "GET",
        url:  ApiUrl + "/index.php?ctl=Index&met=getSearchAdv&typ=json",
        async:false,
        success:function(e){
        var r = e.data;
        $("#list-items-scroll").html(template.render("search_items", r));

        if (area_id) {
            $("#area_id").val(area_id)
        }
        if (price_from) {
            $("#price_from").val(price_from)
        }
        if (price_to) {
            $("#price_to").val(price_to)
        }
        if (own_shop) {
            $("#own_shop").addClass("current")
        }
        if (other_shop) {
            $("#other_shop").addClass("current")
        }
        if (actgoods) {
            $("#actgoods").addClass("current")
        }
        if (virtual) {
            $("#virtual").addClass("current")
        }
        if(plus){
            $("#plus").addClass("current")
        }
        if(priority){
            $("#priority").addClass("current")
        }
        if(distance){
            $("#distance").addClass("current")
        }
        if(brand_ids){
            $("#brand_ids").addClass("brand_ids")
        }
        if(goods_brand_alls){
            $("#goods_brand_alls").addClass("current")
        }
        if(goods_brands_alls){
            $("#goods_brands_alls").addClass("current")
        }
        if (ci) {
            var i = ci.split("_");
            for (var t in i) {
                $('a[name="ci"]').each(function () {
                    if ($(this).attr("value") == i[t]) {
                        $(this).addClass("current")
                    }
                })
            }
        }
        // 品牌查看全部
        $(".brands_more").click(function () {
            $('.search_items').hide();
            $("#list-items-scroll").append(template.render("brands_more"));
            $(".sort-item-li-zm").html(brandsHtml);
            $(".sort-item-li-tj").html(brandsHtml2);
            //排序切换
            $(".goods-brands-sort li").click(function(){
                var index=$(this).index();
                $(".goods-brands-sort li,.sorts-items .sort-item-li").removeClass("active");
                $(this).addClass("active");
                $(".sorts-items .sort-item-li").eq(index).addClass("active");
            })

        });
        $(document).on('click','.sort-href a',function(){
          $(this).addClass('active').siblings().removeClass('active');
        }); 
        $(document).on('click','.search_submit',function () {
            var e = "?keyword=" + keyword, r = "";
            if (typeof($("#area_id").val()) !== 'undefined' && $("#area_id").val() !== '') {
                e += "&transport_id=" + $("#area_id").val();
            }

            if ($("#price_from").val() != "") {
                e += "&price_from=" + $("#price_from").val()
            }
            if ($("#price_to").val() != "") {
                e += "&price_to=" + $("#price_to").val()
            }
            if ($("#own_shop")[0].className == "current") {
                e += "&own_shop=1"
            }
            if ($("#other_shop")[0].className == "current") {
                e += "&other_shop=1"
            }
            if ($("#actgoods")[0].className == "current") {
                e += "&actgoods=1"
            }
            if ($("#virtual")[0].className == "current") {
                e += "&virtual=1"
            }
            if($("#priority")[0].className == "current"){
                e += "&priority=1"
            }
            if($("#distance")[0].className == "current"){
                e += "&distance=1"
            }
            var brand_ids_str = '';
            $.each($(".brand_ids"),function(){
                if ($(this).hasClass("current")){
                    if (brand_ids_str =='') {
                       brand_ids_str += $(this).attr('value');
                    }else{
                       brand_ids_str += ',' + $(this).attr('value');
                    }
                 }
            })
            $.each($(".brand_name_id"),function(){
                if ($(this).prop("checked")){
                    if (brand_ids_str =='') {
                       brand_ids_str += $(this).val();
                    }else{
                       brand_ids_str += ',' + $(this).val();
                    }
                 }
            })
            if (brand_ids_str != '') {
                e += '&brand_id='+ brand_ids_str;
            }
            if (typeof(cat_id) !== 'undefined' && cat_id !== '') {
                e += "&cat_id=" + cat_id
            }
            $('a[name="ci"]').each(function () {
                if ($(this)[0].className == "current") {
                    r += $(this).attr("value") + "_"
                }
            });
            if (r != "") {
                e += "&ci=" + r
            }

            if (specials_search == 'specials') {
                if (label_id) {
                    window.location.href = WapSiteUrl + "/specials/lists.html" + e + "&label_id=" + label_id
                } else {
                    window.location.href = WapSiteUrl + "/specials/lists.html" + e + "&specials_search=specials";
                }
            } else {
                if (label_id) {
                    window.location.href = WapSiteUrl + "/specials/lists.html" + e + "&label_id=" + label_id
                } else {
                    if ($(this).hasClass("p_list")) {
                        window.location.href = WapSiteUrl + "/tmpl/store.html" + e + "&time="+ searchTimes +"&shop_id=" + shop_id + "#allgoods"
                    } else {
                        window.location.href = WapSiteUrl + "/tmpl/store.html" + e + "&shop_id=" + shop_id + "#allgoods"
                    }
                }
            }
           
            
        });


        $(document).on('click','a[nctype="items"',function(){
            var e = new Date;
            if (e.getTime() - searchTimes > 300) {
                $(this).toggleClass("current");
                searchTimes = e.getTime()
            }
        });


            $('input[nctype="price"]').on("blur", function () {
                if ($(this).val() != "" && !/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test($(this).val())) {
                    $(this).val("")
                }
            });
            $(document).on('click','.reset',function(){
                $('a[nctype="items"]').removeClass("current");
                $('input[nctype="price"]').val("");
                $('.sort-item-li-zm .brand_name_id').prop('checked',false);
                $("#area_id").val("");
            })
        }
    })
}
