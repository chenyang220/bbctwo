<?php
include __DIR__ . '/../../includes/header.php';
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-touch-fullscreen" content="yes"/>
        <meta name="format-detection" content="telephone=no"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
        <meta name="format-detection" content="telephone=no"/>
        <meta name="msapplication-tap-highlight" content="no"/>
        <meta name="wap-font-scale" content="no">
        <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1"/>
        <title>礼包</title>
        <link rel="stylesheet" type="text/css" href="../../css/base.css">
        <link rel="stylesheet" href="../../css/iconfont.css">
        <style>
            .main-tit {
                width: 100%;
                height: 16.9545rem;
                margin-top: 2rem;
                background: url(../../images/a-1.png) no-repeat;
                background-size: 100% 100%;
            }

            .tit-purchase {
                text-align: center;
                font-size: 1.0909rem;
                font-family: Alibaba-PuHuiTi-M, Alibaba-PuHuiTi;
                font-weight: normal;
                color: rgba(255, 255, 255, 1);

            }

            .top {
                padding-top: 0.7272rem;
            }

            .titbottom {
                padding-bottom: 0.3rem;
            }

            .tit-purchase i {
                font-size: 1.8181rem;
                font-family: Alibaba-PuHuiTi-H, Alibaba-PuHuiTi;
                font-weight: 800;
                color: rgba(255, 255, 255, 1);
            }

            .tit-button {
                width: 9.5909rem;
                height: 1.1818rem;
                line-height: 1.1818rem;
                margin-top: 0.5rem;
                text-align: center;
                background: linear-gradient(180deg, rgba(250, 217, 97, 1) 0%, rgba(255, 154, 16, 1) 100%);
                border-radius: 0.5909rem;
                margin: auto;
                font-size: 0.6363rem;
                font-family: Alibaba-PuHuiTi-M, Alibaba-PuHuiTi;
                font-weight: 800;
                color: rgba(185, 3, 9, 1);
            }

            .main-shop {
                background: rgba(185, 3, 9, 1);
                padding: 0.4363rem;
                display: flex;
                justify-content: space-between;
                flex-flow: row wrap;
            }

            .relatives {
                padding: 0.25rem;
                background: rgba(255, 255, 255, 1);
                border-radius: 4px;
                margin-top: 0.45454545rem;
                padding-bottom: 0.5rem;

            }

            .blocks {
                font-size: 0.636363rem;
                font-family: PingFangSC-Medium, PingFang SC;
                font-weight: 500;
                color: rgba(74, 74, 74, 1)
            }

            .titu {
                box-sizing: border-box;
                height: 1.82rem;
                width: 7rem;
                font-size: 0.6363rem;
                letter-spacing: 1.3px;
                font-family: Alibaba-PuHuiTi-R, Alibaba-PuHuiTi;
                font-weight: 500;
                color: rgba(33, 33, 33, 1);
                overflow: hidden;
                text-overflow: ellipsis;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                word-break: break-all;
                margin-top: 5px;
                line-height: 0.89rem;
                margin-bottom: 3px;
                /* padding: 0 0.8181rem;
                padding-right: 1rem; */
            }

            .image_re {
                position: absolute;
                width: 100%;
                top: 0;
                left: 0;
            }

            .red {
                color: rgba(238, 46, 35, 1);
                margin-bottom: 0.18rem;
            }

            .bann {
                width: 7rem;
                height: 7rem;
                border: 1px dotted #C4C4C4;
            }

            .blo {
                padding-bottom: 0.3rem;
            }
        </style>
    </head>
    <body>
    <header id="header" class="fixed bgf">
        <div class="header-wrap">
            <!-- <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col9b fz-40"></b></a></div> -->
            <div class="header-title posr">
                <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">礼包</h1>
            </div>
        </div>
    </header>
    <div class="main-tit">
        <div class="tit-purchase top">购买<i class="gprice"></i>元</div>
        <div class="tit-purchase titbottom">以上的单个礼包</div>
        <div class="tit-button"><a>即可成为分销掌柜，单单享佣金</a></div>
    </div>
    <div class="main-shop">
    </div>
    </body>
    </html>
    <script type="text/javascript" src="../../js/zepto.min.js"></script>
    <script type="text/javascript" src="../../js/template.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/tmpl/footer.js"></script>
    <script type="text/javascript">
        $(function () {
            var page = 1;
            init(page);
            //滚动加载
            $(window).scroll(function () {
                if ($(window).scrollTop() + $(window).height() > $(document).height() - 1) {
                    page++;
                    init(page);
                }
            })

            function init(page) {
                $.ajax({
                    type: "post",
                    url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=getPackageGoods&typ=json",
                    data: {k: getCookie('key'), u: getCookie('id'), page: page},
                    dataType: "json",
                    success: function (r) {
                        if (r.status == 200) {
                            $(".gprice").html(r.data.gprice);
                            var e = template.render("distributed-package", r.data);
                            if (r.data.page > 1) {
                                $(".main-shop").append(e);
                            } else {
                                $(".main-shop").html(e);
                            }
                        } else {
                            // alert("暂无礼包商品数据！");
                        }
                    }
                });
            }
        })

    </script>
    <script type="text/html" id="distributed-package">
        <% var package_goods = items; %>
        <%if(package_goods.length >0){%>
        <%for(j=0;j < package_goods.length;j++){%>
        <% var goods_list = package_goods[j]%>
        <a class="relatives" href="../product_detail.html?goods_id=<%=goods_list['good'][0]['goods_id'];%>">
            <img src="<%=goods_list.common_image%>" class="bann"></img>
            <text class="blocks titu blo" style="margin-top: 0.3rem;"><%=goods_list.common_name%></text>
            <text class="blocks red blo">￥<%=goods_list.common_price%></text>
            <div class="image_re"><img src="../../images/tip_copy.png"></div>
        </a>
        <%}%>
        <%}%>
    </script>
<?php
include __DIR__ . '/../../includes/footer.php';
?>