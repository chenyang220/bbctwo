<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/Group-integral.css"/>
<link href="<?= $this->view->css ?>/tips.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?= $this->view->js_com ?>/plugins/jquery.toastr.min.js"></script>
<script type="text/javascript" src="<?= $this->view->js ?>/common.js"></script>
<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/PCMessageList.css"/>

<div class="wrap">
    <div class="PCDetailsBox ">
        <div class="detailsBox">
            <p class="detailHdP"><?= $data['title'] ?></p>
            <div class="storeNameBox">
                <p class="storeName"><?= $data['authorname'] ?></p>
                <?php if ($data['author_type'] == 2) { ?>
                    <a class="entranceBn" href="<?= Yf_Registry::get('url') ?>?ctl=Shop&met=index&typ=e&id=<?= $data['shop_id'] ?>"><i></i>进入店铺</a>
                <?php } ?>
            </div>
            <div class="ParagraphBox">
                <P class="subheadP"><?= $data['subtitle'] ?></P>
                <p class="ParagraphP"><?= $data['content'] ?></p>
                
                <div class="detailsIconBox">
                    <img src="images/banner_02.png" alt="">
                </div>
                <div class="BmShareBox">
                    <p class="ShareP">分享至 : <b class="top iconfont icon-icoshare icon-1 bbc_color"></b><em class="top"></em></p>
                    
                    <img src="images/weixinIcon.png" alt=""> <img src="images/pengyouquanIcon.png" alt=""> <img src="images/qqIcon_01.png" alt=""> <img src="images/qqkongjiangIcon_01.png" alt="">
                    <div class="complainBox">
                        <?php if($data['complaint'] == 1){?>
                         <button class="complainBn" value="<?= $data['id'] ?>">投诉</button>
                        <?php }?>
                        
                        <p><?= $data['create_time'] ?></p>
                        <p>
                            <sapn><?= $data['number'] ?></sapn>
                            条阅读量
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--投诉遮罩-->
<div class="complainTeBox">
    <div class="UpWindows">
        <p>确认投诉此资讯吗？</p>
        <div class="DeleteBnBox">
            <button class="CnConfirm">确定</button>
            <button class="CnCancel">取消</button>
        </div>
    </div>
</div>
<!-- 登录遮罩层 -->
<div id="login_content" style="display:none;">
</div>

<script>
    window._bd_share_config = {
        "common": {
            "bdSnsKey": {},
            "bdText": "",
            "bdMini": "2",
            "bdPic": "",
            "bdUrl": window.location.href,
            "bdStyle": "0",
            "bdSize": "16",
        },
        "share": {},
        "image": {"viewList": ["qzone", "tsina", "tqq", "renren", "weixin"], "viewText": "分享到：", "viewSize": "16"},
        "selectShare": {"bdContainerClass": null, "bdSelectMiniList": ["qzone", "tsina", "tqq", "renren", "weixin"]}
    };
    with (document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = '/shop/static/api/js/share.js?v=89860593.js?cdnversion=' + ~(-new Date() / 36e5)];
</script>

<script>
    
    $(".complainBn").click(function () {
        $(".complainTeBox").css("display", "inline-block");
       
        preventBubble();
    })
     $(".complainTeBox").click(function () {
        $(".complainTeBox").css("display", "none");
        $(document.body).css({
            "overflow-x": "auto",
            "overflow-y": "auto"
        });
        preventBubble();
        return false;
    })
    $(".CnConfirm").click(function () {
        var new_id = $('.complainBn').val();
        $.ajax({
            url: "index.php?ctl=Informationlist&met=Complaint&typ=json",
            data: {id: new_id},
            type: "POST",
            success: function (e) {
                if (e.status == 200) {
                    var data = e.data;
                    Public.tips.success('操作成功!');
                    // $(".complainTeBox").css("display", "none");
                    // $(document.body).css({
                    //     "overflow-x": "auto",
                    //     "overflow-y": "auto"
                    // });
                    // preventBubble();
                    window.location.reload();
                    //return false;
                    
                    
                }
                else {
                    Public.tips.error(e.msg);
                }
                _this.holdSubmit(false);
            }
        });
    })
    $(".CnCancel").click(function () {
        $(".complainTeBox").css("display", "none");
        $(document.body).css({
            "overflow-x": "auto",
            "overflow-y": "auto"
        });
        preventBubble();
        return false;
    })
</script>

<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>
