var reset = true;

$(function () {
    
        var t = $("#newsclass").find(".active").find("a").attr("data-state");
        var n = $(".infor-head-nav").find(".active").attr("data-state");
        var i = new ncScrollLoad;
        i.loadInit({
            url: ApiUrl + "/index.php?ctl=Goods_Informationnewslist&met=index&typ=json",
            getparam: {status: t, number: n},
            tmplid: "newscentent-tmp",
            page: 12,
            containerobj: $(".infor-list-items"),
            iIntervalId: true,
            data: {WapSiteUrl: WapSiteUrl}
        });
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Goods_Informationnewslist&met=newsclasslist&typ=json",
            dataType: "json",
            success: function (e) {
                var html = '<li class="swiper-slide active"  style="width:auto;"><a  data-state="">推荐</a></li>';
                for (var i = 0; i < e.data.length; i++) {
                    html += '<li class="swiper-slide" style="width:auto;"><a data-state="' + e.data[i].id + '">' + e.data[i].newsclass_name + '</a></li>'
                }
                var paiHtml='<li class="active"  data-state=""><a href="javascript:;">默认排序</a></li>';
                    paiHtml+='<li data-state="1"><a href="javascript:;">阅读量排序</a></li>';

                    $("#newsclass").html(html);
                    $(".infor-head-nav").html(paiHtml);
                    var swiper = new Swiper('.information-swiper', {
                      slidesPerView:'auto',
                      spaceBetween: 30
                    });

            }
        });
    
        // $(".infor-list-items").on("click", "[nc_type='fav_del']", function () {
        //     var t = $(this).attr("data_id");
        //     if (dropFavoriteGoods(t)) {
        //         $("#favitem_" + t).remove();
        //         if (!$.trim($("#newscentent").html())) {
        //             location.href = WapSiteUrl + "/tmpl/information_news_list.html";
        //         }
        //     }
        // });
});
 $("#newsclass").on("click", "li", function () {
     $("#newsclass").find("li").removeClass("active");
     $(this).addClass("active");
     reset = true;
     window.scrollTo(0, 0);
     tt()
 });

 $(".infor-head-nav").on("click", "li", function () {
     $(".infor-head-nav").find("li").removeClass("active");
     $(this).addClass("active");
     reset = true;
     window.scrollTo(0, 0);
     tt()
 });
 
 function tt(){
     var t = $("#newsclass").find(".active").find("a").attr("data-state");
     var n = $(".infor-head-nav").find(".active").attr("data-state");
     var i = new ncScrollLoad;
     i.loadInit({
         url: ApiUrl + "/index.php?ctl=Goods_Informationnewslist&met=index&typ=json",
         getparam: {status: t, number: n},
         tmplid: "newscentent-tmp",
         page: 12,
         containerobj: $(".infor-list-items"),
         iIntervalId: true,
         data: {WapSiteUrl: WapSiteUrl}
     });
     // $(".infor-list-items").on("click", "[nc_type='fav_del']", function () {
     //     var t = $(this).attr("data_id");
     //     if (dropFavoriteGoods(t)) {
     //         $("#favitem_" + t).remove();
     //         if (!$.trim($("#newscentent").html())) {
     //             location.href = WapSiteUrl + "/tmpl/information_news_list.html";
     //         }
     //     }
     // });
 }



