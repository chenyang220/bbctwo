
var html = '';
// var sub_site_id = getCookie('sub_site_specials_id');
// alert(sub_site_id);
// if(getCookie('sub_site_specials_id') === '' || getCookie('sub_site_specials_id') === 'undefined' || getCookie('sub_site_specials_id') === null){
//         sub_site_id = 0;
// }
// if(getCookie('sub_site_specials_id') === '' || getCookie('sub_site_specials_id') === 'undefined' || getCookie('sub_site_specials_id') === null){
//         sub_site_id = 0;
// }

var sub_site_id = getCookie('sub_site_id');
if(getCookie('sub_site_id') === '' || getCookie('sub_site_id') === 'undefined' || getCookie('sub_site_id') === null){
        sub_site_id = 0;
        addCookie('sub_site_id',subsite_id,0);
}
$(function() {
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Index&met=tsIndex&typ=json&ua=wap&sub_site_id="+sub_site_id,
        type: 'get',
        dataType: 'json',
        success: function(result) {
            if(result.status == 200)
            {
               if(typeof(result.data.subsite_is_open) == 'undefined' || !result.data.subsite_is_open){
                    $('#cohesive_dev').hide();
                }else{
                    if(typeof(result.data.sub_site_name) != 'undefined' && sub_site_id > 0){
                        $('.sub_site_name_span').html(result.data.sub_site_name);
                    }else{
                        $('.sub_site_name_span').html('全部');
                    }
                }




                if (result.data.label_tag_sort) {
                    var a = template.render("ts_logo_template", result);
                    $("#ts_logo").html(a);
                }
                
                if (result.data.layout_list.adv_list) {
                    var b = template.render("adv_list_template", result);
                    $("#adv_list").html(b);
                    var bannerSwipers= new Swiper(".swiper-custom-index", {
                        autoplay:true,
                        pagination: '#pagination',
                        paginationClickable: true,
                        observer:true,
                        observeParents:true
                    });
                }


                if (result.data.layout_list.goods) {
                    var b = template.render("goods_list_template", result.data.layout_list);
                    $("#goods_list").html(b);
					waterFall(2);
                    window.onscroll=function(){
                         if ($(window).scrollTop() + $(window).height() == $(document).height()) {
                             $(".masonry").append(html);
                              waterFall(2);
                         }
                         
                    }
                    // 页面尺寸改变时实时触发
                    window.onresize = function() {
                        //重新定义瀑布流
                        waterFall(2);
                    };
                    //初始化
                    window.onload = function(){
                        //实现瀑布流
                        waterFall(2);
                    }
                }


                return false;

            }
        }
    })
})