var pagesize = 20;
var firstRow = 10;

var k = getCookie("key");
var u = getCookie("id");

var state = getQueryString('act');
$(function ()
{
    if (!k || !u)
    {
        window.location.href = WapSiteUrl + "/tmpl/member/login.html";
        return ; 
    }
    
    ajaxVoucher(state);
    
});

function ajaxVoucher(state){
    $.ajax({
        type: "get", url: ApiUrl + "/index.php?ctl=Buyer_RedPacket&met=redPacket&typ=json&state="+state+"&pagesize="+pagesize+"&firstRow="+firstRow, data: {k:k,u:u}, dataType: "json", success: function (e)
        {
            $(".delredpacket").click(function ()
            {
                 $.sDialog({
                     skin: "block", content: "确认清空失效红包吗？", okBtn: true, cancelBtn: true, okFn: function ()
                     {
                        delredpacket()
                     }
                 })
            })
            if (e.status == 200){
               if (e.data.items.length==0){
                  
                    return false;
                }else{
                    s = e.data;
                    // if(e.data.items.length > 0){
                        var t = template.render("redpacket_list", s);
                        $("#v_list").append(t);
                        firstRow = firstRow+10;
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
function delredpacket() {
    $.ajax({
        type: "get",
        url: ApiUrl + "/index.php?ctl=Buyer_RedPacket&met=delRedpacket&typ=json",
        data: {k: k, u: u},
        dataType: "json",
        success: function (e) {
            console.log(e)
            if (e.status == 200) {
                window.location.reload();
                ajaxVoucher(state);
            } else {
                $.sDialog({ skin: "block", content: "暂无可清空的红包", okBtn: false, cancelBtn: false,});
            }
        }
    });
};
