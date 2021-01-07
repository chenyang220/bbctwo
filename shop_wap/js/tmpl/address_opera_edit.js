
Zepto(function ()
{
    var user_address_id = getQueryString("user_address_id");
    var e = getCookie("key");
    Zepto.ajax({
        type: "post", url: ApiUrl + "/index.php?ctl=Buyer_User&met=address&act=edit&typ=json", data: {k:e,u:getCookie('id'), id: user_address_id,targ:"wap"}, dataType: "json", success: function (a)
        {
            checkLogin(a.login);
            Zepto("#true_name").val(a.data.address_list.user_address_contact);
            Zepto("#re_user_mobile").val(a.data.address_list.user_address_phone);
            Zepto("#area_code").val(a.data.address_list.area_code);
            Zepto("#area_info").val(a.data.address_list.user_address_area).attr({"data-areaid1": a.data.address_list.user_address_province_id, "data-areaid2": a.data.address_list.user_address_city_id, "data-areaid3": a.data.address_list.user_address_area_id, "data-areaid": a.data.address_list.user_address_province_id});
            Zepto("#address").val(a.data.address_list.user_address_address);
            $("input[name='address_attribute']").removeAttr("checked");
            $("input[name='address_attribute'][value='"+a.data.address_list.user_address_attribute+"']").prop("checked", "checked");
            var e = a.data.address_list.user_address_default == "1" ? true : false;
            Zepto("#is_default").prop("checked", e);
            if (e)
            {
                Zepto("#is_default").parents("label").addClass("checked")
            }
            jQuery("#re_user_mobile").intlTelInput({
                utilsScript: "../../js/utils.js"
            });
        }
    });


    Zepto.sValid.init({
        rules:{
            true_name:{required: true, maxlength: 20},
            area_info:"required",
            address:{required: true, maxlength: 100},
            mob_phone:function(elem, param){
                var area_code = $('#area_code').val();
                var re_user_mobile = elem.val();
                var reg = /^1[3-9]\d{9}$/;
                if(area_code == 86 && !reg.test(re_user_mobile)){
                    return "请输入正确的手机号";
                }
            }
        },
        messages:{
            true_name:{required: "姓名必填！", maxlength: "姓名最多20个字符！"},
            area_info:"地区必填！",
            address:{required: "街道必填！", maxlength: "地址最多100个字符！"}
        },
        callback: function (a, e, r)
        {
            if (a.length > 0)
            {
                var d = "";
                Zepto.map(e, function (a, e)
                {
                    d += "<p>" + a + "</p>"
                });
                errorTipsShow(d)
            }
            else
            {
                errorTipsHide()
            }
        }
    });
    Zepto("#header-nav").click(function ()
    {
        Zepto(".btn").click()
    });
    Zepto(".btn").click(function ()
    {
        if (Zepto.sValid())
        {
            var r = Zepto("#true_name").val();
            var d = Zepto("#re_user_mobile").val();
            var area_code = Zepto("#area_code").val();
            var i = Zepto("#address").val();
            var address_attribute = $("input[name='address_attribute']:checked").val();
            var province_id = Zepto("#area_info").attr("data-areaid1");
            var city_id = Zepto("#area_info").attr("data-areaid2");
            var area_id = Zepto("#area_info").attr("data-areaid3");
            var n = Zepto("#area_info").val();

            var o = Zepto("#is_default").attr("checked") ? 1 : 0;
            if (r.length > 20 || r.length < 2) {
                errorTipsShow("<p>收货人姓名为2~20个字符</p>");
                return false;
            }
            if(!(/^1[345678]\d{9}$/.test(d)) && area_code == 86){
                errorTipsShow("<p>手机号码有误，请重填</p>");
                return false;
            }
            if(i.length > 100){
                errorTipsShow("<p>详细地址不能超过100个字符</p>");
                return false;
            }
            Zepto.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Buyer_User&met=editAddressInfo&typ=json",
                data: {k:e,u:getCookie('id'),address_attribute: address_attribute, user_address_contact: r, user_address_phone: d,area_code:area_code, province_id: province_id, city_id: city_id, area_id: area_id, user_address_address: i, address_area: n, user_address_default: o, user_address_id: user_address_id},
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
        }
    });


    Zepto("#area_info").on("click", function ()
    {
        Zepto.areaSelected({
            success: function (a)
            {
                Zepto("#area_info").val(a.area_info).attr({
                    "data-areaid1": a.area_id_1, 
                    "data-areaid2": a.area_id_2, 
                    "data-areaid3": a.area_id_3, 
                    "data-areaid": a.area_id, 
                    "data-areaid2": a.area_id_2 == 0 ? a.area_id_1 : a.area_id_2
                })
            }
        })
    })

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
