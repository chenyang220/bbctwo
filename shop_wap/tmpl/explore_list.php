<?php
include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" >
    <title>Document</title>
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="viewport" content="width=device-width, viewport-fit=cover">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/heart.css">
    <link rel="stylesheet" href="../css/swiper.min.css">
    <link rel="stylesheet" href="../css/nctouch_common.css">
    <link rel="stylesheet" href="../css/iconfont.css">
    <link rel="stylesheet" href="../css/video-js.min.css">
</head>
<style type="text/css">
    video::-webkit-media-controls-fullscreen-button{display: none;}
</style>
<body class="x-hidden">
<header class="posf social-box-head">
    <div class="header-wrap">
        <div class="header-l">
            <a href='javascript:;' class="social-user"><img src='' alt=""></a>
        </div>
        <div class="header-inp clearfix">
            <i class="icon" id="search"></i>
            <form action="javascript:return true">
            <input class="search-input" id="keyword" type="search" placeholder="<?=__('“请输入关键字”')?>">
            </form>
        </div>
        <a href="javascript:;" class="message colf social-message ml-20 mr-20">
            <i class="iconfont icon-infor mt-20 iblock"></i>
            <p></p>
        </a>
    </div>
    
    <ul class="social-nav tc">
        <li class="find-cut1 " onclick="exploreListType(1)"><a href="javascript:;">关注</a></li>
        <li class="find-cut2 active" onclick="exploreListType(2)"><a href="javascript:;">发现</a></li>
    </ul>
</header>
<div class="nctouch-main-layout social-main-layout">
    <div class="waterfall-img-box">
        <div id="fid-rgt">
            <ul class="find-list waterfall social-push-items" id="waterfall-ul">
            </ul>
        </div>
        <div class="btn-publish"><a href="javascript:;"><i class="iconfont icon-pen"></i><span>发布</span></a></div>
    </div>
</div>
<script id="waterfall-template" type="text/template">
        <% for(var i = 0;i<data.explore_base.length;i++){ %>
        <li class="waterfall-li" id="onclick_<%= data.explore_base[i].explore_id %>">
            <div class="li-box">
                <% if (data.explore_base[i].type == '.mov' || data.explore_base[i].type == '.mp4'){ %>
                    <a class="wp100" href="./explore_base_video.php?explore_id=<%= data.explore_base[i].explore_id%>">
                        <em class="img-box img-center">
                            <img class="wp100 cter" src="<%=data.explore_base[i].poster_image%>">
                            <i class="ic-bofang"></i>
                        </em>
                    </a>
                <% } else { %>
                    <a class="wp100" href="./explore_base.php?explore_id=<%= data.explore_base[i].explore_id%>">
                        <em class="img-box img-center"><img class="img cter wp100" src="<%= data.explore_base[i].img_url %>" alt=""></em>
                    </a>
                <% } %>

                <div class="push-text-module">
                    <% if (data.explore_base[i].type == '.mov' || data.explore_base[i].type == '.mp4'){ %>

                        <a href="./explore_base_video.php?explore_id=<%= data.explore_base[i].explore_id%>">
                            <span class="daily-tit more-overflow"><%= data.explore_base[i].explore_title %></span>
                        </a>
                    <% } else { %>

                        <a href="./explore_base.php?explore_id=<%= data.explore_base[i].explore_id%>">
                            <span class="daily-tit more-overflow"><%= data.explore_base[i].explore_title %></span>
                        </a>

                    <% } %>

                    <p class="clearfix">
                        <span class="publisher">
                            <a href="./explore_center.php?user_id=<%= data.explore_base[i].user_id%>">
                             <img src="<%= data.explore_base[i].user_logo %>">
                                <em class="one-overflow"><%= data.explore_base[i].user_account %></em>
                            </a>
                        </span>
                            <% if (data.login_status == 0){ %>
                            <span class="fr praise " onclick="changetext('<%= data.explore_base[i].explore_id %>')">
                            <% } else { %>
                                <% if (data.explore_base[i].is_like == 1){ %>
                                <span class="fr praise active" onclick="changetext('<%= data.explore_base[i].explore_id %>')">
                                <% } else { %>
                                <span class="fr praise" onclick="changetext('<%= data.explore_base[i].explore_id %>')">
                                <% } %>
                            <% } %>
                            <i class="iconfont icon-like-b"></i>
                        <em id="user_account"><%= data.explore_base[i].explore_like_count %></em>
                        </span>
                    </p>
                </div>
            </div>
        </li>
        <% } %>
</script>
<!-- 无数据 -->
<script id="no-content" type="text/template">
    <div class="module-tips social-nodata">
        <em class="img-box"><img src="../images/new/tips-img2.png" alt="img"></em>
        <p>暂时没有内容</p>
        <p><a href="./explore_find_friends.html" class="tips-btn">发现有趣的人</a></p>
    </div>
</script>
<!-- 未登录页面查看 -->
<script id="gz-login" type="text/template">
    <div class="module-tips social-nodata">
        <em class="img-box"><img src="../images/new/tips-img1.png" alt="img"></em>
        <p>请在登录后查看</p>
        <p><a href="./member/login.html" class="tips-btn">登录</a></p>
    </div>
</script>

<!-- 没有搜索到内容 -->
<script id="no-search-content" type="text/template">
    <div class="module-tips social-nodata">
        <em class="img-box"><img src="../images/new/tips-img3.png" alt="img"></em>
        <p>暂无搜索结果</p>
    </div>
</script>

<!-- 无数据 -->
<script id="no-find-content" type="text/template">
    <div class="module-tips social-nodata">
        <em class="img-box"><img src="../images/new/tips-img3.png" alt="img"></em>
        <p>暂时没有内容</p>
    </div>
</script>

<!-- 发现未登录弹框提示 -->
<div class="dialog tc social-login-dialog">
    <div class="table">
        <div class="table-cell">
            <div class="content">
                <p class="social-login-tips">请先登录</p>
                <div><a href="javascript:;" class="social-login login">登录</a></div>
            </div>
        </div>
    </div>
</div>
<?php
include __DIR__ . '/../includes/footer_menu.php';
?>

<script type="text/javascript" src="../js/template.js"></script>
<script type="text/javascript" src="../js/common.js"></script>
<script type="text/javascript" src="../js/zepto.cookie.js"></script>
<script type="text/javascript" src="../js/simple-plugin.js"></script>
<script type="text/javascript" src="../js/swiper.min.js"></script>
<script type="text/javascript" src="../js/tmpl/footer.js"></script>
<script type="text/javascript" src="../js/jquery.timeCountDown.js"></script>
<script type="text/javascript" src="../js/explore_list.js"></script>
<script type="text/javascript" src="../js/video.min.js"></script>
<!--<script type="text/javascript" src="../js/ckplayer/ckplayer.js"></script>-->
<!--<script type="text/javascript">-->
<!--    var videoObject = {-->
<!--        container: '#video', //容器的ID或className-->
<!--        variable: 'player',//播放函数名称-->
<!--        poster:'../material/poster.jpg',//封面图片-->
<!--        advertisements:'website:ad.json',//单独用一个json文件来配置广告-->
<!--        video: [//视频地址列表形式-->
<!--            ['http://www.yrwl.st.yuanfeng021.com/shop/data/upload/media/plantform/10a699408c212a268846651013d0f26c/video/20190515/1557891383862009.mov', 'video/mov', '中文标清', 0]-->
<!--        ]-->
<!--    };-->
<!--    var player = new ckplayer(videoObject);-->
<!--</script>-->

<script type="text/javascript">
    //设置中文
    videojs.addLanguage('zh-CN', {
        "Play": "播放",
        "Pause": "暂停",
        "Current Time": "当前时间",
        "Duration": "时长",
        "Remaining Time": "剩余时间",
        "Stream Type": "媒体流类型",
        "LIVE": "直播",
        "Loaded": "加载完毕",
        "Progress": "进度",
        "Fullscreen": "全屏",
        "Non-Fullscreen": "退出全屏",
        "Mute": "静音",
        "Unmute": "取消静音",
        "Playback Rate": "播放速度",
        "Subtitles": "字幕",
        "subtitles off": "关闭字幕",
        "Captions": "内嵌字幕",
        "captions off": "关闭内嵌字幕",
        "Chapters": "节目段落",
        "Close Modal Dialog": "关闭弹窗",
        "Descriptions": "描述",
        "descriptions off": "关闭描述",
        "Audio Track": "音轨",
        "You aborted the media playback": "视频播放被终止",
        "A network error caused the media download to fail part-way.": "网络错误导致视频下载中途失败。",
        "The media could not be loaded, either because the server or network failed or because the format is not supported.": "视频因格式不支持或者服务器或网络的问题无法加载。",
        "The media playback was aborted due to a corruption problem or because the media used features your browser did not support.": "由于视频文件损坏或是该视频使用了你的浏览器不支持的功能，播放终止。",
        "No compatible source was found for this media.": "无法找到此视频兼容的源。",
        "The media is encrypted and we do not have the keys to decrypt it.": "视频已加密，无法解密。",
        "Play Video": "播放视频",
        "Close": "关闭",
        "Modal Window": "弹窗",
        "This is a modal window": "这是一个弹窗",
        "This modal can be closed by pressing the Escape key or activating the close button.": "可以按ESC按键或启用关闭按钮来关闭此弹窗。",
        ", opens captions settings dialog": ", 开启标题设置弹窗",
        ", opens subtitles settings dialog": ", 开启字幕设置弹窗",
        ", opens descriptions settings dialog": ", 开启描述设置弹窗",
        ", selected": ", 选择",
        "captions settings": "字幕设定",
        "Audio Player": "音频播放器",
        "Video Player": "视频播放器",
        "Replay": "重播",
        "Progress Bar": "进度小节",
        "Volume Level": "音量",
        "subtitles settings": "字幕设定",
        "descriptions settings": "描述设定",
        "Text": "文字",
        "White": "白",
        "Black": "黑",
        "Red": "红",
        "Green": "绿",
        "Blue": "蓝",
        "Yellow": "黄",
        "Magenta": "紫红",
        "Cyan": "青",
        "Background": "背景",
        "Window": "视窗",
        "Transparent": "透明",
        "Semi-Transparent": "半透明",
        "Opaque": "不透明",
        "Font Size": "字体尺寸",
        "Text Edge Style": "字体边缘样式",
        "None": "无",
        "Raised": "浮雕",
        "Depressed": "压低",
        "Uniform": "均匀",
        "Dropshadow": "下阴影",
        "Font Family": "字体库",
        "Proportional Sans-Serif": "比例无细体",
        "Monospace Sans-Serif": "单间隔无细体",
        "Proportional Serif": "比例细体",
        "Monospace Serif": "单间隔细体",
        "Casual": "舒适",
        "Script": "手写体",
        "Small Caps": "小型大写字体",
        "Reset": "重启",
        "restore all settings to the default values": "恢复全部设定至预设值",
        "Done": "完成",
        "Caption Settings Dialog": "字幕设定视窗",
        "Beginning of dialog window. Escape will cancel and close the window.": "开始对话视窗。离开会取消及关闭视窗",
        "End of dialog window.": "结束对话视窗"
    });


    var myPlayer = videojs('my-video');
    videojs("my-video").ready(function(){
        var myPlayer = this;
        myPlayer.play();
    });
</script>
<script>

    $(".login").click(function(e){
        e.preventDefault();
        window.location.href = ShopWapUrl + "/tmpl/member/login.html";

    });

</script>

</body>
</html>
<?php
include __DIR__ . '/../includes/footer.php';
?>



