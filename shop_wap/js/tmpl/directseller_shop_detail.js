$(function (){
      var distribution_shop_id = getQueryString("sid");
      $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getDistributionShopList&typ=json",
            data: {distribution_shop_id:distribution_shop_id},
            dataType: "json",
            success: function (r) {
                  if(r.data){
                        var e = template.render("distributed-recommend-goods", r);
                        $(".recommend_list").html(e);

                        var t = template.render("distributed-hot-goods", r);
                        $(".hot_list").html(t);
                  } 
            }
      });
});