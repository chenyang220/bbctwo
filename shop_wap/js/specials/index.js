$(function() {
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Index&met=tsIndex&typ=json&ua=wap",
        type: 'get',
        dataType: 'json',
        success: function(result) {
            if(result.status == 200)
            {
                var r = result;

                var a = template.render("ts_logo_template", r);



                                console.log(a);
                $("#ts_logo").html(a);
                return false;
            }
        }
    })
})