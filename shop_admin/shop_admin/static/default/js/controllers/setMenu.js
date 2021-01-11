$(function () {

    //是否创建一级菜单
    $("input[name='first']").click(function () {
        var first_is_open = $(this).val();
        if (first_is_open == 1){
            $("#select_first_menu").hide();
            $("#input_first_menu").show();
            $("#second_is_open").hide();
            $("#input_first_menu_icon").show();
        } else{
            $("#select_first_menu").show();
            $("#input_first_menu").hide();
            $("#second_is_open").show()
            $("#input_first_menu_icon").hide();
        }
    });

    //选择一级菜单
    $("#first_menu").change(function () {
        var menu_id = $(this).val();
        if(menu_id > 0){
            Public.ajaxPost(SITE_URL + '?ctl=Config&met=getMenuById&typ=json', {menu_id: menu_id}, function (res) {
               var data = res.data;
               var html = "<option value=''>请选择</option>";
               for (var i in data){
                    html += "<option value='" + data[i].menu_id + "'>" + data[i].menu_name + "</option>";
               }
               $("#second_menu").html(html);
            });
        }
    })

    //是否创建二级菜单
    $("input[name='second']").click(function () {
        var second_is_open = $(this).val();
        if (second_is_open == 1) {
            $("#select_second_menu").hide();
            $("#input_second_menu").show();
            $("#second_menu_note").show();
        } else {
            $("#select_second_menu").show();
            $("#input_second_menu").hide();
            $("#second_menu_note").hide();
        }
    });

    //提交
    $(".submit-btn").click(function () {
        var param = {
            first_is_open: $("input[name='first']:checked").val(),
            first_menu_id:$("#first_menu").val(),
            first_menu_name: $("#first_menu").find("option:selected").text(),
            input_first_menu: $("input[name='input_first_menu']").val(),
            input_first_menu_icon: $("input[name='input_first_menu_icon']").val(),
            second_is_open: $("input[name='second']:checked").val(),
            second_menu_id: $("#second_menu").val(),
            second_menu_name: $("#second_menu").find("option:selected").text(),
            input_second_menu: $("input[name='input_second_menu']").val(),
            second_menu_note: $("input[name='second_menu_note']").val(),
            input_third_menu: $("input[name='input_third_menu']").val(),
            third_menu_ctl: $("input[name='third_menu_ctl']").val(),
            third_menu_met: $("input[name='third_menu_met']").val(),
            third_menu_note: $("input[name='third_menu_note']").val(),
        }
        Public.ajaxPost(SITE_URL + '?ctl=Config&met=setMenuInfo&typ=json', param, function (res) {
            if (res.status == 200){
                Public.tips({content: '创建菜单成功！'});
                setTimeout(function () {
                    window.location.reload();
                },3000)
            } else{
                alert(res.msg)
                Public.tips({content: '创建菜单失败！' + res.msg});
            }
        });
    });
});