$(function(){
	// html为测试数据
	var columns=1;
	var html='<li class="item"><div class="pad"><span class="goods-pic"><a class="iblock wp100 hp100" href="javascript:;"><img class="wp100" src="../images/spec/img1.png"> </a>'+
			'</span><dl class="goods-info"><dt class="goods-name"><a href="javascript:;"><h4>新疆辣椒干辣皮子西尔丹雪莲辣椒丝原料厚肉辣皮子非大盘鸡线椒</h4> </a></d></p>'+
			'<dd class="goods-sale"> <a href="javascript:;"><p class="label"><label class="label-item">辣椒</label><label class="label-item">干辣线椒</label></p><p><span class="goods-price"><b>￥</b><em>99.00</em></span><b class="had-sale">330人付款</b></p></a></dd>'+
			'<dd class="goods-assist fz0"><a href="javascript:;"> <span>辣妹子店</span><i class="iconfont icon-arrow-right"></i></a></dd></dl></div></li>';			
	window.onscroll=function(){
		 if ($(window).scrollTop() + $(window).height() == $(document).height()) {
			 $(".masonry").append(html);
			  waterFall(columns);
		 }
		 
	}
	$("#menuChange").click(function(){
		if($('.style-change').hasClass('list')){
			$('.style-change').removeClass('list').addClass('grid');
			columns=2;
		}else{
			$('.style-change').removeClass('grid').addClass('list');
			columns=1;
		}
		waterFall(columns);
	})
	// 页面尺寸改变时实时触发
	window.onresize = function() {
	    //重新定义瀑布流
	    // waterFall(columns);
	};
	//初始化
	window.onload = function(){
	    //实现瀑布流
	    waterFall(columns);
	} 
	var handler = function () {
		event.preventDefault();
		event.stopPropagation();
	};
	$(document).on("click", "#ldg_lockmask", function () {
		$(this).remove();
		$(document.body).css("overflow", "auto");
		document.body.removeEventListener('touchmove', handler, false);
		document.body.removeEventListener('wheel', handler, false);
	});
	$.animationLeft(
	    {
	        valve: "#search_adv",
	        wrapper: ".nctouch-full-mask",
	        openCallback: function () {
	            $(".JS-search").css("z-index", 1999);
	            $("body").append("<div id=\"ldg_lockmask\"></div>");
	            $(document.body).css("overflow", "hidden");
	            document.body.addEventListener('touchmove', handler, false);
	            document.body.addEventListener('wheel', handler, false);
	        }
	    }
	);
})