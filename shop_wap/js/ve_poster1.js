window.onload = function(){
    var goods_id = getQueryString("goods_id");
    var common_id = getQueryString("common_id");
    if (!common_id) {
        common_id = getQueryString("cid");
    }
    /*浏览器的宽高*/
    var width = window.screen.availWidth;
    var height = window.screen.availHeight;
    $('.mask_div').css({
        width: width,
        height: height
    })
    $(document).mouseup(function(e){
      if(e.target.className=='vemask'){
        proPic1.style.display="none";
        proPic2.style.display="none";
    　　}
     });
    $(function () {
        /*点击关闭海报*/
        $('.close_poster').click(function(event) {
            $('.mask_div').hide();
        });

        $('#product_detail_html').on('click', '#bill', function(e) {
             document.body.scrollTop = document.documentElement.scrollTop = 0;
            e.preventDefault();
            var _src = getAttr($("#proPic"),'src');
            if (_src=="") {
                $.sDialog({
                    autoTime:100000,
                    skin: "red",
                    content: "生成海报中...",
                    okBtn: false,
                    cancelBtn: false
                });

                var _srcBill = $("#goods_img_bill").attr('src');
                imgToCanvas()
            }else{
                $('.mask_div').show();
            }
        });
        // if(getCookie(''))
    }) 
    function getAttr(ele, attr) {
        var e = ele.get(0);                
        var attrs = e.attributes;
        if (attrs == undefined) {
            return "";
        } else if (attrs[attr] == undefined) {
            return "";
        } else {
            return attrs[attr].value;
        }
    }

    function imgToCanvas(){
        var p = document.getElementById("poster_pic");
        var scaleBy = 2;
        var box = window.getComputedStyle(p);
        var w = parsePixelValue(box.width, 10);
        var h = parsePixelValue(box.height, 10);
        var canvas = document.createElement('canvas');
        // 就是这里了
        canvas.width = w * scaleBy;
        canvas.height = h * scaleBy;
        canvas.style.width = w + 'px';
        canvas.style.height = h + 'px';
        var context = canvas.getContext('2d');
        context.scale(scaleBy, scaleBy);
    // 使用的是0.5版本
        html2canvas(p,{
            useCORS: true,
            scale:scaleBy,
            backgroundColor: null,
            width:w,
            height:h,
            canvas: canvas,  // 把canvas传进去
            dpi: window.devicePixelRatio * 2, // window.devicePixelRatio是设备像素比
            onclone:function(doc){
                let hiddenDiv = doc.querySelector('#poster_pic');
                hiddenDiv.style.opacity = '1.0'; //  这里，设置opacity为显示
            },
        }).then(function(canvas) {
            var _img = canvas.toDataURL("image/png");
            //$('#poster_pic').css('opacity',0);
            let _len = $('#poster_pic').height();

            //如果是 app则 将base64图片传输给服务器 并保存
            if (_img) {
                $.ajax({
                    url: ApiUrl + "/index.php?ctl=Bill&met=getImage&typ=json",
                    type: 'post',
                    dataType: 'json',
                    data: {k:getCookie("key"), u:getCookie("id"),goods_id:goods_id,common_id:common_id,data64_file:_img},
                    success: function (res) {
                        if (res.status==200) {
                            let _imgUrl = res.data.save_path;
                            $('.showPic').val(_imgUrl);
                            $("#proPic").attr("src",_img).show();

                            $(".simple-dialog-wrapper").remove();
                             
                            $('.mask_div').show();
                            //var ua = navigator.userAgent.toLowerCase();
                            //如果是app app分享 下载按钮出现
                            if (getCookie("is_app_guest")) {
                                _imgUrl = $('.showPic').val();
                                //app分享  图片
                                var u = navigator.userAgent;
                                var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
                                var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
                                $('.share_app').on('click', function () {
                                    var share_code = [];//配合ios数据格式
                                    var type = -1;
                                    if (isAndroid) {
                                        share_code.push(type);
                                        android.typeShare(type, _imgUrl);
                                    }
                                    if (isiOS) {
                                        window.webkit.messageHandlers.Share.postMessage(_imgUrl);
                                    }
                                });
                                $('#bao_app').on('click', function () {
                                    var share_code = [];//配合ios数据格式
                                    if (isAndroid) {
                                        android.savePic(_imgUrl);
                                    }
                                    if (isiOS) {
                                        //share_code.push(_imgUrl);
                                        window.webkit.messageHandlers.savePic.postMessage(_imgUrl);
                                    }
                                });
                                $('.main_b').css('display','flex');
                            }else{
                                $('.down_poster').show();
                            }
                        }
                    }
                });
            }

        });
    }

    function backingScale () {
        if (window.devicePixelRatio && window.devicePixelRatio > 1) {
            return window.devicePixelRatio;
        }
        return 2;
    };
    function parsePixelValue(value) {
        return parseInt(value, 10);
    };

    function getBillInfo(){
        // 定制版 生成海报  获取海报信息  如果海报已经存在 则直接返回数据(如果直接返回暂时没有处理)
        $.ajax({
            url: ApiUrl + "/index.php?ctl=Bill&met=getVeBill&typ=json",
            type: 'get',
            dataType: 'json',
            async: true,
            data: {k:getCookie("key"), u:getCookie("id"),goods_id:goods_id,common_id:common_id},
            success: function (res) {
                var data = res.data;
                if(data){
                    //海报
                    var html_poster = template.render("draw_report", data);
					$("#poster_pic").html(html_poster);
					
                    //加载完成后显示生产海报按钮
                     if (getCookie("id")) {
                        $("#bill").show();
                         $(".cccc").removeClass('hide').css('display','block');
                     }
                }
            }
        });
    }
    getBillInfo();
}