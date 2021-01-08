var k = getCookie("key");
var u = getCookie('id');
var param = {};
get_list();
function get_list()
{
    var url = ApiUrl + "/index.php?ctl=Buyer_Order&met=getOrderWuliu&typ=json&ua=wap&order_id="+getQueryString('order_id')+"&k="+k+"&u="+u;

    $.getJSON(url, param, function (e)
    {
        if (!e)
        {
            e = [];
            e.data.items = [];
        }
        var html = template.render("store-lists-area", e);
        $(".store-lists-area").append(html);
        console.log(e);
    })
}