$(function () {
    //我的二维码
    $("#my_code").click(function(){
        if(getCookie('id'))
        {
            window.location.href = WapSiteUrl + '/tmpl/member/share.html?uuid='+getCookie('id');
        }else{
            $.sDialog({
                content: '请登录！',
                okBtn: false,
                cancelBtnText: '返回',
                cancelFn: function () { /*history.back();*/
                }
            });
        }
    });
});

$(function () {
    //我的小店
    $(document).on('click','#distribution_shop',function () {    
        if(getCookie('id')){
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=checkUserDistributionType&typ=json",
                data: {k: getCookie('key'),u: getCookie('id')},
                dataType: "json",
                success: function (r) {
                    if(r.data.type==1){
                        window.location.href = "../../tmpl/member/distribution_shop.html"
                    }else{
                        // $.sDialog({
                        //     content: "您还未成为分销掌柜，没有分销小店哦！\r\n1.购买升级礼包成为分销掌柜;\r\n2.邀请"+r.data.num+"名会员成为分销掌柜。",
                        //     okBtn: false,
                        //     cancelBtnText: '返回',
                        //     cancelFn: function () { /*history.back();*/
                        //     }
                        // });
                        alert("您还未成为分销掌柜，没有分销小店哦！\r\n1.购买升级礼包成为分销掌柜;\r\n2.邀请"+r.data.num+"名会员成为分销掌柜。");
                    }
                }
            });
        }else{
            alert("请登录！");
        }
    });
    //我的店铺海报
    $(document).on('click','#distribution_code',function () {
        if(getCookie('id')){
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=checkUserDistributionType&typ=json",
                data: {k: getCookie('key'),u: getCookie('id')},
                dataType: "json",
                success: function (r) {
                    if(r.data.type==1){
                        window.location.href = "../../tmpl/member/distribution_shop_share.html?uuid="+getCookie('id');
                    }else{
                        // $.sDialog({
                        //     content: "您还未成为分销掌柜，没有分销小店哦！\r\n1.购买升级礼包成为分销掌柜;\r\n2.邀请"+r.data.num+"名会员成为分销掌柜。",
                        //     okBtn: false,
                        //     cancelBtnText: '确定',
                        //     cancelFn: function () { /*history.back();*/
                        //     }
                        // });
                        
                        alert("您还未成为分销掌柜，没有分销小店哦！\r\n1.购买升级礼包成为分销掌柜;\r\n2.邀请"+r.data.num+"名会员成为分销掌柜。");
                    }
                }
            });
        }else{
            alert("请登录！");
        }
    });
});

$("div").delegate(".sign", "click", function(){
    var key =  getCookie('key');
    $.ajax({
        type:'post',
        url: ApiUrl + "/index.php?ctl=Buyer_User&met=userInfoSign&typ=json",
        data: {k: key, u: getCookie('id')},
        dataType: 'json',
        success: function (result) {
            if (result.data.sign_satus == 1) {
                $(".sign").children().find(".middle").html("已签到");
            } else {
                $(".sign").children().find(".middle").html("签到");
            }
        }
    });
});



$(function () {
    if (getQueryString('key') != '') {
        var key = getQueryString('key');
        var username = getQueryString('username');
        addCookie('key', key);
        addCookie('username', username);
        updateCookieCart(key);
    } else {
        var key = getCookie('key');
    }
    var qrCode = '';
    var share_icon = '';

    if (key) {
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?ctl=Buyer_User&met=getUserInfo&typ=json",
            data: {k: key, u: getCookie('id')},
            dataType: 'json',
            //jsonp:'callback',
            success: function (result) {
                if (result.status == 250) {
                    return false;
                }
                checkLogin(result.login);
                if (typeof(result.data.money.user_money) == 'undefined') {
                    $('#user_money').html("￥0");
                } else {
                    $('#user_money').html("￥" + result.data.money.user_money);
                }
                if (typeof(result.data.points.user_points) == 'undefined') {
                    $('#user_points').html('0');
                } else {
                    $('#user_points').html(result.data.points.user_points);
                }
   console.log(result.data.sign_satus)
                var html = '<div class="mine-head-bg"><div class="member-info">'
                    + '<div class="user-avatar"> <a href=" ' + UCenterApiUrl + '"><img src="' + result.data.user_logo + '"/> </a></div>'
                    + '<div class="user-name ml-20 fz0"> <span><strong>' + result.data.user_name + '</strong>'
                    + '<sup>lv' + result.data.user_grade + '</sup></span>';

                    //未开通原样显示
                    if (!result.data.Plusday || result.data.user.user_status==3 || result.data.open_status==0){
                        html += '</div></div></div>'
                             + '<div class="sign"><a href="javascript:void(0);"><i class="goods-sign"></i><b class="middle">'+result.data.sign_satus+'</b></a></div>';
                    }
                    //试用
                    if (result.data.user.user_status ==1 && result.data.open_status==1) {
                      html += '<b class="plus-user-log"></b><i class="plus-member-try">试用</i>'
                            + '<em class="block plus-try-time">'+result.data.Plusday+'天后试用到期</em>'
                            + '</div></div></div>'
                            + '<div class="sign"><a herf="javascript:void(0);"><b class="middle">'+result.data.sign_satus+'</b></a>'
                            + '</div><a href="plus_open.html" class="plus-open-enterance">开通正式会员</a>';
                    }
                    //window.location.href = WapSiteUrl + "/tmpl/member/login.html"
                    //购买
                    if (result.data.user.user_status==2 && result.data.open_status==1) {
                      html +='<b class="plus-user-log active"></b><i class="plus-member-try active">试用</i>'
                           + '<em class="block plus-try-time">'+ result.data.Plusday +'到期</em>'
                           + '</div></div></div>'
                           + '<div class="sign"><a href="javascript:void(0);"><b class="middle">'+ result.data.sign_satus +'</b></a>'
                           + '</div><a href="plus_open.html" class="plus-open-enterance">立即续费</a>'; 
                    }
                    //过期
                    // if (result.data.user.user_status==3) {
                    //   html +='<b class="plus-user-log active"></b><i class="plus-member-try active"></i>'
                    //        + '<em class="block plus-try-time">'+ result.data.Plusday +'</em>'
                    //        + '</div></div></div>'
                    //        + '<div class="sign"><a href="signin.html"><b class="middle">已签到</b></a>'
                    //        + '</div><a href="plus_open.html" class="plus-open-enterance">立即开通</a>'; 
                    // }
                    // 正式会员给.plus-open-enterance的内容改为立即续费
                    if(result.data.distributor_type == 1){
					  html+='<div class="manager-signs"><i class="iconfont icon-zuanshi"></i><em>掌柜</em></div>';
                    }
                //渲染页面

                $(".member-top").html(html);
                if (result.data.find_type == 0) {
                    var html = '<span><a href="favorites_store.html"><em>' + result.data.favorites_shop_num + '</em>'
                        + '<p>我的收藏</p>'
                        + '</a> </span><span><a href="views_list.html"><em>' + result.data.footprint_goods_num + '</em>'
                        + '<p>我的足迹</p>'
                        + '</a> </span>';
                } else {
                    var shop_id_wap = getCookie('SHOP_ID_WAP');
                    var html = '<span><a href="favorites_store.html"><em>' + result.data.favorites_shop_num + '</em>'
                        + '<p>我的收藏</p>'
                        + '</a> </span><span><a href="views_list.html"><em>' + result.data.footprint_goods_num + '</em>'
                        + '<p>我的足迹</p>'
                        + '</a> </span><span><a href="../explore_center.html?from=center"><em>' + result.data.explore_base_count + '</em>'
                        + '<p>我的心得</p>'
                        + '</a> </span>';
                    if(shop_id_wap){
                         var html = '<span><a href="favorites_store.html"><em>' + result.data.favorites_shop_num + '</em>'
                        + '<p>我的收藏</p>'
                        + '</a> </span><span><a href="views_list.html"><em>' + result.data.footprint_goods_num + '</em>'
                        + '<p>我的足迹</p>'
                        +'</a> </span>';
                    }
                }

                $(".member-collect").html(html);
                var html = '<li><a href="order_list.html?data-state=wait_pay"><i class="iconfont icon-daifukuan"></i><p>待付款</p></a>' + (result.data.order_count.wait > 0 ? '<b>' + result.data.order_count.wait + '</b>' : '') + '</li>'
                    + '<li><a href="order_list.html?data-state=order_payed"><i class="iconfont icon-daifahuo"></i><p>待发货</p></a>' + (result.data.order_count.payed > 0 ? '<b>' + result.data.order_count.payed + '</b>' : '') + '</li>'
                    + '<li><a href="order_list.html?data-state=wait_confirm_goods"><i class="iconfont icon-daishouhuo"></i><p>待收货</p></a>' + (result.data.order_count.confirm > 0 ? '<b>' + result.data.order_count.confirm + '</b>' : '') + '</li>'
                    + '<li><a href="order_list.html?data-state=finish"><i class="iconfont icon-daipingjia"></i><p>待评价</p></a>' + (result.data.order_count.finish > 0 ? '<b>' + result.data.order_count.finish + '</b>' : '') + '</li>'
                    + '<li><a href="member_refund.html"><i class="iconfont icon-tuikuan"></i><p>退款/退货</p></a>' + (result.data.order_count.return > 0 ? '<b>' + result.data.order_count.return + '</b>' : '') + '</li>';
                //渲染页面

                $("#order_ul").html(html);
                if (result.data.directseller_is_open > 0) {
                    var html = '<li>' +
                        '<a id="distribution" href="distribution.html">' +
                        '<i class="i-fenxiao"></i><span class="block">分销中心</span>' +
                        '</a>' +
                        '</li>';
                    $(".member-nav-setting").before(html);

                    var dian = '<li>'+
                        '<a id="distribution_shop"><i class="i-xiaodian"></i><span class="block">我的小店</span></a>'+
                        '</li>';
                    $(".member-nav-property").before(dian);
                    
                    var wei = '<li>'+
                        '<a id="distribution_code"><i class="i-erweima"></i><span class="block">店铺推广海报</span></a>'+
                        '</li>';
                    $(".member-nav-wei").before(wei); 

                    var ad = '<a href="distribution_package.html"><img src="../../images/icons/Group.png"></a>';
                    $(".member-interaction").html(ad);      
                }
                if (result.data.fenxiao_is_open > 0) {
                    var html = '<li>' +
                        '<a id="distribution" href="fenxiao.html">' +
                        '<i class="i-fenxiao"></i><span class="block">分销中心</span>' +
                        '</a>' +
                        '</li>';
                    $(".member-nav-setting").before(html);
                }
                // 应产品专员（晓丹）要求先去除分销明细
                // if (result.data.shop_type == 1 && result.data.distribution_is_open == 1 && !result.data.fenxiao_is_open) {
                //     var html = '<li>' +
                //         '<a id="distribution" href="distlog.html">' +
                //         '<i class="i-fenxiao"></i><span>分销明细</span>' +
                //         '</a>' +
                //         '</li>';
                //     $(".member-nav-items").append(html);
                // }
                qrCode = result.data.qrCode;
                share_icon = result.data.share_icon;
                return false;
            }
        });
    } else {
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?ctl=Index&met=getUserLogo&typ=json",
            data: {},
            dataType: 'json',
            //jsonp:'callback',
            success: function (result) {
                if (result.data.image){
                    var image = result.data.image;
                } else{
                    var image = '../../images/new/default-img.png';
                }
                var html = '<div class="mine-head-bg"><div class="member-info">'

                + '<a class="user-avatar logbtn" href="javascript:void(0);"><img src="'+ image + '"></a>'
                + '<a class="logbtn user-name ml-20 user-name-login" href="javascript:void(0);"><span>点击登录</span></a>'
                + '</div></div>';
                //渲染页面
                $(".member-top").html(html);

                var shop_id_wap = getCookie('SHOP_ID_WAP');
                var html = '<div class="member-collect"><span><a class="logbtn" href="javascript:void(0);"><em>0</em>'
                    + '<p>我的收藏</p>'
                    + '</a> </span><span><a class="logbtn" href="javascript:void(0);"><em>0</em>'
                    + '<p>我的足迹</p>'
                    + '</a> </span><span><a class="logbtn" href="javascript:void(0);"><em>0</em>'
                    + '<p>我的心得</p>'
                    + '</a> </span></div>';
                if(shop_id_wap){
                        var html = '<div class="member-collect"><span><a class="logbtn" href="javascript:void(0);"><em>0</em>'
                        + '<p>我的收藏</p>'
                        + '</a> </span><span><a class="logbtn" href="javascript:void(0);"><em>0</em>'
                        + '<p>我的足迹</p>'
                        + '</a> </span>'
                        + '</div>';
                }
                $(".member-collect").html(html);
                var html = '<li><a class="logbtn"><i class="iconfont icon-daifukuan"></i><p>待付款</p></a></li>'
                    + '<li><a class="logbtn"><i class="iconfont icon-daifahuo"></i><p>待发货</p></a></li>'
                    + '<li><a class="logbtn"><i class="iconfont icon-daishouhuo"></i><p>待收货</p></a></li>'
                    + '<li><a class="logbtn"><i class="iconfont icon-daipingjia"></i><p>待评价</p></a></li>'
                    + '<li><a class="logbtn"><i class="iconfont icon-tuikuan"></i><p>退款/退货</p></a></li>';
                //渲染页面
                $("#order_ul").html(html);
                return false;
            }
        });
    }

    //分享
    if (getCookie('id')) {
        var icon = share_icon;
        var title = '我的二维码分享';
        var like = ApiUrl + "/index.php?ctl=Buyer_User&met=location_register&typ=e&uuid=" + getCookie('id');
        var desc = '我的二维码';
        var nativeShare = new NativeShare();
        var shareData = {
            title: title,
            desc: desc,
            // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
            link: like,
            icon: icon,
            // 不要过于依赖以下两个回调，很多浏览器是不支持的
            success: function () {
                alert('success')
            },
            fail: function () {
                alert('fail')
            }
        }
        nativeShare.setShareData(shareData);
        $("#share").click(function () {
            try {
                nativeShare.call();
            } catch (err) {
                // 如果不支持，你可以在这里做降级处理
                alert(err.message)
            }
        });

        // function setTitle(title) {
        //     nativeShare.setShareData({
        //         title: title,
        //     })
        // }
        //app分享
        var u = navigator.userAgent;
        var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
        var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        $(document).on('click','#share_app',function(){
            if(isAndroid)
            {
                android.share(qrCode);
            }
            if(isiOS)
            {
                window.webkit.messageHandlers.Share.postMessage(qrCode);
            }
        })
    }

    //滚动header固定到顶部
    // $.scrollTransparent();

    $("#paycenter,.paycenter").click(function () {
        window.location.href = PayCenterWapUrl;
    });


});