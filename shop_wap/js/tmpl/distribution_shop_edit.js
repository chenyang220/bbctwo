$(function (){
	//图片上传
	$('#img').change(function(){
			var formData = new FormData();
			formData.append("picture",document.getElementById("img").files[0]);
            $.ajax({ 
                type: 'post', 
                url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_UploadWap&met=imgUpload&typ=json",
                data: formData, 
                cache: false, 
                processData: false, 
                contentType: false, 
                success:function(data){
                    console.log(data);
                    if(data.data){
                    	$('input[name="distribution_logo"]').val(data.data.file_path);
                        $("#shop_logo").attr("src",data.data.file_path);
                    }else{
                        alert("上传失败");
                    }
                },
                error:function (data) { 
                   alert("上传失败");
                },
                complete:function(data){
                    console.log(data);
                }
            }); 
        });

	//数据提交
	$(document).on('click','.submit-but',function () {
		var distribution_name = $('input[name="distribution_name"]').val();
		var distribution_logo = $('input[name="distribution_logo"]').val();
		var distribution_desc = $('textarea[name="distribution_desc"]').val();
		var distribution_phone = $('input[name="distribution_phone"]').val();
        if(distribution_phone){
            var reg = /^1[3-9]\d{9}$/;
            if (!reg.test(distribution_phone)) {
                    alert('请输入正确的手机号码');
                    return
                }
        }
		var distribution_template = $('input[name="distribution_template"]:checked').val();
		$.ajax({ 
            type: 'post', 
            url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=saveDistributionShopInfo&typ=json",
            data: {k: getCookie("key"), u: getCookie("id"),distribution_name:distribution_name,distribution_logo:distribution_logo,distribution_desc:distribution_desc,distribution_phone:distribution_phone,distribution_template:distribution_template}, 
            dataType: "json",
            success:function(e){
            	if(e.status==200){
            		alert(e.msg);
            		window.location.reload();
            	}else{
            		alert(e.msg);
            	}
            }
        });
	})
});