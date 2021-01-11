$(document).ready(function(){
	$(".submit-btn").click(function (){
		var ajax_url = BASE_URL+'index.php?ctl=Promotion_RebateRatio&met=saveDetail&typ=json';
		$.ajax({
            type: 'POST',
            url: ajax_url,
            data: $("#rebate_ratio_list").serialize(),
            success: function (a) {
                if(a.status==200){
                	alert("保存成功！");
                	windows.location.reload();
                }else{
                	alert(a.msg);
                }
            }
        });
	})
})