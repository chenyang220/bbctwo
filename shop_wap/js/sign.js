var key = getCookie('key');
var u = getCookie('id');
var page = pagesize;
var curpage = 1;
var firstRow = 0;


 $(function (){
 	//获取会员积分'); ?>
    $.getJSON(ApiUrl + '/index.php?ctl=Buyer_Order&met=getUserInfo&typ=json', {
        'k': key,
        'u':u,
    }, function (result) {
        //我的积分'); ?>
        $("#pointnum").html(result.data.points.user_points);
        $("#continuation_sign").html(result.data.info.user_sign_day);


        var html = template.render("sign_heard_template", result.data);
        $("#sign_heard").html(html);


        if (result.data.sign_satus == 1) {
            $("#sign_btn").html("签到成功").addClass('active');
        }
        

        var points_log_flag_arr = result.data.points_log_flag_arr;

        console.log(points_log_flag_arr);
        $(".btn-zk-exchange").each(function(){
            var type = $(this).attr("date-type");
            if (points_log_flag_arr.indexOf(type) >= 0 ) {
                $(this).css("background-color","#666");
                $(this).html("领取成功");
            }
        });
    });

    //代金券展示

    var param = {};
    $.getJSON(ApiUrl + "/index.php?ctl=Voucher&met=vList&typ=json", param, function (data) {
        if (data.status == 200) {
            var html = template.render("voucher_list_template", data.data.voucher);
            $("#voucher_list").html(html);
        }
    });

    //登录签到
    $("#sign_btn").click(function(){
        $.ajax({
            type:'post',
            url: ApiUrl + "/index.php?ctl=Buyer_User&met=userInfoSign&typ=json",
            data: {k: key, u: getCookie('id')},
            dataType: 'json',
            success: function (result) {

                if (result.status == 200) {
                    if (result.data.sign_satus == 1) {
                        $("#continuation_sign").html(result.data.user_sign_day);
                        $("#sign_btn").html("签到成功").addClass('active');
                        $(".day").addClass("active signed");
                        $.sDialog({
                            content: result.msg,
                            okBtn: false,
                            cancelBtnText: '返回',
                            cancelFn: function () { 
                            }
                        });
                    } 
                } else {
                    $.sDialog({
                        content: result.msg,
                        okBtn: false,
                        cancelBtnText: '返回',
                        cancelFn: function () { 
                        }
                    });
                }
            }
        });
    });


    //领取第三方代金券
    $(".btn-zk-exchange").click(function(){
        var  points_log_flag = $(this).attr("date-type");
        var  src = $(this).attr("date-src");
        var tt = $(this);
        $.ajax({
            type:'post',
            url: ApiUrl + "/index.php?ctl=Buyer_User&met=userInfoVoucher&typ=json",
            data: {k: key, u: getCookie('id'),points_log_flag:points_log_flag},
            dataType: 'json',
            success: function (result) {
                if (result.status == 200) {
                        $(tt).css("background-color","#666");
                        $(tt).html("领取成功");
                        $.sDialog({
                            content: result.msg,
                            okBtn: false,
                            cancelBtnText: '返回',
                            cancelFn: function () { 
                            }
                        });
                    location.href = src;
                } else {
                    $.sDialog({
                        content: result.msg,
                        okBtn: false,
                        cancelBtnText: '返回',
                        cancelFn: function () { 
                        }
                    });
                }
            }
        });
    });



    $(".zk-level-sign").click(function(){
        $("#sign_state").removeClass("hide");
    })


    $(".btn_state").click(function(){
        $("#sign_state").addClass("hide");
    })


    $("#voucher_list").on('click', "[nctype='exchange_integrate']", function() {

        //领取代金券
        if(!getCookie("id")){
            $.sDialog({skin: "red", content: '用户尚未登录！', okBtn: false, cancelBtn: false});
            return false;
        }
        var v_id = $(this).data("vid");
        $.ajax({
            url: ApiUrl + "/index.php?ctl=Voucher&met=getVoucherById&typ=json",
            data: {vid: v_id},
            type: 'post',
            dataType: 'json',
            success: function(data) {
                if ( data.status == 200 ) {
                    var data = data.data, voucher_t_eachlimit = data.voucher_t_eachlimit;
                    var limit = '';
                    if(voucher_t_eachlimit == 0){
                        limit = "每个ID领取无限制";
                    }else{
                        limit = "每个ID限领" + voucher_t_eachlimit + "张";
                    }

                    $.sDialog({ skin: "red",
                        content: limit,
                        okBtn: true,
                        cancelBtn: true,
                        okFn: function () {
                            $.ajax({
                                url: ApiUrl + "/index.php?ctl=Voucher&met=receiveVoucher&typ=json",
                                data: {vid: v_id,k: getCookie("key"),u: getCookie("id")},
                                type: 'post',
                                dataType: 'json',
                                success: function (data) {
                                    $.sDialog({
                                        skin: "red",
                                        content: data.msg,
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                }
                            })
                        }
                    });
                } else {
                    $.sDialog({skin: "red", content: "网络异常", okBtn: false, cancelBtn: false});
                }
            }
        });
    });
 })


     


 