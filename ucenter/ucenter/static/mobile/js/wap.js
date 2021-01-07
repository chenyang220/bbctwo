$(function(){
	// 密码是否可见
	$(".btn-open-see").click(function(){
		if($(this).hasClass("icon-bukeshi")){
			$(this).removeClass("icon-bukeshi").addClass("icon-keshi");
			$('#re_user_password').attr('type','text');
		}else{
			$(this).removeClass("icon-keshi").addClass("icon-bukeshi");
			$('#re_user_password').attr('type','password');
		}
	})
		//查看注册协议
	$(".btn-open-agreement").click(function(){
		$("#reg_protocol").show();
	})
	//关闭弹框
	$(".btn-close-mask").click(function(){
		$(this).parents("#reg_protocol").hide();
	})


	//查看隐私协议
	$(".btn-open-agreements").click(function(){
		$("#privacy_protocol").show();
	})
	//关闭弹框
	$(".btn-close-mask").click(function(){
		$(this).parents("#privacy_protocol").hide();
	})

})