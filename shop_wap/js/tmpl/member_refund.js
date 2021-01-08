$(function () {
    var e = getCookie("key");
    var u = getCookie("id");
    var t = new ncScrollLoad;
    var shop_id_wap = getCookie('SHOP_ID_WAP');
    t.loadInit({
        url: ApiUrl + "/index.php?ctl=Buyer_Service_Return&met=index&typ=json&shop_id_wap="+shop_id_wap,
        getparam: {k: e,u:u},
        tmplid: "refund-list-tmpl",
        containerobj: $("#refund-list"),
        iIntervalId: true,
        data: {WapSiteUrl: WapSiteUrl}
    })
});