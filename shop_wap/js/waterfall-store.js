
// 瀑布流
function waterFall(columns) {
    // 1- 确定图片的宽度 - 滚动条宽度
    var pageWidth = getClient().width-15;
    var columns = columns; //列数
    var itemWidth = parseInt(pageWidth/columns); //得到item的宽度
	$(".masonry .item").css('width',itemWidth); //设置到item的宽度
    var arr = [];
	console.log($(".masonry").length);
	for(var v=0;v<$(".masonry").length;v++){
		$(".masonry").eq(v).find("li").each(function(i,e){
			var height = $(this).height();
			var width = $(this).width();
			 var bi = itemWidth/width; //获取缩小的比值
			 var boxheight = parseInt(height*bi); //图片的高度*比值 = item的高度
			 if (i < columns) {
			     // 2- 确定第一行
			     $(this).css({
			         top:0,
			         left:(itemWidth) * i
			     });
			     arr.push(boxheight);
			 } else {
			     // 其他行
			     // 3- 找到数组中最小高度  和 它的索引
			     var minHeight = arr[0];
			     var index = 0;
			     for (var j = 0; j < arr.length; j++) {
			         if (minHeight > arr[j]) {
			             minHeight = arr[j];
			             index = j;
			         }
			     }
			     // 4- 设置下一行的第一个盒子位置
			     // top值就是最小列的高度 
			     $(this).css({
			         top:arr[index],
			         left:$(".masonry").eq(v).find(".item").eq(index).css("left")
			     });
			 
			     // 5- 修改最小列的高度 
			     // 最小列的高度 = 当前自己的高度 + 拼接过来的高度
			     arr[index] = arr[index] + boxheight;
			 }
		})
		let max = arr[0];
		for (let i = 0; i < arr.length - 1; i++) {
				  max = max < arr[i+1] ? arr[i+1] : max
		}
		$(".masonry").eq(v).css('height',max);
	}
	
 }
 
 
 //clientWidth 处理兼容性
 function getClient() {
     return {
         width: window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
         height: window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
     }
 }
 