$(function () {
    var k = getCookie("key");
    var u = getCookie("id");
    var page = pagesize;
    var curpage = 0;
    var reset = true;

    init()//初始化加载
    function init(){
        if (reset) {
            curpage = 0;
        }
        $.ajax({
            type: "post",
            url: ApiUrl + "/index.php?ctl=Buyer_Message&met=message&typ=json&firstRow=" + curpage,
            data: {k: k, u: u},
            dataType: "json",
            success: function (e) {
                curpage = e.data.page * pagesize;
                if (e.status == 200) {
                    var r = template.render("bargain-info-lists-tmpl", e.data);
                    if (reset) {
                        reset = false;
                        $(".message-notice-items").html(r);
                    } else {
                        $(".message-notice-items").append(r)
                    }
                }
            }
        });
    }

    //滚动加载
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 1) {
            init();
        }
    })

});

