<?php 
include __DIR__.'/../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PLUS会员首页</title>
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" href="../../css/base.css">
	<link rel="stylesheet" href="../../css/plus.css">
	<link rel="stylesheet" href="../../css/iconfont.css">
</head>
<body>
	<header id="header" class="posf">
        <div class="header-wrap">
            <div class="header-l">
                <!-- <a class="js-cancel-push" href="javascript:history.back(-1);"> <i class="back"></i> </a> -->
            </div>
            <div class="tit">PLUS会员首页</div>
        </div>
    </header>

<div class="nctouch-main-layout"  id="plus-goods-items"></div>
<script type="text/html" id="plus-goods-item">
        <div class="plus-index-content pl-20 pr-20 tc <%if(plusTyp>0){%> plus-formal-box <%}%>">
            <h3 class="plus-index-tit">PLUS会员，享专属特权</h3>
            <div class="plus-index-content-power">
             <%if(plusTyp==1){%>
                 <div class="plus-formal-content">
                    <a class="plus-btn-oper" href="plus_open.html">开通正式会员</a>
                    <div class="tl">
                        <em class="img-box"><img src="<%=user_logo%>" alt=""></em>
                        <div class="plus-formal-text fz0">
                            <p><span class="plus-formal-name one-overflow mwp80 align-middle wauto"><%=user_name%></span><b class="plus-formal-try-log">试用</b></p>
                            <p class="plus-formal-try-time"><span>试用</span><em><%=hasPlusday%></em><span>天后到期</span></p>
                            <p class="plus-formal-try-progress"><b style="width:<%=pct%>%;"></b></p>
                        </div>
                    </div>
                </div>
            <%}%>
            <%if(plusTyp==2){%>
                <div class="plus-formal-content">
                    <a class="plus-btn-oper" href="plus_open.html">立即续费</a>
                    <div class="tl">
                        <em class="img-box"><img src="<%=user_logo%>" alt=""></em>
                        <div class="plus-formal-text fz0">
                            <p><span class="plus-formal-name one-overflow mwp80 align-middle wauto"><%=user_name%></span></p>
                            <p><span class="plus-normal-logs"></span></p>
                            <p class="plus-formal-try-time"><em><%=userPlusEndDate%></em><span></p>
                        </div>
                    </div>
                </div>
            <%}%>
                <ul class="plus-power-items fz0">
                    <li>
                        <a href="plus_describle.html#pot1">
                            <i class="iconfont icon-plus-huiyuan"></i>
                            <p>会员专享价</p>
                       </a>
                    </li>
                    <li>
                        <a href="plus_describle.html#pot2">
                            <i class="iconfont icon-plus-jifen"></i>
                            <p>积分加倍</p>
                        </a>
                    </li>
                    <li>
                        <a href="plus_describle.html#pot3">
                            <i class="iconfont icon-plus-huiyuanri"></i>
                            <p>超级会员日</p>
                        </a>
                    </li>
                    <li class="mr-40">
                        <a href="plus_describle.html#pot4">
                            <i class="iconfont icon-plus-hongbao"></i>
                            <p>全品类平台红包</p>
                        </a>
                    </li>
                    <li class="ml-40">
                        <a href="plus_describle.html#pot5">
                            <i class="iconfont icon-plus-fuwu"></i>
                            <p>24小时客服服务</p>
                        </a>
                    </li>
                </ul>
                <%if(!plusTyp || plusTyp==3){%>
                    <div class="plus-btn">
                        <a class="plus-btn-open" href="plus_open.php">立即开通</a>
                         <%if(tryDays){%>
                          <a class="plus-btn-try plus-bottom-btn-try" href="javascript:;">免费试用<b><%=tryDays;%></b>天</a>
                         <%}%>
                    </div>
                <%}%>
            </div>
            <div class="plus-goods-box">
                <div class="plus-goods-header-tit">
                    <a class="block" href="javascript:;">
                    <i class="iconfont icon-huiyuan"></i><span>PLUS会员尊享</span><i class="iconfont icon-you"></i></a>
                </div>
                <ul class="plus-goods-items tl">
                  <%if(typeof(items)!='undefined' && items.length>0){%>
                      <% for (var k in items) { var v = items[k]; %>
                        <li <% if(k>=6){%> class='hide' <%}%> >
                            <a href="../product_detail.html?cid=<%=v.common_id%>">
                                <em class="img-box"><img src="<%=v.common_image %>" alt=""></em>
                                <div>
                                    <span class="block one-overflow"><%=v.common_name %></span>
                                    <em class="block">￥<%=v.common_price %></em>
                                    <p><strong>￥<%=v.plus_price %></strong><b></b></p>
                                </div>
                            </a>
                        </li> 
                      <%}%>
                    <%}%>
                </ul>
            </div>
        </div>
        <%if(!plusTyp){%>
        <div class="plus-bottom-fixed fz0">
             <%if(tryDays){%>
            <a class="plus-bottom-btn-half plus-bottom-btn-try" href="javascript:;">免费试用<b><%=tryDays%></b> 天</a>
             <%}%>
            <a class="plus-bottom-btn-half plus-bottom-btn-open <%if(!tryDays){%> wp100 <%}%>"  href="plus_open.php">立即开通</a>
        </div>
        <%}else{%>
        <div class="plus-normal-bottom-fixed">
            <a class="active" href="javascript:;"><i class="iconfont icon-huiyuan2"></i><em>PLUS会员首页</em></a><a class="" href="plus_goods.html"><i class="iconfont icon-goods"></i><em>会员尊享商品</em></a>
        </div>
        <%}%>
</script>
<!-- 弹框-成功开始试用资格 -->
<div class="plus-dialog tc">
    <div class="plus-dialog-content">
        <strong>恭喜您</strong>
        <p>已成功开启PLUS试用会员资格</p>
        <b>祝您体验愉快！</b>
        <button class="plus-try-close"></button>
    </div>
</div>
<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/simple-plugin.js"></script>
<script type="text/javascript" src="../../js/ncscroll-load.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
</html>
<?php
    include __DIR__ . '/../../includes/footer.php';
?>

<script type="text/javascript">
    $(function () {
        var t = getCookie("key");
        if (!t) {
            window.location.href = WapSiteUrl + "/tmpl/member/login.html";
            return false;
        }
        var i = new ncScrollLoad;
        i.loadInit({
            url: ApiUrl + "/index.php?ctl=Plus_User&met=index&typ=json",
            getparam: {k: t, u: getCookie("id")},
            tmplid: "plus-goods-item",
            containerobj: $("#plus-goods-items"),
            iIntervalId: true,
            data: {WapSiteUrl: WapSiteUrl}
        });
        //点击开通试用plus会员
        $(document).on("click",".plus-bottom-btn-try",function(){
            $.ajax({
                type: 'post',
                url: ApiUrl + '/index.php?ctl=Plus_User&met=openTry&typ=json',
                data: {k: t,u:getCookie('id')},
                dataType: 'json',
                async: false,
                success: function(result) {
                    if (result.status == 200) {
                        //开通试用代码
                        $(".plus-dialog").show();
                        return false;
                    } else {
                        //plus会员开通失败的操作提示
                        $(".plus-dialog strong").text(result.strong);
                        $(".plus-dialog p").text(result.p);
                        $(".plus-dialog b").text(result.em);
                        //开通试用代码
                        $(".plus-dialog").show();
                    }
                }
            });
        });

        $(".plus-try-close").click(function(){
            $(this).parents(".plus-dialog").hide();
        })
    });
</script>