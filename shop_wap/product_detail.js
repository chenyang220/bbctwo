// window.onload = function () {
//     var i = 10;
//     var j = 0;
//     var e = target;
//     var win = document.documentElement || document.body;
//
//     function intern() {
//         var width = e.clientWidth;
//         var height = e.clientHeight;
//         var left = parseFloat(e.style.left);
//         var top = parseFloat(e.style.top);
//         var windowWidth = win.clientWidth;
//         var windowHeight = win.clientHeight;
//         if (windowWidth - width < (left + i)) {
//             i = -i;
//         } else if ((left + i) < 0) {
//             i = -i;
//         }
//         if (windowHeight - height < (top + j)) {
//             j = -j;
//         } else if ((top + j) < 0) {
//             j = -j;
//         }
//         e.style.left = left + i + "px";
//         e.style.top = top + j + "px";
//     }
//
//     setInterval(intern, 500);
// };

var goods_id = getQueryString("goods_id");
var uu_id = getQueryString("uu_id");
var cid = getQueryString("cid");
var lbs_geo = getCookie("lbs_geo");
var rec = getQueryString("rec");

var mydate = new Date();
var goodsInformation;
var goods_detail = [];

if (uu_id) {
    var str1 = document.domain;
    var str2 = str1.substring(str1.indexOf("."));
    
    $.fn.cookie("uu_id", uu_id, {expires: mydate.getTime() + 60 * 60 * 24 * 3, path: "/", domain: str2});
}

if (rec) {
    var str1 = document.domain;
    var str2 = str1.substring(str1.indexOf("."));
    
    $.fn.cookie("yf_recserialize", rec, {expires: mydate.getTime() + 60 * 60 * 24 * 3, path: "/", domain: str2});
}

var option_window = false; //当前弹框
//如果没有goods_id，则根据cid获取goods_id 取第一个数据
if (!goods_id && cid) {
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Goods_Goods&met=getGoodsidByCid&typ=json",
        type: "POST",
        data: {k: getCookie("key"), u: getCookie("id"), cid: cid},
        dataType: "json",
        async: false,
        success: function (result) {
            if (result.status == 200) {
                goods_id = result.data.goods_id;
            }
        }
    });
}
var map_list = [];
var map_index_id = "";
var shop_id;
$(function () {
    var key = getCookie("key");
    var num = 0;
    var unixTimeToDateString = function (ts, ex) {
        ts = parseFloat(ts) || 0;
        if (ts < 1) {
            return "";
        }
        var d = new Date();
        d.setTime(ts * 1e3);
        var s = "" + d.getFullYear() + "-" + (1 + d.getMonth()) + "-" + d.getDate();
        if (ex) {
            s += " " + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds();
        }
        return s;
    };
    
    var buyLimitation = function (a, b) {
        a = parseInt(a) || 0;
        b = parseInt(b) || 0;
        var r = 0;
        if (a > 0) {
            r = a;
        }
        if (b > 0 && r > 0 && b < r) {
            r = b;
        }
        return r;
    };
    
    template.helper("isEmpty", function (o) {
        for (var i in o) {
            return false;
        }
        return true;
    });
    // 图片轮播
    var swiperpic;

    function picSwipe() {
        var elem = $("#mySwipe")[0];
        swiperpic = new Swiper("#mySwipe", {
            pagination: ".swiper-pagination-banner",
            paginationType: "custom",
            paginationClickable: true,
            nextButton: ".swiper-button-next",
            prevButton: ".swiper-button-prev",
            onSlideChangeStart: function (swiper) {
                var index = swiper.activeIndex;
                if ($("#mySwipe").find("video").length > 0) {
                    if (index != 0) {
                        document.getElementById("common_video").pause();
                    }
                    
                }
                
            },
            
            paginationCustomRender: function (swiper, current, total) {
                var customPaginationHtml = "";
                var common_video = $("common_video").val;
                if ($(".goods-detail-pic video").length > 0) {
                    for (var i = 0; i < total; i++) {
                        //判断哪个分页器此刻应该被激活
                        
                        if (i == current - 1) {
                            if (i == 0) {
                                if (common_video != undefined) {
                                    customPaginationHtml += "<span class=\"swiper-pagination-customs swiper-pagination-customs-active\"><i>视频</i></span>";
                                }
                                
                            } else if (i == 1) {
                                customPaginationHtml += "<span class=\"swiper-pagination-customs  swiper-pagination-customs-active swiper-pagination-customs-img\"><i>图片</i></span>";
                                customPaginationHtml += "<b class=\"swiper-pagination-customs swiper-pagination-customs-active\">" + i + "/" + (total - 1) + "</b>";
                                
                            } else {
                                customPaginationHtml += "<b class=\"swiper-pagination-customs swiper-pagination-customs-active\">" + i + "/" + (total - 1) + "</b>";
                            }
                            
                        } else {
                            if (i == 0) {
                                if (common_video != undefined) {
                                    customPaginationHtml += "<span class=\"swiper-pagination-customs\"><i>视频</i></span>";
                                }
                            } else if (i == 1) {
                                if (current > 2) {
                                    customPaginationHtml += "<span class=\"swiper-pagination-customs swiper-pagination-customs-img swiper-pagination-customs-imgs active\"><i>图片</i></span>";
                                    customPaginationHtml += "<b class=\"swiper-pagination-customs\">" + i + "/" + (total - 1) + "</b>";
                                } else {
                                    customPaginationHtml += "<span class=\"swiper-pagination-customs swiper-pagination-customs-img swiper-pagination-customs-imgs\"><i>图片</i></span>";
                                    customPaginationHtml += "<b class=\"swiper-pagination-customs\">" + i + "/" + (total - 1) + "</b>";
                                }
                                
                            } else {
                                customPaginationHtml += "<b class=\"swiper-pagination-customs\">" + i + "/" + (total - 1) + "</b>";
                            }
                            
                        }
                    }
                } else {
                    for (var i = 0; i < total; i++) {
                        //判断哪个分页器此刻应该被激活
                        if (i == current - 1) {
                            customPaginationHtml += "<b class=\"swiper-pagination-customs swiper-pagination-customs-active\">" + (i + 1) + "/" + total + "</b>";
                            
                        } else {
                            customPaginationHtml += "<b class=\"swiper-pagination-customs\">" + (i + 1) + "/" + total + "</b>";
                            
                        }
                    }
                }
                
                return customPaginationHtml;
            }
        });
    }
    
    // banner放大查看
    var winWidth = $(window).width();
    var swiper, dis, tr;
    var client = $(document.body)[0].clientWidth;
    
    function picEnlarge() {
        swiper = new Swiper(".banner-enlarge-swiper", {
            pagination: ".swiper-pagination-enlarge",
            paginationType: "custom",
            paginationClickable: true,
            onSlideChangeStart: function (swiper) {
                if (swiper.activeIndex != 0) {
                    document.getElementById("common_video2").pause();
                }
            },
            onTouchEnd: function (swiper) {
                dis = client * ($(".banner-enlarge-swiper .swiper-slide").length - 1);
                tr = swiper.translate;
                if (tr < -dis) {
                    swiper.setWrapperTranslate(tr);
                    $(".banner-enlarge").removeClass("active");
                }
            },
            paginationCustomRender: function (swiper, current, total) {
                var customPaginationHtml = "";
                var common_video = $("common_video").val;
                if ($(".goods-detail-pic video").length > 0) {
                    for (var i = 0; i < total; i++) {
                        //判断哪个分页器此刻应该被激活 
                        if (i == current - 1) {
                            if (i == 0) {
                                if (common_video != undefined) {
                                    customPaginationHtml += "<span class=\"swiper-pagination-customs swiper-pagination-customs-active\">视频</span>";
                                }
                            } else if (i == 1) {
                                customPaginationHtml += "<span class=\"swiper-pagination-customs  swiper-pagination-customs-active swiper-pagination-customs-img\">图片</span>";
                                customPaginationHtml += "<b class=\"swiper-pagination-customs swiper-pagination-customs-active\">" + i + "/" + (total - 1) + "</b>";
                                
                            } else {
                                customPaginationHtml += "<b class=\"swiper-pagination-customs swiper-pagination-customs-active\">" + i + "/" + (total - 1) + "</b>";
                            }
                            
                        } else {
                            if (i == 0) {
                                if (common_video != undefined) {
                                    customPaginationHtml += "<span class=\"swiper-pagination-customs\">视频</span>";
                                }
                            } else if (i == 1) {
                                if (current > 2) {
                                    customPaginationHtml += "<span class=\"swiper-pagination-customs swiper-pagination-customs-img swiper-pagination-customs-imgs active\">图片</span>";
                                    customPaginationHtml += "<b class=\"swiper-pagination-customs\">" + i + "/" + (total - 1) + "</b>";
                                } else {
                                    customPaginationHtml += "<span class=\"swiper-pagination-customs swiper-pagination-customs-img swiper-pagination-customs-imgs\">图片</span>";
                                    customPaginationHtml += "<b class=\"swiper-pagination-customs\">" + i + "/" + (total - 1) + "</b>";
                                }
                                
                            } else {
                                customPaginationHtml += "<b class=\"swiper-pagination-customs\">" + i + "/" + (total - 1) + "</b>";
                            }
                        }
                    }
                } else {
                    for (var i = 0; i < total; i++) {
                        //判断哪个分页器此刻应该被激活 
                        if (i == current - 1) {
                            
                            customPaginationHtml += "<b class=\"swiper-pagination-customs swiper-pagination-customs-active\">" + (i + 1) + "/" + total + "</b>";
                            
                        } else {
                            
                            customPaginationHtml += "<b class=\"swiper-pagination-customs\">" + (i + 1) + "/" + total + "</b>";
                        }
                    }
                }
                
                return customPaginationHtml;
            }
        });
    }
    
    
    get_detail(goods_id);
    
    
    // 点击规格属性时，如果当前为选中状态则取消
    // 只有当每种规格属性都选择，才可以发起请求，拉去商品信息
    
    // 点击规格属性时，判断是否发起请求
    function checkSpec(spec) {
        var $spec = $(spec);
        
        $spec.hasClass("current")
            ? ($spec.removeClass("current"), $("span.goods-storage").text())
            :$spec.addClass("current").siblings().removeClass("current");
        
        return $("#product_roll").find("dl.spec").length == $("#product_roll").find("a.current").length
            ? true
            :false;
    }
    
    //点击商品规格，获取新的商品
    function arrowClick(self, myData) {
        
        if (!checkSpec(self)) {
            return false;
        }
        
        //拼接属性
        var curEle = $(".spec").find("a.current");
        var curSpec = [];
        $.each(curEle, function (i, v) {
            // convert to int type then sort
            curSpec.push(parseInt($(v).attr("specs_value_id")) || 0);
        });
        var spec_string = curSpec.sort(function (a, b) {
            return a - b;
        }).join("|");
        //获取商品ID
        goods_id = myData.spec_list[spec_string];
        get_detail(goods_id);
    }
    
    function contains(arr, str) {//检测goods_id是否存入
        var i = arr.length;
        while (i--) {
            if (arr[i] === str) {
                return true;
            }
        }
        return false;
    }
    
    $.sValid.init({
        rules: {
            buynum: "digits"
        },
        messages: {
            buynum: "请输入正确的数字"
        },
        callback: function (eId, eMsg, eRules) {
            if (eId.length > 0) {
                var errorHtml = "";
                $.map(eMsg, function (idx, item) {
                    errorHtml += "<p>" + idx + "</p>";
                });
                $.sDialog({
                    skin: "red",
                    content: errorHtml,
                    okBtn: false,
                    cancelBtn: false
                });
            }
        }
    });
    
    //检测商品数目是否为正整数
    function buyNumer() {
        $.sValid();
    }
    
    
    function get_detail(goods_id) {
        if (lbs_geo == "" || lbs_geo == "undefined" || lbs_geo == null) {
            var lbs_geo = getCookie("lbs_geo");
        }
        //渲染页面
        $.ajax({
            url: ApiUrl + "/index.php?ctl=Goods_Goods&met=goods&typ=json",
            type: "get",
            data: {goods_id: goods_id, k: key, u: getCookie("id"), cid: cid, lbs_geo: lbs_geo, ua: "wap"},
            dataType: "json",
            success: function (result) {
                console.log(result);
                var data = result.data;
                if (!data.goods_id) {
                    $.sDialog({
                        content: "该商品已下架或该店铺已关闭！<br>请返回上一页继续操作…",
                        width: 330,
                        height: 200,
                        okBtn: false,
                        cancelBtnText: "返回",
                        cancelFn: function () {
                            history.back();
                        }
                    });
                }
                evalcount = data.goods_info["evalcount"];
                goods_detail = result.data;
                if (result.status == 200) {
                    goodsInformation = result.data;
                    var tel = result.data.store_info.store_tel;
                    $.getJSON(SiteUrl + "/index.php?ctl=Api_Wap&met=version_im&typ=json", function (r) {
                        var st = r.data.im;
                        if (st == 1) {
                            $(".goods-detail-foot .kefu").show();
                        } else if (tel) {
                            setTimeout(function () {
                                $(".goods-detail-foot .kefu").addClass("phone").attr("href", "tel:" + tel).show();
                            }, 500);
                        }
                    });
                    $("title").html(data.goods_info.goods_name);
                    //商品图片格式化数据
                    if (data.goods_image) {
                        var goods_image = data.goods_image.split(";");
                        data.goods_image = goods_image;
                    } else {
                        data.goods_image = [];
                    }
                    //上啊品图片
                    if (data.goods_info) {
                        //商品规格格式化数据
                        if (data.goods_info.common_spec_name) {
                            var goods_map_spec = $.map(data.goods_info.common_spec_name, function (v, i) {
                                var goods_specs = {};
                                goods_specs["goods_spec_id"] = i;
                                goods_specs["goods_spec_name"] = v;
                                if (data.goods_info.common_spec_value_c) {
                                    $.map(data.goods_info.common_spec_value_c, function (vv, vi) {
                                        if (i == vi) {
                                            goods_specs["goods_spec_value"] = $.map(vv, function (vvv, vvi) {
                                                var specs_value = {};
                                                specs_value["specs_value_id"] = vvi;
                                                specs_value["specs_value_name"] = vvv;
                                                return specs_value;
                                            });
                                        }
                                    });
                                    return goods_specs;
                                } else {
                                    data.goods_info.common_spec_value = [];
                                }
                            });
                            data.goods_map_spec = goods_map_spec;
                        } else {
                            data.goods_map_spec = [];
                        }
                        
                        // 虚拟商品限购时间和数量
                        if (data.goods_info.common_is_virtual == "1") {
                            data.goods_info.virtual_indate_str = unixTimeToDateString(data.goods_info.virtual_indate, true);
                            data.goods_info.buyLimitation = buyLimitation(data.goods_info.virtual_limit, data.goods_info.upper_limit);
                        }
                        
                        // 预售发货时间
                        /*if (data.goods_info.is_presell == '1') {
                         data.goods_info.presell_deliverdate_str = unixTimeToDateString(data.goods_info.presell_deliverdate);
                         }*/
                        
                        //渲染模板
                        var html = template.render("product_detail", data);
                        $("#product_detail_html").html(html);
                        //海报
                        if (getCookie("id")) {
                            $("#bill").show();
                            $('.Commission').show();
                        } else {
                            $("#bill").hide();
                            $('.Commission').hide();
                        }

                        var windowWidth=$(window).width();
                        $("#mySwipe").css("height",windowWidth);
    
                        // var userAgentInfos = /iphone/gi.test(navigator.userAgent) && (screen.height == 812 && screen.width == 375);
                        // if (userAgentInfos == true) {
                        //     $(".goods-detail-foot").css({"padding-bottom": "34px"});
                        // }
                        if (getCookie("is_app_guest")) {
                            $("#shareit").attr("href", "/share_toall.html?goods_id=" + data.goods_info.goods_id + "&title=" + encodeURIComponent(data.goods_info.goods_name) + "&img=" + data.goods_image[0] + "&url=" + WapSiteUrl + "/tmpl/product_detail.html?goods_id=" + data.goods_info.goods_id);
                        }
                        if (data.goods_info.common_is_virtual == "0") {
                            $(".goods-detail-o2o").remove();
                        }
                        //渲染模板
                        var html = template.render("product_detail_sepc", data);
                        $("#product_detail_spec_html").html(html);
                        
                        //渲染模板
                        if (typeof(data.promotion_info.voucher_list) != "undefined" && data.promotion_info.voucher_list.length > 0) {
                            var voucher_list_text = "";
                            var voucher_list = data.promotion_info.voucher_list;
                            for (var i in voucher_list) {
                                /*if(voucher_list[i].is_get == 1){*/
                                voucher_list_text = " ￥" + voucher_list[i].voucher_t_price + "代金券;" + voucher_list_text;
                                /*}*/
                            }
                            if (voucher_list_text != "") {
                                if (voucher_list_text.length > 23) {
                                    voucher_list_text = voucher_list_text.substr(1, 20) + "...";
                                }
                                $("#voucher_list_text").html("领取代金券");
                            }
                            var html = template.render("voucher_script", data.promotion_info);
                            $("#voucher_html").html(html);
                        } else {
                            $("#voucher_html").hide();
                            $("#getVoucher").hide();
                        }
                        
                        if (!data.goods_info.chain_stock) {
                            $("#ziti").hide();
                        }
                        
                        //渲染模板
                        var html = template.render("sale-activity", data);
                        $("#sale-activity-html").html(html);
                        contentList['details'] = 'true';
                        integrity();
                        shop_id = data.store_info.store_id;
                        if (data.goods_info.is_virtual == "1") {
                            virtual();
                        }
                        getCartCount();
                        // 购物车中商品数量
                        if (getCookie("cart_count")) {
                            if (getCookie("cart_count") > 0) {
                                $("#cart_count,#cart_count1").html("<sup>" + getCookie("cart_count") + "</sup>");
                            }
                        }
                        
                        //图片轮播
                        picSwipe();
                        
                        // 点击banner显示大图
                        
                        $(".goods-detail-pic li").click(function () {
                            
                            var indexs = $(this).index();
                            $(".banner-enlarge").addClass("active");
                            if ($(".goods-detail-pic").find("video").length > 0) {
                                document.getElementById("common_video").pause();
                            }
                            picEnlarge();
                            $(".banner-enlarge-swiper li").removeClass("swiper-slide-active,swiper-slide-next");
                            $(".banner-enlarge-swiper li").eq(indexs).addClass("swiper-slide-active");
                            $(".banner-enlarge-swiper li").eq(indexs + 1).addClass("swiper-slide-next");
                            var ulCurrent = "translate3d(" + (-indexs * winWidth) + "px, 0px, 0px)";
                            $(".banner-enlarge-swiper .swiper-wrapper").css({
                                "transform": ulCurrent
                            });
                            
                            $(".swiper-pagination-enlarge b").removeClass("swiper-pagination-customs-active");
                            if (indexs >= 1) {
                                $(".swiper-pagination-enlarge span").removeClass("swiper-pagination-customs-active");
                                $(".swiper-pagination-enlarge span").eq(1).addClass("swiper-pagination-customs-active");
                                $(".swiper-pagination-enlarge b").eq(indexs).addClass("swiper-pagination-customs-active");
                            }
                            
                        });
                        $(".banner-enlarge .btn-close").click(function () {
                            
                            $(this).parent(".banner-enlarge").removeClass("active");
                            document.getElementById("common_video2").pause();
                        });
                        // 点击视频和图片按钮切换
                        $(document).on("click", ".goods-detail-pic .swiper-pagination-banner span", function (ev) {
                            var spanIndex = $(this).index();
                            if (spanIndex == 0) {
                                swiperpic.slideTo(0, 100, false);
                                // document.getElementById("common_video").play();
                            } else if (spanIndex == 1) {
                                swiperpic.slideTo(1, 100, false);
                                document.getElementById("common_video").pause();
                            }
                            var e = window.event || ev;
                            if (e.stopPropagation) {
                                e.stopPropagation();
                            } else {
                                e.cancelBubble = true;
                            }
                        });
                        
                        $(document).on("click", ".banner-enlarge .swiper-pagination-enlarge span", function () {
                            var spantab = $(this).index();
                            if (spantab == 0) {
                                swiper.slideTo(0, 100, false);
                                // document.getElementById("common_video2").play();
                            } else if (spantab == 1) {
                                swiper.slideTo(1, 100, false);
                                document.getElementById("common_video2").pause();
                            }
                        });
                        
                        
                        //商品描述
                        $(".pddcp-arrow").click(function () {
                            $(this).parents(".pddcp-one-wp").toggleClass("current");
                        });
                        
                        //规格属性
                        var myData = {};
                        myData["spec_list"] = data.spec_list;
                        $(".spec a").click(function () {
                            var self = this;
                            arrowClick(self, myData);
                        });
                        //购买数量，减
                        $(".minus").click(function () {
                            var buynum = $(".buy-num").val();
                            var data_min = parseInt($(".buy-num").data("min"));
                            var promotion = parseInt($(".buy-num").attr("promotion"));
                            if (buynum > data_min) {
                                $(".buy-num").val(parseInt(buynum - 1));
                            } else {
                                if (promotion == 1) {
                                    var content = "该限时折扣商品最少需购买" + data_min + "件";
                                } else {
                                    var content = "该商品最少需购买" + data_min + "件";
                                }
                                $.sDialog({
                                    content: content,
                                    okBtn: false,
                                    cancelBtn: false
                                });
                                return false;
                            }
                        });
                        
                        //购买数量加
                        $(".add").click(function () {
                            var buynum = parseInt($(".buy-num").val());
                            var data_max = parseInt($(".buy-num").data("max"));
                            if (parseInt(buynum + 1) >= data_max && data_max > 0) {
                                if (buynum == data_max && buynum > 0) {
                                    //如果最大购买数量和库存数量相同，则提示库存不足
                                    if (data.goods_info.goods_stock == data_max) {
                                        $.sDialog({
                                            content: "库存不足",
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                    }
                                    else {
                                        $.sDialog({
                                            content: "每个用户最多只能购买" + data_max + "件",
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                    }
                                    
                                    return false;
                                }
                                $(".buy-num").val(parseInt(data_max));
                            } else {
                                $(".buy-num").val(parseInt(buynum + 1));
                            }
                        });
                        //手动修改商品数量
                        function initChangeProductNum() {
                            $("#buynum").bind({
                                focus: function () {
                                    $(this).toggleClass("chose-product-num");
                                },
                                blur: function () {
                                    $(this).toggleClass("chose-product-num");
                                },
                                change: function () {
                                    var buy_num = $(".buy-num").val();
                                    var data_min = parseInt($(".buy-num").data("min"));
                                    var data_max = parseInt($(".buy-num").data("max"));
                                    var promotion = parseInt($(".buy-num").attr("promotion"));
                                    
                                    //小于最低限制
                                    if (buy_num < data_min) {
                                        this.value = data_min;
                                        if (promotion == 1) {
                                            var content = "该限时折扣商品最少需购买" + data_min + "件";
                                        } else {
                                            var content = "该商品最少需购买" + data_min + "件";
                                        }
                                        $.sDialog({
                                            content: content,
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                        return false;
                                    } else if (buy_num > data_max) {
                                        this.value = data_max;
                                        $.sDialog({
                                            content: "每个用户最多只能购买" + data_max + "件",
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                        return false;
                                    }
                                }
                            });
                        };
                        
                        // 一个F码限制只能购买一件商品 所以限制数量为1
                        if (data.goods_info.is_fcode == "1") {
                            $(".minus").hide();
                            $(".add").hide();
                            $(".buy-num").attr("readOnly", true);
                        }
                        //收藏
                        $(".pd-collect").click(function () {
                            if ($(this).hasClass("favorate")) {
                                if (dropFavoriteGoods(goods_id)) {
                                    $(this).removeClass("favorate");
                                }
                            } else {
                                if (favoriteGoods(goods_id)) {
                                    $(this).addClass("favorate");
                                }
                            }
                        });
                        //加入购物车
                        $("#add-cart").click(function () {
                            if ($(".buy-handle").hasClass("no-buy")) {
                                $.sDialog({
                                    skin: "red",
                                    content: "该商品暂时无货！",
                                    okBtn: false,
                                    cancelBtn: false
                                });
                                return;
                            }
                            var key = getCookie("key");//登录标记
                            var quantity = parseInt($(".buy-num").val());
                            if (!key) {
                                var goods_info = decodeURIComponent(getCookie("goods_cart"));
                                if (goods_info == null) {
                                    goods_info = "";
                                }
                                if (goods_id < 1) {
                                    show_tip();
                                    return false;
                                }
                                var cart_count = 0;
                                if (!goods_info) {
                                    goods_info = goods_id + "," + quantity;
                                    cart_count = 1;
                                } else {
                                    var goodsarr = goods_info.split("|");
                                    for (var i = 0; i < goodsarr.length; i++) {
                                        var arr = goodsarr[i].split(",");
                                        if (contains(arr, goods_id)) {
                                            $(".nctouch-bottom-mask-bg").trigger("click");
                                            show_tip();
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
                                show_tip();
                                getCartCount();
                                $("#cart_count,#cart_count1").html("<sup>" + cart_count + "</sup>");
                                // $(".nctouch-bottom-mask-bg").trigger("click");
                                return false;
                            } else {
                                //判断用户是否已经绑定手机号
                                if (!checkUserMobile()) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "请先绑定手机号",
                                        okBtn: true,
                                        okFn: function() {
                                            window.location.href = WapSiteUrl+'/tmpl/member/member_mobile_bind.html'; 
                                        },
                                        cancelBtn: true
                                    });
                                    return false;
                                }
                                
                                if (data.shop_owner) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "不能购买自己商店的商品！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                if (data.isBuyHave) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "您已达购买上限！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                if (data.buyer_limit > 0 && data.buyer_limit < quantity) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "该商品每人限购" + data.buyer_limit + "件！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                $.ajax({
                                    url: ApiUrl + "/index.php?ctl=Buyer_Cart&met=addCart&typ=json",
                                    data: {k: key, u: getCookie("id"), goods_id: goods_id, goods_num: quantity},
                                    type: "post",
                                    success: function (result) {
                                        /*var rData = $.parseJSON(result);*/
                                        if (checkLogin(result.login)) {
                                            if (result.status == 200) {
                                                show_tip();
                                                // 更新购物车中商品数量
                                                delCookie("cart_count");
                                                getCartCount();
                                                $("#cart_count,#cart_count1").html("<sup>" + getCookie("cart_count") + "</sup>");
                                                $("#product_detail_spec_html>.nctouch-bottom-mask-bg").trigger("click");
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
                        
                        //立即购买
                        if (data.goods_info.common_is_virtual == "1") {
                            $("#buy-now").click(function () {
                                if ($(".buy-handle").hasClass("no-buy")) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "该商品暂时无货！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                var key = getCookie("key");//登录标记
                                if (!key) {
                                    //window.location.href = WapSiteUrl+'/tmpl/member/login.html';
                                    callback = window.location.href;
                                    login_url = UCenterApiUrl + "?ctl=Login&met=index&typ=e";
                                    callback = ApiUrl + "?ctl=Login&met=check&typ=e&redirect=" + encodeURIComponent(callback);
                                    login_url = login_url + "&from=wap&callback=" + encodeURIComponent(callback);
                                    window.location.href = login_url;
                                    return false;
                                }
                                //判断用户是否已经绑定手机号
                                if (!checkUserMobile()) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "请先绑定手机号",
                                        okBtn: true,
                                        okFn: function() {
                                            window.location.href = WapSiteUrl+'/tmpl/member/member_mobile_bind.html'; 
                                        },
                                        cancelBtn: true
                                    });
                                    return false;
                                }
                                
                                var buynum = parseInt($(".buy-num").val()) || 0;
                                
                                if (buynum < 1) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "参数错误！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                if (buynum > data.goods_info.goods_storage) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "库存不足！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                
                                // 虚拟商品限购数量
                                if (data.goods_info.buyLimitation > 0 && buynum > data.goods_info.buyLimitation) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "超过限购数量！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                
                                if (data.shop_owner) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "不能购买自己商店的商品！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                if (data.isBuyHave) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "您已达购买上限！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                
                                if (data.buyer_limit > 0 && data.buyer_limit < buynum) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "该商品每人限购" + data.buyer_limit + "件！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }

                                if (data.goods_info.is_order==1) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "该秒杀商品每人只能购买1次！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                
                                var json = {};
                                json.key = key;
                                json.cart_id = goods_id;
                                json.quantity = buynum;
                                
                                /* location.href = WapSiteUrl + '/tmpl/order/vr_buy_step1.html?goods_id=' + goods_id + '&quantity=' + buynum;*/
                                $.ajax({
                                    type: "post",
                                    url: ApiUrl + "/index.php?ctl=Goods_Goods&met=checkVirtual&typ=json",
                                    data: {k: key, u: getCookie("id"), goods_id: goods_id, goods_num: buynum, buy_now: 1},
                                    dataType: "json",
                                    success: function (result) {
                                        
                                        if (result.status == 250) {
                                            $.sDialog({
                                                skin: "red",
                                                content: "您已达购买上限",
                                                okBtn: false,
                                                cancelBtn: false
                                            });
                                        } else {
                                            if(data.goods_info.promotion_type=='seckill'){
                                                var seckill_goods_id = data.goods_info.seckill_info.seckill_goods_id;
                                                location.href = WapSiteUrl + "/tmpl/order/vr_buy_step1.html?goods_id=" + goods_id + "&quantity=" + buynum+"&seckill=1&seckill_goods_id="+seckill_goods_id;
                                            }else{
                                                location.href = WapSiteUrl + "/tmpl/order/vr_buy_step1.html?goods_id=" + goods_id + "&quantity=" + buynum;
                                            }
                                        }
                                    }
                                });
                            });
                        } else {
                            $("#buy-now").click(function () {
                                if ($(".buy-handle").hasClass("no-buy")) {
                                    $.sDialog({
                                        skin: "red",
                                        content: "该商品暂时无货！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                if (data.goods_info.goods_stock <= 0) {
                                    return $.sDialog({
                                        skin: "red",
                                        content: "该商品暂时无货！",
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                }
                                
                                var key = getCookie("key");//登录标记
                                
                                if (!key) {
                                    //window.location.href = WapSiteUrl+'/tmpl/member/login.html';
                                    callback = window.location.href;
                                    login_url = UCenterApiUrl + "?ctl=Login&met=index&typ=e";
                                    callback = ApiUrl + "?ctl=Login&met=check&typ=e&redirect=" + encodeURIComponent(callback);
                                    login_url = login_url + "&from=wap&callback=" + encodeURIComponent(callback);
                                    window.location.href = login_url;
                                } else {
                                    //判断用户是否已经绑定手机号
                                    if (!checkUserMobile()) {
                                        $.sDialog({
                                            skin: "red",
                                            content: "请先绑定手机号",
                                            okBtn: true,
                                            okFn: function() {
                                                window.location.href = WapSiteUrl+'/tmpl/member/member_mobile_bind.html'; 
                                            },
                                            cancelBtn: true
                                        });
                                        return false;
                                    }
                                    
                                    var buynum = parseInt($(".buy-num").val()) || 0;
                                    if (buynum < 1) {
                                        $.sDialog({
                                            skin: "red",
                                            content: "参数错误！",
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                        return;
                                    }
                                    
                                    if (buynum > data.goods_info.buyer_limit && data.goods_info.buyer_limit) {
                                        $.sDialog({
                                            skin: "red",
                                            content: "库存不足！",
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                        return;
                                    }
                                    
                                    if (data.shop_owner) {
                                        $.sDialog({
                                            skin: "red",
                                            content: "不能购买自己商店的商品！",
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                        return;
                                    }
                                    if (data.isBuyHave) {
                                        $.sDialog({
                                            skin: "red",
                                            content: "您已达购买上限！",
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                        return;
                                    }
                                    
                                    if (data.buyer_limit > 0 && data.buyer_limit < buynum) {
                                        $.sDialog({
                                            skin: "red",
                                            content: "该商品每人限购" + data.buyer_limit + "件！",
                                            okBtn: false,
                                            cancelBtn: false
                                        });
                                        return;
                                    }
                                    if (data.goods_info.goods_parent_id > 0 && data.goods_info.product_is_behalf_delivery == 1) {
                                        location.href = WapSiteUrl + "/tmpl/order/buy_step2.html?goods_id=" + goods_id + "&goods_num=" + buynum;
                                    } else {
                                        var json = {};
                                        json.key = key;
                                        json.cart_id = goods_id + "|" + buynum;
                                        var seckill_goods_id = data.goods_info.seckill_info.seckill_goods_id;
                                        $.ajax({
                                            url: ApiUrl + "/index.php?ctl=Buyer_Cart&met=addCart&typ=json",
                                            data: {k: key, u: getCookie("id"), goods_id: goods_id, goods_num: buynum, buy_now:1,seckill_goods_id:seckill_goods_id},
                                            type: "post",
                                            success: function (result) {
                                                if (checkLogin(result.login)) {
                                                    if (result.status == 200) {
                                                        // show_tip();
                                                        // 更新购物车中商品数量
                                                        delCookie("cart_count");
                                                        getCartCount();
                                                         if(data.goods_info.promotion_type=='seckill'){
                                                            var seckill_goods_id = data.goods_info.seckill_info.seckill_goods_id;
                                                            location.href = WapSiteUrl + "/tmpl/order/buy_step1.html?ifcart=1&cart_id=" + result.data.cart_id+"&seckill=1&seckill_goods_id="+seckill_goods_id;
                                                        }else{
                                                            location.href = WapSiteUrl + "/tmpl/order/buy_step1.html?ifcart=1&cart_id=" + result.data.cart_id;
                                                        }
                                                        //location.href = WapSiteUrl+'/tmpl/order/buy_step1.html?goods_id='+goods_id+'&buynum='+buynum;
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
                                }
                            });
                        }
                    } else {
                        $.sDialog({
                            content: "该商品已下架或该店铺已关闭！<br>请返回上一页继续操作…",
                            okBtn: false,
                            cancelBtnText: "返回",
                            cancelFn: function () {
                                history.back();
                            }
                        });
                    }
                    var _TimeCountDown = $(".fnTimeCountDown");
                    _TimeCountDown.fnTimeCountDown();
                    //渲染页面的cookie记录
                    var area_id_cookie = getCookie('area_id');
                    var common_id_cookie = getCookie('common_id');
                    var get_area_selected_name_cookie = getCookie('get_area_selected_name');
                    var goods_id_cookie = getCookie('goods_id_cookie');
                    if (goods_id_cookie == getQueryString("goods_id")) {
                        if (area_id_cookie != '' && common_id_cookie != '' && get_area_selected_name_cookie != '') {
                            $("#get_area_selected_name").html(get_area_selected_name_cookie);
                            $.getJSON(ApiUrl + "/index.php?ctl=Goods_Goods&met=getTramsport&typ=json", {
                                common_id: common_id_cookie,
                                area_id: area_id_cookie
                            }, function (result) {
                                $("#get_area_selected_content").html(result.data.transport_str);
                                if (result.data.result != true) {
                                    $("#get_area_selected_whether").html("无货");
                                    $(".buy-handle").addClass("no-buy");
                                    $(".add-cart, .buy-now ").removeClass("animation-up");
                                } else {
                                    if (result.status == 250) {
                                        $("#get_area_selected_whether").html("无货");
                                        $(".buy-handle").addClass("no-buy");
                                        $(".add-cart, .buy-now ").removeClass("animation-up");
                                    } else {
                                        $("#get_area_selected_whether").html("有货");
                                        $(".buy-handle").removeClass("no-buy");
                                        $(".add-cart, .buy-now ").addClass("animation-up");

                                        /* bug 补丁 */
                                        $.animationUp({
                                            valve: ".animation-up",            // 动作触发
                                            wrapper: "#product_detail_spec_html",                   // 动作块
                                            scroll: "#product_roll"     // 滚动块，为空不触发滚动
                                        });
                                        /* bug 补丁 */

                                    }
                                }
                            });
                        }
                    }
                } else {
                    $.sDialog({
                        content: result.msg + "！<br>请返回上一页继续操作…",
                        okBtn: false,
                        cancelBtnText: "返回",
                        cancelFn: function () {
                            history.back();
                        }
                    });
                }
                //分享
                
                var icon = data.goods_one_image;//$('#goods_one_img').val();
                var title = data.goods_info.goods_name;//$("#share_goods_name").val();
                var like = data.share;//$("#share_like").val();
                var desc = data.goods_info.common_name;
                // $("#share_wap").click(function () {
                //     soshm.popIn({
                //         // 分享的链接，默认使用location.href
                //         url:like,
                //         // 分享的标题，默认使用document.title
                //         title:title,
                //         // 分享的摘要，默认使用<meta name="description" content="">content的值
                //         digest: desc,
                //         // 分享的图片，默认获取本页面第一个img元素的src
                //         pic: icon,
                //         sites: ['weixin', 'weixintimeline','weibo', 'qzone', 'qq']
                //     });
                // },false);
                // $("#shares_wap").click(function () {
                //     soshm.popIn({
                //         // 分享的链接，默认使用location.href
                //         url:like,
                //         // 分享的标题，默认使用document.title
                //         title:title,
                //         // 分享的摘要，默认使用<meta name="description" content="">content的值
                //         digest: desc,
                //         // 分享的图片，默认获取本页面第一个img元素的src
                //         pic: icon,
                //         sites: ['weixin', 'weixintimeline', 'weibo', 'qzone',  'qq']
                //     });
                // },false);
                var nativeShare = new NativeShare();
                var shareData = {
                    title: title,
                    desc: desc,
                    // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
                    link: like,
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
                var directseller_flag =data.goods_info.common_is_directseller;
                $("#share_wap").click(function () {
                    if(key){
                        $.ajax({
                            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=checkUserDistributionType&typ=json",
                            data: {k: getCookie("key"), u: getCookie("id")},
                            type: "post",
                            async: false,
                            success: function (result) {
                                if(result.data.type==1 && directseller_flag==1){
                                    if(confirm("分享此商品加入到店铺精选")){
                                     　　$.ajax({
                                            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=addDistributionGoods&typ=json",
                                            data: {k: getCookie("key"), u: getCookie("id"),cid:JSON.stringify([data.goods_info.common_id])},
                                            type: "post",
                                            async: false,
                                            success: function (result) {
                                                console.log(result);
                                            }                               
                                        });
                                    }
                                }
                            }                               
                        });
                    }
                    try {
                        nativeShare.call();
                    } catch (err) {
                        // 如果不支持，你可以在这里做降级处理
                        alert(err.message);
                    }
                });
                $("#shares_wap").click(function () {
                    if(key){
                        $.ajax({
                            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=checkUserDistributionType&typ=json",
                            data: {k: getCookie("key"), u: getCookie("id")},
                            type: "post",
                            async: false,
                            success: function (result) {
                                if(result.data.type==1 && directseller_flag==1){
                                    if(confirm("分享此商品加入到店铺精选")){
                                     　　$.ajax({
                                            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=addDistributionGoods&typ=json",
                                            data: {k: getCookie("key"), u: getCookie("id"),cid:JSON.stringify([data.goods_info.common_id])},
                                            type: "post",
                                            async: false,
                                            success: function (result) {
                                                console.log(result);
                                            }                               
                                        });
                                    }
                                }
                            }                               
                        });
                    }
                    try {
                        nativeShare.call();
                    } catch (err) {
                        // 如果不支持，你可以在这里做降级处理
                        alert(err.message);
                    }
                });
                
                //验证购买数量是不是数字
                $("#buynum").blur(buyNumer);
                //库存为0,提示无货
                $(".add-cart, .buy-now ").click(function () {
                    if ($(".add-cart, .buy-now ").parent().hasClass("no-buy")) {
                        $.sDialog({
                            skin: "red",
                            content: "该商品暂时无货！",
                            okBtn: false,
                            cancelBtn: false
                        });
                    }
                });
                
                // 从下到上动态显示隐藏内容
                $.animationUp({
                    valve: ".animation-up,#goods_spec_selected",            // 动作触发
                    wrapper: "#product_detail_spec_html",                   // 动作块
                    scroll: "#product_roll",                                // 滚动块，为空不触发滚动
                    start: function () {                                    // 开始动作触发事件
                        $(".goods-detail-foot").addClass("hide").removeClass("block");
                        if ($(this).hasClass("add-cart")) {
                            option_window = "add_cart";
                            $("#add-cart").css("width", "100%").show();
                            $("#buy-now").hide();
                        } else if ($(this).hasClass("buy-now")) {
                            option_window = "buy_now";
                            $("#buy-now").css("width", "100%").show();
                            $("#add-cart").hide();
                        }
                    },
                    close: function () {                                    // 关闭动作触发事件
                        option_window = false;
                        $("#add-cart,#buy-now").css("width", "50%").show();
                        $(".goods-detail-foot").removeClass("hide").addClass("block");
                    }
                });
                
                $.animationUp({
                    valve: "#getVoucher",          // 动作触发
                    wrapper: "#voucher_html",    // 动作块
                    scroll: "#voucher_roll"     // 滚动块，为空不触发滚动
                });
                
                $.animationUp({
                    valve: "#for-sale",            // 动作触发
                    wrapper: "#sale-activity-html",                   // 动作块
                    scroll: "#voucher_roll"     // 滚动块，为空不触发滚动
                });
                
                $("#voucher_html").on("click", ".btn", function () {
                    getFreeVoucher($(this).attr("data-tid"));
                });
                
                
                $("#voucher_html").on("click", ".new-btn", function () {
                    $(this).removeClass("up");
                    $(this).add("down");
                });
                
                
                // 联系客服
                /**
                 * YF_IM 联系客服 的点击事件方法
                 *
                 * 传参和跳转页面
                 *
                 */
                $(".kefu").click(function () {
                    // 商品参数:
                    console.log(result.data);
                    //判断不是手机号时使用IM
                    if ($(this).attr("href").indexOf("tel:") == -1) {
                        if (!getCookie("user_account") && getCookie("user_account") == undefined) {
                            if (!getCookie("key")) {
                                $.sDialog({
                                    skin: "red",
                                    content: "您还没有登录",
                                    okBtn: true,
                                    okBtnText: "立即登录",
                                    okFn: function () {
                                        window.location.href = WapSiteUrl + "/tmpl/member/login.html";
                                    },
                                    cancelBtn: true,
                                    cancelBtnText: "取消",
                                    cancelFn: function () {
                                    }
                                });
                                return false;
                            }
                        } else {
                            /*
                            * 需要带过去的参数
                            * 商品的第一张图片 result.data.good_one_image
                            * 商品名称：result.data.goods_info.common_name
                            * 商品价格：当前价格/活动价格 result.data.goods_info.common_price/result.data.goods_info.common_market_price
                            * 商品链接：callback_url
                            * 店铺名称：result.data.store_info.store_name
                            *
                            * */
                            var callback_url = window.location.href,
                                goods_info = result.data,
                                shop_name = goods_info.store_info.store_name,
                                goods_price = goods_info.goods_info.common_price,
                                goods_market = goods_info.goods_info.common_market_price,
                                goods_image = goods_info.goods_one_image,
                                goods_name = goods_info.goods_info.common_name,
                                shop_logo = goods_info.store_info.store_logo,
                                seller_name = goods_info.store_info.member_name;
                            window.location.href = ImApiUrl + "?to_kefu=1&shop_name=" + shop_name + "&callback_url=" + callback_url + "&goods_price=" + goods_price + "&goods_market=" + goods_market + "&goods_image=" + goods_image + "&goods_name=" + goods_name + "&shop_logo=" + shop_logo + "&seller_name=" + seller_name+ "&app_id=" + app_id+ "&app_token=" + app_token;
                        }
                    }
                });
                getGoodsNewReview();
                option_window && initOptionWindow(option_window);
            }
        });
    }
    function setCookie(name,value){
        var Days = 30;
        var exp  = new Date();
        exp.setTime(exp.getTime() + Days*24*60*60*1000);
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
    }

    function getCookie(name){
        var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
        if(arr != null){
            return unescape(arr[2]);
        }else{
            return "";
        }
    }
    $.scrollTransparent();
    $("#product_detail_html").on("click", "#get_area_selected", function () {
        var common_id = $(this).data("common_id");
        var transport_type_id = $(this).data("transport_type_id");
        $.areaSelected({
            hideThirdLevel: true,
            success: function (data) {
                $("#get_area_selected_name").html(data.area_info);
                // var area_id = data.area_id_2 == 0 ? data.area_id_1:data.area_id_3;
                var area_id = data.area_id_3 == 0 ? data.area_id_2:data.area_id_3;
                console.log(area_id);
                setCookie('get_area_selected_name',data.area_info);
                 setCookie('area_id',area_id);
                setCookie('common_id',common_id);
                setCookie('goods_id_cookie',getQueryString("goods_id"));
                $.getJSON(ApiUrl + "/index.php?ctl=Goods_Goods&met=getTramsport&typ=json", {
                    common_id: common_id,
                    area_id: area_id
                }, function (result) {
                    $("#get_area_selected_content").html(result.data.transport_str);
                    if (result.data.result != true) {
                        $("#get_area_selected_whether").html("无货");
                        $(".buy-handle").addClass("no-buy");
                        $(".add-cart, .buy-now ").removeClass("animation-up");
                    } else {
                        if (result.status == 250) {
                            $("#get_area_selected_whether").html("无货");
                            $(".buy-handle").addClass("no-buy");
                            $(".add-cart, .buy-now ").removeClass("animation-up");
                        } else {
                            $("#get_area_selected_whether").html("有货");
                            $(".buy-handle").removeClass("no-buy");
                            $(".add-cart, .buy-now ").addClass("animation-up");
                            
                            /* bug 补丁 */
                            $.animationUp({
                                valve: ".animation-up",            // 动作触发
                                wrapper: "#product_detail_spec_html",                   // 动作块
                                scroll: "#product_roll"     // 滚动块，为空不触发滚动
                            });
                            /* bug 补丁 */
                            
                        }
                    }
                });
            }
        });
    });
    
    $("body").on("click", "#goodsBody,#goodsBody1", function () {
        window.location.href = WapSiteUrl + "/tmpl/product_info.html?goods_id=" + goods_id;
    });
    $("body").on("click", "#goodsEvaluation,#goodsEvaluation1,#reviewLink", function () {
        window.location.href = WapSiteUrl + "/tmpl/product_eval_list.html?goods_id=" + goods_id;
    });
    $("body").on("click", "#goodsRecommendation", function () {
        window.location.href = WapSiteUrl + "/tmpl/product_recommendation.html?goods_id=" + goods_id;
    });
    
    $("body").on("click", "#ziti", function () {
        var key = getCookie("key");//登录标记
        if (!key) {
            //window.location.href = WapSiteUrl+'/tmpl/member/login.html';
            callback = window.location.href;
            login_url = UCenterApiUrl + "?ctl=Login&met=index&typ=e";
            callback = ApiUrl + "?ctl=Login&met=check&typ=e&redirect=" + encodeURIComponent(callback);
            login_url = login_url + "&from=wap&callback=" + encodeURIComponent(callback);
            window.location.href = login_url;
            return false;
        }
        Public.ajaxGet(SiteUrl + "?ctl=Chain_Goods&met=isValidUser&typ=json", {
            k: getCookie("key"),
            u: getCookie("id")
        }, function (e) {
            if (e.data.status == 250) {
                return $.sDialog({
                    skin: "red",
                    content: e.data.msg,
                    okBtn: false,
                    cancelBtn: false
                });
            }
        });
        if (getCookie("id") == goodsInformation.store_info.member_id) {
            return $.sDialog({
                skin: "red",
                content: "不能购买自己商店的商品！",
                okBtn: false,
                cancelBtn: false
            });
        }
        
        var goods_num = parseInt($(".buy-num").val()) || 0;
        $.getJSON(ApiUrl + "/index.php?ctl=Goods_Goods&met=chain&typ=json", {
            goods_id: goods_id, shop_id: shop_id, goods_num: goods_num, k: getCookie("key"),
            u: getCookie("id")
        }, function (result) {
            if (result.status == 200) {
                window.location.href = WapSiteUrl + "/tmpl/ziti.html?goods_id=" + goods_id + "&shop_id=" + shop_id + "&goods_num=1";
            } else {
                $.sDialog({
                    skin: "red",
                    content: result.msg,
                    okBtn: false,
                    cancelBtn: false
                });
                
            }
        });
    });
    $("#list-address-scroll").on("click", "dl > a", map);
    $("#map_all").on("click", map);
});

function show_tip() {
    var flyer = $(".goods-pic > img").clone().css({"z-index": "999", "height": "3rem", "width": "3rem"});
    flyer.fly({
        start: {
            left: $(".goods-pic > img").offset().left,
            top: parseInt($(".goods-pic > img").offset().top - $(window).scrollTop())
        },
        end: {
            left: $("#cart_count").offset().left + 140,
            //top:  $("#cart_count").offset().top - $(window).scrollTop()
            top: 600
        },
        onEnd: function () {
            flyer.remove();
        }
    });
}

function virtual() {
    $("#get_area_selected").parents(".goods-detail-item").remove();
    $.getJSON(ApiUrl + "/index.php?act=goods&op=store_o2o_addr", {shop_id: shop_id}, function (result) {
        if (!result.data.error) {
            if (result.data.addr_list.length > 0) {
                $("#list-address-ul").html(template.render("list-address-script", result.data));
                map_list = result.data.addr_list;
                var _html = "";
                _html += "<dl index_id=\"0\">";
                _html += "<dt>" + map_list[0].name_info + "</dt>";
                _html += "<dd>" + map_list[0].address_info + "</dd>";
                _html += "</dl>";
                _html += "<p><a href=\"tel:" + map_list[0].phone_info + "\"></a></p>";
                $("#goods-detail-o2o").html(_html);
                $("#goods-detail-o2o").on("click", "dl", map);
                if (map_list.length > 1) {
                    $("#store_addr_list").html("查看全部" + map_list.length + "家分店地址");
                } else {
                    $("#store_addr_list").html("查看商家地址");
                }
                $("#map_all > em").html(map_list.length);
            } else {
                $(".goods-detail-o2o").hide();
            }
        }
    });
    $.animationLeft({
        valve: "#store_addr_list",
        wrapper: "#list-address-wrapper",
        scroll: "#list-address-scroll"
    });
}

function map() {
    $("#map-wrappers").removeClass("hide").removeClass("right").addClass("left");
    $("#map-wrappers").on("click", ".header-l > a", function () {
        $("#map-wrappers").addClass("right").removeClass("left");
    });
    $("#baidu_map").css("width", document.body.clientWidth);
    $("#baidu_map").css("height", document.body.clientHeight);
    map_index_id = $(this).attr("index_id");
    if (typeof map_index_id != "string") {
        map_index_id = "";
    }
    if (typeof(map_js_flag) == "undefined") {
        $.ajax({
            url: WapSiteUrl + "/js/map.js",
            dataType: "script",
            async: false
        });
    }
    if (typeof BMap == "object") {
        baidu_init();
    } else {
        load_script();
    }
}

//2017-07-12相关需求加入
function getGoodsNewReview() {
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Goods_Goods&met=getGoodsNewReview&typ=json&sort=scores",
        type: "POST",
        data: {
            k: getCookie('key'),
            u: getCookie('id'),
            goods_id: goods_id
        },
        dataType: "json",
        success: function (result) {
            if (result.status == 200) {
                var goodsReviewHtml = template.render('goodsReview', result.data);
                $("#s-rate").append(goodsReviewHtml);
            }
        }
    });
}

function initOptionWindow(option_type) {
    if (option_type == "buy_now") {
        $("#buy-now").css("width", "100%").show();
        $("#add-cart").hide();
    } else {
        $("#add-cart").css("width", "100%").show();
        $("#buy-now").hide();
    }
}

$(document).on("click", ".goods_geval a", function () {
    var start = $(this).data("start");
    var o = $(this).parents(".goods_geval");
    o.find(".nctouch-bigimg-layout").removeClass("hide");
    var i = o.find(".pic-box");
    o.find(".close").click(function () {
        o.find(".nctouch-bigimg-layout").addClass("hide");
    });
    
    if (i.find("li").length < 2) {
        return;
    }
    
    Swipe(i[0], {
        startSlide: start,
        speed: 400,
        auto: 3e3,
        continuous: false,
        disableScroll: false,
        stopPropagation: false,
        callback: function (o, i) {
            $(i).parents(".nctouch-bigimg-layout").find("div").last().find("li").eq(o).addClass("cur").siblings().removeClass("cur");
        },
        transitionEnd: function (o, i) {
        }
    });
    
    
});


$("#sale-activity-html").on("click", ".new-btn", function () {
    $("#sale-activity-html").removeClass("up");
    $("#sale-activity-html").addClass("down");
});
$("#voucher_html").on("click", ".new-btn", function () {
    $("#voucher_html").removeClass("up");
    $("#voucher_html").addClass("down");
});

var pos = getQueryString("pos");
addCookie("goods_pos", pos);

var prevurl,pevdomain;
if(document.referrer !=""){
   prevurl=document.referrer; 
}else{
    prevurl=WapSiteUrl;
}
pevdomain= prevurl.split("/"); //以“/”进行分割
if( pevdomain[2] ) {
    pevdomain = pevdomain[2];
} else {
    pevdomain = ''; //如果url不正确就取空
}
var currentdomain = window.location.host;
if(currentdomain ==pevdomain){
    sessionStorage.setItem('preUrl',prevurl);
}
var preUrlget=sessionStorage.getItem("preUrl");
function backGoodsList() {
    window.location.href=preUrlget;    
}
$(function(){
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
     $.ajax({
            url: ApiUrl + "/index.php?ctl=Goods_Goods&met=goods&typ=json",
            type: "get",
            data: { goods_id: goods_id,
                    k: getCookie('key'),
                    u: getCookie('id')
            },
            dataType: "json",
            success: function (result) {
                if (result.status == 200) {
                    var html = template.render('productRecommendation', result.data);
                                    contentList['recommend']='true';
                    $("#recommendation").append(html);
                } else {
                    $.sDialog({skin: "red", content: "<?= __('网络异常'); ?>", okBtn: false, cancelBtn: false});
                }
            }
        });
})