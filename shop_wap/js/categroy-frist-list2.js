$(function ()
{
    var first_cat,second_cat,order,up,level=1,page = 1;
    var e;
    $("#header").on("click", ".header-inp2", function () {
        location.href = WapSiteUrl + "/tmpl/search.html"
    });
    var index;
    index=index?"0":sessionStorage.getItem('index');
    // 左侧初始化
    var shop_id_wap = getCookie('SHOP_ID_WAP');

    $.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Goods_Cat&met=cat2&typ=json&shop_id_wap=" + shop_id_wap,
        data: {cat_parent_id: "0"},
        dataType: "json",
        success: function (res) {
            var r = res.data;
            r.WapSiteUrl = WapSiteUrl;
            first_cat = r.cat_id;

            //头部一级分类
            var first_cat_html = template.render("first-cat", r);
            $("#first_cat").html(first_cat_html);
            var swiper = new Swiper('.class-swiper-container', {
                slidesPerView:"auto",
                freeMode: true,
            });

            //左侧二级分类
            var second_cat_html = template.render("second-cat", r);
            $("#categroy-cnt").html(second_cat_html);
            // $("#categroy-cnt .categroy-list li").eq(index).addClass("selected");


            //商品列表
            var goods_html = template.render("goods-list", r);
            $("#goods_list").html(goods_html);

            //e = new IScroll("#categroy-cnt", {mouseWheel: true, click: true});
        }
    });

    //一级分类
    $(document).on('click','.first_cat_item',function () {
        first_cat = $(this).data('id');
        second_cat = '';
        order = '';
        level = 1;
        page = 1;
        $(this).siblings().find('span').removeClass('active');
        $(this).find('span').addClass('active');
        $(".fresh-class-sx p span").eq(0).addClass("active").siblings().removeClass('active');
        $(".fresh-class-module").scrollTop(0);
        // $("#categroy-cnt .categroy-list li:gt(0)").remove();
        $("#categroy-cnt .categroy-list").not('.first').remove();
        $("#goods_list").html('');
        getCatGoodsList(true);
    });

    //二级分类
    $(document).on('click', '.category-item', function () {
        second_cat = $(this).data('id');
        $(this).addClass('selected').siblings().removeClass('selected');
        if (second_cat) {
            level = 2;
        }else{
            level = 1;
        }
        page = 1;
        $(".fresh-class-module").scrollTop(0);
        $("#goods_list").html('');
        getCatGoodsList(true);
    });

    //排序
    $(".orderBy").click(function () {
        var index = $(this).index();
        page = 1;
        $(".fresh-class-module").scrollTop(0);
        if (index == 2){
            if (!up){
                up = true;
                order = 'up';
                $(this).addClass('active up').removeClass('down').siblings().removeClass('active');
            }else{
                up = false;
                order = 'down';
                $(this).addClass('active down').removeClass('up').siblings().removeClass('active');
            }
        } else{
            $(this).addClass('active').siblings().removeClass('active up down');
            order = $(this).data('info');
        }
        $("#goods_list").html('');
        getCatGoodsList(true);
    });

    //滚动加载
    $(".fresh-class-module").scroll(function () {
        if ($(".fresh-class-module").scrollTop() + $(".fresh-class-module").height() > $(".fresh-class-ul").height() - 1) {
            // $.sDialog({skin: "red", content: '1212', okBtn: false, cancelBtn: false});
            page++;
            getCatGoodsList(false);
        }
    });


    //加入购物车
    $(document).on('click','.add_cart',function(){
        var goods_id = $(this).data('goods_id');
        var key = getCookie("key");//登录标记
        var quantity = 1;
        if (!key) {
            var goods_info = decodeURIComponent(getCookie("goods_cart"));
            if (goods_info == null) {
                goods_info = "";
            }
            if (goods_id < 1) {
                return false;
            }
            var cart_count = 0;
            if (!goods_info) {
                goods_info = goods_id + "," + quantity;
                cart_count = 1;
            } else {
                var goodsarr = goods_info.split("|");
                console.log(goodsarr);
                for (var i = 0; i < goodsarr.length; i++) {
                    var arr = goodsarr[i].split(",");
                    if (contain(arr, goods_id)) {
                        alert('已经添加');
                        return false;
                    }
                }
                goods_info += "|" + goods_id + "," + quantity;
                cart_count = goodsarr.length;
            }
            // 加入cookie
            addCookie("goods_cart", goods_info);
            // 更新cookie中商品数量
            addCookie("cart_count", cart_count);

            getCartCount();
            alert('添加成功');
            // $.sDialog({skin: "red", content:'加入成功', okBtn: false, cancelBtn: false});
            return false;
        } else {
            //判断用户是否已经绑定手机号
            if (!checkUserMobile()) {
                $.sDialog({
                    skin: "red",
                    content: "请先绑定手机号",
                    okBtn: true,
                    okFn: function () {
                        window.location.href = WapSiteUrl + '/tmpl/member/member_mobile_bind.html';
                    },
                    cancelBtn: true
                });
                return false;
            }

            // if (data.shop_owner) {
            //     $.sDialog({
            //         skin: "red",
            //         content: "不能购买自己商店的商品！",
            //         okBtn: false,
            //         cancelBtn: false
            //     });
            //     return;
            // }
            $.ajax({
                url: ApiUrl + "/index.php?ctl=Buyer_Cart&met=addCart&typ=json",
                data: {k: key, u: getCookie("id"), goods_id: goods_id, goods_num: quantity},
                type: "post",
                success: function (result) {
                    if (checkLogin(result.login)) {
                        if (result.status == 200) {
                            // 更新购物车中商品数量
                            delCookie("cart_count");
                            getCartCount();
                            $.sDialog({skin: "red", content: '添加成功', okBtn: false, cancelBtn: false});
                        } else {
                            $.sDialog({
                                skin: "red",
                                content: result.msg,
                                okBtn: false,
                                cancelBtn: false
                            });
                        }
                    }
                }
            });
        }
    });

    function contain(arr, str) {
        var i = arr.length;
        while (i--) {
            if (arr[i] == str) {
                return true;
            }
        }
        return false;
    }

    function getCatGoodsList(flag){
        var data = {
            level:level,
            first_cat: first_cat,
            second_cat: second_cat,
            order:order,
            page:page
        }
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Goods_Cat&met=getCatGoodsList&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                var data = res.data;

                //左侧二级分类
                if (level == 1){
                    var second_cat_html = template.render("second-cat", data);
                    $("#categroy-cnt").html(second_cat_html);
                    $("#categroy-cnt .categroy-list li").eq(0).addClass("selected");
                }

                //商品列表
                var goods_html = template.render("goods-list", data);
                if (flag){
                    $("#goods_list").html(goods_html);
                } else{
                    $("#goods_list").append(goods_html);
                }
            }
        })
    }

});



