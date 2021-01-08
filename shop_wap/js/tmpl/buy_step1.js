var key = getCookie('key');
var uid = getCookie('id');

// buy_stop2使用变量
var ifcart = getQueryString('ifcart');
if (ifcart == 1) {

    var cart_type = '';
    var cart_id = getQueryString('cart_id')
    // var cart_id = getQueryString('cart_id') ? getQueryString('cart_id') : getQueryString('point_cart_id');
    cart_id = cart_id.split(',');
} else {
    var cart_id = getQueryString("goods_id") + '|' + getQueryString("buynum");
}
var pay_name = 'online';

var address_id, user_rate, offpay_hash, offpay_hash_batch, voucher, pd_pay, password, fcode = '', rcb_pay, rpt,
    payment_code, is_discount = 0;
var message = {};
// change_address 使用变量
var freight_hash, city_id, area_id, province_id;
// 其他变量
var area_info;
var goods_id;


function isEmptyObject(e) {
    var t;
    for (t in e)
        return !1;
    return !0
}

//领取代金券
function getvoucher(id) {
    getFreeVoucher(id);
}

Zepto(function () {

    Zepto(document).on('keyup', '.remarks', function () {
        if (Zepto(this).val()) {
            Zepto(this).next().removeClass('hide');
        }
        else {
            Zepto(this).next().addClass('hide');
        }
    });
    Zepto(document).on('click', '.icon-X', function () {
        Zepto(this).prev().val("");
        Zepto(this).addClass('hide');
    })

    Zepto("input[type='checkbox']").prop("checked", false);

    var isIntegral = getQueryString("isIntegral");

    // 地址列表
    Zepto('#list-address-valve').click(function () {
        var address_id = Zepto(this).find("#address_id").val();
        var area_code = Zepto(this).find("#area_code").val();
        Zepto.ajax({
            type: 'post',
            url: ApiUrl + "/index.php?ctl=Buyer_Cart&met=confirm&typ=json",
            data: {k: key, u: getCookie('id'), product_id: cart_id},
            dataType: 'json',
            async: false,
            success: function (result) {
                checkLogin(result.login);
                if (result.data.address == null) {
                    return false;
                }
                var data = result.data;
                data.address_id = address_id;
                data.area_code = area_code;
                var html = template.render('list-address-add-list-script', data);
                Zepto("#list-address-add-list-ul").html(html);
            }
        });
        // jQuery("#re_user_mobile").intlTelInput({
        //     utilsScript: "../../js/utils.js"
        // });
    });
    Zepto.animationLeft({
        valve: '#list-address-valve',
        wrapper: '#list-address-wrapper',
        scroll: '#list-address-scroll'
    });

    // 地区选择
    Zepto('#list-address-add-list-ul').on('click', 'li', function () {
        Zepto(this).addClass('selected').siblings().removeClass('selected');
        eval('address_info = ' + Zepto(this).attr('data-param'));
        address_id = address_info.user_address_id;
        area_code = address_info.area_code;

        _init(address_info.user_address_id, is_discount);
        Zepto('#true_name').html(address_info.user_address_contact);
        Zepto('#mob_phone').html(address_info.user_address_phone);
        Zepto('#address').html(address_info.user_address_area + address_info.user_address_address);
        Zepto("#address_id").val(address_info.user_address_id);
        Zepto("#area_code").val(address_info.area_code);
        Zepto('#list-address-wrapper').find('.header-l > a').click();
    });

    // 地址新增
    Zepto.animationLeft({
        valve: '#new-address-valve',
        wrapper: '#new-address-wrapper',
        scroll: ''
    });

    var red_price = 0;//还原价格

    // 支付方式
    Zepto.animationUp({
        valve: '#select-payment-valve',
        wrapper: '#payways',
        scroll: ''
    });

    // 地区选择
    Zepto('#new-address-wrapper').on('click', '#varea_info', function () {
        Zepto.areaSelected({
            success: function (data) {
                if(province_id != data.area_id_1 && city_id != data.area_id_2 && area_id != data.area_id_3) {
                    province_id = data.area_id_1;
                    city_id = data.area_id_2;
                    area_id = data.area_id_3;
                    area_info = data.area_info;
                    Zepto('#varea_info').val(data.area_info);
                }
            }
        });
    });

    template.helper('isEmpty', function (o) {
        var b = true;
        Zepto.each(o, function (k, v) {
            b = false;
            return false;
        });
        return b;
    });

    template.helper('pf', function (o) {
        return parseFloat(o) || 0;
    });

    template.helper('p2f', function (o) {
        return (parseFloat(o) || 0).toFixed(2);
    });

    /*
    * address_id:地址id
    * is_discount:是否开启会员折扣 1-开启会员折扣 0-关闭会员折扣
    * */
    var _init = function (address_id, is_discount) {
        var totals = 0;
        var gptotl = 0; //商品总价
        var cptotal = 0; //运费总价
        var vototal = 0; //优惠活动总价
        // 购买第一步 提交
        Zepto.ajax({//提交订单信息
            type: 'post',
            url: ApiUrl + '/index.php?ctl=Buyer_Cart&met=confirm&typ=json',
            dataType: 'json',
            data: {
                k: key,
                u: getCookie('id'),
                product_id: cart_id,
                ifcart: ifcart,
                address_id: address_id,
                is_discount: is_discount,
                cart_type: cart_type
            },
            success: function (result) {
                  console.log(result);
                if (result.status == 250) {
                    Zepto.sDialog({
                        skin: "red",
                        content: result.data.msg,
                        okBtn: false,
                        cancelBtn: false
                    });
                    return false;
                }

                // 商品数据
                user_rate = result.data.user_rate;
                result.data.address_id = address_id;
                result.data.WapSiteUrl = WapSiteUrl;
                delete result.data.glist.count
                var html = template.render('goods_list', result.data);

                Zepto("#deposit").html(html);
                //判断是否是自营决定是否显示会员折扣
                // if (Number(result.data.rate_service_status) == 1 && result.data.is_shop_self_support > 0) {
                //     $("#support").hide();
                // }
                //判断是否开启了会员折扣，开启会员折扣后不渲染红牌列表和代金券列表
                if (is_discount) {
                    Zepto("#use_redpacket").html('平台红包不与会员折扣共用');
                    Zepto("#use_redpacket").removeClass('btn-ziti');
                    Zepto("#use_redpacket").parent().removeClass('mr53');
                    Zepto("#redpacket").find(".icon-arrow").hide();
                    Zepto("#redpacket").removeClass('hongbao');
                    //在会员折扣处显示折扣金额
                    // 折扣：总金额totals
                    totals = Number(result.data.order_price) + Number(result.data.order_discount);

                    Zepto("#ratePrice").html('' + (10 - result.data.order_discount / totals * 10).toFixed(2) + '折，即：减￥' + result.data.order_discount);
                    Zepto("#ratePrice").parent().addClass("default-color");
                    Zepto(".discount").val(result.data.order_discount);
                } else {
                    //初始化红包信息
                    initRedPacket(result);
                    //会员折扣
                    Zepto("#ratePrice").text('此功能不与优惠券共用');
                    Zepto("#ratePrice").parent().removeClass("default-color");
                    Zepto("#use_redpacket").parent().addClass('mr53');
                    // 3.6.7 plus start
                    if (result.data.isPlus && result.data.sumPlusGoods) {
                        Zepto("#ratePrice").next().css('display','none');
                        Zepto("#redpacket").removeClass('hongbao');
                        Zepto(".plus-power-limit").removeClass('hide');
                    }
                    // 3.6.7 plus end
                    //平台红包
                    red_price = Zepto("input[name='red-price']").val();
                    //代金券
                    var total_best_voucher_price = 0;
                    Zepto("input[name='best_voucher_price']").each(function () {
                        total_best_voucher_price += Number(Zepto(this).val());
                    });
                }
                for (var i in result.data.glist) {
                    Zepto.animationUp({
                        valve: '.animation-up' + i,          // 动作触发，为空直接触发
                        wrapper: '.nctouch-bottom-mask' + i,    // 动作块
                        scroll: '.nctouch-bottom-mask-rolling' + i,     // 滚动块，为空不触发滚动
                    });
                }

                //判断是否开启货到付款功能
                if(result.data.cash_on_delivery_status != 1 || result.data.is_gift == 1)
                {
                    Zepto("#select-payment-valve").find('i').hide();
                    Zepto("#select-payment-valve").attr('id','');
                }
                // 默认地区相关
                /*if ($.isEmptyObject(result.data.address)) {
                    $.sDialog({
                        skin:"block",
                        content:'请添加地址',
                        okFn: function() {
                            $('#new-address-valve').click();
                        },
                        cancelFn: function() {
                            history.go(-1);
                        }
                    });
                    return false;
                }*/
                if (Zepto.isEmptyObject(result.data.address)) {
                    Zepto(".address2").removeClass('packets-type');
                } else {
                    Zepto(".address1").removeClass('packets-type');
                }
                Zepto('.new-address-valve').on('click', function () {
                    Zepto('#new-address-valve').click();
                });
                result.data.address && result.data.address.length > 0 && insertHtmlAddress(result.data.address, address_id);
                for (var k in result.data.glist) {
                    // 留言
                    if(k="0" && result.data.glist.length==1){
                        Zepto('#storeMessage0').addClass("form-input-last");
                    }
                    if(k=result.data.glist.length-1){
                        Zepto('#storeMessage' + k).addClass("form-input-last");
                    }
                    message[k] = '';
                    Zepto('#storeMessage' + k).on('change', function () {
                        message[k] = Zepto(this).val();
                    });
                }
                password = '';
                //总价
                Zepto('#totalPrice,#onlineTotal').text(result.data.order_price.toFixed(2));
                //判断是否有活动商品，如果有活动商品不显示会员折扣开关
                if (result.data.pomotion) {
                    Zepto("#ratePrice").text('涉及活动商品不可使用会员折扣');
                    Zepto("#ratePrice").parent().removeClass("default-color");
                    Zepto(".rptbutton").remove();
                }
                /*****加价购、代金券*****/
                initPromotionWindow();
                /*****加价购、代金券*****/

                //判断是否为礼包商品
                if (result.data.isUseDiscount == true) {
                    $("#ToBuyStep").parent().removeClass('hide');
                    $("#ToBuyStep2").parent().addClass('hide');
                } else {
                    $("#ToBuyStep").parent().addClass('hide');
                    $("#ToBuyStep2").parent().removeClass('hide');
                }
            }
        });
    }
    var scrTop = Zepto(window).scrollTop();
    Zepto(document).on("focus",".form-input-last",function(){
        var curscroll=Zepto(window).scrollTop();
        var _this = this;
        typeof(timer) !="undefined" && clearTimeout(timer);
        timer = setTimeout(function() {
         _this.scrollIntoView(false);
        var addscroll=curscroll+100;
            Zepto(window).scrollTop(addscroll);
        }, 400);
    })


    //初始化红包信息
    function initRedPacket(result, refresh) {
        //判断确认订单页是否存在活动商品，如果存在活动商品则不可以使用平台红包
        if (result.data.pomotion) {
            Zepto("#use_redpacket").html('涉及活动商品不可用平台红包');
            Zepto("#use_redpacket").removeClass('btn-ziti');
            Zepto("#redpacket").find(".icon-arrow").hide();
        } else {
            //渲染平台红包列表
            var redpacketHtml = template.render('redpacket_list', result.data);
            Zepto("#order_redpacket_list").html(redpacketHtml);
            Zepto("#redpacket").find(".icon-arrow").show();
            Zepto("#redpacket").addClass('hongbao');
            //点击打开平台红包选择列表框
            Zepto.animationLeft({
                valve: '.hongbao',
                wrapper: '#select-redpacket',
                scroll: ''
            });

            //默认显示最优红包
            if (result.data.rpt_info && result.data.rpt_info.length > 0) {
                Zepto("#use_redpacket").addClass('btn-ziti');
                Zepto("#use_redpacket").html('满' + result.data.rpt_info[0].limit + '减' + result.data.rpt_info[0].price);
                Zepto("input[name='red-price']").val(result.data.rpt_info[0].price);
                Zepto("input[name='red-price']").data('rpt_id', result.data.rpt_info[0].id);
                Zepto("input[name='red-price']").attr('is-best', 1);

                if (refresh) {
                    calculateRedpacket(result.data.rpt_info[0].price, result.data.rpt_info[0].limit, result.data.rpt_info[0].id);
                }
            } else {
                if (result.data.is_discount == 1) {
                    Zepto("#use_redpacket").html('不使用平台红包');
                    Zepto("#use_redpacket").removeClass('btn-ziti');
                } else {
                    Zepto("#use_redpacket").html('暂无可用平台红包');
                    Zepto("#use_redpacket").removeClass('btn-ziti');
                }
            }
        }
    }

    //平台红包选用
    Zepto("#order_redpacket_list").on('click', "input[type='radio']", function () {
        var redpacket_price = Zepto(this).data('price'),
            redpacket_limit = Zepto(this).data('limit'),
            redpacket_id = Zepto(this).data('id');
        calculateRedpacket(redpacket_price, redpacket_limit, redpacket_id);
    });

    //计算平台红包
    function calculateRedpacket(redpacket_price, redpacket_limit, redpacket_id) {
        //选择使用平台红包
        if (redpacket_price > 0 && redpacket_id > 0) {
            Zepto("#use_redpacket").addClass('btn-ziti');
            Zepto("#use_redpacket").html('满' + redpacket_limit + '减' + redpacket_price);
            Zepto("input[name='red-price']").data('rpt_id', redpacket_id);
            Zepto("input[name='red-price']").val(redpacket_price);
            //订单合计 = 订单原合计金额 + 原红包金额 - 红包金额
            Zepto("#totalPrice").text((Zepto("#totalPrice").text() * 1 + Number(red_price) - Number(redpacket_price)).toFixed(2));
        } else {
            //订单合计 = 订单原合计 + 原红包金额 - 红包金额
            Zepto("#totalPrice").text((Zepto("#totalPrice").text() * 1 + Number(red_price) - Number(redpacket_price)).toFixed(2));

            Zepto("input[name='red-price']").data('rpt_id', 0);
            Zepto("input[name='red-price']").val(0);

            //判断是否有可用的代金券
            isbest = Zepto("input[name='red-price']").attr('is-best');

            if (isbest == 1) {
                Zepto("#use_redpacket").html("不使用平台红包");
                Zepto("#use_redpacket").removeClass('btn-ziti');
            } else {
                Zepto("#use_redpacket").html("暂无可用平台红包");
                Zepto("#use_redpacket").removeClass('btn-ziti');
            }

        }

        red_price = redpacket_price;//记录红包金额
        Zepto('#select-redpacket').find('.header-l > a').click();

        //礼包商品使用代金券
        //代金券使用
        var sum_voucher = '';
        $("input[name^=best_voucher_price]").each(function () {
            sum_voucher += Number($(this).val());
        });
        //满送使用
        var sum_ms = '';
        $("input[name^=msprice]").each(function () {
            sum_ms += Number($(this).val());
        })
        if (Number(redpacket_price) > 0 || Number(sum_voucher) > 0 || Number(sum_ms) > 0) {
            $("#ToBuyStep").parent().removeClass('hide');
            $("#ToBuyStep2").parent().addClass('hide');
        } else {
            $("#ToBuyStep").parent().addClass('hide');
            $("#ToBuyStep2").parent().removeClass('hide');
        }

    }


    rcb_pay = 0;
    pd_pay = 0;

    //查找用户的默认地址
    Zepto.ajax({
        type: 'post',
        url: ApiUrl + '/index.php?ctl=Buyer_User&met=getUserConfigAddress&typ=json',
        dataType: 'json',
        data: {k: key, u: getCookie('id')},
        success: function (result) {
            if (result.data) {
                address_id = result.data.id;
            }
            _init(address_id, is_discount);
        }
    })


    // 初始化


    // 插入地址数据到html
    var insertHtmlAddress = function (address, address_id) {
        var address_info = {};
        for (var i = 0; i < address.length; i++) {
            if (address_id != 0) {
                if (address[i].user_address_id == address_id) {
                    //address_info.address_id = address[i].user_address_area_id;
                    address_info.address_id = address[i].user_address_id;
                    address_info.user_address_contact = address[i].user_address_contact;
                    address_info.provice_id = address[i].user_address_provice_id;
                    address_info.city_id = address[i].user_address_city_id;
                    address_info.area_id = address[i].user_address_area_id;
                    address_info.user_address_phone = address[i].user_address_phone;
                    address_info.area_code = address[i].area_code;
                    address_info.user_address_area = address[i].user_address_area;
                    address_info.user_address_address = address[i].user_address_address;
                }
            } else {
                if (address[i].user_address_default) {
                    //address_info.address_id = address[i].user_address_area_id;
                    address_info.address_id = address[i].user_address_id;
                    address_info.user_address_contact = address[i].user_address_contact;
                    address_info.provice_id = address[i].user_address_provice_id;
                    address_info.city_id = address[i].user_address_city_id;
                    address_info.area_id = address[i].user_address_area_id;
                    address_info.user_address_phone = address[i].user_address_phone;
                    address_info.area_code = address[i].area_code;
                    address_info.user_address_area = address[i].user_address_area;
                    address_info.user_address_address = address[i].user_address_address;
                }
            }
        }

        if (!isEmptyObject(address_info)) {
            address_id = address_info.address_id;
            Zepto('#true_name').html(address_info.user_address_contact);
            Zepto('#mob_phone').html(address_info.user_address_phone);
            Zepto('#address').html(address_info.user_address_area + address_info.user_address_address);
        } else {
            Zepto('#address').html('未选择收货地址');
        }

        Zepto("#address_id").val(address_id);
        area_id = address_info.area_id;
        city_id = address_info.city_id;
        province_id = address_info.provice_id;
        Zepto('#ToBuyStep2').parent().addClass('ok');
        Zepto('#ToBuyStep').parent().addClass('ok');
    }

    // 支付方式选择
    // 在线支付
    Zepto('#payment-online,#payment-offline').click(function () {
        var paymentWayText;
        if (this.id == 'payment-online') {
            paymentWayText = '在线支付'
        } else {
            paymentWayText = '货到付款'
        }
        Zepto('#select-payment-valve').find('.current-con').html(paymentWayText);
    })

    // 地址保存
    Zepto.sValid.init({
        rules: {
            vtrue_name: {required: true, maxlength: 20},
            vmob_phone: {required: true, mobile: true},
            varea_info: "required",
            vaddress: {required: true, maxlength: 100}
        },
        messages: {
            vtrue_name: {required: "姓名必填！", maxlength: "姓名最多20个字符！"},
            vmob_phone: {required: "手机号必填！", mobile: "手机号码不正确！"},
            varea_info: "地区必填！",
            vaddress: {required: "街道必填！", maxlength: "地址最多100个字符！"}
        },
        callback: function (eId, eMsg, eRules) {
            if (eId.length > 0) {
                var errorHtml = "";
                Zepto.map(eMsg, function (idx, item) {
                    errorHtml += "<p>" + idx + "</p>";
                });
                errorTipsShow(errorHtml);
            } else {
                errorTipsHide();
            }
        }
    });
	Zepto(".addr-attr input").click(function () {
			$(".addr-attr em").removeClass("active");
			$(this).parent().addClass("active");
		});
    Zepto('#add_address_form').find('.btn-l').click(function () {
        if (Zepto.sValid()) {
            var param = {};
            param.k = key;
            param.user_address_contact = Zepto('#vtrue_name').val();
            param.user_address_phone = Zepto('#re_user_mobile').val();
            param.area_code = Zepto('#area_code').val();
            param.user_address_address = Zepto('#vaddress').val();
            param.address_area = Zepto('#varea_info').val();
            param.province_id = province_id;
            param.city_id = city_id;
            param.area_id = area_id;
            param.address_attribute = $("input[name='address_attribute']:checked").val();
            param.user_address_default = Zepto("#is_default").attr("checked") ? 1 : 0;

            param.u = getCookie('id');
            if (param.user_address_contact.length > 20 || param.user_address_contact.length < 2) {
                errorTipsShow("<p>收货人姓名为2~20个字符</p>");
                return false;
            }
            if(!(/^1[345678]\d{9}$/.test(Zepto('#re_user_mobile').val())) && Zepto('#area_code').val() == 86){
                errorTipsShow("<p>手机号码有误，请重填</p>");
                return false;
            }

            Zepto.ajax({
                type: 'post',
                url: ApiUrl + "/index.php?ctl=Buyer_User&met=addAddressInfo&typ=json",
                data: param,
                dataType: 'json',
                success: function (result) {
                    //console.info(result);
                    if (result.status == 200) {
                        //param.address_id = result.data.address_id;
                        _init(result.data.user_address_id, is_discount);
                        Zepto('#true_name').html(result.data.user_address_contact);
                        Zepto('#mob_phone').html(result.data.user_address_phone);
                        Zepto('#address').html(result.data.user_address_area + result.data.user_address_address);
                        Zepto("#address_id").val(result.data.user_address_id);
                        Zepto("#area_code").val(result.data.area_code);
                        Zepto('#new-address-wrapper,#list-address-wrapper').find('.header-l > a').click();
                        Zepto(".address2").addClass('packets-type');
                    } else {
                        errorTipsShow("<p>" + result.msg + "</p>");
                    }
                }
            });
        }
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
                        Zepto("#vtrue_name").val(a.data.addressee_name);
                        Zepto("#re_user_mobile").val(a.data.addressee_mobile);
                        Zepto("#vaddress").val(a.data.user_address_address);
                        Zepto("#varea_info").val(a.data.addressee_user_info).attr({
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
    //礼包商品使用优惠券
    Zepto('#ToBuyStep').click(function () {
        Zepto.sDialog({
            content: "礼包商品使用优惠券，实付金额不满足升级掌柜提醒", okFn: function () {
                Zepto('#ToBuyStep2').click();
            }
        })
    });
    // 支付
    Zepto('#ToBuyStep2').click(function () {
        // 预售未打开同意支付定金开关弹框
        var presale_pay = Zepto('#presale_pay').attr('checked');
        var presale = $("#presale").val();
        if(!presale_pay&&presale){
            Zepto.sDialog({
                skin: "red",
                content: "预售商品定金不支持退款，同意后可继续下单",
                okBtn:true,
                cancelBtn: true,
                "okBtnText": "同意下单",
                "cancelBtnText": "我再想想",
                "okFn": function() {
                    var buy_able = 1;
                    var goods_name = '';
                    Zepto('.buy-item').each(function () {
                        if (Zepto(this).data('buy_able') == 0) {
                            buy_able = Zepto(this).data('buy_able');
                            goods_name = Zepto(this).data('goods_name');
                            return false;
                        }

                    });
                    if (buy_able == 0) {
                        Zepto.sDialog({
                            content: '商品【' + goods_name + '】不在配送范围，请更换收货地址或者选择其他商品！',
                            okBtn: true,
                            dialogClass:"ceshi",
                            cancelBtn: true,
                            cancelBtnText: '取消',
                            okBtnText: '返回购物车',
                            okFn: function () {
                                history.back();
                            }
                        });
                        return false;
                    }

                    if (Zepto("#totalPayPrice").html() >= 99999999.99) {
                        Zepto.sDialog({
                            content: '订单金额过大，请分批购买！',
                            okBtn: false,
                            cancelBtnText: '返回',
                            cancelFn: function () {
                                history.back();
                            }
                        });
                        return;
                    }

                    //1.获取收货地址
                    address_contact = Zepto("#true_name").html();
                    address_address = Zepto("#address").html();
                    address_phone = Zepto("#mob_phone").html();
                    address_id = Zepto("#address_id").val();
                    area_code = Zepto("#area_code").val();
                    if (address_id == 'undefined') {
                        Zepto.sDialog({
                            skin: "red",
                            content: '请选择收货地址！',
                            okBtn: false,
                            cancelBtn: false
                        });

                        return false;
                    }

                    //2.获取发票信息
                    var invoice = Zepto("#invContent").html();
                    var invoice_id = Zepto("#order_invoice_id").val();
                    var invoice_title = Zepto("#order_invoice_title").val();
                    var invoice_content = Zepto("#order_invoice_content").val();
                    //3.获取商品信息（商品id，商品备注）
                    var cart_id = [];//定义一个数组
                    Zepto("input[name='cart_id']").each(function () {
                        cart_id.push(Zepto(this).val());//将值添加到数组中
                    });

                    var remark = [];
                    var shop_id = [];
                    Zepto("input[name='remarks']").each(function () {
                        shop_id.push(Zepto(this).attr("rel"));
                        remark.push(Zepto(this).val());//将值添加到数组中
                    });

                    //平台红包
                    var redpacket_id = Zepto("input[name='red-price']").data('rpt_id');
                    /****************获取促销信息****************/
                        //代金券
                    var voucher_ids = [];
                    Zepto("input[name='best_voucher_price']").each(function () {
                        voucher_ids.push(Zepto(this).data('voucher_id'));
                    })
                    var promotion_rows = getAllPromotionData(),
                        increase_arr = []; //加价购
                        goods_id = [];//规则id
                        increase_goods_id = [];//规则id
                    if (promotion_rows !== false && !Zepto.isEmptyObject(promotion_rows)) {
                        // for (var k_shop_id in promotion_rows) {
                        //     // promotion_rows[k_shop_id].voucher_id && voucher_ids.push(promotion_rows[k_shop_id].voucher_id);
                        //     if (promotion_rows[k_shop_id].jjg_goods_data) {
                        //         for (var i = 0, promotion_data = promotion_rows[k_shop_id].jjg_goods_data, length = promotion_data.length; i < length; i++) {
                        //             increase_arr.push({
                        //                 increase_shop_id: k_shop_id,
                        //                 increase_goods_id: promotion_data[i].goods_id,
                        //                 increase_goods_num: promotion_data[i].goods_num,
                        //                 increase_price: promotion_data[i].goods_promotion_price
                        //             });
                        //         }
                        //     }
                        // }
                        if(promotion_rows.length > 0)
                            {
                                for (var i = 0; i < promotion_rows.length; i++) {
                                    increase_arr.push({
                                        increase_shop_id: promotion_rows[i]['shop_id'],
                                        increase_goods_id: promotion_rows[i]['goods_id'],
                                        increase_goods_num: promotion_rows[i]['goods_num'],
                                        increase_price: promotion_rows[i]['goods_promotion_price'],
                                        rule_id: promotion_rows[i]['rule_id'],
                                    });
                                    goods_id.push(promotion_rows[i]['promotion_goods_id']);
                                }
                            }
                    }

                    // for(var v = 0;v < goods_id.length;v++)
                    // {
                    //     if (goods_id.indexOf(goods_id[v]) == v)
                    //     {
                    //         increase_goods_id.push(goods_id[v]);
                    //     }
                    // }
                    // if(goods_id.length > 1 && increase_goods_id.length == 1)
                    // {
                    //     Zepto.sDialog({
                    //         content: '同一件商品只能选择一件加价购商品',
                    //         okBtn:false,
                    //         cancelBtn:false,
                    //         cancelFn: function() { /*history.back();*/ }
                    //     });
                    //     return false;
                    // }
                    /****************获取促销信息****************/
                    if (!address_id) {
                        Zepto.sDialog({
                            content: '请填写收货地址！',
                            okBtn: false,
                            cancelBtnText: '返回',
                            cancelFn: function () { /*history.back();*/
                            }
                        });
                        return false;
                    }
                    //获取支付方式
                    var pay_way_id = Zepto('[name="pay-selected"]:checked').val();
                    var seckill = getQueryString('seckill');
                    var seckill_goods_id = getQueryString('seckill_goods_id');
                    var presale = $("#presale").val();
                    var final_mobile = Zepto('#final_mobile').val();
                    if(seckill_goods_id){
                        Zepto.ajax({
                            type: 'post',
                            url: ApiUrl + '?ctl=Buyer_Order&met=seckill&typ=json',
                            data: {
                               k: key,
                               u: getCookie('id'), 
                               seckill_goods_id:seckill_goods_id,
                            },
                            dataType: "json",
                            success: function (a) {
                                if(a.status==200){
                                    Zepto.ajax({
                                        type: 'post',
                                        url: ApiUrl + '?ctl=Buyer_Order&met=addOrder&typ=json',
                                        data: {
                                            receiver_name: address_contact,
                                            receiver_address: address_address,
                                            receiver_phone: address_phone,
                                            area_code:area_code,
                                            invoice: invoice,
                                            invoice_id: invoice_id,
                                            invoice_title: invoice_title,
                                            invoice_content: invoice_content,
                                            cart_id: cart_id,
                                            shop_id: shop_id,
                                            remark: remark,
                                            pay_way_id: pay_way_id,
                                            address_id: address_id,
                                            k: key,
                                            u: getCookie('id'),
                                            from: "wap",
                                            increase_arr: increase_arr, //加价购
                                            voucher_id: voucher_ids, // 代金券
                                            redpacket_id: redpacket_id, //平台红包
                                            is_discount: is_discount,//是否开启会员折扣
                                            seckill:seckill,
                                            seckill_goods_id:seckill_goods_id,
                                        },
                                        dataType: "json",
                                        success: function (a) {
                                            if (a.status == 200) {
                                                delCookie('cart_count');
                                                //重新计算购物车的数量
                                                getCartCount();
                                                if (pay_way_id == 1) {
                                                    window.location.href = PayCenterWapUrl + "/?ctl=Info&met=pay&uorder=" + a.data.uorder + '&typ=e';
                                                    return false;
                                                } else {
                                                    window.location.href = WapSiteUrl + '/tmpl/member/order_list.html';
                                                    return false;
                                                }
                                            } else {
                                                if (a.msg != 'failure') {
                                                    /* Public.tips.error(a.msg);*/
                                                    Zepto.sDialog({
                                                        content: a.msg,
                                                        okBtn: false,
                                                        cancelBtnText: '返回',
                                                        cancelFn: function () { /*history.back();*/
                                                        }
                                                    });
                                                } else {
                                                    /*Public.tips.error('订单提交失败！');*/
                                                    Zepto.sDialog({
                                                        content: '订单提交失败！',
                                                        okBtn: false,
                                                        cancelBtnText: '返回',
                                                        cancelFn: function () { /*history.back();*/
                                                        }
                                                    });
                                                }
                                            }
                                        },
                                        failure: function (a) {
                                            Public.tips.error('操作失败！');
                                        }
                                    });
                                    //alert('秒杀成功');
                                }else{
                                    Zepto.sDialog({
                                        content: a.msg,
                                        okBtn: false,
                                        cancelBtnText: '返回',
                                        cancelFn: function () { /*history.back();*/
                                        }
                                    });

                                }
                                
                            },
                            failure: function (a) {
                                Public.tips.error('操作失败！');
                            }
                        });
                    }else{
                        Zepto.ajax({
                            type: 'post',
                            url: ApiUrl + '?ctl=Buyer_Order&met=addOrder&typ=json',
                            data: {
                                receiver_name: address_contact,
                                receiver_address: address_address,
                                receiver_phone: address_phone,
                                area_code:area_code,
                                invoice: invoice,
                                invoice_id: invoice_id,
                                invoice_title: invoice_title,
                                invoice_content: invoice_content,
                                cart_id: cart_id,
                                shop_id: shop_id,
                                remark: remark,
                                pay_way_id: pay_way_id,
                                address_id: address_id,
                                k: key,
                                u: getCookie('id'),
                                from: "wap",
                                increase_arr: increase_arr, //加价购
                                voucher_id: voucher_ids, // 代金券
                                redpacket_id: redpacket_id, //平台红包
                                is_discount: is_discount,//是否开启会员折扣
                                seckill:seckill,
                                seckill_goods_id:seckill_goods_id,
                                presale:presale,
                                final_mobile:final_mobile,
                            },
                            dataType: "json",
                            success: function (a) {
                                if (a.status == 200) {
                                    delCookie('cart_count');
                                    //重新计算购物车的数量
                                    getCartCount();
                                    if (pay_way_id == 1) {
                                        window.location.href = PayCenterWapUrl + "/?ctl=Info&met=pay&uorder=" + a.data.uorder + '&typ=e';
                                        return false;
                                    } else {
                                        window.location.href = WapSiteUrl + '/tmpl/member/order_list.html';
                                        return false;
                                    }
                                } else {
                                    if (a.msg != 'failure') {
                                        /* Public.tips.error(a.msg);*/
                                        Zepto.sDialog({
                                            content: a.msg,
                                            okBtn: false,
                                            cancelBtnText: '返回',
                                            cancelFn: function () { /*history.back();*/
                                            }
                                        });
                                    } else {
                                        /*Public.tips.error('订单提交失败！');*/
                                        Zepto.sDialog({
                                            content: '订单提交失败！',
                                            okBtn: false,
                                            cancelBtnText: '返回',
                                            cancelFn: function () { /*history.back();*/
                                            }
                                        });
                                    }
                                }
                            },
                            failure: function (a) {
                                Public.tips.error('操作失败！');
                            }
                        });
                    }
                            }, 
                            "cancelFn": function() {
                               return;
                            } 
                        });

        }else{

                var buy_able = 1;
                var goods_name = '';
                Zepto('.buy-item').each(function () {
                    if (Zepto(this).data('buy_able') == 0) {
                        buy_able = Zepto(this).data('buy_able');
                        goods_name = Zepto(this).data('goods_name');
                        return false;
                    }

                });
                if (buy_able == 0) {
                    Zepto.sDialog({
                        content: '商品【' + goods_name + '】不在配送范围，请更换收货地址或者选择其他商品！',
                        okBtn: true,
                        dialogClass:"ceshi",
                        cancelBtn: true,
                        cancelBtnText: '取消',
                        okBtnText: '返回购物车',
                        okFn: function () {
                            history.back();
                        }
                    });
                    return false;
                }

                if (Zepto("#totalPayPrice").html() >= 99999999.99) {
                    Zepto.sDialog({
                        content: '订单金额过大，请分批购买！',
                        okBtn: false,
                        cancelBtnText: '返回',
                        cancelFn: function () {
                            history.back();
                        }
                    });
                    return;
                }

                //1.获取收货地址
                address_contact = Zepto("#true_name").html();
                address_address = Zepto("#address").html();
                address_phone = Zepto("#mob_phone").html();
                address_id = Zepto("#address_id").val();
                area_code = Zepto("#area_code").val();
                if (address_id == 'undefined') {
                    Zepto.sDialog({
                        skin: "red",
                        content: '请选择收货地址！',
                        okBtn: false,
                        cancelBtn: false
                    });

                    return false;
                }

                //2.获取发票信息
                var invoice = Zepto("#invContent").html();
                var invoice_id = Zepto("#order_invoice_id").val();
                var invoice_title = Zepto("#order_invoice_title").val();
                var invoice_content = Zepto("#order_invoice_content").val();
                //3.获取商品信息（商品id，商品备注）
                var cart_id = [];//定义一个数组
                Zepto("input[name='cart_id']").each(function () {
                    cart_id.push(Zepto(this).val());//将值添加到数组中
                });

                var remark = [];
                var shop_id = [];
                Zepto("input[name='remarks']").each(function () {
                    shop_id.push(Zepto(this).attr("rel"));
                    remark.push(Zepto(this).val());//将值添加到数组中
                });

                //平台红包
                var redpacket_id = Zepto("input[name='red-price']").data('rpt_id');
                /****************获取促销信息****************/
                    //代金券
                var voucher_ids = [];
                Zepto("input[name='best_voucher_price']").each(function () {
                    voucher_ids.push(Zepto(this).data('voucher_id'));
                })
                var promotion_rows = getAllPromotionData(),
                    increase_arr = []; //加价购
                    goods_id = [];//规则id
                    increase_goods_id = [];//规则id
            if (promotion_rows !== false && !Zepto.isEmptyObject(promotion_rows)) {
                    // for (var k_shop_id in promotion_rows) {
                    //     // promotion_rows[k_shop_id].voucher_id && voucher_ids.push(promotion_rows[k_shop_id].voucher_id);
                    //     if (promotion_rows[k_shop_id].jjg_goods_data) {
                    //         for (var i = 0, promotion_data = promotion_rows[k_shop_id].jjg_goods_data, length = promotion_data.length; i < length; i++) {
                    //             increase_arr.push({
                    //                 increase_shop_id: k_shop_id,
                    //                 increase_goods_id: promotion_data[i].goods_id,
                    //                 increase_goods_num: promotion_data[i].goods_num,
                    //                 increase_price: promotion_data[i].goods_promotion_price
                    //             });
                    //         }
                    //     }
                    // }
                    if(promotion_rows.length > 0)
                        {
                            for (var i = 0; i < promotion_rows.length; i++) {
                                increase_arr.push({
                                    increase_shop_id: promotion_rows[i]['shop_id'],
                                    increase_goods_id: promotion_rows[i]['goods_id'],
                                    increase_goods_num: promotion_rows[i]['goods_num'],
                                    increase_price: promotion_rows[i]['goods_promotion_price'],
                                    rule_id: promotion_rows[i]['rule_id'],
                                });
                                goods_id.push(promotion_rows[i]['promotion_goods_id']);
                            }
                        }
                }

                // for(var v = 0;v < goods_id.length;v++)
                // {
                //     if (goods_id.indexOf(goods_id[v]) == v)
                //     {
                //         increase_goods_id.push(goods_id[v]);
                //     }
                // }
                // if(goods_id.length > 1 && increase_goods_id.length == 1)
                // {
                //     Zepto.sDialog({
                //         content: '同一件商品只能选择一件加价购商品',
                //         okBtn:false,
                //         cancelBtn:false,
                //         cancelFn: function() { /*history.back();*/ }
                //     });
                //     return false;
                // }
                /****************获取促销信息****************/
                if (!address_id) {
                    Zepto.sDialog({
                        content: '请填写收货地址！',
                        okBtn: false,
                        cancelBtnText: '返回',
                        cancelFn: function () { /*history.back();*/
                        }
                    });
                    return false;
                }
                //获取支付方式
                var pay_way_id = Zepto('[name="pay-selected"]:checked').val();
                var seckill = getQueryString('seckill');
                var seckill_goods_id = getQueryString('seckill_goods_id');
                var presale = $("#presale").val();
                var final_mobile = Zepto('#final_mobile').val();
                var seckill_goods_num = Zepto('#seckill_goods_num').val();
                if(seckill_goods_id){
                    Zepto.ajax({
                        type: 'post',
                        url: ApiUrl + '?ctl=Buyer_Order&met=seckill&typ=json',
                        data: {
                           k: key,
                           u: getCookie('id'), 
                           seckill_goods_id:seckill_goods_id,
                           goods_num:seckill_goods_num
                        },
                        dataType: "json",
                        success: function (a) {
                            if(a.status==200){
                                Zepto.ajax({
                                    type: 'post',
                                    url: ApiUrl + '?ctl=Buyer_Order&met=addOrder&typ=json',
                                    data: {
                                        receiver_name: address_contact,
                                        receiver_address: address_address,
                                        receiver_phone: address_phone,
                                        area_code:area_code,
                                        invoice: invoice,
                                        invoice_id: invoice_id,
                                        invoice_title: invoice_title,
                                        invoice_content: invoice_content,
                                        cart_id: cart_id,
                                        shop_id: shop_id,
                                        remark: remark,
                                        pay_way_id: pay_way_id,
                                        address_id: address_id,
                                        k: key,
                                        u: getCookie('id'),
                                        from: "wap",
                                        increase_arr: increase_arr, //加价购
                                        voucher_id: voucher_ids, // 代金券
                                        redpacket_id: redpacket_id, //平台红包
                                        is_discount: is_discount,//是否开启会员折扣
                                        seckill:seckill,
                                        seckill_goods_id:seckill_goods_id,
                                    },
                                    dataType: "json",
                                    success: function (a) {
                                        if (a.status == 200) {
                                            delCookie('cart_count');
                                            //重新计算购物车的数量
                                            getCartCount();
                                            if (pay_way_id == 1) {
                                                window.location.href = PayCenterWapUrl + "/?ctl=Info&met=pay&uorder=" + a.data.uorder + '&typ=e';
                                                return false;
                                            } else {
                                                window.location.href = WapSiteUrl + '/tmpl/member/order_list.html';
                                                return false;
                                            }
                                        } else {
                                            if (a.msg != 'failure') {
                                                /* Public.tips.error(a.msg);*/
                                                Zepto.sDialog({
                                                    content: a.msg,
                                                    okBtn: false,
                                                    cancelBtnText: '返回',
                                                    cancelFn: function () { /*history.back();*/
                                                    }
                                                });
                                            } else {
                                                /*Public.tips.error('订单提交失败！');*/
                                                Zepto.sDialog({
                                                    content: '订单提交失败！',
                                                    okBtn: false,
                                                    cancelBtnText: '返回',
                                                    cancelFn: function () { /*history.back();*/
                                                    }
                                                });
                                            }
                                        }
                                    },
                                    failure: function (a) {
                                        Public.tips.error('操作失败！');
                                    }
                                });
                                //alert('秒杀成功');
                            }else{
                                Zepto.sDialog({
                                    content: a.msg,
                                    okBtn: false,
                                    cancelBtnText: '返回',
                                    cancelFn: function () { /*history.back();*/
                                    }
                                });

                            }
                            
                        },
                        failure: function (a) {
                            Public.tips.error('操作失败！');
                        }
                    });
                }else{
                    Zepto.ajax({
                        type: 'post',
                        url: ApiUrl + '?ctl=Buyer_Order&met=addOrder&typ=json',
                        data: {
                            receiver_name: address_contact,
                            receiver_address: address_address,
                            receiver_phone: address_phone,
                            area_code:area_code,
                            invoice: invoice,
                            invoice_id: invoice_id,
                            invoice_title: invoice_title,
                            invoice_content: invoice_content,
                            cart_id: cart_id,
                            shop_id: shop_id,
                            remark: remark,
                            pay_way_id: pay_way_id,
                            address_id: address_id,
                            k: key,
                            u: getCookie('id'),
                            from: "wap",
                            increase_arr: increase_arr, //加价购
                            voucher_id: voucher_ids, // 代金券
                            redpacket_id: redpacket_id, //平台红包
                            is_discount: is_discount,//是否开启会员折扣
                            seckill:seckill,
                            seckill_goods_id:seckill_goods_id,
                            presale:presale,
                            final_mobile:final_mobile,
                        },
                        dataType: "json",
                        success: function (a) {
                            if (a.status == 200) {
                                delCookie('cart_count');
                                //重新计算购物车的数量
                                getCartCount();
                                if (pay_way_id == 1) {
                                    window.location.href = PayCenterWapUrl + "/?ctl=Info&met=pay&uorder=" + a.data.uorder + '&typ=e';
                                    return false;
                                } else {
                                    window.location.href = WapSiteUrl + '/tmpl/member/order_list.html';
                                    return false;
                                }
                            } else {
                                if (a.msg != 'failure') {
                                    /* Public.tips.error(a.msg);*/
                                    Zepto.sDialog({
                                        content: a.msg,
                                        okBtn: false,
                                        cancelBtnText: '返回',
                                        cancelFn: function () { /*history.back();*/
                                        }
                                    });
                                } else {
                                    /*Public.tips.error('订单提交失败！');*/
                                    Zepto.sDialog({
                                        content: '订单提交失败！',
                                        okBtn: false,
                                        cancelBtnText: '返回',
                                        cancelFn: function () { /*history.back();*/
                                        }
                                    });
                                }
                            }
                        },
                        failure: function (a) {
                            Public.tips.error('操作失败！');
                        }
                    });
                }

        }
        

        
    });


    //会员折扣开关
    Zepto(".rptbutton").on('click', "input[type='checkbox']", function () {
        if (Zepto(this).is(':checked')) {
            is_discount = 1;
        } else {
            is_discount = 0;
        }
        _init(address_id, is_discount);
    });


//初始化加价购和代金券弹框
    function initPromotionWindow() {
        var $trigger_jjg_list = Zepto("div.trigger_shop_jjg"), //加价购
            $trigger_shop_voucher_list = Zepto("a.trigger_shop_voucher"); //代金券
        if ($trigger_jjg_list.length == 0 && $trigger_shop_voucher_list == 0) {
            return false; //订单列表商品没有加价购和代金券活动
        }

        //初始化事件
        $trigger_jjg_list.each(function (i, e) {
            var shop_id = e.id.replace("trigger_shop_jjg_", ""),
                options = {
                    valve: "#trigger_shop_jjg_" + shop_id,
                    wrapper: "#shop_jjg_html_" + shop_id,
                    close: calculateJJG,
                    scroll: ""
                };
            Zepto.animationUp(options);
        });

        $trigger_shop_voucher_list.each(function (i, e) {
            var shop_id = e.id.replace("trigger_shop_voucher_", ""),
                options = {
                    valve: "#trigger_shop_voucher_" + shop_id,
                    wrapper: "#shop_voucher_html_" + shop_id,
                    scroll: ""
                };
            //代金券
            Zepto('#shop_voucher_html_' + shop_id).on('click', "input[type='radio']", function () {
                calculateVoucher(Zepto(this));
            });
            Zepto.animationLeft(options);
        });

        //计算代金券
        function calculateVoucher(trigger_element) {
            var shop_id = trigger_element.data('shop_id'),
                voucher_id = trigger_element.data('id'),
                voucher_price = trigger_element.data('price'),
                voucher_limit = trigger_element.data('limit');

            var current_index = Zepto("#trigger_shop_voucher_" + shop_id).data('current_index');

            //记录原来使用的代金券金额
            var v_price = Zepto("#trigger_shop_voucher_" + shop_id).find("input[name='best_voucher_price']").val();
            //店铺合计金额
            var total = Number(Zepto("#storeTotal" + current_index).html());
            //订单合计金额(订单价格+使用的红包金额)
            var totalPrice = Number((Zepto("#totalPrice").text() * 1 + Number(red_price)).toFixed(2));
            red_price = 0;

            if (voucher_price > 0 && voucher_id > 0) {   //使用代金券
                Zepto("#trigger_shop_voucher_" + shop_id).find('p').addClass("btn-ziti");
                Zepto("#trigger_shop_voucher_" + shop_id).find('p').html("满" + voucher_limit + "减" + voucher_price);
                //订单合计金额 = 订单原合计金额 + 原代金券 - 代金券金额
                totalPrice = (totalPrice + Number(v_price) - Number(voucher_price)).toFixed(2);
                Zepto("#totalPrice").html(totalPrice);//订单合计金额
            } else {
                if (v_price > 0)  //原来使用了代金券
                {   //订单合计 = 订单原合计金额 + 原代金券 - 代金券金额
                    totalPrice = (totalPrice + Number(v_price) - Number(voucher_price)).toFixed(2);
                    Zepto("#totalPrice").html(totalPrice);//订单合计金额
                }

                //判断是否有可用的代金券
                isbest = Zepto("#trigger_shop_voucher_" + shop_id).find("input[name='best_voucher_price']").attr('is-best');

                if (isbest == 1) {
                    Zepto("#trigger_shop_voucher_" + shop_id).find('p').html("不使用店铺代金券");
                    Zepto("#trigger_shop_voucher_" + shop_id).find('p').removeClass("btn-ziti");
                } else {
                    Zepto("#trigger_shop_voucher_" + shop_id).find('p').html("暂无可用店铺代金券");
                    Zepto("#trigger_shop_voucher_" + shop_id).find('p').removeClass("btn-ziti");
                }

            }
            //店铺合计 = 店铺原合计金额 + 原代金券金额 - 代金券金额
            Zepto("#storeTotal" + current_index).html((total + Number(v_price) - Number(voucher_price)).toFixed(2));//本店合计金额
            Zepto("#trigger_shop_voucher_" + shop_id).find("input[name='best_voucher_price']").val(voucher_price);//记录使用的代金券金额
            Zepto("#trigger_shop_voucher_" + shop_id).find("input[name='best_voucher_price']").data('voucher_id', voucher_id); //记录使用的代金券id

            //礼包商品使用代金券
            if (Number(voucher_price) > 0 || Number($(".msprice" + shop_id).val())) {
                $("#ToBuyStep").parent().removeClass('hide');
                $("#ToBuyStep2").parent().addClass('hide');
            } else {
                $("#ToBuyStep").parent().addClass('hide');
                $("#ToBuyStep2").parent().removeClass('hide');
            }

            //调用接口获取红包列表，修改平台红包的使用情况
            Zepto.ajax({
                type: 'post',
                url: ApiUrl + "/index.php?ctl=Buyer_Cart&met=getUserRedpacket&typ=json",
                data: {k: key, u: getCookie('id'), order_price: totalPrice},
                dataType: 'json',
                async: false,
                success: function (re) {
                    //初始化红包信息
                    initRedPacket(re, true);
                }
            });

            Zepto('#shop_voucher_html_' + shop_id).find('.header-l > a').click();
        }

        //获得原始价格，并记录上次改变金额数量
        var $totalPrice = Zepto("#totalPrice"),  //合计
            $totalPayPrice = Zepto("#totalPayPrice"); //支付金额

        var store_amount_data = {}, //所有店铺金额信息
            $store_total_list = Zepto(".js_store_total"); //获取所有店铺合计

        $store_total_list.each(function (i, e) {
            var index = e.id.replace("storeTotal", "");
            store_amount_data["store_total_" + index] = Zepto(e);
            store_amount_data["jjg_change_amount_" + index] = 0;
            store_amount_data["voucher_change_amount_" + index] = 0;
        });

        //radio取消
        Zepto(document).off("click", "input[type='radio']");
        Zepto(document).on("click", "input[type='radio']", function () {
            var status = Zepto(this).data("status");
            if (status !== 'true') {
                Zepto(this).data("status", true)
            }
        });

        //加价购商品的数量事件
        Zepto(document).off("click", "a.min, a.max");
        Zepto(document).on("click", "a.min, a.max", function () {
            //判断是否允许改变商品数量
            var $operation = Zepto(this);
            if ($operation.hasClass("disabled")) {
                return false;
            }

            $operation.hasClass("min")
                ? _minusGoodsNum($operation)
                : _addGoodsNum($operation);

            _showCheckedGoodsNum($operation);
        });

        function _showCheckedGoodsNum($operation) {
            var goodsNum = $operation.parents("div.JS_operation").find("input[type='number']").val(),
                $goodsNum = $operation.parents("li").find("div.goods-num"),
                $input = $operation.parents("li").find("input[name^=shop_jjg]");
            console.log($goodsNum);
            if (parseInt(goodsNum)) {
                $input.val(goodsNum)
                $goodsNum.show().find("em").text("x" + goodsNum);
            } else {
                $goodsNum.hide();
            }
        }

        //获取规则信息
        function _getRuleData($operation) {
            var $input = $operation.parents("div.JS_operation").find("input"); //获取input

            //获取当前加价购规则内商品上限
            var $radio = $operation.parents("div.JS_operation").find("input[type='checkbox']"),
                rule_id = $radio.data('rule_id'),
                rule_goods_limit = $radio.data('rule_goods_limit');

            return {
                '$input': $input,
                '$radio': $radio,
                'rule_id': rule_id,
                'rule_goods_limit': rule_goods_limit
            }
        }

        function _minusGoodsNum($operation) {
            var rule_data = _getRuleData($operation),
                $input = rule_data.$input,
                $radio = rule_data.$radio,
                changed_num = $input.val() * 1 - 1; //变化后数量
            $input.val(changed_num); //改变数量
            $operation.each(function(){
                var goods_num = Zepto(this).parent().next().find('input').val();
                if(goods_num <= 0){
                    // Zepto(this).parent().next().find('input').val(0);
                    Zepto(this).parent().next().find('input').val(1);
                    Zepto.sDialog({
                        content: '该商品最少需选购一件！',
                        okBtn: false,
                        cancelBtnText: '关闭',
                        cancelFn: function () {
                        }
                    });
                    return false;
                }
            })
            // if (changed_num == 0) { //锁上当前商品减法
            //     $operation.addClass("disabled");
            // }
            // //解锁当前规则所有的加法
            // $("div.JS_operation").find("a.max").removeClass("disabled");

        }
       function _addGoodsNum($operation) {
            console.log($operation);
            var rule_data = _getRuleData($operation),
                rule_id = rule_data.rule_id,
                // rule_goods_limit = rule_data.rule_goods_limit,
                rule_goods_limit = Zepto("div.jia-gou-height").find("input[type='checkbox']").data("rule_goods_limit"),
                $input = rule_data.$input,
                $radio = rule_data.$radio,
                now_goods_num = 0;
            $input.val($input.val() * 1 + 1); //改变数量
            $operation.each(function(){
                var goods_num = Zepto(this).parent().prev().find('input').val();
                var rule_name = Zepto(this).parent().prev().find('input').attr('name');
                var rule_limit = rule_name.replace('jjg_goods','#shop_jjg');
                var goods_limit = Zepto(rule_limit).data('rule_goods_limit');
                if(goods_limit != null && goods_num > goods_limit){
                    Zepto(this).parent().prev().find('input').val(goods_limit);
                }
            })


            //获取当前规则下所有商品数量
            // $("input[type=number]").each(function (i, e) {
            //     now_goods_num += e.value * 1;
            // });
            //解锁当前规则所有符合条件的减法
            // $("div.JS_operation").find("a.min").each(function (i, e) {
            //     var $current_input = $(e).parents("div.JS_operation").find("input");
            //     if ($current_input.val() > 0) {
            //         // $(e).removeClass("disabled");
            //     }
            // });
            //当前规则下所有商品加法禁用
            // if (now_goods_num == rule_goods_limit) {
            //     $("div.JS_operation").find("a.max").each(function (i, e) {
            //         // $(e).addClass("disabled");
            //     });
            // }
        }

        //计算加价购金额
        function calculateJJG(trigger_element) {
            var shop_id = trigger_element.id.replace("trigger_shop_jjg_", ""),
                current_index = Zepto(trigger_element).data("current_index"),
                $radio = Zepto("#shop_jjg_html_" + shop_id).find("input[type='checkbox']:checked");

            restoreAmount(current_index, "jjg"); //还原当前价格，重新计算

            if ($radio.length == 0) {
                showJJGChecked(false, shop_id);
                store_amount_data["jjg_change_amount_" + current_index] = 0;
                return false;
            }
            _showCheckedGoodsNum
            var rule_id = $radio.data("rule_id"),
                // $input_list = Zepto("input[name=\"jjg_goods" + rule_id + "\"]"),
                $input_list = Zepto("input[name^=shop_jjg]"),
                checked_goods_price_sum = 0,
                checked_increase_id = [],
                checked_limit = [],
                checked_goods_rows = [];

            $input_list.each(function (i, e) {
                if (this.value > 0 && $(this).is(":checked") == true) {
                    checked_goods_rows.push({
                        'goods_promotion_price': Zepto(e).data("promotion_price"),
                        'goods_num': this.value
                    });
                    checked_goods_price_sum += Zepto(e).data("promotion_price") * this.value;

                    //处理加价购购买数量限制
                    if (Zepto(e).data("promotion_type") == 'jjg'){
                        var rule_id = Zepto(e).data("rule_id");
                        var goods_limit = Zepto(e).data("goods_limit");
                        checked_increase_id.push(rule_id);
                        checked_limit[rule_id] = goods_limit;
                    }
                }
            });

            var map = [];
            for (var i = 0; i < checked_increase_id.length; i++) {
                var n = checked_increase_id[i];
                if (!map[n]) {
                    map[n] = 1;
                } else {
                    map[n]++;
                }
            }

            //加价购购买数量限制
            console.log(checked_increase_id)
            console.log(checked_limit)
            if (checked_increase_id && checked_limit) {
                for (let m in checked_limit) {
                    //判断同意规则下 购买商品数量
                    if (checked_limit[m] < map[m] && checked_limit[m] > 0) {
                        Zepto.sDialog({
                            content: '加价购商品数量超出限制！',
                            okBtn: false,
                            cancelBtnText: '关闭',
                            cancelFn: function () {
                            }
                        });
                        return false;
                    }
                }
            }

            if (checked_goods_price_sum == 0) {
                showJJGChecked(false, shop_id);
                return false; //不参与加价购活动
            }

            showJJGChecked(true, shop_id, checked_goods_rows); //展示选择加价购信息
            changeMoney(current_index, checked_goods_price_sum, "add", "jjg"); //计算金额
        }

        //改变金额
        function changeMoney(index, amount, operation_type, promotion_type) {
            var discount = 0;
            if (operation_type == "minus") {
                amount = -amount;
            }

            if (promotion_type == "jjg") {
                store_amount_data["jjg_change_amount_" + index] = amount;
                //判断会员是否开启了会员折扣，平台设置的折扣规则，添加加价购商品的店铺是否是自营店铺
                if (is_discount) {
                    //计算会员折扣
                    jja_discount = amount * (100 - user_rate) / 100;
                    discount = jja_discount;
                    amount = amount - jja_discount;
                    //修改会员折扣处的优惠金额
                    order_discount = Zepto(".discount").val() * 1 + jja_discount;
                    Zepto(".discount").val(order_discount);
                    Zepto("#ratePrice").html('减￥' + order_discount);
                    Zepto("#ratePrice").parent().addClass("default-color");
                }
            } else {
                store_amount_data["voucher_change_amount_" + index] = amount;
            }

            var $storeTotal = store_amount_data["store_total_" + index];

            $storeTotal.text(($storeTotal.text() * 1 + amount + discount).toFixed(2));
            $totalPrice.text(($totalPrice.text() * 1 + amount).toFixed(2));
            $totalPayPrice.text(($totalPayPrice.text() * 1 + amount).toFixed(2));
        }

        //还原金额
        function restoreAmount(index, promotion_type) {
            var changeAmount, //改变金额
                $storeTotal = store_amount_data["store_total_" + index],
                storeTotalVal = $storeTotal.text() * 1,
                totalPriceVal = $totalPrice.text() * 1,
                totalPayPriceVal = $totalPayPrice.text() * 1;

            //首先执行还原操作
            if (promotion_type == "jjg") {
                changeAmount = store_amount_data["jjg_change_amount_" + index];

                if (is_discount && changeAmount > 0) {
                    //计算会员折扣
                    jja_discount = changeAmount * (100 - user_rate) / 100;
                    changeAmount = changeAmount - jja_discount;

                    //修改会员折扣处的优惠金额
                    order_discount = Zepto(".discount").val() * 1 + jja_discount;
                    Zepto(".discount").val(order_discount);
                    Zepto("#ratePrice").html('减￥' + order_discount);
                    Zepto("#ratePrice").parent().addClass("default-color");
                }
            } else {
                changeAmount = store_amount_data["voucher_change_amount_" + index];
            }

            storeTotalVal -= changeAmount;
            totalPriceVal -= changeAmount;
            totalPayPriceVal -= changeAmount;

            $storeTotal.text(storeTotalVal.toFixed(2));
            $totalPrice.text(totalPriceVal.toFixed(2));
            $totalPayPrice.text(totalPayPriceVal.toFixed(2));
        }

        //展示选择加价购信息
        function showJJGChecked(checked, shop_id, checked_goods_rows) {
            console.log('加价购选中');
            var $jjg_rule_info = Zepto("#jjg_rule_info" + shop_id),
                $jjg_rule_checked = Zepto("#jjg_rule_checked" + shop_id);

            if (checked) {
                $jjg_rule_info.hide();
                $jjg_rule_checked.empty().show();

                var append_html = "";
                for (var i = 0, len = checked_goods_rows.length; i < len; i++) {
                    append_html += "<li>加价购￥" + checked_goods_rows[i].goods_promotion_price + "X" + checked_goods_rows[i].goods_num + "</li>"
                }

                $jjg_rule_checked.append("<ul>" + append_html + "</ul>");
            } else {
                $jjg_rule_info.show();
                $jjg_rule_checked.hide();
            }
        }

        //展示代金券信息
        function showVoucherChecked(checked, voucher_id) {
            if (checked) {
                Zepto("p.js_voucher_info").hide();
                Zepto("#voucher_info" + voucher_id).show();
            } else {
                Zepto("p.js_voucher_info").show();
            }
        }
    }

    /**
     * 返回需要的加价购、代金券数据
     *
     * return {shop_id: object}
     *
     * object = {
 *     voucher_id: voucher_id,
 *     rule_id: rule_id,
 *     jjg_goods_data: [{
 *         goods_id: goods_id,
 *         goods_num: goods_num,
 *         goods_increase_price: goods_increase_price
 *     }]
 * }
     */
    function getAllPromotionData() {
        var jjg_list = Zepto("input[type='checkbox'][data-promotion_type='jjg']:checked"),
            voucher_list = Zepto("input[type='radio'][data-promotion_type='voucher']:checked"),
            promotion_list = jjg_list.concat(voucher_list);

        if (promotion_list.length == 0) {
            return false;
        }

        var result_data = [];
        Zepto(promotion_list).each(function (i, e) {
            var shop_id,
                promotion_type = Zepto(e).data("promotion_type");
            if (promotion_type == "jjg") {
                shop_id = e.name.replace("shop_jjg", "");
                var jjg_goods_data = [], //选中加价购的商品
                    rule_id = Zepto(e).data("rule_id");

                if (e.value > 0) {
                    jjg_goods_data.push({
                        "goods_id": Zepto(e).data("goods_id"),
                        "goods_num": e.value,
                        "goods_promotion_price": Zepto(e).data("promotion_price"),
                        "shop_id": shop_id,
                        "rule_id": rule_id,
                    });
                }

                if (jjg_goods_data.length > 0) {
                    result_data.push(jjg_goods_data[0]);
                }
            }
        });

        return result_data;
    }

});



