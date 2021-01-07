$(function () {
    var k = getCookie("key");
    var u = getCookie("id");
    var bargain_id = '';
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }

    jQuery("#re_user_mobile").intlTelInput({
        utilsScript: "../../js/utils.js"
    });

    var datas = {
        user_id: u,
        is_list: 1
    };
    getWapBargainList(datas);

    // 砍价活动列表
    function getWapBargainList(data,from = 0) {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Bargain_UnBargain&met=getWapBargainList&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    if (res.data.bargain_status == 1){
                        if (from != 1){
                            $("#progress").removeClass('hide');
                        }
                        var r = template.render("bargain-list-tmpl", res.data);
                        $(".bargain-goods-lists").html(r);
                        var _TimeCountDown = $(".fnTimeCountDown");
                        _TimeCountDown.fnTimeCountDown();
                    } else{
                        $(".bargain_is_on").addClass('hide');
                        $("#bargain_status").removeClass('hide');
                    }
                }
            }
        });
    }

    //关闭地址弹框
    $(".icon-close").click(function () {
        $("#bargain-address-alert-html").removeClass('up').addClass('down');
    })

    //去砍价
    $(document).on('click','.js-bargain-go',function () {
        bargain_id = $(this).data('bargain_id');
        // 收货地址弹框
        if (u) {
            //判断是否为商家自己发起砍价、是否发起过砍价
            $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Bargain_Bargain&met=checkSelf&typ=json",
                data: {k: k, u: u, bargain_id:bargain_id},
                dataType: "json",
                success: function (res) {
                    if (res.status == 200) {
                        $(".info").removeClass('hide');
                        $(".dialog-info").html(res.msg);
                        setTimeout(function () {
                            $(".info").addClass('hide');
                        },3000);
                    }else{
                        // 收货地址弹框
                        $("#bargain-address-alert-html").removeClass('down').addClass('up');
                        //当前用户地址列表
                        getAddressList();
                    }
                }
            });

        } else {
            $(".login-alert-tips").removeClass('hide');
        }
    });

    //当前用户地址列表
    function getAddressList()
    {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Buyer_User&met=address&typ=json",
            data: {k:k, u:u},
            dataType: "json",
            success: function (res) {
                if (res.status == 200) {
                    var r = template.render("bargain-address-lists-tmpl", res.data);
                    $(".bargain-address-lists").html(r);
                }
            }
        });
    }

    //我的砍价
    $(".myBargain").click(function () {
        $(this).addClass('active').prev().removeClass('active');
        if (u) {
            var data = {
                user_id: u,
                is_list: 0
            };
            var from = 1;
            $("#progress").addClass('hide');
            $(".tit").html('我的砍价');
            getWapBargainList(data, from);
        } else {
            $(".login-alert-tips").removeClass('hide');
        }
    });

    //砍价商品
    $(".bargain").click(function () {
        $(this).addClass('active').next().removeClass('active');
        $("#progress").removeClass('hide');
        $(".tit").html('砍价专区');
        var data = {
            user_id: u,
            is_list: 1
        };
        getWapBargainList(data);
    });

    //新增地址
    $(".bargain-address-add").click(function () {
        $("#bargain").addClass('hide');
        $("#add_address").removeClass('hide');
        $("#bargain-address-alert-html").addClass('down').removeClass('up');
    });

    //新增地址 - 返回icon
    $("#hide_address").click(function () {
        $("#bargain").removeClass('hide');
        $("#add_address").addClass('hide');
    });

    //地址验证
    Zepto.sValid.init({
        rules: {true_name: "required", mob_phone: "required", area_info: "required", address: "required"},
        messages: {true_name: "姓名必填！", mob_phone: "手机号必填！", area_info: "地区必填！", address: "街道必填！"},
        callback: function (a, e, r) {
            if (a.length > 0) {
                var i = "";
                Zepto.map(e, function (a, e) {
                    i += "<p>" + a + "</p>"
                });
                errorTipsShow(i)
            }
            else {
                errorTipsHide()
            }
        }
    });
    Zepto("#header-nav").click(function () {
        Zepto(".btn").click()
    });
    Zepto(".btn").click(function () {

        var e = Zepto("#true_name").val();
        var r = Zepto("#re_user_mobile").val();
        var area_code = Zepto('#area_code').val();
        var i = Zepto("#address").val();
        var d = Zepto("#area_info").attr("data-areaid2");
        var t = Zepto("#area_info").attr("data-areaid");
        var n = Zepto("#area_info").val();
        var o = Zepto("#is_default").attr("checked") ? 1 : 0;

        var province_id = Zepto("#area_info").attr("data-areaid1");
        var city_id = Zepto("#area_info").attr("data-areaid2");
        var area_id = Zepto("#area_info").attr("data-areaid3");
        if (e.length > 20 || e.length < 2) {
            errorTipsShow("<p>收货人姓名为2~20个字符</p>");
            return false;
        }
        if (!(/^1[345678]\d{9}$/.test(r)) && area_code == 86) {
            errorTipsShow("<p>手机号码有误，请重填</p>");
            return false;
        }
        if (i.length > 100) {
            errorTipsShow("<p>详细地址不能超过100个字符</p>");
            return false;
        }

        Zepto.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Buyer_User&met=addAddressInfo&typ=json",
            data: {k: getCookie('key'), u: getCookie('id'), user_address_contact: e, user_address_phone: r, province_id: province_id, city_id: city_id, area_id: area_id, user_address_address: i, address_area: n, user_address_default: o},

            dataType: "json",
            success: function (a) {
                if (a.status == 200) {
                    var data = a.data;
                    $("#bargain").removeClass('hide');
                    $("#add_address").addClass('hide');

                    //保存地址
                    // $("#bargain-address-alert-html").removeClass('down').addClass('up');
                    // var str = "<li class='choose_address' data-address_id='" + data.user_address_id + "'>" +
                    //     " <a class='block' href='javascript:;'>" +
                    //     "  <div>" +
                    //     "    <p><span class='mwp50 bargain-address-receiver'>" + data.user_address_contact + "</span><em class='mwp50'>" + data.user_address_phone + "</em></p>" +
                    //     "    <p><span class='one-overflow'> " + data.user_address_area + data.user_address_address + "</span></p>" +
                    //     "  </div>" +
                    //     " </a>" +
                    //     "</li>";
                    // $(".bargain-address-lists").append(str);

                    var bargain_data = {
                        k: k,
                        u: u,
                        user_id: u,
                        bargain_id: bargain_id,
                        address_id: data.user_address_id
                    };
                    var address_info = data.user_address_contact + "，" + data.user_address_phone + "，" + data.user_address_area + " " + data.user_address_address;
                    sureAddress(bargain_data, address_info);
                }
                else {
                    $(".info").removeClass('hide');
                    $(".dialog-info").html('保存失败');
                    setTimeout(function () {
                        $(".info").addClass('hide');
                    }, 3000);
                }
            }
        })

    });
    Zepto("#area_info").on("click", function () {
        Zepto.areaSelected({
            success: function (a) {
                Zepto("#area_info").val(a.area_info).attr({"data-areaid1": a.area_id_1, "data-areaid2": a.area_id_2, "data-areaid3": a.area_id_3, "data-areaid": a.area_id, "data-areaid2": a.area_id_2 == 0 ? a.area_id_1 : a.area_id_2})
            }
        })
    });

    //选择地址  - 去砍价
    $(document).on('click','.choose_address',function () {
        var address_id = $(this).data('address_id');
        var address_info = $(this).data('address_info');
        var data = {
            k: k,
            u: u,
            user_id:u,
            bargain_id:bargain_id,
            address_id:address_id
        };
        sureAddress(data, address_info);
    });

    function sureAddress(data,address_info)
    {
        $(".address").removeClass('hide');
        $(".dialog-address").html(address_info);
        $(".s-dialog-btn-ok").click(function () {
            $(".address").addClass('hide');
            addCookie('userInitiate', 1);
            InitiateBargain(data);
        });
        $(".s-dialog-btn-cancel").click(function () {
            $(".address").addClass('hide');
            $(".nctouch-bottom-mask").removeClass('up').addClass('down');
        });
    }

    function InitiateBargain(data)
    {
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Bargain_Bargain&met=InitiateBargain&typ=json",
            data: data,
            dataType: "json",
            success: function (res) {
                $(".nctouch-bottom-mask").removeClass('up').addClass('down');
                if (res.status == 200) {
                    addCookie('InitiatePrice', res.data.bargain_price);
                    location.href = WapSiteUrl + "/tmpl/bargain_detail.html?buy_id=" + res.data.buy_id;
                }else{
                    $(".info").removeClass('hide');
                    $(".dialog-info").html(res.msg);
                    setTimeout(function () {
                        $(".info").addClass('hide');
                    }, 3000);
                }
            }
        });
    }

});

