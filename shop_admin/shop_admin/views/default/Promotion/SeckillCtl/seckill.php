<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_tha6vbfiyhg.css">

<!-- 秒杀活动 -->

<div id="saleBox" class='sale-box' v-show="show">
    <h4 class="sale-title">标题：</h4>
    <div class="relative mb20">
        <input class="sale-tit-input" type="text" v-model="activeTitle" maxlength="4">
        <em class="sale-tit-limit"><b>{{activeTitle.length}}</b>/{{changenum}}</em>
        <div class="ml40 iblock vm">
            <p><?= __('限时折扣商品过期后，系统会自动在内容列表中删除，不在首页展示。'); ?></p>
            <p><?= __('若未添加商品（或不足12款），系统自动取活动商品在首页展示。'); ?></p>
        </div>
    </div>
 
    <h4 class="sale-title" v-if="contentList.length>0">内容：</h4>
    <div class="items-box">
        <ul class="sale-limit-items">
            <li v-for="(item,index) in contentList" :key="index" :id="item.goods_id">
                <em><img :src="item.goods_image" :alt="item.goods_name"></em>
                <span class="one-overflow">{{item.goods_name}}</span>
                <strong>{{item.seckill_price}}</strong>
                <button class="del-contentGood" v-on:click="delSale(index)">删除</button>
            </li>
        </ul>
    </div>
    <div class="mod-search cf">
        <div>
            <ul class="ul-inline clearfix">
                <li class="fl mr10">
                    <input class="ui-input ui-input-ph matchCon" type="text" v-model="searchInput1" placeholder="活动名称...">
                </li>
                <li class="fl mr10">
                    <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon" v-model="searchInput2"  placeholder="店铺名称">

                </li>
                <li class="fl"> <a class="ui-btn" v-on:click="btndsSearch" id="search">查询<i class="iconfont icon-btn02"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="table-box">
        <table class="sale-table sale2 table-seckill" cellspacing="0" cellpadding="0" >
            <thead>
            <tr>
                <th class="activity">活动名称</th>
                <th class="store">店铺名称</th>
                <th class="img">商品图片</th>
                <th class="pri">商品价格</th>
                <th class="sale">折扣价格</th>
                <th class="goodsprop">商品规格</th>
                <th class="operate">操作</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item,index) in seckillList" :key="index" :data-id="item.seckill_goods_id">
                <td>{{item.seckill_name}}</td>
                <td>{{item.shop_name}}</td>
                <td><img :src="item.goods_image"></td>
                <td>{{item.goods_price}}</td>
                <td>{{item.secklii_price}}</td>
                <td><span class="td-props one-overflow" :title="item.goods_spec_str">{{item.goods_spec_str}}</span></td>
                <td><button class="btn-addgoods" v-on:click="adddsGoods(index)">添加</button></td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- 分页 -->
    <div id="page" class="ui-state-default ui-jqgrid-pager ui-corner-bottom" dir="ltr">
        <div id="pg_page" class="ui-pager-control" role="group">
            <table cellspacing="0" cellpadding="0" border="0" class="ui-pg-table" style="width:100%;table-layout:fixed;height:100%;" role="row">
                <tbody>
                <tr>
                    <td id="page_left" align="left">
                        <table cellspacing="0" cellpadding="0" border="0" class="ui-pg-table navtable" style="float:left;table-layout:auto;">
                            <tbody>
                            <tr>
                                <td class="ui-pg-button ui-corner-all" title="">
                                    <!-- <div class="ui-pg-div">
                                        <span class="ui-icon ui-icon-config"></span>
                                    </div> -->
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td id="page_center" align="center" style="white-space: pre; width: 269px;">
                        <table cellspacing="0" cellpadding="0" border="0" style="table-layout:auto;" class="ui-pg-table">
                            <tbody>
                            <tr>
                                <td id="first_page" @click="fpage()" class="ui-pg-button ui-corner-all ui-state-disabled" style="cursor: default;">
                                    <span class="ui-icon ui-icon-seek-first"></span>
                                </td>
                                <td id="prev_page" @click="prevpage()" class="ui-pg-button ui-corner-all ui-state-disabled">
                                    <span class="ui-icon ui-icon-seek-prev"></span>
                                </td>
                                <td class="ui-pg-button ui-state-disabled" style="width:4px;">
                                    <span class="ui-separator"></span>
                                </td>
                                <td dir="ltr">
                                    <input class="ui-pg-input" @keyup.enter="jpage()" v-model="items.type1" oninput="value=value.replace(/[^\d]/g,'')" type="text" size="2" maxlength="7" value="0" role="textbox">
                                    <span id="sp_1_page"><?= __('共'); ?>{{ totalnum }}<?= __('页'); ?></span>
                                </td>
                                <td class="ui-pg-button ui-state-disabled" style="width:4px;">
                                    <span class="ui-separator"></span>
                                </td>
                                <td id="next_page" @click="nextpage()" class="ui-pg-button ui-corner-all ui-state-disabled" style="cursor: default;">
                                    <span class="ui-icon ui-icon-seek-next"></span>
                                </td>
                                <td id="last_page" @click="lpage()" class="ui-pg-button ui-corner-all ui-state-disabled">
                                    <span class="ui-icon ui-icon-seek-end"></span>
                                </td>
                                <td dir="ltr">
                                    <select class="ui-pg-selbox" role="listbox" v-model="items.type2" @change="cpage()">
                                        <option role="option" value="10" selected="selected">10</option>
                                        <option role="option" value="20">20</option>
                                        <option role="option" value="50">50</option>
                                        <option role="option" value="100">100</option>
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                    <td id="page_right" align="right">
                        <!-- <div dir="ltr" style="text-align:right" class="ui-paging-info">1 - 14　共 14 条</div> -->
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
<!-- 提示 -->
<div id="tips" style="display:none;">
    <p><i class="iconfont icon-tips"></i><?= __('请在后台促销设置中打开商秒杀功能并发布秒杀活动商品'); ?></p>
</div>
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/qs.min.js" charset="utf-8"></script>
<script>
    var vm=new Vue({
        el:"#saleBox",
        data:{
            changenum:"4",
            // 活动名称：
            activeTitle:"",
            // 活动类型
            forumType:'',
            searchInput1:"",
            searchInput2:"",
            searchInput3:"",
            // 分页
            totalnum:1, //总页数
            currentPage:1, //当前页
            size:10, //每页数量
            items: {
                type1:1, //跳转至多少页
                type2:10 //每页数量变动值
            },
            // 内容

            contentList:[],
            // 限时折扣
            seckillList:[],
            changeList:[],
            idList:[],
            postaccount:{},
            show:false,
            max:0
        },
        mounted:function(){
            //获取活动商品
            var api = frameElement.api;
            console.log(api);
            if(api.data.forumType){
                this.activeTitle=api.data.forumType;
            }
            var that=this;
            if (api.data.data.forum_style == 2) {
                that.max = 9;
            } else {
                that.max =12;
            }
            if(api.data.data.length !="0"){
                if(api.data.data[0].type=="seckill" ){
                    that.contentList=api.data.data[0].content_info;
                    console.log(that.contentList);
                    for(var q=0;q<that.contentList.length;q++){
                        that.idList.push(that.contentList[q].seckill_goods_id);
                    }
                }

                // if(api.data.data[1].type=="seckill" ){
                //     that.contentList=api.data.data[1].content_info;
                //     console.log(that.contentList);
                //     for(var q=0;q<that.contentList.length;q++){
                //         that.idList.push(that.contentList[q].seckill_goods_id);
                //     }
                // }
            }

            axios.get(BASE_URL+"/index.php?ctl=Promotion_Seckill&met=getSeckillGoodsList&typ=json",{
                params:{
                    seckill_state:1,
                    page:that.currentPage,
                    rows:that.size,
                }
            }).then(function(res){
                if (res.data.data.length == 0) {
                    $('#tips').css('display', 'block');
                } else {
                    that.show = true;
                    that.seckillList = res.data.data.items;
                    that.totalnum = res.data.data.total;
                }
                console.log( that.seckillList);
            }).then(function(){
                console.log("suceess");
            }).catch(function(){
                console.log("fail");
            })


            api.button({
                id: "confirm",
                name:"确认",
                focus: true,
                callback: function () {
                    that.saleAccount();
                    if(api.data.wxapp == 1){
                        var url = BASE_URL+"/index.php?ctl=Wx_TplLayout&met=editTplABLayout&typ=json";
                    }else{
                        var url = BASE_URL+"/index.php?ctl=Mb_TplLayout&met=editTplABLayout&typ=json";
                    }
                    axios.post(url,Qs.stringify({
                        module:api.data.module,
                        fromto:api.data.fromto,
                        layout_title:that.activeTitle,
                        item_id:api.data.iframe_id,
                        layout_data:that.postaccount
                    },{ indices: false })).then(function(res){
                        console.log("success");
                        var callback = frameElement.api.data.callback;
                        callback();
                        api.close();

                    }).catch(function(res){
                        conosle.log("fail");
                    })
                    return false;
                }
            },{
                id:"cancel",
                name:"取消"
            })

        },
        watch:{
            items:{
                handler:function(val,oldval){
                    var that = this;
                    if (that.items.type1 && that.totalnum >= that.items.type1 && that.items.type1 > 0) {
                        that.currentPage = that.items.type1;
                    } else if (that.items.type1 && that.totalnum < that.items.type1) {
                        that.currentPage = that.totalnum;
                        that.items.type1 = that.totalnum;
                    } else if(that.items.type1 && that.items.type1 <= 0) {
                        that.currentPage = 1;
                        that.items.type1 = 1;
                    }
                    that.size = that.items.type2;
                },
                deep:true
            }
        },
        methods:{
            // 添加限时折扣内容商品
            adddsGoods:function(idx){
                if(this.contentList.length<this.max){
                    this.contentList.push({
                        goods_image:this.seckillList[idx].goods_image,
                        goods_name:this.seckillList[idx].goods_name,
                        goods_price:this.seckillList[idx].goods_price,
                        seckill_price:this.seckillList[idx].seckill_price,
                        goods_id:this.seckillList[idx].seckill_goods_id
                    });
                    this.idList.push(this.seckillList[idx].seckill_goods_id);
                    console.log(this.idList);

                }else{
                    alert("<?= __('最多添加'); ?>" + this.max + "<?= __('个商品'); ?>")
                }
                return false;
            },

            // 删除内容商品
            delSale:function(idx){
                this.contentList.splice(idx,1);
                this.idList.splice(idx,1);
                console.log(this.idList);
            },
            // 搜索
            btndsSearch:function(){
                var that=this;
                axios.get(BASE_URL+"/index.php?ctl=Promotion_Seckill&met=getSeckillGoodsList&typ=json",{
                    params:{
                        seckill_state:1,
                        seckill_name:that.searchInput1,
                        shop_name:that.searchInput2,
                        rows:that.size,
                    }
                }).then(function(res){
                    that.seckillList=res.data.data.items;
                    that.totalnum = res.data.data.total;
                })

            },
            saleAccount:function(){
                var api = frameElement.api;
                var str=this.idList.join(",");
                this.changeList.push({
                    title:this.activeTitle,
                    type:"seckill",
                    content:str
                })
                for(var a=0; a<this.changeList.length; a++) {
                    this.postaccount[a] = this.changeList[a];
                }
            },
            // 分页
            fpage:function(){
                var that = this;
                that.currentPage = 1;
                that.items.type1 = 1;
                axios.get(BASE_URL+"/index.php?ctl=Promotion_Seckill&met=getSeckillGoodsList&typ=json",{
                    params:{
                        seckill_state:1,
                        seckill_name:that.searchInput1,
                        shop_name:that.searchInput2,
                        page:that.currentPage,
                        rows:that.size,
                    }
                }).then(function(res){
                    that.seckillList=res.data.data.items;
                    that.totalnum = res.data.data.total;
                }).catch(function(){
                    console.log("fail");
                })
            },
            lpage:function(){
                var that = this;
                that.currentPage = that.totalnum;
                that.items.type1 = that.totalnum;
                axios.get(BASE_URL+"/index.php?ctl=Promotion_Seckill&met=getSeckillGoodsList&typ=json",{
                    params:{
                        seckill_state:1,
                        seckill_name:that.searchInput1,
                        shop_name:that.searchInput2,
                        page:that.currentPage,
                        rows:that.size,
                    }
                }).then(function(res){
                    that.seckillList=res.data.data.items;
                    that.totalnum = res.data.data.total;
                }).catch(function(){
                    console.log("fail");
                })
            },
            prevpage:function(){
                var that = this;
                if (that.currentPage > 1) {
                    that.currentPage--;
                    that.items.type1--;
                } else {
                    that.currentPage = 1;
                    that.items.type1 = 1;
                }
                axios.get(BASE_URL+"/index.php?ctl=Promotion_Seckill&met=getSeckillGoodsList&typ=json",{
                    params:{
                        seckill_state:1,
                        page:that.currentPage,
                        seckill_name:that.searchInput1,
                        shop_name:that.searchInput2,
                        rows:that.size,
                    }
                }).then(function(res){
                    that.seckillList=res.data.data.items;
                    that.totalnum = res.data.data.total;
                }).catch(function(){
                    console.log("fail");
                })
            },
            nextpage:function(){
                var that = this;
                if (that.currentPage < that.totalnum) {
                    that.currentPage++;
                    that.items.type1++;
                } else {
                    that.currentPage = that.totalnum;
                    that.items.type1 = that.totalnum;
                }
                axios.get(BASE_URL+"/index.php?ctl=Promotion_Seckill&met=getSeckillGoodsList&typ=json",{
                    params:{
                        seckill_state:1,
                        seckill_name:that.searchInput1,
                        shop_name:that.searchInput2,
                        page:that.currentPage,
                        rows:that.size,
                    }
                }).then(function(res){
                    that.seckillList=res.data.data.items;
                    that.totalnum = res.data.data.total;
                }).catch(function(){
                    console.log("fail");
                })
            },
            jpage:function(){
                var that = this;
                axios.get(BASE_URL+"/index.php?ctl=Promotion_Seckill&met=getSeckillGoodsList&typ=json",{
                    params:{
                        seckill_state:1,
                        seckill_name:that.searchInput1,
                        shop_name:that.searchInput2,
                        page:that.currentPage,
                        rows:that.size,
                    }
                }).then(function(res){
                    that.seckillList=res.data.data.items;
                    that.totalnum = res.data.data.total;
                }).catch(function(){
                    console.log("fail");
                })
            },
            cpage:function(){
                var that = this;
                setTimeout(function(){
                    that.currentPage = 1;
                    that.items.type1 = 1;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_Seckill&met=getSeckillGoodsList&typ=json",{
                        params:{
                            seckill_state:1,
                            seckill_name:that.searchInput1,
                            shop_name:that.searchInput2,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.seckillList=res.data.data.items;
                        that.totalnum = res.data.data.total;
                    }).catch(function(){
                        console.log("fail");
                    })

                },0);
            },

        }
    })
</script>