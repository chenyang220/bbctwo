<?php if (!defined('ROOT_PATH')) {exit('No Permission');}?>
<?php
include $this->view->getTplPath() . '/'  . 'header.php';
?>
<link rel="stylesheet" href="<?=$this->view->css?>/add.css">
<link rel="stylesheet" href="http://at.alicdn.com/t/font_136526_rc9m7tdbk1d.css">
<script type="text/javascript" src="<?=$this->view->js_com?>/vue.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/axios.min.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$this->view->js_com?>/qs.min.js" charset="utf-8"></script>

 <div id="saleBox" class='sale-box voucher-box' v-show="show">
            <h4 class="sale-title"><?= __('标题：'); ?></h4>
            <div class="relative mb20">
                <input class="sale-tit-input" type="text" v-model="forumType" maxlength="4">
                <em class="sale-tit-limit"><b>{{forumType.length}}</b>/{{changenum}}</em>
                <div class="ml40 iblock vm">
                    <p><?= __('红包过期后，系统会自动在内容列表中删除，不在首页展示。'); ?></p>
                    <p><?= __('若未添加红包（或不足{{ max }}款），系统自动取红包在首页展示。'); ?></p>
                </div>
            </div>
            
            <h4 class="sale-title" v-if="contentList.length>0"><?= __('内容：'); ?></h4>
            <div class="items-box">
                <ul class="sale-limit-items voucher-items">
                    <li  class="clearfix" v-for="(item,index) in contentList" :key="index">
                         <div class="fl">
                        	<!-- <span class="one-overflow">{{item.redpacket_t_title}}</span> --><!-- 名称 -->
                        	<em><img :src="item.redpacket_t_img" :alt="item.redpacket_t_title"></em>
                        </div>
                        <div class="fr">
                        	<strong><em class="pri">￥</em>{{item.redpacket_t_price}}</strong><!-- 面额 -->	
                        	<div><?= __('满'); ?>{{item.redpacket_t_orderlimit}}<?= __('元使用'); ?></div><!-- 使用限额 -->
                      	  	<time><?= __('有效期至：'); ?>{{item.redpacket_t_end_date}}</time><!-- 有效期 -->
                       	 	<em><?= __('已领取'); ?>{{item.redpacket_t_giveout}}</em><!-- 已被领用次数 -->
                        </div>
                        <button class="del-contentGood" v-on:click="delSale(index)"><?= __('删除'); ?></button>
                    </li>
                </ul>
            </div>
            <div class="mod-search cf">
                    <div>
                        <ul class="ul-inline clearfix">
                            <li class="fl mr10">
                                <input class="ui-input ui-input-ph matchCon" type="text" v-model="searchInput1" placeholder="<?= __('红包名称'); ?>...">
                            </li>
                            <li class="fl"> <a class="ui-btn" v-on:click="btndsSearch" id="search"><?= __('查询'); ?><i class="iconfont icon-btn02"></i></a></li>
                        </ul>
                    </div>
            </div>
            <table class="sale-table sale2" cellspacing="0" cellpadding="0" >
                    <thead>
                        <tr>
                            <th class="store"><?= __('红包名称'); ?></th>
                            <th class="img"><?= __('红包图片'); ?></th>
                            <th class="pri"><?= __('面额'); ?></th>
                            <th class="sale"><?= __('消费限额'); ?></th>
                            <th class="operate"><?= __('操作'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                            <tr v-for="(item,index) in discountList" :key="index" :data-id="item.id">
                                <td>{{item.redpacket_t_title}}</td>
                                <td><img :src="item.redpacket_t_img"></td>
                                <td>{{item.redpacket_t_price}}</td>
                                <td>{{item.redpacket_t_orderlimit}}</td>
                                <td><button class="btn-addgoods" v-on:click="adddsGoods(index)"><?= __('添加'); ?></button></td>
                            </tr>
                    </tbody>
            </table>
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
        <p><?= __('请先发布平台红包'); ?></p>
    </div>
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
                discountList:[],
                changeList:[],
                idList:[],
                postaccount:{},
                show:false,
                max:0
           },
            mounted:function(){
                //获取活动商品
                var api = frameElement.api;
                var that=this;

                if (api.data.data.forum_style == 2) {
                    that.max = 9;
                } else {
                    that.max =15;
                }
                for(var a=0; a<api.data.item.length; a++) {
                    this.contentList.push({
                        redpacket_t_img:api.data.item[a].redpacket_t_img,
                        redpacket_t_title:api.data.item[a].redpacket_t_title,
                        redpacket_t_price:api.data.item[a].redpacket_t_price,
                        redpacket_t_orderlimit:api.data.item[a].redpacket_t_orderlimit,
                        redpacket_t_end_date:api.data.item[a].redpacket_t_end_date,
                        redpacket_t_giveout:api.data.item[a].redpacket_t_giveout,
                    })
                    this.idList.push(api.data.item[a].redpacket_t_id);
                }
                this.forumType = api.data.forumType;
                axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json",{
                    params:{
                        redpacket_t_state:1,
                        page:that.currentPage,
                        rows:that.size,
                    }
                }).then(function(res){
                    if (res.data.data.length == 0) {
                        $('#tips').css('display','block');
                    } else {
                        that.show = true;
                        that.discountList=res.data.data.items;
                        that.totalnum = res.data.data.total;
                    }
                }).catch(function(){
                    console.log("fail");
                })
               
                api.button({
                    id: "confirm",
                    name:"确认",
                    focus: true,
                    callback: function () {
                        that.saleAccount();
                        axios.post(BASE_URL+"/index.php?ctl=Forum&met=editForum&typ=json",Qs.stringify({
                            id:api.data.data.id,
                            forum_name:api.data.data.forum_name,
                            forum_connect:that.postaccount,
                            forum_state:api.data.data.forum_state,
                            edit_content:api.data.formto,
                            forum_style:api.data.data.forum_style,
                        },{ indices: true })).then(function(res){
                            console.log("success");
                            var callback = frameElement.api.data.callback;
                            callback();
                            api.close();
                            
                        }).catch(function(res){
                            console.log("fail");
                        })
                        return false;
                    }
                },{
                    id:"cancel",
                    name:"<?= __('取消'); ?>"
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
                    var api = frameElement.api;
                    var max;
                    if (api.data.data.forum_style == 2) {
                        max = 9;
                    } else {
                        max =15;
                    }
                    if(this.contentList.length<max){
                        this.contentList.push({
                            redpacket_t_img:this.discountList[idx].redpacket_t_img,
                            redpacket_t_title:this.discountList[idx].redpacket_t_title,
                            redpacket_t_price:this.discountList[idx].redpacket_t_price,
                            redpacket_t_orderlimit:this.discountList[idx].redpacket_t_orderlimit,
                            redpacket_t_end_date:this.discountList[idx].redpacket_t_end_date,
                            redpacket_t_giveout:this.discountList[idx].redpacket_t_giveout,
                            goods_id:this.discountList[idx].id
                        })
                        this.idList.push(this.discountList[idx].redpacket_t_id);
                        
                    }else{
                        alert("<?= __('最多添加'); ?>" + max + "<?= __('个红包'); ?>")
                    }
                    return false;
                },
                
                // 删除内容商品
                delSale:function(index){
                    this.contentList.splice(index,1);
                    this.idList.splice(index,1);
                },
                // 搜索
                btndsSearch:function(){
                    var that=this;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json",{
                        params:{
                            redpacket_t_state:1,
                            redpacket_t_title:that.searchInput1,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.discountList=res.data.data.items;
                        that.totalnum = res.data.data.total;
                    })
                    
                },
                saleAccount:function(){
                    var api = frameElement.api;
                    var str=this.idList;//.join(",");
                    this.changeList.push({
                        title:this.forumType,
                        type:"redpacket",
                        content:str
                    })

                    for(var a=0; a<this.changeList.length; a++) {
                        if (api.data.formto == 2) {
                            this.postaccount[1] = this.changeList[a];
                        } else {
                            this.postaccount[0] = this.changeList[a];
                        }
                    }
                },
                // 分页
                fpage:function(){
                    var that = this;
                    that.currentPage = 1;
                    that.items.type1 = 1;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json",{
                        params:{
                            redpacket_t_state:1,
                            redpacket_t_title:that.searchInput1,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.discountList=res.data.data.items;
                        that.totalnum = res.data.data.total;
                    }).catch(function(){
                        console.log("fail");
                    })
                },
                lpage:function(){
                    var that = this;
                    that.currentPage = that.totalnum;
                    that.items.type1 = that.totalnum;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json",{
                        params:{
                            redpacket_t_state:1,
                            redpacket_t_title:that.searchInput1,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.discountList=res.data.data.items;
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
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json",{
                        params:{
                            discount_goods_state:1,
                            page:that.currentPage,
                            discount_name:that.searchInput1,
                            shop_name:that.searchInput2,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.discountList=res.data.data.items;
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
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json",{
                        params:{
                            redpacket_t_state:1,
                            redpacket_t_title:that.searchInput1,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.discountList=res.data.data.items;
                        that.totalnum = res.data.data.total;
                    }).catch(function(){
                        console.log("fail");
                    })
                },
                jpage:function(){
                    var that = this;
                    axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json",{
                        params:{
                            redpacket_t_state:1,
                            redpacket_t_title:that.searchInput1,
                            page:that.currentPage,
                            rows:that.size,
                        }
                    }).then(function(res){
                        that.discountList=res.data.data.items;
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
                        axios.get(BASE_URL+"/index.php?ctl=Promotion_RedPacket&met=getRedPacketTempList&typ=json",{
                            params:{
                                redpacket_t_state:1,
                                redpacket_t_title:that.searchInput1,
                                page:that.currentPage,
                                rows:that.size,
                            }
                        }).then(function(res){
                            that.discountList=res.data.data.items;
                            that.totalnum = res.data.data.total;
                        }).catch(function(){
                            console.log("fail");
                        })

                    },0);
                },
                
            }
        })
    </script>