var key = getCookie('key');
var address_id;
Zepto(function() {

    //判断当前页面是否为积分商品页面
    //这是一个坑，积分商品和普通商品混在了一起，要特别小心

    if (window.location.href.indexOf('integral_product_buy.html') == -1) {
        return false;
    }

    var isIntegral = getQueryString("isIntegral"), sumPoints = getQueryString("sumPoints"), point_cart_id = getQueryString("point_cart_id") ? getQueryString("point_cart_id").split(",") : [];
    address_list = [];

    if ( point_cart_id.length > 0 ) {
        Zepto(".check-out").addClass("ok");
    }

    Zepto(".nctouch-cart-block.mt5").remove();

    Zepto(".nctouch-cart-bottom").find("dt").html("支付总积分：");
    Zepto(".nctouch-cart-bottom").find("dd").html("<em id='totalPayPrice'>" + sumPoints + "</em>");

    Zepto('#ToBuyStep2').unbind("click");
    
    function isEmptyObject(e) {
        var t;
        for (t in e)
            return !1;
        return !0
    }
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
            initPointProductList(address_id);
        }
    })
    //加载页面

    var  initPointProductList= function (address_id) {
        Zepto.ajax({
            type:'POST',
            url: ApiUrl  + '?ctl=Points&met=confirm&typ=json',
            data: {
                points_cart_id: point_cart_id,
                address_id: address_id,
                k: key,
                u: getCookie('id')
            },
            dataType: "json",
            success: function( resp ) {
                console.log(resp);
                if ( resp.status == 200 ) {
                        if(typeof(resp.data.address) != 'undefined' && resp.data.address.length>0){
                            address_list = resp.data.address;
                            var default_address = resp.data.address[0];
                            Zepto('#address_id').val(default_address.user_address_id);
                            Zepto('#area_code').val(default_address.area_code);
                            Zepto('#true_name').html(default_address.user_address_contact);
                            Zepto('#mob_phone').html(default_address.user_address_phone);
                            Zepto('#address').html(default_address.user_address_area);
                        }
                        
                        var list = resp.data.items;
                        if(!list){
                            Zepto.sDialog({
                                content: '暂无兑换商品',
                                okBtn:true,
                                okFn: function() {
                                    window.location.href = WapSiteUrl+'/tmpl/integral_cart_list.html';
                                        }
                           });
                        }
    
                    if (Zepto.isEmptyObject(resp.data.address)) {
                        Zepto(".address2").removeClass('packets-type');
                        Zepto(".address1").addClass('packets-type');
                    } else {
                        Zepto(".address2").addClass('packets-type');
                        Zepto(".address1").removeClass('packets-type');
                    }
                    Zepto('.new-address-valve').on('click', function () {
                        Zepto('#new-address-valve').click();
                    });
                    resp.data.address && resp.data.address.length > 0 && insertHtmlAddress(resp.data.address, address_id);
                    Zepto('#point_goods_list').html();
                    for(var k in list){
                        var html = '<li class="buy-item borb1" data-goods_name="'+list[k].points_goods_name+'" ><div class="buy-li clearfix">'+
                            '<div class="goods-pic"><a href="'+WapSiteUrl+'"/tmpl/product_detail.html?goods_id="'+list[k].points_goods_id+'"> <img src="'+list[k].points_goods_image+'"/></a> </div>'+
                            '<dl class="goods-info fl pl-20"><dt class="goods-name"> <a href="'+WapSiteUrl+'"/tmpl/product_detail.html?goods_id="'+list[k].points_goods_id+'"> '+list[k].points_goods_name+'</a> </dt> </dl>'+
                            '<div class="goods-subtotal"> <span class="goods-price"><em>'+list[k].points_goods_points+'</em>积分</span> </div>'+
                            '<div class="goods-num"> <em>x'+list[k].points_goods_choosenum+'</em>  </div>'+
                            '</div></li>';
                        Zepto('#point_goods_list').append(html);
                    }
                    
                } else {
                    Zepto.sDialog({
                        content: data.msg,
                        okBtn:true,
                        okFn: function() {
                            window.location.href = WapSiteUrl+'/tmpl/integral_cart_list.html'; 
                        }
                    });
                }
                jQuery("#re_user_mobile").intlTelInput({
                    utilsScript: "../../js/utils.js"
                });
            }
        })
    }
    //提交订单
    Zepto('#ToBuyStep2').on("click", function() {

        var address_id = Zepto("#address_id").val();

        if(!address_id) {
            if($("#list-address-add-list-ul li").length > 0){
                if( !address_id ) {
                    return Zepto.sDialog({
                        skin:"red",
                        content:'请选择收货地址！',
                        okBtn:false,
                        cancelBtn:false
                    });
                }
            } else {
                return Zepto.sDialog({
                    skin:"red",
                    content:'请填写收货地址！',
                    cancelBtn:false,
                    okBtn:true,
                    okFn: function() {
                        Zepto('#new-address-valve').click();
                    }
                });
            }
        }


        //1.获取收货地址
        var param = {
            k: key,
            u: getCookie('id'),
            receiver_name: Zepto("#true_name").html(),
            receiver_address: Zepto("#address").html(),
            receiver_phone: Zepto("#mob_phone").html(),
            area_code: Zepto("#area_code").val(),
            point_cart_id: point_cart_id,
            remark: Zepto("#remark").val()
        };
        Zepto.ajax({
            type:'POST',
            url: ApiUrl  + '?ctl=Points&met=addPointsOrder&typ=json',
            data: param,
            dataType: "json",
            success: function( data ) {

                if ( data.status == 200 ) {
                    Zepto.sDialog({
                        content: data.msg,
                        okBtn:true,
                        cancelBtn:false,
                        okFn: function() {
                            window.location.href = WapSiteUrl+'/tmpl/member/member.html'; //没有对应页面，先跳到这里
                        }
                    });
                } else {
                    Zepto.sDialog({
                        content: data.msg,
                        okBtn:false,
                        cancelBtnText:'返回',
                        cancelFn: function() { history.back(); }
                    });
                }
            }
        });
    });

    template.helper('isEmpty', function(o) {
        var b = true;
        Zepto.each(o, function(k, v) {
            b = false;
            return false;
        });
        return b;
    });
    
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
    }
    // 地址列表
    Zepto('#list-address-valve').click(function(){
        var address_id = Zepto(this).find("#address_id").val();
        var area_code = Zepto(this).find("#area_code").val();
        var data = new Array();
        data.address_list = address_list;
        data.address_id = address_id;
        data.area_code = area_code;
        var html = template.render('list-address-add-list-script', data);
        Zepto("#list-address-add-list-ul").html(html);
    });
    Zepto.animationLeft({
        valve : '#list-address-valve',
        wrapper : '#list-address-wrapper',
        scroll : '#list-address-scroll'
    });

    // 地区选择
    Zepto('#list-address-add-list-ul').on('click', 'li', function(){
        Zepto(this).addClass('selected').siblings().removeClass('selected');
        eval('address_info = ' + Zepto(this).attr('data-param'));
        Zepto('#true_name').html(address_info.user_address_contact);
        Zepto('#mob_phone').html(address_info.user_address_phone);
        Zepto('#address').html(address_info.user_address_area + address_info.user_address_address);
        Zepto("#address_id").val(address_info.user_address_id);
        Zepto("#area_code").val(address_info.area_code);
        Zepto('#list-address-wrapper').find('.header-l > a').click();
    });

    // 地址新增
    Zepto.animationLeft({
        valve : '#new-address-valve',
        wrapper : '#new-address-wrapper',
        scroll : ''
    });
    
    // 地区选择
    Zepto('#new-address-wrapper').on('click', '#varea_info', function(){

        Zepto.areaSelected({
            success : function(data){
                province_id = data.area_id_1;
                city_id = data.area_id_2;
                area_id = data.area_id_3;
                area_info = data.area_info;
                Zepto('#varea_info').val(data.area_info);
            }
        });
    });
    
    // 地址保存
    Zepto.sValid.init({
        rules:{
            vtrue_name:{required: true, maxlength: 20},
            vmob_phone:{required: true, mobile: true},
            varea_info:"required",
            vaddress:{required: true, maxlength: 100}
        },
        messages:{
            vtrue_name:{required: "姓名必填！", maxlength: "姓名最多20个字符！"},
            vmob_phone:{required: "手机号必填！", mobile: "手机号码不正确！"},
            varea_info:"地区必填！",
            vaddress:{required: "街道必填！", maxlength: "地址最多100个字符！"}
        },
        callback:function (eId,eMsg,eRules){
            if(eId.length >0){
                var errorHtml = "";
                Zepto.map(eMsg,function (idx,item){
                    errorHtml += "<p>"+idx+"</p>";
                });
                errorTipsShow(errorHtml);
            }else{
                errorTipsHide();
            }
        }
    });
    //新增地区
    Zepto('#add_address_form').find('.btn').click(function(){
        if(Zepto.sValid()){
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

            param.user_address_default = 0;

            param.u = getCookie('id');

            Zepto.ajax({
                type:'post',
                url:ApiUrl+"/index.php?ctl=Buyer_User&met=addAddressInfo&typ=json",
                data:param,
                dataType:'json',
                success:function(result){
                    if (result.status == 200) {
                        address_list.push(result.data);
                        Zepto('#true_name').html(result.data.user_address_contact);
                        Zepto('#mob_phone').html(result.data.user_address_phone);
                        Zepto('#address').html(result.data.user_address_area + result.data.user_address_address);
                        Zepto("#address_id").val(result.data.user_address_id);
                        Zepto("#area_code").val(result.data.area_code);
                        Zepto('#new-address-wrapper,#list-address-wrapper').find('.header-l > a').click();
                        Zepto(".address2").addClass('packets-type');
                        Zepto(".address1").removeClass('packets-type');
                    }else{
                        errorTipsShow("<p>" + result.msg + "</p>");
                    }
                }
            });
        }
    });
});


