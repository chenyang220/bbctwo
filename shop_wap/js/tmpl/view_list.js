var key = getCookie("key");

$(function () {
    // if (!key) {
    //     window.location.href = WapSiteUrl + "/tmpl/member/login.html";
    //     return false;
    // }
  $.ajax({
        url: ApiUrl + "/index.php?ctl=Buyer_Favorites&met=footprintwap&typ=json",
        data: {k: key, u: getCookie('id')},
        type: 'post',
        dataType: 'json',
        success: function(data) {
            if (data.status == 200) {
                 var r = template.render("viewlist_data", data.data);
                    $("#viewlist").html(r)
                    if($("#viewlist li").hasClass('active'))
                    {
                        $("#viewlist li").removeClass('active');
                        $("#viewlist li").addClass('active');
                    }
            }
        }
    });


    // var e = new ncScrollLoad;
    // e.loadInit({
    //     url: ApiUrl + "/index.php?ctl=Buyer_Favorites&met=footprintwap&typ=json",
    //     getparam: {k: key, u: getCookie('id')},
    //     tmplid: "viewlist_data",
    //     containerobj: $("#viewlist"),
    //     iIntervalId: true,
    //     data: {WapSiteUrl: WapSiteUrl},
    //     callback:function (data) {
    //         if($("#viewlist li").hasClass('active'))
    //         {
    //             $("#viewlist li").removeClass('active');
    //             $("#viewlist li").addClass('active');
    //         }
    //     }
    // });
      


    $("#clearbtn").click(function () {
        var common_id = [];
        $("#viewlist li input[type='checkbox']").each(function(){
            if($(this).is(":checked")) {
                common_id.push($(this).val());
            }
        });
        if(common_id.length > 0)
        {
            // $.sDialog({
            //     autoTime: 2000, //当没有 确定和取消按钮的时候，弹出框自动关闭的时间
            //     skin: "red",
            //     content: "确定删除吗？",
            //     okBtn: true,
            //     cancelBtn: true,
            //     "okBtnText": "确定", //确定按钮的文字
            //     "cancelBtnText": "取消", //取消按钮的文字
            //     "lock": true, //是否显示遮罩
            //     okFn: function () {
                    $.ajax({
                        type: "post",
                        url: ApiUrl + "/index.php?ctl=Buyer_Favorites&met=delFootPrint&typ=json",
                        data: {k: key, u: getCookie('id'),common_id:common_id},
                        dataType: "json",
                        async: false,
                        success: function (e) {
                            if (e.status == 200) {
                                window.setTimeout(function () {
                                    location.href = WapSiteUrl + "/tmpl/member/views_list.html";
                                }, 100);
                            } else {
                                $.sDialog({skin: "red", content: e.data.error, okBtn: false, cancelBtn: false});
                            }
                        }
                    });
            //     }
            // });
        }else{
            $.sDialog({skin: "red", content: '请选择一个商品操作！', okBtn: false, cancelBtn: false});
        }
    });
    $(".js-history-edit").click(function(){
        if($(this).hasClass('active')){
            $(this).html("编辑").removeClass('active');
        }else{
            $(this).html("完成").addClass('active');
        }
        $("#viewlist li").toggleClass("active");
        $(".js-history-bottom").toggle();
    })
    // 全选
    $(".history-selall input").click(function(){

        if($(this).prop("checked")==true){
            $("#viewlist li input[type='checkbox']").prop("checked",true);
        }else{
            $("#viewlist li input[type='checkbox']").prop("checked",false);
        }
    })
     // 去除全选
      $('#viewlist').on('click', 'input[type="checkbox"]', function () {
         var _has = false
         $('#viewlist li input[type="checkbox"]').each(function () {
            if (!($(this).prop('checked'))) {
                _has = true;
            }
        });
        if (_has) {
            $('.history-selall input').prop('checked', '');
        } else {
            $('.history-selall input').prop('checked', 'checked');
        }
    });

});
