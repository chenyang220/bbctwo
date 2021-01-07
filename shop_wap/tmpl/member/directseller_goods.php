<?php
include __DIR__ . '/../../includes/header.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="Author" contect="U2FsdGVkX1+liZRYkVWAWC6HsmKNJKZKIr5plAJdZUSg1A==">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-touch-fullscreen" content="yes"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta name="msapplication-tap-highlight" content="no"/>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,minimum-scale=1"/>
    <title>分销中心</title>
    <link rel="stylesheet" type="text/css" href="../../css/base.css">
    <link rel="stylesheet" href="../../css/iconfont.css">
    <link rel="stylesheet" type="text/css" href="../../css/distribution_shop.css">
    <style>
        .container{
            width:100%;
            margin-top: 2.11rem;
            background: rgba(255,255,255,1);
        }
        #a{
            width:50%;
            margin: auto;
            display: flex;
        }
        #a div{
            padding-top: 0.363636rem;
            padding-bottom: 0.363636rem;
            font-size:0.545454rem;
            font-family:PingFangSC-Regular,PingFang SC;
            font-weight:400;
            color:rgba(74,74,74,1);
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
          
        }
        .content{
            width:100%;
            background: rgba(255,255,255,1);
            display: none;
        }
        .container .active{
            display: block;
        }
        .search{
            margin-top: 0.363636rem;
            margin-bottom: 0.363636rem;
            margin: auto;
            width:14.545454rem;
            height:1.590909rem;
            background:rgba(241,241,241,1);
            border-radius:0.81818181rem;
        }
        .search input{
            padding-left: 0.678rem;
                border: none;
                width: 80%;
                height: 1.590909rem;
                background: rgba(241,241,241,1);
                border-radius: 0.81818181rem;
        }
        .search img{
            width: 1.590909rem;
            height: 1.590909rem;
        }
        .electoral{
            color: red !important; 
            border-bottom: 1px solid red;
        }
        .m-box .list ul li .box .price{
                margin-top: -1rem !important;
        }

        .nctouch-nav-la{
            background: rgba(0,0,0,0.56) !important;
            position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
          }
          .nctouch-nav-mu{
            width:263px;
            height:285px;
            margin: auto;
            background:rgba(255,255,255,1);
            border-radius:4px;
            margin-top: 40%;
            position: relative;
          }
          .nctouch-title{
            width: 100%;
            height:80px;
            line-height: 80px;
            text-align: center;
            font-size:18px;
            font-family:PingFangSC-Semibold,PingFang SC;
            font-weight:600;
            color:rgba(255,255,255,1);
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            background:linear-gradient(144deg,rgba(255,66,66,1) 0%,rgba(216,58,26,1) 100%);
            /* border-radius:5px; */
          }
          .nctouch-main{
            width:253px;
            height:106px;
            margin: auto;
            margin-top: 0.5rem;
            background:rgba(245,245,245,1);
            display: flex;
            justify-content: flex-start;
          }
          .nctouch-main .nctouch-image{
            margin: 8px;
            width:88px;
            height:88px;
            border:1px solid rgba(233,233,233,1);
          }
          .nctouch-main .nctouch-image img{
            width: 100%;
            height: 100%;
          }
          .nctouch-shop-tit{
            margin-bottom: 1.3rem;
                 box-sizing: border-box;
                height: 31px;
              width: 134px;
                overflow: hidden;
                text-overflow: ellipsis;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                word-break: break-all;
                line-height: 16px;
          }
          .nctouch-shop span{
            margin-top: 9px;
            font-size:12px;
            display: block;
            font-family:PingFangSC-Regular,PingFang SC;
            font-weight:400;
            color:rgba(63,63,63,1);
          }
          .nctouch-shop-pric{
            color:rgba(255,42,68,1) !important;
          }
          .nctouch-botto{
            width:215px;
            height:40px;
            margin: auto;
            margin-top: 1rem;
            line-height: 40px;
            text-align: center;
            font-size:14px;
            font-family:PingFangSC-Regular,PingFang SC;
            font-weight:400;
            color:rgba(255,255,255,1);
            background:linear-gradient(312deg,rgba(225,16,9,1) 0%,rgba(255,45,45,1) 100%);
            box-shadow:0px 0px 7px 0px rgba(255,69,69,0.5);
            border-radius:20px;
          }
          .nctouch-cuo{
            width: 22px;  
            height: 22px;
            position: absolute;
            right: 0px;
            top:-25px;
          }
          .nctouch-cuo img{
            width: 100%;
            height: 100%;
          }
    </style>
</head>
<script type="text/javascript" src="../../js/tmpl/NativeShare.js"></script>
<body>
<body>
    <header id="header" class="fixed bgf">
        <div class="header-wrap">
            <!-- <div class="header-l"><a href="javascript:history.go(-1)"><b class="iconfont icon-arrow-left col9b fz-40"></b></a></div> -->
            <div class="header-title posr">
                <h1 class="drap-h1-after col38" id="z-tab-order" data-order_state="all">商品库</h1>
            </div>
        </div>
    </header>
    <div class="container">
        <div style="height: 0.5rem;"></div>
        <div class="search">
            <input name="orderkey" id="" value="" placeholder="请输入商品名称进行搜索">
            <img src="../../images/sousuo.png" class="extension2">
        </div>
        <div id="a">
            <div class="distributed">已分销商品</div>
            <div class="pending">待分销商品</div>
          
        </div>
        <div class="content active">

        </div>
    </div>
    <div class="nctouch-nav-la" id="modal1" >
      <div class="nctouch-nav-mu" id="modal">
          <div class="nctouch-title">是否分享该商品？</div>
        <div class="nctouch-main">
          <div class="nctouch-image">
            <img class="goods_img" src="">
          </div>
          <div class="nctouch-shop">
            <span class="nctouch-shop-tit goods_name"></span>
            <span class="nctouch-shop-pric goods_price"></span>
          </div>
        </div>
        <div class="nctouch-botto submit_button">确认</div>
        <div class="nctouch-cuo"><img src="../../images/icon-wrong.png" class="close_k"></div>
      </div>
    </div>    
</body>

<script type="text/javascript" src="../../js/zepto.min.js"></script>
<script type="text/javascript" src="../../js/template.js"></script>
<script type="text/javascript" src="../../js/common.js"></script>
<script type="text/javascript" src="../../js/tmpl/distribution_goods.js"></script>
<script type="text/javascript" src="../../js/tmpl/footer.js"></script>
<script>
    var orderkey = '';
    var t=1;
    $(".distributed").addClass("electoral");
    $(".distributed").click(function(){
        $(".distributed").addClass("electoral");
        $(".pending").removeClass("electoral");
        getList("distributed");
        t=1;
    });
    $(".pending").click(function(){
        $(".pending").addClass("electoral");
        $(".distributed").removeClass("electoral");
        getList("pending");
        t=2;
    });
    getList("distributed");
    //商品数据
    function getList(types){
        $.ajax({
                type: "post",
                url: ApiUrl + "/index.php?ctl=Distribution_NewBuyer_Goods&met=commodityLibrary&typ=json",
                data: {k: getCookie('key'),u: getCookie('id'),type:types,orderkey:orderkey},
                dataType: "json",
                success: function (r) {
                    var r = template.render("distributed-list-goods", r.data);
                    $(".content").html(r);
                }
            });
    }
    $(".extension2").click(function(){
        orderkey=$('input[name="orderkey"]').val();
        if(t==1){
            getList("distributed");
        }else{
            getList("pending");
        }
    })
</script>
<script type="text/html" id="distributed-list-goods">
    <div class="m-box">
        <div class="box_title">
            <a class="gap-check quan" >全选</a>
            <%if(type=='pending'){%>
                <a class="add-goods">加入分销店铺</a>
            <%}else{%>
                <a class="xia"><img src="../../images/lowershelf.png">下架</a>
                <a class="tui">推荐</a>
            <%}%>
           
        </div>
        <div class="list">
            <ul>
                <% var distributed_goods = items; %>
                <%if(distributed_goods.length >0){%>
                    <%for(j=0;j < distributed_goods.length;j++){%>
                        <% var goods_list = distributed_goods[j]%>
                        <li>
                            <div class="box">
                                <input type="checkbox" name="goods_id" value="<%=goods_list.common_id%>" class="check dark-check">
                                <div class="img">
                                    <img src="<%=goods_list.common_image%>" />
                                </div>
                                <div class="info clearfix">
                                    <a href="../product_detail.html?goods_id=<%=goods_list['goods_id'][0]['goods_id'];%>">
                                    <div class="title pad30"><%=goods_list.common_name%></div></a>
                                    <div class="m-num ">
                                        ￥<%=goods_list.common_price%>
                                    </div>
                                     <?php if ($_COOKIE['is_app_guest']) { ?>
                                        <div class="price fr share_wap_buy" data-id='<%=goods_list.common_id;%>'>立即分享</div>
                                    <?php }elseif(strpos($_SERVER['HTTP_USER_AGENT'],'UCBrowser')!==false||strpos($_SERVER['HTTP_USER_AGENT'],'UCWEB')!==false){ ?>
                                        <div class="price fr share_wap_buy" data-id='<%=goods_list.common_id;%>'>立即分享</div>
                                    <?php }elseif (strpos($_SERVER['HTTP_USER_AGENT'],'MQQBrowser')!==false){ ?>
                                        <div class="price fr share_wap_buy" data-id='<%=goods_list.common_id;%>'>立即分享</div>
                                    <?php }?>
                                </div>
                            </div>
                        </li>
                    <%}%>       
                <%}%>
            </ul>
        </div>              
    </div>
    
</script>
</html>
<?php
include __DIR__ . '/../../includes/footer.php';
?>
