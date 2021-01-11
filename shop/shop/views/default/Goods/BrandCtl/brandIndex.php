<?php if (!defined('ROOT_PATH')) {
    exit('No Permission');
} ?>
<?php
include $this->view->getTplPath() . '/' . 'header.php';
?>
    <link type="text/css" rel="stylesheet" href="<?= $this->view->css ?>/classify.css">
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/Group-integral.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/brand.css"/>
    <link rel="stylesheet" type="text/css" href="<?= $this->view->css ?>/iconfont/iconfont.css">
    <script type="text/javascript" src="<?= $this->view->js ?>/tuangou-index.js"></script>
    <script type="text/javascript" src="<?= $this->view->js ?>/brank.js"></script>
    <script src="<?= $this->view->js_com ?>/plugins/jquery.slideBox.min.js" type="text/javascript"></script>
    <!-- 内容 -->

    <div class="wrapsp clearfix">
        <div class="divhead clearfix">
            <ul class="goods_ul_S clearfix">
                <li><a class="xq" href="index.php?ctl=Goods_Cat&met=goodsCatList"><?= __('全部商品分类'); ?></a></li>
                <li class="seles"><a class="pl"><?= __('全部品牌'); ?></a></li>
                <li><a class="xs" href="index.php?ctl=Goods_Goods&met=goodslist"><?= __('全部商品'); ?></a></li>
            </ul>
        </div>
        <!-- 推荐品牌 全部品牌-->
        <div class="brand">
            <div class="brand_one"><span>推荐品牌</span></div>
            <div class="brand_two">
                <!-- 循环 -->
                <?php if ($recomend_list){ ?>
                <?php foreach($recomend_list as $key=>$val){ ?>
                    <a target="_blank" href="index.php?ctl=Goods_Goods&met=goodslist&brand_id=<?= $val['brand_id']; ?>" title="<?= __($val['brand_name']); ?>">
                        <div class="brand_twoa">
                            <div class="brand_twob">
                                <div class="brand_twoc"><img src="<?= $val['brand_pic']?>"/></div>
                            </div>
                            <p><?= $val['brand_name'] ?></p>
                        </div>
                    </a>
               <?php }?>
               <?php }?>
            </div>

            <!-- 全部品牌 -->
            <div class="brand_one" style="padding:30px 0px 14px;"><span>全部品牌</span></div>
            <!-- 字母 -->
            <div class="brand_num">
                <div class="brand_numa active initial" data-initial="">全部</div>
                <div class="brand_numb initial" data-initial="A">A</div>
                <div class="brand_numb initial" data-initial="B">B</div>
                <div class="brand_numb initial" data-initial="C">C</div>
                <div class="brand_numb initial" data-initial="D">D</div>
                <div class="brand_numb initial" data-initial="E">E</div>
                <div class="brand_numb initial" data-initial="F">F</div>
                <div class="brand_numb initial" data-initial="G">G</div>
                <div class="brand_numb initial" data-initial="H">H</div>
                <div class="brand_numb initial" data-initial="I">I</div>
                <div class="brand_numb initial" data-initial="J">J</div>
                <div class="brand_numb initial" data-initial="K">K</div>
                <div class="brand_numb initial" data-initial="L">L</div>
                <div class="brand_numb initial" data-initial="M">M</div>
                <div class="brand_numb initial" data-initial="N">N</div>
                <div class="brand_numb initial" data-initial="O">O</div>
                <div class="brand_numb initial" data-initial="P">P</div>
                <div class="brand_numb initial" data-initial="Q">Q</div>
                <div class="brand_numb initial" data-initial="R">R</div>
                <div class="brand_numb initial" data-initial="S">S</div>
                <div class="brand_numb initial" data-initial="T">T</div>
                <div class="brand_numb initial" data-initial="U">U</div>
                <div class="brand_numb initial" data-initial="V">V</div>
                <div class="brand_numb initial" data-initial="W">W</div>
                <div class="brand_numb initial" data-initial="X">X</div>
                <div class="brand_numb initial" data-initial="Y">Y</div>
                <div class="brand_numb initial" data-initial="Z">Z</div>
                <div class="brand_numc">
                    <input type="" name="keyword" style="outline:medium;"/>
                    <img src="<?= $this->view->img ?>/img_hot01@2x.png" id="search">
                </div>
            </div> 
            <div class="barand_three">
                <?php if($brand_list){ ?>
                <?php foreach($brand_list as $k=>$v){ ?>
                <div class="barand_threea">
                    <div class="barand_threeb"><span><?= $k ?></span></div>
                    <div class="barand_threec">
                        <!-- 循环 -->
                        <?php foreach($v as $v_k=>$v_v){ ?>
                        <a target="_blank" href="index.php?ctl=Goods_Goods&met=goodslist&brand_id=<?= $v_v['brand_id']; ?>" title="<?= __($v_v['brand_name']); ?>">
                            <div class="barand_threed">
                                <div class="barand_threee"><?= $v_v['brand_name'] ?></div>
                                <div class="barand_threef">
                                    <div class="barand_threeg"><img src="<?= $v_v['brand_pic'] ?>"/></div>
                                </div>
                            </div>
                        </a>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <!-- 尾部 -->
    <script>
        $(function(){
            //首字母筛选
            $(document).on('click','.initial',function(){
                $(this).addClass('active').siblings().removeClass('active');
                var brand_initial = $(this).data('initial');
                $.post(SITE_URL + '?ctl=Goods_Brand&met=getBrandListInfo&typ=json', {brand_initial: brand_initial}, function (res) {
                    if(res.status == 200){
                        var data = res.data;
                        brand(data);
                    }else{
                        $(".barand_three").html('');
                    }
                });
            })

            //搜索框
            $("#search").click(function(){
                var brand_name = $(this).prev().val();
                $.post(SITE_URL + '?ctl=Goods_Brand&met=getBrandListInfo&typ=json', {brand_name: brand_name}, function (res) {
                    if (res.status == 200) {
                        var data = res.data;
                        brand(data);
                    } else {
                        $(".barand_three").html('');
                    }
                });
            })
        });
        function brand(data){
            var html = "";
            for (var i in data) {
                html += "<div class='barand_threea'><div class='barand_threeb'><span>" + i + "</span></div><div class='barand_threec'>";
                var brand_list = data[i];
                for (var k = 0; k < brand_list.length; k++) {
                    html += "<a target='_blank' href='index.php?ctl=Goods_Goods&met=goodslist&brand_id=" + brand_list[k].brand_id + "' title='" + brand_list[k].brand_name + "'>" +
                        "                            <div class='barand_threed'>" +
                        "                                <div class='barand_threee'>" + brand_list[k].brand_name + "</div>" +
                        "                                <div class='barand_threef'>" +
                        "                                    <div class='barand_threeg'><img src='" + brand_list[k].brand_pic + "'></div>" +
                        "                                </div>" +
                        "                            </div>" +
                        "                        </a>";
                }
                html += "</div></div>";
                ;
            }
            $(".barand_three").html(html);
        }
    </script>
<?php
include $this->view->getTplPath() . '/' . 'footer.php';
?>