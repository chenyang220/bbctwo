var k = getCookie("key");
var u = getCookie("id");
var type_id = getQueryString("type");
$(function () {
    getPointsOrder();
});

function getPointsOrder(status) {
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Buyer_Points&met=points&op=getPointsOrder&typ=json",
        type: "get",
        data: {k: k, u: u, state: status},
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                var html = template.render('exchange_list_template', result.data);
                $("#exchange_list").html(html);
            }      
        }
    });
}

function express_status(status) {
$(".status_" + status).siblings().removeClass("selected");
    $(".status_" + status).addClass("selected");
    getPointsOrder(status);
}

function test (order_id) {
    $.sDialog({
        content: "您是否确认已收到货品?", 
        okFn: function () {
             $.post(ApiUrl + '/index.php?ctl=Buyer_Points&met=confirmOrder&typ=json', {k: k, u: u,order_id:order_id}, function (data) {
                if ( data.status == 200 ) {
                    window.location.href = WapSiteUrl + "/tmpl/exchange_list.php"
                     return false;
                } else {
                    Zepto.sDialog({
                        skin: "red",
                        content: "<?=__('确认收货失败')?>",
                        okBtn: false,
                        cancelBtn: false
                    });
                    return false;
                }
            })
        },
        cancelBtn:function(){
            
        }
    })
}



function del (order_id) {
    $.sDialog({
        content: "您是否确认删除此条兑换记录?", 
        okFn: function () {
             $.post(ApiUrl + '/index.php?ctl=Buyer_Points&met=confirmOrder&typ=json', {k: k, u: u,del_order_id:order_id}, function (data) {
                if ( data.status == 200 ) {
                    window.location.href = WapSiteUrl + "/tmpl/exchange_list.php"
                     return false;
                } else {
                    Zepto.sDialog({
                        skin: "red",
                        content: "<?=__('删除失败')?>",
                        okBtn: false,
                        cancelBtn: false
                    });
                    return false;
                }
            })
        },
        cancelBtn:function(){
            
        }
    })
}

function express (points_order_id) {
   var order_id = $(".express_"+ points_order_id).attr("data-order_id");
   var shiping_code = $(".express_"+ points_order_id).attr("data-shiping_code");
   var shiping_express = $(".express_"+ points_order_id).attr("data-shiping_express");
   var express_name = $(".express_"+ points_order_id).attr("data-express_name");
   window.location.href='./member/order_delivery.html?order_id='+order_id+'&shipping_code='+shiping_code+'&express_id='+shiping_express+'&express_name='+express_name;
}
