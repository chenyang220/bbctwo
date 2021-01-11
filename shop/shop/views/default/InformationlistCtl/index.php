<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
<!-- 替换css -->
<link rel="stylesheet" type="text/css" href="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f=/shop/static/default/css/goods-list.css,/shop/static/default/css/tips.css,/shop/static/default/css/login.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/PCMessageList.css"/>

<link rel="stylesheet" href="<?= $this->view->css ?>/iconfont/iconfont.css">

<?php
$min = "/shop/static/default/js/tuangou-index.js,";
$min .= "/shop/static/common/js/plugins/jquery.slideBox.min.js,";

$min .= "/shop/static/default/js/select2.min.js";
$min = substr($min, 0, -1);
?>
<script type="text/javascript" src="<?= cdn_url(Yf_Registry::get('base_url') . '/min/?f=' . $min); ?>"></script>


<div class="wrap">
    <div class="infor-head-nav clearfix bgf">
        <strong class="fl">资讯列表</strong>
        <p class="fl">

            <a <?php if (!request_string('status')) {echo 'class="active"';} ?> onclick='tt()' href="javascript:;" data-state="">全部</a>
            <?php foreach ($data['newsclass'] as $key => $vul) { ?>
            <a <?php if(request_string('status')== $vul['id']) echo'class="active"'; ?> onclick='tt()' href="javascript:;" data-state="<?= $vul['id'] ?>"><?= $vul['newsclass_name'] ?></a>
            <?php } ?>
        </p>
        <div class="fr infor-nav-sel">
            <span class="infor-nav-sel-hav">
                <i class="iconfont icon-sort"></i>
                <em><?php if(!request_string('number')) echo '默认排序';else if(request_string('number')=='DESC') echo '阅读量排序' ?></em>
                <i class="iconfont icon-iconjiantouxia"></i>
            </span>
            <ul>
                <li class="<?php if(!request_string('number')) echo 'active'; ?>" onclick='tt()'><a href="javascript:;" data-state=""><span>默认排序</span><i class="iconfont icon-gou"></i></a></li>
                <li class="<?php if(request_string('number')=='DESC') echo  'active';?>" onclick='tt()'><a href="javascript:;" data-state="DESC"><span>阅读量排序</span><i class="iconfont icon-gou"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="infor-list-box bgf">
        <?php if ($data['news']['items']) { ?>
            <ul class="infor-list-items">
                <?php foreach ($data['news']['items'] as $key => $vul) { ?>
                <li class="<?php if (empty($vul['content_url'])) echo 'active';?>">
                    <a href="<?= url("Informationlist/details", ['id' => $vul['id']]) ?>">
                        
                        <em class="img-box infor-left-img">
                            <?php if (!empty($vul['content_url']) && $vul['content_type'] == 'img') { ?>
                                <img src="<?= $vul['content_url']?>" alt="information">
                            <?php } elseif (!empty($vul['content_url']) && $vul['content_type'] == 'embed') { ?>
                                <embed class="wp100" autoatart="false" class="edui-upload-video vjs-default-skin" src="<?= $vul['content_url']?>" wmode="transparent" allowscriptaccess="never" allownetworking="internal">
                            <?php }; ?>
                        </em>
                        <div class="infor-text-module">
                            <h4><?= $vul['title'] ?></h4>
                            <p class="infor-text-des two-overflow"><?= $vul['subtitle'] ?></p>
                            <p class="infor-text-rel clearfix">
                                <span class="fl">
                                    <?php if ($vul['logo']) { ?>
                                    <b class="img-box"><img src="<?= $vul['logo']?>" alt=""></b>
                                    <?php } ?>
                                    <em><?= $vul['authorname'] ?></em></span>
                                <span class="fl view-num"><i class="iconfont icon-view"></i><em><?= $vul['number'] ?>条阅读量</em></span>
                                <span class="fr"><?= $vul['create_time'] ?></span>
                            </p>
                        </div>
                    </a>
                </li>
                <?php } ?>
                
            </ul>
            <?php if ($page_nav) { ?>
                    <div style="clear:both"></div>
                    <div class="page page_front infor-page">
                        <?= $page_nav ?>
                    </div>
                    <div style="clear:both"></div>
                <?php } ?>
            <?php } else { ?>
                
                <div class="no_account">
                    <img class='lazy' data-original="<?= $this->view->img ?>/ico_none.png"/>
                    <p><?= __('暂无符合条件的数据记录') ?></p>
                </div>
            <?php }; ?>
        </div>
</div>


<!-- 登录遮罩层 -->
<div id="login_content" style="display:none;">
</div>


<script>
    $(".infor-head-nav p").on("click", "a", function () {
        $(".infor-head-nav p").find("a").removeClass("active");
        $(this).addClass("active");
        tt()
    });

     $(".infor-nav-sel .infor-nav-sel-hav").click(function(){
        $(this).parent().find("ul").toggleClass("active");
     });
    $(".infor-nav-sel ul li").click(function(){
        $(".infor-nav-sel ul li").removeClass("active");
        $(this).addClass("active");
         var ht=$(this).find("span").html();
        $(this).parents(".infor-nav-sel").find("span em").html(ht);
        tt();
     });

    function tt() {
        var t = $(".infor-head-nav p").find(".active").attr("data-state");
        var n = $(".infor-nav-sel ul").find(".active").find("a").attr("data-state");
        window.location.href = SITE_URL +"/index.php?ctl=Informationlist&met=index"+"&status="+t+"&number="+n;
        
    }
</script>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
