<?php 
 include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="../../css/base.css">
	<link rel="stylesheet" href="../../css/plus.css">
  <link rel="stylesheet" href="../../css/nctouch_common.css">
	<link rel="stylesheet" href="https://at.alicdn.com/t/font_562768_700wlm14tkp.css">
</head>
<body>
	<header id="header" class="posf">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a class="js-cancel-push" href="javascript:history.back(-1);"> <i class="back"></i> </a> -->
            </div>
            <div class="tit">实名认证</div>
        </div>
    </header>
    <div class="nctouch-main-layout bgf" id="certification"></div>
    <!-- 弹框-证件类型 -->
    <div id="plus-photo-type_html" class="nctouch-bottom-mask down" style="z-index: 100">
        <div class="nctouch-bottom-mask-bg"></div>
        <div class="nctouch-bottom-mask-block plus-photo-block">
            <div class="nctouch-bottom-mask-rolling" id="plus-photo_roll">
                <div class="goods-options-stock bort1">
                    <ul class="plus-photo-type-sel js-plus-photo-type-sel">
                      <li class="borb0 active" data-value='1'><span>身份证</span></li>
                      <li class="borb0" data-value='2'><span>护照</span></li>
                      <li class="borb0" data-value='3'><span>军官证</span></li>
                    </ul>
                    <button class="plus-btn-photo-sel-cancel">取消</button>
                </div>
            </div>
          </div>
    </div>
    <!-- 模板 -->
    <script type="text/html" id="tmplcertification">
        <div class="plus-dl-content pl-20 pr-20">
          <input type="hidden" name="user_nickname" value="<%=user_nickname%>">
           <dl>
               <dt>真实姓名：</dt>
               <dd><input type="text" name="user_realname" value="<%=user_realname%>"></dd>
           </dl>
           <dl class="js-btn-photo-type">
               <dt>证件类型：</dt>
               <dd><span id="sel-papers" data-id="1">身份证</span><i class="iconfont fr icon-drap"></i> </dd>
           </dl>
           <dl>
               <dt>证件号码：</dt>
               <dd><input type="text" name="user_identity_card" value="<%=user_identity_card%>"></dd>
           </dl>
           <dl>
               <dt>证件有效期：</dt>
               <dd class="clearfix">
                  <span class="time-sel"><%=user_identity_end_time%></span>
                  <i class="iconfont fr icon-rili"></i>
                  <input type="date" id="dateSelect">
                </dd>
           </dl>
           <dl class="borb0">
               <dt>证件正面照：</dt>
               <dd>
                 <div class="plus-authentication-img">
                   <img name="user_identity_face_logo" src="<%if(user_identity_face_logo){%> <%=user_identity_face_logo%> <%}else{%> ../../images/plus/photo-front.png<%}%>" alt="">
                   <input class="btn-input " type="file" accept="image/*" capture="camera" name="user_identity_face_logo">
                 </div>
             </dd>
           </dl>
           <dl>
               <dt>证件背面照：</dt>
               <dd>
                  <div class="plus-authentication-img">
                    <img name="user_identity_font_logo" src="<%if(user_identity_font_logo){%> <%=user_identity_font_logo%> <%}else{%>../../images/plus/photo-behind.png<%}%>" alt="">
                    <input class="btn-input" type="file" accept="image/*" capture="camera" name="user_identity_font_logo">
                  </div>
              </dd>
           </dl>
        </div>
      <a href="javascript:;" class="plus-bottom-btn">提交</a>
   </script>
  <script type="text/javascript" src="../../js/zepto.min.js"></script>
  <script type="text/javascript" src="../../js/template.js"></script>
  <script type="text/javascript" src="../../js/common.js"></script>
  <script type="text/javascript" src="../../js/simple-plugin.js"></script>
  <script type="text/javascript" src="../../js/ncscroll-load-plus.js"></script>
  <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
  <script type="text/javascript" src="../../js/animation.js"></script>
</body>
</html>
<?php
    include __DIR__ . '/../../includes/footer.php';
?>
<script>
      var t = getCookie("key");
     $(function () {
        if (!t) {
            window.location.href = WapSiteUrl + "/tmpl/member/login.html";
            return false;
        }
        var i = new ncScrollLoad;
        i.loadInit({
            url: PayCenterWapUrl + "/index.php?ctl=Info&met=certification&typ=json",
            getparam: {k: t, u: getCookie("id")},
            tmplid: "tmplcertification",
            containerobj: $("#certification"),
            iIntervalId: true,
            data: {WapSiteUrl: WapSiteUrl}
        });
    });

    //会员实名认证
    $(document).on("click",".plus-bottom-btn",function(){
        var user_realname = $('input[name=user_realname]').val();
        var user_identity_card = $('input[name=user_identity_card]').val();
        var user_identity_type = $('#sel-papers').data('id');
        var user_identity_end_time = $('.time-sel').text();
        var user_identity_font_logo = $('img[name=user_identity_font_logo]').attr('src');
        var user_identity_face_logo = $('img[name=user_identity_face_logo]').attr('src');
        var data = {
                     user_realname:user_realname,
                     user_identity_card:user_identity_card,
                     user_identity_card:user_identity_card,
                     user_identity_end_time:user_identity_end_time,
                     user_identity_type:user_identity_type,
                     user_identity_font_logo:user_identity_font_logo,
                     user_identity_face_logo:user_identity_face_logo,
                     k:t,u:getCookie('id')
                  }
        Zepto.ajax({
            type: 'post',
            url: PayCenterWapUrl + '/index.php?ctl=Info&met=editCertification&typ=json',
            data: data,
            dataType: 'json',
            async: false,
            success: function(result) {
                if (result.status == 200) {
                  //提示框
                   $.sDialog({
                        skin: "green",
                        content: "提交成功",
                        okBtn: false, 
                        cancelBtn: false
                    });
                   window.location.href = WapSiteUrl + '/tmpl/member/plus_open.php';
                } else {
                   //提示框
                   $.sDialog({
                        skin: "red",
                        content: __(result.msg),
                        okBtn: false, 
                        cancelBtn: false
                    });
                }
            }
        });
    });

    //图片处理
    $(document).on("change","input[type=file]",function(){
      fileImage(this);
    })
    //图片显示
    function fileImage(e){
        var that = e;
        var file = e.files[0];
        var imgSize=file.size/1024;
        var name = e.name;
        var formData = new FormData();
        formData.append('file', file);
        formData.append('k', t);
        formData.append('u', getCookie('id'));
        $.ajax({
            type: 'post',
            url: PayCenterWapUrl + '/index.php?ctl=Info&met=upload&typ=json',
            data: file,
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            mimeType: "multipart/form-data",
            success: function (result) {
                $('img[name='+name+']').attr('src',__(result.msg));
            },
            error: function (result) {
                //提示框
                   $.sDialog({
                        skin: "red",
                        content: __(result.msg),
                        okBtn: false, 
                        cancelBtn: false
                  });
            }
        });
        if(imgSize>1024*10){
            //提示框
             $.sDialog({
                  skin: "red",
                  content: __('请上传大小不要超过10M的图片'),
                  okBtn: false, 
                  cancelBtn: false
            });
        }else{
            var reader = new FileReader();
            reader.readAsDataURL(file); // 读出
            reader.onloadend = function () {
                //图片的格式可以直接当成的属性值
                var dataURL = reader.result;
                $(e).prev().attr('src',dataURL);
            };
        }
     }
      // 时间选择改变获取值并赋值
      $(document).on('change','#dateSelect',function(){
          $(".time-sel").html($(this).val());
      });
      // 选择证件类型
      $.animationUp({
          valve: ".js-btn-photo-type",           
          wrapper: "#plus-photo-type_html", 
          scroll: "#plus-photo_roll" 
      });
      $(".js-plus-photo-type-sel li").click(function(){
        var vals=$(this).find("span").html();
        $("#sel-papers").html(vals);
        $("#sel-papers").attr('data-id',$(this).attr('data-value'));
        $("#plus-photo-type_html").removeClass("up").addClass("down");
      })
      $(".plus-btn-photo-sel-cancel").click(function(){
        $("#plus-photo-type_html").removeClass("up").addClass("down");
      })
</script>