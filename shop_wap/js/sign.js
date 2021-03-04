 var key = getCookie('key');
 var u = getCookie('id');



 $(function (){
 	//获取会员积分'); ?>
    $.getJSON(ApiUrl + '/index.php?ctl=Buyer_Index&met=getUserInfo&typ=json', {
        'k': key,
        'u':u,
    }, function (result) {
        //我的积分'); ?>
        $("#pointnum").html(result.data.points.user_points);


        console.log(result.data.info.user_sign_day)
        $("#continuation_sign").html(result.data.info.user_sign_day);


        var html = template.render("sign_heard_template", result.data);
        $("#sign_heard").html(html);
        
    });


 })