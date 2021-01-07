var key = getCookie("key");
var u = getCookie("id");
var goods_id = getQueryString("goods_id");
var quantity = getQueryString("quantity");
var data = {};
data.k = key;
data.u = u;
data.goods_id = goods_id;
data.nums = quantity;

Number.prototype.toFixed = function (e) {
    var t = this + "";
    if (!e) {
        e = 0;
    }
    if (t.indexOf(".") == -1) {
        t += ".";
    }
    t += new Array(e + 1).join("0");
    if (new RegExp("^(-|\\+)?(\\d+(\\.\\d{0," + (e + 1) + "})?)\\d*$").test(t)) {
        var t = "0" + RegExp.$2, a = RegExp.$1, r = RegExp.$3.length, o = true;
        if (r == e + 2) {
            r = t.match(/\d/g);
            if (parseInt(r[r.length - 1]) > 4) {
                for (var n = r.length - 2; n >= 0; n--) {
                    r[n] = parseInt(r[n]) + 1;
                    if (r[n] == 10) {
                        r[n] = 0;
                        o = n != 1;
                    }
                    else {
                        break;
                    }
                }
            }
            t = r.join("").replace(new RegExp("(\\d+)(\\d{" + e + "})\\d$"), "$1.$2");
        }
        if (o) {
            t = t.substr(1);
        }
        return (a + t).replace(/\.$/, "");
    }
    return this + "";
};
var p2f = function (e) {
    return (parseFloat(e) || 0).toFixed(2);
};
Zepto(function () {
    Zepto.ajax({
        type: "post",
        url: ApiUrl + "/index.php?ctl=Buyer_Cart&met=confirmVirtual&typ=json",
        dataType: "json",
        data: data,
        success: function (e) {
            console.info(e);
            var t = e.data;
            if (typeof t.error != "undefined") {
                location.href = WapSiteUrl;
                return;
            }
            t.WapSiteUrl = WapSiteUrl;
            var a = template.render("goods_list", t);
            Zepto("#deposit").html(a);
            Zepto("#totalPrice").html(Number(t.goods_base.sumprice).toFixed(2));
            if (t.user_rate > 0) {
                var payprice = parseFloat(t.goods_base.sumprice * (t.user_rate / 100));
            }
            else {
                var payprice = t.goods_base.sumprice;
            }
            Zepto("#totalPayPrice").html(payprice);
            jQuery("#re_user_mobile").intlTelInput({
                utilsScript: "../../js/utils.js"
            });
        }
    });
    //会员折扣是否开启
    Zepto(document).on("click", "input[name='is_discount']", function () {
        var storeTotal = Zepto("#storeTotal").data("storetotal");
        var totalPrice = Zepto("#totalPrice").html();
        var discount_price = Zepto("#discount_text").data("discount_price");
        if (Zepto(this).is(":checked")) {
            Zepto("#discount_text").show();
            Zepto("#storeTotal").html("￥" + (Number(storeTotal) - Number(discount_price)).toFixed(2));
            Zepto("#totalPrice").html((Number(storeTotal) - Number(discount_price)).toFixed(2));
        }
        else {
            Zepto("#discount_text").hide();
            Zepto("#storeTotal").html("￥" + Number(storeTotal).toFixed(2));
            Zepto("#totalPrice").html(Number(storeTotal).toFixed(2));
        }
    });
    Zepto("#ToBuyStep2").click(function () {
        var has_physical = Zepto("#has_physical").val();
        var storeMessage = Zepto("#storeMessage").val();
        if (typeof(has_physical) != "undefined" && has_physical == 1) {
            if (storeMessage == "") {
                Zepto.sDialog({skin: "red", content: "请填写收货人信息", okBtn: false, cancelBtn: false});
                Zepto("#storeMessage").focus();
                return false;
            }
        }
        var e = {};
        e.k = key;
        e.u = u;
        //商品信息
        e.goods_id = goods_id;
        e.goods_num = quantity;
        e.pay_way_id = 1;
        e.from = "wap";
        //是否开启会员折扣
        e.is_discount = Zepto("input[name='is_discount']").is(":checked") ? 1 : 0;
        var t = Zepto("#re_user_mobile").val();
        var area_code = Zepto("#area_code").val();
        if (!/^1[345678]\d{9}$/.test(t) && area_code == 86) {
            Zepto.sDialog({skin: "red", content: "请正确输入接收手机号码！", okBtn: false, cancelBtn: false});
            return false;
        }
        //手机号
        e.buyer_phone = t;
        e.area_code = area_code;
        //店铺留言
        e.remarks = storeMessage;
        Zepto.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Buyer_Order&met=addVirtualOrder&typ=json",
            data: e,
            dataType: "json",
            success: function (e) {
                checkLogin(e.login);
                console.info(e);
                if (e.status == 250) {
                    Zepto.sDialog({skin: "red", content: e.msg, okBtn: false, cancelBtn: false});
                    return false;
                }
                
                if (e.data.uorder) {
                    location.href = PayCenterWapUrl + "/?ctl=Info&met=pay&uorder=" + e.data.uorder + "&order_g_type=virtual";
                }
                /*if (e.datas.order_id)
                {
                    toPay(e.datas.order_sn, "member_vr_buy", "pay")
                }*/
                return false;
            }
        });
    });
});
