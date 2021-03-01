$(function () {
    var e = getCookie("key");
    var u = getCookie("id");
    var t = new ncScrollLoad;
    var shop_id_wap = getCookie('SHOP_ID_WAP');

    $.ajax({
            type: "post", 
            url: ApiUrl + "/index.php?ctl=Buyer_Service_Return&met=index&typ=json&shop_id_wap="+shop_id_wap, 
            data: {k: e, u:u}, 
            dataType: "json", 
            success: function (e)
            {

                if (e.status == 200) {
                    var html = template.render('refund-list-tmpl', e.data);
                    Zepto("#refund-list").html(html);
                    console.log(e);
                } else {
                     Zepto.sDialog({
                            skin: "red",
                            content: '请登录！',
                            okBtn: false,
                            cancelBtn: false
                    });
                }
                

            }
        })
    // t.loadInit({


    //     url: ApiUrl + "/index.php?ctl=Buyer_Service_Return&met=index&typ=json&shop_id_wap="+shop_id_wap,
    //     getparam: {k: e,u:u},
    //     tmplid: "refund-list-tmpl",
    //     containerobj: $("#refund-list"),
    //     iIntervalId: true,
    //     data: {WapSiteUrl: WapSiteUrl}
    // })
});