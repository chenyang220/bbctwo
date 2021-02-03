var pagesize = 20;
var curpage = 1;

var k = getCookie("key");
var u = getCookie("id");

var state = getQueryString('act');
$(function ()
{
//     if (!k || !u)
//     {
//         window.location.href = WapSiteUrl + "/tmpl/member/login.html";
//         return ; 
//     }
    
    ajaxVoucher(state);
    
});

function ajaxVoucher(state){
    var shop_id_wap = getCookie('SHOP_ID_WAP');
    $.ajax({
        type: "get", url: ApiUrl + "/index.php?ctl=Buyer_Voucher&met=voucher&typ=json&state="+state+"&pagesize="+pagesize+"&curpage="+curpage+'&shop_id_wap='+shop_id_wap, data: {k:k,u:u}, dataType: "json", success: function (e)
        {
            $(".delvoucher").click(function ()
            {
                // $.sDialog({
                //     skin: "block", content: "确认清空失效代金券吗？", okBtn: true, cancelBtn: true, okFn: function ()
                //     {
                        delvoucher()
                //     }
                // })
            })
            if (e.status == 200){
                if (e.data.items.length==0){
                  
                    return false;
                }else{
                    s = e.data;
                    // if(e.data.items.length > 0){
                        var t = template.render("voucher_list", s);
                        $("#v_list").append(t);
                        curpage ++;
                    // }
                }

            }else{
               return false; 
            }

        }
    });
}

$(window).scroll(function (){
    if ($(window).scrollTop() + $(window).height() > $(document).height() - 1){
        
                ajaxVoucher(state);
          
    }
});
function delvoucher() {
    $.ajax({
        type: "get",
        url: ApiUrl + "/index.php?ctl=Buyer_Voucher&met=delVouchers&typ=json",
        data: {k: k, u: u},
        dataType: "json",
        success: function (e) {
            console.log(e)
            if (e.status == 200) {
                window.location.reload();
                ajaxVoucher(state);
            } else {
                $.sDialog({ skin: "block", content: "暂无可清空的代金券", okBtn: false, cancelBtn: false,});
            }
        }
    });
};
