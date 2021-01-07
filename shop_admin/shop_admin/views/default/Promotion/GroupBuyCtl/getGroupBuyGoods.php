<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">

 <!-- 团购 -->
 
 <div id="saleBox" class='sale-box' v-show="show">
        <h4 class="sale-title">标题：</h4>
        <div class="relative mb20">
            <input class="sale-tit-input" type="text" v-model="activeTitle" maxlength="4">
            <em class="sale-tit-limit"><b>{{activeTitle.length}}</b>/{{changenum}}</em>
            <div class="ml40 iblock vm">
                <p>团购商品过期后，系统会自动在内容列表中删除，不在首页展示。</p>
                <p>若未添加商品（或不足12款），系统自动取活动商品在首页展示。</p>
            </div>
        </div>
            <h4 class="sale-title" v-if="contentList.length>0">内容：</h4>
            <div class="items-box">
                <ul class="sale-limit-items">
                    <li v-for="(item,index) in contentList" :key="index" :id="item.goods_id"  draggable="true" @dragstart="dstart($event, index)" @dragover="allowDrop" @drop="drop($event, index)">
                        <em><img :src="item.groupbuy_image" :alt="item.goods_name"></em>
                        <span class="one-overflow">{{item.goods_name}}</span>
                        <strong>￥{{item.groupbuy_price}}</strong>
                        <button class="del-contentGood" v-on:click="delSale(index)">删除</button>
                    </li>
                </ul>
            </div>
            <div class="mod-search cf">
                    <div>
                        <ul class="ul-inline clearfix">
                            <li class="fl mr10">
                                <input class="ui-input ui-input-ph matchCon" type="text" v-model="searchInput1" placeholder="团购名称">
                            </li>
                            <li class="fl mr10">
                                <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon" v-model="searchInput2"  placeholder="商品名称">
                            
                            </li>
                            <li class="fl mr10">
                                <input type="text" id="redpacket_t_title" name="redpacket_t_title" class="ui-input ui-input-ph matchCon" v-model="searchInput3"  placeholder="店铺名称">
                            
                            </li>
                            <li class="fl"> <a class="ui-btn" v-on:click="btntgSearch" id="search">查询<i class="iconfont icon-btn02"></i></a></li>
                        </ul>
                    </div>
            </div>
            <div class="ui-state-default ui-jqgrid-hdiv ui-corner-top table-box">
                <table class="sale-table sale1 table-groupbuy " cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="activity">团购名称</th>
                                <th class="store">商品名称</th>
                                <th class="store">店铺名称</th>
                                <th class="img">团购图片</th>
                                <th class="operate">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr v-for="(item,index) in tuangouList" :key="index" :data-acid="item.groupbuy_id">
                                    <td>{{item.groupbuy_name}}</td>
                                    
                                    <td>{{item.goods_name}}</td>
                                    <td>{{item.shop_name}}</td>
                                    <td><img :src="item.groupbuy_image"></td>
                                    <td><button class="btn-addgoods" v-on:click="addtgGoods(index)">添加</button></td>
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
    <p><i class="iconfont icon-tips"></i><?= __('请在后台促销设置中打开商品促销功能并发布限时折扣商品'); ?></p>
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
                //活动类型
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
                //内容
                contentList:[],
                //团购
                tuangouList:[],
                changeList:[],
                idList:[],
                postaccount:{},
                show:false
           },
            mounted:function(){


                //获取活动商品
                var api = frameElement.api;
                if(api.config.tile){
                    this.activeTitle=api.config.tile;
                }
                var that=this;
                if (api.data.fromto == '1') {
                    if(api.data.data.length !="0"){
                        if(api.data.data[0].type=="groupbuy"){
                            this.contentList=api.data.data[0].content_info;
                            for(var q=0;q<this.contentList.length;q++){
                                this.idList.push(this.contentList[q].groupbuy_id);
                            }
                            this.activeTitle=api.data.data[0].title;
                        }
                    }
                } else if(api.data.fromto == '2'){
                    if(api.data.data.length !="0"){
                        if(api.data.data[1].type=="groupbuy"){
                            this.contentList=api.data.data[1].content_info;
                            for(var q=0;q<this.contentList.length;q++){
                                this.idList.push(this.contentList[q].groupbuy_id);
                            }
                            this.activeTitle=api.data.data[1].title;
                        }
                    }
                }
                axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getTPLGroupBuyGoodsList&typ=json",{
                    params:{
                        groupbuy_state:2,
                        page:that.currentPage,
                        rows:that.size,
                    }
                }).then(function(res){
                    if (res.data.data.length == 0) {
                        $('#tips').css('display', 'block');
                    } else {
                        that.show = true;
                        that.tuangouList = res.data.data.items;
                        that.totalnum = res.data.data.total;
                    }
                    console.log( that.tuangouList);
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
                        console.log(Qs.stringify({
                            module:api.data.module,
                            fromto:api.data.fromto,
                            layout_title:that.activeTitle,
                            item_id:api.data.iframe_id,
                            layout_data:that.postaccount
                        }));
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

                // 添加团购内容商品
                dstart:function(event,index){
                    this.num1=index;
                },
                allowDrop:function(event){
                    event.preventDefault()
                },
                drop:function(event,index){
                    event.preventDefault();
                    var that=this;
                    that.num2=index;
                    var tempOption = that.contentList[that.num1];
                    that.$set(that.contentList, that.num1, that.contentList[that.num2])
                    that.$set(that.contentList, that.num2, tempOption)
                    var ids=that.idList[that.num1];
                    that.$set(that.idList, that.num1, that.idList[that.num2]);
                    that.$set(that.idList, that.num2, ids);
                    // console.log(4444444444444444);

                },
                addtgGoods:function(idx){
                    if(this.contentList.length<12){
                        this.contentList.push({
                            groupbuy_image:this.tuangouList[idx].groupbuy_image,
                            goods_name:this.tuangouList[idx].goods_name,
                            groupbuy_price:this.tuangouList[idx].groupbuy_price,
                            goods_id:this.tuangouList[idx].groupbuy_id
                        })
                        this.idList.push(this.tuangouList[idx].groupbuy_id);
                        console.log(this.idList)
                    }else{
                        alert("最多添加12个商品")
                    }
                    return false;
                },
                

                //删除内容商品
                delSale:function(idx){
                    this.idList.splice(idx,1);
                    console.log(this.idList)
                    this.contentList.splice(idx,1);
                },
                //搜索
                btntgSearch:function(){
                    var that=this;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json",{
                        params:{
                            groupbuy_state:2,
                            groupbuy_name:that.searchInput1,
                            goods_name:that.searchInput2,
                            shop_name:that.searchInput3,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.tuangouList=res.data.data.items;
                        that.totalnum = res.data.data.total;
                    })
                    
                },
                saleAccount:function(){
                    var api = frameElement.api;
                     this.idList = this.idList.filter(function (s) {
                       return s && s.trim();
                    });
                    var str=this.idList.join(",");
                    console.log(this.idList);
                    this.changeList.push({
                        title:this.activeTitle,
                        type:"groupbuy",
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
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json",{
                        params:{
                            groupbuy_state:2,
                            groupbuy_name:that.searchInput1,
                            goods_name:that.searchInput2,
                            shop_name:that.searchInput3,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.tuangouList=res.data.data.items;
                        that.totalnum = res.data.data.total;
                    }).catch(function(){
                        console.log("fail");
                    })
                },
                lpage:function(){
                    var that = this;
                    that.currentPage = that.totalnum;
                    that.items.type1 = that.totalnum;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json",{
                        params:{
                            groupbuy_state:2,
                            groupbuy_name:that.searchInput1,
                            goods_name:that.searchInput2,
                            shop_name:that.searchInput3,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.tuangouList=res.data.data.items;
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
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json",{
                        params:{
                            groupbuy_state:2,
                            groupbuy_name:that.searchInput1,
                            goods_name:that.searchInput2,
                            shop_name:that.searchInput3,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.tuangouList=res.data.data.items;
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
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json",{
                        params:{
                            groupbuy_state:2,
                            groupbuy_name:that.searchInput1,
                            goods_name:that.searchInput2,
                            shop_name:that.searchInput3,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.tuangouList=res.data.data.items;
                        that.totalnum = res.data.data.total;
                    }).catch(function(){
                        console.log("fail");
                    })
                },
                jpage:function(){
                    var that = this;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json",{
                        params:{
                            groupbuy_state:2,
                            groupbuy_name:that.searchInput1,
                            goods_name:that.searchInput2,
                            shop_name:that.searchInput3,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.tuangouList=res.data.data.items;
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
                        axios.get(BASE_URL+"/index.php?ctl=Promotion_GroupBuy&met=getGroupBuyGoodsList&typ=json",{
                            params:{
                                groupbuy_state:2,
                                groupbuy_name:that.searchInput1,
                                goods_name:that.searchInput2,
                                shop_name:that.searchInput3,
                                page:that.currentPage,
                                rows:that.size,
                            }
                        }).then(function(res){
                            that.tuangouList=res.data.data.items;
                            that.totalnum = res.data.data.total;
                        }).catch(function(){
                            console.log("fail");
                        })

                    },0);
                },
                
            }
        })
    </script>