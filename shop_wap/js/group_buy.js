$(function() {
    var param = {
        k: getCookie("key"),
        u: getCookie("id")
    };
    
    if(!getCookie('sub_site_id')){
        addCookie('sub_site_id',0,0);
    }
    var sub_site_id = getCookie('sub_site_id');

    //判断是否有groupbuy_id
    var groupbuy = '';
    if(getQueryString('group_buy_id'))
    {
        groupbuy = '&groupbuy_id='+getQueryString('group_buy_id');
    }

    $.ajax({
        url: ApiUrl + "/index.php?ctl=GroupBuy&met=index&typ=json&ua=wap&sub_site_id="+sub_site_id+groupbuy,
        type: 'get',
        dataType: 'json',
        data: param,
        success: function(data) {
            if ( data.status == 200 ) {
                // console.info(data);
                var data = data.data;

                // var swiper_container_nav = template.render("swiper-container-nav", data);
                // $(".swiper-container-nav").append(swiper_container_nav);
                // var mySwiper = new Swiper('.swiper-container-nav', {
                //     slidesPerView: 4

                // })
                
                $(".righttubiao a").addClass("bottG");
                //判断是否有轮播图
                var banner = data.banner;
                console.log(banner)

                if (typeof banner != "undefined") {
                    if(banner.slider1.slider1_image == '' && banner.slider2.slider2_image == '' && banner.slider3.slider3_image == '' && banner.slider4.slider4_image == '') {
                        $(".swiper-pagination").hide();
                    }
                }
                
                var swiper_banner = template.render("swiper-banner", data);
                $(".swiper-banner").append(swiper_banner);
                var swiper = new Swiper('.swiper-banner', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    autoplay: 3000,
                    nextButton: '.swiper-button-next',
                    prevButton: '.swiper-button-prev',
                    spaceBetween: 30
                });

                var maskG = template.render("maskG", data);
                $(".maskG").append(maskG);
                $(".X").click(function () {
                    $(".maskG").css("display", "none");
                    // $(".righttubiao a").removeClass("topg");
                    // $(".righttubiao a").addClass("bottG");
                    $("body").css("overflow", "none");
                })

                var Group_main = template.render("Group-main", data);
                $(".Group-main").append(Group_main);

                var _TimeCountDown = $(".fnTimeCountDown");
                _TimeCountDown.fnTimeCountDown();
            } else {
                if (data.msg) {
                    $.sDialog({skin: "red", content: data.msg, okBtn: false, cancelBtn: false});
                    $(".Group-banenr").hide();
                    var str='<div class="activity-close-text">'+data.msg+'</div>';
                    $(".Group-main").html(str);
                } else {
                    $.sDialog({skin: "red", content: "网络异常", okBtn: false, cancelBtn: false});
                }
            }
        }
    });

});

$(document).ready(function () {
    $(".swiper-container-nav .swiper-slide").click(function () {
        $(".swiper-container-nav .swiper-slide").removeClass("bor-2");
        $(this).addClass("bor-2");

    })
    var onoffcont = true;
    $(".righttubiao").click(function (e) {
        if (onoffcont) {
            
            $(".maskG").show();
            onoffcont = false;
        } else {
            $(".maskG").hide();
            onoffcont = true;
        }
    });

})

