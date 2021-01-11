<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'site_nav.php';
?>
<link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/plus.css" />
<div class="plus-header">
    <?php if($active || $plusTyp=="0"){?>
    <div class="plus-header-nav">
    <?php }else{ ?>
    <div class="plus-header-nav plus-header-bgf">
    <?php } ?>
        <div class="wrap clearfix">
            <div class="fl">
                <i class="iconfont icon-module"></i>
                <span>PLUS会员专场</span>
            </div>
            <div class="fr plus-header-exchange"><em class="active" onclick="gotoIndex();">Plus会员首页</em><em id="em1click" onclick="gotoIndex('&flag=1');">Plus会员专享</em></div>
        </div>
    </div>
    <?php if($plusTyp=="0"){?>
    <!-- plus会员首页 -->
    <div class="plus-header-li plus-index-module  active">
        <div class="wrap">
            <strong>PLUS会员，享专享特权</strong>
            <ul>
                <li>
                    <span><i class="iconfont icon-plus-huiyuan"></i><em>会员专享价</em></span>
                    <div class="plus-power-tips">
                        <span>开通PLUS会员，即可尊享商城商品价格<?=$plus_rate?>%折扣！</span>
                    </div>
                </li>
                <li>
                    <span><i class="iconfont icon-plus-jifen"></i><em>积分加倍</em></span>
                    <div class="plus-power-tips">
                        <span>PLUS会员在商城购买符合活动范围的商品，每个订单所累计的积分将直接翻<?=$plus_integral;?>倍!</span>
                    </div>
                </li>
                <li>
                    <span><i class="iconfont icon-plus-huiyuanri"></i><em>超级会员日</em></span>
                    <div class="plus-power-tips">
                        <span>每年双11，为超级会员日。PLUS会员将获得<?=$plus_general_red;?>元无门槛平台红包，使用期限为领取后的7天之内!</span>
                    </div>
                </li>
                <li>
                    <span><i class="iconfont icon-plus-hongbao"></i><em>全品类平台红包</em></span>
                    <div class="plus-power-tips">
                        <span>开通PLUS会员即送满<?=$plus_quota;?>元减<?=$plus_red_packet;?>元平台红包，使用期限为领取后的一个月之内，每月送1张。</span>
                    </div>
                </li>
                <li>
                    <span><i class="iconfont icon-plus-fuwu"></i><em>24小时客服服务</em></span>
                    <div class="plus-power-tips">
                        <span>PLUS会员用户享受24小时客服服务。优先解答您问题!</span>
                    </div>
                </li>
            </ul>
            <div class="plus-btn">
                <a class="btn-plus-open" href="<?= Yf_Registry::get('url') ?>?ctl=Plus_User&met=open">立即开通</a>
                <?php if($tryDays){?>
                    <?php if($status){?>
                        <a class="btn-plus-try" href="javascript:;">已开通PLUS试用会员</a>
                    <?php }else{?>
                    <a class="btn-plus-try" href="javascript:;">免费试用<?=$tryDays;?>天</a>
                <?php
					}
				}
				?>
            </div>
        </div>
    </div>
    <?php }else if ($plusTyp=="2"){ ?>
        <div class="wrap plus-use-module">
            <!-- 正式会员begin -->
            <div class="clearfix plus-use-header">
                <div class="fl mt30">
                    <em class="img-box align-middle"><img src="<?= $user_logo;?>" alt="user"></em>
                    <div class="plus-use-header-text fz0">
                        <p><span class="plus-user-name iblock align-middle"><?= $user_name ?></span></p>
                        <p><b class="plus-formal-logo"></b></p>
                        <p class="plus-time plus-formal-time"><?=$userPlusEndDate?>到期</p>
                    </div>
                </div>
                <div class="fr plus-use-operate">
                    <a class="plus-use-operate-href" href="javascript:;"  onclick="gotoIndex('','open');"><em class="iblock align-top">立即续费</em><i class="iconfont icon-double-right"></i></a>
                    <p class="plus-operate-tips tc">立即续费，继续为您省更多的钱</p>
                </div>
            </div>
            <!-- 正式会员end -->
            <div class="plus-use-power">
                <p class="plus-use-tit tc">PLUS会员，享专属特权</p>
                <ul class="fz0 tc">
                    <li><i class="iconfont icon-plus-huiyuan"></i><span>会员专享价</span></li>
                    <li><i class="iconfont icon-plus-jifen"></i><span>积分加倍</span></li>
                    <li><i class="iconfont icon-plus-huiyuanri"></i><span>超级会员日</span></li>
                    <li><i class="iconfont icon-plus-hongbao"></i><span>全品类平台红包</span></li>
                    <li><i class="iconfont icon-plus-fuwu"></i><span>24小时客服服务</span></li>
                </ul>
            </div>
        </div>
    <?php }else if ($plusTyp=="1"){ ?>
        <div class="wrap plus-use-module">
            <!-- 试用会员beigin -->
            <div class="clearfix plus-use-header">
                <div class="fl mt30">
                    <em class="img-box align-middle"><img src="<?= $user_logo;?>" alt="user"></em>
                    <div class="plus-use-header-text">
                        <p><span class="plus-user-name iblock align-middle"><?=$user_name ?></span><b class='plus-status'>试用</b></p>
                        <p class="plus-time">试用<i><?=$hasPlusday?></i>天后到期【<?=$userPlusEndDate?>】</p>
                        <em class="plus-time-progress"><b style="width:<?=$pct?>%;" title="已使用<?=$useDay?>天；剩余<?=$pct?>%"></b></em>
                    </div>
                </div>
                <div class="fr plus-use-operate">
                    <a class="plus-use-operate-href" href="javascript:;" onclick="gotoIndex('','open');">开通正式PLUS会员》</a>
                    <p class="plus-operate-tips tc">成为正式PLUS会员，为您省更多的钱</p>
                </div>
            </div>
            <!-- 试用会员end -->
        </div>
    <?php } ?>
    <?php if($active){ ?>
    <!-- plus会员专享 -->
    <div class="plus-header-li plus-member-module">
        <div class="plus-search clearfix tl"><input id="search_goods" class="plus-search-input" type="text" placeholder="搜索PLUS会员商品名称" value="<?=$words?>" onfocus="this.value='';"><a class="fr" href="javascript:;" onclick="searchplusgoods();" ><i class="iconfont icon-plus-search"></i></a></div>
    </div>
    <?php } ?>
</div>
<div class="plus-goods-box wrap">
    <!-- 有数据 -->
    <div class="plus-goods-header tc">
        <div class="plus-goods-header-tit">
            <i class="iconfont icon-huiyuan"></i><span>PLUS会员尊享</span>
        </div>
        <a class="fr plus-goods-more" href="javascript:;" onclick="gotoIndex('&flag=1');"><em>更多</em><i class="iconfont icon-btnrightarrow"></i></a>
    </div>
    <?php if ($ret['items']){?>
    <ul class="plus-goods-list fz0">
        <?php foreach ($ret['items'] as $item) { ?>
            <li>
                <a href="javascript:;" id="plus_item_<?=$item['common_id']?>"><em class="img-box"><img src="<?=$item['common_image'];?>" alt="<?=$item['common_name'];?>"></em>
                    <div>
                        <span class="one-overflow"><?=$item['common_name'];?></span>
                        <b class="block">￥<?=$item['common_price'];?></b>
                        <p><i>V</i><em>Plus会员专享价</em><strong>￥<?=$item['plus_price'];?></strong></p>
                    </div></a>
                <script>
                    //var str = '$item["goods_id"]; ';
                   // var str = JSON.parse(str);
                    //var goods_id = str[0]['goods_id'];
                    $("#plus_item_<?=$item['common_id']?>").attr('href',"index.php?ctl=Goods_Goods&met=goods&type=goods&cid=<?=$item['common_id']?>");
                </script>
            </li>

        <?php } ?>
    </ul>
    <!-- 分页（Plus会员尊享搜索） -->
    <div class="page page_front plus-page">
        <?=$page_nav;?>
    </div>
    <?php }else{ ?>
    <!-- 无数据 -->
    <div class="table plus-nodata tc">
        <div class="table-cell">
            <div>
                <img src="<?=$this->view->img?>/plus/empty.png" alt="暂无搜索结果">
                <p>暂无搜索结果</p>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<!-- 弹框-开通成功 -->
<div id ="plus_alert_box" class="table plus-alert tc">
    <div class="table-cell">
        <div class="plus-alert-content">
            <strong>恭喜您</strong>
            <p>已成功开启PLUS试用会员资格</p>
            <em>祝您体验愉快！</em>
            <a class="plus-alert-close" href="javascript:;" title="关闭"></a>
        </div>
    </div>
</div>
<script>
    var act =('<?=$active;?>'=='1')?true:false;
    $(document).ready(function(){
        if(act){
            $(".plus-header-exchange em").removeClass("active");
            $(".plus-header-exchange em").eq(1).addClass("active");
            $(".plus-header-li").removeClass("active");
            $(".plus-header-li").eq(1).addClass("active");
        }
    });
    $(window).scroll(function() {
        if($(window).scrollTop()>=38){
             $(".plus-header-nav").addClass("active").addClass("plus-header-bgf2");
         }else{
             $(".plus-header-nav").removeClass("active").removeClass("plus-header-bgf2");
         }
       
    });
    $(".btn-plus-try").click(function(){
        var status = '<?=$status;?>';
        if(status=="1"){
            return ;
        }
        tryPlus();
    })
    $(".plus-alert-close").click(function(){
        $(".plus-alert").removeClass("active");
    })
    $(".plus-header-exchange em").click(function(){
        var index=$(this).index();
        $(".plus-header-exchange em").removeClass("active");
        $(this).addClass("active");
        $(".plus-header-li").removeClass("active");
        $(".plus-header-li").eq(index).addClass("active");
    })
    var protocolStr = document.location.protocol;
    var SITE_URL = "<?=Yf_Registry::get('url')?>";
    if (protocolStr === "http:") {
        SITE_URL = SITE_URL.replace(/https:/, "http:");
    } else if (protocolStr === "https:") {
        SITE_URL = SITE_URL.replace(/http:/, "https:");
    } else {
        SITE_URL = SITE_URL.replace(/https:/, "http:");
    }
    function sleep(numberMillis) {
        var now = new Date();
        var exitTime = now.getTime() + numberMillis;
        while (true) {
            now = new Date();
            if (now.getTime() > exitTime)
                return;
        }
    }
    function tryPlus(){
        $.post(SITE_URL + "?ctl=Plus_User&typ=json&met=openTry",{ts: Date.now()},function(data){
            var data =JSON.parse(data)
            console.log(data);
            if (data.status != '200') {
                $(".plus-alert-content strong").html(data.strong);
                $(".plus-alert-content p").html(data.p);
                $(".plus-alert-content em").html(data.em);
                $(".plus-alert").addClass("active");
                if(data.location){
                    sleep(1000);
                    window.location.href =data.url+'/index.php?ctl=Info&met=certification';
                }
                return ;
            }
            if(data.flag){
                $('.btn-plus-try').html('已开通PLUS试用会员');
                window.location.href = SITE_URL+"?ctl=Plus_User&met=index&type=tryPlus";
            }
            $(".plus-alert").addClass("active");
        });
    }
    //搜索
    function searchplusgoods() {
        var searchstr = $("#search_goods").val();
        //地址中的参数
        var params = window.location.search;
        params = changeURLPar(params, "words", searchstr);
        window.location.href = SITE_URL + params;
    }

    function gotoIndex(str='',met='index'){

        window.location.href = SITE_URL+"?ctl=Plus_User&met="+met+str;
    }

    function changeURLPar(destiny, par, par_value) {
        var pattern = par + "=([^&]*)";
        var replaceText = par + "=" + par_value;
        if (destiny.match(pattern)) {
            var tmp = new RegExp(pattern);
            tmp = destiny.replace(tmp, replaceText);
            return (tmp);
        }
        else {
            if (destiny.match("[\?]")) {
                return destiny + "&" + replaceText;
            }
            else {
                return destiny + "?" + replaceText;
            }


        }
        return destiny + "\n" + par + "\n" + par_value;
    }
</script>

<?php
include $this -> view -> getTplPath() . '/' . 'footer.php';
?>