var modal=document.getElementById("modal");
var modal1=document.getElementById("modal1");
modal.style.display="none";
modal1.style.display="none";
//全选点击
$(document).on('click','.gap-check',function () {
	var $checkboxs = $('.list').find('input[type="checkbox"]');
    $checkboxs.prop("checked", true);
})

$(document).on('click','.close_k',function () {
    modal.style.display="none";
    modal1.style.display="none";
})

//加入分销店铺
$(document).on('click','.add-goods',function () {
	var chk_value =[]; 
    $('input[name="goods_id"]:checked').each(function(){ 
        chk_value.push($(this).val()); 
    });
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=addDistributionGoods&typ=json",
        type: "POST",
        data: {k: getCookie("key"), u: getCookie("id"), cid: JSON.stringify(chk_value)},
        dataType: "json",
        async: false,
        traditional:true,
        success: function (result) {
           if(result.status==200){
           		alert("添加到分销店铺成功");
           		window.location.reload();
           }else{
           		alert("添加失败");
           }
        }
    });
})

//分销商品下架
$(document).on('click','.xia',function () {
	var chk_value =[]; 
    $('input[name="goods_id"]:checked').each(function(){ 
        chk_value.push($(this).val()); 
    });
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=removeDistributionGoods&typ=json",
        type: "POST",
        data: {k: getCookie("key"), u: getCookie("id"), cid: JSON.stringify(chk_value)},
        dataType: "json",
        async: false,
        traditional:true,
        success: function (result) {
           if(result.status==200){
           		alert("商品下架成功");
           		window.location.reload();
           }else{
           		alert("操作失败");
           }
        }
    });
})
    
//分销商品推荐
$(document).on('click','.tui',function () {
    var chk_value =[]; 
    $('input[name="goods_id"]:checked').each(function(){ 
        chk_value.push($(this).val()); 
    });
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=addRecommendGoods&typ=json",
        type: "POST",
        data: {k: getCookie("key"), u: getCookie("id"), cid: JSON.stringify(chk_value)},
        dataType: "json",
        async: false,
        traditional:true,
        success: function (result) {
           if(result.status==200){
                alert("添加推荐商品成功");
           }else{
                alert("操作失败");
           }
        }
    });
})

//商品分享
$(document).on('click','.share_wap_buy',function(){
        var common_id= $(this).data('id');
        $.ajax({
            type:"POST",
            url: SiteUrl + '?ctl=Goods_Goods&met=goodsShare&typ=json',
            data:{common_id:common_id},
            dataType: "json",
            async:false,
            success:function(res){
            	if(t==2){
            		if(confirm("分享此商品加入到店铺精选")){
	                 　　$.ajax({
	                        url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=addDistributionGoods&typ=json",
	                        data: {k: getCookie("key"), u: getCookie("id"),cid:JSON.stringify([common_id])},
	                        type: "post",
	                        success: function (result) {
	                            console.log(result);
	                        }                               
	                    });
	                }
            	}
                if(res.status==200){
                    modal.style.display="block";
　　                modal1.style.display="block";
                    if (getCookie("is_app_guest")) {
                        window.location.href="/share_toall.html?goods_id=" + res.data.goods_id[0].goods_id + "&title=" + encodeURIComponent(res.data.common_name) + "&img=" + res.data.common_image + "&url=" + WapSiteUrl + "/tmpl/product_detail.html?goods_id=" + res.data.goods_id[0].goods_id + "&user_id=" + getCookie("id");
                    }else{
                        var icon = res.data.common_image;
                        var title = res.data.common_name;
                        var like = res.data.share;
                        var desc = res.data.goods_name;
                        var share_like = like + "&user_id=" + getCookie("id");
                        var nativeShare = new NativeShare();
                        var shareData = {
                            title: title,
                            desc: desc,
                            // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
                            link: share_like,
                            icon: icon,
                            // 不要过于依赖以下两个回调，很多浏览器是不支持的
                            success: function () {
                                alert("success");
                            },
                            fail: function () {
                                alert("fail");
                            }
                        };
                        nativeShare.setShareData(shareData);
                        $(".goods_img").attr("src",res.data.common_image);
                        $(".goods_name").html(res.data.common_name);
                        $(".goods_price").html("￥"+res.data.common_price);
                        $(".submit_button").click(function(){
                            modal.style.display="none";
                            modal1.style.display="none";
                            try {
                                    nativeShare.call();
                                } catch (err) {
                                    // 如果不支持，你可以在这里做降级处理
                                    alert(err.message);
                                } 

                        })
                         
                    }

                }

            }
        });
    });
