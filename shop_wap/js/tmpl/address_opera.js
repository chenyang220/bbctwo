// jQuery("#re_user_mobile").intlTelInput({
//     utilsScript: "../../js/utils.js"
// });
Zepto(function ()
{

    var a = getCookie("key");
    Zepto.sValid.init({
        rules: {true_name: "required", mob_phone: "required", area_info: "required", address: "required"},
        messages: {true_name: "姓名必填！", mob_phone: "手机号必填！", area_info: "地区必填！", address: "街道必填！"},
        callback: function (a, e, r)
        {
            if (a.length > 0)
            {
                var i = "";
                Zepto.map(e, function (a, e)
                {
                    i += "<p>" + a + "</p>"
                });
                errorTipsShow(i)
            }
            else
            {
                errorTipsHide()
            }
        }
    });
    Zepto("#header-nav").click(function ()
    {
        Zepto(".btn-l").click()
    });
	Zepto(".addr-attr input").click(function () {
		$(".addr-attr em").removeClass("active");
		$(this).parent().addClass("active");
	});
    Zepto(".btn-l").click(function ()
    {
        var e = Zepto("#true_name").val();
        var r = Zepto("#re_user_mobile").val();
        var area_code = Zepto('#area_code').val();
        var address_attribute = $("input[name='address_attribute']:checked").val();
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
        if(!(/^1[345678]\d{9}$/.test(r)) && area_code == 86){
            errorTipsShow("<p>手机号码有误，请重填</p>");
            return false;
        }
        if(i.length > 100){
            errorTipsShow("<p>详细地址不能超过100个字符</p>");
            return false;
        }

        Zepto.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Buyer_User&met=addAddressInfo&typ=json",
            data: {k:getCookie('key'),u:getCookie('id'), user_address_contact: e, user_address_phone: r, province_id: province_id, city_id: city_id, area_id: area_id, user_address_address: i, address_area: n, user_address_default: o, address_attribute:address_attribute},

            dataType: "json",
            success: function (a)
            {
                if (a)
                {
                    location.href = WapSiteUrl + "/tmpl/member/address_list.html"
                }
                else
                {
                    location.href = WapSiteUrl
                }
            }
        })

    });
    Zepto("#area_info").on("click", function ()
    {
        Zepto.areaSelected({
            success: function (a)
            {
                Zepto("#area_info").val(a.area_info).attr({"data-areaid1": a.area_id_1, "data-areaid2": a.area_id_2, "data-areaid3": a.area_id_3, "data-areaid": a.area_id, "data-areaid2": a.area_id_2 == 0 ? a.area_id_1 : a.area_id_2})
            }
        })
    });


     Zepto("#button2").on("click", function ()
        {

            var address_self_motion = $("textarea[name='address_self_motion']").val();
            if(!address_self_motion){
                errorTipsShow("<p>请输入地址信息</p>");
                return false;
            }
            Zepto.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Buyer_User&met=addressSelfFill&typ=json",
                data: {k:getCookie('key'),u:getCookie('id'), address_self_motion: address_self_motion},
                dataType: "json",
                success: function (a)
                {
                    if (a.status == 200)
                    {
                        Zepto("#true_name").val(a.data.addressee_name);
                        Zepto("#re_user_mobile").val(a.data.addressee_mobile);
                        Zepto("#address").val(a.data.user_address_address);
                        Zepto("#area_info").val(a.data.addressee_user_info).attr({
                            "data-areaid1": a.data.user_address_province_id, 
                            "data-areaid2": a.data.user_address_city_id, 
                            "data-areaid3": a.data.user_address_area_id, 
                            "data-areaid": a.data.user_address_area_id,
                            "data-areaid2": a.user_address_city_id == 0 ? a.user_address_province_id : a.user_address_city_id
                        });

                        $(".form-btn-color").addClass('ok');
                    }
                    else
                    {
                        errorTipsShow("<p>请输入正确的地址格式</p>");
                        return false;
                    }
                }
            })
        });


    Zepto("#button1").on("click", function ()
        {
            $("#adresstext").val('');
        });
});