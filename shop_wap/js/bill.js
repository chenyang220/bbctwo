$(function () {
    var k = getCookie("key");
    var u = getCookie("id");
    var goods_id = getQueryString('goods_id');
    $.ajax({
        url: ApiUrl + "/index.php?ctl=Bill&met=getBill&typ=json",
        type: 'get',
        dataType: 'json',
        data: {k:k, u:u,goods_id:goods_id},
        success: function (res) {
            var data = res.data;
            console.log(data)
            if(data){
                // $(".hp100").show();
                $(".gpc-poster-box").css({"background":"url(" + data.bill_image + ") no-repeat center;","background-size":"cover"});
                var html = template.render("bill_info", data);
                $(".to-poster-box").html(html);
                var saveDom = $(".to-poster-box")[0];
                // 使用html2canvas转化成canvas
                html2canvas(saveDom,{background:"#fff"}).then(function (canvas) {
                    //将canvas转化成base64图片
                    tempSrc = canvas.toDataURL("image/jpeg");
                    //将base64传给img标签
                    $("#imgDownload").attr("src", tempSrc);
                    //删除Dom节点
                    $(".to-poster-box").remove();
                    $.ajax({
                        url: ApiUrl + "/index.php?ctl=Feed&met=uploadFile&typ=json",
                        type: 'POST',
                        dataType: 'json',
                        data: {k:k, u:u,image:tempSrc},
                        success: function (res) {
                            qrCode = res.data.imgUrl;
                            console.log(qrCode);

                            //app分享  图片
                            var u = navigator.userAgent;
                            var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
                            var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
                            $('.share_app').on('click', function () {
                                var share_code = [];//配合ios数据格式
                                var type = -1;
                                share_code.push(type);
                                if (isAndroid) {
                                    android.typeShare(type, qrCode);
                                }
                                if (isiOS) {
                                    share_code.push(qrCode);
                                    window.webkit.messageHandlers.PanelShare.postMessage(share_code);
                                }
                            });


                                 var u = navigator.userAgent;
                                 var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端
                                 var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
                                 $('#bao_app').on('click', function () {
                                     var share_code = [];//配合ios数据格式
                                     // share_code.push(type);
                                     if (isAndroid) {
                                         android.savePic(qrCode);
                                     }
                                     if (isiOS) {
                                         share_code.push(qrCode);
                                         window.webkit.messageHandlers.PanelShare.postMessage(share_code);
                                     }
                                 });

                            //滚动header固定到顶部
                            $.scrollTransparent();

                        }
                    })
                });
            }
        }
    });





        // $(".aaaa").click(function(){
        //     var dataurl  = tempSrc;
        //     // canvas保存图片到本地
        //     (function(t){
        //         var dlLink = t || document.createElement("a");
        //         if(!t){
        //             dlLink.id='dlLink';
        //             dlLink.download = '文件名';
        //             dlLink.type = 'jpeg';
        //             document.body.appendChild(dlLink);
        //         }
        //         dlLink.href = dataurl;
        //         dlLink.click();
        //     })(document.querySelector("#dlLink"));
        // })









});

