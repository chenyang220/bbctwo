$(function (){
      $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getDistributionShopIndex&typ=json",
            data: {k: getCookie('key'),u: getCookie('id')},
            dataType: "json",
            success: function (r) {
                  console.log(r);
                  if(r.data){
                        if(r.data.recommend){      
                              var e = template.render("distributed-recommend-goods", r);
                              $(".distribution-recommend-list").html(e);
                        }
                        if(r.data.hot){
                              var t = template.render("distributed-hot-goods", r);
                              $(".distribution-hot-list").html(t);
                        }
                        
                  } 
            }
      });
});