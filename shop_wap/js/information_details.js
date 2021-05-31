$(function () {
    
    var news_id = getQueryString('id');
  
    $.getJSON(ApiUrl + "/index.php?ctl=Goods_Informationnewslist&met=newsdetails&typ=json", {
      id: news_id
    }, function (t) {
    	console.log(t);
    	 var newsdetails=t.data;
    	 console.log(newsdetails);
         if (newsdetails.number == null || newsdetails.number == "") {
            newsdetails.number = 0;
         }
    	 var html='<h2 class="title-info">'+newsdetails.title+'</h2>';
    	     html+='<p class="subheadInfo">'+newsdetails.subtitle+'</p>';
             html +='<div class="authorBox"><P>'+newsdetails.authorname+'</P><P><span>'+newsdetails.number+'</span>条阅读</P>';
             if (newsdetails['author_type'] == 2) {
                html += '<a class="entranceBn" href="store.html?shop_id='+newsdetails.shop_id+'"><i></i>进入店铺</a>';
             }
             html += '</div><div class="bannerBox"><p>'+newsdetails.content+'</p><div class="BmOther"><p>'+newsdetails.create_time+'</p>'
             if(newsdetails['complaint'] == 2){
                 html += '</div></div>';
             }else{
                 html += '<button class="complainBn" order_id = "' + newsdetails.id + '" > 投诉 </button></div></div>';
             }
             
         $("#news").html(html);
         $(".complainBn").click(g);
    });

    function g() {
        var id = $(this).attr("order_id");
        console.log(id);
        $.sDialog({
            content: "确定投诉吗？", okFn: function () {
                tg(id);
            }
        })
    }

    function tg(e) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Goods_Informationnewslist&met=Complaint&typ=json",
            data: {id: e},
            dataType: "json",
            success: function (e) {
                console.log(e);
                if (e.status == 200) {
                    window.location.reload()
                } else {
                    $.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false})
                }
            }
        })
    }   
  
});


// var id = getQueryString("id"),

// //如果没有goods_id，则根据cid获取goods_id
// if(id)
// {
//     $.ajax({
//         url: ApiUrl + "/index.php?ctl=Goods_Informationnewslist&met=newsdetails&typ=json",
//         type: "POST",
//         data: {id: id},
//         dataType: "json",
//         success: function ( t )
//         {
//         	 var newsclassHtml = template.render("news-tmp", {newsdetails:t.data});
//             $("#news").html(newsclassHtml);
//         }
//     });
// }

// function show_tip() {
//     var flyer = $('.goods-pic > img').clone().css({'z-index':'999','height':'3rem','width':'3rem'});
//     flyer.fly({
//         start: {
//             left: $('.goods-pic > img').offset().left,
//             top: $('.goods-pic > img').offset().top-$(window).scrollTop()
//         },
//         end: {
//             left: $("#cart_count1").offset().left+40,
//             top: $("#cart_count1").offset().top-$(window).scrollTop(),
//             width: 0,
//             height: 0
//         },
//         onEnd: function(){
//             flyer.remove();
//         }
//     });
// }

// $(function (){

//     $('body').on('click', '.complainBn', function(){
//         window.location.href = WapSiteUrl+'/tmpl/integral_product_info.html?id=' + id;
//     });

//     //购买数量，减
//     $("#product_detail_spec_html").on("click", ".minus", function (){
//         var buynum = $(".buy-num").val();
//         if(buynum >1){
//             $(".buy-num").val(parseInt(buynum-1));
//         }
//     });
//     //购买数量加
//     $("#product_detail_spec_html").on("click", ".add", function (){
//         var buynum = parseInt($(".buy-num").val());
//         if(buynum < points_goods_storage){
//             $(".buy-num").val(parseInt(buynum+1));
//         }
//     });

//     //加入购物车
//     $("body").on("click", "#add-cart", function () {

//         var key = getCookie('key');//登录标记

//         if (!key) {
//             window.location.href = WapSiteUrl + '/tmpl/member/login.html';
//             return false;
//         }
//     })
//     //点击立即兑换
//     $('body').on('click', '#buy-now', function(){
//         var quantity = parseInt($(".buy-num").val()) || 0;

//         if (quantity < 1) {
//             return $.sDialog({
//                 skin:"red",
//                 content:'参数错误！',
//                 okBtn:false,
//                 cancelBtn:false
//             });
//         }

//         if ( points_goods_storage && quantity > points_goods_storage ) {
//             return $.sDialog({
//                 skin:"red",
//                 content:'库存不足！',
//                 okBtn:false,
//                 cancelBtn:false
//             });
//         }
        
//         var param = {
//             k: getCookie('key'),
//             u: getCookie('id'),
//             points_goods_id: id,
//             quantity: quantity
//         };

//         if(!getCookie('key'))
//         {
//             $.sDialog({
//                 skin: "red",
//                 content: '需要登录！',
//                 okBtn: true,
//                 cancelBtn: true,
//                 okFn: function (){
//                     callback = window.location.href;
//                     login_url   = UCenterApiUrl + '?ctl=Login&met=index&typ=e';

//                     callback = ApiUrl + '?ctl=Login&met=check&typ=e&redirect=' + encodeURIComponent(callback);

//                     login_url = login_url + '&from=wap&callback=' + encodeURIComponent(callback);

//                     window.location.href = login_url;
//                 },
//                 cancelFn:function (){

//                 }
//             });
//             return;
//         }

//         $.ajax({
//             url: ApiUrl+"/index.php?ctl=Points&met=addPointsCart&typ=json",
//             data: param,
//             type: "POST",
//             success: function ( data ){
//                 if( data.status == 200 ) {
//                     // show_tip();
//                     location.href = WapSiteUrl + "/tmpl/integral_cart_list.html";
//                 } else {
//                     $.sDialog({
//                         skin: "red",
//                         content: data.msg,
//                         okBtn: false,
//                         cancelBtn: false
//                     });
//                 }
//             }
//         });
//     })
// });

