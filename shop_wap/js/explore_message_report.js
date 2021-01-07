var page = 10;
var pagesize = 10;
var curpage = 0;
var hasMore = true;
var footer = false;

$(function () {
    get_detail();

    function get_detail() {
        if (!hasMore) {
            return false
        }
        hasMore = true;

        $.ajax({
            url: ApiUrl + "/index.php?ctl=Explore_Explore&met=getReportMessage&typ=json",
            type: "POST",
            data: {
                k: getCookie("key"),
                u: getCookie("id"),
                firstRow:curpage,
            },
            dataType: "json",
            success: function (e) {
                if (e.status == 200) {
                    curpage = e.data.page * pagesize;
                    if(page > e.data.records)
                    {
                        hasMore = false;
                    }

                    if (!hasMore) {
                        get_footer()
                    }


                    if (e.data.items.length <= 0) {
                        if(curpage == 10){
                            $('.social-nodata').removeClass('hide');
                        }
                    } else {
                        $('.social-nodata').addClass('hide');
                    }

                    var report_html = template.render('report-more', e);
                    $("#report").append(report_html);

                } else {
                    $.sDialog({
                        skin: "red",
                        content: '获取数据失败，请刷新重试！',
                        okBtn: false,
                        cancelBtn: false
                    });
                }
            }
        });
    }

    function get_footer() {
        if (!footer) {
            footer = true;
            $.ajax({url: "../../js/tmpl/footer.js", dataType: "script"})
        }
    }

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() > $(document).height() - 1) {
            get_detail()
        }
    })
})