var img = '';
var qrCode = '';
var uuid = getQueryString('uuid');
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

    //获取合成背景图片
    //$("#share_bgimg_div_idshare").html("<div style='text-align:center;margin-top:200px;'>正在合成专属推广图片，请稍等！</div>");

    $.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?ctl=Goods_Goods&met=composite_user_pic&typ=json",
        data: {k: key, u: getCookie('id'), uuid: uuid},
        dataType: 'json',
        success: function (result) {
            if (result.status == 200 && result.data.composite_pic) {
                //背景图片
                $("#show_set_msg_div").css("display","none");
                $("#share_bgimg_div_idshare").html("<img src='"+result.data.composite_pic+"' width='100%' height='100%'/>");
            }else{
                $("#show_set_msg_div").css("display","block");
                get_comm_set();
                get_user_qr_img();
            }
        }
    });
    //展示头像，描述语，以及背景设置
    function get_comm_set(){
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?ctl=Goods_Goods&met=getMycodeSet&typ=json",
            data: {k: key, u: getCookie('id'), uuid: uuid},
            dataType: 'json',
            success: function (result) {
                if (result.status == 200) {
                    //背景图片
                    var s = result.data.img;//row是table的当前行
                    $("#share_bgimg_div_idshare").css("background-image","url("+s+")").
                    css("background-position","center center").css("background-repeat","no-repeat").css("background-size","cover");
                    //微信昵称显示
                    $("#my_code_wx_name").text("我是"+result.data.wxName);
                    //my_code_txt
                    $("#my_code_txt").text(result.data.txt);
                    //my_code_wx_logo
                    $("#my_code_wx_logo").attr('src',result.data.wxlogo);
                }
            }
        });
    }

    function get_user_qr_img(){
        $.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?ctl=Goods_Goods&met=shareWap&typ=json",
            data: {k: key, u: getCookie('id'),uuid:uuid},
            dataType: 'json',
            success: function (result) {
                if(result.status == 200)
                {
                    $("#shareCode").find('img').attr('src',result.data.qrCode);
                    img = result.data.img;
                    var config = {
                        url:ApiUrl + "/index.php?ctl=Goods_Goods&met=location_register&typ=e&uuid="+getCookie('id'),// 分享的网页链接
                        title:'我的二维码分享',// 标题
                        desc:'我的二维码',// 描述
                        img:img,// 图片
                        img_title:'二维码',// 图片标题
                        from:'bbcBuilder' // 来源
                    };
                    qrCode = result.data.qrCode;
                    var share_obj = new nativeShare('nativeShare',config);
                }
            }
        });
    }


    /*$.ajax({
        type: 'post',
        url: ApiUrl + "/index.php?ctl=Goods_Goods&met=shareWap&typ=json",
        data: {k: key, u: getCookie('id'), uuid: uuid},
        dataType: 'json',
        success: function (result) {
            if (result.status == 200) {
                $("#shareCode").find('img').attr('src', result.data.qrCode);
                img = result.data.img;
                // var config = {
                //     url:ApiUrl + "/index.php?ctl=Goods_Goods&met=location_register&typ=e&uuid="+getCookie('id'),// 分享的网页链接
                //     title:'我的二维码分享',// 标题
                //     desc:'我的二维码',// 描述
                //     img:img,// 图片
                //     img_title:'二维码',// 图片标题
                //     from:'bbcBuilder' // 来源
                // };
                // qrCode = result.data.qrCode;
                // var share_obj = new nativeShare('nativeShare',config);

                var link = ApiUrl + "/index.php?ctl=Goods_Goods&met=location_register&typ=e&uuid=" + getCookie('id');// 分享的网页链接
                var title = '我的二维码分享';// 标题
                var desc = '我的二维码';// 描述
                var icon = img;
                var nativeShare = new NativeShare();
                var shareData = {
                    title: title,
                    desc: desc,
                    // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
                    link: link,
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
                $(document).on('click', '.share_wap', function () {
                    try {
                        nativeShare.call();
                    } catch (err) {
                        // 如果不支持，你可以在这里做降级处理
                        alert(err.message);
                    }
                });
            }
        }
    });*/

    //app分享
    var u = navigator.userAgent;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    $('.share_app').on('click', function () {
        var share_code = [];//配合ios数据格式
        var type = $(this).data('share_type');
        share_code.push(type);
        if (isAndroid) {
            android.typeShare(type, qrCode);
        }
        if (isiOS) {
            share_code.push(qrCode);
            window.webkit.messageHandlers.PanelShare.postMessage(share_code);
        }
    });

    //滚动header固定到顶部
    $.scrollTransparent();
});