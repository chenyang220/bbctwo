$(function () {
    var goods_id = getQueryString("goods_id");
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Goods_Goods&met=getGoodsDetailFormat&typ=json",
        data: {gid: goods_id},
        type: "get",
        success: function (data) {
            var html = '';
            if(data.data.goods_format_top)
            {
                html += data.data.goods_format_top;
            }
            if(data.data.brand_name)
            {
                html += '<p>品牌：'+ data.data.brand_name +'</p>';
            }
            if(data.data.common_property_row)
            {
                for(var i in data.data.common_property_row)
                {
                    if(data.data.common_property_row[i]){
                        html += '<span>'+ i +'：'+ data.data.common_property_row[i] +'</span>';
                    }
                }
            }
            html += data.data.common_detail;
            if(data.data.goods_format_bottom)
            {
                html += data.data.goods_format_bottom;
            }
            $(".fixed-tab-pannel").html(html);
        }
    });
    $("#goodsDetail").click(function () {
        window.location.href = WapSiteUrl + "/tmpl/product_detail.html?goods_id=" + goods_id
    });
    $("#goodsBody").click(function () {
        window.location.href = WapSiteUrl + "/tmpl/product_info.html?goods_id=" + goods_id
    });
    $("#goodsEvaluation").click(function () {
        window.location.href = WapSiteUrl + "/tmpl/product_eval_list.html?goods_id=" + goods_id
    })
    $('body').on('click', '#goodsRecommendation', function () {
        window.location.href = WapSiteUrl + '/tmpl/product_recommendation.html?goods_id=' + goods_id;
    });
    $(window).scroll(function(){
        var scrollTop = $(this).scrollTop();
    　　if (scrollTop<=0){
            window.location.href = WapSiteUrl + "/tmpl/product_detail.html?goods_id=" + goods_id;
        }
    });

});
