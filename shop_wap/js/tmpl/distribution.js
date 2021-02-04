$(function(){
   var e = getCookie("key");
    // if (!e) {
    //     window.location.href = WapSiteUrl + "/tmpl/member/login.html"
    // }

    $.ajax({
        type:'post',
        url:ApiUrl+"/?ctl=Distribution_NewBuyer_Distribution&met=wapIndex&typ=json",
        data:{k:e,u:getCookie('id')},
        dataType:'json',
        //jsonp:'callback',
        success:function(result){
            // console.log(result);
            $(".user_logo").attr("src",result.data.user_logo);
            $(".name").html(result.data.user_name);
            $(".dengji").html("V"+result.data.user_grade);
            $(".user_directseller_commission").html("￥"+result.data.user_directseller_commission);
            $(".invitors").html(result.data.invitors);
            $(".promotion_order_nums").html(result.data.promotion_order_nums);
            $(".goods_num").html(result.data.goods_num);
            $(".user_num").html(result.data.day_invitors);
            $(".day_order_nums").html(result.data.day_order_nums);
            $(".day_goods_num").html(result.data.day_goods_num);
            $(".income_tatol").html((result.data.income_tatol).toFixed(2));
            $(".settlement_income").html(result.data.settlement_income);

            //判断当前用户是否有上级
            if (result.data.user_parent_id > 0) {
                $("#show").attr('href', 'distribution_parent.html?parent_id=' + result.data.user_parent_id);
                $(".parent").html(1);
            } else {
                $(".parent").html(0);
            }
        }
    });

    $("#show").click(function () {
        var h = $(this).attr('href');
        if (h == 'javascript:;') {
            $.sDialog({
                skin: "red",
                content: "您还没有邀请人",
                okBtn: false,
                cancelBtn: false
            });
        }
    })
	
	//滚动header固定到顶部
	$.scrollTransparent();
 
});